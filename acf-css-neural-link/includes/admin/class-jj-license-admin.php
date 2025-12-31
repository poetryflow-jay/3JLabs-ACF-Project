<?php
/**
 * 관리자 페이지 클래스
 * 
 * @package JJ_LicenseManagerincludesAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Admin {
    
    private static $instance = null;
    
    /**
     * 싱글톤 인스턴스
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 생성자
     */
    private function __construct() {
        add_action( 'admin_init', array( $this, 'handle_actions' ) );
        add_action( 'show_user_profile', array( $this, 'add_user_license_section' ) );
        add_action( 'edit_user_profile', array( $this, 'add_user_license_section' ) );
        add_action( 'wp_ajax_jj_toggle_plugin_auto_update', array( $this, 'ajax_toggle_plugin_auto_update' ) );
    }
    
    /**
     * 액션 처리
     */
    public function handle_actions() {
        if ( ! isset( $_GET['page'] ) || strpos( $_GET['page'], 'jj-license' ) === false ) {
            return;
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        // 라이센스 생성
        if ( isset( $_POST['jj_create_license'] ) ) {
            check_admin_referer( 'jj_create_license' );
            $this->handle_create_license();
            return;
        }
        
        // 라이센스 삭제
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['license_id'] ) ) {
            check_admin_referer( 'delete_license_' . $_GET['license_id'] );
            $this->delete_license( intval( $_GET['license_id'] ) );
            wp_redirect( admin_url( 'admin.php?page=jj-license-manager&deleted=1' ) );
            exit;
        }
        
        // 라이센스 비활성화
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'deactivate' && isset( $_GET['license_id'] ) ) {
            check_admin_referer( 'deactivate_license_' . $_GET['license_id'] );
            $this->deactivate_license( intval( $_GET['license_id'] ) );
            wp_redirect( admin_url( 'admin.php?page=jj-license-manager&deactivated=1' ) );
            exit;
        }
        
        // 라이센스 활성화
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'activate' && isset( $_GET['license_id'] ) ) {
            check_admin_referer( 'activate_license_' . $_GET['license_id'] );
            $this->activate_license( intval( $_GET['license_id'] ) );
            wp_redirect( admin_url( 'admin.php?page=jj-license-manager&activated=1' ) );
            exit;
        }
        
        // 라이센스 갱신
        if ( isset( $_POST['jj_renew_license'] ) ) {
            check_admin_referer( 'jj_renew_license' );
            $this->handle_renew_license();
            return;
        }
        
        // 라이센스 생성 페이지
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'create' ) {
            // 이미 처리됨 (render_licenses_page에서 처리)
            return;
        }
    }
    
    /**
     * 라이센스 생성 처리
     */
    private function handle_create_license() {
        require_once JJ_NEURAL_LINK_PATH . 'includes/admin/class-jj-license-creator.php';
        
        $args = array(
            'license_type' => sanitize_text_field( $_POST['license_type'] ),
            'user_id' => intval( $_POST['user_id'] ),
            'product_id' => ! empty( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : null,
            'order_id' => ! empty( $_POST['order_id'] ) ? intval( $_POST['order_id'] ) : null,
            'subscription_period' => ! empty( $_POST['subscription_period'] ) ? sanitize_text_field( $_POST['subscription_period'] ) : null,
            'subscription_length' => ! empty( $_POST['subscription_length'] ) ? sanitize_text_field( $_POST['subscription_length'] ) : null,
            'purchase_date' => ! empty( $_POST['purchase_date'] ) ? sanitize_text_field( $_POST['purchase_date'] ) : current_time( 'mysql' ),
            'status' => ! empty( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'active',
            'send_email' => isset( $_POST['send_email'] ),
        );
        
        $result = JJ_License_Creator::create_license( $args );
        
        if ( $result['success'] ) {
            wp_redirect( admin_url( 'admin.php?page=jj-license-manager&created=1&license_key=' . urlencode( $result['license_key'] ) ) );
        } else {
            wp_redirect( admin_url( 'admin.php?page=jj-license-manager&create_error=1&message=' . urlencode( $result['message'] ) ) );
        }
        exit;
    }
    
    /**
     * 라이센스 목록 페이지 렌더링
     */
    public function render_licenses_page() {
        // 라이센스 생성 페이지 표시
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'create' ) {
            $this->render_create_license_page();
            return;
        }
        
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        
        // 검색 및 필터
        $search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
        $status_filter = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';
        $type_filter = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '';
        
        // 페이지네이션
        $per_page = 20;
        $current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
        $offset = ( $current_page - 1 ) * $per_page;
        
        // 쿼리 구성
        $where = array( '1=1' );
        $where_values = array();
        
        if ( ! empty( $search ) ) {
            $where[] = "(license_key LIKE %s OR user_id IN (SELECT ID FROM {$wpdb->users} WHERE user_login LIKE %s OR user_email LIKE %s OR display_name LIKE %s))";
            $search_like = '%' . $wpdb->esc_like( $search ) . '%';
            $where_values[] = $search_like;
            $where_values[] = $search_like;
            $where_values[] = $search_like;
            $where_values[] = $search_like;
        }
        
        if ( ! empty( $status_filter ) ) {
            $where[] = "status = %s";
            $where_values[] = $status_filter;
        }
        
        if ( ! empty( $type_filter ) ) {
            $where[] = "license_type = %s";
            $where_values[] = $type_filter;
        }
        
        $where_clause = implode( ' AND ', $where );
        
        // 총 개수
        $total_query = "SELECT COUNT(*) FROM {$table_licenses} WHERE {$where_clause}";
        if ( ! empty( $where_values ) ) {
            $total_query = $wpdb->prepare( $total_query, $where_values );
        }
        $total_items = $wpdb->get_var( $total_query );
        
        // 라이센스 목록
        $query = "SELECT l.*, 
                         u.user_login, u.user_email, u.display_name,
                         p.post_title as product_name
                  FROM {$table_licenses} l
                  LEFT JOIN {$wpdb->users} u ON l.user_id = u.ID
                  LEFT JOIN {$wpdb->posts} p ON l.product_id = p.ID
                  WHERE {$where_clause}
                  ORDER BY l.created_at DESC
                  LIMIT %d OFFSET %d";
        
        $query_values = array_merge( $where_values, array( $per_page, $offset ) );
        $query = $wpdb->prepare( $query, $query_values );
        
        $licenses = $wpdb->get_results( $query, ARRAY_A );
        
        // 활성화 수 조회
        foreach ( $licenses as &$license ) {
            $table_activations = JJ_License_Database::get_table_name( 'activations' );
            $license['active_count'] = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_activations} WHERE license_id = %d AND is_active = 1",
                $license['id']
            ) );
        }
        
        include JJ_NEURAL_LINK_PATH . 'templates/admin/licenses.php';
    }
    
    /**
     * 활성화 목록 페이지 렌더링
     */
    public function render_activations_page() {
        global $wpdb;
        
        $table_activations = JJ_License_Database::get_table_name( 'activations' );
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        
        // 페이지네이션
        $per_page = 20;
        $current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
        $offset = ( $current_page - 1 ) * $per_page;
        
        // 총 개수
        $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_activations}" );
        
        // 활성화 목록
        $activations = $wpdb->get_results( $wpdb->prepare(
            "SELECT a.*, l.license_key, l.license_type, u.user_login, u.user_email
             FROM {$table_activations} a
             LEFT JOIN {$table_licenses} l ON a.license_id = l.id
             LEFT JOIN {$wpdb->users} u ON l.user_id = u.ID
             ORDER BY a.activated_at DESC
             LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ), ARRAY_A );
        
        include JJ_NEURAL_LINK_PATH . 'templates/admin/activations.php';
    }
    
    /**
     * 라이센스 생성 페이지 렌더링
     */
    public function render_create_license_page() {
        // 사용자 ID가 URL 파라미터로 전달된 경우
        $preselected_user_id = isset( $_GET['user_id'] ) ? intval( $_GET['user_id'] ) : 0;
        
        include JJ_NEURAL_LINK_PATH . 'templates/admin/create-license.php';
    }
    
    /**
     * 라이센스 갱신 페이지 렌더링
     */
    public function render_renew_license_page() {
        include JJ_NEURAL_LINK_PATH . 'templates/admin/renew-license.php';
    }
    
    /**
     * 라이센스 갱신 처리
     */
    private function handle_renew_license() {
        require_once JJ_NEURAL_LINK_PATH . 'includes/admin/class-jj-license-renewal.php';
        
        $license_id = isset( $_POST['license_id'] ) ? intval( $_POST['license_id'] ) : 0;
        
        if ( empty( $license_id ) ) {
            wp_redirect( admin_url( 'admin.php?page=jj-license-manager&renew_error=1&message=' . urlencode( __( '라이센스 ID가 필요합니다.', 'jj-license-manager' ) ) ) );
            exit;
        }
        
        $args = array(
            'license_id' => $license_id,
            'subscription_period' => ! empty( $_POST['subscription_period'] ) ? sanitize_text_field( $_POST['subscription_period'] ) : null,
            'subscription_length' => ! empty( $_POST['subscription_length'] ) ? sanitize_text_field( $_POST['subscription_length'] ) : null,
            'order_id' => ! empty( $_POST['order_id'] ) ? intval( $_POST['order_id'] ) : null,
            'send_email' => isset( $_POST['send_email'] ),
        );
        
        $result = JJ_License_Renewal::renew_license( $args );
        
        if ( $result['success'] ) {
            wp_redirect( admin_url( 'admin.php?page=jj-license-manager&renewed=1' ) );
        } else {
            wp_redirect( admin_url( 'admin.php?page=jj-license-manager&renew_error=1&message=' . urlencode( $result['message'] ) ) );
        }
        exit;
    }
    
    /**
     * 설정 페이지 렌더링
     */
    public function render_settings_page() {
        if ( isset( $_POST['jj_license_settings_submit'] ) ) {
            check_admin_referer( 'jj_license_settings' );
            
            // 개발자 서버 URL 저장
            if ( isset( $_POST['developer_server_url'] ) ) {
                $developer_server_url = esc_url_raw( $_POST['developer_server_url'] );
                // URL 끝에 슬래시가 없으면 추가
                $developer_server_url = untrailingslashit( $developer_server_url ) . '/';
                update_option( 'jj_license_manager_server_url', $developer_server_url );
                
                // API URL 자동 업데이트
                $api_url = trailingslashit( $developer_server_url ) . 'wp-json/jj-license/v1';
                update_option( 'jj_license_api_url', $api_url );
            }
            
            // 이메일 발송 설정
            update_option( 'jj_license_send_email', isset( $_POST['send_email'] ) ? 1 : 0 );
            
            // API 키 재생성
            if ( isset( $_POST['regenerate_api_key'] ) && $_POST['regenerate_api_key'] ) {
                $new_api_key = wp_generate_password( 32, false );
                update_option( 'jj_license_api_key', $new_api_key );
            }
            
            // 보안 경고 이메일 설정
            if ( isset( $_POST['security_alerts'] ) ) {
                update_option( 'jj_license_manager_security_alerts', 1 );
            } else {
                update_option( 'jj_license_manager_security_alerts', 0 );
            }
            
            echo '<div class="notice notice-success"><p>' . __( '설정이 저장되었습니다.', 'jj-license-manager' ) . '</p></div>';
        }
        
        include JJ_NEURAL_LINK_PATH . 'templates/admin/settings.php';
    }
    
    /**
     * 라이센스 삭제
     */
    private function delete_license( $license_id ) {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_activations = JJ_License_Database::get_table_name( 'activations' );
        $table_history = JJ_License_Database::get_table_name( 'history' );
        
        // 관련 데이터 삭제
        $wpdb->delete( $table_activations, array( 'license_id' => $license_id ), array( '%d' ) );
        $wpdb->delete( $table_history, array( 'license_id' => $license_id ), array( '%d' ) );
        $wpdb->delete( $table_licenses, array( 'id' => $license_id ), array( '%d' ) );
    }
    
    /**
     * 라이센스 비활성화
     */
    private function deactivate_license( $license_id ) {
        $validator = new JJ_License_Validator();
        $validator->deactivate_license( $license_id, 'manual' );
    }
    
    /**
     * 라이센스 활성화
     */
    private function activate_license( $license_id ) {
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        
        $wpdb->update(
            $table_licenses,
            array( 'status' => 'active' ),
            array( 'id' => $license_id ),
            array( '%s' ),
            array( '%d' )
        );
        
        // 히스토리 기록
        $wpdb->insert(
            JJ_License_Database::get_table_name( 'history' ),
            array(
                'license_id' => $license_id,
                'action' => 'activated',
                'description' => __( '관리자가 수동으로 활성화함', 'jj-license-manager' ),
                'performed_by' => get_current_user_id(),
                'performed_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s', '%d', '%s' )
        );
    }
    
    /**
     * 업데이트 페이지 렌더링
     */
    public function render_updates_page() {
        // 플러그인 파일 업로드 처리
        if ( isset( $_POST['jj_upload_plugin'] ) ) {
            check_admin_referer( 'jj_upload_plugin' );
            $this->handle_plugin_upload();
        }
        
        // 플러그인 파일 삭제 처리
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete_plugin' && isset( $_GET['plugin_file'] ) ) {
            check_admin_referer( 'delete_plugin_' . $_GET['plugin_file'] );
            $this->handle_plugin_delete( sanitize_file_name( $_GET['plugin_file'] ) );
            wp_redirect( admin_url( 'admin.php?page=jj-license-updates&deleted=1' ) );
            exit;
        }
        
        include JJ_NEURAL_LINK_PATH . 'templates/admin/updates.php';
    }
    
    /**
     * 플러그인 파일 업로드 처리
     */
    private function handle_plugin_upload() {
        require_once JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-security.php';
        
        // 파일 업로드 확인
        if ( ! isset( $_FILES['plugin_file'] ) || $_FILES['plugin_file']['error'] !== UPLOAD_ERR_OK ) {
            $error_message = __( '파일 업로드에 실패했습니다.', 'jj-license-manager' );
            if ( isset( $_FILES['plugin_file']['error'] ) ) {
                switch ( $_FILES['plugin_file']['error'] ) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $error_message = __( '파일 크기가 너무 큽니다.', 'jj-license-manager' );
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $error_message = __( '파일이 부분적으로만 업로드되었습니다.', 'jj-license-manager' );
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $error_message = __( '파일이 선택되지 않았습니다.', 'jj-license-manager' );
                        break;
                }
            }
            add_action( 'admin_notices', function() use ( $error_message ) {
                echo '<div class="notice notice-error"><p>' . esc_html( $error_message ) . '</p></div>';
            } );
            return;
        }
        
        $file = $_FILES['plugin_file'];
        $plugin_slug = sanitize_text_field( $_POST['plugin_slug'] );
        $version = sanitize_text_field( $_POST['version'] );
        
        // 입력값 검증
        if ( empty( $plugin_slug ) || empty( $version ) ) {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error"><p>' . esc_html__( '플러그인 슬러그와 버전을 입력해주세요.', 'jj-license-manager' ) . '</p></div>';
            } );
            return;
        }
        
        // 버전 형식 검증 (예: 1.0.0)
        if ( ! preg_match( '/^\d+\.\d+\.\d+$/', $version ) ) {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error"><p>' . esc_html__( '버전 형식이 올바르지 않습니다. (예: 1.0.0)', 'jj-license-manager' ) . '</p></div>';
            } );
            return;
        }
        
        // 파일 크기 제한 (50MB)
        $max_size = 50 * 1024 * 1024; // 50MB
        if ( $file['size'] > $max_size ) {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error"><p>' . esc_html__( '파일 크기는 50MB를 초과할 수 없습니다.', 'jj-license-manager' ) . '</p></div>';
            } );
            return;
        }
        
        // 파일 확장자 확인
        $file_ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        if ( $file_ext !== 'zip' ) {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error"><p>' . esc_html__( 'ZIP 파일만 업로드할 수 있습니다.', 'jj-license-manager' ) . '</p></div>';
            } );
            return;
        }
        
        // 파일 MIME 타입 확인
        $allowed_mimes = array( 'application/zip', 'application/x-zip-compressed', 'application/x-zip' );
        $file_type = wp_check_filetype( $file['name'] );
        if ( ! in_array( $file['type'], $allowed_mimes ) && $file_type['ext'] !== 'zip' ) {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error"><p>' . esc_html__( '유효하지 않은 파일 형식입니다.', 'jj-license-manager' ) . '</p></div>';
            } );
            return;
        }
        
        // 보안 로깅
        JJ_License_Security::log_security_event( 'plugin_upload', array(
            'plugin_slug' => $plugin_slug,
            'version' => $version,
            'file_name' => $file['name'],
            'file_size' => $file['size'],
            'ip' => JJ_License_Security::get_client_ip(),
        ) );
        
        // 업로드 디렉토리 생성
        $upload_dir = wp_upload_dir();
        $plugins_dir = $upload_dir['basedir'] . '/jj-plugin-updates';
        
        if ( ! file_exists( $plugins_dir ) ) {
            wp_mkdir_p( $plugins_dir );
        }
        
        // 파일명 생성 (보안 강화: 파일명 검증)
        $file_name = sanitize_file_name( $plugin_slug . '-' . $version . '.zip' );
        
        // 파일명 검증 (특수 문자 제거 확인)
        $expected_name = $plugin_slug . '-' . $version . '.zip';
        if ( $file_name !== $expected_name ) {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error"><p>' . esc_html__( '파일명에 허용되지 않은 문자가 포함되어 있습니다.', 'jj-license-manager' ) . '</p></div>';
            } );
            return;
        }
        
        $file_path = $plugins_dir . '/' . $file_name;
        
        // 파일 이동 (보안 강화: 경로 검증)
        $real_path = realpath( dirname( $file_path ) );
        $real_plugins_dir = realpath( $plugins_dir );
        if ( $real_path !== $real_plugins_dir || strpos( $file_path, $plugins_dir ) !== 0 ) {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error"><p>' . esc_html__( '잘못된 파일 경로입니다.', 'jj-license-manager' ) . '</p></div>';
            } );
            return;
        }
        
        // 파일 이동
        if ( move_uploaded_file( $file['tmp_name'], $file_path ) ) {
            // 파일 권한 설정 (보안 강화)
            chmod( $file_path, 0644 );
            
            // 업로드된 플러그인 정보 저장
            $uploaded_plugins = get_option( 'jj_license_manager_uploaded_plugins', array() );
            if ( ! isset( $uploaded_plugins[ $plugin_slug ] ) ) {
                $uploaded_plugins[ $plugin_slug ] = array();
            }
            $uploaded_plugins[ $plugin_slug ][ $version ] = array(
                'file_path' => $file_path,
                'file_name' => $file_name,
                'uploaded_at' => time(),
                'file_size' => filesize( $file_path ),
            );
            update_option( 'jj_license_manager_uploaded_plugins', $uploaded_plugins );
            
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-success"><p>' . esc_html__( '플러그인 파일이 업로드되었습니다.', 'jj-license-manager' ) . '</p></div>';
            } );
        } else {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error"><p>' . esc_html__( '파일 저장에 실패했습니다. 디렉토리 권한을 확인해주세요.', 'jj-license-manager' ) . '</p></div>';
            } );
        }
    }
    
    /**
     * 플러그인 파일 삭제 처리
     */
    private function handle_plugin_delete( $file_name ) {
        $upload_dir = wp_upload_dir();
        $plugins_dir = $upload_dir['basedir'] . '/jj-plugin-updates';
        $file_path = $plugins_dir . '/' . sanitize_file_name( $file_name );
        
        if ( file_exists( $file_path ) ) {
            unlink( $file_path );
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-success"><p>' . esc_html__( '플러그인 파일이 삭제되었습니다.', 'jj-license-manager' ) . '</p></div>';
            } );
        }
    }
    
    /**
     * 업로드된 플러그인 파일 목록 가져오기
     */
    public function get_uploaded_plugins() {
        $upload_dir = wp_upload_dir();
        $plugins_dir = $upload_dir['basedir'] . '/jj-plugin-updates';
        
        if ( ! file_exists( $plugins_dir ) ) {
            return array();
        }
        
        $files = glob( $plugins_dir . '/*.zip' );
        $plugins = array();
        
        foreach ( $files as $file ) {
            $file_name = basename( $file );
            // 파일명에서 플러그인 슬러그와 버전 추출 (예: jj-style-guide-free-5.1.7.zip)
            if ( preg_match( '/^(.+)-(\d+\.\d+\.\d+)\.zip$/', $file_name, $matches ) ) {
                $plugins[] = array(
                    'file_name' => $file_name,
                    'plugin_slug' => $matches[1],
                    'version' => $matches[2],
                    'file_size' => filesize( $file ),
                    'upload_date' => filemtime( $file ),
                );
            }
        }
        
        return $plugins;
    }
    
    /**
     * AJAX: 플러그인 자동 업데이트 토글
     */
    public function ajax_toggle_plugin_auto_update() {
        // 권한 확인
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-license-manager' ) ) );
            return;
        }
        
        // Nonce 확인
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'jj_license_manager_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( '보안 검증에 실패했습니다.', 'jj-license-manager' ) ) );
            return;
        }
        
        // 플러그인 파일 확인
        if ( ! isset( $_POST['plugin_file'] ) || empty( $_POST['plugin_file'] ) ) {
            wp_send_json_error( array( 'message' => __( '플러그인 파일이 지정되지 않았습니다.', 'jj-license-manager' ) ) );
            return;
        }
        
        $plugin_file = sanitize_text_field( $_POST['plugin_file'] );
        $enable = isset( $_POST['enable'] ) && '1' === $_POST['enable'];
        
        // 자동 업데이트 목록 가져오기
        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
        
        if ( $enable ) {
            // 활성화: 목록에 추가
            if ( ! in_array( $plugin_file, $auto_updates, true ) ) {
                $auto_updates[] = $plugin_file;
                update_site_option( 'auto_update_plugins', $auto_updates );
                wp_send_json_success( array( 
                    'message' => __( '자동 업데이트가 활성화되었습니다.', 'jj-license-manager' ),
                    'state' => '1'
                ) );
            } else {
                wp_send_json_success( array( 
                    'message' => __( '이미 자동 업데이트가 활성화되어 있습니다.', 'jj-license-manager' ),
                    'state' => '1'
                ) );
            }
        } else {
            // 비활성화: 목록에서 제거
            $key = array_search( $plugin_file, $auto_updates, true );
            if ( false !== $key ) {
                unset( $auto_updates[ $key ] );
                update_site_option( 'auto_update_plugins', array_values( $auto_updates ) );
                wp_send_json_success( array( 
                    'message' => __( '자동 업데이트가 비활성화되었습니다.', 'jj-license-manager' ),
                    'state' => '0'
                ) );
            } else {
                wp_send_json_success( array( 
                    'message' => __( '이미 자동 업데이트가 비활성화되어 있습니다.', 'jj-license-manager' ),
                    'state' => '0'
                ) );
            }
        }
    }
}


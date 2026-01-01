<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 8.6] 플러그인 목록 페이지 향상
 * 
 * 플러그인 목록 페이지에 다음 기능 추가:
 * - 자동 업데이트 토글 버튼
 * - 라이센스 키 입력 안내 (필요시)
 * - 업그레이드/프로모션 링크
 * - 롤백 기능
 * - 필수/필요 플러그인 안내
 * - 3J Labs 링크 수정
 */
class JJ_Plugin_List_Enhancer {

    private static $instance = null;
    private $plugin_file = '';
    private $plugin_basename = '';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->plugin_file = JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php';
        $this->plugin_basename = plugin_basename( $this->plugin_file );
        
        // 플러그인 행에 메타 정보 추가 (버전 아래)
        add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 2 );
        
        // 플러그인 행에 동작 링크 추가 (비활성화 옆)
        add_filter( 'plugin_action_links_' . $this->plugin_basename, array( $this, 'enhance_plugin_action_links' ), 10, 1 );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_rollback_plugin', array( $this, 'ajax_rollback_plugin' ) );
    }

    /**
     * 플러그인 행 메타 정보 추가 (버전 아래)
     */
    public function add_plugin_row_meta( $plugin_meta, $plugin_file ) {
        // 우리 플러그인이 아니면 반환
        if ( $plugin_file !== $this->plugin_basename ) {
            return $plugin_meta;
        }

        $new_meta = array();

        // 1. 3J Labs 공식 사이트 (수정된 링크)
        $new_meta[] = '<a href="' . esc_url( 'https://3j-labs.com' ) . '" target="_blank" rel="noopener noreferrer">' . __( '공식 사이트', 'jj-style-guide' ) . '</a>';

        // 2. 문서
        $new_meta[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#system-status' ) ) . '">' . __( '문서', 'jj-style-guide' ) . '</a>';

        // 3. 필수 플러그인 안내 (필요시)
        $required_plugins = $this->get_required_plugins();
        if ( ! empty( $required_plugins ) ) {
            $required_list = array();
            foreach ( $required_plugins as $req ) {
                if ( ! is_plugin_active( $req['file'] ) ) {
                    $required_list[] = $req['name'];
                }
            }
            if ( ! empty( $required_list ) ) {
                $new_meta[] = '<span style="color: #d63638;">' . __( '필수: ', 'jj-style-guide' ) . esc_html( implode( ', ', $required_list ) ) . '</span>';
            }
        }

        // 4. 필요 플러그인 안내 (권장)
        $recommended_plugins = $this->get_recommended_plugins();
        if ( ! empty( $recommended_plugins ) ) {
            $recommended_list = array();
            foreach ( $recommended_plugins as $rec ) {
                if ( ! is_plugin_active( $rec['file'] ) ) {
                    $recommended_list[] = $rec['name'];
                }
            }
            if ( ! empty( $recommended_list ) ) {
                $new_meta[] = '<span style="color: #856404;">' . __( '권장: ', 'jj-style-guide' ) . esc_html( implode( ', ', $recommended_list ) ) . '</span>';
            }
        }

        // 5. 자동 업데이트 상태 표시
        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
        $is_auto_update_enabled = in_array( $this->plugin_basename, $auto_updates, true );
        if ( $is_auto_update_enabled ) {
            $new_meta[] = '<span style="color: #00a32a;">✓ ' . __( '자동 업데이트 활성화', 'jj-style-guide' ) . '</span>';
        }

        // 6. 라이센스 키 필요 안내 (Free 버전인 경우)
        if ( ! $this->is_premium() ) {
            $new_meta[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#license' ) ) . '" style="color: #2271b1; font-weight: 600;">' . __( '라이센스 키 입력', 'jj-style-guide' ) . '</a>';
        }

        return array_merge( $plugin_meta, $new_meta );
    }

    /**
     * 플러그인 동작 링크 향상
     * 
     * 기존 add_plugin_settings_link 메소드의 기능을 포함하여 모든 링크를 통합 관리합니다.
     */
    public function enhance_plugin_action_links( $links ) {
        $new_links = array();

        // 1. 설정 (Admin Center 메인)
        $new_links['settings'] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ) . '" style="font-weight: 600;">' . __( 'Settings', 'jj-style-guide' ) . '</a>';

        // 2. 스타일 (Style Guide)
        $new_links['styles'] = '<a href="' . esc_url( admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ) ) . '">' . __( 'Styles', 'jj-style-guide' ) . '</a>';

        // 3. 어드민 메뉴 (Admin Menu Tab)
        $new_links['menu'] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#admin-menu' ) ) . '">' . __( 'Menu', 'jj-style-guide' ) . '</a>';

        // 4. 비주얼 (Visual Tab)
        $new_links['visual'] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#visual' ) ) . '">' . __( 'Visual', 'jj-style-guide' ) . '</a>';

        // 5. 업그레이드 링크 (Free 버전인 경우)
        if ( ! $this->is_premium() ) {
            $license_manager = null;
            $upgrade_url = 'https://3j-labs.com';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
                if ( $license_manager && method_exists( $license_manager, 'get_purchase_url' ) ) {
                    $upgrade_url = $license_manager->get_purchase_url( 'upgrade' );
                }
            }
            $new_links['upgrade'] = '<a href="' . esc_url( $upgrade_url ) . '" target="_blank" rel="noopener noreferrer" style="color: #2271b1; font-weight: 600;">' . __( '업그레이드', 'jj-style-guide' ) . '</a>';
        }

        // 6. 롤백 링크 (이전 버전이 있는 경우) - 향후 확장용
        // 현재는 구현 준비 중이므로 주석 처리
        // $previous_versions = $this->get_previous_versions();
        // if ( ! empty( $previous_versions ) ) {
        //     $new_links['rollback'] = '<a href="#" class="jj-rollback-plugin" data-plugin="' . esc_attr( $this->plugin_basename ) . '" data-versions="' . esc_attr( json_encode( $previous_versions ) ) . '" style="color: #d63638;">' . __( '롤백', 'jj-style-guide' ) . '</a>';
        // }

        // 새 링크를 기존 링크(비활성화 등) 앞에 추가
        return array_merge( $new_links, $links );
    }

    /**
     * 필수 플러그인 목록
     */
    private function get_required_plugins() {
        // 현재는 없지만, 향후 확장 가능
        return array();
    }

    /**
     * 권장 플러그인 목록
     */
    private function get_recommended_plugins() {
        return array(
            array(
                'name' => 'Advanced Custom Fields',
                'file' => 'advanced-custom-fields/acf.php',
            ),
            array(
                'name' => 'ACF CSS AI Extension',
                'file' => 'acf-css-ai-extension/acf-css-ai-extension.php',
            ),
        );
    }

    /**
     * Premium 버전 여부 확인
     */
    private function is_premium() {
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                return JJ_Edition_Controller::instance()->is_at_least( 'basic' );
            } catch ( Exception $e ) {
                // ignore
            }
        }
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            return in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'BASIC', 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }
        return false;
    }

    /**
     * 이전 버전 목록 가져오기 (롤백용)
     */
    private function get_previous_versions() {
        // 현재 버전 이전의 주요 버전 목록
        // 실제 구현 시에는 업데이트 서버 API를 통해 가져오거나, 
        // 로컬에 저장된 이전 버전 목록을 활용
        $current_version = JJ_STYLE_GUIDE_VERSION;
        
        // 주요 버전 목록 (예시)
        $available_versions = array(
            '8.4.0',
            '8.3.0',
            '8.2.0',
            '8.1.0',
            '8.0.0',
        );
        
        $previous = array();
        foreach ( $available_versions as $version ) {
            if ( version_compare( $version, $current_version, '<' ) ) {
                $previous[] = $version;
            }
        }
        
        // 최근 3개만 반환
        return array_slice( $previous, 0, 3 );
    }

    /**
     * AJAX: 플러그인 롤백
     */
    public function ajax_rollback_plugin() {
        // 보안 검증
        if ( class_exists( 'JJ_Security_Hardener' ) ) {
            if ( ! JJ_Security_Hardener::verify_ajax_request( 'jj_rollback_plugin', 'jj_rollback_nonce', 'install_plugins' ) ) {
                return;
            }
        } else {
            check_ajax_referer( 'jj_rollback_nonce', 'nonce' );
            if ( ! current_user_can( 'install_plugins' ) ) {
                wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
            }
        }

        $plugin_file = isset( $_POST['plugin'] ) ? sanitize_text_field( $_POST['plugin'] ) : '';
        $target_version = isset( $_POST['version'] ) ? sanitize_text_field( $_POST['version'] ) : '';

        if ( $plugin_file !== $this->plugin_basename ) {
            wp_send_json_error( array( 'message' => __( '유효하지 않은 플러그인입니다.', 'jj-style-guide' ) ) );
        }

        // 롤백 로직 구현 (향후 확장)
        // 현재는 안내 메시지만 반환
        wp_send_json_error( array( 'message' => __( '롤백 기능은 곧 제공될 예정입니다.', 'jj-style-guide' ) ) );
    }
}

// 초기화
add_action( 'plugins_loaded', function() {
    if ( class_exists( 'JJ_Plugin_List_Enhancer' ) ) {
        JJ_Plugin_List_Enhancer::instance();
    }
}, 20 );

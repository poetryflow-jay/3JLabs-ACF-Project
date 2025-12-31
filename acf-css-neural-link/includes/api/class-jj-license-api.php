<?php
/**
 * REST API 엔드포인트 클래스
 * 
 * @package JJ_LicenseManagerincludesAPI
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_API {
    
    /**
     * API 라우트 등록
     */
    public function register_routes() {
        register_rest_route( 'jj-license/v1', '/verify', array(
            'methods' => 'POST',
            'callback' => array( $this, 'verify_license' ),
            'permission_callback' => array( $this, 'check_api_permission' ),
        ) );
        
        register_rest_route( 'jj-license/v1', '/activate', array(
            'methods' => 'POST',
            'callback' => array( $this, 'activate_license' ),
            'permission_callback' => array( $this, 'check_api_permission' ),
        ) );
        
        register_rest_route( 'jj-license/v1', '/deactivate', array(
            'methods' => 'POST',
            'callback' => array( $this, 'deactivate_license' ),
            'permission_callback' => array( $this, 'check_api_permission' ),
        ) );
        
        register_rest_route( 'jj-license/v1', '/check-update', array(
            'methods' => 'POST',
            'callback' => array( $this, 'check_update' ),
            'permission_callback' => array( $this, 'check_api_permission' ),
        ) );
        
        register_rest_route( 'jj-license/v1', '/download', array(
            'methods' => 'GET',
            'callback' => array( $this, 'download_plugin' ),
            'permission_callback' => array( $this, 'check_api_permission' ),
        ) );
        
        register_rest_route( 'jj-license/v1', '/security-alert', array(
            'methods' => 'POST',
            'callback' => array( $this, 'receive_security_alert' ),
            'permission_callback' => '__return_true', // 개발자 서버에서만 접근 가능하도록 IP 제한 필요
        ) );
        
        register_rest_route( 'jj-license/v1', '/verify-unlock', array(
            'methods' => 'POST',
            'callback' => array( $this, 'verify_unlock_code' ),
            'permission_callback' => array( $this, 'check_api_permission' ),
        ) );
        
        // [v2.0.2] 원격 제어 API
        register_rest_route( 'jj-license/v1', '/remote-command', array(
            'methods' => 'POST',
            'callback' => array( $this, 'handle_remote_command' ),
            'permission_callback' => array( $this, 'check_remote_command_permission' ),
        ) );
        
        // [v2.0.2] 로그 수집 API
        register_rest_route( 'jj-license/v1', '/collect-log', array(
            'methods' => 'POST',
            'callback' => array( $this, 'collect_log' ),
            'permission_callback' => array( $this, 'check_api_permission' ),
        ) );
        
        // [v2.0.2] 업데이트 알림 수신 API
        register_rest_route( 'jj-license/v1', '/update-notification', array(
            'methods' => 'POST',
            'callback' => array( $this, 'receive_update_notification' ),
            'permission_callback' => array( $this, 'check_remote_command_permission' ),
        ) );
        
        // [v2.0.2] 공지 수신 API
        register_rest_route( 'jj-license/v1', '/announcement', array(
            'methods' => 'POST',
            'callback' => array( $this, 'receive_announcement' ),
            'permission_callback' => array( $this, 'check_remote_command_permission' ),
        ) );
    }
    
    /**
     * API 권한 확인
     * 
     * @param WP_REST_Request $request 요청 객체
     * @return bool|WP_Error 권한 여부 또는 에러
     */
    public function check_api_permission( $request ) {
        $security_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-security.php';
        if ( ! file_exists( $security_file ) ) {
            return new WP_Error( 'missing_dependency', __( '보안 모듈을 로드할 수 없습니다.', 'jj-license-manager' ), array( 'status' => 500 ) );
        }
        require_once $security_file;
        
        // IP 주소 가져오기
        $ip = JJ_License_Security::get_client_ip();
        
        // Rate limiting 확인
        if ( ! JJ_License_Security::check_rate_limit( $ip, 'minute' ) ) {
            JJ_License_Security::log_security_event( 'rate_limit_exceeded', array(
                'ip' => $ip,
                'endpoint' => $request->get_route(),
            ) );
            return new WP_Error( 'rate_limit_exceeded', __( '요청 한도를 초과했습니다. 잠시 후 다시 시도해주세요.', 'jj-license-manager' ), array( 'status' => 429 ) );
        }
        
        // API 키 확인 (선택사항)
        $api_key = $request->get_header( 'X-API-Key' );
        $stored_api_key = function_exists( 'get_option' ) ? get_option( 'jj_license_api_key', '' ) : '';
        
        // API 키가 설정되어 있으면 검증
        if ( ! empty( $stored_api_key ) ) {
            if ( empty( $api_key ) || ! hash_equals( $stored_api_key, $api_key ) ) {
                JJ_License_Security::log_security_event( 'invalid_api_key', array(
                    'ip' => $ip,
                    'endpoint' => $request->get_route(),
                ) );
                return new WP_Error( 'invalid_api_key', __( '유효하지 않은 API 키입니다.', 'jj-license-manager' ), array( 'status' => 401 ) );
            }
        }
        
        return true;
    }
    
    /**
     * 라이센스 검증 API
     * 
     * @param WP_REST_Request $request 요청 객체
     * @return WP_REST_Response 응답 객체
     */
    public function verify_license( $request ) {
        $security_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-security.php';
        $cache_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-cache.php';
        
        if ( file_exists( $security_file ) ) {
            require_once $security_file;
        }
        if ( file_exists( $cache_file ) ) {
            require_once $cache_file;
        }
        
        // 입력값 검증 및 sanitization
        $license_key = JJ_License_Security::validate_input( $request->get_param( 'license_key' ), 'license_key' );
        $site_id = JJ_License_Security::validate_input( $request->get_param( 'site_id' ), 'site_id' );
        $site_url = JJ_License_Security::validate_input( $request->get_param( 'site_url' ), 'site_url' );
        $plugin_version = JJ_License_Security::validate_input( $request->get_param( 'plugin_version' ), 'text' );
        
        // 필수 파라미터 확인
        if ( ! $license_key || ! $site_id || ! $site_url ) {
            JJ_License_Security::log_security_event( 'invalid_parameters', array(
                'ip' => JJ_License_Security::get_client_ip(),
                'endpoint' => 'verify',
            ) );
            return new WP_REST_Response( array(
                'valid' => false,
                'message' => __( '필수 파라미터가 누락되었거나 유효하지 않습니다.', 'jj-license-manager' ),
            ), 400 );
        }
        
        // 캐시 확인
        $cached_result = JJ_License_Cache::get_verification_result( $license_key, $site_id );
        if ( $cached_result !== false ) {
            $cached_result['plugin_version'] = $plugin_version;
            $cached_result['cached'] = true;
            return new WP_REST_Response( $cached_result, $cached_result['valid'] ? 200 : 400 );
        }
        
        // 라이센스 검증
        $validator = new JJ_License_Validator();
        $result = $validator->verify( $license_key, $site_id, $site_url );
        
        // 캐시 저장
        if ( isset( $result['valid'] ) ) {
            JJ_License_Cache::set_verification_result( $license_key, $site_id, $result );
        }
        
        // 플러그인 버전 정보 추가
        $result['plugin_version'] = $plugin_version;
        
        return new WP_REST_Response( $result, $result['valid'] ? 200 : 400 );
    }
    
    /**
     * 라이센스 활성화 API
     * 
     * @param WP_REST_Request $request 요청 객체
     * @return WP_REST_Response 응답 객체
     */
    public function activate_license( $request ) {
        $security_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-security.php';
        $cache_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-cache.php';
        
        if ( file_exists( $security_file ) ) {
            require_once $security_file;
        }
        if ( file_exists( $cache_file ) ) {
            require_once $cache_file;
        }
        
        // 입력값 검증 및 sanitization
        $license_key = JJ_License_Security::validate_input( $request->get_param( 'license_key' ), 'license_key' );
        $site_id = JJ_License_Security::validate_input( $request->get_param( 'site_id' ), 'site_id' );
        $site_url = JJ_License_Security::validate_input( $request->get_param( 'site_url' ), 'site_url' );
        
        // 필수 파라미터 확인
        if ( ! $license_key || ! $site_id || ! $site_url ) {
            JJ_License_Security::log_security_event( 'invalid_parameters', array(
                'ip' => JJ_License_Security::get_client_ip(),
                'endpoint' => 'activate',
            ) );
            return new WP_REST_Response( array(
                'valid' => false,
                'message' => __( '필수 파라미터가 누락되었거나 유효하지 않습니다.', 'jj-license-manager' ),
            ), 400 );
        }
        
        // 라이센스 검증 및 활성화
        $validator = new JJ_License_Validator();
        $result = $validator->verify( $license_key, $site_id, $site_url );
        
        // 활성화 성공 시 캐시 삭제
        if ( isset( $result['valid'] ) && $result['valid'] ) {
            JJ_License_Cache::delete_cache( $license_key, $site_id );
        }
        
        return new WP_REST_Response( $result, $result['valid'] ? 200 : 400 );
    }
    
    /**
     * 라이센스 비활성화 API
     * 
     * @param WP_REST_Request $request 요청 객체
     * @return WP_REST_Response 응답 객체
     */
    public function deactivate_license( $request ) {
        $security_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-security.php';
        $cache_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-cache.php';
        
        if ( file_exists( $security_file ) ) {
            require_once $security_file;
        }
        if ( file_exists( $cache_file ) ) {
            require_once $cache_file;
        }
        
        // 입력값 검증 및 sanitization
        $license_key = JJ_License_Security::validate_input( $request->get_param( 'license_key' ), 'license_key' );
        $site_id = JJ_License_Security::validate_input( $request->get_param( 'site_id' ), 'site_id' );
        
        // 필수 파라미터 확인
        if ( ! $license_key || ! $site_id ) {
            JJ_License_Security::log_security_event( 'invalid_parameters', array(
                'ip' => JJ_License_Security::get_client_ip(),
                'endpoint' => 'deactivate',
            ) );
            return new WP_REST_Response( array(
                'success' => false,
                'message' => __( '필수 파라미터가 누락되었거나 유효하지 않습니다.', 'jj-license-manager' ),
            ), 400 );
        }
        
        global $wpdb;
        
        $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
        $table_activations = JJ_License_Database::get_table_name( 'activations' );
        
        // SQL Injection 방지 - prepared statement 사용
        $query = "SELECT * FROM {$table_licenses} WHERE license_key = %s";
        if ( ! JJ_License_Security::validate_sql_query( $query ) ) {
            JJ_License_Security::log_security_event( 'sql_injection_attempt', array(
                'ip' => JJ_License_Security::get_client_ip(),
                'query' => $query,
            ) );
            return new WP_REST_Response( array(
                'success' => false,
                'message' => __( '보안 검증에 실패했습니다.', 'jj-license-manager' ),
            ), 403 );
        }
        
        // 라이센스 조회
        $license = $wpdb->get_row( $wpdb->prepare( $query, $license_key ), ARRAY_A );
        
        if ( ! $license ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => __( '라이센스 키를 찾을 수 없습니다.', 'jj-license-manager' ),
            ), 404 );
        }
        
        // 활성화 비활성화
        $result = $wpdb->update(
            $table_activations,
            array(
                'is_active' => 0,
                'deactivated_at' => current_time( 'mysql' ),
            ),
            array(
                'license_id' => intval( $license['id'] ),
                'site_id' => $site_id,
                'is_active' => 1,
            ),
            array( '%d', '%s' ),
            array( '%d', '%s', '%d' )
        );
        
        if ( $result ) {
            // 캐시 삭제
            JJ_License_Cache::delete_cache( $license_key, $site_id );
            
            // 히스토리 기록
            $wpdb->insert(
                JJ_License_Database::get_table_name( 'history' ),
                array(
                    'license_id' => intval( $license['id'] ),
                    'action' => 'deactivated',
                    'description' => sprintf(
                        __( '사이트에서 비활성화됨: %s', 'jj-license-manager' ),
                        sanitize_text_field( $site_id )
                    ),
                    'performed_at' => function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
                ),
                array( '%d', '%s', '%s', '%s' )
            );
            
            return new WP_REST_Response( array(
                'success' => true,
                'message' => __( '라이센스가 비활성화되었습니다.', 'jj-license-manager' ),
            ), 200 );
        }
        
        return new WP_REST_Response( array(
            'success' => false,
            'message' => __( '비활성화에 실패했습니다.', 'jj-license-manager' ),
        ), 400 );
    }
    
    /**
     * 업데이트 체크 API
     * 
     * @param WP_REST_Request $request 요청 객체
     * @return WP_REST_Response 응답 객체
     */
    public function check_update( $request ) {
        $security_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-security.php';
        $update_api_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-update-api.php';
        
        if ( file_exists( $security_file ) ) {
            require_once $security_file;
        }
        if ( file_exists( $update_api_file ) ) {
            require_once $update_api_file;
        }
        
        // 입력값 검증 및 sanitization
        $plugin_slug = JJ_License_Security::validate_input( $request->get_param( 'plugin_slug' ), 'text' );
        $current_version = JJ_License_Security::validate_input( $request->get_param( 'current_version' ), 'text' );
        $license_key = JJ_License_Security::validate_input( $request->get_param( 'license_key' ), 'license_key' );
        
        // 필수 파라미터 확인
        if ( ! $plugin_slug || ! $current_version ) {
            return new WP_REST_Response( array(
                'has_update' => false,
                'message' => __( '필수 파라미터가 누락되었습니다.', 'jj-license-manager' ),
            ), 400 );
        }
        
        // 업데이트 정보 가져오기
        $update_api = new JJ_License_Update_API();
        $update_info = $update_api->get_update_info( $plugin_slug, $current_version, $license_key );
        
        if ( $update_info && isset( $update_info['has_update'] ) && $update_info['has_update'] ) {
            return new WP_REST_Response( $update_info, 200 );
        }
        
        return new WP_REST_Response( array(
            'has_update' => false,
            'message' => __( '업데이트가 없습니다.', 'jj-license-manager' ),
        ), 200 );
    }
    
    /**
     * 플러그인 다운로드 API
     * 
     * @param WP_REST_Request $request 요청 객체
     * @return WP_REST_Response|WP_Error 응답 객체 또는 에러
     */
    public function download_plugin( $request ) {
        $security_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-security.php';
        $validator_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-validator.php';
        
        if ( file_exists( $security_file ) ) {
            require_once $security_file;
        }
        if ( file_exists( $validator_file ) ) {
            require_once $validator_file;
        }
        
        // 입력값 검증 및 sanitization
        $plugin_slug = JJ_License_Security::validate_input( $request->get_param( 'plugin_slug' ), 'text' );
        $version = JJ_License_Security::validate_input( $request->get_param( 'version' ), 'text' );
        $license_key = JJ_License_Security::validate_input( $request->get_param( 'license_key' ), 'license_key' );
        $site_id = JJ_License_Security::validate_input( $request->get_param( 'site_id' ), 'site_id' );
        
        // 필수 파라미터 확인
        if ( ! $plugin_slug || ! $version || ! $license_key || ! $site_id ) {
            return new WP_Error( 'missing_parameters', __( '필수 파라미터가 누락되었습니다.', 'jj-license-manager' ), array( 'status' => 400 ) );
        }
        
        // 라이센스 검증
        $validator = new JJ_License_Validator();
        $license_result = $validator->verify( $license_key, $site_id, home_url() );
        
        if ( ! $license_result || ! isset( $license_result['valid'] ) || ! $license_result['valid'] ) {
            return new WP_Error( 'invalid_license', __( '유효하지 않은 라이센스입니다.', 'jj-license-manager' ), array( 'status' => 403 ) );
        }
        
        // 플러그인 파일 경로 생성
        $plugin_file_path = $this->get_plugin_file_path( $plugin_slug, $version );
        
        if ( ! $plugin_file_path || ! file_exists( $plugin_file_path ) ) {
            return new WP_Error( 'file_not_found', __( '플러그인 파일을 찾을 수 없습니다.', 'jj-license-manager' ), array( 'status' => 404 ) );
        }
        
        // 파일 다운로드
        header( 'Content-Type: application/zip' );
        header( 'Content-Disposition: attachment; filename="' . basename( $plugin_file_path ) . '"' );
        header( 'Content-Length: ' . filesize( $plugin_file_path ) );
        header( 'Cache-Control: no-cache, must-revalidate' );
        header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
        
        readfile( $plugin_file_path );
        exit;
    }
    
    /**
     * 플러그인 파일 경로 가져오기
     * 
     * @param string $plugin_slug 플러그인 슬러그
     * @param string $version 버전
     * @return string|false 파일 경로 또는 false
     */
    private function get_plugin_file_path( $plugin_slug, $version ) {
        // 플러그인 파일 저장 디렉토리
        // [v2.0.2] WordPress 함수 안전 호출
        if ( ! function_exists( 'wp_upload_dir' ) ) {
            return false;
        }
        
        $upload_dir = wp_upload_dir();
        if ( ! isset( $upload_dir['basedir'] ) ) {
            return false;
        }
        
        $plugins_dir = $upload_dir['basedir'] . '/jj-plugin-updates';
        
        // 디렉토리가 없으면 생성
        if ( ! file_exists( $plugins_dir ) ) {
            if ( function_exists( 'wp_mkdir_p' ) ) {
                wp_mkdir_p( $plugins_dir );
            } else {
                @mkdir( $plugins_dir, 0755, true );
            }
        }
        
        // 파일 경로 생성
        $file_name = sanitize_file_name( $plugin_slug . '-' . $version . '.zip' );
        $file_path = $plugins_dir . '/' . $file_name;
        
        return $file_path;
    }
    
    /**
     * 보안 경고 수신 API
     * 
     * 플러그인에서 코드 수정 감지 시 개발자 서버로 전송되는 알림을 수신합니다.
     * 
     * @param WP_REST_Request $request 요청 객체
     * @return WP_REST_Response 응답 객체
     */
    public function receive_security_alert( $request ) {
        $security_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-security.php';
        if ( file_exists( $security_file ) ) {
            require_once $security_file;
        }
        
        // IP 주소 확인 (개발자 서버에서만 접근 가능하도록)
        $allowed_ips = function_exists( 'get_option' ) ? get_option( 'jj_license_manager_allowed_ips', array() ) : array();
        $client_ip = JJ_License_Security::get_client_ip();
        
        // IP 화이트리스트가 설정되어 있으면 검증
        if ( ! empty( $allowed_ips ) && ! in_array( $client_ip, $allowed_ips, true ) ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => __( '허용되지 않은 IP 주소입니다.', 'jj-license-manager' ),
            ), 403 );
        }
        
        // 요청 데이터 가져오기
        $data = $request->get_json_params();
        
        if ( empty( $data ) ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => __( '요청 데이터가 없습니다.', 'jj-license-manager' ),
            ), 400 );
        }
        
        // 보안 이벤트 로깅
        require_once JJ_NEURAL_LINK_PATH . 'includes/admin/class-jj-license-admin.php';
        $admin = JJ_License_Admin::instance();
        
        // 데이터베이스에 보안 이벤트 저장
        global $wpdb;
        $table_history = JJ_License_Database::get_table_name( 'history' );
        
        // 라이센스 키로 라이센스 ID 찾기
        $license_key = isset( $data['license_key'] ) ? sanitize_text_field( $data['license_key'] ) : '';
        $license_id = null;
        
        if ( ! empty( $license_key ) ) {
            $table_licenses = JJ_License_Database::get_table_name( 'licenses' );
            $license = $wpdb->get_row( $wpdb->prepare(
                "SELECT id FROM {$table_licenses} WHERE license_key = %s",
                $license_key
            ), ARRAY_A );
            
            if ( $license ) {
                $license_id = intval( $license['id'] );
            }
        }
        
        // 보안 이벤트 기록
        $wpdb->insert(
            $table_history,
            array(
                'license_id' => $license_id,
                'action' => 'security_violation',
                'description' => sprintf(
                    __( '보안 위반 감지: %s - 사이트: %s', 'jj-license-manager' ),
                    isset( $data['violations'] ) ? json_encode( $data['violations'] ) : '',
                    isset( $data['site_url'] ) ? esc_url_raw( $data['site_url'] ) : ''
                ),
                'performed_at' => function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ),
            ),
            array( '%d', '%s', '%s', '%s' )
        );
        
        // 관리자에게 이메일 알림 (선택사항)
        if ( function_exists( 'get_option' ) && get_option( 'jj_license_manager_security_alerts', false ) ) {
            $this->send_security_email( $data );
        }
        
        return new WP_REST_Response( array(
            'success' => true,
            'message' => __( '보안 경고가 수신되었습니다.', 'jj-license-manager' ),
        ), 200 );
    }
    
    /**
     * 잠금 해제 코드 검증 API
     * 
     * @param WP_REST_Request $request 요청 객체
     * @return WP_REST_Response 응답 객체
     */
    public function verify_unlock_code( $request ) {
        $security_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-security.php';
        if ( file_exists( $security_file ) ) {
            require_once $security_file;
        }
        
        $unlock_code = JJ_License_Security::validate_input( $request->get_param( 'unlock_code' ), 'text' );
        $site_id = JJ_License_Security::validate_input( $request->get_param( 'site_id' ), 'site_id' );
        $license_key = JJ_License_Security::validate_input( $request->get_param( 'license_key' ), 'license_key' );
        
        if ( empty( $unlock_code ) || empty( $site_id ) || empty( $license_key ) ) {
            return new WP_REST_Response( array(
                'verified' => false,
                'message' => __( '필수 파라미터가 누락되었습니다.', 'jj-license-manager' ),
            ), 400 );
        }
        
        // 라이센스 검증
        $validator = new JJ_License_Validator();
        $license_result = $validator->verify( $license_key, $site_id, home_url() );
        
        if ( ! $license_result || ! isset( $license_result['valid'] ) || ! $license_result['valid'] ) {
            return new WP_REST_Response( array(
                'verified' => false,
                'message' => __( '유효하지 않은 라이센스입니다.', 'jj-license-manager' ),
            ), 403 );
        }
        
        // 잠금 해제 코드 검증 (실제 구현은 개발자 서버에서 수행)
        // 여기서는 기본 검증만 수행
        $stored_unlock_code = function_exists( 'get_option' ) ? get_option( 'jj_unlock_code_' . md5( $license_key . $site_id ), '' ) : '';
        
        if ( ! empty( $stored_unlock_code ) && hash_equals( $stored_unlock_code, $unlock_code ) ) {
            return new WP_REST_Response( array(
                'verified' => true,
                'message' => __( '잠금 해제 코드가 확인되었습니다.', 'jj-license-manager' ),
            ), 200 );
        }
        
        // 개발자가 직접 생성한 잠금 해제 코드 검증 (추가 검증 로직 필요)
        // 실제로는 개발자 서버에서 검증해야 함
        
        return new WP_REST_Response( array(
            'verified' => false,
            'message' => __( '유효하지 않은 잠금 해제 코드입니다.', 'jj-license-manager' ),
        ), 403 );
    }
    
    /**
     * 보안 이메일 발송
     * 
     * @param array $data 보안 이벤트 데이터
     */
    private function send_security_email( $data ) {
        $admin_email = function_exists( 'get_option' ) ? get_option( 'admin_email' ) : '';
        $site_url = isset( $data['site_url'] ) ? $data['site_url'] : ( function_exists( 'home_url' ) ? home_url() : '' );
        
        $subject = sprintf( '[보안 경고] %s - 코드 무결성 위반 감지', $site_url );
        
        $message = sprintf(
            "보안 위반이 감지되었습니다.\n\n" .
            "사이트 URL: %s\n" .
            "사이트 ID: %s\n" .
            "라이센스 키: %s\n" .
            "발생 시간: %s\n\n" .
            "위반 사항:\n%s\n\n" .
            "파일 변경 사항:\n%s\n\n" .
            "라이센스 정보:\n%s",
            $site_url,
            isset( $data['site_id'] ) ? $data['site_id'] : 'N/A',
            isset( $data['license_key'] ) ? $data['license_key'] : 'N/A',
            isset( $data['timestamp'] ) ? date( 'Y-m-d H:i:s', $data['timestamp'] ) : ( function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' ) ),
            isset( $data['violations'] ) ? print_r( $data['violations'], true ) : 'N/A',
            isset( $data['file_changes'] ) ? print_r( $data['file_changes'], true ) : 'N/A',
            isset( $data['license_info'] ) ? print_r( $data['license_info'], true ) : 'N/A'
        );
        
        wp_mail( $admin_email, $subject, $message );
    }
}


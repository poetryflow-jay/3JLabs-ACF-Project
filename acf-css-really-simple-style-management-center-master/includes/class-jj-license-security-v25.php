<?php
/**
 * [v25.0.0] 라이센스 보안 강화 모듈
 * 
 * 가짜 라이센스 키 방지 및 권한 속임 방지 시스템
 * 
 * @package ACF_CSS_Style_Guide
 * @version 25.0.0
 * @author 3J Labs
 * 
 * 주요 기능:
 * - 가짜 라이센스 키 감지 및 차단
 * - 라이센스 플랜 변조 방지
 * - 서버 측 라이센스 검증
 * - 권한 속임 방지 (마스터/파트너 사기 방지)
 * - 라이센스 키 암호화 및 서명 검증
 * - 실시간 라이센스 상태 모니터링
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Security_V25 {

    private static $instance = null;
    private $license_server_url = 'https://license.3j-labs.com';
    private $verification_cache_time = 3600; // 1시간
    private $strict_mode = true;
    private $detected_violations = array();

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
        $this->load_settings();
    }

    /**
     * 설정 로드
     */
    private function load_settings() {
        $settings = get_option( 'jj_license_security_settings_v25', array(
            'strict_mode' => true,
            'server_verification' => true,
            'cache_verification' => true,
            'auto_revoke_on_violation' => true,
            'log_violations' => true,
        ) );

        $this->strict_mode = ! empty( $settings['strict_mode'] );
        $this->license_server_url = get_option( 'jj_license_manager_server_url', 'https://license.3j-labs.com' );
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 라이센스 키 검증 필터
        add_filter( 'jj_license_is_valid', array( $this, 'verify_license_key' ), 10, 2 );
        add_filter( 'jj_license_type', array( $this, 'verify_license_type' ), 10, 2 );
        add_filter( 'jj_license_edition', array( $this, 'verify_license_edition' ), 10, 2 );
        
        // 라이센스 키 저장 전 검증
        add_action( 'update_option_jj_style_guide_license_key', array( $this, 'validate_license_before_save' ), 10, 2 );
        
        // 라이센스 상태 모니터링
        add_action( 'admin_init', array( $this, 'monitor_license_status' ), 1 );
        
        // 주기적 서버 검증 (매일)
        if ( ! wp_next_scheduled( 'jj_license_server_verification_v25' ) ) {
            wp_schedule_event( time(), 'daily', 'jj_license_server_verification_v25' );
        }
        add_action( 'jj_license_server_verification_v25', array( $this, 'verify_with_server' ) );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_verify_license_security', array( $this, 'ajax_verify_license' ) );
        add_action( 'wp_ajax_jj_revoke_fake_license', array( $this, 'ajax_revoke_fake_license' ) );
    }

    /**
     * [v25.0.0] 라이센스 키 검증 (가짜 키 감지)
     */
    public function verify_license_key( $is_valid, $license_key ) {
        if ( empty( $license_key ) ) {
            return false;
        }

        // 1. 형식 검증
        if ( ! $this->validate_license_format( $license_key ) ) {
            $this->log_violation( 'invalid_format', array( 'license_key' => substr( $license_key, 0, 10 ) . '...' ) );
            return false;
        }

        // 2. 로컬 해시 검증
        $stored_hash = get_option( 'jj_license_key_hash_v25', '' );
        if ( ! empty( $stored_hash ) ) {
            $current_hash = hash( 'sha512', $license_key );
            if ( $stored_hash !== $current_hash ) {
                // 키가 변경되었음 - 서버 재검증 필요
                $this->log_violation( 'key_changed', array( 'old_hash' => substr( $stored_hash, 0, 16 ) ) );
                return $this->verify_with_server( $license_key );
            }
        }

        // 3. 서버 검증 (캐시 확인)
        $cached_verification = get_transient( 'jj_license_verification_v25' );
        if ( $cached_verification !== false ) {
            return $cached_verification['is_valid'];
        }

        // 4. 서버 검증 실행
        return $this->verify_with_server( $license_key );
    }

    /**
     * [v25.0.0] 라이센스 타입 검증 (권한 속임 방지)
     */
    public function verify_license_type( $license_type, $license_key ) {
        if ( empty( $license_key ) ) {
            return 'FREE';
        }

        // 서버에서 실제 타입 가져오기
        $server_data = $this->get_license_data_from_server( $license_key );
        
        if ( $server_data && isset( $server_data['type'] ) ) {
            $server_type = $server_data['type'];
            
            // 로컬 타입과 서버 타입이 다르면 위반
            if ( $license_type !== $server_type ) {
                $this->log_violation( 'type_mismatch', array(
                    'local_type' => $license_type,
                    'server_type' => $server_type,
                    'license_key' => substr( $license_key, 0, 10 ) . '...'
                ) );
                
                // 위반 시 FREE로 강제
                if ( $this->strict_mode ) {
                    $this->revoke_license();
                    return 'FREE';
                }
            }
            
            return $server_type;
        }

        return $license_type;
    }

    /**
     * [v25.0.0] 라이센스 에디션 검증 (마스터/파트너 사기 방지)
     */
    public function verify_license_edition( $edition, $license_key ) {
        if ( empty( $license_key ) ) {
            return 'free';
        }

        // 서버에서 실제 에디션 가져오기
        $server_data = $this->get_license_data_from_server( $license_key );
        
        if ( $server_data && isset( $server_data['edition'] ) ) {
            $server_edition = $server_data['edition'];
            
            // 로컬 에디션과 서버 에디션이 다르면 위반
            if ( $edition !== $server_edition ) {
                $this->log_violation( 'edition_mismatch', array(
                    'local_edition' => $edition,
                    'server_edition' => $server_edition,
                    'license_key' => substr( $license_key, 0, 10 ) . '...'
                ) );
                
                // 위반 시 free로 강제
                if ( $this->strict_mode ) {
                    $this->revoke_license();
                    return 'free';
                }
            }
            
            return $server_edition;
        }

        return $edition;
    }

    /**
     * [v25.0.0] 라이센스 형식 검증
     */
    private function validate_license_format( $license_key ) {
        if ( empty( $license_key ) ) {
            return false;
        }

        // 기본 형식 검증 (예: XXXXX-XXXXX-XXXXX-XXXXX)
        $pattern = '/^[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}$/';
        if ( ! preg_match( $pattern, $license_key ) ) {
            return false;
        }

        // 체크섬 검증
        return $this->validate_checksum( $license_key );
    }

    /**
     * [v25.0.0] 체크섬 검증
     */
    private function validate_checksum( $license_key ) {
        // 하이픈 제거
        $key_without_dashes = str_replace( '-', '', $license_key );
        
        // 마지막 4자리가 체크섬
        $checksum = substr( $key_without_dashes, -4 );
        $data = substr( $key_without_dashes, 0, -4 );
        
        // 체크섬 계산
        $calculated_checksum = $this->calculate_checksum( $data );
        
        return hash_equals( $checksum, $calculated_checksum );
    }

    /**
     * [v25.0.0] 체크섬 계산
     */
    private function calculate_checksum( $data ) {
        // 간단한 체크섬 알고리즘 (실제로는 더 복잡한 알고리즘 사용)
        $hash = hash( 'sha256', $data . wp_salt( 'auth' ) );
        return strtoupper( substr( $hash, 0, 4 ) );
    }

    /**
     * [v25.0.0] 서버 검증
     */
    public function verify_with_server( $license_key = null ) {
        if ( $license_key === null ) {
            $license_key = get_option( 'jj_style_guide_license_key', '' );
        }

        if ( empty( $license_key ) ) {
            return false;
        }

        // 서버에 검증 요청
        $response = $this->request_server_verification( $license_key );
        
        if ( $response && isset( $response['is_valid'] ) ) {
            $is_valid = (bool) $response['is_valid'];
            
            // 캐시에 저장
            set_transient( 'jj_license_verification_v25', array(
                'is_valid' => $is_valid,
                'verified_at' => time(),
                'license_type' => $response['type'] ?? 'FREE',
                'license_edition' => $response['edition'] ?? 'free',
            ), $this->verification_cache_time );
            
            // 해시 저장
            if ( $is_valid ) {
                update_option( 'jj_license_key_hash_v25', hash( 'sha512', $license_key ) );
            }
            
            // 위반 감지
            if ( ! $is_valid ) {
                $this->log_violation( 'server_verification_failed', array(
                    'license_key' => substr( $license_key, 0, 10 ) . '...',
                    'response' => $response
                ) );
                
                if ( $this->strict_mode ) {
                    $this->revoke_license();
                }
            }
            
            return $is_valid;
        }

        // 서버 응답 실패 시 캐시된 결과 사용 (오프라인 모드)
        $cached = get_transient( 'jj_license_verification_v25' );
        if ( $cached !== false ) {
            return $cached['is_valid'];
        }

        return false;
    }

    /**
     * [v25.0.0] 서버 검증 요청
     */
    private function request_server_verification( $license_key ) {
        $site_url = home_url();
        $site_name = get_bloginfo( 'name' );
        
        $request_data = array(
            'license_key' => $license_key,
            'site_url' => $site_url,
            'site_name' => $site_name,
            'plugin_version' => defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '25.0.0',
            'wp_version' => get_bloginfo( 'version' ),
            'timestamp' => time(),
        );

        // 서명 생성
        $signature = $this->sign_request( $request_data );
        $request_data['signature'] = $signature;

        // 서버에 요청
        $response = wp_remote_post( $this->license_server_url . '/api/v1/verify', array(
            'timeout' => 10,
            'body' => json_encode( $request_data ),
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( ! $data || ! isset( $data['success'] ) ) {
            return false;
        }

        // 서버 응답 서명 검증
        if ( isset( $data['signature'] ) ) {
            if ( ! $this->verify_server_response( $data ) ) {
                $this->log_violation( 'server_response_signature_invalid', array() );
                return false;
            }
        }

        return $data;
    }

    /**
     * [v25.0.0] 서버에서 라이센스 데이터 가져오기
     */
    private function get_license_data_from_server( $license_key ) {
        $cached = get_transient( 'jj_license_data_v25' );
        if ( $cached !== false ) {
            return $cached;
        }

        $response = $this->request_server_verification( $license_key );
        
        if ( $response && isset( $response['data'] ) ) {
            $data = $response['data'];
            
            // 캐시에 저장
            set_transient( 'jj_license_data_v25', $data, $this->verification_cache_time );
            
            return $data;
        }

        return false;
    }

    /**
     * [v25.0.0] 요청 서명 생성
     */
    private function sign_request( $data ) {
        $secret = wp_salt( 'auth' );
        $payload = json_encode( $data, JSON_UNESCAPED_UNICODE );
        return hash_hmac( 'sha256', $payload, $secret );
    }

    /**
     * [v25.0.0] 서버 응답 서명 검증
     */
    private function verify_server_response( $data ) {
        // 서버의 공개 키로 서명 검증 (실제 구현 시)
        // 여기서는 기본 검증만 수행
        if ( ! isset( $data['signature'] ) ) {
            return false;
        }

        // 서명이 있으면 유효한 것으로 간주 (실제로는 공개 키로 검증)
        return ! empty( $data['signature'] );
    }

    /**
     * [v25.0.0] 라이센스 저장 전 검증
     */
    public function validate_license_before_save( $old_value, $new_value ) {
        if ( empty( $new_value ) ) {
            return;
        }

        // 즉시 서버 검증
        $is_valid = $this->verify_with_server( $new_value );
        
        if ( ! $is_valid ) {
            // 잘못된 라이센스 키 - 저장 차단
            add_action( 'admin_notices', function() {
                ?>
                <div class="notice notice-error">
                    <p>
                        <strong><?php _e( '⚠️ 라이센스 키 검증 실패', 'acf-css-really-simple-style-management-center' ); ?></strong>
                        <?php _e( '입력하신 라이센스 키가 유효하지 않거나 서버에서 검증할 수 없습니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </div>
                <?php
            } );
            
            // 옵션 업데이트 롤백
            update_option( 'jj_style_guide_license_key', $old_value );
            return;
        }

        // 라이센스 타입 및 에디션 검증
        $server_data = $this->get_license_data_from_server( $new_value );
        
        if ( $server_data ) {
            // 서버에서 받은 타입과 에디션으로 업데이트
            if ( isset( $server_data['type'] ) ) {
                update_option( 'jj_style_guide_license_type', $server_data['type'] );
            }
            
            if ( isset( $server_data['edition'] ) ) {
                update_option( 'jj_style_guide_license_edition', $server_data['edition'] );
            }
        }
    }

    /**
     * [v25.0.0] 라이센스 상태 모니터링
     */
    public function monitor_license_status() {
        if ( ! is_admin() ) {
            return;
        }

        $license_key = get_option( 'jj_style_guide_license_key', '' );
        
        if ( empty( $license_key ) ) {
            return;
        }

        // 마지막 검증 시간 확인
        $last_verification = get_transient( 'jj_license_last_verification_v25' );
        $current_time = time();
        
        // 1시간마다 검증
        if ( $last_verification === false || ( $current_time - $last_verification ) > $this->verification_cache_time ) {
            $this->verify_with_server( $license_key );
            set_transient( 'jj_license_last_verification_v25', $current_time, $this->verification_cache_time );
        }

        // 위반 감지 확인
        $this->check_violations();
    }

    /**
     * [v25.0.0] 위반 감지 확인
     */
    private function check_violations() {
        $violations = get_option( 'jj_license_violations_v25', array() );
        
        if ( empty( $violations ) ) {
            return;
        }

        // 최근 위반이 있으면 알림
        $recent_violations = array_filter( $violations, function( $v ) {
            return ( time() - $v['timestamp'] ) < 86400; // 24시간 이내
        } );

        if ( ! empty( $recent_violations ) ) {
            add_action( 'admin_notices', function() use ( $recent_violations ) {
                ?>
                <div class="notice notice-error">
                    <p>
                        <strong><?php _e( '⚠️ 라이센스 보안 위반 감지', 'acf-css-really-simple-style-management-center' ); ?></strong>
                        <?php printf( __( '%d개의 보안 위반이 감지되었습니다. 라이센스가 무효화되었을 수 있습니다.', 'acf-css-really-simple-style-management-center' ), count( $recent_violations ) ); ?>
                        <a href="<?php echo admin_url( 'admin.php?page=jj-style-guide-security' ); ?>" class="button button-primary" style="margin-left: 10px;">
                            <?php _e( '상세 확인', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </p>
                </div>
                <?php
            } );
        }
    }

    /**
     * [v25.0.0] 라이센스 무효화
     */
    private function revoke_license() {
        // 라이센스 키 제거
        delete_option( 'jj_style_guide_license_key' );
        delete_option( 'jj_style_guide_license_type' );
        delete_option( 'jj_style_guide_license_edition' );
        
        // 캐시 제거
        delete_transient( 'jj_license_verification_v25' );
        delete_transient( 'jj_license_data_v25' );
        delete_transient( 'jj_license_last_verification_v25' );
        
        // 해시 제거
        delete_option( 'jj_license_key_hash_v25' );
        
        // 위반 로그 기록
        $this->log_violation( 'license_revoked', array(
            'reason' => 'security_violation',
            'timestamp' => time()
        ) );
    }

    /**
     * [v25.0.0] 위반 로그 기록
     */
    private function log_violation( $violation_type, $data = array() ) {
        $violations = get_option( 'jj_license_violations_v25', array() );
        
        $violations[] = array(
            'type' => $violation_type,
            'data' => $data,
            'timestamp' => time(),
            'user_id' => get_current_user_id(),
            'ip_address' => $this->get_client_ip(),
        );

        // 최대 1000개까지만 유지
        if ( count( $violations ) > 1000 ) {
            $violations = array_slice( $violations, -1000 );
        }

        update_option( 'jj_license_violations_v25', $violations );
    }

    /**
     * [v25.0.0] AJAX: 라이센스 검증
     */
    public function ajax_verify_license() {
        check_ajax_referer( 'jj_license_security_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
        
        if ( empty( $license_key ) ) {
            $license_key = get_option( 'jj_style_guide_license_key', '' );
        }

        $result = $this->verify_with_server( $license_key );
        $server_data = $this->get_license_data_from_server( $license_key );

        wp_send_json_success( array(
            'is_valid' => $result,
            'license_data' => $server_data,
            'violations' => get_option( 'jj_license_violations_v25', array() ),
        ) );
    }

    /**
     * [v25.0.0] AJAX: 가짜 라이센스 무효화
     */
    public function ajax_revoke_fake_license() {
        check_ajax_referer( 'jj_license_security_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $this->revoke_license();

        wp_send_json_success( array(
            'message' => __( '라이센스가 무효화되었습니다.', 'acf-css-really-simple-style-management-center' )
        ) );
    }

    /**
     * 클라이언트 IP 주소 가져오기
     */
    private function get_client_ip() {
        $ip_keys = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
        
        foreach ( $ip_keys as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ip = sanitize_text_field( $_SERVER[ $key ] );
                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }
        
        return 'unknown';
    }
}

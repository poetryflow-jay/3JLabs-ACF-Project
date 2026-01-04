<?php
/**
 * [v25.0.0] 업데이트 보안 강화 모듈
 * 
 * 업데이트 하이재킹 방지 및 서명 검증 시스템
 * 
 * @package ACF_CSS_Style_Guide
 * @version 25.0.0
 * @author 3J Labs
 * 
 * 주요 기능:
 * - 업데이트 서버 응답 서명 검증
 * - 채널 인증 강화
 * - 롤백 보호
 * - 업데이트 소스 검증
 * - Ed25519 서명 검증
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Update_Security_V25 {

    private static $instance = null;
    private $public_key = null;
    private $update_server_url = 'https://updates.3j-labs.com';
    private $signature_required = true;
    private $channel_verification = true;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_public_key();
        $this->init_hooks();
    }

    /**
     * 공개 키 초기화
     */
    private function init_public_key() {
        // [v25.0.0] Ed25519 공개 키 (서버에서 제공하는 업데이트 서명 검증용)
        // 실제 운영 환경에서는 서버에서 제공하는 키를 사용
        $this->public_key = get_option( 'jj_update_public_key_v25', '' );
        
        if ( empty( $this->public_key ) ) {
            // 기본 공개 키 (개발용, 실제로는 서버에서 제공)
            $this->public_key = $this->get_default_public_key();
        }
    }

    /**
     * 기본 공개 키 (개발용)
     */
    private function get_default_public_key() {
        // 실제 운영 환경에서는 서버에서 제공하는 키를 사용해야 함
        return '3j-labs-update-signing-key-v25'; // 플레이스홀더
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 업데이트 응답 검증
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'verify_update_response' ), 10, 2 );
        
        // 업데이트 다운로드 전 검증
        add_filter( 'upgrader_pre_download', array( $this, 'verify_update_package' ), 10, 3 );
        
        // 업데이트 후 검증
        add_action( 'upgrader_process_complete', array( $this, 'verify_installed_update' ), 10, 2 );
        
        // 롤백 보호
        add_filter( 'wp_plugin_update_rows', array( $this, 'prevent_unauthorized_rollback' ), 10, 1 );
    }

    /**
     * [v25.0.0] 업데이트 응답 검증
     */
    public function verify_update_response( $transient, $transient_key ) {
        if ( empty( $transient->response ) ) {
            return $transient;
        }

        $plugin_slug = 'acf-css-really-simple-style-guide/acf-css-really-simple-style-guide.php';
        
        if ( ! isset( $transient->response[ $plugin_slug ] ) ) {
            return $transient;
        }

        $update_data = $transient->response[ $plugin_slug ];

        // 서명 검증
        if ( $this->signature_required && ! $this->verify_signature( $update_data ) ) {
            // 서명 검증 실패 - 업데이트 제거
            unset( $transient->response[ $plugin_slug ] );
            
            $this->log_security_event( 'update_response_signature_failed', array(
                'plugin' => $plugin_slug,
                'version' => $update_data->new_version ?? 'unknown',
            ) );
            
            return $transient;
        }

        // 채널 검증
        if ( $this->channel_verification && ! $this->verify_channel( $update_data ) ) {
            unset( $transient->response[ $plugin_slug ] );
            
            $this->log_security_event( 'update_response_channel_failed', array(
                'plugin' => $plugin_slug,
                'channel' => $update_data->channel ?? 'unknown',
            ) );
            
            return $transient;
        }

        // 소스 검증
        if ( ! $this->verify_source( $update_data ) ) {
            unset( $transient->response[ $plugin_slug ] );
            
            $this->log_security_event( 'update_response_source_failed', array(
                'plugin' => $plugin_slug,
                'package' => $update_data->package ?? 'unknown',
            ) );
            
            return $transient;
        }

        return $transient;
    }

    /**
     * [v25.0.0] 서명 검증 (Ed25519)
     */
    private function verify_signature( $update_data ) {
        if ( ! isset( $update_data->signature ) || ! isset( $update_data->signed_data ) ) {
            // 서명이 없으면 검증 실패 (개발 모드에서는 허용 가능)
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                return true; // 개발 모드에서는 서명 없이도 허용
            }
            return false;
        }

        $signature = $update_data->signature;
        $signed_data = $update_data->signed_data;
        
        // Ed25519 서명 검증 (PHP 7.2+)
        if ( function_exists( 'sodium_crypto_sign_verify_detached' ) ) {
            $public_key_bin = sodium_hex2bin( $this->public_key );
            $signature_bin = sodium_hex2bin( $signature );
            
            return sodium_crypto_sign_verify_detached( $signature_bin, $signed_data, $public_key_bin );
        }

        // Ed25519가 없으면 간단한 해시 검증 (보안 수준 낮음)
        $expected_hash = hash_hmac( 'sha256', $signed_data, $this->public_key );
        return hash_equals( $expected_hash, $signature );
    }

    /**
     * [v25.0.0] 채널 검증
     */
    private function verify_channel( $update_data ) {
        $allowed_channels = array( 'stable', 'staging', 'beta', 'alpha' );
        $update_channel = $update_data->channel ?? 'stable';
        
        // 현재 설정된 채널과 일치하는지 확인
        $current_channel = get_option( 'jj_style_guide_update_channel', 'stable' );
        
        // Stable 채널은 항상 허용
        if ( $update_channel === 'stable' ) {
            return true;
        }

        // 다른 채널은 현재 설정과 일치해야 함
        return $update_channel === $current_channel;
    }

    /**
     * [v25.0.0] 소스 검증
     */
    private function verify_source( $update_data ) {
        if ( ! isset( $update_data->package ) ) {
            return false;
        }

        $package_url = $update_data->package;
        $allowed_domains = array(
            '3j-labs.com',
            'updates.3j-labs.com',
            'j-j-labs.com',
        );

        $parsed_url = parse_url( $package_url );
        $domain = $parsed_url['host'] ?? '';

        // 허용된 도메인인지 확인
        foreach ( $allowed_domains as $allowed_domain ) {
            if ( $domain === $allowed_domain || strpos( $domain, '.' . $allowed_domain ) !== false ) {
                return true;
            }
        }

        return false;
    }

    /**
     * [v25.0.0] 업데이트 패키지 검증
     */
    public function verify_update_package( $reply, $package, $upgrader ) {
        // 패키지 다운로드 전 검증
        if ( strpos( $package, '3j-labs.com' ) === false && strpos( $package, 'j-j-labs.com' ) === false ) {
            return new WP_Error(
                'unauthorized_source',
                __( '허가되지 않은 업데이트 소스입니다.', 'acf-css-really-simple-style-management-center' )
            );
        }

        return $reply;
    }

    /**
     * [v25.0.0] 설치된 업데이트 검증
     */
    public function verify_installed_update( $upgrader, $hook_extra ) {
        if ( ! isset( $hook_extra['plugin'] ) ) {
            return;
        }

        $plugin_file = $hook_extra['plugin'];
        
        if ( $plugin_file !== 'acf-css-really-simple-style-guide/acf-css-really-simple-style-guide.php' ) {
            return;
        }

        // 설치된 파일의 무결성 검사
        $file_integrity = JJ_File_Integrity_Monitor_V25::instance();
        $result = $file_integrity->check_integrity();

        if ( $result['status'] !== 'clean' ) {
            // 무결성 검사 실패 - 롤백 권장
            $this->log_security_event( 'update_integrity_failed', array(
                'plugin' => $plugin_file,
                'violations' => count( $result['violations'] ),
            ) );

            add_action( 'admin_notices', function() {
                ?>
                <div class="notice notice-error">
                    <p>
                        <strong><?php _e( '⚠️ 업데이트 무결성 검사 실패', 'acf-css-really-simple-style-management-center' ); ?></strong>
                        <?php _e( '업데이트된 파일에서 무결성 위반이 감지되었습니다. 이전 버전으로 롤백하는 것을 권장합니다.', 'acf-css-really-simple-style-management-center' ); ?>
                    </p>
                </div>
                <?php
            } );
        }
    }

    /**
     * [v25.0.0] 무단 롤백 방지
     */
    public function prevent_unauthorized_rollback( $plugins ) {
        // 현재 버전 저장
        $current_version = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '0.0.0';
        $stored_version = get_option( 'jj_plugin_last_verified_version_v25', $current_version );
        
        // 업데이트가 있는 경우에만 검증
        if ( ! empty( $plugins ) && is_array( $plugins ) ) {
            foreach ( $plugins as $plugin_file => $plugin_data ) {
                if ( strpos( $plugin_file, 'acf-css-really-simple-style-guide' ) !== false ) {
                    // 새 버전이 현재 버전보다 낮으면 롤백 시도로 간주
                    if ( isset( $plugin_data->new_version ) ) {
                        if ( version_compare( $plugin_data->new_version, $current_version, '<' ) ) {
                            // 롤백 시도 감지 - 차단
                            unset( $plugins[ $plugin_file ] );
                            
                            $this->log_security_event( 'rollback_attempt_blocked', array(
                                'plugin' => $plugin_file,
                                'current_version' => $current_version,
                                'attempted_version' => $plugin_data->new_version,
                            ) );
                            
                            add_action( 'admin_notices', function() use ( $plugin_data ) {
                                ?>
                                <div class="notice notice-error">
                                    <p>
                                        <strong><?php _e( '⚠️ 무단 롤백 시도 차단', 'acf-css-really-simple-style-management-center' ); ?></strong>
                                        <?php printf( __( '이전 버전(%s)으로의 롤백 시도가 차단되었습니다. 보안상의 이유로 이전 버전으로의 다운그레이드는 허용되지 않습니다.', 'acf-css-really-simple-style-management-center' ), esc_html( $plugin_data->new_version ) ); ?>
                                    </p>
                                </div>
                                <?php
                            } );
                        } else {
                            // 정상 업데이트 - 버전 저장
                            update_option( 'jj_plugin_last_verified_version_v25', $plugin_data->new_version );
                        }
                    }
                }
            }
        }
        
        return $plugins;
    }

    /**
     * [v25.0.0] 보안 이벤트 로그
     */
    private function log_security_event( $event_type, $data = array() ) {
        $logs = get_option( 'jj_update_security_logs_v25', array() );
        
        $logs[] = array(
            'timestamp' => current_time( 'mysql' ),
            'event_type' => $event_type,
            'data' => $data,
            'user_id' => get_current_user_id(),
            'ip_address' => $this->get_client_ip(),
        );

        // 최대 500개까지만 유지
        if ( count( $logs ) > 500 ) {
            $logs = array_slice( $logs, -500 );
        }

        update_option( 'jj_update_security_logs_v25', $logs );
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

<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 20] 보안 강화 모듈
 * 
 * 라이센스 암호화, 업데이트 서버 보안, API 엔드포인트 보안 강화
 * 
 * @since 20.0.0
 */
class JJ_Security_Enhancer {

    private static $instance = null;
    private $encryption_key = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_encryption_key();
        $this->init_hooks();
    }

    /**
     * [v26.1.0] 암호화 키 초기화 (보안 강화)
     *
     * 우선순위:
     * 1. wp-config.php의 JJ_ENCRYPTION_KEY 상수 (권장)
     * 2. WordPress AUTH_KEY + SECURE_AUTH_KEY 조합 (폴백)
     * 3. DB 저장 키는 더 이상 사용하지 않음 (마이그레이션 필요)
     */
    private function init_encryption_key() {
        // 우선순위 1: wp-config.php에 정의된 전용 상수
        if ( defined( 'JJ_ENCRYPTION_KEY' ) && strlen( JJ_ENCRYPTION_KEY ) >= 32 ) {
            $this->encryption_key = JJ_ENCRYPTION_KEY;
            return;
        }

        // 우선순위 2: WordPress 인증 키 조합 (항상 존재함)
        $wp_keys = '';
        if ( defined( 'AUTH_KEY' ) ) {
            $wp_keys .= AUTH_KEY;
        }
        if ( defined( 'SECURE_AUTH_KEY' ) ) {
            $wp_keys .= SECURE_AUTH_KEY;
        }

        if ( ! empty( $wp_keys ) ) {
            // WordPress 키를 SHA-256으로 해시하여 사용
            $this->encryption_key = hash( 'sha256', $wp_keys . 'jj_security_v26' );
            return;
        }

        // 최후의 폴백: 사이트 정보 기반 키 생성 (경고 로그)
        $this->encryption_key = $this->generate_fallback_key();

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'JJ Security Enhancer: wp-config.php에 JJ_ENCRYPTION_KEY를 정의해주세요. 현재 폴백 키를 사용 중입니다.' );
        }
    }

    /**
     * [v26.1.0] 폴백 암호화 키 생성 (권장하지 않음)
     *
     * @return string
     */
    private function generate_fallback_key() {
        // DB에 저장된 기존 키가 있으면 마이그레이션을 위해 사용
        $legacy_key = get_option( 'jj_security_encryption_key', '' );
        if ( ! empty( $legacy_key ) ) {
            return $legacy_key;
        }

        // 사이트 URL과 DB 이름을 조합하여 고유 키 생성
        $site_url = function_exists( 'home_url' ) ? home_url() : '';
        $db_name = defined( 'DB_NAME' ) ? DB_NAME : '';
        $salt = function_exists( 'wp_salt' ) ? wp_salt( 'auth' ) : '';

        return hash( 'sha256', $site_url . $db_name . $salt );
    }

    /**
     * [v26.1.0] 암호화 키 마이그레이션 (DB에서 wp-config로)
     *
     * @return array 마이그레이션 결과
     */
    public function migrate_encryption_key() {
        $result = array(
            'success' => false,
            'message' => '',
            'new_key' => '',
        );

        // 이미 wp-config에 키가 정의되어 있으면 마이그레이션 완료
        if ( defined( 'JJ_ENCRYPTION_KEY' ) && strlen( JJ_ENCRYPTION_KEY ) >= 32 ) {
            // DB에 저장된 레거시 키 삭제
            delete_option( 'jj_security_encryption_key' );
            $result['success'] = true;
            $result['message'] = __( '이미 wp-config.php에 JJ_ENCRYPTION_KEY가 정의되어 있습니다. DB 키를 삭제했습니다.', 'acf-css-really-simple-style-management-center' );
            return $result;
        }

        // 새로운 키 생성
        $new_key = bin2hex( random_bytes( 32 ) );
        $result['new_key'] = $new_key;
        $result['success'] = true;
        $result['message'] = sprintf(
            __( 'wp-config.php에 다음 코드를 추가하세요: %s', 'acf-css-really-simple-style-management-center' ),
            "define( 'JJ_ENCRYPTION_KEY', '{$new_key}' );"
        );

        return $result;
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 업데이트 서버 응답 검증
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'verify_update_response' ), 10, 2 );
        
        // 라이센스 키 암호화 필터
        add_filter( 'jj_license_key_encrypt', array( $this, 'encrypt_license_key' ), 10, 1 );
        add_filter( 'jj_license_key_decrypt', array( $this, 'decrypt_license_key' ), 10, 1 );
    }

    /**
     * [v26.1.0] 라이센스 키 암호화 (AES-256-CBC)
     *
     * 보안 강화: OpenSSL 필수, base64 폴백 제거
     *
     * @param string $license_key 암호화할 라이센스 키
     * @return string 암호화된 키 (빈 문자열 = 실패)
     */
    public function encrypt_license_key( $license_key ) {
        if ( empty( $license_key ) ) {
            return '';
        }

        // [v26.1.0] OpenSSL 필수 - 없으면 암호화 거부
        if ( ! $this->is_openssl_available() ) {
            $this->log_encryption_failure( 'encrypt', 'openssl_not_available' );
            return '';
        }

        $method = 'AES-256-CBC';
        $iv_length = openssl_cipher_iv_length( $method );

        if ( $iv_length === false ) {
            $this->log_encryption_failure( 'encrypt', 'invalid_cipher_method' );
            return '';
        }

        $iv = openssl_random_pseudo_bytes( $iv_length, $strong );

        // 암호학적으로 강력한 랜덤이 아니면 거부
        if ( ! $strong ) {
            $this->log_encryption_failure( 'encrypt', 'weak_random_bytes' );
            return '';
        }

        $encrypted = openssl_encrypt( $license_key, $method, $this->encryption_key, OPENSSL_RAW_DATA, $iv );

        if ( $encrypted === false ) {
            $this->log_encryption_failure( 'encrypt', 'openssl_encrypt_failed', openssl_error_string() );
            return '';
        }

        // 버전 프리픽스 + IV + 암호화된 데이터
        // v2: OPENSSL_RAW_DATA 사용, HMAC 추가
        $hmac = hash_hmac( 'sha256', $iv . $encrypted, $this->encryption_key, true );
        $payload = 'v2:' . base64_encode( $hmac . $iv . $encrypted );

        return $payload;
    }

    /**
     * [v26.1.0] 라이센스 키 복호화 (버전 호환성 지원)
     *
     * @param string $encrypted_key 암호화된 키
     * @return string 복호화된 키 (빈 문자열 = 실패)
     */
    public function decrypt_license_key( $encrypted_key ) {
        if ( empty( $encrypted_key ) ) {
            return '';
        }

        // [v26.1.0] OpenSSL 필수
        if ( ! $this->is_openssl_available() ) {
            $this->log_encryption_failure( 'decrypt', 'openssl_not_available' );
            return '';
        }

        // 버전 확인 (v2: 신규 형식, 없음: 레거시)
        if ( strpos( $encrypted_key, 'v2:' ) === 0 ) {
            return $this->decrypt_v2( substr( $encrypted_key, 3 ) );
        }

        // 레거시 형식 복호화 시도 (마이그레이션 기간 동안만 지원)
        return $this->decrypt_legacy( $encrypted_key );
    }

    /**
     * [v26.1.0] v2 형식 복호화 (HMAC 검증 포함)
     *
     * @param string $payload base64 인코딩된 페이로드
     * @return string 복호화된 키
     */
    private function decrypt_v2( $payload ) {
        $decoded = base64_decode( $payload, true );
        if ( $decoded === false ) {
            $this->log_encryption_failure( 'decrypt_v2', 'invalid_base64' );
            return '';
        }

        $method = 'AES-256-CBC';
        $iv_length = openssl_cipher_iv_length( $method );
        $hmac_length = 32; // SHA-256 = 32 bytes

        $min_length = $hmac_length + $iv_length + 1;
        if ( strlen( $decoded ) < $min_length ) {
            $this->log_encryption_failure( 'decrypt_v2', 'payload_too_short' );
            return '';
        }

        // HMAC, IV, 암호화된 데이터 분리
        $hmac = substr( $decoded, 0, $hmac_length );
        $iv = substr( $decoded, $hmac_length, $iv_length );
        $encrypted = substr( $decoded, $hmac_length + $iv_length );

        // HMAC 검증 (타이밍 공격 방지)
        $expected_hmac = hash_hmac( 'sha256', $iv . $encrypted, $this->encryption_key, true );
        if ( ! hash_equals( $expected_hmac, $hmac ) ) {
            $this->log_encryption_failure( 'decrypt_v2', 'hmac_mismatch' );
            return '';
        }

        $decrypted = openssl_decrypt( $encrypted, $method, $this->encryption_key, OPENSSL_RAW_DATA, $iv );

        if ( $decrypted === false ) {
            $this->log_encryption_failure( 'decrypt_v2', 'openssl_decrypt_failed', openssl_error_string() );
            return '';
        }

        return $decrypted;
    }

    /**
     * [v26.1.0] 레거시 형식 복호화 (하위 호환성)
     *
     * @param string $encrypted_key 레거시 형식 암호화 키
     * @return string 복호화된 키
     */
    private function decrypt_legacy( $encrypted_key ) {
        $decoded = base64_decode( $encrypted_key, true );

        if ( $decoded === false ) {
            return '';
        }

        $method = 'AES-256-CBC';
        $iv_length = openssl_cipher_iv_length( $method );

        if ( strlen( $decoded ) < $iv_length ) {
            return '';
        }

        $iv = substr( $decoded, 0, $iv_length );
        $encrypted = substr( $decoded, $iv_length );

        // 레거시는 base64 인코딩된 상태로 저장되었음
        $decrypted = openssl_decrypt( $encrypted, $method, $this->encryption_key, 0, $iv );

        if ( $decrypted === false ) {
            // 아주 오래된 레거시: 순수 base64 (암호화 없음)
            // 이 경우는 더 이상 지원하지 않음 - 보안 취약
            $this->log_encryption_failure( 'decrypt_legacy', 'unsupported_format' );
            return '';
        }

        return $decrypted;
    }

    /**
     * [v26.1.0] OpenSSL 사용 가능 여부 확인
     *
     * @return bool
     */
    private function is_openssl_available() {
        return function_exists( 'openssl_encrypt' )
            && function_exists( 'openssl_decrypt' )
            && function_exists( 'openssl_cipher_iv_length' )
            && function_exists( 'openssl_random_pseudo_bytes' );
    }

    /**
     * [v26.1.0] 암호화/복호화 실패 로깅
     *
     * @param string $operation 작업 유형
     * @param string $reason    실패 사유
     * @param string $details   상세 정보
     */
    private function log_encryption_failure( $operation, $reason, $details = '' ) {
        $log_entry = array(
            'timestamp' => current_time( 'mysql' ),
            'operation' => sanitize_key( $operation ),
            'reason'    => sanitize_key( $reason ),
            'details'   => sanitize_text_field( $details ),
        );

        if ( class_exists( 'JJ_Security_Hardener' ) && method_exists( 'JJ_Security_Hardener', 'log_security_event' ) ) {
            JJ_Security_Hardener::log_security_event( 'encryption_failure', $log_entry );
        }

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( sprintf(
                'JJ Security Enhancer: %s failed - %s: %s',
                $operation,
                $reason,
                $details
            ) );
        }
    }

    /**
     * 업데이트 서버 응답 검증
     */
    public function verify_update_response( $transient, $plugin_file ) {
        // 우리 플러그인이 아니면 통과
        if ( strpos( $plugin_file, 'acf-css-really-simple-style-guide' ) === false ) {
            return $transient;
        }

        // 응답에 서명이 있는지 확인
        if ( isset( $transient->response[ $plugin_file ] ) ) {
            $update_info = $transient->response[ $plugin_file ];
            
            // 서명 검증 (서버에서 제공하는 경우)
            if ( isset( $update_info->signature ) && isset( $update_info->package ) ) {
                $is_valid = $this->verify_package_signature( $update_info->package, $update_info->signature );
                
                if ( ! $is_valid ) {
                    // 서명 검증 실패 시 업데이트 제거
                    unset( $transient->response[ $plugin_file ] );
                    
                    // 로그 기록
                    if ( class_exists( 'JJ_Security_Hardener' ) ) {
                        JJ_Security_Hardener::log_security_event( 'update_signature_invalid', array(
                            'plugin' => $plugin_file,
                        ) );
                    }
                }
            }
        }

        return $transient;
    }

    /**
     * 패키지 서명 검증
     * [v22.4.0] Phase 37: 보안 강화 - 서명 검증 로직 개선
     */
    public function verify_package_signature( $package_url, $signature ) {
        // 실제 구현 시 서버의 공개 키로 서명 검증
        // 여기서는 기본 검증만 수행
        
        if ( empty( $signature ) ) {
            return false;
        }

        // 서명 형식 검증 (예: base64 인코딩된 서명)
        $decoded_signature = base64_decode( $signature, true );
        if ( $decoded_signature === false ) {
            return false;
        }

        // [v22.4.0] 추가 검증: 서명 길이 확인 (너무 짧으면 위변조 의심)
        if ( strlen( $decoded_signature ) < 32 ) {
            return false;
        }

        // [v22.4.0] URL과 서명의 일관성 확인 (간단한 해시 검증)
        $url_hash = hash( 'sha256', $package_url . $this->encryption_key );
        $expected_prefix = substr( $url_hash, 0, 16 );
        $signature_prefix = substr( bin2hex( $decoded_signature ), 0, 16 );
        
        // 실제 서명 검증은 서버의 공개 키가 필요하므로
        // 여기서는 기본 검증만 수행 (실제 구현 시 OpenSSL 공개 키 검증 추가)
        // 현재는 URL과 서명의 일관성만 확인
        return hash_equals( $expected_prefix, $signature_prefix );
    }

    /**
     * API 요청 서명 생성
     */
    public function sign_request( $data, $secret_key = null ) {
        if ( $secret_key === null ) {
            $secret_key = $this->encryption_key;
        }

        $payload = json_encode( $data, JSON_UNESCAPED_UNICODE );
        $signature = hash_hmac( 'sha256', $payload, $secret_key );
        
        return array(
            'data' => $data,
            'signature' => $signature,
            'timestamp' => time(),
        );
    }

    /**
     * API 요청 서명 검증
     */
    public function verify_request_signature( $signed_data, $secret_key = null ) {
        if ( ! isset( $signed_data['data'], $signed_data['signature'], $signed_data['timestamp'] ) ) {
            return false;
        }

        if ( $secret_key === null ) {
            $secret_key = $this->encryption_key;
        }

        // 타임스탬프 검증 (5분 이내)
        $timestamp = intval( $signed_data['timestamp'] );
        if ( abs( time() - $timestamp ) > 300 ) {
            return false; // 타임스탬프가 너무 오래됨
        }

        // 서명 재계산 및 비교
        $payload = json_encode( $signed_data['data'], JSON_UNESCAPED_UNICODE );
        $expected_signature = hash_hmac( 'sha256', $payload, $secret_key );
        
        return hash_equals( $expected_signature, $signed_data['signature'] );
    }
}

// 초기화
if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    JJ_Security_Enhancer::instance();
}

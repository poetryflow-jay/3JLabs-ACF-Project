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
     * 암호화 키 초기화
     */
    private function init_encryption_key() {
        // 사이트별 고유 키 생성 (DB 옵션에 저장)
        $stored_key = get_option( 'jj_security_encryption_key', '' );
        
        if ( empty( $stored_key ) ) {
            // 새로운 키 생성
            $stored_key = $this->generate_encryption_key();
            update_option( 'jj_security_encryption_key', $stored_key );
        }
        
        $this->encryption_key = $stored_key;
    }

    /**
     * 암호화 키 생성
     */
    private function generate_encryption_key() {
        // 사이트 URL과 DB 이름을 조합하여 고유 키 생성
        $site_url = home_url();
        $db_name = DB_NAME;
        $salt = wp_salt( 'auth' );
        
        return hash( 'sha256', $site_url . $db_name . $salt . time() );
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
     * 라이센스 키 암호화 (AES-256)
     */
    public function encrypt_license_key( $license_key ) {
        if ( empty( $license_key ) ) {
            return '';
        }

        // OpenSSL 사용 가능 여부 확인
        if ( ! function_exists( 'openssl_encrypt' ) ) {
            // OpenSSL이 없으면 간단한 base64 인코딩 (보안 수준 낮음)
            return base64_encode( $license_key );
        }

        $method = 'AES-256-CBC';
        $iv_length = openssl_cipher_iv_length( $method );
        $iv = openssl_random_pseudo_bytes( $iv_length );
        
        $encrypted = openssl_encrypt( $license_key, $method, $this->encryption_key, 0, $iv );
        
        if ( $encrypted === false ) {
            return base64_encode( $license_key ); // 암호화 실패 시 폴백
        }
        
        // IV와 암호화된 데이터를 함께 저장
        return base64_encode( $iv . $encrypted );
    }

    /**
     * 라이센스 키 복호화
     */
    public function decrypt_license_key( $encrypted_key ) {
        if ( empty( $encrypted_key ) ) {
            return '';
        }

        $decoded = base64_decode( $encrypted_key, true );
        
        if ( $decoded === false ) {
            return ''; // 잘못된 형식
        }

        // OpenSSL 사용 가능 여부 확인
        if ( ! function_exists( 'openssl_decrypt' ) ) {
            // OpenSSL이 없으면 base64 디코딩만
            return base64_decode( $encrypted_key );
        }

        $method = 'AES-256-CBC';
        $iv_length = openssl_cipher_iv_length( $method );
        
        if ( strlen( $decoded ) < $iv_length ) {
            return ''; // IV가 없음
        }
        
        $iv = substr( $decoded, 0, $iv_length );
        $encrypted = substr( $decoded, $iv_length );
        
        $decrypted = openssl_decrypt( $encrypted, $method, $this->encryption_key, 0, $iv );
        
        if ( $decrypted === false ) {
            return ''; // 복호화 실패
        }
        
        return $decrypted;
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
     */
    private function verify_package_signature( $package_url, $signature ) {
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

        // 실제 서명 검증은 서버의 공개 키가 필요하므로
        // 여기서는 기본 검증만 수행 (실제 구현 시 OpenSSL 공개 키 검증 추가)
        return true;
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

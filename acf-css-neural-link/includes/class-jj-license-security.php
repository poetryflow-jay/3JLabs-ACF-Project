<?php
/**
 * 보안 강화 클래스
 * 
 * @package JJ_LicenseManagerincludes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Security {
    
    /**
     * Rate limiting을 위한 transients 키
     */
    private static $rate_limit_prefix = 'jj_license_rate_limit_';
    
    /**
     * 최대 요청 수 (시간당)
     */
    private static $max_requests_per_hour = 100;
    
    /**
     * 최대 요청 수 (분당)
     */
    private static $max_requests_per_minute = 20;
    
    /**
     * Rate limiting 확인
     * 
     * @param string $identifier 식별자 (IP 주소 또는 라이센스 키)
     * @param string $period 기간 ('hour' 또는 'minute')
     * @return bool 허용 여부
     */
    public static function check_rate_limit( $identifier, $period = 'hour' ) {
        $max_requests = $period === 'minute' ? self::$max_requests_per_minute : self::$max_requests_per_hour;
        $expiration = $period === 'minute' ? 60 : 3600;
        
        $key = self::$rate_limit_prefix . md5( $identifier . $period );
        $count = get_transient( $key );
        
        if ( $count === false ) {
            set_transient( $key, 1, $expiration );
            return true;
        }
        
        if ( $count >= $max_requests ) {
            return false;
        }
        
        set_transient( $key, $count + 1, $expiration );
        return true;
    }
    
    /**
     * IP 주소 가져오기 (프록시 고려)
     * 
     * @return string IP 주소
     */
    public static function get_client_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_REAL_IP',        // Nginx proxy
            'HTTP_X_FORWARDED_FOR',  // 일반 프록시
            'REMOTE_ADDR',           // 직접 연결
        );
        
        foreach ( $ip_keys as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ip = sanitize_text_field( $_SERVER[ $key ] );
                // X-Forwarded-For는 여러 IP를 포함할 수 있음
                if ( $key === 'HTTP_X_FORWARDED_FOR' ) {
                    $ips = explode( ',', $ip );
                    $ip = trim( $ips[0] );
                }
                // 유효한 IP 주소인지 확인
                if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
                    return $ip;
                }
            }
        }
        
        return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '0.0.0.0';
    }
    
    /**
     * 입력값 검증 및 sanitization
     * 
     * @param mixed $value 입력값
     * @param string $type 타입 ('license_key', 'site_id', 'site_url', 'email', 'int', 'text')
     * @return mixed 검증된 값 또는 false
     */
    public static function validate_input( $value, $type = 'text' ) {
        if ( $value === null || $value === '' ) {
            return false;
        }
        
        switch ( $type ) {
            case 'license_key':
                // 라이센스 키 형식 검증
                if ( ! is_string( $value ) || strlen( $value ) > 100 ) {
                    return false;
                }
                return sanitize_text_field( $value );
                
            case 'site_id':
                // 사이트 ID는 MD5 해시 (32자)
                if ( ! is_string( $value ) || ! preg_match( '/^[a-f0-9]{32}$/i', $value ) ) {
                    return false;
                }
                return sanitize_text_field( $value );
                
            case 'site_url':
                // URL 검증
                $url = esc_url_raw( $value );
                if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
                    return false;
                }
                // 허용된 프로토콜만
                $allowed_protocols = array( 'http', 'https' );
                $parsed = parse_url( $url );
                if ( ! in_array( $parsed['scheme'] ?? '', $allowed_protocols ) ) {
                    return false;
                }
                return $url;
                
            case 'email':
                return is_email( $value ) ? sanitize_email( $value ) : false;
                
            case 'int':
                return filter_var( $value, FILTER_VALIDATE_INT );
                
            case 'text':
            default:
                return sanitize_text_field( $value );
        }
    }
    
    /**
     * API 요청 서명 검증
     * 
     * @param array $params 요청 파라미터
     * @param string $signature 서명
     * @return bool 유효 여부
     */
    public static function verify_signature( $params, $signature ) {
        $secret_key = get_option( 'jj_license_manager_secret_key', '' );
        if ( empty( $secret_key ) ) {
            return false;
        }
        
        // 파라미터 정렬 및 해시 생성
        ksort( $params );
        $data_string = http_build_query( $params );
        $expected_signature = hash_hmac( 'sha256', $data_string, $secret_key );
        
        // 타임스탬프 검증 (5분 이내)
        if ( isset( $params['timestamp'] ) ) {
            $timestamp = intval( $params['timestamp'] );
            if ( abs( time() - $timestamp ) > 300 ) {
                return false;
            }
        }
        
        return hash_equals( $expected_signature, $signature );
    }
    
    /**
     * 보안 로깅
     * 
     * @param string $event 이벤트 타입
     * @param array $data 로그 데이터
     */
    public static function log_security_event( $event, $data = array() ) {
        $log_data = array(
            'timestamp' => current_time( 'mysql' ),
            'event' => sanitize_text_field( $event ),
            'ip' => self::get_client_ip(),
            'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',
            'data' => $data,
        );
        
        // 옵션에 저장 (최근 100개만 유지)
        $logs = get_option( 'jj_license_security_logs', array() );
        $logs[] = $log_data;
        
        // 최근 100개만 유지
        if ( count( $logs ) > 100 ) {
            $logs = array_slice( $logs, -100 );
        }
        
        update_option( 'jj_license_security_logs', $logs );
    }
    
    /**
     * SQL Injection 방지를 위한 추가 검증
     * 
     * @param string $query SQL 쿼리
     * @param array $values 값 배열
     * @return bool 안전 여부
     */
    public static function validate_sql_query( $query, $values = array() ) {
        // 위험한 SQL 키워드 확인
        $dangerous_keywords = array( 'DROP', 'DELETE', 'TRUNCATE', 'ALTER', 'CREATE', 'INSERT', 'UPDATE', 'EXEC', 'EXECUTE' );
        
        foreach ( $dangerous_keywords as $keyword ) {
            if ( stripos( $query, $keyword ) !== false && stripos( $query, 'PREPARE' ) === false ) {
                // SELECT 쿼리에서만 허용
                if ( stripos( $query, 'SELECT' ) === false ) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * CSRF 토큰 생성
     * 
     * @param string $action 액션 이름
     * @return string 토큰
     */
    public static function generate_csrf_token( $action ) {
        return wp_create_nonce( 'jj_license_' . $action );
    }
    
    /**
     * CSRF 토큰 검증
     * 
     * @param string $token 토큰
     * @param string $action 액션 이름
     * @return bool 유효 여부
     */
    public static function verify_csrf_token( $token, $action ) {
        return wp_verify_nonce( $token, 'jj_license_' . $action );
    }
}


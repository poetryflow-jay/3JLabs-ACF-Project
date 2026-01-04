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
     * [v22.3.2] 라이선스 변조 감지 - Mikael의 알고리즘
     * 
     * @param string $license_key 라이선스 키
     * @return bool 유효 여부
     */
    public static function detect_tampering( $license_key ) {
        if ( empty( $license_key ) ) {
            return false;
        }
        
        // 1. 라이선스 키 체크섬 검증
        if ( ! self::validate_license_checksum( $license_key ) ) {
            self::log_security_event( 'tampering_detected', array(
                'license_key' => $license_key,
                'reason' => 'checksum_mismatch'
            ) );
            return false;
        }
        
        // 2. 라이선스 파일 변조 검증
        $plugin_file = JJ_NEURAL_LINK_PATH . 'acf-css-neural-link.php';
        if ( file_exists( $plugin_file ) ) {
            $plugin_data = get_file_data( $plugin_file );
            $expected_version = defined( 'JJ_NEURAL_LINK_VERSION' ) ? JJ_NEURAL_LINK_VERSION : '';
            
            if ( $plugin_data['Version'] !== $expected_version ) {
                self::log_security_event( 'version_mismatch', array(
                    'license_key' => $license_key,
                    'expected_version' => $expected_version,
                    'detected_version' => $plugin_data['Version']
                ) );
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * [v22.3.2] 라이선스 키 체크섬 검증
     * 
     * @param string $license_key 라이선스 키
     * @return bool 유효 여부
     */
    private static function validate_license_checksum( $license_key ) {
        // 라이선스 키 형식: XXXX-XXXX-XXXX-XXXX-XXXX (4그룹)
        if ( ! preg_match( '/^[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}$/', $license_key ) ) {
            return false;
        }
        
        // 체크섬 계산 (마지막 4자리)
        $parts = explode( '-', $license_key );
        $checksum_part = $parts[3];
        
        // 앞 3그룹으로 예상 체크섬 계산
        $expected_checksum = self::calculate_checksum( $parts[0] . '-' . $parts[1] . '-' . $parts[2] );
        
        return hash_equals( $checksum_part, $expected_checksum );
    }
    
    /**
     * [v22.3.2] 라이선스 체크섬 계산
     * 
     * @param string $data 데이터
     * @return string 체크섬 (16자리 hex)
     */
    private static function calculate_checksum( $data ) {
        return substr( hash( 'sha256', $data ), 0, 16 );
    }
    
    /**
     * [v6.3.2] 라이센스 데이터 무결성 검증
     *
     * DB에 저장된 라이센스 데이터와 실제 사용 환경을 비교하여
     * 변조 여부를 감지합니다.
     *
     * @param string $license_key 라이센스 키
     * @param string $site_url 사이트 URL
     * @return array 검증 결과 ['valid' => bool, 'reason' => string]
     */
    public static function verify_license_integrity( $license_key, $site_url = '' ) {
        $result = array(
            'valid'  => true,
            'reason' => '',
        );

        if ( empty( $site_url ) ) {
            $site_url = home_url();
        }

        // 1. DB에서 라이센스 데이터 조회
        global $wpdb;
        $table_name = $wpdb->prefix . 'jj_licenses';

        // 테이블 존재 확인
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
            // 테이블이 없으면 검증 스킵 (초기 설치 상태)
            return $result;
        }

        $license = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE license_key = %s",
            $license_key
        ) );

        if ( ! $license ) {
            $result['valid']  = false;
            $result['reason'] = 'license_not_found';
            self::log_security_event( 'integrity_check_failed', array(
                'license_key' => substr( $license_key, 0, 8 ) . '****',
                'reason'      => 'not_found_in_db',
            ) );
            return $result;
        }

        // 2. 라이센스 상태 확인
        if ( $license->status !== 'active' ) {
            $result['valid']  = false;
            $result['reason'] = 'license_inactive';
            return $result;
        }

        // 3. 만료일 확인
        if ( ! empty( $license->expires_at ) && strtotime( $license->expires_at ) < time() ) {
            $result['valid']  = false;
            $result['reason'] = 'license_expired';
            return $result;
        }

        // 4. 사이트 URL 매칭 확인 (활성화된 사이트 목록과 비교)
        $activations_table = $wpdb->prefix . 'jj_license_activations';
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $activations_table ) ) === $activations_table ) {
            $site_hash = md5( $site_url );
            $activation = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$activations_table}
                 WHERE license_id = %d AND (site_url = %s OR site_id = %s) AND status = 'active'",
                $license->id,
                $site_url,
                $site_hash
            ) );

            if ( ! $activation ) {
                $result['valid']  = false;
                $result['reason'] = 'site_not_activated';
                self::log_security_event( 'integrity_check_failed', array(
                    'license_key' => substr( $license_key, 0, 8 ) . '****',
                    'site_url'    => $site_url,
                    'reason'      => 'unauthorized_site',
                ) );
                return $result;
            }
        }

        // 5. 사용량 검증 (최대 활성화 수 초과 확인)
        if ( isset( $license->max_activations ) && $license->max_activations > 0 ) {
            $active_count = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$activations_table} WHERE license_id = %d AND status = 'active'",
                $license->id
            ) );

            if ( $active_count > $license->max_activations ) {
                $result['valid']  = false;
                $result['reason'] = 'activation_limit_exceeded';
                self::log_security_event( 'tampering_suspected', array(
                    'license_key'     => substr( $license_key, 0, 8 ) . '****',
                    'max_activations' => $license->max_activations,
                    'active_count'    => $active_count,
                ) );
                return $result;
            }
        }

        return $result;
    }

    /**
     * [v6.3.2] 플러그인 파일 무결성 검증
     *
     * 핵심 파일들의 해시를 비교하여 변조 여부를 감지합니다.
     *
     * @param string $plugin_path 플러그인 경로
     * @return array 검증 결과 ['valid' => bool, 'modified_files' => array]
     */
    public static function verify_file_integrity( $plugin_path ) {
        $result = array(
            'valid'          => true,
            'modified_files' => array(),
        );

        // 검사할 핵심 파일 목록
        $core_files = array(
            'acf-css-neural-link.php',
            'includes/class-jj-license-manager-main.php',
            'includes/class-jj-license-validator.php',
            'includes/class-jj-license-security.php',
            'includes/api/class-jj-license-api.php',
        );

        // 저장된 해시 가져오기
        $stored_hashes = get_option( 'jj_neural_link_file_hashes', array() );

        foreach ( $core_files as $file ) {
            $file_path = trailingslashit( $plugin_path ) . $file;

            if ( ! file_exists( $file_path ) ) {
                continue; // 파일이 없으면 스킵 (선택적 파일일 수 있음)
            }

            $current_hash = md5_file( $file_path );

            // 해시가 저장되어 있고 다르면 변조 의심
            if ( isset( $stored_hashes[ $file ] ) && $stored_hashes[ $file ] !== $current_hash ) {
                $result['valid'] = false;
                $result['modified_files'][] = $file;
            }
        }

        if ( ! $result['valid'] ) {
            self::log_security_event( 'file_tampering_detected', array(
                'modified_files' => $result['modified_files'],
            ) );
        }

        return $result;
    }

    /**
     * [v6.3.2] 플러그인 파일 해시 저장 (설치/업데이트 시 호출)
     *
     * @param string $plugin_path 플러그인 경로
     */
    public static function store_file_hashes( $plugin_path ) {
        $core_files = array(
            'acf-css-neural-link.php',
            'includes/class-jj-license-manager-main.php',
            'includes/class-jj-license-validator.php',
            'includes/class-jj-license-security.php',
            'includes/api/class-jj-license-api.php',
        );

        $hashes = array();
        foreach ( $core_files as $file ) {
            $file_path = trailingslashit( $plugin_path ) . $file;
            if ( file_exists( $file_path ) ) {
                $hashes[ $file ] = md5_file( $file_path );
            }
        }

        update_option( 'jj_neural_link_file_hashes', $hashes );
    }

    /**
     * [v6.3.2] 비정상적인 사용 패턴 감지
     *
     * @param string $license_key 라이센스 키
     * @return array 감지 결과 ['suspicious' => bool, 'flags' => array]
     */
    public static function detect_abnormal_usage( $license_key ) {
        $result = array(
            'suspicious' => false,
            'flags'      => array(),
        );

        $ip = self::get_client_ip();
        $logs = get_option( 'jj_license_security_logs', array() );

        // 최근 24시간 로그만 분석
        $recent_logs = array_filter( $logs, function( $log ) {
            return isset( $log['timestamp'] ) && strtotime( $log['timestamp'] ) > ( time() - 86400 );
        } );

        // 1. 동일 IP에서 다수 라이센스 키 사용 시도 감지
        $different_keys_from_ip = array();
        foreach ( $recent_logs as $log ) {
            if ( isset( $log['ip'] ) && $log['ip'] === $ip && isset( $log['data']['license_key'] ) ) {
                $different_keys_from_ip[] = $log['data']['license_key'];
            }
        }
        $different_keys_from_ip = array_unique( $different_keys_from_ip );
        if ( count( $different_keys_from_ip ) > 3 ) {
            $result['suspicious'] = true;
            $result['flags'][]    = 'multiple_keys_same_ip';
        }

        // 2. 동일 라이센스 키로 다수 IP에서 접근 시도 감지
        $different_ips_for_key = array();
        foreach ( $recent_logs as $log ) {
            if ( isset( $log['data']['license_key'] ) &&
                 strpos( $log['data']['license_key'], substr( $license_key, 0, 8 ) ) === 0 &&
                 isset( $log['ip'] ) ) {
                $different_ips_for_key[] = $log['ip'];
            }
        }
        $different_ips_for_key = array_unique( $different_ips_for_key );
        if ( count( $different_ips_for_key ) > 10 ) {
            $result['suspicious'] = true;
            $result['flags'][]    = 'multiple_ips_same_key';
        }

        // 3. 빈번한 실패 시도 감지
        $failure_count = 0;
        foreach ( $recent_logs as $log ) {
            if ( isset( $log['event'] ) &&
                 in_array( $log['event'], array( 'tampering_detected', 'integrity_check_failed', 'auth_failed' ) ) &&
                 isset( $log['ip'] ) && $log['ip'] === $ip ) {
                $failure_count++;
            }
        }
        if ( $failure_count > 5 ) {
            $result['suspicious'] = true;
            $result['flags'][]    = 'frequent_failures';
        }

        if ( $result['suspicious'] ) {
            self::log_security_event( 'abnormal_usage_detected', array(
                'license_key' => substr( $license_key, 0, 8 ) . '****',
                'flags'       => $result['flags'],
            ) );
        }

        return $result;
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


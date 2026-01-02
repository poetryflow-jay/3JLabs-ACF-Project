<?php
/**
 * Security Hardener Class
 * 
 * Phase 8.2: 보안 강화 - 입력 검증, 이스케이프, 권한 관리
 * 
 * @package JJ_Style_Guide
 * @since 8.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Security_Hardener {
    
    private static $instance = null;
    
    /**
     * 레이트 리밋 기본값
     */
    private const RATE_LIMIT_MAX_REQUESTS = 60;
    private const RATE_LIMIT_WINDOW_SEC   = 300; // 5분
    
    /**
     * 보안 이벤트 로그
     */
    private static $security_logs = array();
    
    /**
     * 최대 로그 항목 수
     */
    private const MAX_LOG_ITEMS = 100;
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // 보안 헤더 추가
        add_action( 'send_headers', array( $this, 'add_security_headers' ) );
        
        // AJAX 요청 검증 강화
        add_action( 'admin_init', array( $this, 'init_security_hooks' ) );
    }
    
    /**
     * 보안 헤더 추가
     */
    public function add_security_headers() {
        // 관리자 페이지에서만 적용
        if ( ! is_admin() ) {
            return;
        }
        
        // X-Frame-Options: 클릭재킹 방지
        if ( ! headers_sent() ) {
            header( 'X-Frame-Options: SAMEORIGIN' );
        }
        
        // X-Content-Type-Options: MIME 타입 스니핑 방지
        if ( ! headers_sent() ) {
            header( 'X-Content-Type-Options: nosniff' );
        }
        
        // Content-Security-Policy
        if ( ! headers_sent() ) {
            $csp = apply_filters( 'jj_security_csp', $this->generate_csp_header() );
            if ( ! empty( $csp ) ) {
                header( 'Content-Security-Policy: ' . $csp );
            }
        }
        
        // Referrer-Policy: 리퍼러 정보 제어
        if ( ! headers_sent() ) {
            header( 'Referrer-Policy: strict-origin-when-cross-origin' );
        }
        
        // Permissions-Policy: 기능 제어
        if ( ! headers_sent() ) {
            $permissions = array(
                'geolocation=()',
                'microphone=()',
                'camera=()',
            );
            header( 'Permissions-Policy: ' . implode( ', ', $permissions ) );
        }
    }
    
    /**
     * 보안 훅 초기화
     */
    public function init_security_hooks() {
        // AJAX 요청 전 검증 훅 추가
        add_action( 'wp_ajax_nopriv', array( $this, 'block_public_ajax' ), 1 );
    }
    
    /**
     * 공개 AJAX 요청 차단 (인증되지 않은 사용자)
     */
    public function block_public_ajax() {
        $action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';
        
        // 레이트 리밋 (인증되지 않은 요청)
        if ( ! self::check_rate_limit( $action ?: 'wp_ajax_nopriv' ) ) {
            return;
        }
        
        // jj_ 접두사를 가진 액션은 인증 필수
        if ( 0 === strpos( $action, 'jj_' ) ) {
            $this->log_security_event( 'unauthorized_ajax_attempt', array(
                'action' => $action,
                'ip' => $this->get_client_ip(),
            ) );
            
            wp_send_json_error( array(
                'message' => __( '인증이 필요합니다.', 'acf-css-really-simple-style-management-center' ),
            ) );
            exit;
        }
    }
    
    /**
     * AJAX 요청 검증 (통합)
     * 
     * @param string $action AJAX 액션
     * @param string $nonce_key Nonce 키
     * @param string $capability 필요한 권한
     * @return bool 검증 성공 여부
     */
    public static function verify_ajax_request( $action, $nonce_key, $capability = 'manage_options' ) {
        // 레이트 리밋
        if ( ! self::check_rate_limit( $action ) ) {
            return false;
        }
        
        // Nonce 검증
        if ( ! check_ajax_referer( $nonce_key, 'security', false ) ) {
            self::log_security_event( 'invalid_nonce', array(
                'action' => $action,
                'nonce_key' => $nonce_key,
                'ip' => self::get_client_ip(),
            ) );
            
            wp_send_json_error( array(
                'message' => __( '보안 검증에 실패했습니다. 페이지를 새로고침하고 다시 시도하세요.', 'acf-css-really-simple-style-management-center' ),
            ) );
            return false;
        }
        
        // 권한 검증
        if ( ! current_user_can( $capability ) ) {
            self::log_security_event( 'insufficient_permission', array(
                'action' => $action,
                'capability' => $capability,
                'user_id' => get_current_user_id(),
                'ip' => self::get_client_ip(),
            ) );
            
            wp_send_json_error( array(
                'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ),
            ) );
            return false;
        }
        
        return true;
    }
    
    /**
     * 입력 데이터 검증 및 정리
     * 
     * @param mixed $data 입력 데이터
     * @param string $type 데이터 타입 (text, textarea, email, url, int, float, hex_color, key)
     * @param mixed $default 기본값
     * @return mixed 정리된 데이터
     */
    public static function sanitize_input( $data, $type = 'text', $default = '' ) {
        if ( is_null( $data ) ) {
            return $default;
        }
        
        switch ( $type ) {
            case 'text':
                return sanitize_text_field( $data );
                
            case 'textarea':
                return sanitize_textarea_field( $data );
                
            case 'email':
                return sanitize_email( $data );
                
            case 'url':
                return esc_url_raw( $data );
                
            case 'int':
                return intval( $data );
                
            case 'float':
                return floatval( $data );
                
            case 'hex_color':
                return sanitize_hex_color( $data );
                
            case 'key':
                return sanitize_key( $data );
                
            case 'slug':
                return sanitize_title( $data );
                
            case 'array':
                if ( ! is_array( $data ) ) {
                    return array();
                }
                // 재귀적으로 배열 내부 정리
                return array_map( function( $item ) {
                    if ( is_array( $item ) ) {
                        return self::sanitize_input( $item, 'array' );
                    }
                    return sanitize_text_field( $item );
                }, $data );
                
            default:
                return sanitize_text_field( $data );
        }
    }
    
    /**
     * 출력 데이터 이스케이프
     * 
     * @param mixed $data 출력할 데이터
     * @param string $type 이스케이프 타입 (html, attr, url, js)
     * @return string 이스케이프된 데이터
     */
    public static function escape_output( $data, $type = 'html' ) {
        switch ( $type ) {
            case 'html':
                return esc_html( $data );
                
            case 'attr':
                return esc_attr( $data );
                
            case 'url':
                return esc_url( $data );
                
            case 'js':
                return esc_js( $data );
                
            default:
                return esc_html( $data );
        }
    }
    
    /**
     * 파일 업로드 검증
     * 
     * @param array $file $_FILES 배열 항목
     * @param array $allowed_types 허용된 MIME 타입
     * @param int $max_size 최대 크기 (바이트)
     * @return array|WP_Error 검증된 파일 정보 또는 오류
     */
    public static function validate_upload( $file, $allowed_types = array(), $max_size = 5242880 ) {
        // 파일 존재 확인
        if ( ! isset( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
            return new WP_Error( 'invalid_upload', __( '유효하지 않은 파일 업로드입니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        // 파일 크기 확인
        if ( isset( $file['size'] ) && $file['size'] > $max_size ) {
            return new WP_Error( 'file_too_large', sprintf(
                __( '파일 크기가 너무 큽니다. 최대 크기: %s', 'acf-css-really-simple-style-management-center' ),
                size_format( $max_size )
            ) );
        }
        
        // MIME 타입 확인
        if ( ! empty( $allowed_types ) ) {
            $file_type = wp_check_filetype( $file['name'] );
            $mime_type = $file_type['type'];
            
            if ( empty( $mime_type ) || ! in_array( $mime_type, $allowed_types, true ) ) {
                return new WP_Error( 'invalid_file_type', __( '허용되지 않은 파일 타입입니다.', 'acf-css-really-simple-style-management-center' ) );
            }
            
            // 실제 파일 내용 확인 (MIME 타입 조작 방지)
            $real_mime = mime_content_type( $file['tmp_name'] );
            if ( $real_mime && ! in_array( $real_mime, $allowed_types, true ) ) {
                return new WP_Error( 'invalid_file_type', __( '파일 타입이 일치하지 않습니다.', 'acf-css-really-simple-style-management-center' ) );
            }
        }
        
        // 파일 확장자 확인
        $file_extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        $allowed_extensions = array( 'zip', 'json' ); // 기본 허용 확장자
        
        if ( ! in_array( $file_extension, $allowed_extensions, true ) ) {
            return new WP_Error( 'invalid_extension', __( '허용되지 않은 파일 확장자입니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        return array(
            'name' => sanitize_file_name( $file['name'] ),
            'type' => $mime_type ?? '',
            'tmp_name' => $file['tmp_name'],
            'size' => intval( $file['size'] ),
        );
    }
    
    /**
     * SQL Injection 방어 (Prepared Statement 강제)
     * 
     * @param string $query SQL 쿼리
     * @param array $params 파라미터 배열
     * @return string|WP_Error 준비된 쿼리 또는 오류
     */
    public static function prepare_query( $query, $params = array() ) {
        global $wpdb;
        
        if ( ! $wpdb ) {
            return new WP_Error( 'db_not_available', __( '데이터베이스 연결을 사용할 수 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        // 직접 쿼리 문자열 조작 감지
        $dangerous_patterns = array( '$_', '$_POST', '$_GET', '$_REQUEST', '$', '. "', ". '" );
        foreach ( $dangerous_patterns as $pattern ) {
            if ( strpos( $query, $pattern ) !== false ) {
                self::log_security_event( 'dangerous_query_detected', array(
                    'query_preview' => substr( $query, 0, 100 ),
                ) );
                return new WP_Error( 'dangerous_query', __( '안전하지 않은 쿼리가 감지되었습니다.', 'acf-css-really-simple-style-management-center' ) );
            }
        }
        
        // Prepared Statement 사용
        if ( ! empty( $params ) ) {
            return $wpdb->prepare( $query, $params );
        }
        
        return $query;
    }
    
    /**
     * 보안 이벤트 로깅
     * 
     * @param string $event_type 이벤트 타입
     * @param array $data 추가 데이터
     */
    public static function log_security_event( $event_type, $data = array() ) {
        $log_entry = array(
            'timestamp' => current_time( 'mysql' ),
            'type' => sanitize_key( $event_type ),
            'data' => $data,
            'user_id' => get_current_user_id(),
            'ip' => self::get_client_ip(),
            'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',
        );
        
        // 메모리에 저장 (최대 100개)
        self::$security_logs[] = $log_entry;
        if ( count( self::$security_logs ) > self::MAX_LOG_ITEMS ) {
            array_shift( self::$security_logs );
        }
        
        // WordPress 로그에도 기록
        if ( function_exists( 'error_log' ) && ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
            error_log( sprintf(
                'JJ Security Event: %s - %s',
                $event_type,
                json_encode( $data, JSON_UNESCAPED_UNICODE )
            ) );
        }
        
        // Transient로 저장 (최근 24시간)
        $log_key = 'jj_security_log_' . time();
        set_transient( $log_key, $log_entry, DAY_IN_SECONDS );
    }
    
    /**
     * 보안 로그 가져오기
     * 
     * @param int $limit 최대 항목 수
     * @return array 로그 배열
     */
    public static function get_security_logs( $limit = 50 ) {
        return array_slice( self::$security_logs, -$limit );
    }
    
    /**
     * 클라이언트 IP 주소 가져오기
     * 
     * @return string IP 주소
     */
    private static function get_client_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR',
        );
        
        foreach ( $ip_keys as $key ) {
            if ( isset( $_SERVER[ $key ] ) && ! empty( $_SERVER[ $key ] ) ) {
                $ip = sanitize_text_field( $_SERVER[ $key ] );
                // X-Forwarded-For는 첫 번째 IP만 사용
                if ( strpos( $ip, ',' ) !== false ) {
                    $ip = trim( explode( ',', $ip )[0] );
                }
                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }
    
    /**
     * Content Security Policy 헤더 생성
     * 
     * @return string CSP 헤더 값
     */
    public function generate_csp_header() {
        $directives = apply_filters(
            'jj_security_csp_directives',
            array(
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' data: blob:",
                "style-src 'self' 'unsafe-inline'",
                "img-src 'self' data: blob: https:",
                "font-src 'self' data:",
                "connect-src 'self' https:",
                "media-src 'self' data:",
                "object-src 'none'",
                "frame-ancestors 'self'",
            )
        );
        
        return implode( '; ', array_filter( $directives ) );
    }
    
    /**
     * 레이트 리밋 체크
     *
     * @param string $action 액션 이름
     * @param int|null $max 최대 요청 수
     * @param int|null $window_sec 시간 창 (초)
     * @return bool 제한을 통과하면 true, 차단 시 false
     */
    private static function check_rate_limit( $action, $max = null, $window_sec = null ) {
        $max = $max ? intval( $max ) : self::RATE_LIMIT_MAX_REQUESTS;
        $window_sec = $window_sec ? intval( $window_sec ) : self::RATE_LIMIT_WINDOW_SEC;
        
        $ip  = self::get_client_ip();
        $key = 'jj_rl_' . md5( $action . '|' . $ip . '|' . $max . '|' . $window_sec );
        
        $now  = time();
        $data = get_transient( $key );
        
        if ( empty( $data ) || ! isset( $data['start'] ) || ( $now - intval( $data['start'] ) ) > $window_sec ) {
            $data = array(
                'count' => 1,
                'start' => $now,
            );
            set_transient( $key, $data, $window_sec );
            return true;
        }
        
        $data['count'] = intval( $data['count'] ) + 1;
        set_transient( $key, $data, $window_sec );
        
        if ( $data['count'] > $max ) {
            self::log_security_event( 'rate_limit_exceeded', array(
                'action' => $action,
                'ip'     => $ip,
                'count'  => $data['count'],
            ) );
            
            status_header( 429 );
            wp_send_json_error(
                array(
                    'message' => __( '요청이 너무 많습니다. 잠시 후 다시 시도하세요.', 'acf-css-really-simple-style-management-center' ),
                ),
                429
            );
            return false;
        }
        
        return true;
    }
}

// 초기화
add_action( 'plugins_loaded', array( 'JJ_Security_Hardener', 'instance' ), 10 );

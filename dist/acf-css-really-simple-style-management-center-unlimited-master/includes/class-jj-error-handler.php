<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 에러 핸들링 클래스
 * 
 * 플러그인 실행 중 발생하는 에러를 안전하게 처리하고
 * 디버그 정보를 제공합니다.
 * 
 * @since v3.5.0
 */
final class JJ_Error_Handler {
    
    private static $instance = null;
    private $errors = array();
    private $debug_mode = false;
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->debug_mode = defined( 'WP_DEBUG' ) && WP_DEBUG;
    }
    
    /**
     * 에러 로깅
     * 
     * @param string $message 에러 메시지
     * @param string $context 컨텍스트 (adapter, strategy 등)
     * @param array $data 추가 데이터
     */
    public function log_error( $message, $context = '', $data = array() ) {
        // [v5.1.7] WordPress 함수 안전 호출
        $timestamp = function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' );
        
        $error = array(
            'message' => $message,
            'context' => $context,
            'data' => $data,
            'timestamp' => $timestamp,
        );
        
        $this->errors[] = $error;
        
        if ( $this->debug_mode ) {
            error_log( sprintf(
                '[JJ Style Guide] %s: %s',
                $context ?: 'General',
                $message
            ) );
            
            if ( ! empty( $data ) ) {
                error_log( 'Data: ' . print_r( $data, true ) );
            }
        }
    }
    
    /**
     * 어댑터 로드 에러 처리
     * 
     * @param string $adapter_name 어댑터 이름
     * @param string $error_message 에러 메시지
     */
    public function handle_adapter_error( $adapter_name, $error_message ) {
        $this->log_error(
            sprintf( 'Adapter "%s" failed to load: %s', $adapter_name, $error_message ),
            'Adapter',
            array( 'adapter' => $adapter_name )
        );
    }
    
    /**
     * CSS 생성 에러 처리
     * 
     * @param string $context 컨텍스트
     * @param string $error_message 에러 메시지
     */
    public function handle_css_error( $context, $error_message ) {
        $this->log_error(
            sprintf( 'CSS generation failed in %s: %s', $context, $error_message ),
            'CSS',
            array( 'context' => $context )
        );
    }
    
    /**
     * 에러 목록 가져오기
     * 
     * @return array 에러 배열
     */
    public function get_errors() {
        return $this->errors;
    }
    
    /**
     * 에러 초기화
     */
    public function clear_errors() {
        $this->errors = array();
    }
    
    /**
     * 안전한 함수 실행
     * 
     * @param callable $callback 실행할 함수
     * @param array $args 함수 인자
     * @param mixed $default 실패 시 기본값
     * @return mixed 실행 결과 또는 기본값
     */
    public function safe_execute( $callback, $args = array(), $default = null ) {
        try {
            if ( is_callable( $callback ) ) {
                return call_user_func_array( $callback, $args );
            }
        } catch ( Exception $e ) {
            $this->log_error(
                $e->getMessage(),
                'SafeExecute',
                array( 'callback' => $callback, 'args' => $args )
            );
        }
        
        return $default;
    }
}


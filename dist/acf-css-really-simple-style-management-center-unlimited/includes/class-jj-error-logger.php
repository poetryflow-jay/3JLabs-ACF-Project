<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 에러 로거 클래스 (v5.0.3)
 * 
 * 플러그인 에러를 로깅하고 모니터링합니다.
 * 
 * @since 5.0.3
 * @version 5.0.3
 */
final class JJ_Error_Logger {
    
    private static $instance = null;
    private $log_file = null;
    private $max_log_size = 5 * 1024 * 1024; // 5MB
    private $max_log_files = 5;
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // 로그 파일 경로 설정
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/jj-style-guide-logs';
        
        // 로그 디렉토리 생성
        if ( ! file_exists( $log_dir ) ) {
            wp_mkdir_p( $log_dir );
            // 보안: .htaccess 파일 추가
            file_put_contents( $log_dir . '/.htaccess', 'deny from all' );
        }
        
        $this->log_file = $log_dir . '/error-' . date( 'Y-m-d' ) . '.log';
    }
    
    /**
     * 에러 로깅
     * 
     * @param string $message 에러 메시지
     * @param string $context 컨텍스트 정보
     * @param string $level 로그 레벨 (error, warning, info)
     */
    public function log( $message, $context = '', $level = 'error' ) {
        // 디버그 모드가 아니면 로깅하지 않음
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }
        
        // 로그 파일 크기 확인 및 로테이션
        $this->maybe_rotate_log();
        
        // [v5.1.7] WordPress 함수 안전 호출
        $timestamp = function_exists( 'current_time' ) ? current_time( 'mysql' ) : date( 'Y-m-d H:i:s' );
        $log_entry = sprintf(
            "[%s] [%s] %s %s\n",
            $timestamp,
            strtoupper( $level ),
            $message,
            $context ? 'Context: ' . $context : ''
        );
        
        // 파일에 로그 기록
        @file_put_contents( $this->log_file, $log_entry, FILE_APPEND | LOCK_EX );
    }
    
    /**
     * 에러 로깅 (간편 메서드)
     */
    public function error( $message, $context = '' ) {
        $this->log( $message, $context, 'error' );
    }
    
    /**
     * 경고 로깅
     */
    public function warning( $message, $context = '' ) {
        $this->log( $message, $context, 'warning' );
    }
    
    /**
     * 정보 로깅
     */
    public function info( $message, $context = '' ) {
        $this->log( $message, $context, 'info' );
    }
    
    /**
     * 로그 파일 로테이션
     */
    private function maybe_rotate_log() {
        if ( ! file_exists( $this->log_file ) ) {
            return;
        }
        
        // 파일 크기가 최대 크기를 초과하면 로테이션
        if ( filesize( $this->log_file ) > $this->max_log_size ) {
            $upload_dir = wp_upload_dir();
            $log_dir = $upload_dir['basedir'] . '/jj-style-guide-logs';
            
            // 오래된 로그 파일 삭제
            $log_files = glob( $log_dir . '/error-*.log' );
            if ( count( $log_files ) > $this->max_log_files ) {
                usort( $log_files, function( $a, $b ) {
                    return filemtime( $a ) - filemtime( $b );
                } );
                
                $files_to_delete = array_slice( $log_files, 0, count( $log_files ) - $this->max_log_files );
                foreach ( $files_to_delete as $file ) {
                    @unlink( $file );
                }
            }
            
            // 새 로그 파일 생성
            $this->log_file = $log_dir . '/error-' . date( 'Y-m-d' ) . '.log';
        }
    }
    
    /**
     * 로그 파일 읽기 (관리자용)
     * 
     * @param int $lines 읽을 줄 수
     * @return array 로그 항목 배열
     */
    public function get_logs( $lines = 100 ) {
        if ( ! file_exists( $this->log_file ) ) {
            return array();
        }
        
        $file = file( $this->log_file );
        return array_slice( $file, -$lines );
    }
    
    /**
     * 로그 파일 삭제
     */
    public function clear_logs() {
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/jj-style-guide-logs';
        $log_files = glob( $log_dir . '/error-*.log' );
        
        foreach ( $log_files as $file ) {
            @unlink( $file );
        }
    }
}


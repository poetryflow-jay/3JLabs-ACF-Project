<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.5] Error Tracker
 * 
 * 에러 추적 시스템
 * - 에러 자동 수집
 * - 에러 패턴 분석
 * - 자동 알림 시스템
 * 
 * @since 9.5.0
 */
class JJ_Error_Tracker {

    private static $instance = null;
    private $option_key = 'jj_error_log';
    private $pattern_key = 'jj_error_patterns';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 에러 핸들러 등록
        set_error_handler( array( $this, 'handle_error' ) );
        set_exception_handler( array( $this, 'handle_exception' ) );
        register_shutdown_function( array( $this, 'handle_shutdown' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_get_error_log', array( $this, 'ajax_get_error_log' ) );
        add_action( 'wp_ajax_jj_analyze_error_patterns', array( $this, 'ajax_analyze_patterns' ) );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'jj-admin-center' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'jj-error-tracker',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-error-tracker.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '9.5.0',
            true
        );

        wp_localize_script(
            'jj-error-tracker',
            'jjErrorTracker',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_error_tracker_action' ),
            )
        );
    }

    /**
     * 에러 핸들링
     */
    public function handle_error( $errno, $errstr, $errfile, $errline ) {
        // 플러그인 관련 에러만 추적
        if ( strpos( $errfile, 'acf-css-really-simple-style-management-center' ) === false && 
             strpos( $errfile, 'acf-css' ) === false ) {
            return false; // 기본 핸들러로 전달
        }

        $error = array(
            'type'      => 'error',
            'code'      => $errno,
            'message'   => $errstr,
            'file'      => $errfile,
            'line'      => $errline,
            'timestamp' => current_time( 'mysql' ),
            'severity'  => $this->get_severity( $errno ),
        );

        $this->record_error( $error );
        return false; // 기본 핸들러도 실행
    }

    /**
     * 예외 핸들링
     */
    public function handle_exception( $exception ) {
        $error = array(
            'type'      => 'exception',
            'message'   => $exception->getMessage(),
            'file'      => $exception->getFile(),
            'line'      => $exception->getLine(),
            'trace'     => $exception->getTraceAsString(),
            'timestamp' => current_time( 'mysql' ),
            'severity'  => 'high',
        );

        $this->record_error( $error );
    }

    /**
     * Shutdown 핸들링 (Fatal Error)
     */
    public function handle_shutdown() {
        $error = error_get_last();
        
        if ( $error && in_array( $error['type'], array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR ) ) ) {
            // 플러그인 관련 에러만 추적
            if ( strpos( $error['file'], 'acf-css-really-simple-style-management-center' ) !== false || 
                 strpos( $error['file'], 'acf-css' ) !== false ) {
                $this->record_error( array(
                    'type'      => 'fatal',
                    'message'   => $error['message'],
                    'file'      => $error['file'],
                    'line'      => $error['line'],
                    'timestamp' => current_time( 'mysql' ),
                    'severity'  => 'critical',
                ) );
            }
        }
    }

    /**
     * 에러 기록
     */
    private function record_error( $error ) {
        $errors = get_option( $this->option_key, array() );
        $errors[] = $error;

        // 최대 500개까지만 저장
        if ( count( $errors ) > 500 ) {
            $errors = array_slice( $errors, -500 );
        }

        update_option( $this->option_key, $errors );

        // 심각한 에러는 즉시 알림
        if ( isset( $error['severity'] ) && in_array( $error['severity'], array( 'high', 'critical' ) ) ) {
            $this->send_alert( $error );
        }
    }

    /**
     * 심각도 판단
     */
    private function get_severity( $errno ) {
        switch ( $errno ) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
                return 'critical';
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
                return 'high';
            case E_NOTICE:
            case E_USER_NOTICE:
                return 'low';
            default:
                return 'medium';
        }
    }

    /**
     * 알림 전송
     */
    private function send_alert( $error ) {
        // 관리자에게 알림 (옵션)
        $alerts_enabled = get_option( 'jj_error_alerts_enabled', false );
        
        if ( $alerts_enabled ) {
            // 이메일 또는 다른 알림 방식으로 전송
            // 여기서는 옵션에만 저장
            $alerts = get_option( 'jj_error_alerts', array() );
            $alerts[] = array(
                'error' => $error,
                'sent_at' => current_time( 'mysql' ),
            );
            
            if ( count( $alerts ) > 50 ) {
                $alerts = array_slice( $alerts, -50 );
            }
            
            update_option( 'jj_error_alerts', $alerts );
        }
    }

    /**
     * 에러 패턴 분석
     */
    public function analyze_patterns() {
        $errors = get_option( $this->option_key, array() );
        
        if ( empty( $errors ) ) {
            return array(
                'patterns' => array(),
                'summary' => array(),
            );
        }

        // 에러 타입별 그룹화
        $by_type = array();
        $by_file = array();
        $by_severity = array();

        foreach ( $errors as $error ) {
            $type = $error['type'] ?? 'unknown';
            $file = $error['file'] ?? 'unknown';
            $severity = $error['severity'] ?? 'medium';

            if ( ! isset( $by_type[ $type ] ) ) {
                $by_type[ $type ] = 0;
            }
            $by_type[ $type ]++;

            if ( ! isset( $by_file[ $file ] ) ) {
                $by_file[ $file ] = 0;
            }
            $by_file[ $file ]++;

            if ( ! isset( $by_severity[ $severity ] ) ) {
                $by_severity[ $severity ] = 0;
            }
            $by_severity[ $severity ]++;
        }

        // 가장 빈번한 에러 찾기
        arsort( $by_file );
        $most_frequent = array_slice( $by_file, 0, 5, true );

        return array(
            'patterns' => array(
                'by_type' => $by_type,
                'by_file' => $by_file,
                'by_severity' => $by_severity,
            ),
            'summary' => array(
                'total' => count( $errors ),
                'most_frequent' => $most_frequent,
                'critical_count' => $by_severity['critical'] ?? 0,
                'high_count' => $by_severity['high'] ?? 0,
            ),
        );
    }

    /**
     * AJAX: 에러 로그 가져오기
     */
    public function ajax_get_error_log() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_error_tracker_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $limit = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 50;
        $severity = isset( $_POST['severity'] ) ? sanitize_text_field( $_POST['severity'] ) : null;

        $errors = get_option( $this->option_key, array() );

        // 심각도 필터
        if ( $severity ) {
            $errors = array_filter( $errors, function( $error ) use ( $severity ) {
                return ( $error['severity'] ?? 'medium' ) === $severity;
            } );
        }

        // 최신순 정렬
        usort( $errors, function( $a, $b ) {
            return strtotime( $b['timestamp'] ) - strtotime( $a['timestamp'] );
        } );

        wp_send_json_success( array(
            'errors' => array_slice( $errors, 0, $limit ),
        ) );
    }

    /**
     * AJAX: 에러 패턴 분석
     */
    public function ajax_analyze_patterns() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_error_tracker_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $analysis = $this->analyze_patterns();

        wp_send_json_success( $analysis );
    }
}

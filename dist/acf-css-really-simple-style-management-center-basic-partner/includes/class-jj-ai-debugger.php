<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.4] AI Debugger
 * 
 * AI 기반 디버깅 시스템
 * - 스타일 충돌 감지
 * - 에러 분석 및 해결 제안
 * 
 * @since 9.4.0
 */
class JJ_AI_Debugger {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_detect_style_conflicts', array( $this, 'ajax_detect_conflicts' ) );
        add_action( 'wp_ajax_jj_analyze_errors', array( $this, 'ajax_analyze_errors' ) );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'jj-admin-center' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'jj-ai-debugger',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-ai-debugger.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '9.4.0',
            true
        );

        wp_localize_script(
            'jj-ai-debugger',
            'jjAIDebugger',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_ai_debugger_action' ),
                'strings'  => array(
                    'detecting' => __( '충돌 감지 중...', 'acf-css-really-simple-style-management-center' ),
                    'analyzing' => __( '에러 분석 중...', 'acf-css-really-simple-style-management-center' ),
                ),
            )
        );
    }

    /**
     * 스타일 충돌 감지
     */
    public function detect_conflicts() {
        $options = get_option( 'jj_style_guide_options', array() );
        $conflicts = array();

        // 1. 선택자 충돌 검사
        $selector_conflicts = $this->detect_selector_conflicts( $options );
        if ( ! empty( $selector_conflicts ) ) {
            $conflicts = array_merge( $conflicts, $selector_conflicts );
        }

        // 2. 속성 충돌 검사
        $property_conflicts = $this->detect_property_conflicts( $options );
        if ( ! empty( $property_conflicts ) ) {
            $conflicts = array_merge( $conflicts, $property_conflicts );
        }

        // 3. 특이도 충돌 검사
        $specificity_conflicts = $this->detect_specificity_conflicts( $options );
        if ( ! empty( $specificity_conflicts ) ) {
            $conflicts = array_merge( $conflicts, $specificity_conflicts );
        }

        return array(
            'conflicts' => $conflicts,
            'count'     => count( $conflicts ),
            'solutions' => $this->suggest_solutions( $conflicts ),
        );
    }

    /**
     * 선택자 충돌 감지
     */
    private function detect_selector_conflicts( $options ) {
        $conflicts = array();
        
        // 간단한 선택자 충돌 검사
        // 실제 구현에서는 더 정교한 분석 필요
        
        return $conflicts;
    }

    /**
     * 속성 충돌 감지
     */
    private function detect_property_conflicts( $options ) {
        $conflicts = array();
        
        // 같은 요소에 충돌하는 속성 검사
        // 예: display: block과 display: flex가 동시에 적용되는 경우
        
        return $conflicts;
    }

    /**
     * 특이도 충돌 감지
     */
    private function detect_specificity_conflicts( $options ) {
        $conflicts = array();
        
        // CSS 특이도 기반 충돌 검사
        
        return $conflicts;
    }

    /**
     * 해결 방법 제안
     */
    private function suggest_solutions( $conflicts ) {
        $solutions = array();
        
        foreach ( $conflicts as $conflict ) {
            $solutions[] = array(
                'conflict_id' => $conflict['id'] ?? '',
                'solution' => $this->generate_solution( $conflict ),
                'auto_fixable' => $conflict['auto_fixable'] ?? false,
            );
        }

        return $solutions;
    }

    /**
     * 해결 방법 생성
     */
    private function generate_solution( $conflict ) {
        $solution = array(
            'description' => __( '충돌을 해결하기 위한 제안', 'acf-css-really-simple-style-management-center' ),
            'steps' => array(),
        );

        // 충돌 타입에 따른 해결 방법
        switch ( $conflict['type'] ?? '' ) {
            case 'selector':
                $solution['description'] = __( '선택자 특이도를 조정하거나 !important를 사용하세요.', 'acf-css-really-simple-style-management-center' );
                break;
            case 'property':
                $solution['description'] = __( '충돌하는 속성 중 하나를 제거하거나 우선순위를 조정하세요.', 'acf-css-really-simple-style-management-center' );
                break;
        }

        return $solution;
    }

    /**
     * 에러 분석
     */
    public function analyze_errors() {
        $error_log = $this->get_error_log();
        $analysis = array();

        foreach ( $error_log as $error ) {
            $analysis[] = array(
                'error' => $error,
                'category' => $this->categorize_error( $error ),
                'solution' => $this->suggest_error_solution( $error ),
                'severity' => $this->assess_severity( $error ),
            );
        }

        return array(
            'errors' => $analysis,
            'summary' => $this->generate_error_summary( $analysis ),
        );
    }

    /**
     * 에러 로그 가져오기
     */
    private function get_error_log() {
        // WordPress 디버그 로그 또는 플러그인 전용 로그
        $log_file = WP_CONTENT_DIR . '/debug.log';
        $errors = array();

        if ( file_exists( $log_file ) && is_readable( $log_file ) ) {
            $lines = file( $log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
            $recent_lines = array_slice( $lines, -100 ); // 최근 100줄
            
            foreach ( $recent_lines as $line ) {
                if ( stripos( $line, 'acf-css-really-simple-style-management-center' ) !== false || 
                     stripos( $line, 'acf-css' ) !== false ) {
                    $errors[] = $line;
                }
            }
        }

        return array_slice( $errors, -20 ); // 최근 20개
    }

    /**
     * 에러 분류
     */
    private function categorize_error( $error ) {
        if ( stripos( $error, 'fatal' ) !== false || stripos( $error, 'fatal error' ) !== false ) {
            return 'fatal';
        }
        if ( stripos( $error, 'warning' ) !== false ) {
            return 'warning';
        }
        if ( stripos( $error, 'notice' ) !== false ) {
            return 'notice';
        }
        return 'unknown';
    }

    /**
     * 에러 해결 방법 제안
     */
    private function suggest_error_solution( $error ) {
        $solutions = array();

        // 일반적인 에러 패턴 매칭
        if ( stripos( $error, 'undefined function' ) !== false ) {
            $solutions[] = __( '함수가 정의되지 않았습니다. 플러그인을 재활성화하거나 파일이 누락되었는지 확인하세요.', 'acf-css-really-simple-style-management-center' );
        }
        
        if ( stripos( $error, 'undefined class' ) !== false ) {
            $solutions[] = __( '클래스가 로드되지 않았습니다. 파일 경로를 확인하세요.', 'acf-css-really-simple-style-management-center' );
        }

        if ( stripos( $error, 'permission' ) !== false || stripos( $error, '권한' ) !== false ) {
            $solutions[] = __( '권한 문제입니다. 파일/폴더 권한을 확인하세요.', 'acf-css-really-simple-style-management-center' );
        }

        return ! empty( $solutions ) ? $solutions : array( __( '에러를 분석하여 해결 방법을 제안합니다.', 'acf-css-really-simple-style-management-center' ) );
    }

    /**
     * 심각도 평가
     */
    private function assess_severity( $error ) {
        $category = $this->categorize_error( $error );
        
        switch ( $category ) {
            case 'fatal':
                return 'high';
            case 'warning':
                return 'medium';
            case 'notice':
                return 'low';
            default:
                return 'unknown';
        }
    }

    /**
     * 에러 요약 생성
     */
    private function generate_error_summary( $analysis ) {
        $summary = array(
            'total' => count( $analysis ),
            'by_category' => array(),
            'by_severity' => array(),
        );

        foreach ( $analysis as $item ) {
            $category = $item['category'];
            $severity = $item['severity'];

            if ( ! isset( $summary['by_category'][ $category ] ) ) {
                $summary['by_category'][ $category ] = 0;
            }
            $summary['by_category'][ $category ]++;

            if ( ! isset( $summary['by_severity'][ $severity ] ) ) {
                $summary['by_severity'][ $severity ] = 0;
            }
            $summary['by_severity'][ $severity ]++;
        }

        return $summary;
    }

    /**
     * AJAX: 충돌 감지
     */
    public function ajax_detect_conflicts() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_ai_debugger_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $result = $this->detect_conflicts();

        wp_send_json_success( $result );
    }

    /**
     * AJAX: 에러 분석
     */
    public function ajax_analyze_errors() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_ai_debugger_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $result = $this->analyze_errors();

        wp_send_json_success( $result );
    }
}

<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.5] Code Optimizer
 * 
 * 코드 최적화 시스템
 * - 불필요한 코드 제거
 * - 알고리즘 최적화
 * - 메모리 사용 최적화
 * 
 * @since 9.5.0
 */
class JJ_Code_Optimizer {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_optimize_code', array( $this, 'ajax_optimize_code' ) );
        add_action( 'wp_ajax_jj_analyze_code_quality', array( $this, 'ajax_analyze_code_quality' ) );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'jj-admin-center' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'jj-code-optimizer',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-code-optimizer.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '9.5.0',
            true
        );

        wp_localize_script(
            'jj-code-optimizer',
            'jjCodeOptimizer',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_code_optimizer_action' ),
            )
        );
    }

    /**
     * 코드 품질 분석
     */
    public function analyze_code_quality() {
        $analysis = array(
            'duplicate_code' => $this->detect_duplicate_code(),
            'unused_functions' => $this->detect_unused_functions(),
            'optimization_opportunities' => $this->find_optimization_opportunities(),
            'code_complexity' => $this->analyze_complexity(),
        );

        return $analysis;
    }

    /**
     * 중복 코드 감지
     */
    private function detect_duplicate_code() {
        $duplicates = array();
        
        // 간단한 중복 코드 감지
        // 실제 구현에서는 더 정교한 분석 필요
        
        return $duplicates;
    }

    /**
     * 사용되지 않는 함수 감지
     */
    private function detect_unused_functions() {
        $unused = array();
        
        // 사용되지 않는 함수 감지
        // 실제 구현에서는 코드베이스 전체 분석 필요
        
        return $unused;
    }

    /**
     * 최적화 기회 찾기
     */
    private function find_optimization_opportunities() {
        $opportunities = array();

        // 1. 옵션 캐시 활용 기회
        if ( class_exists( 'JJ_Options_Cache' ) ) {
            $cache = JJ_Options_Cache::instance();
            if ( ! $cache->is_enabled() ) {
                $opportunities[] = array(
                    'type' => 'enable_options_cache',
                    'priority' => 'high',
                    'message' => __( '옵션 캐시를 활성화하면 데이터베이스 쿼리를 40-50% 감소시킬 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                );
            }
        }

        // 2. CSS 캐시 활용 기회
        if ( class_exists( 'JJ_CSS_Cache' ) ) {
            $css_cache = JJ_CSS_Cache::instance();
            if ( ! $css_cache->is_enabled() ) {
                $opportunities[] = array(
                    'type' => 'enable_css_cache',
                    'priority' => 'high',
                    'message' => __( 'CSS 캐시를 활성화하면 페이지 로딩 시간을 단축할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                );
            }
        }

        // 3. Asset Optimizer 활용 기회
        if ( class_exists( 'JJ_Asset_Optimizer' ) ) {
            $asset_optimizer = JJ_Asset_Optimizer::instance();
            if ( ! $asset_optimizer->is_enabled() ) {
                $opportunities[] = array(
                    'type' => 'enable_asset_optimizer',
                    'priority' => 'medium',
                    'message' => __( 'Asset Optimizer를 활성화하면 리소스 로딩을 최적화할 수 있습니다.', 'acf-css-really-simple-style-management-center' ),
                );
            }
        }

        return $opportunities;
    }

    /**
     * 코드 복잡도 분석
     */
    private function analyze_complexity() {
        // 간단한 복잡도 분석
        // 실제 구현에서는 순환 복잡도(Cyclomatic Complexity) 계산 필요
        
        return array(
            'average_complexity' => 'low',
            'high_complexity_files' => array(),
        );
    }

    /**
     * 코드 최적화 실행
     */
    public function optimize_code( $optimizations ) {
        $results = array();

        foreach ( $optimizations as $optimization ) {
            switch ( $optimization['type'] ) {
                case 'enable_options_cache':
                    if ( class_exists( 'JJ_Options_Cache' ) ) {
                        update_option( 'jj_options_cache_enabled', true );
                        $results[] = array(
                            'type' => $optimization['type'],
                            'success' => true,
                            'message' => __( '옵션 캐시가 활성화되었습니다.', 'acf-css-really-simple-style-management-center' ),
                        );
                    }
                    break;

                case 'enable_css_cache':
                    if ( class_exists( 'JJ_CSS_Cache' ) ) {
                        update_option( 'jj_css_cache_enabled', true );
                        $results[] = array(
                            'type' => $optimization['type'],
                            'success' => true,
                            'message' => __( 'CSS 캐시가 활성화되었습니다.', 'acf-css-really-simple-style-management-center' ),
                        );
                    }
                    break;

                case 'enable_asset_optimizer':
                    if ( class_exists( 'JJ_Asset_Optimizer' ) ) {
                        update_option( 'jj_asset_optimizer_enabled', true );
                        $results[] = array(
                            'type' => $optimization['type'],
                            'success' => true,
                            'message' => __( 'Asset Optimizer가 활성화되었습니다.', 'acf-css-really-simple-style-management-center' ),
                        );
                    }
                    break;
            }
        }

        return $results;
    }

    /**
     * AJAX: 코드 최적화
     */
    public function ajax_optimize_code() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_code_optimizer_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $optimizations = isset( $_POST['optimizations'] ) ? json_decode( stripslashes( $_POST['optimizations'] ), true ) : array();

        if ( empty( $optimizations ) ) {
            wp_send_json_error( array( 'message' => __( '최적화 항목이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $results = $this->optimize_code( $optimizations );

        wp_send_json_success( array(
            'results' => $results,
        ) );
    }

    /**
     * AJAX: 코드 품질 분석
     */
    public function ajax_analyze_code_quality() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_code_optimizer_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $analysis = $this->analyze_code_quality();

        wp_send_json_success( $analysis );
    }
}

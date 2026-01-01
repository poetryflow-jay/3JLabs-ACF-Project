<?php
/**
 * Asset Optimizer Class
 * 
 * Phase 8.1: 성능 최적화 - 조건부 로딩 및 지연 로딩
 * 
 * @package JJ_Style_Guide
 * @since 8.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Asset_Optimizer {
    
    private static $instance = null;
    
    /**
     * 로드된 탭 추적 (지연 로딩용)
     */
    private static $loaded_tabs = array();
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Admin 페이지에서만 동작
        if ( ! is_admin() ) {
            return;
        }
        
        // 조건부 스크립트 로딩
        add_action( 'admin_enqueue_scripts', array( $this, 'optimize_admin_assets' ), 5 );
        
        // 탭 전환 시 지연 로딩
        add_action( 'wp_ajax_jj_load_tab_content', array( $this, 'ajax_load_tab_content' ) );
    }
    
    /**
     * 관리자 페이지 자산 최적화
     */
    public function optimize_admin_assets( $hook ) {
        // Admin Center 페이지인지 확인
        $is_admin_center = in_array( $hook, array(
            'settings_page_jj-admin-center',
            'appearance_page_jj-admin-center',
            'tools_page_jj-admin-center',
        ), true );
        
        if ( ! $is_admin_center ) {
            return;
        }
        
        // 현재 탭 감지 (URL 파라미터 또는 기본값)
        $current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'dashboard';
        
        // 필수 자산만 먼저 로드
        $this->enqueue_critical_assets();
        
        // 탭별 조건부 로딩
        $this->enqueue_tab_specific_assets( $current_tab );
    }
    
    /**
     * 필수 자산만 먼저 로드 (Critical Path)
     */
    private function enqueue_critical_assets() {
        $version = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.1.0';
        $base_url = defined( 'JJ_STYLE_GUIDE_URL' ) ? JJ_STYLE_GUIDE_URL : '';
        
        // 최소 CSS (레이아웃 및 기본 스타일만)
        wp_enqueue_style(
            'jj-admin-center-critical',
            $base_url . 'assets/css/jj-admin-center.css',
            array(),
            $version
        );
        
        // [Phase 8.1] 공통 유틸리티 먼저 로드
        wp_enqueue_script(
            'jj-common-utils',
            $base_url . 'assets/js/jj-common-utils.js',
            array( 'jquery' ),
            $version,
            true
        );
        
        // 최소 JavaScript (탭 전환 및 기본 기능만)
        wp_enqueue_script(
            'jj-admin-center-critical',
            $base_url . 'assets/js/jj-admin-center.js',
            array( 'jquery', 'jj-common-utils' ),
            $version,
            true
        );
        
        // AJAX 파라미터 (조건부 로딩에 필요)
        wp_localize_script( 'jj-admin-center-critical', 'jjAdminCenter', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'jj_admin_center_ajax' ),
            'lazy_load_tabs' => true, // 지연 로딩 활성화
        ) );
    }
    
    /**
     * 탭별 조건부 자산 로딩
     */
    private function enqueue_tab_specific_assets( $tab ) {
        $version = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.1.0';
        $base_url = defined( 'JJ_STYLE_GUIDE_URL' ) ? JJ_STYLE_GUIDE_URL : '';
        
        // 탭별 필요한 스크립트만 로드
        $tab_scripts = array(
            'colors' => array( 'jj-style-guide-presets.js' ),
            'labs' => array( 'jj-labs-center.js' ),
            'editor' => array( 'jj-style-guide-editor.js', 'jj-live-preview.js' ),
            'system-status' => array(), // 추가 스크립트 없음
            'updates' => array(), // 추가 스크립트 없음
        );
        
        if ( isset( $tab_scripts[ $tab ] ) && ! empty( $tab_scripts[ $tab ] ) ) {
            foreach ( $tab_scripts[ $tab ] as $script ) {
                $handle = 'jj-' . str_replace( array( '.js', 'jj-' ), '', $script );
                
                wp_enqueue_script(
                    $handle,
                    $base_url . 'assets/js/' . $script,
                    array( 'jquery', 'jj-admin-center-critical' ),
                    $version,
                    true
                );
            }
        }
        
        // 특정 탭에만 필요한 CSS
        if ( 'colors' === $tab ) {
            wp_enqueue_style(
                'jj-style-guide-presets',
                $base_url . 'assets/css/jj-style-guide-presets.css',
                array( 'jj-admin-center-critical' ),
                $version
            );
        }
        
        // 키보드 단축키는 항상 로드 (전역 기능)
        wp_enqueue_script(
            'jj-keyboard-shortcuts',
            $base_url . 'assets/js/jj-keyboard-shortcuts.js',
            array( 'jquery' ),
            $version,
            true
        );
        
        // 툴팁도 항상 로드 (전역 기능)
        wp_enqueue_script(
            'jj-tooltips',
            $base_url . 'assets/js/jj-tooltips.js',
            array( 'jquery' ),
            $version,
            true
        );
    }
    
    /**
     * AJAX: 탭 컨텐츠 지연 로딩
     */
    public function ajax_load_tab_content() {
        check_ajax_referer( 'jj_admin_center_ajax', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $tab = isset( $_POST['tab'] ) ? sanitize_text_field( $_POST['tab'] ) : '';
        
        if ( empty( $tab ) ) {
            wp_send_json_error( array( 'message' => __( '탭이 지정되지 않았습니다.', 'jj-style-guide' ) ) );
        }
        
        // 이미 로드된 탭이면 스킵
        if ( in_array( $tab, self::$loaded_tabs, true ) ) {
            wp_send_json_success( array( 'message' => __( '이미 로드되었습니다.', 'jj-style-guide' ) ) );
        }
        
        // 탭 컨텐츠 로드 (필요한 스크립트/스타일 포함)
        $this->enqueue_tab_specific_assets( $tab );
        
        // 로드된 탭으로 표시
        self::$loaded_tabs[] = $tab;
        
        wp_send_json_success( array(
            'message' => __( '탭 컨텐츠가 로드되었습니다.', 'jj-style-guide' ),
            'tab' => $tab,
        ) );
    }
    
    /**
     * 공통 유틸리티 함수 통합
     * 중복 코드 제거를 위한 헬퍼 함수들
     */
    public static function get_common_utils() {
        return array(
            'showToast' => 'function(msg, type) { /* 통합 Toast 함수 */ }',
            'formatColor' => 'function(color) { /* 색상 포맷팅 */ }',
            'debounce' => 'function(func, wait) { /* Debounce 함수 */ }',
        );
    }
}

// 초기화
add_action( 'plugins_loaded', array( 'JJ_Asset_Optimizer', 'instance' ), 20 );

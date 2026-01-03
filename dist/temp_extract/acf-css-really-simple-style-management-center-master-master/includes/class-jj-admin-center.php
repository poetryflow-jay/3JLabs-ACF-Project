<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ACF CSS 관리자 센터
 * - 스타일 센터 UI에 사용되는 텍스트/레이블 일부를 옵션으로 제어하기 위한 컨트롤 패널
 * - 상위/마스터 버전에서만 노출되도록 확장 가능하도록 설계
 * 
 * @version 22.0.1
 * - [v22.0.1] UI/UX 긴급 패치 및 Fatal Error 해결
 * - [v21.0.0] 메이저 버전 업데이트 및 넛지 프리셋 통합
 * - [v20.2.4] Nudge Flow 템플릿 마켓 전략 프리셋 반영
 * - [v20.2.3] Style Guide 페이지 등록 추가 (권한 오류 수정)
 * - [v20.2.1] 번역 로딩 타이밍 수정 (WordPress 6.7.0+ 호환)
 * - [v13.4.7] Admin Center 빈 화면 방지: 탭 로딩 예외 처리 추가
 * - 탭 기반 인터페이스 추가 (General, Admin Menu, Section Layout, Texts, Colors)
 * - 2패널 레이아웃 구현 (Admin Menu 탭: 왼쪽 메뉴 목록, 오른쪽 상세 설정)
 * - 사이드바 액션 버튼 추가 (Save, Reset, Export, Import)
 * - 색상 미리보기 기능 추가
 * - 전용 CSS/JS 파일 분리 (jj-admin-center.css, jj-admin-center.js)
 */
final class JJ_Admin_Center {

    private static $instance = null;
    private $option_key           = 'jj_style_guide_admin_texts';
    private $sections_option_key  = 'jj_style_guide_section_layout';
    private $menu_option_key      = 'jj_style_guide_admin_menu_layout';
    private $colors_option_key    = 'jj_style_guide_admin_menu_colors';
    private $config               = array();
    
    // [v5.0.0] 성능 최적화: 섹션 레이아웃 캐싱
    private static $sections_layout_cache = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/ui-text-config.php';
            if ( file_exists( $config_file ) ) {
                $this->config = include $config_file;
            }
        }
    }

    /**
     * 초기화: 메뉴 등록 훅 연결
     */
    public function init() {
        // [v20.2.0] admin_menu 훅 우선순위를 1로 설정하여 먼저 등록
        add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ), 1 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_center_assets' ) );
        // [Phase 4.5] 상단바(Admin Bar) 어디서나 접근 가능한 진입점 추가
        add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 100 );
        
        // [v20.2.0] 스타일 센터 메뉴 강조 스타일 (앰버/오렌지 계열)
        add_action( 'admin_head', array( $this, 'output_style_center_menu_highlight' ) );
        
        // [v20.2.0] 메뉴 순서 강제 지정 (알림판 > 벌크 매니저 > 스타일 센터)
        add_filter( 'menu_order', array( $this, 'force_style_center_menu_order' ), 1000 );

        // 관리자 메뉴 커스터마이징 적용
        add_action( 'admin_menu', array( $this, 'apply_admin_menu_customizations' ), 999 );
        add_filter( 'custom_menu_order', '__return_true' );
        add_filter( 'menu_order', array( $this, 'filter_menu_order' ) );
        add_action( 'admin_head', array( $this, 'output_admin_menu_styles' ) );
        // [v13.4.5] JS 로드 실패 시 폴백 스타일 출력
        add_action( 'admin_head', array( $this, 'output_admin_center_fallback_styles' ) );

        // AJAX 핸들러: 팔레트 데이터 가져오기
        add_action( 'wp_ajax_jj_get_palette_data', array( $this, 'ajax_get_palette_data' ) );
        
        // [v4.0.1 신규] Admin Center 설정 저장 AJAX 핸들러
        add_action( 'wp_ajax_jj_admin_center_save', array( $this, 'ajax_save_admin_center_settings' ) );
        // [Phase 10.5] Colors 탭: 관리자 메뉴/상단바 색상 기본값 리셋(AJAX)
        add_action( 'wp_ajax_jj_admin_center_reset_colors', array( $this, 'ajax_reset_admin_colors' ) );
        
        // [v3.8.0 신규] 라이센스 관리 AJAX 핸들러
        add_action( 'wp_ajax_jj_save_license_key', array( $this, 'ajax_save_license_key' ) );
        add_action( 'wp_ajax_jj_verify_license_key', array( $this, 'ajax_verify_license_key' ) );
        
        // [v5.1.7 신규] 업데이트 설정 AJAX 핸들러
        add_action( 'wp_ajax_jj_save_update_settings', array( $this, 'ajax_save_update_settings' ) );
        add_action( 'wp_ajax_jj_check_updates_now', array( $this, 'ajax_check_updates_now' ) );
        // [v8.x] Updates 탭: 플러그인별 자동 업데이트 토글 (WP 코어 옵션과 직접 동기화)
        add_action( 'wp_ajax_jj_toggle_auto_update_plugin', array( $this, 'ajax_toggle_auto_update_plugin' ) );
        // [v8.x] Updates 탭: Suite 전체 업데이트 체크(트랜지언트 갱신)
        add_action( 'wp_ajax_jj_suite_refresh_updates', array( $this, 'ajax_suite_refresh_updates' ) );
        // [Phase 6] 자가 진단 AJAX 핸들러
        add_action( 'wp_ajax_jj_run_self_test', array( $this, 'ajax_run_self_test' ) );
        // [Phase 8.5.2] 진단 알림 해제 AJAX 핸들러
        add_action( 'wp_ajax_jj_dismiss_diagnostic_notice', array( $this, 'ajax_dismiss_diagnostic_notice' ) );

        // [v8.0.0] Bulk Installer AJAX 핸들러 (Tools 탭용)
        add_action( 'wp_ajax_jj_bulk_install_upload', array( $this, 'ajax_handle_bulk_upload' ) );
        add_action( 'wp_ajax_jj_bulk_install_process', array( $this, 'ajax_handle_bulk_install' ) );
        add_action( 'wp_ajax_jj_bulk_activate_plugin', array( $this, 'ajax_handle_bulk_activate' ) );

        // [v8.0.0] 자동 업데이트 필터 (강제 적용)
        add_filter( 'auto_update_plugin', array( $this, 'filter_auto_update_plugin' ), 10, 2 );
    }

    /**
     * [v8.0.0] 자동 업데이트 필터 핸들러
     * 워드프레스 코어가 자동 업데이트 여부를 결정할 때 개입합니다.
     */
    public function filter_auto_update_plugin( $update, $item ) {
        // 이 플러그인인지 확인 (슬러그 또는 파일명 매칭)
        $plugin_file = plugin_basename( JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php' );
        
        // $item이 객체인 경우와 배열인 경우 모두 처리
        $item_slug = '';
        if ( is_object( $item ) ) {
            $item_slug = isset( $item->plugin ) ? $item->plugin : ( isset( $item->slug ) ? $item->slug : '' );
        } elseif ( is_array( $item ) ) {
            $item_slug = isset( $item['plugin'] ) ? $item['plugin'] : ( isset( $item['slug'] ) ? $item['slug'] : '' );
        } else {
            // $item이 문자열(파일 경로)로 넘어오는 경우도 있음
            $item_slug = $item;
        }

        // 경로 매칭 시도 (정확한 매칭 또는 끝부분 매칭)
        if ( $item_slug === $plugin_file || strpos( $item_slug, 'acf-css-really-simple-style-guide.php' ) !== false ) {
            // WordPress 코어 자동 업데이트 설정(플러그인 목록 UI)의 상태를 최우선으로 신뢰합니다.
            // - 플러그인 목록에서 토글한 값이 곧바로 반영되어야 UX가 일관됩니다.
            $core_auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
            $core_enabled = in_array( $plugin_file, $core_auto_updates, true );

            // 플러그인 내부 설정 옵션과 동기화(불일치 시에만)
            // [Phase 8.1] Options Cache 활용
            $update_settings = class_exists( 'JJ_Options_Cache' ) 
                ? JJ_Options_Cache::get_option( 'jj_style_guide_update_settings', array() )
                : get_option( 'jj_style_guide_update_settings', array() );
            if ( isset( $update_settings['auto_update_enabled'] ) && (bool) $update_settings['auto_update_enabled'] !== $core_enabled ) {
                $update_settings['auto_update_enabled'] = $core_enabled;
                update_option( 'jj_style_guide_update_settings', $update_settings );
            }

            return $core_enabled;
        }
        
        return $update;
    }

    /**
     * Admin Center 전용 CSS/JS enqueue
     */
    public function enqueue_admin_center_assets( $hook ) {
        // Admin Center 페이지에서만 로드
        $allowed_hooks = array(
            'toplevel_page_jj-admin-center',
            'settings_page_jj-admin-center',
            'appearance_page_jj-admin-center',
            'tools_page_jj-admin-center',
        );
        if ( ! in_array( $hook, $allowed_hooks, true ) ) {
            return;
        }

        $css_url = JJ_STYLE_GUIDE_URL . 'assets/css/jj-admin-center.css';
        $js_url  = JJ_STYLE_GUIDE_URL . 'assets/js/jj-admin-center.js';

        // 캐시 이슈 방지: 파일 변경 시 자동으로 버전이 바뀌도록 filemtime 사용 (가능할 때만)
        $fallback_ver = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0';
        $css_ver = $fallback_ver;
        $js_ver  = $fallback_ver;

        $css_path = JJ_STYLE_GUIDE_PATH . 'assets/css/jj-admin-center.css';
        if ( file_exists( $css_path ) ) {
            $css_ver .= '.' . filemtime( $css_path );
        }

        $js_path = JJ_STYLE_GUIDE_PATH . 'assets/js/jj-admin-center.js';
        if ( file_exists( $js_path ) ) {
            $js_ver .= '.' . filemtime( $js_path );
        }

        wp_enqueue_style( 'jj-admin-center', $css_url, array(), $css_ver );
        
        // jQuery UI Sortable (메뉴 순서 변경용)
        wp_enqueue_script( 'jquery-ui-sortable' );
        
        // [Phase 10.5] 색상 미리보기를 위한 wp-color-picker
        wp_enqueue_style( 'wp-color-picker' );
        
        // [v5.0.3] CodeMirror (CSS 편집기)
        wp_enqueue_code_editor( array( 'type' => 'text/css' ) );

        wp_enqueue_script( 'jj-admin-center', $js_url, array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker', 'wp-theme-plugin-editor' ), $js_ver, true );
        
        // [v13.4.5] JS 로드 완료 시 body에 클래스 추가하는 인라인 스크립트
        wp_add_inline_script(
            'jj-admin-center',
            'jQuery(document).ready(function($){ $("body").addClass("js-loaded"); });',
            'after'
        );

        // AJAX URL 및 Nonce 전달
        wp_localize_script( 'jj-admin-center', 'jjAdminCenter', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'jj_admin_center_save_action' ),
            'strings'  => array(
                'save_success' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ),
                'save_fail'    => __( '설정 저장에 실패했습니다.', 'acf-css-really-simple-style-management-center' ),
                'reset_confirm'=> __( '정말 기본값으로 되돌리시겠습니까? 이 작업은 취소할 수 없습니다.', 'acf-css-really-simple-style-management-center' ),
                'export_fail'  => __( '설정 내보내기에 실패했습니다.', 'acf-css-really-simple-style-management-center' ),
                'import_confirm' => __( '설정을 불러오면 현재 설정이 덮어씌워집니다. 계속하시겠습니까?', 'acf-css-really-simple-style-management-center' ),
                'import_success' => __( '설정을 성공적으로 불러왔습니다. 페이지를 새로고침합니다.', 'acf-css-really-simple-style-management-center' ),
                'import_fail'    => __( '설정 불러오기에 실패했습니다. 파일 형식을 확인해주세요.', 'acf-css-really-simple-style-management-center' ),
                // [v5.0.3] 색상 복원
                'restore_color_confirm' => __( '이전 색상으로 복원하시겠습니까?', 'acf-css-really-simple-style-management-center' ),
                // [v5.1.7] 업데이트 확인
                'checking_updates' => __( '업데이트 확인 중...', 'acf-css-really-simple-style-management-center' ),
                'updates_checked' => __( '업데이트 확인 완료.', 'acf-css-really-simple-style-management-center' ),
                // [v8.0.0] Bulk Installer
                'uploading' => __( '업로드 중...', 'acf-css-really-simple-style-management-center' ),
                'installing' => __( '설치 중...', 'acf-css-really-simple-style-management-center' ),
                'activating' => __( '활성화 중...', 'acf-css-really-simple-style-management-center' ),
                'done' => __( '완료', 'acf-css-really-simple-style-management-center' ),
                'error' => __( '오류', 'acf-css-really-simple-style-management-center' ),
            )
        ) );
        
        // [v5.0.3] 키보드 단축키 시스템 로드
        wp_enqueue_script( 'jj-keyboard-shortcuts', JJ_STYLE_GUIDE_URL . 'assets/js/jj-keyboard-shortcuts.js', array( 'jquery', 'jj-common-utils' ), defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0', true );
        
        // [v5.0.3] 툴팁 시스템 로드
        wp_enqueue_script( 'jj-tooltips', JJ_STYLE_GUIDE_URL . 'assets/js/jj-tooltips.js', array( 'jquery', 'jj-common-utils' ), defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0', true );

        // [v5.0.3] 라이브 미리보기 시스템 (관리자용)
        wp_enqueue_script( 'jj-live-preview', JJ_STYLE_GUIDE_URL . 'assets/js/jj-live-preview.js', array( 'jquery', 'jj-common-utils' ), $js_ver, true );
        
        // [v5.6.0] 미디어 업로더
        wp_enqueue_media();
        
        // [v8.3.2] 폼 UX 개선 스크립트
        wp_enqueue_script( 
            'jj-form-enhancer', 
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-form-enhancer.js', 
            array( 'jquery', 'jj-common-utils' ), 
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0', 
            true 
        );
    }

    /**
     * [v13.4.5] JS 로드 실패 시 폴백 스타일 출력
     * - JS가 없으면 탭 기능이 동작하지 않으므로, 기본 탭 내용을 강제로 표시
     */
    public function output_admin_center_fallback_styles() {
        echo '<style type="text/css">';
        echo 'body:not(.js-loaded) .jj-admin-center-tab-content:not([data-tab="general"]) { display: none !important; }';
        echo 'body:not(.js-loaded) .jj-admin-center-tab-content[data-tab="general"] { display: block !important; }';
        echo 'body:not(.js-loaded) .jj-admin-center-sidebar-nav a:not([data-tab="general"]) { opacity: 0.5; pointer-events: none; }';
        echo 'body:not(.js-loaded) .jj-admin-center-sidebar-nav a[data-tab="general"] { font-weight: bold; }';
        echo '</style>';
    }

    /**
     * [v20.2.0] 3J Labs 패밀리 플러그인 메뉴 강조 스타일 (통합 관리)
     * - ACF 스타일 센터: 앰버/오렌지 (#f59e0b)
     * - WP Bulk Manager: 에메랄드 그린 (#10b981) - WP Bulk Manager에서 자체 관리
     * - Code Snippets Box: 퍼플/바이올렛 (#8b5cf6)
     * - WooCommerce Toolkit: 핑크/마젠타 (#ec4899)
     * - AI Extension: 시안/청록 (#06b6d4)
     * - MBA Nudge Flow: 레드/코랄 (#ef4444)
     * - Admin Menu Editor Pro: 인디고 (#6366f1)
     */
    public function output_style_center_menu_highlight() {
        ?>
        <style>
            /* ═══════════════════════════════════════════════════════════════ */
            /* 3J Labs 패밀리 플러그인 메뉴 강조 스타일 */
            /* ═══════════════════════════════════════════════════════════════ */
            
            /* ACF 스타일 센터 - 앰버/오렌지 */
            #adminmenu li.toplevel_page_jj-admin-center > a {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                color: #fff !important;
                font-weight: 700 !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
                border-radius: 4px;
                margin: 2px 8px;
                transition: all 0.2s ease;
            }
            #adminmenu li.toplevel_page_jj-admin-center > a:hover {
                background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
            }
            #adminmenu li.toplevel_page_jj-admin-center > a .wp-menu-image:before {
                color: #fff !important;
            }
            #adminmenu li.toplevel_page_jj-admin-center.current > a,
            #adminmenu li.toplevel_page_jj-admin-center.wp-has-current-submenu > a {
                background: linear-gradient(135deg, #b45309 0%, #92400e 100%) !important;
            }
            
            /* ACF Code Snippets Box - 퍼플/바이올렛 */
            #adminmenu li.toplevel_page_acf-code-snippets > a,
            #adminmenu li.menu-top[class*="acf-code-snippets"] > a {
                background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
                color: #fff !important;
                font-weight: 700 !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
                border-radius: 4px;
                margin: 2px 8px;
                transition: all 0.2s ease;
            }
            #adminmenu li.toplevel_page_acf-code-snippets > a:hover,
            #adminmenu li.menu-top[class*="acf-code-snippets"] > a:hover {
                background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%) !important;
            }
            #adminmenu li.toplevel_page_acf-code-snippets > a .wp-menu-image:before,
            #adminmenu li.menu-top[class*="acf-code-snippets"] > a .wp-menu-image:before {
                color: #fff !important;
            }
            
            /* ACF CSS WooCommerce Toolkit - 핑크/마젠타 */
            #adminmenu li.toplevel_page_acf-css-woocommerce-toolkit > a,
            #adminmenu li.menu-top[class*="acf-css-woo"] > a {
                background: linear-gradient(135deg, #ec4899 0%, #db2777 100%) !important;
                color: #fff !important;
                font-weight: 700 !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
                border-radius: 4px;
                margin: 2px 8px;
                transition: all 0.2s ease;
            }
            #adminmenu li.toplevel_page_acf-css-woocommerce-toolkit > a:hover,
            #adminmenu li.menu-top[class*="acf-css-woo"] > a:hover {
                background: linear-gradient(135deg, #db2777 0%, #be185d 100%) !important;
            }
            #adminmenu li.toplevel_page_acf-css-woocommerce-toolkit > a .wp-menu-image:before,
            #adminmenu li.menu-top[class*="acf-css-woo"] > a .wp-menu-image:before {
                color: #fff !important;
            }
            
            /* ACF CSS AI Extension - 시안/청록 */
            #adminmenu li.toplevel_page_acf-css-ai-extension > a,
            #adminmenu li.menu-top[class*="acf-css-ai"] > a {
                background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;
                color: #fff !important;
                font-weight: 700 !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
                border-radius: 4px;
                margin: 2px 8px;
                transition: all 0.2s ease;
            }
            #adminmenu li.toplevel_page_acf-css-ai-extension > a:hover,
            #adminmenu li.menu-top[class*="acf-css-ai"] > a:hover {
                background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%) !important;
            }
            #adminmenu li.toplevel_page_acf-css-ai-extension > a .wp-menu-image:before,
            #adminmenu li.menu-top[class*="acf-css-ai"] > a .wp-menu-image:before {
                color: #fff !important;
            }
            
            /* ACF MBA Nudge Flow - 레드/코랄 */
            #adminmenu li.toplevel_page_acf-nudge-flow > a,
            #adminmenu li.toplevel_page_acf-mba > a,
            #adminmenu li.menu-top[class*="nudge-flow"] > a {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
                color: #fff !important;
                font-weight: 700 !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
                border-radius: 4px;
                margin: 2px 8px;
                transition: all 0.2s ease;
            }
            #adminmenu li.toplevel_page_acf-nudge-flow > a:hover,
            #adminmenu li.toplevel_page_acf-mba > a:hover,
            #adminmenu li.menu-top[class*="nudge-flow"] > a:hover {
                background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
            }
            #adminmenu li.toplevel_page_acf-nudge-flow > a .wp-menu-image:before,
            #adminmenu li.toplevel_page_acf-mba > a .wp-menu-image:before,
            #adminmenu li.menu-top[class*="nudge-flow"] > a .wp-menu-image:before {
                color: #fff !important;
            }
            
            /* Admin Menu Editor Pro - 인디고 */
            #adminmenu li.toplevel_page_admin-menu-editor-pro > a,
            #adminmenu li.menu-top[class*="admin-menu-editor"] > a {
                background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
                color: #fff !important;
                font-weight: 700 !important;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
                border-radius: 4px;
                margin: 2px 8px;
                transition: all 0.2s ease;
            }
            #adminmenu li.toplevel_page_admin-menu-editor-pro > a:hover,
            #adminmenu li.menu-top[class*="admin-menu-editor"] > a:hover {
                background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important;
            }
            #adminmenu li.toplevel_page_admin-menu-editor-pro > a .wp-menu-image:before,
            #adminmenu li.menu-top[class*="admin-menu-editor"] > a .wp-menu-image:before {
                color: #fff !important;
            }
            
            /* 공통: 모든 3J Labs 메뉴 선택 상태 */
            #adminmenu li[class*="jj-"].current > a,
            #adminmenu li[class*="jj-"].wp-has-current-submenu > a,
            #adminmenu li[class*="acf-"].current > a,
            #adminmenu li[class*="acf-"].wp-has-current-submenu > a {
                opacity: 0.95;
            }
        </style>
        <?php
    }

    /**
     * [v20.2.0] 스타일 센터 메뉴 순서 강제 지정
     * 알림판 > 벌크 매니저 > 스타일 센터 순서
     */
    public function force_style_center_menu_order( $menu_order ) {
        if ( ! is_array( $menu_order ) ) {
            return $menu_order;
        }
        
        $our_slug = 'jj-admin-center';
        $bulk_manager_slug = 'jj-bulk-installer-main';
        
        // 우리 메뉴 위치 찾기
        $our_position = array_search( $our_slug, $menu_order );
        
        if ( $our_position !== false ) {
            // 우리 메뉴를 제거
            unset( $menu_order[ $our_position ] );
            $menu_order = array_values( $menu_order );
        }
        
        // 벌크 매니저 바로 뒤에 삽입
        $bulk_position = array_search( $bulk_manager_slug, $menu_order );
        if ( $bulk_position !== false ) {
            array_splice( $menu_order, $bulk_position + 1, 0, $our_slug );
        } else {
            // 벌크 매니저가 없으면 Dashboard 바로 뒤에
            $dashboard_position = array_search( 'index.php', $menu_order );
            if ( $dashboard_position !== false ) {
                array_splice( $menu_order, $dashboard_position + 1, 0, $our_slug );
            } else {
                array_unshift( $menu_order, $our_slug );
            }
        }
        
        return $menu_order;
    }

    /**
     * 메뉴 페이지 등록
     * [v20.2.0] 최상위 메뉴로 변경 - 알림판 > 벌크 매니저 바로 아래에 "ACF 스타일 센터" 배치
     */
    public function add_admin_menu_page() {
        $menu_title = __( '스타일 센터 🎨', 'acf-css-really-simple-style-management-center' );
        $page_title = ( class_exists( 'JJ_Edition_Controller' ) ? JJ_Edition_Controller::instance()->get_branding( 'full_name' ) : __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ) );

        // [v22.0.0] 최상위 메뉴 하나만 등록 (중복 제거)
        add_menu_page(
            $page_title,
            $menu_title,
            'manage_options',
            'jj-admin-center',
            array( $this, 'render_admin_center_page' ),
            'dashicons-art',
            2.6 
        );

        // [v22.0.0] 실험실 센터를 스타일 센터의 서브메뉴로 통합 (중복 제거)
        if ( class_exists( 'JJ_Labs_Center' ) ) {
            $labs_title = __( '실험실 센터', 'acf-css-really-simple-style-management-center' );
            add_submenu_page(
                'jj-admin-center',
                $labs_title,
                $labs_title,
                'manage_options',
                'jj-labs-center',
                array( JJ_Labs_Center::instance(), 'render_labs_center_page' )
            );
        }
    }

    /**
     * Style Guide 페이지 렌더링
     * [v20.2.2] Style Guide 페이지 등록 및 렌더링
     */
    public function render_style_guide_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        // Style Guide 페이지는 Admin Center와 동일한 내용을 표시
        // (향후 별도 렌더링 로직 추가 가능)
        $this->render_admin_center_page();
    }

    /**
     * [Phase 4.5] 어드민 바(Admin Bar) 메뉴 추가
     * - 어디서든 "스타일 센터/관리자 센터/Customizer"로 즉시 이동
     *
     * @param WP_Admin_Bar $wp_admin_bar
     */
    public function add_admin_bar_menu( $wp_admin_bar ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $menu_title = ( class_exists( 'JJ_Edition_Controller' ) ? JJ_Edition_Controller::instance()->get_branding( 'menu_title' ) : 'ACF CSS' );

        // 최상위 노드
        $wp_admin_bar->add_node( array(
            'id'    => 'jj-style-guide',
            'title' => '<span class="ab-icon dashicons dashicons-art"></span> ' . $menu_title,
            'href'  => admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ),
            'meta'  => array( 'title' => $menu_title ),
        ) );

        // 서브: 스타일 센터
        $wp_admin_bar->add_node( array(
            'id'     => 'jj-style-guide-cockpit',
            'parent' => 'jj-style-guide',
            'title'  => __( '스타일 센터 (Cockpit)', 'acf-css-really-simple-style-management-center' ),
            'href'   => admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ),
        ) );

        // 서브: 설정 관리자 (Admin Center)
        $wp_admin_bar->add_node( array(
            'id'     => 'jj-style-guide-admin',
            'parent' => 'jj-style-guide',
            'title'  => __( '설정 관리자 (Admin Center)', 'acf-css-really-simple-style-management-center' ),
            'href'   => admin_url( 'options-general.php?page=jj-admin-center' ),
        ) );

        // [v5.5.0] 서브: 실험실 센터 (Premium 이상)
        if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'basic' ) ) {
            $wp_admin_bar->add_node( array(
                'id'     => 'jj-style-guide-labs',
                'parent' => 'jj-style-guide',
                'title'  => __( '실험실 센터 (Labs)', 'acf-css-really-simple-style-management-center' ),
                'href'   => admin_url( 'options-general.php?page=jj-labs-center' ),
            ) );
        }

        // 서브: Customizer 패널로 이동
        $wp_admin_bar->add_node( array(
            'id'     => 'jj-style-guide-customizer',
            'parent' => 'jj-style-guide',
            'title'  => __( '실시간 편집 (Customizer)', 'acf-css-really-simple-style-management-center' ),
            'href'   => admin_url( 'customize.php?autofocus[panel]=jj_style_guide_panel' ),
        ) );
    }

    /**
     * 관리자 센터 화면 렌더링
     */
    public function render_admin_center_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // [v7.0.0] 타 플러그인 알림 숨김 (몰입형 환경)
        remove_all_actions( 'admin_notices' );

        // 색상 기본값으로 되돌리기 처리 (다른 저장 로직보다 먼저 처리)
        if ( isset( $_POST['jj_admin_center_reset_colors'] ) && check_admin_referer( 'jj_admin_center_save_action', 'jj_admin_center_nonce' ) ) {
            delete_option( $this->colors_option_key );
            ?>
            <div class="updated notice"><p><?php esc_html_e( '관리자 메뉴 / 상단바 색상이 기본값으로 되돌려졌습니다.', 'acf-css-really-simple-style-management-center' ); ?></p></div>
            <?php
        }

        // 저장 처리 (텍스트 + 섹션 레이아웃 + 메뉴 + 색상)
        if ( isset( $_POST['jj_admin_center_save'] ) && check_admin_referer( 'jj_admin_center_save_action', 'jj_admin_center_nonce' ) ) {
            // 1) 텍스트 필드 저장
            $raw_texts   = isset( $_POST['jj_admin_texts'] ) && is_array( $_POST['jj_admin_texts'] ) ? wp_unslash( $_POST['jj_admin_texts'] ) : array();
            $clean_texts = array();
            foreach ( $raw_texts as $key => $value ) {
                $clean_texts[ sanitize_key( $key ) ] = sanitize_textarea_field( $value );
            }
            update_option( $this->option_key, $clean_texts );

            // 2) 섹션 레이아웃 저장
            $raw_sections   = isset( $_POST['jj_section_layout'] ) && is_array( $_POST['jj_section_layout'] ) ? wp_unslash( $_POST['jj_section_layout'] ) : array();
            $clean_sections = array();
            foreach ( $this->get_default_sections_layout() as $slug => $meta ) {
                $enabled = isset( $raw_sections[ $slug ]['enabled'] ) && '1' === $raw_sections[ $slug ]['enabled'];
                $order   = isset( $raw_sections[ $slug ]['order'] ) ? intval( $raw_sections[ $slug ]['order'] ) : (int) $meta['order'];
                $clean_sections[ $slug ] = array(
                    'enabled' => $enabled ? 1 : 0,
                    'order'   => $order,
                );
            }
            update_option( $this->sections_option_key, $clean_sections );
            
            // [v5.0.0] 캐시 플러시
            self::flush_sections_layout_cache();

            // 3) 관리자 메뉴 레이아웃 저장
            $raw_menu   = isset( $_POST['jj_admin_menu_layout'] ) && is_array( $_POST['jj_admin_menu_layout'] ) ? wp_unslash( $_POST['jj_admin_menu_layout'] ) : array();
            $clean_menu = array();
            foreach ( $raw_menu as $slug => $meta ) {
                $slug    = sanitize_key( $slug );
                $enabled = isset( $meta['enabled'] ) && '1' === $meta['enabled'];
                $order   = isset( $meta['order'] ) ? intval( $meta['order'] ) : 0;
                $label   = isset( $meta['label'] ) ? sanitize_text_field( $meta['label'] ) : '';
                $icon    = isset( $meta['icon'] ) ? sanitize_text_field( $meta['icon'] ) : '';
                $capability = isset( $meta['capability'] ) ? sanitize_text_field( $meta['capability'] ) : '';
                
                // 서브메뉴 저장
                $submenus = array();
                if ( isset( $meta['submenus'] ) && is_array( $meta['submenus'] ) ) {
                    foreach ( $meta['submenus'] as $submenu_slug => $submenu_meta ) {
                        $submenu_slug = sanitize_key( $submenu_slug );
                        $submenu_enabled = isset( $submenu_meta['enabled'] ) && '1' === $submenu_meta['enabled'];
                        $submenu_label = isset( $submenu_meta['label'] ) ? sanitize_text_field( $submenu_meta['label'] ) : '';
                        $submenu_order = isset( $submenu_meta['order'] ) ? intval( $submenu_meta['order'] ) : 0;
                        $submenu_capability = isset( $submenu_meta['capability'] ) ? sanitize_text_field( $submenu_meta['capability'] ) : '';
                        
                        $submenus[ $submenu_slug ] = array(
                            'enabled' => $submenu_enabled ? 1 : 0,
                            'label'   => $submenu_label,
                            'order'   => $submenu_order,
                            'capability' => $submenu_capability,
                        );
                    }
                }
                
                $clean_menu[ $slug ] = array(
                    'enabled' => $enabled ? 1 : 0,
                    'order'   => $order,
                    'label'   => $label,
                    'icon'    => $icon,
                    'capability' => $capability,
                    'submenus' => $submenus,
                );
            }
            update_option( $this->menu_option_key, $clean_menu );

            // 4) 관리자 메뉴/상단바 색상 저장
            $raw_colors   = isset( $_POST['jj_admin_menu_colors'] ) && is_array( $_POST['jj_admin_menu_colors'] ) ? wp_unslash( $_POST['jj_admin_menu_colors'] ) : array();
            $clean_colors = array();
            foreach ( $this->get_default_admin_colors() as $key => $default_hex ) {
                $raw = isset( $raw_colors[ $key ] ) ? $raw_colors[ $key ] : '';
                $san = sanitize_hex_color( $raw );
                $clean_colors[ $key ] = $san ? $san : $default_hex;
            }
            update_option( $this->colors_option_key, $clean_colors );
            ?>
            <div class="updated notice"><p><?php esc_html_e( '관리자 센터 설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ); ?></p></div>
            <?php
        }

        $stored          = (array) get_option( $this->option_key );
        $admin_texts     = $stored; // 변수명 통일
        $sections_layout = $this->get_sections_layout();
        $menu_layout     = $this->get_menu_layout();
        $colors_layout   = $this->get_admin_menu_colors();
        
        global $menu, $submenu; // [Fix] 메뉴 탭에서 사용
        ?>
        <div class="wrap jj-admin-center-wrap jj-has-sidebar">
            <!-- [v6.3.0] 왼쪽 고정 사이드바 네비게이션 -->
            <div class="jj-admin-center-sidebar">
                <div class="jj-admin-center-sidebar-header">
                    <h2><?php esc_html_e( '빠른 이동', 'acf-css-really-simple-style-management-center' ); ?></h2>
                    <button type="button" class="jj-sidebar-toggle" aria-label="<?php esc_attr_e( '사이드바 토글', 'acf-css-really-simple-style-management-center' ); ?>">
                        <span class="dashicons dashicons-menu-alt"></span>
                    </button>
                </div>
                <ul class="jj-admin-center-sidebar-nav">
                    <li>
                        <a href="#general" data-tab="general" class="active">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php esc_html_e( 'General', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#admin-menu" data-tab="admin-menu">
                            <span class="dashicons dashicons-menu"></span>
                            <?php esc_html_e( 'Admin Menu', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#section-layout" data-tab="section-layout">
                            <span class="dashicons dashicons-layout"></span>
                            <?php esc_html_e( 'Section Layout', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#texts" data-tab="texts">
                            <span class="dashicons dashicons-text"></span>
                            <?php esc_html_e( 'Texts', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#colors" data-tab="colors">
                            <span class="dashicons dashicons-admin-appearance"></span>
                            <?php esc_html_e( 'Colors', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#visual" data-tab="visual">
                            <span class="dashicons dashicons-visibility"></span>
                            <?php esc_html_e( 'Visual', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#cloud" data-tab="cloud">
                            <span class="dashicons dashicons-cloud"></span>
                            <?php esc_html_e( 'Cloud', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#backup" data-tab="backup">
                            <span class="dashicons dashicons-backup"></span>
                            <?php esc_html_e( '백업 관리', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#tools" data-tab="tools">
                            <span class="dashicons dashicons-admin-tools"></span>
                            <?php esc_html_e( '도구', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#figma" data-tab="figma">
                            <span class="dashicons dashicons-art"></span>
                            <?php esc_html_e( 'Figma', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#updates" data-tab="updates">
                            <span class="dashicons dashicons-update"></span>
                            <?php esc_html_e( '업데이트', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#license" data-tab="license">
                            <span class="dashicons dashicons-admin-network"></span>
                            <?php esc_html_e( '라이센스', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#system-status" data-tab="system-status">
                            <span class="dashicons dashicons-info"></span>
                            <?php esc_html_e( '시스템 상태', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <?php if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'basic' ) ) : ?>
                    <li class="jj-sidebar-nav-divider" style="margin: 10px 0; border-top: 1px solid #333;"></li>
                    <li>
                        <a href="<?php echo esc_url( admin_url( 'options-general.php?page=jj-labs-center' ) ); ?>">
                            <span class="dashicons dashicons-beaker"></span>
                            <?php esc_html_e( 'ACF CSS 실험실 센터', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="jj-admin-center-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h1 style="margin: 0;"><?php esc_html_e( 'ACF CSS 설정 관리자', 'acf-css-really-simple-style-management-center' ); ?></h1>
                        <a href="<?php echo esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ); ?>" 
                           class="button button-secondary" 
                           style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <?php esc_html_e( '스타일 센터', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                    <a href="<?php echo esc_url( admin_url( 'options-general.php?page=jj-labs-center' ) ); ?>" 
                       class="button button-secondary" 
                       style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <?php esc_html_e( 'ACF CSS 실험실 센터', 'acf-css-really-simple-style-management-center' ); ?>
                    </a>
                    <?php
                    // [v5.1.6] 마스터 버전이 아닌 경우 결제 유도 문구 표시
                    $is_master_version = false;
                    if ( class_exists( 'JJ_Edition_Controller' ) ) {
                        $is_master_version = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                    }
                    if ( ! $is_master_version ) {
                        $license_manager = null;
                        $purchase_url = 'https://3j-labs.com'; // 기본값
                        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                            if ( class_exists( 'JJ_License_Manager' ) ) {
                                $license_manager = JJ_License_Manager::instance();
                                if ( $license_manager ) {
                                    $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                                }
                            }
                        }
                        ?>
                        <a href="<?php echo esc_url( $purchase_url ); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="button button-primary" 
                           style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px; font-weight: 600;">
                            <span class="dashicons dashicons-cart" style="margin-top: 4px;"></span> <?php esc_html_e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <div class="jj-header-actions">
                    <!-- [v3.7.0 '신규'] 관리자 센터 설정 내보내기/불러오기 (헤더) -->
                    <button type="button" class="button button-secondary jj-export-settings" data-center="admin-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px; margin-right: 8px;">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php esc_html_e( '내보내기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-settings" data-center="admin-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span> <?php esc_html_e( '불러오기', 'acf-css-really-simple-style-management-center' ); ?>
                    </button>
                </div>
            </div>

            <!-- 탭 네비게이션 -->
            <ul class="jj-admin-center-tabs">
                <li data-tab="general" class="active">
                    <a href="#general"><?php esc_html_e( 'General', 'acf-css-really-simple-style-management-center' ); ?></a>
                </li>
                <li data-tab="admin-menu">
                    <a href="#admin-menu"><?php esc_html_e( 'Admin Menu', 'acf-css-really-simple-style-management-center' ); ?></a>
                </li>
                <li data-tab="section-layout">
                    <a href="#section-layout"><?php esc_html_e( 'Section Layout', 'acf-css-really-simple-style-management-center' ); ?></a>
                </li>
                <li data-tab="texts">
                    <a href="#texts"><?php esc_html_e( 'Texts', 'acf-css-really-simple-style-management-center' ); ?></a>
                </li>
                <li data-tab="colors">
                    <a href="#colors"><?php esc_html_e( 'Colors', 'acf-css-really-simple-style-management-center' ); ?></a>
                </li>
                <li data-tab="visual">
                    <a href="#visual"><?php esc_html_e( 'Visual', 'acf-css-really-simple-style-management-center' ); ?></a>
                </li>
                <li data-tab="cloud">
                    <a href="#cloud"><?php esc_html_e( 'Cloud', 'acf-css-really-simple-style-management-center' ); ?></a>
                </li>
                <li data-tab="backup">
                    <a href="#backup"><?php esc_html_e( '백업 관리', 'acf-css-really-simple-style-management-center' ); ?></a>
                </li>
                <li data-tab="tools">
                    <a href="#tools"><?php esc_html_e( '도구', 'acf-css-really-simple-style-management-center' ); ?></a>
                </li>
                <li data-tab="figma">
                    <a href="#figma"><?php esc_html_e( 'Figma', 'acf-css-really-simple-style-management-center' ); ?></a>
                </li>
                <li data-tab="updates">
                    <a href="#updates"><?php esc_html_e( '업데이트', 'acf-css-really-simple-style-management-center' ); ?></a>
                </li>
                <li data-tab="license">
                    <a href="#license"><?php esc_html_e( '라이센스', 'acf-css-really-simple-style-management-center' ); ?></a>
                </li>
                <li data-tab="system-status">
                    <a href="#system-status"><?php esc_html_e( '시스템 상태', 'acf-css-really-simple-style-management-center' ); ?></a>
                </li>
            </ul>

            <form method="post" id="jj-admin-center-form">
                <?php wp_nonce_field( 'jj_admin_center_save_action', 'jj_admin_center_nonce' ); ?>

                <?php
                // [Phase 19.1] Figma 클래스 로드 (탭 파일 include 전에 로드)
                if ( ! class_exists( 'JJ_Figma_Connector' ) ) {
                    $figma_connector_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-figma-connector.php';
                    if ( file_exists( $figma_connector_path ) ) {
                        require_once $figma_connector_path;
                        if ( class_exists( 'JJ_Figma_Connector' ) ) {
                            JJ_Figma_Connector::instance()->init();
                        }
                    }
                }
                
                // [Phase 19.1] Figma Advanced Integration 클래스 로드
                if ( ! class_exists( 'JJ_Figma_Advanced_Integration' ) ) {
                    $figma_advanced_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-figma-advanced-integration.php';
                    if ( file_exists( $figma_advanced_path ) ) {
                        require_once $figma_advanced_path;
                        if ( class_exists( 'JJ_Figma_Advanced_Integration' ) ) {
                            JJ_Figma_Advanced_Integration::instance()->init();
                        }
                    }
                }
                
                // Admin Center 탭 파일 include (누락 방지/안전)
                $tabs = array(
                    'tab-general.php',
                    'tab-admin-menu.php',
                    'tab-section-layout.php',
                    'tab-texts.php',
                    'tab-colors.php',
                    'tab-visual.php',
                    'tab-cloud.php',
                    'tab-backup.php',
                    'tab-tools.php',
                    'tab-figma.php', // [Phase 13] Figma 연동, [Phase 19.1] 고급 기능 추가
                    'tab-updates.php',
                    'tab-system-status.php',
                );

                foreach ( $tabs as $tab_file ) {
                    $tab_path = JJ_STYLE_GUIDE_PATH . 'includes/admin/views/tabs/' . $tab_file;
                    
                    // [v13.4.7] 탭 로딩 안전 처리 (Throwable catch)
                    // - 특정 탭 파일에 문법 오류가 있어도 전체 페이지가 흰 화면이 되지 않도록 방어
                    try {
                        if ( file_exists( $tab_path ) ) {
                            include $tab_path;
                        } else {
                            echo '<div class="notice notice-error jj-notice"><p>' . esc_html( '탭 파일을 찾을 수 없습니다: ' . $tab_file ) . '</p></div>';
                        }
                    } catch ( Exception $e ) {
                        echo '<div class="notice notice-error jj-notice"><p><strong>Error loading tab ' . esc_html( $tab_file ) . ':</strong> ' . esc_html( $e->getMessage() ) . '</p></div>';
                    } catch ( Error $e ) {
                        echo '<div class="notice notice-error jj-notice"><p><strong>Fatal Error loading tab ' . esc_html( $tab_file ) . ':</strong> ' . esc_html( $e->getMessage() ) . '</p></div>';
                    }
                }
                ?>

                <!-- [v3.7.0+] 관리자 센터 설정 저장/내보내기/불러오기 (푸터) -->
                <div class="jj-admin-center-footer" style="margin-top: 30px; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                    <div style="display:flex; gap:8px; align-items:center;">
                        <button type="submit" name="jj_admin_center_save" class="button button-primary">
                            <?php esc_html_e( '저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <button type="button" class="button button-secondary jj-export-settings" data-center="admin-center">
                            <span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php esc_html_e( '내보내기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <button type="button" class="button button-secondary jj-import-settings" data-center="admin-center">
                            <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span> <?php esc_html_e( '불러오기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                    <div style="color:#666; font-size:12px;">
                        <?php esc_html_e( '변경 후 “저장”을 눌러 적용하세요.', 'acf-css-really-simple-style-management-center' ); ?>
                    </div>
                </div>
            </form>

            <?php
            // License 탭은 자체 <form>을 포함하므로, 상위 폼 밖에서 별도 렌더(중첩 form 방지)
            $tab_license = JJ_STYLE_GUIDE_PATH . 'includes/admin/views/tabs/tab-license.php';
            if ( file_exists( $tab_license ) ) {
                try {
                    include $tab_license;
                } catch ( Throwable $e ) {
                    echo '<div class="notice notice-error jj-notice"><p>Error loading License tab: ' . esc_html( $e->getMessage() ) . '</p></div>';
                }
            } else {
                echo '<div class="notice notice-error jj-notice"><p>' . esc_html( '탭 파일을 찾을 수 없습니다: tab-license.php' ) . '</p></div>';
            }
            ?>
        </div><!-- /.wrap -->
        <?php
    }

    /**
     * UI 텍스트 가져오기
     *
     * @param string $key
     * @param string $fallback
     * @return string
     */
    public function get_text( $key, $fallback = '' ) {
        $stored = (array) get_option( $this->option_key );

        if ( isset( $stored[ $key ] ) && $stored[ $key ] !== '' ) {
            return $stored[ $key ];
        }

        if ( isset( $this->config[ $key ]['default'] ) ) {
            return $this->config[ $key ]['default'];
        }

        return $fallback;
    }

    /**
     * 섹션 레이아웃 기본값
     *
     * @return array
     */
    public function get_default_sections_layout() {
        // [v5.0.0] 탭 활성화/비활성화 기능 추가
        $defaults = array(
            'colors'     => array(
                'label'  => __( '1. 팔레트 시스템', 'acf-css-really-simple-style-management-center' ),
                'order'  => 10,
                'enabled'=> 1,
                'tabs'   => array( // [v5.0.0] 탭 활성화/비활성화
                    'brand'       => array( 'label' => __( '브랜드 팔레트', 'acf-css-really-simple-style-management-center' ), 'enabled' => 1 ),
                    'system'      => array( 'label' => __( '시스템 팔레트', 'acf-css-really-simple-style-management-center' ), 'enabled' => 1 ),
                    'alternative' => array( 'label' => __( '얼터너티브 팔레트', 'acf-css-really-simple-style-management-center' ), 'enabled' => 1 ),
                    'another'     => array( 'label' => __( '어나더 팔레트', 'acf-css-really-simple-style-management-center' ), 'enabled' => 1 ),
                    'temp-palette'=> array( 'label' => __( '임시 팔레트', 'acf-css-really-simple-style-management-center' ), 'enabled' => 1 ),
                ),
            ),
            'typography' => array(
                'label'  => __( '2. 타이포그래피', 'acf-css-really-simple-style-management-center' ),
                'order'  => 20,
                'enabled'=> 1,
                'tabs'   => array(), // 탭 없음
            ),
            'buttons'    => array(
                'label'  => __( '3. 버튼', 'acf-css-really-simple-style-management-center' ),
                'order'  => 30,
                'enabled'=> 1,
                'tabs'   => array( // [v5.0.0] 탭 활성화/비활성화
                    'btn-primary'   => array( 'label' => __( 'Primary Button', 'acf-css-really-simple-style-management-center' ), 'enabled' => 1 ),
                    'btn-secondary' => array( 'label' => __( 'Secondary Button', 'acf-css-really-simple-style-management-center' ), 'enabled' => 1 ),
                    'btn-text'      => array( 'label' => __( 'Text / Outline Button', 'acf-css-really-simple-style-management-center' ), 'enabled' => 1 ),
                ),
            ),
            'forms'      => array(
                'label'  => __( '4. 폼 & 필드', 'acf-css-really-simple-style-management-center' ),
                'order'  => 40,
                'enabled'=> 1,
                'tabs'   => array( // [v5.0.0] 탭 활성화/비활성화
                    'form-label' => array( 'label' => __( '라벨 (Labels)', 'acf-css-really-simple-style-management-center' ), 'enabled' => 1 ),
                    'form-field' => array( 'label' => __( '입력 필드 (Fields)', 'acf-css-really-simple-style-management-center' ), 'enabled' => 1 ),
                ),
            ),
        );

        /**
         * 섹션 레이아웃 기본값을 필터링할 수 있습니다.
         */
        return apply_filters( 'jj_style_guide_default_sections_layout', $defaults );
    }

    /**
     * 섹션 레이아웃 가져오기
     * [v5.0.0] 성능 최적화: static 캐싱 추가
     *
     * @return array
     */
    public function get_sections_layout() {
        // [v5.0.0] 캐시된 값이 있으면 반환
        if ( self::$sections_layout_cache !== null ) {
            return self::$sections_layout_cache;
        }
        
        $stored   = (array) get_option( $this->sections_option_key );
        $defaults = $this->get_default_sections_layout();
        $result   = array();

        foreach ( $defaults as $slug => $meta ) {
            $enabled = isset( $stored[ $slug ]['enabled'] ) ? (bool) $stored[ $slug ]['enabled'] : (bool) ( $meta['enabled'] ?? true );
            $order   = isset( $stored[ $slug ]['order'] ) ? intval( $stored[ $slug ]['order'] ) : (int) ( $meta['order'] ?? 0 );
            
            // [v5.0.0] 탭 활성화/비활성화 정보 병합
            $tabs = array();
            if ( isset( $meta['tabs'] ) && is_array( $meta['tabs'] ) ) {
                foreach ( $meta['tabs'] as $tab_slug => $tab_meta ) {
                    $tab_enabled = isset( $stored[ $slug ]['tabs'][ $tab_slug ]['enabled'] ) 
                        ? (bool) $stored[ $slug ]['tabs'][ $tab_slug ]['enabled'] 
                        : (bool) ( $tab_meta['enabled'] ?? true );
                    $tabs[ $tab_slug ] = array(
                        'label'   => $tab_meta['label'],
                        'enabled' => $tab_enabled ? 1 : 0,
                    );
                }
            }
            
            $result[ $slug ] = array(
                'label'   => $meta['label'],
                'enabled' => $enabled ? 1 : 0,
                'order'   => $order,
                'tabs'    => $tabs, // [v5.0.0] 탭 활성화/비활성화 정보
            );
        }

        // [v5.0.0] 캐시에 저장
        self::$sections_layout_cache = $result;
        
        return $result;
    }
    
    /**
     * 섹션 레이아웃 캐시 플러시
     * [v5.0.0] 성능 최적화: 옵션이 업데이트되면 호출하여 캐시를 무효화
     *
     * @return void
     */
    public static function flush_sections_layout_cache() {
        self::$sections_layout_cache = null;
    }
    
    /**
     * [v5.0.0] 특정 섹션의 특정 탭이 활성화되어 있는지 확인
     *
     * @param string $section_slug 섹션 슬러그
     * @param string $tab_slug 탭 슬러그
     * @return bool
     */
    public function is_tab_enabled( $section_slug, $tab_slug ) {
        $layout = $this->get_sections_layout();
        if ( ! isset( $layout[ $section_slug ] ) ) {
            return false;
        }
        if ( ! isset( $layout[ $section_slug ]['tabs'][ $tab_slug ] ) ) {
            return true; // 탭이 정의되지 않았으면 활성화된 것으로 간주
        }
        return ! empty( $layout[ $section_slug ]['tabs'][ $tab_slug ]['enabled'] );
    }

    /**
     * 주어진 섹션 슬러그의 현재 표시 순서(1부터 시작하는 인덱스)를 반환
     *
     * @param string $slug
     * @return int|null
     */
    public function get_section_index( $slug ) {
        $layout = $this->get_sections_layout();

        // 표시가 허용된 섹션만 정렬
        $enabled_sections = array_filter(
            $layout,
            function ( $meta ) {
                return ! empty( $meta['enabled'] );
            }
        );

        uasort(
            $enabled_sections,
            function ( $a, $b ) {
                return (int) $a['order'] <=> (int) $b['order'];
            }
        );

        $index = 1;
        foreach ( $enabled_sections as $section_slug => $meta ) {
            if ( $section_slug === $slug ) {
                return $index;
            }
            $index++;
        }

        return null;
    }

    /**
     * 관리자 메뉴 레이아웃 반환
     *
     * @return array
     */
    public function get_menu_layout() {
        $stored = (array) get_option( $this->menu_option_key );
        return $stored;
    }

    /**
     * 관리자 메뉴 / 상단바 색상 기본값
     *
     * @return array
     */
    public function get_default_admin_colors() {
        return array(
            'sidebar_bg'         => '#1f2933',
            'sidebar_text'       => '#d9e2ec',
            'sidebar_text_hover' => '#ffffff',
            'sidebar_bg_hover'   => '#111827',
            'sidebar_bg_active'  => '#111827',
            'sidebar_text_active'=> '#ffffff',
            'topbar_bg'          => '#111827',
            'topbar_text'        => '#d9e2ec',
            'topbar_text_hover'  => '#ffffff',
        );
    }

    /**
     * 저장된 관리자 메뉴 / 상단바 색상
     *
     * @return array
     */
    public function get_admin_menu_colors() {
        $stored   = (array) get_option( $this->colors_option_key );
        $defaults = $this->get_default_admin_colors();

        return array_merge( $defaults, $stored );
    }

    /**
     * [v22.0.0] get_admin_menu_colors()의 별칭 (Fatal Error 방지)
     */
    public function get_admin_colors() {
        return $this->get_admin_menu_colors();
    }

    /**
     * admin_menu 훅에서 실제 메뉴 배열에 label/visibility/icon/capability 적용
     * [v3.7.0 '신규'] 아이콘, 권한, 서브메뉴 편집 기능 추가
     */
    public function apply_admin_menu_customizations() {
        $layout = $this->get_menu_layout();
        if ( empty( $layout ) ) {
            return;
        }

        global $menu, $submenu;
        if ( ! is_array( $menu ) ) {
            return;
        }

        foreach ( $menu as $index => $item ) {
            if ( ! isset( $item[2] ) ) {
                continue;
            }
            $slug = sanitize_key( $item[2] );
            if ( ! isset( $layout[ $slug ] ) ) {
                continue;
            }

            $meta = $layout[ $slug ];

            // 표시 여부
            if ( isset( $meta['enabled'] ) && ! $meta['enabled'] ) {
                // 메뉴 숨김 시 서브메뉴도 함께 숨김
                if ( isset( $submenu[ $slug ] ) ) {
                    unset( $submenu[ $slug ] );
                }
                unset( $menu[ $index ] );
                continue;
            }

            // 커스텀 레이블
            if ( ! empty( $meta['label'] ) ) {
                $menu[ $index ][0] = $meta['label'];
            }
            
            // [v3.7.0 '신규'] 커스텀 아이콘
            if ( ! empty( $meta['icon'] ) ) {
                $icon = $meta['icon'];
                // Dashicons 클래스 이름 정규화
                if ( strpos( $icon, 'dashicons-' ) === 0 ) {
                    $menu[ $index ][6] = $icon;
                } elseif ( strpos( $icon, 'dashicons ' ) === 0 ) {
                    $menu[ $index ][6] = 'dashicons-' . str_replace( 'dashicons ', '', $icon );
                } elseif ( filter_var( $icon, FILTER_VALIDATE_URL ) ) {
                    // 이미지 URL
                    $menu[ $index ][6] = $icon;
                }
            }
            
            // [v3.7.0 '신규'] 커스텀 권한
            if ( ! empty( $meta['capability'] ) ) {
                // 현재 사용자가 해당 권한을 가지고 있는지 확인
                if ( ! current_user_can( $meta['capability'] ) ) {
                    // 권한이 없으면 메뉴 숨김
                    if ( isset( $submenu[ $slug ] ) ) {
                        unset( $submenu[ $slug ] );
                    }
                    unset( $menu[ $index ] );
                    continue;
                }
                
                // 메뉴 아이템의 권한 변경 (주의: 실제 페이지 접근 권한은 변경되지 않음)
                $menu[ $index ][1] = $meta['capability'];
            }
            
            // 서브메뉴 커스터마이징
            if ( isset( $meta['submenus'] ) && is_array( $meta['submenus'] ) && isset( $submenu[ $slug ] ) ) {
                foreach ( $submenu[ $slug ] as $sub_index => $sub_item ) {
                    if ( ! isset( $sub_item[2] ) ) continue;
                    $sub_slug = sanitize_key( $sub_item[2] );
                    
                    if ( isset( $meta['submenus'][ $sub_slug ] ) ) {
                        $sub_meta = $meta['submenus'][ $sub_slug ];
                        
                        // 서브메뉴 숨김
                        if ( isset( $sub_meta['enabled'] ) && ! $sub_meta['enabled'] ) {
                            unset( $submenu[ $slug ][ $sub_index ] );
                            continue;
                        }
                        
                        // 서브메뉴 레이블 변경
                        if ( ! empty( $sub_meta['label'] ) ) {
                            $submenu[ $slug ][ $sub_index ][0] = $sub_meta['label'];
                        }
                        
                        // 서브메뉴 권한 변경
                        if ( ! empty( $sub_meta['capability'] ) ) {
                            if ( ! current_user_can( $sub_meta['capability'] ) ) {
                                unset( $submenu[ $slug ][ $sub_index ] );
                                continue;
                            }
                            $submenu[ $slug ][ $sub_index ][1] = $sub_meta['capability'];
                        }
                    }
                }
                // 배열 인덱스 재정렬 (중간에 빠진 항목 때문에)
                $submenu[ $slug ] = array_values( $submenu[ $slug ] );
            }
        }
        
        // 메뉴 순서 재정렬은 filter_menu_order에서 처리
    }

    /**
     * menu_order 필터 핸들러
     */
    public function filter_menu_order( $menu_order ) {
        $layout = $this->get_menu_layout();
        if ( empty( $layout ) ) {
            return $menu_order;
        }

        // 순서가 지정된 항목들 추출
        $ordered_items = array();
        foreach ( $layout as $slug => $meta ) {
            if ( isset( $meta['order'] ) && $meta['order'] > 0 ) {
                // 원래 슬러그 찾기 (sanitize된 키와 매핑)
                $original_slug = $this->find_original_slug( $slug, $menu_order );
                if ( $original_slug ) {
                    $ordered_items[ $original_slug ] = (int) $meta['order'];
                }
            }
        }

        if ( empty( $ordered_items ) ) {
            return $menu_order;
        }

        // 순서대로 정렬
        asort( $ordered_items );

        $new_order = array();
        $remaining_items = $menu_order;

        // 1. 순서 지정된 항목들 먼저 배치
        foreach ( $ordered_items as $slug => $order ) {
            if ( in_array( $slug, $remaining_items ) ) {
                $new_order[] = $slug;
                $key = array_search( $slug, $remaining_items );
                if ( $key !== false ) {
                    unset( $remaining_items[ $key ] );
                }
            }
        }

        // 2. 나머지 항목들 배치
        $new_order = array_merge( $new_order, array_values( $remaining_items ) );

        return $new_order;
    }

    /**
     * sanitize된 키로부터 원본 메뉴 슬러그 찾기
     */
    private function find_original_slug( $sanitized_key, $menu_order ) {
        foreach ( $menu_order as $slug ) {
            if ( sanitize_key( $slug ) === $sanitized_key ) {
                return $slug;
            }
        }
        return false;
    }

    /**
     * 커스텀 스타일 출력 (관리자 헤더)
     */
    public function output_admin_menu_styles() {
        $colors = $this->get_admin_menu_colors();
        $defaults = $this->get_default_admin_colors();
        
        // 변경된 색상이 없으면 출력하지 않음
        $diff = array_diff_assoc( $colors, $defaults );
        if ( empty( $diff ) ) {
            return;
        }
        
        ?>
        <style type="text/css">
            /* Admin Menu Custom Colors */
            #adminmenu, #adminmenu .wp-submenu, #adminmenuback, #adminmenuwrap {
                background-color: <?php echo esc_attr( $colors['sidebar_bg'] ); ?> !important;
            }
            #adminmenu a {
                color: <?php echo esc_attr( $colors['sidebar_text'] ); ?> !important;
            }
            #adminmenu a:hover, #adminmenu li.menu-top:hover, #adminmenu li.opensub > a.menu-top, #adminmenu li > a.menu-top:focus {
                color: <?php echo esc_attr( $colors['sidebar_text_hover'] ); ?> !important;
                background-color: <?php echo esc_attr( $colors['sidebar_bg_hover'] ); ?> !important;
            }
            #adminmenu li.current a.menu-top, #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu {
                background-color: <?php echo esc_attr( $colors['sidebar_bg_active'] ); ?> !important;
                color: <?php echo esc_attr( $colors['sidebar_text_active'] ); ?> !important;
            }
            #adminmenu .wp-submenu a {
                color: <?php echo esc_attr( $colors['sidebar_text'] ); ?> !important; /* 서브메뉴 텍스트도 동일하게 */
            }
            #adminmenu .wp-submenu a:hover, #adminmenu .wp-submenu a:focus {
                color: <?php echo esc_attr( $colors['sidebar_text_hover'] ); ?> !important;
            }
            
            /* Admin Bar Custom Colors */
            #wpadminbar {
                background-color: <?php echo esc_attr( $colors['topbar_bg'] ); ?> !important;
                color: <?php echo esc_attr( $colors['topbar_text'] ); ?> !important;
            }
            #wpadminbar .ab-item, #wpadminbar a.ab-item, #wpadminbar > #wp-toolbar span.ab-label, #wpadminbar > #wp-toolbar span.noticon {
                color: <?php echo esc_attr( $colors['topbar_text'] ); ?> !important;
            }
            #wpadminbar .ab-top-secondary {
                background-color: <?php echo esc_attr( $colors['topbar_bg'] ); ?> !important;
            }
            #wpadminbar .quicklinks .menupop ul.ab-sub-secondary, #wpadminbar .quicklinks .menupop ul.ab-sub-secondary .ab-submenu {
                background-color: <?php echo esc_attr( $colors['topbar_bg'] ); ?> !important;
            }
            #wpadminbar:not(.mobile) .ab-top-menu > li:hover > .ab-item, #wpadminbar:not(.mobile) .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojs .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item {
                background-color: <?php echo esc_attr( $colors['sidebar_bg_hover'] ); ?> !important;
                color: <?php echo esc_attr( $colors['topbar_text_hover'] ); ?> !important;
            }
        </style>
        <?php
    }

    /**
     * AJAX: 색상 초기화
     */
    public function ajax_reset_admin_colors() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        
        delete_option( $this->colors_option_key );
        wp_send_json_success( array( 'message' => __( '색상이 초기화되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * AJAX: Admin Center 설정 저장 (통합 핸들러)
     */
    public function ajax_save_admin_center_settings() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $data = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : array();
        // data는 jQuery serialize()로 전송된 문자열일 수 있음
        if ( is_string( $data ) ) {
            parse_str( $data, $parsed_data );
            $data = $parsed_data;
        }

        // 1. 텍스트 설정 저장
        if ( isset( $data['jj_admin_texts'] ) ) {
            $clean_texts = array();
            foreach ( $data['jj_admin_texts'] as $key => $value ) {
                $clean_texts[ sanitize_key( $key ) ] = sanitize_textarea_field( $value );
            }
            update_option( $this->option_key, $clean_texts );
        }

        // 2. 섹션 레이아웃 저장
        if ( isset( $data['jj_section_layout'] ) ) {
            $raw_sections = $data['jj_section_layout'];
            $clean_sections = array();
            foreach ( $this->get_default_sections_layout() as $slug => $meta ) {
                $enabled = isset( $raw_sections[ $slug ]['enabled'] ) && '1' === $raw_sections[ $slug ]['enabled'];
                $order   = isset( $raw_sections[ $slug ]['order'] ) ? intval( $raw_sections[ $slug ]['order'] ) : (int) $meta['order'];
                
                // [v5.0.0] 탭 정보 저장
                $tabs = array();
                if ( isset( $meta['tabs'] ) && is_array( $meta['tabs'] ) ) {
                    foreach ( $meta['tabs'] as $tab_slug => $tab_meta ) {
                        // 기본값
                        $tab_enabled = true;
                        // POST 데이터 확인
                        if ( isset( $raw_sections[ $slug ]['tabs'][ $tab_slug ] ) ) {
                            $tab_raw = $raw_sections[ $slug ]['tabs'][ $tab_slug ];
                            // enabled 키가 존재하고 '1'이면 활성화, 아니면 비활성화 (체크박스 미전송 시 비활성화)
                            // 주의: serialize() 된 데이터는 체크된 항목만 전송됨
                            // 따라서 'tabs' 배열 내에 해당 탭 키가 아예 없으면 비활성화로 간주해야 함?
                            // -> 클라이언트 JS에서 체크박스 값을 적절히 처리해서 보내야 함.
                            // 여기서는 간단히: 데이터에 있으면 값 확인, 없으면... 기존 로직 유지?
                            // jQuery serialize()는 체크 안 된 체크박스는 보내지 않음.
                            // hidden input 트릭을 쓰거나, JS에서 처리해야 함.
                            // 여기서는 일단 넘어온 데이터 기준으로 처리.
                            $tab_enabled = isset( $tab_raw['enabled'] ) && '1' === $tab_raw['enabled'];
                        } else {
                            // 데이터가 아예 없으면? (섹션 전체가 비활성화되었거나 등)
                            // 기존 값이 있으면 유지? 아니면 비활성화?
                            // 안전하게 기본값 true로? 아니면 false?
                            // UX상: 체크 해제 -> 데이터 없음 -> false가 맞음.
                            // 단, 섹션 자체가 전송되지 않은 경우와 구분 필요.
                            $tab_enabled = false;
                        }
                        
                        $tabs[ $tab_slug ] = array(
                            'enabled' => $tab_enabled ? 1 : 0
                        );
                    }
                }

                $clean_sections[ $slug ] = array(
                    'enabled' => $enabled ? 1 : 0,
                    'order'   => $order,
                    'tabs'    => $tabs,
                );
            }
            update_option( $this->sections_option_key, $clean_sections );
            self::flush_sections_layout_cache();
        }

        // 3. 관리자 메뉴 레이아웃 저장
        if ( isset( $data['jj_admin_menu_layout'] ) ) {
            $raw_menu = $data['jj_admin_menu_layout'];
            $clean_menu = array();
            
            // [v4.0.2] 메뉴 순서 배열 별도 처리
            $menu_order_map = array();
            if ( isset( $data['jj_admin_menu_order'] ) && is_array( $data['jj_admin_menu_order'] ) ) {
                foreach ( $data['jj_admin_menu_order'] as $index => $slug ) {
                    $menu_order_map[ $slug ] = $index + 1;
                }
            }

            foreach ( $raw_menu as $slug => $meta ) {
                $slug    = sanitize_key( $slug );
                $enabled = isset( $meta['enabled'] ) && '1' === $meta['enabled'];
                
                // 순서: 별도 맵이 있으면 우선 사용, 없으면 기존 order 값 사용
                $order = isset( $menu_order_map[ $slug ] ) ? $menu_order_map[ $slug ] : ( isset( $meta['order'] ) ? intval( $meta['order'] ) : 0 );
                
                $label   = isset( $meta['label'] ) ? sanitize_text_field( $meta['label'] ) : '';
                $icon    = isset( $meta['icon'] ) ? sanitize_text_field( $meta['icon'] ) : '';
                $capability = isset( $meta['capability'] ) ? sanitize_text_field( $meta['capability'] ) : '';
                
                $submenus = array();
                if ( isset( $meta['submenus'] ) && is_array( $meta['submenus'] ) ) {
                    foreach ( $meta['submenus'] as $submenu_slug => $submenu_meta ) {
                        $submenu_slug = sanitize_key( $submenu_slug );
                        $submenu_enabled = isset( $submenu_meta['enabled'] ) && '1' === $submenu_meta['enabled'];
                        $submenu_label = isset( $submenu_meta['label'] ) ? sanitize_text_field( $submenu_meta['label'] ) : '';
                        $submenu_order = isset( $submenu_meta['order'] ) ? intval( $submenu_meta['order'] ) : 0;
                        $submenu_capability = isset( $submenu_meta['capability'] ) ? sanitize_text_field( $submenu_meta['capability'] ) : '';
                        
                        $submenus[ $submenu_slug ] = array(
                            'enabled' => $submenu_enabled ? 1 : 0,
                            'label'   => $submenu_label,
                            'order'   => $submenu_order,
                            'capability' => $submenu_capability,
                        );
                    }
                }
                
                $clean_menu[ $slug ] = array(
                    'enabled' => $enabled ? 1 : 0,
                    'order'   => $order,
                    'label'   => $label,
                    'icon'    => $icon,
                    'capability' => $capability,
                    'submenus' => $submenus,
                );
            }
            update_option( $this->menu_option_key, $clean_menu );
        }

        // 4. 색상 저장
        if ( isset( $data['jj_admin_menu_colors'] ) ) {
            $raw_colors = $data['jj_admin_menu_colors'];
            $clean_colors = array();
            foreach ( $this->get_default_admin_colors() as $key => $default_hex ) {
                $raw = isset( $raw_colors[ $key ] ) ? $raw_colors[ $key ] : '';
                $san = sanitize_hex_color( $raw );
                $clean_colors[ $key ] = $san ? $san : $default_hex;
            }
            update_option( $this->colors_option_key, $clean_colors );
        }

        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * AJAX: 업데이트 설정 저장
     */
    public function ajax_save_update_settings() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        $data = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : array();
        
        $settings = array(
            'auto_update_enabled'  => isset( $data['auto_update_enabled'] ) && '1' === $data['auto_update_enabled'],
            'update_channel'       => isset( $data['update_channel'] ) ? sanitize_key( $data['update_channel'] ) : 'stable',
            'beta_updates_enabled' => isset( $data['beta_updates_enabled'] ) && '1' === $data['beta_updates_enabled'],
            'send_app_logs'        => isset( $data['send_app_logs'] ) && '1' === $data['send_app_logs'],
            'send_error_logs'      => isset( $data['send_error_logs'] ) && '1' === $data['send_error_logs'],
        );

        update_option( 'jj_style_guide_update_settings', $settings );
        
        // WP 코어 자동 업데이트 동기화 (가능하다면)
        if ( $settings['auto_update_enabled'] ) {
            // 활성화 로직
        }

        wp_send_json_success( array( 'message' => '업데이트 설정이 저장되었습니다.' ) );
    }

    /**
     * AJAX: 라이센스 키 저장
     * [v22.0.1] AES-256-CBC 암호화 적용
     */
    public function ajax_save_license_key() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
        
        if ( empty( $license_key ) ) {
            wp_send_json_error( array( 'message' => __( '라이센스 키를 입력해 주세요.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 암호화하여 저장
        $encrypted_key = apply_filters( 'jj_license_key_encrypt', $license_key );
        update_option( 'jj_style_guide_license_key', $encrypted_key );

        wp_send_json_success( array( 'message' => __( '라이센스 키가 안전하게 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * AJAX: 라이센스 키 검증
     */
    public function ajax_verify_license_key() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $license_manager = class_exists( 'JJ_License_Manager' ) ? JJ_License_Manager::instance() : null;
        
        if ( ! $license_manager ) {
            wp_send_json_error( array( 'message' => __( '라이센스 관리자를 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $result = $license_manager->verify_license();
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => __( '라이센스 인증에 성공했습니다!', 'acf-css-really-simple-style-management-center' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( '라이센스 인증에 실패했습니다. 키를 확인해 주세요.', 'acf-css-really-simple-style-management-center' ) ) );
        }
    }

    /**
     * AJAX: 자가 진단 실행
     */
    public function ajax_run_self_test() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        // 자가 진단 클래스 로드 및 실행
        if ( ! class_exists( 'JJ_Self_Tester' ) ) {
            $tester_path = JJ_STYLE_GUIDE_PATH . 'tests/class-jj-self-tester.php';
            if ( file_exists( $tester_path ) ) {
                require_once $tester_path;
            }
        }

        $results = array();
        if ( class_exists( 'JJ_Self_Tester' ) ) {
            $tester = new JJ_Self_Tester();
            $results = $tester->run_tests();
        } else {
            $results[] = array(
                'test' => 'Self Tester Class',
                'status' => 'fail',
                'message' => 'Tester class not found'
            );
        }

        wp_send_json_success( array( 'results' => $results ) );
    }
}

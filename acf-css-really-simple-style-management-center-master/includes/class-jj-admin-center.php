<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ACF CSS 관리자 센터
 * - 스타일 센터 UI에 사용되는 텍스트/레이블 일부를 옵션으로 제어하기 위한 컨트롤 패널
 * - 상위/마스터 버전에서만 노출되도록 확장 가능하도록 설계
 * 
 * @version 3.7.0
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
        add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_center_assets' ) );
        // [Phase 4.5] 상단바(Admin Bar) 어디서나 접근 가능한 진입점 추가
        add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 100 );

        // 관리자 메뉴 커스터마이징 적용
        add_action( 'admin_menu', array( $this, 'apply_admin_menu_customizations' ), 999 );
        add_filter( 'custom_menu_order', '__return_true' );
        add_filter( 'menu_order', array( $this, 'filter_menu_order' ) );
        add_action( 'admin_head', array( $this, 'output_admin_menu_styles' ) );

        // AJAX 핸들러: 팔레트 데이터 가져오기
        add_action( 'wp_ajax_jj_get_palette_data', array( $this, 'ajax_get_palette_data' ) );
        
        // [v4.0.1 신규] Admin Center 설정 저장 AJAX 핸들러
        add_action( 'wp_ajax_jj_admin_center_save', array( $this, 'ajax_save_admin_center_settings' ) );
        
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
        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            $css_path = JJ_STYLE_GUIDE_PATH . 'assets/css/jj-admin-center.css';
            $js_path  = JJ_STYLE_GUIDE_PATH . 'assets/js/jj-admin-center.js';
            if ( file_exists( $css_path ) ) {
                $css_ver = filemtime( $css_path );
            }
            if ( file_exists( $js_path ) ) {
                $js_ver = filemtime( $js_path );
            }
        }

        wp_enqueue_style(
            'jj-admin-center',
            $css_url,
            array(),
            $css_ver
        );

        // [UI Polish] 타 플러그인 알림 숨김 (몰입형 환경)
        wp_add_inline_style( 'jj-admin-center', '
            .notice:not(.jj-notice), .error:not(.jj-notice), .updated:not(.jj-notice) { display: none !important; }
            #wpbody-content > .notice, #wpbody-content > .error, #wpbody-content > .updated { display: none !important; }
        ' );

        // wpColorPicker (색상 피커용)
        wp_enqueue_style( 'wp-color-picker' );

        // jQuery UI Sortable (드래그 앤 드롭용)
        // jQuery UI Core와 Mouse가 필요함 (의존성 명시)
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-mouse' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        
        // [Phase 8.1] 공통 유틸리티 먼저 로드
        wp_enqueue_script(
            'jj-common-utils',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-common-utils.js',
            array( 'jquery' ),
            $js_ver,
            true
        );
        
        wp_enqueue_script(
            'jj-admin-center',
            $js_url,
            array( 'jquery', 'wp-color-picker', 'jquery-ui-core', 'jquery-ui-mouse', 'jquery-ui-sortable', 'jj-common-utils' ),
            $js_ver,
            true
        );
        
        // [v5.0.3] 키보드 단축키 시스템 로드
        wp_enqueue_script( 'jj-keyboard-shortcuts', JJ_STYLE_GUIDE_URL . 'assets/js/jj-keyboard-shortcuts.js', array( 'jquery', 'jj-common-utils' ), defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0', true );
        
        // [v5.0.3] 툴팁 시스템 로드
        wp_enqueue_script( 'jj-tooltips', JJ_STYLE_GUIDE_URL . 'assets/js/jj-tooltips.js', array( 'jquery', 'jj-common-utils' ), defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0', true );

        // [Phase 8.3.2] 폼 UX 개선 (자동 저장, 유효성 검사)
        wp_enqueue_script(
            'jj-form-enhancer',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-form-enhancer.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0',
            true
        );

        // AJAX 파라미터 로컬라이즈
        wp_localize_script(
            'jj-admin-center',
            'jjAdminCenter',
            array(
                'ajax_url'           => admin_url( 'admin-ajax.php' ),
                'nonce'              => wp_create_nonce( 'jj_admin_center_save_action' ),
                'style_guide_nonce'  => wp_create_nonce( 'jj_style_guide_nonce' ), // export/import용
                'backup_nonce'       => wp_create_nonce( 'jj_style_guide_nonce' ), // [v3.7.0] 백업 관리용
                'locale'             => get_locale(), // [v5.0.3] 다국어 지원을 위한 로케일 전달
                'i18n'               => array(
                    'eyedropper_not_supported' => __( '브라우저에서 스포이드 기능을 지원하지 않습니다.', 'jj-style-guide' ),
                    'palette_load_error'       => __( '팔레트를 불러오는 중 오류가 발생했습니다.', 'jj-style-guide' ),
                    'no_colors_found'          => __( '선택한 팔레트에 사용 가능한 색상이 없습니다.', 'jj-style-guide' ),
                    'license_save_success'     => __( '라이센스 키가 저장되었습니다.', 'jj-style-guide' ),
                    'license_save_error'       => __( '라이센스 키 저장에 실패했습니다.', 'jj-style-guide' ),
                    'license_verify_success'   => __( '라이센스 검증이 완료되었습니다.', 'jj-style-guide' ),
                    'license_verify_error'     => __( '라이센스 검증에 실패했습니다.', 'jj-style-guide' ),
                ),
            )
        );
    }

    /**
     * "ACF CSS 관리자 센터" 서브메뉴 추가
     * - 기본적으로 옵션 페이지 아래에 배치 (향후 라이선스/권한에 따라 노출 제어 가능)
     */
    public function add_admin_menu_page() {
        $menu_title = ( class_exists( 'JJ_Edition_Controller' ) ? JJ_Edition_Controller::instance()->get_branding( 'menu_title' ) : __( 'ACF CSS 관리자 센터', 'jj-style-guide' ) );
        $page_title = ( class_exists( 'JJ_Edition_Controller' ) ? JJ_Edition_Controller::instance()->get_branding( 'full_name' ) : __( 'ACF CSS 관리자 센터', 'jj-style-guide' ) );

        add_options_page(
            $page_title,
            $menu_title,
            'manage_options',
            'jj-admin-center',
            array( $this, 'render_admin_center_page' )
        );
        
        // [Phase 4.5] 모양(Appearance) 및 도구(Tools) 메뉴 추가
        // 중요: 동일한 slug(jj-admin-center)로 등록해야 hook_suffix가 일관되고, CSS/JS가 정상 로드됩니다.
        add_theme_page( $page_title, $menu_title, 'manage_options', 'jj-admin-center', array( $this, 'render_admin_center_page' ) );
        add_management_page( $page_title, $menu_title, 'manage_options', 'jj-admin-center', array( $this, 'render_admin_center_page' ) );

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

        // 메인 메뉴: Admin Center
        $wp_admin_bar->add_node( array(
            'id'    => 'jj-style-guide',
            'title' => '<span class="ab-icon dashicons dashicons-art"></span> ' . $menu_title,
            'href'  => admin_url( 'options-general.php?page=jj-admin-center' ),
        ) );

        // 서브: 스타일 센터 (기존 스타일 편집 화면)
        $wp_admin_bar->add_node( array(
            'id'     => 'jj-style-guide-style-center',
            'parent' => 'jj-style-guide',
            'title'  => __( '스타일 센터', 'jj-style-guide' ),
            'href'   => admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ),
        ) );

        // 서브: Admin Center 주요 탭
        $wp_admin_bar->add_node( array(
            'id'     => 'jj-style-guide-admin-center',
            'parent' => 'jj-style-guide',
            'title'  => __( '관리자 센터', 'jj-style-guide' ),
            'href'   => admin_url( 'options-general.php?page=jj-admin-center' ),
        ) );

        // 서브: 실험실 (Labs)
        if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'basic' ) ) {
            $wp_admin_bar->add_node( array(
                'id'     => 'jj-style-guide-labs',
                'parent' => 'jj-style-guide',
                'title'  => __( '실험실 (Labs)', 'jj-style-guide' ),
                'href'   => admin_url( 'options-general.php?page=jj-labs-center' ),
            ) );
        }

        $wp_admin_bar->add_node( array(
            'id'     => 'jj-style-guide-colors',
            'parent' => 'jj-style-guide',
            'title'  => __( 'Admin Center: Colors', 'jj-style-guide' ),
            'href'   => admin_url( 'options-general.php?page=jj-admin-center#colors' ),
        ) );

        $wp_admin_bar->add_node( array(
            'id'     => 'jj-style-guide-visual',
            'parent' => 'jj-style-guide',
            'title'  => __( 'Admin Center: Visual', 'jj-style-guide' ),
            'href'   => admin_url( 'options-general.php?page=jj-admin-center#visual' ),
        ) );

        // 서브: Customizer 패널로 이동
        $wp_admin_bar->add_node( array(
            'id'     => 'jj-style-guide-customizer',
            'parent' => 'jj-style-guide',
            'title'  => __( '실시간 편집 (Customizer)', 'jj-style-guide' ),
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
            <div class="updated notice"><p><?php esc_html_e( '관리자 메뉴 / 상단바 색상이 기본값으로 되돌려졌습니다.', 'jj-style-guide' ); ?></p></div>
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
            <div class="updated notice"><p><?php esc_html_e( '관리자 센터 설정이 저장되었습니다.', 'jj-style-guide' ); ?></p></div>
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
                    <h2><?php esc_html_e( '빠른 이동', 'jj-style-guide' ); ?></h2>
                    <button type="button" class="jj-sidebar-toggle" aria-label="<?php esc_attr_e( '사이드바 토글', 'jj-style-guide' ); ?>">
                        <span class="dashicons dashicons-menu-alt"></span>
                    </button>
                </div>
                <ul class="jj-admin-center-sidebar-nav">
                    <li>
                        <a href="#general" data-tab="general" class="active">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php esc_html_e( 'General', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#admin-menu" data-tab="admin-menu">
                            <span class="dashicons dashicons-menu"></span>
                            <?php esc_html_e( 'Admin Menu', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#section-layout" data-tab="section-layout">
                            <span class="dashicons dashicons-layout"></span>
                            <?php esc_html_e( 'Section Layout', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#texts" data-tab="texts">
                            <span class="dashicons dashicons-text"></span>
                            <?php esc_html_e( 'Texts', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#colors" data-tab="colors">
                            <span class="dashicons dashicons-admin-appearance"></span>
                            <?php esc_html_e( 'Colors', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#visual" data-tab="visual">
                            <span class="dashicons dashicons-visibility"></span>
                            <?php esc_html_e( 'Visual', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#cloud" data-tab="cloud">
                            <span class="dashicons dashicons-cloud"></span>
                            <?php esc_html_e( 'Cloud', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#backup" data-tab="backup">
                            <span class="dashicons dashicons-backup"></span>
                            <?php esc_html_e( '백업 관리', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#updates" data-tab="updates">
                            <span class="dashicons dashicons-update"></span>
                            <?php esc_html_e( '업데이트', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#license" data-tab="license">
                            <span class="dashicons dashicons-admin-network"></span>
                            <?php esc_html_e( '라이센스', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#system-status" data-tab="system-status">
                            <span class="dashicons dashicons-info"></span>
                            <?php esc_html_e( '시스템 상태', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <?php if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'basic' ) ) : ?>
                    <li class="jj-sidebar-nav-divider" style="margin: 10px 0; border-top: 1px solid #333;"></li>
                    <li>
                        <a href="<?php echo esc_url( admin_url( 'options-general.php?page=jj-labs-center' ) ); ?>">
                            <span class="dashicons dashicons-beaker"></span>
                            <?php esc_html_e( '실험실 센터', 'jj-style-guide' ); ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="jj-admin-center-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h1 style="margin: 0;"><?php esc_html_e( 'ACF CSS 관리자 센터', 'jj-style-guide' ); ?></h1>
                        <a href="<?php echo esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ); ?>" 
                           class="button button-secondary" 
                           style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <?php esc_html_e( '스타일 센터', 'jj-style-guide' ); ?>
                        </a>
                    <a href="<?php echo esc_url( admin_url( 'options-general.php?page=jj-labs-center' ) ); ?>" 
                       class="button button-secondary" 
                       style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <?php esc_html_e( '실험실 센터', 'jj-style-guide' ); ?>
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
                            <span class="dashicons dashicons-cart" style="margin-top: 4px;"></span> <?php esc_html_e( '업그레이드하기', 'jj-style-guide' ); ?>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <div class="jj-header-actions">
                    <!-- [v3.7.0 '신규'] 관리자 센터 설정 내보내기/불러오기 (헤더) -->
                    <button type="button" class="button button-secondary jj-export-settings" data-center="admin-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px; margin-right: 8px;">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php esc_html_e( '내보내기', 'jj-style-guide' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-settings" data-center="admin-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span> <?php esc_html_e( '불러오기', 'jj-style-guide' ); ?>
                    </button>
                </div>
            </div>

            <!-- 탭 네비게이션 -->
            <ul class="jj-admin-center-tabs">
                <li data-tab="general" class="active">
                    <a href="#general"><?php esc_html_e( 'General', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="admin-menu">
                    <a href="#admin-menu"><?php esc_html_e( 'Admin Menu', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="section-layout">
                    <a href="#section-layout"><?php esc_html_e( 'Section Layout', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="texts">
                    <a href="#texts"><?php esc_html_e( 'Texts', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="colors">
                    <a href="#colors"><?php esc_html_e( 'Colors', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="visual">
                    <a href="#visual"><?php esc_html_e( 'Visual', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="cloud">
                    <a href="#cloud"><?php esc_html_e( 'Cloud', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="backup">
                    <a href="#backup"><?php esc_html_e( '백업 관리', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="tools">
                    <a href="#tools"><?php esc_html_e( '도구', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="updates">
                    <a href="#updates"><?php esc_html_e( '업데이트', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="license">
                    <a href="#license"><?php esc_html_e( '라이센스', 'jj-style-guide' ); ?></a>
                </li>
                <li data-tab="system-status">
                    <a href="#system-status"><?php esc_html_e( '시스템 상태', 'jj-style-guide' ); ?></a>
                </li>
            </ul>

            <form method="post" id="jj-admin-center-form">
                <?php wp_nonce_field( 'jj_admin_center_save_action', 'jj_admin_center_nonce' ); ?>

                <!-- General 탭 -->
                <?php include JJ_STYLE_GUIDE_PATH . 'includes/admin/views/tabs/tab-general.php'; ?>

                                                <!-- Admin Menu 탭 -->
                <?php 
                $tab_admin_menu = JJ_STYLE_GUIDE_PATH . 'includes/admin/views/tabs/tab-admin-menu.php';
                if ( file_exists( $tab_admin_menu ) ) {
                    include $tab_admin_menu; 
                } else {
                    echo '<div class="notice notice-error"><p>탭 파일을 찾을 수 없습니다: tab-admin-menu.php</p></div>';
                }
                ?>

<!-- [v3.7.0 '신규'] 관리자 센터 설정 내보내기/불러오기 (푸터) -->
            <!-- Tools 탭 -->
                <?php 
                $tab_tools = JJ_STYLE_GUIDE_PATH . 'includes/admin/views/tabs/tab-tools.php';
                if ( file_exists( $tab_tools ) ) {
                    include $tab_tools;
                }
                ?>
                
            <div class="jj-admin-center-footer"  style="margin-top: 30px; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <button type="button" class="button button-secondary jj-export-settings" data-center="admin-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px; margin-right: 8px;">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span> <?php esc_html_e( '내보내기', 'jj-style-guide' ); ?>
                    </button>
                    <button type="button" class="button button-secondary jj-import-settings" data-center="admin-center" style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                        <span class="dashicons dashicons-upload" style="margin-top: 4px;"></span> <?php esc_html_e( '불러오기', 'jj-style-guide' ); ?>
                    </button>
                </div>
            </div>
        </div>
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
                'label'  => __( '1. 팔레트 시스템', 'jj-style-guide' ),
                'order'  => 10,
                'enabled'=> 1,
                'tabs'   => array( // [v5.0.0] 탭 활성화/비활성화
                    'brand'       => array( 'label' => __( '브랜드 팔레트', 'jj-style-guide' ), 'enabled' => 1 ),
                    'system'      => array( 'label' => __( '시스템 팔레트', 'jj-style-guide' ), 'enabled' => 1 ),
                    'alternative' => array( 'label' => __( '얼터너티브 팔레트', 'jj-style-guide' ), 'enabled' => 1 ),
                    'another'     => array( 'label' => __( '어나더 팔레트', 'jj-style-guide' ), 'enabled' => 1 ),
                    'temp-palette'=> array( 'label' => __( '임시 팔레트', 'jj-style-guide' ), 'enabled' => 1 ),
                ),
            ),
            'typography' => array(
                'label'  => __( '2. 타이포그래피', 'jj-style-guide' ),
                'order'  => 20,
                'enabled'=> 1,
                'tabs'   => array(), // 탭 없음
            ),
            'buttons'    => array(
                'label'  => __( '3. 버튼', 'jj-style-guide' ),
                'order'  => 30,
                'enabled'=> 1,
                'tabs'   => array( // [v5.0.0] 탭 활성화/비활성화
                    'btn-primary'   => array( 'label' => __( 'Primary Button', 'jj-style-guide' ), 'enabled' => 1 ),
                    'btn-secondary' => array( 'label' => __( 'Secondary Button', 'jj-style-guide' ), 'enabled' => 1 ),
                    'btn-text'      => array( 'label' => __( 'Text / Outline Button', 'jj-style-guide' ), 'enabled' => 1 ),
                ),
            ),
            'forms'      => array(
                'label'  => __( '4. 폼 & 필드', 'jj-style-guide' ),
                'order'  => 40,
                'enabled'=> 1,
                'tabs'   => array( // [v5.0.0] 탭 활성화/비활성화
                    'form-label' => array( 'label' => __( '라벨 (Labels)', 'jj-style-guide' ), 'enabled' => 1 ),
                    'form-field' => array( 'label' => __( '입력 필드 (Fields)', 'jj-style-guide' ), 'enabled' => 1 ),
                ),
            ),
        );

        /**
         * 섹션 레이아웃 기본값을 필터링할 수 있습니다.
         */
        return apply_filters( 'jj_style_guide_default_sections_layout', $defaults );
    }

    /**
     * 저장된 섹션 레이아웃 + 기본값 병합
     *
     * @return array
     */
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
                } else {
                    $menu[ $index ][1] = $meta['capability'];
                }
            }
            
            // [v3.7.0 '신규'] 서브메뉴 편집 적용
            if ( isset( $meta['submenus'] ) && is_array( $meta['submenus'] ) && isset( $submenu[ $slug ] ) ) {
                $submenu_items = array();
                
                // 서브메뉴 정렬 및 필터링
                foreach ( $submenu[ $slug ] as $submenu_index => $submenu_item ) {
                    $submenu_slug = isset( $submenu_item[2] ) ? sanitize_key( $submenu_item[2] ) : '';
                    if ( empty( $submenu_slug ) || ! isset( $meta['submenus'][ $submenu_slug ] ) ) {
                        // 커스터마이징되지 않은 서브메뉴는 그대로 유지
                        $submenu_items[ $submenu_index ] = $submenu_item;
                        continue;
                    }
                    
                    $submenu_meta = $meta['submenus'][ $submenu_slug ];
                    
                    // 서브메뉴 표시 여부
                    if ( isset( $submenu_meta['enabled'] ) && ! $submenu_meta['enabled'] ) {
                        continue; // 이 서브메뉴는 숨김
                    }
                    
                    // 서브메뉴 권한 확인
                    $submenu_capability = ! empty( $submenu_meta['capability'] ) ? $submenu_meta['capability'] : ( isset( $submenu_item[1] ) ? $submenu_item[1] : 'read' );
                    if ( ! current_user_can( $submenu_capability ) ) {
                        continue; // 권한이 없으면 숨김
                    }
                    
                    // 서브메뉴 레이블 변경
                    $submenu_item_copy = $submenu_item;
                    if ( ! empty( $submenu_meta['label'] ) ) {
                        $submenu_item_copy[0] = $submenu_meta['label'];
                    }
                    
                    // 서브메뉴 권한 변경
                    if ( ! empty( $submenu_meta['capability'] ) ) {
                        $submenu_item_copy[1] = $submenu_meta['capability'];
                    }
                    
                    // 서브메뉴 순서 저장 (order 값을 인덱스로 사용하기 위해 임시 저장)
                    $submenu_order = isset( $submenu_meta['order'] ) ? intval( $submenu_meta['order'] ) : $submenu_index;
                    $submenu_items[ $submenu_order * 1000 + $submenu_index ] = $submenu_item_copy; // 중복 방지를 위해 인덱스 추가
                }
                
                // 순서 정렬
                ksort( $submenu_items );
                $submenu[ $slug ] = array_values( $submenu_items );
            }
        }
    }

    /**
     * menu_order 필터에서 순서 재정의
     *
     * @param array $menu_order
     * @return array
     */
    public function filter_menu_order( $menu_order ) {
        $layout = $this->get_menu_layout();
        if ( empty( $layout ) || ! is_array( $menu_order ) ) {
            return $menu_order;
        }

        $order_map = array();
        foreach ( $menu_order as $index => $slug ) {
            $key = sanitize_key( $slug );
            $order_map[ $slug ] = isset( $layout[ $key ]['order'] ) ? (int) $layout[ $key ]['order'] : 0;
        }

        usort(
            $menu_order,
            function ( $a, $b ) use ( $order_map ) {
                $a_order = isset( $order_map[ $a ] ) ? $order_map[ $a ] : 0;
                $b_order = isset( $order_map[ $b ] ) ? $order_map[ $b ] : 0;

                if ( $a_order === $b_order ) {
                    return 0;
                }

                return ( $a_order < $b_order ) ? -1 : 1;
            }
        );

        return $menu_order;
    }

    /**
     * 관리자 메뉴/상단바 색상 커스터마이징용 CSS 출력 (기본 버전: 색상 상수, 추후 옵션화 가능)
     */
    public function output_admin_menu_styles() {
        // 향후 색상 옵션을 별도 키로 분리할 수 있도록, 현재는 최소한의 구조만 출력
        $colors = apply_filters(
            'jj_style_guide_admin_menu_colors',
            $this->get_admin_menu_colors()
        );
        ?>
        <style id="jj-admin-menu-custom-colors">
            #adminmenu, #adminmenu .wp-submenu, #adminmenuback, #adminmenuwrap {
                background-color: <?php echo esc_html( $colors['sidebar_bg'] ); ?>;
            }
            #adminmenu a {
                color: <?php echo esc_html( $colors['sidebar_text'] ); ?>;
            }
            #adminmenu .wp-has-current-submenu > a.menu-top,
            #adminmenu .current a.menu-top,
            #adminmenu .wp-menu-open > a.menu-top {
                background-color: <?php echo esc_html( $colors['sidebar_bg_active'] ); ?>;
                color: <?php echo esc_html( $colors['sidebar_text_active'] ); ?>;
            }
            #adminmenu a:hover,
            #adminmenu li.menu-top:hover,
            #adminmenu li.opensub > a.menu-top,
            #adminmenu li > a.menu-top:focus {
                background-color: <?php echo esc_html( $colors['sidebar_bg_hover'] ); ?>;
                color: <?php echo esc_html( $colors['sidebar_text_hover'] ); ?>;
            }
            #wpadminbar {
                background: <?php echo esc_html( $colors['topbar_bg'] ); ?>;
            }
            #wpadminbar .ab-item,
            #wpadminbar a.ab-item,
            #wpadminbar > #wp-toolbar span.ab-label,
            #wpadminbar > #wp-toolbar span.noticon {
                color: <?php echo esc_html( $colors['topbar_text'] ); ?>;
            }
            #wpadminbar .ab-item:hover,
            #wpadminbar .ab-item:focus,
            #wpadminbar a.ab-item:focus,
            #wpadminbar a.ab-item:hover {
                color: <?php echo esc_html( $colors['topbar_text_hover'] ); ?>;
            }
        </style>
        <?php
    }

    /**
     * [Phase 8.2] 통합 AJAX 보안 검증 헬퍼
     * 
     * @param string $ajax_action AJAX 액션명
     * @param string $nonce_key Nonce 키
     * @param string $capability 필요한 권한
     * @return bool 검증 성공 여부
     */
    private function verify_ajax_security( $ajax_action, $nonce_key, $capability = 'manage_options' ) {
        if ( class_exists( 'JJ_Security_Hardener' ) ) {
            return JJ_Security_Hardener::verify_ajax_request( $ajax_action, $nonce_key, $capability );
        } else {
            // 폴백
            check_ajax_referer( $nonce_key, 'security' );
            if ( ! current_user_can( $capability ) ) {
                wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
                return false;
            }
            return true;
        }
    }

    /**
     * AJAX 핸들러: 팔레트 데이터 가져오기
     * [v3.8.0] 관리자 센터 Colors 탭에서 팔레트 색상 불러오기용
     * [Phase 8.2] 보안 강화
     */
    public function ajax_get_palette_data() {
        if ( ! $this->verify_ajax_security( 'jj_get_palette_data', 'jj_admin_center_save_action', 'manage_options' ) ) {
            return;
        }

        // 팔레트 데이터 가져오기
        $hub_options = get_option( JJ_STYLE_GUIDE_OPTIONS_KEY, array() );
        $temp_options = get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );

        $palettes = array();

        // 브랜드 팔레트
        if ( ! empty( $hub_options['palettes']['brand'] ) ) {
            $palettes['brand'] = array(
                'name' => __( '브랜드 팔레트', 'jj-style-guide' ),
                'colors' => $hub_options['palettes']['brand'],
            );
        }

        // 시스템 팔레트
        if ( ! empty( $hub_options['palettes']['system'] ) ) {
            $palettes['system'] = array(
                'name' => __( '시스템 팔레트', 'jj-style-guide' ),
                'colors' => $hub_options['palettes']['system'],
            );
        }

        // 얼터너티브 팔레트
        if ( ! empty( $hub_options['palettes']['alternative'] ) ) {
            $palettes['alternative'] = array(
                'name' => __( '얼터너티브 팔레트', 'jj-style-guide' ),
                'colors' => $hub_options['palettes']['alternative'],
            );
        }

        // 어나더 팔레트
        if ( ! empty( $hub_options['palettes']['another'] ) ) {
            $palettes['another'] = array(
                'name' => __( '어나더 팔레트', 'jj-style-guide' ),
                'colors' => $hub_options['palettes']['another'],
            );
        }

        // 임시 팔레트
        if ( ! empty( $temp_options['palettes']['brand'] ) ) {
            $palettes['temporary'] = array(
                'name' => __( '임시 팔레트', 'jj-style-guide' ),
                'colors' => $temp_options['palettes']['brand'],
            );
        }

        wp_send_json_success( array( 'palettes' => $palettes ) );
    }

    /**
     * [v3.8.0 신규] AJAX: 라이센스 키 저장
     * [Phase 8.2] 보안 강화
     */
    public function ajax_save_license_key() {
        if ( ! $this->verify_ajax_security( 'jj_save_license_key', 'jj_license_save_action', 'manage_options' ) ) {
            return;
        }
        
        $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
        
        if ( empty( $license_key ) ) {
            wp_send_json_error( array( 'message' => __( '라이센스 키를 입력하세요.', 'jj-style-guide' ) ) );
        }
        
        $license_manager = null;
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
            }
        }
        
        if ( ! $license_manager ) {
            wp_send_json_error( array( 'message' => __( '라이센스 관리자를 로드할 수 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $result = $license_manager->save_license_key( $license_key );
        
        if ( $result['success'] ) {
            // 라이센스 타입 상수 업데이트 시도 (이미 정의되어 있으면 업데이트 불가)
            if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
                // 상수는 재정의할 수 없으므로 옵션에 저장
                update_option( 'jj_style_guide_license_type_override', $result['status']['type'] );
            }
            
            wp_send_json_success( array(
                'message' => $result['message'],
                'status' => $result['status'],
            ) );
        } else {
            wp_send_json_error( array( 'message' => $result['message'] ) );
        }
    }

    /**
     * [v3.8.0 신규] AJAX: 라이센스 키 검증
     */
    public function ajax_verify_license_key() {
        check_ajax_referer( 'jj_license_save_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $license_manager = null;
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
            }
        }
        
        if ( ! $license_manager ) {
            wp_send_json_error( array( 'message' => __( '라이센스 관리자를 로드할 수 없습니다.', 'jj-style-guide' ) ) );
        }
        
        // 강제 온라인 검증
        $status = $license_manager->verify_license( true );
        
        wp_send_json_success( array(
            'status' => $status,
            'message' => $status['message'],
        ) );
    }

    /**
     * [v5.1.7 신규] AJAX: 업데이트 설정 저장
     */
    public function ajax_save_update_settings() {
        check_ajax_referer( 'jj_admin_center_save_action', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $raw_update_settings = isset( $_POST['update_settings'] ) && is_array( $_POST['update_settings'] ) ? wp_unslash( $_POST['update_settings'] ) : array();
        $clean_update_settings = array(
            'auto_update_enabled' => isset( $raw_update_settings['auto_update_enabled'] ) && '1' === $raw_update_settings['auto_update_enabled'],
            'update_channel' => isset( $raw_update_settings['update_channel'] ) ? sanitize_text_field( $raw_update_settings['update_channel'] ) : 'stable',
            'beta_updates_enabled' => isset( $raw_update_settings['beta_updates_enabled'] ) && '1' === $raw_update_settings['beta_updates_enabled'],
            'send_app_logs' => isset( $raw_update_settings['send_app_logs'] ) && '1' === $raw_update_settings['send_app_logs'],
            'send_error_logs' => isset( $raw_update_settings['send_error_logs'] ) && '1' === $raw_update_settings['send_error_logs'],
        );
        
        // Partner/Master는 모든 로그를 반드시 전송 (내부용)
        $is_master_version = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $is_master_version = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_master_version = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        }
        if ( $is_master_version ) {
            $clean_update_settings['send_app_logs'] = true;
            $clean_update_settings['send_error_logs'] = true;
        }
        
        // 업데이트 채널 유효성 검사
        // NOTE: build/deployment 채널과 UI를 맞춤 (stable/beta/staging). test/dev는 레거시 호환용으로만 허용.
        $allowed_channels = array( 'stable', 'beta', 'staging', 'test', 'dev' );
        if ( ! in_array( $clean_update_settings['update_channel'], $allowed_channels, true ) ) {
            $clean_update_settings['update_channel'] = 'stable';
        }

        // 채널이 stable이 아니면(=실험 채널) beta flag는 자동으로 true로 맞춤 (구버전 로직 호환)
        if ( 'stable' !== $clean_update_settings['update_channel'] ) {
            $clean_update_settings['beta_updates_enabled'] = true;
        }
        
        update_option( 'jj_style_guide_update_settings', $clean_update_settings );
        
        // [v5.6.0] Visual Command Center 설정 저장
        if ( isset( $_POST['visual_options'] ) && is_array( $_POST['visual_options'] ) ) {
            // 간단한 sanitize (실제로는 더 정교해야 함)
            $visual_options = array_map( 'sanitize_text_field', wp_unslash( $_POST['visual_options'] ) );
            update_option( 'jj_style_guide_visual_options', $visual_options );
        }
        
        // 자동 업데이트 활성화/비활성화 (메인 파일 경로가 다를 수 있음 고려)
        $possible_plugin_files = array(
            'acf-css-really-simple-style-management-center/acf-css-really-simple-style-guide.php',
            'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php',
            plugin_basename( JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php' )
        );
        $plugin_file = plugin_basename( JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php' );

        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
        
        if ( $clean_update_settings['auto_update_enabled'] ) {
            if ( ! in_array( $plugin_file, $auto_updates, true ) ) {
                $auto_updates[] = $plugin_file;
                update_site_option( 'auto_update_plugins', $auto_updates );
            }
        } else {
            // 비활성화 시 모든 가능한 경로 제거 (안전장치)
            foreach ( $possible_plugin_files as $file ) {
                if ( ( $key = array_search( $file, $auto_updates, true ) ) !== false ) {
                    unset( $auto_updates[$key] );
                }
            }
            // 현재 감지된 파일도 제거
            if ( ( $key = array_search( $plugin_file, $auto_updates, true ) ) !== false ) {
                unset( $auto_updates[$key] );
            }
            update_site_option( 'auto_update_plugins', $auto_updates );
        }
        
        wp_send_json_success( array( 
            'message' => __( '업데이트 설정이 저장되었습니다.', 'jj-style-guide' ),
            'settings' => $clean_update_settings,
        ) );
    }

    /**
     * [v8.x] AJAX: 플러그인별 자동 업데이트 토글
     *
     * - WP 코어의 auto_update_plugins(site option)를 직접 업데이트합니다.
     * - 플러그인 목록 화면의 토글과 동일한 효과를 가집니다.
     */
    /**
     * [Phase 8.2] 보안 강화
     */
    public function ajax_toggle_auto_update_plugin() {
        if ( ! $this->verify_ajax_security( 'jj_toggle_auto_update_plugin', 'jj_admin_center_save_action', 'manage_options' ) ) {
            return;
        }

        $plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) : '';
        $enabled_raw = isset( $_POST['enabled'] ) ? wp_unslash( $_POST['enabled'] ) : null;

        if ( '' === $plugin ) {
            wp_send_json_error( array( 'message' => __( '플러그인 정보가 없습니다.', 'jj-style-guide' ) ) );
        }

        $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
        $is_enabled = in_array( $plugin, $auto_updates, true );

        // enabled 파라미터가 오면 그 값으로, 없으면 토글
        $target = null;
        if ( null !== $enabled_raw ) {
            $target = ( '1' === (string) $enabled_raw || 'true' === (string) $enabled_raw );
        } else {
            $target = ! $is_enabled;
        }

        if ( $target ) {
            if ( ! $is_enabled ) {
                $auto_updates[] = $plugin;
                $auto_updates = array_values( array_unique( $auto_updates ) );
                update_site_option( 'auto_update_plugins', $auto_updates );
            }
        } else {
            if ( ( $key = array_search( $plugin, $auto_updates, true ) ) !== false ) {
                unset( $auto_updates[ $key ] );
                $auto_updates = array_values( $auto_updates );
                update_site_option( 'auto_update_plugins', $auto_updates );
            }
        }

        // 코어 플러그인인 경우 내부 옵션도 동기화 (UX 일관성)
        $core_plugin_file = plugin_basename( JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php' );
        if ( $plugin === $core_plugin_file ) {
            $update_settings = get_option( 'jj_style_guide_update_settings', array() );
            if ( ! is_array( $update_settings ) ) {
                $update_settings = array();
            }
            $update_settings['auto_update_enabled'] = $target;
            update_option( 'jj_style_guide_update_settings', $update_settings );
        }

        wp_send_json_success(
            array(
                'plugin'  => $plugin,
                'enabled' => (bool) $target,
            )
        );
    }

    /**
     * [v8.x] AJAX: Suite 전체 업데이트 상태 갱신 (WP 코어 update_plugins transient)
     *
     * - delete_site_transient('update_plugins') 후 wp_update_plugins()를 호출하여
     *   Updates 탭의 "업데이트 가능" 표시를 최신으로 만듭니다.
     */
    /**
     * [Phase 8.2] 보안 강화
     */
    public function ajax_suite_refresh_updates() {
        if ( ! $this->verify_ajax_security( 'jj_suite_refresh_updates', 'jj_admin_center_save_action', 'update_plugins' ) ) {
            return;
        }

        // 캐시 삭제
        delete_site_transient( 'update_plugins' );

        // 업데이트 체크 강제 실행
        if ( ! function_exists( 'wp_update_plugins' ) && defined( 'ABSPATH' ) ) {
            $update_php = ABSPATH . 'wp-admin/includes/update.php';
            if ( file_exists( $update_php ) ) {
                require_once $update_php;
            }
        }
        if ( function_exists( 'wp_update_plugins' ) ) {
            wp_update_plugins();
        }

        $updates_obj = get_site_transient( 'update_plugins' );
        $last_checked = ( is_object( $updates_obj ) && isset( $updates_obj->last_checked ) ) ? (int) $updates_obj->last_checked : 0;
        $resp = ( is_object( $updates_obj ) && isset( $updates_obj->response ) && is_array( $updates_obj->response ) ) ? $updates_obj->response : array();
        $checked = ( is_object( $updates_obj ) && isset( $updates_obj->checked ) && is_array( $updates_obj->checked ) ) ? $updates_obj->checked : array();
        $next_check = function_exists( 'wp_next_scheduled' ) ? (int) wp_next_scheduled( 'wp_update_plugins' ) : 0;

        wp_send_json_success(
            array(
                'message' => __( '업데이트 정보를 갱신했습니다.', 'jj-style-guide' ),
                'last_checked' => $last_checked,
                'next_check' => $next_check,
                'updates_count' => is_array( $resp ) ? count( $resp ) : 0,
                'checked_count' => is_array( $checked ) ? count( $checked ) : 0,
            )
        );
    }

    /**
     * [v5.1.7 신규] AJAX: 지금 업데이트 확인
     */
    /**
     * [Phase 8.2] 보안 강화
     */
    public function ajax_check_updates_now() {
        if ( ! $this->verify_ajax_security( 'jj_check_updates_now', 'jj_admin_center_save_action', 'update_plugins' ) ) {
            return;
        }
        check_ajax_referer( 'jj_admin_center_save_action', 'security' );
        
        if ( ! current_user_can( 'update_plugins' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        // 캐시 삭제하여 강제로 업데이트 체크
        delete_site_transient( 'update_plugins' );
        
        // 업데이트 정보 가져오기
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php';
            
            $plugin_slug = 'advanced-custom-style-manage-center';
            $current_version = JJ_STYLE_GUIDE_VERSION;
            $license_key = get_option( 'jj_style_guide_license_key', '' );
            $license_type = defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ? JJ_STYLE_GUIDE_LICENSE_TYPE : 'FREE';
            
            if ( class_exists( 'JJ_Plugin_Updater' ) ) {
                $updater = new JJ_Plugin_Updater(
                    JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php',
                    $plugin_slug,
                    'JJ\'s Center of Style Setting',
                    $current_version,
                    $license_type
                );
                
                // 업데이트 정보 가져오기
                $update_info = $updater->get_update_info();
                
                if ( $update_info && isset( $update_info['new_version'] ) ) {
                    if ( version_compare( $current_version, $update_info['new_version'], '<' ) ) {
                        wp_send_json_success( array(
                            'has_update' => true,
                            'current_version' => $current_version,
                            'new_version' => $update_info['new_version'],
                            'message' => sprintf( __( '새 버전 %s이(가) 사용 가능합니다.', 'jj-style-guide' ), $update_info['new_version'] ),
                        ) );
                    } else {
                        wp_send_json_success( array(
                            'has_update' => false,
                            'message' => __( '최신 버전을 사용 중입니다.', 'jj-style-guide' ),
                        ) );
                    }
                } else {
                    wp_send_json_error( array( 'message' => __( '업데이트 정보를 가져올 수 없습니다.', 'jj-style-guide' ) ) );
                }
            } else {
                wp_send_json_error( array( 'message' => __( '업데이터 클래스를 로드할 수 없습니다.', 'jj-style-guide' ) ) );
            }
        } else {
            wp_send_json_error( array( 'message' => __( '업데이터 파일을 찾을 수 없습니다.', 'jj-style-guide' ) ) );
        }
    }

    /**
     * [v4.0.1 신규] AJAX: Admin Center 설정 저장
     * [Phase 8.2] 보안 강화
     */
    public function ajax_save_admin_center_settings() {
        if ( ! $this->verify_ajax_security( 'jj_admin_center_save', 'jj_admin_center_save_action', 'manage_options' ) ) {
            return;
        }

        try {
            // [Phase 5.2] Webhook/자동화용 변경 전 스냅샷
            $old_snapshot = array();
            if ( function_exists( 'get_option' ) ) {
                $old_snapshot = array(
                    'admin_texts'        => get_option( $this->option_key, array() ),
                    'section_layout'     => get_option( $this->sections_option_key, array() ),
                    'admin_menu_layout'  => get_option( $this->menu_option_key, array() ),
                    'admin_menu_colors'  => get_option( $this->colors_option_key, array() ),
                    'update_settings'    => get_option( 'jj_style_guide_update_settings', array() ),
                    'visual_options'     => get_option( 'jj_style_guide_visual_options', array() ),
                    'webhooks'           => get_option( 'jj_style_guide_webhooks', array() ),
                );
            }

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

            // 4) 업데이트 설정 저장
            if ( isset( $_POST['jj_update_settings'] ) && is_array( $_POST['jj_update_settings'] ) ) {
                $raw_update_settings = wp_unslash( $_POST['jj_update_settings'] );
                $clean_update_settings = array(
                    'auto_update_enabled' => isset( $raw_update_settings['auto_update_enabled'] ) && '1' === $raw_update_settings['auto_update_enabled'],
                    'update_channel' => isset( $raw_update_settings['update_channel'] ) ? sanitize_text_field( $raw_update_settings['update_channel'] ) : 'stable',
                    'beta_updates_enabled' => isset( $raw_update_settings['beta_updates_enabled'] ) && '1' === $raw_update_settings['beta_updates_enabled'],
                    'send_app_logs' => isset( $raw_update_settings['send_app_logs'] ) && '1' === $raw_update_settings['send_app_logs'],
                    'send_error_logs' => isset( $raw_update_settings['send_error_logs'] ) && '1' === $raw_update_settings['send_error_logs'],
                );
                
                // 마스터 버전은 모든 로그를 반드시 전송
                $is_master_version = false;
                    if ( class_exists( 'JJ_Edition_Controller' ) ) {
                        $is_master_version = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                    }
                if ( $is_master_version ) {
                    $clean_update_settings['send_app_logs'] = true;
                    $clean_update_settings['send_error_logs'] = true;
                }
                
                // 업데이트 채널 유효성 검사
                $allowed_channels = array( 'stable', 'beta', 'test', 'dev' );
                if ( ! in_array( $clean_update_settings['update_channel'], $allowed_channels, true ) ) {
                    $clean_update_settings['update_channel'] = 'stable';
                }
                
                update_option( 'jj_style_guide_update_settings', $clean_update_settings );
            }

            // [v5.6.0] Visual Command Center 설정 저장
            if ( isset( $_POST['visual_options'] ) && is_array( $_POST['visual_options'] ) ) {
                $visual_options = array_map( 'sanitize_text_field', wp_unslash( $_POST['visual_options'] ) );
                update_option( 'jj_style_guide_visual_options', $visual_options );
            }

            // [Phase 5.2] Webhook 설정 저장
            if ( isset( $_POST['jj_webhooks'] ) && is_array( $_POST['jj_webhooks'] ) ) {
                $raw_webhooks = wp_unslash( $_POST['jj_webhooks'] );
                $existing_webhooks = function_exists( 'get_option' ) ? get_option( 'jj_style_guide_webhooks', array() ) : array();
                if ( ! is_array( $existing_webhooks ) ) {
                    $existing_webhooks = array();
                }

                $enabled = isset( $raw_webhooks['enabled'] ) && '1' === $raw_webhooks['enabled'];

                $payload_mode = ( isset( $raw_webhooks['payload_mode'] ) && 'full' === $raw_webhooks['payload_mode'] ) ? 'full' : 'minimal';
                $timeout_seconds = isset( $raw_webhooks['timeout_seconds'] ) ? (int) $raw_webhooks['timeout_seconds'] : 5;
                $timeout_seconds = max( 1, min( 30, $timeout_seconds ) );

                // endpoints: textarea (줄바꿈)
                $endpoints_text = isset( $raw_webhooks['endpoints'] ) ? (string) $raw_webhooks['endpoints'] : '';
                $lines = preg_split( "/\\r\\n|\\r|\\n/", $endpoints_text );
                $endpoints = array();
                if ( is_array( $lines ) ) {
                    foreach ( $lines as $line ) {
                        $line = trim( (string) $line );
                        if ( '' === $line ) {
                            continue;
                        }
                        $url = function_exists( 'esc_url_raw' ) ? esc_url_raw( $line ) : $line;
                        if ( $url ) {
                            $endpoints[] = $url;
                        }
                    }
                }
                $endpoints = array_values( array_unique( $endpoints ) );

                // events
                $allowed_events = array( 'style_settings_updated', 'admin_center_updated' );
                $events = array();
                if ( isset( $raw_webhooks['events'] ) && is_array( $raw_webhooks['events'] ) ) {
                    foreach ( $raw_webhooks['events'] as $evt ) {
                        $evt = sanitize_text_field( (string) $evt );
                        if ( in_array( $evt, $allowed_events, true ) ) {
                            $events[] = $evt;
                        }
                    }
                }
                $events = array_values( array_unique( $events ) );

                // secret: 비워두면 기존 유지, clear_secret 체크 시 초기화
                $secret = isset( $existing_webhooks['secret'] ) ? (string) $existing_webhooks['secret'] : '';
                if ( isset( $raw_webhooks['clear_secret'] ) && '1' === $raw_webhooks['clear_secret'] ) {
                    $secret = '';
                } elseif ( isset( $raw_webhooks['secret'] ) ) {
                    $incoming_secret = trim( (string) $raw_webhooks['secret'] );
                    if ( '' !== $incoming_secret ) {
                        $secret = sanitize_text_field( $incoming_secret );
                    }
                }

                $clean_webhooks = array(
                    'enabled'         => $enabled,
                    'endpoints'       => $endpoints,
                    'secret'          => $secret,
                    'events'          => $events,
                    'payload_mode'    => $payload_mode,
                    'timeout_seconds' => $timeout_seconds,
                );

                update_option( 'jj_style_guide_webhooks', $clean_webhooks );
            }
            
            // 5) 관리자 메뉴/상단바 색상 저장
            $raw_colors   = isset( $_POST['jj_admin_menu_colors'] ) && is_array( $_POST['jj_admin_menu_colors'] ) ? wp_unslash( $_POST['jj_admin_menu_colors'] ) : array();
            $clean_colors = array();
            foreach ( $this->get_default_admin_colors() as $key => $default_hex ) {
                $raw = isset( $raw_colors[ $key ] ) ? $raw_colors[ $key ] : '';
                $san = sanitize_hex_color( $raw );
                $clean_colors[ $key ] = $san ? $san : $default_hex;
            }
            update_option( $this->colors_option_key, $clean_colors );

            // [Phase 5.2] Webhook/자동화용 Admin Center 변경 이벤트 트리거
            if ( function_exists( 'do_action' ) && function_exists( 'get_option' ) ) {
                $new_snapshot = array(
                    'admin_texts'        => get_option( $this->option_key, array() ),
                    'section_layout'     => get_option( $this->sections_option_key, array() ),
                    'admin_menu_layout'  => get_option( $this->menu_option_key, array() ),
                    'admin_menu_colors'  => get_option( $this->colors_option_key, array() ),
                    'update_settings'    => get_option( 'jj_style_guide_update_settings', array() ),
                    'visual_options'     => get_option( 'jj_style_guide_visual_options', array() ),
                    'webhooks'           => get_option( 'jj_style_guide_webhooks', array() ),
                );
                do_action( 'jj_style_guide_admin_center_updated', $new_snapshot, $old_snapshot, 'admin_center_ajax' );
            }

            wp_send_json_success( array( 'message' => __( '관리자 센터 설정이 저장되었습니다.', 'jj-style-guide' ) ) );
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'message' => __( '저장 중 오류가 발생했습니다: ', 'jj-style-guide' ) . $e->getMessage() ) );
        }
    }

    /**
     * [Phase 6] AJAX: 자가 진단 실행
     * [Phase 8.2] 보안 강화: Security Hardener 활용
     */
    public function ajax_run_self_test() {
        // [Phase 8.2] 통합 보안 검증
        if ( class_exists( 'JJ_Security_Hardener' ) ) {
            if ( ! JJ_Security_Hardener::verify_ajax_request( 'jj_run_self_test', 'jj_admin_center_save_action', 'manage_options' ) ) {
                return; // verify_ajax_request가 이미 오류 응답을 보냄
            }
        } else {
            // 폴백: 기존 검증
            check_ajax_referer( 'jj_admin_center_save_action', 'security' );
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
                return;
            }
        }
        
        if ( ! class_exists( 'JJ_Self_Tester' ) ) {
            if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
                $tester_path = JJ_STYLE_GUIDE_PATH . 'tests/class-jj-self-tester.php';
                if ( file_exists( $tester_path ) ) {
                    require_once $tester_path;
                }
            }
        }

        if ( class_exists( 'JJ_Self_Tester' ) ) {
            $results = JJ_Self_Tester::run_tests();
            wp_send_json_success( array( 'results' => $results ) );
        } else {
            wp_send_json_error( array( 'message' => __( '테스터 클래스를 로드할 수 없습니다.', 'jj-style-guide' ) ) );
        }
    }

    /**
     * [Phase 8.5.2] AJAX: 진단 알림 해제
     */
    public function ajax_dismiss_diagnostic_notice() {
        if ( ! $this->verify_ajax_security( 'jj_dismiss_diagnostic_notice', 'jj_diagnostic_notice' ) ) {
            return;
        }
        
        update_user_meta( get_current_user_id(), 'jj_diagnostic_notice_dismissed', time() );
        wp_send_json_success();
    }

    /**
     * [v8.0.0] Bulk Installer: 파일 업로드 핸들러
     * [Phase 8.2] 보안 강화: 파일 검증 및 보안 헬퍼 활용
     */
    public function ajax_handle_bulk_upload() {
        // [Phase 8.2] 통합 보안 검증
        if ( ! $this->verify_ajax_security( 'jj_bulk_install_upload', 'jj_bulk_install', 'install_plugins' ) ) {
            return;
        }
        
        if ( empty( $_FILES['file'] ) ) {
            wp_send_json_error( array( 'message' => __( '파일이 없습니다.', 'jj-style-guide' ) ) );
            return;
        }

        // [Phase 8.2] 파일 업로드 검증 강화
        $allowed_types = array( 'application/zip', 'application/x-zip-compressed' );
        $max_size = 50 * 1024 * 1024; // 50MB
        
        if ( class_exists( 'JJ_Security_Hardener' ) ) {
            $validation = JJ_Security_Hardener::validate_upload( $_FILES['file'], $allowed_types, $max_size );
            
            if ( is_wp_error( $validation ) ) {
                wp_send_json_error( array( 'message' => $validation->get_error_message() ) );
                return;
            }
            
            $file = $validation;
        } else {
            // 기본 검증
            $file = $_FILES['file'];
            if ( isset( $file['size'] ) && $file['size'] > $max_size ) {
                wp_send_json_error( array( 'message' => __( '파일 크기가 너무 큽니다.', 'jj-style-guide' ) ) );
                return;
            }
        }
        
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/jj-bulk-temp/';
        if ( ! file_exists( $temp_dir ) ) {
            wp_mkdir_p( $temp_dir );
        }
        
        // [Phase 8.2] 파일명 정리 및 경로 조작 방지
        $safe_filename = sanitize_file_name( $file['name'] );
        $target_path = $temp_dir . $safe_filename;
        
        // 경로 조작 방지 (directory traversal)
        $target_path = realpath( dirname( $target_path ) ) . '/' . basename( $target_path );
        if ( strpos( $target_path, realpath( $temp_dir ) ) !== 0 ) {
            wp_send_json_error( array( 'message' => __( '유효하지 않은 파일 경로입니다.', 'jj-style-guide' ) ) );
            return;
        }
        
        if ( move_uploaded_file( $file['tmp_name'], $target_path ) ) {
            wp_send_json_success( array( 
                'path' => $target_path,
                'name' => $safe_filename,
                'type' => $this->detect_bulk_type( $target_path )
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( '파일 업로드 실패', 'jj-style-guide' ) ) );
        }
    }

    /**
     * [v8.0.0] Bulk Installer: 설치 핸들러
     */
    public function ajax_handle_bulk_install() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'install_plugins' ) ) wp_send_json_error( '권한이 없습니다.' );
        
        $file_path = isset( $_POST['path'] ) ? sanitize_text_field( $_POST['path'] ) : '';
        $type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'plugin';
        $auto_activate = isset( $_POST['activate'] ) && 'true' === $_POST['activate'];

        if ( ! file_exists( $file_path ) ) wp_send_json_error( '파일을 찾을 수 없습니다.' );

        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        include_once ABSPATH . 'wp-admin/includes/theme.php';

        ob_start();
        $skin = new WP_Ajax_Upgrader_Skin();
        $upgrader = ( $type === 'theme' ) ? new Theme_Upgrader( $skin ) : new Plugin_Upgrader( $skin );
        $result = $upgrader->install( $file_path );
        ob_end_clean();

        if ( is_wp_error( $result ) ) {
            @unlink( $file_path );
            wp_send_json_error( $result->get_error_message() );
        }

        $plugin_slug = ( $type === 'plugin' ) ? $upgrader->plugin_info() : '';
        @unlink( $file_path );

        $response = array( 'status' => 'installed', 'slug' => $plugin_slug );
        if ( $auto_activate && $plugin_slug ) {
            $activate_result = activate_plugin( $plugin_slug );
            $response['activated'] = ! is_wp_error( $activate_result );
        }

        wp_send_json_success( $response );
    }

    /**
     * [v8.0.0] Bulk Installer: 활성화 핸들러
     */
    /**
     * [Phase 8.2] 보안 강화
     */
    public function ajax_handle_bulk_activate() {
        if ( ! $this->verify_ajax_security( 'jj_bulk_activate_plugin', 'jj_bulk_install', 'install_plugins' ) ) {
            return;
        }
        
        $slug = isset( $_POST['slug'] ) ? sanitize_text_field( $_POST['slug'] ) : '';
        if ( ! $slug ) wp_send_json_error( '플러그인 정보가 없습니다.' );

        $result = activate_plugin( $slug );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }
        wp_send_json_success( '활성화됨' );
    }

    /**
     * [v8.0.0] Bulk Installer: 파일 타입 감지
     */
    private function detect_bulk_type( $zip_path ) {
        if ( ! class_exists( 'ZipArchive' ) ) return 'plugin';
        $zip = new ZipArchive;
        if ( $zip->open( $zip_path ) === TRUE ) {
            for ( $i = 0; $i < $zip->numFiles; $i++ ) {
                $filename = $zip->getNameIndex( $i );
                if ( strpos( $filename, 'style.css' ) !== false && substr_count( $filename, '/' ) <= 1 ) {
                    $content = $zip->getFromIndex( $i );
                    if ( strpos( $content, 'Theme Name:' ) !== false ) {
                        $zip->close();
                        return 'theme';
                    }
                }
            }
            $zip->close();
        }
        return 'plugin';
    }
}

if ( ! function_exists( 'jj_style_guide_text' ) ) {
    /**
     * 헬퍼 함수: 관리자 센터에서 정의한 텍스트를 가져옵니다.
     */
    function jj_style_guide_text( $key, $fallback = '' ) {
        return JJ_Admin_Center::instance()->get_text( $key, $fallback );
    }
}

if ( ! function_exists( 'jj_style_guide_sections_layout' ) ) {
    /**
     * 헬퍼: 섹션 레이아웃 전체를 반환
     *
     * @return array
     */
    function jj_style_guide_sections_layout() {
        return JJ_Admin_Center::instance()->get_sections_layout();
    }
}

if ( ! function_exists( 'jj_style_guide_section_index' ) ) {
    /**
     * 헬퍼: 특정 섹션의 1부터 시작하는 표시 순서 인덱스 반환
     *
     * @param string $slug
     * @return int|null
     */
    function jj_style_guide_section_index( $slug ) {
        return JJ_Admin_Center::instance()->get_section_index( $slug );
    }
}

// [v5.0.0] 헬퍼: 특정 섹션의 특정 탭이 활성화되어 있는지 확인
if ( ! function_exists( 'jj_style_guide_is_tab_enabled' ) ) {
    /**
     * 헬퍼: 특정 섹션의 특정 탭이 활성화되어 있는지 확인
     *
     * @param string $section_slug 섹션 슬러그
     * @param string $tab_slug 탭 슬러그
     * @return bool
     */
    function jj_style_guide_is_tab_enabled( $section_slug, $tab_slug ) {
        return JJ_Admin_Center::instance()->is_tab_enabled( $section_slug, $tab_slug );
    }
}

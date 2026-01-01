---

**User**

훌륭해, 제이슨. 역시 우리의 CTO답군. 고맙네. 내가 짚지 않은 곳이나 미처 생각치도 못한 부분까지 훌륭하게 개선해주었어. 나의 원래 취지에 딱 맞는 훌륭한 개선이었네. 이제 추가 개선 작업을 진행해주시게. 자네의 다음 작업을 기대하네. 나는 자네에게 참고가 될만한, UI/UX를 텍스트 몇 개와 링크 몇 개만 수정하는 것으로 대폭 개선할 수 있는 스크린샷을 공유하네. 타 플러그인들이 얼마나 잘했는지 참고해주길 부탁하네. 잘 보고, 깊이 생각해보시게. 자동 업데이트 허용 비허용 유무부터 시작하여 각종 링크나 색상과 볼드로 강조되어 있는 링크 텍스트, 필수/필요 플러그인에 대한 안내 등을 말이야... 제이슨, 자네는 내가 지금 말한 부분도 개선하면서 다음 작업도 진행해주게.. 추천 팔레트 부분, 그리고 그와 연계되는 다른 영역들까지 말이야. 자네의 재량에 맡기도록 하겠네. 잘 부탁하네, CTO.


---

**Cursor**

좋습니다. 스크린샷에서 핵심 힌트가 보입니다: **“자동 업데이트 상태(활성/비활성)”가 한눈에 보이고, 중요한 링크/경고/필수 플러그인 안내가 ‘텍스트 나열’이 아니라 ‘강조/버튼/배지’로 구조화**되어 있죠. 이제 우리 플러그인도 같은 기준으로, **업데이트(채널/자동업데이트) UX를 WP 코어(플러그인 목록 표시)와 일치**시키고, 팔레트 추천을 다른 영역(버튼/폼/관리자색상)까지 자연스럽게 연결하겠습니다. 먼저 업데이트/자동업데이트 관련 코드/탭을 찾아서 정리부터 들어갑니다.

```php
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
        // [Phase 6] 자가 진단 AJAX 핸들러
        add_action( 'wp_ajax_jj_run_self_test', array( $this, 'ajax_run_self_test' ) );

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
            $update_settings = get_option( 'jj_style_guide_update_settings', array() );
            if ( isset( $update_settings['auto_update_enabled'] ) ) {
                return (bool) $update_settings['auto_update_enabled'];
            }
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

        wp_enqueue_style(
            'jj-admin-center',
            $css_url,
            array(),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0'
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
        
        wp_enqueue_script(
            'jj-admin-center',
            $js_url,
            array( 'jquery', 'wp-color-picker', 'jquery-ui-core', 'jquery-ui-mouse', 'jquery-ui-sortable' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0',
            true
        );
        
        // [v5.0.3] 키보드 단축키 시스템 로드
        wp_enqueue_script( 'jj-keyboard-shortcuts', JJ_STYLE_GUIDE_URL . 'assets/js/jj-keyboard-shortcuts.js', array( 'jquery' ), defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0', true );
        
        // [v5.0.3] 툴팁 시스템 로드
        wp_enqueue_script( 'jj-tooltips', JJ_STYLE_GUIDE_URL . 'assets/js/jj-tooltips.js', array( 'jquery' ), defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0', true );

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
                        $purchase_url = 'https://j-j-labs.com'; // 기본값
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
     * AJAX 핸들러: 팔레트 데이터 가져오기
     * [v3.8.0] 관리자 센터 Colors 탭에서 팔레트 색상 불러오기용
     */
    public function ajax_get_palette_data() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
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
     */
    public function ajax_save_license_key() {
        check_ajax_referer( 'jj_license_save_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
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
     * [v5.1.7 신규] AJAX: 지금 업데이트 확인
     */
    public function ajax_check_updates_now() {
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
     */
    public function ajax_save_admin_center_settings() {
        check_ajax_referer( 'jj_admin_center_save_action', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
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
     */
    public function ajax_run_self_test() {
        check_ajax_referer( 'jj_admin_center_save_action', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
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
     * [v8.0.0] Bulk Installer: 파일 업로드 핸들러
     */
    public function ajax_handle_bulk_upload() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'install_plugins' ) ) wp_send_json_error( '권한이 없습니다.' );
        if ( empty( $_FILES['file'] ) ) wp_send_json_error( '파일이 없습니다.' );

        $file = $_FILES['file'];
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/jj-bulk-temp/';
        if ( ! file_exists( $temp_dir ) ) wp_mkdir_p( $temp_dir );
        
        $target_path = $temp_dir . basename( $file['name'] );
        
        if ( move_uploaded_file( $file['tmp_name'], $target_path ) ) {
            wp_send_json_success( array( 
                'path' => $target_path,
                'name' => $file['name'],
                'type' => $this->detect_bulk_type( $target_path )
            ) );
        } else {
            wp_send_json_error( '파일 업로드 실패' );
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
    public function ajax_handle_bulk_activate() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'install_plugins' ) ) wp_send_json_error( '권한이 없습니다.' );
        
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

```

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="jj-admin-center-tab-content" data-tab="updates">
    <?php
    // Partner/Master (내부/파트너용) 판별: 업그레이드/제한 UI 금지, 로그 전송 강제 등
    $is_partner_or_higher = false;
    if ( class_exists( 'JJ_Edition_Controller' ) ) {
        try {
            $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
        } catch ( Exception $e ) {
            // ignore
        } catch ( Error $e ) {
            // ignore
        }
    } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
        $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
    }

    // WP 코어 자동 업데이트 상태(플러그인 목록 UI와 동기화)
    $plugin_file = function_exists( 'plugin_basename' )
        ? plugin_basename( JJ_STYLE_GUIDE_PATH . 'acf-css-really-simple-style-guide.php' )
        : 'acf-css-really-simple-style-management-center-master/acf-css-really-simple-style-guide.php';
    $auto_updates = function_exists( 'get_site_option' ) ? (array) get_site_option( 'auto_update_plugins', array() ) : array();
    $core_auto_update_enabled = in_array( $plugin_file, $auto_updates, true );

    $update_settings = get_option( 'jj_style_guide_update_settings', array(
        'auto_update_enabled' => $core_auto_update_enabled,
        'update_channel' => 'stable',
        'beta_updates_enabled' => false,
        'send_app_logs' => false,
        'send_error_logs' => false,
    ) );
    
    // Partner/Master는 모든 로그를 반드시 전송
    if ( $is_partner_or_higher ) {
        $update_settings['send_app_logs'] = true;
        $update_settings['send_error_logs'] = true;
    }
    ?>
    <div style="max-width: 800px;">
        <h2 style="margin-top: 0;"><?php esc_html_e( '플러그인 업데이트 설정', 'jj-style-guide' ); ?></h2>
        <p class="description"><?php esc_html_e( '플러그인 업데이트 및 로그 전송 설정을 관리합니다. (WordPress 플러그인 목록의 “자동 업데이트 활성/비활성”과 동기화됩니다.)', 'jj-style-guide' ); ?></p>

        <?php
        $installed_channel = defined( 'JJ_STYLE_GUIDE_UPDATE_CHANNEL' ) ? JJ_STYLE_GUIDE_UPDATE_CHANNEL : '';
        ?>
        <div style="margin: 16px 0 8px; padding: 14px; border: 1px solid #c3c4c7; border-radius: 6px; background: #fff; display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
            <span class="jj-status-badge <?php echo $core_auto_update_enabled ? 'active' : 'inactive'; ?>">
                <?php echo $core_auto_update_enabled ? esc_html__( 'AUTO UPDATE: ON', 'jj-style-guide' ) : esc_html__( 'AUTO UPDATE: OFF', 'jj-style-guide' ); ?>
            </span>
            <?php if ( $installed_channel ) : ?>
                <span class="jj-license-type-badge jj-license-type-basic" style="background:#2271b1;">
                    <?php echo esc_html( strtoupper( $installed_channel ) ); ?>
                </span>
                <span class="description" style="margin:0;">
                    <?php
                    printf(
                        /* translators: %s: channel */
                        esc_html__( '현재 설치된 빌드 채널: %s', 'jj-style-guide' ),
                        esc_html( $installed_channel )
                    );
                    ?>
                </span>
            <?php endif; ?>
            <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button button-secondary" style="margin-left:auto;">
                <?php esc_html_e( '플러그인 목록에서 확인', 'jj-style-guide' ); ?>
            </a>
        </div>
        
        <table class="form-table" style="margin-top: 20px;">
            <tr>
                <th scope="row">
                    <label for="jj_auto_update_enabled"><?php esc_html_e( '자동 업데이트', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="checkbox" 
                                   id="jj_auto_update_enabled" 
                                   name="jj_update_settings[auto_update_enabled]" 
                                   value="1" 
                                   <?php checked( $update_settings['auto_update_enabled'], true ); ?>>
                            <?php esc_html_e( '자동 업데이트 활성화', 'jj-style-guide' ); ?>
                        </label>
                        <button type="button" 
                                id="jj-toggle-auto-update" 
                                class="button button-small"
                                data-enabled="<?php echo $update_settings['auto_update_enabled'] ? '1' : '0'; ?>">
                            <?php echo $update_settings['auto_update_enabled'] ? esc_html__( '비활성화', 'jj-style-guide' ) : esc_html__( '활성화', 'jj-style-guide' ); ?>
                        </button>
                    </div>
                    <p class="description">
                        <?php esc_html_e( '새 버전이 출시되면 자동으로 업데이트됩니다. (자동 업데이트를 꺼도 “업데이트 알림/수동 업데이트”는 유지됩니다.)', 'jj-style-guide' ); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="jj_update_channel"><?php esc_html_e( '업데이트 채널', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <select id="jj_update_channel" name="jj_update_settings[update_channel]" style="min-width: 200px;">
                        <option value="stable" <?php selected( $update_settings['update_channel'], 'stable' ); ?>>
                            <?php esc_html_e( '정식 (Stable)', 'jj-style-guide' ); ?>
                        </option>
                        <option value="beta" <?php selected( $update_settings['update_channel'], 'beta' ); ?>>
                            <?php esc_html_e( '베타 (Beta)', 'jj-style-guide' ); ?>
                        </option>
                        <?php if ( $is_partner_or_higher ) : ?>
                            <option value="staging" <?php selected( $update_settings['update_channel'], 'staging' ); ?>>
                                <?php esc_html_e( '스테이징 (Staging)', 'jj-style-guide' ); ?>
                            </option>
                        <?php endif; ?>
                    </select>
                    <p class="description">
                        <?php esc_html_e( '업데이트를 받을 채널을 선택하세요. 베타/스테이징은 실험적일 수 있으니 운영 사이트에서는 주의가 필요합니다.', 'jj-style-guide' ); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="jj_beta_updates_enabled"><?php esc_html_e( '베타 업데이트', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" 
                               id="jj_beta_updates_enabled" 
                               name="jj_update_settings[beta_updates_enabled]" 
                               value="1" 
                               <?php checked( $update_settings['beta_updates_enabled'], true ); ?>
                               <?php disabled( $is_partner_or_higher, true ); ?>>
                        <?php esc_html_e( '베타 업데이트 수신', 'jj-style-guide' ); ?>
                    </label>
                    <p class="description">
                        <?php esc_html_e( '베타 버전 업데이트를 받을지 선택합니다. 베타 버전은 불안정할 수 있습니다.', 'jj-style-guide' ); ?>
                        <?php if ( $is_partner_or_higher ) : ?>
                        <br><strong style="color: #d63638;"><?php esc_html_e( 'Partner/Master는 내부 정책상 로그/업데이트 옵션이 일부 고정될 수 있습니다.', 'jj-style-guide' ); ?></strong>
                        <?php endif; ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label><?php esc_html_e( '로그 전송', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" 
                                   id="jj_send_app_logs" 
                                   name="jj_update_settings[send_app_logs]" 
                                   value="1" 
                                   <?php checked( $update_settings['send_app_logs'], true ); ?>
                                   <?php disabled( $is_partner_or_higher, true ); ?>>
                            <?php esc_html_e( '앱 내 로그 전송', 'jj-style-guide' ); ?>
                        </label>
                        <?php if ( $is_partner_or_higher ) : ?>
                        <span style="color: #d63638; margin-left: 10px;"><?php esc_html_e( '(필수)', 'jj-style-guide' ); ?></span>
                        <?php endif; ?>
                        <p class="description" style="margin-left: 25px;">
                            <?php esc_html_e( '플러그인 내부 로그를 개발자에게 전송합니다.', 'jj-style-guide' ); ?>
                        </p>
                        
                        <label style="display: block; margin-top: 15px;">
                            <input type="checkbox" 
                                   id="jj_send_error_logs" 
                                   name="jj_update_settings[send_error_logs]" 
                                   value="1" 
                                   <?php checked( $update_settings['send_error_logs'], true ); ?>
                                   <?php disabled( $is_partner_or_higher, true ); ?>>
                            <?php esc_html_e( '오류 로그 전송', 'jj-style-guide' ); ?>
                        </label>
                        <?php if ( $is_partner_or_higher ) : ?>
                        <span style="color: #d63638; margin-left: 10px;"><?php esc_html_e( '(필수)', 'jj-style-guide' ); ?></span>
                        <?php endif; ?>
                        <p class="description" style="margin-left: 25px;">
                            <?php esc_html_e( '오류 및 예외 로그를 개발자에게 전송합니다.', 'jj-style-guide' ); ?>
                        </p>
                    </fieldset>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <button type="button" 
                    id="jj-save-update-settings" 
                    class="button button-primary">
                <?php esc_html_e( '설정 저장', 'jj-style-guide' ); ?>
            </button>
            <button type="button" 
                    id="jj-check-updates-now" 
                    class="button button-secondary">
                <?php esc_html_e( '지금 업데이트 확인', 'jj-style-guide' ); ?>
            </button>
        </p>
        
        <div id="jj-update-status" style="margin-top: 20px;"></div>

        <hr style="margin: 30px 0;">

        <?php
        // [Phase 5.2] Webhook 자동화 설정
        $webhook_settings = get_option( 'jj_style_guide_webhooks', array() );
        if ( ! is_array( $webhook_settings ) ) {
            $webhook_settings = array();
        }
        $wh_enabled      = ! empty( $webhook_settings['enabled'] );
        $wh_endpoints    = ( isset( $webhook_settings['endpoints'] ) && is_array( $webhook_settings['endpoints'] ) ) ? implode( "\n", array_map( 'trim', $webhook_settings['endpoints'] ) ) : '';
        $wh_payload_mode = ( isset( $webhook_settings['payload_mode'] ) && 'full' === $webhook_settings['payload_mode'] ) ? 'full' : 'minimal';
        $wh_timeout      = isset( $webhook_settings['timeout_seconds'] ) ? (int) $webhook_settings['timeout_seconds'] : 5;
        $wh_events       = ( isset( $webhook_settings['events'] ) && is_array( $webhook_settings['events'] ) ) ? $webhook_settings['events'] : array( 'style_settings_updated', 'admin_center_updated' );
        ?>

        <h2><?php esc_html_e( 'Webhook 자동화', 'jj-style-guide' ); ?></h2>
        <p class="description"><?php esc_html_e( '설정 변경 시 외부 자동화 시스템(CI/CD, Slack, 자체 서버 등)으로 Webhook을 전송합니다.', 'jj-style-guide' ); ?></p>

        <table class="form-table" style="margin-top: 20px;">
            <tr>
                <th scope="row">
                    <label for="jj_webhooks_enabled"><?php esc_html_e( 'Webhook 활성화', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="jj_webhooks_enabled" name="jj_webhooks[enabled]" value="1" <?php checked( $wh_enabled, true ); ?> />
                        <?php esc_html_e( '전송 활성화', 'jj-style-guide' ); ?>
                    </label>
                    <p class="description"><?php esc_html_e( '활성화 시, 아래 이벤트가 발생할 때 등록된 URL로 POST 요청이 전송됩니다.', 'jj-style-guide' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="jj_webhooks_endpoints"><?php esc_html_e( 'Webhook URL', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <textarea id="jj_webhooks_endpoints" name="jj_webhooks[endpoints]" rows="4" class="large-text code" placeholder="https://example.com/webhook"><?php echo esc_textarea( $wh_endpoints ); ?></textarea>
                    <p class="description"><?php esc_html_e( '한 줄에 하나씩 입력하세요. (여러 개 가능)', 'jj-style-guide' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="jj_webhooks_secret"><?php esc_html_e( '서명(Secret)', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <input type="password" id="jj_webhooks_secret" name="jj_webhooks[secret]" value="" class="regular-text" autocomplete="new-password" />
                    <label style="margin-left: 10px;">
                        <input type="checkbox" name="jj_webhooks[clear_secret]" value="1" />
                        <?php esc_html_e( 'Secret 초기화', 'jj-style-guide' ); ?>
                    </label>
                    <p class="description"><?php esc_html_e( '입력하면 저장되며, 비워두면 기존 Secret을 유지합니다. Secret이 있으면 X-JJ-Signature 헤더(HMAC-SHA256)가 포함됩니다.', 'jj-style-guide' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="jj_webhooks_payload_mode"><?php esc_html_e( '페이로드', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <select id="jj_webhooks_payload_mode" name="jj_webhooks[payload_mode]">
                        <option value="minimal" <?php selected( $wh_payload_mode, 'minimal' ); ?>>
                            <?php esc_html_e( 'Minimal (키/메타 중심)', 'jj-style-guide' ); ?>
                        </option>
                        <option value="full" <?php selected( $wh_payload_mode, 'full' ); ?>>
                            <?php esc_html_e( 'Full (설정 전체 포함)', 'jj-style-guide' ); ?>
                        </option>
                    </select>
                    <p class="description"><?php esc_html_e( 'Full은 데이터가 커질 수 있으므로 자동화 목적에 맞게 선택하세요.', 'jj-style-guide' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="jj_webhooks_timeout"><?php esc_html_e( '타임아웃(초)', 'jj-style-guide' ); ?></label>
                </th>
                <td>
                    <input type="number" id="jj_webhooks_timeout" name="jj_webhooks[timeout_seconds]" min="1" max="30" value="<?php echo esc_attr( $wh_timeout ); ?>" />
                    <p class="description"><?php esc_html_e( '테스트/동기 전송 시 적용됩니다. 실사용 전송은 비동기(blocking=false)로 수행됩니다.', 'jj-style-guide' ); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e( '이벤트', 'jj-style-guide' ); ?></th>
                <td>
                    <label style="display: block; margin-bottom: 8px;">
                        <input type="checkbox" name="jj_webhooks[events][]" value="style_settings_updated" <?php checked( in_array( 'style_settings_updated', $wh_events, true ), true ); ?> />
                        <?php esc_html_e( '스타일 센터 설정 저장', 'jj-style-guide' ); ?>
                    </label>
                    <label style="display: block;">
                        <input type="checkbox" name="jj_webhooks[events][]" value="admin_center_updated" <?php checked( in_array( 'admin_center_updated', $wh_events, true ), true ); ?> />
                        <?php esc_html_e( 'Admin Center(메뉴/상단바/텍스트/업데이트 등) 저장', 'jj-style-guide' ); ?>
                    </label>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="button" id="jj-test-webhook" class="button button-secondary">
                <?php esc_html_e( 'Webhook 테스트', 'jj-style-guide' ); ?>
            </button>
            <span id="jj-webhook-test-result" style="margin-left: 10px;"></span>
        </p>
    </div>
</div>


```

```php
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
        // [Phase 6] 자가 진단 AJAX 핸들러
        add_action( 'wp_ajax_jj_run_self_test', array( $this, 'ajax_run_self_test' ) );

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
            $update_settings = get_option( 'jj_style_guide_update_settings', array() );
            if ( isset( $update_settings['auto_update_enabled'] ) ) {
                return (bool) $update_settings['auto_update_enabled'];
            }
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

        wp_enqueue_style(
            'jj-admin-center',
            $css_url,
            array(),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0'
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
        
        wp_enqueue_script(
            'jj-admin-center',
            $js_url,
            array( 'jquery', 'wp-color-picker', 'jquery-ui-core', 'jquery-ui-mouse', 'jquery-ui-sortable' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0',
            true
        );
        
        // [v5.0.3] 키보드 단축키 시스템 로드
        wp_enqueue_script( 'jj-keyboard-shortcuts', JJ_STYLE_GUIDE_URL . 'assets/js/jj-keyboard-shortcuts.js', array( 'jquery' ), defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0', true );
        
        // [v5.0.3] 툴팁 시스템 로드
        wp_enqueue_script( 'jj-tooltips', JJ_STYLE_GUIDE_URL . 'assets/js/jj-tooltips.js', array( 'jquery' ), defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0', true );

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
                        $purchase_url = 'https://j-j-labs.com'; // 기본값
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
     * AJAX 핸들러: 팔레트 데이터 가져오기
     * [v3.8.0] 관리자 센터 Colors 탭에서 팔레트 색상 불러오기용
     */
    public function ajax_get_palette_data() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
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
     */
    public function ajax_save_license_key() {
        check_ajax_referer( 'jj_license_save_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
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
     * [v5.1.7 신규] AJAX: 지금 업데이트 확인
     */
    public function ajax_check_updates_now() {
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
     */
    public function ajax_save_admin_center_settings() {
        check_ajax_referer( 'jj_admin_center_save_action', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
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
     */
    public function ajax_run_self_test() {
        check_ajax_referer( 'jj_admin_center_save_action', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
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
     * [v8.0.0] Bulk Installer: 파일 업로드 핸들러
     */
    public function ajax_handle_bulk_upload() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'install_plugins' ) ) wp_send_json_error( '권한이 없습니다.' );
        if ( empty( $_FILES['file'] ) ) wp_send_json_error( '파일이 없습니다.' );

        $file = $_FILES['file'];
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/jj-bulk-temp/';
        if ( ! file_exists( $temp_dir ) ) wp_mkdir_p( $temp_dir );
        
        $target_path = $temp_dir . basename( $file['name'] );
        
        if ( move_uploaded_file( $file['tmp_name'], $target_path ) ) {
            wp_send_json_success( array( 
                'path' => $target_path,
                'name' => $file['name'],
                'type' => $this->detect_bulk_type( $target_path )
            ) );
        } else {
            wp_send_json_error( '파일 업로드 실패' );
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
    public function ajax_handle_bulk_activate() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'install_plugins' ) ) wp_send_json_error( '권한이 없습니다.' );
        
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

```

```javascript
/**
 * J&J Admin Center JavaScript - v4.0.2
 * 
 * 탭 기반 인터페이스와 2패널 레이아웃 제어
 * - 탭 전환 기능
 * - 2패널 레이아웃: 메뉴 아이템 선택 및 상세 정보 표시
 * - 색상 미리보기 업데이트
 * - 사이드바 액션 버튼 처리
 * - AJAX 기반 설정 저장/불러오기
 * 
 * @since 3.7.0
 * @version 4.0.2
 * 
 * [v4.0.2 변경사항]
 * - [CRITICAL FIX] 저장 기능 완전 수정
 *   - AJAX 핸들러 추가로 저장 기능 작동 보장
 *   - FormData 대신 jQuery serialize() 사용
 *   - 메뉴 순서 데이터 명시적 수집 및 업데이트
 * 
 * [v4.0.1 변경사항]
 * - 드래그앤드롭 초기화 로직 대폭 개선
 *   - 드래그 핸들 자동 생성 기능 추가
 *   - 초기화 검증 및 자동 재시도 메커니즘 강화
 *   - 이벤트 핸들링 개선 (다른 스크립트와의 충돌 방지)
 *   - 상세한 디버깅 로그 추가
 *   - 탭 전환 시 초기화 로직 개선
 *   - 재시도 횟수 증가 (20 → 30)
 *   - 터치 이벤트 지원 추가
 * 
 * [v6.3.0 추가]
 * - 왼쪽 고정 사이드바 네비게이션 제어
 * - 색상 히스토리 관리 (localStorage)
 * - 변경사항 미리보기 모달 시스템
 * - 모바일 최적화 (스와이프, 햄버거 메뉴)
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const $wrap = $('.jj-admin-center-wrap');

        // [v5.6.0] 미디어 업로드 버튼 핸들러
        $wrap.on('click', '.jj-upload-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const target = $btn.data('target');
            const $targetInput = $(target);
            
            let frame = wp.media({
                title: '이미지 선택',
                multiple: false,
                library: { type: 'image' },
                button: { text: '선택' }
            });
            
            frame.on('select', function() {
                const attachment = frame.state().get('selection').first().toJSON();
                $targetInput.val(attachment.url);
            });
            
            frame.open();
        });

        // [v5.7.0] AI Smart Palette Generator
        $wrap.on('click', '#jj-btn-generate-palette', function(e) {
            e.preventDefault();
            const baseColor = $('#jj-ai-base-color').val();
            const harmony = $('#jj-ai-harmony').val();
            const $resultDiv = $('#jj-ai-palette-result');
            const $chipsDiv = $resultDiv.find('.jj-ai-color-chips');
            
            if (!baseColor) {
                alert('색상을 선택하세요.');
                return;
            }
            
            $chipsDiv.html('<span class="spinner is-active" style="float:none;"></span>');
            $resultDiv.show();
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_generate_smart_palette',
                    nonce: jjAdminCenter.nonce,
                    base_color: baseColor,
                    harmony: harmony
                },
                success: function(response) {
                    if (response.success) {
                        const palette = response.data.palette.palette; // 배열
                        let html = '';
                        palette.forEach(function(color) {
                            html += '<div class="jj-ai-chip" style="width:40px; height:40px; background:' + color + '; border-radius:4px; cursor:pointer;" title="' + color + '" data-color="' + color + '"></div>';
                        });
                        $chipsDiv.html(html);
                        
                        // 적용하기 버튼 데이터 바인딩
                        $('#jj-btn-apply-ai-palette').data('palette', response.data.palette);
                    } else {
                        $chipsDiv.html('<span style="color:red;">' + response.data.message + '</span>');
                    }
                },
                error: function() {
                    $chipsDiv.html('<span style="color:red;">서버 오류가 발생했습니다.</span>');
                }
            });
        });
        
        // 팔레트 적용하기 버튼
        $wrap.on('click', '#jj-btn-apply-ai-palette', function(e) {
            e.preventDefault();
            const paletteData = $(this).data('palette');
            if (!paletteData) return;
            
            // Primary
            $('input[name*="[primary_color]"]').first().val(paletteData.primary).trigger('change');
            // Secondary
            $('input[name*="[secondary_color]"]').first().val(paletteData.secondary).trigger('change');
            
            alert('팔레트가 적용되었습니다. 저장 버튼을 눌러 확정하세요.');
        });

        // [v5.8.0] Cloud Export
        $wrap.on('click', '#jj-btn-cloud-export', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $result = $('#jj-cloud-export-result');
            
            $btn.prop('disabled', true).text('저장 중...');
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_cloud_export',
                    nonce: jjAdminCenter.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $result.find('.jj-code-box').text(response.data.share_code);
                        $result.slideDown();
                        alert('클라우드에 저장되었습니다. 공유 코드를 확인하세요.');
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('서버 통신 오류가 발생했습니다.');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('지금 저장하기');
                }
            });
        });

        // [v5.8.0] Cloud Import
        $wrap.on('click', '#jj-btn-cloud-import', function(e) {
            e.preventDefault();
            const code = $('#jj-cloud-share-code').val().trim();
            if (!code) {
                alert('공유 코드를 입력하세요.');
                return;
            }
            
            if (!confirm('현재 설정이 덮어씌워집니다. 계속하시겠습니까?')) {
                return;
            }
            
            const $btn = $(this);
            $btn.prop('disabled', true).text('불러오는 중...');
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_cloud_import',
                    nonce: jjAdminCenter.nonce,
                    share_code: code
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('서버 통신 오류가 발생했습니다.');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('불러오기');
                }
            });
        });

        // [v3.8.0 신규] 라이센스 관리 기능
        $('#jj-license-key-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $('#jj-save-license-btn');
            const $spinner = $('.jj-license-spinner');
            const $message = $('#jj-license-message');
            const licenseKey = $('#jj-license-key-input').val().trim();

            if (!licenseKey) {
                showLicenseMessage('error', '라이센스 키를 입력하세요.');
                return;
            }

            $btn.prop('disabled', true);
            $spinner.show();
            $message.hide();
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_save_license_key',
                    nonce: jjAdminCenter.nonce,
                    license_key: licenseKey
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    
                    if (response.success) {
                        showLicenseMessage('success', response.data.message);
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        showLicenseMessage('error', response.data.message || '라이센스 키 저장에 실패했습니다.');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    showLicenseMessage('error', '오류가 발생했습니다.');
                }
            });
        });
        
        // 라이센스 검증 버튼 클릭
        $('#jj-verify-license-btn').on('click', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $spinner = $('.jj-license-spinner');
            const $message = $('#jj-license-message');
            
            $btn.prop('disabled', true);
            $spinner.show();
            $message.hide();
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_verify_license_key',
                    nonce: jjAdminCenter.nonce
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    
                    if (response.success) {
                        const status = response.data.status;
                        let message = response.data.message;
                        
                        if (status.valid) {
                            showLicenseMessage('success', message);
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            showLicenseMessage('error', message);
                        }
                    } else {
                        showLicenseMessage('error', response.data.message || '라이센스 검증에 실패했습니다.');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    showLicenseMessage('error', '오류가 발생했습니다.');
                }
            });
        });
        
        // 라이센스 키 제거 버튼 클릭
        $('#jj-remove-license-btn').on('click', function(e) {
            e.preventDefault();
            if (!confirm('정말로 라이센스 키를 제거하시겠습니까? Free 버전으로 실행됩니다.')) {
                return;
            }
            
            const $btn = $(this);
            const $spinner = $('.jj-license-spinner');
            const $message = $('#jj-license-message');
            
            $btn.prop('disabled', true);
            $spinner.show();
            $message.hide();
            
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_save_license_key',
                    nonce: jjAdminCenter.nonce,
                    license_key: '' 
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    
                    showLicenseMessage('success', '라이센스 키가 제거되었습니다. Free 버전으로 실행됩니다.');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function() {
                    $btn.prop('disabled', false);
                    $spinner.hide();
                    showLicenseMessage('error', '오류가 발생했습니다.');
                }
            });
        });
        
        // 라이센스 키 복사 버튼
        $('.jj-copy-license-key-display').on('click', function(e) {
            e.preventDefault();
            const licenseKey = $(this).data('license-key');
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(licenseKey).select();
            document.execCommand('copy');
            $temp.remove();
            
            const $btn = $(this);
            const originalText = $btn.html();
            $btn.html('<span class="dashicons dashicons-yes" style="font-size: 12px; margin-top: 2px; color: #00a32a;"></span> 복사됨');
            
            setTimeout(function() {
                $btn.html(originalText);
            }, 2000);
        });
        
        function showLicenseMessage(type, message) {
            const $message = $('#jj-license-message');
            $message.removeClass('success error')
                    .addClass(type)
                    .html(message)
                    .fadeIn(300);
            
            setTimeout(function() {
                $message.fadeOut(300);
            }, 5000);
        }

        if (!$wrap.length) {
            return;
        }

        // [v6.3.0] 탭 전환 공통 함수
        function switchTab(tabId) {
            $wrap.find('.jj-admin-center-tabs li').removeClass('active');
            $wrap.find('.jj-admin-center-tabs li[data-tab="' + tabId + '"]').addClass('active');
            
            $wrap.find('.jj-admin-center-sidebar-nav a').removeClass('active');
            $wrap.find('.jj-admin-center-sidebar-nav a[data-tab="' + tabId + '"]').addClass('active');

            $wrap.find('.jj-admin-center-tab-content').removeClass('active');
            $wrap.find('.jj-admin-center-tab-content[data-tab="' + tabId + '"]').addClass('active');
            
            if (history.pushState) {
                history.pushState(null, null, '#' + tabId);
            } else {
                window.location.hash = tabId;
            }
        }

        $wrap.on('click', '.jj-admin-center-tabs a', function(e) {
            e.preventDefault();
            const $tab = $(this).closest('li');
            const tabId = $tab.data('tab');
            switchTab(tabId);
            
            if (tabId === 'admin-menu') {
                let tabInitRetryCount = 0;
                const tabInitMaxRetries = 10;
                const initSortableOnTabSwitch = function() {
                    const $menuList = $wrap.find('.jj-admin-center-menu-list');
                    const hasMenuItems = $menuList.length && $menuList.find('.jj-admin-center-menu-item').length > 0;
                    
                    if ($.fn.sortable && hasMenuItems) {
                        requestAnimationFrame(function() {
                            setTimeout(function() {
                                initializeSortable();
                            }, 100);
                        });
                    } else if (tabInitRetryCount < tabInitMaxRetries) {
                        tabInitRetryCount++;
                        setTimeout(initSortableOnTabSwitch, 200);
                    }
                };
                setTimeout(initSortableOnTabSwitch, 200);
            }
            
            if (tabId === 'colors' || tabId === 'visual') {
                setTimeout(function() {
                    initColorPickers();
                    loadPaletteChips(); 
                    initColorHistory(); 
                }, 200);
            }
        });

        let isDragging = false;
        let dragStartTime = 0;
        let dragStartPosition = null;
        
        $wrap.on('mousedown', '.jj-admin-center-menu-item', function(e) {
            if ($(e.target).closest('.jj-admin-center-menu-item-handle').length) {
                isDragging = false;
                dragStartTime = Date.now();
                dragStartPosition = { x: e.pageX, y: e.pageY };
                return true; 
            }
            isDragging = false;
            dragStartTime = 0;
            dragStartPosition = null;
        });
        
        $wrap.on('click', '.jj-admin-center-menu-item', function(e) {
            if ($(e.target).closest('.jj-toggle-submenu, .jj-admin-center-menu-item-handle').length) {
                return;
            }
            
            if (isDragging || (dragStartTime > 0 && Date.now() - dragStartTime < 300 && dragStartPosition)) {
                const currentPosition = { x: e.pageX, y: e.pageY };
                const distance = Math.sqrt(
                    Math.pow(currentPosition.x - dragStartPosition.x, 2) + 
                    Math.pow(currentPosition.y - dragStartPosition.y, 2)
                );
                if (distance > 5) return;
            }
            
            const $item = $(this);
            const itemId = $item.data('item-id');

            $wrap.find('.jj-admin-center-menu-item').removeClass('active');
            $item.addClass('active');

            loadMenuItemDetails(itemId);
        });

        function initColorPickers() {
            $wrap.find('.jj-admin-center-color-picker').each(function() {
                const $input = $(this);
                if ($input.data('wpColorPicker')) {
                    try {
                        $input.wpColorPicker('destroy');
                    } catch (e) {}
                }
                
                $input.wpColorPicker({
                    change: function(event, ui) {
                        const color = ui.color.toString();
                        $input.val(color).trigger('change');
                        updateColorPreview($input);
                    },
                    clear: function(event) {
                        $input.val('').trigger('change');
                        updateColorPreview($input);
                    }
                });
            });
        }

        function updateColorPreview($input) {
            const $preview = $input.closest('.jj-admin-center-color-input').find('.jj-admin-center-color-preview');
            const color = $input.val();

            if (color && /^#[0-9A-Fa-f]{6}$/.test(color)) {
                $preview.css('background-color', color);
                const $paletteChips = $input.closest('.jj-admin-center-color-input').find('.jj-palette-chip-inline');
                $paletteChips.css({
                    'border-color': '#ddd',
                    'border-width': '2px'
                });
                $paletteChips.filter('[data-color="' + color + '"]').css({
                    'border-color': '#2271b1',
                    'border-width': '3px'
                });
            } else {
                $preview.css('background-color', 'transparent');
            }
        }

        $wrap.on('input change', '.jj-admin-center-color-input input[type="text"]', function() {
            const $input = $(this);
            updateColorPreview($input);
            if ($input.data('wpColorPicker')) {
                try {
                    $input.wpColorPicker('color', $input.val());
                } catch (e) {}
            }
        });

        $wrap.find('.jj-admin-center-color-input input[type="text"]').each(function() {
            updateColorPreview($(this));
        });
        
        const COLOR_HISTORY_KEY = 'jj_color_history';
        const MAX_HISTORY_SIZE = 20;

        function getColorHistory() {
            try {
                const history = localStorage.getItem(COLOR_HISTORY_KEY);
                return history ? JSON.parse(history) : [];
            } catch (e) { return []; }
        }

        function saveColorToHistory(color) {
            if (!color || !/^#[0-9A-Fa-f]{6}$/.test(color)) return;
            let history = getColorHistory();
            history = history.filter(c => c !== color);
            history.unshift(color);
            if (history.length > MAX_HISTORY_SIZE) history = history.slice(0, MAX_HISTORY_SIZE);
            try {
                localStorage.setItem(COLOR_HISTORY_KEY, JSON.stringify(history));
            } catch (e) {}
        }

        function renderColorHistory($input) {
            const history = getColorHistory();
            const $historyContainer = $input.closest('.jj-admin-center-color-input').find('.jj-color-history');
            const $chipsContainer = $historyContainer.find('.jj-color-history-chips');
            
            if (history.length === 0) {
                $historyContainer.hide();
                return;
            }
            
            $historyContainer.show();
            $chipsContainer.empty();
            
            history.forEach(function(color) {
                const $chip = $('<div>')
                    .addClass('jj-color-history-chip')
                    .css('background-color', color)
                    .attr('data-color', color)
                    .attr('title', color)
                    .on('click', function() {
                        $input.val(color).trigger('change');
                        updateColorPreview($input);
                        if ($input.data('wpColorPicker')) {
                            try { $input.wpColorPicker('color', color); } catch (e) {}
                        }
                    });
                $chipsContainer.append($chip);
            });
        }

        function initColorHistory() {
            $wrap.find('.jj-admin-center-color-picker').each(function() {
                renderColorHistory($(this));
            });
        }

        $wrap.on('change', '.jj-admin-center-color-picker', function() {
            const color = $(this).val();
            if (color && /^#[0-9A-Fa-f]{6}$/.test(color)) {
                saveColorToHistory(color);
                renderColorHistory($(this));
            }
        });

        $wrap.on('click', '.jj-admin-center-color-preview', function(e) {
            e.preventDefault();
            const $input = $(this).closest('.jj-admin-center-color-input').find('.jj-admin-center-color-picker');
            if ($input.length && $input.data('wpColorPicker')) {
                $input.closest('.wp-picker-container').find('.wp-color-result').trigger('click');
            }
        });

        if ($wrap.find('.jj-admin-center-tab-content[data-tab="colors"]').hasClass('active')) {
            setTimeout(function() {
                initColorPickers();
                loadPaletteChips();
                initColorHistory();
            }, 200);
        }
        
        function loadPaletteChips() {
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: {
                    action: 'jj_get_palette_data',
                    nonce: jjAdminCenter.nonce
                },
                success: function(response) {
                    if (response.success && response.data.palettes) {
                        const palettes = response.data.palettes;
                        $wrap.find('.jj-admin-center-palette-chips').each(function() {
                            const $container = $(this);
                            const $targetInput = $container.closest('.jj-admin-center-color-input').find('.jj-admin-center-color-picker').first();
                            const $chipsContainer = $container.find('.jj-palette-chips-container');
                            $chipsContainer.empty();
                            
                            Object.keys(palettes).forEach(function(paletteKey) {
                                const palette = palettes[paletteKey];
                                const colors = palette.colors || {};
                                Object.keys(colors).forEach(function(colorKey) {
                                    const color = colors[colorKey];
                                    if (color && /^#[0-9A-Fa-f]{6}$/.test(color)) {
                                        const $chip = $('<div class="jj-palette-chip-inline" style="background: ' + color + '" data-color="' + color + '"></div>');
                                        $chip.on('click', function() {
                                            const selectedColor = $(this).data('color');
                                            $targetInput.val(selectedColor).trigger('change');
                                            if ($targetInput.data('wpColorPicker')) {
                                                try { $targetInput.wpColorPicker('color', selectedColor); } catch (e) {}
                                            }
                                            updateColorPreview($targetInput);
                                        });
                                        $chipsContainer.append($chip);
                                    }
                                });
                            });
                        });
                    }
                }
            });
        }

        function collectChanges() {
            const changes = [];
            const $form = $('#jj-admin-center-form');
            
            $form.find('.jj-admin-center-color-picker').each(function() {
                const $input = $(this);
                const newValue = $input.val();
                const oldValue = $input.data('original-value') || '';
                if (newValue !== oldValue && newValue) {
                    changes.push({ label: $input.closest('tr').find('th').text().trim(), oldValue: oldValue || '(비어있음)', newValue: newValue });
                }
            });
            
            $form.find('input[type="text"]:not(.jj-admin-center-color-picker), textarea').each(function() {
                const $input = $(this);
                const name = $input.attr('name');
                if (!name || name.includes('_nonce')) return;
                const newValue = $input.val();
                const oldValue = $input.data('original-value') || '';
                if (newValue !== oldValue) {
                    changes.push({ label: $input.closest('tr').find('th').text().trim() || name, oldValue: oldValue || '(비어있음)', newValue: newValue || '(비어있음)' });
                }
            });
            
            return changes;
        }

        function showChangesPreview(changes) {
            const $modal = $('#jj-changes-preview-modal');
            const $list = $('#jj-changes-list');
            if (changes.length === 0) {
                $list.html('<p>변경된 내용이 없습니다.</p>');
            } else {
                let html = '';
                changes.forEach(function(change) {
                    html += '<div class="jj-change-item"><div class="jj-change-item-title">' + change.label + '</div>';
                    html += '<div class="jj-change-item-detail">이전: <span class="jj-change-old-value">' + change.oldValue + '</span> -> 변경: <span class="jj-change-new-value">' + change.newValue + '</span></div></div>';
                });
                $list.html(html);
            }
            $modal.fadeIn(200);
        }

        function executeSave() {
            const $form = $('#jj-admin-center-form');
            const $submitBtn = $form.find('button[type="submit"]');
            $submitBtn.prop('disabled', true);
            $.ajax({
                url: jjAdminCenter.ajax_url,
                type: 'POST',
                data: $form.serialize() + '&action=jj_admin_center_save&security=' + jjAdminCenter.nonce,
                success: function(response) {
                    if (response.success) {
                        $('#jj-changes-preview-modal').fadeOut(200);
                        location.reload();
                    } else { alert(response.data.message); }
                },
                complete: function() { $submitBtn.prop('disabled', false); }
            });
        }

        $wrap.on('click', '.jj-modal-confirm-save', executeSave);
        $wrap.on('click', '.jj-modal-close, .jj-modal-cancel', function() { $('#jj-changes-preview-modal').fadeOut(200); });

        $('#jj-admin-center-form').on('submit', function(e) {
            e.preventDefault();
            showChangesPreview(collectChanges());
        });

        // ============================================================
        // Updates Tab (WP Plugins screen UX-aligned)
        // ============================================================
        function updateAutoUpdateBadge(isEnabled) {
            const $badge = $('.jj-admin-center-tab-content[data-tab="updates"]').find('.jj-status-badge').first();
            if (!$badge.length) return;
            $badge.toggleClass('active', !!isEnabled);
            $badge.toggleClass('inactive', !isEnabled);
            $badge.text(isEnabled ? 'AUTO UPDATE: ON' : 'AUTO UPDATE: OFF');
        }

        function syncAutoUpdateToggleUi() {
            const enabled = $('#jj_auto_update_enabled').is(':checked');
            const $btn = $('#jj-toggle-auto-update');
            if ($btn.length) {
                $btn.attr('data-enabled', enabled ? '1' : '0');
                $btn.text(enabled ? '비활성화' : '활성화');
            }
            updateAutoUpdateBadge(enabled);
        }

        // 토글 버튼: 체크박스 상태만 즉시 변경 (저장은 별도)
        $wrap.on('click', '#jj-toggle-auto-update', function(e) {
            e.preventDefault();
            const $checkbox = $('#jj_auto_update_enabled');
            if (!$checkbox.length || $checkbox.is(':disabled')) return;
            $checkbox.prop('checked', !$checkbox.is(':checked')).trigger('change');
            syncAutoUpdateToggleUi();
        });

        $wrap.on('change', '#jj_auto_update_enabled', function() {
            syncAutoUpdateToggleUi();
        });

        // 업데이트 설정 저장
        $wrap.on('click', '#jj-save-update-settings', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $status = $('#jj-update-status');

            const payload = {
                action: 'jj_save_update_settings',
                security: jjAdminCenter.nonce,
                update_settings: {
                    auto_update_enabled: $('#jj_auto_update_enabled').is(':checked') ? '1' : '0',
                    update_channel: $('#jj_update_channel').val() || 'stable',
                    beta_updates_enabled: $('#jj_beta_updates_enabled').is(':checked') ? '1' : '0',
                    send_app_logs: $('#jj_send_app_logs').is(':checked') ? '1' : '0',
                    send_error_logs: $('#jj_send_error_logs').is(':checked') ? '1' : '0',
                }
            };

            $btn.prop('disabled', true);
            $status.html('<span class="spinner is-active" style="float:none; margin:0 6px 0 0;"></span> 저장 중...');

            $.post(jjAdminCenter.ajax_url, payload)
                .done(function(resp) {
                    if (resp && resp.success) {
                        $status.html('<div class="notice notice-success inline"><p><strong>저장 완료:</strong> 업데이트 설정이 저장되었습니다.</p></div>');
                    } else {
                        const msg = resp && resp.data && resp.data.message ? resp.data.message : '저장에 실패했습니다.';
                        $status.html('<div class="notice notice-error inline"><p><strong>오류:</strong> ' + msg + '</p></div>');
                    }
                })
                .fail(function() {
                    $status.html('<div class="notice notice-error inline"><p><strong>오류:</strong> 네트워크 오류가 발생했습니다.</p></div>');
                })
                .always(function() {
                    $btn.prop('disabled', false);
                    // UI 동기화
                    syncAutoUpdateToggleUi();
                });
        });

        // 지금 업데이트 확인
        $wrap.on('click', '#jj-check-updates-now', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $status = $('#jj-update-status');

            $btn.prop('disabled', true);
            $status.html('<span class="spinner is-active" style="float:none; margin:0 6px 0 0;"></span> 업데이트 확인 중...');

            $.post(jjAdminCenter.ajax_url, { action: 'jj_check_updates_now', security: jjAdminCenter.nonce })
                .done(function(resp) {
                    if (resp && resp.success) {
                        const d = resp.data || {};
                        if (d.has_update) {
                            $status.html('<div class="notice notice-warning inline"><p><strong>업데이트 가능:</strong> ' + (d.message || '') + '</p></div>');
                        } else {
                            $status.html('<div class="notice notice-success inline"><p><strong>최신 상태:</strong> ' + (d.message || '') + '</p></div>');
                        }
                    } else {
                        const msg = resp && resp.data && resp.data.message ? resp.data.message : '업데이트 확인에 실패했습니다.';
                        $status.html('<div class="notice notice-error inline"><p><strong>오류:</strong> ' + msg + '</p></div>');
                    }
                })
                .fail(function() {
                    $status.html('<div class="notice notice-error inline"><p><strong>오류:</strong> 네트워크 오류가 발생했습니다.</p></div>');
                })
                .always(function() {
                    $btn.prop('disabled', false);
                });
        });

        // 최초 1회 동기화
        syncAutoUpdateToggleUi();

        function loadMenuItemDetails(itemId) {
            $wrap.find('.jj-admin-center-item-details').removeClass('active');
            $wrap.find('.jj-admin-center-item-details[data-item-id="' + itemId + '"]').addClass('active');
        }

        function initializeSortable() {
            const $menuList = $wrap.find('.jj-admin-center-menu-list');
            if (!$menuList.length || !$.fn.sortable) return;
            $menuList.sortable({
                handle: '.jj-admin-center-menu-item-handle',
                placeholder: 'jj-sortable-placeholder',
                stop: function() { updateMenuOrder(); }
            });
        }

        function updateMenuOrder() {
            $wrap.find('.jj-admin-center-menu-list > .jj-admin-center-menu-item').each(function(index) {
                const order = index + 1;
                $(this).find('.jj-admin-center-menu-item-order').text(order);
                const itemId = $(this).data('item-id');
                $wrap.find('input[name="jj_admin_menu_layout[' + itemId + '][order]"]').val(order);
            });
        }

        initializeSortable();
        $wrap.on('click', '.jj-admin-center-sidebar-nav a', function(e) {
            e.preventDefault();
            switchTab($(this).data('tab'));
        });

        $wrap.on('click', '.jj-sidebar-toggle', function() {
            $('.jj-admin-center-sidebar').toggleClass('jj-sidebar-open');
        });
    });
})(jQuery);

```

```php
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
        // [Phase 6] 자가 진단 AJAX 핸들러
        add_action( 'wp_ajax_jj_run_self_test', array( $this, 'ajax_run_self_test' ) );

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
            $update_settings = get_option( 'jj_style_guide_update_settings', array() );
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

        wp_enqueue_style(
            'jj-admin-center',
            $css_url,
            array(),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0'
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
        
        wp_enqueue_script(
            'jj-admin-center',
            $js_url,
            array( 'jquery', 'wp-color-picker', 'jquery-ui-core', 'jquery-ui-mouse', 'jquery-ui-sortable' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0',
            true
        );
        
        // [v5.0.3] 키보드 단축키 시스템 로드
        wp_enqueue_script( 'jj-keyboard-shortcuts', JJ_STYLE_GUIDE_URL . 'assets/js/jj-keyboard-shortcuts.js', array( 'jquery' ), defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0', true );
        
        // [v5.0.3] 툴팁 시스템 로드
        wp_enqueue_script( 'jj-tooltips', JJ_STYLE_GUIDE_URL . 'assets/js/jj-tooltips.js', array( 'jquery' ), defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '8.0.0', true );

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
                        $purchase_url = 'https://j-j-labs.com'; // 기본값
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
     * AJAX 핸들러: 팔레트 데이터 가져오기
     * [v3.8.0] 관리자 센터 Colors 탭에서 팔레트 색상 불러오기용
     */
    public function ajax_get_palette_data() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
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
     */
    public function ajax_save_license_key() {
        check_ajax_referer( 'jj_license_save_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
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
     * [v5.1.7 신규] AJAX: 지금 업데이트 확인
     */
    public function ajax_check_updates_now() {
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
     */
    public function ajax_save_admin_center_settings() {
        check_ajax_referer( 'jj_admin_center_save_action', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
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
     */
    public function ajax_run_self_test() {
        check_ajax_referer( 'jj_admin_center_save_action', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
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
     * [v8.0.0] Bulk Installer: 파일 업로드 핸들러
     */
    public function ajax_handle_bulk_upload() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'install_plugins' ) ) wp_send_json_error( '권한이 없습니다.' );
        if ( empty( $_FILES['file'] ) ) wp_send_json_error( '파일이 없습니다.' );

        $file = $_FILES['file'];
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/jj-bulk-temp/';
        if ( ! file_exists( $temp_dir ) ) wp_mkdir_p( $temp_dir );
        
        $target_path = $temp_dir . basename( $file['name'] );
        
        if ( move_uploaded_file( $file['tmp_name'], $target_path ) ) {
            wp_send_json_success( array( 
                'path' => $target_path,
                'name' => $file['name'],
                'type' => $this->detect_bulk_type( $target_path )
            ) );
        } else {
            wp_send_json_error( '파일 업로드 실패' );
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
    public function ajax_handle_bulk_activate() {
        check_ajax_referer( 'jj_bulk_install', 'nonce' );
        if ( ! current_user_can( 'install_plugins' ) ) wp_send_json_error( '권한이 없습니다.' );
        
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

```

```php
<?php
/**
 * 플러그인 업데이트 체크 클래스
 * 
 * WordPress 플러그인 업데이트 시스템과 통합하여 자동 업데이트를 제공합니다.
 * 
 * @package JJ_Style_Guide
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Plugin_Updater {
    
    private $plugin_file;
    private $plugin_slug;
    private $plugin_name;
    private $current_version;
    private $license_key;
    private $license_type;
    private $update_api_url;
    private $update_channel;
    private $beta_updates_enabled;
    
    /**
     * 생성자
     * 
     * @param string $plugin_file 플러그인 메인 파일 경로
     * @param string $plugin_slug 플러그인 슬러그
     * @param string $plugin_name 플러그인 이름
     * @param string $current_version 현재 버전
     * @param string $license_type 라이센스 타입 (FREE, BASIC, PREM, UNLIM)
     */
    public function __construct( $plugin_file, $plugin_slug, $plugin_name, $current_version, $license_type = 'FREE' ) {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = $plugin_slug;
        $this->plugin_name = $plugin_name;
        $this->current_version = $current_version;
        $this->license_type = $license_type;
        
        // 기본값 설정 (WordPress 함수가 없을 경우 대비)
        $this->license_key = '';
        $this->update_api_url = '';
        $this->update_channel = 'stable';
        $this->beta_updates_enabled = false;
        
        // WordPress가 완전히 로드된 후에만 초기화
        // plugins_loaded 훅 안에서 호출되는 경우 즉시 실행
        if ( did_action( 'plugins_loaded' ) ) {
            $this->init();
        } else {
            // plugins_loaded 훅에 등록 (일반적인 경우)
            add_action( 'plugins_loaded', array( $this, 'init' ), 25 );
        }
    }
    
    /**
     * 초기화 (WordPress 로드 후 실행)
     */
    public function init() {
        // WordPress 함수가 없으면 초기화하지 않음
        if ( ! function_exists( 'get_option' ) ) {
            return;
        }
        
        // 라이센스 키 가져오기
        $this->license_key = get_option( 'jj_style_guide_license_key', '' );
        
        // 업데이트 API URL 가져오기
        $this->update_api_url = get_option( 'jj_license_manager_server_url', 'https://j-j-labs.com' );
        
        // 업데이트 설정 가져오기
        $update_settings = get_option( 'jj_style_guide_update_settings', array(
            'auto_update_enabled' => true,
            'update_channel' => 'stable',
            'beta_updates_enabled' => false,
            'send_app_logs' => false,
            'send_error_logs' => false,
        ) );
        
        $this->update_channel = isset( $update_settings['update_channel'] ) ? $update_settings['update_channel'] : 'stable';
        $this->beta_updates_enabled = isset( $update_settings['beta_updates_enabled'] ) ? $update_settings['beta_updates_enabled'] : false;
        
        // 레거시 채널 매핑 (test/dev → staging)
        if ( in_array( $this->update_channel, array( 'test', 'dev' ), true ) ) {
            $this->update_channel = 'staging';
        }

        // Partner/Master는 기본적으로 staging 채널을 사용 (내부/파트너용)
        $is_partner_or_higher = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        }

        if ( $is_partner_or_higher && $this->update_channel === 'stable' ) {
            $this->update_channel = 'staging';
            $this->beta_updates_enabled = true;
        }
        
        // 업데이트 체크 훅 등록
        $this->init_hooks();
        
        // 로그 전송 스케줄링 (init 훅에서 실행)
        if ( did_action( 'init' ) ) {
            $this->schedule_log_sending();
        } else {
            add_action( 'init', array( $this, 'schedule_log_sending' ), 20 );
        }
    }
    
    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // WordPress 업데이트 시스템과 통합
        add_filter( 'site_transient_update_plugins', array( $this, 'check_for_updates' ) );
        add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
        add_filter( 'upgrader_post_install', array( $this, 'post_install' ), 10, 3 );
        
        // 수동 업데이트 체크
        add_action( 'wp_ajax_jj_check_plugin_updates', array( $this, 'ajax_check_updates' ) );
        
        // 자동 업데이트 활성화/비활성화
        $this->handle_auto_update();
    }
    
    /**
     * 자동 업데이트 활성화/비활성화 처리
     */
    private function handle_auto_update() {
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'plugin_basename' ) || ! function_exists( 'get_site_option' ) || ! function_exists( 'update_site_option' ) ) {
            return;
        }
        
        $update_settings = get_option( 'jj_style_guide_update_settings', array() );
        $auto_update_enabled = isset( $update_settings['auto_update_enabled'] ) ? $update_settings['auto_update_enabled'] : true;
        
        $plugin_file = plugin_basename( $this->plugin_file );
        if ( empty( $plugin_file ) ) {
            return;
        }
        
        if ( $auto_update_enabled ) {
            // 자동 업데이트 활성화
            $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
            if ( ! in_array( $plugin_file, $auto_updates, true ) ) {
                $auto_updates[] = $plugin_file;
                update_site_option( 'auto_update_plugins', $auto_updates );
            }
        } else {
            // 자동 업데이트 비활성화
            $auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
            $key = array_search( $plugin_file, $auto_updates, true );
            if ( false !== $key ) {
                unset( $auto_updates[ $key ] );
                update_site_option( 'auto_update_plugins', array_values( $auto_updates ) );
            }
        }
    }
    
    /**
     * 업데이트 체크
     * 
     * @param object $transient 업데이트 transient 객체
     * @return object 수정된 transient 객체
     */
    public function check_for_updates( $transient ) {
        // WordPress 함수가 없으면 체크하지 않음
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'plugin_basename' ) ) {
            return $transient;
        }
        
        // 업데이트 API URL이 없으면 체크하지 않음
        if ( empty( $this->update_api_url ) ) {
            return $transient;
        }
        
        // NOTE: 자동 업데이트 OFF여도 "업데이트 알림/수동 업데이트"는 유지되어야 합니다.
        // 자동 업데이트의 실제 동작은 WordPress 코어(auto_update_plugins) 설정에 의해 제어됩니다.
        
        // 플러그인 파일 경로 생성
        $plugin_basename = plugin_basename( $this->plugin_file );
        if ( empty( $plugin_basename ) ) {
            return $transient;
        }
        
        // 업데이트 정보 가져오기
        $update_info = $this->get_update_info();
        
        if ( $update_info && isset( $update_info['new_version'] ) ) {
            // 버전 비교
            if ( version_compare( $this->current_version, $update_info['new_version'], '<' ) ) {
                // 업데이트 정보 추가
                $transient->response[ $plugin_basename ] = (object) array(
                    'slug' => $this->plugin_slug,
                    'plugin' => $plugin_basename,
                    'new_version' => $update_info['new_version'],
                    'url' => isset( $update_info['url'] ) ? $update_info['url'] : '',
                    'package' => $this->get_download_url( $update_info['new_version'] ),
                    'tested' => isset( $update_info['tested'] ) ? $update_info['tested'] : get_bloginfo( 'version' ),
                    'requires_php' => isset( $update_info['requires_php'] ) ? $update_info['requires_php'] : '7.4',
                    'compatibility' => isset( $update_info['compatibility'] ) ? $update_info['compatibility'] : new stdClass(),
                );
            }
        }
        
        return $transient;
    }
    
    /**
     * 플러그인 API 필터
     * 
     * @param false|object|array $result 플러그인 정보
     * @param string $action 액션
     * @param object $args 인수
     * @return false|object|array 수정된 플러그인 정보
     */
    public function plugins_api_filter( $result, $action, $args ) {
        if ( $action !== 'plugin_information' || $args->slug !== $this->plugin_slug ) {
            return $result;
        }
        
        // 업데이트 정보 가져오기
        $update_info = $this->get_update_info();
        
        if ( $update_info ) {
            $result = (object) array(
                'name' => $this->plugin_name,
                'slug' => $this->plugin_slug,
                'version' => isset( $update_info['new_version'] ) ? $update_info['new_version'] : $this->current_version,
                'author' => isset( $update_info['author'] ) ? $update_info['author'] : 'Jay & Jenny Labs',
                'author_profile' => isset( $update_info['author_profile'] ) ? $update_info['author_profile'] : 'https://poetryflow.blog',
                'requires' => isset( $update_info['requires'] ) ? $update_info['requires'] : '6.0',
                'tested' => isset( $update_info['tested'] ) ? $update_info['tested'] : get_bloginfo( 'version' ),
                'requires_php' => isset( $update_info['requires_php'] ) ? $update_info['requires_php'] : '7.4',
                'download_link' => $this->get_download_url( isset( $update_info['new_version'] ) ? $update_info['new_version'] : $this->current_version ),
                'sections' => isset( $update_info['sections'] ) ? $update_info['sections'] : array(),
                'banners' => isset( $update_info['banners'] ) ? $update_info['banners'] : array(),
            );
        }
        
        return $result;
    }
    
    /**
     * 업데이트 후 처리
     * 
     * @param bool $response 응답
     * @param array $hook_extra 추가 정보
     * @param array $result 결과
     * @return bool 수정된 응답
     */
    public function post_install( $response, $hook_extra, $result ) {
        $plugin_basename = plugin_basename( $this->plugin_file );
        
        if ( isset( $hook_extra['plugin'] ) && $hook_extra['plugin'] === $plugin_basename ) {
            // 플러그인 활성화 유지
            $activate = activate_plugin( $plugin_basename );
            if ( is_wp_error( $activate ) ) {
                error_log( 'JJ Plugin Updater: 플러그인 활성화 실패 - ' . $activate->get_error_message() );
            }
        }
        
        return $response;
    }
    
    /**
     * AJAX 업데이트 체크
     */
    public function ajax_check_updates() {
        check_ajax_referer( 'jj_plugin_updater_nonce', 'nonce' );
        
        if ( ! current_user_can( 'update_plugins' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        // 캐시 삭제하여 강제로 업데이트 체크
        delete_site_transient( 'update_plugins' );
        
        // 업데이트 정보 가져오기
        $update_info = $this->get_update_info();
        
        if ( $update_info && isset( $update_info['new_version'] ) ) {
            if ( version_compare( $this->current_version, $update_info['new_version'], '<' ) ) {
                wp_send_json_success( array(
                    'has_update' => true,
                    'current_version' => $this->current_version,
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
    }
    
    /**
     * 업데이트 정보 가져오기
     * 
     * @return array|false 업데이트 정보 또는 false
     */
    public function get_update_info() {
        if ( ! function_exists( 'get_transient' ) || ! function_exists( 'set_transient' ) ) {
            return false;
        }
        
        // 캐시 확인 (채널 포함)
        $cache_key = 'jj_plugin_update_' . md5( $this->plugin_slug . $this->current_version . $this->license_key . $this->update_channel );
        $cached = get_transient( $cache_key );
        
        if ( $cached !== false ) {
            return $cached;
        }
        
        // 라이센스 매니저의 업데이트 API 사용
        if ( class_exists( 'JJ_License_Update_API' ) ) {
            $update_api = new JJ_License_Update_API();
            $update_info = $update_api->get_update_info( 
                $this->plugin_slug, 
                $this->current_version, 
                $this->license_key,
                $this->update_channel,
                $this->beta_updates_enabled
            );
            
            // 캐시 저장 (12시간)
            if ( $update_info && defined( 'HOUR_IN_SECONDS' ) ) {
                set_transient( $cache_key, $update_info, 12 * HOUR_IN_SECONDS );
            }
            
            return $update_info;
        }
        
        return false;
    }
    
    /**
     * 로그 전송 스케줄링
     */
    public function schedule_log_sending() {
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'wp_next_scheduled' ) || ! function_exists( 'wp_schedule_event' ) || ! function_exists( 'wp_unschedule_event' ) ) {
            return;
        }
        
        $update_settings = get_option( 'jj_style_guide_update_settings', array() );
        $send_app_logs = isset( $update_settings['send_app_logs'] ) ? $update_settings['send_app_logs'] : false;
        $send_error_logs = isset( $update_settings['send_error_logs'] ) ? $update_settings['send_error_logs'] : false;
        
        // Partner/Master는 모든 로그를 반드시 전송
        $is_partner_or_higher = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        }
        if ( $is_partner_or_higher ) {
            $send_app_logs = true;
            $send_error_logs = true;
        }
        
        // 로그 전송이 활성화된 경우에만 스케줄링
        if ( $send_app_logs || $send_error_logs ) {
            if ( ! wp_next_scheduled( 'jj_send_logs_to_server' ) ) {
                wp_schedule_event( time(), 'daily', 'jj_send_logs_to_server' );
            }
            add_action( 'jj_send_logs_to_server', array( $this, 'send_logs_to_server' ) );
        } else {
            // 비활성화된 경우 스케줄 제거
            $timestamp = wp_next_scheduled( 'jj_send_logs_to_server' );
            if ( $timestamp ) {
                wp_unschedule_event( $timestamp, 'jj_send_logs_to_server' );
            }
        }
    }
    
    /**
     * 로그를 서버로 전송
     */
    public function send_logs_to_server() {
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'home_url' ) || ! function_exists( 'wp_remote_post' ) ) {
            return;
        }
        
        $update_settings = get_option( 'jj_style_guide_update_settings', array() );
        $send_app_logs = isset( $update_settings['send_app_logs'] ) ? $update_settings['send_app_logs'] : false;
        $send_error_logs = isset( $update_settings['send_error_logs'] ) ? $update_settings['send_error_logs'] : false;
        
        // Partner/Master는 모든 로그를 반드시 전송
        $is_partner_or_higher = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        }
        if ( $is_partner_or_higher ) {
            $send_app_logs = true;
            $send_error_logs = true;
        }
        
        if ( ! $send_app_logs && ! $send_error_logs ) {
            return;
        }
        
        $license_server_url = get_option( 'jj_license_manager_server_url', '' );
        if ( empty( $license_server_url ) ) {
            return;
        }
        
        $logs = array();
        
        // 앱 내 로그 수집
        if ( $send_app_logs && class_exists( 'JJ_Error_Logger' ) ) {
            $error_logger = JJ_Error_Logger::instance();
            if ( method_exists( $error_logger, 'get_app_logs' ) ) {
                $logs['app'] = $error_logger->get_app_logs();
            }
        }
        
        // 오류 로그 수집
        if ( $send_error_logs ) {
            // WordPress 오류 로그
            $error_log_file = WP_CONTENT_DIR . '/debug.log';
            if ( file_exists( $error_log_file ) && is_readable( $error_log_file ) ) {
                $error_logs = file_get_contents( $error_log_file );
                if ( strlen( $error_logs ) > 100000 ) {
                    // 로그가 너무 크면 마지막 100KB만 전송
                    $error_logs = substr( $error_logs, -100000 );
                }
                $logs['error'] = $error_logs;
            }
        }
        
        if ( empty( $logs ) ) {
            return;
        }
        
        // REST API 엔드포인트 URL 생성
        $rest_url = trailingslashit( $license_server_url ) . 'wp-json/jj-license/v1/logs';
        
        // POST 요청 데이터
        $data = array(
            'plugin_slug' => sanitize_text_field( $this->plugin_slug ),
            'plugin_version' => sanitize_text_field( $this->current_version ),
            'site_id' => $this->get_site_id(),
            'site_url' => esc_url_raw( home_url() ),
            'logs' => $logs,
            'timestamp' => time(),
        );
        
        // API 키 가져오기 (선택사항)
        $api_key = get_option( 'jj_license_api_key', '' );
        $headers = array(
            'User-Agent' => 'JJ-License-Manager/' . ( defined( 'JJ_LICENSE_MANAGER_VERSION' ) ? JJ_LICENSE_MANAGER_VERSION : '2.0.0' ),
            'Content-Type' => 'application/json',
        );
        
        if ( ! empty( $api_key ) ) {
            $headers['X-API-Key'] = $api_key;
        }
        
        // WordPress HTTP API 사용
        $response = wp_remote_post( esc_url_raw( $rest_url ), array(
            'timeout' => 30,
            'sslverify' => true,
            'body' => json_encode( $data ),
            'headers' => $headers,
            'redirection' => 0,
        ) );
        
        // 네트워크 오류 처리 (로그만 기록, 사용자에게 알리지 않음)
        if ( is_wp_error( $response ) ) {
            error_log( 'JJ Plugin Updater: 로그 전송 실패 - ' . $response->get_error_message() );
        }
    }
    
    /**
     * 사이트 ID 가져오기
     */
    private function get_site_id() {
        if ( ! function_exists( 'get_option' ) || ! function_exists( 'home_url' ) || ! function_exists( 'update_option' ) ) {
            return '';
        }
        
        $site_id = get_option( 'jj_license_site_id' );
        
        if ( empty( $site_id ) ) {
            $site_url = home_url();
            $site_id = md5( $site_url . ( defined( 'ABSPATH' ) ? ABSPATH : '' ) );
            update_option( 'jj_license_site_id', $site_id );
        }
        
        return $site_id;
    }
    
    /**
     * 다운로드 URL 가져오기
     * 
     * @param string $version 버전
     * @return string 다운로드 URL
     */
    private function get_download_url( $version ) {
        if ( class_exists( 'JJ_License_Update_API' ) ) {
            $update_api = new JJ_License_Update_API();
            $download_url = $update_api->get_download_url( $this->plugin_slug, $version, $this->license_key );
            
            if ( $download_url ) {
                return $download_url;
            }
        }
        
        return '';
    }
}


```

```php
<?php
/**
 * Plugin Name: ACF CSS License Bridge for WooCommerce
 * Plugin URI: https://j-j-labs.com/acf-css-manager
 * Description: WooCommerce 결제 완료 시 Neural Link 서버에 라이센스 발행 요청을 전송합니다.
 * Version:           2.0.0
 * Author:            3J Labs
 * Created by:        Jay & Jason & Jenny
 * Author URI: https://j-j-labs.com
 * License: GPL-2.0+
 * Text Domain: acf-css-woo-license
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// WooCommerce 활성화 확인
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    return;
}

/**
 * WooCommerce HPOS(High-Performance Order Storage) 호환성 선언
 */
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

class ACF_CSS_Woo_License {

    /**
     * Neural Link 서버 URL
     */
    private $neural_link_url;

    /**
     * API Key
     */
    private $api_key;

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 인스턴스 반환
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->neural_link_url = get_option( 'acf_css_neural_link_url', '' );
        $this->api_key = get_option( 'acf_css_neural_link_api_key', '' );

        // WooCommerce 훅
        add_action( 'woocommerce_order_status_completed', array( $this, 'process_completed_order' ) );
        add_action( 'woocommerce_order_status_processing', array( $this, 'process_completed_order' ) );
        
        // 관리자 설정
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        
        // 상품 메타 필드
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_license_product_fields' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_license_product_fields' ) );
        
        // 주문 완료 페이지에 라이센스 키 표시
        add_action( 'woocommerce_thankyou', array( $this, 'display_license_key_on_thankyou' ) );
        add_action( 'woocommerce_order_details_after_order_table', array( $this, 'display_license_key_in_order_details' ) );
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'ACF CSS 라이센스 설정', 'acf-css-woo-license' ),
            __( 'ACF CSS 라이센스', 'acf-css-woo-license' ),
            'manage_options',
            'acf-css-woo-license',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * 설정 등록
     */
    public function register_settings() {
        register_setting( 'acf_css_woo_license_settings', 'acf_css_neural_link_url' );
        register_setting( 'acf_css_woo_license_settings', 'acf_css_neural_link_api_key' );
        
        add_settings_section(
            'acf_css_neural_link_section',
            __( 'Neural Link 서버 설정', 'acf-css-woo-license' ),
            null,
            'acf-css-woo-license'
        );
        
        add_settings_field(
            'acf_css_neural_link_url',
            __( 'Neural Link URL', 'acf-css-woo-license' ),
            array( $this, 'render_url_field' ),
            'acf-css-woo-license',
            'acf_css_neural_link_section'
        );
        
        add_settings_field(
            'acf_css_neural_link_api_key',
            __( 'API Key', 'acf-css-woo-license' ),
            array( $this, 'render_api_key_field' ),
            'acf-css-woo-license',
            'acf_css_neural_link_section'
        );
    }

    /**
     * 설정 페이지 렌더링
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'ACF CSS 라이센스 설정', 'acf-css-woo-license' ); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields( 'acf_css_woo_license_settings' );
                do_settings_sections( 'acf-css-woo-license' );
                submit_button();
                ?>
            </form>
            
            <hr>
            
            <h2><?php _e( '연결 테스트', 'acf-css-woo-license' ); ?></h2>
            <p><?php _e( 'Neural Link 서버 연결 상태를 테스트합니다.', 'acf-css-woo-license' ); ?></p>
            <button type="button" class="button" id="acf-css-test-connection">
                <?php _e( '연결 테스트', 'acf-css-woo-license' ); ?>
            </button>
            <span id="acf-css-test-result" style="margin-left: 10px;"></span>
            
            <script>
            jQuery(document).ready(function($) {
                $('#acf-css-test-connection').on('click', function() {
                    var $btn = $(this);
                    var $result = $('#acf-css-test-result');
                    
                    $btn.prop('disabled', true);
                    $result.html('테스트 중...');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'acf_css_test_neural_link',
                            nonce: '<?php echo wp_create_nonce( 'acf_css_test_neural_link' ); ?>'
                        },
                        success: function(response) {
                            $btn.prop('disabled', false);
                            if (response.success) {
                                $result.html('<span style="color: green;">✓ ' + response.data.message + '</span>');
                            } else {
                                $result.html('<span style="color: red;">✗ ' + response.data.message + '</span>');
                            }
                        },
                        error: function() {
                            $btn.prop('disabled', false);
                            $result.html('<span style="color: red;">✗ 연결 실패</span>');
                        }
                    });
                });
            });
            </script>
        </div>
        <?php
    }

    /**
     * URL 필드 렌더링
     */
    public function render_url_field() {
        $url = get_option( 'acf_css_neural_link_url', '' );
        ?>
        <input type="url" name="acf_css_neural_link_url" value="<?php echo esc_attr( $url ); ?>" class="regular-text" placeholder="https://your-neural-link-server.com">
        <p class="description"><?php _e( 'Neural Link 서버가 설치된 WordPress 사이트 URL', 'acf-css-woo-license' ); ?></p>
        <?php
    }

    /**
     * API Key 필드 렌더링
     */
    public function render_api_key_field() {
        $api_key = get_option( 'acf_css_neural_link_api_key', '' );
        ?>
        <input type="password" name="acf_css_neural_link_api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text">
        <p class="description"><?php _e( 'Neural Link 서버에서 발급받은 API Key', 'acf-css-woo-license' ); ?></p>
        <?php
    }

    /**
     * 상품 편집 페이지에 라이센스 필드 추가
     */
    public function add_license_product_fields() {
        global $post;
        
        echo '<div class="options_group">';
        
        woocommerce_wp_checkbox( array(
            'id'          => '_acf_css_license_product',
            'label'       => __( 'ACF CSS 라이센스 상품', 'acf-css-woo-license' ),
            'description' => __( '이 상품 구매 시 ACF CSS 라이센스를 발행합니다.', 'acf-css-woo-license' ),
        ) );
        
        woocommerce_wp_select( array(
            'id'          => '_acf_css_license_edition',
            'label'       => __( '라이센스 에디션', 'acf-css-woo-license' ),
            'options'     => array(
                ''          => __( '선택하세요', 'acf-css-woo-license' ),
                'free'      => __( 'Free', 'acf-css-woo-license' ),
                'basic'     => __( 'Basic (PRO)', 'acf-css-woo-license' ),
                'premium'   => __( 'Premium (PRO)', 'acf-css-woo-license' ),
                'unlimited' => __( 'Unlimited (PRO)', 'acf-css-woo-license' ),
                'partner'   => __( 'Partner', 'acf-css-woo-license' ),
                'master'    => __( 'Master', 'acf-css-woo-license' ),
            ),
        ) );
        
        woocommerce_wp_text_input( array(
            'id'          => '_acf_css_license_duration_days',
            'label'       => __( '라이센스 기간 (일)', 'acf-css-woo-license' ),
            'type'        => 'number',
            'desc_tip'    => true,
            'description' => __( '0 = 영구 라이센스, 365 = 1년', 'acf-css-woo-license' ),
        ) );
        
        woocommerce_wp_text_input( array(
            'id'          => '_acf_css_license_site_limit',
            'label'       => __( '사이트 수 제한', 'acf-css-woo-license' ),
            'type'        => 'number',
            'desc_tip'    => true,
            'description' => __( '0 = 무제한', 'acf-css-woo-license' ),
        ) );
        
        echo '</div>';
    }

    /**
     * 라이센스 상품 필드 저장
     */
    public function save_license_product_fields( $post_id ) {
        $is_license = isset( $_POST['_acf_css_license_product'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_acf_css_license_product', $is_license );
        
        if ( isset( $_POST['_acf_css_license_edition'] ) ) {
            update_post_meta( $post_id, '_acf_css_license_edition', sanitize_text_field( $_POST['_acf_css_license_edition'] ) );
        }
        
        if ( isset( $_POST['_acf_css_license_duration_days'] ) ) {
            update_post_meta( $post_id, '_acf_css_license_duration_days', absint( $_POST['_acf_css_license_duration_days'] ) );
        }
        
        if ( isset( $_POST['_acf_css_license_site_limit'] ) ) {
            update_post_meta( $post_id, '_acf_css_license_site_limit', absint( $_POST['_acf_css_license_site_limit'] ) );
        }
    }

    /**
     * 주문 완료 시 라이센스 발행
     */
    public function process_completed_order( $order_id ) {
        $order = wc_get_order( $order_id );
        
        if ( ! $order ) {
            return;
        }
        
        // 이미 처리된 주문인지 확인
        if ( $order->get_meta( '_acf_css_license_issued' ) === 'yes' ) {
            return;
        }
        
        $licenses_issued = array();
        
        foreach ( $order->get_items() as $item_id => $item ) {
            $product_id = $item->get_product_id();
            
            // 라이센스 상품인지 확인
            if ( get_post_meta( $product_id, '_acf_css_license_product', true ) !== 'yes' ) {
                continue;
            }
            
            $edition = get_post_meta( $product_id, '_acf_css_license_edition', true );
            $duration = (int) get_post_meta( $product_id, '_acf_css_license_duration_days', true );
            $site_limit = (int) get_post_meta( $product_id, '_acf_css_license_site_limit', true );
            
            // Neural Link에 라이센스 발행 요청
            $license_key = $this->issue_license(
                $order->get_billing_email(),
                $edition,
                $duration,
                $site_limit,
                $order_id
            );
            
            if ( $license_key ) {
                $licenses_issued[] = array(
                    'product_name' => $item->get_name(),
                    'edition'      => $edition,
                    'license_key'  => $license_key,
                );
                
                // 주문 항목에 라이센스 키 저장
                wc_update_order_item_meta( $item_id, '_acf_css_license_key', $license_key );
            }
        }
        
        if ( ! empty( $licenses_issued ) ) {
            // 주문에 발행 완료 표시
            $order->update_meta_data( '_acf_css_license_issued', 'yes' );
            $order->update_meta_data( '_acf_css_licenses', $licenses_issued );
            $order->save();
            
            // 이메일로 라이센스 키 전송
            $this->send_license_email( $order, $licenses_issued );
            
            // 주문 노트 추가
            $order->add_order_note( 
                sprintf( 
                    __( 'ACF CSS 라이센스 %d개 발행 완료', 'acf-css-woo-license' ), 
                    count( $licenses_issued ) 
                ) 
            );
        }
    }

    /**
     * Neural Link에 라이센스 발행 요청
     */
    private function issue_license( $email, $edition, $duration, $site_limit, $order_id ) {
        if ( empty( $this->neural_link_url ) || empty( $this->api_key ) ) {
            error_log( 'ACF CSS Woo License: Neural Link 설정이 완료되지 않았습니다.' );
            return false;
        }
        
        $endpoint = trailingslashit( $this->neural_link_url ) . 'wp-json/acf-neural-link/v1/license/issue';
        
        $response = wp_remote_post( $endpoint, array(
            'timeout' => 30,
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
            ),
            'body'    => wp_json_encode( array(
                'email'       => $email,
                'edition'     => $edition,
                'duration'    => $duration,
                'site_limit'  => $site_limit,
                'order_id'    => $order_id,
                'source'      => 'woocommerce',
            ) ),
            'sslverify' => true,
        ) );
        
        if ( is_wp_error( $response ) ) {
            error_log( 'ACF CSS Woo License: Neural Link 연결 실패 - ' . $response->get_error_message() );
            return false;
        }
        
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        
        if ( isset( $body['success'] ) && $body['success'] && isset( $body['license_key'] ) ) {
            return $body['license_key'];
        }
        
        error_log( 'ACF CSS Woo License: 라이센스 발행 실패 - ' . print_r( $body, true ) );
        return false;
    }

    /**
     * 라이센스 키 이메일 전송
     */
    private function send_license_email( $order, $licenses ) {
        $to = $order->get_billing_email();
        $subject = sprintf( __( '[ACF CSS Manager] 라이센스 키 발급 완료 - 주문 #%s', 'acf-css-woo-license' ), $order->get_id() );
        
        $message = sprintf( __( '안녕하세요 %s님,', 'acf-css-woo-license' ), $order->get_billing_first_name() ) . "\n\n";
        $message .= __( 'ACF CSS Manager 라이센스가 성공적으로 발급되었습니다.', 'acf-css-woo-license' ) . "\n\n";
        
        foreach ( $licenses as $license ) {
            $message .= sprintf( "상품: %s\n", $license['product_name'] );
            $message .= sprintf( "에디션: %s\n", strtoupper( $license['edition'] ) );
            $message .= sprintf( "라이센스 키: %s\n", $license['license_key'] );
            $message .= "\n";
        }
        
        $message .= __( '라이센스 활성화 방법:', 'acf-css-woo-license' ) . "\n";
        $message .= "1. WordPress 관리자 > 설정 > ACF CSS Manager\n";
        $message .= "2. '라이센스' 탭 클릭\n";
        $message .= "3. 위 라이센스 키 입력 후 '활성화' 버튼 클릭\n\n";
        
        $message .= __( '감사합니다.', 'acf-css-woo-license' ) . "\n";
        $message .= "J&J Labs";
        
        wp_mail( $to, $subject, $message );
    }

    /**
     * 주문 완료 페이지에 라이센스 키 표시
     */
    public function display_license_key_on_thankyou( $order_id ) {
        $order = wc_get_order( $order_id );
        
        if ( ! $order ) {
            return;
        }
        
        $licenses = $order->get_meta( '_acf_css_licenses' );
        
        if ( empty( $licenses ) ) {
            return;
        }
        
        ?>
        <section class="woocommerce-acf-css-licenses">
            <h2><?php _e( '🔑 ACF CSS 라이센스 키', 'acf-css-woo-license' ); ?></h2>
            <p><?php _e( '아래 라이센스 키를 복사하여 플러그인에서 활성화하세요.', 'acf-css-woo-license' ); ?></p>
            
            <table class="woocommerce-table shop_table">
                <thead>
                    <tr>
                        <th><?php _e( '상품', 'acf-css-woo-license' ); ?></th>
                        <th><?php _e( '에디션', 'acf-css-woo-license' ); ?></th>
                        <th><?php _e( '라이센스 키', 'acf-css-woo-license' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $licenses as $license ) : ?>
                    <tr>
                        <td><?php echo esc_html( $license['product_name'] ); ?></td>
                        <td><?php echo esc_html( strtoupper( $license['edition'] ) ); ?></td>
                        <td>
                            <code style="font-size: 1.1em; padding: 5px 10px; background: #f0f0f0; user-select: all;">
                                <?php echo esc_html( $license['license_key'] ); ?>
                            </code>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <?php
    }

    /**
     * 주문 상세 페이지에 라이센스 키 표시
     */
    public function display_license_key_in_order_details( $order ) {
        $licenses = $order->get_meta( '_acf_css_licenses' );
        
        if ( empty( $licenses ) ) {
            return;
        }
        
        ?>
        <section class="woocommerce-acf-css-licenses">
            <h2><?php _e( '🔑 ACF CSS 라이센스 키', 'acf-css-woo-license' ); ?></h2>
            
            <table class="woocommerce-table shop_table">
                <thead>
                    <tr>
                        <th><?php _e( '상품', 'acf-css-woo-license' ); ?></th>
                        <th><?php _e( '라이센스 키', 'acf-css-woo-license' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $licenses as $license ) : ?>
                    <tr>
                        <td><?php echo esc_html( $license['product_name'] ); ?></td>
                        <td><code><?php echo esc_html( $license['license_key'] ); ?></code></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <?php
    }
}

// 초기화
add_action( 'plugins_loaded', array( 'ACF_CSS_Woo_License', 'instance' ) );

// AJAX 핸들러: 연결 테스트
add_action( 'wp_ajax_acf_css_test_neural_link', function() {
    check_ajax_referer( 'acf_css_test_neural_link', 'nonce' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
    }
    
    $url = get_option( 'acf_css_neural_link_url', '' );
    $api_key = get_option( 'acf_css_neural_link_api_key', '' );
    
    if ( empty( $url ) || empty( $api_key ) ) {
        wp_send_json_error( array( 'message' => 'Neural Link URL 또는 API Key가 설정되지 않았습니다.' ) );
    }
    
    $endpoint = trailingslashit( $url ) . 'wp-json/acf-neural-link/v1/ping';
    
    $response = wp_remote_get( $endpoint, array(
        'timeout' => 10,
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
        ),
        'sslverify' => true,
    ) );
    
    if ( is_wp_error( $response ) ) {
        wp_send_json_error( array( 'message' => '연결 실패: ' . $response->get_error_message() ) );
    }
    
    $code = wp_remote_retrieve_response_code( $response );
    
    if ( $code === 200 ) {
        wp_send_json_success( array( 'message' => 'Neural Link 서버 연결 성공!' ) );
    } else {
        wp_send_json_error( array( 'message' => '서버 응답 오류: HTTP ' . $code ) );
    }
} );

// 추가 모듈 로드
if ( file_exists( __DIR__ . '/includes/class-portone-webhook.php' ) ) {
    require_once __DIR__ . '/includes/class-portone-webhook.php';
}

if ( file_exists( __DIR__ . '/includes/class-woo-myaccount-licenses.php' ) ) {
    require_once __DIR__ . '/includes/class-woo-myaccount-licenses.php';
}

if ( file_exists( __DIR__ . '/includes/class-coupon-generator.php' ) ) {
    require_once __DIR__ . '/includes/class-coupon-generator.php';
}


```

```javascript
/**
 * JJ Style Guide - Palette Presets & Inline Preview
 *
 * 목표:
 * - 텍스트 위주의 팔레트 UI를 "보여주는" UI로 보강
 * - 2-Color 조합(Primary/Secondary) + 권장 System 팔레트까지 프리셋으로 제공
 * - 클릭 1~2번으로 즉시 적용 + 즉시 미리보기
 *
 * 주의:
 * - 기존 `jj-style-guide-editor.js`의 currentSettings/필드 바인딩 로직을 존중합니다.
 * - 실제 값 적용은 `data-setting-key` 필드 값을 변경(trigger change)하는 방식으로 통일합니다.
 */
(function($){
  'use strict';

  function normalizeHex(hex) {
    if (!hex || typeof hex !== 'string') return '';
    var h = hex.trim();
    if (!h) return '';
    if (h[0] !== '#') h = '#' + h;
    if (h.length === 4) {
      // #RGB -> #RRGGBB
      h = '#' + h[1] + h[1] + h[2] + h[2] + h[3] + h[3];
    }
    return h.toUpperCase();
  }

  function clamp(n, min, max) { return Math.max(min, Math.min(max, n)); }

  function hexToRgb(hex) {
    var h = normalizeHex(hex);
    if (!h || h.length !== 7) return null;
    return {
      r: parseInt(h.slice(1, 3), 16),
      g: parseInt(h.slice(3, 5), 16),
      b: parseInt(h.slice(5, 7), 16),
    };
  }

  function rgbToHex(r, g, b) {
    var rr = clamp(Math.round(r), 0, 255).toString(16).padStart(2, '0');
    var gg = clamp(Math.round(g), 0, 255).toString(16).padStart(2, '0');
    var bb = clamp(Math.round(b), 0, 255).toString(16).padStart(2, '0');
    return ('#' + rr + gg + bb).toUpperCase();
  }

  // amount: -1.0 ~ +1.0 (음수: 어둡게, 양수: 밝게)
  function shade(hex, amount) {
    var rgb = hexToRgb(hex);
    if (!rgb) return '';
    var t = amount < 0 ? 0 : 255;
    var p = Math.abs(amount);
    return rgbToHex(
      (t - rgb.r) * p + rgb.r,
      (t - rgb.g) * p + rgb.g,
      (t - rgb.b) * p + rgb.b
    );
  }

  function setField(settingKey, value) {
    if (!settingKey) return;
    var v = normalizeHex(value);
    var $field = $('#jj-style-guide-form').find('[data-setting-key="' + settingKey + '"]');
    if (!$field.length) return;

    $field.val(v).trigger('change');
    // wpColorPicker가 있으면 색상도 동기화
    if ($field.closest('.wp-picker-container').length) {
      try { $field.wpColorPicker('color', v); } catch (e) {}
    }
    // 카드 미리보기(기존 DOM)
    var $preview = $field.closest('.jj-control-group').find('.jj-color-preview').first();
    if ($preview.length) $preview.css('background-color', v);
  }

  function readField(settingKey, fallback) {
    var $field = $('#jj-style-guide-form').find('[data-setting-key="' + settingKey + '"]');
    var v = $field.length ? normalizeHex($field.val()) : '';
    return v || normalizeHex(fallback) || '';
  }

  function updateInlinePreview() {
    var $preview = $('#jj-palette-inline-preview');
    if (!$preview.length) return;

    var primary = readField('palettes[brand][primary_color]', '#2271B1');
    var primaryHover = readField('palettes[brand][primary_color_hover]', shade(primary, -0.12));
    var secondary = readField('palettes[brand][secondary_color]', '#444444');
    var secondaryHover = readField('palettes[brand][secondary_color_hover]', shade(secondary, -0.12));

    var bg = readField('palettes[system][site_bg]', '#F6F7F7');
    var surface = readField('palettes[system][content_bg]', '#FFFFFF');
    var text = readField('palettes[system][text_color]', '#1D2327');
    var link = readField('palettes[system][link_color]', primary);

    $preview.css('--jj-prev-bg', bg);
    $preview.css('--jj-prev-surface', surface);
    $preview.css('--jj-prev-text', text);
    $preview.css('--jj-prev-link', link);
    $preview.css('--jj-prev-primary', primary);
    $preview.css('--jj-prev-primary-hover', primaryHover);
    $preview.css('--jj-prev-secondary', secondary);
    $preview.css('--jj-prev-secondary-hover', secondaryHover);

    $preview.find('[data-jj-color="primary"]').text(primary);
    $preview.find('[data-jj-color="secondary"]').text(secondary);
    $preview.find('[data-jj-color="bg"]').text(bg);
    $preview.find('[data-jj-color="text"]').text(text);
  }

  $(document).ready(function(){
    var $mount = $('#jj-palette-presets');
    if (!$mount.length) return;

    var PRESETS = [
      {
        id: 'luxury-black-gold',
        name: 'Black & Gold',
        tags: ['럭셔리', '다크', '고대비', '프리미엄'],
        note: '고급·권위·집중(CTA 강함). 법률/컨설팅/하이엔드에 적합.',
        brand: { primary: '#C9A227', secondary: '#0B0D0F' },
        system: { site_bg: '#0B0D0F', content_bg: '#101418', text_color: '#EAEAEA', link_color: '#C9A227' }
      },
      {
        id: 'trust-navy-cyan',
        name: 'Navy & Cyan',
        tags: ['신뢰', '테크', '차분함'],
        note: '신뢰·안정·정교함. SaaS/데이터/기업 사이트에 적합.',
        brand: { primary: '#1F3C88', secondary: '#00B8D9' },
        system: { site_bg: '#F7FAFC', content_bg: '#FFFFFF', text_color: '#1D2327', link_color: '#1F3C88' }
      },
      {
        id: 'modern-coral-slate',
        name: 'Coral & Slate',
        tags: ['모던', '따뜻함', '크리에이티브'],
        note: '친근·활기. 브랜딩/포트폴리오/커머스에 적합.',
        brand: { primary: '#FF6B6B', secondary: '#334E68' },
        system: { site_bg: '#FFFFFF', content_bg: '#F7FAFC', text_color: '#102A43', link_color: '#334E68' }
      },
      {
        id: 'minimal-mono',
        name: 'Minimal Mono',
        tags: ['미니멀', '라이트', '가독성'],
        note: '콘텐츠 중심·가독성 강화. 블로그/매거진에 적합.',
        brand: { primary: '#111827', secondary: '#6B7280' },
        system: { site_bg: '#F9FAFB', content_bg: '#FFFFFF', text_color: '#111827', link_color: '#111827' }
      },
      {
        id: 'emerald-charcoal',
        name: 'Emerald & Charcoal',
        tags: ['헬스', '자연', '안정'],
        note: '안정·신선. 헬스/웰니스/교육에 적합.',
        brand: { primary: '#10B981', secondary: '#111827' },
        system: { site_bg: '#F7FAFC', content_bg: '#FFFFFF', text_color: '#111827', link_color: '#10B981' }
      },
      {
        id: 'royal-purple-ice',
        name: 'Royal Purple',
        tags: ['프리미엄', '창의', '브랜딩'],
        note: '독창·세련. 이벤트/크리에이티브 스튜디오에 적합.',
        brand: { primary: '#6D28D9', secondary: '#0EA5E9' },
        system: { site_bg: '#F8FAFC', content_bg: '#FFFFFF', text_color: '#0F172A', link_color: '#6D28D9' }
      },
      {
        id: 'warm-brown-cream',
        name: 'Brown & Cream',
        tags: ['따뜻함', '클래식', '브랜드'],
        note: '따뜻·신뢰. 카페/식품/라이프스타일에 적합.',
        brand: { primary: '#8B5E34', secondary: '#D4A373' },
        system: { site_bg: '#FFF7ED', content_bg: '#FFFFFF', text_color: '#1F2937', link_color: '#8B5E34' }
      },
      {
        id: 'contrast-blue-yellow',
        name: 'Blue & Yellow (CTA)',
        tags: ['고대비', '전환', 'CTA'],
        note: '버튼/링크가 눈에 띄게. 랜딩/광고 페이지에 적합.',
        brand: { primary: '#2563EB', secondary: '#F59E0B' },
        system: { site_bg: '#FFFFFF', content_bg: '#F8FAFC', text_color: '#0F172A', link_color: '#2563EB' }
      }
    ];

    // hover 자동 생성(없는 경우)
    PRESETS.forEach(function(p){
      p.brand.primary = normalizeHex(p.brand.primary);
      p.brand.secondary = normalizeHex(p.brand.secondary);
      p.brand.primary_hover = normalizeHex(p.brand.primary_hover || shade(p.brand.primary, -0.12));
      p.brand.secondary_hover = normalizeHex(p.brand.secondary_hover || shade(p.brand.secondary, -0.12));
      p.system.site_bg = normalizeHex(p.system.site_bg);
      p.system.content_bg = normalizeHex(p.system.content_bg);
      p.system.text_color = normalizeHex(p.system.text_color);
      p.system.link_color = normalizeHex(p.system.link_color || p.brand.primary);
    });

    var selectedId = null;

    function render(filterText) {
      var q = (filterText || '').toLowerCase().trim();
      $mount.empty();

      var $toolbar = $('<div class="jj-preset-toolbar"></div>');
      var $search = $('<input type="search" class="jj-preset-search" placeholder="프리셋 검색 (예: 럭셔리, 신뢰, 다크)"/>');
      $search.val(filterText || '');

      var $includeSystem = $('<label class="jj-preset-include-system"><input type="checkbox" checked> 시스템 팔레트도 함께 적용</label>');
      var $includeComponents = $('<label class="jj-preset-include-system"><input type="checkbox" checked> 버튼/폼/링크 자동 맞추기</label>');

      $toolbar.append($search).append($includeSystem).append($includeComponents);
      $mount.append($toolbar);

      var $grid = $('<div class="jj-preset-grid" role="list"></div>');
      var shown = 0;

      PRESETS.forEach(function(p){
        var hay = (p.name + ' ' + p.tags.join(' ') + ' ' + p.note).toLowerCase();
        if (q && hay.indexOf(q) === -1) return;
        shown++;

        var isActive = selectedId === p.id;
        var $card = $('<button type="button" class="jj-preset-card" role="listitem"></button>');
        $card.toggleClass('is-active', !!isActive);
        $card.attr('data-preset-id', p.id);

        var swatches = [
          { c: p.brand.primary, label: 'Primary' },
          { c: p.brand.secondary, label: 'Secondary' },
          { c: p.system.site_bg, label: 'BG' },
          { c: p.system.text_color, label: 'Text' }
        ];

        var $sw = $('<div class="jj-preset-swatches" aria-hidden="true"></div>');
        swatches.forEach(function(s){
          var $s = $('<span class="jj-preset-swatch"></span>');
          $s.css('background-color', s.c);
          $s.attr('title', s.label + ': ' + s.c);
          $sw.append($s);
        });

        var $meta = $('<div class="jj-preset-meta"></div>');
        $meta.append($('<div class="jj-preset-title"></div>').text(p.name));
        $meta.append($('<div class="jj-preset-tags"></div>').text(p.tags.join(' · ')));
        $meta.append($('<div class="jj-preset-note"></div>').text(p.note));

        $card.append($sw).append($meta);
        $grid.append($card);
      });

      if (shown === 0) {
        $grid.append($('<div class="jj-preset-empty"></div>').text('검색 결과가 없습니다.'));
      }

      var $actions = $('<div class="jj-preset-actions"></div>');
      var $apply = $('<button type="button" class="button button-primary jj-preset-apply" disabled>선택한 프리셋 적용</button>');
      var $hint = $('<span class="jj-preset-hint">클릭 → 미리보기 → 적용 (언제든 수정 가능)</span>');
      $actions.append($apply).append($hint);

      $mount.append($grid).append($actions);

      // events
      $search.on('input', function(){ render($(this).val()); });

      $grid.on('click', '.jj-preset-card', function(){
        selectedId = $(this).data('preset-id');
        render($search.val());

        // 선택 즉시 "필드에 적용하지 않고" 미리보기만 업데이트하려면 별도 상태가 필요합니다.
        // 현재 단계에서는 선택 후 적용 버튼을 누르게 하고, 인라인 프리뷰는 현재 필드값을 보여줍니다.
      });

      $apply.prop('disabled', !selectedId);
      $apply.on('click', function(){
        var preset = PRESETS.find(function(x){ return x.id === selectedId; });
        if (!preset) return;

        var includeSystem = $includeSystem.find('input').is(':checked');
        var includeComponents = $includeComponents.find('input').is(':checked');

        // brand
        setField('palettes[brand][primary_color]', preset.brand.primary);
        setField('palettes[brand][primary_color_hover]', preset.brand.primary_hover);
        setField('palettes[brand][secondary_color]', preset.brand.secondary);
        setField('palettes[brand][secondary_color_hover]', preset.brand.secondary_hover);

        // system (옵션)
        if (includeSystem) {
          setField('palettes[system][site_bg]', preset.system.site_bg);
          setField('palettes[system][content_bg]', preset.system.content_bg);
          setField('palettes[system][text_color]', preset.system.text_color);
          setField('palettes[system][link_color]', preset.system.link_color);
        }

        // 미리보기 갱신
        updateInlinePreview();

        // 버튼/폼/링크 자동 매핑 (기존 "일괄 적용" 로직 재사용)
        if (includeComponents) {
          var $btn = $('#jj-style-guide-form').find('.jj-apply-brand-palette-to-components').first();
          if ($btn.length) {
            $btn.trigger('click');
          }
        }
      });
    }

    render('');
    updateInlinePreview();

    // 색상 변경 시 미리보기 자동 갱신
    $('#jj-style-guide-form').on('change input', '.jj-color-field', function(){
      updateInlinePreview();
    });
  });
})(jQuery);


```

```php
*** Begin Patch
*** Update File: acf-css-really-simple-style-management-center-master/includes/admin/views/tabs/tab-license.php
@@
-    // 마스터 버전 체크 (Dev 환경)
-    $is_master_version = ( defined( 'JJ_STYLE_GUIDE_ENV' ) && 'dev' === JJ_STYLE_GUIDE_ENV );
+    // Partner/Master(내부/파트너용) 판별: 업그레이드 유도 UI 금지, 발급 도구 노출
+    $is_partner_or_higher = false;
+    if ( class_exists( 'JJ_Edition_Controller' ) ) {
+        try {
+            $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
+        } catch ( Exception $e ) {
+            // ignore
+        } catch ( Error $e ) {
+            // ignore
+        }
+    } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
+        $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
+    } elseif ( is_string( $license_type ) ) {
+        $is_partner_or_higher = in_array( $license_type, array( 'PARTNER', 'MASTER' ), true );
+    }
     ?>
@@
-        <?php if ( $is_master_version ) : ?>
+        <?php if ( $is_partner_or_higher ) : ?>
         <div class="jj-master-license-management" style="margin-bottom: 30px; padding: 20px; background: #f0f6fc; border: 1px solid #c3c4c7; border-radius: 4px;">
@@
-                <?php if ( ! $is_master_version ) : ?>
+                <?php if ( ! $is_partner_or_higher ) : ?>
                 <div style="text-align: right;">
@@
                             if ( $license_type === 'BASIC' ) { $type_bg = '#2271b1'; $type_color = '#fff'; }
                             elseif ( $license_type === 'PREM' ) { $type_bg = '#0073aa'; $type_color = '#fff'; }
                             elseif ( $license_type === 'UNLIM' ) { $type_bg = '#8b5cf6'; $type_color = '#fff'; }
+                            elseif ( $license_type === 'PARTNER' ) { $type_bg = '#0ea5e9'; $type_color = '#fff'; }
+                            elseif ( $license_type === 'MASTER' ) { $type_bg = '#c0392b'; $type_color = '#fff'; }
                             ?>
*** End Patch
```

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="jj-admin-center-tab-content" data-tab="license">
    <?php
    $license_manager = null;
    $current_license_key = get_option( 'jj_style_guide_license_key', '' );
    if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
        require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $license_manager = JJ_License_Manager::instance();
        }
    }
    
    // 라이센스 매니저가 없으면 안내 메시지
    if ( ! $license_manager ) {
        echo '<div class="notice notice-error inline"><p>' . esc_html__( '라이센스 관리 모듈을 로드할 수 없습니다.', 'jj-style-guide' ) . '</p></div>';
        return;
    }
    
    // 현재 상태 조회
    $license_status = $license_manager->get_license_status();
    $license_type = $license_manager->get_current_license_type();
    
    // 결제/연장 링크 결정
    $is_expired = false;
    $action_text = __( '업그레이드', 'jj-style-guide' );
    
    if ( isset( $license_status['valid_until'] ) && $license_status['valid_until'] < time() ) {
        $is_expired = true;
        $action_text = __( '기한 연장', 'jj-style-guide' );
    } elseif ( ! $license_status['valid'] && ! empty( $current_license_key ) ) {
        if ( in_array( $license_type, array( 'BASIC', 'PREM', 'UNLIM' ) ) ) {
            $is_expired = true;
            $action_text = __( '기한 연장', 'jj-style-guide' );
        }
    }
    $purchase_url = $license_manager->get_purchase_url( $is_expired ? 'renew' : 'upgrade' );
    
    // Partner/Master(내부/파트너용) 판별: 업그레이드 유도 UI 금지, 발급 도구 노출
    $is_partner_or_higher = false;
    if ( class_exists( 'JJ_Edition_Controller' ) ) {
        try {
            $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
        } catch ( Exception $e ) {
            // ignore
        } catch ( Error $e ) {
            // ignore
        }
    } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
        $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
    } elseif ( is_string( $license_type ) ) {
        $is_partner_or_higher = in_array( $license_type, array( 'PARTNER', 'MASTER' ), true );
    }
    ?>
    
    <div class="jj-license-settings">
        <h3><?php esc_html_e( '라이센스 관리 (License Management)', 'jj-style-guide' ); ?></h3>
        <p class="description">
            <?php esc_html_e( '제품 정품 인증을 통해 모든 기능을 활성화하고 자동 업데이트를 받으세요.', 'jj-style-guide' ); ?>
        </p>

        <!-- [마스터 버전 전용 도구] -->
        <?php if ( $is_partner_or_higher ) : ?>
        <div class="jj-master-license-management" style="margin-bottom: 30px; padding: 20px; background: #f0f6fc; border: 1px solid #c3c4c7; border-radius: 4px;">
            <h4 style="margin-top: 0; display: flex; align-items: center; gap: 8px;">
                <span class="dashicons dashicons-shield-alt"></span>
                <?php esc_html_e( '마스터 권한: 라이센스 발급 도구', 'jj-style-guide' ); ?>
            </h4>
            <p class="description">
                <?php esc_html_e( '라이센스 키를 생성, 검증, 관리할 수 있는 발급자(Issuer) 패널로 이동합니다.', 'jj-style-guide' ); ?>
            </p>
            <p class="submit" style="margin-bottom: 0;">
                <a href="<?php echo esc_url( admin_url( 'options-general.php?page=jj-license-issuer' ) ); ?>" class="button button-primary">
                    <?php esc_html_e( '라이센스 발급 관리자 이동', 'jj-style-guide' ); ?>
                </a>
            </p>
        </div>
        <?php endif; ?>

        <!-- [현재 라이센스 상태 카드] -->
        <div class="jj-license-status-card" style="margin-bottom: 30px; padding: 25px; background: #fff; border: 1px solid #dcdcde; border-left: 4px solid <?php echo $license_status['valid'] ? '#00a32a' : '#d63638'; ?>; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                <div style="flex: 1;">
                    <h4 style="margin-top: 0; margin-bottom: 15px; font-size: 16px;">
                        <?php esc_html_e( '현재 라이센스 상태', 'jj-style-guide' ); ?>
                    </h4>
                    
                    <div style="display: grid; grid-template-columns: auto 1fr; gap: 15px; align-items: center;">
                        <!-- 상태 -->
                        <div style="font-weight: 600; color: #646970;"><?php esc_html_e( '활성화 상태:', 'jj-style-guide' ); ?></div>
                        <div>
                            <?php if ( $license_status['valid'] ) : ?>
                                <span class="jj-status-badge active" style="background: #00a32a; color: #fff; padding: 4px 10px; border-radius: 4px; font-weight: 600; font-size: 12px;">
                                    <span class="dashicons dashicons-yes" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span>
                                    <?php esc_html_e( '정품 인증됨', 'jj-style-guide' ); ?>
                                </span>
                            <?php else : ?>
                                <span class="jj-status-badge inactive" style="background: #d63638; color: #fff; padding: 4px 10px; border-radius: 4px; font-weight: 600; font-size: 12px;">
                                    <span class="dashicons dashicons-warning" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span>
                                    <?php esc_html_e( '인증되지 않음', 'jj-style-guide' ); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- 에디션 -->
                        <div style="font-weight: 600; color: #646970;"><?php esc_html_e( '에디션:', 'jj-style-guide' ); ?></div>
                        <div>
                            <?php
                            $type_bg = '#e5e5e5'; // Free
                            $type_color = '#50575e';
                            if ( $license_type === 'BASIC' ) { $type_bg = '#2271b1'; $type_color = '#fff'; }
                            elseif ( $license_type === 'PREM' ) { $type_bg = '#0073aa'; $type_color = '#fff'; }
                            elseif ( $license_type === 'UNLIM' ) { $type_bg = '#8b5cf6'; $type_color = '#fff'; }
                            elseif ( $license_type === 'PARTNER' ) { $type_bg = '#0ea5e9'; $type_color = '#fff'; }
                            elseif ( $license_type === 'MASTER' ) { $type_bg = '#c0392b'; $type_color = '#fff'; }
                            ?>
                            <span class="jj-license-type-badge" style="background: <?php echo esc_attr( $type_bg ); ?>; color: <?php echo esc_attr( $type_color ); ?>; padding: 4px 10px; border-radius: 4px; font-weight: 600; font-size: 12px;">
                                <?php echo esc_html( $license_type ); ?>
                            </span>
                        </div>

                        <!-- 만료일 -->
                        <?php if ( isset( $license_status['valid_until'] ) && $license_status['valid_until'] > 0 ) : ?>
                        <div style="font-weight: 600; color: #646970;"><?php esc_html_e( '만료일:', 'jj-style-guide' ); ?></div>
                        <div>
                            <?php 
                            $days_left = ceil( ( $license_status['valid_until'] - time() ) / ( 60 * 60 * 24 ) );
                            echo date_i18n( 'Y년 m월 d일', $license_status['valid_until'] );
                            
                            if ( $days_left <= 30 ) {
                                echo ' <span style="color: #d63638; font-weight: bold;">(' . sprintf( __( '%d일 남음', 'jj-style-guide' ), $days_left ) . ')</span>';
                            }
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 업그레이드 버튼 -->
                <?php if ( ! $is_partner_or_higher ) : ?>
                <div style="text-align: right;">
                    <a href="<?php echo esc_url( $purchase_url ); ?>" target="_blank" class="button button-primary" style="height: 36px; line-height: 34px; font-size: 14px; padding: 0 20px;">
                        <?php echo esc_html( $action_text ); ?>
                        <span class="dashicons dashicons-external" style="font-size: 16px; margin-left: 4px;"></span>
                    </a>
                    <p class="description" style="margin-top: 8px; font-size: 12px;">
                        <?php esc_html_e( '더 많은 기능을 잠금 해제하세요.', 'jj-style-guide' ); ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- [라이센스 키 입력 폼] -->
        <div class="jj-license-input-box" style="padding: 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px;">
            <h4 style="margin-top: 0; margin-bottom: 15px; font-size: 15px;"><?php esc_html_e( '라이센스 키 입력', 'jj-style-guide' ); ?></h4>
            
            <form id="jj-license-key-form" method="post">
                <div style="display: flex; gap: 10px; max-width: 600px;">
                    <input type="text" 
                           id="jj-license-key-input" 
                           name="license_key" 
                           class="large-text" 
                           value="<?php echo esc_attr( $current_license_key ); ?>" 
                           placeholder="JJ-XXXX-XXXX-XXXX-XXXX"
                           style="font-family: monospace; letter-spacing: 1px; font-size: 14px; padding: 8px 12px; height: 40px;">
                    
                    <button type="submit" class="button button-primary button-large" id="jj-save-license-btn" style="height: 40px; padding: 0 20px;">
                        <?php esc_html_e( '저장 및 검증', 'jj-style-guide' ); ?>
                    </button>
                </div>
                <p class="description" style="margin-top: 8px;">
                    <?php esc_html_e( '구매 확인 이메일에 포함된 라이센스 키를 입력하세요.', 'jj-style-guide' ); ?>
                </p>
                
                <!-- 검증 결과 메시지 -->
                <div id="jj-license-message" style="margin-top: 15px; padding: 12px; border-radius: 4px; display: none;"></div>
            </form>
        </div>

        <?php if ( ! empty( $current_license_key ) ) : ?>
        <!-- [초기화 버튼] -->
        <div style="margin-top: 20px; text-align: right;">
            <button type="button" class="button button-link-delete" id="jj-remove-license-btn">
                <?php esc_html_e( '라이센스 키 제거 및 초기화', 'jj-style-guide' ); ?>
            </button>
        </div>
        <?php endif; ?>
    </div>
</div>

```

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="jj-admin-center-tab-content" data-tab="license">
    <?php
    $license_manager = null;
    $current_license_key = get_option( 'jj_style_guide_license_key', '' );
    if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
        require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $license_manager = JJ_License_Manager::instance();
        }
    }
    
    // 라이센스 매니저가 없으면 안내 메시지
    if ( ! $license_manager ) {
        echo '<div class="notice notice-error inline"><p>' . esc_html__( '라이센스 관리 모듈을 로드할 수 없습니다.', 'jj-style-guide' ) . '</p></div>';
        return;
    }
    
    // 현재 상태 조회
    $license_status = $license_manager->get_license_status();
    $license_type = $license_manager->get_current_license_type();
    
    // 결제/연장 링크 결정
    $is_expired = false;
    $action_text = __( '업그레이드', 'jj-style-guide' );
    
    if ( isset( $license_status['valid_until'] ) && $license_status['valid_until'] < time() ) {
        $is_expired = true;
        $action_text = __( '기한 연장', 'jj-style-guide' );
    } elseif ( ! $license_status['valid'] && ! empty( $current_license_key ) ) {
        if ( in_array( $license_type, array( 'BASIC', 'PREM', 'UNLIM' ) ) ) {
            $is_expired = true;
            $action_text = __( '기한 연장', 'jj-style-guide' );
        }
    }
    $purchase_url = $license_manager->get_purchase_url( $is_expired ? 'renew' : 'upgrade' );
    
    // Partner/Master 판별:
    // - Partner/Master: 업그레이드 유도 UI 금지
    // - Master: 라이센스 발급 도구 노출
    $is_partner_or_higher = false;
    $is_master = false;
    if ( class_exists( 'JJ_Edition_Controller' ) ) {
        try {
            $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            $is_master = JJ_Edition_Controller::instance()->is_at_least( 'master' );
        } catch ( Exception $e ) {
            // ignore
        } catch ( Error $e ) {
            // ignore
        }
    } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
        $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        $is_master = ( 'MASTER' === JJ_STYLE_GUIDE_LICENSE_TYPE );
    } elseif ( is_string( $license_type ) ) {
        $is_partner_or_higher = in_array( $license_type, array( 'PARTNER', 'MASTER' ), true );
        $is_master = ( 'MASTER' === $license_type );
    }
    ?>
    
    <div class="jj-license-settings">
        <h3><?php esc_html_e( '라이센스 관리 (License Management)', 'jj-style-guide' ); ?></h3>
        <p class="description">
            <?php esc_html_e( '제품 정품 인증을 통해 모든 기능을 활성화하고 자동 업데이트를 받으세요.', 'jj-style-guide' ); ?>
        </p>

        <!-- [마스터 버전 전용 도구] -->
        <?php if ( $is_master ) : ?>
        <div class="jj-master-license-management" style="margin-bottom: 30px; padding: 20px; background: #f0f6fc; border: 1px solid #c3c4c7; border-radius: 4px;">
            <h4 style="margin-top: 0; display: flex; align-items: center; gap: 8px;">
                <span class="dashicons dashicons-shield-alt"></span>
                <?php esc_html_e( '마스터 권한: 라이센스 발급 도구', 'jj-style-guide' ); ?>
            </h4>
            <p class="description">
                <?php esc_html_e( '라이센스 키를 생성, 검증, 관리할 수 있는 발급자(Issuer) 패널로 이동합니다.', 'jj-style-guide' ); ?>
            </p>
            <p class="submit" style="margin-bottom: 0;">
                <a href="<?php echo esc_url( admin_url( 'options-general.php?page=jj-license-issuer' ) ); ?>" class="button button-primary">
                    <?php esc_html_e( '라이센스 발급 관리자 이동', 'jj-style-guide' ); ?>
                </a>
            </p>
        </div>
        <?php endif; ?>

        <!-- [현재 라이센스 상태 카드] -->
        <div class="jj-license-status-card" style="margin-bottom: 30px; padding: 25px; background: #fff; border: 1px solid #dcdcde; border-left: 4px solid <?php echo $license_status['valid'] ? '#00a32a' : '#d63638'; ?>; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                <div style="flex: 1;">
                    <h4 style="margin-top: 0; margin-bottom: 15px; font-size: 16px;">
                        <?php esc_html_e( '현재 라이센스 상태', 'jj-style-guide' ); ?>
                    </h4>
                    
                    <div style="display: grid; grid-template-columns: auto 1fr; gap: 15px; align-items: center;">
                        <!-- 상태 -->
                        <div style="font-weight: 600; color: #646970;"><?php esc_html_e( '활성화 상태:', 'jj-style-guide' ); ?></div>
                        <div>
                            <?php if ( $license_status['valid'] ) : ?>
                                <span class="jj-status-badge active" style="background: #00a32a; color: #fff; padding: 4px 10px; border-radius: 4px; font-weight: 600; font-size: 12px;">
                                    <span class="dashicons dashicons-yes" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span>
                                    <?php esc_html_e( '정품 인증됨', 'jj-style-guide' ); ?>
                                </span>
                            <?php else : ?>
                                <span class="jj-status-badge inactive" style="background: #d63638; color: #fff; padding: 4px 10px; border-radius: 4px; font-weight: 600; font-size: 12px;">
                                    <span class="dashicons dashicons-warning" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span>
                                    <?php esc_html_e( '인증되지 않음', 'jj-style-guide' ); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- 에디션 -->
                        <div style="font-weight: 600; color: #646970;"><?php esc_html_e( '에디션:', 'jj-style-guide' ); ?></div>
                        <div>
                            <?php
                            $type_bg = '#e5e5e5'; // Free
                            $type_color = '#50575e';
                            if ( $license_type === 'BASIC' ) { $type_bg = '#2271b1'; $type_color = '#fff'; }
                            elseif ( $license_type === 'PREM' ) { $type_bg = '#0073aa'; $type_color = '#fff'; }
                            elseif ( $license_type === 'UNLIM' ) { $type_bg = '#8b5cf6'; $type_color = '#fff'; }
                            elseif ( $license_type === 'PARTNER' ) { $type_bg = '#0ea5e9'; $type_color = '#fff'; }
                            elseif ( $license_type === 'MASTER' ) { $type_bg = '#c0392b'; $type_color = '#fff'; }
                            ?>
                            <span class="jj-license-type-badge" style="background: <?php echo esc_attr( $type_bg ); ?>; color: <?php echo esc_attr( $type_color ); ?>; padding: 4px 10px; border-radius: 4px; font-weight: 600; font-size: 12px;">
                                <?php echo esc_html( $license_type ); ?>
                            </span>
                        </div>

                        <!-- 만료일 -->
                        <?php if ( isset( $license_status['valid_until'] ) && $license_status['valid_until'] > 0 ) : ?>
                        <div style="font-weight: 600; color: #646970;"><?php esc_html_e( '만료일:', 'jj-style-guide' ); ?></div>
                        <div>
                            <?php 
                            $days_left = ceil( ( $license_status['valid_until'] - time() ) / ( 60 * 60 * 24 ) );
                            echo date_i18n( 'Y년 m월 d일', $license_status['valid_until'] );
                            
                            if ( $days_left <= 30 ) {
                                echo ' <span style="color: #d63638; font-weight: bold;">(' . sprintf( __( '%d일 남음', 'jj-style-guide' ), $days_left ) . ')</span>';
                            }
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 업그레이드 버튼 -->
                <?php if ( ! $is_partner_or_higher ) : ?>
                <div style="text-align: right;">
                    <a href="<?php echo esc_url( $purchase_url ); ?>" target="_blank" class="button button-primary" style="height: 36px; line-height: 34px; font-size: 14px; padding: 0 20px;">
                        <?php echo esc_html( $action_text ); ?>
                        <span class="dashicons dashicons-external" style="font-size: 16px; margin-left: 4px;"></span>
                    </a>
                    <p class="description" style="margin-top: 8px; font-size: 12px;">
                        <?php esc_html_e( '더 많은 기능을 잠금 해제하세요.', 'jj-style-guide' ); ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- [라이센스 키 입력 폼] -->
        <div class="jj-license-input-box" style="padding: 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px;">
            <h4 style="margin-top: 0; margin-bottom: 15px; font-size: 15px;"><?php esc_html_e( '라이센스 키 입력', 'jj-style-guide' ); ?></h4>
            
            <form id="jj-license-key-form" method="post">
                <div style="display: flex; gap: 10px; max-width: 600px;">
                    <input type="text" 
                           id="jj-license-key-input" 
                           name="license_key" 
                           class="large-text" 
                           value="<?php echo esc_attr( $current_license_key ); ?>" 
                           placeholder="JJ-XXXX-XXXX-XXXX-XXXX"
                           style="font-family: monospace; letter-spacing: 1px; font-size: 14px; padding: 8px 12px; height: 40px;">
                    
                    <button type="submit" class="button button-primary button-large" id="jj-save-license-btn" style="height: 40px; padding: 0 20px;">
                        <?php esc_html_e( '저장 및 검증', 'jj-style-guide' ); ?>
                    </button>
                </div>
                <p class="description" style="margin-top: 8px;">
                    <?php esc_html_e( '구매 확인 이메일에 포함된 라이센스 키를 입력하세요.', 'jj-style-guide' ); ?>
                </p>
                
                <!-- 검증 결과 메시지 -->
                <div id="jj-license-message" style="margin-top: 15px; padding: 12px; border-radius: 4px; display: none;"></div>
            </form>
        </div>

        <?php if ( ! empty( $current_license_key ) ) : ?>
        <!-- [초기화 버튼] -->
        <div style="margin-top: 20px; text-align: right;">
            <button type="button" class="button button-link-delete" id="jj-remove-license-btn">
                <?php esc_html_e( '라이센스 키 제거 및 초기화', 'jj-style-guide' ); ?>
            </button>
        </div>
        <?php endif; ?>
    </div>
</div>

```

```php
<?php
/**
 * J&J 라이센스 발행 관리자 (마스터 플러그인 전용)
 * 
 * 라이센스 키 생성, 발행, 활성화 상태 확인, 비활성화 등을 관리합니다.
 * 마스터 버전에서만 사용 가능한 기능입니다.
 * 
 * @version 3.8.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

final class JJ_License_Issuer {

    private static $instance = null;
    private $licenses_option_key = 'jj_style_guide_issued_licenses';
    private $license_activations_key = 'jj_style_guide_license_activations';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 마스터 버전 체크
        if ( ! $this->is_master_version() ) {
            return;
        }

        add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_generate_license', array( $this, 'ajax_generate_license' ) );
        add_action( 'wp_ajax_jj_deactivate_license', array( $this, 'ajax_deactivate_license' ) );
        add_action( 'wp_ajax_jj_check_license_status', array( $this, 'ajax_check_license_status' ) );
        add_action( 'wp_ajax_jj_delete_license', array( $this, 'ajax_delete_license' ) );
    }

    /**
     * 마스터 버전 여부 확인
     * 
     * @return bool
     */
    private function is_master_version() {
        // Master 에디션에서만 활성화 (Partner는 제외)
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                return JJ_Edition_Controller::instance()->is_at_least( 'master' );
            } catch ( Exception $e ) {
                return false;
            } catch ( Error $e ) {
                return false;
            }
        }
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            return ( 'MASTER' === JJ_STYLE_GUIDE_LICENSE_TYPE );
        }
        return false;
    }

    /**
     * 관리자 메뉴 페이지 추가
     */
    public function add_admin_menu_page() {
        if ( ! $this->is_master_version() ) {
            return;
        }

        add_submenu_page(
            'options-general.php',
            __( '라이센스 발행 관리', 'jj-style-guide' ),
            __( '라이센스 발행 관리', 'jj-style-guide' ),
            'manage_options',
            'jj-license-issuer',
            array( $this, 'render_license_issuer_page' )
        );
    }

    /**
     * 에셋 enqueue
     */
    public function enqueue_assets( $hook ) {
        if ( 'settings_page_jj-license-issuer' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'jj-license-issuer',
            JJ_STYLE_GUIDE_URL . 'assets/css/jj-license-issuer.css',
            array(),
            JJ_STYLE_GUIDE_VERSION
        );

        wp_enqueue_script(
            'jj-license-issuer',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-license-issuer.js',
            array( 'jquery' ),
            JJ_STYLE_GUIDE_VERSION,
            true
        );

        wp_localize_script(
            'jj-license-issuer',
            'jjLicenseIssuer',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_license_issuer_action' ),
                'i18n'     => array(
                    'confirm_delete' => __( '정말로 이 라이센스를 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.', 'jj-style-guide' ),
                    'confirm_deactivate' => __( '정말로 이 라이센스를 비활성화하시겠습니까?', 'jj-style-guide' ),
                    'generating' => __( '라이센스 생성 중...', 'jj-style-guide' ),
                    'success' => __( '성공', 'jj-style-guide' ),
                    'error' => __( '오류', 'jj-style-guide' ),
                ),
            )
        );
    }

    /**
     * 라이센스 발행 관리 페이지 렌더링
     */
    public function render_license_issuer_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! $this->is_master_version() ) {
            ?>
            <div class="wrap">
                <h1><?php _e( '라이센스 발행 관리', 'jj-style-guide' ); ?></h1>
                <div class="notice notice-error">
                    <p><?php _e( '이 기능은 마스터 버전에서만 사용할 수 있습니다.', 'jj-style-guide' ); ?></p>
                </div>
            </div>
            <?php
            return;
        }

        $licenses = $this->get_all_licenses();
        ?>
        <div class="wrap jj-license-issuer-wrap">
            <h1><?php _e( 'J&J 라이센스 발행 관리', 'jj-style-guide' ); ?></h1>
            
            <div class="jj-license-issuer-header" style="margin: 20px 0; padding: 20px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px;">
                <h2 style="margin-top: 0;"><?php _e( '새 라이센스 발행', 'jj-style-guide' ); ?></h2>
                <form id="jj-generate-license-form" method="post">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="jj-license-type"><?php _e( '라이센스 타입', 'jj-style-guide' ); ?></label>
                                </th>
                                <td>
                                    <select id="jj-license-type" name="license_type" required style="width: 300px; padding: 8px;">
                                        <option value=""><?php _e( '선택하세요', 'jj-style-guide' ); ?></option>
                                        <option value="FREE"><?php _e( 'Free', 'jj-style-guide' ); ?></option>
                                        <option value="BASIC"><?php _e( 'Basic', 'jj-style-guide' ); ?></option>
                                        <option value="PREM"><?php _e( 'Premium', 'jj-style-guide' ); ?></option>
                                        <option value="UNLIM"><?php _e( 'Unlimited', 'jj-style-guide' ); ?></option>
                                    </select>
                                    <p class="description">
                                        <?php _e( '발행할 라이센스의 타입을 선택하세요.', 'jj-style-guide' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="jj-license-customer"><?php _e( '고객명/메모', 'jj-style-guide' ); ?></label>
                                </th>
                                <td>
                                    <input type="text" 
                                           id="jj-license-customer" 
                                           name="customer" 
                                           class="regular-text" 
                                           placeholder="<?php esc_attr_e( '예: 홍길동, example@email.com', 'jj-style-guide' ); ?>">
                                    <p class="description">
                                        <?php _e( '라이센스 구매자 정보나 메모를 입력하세요 (선택사항).', 'jj-style-guide' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="jj-license-expires"><?php _e( '만료일', 'jj-style-guide' ); ?></label>
                                </th>
                                <td>
                                    <input type="date" 
                                           id="jj-license-expires" 
                                           name="expires" 
                                           class="regular-text"
                                           min="<?php echo date( 'Y-m-d' ); ?>">
                                    <p class="description">
                                        <?php _e( '라이센스 만료일을 설정하세요. 비워두면 1년 후로 설정됩니다. (선택사항)', 'jj-style-guide' ); ?>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="submit">
                        <?php wp_nonce_field( 'jj_license_issuer_action', 'jj_license_issuer_nonce' ); ?>
                        <button type="submit" class="button button-primary button-large" id="jj-generate-license-btn">
                            <span class="dashicons dashicons-admin-network" style="margin-top: 4px;"></span>
                            <?php _e( '라이센스 생성', 'jj-style-guide' ); ?>
                        </button>
                        <span class="spinner" style="float: none; margin-left: 10px;"></span>
                    </p>
                </form>
            </div>

            <div class="jj-license-issuer-list" style="margin: 20px 0;">
                <h2><?php _e( '발행된 라이센스 목록', 'jj-style-guide' ); ?></h2>
                <div class="jj-license-stats" style="margin-bottom: 20px; padding: 15px; background: #f0f6fc; border: 1px solid #c3c4c7; border-radius: 4px;">
                    <strong><?php _e( '통계:', 'jj-style-guide' ); ?></strong>
                    <span style="margin-left: 15px;">
                        <?php
                        $stats = $this->get_license_stats();
                        printf(
                            __( '전체: %d개 | 활성화됨: %d개 | 비활성화됨: %d개 | 사용 중: %d개', 'jj-style-guide' ),
                            $stats['total'],
                            $stats['active'],
                            $stats['inactive'],
                            $stats['in_use']
                        );
                        ?>
                    </span>
                </div>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 200px;"><?php _e( '라이센스 키', 'jj-style-guide' ); ?></th>
                            <th style="width: 100px;"><?php _e( '타입', 'jj-style-guide' ); ?></th>
                            <th style="width: 150px;"><?php _e( '고객명/메모', 'jj-style-guide' ); ?></th>
                            <th style="width: 120px;"><?php _e( '상태', 'jj-style-guide' ); ?></th>
                            <th style="width: 200px;"><?php _e( '활성화된 사이트', 'jj-style-guide' ); ?></th>
                            <th style="width: 120px;"><?php _e( '발행일', 'jj-style-guide' ); ?></th>
                            <th style="width: 120px;"><?php _e( '만료일', 'jj-style-guide' ); ?></th>
                            <th style="width: 150px;"><?php _e( '작업', 'jj-style-guide' ); ?></th>
                        </tr>
                    </thead>
                    <tbody id="jj-license-list-body">
                        <?php if ( empty( $licenses ) ) : ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 30px;">
                                <?php _e( '발행된 라이센스가 없습니다.', 'jj-style-guide' ); ?>
                            </td>
                        </tr>
                        <?php else : ?>
                        <?php foreach ( $licenses as $license_key => $license_data ) : 
                            $activations = $this->get_license_activations( $license_key );
                            $is_active = isset( $license_data['status'] ) && $license_data['status'] === 'active';
                            $is_expired = isset( $license_data['expires'] ) && strtotime( $license_data['expires'] ) < time();
                        ?>
                        <tr data-license-key="<?php echo esc_attr( $license_key ); ?>" class="<?php echo $is_expired ? 'expired' : ''; ?>">
                            <td>
                                <code style="font-size: 11px; background: #f0f0f1; padding: 4px 8px; border-radius: 3px; word-break: break-all;">
                                    <?php echo esc_html( $license_key ); ?>
                                </code>
                                <button type="button" class="button button-small jj-copy-license-key" 
                                        data-license-key="<?php echo esc_attr( $license_key ); ?>"
                                        style="margin-top: 5px; font-size: 11px; padding: 2px 8px; height: 24px;">
                                    <span class="dashicons dashicons-admin-page" style="font-size: 12px; margin-top: 2px;"></span>
                                    <?php _e( '복사', 'jj-style-guide' ); ?>
                                </button>
                            </td>
                            <td>
                                <span class="jj-license-type-badge jj-license-type-<?php echo strtolower( esc_attr( $license_data['type'] ?? 'FREE' ) ); ?>">
                                    <?php echo esc_html( $license_data['type'] ?? 'FREE' ); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo esc_html( $license_data['customer'] ?? '-' ); ?>
                            </td>
                            <td>
                                <?php if ( $is_expired ) : ?>
                                    <span class="jj-status-badge expired"><?php _e( '만료됨', 'jj-style-guide' ); ?></span>
                                <?php elseif ( $is_active ) : ?>
                                    <span class="jj-status-badge active"><?php _e( '활성화됨', 'jj-style-guide' ); ?></span>
                                <?php else : ?>
                                    <span class="jj-status-badge inactive"><?php _e( '비활성화됨', 'jj-style-guide' ); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ( ! empty( $activations ) ) : ?>
                                    <div class="jj-activations-list">
                                        <?php foreach ( $activations as $activation ) : ?>
                                        <div class="jj-activation-item" style="margin-bottom: 5px; padding: 5px; background: #f9f9f9; border-radius: 3px; font-size: 12px;">
                                            <strong><?php echo esc_html( $activation['site_url'] ?? 'Unknown' ); ?></strong>
                                            <br>
                                            <small style="color: #666;">
                                                <?php printf( __( '활성화: %s', 'jj-style-guide' ), date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $activation['activated_at'] ?? time() ) ); ?>
                                            </small>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <span style="color: #999;"><?php _e( '없음', 'jj-style-guide' ); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo isset( $license_data['issued_at'] ) ? date_i18n( get_option( 'date_format' ), $license_data['issued_at'] ) : '-'; ?>
                            </td>
                            <td>
                                <?php 
                                if ( isset( $license_data['expires'] ) ) {
                                    $expires_timestamp = strtotime( $license_data['expires'] );
                                    echo date_i18n( get_option( 'date_format' ), $expires_timestamp );
                                    if ( $is_expired ) {
                                        echo ' <span style="color: #d63638;">(' . __( '만료', 'jj-style-guide' ) . ')</span>';
                                    }
                                } else {
                                    echo __( '무제한', 'jj-style-guide' );
                                }
                                ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                    <?php if ( $is_active ) : ?>
                                    <button type="button" 
                                            class="button button-small jj-deactivate-license" 
                                            data-license-key="<?php echo esc_attr( $license_key ); ?>"
                                            style="font-size: 11px; padding: 2px 8px; height: 26px;">
                                        <?php _e( '비활성화', 'jj-style-guide' ); ?>
                                    </button>
                                    <?php else : ?>
                                    <button type="button" 
                                            class="button button-small jj-activate-license" 
                                            data-license-key="<?php echo esc_attr( $license_key ); ?>"
                                            style="font-size: 11px; padding: 2px 8px; height: 26px;">
                                        <?php _e( '활성화', 'jj-style-guide' ); ?>
                                    </button>
                                    <?php endif; ?>
                                    <button type="button" 
                                            class="button button-small jj-check-license-status" 
                                            data-license-key="<?php echo esc_attr( $license_key ); ?>"
                                            style="font-size: 11px; padding: 2px 8px; height: 26px;">
                                        <?php _e( '상태 확인', 'jj-style-guide' ); ?>
                                    </button>
                                    <button type="button" 
                                            class="button button-small button-link-delete jj-delete-license" 
                                            data-license-key="<?php echo esc_attr( $license_key ); ?>"
                                            style="font-size: 11px; padding: 2px 8px; height: 26px; color: #b32d2e;">
                                        <?php _e( '삭제', 'jj-style-guide' ); ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * 라이센스 키 생성
     * 
     * @param string $type
     * @param string $customer
     * @param string $expires
     * @return array
     */
    public function generate_license_key( $type = 'FREE', $customer = '', $expires = '' ) {
        // 라이센스 타입 검증
        $allowed_types = array( 'FREE', 'BASIC', 'PREM', 'UNLIM' );
        if ( ! in_array( $type, $allowed_types ) ) {
            return array(
                'success' => false,
                'message' => __( '유효하지 않은 라이센스 타입입니다.', 'jj-style-guide' ),
            );
        }

        // 버전 정보
        $version = JJ_STYLE_GUIDE_VERSION;
        
        // 랜덤 문자열 생성 (8자리)
        $random = strtoupper( wp_generate_password( 8, false, false ) );
        
        // 체크섬 생성
        $checksum_source = $version . $type . $random . 'jj-secret-key';
        $checksum = strtoupper( substr( md5( $checksum_source ), 0, 8 ) );
        
        // 라이센스 키 생성: JJ-[VERSION]-[TYPE]-[RANDOM]-[CHECKSUM]
        $license_key = sprintf( 'JJ-%s-%s-%s-%s', $version, $type, $random, $checksum );
        
        // 만료일 설정 (없으면 1년 후)
        if ( empty( $expires ) ) {
            $expires = date( 'Y-m-d', strtotime( '+1 year' ) );
        }
        $expires_timestamp = strtotime( $expires );
        
        // 라이센스 데이터 저장
        $licenses = get_option( $this->licenses_option_key, array() );
        $licenses[ $license_key ] = array(
            'type' => $type,
            'customer' => sanitize_text_field( $customer ),
            'issued_at' => time(),
            'expires' => $expires,
            'expires_timestamp' => $expires_timestamp,
            'status' => 'active',
        );
        update_option( $this->licenses_option_key, $licenses );
        
        return array(
            'success' => true,
            'license_key' => $license_key,
            'message' => __( '라이센스 키가 생성되었습니다.', 'jj-style-guide' ),
            'data' => $licenses[ $license_key ],
        );
    }

    /**
     * 모든 라이센스 가져오기
     * 
     * @return array
     */
    public function get_all_licenses() {
        return get_option( $this->licenses_option_key, array() );
    }

    /**
     * 특정 라이센스 가져오기
     * 
     * @param string $license_key
     * @return array|null
     */
    public function get_license( $license_key ) {
        $licenses = $this->get_all_licenses();
        return isset( $licenses[ $license_key ] ) ? $licenses[ $license_key ] : null;
    }

    /**
     * 라이센스 활성화 상태 변경
     * 
     * @param string $license_key
     * @param string $status (active|inactive)
     * @return array
     */
    public function set_license_status( $license_key, $status = 'active' ) {
        $licenses = $this->get_all_licenses();
        
        if ( ! isset( $licenses[ $license_key ] ) ) {
            return array(
                'success' => false,
                'message' => __( '라이센스를 찾을 수 없습니다.', 'jj-style-guide' ),
            );
        }
        
        $licenses[ $license_key ]['status'] = $status;
        $licenses[ $license_key ]['status_changed_at'] = time();
        update_option( $this->licenses_option_key, $licenses );
        
        return array(
            'success' => true,
            'message' => $status === 'active' 
                ? __( '라이센스가 활성화되었습니다.', 'jj-style-guide' )
                : __( '라이센스가 비활성화되었습니다.', 'jj-style-guide' ),
        );
    }

    /**
     * 라이센스 삭제
     * 
     * @param string $license_key
     * @return array
     */
    public function delete_license( $license_key ) {
        $licenses = $this->get_all_licenses();
        
        if ( ! isset( $licenses[ $license_key ] ) ) {
            return array(
                'success' => false,
                'message' => __( '라이센스를 찾을 수 없습니다.', 'jj-style-guide' ),
            );
        }
        
        unset( $licenses[ $license_key ] );
        update_option( $this->licenses_option_key, $licenses );
        
        // 활성화 정보도 삭제
        $activations = get_option( $this->license_activations_key, array() );
        if ( isset( $activations[ $license_key ] ) ) {
            unset( $activations[ $license_key ] );
            update_option( $this->license_activations_key, $activations );
        }
        
        return array(
            'success' => true,
            'message' => __( '라이센스가 삭제되었습니다.', 'jj-style-guide' ),
        );
    }

    /**
     * 라이센스 활성화 정보 가져오기
     * 
     * @param string $license_key
     * @return array
     */
    public function get_license_activations( $license_key ) {
        $activations = get_option( $this->license_activations_key, array() );
        return isset( $activations[ $license_key ] ) ? $activations[ $license_key ] : array();
    }

    /**
     * 라이센스 활성화 기록
     * 
     * @param string $license_key
     * @param string $site_id
     * @param string $site_url
     * @return void
     */
    public function record_activation( $license_key, $site_id, $site_url ) {
        $activations = get_option( $this->license_activations_key, array() );
        
        if ( ! isset( $activations[ $license_key ] ) ) {
            $activations[ $license_key ] = array();
        }
        
        // 동일한 사이트 ID가 있으면 업데이트, 없으면 추가
        $found = false;
        foreach ( $activations[ $license_key ] as &$activation ) {
            if ( $activation['site_id'] === $site_id ) {
                $activation['site_url'] = $site_url;
                $activation['last_verified'] = time();
                $found = true;
                break;
            }
        }
        
        if ( ! $found ) {
            $activations[ $license_key ][] = array(
                'site_id' => $site_id,
                'site_url' => $site_url,
                'activated_at' => time(),
                'last_verified' => time(),
            );
        }
        
        update_option( $this->license_activations_key, $activations );
    }

    /**
     * 라이센스 통계 가져오기
     * 
     * @return array
     */
    public function get_license_stats() {
        $licenses = $this->get_all_licenses();
        $activations = get_option( $this->license_activations_key, array() );
        
        $stats = array(
            'total' => count( $licenses ),
            'active' => 0,
            'inactive' => 0,
            'in_use' => 0,
        );
        
        foreach ( $licenses as $license_key => $license_data ) {
            if ( isset( $license_data['status'] ) && $license_data['status'] === 'active' ) {
                $stats['active']++;
            } else {
                $stats['inactive']++;
            }
            
            if ( isset( $activations[ $license_key ] ) && ! empty( $activations[ $license_key ] ) ) {
                $stats['in_use']++;
            }
        }
        
        return $stats;
    }

    /**
     * AJAX: 라이센스 생성
     */
    public function ajax_generate_license() {
        check_ajax_referer( 'jj_license_issuer_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        if ( ! $this->is_master_version() ) {
            wp_send_json_error( array( 'message' => __( '마스터 버전에서만 사용할 수 있습니다.', 'jj-style-guide' ) ) );
        }
        
        $type = isset( $_POST['license_type'] ) ? sanitize_text_field( $_POST['license_type'] ) : '';
        $customer = isset( $_POST['customer'] ) ? sanitize_text_field( $_POST['customer'] ) : '';
        $expires = isset( $_POST['expires'] ) ? sanitize_text_field( $_POST['expires'] ) : '';
        
        if ( empty( $type ) ) {
            wp_send_json_error( array( 'message' => __( '라이센스 타입을 선택하세요.', 'jj-style-guide' ) ) );
        }
        
        $result = $this->generate_license_key( $type, $customer, $expires );
        
        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }

    /**
     * AJAX: 라이센스 비활성화/활성화
     */
    public function ajax_deactivate_license() {
        check_ajax_referer( 'jj_license_issuer_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
        $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'inactive';
        
        if ( empty( $license_key ) ) {
            wp_send_json_error( array( 'message' => __( '라이센스 키가 필요합니다.', 'jj-style-guide' ) ) );
        }
        
        $result = $this->set_license_status( $license_key, $status );
        
        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }

    /**
     * AJAX: 라이센스 상태 확인
     */
    public function ajax_check_license_status() {
        check_ajax_referer( 'jj_license_issuer_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
        
        if ( empty( $license_key ) ) {
            wp_send_json_error( array( 'message' => __( '라이센스 키가 필요합니다.', 'jj-style-guide' ) ) );
        }
        
        $license = $this->get_license( $license_key );
        $activations = $this->get_license_activations( $license_key );
        
        if ( ! $license ) {
            wp_send_json_error( array( 'message' => __( '라이센스를 찾을 수 없습니다.', 'jj-style-guide' ) ) );
        }
        
        wp_send_json_success( array(
            'license' => $license,
            'activations' => $activations,
            'message' => __( '라이센스 상태를 확인했습니다.', 'jj-style-guide' ),
        ) );
    }

    /**
     * AJAX: 라이센스 삭제
     */
    public function ajax_delete_license() {
        check_ajax_referer( 'jj_license_issuer_action', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
        }
        
        $license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';
        
        if ( empty( $license_key ) ) {
            wp_send_json_error( array( 'message' => __( '라이센스 키가 필요합니다.', 'jj-style-guide' ) ) );
        }
        
        $result = $this->delete_license( $license_key );
        
        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }
}


```

```php
<?php
/**
 * J&J 라이센스 관리자
 * 
 * 라이센스 키 검증, 사이트 바인딩, 기능 제한 등을 관리합니다.
 * 
 * @version 3.8.0
 * - 라이센스 키 구조: JJ-[VERSION]-[TYPE]-[RANDOM]-[CHECKSUM]
 * - 온라인/오프라인 검증 방식
 * - 사이트 바인딩 및 보안 장치
 */
if ( ! defined( 'ABSPATH' ) ) exit;

final class JJ_License_Manager {

    private static $instance = null;
    private $license_key_option = 'jj_style_guide_license_key';
    private $license_status_option = 'jj_style_guide_license_status';
    private $license_data_option = 'jj_style_guide_license_data';
    private $site_binding_option = 'jj_style_guide_site_binding';
    
    // 라이센스 타입 상수
    const TYPE_FREE = 'FREE';
    const TYPE_BASIC = 'BASIC';
    const TYPE_PREMIUM = 'PREM';
    const TYPE_UNLIMITED = 'UNLIM';
    
    // 라이센스 검증 서버 URL (설정에서 가져오거나 기본값 사용)
    private $license_server_url = '';
    
    // 오프라인 검증 캐시 기간 (일)
    private $offline_cache_days = 30;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 라이센스 서버 URL 초기화
        $this->init_license_server_url();
        
        // 라이센스 타입 상수 정의 (이미 정의되어 있지 않은 경우)
        if ( ! defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            // 라이센스 키에서 타입 읽기
            $license_key = get_option( $this->license_key_option, '' );
            if ( $license_key ) {
                $license_type = $this->parse_license_type( $license_key );
                
                // 보안 강화: 외부 서버 검증 결과와 일치하는지 확인
                $verified_type = $this->verify_license_type_with_server( $license_type );
                define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', $verified_type );
            } else {
                // 라이센스 키가 없으면 FREE 버전
                define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', self::TYPE_FREE );
            }
        } else {
            // 이미 정의된 경우에도 검증 수행 (보안 강화)
            $hardcoded_type = JJ_STYLE_GUIDE_LICENSE_TYPE;
            $verified_type = $this->verify_license_type_with_server( $hardcoded_type );
            
            // 검증 결과가 다르면 재정의 (서버 검증 결과 우선)
            if ( $verified_type !== $hardcoded_type ) {
                // 상수는 재정의할 수 없으므로, 런타임 검증 강제
                // 실제 기능 제한은 get_current_license_type()에서 검증된 값 사용
            }
        }
    }
    
    /**
     * 외부 서버에서 라이센스 타입 검증
     * 
     * @param string $local_type 로컬에서 파싱한 라이센스 타입
     * @return string 검증된 라이센스 타입
     */
    private function verify_license_type_with_server( $local_type ) {
        // Partner/Master(내부/파트너용)에서는 서버 검증을 건너뜁니다.
        // - 배포/실험 과정에서 라이센스 타입이 고정될 수 있으며, 내부 툴이 영향을 받지 않도록 함
        $is_partner_or_higher = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        }
        if ( $is_partner_or_higher ) {
            return $local_type;
        }
        
        $license_key = get_option( $this->license_key_option, '' );
        if ( empty( $license_key ) ) {
            return self::TYPE_FREE;
        }
        
        // 외부 서버에서 검증 (캐시 사용)
        $license_status = $this->get_license_status();
        
        // 서버 검증 결과가 있으면 우선 사용
        if ( isset( $license_status['type'] ) && $license_status['valid'] ) {
            $server_type = $license_status['type'];
            
            // 서버 타입과 로컬 타입이 다르면 서버 결과 우선
            // 이는 파일이 수정되었을 가능성을 나타냄
            if ( $server_type !== $local_type ) {
                // 보안 로그 기록 (보안 강화 클래스가 있으면 사용)
                if ( class_exists( 'JJ_License_Security_Hardening' ) ) {
                    $security = JJ_License_Security_Hardening::instance();
                    // 로그는 클래스 내부에서 기록됨
                }
                
                return $server_type; // 서버 검증 결과 우선
            }
        }
        
        return $local_type;
    }
    
    /**
     * 라이센스 서버 URL 초기화
     */
    private function init_license_server_url() {
        // 기본값: https://j-j-labs.com/
        $default_server_url = 'https://j-j-labs.com/';
        
        // 설정에서 개발자 서버 URL 가져오기
        $developer_server_url = get_option( 'jj_license_manager_server_url', $default_server_url );
        
        // URL이 비어있거나 기본값이면 기본값 사용
        if ( empty( $developer_server_url ) ) {
            $developer_server_url = $default_server_url;
        }
        
        // URL 끝에 슬래시가 없으면 추가
        $developer_server_url = untrailingslashit( $developer_server_url ) . '/';
        
        // API 엔드포인트 경로 추가
        $this->license_server_url = trailingslashit( $developer_server_url ) . 'wp-json/jj-license/v1/verify';
    }

    /**
     * 라이센스 타입 파싱
     * 
     * @param string $license_key
     * @return string
     */
    public function parse_license_type( $license_key ) {
        if ( empty( $license_key ) ) {
            return self::TYPE_FREE;
        }
        
        // 라이센스 키 형식: JJ-[VERSION]-[TYPE]-[RANDOM]-[CHECKSUM]
        $parts = explode( '-', $license_key );
        if ( count( $parts ) >= 3 && isset( $parts[2] ) ) {
            $type = strtoupper( $parts[2] );
            if ( in_array( $type, array( self::TYPE_FREE, self::TYPE_BASIC, self::TYPE_PREMIUM, self::TYPE_UNLIMITED ) ) ) {
                return $type;
            }
        }
        
        return self::TYPE_FREE;
    }

    /**
     * 라이센스 키 검증 (최소한의 형식만 검증, 실제 검증은 외부 서버에서 수행)
     * 
     * @param string $license_key
     * @return bool
     */
    public function verify_license_key_format( $license_key ) {
        if ( empty( $license_key ) ) {
            return false;
        }
        
        // 최소한의 형식만 검증 (실제 검증은 외부 서버에서 수행)
        // 라이센스 키 형식: JJ-[VERSION]-[TYPE]-[RANDOM]-[CHECKSUM]
        // 체크섬 검증은 제거하여 플러그인 내부에서 확인 불가능하도록 함
        if ( ! preg_match( '/^JJ-\d+\.\d+-(FREE|BASIC|PREM|UNLIM)-[A-Z0-9]{8}-[A-Z0-9]{8}$/i', $license_key ) ) {
            return false;
        }
        
        // 형식만 확인하고, 실제 유효성은 외부 서버 검증에 의존
        return true;
    }

    /**
     * 사이트 고유 ID 생성
     * 
     * @return string
     */
    public function get_site_id() {
        $site_url = home_url();
        $site_path = ABSPATH;
        $install_timestamp = get_option( 'jj_style_guide_install_timestamp', time() );
        
        return md5( $site_url . $site_path . $install_timestamp );
    }

    /**
     * 라이센스 상태 가져오기
     * 
     * @return array
     */
    public function get_license_status() {
        // [Safety Lock] MASTER 버전은 항상 유효한 것으로 간주
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) === 'MASTER' ) {
            return array(
                'valid' => true,
                'type' => 'MASTER',
                'message' => __( 'Master 에디션이 활성화되었습니다.', 'jj-style-guide' ),
                'valid_until' => time() + ( 10 * 365 * 24 * 60 * 60 ), // 10년
            );
        }

        $cached_status = get_option( $this->license_status_option, null );
        
        // 캐시된 상태가 있고 유효하면 반환
        if ( $cached_status && isset( $cached_status['valid_until'] ) ) {
            if ( time() < $cached_status['valid_until'] ) {
                return $cached_status;
            }
        }
        
        // 캐시가 만료되었거나 없으면 재검증
        return $this->verify_license( true );
    }

    /**
     * 라이센스 검증 (온라인/오프라인)
     * 모든 검증은 외부 서버에서 수행되며, 플러그인 내부에서는 최소한의 형식만 확인
     * 
     * @param bool $force_online
     * @return array
     */
    public function verify_license( $force_online = false ) {
        // 코드 무결성 모니터 확인
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                return array(
                    'valid' => false,
                    'type' => self::TYPE_FREE,
                    'message' => __( '플러그인이 잠금되었습니다. 코드 무결성 위반이 감지되었습니다.', 'jj-style-guide' ),
                    'activation_required' => true,
                    'locked' => true,
                );
            }
        }
        
        $license_key = get_option( $this->license_key_option, '' );
        
        if ( empty( $license_key ) ) {
            return array(
                'valid' => false,
                'type' => self::TYPE_FREE,
                'message' => __( '라이센스 키가 설정되지 않았습니다. Free 버전으로 실행됩니다.', 'jj-style-guide' ),
                'activation_required' => true,
            );
        }
        
        // 가짜 라이센스 키 감지 (보안 강화)
        if ( class_exists( 'JJ_License_Enforcement' ) ) {
            $enforcement = JJ_License_Enforcement::instance();
            if ( $enforcement->detect_fake_license_key( $license_key ) ) {
                return array(
                    'valid' => false,
                    'type' => self::TYPE_FREE,
                    'message' => __( '유효하지 않은 라이센스 키입니다. 서버에서 확인할 수 없습니다.', 'jj-style-guide' ),
                    'activation_required' => true,
                    'fake_key' => true,
                );
            }
        }
        
        // 최소한의 형식만 검증 (실제 검증은 외부 서버에서 수행)
        if ( ! $this->verify_license_key_format( $license_key ) ) {
            return array(
                'valid' => false,
                'type' => self::TYPE_FREE,
                'message' => __( '유효하지 않은 라이센스 키 형식입니다.', 'jj-style-guide' ),
                'activation_required' => true,
            );
        }
        
        $license_type = $this->parse_license_type( $license_key );
        
        // [v3.8.0] 마스터 버전의 라이센스 발행 관리자에서 상태 확인 (우선순위)
        // 이는 개발 환경에서만 사용되며, 실제 배포 버전에서는 외부 서버 검증만 사용
        if ( class_exists( 'JJ_License_Issuer' ) && defined( 'JJ_STYLE_GUIDE_ENV' ) && JJ_STYLE_GUIDE_ENV === 'dev' ) {
            $issuer = JJ_License_Issuer::instance();
            if ( method_exists( $issuer, 'get_license' ) ) {
                $issued_license = $issuer->get_license( $license_key );
                if ( $issued_license ) {
                    // 발행된 라이센스가 비활성화 상태인지 확인
                    if ( isset( $issued_license['status'] ) && $issued_license['status'] !== 'active' ) {
                        return array(
                            'valid' => false,
                            'type' => $license_type,
                            'message' => __( '이 라이센스는 비활성화되었습니다.', 'jj-style-guide' ),
                            'activation_required' => true,
                        );
                    }
                    
                    // 만료일 확인
                    if ( isset( $issued_license['expires_timestamp'] ) && $issued_license['expires_timestamp'] < time() ) {
                        return array(
                            'valid' => false,
                            'type' => $license_type,
                            'message' => __( '이 라이센스는 만료되었습니다.', 'jj-style-guide' ),
                            'expired' => true,
                            'activation_required' => true,
                        );
                    }
                    
                    // Premium 버전: 사이트 바인딩 확인
                    if ( in_array( $license_type, array( self::TYPE_PREMIUM, self::TYPE_UNLIMITED ) ) ) {
                        $site_id = $this->get_site_id();
                        $activations = $issuer->get_license_activations( $license_key );
                        
                        if ( ! empty( $activations ) ) {
                            // 이미 다른 사이트에서 사용 중인지 확인
                            $bound_to_other_site = false;
                            foreach ( $activations as $activation ) {
                                if ( $activation['site_id'] !== $site_id ) {
                                    $bound_to_other_site = true;
                                    break;
                                }
                            }
                            
                            if ( $bound_to_other_site && $license_type === self::TYPE_PREMIUM ) {
                                // Premium은 1개 사이트만 사용 가능
                                return array(
                                    'valid' => false,
                                    'type' => $license_type,
                                    'message' => __( '이 라이센스는 다른 사이트에서 사용 중입니다. Premium 버전은 1개 사이트에서만 사용 가능합니다.', 'jj-style-guide' ),
                                    'activation_required' => true,
                                );
                            }
                        }
                        
                        // 사이트 바인딩 저장
                        $this->bind_site( $license_key, $site_id );
                        
                        // 활성화 기록
                        if ( method_exists( $issuer, 'record_activation' ) ) {
                            $issuer->record_activation( $license_key, $site_id, home_url() );
                        }
                    }
                    
                    // 마스터 버전에서 발행된 라이센스가 유효하면 바로 반환
                    $expires_timestamp = isset( $issued_license['expires_timestamp'] ) ? $issued_license['expires_timestamp'] : time() + ( 365 * 24 * 60 * 60 );
                    $days_until_expiry = ( $expires_timestamp - time() ) / ( 24 * 60 * 60 );
                    
                    return array(
                        'valid' => true,
                        'type' => $license_type,
                        'message' => __( '라이센스가 활성화되었습니다.', 'jj-style-guide' ),
                        'valid_until' => $expires_timestamp,
                        'days_until_expiry' => $days_until_expiry,
                        'expiring_soon' => $days_until_expiry <= 14,
                    );
                }
            }
        }
        
        // FREE 버전은 추가 검증 불필요
        if ( $license_type === self::TYPE_FREE ) {
            return array(
                'valid' => true,
                'type' => self::TYPE_FREE,
                'message' => __( 'Free 버전이 활성화되었습니다.', 'jj-style-guide' ),
                'valid_until' => time() + ( 365 * 24 * 60 * 60 ), // 1년
            );
        }
        
        // 모든 상용 버전은 외부 서버에서 검증 (필수)
        // 온라인 검증 시도 (항상 시도)
        $online_result = $this->verify_license_online( $license_key );
        
        if ( $online_result['valid'] ) {
            // 검증 성공 시 캐시 저장
            $this->cache_license_status( $online_result );
            return $online_result;
        }
        
        // 외부 서버 검증 실패 시 (네트워크 오류 등)
        // 오프라인 검증으로 폴백 (제한된 기간만 유효)
        return $this->verify_license_offline( $license_key, $license_type );
    }

    /**
     * 온라인 검증 필요 여부 확인
     * 
     * @return bool
     */
    private function should_verify_online() {
        $cached_status = get_option( $this->license_status_option, null );
        
        // 캐시가 없거나 만료되었으면 온라인 검증 필요
        if ( ! $cached_status || ! isset( $cached_status['valid_until'] ) ) {
            return true;
        }
        
        // 캐시가 만료되었으면 온라인 검증 필요
        if ( time() >= $cached_status['valid_until'] ) {
            return true;
        }
        
        // 마지막 검증 후 30일이 지났으면 온라인 검증 필요
        $last_verified = isset( $cached_status['last_verified'] ) ? $cached_status['last_verified'] : 0;
        if ( time() - $last_verified > ( $this->offline_cache_days * 24 * 60 * 60 ) ) {
            return true;
        }
        
        return false;
    }

    /**
     * 온라인 라이센스 검증 (외부 서버에서 모든 검증 수행)
     * 
     * @param string $license_key
     * @return array
     */
    private function verify_license_online( $license_key ) {
        // 입력값 검증
        if ( empty( $license_key ) || ! is_string( $license_key ) || strlen( $license_key ) > 100 ) {
            return array(
                'valid' => false,
                'message' => __( '유효하지 않은 라이센스 키 형식입니다.', 'jj-style-guide' ),
            );
        }
        
        // 라이센스 키 형식 검증
        if ( ! $this->verify_license_key_format( $license_key ) ) {
            return array(
                'valid' => false,
                'message' => __( '유효하지 않은 라이센스 키 형식입니다.', 'jj-style-guide' ),
            );
        }
        
        $site_id = $this->get_site_id();
        $license_type = $this->parse_license_type( $license_key );
        
        // 사이트 ID 검증 (MD5 해시 형식)
        if ( ! preg_match( '/^[a-f0-9]{32}$/i', $site_id ) ) {
            return array(
                'valid' => false,
                'message' => __( '유효하지 않은 사이트 ID입니다.', 'jj-style-guide' ),
            );
        }
        
        // POST 요청 데이터 (sanitization)
        $data = array(
            'license_key' => sanitize_text_field( $license_key ),
            'site_id' => sanitize_text_field( $site_id ),
            'site_url' => esc_url_raw( home_url() ),
            'action' => 'verify',
            'plugin_version' => sanitize_text_field( defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.1.6' ),
            'timestamp' => time(), // 타임스탬프 추가 (서버 측 검증용)
        );
        
        // WordPress HTTP API 사용 (보안 강화)
        $response = wp_remote_post( esc_url_raw( $this->license_server_url ), array(
            'timeout' => 10,
            'sslverify' => true, // SSL 검증 활성화
            'body' => $data,
            'headers' => array(
                'User-Agent' => 'JJ-Style-Guide/' . sanitize_text_field( defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.1.6' ),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'redirection' => 0, // 리다이렉션 방지
        ) );
        
        // 네트워크 오류 처리
        if ( is_wp_error( $response ) ) {
            error_log( 'JJ License Manager: 네트워크 오류 - ' . $response->get_error_message() );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        // HTTP 상태 코드 확인
        $response_code = wp_remote_retrieve_response_code( $response );
        if ( $response_code !== 200 ) {
            error_log( 'JJ License Manager: 서버 응답 오류 - HTTP ' . $response_code );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        $body = wp_remote_retrieve_body( $response );
        
        // 응답 본문 검증
        if ( empty( $body ) || strlen( $body ) > 10000 ) { // 응답 크기 제한
            error_log( 'JJ License Manager: 유효하지 않은 응답 본문' );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        $result = json_decode( $body, true );
        
        // JSON 파싱 오류 확인
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            error_log( 'JJ License Manager: JSON 파싱 오류 - ' . json_last_error_msg() );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        if ( ! $result || ! isset( $result['valid'] ) || ! is_bool( $result['valid'] ) ) {
            error_log( 'JJ License Manager: 유효하지 않은 응답 형식' );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        // 외부 서버에서 반환된 결과 사용
        // 서버에서 다음 정보를 반환해야 함:
        // - valid: 라이센스 유효 여부
        // - type: 라이센스 타입
        // - message: 상태 메시지
        // - valid_until: 만료일 (타임스탬프)
        // - status: 활성화 상태 ('active', 'inactive', 'expired')
        // - expires_timestamp: 만료 타임스탬프
        
        // 만료일 확인
        if ( isset( $result['expires_timestamp'] ) && $result['expires_timestamp'] < time() ) {
            $result['valid'] = false;
            $result['expired'] = true;
            $result['message'] = __( '이 라이센스는 만료되었습니다.', 'jj-style-guide' );
            $result['activation_required'] = true;
        }
        
        // 만료 임박 확인 (14일 이하)
        if ( isset( $result['expires_timestamp'] ) && $result['expires_timestamp'] > time() ) {
            $days_until_expiry = ( $result['expires_timestamp'] - time() ) / ( 24 * 60 * 60 );
            $result['days_until_expiry'] = $days_until_expiry;
            $result['expiring_soon'] = $days_until_expiry <= 14;
        }
        
        // 활성화 필요 여부 확인
        if ( ! $result['valid'] || ( isset( $result['status'] ) && $result['status'] !== 'active' ) ) {
            $result['activation_required'] = true;
        }
        
        // 사이트 바인딩 확인 (Premium, Unlimited만)
        if ( in_array( $license_type, array( self::TYPE_PREMIUM, self::TYPE_UNLIMITED ) ) ) {
            if ( isset( $result['bound_site_id'] ) && $result['bound_site_id'] !== $site_id ) {
                // Premium 버전은 1개 사이트만 사용 가능
                if ( $license_type === self::TYPE_PREMIUM ) {
                    return array(
                        'valid' => false,
                        'type' => $license_type,
                        'message' => __( '이 라이센스는 다른 사이트에서 사용 중입니다. Premium 버전은 1개 사이트에서만 사용 가능합니다.', 'jj-style-guide' ),
                        'activation_required' => true,
                    );
                }
            }
            
            // 사이트 바인딩 저장
            $this->bind_site( $license_key, $site_id );
        }
        
        // 검증 시간 기록
        $result['last_verified'] = time();
        
        // valid_until이 없으면 서버에서 반환된 expires_timestamp 사용
        if ( ! isset( $result['valid_until'] ) && isset( $result['expires_timestamp'] ) ) {
            $result['valid_until'] = $result['expires_timestamp'];
        }
        
        // valid_until이 여전히 없으면 제한된 기간만 유효
        if ( ! isset( $result['valid_until'] ) ) {
            $result['valid_until'] = time() + ( 7 * 24 * 60 * 60 ); // 7일만 유효
        }

        return $result;
    }

    /**
     * 오프라인 라이센스 검증 (캐시 사용)
     * 
     * @param string $license_key
     * @param string $license_type
     * @return array
     */
    private function verify_license_offline( $license_key, $license_type ) {
        $cached_status = get_option( $this->license_status_option, null );
        
        // 캐시된 상태가 있고 유효하면 반환
        if ( $cached_status && isset( $cached_status['valid_until'] ) ) {
            if ( time() < $cached_status['valid_until'] ) {
                return $cached_status;
            }
        }
        
        // 캐시가 없거나 만료되었으면 라이센스 키 형식만 검증
        return array(
            'valid' => true,
            'type' => $license_type,
            'message' => __( '오프라인 모드로 실행 중입니다. 온라인 검증이 필요할 수 있습니다.', 'jj-style-guide' ),
            'valid_until' => time() + ( 7 * 24 * 60 * 60 ), // 7일만 유효
            'offline' => true,
        );
    }

    /**
     * 라이센스 상태 캐시 저장
     * 
     * @param array $status
     */
    private function cache_license_status( $status ) {
        update_option( $this->license_status_option, $status );
    }

    /**
     * 사이트 바인딩
     * 
     * @param string $license_key
     * @param string $site_id
     */
    private function bind_site( $license_key, $site_id ) {
        update_option( $this->site_binding_option, array(
            'license_key' => $license_key,
            'site_id' => $site_id,
            'bound_at' => time(),
        ) );
    }

    /**
     * 현재 라이센스 타입 가져오기
     * 
     * @return string
     */
    public function get_current_license_type() {
        // [Safety Lock] MASTER 버전은 무조건 MASTER 반환
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) === 'MASTER' ) {
            return 'MASTER';
        }

        // 코드 무결성 모니터 확인
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                // 잠금 상태면 FREE로 강제
                return self::TYPE_FREE;
            }
        }
        
        // 라이센스 강제 실행 클래스 확인
        if ( class_exists( 'JJ_License_Enforcement' ) ) {
            $enforcement = JJ_License_Enforcement::instance();
            $license_type = defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ? JJ_STYLE_GUIDE_LICENSE_TYPE : self::TYPE_FREE;
            return apply_filters( 'jj_get_license_type', $license_type );
        }
        
        // Pro 버전 활성화 코드 확인 (우선순위)
        if ( function_exists( 'get_option' ) ) {
            $pro_activation_data = get_option( 'jj_style_guide_pro_activation_data', array() );
            if ( ! empty( $pro_activation_data ) && isset( $pro_activation_data['license_type'] ) ) {
                $pro_license_type = $pro_activation_data['license_type'];
                
                // 활성화 코드 유효성 확인
                $pro_activation_code = get_option( 'jj_style_guide_pro_activation_code', '' );
                if ( ! empty( $pro_activation_code ) ) {
                    // 만료 확인
                    if ( isset( $pro_activation_data['expires_timestamp'] ) && $pro_activation_data['expires_timestamp'] > 0 ) {
                        if ( time() > $pro_activation_data['expires_timestamp'] ) {
                            return self::TYPE_FREE; // 만료됨
                        }
                    }
                    
                    // Pro 버전의 라이센스 타입 반환
                    return apply_filters( 'jj_get_license_type', $pro_license_type );
                }
            }
        }
        
        // 외부 서버 검증 결과 우선 사용 (보안 강화)
        $license_status = $this->get_license_status();
        if ( isset( $license_status['type'] ) && $license_status['valid'] ) {
            return $license_status['type'];
        }
        
        // 서버 검증 실패 시 하드코딩된 상수 사용 (하지만 검증됨)
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            // 보안 강화: 하드코딩된 값도 서버와 재검증
            return $this->verify_license_type_with_server( JJ_STYLE_GUIDE_LICENSE_TYPE );
        }
        
        $license_key = get_option( $this->license_key_option, '' );
        return $this->parse_license_type( $license_key );
    }

    /**
     * 기능 사용 가능 여부 확인
     * 
     * @param string $feature
     * @return bool
     */
    public function can_use_feature( $feature ) {
        // [Safety Lock] MASTER 버전은 모든 기능 사용 가능
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) === 'MASTER' ) {
            return true;
        }

        // 코드 무결성 모니터 확인 (최우선)
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                return false; // 잠금 상태면 모든 기능 비활성화
            }
        }
        
        $license_type = $this->get_current_license_type();
        $status = $this->get_license_status();
        
        // 서버 검증 실패 시 기능 비활성화 (보안 강화)
        if ( ! $status['valid'] || ! isset( $status['type'] ) ) {
            return false;
        }
        
        // 서버 검증 결과와 로컬 라이센스 타입 불일치 시 비활성화
        if ( isset( $status['type'] ) && $status['type'] !== $license_type ) {
            // 불일치 감지 시 개발자에게 알림 (비동기)
            $this->report_license_mismatch( $license_type, $status['type'] );
            return false;
        }
        
        // 기능별 라이센스 요구사항 확인
        $feature_requirements = $this->get_feature_requirements();
        
        if ( ! isset( $feature_requirements[ $feature ] ) ) {
            // 정의되지 않은 기능은 모든 라이센스에서 사용 가능
            return true;
        }
        
        $required_type = $feature_requirements[ $feature ];
        $type_hierarchy = array(
            self::TYPE_FREE => 1,
            self::TYPE_BASIC => 2,
            self::TYPE_PREMIUM => 3,
            self::TYPE_UNLIMITED => 4,
        );
        
        $current_level = isset( $type_hierarchy[ $license_type ] ) ? $type_hierarchy[ $license_type ] : 0;
        $required_level = isset( $type_hierarchy[ $required_type ] ) ? $type_hierarchy[ $required_type ] : 999;
        
        return $current_level >= $required_level;
    }
    
    /**
     * 라이센스 타입 불일치 보고
     * 
     * @param string $local_type 로컬 라이센스 타입
     * @param string $server_type 서버 검증 라이센스 타입
     */
    private function report_license_mismatch( $local_type, $server_type ) {
        // 이미 보고된 경우 중복 방지 (24시간 내)
        $last_report = get_option( 'jj_license_mismatch_last_report', 0 );
        if ( time() - $last_report < 24 * 60 * 60 ) {
            return;
        }
        
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            $monitor->notify_developer( array(
                array(
                    'type' => 'license_type_mismatch',
                    'severity' => 'critical',
                    'local_type' => $local_type,
                    'server_type' => $server_type,
                ),
            ), array() );
        }
        
        update_option( 'jj_license_mismatch_last_report', time() );
    }

    /**
     * 기능별 라이센스 요구사항
     * 
     * @return array
     */
    private function get_feature_requirements() {
        return array(
            // 팔레트 시스템
            'palette_brand' => self::TYPE_FREE,
            'palette_system' => self::TYPE_BASIC,
            'palette_alternative' => self::TYPE_PREMIUM,
            'palette_another' => self::TYPE_PREMIUM,
            'palette_temp' => self::TYPE_BASIC,
            
            // 타이포그래피
            'typography_h1_h2' => self::TYPE_FREE,
            'typography_h3_h4' => self::TYPE_BASIC,
            'typography_h5_h6' => self::TYPE_PREMIUM,
            
            // 버튼
            'button_primary' => self::TYPE_FREE,
            'button_secondary' => self::TYPE_BASIC,
            'button_text' => self::TYPE_PREMIUM,
            
            // 폼
            'form_basic' => self::TYPE_FREE,
            'form_advanced' => self::TYPE_BASIC,
            
            // 내보내기/불러오기
            'export_import_full' => self::TYPE_BASIC,
            'export_import_section' => self::TYPE_PREMIUM,
            
            // 관리자 센터
            'admin_center_general' => self::TYPE_BASIC,
            'admin_center_full' => self::TYPE_PREMIUM,
            
            // 실험실 센터
            'labs_center_list' => self::TYPE_BASIC,
            'labs_center_scanner' => self::TYPE_PREMIUM,
            'labs_center_full' => self::TYPE_UNLIMITED,
            
            // Customizer 통합
            'customizer_basic' => self::TYPE_FREE,
            'customizer_full' => self::TYPE_PREMIUM,
            
            // 어댑터 (개수 제한)
            'adapter_themes' => self::TYPE_FREE, // 개수는 버전별로 다름
            'adapter_plugins' => self::TYPE_FREE, // 개수는 버전별로 다름
            
            // 고급 기능
            'eyedropper' => self::TYPE_BASIC,
            'palette_load' => self::TYPE_PREMIUM,
            'customizer_sync' => self::TYPE_PREMIUM,
        );
    }

    /**
     * 라이센스 키 저장
     * 
     * @param string $license_key
     * @return array
     */
    public function save_license_key( $license_key ) {
        // 라이센스 키 제거 (빈 문자열인 경우)
        if ( empty( $license_key ) ) {
            delete_option( $this->license_key_option );
            delete_option( $this->license_status_option );
            delete_option( $this->license_data_option );
            delete_option( 'jj_style_guide_license_type_override' );
            
            return array(
                'success' => true,
                'message' => __( '라이센스 키가 제거되었습니다. Free 버전으로 실행됩니다.', 'jj-style-guide' ),
                'status' => array(
                    'valid' => false,
                    'type' => self::TYPE_FREE,
                    'message' => __( '라이센스 키가 설정되지 않았습니다. Free 버전으로 실행됩니다.', 'jj-style-guide' ),
                ),
            );
        }
        
        // 라이센스 키 형식 검증
        if ( ! $this->verify_license_key_format( $license_key ) ) {
            return array(
                'success' => false,
                'message' => __( '유효하지 않은 라이센스 키 형식입니다.', 'jj-style-guide' ),
            );
        }
        
        // 라이센스 키 저장
        update_option( $this->license_key_option, $license_key );
        
        // 라이센스 타입 업데이트
        $license_type = $this->parse_license_type( $license_key );
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            // 상수는 재정의할 수 없으므로 옵션에 저장
            update_option( 'jj_style_guide_license_type_override', $license_type );
        }
        
        // 라이센스 검증
        $status = $this->verify_license( true );
        
        return array(
            'success' => $status['valid'],
            'message' => $status['message'],
            'status' => $status,
        );
    }

    /**
     * 결제 페이지 URL 가져오기
     * 
     * @param string $action (upgrade, renew)
     * @return string
     */
    public function get_purchase_url( $action = 'upgrade' ) {
        // 결제 페이지 주소 (고정)
        return 'https://j-j-labs.com/?product=plugin-jj-center-of-style-setting';
    }

    /**
     * 업그레이드 프롬프트 메시지 생성
     * 
     * @param string $feature
     * @return string
     */
    public function get_upgrade_prompt( $feature ) {
        $license_type = $this->get_current_license_type();

        // [Safety Lock] MASTER 버전은 업그레이드 프롬프트 표시 안 함
        if ( 'MASTER' === $license_type || ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && 'MASTER' === strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) ) ) {
            return '';
        }

        $feature_requirements = $this->get_feature_requirements();
        
        if ( ! isset( $feature_requirements[ $feature ] ) ) {
            return '';
        }
        
        $required_type = $feature_requirements[ $feature ];
        
        if ( $this->can_use_feature( $feature ) ) {
            return '';
        }
        
        $type_names = array(
            self::TYPE_FREE => __( 'Free', 'jj-style-guide' ),
            self::TYPE_BASIC => __( 'Basic', 'jj-style-guide' ),
            self::TYPE_PREMIUM => __( 'Premium', 'jj-style-guide' ),
            self::TYPE_UNLIMITED => __( 'Unlimited', 'jj-style-guide' ),
        );
        
        $current_name = isset( $type_names[ $license_type ] ) ? $type_names[ $license_type ] : $license_type;
        $required_name = isset( $type_names[ $required_type ] ) ? $type_names[ $required_type ] : $required_type;
        
        // 라이센스 상태 확인 (만료 여부)
        $license_status = $this->get_license_status();
        $is_expired = false;
        $action_text = __( '업그레이드하기', 'jj-style-guide' );
        
        if ( isset( $license_status['valid_until'] ) && $license_status['valid_until'] < time() ) {
            $is_expired = true;
            $action_text = __( '기한 연장하기', 'jj-style-guide' );
        } elseif ( ! $license_status['valid'] ) {
            // 라이센스가 유효하지 않은 경우도 기한 연장 고려
            if ( in_array( $license_type, array( self::TYPE_BASIC, self::TYPE_PREMIUM, self::TYPE_UNLIMITED ) ) ) {
                $is_expired = true;
                $action_text = __( '기한 연장하기', 'jj-style-guide' );
            }
        }
        
        $purchase_url = $this->get_purchase_url( $is_expired ? 'renew' : 'upgrade' );
        
        return sprintf(
            __( '이 기능은 %s 버전 이상에서 사용할 수 있습니다. 현재 버전: %s. <a href="%s" target="_blank" rel="noopener noreferrer" style="font-weight: 600; color: #2271b1; text-decoration: underline;">%s</a>', 'jj-style-guide' ),
            $required_name,
            $current_name,
            esc_url( $purchase_url ),
            $action_text
        );
    }
}

```

```php
<?php
/**
 * 라이센스 보안 강화 클래스
 * 
 * 파일 무결성 검사 및 라이센스 우회 방지 기능을 제공합니다.
 * 
 * @package JJ_Style_Guide
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Security_Hardening {
    
    private static $instance = null;
    private $file_hashes = array();
    private $license_type_verified = false;
    
    /**
     * 싱글톤 인스턴스
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 생성자
     */
    private function __construct() {
        $this->init_file_hashes();
        $this->verify_file_integrity();
    }
    
    /**
     * 파일 해시 초기화
     * 
     * 실제 배포 시에는 각 파일의 해시값을 미리 계산하여 저장해야 합니다.
     */
    private function init_file_hashes() {
        // 주요 파일들의 해시값 (실제 배포 시 계산된 값으로 교체)
        $this->file_hashes = array(
            // 플러그인 메인 파일들
            'free' => array(
                'file' => JJ_STYLE_GUIDE_PATH . 'jj-simple-style-guide.php',
                'expected_hash' => '', // 실제 해시값으로 교체 필요
                'critical_lines' => array( 30 ), // 라이센스 타입 정의 라인
            ),
            'premium' => array(
                'file' => JJ_STYLE_GUIDE_PATH . 'jj-simple-style-guide.php',
                'expected_hash' => '', // 실제 해시값으로 교체 필요
                'critical_lines' => array( 28 ), // 라이센스 타입 정의 라인
            ),
        );
    }
    
    /**
     * 파일 무결성 검사
     * 
     * @return bool 파일이 수정되었는지 여부
     */
    public function verify_file_integrity() {
        // Partner/Master(내부/파트너용)에서는 검사 건너뛰기
        // - 해시/라인 기준이 레거시 파일명에 묶여있고, 내부 빌드에서는 차단이 치명적일 수 있음
        $is_partner_or_higher = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        }
        if ( $is_partner_or_higher ) {
            return true;
        }
        
        $edition = defined( 'JJ_STYLE_GUIDE_EDITION' ) ? JJ_STYLE_GUIDE_EDITION : 'free';
        
        if ( ! isset( $this->file_hashes[ $edition ] ) ) {
            return true; // 해시가 설정되지 않은 경우 통과
        }
        
        $file_info = $this->file_hashes[ $edition ];
        $file_path = $file_info['file'];
        
        if ( ! file_exists( $file_path ) ) {
            return true; // 파일이 없으면 통과 (다른 버전일 수 있음)
        }
        
        // 파일 해시 검증
        if ( ! empty( $file_info['expected_hash'] ) ) {
            $actual_hash = hash_file( 'sha256', $file_path );
            if ( $actual_hash !== $file_info['expected_hash'] ) {
                $this->log_security_event( 'file_modified', array(
                    'file' => $file_path,
                    'expected_hash' => $file_info['expected_hash'],
                    'actual_hash' => $actual_hash,
                ) );
                return false;
            }
        }
        
        // 중요 라인 검증 (라이센스 타입 상수가 수정되었는지 확인)
        if ( ! empty( $file_info['critical_lines'] ) ) {
            $file_content = file_get_contents( $file_path );
            $lines = explode( "\n", $file_content );
            
            foreach ( $file_info['critical_lines'] as $line_num ) {
                $line_index = $line_num - 1; // 배열 인덱스는 0부터 시작
                if ( isset( $lines[ $line_index ] ) ) {
                    $line = trim( $lines[ $line_index ] );
                    // 라이센스 타입이 예상과 다른 값으로 변경되었는지 확인
                    if ( preg_match( "/define\s*\(\s*['\"]JJ_STYLE_GUIDE_LICENSE_TYPE['\"]\s*,\s*['\"](FREE|BASIC|PREM|UNLIM)['\"]\s*\)/", $line, $matches ) ) {
                        $found_type = $matches[1];
                        $expected_type = strtoupper( $edition === 'free' ? 'FREE' : ( $edition === 'basic' ? 'BASIC' : ( $edition === 'premium' ? 'PREM' : 'UNLIM' ) ) );
                        
                        if ( $found_type !== $expected_type ) {
                            $this->log_security_event( 'license_type_modified', array(
                                'file' => $file_path,
                                'line' => $line_num,
                                'expected_type' => $expected_type,
                                'found_type' => $found_type,
                            ) );
                            return false;
                        }
                    }
                }
            }
        }
        
        return true;
    }
    
    /**
     * 라이센스 타입 검증
     * 
     * 하드코딩된 상수 대신 외부 서버 검증 결과만 사용하도록 강제
     * 
     * @param string $hardcoded_type 파일에 하드코딩된 라이센스 타입
     * @return string 검증된 라이센스 타입
     */
    public function verify_license_type( $hardcoded_type ) {
        // 이미 검증된 경우 캐시된 결과 사용
        if ( $this->license_type_verified ) {
            return $hardcoded_type;
        }
        
        // 파일 무결성 검사 실패 시 FREE로 강제
        if ( ! $this->verify_file_integrity() ) {
            $this->log_security_event( 'integrity_check_failed', array(
                'hardcoded_type' => $hardcoded_type,
            ) );
            return 'FREE';
        }
        
        // 외부 서버에서 라이센스 검증 (최소 7일마다 강제)
        $license_manager = JJ_License_Manager::instance();
        $license_status = $license_manager->get_license_status();
        
        // 외부 서버 검증 결과가 하드코딩된 타입과 일치하는지 확인
        if ( isset( $license_status['type'] ) ) {
            $server_type = $license_status['type'];
            
            // 타입 불일치 시 하드코딩된 타입 무시하고 서버 결과 사용
            if ( $server_type !== $hardcoded_type && $license_status['valid'] ) {
                $this->log_security_event( 'license_type_mismatch', array(
                    'hardcoded_type' => $hardcoded_type,
                    'server_type' => $server_type,
                ) );
                // 서버 검증 결과를 우선 사용
                $this->license_type_verified = true;
                return $server_type;
            }
        }
        
        // 검증 실패 시 FREE로 강제
        if ( ! isset( $license_status['valid'] ) || ! $license_status['valid'] ) {
            return 'FREE';
        }
        
        $this->license_type_verified = true;
        return $hardcoded_type;
    }
    
    /**
     * 보안 이벤트 로깅
     * 
     * @param string $event_type 이벤트 타입
     * @param array $data 이벤트 데이터
     */
    private function log_security_event( $event_type, $data = array() ) {
        $log_data = array(
            'timestamp' => current_time( 'mysql' ),
            'event_type' => $event_type,
            'data' => $data,
            'ip' => $this->get_client_ip(),
            'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '',
        );
        
        // 옵션에 로그 저장 (최근 100개만 유지)
        $logs = get_option( 'jj_security_logs', array() );
        $logs[] = $log_data;
        
        // 최근 100개만 유지
        if ( count( $logs ) > 100 ) {
            $logs = array_slice( $logs, -100 );
        }
        
        update_option( 'jj_security_logs', $logs );
        
        // 심각한 이벤트는 관리자에게 이메일 발송 (선택사항)
        if ( in_array( $event_type, array( 'file_modified', 'license_type_modified', 'license_type_mismatch' ) ) ) {
            $this->send_security_alert( $event_type, $data );
        }
    }
    
    /**
     * 보안 경고 이메일 발송
     * 
     * @param string $event_type 이벤트 타입
     * @param array $data 이벤트 데이터
     */
    private function send_security_alert( $event_type, $data ) {
        $admin_email = get_option( 'admin_email' );
        $site_url = home_url();
        
        $subject = sprintf( '[보안 경고] %s - 라이센스 보안 이벤트 발생', $site_url );
        $message = sprintf(
            "보안 이벤트가 발생했습니다.\n\n" .
            "이벤트 타입: %s\n" .
            "사이트 URL: %s\n" .
            "발생 시간: %s\n\n" .
            "상세 정보:\n%s",
            $event_type,
            $site_url,
            current_time( 'mysql' ),
            print_r( $data, true )
        );
        
        // 이메일 발송 (선택사항, 기본적으로 비활성화)
        if ( defined( 'JJ_SECURITY_ALERT_EMAIL' ) && JJ_SECURITY_ALERT_EMAIL ) {
            wp_mail( $admin_email, $subject, $message );
        }
    }
    
    /**
     * 클라이언트 IP 주소 가져오기
     * 
     * @return string IP 주소
     */
    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_REAL_IP',        // Nginx proxy
            'HTTP_X_FORWARDED_FOR',  // Proxy
            'REMOTE_ADDR',           // Standard
        );
        
        foreach ( $ip_keys as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ip = sanitize_text_field( $_SERVER[ $key ] );
                // X-Forwarded-For는 여러 IP가 있을 수 있음
                if ( strpos( $ip, ',' ) !== false ) {
                    $ips = explode( ',', $ip );
                    $ip = trim( $ips[0] );
                }
                return $ip;
            }
        }
        
        return '0.0.0.0';
    }
    
    /**
     * 보안 로그 가져오기
     * 
     * @param int $limit 로그 개수 제한
     * @return array 보안 로그
     */
    public function get_security_logs( $limit = 50 ) {
        $logs = get_option( 'jj_security_logs', array() );
        return array_slice( $logs, -$limit );
    }
}


```

```php
<?php
/**
 * J&J 라이센스 관리자
 * 
 * 라이센스 키 검증, 사이트 바인딩, 기능 제한 등을 관리합니다.
 * 
 * @version 3.8.0
 * - 라이센스 키 구조: JJ-[VERSION]-[TYPE]-[RANDOM]-[CHECKSUM]
 * - 온라인/오프라인 검증 방식
 * - 사이트 바인딩 및 보안 장치
 */
if ( ! defined( 'ABSPATH' ) ) exit;

final class JJ_License_Manager {

    private static $instance = null;
    private $license_key_option = 'jj_style_guide_license_key';
    private $license_status_option = 'jj_style_guide_license_status';
    private $license_data_option = 'jj_style_guide_license_data';
    private $site_binding_option = 'jj_style_guide_site_binding';
    
    // 라이센스 타입 상수
    const TYPE_FREE = 'FREE';
    const TYPE_BASIC = 'BASIC';
    const TYPE_PREMIUM = 'PREM';
    const TYPE_UNLIMITED = 'UNLIM';
    
    // 라이센스 검증 서버 URL (설정에서 가져오거나 기본값 사용)
    private $license_server_url = '';
    
    // 오프라인 검증 캐시 기간 (일)
    private $offline_cache_days = 30;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 라이센스 서버 URL 초기화
        $this->init_license_server_url();
        
        // 라이센스 타입 상수 정의 (이미 정의되어 있지 않은 경우)
        if ( ! defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            // 라이센스 키에서 타입 읽기
            $license_key = get_option( $this->license_key_option, '' );
            if ( $license_key ) {
                $license_type = $this->parse_license_type( $license_key );
                
                // 보안 강화: 외부 서버 검증 결과와 일치하는지 확인
                $verified_type = $this->verify_license_type_with_server( $license_type );
                define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', $verified_type );
            } else {
                // 라이센스 키가 없으면 FREE 버전
                define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', self::TYPE_FREE );
            }
        } else {
            // 이미 정의된 경우에도 검증 수행 (보안 강화)
            $hardcoded_type = JJ_STYLE_GUIDE_LICENSE_TYPE;
            $verified_type = $this->verify_license_type_with_server( $hardcoded_type );
            
            // 검증 결과가 다르면 재정의 (서버 검증 결과 우선)
            if ( $verified_type !== $hardcoded_type ) {
                // 상수는 재정의할 수 없으므로, 런타임 검증 강제
                // 실제 기능 제한은 get_current_license_type()에서 검증된 값 사용
            }
        }
    }
    
    /**
     * 외부 서버에서 라이센스 타입 검증
     * 
     * @param string $local_type 로컬에서 파싱한 라이센스 타입
     * @return string 검증된 라이센스 타입
     */
    private function verify_license_type_with_server( $local_type ) {
        // Partner/Master(내부/파트너용)에서는 서버 검증을 건너뜁니다.
        // - 배포/실험 과정에서 라이센스 타입이 고정될 수 있으며, 내부 툴이 영향을 받지 않도록 함
        $is_partner_or_higher = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $is_partner_or_higher = JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_partner_or_higher = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
        }
        if ( $is_partner_or_higher ) {
            return $local_type;
        }
        
        $license_key = get_option( $this->license_key_option, '' );
        if ( empty( $license_key ) ) {
            return self::TYPE_FREE;
        }
        
        // 외부 서버에서 검증 (캐시 사용)
        $license_status = $this->get_license_status();
        
        // 서버 검증 결과가 있으면 우선 사용
        if ( isset( $license_status['type'] ) && $license_status['valid'] ) {
            $server_type = $license_status['type'];
            
            // 서버 타입과 로컬 타입이 다르면 서버 결과 우선
            // 이는 파일이 수정되었을 가능성을 나타냄
            if ( $server_type !== $local_type ) {
                // 보안 로그 기록 (보안 강화 클래스가 있으면 사용)
                if ( class_exists( 'JJ_License_Security_Hardening' ) ) {
                    $security = JJ_License_Security_Hardening::instance();
                    // 로그는 클래스 내부에서 기록됨
                }
                
                return $server_type; // 서버 검증 결과 우선
            }
        }
        
        return $local_type;
    }
    
    /**
     * 라이센스 서버 URL 초기화
     */
    private function init_license_server_url() {
        // 기본값: https://j-j-labs.com/
        $default_server_url = 'https://j-j-labs.com/';
        
        // 설정에서 개발자 서버 URL 가져오기
        $developer_server_url = get_option( 'jj_license_manager_server_url', $default_server_url );
        
        // URL이 비어있거나 기본값이면 기본값 사용
        if ( empty( $developer_server_url ) ) {
            $developer_server_url = $default_server_url;
        }
        
        // URL 끝에 슬래시가 없으면 추가
        $developer_server_url = untrailingslashit( $developer_server_url ) . '/';
        
        // API 엔드포인트 경로 추가
        $this->license_server_url = trailingslashit( $developer_server_url ) . 'wp-json/jj-license/v1/verify';
    }

    /**
     * 라이센스 타입 파싱
     * 
     * @param string $license_key
     * @return string
     */
    public function parse_license_type( $license_key ) {
        if ( empty( $license_key ) ) {
            return self::TYPE_FREE;
        }
        
        // 라이센스 키 형식: JJ-[VERSION]-[TYPE]-[RANDOM]-[CHECKSUM]
        $parts = explode( '-', $license_key );
        if ( count( $parts ) >= 3 && isset( $parts[2] ) ) {
            $type = strtoupper( $parts[2] );
            if ( in_array( $type, array( self::TYPE_FREE, self::TYPE_BASIC, self::TYPE_PREMIUM, self::TYPE_UNLIMITED ) ) ) {
                return $type;
            }
        }
        
        return self::TYPE_FREE;
    }

    /**
     * 라이센스 키 검증 (최소한의 형식만 검증, 실제 검증은 외부 서버에서 수행)
     * 
     * @param string $license_key
     * @return bool
     */
    public function verify_license_key_format( $license_key ) {
        if ( empty( $license_key ) ) {
            return false;
        }
        
        // 최소한의 형식만 검증 (실제 검증은 외부 서버에서 수행)
        // 라이센스 키 형식: JJ-[VERSION]-[TYPE]-[RANDOM]-[CHECKSUM]
        // 체크섬 검증은 제거하여 플러그인 내부에서 확인 불가능하도록 함
        if ( ! preg_match( '/^JJ-\d+\.\d+-(FREE|BASIC|PREM|UNLIM)-[A-Z0-9]{8}-[A-Z0-9]{8}$/i', $license_key ) ) {
            return false;
        }
        
        // 형식만 확인하고, 실제 유효성은 외부 서버 검증에 의존
        return true;
    }

    /**
     * 사이트 고유 ID 생성
     * 
     * @return string
     */
    public function get_site_id() {
        $site_url = home_url();
        $site_path = ABSPATH;
        $install_timestamp = get_option( 'jj_style_guide_install_timestamp', time() );
        
        return md5( $site_url . $site_path . $install_timestamp );
    }

    /**
     * 라이센스 상태 가져오기
     * 
     * @return array
     */
    public function get_license_status() {
        // [Safety Lock] MASTER 버전은 항상 유효한 것으로 간주
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) === 'MASTER' ) {
            return array(
                'valid' => true,
                'type' => 'MASTER',
                'message' => __( 'Master 에디션이 활성화되었습니다.', 'jj-style-guide' ),
                'valid_until' => time() + ( 10 * 365 * 24 * 60 * 60 ), // 10년
            );
        }

        $cached_status = get_option( $this->license_status_option, null );
        
        // 캐시된 상태가 있고 유효하면 반환
        if ( $cached_status && isset( $cached_status['valid_until'] ) ) {
            if ( time() < $cached_status['valid_until'] ) {
                return $cached_status;
            }
        }
        
        // 캐시가 만료되었거나 없으면 재검증
        return $this->verify_license( true );
    }

    /**
     * 라이센스 검증 (온라인/오프라인)
     * 모든 검증은 외부 서버에서 수행되며, 플러그인 내부에서는 최소한의 형식만 확인
     * 
     * @param bool $force_online
     * @return array
     */
    public function verify_license( $force_online = false ) {
        // 코드 무결성 모니터 확인
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                return array(
                    'valid' => false,
                    'type' => self::TYPE_FREE,
                    'message' => __( '플러그인이 잠금되었습니다. 코드 무결성 위반이 감지되었습니다.', 'jj-style-guide' ),
                    'activation_required' => true,
                    'locked' => true,
                );
            }
        }
        
        $license_key = get_option( $this->license_key_option, '' );
        
        if ( empty( $license_key ) ) {
            return array(
                'valid' => false,
                'type' => self::TYPE_FREE,
                'message' => __( '라이센스 키가 설정되지 않았습니다. Free 버전으로 실행됩니다.', 'jj-style-guide' ),
                'activation_required' => true,
            );
        }
        
        // 가짜 라이센스 키 감지 (보안 강화)
        if ( class_exists( 'JJ_License_Enforcement' ) ) {
            $enforcement = JJ_License_Enforcement::instance();
            if ( $enforcement->detect_fake_license_key( $license_key ) ) {
                return array(
                    'valid' => false,
                    'type' => self::TYPE_FREE,
                    'message' => __( '유효하지 않은 라이센스 키입니다. 서버에서 확인할 수 없습니다.', 'jj-style-guide' ),
                    'activation_required' => true,
                    'fake_key' => true,
                );
            }
        }
        
        // 최소한의 형식만 검증 (실제 검증은 외부 서버에서 수행)
        if ( ! $this->verify_license_key_format( $license_key ) ) {
            return array(
                'valid' => false,
                'type' => self::TYPE_FREE,
                'message' => __( '유효하지 않은 라이센스 키 형식입니다.', 'jj-style-guide' ),
                'activation_required' => true,
            );
        }
        
        $license_type = $this->parse_license_type( $license_key );
        
        // [v3.8.0] 마스터 버전: 라이센스 발행 관리자에서 상태 확인 (우선순위)
        $is_master = false;
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $is_master = JJ_Edition_Controller::instance()->is_at_least( 'master' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $is_master = ( 'MASTER' === JJ_STYLE_GUIDE_LICENSE_TYPE );
        }

        if ( $is_master && class_exists( 'JJ_License_Issuer' ) ) {
            $issuer = JJ_License_Issuer::instance();
            if ( method_exists( $issuer, 'get_license' ) ) {
                $issued_license = $issuer->get_license( $license_key );
                if ( $issued_license ) {
                    // 발행된 라이센스가 비활성화 상태인지 확인
                    if ( isset( $issued_license['status'] ) && $issued_license['status'] !== 'active' ) {
                        return array(
                            'valid' => false,
                            'type' => $license_type,
                            'message' => __( '이 라이센스는 비활성화되었습니다.', 'jj-style-guide' ),
                            'activation_required' => true,
                        );
                    }
                    
                    // 만료일 확인
                    if ( isset( $issued_license['expires_timestamp'] ) && $issued_license['expires_timestamp'] < time() ) {
                        return array(
                            'valid' => false,
                            'type' => $license_type,
                            'message' => __( '이 라이센스는 만료되었습니다.', 'jj-style-guide' ),
                            'expired' => true,
                            'activation_required' => true,
                        );
                    }
                    
                    // Premium 버전: 사이트 바인딩 확인
                    if ( in_array( $license_type, array( self::TYPE_PREMIUM, self::TYPE_UNLIMITED ) ) ) {
                        $site_id = $this->get_site_id();
                        $activations = $issuer->get_license_activations( $license_key );
                        
                        if ( ! empty( $activations ) ) {
                            // 이미 다른 사이트에서 사용 중인지 확인
                            $bound_to_other_site = false;
                            foreach ( $activations as $activation ) {
                                if ( $activation['site_id'] !== $site_id ) {
                                    $bound_to_other_site = true;
                                    break;
                                }
                            }
                            
                            if ( $bound_to_other_site && $license_type === self::TYPE_PREMIUM ) {
                                // Premium은 1개 사이트만 사용 가능
                                return array(
                                    'valid' => false,
                                    'type' => $license_type,
                                    'message' => __( '이 라이센스는 다른 사이트에서 사용 중입니다. Premium 버전은 1개 사이트에서만 사용 가능합니다.', 'jj-style-guide' ),
                                    'activation_required' => true,
                                );
                            }
                        }
                        
                        // 사이트 바인딩 저장
                        $this->bind_site( $license_key, $site_id );
                        
                        // 활성화 기록
                        if ( method_exists( $issuer, 'record_activation' ) ) {
                            $issuer->record_activation( $license_key, $site_id, home_url() );
                        }
                    }
                    
                    // 마스터 버전에서 발행된 라이센스가 유효하면 바로 반환
                    $expires_timestamp = isset( $issued_license['expires_timestamp'] ) ? $issued_license['expires_timestamp'] : time() + ( 365 * 24 * 60 * 60 );
                    $days_until_expiry = ( $expires_timestamp - time() ) / ( 24 * 60 * 60 );
                    
                    return array(
                        'valid' => true,
                        'type' => $license_type,
                        'message' => __( '라이센스가 활성화되었습니다.', 'jj-style-guide' ),
                        'valid_until' => $expires_timestamp,
                        'days_until_expiry' => $days_until_expiry,
                        'expiring_soon' => $days_until_expiry <= 14,
                    );
                }
            }
        }
        
        // FREE 버전은 추가 검증 불필요
        if ( $license_type === self::TYPE_FREE ) {
            return array(
                'valid' => true,
                'type' => self::TYPE_FREE,
                'message' => __( 'Free 버전이 활성화되었습니다.', 'jj-style-guide' ),
                'valid_until' => time() + ( 365 * 24 * 60 * 60 ), // 1년
            );
        }
        
        // 모든 상용 버전은 외부 서버에서 검증 (필수)
        // 온라인 검증 시도 (항상 시도)
        $online_result = $this->verify_license_online( $license_key );
        
        if ( $online_result['valid'] ) {
            // 검증 성공 시 캐시 저장
            $this->cache_license_status( $online_result );
            return $online_result;
        }
        
        // 외부 서버 검증 실패 시 (네트워크 오류 등)
        // 오프라인 검증으로 폴백 (제한된 기간만 유효)
        return $this->verify_license_offline( $license_key, $license_type );
    }

    /**
     * 온라인 검증 필요 여부 확인
     * 
     * @return bool
     */
    private function should_verify_online() {
        $cached_status = get_option( $this->license_status_option, null );
        
        // 캐시가 없거나 만료되었으면 온라인 검증 필요
        if ( ! $cached_status || ! isset( $cached_status['valid_until'] ) ) {
            return true;
        }
        
        // 캐시가 만료되었으면 온라인 검증 필요
        if ( time() >= $cached_status['valid_until'] ) {
            return true;
        }
        
        // 마지막 검증 후 30일이 지났으면 온라인 검증 필요
        $last_verified = isset( $cached_status['last_verified'] ) ? $cached_status['last_verified'] : 0;
        if ( time() - $last_verified > ( $this->offline_cache_days * 24 * 60 * 60 ) ) {
            return true;
        }
        
        return false;
    }

    /**
     * 온라인 라이센스 검증 (외부 서버에서 모든 검증 수행)
     * 
     * @param string $license_key
     * @return array
     */
    private function verify_license_online( $license_key ) {
        // 입력값 검증
        if ( empty( $license_key ) || ! is_string( $license_key ) || strlen( $license_key ) > 100 ) {
            return array(
                'valid' => false,
                'message' => __( '유효하지 않은 라이센스 키 형식입니다.', 'jj-style-guide' ),
            );
        }
        
        // 라이센스 키 형식 검증
        if ( ! $this->verify_license_key_format( $license_key ) ) {
            return array(
                'valid' => false,
                'message' => __( '유효하지 않은 라이센스 키 형식입니다.', 'jj-style-guide' ),
            );
        }
        
        $site_id = $this->get_site_id();
        $license_type = $this->parse_license_type( $license_key );
        
        // 사이트 ID 검증 (MD5 해시 형식)
        if ( ! preg_match( '/^[a-f0-9]{32}$/i', $site_id ) ) {
            return array(
                'valid' => false,
                'message' => __( '유효하지 않은 사이트 ID입니다.', 'jj-style-guide' ),
            );
        }
        
        // POST 요청 데이터 (sanitization)
        $data = array(
            'license_key' => sanitize_text_field( $license_key ),
            'site_id' => sanitize_text_field( $site_id ),
            'site_url' => esc_url_raw( home_url() ),
            'action' => 'verify',
            'plugin_version' => sanitize_text_field( defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.1.6' ),
            'timestamp' => time(), // 타임스탬프 추가 (서버 측 검증용)
        );
        
        // WordPress HTTP API 사용 (보안 강화)
        $response = wp_remote_post( esc_url_raw( $this->license_server_url ), array(
            'timeout' => 10,
            'sslverify' => true, // SSL 검증 활성화
            'body' => $data,
            'headers' => array(
                'User-Agent' => 'JJ-Style-Guide/' . sanitize_text_field( defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.1.6' ),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'redirection' => 0, // 리다이렉션 방지
        ) );
        
        // 네트워크 오류 처리
        if ( is_wp_error( $response ) ) {
            error_log( 'JJ License Manager: 네트워크 오류 - ' . $response->get_error_message() );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        // HTTP 상태 코드 확인
        $response_code = wp_remote_retrieve_response_code( $response );
        if ( $response_code !== 200 ) {
            error_log( 'JJ License Manager: 서버 응답 오류 - HTTP ' . $response_code );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        $body = wp_remote_retrieve_body( $response );
        
        // 응답 본문 검증
        if ( empty( $body ) || strlen( $body ) > 10000 ) { // 응답 크기 제한
            error_log( 'JJ License Manager: 유효하지 않은 응답 본문' );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        $result = json_decode( $body, true );
        
        // JSON 파싱 오류 확인
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            error_log( 'JJ License Manager: JSON 파싱 오류 - ' . json_last_error_msg() );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        if ( ! $result || ! isset( $result['valid'] ) || ! is_bool( $result['valid'] ) ) {
            error_log( 'JJ License Manager: 유효하지 않은 응답 형식' );
            return $this->verify_license_offline( $license_key, $license_type );
        }
        
        // 외부 서버에서 반환된 결과 사용
        // 서버에서 다음 정보를 반환해야 함:
        // - valid: 라이센스 유효 여부
        // - type: 라이센스 타입
        // - message: 상태 메시지
        // - valid_until: 만료일 (타임스탬프)
        // - status: 활성화 상태 ('active', 'inactive', 'expired')
        // - expires_timestamp: 만료 타임스탬프
        
        // 만료일 확인
        if ( isset( $result['expires_timestamp'] ) && $result['expires_timestamp'] < time() ) {
            $result['valid'] = false;
            $result['expired'] = true;
            $result['message'] = __( '이 라이센스는 만료되었습니다.', 'jj-style-guide' );
            $result['activation_required'] = true;
        }
        
        // 만료 임박 확인 (14일 이하)
        if ( isset( $result['expires_timestamp'] ) && $result['expires_timestamp'] > time() ) {
            $days_until_expiry = ( $result['expires_timestamp'] - time() ) / ( 24 * 60 * 60 );
            $result['days_until_expiry'] = $days_until_expiry;
            $result['expiring_soon'] = $days_until_expiry <= 14;
        }
        
        // 활성화 필요 여부 확인
        if ( ! $result['valid'] || ( isset( $result['status'] ) && $result['status'] !== 'active' ) ) {
            $result['activation_required'] = true;
        }
        
        // 사이트 바인딩 확인 (Premium, Unlimited만)
        if ( in_array( $license_type, array( self::TYPE_PREMIUM, self::TYPE_UNLIMITED ) ) ) {
            if ( isset( $result['bound_site_id'] ) && $result['bound_site_id'] !== $site_id ) {
                // Premium 버전은 1개 사이트만 사용 가능
                if ( $license_type === self::TYPE_PREMIUM ) {
                    return array(
                        'valid' => false,
                        'type' => $license_type,
                        'message' => __( '이 라이센스는 다른 사이트에서 사용 중입니다. Premium 버전은 1개 사이트에서만 사용 가능합니다.', 'jj-style-guide' ),
                        'activation_required' => true,
                    );
                }
            }
            
            // 사이트 바인딩 저장
            $this->bind_site( $license_key, $site_id );
        }
        
        // 검증 시간 기록
        $result['last_verified'] = time();
        
        // valid_until이 없으면 서버에서 반환된 expires_timestamp 사용
        if ( ! isset( $result['valid_until'] ) && isset( $result['expires_timestamp'] ) ) {
            $result['valid_until'] = $result['expires_timestamp'];
        }
        
        // valid_until이 여전히 없으면 제한된 기간만 유효
        if ( ! isset( $result['valid_until'] ) ) {
            $result['valid_until'] = time() + ( 7 * 24 * 60 * 60 ); // 7일만 유효
        }

        return $result;
    }

    /**
     * 오프라인 라이센스 검증 (캐시 사용)
     * 
     * @param string $license_key
     * @param string $license_type
     * @return array
     */
    private function verify_license_offline( $license_key, $license_type ) {
        $cached_status = get_option( $this->license_status_option, null );
        
        // 캐시된 상태가 있고 유효하면 반환
        if ( $cached_status && isset( $cached_status['valid_until'] ) ) {
            if ( time() < $cached_status['valid_until'] ) {
                return $cached_status;
            }
        }
        
        // 캐시가 없거나 만료되었으면 라이센스 키 형식만 검증
        return array(
            'valid' => true,
            'type' => $license_type,
            'message' => __( '오프라인 모드로 실행 중입니다. 온라인 검증이 필요할 수 있습니다.', 'jj-style-guide' ),
            'valid_until' => time() + ( 7 * 24 * 60 * 60 ), // 7일만 유효
            'offline' => true,
        );
    }

    /**
     * 라이센스 상태 캐시 저장
     * 
     * @param array $status
     */
    private function cache_license_status( $status ) {
        update_option( $this->license_status_option, $status );
    }

    /**
     * 사이트 바인딩
     * 
     * @param string $license_key
     * @param string $site_id
     */
    private function bind_site( $license_key, $site_id ) {
        update_option( $this->site_binding_option, array(
            'license_key' => $license_key,
            'site_id' => $site_id,
            'bound_at' => time(),
        ) );
    }

    /**
     * 현재 라이센스 타입 가져오기
     * 
     * @return string
     */
    public function get_current_license_type() {
        // [Safety Lock] MASTER 버전은 무조건 MASTER 반환
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) === 'MASTER' ) {
            return 'MASTER';
        }

        // 코드 무결성 모니터 확인
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                // 잠금 상태면 FREE로 강제
                return self::TYPE_FREE;
            }
        }
        
        // 라이센스 강제 실행 클래스 확인
        if ( class_exists( 'JJ_License_Enforcement' ) ) {
            $enforcement = JJ_License_Enforcement::instance();
            $license_type = defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ? JJ_STYLE_GUIDE_LICENSE_TYPE : self::TYPE_FREE;
            return apply_filters( 'jj_get_license_type', $license_type );
        }
        
        // Pro 버전 활성화 코드 확인 (우선순위)
        if ( function_exists( 'get_option' ) ) {
            $pro_activation_data = get_option( 'jj_style_guide_pro_activation_data', array() );
            if ( ! empty( $pro_activation_data ) && isset( $pro_activation_data['license_type'] ) ) {
                $pro_license_type = $pro_activation_data['license_type'];
                
                // 활성화 코드 유효성 확인
                $pro_activation_code = get_option( 'jj_style_guide_pro_activation_code', '' );
                if ( ! empty( $pro_activation_code ) ) {
                    // 만료 확인
                    if ( isset( $pro_activation_data['expires_timestamp'] ) && $pro_activation_data['expires_timestamp'] > 0 ) {
                        if ( time() > $pro_activation_data['expires_timestamp'] ) {
                            return self::TYPE_FREE; // 만료됨
                        }
                    }
                    
                    // Pro 버전의 라이센스 타입 반환
                    return apply_filters( 'jj_get_license_type', $pro_license_type );
                }
            }
        }
        
        // 외부 서버 검증 결과 우선 사용 (보안 강화)
        $license_status = $this->get_license_status();
        if ( isset( $license_status['type'] ) && $license_status['valid'] ) {
            return $license_status['type'];
        }
        
        // 서버 검증 실패 시 하드코딩된 상수 사용 (하지만 검증됨)
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            // 보안 강화: 하드코딩된 값도 서버와 재검증
            return $this->verify_license_type_with_server( JJ_STYLE_GUIDE_LICENSE_TYPE );
        }
        
        $license_key = get_option( $this->license_key_option, '' );
        return $this->parse_license_type( $license_key );
    }

    /**
     * 기능 사용 가능 여부 확인
     * 
     * @param string $feature
     * @return bool
     */
    public function can_use_feature( $feature ) {
        // [Safety Lock] MASTER 버전은 모든 기능 사용 가능
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) === 'MASTER' ) {
            return true;
        }

        // 코드 무결성 모니터 확인 (최우선)
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            if ( $monitor->is_locked() ) {
                return false; // 잠금 상태면 모든 기능 비활성화
            }
        }
        
        $license_type = $this->get_current_license_type();
        $status = $this->get_license_status();
        
        // 서버 검증 실패 시 기능 비활성화 (보안 강화)
        if ( ! $status['valid'] || ! isset( $status['type'] ) ) {
            return false;
        }
        
        // 서버 검증 결과와 로컬 라이센스 타입 불일치 시 비활성화
        if ( isset( $status['type'] ) && $status['type'] !== $license_type ) {
            // 불일치 감지 시 개발자에게 알림 (비동기)
            $this->report_license_mismatch( $license_type, $status['type'] );
            return false;
        }
        
        // 기능별 라이센스 요구사항 확인
        $feature_requirements = $this->get_feature_requirements();
        
        if ( ! isset( $feature_requirements[ $feature ] ) ) {
            // 정의되지 않은 기능은 모든 라이센스에서 사용 가능
            return true;
        }
        
        $required_type = $feature_requirements[ $feature ];
        $type_hierarchy = array(
            self::TYPE_FREE => 1,
            self::TYPE_BASIC => 2,
            self::TYPE_PREMIUM => 3,
            self::TYPE_UNLIMITED => 4,
        );
        
        $current_level = isset( $type_hierarchy[ $license_type ] ) ? $type_hierarchy[ $license_type ] : 0;
        $required_level = isset( $type_hierarchy[ $required_type ] ) ? $type_hierarchy[ $required_type ] : 999;
        
        return $current_level >= $required_level;
    }
    
    /**
     * 라이센스 타입 불일치 보고
     * 
     * @param string $local_type 로컬 라이센스 타입
     * @param string $server_type 서버 검증 라이센스 타입
     */
    private function report_license_mismatch( $local_type, $server_type ) {
        // 이미 보고된 경우 중복 방지 (24시간 내)
        $last_report = get_option( 'jj_license_mismatch_last_report', 0 );
        if ( time() - $last_report < 24 * 60 * 60 ) {
            return;
        }
        
        if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
            $monitor = JJ_Code_Integrity_Monitor::instance();
            $monitor->notify_developer( array(
                array(
                    'type' => 'license_type_mismatch',
                    'severity' => 'critical',
                    'local_type' => $local_type,
                    'server_type' => $server_type,
                ),
            ), array() );
        }
        
        update_option( 'jj_license_mismatch_last_report', time() );
    }

    /**
     * 기능별 라이센스 요구사항
     * 
     * @return array
     */
    private function get_feature_requirements() {
        return array(
            // 팔레트 시스템
            'palette_brand' => self::TYPE_FREE,
            'palette_system' => self::TYPE_BASIC,
            'palette_alternative' => self::TYPE_PREMIUM,
            'palette_another' => self::TYPE_PREMIUM,
            'palette_temp' => self::TYPE_BASIC,
            
            // 타이포그래피
            'typography_h1_h2' => self::TYPE_FREE,
            'typography_h3_h4' => self::TYPE_BASIC,
            'typography_h5_h6' => self::TYPE_PREMIUM,
            
            // 버튼
            'button_primary' => self::TYPE_FREE,
            'button_secondary' => self::TYPE_BASIC,
            'button_text' => self::TYPE_PREMIUM,
            
            // 폼
            'form_basic' => self::TYPE_FREE,
            'form_advanced' => self::TYPE_BASIC,
            
            // 내보내기/불러오기
            'export_import_full' => self::TYPE_BASIC,
            'export_import_section' => self::TYPE_PREMIUM,
            
            // 관리자 센터
            'admin_center_general' => self::TYPE_BASIC,
            'admin_center_full' => self::TYPE_PREMIUM,
            
            // 실험실 센터
            'labs_center_list' => self::TYPE_BASIC,
            'labs_center_scanner' => self::TYPE_PREMIUM,
            'labs_center_full' => self::TYPE_UNLIMITED,
            
            // Customizer 통합
            'customizer_basic' => self::TYPE_FREE,
            'customizer_full' => self::TYPE_PREMIUM,
            
            // 어댑터 (개수 제한)
            'adapter_themes' => self::TYPE_FREE, // 개수는 버전별로 다름
            'adapter_plugins' => self::TYPE_FREE, // 개수는 버전별로 다름
            
            // 고급 기능
            'eyedropper' => self::TYPE_BASIC,
            'palette_load' => self::TYPE_PREMIUM,
            'customizer_sync' => self::TYPE_PREMIUM,
        );
    }

    /**
     * 라이센스 키 저장
     * 
     * @param string $license_key
     * @return array
     */
    public function save_license_key( $license_key ) {
        // 라이센스 키 제거 (빈 문자열인 경우)
        if ( empty( $license_key ) ) {
            delete_option( $this->license_key_option );
            delete_option( $this->license_status_option );
            delete_option( $this->license_data_option );
            delete_option( 'jj_style_guide_license_type_override' );
            
            return array(
                'success' => true,
                'message' => __( '라이센스 키가 제거되었습니다. Free 버전으로 실행됩니다.', 'jj-style-guide' ),
                'status' => array(
                    'valid' => false,
                    'type' => self::TYPE_FREE,
                    'message' => __( '라이센스 키가 설정되지 않았습니다. Free 버전으로 실행됩니다.', 'jj-style-guide' ),
                ),
            );
        }
        
        // 라이센스 키 형식 검증
        if ( ! $this->verify_license_key_format( $license_key ) ) {
            return array(
                'success' => false,
                'message' => __( '유효하지 않은 라이센스 키 형식입니다.', 'jj-style-guide' ),
            );
        }
        
        // 라이센스 키 저장
        update_option( $this->license_key_option, $license_key );
        
        // 라이센스 타입 업데이트
        $license_type = $this->parse_license_type( $license_key );
        if ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            // 상수는 재정의할 수 없으므로 옵션에 저장
            update_option( 'jj_style_guide_license_type_override', $license_type );
        }
        
        // 라이센스 검증
        $status = $this->verify_license( true );
        
        return array(
            'success' => $status['valid'],
            'message' => $status['message'],
            'status' => $status,
        );
    }

    /**
     * 결제 페이지 URL 가져오기
     * 
     * @param string $action (upgrade, renew)
     * @return string
     */
    public function get_purchase_url( $action = 'upgrade' ) {
        // 결제 페이지 주소 (고정)
        return 'https://j-j-labs.com/?product=plugin-jj-center-of-style-setting';
    }

    /**
     * 업그레이드 프롬프트 메시지 생성
     * 
     * @param string $feature
     * @return string
     */
    public function get_upgrade_prompt( $feature ) {
        $license_type = $this->get_current_license_type();

        // [Safety Lock] MASTER 버전은 업그레이드 프롬프트 표시 안 함
        if ( 'MASTER' === $license_type || ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) && 'MASTER' === strtoupper( JJ_STYLE_GUIDE_LICENSE_TYPE ) ) ) {
            return '';
        }

        $feature_requirements = $this->get_feature_requirements();
        
        if ( ! isset( $feature_requirements[ $feature ] ) ) {
            return '';
        }
        
        $required_type = $feature_requirements[ $feature ];
        
        if ( $this->can_use_feature( $feature ) ) {
            return '';
        }
        
        $type_names = array(
            self::TYPE_FREE => __( 'Free', 'jj-style-guide' ),
            self::TYPE_BASIC => __( 'Basic', 'jj-style-guide' ),
            self::TYPE_PREMIUM => __( 'Premium', 'jj-style-guide' ),
            self::TYPE_UNLIMITED => __( 'Unlimited', 'jj-style-guide' ),
        );
        
        $current_name = isset( $type_names[ $license_type ] ) ? $type_names[ $license_type ] : $license_type;
        $required_name = isset( $type_names[ $required_type ] ) ? $type_names[ $required_type ] : $required_type;
        
        // 라이센스 상태 확인 (만료 여부)
        $license_status = $this->get_license_status();
        $is_expired = false;
        $action_text = __( '업그레이드하기', 'jj-style-guide' );
        
        if ( isset( $license_status['valid_until'] ) && $license_status['valid_until'] < time() ) {
            $is_expired = true;
            $action_text = __( '기한 연장하기', 'jj-style-guide' );
        } elseif ( ! $license_status['valid'] ) {
            // 라이센스가 유효하지 않은 경우도 기한 연장 고려
            if ( in_array( $license_type, array( self::TYPE_BASIC, self::TYPE_PREMIUM, self::TYPE_UNLIMITED ) ) ) {
                $is_expired = true;
                $action_text = __( '기한 연장하기', 'jj-style-guide' );
            }
        }
        
        $purchase_url = $this->get_purchase_url( $is_expired ? 'renew' : 'upgrade' );
        
        return sprintf(
            __( '이 기능은 %s 버전 이상에서 사용할 수 있습니다. 현재 버전: %s. <a href="%s" target="_blank" rel="noopener noreferrer" style="font-weight: 600; color: #2271b1; text-decoration: underline;">%s</a>', 'jj-style-guide' ),
            $required_name,
            $current_name,
            esc_url( $purchase_url ),
            $action_text
        );
    }
}

```

### 추가 UI/UX 개선 반영 완료(스크린샷 레퍼런스 기반)

이번에는 사장님이 주신 스크린샷의 “플러그인 목록 UX”를 기준으로, **자동 업데이트/강조 링크/필수 플러그인 안내/추천 팔레트 연계**를 더 깊게 정리했습니다.

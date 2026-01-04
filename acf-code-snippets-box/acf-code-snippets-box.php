<?php
/**
 * Plugin Name:       ACF Code Snippets Box - Advanced Custom Function Manager
 * Plugin URI:        https://3j-labs.com
 * Description:       ACF Code Snippets Box (Advanced Custom Function) - WordPress 코어를 수정하지 않고 JS, CSS, PHP, HTML 코드 스니펫을 저장하고 조건에 따라 실행하는 강력한 코드 관리 플러그인입니다. ACF CSS (Advanced Custom Fonts & Colors & Styles) 패밀리 플러그인으로, 스타일 변수와 디자인 토큰을 쉽게 참조할 수 있습니다.
 * Version:           2.3.2
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com
 * Text Domain:       acf-code-snippets-box
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * @package ACF_Code_Snippets_Box
 */

// 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 플러그인 상수 정의
 */
define( 'ACF_CSB_VERSION', '2.3.1' ); // [v2.3.1] 깨진 링크 수정 - jj-style-center → jj-style-guide-cockpit
define( 'ACF_CSB_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACF_CSB_URL', plugin_dir_url( __FILE__ ) );
define( 'ACF_CSB_BASENAME', plugin_basename( __FILE__ ) );
define( 'ACF_CSB_SLUG', 'acf-code-snippets-box' );

/**
 * 플러그인 메인 클래스
 */
final class ACF_Code_Snippets_Box {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 싱글톤 인스턴스 반환
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
        $this->load_dependencies();
        $this->init_hooks();
        
        // [Phase 19.1] 플러그인 목록 페이지 UI/UX 개선
        $this->init_plugin_list_enhancer();
    }
    
    /**
     * [Phase 19.1] 플러그인 목록 페이지 향상 초기화
     */
    private function init_plugin_list_enhancer() {
        // ACF CSS Manager의 Plugin List Enhancer 클래스 사용
        if ( class_exists( 'JJ_Plugin_List_Enhancer' ) ) {
            $enhancer = new JJ_Plugin_List_Enhancer();
            $enhancer->init( array(
                'plugin_file' => __FILE__,
                'plugin_name' => 'ACF Code Snippets Box',
                'settings_url' => admin_url( 'admin.php?page=acf-code-snippets' ),
                'text_domain' => 'acf-code-snippets-box',
                'version_constant' => 'ACF_CSB_VERSION',
                'license_constant' => 'ACF_CSB_LICENSE_TYPE',
                'upgrade_url' => 'https://3j-labs.com',
                'docs_url' => admin_url( 'admin.php?page=acf-code-snippets' ),
                'support_url' => 'https://3j-labs.com/support',
            ) );
        }
    }

    /**
     * 의존성 로드
     */
    private function load_dependencies() {
        // 코어 클래스
        require_once ACF_CSB_PATH . 'includes/class-acf-csb-post-type.php';
        require_once ACF_CSB_PATH . 'includes/class-acf-csb-executor.php';
        require_once ACF_CSB_PATH . 'includes/class-acf-csb-triggers.php';
        require_once ACF_CSB_PATH . 'includes/class-acf-csb-admin.php';
        require_once ACF_CSB_PATH . 'includes/class-acf-csb-presets.php';
        require_once ACF_CSB_PATH . 'includes/class-acf-csb-acf-css-bridge.php';
        
        // 고급 조건 빌더 (WPCODEBOX2 스타일)
        require_once ACF_CSB_PATH . 'includes/class-acf-csb-condition-builder.php';
        
        // 라이선스 & 기능 접근 제어
        require_once ACF_CSB_PATH . 'includes/class-acf-csb-license.php';
        
        // 넛지 마케팅 시스템
        require_once ACF_CSB_PATH . 'includes/class-acf-csb-nudge-system.php';
        
        // 서드파티 연동 (FacetWP, Perfmatters, ACF, WooCommerce 등)
        require_once ACF_CSB_PATH . 'includes/class-acf-csb-integrations.php';
        
        // 내보내기/가져오기
        require_once ACF_CSB_PATH . 'includes/class-acf-csb-export-import.php';
        
        // 클라우드 동기화 (Pro Premium+)
        require_once ACF_CSB_PATH . 'includes/class-acf-csb-cloud-sync.php';
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 플러그인 활성화/비활성화
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        // 초기화
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'init', array( $this, 'init' ) );

        // 관리자 메뉴
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        
        // 관리자 에셋
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // 플러그인 링크
        add_filter( 'plugin_action_links_' . ACF_CSB_BASENAME, array( $this, 'plugin_action_links' ) );
    }

    /**
     * 플러그인 활성화
     */
    public function activate() {
        // 권한 확인
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        // CPT 등록 후 rewrite rules 갱신
        ACF_CSB_Post_Type::instance()->register();
        flush_rewrite_rules();

        // 기본 옵션 설정
        $defaults = array(
            'enable_php_execution' => false, // 보안상 기본 비활성화
            'enable_error_logging' => true,
            'syntax_highlighting'  => true,
            'auto_complete'        => true,
        );
        add_option( 'acf_csb_settings', $defaults );

        // 활성화 시간 기록
        update_option( 'acf_csb_activated', time() );
    }

    /**
     * 플러그인 비활성화
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * 텍스트 도메인 로드
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'acf-code-snippets-box',
            false,
            dirname( ACF_CSB_BASENAME ) . '/languages/'
        );
    }

    /**
     * 초기화
     */
    public function init() {
        // Post Type 등록
        ACF_CSB_Post_Type::instance()->register();

        // 실행기 초기화
        ACF_CSB_Executor::instance()->init();

        // 프리셋 초기화
        ACF_CSB_Presets::instance()->init();

        // ACF CSS 연동
        ACF_CSB_ACF_CSS_Bridge::instance()->init();
    }

    /**
     * 관리자 에셋 로드
     * [v2.2.0] UI System 2026 Enhancement
     */
    public function enqueue_admin_assets( $hook ) {
        // ACF Code Snippets 페이지에서만 로드
        if ( strpos( $hook, 'acf-code-snippets' ) === false && strpos( $hook, 'acf_code_snippet' ) === false ) {
            return;
        }
        
        // [v2.2.0] UI System 2026 Enhancement
        $enhanced_css_path = ACF_CSB_PATH . 'assets/css/jj-code-snippets-enhanced-2026.css';
        if ( file_exists( $enhanced_css_path ) ) {
            $css_version = ACF_CSB_VERSION . '.' . filemtime( $enhanced_css_path );
            wp_enqueue_style(
                'acf-csb-enhanced-2026',
                ACF_CSB_URL . 'assets/css/jj-code-snippets-enhanced-2026.css',
                array(),
                $css_version
            );
        }
    }
    
    /**
     * 관리자 메뉴
     */
    public function admin_menu() {
        // 메인 메뉴
        add_menu_page(
            __( 'Code Snippets', 'acf-code-snippets-box' ),
            __( 'Code Snippets', 'acf-code-snippets-box' ),
            'manage_options',
            'acf-code-snippets',
            array( $this, 'render_main_page' ),
            'dashicons-editor-code',
            30
        );

        // 서브메뉴: 모든 스니펫
        add_submenu_page(
            'acf-code-snippets',
            __( '모든 스니펫', 'acf-code-snippets-box' ),
            __( '모든 스니펫', 'acf-code-snippets-box' ),
            'manage_options',
            'edit.php?post_type=acf_code_snippet'
        );

        // 서브메뉴: 새 스니펫 추가
        add_submenu_page(
            'acf-code-snippets',
            __( '새 스니펫 추가', 'acf-code-snippets-box' ),
            __( '새 스니펫 추가', 'acf-code-snippets-box' ),
            'manage_options',
            'post-new.php?post_type=acf_code_snippet'
        );

        // 서브메뉴: 프리셋 라이브러리
        add_submenu_page(
            'acf-code-snippets',
            __( '프리셋 라이브러리', 'acf-code-snippets-box' ),
            __( '프리셋 라이브러리', 'acf-code-snippets-box' ),
            'manage_options',
            'acf-code-snippets-presets',
            array( $this, 'render_presets_page' )
        );

        // 서브메뉴: 설정
        add_submenu_page(
            'acf-code-snippets',
            __( '설정', 'acf-code-snippets-box' ),
            __( '설정', 'acf-code-snippets-box' ),
            'manage_options',
            'acf-code-snippets-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * 메인 페이지 렌더링
     */
    public function render_main_page() {
        include ACF_CSB_PATH . 'admin/views/main-page.php';
    }

    /**
     * 프리셋 페이지 렌더링
     */
    public function render_presets_page() {
        include ACF_CSB_PATH . 'admin/views/presets-page.php';
    }

    /**
     * 설정 페이지 렌더링
     */
    public function render_settings_page() {
        include ACF_CSB_PATH . 'admin/views/settings-page.php';
    }

    /**
     * 플러그인 액션 링크
     */
    public function plugin_action_links( $links ) {
        $plugin_links = array(
            '<a href="' . admin_url( 'admin.php?page=acf-code-snippets-settings' ) . '">' . __( '설정', 'acf-code-snippets-box' ) . '</a>',
        );
        return array_merge( $plugin_links, $links );
    }

    /**
     * ACF CSS 플러그인 활성화 여부 확인
     */
    public static function is_acf_css_active() {
        return defined( 'JJ_STYLE_GUIDE_VERSION' );
    }
}

/**
 * 플러그인 인스턴스 반환
 */
function acf_code_snippets_box() {
    return ACF_Code_Snippets_Box::instance();
}

// 플러그인 초기화
add_action( 'plugins_loaded', 'acf_code_snippets_box', 5 );

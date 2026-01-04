<?php
/**
 * Plugin Name:       3J Labs Marketing Automation Dashboard
 * Plugin URI:        https://3j-labs.com/
 * Description:       3J Labs 플러그인 패밀리의 종합 마케팅 자동화 대시보드입니다. 모든 플러그인의 사용 데이터를 통합 분석하고, SEO 최적화, 캠페 추적, 마케팅 성과 측정을 제공합니다. Google Analytics, Search Console, 소셜 미디어 통합을 지원합니다.
 * Version:           2.0.0
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com/
 * Text Domain:       jj-marketing-dashboard
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package JJ_Marketing_Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 플러그인 상수 정의
 */
define( 'JJ_MARKETING_DASHBOARD_VERSION', '2.1.0' ); // [v2.1.0] v25.0.0: 보안 강화 및 라이센스 관리 추가
define( 'JJ_MARKETING_DASHBOARD_PATH', plugin_dir_path( __FILE__ ) );
define( 'JJ_MARKETING_DASHBOARD_URL', plugin_dir_url( __FILE__ ) );
define( 'JJ_MARKETING_DASHBOARD_BASENAME', plugin_basename( __FILE__ ) );
define( 'JJ_MARKETING_DASHBOARD_SLUG', 'jj-marketing-dashboard' );

// [v25.0.0] 보안 모듈 및 라이센스 관리 로드
$shared_path = dirname( dirname( __FILE__ ) ) . '/shared-ui-assets';
if ( file_exists( $shared_path . '/class-jj-security-module-v25.php' ) ) {
    require_once $shared_path . '/class-jj-security-module-v25.php';
    if ( class_exists( 'JJ_Security_Module_V25_Loader' ) ) {
        JJ_Security_Module_V25_Loader::instance( JJ_MARKETING_DASHBOARD_PATH, JJ_MARKETING_DASHBOARD_URL, JJ_MARKETING_DASHBOARD_VERSION, JJ_MARKETING_DASHBOARD_SLUG );
    }
}
if ( file_exists( $shared_path . '/class-jj-license-manager-shared.php' ) ) {
    require_once $shared_path . '/class-jj-license-manager-shared.php';
    if ( class_exists( 'JJ_License_Manager_Shared' ) ) {
        JJ_License_Manager_Shared::instance( JJ_MARKETING_DASHBOARD_SLUG );
    }
}

/**
 * 메인 플러그인 클래스
 */
final class JJ_Marketing_Dashboard {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    private function load_dependencies() {
        require_once JJ_MARKETING_DASHBOARD_PATH . 'includes/class-jj-analytics-collector.php';
        require_once JJ_MARKETING_DASHBOARD_PATH . 'includes/class-jj-seo-optimizer.php';
        require_once JJ_MARKETING_DASHBOARD_PATH . 'includes/class-jj-campaign-tracker.php';
        require_once JJ_MARKETING_DASHBOARD_PATH . 'includes/class-jj-dashboard-admin.php';
        require_once JJ_MARKETING_DASHBOARD_PATH . 'includes/class-jj-integration-manager.php';
        
        // 클래스 인스턴스 초기화
        if ( class_exists( 'JJ_Campaign_Tracker' ) ) {
            new JJ_Campaign_Tracker();
        }
        if ( class_exists( 'JJ_Integration_Manager' ) ) {
            new JJ_Integration_Manager();
        }
    }

    private function init_hooks() {
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'init', array( $this, 'init' ) );

        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 30 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        add_action( 'wp_ajax_jj_marketing_get_stats', array( $this, 'ajax_get_stats' ) );
        add_action( 'wp_ajax_jj_marketing_save_settings', array( $this, 'ajax_save_settings' ) );
        add_action( 'wp_ajax_jj_marketing_run_seo_audit', array( $this, 'ajax_run_seo_audit' ) );
    }

    public function init() {
        if ( class_exists( 'JJ_Plugin_List_Enhancer' ) ) {
            $enhancer = new JJ_Plugin_List_Enhancer();
            $enhancer->init( array(
                'plugin_file' => __FILE__,
                'plugin_name' => '3J Labs Marketing Dashboard',
                'settings_url' => admin_url( 'admin.php?page=' . JJ_MARKETING_DASHBOARD_SLUG ),
                'text_domain' => 'jj-marketing-dashboard',
                'version_constant' => 'JJ_MARKETING_DASHBOARD_VERSION',
                'license_constant' => 'JJ_MARKETING_DASHBOARD_LICENSE',
                'upgrade_url' => 'https://3j-labs.com/',
                'docs_url' => 'https://3j-labs.com/docs',
                'support_url' => 'https://3j-labs.com/support',
            ) );
        }
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            'jj-marketing-dashboard',
            false,
            dirname( JJ_MARKETING_DASHBOARD_BASENAME ) . '/languages'
        );
    }

    public function add_admin_menu() {
        add_menu_page(
            __( 'Marketing Dashboard', 'jj-marketing-dashboard' ),
            '마케팅대시보드 📊',
            'manage_options',
            JJ_MARKETING_DASHBOARD_SLUG,
            array( $this, 'render_dashboard' ),
            'dashicons-chart-pie',
            26 // 워드프레스 '마케팅' 바로 아래
        );

        // 서브메뉴: 종합 대시보드
        add_submenu_page(
            JJ_MARKETING_DASHBOARD_SLUG,
            __( 'Overview', 'jj-marketing-dashboard' ),
            __( '종합 대시보드', 'jj-marketing-dashboard' ),
            'manage_options',
            JJ_MARKETING_DASHBOARD_SLUG,
            array( $this, 'render_dashboard' )
        );

        // 서브메뉴: 통계 분석
        add_submenu_page(
            JJ_MARKETING_DASHBOARD_SLUG,
            __( 'Analytics', 'jj-marketing-dashboard' ),
            __( '통계 분석', 'jj-marketing-dashboard' ),
            'manage_options',
            'jj-marketing-analytics',
            array( $this, 'render_analytics' )
        );

        // 서브메뉴: SEO 최적화
        add_submenu_page(
            JJ_MARKETING_DASHBOARD_SLUG,
            __( 'SEO', 'jj-marketing-dashboard' ),
            __( 'SEO 최적화', 'jj-marketing-dashboard' ),
            'manage_options',
            'jj-marketing-seo',
            array( $this, 'render_seo' )
        );

        // 서브메뉴: 캠페 트래커
        add_submenu_page(
            JJ_MARKETING_DASHBOARD_SLUG,
            __( 'Campaigns', 'jj-marketing-dashboard' ),
            __( '캠페 트래커', 'jj-marketing-dashboard' ),
            'manage_options',
            'jj-marketing-campaigns',
            array( $this, 'render_campaigns' )
        );

        // 서브메뉴: 통합 관리
        add_submenu_page(
            JJ_MARKETING_DASHBOARD_SLUG,
            __( 'Integrations', 'jj-marketing-dashboard' ),
            __( '통합 관리', 'jj-marketing-dashboard' ),
            'manage_options',
            'jj-marketing-integrations',
            array( $this, 'render_integrations' )
        );

        // 서브메뉴: 설정
        add_submenu_page(
            JJ_MARKETING_DASHBOARD_SLUG,
            __( 'Settings', 'jj-marketing-dashboard' ),
            __( '설정', 'jj-marketing-dashboard' ),
            'manage_options',
            'jj-marketing-settings',
            array( $this, 'render_settings' )
        );
    }

    public function render_dashboard() {
        require_once JJ_MARKETING_DASHBOARD_PATH . 'includes/views/view-dashboard.php';
    }

    public function render_analytics() {
        require_once JJ_MARKETING_DASHBOARD_PATH . 'includes/views/view-analytics.php';
    }

    public function render_seo() {
        require_once JJ_MARKETING_DASHBOARD_PATH . 'includes/views/view-seo.php';
    }

    public function render_campaigns() {
        require_once JJ_MARKETING_DASHBOARD_PATH . 'includes/views/view-campaigns.php';
    }

    public function render_integrations() {
        require_once JJ_MARKETING_DASHBOARD_PATH . 'includes/views/view-integrations.php';
    }

    public function render_settings() {
        require_once JJ_MARKETING_DASHBOARD_PATH . 'includes/views/view-settings.php';
    }

    public function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'jj-marketing-dashboard' ) === false ) {
            return;
        }

        // UI System 2026
        $enhanced_css_path = JJ_MARKETING_DASHBOARD_PATH . 'assets/css/jj-marketing-dashboard-enhanced-2026.css';
        if ( file_exists( $enhanced_css_path ) ) {
            $css_version = JJ_MARKETING_DASHBOARD_VERSION . '.' . filemtime( $enhanced_css_path );
            wp_enqueue_style(
                'jj-marketing-dashboard-enhanced-2026',
                JJ_MARKETING_DASHBOARD_URL . 'assets/css/jj-marketing-dashboard-enhanced-2026.css',
                array(),
                $css_version
            );
        }

        wp_enqueue_script(
            'jj-marketing-dashboard',
            JJ_MARKETING_DASHBOARD_URL . 'assets/js/marketing-dashboard.js',
            array( 'jquery' ),
            JJ_MARKETING_DASHBOARD_VERSION,
            true
        );

        // Google Charts
        wp_enqueue_script( 'google-charts', 'https://www.gstatic.com/charts/loader.js', array(), null, true );
    }

    public function ajax_get_stats() {
        check_ajax_referer( 'jj_marketing_dashboard_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'jj-marketing-dashboard' ) );
        }

        $collector = new JJ_Analytics_Collector();
        $stats = $collector->get_comprehensive_stats();

        wp_send_json_success( $stats );
    }

    public function ajax_save_settings() {
        check_ajax_referer( 'jj_marketing_dashboard_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'jj-marketing-dashboard' ) );
        }

        $settings = isset( $_POST['settings'] ) ? map_deep( stripslashes( $_POST['settings'] ), 'sanitize_text_field' ) : array();
        update_option( 'jj_marketing_dashboard_settings', $settings );

        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', 'jj-marketing-dashboard' ) ) );
    }

    public function ajax_run_seo_audit() {
        check_ajax_referer( 'jj_marketing_dashboard_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'jj-marketing-dashboard' ) );
        }

        $seo = new JJ_SEO_Optimizer();
        $results = $seo->run_audit();

        wp_send_json_success( $results );
    }

    public function activate() {
        flush_rewrite_rules();
        
        $default_settings = array(
            'google_analytics_enabled' => false,
            'google_analytics_id' => '',
            'google_search_console_enabled' => false,
            'auto_seo_audit' => true,
            'email_notifications' => false,
            'notification_email' => '',
        );
        
        add_option( 'jj_marketing_dashboard_settings', $default_settings );
        update_option( 'jj_marketing_dashboard_activated', time() );
    }

    public function deactivate() {
        flush_rewrite_rules();
    }
}

/**
 * 플러그인 초기화
 */
function jj_marketing_dashboard_init() {
    return JJ_Marketing_Dashboard::instance();
}
add_action( 'plugins_loaded', 'jj_marketing_dashboard_init' );

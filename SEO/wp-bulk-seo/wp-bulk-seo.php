<?php
/**
 * Plugin Name: WP Bulk SEO & AEO
 * Plugin URI: https://3jlabs.com/wp-bulk-seo
 * Description: Advanced SEO and AEO (Answer Engine Optimization) plugin with bulk analysis, PageSpeed integration, and AI-powered optimization.
 * Version: 1.0.0
 * Author: 3J Labs
 * Author URI: https://3jlabs.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-bulk-seo
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package WP_Bulk_SEO
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('WP_BULK_SEO_VERSION', '2.0.0'); // [v2.0.0] 실시간 모니터링, 자동 최적화, Google API 통합
define('WP_BULK_SEO_PLUGIN_FILE', __FILE__);
define('WP_BULK_SEO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_BULK_SEO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_BULK_SEO_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
final class WP_Bulk_SEO {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required files
     */
    private function includes() {
        // Database
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/class-database.php';

        // Algorithm
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/algorithm/class-ranking-factors-db.php';
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/algorithm/class-seo-scorer.php';
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/algorithm/class-seo-analyzer.php';

        // Schema & Sitemap
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/class-schema-generator.php';
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/class-sitemap-manager.php';

        // AEO
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/aeo/class-aeo-engine.php';
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/aeo/class-faq-generator.php';
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/aeo/class-featured-snippet-optimizer.php';

        // API
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/api/class-rest-controller.php';
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/api/class-ai-provider.php';
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/api/class-google-search-console.php';

        // [v2.0.0] Real-time Monitor
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/class-realtime-monitor.php';

        // [v2.0.0] Auto Optimizer
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/class-auto-optimizer.php';

        // [v2.1.0] Keyword Extractor
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/class-keyword-extractor.php';

        // [v2.1.0] Alli AI Style Automation
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/class-alli-automation.php';

        // Admin
        if (is_admin()) {
            require_once WP_BULK_SEO_PLUGIN_DIR . 'admin/class-admin.php';
        }

        // Frontend
        require_once WP_BULK_SEO_PLUGIN_DIR . 'includes/class-frontend.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        add_action('plugins_loaded', [$this, 'on_plugins_loaded']);
        add_action('init', [$this, 'init']);
    }

    /**
     * Plugins loaded
     */
    public function on_plugins_loaded() {
        load_plugin_textdomain('wp-bulk-seo', false, dirname(WP_BULK_SEO_PLUGIN_BASENAME) . '/languages');
    }

    /**
     * Init
     */
    public function init() {
        // Initialize components
        WP_Bulk_SEO_Database::get_instance();
        WP_Bulk_SEO_Schema_Generator::get_instance();
        WP_Bulk_SEO_Sitemap_Manager::get_instance();
        WP_Bulk_SEO_REST_Controller::get_instance();
        WP_Bulk_SEO_Frontend::get_instance();

        // [v2.0.0] Real-time Monitor
        WP_Bulk_SEO_Realtime_Monitor::instance();

        // [v2.0.0] Auto Optimizer
        WP_Bulk_SEO_Auto_Optimizer::instance();

        // [v2.1.0] Keyword Extractor
        WP_Bulk_SEO_Keyword_Extractor::instance();

        // [v2.1.0] Alli AI Automation
        WP_Bulk_SEO_Alli_Automation::instance();

        if (is_admin()) {
            WP_Bulk_SEO_Admin::get_instance();
        }
    }

    /**
     * Activate plugin
     */
    public function activate() {
        // Create database tables
        WP_Bulk_SEO_Database::get_instance()->create_tables();

        // Initialize ranking factors
        WP_Bulk_SEO_Ranking_Factors_DB::get_instance()->initialize_factors();

        // Set default options
        $this->set_default_options();

        // Flush rewrite rules for sitemap
        flush_rewrite_rules();

        // Set activation flag
        set_transient('wp_bulk_seo_activated', true, 30);
    }

    /**
     * Deactivate plugin
     */
    public function deactivate() {
        // Clear scheduled events
        wp_clear_scheduled_hook('wp_bulk_seo_daily_cron');

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Set default options
     */
    private function set_default_options() {
        $defaults = [
            'wp_bulk_seo_general' => [
                'post_types' => ['post', 'page'],
                'auto_analyze' => false,
                'score_in_list' => true,
            ],
            'wp_bulk_seo_sitemap' => [
                'enabled' => true,
                'post_types' => ['post', 'page'],
                'include_images' => true,
                'ping_search_engines' => true,
            ],
            'wp_bulk_seo_aeo' => [
                'enabled' => true,
                'auto_generate_faq' => false,
                'auto_optimize_snippets' => false,
                'ai_provider' => 'openai',
            ],
        ];

        foreach ($defaults as $option => $value) {
            if (false === get_option($option)) {
                add_option($option, $value);
            }
        }
    }

    /**
     * Get plugin URL
     */
    public function get_plugin_url() {
        return WP_BULK_SEO_PLUGIN_URL;
    }

    /**
     * Get plugin path
     */
    public function get_plugin_path() {
        return WP_BULK_SEO_PLUGIN_DIR;
    }

    /**
     * Get version
     */
    public function get_version() {
        return WP_BULK_SEO_VERSION;
    }
}

/**
 * Get plugin instance
 */
function wp_bulk_seo() {
    return WP_Bulk_SEO::get_instance();
}

// Initialize plugin
wp_bulk_seo();

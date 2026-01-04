<?php
/**
 * Plugin Name: 1-Click SEO Pro
 * Plugin URI: https://3jlabs.com/1-click-seo-pro
 * Description: All-in-one SEO & AEO optimization with AI-powered content suggestions, 200+ ranking factors analysis, and Answer Engine Optimization for WordPress.
 * Version: 1.0.0
 * Author: 3J Labs
 * Author URI: https://3jlabs.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: oneclick-seo-pro
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package OneClick_SEO_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

// Prevent conflict with other SEO plugins
if (defined('ONECLICK_SEO_PRO_VERSION')) {
    return;
}

// Plugin constants
define('ONECLICK_SEO_PRO_VERSION', '1.0.0');
define('ONECLICK_SEO_PRO_FILE', __FILE__);
define('ONECLICK_SEO_PRO_DIR', plugin_dir_path(__FILE__));
define('ONECLICK_SEO_PRO_URL', plugin_dir_url(__FILE__));
define('ONECLICK_SEO_PRO_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
final class OneClick_SEO_Pro {

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
        $include_files = [
            'includes/class-database.php',
            'includes/algorithm/class-ranking-factors-db.php',
            'includes/algorithm/class-seo-scorer.php',
            'includes/algorithm/class-seo-analyzer.php',
            'includes/class-schema-generator.php',
            'includes/class-sitemap-manager.php',
            'includes/aeo/class-aeo-engine.php',
            'includes/aeo/class-faq-generator.php',
            'includes/aeo/class-featured-snippet-optimizer.php',
            'includes/api/class-rest-controller.php',
            'includes/api/class-ai-provider.php',
            'includes/class-frontend.php',
        ];

        foreach ($include_files as $file) {
            $filepath = ONECLICK_SEO_PRO_DIR . $file;
            if (file_exists($filepath)) {
                require_once $filepath;
            }
        }

        // Admin
        if (is_admin()) {
            $admin_file = ONECLICK_SEO_PRO_DIR . 'admin/class-admin.php';
            if (file_exists($admin_file)) {
                require_once $admin_file;
            }
        }
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
        load_plugin_textdomain('oneclick-seo-pro', false, dirname(ONECLICK_SEO_PRO_BASENAME) . '/languages');
    }

    /**
     * Init
     */
    public function init() {
        // Initialize components with class existence check
        $components = [
            'OneClick_SEO_Pro_Database',
            'OneClick_SEO_Pro_Schema_Generator',
            'OneClick_SEO_Pro_Sitemap_Manager',
            'OneClick_SEO_Pro_REST_Controller',
            'OneClick_SEO_Pro_Frontend',
        ];

        foreach ($components as $class) {
            if (class_exists($class)) {
                $class::get_instance();
            }
        }

        if (is_admin() && class_exists('OneClick_SEO_Pro_Admin')) {
            OneClick_SEO_Pro_Admin::get_instance();
        }
    }

    /**
     * Activate plugin
     */
    public function activate() {
        // Create database tables
        if (class_exists('OneClick_SEO_Pro_Database')) {
            OneClick_SEO_Pro_Database::get_instance()->create_tables();
        }

        // Initialize ranking factors
        if (class_exists('OneClick_SEO_Pro_Ranking_Factors_DB')) {
            OneClick_SEO_Pro_Ranking_Factors_DB::get_instance()->initialize_factors();
        }

        // Set default options
        $this->set_default_options();

        // Flush rewrite rules for sitemap
        flush_rewrite_rules();

        // Set activation flag
        set_transient('oneclick_seo_pro_activated', true, 30);
    }

    /**
     * Deactivate plugin
     */
    public function deactivate() {
        // Clear scheduled events
        wp_clear_scheduled_hook('oneclick_seo_pro_daily_cron');

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Set default options
     */
    private function set_default_options() {
        $defaults = [
            'oneclick_seo_pro_general' => [
                'post_types' => ['post', 'page'],
                'auto_analyze' => false,
                'score_in_list' => true,
            ],
            'oneclick_seo_pro_sitemap' => [
                'enabled' => true,
                'post_types' => ['post', 'page'],
                'include_images' => true,
                'ping_search_engines' => true,
            ],
            'oneclick_seo_pro_aeo' => [
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
        return ONECLICK_SEO_PRO_URL;
    }

    /**
     * Get plugin path
     */
    public function get_plugin_path() {
        return ONECLICK_SEO_PRO_DIR;
    }

    /**
     * Get version
     */
    public function get_version() {
        return ONECLICK_SEO_PRO_VERSION;
    }
}

/**
 * Get plugin instance
 */
function oneclick_seo_pro() {
    return OneClick_SEO_Pro::get_instance();
}

// Initialize plugin
oneclick_seo_pro();

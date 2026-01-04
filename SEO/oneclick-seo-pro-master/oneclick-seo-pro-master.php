<?php
/**
 * Plugin Name: 1-Click SEO Pro Master
 * Plugin URI: https://3jlabs.com/1-click-seo-pro
 * Description: Master dashboard for managing 1-Click SEO Pro licenses, remote sites, rank tracking, and cross-platform SEO services.
 * Version: 1.0.0
 * Author: 3J Labs
 * Author URI: https://3jlabs.com
 * License: GPL v2 or later
 * Text Domain: oneclick-seo-master
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package OneClick_SEO_Pro_Master
 */

if (!defined('ABSPATH')) {
    exit;
}

// Prevent conflict with other plugins
if (defined('ONECLICK_SEO_MASTER_VERSION')) {
    return;
}

define('ONECLICK_SEO_MASTER_VERSION', '1.0.0');
define('ONECLICK_SEO_MASTER_FILE', __FILE__);
define('ONECLICK_SEO_MASTER_DIR', plugin_dir_path(__FILE__));
define('ONECLICK_SEO_MASTER_URL', plugin_dir_url(__FILE__));

/**
 * Main Master Plugin Class
 */
final class OneClick_SEO_Pro_Master {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    private function includes() {
        $files = [
            ONECLICK_SEO_MASTER_DIR . 'includes/class-database.php',
            ONECLICK_SEO_MASTER_DIR . 'includes/class-license-manager.php',
            ONECLICK_SEO_MASTER_DIR . 'includes/class-remote-site-manager.php',
            ONECLICK_SEO_MASTER_DIR . 'includes/class-rank-tracker.php',
            ONECLICK_SEO_MASTER_DIR . 'includes/class-universal-snippet.php',
            ONECLICK_SEO_MASTER_DIR . 'includes/api/class-rest-api.php',
        ];

        if (is_admin()) {
            $files[] = ONECLICK_SEO_MASTER_DIR . 'admin/class-admin.php';
        }

        foreach ($files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }

    private function init_hooks() {
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        add_action('init', [$this, 'init']);
        add_action('plugins_loaded', [$this, 'on_plugins_loaded']);
    }

    public function on_plugins_loaded() {
        load_plugin_textdomain('oneclick-seo-master', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function init() {
        $components = [
            'OneClick_SEO_Master_Database',
            'OneClick_SEO_License_Manager',
            'OneClick_SEO_Remote_Site_Manager',
            'OneClick_SEO_Rank_Tracker',
            'OneClick_SEO_Universal_Snippet',
            'OneClick_SEO_Master_REST_API',
        ];

        foreach ($components as $class) {
            if (class_exists($class)) {
                $class::get_instance();
            }
        }

        if (is_admin() && class_exists('OneClick_SEO_Master_Admin')) {
            OneClick_SEO_Master_Admin::get_instance();
        }
    }

    public function activate() {
        if (class_exists('OneClick_SEO_Master_Database')) {
            try {
                OneClick_SEO_Master_Database::get_instance()->create_tables();
            } catch (Exception $e) {
                error_log('1-Click SEO Pro Master Activation Error: ' . $e->getMessage());
            }
        }

        if (!wp_next_scheduled('oneclick_seo_rank_check')) {
            wp_schedule_event(time(), 'hourly', 'oneclick_seo_rank_check');
        }

        flush_rewrite_rules();
    }

    public function deactivate() {
        wp_clear_scheduled_hook('oneclick_seo_rank_check');
        flush_rewrite_rules();
    }
}

function oneclick_seo_master() {
    try {
        return OneClick_SEO_Pro_Master::get_instance();
    } catch (Exception $e) {
        error_log('1-Click SEO Pro Master Init Error: ' . $e->getMessage());
        return null;
    } catch (Error $e) {
        error_log('1-Click SEO Pro Master Fatal Error: ' . $e->getMessage());
        return null;
    }
}

// Initialize plugin
oneclick_seo_master();

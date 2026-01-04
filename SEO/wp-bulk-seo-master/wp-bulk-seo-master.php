<?php
/**
 * Plugin Name: WP Bulk SEO Master
 * Plugin URI: https://3j-labs.com/
 * Description: Master dashboard for managing WP Bulk SEO licenses, remote sites, and cross-platform SEO services.
 * Version: 1.0.0
 * Author: 3J Labs (제이x제니x제이슨 연구소)
 * Author URI: https://3j-labs.com/
 * License: GPL v2 or later
 * Text Domain: wp-bulk-seo-master
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package WP_Bulk_SEO_Master
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WP_BULK_SEO_MASTER_VERSION', '1.0.0');
define('WP_BULK_SEO_MASTER_FILE', __FILE__);
define('WP_BULK_SEO_MASTER_DIR', plugin_dir_path(__FILE__));
define('WP_BULK_SEO_MASTER_URL', plugin_dir_url(__FILE__));

/**
 * Main Master Plugin Class
 */
final class WP_Bulk_SEO_Master {

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
        // 파일 존재 확인 후 로드 (오류 방지)
        $files = [
            WP_BULK_SEO_MASTER_DIR . 'includes/class-database.php',
            WP_BULK_SEO_MASTER_DIR . 'includes/class-license-manager.php',
            WP_BULK_SEO_MASTER_DIR . 'includes/class-remote-site-manager.php',
            WP_BULK_SEO_MASTER_DIR . 'includes/class-rank-tracker.php',
            WP_BULK_SEO_MASTER_DIR . 'includes/class-universal-snippet.php',
            WP_BULK_SEO_MASTER_DIR . 'includes/api/class-rest-api.php',
        ];
        
        if (is_admin()) {
            $files[] = WP_BULK_SEO_MASTER_DIR . 'admin/class-admin.php';
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
        load_plugin_textdomain('wp-bulk-seo-master', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function init() {
        WP_Bulk_SEO_Master_Database::get_instance();
        WP_Bulk_SEO_License_Manager::get_instance();
        WP_Bulk_SEO_Remote_Site_Manager::get_instance();
        WP_Bulk_SEO_Rank_Tracker::get_instance();
        WP_Bulk_SEO_Universal_Snippet::get_instance();
        WP_Bulk_SEO_Master_REST_API::get_instance();
        
        if (is_admin()) {
            WP_Bulk_SEO_Master_Admin::get_instance();
        }
    }

    public function activate() {
        // 클래스 존재 확인 후 테이블 생성
        if (class_exists('WP_Bulk_SEO_Master_Database')) {
            try {
                WP_Bulk_SEO_Master_Database::get_instance()->create_tables();
            } catch (Exception $e) {
                if (function_exists('error_log')) {
                    error_log('WP Bulk SEO Master Activation Error: ' . $e->getMessage());
                }
            }
        }
        
        // Schedule cron jobs for rank tracking
        if (function_exists('wp_next_scheduled') && function_exists('wp_schedule_event')) {
            if (!wp_next_scheduled('wp_bulk_seo_rank_check')) {
                wp_schedule_event(time(), 'hourly', 'wp_bulk_seo_rank_check');
            }
        }
        
        if (function_exists('flush_rewrite_rules')) {
            flush_rewrite_rules();
        }
    }

    public function deactivate() {
        try {
            if (function_exists('wp_clear_scheduled_hook')) {
                wp_clear_scheduled_hook('wp_bulk_seo_rank_check');
            }
            if (function_exists('flush_rewrite_rules')) {
                flush_rewrite_rules();
            }
        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO Master Deactivation Error: ' . $e->getMessage());
            }
        }
    }
}

function wp_bulk_seo_master() {
    try {
        return WP_Bulk_SEO_Master::get_instance();
    } catch (Exception $e) {
        if (function_exists('error_log')) {
            error_log('WP Bulk SEO Master Init Error: ' . $e->getMessage());
        }
        // 오류가 발생해도 플러그인은 활성화되도록 함
        return null;
    } catch (Error $e) {
        if (function_exists('error_log')) {
            error_log('WP Bulk SEO Master Init Fatal Error: ' . $e->getMessage());
        }
        // 치명적 오류가 발생해도 플러그인은 활성화되도록 함
        return null;
    }
}

// Initialize plugin (오류 처리 포함)
try {
    wp_bulk_seo_master();
} catch (Exception $e) {
    if (function_exists('error_log')) {
        error_log('WP Bulk SEO Master Global Init Error: ' . $e->getMessage());
    }
} catch (Error $e) {
    if (function_exists('error_log')) {
        error_log('WP Bulk SEO Master Global Init Fatal Error: ' . $e->getMessage());
    }
}

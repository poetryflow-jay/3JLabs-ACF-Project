<?php
/**
 * WP Bulk SEO Master - Admin
 *
 * @package OneClick_SEO_Master
 */

if (!defined('ABSPATH')) {
    exit;
}

class OneClick_SEO_Master_Admin {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function add_menu() {
        add_menu_page(
            __('SEO Master', 'oneclick-seo-master'),
            __('SEO Master', 'oneclick-seo-master'),
            'manage_options',
            'seo-master',
            [$this, 'render_dashboard'],
            'dashicons-chart-area',
            30
        );

        add_submenu_page('seo-master', __('Dashboard', 'oneclick-seo-master'), __('Dashboard', 'oneclick-seo-master'), 'manage_options', 'seo-master', [$this, 'render_dashboard']);
        add_submenu_page('seo-master', __('Licenses', 'oneclick-seo-master'), __('Licenses', 'oneclick-seo-master'), 'manage_options', 'seo-master-licenses', [$this, 'render_licenses']);
        add_submenu_page('seo-master', __('Remote Sites', 'oneclick-seo-master'), __('Remote Sites', 'oneclick-seo-master'), 'manage_options', 'seo-master-sites', [$this, 'render_sites']);
        add_submenu_page('seo-master', __('Rank Tracker', 'oneclick-seo-master'), __('Rank Tracker', 'oneclick-seo-master'), 'manage_options', 'seo-master-ranks', [$this, 'render_ranks']);
        add_submenu_page('seo-master', __('Settings', 'oneclick-seo-master'), __('Settings', 'oneclick-seo-master'), 'manage_options', 'seo-master-settings', [$this, 'render_settings']);
    }

    public function enqueue_assets($hook) {
        if (strpos($hook, 'seo-master') === false) return;

        wp_enqueue_style('seo-master-admin', ONECLICK_SEO_MASTER_URL . 'assets/css/admin.css', [], ONECLICK_SEO_MASTER_VERSION);
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], '4.4.0', true);
        wp_enqueue_script('seo-master-admin', ONECLICK_SEO_MASTER_URL . 'assets/js/admin.js', ['jquery', 'chart-js'], ONECLICK_SEO_MASTER_VERSION, true);

        wp_localize_script('seo-master-admin', 'seoMaster', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('seo-master/v1/'),
            'nonce' => wp_create_nonce('oneclick_master_nonce'),
            'restNonce' => wp_create_nonce('wp_rest')
        ]);
    }

    public function render_dashboard() {
        include ONECLICK_SEO_MASTER_DIR . 'admin/views/dashboard.php';
    }

    public function render_licenses() {
        include ONECLICK_SEO_MASTER_DIR . 'admin/views/licenses.php';
    }

    public function render_sites() {
        include ONECLICK_SEO_MASTER_DIR . 'admin/views/sites.php';
    }

    public function render_ranks() {
        include ONECLICK_SEO_MASTER_DIR . 'admin/views/ranks.php';
    }

    public function render_settings() {
        include ONECLICK_SEO_MASTER_DIR . 'admin/views/settings.php';
    }
}

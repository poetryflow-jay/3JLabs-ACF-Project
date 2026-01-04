<?php
/**
 * Admin Interface Class
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Admin {

    /**
     * Plugin instance
     */
    private $plugin;

    /**
     * Constructor
     */
    public function __construct($plugin) {
        $this->plugin = $plugin;
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Admin menu is handled by main plugin class
        // Add additional admin hooks here
        add_action('admin_notices', [$this, 'admin_notices']);
    }

    /**
     * Display admin notices
     */
    public function admin_notices() {
        // Check if API keys are configured
        $pagespeed_key = get_option('wp_bulk_seo_aeo_pagespeed_api_key', '');
        $gsc_token = get_option('wp_bulk_seo_aeo_gsc_token', '');
        $ga_token = get_option('wp_bulk_seo_aeo_ga_token', '');

        if (empty($pagespeed_key) && empty($gsc_token) && empty($ga_token)) {
            $screen = get_current_screen();
            if ($screen && strpos($screen->id, 'wp-bulk-seo-aeo') !== false) {
                ?>
                <div class="notice notice-info is-dismissible">
                    <p>
                        <strong><?php esc_html_e('WP Bulk SEO & AEO', 'wp-bulk-seo-aeo'); ?>:</strong>
                        <?php esc_html_e('API 키를 설정하여 Google Search Console, PageSpeed Insights, Google Analytics 데이터를 활용하세요.', 'wp-bulk-seo-aeo'); ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=wp-bulk-seo-aeo-settings')); ?>">
                            <?php esc_html_e('설정 페이지로 이동', 'wp-bulk-seo-aeo'); ?>
                        </a>
                    </p>
                </div>
                <?php
            }
        }
    }
}

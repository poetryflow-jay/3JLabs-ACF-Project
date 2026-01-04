<?php
/**
 * License Manager Class
 *
 * Manages licenses for WordPress and non-WordPress sites
 * Handles license validation, updates, and remote service access
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_License_Manager {

    /**
     * License API endpoint
     */
    private $license_api_url = 'https://3j-labs.com/wp-json/3j-license/v1/';

    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('admin_menu', [$this, 'add_license_menu'], 100);
        add_action('wp_ajax_wp_bulk_seo_aeo_activate_license', [$this, 'ajax_activate_license']);
        add_action('wp_ajax_wp_bulk_seo_aeo_deactivate_license', [$this, 'ajax_deactivate_license']);
        add_action('wp_ajax_wp_bulk_seo_aeo_check_license', [$this, 'ajax_check_license']);
    }

    /**
     * Add license menu
     */
    public function add_license_menu() {
        add_submenu_page(
            'wp-bulk-seo-aeo',
            __('License & Updates', 'wp-bulk-seo-aeo'),
            __('License & Updates', 'wp-bulk-seo-aeo'),
            'manage_options',
            'wp-bulk-seo-aeo-license',
            [$this, 'render_license_page']
        );
    }

    /**
     * Render license page
     */
    public function render_license_page() {
        $license_key = get_option('wp_bulk_seo_aeo_license_key', '');
        $license_status = get_option('wp_bulk_seo_aeo_license_status', 'inactive');
        $license_data = get_option('wp_bulk_seo_aeo_license_data', []);

        include WP_BULK_SEO_AEO_PATH . 'includes/admin/views/license.php';
    }

    /**
     * Activate license
     *
     * @param string $license_key License key
     * @return array|WP_Error Result
     */
    public function activate_license($license_key) {
        $response = wp_remote_post($this->license_api_url . 'activate', [
            'body' => [
                'license_key' => $license_key,
                'site_url' => home_url(),
                'site_name' => get_bloginfo('name'),
            ],
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['success']) && $body['success']) {
            update_option('wp_bulk_seo_aeo_license_key', $license_key);
            update_option('wp_bulk_seo_aeo_license_status', 'active');
            update_option('wp_bulk_seo_aeo_license_data', $body['data'] ?? []);
            update_option('wp_bulk_seo_aeo_license_expires', $body['data']['expires'] ?? '');

            return [
                'success' => true,
                'message' => 'License activated successfully',
                'data' => $body['data'],
            ];
        }

        return new WP_Error('activation_failed', $body['message'] ?? 'License activation failed');
    }

    /**
     * Deactivate license
     *
     * @return array|WP_Error Result
     */
    public function deactivate_license() {
        $license_key = get_option('wp_bulk_seo_aeo_license_key', '');

        if (empty($license_key)) {
            return new WP_Error('no_license', 'No license key found');
        }

        $response = wp_remote_post($this->license_api_url . 'deactivate', [
            'body' => [
                'license_key' => $license_key,
                'site_url' => home_url(),
            ],
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        // Clear license data
        delete_option('wp_bulk_seo_aeo_license_key');
        update_option('wp_bulk_seo_aeo_license_status', 'inactive');
        delete_option('wp_bulk_seo_aeo_license_data');

        return [
            'success' => true,
            'message' => 'License deactivated',
        ];
    }

    /**
     * Check license status
     *
     * @return array|WP_Error License status
     */
    public function check_license() {
        $license_key = get_option('wp_bulk_seo_aeo_license_key', '');

        if (empty($license_key)) {
            return [
                'status' => 'inactive',
                'message' => 'No license key',
            ];
        }

        $response = wp_remote_get($this->license_api_url . 'check', [
            'body' => [
                'license_key' => $license_key,
                'site_url' => home_url(),
            ],
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['success']) && $body['success']) {
            $status = $body['data']['status'] ?? 'inactive';
            update_option('wp_bulk_seo_aeo_license_status', $status);
            update_option('wp_bulk_seo_aeo_license_data', $body['data'] ?? []);

            return [
                'status' => $status,
                'data' => $body['data'],
            ];
        }

        return [
            'status' => 'invalid',
            'message' => $body['message'] ?? 'License check failed',
        ];
    }

    /**
     * AJAX: Activate license
     */
    public function ajax_activate_license() {
        check_ajax_referer('wp_rest', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
        }

        $license_key = sanitize_text_field($_POST['license_key'] ?? '');

        if (empty($license_key)) {
            wp_send_json_error(['message' => 'License key required']);
        }

        $result = $this->activate_license($license_key);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success($result);
    }

    /**
     * AJAX: Deactivate license
     */
    public function ajax_deactivate_license() {
        check_ajax_referer('wp_rest', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
        }

        $result = $this->deactivate_license();

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success($result);
    }

    /**
     * AJAX: Check license
     */
    public function ajax_check_license() {
        check_ajax_referer('wp_rest', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
        }

        $result = $this->check_license();

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success($result);
    }

    /**
     * Verify license for remote service
     *
     * @param string $license_key License key
     * @return bool Valid
     */
    public function verify_license($license_key) {
        $stored_key = get_option('wp_bulk_seo_aeo_license_key', '');
        
        if (empty($stored_key) || $stored_key !== $license_key) {
            return false;
        }

        $status = get_option('wp_bulk_seo_aeo_license_status', 'inactive');
        return $status === 'active';
    }

    /**
     * Get license info
     *
     * @return array License info
     */
    public function get_license_info() {
        return [
            'key' => get_option('wp_bulk_seo_aeo_license_key', ''),
            'status' => get_option('wp_bulk_seo_aeo_license_status', 'inactive'),
            'data' => get_option('wp_bulk_seo_aeo_license_data', []),
            'expires' => get_option('wp_bulk_seo_aeo_license_expires', ''),
        ];
    }
}

<?php
/**
 * WP Bulk SEO Master - License Manager
 *
 * @package WP_Bulk_SEO_Master
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_License_Manager {

    private static $instance = null;
    
    const LICENSE_TYPES = [
        'starter' => ['max_sites' => 1, 'features' => ['basic']],
        'professional' => ['max_sites' => 5, 'features' => ['basic', 'advanced', 'api']],
        'agency' => ['max_sites' => 25, 'features' => ['basic', 'advanced', 'api', 'whitelabel']],
        'enterprise' => ['max_sites' => -1, 'features' => ['all']]
    ];

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_generate_license', [$this, 'ajax_generate_license']);
        add_action('wp_ajax_revoke_license', [$this, 'ajax_revoke_license']);
    }

    /**
     * Generate a new license key
     */
    public function generate_license($data) {
        global $wpdb;

        $license_key = $this->generate_license_key();
        $type = $data['type'] ?? 'starter';
        $type_config = self::LICENSE_TYPES[$type] ?? self::LICENSE_TYPES['starter'];

        $result = $wpdb->insert(
            $wpdb->prefix . 'seo_master_licenses',
            [
                'license_key' => $license_key,
                'license_type' => $type,
                'customer_email' => sanitize_email($data['email']),
                'customer_name' => sanitize_text_field($data['name'] ?? ''),
                'max_sites' => $data['max_sites'] ?? $type_config['max_sites'],
                'status' => 'active',
                'expires_at' => $data['expires_at'] ?? null,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ],
            ['%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s']
        );

        if ($result === false) {
            return new WP_Error('db_error', 'Failed to create license');
        }

        return [
            'license_key' => $license_key,
            'license_id' => $wpdb->insert_id,
            'type' => $type,
            'max_sites' => $data['max_sites'] ?? $type_config['max_sites']
        ];
    }

    /**
     * Validate a license key
     */
    public function validate_license($license_key, $site_url = null) {
        global $wpdb;

        $license = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}seo_master_licenses WHERE license_key = %s",
            $license_key
        ));

        if (!$license) {
            return ['valid' => false, 'error' => 'invalid_key', 'message' => 'License key not found'];
        }

        if ($license->status !== 'active') {
            return ['valid' => false, 'error' => 'inactive', 'message' => 'License is not active'];
        }

        if ($license->expires_at && strtotime($license->expires_at) < time()) {
            return ['valid' => false, 'error' => 'expired', 'message' => 'License has expired'];
        }

        // Check site limit
        if ($license->max_sites > 0 && $license->active_sites >= $license->max_sites) {
            // Check if this site is already activated
            if ($site_url) {
                $existing = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}seo_master_activations 
                     WHERE license_id = %d AND site_url = %s AND status = 'active'",
                    $license->id, $site_url
                ));
                
                if (!$existing) {
                    return ['valid' => false, 'error' => 'limit_reached', 'message' => 'Site limit reached'];
                }
            }
        }

        return [
            'valid' => true,
            'license' => [
                'type' => $license->license_type,
                'max_sites' => $license->max_sites,
                'active_sites' => $license->active_sites,
                'expires_at' => $license->expires_at,
                'features' => self::LICENSE_TYPES[$license->license_type]['features'] ?? []
            ]
        ];
    }

    /**
     * Activate license for a site
     */
    public function activate_site($license_key, $site_url, $site_data = []) {
        global $wpdb;

        $validation = $this->validate_license($license_key, $site_url);
        if (!$validation['valid']) {
            return new WP_Error($validation['error'], $validation['message']);
        }

        $license = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}seo_master_licenses WHERE license_key = %s",
            $license_key
        ));

        // Check if already activated
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}seo_master_activations 
             WHERE license_id = %d AND site_url = %s",
            $license->id, $site_url
        ));

        if ($existing && $existing->status === 'active') {
            return [
                'success' => true,
                'activation_token' => $existing->activation_token,
                'message' => 'Site already activated'
            ];
        }

        $activation_token = $this->generate_activation_token();

        if ($existing) {
            // Reactivate
            $wpdb->update(
                $wpdb->prefix . 'seo_master_activations',
                [
                    'activation_token' => $activation_token,
                    'status' => 'active',
                    'activated_at' => current_time('mysql'),
                    'deactivated_at' => null
                ],
                ['id' => $existing->id]
            );
        } else {
            // New activation
            $wpdb->insert(
                $wpdb->prefix . 'seo_master_activations',
                [
                    'license_id' => $license->id,
                    'site_url' => $site_url,
                    'site_name' => $site_data['name'] ?? '',
                    'site_type' => $site_data['type'] ?? 'wordpress',
                    'activation_token' => $activation_token,
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                    'status' => 'active',
                    'activated_at' => current_time('mysql')
                ]
            );

            // Increment active sites count
            $wpdb->query($wpdb->prepare(
                "UPDATE {$wpdb->prefix}seo_master_licenses SET active_sites = active_sites + 1 WHERE id = %d",
                $license->id
            ));
        }

        return [
            'success' => true,
            'activation_token' => $activation_token,
            'license_type' => $license->license_type,
            'features' => self::LICENSE_TYPES[$license->license_type]['features'] ?? []
        ];
    }

    /**
     * Deactivate license for a site
     */
    public function deactivate_site($activation_token) {
        global $wpdb;

        $activation = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}seo_master_activations WHERE activation_token = %s AND status = 'active'",
            $activation_token
        ));

        if (!$activation) {
            return new WP_Error('not_found', 'Activation not found');
        }

        $wpdb->update(
            $wpdb->prefix . 'seo_master_activations',
            [
                'status' => 'deactivated',
                'deactivated_at' => current_time('mysql')
            ],
            ['id' => $activation->id]
        );

        // Decrement active sites count
        $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->prefix}seo_master_licenses SET active_sites = GREATEST(0, active_sites - 1) WHERE id = %d",
            $activation->license_id
        ));

        return ['success' => true];
    }

    /**
     * Get all licenses
     */
    public function get_licenses($args = []) {
        global $wpdb;

        $defaults = [
            'status' => '',
            'search' => '',
            'per_page' => 20,
            'page' => 1
        ];
        $args = wp_parse_args($args, $defaults);

        $where = ['1=1'];
        $params = [];

        if ($args['status']) {
            $where[] = 'status = %s';
            $params[] = $args['status'];
        }

        if ($args['search']) {
            $where[] = '(license_key LIKE %s OR customer_email LIKE %s OR customer_name LIKE %s)';
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $where_sql = implode(' AND ', $where);
        $offset = ($args['page'] - 1) * $args['per_page'];

        $count_sql = "SELECT COUNT(*) FROM {$wpdb->prefix}seo_master_licenses WHERE $where_sql";
        $total = $wpdb->get_var($params ? $wpdb->prepare($count_sql, $params) : $count_sql);

        $params[] = $args['per_page'];
        $params[] = $offset;

        $sql = "SELECT * FROM {$wpdb->prefix}seo_master_licenses WHERE $where_sql ORDER BY created_at DESC LIMIT %d OFFSET %d";
        $licenses = $wpdb->get_results($wpdb->prepare($sql, $params));

        return [
            'licenses' => $licenses,
            'total' => (int) $total,
            'pages' => ceil($total / $args['per_page'])
        ];
    }

    /**
     * Get license activations
     */
    public function get_activations($license_id) {
        global $wpdb;

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}seo_master_activations WHERE license_id = %d ORDER BY activated_at DESC",
            $license_id
        ));
    }

    /**
     * Generate unique license key
     */
    private function generate_license_key() {
        $segments = [];
        for ($i = 0; $i < 4; $i++) {
            $segments[] = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        }
        return implode('-', $segments);
    }

    /**
     * Generate activation token
     */
    private function generate_activation_token() {
        return bin2hex(random_bytes(32));
    }

    /**
     * AJAX: Generate license
     */
    public function ajax_generate_license() {
        check_ajax_referer('seo_master_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        $result = $this->generate_license([
            'email' => $_POST['email'] ?? '',
            'name' => $_POST['name'] ?? '',
            'type' => $_POST['type'] ?? 'starter',
            'max_sites' => $_POST['max_sites'] ?? null,
            'expires_at' => $_POST['expires_at'] ?? null
        ]);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success($result);
    }

    /**
     * AJAX: Revoke license
     */
    public function ajax_revoke_license() {
        check_ajax_referer('seo_master_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        global $wpdb;

        $license_id = intval($_POST['license_id'] ?? 0);

        $wpdb->update(
            $wpdb->prefix . 'seo_master_licenses',
            ['status' => 'revoked', 'updated_at' => current_time('mysql')],
            ['id' => $license_id]
        );

        // Deactivate all sites
        $wpdb->update(
            $wpdb->prefix . 'seo_master_activations',
            ['status' => 'deactivated', 'deactivated_at' => current_time('mysql')],
            ['license_id' => $license_id, 'status' => 'active']
        );

        wp_send_json_success(['message' => 'License revoked']);
    }
}

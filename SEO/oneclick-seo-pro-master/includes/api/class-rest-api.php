<?php
/**
 * WP Bulk SEO Master - REST API
 *
 * @package OneClick_SEO_Master
 */

if (!defined('ABSPATH')) {
    exit;
}

class OneClick_SEO_Master_REST_API {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        $namespace = 'seo-master/v1';

        // License endpoints
        register_rest_route($namespace, '/license/validate', [
            'methods' => 'POST',
            'callback' => [$this, 'validate_license'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route($namespace, '/license/activate', [
            'methods' => 'POST',
            'callback' => [$this, 'activate_license'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route($namespace, '/license/deactivate', [
            'methods' => 'POST',
            'callback' => [$this, 'deactivate_license'],
            'permission_callback' => '__return_true'
        ]);

        // Site management
        register_rest_route($namespace, '/sites', [
            'methods' => 'GET',
            'callback' => [$this, 'get_sites'],
            'permission_callback' => [$this, 'check_admin_permission']
        ]);

        register_rest_route($namespace, '/sites/(?P<id>\d+)/audit', [
            'methods' => 'GET',
            'callback' => [$this, 'get_site_audit'],
            'permission_callback' => [$this, 'check_admin_permission']
        ]);

        // Rank tracking
        register_rest_route($namespace, '/rankings', [
            'methods' => 'GET',
            'callback' => [$this, 'get_rankings'],
            'permission_callback' => [$this, 'check_admin_permission']
        ]);

        register_rest_route($namespace, '/rankings/sync', [
            'methods' => 'POST',
            'callback' => [$this, 'sync_rankings'],
            'permission_callback' => '__return_true'
        ]);

        // Statistics
        register_rest_route($namespace, '/stats', [
            'methods' => 'GET',
            'callback' => [$this, 'get_stats'],
            'permission_callback' => [$this, 'check_admin_permission']
        ]);

        // Chrome extension connection
        register_rest_route($namespace, '/extension/connect', [
            'methods' => 'POST',
            'callback' => [$this, 'extension_connect'],
            'permission_callback' => '__return_true'
        ]);
    }

    public function check_admin_permission() {
        return current_user_can('manage_options');
    }

    public function validate_license($request) {
        $license_key = sanitize_text_field($request->get_param('license_key'));
        $site_url = esc_url_raw($request->get_param('site_url'));

        $manager = OneClick_SEO_License_Manager::get_instance();
        $result = $manager->validate_license($license_key, $site_url);

        return new WP_REST_Response($result);
    }

    public function activate_license($request) {
        $license_key = sanitize_text_field($request->get_param('license_key'));
        $site_url = esc_url_raw($request->get_param('site_url'));
        $site_data = $request->get_param('site_data') ?: [];

        $manager = OneClick_SEO_License_Manager::get_instance();
        $result = $manager->activate_site($license_key, $site_url, $site_data);

        if (is_wp_error($result)) {
            return new WP_REST_Response([
                'success' => false,
                'error' => $result->get_error_code(),
                'message' => $result->get_error_message()
            ], 400);
        }

        return new WP_REST_Response($result);
    }

    public function deactivate_license($request) {
        $activation_token = sanitize_text_field($request->get_param('activation_token'));

        $manager = OneClick_SEO_License_Manager::get_instance();
        $result = $manager->deactivate_site($activation_token);

        if (is_wp_error($result)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => $result->get_error_message()
            ], 400);
        }

        return new WP_REST_Response($result);
    }

    public function get_sites($request) {
        $manager = OneClick_SEO_Remote_Site_Manager::get_instance();
        $sites = $manager->get_sites([
            'status' => $request->get_param('status'),
            'type' => $request->get_param('type'),
            'per_page' => $request->get_param('per_page') ?: 20,
            'page' => $request->get_param('page') ?: 1
        ]);

        return new WP_REST_Response(['sites' => $sites]);
    }

    public function get_site_audit($request) {
        global $wpdb;

        $site_id = intval($request->get_param('id'));

        $audit = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}oneclick_master_audits 
             WHERE site_id = %d ORDER BY created_at DESC LIMIT 1",
            $site_id
        ));

        if (!$audit) {
            return new WP_REST_Response(['error' => 'No audit found'], 404);
        }

        $audit->audit_data = json_decode($audit->audit_data, true);

        return new WP_REST_Response(['audit' => $audit]);
    }

    public function get_rankings($request) {
        $tracker = OneClick_SEO_Rank_Tracker::get_instance();
        
        $keywords = $tracker->get_keywords([
            'site_id' => $request->get_param('site_id'),
            'per_page' => $request->get_param('per_page') ?: 50,
            'page' => $request->get_param('page') ?: 1
        ]);

        $stats = $tracker->get_statistics($request->get_param('site_id'));

        return new WP_REST_Response([
            'keywords' => $keywords,
            'stats' => $stats
        ]);
    }

    public function sync_rankings($request) {
        $data = $request->get_json_params();
        $activation_token = $data['activation_token'] ?? '';

        if (empty($activation_token)) {
            return new WP_REST_Response(['error' => 'Missing token'], 401);
        }

        global $wpdb;

        // Verify activation
        $activation = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}oneclick_master_activations 
             WHERE activation_token = %s AND status = 'active'",
            $activation_token
        ));

        if (!$activation) {
            return new WP_REST_Response(['error' => 'Invalid token'], 401);
        }

        // Save ranking data
        if (!empty($data['rankings'])) {
            foreach ($data['rankings'] as $ranking) {
                // Find or create keyword
                $keyword_id = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM {$wpdb->prefix}oneclick_master_keywords 
                     WHERE keyword = %s AND target_url = %s",
                    $ranking['keyword'], $ranking['url']
                ));

                if (!$keyword_id) {
                    $wpdb->insert(
                        $wpdb->prefix . 'oneclick_master_keywords',
                        [
                            'keyword' => $ranking['keyword'],
                            'target_url' => $ranking['url'],
                            'status' => 'active',
                            'created_at' => current_time('mysql')
                        ]
                    );
                    $keyword_id = $wpdb->insert_id;
                }

                // Save rank history
                $wpdb->insert(
                    $wpdb->prefix . 'oneclick_master_rank_history',
                    [
                        'keyword_id' => $keyword_id,
                        'position' => $ranking['position'],
                        'url' => $ranking['url'],
                        'checked_at' => $ranking['checked_at'] ?? current_time('mysql')
                    ]
                );
            }
        }

        return new WP_REST_Response(['success' => true]);
    }

    public function get_stats($request) {
        global $wpdb;

        // License stats
        $total_licenses = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}oneclick_master_licenses"
        );
        $active_licenses = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}oneclick_master_licenses WHERE status = 'active'"
        );

        // Site stats
        $total_sites = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}oneclick_master_remote_sites"
        );
        $active_activations = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}oneclick_master_activations WHERE status = 'active'"
        );

        // Keyword stats
        $tracker = OneClick_SEO_Rank_Tracker::get_instance();
        $rank_stats = $tracker->get_statistics();

        return new WP_REST_Response([
            'licenses' => [
                'total' => (int) $total_licenses,
                'active' => (int) $active_licenses
            ],
            'sites' => [
                'managed' => (int) $total_sites,
                'activated' => (int) $active_activations
            ],
            'rankings' => $rank_stats
        ]);
    }

    public function extension_connect($request) {
        $data = $request->get_json_params();
        $email = sanitize_email($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            return new WP_REST_Response(['error' => 'Missing credentials'], 400);
        }

        // Authenticate user
        $user = wp_authenticate($email, $password);

        if (is_wp_error($user)) {
            return new WP_REST_Response(['error' => 'Invalid credentials'], 401);
        }

        // Check user capability
        if (!user_can($user, 'manage_options')) {
            return new WP_REST_Response(['error' => 'Insufficient permissions'], 403);
        }

        // Generate connection token
        $token = bin2hex(random_bytes(32));
        update_user_meta($user->ID, 'seo_extension_token', $token);
        update_user_meta($user->ID, 'seo_extension_token_expires', time() + (30 * DAY_IN_SECONDS));

        return new WP_REST_Response([
            'success' => true,
            'token' => $token,
            'site_name' => get_bloginfo('name'),
            'site_url' => home_url(),
            'nonce' => wp_create_nonce('wp_rest')
        ]);
    }
}

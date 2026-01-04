<?php
/**
 * Chrome Extension Sync Class
 *
 * Synchronizes data from Chrome extension to WordPress
 * Collects ranking, competition, search volume data from extension
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Chrome_Extension_Sync {

    /**
     * API endpoint for extension data
     */
    private const API_NAMESPACE = 'wp-bulk-seo-aeo/v1';
    private const API_ROUTE = '/chrome-extension/sync';

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
        add_action('rest_api_init', [$this, 'register_api_routes']);
        add_action('wp_ajax_wp_bulk_seo_aeo_extension_auth', [$this, 'ajax_extension_auth']);
    }

    /**
     * Register REST API routes for Chrome extension
     */
    public function register_api_routes() {
        register_rest_route(self::API_NAMESPACE, '/chrome-extension/sync', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_extension_sync'],
            'permission_callback' => [$this, 'check_extension_permission'],
        ]);

        register_rest_route(self::API_NAMESPACE, '/chrome-extension/rankings', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_rankings_data'],
            'permission_callback' => [$this, 'check_extension_permission'],
        ]);

        register_rest_route(self::API_NAMESPACE, '/chrome-extension/competition', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_competition_data'],
            'permission_callback' => [$this, 'check_extension_permission'],
        ]);

        register_rest_route(self::API_NAMESPACE, '/chrome-extension/search-volume', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_search_volume_data'],
            'permission_callback' => [$this, 'check_extension_permission'],
        ]);
    }

    /**
     * Check extension permission
     *
     * @param WP_REST_Request $request Request object
     * @return bool|WP_Error
     */
    public function check_extension_permission($request) {
        // Check for extension token
        $token = $request->get_header('X-Extension-Token');
        $user_id = $request->get_header('X-User-ID');

        if (empty($token) || empty($user_id)) {
            return new WP_Error('missing_auth', 'Extension token or user ID missing', ['status' => 401]);
        }

        // Verify token matches user's stored extension token
        $stored_token = get_user_meta($user_id, 'wp_bulk_seo_aeo_extension_token', true);
        if ($token !== $stored_token) {
            return new WP_Error('invalid_token', 'Invalid extension token', ['status' => 401]);
        }

        // Verify user exists and can manage options
        $user = get_userdata($user_id);
        if (!$user || !user_can($user_id, 'manage_options')) {
            return new WP_Error('insufficient_permissions', 'User does not have permission', ['status' => 403]);
        }

        return true;
    }

    /**
     * Handle extension sync data
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function handle_extension_sync($request) {
        $data = $request->get_json_params();
        $user_id = $request->get_header('X-User-ID');

        if (empty($data)) {
            return new WP_Error('no_data', 'No data provided', ['status' => 400]);
        }

        // Store extension data
        $sync_data = [
            'timestamp' => current_time('mysql'),
            'data' => $data,
            'user_id' => $user_id,
        ];

        // Save to database
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_extension_data';
        
        $wpdb->insert($table, [
            'user_id' => $user_id,
            'data_type' => 'sync',
            'data' => wp_json_encode($sync_data),
            'created_at' => current_time('mysql'),
        ]);

        return rest_ensure_response([
            'success' => true,
            'message' => 'Data synced successfully',
        ]);
    }

    /**
     * Handle rankings data from extension
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function handle_rankings_data($request) {
        $data = $request->get_json_params();
        $user_id = $request->get_header('X-User-ID');

        if (empty($data['rankings'])) {
            return new WP_Error('no_rankings', 'No rankings data provided', ['status' => 400]);
        }

        // Process rankings data
        $rankings = $data['rankings'];
        $keyword = sanitize_text_field($data['keyword'] ?? '');
        $url = esc_url_raw($data['url'] ?? '');

        // Save rankings
        $this->save_rankings($user_id, $keyword, $url, $rankings);

        return rest_ensure_response([
            'success' => true,
            'message' => 'Rankings data saved',
            'rankings_count' => count($rankings),
        ]);
    }

    /**
     * Handle competition data
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function handle_competition_data($request) {
        $data = $request->get_json_params();
        $user_id = $request->get_header('X-User-ID');

        if (empty($data['competition'])) {
            return new WP_Error('no_competition', 'No competition data provided', ['status' => 400]);
        }

        // Save competition data
        $this->save_competition_data($user_id, $data['competition']);

        return rest_ensure_response([
            'success' => true,
            'message' => 'Competition data saved',
        ]);
    }

    /**
     * Handle search volume data
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function handle_search_volume_data($request) {
        $data = $request->get_json_params();
        $user_id = $request->get_header('X-User-ID');

        if (empty($data['search_volume'])) {
            return new WP_Error('no_volume', 'No search volume data provided', ['status' => 400]);
        }

        // Save search volume
        $this->save_search_volume($user_id, $data['search_volume']);

        return rest_ensure_response([
            'success' => true,
            'message' => 'Search volume data saved',
        ]);
    }

    /**
     * Save rankings data
     *
     * @param int $user_id User ID
     * @param string $keyword Keyword
     * @param string $url URL
     * @param array $rankings Rankings data
     */
    private function save_rankings($user_id, $keyword, $url, $rankings) {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_rankings';

        foreach ($rankings as $ranking) {
            $wpdb->replace($table, [
                'user_id' => $user_id,
                'keyword' => $keyword,
                'url' => $url,
                'position' => intval($ranking['position'] ?? 0),
                'page_title' => sanitize_text_field($ranking['title'] ?? ''),
                'page_url' => esc_url_raw($ranking['url'] ?? ''),
                'domain' => sanitize_text_field($ranking['domain'] ?? ''),
                'tracked_at' => current_time('mysql'),
            ], [
                '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s',
            ]);
        }
    }

    /**
     * Save competition data
     *
     * @param int $user_id User ID
     * @param array $competition Competition data
     */
    private function save_competition_data($user_id, $competition) {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_competition';

        foreach ($competition as $comp) {
            $wpdb->replace($table, [
                'user_id' => $user_id,
                'keyword' => sanitize_text_field($comp['keyword'] ?? ''),
                'competition_level' => sanitize_text_field($comp['level'] ?? ''),
                'competition_score' => floatval($comp['score'] ?? 0),
                'top_competitors' => wp_json_encode($comp['competitors'] ?? []),
                'updated_at' => current_time('mysql'),
            ], [
                '%d', '%s', '%s', '%f', '%s', '%s',
            ]);
        }
    }

    /**
     * Save search volume data
     *
     * @param int $user_id User ID
     * @param array $volume_data Search volume data
     */
    private function save_search_volume($user_id, $volume_data) {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_search_volume';

        foreach ($volume_data as $volume) {
            $wpdb->replace($table, [
                'user_id' => $user_id,
                'keyword' => sanitize_text_field($volume['keyword'] ?? ''),
                'search_volume' => intval($volume['volume'] ?? 0),
                'cpc' => floatval($volume['cpc'] ?? 0),
                'difficulty' => intval($volume['difficulty'] ?? 0),
                'updated_at' => current_time('mysql'),
            ], [
                '%d', '%s', '%d', '%f', '%d', '%s',
            ]);
        }
    }

    /**
     * AJAX: Generate extension authentication token
     */
    public function ajax_extension_auth() {
        check_ajax_referer('wp_rest', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
        }

        $user_id = get_current_user_id();
        $token = wp_generate_password(32, false);

        // Save token for user
        update_user_meta($user_id, 'wp_bulk_seo_aeo_extension_token', $token);
        update_user_meta($user_id, 'wp_bulk_seo_aeo_extension_token_created', current_time('mysql'));

        wp_send_json_success([
            'token' => $token,
            'user_id' => $user_id,
            'api_url' => rest_url(self::API_NAMESPACE . self::API_ROUTE),
        ]);
    }

    /**
     * Get extension data for user
     *
     * @param int $user_id User ID
     * @return array Extension data
     */
    public function get_user_extension_data($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_extension_data';

        $data = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC LIMIT 100",
            $user_id
        ), ARRAY_A);

        return $data;
    }
}

<?php
/**
 * REST API Class
 *
 * Provides REST API endpoints for the plugin
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_REST_API {

    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route('wp-bulk-seo-aeo/v1', '/analyze/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => [$this, 'analyze_post'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
            ],
        ]);

        register_rest_route('wp-bulk-seo-aeo/v1', '/optimize/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => [$this, 'optimize_post'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
            ],
        ]);

        register_rest_route('wp-bulk-seo-aeo/v1', '/score/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_score'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
            ],
        ]);

        register_rest_route('wp-bulk-seo-aeo/v1', '/statistics', [
            'methods' => 'GET',
            'callback' => [$this, 'get_statistics'],
            'permission_callback' => [$this, 'check_permission'],
        ]);

        register_rest_route('wp-bulk-seo-aeo/v1', '/predictions', [
            'methods' => 'GET',
            'callback' => [$this, 'get_predictions'],
            'permission_callback' => [$this, 'check_permission'],
        ]);

        register_rest_route('wp-bulk-seo-aeo/v1', '/track', [
            'methods' => 'POST',
            'callback' => [$this, 'track_metric'],
            'permission_callback' => '__return_true', // Public for GTM script
        ]);

        register_rest_route('wp-bulk-seo-aeo/v1', '/sync', [
            'methods' => 'POST',
            'callback' => [$this, 'sync_page_data'],
            'permission_callback' => '__return_true', // Public for GTM script
        ]);
    }

    /**
     * Check user permission
     *
     * @param WP_REST_Request $request Request object
     * @return bool
     */
    public function check_permission($request) {
        return current_user_can('manage_options');
    }

    /**
     * Analyze a post
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function analyze_post($request) {
        $post_id = $request->get_param('id');
        $plugin = WP_Bulk_SEO_AEO::instance();

        if (!$plugin->bulk_optimizer) {
            return new WP_Error('not_initialized', 'Bulk optimizer not initialized', ['status' => 500]);
        }

        $result = $plugin->bulk_optimizer->analyze_post($post_id);

        if ($result['success']) {
            return rest_ensure_response([
                'success' => true,
                'score' => $result['score'],
                'grade' => $result['grade'],
                'recommendations' => $result['recommendations'],
            ]);
        }

        return new WP_Error('analysis_failed', $result['error'] ?? 'Analysis failed', ['status' => 500]);
    }

    /**
     * Optimize a post
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function optimize_post($request) {
        $post_id = $request->get_param('id');
        $options = $request->get_json_params() ?? [];
        $plugin = WP_Bulk_SEO_AEO::instance();

        if (!$plugin->bulk_optimizer) {
            return new WP_Error('not_initialized', 'Bulk optimizer not initialized', ['status' => 500]);
        }

        $result = $plugin->bulk_optimizer->optimize_post($post_id, $options);

        if ($result['success']) {
            return rest_ensure_response([
                'success' => true,
                'optimizations' => $result['optimizations'],
                'improvement' => $result['improvement'],
            ]);
        }

        return new WP_Error('optimization_failed', 'Optimization failed', ['status' => 500]);
    }

    /**
     * Get SEO score for a post
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function get_score($request) {
        $post_id = $request->get_param('id');
        global $wpdb;

        $table = $wpdb->prefix . 'bulk_seo_aeo_scores';
        $score = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE post_id = %d",
            $post_id
        ), ARRAY_A);

        if ($score) {
            return rest_ensure_response($score);
        }

        return new WP_Error('not_found', 'Score not found', ['status' => 404]);
    }

    /**
     * Get statistics
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response
     */
    public function get_statistics($request) {
        $plugin = WP_Bulk_SEO_AEO::instance();

        if (!$plugin->bulk_optimizer) {
            return rest_ensure_response([]);
        }

        $stats = $plugin->bulk_optimizer->get_statistics();
        return rest_ensure_response($stats);
    }

    /**
     * Get ranking predictions
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response
     */
    public function get_predictions($request) {
        global $wpdb;
        $keywords_table = $wpdb->prefix . 'bulk_seo_aeo_keywords';
        $keywords = $wpdb->get_results("SELECT * FROM $keywords_table WHERE is_active = 1", ARRAY_A);

        $predictor = new WP_Bulk_SEO_AEO_Ranking_Predictor();
        $predictions = [];

        foreach ($keywords as $keyword_data) {
            $prediction = $predictor->predict_keyword_ranking(
                $keyword_data['keyword'],
                $keyword_data['target_url'],
                ['meta_tags', 'title', 'content', 'schema']
            );
            $predictions[] = $prediction;
        }

        return rest_ensure_response([
            'success' => true,
            'predictions' => $predictions,
        ]);
    }

    /**
     * Track metric (from GTM script)
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response
     */
    public function track_metric($request) {
        $data = $request->get_json_params();
        $site_id = $data['site'] ?? '';
        $metric = $data['metric'] ?? '';
        $value = $data['value'] ?? 0;
        $url = $data['url'] ?? '';

        // Save metric data
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_metrics';

        $wpdb->insert($table, [
            'site_id' => $site_id,
            'url' => esc_url_raw($url),
            'metric' => sanitize_text_field($metric),
            'value' => floatval($value),
            'tracked_at' => current_time('mysql'),
        ]);

        return rest_ensure_response(['success' => true]);
    }

    /**
     * Sync page data (from GTM script)
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response
     */
    public function sync_page_data($request) {
        $site_token = $request->get_header('X-Site-Token');
        $data = $request->get_json_params();

        // Verify site token
        global $wpdb;
        $sites_table = $wpdb->prefix . 'bulk_seo_aeo_remote_sites';
        $site = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $sites_table WHERE site_token = %s",
            $site_token
        ), ARRAY_A);

        if (!$site) {
            return new WP_Error('invalid_token', 'Invalid site token', ['status' => 401]);
        }

        // Save sync data
        $sync_table = $wpdb->prefix . 'bulk_seo_aeo_remote_sync';
        $wpdb->replace($sync_table, [
            'site_id' => $site['id'],
            'url' => esc_url_raw($data['url'] ?? ''),
            'title' => sanitize_text_field($data['title'] ?? ''),
            'data' => wp_json_encode($data),
            'synced_at' => current_time('mysql'),
        ]);

        return rest_ensure_response(['success' => true]);
    }
}

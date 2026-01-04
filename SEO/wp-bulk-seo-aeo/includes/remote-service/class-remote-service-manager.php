<?php
/**
 * Remote Service Manager Class
 *
 * Manages remote SEO optimization service for non-WordPress sites
 * Provides one-line code installation and remote optimization
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Remote_Service_Manager {

    /**
     * API namespace
     */
    private const API_NAMESPACE = 'wp-bulk-seo-aeo/v1';

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
    }

    /**
     * Register REST API routes
     */
    public function register_api_routes() {
        // Remote site registration
        register_rest_route(self::API_NAMESPACE, '/remote/register', [
            'methods' => 'POST',
            'callback' => [$this, 'register_remote_site'],
            'permission_callback' => '__return_true', // Public endpoint for registration
        ]);

        // Remote site data sync
        register_rest_route(self::API_NAMESPACE, '/remote/sync', [
            'methods' => 'POST',
            'callback' => [$this, 'sync_remote_data'],
            'permission_callback' => [$this, 'check_remote_permission'],
        ]);

        // Get optimization recommendations
        register_rest_route(self::API_NAMESPACE, '/remote/recommendations', [
            'methods' => 'GET',
            'callback' => [$this, 'get_remote_recommendations'],
            'permission_callback' => [$this, 'check_remote_permission'],
        ]);

        // Apply optimizations remotely
        register_rest_route(self::API_NAMESPACE, '/remote/apply', [
            'methods' => 'POST',
            'callback' => [$this, 'apply_remote_optimizations'],
            'permission_callback' => [$this, 'check_remote_permission'],
        ]);
    }

    /**
     * Register remote site
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function register_remote_site($request) {
        $data = $request->get_json_params();
        
        $site_url = esc_url_raw($data['site_url'] ?? '');
        $site_name = sanitize_text_field($data['site_name'] ?? '');
        $license_key = sanitize_text_field($data['license_key'] ?? '');

        if (empty($site_url) || empty($license_key)) {
            return new WP_Error('missing_data', 'Site URL and license key required', ['status' => 400]);
        }

        // Verify license
        $license_valid = $this->verify_license($license_key);
        if (!$license_valid) {
            return new WP_Error('invalid_license', 'Invalid license key', ['status' => 403]);
        }

        // Generate site token
        $site_token = $this->generate_site_token($site_url);

        // Register site
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_remote_sites';

        $site_id = $wpdb->insert($table, [
            'site_url' => $site_url,
            'site_name' => $site_name,
            'license_key' => $license_key,
            'site_token' => $site_token,
            'platform' => sanitize_text_field($data['platform'] ?? 'unknown'),
            'status' => 'active',
            'registered_at' => current_time('mysql'),
        ], [
            '%s', '%s', '%s', '%s', '%s', '%s', '%s',
        ]);

        if ($site_id) {
            return rest_ensure_response([
                'success' => true,
                'site_id' => $wpdb->insert_id,
                'site_token' => $site_token,
                'api_endpoint' => rest_url(self::API_NAMESPACE . '/remote/'),
            ]);
        }

        return new WP_Error('registration_failed', 'Failed to register site', ['status' => 500]);
    }

    /**
     * Sync remote site data
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function sync_remote_data($request) {
        $site_token = $request->get_header('X-Site-Token');
        $data = $request->get_json_params();

        if (empty($site_token)) {
            return new WP_Error('missing_token', 'Site token required', ['status' => 401]);
        }

        // Verify site
        $site = $this->get_site_by_token($site_token);
        if (!$site) {
            return new WP_Error('invalid_token', 'Invalid site token', ['status' => 401]);
        }

        // Process sync data
        $sync_result = $this->process_sync_data($site['id'], $data);

        return rest_ensure_response([
            'success' => true,
            'synced' => $sync_result,
        ]);
    }

    /**
     * Get remote recommendations
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function get_remote_recommendations($request) {
        $site_token = $request->get_header('X-Site-Token');
        $url = $request->get_param('url');

        if (empty($site_token)) {
            return new WP_Error('missing_token', 'Site token required', ['status' => 401]);
        }

        $site = $this->get_site_by_token($site_token);
        if (!$site) {
            return new WP_Error('invalid_token', 'Invalid site token', ['status' => 401]);
        }

        // Analyze URL and generate recommendations
        $analyzer = new ThreeJ_SEO_Analyzer();
        $page_data = $analyzer->analyze_url($url);

        if (isset($page_data['error'])) {
            return new WP_Error('analysis_failed', $page_data['message'], ['status' => 500]);
        }

        // Get scorer
        $scorer = new ThreeJ_SEO_Scorer();
        $score_result = $scorer->analyze_page($page_data);

        // Generate recommendations
        $recommendations = $this->generate_remote_recommendations($score_result);

        return rest_ensure_response([
            'success' => true,
            'url' => $url,
            'score' => $score_result['overall_score'],
            'grade' => $score_result['grade'],
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Apply remote optimizations
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function apply_remote_optimizations($request) {
        $site_token = $request->get_header('X-Site-Token');
        $data = $request->get_json_params();

        if (empty($site_token)) {
            return new WP_Error('missing_token', 'Site token required', ['status' => 401]);
        }

        $site = $this->get_site_by_token($site_token);
        if (!$site) {
            return new WP_Error('invalid_token', 'Invalid site token', ['status' => 401]);
        }

        $url = esc_url_raw($data['url'] ?? '');
        $optimizations = $data['optimizations'] ?? [];

        if (empty($url)) {
            return new WP_Error('missing_url', 'URL required', ['status' => 400]);
        }

        // Generate optimization code
        $optimization_code = $this->generate_optimization_code($url, $optimizations);

        return rest_ensure_response([
            'success' => true,
            'url' => $url,
            'optimization_code' => $optimization_code,
            'instructions' => $this->get_application_instructions($optimizations),
        ]);
    }

    /**
     * Check remote permission
     *
     * @param WP_REST_Request $request Request object
     * @return bool|WP_Error
     */
    public function check_remote_permission($request) {
        $site_token = $request->get_header('X-Site-Token');

        if (empty($site_token)) {
            return new WP_Error('missing_token', 'Site token required', ['status' => 401]);
        }

        $site = $this->get_site_by_token($site_token);
        if (!$site) {
            return new WP_Error('invalid_token', 'Invalid site token', ['status' => 401]);
        }

        if ($site['status'] !== 'active') {
            return new WP_Error('inactive_site', 'Site is not active', ['status' => 403]);
        }

        return true;
    }

    /**
     * Verify license key
     *
     * @param string $license_key License key
     * @return bool Valid
     */
    private function verify_license($license_key) {
        // This would integrate with your license management system
        // For now, basic validation
        return !empty($license_key) && strlen($license_key) >= 16;
    }

    /**
     * Generate site token
     *
     * @param string $site_url Site URL
     * @return string Token
     */
    private function generate_site_token($site_url) {
        return 'remote_' . md5($site_url . time() . wp_generate_password(16, false));
    }

    /**
     * Get site by token
     *
     * @param string $token Site token
     * @return array|null Site data
     */
    private function get_site_by_token($token) {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_remote_sites';

        $site = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE site_token = %s",
            $token
        ), ARRAY_A);

        return $site;
    }

    /**
     * Process sync data
     *
     * @param int $site_id Site ID
     * @param array $data Sync data
     * @return array Result
     */
    private function process_sync_data($site_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_remote_sync';

        $synced = [];

        // Save page data
        if (isset($data['pages'])) {
            foreach ($data['pages'] as $page) {
                $wpdb->replace($table, [
                    'site_id' => $site_id,
                    'url' => esc_url_raw($page['url'] ?? ''),
                    'title' => sanitize_text_field($page['title'] ?? ''),
                    'data' => wp_json_encode($page),
                    'synced_at' => current_time('mysql'),
                ]);

                $synced[] = $page['url'];
            }
        }

        return $synced;
    }

    /**
     * Generate remote recommendations
     *
     * @param array $score_result Score result
     * @return array Recommendations
     */
    private function generate_remote_recommendations($score_result) {
        $recommendations = [];

        if (!empty($score_result['recommendations'])) {
            foreach ($score_result['recommendations'] as $rec) {
                $recommendations[] = [
                    'type' => $rec['factor'],
                    'priority' => $rec['priority'],
                    'message' => $rec['label_kr'] ?? $rec['label'],
                    'current_score' => $rec['current_score'],
                    'impact' => $rec['impact'],
                    'suggestion' => $this->get_suggestion_for_factor($rec['factor']),
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Get suggestion for factor
     *
     * @param string $factor Factor name
     * @return string Suggestion
     */
    private function get_suggestion_for_factor($factor) {
        $suggestions = [
            'title' => '제목에 키워드를 포함하고 50-60자로 최적화하세요.',
            'meta_description' => '메타 설명을 150-160자로 작성하고 키워드를 포함하세요.',
            'content_length' => '콘텐츠를 더 길고 상세하게 작성하세요 (최소 1000단어 권장).',
            'images' => '모든 이미지에 alt 텍스트를 추가하세요.',
            'internal_links' => '관련 페이지로의 내부 링크를 추가하세요.',
            'schema' => 'Schema.org 마크업을 추가하여 리치 결과 표시 가능성을 높이세요.',
        ];

        return $suggestions[$factor] ?? '이 요소를 개선하세요.';
    }

    /**
     * Generate optimization code
     *
     * @param string $url URL
     * @param array $optimizations Optimizations
     * @return string Code
     */
    private function generate_optimization_code($url, $optimizations) {
        $code = "<!-- WP Bulk SEO & AEO Optimization Code -->\n";
        $code .= "<script>\n";
        $code .= "window.WPBulkSEOAEO = window.WPBulkSEOAEO || {};\n";
        $code .= "window.WPBulkSEOAEO.optimizations = " . wp_json_encode($optimizations) . ";\n";
        $code .= "window.WPBulkSEOAEO.targetUrl = '" . esc_js($url) . "';\n";
        $code .= "</script>\n";
        $code .= "<!-- End WP Bulk SEO & AEO -->\n";

        return $code;
    }

    /**
     * Get application instructions
     *
     * @param array $optimizations Optimizations
     * @return string Instructions
     */
    private function get_application_instructions($optimizations) {
        $instructions = "다음 최적화를 적용하세요:\n\n";
        
        foreach ($optimizations as $opt) {
            $instructions .= "- " . $this->get_instruction_for_optimization($opt) . "\n";
        }

        return $instructions;
    }

    /**
     * Get instruction for optimization
     *
     * @param string $optimization Optimization type
     * @return string Instruction
     */
    private function get_instruction_for_optimization($optimization) {
        $instructions = [
            'meta_tags' => '메타 태그를 최적화하세요 (제목, 설명)',
            'title' => '페이지 제목을 50-60자로 최적화하세요',
            'content' => '콘텐츠 길이와 품질을 개선하세요',
            'images' => '이미지 alt 텍스트를 추가하세요',
            'schema' => 'Schema.org 마크업을 추가하세요',
        ];

        return $instructions[$optimization] ?? $optimization . ' 최적화를 적용하세요';
    }
}

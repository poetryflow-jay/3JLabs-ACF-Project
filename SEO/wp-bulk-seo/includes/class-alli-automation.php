<?php
/**
 * WP Bulk SEO - Alli AI Style Automation
 *
 * Alli AI를 벤치마킹한 자동화 기능
 * - Live Editor (실시간 편집)
 * - 대량 자동 최적화
 * - SEO A/B Testing
 * - Site Speed 자동 최적화
 * - 스마트 일괄 처리
 *
 * @package WP_Bulk_SEO
 * @version 2.1.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Alli_Automation {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Live Editor integration
        add_action('admin_enqueue_scripts', [$this, 'enqueue_live_editor_assets']);
        add_action('wp_ajax_wp_bulk_seo_live_edit', [$this, 'ajax_live_edit']);

        // Bulk automation
        add_action('wp_ajax_wp_bulk_seo_bulk_automate', [$this, 'ajax_bulk_automate']);

        // A/B Testing
        add_action('wp_ajax_wp_bulk_seo_create_ab_test', [$this, 'ajax_create_ab_test']);
        add_action('wp_ajax_wp_bulk_seo_get_ab_results', [$this, 'ajax_get_ab_results']);

        // Scheduled automation
        add_action('wp_bulk_seo_daily_automation', [$this, 'run_daily_automation']);
        if (!wp_next_scheduled('wp_bulk_seo_daily_automation')) {
            wp_schedule_event(time(), 'daily', 'wp_bulk_seo_daily_automation');
        }
    }

    /**
     * Enqueue Live Editor assets
     */
    public function enqueue_live_editor_assets($hook) {
        if ($hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }

        wp_enqueue_script(
            'wp-bulk-seo-live-editor',
            WP_BULK_SEO_PLUGIN_URL . 'assets/js/live-editor.js',
            ['jquery', 'wp-util'],
            WP_BULK_SEO_VERSION,
            true
        );

        wp_localize_script('wp-bulk-seo-live-editor', 'wpBulkSeoLive', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_bulk_seo_live_edit'),
        ]);
    }

    /**
     * Live Editor - Real-time SEO preview
     */
    public function ajax_live_edit() {
        check_ajax_referer('wp_bulk_seo_live_edit', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $field = isset($_POST['field']) ? sanitize_key($_POST['field']) : '';
        $value = isset($_POST['value']) ? sanitize_text_field($_POST['value']) : '';

        if (!$post_id || !$field) {
            wp_send_json_error(['message' => 'Invalid parameters']);
        }

        // Update field
        switch ($field) {
            case 'title':
                update_post_meta($post_id, '_wp_bulk_seo_title', $value);
                break;
            case 'description':
                update_post_meta($post_id, '_wp_bulk_seo_description', $value);
                break;
            case 'focus_keyword':
                update_post_meta($post_id, '_wp_bulk_seo_focus_keyword', $value);
                break;
        }

        // Re-analyze
        $analyzer = new WP_Bulk_SEO_Analyzer();
        $scorer = new WP_Bulk_SEO_Scorer();
        $page_data = $analyzer->analyze_post($post_id);
        $score = $scorer->calculate_score($page_data);

        wp_send_json_success([
            'score' => $score['overall_score'],
            'grade' => $score['grade'],
            'preview' => $this->generate_preview($post_id, $field, $value),
        ]);
    }

    /**
     * Generate SEO preview
     */
    private function generate_preview($post_id, $field, $value) {
        $post = get_post($post_id);
        $site_name = get_bloginfo('name');

        $preview = [
            'title' => get_post_meta($post_id, '_wp_bulk_seo_title', true) ?: $post->post_title,
            'description' => get_post_meta($post_id, '_wp_bulk_seo_description', true) ?: wp_trim_words($post->post_content, 30),
            'url' => get_permalink($post_id),
        ];

        // Update preview with new value
        if ($field === 'title') {
            $preview['title'] = $value;
        } elseif ($field === 'description') {
            $preview['description'] = $value;
        }

        // Generate Google SERP preview
        $serp_preview = sprintf(
            '<div class="serp-preview">
                <div class="serp-url">%s</div>
                <div class="serp-title">%s</div>
                <div class="serp-description">%s</div>
            </div>',
            esc_html($preview['url']),
            esc_html($preview['title']),
            esc_html($preview['description'])
        );

        return $serp_preview;
    }

    /**
     * Bulk automation
     */
    public function ajax_bulk_automate() {
        check_ajax_referer('wp_bulk_seo_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $post_ids = isset($_POST['post_ids']) ? array_map('intval', (array) $_POST['post_ids']) : [];
        $automation_type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'full';

        if (empty($post_ids)) {
            wp_send_json_error(['message' => 'No posts selected']);
        }

        $results = $this->run_bulk_automation($post_ids, $automation_type);

        wp_send_json_success($results);
    }

    /**
     * Run bulk automation
     */
    private function run_bulk_automation($post_ids, $type = 'full') {
        $auto_optimizer = WP_Bulk_SEO_Auto_Optimizer::instance();
        $results = [];

        foreach ($post_ids as $post_id) {
            try {
                $options = $this->get_automation_options($type);
                $result = $auto_optimizer->auto_optimize($post_id, $options);

                $results[] = [
                    'post_id' => $post_id,
                    'success' => $result['success'],
                    'score_improvement' => $result['score_improvement'] ?? 0,
                    'optimizations' => count($result['optimizations'] ?? []),
                ];
            } catch (Exception $e) {
                $results[] = [
                    'post_id' => $post_id,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'total' => count($post_ids),
            'success' => count(array_filter($results, fn($r) => $r['success'])),
            'failed' => count(array_filter($results, fn($r) => !$r['success'])),
            'results' => $results,
        ];
    }

    /**
     * Get automation options by type
     */
    private function get_automation_options($type) {
        $presets = [
            'full' => [
                'optimize_title' => true,
                'optimize_meta' => true,
                'add_schema' => true,
                'suggest_images' => true,
                'suggest_links' => true,
                'optimize_keywords' => true,
                'use_ai' => true,
            ],
            'quick' => [
                'optimize_title' => true,
                'optimize_meta' => true,
                'add_schema' => false,
                'suggest_images' => false,
                'suggest_links' => false,
                'optimize_keywords' => false,
                'use_ai' => true,
            ],
            'schema_only' => [
                'optimize_title' => false,
                'optimize_meta' => false,
                'add_schema' => true,
                'suggest_images' => false,
                'suggest_links' => false,
                'optimize_keywords' => false,
                'use_ai' => false,
            ],
        ];

        return $presets[$type] ?? $presets['full'];
    }

    /**
     * Create A/B test
     */
    public function ajax_create_ab_test() {
        check_ajax_referer('wp_bulk_seo_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $variant_a = isset($_POST['variant_a']) ? $_POST['variant_a'] : [];
        $variant_b = isset($_POST['variant_b']) ? $_POST['variant_b'] : [];

        if (!$post_id) {
            wp_send_json_error(['message' => 'Invalid post ID']);
        }

        $test_id = $this->create_ab_test($post_id, $variant_a, $variant_b);

        wp_send_json_success(['test_id' => $test_id]);
    }

    /**
     * Create A/B test
     */
    private function create_ab_test($post_id, $variant_a, $variant_b) {
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_ab_tests';

        $wpdb->insert($table, [
            'post_id' => $post_id,
            'variant_a' => wp_json_encode($variant_a),
            'variant_b' => wp_json_encode($variant_b),
            'status' => 'running',
            'traffic_split' => 50, // 50/50 split
            'started_at' => current_time('mysql'),
        ]);

        return $wpdb->insert_id;
    }

    /**
     * Get A/B test results
     */
    public function ajax_get_ab_results() {
        check_ajax_referer('wp_bulk_seo_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $test_id = isset($_POST['test_id']) ? intval($_POST['test_id']) : 0;

        if (!$test_id) {
            wp_send_json_error(['message' => 'Invalid test ID']);
        }

        $results = $this->get_ab_test_results($test_id);

        wp_send_json_success($results);
    }

    /**
     * Get A/B test results
     */
    private function get_ab_test_results($test_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_ab_tests';

        $test = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $test_id
        ), ARRAY_A);

        if (!$test) {
            return null;
        }

        // Calculate conversion rates (would integrate with analytics)
        $results = [
            'test_id' => $test_id,
            'variant_a' => [
                'impressions' => $test['variant_a_impressions'] ?? 0,
                'clicks' => $test['variant_a_clicks'] ?? 0,
                'ctr' => $this->calculate_ctr($test['variant_a_impressions'] ?? 0, $test['variant_a_clicks'] ?? 0),
            ],
            'variant_b' => [
                'impressions' => $test['variant_b_impressions'] ?? 0,
                'clicks' => $test['variant_b_clicks'] ?? 0,
                'ctr' => $this->calculate_ctr($test['variant_b_impressions'] ?? 0, $test['variant_b_clicks'] ?? 0),
            ],
            'winner' => $this->determine_winner($test),
            'confidence' => $this->calculate_confidence($test),
        ];

        return $results;
    }

    /**
     * Calculate CTR
     */
    private function calculate_ctr($impressions, $clicks) {
        if ($impressions === 0) return 0;
        return round(($clicks / $impressions) * 100, 2);
    }

    /**
     * Determine winner
     */
    private function determine_winner($test) {
        $ctr_a = $this->calculate_ctr($test['variant_a_impressions'] ?? 0, $test['variant_a_clicks'] ?? 0);
        $ctr_b = $this->calculate_ctr($test['variant_b_impressions'] ?? 0, $test['variant_b_clicks'] ?? 0);

        if ($ctr_a > $ctr_b) {
            return 'A';
        } elseif ($ctr_b > $ctr_a) {
            return 'B';
        }

        return 'tie';
    }

    /**
     * Calculate statistical confidence
     */
    private function calculate_confidence($test) {
        // Simplified confidence calculation
        $total_impressions = ($test['variant_a_impressions'] ?? 0) + ($test['variant_b_impressions'] ?? 0);

        if ($total_impressions < 100) {
            return 'low';
        } elseif ($total_impressions < 1000) {
            return 'medium';
        }

        return 'high';
    }

    /**
     * Run daily automation
     */
    public function run_daily_automation() {
        if (!get_option('wp_bulk_seo_auto_automation', false)) {
            return;
        }

        // Get posts that need optimization
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_scores';
        $cutoff_score = get_option('wp_bulk_seo_auto_optimize_threshold', 60);

        $low_scoring = $wpdb->get_col($wpdb->prepare(
            "SELECT post_id FROM $table WHERE overall_score < %d ORDER BY overall_score ASC LIMIT 50",
            $cutoff_score
        ));

        if (!empty($low_scoring)) {
            $this->run_bulk_automation($low_scoring, 'full');
        }
    }

    /**
     * Smart batch processing
     */
    public function smart_batch_process($post_ids, $batch_size = 10) {
        $batches = array_chunk($post_ids, $batch_size);
        $results = [];

        foreach ($batches as $batch) {
            $batch_results = $this->run_bulk_automation($batch, 'full');
            $results = array_merge($results, $batch_results['results']);

            // Prevent timeout
            if (function_exists('wp_cache_flush')) {
                wp_cache_flush();
            }

            // Small delay to prevent server overload
            usleep(500000); // 0.5 seconds
        }

        return [
            'total' => count($post_ids),
            'success' => count(array_filter($results, fn($r) => $r['success'])),
            'failed' => count(array_filter($results, fn($r) => !$r['success'])),
            'results' => $results,
        ];
    }
}

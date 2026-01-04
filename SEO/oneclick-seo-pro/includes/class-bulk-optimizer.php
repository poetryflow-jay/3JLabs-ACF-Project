<?php
/**
 * WP Bulk SEO - Bulk Optimizer
 *
 * Handles bulk SEO analysis and optimization operations.
 *
 * @package OneClick_SEO_Pro
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class OneClick_SEO_Pro_Bulk_Optimizer {

    /**
     * Batch size for processing
     */
    private $batch_size;

    /**
     * Constructor
     */
    public function __construct() {
        $this->batch_size = get_option('oneclick_seo_pro_bulk_batch_size', 10);
    }

    /**
     * Analyze all posts
     *
     * @param array $args Query arguments
     * @return array Results
     */
    public function analyze_all($args = []) {
        $defaults = [
            'post_type' => get_option('oneclick_seo_pro_post_types', ['post', 'page']),
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ];

        $args = wp_parse_args($args, $defaults);
        $posts = get_posts($args);

        return $this->process_batch($posts, 'analyze');
    }

    /**
     * Analyze posts by score threshold
     *
     * @param int $max_score Only analyze posts with score below this
     * @return array Results
     */
    public function analyze_low_scoring($max_score = 60) {
        global $wpdb;

        $table = $wpdb->prefix . 'oneclick_seo_scores';
        $post_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT post_id FROM $table WHERE overall_score < %d OR overall_score IS NULL",
            $max_score
        ));

        if (empty($post_ids)) {
            // Get posts without scores
            $analyzed = $wpdb->get_col("SELECT post_id FROM $table");
            $all_posts = get_posts([
                'post_type' => get_option('oneclick_seo_pro_post_types', ['post', 'page']),
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'fields' => 'ids',
            ]);

            $post_ids = array_diff($all_posts, $analyzed);
        }

        return $this->process_batch($post_ids, 'analyze');
    }

    /**
     * Process a batch of posts
     *
     * @param array $post_ids Post IDs to process
     * @param string $operation Operation type (analyze|optimize)
     * @return array Results
     */
    public function process_batch($post_ids, $operation = 'analyze') {
        $total = count($post_ids);
        $processed = 0;
        $success = 0;
        $failed = 0;
        $results = [];

        // Log operation start
        $log_id = $this->log_operation_start($operation, $total);

        // Process in batches
        $batches = array_chunk($post_ids, $this->batch_size);

        foreach ($batches as $batch) {
            foreach ($batch as $post_id) {
                try {
                    if ($operation === 'analyze') {
                        $result = $this->analyze_post($post_id);
                    } else {
                        $result = $this->optimize_post($post_id);
                    }

                    if ($result['success']) {
                        $success++;
                        $results[$post_id] = [
                            'status' => 'success',
                            'score' => $result['score'] ?? null,
                            'grade' => $result['grade'] ?? null,
                        ];
                    } else {
                        $failed++;
                        $results[$post_id] = [
                            'status' => 'failed',
                            'error' => $result['error'] ?? 'Unknown error',
                        ];
                    }
                } catch (\Exception $e) {
                    $failed++;
                    $results[$post_id] = [
                        'status' => 'error',
                        'error' => $e->getMessage(),
                    ];
                }

                $processed++;

                // Update log
                $this->update_operation_log($log_id, $processed, $success, $failed);
            }

            // Prevent timeout
            if (function_exists('wp_cache_flush')) {
                wp_cache_flush();
            }
        }

        // Complete log
        $this->complete_operation_log($log_id, $results);

        return [
            'total' => $total,
            'processed' => $processed,
            'success' => $success,
            'failed' => $failed,
            'results' => $results,
        ];
    }

    /**
     * Analyze a single post
     *
     * @param int $post_id Post ID
     * @return array Result
     */
    public function analyze_post($post_id) {
        $analyzer = oneclick_seo_pro()->analyzer;
        $scorer = oneclick_seo_pro()->scorer;

        if (!$analyzer || !$scorer) {
            return [
                'success' => false,
                'error' => 'Analyzer or scorer not initialized',
            ];
        }

        // Analyze the post
        $page_data = $analyzer->analyze_post($post_id);

        if (isset($page_data['error'])) {
            return [
                'success' => false,
                'error' => $page_data['message'] ?? 'Analysis failed',
            ];
        }

        // Get focus keyword if set
        $page_data['focus_keyword'] = $this->get_focus_keyword($post_id);

        // Score the page
        $score_result = $scorer->analyze_page($page_data);

        // Save to database
        $this->save_score($post_id, $score_result);

        // Save issues
        $this->save_issues($post_id, $score_result);

        return [
            'success' => true,
            'score' => $score_result['overall_score'],
            'grade' => $score_result['grade'],
            'categories' => $score_result['categories'],
            'recommendations' => $score_result['recommendations'],
        ];
    }

    /**
     * Optimize a single post
     *
     * @param int $post_id Post ID
     * @param array $options Optimization options
     * @return array Result
     */
    public function optimize_post($post_id, $options = []) {
        $defaults = [
            'optimize_title' => true,
            'optimize_meta' => true,
            'optimize_headings' => false,
            'add_schema' => true,
            'use_ai' => true,
        ];

        $options = wp_parse_args($options, $defaults);

        // First, analyze current state
        $analysis = $this->analyze_post($post_id);

        if (!$analysis['success']) {
            return $analysis;
        }

        $optimizations = [];

        // Title optimization
        if ($options['optimize_title']) {
            $title_result = $this->optimize_title($post_id, $options['use_ai']);
            if ($title_result['optimized']) {
                $optimizations[] = 'title';
            }
        }

        // Meta description optimization
        if ($options['optimize_meta']) {
            $meta_result = $this->optimize_meta_description($post_id, $options['use_ai']);
            if ($meta_result['optimized']) {
                $optimizations[] = 'meta_description';
            }
        }

        // Schema markup
        if ($options['add_schema']) {
            $schema_result = $this->add_schema_markup($post_id);
            if ($schema_result['added']) {
                $optimizations[] = 'schema';
            }
        }

        // Re-analyze after optimization
        $new_analysis = $this->analyze_post($post_id);

        return [
            'success' => true,
            'optimizations' => $optimizations,
            'before_score' => $analysis['score'],
            'after_score' => $new_analysis['score'],
            'improvement' => $new_analysis['score'] - $analysis['score'],
        ];
    }

    /**
     * Get focus keyword for post
     */
    private function get_focus_keyword($post_id) {
        // Check various SEO plugins
        $keyword = get_post_meta($post_id, '_yoast_wpseo_focuskw', true);
        if ($keyword) return $keyword;

        $keyword = get_post_meta($post_id, 'rank_math_focus_keyword', true);
        if ($keyword) return $keyword;

        $keyword = get_post_meta($post_id, '_aioseo_keywords', true);
        if ($keyword) return $keyword;

        // Our own meta
        $keyword = get_post_meta($post_id, '_oneclick_seo_pro_focus_keyword', true);
        if ($keyword) return $keyword;

        return '';
    }

    /**
     * Save score to database
     */
    private function save_score($post_id, $score_result) {
        global $wpdb;

        $table = $wpdb->prefix . 'oneclick_seo_scores';

        $data = [
            'post_id' => $post_id,
            'overall_score' => $score_result['overall_score'],
            'grade' => $score_result['grade'],
            'technical_score' => $score_result['categories']['technical']['score'] ?? 0,
            'on_page_score' => $score_result['categories']['on_page']['score'] ?? 0,
            'content_score' => $score_result['categories']['content']['score'] ?? 0,
            'links_score' => $score_result['categories']['links']['score'] ?? 0,
            'schema_score' => $score_result['categories']['schema']['score'] ?? 0,
            'engagement_score' => $score_result['categories']['engagement']['score'] ?? 0,
            'freshness_score' => $score_result['categories']['freshness']['score'] ?? 0,
            'eeat_score' => $score_result['categories']['eeat']['score'] ?? 0,
            'demotion_risk' => $score_result['categories']['demotions']['has_risk'] ?? 0 ? 1 : 0,
            'analysis_data' => wp_json_encode($score_result),
            'recommendations' => wp_json_encode($score_result['recommendations']),
            'analyzed_at' => current_time('mysql'),
        ];

        // Check if record exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE post_id = %d",
            $post_id
        ));

        if ($existing) {
            $wpdb->update($table, $data, ['post_id' => $post_id]);
        } else {
            $wpdb->insert($table, $data);
        }
    }

    /**
     * Save issues to database
     */
    private function save_issues($post_id, $score_result) {
        global $wpdb;

        $table = $wpdb->prefix . 'oneclick_seo_issues';

        // Clear old unresolved issues for this post
        $wpdb->delete($table, [
            'post_id' => $post_id,
            'is_resolved' => 0,
        ]);

        // Insert new issues from recommendations
        if (!empty($score_result['recommendations'])) {
            foreach ($score_result['recommendations'] as $rec) {
                $severity = 'low';
                if ($rec['priority'] === 'P0') $severity = 'critical';
                elseif ($rec['priority'] === 'P1') $severity = 'high';
                elseif ($rec['priority'] === 'P2') $severity = 'medium';

                $wpdb->insert($table, [
                    'post_id' => $post_id,
                    'issue_type' => $rec['factor'],
                    'severity' => $severity,
                    'factor_key' => $rec['factor'],
                    'message' => sprintf('%s needs improvement (Score: %d)', $rec['label'], $rec['current_score']),
                    'message_kr' => sprintf('%s 개선 필요 (점수: %d)', $rec['label_kr'], $rec['current_score']),
                    'is_resolved' => 0,
                    'detected_at' => current_time('mysql'),
                ]);
            }
        }

        // Insert priority actions
        if (!empty($score_result['priority_actions'])) {
            foreach ($score_result['priority_actions'] as $action) {
                $wpdb->insert($table, [
                    'post_id' => $post_id,
                    'issue_type' => $action['type'],
                    'severity' => 'critical',
                    'factor_key' => $action['factor'],
                    'message' => $action['message'],
                    'message_kr' => $action['message_kr'],
                    'is_resolved' => 0,
                    'detected_at' => current_time('mysql'),
                ]);
            }
        }
    }

    /**
     * Optimize title using AI
     */
    private function optimize_title($post_id, $use_ai = true) {
        $post = get_post($post_id);
        $current_title = $post->post_title;

        if (!$use_ai) {
            return ['optimized' => false, 'title' => $current_title];
        }

        $ai = oneclick_seo_pro()->ai_engine;
        if (!$ai) {
            return ['optimized' => false, 'title' => $current_title];
        }

        $focus_keyword = $this->get_focus_keyword($post_id);
        $optimized_title = $ai->optimize_title($current_title, $focus_keyword);

        if ($optimized_title && $optimized_title !== $current_title) {
            // Save as SEO title, not post title
            update_post_meta($post_id, '_oneclick_seo_pro_title', $optimized_title);
            return ['optimized' => true, 'title' => $optimized_title];
        }

        return ['optimized' => false, 'title' => $current_title];
    }

    /**
     * Optimize meta description using AI
     */
    private function optimize_meta_description($post_id, $use_ai = true) {
        $current_desc = get_post_meta($post_id, '_oneclick_seo_pro_description', true);

        if (empty($current_desc)) {
            $post = get_post($post_id);
            $current_desc = wp_trim_words($post->post_content, 30, '...');
        }

        if (!$use_ai) {
            return ['optimized' => false, 'description' => $current_desc];
        }

        $ai = oneclick_seo_pro()->ai_engine;
        if (!$ai) {
            return ['optimized' => false, 'description' => $current_desc];
        }

        $focus_keyword = $this->get_focus_keyword($post_id);
        $optimized_desc = $ai->optimize_meta_description($current_desc, $focus_keyword);

        if ($optimized_desc && $optimized_desc !== $current_desc) {
            update_post_meta($post_id, '_oneclick_seo_pro_description', $optimized_desc);
            return ['optimized' => true, 'description' => $optimized_desc];
        }

        return ['optimized' => false, 'description' => $current_desc];
    }

    /**
     * Add schema markup
     */
    private function add_schema_markup($post_id) {
        $schema_generator = oneclick_seo_pro()->schema_generator;

        if (!$schema_generator) {
            return ['added' => false];
        }

        $schema = $schema_generator->generate_for_post($post_id);

        if ($schema) {
            update_post_meta($post_id, '_oneclick_seo_pro_schema', wp_json_encode($schema));
            return ['added' => true, 'schema' => $schema];
        }

        return ['added' => false];
    }

    /**
     * Log operation start
     */
    private function log_operation_start($operation, $total) {
        global $wpdb;

        $wpdb->insert($wpdb->prefix . 'oneclick_seo_log', [
            'operation_type' => $operation,
            'status' => 'running',
            'total_items' => $total,
            'processed_items' => 0,
            'success_items' => 0,
            'failed_items' => 0,
            'started_at' => current_time('mysql'),
            'user_id' => get_current_user_id(),
        ]);

        return $wpdb->insert_id;
    }

    /**
     * Update operation log
     */
    private function update_operation_log($log_id, $processed, $success, $failed) {
        global $wpdb;

        $wpdb->update(
            $wpdb->prefix . 'oneclick_seo_log',
            [
                'processed_items' => $processed,
                'success_items' => $success,
                'failed_items' => $failed,
            ],
            ['id' => $log_id]
        );
    }

    /**
     * Complete operation log
     */
    private function complete_operation_log($log_id, $results) {
        global $wpdb;

        $wpdb->update(
            $wpdb->prefix . 'oneclick_seo_log',
            [
                'status' => 'completed',
                'details' => wp_json_encode($results),
                'completed_at' => current_time('mysql'),
            ],
            ['id' => $log_id]
        );
    }

    /**
     * Run scheduled analysis (cron)
     */
    public function run_scheduled_analysis() {
        if (!get_option('oneclick_seo_pro_auto_analyze', true)) {
            return;
        }

        // Analyze posts that haven't been analyzed in 7 days
        global $wpdb;

        $table = $wpdb->prefix . 'oneclick_seo_scores';
        $cutoff = date('Y-m-d H:i:s', strtotime('-7 days'));

        $stale_posts = $wpdb->get_col($wpdb->prepare(
            "SELECT post_id FROM $table WHERE analyzed_at < %s",
            $cutoff
        ));

        if (!empty($stale_posts)) {
            $this->process_batch(array_slice($stale_posts, 0, 50), 'analyze');
        }
    }

    /**
     * Get statistics
     */
    public function get_statistics() {
        global $wpdb;

        $table = $wpdb->prefix . 'oneclick_seo_scores';

        $stats = $wpdb->get_row("
            SELECT
                COUNT(*) as total_analyzed,
                AVG(overall_score) as avg_score,
                SUM(CASE WHEN overall_score >= 80 THEN 1 ELSE 0 END) as good_count,
                SUM(CASE WHEN overall_score >= 60 AND overall_score < 80 THEN 1 ELSE 0 END) as warning_count,
                SUM(CASE WHEN overall_score < 60 THEN 1 ELSE 0 END) as critical_count,
                SUM(CASE WHEN demotion_risk = 1 THEN 1 ELSE 0 END) as at_risk_count
            FROM $table
        ", ARRAY_A);

        // Total posts
        $total_posts = wp_count_posts('post')->publish + wp_count_posts('page')->publish;
        $stats['total_posts'] = $total_posts;
        $stats['not_analyzed'] = $total_posts - ($stats['total_analyzed'] ?? 0);
        $stats['avg_score'] = round($stats['avg_score'] ?? 0, 1);

        return $stats;
    }
}

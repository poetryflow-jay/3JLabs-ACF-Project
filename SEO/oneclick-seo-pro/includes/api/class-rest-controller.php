<?php
/**
 * WP Bulk SEO - REST API Controller
 *
 * @package OneClick_SEO_Pro
 * @subpackage API
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * REST API Controller
 */
class OneClick_SEO_Pro_REST_Controller {

    /**
     * Namespace
     */
    const NAMESPACE = 'oneclick-seo-pro/v1';

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register REST routes
     */
    public function register_routes() {
        // Analyze single URL
        register_rest_route(self::NAMESPACE, '/analyze', [
            'methods' => 'POST',
            'callback' => [$this, 'analyze_url'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'url' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'esc_url_raw',
                ],
                'include_pagespeed' => [
                    'type' => 'boolean',
                    'default' => true,
                ],
            ],
        ]);

        // Analyze post
        register_rest_route(self::NAMESPACE, '/analyze/(?P<post_id>\d+)', [
            'methods' => 'POST',
            'callback' => [$this, 'analyze_post'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'post_id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
            ],
        ]);

        // Get post SEO data
        register_rest_route(self::NAMESPACE, '/posts/(?P<post_id>\d+)/seo', [
            'methods' => 'GET',
            'callback' => [$this, 'get_post_seo'],
            'permission_callback' => [$this, 'check_permission'],
        ]);

        // Update post SEO data
        register_rest_route(self::NAMESPACE, '/posts/(?P<post_id>\d+)/seo', [
            'methods' => 'PUT',
            'callback' => [$this, 'update_post_seo'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'seo_title' => ['type' => 'string'],
                'meta_description' => ['type' => 'string'],
                'focus_keyword' => ['type' => 'string'],
                'canonical_url' => ['type' => 'string'],
                'robots' => ['type' => 'string'],
            ],
        ]);

        // Get all scores
        register_rest_route(self::NAMESPACE, '/scores', [
            'methods' => 'GET',
            'callback' => [$this, 'get_scores'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'post_type' => ['type' => 'string'],
                'grade' => ['type' => 'string'],
                'per_page' => ['type' => 'integer', 'default' => 20],
                'page' => ['type' => 'integer', 'default' => 1],
            ],
        ]);

        // Get dashboard stats
        register_rest_route(self::NAMESPACE, '/stats', [
            'methods' => 'GET',
            'callback' => [$this, 'get_stats'],
            'permission_callback' => [$this, 'check_permission'],
        ]);

        // Bulk analyze
        register_rest_route(self::NAMESPACE, '/bulk-analyze', [
            'methods' => 'POST',
            'callback' => [$this, 'bulk_analyze'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'post_ids' => [
                    'required' => true,
                    'type' => 'array',
                ],
            ],
        ]);

        // AEO analyze
        register_rest_route(self::NAMESPACE, '/aeo/analyze/(?P<post_id>\d+)', [
            'methods' => 'POST',
            'callback' => [$this, 'analyze_aeo'],
            'permission_callback' => [$this, 'check_permission'],
        ]);

        // Generate FAQ
        register_rest_route(self::NAMESPACE, '/aeo/faq/(?P<post_id>\d+)', [
            'methods' => 'POST',
            'callback' => [$this, 'generate_faq'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'generate_ai' => ['type' => 'boolean', 'default' => false],
            ],
        ]);

        // Save FAQ
        register_rest_route(self::NAMESPACE, '/aeo/faq/(?P<post_id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$this, 'save_faq'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'faqs' => ['type' => 'array', 'required' => true],
            ],
        ]);

        // Generate schema
        register_rest_route(self::NAMESPACE, '/schema/(?P<post_id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_schema'],
            'permission_callback' => '__return_true',
        ]);

        // Sitemap endpoints
        register_rest_route(self::NAMESPACE, '/sitemap/regenerate', [
            'methods' => 'POST',
            'callback' => [$this, 'regenerate_sitemap'],
            'permission_callback' => [$this, 'check_permission'],
        ]);

        // AI suggestions
        register_rest_route(self::NAMESPACE, '/ai/suggestions/(?P<post_id>\d+)', [
            'methods' => 'POST',
            'callback' => [$this, 'get_ai_suggestions'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'type' => ['type' => 'string', 'enum' => ['title', 'description', 'content']],
            ],
        ]);

        // PageSpeed data
        register_rest_route(self::NAMESPACE, '/pagespeed', [
            'methods' => 'POST',
            'callback' => [$this, 'get_pagespeed'],
            'permission_callback' => [$this, 'check_permission'],
            'args' => [
                'url' => ['type' => 'string', 'required' => true],
                'strategy' => ['type' => 'string', 'default' => 'mobile'],
            ],
        ]);

        // Settings
        register_rest_route(self::NAMESPACE, '/settings', [
            'methods' => 'GET',
            'callback' => [$this, 'get_settings'],
            'permission_callback' => [$this, 'check_admin_permission'],
        ]);

        register_rest_route(self::NAMESPACE, '/settings', [
            'methods' => 'PUT',
            'callback' => [$this, 'update_settings'],
            'permission_callback' => [$this, 'check_admin_permission'],
        ]);
    }

    /**
     * Check permission
     */
    public function check_permission($request) {
        return current_user_can('edit_posts');
    }

    /**
     * Check admin permission
     */
    public function check_admin_permission($request) {
        return current_user_can('manage_options');
    }

    /**
     * Analyze URL
     */
    public function analyze_url($request) {
        $url = $request->get_param('url');
        $include_pagespeed = $request->get_param('include_pagespeed');

        $analyzer = OneClick_SEO_Pro_Analyzer::get_instance();
        $result = $analyzer->analyze_url($url);

        if (is_wp_error($result)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => $result->get_error_message(),
            ], 400);
        }

        // Add PageSpeed data if requested
        if ($include_pagespeed) {
            $pagespeed_data = $analyzer->fetch_pagespeed_data($url);
            if (!is_wp_error($pagespeed_data)) {
                $result['pagespeed'] = $pagespeed_data;
            }
        }

        return new WP_REST_Response([
            'success' => true,
            'data' => $result,
        ], 200);
    }

    /**
     * Analyze post
     */
    public function analyze_post($request) {
        $post_id = $request->get_param('post_id');
        $post = get_post($post_id);

        if (!$post) {
            return new WP_REST_Response([
                'success' => false,
                'message' => __('Post not found', 'oneclick-seo-pro'),
            ], 404);
        }

        $analyzer = OneClick_SEO_Pro_Analyzer::get_instance();
        $result = $analyzer->analyze_post($post_id);

        if (is_wp_error($result)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => $result->get_error_message(),
            ], 400);
        }

        // Save score to database
        $this->save_score($post_id, $result);

        return new WP_REST_Response([
            'success' => true,
            'data' => $result,
        ], 200);
    }

    /**
     * Get post SEO data
     */
    public function get_post_seo($request) {
        $post_id = $request->get_param('post_id');

        $data = [
            'post_id' => $post_id,
            'seo_title' => get_post_meta($post_id, '_oneclick_seo_pro_title', true),
            'meta_description' => get_post_meta($post_id, '_oneclick_seo_pro_description', true),
            'focus_keyword' => get_post_meta($post_id, '_oneclick_seo_pro_focus_keyword', true),
            'canonical_url' => get_post_meta($post_id, '_oneclick_seo_pro_canonical', true),
            'robots' => get_post_meta($post_id, '_oneclick_seo_pro_robots', true),
            'score' => get_post_meta($post_id, '_oneclick_seo_pro_score', true),
            'grade' => get_post_meta($post_id, '_oneclick_seo_pro_grade', true),
            'last_analyzed' => get_post_meta($post_id, '_oneclick_seo_pro_analyzed', true),
        ];

        return new WP_REST_Response([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    /**
     * Update post SEO data
     */
    public function update_post_seo($request) {
        $post_id = $request->get_param('post_id');

        $fields = ['seo_title', 'meta_description', 'focus_keyword', 'canonical_url', 'robots'];

        foreach ($fields as $field) {
            $value = $request->get_param($field);
            if ($value !== null) {
                $meta_key = '_oneclick_seo_pro_' . str_replace('seo_', '', $field);
                update_post_meta($post_id, $meta_key, sanitize_text_field($value));
            }
        }

        return new WP_REST_Response([
            'success' => true,
            'message' => __('SEO data updated', 'oneclick-seo-pro'),
        ], 200);
    }

    /**
     * Get scores
     */
    public function get_scores($request) {
        global $wpdb;

        $post_type = $request->get_param('post_type');
        $grade = $request->get_param('grade');
        $per_page = $request->get_param('per_page');
        $page = $request->get_param('page');
        $offset = ($page - 1) * $per_page;

        $table = $wpdb->prefix . 'oneclick_seo_scores';

        $where = ['1=1'];
        $params = [];

        if ($post_type) {
            $where[] = 'post_type = %s';
            $params[] = $post_type;
        }

        if ($grade) {
            $where[] = 'grade = %s';
            $params[] = $grade;
        }

        $where_sql = implode(' AND ', $where);

        // Get total count
        $count_sql = "SELECT COUNT(*) FROM $table WHERE $where_sql";
        $total = $wpdb->get_var($params ? $wpdb->prepare($count_sql, $params) : $count_sql);

        // Get results
        $params[] = $per_page;
        $params[] = $offset;

        $sql = "SELECT s.*, p.post_title
                FROM $table s
                LEFT JOIN {$wpdb->posts} p ON s.post_id = p.ID
                WHERE $where_sql
                ORDER BY s.analyzed_at DESC
                LIMIT %d OFFSET %d";

        $results = $wpdb->get_results($wpdb->prepare($sql, $params));

        return new WP_REST_Response([
            'success' => true,
            'data' => [
                'scores' => $results,
                'total' => (int) $total,
                'pages' => ceil($total / $per_page),
            ],
        ], 200);
    }

    /**
     * Get dashboard stats
     */
    public function get_stats($request) {
        global $wpdb;

        $general_settings = get_option('oneclick_seo_pro_general', []);
        $post_types = $general_settings['post_types'] ?? ['post', 'page'];
        $placeholders = implode(',', array_fill(0, count($post_types), '%s'));

        // Total posts
        $total_sql = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN ($placeholders)";
        $total_posts = $wpdb->get_var($wpdb->prepare($total_sql, $post_types));

        // Analyzed posts
        $table = $wpdb->prefix . 'oneclick_seo_scores';
        $analyzed_posts = $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM $table");

        // Average score
        $average_score = $wpdb->get_var("SELECT AVG(overall_score) FROM $table");

        // Grade distribution
        $grades = $wpdb->get_results("SELECT grade, COUNT(*) as count FROM $table GROUP BY grade", OBJECT_K);
        $grade_distribution = [
            'A+' => $grades['A+']->count ?? 0,
            'A' => $grades['A']->count ?? 0,
            'B' => $grades['B']->count ?? 0,
            'C' => $grades['C']->count ?? 0,
            'D' => $grades['D']->count ?? 0,
            'F' => $grades['F']->count ?? 0,
        ];

        // Issues count
        $issues_table = $wpdb->prefix . 'oneclick_seo_issues';
        $issues_count = $wpdb->get_var("SELECT COUNT(*) FROM $issues_table WHERE status = 'open'");
        $critical_issues = $wpdb->get_var("SELECT COUNT(*) FROM $issues_table WHERE status = 'open' AND severity = 'critical'");

        // Top issues
        $top_issues = $wpdb->get_results(
            "SELECT issue_type, COUNT(*) as count FROM $issues_table WHERE status = 'open' GROUP BY issue_type ORDER BY count DESC LIMIT 5"
        );

        // Recent scores
        $recent_scores = $wpdb->get_results(
            "SELECT s.*, p.post_title FROM $table s LEFT JOIN {$wpdb->posts} p ON s.post_id = p.ID ORDER BY s.analyzed_at DESC LIMIT 10"
        );

        return new WP_REST_Response([
            'success' => true,
            'data' => [
                'total_posts' => (int) $total_posts,
                'analyzed_posts' => (int) $analyzed_posts,
                'average_score' => round((float) $average_score, 1),
                'grade_distribution' => $grade_distribution,
                'issues_count' => (int) $issues_count,
                'critical_issues' => (int) $critical_issues,
                'top_issues' => $top_issues,
                'recent_scores' => $recent_scores,
            ],
        ], 200);
    }

    /**
     * Bulk analyze
     */
    public function bulk_analyze($request) {
        $post_ids = $request->get_param('post_ids');
        $analyzer = OneClick_SEO_Pro_Analyzer::get_instance();

        $results = [];
        foreach ($post_ids as $post_id) {
            $result = $analyzer->analyze_post($post_id);
            if (!is_wp_error($result)) {
                $this->save_score($post_id, $result);
                $results[$post_id] = [
                    'success' => true,
                    'score' => $result['score']['overall'],
                    'grade' => $result['score']['grade'],
                ];
            } else {
                $results[$post_id] = [
                    'success' => false,
                    'error' => $result->get_error_message(),
                ];
            }
        }

        return new WP_REST_Response([
            'success' => true,
            'data' => $results,
        ], 200);
    }

    /**
     * Analyze AEO
     */
    public function analyze_aeo($request) {
        $post_id = $request->get_param('post_id');
        $post = get_post($post_id);

        if (!$post) {
            return new WP_REST_Response([
                'success' => false,
                'message' => __('Post not found', 'oneclick-seo-pro'),
            ], 404);
        }

        $aeo_engine = OneClick_SEO_Pro_AEO_Engine::get_instance();
        $result = $aeo_engine->analyze($post);

        return new WP_REST_Response([
            'success' => true,
            'data' => array_merge($result, ['post_title' => $post->post_title]),
        ], 200);
    }

    /**
     * Generate FAQ
     */
    public function generate_faq($request) {
        $post_id = $request->get_param('post_id');
        $generate_ai = $request->get_param('generate_ai');

        $post = get_post($post_id);
        if (!$post) {
            return new WP_REST_Response([
                'success' => false,
                'message' => __('Post not found', 'oneclick-seo-pro'),
            ], 404);
        }

        $faq_generator = OneClick_SEO_Pro_FAQ_Generator::get_instance();

        if ($generate_ai) {
            $faqs = $faq_generator->generate_from_content($post->post_content);
        } else {
            $faqs = $faq_generator->extract_existing($post);
        }

        return new WP_REST_Response([
            'success' => true,
            'data' => ['faqs' => $faqs],
        ], 200);
    }

    /**
     * Save FAQ
     */
    public function save_faq($request) {
        $post_id = $request->get_param('post_id');
        $faqs = $request->get_param('faqs');

        // Sanitize FAQs
        $sanitized = array_map(function($faq) {
            return [
                'question' => sanitize_text_field($faq['question'] ?? ''),
                'answer' => wp_kses_post($faq['answer'] ?? ''),
            ];
        }, $faqs);

        // Save to post meta
        update_post_meta($post_id, '_oneclick_seo_pro_faq', $sanitized);

        return new WP_REST_Response([
            'success' => true,
            'message' => __('FAQ saved', 'oneclick-seo-pro'),
        ], 200);
    }

    /**
     * Get schema
     */
    public function get_schema($request) {
        $post_id = $request->get_param('post_id');
        $post = get_post($post_id);

        if (!$post) {
            return new WP_REST_Response([
                'success' => false,
                'message' => __('Post not found', 'oneclick-seo-pro'),
            ], 404);
        }

        $schema_generator = OneClick_SEO_Pro_Schema_Generator::get_instance();
        $schema = $schema_generator->generate_all_schema($post);

        return new WP_REST_Response([
            'success' => true,
            'data' => $schema,
        ], 200);
    }

    /**
     * Regenerate sitemap
     */
    public function regenerate_sitemap($request) {
        $sitemap_manager = OneClick_SEO_Pro_Sitemap_Manager::get_instance();
        $sitemap_manager->regenerate();

        return new WP_REST_Response([
            'success' => true,
            'message' => __('Sitemap regenerated', 'oneclick-seo-pro'),
            'sitemap_url' => home_url('/sitemap.xml'),
        ], 200);
    }

    /**
     * Get AI suggestions
     */
    public function get_ai_suggestions($request) {
        $post_id = $request->get_param('post_id');
        $type = $request->get_param('type') ?: 'all';

        $post = get_post($post_id);
        if (!$post) {
            return new WP_REST_Response([
                'success' => false,
                'message' => __('Post not found', 'oneclick-seo-pro'),
            ], 404);
        }

        $ai = OneClick_SEO_Pro_AI_Provider::get_instance();

        $suggestions = [];

        if ($type === 'title' || $type === 'all') {
            $suggestions['titles'] = $ai->generate_title_suggestions($post);
        }

        if ($type === 'description' || $type === 'all') {
            $suggestions['descriptions'] = $ai->generate_description_suggestions($post);
        }

        return new WP_REST_Response([
            'success' => true,
            'data' => $suggestions,
        ], 200);
    }

    /**
     * Get PageSpeed data
     */
    public function get_pagespeed($request) {
        $url = $request->get_param('url');
        $strategy = $request->get_param('strategy');

        $analyzer = OneClick_SEO_Pro_Analyzer::get_instance();
        $data = $analyzer->fetch_pagespeed_data($url, $strategy);

        if (is_wp_error($data)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => $data->get_error_message(),
            ], 400);
        }

        return new WP_REST_Response([
            'success' => true,
            'data' => $data,
        ], 200);
    }

    /**
     * Get settings
     */
    public function get_settings($request) {
        $settings = [
            'general' => get_option('oneclick_seo_pro_general', []),
            'sitemap' => get_option('oneclick_seo_pro_sitemap', []),
            'schema' => get_option('oneclick_seo_pro_organization', []),
            'aeo' => get_option('oneclick_seo_pro_aeo', []),
        ];

        return new WP_REST_Response([
            'success' => true,
            'data' => $settings,
        ], 200);
    }

    /**
     * Update settings
     */
    public function update_settings($request) {
        $settings = $request->get_json_params();

        if (isset($settings['general'])) {
            update_option('oneclick_seo_pro_general', $settings['general']);
        }
        if (isset($settings['sitemap'])) {
            update_option('oneclick_seo_pro_sitemap', $settings['sitemap']);
        }
        if (isset($settings['schema'])) {
            update_option('oneclick_seo_pro_organization', $settings['schema']);
        }
        if (isset($settings['aeo'])) {
            update_option('oneclick_seo_pro_aeo', $settings['aeo']);
        }

        return new WP_REST_Response([
            'success' => true,
            'message' => __('Settings updated', 'oneclick-seo-pro'),
        ], 200);
    }

    /**
     * Save score to database
     */
    private function save_score($post_id, $result) {
        global $wpdb;

        $table = $wpdb->prefix . 'oneclick_seo_scores';
        $post = get_post($post_id);

        // Delete existing
        $wpdb->delete($table, ['post_id' => $post_id]);

        // Insert new
        $wpdb->insert($table, [
            'post_id' => $post_id,
            'post_type' => $post->post_type,
            'overall_score' => $result['score']['overall'] ?? 0,
            'grade' => $result['score']['grade'] ?? 'F',
            'module_scores' => json_encode($result['score']['modules'] ?? []),
            'issues' => json_encode($result['issues'] ?? []),
            'analyzed_at' => current_time('mysql'),
        ]);

        // Update post meta
        update_post_meta($post_id, '_oneclick_seo_pro_score', $result['score']['overall'] ?? 0);
        update_post_meta($post_id, '_oneclick_seo_pro_grade', $result['score']['grade'] ?? 'F');
        update_post_meta($post_id, '_oneclick_seo_pro_analyzed', current_time('mysql'));

        // Save issues
        if (!empty($result['issues'])) {
            $this->save_issues($post_id, $result['issues']);
        }
    }

    /**
     * Save issues to database
     */
    private function save_issues($post_id, $issues) {
        global $wpdb;

        $table = $wpdb->prefix . 'oneclick_seo_issues';

        // Mark old issues as resolved
        $wpdb->update($table, ['status' => 'resolved'], ['post_id' => $post_id, 'status' => 'open']);

        // Insert new issues
        foreach ($issues as $issue) {
            $wpdb->insert($table, [
                'post_id' => $post_id,
                'issue_type' => $issue['type'] ?? 'unknown',
                'severity' => $issue['severity'] ?? 'warning',
                'message' => $issue['message'] ?? '',
                'module' => $issue['module'] ?? '',
                'status' => 'open',
                'created_at' => current_time('mysql'),
            ]);
        }
    }
}

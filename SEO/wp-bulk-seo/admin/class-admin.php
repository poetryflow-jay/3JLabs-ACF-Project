<?php
/**
 * WP Bulk SEO - Admin Class
 *
 * Main admin class handling menus, pages, and settings.
 *
 * @package WP_Bulk_SEO
 * @subpackage Admin
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Admin {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Plugin version
     */
    private $version;

    /**
     * Plugin path
     */
    private $plugin_path;

    /**
     * Plugin URL
     */
    private $plugin_url;

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
        $this->version = WP_BULK_SEO_VERSION ?? '2.0.0';
        $this->plugin_path = plugin_dir_path(dirname(__FILE__));
        $this->plugin_url = plugin_dir_url(dirname(__FILE__));

        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_init', [$this, 'register_settings']);

        // AJAX handlers
        add_action('wp_ajax_wp_bulk_seo_analyze', [$this, 'ajax_analyze']);
        add_action('wp_ajax_wp_bulk_seo_bulk_analyze', [$this, 'ajax_bulk_analyze']);
        add_action('wp_ajax_wp_bulk_seo_optimize', [$this, 'ajax_optimize']);
        add_action('wp_ajax_wp_bulk_seo_get_stats', [$this, 'ajax_get_stats']);
        add_action('wp_ajax_wp_bulk_seo_auto_optimize', [$this, 'ajax_auto_optimize']);
        add_action('wp_ajax_wp_bulk_seo_dismiss_notification', [$this, 'ajax_dismiss_notification']);
        add_action('wp_ajax_wp_bulk_seo_get_notifications', [$this, 'ajax_get_notifications']);

        // [v2.1.0] Keyword AJAX handlers
        add_action('wp_ajax_wp_bulk_seo_extract_keywords', [$this, 'ajax_extract_keywords']);
        add_action('wp_ajax_wp_bulk_seo_analyze_keyword', [$this, 'ajax_analyze_keyword']);
        add_action('wp_ajax_wp_bulk_seo_recommend_keywords', [$this, 'ajax_recommend_keywords']);
        add_action('wp_ajax_wp_bulk_seo_get_content_suggestions', [$this, 'ajax_get_content_suggestions']);

        // Dashboard widget
        add_action('wp_dashboard_setup', [$this, 'add_dashboard_widget']);

        // [v2.0.0] Handle OAuth callback
        add_action('admin_init', [$this, 'handle_gsc_oauth_callback']);
        add_action('admin_init', [$this, 'handle_gsc_disconnect']);

        // [v2.1.0] Add Live Editor meta box
        add_action('add_meta_boxes', [$this, 'add_live_editor_meta_box']);
    }

    /**
     * Handle Google Search Console OAuth callback
     * [v2.0.0]
     */
    public function handle_gsc_oauth_callback() {
        if (!isset($_GET['page']) || $_GET['page'] !== 'wp-bulk-seo-settings') {
            return;
        }

        if (!isset($_GET['code'])) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        if (!class_exists('WP_Bulk_SEO_Google_Search_Console')) {
            return;
        }

        $code = sanitize_text_field($_GET['code']);
        $gsc = WP_Bulk_SEO_Google_Search_Console::instance();

        if ($gsc->handle_oauth_callback($code)) {
            wp_redirect(admin_url('admin.php?page=wp-bulk-seo-settings&tab=search_console&connected=1'));
            exit;
        } else {
            wp_redirect(admin_url('admin.php?page=wp-bulk-seo-settings&tab=search_console&error=1'));
            exit;
        }
    }

    /**
     * Handle Google Search Console disconnect
     * [v2.0.0]
     */
    public function handle_gsc_disconnect() {
        if (!isset($_POST['action']) || $_POST['action'] !== 'disconnect_gsc') {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        if (!isset($_POST['disconnect_nonce']) || !wp_verify_nonce($_POST['disconnect_nonce'], 'wp_bulk_seo_disconnect_gsc')) {
            return;
        }

        if (!class_exists('WP_Bulk_SEO_Google_Search_Console')) {
            return;
        }

        $gsc = WP_Bulk_SEO_Google_Search_Console::instance();
        $gsc->disconnect();

        wp_redirect(admin_url('admin.php?page=wp-bulk-seo-settings&tab=search_console&disconnected=1'));
        exit;
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('WP Bulk SEO', 'wp-bulk-seo'),
            __('Bulk SEO', 'wp-bulk-seo'),
            'manage_options',
            'wp-bulk-seo',
            [$this, 'render_dashboard_page'],
            'dashicons-chart-line',
            30
        );

        // Dashboard submenu
        add_submenu_page(
            'wp-bulk-seo',
            __('Dashboard', 'wp-bulk-seo'),
            __('Dashboard', 'wp-bulk-seo'),
            'manage_options',
            'wp-bulk-seo',
            [$this, 'render_dashboard_page']
        );

        // Analyzer submenu
        add_submenu_page(
            'wp-bulk-seo',
            __('SEO Analyzer', 'wp-bulk-seo'),
            __('Analyzer', 'wp-bulk-seo'),
            'manage_options',
            'wp-bulk-seo-analyzer',
            [$this, 'render_analyzer_page']
        );

        // Bulk Optimizer submenu
        add_submenu_page(
            'wp-bulk-seo',
            __('Bulk Optimizer', 'wp-bulk-seo'),
            __('Bulk Optimizer', 'wp-bulk-seo'),
            'manage_options',
            'wp-bulk-seo-optimizer',
            [$this, 'render_optimizer_page']
        );

        // AEO submenu
        add_submenu_page(
            'wp-bulk-seo',
            __('AEO', 'wp-bulk-seo'),
            __('AEO (AI SEO)', 'wp-bulk-seo'),
            'manage_options',
            'wp-bulk-seo-aeo',
            [$this, 'render_aeo_page']
        );

        // [v2.1.0] Keywords submenu
        add_submenu_page(
            'wp-bulk-seo',
            __('Keywords', 'wp-bulk-seo'),
            __('Keywords', 'wp-bulk-seo'),
            'manage_options',
            'wp-bulk-seo-keywords',
            [$this, 'render_keywords_page']
        );

        // Settings submenu
        add_submenu_page(
            'wp-bulk-seo',
            __('Settings', 'wp-bulk-seo'),
            __('Settings', 'wp-bulk-seo'),
            'manage_options',
            'wp-bulk-seo-settings',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'wp-bulk-seo') === false) {
            return;
        }

        // CSS
        wp_enqueue_style(
            'wp-bulk-seo-admin',
            $this->plugin_url . 'assets/css/admin.css',
            [],
            $this->version
        );

        // Chart.js for dashboards
        wp_enqueue_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
            [],
            '4.4.1',
            true
        );

        // Admin JS
        wp_enqueue_script(
            'wp-bulk-seo-admin',
            $this->plugin_url . 'assets/js/admin.js',
            ['jquery', 'chartjs'],
            $this->version,
            true
        );

        // [v2.1.0] Live Editor JS (for post editor)
        if ($hook === 'post.php' || $hook === 'post-new.php') {
            wp_enqueue_script(
                'wp-bulk-seo-live-editor',
                $this->plugin_url . 'assets/js/live-editor.js',
                ['jquery'],
                $this->version,
                true
            );

            wp_localize_script('wp-bulk-seo-live-editor', 'wpBulkSeoLive', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wp_bulk_seo_live_edit'),
            ]);
        }

        // Localize script
        wp_localize_script('wp-bulk-seo-admin', 'wpBulkSeo', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_bulk_seo_nonce'),
            'strings' => [
                'analyzing' => __('Analyzing...', 'wp-bulk-seo'),
                'optimizing' => __('Optimizing...', 'wp-bulk-seo'),
                'success' => __('Success!', 'wp-bulk-seo'),
                'error' => __('Error occurred', 'wp-bulk-seo'),
                'confirm_bulk' => __('Are you sure you want to bulk optimize selected items?', 'wp-bulk-seo'),
            ],
        ]);
    }

    /**
     * Register settings
     */
    public function register_settings() {
        // General settings
        register_setting('wp_bulk_seo_general', 'wp_bulk_seo_general', [
            'sanitize_callback' => [$this, 'sanitize_general_settings'],
        ]);

        // API settings
        register_setting('wp_bulk_seo_api', 'wp_bulk_seo_pagespeed_api_key');
        register_setting('wp_bulk_seo_api', 'wp_bulk_seo_openai_api_key');
        register_setting('wp_bulk_seo_api', 'wp_bulk_seo_anthropic_api_key');

        // Sitemap settings
        register_setting('wp_bulk_seo_sitemap', 'wp_bulk_seo_sitemap');

        // Schema settings
        register_setting('wp_bulk_seo_schema', 'wp_bulk_seo_organization');

        // AEO settings
        register_setting('wp_bulk_seo_aeo', 'wp_bulk_seo_aeo');
    }

    /**
     * Sanitize general settings
     */
    public function sanitize_general_settings($input) {
        $sanitized = [];

        $sanitized['post_types'] = isset($input['post_types']) ? array_map('sanitize_key', $input['post_types']) : [];
        $sanitized['auto_analyze'] = isset($input['auto_analyze']) ? (bool) $input['auto_analyze'] : false;
        $sanitized['score_in_list'] = isset($input['score_in_list']) ? (bool) $input['score_in_list'] : true;

        return $sanitized;
    }

    /**
     * Render dashboard page
     */
    public function render_dashboard_page() {
        $stats = $this->get_seo_stats();
        include $this->plugin_path . 'admin/views/dashboard.php';
    }

    /**
     * Render analyzer page
     */
    public function render_analyzer_page() {
        $post_types = get_post_types(['public' => true], 'objects');
        include $this->plugin_path . 'admin/views/analyzer.php';
    }

    /**
     * Render optimizer page
     */
    public function render_optimizer_page() {
        $posts = $this->get_posts_for_optimization();
        include $this->plugin_path . 'admin/views/optimizer.php';
    }

    /**
     * Render AEO page
     */
    public function render_aeo_page() {
        include $this->plugin_path . 'admin/views/aeo.php';
    }

    /**
     * Render Keywords page
     * [v2.1.0]
     */
    public function render_keywords_page() {
        include $this->plugin_path . 'admin/views/keywords.php';
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
        include $this->plugin_path . 'admin/views/settings.php';
    }

    /**
     * Get SEO stats
     */
    private function get_seo_stats() {
        global $wpdb;

        $table_scores = $wpdb->prefix . 'wp_bulk_seo_scores';
        $table_issues = $wpdb->prefix . 'wp_bulk_seo_issues';

        // Check if tables exist
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_scores'") === $table_scores;

        $stats = [
            'total_posts' => wp_count_posts('post')->publish + wp_count_posts('page')->publish,
            'analyzed_posts' => 0,
            'average_score' => 0,
            'grade_distribution' => [
                'A+' => 0, 'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0,
            ],
            'issues_count' => 0,
            'critical_issues' => 0,
            'top_issues' => [],
            'recent_scores' => [],
        ];

        if (!$table_exists) {
            return $stats;
        }

        // Analyzed posts count
        $stats['analyzed_posts'] = (int) $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM $table_scores");

        // Average score
        $stats['average_score'] = (float) $wpdb->get_var("SELECT AVG(overall_score) FROM $table_scores");

        // Grade distribution
        $grades = $wpdb->get_results(
            "SELECT grade, COUNT(*) as count FROM $table_scores GROUP BY grade",
            ARRAY_A
        );

        foreach ($grades as $grade) {
            if (isset($stats['grade_distribution'][$grade['grade']])) {
                $stats['grade_distribution'][$grade['grade']] = (int) $grade['count'];
            }
        }

        // Issues count
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_issues'") === $table_issues) {
            $stats['issues_count'] = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_issues WHERE status = 'open'");
            $stats['critical_issues'] = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_issues WHERE status = 'open' AND priority = 'critical'");

            // Top issues
            $stats['top_issues'] = $wpdb->get_results(
                "SELECT issue_type, COUNT(*) as count FROM $table_issues WHERE status = 'open' GROUP BY issue_type ORDER BY count DESC LIMIT 10",
                ARRAY_A
            );
        }

        // Recent scores
        $stats['recent_scores'] = $wpdb->get_results(
            "SELECT s.post_id, p.post_title, s.overall_score, s.grade, s.analyzed_at
             FROM $table_scores s
             LEFT JOIN {$wpdb->posts} p ON s.post_id = p.ID
             ORDER BY s.analyzed_at DESC
             LIMIT 10",
            ARRAY_A
        );

        return $stats;
    }

    /**
     * Get posts for optimization
     */
    private function get_posts_for_optimization() {
        global $wpdb;

        $table_scores = $wpdb->prefix . 'wp_bulk_seo_scores';
        $per_page = 20;
        $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($paged - 1) * $per_page;

        // Get filter parameters
        $post_type = isset($_GET['post_type']) ? sanitize_key($_GET['post_type']) : 'all';
        $grade_filter = isset($_GET['grade']) ? sanitize_key($_GET['grade']) : 'all';
        $order_by = isset($_GET['orderby']) ? sanitize_key($_GET['orderby']) : 'score';
        $order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

        // Build query
        $where = ["p.post_status = 'publish'"];

        if ($post_type !== 'all') {
            $where[] = $wpdb->prepare("p.post_type = %s", $post_type);
        } else {
            $where[] = "p.post_type IN ('post', 'page', 'product')";
        }

        if ($grade_filter !== 'all') {
            $where[] = $wpdb->prepare("s.grade = %s", strtoupper($grade_filter));
        }

        $where_clause = implode(' AND ', $where);

        // Order clause
        $order_field = 's.overall_score';
        if ($order_by === 'date') {
            $order_field = 'p.post_date';
        } elseif ($order_by === 'title') {
            $order_field = 'p.post_title';
        }

        $sql = "SELECT p.ID, p.post_title, p.post_type, p.post_date,
                       s.overall_score, s.grade, s.analyzed_at
                FROM {$wpdb->posts} p
                LEFT JOIN $table_scores s ON p.ID = s.post_id
                WHERE $where_clause
                ORDER BY $order_field $order
                LIMIT %d OFFSET %d";

        $posts = $wpdb->get_results($wpdb->prepare($sql, $per_page, $offset));

        // Get total count
        $count_sql = "SELECT COUNT(*)
                      FROM {$wpdb->posts} p
                      LEFT JOIN $table_scores s ON p.ID = s.post_id
                      WHERE $where_clause";
        $total = $wpdb->get_var($count_sql);

        return [
            'posts' => $posts,
            'total' => $total,
            'per_page' => $per_page,
            'paged' => $paged,
            'total_pages' => ceil($total / $per_page),
        ];
    }

    /**
     * AJAX: Analyze single post
     */
    public function ajax_analyze() {
        check_ajax_referer('wp_bulk_seo_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

        if (!$post_id) {
            wp_send_json_error(['message' => 'Invalid post ID']);
        }

        // Get analyzer and scorer
        if (!class_exists('WP_Bulk_SEO_Analyzer')) {
            require_once $this->plugin_path . 'includes/algorithm/class-seo-analyzer.php';
        }
        if (!class_exists('WP_Bulk_SEO_Scorer')) {
            require_once $this->plugin_path . 'includes/algorithm/class-seo-scorer.php';
        }

        $analyzer = new WP_Bulk_SEO_Analyzer();
        $scorer = new WP_Bulk_SEO_Scorer();

        // Analyze
        $page_data = $analyzer->analyze_post($post_id);
        $score = $scorer->calculate_score($page_data);

        // Save score
        $this->save_score($post_id, $score);

        // [v2.0.0] Trigger score saved event for monitoring
        do_action('wp_bulk_seo_score_saved', $post_id, $score);

        wp_send_json_success([
            'post_id' => $post_id,
            'score' => $score['overall_score'],
            'grade' => $score['grade'],
            'recommendations' => array_slice($score['recommendations'], 0, 5),
        ]);
    }

    /**
     * AJAX: Bulk analyze
     */
    public function ajax_bulk_analyze() {
        check_ajax_referer('wp_bulk_seo_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $post_ids = isset($_POST['post_ids']) ? array_map('intval', (array) $_POST['post_ids']) : [];

        if (empty($post_ids)) {
            wp_send_json_error(['message' => 'No posts selected']);
        }

        // Limit batch size
        $post_ids = array_slice($post_ids, 0, 10);

        // Get analyzer and scorer
        if (!class_exists('WP_Bulk_SEO_Analyzer')) {
            require_once $this->plugin_path . 'includes/algorithm/class-seo-analyzer.php';
        }
        if (!class_exists('WP_Bulk_SEO_Scorer')) {
            require_once $this->plugin_path . 'includes/algorithm/class-seo-scorer.php';
        }

        $analyzer = new WP_Bulk_SEO_Analyzer();
        $scorer = new WP_Bulk_SEO_Scorer();

        $results = [];

        foreach ($post_ids as $post_id) {
            $page_data = $analyzer->analyze_post($post_id);
            $score = $scorer->calculate_score($page_data);
            $this->save_score($post_id, $score);

            $results[] = [
                'post_id' => $post_id,
                'title' => get_the_title($post_id),
                'score' => $score['overall_score'],
                'grade' => $score['grade'],
            ];
        }

        wp_send_json_success([
            'analyzed' => count($results),
            'results' => $results,
        ]);
    }

    /**
     * AJAX: Get stats
     */
    public function ajax_get_stats() {
        check_ajax_referer('wp_bulk_seo_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $stats = $this->get_seo_stats();
        wp_send_json_success($stats);
    }

    /**
     * AJAX: Optimize
     */
    public function ajax_optimize() {
        check_ajax_referer('wp_bulk_seo_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $optimize_type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'all';

        if (!$post_id) {
            wp_send_json_error(['message' => 'Invalid post ID']);
        }

        // Get AI engine
        if (!class_exists('WP_Bulk_SEO_AI_Engine')) {
            require_once $this->plugin_path . 'includes/class-ai-engine.php';
        }

        $ai = new WP_Bulk_SEO_AI_Engine();
        $post = get_post($post_id);

        if (!$post) {
            wp_send_json_error(['message' => 'Post not found']);
        }

        $optimizations = [];

        // Optimize title
        if ($optimize_type === 'all' || $optimize_type === 'title') {
            $new_title = $ai->optimize_title(get_the_title($post), $post->post_content);
            if ($new_title) {
                $optimizations['title'] = [
                    'original' => get_the_title($post),
                    'optimized' => $new_title,
                ];
            }
        }

        // Optimize meta description
        if ($optimize_type === 'all' || $optimize_type === 'meta') {
            $current_desc = get_post_meta($post_id, '_yoast_wpseo_metadesc', true) ?:
                            get_post_meta($post_id, 'rank_math_description', true) ?:
                            get_the_excerpt($post);

            $new_desc = $ai->optimize_meta_description($current_desc, $post->post_content);
            if ($new_desc) {
                $optimizations['meta_description'] = [
                    'original' => $current_desc,
                    'optimized' => $new_desc,
                ];
            }
        }

        wp_send_json_success([
            'post_id' => $post_id,
            'optimizations' => $optimizations,
        ]);
    }

    /**
     * Save score to database
     */
    private function save_score($post_id, $score) {
        global $wpdb;

        $table = $wpdb->prefix . 'wp_bulk_seo_scores';

        // Check if score exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE post_id = %d",
            $post_id
        ));

        $data = [
            'post_id' => $post_id,
            'overall_score' => $score['overall_score'],
            'grade' => $score['grade'],
            'modules' => wp_json_encode($score['modules']),
            'recommendations' => wp_json_encode($score['recommendations']),
            'analyzed_at' => current_time('mysql'),
        ];

        if ($existing) {
            $wpdb->update($table, $data, ['id' => $existing]);
        } else {
            $wpdb->insert($table, $data);
        }
    }

    /**
     * Add dashboard widget
     */
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'wp_bulk_seo_dashboard_widget',
            __('SEO Overview', 'wp-bulk-seo'),
            [$this, 'render_dashboard_widget']
        );
    }

    /**
     * AJAX: Auto optimize
     * [v2.0.0]
     */
    public function ajax_auto_optimize() {
        check_ajax_referer('wp_bulk_seo_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $options = isset($_POST['options']) ? $_POST['options'] : [];

        if (!$post_id) {
            wp_send_json_error(['message' => 'Invalid post ID']);
        }

        $auto_optimizer = WP_Bulk_SEO_Auto_Optimizer::instance();
        $result = $auto_optimizer->auto_optimize($post_id, $options);

        wp_send_json_success($result);
    }

    /**
     * AJAX: Dismiss notification
     * [v2.0.0]
     */
    public function ajax_dismiss_notification() {
        check_ajax_referer('wp_bulk_seo_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $notification_id = isset($_POST['notification_id']) ? intval($_POST['notification_id']) : 0;

        if (!$notification_id) {
            wp_send_json_error(['message' => 'Invalid notification ID']);
        }

        $monitor = WP_Bulk_SEO_Realtime_Monitor::instance();
        $monitor->mark_notification_read($notification_id);

        wp_send_json_success(['message' => 'Notification dismissed']);
    }

    /**
     * AJAX: Get notifications
     * [v2.0.0]
     */
    public function ajax_get_notifications() {
        check_ajax_referer('wp_bulk_seo_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
        $monitor = WP_Bulk_SEO_Realtime_Monitor::instance();
        $notifications = $monitor->get_notifications($limit);

        wp_send_json_success(['notifications' => $notifications]);
    }

    /**
     * Add Live Editor meta box
     * [v2.1.0]
     */
    public function add_live_editor_meta_box() {
        $post_types = get_option('wp_bulk_seo_general', [])['post_types'] ?? ['post', 'page'];
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'wp-bulk-seo-live-editor',
                __('⚡ Live SEO Editor', 'wp-bulk-seo') . ' <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 5px;">Alli AI Style</span>',
                [$this, 'render_live_editor_meta_box'],
                $post_type,
                'normal',
                'high'
            );
        }
    }

    /**
     * Render Live Editor meta box
     * [v2.1.0]
     */
    public function render_live_editor_meta_box($post) {
        $seo_title = get_post_meta($post->ID, '_wp_bulk_seo_title', true);
        $seo_desc = get_post_meta($post->ID, '_wp_bulk_seo_description', true);
        $focus_keyword = get_post_meta($post->ID, '_wp_bulk_seo_focus_keyword', true);

        // Get current score
        global $wpdb;
        $table = $wpdb->prefix . 'wp_bulk_seo_scores';
        $score_data = $wpdb->get_row($wpdb->prepare(
            "SELECT overall_score, grade FROM $table WHERE post_id = %d",
            $post->ID
        ), ARRAY_A);

        $current_score = $score_data['overall_score'] ?? null;
        $current_grade = $score_data['grade'] ?? 'N/A';
        ?>
        <div id="wp-bulk-seo-live-editor" style="padding: 20px;">
            <!-- Score Badge -->
            <div style="
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #fff;
                padding: 15px 20px;
                border-radius: 8px;
                margin-bottom: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            ">
                <div>
                    <div style="font-size: 12px; opacity: 0.9;">현재 SEO 점수</div>
                    <div style="font-size: 32px; font-weight: 700;" id="wp-bulk-seo-live-score">
                        <?php echo $current_score !== null ? esc_html($current_score) : 'N/A'; ?>
                        <span style="font-size: 18px;">(<?php echo esc_html($current_grade); ?>)</span>
                    </div>
                </div>
                <button type="button" class="button button-secondary" id="refresh-seo-score" style="background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.3); color: #fff;">
                    <?php esc_html_e('🔄 재분석', 'wp-bulk-seo'); ?>
                </button>
            </div>

            <!-- SERP Preview -->
            <div id="wp-bulk-seo-serp-preview" style="
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
            ">
                <h3 style="margin-top: 0;"><?php esc_html_e('Google 검색 결과 미리보기', 'wp-bulk-seo'); ?></h3>
                <div class="serp-preview" style="
                    border: 1px solid #e0e0e0;
                    border-radius: 4px;
                    padding: 15px;
                    background: #f9f9f9;
                ">
                    <div class="serp-url" style="
                        color: #006621;
                        font-size: 14px;
                        margin-bottom: 5px;
                    "><?php echo esc_html(str_replace(['http://', 'https://'], '', get_permalink($post->ID))); ?></div>
                    <div class="serp-title" style="
                        color: #1a0dab;
                        font-size: 20px;
                        font-weight: 400;
                        margin-bottom: 5px;
                        cursor: pointer;
                    "><?php echo esc_html($seo_title ?: $post->post_title); ?></div>
                    <div class="serp-description" style="
                        color: #545454;
                        font-size: 14px;
                        line-height: 1.4;
                    "><?php echo esc_html($seo_desc ?: wp_trim_words($post->post_content, 30)); ?></div>
                </div>
            </div>

            <!-- Live Editor Fields -->
            <div style="display: grid; gap: 20px;">
                <!-- SEO Title -->
                <div>
                    <label for="wp-bulk-seo-live-title" style="display: block; font-weight: 600; margin-bottom: 8px;">
                        <?php esc_html_e('SEO 제목', 'wp-bulk-seo'); ?>
                        <span id="title-length-indicator" style="font-size: 12px; color: #666; margin-left: 10px;">
                            <?php echo mb_strlen($seo_title ?: $post->post_title); ?>/60
                        </span>
                    </label>
                    <input type="text" 
                           id="wp-bulk-seo-live-title" 
                           class="large-text" 
                           value="<?php echo esc_attr($seo_title ?: $post->post_title); ?>"
                           placeholder="<?php esc_attr_e('SEO 최적화된 제목을 입력하세요', 'wp-bulk-seo'); ?>"
                           style="padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 16px;">
                    <p class="description">
                        <?php esc_html_e('검색 결과에 표시될 제목입니다. 50-60자가 권장됩니다.', 'wp-bulk-seo'); ?>
                    </p>
                </div>

                <!-- Meta Description -->
                <div>
                    <label for="wp-bulk-seo-live-description" style="display: block; font-weight: 600; margin-bottom: 8px;">
                        <?php esc_html_e('메타 설명', 'wp-bulk-seo'); ?>
                        <span id="description-length-indicator" style="font-size: 12px; color: #666; margin-left: 10px;">
                            <?php echo mb_strlen($seo_desc ?: wp_trim_words($post->post_content, 30)); ?>/160
                        </span>
                    </label>
                    <textarea id="wp-bulk-seo-live-description" 
                              rows="3" 
                              class="large-text"
                              placeholder="<?php esc_attr_e('검색 결과에 표시될 설명을 입력하세요', 'wp-bulk-seo'); ?>"
                              style="padding: 10px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;"><?php echo esc_textarea($seo_desc ?: wp_trim_words($post->post_content, 30)); ?></textarea>
                    <p class="description">
                        <?php esc_html_e('검색 결과에 표시될 설명입니다. 150-160자가 권장됩니다.', 'wp-bulk-seo'); ?>
                    </p>
                </div>

                <!-- Focus Keyword -->
                <div>
                    <label for="wp-bulk-seo-live-keyword" style="display: block; font-weight: 600; margin-bottom: 8px;">
                        <?php esc_html_e('포커스 키워드', 'wp-bulk-seo'); ?>
                    </label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" 
                               id="wp-bulk-seo-live-keyword" 
                               class="regular-text" 
                               value="<?php echo esc_attr($focus_keyword); ?>"
                               placeholder="<?php esc_attr_e('예: WordPress SEO', 'wp-bulk-seo'); ?>"
                               style="flex: 1; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
                        <button type="button" class="button" id="extract-keyword-from-content">
                            <?php esc_html_e('📊 콘텐츠에서 추출', 'wp-bulk-seo'); ?>
                        </button>
                        <button type="button" class="button" id="recommend-keywords">
                            <?php esc_html_e('💡 추천 받기', 'wp-bulk-seo'); ?>
                        </button>
                    </div>
                    <p class="description">
                        <?php esc_html_e('이 포스트의 주요 키워드를 입력하거나 자동으로 추출/추천받으세요.', 'wp-bulk-seo'); ?>
                    </p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #ddd;
                display: flex;
                gap: 10px;
            ">
                <button type="button" class="button button-primary" id="auto-optimize-live">
                    <?php esc_html_e('⚡ 자동 최적화', 'wp-bulk-seo'); ?>
                </button>
                <button type="button" class="button" id="analyze-live">
                    <?php esc_html_e('📊 분석', 'wp-bulk-seo'); ?>
                </button>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var postId = <?php echo $post->ID; ?>;

            // Refresh score
            $('#refresh-seo-score').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true).text('<?php esc_html_e('분석 중...', 'wp-bulk-seo'); ?>');

                $.post(wpBulkSeo.ajaxUrl, {
                    action: 'wp_bulk_seo_analyze',
                    nonce: wpBulkSeo.nonce,
                    post_id: postId
                }).done(function(response) {
                    if (response.success) {
                        $('#wp-bulk-seo-live-score').text(response.data.score + ' (' + response.data.grade + ')');
                        location.reload();
                    }
                }).always(function() {
                    $btn.prop('disabled', false).text('<?php esc_html_e('🔄 재분석', 'wp-bulk-seo'); ?>');
                });
            });

            // Extract keyword from content
            $('#extract-keyword-from-content').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true).text('<?php esc_html_e('추출 중...', 'wp-bulk-seo'); ?>');

                $.post(wpBulkSeo.ajaxUrl, {
                    action: 'wp_bulk_seo_get_content_suggestions',
                    nonce: wpBulkSeo.nonce,
                    post_id: postId
                }).done(function(response) {
                    if (response.success && response.data.primary_keyword) {
                        $('#wp-bulk-seo-live-keyword').val(response.data.primary_keyword);
                    }
                }).always(function() {
                    $btn.prop('disabled', false).text('<?php esc_html_e('📊 콘텐츠에서 추출', 'wp-bulk-seo'); ?>');
                });
            });

            // Recommend keywords
            $('#recommend-keywords').on('click', function() {
                var keyword = $('#wp-bulk-seo-live-keyword').val();
                if (!keyword) {
                    alert('<?php esc_html_e('먼저 키워드를 입력하세요.', 'wp-bulk-seo'); ?>');
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).text('<?php esc_html_e('추천 중...', 'wp-bulk-seo'); ?>');

                $.post(wpBulkSeo.ajaxUrl, {
                    action: 'wp_bulk_seo_recommend_keywords',
                    nonce: wpBulkSeo.nonce,
                    keyword: keyword
                }).done(function(response) {
                    if (response.success && response.data.recommendations.length > 0) {
                        var suggestions = response.data.recommendations.slice(0, 3).map(function(r) {
                            return r.keyword;
                        }).join(', ');
                        alert('<?php esc_html_e('추천 키워드:', 'wp-bulk-seo'); ?>\n' + suggestions);
                    }
                }).always(function() {
                    $btn.prop('disabled', false).text('<?php esc_html_e('💡 추천 받기', 'wp-bulk-seo'); ?>');
                });
            });

            // Auto optimize
            $('#auto-optimize-live').on('click', function() {
                if (!confirm('<?php esc_html_e('이 포스트를 자동으로 최적화하시겠습니까?', 'wp-bulk-seo'); ?>')) {
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).text('<?php esc_html_e('최적화 중...', 'wp-bulk-seo'); ?>');

                $.post(wpBulkSeo.ajaxUrl, {
                    action: 'wp_bulk_seo_auto_optimize',
                    nonce: wpBulkSeo.nonce,
                    post_id: postId,
                    options: {
                        optimize_title: true,
                        optimize_meta: true,
                        add_schema: true,
                        optimize_keywords: true,
                        use_ai: true
                    }
                }).done(function(response) {
                    if (response.success) {
                        alert('<?php esc_html_e('최적화 완료! 점수:', 'wp-bulk-seo'); ?> ' + response.data.score_before + ' → ' + response.data.score_after);
                        location.reload();
                    }
                }).always(function() {
                    $btn.prop('disabled', false).text('<?php esc_html_e('⚡ 자동 최적화', 'wp-bulk-seo'); ?>');
                });
            });

            // Analyze
            $('#analyze-live').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true).text('<?php esc_html_e('분석 중...', 'wp-bulk-seo'); ?>');

                $.post(wpBulkSeo.ajaxUrl, {
                    action: 'wp_bulk_seo_analyze',
                    nonce: wpBulkSeo.nonce,
                    post_id: postId
                }).done(function(response) {
                    if (response.success) {
                        $('#wp-bulk-seo-live-score').text(response.data.score + ' (' + response.data.grade + ')');
                    }
                }).always(function() {
                    $btn.prop('disabled', false).text('<?php esc_html_e('📊 분석', 'wp-bulk-seo'); ?>');
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Render dashboard widget
     */
    public function render_dashboard_widget() {
        $stats = $this->get_seo_stats();
        ?>
        <div class="wp-bulk-seo-widget">
            <div class="seo-stats-grid">
                <div class="stat-item">
                    <span class="stat-value"><?php echo esc_html($stats['analyzed_posts']); ?></span>
                    <span class="stat-label"><?php esc_html_e('Analyzed', 'wp-bulk-seo'); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo esc_html(round($stats['average_score'])); ?></span>
                    <span class="stat-label"><?php esc_html_e('Avg Score', 'wp-bulk-seo'); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo esc_html($stats['issues_count']); ?></span>
                    <span class="stat-label"><?php esc_html_e('Issues', 'wp-bulk-seo'); ?></span>
                </div>
            </div>

            <?php if ($stats['critical_issues'] > 0): ?>
            <div class="seo-alert">
                <span class="dashicons dashicons-warning"></span>
                <?php printf(
                    esc_html(_n('%d critical issue needs attention', '%d critical issues need attention', $stats['critical_issues'], 'wp-bulk-seo')),
                    $stats['critical_issues']
                ); ?>
            </div>
            <?php endif; ?>

            <p class="widget-footer">
                <a href="<?php echo esc_url(admin_url('admin.php?page=wp-bulk-seo')); ?>">
                    <?php esc_html_e('View Full Dashboard', 'wp-bulk-seo'); ?> &rarr;
                </a>
            </p>
        </div>

        <style>
            .wp-bulk-seo-widget .seo-stats-grid { display: flex; gap: 15px; margin-bottom: 15px; }
            .wp-bulk-seo-widget .stat-item { flex: 1; text-align: center; padding: 10px; background: #f9f9f9; border-radius: 4px; }
            .wp-bulk-seo-widget .stat-value { display: block; font-size: 24px; font-weight: 600; color: #1e3a5f; }
            .wp-bulk-seo-widget .stat-label { font-size: 12px; color: #666; }
            .wp-bulk-seo-widget .seo-alert { background: #fef7e1; border-left: 3px solid #ffb900; padding: 10px; margin-bottom: 15px; }
            .wp-bulk-seo-widget .seo-alert .dashicons { color: #ffb900; margin-right: 5px; }
            .wp-bulk-seo-widget .widget-footer { text-align: right; margin: 0; }
        </style>
        <?php
    }
}

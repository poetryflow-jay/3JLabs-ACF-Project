<?php
/**
 * WP Bulk SEO - Admin Class
 *
 * Main admin class handling menus, pages, and settings.
 *
 * @package OneClick_SEO_Pro
 * @subpackage Admin
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class OneClick_SEO_Pro_Admin {

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
        // [v2.1.1] defined() 체크로 상수 미정의 에러 방지
        $this->version = defined('ONECLICK_SEO_PRO_VERSION') ? ONECLICK_SEO_PRO_VERSION : 
                        (defined('WP_BULK_SEO_VERSION') ? WP_BULK_SEO_VERSION : '2.0.0');
        $this->plugin_path = plugin_dir_path(dirname(__FILE__));
        $this->plugin_url = plugin_dir_url(dirname(__FILE__));

        $this->init_hooks();
    }

    /**
     * Initialize hooks
     * [v2.1.3] 메뉴 등록은 메인 플러그인 파일(oneclick-seo-pro.php)에서 처리
     * add_admin_menu 제거하여 중복 메뉴 방지
     */
    private function init_hooks() {
        // [v2.1.3] 메뉴는 메인 플러그인에서 등록하므로 여기서 제거
        // add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_init', [$this, 'register_settings']);

        // AJAX handlers
        add_action('wp_ajax_oneclick_seo_pro_analyze', [$this, 'ajax_analyze']);
        add_action('wp_ajax_oneclick_seo_pro_bulk_analyze', [$this, 'ajax_bulk_analyze']);
        add_action('wp_ajax_oneclick_seo_pro_optimize', [$this, 'ajax_optimize']);
        add_action('wp_ajax_oneclick_seo_pro_get_stats', [$this, 'ajax_get_stats']);

        // Dashboard widget
        add_action('wp_dashboard_setup', [$this, 'add_dashboard_widget']);

        // Plugin list links
        if (defined('ONECLICK_SEO_PRO_BASENAME')) {
            add_filter('plugin_action_links_' . ONECLICK_SEO_PRO_BASENAME, [$this, 'add_plugin_action_links']);
            add_filter('plugin_row_meta', [$this, 'add_plugin_row_meta'], 10, 2);
        }
    }

    /**
     * Add plugin action links
     */
    public function add_plugin_action_links($links) {
        $custom_links = [
            '<a href="' . admin_url('admin.php?page=oneclick-seo-pro') . '">' . __('Dashboard', 'oneclick-seo-pro') . '</a>',
            '<a href="' . admin_url('admin.php?page=oneclick-seo-pro-settings') . '">' . __('Settings', 'oneclick-seo-pro') . '</a>',
        ];
        return array_merge($custom_links, $links);
    }

    /**
     * Add plugin row meta
     */
    public function add_plugin_row_meta($links, $file) {
        if (!defined('ONECLICK_SEO_PRO_BASENAME') || ONECLICK_SEO_PRO_BASENAME !== $file) {
            return $links;
        }

        $extra_links = [
            '<a href="https://3j-labs.com/docs/oneclick-seo" target="_blank">' . __('Documentation', 'oneclick-seo-pro') . '</a>',
        ];

        // Promo links
        if (!is_plugin_active('acf-user-journey-analytics/acf-user-journey-analytics.php')) {
            $extra_links[] = '<a href="https://wordpress.org/plugins/acf-user-journey-analytics/" target="_blank" style="color: #6366f1; font-weight: 600;">📊 ' . __('Free Analytics Dashboard', 'oneclick-seo-pro') . '</a>';
        }
        if (!is_plugin_active('acf-nudge-flow/acf-nudge-flow.php')) {
            $extra_links[] = '<a href="https://wordpress.org/plugins/acf-nudge-flow/" target="_blank" style="color: #22c55e; font-weight: 600;">🎯 ' . __('Marketing Automation', 'oneclick-seo-pro') . '</a>';
        }

        return array_merge($links, $extra_links);
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('WP Bulk SEO', 'oneclick-seo-pro'),
            __('Bulk SEO', 'oneclick-seo-pro'),
            'manage_options',
            'oneclick-seo-pro',
            [$this, 'render_dashboard_page'],
            'dashicons-chart-line',
            30
        );

        // Dashboard submenu
        add_submenu_page(
            'oneclick-seo-pro',
            __('Dashboard', 'oneclick-seo-pro'),
            __('Dashboard', 'oneclick-seo-pro'),
            'manage_options',
            'oneclick-seo-pro',
            [$this, 'render_dashboard_page']
        );

        // Analyzer submenu
        add_submenu_page(
            'oneclick-seo-pro',
            __('SEO Analyzer', 'oneclick-seo-pro'),
            __('Analyzer', 'oneclick-seo-pro'),
            'manage_options',
            'oneclick-seo-pro-analyzer',
            [$this, 'render_analyzer_page']
        );

        // Bulk Optimizer submenu
        add_submenu_page(
            'oneclick-seo-pro',
            __('Bulk Optimizer', 'oneclick-seo-pro'),
            __('Bulk Optimizer', 'oneclick-seo-pro'),
            'manage_options',
            'oneclick-seo-pro-optimizer',
            [$this, 'render_optimizer_page']
        );

        // AEO submenu
        add_submenu_page(
            'oneclick-seo-pro',
            __('AEO', 'oneclick-seo-pro'),
            __('AEO (AI SEO)', 'oneclick-seo-pro'),
            'manage_options',
            'oneclick-seo-pro-aeo',
            [$this, 'render_aeo_page']
        );

        // Settings submenu
        add_submenu_page(
            'oneclick-seo-pro',
            __('Settings', 'oneclick-seo-pro'),
            __('Settings', 'oneclick-seo-pro'),
            'manage_options',
            'oneclick-seo-pro-settings',
            [$this, 'render_settings_page']
        );

        // Traffic Analytics submenu (User Journey Analytics Integration)
        add_submenu_page(
            'oneclick-seo-pro',
            __('Traffic Analytics', 'oneclick-seo-pro'),
            __('Traffic Analytics', 'oneclick-seo-pro'),
            'manage_options',
            'oneclick-seo-pro-analytics',
            [$this, 'render_analytics_page']
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'oneclick-seo-pro') === false) {
            return;
        }

        // CSS
        wp_enqueue_style(
            'oneclick-seo-pro-admin',
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
            'oneclick-seo-pro-admin',
            $this->plugin_url . 'assets/js/admin.js',
            ['jquery', 'chartjs'],
            $this->version,
            true
        );

        // Localize script
        wp_localize_script('oneclick-seo-pro-admin', 'wpBulkSeo', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('oneclick_seo_pro_nonce'),
            'strings' => [
                'analyzing' => __('Analyzing...', 'oneclick-seo-pro'),
                'optimizing' => __('Optimizing...', 'oneclick-seo-pro'),
                'success' => __('Success!', 'oneclick-seo-pro'),
                'error' => __('Error occurred', 'oneclick-seo-pro'),
                'confirm_bulk' => __('Are you sure you want to bulk optimize selected items?', 'oneclick-seo-pro'),
            ],
        ]);
    }

    /**
     * Register settings
     */
    public function register_settings() {
        // General settings
        register_setting('oneclick_seo_pro_general', 'oneclick_seo_pro_general', [
            'sanitize_callback' => [$this, 'sanitize_general_settings'],
        ]);

        // API settings
        register_setting('oneclick_seo_pro_api', 'oneclick_seo_pro_pagespeed_api_key');
        register_setting('oneclick_seo_pro_api', 'oneclick_seo_pro_openai_api_key');
        register_setting('oneclick_seo_pro_api', 'oneclick_seo_pro_anthropic_api_key');

        // Sitemap settings
        register_setting('oneclick_seo_pro_sitemap', 'oneclick_seo_pro_sitemap');

        // Schema settings
        register_setting('oneclick_seo_pro_schema', 'oneclick_seo_pro_organization');

        // AEO settings
        register_setting('oneclick_seo_pro_aeo', 'oneclick_seo_pro_aeo');
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
     * Render settings page
     */
    public function render_settings_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
        include $this->plugin_path . 'admin/views/settings.php';
    }

    /**
     * Render analytics page (User Journey Analytics Integration)
     */
    public function render_analytics_page() {
        include $this->plugin_path . 'admin/views/analytics.php';
    }

    /**
     * Get SEO stats
     */
    private function get_seo_stats() {
        global $wpdb;

        $table_scores = $wpdb->prefix . 'oneclick_seo_pro_scores';
        $table_issues = $wpdb->prefix . 'oneclick_seo_pro_issues';

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

        $table_scores = $wpdb->prefix . 'oneclick_seo_pro_scores';
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
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

        if (!$post_id) {
            wp_send_json_error(['message' => 'Invalid post ID']);
        }

        // Get analyzer and scorer
        if (!class_exists('OneClick_SEO_Pro_Analyzer')) {
            require_once $this->plugin_path . 'includes/algorithm/class-seo-analyzer.php';
        }
        if (!class_exists('OneClick_SEO_Pro_Scorer')) {
            require_once $this->plugin_path . 'includes/algorithm/class-seo-scorer.php';
        }

        $analyzer = new OneClick_SEO_Pro_Analyzer();
        $scorer = new OneClick_SEO_Pro_Scorer();

        // Analyze
        $page_data = $analyzer->analyze_post($post_id);
        $score = $scorer->calculate_score($page_data);

        // Save score
        $this->save_score($post_id, $score);

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
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');

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
        if (!class_exists('OneClick_SEO_Pro_Analyzer')) {
            require_once $this->plugin_path . 'includes/algorithm/class-seo-analyzer.php';
        }
        if (!class_exists('OneClick_SEO_Pro_Scorer')) {
            require_once $this->plugin_path . 'includes/algorithm/class-seo-scorer.php';
        }

        $analyzer = new OneClick_SEO_Pro_Analyzer();
        $scorer = new OneClick_SEO_Pro_Scorer();

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
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');

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
        check_ajax_referer('oneclick_seo_pro_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $optimize_type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'all';

        if (!$post_id) {
            wp_send_json_error(['message' => 'Invalid post ID']);
        }

        // Get AI engine
        if (!class_exists('OneClick_SEO_Pro_AI_Engine')) {
            require_once $this->plugin_path . 'includes/class-ai-engine.php';
        }

        $ai = new OneClick_SEO_Pro_AI_Engine();
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

        $table = $wpdb->prefix . 'oneclick_seo_pro_scores';

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
            'oneclick_seo_pro_dashboard_widget',
            __('SEO Overview', 'oneclick-seo-pro'),
            [$this, 'render_dashboard_widget']
        );
    }

    /**
     * Render dashboard widget
     */
    public function render_dashboard_widget() {
        $stats = $this->get_seo_stats();
        ?>
        <div class="oneclick-seo-pro-widget">
            <div class="seo-stats-grid">
                <div class="stat-item">
                    <span class="stat-value"><?php echo esc_html($stats['analyzed_posts']); ?></span>
                    <span class="stat-label"><?php esc_html_e('Analyzed', 'oneclick-seo-pro'); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo esc_html(round($stats['average_score'])); ?></span>
                    <span class="stat-label"><?php esc_html_e('Avg Score', 'oneclick-seo-pro'); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo esc_html($stats['issues_count']); ?></span>
                    <span class="stat-label"><?php esc_html_e('Issues', 'oneclick-seo-pro'); ?></span>
                </div>
            </div>

            <?php if ($stats['critical_issues'] > 0): ?>
            <div class="seo-alert">
                <span class="dashicons dashicons-warning"></span>
                <?php printf(
                    esc_html(_n('%d critical issue needs attention', '%d critical issues need attention', $stats['critical_issues'], 'oneclick-seo-pro')),
                    $stats['critical_issues']
                ); ?>
            </div>
            <?php endif; ?>

            <p class="widget-footer">
                <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro')); ?>">
                    <?php esc_html_e('View Full Dashboard', 'oneclick-seo-pro'); ?> &rarr;
                </a>
            </p>
        </div>

        <style>
            .oneclick-seo-pro-widget .seo-stats-grid { display: flex; gap: 15px; margin-bottom: 15px; }
            .oneclick-seo-pro-widget .stat-item { flex: 1; text-align: center; padding: 10px; background: #f9f9f9; border-radius: 4px; }
            .oneclick-seo-pro-widget .stat-value { display: block; font-size: 24px; font-weight: 600; color: #1e3a5f; }
            .oneclick-seo-pro-widget .stat-label { font-size: 12px; color: #666; }
            .oneclick-seo-pro-widget .seo-alert { background: #fef7e1; border-left: 3px solid #ffb900; padding: 10px; margin-bottom: 15px; }
            .oneclick-seo-pro-widget .seo-alert .dashicons { color: #ffb900; margin-right: 5px; }
            .oneclick-seo-pro-widget .widget-footer { text-align: right; margin: 0; }
        </style>
        <?php
    }
}

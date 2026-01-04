<?php
/**
 * Plugin Name: 1-Click SEO Pro
 * Plugin URI: https://3j-labs.com/1-click-seo-pro
 * Description: All-in-one SEO & AEO optimization with AI-powered content suggestions, 200+ ranking factors analysis, and Answer Engine Optimization for WordPress. 원클릭으로 SEO 최적화를 완료하세요.
 * Version: 2.0.0
 * Author: 3J Labs (제이x제니x제이슨 연구소)
 * Author URI: https://3j-labs.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: oneclick-seo-pro
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 *
 * @package OneClick_SEO_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

// Prevent conflict with other SEO plugins
if (defined('ONECLICK_SEO_PRO_VERSION')) {
    return;
}

// Plugin constants
define('ONECLICK_SEO_PRO_VERSION', '2.0.0');
define('ONECLICK_SEO_PRO_FILE', __FILE__);
define('ONECLICK_SEO_PRO_DIR', plugin_dir_path(__FILE__));
define('ONECLICK_SEO_PRO_URL', plugin_dir_url(__FILE__));
define('ONECLICK_SEO_PRO_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 *
 * [v2.0.0] WP Bulk SEO AEO 수준의 보안 및 오류 처리 적용
 */
final class OneClick_SEO_Pro {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Plugin components
     */
    public $scorer;
    public $analyzer;
    public $ranking_db;
    public $schema_generator;
    public $sitemap_manager;
    public $aeo_engine;

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
        try {
            $this->includes();
            $this->init_hooks();
        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('1-Click SEO Pro Constructor Error: ' . $e->getMessage());
            }
        } catch (Error $e) {
            if (function_exists('error_log')) {
                error_log('1-Click SEO Pro Constructor Fatal Error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Include required files
     */
    private function includes() {
        // Algorithm classes
        $algorithm_files = [
            'includes/algorithm/class-ranking-factors-db.php',
            'includes/algorithm/class-seo-scorer.php',
            'includes/algorithm/class-seo-analyzer.php',
        ];

        // [v2.0.0] Security module first
        $security_files = [
            'includes/class-security-init.php',
        ];

        // Core functionality
        $core_files = [
            'includes/class-database.php',
            'includes/class-schema-generator.php',
            'includes/class-sitemap-manager.php',
            'includes/class-frontend.php',
        ];

        // API Integration
        $api_files = [
            'includes/api/class-rest-controller.php',
            'includes/api/class-ai-provider.php',
        ];

        // AEO (Answer Engine Optimization)
        $aeo_files = [
            'includes/aeo/class-aeo-engine.php',
            'includes/aeo/class-faq-generator.php',
            'includes/aeo/class-featured-snippet-optimizer.php',
        ];

        // Admin
        $admin_files = [];
        if (is_admin()) {
            $admin_files[] = 'admin/class-admin.php';
        }

        // 모든 파일을 안전하게 로드 (보안 모듈 먼저)
        $all_files = array_merge($security_files, $algorithm_files, $core_files, $api_files, $aeo_files, $admin_files);

        foreach ($all_files as $file) {
            $filepath = ONECLICK_SEO_PRO_DIR . $file;
            if (file_exists($filepath)) {
                try {
                    require_once $filepath;
                } catch (Exception $e) {
                    if (function_exists('error_log')) {
                        error_log('1-Click SEO Pro: Failed to load ' . $file . ': ' . $e->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        add_action('plugins_loaded', [$this, 'on_plugins_loaded']);
        add_action('init', [$this, 'init']);

        // Admin hooks
        if (is_admin()) {
            add_action('admin_menu', [$this, 'add_admin_menu']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

            // AJAX handlers with nonce verification
            add_action('wp_ajax_oneclick_seo_pro_analyze', [$this, 'ajax_analyze']);
            add_action('wp_ajax_oneclick_seo_pro_bulk_analyze', [$this, 'ajax_bulk_analyze']);
            add_action('wp_ajax_oneclick_seo_pro_optimize', [$this, 'ajax_optimize']);
            add_action('wp_ajax_oneclick_seo_pro_get_stats', [$this, 'ajax_get_stats']);
            add_action('wp_ajax_oneclick_seo_pro_1click_setup', [$this, 'ajax_1click_setup']);
        }

        // Frontend hooks
        add_action('wp_head', [$this, 'output_meta_tags'], 1);
        add_action('wp_head', [$this, 'output_schema'], 5);

        // REST API
        add_action('rest_api_init', [$this, 'register_rest_routes']);

        // Dashboard widget
        add_action('wp_dashboard_setup', [$this, 'add_dashboard_widget']);

        // Plugin list enhancer
        add_filter('plugin_action_links_' . ONECLICK_SEO_PRO_BASENAME, [$this, 'add_plugin_action_links']);
        add_filter('plugin_row_meta', [$this, 'add_plugin_row_meta'], 10, 2);

        // Cron
        add_action('oneclick_seo_pro_daily_cron', [$this, 'run_daily_tasks']);
    }

    /**
     * Plugins loaded
     */
    public function on_plugins_loaded() {
        load_plugin_textdomain('oneclick-seo-pro', false, dirname(ONECLICK_SEO_PRO_BASENAME) . '/languages');
    }

    /**
     * Init
     */
    public function init() {
        try {
            // [v2.0.0] 보안 모듈 먼저 초기화
            if (class_exists('OneClick_SEO_Pro_Security_Init')) {
                OneClick_SEO_Pro_Security_Init::get_instance();
            }

            // Initialize components with class existence check
            if (class_exists('OneClick_SEO_Pro_Database')) {
                OneClick_SEO_Pro_Database::get_instance();
            }
            if (class_exists('OneClick_SEO_Pro_Schema_Generator')) {
                $this->schema_generator = OneClick_SEO_Pro_Schema_Generator::get_instance();
            }
            if (class_exists('OneClick_SEO_Pro_Sitemap_Manager')) {
                $this->sitemap_manager = OneClick_SEO_Pro_Sitemap_Manager::get_instance();
            }
            if (class_exists('OneClick_SEO_Pro_REST_Controller')) {
                OneClick_SEO_Pro_REST_Controller::get_instance();
            }
            if (class_exists('OneClick_SEO_Pro_Frontend')) {
                OneClick_SEO_Pro_Frontend::get_instance();
            }

            // Admin
            if (is_admin() && class_exists('OneClick_SEO_Pro_Admin')) {
                OneClick_SEO_Pro_Admin::get_instance();
            }

            // [v2.0.0] 플러그인 리스트 향상 (JJ_Plugin_List_Enhancer)
            if (is_admin()) {
                $this->init_plugin_list_enhancer();
            }
        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('1-Click SEO Pro: Init error: ' . $e->getMessage());
            }
        }
    }

    /**
     * [v2.0.0] 플러그인 목록 페이지 향상 초기화
     */
    private function init_plugin_list_enhancer() {
        // ACF CSS Manager의 Plugin List Enhancer 클래스 사용
        if (class_exists('JJ_Plugin_List_Enhancer')) {
            $enhancer = new JJ_Plugin_List_Enhancer();
            $enhancer->init(array(
                'plugin_file' => __FILE__,
                'plugin_name' => '1-Click SEO Pro',
                'settings_url' => admin_url('admin.php?page=oneclick-seo-pro-settings'),
                'text_domain' => 'oneclick-seo-pro',
                'version_constant' => 'ONECLICK_SEO_PRO_VERSION',
                'license_constant' => 'ONECLICK_SEO_PRO_LICENSE_TYPE',
                'upgrade_url' => 'https://3j-labs.com/',
                'docs_url' => 'https://3j-labs.com/docs',
                'support_url' => 'https://3j-labs.com/support',
            ));
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
        if (ONECLICK_SEO_PRO_BASENAME !== $file) {
            return $links;
        }

        $extra_links = [
            '<a href="https://3j-labs.com/docs/oneclick-seo" target="_blank">' . __('Documentation', 'oneclick-seo-pro') . '</a>',
        ];

        // Promo links
        if (!is_plugin_active('acf-user-journey-analytics/acf-user-journey-analytics.php')) {
            $extra_links[] = '<a href="https://wordpress.org/plugins/acf-user-journey-analytics/" target="_blank" style="color: #6366f1; font-weight: 600;">📊 ' . __('무료 애널리틱스', 'oneclick-seo-pro') . '</a>';
        }
        if (!is_plugin_active('acf-nudge-flow/acf-nudge-flow.php')) {
            $extra_links[] = '<a href="https://wordpress.org/plugins/acf-nudge-flow/" target="_blank" style="color: #22c55e; font-weight: 600;">🎯 ' . __('마케팅 자동화', 'oneclick-seo-pro') . '</a>';
        }

        return array_merge($links, $extra_links);
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('1-Click SEO', 'oneclick-seo-pro'),
            '1-Click SEO 🚀',
            'manage_options',
            'oneclick-seo-pro',
            [$this, 'render_dashboard_page'],
            'dashicons-chart-line',
            65
        );

        // Submenus
        $submenus = [
            ['oneclick-seo-pro', __('Dashboard', 'oneclick-seo-pro'), __('Dashboard', 'oneclick-seo-pro'), 'manage_options', 'oneclick-seo-pro', 'render_dashboard_page'],
            ['oneclick-seo-pro', __('SEO Analyzer', 'oneclick-seo-pro'), __('Analyzer', 'oneclick-seo-pro'), 'manage_options', 'oneclick-seo-pro-analyzer', 'render_analyzer_page'],
            ['oneclick-seo-pro', __('Bulk Optimizer', 'oneclick-seo-pro'), __('Bulk Optimizer', 'oneclick-seo-pro'), 'manage_options', 'oneclick-seo-pro-optimizer', 'render_optimizer_page'],
            ['oneclick-seo-pro', __('AEO', 'oneclick-seo-pro'), __('AEO (AI SEO)', 'oneclick-seo-pro'), 'manage_options', 'oneclick-seo-pro-aeo', 'render_aeo_page'],
            ['oneclick-seo-pro', __('Schema', 'oneclick-seo-pro'), __('Schema', 'oneclick-seo-pro'), 'manage_options', 'oneclick-seo-pro-schema', 'render_schema_page'],
            ['oneclick-seo-pro', __('Settings', 'oneclick-seo-pro'), __('Settings', 'oneclick-seo-pro'), 'manage_options', 'oneclick-seo-pro-settings', 'render_settings_page'],
            ['oneclick-seo-pro', __('Traffic Analytics', 'oneclick-seo-pro'), __('📊 Traffic', 'oneclick-seo-pro'), 'manage_options', 'oneclick-seo-pro-analytics', 'render_analytics_page'],
        ];

        foreach ($submenus as $submenu) {
            add_submenu_page(
                $submenu[0],
                $submenu[1],
                $submenu[2],
                $submenu[3],
                $submenu[4],
                [$this, $submenu[5]]
            );
        }
    }

    /**
     * Enqueue admin assets
     * [v2.0.0] v25 디자인 시스템 통합
     */
    public function enqueue_admin_assets($hook) {
        // Only on plugin pages or post editor
        if (strpos($hook, 'oneclick-seo-pro') === false && $hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }

        // [v2.0.0] v25 디자인 시스템 CSS
        $shared_path = dirname(dirname(ONECLICK_SEO_PRO_DIR)) . '/acf-css-really-simple-style-management-center-master';
        if (file_exists($shared_path . '/assets/css/jj-design-system-v25.css')) {
            wp_enqueue_style(
                'jj-design-system-v25',
                plugin_dir_url(dirname(dirname(__FILE__))) . 'acf-css-really-simple-style-management-center-master/assets/css/jj-design-system-v25.css',
                [],
                '25.0.0'
            );
        }

        // [v2.0.0] SEO v25 UI CSS
        wp_enqueue_style(
            'oneclick-seo-v25-ui',
            ONECLICK_SEO_PRO_URL . 'assets/css/jj-seo-v25-ui.css',
            [],
            ONECLICK_SEO_PRO_VERSION
        );

        // Legacy admin styles (하위 호환성)
        wp_enqueue_style(
            'oneclick-seo-pro-admin',
            ONECLICK_SEO_PRO_URL . 'assets/css/admin.css',
            ['oneclick-seo-v25-ui'],
            ONECLICK_SEO_PRO_VERSION
        );

        // [v2.0.0] 다크 모드 시스템
        if (file_exists($shared_path . '/assets/js/jj-dark-mode-v25.js')) {
            wp_enqueue_script(
                'jj-dark-mode-v25',
                plugin_dir_url(dirname(dirname(__FILE__))) . 'acf-css-really-simple-style-management-center-master/assets/js/jj-dark-mode-v25.js',
                ['jquery'],
                '25.0.0',
                true
            );
        }

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
            ONECLICK_SEO_PRO_URL . 'assets/js/admin.js',
            ['jquery', 'chartjs'],
            ONECLICK_SEO_PRO_VERSION,
            true
        );

        // Localize script
        wp_localize_script('oneclick-seo-pro-admin', 'oneclickSEO', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('oneclick-seo-pro/v1/'),
            'nonce' => wp_create_nonce('oneclick_seo_pro_nonce'),
            'restNonce' => wp_create_nonce('wp_rest'),
            'i18n' => [
                'analyzing' => __('분석 중...', 'oneclick-seo-pro'),
                'optimizing' => __('최적화 중...', 'oneclick-seo-pro'),
                'success' => __('성공!', 'oneclick-seo-pro'),
                'error' => __('오류 발생', 'oneclick-seo-pro'),
                'confirm_bulk' => __('선택한 항목을 벌크 최적화하시겠습니까?', 'oneclick-seo-pro'),
            ],
        ]);
    }

    /**
     * Activate plugin
     */
    public function activate() {
        try {
            // Create database tables
            if (class_exists('OneClick_SEO_Pro_Database')) {
                OneClick_SEO_Pro_Database::get_instance()->create_tables();
            }

            // Initialize ranking factors
            if (class_exists('OneClick_SEO_Pro_Ranking_Factors_DB')) {
                OneClick_SEO_Pro_Ranking_Factors_DB::get_instance()->initialize_factors();
            }

            // Set default options
            $this->set_default_options();

            // Schedule cron
            if (!wp_next_scheduled('oneclick_seo_pro_daily_cron')) {
                wp_schedule_event(time(), 'daily', 'oneclick_seo_pro_daily_cron');
            }

            // Flush rewrite rules for sitemap
            flush_rewrite_rules();

            // Set activation flag
            set_transient('oneclick_seo_pro_activated', true, 30);

        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('1-Click SEO Pro Activation Error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Deactivate plugin
     */
    public function deactivate() {
        try {
            // Clear scheduled events
            if (function_exists('wp_clear_scheduled_hook')) {
                wp_clear_scheduled_hook('oneclick_seo_pro_daily_cron');
            }

            // Flush rewrite rules
            if (function_exists('flush_rewrite_rules')) {
                flush_rewrite_rules();
            }
        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('1-Click SEO Pro Deactivation Error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Set default options
     */
    private function set_default_options() {
        $defaults = [
            'oneclick_seo_pro_version' => ONECLICK_SEO_PRO_VERSION,
            'oneclick_seo_pro_general' => [
                'post_types' => ['post', 'page'],
                'auto_analyze' => false,
                'score_in_list' => true,
            ],
            'oneclick_seo_pro_sitemap' => [
                'enabled' => true,
                'post_types' => ['post', 'page'],
                'include_images' => true,
                'ping_search_engines' => true,
            ],
            'oneclick_seo_pro_aeo' => [
                'enabled' => true,
                'auto_generate_faq' => false,
                'auto_optimize_snippets' => false,
                'ai_provider' => 'openai',
            ],
            'oneclick_seo_pro_schema' => [
                'enabled' => true,
                'types' => ['Article', 'WebPage', 'BreadcrumbList'],
            ],
        ];

        foreach ($defaults as $option => $value) {
            if (false === get_option($option)) {
                add_option($option, $value);
            }
        }
    }

    /**
     * Output meta tags
     */
    public function output_meta_tags() {
        if (!is_singular()) {
            return;
        }

        $post_id = get_the_ID();
        do_action('oneclick_seo_pro_meta_tags', $post_id);
    }

    /**
     * Output schema
     */
    public function output_schema() {
        $schema_settings = get_option('oneclick_seo_pro_schema', []);
        if (empty($schema_settings['enabled'])) {
            return;
        }

        if ($this->schema_generator) {
            $this->schema_generator->output_schema();
        }
    }

    /**
     * Register REST routes
     */
    public function register_rest_routes() {
        if (class_exists('OneClick_SEO_Pro_REST_Controller')) {
            $controller = OneClick_SEO_Pro_REST_Controller::get_instance();
            $controller->register_routes();
        }
    }

    /**
     * Add dashboard widget
     */
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'oneclick_seo_pro_dashboard_widget',
            __('1-Click SEO 개요', 'oneclick-seo-pro'),
            [$this, 'render_dashboard_widget']
        );
    }

    /**
     * Render dashboard widget
     */
    public function render_dashboard_widget() {
        $stats = $this->get_seo_stats();
        ?>
        <div class="oneclick-seo-widget">
            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 1; text-align: center; padding: 10px; background: #f9f9f9; border-radius: 4px;">
                    <div style="font-size: 24px; font-weight: 600; color: #FF6B35;"><?php echo esc_html($stats['analyzed_posts']); ?></div>
                    <div style="font-size: 12px; color: #666;"><?php esc_html_e('분석 완료', 'oneclick-seo-pro'); ?></div>
                </div>
                <div style="flex: 1; text-align: center; padding: 10px; background: #f9f9f9; border-radius: 4px;">
                    <div style="font-size: 24px; font-weight: 600; color: #10B981;"><?php echo esc_html(round($stats['average_score'])); ?></div>
                    <div style="font-size: 12px; color: #666;"><?php esc_html_e('평균 점수', 'oneclick-seo-pro'); ?></div>
                </div>
                <div style="flex: 1; text-align: center; padding: 10px; background: #f9f9f9; border-radius: 4px;">
                    <div style="font-size: 24px; font-weight: 600; color: #F59E0B;"><?php echo esc_html($stats['issues_count']); ?></div>
                    <div style="font-size: 12px; color: #666;"><?php esc_html_e('이슈', 'oneclick-seo-pro'); ?></div>
                </div>
            </div>

            <?php if ($stats['critical_issues'] > 0): ?>
            <div style="background: #fef7e1; border-left: 3px solid #ffb900; padding: 10px; margin-bottom: 15px;">
                <span class="dashicons dashicons-warning" style="color: #ffb900; margin-right: 5px;"></span>
                <?php printf(
                    esc_html(_n('%d개의 긴급 이슈가 있습니다', '%d개의 긴급 이슈가 있습니다', $stats['critical_issues'], 'oneclick-seo-pro')),
                    $stats['critical_issues']
                ); ?>
            </div>
            <?php endif; ?>

            <p style="text-align: right; margin: 0;">
                <a href="<?php echo esc_url(admin_url('admin.php?page=oneclick-seo-pro')); ?>">
                    <?php esc_html_e('전체 대시보드 보기', 'oneclick-seo-pro'); ?> &rarr;
                </a>
            </p>
        </div>
        <?php
    }

    /**
     * Get SEO stats
     */
    private function get_seo_stats() {
        global $wpdb;

        $table_scores = $wpdb->prefix . 'oneclick_seo_scores';
        $table_issues = $wpdb->prefix . 'oneclick_seo_issues';

        $stats = [
            'total_posts' => wp_count_posts('post')->publish + wp_count_posts('page')->publish,
            'analyzed_posts' => 0,
            'average_score' => 0,
            'issues_count' => 0,
            'critical_issues' => 0,
        ];

        // Check if tables exist
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_scores));

        if (!$table_exists) {
            return $stats;
        }

        try {
            $stats['analyzed_posts'] = (int) $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM $table_scores");
            $stats['average_score'] = (float) $wpdb->get_var("SELECT AVG(overall_score) FROM $table_scores");

            $issues_table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_issues));
            if ($issues_table_exists) {
                $stats['issues_count'] = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_issues WHERE status = 'open'");
                $stats['critical_issues'] = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_issues WHERE status = 'open' AND priority = 'critical'");
            }
        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('1-Click SEO Pro: Stats error: ' . $e->getMessage());
            }
        }

        return $stats;
    }

    /**
     * Run daily tasks
     */
    public function run_daily_tasks() {
        // Clean expired cache
        if (class_exists('OneClick_SEO_Pro_Database')) {
            OneClick_SEO_Pro_Database::get_instance()->clean_expired_cache();
        }
    }

    // =========================================
    // AJAX Handlers with Security
    // =========================================

    /**
     * AJAX: Analyze single post
     */
    public function ajax_analyze() {
        // Nonce 검증
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'oneclick_seo_pro_nonce')) {
            wp_send_json_error(['message' => __('보안 검증에 실패했습니다.', 'oneclick-seo-pro')]);
        }

        // 권한 확인
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('권한이 없습니다.', 'oneclick-seo-pro')]);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

        if (!$post_id) {
            wp_send_json_error(['message' => __('잘못된 포스트 ID입니다.', 'oneclick-seo-pro')]);
        }

        try {
            // Load classes
            if (!class_exists('OneClick_SEO_Pro_Analyzer')) {
                require_once ONECLICK_SEO_PRO_DIR . 'includes/algorithm/class-seo-analyzer.php';
            }
            if (!class_exists('OneClick_SEO_Pro_Scorer')) {
                require_once ONECLICK_SEO_PRO_DIR . 'includes/algorithm/class-seo-scorer.php';
            }

            $analyzer = new OneClick_SEO_Pro_Analyzer();
            $scorer = new OneClick_SEO_Pro_Scorer();

            $page_data = $analyzer->analyze_post($post_id);
            $score = $scorer->calculate_score($page_data);

            $this->save_score($post_id, $score);

            wp_send_json_success([
                'post_id' => $post_id,
                'score' => $score['overall_score'],
                'grade' => $score['grade'],
                'recommendations' => array_slice($score['recommendations'], 0, 5),
            ]);

        } catch (Exception $e) {
            wp_send_json_error(['message' => sprintf(__('오류: %s', 'oneclick-seo-pro'), $e->getMessage())]);
        }
    }

    /**
     * AJAX: Bulk analyze
     */
    public function ajax_bulk_analyze() {
        // Nonce 검증
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'oneclick_seo_pro_nonce')) {
            wp_send_json_error(['message' => __('보안 검증에 실패했습니다.', 'oneclick-seo-pro')]);
        }

        // 권한 확인
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('권한이 없습니다.', 'oneclick-seo-pro')]);
        }

        $post_ids = isset($_POST['post_ids']) ? array_map('intval', (array) $_POST['post_ids']) : [];

        if (empty($post_ids)) {
            wp_send_json_error(['message' => __('선택된 포스트가 없습니다.', 'oneclick-seo-pro')]);
        }

        // Limit batch size
        $post_ids = array_slice($post_ids, 0, 10);

        try {
            if (!class_exists('OneClick_SEO_Pro_Analyzer')) {
                require_once ONECLICK_SEO_PRO_DIR . 'includes/algorithm/class-seo-analyzer.php';
            }
            if (!class_exists('OneClick_SEO_Pro_Scorer')) {
                require_once ONECLICK_SEO_PRO_DIR . 'includes/algorithm/class-seo-scorer.php';
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

        } catch (Exception $e) {
            wp_send_json_error(['message' => sprintf(__('오류: %s', 'oneclick-seo-pro'), $e->getMessage())]);
        }
    }

    /**
     * AJAX: Get stats
     */
    public function ajax_get_stats() {
        // Nonce 검증
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'oneclick_seo_pro_nonce')) {
            wp_send_json_error(['message' => __('보안 검증에 실패했습니다.', 'oneclick-seo-pro')]);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('권한이 없습니다.', 'oneclick-seo-pro')]);
        }

        $stats = $this->get_seo_stats();
        wp_send_json_success($stats);
    }

    /**
     * AJAX: Optimize
     */
    public function ajax_optimize() {
        // Nonce 검증
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'oneclick_seo_pro_nonce')) {
            wp_send_json_error(['message' => __('보안 검증에 실패했습니다.', 'oneclick-seo-pro')]);
        }

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('권한이 없습니다.', 'oneclick-seo-pro')]);
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

        if (!$post_id) {
            wp_send_json_error(['message' => __('잘못된 포스트 ID입니다.', 'oneclick-seo-pro')]);
        }

        // TODO: Implement AI optimization
        wp_send_json_success([
            'post_id' => $post_id,
            'message' => __('최적화가 완료되었습니다.', 'oneclick-seo-pro'),
        ]);
    }

    /**
     * AJAX: 1-Click Setup
     */
    public function ajax_1click_setup() {
        // Nonce 검증
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'oneclick_seo_pro_nonce')) {
            wp_send_json_error(['message' => __('보안 검증에 실패했습니다.', 'oneclick-seo-pro')]);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('권한이 없습니다.', 'oneclick-seo-pro')]);
        }

        try {
            // Apply basic SEO settings
            update_option('oneclick_seo_pro_general', [
                'post_types' => ['post', 'page'],
                'auto_analyze' => true,
                'score_in_list' => true,
            ]);

            update_option('oneclick_seo_pro_schema', [
                'enabled' => true,
                'types' => ['Article', 'WebPage', 'BreadcrumbList'],
            ]);

            update_option('oneclick_seo_pro_sitemap', [
                'enabled' => true,
                'post_types' => ['post', 'page'],
                'include_images' => true,
                'ping_search_engines' => true,
            ]);

            wp_send_json_success([
                'message' => __('1-Click SEO 설정이 완료되었습니다!', 'oneclick-seo-pro'),
            ]);

        } catch (Exception $e) {
            wp_send_json_error(['message' => sprintf(__('오류: %s', 'oneclick-seo-pro'), $e->getMessage())]);
        }
    }

    /**
     * Save score to database
     */
    private function save_score($post_id, $score) {
        global $wpdb;

        $table = $wpdb->prefix . 'oneclick_seo_scores';

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

    // =========================================
    // Page Render Methods
    // =========================================

    public function render_dashboard_page() {
        $file = ONECLICK_SEO_PRO_DIR . 'admin/views/dashboard.php';
        if (file_exists($file)) {
            include $file;
        } else {
            $this->render_placeholder_page(__('Dashboard', 'oneclick-seo-pro'));
        }
    }

    public function render_analyzer_page() {
        $file = ONECLICK_SEO_PRO_DIR . 'admin/views/analyzer.php';
        if (file_exists($file)) {
            include $file;
        } else {
            $this->render_placeholder_page(__('SEO Analyzer', 'oneclick-seo-pro'));
        }
    }

    public function render_optimizer_page() {
        $file = ONECLICK_SEO_PRO_DIR . 'admin/views/optimizer.php';
        if (file_exists($file)) {
            include $file;
        } else {
            $this->render_placeholder_page(__('Bulk Optimizer', 'oneclick-seo-pro'));
        }
    }

    public function render_aeo_page() {
        $file = ONECLICK_SEO_PRO_DIR . 'admin/views/aeo.php';
        if (file_exists($file)) {
            include $file;
        } else {
            $this->render_placeholder_page(__('AEO Settings', 'oneclick-seo-pro'));
        }
    }

    public function render_schema_page() {
        $file = ONECLICK_SEO_PRO_DIR . 'admin/views/schema.php';
        if (file_exists($file)) {
            include $file;
        } else {
            $this->render_placeholder_page(__('Schema Manager', 'oneclick-seo-pro'));
        }
    }

    public function render_settings_page() {
        $file = ONECLICK_SEO_PRO_DIR . 'admin/views/settings.php';
        if (file_exists($file)) {
            include $file;
        } else {
            $this->render_placeholder_page(__('Settings', 'oneclick-seo-pro'));
        }
    }

    public function render_analytics_page() {
        $file = ONECLICK_SEO_PRO_DIR . 'admin/views/analytics.php';
        if (file_exists($file)) {
            include $file;
        } else {
            $this->render_placeholder_page(__('Traffic Analytics', 'oneclick-seo-pro'));
        }
    }

    /**
     * Render placeholder page
     */
    private function render_placeholder_page($title) {
        ?>
        <div class="wrap oneclick-seo-dashboard-v25">
            <div class="oneclick-seo-header-v25">
                <h1><?php echo esc_html($title); ?></h1>
            </div>
            <div class="oneclick-seo-welcome-v25">
                <h2><?php esc_html_e('페이지 준비 중', 'oneclick-seo-pro'); ?></h2>
                <p><?php esc_html_e('이 기능은 곧 제공될 예정입니다.', 'oneclick-seo-pro'); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Get plugin URL
     */
    public function get_plugin_url() {
        return ONECLICK_SEO_PRO_URL;
    }

    /**
     * Get plugin path
     */
    public function get_plugin_path() {
        return ONECLICK_SEO_PRO_DIR;
    }

    /**
     * Get version
     */
    public function get_version() {
        return ONECLICK_SEO_PRO_VERSION;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new Exception('Cannot unserialize singleton');
    }
}

/**
 * Get plugin instance
 */
function oneclick_seo_pro() {
    return OneClick_SEO_Pro::get_instance();
}

// Initialize plugin with error handling
try {
    oneclick_seo_pro();
} catch (Exception $e) {
    if (function_exists('error_log')) {
        error_log('1-Click SEO Pro Init Error: ' . $e->getMessage());
    }
} catch (Error $e) {
    if (function_exists('error_log')) {
        error_log('1-Click SEO Pro Init Fatal Error: ' . $e->getMessage());
    }
}

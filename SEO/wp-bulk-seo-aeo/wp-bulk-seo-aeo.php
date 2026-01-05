<?php
/**
 * Plugin Name: WP Bulk SEO & AEO(AIO) - WordPress Really Simple and Easy Search Engine Optimization and AI Optimization Automation. 1, 2, 3 Click Auto Optimization.
 * Plugin URI: https://3j-labs.com/
 * Description: AI-powered SEO & AEO optimization based on real Google ranking factors from the 2024 algorithm leak. Integrates Google Search Console API, Google Analytics API, and PageSpeed Insights API v5 for comprehensive SEO analysis. Features 1-Click SEO, 2-Click AI optimization, and 3-Click full automation.
 * Version: 2.1.2
 * Author: 3J Labs (제이x제니x제이슨 연구소)
 * Author URI: https://3j-labs.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-bulk-seo-aeo
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 *
 * @package WP_Bulk_SEO_AEO
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('WP_BULK_SEO_AEO_VERSION', '2.1.2');
define('WP_BULK_SEO_AEO_FILE', __FILE__);
define('WP_BULK_SEO_AEO_PATH', plugin_dir_path(__FILE__));
define('WP_BULK_SEO_AEO_URL', plugin_dir_url(__FILE__));
define('WP_BULK_SEO_AEO_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
final class WP_Bulk_SEO_AEO {

    /**
     * Plugin instance
     */
    private static $instance = null;

    /**
     * Plugin components
     */
    public $scorer;
    public $analyzer;
    public $ranking_db;
    public $bulk_optimizer;
    public $ai_engine;
    public $schema_generator;
    public $sitemap_manager;

    /**
     * Get plugin instance
     */
    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        try {
            $this->load_dependencies();
            $this->init_hooks();
        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO & AEO Constructor Error: ' . $e->getMessage());
            }
            // 오류가 발생해도 플러그인은 활성화되도록 함
        } catch (Error $e) {
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO & AEO Constructor Fatal Error: ' . $e->getMessage());
            }
            // 치명적 오류가 발생해도 플러그인은 활성화되도록 함
        }
    }

    /**
     * Load required files
     */
    private function load_dependencies() {
        // Core algorithm classes (from 3j-seo-algorithm)
        $algorithm_path = WP_BULK_SEO_AEO_PATH . 'includes/algorithm/';
        if (file_exists($algorithm_path . 'class-3j-seo-scorer.php')) {
            require_once $algorithm_path . 'class-3j-seo-scorer.php';
        }
        if (file_exists($algorithm_path . 'class-3j-seo-analyzer.php')) {
            require_once $algorithm_path . 'class-3j-seo-analyzer.php';
        }
        if (file_exists($algorithm_path . 'class-3j-ranking-factors-db.php')) {
            require_once $algorithm_path . 'class-3j-ranking-factors-db.php';
        }
        // Legacy class names (if exists)
        if (file_exists($algorithm_path . 'class-ranking-factors-db.php')) {
            require_once $algorithm_path . 'class-ranking-factors-db.php';
        }
        if (file_exists($algorithm_path . 'class-seo-scorer.php')) {
            require_once $algorithm_path . 'class-seo-scorer.php';
        }
        if (file_exists($algorithm_path . 'class-seo-analyzer.php')) {
            require_once $algorithm_path . 'class-seo-analyzer.php';
        }

        // Core functionality
        $core_files = [
            'includes/class-bulk-optimizer.php',
            'includes/class-ai-engine.php',
            'includes/class-schema-generator.php',
            'includes/class-sitemap-manager.php',
            'includes/class-content-optimizer.php',
            'includes/class-gtm-style-optimizer.php',
        ];
        
        // API Integrations
        $api_files = [
            'includes/api/class-google-search-console.php',
            'includes/api/class-pagespeed-insights.php',
            'includes/api/class-google-analytics.php',
            'includes/api/class-rest-api.php', // 필수
        ];
        
        // AEO (Answer Engine Optimization)
        $aeo_files = [
            'includes/aeo/class-aeo-engine.php',
            'includes/aeo/class-faq-generator.php',
            'includes/aeo/class-featured-snippet-optimizer.php',
        ];
        
        // Extension & Benchmarking
        $extension_files = [
            'includes/chrome-extension/class-chrome-extension-sync.php',
            'includes/benchmarking/class-benchmark-engine.php',
            'includes/ranking/class-ranking-monitor.php',
            'includes/ranking/class-ranking-predictor.php',
        ];
        
        // Remote Service
        $remote_files = [
            'includes/remote-service/class-remote-service-manager.php',
            'includes/remote-service/class-license-manager.php',
        ];
        
        // Modules (Rank Math Pro style)
        $module_files = [
            'includes/modules/class-seo-analysis-module.php',
            'includes/modules/class-redirections-module.php',
            'includes/modules/class-content-ai-module.php',
        ];
        
        // Admin
        $admin_files = [];
        if (is_admin()) {
            $admin_files[] = 'includes/admin/class-admin.php';
        }
        
        // 모든 파일을 안전하게 로드
        $all_files = array_merge($core_files, $api_files, $aeo_files, $extension_files, $remote_files, $module_files, $admin_files);
        
        foreach ($all_files as $file) {
            $file_path = WP_BULK_SEO_AEO_PATH . $file;
            if (file_exists($file_path)) {
                try {
                    require_once $file_path;
                } catch (Exception $e) {
                    if (function_exists('error_log')) {
                        error_log('WP Bulk SEO & AEO: Failed to load ' . $file . ': ' . $e->getMessage());
                    }
                    // 파일 로드 오류가 발생해도 계속 진행
                }
            }
        }
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation
        register_activation_hook(WP_BULK_SEO_AEO_FILE, [$this, 'activate']);
        register_deactivation_hook(WP_BULK_SEO_AEO_FILE, [$this, 'deactivate']);

        // Initialize
        add_action('plugins_loaded', [$this, 'init']);
        add_action('init', [$this, 'init_components']);

        // Frontend
        add_action('wp_head', [$this, 'output_meta_tags'], 1);
        add_action('wp_head', [$this, 'output_schema'], 5);

        // Admin
        if (is_admin()) {
            add_action('admin_menu', [$this, 'add_admin_menu']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
            
            // AJAX handlers
            add_action('wp_ajax_wp_bulk_seo_aeo_1click_setup', [$this, 'ajax_1click_setup']);
            add_action('wp_ajax_wp_bulk_seo_aeo_bulk_analyze', [$this, 'ajax_bulk_analyze']);
            add_action('wp_ajax_wp_bulk_seo_aeo_bulk_optimize', [$this, 'ajax_bulk_optimize']);
        }

        // REST API
        add_action('rest_api_init', [$this, 'register_rest_routes']);

        // Cron for bulk operations
        add_action('wp_bulk_seo_aeo_analyze_cron', [$this, 'run_scheduled_analysis']);
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables
        $this->create_tables();

        // Set default options
        $this->set_default_options();

        // Schedule cron
        if (!wp_next_scheduled('wp_bulk_seo_aeo_analyze_cron')) {
            wp_schedule_event(time(), 'daily', 'wp_bulk_seo_aeo_analyze_cron');
        }

        // Schedule ranking monitoring (hourly, will check frequency internally)
        if (!wp_next_scheduled('wp_bulk_seo_aeo_ranking_monitor')) {
            wp_schedule_event(time(), 'hourly', 'wp_bulk_seo_aeo_ranking_monitor');
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        try {
            // Clear scheduled hooks
            if (function_exists('wp_clear_scheduled_hook')) {
                wp_clear_scheduled_hook('wp_bulk_seo_aeo_analyze_cron');
                wp_clear_scheduled_hook('wp_bulk_seo_aeo_ranking_monitor');
            }

            // Flush rewrite rules
            if (function_exists('flush_rewrite_rules')) {
                flush_rewrite_rules();
            }
        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO & AEO Deactivation Error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;

        // WordPress가 로드되지 않았을 경우 대비
        if (!function_exists('dbDelta')) {
            if (defined('ABSPATH') && file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            } else {
                // dbDelta가 없으면 테이블 생성을 건너뜀
                return;
            }
        }

        // $wpdb가 없으면 테이블 생성을 건너뜀
        if (!isset($wpdb) || !is_object($wpdb)) {
            return;
        }

        $charset_collate = $wpdb->get_charset_collate();

        // SEO Factors table (from CSV data)
        $table_factors = $wpdb->prefix . 'bulk_seo_aeo_factors';
        $sql_factors = "CREATE TABLE $table_factors (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            module_name varchar(255) DEFAULT '',
            category varchar(100) DEFAULT '',
            seo_factor varchar(255) DEFAULT '',
            estimated_weight tinyint(3) unsigned DEFAULT 5,
            impact varchar(50) DEFAULT '',
            explanation text,
            google_element varchar(255) DEFAULT '',
            api_source varchar(100) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY category (category),
            KEY estimated_weight (estimated_weight),
            KEY google_element (google_element)
        ) $charset_collate;";

        // SEO Scores table
        $table_scores = $wpdb->prefix . 'bulk_seo_aeo_scores';
        $sql_scores = "CREATE TABLE $table_scores (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            overall_score tinyint(3) unsigned DEFAULT 0,
            grade varchar(5) DEFAULT '',
            technical_score tinyint(3) unsigned DEFAULT 0,
            on_page_score tinyint(3) unsigned DEFAULT 0,
            content_score tinyint(3) unsigned DEFAULT 0,
            links_score tinyint(3) unsigned DEFAULT 0,
            schema_score tinyint(3) unsigned DEFAULT 0,
            engagement_score tinyint(3) unsigned DEFAULT 0,
            freshness_score tinyint(3) unsigned DEFAULT 0,
            eeat_score tinyint(3) unsigned DEFAULT 0,
            demotion_risk tinyint(3) unsigned DEFAULT 0,
            analysis_data longtext,
            recommendations longtext,
            analyzed_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY post_id (post_id),
            KEY overall_score (overall_score),
            KEY analyzed_at (analyzed_at)
        ) $charset_collate;";

        // SEO Issues table
        $table_issues = $wpdb->prefix . 'bulk_seo_aeo_issues';
        $sql_issues = "CREATE TABLE $table_issues (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            issue_type varchar(50) NOT NULL,
            severity varchar(20) NOT NULL,
            factor_key varchar(100) DEFAULT '',
            message text,
            message_kr text,
            fix_suggestion text,
            is_resolved tinyint(1) DEFAULT 0,
            detected_at datetime DEFAULT NULL,
            resolved_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY issue_type (issue_type),
            KEY severity (severity),
            KEY is_resolved (is_resolved)
        ) $charset_collate;";

        // Bulk Operations Log
        $table_log = $wpdb->prefix . 'bulk_seo_aeo_log';
        $sql_log = "CREATE TABLE $table_log (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            operation_type varchar(50) NOT NULL,
            status varchar(20) NOT NULL,
            total_items int(11) unsigned DEFAULT 0,
            processed_items int(11) unsigned DEFAULT 0,
            success_items int(11) unsigned DEFAULT 0,
            failed_items int(11) unsigned DEFAULT 0,
            details longtext,
            started_at datetime DEFAULT NULL,
            completed_at datetime DEFAULT NULL,
            user_id bigint(20) unsigned DEFAULT 0,
            PRIMARY KEY (id),
            KEY operation_type (operation_type),
            KEY status (status),
            KEY started_at (started_at)
        ) $charset_collate;";

        // Rankings table (from Chrome extension)
        $table_rankings = $wpdb->prefix . 'bulk_seo_aeo_rankings';
        $sql_rankings = "CREATE TABLE $table_rankings (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT 0,
            keyword varchar(255) NOT NULL,
            url varchar(500) DEFAULT '',
            position int(11) unsigned DEFAULT 0,
            page_title varchar(500) DEFAULT '',
            page_url varchar(500) DEFAULT '',
            domain varchar(255) DEFAULT '',
            tracked_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY keyword (keyword),
            KEY url (url(191)),
            KEY position (position),
            KEY tracked_at (tracked_at)
        ) $charset_collate;";

        // Ranking history (time-series data)
        $table_ranking_history = $wpdb->prefix . 'bulk_seo_aeo_ranking_history';
        $sql_ranking_history = "CREATE TABLE $table_ranking_history (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            keyword_id bigint(20) unsigned DEFAULT 0,
            keyword varchar(255) NOT NULL,
            url varchar(500) DEFAULT '',
            position int(11) unsigned DEFAULT 0,
            source varchar(50) DEFAULT '',
            checked_at datetime DEFAULT NULL,
            time_of_day varchar(20) DEFAULT '',
            PRIMARY KEY (id),
            KEY keyword_id (keyword_id),
            KEY keyword (keyword),
            KEY checked_at (checked_at),
            KEY time_of_day (time_of_day)
        ) $charset_collate;";

        // Competition data
        $table_competition = $wpdb->prefix . 'bulk_seo_aeo_competition';
        $sql_competition = "CREATE TABLE $table_competition (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT 0,
            keyword varchar(255) NOT NULL,
            competition_level varchar(50) DEFAULT '',
            competition_score float DEFAULT 0,
            top_competitors longtext,
            updated_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY keyword_user (keyword, user_id),
            KEY competition_score (competition_score)
        ) $charset_collate;";

        // Search volume data
        $table_search_volume = $wpdb->prefix . 'bulk_seo_aeo_search_volume';
        $sql_search_volume = "CREATE TABLE $table_search_volume (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT 0,
            keyword varchar(255) NOT NULL,
            search_volume int(11) unsigned DEFAULT 0,
            cpc float DEFAULT 0,
            difficulty int(11) unsigned DEFAULT 0,
            updated_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY keyword_user (keyword, user_id),
            KEY search_volume (search_volume)
        ) $charset_collate;";

        // Keywords to monitor
        $table_keywords = $wpdb->prefix . 'bulk_seo_aeo_keywords';
        $sql_keywords = "CREATE TABLE $table_keywords (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT 0,
            keyword varchar(255) NOT NULL,
            target_url varchar(500) DEFAULT '',
            is_active tinyint(1) DEFAULT 1,
            priority int(11) DEFAULT 5,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY keyword (keyword),
            KEY is_active (is_active)
        ) $charset_collate;";

        // Chrome extension data
        $table_extension_data = $wpdb->prefix . 'bulk_seo_aeo_extension_data';
        $sql_extension_data = "CREATE TABLE $table_extension_data (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT 0,
            data_type varchar(50) DEFAULT '',
            data longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY data_type (data_type)
        ) $charset_collate;";

        // Remote sites (for non-WordPress sites)
        $table_remote_sites = $wpdb->prefix . 'bulk_seo_aeo_remote_sites';
        $sql_remote_sites = "CREATE TABLE $table_remote_sites (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            site_url varchar(500) NOT NULL,
            site_name varchar(255) DEFAULT '',
            license_key varchar(255) DEFAULT '',
            site_token varchar(255) DEFAULT '',
            platform varchar(50) DEFAULT '',
            status varchar(20) DEFAULT 'active',
            registered_at datetime DEFAULT CURRENT_TIMESTAMP,
            last_sync_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY site_token (site_token),
            KEY license_key (license_key),
            KEY status (status)
        ) $charset_collate;";

        // Remote sync data
        $table_remote_sync = $wpdb->prefix . 'bulk_seo_aeo_remote_sync';
        $sql_remote_sync = "CREATE TABLE $table_remote_sync (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            site_id bigint(20) unsigned DEFAULT 0,
            url varchar(500) DEFAULT '',
            title varchar(500) DEFAULT '',
            data longtext,
            synced_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY site_id (site_id),
            KEY url (url(191))
        ) $charset_collate;";

        // Metrics table (for GTM script tracking)
        $table_metrics = $wpdb->prefix . 'bulk_seo_aeo_metrics';
        $sql_metrics = "CREATE TABLE $table_metrics (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            site_id varchar(255) DEFAULT '',
            url varchar(500) DEFAULT '',
            metric varchar(50) DEFAULT '',
            value float DEFAULT 0,
            tracked_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY site_id (site_id),
            KEY metric (metric),
            KEY tracked_at (tracked_at)
        ) $charset_collate;";

        // dbDelta 함수가 있는지 확인 후 실행
        if (function_exists('dbDelta')) {
            try {
                dbDelta($sql_factors);
                dbDelta($sql_scores);
                dbDelta($sql_issues);
                dbDelta($sql_log);
                dbDelta($sql_rankings);
                dbDelta($sql_ranking_history);
                dbDelta($sql_competition);
                dbDelta($sql_search_volume);
                dbDelta($sql_keywords);
                dbDelta($sql_extension_data);
                dbDelta($sql_remote_sites);
                dbDelta($sql_remote_sync);
                dbDelta($sql_metrics);
            } catch (Exception $e) {
                if (function_exists('error_log')) {
                    error_log('WP Bulk SEO & AEO Table Creation Error: ' . $e->getMessage());
                }
                // 테이블 생성 오류가 발생해도 계속 진행
            }
        }

        // Load CSV data into factors table (오류 처리 포함)
        try {
            $this->load_factors_from_csv();
        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO & AEO CSV Load Error: ' . $e->getMessage());
            }
            // CSV 로드 오류가 발생해도 계속 진행
        }
    }

    /**
     * Load SEO factors from CSV file
     */
    private function load_factors_from_csv() {
        global $wpdb;

        // $wpdb가 없으면 CSV 로드를 건너뜀
        if (!isset($wpdb) || !is_object($wpdb)) {
            return;
        }

        $table = $wpdb->prefix . 'bulk_seo_aeo_factors';

        // Check if data already exists
        $count = 0;
        try {
            // 테이블이 존재하는지 먼저 확인
            $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
            if ($table_exists) {
                $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
            } else {
                // 테이블이 없으면 CSV 로드를 건너뜀
                return;
            }
        } catch (Exception $e) {
            // 테이블이 없을 수 있으므로 오류 무시하고 계속 진행
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO & AEO CSV Load: Table check error: ' . $e->getMessage());
            }
            return; // 테이블이 없으면 CSV 로드를 건너뜀
        } catch (Error $e) {
            // 치명적 오류도 처리
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO & AEO CSV Load: Table check fatal error: ' . $e->getMessage());
            }
            return;
        }
        
        if ($count > 0) {
            return; // Data already loaded
        }

        // Try multiple CSV file locations
        $csv_files = [
            WP_BULK_SEO_AEO_PATH . 'includes/algorithm/3j-unified-ranking-factors.csv',
            WP_BULK_SEO_AEO_PATH . 'includes/algorithm/3j-ranking-factors-database.csv',
            dirname(WP_BULK_SEO_AEO_PATH) . '/../seo_factors_sample_data.csv',
        ];
        
        // CSV 파일이 없으면 로드를 건너뜀
        $csv_file = null;
        foreach ($csv_files as $file) {
            if (file_exists($file) && is_readable($file)) {
                $csv_file = $file;
                break;
            }
        }
        
        if (!$csv_file) {
            // CSV 파일이 없어도 플러그인은 정상 작동
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO & AEO: CSV file not found, skipping data load');
            }
            return;
        }

        $handle = @fopen($csv_file, 'r');
        if (!$handle) {
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO & AEO: Failed to open CSV file: ' . $csv_file);
            }
            return;
        }

        // Read header (오류 처리 포함)
        $header = @fgetcsv($handle);
        if (!$header || empty($header)) {
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO & AEO: Failed to read CSV header');
            }
            return;
        }

        // Map header columns
        $column_map = [];
        foreach ($header as $index => $col) {
            $col_lower = strtolower(trim($col));
            if (strpos($col_lower, 'name') !== false) $column_map['name'] = $index;
            elseif (strpos($col_lower, 'module') !== false) $column_map['module_name'] = $index;
            elseif (strpos($col_lower, 'category') !== false) $column_map['category'] = $index;
            elseif (strpos($col_lower, 'seo_factor') !== false || strpos($col_lower, 'factor') !== false) $column_map['seo_factor'] = $index;
            elseif (strpos($col_lower, 'weight') !== false) $column_map['estimated_weight'] = $index;
            elseif (strpos($col_lower, 'impact') !== false) $column_map['impact'] = $index;
            elseif (strpos($col_lower, 'explanation') !== false || strpos($col_lower, 'description') !== false) $column_map['explanation'] = $index;
            elseif (strpos($col_lower, 'google') !== false) $column_map['google_element'] = $index;
            elseif (strpos($col_lower, 'api_source') !== false || strpos($col_lower, 'api') !== false) $column_map['api_source'] = $index;
        }

        // Insert data (오류 처리 포함)
        $inserted = 0;
        try {
            while (($data = @fgetcsv($handle)) !== false) {
                if (count($data) < 3) continue; // Skip invalid rows

                $insert_data = [];
                if (isset($column_map['name'])) $insert_data['name'] = sanitize_text_field($data[$column_map['name']] ?? '');
                if (isset($column_map['module_name'])) $insert_data['module_name'] = sanitize_text_field($data[$column_map['module_name']] ?? '');
                if (isset($column_map['category'])) $insert_data['category'] = sanitize_text_field($data[$column_map['category']] ?? '');
                if (isset($column_map['seo_factor'])) $insert_data['seo_factor'] = sanitize_text_field($data[$column_map['seo_factor']] ?? '');
                if (isset($column_map['estimated_weight'])) {
                    $weight = intval($data[$column_map['estimated_weight']] ?? 5);
                    $insert_data['estimated_weight'] = max(1, min(10, $weight)); // Clamp between 1-10
                }
                if (isset($column_map['impact'])) $insert_data['impact'] = sanitize_text_field($data[$column_map['impact']] ?? '');
                if (isset($column_map['explanation'])) $insert_data['explanation'] = sanitize_textarea_field($data[$column_map['explanation']] ?? '');
                if (isset($column_map['google_element'])) $insert_data['google_element'] = sanitize_text_field($data[$column_map['google_element']] ?? '');
                if (isset($column_map['api_source'])) $insert_data['api_source'] = sanitize_text_field($data[$column_map['api_source']] ?? '');

                if (!empty($insert_data['name'])) {
                    try {
                        $wpdb->insert($table, $insert_data);
                        $inserted++;
                    } catch (Exception $e) {
                        // 개별 행 삽입 오류는 무시하고 계속 진행
                        if (function_exists('error_log')) {
                            error_log('WP Bulk SEO & AEO CSV: Row insert error: ' . $e->getMessage());
                        }
                    }
                }
            }
        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO & AEO CSV: Data processing error: ' . $e->getMessage());
            }
        } finally {
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
        }
    }

    /**
     * Set default options
     */
    private function set_default_options() {
        $defaults = [
            'version' => WP_BULK_SEO_AEO_VERSION,
            'post_types' => ['post', 'page'],
            'auto_analyze' => true,
            'analyze_on_save' => true,
            'show_score_column' => true,
            'enable_schema' => true,
            'schema_types' => ['Article', 'WebPage', 'BreadcrumbList'],
            'enable_sitemap' => true,
            'enable_aeo' => true,
            'ai_provider' => 'openai',
            'ai_model' => 'gpt-4o-mini',
            'bulk_batch_size' => 10,
            'score_threshold_critical' => 40,
            'score_threshold_warning' => 60,
            'score_threshold_good' => 80,
        ];

        foreach ($defaults as $key => $value) {
            if (get_option('wp_bulk_seo_aeo_' . $key) === false) {
                update_option('wp_bulk_seo_aeo_' . $key, $value);
            }
        }
    }

    /**
     * Initialize plugin
     */
    public function init() {
        // Load textdomain
        load_plugin_textdomain('wp-bulk-seo-aeo', false, dirname(WP_BULK_SEO_AEO_BASENAME) . '/languages');
    }

    /**
     * Initialize components
     */
    public function init_components() {
        try {
            // Initialize core components (클래스 존재 확인 후 인스턴스 생성)
            if (class_exists('ThreeJ_SEO_Scorer')) {
                try {
                    $this->scorer = new ThreeJ_SEO_Scorer();
                } catch (Exception $e) {
                    if (function_exists('error_log')) {
                        error_log('WP Bulk SEO & AEO: ThreeJ_SEO_Scorer init error: ' . $e->getMessage());
                    }
                }
            }
            if (class_exists('ThreeJ_SEO_Analyzer')) {
                try {
                    $this->analyzer = new ThreeJ_SEO_Analyzer();
                } catch (Exception $e) {
                    if (function_exists('error_log')) {
                        error_log('WP Bulk SEO & AEO: ThreeJ_SEO_Analyzer init error: ' . $e->getMessage());
                    }
                }
            }
            if (class_exists('WP_Bulk_SEO_AEO_Bulk_Optimizer')) {
                try {
                    $this->bulk_optimizer = new WP_Bulk_SEO_AEO_Bulk_Optimizer();
                } catch (Exception $e) {
                    if (function_exists('error_log')) {
                        error_log('WP Bulk SEO & AEO: Bulk_Optimizer init error: ' . $e->getMessage());
                    }
                }
            }
            if (class_exists('WP_Bulk_SEO_AEO_AI_Engine')) {
                try {
                    $this->ai_engine = new WP_Bulk_SEO_AEO_AI_Engine();
                } catch (Exception $e) {
                    if (function_exists('error_log')) {
                        error_log('WP Bulk SEO & AEO: AI_Engine init error: ' . $e->getMessage());
                    }
                }
            }
            if (class_exists('WP_Bulk_SEO_AEO_Schema_Generator')) {
                try {
                    $this->schema_generator = new WP_Bulk_SEO_AEO_Schema_Generator();
                } catch (Exception $e) {
                    if (function_exists('error_log')) {
                        error_log('WP Bulk SEO & AEO: Schema_Generator init error: ' . $e->getMessage());
                    }
                }
            }
            if (class_exists('WP_Bulk_SEO_AEO_Sitemap_Manager')) {
                try {
                    $this->sitemap_manager = new WP_Bulk_SEO_AEO_Sitemap_Manager();
                } catch (Exception $e) {
                    if (function_exists('error_log')) {
                        error_log('WP Bulk SEO & AEO: Sitemap_Manager init error: ' . $e->getMessage());
                    }
                }
            }

            // Initialize Modules (Rank Math Pro style)
            if (class_exists('WP_Bulk_SEO_AEO_Analysis_Module')) {
                try {
                    WP_Bulk_SEO_AEO_Analysis_Module::instance();
                } catch (Exception $e) {
                    if (function_exists('error_log')) {
                        error_log('WP Bulk SEO & AEO: Analysis Module init error: ' . $e->getMessage());
                    }
                }
            }
            if (class_exists('WP_Bulk_SEO_AEO_Redirections_Module')) {
                try {
                    WP_Bulk_SEO_AEO_Redirections_Module::instance();
                } catch (Exception $e) {
                    if (function_exists('error_log')) {
                        error_log('WP Bulk SEO & AEO: Redirections Module init error: ' . $e->getMessage());
                    }
                }
            }
            if (class_exists('WP_Bulk_SEO_AEO_Content_AI_Module')) {
                try {
                    WP_Bulk_SEO_AEO_Content_AI_Module::instance();
                } catch (Exception $e) {
                    if (function_exists('error_log')) {
                        error_log('WP Bulk SEO & AEO: Content AI Module init error: ' . $e->getMessage());
                    }
                }
            }

            // Initialize Admin
            if (is_admin() && class_exists('WP_Bulk_SEO_AEO_Admin')) {
                try {
                    new WP_Bulk_SEO_AEO_Admin($this);
                } catch (Exception $e) {
                    if (function_exists('error_log')) {
                        error_log('WP Bulk SEO & AEO: Admin init error: ' . $e->getMessage());
                    }
                }
            }
            
            // [v2.0.0] 플러그인 리스트 향상 (JJ_Plugin_List_Enhancer)
            if (is_admin()) {
                $this->init_plugin_list_enhancer();
            }
        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO & AEO: Components init error: ' . $e->getMessage());
            }
            // 오류가 발생해도 플러그인은 활성화되도록 함
        } catch (Error $e) {
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO & AEO: Components init fatal error: ' . $e->getMessage());
            }
            // 치명적 오류가 발생해도 플러그인은 활성화되도록 함
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
                'plugin_name' => '원 클릭 SEO',
                'settings_url' => admin_url('admin.php?page=wp-bulk-seo-aeo'),
                'text_domain' => 'wp-bulk-seo-aeo',
                'version_constant' => 'WP_BULK_SEO_AEO_VERSION',
                'license_constant' => 'WP_BULK_SEO_AEO_LICENSE_TYPE',
                'upgrade_url' => 'https://3j-labs.com/',
                'docs_url' => 'https://3j-labs.com/docs',
                'support_url' => 'https://3j-labs.com/support',
            ));
        }
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('WP Bulk SEO & AEO', 'wp-bulk-seo-aeo'),
            '원 클릭 SEO 📈',
            'manage_options',
            'wp-bulk-seo-aeo',
            [$this, 'render_dashboard'],
            'dashicons-chart-area',
            70 // '사용자' 바로 위
        );

        // Submenus
        add_submenu_page(
            'wp-bulk-seo-aeo',
            __('Dashboard', 'wp-bulk-seo-aeo'),
            __('Dashboard', 'wp-bulk-seo-aeo'),
            'manage_options',
            'wp-bulk-seo-aeo',
            [$this, 'render_dashboard']
        );

        add_submenu_page(
            'wp-bulk-seo-aeo',
            __('Bulk Analyzer', 'wp-bulk-seo-aeo'),
            __('Bulk Analyzer', 'wp-bulk-seo-aeo'),
            'manage_options',
            'wp-bulk-seo-aeo-analyzer',
            [$this, 'render_analyzer']
        );

        add_submenu_page(
            'wp-bulk-seo-aeo',
            __('Bulk Optimizer', 'wp-bulk-seo-aeo'),
            __('Bulk Optimizer', 'wp-bulk-seo-aeo'),
            'manage_options',
            'wp-bulk-seo-aeo-optimizer',
            [$this, 'render_optimizer']
        );

        add_submenu_page(
            'wp-bulk-seo-aeo',
            __('AEO Settings', 'wp-bulk-seo-aeo'),
            __('AEO Settings', 'wp-bulk-seo-aeo'),
            'manage_options',
            'wp-bulk-seo-aeo-aeo',
            [$this, 'render_aeo']
        );

        add_submenu_page(
            'wp-bulk-seo-aeo',
            __('Schema Manager', 'wp-bulk-seo-aeo'),
            __('Schema Manager', 'wp-bulk-seo-aeo'),
            'manage_options',
            'wp-bulk-seo-aeo-schema',
            [$this, 'render_schema']
        );

        add_submenu_page(
            'wp-bulk-seo-aeo',
            __('Ranking Factors', 'wp-bulk-seo-aeo'),
            __('Ranking Factors', 'wp-bulk-seo-aeo'),
            'manage_options',
            'wp-bulk-seo-aeo-factors',
            [$this, 'render_factors']
        );

        add_submenu_page(
            'wp-bulk-seo-aeo',
            __('Settings', 'wp-bulk-seo-aeo'),
            __('Settings', 'wp-bulk-seo-aeo'),
            'manage_options',
            'wp-bulk-seo-aeo-settings',
            [$this, 'render_settings']
        );

        add_submenu_page(
            'wp-bulk-seo-aeo',
            __('License & Updates', 'wp-bulk-seo-aeo'),
            __('License & Updates', 'wp-bulk-seo-aeo'),
            'manage_options',
            'wp-bulk-seo-aeo-license',
            [$this, 'render_license']
        );
        
        // Redirections (Rank Math Pro style)
        add_submenu_page(
            'wp-bulk-seo-aeo',
            __('Redirections', 'wp-bulk-seo-aeo'),
            __('리다이렉션', 'wp-bulk-seo-aeo'),
            'manage_options',
            'wp-bulk-seo-aeo-redirections',
            [$this, 'render_redirections']
        );
    }

    /**
     * Enqueue admin assets
     * [v25.0.0] v25 디자인 시스템 통합
     */
    public function enqueue_admin_assets($hook) {
        // Only on plugin pages
        if (strpos($hook, 'wp-bulk-seo') === false && $hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }

        // [v25.0.0] v25 디자인 시스템 CSS
        $shared_path = dirname(dirname(WP_BULK_SEO_AEO_PATH)) . '/acf-css-really-simple-style-management-center-master';
        if (file_exists($shared_path . '/assets/css/jj-design-system-v25.css')) {
            wp_enqueue_style(
                'jj-design-system-v25',
                plugin_dir_url(dirname(dirname(__FILE__))) . 'acf-css-really-simple-style-management-center-master/assets/css/jj-design-system-v25.css',
                [],
                '25.0.0'
            );
        }

        // [v25.0.0] SEO v25 UI CSS
        wp_enqueue_style(
            'jj-seo-v25-ui',
            WP_BULK_SEO_AEO_URL . 'assets/css/jj-seo-v25-ui.css',
            ['jj-design-system-v25'],
            WP_BULK_SEO_AEO_VERSION
        );

        // [v25.0.0] 애니메이션 시스템
        if (file_exists($shared_path . '/assets/css/jj-animations-v25.css')) {
            wp_enqueue_style(
                'jj-animations-v25',
                plugin_dir_url(dirname(dirname(__FILE__))) . 'acf-css-really-simple-style-management-center-master/assets/css/jj-animations-v25.css',
                ['jj-design-system-v25'],
                '25.0.0'
            );
        }

        // [v25.0.0] 다크 모드 시스템
        if (file_exists($shared_path . '/assets/js/jj-dark-mode-v25.js')) {
            wp_enqueue_script(
                'jj-dark-mode-v25',
                plugin_dir_url(dirname(dirname(__FILE__))) . 'acf-css-really-simple-style-management-center-master/assets/js/jj-dark-mode-v25.js',
                ['jquery'],
                '25.0.0',
                true
            );
        }

        // [v25.0.0] 시각화 시스템 (Chart.js)
        if (file_exists($shared_path . '/assets/js/jj-visualizations-v25.js')) {
            wp_enqueue_script(
                'jj-visualizations-v25',
                plugin_dir_url(dirname(dirname(__FILE__))) . 'acf-css-really-simple-style-management-center-master/assets/js/jj-visualizations-v25.js',
                ['jquery'],
                '25.0.0',
                true
            );
        }

        // Legacy admin styles (하위 호환성)
        wp_enqueue_style(
            'wp-bulk-seo-aeo-admin',
            WP_BULK_SEO_AEO_URL . 'assets/css/admin.css',
            ['jj-seo-v25-ui'],
            WP_BULK_SEO_AEO_VERSION
        );

        // Scripts
        wp_enqueue_script(
            'wp-bulk-seo-aeo-admin',
            WP_BULK_SEO_AEO_URL . 'assets/js/admin.js',
            ['jquery', 'wp-api-fetch'],
            WP_BULK_SEO_AEO_VERSION,
            true
        );

        wp_localize_script('wp-bulk-seo-aeo-admin', 'wpBulkSEOAEO', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('wp-bulk-seo-aeo/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'i18n' => [
                'analyzing' => __('분석 중...', 'wp-bulk-seo-aeo'),
                'optimizing' => __('최적화 중...', 'wp-bulk-seo-aeo'),
                'success' => __('성공!', 'wp-bulk-seo-aeo'),
                'error' => __('오류 발생', 'wp-bulk-seo-aeo'),
            ],
        ]);
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        if (class_exists('WP_Bulk_SEO_AEO_REST_API')) {
            $rest_api = new WP_Bulk_SEO_AEO_REST_API();
            $rest_api->register_routes();
        }
    }

    /**
     * Output meta tags in head
     */
    public function output_meta_tags() {
        if (!is_singular()) {
            return;
        }

        $post_id = get_the_ID();
        // Meta tag output logic
        do_action('wp_bulk_seo_meta_tags', $post_id);
    }

    /**
     * Output schema in head
     */
    public function output_schema() {
        if (!get_option('wp_bulk_seo_aeo_enable_schema', true)) {
            return;
        }

        if ($this->schema_generator) {
            $this->schema_generator->output_schema();
        }
    }

    /**
     * Get plugin statistics
     *
     * @return array Statistics
     */
    public function get_statistics() {
        if ($this->bulk_optimizer) {
            return $this->bulk_optimizer->get_statistics();
        }
        return [];
    }

    /**
     * Run scheduled analysis
     */
    public function run_scheduled_analysis() {
        if ($this->bulk_optimizer) {
            $this->bulk_optimizer->run_scheduled_analysis();
        }
    }

    /**
     * Run ranking monitoring
     */
    public function run_ranking_monitoring() {
        if (class_exists('WP_Bulk_SEO_AEO_Ranking_Monitor')) {
            $monitor = new WP_Bulk_SEO_AEO_Ranking_Monitor();
            $monitor->run_monitoring();
        }
    }

    // Admin page render methods
    public function render_dashboard() {
        $file = WP_BULK_SEO_AEO_PATH . 'includes/admin/views/dashboard.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__('Dashboard', 'wp-bulk-seo-aeo') . '</h1><p>' . esc_html__('Dashboard view file not found.', 'wp-bulk-seo-aeo') . '</p></div>';
        }
    }

    public function render_analyzer() {
        $file = WP_BULK_SEO_AEO_PATH . 'includes/admin/views/analyzer.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__('Bulk Analyzer', 'wp-bulk-seo-aeo') . '</h1><p>' . esc_html__('Analyzer view file not found.', 'wp-bulk-seo-aeo') . '</p></div>';
        }
    }

    public function render_optimizer() {
        $file = WP_BULK_SEO_AEO_PATH . 'includes/admin/views/optimizer.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__('Bulk Optimizer', 'wp-bulk-seo-aeo') . '</h1><p>' . esc_html__('Optimizer view file not found.', 'wp-bulk-seo-aeo') . '</p></div>';
        }
    }

    public function render_aeo() {
        $file = WP_BULK_SEO_AEO_PATH . 'includes/admin/views/aeo.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__('AEO Settings', 'wp-bulk-seo-aeo') . '</h1><p>' . esc_html__('AEO view file not found.', 'wp-bulk-seo-aeo') . '</p></div>';
        }
    }

    public function render_schema() {
        $file = WP_BULK_SEO_AEO_PATH . 'includes/admin/views/schema.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__('Schema Manager', 'wp-bulk-seo-aeo') . '</h1><p>' . esc_html__('Schema view file not found.', 'wp-bulk-seo-aeo') . '</p></div>';
        }
    }

    public function render_factors() {
        $file = WP_BULK_SEO_AEO_PATH . 'includes/admin/views/factors.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__('Ranking Factors', 'wp-bulk-seo-aeo') . '</h1><p>' . esc_html__('Factors view file not found.', 'wp-bulk-seo-aeo') . '</p></div>';
        }
    }

    public function render_settings() {
        $file = WP_BULK_SEO_AEO_PATH . 'includes/admin/views/settings.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__('Settings', 'wp-bulk-seo-aeo') . '</h1><p>' . esc_html__('Settings view file not found.', 'wp-bulk-seo-aeo') . '</p></div>';
        }
    }

    public function render_license() {
        $file = WP_BULK_SEO_AEO_PATH . 'includes/admin/views/license.php';
        if (file_exists($file)) {
            include $file;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__('License & Updates', 'wp-bulk-seo-aeo') . '</h1><p>' . esc_html__('License view file not found.', 'wp-bulk-seo-aeo') . '</p></div>';
        }
    }

    /**
     * AJAX: 1-Click SEO Setup
     */
    public function ajax_1click_setup() {
        check_ajax_referer('wp_rest', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
        }

        // Apply basic SEO settings
        update_option('wp_bulk_seo_aeo_auto_analyze', true);
        update_option('wp_bulk_seo_aeo_analyze_on_save', true);
        update_option('wp_bulk_seo_aeo_enable_schema', true);
        update_option('wp_bulk_seo_aeo_enable_sitemap', true);

        wp_send_json_success(['message' => '1-Click SEO setup completed']);
    }

    /**
     * AJAX: Bulk Analyze
     */
    public function ajax_bulk_analyze() {
        // Nonce 검증
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wp_rest')) {
            wp_send_json_error([
                'message' => __('보안 검증에 실패했습니다. 페이지를 새로고침하고 다시 시도해주세요.', 'wp-bulk-seo-aeo')
            ]);
            return;
        }

        // 권한 확인
        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('권한이 없습니다.', 'wp-bulk-seo-aeo')
            ]);
            return;
        }

        try {
            $post_types = isset($_POST['post_types']) && is_array($_POST['post_types']) 
                ? array_map('sanitize_text_field', $_POST['post_types']) 
                : ['post', 'page'];
            $batch_size = isset($_POST['batch_size']) ? intval($_POST['batch_size']) : 10;

            if (!$this->bulk_optimizer) {
                wp_send_json_error([
                    'message' => __('벌크 최적화기가 초기화되지 않았습니다.', 'wp-bulk-seo-aeo')
                ]);
                return;
            }

            $args = [
                'post_type' => $post_types,
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'fields' => 'ids',
            ];

            $result = $this->bulk_optimizer->analyze_all($args);

            wp_send_json_success($result);
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => sprintf(__('오류가 발생했습니다: %s', 'wp-bulk-seo-aeo'), $e->getMessage())
            ]);
        } catch (Error $e) {
            wp_send_json_error([
                'message' => sprintf(__('치명적 오류가 발생했습니다: %s', 'wp-bulk-seo-aeo'), $e->getMessage())
            ]);
        }
    }

    /**
     * AJAX: Bulk Optimize
     */
    public function ajax_bulk_optimize() {
        check_ajax_referer('wp_rest', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
        }

        $optimizations = isset($_POST['optimizations']) ? array_map('sanitize_text_field', $_POST['optimizations']) : [];

        if (!$this->bulk_optimizer) {
            wp_send_json_error(['message' => 'Bulk optimizer not initialized']);
        }

        // Get all posts
        $posts = get_posts([
            'post_type' => get_option('wp_bulk_seo_aeo_post_types', ['post', 'page']),
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);

        $results = [];
        foreach ($posts as $post_id) {
            $result = $this->bulk_optimizer->optimize_post($post_id, ['optimizations' => $optimizations]);
            $results[$post_id] = $result;
        }

        wp_send_json_success([
            'total' => count($posts),
            'results' => $results,
        ]);
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new \Exception('Cannot unserialize singleton');
    }
}

/**
 * Get plugin instance
 */
function wp_bulk_seo_aeo() {
    return WP_Bulk_SEO_AEO::instance();
}

// Initialize plugin (오류 처리 포함)
try {
    wp_bulk_seo_aeo();
} catch (Exception $e) {
    if (function_exists('error_log')) {
        error_log('WP Bulk SEO & AEO Init Error: ' . $e->getMessage());
    }
    // 오류가 발생해도 플러그인은 활성화되도록 함
} catch (Error $e) {
    if (function_exists('error_log')) {
        error_log('WP Bulk SEO & AEO Init Fatal Error: ' . $e->getMessage());
    }
    // 치명적 오류가 발생해도 플러그인은 활성화되도록 함
}

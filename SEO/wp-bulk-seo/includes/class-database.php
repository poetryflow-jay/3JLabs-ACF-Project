<?php
/**
 * WP Bulk SEO - Database
 *
 * @package WP_Bulk_SEO
 * @subpackage Database
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Database Class
 */
class WP_Bulk_SEO_Database {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Database version
     */
    const DB_VERSION = '2.1.0'; // [v2.1.0] A/B Tests, Keywords tables added

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
        $this->maybe_upgrade();
    }

    /**
     * Create tables
     */
    public function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $tables = [];

        // Scores table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bulk_seo_scores (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            post_type varchar(50) NOT NULL DEFAULT 'post',
            overall_score int(3) NOT NULL DEFAULT 0,
            grade varchar(5) NOT NULL DEFAULT 'F',
            module_scores longtext,
            issues longtext,
            analyzed_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY post_id (post_id),
            KEY post_type (post_type),
            KEY grade (grade),
            KEY overall_score (overall_score)
        ) $charset_collate;";

        // Issues table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bulk_seo_issues (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            issue_type varchar(100) NOT NULL,
            severity varchar(20) NOT NULL DEFAULT 'warning',
            message text NOT NULL,
            module varchar(50),
            status varchar(20) NOT NULL DEFAULT 'open',
            created_at datetime NOT NULL,
            resolved_at datetime,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY issue_type (issue_type),
            KEY severity (severity),
            KEY status (status)
        ) $charset_collate;";

        // Ranking factors table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bulk_seo_ranking_factors (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            factor_id varchar(100) NOT NULL,
            module varchar(50) NOT NULL,
            name varchar(200) NOT NULL,
            description text,
            priority varchar(10) NOT NULL DEFAULT 'P2',
            weight decimal(5,2) NOT NULL DEFAULT 1.00,
            scoring_logic text,
            thresholds text,
            source varchar(50),
            is_active tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY factor_id (factor_id),
            KEY module (module),
            KEY priority (priority),
            KEY is_active (is_active)
        ) $charset_collate;";

        // PageSpeed cache table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bulk_seo_pagespeed_cache (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            url_hash varchar(64) NOT NULL,
            url text NOT NULL,
            strategy varchar(20) NOT NULL DEFAULT 'mobile',
            data longtext,
            fetched_at datetime NOT NULL,
            expires_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY url_strategy (url_hash, strategy),
            KEY expires_at (expires_at)
        ) $charset_collate;";

        // AEO scores table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bulk_seo_aeo_scores (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            overall_score int(3) NOT NULL DEFAULT 0,
            grade varchar(5) NOT NULL DEFAULT 'F',
            factor_scores longtext,
            recommendations longtext,
            analyzed_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY post_id (post_id)
        ) $charset_collate;";

        // FAQ table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bulk_seo_faq (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            question text NOT NULL,
            answer text NOT NULL,
            sort_order int(11) NOT NULL DEFAULT 0,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY is_active (is_active)
        ) $charset_collate;";

        // [v2.0.0] Score history table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wp_bulk_seo_score_history (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            score decimal(5,2) NOT NULL,
            grade varchar(5) NOT NULL,
            modules longtext,
            recorded_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY recorded_at (recorded_at)
        ) $charset_collate;";

        // [v2.0.0] Alerts table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wp_bulk_seo_alerts (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            alert_type varchar(50) NOT NULL,
            severity varchar(20) NOT NULL DEFAULT 'medium',
            data longtext,
            created_at datetime NOT NULL,
            is_read tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            KEY alert_type (alert_type),
            KEY severity (severity),
            KEY is_read (is_read),
            KEY created_at (created_at)
        ) $charset_collate;";

        // [v2.0.0] Notifications table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wp_bulk_seo_notifications (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            alert_type varchar(50) NOT NULL,
            severity varchar(20) NOT NULL DEFAULT 'medium',
            data longtext,
            message text,
            is_read tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY is_read (is_read),
            KEY created_at (created_at)
        ) $charset_collate;";

        // [v2.0.0] Improvements table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wp_bulk_seo_improvements (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            previous_score decimal(5,2) NOT NULL,
            new_score decimal(5,2) NOT NULL,
            improvement decimal(5,2) NOT NULL,
            recorded_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY recorded_at (recorded_at)
        ) $charset_collate;";

        // [v2.0.0] Operation logs table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bulk_seo_log (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            operation_type varchar(50) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'running',
            total_items int(11) NOT NULL DEFAULT 0,
            processed_items int(11) NOT NULL DEFAULT 0,
            success_items int(11) NOT NULL DEFAULT 0,
            failed_items int(11) NOT NULL DEFAULT 0,
            details longtext,
            started_at datetime NOT NULL,
            completed_at datetime,
            user_id bigint(20) unsigned,
            PRIMARY KEY (id),
            KEY operation_type (operation_type),
            KEY status (status),
            KEY started_at (started_at)
        ) $charset_collate;";

        // [v2.1.0] A/B Tests table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wp_bulk_seo_ab_tests (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            variant_a longtext NOT NULL,
            variant_b longtext NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'running',
            traffic_split tinyint(3) NOT NULL DEFAULT 50,
            variant_a_impressions int(11) NOT NULL DEFAULT 0,
            variant_a_clicks int(11) NOT NULL DEFAULT 0,
            variant_b_impressions int(11) NOT NULL DEFAULT 0,
            variant_b_clicks int(11) NOT NULL DEFAULT 0,
            winner varchar(1),
            started_at datetime NOT NULL,
            ended_at datetime,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY status (status),
            KEY started_at (started_at)
        ) $charset_collate;";

        // [v2.1.0] Keywords table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wp_bulk_seo_keywords (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            keyword varchar(255) NOT NULL,
            keyword_type varchar(50) NOT NULL DEFAULT 'extracted',
            frequency int(11) NOT NULL DEFAULT 0,
            tfidf_score decimal(10,4) NOT NULL DEFAULT 0,
            relevance_score decimal(5,2) NOT NULL DEFAULT 0,
            is_primary tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY keyword (keyword),
            KEY is_primary (is_primary)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        foreach ($tables as $sql) {
            dbDelta($sql);
        }

        update_option('wp_bulk_seo_db_version', self::DB_VERSION);
    }

    /**
     * Maybe upgrade database
     */
    private function maybe_upgrade() {
        $installed_version = get_option('wp_bulk_seo_db_version', '0');

        if (version_compare($installed_version, self::DB_VERSION, '<')) {
            $this->create_tables();
        }
    }

    /**
     * Drop tables (for uninstall)
     */
    public static function drop_tables() {
        global $wpdb;

        $tables = [
            $wpdb->prefix . 'bulk_seo_scores',
            $wpdb->prefix . 'bulk_seo_issues',
            $wpdb->prefix . 'bulk_seo_ranking_factors',
            $wpdb->prefix . 'bulk_seo_pagespeed_cache',
            $wpdb->prefix . 'bulk_seo_aeo_scores',
            $wpdb->prefix . 'bulk_seo_faq',
            $wpdb->prefix . 'wp_bulk_seo_score_history',
            $wpdb->prefix . 'wp_bulk_seo_alerts',
            $wpdb->prefix . 'wp_bulk_seo_notifications',
            $wpdb->prefix . 'wp_bulk_seo_improvements',
            $wpdb->prefix . 'bulk_seo_log',
        ];

        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }

        delete_option('wp_bulk_seo_db_version');
    }

    /**
     * Clean expired cache
     */
    public function clean_expired_cache() {
        global $wpdb;

        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}bulk_seo_pagespeed_cache WHERE expires_at < %s",
            current_time('mysql')
        ));
    }

    /**
     * Get table name
     */
    public function get_table($name) {
        global $wpdb;
        return $wpdb->prefix . 'bulk_seo_' . $name;
    }
}

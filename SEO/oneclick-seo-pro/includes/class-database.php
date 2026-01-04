<?php
/**
 * 1-Click SEO Pro - Database
 *
 * @package OneClick_SEO_Pro
 * @subpackage Database
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Database Class
 */
class OneClick_SEO_Pro_Database {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Database version
     */
    const DB_VERSION = '1.0.0';

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
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}oneclick_seo_scores (
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
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}oneclick_seo_issues (
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
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}oneclick_seo_ranking_factors (
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
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}oneclick_seo_pagespeed_cache (
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
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}oneclick_seo_aeo_scores (
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
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}oneclick_seo_faq (
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

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        foreach ($tables as $sql) {
            dbDelta($sql);
        }

        update_option('oneclick_seo_pro_db_version', self::DB_VERSION);
    }

    /**
     * Maybe upgrade database
     */
    private function maybe_upgrade() {
        $installed_version = get_option('oneclick_seo_pro_db_version', '0');

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
            $wpdb->prefix . 'oneclick_seo_scores',
            $wpdb->prefix . 'oneclick_seo_issues',
            $wpdb->prefix . 'oneclick_seo_ranking_factors',
            $wpdb->prefix . 'oneclick_seo_pagespeed_cache',
            $wpdb->prefix . 'oneclick_seo_aeo_scores',
            $wpdb->prefix . 'oneclick_seo_faq',
        ];

        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }

        delete_option('oneclick_seo_pro_db_version');
    }

    /**
     * Clean expired cache
     */
    public function clean_expired_cache() {
        global $wpdb;

        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}oneclick_seo_pagespeed_cache WHERE expires_at < %s",
            current_time('mysql')
        ));
    }

    /**
     * Get table name
     */
    public function get_table($name) {
        global $wpdb;
        return $wpdb->prefix . 'oneclick_seo_' . $name;
    }
}

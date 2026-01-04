<?php
/**
 * WP Bulk SEO Master - Database
 *
 * @package WP_Bulk_SEO_Master
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Master_Database {

    private static $instance = null;
    const DB_VERSION = '1.0.0';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->maybe_upgrade();
    }

    public function create_tables() {
        global $wpdb;
        
        // $wpdb가 없으면 테이블 생성을 건너뜀
        if (!isset($wpdb) || !is_object($wpdb)) {
            return;
        }
        
        // WordPress가 로드되지 않았을 경우 대비
        if (!function_exists('dbDelta')) {
            if (defined('ABSPATH') && file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            } else {
                // dbDelta가 없으면 테이블 생성을 건너뜀
                return;
            }
        }
        
        $charset_collate = $wpdb->get_charset_collate();

        $tables = [];

        // Licenses table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}seo_master_licenses (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            license_key varchar(64) NOT NULL,
            license_type varchar(20) NOT NULL DEFAULT 'standard',
            customer_email varchar(100) NOT NULL,
            customer_name varchar(100),
            max_sites int(11) NOT NULL DEFAULT 1,
            active_sites int(11) NOT NULL DEFAULT 0,
            status varchar(20) NOT NULL DEFAULT 'active',
            expires_at datetime,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY license_key (license_key),
            KEY customer_email (customer_email),
            KEY status (status)
        ) $charset_collate;";

        // License activations table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}seo_master_activations (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            license_id bigint(20) unsigned NOT NULL,
            site_url varchar(255) NOT NULL,
            site_name varchar(100),
            site_type varchar(20) NOT NULL DEFAULT 'wordpress',
            activation_token varchar(64) NOT NULL,
            ip_address varchar(45),
            status varchar(20) NOT NULL DEFAULT 'active',
            last_check datetime,
            activated_at datetime NOT NULL,
            deactivated_at datetime,
            PRIMARY KEY (id),
            KEY license_id (license_id),
            KEY site_url (site_url(191)),
            KEY activation_token (activation_token),
            KEY status (status)
        ) $charset_collate;";

        // Remote sites table (non-WP sites managed by Master)
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}seo_master_remote_sites (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            site_url varchar(255) NOT NULL,
            site_name varchar(100),
            site_type varchar(20) NOT NULL DEFAULT 'other',
            snippet_key varchar(64) NOT NULL,
            settings longtext,
            last_crawl datetime,
            seo_score int(3),
            status varchar(20) NOT NULL DEFAULT 'active',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY site_url (site_url(191)),
            KEY snippet_key (snippet_key),
            KEY status (status)
        ) $charset_collate;";

        // Keywords table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}seo_master_keywords (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            site_id bigint(20) unsigned,
            keyword varchar(255) NOT NULL,
            target_url varchar(500),
            search_engine varchar(20) NOT NULL DEFAULT 'google',
            country varchar(5) NOT NULL DEFAULT 'us',
            language varchar(5) NOT NULL DEFAULT 'en',
            device varchar(10) NOT NULL DEFAULT 'desktop',
            tags varchar(255),
            status varchar(20) NOT NULL DEFAULT 'active',
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY site_id (site_id),
            KEY keyword (keyword(191)),
            KEY status (status)
        ) $charset_collate;";

        // Rank history table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}seo_master_rank_history (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            keyword_id bigint(20) unsigned NOT NULL,
            position int(11),
            previous_position int(11),
            url varchar(500),
            serp_features varchar(255),
            checked_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY keyword_id (keyword_id),
            KEY checked_at (checked_at)
        ) $charset_collate;";

        // SEO audits table
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}seo_master_audits (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            site_id bigint(20) unsigned NOT NULL,
            audit_type varchar(20) NOT NULL DEFAULT 'full',
            overall_score int(3),
            technical_score int(3),
            content_score int(3),
            performance_score int(3),
            issues_count int(11) NOT NULL DEFAULT 0,
            audit_data longtext,
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY site_id (site_id),
            KEY created_at (created_at)
        ) $charset_collate;";

        // Bulk changes queue
        $tables[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}seo_master_bulk_queue (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            site_id bigint(20) unsigned NOT NULL,
            change_type varchar(50) NOT NULL,
            target_selector varchar(255),
            old_value text,
            new_value text,
            status varchar(20) NOT NULL DEFAULT 'pending',
            applied_at datetime,
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY site_id (site_id),
            KEY status (status)
        ) $charset_collate;";

        // dbDelta 함수가 있는지 확인 후 실행
        if (!function_exists('dbDelta')) {
            if (defined('ABSPATH') && file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            } else {
                // dbDelta가 없으면 테이블 생성을 건너뜀
                return;
            }
        }

        // 테이블 생성 시도 (오류 처리 포함)
        try {
            foreach ($tables as $sql) {
                if (function_exists('dbDelta')) {
                    dbDelta($sql);
                }
            }
        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('WP Bulk SEO Master Table Creation Error: ' . $e->getMessage());
            }
            // 테이블 생성 오류가 발생해도 계속 진행
        }

        // DB 버전 업데이트
        if (function_exists('update_option')) {
            update_option('wp_bulk_seo_master_db_version', self::DB_VERSION);
        }
    }

    private function maybe_upgrade() {
        $installed = get_option('wp_bulk_seo_master_db_version', '0');
        if (version_compare($installed, self::DB_VERSION, '<')) {
            $this->create_tables();
        }
    }

    public function get_table($name) {
        global $wpdb;
        return $wpdb->prefix . 'seo_master_' . $name;
    }
}

<?php
/**
 * 데이터베이스 테이블 관리 클래스
 * 
 * @package JJ_LicenseManagerincludes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Database {
    
    /**
     * 데이터베이스 테이블 생성
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // 라이센스 테이블 (인덱스 최적화)
        $table_licenses = $wpdb->prefix . 'jj_licenses';
        $sql_licenses = "CREATE TABLE IF NOT EXISTS {$table_licenses} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            license_key varchar(100) NOT NULL,
            license_type varchar(20) NOT NULL,
            user_id bigint(20) UNSIGNED NOT NULL,
            order_id bigint(20) UNSIGNED DEFAULT NULL,
            order_item_id bigint(20) UNSIGNED DEFAULT NULL,
            product_id bigint(20) UNSIGNED DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            created_at datetime NOT NULL,
            expires_at datetime DEFAULT NULL,
            max_activations int(11) DEFAULT 1,
            activation_count int(11) DEFAULT 0,
            purchase_date datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY license_key (license_key),
            KEY user_id (user_id),
            KEY order_id (order_id),
            KEY order_item_id (order_item_id),
            KEY product_id (product_id),
            KEY status (status),
            KEY expires_at (expires_at),
            KEY status_expires (status, expires_at),
            KEY license_type_status (license_type, status)
        ) {$charset_collate};";
        
        // 라이센스 활성화 테이블 (인덱스 최적화)
        $table_activations = $wpdb->prefix . 'jj_license_activations';
        $sql_activations = "CREATE TABLE IF NOT EXISTS {$table_activations} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            license_id bigint(20) UNSIGNED NOT NULL,
            site_id varchar(64) NOT NULL,
            site_url varchar(255) NOT NULL,
            activated_at datetime NOT NULL,
            deactivated_at datetime DEFAULT NULL,
            is_active tinyint(1) DEFAULT 1,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY license_site (license_id, site_id),
            KEY license_id (license_id),
            KEY site_id (site_id),
            KEY is_active (is_active),
            KEY license_active (license_id, is_active),
            KEY activated_at (activated_at)
        ) {$charset_collate};";
        
        // 라이센스 히스토리 테이블
        $table_history = $wpdb->prefix . 'jj_license_history';
        $sql_history = "CREATE TABLE IF NOT EXISTS {$table_history} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            license_id bigint(20) UNSIGNED NOT NULL,
            action varchar(50) NOT NULL,
            description text DEFAULT NULL,
            performed_by bigint(20) UNSIGNED DEFAULT NULL,
            performed_at datetime NOT NULL,
            ip_address varchar(45) DEFAULT NULL,
            metadata longtext DEFAULT NULL,
            PRIMARY KEY (id),
            KEY license_id (license_id),
            KEY action (action),
            KEY performed_at (performed_at)
        ) {$charset_collate};";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_licenses );
        dbDelta( $sql_activations );
        dbDelta( $sql_history );
    }
    
    /**
     * 테이블 이름 가져오기
     */
    public static function get_table_name( $table ) {
        global $wpdb;
        
        $tables = array(
            'licenses' => $wpdb->prefix . 'jj_licenses',
            'activations' => $wpdb->prefix . 'jj_license_activations',
            'history' => $wpdb->prefix . 'jj_license_history',
        );
        
        return isset( $tables[ $table ] ) ? $tables[ $table ] : null;
    }
}


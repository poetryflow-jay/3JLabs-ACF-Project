<?php
/**
 * 데이터베이스 관리 클래스
 *
 * @package ACF_User_Journey_Analytics
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_UJA_Database {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 테이블 생성
     */
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // 트래픽 추적 테이블
        $table_tracking = $wpdb->prefix . 'acf_uja_tracking';
        $sql_tracking = "CREATE TABLE $table_tracking (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            visitor_id varchar(64) NOT NULL,
            session_id varchar(64) NOT NULL,
            user_id bigint(20) unsigned DEFAULT 0,

            -- UTM 파라미터
            utm_source varchar(255) DEFAULT '',
            utm_medium varchar(255) DEFAULT '',
            utm_campaign varchar(255) DEFAULT '',
            utm_term varchar(255) DEFAULT '',
            utm_content varchar(255) DEFAULT '',

            -- 글로벌 광고 클릭 ID
            gclid varchar(255) DEFAULT '',
            gad_source varchar(50) DEFAULT '',
            gbraid varchar(255) DEFAULT '',
            wbraid varchar(255) DEFAULT '',
            fbclid varchar(255) DEFAULT '',
            ttclid varchar(255) DEFAULT '',
            twclid varchar(255) DEFAULT '',
            msclkid varchar(255) DEFAULT '',
            li_fat_id varchar(255) DEFAULT '',
            epik varchar(255) DEFAULT '',
            sccid varchar(255) DEFAULT '',

            -- 네이버 광고 파라미터
            n_media varchar(100) DEFAULT '',
            n_query varchar(255) DEFAULT '',
            n_rank varchar(20) DEFAULT '',
            n_ad_group varchar(100) DEFAULT '',
            n_ad varchar(100) DEFAULT '',
            n_keyword_id varchar(100) DEFAULT '',
            n_keyword varchar(255) DEFAULT '',
            n_campaign_type varchar(50) DEFAULT '',
            n_ad_group_type varchar(50) DEFAULT '',
            n_match varchar(20) DEFAULT '',
            n_mall_pid varchar(100) DEFAULT '',
            n_mall_id varchar(100) DEFAULT '',
            na_source varchar(100) DEFAULT '',
            na_medium varchar(100) DEFAULT '',
            na_campaign varchar(255) DEFAULT '',
            na_adset varchar(255) DEFAULT '',
            na_ad varchar(255) DEFAULT '',

            -- 카카오 광고 파라미터
            kakao_campaign varchar(255) DEFAULT '',
            kakao_adgrp varchar(255) DEFAULT '',
            kakao_creative varchar(255) DEFAULT '',
            kakao_keyword varchar(255) DEFAULT '',
            dkwid varchar(100) DEFAULT '',
            dkw varchar(255) DEFAULT '',

            -- 기타 광고 파라미터
            dablead varchar(255) DEFAULT '',
            ti varchar(255) DEFAULT '',

            -- 광고 소스 감지 결과
            detected_ad_source varchar(100) DEFAULT '',
            detected_ad_platform varchar(100) DEFAULT '',
            detected_ad_type varchar(100) DEFAULT '',
            ad_source_details text,

            -- 리퍼러 정보
            referrer_url text,
            referrer_domain varchar(255) DEFAULT '',
            referrer_type varchar(50) DEFAULT '',
            referrer_source varchar(100) DEFAULT '',
            referrer_name varchar(100) DEFAULT '',

            -- 랜딩 페이지
            landing_page text,
            landing_path varchar(500) DEFAULT '',

            -- 디바이스/환경
            device_type varchar(20) DEFAULT '',
            screen_resolution varchar(20) DEFAULT '',
            viewport varchar(20) DEFAULT '',
            user_language varchar(10) DEFAULT '',
            user_agent text,

            -- 사용자 여정 데이터
            touch_count int(11) DEFAULT 1,
            touch_history longtext,
            dwell_time int(11) DEFAULT 0,

            -- 전환 데이터
            converted tinyint(1) DEFAULT 0,
            conversion_at datetime DEFAULT NULL,
            conversion_value decimal(15,2) DEFAULT 0.00,
            conversion_type varchar(50) DEFAULT '',

            -- 타임스탬프
            first_touch_at datetime NOT NULL,
            last_touch_at datetime NOT NULL,

            PRIMARY KEY (id),
            KEY visitor_id (visitor_id),
            KEY session_id (session_id),
            KEY user_id (user_id),
            KEY first_touch_at (first_touch_at),
            KEY referrer_type (referrer_type),
            KEY detected_ad_source (detected_ad_source),
            KEY detected_ad_platform (detected_ad_platform),
            KEY converted (converted),
            KEY utm_source (utm_source(100)),
            KEY utm_campaign (utm_campaign(100))
        ) $charset_collate;";

        dbDelta( $sql_tracking );

        // 광고 비용 테이블 (ROI 계산용)
        $table_costs = $wpdb->prefix . 'acf_uja_ad_costs';
        $sql_costs = "CREATE TABLE $table_costs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            date date NOT NULL,
            platform varchar(100) NOT NULL,
            campaign varchar(255) DEFAULT '',
            cost decimal(15,2) DEFAULT 0.00,
            impressions bigint(20) DEFAULT 0,
            clicks bigint(20) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY date (date),
            KEY platform (platform),
            UNIQUE KEY date_platform_campaign (date, platform, campaign(100))
        ) $charset_collate;";

        dbDelta( $sql_costs );

        update_option( 'acf_uja_db_version', ACF_UJA_VERSION );
    }

    /**
     * 테이블 이름 반환
     */
    public function get_tracking_table() {
        global $wpdb;
        return $wpdb->prefix . 'acf_uja_tracking';
    }

    public function get_costs_table() {
        global $wpdb;
        return $wpdb->prefix . 'acf_uja_ad_costs';
    }
}

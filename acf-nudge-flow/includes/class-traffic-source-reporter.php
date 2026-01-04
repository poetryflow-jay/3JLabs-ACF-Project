<?php
/**
 * 트래픽 소스 리포트 생성 클래스
 *
 * 트래픽 소스별 분석 데이터 조회 및 리포트 생성
 *
 * @package ACF_Nudge_Flow
 * @since 22.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Nudge_Flow_Traffic_Source_Reporter {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        // AJAX 핸들러
        add_action( 'wp_ajax_acf_nf_get_traffic_report', array( $this, 'ajax_get_traffic_report' ) );
        add_action( 'wp_ajax_acf_nf_export_traffic_report', array( $this, 'ajax_export_traffic_report' ) );
    }

    /**
     * 기간별 트래픽 요약 조회
     */
    public function get_traffic_summary( $start_date, $end_date ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        $result = $wpdb->get_row( $wpdb->prepare(
            "SELECT
                COUNT(DISTINCT visitor_id) as total_visitors,
                COUNT(*) as total_sessions,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as total_conversions,
                SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as total_revenue,
                AVG(touch_count) as avg_touchpoints
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s",
            $start_date,
            $end_date
        ) );

        if ( $result ) {
            $result->conversion_rate = $result->total_sessions > 0
                ? round( ( $result->total_conversions / $result->total_sessions ) * 100, 2 )
                : 0;
            $result->revenue_per_visitor = $result->total_visitors > 0
                ? round( $result->total_revenue / $result->total_visitors, 2 )
                : 0;
        }

        return $result;
    }

    /**
     * 리퍼러 유형별 분석
     */
    public function get_referrer_type_breakdown( $start_date, $end_date, $limit = 20 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                COALESCE(NULLIF(referrer_type, ''), 'direct') as referrer_type,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue,
                ROUND(SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as conversion_rate
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
            GROUP BY referrer_type
            ORDER BY sessions DESC
            LIMIT %d",
            $start_date,
            $end_date,
            $limit
        ) );
    }

    /**
     * 광고 소스별 성과 분석
     */
    public function get_ad_source_performance( $start_date, $end_date, $limit = 20 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                detected_ad_source as source,
                detected_ad_platform as platform,
                detected_ad_type as type,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue,
                ROUND(SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as conversion_rate,
                ROUND(SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) / COUNT(*), 2) as revenue_per_session
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
                AND detected_ad_source IS NOT NULL
                AND detected_ad_source != ''
            GROUP BY detected_ad_source, detected_ad_platform, detected_ad_type
            ORDER BY revenue DESC, sessions DESC
            LIMIT %d",
            $start_date,
            $end_date,
            $limit
        ) );
    }

    /**
     * UTM 캠페인별 성과 분석
     */
    public function get_utm_campaign_performance( $start_date, $end_date, $limit = 20 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                utm_source,
                utm_medium,
                utm_campaign,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue,
                ROUND(SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as conversion_rate,
                ROUND(SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) / COUNT(*), 2) as revenue_per_session
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
                AND utm_source IS NOT NULL
                AND utm_source != ''
            GROUP BY utm_source, utm_medium, utm_campaign
            ORDER BY revenue DESC, sessions DESC
            LIMIT %d",
            $start_date,
            $end_date,
            $limit
        ) );
    }

    /**
     * 네이버 광고 상세 분석
     */
    public function get_naver_ads_breakdown( $start_date, $end_date ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                n_media,
                n_keyword,
                n_campaign_type,
                CASE
                    WHEN n_mall_pid IS NOT NULL AND n_mall_pid != '' THEN 'shopping_search'
                    WHEN na_source = 'gfa' THEN 'gfa'
                    WHEN n_keyword IS NOT NULL AND n_keyword != '' THEN 'search_ad'
                    WHEN n_campaign_type = 'brand' THEN 'brand_search'
                    ELSE 'other'
                END as ad_type,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue,
                ROUND(SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as conversion_rate
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
                AND (n_media IS NOT NULL AND n_media != '')
            GROUP BY n_media, n_keyword, n_campaign_type, ad_type
            ORDER BY revenue DESC, sessions DESC
            LIMIT 50",
            $start_date,
            $end_date
        ) );
    }

    /**
     * 카카오 광고 상세 분석
     */
    public function get_kakao_ads_breakdown( $start_date, $end_date ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                kakao_campaign,
                kakao_adgrp,
                kakao_keyword,
                CASE
                    WHEN dkwid IS NOT NULL AND dkwid != '' THEN 'keyword_ad'
                    ELSE 'moment'
                END as ad_type,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue,
                ROUND(SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as conversion_rate
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
                AND ((kakao_campaign IS NOT NULL AND kakao_campaign != '') OR (dkwid IS NOT NULL AND dkwid != ''))
            GROUP BY kakao_campaign, kakao_adgrp, kakao_keyword, ad_type
            ORDER BY revenue DESC, sessions DESC
            LIMIT 50",
            $start_date,
            $end_date
        ) );
    }

    /**
     * AI 리퍼러 분석
     */
    public function get_ai_referrer_breakdown( $start_date, $end_date ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                referrer_source,
                referrer_name,
                referrer_type,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
                AND referrer_type IN ('ai_search', 'ai_chatbot')
            GROUP BY referrer_source, referrer_name, referrer_type
            ORDER BY sessions DESC",
            $start_date,
            $end_date
        ) );
    }

    /**
     * 검색 엔진별 분석
     */
    public function get_search_engine_breakdown( $start_date, $end_date ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                referrer_source,
                referrer_name,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue,
                ROUND(SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as conversion_rate
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
                AND referrer_type = 'search'
            GROUP BY referrer_source, referrer_name
            ORDER BY sessions DESC",
            $start_date,
            $end_date
        ) );
    }

    /**
     * 소셜 미디어별 분석
     */
    public function get_social_media_breakdown( $start_date, $end_date ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                referrer_source,
                referrer_name,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue,
                ROUND(SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as conversion_rate
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
                AND referrer_type = 'social'
            GROUP BY referrer_source, referrer_name
            ORDER BY sessions DESC",
            $start_date,
            $end_date
        ) );
    }

    /**
     * 일별 트래픽 트렌드
     */
    public function get_daily_traffic_trend( $start_date, $end_date ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                DATE(first_touch_at) as date,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
            GROUP BY DATE(first_touch_at)
            ORDER BY date ASC",
            $start_date,
            $end_date
        ) );
    }

    /**
     * 소스별 일별 트렌드
     */
    public function get_daily_source_trend( $start_date, $end_date, $source_type = 'referrer_type' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        $group_column = sanitize_key( $source_type );

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                DATE(first_touch_at) as date,
                $group_column as source,
                COUNT(*) as sessions,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
            GROUP BY DATE(first_touch_at), $group_column
            ORDER BY date ASC, sessions DESC",
            $start_date,
            $end_date
        ) );
    }

    /**
     * 랜딩 페이지별 분석
     */
    public function get_landing_page_breakdown( $start_date, $end_date, $limit = 20 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                landing_path,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue,
                ROUND(SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as conversion_rate
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
                AND landing_path IS NOT NULL
                AND landing_path != ''
            GROUP BY landing_path
            ORDER BY sessions DESC
            LIMIT %d",
            $start_date,
            $end_date,
            $limit
        ) );
    }

    /**
     * 디바이스별 분석
     */
    public function get_device_breakdown( $start_date, $end_date ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                COALESCE(NULLIF(device_type, ''), 'unknown') as device,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue,
                ROUND(SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as conversion_rate
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
            GROUP BY device
            ORDER BY sessions DESC",
            $start_date,
            $end_date
        ) );
    }

    /**
     * 클릭 ID별 성과 분석 (자동 태깅)
     */
    public function get_click_id_performance( $start_date, $end_date ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        $click_id_columns = array(
            'gclid' => 'Google Ads',
            'fbclid' => 'Meta (Facebook)',
            'ttclid' => 'TikTok',
            'msclkid' => 'Microsoft Ads',
            'twclid' => 'Twitter (X)',
            'li_fat_id' => 'LinkedIn',
            'epik' => 'Pinterest',
            'sccid' => 'Snapchat',
        );

        $results = array();

        foreach ( $click_id_columns as $column => $platform_name ) {
            $row = $wpdb->get_row( $wpdb->prepare(
                "SELECT
                    COUNT(*) as sessions,
                    COUNT(DISTINCT visitor_id) as visitors,
                    SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                    SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue
                FROM $table
                WHERE first_touch_at BETWEEN %s AND %s
                    AND $column IS NOT NULL
                    AND $column != ''",
                $start_date,
                $end_date
            ) );

            if ( $row && $row->sessions > 0 ) {
                $results[] = (object) array(
                    'click_id_type' => $column,
                    'platform' => $platform_name,
                    'sessions' => $row->sessions,
                    'visitors' => $row->visitors,
                    'conversions' => $row->conversions,
                    'revenue' => $row->revenue,
                    'conversion_rate' => $row->sessions > 0 ? round( ( $row->conversions / $row->sessions ) * 100, 2 ) : 0,
                );
            }
        }

        // 세션 수로 정렬
        usort( $results, function( $a, $b ) {
            return $b->sessions - $a->sessions;
        } );

        return $results;
    }

    /**
     * 전환 경로 분석 (멀티터치 어트리뷰션)
     */
    public function get_conversion_paths( $start_date, $end_date, $limit = 20 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        // 전환된 방문자의 터치포인트 수 분포
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                touch_count,
                COUNT(*) as conversions,
                SUM(conversion_value) as revenue,
                AVG(conversion_value) as avg_order_value
            FROM $table
            WHERE conversion_at BETWEEN %s AND %s
                AND converted = 1
            GROUP BY touch_count
            ORDER BY touch_count ASC
            LIMIT %d",
            $start_date,
            $end_date,
            $limit
        ) );
    }

    /**
     * AJAX: 트래픽 리포트 조회
     */
    public function ajax_get_traffic_report() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        $report_type = isset( $_POST['report_type'] ) ? sanitize_text_field( $_POST['report_type'] ) : 'summary';
        $start_date = isset( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : date( 'Y-m-d', strtotime( '-7 days' ) );
        $end_date = isset( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : date( 'Y-m-d' );

        $start_datetime = $start_date . ' 00:00:00';
        $end_datetime = $end_date . ' 23:59:59';

        $data = array();

        switch ( $report_type ) {
            case 'summary':
                $data = $this->get_traffic_summary( $start_datetime, $end_datetime );
                break;
            case 'referrer_types':
                $data = $this->get_referrer_type_breakdown( $start_datetime, $end_datetime );
                break;
            case 'ad_sources':
                $data = $this->get_ad_source_performance( $start_datetime, $end_datetime );
                break;
            case 'utm_campaigns':
                $data = $this->get_utm_campaign_performance( $start_datetime, $end_datetime );
                break;
            case 'naver_ads':
                $data = $this->get_naver_ads_breakdown( $start_datetime, $end_datetime );
                break;
            case 'kakao_ads':
                $data = $this->get_kakao_ads_breakdown( $start_datetime, $end_datetime );
                break;
            case 'ai_referrers':
                $data = $this->get_ai_referrer_breakdown( $start_datetime, $end_datetime );
                break;
            case 'daily_trend':
                $data = $this->get_daily_traffic_trend( $start_datetime, $end_datetime );
                break;
            case 'click_ids':
                $data = $this->get_click_id_performance( $start_datetime, $end_datetime );
                break;
            case 'landing_pages':
                $data = $this->get_landing_page_breakdown( $start_datetime, $end_datetime );
                break;
            default:
                wp_send_json_error( array( 'message' => '유효하지 않은 리포트 유형입니다.' ) );
                return;
        }

        wp_send_json_success( array(
            'report_type' => $report_type,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'data' => $data,
        ) );
    }

    /**
     * AJAX: 트래픽 리포트 CSV 내보내기
     */
    public function ajax_export_traffic_report() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( '권한이 없습니다.' );
        }

        $report_type = isset( $_GET['report_type'] ) ? sanitize_text_field( $_GET['report_type'] ) : 'summary';
        $start_date = isset( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : date( 'Y-m-d', strtotime( '-7 days' ) );
        $end_date = isset( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : date( 'Y-m-d' );

        $start_datetime = $start_date . ' 00:00:00';
        $end_datetime = $end_date . ' 23:59:59';

        $data = array();

        switch ( $report_type ) {
            case 'ad_sources':
                $data = $this->get_ad_source_performance( $start_datetime, $end_datetime, 1000 );
                $filename = "ad_sources_report_{$start_date}_{$end_date}.csv";
                $headers = array( 'Source', 'Platform', 'Type', 'Sessions', 'Visitors', 'Conversions', 'Revenue', 'Conversion Rate' );
                break;
            case 'utm_campaigns':
                $data = $this->get_utm_campaign_performance( $start_datetime, $end_datetime, 1000 );
                $filename = "utm_campaigns_report_{$start_date}_{$end_date}.csv";
                $headers = array( 'Source', 'Medium', 'Campaign', 'Sessions', 'Visitors', 'Conversions', 'Revenue', 'Conversion Rate' );
                break;
            default:
                wp_die( '지원하지 않는 리포트 유형입니다.' );
        }

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=' . $filename );

        $output = fopen( 'php://output', 'w' );

        // BOM for UTF-8
        fprintf( $output, chr(0xEF) . chr(0xBB) . chr(0xBF) );

        // 헤더 출력
        fputcsv( $output, $headers );

        // 데이터 출력
        foreach ( $data as $row ) {
            fputcsv( $output, (array) $row );
        }

        fclose( $output );
        exit;
    }

    /**
     * 종합 리포트 데이터 조회 (대시보드용)
     */
    public function get_dashboard_report( $start_date, $end_date ) {
        return array(
            'summary' => $this->get_traffic_summary( $start_date, $end_date ),
            'referrer_types' => $this->get_referrer_type_breakdown( $start_date, $end_date, 10 ),
            'ad_sources' => $this->get_ad_source_performance( $start_date, $end_date, 10 ),
            'utm_campaigns' => $this->get_utm_campaign_performance( $start_date, $end_date, 10 ),
            'ai_referrers' => $this->get_ai_referrer_breakdown( $start_date, $end_date ),
            'click_ids' => $this->get_click_id_performance( $start_date, $end_date ),
            'daily_trend' => $this->get_daily_traffic_trend( $start_date, $end_date ),
        );
    }

    /**
     * [v22.9.1] 트래픽 소스별 퍼널 분석
     *
     * 각 트래픽 소스에서 전환까지의 단계별 이탈율 분석
     */
    public function get_source_funnel_analysis( $start_date, $end_date, $source_type = 'referrer_type' ) {
        global $wpdb;
        $utm_table = $wpdb->prefix . 'acf_nf_utm_tracking';
        $funnel_table = $wpdb->prefix . 'acf_nf_funnel_progress';

        // 유효한 소스 유형만 허용
        $valid_source_types = array( 'referrer_type', 'detected_ad_source', 'detected_ad_platform', 'utm_source' );
        if ( ! in_array( $source_type, $valid_source_types ) ) {
            $source_type = 'referrer_type';
        }

        // 소스별 퍼널 단계 데이터 조회
        $query = $wpdb->prepare(
            "SELECT
                COALESCE(NULLIF(u.{$source_type}, ''), 'direct') as traffic_source,
                COUNT(DISTINCT u.visitor_id) as total_visitors,

                -- 퍼널 단계별 카운트
                SUM(CASE WHEN u.landing_page IS NOT NULL THEN 1 ELSE 0 END) as step_1_landing,
                SUM(CASE WHEN u.touch_count >= 2 THEN 1 ELSE 0 END) as step_2_engagement,
                SUM(CASE WHEN u.dwell_time >= 30 THEN 1 ELSE 0 END) as step_3_interest,
                SUM(CASE WHEN u.touch_count >= 3 OR u.dwell_time >= 60 THEN 1 ELSE 0 END) as step_4_consideration,
                SUM(CASE WHEN u.converted = 1 THEN 1 ELSE 0 END) as step_5_conversion,

                -- 전환 가치
                SUM(CASE WHEN u.converted = 1 THEN u.conversion_value ELSE 0 END) as total_revenue,

                -- 평균 터치포인트
                ROUND(AVG(u.touch_count), 2) as avg_touchpoints,

                -- 평균 체류 시간
                ROUND(AVG(u.dwell_time), 0) as avg_dwell_time

            FROM $utm_table u
            WHERE u.first_touch_at BETWEEN %s AND %s
            GROUP BY traffic_source
            ORDER BY total_visitors DESC
            LIMIT 20",
            $start_date,
            $end_date
        );

        $results = $wpdb->get_results( $query );

        // 각 단계별 전환율 계산
        foreach ( $results as &$row ) {
            $row->step_1_rate = $row->total_visitors > 0 ? round( ( $row->step_1_landing / $row->total_visitors ) * 100, 1 ) : 0;
            $row->step_2_rate = $row->step_1_landing > 0 ? round( ( $row->step_2_engagement / $row->step_1_landing ) * 100, 1 ) : 0;
            $row->step_3_rate = $row->step_2_engagement > 0 ? round( ( $row->step_3_interest / $row->step_2_engagement ) * 100, 1 ) : 0;
            $row->step_4_rate = $row->step_3_interest > 0 ? round( ( $row->step_4_consideration / $row->step_3_interest ) * 100, 1 ) : 0;
            $row->step_5_rate = $row->step_4_consideration > 0 ? round( ( $row->step_5_conversion / $row->step_4_consideration ) * 100, 1 ) : 0;

            $row->overall_conversion_rate = $row->total_visitors > 0 ? round( ( $row->step_5_conversion / $row->total_visitors ) * 100, 2 ) : 0;
            $row->revenue_per_visitor = $row->total_visitors > 0 ? round( $row->total_revenue / $row->total_visitors, 0 ) : 0;
        }

        return $results;
    }

    /**
     * [v22.9.1] 광고 플랫폼별 ROI 분석
     */
    public function get_ad_roi_analysis( $start_date, $end_date ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';
        $cost_table = $wpdb->prefix . 'acf_nf_ad_costs';

        // 광고 비용 테이블이 존재하는지 확인
        $cost_table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$cost_table'" ) === $cost_table;

        $query = $wpdb->prepare(
            "SELECT
                COALESCE(NULLIF(detected_ad_platform, ''), 'Unknown') as platform,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(CASE WHEN converted = 1 THEN conversion_value ELSE 0 END) as revenue
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
                AND (detected_ad_source IS NOT NULL AND detected_ad_source != '')
            GROUP BY platform
            ORDER BY revenue DESC",
            $start_date,
            $end_date
        );

        $results = $wpdb->get_results( $query );

        // 비용 데이터 조회 (테이블이 있는 경우)
        $costs = array();
        if ( $cost_table_exists ) {
            $cost_data = $wpdb->get_results( $wpdb->prepare(
                "SELECT platform, SUM(cost) as total_cost
                FROM $cost_table
                WHERE date BETWEEN %s AND %s
                GROUP BY platform",
                date( 'Y-m-d', strtotime( $start_date ) ),
                date( 'Y-m-d', strtotime( $end_date ) )
            ) );

            foreach ( $cost_data as $row ) {
                $costs[ $row->platform ] = (float) $row->total_cost;
            }
        }

        // ROI 계산
        foreach ( $results as &$row ) {
            $row->cost = isset( $costs[ $row->platform ] ) ? $costs[ $row->platform ] : 0;
            $row->profit = $row->revenue - $row->cost;
            $row->roi = $row->cost > 0 ? round( ( ( $row->revenue - $row->cost ) / $row->cost ) * 100, 1 ) : ( $row->revenue > 0 ? 100 : 0 );
            $row->roas = $row->cost > 0 ? round( $row->revenue / $row->cost, 2 ) : 0;
            $row->cpa = $row->conversions > 0 ? round( $row->cost / $row->conversions, 0 ) : 0;
            $row->cpc = $row->sessions > 0 ? round( $row->cost / $row->sessions, 0 ) : 0;
            $row->conversion_rate = $row->sessions > 0 ? round( ( $row->conversions / $row->sessions ) * 100, 2 ) : 0;
        }

        return $results;
    }

    /**
     * [v22.9.1] 전환 경로 분석 (Multi-Touch Attribution)
     */
    public function get_conversion_path_analysis( $start_date, $end_date ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        // 전환된 방문자의 터치 히스토리 분석
        $query = $wpdb->prepare(
            "SELECT
                COALESCE(NULLIF(detected_ad_platform, ''), COALESCE(NULLIF(referrer_type, ''), 'direct')) as first_touch_source,
                touch_history,
                conversion_value
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
                AND converted = 1
                AND touch_history IS NOT NULL
            LIMIT 1000",
            $start_date,
            $end_date
        );

        $results = $wpdb->get_results( $query );

        // 터치 경로 집계
        $path_stats = array();
        $source_contribution = array();

        foreach ( $results as $row ) {
            $history = json_decode( $row->touch_history, true );
            if ( ! is_array( $history ) ) continue;

            // 경로 생성 (최대 5단계)
            $sources = array_slice( array_column( $history, 'source' ), 0, 5 );
            if ( empty( $sources ) ) {
                $sources = array( $row->first_touch_source );
            }
            $path = implode( ' → ', $sources );

            if ( ! isset( $path_stats[ $path ] ) ) {
                $path_stats[ $path ] = array( 'count' => 0, 'revenue' => 0 );
            }
            $path_stats[ $path ]['count']++;
            $path_stats[ $path ]['revenue'] += (float) $row->conversion_value;

            // 소스별 기여도 (Linear Attribution)
            $source_count = count( $sources );
            $attribution = (float) $row->conversion_value / $source_count;
            foreach ( $sources as $source ) {
                if ( ! isset( $source_contribution[ $source ] ) ) {
                    $source_contribution[ $source ] = array( 'attributed_revenue' => 0, 'touch_count' => 0 );
                }
                $source_contribution[ $source ]['attributed_revenue'] += $attribution;
                $source_contribution[ $source ]['touch_count']++;
            }
        }

        // 상위 전환 경로
        arsort( $path_stats );
        $top_paths = array_slice( $path_stats, 0, 10, true );

        // 소스별 기여도 정렬
        uasort( $source_contribution, function( $a, $b ) {
            return $b['attributed_revenue'] <=> $a['attributed_revenue'];
        } );

        return array(
            'top_conversion_paths' => $top_paths,
            'source_attribution' => $source_contribution,
        );
    }
}

// 인스턴스 생성
ACF_Nudge_Flow_Traffic_Source_Reporter::instance();

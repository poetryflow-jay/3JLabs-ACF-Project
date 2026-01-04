<?php
/**
 * 트래픽 리포터 클래스
 *
 * @package ACF_User_Journey_Analytics
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_UJA_Traffic_Reporter {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 실시간 데이터
     */
    public function get_realtime_data() {
        global $wpdb;

        $db = ACF_UJA_Database::instance();
        $table = $db->get_tracking_table();

        $now = current_time( 'mysql' );
        $five_minutes_ago = date( 'Y-m-d H:i:s', strtotime( '-5 minutes', strtotime( $now ) ) );
        $today_start = date( 'Y-m-d 00:00:00' );

        // 5분 내 활성 사용자
        $realtime_users = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT visitor_id) FROM $table WHERE last_touch_at >= %s",
            $five_minutes_ago
        ) );

        // 오늘 방문자
        $today_visitors = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT visitor_id) FROM $table WHERE first_touch_at >= %s",
            $today_start
        ) );

        // 오늘 세션
        $today_sessions = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM $table WHERE first_touch_at >= %s",
            $today_start
        ) );

        // 상위 5개 소스
        $top_sources = $wpdb->get_results( $wpdb->prepare(
            "SELECT referrer_type, COUNT(*) as count
            FROM $table
            WHERE first_touch_at >= %s
            GROUP BY referrer_type
            ORDER BY count DESC
            LIMIT 5",
            $today_start
        ) );

        return array(
            'realtime_users' => (int) $realtime_users,
            'today_visitors' => (int) $today_visitors,
            'today_sessions' => (int) $today_sessions,
            'top_sources' => $top_sources,
            'timestamp' => $now,
        );
    }

    /**
     * 트래픽 요약
     */
    public function get_traffic_summary( $start_date, $end_date ) {
        global $wpdb;

        $db = ACF_UJA_Database::instance();
        $table = $db->get_tracking_table();

        $summary = $wpdb->get_row( $wpdb->prepare(
            "SELECT
                COUNT(DISTINCT visitor_id) as unique_visitors,
                COUNT(DISTINCT session_id) as sessions,
                COUNT(*) as pageviews,
                AVG(touch_count) as avg_pages_per_session,
                AVG(dwell_time) as avg_dwell_time,
                SUM(converted) as conversions,
                SUM(conversion_value) as total_conversion_value
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s",
            $start_date,
            $end_date
        ) );

        // 전환율 계산
        $conversion_rate = 0;
        if ( $summary && $summary->sessions > 0 ) {
            $conversion_rate = ( $summary->conversions / $summary->sessions ) * 100;
        }

        return array(
            'unique_visitors' => (int) $summary->unique_visitors,
            'sessions' => (int) $summary->sessions,
            'pageviews' => (int) $summary->pageviews,
            'avg_pages_per_session' => round( (float) $summary->avg_pages_per_session, 2 ),
            'avg_dwell_time' => (int) $summary->avg_dwell_time,
            'conversions' => (int) $summary->conversions,
            'total_conversion_value' => (float) $summary->total_conversion_value,
            'conversion_rate' => round( $conversion_rate, 2 ),
        );
    }

    /**
     * 리퍼러 분석
     */
    public function get_referrer_breakdown( $start_date, $end_date ) {
        global $wpdb;

        $db = ACF_UJA_Database::instance();
        $table = $db->get_tracking_table();

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                referrer_type,
                referrer_source,
                referrer_name,
                COUNT(DISTINCT visitor_id) as visitors,
                COUNT(DISTINCT session_id) as sessions,
                SUM(converted) as conversions,
                SUM(conversion_value) as revenue
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
            GROUP BY referrer_type, referrer_source, referrer_name
            ORDER BY sessions DESC",
            $start_date,
            $end_date
        ) );
    }

    /**
     * 일별 트래픽
     */
    public function get_daily_traffic( $start_date, $end_date ) {
        global $wpdb;

        $db = ACF_UJA_Database::instance();
        $table = $db->get_tracking_table();

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                DATE(first_touch_at) as date,
                COUNT(DISTINCT visitor_id) as visitors,
                COUNT(DISTINCT session_id) as sessions,
                SUM(converted) as conversions
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
            GROUP BY DATE(first_touch_at)
            ORDER BY date ASC",
            $start_date,
            $end_date
        ) );
    }

    /**
     * 시간별 트래픽
     */
    public function get_hourly_traffic( $start_date, $end_date ) {
        global $wpdb;

        $db = ACF_UJA_Database::instance();
        $table = $db->get_tracking_table();

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                HOUR(first_touch_at) as hour,
                COUNT(DISTINCT session_id) as sessions
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
            GROUP BY HOUR(first_touch_at)
            ORDER BY hour ASC",
            $start_date,
            $end_date
        ) );
    }

    /**
     * 디바이스 통계
     */
    public function get_device_stats( $start_date, $end_date ) {
        global $wpdb;

        $db = ACF_UJA_Database::instance();
        $table = $db->get_tracking_table();

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                device_type,
                COUNT(DISTINCT session_id) as sessions,
                SUM(converted) as conversions
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
            GROUP BY device_type
            ORDER BY sessions DESC",
            $start_date,
            $end_date
        ) );
    }

    /**
     * UTM 캠페인 분석
     */
    public function get_utm_campaign_stats( $start_date, $end_date ) {
        global $wpdb;

        $db = ACF_UJA_Database::instance();
        $table = $db->get_tracking_table();

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                utm_source,
                utm_medium,
                utm_campaign,
                COUNT(DISTINCT visitor_id) as visitors,
                COUNT(DISTINCT session_id) as sessions,
                SUM(converted) as conversions,
                SUM(conversion_value) as revenue
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
            AND utm_source != ''
            GROUP BY utm_source, utm_medium, utm_campaign
            ORDER BY sessions DESC
            LIMIT 50",
            $start_date,
            $end_date
        ) );
    }

    /**
     * 광고 플랫폼별 분석
     */
    public function get_ad_platform_stats( $start_date, $end_date ) {
        global $wpdb;

        $db = ACF_UJA_Database::instance();
        $table = $db->get_tracking_table();

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT
                detected_ad_platform as platform,
                detected_ad_source as source,
                COUNT(DISTINCT visitor_id) as visitors,
                COUNT(DISTINCT session_id) as sessions,
                SUM(converted) as conversions,
                SUM(conversion_value) as revenue
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
            AND detected_ad_source != ''
            GROUP BY detected_ad_platform, detected_ad_source
            ORDER BY sessions DESC",
            $start_date,
            $end_date
        ) );
    }

    /**
     * 퍼널 분석
     */
    public function get_funnel_analysis( $start_date, $end_date ) {
        global $wpdb;

        $db = ACF_UJA_Database::instance();
        $table = $db->get_tracking_table();

        // 총 세션
        $total_sessions = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM $table WHERE first_touch_at BETWEEN %s AND %s",
            $start_date, $end_date
        ) );

        // 2페이지 이상 조회
        $engaged = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM $table WHERE first_touch_at BETWEEN %s AND %s AND touch_count >= 2",
            $start_date, $end_date
        ) );

        // 3페이지 이상 조회
        $interested = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM $table WHERE first_touch_at BETWEEN %s AND %s AND touch_count >= 3",
            $start_date, $end_date
        ) );

        // 5페이지 이상 조회
        $considering = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM $table WHERE first_touch_at BETWEEN %s AND %s AND touch_count >= 5",
            $start_date, $end_date
        ) );

        // 전환
        $converted = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM $table WHERE first_touch_at BETWEEN %s AND %s AND converted = 1",
            $start_date, $end_date
        ) );

        return array(
            array( 'step' => '방문 (Landing)', 'count' => (int) $total_sessions, 'rate' => 100 ),
            array( 'step' => '관심 (Engagement)', 'count' => (int) $engaged, 'rate' => $total_sessions > 0 ? round( ( $engaged / $total_sessions ) * 100, 1 ) : 0 ),
            array( 'step' => '탐색 (Interest)', 'count' => (int) $interested, 'rate' => $total_sessions > 0 ? round( ( $interested / $total_sessions ) * 100, 1 ) : 0 ),
            array( 'step' => '검토 (Consideration)', 'count' => (int) $considering, 'rate' => $total_sessions > 0 ? round( ( $considering / $total_sessions ) * 100, 1 ) : 0 ),
            array( 'step' => '전환 (Conversion)', 'count' => (int) $converted, 'rate' => $total_sessions > 0 ? round( ( $converted / $total_sessions ) * 100, 1 ) : 0 ),
        );
    }

    /**
     * ROI 분석
     */
    public function get_roi_analysis( $start_date, $end_date ) {
        global $wpdb;

        $db = ACF_UJA_Database::instance();
        $tracking_table = $db->get_tracking_table();
        $costs_table = $db->get_costs_table();

        // 광고 비용
        $costs = $wpdb->get_results( $wpdb->prepare(
            "SELECT platform, SUM(cost) as total_cost, SUM(clicks) as total_clicks
            FROM $costs_table
            WHERE date BETWEEN %s AND %s
            GROUP BY platform",
            $start_date, $end_date
        ), OBJECT_K );

        // 광고 성과
        $performance = $wpdb->get_results( $wpdb->prepare(
            "SELECT
                detected_ad_source as platform,
                COUNT(DISTINCT session_id) as sessions,
                SUM(converted) as conversions,
                SUM(conversion_value) as revenue
            FROM $tracking_table
            WHERE first_touch_at BETWEEN %s AND %s
            AND detected_ad_source != ''
            GROUP BY detected_ad_source",
            $start_date, $end_date
        ) );

        $roi_data = array();
        foreach ( $performance as $row ) {
            $platform = $row->platform;
            $cost = isset( $costs[ $platform ] ) ? (float) $costs[ $platform ]->total_cost : 0;
            $revenue = (float) $row->revenue;
            $conversions = (int) $row->conversions;
            $sessions = (int) $row->sessions;

            $roi = $cost > 0 ? ( ( $revenue - $cost ) / $cost ) * 100 : 0;
            $roas = $cost > 0 ? $revenue / $cost : 0;
            $cpa = $conversions > 0 ? $cost / $conversions : 0;
            $cpc = $sessions > 0 ? $cost / $sessions : 0;

            $roi_data[] = array(
                'platform' => $platform,
                'cost' => $cost,
                'revenue' => $revenue,
                'conversions' => $conversions,
                'sessions' => $sessions,
                'roi' => round( $roi, 2 ),
                'roas' => round( $roas, 2 ),
                'cpa' => round( $cpa, 2 ),
                'cpc' => round( $cpc, 2 ),
            );
        }

        return $roi_data;
    }

    /**
     * CSV 생성
     */
    public function generate_csv( $period = '7days' ) {
        global $wpdb;

        $db = ACF_UJA_Database::instance();
        $table = $db->get_tracking_table();

        // 날짜 범위 계산
        switch ( $period ) {
            case '30days':
                $start_date = date( 'Y-m-d', strtotime( '-30 days' ) );
                break;
            case '90days':
                $start_date = date( 'Y-m-d', strtotime( '-90 days' ) );
                break;
            default:
                $start_date = date( 'Y-m-d', strtotime( '-7 days' ) );
        }
        $end_date = date( 'Y-m-d 23:59:59' );

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table WHERE first_touch_at BETWEEN %s AND %s ORDER BY first_touch_at DESC",
            $start_date, $end_date
        ), ARRAY_A );

        if ( empty( $results ) ) {
            return '';
        }

        // UTF-8 BOM
        $csv = "\xEF\xBB\xBF";

        // 헤더
        $headers = array(
            '방문일시', '방문자ID', '세션ID', 'UTM소스', 'UTM매체', 'UTM캠페인',
            '리퍼러유형', '리퍼러소스', '광고플랫폼', '디바이스', '페이지뷰', '전환여부', '전환금액'
        );
        $csv .= implode( ',', $headers ) . "\n";

        // 데이터
        foreach ( $results as $row ) {
            $line = array(
                $this->csv_escape( $row['first_touch_at'] ),
                $this->csv_escape( $row['visitor_id'] ),
                $this->csv_escape( $row['session_id'] ),
                $this->csv_escape( $row['utm_source'] ),
                $this->csv_escape( $row['utm_medium'] ),
                $this->csv_escape( $row['utm_campaign'] ),
                $this->csv_escape( $row['referrer_type'] ),
                $this->csv_escape( $row['referrer_source'] ),
                $this->csv_escape( $row['detected_ad_platform'] ),
                $this->csv_escape( $row['device_type'] ),
                $row['touch_count'],
                $row['converted'] ? '전환' : '-',
                $row['conversion_value'],
            );
            $csv .= implode( ',', $line ) . "\n";
        }

        return $csv;
    }

    /**
     * CSV 이스케이프
     */
    private function csv_escape( $value ) {
        if ( strpos( $value, ',' ) !== false || strpos( $value, '"' ) !== false || strpos( $value, "\n" ) !== false ) {
            return '"' . str_replace( '"', '""', $value ) . '"';
        }
        return $value;
    }
}

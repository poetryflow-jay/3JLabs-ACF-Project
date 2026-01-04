<?php
/**
 * 넛지 플로우 네이티브 애널리틱스 시스템
 * 
 * 외부 분석 도구 없이도 Clarity/GA 수준의 분석 기능 제공
 * - 히트맵 (클릭/터치/호버)
 * - 스크롤 맵
 * - 유저 저니 맵
 * - 이벤트 기반 분석
 * - 세션 리플레이 데이터
 * 
 * @package ACF_Nudge_Flow
 * @since 22.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Nudge_Flow_Native_Analytics {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 데이터베이스 테이블
     */
    private $table_events;
    private $table_heatmap;
    private $table_scrollmap;
    private $table_journeys;
    private $table_sessions;
    private $table_page_stats;

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
        global $wpdb;
        
        $prefix = $wpdb->prefix . 'acf_nf_';
        $this->table_events = $prefix . 'analytics_events';
        $this->table_heatmap = $prefix . 'analytics_heatmap';
        $this->table_scrollmap = $prefix . 'analytics_scrollmap';
        $this->table_journeys = $prefix . 'analytics_journeys';
        $this->table_sessions = $prefix . 'analytics_sessions';
        $this->table_page_stats = $prefix . 'analytics_page_stats';

        $this->init_hooks();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // AJAX 핸들러
        add_action( 'wp_ajax_acf_nf_record_event', array( $this, 'ajax_record_event' ) );
        add_action( 'wp_ajax_nopriv_acf_nf_record_event', array( $this, 'ajax_record_event' ) );
        
        add_action( 'wp_ajax_acf_nf_record_heatmap', array( $this, 'ajax_record_heatmap' ) );
        add_action( 'wp_ajax_nopriv_acf_nf_record_heatmap', array( $this, 'ajax_record_heatmap' ) );
        
        add_action( 'wp_ajax_acf_nf_record_scroll', array( $this, 'ajax_record_scroll' ) );
        add_action( 'wp_ajax_nopriv_acf_nf_record_scroll', array( $this, 'ajax_record_scroll' ) );
        
        add_action( 'wp_ajax_acf_nf_record_journey', array( $this, 'ajax_record_journey' ) );
        add_action( 'wp_ajax_nopriv_acf_nf_record_journey', array( $this, 'ajax_record_journey' ) );
        
        add_action( 'wp_ajax_acf_nf_end_session', array( $this, 'ajax_end_session' ) );
        add_action( 'wp_ajax_nopriv_acf_nf_end_session', array( $this, 'ajax_end_session' ) );

        // REST API
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

        // 일일 통계 집계
        add_action( 'acf_nf_daily_stats_aggregation', array( $this, 'aggregate_daily_stats' ) );
        if ( ! wp_next_scheduled( 'acf_nf_daily_stats_aggregation' ) ) {
            wp_schedule_event( strtotime( 'tomorrow midnight' ), 'daily', 'acf_nf_daily_stats_aggregation' );
        }

        // 오래된 데이터 정리 (90일 이상)
        add_action( 'acf_nf_cleanup_old_analytics', array( $this, 'cleanup_old_data' ) );
        if ( ! wp_next_scheduled( 'acf_nf_cleanup_old_analytics' ) ) {
            wp_schedule_event( time(), 'weekly', 'acf_nf_cleanup_old_analytics' );
        }
    }

    /**
     * 데이터베이스 테이블 생성
     */
    public function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // 1. 이벤트 테이블 (이벤트 중심 분석)
        $sql_events = "CREATE TABLE IF NOT EXISTS {$this->table_events} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_id varchar(100) NOT NULL,
            visitor_id varchar(100) NOT NULL,
            user_id bigint(20) unsigned DEFAULT 0,
            
            -- 이벤트 정보
            event_name varchar(100) NOT NULL,
            event_category varchar(50) DEFAULT 'general',
            event_action varchar(100) DEFAULT NULL,
            event_label varchar(255) DEFAULT NULL,
            event_value decimal(15,2) DEFAULT 0,
            
            -- 컨텍스트
            page_url varchar(500) NOT NULL,
            page_title varchar(255) DEFAULT NULL,
            page_type varchar(50) DEFAULT NULL,
            referrer varchar(500) DEFAULT NULL,
            
            -- 요소 정보 (클릭/인터랙션 시)
            element_type varchar(50) DEFAULT NULL,
            element_id varchar(100) DEFAULT NULL,
            element_class varchar(255) DEFAULT NULL,
            element_text varchar(500) DEFAULT NULL,
            element_href varchar(500) DEFAULT NULL,
            element_xpath varchar(1000) DEFAULT NULL,
            
            -- 위치 정보
            position_x int(11) DEFAULT NULL,
            position_y int(11) DEFAULT NULL,
            viewport_width int(11) DEFAULT NULL,
            viewport_height int(11) DEFAULT NULL,
            scroll_depth int(11) DEFAULT NULL,
            
            -- 디바이스 정보
            device_type varchar(20) DEFAULT NULL,
            browser varchar(50) DEFAULT NULL,
            os varchar(50) DEFAULT NULL,
            screen_width int(11) DEFAULT NULL,
            screen_height int(11) DEFAULT NULL,
            
            -- 타이밍
            time_on_page int(11) DEFAULT 0,
            timestamp_ms bigint(20) DEFAULT NULL,
            
            -- 추가 데이터
            custom_data longtext DEFAULT NULL,
            
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            
            PRIMARY KEY (id),
            KEY idx_session (session_id),
            KEY idx_visitor (visitor_id),
            KEY idx_user (user_id),
            KEY idx_event_name (event_name),
            KEY idx_event_category (event_category),
            KEY idx_page_url (page_url(191)),
            KEY idx_created_at (created_at),
            KEY idx_composite (session_id, event_name, created_at)
        ) $charset_collate;";

        dbDelta( $sql_events );

        // 2. 히트맵 테이블 (클릭/터치/호버)
        $sql_heatmap = "CREATE TABLE IF NOT EXISTS {$this->table_heatmap} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_id varchar(100) NOT NULL,
            visitor_id varchar(100) NOT NULL,
            
            -- 페이지 정보
            page_url varchar(500) NOT NULL,
            page_path varchar(255) NOT NULL,
            
            -- 인터랙션 타입
            interaction_type enum('click', 'touch', 'hover', 'rage_click', 'dead_click') NOT NULL,
            
            -- 위치 정보 (절대/상대)
            x_absolute int(11) NOT NULL,
            y_absolute int(11) NOT NULL,
            x_relative float NOT NULL,
            y_relative float NOT NULL,
            
            -- 페이지 크기 (정규화용)
            page_width int(11) NOT NULL,
            page_height int(11) NOT NULL,
            viewport_width int(11) NOT NULL,
            viewport_height int(11) NOT NULL,
            
            -- 요소 정보
            element_selector varchar(500) DEFAULT NULL,
            element_tag varchar(50) DEFAULT NULL,
            element_text varchar(255) DEFAULT NULL,
            is_clickable tinyint(1) DEFAULT 0,
            
            -- 디바이스
            device_type varchar(20) NOT NULL,
            
            -- 타이밍
            hover_duration int(11) DEFAULT NULL,
            
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            
            PRIMARY KEY (id),
            KEY idx_page_path (page_path),
            KEY idx_interaction (interaction_type),
            KEY idx_device (device_type),
            KEY idx_created_at (created_at),
            KEY idx_coordinates (page_path, x_relative, y_relative)
        ) $charset_collate;";

        dbDelta( $sql_heatmap );

        // 3. 스크롤맵 테이블
        $sql_scrollmap = "CREATE TABLE IF NOT EXISTS {$this->table_scrollmap} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_id varchar(100) NOT NULL,
            visitor_id varchar(100) NOT NULL,
            
            -- 페이지 정보
            page_url varchar(500) NOT NULL,
            page_path varchar(255) NOT NULL,
            page_height int(11) NOT NULL,
            viewport_height int(11) NOT NULL,
            
            -- 스크롤 데이터
            max_scroll_depth int(11) NOT NULL,
            max_scroll_percent float NOT NULL,
            
            -- 스크롤 구간별 체류 시간 (JSON: {\"0-25\": 5000, \"25-50\": 3000, ...})
            scroll_sections_time longtext DEFAULT NULL,
            
            -- 폴드 라인
            fold_line int(11) DEFAULT NULL,
            below_fold_time int(11) DEFAULT 0,
            
            -- 스크롤 패턴
            scroll_ups int(11) DEFAULT 0,
            scroll_downs int(11) DEFAULT 0,
            scroll_speed_avg float DEFAULT NULL,
            
            -- 읽기 패턴
            reading_time int(11) DEFAULT 0,
            attention_time int(11) DEFAULT 0,
            
            -- 디바이스
            device_type varchar(20) NOT NULL,
            
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            
            PRIMARY KEY (id),
            KEY idx_page_path (page_path),
            KEY idx_session (session_id),
            KEY idx_device (device_type),
            KEY idx_created_at (created_at)
        ) $charset_collate;";

        dbDelta( $sql_scrollmap );

        // 4. 유저 저니 테이블
        $sql_journeys = "CREATE TABLE IF NOT EXISTS {$this->table_journeys} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_id varchar(100) NOT NULL,
            visitor_id varchar(100) NOT NULL,
            user_id bigint(20) unsigned DEFAULT 0,
            
            -- 저니 순서
            step_number int(11) NOT NULL,
            
            -- 페이지 정보
            page_url varchar(500) NOT NULL,
            page_path varchar(255) NOT NULL,
            page_title varchar(255) DEFAULT NULL,
            page_type varchar(50) DEFAULT NULL,
            
            -- 유입 정보
            entry_source varchar(50) DEFAULT NULL,
            referrer varchar(500) DEFAULT NULL,
            utm_source varchar(100) DEFAULT NULL,
            utm_medium varchar(100) DEFAULT NULL,
            utm_campaign varchar(100) DEFAULT NULL,
            
            -- 체류 정보
            time_on_page int(11) DEFAULT 0,
            scroll_depth int(11) DEFAULT 0,
            interactions_count int(11) DEFAULT 0,
            
            -- 이동 정보
            exit_type enum('navigate', 'back', 'external', 'close', 'timeout') DEFAULT NULL,
            next_page varchar(500) DEFAULT NULL,
            
            -- 전환 정보
            is_conversion tinyint(1) DEFAULT 0,
            conversion_type varchar(50) DEFAULT NULL,
            conversion_value decimal(15,2) DEFAULT 0,
            
            -- 이탈 신호
            is_bounce tinyint(1) DEFAULT 0,
            exit_intent_detected tinyint(1) DEFAULT 0,
            
            -- 디바이스
            device_type varchar(20) DEFAULT NULL,
            
            entered_at datetime NOT NULL,
            exited_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            
            PRIMARY KEY (id),
            KEY idx_session (session_id),
            KEY idx_visitor (visitor_id),
            KEY idx_user (user_id),
            KEY idx_page_path (page_path),
            KEY idx_step (session_id, step_number),
            KEY idx_conversion (is_conversion),
            KEY idx_entered (entered_at)
        ) $charset_collate;";

        dbDelta( $sql_journeys );

        // 5. 세션 테이블
        $sql_sessions = "CREATE TABLE IF NOT EXISTS {$this->table_sessions} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_id varchar(100) NOT NULL UNIQUE,
            visitor_id varchar(100) NOT NULL,
            user_id bigint(20) unsigned DEFAULT 0,
            
            -- 세션 정보
            started_at datetime NOT NULL,
            ended_at datetime DEFAULT NULL,
            duration int(11) DEFAULT 0,
            is_active tinyint(1) DEFAULT 1,
            
            -- 페이지 통계
            page_views int(11) DEFAULT 0,
            unique_pages int(11) DEFAULT 0,
            
            -- 인터랙션 통계
            total_clicks int(11) DEFAULT 0,
            total_scrolls int(11) DEFAULT 0,
            total_events int(11) DEFAULT 0,
            
            -- 유입 정보
            landing_page varchar(500) DEFAULT NULL,
            referrer varchar(500) DEFAULT NULL,
            traffic_source varchar(50) DEFAULT NULL,
            utm_source varchar(100) DEFAULT NULL,
            utm_medium varchar(100) DEFAULT NULL,
            utm_campaign varchar(100) DEFAULT NULL,
            
            -- 이탈 정보
            exit_page varchar(500) DEFAULT NULL,
            is_bounce tinyint(1) DEFAULT 0,
            bounce_reason varchar(50) DEFAULT NULL,
            
            -- 전환 정보
            has_conversion tinyint(1) DEFAULT 0,
            conversion_count int(11) DEFAULT 0,
            conversion_value decimal(15,2) DEFAULT 0,
            
            -- 디바이스 정보
            device_type varchar(20) DEFAULT NULL,
            browser varchar(50) DEFAULT NULL,
            os varchar(50) DEFAULT NULL,
            screen_resolution varchar(20) DEFAULT NULL,
            
            -- 위치 정보
            country_code varchar(2) DEFAULT NULL,
            region varchar(100) DEFAULT NULL,
            city varchar(100) DEFAULT NULL,
            
            -- 저니 요약 (JSON)
            journey_summary longtext DEFAULT NULL,
            
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            PRIMARY KEY (id),
            UNIQUE KEY idx_session_id (session_id),
            KEY idx_visitor (visitor_id),
            KEY idx_user (user_id),
            KEY idx_started (started_at),
            KEY idx_active (is_active),
            KEY idx_bounce (is_bounce),
            KEY idx_conversion (has_conversion)
        ) $charset_collate;";

        dbDelta( $sql_sessions );

        // 6. 페이지 통계 테이블 (집계 데이터)
        $sql_page_stats = "CREATE TABLE IF NOT EXISTS {$this->table_page_stats} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            page_path varchar(255) NOT NULL,
            page_url varchar(500) DEFAULT NULL,
            page_title varchar(255) DEFAULT NULL,
            
            -- 날짜 (집계 단위)
            stat_date date NOT NULL,
            
            -- 방문 통계
            page_views int(11) DEFAULT 0,
            unique_visitors int(11) DEFAULT 0,
            unique_sessions int(11) DEFAULT 0,
            
            -- 체류 통계
            avg_time_on_page float DEFAULT 0,
            total_time_on_page int(11) DEFAULT 0,
            
            -- 스크롤 통계
            avg_scroll_depth float DEFAULT 0,
            scroll_25_percent int(11) DEFAULT 0,
            scroll_50_percent int(11) DEFAULT 0,
            scroll_75_percent int(11) DEFAULT 0,
            scroll_100_percent int(11) DEFAULT 0,
            
            -- 이탈 통계
            bounce_count int(11) DEFAULT 0,
            bounce_rate float DEFAULT 0,
            exit_count int(11) DEFAULT 0,
            exit_rate float DEFAULT 0,
            
            -- 클릭 통계
            total_clicks int(11) DEFAULT 0,
            unique_click_elements int(11) DEFAULT 0,
            rage_clicks int(11) DEFAULT 0,
            dead_clicks int(11) DEFAULT 0,
            
            -- 전환 통계
            conversions int(11) DEFAULT 0,
            conversion_rate float DEFAULT 0,
            conversion_value decimal(15,2) DEFAULT 0,
            
            -- 유입 통계 (진입 페이지로 사용된 횟수)
            entry_count int(11) DEFAULT 0,
            
            -- 디바이스별 통계 (JSON)
            device_breakdown longtext DEFAULT NULL,
            
            -- 트래픽 소스별 통계 (JSON)
            source_breakdown longtext DEFAULT NULL,
            
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            PRIMARY KEY (id),
            UNIQUE KEY idx_page_date (page_path, stat_date),
            KEY idx_date (stat_date),
            KEY idx_page_views (page_views),
            KEY idx_bounce_rate (bounce_rate)
        ) $charset_collate;";

        dbDelta( $sql_page_stats );

        return true;
    }

    /**
     * REST API 라우트 등록
     */
    public function register_rest_routes() {
        $namespace = 'acf-nudge-flow/v1';

        // 히트맵 데이터 조회
        register_rest_route( $namespace, '/analytics/heatmap', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_heatmap' ),
            'permission_callback' => array( $this, 'rest_permission_check' ),
            'args' => array(
                'page_path' => array( 'required' => true, 'type' => 'string' ),
                'type' => array( 'default' => 'click', 'type' => 'string' ),
                'device' => array( 'default' => 'all', 'type' => 'string' ),
                'days' => array( 'default' => 30, 'type' => 'integer' ),
            ),
        ));

        // 스크롤맵 데이터 조회
        register_rest_route( $namespace, '/analytics/scrollmap', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_scrollmap' ),
            'permission_callback' => array( $this, 'rest_permission_check' ),
            'args' => array(
                'page_path' => array( 'required' => true, 'type' => 'string' ),
                'device' => array( 'default' => 'all', 'type' => 'string' ),
                'days' => array( 'default' => 30, 'type' => 'integer' ),
            ),
        ));

        // 유저 저니 조회
        register_rest_route( $namespace, '/analytics/journey/(?P<session_id>[a-zA-Z0-9_-]+)', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_journey' ),
            'permission_callback' => array( $this, 'rest_permission_check' ),
        ));

        // 이벤트 분석 조회
        register_rest_route( $namespace, '/analytics/events', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_events' ),
            'permission_callback' => array( $this, 'rest_permission_check' ),
            'args' => array(
                'event_name' => array( 'type' => 'string' ),
                'event_category' => array( 'type' => 'string' ),
                'page_path' => array( 'type' => 'string' ),
                'days' => array( 'default' => 30, 'type' => 'integer' ),
                'limit' => array( 'default' => 100, 'type' => 'integer' ),
            ),
        ));

        // 페이지 통계 조회
        register_rest_route( $namespace, '/analytics/page-stats', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_page_stats' ),
            'permission_callback' => array( $this, 'rest_permission_check' ),
            'args' => array(
                'page_path' => array( 'type' => 'string' ),
                'start_date' => array( 'type' => 'string' ),
                'end_date' => array( 'type' => 'string' ),
            ),
        ));

        // 대시보드 요약
        register_rest_route( $namespace, '/analytics/dashboard', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_dashboard' ),
            'permission_callback' => array( $this, 'rest_permission_check' ),
            'args' => array(
                'days' => array( 'default' => 7, 'type' => 'integer' ),
            ),
        ));
    }

    /**
     * REST API 권한 확인
     */
    public function rest_permission_check( $request ) {
        return current_user_can( 'manage_options' );
    }

    // ==========================================
    // AJAX 핸들러
    // ==========================================

    /**
     * 이벤트 기록 AJAX
     */
    public function ajax_record_event() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $event_data = isset( $_POST['event'] ) ? $_POST['event'] : array();
        
        if ( empty( $event_data['event_name'] ) ) {
            wp_send_json_error( 'Event name required' );
        }

        $this->record_event( $event_data );
        wp_send_json_success();
    }

    /**
     * 히트맵 데이터 기록 AJAX
     */
    public function ajax_record_heatmap() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $heatmap_data = isset( $_POST['heatmap'] ) ? $_POST['heatmap'] : array();
        
        if ( empty( $heatmap_data ) ) {
            wp_send_json_error( 'Heatmap data required' );
        }

        // 배열로 여러 포인트 전송 가능
        if ( isset( $heatmap_data[0] ) ) {
            foreach ( $heatmap_data as $point ) {
                $this->record_heatmap_point( $point );
            }
        } else {
            $this->record_heatmap_point( $heatmap_data );
        }

        wp_send_json_success();
    }

    /**
     * 스크롤 데이터 기록 AJAX
     */
    public function ajax_record_scroll() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $scroll_data = isset( $_POST['scroll'] ) ? $_POST['scroll'] : array();
        
        if ( empty( $scroll_data ) ) {
            wp_send_json_error( 'Scroll data required' );
        }

        $this->record_scroll_data( $scroll_data );
        wp_send_json_success();
    }

    /**
     * 저니 기록 AJAX
     */
    public function ajax_record_journey() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $journey_data = isset( $_POST['journey'] ) ? $_POST['journey'] : array();
        
        if ( empty( $journey_data ) ) {
            wp_send_json_error( 'Journey data required' );
        }

        $this->record_journey_step( $journey_data );
        wp_send_json_success();
    }

    /**
     * 세션 종료 AJAX
     */
    public function ajax_end_session() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $session_id = isset( $_POST['session_id'] ) ? sanitize_text_field( $_POST['session_id'] ) : '';
        $session_data = isset( $_POST['session'] ) ? $_POST['session'] : array();

        if ( empty( $session_id ) ) {
            wp_send_json_error( 'Session ID required' );
        }

        $this->end_session( $session_id, $session_data );
        wp_send_json_success();
    }

    // ==========================================
    // 데이터 기록 메서드
    // ==========================================

    /**
     * 이벤트 기록
     */
    public function record_event( $data ) {
        global $wpdb;

        $insert_data = array(
            'session_id' => sanitize_text_field( $data['session_id'] ?? '' ),
            'visitor_id' => sanitize_text_field( $data['visitor_id'] ?? '' ),
            'user_id' => absint( $data['user_id'] ?? get_current_user_id() ),
            'event_name' => sanitize_text_field( $data['event_name'] ?? '' ),
            'event_category' => sanitize_text_field( $data['event_category'] ?? 'general' ),
            'event_action' => sanitize_text_field( $data['event_action'] ?? '' ),
            'event_label' => sanitize_text_field( $data['event_label'] ?? '' ),
            'event_value' => floatval( $data['event_value'] ?? 0 ),
            'page_url' => esc_url_raw( $data['page_url'] ?? '' ),
            'page_title' => sanitize_text_field( $data['page_title'] ?? '' ),
            'page_type' => sanitize_text_field( $data['page_type'] ?? '' ),
            'referrer' => esc_url_raw( $data['referrer'] ?? '' ),
            'element_type' => sanitize_text_field( $data['element_type'] ?? '' ),
            'element_id' => sanitize_text_field( $data['element_id'] ?? '' ),
            'element_class' => sanitize_text_field( $data['element_class'] ?? '' ),
            'element_text' => sanitize_text_field( substr( $data['element_text'] ?? '', 0, 500 ) ),
            'element_href' => esc_url_raw( $data['element_href'] ?? '' ),
            'element_xpath' => sanitize_text_field( $data['element_xpath'] ?? '' ),
            'position_x' => isset( $data['position_x'] ) ? intval( $data['position_x'] ) : null,
            'position_y' => isset( $data['position_y'] ) ? intval( $data['position_y'] ) : null,
            'viewport_width' => isset( $data['viewport_width'] ) ? intval( $data['viewport_width'] ) : null,
            'viewport_height' => isset( $data['viewport_height'] ) ? intval( $data['viewport_height'] ) : null,
            'scroll_depth' => isset( $data['scroll_depth'] ) ? intval( $data['scroll_depth'] ) : null,
            'device_type' => sanitize_text_field( $data['device_type'] ?? '' ),
            'browser' => sanitize_text_field( $data['browser'] ?? '' ),
            'os' => sanitize_text_field( $data['os'] ?? '' ),
            'screen_width' => isset( $data['screen_width'] ) ? intval( $data['screen_width'] ) : null,
            'screen_height' => isset( $data['screen_height'] ) ? intval( $data['screen_height'] ) : null,
            'time_on_page' => intval( $data['time_on_page'] ?? 0 ),
            'timestamp_ms' => isset( $data['timestamp_ms'] ) ? intval( $data['timestamp_ms'] ) : null,
            'custom_data' => ! empty( $data['custom_data'] ) ? wp_json_encode( $data['custom_data'] ) : null,
            'created_at' => current_time( 'mysql' ),
        );

        $wpdb->insert( $this->table_events, $insert_data );

        // 세션 이벤트 카운트 업데이트
        $this->update_session_event_count( $insert_data['session_id'] );

        return $wpdb->insert_id;
    }

    /**
     * 히트맵 포인트 기록
     */
    public function record_heatmap_point( $data ) {
        global $wpdb;

        $insert_data = array(
            'session_id' => sanitize_text_field( $data['session_id'] ?? '' ),
            'visitor_id' => sanitize_text_field( $data['visitor_id'] ?? '' ),
            'page_url' => esc_url_raw( $data['page_url'] ?? '' ),
            'page_path' => sanitize_text_field( wp_parse_url( $data['page_url'] ?? '', PHP_URL_PATH ) ?: '/' ),
            'interaction_type' => sanitize_text_field( $data['interaction_type'] ?? 'click' ),
            'x_absolute' => intval( $data['x_absolute'] ?? 0 ),
            'y_absolute' => intval( $data['y_absolute'] ?? 0 ),
            'x_relative' => floatval( $data['x_relative'] ?? 0 ),
            'y_relative' => floatval( $data['y_relative'] ?? 0 ),
            'page_width' => intval( $data['page_width'] ?? 0 ),
            'page_height' => intval( $data['page_height'] ?? 0 ),
            'viewport_width' => intval( $data['viewport_width'] ?? 0 ),
            'viewport_height' => intval( $data['viewport_height'] ?? 0 ),
            'element_selector' => sanitize_text_field( $data['element_selector'] ?? '' ),
            'element_tag' => sanitize_text_field( $data['element_tag'] ?? '' ),
            'element_text' => sanitize_text_field( substr( $data['element_text'] ?? '', 0, 255 ) ),
            'is_clickable' => ! empty( $data['is_clickable'] ) ? 1 : 0,
            'device_type' => sanitize_text_field( $data['device_type'] ?? 'desktop' ),
            'hover_duration' => isset( $data['hover_duration'] ) ? intval( $data['hover_duration'] ) : null,
            'created_at' => current_time( 'mysql' ),
        );

        $wpdb->insert( $this->table_heatmap, $insert_data );

        // 세션 클릭 카운트 업데이트
        if ( $insert_data['interaction_type'] === 'click' || $insert_data['interaction_type'] === 'touch' ) {
            $this->update_session_click_count( $insert_data['session_id'] );
        }

        return $wpdb->insert_id;
    }

    /**
     * 스크롤 데이터 기록
     */
    public function record_scroll_data( $data ) {
        global $wpdb;

        $page_path = sanitize_text_field( wp_parse_url( $data['page_url'] ?? '', PHP_URL_PATH ) ?: '/' );
        $session_id = sanitize_text_field( $data['session_id'] ?? '' );

        // 기존 데이터가 있으면 업데이트
        $existing = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$this->table_scrollmap} 
             WHERE session_id = %s AND page_path = %s",
            $session_id,
            $page_path
        ));

        $record_data = array(
            'session_id' => $session_id,
            'visitor_id' => sanitize_text_field( $data['visitor_id'] ?? '' ),
            'page_url' => esc_url_raw( $data['page_url'] ?? '' ),
            'page_path' => $page_path,
            'page_height' => intval( $data['page_height'] ?? 0 ),
            'viewport_height' => intval( $data['viewport_height'] ?? 0 ),
            'max_scroll_depth' => intval( $data['max_scroll_depth'] ?? 0 ),
            'max_scroll_percent' => floatval( $data['max_scroll_percent'] ?? 0 ),
            'scroll_sections_time' => ! empty( $data['scroll_sections_time'] ) ? wp_json_encode( $data['scroll_sections_time'] ) : null,
            'fold_line' => isset( $data['fold_line'] ) ? intval( $data['fold_line'] ) : null,
            'below_fold_time' => intval( $data['below_fold_time'] ?? 0 ),
            'scroll_ups' => intval( $data['scroll_ups'] ?? 0 ),
            'scroll_downs' => intval( $data['scroll_downs'] ?? 0 ),
            'scroll_speed_avg' => isset( $data['scroll_speed_avg'] ) ? floatval( $data['scroll_speed_avg'] ) : null,
            'reading_time' => intval( $data['reading_time'] ?? 0 ),
            'attention_time' => intval( $data['attention_time'] ?? 0 ),
            'device_type' => sanitize_text_field( $data['device_type'] ?? 'desktop' ),
        );

        if ( $existing ) {
            $wpdb->update( $this->table_scrollmap, $record_data, array( 'id' => $existing ) );
            return $existing;
        } else {
            $record_data['created_at'] = current_time( 'mysql' );
            $wpdb->insert( $this->table_scrollmap, $record_data );
            return $wpdb->insert_id;
        }
    }

    /**
     * 저니 스텝 기록
     */
    public function record_journey_step( $data ) {
        global $wpdb;

        $session_id = sanitize_text_field( $data['session_id'] ?? '' );

        // 다음 스텝 번호 조회
        $step_number = $wpdb->get_var( $wpdb->prepare(
            "SELECT COALESCE(MAX(step_number), 0) + 1 FROM {$this->table_journeys} WHERE session_id = %s",
            $session_id
        ));

        $insert_data = array(
            'session_id' => $session_id,
            'visitor_id' => sanitize_text_field( $data['visitor_id'] ?? '' ),
            'user_id' => absint( $data['user_id'] ?? get_current_user_id() ),
            'step_number' => intval( $step_number ),
            'page_url' => esc_url_raw( $data['page_url'] ?? '' ),
            'page_path' => sanitize_text_field( wp_parse_url( $data['page_url'] ?? '', PHP_URL_PATH ) ?: '/' ),
            'page_title' => sanitize_text_field( $data['page_title'] ?? '' ),
            'page_type' => sanitize_text_field( $data['page_type'] ?? '' ),
            'entry_source' => sanitize_text_field( $data['entry_source'] ?? '' ),
            'referrer' => esc_url_raw( $data['referrer'] ?? '' ),
            'utm_source' => sanitize_text_field( $data['utm_source'] ?? '' ),
            'utm_medium' => sanitize_text_field( $data['utm_medium'] ?? '' ),
            'utm_campaign' => sanitize_text_field( $data['utm_campaign'] ?? '' ),
            'time_on_page' => intval( $data['time_on_page'] ?? 0 ),
            'scroll_depth' => intval( $data['scroll_depth'] ?? 0 ),
            'interactions_count' => intval( $data['interactions_count'] ?? 0 ),
            'exit_type' => sanitize_text_field( $data['exit_type'] ?? '' ),
            'next_page' => esc_url_raw( $data['next_page'] ?? '' ),
            'is_conversion' => ! empty( $data['is_conversion'] ) ? 1 : 0,
            'conversion_type' => sanitize_text_field( $data['conversion_type'] ?? '' ),
            'conversion_value' => floatval( $data['conversion_value'] ?? 0 ),
            'is_bounce' => ! empty( $data['is_bounce'] ) ? 1 : 0,
            'exit_intent_detected' => ! empty( $data['exit_intent_detected'] ) ? 1 : 0,
            'device_type' => sanitize_text_field( $data['device_type'] ?? '' ),
            'entered_at' => sanitize_text_field( $data['entered_at'] ?? current_time( 'mysql' ) ),
            'exited_at' => ! empty( $data['exited_at'] ) ? sanitize_text_field( $data['exited_at'] ) : null,
            'created_at' => current_time( 'mysql' ),
        );

        $wpdb->insert( $this->table_journeys, $insert_data );

        // 세션 페이지뷰 카운트 업데이트
        $this->update_session_pageview_count( $session_id );

        return $wpdb->insert_id;
    }

    /**
     * 세션 시작/업데이트
     */
    public function start_or_update_session( $data ) {
        global $wpdb;

        $session_id = sanitize_text_field( $data['session_id'] ?? '' );

        $existing = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$this->table_sessions} WHERE session_id = %s",
            $session_id
        ) );

        if ( $existing ) {
            // 세션 업데이트
            $wpdb->update(
                $this->table_sessions,
                array(
                    'is_active' => 1,
                    'updated_at' => current_time( 'mysql' ),
                ),
                array( 'session_id' => $session_id )
            );
            return $existing->id;
        }

        // 새 세션 생성
        $insert_data = array(
            'session_id' => $session_id,
            'visitor_id' => sanitize_text_field( $data['visitor_id'] ?? '' ),
            'user_id' => absint( $data['user_id'] ?? get_current_user_id() ),
            'started_at' => current_time( 'mysql' ),
            'is_active' => 1,
            'landing_page' => esc_url_raw( $data['landing_page'] ?? '' ),
            'referrer' => esc_url_raw( $data['referrer'] ?? '' ),
            'traffic_source' => sanitize_text_field( $data['traffic_source'] ?? '' ),
            'utm_source' => sanitize_text_field( $data['utm_source'] ?? '' ),
            'utm_medium' => sanitize_text_field( $data['utm_medium'] ?? '' ),
            'utm_campaign' => sanitize_text_field( $data['utm_campaign'] ?? '' ),
            'device_type' => sanitize_text_field( $data['device_type'] ?? '' ),
            'browser' => sanitize_text_field( $data['browser'] ?? '' ),
            'os' => sanitize_text_field( $data['os'] ?? '' ),
            'screen_resolution' => sanitize_text_field( $data['screen_resolution'] ?? '' ),
            'country_code' => sanitize_text_field( $data['country_code'] ?? '' ),
            'created_at' => current_time( 'mysql' ),
        );

        $wpdb->insert( $this->table_sessions, $insert_data );
        return $wpdb->insert_id;
    }

    /**
     * 세션 종료
     */
    public function end_session( $session_id, $data = array() ) {
        global $wpdb;

        $update_data = array(
            'ended_at' => current_time( 'mysql' ),
            'is_active' => 0,
            'duration' => intval( $data['duration'] ?? 0 ),
            'exit_page' => esc_url_raw( $data['exit_page'] ?? '' ),
            'is_bounce' => ! empty( $data['is_bounce'] ) ? 1 : 0,
            'bounce_reason' => sanitize_text_field( $data['bounce_reason'] ?? '' ),
            'has_conversion' => ! empty( $data['has_conversion'] ) ? 1 : 0,
            'conversion_count' => intval( $data['conversion_count'] ?? 0 ),
            'conversion_value' => floatval( $data['conversion_value'] ?? 0 ),
            'journey_summary' => ! empty( $data['journey_summary'] ) ? wp_json_encode( $data['journey_summary'] ) : null,
            'updated_at' => current_time( 'mysql' ),
        );

        return $wpdb->update(
            $this->table_sessions,
            $update_data,
            array( 'session_id' => $session_id )
        );
    }

    /**
     * 세션 이벤트 카운트 업데이트
     */
    private function update_session_event_count( $session_id ) {
        global $wpdb;
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$this->table_sessions} SET total_events = total_events + 1, updated_at = %s WHERE session_id = %s",
            current_time( 'mysql' ),
            $session_id
        ));
    }

    /**
     * 세션 클릭 카운트 업데이트
     */
    private function update_session_click_count( $session_id ) {
        global $wpdb;
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$this->table_sessions} SET total_clicks = total_clicks + 1, updated_at = %s WHERE session_id = %s",
            current_time( 'mysql' ),
            $session_id
        ));
    }

    /**
     * 세션 페이지뷰 카운트 업데이트
     */
    private function update_session_pageview_count( $session_id ) {
        global $wpdb;
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$this->table_sessions} SET page_views = page_views + 1, updated_at = %s WHERE session_id = %s",
            current_time( 'mysql' ),
            $session_id
        ));
    }

    // ==========================================
    // REST API 핸들러
    // ==========================================

    /**
     * 히트맵 데이터 조회
     */
    public function rest_get_heatmap( $request ) {
        global $wpdb;

        $page_path = sanitize_text_field( $request['page_path'] );
        $type = sanitize_text_field( $request['type'] );
        $device = sanitize_text_field( $request['device'] );
        $days = intval( $request['days'] );

        $where = array( 'page_path = %s' );
        $where_values = array( $page_path );

        if ( $type !== 'all' ) {
            $where[] = 'interaction_type = %s';
            $where_values[] = $type;
        }

        if ( $device !== 'all' ) {
            $where[] = 'device_type = %s';
            $where_values[] = $device;
        }

        $where[] = 'created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)';
        $where_values[] = $days;

        $where_clause = implode( ' AND ', $where );

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT 
                x_relative, y_relative, 
                COUNT(*) as count,
                interaction_type,
                element_tag,
                element_text
             FROM {$this->table_heatmap}
             WHERE $where_clause
             GROUP BY ROUND(x_relative, 2), ROUND(y_relative, 2), interaction_type
             ORDER BY count DESC
             LIMIT 10000",
            ...$where_values
        ), ARRAY_A );

        // 히트맵 포인트 정규화
        $heatmap_data = array();
        foreach ( $results as $row ) {
            $heatmap_data[] = array(
                'x' => floatval( $row['x_relative'] ),
                'y' => floatval( $row['y_relative'] ),
                'value' => intval( $row['count'] ),
                'type' => $row['interaction_type'],
                'element' => array(
                    'tag' => $row['element_tag'],
                    'text' => $row['element_text'],
                ),
            );
        }

        return new WP_REST_Response( array(
            'page_path' => $page_path,
            'type' => $type,
            'device' => $device,
            'days' => $days,
            'total_points' => count( $heatmap_data ),
            'data' => $heatmap_data,
        ), 200 );
    }

    /**
     * 스크롤맵 데이터 조회
     */
    public function rest_get_scrollmap( $request ) {
        global $wpdb;

        $page_path = sanitize_text_field( $request['page_path'] );
        $device = sanitize_text_field( $request['device'] );
        $days = intval( $request['days'] );

        $where = array( 'page_path = %s' );
        $where_values = array( $page_path );

        if ( $device !== 'all' ) {
            $where[] = 'device_type = %s';
            $where_values[] = $device;
        }

        $where[] = 'created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)';
        $where_values[] = $days;

        $where_clause = implode( ' AND ', $where );

        // 스크롤 구간별 통계
        $scroll_stats = $wpdb->get_results( $wpdb->prepare(
            "SELECT 
                FLOOR(max_scroll_percent / 10) * 10 as scroll_bracket,
                COUNT(*) as sessions,
                AVG(reading_time) as avg_reading_time,
                AVG(attention_time) as avg_attention_time
             FROM {$this->table_scrollmap}
             WHERE $where_clause
             GROUP BY scroll_bracket
             ORDER BY scroll_bracket",
            ...$where_values
        ), ARRAY_A );

        // 평균 스크롤 깊이
        $avg_stats = $wpdb->get_row( $wpdb->prepare(
            "SELECT 
                AVG(max_scroll_percent) as avg_scroll_depth,
                AVG(max_scroll_depth) as avg_scroll_px,
                AVG(reading_time) as avg_reading_time,
                AVG(scroll_ups) as avg_scroll_ups,
                AVG(scroll_downs) as avg_scroll_downs,
                COUNT(*) as total_sessions
             FROM {$this->table_scrollmap}
             WHERE $where_clause",
            ...$where_values
        ), ARRAY_A );

        // 스크롤 맵 데이터 (10% 구간별)
        $scrollmap_data = array();
        for ( $i = 0; $i <= 100; $i += 10 ) {
            $bracket_data = array_filter( $scroll_stats, function( $s ) use ( $i ) {
                return intval( $s['scroll_bracket'] ) === $i;
            });
            $bracket = reset( $bracket_data );

            $scrollmap_data[] = array(
                'percent' => $i,
                'sessions' => $bracket ? intval( $bracket['sessions'] ) : 0,
                'avg_reading_time' => $bracket ? floatval( $bracket['avg_reading_time'] ) : 0,
            );
        }

        return new WP_REST_Response( array(
            'page_path' => $page_path,
            'device' => $device,
            'days' => $days,
            'summary' => $avg_stats,
            'scrollmap' => $scrollmap_data,
        ), 200 );
    }

    /**
     * 유저 저니 조회
     */
    public function rest_get_journey( $request ) {
        global $wpdb;

        $session_id = sanitize_text_field( $request['session_id'] );

        // 세션 정보
        $session = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$this->table_sessions} WHERE session_id = %s",
            $session_id
        ), ARRAY_A );

        if ( ! $session ) {
            return new WP_REST_Response( array( 'error' => 'Session not found' ), 404 );
        }

        // 저니 스텝
        $steps = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$this->table_journeys} WHERE session_id = %s ORDER BY step_number ASC",
            $session_id
        ), ARRAY_A );

        // 이벤트
        $events = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$this->table_events} WHERE session_id = %s ORDER BY created_at ASC",
            $session_id
        ), ARRAY_A );

        return new WP_REST_Response( array(
            'session' => $session,
            'journey' => $steps,
            'events' => $events,
            'total_steps' => count( $steps ),
            'total_events' => count( $events ),
        ), 200 );
    }

    /**
     * 이벤트 분석 조회
     */
    public function rest_get_events( $request ) {
        global $wpdb;

        $event_name = sanitize_text_field( $request['event_name'] ?? '' );
        $event_category = sanitize_text_field( $request['event_category'] ?? '' );
        $page_path = sanitize_text_field( $request['page_path'] ?? '' );
        $days = intval( $request['days'] );
        $limit = min( 1000, intval( $request['limit'] ) );

        $where = array( 'created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)' );
        $where_values = array( $days );

        if ( ! empty( $event_name ) ) {
            $where[] = 'event_name = %s';
            $where_values[] = $event_name;
        }

        if ( ! empty( $event_category ) ) {
            $where[] = 'event_category = %s';
            $where_values[] = $event_category;
        }

        if ( ! empty( $page_path ) ) {
            $where[] = 'page_url LIKE %s';
            $where_values[] = '%' . $wpdb->esc_like( $page_path ) . '%';
        }

        $where_clause = implode( ' AND ', $where );

        // 이벤트 통계
        $stats = $wpdb->get_results( $wpdb->prepare(
            "SELECT 
                event_name,
                event_category,
                COUNT(*) as total_count,
                COUNT(DISTINCT session_id) as unique_sessions,
                COUNT(DISTINCT visitor_id) as unique_visitors,
                SUM(event_value) as total_value,
                AVG(event_value) as avg_value
             FROM {$this->table_events}
             WHERE $where_clause
             GROUP BY event_name, event_category
             ORDER BY total_count DESC
             LIMIT %d",
            ...array_merge( $where_values, array( $limit ) )
        ), ARRAY_A );

        // 시간대별 이벤트 분포
        $hourly = $wpdb->get_results( $wpdb->prepare(
            "SELECT 
                HOUR(created_at) as hour,
                COUNT(*) as count
             FROM {$this->table_events}
             WHERE $where_clause
             GROUP BY hour
             ORDER BY hour",
            ...$where_values
        ), ARRAY_A );

        return new WP_REST_Response( array(
            'days' => $days,
            'event_stats' => $stats,
            'hourly_distribution' => $hourly,
            'total_events' => array_sum( array_column( $stats, 'total_count' ) ),
        ), 200 );
    }

    /**
     * 페이지 통계 조회
     */
    public function rest_get_page_stats( $request ) {
        global $wpdb;

        $page_path = sanitize_text_field( $request['page_path'] ?? '' );
        $start_date = sanitize_text_field( $request['start_date'] ?? date( 'Y-m-d', strtotime( '-30 days' ) ) );
        $end_date = sanitize_text_field( $request['end_date'] ?? date( 'Y-m-d' ) );

        $where = array( 'stat_date BETWEEN %s AND %s' );
        $where_values = array( $start_date, $end_date );

        if ( ! empty( $page_path ) ) {
            $where[] = 'page_path = %s';
            $where_values[] = $page_path;
        }

        $where_clause = implode( ' AND ', $where );

        $stats = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$this->table_page_stats}
             WHERE $where_clause
             ORDER BY stat_date DESC, page_views DESC",
            ...$where_values
        ), ARRAY_A );

        // 집계
        $summary = $wpdb->get_row( $wpdb->prepare(
            "SELECT 
                SUM(page_views) as total_page_views,
                SUM(unique_visitors) as total_unique_visitors,
                AVG(avg_time_on_page) as avg_time_on_page,
                AVG(avg_scroll_depth) as avg_scroll_depth,
                AVG(bounce_rate) as avg_bounce_rate,
                SUM(conversions) as total_conversions,
                SUM(conversion_value) as total_conversion_value
             FROM {$this->table_page_stats}
             WHERE $where_clause",
            ...$where_values
        ), ARRAY_A );

        return new WP_REST_Response( array(
            'start_date' => $start_date,
            'end_date' => $end_date,
            'page_path' => $page_path,
            'summary' => $summary,
            'daily_stats' => $stats,
        ), 200 );
    }

    /**
     * 대시보드 요약 조회
     */
    public function rest_get_dashboard( $request ) {
        global $wpdb;

        $days = intval( $request['days'] );

        // 세션 통계
        $session_stats = $wpdb->get_row( $wpdb->prepare(
            "SELECT 
                COUNT(*) as total_sessions,
                COUNT(DISTINCT visitor_id) as unique_visitors,
                AVG(duration) as avg_session_duration,
                AVG(page_views) as avg_pages_per_session,
                SUM(CASE WHEN is_bounce = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100 as bounce_rate,
                SUM(CASE WHEN has_conversion = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(conversion_value) as total_revenue
             FROM {$this->table_sessions}
             WHERE started_at >= DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ), ARRAY_A );

        // 일별 추이
        $daily_trend = $wpdb->get_results( $wpdb->prepare(
            "SELECT 
                DATE(started_at) as date,
                COUNT(*) as sessions,
                COUNT(DISTINCT visitor_id) as visitors,
                SUM(CASE WHEN has_conversion = 1 THEN 1 ELSE 0 END) as conversions
             FROM {$this->table_sessions}
             WHERE started_at >= DATE_SUB(NOW(), INTERVAL %d DAY)
             GROUP BY DATE(started_at)
             ORDER BY date",
            $days
        ), ARRAY_A );

        // 상위 페이지
        $top_pages = $wpdb->get_results( $wpdb->prepare(
            "SELECT 
                page_path,
                SUM(page_views) as total_views,
                AVG(avg_scroll_depth) as avg_scroll,
                AVG(bounce_rate) as bounce_rate
             FROM {$this->table_page_stats}
             WHERE stat_date >= DATE_SUB(NOW(), INTERVAL %d DAY)
             GROUP BY page_path
             ORDER BY total_views DESC
             LIMIT 10",
            $days
        ), ARRAY_A );

        // 상위 이벤트
        $top_events = $wpdb->get_results( $wpdb->prepare(
            "SELECT 
                event_name,
                event_category,
                COUNT(*) as count
             FROM {$this->table_events}
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)
             GROUP BY event_name, event_category
             ORDER BY count DESC
             LIMIT 10",
            $days
        ), ARRAY_A );

        // 디바이스 분포
        $device_breakdown = $wpdb->get_results( $wpdb->prepare(
            "SELECT 
                device_type,
                COUNT(*) as sessions,
                AVG(duration) as avg_duration
             FROM {$this->table_sessions}
             WHERE started_at >= DATE_SUB(NOW(), INTERVAL %d DAY)
             GROUP BY device_type",
            $days
        ), ARRAY_A );

        return new WP_REST_Response( array(
            'days' => $days,
            'summary' => $session_stats,
            'daily_trend' => $daily_trend,
            'top_pages' => $top_pages,
            'top_events' => $top_events,
            'device_breakdown' => $device_breakdown,
        ), 200 );
    }

    // ==========================================
    // 통계 집계 및 정리
    // ==========================================

    /**
     * 일일 통계 집계
     */
    public function aggregate_daily_stats() {
        global $wpdb;

        $yesterday = date( 'Y-m-d', strtotime( '-1 day' ) );

        // 저니 데이터에서 페이지별 통계 집계
        $page_data = $wpdb->get_results( $wpdb->prepare(
            "SELECT 
                page_path,
                MAX(page_url) as page_url,
                MAX(page_title) as page_title,
                COUNT(*) as page_views,
                COUNT(DISTINCT visitor_id) as unique_visitors,
                COUNT(DISTINCT session_id) as unique_sessions,
                AVG(time_on_page) as avg_time_on_page,
                SUM(time_on_page) as total_time_on_page,
                AVG(scroll_depth) as avg_scroll_depth,
                SUM(CASE WHEN scroll_depth >= 25 THEN 1 ELSE 0 END) as scroll_25,
                SUM(CASE WHEN scroll_depth >= 50 THEN 1 ELSE 0 END) as scroll_50,
                SUM(CASE WHEN scroll_depth >= 75 THEN 1 ELSE 0 END) as scroll_75,
                SUM(CASE WHEN scroll_depth >= 100 THEN 1 ELSE 0 END) as scroll_100,
                SUM(CASE WHEN is_bounce = 1 THEN 1 ELSE 0 END) as bounce_count,
                SUM(CASE WHEN is_conversion = 1 THEN 1 ELSE 0 END) as conversions,
                SUM(conversion_value) as conversion_value,
                SUM(CASE WHEN step_number = 1 THEN 1 ELSE 0 END) as entry_count
             FROM {$this->table_journeys}
             WHERE DATE(entered_at) = %s
             GROUP BY page_path",
            $yesterday
        ), ARRAY_A );

        foreach ( $page_data as $page ) {
            $page_views = intval( $page['page_views'] );
            
            $insert_data = array(
                'page_path' => $page['page_path'],
                'page_url' => $page['page_url'],
                'page_title' => $page['page_title'],
                'stat_date' => $yesterday,
                'page_views' => $page_views,
                'unique_visitors' => intval( $page['unique_visitors'] ),
                'unique_sessions' => intval( $page['unique_sessions'] ),
                'avg_time_on_page' => floatval( $page['avg_time_on_page'] ),
                'total_time_on_page' => intval( $page['total_time_on_page'] ),
                'avg_scroll_depth' => floatval( $page['avg_scroll_depth'] ),
                'scroll_25_percent' => intval( $page['scroll_25'] ),
                'scroll_50_percent' => intval( $page['scroll_50'] ),
                'scroll_75_percent' => intval( $page['scroll_75'] ),
                'scroll_100_percent' => intval( $page['scroll_100'] ),
                'bounce_count' => intval( $page['bounce_count'] ),
                'bounce_rate' => $page_views > 0 ? ( $page['bounce_count'] / $page_views ) * 100 : 0,
                'conversions' => intval( $page['conversions'] ),
                'conversion_rate' => $page_views > 0 ? ( $page['conversions'] / $page_views ) * 100 : 0,
                'conversion_value' => floatval( $page['conversion_value'] ),
                'entry_count' => intval( $page['entry_count'] ),
                'created_at' => current_time( 'mysql' ),
            );

            // 히트맵에서 클릭 통계 추가
            $click_stats = $wpdb->get_row( $wpdb->prepare(
                "SELECT 
                    COUNT(*) as total_clicks,
                    COUNT(DISTINCT element_selector) as unique_elements,
                    SUM(CASE WHEN interaction_type = 'rage_click' THEN 1 ELSE 0 END) as rage_clicks,
                    SUM(CASE WHEN interaction_type = 'dead_click' THEN 1 ELSE 0 END) as dead_clicks
                 FROM {$this->table_heatmap}
                 WHERE page_path = %s AND DATE(created_at) = %s",
                $page['page_path'],
                $yesterday
            ) );

            if ( $click_stats ) {
                $insert_data['total_clicks'] = intval( $click_stats->total_clicks );
                $insert_data['unique_click_elements'] = intval( $click_stats->unique_elements );
                $insert_data['rage_clicks'] = intval( $click_stats->rage_clicks );
                $insert_data['dead_clicks'] = intval( $click_stats->dead_clicks );
            }

            // UPSERT
            $existing = $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM {$this->table_page_stats} WHERE page_path = %s AND stat_date = %s",
                $page['page_path'],
                $yesterday
            ));

            if ( $existing ) {
                $wpdb->update( $this->table_page_stats, $insert_data, array( 'id' => $existing ) );
            } else {
                $wpdb->insert( $this->table_page_stats, $insert_data );
            }
        }
    }

    /**
     * 오래된 데이터 정리
     */
    public function cleanup_old_data() {
        global $wpdb;

        $retention_days = apply_filters( 'acf_nf_analytics_retention_days', 90 );
        $cutoff_date = date( 'Y-m-d', strtotime( "-{$retention_days} days" ) );

        // 히트맵 데이터 정리
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$this->table_heatmap} WHERE created_at < %s",
            $cutoff_date
        ));

        // 스크롤맵 데이터 정리
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$this->table_scrollmap} WHERE created_at < %s",
            $cutoff_date
        ));

        // 이벤트 데이터 정리 (상세 데이터)
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$this->table_events} WHERE created_at < %s",
            $cutoff_date
        ));

        // 저니 데이터 정리
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$this->table_journeys} WHERE created_at < %s",
            $cutoff_date
        ));

        // 비활성 세션 정리 (7일 이상 비활성)
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM {$this->table_sessions} WHERE is_active = 0 AND ended_at < %s",
            date( 'Y-m-d', strtotime( '-7 days' ) )
        ));

        // 테이블 최적화
        $wpdb->query( "OPTIMIZE TABLE {$this->table_heatmap}" );
        $wpdb->query( "OPTIMIZE TABLE {$this->table_scrollmap}" );
        $wpdb->query( "OPTIMIZE TABLE {$this->table_events}" );
        $wpdb->query( "OPTIMIZE TABLE {$this->table_journeys}" );
        $wpdb->query( "OPTIMIZE TABLE {$this->table_sessions}" );
    }

    /**
     * 테이블 이름 조회
     */
    public function get_table_name( $table ) {
        $tables = array(
            'events' => $this->table_events,
            'heatmap' => $this->table_heatmap,
            'scrollmap' => $this->table_scrollmap,
            'journeys' => $this->table_journeys,
            'sessions' => $this->table_sessions,
            'page_stats' => $this->table_page_stats,
        );

        return isset( $tables[ $table ] ) ? $tables[ $table ] : '';
    }
}

/**
 * 전역 함수: 네이티브 애널리틱스 인스턴스 반환
 */
function acf_nudge_flow_analytics() {
    return ACF_Nudge_Flow_Native_Analytics::instance();
}

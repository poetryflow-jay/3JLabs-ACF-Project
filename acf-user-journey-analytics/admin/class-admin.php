<?php
/**
 * 관리자 클래스
 *
 * @package ACF_User_Journey_Analytics
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_UJA_Admin {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'User Journey Analytics', 'acf-user-journey' ),
            __( 'Journey Analytics', 'acf-user-journey' ),
            'manage_options',
            'acf-user-journey',
            array( $this, 'render_dashboard_page' ),
            'dashicons-chart-line',
            30
        );

        add_submenu_page(
            'acf-user-journey',
            __( '대시보드', 'acf-user-journey' ),
            __( '대시보드', 'acf-user-journey' ),
            'manage_options',
            'acf-user-journey',
            array( $this, 'render_dashboard_page' )
        );

        add_submenu_page(
            'acf-user-journey',
            __( '트래픽 소스', 'acf-user-journey' ),
            __( '트래픽 소스', 'acf-user-journey' ),
            'manage_options',
            'acf-user-journey-sources',
            array( $this, 'render_sources_page' )
        );

        add_submenu_page(
            'acf-user-journey',
            __( '퍼널 분석', 'acf-user-journey' ),
            __( '퍼널 분석', 'acf-user-journey' ),
            'manage_options',
            'acf-user-journey-funnel',
            array( $this, 'render_funnel_page' )
        );

        add_submenu_page(
            'acf-user-journey',
            __( 'ROI 분석', 'acf-user-journey' ),
            __( 'ROI 분석', 'acf-user-journey' ),
            'manage_options',
            'acf-user-journey-roi',
            array( $this, 'render_roi_page' )
        );

        add_submenu_page(
            'acf-user-journey',
            __( '설정', 'acf-user-journey' ),
            __( '설정', 'acf-user-journey' ),
            'manage_options',
            'acf-user-journey-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * 관리자 에셋 로드
     */
    public function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'acf-user-journey' ) === false ) {
            return;
        }

        // Chart.js
        wp_enqueue_script( 'chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '4.4.1', true );

        // 관리자 CSS
        wp_enqueue_style(
            'acf-uja-admin',
            ACF_UJA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            ACF_UJA_VERSION
        );

        // 관리자 JS
        wp_enqueue_script(
            'acf-uja-admin',
            ACF_UJA_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery', 'chartjs' ),
            ACF_UJA_VERSION,
            true
        );

        wp_localize_script( 'acf-uja-admin', 'acf_uja_admin', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'acf_uja_admin' ),
        ) );
    }

    /**
     * 날짜 범위 계산
     */
    private function get_date_range( $period = '7days' ) {
        $end_date = date( 'Y-m-d 23:59:59' );

        switch ( $period ) {
            case 'today':
                $start_date = date( 'Y-m-d 00:00:00' );
                break;
            case 'yesterday':
                $start_date = date( 'Y-m-d 00:00:00', strtotime( '-1 day' ) );
                $end_date = date( 'Y-m-d 23:59:59', strtotime( '-1 day' ) );
                break;
            case '30days':
                $start_date = date( 'Y-m-d 00:00:00', strtotime( '-30 days' ) );
                break;
            case '90days':
                $start_date = date( 'Y-m-d 00:00:00', strtotime( '-90 days' ) );
                break;
            default:
                $start_date = date( 'Y-m-d 00:00:00', strtotime( '-7 days' ) );
        }

        return array( 'start' => $start_date, 'end' => $end_date );
    }

    /**
     * 대시보드 페이지
     */
    public function render_dashboard_page() {
        $period = isset( $_GET['period'] ) ? sanitize_text_field( $_GET['period'] ) : '7days';
        $date_range = $this->get_date_range( $period );

        $reporter = ACF_UJA_Traffic_Reporter::instance();
        $summary = $reporter->get_traffic_summary( $date_range['start'], $date_range['end'] );
        $daily_traffic = $reporter->get_daily_traffic( $date_range['start'], $date_range['end'] );
        $referrer_breakdown = $reporter->get_referrer_breakdown( $date_range['start'], $date_range['end'] );
        $device_stats = $reporter->get_device_stats( $date_range['start'], $date_range['end'] );
        $realtime = $reporter->get_realtime_data();

        include ACF_UJA_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    /**
     * 트래픽 소스 페이지
     */
    public function render_sources_page() {
        $period = isset( $_GET['period'] ) ? sanitize_text_field( $_GET['period'] ) : '7days';
        $date_range = $this->get_date_range( $period );

        $reporter = ACF_UJA_Traffic_Reporter::instance();
        $referrer_breakdown = $reporter->get_referrer_breakdown( $date_range['start'], $date_range['end'] );
        $utm_stats = $reporter->get_utm_campaign_stats( $date_range['start'], $date_range['end'] );
        $ad_platform_stats = $reporter->get_ad_platform_stats( $date_range['start'], $date_range['end'] );

        include ACF_UJA_PLUGIN_DIR . 'admin/views/sources.php';
    }

    /**
     * 퍼널 분석 페이지
     */
    public function render_funnel_page() {
        $period = isset( $_GET['period'] ) ? sanitize_text_field( $_GET['period'] ) : '7days';
        $date_range = $this->get_date_range( $period );

        $reporter = ACF_UJA_Traffic_Reporter::instance();
        $funnel_data = $reporter->get_funnel_analysis( $date_range['start'], $date_range['end'] );
        $referrer_breakdown = $reporter->get_referrer_breakdown( $date_range['start'], $date_range['end'] );

        include ACF_UJA_PLUGIN_DIR . 'admin/views/funnel.php';
    }

    /**
     * ROI 분석 페이지
     */
    public function render_roi_page() {
        $period = isset( $_GET['period'] ) ? sanitize_text_field( $_GET['period'] ) : '7days';
        $date_range = $this->get_date_range( $period );

        $reporter = ACF_UJA_Traffic_Reporter::instance();
        $roi_data = $reporter->get_roi_analysis( $date_range['start'], $date_range['end'] );
        $ad_platform_stats = $reporter->get_ad_platform_stats( $date_range['start'], $date_range['end'] );

        include ACF_UJA_PLUGIN_DIR . 'admin/views/roi.php';
    }

    /**
     * 설정 페이지
     */
    public function render_settings_page() {
        if ( isset( $_POST['acf_uja_save_settings'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'acf_uja_settings' ) ) {
            $settings = array(
                'enabled' => isset( $_POST['enabled'] ),
                'track_logged_in' => isset( $_POST['track_logged_in'] ),
                'exclude_roles' => isset( $_POST['exclude_roles'] ) ? array_map( 'sanitize_text_field', $_POST['exclude_roles'] ) : array(),
                'data_retention_days' => intval( $_POST['data_retention_days'] ),
            );
            update_option( 'acf_uja_settings', $settings );
        }

        $settings = get_option( 'acf_uja_settings', array(
            'enabled' => true,
            'track_logged_in' => true,
            'exclude_roles' => array( 'administrator' ),
            'data_retention_days' => 90,
        ) );

        include ACF_UJA_PLUGIN_DIR . 'admin/views/settings.php';
    }
}

// 인스턴스 초기화
ACF_UJA_Admin::instance();

<?php
/**
 * Plugin Name:       JJ Analytics Dashboard
 * Plugin URI:        https://3j-labs.com
 * Description:       3J Labs 플러그인 스위트 전체 성과, 활용 현황, 버전 관리를 한눈에 확인할 수 있는 대시보드입니다.
 * Version:           1.1.0
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com
 * Text Domain:       jj-analytics-dashboard
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 플러그인 상수 정의
 */
define( 'JJ_ANALYTICS_DASHBOARD_VERSION', '1.2.0' ); // [v1.2.0] Phase 50B: 통합 대시보드 Command Center
define( 'JJ_ANALYTICS_DASHBOARD_PLUGIN_FILE', __FILE__ );
define( 'JJ_ANALYTICS_DASHBOARD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JJ_ANALYTICS_DASHBOARD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'JJ_ANALYTICS_DASHBOARD_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'JJ_ANALYTICS_DASHBOARD_SLUG', 'jj-analytics-dashboard' );

// [v25.0.0] 보안 모듈 및 라이센스 관리 로드
$shared_path = dirname( dirname( __FILE__ ) ) . '/shared-ui-assets';
if ( file_exists( $shared_path . '/class-jj-security-module-v25.php' ) ) {
    require_once $shared_path . '/class-jj-security-module-v25.php';
    if ( class_exists( 'JJ_Security_Module_V25_Loader' ) ) {
        JJ_Security_Module_V25_Loader::instance( JJ_ANALYTICS_DASHBOARD_PLUGIN_DIR, JJ_ANALYTICS_DASHBOARD_PLUGIN_URL, JJ_ANALYTICS_DASHBOARD_VERSION, JJ_ANALYTICS_DASHBOARD_SLUG );
    }
}
if ( file_exists( $shared_path . '/class-jj-license-manager-shared.php' ) ) {
    require_once $shared_path . '/class-jj-license-manager-shared.php';
    if ( class_exists( 'JJ_License_Manager_Shared' ) ) {
        JJ_License_Manager_Shared::instance( JJ_ANALYTICS_DASHBOARD_SLUG );
    }
}

// [Phase 49-3] 실시간 대시보드 클래스 로드
require_once JJ_ANALYTICS_DASHBOARD_PLUGIN_DIR . 'includes/class-realtime-dashboard.php';

// [Phase 50B] 통합 대시보드 Command Center 로드
require_once JJ_ANALYTICS_DASHBOARD_PLUGIN_DIR . 'includes/class-unified-dashboard.php';

/**
 * 메인 플러그인 클래스
 */
final class JJ_Analytics_Dashboard {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * 의존성 로드
     */
    private function load_dependencies() {
        // Chart.js 로드
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // [Phase 49-3] 실시간 대시보드 초기화
        if ( class_exists( 'JJ_Realtime_Dashboard' ) ) {
            JJ_Realtime_Dashboard::get_instance();
        }

        // [Phase 50B] 통합 대시보드 Command Center 초기화
        if ( class_exists( 'JJ_Unified_Dashboard' ) ) {
            JJ_Unified_Dashboard::get_instance();
        }
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_menu_page(
            'JJ Analytics Dashboard',
            'JJ Analytics Dashboard',
            'manage_options',
            'jj-analytics-dashboard',
            array( $this, 'render_admin_page' ),
            'dashicons-chart-bar',
            30
        );
    }

    /**
     * 설정 등록
     */
    public function register_settings() {
        register_setting( 'jj_analytics', 'refresh_interval', array(
            'type' => 'number',
            'default' => 30,
            'sanitize_callback' => 'absint'
        ) );
        
        register_setting( 'jj_analytics', 'cache_duration', array(
            'type' => 'number',
            'default' => 3600,
            'sanitize_callback' => 'absint'
        ) );
        
        register_setting( 'jj_analytics', 'enable_realtime', array(
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean'
        ) );
        
        register_setting( 'jj_analytics', 'chart_type', array(
            'type' => 'string',
            'default' => 'line',
            'sanitize_callback' => 'sanitize_text_field'
        ) );
        
        register_setting( 'jj_analytics', 'default_period', array(
            'type' => 'number',
            'default' => 7,
            'sanitize_callback' => 'absint'
        ) );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        if ( 'toplevel_page_jj-analytics-dashboard' !== $hook ) {
            return;
        }

        // Chart.js CDN
        wp_enqueue_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
            array(),
            '4.4.1',
            true
        );

        // 플러그인 CSS
        wp_enqueue_style(
            'jj-analytics-dashboard',
            JJ_ANALYTICS_DASHBOARD_PLUGIN_URL . 'assets/css/analytics.css',
            array(),
            JJ_ANALYTICS_DASHBOARD_VERSION,
            false
        );

        // [Phase 49-3] 실시간 대시보드 JS
        wp_enqueue_script(
            'jj-realtime-dashboard',
            JJ_ANALYTICS_DASHBOARD_PLUGIN_URL . 'assets/js/realtime-dashboard.js',
            array( 'jquery', 'chartjs' ),
            JJ_ANALYTICS_DASHBOARD_VERSION,
            true
        );

        wp_localize_script( 'jj-realtime-dashboard', 'jjRealtimeData', array(
            'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
            'restUrl'         => rest_url( 'jj-analytics/v1/' ),
            'nonce'           => wp_create_nonce( 'jj_analytics_nonce' ),
            'restNonce'       => wp_create_nonce( 'wp_rest' ),
            'refreshInterval' => get_option( 'jj_analytics_refresh_interval', 30 ) * 1000,
            'enableRealtime'  => get_option( 'jj_analytics_enable_realtime', true ),
            'i18n'            => array(
                'loading'    => __( '로딩 중...', 'jj-analytics-dashboard' ),
                'error'      => __( '데이터를 불러올 수 없습니다.', 'jj-analytics-dashboard' ),
                'healthy'    => __( '정상', 'jj-analytics-dashboard' ),
                'inactive'   => __( '비활성', 'jj-analytics-dashboard' ),
                'warning'    => __( '주의', 'jj-analytics-dashboard' ),
            ),
        ) );
    }

    /**
     * 관리자 페이지 렌더링
     */
    public function render_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( '권한이 없습니다.' );
        }

        $refresh_interval = get_option( 'jj_analytics_refresh_interval', 30 );
        $enable_realtime = get_option( 'jj_analytics_enable_realtime', true );
        $chart_type = get_option( 'jj_analytics_chart_type', 'line' );
        $default_period = get_option( 'jj_analytics_default_period', 7 );

        ?>
        <div class="wrap jj-analytics-dashboard">
            <h1>
                <span class="dashicons dashicons-chart-bar" style="font-size: 30px; width: 30px; height: 30px; margin-right: 12px; vertical-align: middle;"></span>
                JJ Analytics Dashboard
                <span style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #fff; padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 600; margin-left: 20px;">
                    v<?php echo esc_html( JJ_ANALYTICS_DASHBOARD_VERSION ); ?>
                </span>
            </h1>

            <div id="jj-analytics-app">
                <!-- Tab Navigation -->
                <div class="jj-analytics-tabs" style="margin-bottom: 30px;">
                    <button class="jj-analytics-tab active" data-tab="overview">
                        <span class="dashicons dashicons-chart-area"></span>
                        개요
                    </button>
                    <button class="jj-analytics-tab" data-tab="realtime">
                        <span class="dashicons dashicons-update"></span>
                        실시간
                    </button>
                    <button class="jj-analytics-tab" data-tab="metrics">
                        <span class="dashicons dashicons-performance"></span>
                        성과
                    </button>
                    <button class="jj-analytics-tab" data-tab="trends">
                        <span class="dashicons dashicons-line"></span>
                        추이
                    </button>
                    <button class="jj-analytics-tab" data-tab="system">
                        <span class="dashicons dashicons-admin-generic"></span>
                        시스템
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="jj-analytics-tab-content">
                    <div id="jj-tab-overview" class="jj-analytics-tab-pane active">
                        <?php require_once JJ_ANALYTICS_DASHBOARD_PLUGIN_DIR . 'admin/views/components/stats-overview.php'; ?>
                    </div>
                    <div id="jj-tab-realtime" class="jj-analytics-tab-pane">
                        <?php $this->render_realtime_tab(); ?>
                    </div>
                    <div id="jj-tab-metrics" class="jj-analytics-tab-pane">
                        <?php require_once JJ_ANALYTICS_DASHBOARD_PLUGIN_DIR . 'admin/views/components/plugin-metrics.php'; ?>
                    </div>
                    <div id="jj-tab-trends" class="jj-analytics-tab-pane">
                        <?php require_once JJ_ANALYTICS_DASHBOARD_PLUGIN_DIR . 'admin/views/components/trends-charts.php'; ?>
                    </div>
                    <div id="jj-tab-system" class="jj-analytics-tab-pane">
                        <?php require_once JJ_ANALYTICS_DASHBOARD_PLUGIN_DIR . 'admin/views/components/comparison-chart.php'; ?>
                    </div>
                </div>
            </div>
        </div>

        <script>
        // Tab switching
        jQuery(document).ready(function($) {
            $('.jj-analytics-tab').on('click', function() {
                $('.jj-analytics-tab').removeClass('active');
                $(this).addClass('active');
                
                $('.jj-analytics-tab-pane').removeClass('active');
                var tabId = $(this).data('tab');
                $('#jj-tab-' + tabId).addClass('active');
            });
        });
        </script>
        <?php

        // 설정 폼
        ?>
        <div class="wrap" style="margin-top: 40px;">
            <h2>⚙️ 설정</h2>
            <form method="post" action="options.php">
                <?php settings_fields( 'jj_analytics' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">데이터 새로고침 간격</th>
                        <td>
                            <input type="number" name="jj_analytics_refresh_interval" value="<?php echo esc_attr( $refresh_interval ); ?>" min="10" max="300">
                            <span style="margin-left: 10px;">초</span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">캐시 만료 시간</th>
                        <td>
                            <input type="number" name="jj_analytics_cache_duration" value="<?php echo esc_attr( $cache_duration ); ?>" min="300" max="7200">
                            <span style="margin-left: 10px;">초</span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">실시간 데이터 사용</th>
                        <td>
                            <label>
                                <input type="checkbox" name="jj_analytics_enable_realtime" <?php checked( $enable_realtime ); ?>>
                                AJAX 폴링으로 실시간 데이터 수집
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">차트 타입</th>
                        <td>
                            <select name="jj_analytics_chart_type">
                                <option value="line" <?php selected( $chart_type === 'line' ); ?>>Line Chart</option>
                                <option value="bar" <?php selected( $chart_type === 'bar' ); ?>>Bar Chart</option>
                                <option value="doughnut" <?php selected( $chart_type === 'doughnut' ); ?>>Doughnut Chart</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">기본 조회 기간</th>
                        <td>
                            <select name="jj_analytics_default_period">
                                <option value="1">1일</option>
                                <option value="7">7일</option>
                                <option value="30" selected>30일</option>
                                <option value="90">90일</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button( '설정 저장' ); ?>
            </form>
        </div>
        <?php
    }

    /**
     * [Phase 49-3] 실시간 탭 렌더링
     */
    private function render_realtime_tab() {
        ?>
        <div class="jj-realtime-dashboard">
            <!-- 실시간 헤더 -->
            <div class="jj-realtime-header">
                <div class="jj-realtime-title">
                    <span class="dashicons dashicons-update-alt jj-spin"></span>
                    <h3>실시간 대시보드</h3>
                    <span class="jj-last-update"></span>
                </div>
                <button type="button" class="button jj-refresh-btn">
                    <span class="dashicons dashicons-update"></span>
                    새로고침
                </button>
            </div>

            <!-- 로딩 오버레이 -->
            <div class="jj-realtime-loading">
                <span class="spinner is-active"></span>
            </div>

            <!-- 개요 통계 위젯 -->
            <div class="jj-realtime-section">
                <h4><span class="dashicons dashicons-chart-pie"></span> 개요</h4>
                <div id="jj-realtime-overview"></div>
            </div>

            <!-- 플러그인 상태 위젯 -->
            <div class="jj-realtime-section">
                <h4><span class="dashicons dashicons-admin-plugins"></span> 플러그인 상태</h4>
                <div id="jj-realtime-plugins"></div>
            </div>

            <!-- 차트 영역 -->
            <div class="jj-realtime-section jj-chart-section">
                <h4><span class="dashicons dashicons-chart-line"></span> 트렌드 차트</h4>
                <div class="jj-chart-controls">
                    <div class="jj-data-type-btns">
                        <button type="button" class="jj-data-type-btn active" data-type="emails">이메일</button>
                        <button type="button" class="jj-data-type-btn" data-type="submissions">폼 제출</button>
                    </div>
                    <select class="jj-chart-period" data-chart-type="emails">
                        <option value="7">7일</option>
                        <option value="14">14일</option>
                        <option value="30">30일</option>
                    </select>
                </div>
                <div class="jj-chart-container">
                    <canvas id="jj-chart-emails" height="250"></canvas>
                </div>
            </div>

            <!-- 2열 레이아웃 -->
            <div class="jj-realtime-row">
                <!-- 성능 메트릭 -->
                <div class="jj-realtime-section jj-realtime-col">
                    <h4><span class="dashicons dashicons-performance"></span> 시스템 성능</h4>
                    <div id="jj-realtime-performance"></div>
                </div>

                <!-- 최근 활동 -->
                <div class="jj-realtime-section jj-realtime-col">
                    <h4><span class="dashicons dashicons-clock"></span> 최근 활동</h4>
                    <div id="jj-realtime-activity"></div>
                </div>
            </div>
        </div>

        <style>
        /* 실시간 대시보드 스타일 */
        .jj-realtime-dashboard {
            position: relative;
        }

        .jj-realtime-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .jj-realtime-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .jj-realtime-title h3 {
            margin: 0;
            font-size: 18px;
        }

        .jj-realtime-title .dashicons {
            font-size: 24px;
            width: 24px;
            height: 24px;
            color: #6366f1;
        }

        .jj-spin {
            animation: jj-spin 2s linear infinite;
        }

        @keyframes jj-spin {
            100% { transform: rotate(360deg); }
        }

        .jj-last-update {
            font-size: 12px;
            color: #888;
        }

        .jj-realtime-loading {
            position: absolute;
            top: 0;
            right: 0;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .jj-realtime-loading.active {
            opacity: 1;
        }

        .jj-realtime-section {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .jj-realtime-section h4 {
            margin: 0 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .jj-realtime-section h4 .dashicons {
            color: #6366f1;
        }

        /* 통계 그리드 */
        .jj-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .jj-stat-card {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .jj-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .jj-stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .jj-stat-icon .dashicons {
            font-size: 24px;
            width: 24px;
            height: 24px;
        }

        .jj-stat-content {
            display: flex;
            flex-direction: column;
        }

        .jj-stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #1d2327;
            line-height: 1.2;
        }

        .jj-stat-value.animate {
            animation: jj-pulse 0.5s ease;
        }

        @keyframes jj-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .jj-stat-label {
            font-size: 12px;
            color: #646970;
        }

        .jj-stat-sub {
            font-size: 11px;
            color: #888;
            margin-top: 2px;
        }

        /* 플러그인 목록 */
        .jj-plugin-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .jj-plugin-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background: #f9f9f9;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .jj-plugin-item:hover {
            background: #f0f0f0;
        }

        .jj-plugin-item.inactive {
            opacity: 0.6;
        }

        .jj-plugin-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .jj-plugin-name {
            font-weight: 500;
        }

        .jj-plugin-version {
            font-size: 11px;
            color: #888;
            background: #e0e0e0;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .jj-plugin-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-healthy .status-dot { background: #10b981; }
        .status-inactive .status-dot { background: #6b7280; }
        .status-warning .status-dot { background: #f59e0b; }

        /* 차트 */
        .jj-chart-section .jj-chart-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .jj-data-type-btns {
            display: flex;
            gap: 5px;
        }

        .jj-data-type-btn {
            padding: 6px 12px;
            border: 1px solid #e0e0e0;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }

        .jj-data-type-btn:hover {
            border-color: #6366f1;
            color: #6366f1;
        }

        .jj-data-type-btn.active {
            background: #6366f1;
            border-color: #6366f1;
            color: #fff;
        }

        .jj-chart-container {
            height: 250px;
        }

        /* 2열 레이아웃 */
        .jj-realtime-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* 성능 그리드 */
        .jj-performance-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .jj-perf-item {
            padding: 12px;
            background: #f9f9f9;
            border-radius: 6px;
        }

        .jj-perf-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .jj-perf-label {
            font-size: 12px;
            color: #646970;
        }

        .jj-perf-value {
            font-weight: 600;
            color: #1d2327;
        }

        .jj-perf-bar {
            height: 6px;
            background: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
        }

        .jj-perf-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .jj-perf-sub {
            font-size: 11px;
            color: #888;
            margin-top: 4px;
            display: block;
        }

        /* 활동 목록 */
        .jj-activity-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 300px;
            overflow-y: auto;
        }

        .jj-activity-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 8px;
            background: #f9f9f9;
            border-radius: 6px;
        }

        .jj-activity-item .dashicons {
            color: #6366f1;
            margin-top: 2px;
        }

        .jj-activity-content {
            flex: 1;
        }

        .jj-activity-message {
            display: block;
            font-size: 13px;
        }

        .jj-activity-time {
            display: block;
            font-size: 11px;
            color: #888;
            margin-top: 2px;
        }

        .jj-no-activity {
            text-align: center;
            color: #888;
            padding: 30px;
        }

        /* 스켈레톤 */
        .skeleton .skeleton-box,
        .jj-skeleton-item .skeleton-box {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: jj-skeleton 1.5s infinite;
        }

        @keyframes jj-skeleton {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* 토스트 */
        .jj-toast {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            padding: 12px 24px;
            background: #1d2327;
            color: #fff;
            border-radius: 6px;
            font-size: 14px;
            opacity: 0;
            transition: all 0.3s;
            z-index: 100000;
        }

        .jj-toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        .jj-toast.error {
            background: #d63638;
        }

        /* 반응형 */
        @media (max-width: 782px) {
            .jj-stat-grid {
                grid-template-columns: 1fr 1fr;
            }

            .jj-realtime-row {
                grid-template-columns: 1fr;
            }

            .jj-performance-grid {
                grid-template-columns: 1fr;
            }
        }
        </style>
        <?php
    }
}


// 플러그인 초기화
add_action( "plugins_loaded", function() {
    JJ_Analytics_Dashboard::instance();
} );


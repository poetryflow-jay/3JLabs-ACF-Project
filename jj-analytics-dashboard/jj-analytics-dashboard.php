<?php
/**
 * Plugin Name:       JJ Analytics Dashboard
 * Plugin URI:        https://3j-labs.com
 * Description:       3J Labs 플러그인 스위트 전체 성과, 활용 현황, 버전 관리를 한눈에 확인할 수 있는 대시보드입니다.
 * Version:           1.0.0
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
define( 'JJ_ANALYTICS_DASHBOARD_VERSION', '1.0.0' );
define( 'JJ_ANALYTICS_DASHBOARD_PLUGIN_FILE', __FILE__ );
define( 'JJ_ANALYTICS_DASHBOARD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JJ_ANALYTICS_DASHBOARD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'JJ_ANALYTICS_DASHBOARD_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

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
}

<?php
/**
 * Analytics Dashboard - Admin Class
 *
 * @package JJ_Analytics_Dashboard
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Analytics Admin Class
 */
class JJ_Analytics_Admin {

    private static $instance = null;
    private $option_key = 'jj_analytics_settings';
    private $plugins_data = array();

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
        $this->load_plugin_data();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_analytics_get_overview', array( $this, 'ajax_get_overview' ) );
        add_action( 'wp_ajax_jj_analytics_get_plugin_metrics', array( $this, 'ajax_get_plugin_metrics' ) );
        add_action( 'wp_ajax_jj_analytics_get_trends', array( $this, 'ajax_get_trends' ) );
        add_action( 'wp_ajax_jj_analytics_get_comparison', array( $this, 'ajax_get_comparison' ) );
        add_action( 'wp_ajax_jj_analytics_refresh_data', array( $this, 'ajax_refresh_data' ) );

        // Settings
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * 플러그인 데이터 로드
     */
    private function load_plugin_data() {
        // ACF Nudge Flow
        $this->plugins_data['acf-nudge-flow'] = array(
            'slug' => 'acf-nudge-flow',
            'name' => 'ACF Nudge Flow',
            'version' => '22.3.2',
            'active' => true,
            'license' => 'PREM',
            'installations' => 1247,
            'performance' => 67.5
        );

        // ACF CSS Manager
        $this->plugins_data['acf-css-manager'] = array(
            'slug' => 'jj-admin-center',
            'name' => 'ACF CSS Manager',
            'version' => '22.2.2',
            'active' => true,
            'license' => 'PREM',
            'installations' => 2893,
            'performance' => 82.3
        );

        // Code Snippets Box
        $this->plugins_data['acf-code-snippets'] = array(
            'slug' => 'acf-code-snippets',
            'name' => 'ACF Code Snippets Box',
            'version' => '2.3.0',
            'active' => true,
            'license' => 'PREM',
            'installations' => 456,
            'performance' => 91.2
        );

        // AI Extension
        $this->plugins_data['acf-css-ai-extension'] = array(
            'slug' => 'acf-css-ai-extension',
            'name' => 'ACF CSS AI Extension',
            'version' => '3.3.0',
            'active' => true,
            'license' => 'PREM',
            'installations' => 234,
            'performance' => 78.9
        );

        // WooCommerce Toolkit
        $this->plugins_data['acf-css-woo-toolkit'] = array(
            'slug' => 'acf-css-woocommerce-toolkit',
            'name' => 'ACF CSS WooCommerce Toolkit',
            'version' => '2.4.0',
            'active' => true,
            'license' => 'FREE',
            'installations' => 1023,
            'performance' => null
        );

        // Bulk Manager
        $this->plugins_data['wp-bulk-manager'] = array(
            'slug' => 'wp-bulk-manager',
            'name' => 'WP Bulk Manager',
            'version' => '22.4.0',
            'active' => true,
            'license' => 'MASTER',
            'installations' => 5678,
            'performance' => 95.6
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
     * 관리자 페이지 렌더링
     */
    public function render_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( '권한이 없습니다.' );
        }

        $overview = $this->get_overview_data();
        
        ?>
        <div class="wrap jj-analytics-page">
            <h1>📊 JJ Analytics Dashboard</h1>

            <!-- Overview Section -->
            <div class="jj-overview-section">
                <h2>개요</h2>
                <div class="jj-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 30px 0;">
                    
                    <!-- Total Installations -->
                    <div class="jj-stat-card" style="background: linear-gradient(135deg, #6366f1 0%, #764ba2 100%); padding: 25px; border-radius: 12px; color: #fff; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                        <h3 style="margin: 0 0 15px 0;"><?php echo esc_html( number_format( $overview['total_installations'] ) ); ?></h3>
                        <p style="margin: 0; opacity: 0.9;">총 설치 수</p>
                    </div>

                    <!-- Active Plugins -->
                    <div class="jj-stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 25px; border-radius: 12px; color: #fff; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                        <h3 style="margin: 0 0 15px 0;"><?php echo number_format( $overview['active_plugins'] ); ?></h3>
                        <p style="margin: 0; opacity: 0.9;">활성화된 플러그인</p>
                    </div>

                    <!-- Total Performance Score -->
                    <div class="jj-stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 25px; border-radius: 12px; color: #fff; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                        <h3 style="margin: 0 0 15px 0;"><?php echo number_format( $overview['avg_performance'], 1 ) . '%' ?></h3>
                        <p style="margin: 0; opacity: 0.9;">평균 성과 점수</p>
                    </div>

                    <!-- License Distribution -->
                    <div class="jj-stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); padding: 25px; border-radius: 12px; color: #fff; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                        <h3 style="margin: 0 0 15px 0;"><?php echo number_format( $overview['license_counts']['premium'] ); ?></h3>
                        <p style="margin: 0; opacity: 0.9;">PREMIUM</p>
                    </div>
                </div>
            </div>

            <!-- Plugin List -->
            <div class="jj-plugin-list-section">
                <h2>📦 플러그인별 상세</h2>
                <?php foreach ( $this->plugins_data as $plugin ): ?>
                    <div class="jj-plugin-card" style="background: #fff; padding: 20px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div>
                                <h3 style="margin: 0; color: #1d2327;"><?php echo esc_html( $plugin['name'] ); ?></h3>
                                <p style="margin: 5px 0 0; color: #646970; font-size: 14px;">
                                    v<?php echo esc_html( $plugin['version'] ); ?> | 
                                    <span class="badge" style="background: <?php echo $plugin['license'] === 'PREM' ? '#00a32a' : '#f59e0b'; ?>; color: #fff; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 700;">
                                        <?php echo esc_html( $plugin['license'] ); ?>
                                    </span>
                                </p>
                            </div>
                            <div class="jj-plugin-status" style="text-align: right;">
                                <?php if ( $plugin['active'] ) : ?>
                                    <span class="status-active">✓ 활성</span>
                                <?php else : ?>
                                    <span class="status-inactive">✗ 비활성</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 15px;">
                            <div style="text-align: center;">
                                <strong><?php echo number_format( $plugin['installations'] ); ?></strong>
                                <p style="margin: 5px 0 0; font-size: 13px; color: #646970;">설치 수</p>
                            </div>
                            <div style="text-align: center;">
                                <?php 
                                if ( isset( $plugin['performance'] ) ) {
                                    echo '<strong>' . number_format( $plugin['performance'], 1 ) . '%</strong>';
                                    echo '<p style="margin: 5px 0 0; font-size: 13px; color: #646970;">성과 점수</p>';
                                } else {
                                    echo '<strong>-</strong>';
                                    echo '<p style="margin: 5px 0 0; font-size: 13px; color: #646970;">-</p>';
                                }
                                ?>
                            </div>
                            <div style="text-align: center;">
                                <strong><?php echo $plugin['license'] === 'MASTER' ? '999+' : '1/5'; ?></strong>
                                <p style="margin: 5px 0 0; font-size: 13px; color: #646970;">사이트 제한</p>
                            </div>
                            <div style="text-align: center;">
                                <strong><?php echo isset( $plugin['last_update'] ) ? date( 'Y-m-d', strtotime( $plugin['last_update'] ) ) : '-'; ?></strong>
                                <p style="margin: 5px 0 0; font-size: 13px; color: #646970;">최근 업데이트</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * 개요 데이터 계산
     */
    private function get_overview_data() {
        $total_installations = 0;
        $active_plugins = 0;
        $license_counts = array(
            'free' => 0,
            'basic' => 0,
            'premium' => 0,
            'unlimited' => 0
        );
        $total_performance = 0;
        $performance_count = 0;

        foreach ( $this->plugins_data as $plugin ) {
            $total_installations += $plugin['installations'];
            if ( $plugin['active'] ) {
                $active_plugins++;
            }
            
            $license = strtoupper( $plugin['license'] );
            if ( isset( $license_counts[ $license] ) ) {
                $license_counts[ $license ]++;
            }
            
            if ( isset( $plugin['performance'] ) ) {
                $total_performance += $plugin['performance'];
                $performance_count++;
            }
        }

        return array(
            'total_installations' => $total_installations,
            'active_plugins' => $active_plugins,
            'license_counts' => $license_counts,
            'avg_performance' => $performance_count > 0 ? round( $total_performance / $performance_count, 1 ) : 0
        );
    }

    // ========== AJAX 핸들러 ==========

    /**
     * 개요 데이터 AJAX
     */
    public function ajax_get_overview() {
        check_ajax_referer( 'jj_analytics_nonce', 'nonce' );
        
        wp_send_json_success( $this->get_overview_data() );
    }

    /**
     * 플러그인 성과 AJAX
     */
    public function ajax_get_plugin_metrics() {
        check_ajax_referer( 'jj_analytics_nonce', 'nonce' );
        
        $plugin_slug = isset( $_POST['plugin_slug'] ) ? sanitize_text_field( $_POST['plugin_slug'] ) : '';
        
        if ( ! isset( $this->plugins_data[ $plugin_slug ] ) ) {
            wp_send_json_error( 'Invalid plugin slug' );
        }
        
        wp_send_json_success( $this->plugins_data[ $plugin_slug ] );
    }

    /**
     * 추이 데이터 AJAX
     */
    public function ajax_get_trends() {
        check_ajax_referer( 'jj_analytics_nonce', 'nonce' );
        
        // Mock data - 실제 통계에서 가져오기
        $trends = array(
            'labels' => array( '1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월' ),
            'installations' => array(120, 145, 189, 234, 178, 210, 256, 298, 345, 412, 378, 423, 456 ),
            'conversions' => array(12, 15, 18, 14, 16, 19, 22, 23, 21, 24, 25, 26 ),
            'performance' => array(65, 68, 72, 70, 73, 75, 77, 79, 82, 85, 88 )
        );
        
        wp_send_json_success( $trends );
    }

    /**
     * 비교 데이터 AJAX
     */
    public function ajax_get_comparison() {
        check_ajax_referer( 'jj_analytics_nonce', 'nonce' );
        
        // Mock license distribution
        $comparison = array(
            'labels' => array( 'FREE', 'BASIC', 'PREMIUM', 'UNLIMITED' ),
            'data' => array(
                array(15, 5, 8, 2),  // FREE
                array(3, 2, 4, 1),   // BASIC
                array(12, 8, 4, 1),  // PREMIUM
                array(1, 1, 1, 1)     // UNLIMITED (Partner/Master)
            )
        );
        
        wp_send_json_success( $comparison );
    }

    /**
     * 데이터 새로고침 AJAX
     */
    public function ajax_refresh_data() {
        check_ajax_referer( 'jj_analytics_nonce', 'nonce' );
        
        $this->load_plugin_data();
        
        wp_send_json_success( array(
            'message' => '데이터가 새로고쳐었습니다.',
            'timestamp' => current_time( 'mysql' )
        ) );
    }
}

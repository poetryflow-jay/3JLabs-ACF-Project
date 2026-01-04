<?php
/**
 * Plugin Name:       ACF User Journey Map Analytics Dashboard - Advanced Customer Funnel and User Journey Map Analytics Dashboard
 * Plugin URI:        https://3j-labs.com/
 * Description:       무료 사용자 여정 분석 대시보드. 트래픽 소스 추적, UTM 파라미터 분석, 광고 플랫폼별 성과 측정, 실시간 방문자 모니터링을 제공합니다. 네이버, 카카오, 구글, 메타, 틱톡 등 모든 광고 플랫폼을 자동 감지합니다.
 * Version:           1.0.0
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com/
 * Text Domain:       acf-user-journey
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package ACF_User_Journey_Analytics
 */

// 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 플러그인 상수 정의
 */
define( 'ACF_UJA_VERSION', '1.0.0' );
define( 'ACF_UJA_PLUGIN_FILE', __FILE__ );
define( 'ACF_UJA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ACF_UJA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ACF_UJA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'ACF_UJA_SLUG', 'acf-user-journey-analytics' );

/**
 * 메인 플러그인 클래스
 */
if ( ! class_exists( 'ACF_User_Journey_Analytics' ) ) {
final class ACF_User_Journey_Analytics {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * 의존성 로드
     */
    private function load_dependencies() {
        require_once ACF_UJA_PLUGIN_DIR . 'includes/class-traffic-tracker.php';
        require_once ACF_UJA_PLUGIN_DIR . 'includes/class-traffic-source-definitions.php';
        require_once ACF_UJA_PLUGIN_DIR . 'includes/class-traffic-reporter.php';
        require_once ACF_UJA_PLUGIN_DIR . 'includes/class-database.php';

        if ( is_admin() ) {
            require_once ACF_UJA_PLUGIN_DIR . 'admin/class-admin.php';
        }
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'init', array( $this, 'init' ) );
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );

        // AJAX 핸들러
        add_action( 'wp_ajax_acf_uja_track_visit', array( $this, 'ajax_track_visit' ) );
        add_action( 'wp_ajax_nopriv_acf_uja_track_visit', array( $this, 'ajax_track_visit' ) );
        add_action( 'wp_ajax_acf_uja_export_csv', array( $this, 'ajax_export_csv' ) );
        add_action( 'wp_ajax_acf_uja_realtime_data', array( $this, 'ajax_realtime_data' ) );

        // 워드프레스 대시보드 위젯
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );

        // 플러그인 목록 페이지 링크
        add_filter( 'plugin_action_links_' . ACF_UJA_PLUGIN_BASENAME, array( $this, 'add_plugin_links' ) );
        add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 2 );
    }

    /**
     * 플러그인 초기화
     */
    public function init() {
        // 초기화 작업
    }

    /**
     * 텍스트 도메인 로드
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'acf-user-journey',
            false,
            dirname( ACF_UJA_PLUGIN_BASENAME ) . '/languages'
        );
    }

    /**
     * 프론트엔드 에셋 로드
     */
    public function enqueue_public_assets() {
        // 추적 스크립트
        wp_enqueue_script(
            'acf-uja-tracker',
            ACF_UJA_PLUGIN_URL . 'assets/js/tracker.js',
            array(),
            ACF_UJA_VERSION,
            false // 헤드에서 로드
        );

        wp_localize_script( 'acf-uja-tracker', 'acf_uja_config', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'acf_uja_nonce' ),
            'userId' => get_current_user_id(),
        ) );
    }

    /**
     * 방문 추적 AJAX
     */
    public function ajax_track_visit() {
        // nonce 검증 (프론트엔드 추적이므로 실패 시에도 조용히 처리)
        if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'acf_uja_nonce' ) ) {
            wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
            return;
        }

        $tracker = ACF_UJA_Traffic_Tracker::instance();
        $tracker->track_visit( $_POST );
        wp_send_json_success();
    }

    /**
     * CSV 내보내기 AJAX
     */
    public function ajax_export_csv() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
            return;
        }

        if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'acf_uja_export' ) ) {
            wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
            return;
        }

        $reporter = ACF_UJA_Traffic_Reporter::instance();
        $csv = $reporter->generate_csv( $_POST['period'] ?? '7days' );

        wp_send_json_success( array( 'csv' => $csv ) );
    }

    /**
     * 실시간 데이터 AJAX
     */
    public function ajax_realtime_data() {
        if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'acf_uja_realtime' ) ) {
            wp_send_json_error();
            return;
        }

        $reporter = ACF_UJA_Traffic_Reporter::instance();
        wp_send_json_success( $reporter->get_realtime_data() );
    }

    /**
     * 대시보드 위젯 추가
     */
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'acf_uja_realtime_widget',
            '📊 실시간 트래픽 모니터링 - User Journey Analytics',
            array( $this, 'render_dashboard_widget' )
        );
    }

    /**
     * 대시보드 위젯 렌더링
     */
    public function render_dashboard_widget() {
        $reporter = ACF_UJA_Traffic_Reporter::instance();
        $data = $reporter->get_realtime_data();
        include ACF_UJA_PLUGIN_DIR . 'admin/views/dashboard-widget.php';
    }

    /**
     * 플러그인 링크 추가
     */
    public function add_plugin_links( $links ) {
        $custom_links = array(
            '<a href="' . admin_url( 'admin.php?page=acf-user-journey' ) . '">' . __( '대시보드', 'acf-user-journey' ) . '</a>',
        );
        return array_merge( $custom_links, $links );
    }

    /**
     * 플러그인 행 메타 추가
     */
    public function add_plugin_row_meta( $links, $file ) {
        if ( ACF_UJA_PLUGIN_BASENAME !== $file ) {
            return $links;
        }

        $extra_links = array(
            '<a href="https://3j-labs.com/docs/user-journey-analytics" target="_blank">' . __( '문서', 'acf-user-journey' ) . '</a>',
        );

        // 프로모션 링크 추가
        if ( ! is_plugin_active( 'acf-nudge-flow/acf-nudge-flow.php' ) ) {
            $extra_links[] = '<a href="https://wordpress.org/plugins/acf-nudge-flow/" target="_blank" style="color: #22c55e; font-weight: 600;">' . __( '🎯 Nudge Flow로 마케팅 자동화', 'acf-user-journey' ) . '</a>';
        }
        if ( ! is_plugin_active( 'oneclick-seo-pro/oneclick-seo-pro.php' ) ) {
            $extra_links[] = '<a href="https://wordpress.org/plugins/oneclick-seo-pro/" target="_blank" style="color: #6366f1; font-weight: 600;">' . __( '🔍 1-Click SEO로 검색 최적화', 'acf-user-journey' ) . '</a>';
        }

        return array_merge( $links, $extra_links );
    }

    /**
     * 플러그인 활성화
     */
    public function activate() {
        $db = ACF_UJA_Database::instance();
        $db->create_tables();

        // 기본 옵션 설정
        $defaults = array(
            'enabled' => true,
            'track_logged_in' => true,
            'exclude_roles' => array( 'administrator' ),
            'data_retention_days' => 90,
        );

        if ( ! get_option( 'acf_uja_settings' ) ) {
            update_option( 'acf_uja_settings', $defaults );
        }

        flush_rewrite_rules();
    }

    /**
     * 플러그인 비활성화
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * 다른 플러그인과 데이터 공유를 위한 API
     */
    public static function get_traffic_data( $start_date, $end_date, $type = 'summary' ) {
        $reporter = ACF_UJA_Traffic_Reporter::instance();

        switch ( $type ) {
            case 'summary':
                return $reporter->get_traffic_summary( $start_date, $end_date );
            case 'sources':
                return $reporter->get_referrer_breakdown( $start_date, $end_date );
            case 'funnel':
                return $reporter->get_funnel_analysis( $start_date, $end_date );
            case 'roi':
                return $reporter->get_roi_analysis( $start_date, $end_date );
            default:
                return null;
        }
    }
}
} // End of class

/**
 * 플러그인 인스턴스 반환
 */
function acf_user_journey_analytics() {
    return ACF_User_Journey_Analytics::get_instance();
}

// 플러그인 시작
acf_user_journey_analytics();

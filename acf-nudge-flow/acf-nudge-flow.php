<?php
/**
 * Plugin Name:       ACF MBA - Marketing Booster Accelerator (Advanced Custom Funnel)
 * Plugin URI:        https://3j-labs.com
 * Description:       ACF MBA (Advanced Custom Funnel Marketing Booster Accelerator) - 트리거 기반 넛지 마케팅 자동화 플러그인입니다. IF-DO 방식의 시각적 워크플로우 빌더로 방문자 행동에 따른 팝업, 알림, 할인, 업셀링을 자동화합니다. WooCommerce와 완벽 연동됩니다.
 * Version:           22.0.0
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com
 * Text Domain:       acf-nudge-flow
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package ACF_MBA
 */

// 직접 접근 방지
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 플러그인 상수 정의
 */
define( 'ACF_NUDGE_FLOW_VERSION', '22.0.0' ); // [v22.0.0] 전략적 프리셋 및 마켓플레이스 통합 메이저 업데이트
define( 'ACF_NUDGE_FLOW_PLUGIN_FILE', __FILE__ );
define( 'ACF_NUDGE_FLOW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ACF_NUDGE_FLOW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ACF_NUDGE_FLOW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * 메인 플러그인 클래스
 */
final class ACF_Nudge_Flow {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 트리거 매니저
     */
    public $triggers;

    /**
     * 액션 매니저
     */
    public $actions;

    /**
     * 워크플로우 매니저
     */
    public $workflows;

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
        // Core classes
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-trigger-manager.php';
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-action-manager.php';
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-workflow-manager.php';
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-visitor-tracker.php';
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-condition-evaluator.php';

        // Admin
        if ( is_admin() ) {
            require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'admin/class-admin.php';
        }

        // Public
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'public/class-frontend.php';
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 플러그인 활성화/비활성화
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        // 초기화
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

        // 스크립트/스타일
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // AJAX 핸들러
        add_action( 'wp_ajax_acf_nudge_track', array( $this, 'ajax_track_event' ) );
        add_action( 'wp_ajax_nopriv_acf_nudge_track', array( $this, 'ajax_track_event' ) );
        add_action( 'wp_ajax_acf_nudge_dismiss', array( $this, 'ajax_dismiss_nudge' ) );
        add_action( 'wp_ajax_nopriv_acf_nudge_dismiss', array( $this, 'ajax_dismiss_nudge' ) );
    }

    /**
     * 플러그인 초기화
     */
    public function init() {
        // 매니저 인스턴스 생성
        $this->triggers = new ACF_Nudge_Trigger_Manager();
        $this->actions = new ACF_Nudge_Action_Manager();
        $this->workflows = new ACF_Nudge_Workflow_Manager();

        // 커스텀 포스트 타입 등록
        $this->register_post_types();
    }

    /**
     * 텍스트 도메인 로드
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'acf-nudge-flow',
            false,
            dirname( ACF_NUDGE_FLOW_PLUGIN_BASENAME ) . '/languages'
        );
    }

    /**
     * 커스텀 포스트 타입 등록
     */
    private function register_post_types() {
        // 워크플로우 CPT
        register_post_type( 'acf_nudge_workflow', array(
            'labels' => array(
                'name'               => __( '워크플로우', 'acf-nudge-flow' ),
                'singular_name'      => __( '워크플로우', 'acf-nudge-flow' ),
                'add_new'            => __( '새 워크플로우', 'acf-nudge-flow' ),
                'add_new_item'       => __( '새 워크플로우 추가', 'acf-nudge-flow' ),
                'edit_item'          => __( '워크플로우 편집', 'acf-nudge-flow' ),
            ),
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => 'acf-nudge-flow',
            'capability_type'    => 'post',
            'supports'           => array( 'title' ),
            'menu_icon'          => 'dashicons-chart-area',
        ) );

        // 넛지 템플릿 CPT
        register_post_type( 'acf_nudge_template', array(
            'labels' => array(
                'name'               => __( '넛지 템플릿', 'acf-nudge-flow' ),
                'singular_name'      => __( '넛지 템플릿', 'acf-nudge-flow' ),
            ),
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => 'acf-nudge-flow',
            'capability_type'    => 'post',
            'supports'           => array( 'title', 'editor' ),
        ) );
    }

    /**
     * 프론트엔드 에셋 로드
     */
    public function enqueue_public_assets() {
        wp_enqueue_style(
            'acf-nudge-flow-public',
            ACF_NUDGE_FLOW_PLUGIN_URL . 'public/css/nudge-flow.css',
            array(),
            ACF_NUDGE_FLOW_VERSION
        );

        wp_enqueue_script(
            'acf-nudge-flow-public',
            ACF_NUDGE_FLOW_PLUGIN_URL . 'public/js/nudge-flow.js',
            array( 'jquery' ),
            ACF_NUDGE_FLOW_VERSION,
            true
        );

        // 방문자 데이터 전달
        wp_localize_script( 'acf-nudge-flow-public', 'acfNudgeFlow', array(
            'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
            'nonce'        => wp_create_nonce( 'acf_nudge_flow_nonce' ),
            'visitorData'  => $this->get_visitor_data(),
            'workflows'    => $this->get_active_workflows(),
        ) );
    }

    /**
     * 관리자 에셋 로드
     */
    public function enqueue_admin_assets( $hook ) {
        // ACF Nudge Flow 페이지에서만 로드
        if ( strpos( $hook, 'acf-nudge-flow' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'acf-nudge-flow-admin',
            ACF_NUDGE_FLOW_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            ACF_NUDGE_FLOW_VERSION
        );

        wp_enqueue_script(
            'acf-nudge-flow-admin',
            ACF_NUDGE_FLOW_PLUGIN_URL . 'admin/js/admin.js',
            array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ),
            ACF_NUDGE_FLOW_VERSION,
            true
        );

        // 워크플로우 빌더 (React 기반)
        wp_enqueue_script(
            'acf-nudge-flow-builder',
            ACF_NUDGE_FLOW_PLUGIN_URL . 'admin/js/workflow-builder.js',
            array( 'wp-element', 'wp-components', 'wp-i18n' ),
            ACF_NUDGE_FLOW_VERSION,
            true
        );
    }

    /**
     * 방문자 데이터 수집
     */
    public function get_visitor_data() {
        $tracker = new ACF_Nudge_Visitor_Tracker();
        return $tracker->get_data();
    }

    /**
     * 활성 워크플로우 조회
     */
    public function get_active_workflows() {
        if ( ! $this->workflows ) {
            return array();
        }
        return $this->workflows->get_active();
    }

    /**
     * 이벤트 추적 AJAX
     */
    public function ajax_track_event() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $event_type = isset( $_POST['event_type'] ) ? sanitize_text_field( $_POST['event_type'] ) : '';
        $event_data = isset( $_POST['event_data'] ) ? array_map( 'sanitize_text_field', $_POST['event_data'] ) : array();

        // 이벤트 저장
        $tracker = new ACF_Nudge_Visitor_Tracker();
        $tracker->track_event( $event_type, $event_data );

        wp_send_json_success();
    }

    /**
     * 넛지 닫기 AJAX
     */
    public function ajax_dismiss_nudge() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $nudge_id = isset( $_POST['nudge_id'] ) ? intval( $_POST['nudge_id'] ) : 0;
        $dismiss_type = isset( $_POST['dismiss_type'] ) ? sanitize_text_field( $_POST['dismiss_type'] ) : 'session';

        // 닫기 상태 저장
        $tracker = new ACF_Nudge_Visitor_Tracker();
        $tracker->dismiss_nudge( $nudge_id, $dismiss_type );

        wp_send_json_success();
    }

    /**
     * 플러그인 활성화
     */
    public function activate() {
        // 데이터베이스 테이블 생성
        $this->create_tables();

        // 기본 옵션 설정
        $this->set_default_options();

        // 리라이트 규칙 갱신
        flush_rewrite_rules();
    }

    /**
     * 플러그인 비활성화
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * 데이터베이스 테이블 생성
     */
    private function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // 이벤트 로그 테이블
        $table_events = $wpdb->prefix . 'acf_nudge_events';
        $sql_events = "CREATE TABLE IF NOT EXISTS $table_events (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            visitor_id varchar(64) NOT NULL,
            event_type varchar(50) NOT NULL,
            event_data longtext,
            workflow_id bigint(20),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY visitor_id (visitor_id),
            KEY event_type (event_type)
        ) $charset_collate;";

        // 방문자 테이블
        $table_visitors = $wpdb->prefix . 'acf_nudge_visitors';
        $sql_visitors = "CREATE TABLE IF NOT EXISTS $table_visitors (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            visitor_id varchar(64) NOT NULL,
            user_id bigint(20) DEFAULT 0,
            first_visit datetime,
            last_visit datetime,
            visit_count int(11) DEFAULT 1,
            referrer varchar(255),
            utm_source varchar(100),
            utm_medium varchar(100),
            utm_campaign varchar(100),
            data longtext,
            PRIMARY KEY (id),
            UNIQUE KEY visitor_id (visitor_id),
            KEY user_id (user_id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_events );
        dbDelta( $sql_visitors );
    }

    /**
     * 기본 옵션 설정
     */
    private function set_default_options() {
        $defaults = array(
            'enabled'              => true,
            'debug_mode'           => false,
            'cookie_duration'      => 30, // days
            'max_nudges_per_visit' => 3,
            'delay_between_nudges' => 60, // seconds
            'excluded_pages'       => array(),
            'excluded_roles'       => array( 'administrator' ),
        );

        if ( ! get_option( 'acf_nudge_flow_settings' ) ) {
            update_option( 'acf_nudge_flow_settings', $defaults );
        }
    }
}

/**
 * 플러그인 인스턴스 반환
 */
function acf_nudge_flow() {
    return ACF_Nudge_Flow::get_instance();
}

// 플러그인 시작
acf_nudge_flow();

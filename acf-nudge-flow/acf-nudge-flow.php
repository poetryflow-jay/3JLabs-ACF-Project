<?php
/**
 * Plugin Name:       넛지 플로우 - ACF MBA (Advanced Custom Funnels for Marketing Boosting Accelerator)
 * Plugin URI:        https://3j-labs.com/
 * Description:       넛지 플로우 (Nudge Flow) - ACF MBA (Advanced Custom Funnels for Marketing Boosting Accelerator). 트리거 기반 넛지 마케팅 자동화 플러그인입니다. IF-DO 방식의 시각적 워크플로우 빌더로 방문자 행동에 따른 팝업, 알림, 할인, 업셀링을 자동화합니다. WooCommerce와 완벽 연동됩니다.
 * Version:           23.0.0
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com/
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
define( 'ACF_NUDGE_FLOW_VERSION', '23.0.0' ); // [v23.0.0] 드래그 앤 드롭 워크플로우 빌더 + IF-DO 방식 프론트엔드 호환
define( 'ACF_NUDGE_FLOW_PLUGIN_FILE', __FILE__ );
define( 'ACF_NUDGE_FLOW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ACF_NUDGE_FLOW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ACF_NUDGE_FLOW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'ACF_NUDGE_FLOW_SLUG', 'acf-nudge-flow' );

// [v25.0.0] 보안 모듈 및 라이센스 관리 로드
$shared_path = dirname( dirname( __FILE__ ) ) . '/shared-ui-assets';
if ( file_exists( $shared_path . '/class-jj-security-module-v25.php' ) ) {
    require_once $shared_path . '/class-jj-security-module-v25.php';
    if ( class_exists( 'JJ_Security_Module_V25_Loader' ) ) {
        JJ_Security_Module_V25_Loader::instance( ACF_NUDGE_FLOW_PLUGIN_DIR, ACF_NUDGE_FLOW_PLUGIN_URL, ACF_NUDGE_FLOW_VERSION, ACF_NUDGE_FLOW_SLUG );
    }
}
if ( file_exists( $shared_path . '/class-jj-license-manager-shared.php' ) ) {
    require_once $shared_path . '/class-jj-license-manager-shared.php';
    if ( class_exists( 'JJ_License_Manager_Shared' ) ) {
        JJ_License_Manager_Shared::instance( ACF_NUDGE_FLOW_SLUG );
    }
}

/**
 * 메인 플러그인 클래스
 * [v22.4.3] 클래스 중복 선언 방지 추가
 */
if ( ! class_exists( 'ACF_Nudge_Flow' ) ) {
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
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-mab-optimizer.php';
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-rfm-calculator.php';
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-template-database.php';
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-data-source-integration.php';
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-native-analytics.php';
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-ad-pixel-manager.php';
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-traffic-source-definitions.php';
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-traffic-source-reporter.php';

        // Admin
        if ( is_admin() ) {
            require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'admin/class-admin.php';
        }

        // Public
        require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'public/class-frontend.php';
        
        // Plugin List Enhancer (플러그인 목록 페이지 향상)
        if ( file_exists( dirname( dirname( __FILE__ ) ) . '/acf-css-really-simple-style-management-center-master/includes/class-jj-plugin-list-enhancer.php' ) ) {
            require_once dirname( dirname( __FILE__ ) ) . '/acf-css-really-simple-style-management-center-master/includes/class-jj-plugin-list-enhancer.php';
        }
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
        add_action( 'wp_ajax_acf_nudge_analytics', array( $this, 'ajax_save_analytics' ) );
        add_action( 'wp_ajax_nopriv_acf_nudge_analytics', array( $this, 'ajax_save_analytics' ) );
        add_action( 'wp_ajax_acf_nudge_dismiss', array( $this, 'ajax_dismiss_nudge' ) );
        add_action( 'wp_ajax_nopriv_acf_nudge_dismiss', array( $this, 'ajax_dismiss_nudge' ) );
        add_action( 'wp_ajax_acf_nudge_conversion', array( $this, 'ajax_record_conversion' ) );
        add_action( 'wp_ajax_nopriv_acf_nudge_conversion', array( $this, 'ajax_record_conversion' ) );

        // [v22.7.0] UTM 및 퍼널 추적 AJAX 핸들러
        add_action( 'wp_ajax_acf_nf_track_utm', array( $this, 'ajax_track_utm' ) );
        add_action( 'wp_ajax_nopriv_acf_nf_track_utm', array( $this, 'ajax_track_utm' ) );
        add_action( 'wp_ajax_acf_nf_track_dwell_time', array( $this, 'ajax_track_dwell_time' ) );
        add_action( 'wp_ajax_nopriv_acf_nf_track_dwell_time', array( $this, 'ajax_track_dwell_time' ) );
        add_action( 'wp_ajax_acf_nf_record_funnel_step', array( $this, 'ajax_record_funnel_step' ) );
        add_action( 'wp_ajax_nopriv_acf_nf_record_funnel_step', array( $this, 'ajax_record_funnel_step' ) );
        add_action( 'wp_ajax_acf_nf_record_funnel_event', array( $this, 'ajax_record_funnel_event' ) );
        add_action( 'wp_ajax_nopriv_acf_nf_record_funnel_event', array( $this, 'ajax_record_funnel_event' ) );

        // [v22.9.1] 트래픽 분석 CSV 내보내기
        add_action( 'wp_ajax_acf_nf_export_traffic_csv', array( $this, 'ajax_export_traffic_csv' ) );

        // 플러그인 목록 페이지 링크
        add_filter( 'plugin_action_links_' . ACF_NUDGE_FLOW_PLUGIN_BASENAME, array( $this, 'add_plugin_action_links' ) );
        add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 2 );
    }

    /**
     * 플러그인 액션 링크 추가
     */
    public function add_plugin_action_links( $links ) {
        $custom_links = array(
            '<a href="' . admin_url( 'admin.php?page=acf-nudge-flow' ) . '">' . __( '대시보드', 'acf-nudge-flow' ) . '</a>',
            '<a href="' . admin_url( 'admin.php?page=acf-nudge-flow-settings' ) . '">' . __( '설정', 'acf-nudge-flow' ) . '</a>',
        );
        return array_merge( $custom_links, $links );
    }

    /**
     * 플러그인 행 메타 추가
     */
    public function add_plugin_row_meta( $links, $file ) {
        if ( ACF_NUDGE_FLOW_PLUGIN_BASENAME !== $file ) {
            return $links;
        }

        $extra_links = array(
            '<a href="https://3j-labs.com/docs/nudge-flow" target="_blank">' . __( '문서', 'acf-nudge-flow' ) . '</a>',
        );

        // 프로모션 링크 추가
        if ( ! is_plugin_active( 'acf-user-journey-analytics/acf-user-journey-analytics.php' ) ) {
            $extra_links[] = '<a href="https://wordpress.org/plugins/acf-user-journey-analytics/" target="_blank" style="color: #6366f1; font-weight: 600;">' . __( '📊 무료 분석 대시보드 설치', 'acf-nudge-flow' ) . '</a>';
        }
        if ( ! is_plugin_active( 'oneclick-seo-pro/oneclick-seo-pro.php' ) ) {
            $extra_links[] = '<a href="https://wordpress.org/plugins/oneclick-seo-pro/" target="_blank" style="color: #22c55e; font-weight: 600;">' . __( '🔍 1-Click SEO로 트래픽 증가', 'acf-nudge-flow' ) . '</a>';
        }

        return array_merge( $links, $extra_links );
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
        // 워크플로우 CPT (메뉴에서 숨김 - 수동 메뉴 사용)
        register_post_type( 'acf_nudge_workflow', array(
            'labels' => array(
                'name'               => __( '워크플로우', 'acf-nudge-flow' ),
                'singular_name'      => __( '워크플로우', 'acf-nudge-flow' ),
                'add_new'            => __( '새 워크플로우', 'acf-nudge-flow' ),
                'add_new_item'       => __( '새 워크플로우 추가', 'acf-nudge-flow' ),
                'edit_item'          => __( '워크플로우 편집', 'acf-nudge-flow' ),
            ),
            'public'             => false,
            'show_ui'            => false, // UI 숨김 (수동 메뉴 사용)
            'show_in_menu'       => false, // 메뉴에서 숨김
            'capability_type'    => 'post',
            'supports'           => array( 'title' ),
        ) );

        // 넛지 템플릿 CPT (메뉴에서 숨김 - 수동 메뉴 사용)
        register_post_type( 'acf_nudge_template', array(
            'labels' => array(
                'name'               => __( '넛지 템플릿', 'acf-nudge-flow' ),
                'singular_name'      => __( '넛지 템플릿', 'acf-nudge-flow' ),
            ),
            'public'             => false,
            'show_ui'            => false, // UI 숨김 (수동 메뉴 사용)
            'show_in_menu'       => false, // 메뉴에서 숨김
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

        // [v22.5.2] 외부 분석 도구 통합 스크립트
        wp_enqueue_script(
            'acf-nudge-flow-analytics',
            ACF_NUDGE_FLOW_PLUGIN_URL . 'assets/js/analytics-integration.js',
            array( 'jquery', 'acf-nudge-flow-public' ),
            ACF_NUDGE_FLOW_VERSION,
            true
        );

        // [v22.6.0] 네이티브 애널리틱스 스크립트
        $settings = get_option( 'acf_nudge_flow_settings', array() );
        $native_analytics_enabled = isset( $settings['native_analytics_enabled'] ) ? $settings['native_analytics_enabled'] : true;
        
        if ( $native_analytics_enabled ) {
            wp_enqueue_script(
                'acf-nudge-flow-native-analytics',
                ACF_NUDGE_FLOW_PLUGIN_URL . 'assets/js/native-analytics.js',
                array( 'acf-nudge-flow-public' ),
                ACF_NUDGE_FLOW_VERSION,
                true
            );

            // 네이티브 애널리틱스 설정 전달
            wp_localize_script( 'acf-nudge-flow-native-analytics', 'acf_nf_native_analytics_config', array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'acf_nudge_flow_nonce' ),
                'userId' => get_current_user_id(),
                'siteUrl' => home_url(),
            ) );
        }

        // 외부 분석 도구 설정 전달
        wp_localize_script( 'acf-nudge-flow-analytics', 'acf_nf_analytics_config', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'acf_nudge_flow_nonce' ),
            'userId' => get_current_user_id(),
        ) );

        // [v22.7.0] UTM 및 트래픽 소스 추적 스크립트
        $utm_settings = get_option( 'acf_nudge_flow_utm_settings', array( 'enabled' => true ) );
        if ( ! empty( $utm_settings['enabled'] ) ) {
            wp_enqueue_script(
                'acf-nudge-flow-utm-tracker',
                ACF_NUDGE_FLOW_PLUGIN_URL . 'assets/js/utm-tracker.js',
                array(),
                ACF_NUDGE_FLOW_VERSION,
                false // 헤드에서 로드하여 빠른 추적
            );

            wp_localize_script( 'acf-nudge-flow-utm-tracker', 'acf_nf_utm_config', array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'acf_nudge_flow_nonce' ),
                'userId' => get_current_user_id(),
                'storeDuration' => $utm_settings['store_duration'] ?? 30,
                'attributionModel' => $utm_settings['attribution_model'] ?? 'last_click',
            ) );
        }

        // 방문자 데이터 전달
        $visitor_data = $this->get_visitor_data();
        
        // [v22.5.2] 통합 프로필 데이터 추가
        if ( class_exists( 'ACF_Nudge_Flow_Data_Source_Integration' ) ) {
            $data_source = ACF_Nudge_Flow_Data_Source_Integration::instance();
            $user_id = get_current_user_id();
            $visitor_id = isset( $visitor_data['visitor_id'] ) ? $visitor_data['visitor_id'] : '';
            
            // 통합 프로필 조회
            if ( $user_id ) {
                $visitor_data['unified_profile'] = $data_source->get_unified_profile( $user_id );
            }
            $visitor_data['active_sources'] = $data_source->get_active_sources();
        }
        
        wp_localize_script( 'acf-nudge-flow-public', 'acfNudgeFlow', array(
            'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
            'nonce'        => wp_create_nonce( 'acf_nudge_flow_nonce' ),
            'visitorData'  => $visitor_data,
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

        // [v22.3.0] UI System 2026 Enhancement
        $enhanced_css_path = ACF_NUDGE_FLOW_PLUGIN_DIR . 'assets/css/jj-nudge-flow-enhanced-2026.css';
        if ( file_exists( $enhanced_css_path ) ) {
            $css_version = ACF_NUDGE_FLOW_VERSION . '.' . filemtime( $enhanced_css_path );
            wp_enqueue_style(
                'acf-nudge-flow-enhanced-2026',
                ACF_NUDGE_FLOW_PLUGIN_URL . 'assets/css/jj-nudge-flow-enhanced-2026.css',
                array( 'acf-nudge-flow-admin' ),
                $css_version
            );
        }
        
        // [v22.3.1] MAB Optimization Visualization
        $mab_viz_css_path = ACF_NUDGE_FLOW_PLUGIN_DIR . 'assets/css/jj-nudge-mab-visualization.css';
        if ( file_exists( $mab_viz_css_path ) ) {
            $css_version = ACF_NUDGE_FLOW_VERSION . '.' . filemtime( $mab_viz_css_path );
            wp_enqueue_style(
                'acf-nudge-mab-visualization',
                ACF_NUDGE_FLOW_PLUGIN_URL . 'assets/css/jj-nudge-mab-visualization.css',
                array( 'acf-nudge-flow-admin' ),
                $css_version
            );
        }
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
        
        $workflows = $this->workflows->get_active();
        
        // MAB 자동 최적화가 활성화된 경우, Thompson Sampling으로 넛지 선택
        $settings = get_option( 'acf_nudge_flow_settings', array() );
        $mab_enabled = isset( $settings['mab_enabled'] ) && $settings['mab_enabled'];
        
        if ( $mab_enabled && ! empty( $workflows ) ) {
            $mab = JJ_MAB_Optimizer::instance();
            $stats = get_option( 'jj_nudge_mab_stats', array() );
            
            // 워크플로우별 통계 포함
            foreach ( $workflows as &$workflow ) {
                $workflow_id = $workflow['id'];
                if ( isset( $stats[ $workflow_id ] ) ) {
                    $workflow['successes'] = $stats[ $workflow_id ]['successes'];
                    $workflow['failures'] = $stats[ $workflow_id ]['failures'];
                } else {
                    $workflow['successes'] = 0;
                    $workflow['failures'] = 0;
                }
            }
            
            // Thompson Sampling으로 최적 넛지 선택
            $selected_id = $mab->select_nudge( $workflows );
            
            // 선택된 넛지만 반환 (나머지는 필터링)
            $workflows = array_filter( $workflows, function( $w ) use ( $selected_id ) {
                return $w['id'] === $selected_id;
            } );
        }
        
        return $workflows;
    }

    /**
     * 이벤트 추적 AJAX
     * 
     * [v22.5.2] 분석 데이터 처리 지원
     */
    public function ajax_track_event() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $event_type = isset( $_POST['event_type'] ) ? sanitize_text_field( $_POST['event_type'] ) : '';
        $event_data = isset( $_POST['event_data'] ) ? $_POST['event_data'] : array();

        // 분석 데이터인 경우 특별 처리
        if ( $event_type === 'analytics_data' && is_array( $event_data ) ) {
            $this->save_analytics_data( $event_data );
        } else {
            // 일반 이벤트 데이터 정제
            $event_data = array_map( 'sanitize_text_field', $event_data );
        }

        // 이벤트 저장
        $tracker = new ACF_Nudge_Visitor_Tracker();
        $tracker->track_event( $event_type, $event_data );

        wp_send_json_success();
    }

    /**
     * 분석 데이터 저장 AJAX
     * 
     * [v22.5.2] 프론트엔드에서 수집한 분석 도구 데이터 저장
     */
    public function ajax_save_analytics() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $analytics_data = isset( $_POST['data'] ) ? $_POST['data'] : array();
        
        if ( ! empty( $analytics_data ) ) {
            $this->save_analytics_data( $analytics_data );
        }

        wp_send_json_success();
    }

    /**
     * 분석 데이터 저장
     * 
     * @param array $analytics_data 분석 데이터
     */
    private function save_analytics_data( $analytics_data ) {
        $tracker = new ACF_Nudge_Visitor_Tracker();
        $visitor_id = $tracker->get_visitor_id();
        $user_id = get_current_user_id();

        // 데이터 소스 통합 클래스로 데이터 저장
        if ( class_exists( 'ACF_Nudge_Flow_Data_Source_Integration' ) ) {
            $data_source = ACF_Nudge_Flow_Data_Source_Integration::instance();
            
            // 사용자 메타에 저장 (로그인 사용자)
            if ( $user_id ) {
                update_user_meta( $user_id, '_acf_nudge_analytics_data', $analytics_data );
                
                // MonsterInsights 데이터가 있으면 별도 저장
                if ( isset( $analytics_data['monsterinsights'] ) ) {
                    update_user_meta( $user_id, '_monsterinsights_user_data', $analytics_data['monsterinsights'] );
                }
                
                // Google Analytics 데이터가 있으면 별도 저장
                if ( isset( $analytics_data['google_analytics'] ) ) {
                    update_user_meta( $user_id, '_ga_user_data', $analytics_data['google_analytics'] );
                }
            }

            // 방문자 테이블에도 저장 (비로그인 사용자)
            if ( $visitor_id ) {
                global $wpdb;
                $table = $wpdb->prefix . 'acf_nudge_visitors';
                
                $wpdb->update(
                    $table,
                    array( 'data' => wp_json_encode( $analytics_data ) ),
                    array( 'visitor_id' => $visitor_id ),
                    array( '%s' ),
                    array( '%s' )
                );
            }
        }
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
     * 전환 추적 AJAX (MAB 학습용)
     */
    public function ajax_record_conversion() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $nudge_id = isset( $_POST['nudge_id'] ) ? sanitize_text_field( $_POST['nudge_id'] ) : '';
        $success = isset( $_POST['success'] ) && $_POST['success'] === 'true';

        if ( empty( $nudge_id ) ) {
            wp_send_json_error( array( 'message' => __( '넛지 ID가 필요합니다.', 'acf-nudge-flow' ) ) );
        }

        // MAB 최적화 엔진에 결과 기록
        $mab = JJ_MAB_Optimizer::instance();
        $mab->record_outcome( $nudge_id, $success );

        wp_send_json_success( array(
            'message' => __( '전환이 기록되었습니다.', 'acf-nudge-flow' ),
            'conversion_rates' => $mab->get_conversion_rates()
        ) );
    }

    /**
     * [v22.8.0] UTM 및 광고 파라미터 추적 AJAX (확장된 파라미터 지원)
     */
    public function ajax_track_utm() {
        // Nonce는 선택적 (Beacon API 호환성)
        $visitor_id = isset( $_POST['visitor_id'] ) ? sanitize_text_field( $_POST['visitor_id'] ) : '';
        $session_id = isset( $_POST['session_id'] ) ? sanitize_text_field( $_POST['session_id'] ) : '';
        $user_id = isset( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : get_current_user_id();
        $data = isset( $_POST['data'] ) ? $_POST['data'] : array();

        if ( is_string( $data ) ) {
            $data = json_decode( stripslashes( $data ), true );
        }

        if ( empty( $visitor_id ) || empty( $data ) ) {
            wp_send_json_error();
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        // 기존 레코드 확인
        $existing = $wpdb->get_row( $wpdb->prepare(
            "SELECT id, touch_count FROM $table WHERE visitor_id = %s AND session_id = %s",
            $visitor_id,
            $session_id
        ) );

        // 네이버 파라미터 추출
        $naver_params = isset( $data['naver_params'] ) ? $data['naver_params'] : array();

        // 카카오 파라미터 추출
        $kakao_params = isset( $data['kakao_params'] ) ? $data['kakao_params'] : array();

        // 광고 소스 감지 결과
        $ad_source = isset( $data['ad_source'] ) ? $data['ad_source'] : array();

        $insert_data = array(
            'visitor_id' => $visitor_id,
            'user_id' => $user_id,
            'session_id' => $session_id,

            // UTM 파라미터
            'utm_source' => sanitize_text_field( $data['utm_source'] ?? '' ),
            'utm_medium' => sanitize_text_field( $data['utm_medium'] ?? '' ),
            'utm_campaign' => sanitize_text_field( $data['utm_campaign'] ?? '' ),
            'utm_term' => sanitize_text_field( $data['utm_term'] ?? '' ),
            'utm_content' => sanitize_text_field( $data['utm_content'] ?? '' ),

            // 글로벌 광고 클릭 ID
            'gclid' => sanitize_text_field( $data['gclid'] ?? '' ),
            'gad_source' => sanitize_text_field( $data['gad_source'] ?? '' ),
            'gbraid' => sanitize_text_field( $data['gbraid'] ?? '' ),
            'wbraid' => sanitize_text_field( $data['wbraid'] ?? '' ),
            'fbclid' => sanitize_text_field( $data['fbclid'] ?? '' ),
            'ttclid' => sanitize_text_field( $data['ttclid'] ?? '' ),
            'twclid' => sanitize_text_field( $data['twclid'] ?? '' ),
            'msclkid' => sanitize_text_field( $data['msclkid'] ?? '' ),
            'li_fat_id' => sanitize_text_field( $data['li_fat_id'] ?? '' ),
            'epik' => sanitize_text_field( $data['epik'] ?? '' ),
            'sccid' => sanitize_text_field( $data['ScCid'] ?? $data['sccid'] ?? '' ),

            // 네이버 광고 파라미터
            'n_media' => sanitize_text_field( $naver_params['n_media'] ?? '' ),
            'n_query' => sanitize_text_field( $naver_params['n_query'] ?? '' ),
            'n_rank' => sanitize_text_field( $naver_params['n_rank'] ?? '' ),
            'n_ad_group' => sanitize_text_field( $naver_params['n_ad_group'] ?? '' ),
            'n_ad' => sanitize_text_field( $naver_params['n_ad'] ?? '' ),
            'n_keyword_id' => sanitize_text_field( $naver_params['n_keyword_id'] ?? '' ),
            'n_keyword' => sanitize_text_field( $naver_params['n_keyword'] ?? '' ),
            'n_campaign_type' => sanitize_text_field( $naver_params['n_campaign_type'] ?? '' ),
            'n_ad_group_type' => sanitize_text_field( $naver_params['n_ad_group_type'] ?? '' ),
            'n_match' => sanitize_text_field( $naver_params['n_match'] ?? '' ),
            'n_mall_pid' => sanitize_text_field( $naver_params['n_mall_pid'] ?? '' ),
            'n_mall_id' => sanitize_text_field( $naver_params['n_mall_id'] ?? '' ),
            'na_source' => sanitize_text_field( $naver_params['na_source'] ?? '' ),
            'na_medium' => sanitize_text_field( $naver_params['na_medium'] ?? '' ),
            'na_campaign' => sanitize_text_field( $naver_params['na_campaign'] ?? '' ),
            'na_adset' => sanitize_text_field( $naver_params['na_adset'] ?? '' ),
            'na_ad' => sanitize_text_field( $naver_params['na_ad'] ?? '' ),

            // 카카오 광고 파라미터
            'kakao_campaign' => sanitize_text_field( $kakao_params['kakao_campaign'] ?? '' ),
            'kakao_adgrp' => sanitize_text_field( $kakao_params['kakao_adgrp'] ?? '' ),
            'kakao_creative' => sanitize_text_field( $kakao_params['kakao_creative'] ?? '' ),
            'kakao_keyword' => sanitize_text_field( $kakao_params['kakao_keyword'] ?? '' ),
            'dkwid' => sanitize_text_field( $kakao_params['dkwid'] ?? '' ),
            'dkw' => sanitize_text_field( $kakao_params['dkw'] ?? '' ),

            // 기타 광고 파라미터
            'dablead' => sanitize_text_field( $data['other_ad_params']['dablead']['value'] ?? '' ),
            'ti' => sanitize_text_field( $data['other_ad_params']['ti']['value'] ?? '' ),

            // 광고 소스 감지 결과
            'detected_ad_source' => sanitize_text_field( $ad_source['source'] ?? '' ),
            'detected_ad_platform' => sanitize_text_field( $ad_source['platform'] ?? '' ),
            'detected_ad_type' => sanitize_text_field( $ad_source['type'] ?? '' ),
            'ad_source_details' => wp_json_encode( $ad_source['details'] ?? array() ),

            // 리퍼러 정보
            'referrer_url' => esc_url_raw( $data['referrer_url'] ?? '' ),
            'referrer_domain' => sanitize_text_field( $data['referrer_domain'] ?? '' ),
            'referrer_type' => sanitize_text_field( $data['referrer_type'] ?? '' ),
            'referrer_source' => sanitize_text_field( $data['referrer_source'] ?? '' ),
            'referrer_name' => sanitize_text_field( $data['referrer_name'] ?? '' ),

            // 랜딩 페이지
            'landing_page' => esc_url_raw( $data['landing_page'] ?? '' ),
            'landing_path' => sanitize_text_field( $data['landing_path'] ?? '' ),

            // 디바이스/환경 정보
            'screen_resolution' => sanitize_text_field( $data['screen_resolution'] ?? '' ),
            'viewport' => sanitize_text_field( $data['viewport'] ?? '' ),
            'user_language' => sanitize_text_field( $data['language'] ?? '' ),
            'user_agent' => sanitize_text_field( $data['user_agent'] ?? '' ),

            'last_touch_at' => current_time( 'mysql' ),
        );

        if ( $existing ) {
            // 업데이트
            $insert_data['touch_count'] = $existing->touch_count + 1;
            $wpdb->update( $table, $insert_data, array( 'id' => $existing->id ) );
        } else {
            // 새 레코드
            $insert_data['first_touch_at'] = current_time( 'mysql' );
            $insert_data['touch_count'] = 1;
            $wpdb->insert( $table, $insert_data );
        }

        wp_send_json_success();
    }

    /**
     * [v22.7.0] 체류 시간 추적 AJAX
     */
    public function ajax_track_dwell_time() {
        $data = isset( $_POST['data'] ) ? $_POST['data'] : array();

        if ( is_string( $data ) ) {
            $data = json_decode( stripslashes( $data ), true );
        }

        if ( empty( $data ) ) {
            wp_send_json_error();
            return;
        }

        // 네이티브 애널리틱스에 기록
        if ( class_exists( 'ACF_Nudge_Flow_Native_Analytics' ) ) {
            $analytics = ACF_Nudge_Flow_Native_Analytics::instance();
            $analytics->record_event( array(
                'event_type' => 'dwell_time',
                'event_data' => wp_json_encode( $data ),
                'page_url' => sanitize_text_field( $data['page_url'] ?? '' ),
            ) );
        }

        wp_send_json_success();
    }

    /**
     * [v22.7.0] 퍼널 단계 기록 AJAX
     */
    public function ajax_record_funnel_step() {
        $page_type = isset( $_POST['page_type'] ) ? sanitize_text_field( $_POST['page_type'] ) : '';
        $page_url = isset( $_POST['page_url'] ) ? esc_url_raw( $_POST['page_url'] ) : '';
        $page_path = isset( $_POST['page_path'] ) ? sanitize_text_field( $_POST['page_path'] ) : '';
        $referrer = isset( $_POST['referrer'] ) ? esc_url_raw( $_POST['referrer'] ) : '';

        if ( empty( $page_type ) ) {
            wp_send_json_error();
            return;
        }

        global $wpdb;
        $funnels_table = $wpdb->prefix . 'acf_nf_funnels';
        $progress_table = $wpdb->prefix . 'acf_nf_funnel_progress';

        // 활성 퍼널 조회
        $active_funnels = $wpdb->get_results( "SELECT * FROM $funnels_table WHERE is_active = 1" );

        if ( empty( $active_funnels ) ) {
            wp_send_json_success();
            return;
        }

        // 방문자 ID
        $visitor_id = isset( $_COOKIE['acf_nf_visitor_id'] ) ? sanitize_text_field( $_COOKIE['acf_nf_visitor_id'] ) : '';
        $user_id = get_current_user_id();

        foreach ( $active_funnels as $funnel ) {
            $steps = json_decode( $funnel->steps, true );
            if ( ! is_array( $steps ) ) continue;

            // 현재 페이지가 어떤 단계에 해당하는지 확인
            foreach ( $steps as $step_index => $step ) {
                $matches = false;

                switch ( $step['condition'] ) {
                    case 'is_front_page':
                        $matches = ( $page_type === 'home' );
                        break;
                    case 'is_shop':
                        $matches = ( $page_type === 'shop' );
                        break;
                    case 'is_product':
                        $matches = ( $page_type === 'product' );
                        break;
                    case 'is_cart':
                        $matches = ( $page_type === 'cart' );
                        break;
                    case 'is_checkout':
                        $matches = ( $page_type === 'checkout' );
                        break;
                    case 'landing_page':
                        $matches = empty( $referrer ) || strpos( $referrer, home_url() ) === false;
                        break;
                }

                if ( $matches && $step['type'] === 'page_view' ) {
                    $this->update_funnel_progress( $funnel->id, $visitor_id, $user_id, $step_index );
                    break;
                }
            }
        }

        wp_send_json_success();
    }

    /**
     * [v22.7.0] 퍼널 이벤트 기록 AJAX
     */
    public function ajax_record_funnel_event() {
        $event_name = isset( $_POST['event_name'] ) ? sanitize_text_field( $_POST['event_name'] ) : '';
        $event_data = isset( $_POST['event_data'] ) ? $_POST['event_data'] : '';

        if ( empty( $event_name ) ) {
            wp_send_json_error();
            return;
        }

        if ( is_string( $event_data ) ) {
            $event_data = json_decode( stripslashes( $event_data ), true );
        }

        global $wpdb;
        $funnels_table = $wpdb->prefix . 'acf_nf_funnels';

        // 이벤트와 매칭되는 퍼널 단계 찾기
        $active_funnels = $wpdb->get_results( "SELECT * FROM $funnels_table WHERE is_active = 1" );
        $visitor_id = isset( $_COOKIE['acf_nf_visitor_id'] ) ? sanitize_text_field( $_COOKIE['acf_nf_visitor_id'] ) : '';
        $user_id = get_current_user_id();

        foreach ( $active_funnels as $funnel ) {
            $steps = json_decode( $funnel->steps, true );
            if ( ! is_array( $steps ) ) continue;

            foreach ( $steps as $step_index => $step ) {
                if ( $step['type'] === 'event' && $step['condition'] === $event_name ) {
                    $this->update_funnel_progress( $funnel->id, $visitor_id, $user_id, $step_index );

                    // 전환 목표 달성 확인
                    if ( $funnel->conversion_goal === $event_name ) {
                        $this->mark_funnel_completed( $funnel->id, $visitor_id );
                    }
                    break;
                }
            }
        }

        wp_send_json_success();
    }

    /**
     * [v22.7.0] 퍼널 진행 상황 업데이트
     */
    private function update_funnel_progress( $funnel_id, $visitor_id, $user_id, $step_index ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_funnel_progress';

        // 기존 진행 상황 조회
        $existing = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE funnel_id = %d AND visitor_id = %s AND is_completed = 0 ORDER BY id DESC LIMIT 1",
            $funnel_id,
            $visitor_id
        ) );

        $current_time = current_time( 'mysql' );

        if ( $existing ) {
            // 기존 진행 업데이트
            $steps_completed = json_decode( $existing->steps_completed, true ) ?: array();
            $step_timestamps = json_decode( $existing->step_timestamps, true ) ?: array();

            if ( ! in_array( $step_index, $steps_completed ) ) {
                $steps_completed[] = $step_index;
                $step_timestamps[ $step_index ] = time();
            }

            $wpdb->update(
                $table,
                array(
                    'current_step' => max( $step_index, $existing->current_step ),
                    'steps_completed' => wp_json_encode( $steps_completed ),
                    'step_timestamps' => wp_json_encode( $step_timestamps ),
                    'last_activity_at' => $current_time,
                ),
                array( 'id' => $existing->id )
            );
        } else {
            // 새 진행 시작
            $wpdb->insert( $table, array(
                'funnel_id' => $funnel_id,
                'visitor_id' => $visitor_id,
                'user_id' => $user_id,
                'current_step' => $step_index,
                'steps_completed' => wp_json_encode( array( $step_index ) ),
                'step_timestamps' => wp_json_encode( array( $step_index => time() ) ),
                'started_at' => $current_time,
                'last_activity_at' => $current_time,
            ) );
        }
    }

    /**
     * [v22.7.0] 퍼널 완료 처리
     */
    private function mark_funnel_completed( $funnel_id, $visitor_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_funnel_progress';

        $progress = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE funnel_id = %d AND visitor_id = %s AND is_completed = 0 ORDER BY id DESC LIMIT 1",
            $funnel_id,
            $visitor_id
        ) );

        if ( $progress ) {
            $started = strtotime( $progress->started_at );
            $now = time();
            $time_in_funnel = $now - $started;

            $wpdb->update(
                $table,
                array(
                    'is_completed' => 1,
                    'completed_at' => current_time( 'mysql' ),
                    'time_in_funnel' => $time_in_funnel,
                ),
                array( 'id' => $progress->id )
            );
        }
    }

    /**
     * 플러그인 활성화
     */
    public function activate() {
        // 데이터베이스 테이블 생성
        $this->create_tables();

        // [v22.5.2] 템플릿 데이터베이스 테이블 생성
        if ( class_exists( 'ACF_Nudge_Flow_Template_Database' ) ) {
            $template_db = ACF_Nudge_Flow_Template_Database::instance();
            $template_db->create_tables();
        }

        // [v22.6.0] 네이티브 애널리틱스 테이블 생성
        if ( class_exists( 'ACF_Nudge_Flow_Native_Analytics' ) ) {
            $native_analytics = ACF_Nudge_Flow_Native_Analytics::instance();
            $native_analytics->create_tables();
        }

        // [v22.7.0] 광고 픽셀 전환 추적 테이블 생성
        if ( class_exists( 'ACF_Nudge_Flow_Ad_Pixel_Manager' ) ) {
            $pixel_manager = ACF_Nudge_Flow_Ad_Pixel_Manager::instance();
            $pixel_manager->create_tables();
        }

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
            'mab_enabled'          => false, // MAB 자동 최적화 비활성화 (기본값)
            'native_analytics_enabled' => true, // [v22.6.0] 네이티브 애널리틱스 활성화
            'analytics_retention_days' => 90, // 데이터 보존 기간 (일)
        );

        if ( ! get_option( 'acf_nudge_flow_settings' ) ) {
            update_option( 'acf_nudge_flow_settings', $defaults );
        }
    }
    
    /**
     * 플러그인 목록 페이지 향상 초기화
     */
    public function init_plugin_list_enhancer() {
        if ( class_exists( 'JJ_Plugin_List_Enhancer' ) ) {
            $enhancer = new JJ_Plugin_List_Enhancer();
            $enhancer->init( array(
                'plugin_file' => __FILE__,
                'plugin_name' => '넛지 플로우',
                'settings_url' => admin_url( 'admin.php?page=acf-nudge-flow-workflows' ),
                'text_domain' => 'acf-nudge-flow',
                'version_constant' => 'ACF_NUDGE_FLOW_VERSION',
                'license_constant' => 'ACF_NUDGE_FLOW_LICENSE_TYPE',
                'upgrade_url' => 'https://3j-labs.com/',
                'docs_url' => admin_url( 'admin.php?page=acf-nudge-flow-templates' ),
                'support_url' => 'https://3j-labs.com/support',
            ) );
        }
    }

    /**
     * [v22.9.1] 트래픽 분석 CSV 내보내기
     */
    public function ajax_export_traffic_csv() {
        // 권한 확인
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
            return;
        }

        // Nonce 확인
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'acf_nf_export_traffic' ) ) {
            wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
            return;
        }

        $period = isset( $_POST['period'] ) ? sanitize_text_field( $_POST['period'] ) : '7days';

        // 기간 설정
        $date_ranges = array(
            'today' => array(
                'start' => date( 'Y-m-d 00:00:00' ),
                'end' => date( 'Y-m-d 23:59:59' ),
            ),
            '7days' => array(
                'start' => date( 'Y-m-d 00:00:00', strtotime( '-7 days' ) ),
                'end' => date( 'Y-m-d 23:59:59' ),
            ),
            '30days' => array(
                'start' => date( 'Y-m-d 00:00:00', strtotime( '-30 days' ) ),
                'end' => date( 'Y-m-d 23:59:59' ),
            ),
            '90days' => array(
                'start' => date( 'Y-m-d 00:00:00', strtotime( '-90 days' ) ),
                'end' => date( 'Y-m-d 23:59:59' ),
            ),
        );

        $date_range = isset( $date_ranges[ $period ] ) ? $date_ranges[ $period ] : $date_ranges['7days'];

        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        // 데이터 조회
        $data = $wpdb->get_results( $wpdb->prepare(
            "SELECT
                first_touch_at,
                visitor_id,
                referrer_type,
                referrer_source,
                referrer_name,
                utm_source,
                utm_medium,
                utm_campaign,
                utm_content,
                utm_term,
                detected_ad_source,
                detected_ad_platform,
                detected_ad_type,
                gclid,
                fbclid,
                n_media,
                n_keyword,
                kakao_campaign,
                device_type,
                landing_page,
                converted,
                conversion_value
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
            ORDER BY first_touch_at DESC",
            $date_range['start'],
            $date_range['end']
        ), ARRAY_A );

        if ( empty( $data ) ) {
            wp_send_json_error( array( 'message' => 'No data available' ) );
            return;
        }

        // CSV 생성
        $csv_lines = array();

        // 헤더
        $headers = array(
            '일시',
            '방문자ID',
            '트래픽유형',
            '소스',
            '소스명',
            'UTM소스',
            'UTM매체',
            'UTM캠페인',
            'UTM콘텐츠',
            'UTM키워드',
            '광고소스',
            '광고플랫폼',
            '광고유형',
            'GCLID',
            'FBCLID',
            '네이버매체',
            '네이버키워드',
            '카카오캠페인',
            '기기유형',
            '랜딩페이지',
            '전환여부',
            '전환금액'
        );
        $csv_lines[] = implode( ',', array_map( array( $this, 'csv_escape' ), $headers ) );

        // 데이터 행
        foreach ( $data as $row ) {
            $csv_lines[] = implode( ',', array_map( array( $this, 'csv_escape' ), array_values( $row ) ) );
        }

        $csv = implode( "\n", $csv_lines );

        wp_send_json_success( array( 'csv' => $csv ) );
    }

    /**
     * CSV 필드 이스케이프
     */
    private function csv_escape( $value ) {
        $value = str_replace( '"', '""', $value ?? '' );
        if ( strpos( $value, ',' ) !== false || strpos( $value, '"' ) !== false || strpos( $value, "\n" ) !== false ) {
            $value = '"' . $value . '"';
        }
        return $value;
    }
} // End of class ACF_Nudge_Flow

} // End of class_exists check

/**
 * 플러그인 인스턴스 반환
 */
if ( ! function_exists( 'acf_nudge_flow' ) ) {
    function acf_nudge_flow() {
        if ( class_exists( 'ACF_Nudge_Flow' ) ) {
            return ACF_Nudge_Flow::get_instance();
        }
        return null;
    }
}

// 플러그인 시작
if ( class_exists( 'ACF_Nudge_Flow' ) ) {
    acf_nudge_flow();
}

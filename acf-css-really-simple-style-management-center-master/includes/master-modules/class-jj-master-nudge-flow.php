<?php
/**
 * JJ Master Nudge Flow - 마스터 버전 통합 마케팅 모듈
 * 
 * ACF MBA (Advanced Custom Funnel Marketing Boosting Accelerator)의 
 * 핵심 기능을 마스터 버전에 통합합니다.
 * 
 * @since 13.4.4
 * @package ACF_CSS
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Master_Nudge_Flow {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    private function init() {
        // 워크플로우 포스트 타입
        add_action( 'init', array( $this, 'register_post_type' ) );
        
        // 관리자 메뉴
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );
        
        // 프론트엔드 넛지 실행
        add_action( 'wp_footer', array( $this, 'execute_nudges' ) );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_nudge_dismiss', array( $this, 'ajax_dismiss_nudge' ) );
        add_action( 'wp_ajax_nopriv_jj_nudge_dismiss', array( $this, 'ajax_dismiss_nudge' ) );
        
        // 스크립트 로드
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /**
     * 워크플로우 포스트 타입 등록
     */
    public function register_post_type() {
        $labels = array(
            'name' => __( '넛지 워크플로우', 'acf-css-really-simple-style-management-center' ),
            'singular_name' => __( '넛지 워크플로우', 'acf-css-really-simple-style-management-center' ),
            'add_new' => __( '새 워크플로우', 'acf-css-really-simple-style-management-center' ),
            'add_new_item' => __( '새 넛지 워크플로우 추가', 'acf-css-really-simple-style-management-center' ),
            'edit_item' => __( '워크플로우 편집', 'acf-css-really-simple-style-management-center' ),
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'supports' => array( 'title' ),
            'menu_icon' => 'dashicons-megaphone',
        );

        register_post_type( 'jj_nudge_workflow', $args );
    }

    /**
     * 관리자 메뉴 추가
     */
    public function add_admin_menu() {
        add_submenu_page(
            'jj-admin-center',
            __( '마케팅 자동화', 'acf-css-really-simple-style-management-center' ),
            __( '📣 마케팅 넛지', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            'jj-nudge-flow',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * 관리자 페이지 렌더링
     */
    public function render_admin_page() {
        ?>
        <div class="wrap jj-nudge-flow-wrap">
            <h1><?php esc_html_e( 'ACF MBA - 마케팅 넛지 자동화', 'acf-css-really-simple-style-management-center' ); ?></h1>
            
            <div class="notice notice-info">
                <p><?php esc_html_e( 'IF-DO 방식의 시각적 워크플로우 빌더로 방문자 행동에 따른 팝업, 알림, 프로모션을 자동화합니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>

            <div class="jj-nudge-dashboard" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px;">
                <!-- 워크플로우 목록 -->
                <div class="jj-nudge-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3><?php esc_html_e( '📋 활성 워크플로우', 'acf-css-really-simple-style-management-center' ); ?></h3>
                    <?php $this->render_workflow_list(); ?>
                </div>

                <!-- 트리거 유형 -->
                <div class="jj-nudge-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3><?php esc_html_e( '⚡ 트리거 유형', 'acf-css-really-simple-style-management-center' ); ?></h3>
                    <ul style="margin: 0; padding-left: 20px;">
                        <li><?php esc_html_e( '방문자 유형 (신규/재방문/로그인)', 'acf-css-really-simple-style-management-center' ); ?></li>
                        <li><?php esc_html_e( '유입 소스 (광고/오가닉/UTM)', 'acf-css-really-simple-style-management-center' ); ?></li>
                        <li><?php esc_html_e( '페이지/상품 조회', 'acf-css-really-simple-style-management-center' ); ?></li>
                        <li><?php esc_html_e( '장바구니 금액', 'acf-css-really-simple-style-management-center' ); ?></li>
                        <li><?php esc_html_e( '이탈 의도 감지', 'acf-css-really-simple-style-management-center' ); ?></li>
                        <li><?php esc_html_e( '스크롤 위치', 'acf-css-really-simple-style-management-center' ); ?></li>
                    </ul>
                </div>

                <!-- 액션 유형 -->
                <div class="jj-nudge-card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3><?php esc_html_e( '🎯 액션 유형', 'acf-css-really-simple-style-management-center' ); ?></h3>
                    <ul style="margin: 0; padding-left: 20px;">
                        <li><?php esc_html_e( '팝업/모달 표시', 'acf-css-really-simple-style-management-center' ); ?></li>
                        <li><?php esc_html_e( '상단/하단 바 표시', 'acf-css-really-simple-style-management-center' ); ?></li>
                        <li><?php esc_html_e( '할인 쿠폰 제안', 'acf-css-really-simple-style-management-center' ); ?></li>
                        <li><?php esc_html_e( '관련 상품 추천', 'acf-css-really-simple-style-management-center' ); ?></li>
                        <li><?php esc_html_e( '뉴스레터 구독 양식', 'acf-css-really-simple-style-management-center' ); ?></li>
                        <li><?php esc_html_e( '페이지 리다이렉트', 'acf-css-really-simple-style-management-center' ); ?></li>
                    </ul>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=jj_nudge_workflow' ) ); ?>" class="button button-primary button-hero">
                    <?php esc_html_e( '➕ 새 워크플로우 만들기', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
            </div>
        </div>
        <?php
    }

    /**
     * 워크플로우 목록 렌더링
     */
    private function render_workflow_list() {
        $workflows = get_posts( array(
            'post_type' => 'jj_nudge_workflow',
            'post_status' => 'publish',
            'posts_per_page' => 10,
        ) );

        if ( empty( $workflows ) ) {
            echo '<p style="color: #999;">' . esc_html__( '아직 워크플로우가 없습니다.', 'acf-css-really-simple-style-management-center' ) . '</p>';
            return;
        }

        echo '<ul style="margin: 0; padding: 0; list-style: none;">';
        foreach ( $workflows as $workflow ) {
            $status = get_post_meta( $workflow->ID, '_jj_workflow_active', true );
            $status_icon = $status ? '🟢' : '🔴';
            echo sprintf(
                '<li style="padding: 8px 0; border-bottom: 1px solid #eee;">%s <a href="%s">%s</a></li>',
                $status_icon,
                esc_url( get_edit_post_link( $workflow->ID ) ),
                esc_html( $workflow->post_title )
            );
        }
        echo '</ul>';
    }

    /**
     * 프론트엔드 스크립트 로드
     */
    public function enqueue_scripts() {
        if ( is_admin() ) {
            return;
        }

        wp_enqueue_script(
            'jj-nudge-flow',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-nudge-flow.js',
            array( 'jquery' ),
            JJ_STYLE_GUIDE_VERSION,
            true
        );

        wp_localize_script( 'jj-nudge-flow', 'jjNudgeFlow', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'jj_nudge_nonce' ),
            'workflows' => $this->get_active_workflows(),
        ) );
    }

    /**
     * 활성 워크플로우 가져오기
     */
    private function get_active_workflows() {
        $workflows = get_posts( array(
            'post_type' => 'jj_nudge_workflow',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_jj_workflow_active',
                    'value' => '1',
                ),
            ),
        ) );

        $result = array();
        foreach ( $workflows as $workflow ) {
            $result[] = array(
                'id' => $workflow->ID,
                'trigger' => get_post_meta( $workflow->ID, '_jj_workflow_trigger', true ),
                'action' => get_post_meta( $workflow->ID, '_jj_workflow_action', true ),
                'config' => get_post_meta( $workflow->ID, '_jj_workflow_config', true ),
            );
        }

        return $result;
    }

    /**
     * 프론트엔드 넛지 실행
     */
    public function execute_nudges() {
        // JavaScript 기반으로 실행됨
    }

    /**
     * AJAX: 넛지 닫기
     */
    public function ajax_dismiss_nudge() {
        check_ajax_referer( 'jj_nudge_nonce', 'nonce' );

        $workflow_id = isset( $_POST['workflow_id'] ) ? intval( $_POST['workflow_id'] ) : 0;
        
        if ( $workflow_id > 0 ) {
            // 세션 또는 쿠키에 닫은 워크플로우 기록
            $dismissed = isset( $_COOKIE['jj_nudge_dismissed'] ) ? json_decode( stripslashes( $_COOKIE['jj_nudge_dismissed'] ), true ) : array();
            $dismissed[] = $workflow_id;
            setcookie( 'jj_nudge_dismissed', json_encode( array_unique( $dismissed ) ), time() + ( 24 * 60 * 60 ), '/' );
        }

        wp_send_json_success();
    }
}

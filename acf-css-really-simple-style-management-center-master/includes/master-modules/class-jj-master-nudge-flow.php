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
        
        // 메뉴 순서 강제 조정 (WooCommerce 마케팅 아래)
        add_filter( 'menu_order', array( $this, 'force_menu_order' ), 1001 );
        add_filter( 'custom_menu_order', '__return_true' );

        // 프론트엔드 넛지 실행
        add_action( 'wp_footer', array( $this, 'execute_nudges' ) );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_nudge_dismiss', array( $this, 'ajax_dismiss_nudge' ) );
        add_action( 'wp_ajax_nopriv_jj_nudge_dismiss', array( $this, 'ajax_dismiss_nudge' ) );
        add_action( 'wp_ajax_jj_install_nudge_preset', array( $this, 'ajax_install_nudge_preset' ) );
        
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
     * [v20.2.2] WooCommerce '마케팅' 메뉴 아래로 배치 및 서브메뉴 구조화
     */
    public function add_admin_menu() {
        $parent_slug = 'woocommerce-marketing'; // WooCommerce 마케팅 메뉴 슬러그
        $capability  = 'manage_options';

        // 1. 최상위 메뉴 (마케팅 메뉴 하위로 리다이렉트되도록 하거나 독자 노출)
        // 사장님 요청에 따라 '마케팅' 메뉴 바로 아래에 배치
        add_menu_page(
            __( '넛지 플로우', 'acf-css-really-simple-style-management-center' ),
            __( '🚀 넛지 플로우', 'acf-css-really-simple-style-management-center' ),
            $capability,
            'jj-nudge-flow',
            array( $this, 'render_dashboard_page' ), // 첫 페이지는 대시보드
            'dashicons-megaphone',
            58 // WooCommerce 마케팅(58) 인근 배치
        );

        // 2. 서브메뉴 구성 (순서 중요)
        
        // (1) 대시보드 (최상단)
        add_submenu_page(
            'jj-nudge-flow',
            __( '대시보드', 'acf-css-really-simple-style-management-center' ),
            __( '📊 대시보드', 'acf-css-really-simple-style-management-center' ),
            $capability,
            'jj-nudge-flow', // 상위 슬러그와 동일하게 하여 기본 페이지로 설정
            array( $this, 'render_dashboard_page' )
        );

        // (2) 워크플로우 (모든 넛지 흐름 관리)
        add_submenu_page(
            'jj-nudge-flow',
            __( '워크플로우', 'acf-css-really-simple-style-management-center' ),
            __( '🔄 워크플로우', 'acf-css-really-simple-style-management-center' ),
            $capability,
            'edit.php?post_type=jj_nudge_workflow',
            null // 기본 포스트 목록 페이지 사용
        );

        // (3) 분석 (데이터 통계)
        add_submenu_page(
            'jj-nudge-flow',
            __( '분석', 'acf-css-really-simple-style-management-center' ),
            __( '📈 분석 통계', 'acf-css-really-simple-style-management-center' ),
            $capability,
            'jj-nudge-analytics',
            array( $this, 'render_analytics_page' )
        );

        // (4) 템플릿 센터 (불러오기/내보내기 및 공유 마켓)
        add_submenu_page(
            'jj-nudge-flow',
            __( '템플릿 센터', 'acf-css-really-simple-style-management-center' ),
            __( '🎁 템플릿 센터', 'acf-css-really-simple-style-management-center' ),
            $capability,
            'jj-nudge-templates',
            array( $this, 'render_template_center_page' )
        );
        
        // (5) 설정
        add_submenu_page(
            'jj-nudge-flow',
            __( '설정', 'acf-css-really-simple-style-management-center' ),
            __( '⚙️ 설정', 'acf-css-really-simple-style-management-center' ),
            $capability,
            'jj-nudge-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * 대시보드 페이지 렌더링
     */
    public function render_dashboard_page() {
        $this->render_admin_page(); // 기존 렌더링 함수 활용
    }

    /**
     * 분석 페이지 렌더링
     */
    public function render_analytics_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( '📈 넛지 분석 통계', 'acf-css-really-simple-style-management-center' ); ?></h1>
            <p><?php esc_html_e( '각 넛지별 노출수, 클릭수, 전환율을 분석합니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
            <div class="notice notice-info"><p><?php esc_html_e( '데이터 수집 중입니다...', 'acf-css-really-simple-style-management-center' ); ?></p></div>
        </div>
        <?php
    }

    /**
     * 템플릿 센터 페이지 렌더링
     * [v20.2.2] 유료/무료 공유 템플릿 생태계 구축
     */
    public function render_template_center_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( '🎁 템플릿 센터', 'acf-css-really-simple-style-management-center' ); ?></h1>
            <p><?php esc_html_e( '검증된 넛지 템플릿을 불러오거나, 직접 만든 템플릿을 공유하여 수익을 창출하세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
            
            <h2 class="nav-tab-wrapper">
                <a href="#free" class="nav-tab nav-tab-active"><?php esc_html_e( '무료 템플릿', 'acf-css-really-simple-style-management-center' ); ?></a>
                <a href="#premium" class="nav-tab"><?php esc_html_e( '유료 프리미엄', 'acf-css-really-simple-style-management-center' ); ?></a>
                <a href="#my-shared" class="nav-tab"><?php esc_html_e( '내 공유 현황', 'acf-css-really-simple-style-management-center' ); ?></a>
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
                <!-- 템플릿 카드 예시 -->
                <div class="postbox" style="padding: 15px;">
                    <h3><?php esc_html_e( '장바구니 리마인더 (기본)', 'acf-css-really-simple-style-management-center' ); ?></h3>
                    <p><?php esc_html_e( '장바구니에 상품을 담고 결제하지 않은 고객에게 쿠폰을 제안합니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="badge" style="background: #eee; padding: 2px 8px; border-radius: 4px;"><?php esc_html_e( '무료', 'acf-css-really-simple-style-management-center' ); ?></span>
                        <button class="button button-primary"><?php esc_html_e( '가져오기', 'acf-css-really-simple-style-management-center' ); ?></button>
                    </div>
                </div>

                <div class="postbox" style="padding: 15px; border-left: 4px solid #f59e0b;">
                    <h3 style="color: #d97706;"><?php esc_html_e( '⚡ 초고속 완판 전략 세트', 'acf-css-really-simple-style-management-center' ); ?> <span class="dashicons dashicons-star-filled"></span></h3>
                    <p><?php esc_html_e( '타임 세일과 이탈 방지 넛지가 결합된 고효율 패키지입니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: bold; color: #10b981;">₩19,900</span>
                        <button class="button button-secondary"><?php esc_html_e( '구매하기', 'acf-css-really-simple-style-management-center' ); ?></button>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 40px; padding: 20px; background: #f0f0f1; border-radius: 8px;">
                <h3><?php esc_html_e( '💰 템플릿 판매자 되기', 'acf-css-really-simple-style-management-center' ); ?></h3>
                <p><?php esc_html_e( '자신만의 독창적인 넛지 시나리오를 3J Labs 마켓플레이스에 등록하세요. 판매 수익의 70%를 정산해 드립니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                <button class="button button-large"><?php esc_html_e( '판매자 등록 신청', 'acf-css-really-simple-style-management-center' ); ?></button>
            </div>
        </div>
        <?php
    }

    /**
     * 설정 페이지 렌더링
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( '⚙️ 넛지 플로우 설정', 'acf-css-really-simple-style-management-center' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'jj_nudge_settings' );
                do_settings_sections( 'jj-nudge-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
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
     * AJAX: 프리셋 템플릿 설치
     * [v20.2.4] 설치 시 비활성화(draft) 상태로 생성
     */
    public function ajax_install_nudge_preset() {
        check_ajax_referer( 'jj_nudge_market_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $preset_id = isset( $_POST['preset_id'] ) ? sanitize_text_field( $_POST['preset_id'] ) : '';
        $presets = $this->get_preset_templates();

        if ( ! isset( $presets[ $preset_id ] ) ) {
            wp_send_json_error( __( '유효하지 않은 프리셋입니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $data = $presets[ $preset_id ];

        // 새로운 워크플로우 생성
        $post_id = wp_insert_post( array(
            'post_title'   => $data['title'] . ' (Preset)',
            'post_type'    => 'jj_nudge_workflow',
            'post_status'  => 'draft', // 초기 비활성화 상태
            'post_content' => $data['description'],
        ) );

        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( $post_id->get_error_message() );
        }

        // 메타 데이터 저장
        update_post_meta( $post_id, '_jj_workflow_active', '0' );
        update_post_meta( $post_id, '_jj_workflow_trigger', $data['trigger'] );
        update_post_meta( $post_id, '_jj_workflow_action', $data['action'] );
        update_post_meta( $post_id, '_jj_workflow_preset_id', $preset_id );
        
        // 프리셋별 기본 설정값 (예시)
        $default_config = array(
            'delay' => 5,
            'frequency' => 'once_per_session',
            'theme' => 'modern',
        );
        update_post_meta( $post_id, '_jj_workflow_config', $default_config );

        wp_send_json_success( array( 'post_id' => $post_id ) );
    }

    /**
     * 메뉴 순서 강제 조정
     */
    public function force_menu_order( $menu_ord ) {
        if ( ! $menu_ord ) return $menu_ord;

        $new_order = array();
        $target_menu = 'jj-nudge-flow';
        $marketing_menu = 'woocommerce-marketing';

        foreach ( $menu_ord as $item ) {
            $new_order[] = $item;
            if ( $item === $marketing_menu ) {
                // 마케팅 메뉴 바로 다음에 넛지 플로우 배치
                if ( ( $key = array_search( $target_menu, $menu_ord ) ) !== false ) {
                    unset( $new_order[ array_search( $target_menu, $new_order ) ] );
                    $new_order[] = $target_menu;
                }
            }
        }

        return array_values( array_unique( $new_order ) );
    }
}

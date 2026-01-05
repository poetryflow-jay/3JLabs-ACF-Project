<?php
/**
 * 관리자 클래스
 *
 * @package ACF_Nudge_Flow
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 관리자 페이지
 * [v22.4.3] 클래스 중복 선언 방지 추가
 */
if ( ! class_exists( 'ACF_Nudge_Flow_Admin' ) ) {
class ACF_Nudge_Flow_Admin {

    /**
     * 생성자
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'wp_ajax_acf_nudge_save_workflow', array( $this, 'ajax_save_workflow' ) );
        add_action( 'wp_ajax_acf_nudge_get_workflow', array( $this, 'ajax_get_workflow' ) );
        add_action( 'wp_ajax_acf_nudge_get_triggers', array( $this, 'ajax_get_triggers' ) );
        add_action( 'wp_ajax_acf_nudge_get_actions', array( $this, 'ajax_get_actions' ) );
        add_action( 'wp_ajax_jj_install_nudge_preset', array( $this, 'ajax_install_nudge_preset' ) );

        // [v22.3.1] Chart.js and dashboard assets
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_dashboard_assets' ) );

        // [v22.4.6] 초기화 시 프리셋 템플릿 생성 (admin_init에서 호출)
        add_action( 'admin_init', array( $this, 'maybe_ensure_preset_templates' ) );

        // 빌더 UI 템플릿 출력
        add_action( 'admin_footer', array( $this, 'output_builder_templates' ) );

        // [v22.9.1] 워드프레스 대시보드 위젯 등록
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );

        // [v22.9.1] 실시간 트래픽 데이터 AJAX
        add_action( 'wp_ajax_acf_nf_realtime_traffic', array( $this, 'ajax_realtime_traffic' ) );
    }
    
    /**
     * [v22.4.6] 프리셋 템플릿 자동 생성 (admin_init 훅)
     */
    public function maybe_ensure_preset_templates() {
        // 넛지 템플릿 페이지에서만 실행
        if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'acf-nudge-templates' ) {
            return;
        }
        
        $this->ensure_preset_templates_internal();
    }
    
    /**
     * [v22.3.1] Enqueue Chart.js and dashboard visualization assets
     * [v25.0.0] v25 디자인 시스템 통합
     */
    public function enqueue_dashboard_assets( $hook ) {
        // Only load on our dashboard pages
        if ( strpos( $hook, 'acf-nudge-flow' ) === false ) {
            return;
        }
        
        // [v25.0.0] v25 디자인 시스템 CSS
        $shared_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/acf-css-really-simple-style-management-center-master';
        if ( file_exists( $shared_path . '/assets/css/jj-design-system-v25.css' ) ) {
            wp_enqueue_style(
                'jj-design-system-v25',
                plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . '../acf-css-really-simple-style-management-center-master/assets/css/jj-design-system-v25.css',
                array(),
                '25.0.0'
            );
        }
        
        // [v25.0.0] Nudge Flow v25 UI CSS
        wp_enqueue_style(
            'acf-nudge-flow-v25-ui',
            ACF_NUDGE_FLOW_PLUGIN_URL . 'assets/css/jj-nudge-flow-v25-ui.css',
            array( 'jj-design-system-v25' ),
            ACF_NUDGE_FLOW_VERSION
        );
        
        // [v25.0.0] 애니메이션 시스템
        if ( file_exists( $shared_path . '/assets/css/jj-animations-v25.css' ) ) {
            wp_enqueue_style(
                'jj-animations-v25',
                plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . '../acf-css-really-simple-style-management-center-master/assets/css/jj-animations-v25.css',
                array( 'jj-design-system-v25' ),
                '25.0.0'
            );
        }
        
        // [v25.0.0] 다크 모드 시스템
        if ( file_exists( $shared_path . '/assets/js/jj-dark-mode-v25.js' ) ) {
            wp_enqueue_script(
                'jj-dark-mode-v25',
                plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . '../acf-css-really-simple-style-management-center-master/assets/js/jj-dark-mode-v25.js',
                array( 'jquery' ),
                '25.0.0',
                true
            );
        }
        
        // Chart.js CDN
        wp_enqueue_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
            array(),
            '4.4.1',
            true
        );
        
        // Dashboard styles
        wp_add_inline_style( 'wp-admin', $this->get_dashboard_inline_styles() );
    }
    
    /**
     * [v22.3.1] Dashboard inline styles - Jenny's gradient card design
     */
    private function get_dashboard_inline_styles() {
        return '
            .acf-nudge-stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 20px;
                margin: 30px 0;
            }
            
            .acf-nudge-stat-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 12px;
                padding: 24px;
                color: #fff;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            
            .acf-nudge-stat-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            }
            
            .acf-nudge-stat-card:nth-child(1) {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            .acf-nudge-stat-card:nth-child(2) {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            }
            
            .acf-nudge-stat-card:nth-child(3) {
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            }
            
            .acf-nudge-stat-card:nth-child(4) {
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            }
            
            .acf-nudge-stat-card .stat-icon {
                font-size: 32px;
                margin-bottom: 12px;
                opacity: 0.9;
            }
            
            .acf-nudge-stat-card .stat-value {
                font-size: 36px;
                font-weight: 700;
                line-height: 1.2;
                margin-bottom: 8px;
            }
            
            .acf-nudge-stat-card .stat-label {
                font-size: 14px;
                opacity: 0.9;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .acf-nudge-chart-container {
                background: #fff;
                border-radius: 12px;
                padding: 24px;
                margin: 30px 0;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }
            
            .acf-nudge-quick-actions {
                margin-top: 40px;
            }
            
            .quick-action-cards {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 16px;
                margin-top: 20px;
            }
            
            .quick-action-card {
                background: #fff;
                border: 2px solid #e5e7eb;
                border-radius: 8px;
                padding: 20px;
                text-align: center;
                text-decoration: none;
                transition: all 0.2s ease;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            
            .quick-action-card:hover {
                border-color: #667eea;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
            }
            
            .quick-action-card .icon {
                font-size: 40px;
                margin-bottom: 12px;
            }
            
            .quick-action-card .title {
                font-size: 16px;
                font-weight: 600;
                color: #111827;
                margin-bottom: 8px;
                display: block;
            }
            
            .quick-action-card .desc {
                font-size: 13px;
                color: #6b7280;
                display: block;
            }
        ';
    }

    /**
     * AJAX: 프리셋 템플릿 설치
     * [v21.0.0] 설치 시 비활성화(draft) 상태로 생성
     */
    public function ajax_install_nudge_preset() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-nudge-flow' ) );
        }

        $preset_id = isset( $_POST['preset_id'] ) ? sanitize_text_field( $_POST['preset_id'] ) : '';
        $presets = $this->get_preset_templates();

        if ( ! isset( $presets[ $preset_id ] ) ) {
            wp_send_json_error( __( '유효하지 않은 프리셋입니다.', 'acf-nudge-flow' ) );
        }

        $data = $presets[ $preset_id ];

        // 새로운 워크플로우 생성
        $post_id = wp_insert_post( array(
            'post_title'   => $data['title'] . ' (Preset)',
            'post_type'    => 'acf_nudge_workflow',
            'post_status'  => 'draft', // 초기 비활성화 상태
            'post_content' => $data['description'],
        ) );

        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( $post_id->get_error_message() );
        }

        // 메타 데이터 저장
        update_post_meta( $post_id, '_acf_nudge_workflow_enabled', '0' );
        update_post_meta( $post_id, '_acf_nudge_workflow_trigger', $data['trigger'] );
        update_post_meta( $post_id, '_acf_nudge_workflow_trigger_settings', $data['trigger_settings'] ?? array() );
        update_post_meta( $post_id, '_acf_nudge_workflow_action', $data['action'] );
        update_post_meta( $post_id, '_acf_nudge_workflow_action_settings', $data['action_settings'] ?? array() );
        update_post_meta( $post_id, '_acf_nudge_workflow_preset_id', $preset_id );
        
        $default_config = array(
            'delay' => 5,
            'frequency' => 'once_per_session',
            'theme' => 'modern',
        );
        update_post_meta( $post_id, '_acf_nudge_workflow_config', $default_config );

        wp_send_json_success( array( 'post_id' => $post_id ) );
    }

    /**
     * 빌더 템플릿 출력 (좌측 패널 트리거/액션 목록 드래그 앤 드롭 지원)
     * [v22.4.6] 빌더 페이지에서만 출력하도록 수정
     */
    public function output_builder_templates() {
        // 빌더 페이지에서만 출력
        if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'acf-nudge-flow-builder' ) {
            return;
        }
        ?>
        <!-- [v22.4.6] 빌더 템플릿은 render_builder()에서 직접 렌더링하므로 여기서는 제거 -->
        <?php
    }

    /**
     * 관리자 메뉴 추가
     * [v22.4.6] 대시보드를 첫 화면으로 설정, 메뉴 순서 수정
     */
    public function add_admin_menu() {
        $parent_slug = 'woocommerce-marketing'; // WooCommerce 마케팅 메뉴 슬러그
        $capability  = 'manage_options';

        // 최상위 메뉴 - 대시보드를 기본 화면으로 설정
        add_menu_page(
            __( '넛지 플로우', 'acf-nudge-flow' ),
            __( '넛지 플로우 🚀', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-flow',
            array( $this, 'render_dashboard' ), // [v22.4.6] 대시보드를 첫 화면으로
            'dashicons-chart-area',
            58 // WooCommerce Marketing (58) 인근
        );

        // (1) 대시보드 - 첫 번째 서브메뉴 (WordPress가 자동으로 메인 메뉴와 같은 슬러그로 처리)
        add_submenu_page(
            'acf-nudge-flow',
            __( '대시보드', 'acf-nudge-flow' ),
            __( '📊 대시보드', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-flow', // [v22.4.6] 메인 메뉴와 동일한 슬러그로 첫 화면 보장
            array( $this, 'render_dashboard' )
        );

        // (2) 워크플로우
        add_submenu_page(
            'acf-nudge-flow',
            __( '워크플로우', 'acf-nudge-flow' ),
            __( '🔄 워크플로우', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-flow-workflows',
            array( $this, 'render_workflows' )
        );

        // (3) 넛지 템플릿 - 프리셋 템플릿 관리
        add_submenu_page(
            'acf-nudge-flow',
            __( '넛지 템플릿', 'acf-nudge-flow' ),
            __( '📋 넛지 템플릿', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-templates',
            array( $this, 'render_nudge_templates' ) // [v22.4.6] 프리셋 템플릿 페이지
        );

        // (4) 분석
        add_submenu_page(
            'acf-nudge-flow',
            __( '분석', 'acf-nudge-flow' ),
            __( '📈 분석 통계', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-flow-analytics',
            array( $this, 'render_analytics' )
        );

        // (5) 템플릿 센터 (전략적 프리셋)
        add_submenu_page(
            'acf-nudge-flow',
            __( '템플릿 센터', 'acf-nudge-flow' ),
            __( '🎁 템플릿 센터', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-template-center',
            array( $this, 'render_template_center' )
        );

        // (6) 광고 픽셀 & 전환 추적
        add_submenu_page(
            'acf-nudge-flow',
            __( '광고 픽셀', 'acf-nudge-flow' ),
            __( '📡 광고 픽셀', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-flow-pixels',
            array( $this, 'render_ad_pixels' )
        );

        // (7) 트래픽 소스 분석 [v22.8.0] → [v22.10.0] User Journey Analytics 연동
        add_submenu_page(
            'acf-nudge-flow',
            __( '트래픽 연동', 'acf-nudge-flow' ),
            __( '📊 트래픽 연동', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-flow-traffic',
            array( $this, 'render_traffic_analytics' )
        );

        // (8) 설정
        add_submenu_page(
            'acf-nudge-flow',
            __( '설정', 'acf-nudge-flow' ),
            __( '⚙️ 설정', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-flow-settings',
            array( $this, 'render_settings' )
        );

        // (8) 빌더 (숨김 메뉴)
        add_submenu_page(
            'acf-nudge-flow',
            __( '워크플로우 빌더', 'acf-nudge-flow' ),
            '', // 메뉴에서 숨김
            $capability,
            'acf-nudge-flow-builder',
            array( $this, 'render_builder' )
        );

        // 메뉴 순서 강제 조정 (WooCommerce 마케팅 아래)
        add_filter( 'custom_menu_order', '__return_true' );
        add_filter( 'menu_order', array( $this, 'force_menu_order' ), 1001 );
    }

    /**
     * 메뉴 순서 강제 조정
     */
    public function force_menu_order( $menu_ord ) {
        if ( ! $menu_ord ) return $menu_ord;

        $new_order = array();
        $target_menu = 'acf-nudge-flow';
        $marketing_menu = 'woocommerce-marketing';

        foreach ( $menu_ord as $item ) {
            $new_order[] = $item;
            if ( $item === $marketing_menu ) {
                if ( ( $key = array_search( $target_menu, $menu_ord ) ) !== false ) {
                    unset( $new_order[ array_search( $target_menu, $new_order ) ] );
                    $new_order[] = $target_menu;
                }
            }
        }

        return array_values( array_unique( $new_order ) );
    }

    /**
     * 개인화 마케팅 보고서 기반 프리셋 데이터
     * [v22.0.1] 실제 작동 가능한 트리거/액션 설정 주입
     * [v22.5.0] 고객 템플릿과 넛지 템플릿 분리
     */
    public function get_preset_templates( $type = 'all' ) {
        // 고객 템플릿 (Customer Templates) - 고객 대상 메시지
        $customer_templates = array(
            'welcome_curation' => array(
                'title'       => __( '첫 방문자 환영 & 큐레이션', 'acf-nudge-flow' ),
                'description' => __( '방문 초기 이탈을 방지하기 위해 브랜드 베스트셀러와 현재 진행 중인 기획전으로 안내합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Visit',
                'icon'        => 'dashicons-welcome-widgets-menus',
                'trigger'     => 'first_visit',
                'trigger_settings' => array(),
                'action'      => 'popup_center',
                'action_settings' => array(
                    'title' => __( '반갑습니다! 👋', 'acf-nudge-flow' ),
                    'content' => __( '저희 브랜드를 처음 방문해 주셨군요. 지금 가장 인기 있는 상품들을 만나보세요.', 'acf-nudge-flow' ),
                    'cta_text' => __( '베스트셀러 보기', 'acf-nudge-flow' ),
                    'cta_url' => home_url( '/shop' ),
                    'style' => 'default'
                ),
            ),
            'signup_nudge' => array(
                'title'       => __( '회원 가입 유도 혜택 알림', 'acf-nudge-flow' ),
                'description' => __( '페이지를 2개 이상 조회한 관심 고객에게 회원 가입 시 즉시 사용 가능한 혜택을 노출합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Product View',
                'icon'        => 'dashicons-id',
                'trigger'     => 'visit_count',
                'trigger_settings' => array( 'operator' => '>=', 'count' => 2 ),
                'action'      => 'popup_slide_in',
                'action_settings' => array(
                    'position' => 'bottom-right',
                    'title' => __( '잠시만요! 🎁', 'acf-nudge-flow' ),
                    'content' => __( '지금 회원 가입하시면 첫 구매 10% 할인 쿠폰을 즉시 드립니다.', 'acf-nudge-flow' ),
                    'cta_text' => __( '1분만에 가입하기', 'acf-nudge-flow' ),
                    'cta_url' => wp_registration_url()
                ),
            ),
            'cart_recovery' => array(
                'title'       => __( '장바구니 이탈 방지 & 리뷰 넛지', 'acf-nudge-flow' ),
                'description' => __( '장바구니에 담고 결제 없이 나가려는 고객에게 실제 구매 고객의 생생한 리뷰를 보여주며 설득합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Cart',
                'icon'        => 'dashicons-cart',
                'trigger'     => 'exit_intent',
                'trigger_settings' => array(),
                'action'      => 'toast',
                'action_settings' => array(
                    'message' => __( '🛒 장바구니에 담긴 상품이 곧 품절될 수 있어요! (누적 리뷰 4.9/5)', 'acf-nudge-flow' ),
                    'type' => 'promo',
                    'position' => 'bottom-left',
                    'duration' => 8
                ),
            ),
            'free_shipping' => array(
                'title'       => __( '무료 배송 임계치 달성 유도', 'acf-nudge-flow' ),
                'description' => __( '장바구니 금액이 무료 배송 기준 미만일 때, 추가 구매 시 배송비가 무료임을 알려 객단가를 높입니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'AOV Boost',
                'icon'        => 'dashicons-truck',
                'price'       => '₩19,000 / $14 / €13',
                'trigger'     => 'cart_value',
                'trigger_settings' => array( 'operator' => '<', 'amount' => 50000 ),
                'action'      => 'free_shipping_bar',
                'action_settings' => array(
                    'threshold' => 50000,
                    'message_before' => __( '🚚 {{remaining}}원 더 담으면 무료배송 혜택을 받으실 수 있어요!', 'acf-nudge-flow' ),
                    'message_after' => __( '🎉 축하합니다! 무료배송 혜택이 적용되었습니다.', 'acf-nudge-flow' )
                ),
            ),
            'cross_sell' => array(
                'title'       => __( '관련 상품 스마트 교차 판매', 'acf-nudge-flow' ),
                'description' => __( '특정 카테고리 상품을 담은 고객에게 함께 사면 좋은 연관 상품을 스마트하게 추천합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'Cross-sell',
                'icon'        => 'dashicons-plus-alt',
                'price'       => '₩25,000 / $18 / €17',
                'trigger'     => 'cart_value', // 임시: 카테고리 트리거 미구현 시 대안
                'trigger_settings' => array( 'operator' => '>=', 'amount' => 10000 ),
                'action'      => 'crosssell_popup',
                'action_settings' => array(
                    'title' => __( '이 상품과 같이 많이 구매해요 🤝', 'acf-nudge-flow' ),
                    'discount' => 5
                ),
            ),
            'vip_retention' => array(
                'title'       => __( 'VIP 고객 자동 리텐션 팩', 'acf-nudge-flow' ),
                'description' => __( '누적 구매 금액이 높은 VIP 고객이 방문했을 때만 특별한 비밀 혜택을 제공하여 충성도를 강화합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'Retention',
                'icon'        => 'dashicons-star-filled',
                'price'       => '₩29,000 / $21 / €19',
                'trigger'     => 'total_spent',
                'trigger_settings' => array( 'operator' => '>=', 'amount' => 500000 ),
                'action'      => 'discount_reveal',
                'action_settings' => array(
                    'title' => __( 'VIP 고객님을 위한 비밀 쿠폰 💎', 'acf-nudge-flow' ),
                    'coupon_code' => 'THANKSVIP20',
                    'description' => __( '항상 저희를 믿고 이용해 주셔서 감사합니다. 감사의 마음을 담아 20% 추가 할인 쿠폰을 드립니다.', 'acf-nudge-flow' ),
                    'auto_apply' => true
                ),
            ),
        );
        
        // 넛지 템플릿 (Nudge Templates) - IFDO 마케팅 자동화 메시지 기반
        // [v22.5.0] IFDO 세그먼트 스토어 & 디자인 스토어 데이터 반영
        $nudge_templates = array(
            // === RFM 기반 넛지 ===
            'rfm_diamond_vip' => array(
                'title'       => __( '다이아몬드 VIP 리워드', 'acf-nudge-flow' ),
                'description' => __( '최근 구매 + 고빈도 + 고금액 다이아몬드 고객에게 VIP 전용 리워드를 제공합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'RFM',
                'icon'        => 'dashicons-awards',
                'price'       => '₩35,000 / $25 / €23',
                'trigger'     => 'rfm_segment',
                'trigger_settings' => array( 'segment' => 'diamond', 'r_score' => 5, 'f_score' => 5, 'm_level' => 'high' ),
                'action'      => 'discount_reveal',
                'action_settings' => array(
                    'title' => __( '💎 다이아몬드 VIP 전용 혜택', 'acf-nudge-flow' ),
                    'content' => __( '최고 등급 고객님께 감사드립니다. 오늘만 25% 추가 할인!', 'acf-nudge-flow' ),
                    'coupon_code' => 'DIAMOND25',
                    'auto_apply' => true
                ),
            ),
            'rfm_cannot_lose' => array(
                'title'       => __( '절대 놓치지 마세요 (SOS)', 'acf-nudge-flow' ),
                'description' => __( '과거 VIP였으나 최근 구매 없는 고객을 긴급 재활성화합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'RFM',
                'icon'        => 'dashicons-warning',
                'price'       => '₩29,000 / $21 / €19',
                'trigger'     => 'rfm_segment',
                'trigger_settings' => array( 'segment' => 'cannot_lose', 'r_score_max' => 2, 'f_score_min' => 4 ),
                'action'      => 'popup_center',
                'action_settings' => array(
                    'title' => __( '🚨 오랜만이에요! 돌아와주세요', 'acf-nudge-flow' ),
                    'content' => __( '저희가 많이 그리웠어요. 특별 복귀 할인 30%를 준비했습니다!', 'acf-nudge-flow' ),
                    'cta_text' => __( '다시 만나기', 'acf-nudge-flow' ),
                    'style' => 'urgent'
                ),
            ),
            'rfm_new_customer_welcome' => array(
                'title'       => __( '신규 고객 웰컴 시리즈', 'acf-nudge-flow' ),
                'description' => __( '최근 첫 구매 신규 고객에게 웰컴 시리즈와 두번째 구매 유도 메시지를 전송합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'RFM',
                'icon'        => 'dashicons-heart',
                'trigger'     => 'rfm_segment',
                'trigger_settings' => array( 'segment' => 'new_customer', 'r_score' => 5, 'f_score' => 1 ),
                'action'      => 'popup_slide_in',
                'action_settings' => array(
                    'position' => 'bottom-right',
                    'title' => __( '🎉 첫 구매 감사합니다!', 'acf-nudge-flow' ),
                    'content' => __( '다음 구매 시 사용 가능한 15% 쿠폰을 드려요.', 'acf-nudge-flow' ),
                    'cta_text' => __( '쿠폰 받기', 'acf-nudge-flow' )
                ),
            ),
            'rfm_hibernating' => array(
                'title'       => __( '겨울잠 고객 재활성화', 'acf-nudge-flow' ),
                'description' => __( '장기간 미구매 휴면 고객에게 재활성화 캠페인과 특별 혜택을 전송합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'RFM',
                'icon'        => 'dashicons-clock',
                'price'       => '₩19,000 / $14 / €13',
                'trigger'     => 'rfm_segment',
                'trigger_settings' => array( 'segment' => 'hibernating', 'r_score' => 1, 'f_score_max' => 2 ),
                'action'      => 'toast',
                'action_settings' => array(
                    'message' => __( '😴 오랜만이에요! 복귀 기념 무료배송 쿠폰을 확인하세요', 'acf-nudge-flow' ),
                    'type' => 'promo',
                    'position' => 'bottom-left',
                    'duration' => 10
                ),
            ),
            'rfm_at_risk' => array(
                'title'       => __( '갈림길 고객 리텐션', 'acf-nudge-flow' ),
                'description' => __( '이탈 위험 고객에게 리텐션 캠페인과 혜택을 제공합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'RFM',
                'icon'        => 'dashicons-admin-network',
                'price'       => '₩25,000 / $18 / €17',
                'trigger'     => 'rfm_segment',
                'trigger_settings' => array( 'segment' => 'at_risk', 'r_score_range' => '2-3', 'f_score_range' => '3-4' ),
                'action'      => 'popup_center',
                'action_settings' => array(
                    'title' => __( '🤔 뭔가 놓치신 건 없으신가요?', 'acf-nudge-flow' ),
                    'content' => __( '관심 있으셨던 상품들이 기다리고 있어요. 지금 확인해보세요!', 'acf-nudge-flow' ),
                    'cta_text' => __( '상품 보러 가기', 'acf-nudge-flow' ),
                    'style' => 'friendly'
                ),
            ),

            // === 세그먼트 스토어 기반 넛지 ===
            'segment_loyal_visitor' => array(
                'title'       => __( '충성 방문자 감사 메시지', 'acf-nudge-flow' ),
                'description' => __( '누적 방문수 10회 이상인 충성 방문자에게 감사 메시지와 혜택을 제공합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Segment',
                'icon'        => 'dashicons-groups',
                'trigger'     => 'visit_count',
                'trigger_settings' => array( 'operator' => '>=', 'count' => 10 ),
                'action'      => 'popup_slide_in',
                'action_settings' => array(
                    'position' => 'bottom-right',
                    'title' => __( '🙏 자주 방문해 주셔서 감사해요!', 'acf-nudge-flow' ),
                    'content' => __( '충성 고객님께 특별 할인 쿠폰을 드립니다.', 'acf-nudge-flow' ),
                    'cta_text' => __( '쿠폰 받기', 'acf-nudge-flow' )
                ),
            ),
            'segment_bounce_risk' => array(
                'title'       => __( '이탈 위험 방문자 잡기', 'acf-nudge-flow' ),
                'description' => __( '평균 체류시간 30초 이내인 이탈 위험 방문자에게 관심 유도 콘텐츠를 제공합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Segment',
                'icon'        => 'dashicons-migrate',
                'trigger'     => 'avg_stay_time',
                'trigger_settings' => array( 'operator' => '<=', 'seconds' => 30 ),
                'action'      => 'toast',
                'action_settings' => array(
                    'message' => __( '👀 잠깐만요! 놓치기 아까운 인기 상품을 확인해보세요', 'acf-nudge-flow' ),
                    'type' => 'info',
                    'position' => 'top-center',
                    'duration' => 5
                ),
            ),
            'segment_search_engine' => array(
                'title'       => __( '검색엔진 유입자 환영', 'acf-nudge-flow' ),
                'description' => __( '검색엔진에서 유입된 방문자에게 맞춤 환영 메시지를 제공합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Segment',
                'icon'        => 'dashicons-search',
                'trigger'     => 'referrer_type',
                'trigger_settings' => array( 'type' => 'search_engine' ),
                'action'      => 'popup_slide_in',
                'action_settings' => array(
                    'position' => 'bottom-right',
                    'title' => __( '🔍 찾으시던 정보를 발견하셨나요?', 'acf-nudge-flow' ),
                    'content' => __( '검색으로 오셨군요! 원하시는 상품을 빠르게 찾아드릴게요.', 'acf-nudge-flow' ),
                    'cta_text' => __( '상품 검색', 'acf-nudge-flow' )
                ),
            ),
            'segment_campaign_visitor' => array(
                'title'       => __( '캠페인 유입자 특별 오퍼', 'acf-nudge-flow' ),
                'description' => __( '특정 캠페인으로 유입된 방문자에게 캠페인 전용 혜택을 제공합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'Segment',
                'icon'        => 'dashicons-megaphone',
                'price'       => '₩15,000 / $11 / €10',
                'trigger'     => 'utm_campaign',
                'trigger_settings' => array( 'campaign' => '' ), // 사용자가 설정
                'action'      => 'popup_center',
                'action_settings' => array(
                    'title' => __( '🎯 캠페인 전용 혜택!', 'acf-nudge-flow' ),
                    'content' => __( '이 링크로 오셨다면 특별 혜택을 받으실 수 있어요.', 'acf-nudge-flow' ),
                    'cta_text' => __( '혜택 확인하기', 'acf-nudge-flow' )
                ),
            ),
            'segment_dormant_return' => array(
                'title'       => __( '휴면 복귀자 환영', 'acf-nudge-flow' ),
                'description' => __( '마지막 방문 이후 30일 이상 방문이 없었다가 다시 방문한 고객을 환영합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Segment',
                'icon'        => 'dashicons-update',
                'trigger'     => 'days_since_last_visit',
                'trigger_settings' => array( 'operator' => '>=', 'days' => 30 ),
                'action'      => 'popup_center',
                'action_settings' => array(
                    'title' => __( '🎊 다시 오셨군요!', 'acf-nudge-flow' ),
                    'content' => __( '오랜만에 방문해 주셔서 감사해요. 복귀 기념 할인 쿠폰을 드립니다!', 'acf-nudge-flow' ),
                    'cta_text' => __( '쿠폰 받기', 'acf-nudge-flow' )
                ),
            ),

            // === 디자인 스토어 기반 넛지 (UI 타입별) ===
            'design_round_floating_kakao' => array(
                'title'       => __( '카카오톡 채널 추가 유도', 'acf-nudge-flow' ),
                'description' => __( '라운드 플로팅 디자인으로 카카오톡 채널 추가를 유도합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Design Store',
                'icon'        => 'dashicons-format-chat',
                'trigger'     => 'time_on_site',
                'trigger_settings' => array( 'seconds' => 10 ),
                'action'      => 'floating_button',
                'action_settings' => array(
                    'type' => 'kakao_channel',
                    'message' => __( '🎁 카톡채널 추가하고 경품받기', 'acf-nudge-flow' ),
                    'icon' => 'gift',
                    'position' => 'bottom-right'
                ),
            ),
            'design_round_floating_phone' => array(
                'title'       => __( '전화 상담 연결 버튼', 'acf-nudge-flow' ),
                'description' => __( '라운드 플로팅 디자인으로 무료 상담 전화 연결을 유도합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Design Store',
                'icon'        => 'dashicons-phone',
                'trigger'     => 'scroll_depth',
                'trigger_settings' => array( 'percentage' => 30 ),
                'action'      => 'floating_button',
                'action_settings' => array(
                    'type' => 'phone_call',
                    'message' => __( '📞 무료 상담 전화 안내', 'acf-nudge-flow' ),
                    'phone_number' => '080-123-1234',
                    'position' => 'bottom-right'
                ),
            ),
            'design_rich_popup_video' => array(
                'title'       => __( '동영상 리치 팝업', 'acf-nudge-flow' ),
                'description' => __( '리치팝업 동영상 전용 디자인으로 이벤트 영상을 자동 재생합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'Design Store',
                'icon'        => 'dashicons-video-alt3',
                'price'       => '₩25,000 / $18 / €17',
                'trigger'     => 'first_visit',
                'trigger_settings' => array(),
                'action'      => 'video_popup',
                'action_settings' => array(
                    'title' => __( '🎬 오늘의 이벤트', 'acf-nudge-flow' ),
                    'video_url' => '',
                    'autoplay' => true
                ),
            ),
            'design_coupon_box' => array(
                'title'       => __( '쿠폰박스 할인 증정', 'acf-nudge-flow' ),
                'description' => __( '쿠폰박스 디자인으로 할인 쿠폰 다운로드를 유도합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Design Store',
                'icon'        => 'dashicons-tickets-alt',
                'trigger'     => 'time_on_site',
                'trigger_settings' => array( 'seconds' => 15 ),
                'action'      => 'coupon_box',
                'action_settings' => array(
                    'title' => __( '🎟️ 할인 쿠폰 증정', 'acf-nudge-flow' ),
                    'coupon_code' => 'WELCOME10',
                    'discount_text' => '10% 할인',
                    'cta_text' => __( '쿠폰 다운로드', 'acf-nudge-flow' )
                ),
            ),
            'design_welcome_bar' => array(
                'title'       => __( '웰컴바 상단 공지', 'acf-nudge-flow' ),
                'description' => __( '웰컴바 공지형 디자인으로 중요 공지나 프로모션을 상단에 표시합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Design Store',
                'icon'        => 'dashicons-info-outline',
                'trigger'     => 'page_load',
                'trigger_settings' => array(),
                'action'      => 'welcome_bar',
                'action_settings' => array(
                    'message' => __( '📢 오늘만! 전 품목 무료배송 이벤트 진행중', 'acf-nudge-flow' ),
                    'position' => 'top',
                    'dismissible' => true,
                    'link_text' => __( '자세히 보기', 'acf-nudge-flow' )
                ),
            ),
            'design_product_recommend_pc' => array(
                'title'       => __( 'PC 맞춤 상품 추천', 'acf-nudge-flow' ),
                'description' => __( 'PC 화면에 최적화된 상품 추천 디자인으로 맞춤 상품을 노출합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'Design Store',
                'icon'        => 'dashicons-desktop',
                'price'       => '₩29,000 / $21 / €19',
                'trigger'     => 'device_type',
                'trigger_settings' => array( 'device' => 'pc' ),
                'action'      => 'product_recommend',
                'action_settings' => array(
                    'title' => __( '👀 이 상품은 어떠세요?', 'acf-nudge-flow' ),
                    'recommendation_type' => 'personalized',
                    'layout' => 'horizontal',
                    'max_products' => 4
                ),
            ),
            'design_product_recommend_mobile' => array(
                'title'       => __( '모바일 맞춤 상품 추천', 'acf-nudge-flow' ),
                'description' => __( '모바일 화면에 최적화된 상품 추천 디자인으로 맞춤 상품을 노출합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'Design Store',
                'icon'        => 'dashicons-smartphone',
                'price'       => '₩29,000 / $21 / €19',
                'trigger'     => 'device_type',
                'trigger_settings' => array( 'device' => 'mobile' ),
                'action'      => 'product_recommend',
                'action_settings' => array(
                    'title' => __( '📱 추천 상품', 'acf-nudge-flow' ),
                    'recommendation_type' => 'personalized',
                    'layout' => 'vertical',
                    'max_products' => 3
                ),
            ),

            // === 전자상거래 세그먼트 기반 ===
            'ecom_cart_abandon' => array(
                'title'       => __( '장바구니 이탈 긴급 알림', 'acf-nudge-flow' ),
                'description' => __( '장바구니에 상품을 담은 후 3일 이상 구매하지 않은 방문자에게 리마인드 메시지를 보냅니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'E-commerce',
                'icon'        => 'dashicons-cart',
                'trigger'     => 'cart_abandonment',
                'trigger_settings' => array( 'days' => 3 ),
                'action'      => 'popup_center',
                'action_settings' => array(
                    'title' => __( '🛒 장바구니를 잊으셨나요?', 'acf-nudge-flow' ),
                    'content' => __( '담아두신 상품이 기다리고 있어요. 지금 결제하면 추가 5% 할인!', 'acf-nudge-flow' ),
                    'cta_text' => __( '장바구니 보기', 'acf-nudge-flow' )
                ),
            ),
            'ecom_wishlist_promo' => array(
                'title'       => __( '위시리스트 상품 할인 알림', 'acf-nudge-flow' ),
                'description' => __( '위시리스트에 5개 이상 상품을 보관 중인 방문자에게 할인 알림을 보냅니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'E-commerce',
                'icon'        => 'dashicons-heart',
                'price'       => '₩19,000 / $14 / €13',
                'trigger'     => 'wishlist_count',
                'trigger_settings' => array( 'operator' => '>=', 'count' => 5 ),
                'action'      => 'toast',
                'action_settings' => array(
                    'message' => __( '💝 찜한 상품 중 3개가 지금 할인 중이에요!', 'acf-nudge-flow' ),
                    'type' => 'promo',
                    'position' => 'bottom-right',
                    'duration' => 8
                ),
            ),
            'ecom_repeat_buyer' => array(
                'title'       => __( '재구매자 감사 메시지', 'acf-nudge-flow' ),
                'description' => __( '누적 구매 2회 이상인 재구매 고객에게 감사 메시지와 추가 혜택을 제공합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'E-commerce',
                'icon'        => 'dashicons-thumbs-up',
                'trigger'     => 'purchase_count',
                'trigger_settings' => array( 'operator' => '>=', 'count' => 2 ),
                'action'      => 'popup_slide_in',
                'action_settings' => array(
                    'position' => 'bottom-right',
                    'title' => __( '🙏 재구매 감사합니다!', 'acf-nudge-flow' ),
                    'content' => __( '다시 찾아주신 고객님께 특별 쿠폰을 드려요.', 'acf-nudge-flow' ),
                    'cta_text' => __( '쿠폰 받기', 'acf-nudge-flow' )
                ),
            ),
            'ecom_high_value_customer' => array(
                'title'       => __( '고가치 고객 전용 오퍼', 'acf-nudge-flow' ),
                'description' => __( '누적 구매액 100만원 이상인 고가치 고객에게 전용 혜택을 제공합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'E-commerce',
                'icon'        => 'dashicons-money-alt',
                'price'       => '₩35,000 / $25 / €23',
                'trigger'     => 'total_spent',
                'trigger_settings' => array( 'operator' => '>=', 'amount' => 1000000 ),
                'action'      => 'discount_reveal',
                'action_settings' => array(
                    'title' => __( '👑 프리미엄 고객 전용', 'acf-nudge-flow' ),
                    'content' => __( '최상위 고객님께만 드리는 비밀 쿠폰입니다.', 'acf-nudge-flow' ),
                    'coupon_code' => 'PREMIUM30',
                    'auto_apply' => true
                ),
            ),
        );

        // [v22.5.0] 디자인 템플릿 (Design Templates) - 미리 디자인된 시각적 템플릿
        $design_templates = array(
            'design_minimal_white' => array(
                'title'       => __( '미니멀 화이트', 'acf-nudge-flow' ),
                'description' => __( '깔끔하고 심플한 화이트 배경의 미니멀 디자인. 모든 브랜드에 어울리는 범용 템플릿입니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Minimal',
                'icon'        => 'dashicons-layout',
                'preview'     => 'minimal-white.png',
                'styles'      => array(
                    'background' => '#ffffff',
                    'color' => '#1f2937',
                    'border_radius' => '12px',
                    'shadow' => '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
                    'font_family' => 'Inter, -apple-system, sans-serif',
                ),
                'trigger'     => 'time_on_site',
                'trigger_settings' => array( 'seconds' => 5 ),
                'action'      => 'popup_center',
                'action_settings' => array(
                    'title' => __( '특별 할인 안내', 'acf-nudge-flow' ),
                    'content' => __( '지금 가입하시면 첫 구매 15% 할인 혜택을 드립니다.', 'acf-nudge-flow' ),
                    'cta_text' => __( '할인 받기', 'acf-nudge-flow' ),
                    'style' => 'minimal'
                ),
            ),
            'design_gradient_purple' => array(
                'title'       => __( '그라디언트 퍼플', 'acf-nudge-flow' ),
                'description' => __( '눈길을 사로잡는 보라색 그라디언트 디자인. 프리미엄 브랜드에 적합합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Gradient',
                'icon'        => 'dashicons-art',
                'preview'     => 'gradient-purple.png',
                'styles'      => array(
                    'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    'color' => '#ffffff',
                    'border_radius' => '16px',
                    'shadow' => '0 20px 25px -5px rgba(102, 126, 234, 0.3)',
                    'font_family' => 'Poppins, sans-serif',
                ),
                'trigger'     => 'scroll_depth',
                'trigger_settings' => array( 'percentage' => 50 ),
                'action'      => 'popup_center',
                'action_settings' => array(
                    'title' => __( '🎉 특별 이벤트', 'acf-nudge-flow' ),
                    'content' => __( '지금 바로 참여하고 놀라운 혜택을 받아보세요!', 'acf-nudge-flow' ),
                    'cta_text' => __( '참여하기', 'acf-nudge-flow' ),
                    'style' => 'gradient'
                ),
            ),
            'design_dark_elegant' => array(
                'title'       => __( '다크 엘레강트', 'acf-nudge-flow' ),
                'description' => __( '세련된 다크 테마 디자인. 럭셔리 브랜드나 테크 기업에 적합합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Dark',
                'icon'        => 'dashicons-visibility',
                'preview'     => 'dark-elegant.png',
                'styles'      => array(
                    'background' => '#1a1a2e',
                    'color' => '#eaeaea',
                    'border_radius' => '8px',
                    'shadow' => '0 25px 50px -12px rgba(0, 0, 0, 0.5)',
                    'accent_color' => '#e94560',
                    'font_family' => 'SF Pro Display, -apple-system, sans-serif',
                ),
                'trigger'     => 'exit_intent',
                'trigger_settings' => array(),
                'action'      => 'popup_center',
                'action_settings' => array(
                    'title' => __( '잠깐만요! ✨', 'acf-nudge-flow' ),
                    'content' => __( '떠나시기 전에 특별한 제안을 확인해보세요.', 'acf-nudge-flow' ),
                    'cta_text' => __( '제안 확인', 'acf-nudge-flow' ),
                    'style' => 'dark'
                ),
            ),
            'design_playful_colorful' => array(
                'title'       => __( '플레이풀 컬러풀', 'acf-nudge-flow' ),
                'description' => __( '밝고 경쾌한 멀티 컬러 디자인. 젊은 타겟층을 위한 브랜드에 적합합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Playful',
                'icon'        => 'dashicons-smiley',
                'preview'     => 'playful-colorful.png',
                'styles'      => array(
                    'background' => '#fff5f5',
                    'color' => '#2d3748',
                    'border_radius' => '20px',
                    'border' => '3px solid #f687b3',
                    'shadow' => '0 10px 40px rgba(246, 135, 179, 0.3)',
                    'font_family' => 'Nunito, Comic Sans MS, sans-serif',
                ),
                'trigger'     => 'first_visit',
                'trigger_settings' => array(),
                'action'      => 'popup_center',
                'action_settings' => array(
                    'title' => __( '안녕하세요! 👋', 'acf-nudge-flow' ),
                    'content' => __( '처음 오셨군요! 환영 선물로 10% 할인 쿠폰을 드려요 🎁', 'acf-nudge-flow' ),
                    'cta_text' => __( '쿠폰 받기', 'acf-nudge-flow' ),
                    'style' => 'playful'
                ),
            ),
            'design_corporate_blue' => array(
                'title'       => __( '코퍼레이트 블루', 'acf-nudge-flow' ),
                'description' => __( '신뢰감을 주는 기업형 블루 디자인. B2B 비즈니스나 금융 서비스에 적합합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'Corporate',
                'icon'        => 'dashicons-building',
                'price'       => '₩15,000 / $11 / €10',
                'preview'     => 'corporate-blue.png',
                'styles'      => array(
                    'background' => '#f8fafc',
                    'color' => '#1e40af',
                    'border_radius' => '4px',
                    'border' => '1px solid #bfdbfe',
                    'shadow' => '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                    'font_family' => 'Roboto, Arial, sans-serif',
                ),
                'trigger'     => 'visit_count',
                'trigger_settings' => array( 'operator' => '>=', 'count' => 3 ),
                'action'      => 'popup_slide_in',
                'action_settings' => array(
                    'position' => 'bottom-right',
                    'title' => __( '비즈니스 상담 안내', 'acf-nudge-flow' ),
                    'content' => __( '전문 컨설턴트와 무료 상담을 받아보세요.', 'acf-nudge-flow' ),
                    'cta_text' => __( '상담 신청', 'acf-nudge-flow' ),
                    'style' => 'corporate'
                ),
            ),
            'design_glassmorphism' => array(
                'title'       => __( '글래스모피즘', 'acf-nudge-flow' ),
                'description' => __( '트렌디한 유리 효과 디자인. 모던하고 세련된 이미지를 원하는 브랜드에 적합합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'Modern',
                'icon'        => 'dashicons-admin-appearance',
                'price'       => '₩19,000 / $14 / €13',
                'preview'     => 'glassmorphism.png',
                'styles'      => array(
                    'background' => 'rgba(255, 255, 255, 0.2)',
                    'backdrop_filter' => 'blur(20px)',
                    'color' => '#ffffff',
                    'border_radius' => '24px',
                    'border' => '1px solid rgba(255, 255, 255, 0.3)',
                    'shadow' => '0 8px 32px rgba(0, 0, 0, 0.1)',
                    'font_family' => 'Inter, -apple-system, sans-serif',
                ),
                'trigger'     => 'time_on_site',
                'trigger_settings' => array( 'seconds' => 10 ),
                'action'      => 'popup_center',
                'action_settings' => array(
                    'title' => __( '독점 혜택', 'acf-nudge-flow' ),
                    'content' => __( '지금 가입하시면 VIP 회원 전용 혜택을 제공해 드립니다.', 'acf-nudge-flow' ),
                    'cta_text' => __( '가입하기', 'acf-nudge-flow' ),
                    'style' => 'glass'
                ),
            ),
            'design_neomorphism' => array(
                'title'       => __( '뉴모피즘', 'acf-nudge-flow' ),
                'description' => __( '부드러운 3D 효과의 뉴모픽 디자인. UI/UX 중심 브랜드에 적합합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'Modern',
                'icon'        => 'dashicons-editor-contract',
                'price'       => '₩19,000 / $14 / €13',
                'preview'     => 'neomorphism.png',
                'styles'      => array(
                    'background' => '#e0e5ec',
                    'color' => '#4a5568',
                    'border_radius' => '20px',
                    'shadow' => '20px 20px 60px #bebebe, -20px -20px 60px #ffffff',
                    'font_family' => 'Quicksand, sans-serif',
                ),
                'trigger'     => 'scroll_depth',
                'trigger_settings' => array( 'percentage' => 75 ),
                'action'      => 'popup_center',
                'action_settings' => array(
                    'title' => __( '관심 감사드려요', 'acf-nudge-flow' ),
                    'content' => __( '콘텐츠가 마음에 드셨나요? 뉴스레터를 구독해보세요.', 'acf-nudge-flow' ),
                    'cta_text' => __( '구독하기', 'acf-nudge-flow' ),
                    'style' => 'neumorphic'
                ),
            ),
            'design_toast_notification' => array(
                'title'       => __( '토스트 알림', 'acf-nudge-flow' ),
                'description' => __( '눈에 띄지 않으면서 효과적인 토스트 알림 디자인. 비침투적 넛지에 적합합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Notification',
                'icon'        => 'dashicons-megaphone',
                'preview'     => 'toast-notification.png',
                'styles'      => array(
                    'background' => '#323232',
                    'color' => '#ffffff',
                    'border_radius' => '8px',
                    'shadow' => '0 3px 10px rgba(0, 0, 0, 0.2)',
                    'font_family' => 'system-ui, sans-serif',
                ),
                'trigger'     => 'time_on_site',
                'trigger_settings' => array( 'seconds' => 3 ),
                'action'      => 'toast',
                'action_settings' => array(
                    'message' => __( '🔥 현재 25명이 이 상품을 보고 있습니다!', 'acf-nudge-flow' ),
                    'type' => 'info',
                    'position' => 'bottom-left',
                    'duration' => 5
                ),
            ),
            'design_fullscreen_hero' => array(
                'title'       => __( '풀스크린 히어로', 'acf-nudge-flow' ),
                'description' => __( '강렬한 임팩트의 전체 화면 오버레이 디자인. 중요 공지나 대형 프로모션에 적합합니다.', 'acf-nudge-flow' ),
                'type'        => 'premium',
                'category'    => 'Fullscreen',
                'icon'        => 'dashicons-fullscreen-alt',
                'price'       => '₩25,000 / $18 / €17',
                'preview'     => 'fullscreen-hero.png',
                'styles'      => array(
                    'background' => 'linear-gradient(to right, #0f0c29, #302b63, #24243e)',
                    'color' => '#ffffff',
                    'font_family' => 'Montserrat, sans-serif',
                ),
                'trigger'     => 'first_visit',
                'trigger_settings' => array(),
                'action'      => 'fullscreen',
                'action_settings' => array(
                    'title' => __( '🚀 그랜드 오픈!', 'acf-nudge-flow' ),
                    'content' => __( '새로운 시즌이 시작되었습니다. 지금 최대 50% 할인 혜택을 만나보세요.', 'acf-nudge-flow' ),
                    'cta_text' => __( '쇼핑하러 가기', 'acf-nudge-flow' ),
                    'style' => 'fullscreen'
                ),
            ),
            'design_slide_in_bar' => array(
                'title'       => __( '슬라이드 인 바', 'acf-nudge-flow' ),
                'description' => __( '화면 상단/하단에서 슬라이드로 나타나는 바 디자인. 공지나 프로모션 코드 전달에 적합합니다.', 'acf-nudge-flow' ),
                'type'        => 'free',
                'category'    => 'Bar',
                'icon'        => 'dashicons-minus',
                'preview'     => 'slide-in-bar.png',
                'styles'      => array(
                    'background' => '#10b981',
                    'color' => '#ffffff',
                    'font_family' => 'Inter, sans-serif',
                ),
                'trigger'     => 'time_on_site',
                'trigger_settings' => array( 'seconds' => 2 ),
                'action'      => 'countdown_bar',
                'action_settings' => array(
                    'message' => __( '⏰ 한정 특가! 오늘만 무료 배송 - 코드: FREESHIP', 'acf-nudge-flow' ),
                    'position' => 'top',
                    'countdown_hours' => 24,
                    'dismissible' => true
                ),
            ),
        );

        // 타입별 반환
        if ( $type === 'customer' ) {
            return $customer_templates;
        } elseif ( $type === 'nudge' ) {
            return $nudge_templates;
        } elseif ( $type === 'design' ) {
            return $design_templates;
        } else {
            // 'all' - 모든 타입 반환
            // 데이터베이스 템플릿이 있으면 병합
            if ( isset( $nudge_templates ) && ! empty( $nudge_templates ) ) {
                return array_merge( $customer_templates, $nudge_templates, $design_templates );
            } else {
                return array_merge( $customer_templates, $nudge_templates, $design_templates );
            }
        }
    }

    /**
     * 템플릿 센터 렌더링
     * [v22.5.0] 고객 템플릿과 넛지 템플릿 분리 표시
     */
    public function render_template_center() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-nudge-flow' ) );
        }
        $customer_templates = $this->get_preset_templates( 'customer' );
        $nudge_templates = $this->get_preset_templates( 'nudge' );
        $design_templates = $this->get_preset_templates( 'design' );
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'customer';
        ?>


        <div class="wrap acf-nudge-flow-admin">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h1><?php esc_html_e( '🎁 템플릿 센터', 'acf-nudge-flow' ); ?></h1>
                <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-flow-workflows' ); ?>" class="button">
                    <?php esc_html_e( '워크플로우 목록', 'acf-nudge-flow' ); ?>
                </a>
            </div>

            <div class="notice notice-info">
                <p><?php esc_html_e( '개인화 마케팅 보고서 기반의 고효율 프리셋을 즉시 설치할 수 있습니다. 설치된 템플릿은 워크플로우 메뉴에서 확인 가능합니다.', 'acf-nudge-flow' ); ?></p>
            </div>

            <!-- 탭 네비게이션 -->
            <nav class="nav-tab-wrapper" style="margin-bottom: 20px;">
                <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-template-center&tab=customer' ); ?>"
                   class="nav-tab <?php echo $active_tab === 'customer' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e( '👥 고객 템플릿', 'acf-nudge-flow' ); ?>
                    <span class="count">(<?php echo count( $customer_templates ); ?>)</span>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-template-center&tab=nudge' ); ?>"
                   class="nav-tab <?php echo $active_tab === 'nudge' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e( '📢 넛지 템플릿', 'acf-nudge-flow' ); ?>
                    <span class="count">(<?php echo count( $nudge_templates ); ?>)</span>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-template-center&tab=design' ); ?>"
                   class="nav-tab <?php echo $active_tab === 'design' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e( '🎨 디자인 템플릿', 'acf-nudge-flow' ); ?>
                    <span class="count">(<?php echo count( $design_templates ); ?>)</span>
                </a>
            </nav>

            <?php if ( $active_tab === 'customer' ) : ?>
                <div class="acf-nudge-market-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px; margin-top:20px;">
                    <?php foreach ( $customer_templates as $id => $data ) : 
                        $is_premium = ( $data['type'] === 'premium' );
                    ?>
                    <div class="postbox" style="border-radius:8px; overflow:hidden;">
                        <div style="padding:20px; background:<?php echo $is_premium ? '#fffbeb' : '#f8fafc'; ?>; border-bottom:1px solid #eee; text-align:center;">
                            <span class="dashicons <?php echo esc_attr( $data['icon'] ); ?>" style="font-size:40px; width:40px; height:40px; color:<?php echo $is_premium ? '#f59e0b' : '#94a3b8'; ?>;"></span>
                        </div>
                        <div style="padding:15px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                                <span style="font-size:10px; font-weight:700; color:#6366f1; text-transform:uppercase;"><?php echo esc_html( $data['category'] ); ?></span>
                                <span class="badge" style="background:<?php echo $is_premium ? '#fef3c7' : '#f1f5f9'; ?>; color:<?php echo $is_premium ? '#b45309' : '#475569'; ?>; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:700;">
                                    <?php echo $is_premium ? esc_html( $data['price'] ) : __( 'FREE', 'acf-nudge-flow' ); ?>
                                </span>
                            </div>
                            <h3 style="margin:0 0 10px; font-size:16px;"><?php echo esc_html( $data['title'] ); ?></h3>
                            <p style="font-size:12px; color:#666; height:45px; overflow:hidden;"><?php echo esc_html( $data['description'] ); ?></p>
                            <div style="margin-top:15px; text-align:right;">
                                <button class="button <?php echo $is_premium ? 'button-primary' : 'button-secondary'; ?> acf-install-preset" data-preset="<?php echo esc_attr( $id ); ?>">
                                    <?php echo $is_premium ? __( '구매/설치', 'acf-nudge-flow' ) : __( '즉시 설치', 'acf-nudge-flow' ); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif ( $active_tab === 'nudge' ) : ?>
                <div class="acf-nudge-market-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px; margin-top:20px;">
                    <?php if ( empty( $nudge_templates ) ) : ?>
                        <div class="postbox" style="padding: 40px; text-align: center; grid-column: 1 / -1;">
                            <p style="font-size: 16px; color: #666;">
                                <?php esc_html_e( '넛지 템플릿이 아직 없습니다.', 'acf-nudge-flow' ); ?>
                            </p>
                            <p style="font-size: 14px; color: #999; margin-top: 10px;">
                                <?php esc_html_e( '이프두에서 수집한 오토 메시지 템플릿이 여기에 표시됩니다.', 'acf-nudge-flow' ); ?>
                            </p>
                        </div>
                    <?php else : ?>
                        <?php foreach ( $nudge_templates as $id => $data ) :
                            $is_premium = ( $data['type'] === 'premium' );
                        ?>
                        <div class="postbox" style="border-radius:8px; overflow:hidden;">
                            <div style="padding:20px; background:<?php echo $is_premium ? '#fffbeb' : '#f8fafc'; ?>; border-bottom:1px solid #eee; text-align:center;">
                                <span class="dashicons <?php echo esc_attr( $data['icon'] ); ?>" style="font-size:40px; width:40px; height:40px; color:<?php echo $is_premium ? '#f59e0b' : '#94a3b8'; ?>;"></span>
                            </div>
                            <div style="padding:15px;">
                                <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                                    <span style="font-size:10px; font-weight:700; color:#6366f1; text-transform:uppercase;"><?php echo esc_html( $data['category'] ); ?></span>
                                    <span class="badge" style="background:<?php echo $is_premium ? '#fef3c7' : '#f1f5f9'; ?>; color:<?php echo $is_premium ? '#b45309' : '#475569'; ?>; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:700;">
                                        <?php echo $is_premium ? esc_html( $data['price'] ) : __( 'FREE', 'acf-nudge-flow' ); ?>
                                    </span>
                                </div>
                                <h3 style="margin:0 0 10px; font-size:16px;"><?php echo esc_html( $data['title'] ); ?></h3>
                                <p style="font-size:12px; color:#666; height:45px; overflow:hidden;"><?php echo esc_html( $data['description'] ); ?></p>
                                <div style="margin-top:15px; text-align:right;">
                                    <button class="button <?php echo $is_premium ? 'button-primary' : 'button-secondary'; ?> acf-install-preset" data-preset="<?php echo esc_attr( $id ); ?>">
                                        <?php echo $is_premium ? __( '구매/설치', 'acf-nudge-flow' ) : __( '즉시 설치', 'acf-nudge-flow' ); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php elseif ( $active_tab === 'design' ) : ?>
                <!-- [v22.5.0] 디자인 템플릿 탭 -->
                <div class="acf-nudge-market-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px; margin-top:20px;">
                    <?php foreach ( $design_templates as $id => $data ) :
                        $is_premium = ( $data['type'] === 'premium' );
                        $styles = $data['styles'] ?? array();
                        $preview_style = 'background: ' . ( $styles['background'] ?? '#f8fafc' ) . ';';
                        if ( isset( $styles['color'] ) ) {
                            $preview_style .= ' color: ' . $styles['color'] . ';';
                        }
                    ?>
                    <div class="postbox" style="border-radius:8px; overflow:hidden;">
                        <!-- 디자인 프리뷰 영역 -->
                        <div style="padding:30px; <?php echo esc_attr( $preview_style ); ?> text-align:center; min-height: 100px; display: flex; align-items: center; justify-content: center; border-bottom:1px solid #eee;">
                            <div>
                                <span class="dashicons <?php echo esc_attr( $data['icon'] ); ?>" style="font-size:32px; width:32px; height:32px; margin-bottom: 10px;"></span>
                                <div style="font-size: 14px; font-weight: 600;"><?php echo esc_html( $data['title'] ); ?></div>
                            </div>
                        </div>
                        <div style="padding:15px; background: #fff;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                                <span style="font-size:10px; font-weight:700; color:#10b981; text-transform:uppercase;"><?php echo esc_html( $data['category'] ); ?></span>
                                <span class="badge" style="background:<?php echo $is_premium ? '#fef3c7' : '#f1f5f9'; ?>; color:<?php echo $is_premium ? '#b45309' : '#475569'; ?>; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:700;">
                                    <?php echo $is_premium ? esc_html( $data['price'] ) : __( 'FREE', 'acf-nudge-flow' ); ?>
                                </span>
                            </div>
                            <p style="font-size:12px; color:#666; height:45px; overflow:hidden;"><?php echo esc_html( $data['description'] ); ?></p>
                            <div style="margin-top:15px; display: flex; gap: 8px; justify-content: flex-end;">
                                <button class="button button-link acf-preview-design" data-preset="<?php echo esc_attr( $id ); ?>" style="font-size: 12px;">
                                    <?php esc_html_e( '미리보기', 'acf-nudge-flow' ); ?>
                                </button>
                                <button class="button <?php echo $is_premium ? 'button-primary' : 'button-secondary'; ?> acf-install-preset" data-preset="<?php echo esc_attr( $id ); ?>">
                                    <?php echo $is_premium ? __( '구매/설치', 'acf-nudge-flow' ) : __( '즉시 설치', 'acf-nudge-flow' ); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $('.acf-install-preset').on('click', function() {
                var presetId = $(this).data('preset');
                var $btn = $(this);
                
                if (confirm('<?php echo esc_js( __( '이 템플릿을 설치하시겠습니까? 설치 후 워크플로우 메뉴에서 확인 가능합니다.', 'acf-nudge-flow' ) ); ?>')) {
                    $btn.prop('disabled', true).text('<?php echo esc_js( __( '설치 중...', 'acf-nudge-flow' ) ); ?>');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'jj_install_nudge_preset',
                            preset_id: presetId,
                            nonce: '<?php echo wp_create_nonce( "acf_nudge_flow_nonce" ); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('<?php echo esc_js( __( '성공적으로 설치되었습니다! 워크플로우 메뉴에서 설정을 확인하세요.', 'acf-nudge-flow' ) ); ?>');
                                $btn.text('<?php echo esc_js( __( '설치 완료', 'acf-nudge-flow' ) ); ?>');
                            } else {
                                alert('오류: ' + response.data);
                                $btn.prop('disabled', false).text('<?php echo esc_js( __( '다시 시도', 'acf-nudge-flow' ) ); ?>');
                            }
                        }
                    });
                }
            });
        });
        </script>
        <?php
    }

    /**
     * 대시보드 렌더링
     */
    public function render_dashboard() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-nudge-flow' ) );
        }
        ?>


        <div class="wrap acf-nudge-flow-admin">
            <h1><?php esc_html_e( 'ACF Nudge Flow', 'acf-nudge-flow' ); ?></h1>
            
            <div class="acf-nudge-dashboard">
                <div class="acf-nudge-stats-grid">
                    <div class="acf-nudge-stat-card">
                        <div class="stat-icon">📊</div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $this->get_total_workflows(); ?></div>
                            <div class="stat-label"><?php esc_html_e( '활성 워크플로우', 'acf-nudge-flow' ); ?></div>
                        </div>
                    </div>
                    
                    <div class="acf-nudge-stat-card">
                        <div class="stat-icon">👁️</div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $this->get_total_impressions(); ?></div>
                            <div class="stat-label"><?php esc_html_e( '오늘 노출', 'acf-nudge-flow' ); ?></div>
                        </div>
                    </div>
                    
                    <div class="acf-nudge-stat-card">
                        <div class="stat-icon">🎯</div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $this->get_conversion_rate(); ?>%</div>
                            <div class="stat-label"><?php esc_html_e( '전환율', 'acf-nudge-flow' ); ?></div>
                        </div>
                    </div>
                    
                    <div class="acf-nudge-stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $this->get_unique_visitors(); ?></div>
                            <div class="stat-label"><?php esc_html_e( '이번 주 방문자', 'acf-nudge-flow' ); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- [v22.3.1] Performance chart (Jenny's design) -->
                <div class="acf-nudge-chart-container">
                    <h2 style="margin: 0 0 20px 0;"><?php esc_html_e( '📈 7일 성과 추이', 'acf-nudge-flow' ); ?></h2>
                    <canvas id="acf-nudge-performance-chart" style="max-height: 300px;"></canvas>
                </div>
                
                <div class="acf-nudge-quick-actions">
                    <h2><?php esc_html_e( '빠른 시작', 'acf-nudge-flow' ); ?></h2>
                    <div class="quick-action-cards">
                        <a href="#" class="quick-action-card" data-preset="welcome_curation" onclick="return acfNudgeInstallPreset('welcome_curation', this);">
                            <span class="icon">👋</span>
                            <span class="title"><?php esc_html_e( '환영 팝업', 'acf-nudge-flow' ); ?></span>
                            <span class="desc"><?php esc_html_e( '첫 방문자에게 환영 메시지', 'acf-nudge-flow' ); ?></span>
                        </a>
                        
                        <a href="#" class="quick-action-card" data-preset="cart_recovery" onclick="return acfNudgeInstallPreset('cart_recovery', this);">
                            <span class="icon">🚪</span>
                            <span class="title"><?php esc_html_e( '이탈 방지 팝업', 'acf-nudge-flow' ); ?></span>
                            <span class="desc"><?php esc_html_e( '이탈 시 할인 제안', 'acf-nudge-flow' ); ?></span>
                        </a>
                        
                        <a href="#" class="quick-action-card" data-preset="signup_nudge" onclick="return acfNudgeInstallPreset('signup_nudge', this);">
                            <span class="icon">📧</span>
                            <span class="title"><?php esc_html_e( '뉴스레터 구독', 'acf-nudge-flow' ); ?></span>
                            <span class="desc"><?php esc_html_e( '이메일 수집 팝업', 'acf-nudge-flow' ); ?></span>
                        </a>
                        
                        <a href="#" class="quick-action-card" data-preset="free_shipping" onclick="return acfNudgeInstallPreset('free_shipping', this);">
                            <span class="icon">🛒</span>
                            <span class="title"><?php esc_html_e( '장바구니 리마인더', 'acf-nudge-flow' ); ?></span>
                            <span class="desc"><?php esc_html_e( '장바구니 이탈 고객 유도', 'acf-nudge-flow' ); ?></span>
                        </a>
                    </div>
                </div>
                
                <script>
                // [v22.4.6] 빠른 시작 카드 클릭 핸들러
                function acfNudgeInstallPreset(presetId, element) {
                    var $card = jQuery(element).closest('.quick-action-card');
                    var originalText = $card.find('.title').text();
                    
                    $card.css('opacity', '0.6').find('.title').text('<?php echo esc_js( __( '설치 중...', 'acf-nudge-flow' ) ); ?>');
                    
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'jj_install_nudge_preset',
                            preset_id: presetId,
                            nonce: '<?php echo wp_create_nonce( "acf_nudge_flow_nonce" ); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                $card.css('opacity', '1').find('.title').text('<?php echo esc_js( __( '설치 완료!', 'acf-nudge-flow' ) ); ?>');
                                setTimeout(function() {
                                    window.location.href = '<?php echo admin_url( "admin.php?page=acf-nudge-flow-workflows" ); ?>';
                                }, 1000);
                            } else {
                                alert('오류: ' + (response.data || '<?php echo esc_js( __( "설치 실패", "acf-nudge-flow" ) ); ?>'));
                                $card.css('opacity', '1').find('.title').text(originalText);
                            }
                        },
                        error: function() {
                            alert('<?php echo esc_js( __( "서버 통신 오류가 발생했습니다.", "acf-nudge-flow" ) ); ?>');
                            $card.css('opacity', '1').find('.title').text(originalText);
                        }
                    });
                    
                    return false;
                }
                </script>
            </div>
        </div>
        
        <script>
        // [v22.3.1] Chart.js visualization - Mikael's data + Jenny's design
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('acf-nudge-performance-chart');
            if (!ctx) return;
            
            // Sample data - will be replaced with real AJAX data
            const chartData = {
                labels: ['월', '화', '수', '목', '금', '토', '일'],
                datasets: [{
                    label: '노출수',
                    data: [120, 190, 300, 250, 200, 280, 310],
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: '전환수',
                    data: [12, 19, 24, 18, 22, 26, 31],
                    borderColor: 'rgb(67, 233, 123)',
                    backgroundColor: 'rgba(67, 233, 123, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            };
            
            new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
        </script>
        <?php
    }

    /**
     * 워크플로우 목록 렌더링
     */
    public function render_workflows() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-nudge-flow' ) );
        }
        $workflows = get_posts( array(


            'post_type'      => 'acf_nudge_workflow',
            'posts_per_page' => -1,
            'post_status'    => array( 'publish', 'draft' ),
        ) );
        ?>
        <div class="wrap acf-nudge-flow-admin">
            <h1>
                <?php esc_html_e( '워크플로우', 'acf-nudge-flow' ); ?>
                <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-flow-builder' ); ?>" class="page-title-action">
                    <?php esc_html_e( '새 워크플로우', 'acf-nudge-flow' ); ?>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-template-center' ); ?>" class="page-title-action">
                    <?php esc_html_e( '템플릿에서 선택', 'acf-nudge-flow' ); ?>
                </a>
            </h1>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( '이름', 'acf-nudge-flow' ); ?></th>
                        <th><?php esc_html_e( '상태', 'acf-nudge-flow' ); ?></th>
                        <th><?php esc_html_e( '노출', 'acf-nudge-flow' ); ?></th>
                        <th><?php esc_html_e( '전환', 'acf-nudge-flow' ); ?></th>
                        <th><?php esc_html_e( '수정일', 'acf-nudge-flow' ); ?></th>
                        <th><?php esc_html_e( '작업', 'acf-nudge-flow' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $workflows ) ) : ?>
                        <tr>
                            <td colspan="6"><?php esc_html_e( '워크플로우가 없습니다.', 'acf-nudge-flow' ); ?></td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $workflows as $workflow ) : 
                            $enabled = get_post_meta( $workflow->ID, '_acf_nudge_workflow_enabled', true );
                        ?>
                            <tr>
                                <td>
                                    <strong>
                                        <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-flow-builder&id=' . $workflow->ID ); ?>">
                                            <?php echo esc_html( $workflow->post_title ); ?>
                                        </a>
                                    </strong>
                                </td>
                                <td>
                                    <?php if ( $enabled ) : ?>
                                        <span class="status-badge status-active"><?php esc_html_e( '활성', 'acf-nudge-flow' ); ?></span>
                                    <?php else : ?>
                                        <span class="status-badge status-inactive"><?php esc_html_e( '비활성', 'acf-nudge-flow' ); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $this->get_workflow_impressions( $workflow->ID ); ?></td>
                                <td><?php echo $this->get_workflow_conversions( $workflow->ID ); ?></td>
                                <td><?php echo get_the_modified_date( 'Y-m-d H:i', $workflow ); ?></td>
                                <td>
                                    <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-flow-builder&id=' . $workflow->ID ); ?>" class="button button-small">
                                        <?php esc_html_e( '편집', 'acf-nudge-flow' ); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * 워크플로우 빌더 렌더링
     * [v23.0.0] 드래그 앤 드롭 + IF-DO 하이브리드 빌더
     */
    public function render_builder() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-nudge-flow' ) );
        }
        $workflow_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
        $template = isset( $_GET['template'] ) ? sanitize_text_field( $_GET['template'] ) : '';

        // 트리거/액션 데이터 전달
        $triggers = array();
        $actions = array();

        // 클래스 자동 로드
        if ( ! class_exists( 'ACF_Nudge_Trigger_Manager' ) ) {
            require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-trigger-manager.php';
        }
        if ( ! class_exists( 'ACF_Nudge_Action_Manager' ) ) {
            require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-action-manager.php';
        }

        $trigger_manager = new ACF_Nudge_Trigger_Manager();
        $triggers = $trigger_manager->get_all();

        $action_manager = new ACF_Nudge_Action_Manager();
        $actions = $action_manager->get_all();

        // 템플릿이 지정된 경우 프리셋 데이터 로드
        $preset_data = null;
        if ( ! empty( $template ) ) {
            $presets = $this->get_preset_templates();
            if ( isset( $presets[ $template ] ) ) {
                $preset_data = $presets[ $template ];
            }
        }

        // 기존 워크플로우 데이터 로드
        $workflow_data = array(
            'trigger_id' => '',
            'trigger_settings' => array(),
            'action_id' => '',
            'action_settings' => array(),
            'enabled' => false,
        );
        if ( $workflow_id ) {
            $workflow_data['trigger_id'] = get_post_meta( $workflow_id, '_acf_nudge_workflow_trigger', true );
            $workflow_data['trigger_settings'] = get_post_meta( $workflow_id, '_acf_nudge_workflow_trigger_settings', true ) ?: array();
            $workflow_data['action_id'] = get_post_meta( $workflow_id, '_acf_nudge_workflow_action', true );
            $workflow_data['action_settings'] = get_post_meta( $workflow_id, '_acf_nudge_workflow_action_settings', true ) ?: array();
            $workflow_data['enabled'] = get_post_meta( $workflow_id, '_acf_nudge_workflow_enabled', true ) === '1';
        } elseif ( $preset_data ) {
            $workflow_data['trigger_id'] = $preset_data['trigger'] ?? '';
            $workflow_data['trigger_settings'] = $preset_data['trigger_settings'] ?? array();
            $workflow_data['action_id'] = $preset_data['action'] ?? '';
            $workflow_data['action_settings'] = $preset_data['action_settings'] ?? array();
        }

        // 카테고리별 트리거 분류
        $trigger_categories = array();
        foreach ( $triggers as $id => $trigger ) {
            $cat = $trigger['category'] ?? 'general';
            if ( ! isset( $trigger_categories[ $cat ] ) ) {
                $trigger_categories[ $cat ] = array();
            }
            $trigger_categories[ $cat ][ $id ] = $trigger;
        }

        // 카테고리별 액션 분류
        $action_categories = array();
        foreach ( $actions as $id => $action ) {
            $cat = $action['category'] ?? 'general';
            if ( ! isset( $action_categories[ $cat ] ) ) {
                $action_categories[ $cat ] = array();
            }
            $action_categories[ $cat ][ $id ] = $action;
        }

        $category_labels = array(
            'visitor' => __( '방문자', 'acf-nudge-flow' ),
            'traffic' => __( '트래픽 소스', 'acf-nudge-flow' ),
            'user' => __( '사용자 상태', 'acf-nudge-flow' ),
            'woocommerce' => __( 'WooCommerce', 'acf-nudge-flow' ),
            'time' => __( '시간', 'acf-nudge-flow' ),
            'general' => __( '일반', 'acf-nudge-flow' ),
            'popup' => __( '팝업/모달', 'acf-nudge-flow' ),
            'bar' => __( '바/배너', 'acf-nudge-flow' ),
            'notification' => __( '토스트/알림', 'acf-nudge-flow' ),
            'form' => __( '폼/리드', 'acf-nudge-flow' ),
            'redirect' => __( '리다이렉트', 'acf-nudge-flow' ),
            'page' => __( '페이지', 'acf-nudge-flow' ),
        );
        ?>
        <style>
        /* [v23.0.0] 드래그 앤 드롭 빌더 스타일 */
        .nf-builder-wrap { background: #f0f2f5; min-height: calc(100vh - 100px); margin-left: -20px; padding: 20px; }
        .nf-builder-header { background: #fff; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .nf-builder-header input[type="text"] { flex: 1; max-width: 400px; padding: 10px 15px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 15px; transition: border-color 0.2s; }
        .nf-builder-header input[type="text"]:focus { border-color: #667eea; outline: none; }
        .nf-builder-header .nf-btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; }
        .nf-builder-header .nf-btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
        .nf-builder-header .nf-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); }
        .nf-builder-header .nf-btn-secondary { background: #fff; color: #374151; border: 2px solid #e2e8f0; }
        .nf-builder-header .nf-btn-secondary:hover { border-color: #667eea; color: #667eea; }
        .nf-builder-header .nf-toggle { display: flex; align-items: center; gap: 8px; padding: 8px 15px; background: #f8fafc; border-radius: 8px; }
        .nf-builder-header .nf-toggle input[type="checkbox"] { width: 18px; height: 18px; accent-color: #10b981; }

        .nf-builder-main { display: grid; grid-template-columns: 280px 1fr 320px; gap: 20px; min-height: 600px; }

        /* 좌측 팔레트 */
        .nf-palette { background: #fff; border-radius: 12px; padding: 20px; overflow-y: auto; max-height: calc(100vh - 200px); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .nf-palette h3 { margin: 0 0 15px; font-size: 14px; color: #374151; display: flex; align-items: center; gap: 8px; }
        .nf-palette h3 .icon { font-size: 18px; }
        .nf-palette-section { margin-bottom: 25px; }
        .nf-palette-category { font-size: 11px; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.5px; margin: 15px 0 8px; padding-left: 5px; }
        .nf-palette-item {
            padding: 12px 15px;
            background: #f8fafc;
            border: 2px solid transparent;
            border-radius: 10px;
            margin-bottom: 8px;
            cursor: grab;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nf-palette-item:hover { border-color: #667eea; background: #fff; transform: translateX(3px); }
        .nf-palette-item.dragging { opacity: 0.5; transform: scale(0.95); }
        .nf-palette-item .item-icon { font-size: 20px; }
        .nf-palette-item .item-info { flex: 1; }
        .nf-palette-item .item-label { font-size: 13px; font-weight: 600; color: #1f2937; }
        .nf-palette-item .item-desc { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .nf-palette-item[data-type="trigger"] { border-left: 3px solid #667eea; }
        .nf-palette-item[data-type="action"] { border-left: 3px solid #f5576c; }

        /* 캔버스 */
        .nf-canvas {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            position: relative;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }
        .nf-canvas.drag-over { background: #f0f4ff; border: 2px dashed #667eea; }
        .nf-canvas-empty { text-align: center; color: #9ca3af; padding: 60px 20px; }
        .nf-canvas-empty .icon { font-size: 60px; margin-bottom: 20px; opacity: 0.5; }
        .nf-canvas-empty h3 { font-size: 18px; color: #374151; margin-bottom: 10px; }
        .nf-canvas-empty p { font-size: 14px; line-height: 1.6; }

        /* 캔버스 노드 */
        .nf-node {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            overflow: hidden;
            transition: all 0.2s;
        }
        .nf-node:hover { border-color: #667eea; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .nf-node.selected { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2); }
        .nf-node-header {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }
        .nf-node-header .node-icon { font-size: 24px; }
        .nf-node-header .node-info { flex: 1; }
        .nf-node-header .node-type { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .nf-node-header .node-label { font-size: 15px; font-weight: 600; color: #1f2937; }
        .nf-node-header .node-remove {
            width: 28px; height: 28px;
            border-radius: 6px;
            border: none;
            background: #fee2e2;
            color: #ef4444;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .nf-node-header .node-remove:hover { background: #ef4444; color: #fff; }
        .nf-node.trigger-node .nf-node-header { background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); }
        .nf-node.trigger-node .nf-node-header .node-type { color: #667eea; }
        .nf-node.action-node .nf-node-header { background: linear-gradient(135deg, #fef2f2 0%, #fce7f3 100%); }
        .nf-node.action-node .nf-node-header .node-type { color: #f5576c; }

        /* 연결선 */
        .nf-connector {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .nf-connector::before {
            content: '';
            position: absolute;
            width: 3px;
            height: 100%;
            background: linear-gradient(to bottom, #667eea, #f5576c);
            border-radius: 3px;
        }
        .nf-connector .connector-icon {
            width: 30px;
            height: 30px;
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            z-index: 1;
        }

        /* 우측 설정 패널 */
        .nf-settings {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
        }
        .nf-settings-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nf-settings-header .settings-icon { font-size: 24px; }
        .nf-settings-header h3 { margin: 0; font-size: 16px; color: #1f2937; }
        .nf-settings-body { padding: 20px; flex: 1; overflow-y: auto; max-height: calc(100vh - 300px); }
        .nf-settings-empty { text-align: center; color: #9ca3af; padding: 40px 20px; }
        .nf-settings-empty .icon { font-size: 40px; margin-bottom: 15px; opacity: 0.5; }

        .nf-field { margin-bottom: 20px; }
        .nf-field label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }
        .nf-field input[type="text"],
        .nf-field input[type="number"],
        .nf-field input[type="url"],
        .nf-field select,
        .nf-field textarea {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .nf-field input:focus, .nf-field select:focus, .nf-field textarea:focus {
            border-color: #667eea;
            outline: none;
        }
        .nf-field textarea { min-height: 80px; resize: vertical; }
        .nf-field input[type="color"] { height: 40px; padding: 5px; }
        .nf-field .description { font-size: 12px; color: #6b7280; margin-top: 5px; }
        .nf-field .checkbox-label { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .nf-field .checkbox-label input[type="checkbox"] { width: 18px; height: 18px; accent-color: #667eea; }

        /* 드롭존 표시 */
        .nf-dropzone {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            color: #9ca3af;
            transition: all 0.2s;
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .nf-dropzone.active { border-color: #667eea; background: #f0f4ff; color: #667eea; }
        .nf-dropzone.trigger-zone.has-node, .nf-dropzone.action-zone.has-node { display: none; }
        </style>

        <div class="wrap nf-builder-wrap">
            <!-- 헤더 -->
            <div class="nf-builder-header">
                <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-flow-workflows' ); ?>" class="nf-btn nf-btn-secondary">
                    ← <?php esc_html_e( '목록', 'acf-nudge-flow' ); ?>
                </a>
                <input type="text"
                       id="workflow-name"
                       value="<?php echo $preset_data ? esc_attr( $preset_data['title'] ) : ( $workflow_id ? get_the_title( $workflow_id ) : '' ); ?>"
                       placeholder="<?php esc_attr_e( '워크플로우 이름 입력...', 'acf-nudge-flow' ); ?>">
                <div class="nf-toggle">
                    <input type="checkbox" id="workflow-enabled" <?php checked( $workflow_data['enabled'] ); ?>>
                    <label for="workflow-enabled"><?php esc_html_e( '활성화', 'acf-nudge-flow' ); ?></label>
                </div>
                <button type="button" class="nf-btn nf-btn-primary" id="save-workflow">
                    <?php esc_html_e( '💾 저장', 'acf-nudge-flow' ); ?>
                </button>
                <?php if ( $preset_data ) : ?>
                    <span style="color: #10b981; font-size: 13px;">✓ <?php echo esc_html( $preset_data['title'] ); ?></span>
                <?php endif; ?>
            </div>

            <!-- 메인 빌더 영역 -->
            <div class="nf-builder-main">
                <!-- 좌측: 팔레트 -->
                <div class="nf-palette">
                    <!-- 트리거 팔레트 -->
                    <div class="nf-palette-section">
                        <h3><span class="icon">⚡</span> <?php esc_html_e( '트리거 (IF)', 'acf-nudge-flow' ); ?></h3>
                        <?php foreach ( $trigger_categories as $cat => $cat_triggers ) : ?>
                            <div class="nf-palette-category"><?php echo esc_html( $category_labels[ $cat ] ?? ucfirst( $cat ) ); ?></div>
                            <?php foreach ( $cat_triggers as $id => $trigger ) : ?>
                                <div class="nf-palette-item"
                                     data-type="trigger"
                                     data-id="<?php echo esc_attr( $id ); ?>"
                                     data-fields='<?php echo esc_attr( wp_json_encode( $trigger['fields'] ?? array() ) ); ?>'
                                     draggable="true">
                                    <span class="item-icon"><?php echo esc_html( $trigger['icon'] ?? '⚡' ); ?></span>
                                    <div class="item-info">
                                        <div class="item-label"><?php echo esc_html( $trigger['label'] ?? $id ); ?></div>
                                        <div class="item-desc"><?php echo esc_html( mb_strimwidth( $trigger['description'] ?? '', 0, 40, '...' ) ); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- 액션 팔레트 -->
                    <div class="nf-palette-section">
                        <h3><span class="icon">🎯</span> <?php esc_html_e( '액션 (DO)', 'acf-nudge-flow' ); ?></h3>
                        <?php foreach ( $action_categories as $cat => $cat_actions ) : ?>
                            <div class="nf-palette-category"><?php echo esc_html( $category_labels[ $cat ] ?? ucfirst( $cat ) ); ?></div>
                            <?php foreach ( $cat_actions as $id => $action ) : ?>
                                <div class="nf-palette-item"
                                     data-type="action"
                                     data-id="<?php echo esc_attr( $id ); ?>"
                                     data-fields='<?php echo esc_attr( wp_json_encode( $action['fields'] ?? array() ) ); ?>'
                                     draggable="true">
                                    <span class="item-icon"><?php echo esc_html( $action['icon'] ?? '🎯' ); ?></span>
                                    <div class="item-info">
                                        <div class="item-label"><?php echo esc_html( $action['label'] ?? $id ); ?></div>
                                        <div class="item-desc"><?php echo esc_html( mb_strimwidth( $action['description'] ?? '', 0, 40, '...' ) ); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 중앙: 캔버스 -->
                <div class="nf-canvas" id="workflow-canvas">
                    <?php if ( empty( $workflow_data['trigger_id'] ) && empty( $workflow_data['action_id'] ) ) : ?>
                        <div class="nf-canvas-empty" id="canvas-empty">
                            <div class="icon">🚀</div>
                            <h3><?php esc_html_e( '워크플로우를 만들어보세요', 'acf-nudge-flow' ); ?></h3>
                            <p><?php esc_html_e( '왼쪽 패널에서 트리거와 액션을 드래그하여<br>자동화 워크플로우를 구성하세요.', 'acf-nudge-flow' ); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- 트리거 드롭존/노드 -->
                    <div class="nf-dropzone trigger-zone <?php echo ! empty( $workflow_data['trigger_id'] ) ? 'has-node' : ''; ?>"
                         data-accept="trigger" id="trigger-dropzone">
                        <?php esc_html_e( '⚡ 트리거를 여기에 드롭하세요', 'acf-nudge-flow' ); ?>
                    </div>

                    <?php if ( ! empty( $workflow_data['trigger_id'] ) && isset( $triggers[ $workflow_data['trigger_id'] ] ) ) :
                        $t = $triggers[ $workflow_data['trigger_id'] ];
                    ?>
                        <div class="nf-node trigger-node" data-type="trigger" data-id="<?php echo esc_attr( $workflow_data['trigger_id'] ); ?>">
                            <div class="nf-node-header">
                                <span class="node-icon"><?php echo esc_html( $t['icon'] ?? '⚡' ); ?></span>
                                <div class="node-info">
                                    <div class="node-type"><?php esc_html_e( 'IF (트리거)', 'acf-nudge-flow' ); ?></div>
                                    <div class="node-label"><?php echo esc_html( $t['label'] ?? $workflow_data['trigger_id'] ); ?></div>
                                </div>
                                <button type="button" class="node-remove" data-target="trigger">✕</button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 연결선 -->
                    <div class="nf-connector" id="workflow-connector" style="<?php echo ( empty( $workflow_data['trigger_id'] ) || empty( $workflow_data['action_id'] ) ) ? 'display:none;' : ''; ?>">
                        <span class="connector-icon">→</span>
                    </div>

                    <!-- 액션 드롭존/노드 -->
                    <div class="nf-dropzone action-zone <?php echo ! empty( $workflow_data['action_id'] ) ? 'has-node' : ''; ?>"
                         data-accept="action" id="action-dropzone">
                        <?php esc_html_e( '🎯 액션을 여기에 드롭하세요', 'acf-nudge-flow' ); ?>
                    </div>

                    <?php if ( ! empty( $workflow_data['action_id'] ) && isset( $actions[ $workflow_data['action_id'] ] ) ) :
                        $a = $actions[ $workflow_data['action_id'] ];
                    ?>
                        <div class="nf-node action-node" data-type="action" data-id="<?php echo esc_attr( $workflow_data['action_id'] ); ?>">
                            <div class="nf-node-header">
                                <span class="node-icon"><?php echo esc_html( $a['icon'] ?? '🎯' ); ?></span>
                                <div class="node-info">
                                    <div class="node-type"><?php esc_html_e( 'DO (액션)', 'acf-nudge-flow' ); ?></div>
                                    <div class="node-label"><?php echo esc_html( $a['label'] ?? $workflow_data['action_id'] ); ?></div>
                                </div>
                                <button type="button" class="node-remove" data-target="action">✕</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 우측: 설정 패널 -->
                <div class="nf-settings">
                    <div class="nf-settings-header">
                        <span class="settings-icon">⚙️</span>
                        <h3 id="settings-title"><?php esc_html_e( '설정', 'acf-nudge-flow' ); ?></h3>
                    </div>
                    <div class="nf-settings-body" id="settings-body">
                        <div class="nf-settings-empty">
                            <div class="icon">👆</div>
                            <p><?php esc_html_e( '캔버스에서 노드를 선택하면<br>설정 옵션이 표시됩니다.', 'acf-nudge-flow' ); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        // [v23.0.0] 드래그 앤 드롭 워크플로우 빌더
        jQuery(document).ready(function($) {
            var triggersData = <?php echo wp_json_encode( $triggers ); ?>;
            var actionsData = <?php echo wp_json_encode( $actions ); ?>;
            var workflowId = <?php echo $workflow_id; ?>;

            // 현재 워크플로우 상태
            var workflowState = {
                trigger: '<?php echo esc_js( $workflow_data['trigger_id'] ); ?>',
                triggerSettings: <?php echo wp_json_encode( $workflow_data['trigger_settings'] ); ?>,
                action: '<?php echo esc_js( $workflow_data['action_id'] ); ?>',
                actionSettings: <?php echo wp_json_encode( $workflow_data['action_settings'] ); ?>,
                selectedNode: null
            };

            // ========================
            // 드래그 앤 드롭 핸들러
            // ========================

            // 팔레트 아이템 드래그 시작
            $('.nf-palette-item').on('dragstart', function(e) {
                var $item = $(this);
                $item.addClass('dragging');
                e.originalEvent.dataTransfer.setData('text/plain', JSON.stringify({
                    type: $item.data('type'),
                    id: $item.data('id'),
                    fields: $item.data('fields')
                }));
                e.originalEvent.dataTransfer.effectAllowed = 'copy';
            });

            $('.nf-palette-item').on('dragend', function() {
                $(this).removeClass('dragging');
                $('.nf-dropzone').removeClass('active');
                $('#workflow-canvas').removeClass('drag-over');
            });

            // 드롭존 드래그오버
            $('.nf-dropzone, #workflow-canvas').on('dragover', function(e) {
                e.preventDefault();
                e.originalEvent.dataTransfer.dropEffect = 'copy';

                var $target = $(this);
                if ($target.hasClass('nf-dropzone')) {
                    $target.addClass('active');
                } else {
                    $target.addClass('drag-over');
                }
            });

            $('.nf-dropzone, #workflow-canvas').on('dragleave', function(e) {
                $(this).removeClass('active drag-over');
            });

            // 드롭 처리
            $('.nf-dropzone, #workflow-canvas').on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('active drag-over');

                var data;
                try {
                    data = JSON.parse(e.originalEvent.dataTransfer.getData('text/plain'));
                } catch (err) {
                    return;
                }

                var $target = $(this);
                var acceptType = $target.data('accept');

                // 캔버스에 드롭된 경우 타입에 따라 적절한 영역에 배치
                if ($target.attr('id') === 'workflow-canvas') {
                    acceptType = data.type;
                }

                if (acceptType && acceptType !== data.type) {
                    alert('<?php echo esc_js( __( '올바른 영역에 드롭해주세요. 트리거는 트리거 영역에, 액션은 액션 영역에 드롭하세요.', 'acf-nudge-flow' ) ); ?>');
                    return;
                }

                addNodeToCanvas(data.type, data.id, data.fields);
            });

            // ========================
            // 캔버스 노드 관리
            // ========================

            function addNodeToCanvas(type, id, fields) {
                var itemData = type === 'trigger' ? triggersData[id] : actionsData[id];
                if (!itemData) return;

                // 기존 노드 제거
                if (type === 'trigger') {
                    $('.nf-node.trigger-node').remove();
                    workflowState.trigger = id;
                    workflowState.triggerSettings = {};
                    $('#trigger-dropzone').addClass('has-node');
                } else {
                    $('.nf-node.action-node').remove();
                    workflowState.action = id;
                    workflowState.actionSettings = {};
                    $('#action-dropzone').addClass('has-node');
                }

                // 새 노드 생성
                var nodeClass = type === 'trigger' ? 'trigger-node' : 'action-node';
                var nodeTypeLabel = type === 'trigger' ? '<?php echo esc_js( __( 'IF (트리거)', 'acf-nudge-flow' ) ); ?>' : '<?php echo esc_js( __( 'DO (액션)', 'acf-nudge-flow' ) ); ?>';

                var $node = $('<div class="nf-node ' + nodeClass + '" data-type="' + type + '" data-id="' + id + '">' +
                    '<div class="nf-node-header">' +
                        '<span class="node-icon">' + (itemData.icon || (type === 'trigger' ? '⚡' : '🎯')) + '</span>' +
                        '<div class="node-info">' +
                            '<div class="node-type">' + nodeTypeLabel + '</div>' +
                            '<div class="node-label">' + (itemData.label || id) + '</div>' +
                        '</div>' +
                        '<button type="button" class="node-remove" data-target="' + type + '">✕</button>' +
                    '</div>' +
                '</div>');

                // 적절한 위치에 삽입
                if (type === 'trigger') {
                    $('#trigger-dropzone').after($node);
                } else {
                    var $connector = $('#workflow-connector');
                    if ($connector.length) {
                        $connector.after($node);
                    } else {
                        $('#action-dropzone').after($node);
                    }
                }

                // 빈 상태 숨기기
                $('#canvas-empty').hide();

                // 연결선 표시
                updateConnector();

                // 새 노드 선택
                selectNode($node);
            }

            function removeNode(type) {
                if (type === 'trigger') {
                    $('.nf-node.trigger-node').remove();
                    workflowState.trigger = '';
                    workflowState.triggerSettings = {};
                    $('#trigger-dropzone').removeClass('has-node');
                } else {
                    $('.nf-node.action-node').remove();
                    workflowState.action = '';
                    workflowState.actionSettings = {};
                    $('#action-dropzone').removeClass('has-node');
                }

                updateConnector();
                clearSettings();

                // 빈 상태 표시
                if (!workflowState.trigger && !workflowState.action) {
                    $('#canvas-empty').show();
                }
            }

            function updateConnector() {
                if (workflowState.trigger && workflowState.action) {
                    $('#workflow-connector').show();
                } else {
                    $('#workflow-connector').hide();
                }
            }

            // ========================
            // 노드 선택 및 설정 패널
            // ========================

            $(document).on('click', '.nf-node', function(e) {
                if ($(e.target).hasClass('node-remove')) return;
                selectNode($(this));
            });

            $(document).on('click', '.node-remove', function(e) {
                e.stopPropagation();
                var type = $(this).data('target');
                if (confirm('<?php echo esc_js( __( '이 노드를 삭제하시겠습니까?', 'acf-nudge-flow' ) ); ?>')) {
                    removeNode(type);
                }
            });

            function selectNode($node) {
                $('.nf-node').removeClass('selected');
                $node.addClass('selected');

                var type = $node.data('type');
                var id = $node.data('id');
                workflowState.selectedNode = { type: type, id: id };

                renderSettings(type, id);
            }

            function clearSettings() {
                workflowState.selectedNode = null;
                $('#settings-title').text('<?php echo esc_js( __( '설정', 'acf-nudge-flow' ) ); ?>');
                $('#settings-body').html(
                    '<div class="nf-settings-empty">' +
                        '<div class="icon">👆</div>' +
                        '<p><?php echo esc_js( __( '캔버스에서 노드를 선택하면<br>설정 옵션이 표시됩니다.', 'acf-nudge-flow' ) ); ?></p>' +
                    '</div>'
                );
            }

            function renderSettings(type, id) {
                var data = type === 'trigger' ? triggersData[id] : actionsData[id];
                var settings = type === 'trigger' ? workflowState.triggerSettings : workflowState.actionSettings;

                if (!data) return;

                $('#settings-title').html((data.icon || '') + ' ' + (data.label || id));

                var fields = data.fields || {};
                var html = '';

                if (Object.keys(fields).length === 0) {
                    html = '<div class="nf-settings-empty">' +
                        '<div class="icon">✓</div>' +
                        '<p><?php echo esc_js( __( '이 항목은 추가 설정이 필요하지 않습니다.', 'acf-nudge-flow' ) ); ?></p>' +
                    '</div>';
                } else {
                    for (var fieldId in fields) {
                        var field = fields[fieldId];
                        var value = settings[fieldId] !== undefined ? settings[fieldId] : (field.default || '');

                        html += '<div class="nf-field">';
                        html += '<label>' + (field.label || fieldId) + '</label>';

                        if (field.type === 'select') {
                            html += '<select data-field="' + fieldId + '" data-node-type="' + type + '">';
                            for (var optValue in field.options) {
                                var selected = optValue == value ? ' selected' : '';
                                html += '<option value="' + optValue + '"' + selected + '>' + field.options[optValue] + '</option>';
                            }
                            html += '</select>';
                        } else if (field.type === 'number') {
                            html += '<input type="number" data-field="' + fieldId + '" data-node-type="' + type + '" value="' + value + '"';
                            if (field.min !== undefined) html += ' min="' + field.min + '"';
                            if (field.max !== undefined) html += ' max="' + field.max + '"';
                            html += '>';
                        } else if (field.type === 'textarea' || field.type === 'wysiwyg') {
                            html += '<textarea data-field="' + fieldId + '" data-node-type="' + type + '">' + value + '</textarea>';
                        } else if (field.type === 'checkbox') {
                            var checked = value === '1' || value === true ? ' checked' : '';
                            html += '<label class="checkbox-label">';
                            html += '<input type="checkbox" data-field="' + fieldId + '" data-node-type="' + type + '" value="1"' + checked + '>';
                            html += (field.description || '');
                            html += '</label>';
                        } else if (field.type === 'color') {
                            html += '<input type="color" data-field="' + fieldId + '" data-node-type="' + type + '" value="' + (value || '#000000') + '">';
                        } else {
                            html += '<input type="text" data-field="' + fieldId + '" data-node-type="' + type + '" value="' + value + '"';
                            if (field.placeholder) html += ' placeholder="' + field.placeholder + '"';
                            html += '>';
                        }

                        if (field.description && field.type !== 'checkbox') {
                            html += '<p class="description">' + field.description + '</p>';
                        }

                        html += '</div>';
                    }
                }

                $('#settings-body').html(html);
            }

            // 설정 변경 시 자동 저장
            $(document).on('change input', '#settings-body input, #settings-body select, #settings-body textarea', function() {
                var $input = $(this);
                var fieldId = $input.data('field');
                var nodeType = $input.data('node-type');
                var value;

                if ($input.attr('type') === 'checkbox') {
                    value = $input.is(':checked') ? '1' : '0';
                } else {
                    value = $input.val();
                }

                if (nodeType === 'trigger') {
                    workflowState.triggerSettings[fieldId] = value;
                } else {
                    workflowState.actionSettings[fieldId] = value;
                }
            });

            // ========================
            // 저장
            // ========================

            $('#save-workflow').on('click', function() {
                var workflowName = $('#workflow-name').val();
                if (!workflowName) {
                    alert('<?php echo esc_js( __( '워크플로우 이름을 입력해주세요.', 'acf-nudge-flow' ) ); ?>');
                    $('#workflow-name').focus();
                    return;
                }

                if (!workflowState.trigger || !workflowState.action) {
                    alert('<?php echo esc_js( __( '트리거와 액션을 모두 추가해주세요.', 'acf-nudge-flow' ) ); ?>');
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).text('<?php echo esc_js( __( '저장 중...', 'acf-nudge-flow' ) ); ?>');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'acf_nudge_save_workflow',
                        nonce: '<?php echo wp_create_nonce( "acf_nudge_flow_nonce" ); ?>',
                        workflow_id: workflowId,
                        data: JSON.stringify({
                            title: workflowName,
                            enabled: $('#workflow-enabled').is(':checked'),
                            trigger: workflowState.trigger,
                            trigger_settings: workflowState.triggerSettings,
                            action: workflowState.action,
                            action_settings: workflowState.actionSettings
                        })
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('<?php echo esc_js( __( '워크플로우가 저장되었습니다.', 'acf-nudge-flow' ) ); ?>');
                            if (response.data && response.data.id && !workflowId) {
                                window.location.href = '<?php echo admin_url( 'admin.php?page=acf-nudge-flow-builder&id=' ); ?>' + response.data.id;
                            }
                        } else {
                            alert('<?php echo esc_js( __( '저장 중 오류가 발생했습니다.', 'acf-nudge-flow' ) ); ?>');
                        }
                    },
                    error: function() {
                        alert('<?php echo esc_js( __( '서버 통신 오류가 발생했습니다.', 'acf-nudge-flow' ) ); ?>');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('💾 <?php echo esc_js( __( '저장', 'acf-nudge-flow' ) ); ?>');
                    }
                });
            });

            // ========================
            // 초기화
            // ========================

            // 기존 데이터가 있으면 연결선 표시
            updateConnector();

            // 기존 노드가 있으면 빈 상태 숨기기
            if (workflowState.trigger || workflowState.action) {
                $('#canvas-empty').hide();
            }

            // 기존 노드가 있으면 첫 번째 노드 선택
            var $firstNode = $('.nf-node').first();
            if ($firstNode.length) {
                selectNode($firstNode);
            }
        });
        </script>
        <?php
    }

    /**
     * 넛지 템플릿 페이지 렌더링
     * [v22.4.6] 프리셋 템플릿 목록 표시
     */
    public function render_nudge_templates() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-nudge-flow' ) );
        }
        
        // 프리셋 템플릿을 템플릿 CPT에 자동 생성 (없는 경우)
        $this->ensure_preset_templates_internal();
        
        $templates = get_posts( array(
            'post_type'      => 'acf_nudge_template',
            'posts_per_page' => -1,
            'post_status'    => 'any',
        ) );
        ?>
        <div class="wrap acf-nudge-flow-admin">
            <h1>
                <?php esc_html_e( '넛지 템플릿', 'acf-nudge-flow' ); ?>
                <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-template-center' ); ?>" class="page-title-action">
                    <?php esc_html_e( '템플릿 센터에서 더 보기', 'acf-nudge-flow' ); ?>
                </a>
            </h1>
            
            <div class="notice notice-info">
                <p><?php esc_html_e( '프리셋 템플릿을 선택하여 워크플로우를 빠르게 생성할 수 있습니다.', 'acf-nudge-flow' ); ?></p>
            </div>
            
            <?php if ( empty( $templates ) ) : ?>
                <div class="notice notice-warning">
                    <p><?php esc_html_e( '템플릿이 없습니다. 템플릿 센터에서 프리셋을 설치하세요.', 'acf-nudge-flow' ); ?></p>
                </div>
            <?php else : ?>
                <div class="acf-nudge-template-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 20px;">
                    <?php foreach ( $templates as $template ) : 
                        $preset_id = get_post_meta( $template->ID, '_preset_id', true );
                        $presets = $this->get_preset_templates();
                        $preset_data = isset( $presets[ $preset_id ] ) ? $presets[ $preset_id ] : null;
                    ?>
                        <div class="postbox" style="border-radius: 8px; overflow: hidden;">
                            <div style="padding: 20px; background: #f8fafc; border-bottom: 1px solid #eee; text-align: center;">
                                <span class="dashicons <?php echo esc_attr( $preset_data['icon'] ?? 'dashicons-admin-generic' ); ?>" 
                                      style="font-size: 40px; width: 40px; height: 40px; color: #667eea;"></span>
                            </div>
                            <div style="padding: 15px;">
                                <h3 style="margin: 0 0 10px; font-size: 16px;">
                                    <?php echo esc_html( $template->post_title ); ?>
                                </h3>
                                <p style="font-size: 12px; color: #666; height: 60px; overflow: hidden;">
                                    <?php echo esc_html( $template->post_content ?: ( $preset_data['description'] ?? '' ) ); ?>
                                </p>
                                <div style="margin-top: 15px;">
                                    <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-flow-builder&template=' . esc_attr( $preset_id ) ); ?>" 
                                       class="button button-primary">
                                        <?php esc_html_e( '워크플로우 생성', 'acf-nudge-flow' ); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * 프리셋 템플릿을 템플릿 CPT에 자동 생성
     * [v22.4.6] 초기화 시 프리셋 템플릿 생성
     * @access private (내부 호출용)
     */
    private function ensure_preset_templates_internal() {
        $presets = $this->get_preset_templates();
        
        foreach ( $presets as $preset_id => $preset_data ) {
            // 이미 존재하는지 확인
            $existing = get_posts( array(
                'post_type'      => 'acf_nudge_template',
                'meta_key'       => '_preset_id',
                'meta_value'     => $preset_id,
                'posts_per_page' => 1,
                'post_status'    => 'any',
            ) );
            
            if ( empty( $existing ) ) {
                // 템플릿 생성
                $post_id = wp_insert_post( array(
                    'post_title'   => $preset_data['title'],
                    'post_content' => $preset_data['description'],
                    'post_type'    => 'acf_nudge_template',
                    'post_status'  => 'publish',
                ) );
                
                if ( $post_id && ! is_wp_error( $post_id ) ) {
                    update_post_meta( $post_id, '_preset_id', $preset_id );
                    update_post_meta( $post_id, '_preset_type', $preset_data['type'] ?? 'free' );
                    update_post_meta( $post_id, '_preset_category', $preset_data['category'] ?? 'general' );
                }
            }
        }
    }

    /**
     * 분석 페이지 렌더링
     */
    public function render_analytics() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-nudge-flow' ) );
        }
        ?>


        <div class="wrap acf-nudge-flow-admin">
            <h1><?php esc_html_e( '📈 분석 통계', 'acf-nudge-flow' ); ?></h1>
            
            <div class="acf-nudge-stats-grid" style="margin-top: 30px;">
                <div class="acf-nudge-stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $this->get_total_workflows(); ?></div>
                        <div class="stat-label"><?php esc_html_e( '활성 워크플로우', 'acf-nudge-flow' ); ?></div>
                    </div>
                </div>
                
                <div class="acf-nudge-stat-card">
                    <div class="stat-icon">👁️</div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $this->get_total_impressions(); ?></div>
                        <div class="stat-label"><?php esc_html_e( '오늘 노출', 'acf-nudge-flow' ); ?></div>
                    </div>
                </div>
                
                <div class="acf-nudge-stat-card">
                    <div class="stat-icon">🎯</div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $this->get_conversion_rate(); ?>%</div>
                        <div class="stat-label"><?php esc_html_e( '전환율', 'acf-nudge-flow' ); ?></div>
                    </div>
                </div>
                
                <div class="acf-nudge-stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $this->get_unique_visitors(); ?></div>
                        <div class="stat-label"><?php esc_html_e( '이번 주 방문자', 'acf-nudge-flow' ); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="acf-nudge-chart-container" style="margin-top: 30px;">
                <h2><?php esc_html_e( '📈 7일 성과 추이', 'acf-nudge-flow' ); ?></h2>
                <canvas id="acf-nudge-analytics-chart" style="max-height: 300px;"></canvas>
            </div>
        </div>
        
        <script>
        // [v22.4.6] 분석 페이지 차트
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('acf-nudge-analytics-chart');
            if (!ctx || typeof Chart === 'undefined') return;
            
            const chartData = {
                labels: ['월', '화', '수', '목', '금', '토', '일'],
                datasets: [{
                    label: '노출수',
                    data: [120, 190, 300, 250, 200, 280, 310],
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: '전환수',
                    data: [12, 19, 24, 18, 22, 26, 31],
                    borderColor: 'rgb(67, 233, 123)',
                    backgroundColor: 'rgba(67, 233, 123, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            };
            
            new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
        </script>
        <?php
    }

    /**
     * [v22.7.0] 광고 픽셀 설정 페이지 렌더링
     */
    public function render_ad_pixels() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-nudge-flow' ) );
        }

        // 광고 픽셀 설정 페이지 로드
        include ACF_NUDGE_FLOW_PLUGIN_DIR . 'admin/views/ad-pixels-settings.php';
    }

    /**
     * [v22.8.0] 트래픽 소스 분석 대시보드 렌더링
     * [v22.10.0] User Journey Analytics 연동 페이지로 변경
     */
    public function render_traffic_analytics() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-nudge-flow' ) );
        }

        // [v22.10.0] User Journey Analytics 연동 페이지 로드
        include ACF_NUDGE_FLOW_PLUGIN_DIR . 'admin/views/traffic-analytics-integration.php';
    }

    public function render_settings() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-nudge-flow' ) );
        }

        if ( isset( $_POST['acf_nudge_settings_nonce'] ) &&


             wp_verify_nonce( $_POST['acf_nudge_settings_nonce'], 'acf_nudge_save_settings' ) ) {
            $this->save_settings();
        }

        $settings = get_option( 'acf_nudge_flow_settings', array() );
        ?>
        <div class="wrap acf-nudge-flow-admin">
            <h1><?php esc_html_e( '설정', 'acf-nudge-flow' ); ?></h1>
            
            <form method="post" action="">
                <?php wp_nonce_field( 'acf_nudge_save_settings', 'acf_nudge_settings_nonce' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( '플러그인 활성화', 'acf-nudge-flow' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enabled" value="1" <?php checked( $settings['enabled'] ?? true ); ?>>
                                <?php esc_html_e( '넛지 플로우 활성화', 'acf-nudge-flow' ); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php esc_html_e( '쿠키 유효 기간', 'acf-nudge-flow' ); ?></th>
                        <td>
                            <input type="number" name="cookie_duration" value="<?php echo esc_attr( $settings['cookie_duration'] ?? 30 ); ?>" min="1" max="365">
                            <?php esc_html_e( '일', 'acf-nudge-flow' ); ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php esc_html_e( '방문당 최대 넛지 수', 'acf-nudge-flow' ); ?></th>
                        <td>
                            <input type="number" name="max_nudges_per_visit" value="<?php echo esc_attr( $settings['max_nudges_per_visit'] ?? 3 ); ?>" min="1" max="10">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php esc_html_e( '넛지 간 지연 시간', 'acf-nudge-flow' ); ?></th>
                        <td>
                            <input type="number" name="delay_between_nudges" value="<?php echo esc_attr( $settings['delay_between_nudges'] ?? 60 ); ?>" min="0">
                            <?php esc_html_e( '초', 'acf-nudge-flow' ); ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php esc_html_e( 'MAB 자동 최적화', 'acf-nudge-flow' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="mab_enabled" value="1" <?php checked( $settings['mab_enabled'] ?? false ); ?>>
                                <?php esc_html_e( '넛지 성과 자동 학습 및 최적화 (Multi-Armed Bandit)', 'acf-nudge-flow' ); ?>
                            </label>
                            <p class="description">
                                <?php esc_html_e( '활성화 시, Thompson Sampling 알고리즘이 전환율이 높은 넛지를 자동으로 학습하고 더 자주 표시합니다.', 'acf-nudge-flow' ); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php esc_html_e( '제외 역할', 'acf-nudge-flow' ); ?></th>
                        <td>
                            <?php
                            $excluded_roles = $settings['excluded_roles'] ?? array( 'administrator' );
                            foreach ( wp_roles()->roles as $role_key => $role ) :
                            ?>
                                <label style="display: block; margin-bottom: 5px;">
                                    <input type="checkbox" name="excluded_roles[]" value="<?php echo esc_attr( $role_key ); ?>" 
                                           <?php checked( in_array( $role_key, $excluded_roles ) ); ?>>
                                    <?php echo esc_html( $role['name'] ); ?>
                                </label>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * 설정 저장
     */
    private function save_settings() {
        $settings = array(
            'enabled'              => isset( $_POST['enabled'] ),
            'cookie_duration'      => intval( $_POST['cookie_duration'] ?? 30 ),
            'max_nudges_per_visit' => intval( $_POST['max_nudges_per_visit'] ?? 3 ),
            'delay_between_nudges' => intval( $_POST['delay_between_nudges'] ?? 60 ),
            'excluded_roles'       => isset( $_POST['excluded_roles'] ) ? array_map( 'sanitize_text_field', $_POST['excluded_roles'] ) : array(),
            'mab_enabled'          => isset( $_POST['mab_enabled'] ),
        );

        update_option( 'acf_nudge_flow_settings', $settings );

        add_settings_error( 'acf_nudge_flow', 'settings_saved', __( '설정이 저장되었습니다.', 'acf-nudge-flow' ), 'success' );
    }

    // === 통계 헬퍼 메서드 ===

    private function get_total_workflows() {
        return wp_count_posts( 'acf_nudge_workflow' )->publish ?? 0;
    }

    private function get_total_impressions() {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nudge_events';
        return $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE event_type = 'impression' AND DATE(created_at) = CURDATE()" ) ?? 0;
    }

    private function get_conversion_rate() {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nudge_events';
        
        $impressions = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE event_type = 'impression'" );
        $conversions = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE event_type = 'conversion'" );
        
        if ( ! $impressions ) {
            return 0;
        }
        
        return round( ( $conversions / $impressions ) * 100, 1 );
    }

    private function get_unique_visitors() {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nudge_visitors';
        return $wpdb->get_var( "SELECT COUNT(DISTINCT visitor_id) FROM $table WHERE last_visit >= DATE_SUB(NOW(), INTERVAL 7 DAY)" ) ?? 0;
    }

    private function get_workflow_impressions( $workflow_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nudge_events';
        return $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE workflow_id = %d AND event_type = 'impression'",
            $workflow_id
        ) ) ?? 0;
    }

    private function get_workflow_conversions( $workflow_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nudge_events';
        return $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE workflow_id = %d AND event_type = 'conversion'",
            $workflow_id
        ) ) ?? 0;
    }

    // === AJAX 핸들러 ===

    public function ajax_save_workflow() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }

        $workflow_id = isset( $_POST['workflow_id'] ) ? intval( $_POST['workflow_id'] ) : 0;
        
        // [v22.5.0] IF-DO 방식: POST 데이터에서 직접 가져오기
        $data = array();
        if ( isset( $_POST['data'] ) ) {
            // JSON 문자열인 경우 파싱
            $decoded = json_decode( stripslashes( $_POST['data'] ), true );
            if ( json_last_error() === JSON_ERROR_NONE ) {
                $data = $decoded;
            }
        }
        
        // POST에서 직접 가져오기 (폼 제출 시)
        if ( empty( $data['title'] ) && isset( $_POST['workflow_name'] ) ) {
            $data['title'] = sanitize_text_field( $_POST['workflow_name'] );
        }
        if ( empty( $data['trigger'] ) && isset( $_POST['workflow_trigger'] ) ) {
            $data['trigger'] = sanitize_text_field( $_POST['workflow_trigger'] );
        }
        if ( empty( $data['trigger_settings'] ) && isset( $_POST['trigger_settings'] ) ) {
            $data['trigger_settings'] = array_map( 'sanitize_text_field', $_POST['trigger_settings'] );
        }
        if ( empty( $data['action'] ) && isset( $_POST['workflow_action'] ) ) {
            $data['action'] = sanitize_text_field( $_POST['workflow_action'] );
        }
        if ( empty( $data['action_settings'] ) && isset( $_POST['action_settings'] ) ) {
            $data['action_settings'] = array();
            foreach ( $_POST['action_settings'] as $key => $value ) {
                $data['action_settings'][ sanitize_text_field( $key ) ] = wp_kses_post( $value );
            }
        }
        if ( ! isset( $data['enabled'] ) && isset( $_POST['workflow_enabled'] ) ) {
            $data['enabled'] = $_POST['workflow_enabled'] === '1';
        }

        $manager = new ACF_Nudge_Workflow_Manager();
        $result = $manager->save( $workflow_id, $data );

        wp_send_json_success( array( 'id' => $result ) );
    }

    public function ajax_get_workflow() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $workflow_id = isset( $_GET['workflow_id'] ) ? intval( $_GET['workflow_id'] ) : 0;

        if ( ! $workflow_id ) {
            wp_send_json_error( 'Invalid workflow ID' );
        }

        $manager = new ACF_Nudge_Workflow_Manager();
        $workflow = $manager->get_by_id( $workflow_id );

        wp_send_json_success( $workflow );
    }

    public function ajax_get_triggers() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        if ( ! class_exists( 'ACF_Nudge_Trigger_Manager' ) ) {
            require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-trigger-manager.php';
        }
        
        $manager = new ACF_Nudge_Trigger_Manager();
        $triggers = $manager->get_all();
        
        // [v22.4.6] 빈 배열이 아닌 실제 데이터 반환 보장
        if ( empty( $triggers ) ) {
            // 기본 트리거가 없으면 에러 반환
            wp_send_json_error( array( 'message' => __( '트리거를 불러올 수 없습니다.', 'acf-nudge-flow' ) ) );
        }
        
        wp_send_json_success( $triggers );
    }

    public function ajax_get_actions() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        if ( ! class_exists( 'ACF_Nudge_Action_Manager' ) ) {
            require_once ACF_NUDGE_FLOW_PLUGIN_DIR . 'includes/class-action-manager.php';
        }
        
        $manager = new ACF_Nudge_Action_Manager();
        $actions = $manager->get_all();
        
        // [v22.4.6] 빈 배열이 아닌 실제 데이터 반환 보장
        if ( empty( $actions ) ) {
            // 기본 액션이 없으면 에러 반환
            wp_send_json_error( array( 'message' => __( '액션을 불러올 수 없습니다.', 'acf-nudge-flow' ) ) );
        }
        
        wp_send_json_success( $actions );
    }

    /**
     * [v22.9.1] 워드프레스 대시보드 위젯 등록
     */
    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'acf_nf_realtime_traffic_widget',
            '📊 실시간 트래픽 모니터링 - Nudge Flow',
            array( $this, 'render_dashboard_widget' )
        );
    }

    /**
     * [v22.9.1] 대시보드 위젯 렌더링
     */
    public function render_dashboard_widget() {
        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        // 오늘 통계
        $today_start = date( 'Y-m-d 00:00:00' );
        $today_end = date( 'Y-m-d 23:59:59' );

        $today_visitors = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT visitor_id) FROM $table WHERE first_touch_at BETWEEN %s AND %s",
            $today_start, $today_end
        ) ) ?: 0;

        $today_sessions = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE first_touch_at BETWEEN %s AND %s",
            $today_start, $today_end
        ) ) ?: 0;

        $today_conversions = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE converted = 1 AND conversion_at BETWEEN %s AND %s",
            $today_start, $today_end
        ) ) ?: 0;

        // 최근 5분 활성 사용자 (실시간)
        $five_min_ago = date( 'Y-m-d H:i:s', strtotime( '-5 minutes' ) );
        $realtime_users = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT visitor_id) FROM $table WHERE first_touch_at >= %s",
            $five_min_ago
        ) ) ?: 0;

        // 최근 유입 소스 TOP 5
        $top_sources = $wpdb->get_results( $wpdb->prepare(
            "SELECT
                COALESCE(NULLIF(detected_ad_platform, ''), NULLIF(referrer_name, ''), referrer_type) as source_name,
                COUNT(*) as count
            FROM $table
            WHERE first_touch_at BETWEEN %s AND %s
            GROUP BY source_name
            ORDER BY count DESC
            LIMIT 5",
            $today_start, $today_end
        ) );
        ?>
        <style>
            .nf-widget-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px; }
            .nf-widget-stat { background: #f8f9fa; border-radius: 8px; padding: 15px; text-align: center; }
            .nf-widget-stat.realtime { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; grid-column: span 2; }
            .nf-widget-stat-value { font-size: 28px; font-weight: 700; line-height: 1.2; }
            .nf-widget-stat-label { font-size: 12px; color: #6b7280; margin-top: 5px; }
            .nf-widget-stat.realtime .nf-widget-stat-label { color: rgba(255,255,255,0.8); }
            .nf-widget-realtime-dot { display: inline-block; width: 8px; height: 8px; background: #22c55e; border-radius: 50%; margin-right: 5px; animation: nf-pulse 2s infinite; }
            @keyframes nf-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
            .nf-widget-sources { margin-top: 15px; }
            .nf-widget-sources h4 { margin: 0 0 10px 0; font-size: 13px; color: #374151; }
            .nf-widget-source-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
            .nf-widget-source-item:last-child { border-bottom: none; }
            .nf-widget-source-name { color: #374151; }
            .nf-widget-source-count { font-weight: 600; color: #667eea; }
            .nf-widget-footer { margin-top: 15px; text-align: center; }
            .nf-widget-footer a { font-size: 13px; color: #667eea; text-decoration: none; }
            .nf-widget-footer a:hover { text-decoration: underline; }
        </style>

        <div class="nf-widget-wrap" id="nf-realtime-widget">
            <div class="nf-widget-stats">
                <div class="nf-widget-stat realtime">
                    <div class="nf-widget-stat-value">
                        <span class="nf-widget-realtime-dot"></span>
                        <span id="nf-realtime-count"><?php echo number_format( $realtime_users ); ?></span>
                    </div>
                    <div class="nf-widget-stat-label">실시간 활성 사용자 (5분)</div>
                </div>
                <div class="nf-widget-stat">
                    <div class="nf-widget-stat-value" id="nf-today-visitors"><?php echo number_format( $today_visitors ); ?></div>
                    <div class="nf-widget-stat-label">오늘 방문자</div>
                </div>
                <div class="nf-widget-stat">
                    <div class="nf-widget-stat-value" id="nf-today-sessions"><?php echo number_format( $today_sessions ); ?></div>
                    <div class="nf-widget-stat-label">오늘 세션</div>
                </div>
            </div>

            <div class="nf-widget-sources">
                <h4>오늘 유입 소스 TOP 5</h4>
                <div id="nf-top-sources">
                    <?php if ( ! empty( $top_sources ) ) : ?>
                        <?php foreach ( $top_sources as $source ) : ?>
                            <div class="nf-widget-source-item">
                                <span class="nf-widget-source-name"><?php echo esc_html( $source->source_name ?: '직접 방문' ); ?></span>
                                <span class="nf-widget-source-count"><?php echo number_format( $source->count ); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="nf-widget-source-item">
                            <span class="nf-widget-source-name" style="color: #9ca3af;">아직 오늘 트래픽 데이터가 없습니다.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="nf-widget-footer">
                <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-flow-traffic' ); ?>">
                    상세 트래픽 분석 보기 →
                </a>
            </div>
        </div>

        <script>
        (function() {
            // 30초마다 실시간 데이터 업데이트
            setInterval(function() {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', ajaxurl, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.success && response.data) {
                                document.getElementById('nf-realtime-count').textContent = response.data.realtime_users.toLocaleString();
                                document.getElementById('nf-today-visitors').textContent = response.data.today_visitors.toLocaleString();
                                document.getElementById('nf-today-sessions').textContent = response.data.today_sessions.toLocaleString();
                            }
                        } catch (e) {
                            console.log('Realtime update error:', e);
                        }
                    }
                };
                xhr.send('action=acf_nf_realtime_traffic&nonce=<?php echo wp_create_nonce( 'acf_nf_realtime' ); ?>');
            }, 30000);
        })();
        </script>
        <?php
    }

    /**
     * [v22.9.1] 실시간 트래픽 데이터 AJAX 핸들러
     */
    public function ajax_realtime_traffic() {
        if ( ! check_ajax_referer( 'acf_nf_realtime', 'nonce', false ) ) {
            wp_send_json_error( 'Invalid nonce' );
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'acf_nf_utm_tracking';

        $today_start = date( 'Y-m-d 00:00:00' );
        $today_end = date( 'Y-m-d 23:59:59' );
        $five_min_ago = date( 'Y-m-d H:i:s', strtotime( '-5 minutes' ) );

        $realtime_users = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT visitor_id) FROM $table WHERE first_touch_at >= %s",
            $five_min_ago
        ) ) ?: 0;

        $today_visitors = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT visitor_id) FROM $table WHERE first_touch_at BETWEEN %s AND %s",
            $today_start, $today_end
        ) ) ?: 0;

        $today_sessions = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE first_touch_at BETWEEN %s AND %s",
            $today_start, $today_end
        ) ) ?: 0;

        wp_send_json_success( array(
            'realtime_users' => (int) $realtime_users,
            'today_visitors' => (int) $today_visitors,
            'today_sessions' => (int) $today_sessions,
        ) );
    }
} // End of class ACF_Nudge_Flow_Admin

} // End of class_exists check

// 관리자 인스턴스 생성
if ( class_exists( 'ACF_Nudge_Flow_Admin' ) ) {
    new ACF_Nudge_Flow_Admin();
}

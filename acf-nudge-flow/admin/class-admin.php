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
 */
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
        
        // 빌더 UI 템플릿 출력
        add_action( 'admin_footer', array( $this, 'output_builder_templates' ) );
    }
    
    /**
     * [v22.3.1] Enqueue Chart.js and dashboard visualization assets
     */
    public function enqueue_dashboard_assets( $hook ) {
        // Only load on our dashboard pages
        if ( strpos( $hook, 'acf-nudge-flow' ) === false ) {
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
     */
    public function output_builder_templates() {
        $screen = get_current_screen();
        if ( ! $screen || 'acf_nudge_workflow' !== $screen->post_type ) {
            return;
        }
        ?>
        <div id="acf-nudge-builder-sidebar-source" style="display:none;">
            <div class="acf-builder-panel">
                <h3><?php esc_html_e( '⚡ 트리거 (Triggers)', 'acf-nudge-flow' ); ?></h3>
                <div class="acf-draggable-item" data-type="trigger" data-id="first_visit"><?php esc_html_e( '첫 방문', 'acf-nudge-flow' ); ?></div>
                <div class="acf-draggable-item" data-type="trigger" data-id="visit_count"><?php esc_html_e( '방문 횟수', 'acf-nudge-flow' ); ?></div>
                <div class="acf-draggable-item" data-type="trigger" data-id="exit_intent"><?php esc_html_e( '이탈 감지', 'acf-nudge-flow' ); ?></div>
                
                <h3><?php esc_html_e( '🎯 액션 (Actions)', 'acf-nudge-flow' ); ?></h3>
                <div class="acf-draggable-item" data-type="action" data-id="popup"><?php esc_html_e( '팝업 노출', 'acf-nudge-flow' ); ?></div>
                <div class="acf-draggable-item" data-type="action" data-id="toast"><?php esc_html_e( '토스트 알림', 'acf-nudge-flow' ); ?></div>
                <div class="acf-draggable-item" data-type="action" data-id="coupon"><?php esc_html_e( '쿠폰 지급', 'acf-nudge-flow' ); ?></div>
            </div>
        </div>
        <?php
    }

    /**
     * 관리자 메뉴 추가
     * [v21.0.0] WooCommerce '마케팅' 메뉴 하위 배치 및 서브메뉴 구조화
     */
    public function add_admin_menu() {
        $parent_slug = 'woocommerce-marketing'; // WooCommerce 마케팅 메뉴 슬러그
        $capability  = 'manage_options';

        // 최상위 메뉴 (마케팅 메뉴 하위로 배치 시도)
        add_menu_page(
            __( '넛지 플로우', 'acf-nudge-flow' ),
            __( '넛지 플로우 🚀', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-flow',
            array( $this, 'render_dashboard' ),
            'dashicons-chart-area',
            58 // WooCommerce Marketing (58) 인근
        );

        // (1) 대시보드
        add_submenu_page(
            'acf-nudge-flow',
            __( '대시보드', 'acf-nudge-flow' ),
            __( '📊 대시보드', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-flow',
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

        // (3) 분석
        add_submenu_page(
            'acf-nudge-flow',
            __( '분석', 'acf-nudge-flow' ),
            __( '📈 분석 통계', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-flow-analytics',
            array( $this, 'render_analytics' )
        );

        // (4) 템플릿 센터 (전략적 프리셋)
        add_submenu_page(
            'acf-nudge-flow',
            __( '템플릿 센터', 'acf-nudge-flow' ),
            __( '🎁 템플릿 센터', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-templates',
            array( $this, 'render_template_center' )
        );

        // (5) 설정
        add_submenu_page(
            'acf-nudge-flow',
            __( '설정', 'acf-nudge-flow' ),
            __( '⚙️ 설정', 'acf-nudge-flow' ),
            $capability,
            'acf-nudge-flow-settings',
            array( $this, 'render_settings' )
        );

        // (6) 빌더 (숨김 메뉴)
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
     */
    public function get_preset_templates() {
        return array(
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
                'price'       => '₩19,000',
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
                'price'       => '₩25,000',
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
                'price'       => '₩29,000',
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
    }

    /**
     * 템플릿 센터 렌더링
     */
    public function render_template_center() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-nudge-flow' ) );
        }
        $presets = $this->get_preset_templates();
        ?>


        <div class="wrap acf-nudge-flow-admin">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h1><?php esc_html_e( '🎁 전략적 넛지 템플릿 센터', 'acf-nudge-flow' ); ?></h1>
                <button class="button button-primary" style="background:#6366f1; border-color:#4f46e5;">
                    <?php esc_html_e( '내 시나리오 판매 등록', 'acf-nudge-flow' ); ?>
                </button>
            </div>

            <div class="notice notice-info">
                <p><?php esc_html_e( '개인화 마케팅 보고서 기반의 고효율 프리셋을 즉시 설치할 수 있습니다. 설치된 템플릿은 워크플로우 메뉴에서 확인 가능합니다.', 'acf-nudge-flow' ); ?></p>
            </div>

            <div class="acf-nudge-market-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px; margin-top:20px;">
                <?php foreach ( $presets as $id => $data ) : 
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
                        <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-flow-builder&template=welcome_popup' ); ?>" class="quick-action-card">
                            <span class="icon">👋</span>
                            <span class="title"><?php esc_html_e( '환영 팝업', 'acf-nudge-flow' ); ?></span>
                            <span class="desc"><?php esc_html_e( '첫 방문자에게 환영 메시지', 'acf-nudge-flow' ); ?></span>
                        </a>
                        
                        <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-flow-builder&template=exit_intent' ); ?>" class="quick-action-card">
                            <span class="icon">🚪</span>
                            <span class="title"><?php esc_html_e( '이탈 방지 팝업', 'acf-nudge-flow' ); ?></span>
                            <span class="desc"><?php esc_html_e( '이탈 시 할인 제안', 'acf-nudge-flow' ); ?></span>
                        </a>
                        
                        <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-flow-builder&template=newsletter' ); ?>" class="quick-action-card">
                            <span class="icon">📧</span>
                            <span class="title"><?php esc_html_e( '뉴스레터 구독', 'acf-nudge-flow' ); ?></span>
                            <span class="desc"><?php esc_html_e( '이메일 수집 팝업', 'acf-nudge-flow' ); ?></span>
                        </a>
                        
                        <a href="<?php echo admin_url( 'admin.php?page=acf-nudge-flow-builder&template=cart_reminder' ); ?>" class="quick-action-card">
                            <span class="icon">🛒</span>
                            <span class="title"><?php esc_html_e( '장바구니 리마인더', 'acf-nudge-flow' ); ?></span>
                            <span class="desc"><?php esc_html_e( '장바구니 이탈 고객 유도', 'acf-nudge-flow' ); ?></span>
                        </a>
                    </div>
                </div>
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
     */
    public function render_builder() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-nudge-flow' ) );
        }
        $workflow_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;


        $template = isset( $_GET['template'] ) ? sanitize_text_field( $_GET['template'] ) : '';
        ?>
        <div class="wrap acf-nudge-flow-admin">
            <div id="acf-nudge-workflow-builder" 
                 data-workflow-id="<?php echo esc_attr( $workflow_id ); ?>"
                 data-template="<?php echo esc_attr( $template ); ?>">
                <!-- React App will mount here -->
                <div class="acf-nudge-builder-loading">
                    <p><?php esc_html_e( '워크플로우 빌더 로딩 중...', 'acf-nudge-flow' ); ?></p>
                </div>
            </div>
        </div>
        <?php
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
            <h1><?php esc_html_e( '분석', 'acf-nudge-flow' ); ?></h1>
            <div id="acf-nudge-analytics">
                <p><?php esc_html_e( '분석 대시보드가 여기에 표시됩니다.', 'acf-nudge-flow' ); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * 설정 페이지 렌더링
     */
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
        $data = isset( $_POST['data'] ) ? json_decode( stripslashes( $_POST['data'] ), true ) : array();

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

        $manager = new ACF_Nudge_Trigger_Manager();
        wp_send_json_success( $manager->get_all() );
    }

    public function ajax_get_actions() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        $manager = new ACF_Nudge_Action_Manager();
        wp_send_json_success( $manager->get_all() );
    }
}

// 관리자 인스턴스 생성
new ACF_Nudge_Flow_Admin();

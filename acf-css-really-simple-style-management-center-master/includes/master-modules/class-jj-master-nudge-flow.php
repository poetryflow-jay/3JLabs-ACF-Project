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
        
        // 관리자 메뉴 (우선순위 상향)
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 5 );
        
        // 메뉴 순서 강제 조정 (대시보드 중심)
        add_filter( 'menu_order', array( $this, 'force_menu_order' ), 1001 );
        add_filter( 'custom_menu_order', '__return_true' );

        // 빌더 UI 복구 및 에셋 로드
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_footer', array( $this, 'output_builder_templates' ) );

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
     * [v21.0.0] Clean Master 재편: 대시보드를 최상단으로 올리고 메뉴 구조 정상화
     */
    public function add_admin_menu() {
        $capability  = 'manage_options';

        // 1. 최상위 메뉴 (대시보드 중심)
        add_menu_page(
            __( '넛지 플로우', 'acf-css-really-simple-style-management-center' ),
            __( '🚀 넛지 플로우', 'acf-css-really-simple-style-management-center' ),
            $capability,
            'jj-nudge-flow',
            array( $this, 'render_dashboard_page' ), 
            'dashicons-megaphone',
            58 
        );

        // 2. 서브메뉴 구성 (순서: 대시보드 > 워크플로우 > 분석 > 템플릿 > 설정)
        
        // (1) 대시보드 (기본 페이지)
        add_submenu_page(
            'jj-nudge-flow',
            __( '대시보드 통계', 'acf-css-really-simple-style-management-center' ),
            __( '📊 대시보드', 'acf-css-really-simple-style-management-center' ),
            $capability,
            'jj-nudge-flow',
            array( $this, 'render_dashboard_page' )
        );

        // (2) 워크플로우 (드래그 앤 드롭 빌더)
        add_submenu_page(
            'jj-nudge-flow',
            __( '워크플로우 관리', 'acf-css-really-simple-style-management-center' ),
            __( '🔄 워크플로우', 'acf-css-really-simple-style-management-center' ),
            $capability,
            'edit.php?post_type=jj_nudge_workflow',
            null 
        );

        // (3) 분석 (데이터 통계)
        add_submenu_page(
            'jj-nudge-flow',
            __( '성과 분석', 'acf-css-really-simple-style-management-center' ),
            __( '📈 분석 통계', 'acf-css-really-simple-style-management-center' ),
            $capability,
            'jj-nudge-analytics',
            array( $this, 'render_analytics_page' )
        );

        // (4) 템플릿 센터 (전략적 프리셋)
        add_submenu_page(
            'jj-nudge-flow',
            __( '전략 템플릿 센터', 'acf-css-really-simple-style-management-center' ),
            __( '🎁 템플릿 센터', 'acf-css-really-simple-style-management-center' ),
            $capability,
            'jj-nudge-templates',
            array( $this, 'render_template_center_page' )
        );
        
        // (5) 설정
        add_submenu_page(
            'jj-nudge-flow',
            __( '넛지 플로우 설정', 'acf-css-really-simple-style-management-center' ),
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
     * [v21.0.0] 전략적 프리셋 강제 주입 및 마켓플레이스 UI 고도화
     */
    public function render_template_center_page() {
        $presets = $this->get_preset_templates();
        ?>
        <div class="wrap jj-nudge-market-wrap">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h1><?php esc_html_e( '🎁 전략적 넛지 템플릿 센터', 'acf-css-really-simple-style-management-center' ); ?></h1>
                <button class="button button-primary button-hero" style="background:#6366f1; border-color:#4f46e5;">
                    <span class="dashicons dashicons-money-alt" style="margin-top:4px;"></span> <?php esc_html_e( '내 시나리오 판매 등록', 'acf-css-really-simple-style-management-center' ); ?>
                </button>
            </div>

            <div class="notice notice-info">
                <p><strong><?php esc_html_e( '💡 사장님을 위한 팁:', 'acf-css-really-simple-style-management-center' ); ?></strong> <?php esc_html_e( '아래 템플릿들은 설치 시 즉시 비활성화(Draft) 상태로 저장됩니다. 워크플로우 메뉴에서 쇼핑몰 환경에 맞게 세부 수치를 조정한 후 활성화하세요.', 'acf-css-really-simple-style-management-center' ); ?></p>
            </div>

            <h2 class="nav-tab-wrapper">
                <a href="#strategic" class="nav-tab nav-tab-active"><?php esc_html_e( '3J Labs 추천 전략 (보고서 기반)', 'acf-css-really-simple-style-management-center' ); ?></a>
                <a href="#global" class="nav-tab"><?php esc_html_e( '글로벌 유저 공유 마켓', 'acf-css-really-simple-style-management-center' ); ?></a>
                <a href="#my" class="nav-tab"><?php esc_html_e( '내 템플릿 보관함', 'acf-css-really-simple-style-management-center' ); ?></a>
            </h2>

            <div class="jj-market-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:25px; margin-top:25px;">
                <?php if ( ! empty( $presets ) ) : ?>
                    <?php foreach ( $presets as $id => $data ) : 
                        $is_premium = ( isset($data['type']) && $data['type'] === 'premium' );
                        $border_color = $is_premium ? '#f59e0b' : '#e2e8f0';
                        $bg_gradient = $is_premium ? 'linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%)' : '#f8fafc';
                    ?>
                    <div class="postbox" style="overflow:hidden; border-radius:10px; border-left:4px solid <?php echo $border_color; ?>; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                        <div style="height:140px; background:<?php echo $bg_gradient; ?>; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:center;">
                            <span class="dashicons <?php echo esc_attr( $data['icon'] ); ?>" style="font-size:50px; width:50px; height:50px; color:<?php echo $is_premium ? '#f59e0b' : '#94a3b8'; ?>;"></span>
                        </div>
                        <div style="padding:20px;">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
                                <span style="font-size:11px; color:#6366f1; font-weight:700; text-transform:uppercase;"><?php echo esc_html( $data['category'] ); ?></span>
                                <?php if ( $is_premium ) : ?>
                                    <span style="background:#fef3c7; color:#b45309; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:700;"><?php echo esc_html( $data['price'] ); ?></span>
                                <?php else : ?>
                                    <span style="background:#f1f5f9; color:#475569; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600;"><?php esc_html_e( 'FREE', 'acf-css-really-simple-style-management-center' ); ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 style="margin:0; font-size:17px; font-weight:800; color:#1e293b;"><?php echo esc_html( $data['title'] ); ?></h3>
                            <p style="font-size:13px; color:#64748b; margin:12px 0 20px; line-height:1.6; height:60px; overflow:hidden;">
                                <?php echo esc_html( $data['description'] ); ?>
                            </p>
                            <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #f1f5f9; padding-top:15px;">
                                <span style="font-size:12px; color:#94a3b8;">by 3J Labs Strategy</span>
                                <button class="button <?php echo $is_premium ? 'button-primary' : 'button-secondary'; ?> jj-install-preset" data-preset="<?php echo esc_attr( $id ); ?>">
                                    <?php echo $is_premium ? __( '구매 후 설치', 'acf-css-really-simple-style-management-center' ) : __( '지금 설치 (무료)', 'acf-css-really-simple-style-management-center' ); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p><?php esc_html_e( '로드할 수 있는 템플릿이 없습니다.', 'acf-css-really-simple-style-management-center' ); ?></p>
                <?php endif; ?>
            </div>

            <!-- 판매자 수익화 섹션 -->
            <div style="margin-top:50px; padding:40px; background:linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius:15px; color:#fff; box-shadow:0 10px 25px -5px rgba(79, 70, 229, 0.4);">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:40px;">
                    <div style="max-width:65%;">
                        <h2 style="color:#fff; margin:0 0 15px; font-size:26px; font-weight:900;"><?php esc_html_e( '💰 사장님의 마케팅 노하우를 자산으로 만드세요', 'acf-css-really-simple-style-management-center' ); ?></h2>
                        <p style="font-size:16px; opacity:0.95; line-height:1.7; margin:0;">
                            <?php esc_html_e( '직접 운영하며 검증된 고효율 넛지 시나리오가 있나요? 템플릿으로 만들어 마켓에 등록해 보세요. 판매 금액의 70%가 사장님의 수익으로 즉시 적립됩니다.', 'acf-css-really-simple-style-management-center' ); ?>
                        </p>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <button class="button button-large" style="height:55px; padding:0 40px; font-weight:900; border-radius:10px; font-size:16px; color:#4f46e5; background:#fff; border:none; box-shadow:0 10px 15px -3px rgba(0,0,0,0.2); cursor:pointer;">
                            <?php esc_html_e( '판매자 파트너 신청하기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $('.jj-install-preset').on('click', function() {
                var presetId = $(this).data('preset');
                var $btn = $(this);
                
                if (confirm('<?php echo esc_js( __( '이 템플릿을 설치하시겠습니까? 설치 후 워크플로우 메뉴에서 확인 가능합니다.', 'acf-css-really-simple-style-management-center' ) ); ?>')) {
                    $btn.prop('disabled', true).text('<?php echo esc_js( __( '설치 중...', 'acf-css-really-simple-style-management-center' ) ); ?>');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'jj_install_nudge_preset',
                            preset_id: presetId,
                            nonce: '<?php echo wp_create_nonce( "jj_nudge_market_nonce" ); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('<?php echo esc_js( __( '성공적으로 설치되었습니다! 워크플로우 메뉴에서 설정을 확인하세요.', 'acf-css-really-simple-style-management-center' ) ); ?>');
                                $btn.text('<?php echo esc_js( __( '설치 완료', 'acf-css-really-simple-style-management-center' ) ); ?>');
                            } else {
                                alert('오류: ' + response.data);
                                $btn.prop('disabled', false).text('<?php echo esc_js( __( '다시 시도', 'acf-css-really-simple-style-management-center' ) ); ?>');
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
     * 관리자용 에셋 로드 (빌더 UI용)
     * [v21.0.0] 좌측 패널 드래그 앤 드롭 빌더 복구
     */
    public function admin_enqueue_scripts( $hook ) {
        global $post_type;
        if ( 'jj_nudge_workflow' !== $post_type ) {
            return;
        }

        wp_enqueue_style( 'jj-nudge-builder', JJ_STYLE_GUIDE_URL . 'assets/css/jj-nudge-builder.css', array(), JJ_STYLE_GUIDE_VERSION );
        wp_enqueue_script( 'jquery-ui-draggable' );
        wp_enqueue_script( 'jquery-ui-droppable' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'jj-nudge-builder', JJ_STYLE_GUIDE_URL . 'assets/js/jj-nudge-builder.js', array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-droppable' ), JJ_STYLE_GUIDE_VERSION, true );
    }

    /**
     * 빌더 템플릿 출력 (좌측 패널 트리거/액션 목록)
     */
    public function output_builder_templates() {
        global $post_type;
        if ( 'jj_nudge_workflow' !== $post_type ) {
            return;
        }
        ?>
        <div id="jj-nudge-builder-sidebar" style="display:none;">
            <div class="jj-builder-panel">
                <h3><?php esc_html_e( '⚡ 트리거 (Triggers)', 'acf-css-really-simple-style-management-center' ); ?></h3>
                <div class="jj-draggable-item" data-type="trigger" data-id="first_visit"><?php esc_html_e( '첫 방문', 'acf-css-really-simple-style-management-center' ); ?></div>
                <div class="jj-draggable-item" data-type="trigger" data-id="page_view"><?php esc_html_e( '페이지 조회', 'acf-css-really-simple-style-management-center' ); ?></div>
                <div class="jj-draggable-item" data-type="trigger" data-id="exit_intent"><?php esc_html_e( '이탈 감지', 'acf-css-really-simple-style-management-center' ); ?></div>
                
                <h3><?php esc_html_e( '🎯 액션 (Actions)', 'acf-css-really-simple-style-management-center' ); ?></h3>
                <div class="jj-draggable-item" data-type="action" data-id="popup"><?php esc_html_e( '팝업 노출', 'acf-css-really-simple-style-management-center' ); ?></div>
                <div class="jj-draggable-item" data-type="action" data-id="coupon"><?php esc_html_e( '쿠폰 지급', 'acf-css-really-simple-style-management-center' ); ?></div>
                <div class="jj-draggable-item" data-type="action" data-id="toast"><?php esc_html_e( '토스트 알림', 'acf-css-really-simple-style-management-center' ); ?></div>
            </div>
        </div>
        <script>
            // WP 에디터 상단에 빌더 사이드바 강제 삽입
            jQuery(document).ready(function($) {
                if ($('#poststuff').length) {
                    $('#jj-nudge-builder-sidebar').show().prependTo('#poststuff');
                    $('body').addClass('jj-nudge-builder-active');
                }
            });
        </script>
        <?php
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

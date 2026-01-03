<?php
/**
 * ACF CSS WooCommerce Toolkit - Admin Settings
 *
 * 관리자 설정 페이지
 *
 * @package ACF_CSS_WooCommerce_Toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin Settings 클래스
 */
class ACF_CSS_WC_Admin_Settings {

    /**
     * 설정 옵션명
     */
    const OPTION_NAME = 'acf_css_wc_settings';

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * 메뉴 추가
     */
    public function add_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'ACF CSS WooCommerce Toolkit', 'acf-css-woocommerce-toolkit' ),
            __( 'ACF CSS Toolkit', 'acf-css-woocommerce-toolkit' ),
            'manage_woocommerce',
            'acf-css-wc-toolkit',
            array( $this, 'render_settings_page' )
        );
        
        // Page Styler submenu
        add_submenu_page(
            'woocommerce',
            __( 'Page Styler', 'acf-css-woocommerce-toolkit' ),
            __( '🎨 Page Styler', 'acf-css-woocommerce-toolkit' ),
            'manage_woocommerce',
            'acf-css-wc-page-styler',
            array( $this, 'render_styler_page' )
        );
    }

    /**
     * 설정 등록
     */
    public function register_settings() {
        register_setting( 'acf_css_wc_settings_group', self::OPTION_NAME, array(
            'sanitize_callback' => array( $this, 'sanitize_settings' ),
        ) );

        // 일반 설정 섹션
        add_settings_section(
            'acf_css_wc_general_section',
            __( '일반 설정', 'acf-css-woocommerce-toolkit' ),
            array( $this, 'render_section_description' ),
            'acf-css-wc-toolkit'
        );

        // 가격 엔진 활성화
        add_settings_field(
            'enable_price_engine',
            __( '가격 엔진', 'acf-css-woocommerce-toolkit' ),
            array( $this, 'render_checkbox_field' ),
            'acf-css-wc-toolkit',
            'acf_css_wc_general_section',
            array(
                'id'          => 'enable_price_engine',
                'label'       => __( '가격 계산 엔진 활성화', 'acf-css-woocommerce-toolkit' ),
                'description' => __( '할인율, 절약금액, 할부 가격 계산 기능', 'acf-css-woocommerce-toolkit' ),
            )
        );

        // 할인 계산기 활성화
        add_settings_field(
            'enable_discount_calculator',
            __( '할인 계산기', 'acf-css-woocommerce-toolkit' ),
            array( $this, 'render_checkbox_field' ),
            'acf-css-wc-toolkit',
            'acf_css_wc_general_section',
            array(
                'id'          => 'enable_discount_calculator',
                'label'       => __( '상품 편집 할인 계산기 활성화', 'acf-css-woocommerce-toolkit' ),
                'description' => __( '상품 편집 화면에서 퍼센트/금액 기반 할인 계산', 'acf-css-woocommerce-toolkit' ),
            )
        );

        // 빠른 편집 활성화
        add_settings_field(
            'enable_quick_edit',
            __( '빠른 편집', 'acf-css-woocommerce-toolkit' ),
            array( $this, 'render_checkbox_field' ),
            'acf-css-wc-toolkit',
            'acf_css_wc_general_section',
            array(
                'id'          => 'enable_quick_edit',
                'label'       => __( '빠른 편집 필드 확장 활성화', 'acf-css-woocommerce-toolkit' ),
                'description' => __( '상품 목록에서 할부 개월 수 빠른 편집', 'acf-css-woocommerce-toolkit' ),
            )
        );

        // 장바구니 개선 활성화
        add_settings_field(
            'enable_cart_enhancer',
            __( '장바구니 개선', 'acf-css-woocommerce-toolkit' ),
            array( $this, 'render_checkbox_field' ),
            'acf-css-wc-toolkit',
            'acf_css_wc_general_section',
            array(
                'id'          => 'enable_cart_enhancer',
                'label'       => __( '장바구니 UI 개선 활성화', 'acf-css-woocommerce-toolkit' ),
                'description' => __( '장바구니/미니카트 상품명 정리', 'acf-css-woocommerce-toolkit' ),
            )
        );

        // 할부 표시 활성화
        add_settings_field(
            'enable_installment_display',
            __( '할부 표시', 'acf-css-woocommerce-toolkit' ),
            array( $this, 'render_checkbox_field' ),
            'acf-css-wc-toolkit',
            'acf_css_wc_general_section',
            array(
                'id'          => 'enable_installment_display',
                'label'       => __( '할부 가격 표시 활성화', 'acf-css-woocommerce-toolkit' ),
                'description' => __( '상품 가격에 월 할부 금액 표시', 'acf-css-woocommerce-toolkit' ),
            )
        );
    }

    /**
     * 설정 정제
     */
    public function sanitize_settings( $input ) {
        $sanitized = array();
        
        $checkboxes = array(
            'enable_price_engine',
            'enable_discount_calculator',
            'enable_quick_edit',
            'enable_cart_enhancer',
            'enable_installment_display',
        );

        foreach ( $checkboxes as $key ) {
            $sanitized[ $key ] = isset( $input[ $key ] ) && $input[ $key ] ? true : false;
        }

        return $sanitized;
    }

    /**
     * 섹션 설명 렌더링
     */
    public function render_section_description() {
        echo '<p>' . esc_html__( 'ACF CSS WooCommerce Toolkit의 기능을 활성화/비활성화합니다.', 'acf-css-woocommerce-toolkit' ) . '</p>';
    }

    /**
     * 체크박스 필드 렌더링
     */
    public function render_checkbox_field( $args ) {
        $options = get_option( self::OPTION_NAME, array() );
        $value   = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : true;
        ?>
        <label>
            <input type="checkbox" 
                   name="<?php echo esc_attr( self::OPTION_NAME . '[' . $args['id'] . ']' ); ?>" 
                   value="1" 
                   <?php checked( $value, true ); ?>>
            <?php echo esc_html( $args['label'] ); ?>
        </label>
        <?php if ( ! empty( $args['description'] ) ) : ?>
            <p class="description"><?php echo esc_html( $args['description'] ); ?></p>
        <?php endif;
    }

    /**
     * 설정 페이지 렌더링
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>
                🛒 <?php esc_html_e( 'ACF CSS WooCommerce Toolkit', 'acf-css-woocommerce-toolkit' ); ?>
            </h1>
            
            <p class="description">
                <?php esc_html_e( 'WooCommerce 상점의 가격 표시, 할인 계산, 장바구니 UI를 개선하는 도구 모음입니다.', 'acf-css-woocommerce-toolkit' ); ?>
            </p>

            <form method="post" action="options.php">
                <?php
                settings_fields( 'acf_css_wc_settings_group' );
                do_settings_sections( 'acf-css-wc-toolkit' );
                submit_button();
                ?>
            </form>

            <hr>

            <h2><?php esc_html_e( '사용 가능한 숏코드', 'acf-css-woocommerce-toolkit' ); ?></h2>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( '숏코드', 'acf-css-woocommerce-toolkit' ); ?></th>
                        <th><?php esc_html_e( '설명', 'acf-css-woocommerce-toolkit' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>[acf_wc_price]</code> / <code>[realdeal_price]</code></td>
                        <td><?php esc_html_e( '통합 가격 표시 (배지, 할인, 할부 포함)', 'acf-css-woocommerce-toolkit' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>[acf_wc_badge]</code> / <code>[rd_badge]</code></td>
                        <td><?php esc_html_e( '할인 배지 (예: 30% OFF)', 'acf-css-woocommerce-toolkit' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>[acf_wc_saved]</code> / <code>[rd_summary]</code></td>
                        <td><?php esc_html_e( '절약 금액 표시', 'acf-css-woocommerce-toolkit' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>[acf_wc_installments]</code> / <code>[rd_installments]</code></td>
                        <td><?php esc_html_e( '할부 정보 (예: 월 12,000원 / 12개월)', 'acf-css-woocommerce-toolkit' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * 설정값 가져오기
     */
    public static function get_option( $key, $default = null ) {
        $options = get_option( self::OPTION_NAME, array() );
        return isset( $options[ $key ] ) ? $options[ $key ] : $default;
    }

    /**
     * Page Styler 페이지 렌더링
     */
    public function render_styler_page() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( __( '권한이 없습니다.', 'acf-css-woocommerce-toolkit' ) );
        }

        if ( ! class_exists( 'JJ_WC_Product_Page_Styler' ) ) {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'Product Page Styler가 로드되지 않았습니다.', 'acf-css-woocommerce-toolkit' ) . '</p></div>';
            return;
        }

        $styler = JJ_WC_Product_Page_Styler::instance();
        $templates = $styler->get_templates();
        $applied = $styler->get_applied_styles();
        ?>
        <div class="wrap acf-css-wc-styler">
            <h1><?php esc_html_e( 'WooCommerce Page Styler', 'acf-css-woocommerce-toolkit' ); ?></h1>
            <p class="description">
                <?php esc_html_e( '원클릭으로 우커머스 페이지에 전문가가 디자인한 스타일을 적용하세요.', 'acf-css-woocommerce-toolkit' ); ?>
            </p>

            <style>
                .jj-template-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                    gap: 24px;
                    margin-top: 24px;
                }
                .jj-template-card {
                    background: #fff;
                    border: 2px solid #e5e7eb;
                    border-radius: 12px;
                    padding: 20px;
                    transition: all 0.3s ease;
                }
                .jj-template-card:hover {
                    border-color: #3b82f6;
                    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
                }
                .jj-template-card.applied {
                    border-color: #10b981;
                    background: #f0fdf4;
                }
                .jj-template-card h3 {
                    margin: 0 0 8px 0;
                    font-size: 18px;
                    color: #111827;
                }
                .jj-template-card .description {
                    color: #6b7280;
                    font-size: 14px;
                    margin-bottom: 16px;
                }
                .jj-template-card .target-badge {
                    display: inline-block;
                    padding: 4px 12px;
                    background: #e5e7eb;
                    border-radius: 6px;
                    font-size: 12px;
                    font-weight: 600;
                    color: #374151;
                    margin-bottom: 12px;
                }
                .jj-template-card .button {
                    width: 100%;
                    margin-top: 8px;
                }
                .jj-template-card .button-primary {
                    background: #3b82f6;
                    border-color: #3b82f6;
                }
                .jj-template-card .button-secondary {
                    background: #ef4444;
                    border-color: #ef4444;
                    color: #fff;
                }
                .jj-template-card.applied .applied-badge {
                    display: inline-block;
                    padding: 6px 12px;
                    background: #10b981;
                    color: #fff;
                    border-radius: 6px;
                    font-size: 12px;
                    font-weight: 700;
                    margin-bottom: 12px;
                }
            </style>

            <div class="jj-template-grid">
                <?php foreach ( $templates as $id => $template ) : 
                    $is_applied = isset( $applied[ $template['target'] ] ) && $applied[ $template['target'] ]['template_id'] === $id;
                ?>
                    <div class="jj-template-card <?php echo $is_applied ? 'applied' : ''; ?>" data-template-id="<?php echo esc_attr( $id ); ?>" data-target="<?php echo esc_attr( $template['target'] ); ?>">
                        <?php if ( $is_applied ) : ?>
                            <span class="applied-badge">✓ <?php esc_html_e( '적용됨', 'acf-css-woocommerce-toolkit' ); ?></span>
                        <?php endif; ?>
                        
                        <h3><?php echo esc_html( $template['name'] ); ?></h3>
                        
                        <span class="target-badge"><?php echo esc_html( str_replace( '_', ' ', $template['target'] ) ); ?></span>
                        
                        <p class="description"><?php echo esc_html( $template['description'] ); ?></p>
                        
                        <?php if ( ! $is_applied ) : ?>
                            <button class="button button-primary jj-apply-template" data-template-id="<?php echo esc_attr( $id ); ?>">
                                <?php esc_html_e( '적용하기', 'acf-css-woocommerce-toolkit' ); ?>
                            </button>
                        <?php else : ?>
                            <button class="button button-secondary jj-remove-template" data-target="<?php echo esc_attr( $template['target'] ); ?>">
                                <?php esc_html_e( '제거하기', 'acf-css-woocommerce-toolkit' ); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <script>
            jQuery(document).ready(function($) {
                $('.jj-apply-template').on('click', function() {
                    var $btn = $(this);
                    var templateId = $btn.data('template-id');
                    
                    $btn.prop('disabled', true).text('<?php esc_html_e( '적용 중...', 'acf-css-woocommerce-toolkit' ); ?>');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'jj_wc_apply_template',
                            security: '<?php echo wp_create_nonce( 'jj_wc_styler_nonce' ); ?>',
                            template_id: templateId
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert(response.data.message);
                                $btn.prop('disabled', false).text('<?php esc_html_e( '적용하기', 'acf-css-woocommerce-toolkit' ); ?>');
                            }
                        },
                        error: function() {
                            alert('<?php esc_html_e( '오류가 발생했습니다.', 'acf-css-woocommerce-toolkit' ); ?>');
                            $btn.prop('disabled', false).text('<?php esc_html_e( '적용하기', 'acf-css-woocommerce-toolkit' ); ?>');
                        }
                    });
                });

                $('.jj-remove-template').on('click', function() {
                    if (!confirm('<?php esc_html_e( '정말 제거하시겠습니까?', 'acf-css-woocommerce-toolkit' ); ?>')) {
                        return;
                    }
                    
                    var $btn = $(this);
                    var target = $btn.data('target');
                    
                    $btn.prop('disabled', true).text('<?php esc_html_e( '제거 중...', 'acf-css-woocommerce-toolkit' ); ?>');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'jj_wc_remove_template',
                            security: '<?php echo wp_create_nonce( 'jj_wc_styler_nonce' ); ?>',
                            target: target
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert(response.data.message);
                                $btn.prop('disabled', false).text('<?php esc_html_e( '제거하기', 'acf-css-woocommerce-toolkit' ); ?>');
                            }
                        },
                        error: function() {
                            alert('<?php esc_html_e( '오류가 발생했습니다.', 'acf-css-woocommerce-toolkit' ); ?>');
                            $btn.prop('disabled', false).text('<?php esc_html_e( '제거하기', 'acf-css-woocommerce-toolkit' ); ?>');
                        }
                    });
                });
            });
            </script>
        </div>
        <?php
    }
}

// 인스턴스 초기화
ACF_CSS_WC_Admin_Settings::instance();

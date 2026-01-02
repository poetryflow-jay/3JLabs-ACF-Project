<?php
/**
 * ACF CSS WooCommerce Toolkit - Discount Calculator
 *
 * 상품 편집 화면의 할인 계산기
 *
 * @package ACF_CSS_WooCommerce_Toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Discount Calculator 클래스
 */
class ACF_CSS_WC_Discount_Calculator {

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
        add_action( 'woocommerce_product_options_pricing', array( $this, 'add_pricing_fields' ) );
    }

    /**
     * 가격 설정 필드 추가
     */
    public function add_pricing_fields() {
        ?>
        <div class="options_group pricing show_if_simple show_if_external">
            <?php
            // 할부 개월 수 설정
            woocommerce_wp_select( array(
                'id'          => '_installment_months',
                'label'       => __( '할부 개월 수', 'acf-css-woocommerce-toolkit' ),
                'options'     => array(
                    '0'  => __( '표시 안 함', 'acf-css-woocommerce-toolkit' ),
                    '1'  => __( '일시불', 'acf-css-woocommerce-toolkit' ),
                    '3'  => __( '3개월', 'acf-css-woocommerce-toolkit' ),
                    '6'  => __( '6개월', 'acf-css-woocommerce-toolkit' ),
                    '10' => __( '10개월', 'acf-css-woocommerce-toolkit' ),
                    '12' => __( '12개월', 'acf-css-woocommerce-toolkit' ),
                    '24' => __( '24개월', 'acf-css-woocommerce-toolkit' ),
                    '36' => __( '36개월', 'acf-css-woocommerce-toolkit' ),
                ),
                'desc_tip'    => true,
                'description' => __( '상품 가격에 할부 정보를 표시합니다.', 'acf-css-woocommerce-toolkit' ),
            ) );
            ?>
            
            <!-- 할인 계산기 섹션 -->
            <div class="acf-wc-discount-calculator" style="border: 1px solid #ddd; padding: 15px; margin: 10px 12px; background: #fafafa; border-radius: 4px;">
                <h4 style="margin-top: 0; color: #23282d;">
                    🧮 <?php esc_html_e( '할인 계산기', 'acf-css-woocommerce-toolkit' ); ?>
                </h4>
                <p class="description" style="margin-bottom: 15px;">
                    <?php esc_html_e( '정가를 기준으로 할인율 또는 할인 금액을 계산하여 할인가에 자동 적용합니다.', 'acf-css-woocommerce-toolkit' ); ?>
                </p>
                
                <div class="acf-wc-calc-row" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;">
                    <div style="flex: 1; min-width: 200px;">
                        <label for="acf_wc_discount_percent" style="display: block; margin-bottom: 5px; font-weight: 600;">
                            <?php esc_html_e( '퍼센트 할인', 'acf-css-woocommerce-toolkit' ); ?>
                        </label>
                        <div style="display: flex; gap: 5px;">
                            <input type="number" 
                                   id="acf_wc_discount_percent" 
                                   placeholder="%" 
                                   min="0" 
                                   max="100" 
                                   step="0.1"
                                   style="width: 80px;">
                            <button type="button" class="button acf-wc-apply-percent-discount">
                                <?php esc_html_e( '% 적용', 'acf-css-woocommerce-toolkit' ); ?>
                            </button>
                        </div>
                    </div>
                    
                    <div style="flex: 1; min-width: 200px;">
                        <label for="acf_wc_discount_amount" style="display: block; margin-bottom: 5px; font-weight: 600;">
                            <?php esc_html_e( '금액 할인', 'acf-css-woocommerce-toolkit' ); ?>
                        </label>
                        <div style="display: flex; gap: 5px;">
                            <input type="number" 
                                   id="acf_wc_discount_amount" 
                                   placeholder="<?php echo esc_attr( get_woocommerce_currency_symbol() ); ?>" 
                                   min="0"
                                   step="100"
                                   style="width: 120px;">
                            <button type="button" class="button acf-wc-apply-amount-discount">
                                <?php esc_html_e( '금액 차감', 'acf-css-woocommerce-toolkit' ); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- 계산 결과 미리보기 -->
                <div id="acf-wc-discount-preview" style="background: #fff; padding: 12px; margin-top: 10px; border: 1px solid #e1e1e1; border-radius: 4px; display: none;">
                    <strong>📊 <?php esc_html_e( '계산 결과:', 'acf-css-woocommerce-toolkit' ); ?></strong>
                    <span id="acf-wc-preview-text"></span>
                </div>
            </div>
        </div>
        <?php
    }
}

// 인스턴스 초기화
ACF_CSS_WC_Discount_Calculator::instance();

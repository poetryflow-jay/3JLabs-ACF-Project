<?php
/**
 * ACF CSS WooCommerce Toolkit - Quick Edit Fields
 *
 * 상품 목록의 빠른 편집 필드 확장
 *
 * @package ACF_CSS_WooCommerce_Toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Quick Edit Fields 클래스
 */
class ACF_CSS_WC_Quick_Edit_Fields {

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
        // 빠른 편집 필드 추가
        add_action( 'woocommerce_product_quick_edit_end', array( $this, 'add_quick_edit_fields' ) );
        
        // 빠른 편집 저장
        add_action( 'woocommerce_product_quick_edit_save', array( $this, 'save_quick_edit_fields' ) );
        
        // 컬럼에 데이터 추가 (빠른 편집에서 사용)
        add_action( 'manage_product_posts_custom_column', array( $this, 'add_column_data' ), 10, 2 );
    }

    /**
     * 빠른 편집 필드 추가
     */
    public function add_quick_edit_fields() {
        wp_nonce_field( 'acf_wc_quick_edit_nonce', 'acf_wc_quick_edit_nonce_field' );
        ?>
        <div class="acf-wc-quick-edit-fields">
            <h4><?php esc_html_e( '가격 상세 설정', 'acf-css-woocommerce-toolkit' ); ?></h4>
            
            <label>
                <span class="title"><?php esc_html_e( '할부 개월 수', 'acf-css-woocommerce-toolkit' ); ?></span>
                <span class="input-text-wrap">
                    <select name="_installment_months" class="acf-wc-installment-months">
                        <option value="0"><?php esc_html_e( '표시 안 함', 'acf-css-woocommerce-toolkit' ); ?></option>
                        <option value="1"><?php esc_html_e( '일시불', 'acf-css-woocommerce-toolkit' ); ?></option>
                        <option value="3"><?php esc_html_e( '3개월', 'acf-css-woocommerce-toolkit' ); ?></option>
                        <option value="6"><?php esc_html_e( '6개월', 'acf-css-woocommerce-toolkit' ); ?></option>
                        <option value="10"><?php esc_html_e( '10개월', 'acf-css-woocommerce-toolkit' ); ?></option>
                        <option value="12"><?php esc_html_e( '12개월', 'acf-css-woocommerce-toolkit' ); ?></option>
                        <option value="24"><?php esc_html_e( '24개월', 'acf-css-woocommerce-toolkit' ); ?></option>
                    </select>
                </span>
            </label>
        </div>
        <?php
    }

    /**
     * 빠른 편집 저장
     *
     * @param WC_Product $product 상품 객체
     */
    public function save_quick_edit_fields( $product ) {
        // Nonce 검증
        if ( ! isset( $_POST['acf_wc_quick_edit_nonce_field'] ) || 
             ! wp_verify_nonce( $_POST['acf_wc_quick_edit_nonce_field'], 'acf_wc_quick_edit_nonce' ) ) {
            return;
        }

        // 할부 개월 수 저장
        if ( isset( $_POST['_installment_months'] ) ) {
            $product->update_meta_data( '_installment_months', absint( $_POST['_installment_months'] ) );
        }

        $product->save();
    }

    /**
     * 컬럼에 데이터 추가 (빠른 편집 JS에서 사용)
     *
     * @param string $column 컬럼명
     * @param int $post_id 상품 ID
     */
    public function add_column_data( $column, $post_id ) {
        if ( $column === 'price' ) {
            $product = wc_get_product( $post_id );
            if ( $product ) {
                $installment_months = $product->get_meta( '_installment_months' );
                echo '<div class="hidden acf-wc-installment-months-data" data-months="' . esc_attr( $installment_months ) . '"></div>';
            }
        }
    }
}

// 인스턴스 초기화
ACF_CSS_WC_Quick_Edit_Fields::instance();

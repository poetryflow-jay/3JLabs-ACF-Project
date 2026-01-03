<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Product Page Styler
 * - One-click styling templates for WooCommerce pages
 * - Product Grid, Single Product, Cart, Checkout templates
 * 
 * @package ACF_CSS_WooCommerce_Toolkit
 * @version 2.2.0
 * @author Jenny (UX) + Jason (Implementation)
 */
class JJ_WC_Product_Page_Styler {
    private static $instance = null;
    private $option_key = 'jj_wc_page_styles';
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 스타일 템플릿 정의
     */
    public function get_templates() {
        return array(
            'modern_grid' => array(
                'name' => __( 'Modern Grid', 'acf-css-woocommerce-toolkit' ),
                'description' => __( '깔끔한 그리드 레이아웃, 호버 애니메이션', 'acf-css-woocommerce-toolkit' ),
                'preview_image' => ACF_CSS_WC_PLUGIN_URL . 'assets/img/preview-modern-grid.jpg',
                'target' => 'product_archive',
                'css' => "
                    .woocommerce ul.products li.product {
                        border: 1px solid #e5e7eb;
                        border-radius: 12px;
                        padding: 20px;
                        transition: all 0.3s ease;
                        background: #fff;
                    }
                    .woocommerce ul.products li.product:hover {
                        transform: translateY(-8px);
                        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
                        border-color: #3b82f6;
                    }
                    .woocommerce ul.products li.product img {
                        border-radius: 8px;
                        transition: transform 0.3s ease;
                    }
                    .woocommerce ul.products li.product:hover img {
                        transform: scale(1.05);
                    }
                    .woocommerce ul.products li.product .woocommerce-loop-product__title {
                        font-size: 16px;
                        font-weight: 600;
                        margin-top: 16px;
                        color: #1f2937;
                    }
                    .woocommerce ul.products li.product .price {
                        font-size: 20px;
                        font-weight: 700;
                        color: #3b82f6;
                        margin-top: 8px;
                    }
                    .woocommerce ul.products li.product .button {
                        width: 100%;
                        margin-top: 12px;
                        background: #3b82f6;
                        color: #fff;
                        border-radius: 8px;
                        padding: 12px 24px;
                        transition: all 0.2s ease;
                    }
                    .woocommerce ul.products li.product .button:hover {
                        background: #2563eb;
                        transform: translateY(-2px);
                    }
                ",
            ),
            'luxury_single' => array(
                'name' => __( 'Luxury Single Product', 'acf-css-woocommerce-toolkit' ),
                'description' => __( '고급스러운 단일 상품 페이지 디자인', 'acf-css-woocommerce-toolkit' ),
                'preview_image' => ACF_CSS_WC_PLUGIN_URL . 'assets/img/preview-luxury-single.jpg',
                'target' => 'single_product',
                'css' => "
                    .woocommerce div.product {
                        background: linear-gradient(135deg, #fdfcfb 0%, #f3f4f6 100%);
                        padding: 40px;
                        border-radius: 16px;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
                    }
                    .woocommerce div.product .product_title {
                        font-size: 36px;
                        font-weight: 700;
                        color: #111827;
                        letter-spacing: -0.5px;
                        margin-bottom: 16px;
                    }
                    .woocommerce div.product p.price {
                        font-size: 32px;
                        font-weight: 800;
                        color: #d97706;
                        margin-bottom: 24px;
                        text-shadow: 0 2px 4px rgba(217,119,6,0.1);
                    }
                    .woocommerce div.product .woocommerce-product-gallery {
                        border-radius: 12px;
                        overflow: hidden;
                        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
                    }
                    .woocommerce div.product form.cart {
                        background: #fff;
                        padding: 24px;
                        border-radius: 12px;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
                    }
                    .woocommerce div.product form.cart .button {
                        width: 100%;
                        padding: 18px 36px;
                        font-size: 18px;
                        font-weight: 700;
                        background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
                        color: #fff;
                        border-radius: 12px;
                        border: none;
                        box-shadow: 0 4px 12px rgba(217,119,6,0.3);
                        transition: all 0.3s ease;
                    }
                    .woocommerce div.product form.cart .button:hover {
                        transform: translateY(-3px);
                        box-shadow: 0 8px 20px rgba(217,119,6,0.4);
                    }
                    .woocommerce div.product .woocommerce-product-details__short-description {
                        font-size: 16px;
                        line-height: 1.8;
                        color: #4b5563;
                        margin-bottom: 24px;
                    }
                ",
            ),
            'minimal_cart' => array(
                'name' => __( 'Minimal Cart', 'acf-css-woocommerce-toolkit' ),
                'description' => __( '미니멀 디자인 장바구니', 'acf-css-woocommerce-toolkit' ),
                'preview_image' => ACF_CSS_WC_PLUGIN_URL . 'assets/img/preview-minimal-cart.jpg',
                'target' => 'cart',
                'css' => "
                    .woocommerce-cart table.cart {
                        border: none;
                        background: #fff;
                        border-radius: 12px;
                        overflow: hidden;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                    }
                    .woocommerce-cart table.cart thead {
                        background: #f9fafb;
                        border-bottom: 2px solid #e5e7eb;
                    }
                    .woocommerce-cart table.cart thead th {
                        padding: 20px 16px;
                        font-weight: 600;
                        color: #374151;
                        text-transform: uppercase;
                        font-size: 12px;
                        letter-spacing: 0.5px;
                    }
                    .woocommerce-cart table.cart td {
                        padding: 24px 16px;
                        border-bottom: 1px solid #f3f4f6;
                    }
                    .woocommerce-cart table.cart td.product-thumbnail img {
                        border-radius: 8px;
                        border: 2px solid #e5e7eb;
                    }
                    .woocommerce-cart table.cart td.product-name a {
                        font-weight: 600;
                        color: #111827;
                        text-decoration: none;
                    }
                    .woocommerce-cart table.cart td.product-name a:hover {
                        color: #3b82f6;
                    }
                    .woocommerce-cart table.cart td.product-quantity input {
                        border: 2px solid #e5e7eb;
                        border-radius: 6px;
                        padding: 8px 12px;
                        font-size: 14px;
                        font-weight: 600;
                    }
                    .woocommerce-cart .cart-collaterals {
                        background: #f9fafb;
                        padding: 32px;
                        border-radius: 12px;
                        margin-top: 24px;
                    }
                    .woocommerce-cart .cart-collaterals h2 {
                        font-size: 24px;
                        font-weight: 700;
                        color: #111827;
                        margin-bottom: 24px;
                    }
                    .woocommerce-cart .cart-collaterals .shop_table {
                        background: #fff;
                        border-radius: 8px;
                        padding: 16px;
                    }
                    .woocommerce-cart .wc-proceed-to-checkout .button {
                        width: 100%;
                        padding: 16px 32px;
                        font-size: 18px;
                        font-weight: 700;
                        background: #10b981;
                        color: #fff;
                        border-radius: 8px;
                        text-align: center;
                        transition: all 0.2s ease;
                    }
                    .woocommerce-cart .wc-proceed-to-checkout .button:hover {
                        background: #059669;
                        transform: translateY(-2px);
                    }
                ",
            ),
            'clean_checkout' => array(
                'name' => __( 'Clean Checkout', 'acf-css-woocommerce-toolkit' ),
                'description' => __( '간결하고 신뢰감 있는 결제 페이지', 'acf-css-woocommerce-toolkit' ),
                'preview_image' => ACF_CSS_WC_PLUGIN_URL . 'assets/img/preview-clean-checkout.jpg',
                'target' => 'checkout',
                'css' => "
                    .woocommerce-checkout {
                        background: #f9fafb;
                        padding: 32px 0;
                    }
                    .woocommerce-checkout .woocommerce-billing-fields,
                    .woocommerce-checkout .woocommerce-shipping-fields {
                        background: #fff;
                        padding: 32px;
                        border-radius: 12px;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                        margin-bottom: 24px;
                    }
                    .woocommerce-checkout h3 {
                        font-size: 20px;
                        font-weight: 700;
                        color: #111827;
                        margin-bottom: 24px;
                        padding-bottom: 12px;
                        border-bottom: 2px solid #e5e7eb;
                    }
                    .woocommerce-checkout .form-row input.input-text,
                    .woocommerce-checkout .form-row select {
                        border: 2px solid #e5e7eb;
                        border-radius: 8px;
                        padding: 12px 16px;
                        font-size: 14px;
                        transition: all 0.2s ease;
                    }
                    .woocommerce-checkout .form-row input.input-text:focus,
                    .woocommerce-checkout .form-row select:focus {
                        border-color: #3b82f6;
                        outline: none;
                        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
                    }
                    .woocommerce-checkout #order_review {
                        background: #fff;
                        padding: 32px;
                        border-radius: 12px;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                    }
                    .woocommerce-checkout #order_review_heading {
                        font-size: 24px;
                        font-weight: 700;
                        color: #111827;
                        margin-bottom: 24px;
                    }
                    .woocommerce-checkout #payment {
                        background: #f9fafb;
                        border-radius: 8px;
                        padding: 24px;
                    }
                    .woocommerce-checkout #place_order {
                        width: 100%;
                        padding: 18px 36px;
                        font-size: 18px;
                        font-weight: 700;
                        background: #10b981;
                        color: #fff;
                        border-radius: 8px;
                        border: none;
                        transition: all 0.3s ease;
                    }
                    .woocommerce-checkout #place_order:hover {
                        background: #059669;
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
                    }
                ",
            ),
        );
    }

    /**
     * 템플릿 적용
     */
    public function apply_template( $template_id ) {
        $templates = $this->get_templates();
        
        if ( ! isset( $templates[ $template_id ] ) ) {
            return false;
        }
        
        $template = $templates[ $template_id ];
        $current_styles = get_option( $this->option_key, array() );
        
        // 템플릿 스타일 저장
        $current_styles[ $template['target'] ] = array(
            'template_id' => $template_id,
            'css' => $template['css'],
            'applied_at' => current_time( 'mysql' ),
        );
        
        update_option( $this->option_key, $current_styles, false );
        
        return true;
    }

    /**
     * 스타일 제거
     */
    public function remove_template( $target ) {
        $current_styles = get_option( $this->option_key, array() );
        
        if ( isset( $current_styles[ $target ] ) ) {
            unset( $current_styles[ $target ] );
            update_option( $this->option_key, $current_styles, false );
            return true;
        }
        
        return false;
    }

    /**
     * 적용된 스타일 가져오기
     */
    public function get_applied_styles() {
        return get_option( $this->option_key, array() );
    }

    /**
     * 프론트엔드에 스타일 출력
     */
    public function output_styles() {
        $styles = $this->get_applied_styles();
        
        if ( empty( $styles ) ) {
            return;
        }
        
        echo '<style id="jj-wc-page-styler">' . "\n";
        
        foreach ( $styles as $target => $data ) {
            if ( $this->should_apply_style( $target ) ) {
                echo "/* WooCommerce {$target} Styling */\n";
                echo $data['css'] . "\n";
            }
        }
        
        echo '</style>' . "\n";
    }

    /**
     * 현재 페이지에 스타일 적용 여부
     */
    private function should_apply_style( $target ) {
        if ( ! function_exists( 'is_woocommerce' ) ) {
            return false;
        }
        
        switch ( $target ) {
            case 'product_archive':
                return is_shop() || is_product_category() || is_product_tag();
            case 'single_product':
                return is_product();
            case 'cart':
                return is_cart();
            case 'checkout':
                return is_checkout();
            default:
                return false;
        }
    }
}

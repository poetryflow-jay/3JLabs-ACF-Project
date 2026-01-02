<?php
/**
 * ACF CSS WooCommerce Toolkit - Cart Enhancer
 *
 * 장바구니 UI 개선
 *
 * @package ACF_CSS_WooCommerce_Toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Cart Enhancer 클래스
 */
class ACF_CSS_WC_Cart_Enhancer {

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
        // 장바구니 상품명 정리
        add_filter( 'woocommerce_cart_item_name', array( $this, 'cleanup_cart_item_name' ), 100, 3 );
        
        // 미니카트 상품명 정리
        add_filter( 'woocommerce_widget_cart_item_name', array( $this, 'cleanup_cart_item_name' ), 100, 3 );
    }

    /**
     * 장바구니 상품명 정리
     * 
     * 상품명 영역에 불필요한 요소가 출력되는 문제를 해결합니다.
     *
     * @param string $product_name 상품명
     * @param array $cart_item 장바구니 아이템
     * @param string $cart_item_key 장바구니 아이템 키
     * @return string 정리된 상품명
     */
    public function cleanup_cart_item_name( $product_name, $cart_item, $cart_item_key ) {
        // 장바구니, 체크아웃, AJAX 요청에서만 작동
        if ( is_cart() || is_checkout() || ( defined( 'WOOCOMMERCE_CART' ) && WOOCOMMERCE_CART ) ) {
            $_product = apply_filters( 
                'woocommerce_cart_item_product', 
                $cart_item['data'], 
                $cart_item, 
                $cart_item_key 
            );

            $product_permalink = apply_filters( 
                'woocommerce_cart_item_permalink', 
                $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', 
                $cart_item, 
                $cart_item_key 
            );

            // 깨끗한 상품명만 반환
            if ( $product_permalink ) {
                return sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() );
            } else {
                return $_product->get_name();
            }
        }

        return $product_name;
    }
}

// 인스턴스 초기화
ACF_CSS_WC_Cart_Enhancer::instance();

<?php
/**
 * ACF CSS WooCommerce Toolkit - Shortcodes
 *
 * 가격 관련 숏코드
 *
 * @package ACF_CSS_WooCommerce_Toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shortcodes 클래스
 */
class ACF_CSS_WC_Shortcodes {

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
        $this->register_shortcodes();
    }

    /**
     * 숏코드 등록
     */
    private function register_shortcodes() {
        // 통합 가격 숏코드
        add_shortcode( 'acf_wc_price', array( $this, 'price_shortcode' ) );
        add_shortcode( 'realdeal_price', array( $this, 'price_shortcode' ) ); // 호환성

        // 모듈형 숏코드
        add_shortcode( 'acf_wc_badge', array( $this, 'badge_shortcode' ) );
        add_shortcode( 'acf_wc_saved', array( $this, 'saved_shortcode' ) );
        add_shortcode( 'acf_wc_installments', array( $this, 'installments_shortcode' ) );
        add_shortcode( 'acf_wc_regular_price', array( $this, 'regular_price_shortcode' ) );
        add_shortcode( 'acf_wc_sale_price', array( $this, 'sale_price_shortcode' ) );

        // 호환성 숏코드 (rd_ 접두사)
        add_shortcode( 'rd_badge', array( $this, 'badge_shortcode' ) );
        add_shortcode( 'rd_summary', array( $this, 'saved_shortcode' ) );
        add_shortcode( 'rd_installments', array( $this, 'installments_shortcode' ) );
        add_shortcode( 'rd_regular_price', array( $this, 'regular_price_shortcode' ) );
        add_shortcode( 'rd_sale_price', array( $this, 'sale_price_shortcode' ) );
    }

    /**
     * 현재 상품 가져오기
     *
     * @return WC_Product|null
     */
    private function get_current_product() {
        global $product;
        
        if ( ! $product ) {
            $product = wc_get_product( get_the_ID() );
        }
        
        return $product;
    }

    /**
     * 통합 가격 숏코드
     *
     * @param array $atts 속성
     * @return string HTML
     */
    public function price_shortcode( $atts ) {
        $product = $this->get_current_product();
        if ( ! $product ) {
            return '';
        }

        $data = ACF_CSS_WC_Price_Engine::get_price_data( $product );
        if ( ! $data ) {
            return '';
        }

        return ACF_CSS_WC_Price_Engine::get_full_price_html( $data );
    }

    /**
     * 할인 배지 숏코드
     *
     * @param array $atts 속성
     * @return string HTML
     */
    public function badge_shortcode( $atts ) {
        $product = $this->get_current_product();
        if ( ! $product ) {
            return '';
        }

        $data = ACF_CSS_WC_Price_Engine::get_price_data( $product );
        if ( ! $data ) {
            return '';
        }

        return ACF_CSS_WC_Price_Engine::get_discount_badge_html( $data );
    }

    /**
     * 절약 금액 숏코드
     *
     * @param array $atts 속성
     * @return string HTML
     */
    public function saved_shortcode( $atts ) {
        $product = $this->get_current_product();
        if ( ! $product ) {
            return '';
        }

        $data = ACF_CSS_WC_Price_Engine::get_price_data( $product );
        if ( ! $data ) {
            return '';
        }

        return ACF_CSS_WC_Price_Engine::get_saved_amount_html( $data );
    }

    /**
     * 할부 정보 숏코드
     *
     * @param array $atts 속성
     * @return string HTML
     */
    public function installments_shortcode( $atts ) {
        $product = $this->get_current_product();
        if ( ! $product ) {
            return '';
        }

        $data = ACF_CSS_WC_Price_Engine::get_price_data( $product );
        if ( ! $data ) {
            return '';
        }

        return ACF_CSS_WC_Price_Engine::get_installment_html( $data );
    }

    /**
     * 정가 숏코드
     *
     * @param array $atts 속성
     * @return string HTML
     */
    public function regular_price_shortcode( $atts ) {
        $product = $this->get_current_product();
        if ( ! $product ) {
            return '';
        }

        $data = ACF_CSS_WC_Price_Engine::get_price_data( $product );
        if ( ! $data || ! $data['is_on_sale'] ) {
            return '';
        }

        return '<del aria-hidden="true">' . wc_price( $data['regular_price'] ) . '</del>';
    }

    /**
     * 할인가 숏코드
     *
     * @param array $atts 속성
     * @return string HTML
     */
    public function sale_price_shortcode( $atts ) {
        $product = $this->get_current_product();
        if ( ! $product ) {
            return '';
        }

        $data = ACF_CSS_WC_Price_Engine::get_price_data( $product );
        if ( ! $data ) {
            return '';
        }

        $price = $data['is_on_sale'] ? $data['sale_price'] : $data['regular_price'];
        return '<ins>' . wc_price( $price ) . '</ins>';
    }
}

// 인스턴스 초기화
ACF_CSS_WC_Shortcodes::instance();

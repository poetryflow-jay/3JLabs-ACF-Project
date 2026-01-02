<?php
/**
 * ACF CSS WooCommerce Toolkit - Price Engine
 *
 * 가격 계산 핵심 엔진
 *
 * @package ACF_CSS_WooCommerce_Toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Price Engine 클래스
 */
class ACF_CSS_WC_Price_Engine {

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
        // 할부 메타 저장
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_installment_meta' ) );
        
        // 번역 오류 수정
        add_filter( 'gettext', array( $this, 'fix_translation_error' ), 20, 3 );
    }

    /**
     * 상품 가격 데이터 계산
     *
     * @param WC_Product $product 상품 객체
     * @return array|null 가격 데이터 배열
     */
    public static function get_price_data( $product ) {
        if ( ! is_a( $product, 'WC_Product' ) ) {
            return null;
        }

        $data = array(
            'regular_price'       => (float) $product->get_regular_price(),
            'sale_price'          => (float) $product->get_sale_price(),
            'is_on_sale'          => $product->is_on_sale(),
            'saved_amount'        => 0,
            'discount_percentage' => 0,
            'installment_months'  => (int) get_post_meta( $product->get_id(), '_installment_months', true ),
            'installment_price'   => 0,
        );

        // 할인 정보 계산
        if ( $data['is_on_sale'] && ! empty( $data['sale_price'] ) && $data['regular_price'] > $data['sale_price'] ) {
            $data['saved_amount'] = $data['regular_price'] - $data['sale_price'];
            if ( $data['regular_price'] > 0 ) {
                $data['discount_percentage'] = round( ( $data['saved_amount'] / $data['regular_price'] ) * 100 );
            }
        }

        // 할부 가격 계산
        if ( $data['installment_months'] > 0 ) {
            $price_for_installment = $data['is_on_sale'] && ! empty( $data['sale_price'] ) 
                ? $data['sale_price'] 
                : $data['regular_price'];
            
            if ( $price_for_installment > 0 ) {
                $data['installment_price'] = round( ( $price_for_installment / $data['installment_months'] ), -2 );
            }
        }

        return $data;
    }

    /**
     * 할인 배지 HTML 생성
     *
     * @param array $data 가격 데이터
     * @return string HTML
     */
    public static function get_discount_badge_html( $data ) {
        if ( $data['discount_percentage'] > 0 ) {
            return sprintf(
                '<span class="acf-wc-discount-badge">%d%% OFF</span>',
                $data['discount_percentage']
            );
        }
        return '';
    }

    /**
     * 절약 금액 HTML 생성
     *
     * @param array $data 가격 데이터
     * @return string HTML
     */
    public static function get_saved_amount_html( $data ) {
        if ( $data['saved_amount'] > 0 ) {
            return sprintf(
                '<div class="acf-wc-discount-summary">✨ %s %s</div>',
                wp_strip_all_tags( wc_price( $data['saved_amount'] ) ),
                esc_html__( '절약', 'acf-css-woocommerce-toolkit' )
            );
        }
        return '';
    }

    /**
     * 할부 정보 HTML 생성
     *
     * @param array $data 가격 데이터
     * @return string HTML
     */
    public static function get_installment_html( $data ) {
        if ( $data['installment_price'] > 0 && $data['installment_months'] > 1 ) {
            return sprintf(
                '<small class="acf-wc-installment-price">(%s %s / %d%s)</small>',
                esc_html__( '월', 'acf-css-woocommerce-toolkit' ),
                number_format_i18n( $data['installment_price'] ) . get_woocommerce_currency_symbol(),
                $data['installment_months'],
                esc_html__( '개월', 'acf-css-woocommerce-toolkit' )
            );
        }
        return '';
    }

    /**
     * 전체 가격 HTML 생성
     *
     * @param array $data 가격 데이터
     * @return string HTML
     */
    public static function get_full_price_html( $data ) {
        $badge_html = self::get_discount_badge_html( $data );
        $summary_html = self::get_saved_amount_html( $data );
        $installments_html = self::get_installment_html( $data );

        if ( $data['is_on_sale'] ) {
            $price_html = '<del>' . wc_price( $data['regular_price'] ) . '</del> <ins>' . wc_price( $data['sale_price'] ) . '</ins>';
        } else {
            $price_html = '<ins>' . wc_price( $data['regular_price'] ) . '</ins>';
        }

        return sprintf(
            '<div class="acf-wc-price-wrapper price">%s%s%s%s</div>',
            $badge_html,
            $summary_html,
            $price_html,
            $installments_html ? '<br>' . $installments_html : ''
        );
    }

    /**
     * 할부 메타 저장
     *
     * @param int $post_id 상품 ID
     */
    public function save_installment_meta( $post_id ) {
        if ( isset( $_POST['_installment_months'] ) ) {
            update_post_meta( $post_id, '_installment_months', absint( $_POST['_installment_months'] ) );
        }
    }

    /**
     * 번역 오류 수정 (Saved → 절약)
     *
     * @param string $translated_text 번역된 텍스트
     * @param string $text 원본 텍스트
     * @param string $domain 텍스트 도메인
     * @return string 수정된 번역
     */
    public function fix_translation_error( $translated_text, $text, $domain ) {
        if ( 'Saved' === $text && '저장' === $translated_text ) {
            $translated_text = '절약';
        }
        return $translated_text;
    }
}

// 인스턴스 초기화
ACF_CSS_WC_Price_Engine::instance();

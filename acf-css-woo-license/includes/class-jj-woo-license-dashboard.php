<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * JJ WooCommerce License Dashboard
 * - 판매 내역 및 정산 정보 표시 (Master/Partner 에디션별 차별화)
 * 
 * @since v20.2.2
 */
class JJ_Woo_License_Dashboard {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 메뉴는 메인 클래스에서 등록
    }

    /**
     * 판매 내역 데이터 조회
     */
    public function get_sales_data( $limit = 20, $offset = 0 ) {
        $is_master = ( defined( 'ACF_CSS_WOO_LICENSE_EDITION' ) && ACF_CSS_WOO_LICENSE_EDITION === 'master' );
        
        $args = array(
            'limit' => $limit,
            'offset' => $offset,
            'status' => array( 'wc-completed', 'wc-processing' ),
            'orderby' => 'date',
            'order' => 'DESC',
            'return' => 'ids',
        );

        // 마스터가 아니면 자신의 상품만 조회되도록 필터링하는 로직이 필요함 (현재는 전체 주문에서 라이센스 주문만 필터링)
        $query = new WC_Order_Query( $args );
        $order_ids = $query->get_orders();
        
        $sales_data = array();
        foreach ( $order_ids as $order_id ) {
            $order = wc_get_order( $order_id );
            $licenses = $order->get_meta( '_acf_css_licenses' );
            
            if ( ! empty( $licenses ) ) {
                foreach ( $licenses as $license ) {
                    $sales_data[] = array(
                        'order_id'     => $order_id,
                        'customer'     => $order->get_billing_email(),
                        'product_name' => $license['product_name'],
                        'amount'       => $order->get_total(),
                        'date'         => $order->get_date_created()->date( 'Y-m-d H:i' ),
                        'status'       => $order->get_status(),
                    );
                }
            }
        }

        return $sales_data;
    }

    /**
     * 통계 데이터 조회
     */
    public function get_stats() {
        // [WIP] 실제 DB 쿼리를 통한 통계 계산 로직 구현 예정
        return array(
            'today_count' => 0,
            'month_total' => 0,
        );
    }
}

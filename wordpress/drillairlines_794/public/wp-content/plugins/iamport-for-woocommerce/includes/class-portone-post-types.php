<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Portone_Post_Type {
	public static function init() {
		add_filter( 'woocommerce_register_shop_order_post_statuses', array( __CLASS__, 'register_order_status' ) );
	}
	public static function register_order_status( $order_statuses ) {
		return array_merge( $order_statuses,
			array(
				'wc-refund-request'         => array(
					'label'                     => _x( '반품요청', 'Order status', 'iamport-for-woocommerce' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					// translators: %s: number of orders with a return request
					'label_count'               => _n_noop( "반품요청 <span class=\"count\">(%s)</span>", "반품요청 <span class=\"count\">(%s)</span>", 'iamport-for-woocommerce' )
				),
				'wc-exchange-request' => array(
					'label'                     => _x( '교환신청', 'Order status', 'iamport-for-woocommerce' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					// translators: %s: number of orders with a exchange request
					'label_count'               => _n_noop( '교환신청 <span class="count">(%s)</span>', '교환신청 <span class="count">(%s)</span>', 'iamport-for-woocommerce' )
				),
				'wc-address-changed'          => array(
					'label'                     => _x( '배송지 변경', 'Order status', 'iamport-for-woocommerce' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					// translators: %s: number of orders with change shipping address requests
					'label_count'               => _n_noop( '배송지 변경 <span class="count">(%s)</span>', '배송지 변경 <span class="count">(%s)</span>', 'iamport-for-woocommerce' )
				),
				'wc-awaiting-vbank'          => array(
					'label'                     => _x( '가상계좌 입금대기 중', 'Order status', 'iamport-for-woocommerce' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					// translators: %s: number of orders waiting for virtual account deposit
					'label_count'               => _n_noop( '가상계좌 입금대기 중 <span class="count">(%s)</span>', '가상계좌 입금대기 중 <span class="count">(%s)</span>', 'iamport-for-woocommerce' )
				),
			)
		);
	}

}

Portone_Post_Type::init();

<?php


/**
 * Plugin Name: 우커머스 포트원 플러그인 (국내 모든 PG를 한 번에)
 * Plugin URI: 
 * Description: 대한민국 모든 PG를 한번에 연동할 수 있는 결제 플러그인 (신용카드 / 실시간계좌이체 / 가상계좌 / 휴대폰소액결제 / 에스크로)
 * Version: 3.3.11
 * Requires Plugins: woocommerce
 *
 * Author: PortOne
 * Author URI: https://portone.io/
 * Requires at least: 4.6
 * Requires PHP: 7.4
 * License: GPLv2 or later
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
}

if ( ! class_exists( 'Portone_For_WooCommerce' ) ) {
	class Portone_For_WooCommerce {

		private static $_instance = null;
		protected $slug;
		protected $version = '3.3.11';
		protected $plugin_url;
		protected $plugin_path;
		public function __construct() {
			$this->slug = 'iamport-for-woocommerce';


			define( 'PORTONE_SLUG', $this->slug );
			define( 'PORTONE_PLUGIN_DIR', __DIR__ );
			define( 'PORTONE_PLUGIN_FILE', __FILE__ );
			define( 'PORTONE_VERSION', $this->version );

			require_once( 'includes/portone-hpos.php' );
			require_once( 'includes/portone-settings-tab.php' );
			require_once( 'includes/class-portone-helper.php' );
			require_once( 'includes/portone-functions.php' );
			require_once( 'includes/class-portone-tax.php' );
			require_once( 'includes/portone-email.php' );

			require_once( 'includes/portone-template-functions.php' );
			require_once( 'includes/portone-template-hooks.php' );

			require_once( 'includes/class-portone-post-types.php' );

			add_action( 'before_woocommerce_init', array( $this, 'declare_woocommerce_compatibility' ) );
			add_filter( 'woocommerce_payment_gateways', array( $this, 'woocommerce_payment_gateways' ), 1 );
			add_action( 'woocommerce_blocks_loaded', array( $this, 'woocommerce_blocks_loaded' ) );

			add_action( 'woocommerce_init', array( $this, 'woocommerce_init' ) );
		}
		public function plugin_url() {
			if ( $this->plugin_url ) {
				return $this->plugin_url;
			}

			return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		}
		public function plugin_path() {
			if ( $this->plugin_path ) {
				return $this->plugin_path;
			}

			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}
		public function template_path() {
			return $this->plugin_path() . '/templates/';
		}

		public function woocommerce_init() {
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
			add_filter( 'wc_order_statuses', array( $this, 'add_order_statuses' ), 10, 1 );

			$this->includes();
		}
		public function declare_woocommerce_compatibility() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		}
		function woocommerce_payment_gateways( $methods ) {
			require_once( 'includes/abstracts/abstract-portone-payment-gateway.php' );
			foreach ( glob( PORTONE_PLUGIN_DIR . "/includes/gateways/*.php" ) as $gateway ) {
				require_once( $gateway );
			}

			$methods = array_merge( $methods, array(
				'WC_Gateway_Portone_Card',
				'WC_Gateway_Portone_Samsung',
				'WC_Gateway_Portone_Vbank',
				'WC_Gateway_Portone_Trans',
				'WC_Gateway_Portone_Phone',
				'WC_Gateway_Portone_Kakao',
				'WC_Gateway_Portone_Paymentwall',
				'WC_Gateway_Portone_Subscription',
				'WC_Gateway_Portone_Subscription_Ex',
				'WC_Gateway_Portone_Hub_NaverPay',
				'WC_Gateway_Portone_Eximbay',
			) );

			if ( iamport_naverpay_ext_enable_status() ) {
				$methods = array_merge( $methods, array(
					'WC_Gateway_Portone_NaverPayExt',
				) );
			}

			return $methods;
		}
		function get_supported_gateway_ids() {
			$methods = array(
				'iamport_card',
				'iamport_eximbay',
				'iamport_kakao',
				'iamport_hub_naverpay',
				'iamport_paymentwall',
				'iamport_phone',
				'iamport_samsung',
				'iamport_subscription_ex',
				'iamport_trans',
				'iamport_vbank',
			);

			if ( iamport_naverpay_ext_enable_status() ) {
				$methods = array_merge( $methods, array(
					'iamport_naverpay_ext',
				) );
			}

			return $methods;
		}
		function woocommerce_blocks_loaded() {
			if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
				require_once( 'includes/abstracts/abstract-portone-payments-blocks.php' );

				add_action(
					'woocommerce_blocks_payment_method_type_registration',
					function ( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
						foreach ( PORTONE()->get_supported_gateway_ids() as $gateway_id ) {
							$payment_method_registry->register( new PORTONE_Payment_Gateway_Blocks_Support( $gateway_id ) );
						}
					}
				);
			}
		}
		function includes() {
			if ( is_admin() ) {
				$this->admin_includes();
			}

			if ( defined( 'DOING_AJAX' ) ) {
				$this->ajax_includes();
			}

			if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
				$this->frontend_includes();
			}
		}

		public function admin_includes() {
		}

		public function ajax_includes() {
		}

		public function frontend_includes() {
		}
		public function wp_enqueue_scripts( $force = false ) {
			if ( $force || ( is_checkout() && ! is_order_received_page() && ( ! function_exists( 'is_pafw_dc_checkout_page' ) || ! is_pafw_dc_checkout_page() ) ) ) {
				list( $sdk_version, $cache_version ) = explode( '/', get_option( 'woocommerce_iamport_sdk_version', '1.1.7/20190812' ) );

				$cache_version = 'latest' != $cache_version ? $cache_version : null;

				if ( '1.3.0' == $sdk_version ) {
					wp_register_script( 'woocommerce_portone_script', 'https://cdn.iamport.kr/v1/iamport.js', array( 'jquery' ), $cache_version );
				} else {
					wp_register_script( 'woocommerce_portone_script', 'https://cdn.iamport.kr/js/iamport.payment-' . $sdk_version . '.js', array( 'jquery' ), $cache_version );
				}
				wp_register_script( 'portone_jquery_url', plugins_url( '/assets/js/url.min.js', PORTONE_PLUGIN_FILE ), array(), '20190918' );
				wp_register_script( 'portone_script_for_woocommerce', plugins_url( '/assets/js/portone.woocommerce.js', PORTONE_PLUGIN_FILE ), array( 'jquery', 'portone_jquery_url' ), '20200925' );
				wp_register_script( 'samsung_runnable', 'https://d3sfvyfh4b9elq.cloudfront.net/pmt/web/device.json' );

				wp_enqueue_script( 'woocommerce_portone_script' );
				wp_enqueue_script( 'portone_jquery_url' );
				wp_enqueue_script( 'portone_script_for_woocommerce' );
				wp_enqueue_script( 'samsung_runnable' );
			}
		}
		function add_order_statuses( $order_statuses ) {

			$new_order_statuses = array();

			// cancelled status다음에 추가
			foreach ( $order_statuses as $key => $status ) {

				$new_order_statuses[ $key ] = $status;

				if ( 'wc-cancelled' === $key ) {
					$new_order_statuses[ 'wc-refund-request' ]   = Portone_Helper::display_label( Portone_Helper::STATUS_REFUND );
					$new_order_statuses[ 'wc-exchange-request' ] = Portone_Helper::display_label( Portone_Helper::STATUS_EXCHANGE );
					$new_order_statuses[ 'wc-address-changed' ]  = Portone_Helper::display_label( Portone_Helper::STATUS_AWAITING_VBANK );
					$new_order_statuses[ 'wc-awaiting-vbank' ]   = Portone_Helper::DEFAULT_STATUS_ADDRESS_CHANGED;;
				}
			}

			return $new_order_statuses;


		}

		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'iamport-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . "/languages/" );
		}

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
	function PORTONE() {
		return Portone_For_WooCommerce::instance();
	}

	return PORTONE();

}

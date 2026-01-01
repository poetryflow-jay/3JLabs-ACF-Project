<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

abstract class Abstract_PORTONE_Payment_Blocks_Support extends AbstractPaymentMethodType {
	protected $gateway;
	protected $settings;
	protected static $is_enqueued = false;
	public function initialize() {
		$gateways = WC()->payment_gateways->payment_gateways();

		$this->gateway = $gateways[ $this->name ];

		$this->settings = $this->gateway->settings;
	}
	public function is_active() {
		return $this->gateway->is_available();
	}
	public function get_payment_method_script_handles() {
		if ( ! self::$is_enqueued ) {
			$script_path       = '/assets/blocks/js/frontend/blocks.js';
			$script_asset_path = PORTONE_PLUGIN_DIR . '/assets/blocks/js/frontend/blocks.asset.php';

			$script_asset = file_exists( $script_asset_path )
				? require( $script_asset_path )
				: array(
					'dependencies' => array(),
					'version'      => PORTONE_VERSION
				);

			wp_register_script(
				'portone-payments-blocks',
				plugins_url( $script_path, PORTONE_PLUGIN_FILE ),
				$script_asset['dependencies'],
				$script_asset['version'],
				true
			);

			wp_localize_script( 'portone-payments-blocks', '_portone_payment_blocks', array(
				'supported_payment_methods' => PORTONE()->get_supported_gateway_ids(),
			) );

			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'portone-payments-blocks', 'iamport-for-woocommerce', PORTONE_PLUGIN_DIR . '/languages/' );
			}

			self::$is_enqueued = true;
		}

		return [ 'portone-payments-blocks' ];
	}
	public function get_payment_method_data() {
		$payment_gateway = $this->gateway;

		return [
			'title'         => $payment_gateway->get_title(),
			'description'   => $this->get_setting( 'description' ),
			'name'          => $this->name,
			'supports'      => array_filter( $payment_gateway->supports, [ $payment_gateway, 'supports' ] ),
			'uuid'          => wp_generate_uuid4(),
			'quotas'        => array_filter( array_merge( array( "00" ), explode( ',', portone_get( $payment_gateway->settings, 'quota' ) ) ) ),
		];
	}
}

class PORTONE_Payment_Gateway_Blocks_Support extends Abstract_PORTONE_Payment_Blocks_Support {
	public function __construct( $payment_method ) {
		$this->name = $payment_method;
	}
}

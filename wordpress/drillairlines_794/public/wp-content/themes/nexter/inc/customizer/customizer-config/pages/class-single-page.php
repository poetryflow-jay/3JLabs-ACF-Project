<?php
/**
 * Single Page Options for Nexter Theme.
 *
 * @package     Nexter
 * @since       Nexter 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Nexter_Single_Page' ) ) {

	class Nexter_Single_Page extends Nexter_Customizer_Config {

		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			parent::__construct();
		}
		
		/**
		 * Register Single Page Options Customizer Configurations.
		 * @since 1.0.0
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$options = array(
				array(
					'name'      => NXT_OPTIONS . '[heading-single-page]',
					'type'      => 'control',
					'control'   => 'nxt-heading',
					'section'   => 'section-page-single',
					'priority'  => 4,
					'title'     => __( 'Single Page', 'nexter' ),
					'settings'  => array(),
					'separator' => false,
				),
				array(
					'name'      => NXT_OPTIONS . '[page-hide-fea-image]',
					'type'      => 'control',
					'control'   => 'nxt-switcher',
					'section'   => 'section-page-single',
					'priority'  => 6,
					'default' 	=> 'off',
					'title'     => __( 'Hide Featured Image', 'nexter' ),
					'choices'  => array(
						'off'	=> __( 'OFF', 'nexter' ),
						'on'	=> __( 'ON', 'nexter' ),
					),
				),
			);

			return array_merge( $configurations, $options );
		}
		
	}
}

new Nexter_Single_Page;
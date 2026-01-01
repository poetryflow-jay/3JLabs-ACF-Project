<?php
/**
 * Whole Site Text Selection Options for Nexter Theme.
 *
 * @package     Nexter
 * @since       Nexter 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Nexter_Site_Text_Selection_Style' ) ) {

	class Nexter_Site_Text_Selection_Style extends Nexter_Customizer_Config {
		
		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			parent::__construct();
		}
		
		/**
		 * Whole Site Text Selection Style Customizer.
		 * @since 1.0.0
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$options = array(

				array(
					'name'      => NXT_OPTIONS . '[heading-text-selection]',
					'type'      => 'control',
					'control'   => 'nxt-heading',
					'section'   => 'section-selected-text-style',
					'priority'  => 5,
					'title'     => __( 'Selection Text/Content', 'nexter' ),
					'settings'  => array(),
					'separator' => false,
				),
				array(
					'name'     => NXT_OPTIONS . '[selected-text-bg-color]',
					'type'     => 'control',
					'control'  => 'nxt-color',
					'section'  => 'section-selected-text-style',
					'transport' => 'postMessage',
					'default'  => '#ff5a6e',
					'priority' => 10,
					'title'    => __( 'Selected Text Background Color', 'nexter' ),					
				),
				array(
					'name'     => NXT_OPTIONS . '[selected-text-color]',
					'type'     => 'control',
					'control'  => 'nxt-color',
					'section'  => 'section-selected-text-style',
					'transport' => 'postMessage',
					'default'  => '#fff',
					'priority' => 15,
					'title'    => __( 'Selected Text Color', 'nexter' ),
				),
			);

			return array_merge( $configurations, $options );
		}
		
	}
}

new Nexter_Site_Text_Selection_Style;
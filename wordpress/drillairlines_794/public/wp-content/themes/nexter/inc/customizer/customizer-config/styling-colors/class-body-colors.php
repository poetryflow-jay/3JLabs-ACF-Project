<?php
/**
 * Body Color Styling Options for Nexter Theme.
 *
 * @package     Nexter
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Nexter_Body_General_Colors' ) ) {

	class Nexter_Body_General_Colors extends Nexter_Customizer_Config {
		
		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			parent::__construct();
		}
		
		/**
		 * Body Color Customizer.
		 * @since 1.0.0
		 */
		public function register_configuration( $configurations, $wp_customize ) {
			$options = array(

				/** Start
				 * Options Body Styling Color
				 */
				array(
					'name'      => NXT_OPTIONS . '[heading-body-color]',
					'type'      => 'control',
					'control'   => 'nxt-heading',
					'section'   => 'section-body-colors',
					'priority'  => 4,
					'title'     => __( 'Body Color', 'nexter' ),
					'settings'  => array(),
					'separator' => false,
				),
				array(
					'name'     => NXT_OPTIONS . '[body-color]',
					'type'     => 'control',
					'control'  => 'nxt-color',
					'section'  => 'section-body-colors',
					'default'  => '#888',
					'transport' => 'postMessage',
					'priority' => 5,
					'title'    => __( 'Text Color', 'nexter' ),
				),
				array(
					'name'     => NXT_OPTIONS . '[link-color]',
					'section'  => 'section-body-colors',
					'type'     => 'control',
					'control'  => 'nxt-color',
					'default'  => '#8072fc',
					'priority' => 5,
					'title'    => __( 'Link Color', 'nexter' ),
				),
				array(
					'name'     => NXT_OPTIONS . '[link-hover-color]',
					'section'  => 'section-body-colors',
					'default'  => '#ff5a6e',
					'type'     => 'control',
					'control'  => 'nxt-color',
					'priority' => 10,
					'title'    => __( 'Link Hover Color', 'nexter' ),
				),
				/** End
				 * Options Body Styling Color
				 */
			);

			return array_merge( $configurations, $options );
		}

	}
}

new Nexter_Body_General_Colors;
<?php
/**
 * Heading Color Styling Options for Nexter Theme.
 *
 * @package	Nexter
 * @since	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Nexter_Heading_Colors' ) ) {

	class Nexter_Heading_Colors extends Nexter_Customizer_Config {
		
		/**
		 * Constructor
		 */
		public function __construct() {
			parent::__construct();
		}
		
		/**
		 * Heading Color Customizer.
		 * @since 1.0.0
		 */
		public function register_configuration( $configurations, $wp_customize ) {
			$options = array(

				/** Start
				 * Options Heading Styling Color
				 */
				array(
					'name'      => NXT_OPTIONS . '[heading-colors]',
					'type'      => 'control',
					'control'   => 'nxt-heading',
					'section'   => 'section-heading-colors',
					'priority'  => 4,
					'title'     => __( 'Heading (H1-H6) Color', 'nexter' ),
					'settings'  => array(),
					'separator' => false,
				),
				array(
					'name'     => NXT_OPTIONS . '[heading-color-h1]',
					'type'     => 'control',
					'control'  => 'nxt-color',
					'section'  => 'section-heading-colors',
					'default'  => '#313131',
					'transport' => 'postMessage',
					'priority' => 5,
					'title'    => __( 'Heading (H1) Color', 'nexter' ),
				),
				array(
					'name'     => NXT_OPTIONS . '[heading-color-h2]',
					'type'     => 'control',
					'control'  => 'nxt-color',
					'section'  => 'section-heading-colors',
					'default'  => '#313131',
					'transport' => 'postMessage',
					'priority' => 10,
					'title'    => __( 'Heading (H2) Color', 'nexter' ),
				),
				array(
					'name'     => NXT_OPTIONS . '[heading-color-h3]',
					'type'     => 'control',
					'control'  => 'nxt-color',
					'section'  => 'section-heading-colors',
					'default'  => '#313131',
					'transport' => 'postMessage',
					'priority' => 15,
					'title'    => __( 'Heading (H3) Color', 'nexter' ),
				),
				array(
					'name'     => NXT_OPTIONS . '[heading-color-h4]',
					'type'     => 'control',
					'control'  => 'nxt-color',
					'section'  => 'section-heading-colors',
					'default'  => '#313131',
					'transport' => 'postMessage',
					'priority' => 20,
					'title'    => __( 'Heading (H4) Color', 'nexter' ),
				),
				array(
					'name'     => NXT_OPTIONS . '[heading-color-h5]',
					'type'     => 'control',
					'control'  => 'nxt-color',
					'section'  => 'section-heading-colors',
					'default'  => '#808285',
					'transport' => 'postMessage',
					'priority' => 25,
					'title'    => __( 'Heading (H5) Color', 'nexter' ),
				),
				array(
					'name'     => NXT_OPTIONS . '[heading-color-h6]',
					'type'     => 'control',
					'control'  => 'nxt-color',
					'section'  => 'section-heading-colors',
					'default'  => '#808285',
					'transport' => 'postMessage',
					'priority' => 30,
					'title'    => __( 'Heading (H6) Color', 'nexter' ),
				),
				
				/** End
				 * Options Body Styling Color
				 */
			);

			return array_merge( $configurations, $options );
		}
		
	}
}

new Nexter_Heading_Colors;
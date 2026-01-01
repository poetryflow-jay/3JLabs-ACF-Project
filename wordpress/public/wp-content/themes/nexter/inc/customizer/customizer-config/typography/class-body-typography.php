<?php
/**
 * Body Typography Styling Options for Nexter Theme.
 *
 * @package	Nexter
 * @since	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Nexter_Body_Typography' ) ) {

	class Nexter_Body_Typography extends Nexter_Customizer_Config {
		
		/**
		 * Constructor
		 */
		public function __construct() {
			parent::__construct();
		}
		
		/**
		 * Body Typography Customizer.
		 * @since 1.0.0
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$options = array(

				/** Start
				 * Options Body Typography
				 */
				array(
					'name'      => NXT_OPTIONS . '[heading-body-typo]',
					'type'      => 'control',
					'control'   => 'nxt-heading',
					'section'   => 'section-body-typography',
					'priority'  => 4,
					'title'     => __( 'Body', 'nexter' ),
					'settings'  => array(),
					'separator' => false,
				),
				array(
					'name'        => NXT_OPTIONS . '[body-font-family]',
					'type'        => 'control',
					'control'     => 'nxt-font-control',
					'section'     => 'section-body-typography',
					'font-type'   => 'nxt-font-family',
					'font_inherit' => __( 'Default System Font', 'nexter' ),
					'default'     => nexter_get_option( 'body-font-family' ),
					'priority'    => 5,
					'title'       => __( 'Font Family', 'nexter' ),
					'connect'     => NXT_OPTIONS . '[body-font-weight]',
					'variant'     => NXT_OPTIONS . '[body-font-variant]',
				),
				array(
					'name'              => NXT_OPTIONS . '[body-font-variant]',
					'type'              => 'control',
					'control'           => 'nxt-font-control',
					'section'           => 'section-body-typography',
					'font-type'         => 'nxt-font-variant',
					'sanitize_callback' => array( 'Nexter_Customizer_Sanitizes_Callbacks', 'sanitize_font_variant' ),
					'default'           => nexter_get_option( 'body-font-variant' ),
					'font_inherit'       => __( 'Default', 'nexter' ),					
					'priority'          => 10,
					'title'             => __( 'Font Variant', 'nexter' ),
					'variant'           => NXT_OPTIONS . '[body-font-family]',
				),
				array(
					'name'              => NXT_OPTIONS . '[body-font-weight]',
					'type'              => 'control',
					'control'           => 'nxt-font-control',
					'section'           => 'section-body-typography',
					'font-type'         => 'nxt-font-weight',
					'sanitize_callback' => array( 'Nexter_Customizer_Sanitizes_Callbacks', 'sanitize_font_weight' ),
					'default'           => nexter_get_option( 'body-font-weight' ),
					'font_inherit'       => __( 'Default', 'nexter' ),					
					'priority'          => 10,
					'title'             => __( 'Font Weight', 'nexter' ),
					'connect'           => NXT_OPTIONS . '[body-font-family]',
				),
				array(
					'name'     => NXT_OPTIONS . '[body-transform]',
					'type'     => 'control',
					'control'  => 'nxt-text-transform',
					'section'  => 'section-body-typography',
					'transport'         => 'postMessage',
					'default'  => nexter_get_option( 'body-transform' ),
					'priority' => 15,
					'title'    => __( 'Text Transform', 'nexter' ),
					'choices'  => array(
						''           => __( 'Default', 'nexter' ),
						// 'none'       => __( 'None', 'nexter' ),
						'capitalize' => __( 'Capitalize', 'nexter' ),
						'uppercase'  => __( 'Uppercase', 'nexter' ),
						'lowercase'  => __( 'Lowercase', 'nexter' ),
					),
				),
				array(
					'name'        => NXT_OPTIONS . '[font-size-body]',
					'default'     => nexter_get_option( 'font-size-body'),
					'title'       => __( 'Font Size', 'nexter' ),
					'type'        => 'control',
					'transport'   => 'postMessage',
					'control'     => 'nxt-responsive-slider',
					'section'     => 'section-body-typography',
					'priority'    => 20,
					'input_attrs' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1
					),
					'units'       => array(
						'px' => 'px',
						'em' => 'em',
					),
				),
				array(
					'name'        => NXT_OPTIONS . '[body-line-height]',
					'default'     => nexter_get_option( 'body-line-height' ),
					'title'       => __( 'Line Height', 'nexter' ),
					'type'        => 'control',
					'transport'   => 'postMessage',
					'control'     => 'nxt-responsive-slider',
					'section'     => 'section-body-typography',
					'priority'    => 25,
					'input_attrs' => array(
						'min'  => 0.5,
						'step' => 0.01,
						'max'  => 15,
					),
				),
				/** End
				 * Options Body Typography
				 */
				
				/**
				 * Options Paragraph (P) Margin Bottom
				 */
				array(
					'name'              => NXT_OPTIONS . '[paragraph-mb]',
					'type'              => 'control',
					'control'           => 'nxt-slider',
					'default'           => '',
					'sanitize_callback' => array( 'Nexter_Customizer_Sanitizes_Callbacks', 'sanitize_number_val' ),
					'transport'         => 'postMessage',
					'section'           => 'section-body-typography',
					'priority'          => 25,
					'title'             => __( 'Paragraph (P) Margin Bottom', 'nexter' ),
					'suffix'            => '',
					'input_attrs'       => array(
						'min'  => 0,
						'step' => 0.01,
						'max'  => 5,
					),
				),

			);

			return array_merge( $configurations, $options );
		}

	}
}

new Nexter_Body_Typography;
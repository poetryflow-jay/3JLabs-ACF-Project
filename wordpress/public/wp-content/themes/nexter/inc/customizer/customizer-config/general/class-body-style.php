<?php
/**
 * Body Style Options for Nexter Theme.
 *
 * @package	Nexter
 * @since	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Nexter_Body_Style' ) ) {

	class Nexter_Body_Style extends Nexter_Customizer_Config {
		
		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			parent::__construct();
		}
		
		/**
		 * Register Body Style Customizer Configurations.
		 * @since 1.0.0
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$options = array(

				/** Start
				 * Options Body Background Color
				 */
				array(
					'name'      => NXT_OPTIONS . '[heading-body-bgcolor]',
					'type'      => 'control',
					'control'   => 'nxt-heading',
					'section'   => 'section-body-style',
					'priority'  => 5,
					'title'     => __( 'Body Background', 'nexter' ),
					'settings'  => array(),
					'separator' => false,
				),
				array(
					'name'     => NXT_OPTIONS . '[body-bgcolor]',
					'type'     => 'control',
					'control'  => 'nxt-background',
					'default'  => array('background-color'=>'#fff'),
					'section'  => 'section-body-style',
					'priority' => 10,
					'title'    => __( 'Background', 'nexter' ),
				),
				/** End
				 * Options Body Background Color
				 */
				 
				 /** Start
				 * Options Content Background Color
				 */
				array(
					'name'      => NXT_OPTIONS . '[heading-content-bgcolor]',
					'type'      => 'control',
					'control'   => 'nxt-heading',
					'section'   => 'section-body-style',
					'priority'  => 12,
					'title'     => __( 'Content Background', 'nexter' ),
					'settings'  => array(),
					'separator' => false,
				),
				array(
					'name'     => NXT_OPTIONS . '[content-bgcolor]',
					'type'     => 'control',
					'control'  => 'nxt-background',
					'default'  => array('background-color'=>'#fff'),
					'section'  => 'section-body-style',
					'priority' => 14,
					'title'    => __( 'Content Background', 'nexter' ),
				),
				/** End
				 * Options Content Background Color
				 */
				 
				array(
					'name'      => NXT_OPTIONS . '[heading-body-frame]',
					'type'      => 'control',
					'control'   => 'nxt-heading',
					'section'   => 'section-body-style',
					'priority'  => 15,
					'title'     => __( 'Body Frame', 'nexter' ),
					'settings'  => array(),
					'separator' => false,
				),				
				array(
					'name'           => NXT_OPTIONS . '[body-frame-padding]',
					'default'        => nexter_get_option( 'body-frame-padding' ),
					'type'           => 'control',
					'control'        => 'nxt-responsive-spacing',
					'section'        => 'section-body-style',
					'transport'      => 'postMessage',
					'priority'       => 20,
					'title'          => __( 'Body Frame (Space)', 'nexter' ),
					'linked' => true,
					'unit'   => array( 'px', 'em', '%' ),
					'choices'        => array(
						'top'    => __( 'Top', 'nexter' ),
						'right'  => __( 'Right', 'nexter' ),
						'bottom' => __( 'Bottom', 'nexter' ),
						'left'   => __( 'Left', 'nexter' ),
					),
				),
				array(
					'name'      => NXT_OPTIONS . '[fixed-body-frame]',
					'type'      => 'control',
					'control'   => 'nxt-switcher',
					'section'   => 'section-body-style',
					'priority'  => 25,
					'default' 	=> 'off',
					'title'     => __( 'Fixed Body Frame', 'nexter' ),
					'choices'  => array(
						'off'	=> __( 'OFF', 'nexter' ),
						'on'	=> __( 'ON', 'nexter' ),
					),
				),
				array(
					'name'     => NXT_OPTIONS . '[body-frame-color]',
					'type'     => 'control',
					'control'  => 'nxt-color',
					'section'  => 'section-body-style',
					'default'  => '#888',
					'priority' => 30,
					'title'    => __( 'Frame Color', 'nexter' ),
					'conditional' => array( NXT_OPTIONS . '[fixed-body-frame]', '==', 'on' ),
				),
			);

			return array_merge( $configurations, $options );
		}

	}
}

new Nexter_Body_Style;



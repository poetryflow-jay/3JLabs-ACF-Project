<?php
/**
 * Container Layout Options for Nexter Theme.
 *
 * @package	Nexter
 * @since	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Nexter_Layout_Container' ) ) {

	class Nexter_Layout_Container extends Nexter_Customizer_Config {
		
		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			parent::__construct();
		}
		
		/**
		 * Container Layout Customizer.
		 * @since 1.0.0
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$image_path = NXT_THEME_URI.'assets/images/customizer/sidebar/width/';

			$header_footer_container = [];
			
			/* $header_width = nexter_get_option( 'site-header-container-width' );
			$header_default = '';
			if ( (isset($header_width['desktop']) && $header_width['desktop'] != '') || (isset($header_width['tablet']) && $header_width['tablet'] != '') ) {
				$header_default = 'container-block-editor';
			}

			$footer_width = nexter_get_option( 'site-footer-container-width' );
			$footer_default = '';
			if ( (isset($footer_width['desktop']) && $footer_width['desktop'] != '') || (isset($footer_width['tablet']) && $footer_width['tablet'] != '') ) {
				$footer_default = 'container-block-editor';
			} */

			if( nexter_settings_page_get( 'header_footer_css' ) ){
				$header_footer_container = array(
					array(
						'name'     => NXT_OPTIONS . '[site-header-container]',
						'default'  => '',
						'title'    => __( 'Header', 'nexter' ),
						'type'     => 'control',
						'control'  => 'nxt-style', // Change to custom control type
						'section'  => 'section-site-layout-container',
						'priority' => 5,
						'choices'  => array(
							'' => array(
								'image' => $image_path . 'default.png',
								'title' => __( 'Default', 'nexter' ),
							),
							'container-block-editor' => array(
								'image' => $image_path . 'container.png',
								'title' => __( 'Container', 'nexter' ),
							),
							'container-fluid' => array(
								'image' => $image_path . 'full-width.png',
								'title' => __( 'Full Width', 'nexter' ),
							),
						),
						'input_attrs' => array(
							'name' => NXT_OPTIONS . '[site-header-container]',
						),
					),
					array(
						'name'        => NXT_OPTIONS . '[site-header-container-width]',
						'default'     => nexter_get_option( 'site-header-container-width', ['desktop' => 1140, 'tablet' => 960 ] ),
						'title'       => __( 'Container(Wide) Width', 'nexter' ),
						'type'        => 'control',
						'transport'   => 'postMessage',
						'control'     => 'nxt-responsive-slider',
						'section'     => 'section-site-layout-container',
						'priority'    => 5,
						'input_attrs' => array(
							'min'  => 300,
							'step' => 1,
							'max'  => 2000,
						),
						'conditional' => array( NXT_OPTIONS . '[site-header-container]', '==', array('container-block-editor','container') ),
					),
					array(
						'name'           => NXT_OPTIONS . '[header-fluid-spacing]',
						'default'        => nexter_get_option( 'header-fluid-spacing' ) ? nexter_get_option( 'header-fluid-spacing' ) : array(
							'md' => ['left' => 15, 'right' => 15],
							'sm' => ['left' => '', 'right' => ''],
							'xs' => ['left' => '', 'right' => ''],
							'md-unit' => 'px',
							'sm-unit' => 'px',
							'xs-unit' => 'px',
						),
						'type'           => 'control',
						'control'        => 'nxt-responsive-spacing',
						'section'        => 'section-site-layout-container',
						'transport'      => 'postMessage',
						'priority'       => 5,
						'title'          => __( 'Spacing(Padding)', 'nexter' ),
						'linked' => true,
						'unit'   => array( 'px', 'em' ),
						'choices'        => array(
							'left'   => __( 'Left', 'nexter' ),
							'right'  => __( 'Right', 'nexter' ),
						),
						'conditional' => array( NXT_OPTIONS . '[site-header-container]', '==', 'container-fluid' ),
					),
					/*Header Container*/
					/*footer Container*/
					array(
						'name'     => NXT_OPTIONS . '[heading-footer-divider]',
						'type'     => 'control',					
						'control'  => 'nxt-heading',
						'section'  => 'section-site-layout-container',
						'priority' => 5,
						'settings' => array(),					
					),

					array(
						'name'     => NXT_OPTIONS . '[site-footer-container]',
						'default'  => '',
						'title'    => __( 'Footer', 'nexter' ),
						'type'     => 'control',
						'control'  => 'nxt-style', // Change to custom control type
						'section'  => 'section-site-layout-container',
						'priority' => 5,
						'choices'  => array(
							'' => array(
								'image' => $image_path . 'default.png',
								'title' => __( 'Default', 'nexter' ),
							),
							'container-block-editor' => array(
								'image' => $image_path . 'container.png',
								'title' => __( 'Container', 'nexter' ),
							),
							'container-fluid' => array(
								'image' => $image_path . 'full-width.png',
								'title' => __( 'Full Width', 'nexter' ),
							),
						),
						'input_attrs' => array(
							'name' => NXT_OPTIONS . '[site-footer-container]',
						),
					),
					array(
						'name'        => NXT_OPTIONS . '[site-footer-container-width]',
						'default'     => nexter_get_option( 'site-footer-container-width', [ 'desktop' => 1140, 'tablet' => 960 ] ),
						'title'       => __( 'Container(Wide) Width', 'nexter' ),
						'type'        => 'control',
						'transport'   => 'postMessage',
						'control'     => 'nxt-responsive-slider',
						'section'     => 'section-site-layout-container',
						'priority'    => 5,
						'input_attrs' => array(
							'min'  => 300,
							'step' => 1,
							'max'  => 2000,
						),
						'conditional' => array( NXT_OPTIONS . '[site-footer-container]', '==', array('container-block-editor','container') ),
					),
					array(
						'name'           => NXT_OPTIONS . '[footer-fluid-spacing]',
						'default'        => nexter_get_option( 'footer-fluid-spacing' ) ? nexter_get_option( 'footer-fluid-spacing' ) : array(
							'md' => ['left' => 15, 'right' => 15],
							'sm' => ['left' => '', 'right' => ''],
							'xs' => ['left' => '', 'right' => ''],
							'md-unit' => 'px',
							'sm-unit' => 'px',
							'xs-unit' => 'px',
						),
						'type'           => 'control',
						'control'        => 'nxt-responsive-spacing',
						'section'        => 'section-site-layout-container',
						'transport'      => 'postMessage',
						'priority'       => 5,
						'title'          => __( 'Spacing(Padding)', 'nexter' ),
						'linked' => true,
						'unit'   => array( 'px', 'em' ),
						'choices'        => array(
							'left'   => __( 'Left', 'nexter' ),
							'right'  => __( 'Right', 'nexter' ),
						),
						'conditional' => array( NXT_OPTIONS . '[site-footer-container]', '==', 'container-fluid' ),
					),
					/*Footer Container*/
					
				);
			}

			$options = array(
				/** Start
				 * Options Layout/Container
				 */
				array(
					'name'      => NXT_OPTIONS . '[heading-site-container]',
					'type'      => 'control',
					'control'   => 'nxt-heading',
					'section'   => 'section-site-layout-container',
					'priority'  => 4,
					'title'     => __( 'Container', 'nexter' ),
					'settings'  => array(),
					'separator' => false,
				),
				/*Content/Body Container*/
				array(
					'name'     => NXT_OPTIONS . '[site-layout-container]',
					'default'  => 'container-block-editor',
					'title'    => __( 'Site Container', 'nexter' ),
					'type'     => 'control',
					'control'  => 'nxt-style', // Change to custom control type
					'section'  => 'section-site-layout-container',
					'priority' => 5,
					'choices'  => array(
						'container-block-editor' => array(
							'image' => $image_path . 'container.png',
							'title' => __( 'Container', 'nexter' ),
						),
						'container-fluid' => array(
							'image' => $image_path . 'full-width.png',
							'title' => __( 'Full Width', 'nexter' ),
						),
					),
					'input_attrs' => array(
						'name' => NXT_OPTIONS . '[site-layout-container]', // This is where the name gets passed
					),
				),
				array(
					'name'        => NXT_OPTIONS . '[layout-container]',
					'default'     => nexter_get_option( 'layout-container', ['desktop' => 1140, 'tablet' => 960 ] ),
					'title'       => __( 'Container Width', 'nexter' ),
					'type'        => 'control',
					'transport'   => 'postMessage',
					'control'     => 'nxt-responsive-slider',
					'section'     => 'section-site-layout-container',
					'priority'    => 5,
					'input_attrs' => array(
						'min'  => 300,
						'step' => 1,
						'max'  => 2000,
					),
					'conditional' => array( NXT_OPTIONS . '[site-layout-container]', '==', array('container-block-editor','container') ),
				),
				array(
					'name'           => NXT_OPTIONS . '[site-fluid-spacing]',
					'default'        => nexter_get_option( 'site-fluid-spacing' ) ? nexter_get_option( 'site-fluid-spacing' ) : array(
						'md' => ['left' => 15, 'right' => 15],
						'sm' => ['left' => '', 'right' => ''],
						'xs' => ['left' => '', 'right' => ''],
						'md-unit' => 'px',
						'sm-unit' => 'px',
						'xs-unit' => 'px',
					),
					'type'           => 'control',
					'control'        => 'nxt-responsive-spacing',
					'section'        => 'section-site-layout-container',
					'transport'      => 'postMessage',
					'priority'       => 5,
					'title'          => __( 'Spacing(Padding)', 'nexter' ),
					'linked' => true,
					'unit'   => array( 'px', 'em' ),
					'choices'        => array(
						'left'   => __( 'Left', 'nexter' ),
						'right'  => __( 'Right', 'nexter' ),
					),
					'conditional' => array( NXT_OPTIONS . '[site-layout-container]', '==', 'container-fluid' ),
				),
				array(
					'name'     => NXT_OPTIONS . '[heading-site-layout-divider]',
					'type'     => 'control',
					'control'  => 'nxt-heading',
					'section'  => 'section-site-layout-container',
					'priority' => 5,
					'settings' => array(),
				),
				/*Content/Body Container*/
				...$header_footer_container,
				/*Page Container*/
				array(
					'name'     => NXT_OPTIONS . '[heading-page-divider]',
					'type'     => 'control',					
					'control'  => 'nxt-heading',
					'section'  => 'section-site-layout-container',
					'priority' => 6,
					'settings' => array(),					
				),
				array(
					'name'     => NXT_OPTIONS . '[site-page-container]',
					'default'  => '',
					'title'    => __( 'Page Container', 'nexter' ),
					'type'     => 'control',
					'control'  => 'nxt-style', // Change to custom control type
					'section'  => 'section-site-layout-container',
					'priority' => 7,
					'choices'  => array(
						'' => array(
							'image' => $image_path . 'default.png',
							'title' => __( 'Default', 'nexter' ),
						),
						'container-block-editor' => array(
							'image' => $image_path . 'container.png',
							'title' => __( 'Container', 'nexter' ),
						),
						'container-fluid' => array(
							'image' => $image_path . 'full-width.png',
							'title' => __( 'Full Width', 'nexter' ),
						),
					),
					'input_attrs' => array(
						'name' => NXT_OPTIONS . '[site-page-container]', // This is where the name gets passed
					),
				),
				array(
					'name'              => NXT_OPTIONS . '[layout-page-container]',
					'default'           => nexter_get_option( 'layout-page-container' ),
					'type'              => 'control',
					'control'           => 'nxt-responsive-slider',
					'section'           => 'section-site-layout-container',
					'title'             => __( 'Container Width', 'nexter' ),
					'transport'         => 'postMessage',
					'priority'          => 8,
					'suffix'            => '',
					'input_attrs'       => array(
						'min'  => 300,
						'step' => 1,
						'max'  => 2000,
					),
					'conditional' => array( NXT_OPTIONS . '[site-page-container]', '==', array('container-block-editor', 'container') ),
				),
				array(
					'name'           => NXT_OPTIONS . '[page-fluid-spacing]',
					'default'        => nexter_get_option( 'page-fluid-spacing' ) ? nexter_get_option( 'page-fluid-spacing' ) : array(
						'md' => ['left' => 15, 'right' => 15],
						'sm' => ['left' => '', 'right' => ''],
						'xs' => ['left' => '', 'right' => ''],
						'md-unit' => 'px',
						'sm-unit' => 'px',
						'xs-unit' => 'px',
					),
					'type'           => 'control',
					'control'        => 'nxt-responsive-spacing',
					'section'        => 'section-site-layout-container',
					'transport'      => 'postMessage',
					'priority'       => 8,
					'title'          => __( 'Spacing(Padding)', 'nexter' ),
					'linked' => true,
					'unit'   => array( 'px', 'em' ),
					'choices'        => array(
						'left'   => __( 'Left', 'nexter' ),
						'right'  => __( 'Right', 'nexter' ),
					),
					'conditional' => array( NXT_OPTIONS . '[site-page-container]', '==', 'container-fluid' ),
				),
				/*Page Container*/
				/*Post Container*/
				array(
					'name'     => NXT_OPTIONS . '[heading-post-divider]',
					'type'     => 'control',					
					'control'  => 'nxt-heading',
					'section'  => 'section-site-layout-container',
					'priority' => 9,
					'settings' => array(),					
				),
				array(
					'name'     => NXT_OPTIONS . '[site-posts-container]',
					'default'  => '',
					'title'    => __( 'Single Post Container', 'nexter' ),
					'type'     => 'control',
					'control'  => 'nxt-style', // Change to custom control type
					'section'  => 'section-site-layout-container',
					'priority' => 10,
					'choices'  => array(
						'' => array(
							'image' => $image_path . 'default.png',
							'title' => __( 'Default', 'nexter' ),
						),
						'container-block-editor' => array(
							'image' => $image_path . 'container.png',
							'title' => __( 'Container', 'nexter' ),
						),
						'container-fluid' => array(
							'image' => $image_path . 'full-width.png',
							'title' => __( 'Full Width', 'nexter' ),
						),
					),
					'input_attrs' => array(
						'name' => NXT_OPTIONS . '[site-posts-container]', // This is where the name gets passed
					),
				),
				array(
					'name'              => NXT_OPTIONS . '[layout-posts-container]',
					'default'           => nexter_get_option( 'layout-posts-container' ),
					'type'              => 'control',
					'control'           => 'nxt-responsive-slider',
					'section'           => 'section-site-layout-container',
					'title'             => __( 'Container Width', 'nexter' ),
					'transport'         => 'postMessage',
					'priority'          => 11,
					'suffix'            => '',
					'input_attrs'       => array(
						'min'  => 300,
						'step' => 1,
						'max'  => 2000,
					),
					'conditional' => array( NXT_OPTIONS . '[site-posts-container]', '==', array('container-block-editor', 'container') ),
				),
				array(
					'name'           => NXT_OPTIONS . '[post-fluid-spacing]',
					'default'        => nexter_get_option( 'post-fluid-spacing' ) ? nexter_get_option( 'post-fluid-spacing' ) : array(
						'md' => ['left' => 15, 'right' => 15],
						'sm' => ['left' => '', 'right' => ''],
						'xs' => ['left' => '', 'right' => ''],
						'md-unit' => 'px',
						'sm-unit' => 'px',
						'xs-unit' => 'px',
					),
					'type'           => 'control',
					'control'        => 'nxt-responsive-spacing',
					'section'        => 'section-site-layout-container',
					'transport'      => 'postMessage',
					'priority'       => 11,
					'title'          => __( 'Spacing(Padding)', 'nexter' ),
					'linked' => true,
					'unit'   => array( 'px', 'em' ),
					'choices'        => array(
						'left'   => __( 'Left', 'nexter' ),
						'right'  => __( 'Right', 'nexter' ),
					),
					'conditional' => array( NXT_OPTIONS . '[site-posts-container]', '==', 'container-fluid' ),
				),
				/*Post Container*/
				/*Archive Container*/
				array(
					'name'     => NXT_OPTIONS . '[heading-archive-divider]',
					'type'     => 'control',					
					'control'  => 'nxt-heading',
					'section'  => 'section-site-layout-container',
					'priority' => 12,
					'settings' => array(),					
				),
				array(
					'name'     => NXT_OPTIONS . '[site-archive-container]',
					'default'  => '',
					'title'    => __( 'Archive Posts Container', 'nexter' ),
					'type'     => 'control',
					'control'  => 'nxt-style', // Change to custom control type
					'section'  => 'section-site-layout-container',
					'priority' => 13,
					'choices'  => array(
						'' => array(
							'image' => $image_path . 'default.png',
							'title' => __( 'Default', 'nexter' ),
						),
						'container-block-editor' => array(
							'image' => $image_path . 'container.png',
							'title' => __( 'Container', 'nexter' ),
						),
						'container-fluid' => array(
							'image' => $image_path . 'full-width.png',
							'title' => __( 'Full Width', 'nexter' ),
						),
					),
					'input_attrs' => array(
						'name' => NXT_OPTIONS . '[site-archive-container]', // This is where the name gets passed
					),
				),
				array(
					'name'              => NXT_OPTIONS . '[layout-archive-container]',
					'default'           => nexter_get_option( 'layout-archive-container' ),
					'type'              => 'control',
					'control'           => 'nxt-responsive-slider',
					'section'           => 'section-site-layout-container',
					'title'             => __( 'Container Width', 'nexter' ),
					'transport'         => 'postMessage',
					'priority'          => 14,
					'suffix'            => '',
					'input_attrs'       => array(
						'min'  => 300,
						'step' => 1,
						'max'  => 2000,
					),
					'conditional' => array( NXT_OPTIONS . '[site-archive-container]', '==', array('container-block-editor', 'container') ),
				),
				array(
					'name'           => NXT_OPTIONS . '[archive-fluid-spacing]',
					'default'        => nexter_get_option( 'archive-fluid-spacing' ) ? nexter_get_option( 'archive-fluid-spacing' ) : array(
						'md' => ['left' => 15, 'right' => 15],
						'sm' => ['left' => '', 'right' => ''],
						'xs' => ['left' => '', 'right' => ''],
						'md-unit' => 'px',
						'sm-unit' => 'px',
						'xs-unit' => 'px',
					),
					'type'           => 'control',
					'control'        => 'nxt-responsive-spacing',
					'section'        => 'section-site-layout-container',
					'transport'      => 'postMessage',
					'priority'       => 14,
					'title'          => __( 'Spacing(Padding)', 'nexter' ),
					'linked' => true,
					'unit'   => array( 'px', 'em' ),
					'choices'        => array(
						'left'   => __( 'Left', 'nexter' ),
						'right'  => __( 'Right', 'nexter' ),
					),
					'conditional' => array( NXT_OPTIONS . '[site-archive-container]', '==', 'container-fluid' ),
				),
				/** End
				 * Options Layout/Container
				 */
			);

			return array_merge( $configurations, $options );
		}
		
	}
}

new Nexter_Layout_Container;
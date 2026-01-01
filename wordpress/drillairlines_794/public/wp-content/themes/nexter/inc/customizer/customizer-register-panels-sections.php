<?php
/**
 * Register customizer panels & sections (optimized)
 *
 * @package Nexter
 * @since   1.0.0
 */
if ( ! class_exists( 'Nexter_Customizer_Register_Sections_Panels' ) ) {

	class Nexter_Customizer_Register_Sections_Panels {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'customize_register', [ $this, 'register_configuration' ] );
		}

		/**
		 * Register panels & sections
		 */
		public function register_configuration( $wp_customize ) {

			// Panels config
			$panels = [
				'panel-global-general' => [
					'title'    => esc_html__( 'General', 'nexter' ),
					'priority' => 5,
					'sections' => [
						'section-site-layout-container' => [ 'Container', 5 ],
						'section-header-mode'           => [ 'Header', 10 ],
						'section-footer-mode'           => [ 'Footer', 10 ],
						'section-layout-sidebar'        => [ 'Sidebar', 10 ],
						'section-body-style'            => [ 'Body Style', 15 ],
						'section-selected-text-style'   => [ 'Selection Text Color', 20 ],
						'section-maintenance-mode'      => [ 'Maintenance Mode', 30 ],
					],
				],
				'panel-styling-colors' => [
					'title'    => esc_html__( 'Styling Colors', 'nexter' ),
					'priority' => 15,
					'sections' => [
						'section-body-colors'    => [ 'Body', 1 ],
						'section-heading-colors' => [ 'Headings H1-H6', 5 ],
					],
				],
				'panel-typography' => [
					'title'    => esc_html__( 'Typography', 'nexter' ),
					'priority' => 20,
					'sections' => [
						'section-body-typography' => [ 'Body', 5 ],
						'section-heading-h1-typo' => [ 'Heading H1', 5 ],
						'section-heading-h2-typo' => [ 'Heading H2', 10 ],
						'section-heading-h3-typo' => [ 'Heading H3', 15 ],
						'section-heading-h4-typo' => [ 'Heading H4', 20 ],
						'section-heading-h5-typo' => [ 'Heading H5', 25 ],
						'section-heading-h6-typo' => [ 'Heading H6', 30 ],
					],
				],
				'panel-pages-option' => [
					'title'    => esc_html__( 'Pages', 'nexter' ),
					'priority' => 22,
					'sections' => [
						'section-page-single' => [ 'Single Page', 5 ],
					],
				],
				'panel-blog-layout' => [
					'title'    => esc_html__( 'Blog', 'nexter' ),
					'priority' => 25,
					'sections' => [
						'section-blog-single' => [ 'Single Post', 5 ],
					],
				],
			];

			// Add panels & their sections
			foreach ( $panels as $panel_id => $panel ) {
				$wp_customize->add_panel(
					new Nexter_Customizer_Panel(
						$wp_customize,
						$panel_id,
						[
							'title'    => $panel['title'],
							'priority' => $panel['priority'],
						]
					)
				);

				if ( ! empty( $panel['sections'] ) ) {
					foreach ( $panel['sections'] as $section_id => $section ) {
						$wp_customize->add_section(
							new Nexter_Customizer_Section(
								$wp_customize,
								$section_id,
								[
									'title'    => $section[0],
									'priority' => $section[1],
									'panel'    => $panel_id,
								]
							)
						);
					}
				}
			}

			// Standalone section (Site Identity)
			$wp_customize->add_section(
				new Nexter_Customizer_Section(
					$wp_customize,
					'title_tagline',
					[
						'title'    => __( 'Site Identity', 'nexter' ),
						'priority' => 5,
					]
				)
			);
		}
	}
}

new Nexter_Customizer_Register_Sections_Panels();
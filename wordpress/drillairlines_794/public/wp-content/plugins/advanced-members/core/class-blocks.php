<?php
/**
 * Advanced Members for ACF Blocks
 *
 * @since 1.0.0
 * @package Advanced Members for ACF
 */

namespace AMem;

use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Render the dynamic aspects of our blocks.
 *
 * @since 1.0.0
 */
class Blocks extends Module {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', [$this, 'register_blocks'] );

		add_action( 'enqueue_block_editor_assets', [$this, 'block_editor_assets'] );

		if ( version_compare( $GLOBALS['wp_version'], '5.8-alpha-1', '<' ) ) {
			add_filter( 'block_categories', [$this, 'apply_category'] );
		} else {
			add_filter( 'block_categories_all', [$this, 'apply_category'] );
		}

	}

	/**
	 * Register blocks.
	 *
	 * @since 1.0.0
	 */
	public function register_blocks() {
		amem_include( 'core/blocks/class-form.php' );

		$file = amem_get_path('build/blocks/blocks/form') . '/block.json';

		register_block_type(
			amem_get_path('build/blocks/blocks/form'),
			array(
				'title' => esc_html__( 'Adv. Members Form', 'advanced-members' ),
				'render_callback' => [ '\AMem\Blocks\Form', 'render_block' ],
				'uses_context' => array(
					'postType',
					'postId',
				),
			)
		);

		do_action( 'amem/blocks/register' );
	}

	/**
	 * Add Advanced Members for ACF category to Gutenberg.
	 *
	 * @param array $categories Existing categories.
	 * @since 1.0.0
	 */
	function apply_category( $categories ) {
		return array_merge(
			array(
				array(
					'slug'  => 'advanced-members',
					'title' => __( 'Advanced Members', 'advanced-members' ),
				),
			),
			$categories
		);
	}

	/**
	 * Enqueue Gutenberg block assets for backend editor.
	 *
	 * @uses {wp-blocks} for block type registration & related functions.
	 * @uses {wp-element} for WP Element abstraction — structure of blocks.
	 * @uses {wp-i18n} to internationalize the block's text.
	 * @uses {wp-editor} for WP editor styles.
	 * @since 1.0.0
	 */
	function block_editor_assets() {
		global $pagenow;

		$amemblocks_deps = array( 'wp-blocks', 'wp-i18n', 'wp-editor', 'wp-element', 'wp-compose', 'wp-data' );

		if ( 'widgets.php' === $pagenow ) {
			unset( $amemblocks_deps[2] );
		}

		$assets_file = amem_get_path('build/blocks/blocks.asset.php');
		$compiled_assets = file_exists( $assets_file )
			? require $assets_file
			: false;

		$assets =
			isset( $compiled_assets['dependencies'] ) &&
			isset( $compiled_assets['version'] )
			? $compiled_assets
			: [
				'dependencies' => $amemblocks_deps,
				'version' => filemtime( amem_get_path('build/blocks/blocks.js') ),
			];

		wp_enqueue_script(
			'amemblocks',
			amem_get_url('build/blocks/blocks.js'),
			$assets['dependencies'],
			$assets['version'],
			true
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'amemblocks', 'advanced-members' );
		}

		// wp_enqueue_style(
		// 	'amemblocks',
		// 	amem_get_url('build/blocks/blocks.css)',
		// 	array( 'wp-edit-blocks' ),
		// 	filemtime( amem_get_path('build/blocks/blocks.css') )
		// );

		$mdash = html_entity_decode( '&mdash;' );
		$blank_form = [
			'form' => '0',
			'preForm' => '0',
			/* translators: 1: &mdash; entitiy, 2: &mdash; entitiy */
			'title' => sprintf( __(' %1$s Select a form %2$s ', 'advanced-members'), $mdash, $mdash ),
			'hash' => '',
			'slug' => ''
		];
		$forms = amem()->rest->forms( ['type' => ['login', 'registration']] );
		// $forms = [$blank_form] + $forms;

		$preForms = amem()->rest->preForms();
		// $preForms = [$blank_form] + $preForms;

		$allForms = array_merge( $forms, $preForms );

		uasort( $allForms, function($a, $b) {
			return strcmp( $a['title'], $b['title'] );
		} );

		$form_args = array(
			'forms' => array_values($forms),
			'preForms' => array_values($preForms),
			'allForms' => (object) array_values($allForms),
		);
		$form_args = apply_filters( 'amem/blocks/assets/form_args', $form_args );

		wp_localize_script(
			'amemblocks',
			'amemBlocks',
			$form_args
		);

		$defaults = $this->get_block_defaults();

		wp_localize_script(
			'amemblocks',
			'amemBlocksDefaults',
			$defaults
		);
	}

	function get_block_defaults() {
		$use_cache = apply_filters( 'amem/blocks/cache/defaults', false );

		if ( $use_cache ) {
			$cached_data = wp_cache_get(
				'amem/blocks/defaults/cache',
				'amem/blocks/cache/group'
			);

			if ( $cached_data ) {
				return $cached_data;
			}
		}

		amem_include( 'core/blocks/class-form.php' );

		$defaults = apply_filters(
			'amem/blocks/defaults',
			[
				'form' => $this->apply_global_defaults( Blocks\Form::defaults() ),
			]
		);

		if ( $use_cache ) {
			wp_cache_set(
				'amem/blocks/defaults/cache',
				$defaults,
				'amem/blocks/cache/group'
			);
		}

		return $defaults;
	}

	function apply_global_defaults( $defaults ) {
		// Some defaults for future usage
		$defaults['color'] = [];
		$defaults['spacing'] = [];
		$defaults['typography'] = [];

		return $defaults;
	}

}

amem()->register_module( 'blocks', Blocks::getInstance() );

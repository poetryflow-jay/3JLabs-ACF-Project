<?php
/**
 * Advanced Members for ACF Form Block
 *
 * @since 1.0.0
 * @package Advanced Members for ACF
 */
namespace AMem\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Form {
	/**
	 * Keep track of all blocks of this type on the page.
	 *
	 * @var array $block_ids The current block id.
	 */
	private static $block_ids = [];

	/**
	 * Block defaults.
	 */
	public static function defaults() {
		return [
			'type' => '',
			'form' => '',
			'preForm' => '',
			'slug' => '',
			'hash' => '',
			'output' => 'form',
		];
	}

	/**
	 * Wrapper function for our dynamic buttons.
	 *
	 * @since 1.0.0
	 * @param array    $attributes The block attributes.
	 * @param string   $content The dynamic text to display.
	 * @param WP_Block $block Block instance.
	 */
	public static function render_block( $attributes, $content, $block ) {
		if ( !$attributes['hash'] )
			return '';

		$output = '';

		$classes = ['amem-test'];
		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

		$output .= sprintf( '<div %1$s>', $wrapper_attributes );

		// Check if it's a shortcode
		if ( strpos($attributes['hash'], '[') === 0 ) {
			$output .= do_shortcode($attributes['hash']);
		} else {
			$output .= amem_form($attributes['form'], ['echo' => false]);
		}

		$output .= '</div>';

		return $output;
	}

}

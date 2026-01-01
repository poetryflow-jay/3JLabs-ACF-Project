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

add_filter( 'nxt_render_theme_css', 'nxt_text_selection_dynamic_css' );
function nxt_text_selection_dynamic_css( $theme_css ){

	$selected_text_bg_color = nexter_get_option('selected-text-bg-color');
	$selected_text_color = nexter_get_option('selected-text-color');

	$style =array();

	$style = array(
		'::selection' => array(
			'color' => esc_attr($selected_text_color),
			'background' => esc_attr($selected_text_bg_color)
		),
	);

	if( !empty($style)){
		$theme_css[]= $style;
	}

	return $theme_css;
}
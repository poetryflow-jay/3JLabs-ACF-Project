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

add_filter( 'nxt_render_theme_css', 'nxt_heading_dynamic_css' );
add_filter( 'nxt_gutenberg_render_theme_css', 'nxt_heading_editor_dynamic_css',1 );
function nxt_heading_dynamic_css( $theme_css ){
	
	$heading_selector = [ 'h1' => 'h1, h1 a',
						'h2' => 'h2, h2 a',
						'h3' => 'h3, h3 a, .archive-post-title a',
						'h4' => 'h4, h4 a',
						'h5' => 'h5, h5 a',
						'h6' => 'h6, h6 a'
					];
	$style = [];			
	foreach($heading_selector as $key => $selector){
		$color	= nexter_get_option('heading-color-'.$key);
		$style[$selector]  = [
			'color' => esc_attr($color)
		];
	}
	
	if( !empty($style)){
		$theme_css[]= $style;
	}
	
	return $theme_css;
}
function nxt_heading_editor_dynamic_css( $theme_css ){
	
	$heading_selector = [ 
		'h1' => ':where(.editor-styles-wrapper) h1, :where(.editor-styles-wrapper) h1 a',
		'h2' => ':where(.editor-styles-wrapper) h2, :where(.editor-styles-wrapper) h2 a',
		'h3' => ':where(.editor-styles-wrapper) h3, :where(.editor-styles-wrapper) h3 a',
		'h4' => ':where(.editor-styles-wrapper) h4, :where(.editor-styles-wrapper) h4 a',
		'h5' => ':where(.editor-styles-wrapper) h5, :where(.editor-styles-wrapper) h5 a',
		'h6' => ':where(.editor-styles-wrapper) h6, :where(.editor-styles-wrapper) h6 a'
	];
	$style = [];			
	foreach($heading_selector as $key => $selector){
		$color	= nexter_get_option('heading-color-'.$key);
		$style[$selector]  = [
			'color' => esc_attr($color)
		];
	}
	if( !empty($style)){
		$theme_css[]= $style;
	}
	
	return $theme_css;
}
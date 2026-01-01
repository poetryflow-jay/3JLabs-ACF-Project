<?php
/**
 * Heading Typography Styling Options for Nexter Theme.
 *
 * @package	Nexter
 * @since	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'nxt_render_theme_css', 'nxt_heading_typo_dynamic_css');
add_filter( 'nxt_gutenberg_render_theme_css', 'nxt_heading_typo_editor_dynamic_css',1 );
function nxt_heading_typo_dynamic_css( $theme_css ){
		
	$heading_selector = [ 
		'h1' => 'h1, h1 a',
		'h2' => 'h2, h2 a',
		'h3' => 'h3, h3 a, .archive-post-title a',
		'h4' => 'h4, h4 a',
		'h5' => 'h5, h5 a',
		'h6' => 'h6, h6 a'
	];
	
	$opt_val = [];
	$style	 = [];
	$tablet_style = [];
	$mobile_style = [];
	foreach($heading_selector as $key => $selector){
		$opt_val[$key.'-size']   = nexter_get_option('font-size-'.$key );
		$opt_val[$key.'-height'] = nexter_get_option('line-height-'.$key);
		$opt_val[$key.'-family'] = nexter_get_option('font-family-'.$key);
		$opt_val[$key.'-weight'] = nexter_get_option('font-weight-'.$key);
		$opt_val[$key.'-transform'] = nexter_get_option('transform-'.$key);
		
		$style[$selector]  = [
			'font-size' => nexter_responsive_size_css($opt_val[$key.'-size'], 'desktop'),
			'line-height' => (!empty($opt_val[$key.'-height']['desktop'])) ? nexter_get_option_css_value( $opt_val[$key.'-height']['desktop'], '' ) : '',
			'font-family' => nexter_get_option_css_value($opt_val[$key.'-family'], 'font'),
			'font-weight' => nexter_get_option_css_value($opt_val[$key.'-weight'], 'font'),
			'text-transform' => esc_attr($opt_val[$key.'-transform']),
		];
		
		$tablet_style[$selector] = [
			'font-size' => nexter_responsive_size_css($opt_val[$key.'-size'], 'tablet'),
			'line-height' => (!empty($opt_val[$key.'-height']['tablet'])) ? nexter_get_option_css_value( $opt_val[$key.'-height']['tablet'], '' ) : ((!empty($opt_val[$key.'-height']['desktop'])) ? nexter_get_option_css_value( $opt_val[$key.'-height']['desktop'], '' ) : ''),
		];
		$mobile_style[$selector] = [
			'font-size' => nexter_responsive_size_css($opt_val[$key.'-size'], 'mobile'),
			'line-height' => (!empty($opt_val[$key.'-height']['mobile'])) ? nexter_get_option_css_value( $opt_val[$key.'-height']['mobile'], '' ) : ((!empty($opt_val[$key.'-height']['tablet'])) ? nexter_get_option_css_value( $opt_val[$key.'-height']['tablet'], '' ) : ((!empty($opt_val[$key.'-height']['desktop'])) ? nexter_get_option_css_value( $opt_val[$key.'-height']['desktop'], '' ) : '') ),
		];
	}
	
	if( !empty($style)){
		$theme_css[]= $style;
	}
	
	if( !empty($tablet_style)){
		$theme_css['tablet']= array_merge_recursive($theme_css['tablet'],$tablet_style);
	}
	
	if( !empty($mobile_style)){
		$theme_css['mobile']= array_merge_recursive($theme_css['mobile'],$mobile_style);
	}
	
	return $theme_css;
}
function nxt_heading_typo_editor_dynamic_css( $theme_css ){
			
	$heading_selector = [ 'h1' => ':where(.editor-styles-wrapper) h1,:where(.editor-styles-wrapper) h1 a, .editor-styles-wrapper h1.block-editor-block-list__block:not(.editor-post-title)',
				'h2' => ':where(.editor-styles-wrapper) h2, :where(.editor-styles-wrapper) h2 a, .editor-styles-wrapper h2.block-editor-block-list__block',
				'h3' => ':where(.editor-styles-wrapper) h3, :where(.editor-styles-wrapper) h3 a, .archive-post-title a, .editor-styles-wrapper h3.block-editor-block-list__block',
				'h4' => ':where(.editor-styles-wrapper) h4, :where(.editor-styles-wrapper) h4 a, .editor-styles-wrapper h4.block-editor-block-list__block',
				'h5' => ':where(.editor-styles-wrapper) h5, :where(.editor-styles-wrapper) h5 a, .editor-styles-wrapper h5.block-editor-block-list__block',
				'h6' => ':where(.editor-styles-wrapper) h6, :where(.editor-styles-wrapper) h6 a, .editor-styles-wrapper h6.block-editor-block-list__block'
			];
				
	$opt_val = [];		   
	$style = [];
	$tablet_style = [];
	$mobile_style = [];
	
	foreach($heading_selector as $key => $selector){
		$opt_val[$key.'-size']   = nexter_get_option('font-size-'.$key );
		$opt_val[$key.'-height'] = nexter_get_option('line-height-'.$key);
		$opt_val[$key.'-family'] = nexter_get_option('font-family-'.$key);
		$opt_val[$key.'-weight'] = nexter_get_option('font-weight-'.$key);
		$opt_val[$key.'-transform'] = nexter_get_option('transform-'.$key);
		
		$style[$selector]  = [
			'font-size' => nexter_responsive_size_css($opt_val[$key.'-size'], 'desktop'),
			'line-height' => (!empty($opt_val[$key.'-height']['desktop'])) ? nexter_get_option_css_value( $opt_val[$key.'-height']['desktop'], '' ) : '',
			'font-family' => nexter_get_option_css_value($opt_val[$key.'-family'], 'font'),
			'font-weight' => nexter_get_option_css_value($opt_val[$key.'-weight'], 'font'),
			'text-transform' => esc_attr($opt_val[$key.'-transform']),
		];
		
		$tablet_style[$selector] = [
			'font-size' => nexter_responsive_size_css($opt_val[$key.'-size'], 'tablet'),
			'line-height' => (!empty($opt_val[$key.'-height']['tablet'])) ? nexter_get_option_css_value( $opt_val[$key.'-height']['tablet'], '' ) : ((!empty($opt_val[$key.'-height']['desktop'])) ? nexter_get_option_css_value( $opt_val[$key.'-height']['desktop'], '' ) : ''),
		];
		$mobile_style[$selector] = [
			'font-size' => nexter_responsive_size_css($opt_val[$key.'-size'], 'mobile'),
			'line-height' => (!empty($opt_val[$key.'-height']['mobile'])) ? nexter_get_option_css_value( $opt_val[$key.'-height']['mobile'], '' ) : ((!empty($opt_val[$key.'-height']['tablet'])) ? nexter_get_option_css_value( $opt_val[$key.'-height']['tablet'], '' ) : ((!empty($opt_val[$key.'-height']['desktop'])) ? nexter_get_option_css_value( $opt_val[$key.'-height']['desktop'], '' ) : '')),
		];
	}
	
	if( !empty($style)){
		$theme_css[]= $style;
	}
	
	if( !empty($tablet_style)){
		$theme_css['tablet'] = (!empty($theme_css['tablet']) && isset($theme_css['tablet'])) ? $theme_css['tablet'] : [];
		$theme_css['tablet']= array_merge_recursive($theme_css['tablet'],$tablet_style);
	}
	
	if( !empty($mobile_style)){
		$theme_css['mobile'] = (!empty($theme_css['mobile']) && isset($theme_css['mobile'])) ? $theme_css['mobile'] : [];
		$theme_css['mobile']= array_merge_recursive($theme_css['mobile'],$mobile_style);
	}
	
	return $theme_css;
}
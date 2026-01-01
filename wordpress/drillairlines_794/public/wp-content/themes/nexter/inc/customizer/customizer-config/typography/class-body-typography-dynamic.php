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

add_filter( 'nxt_render_theme_css', 'nxt_body_typo_dynamic_css',1 );
add_filter( 'nxt_gutenberg_render_theme_css', 'nxt_body_typo_editor_dynamic_css',1 );
function nxt_body_typo_dynamic_css( $theme_css ){

	$body_fontfamily = nexter_get_body_fontfamily();
	$body_fontweight = nexter_get_option('body-font-weight');
	$body_fontsize   = nexter_get_option('font-size-body');
	$body_lineheight = nexter_get_option('body-line-height');
	$body_transform  = nexter_get_option('body-transform');
	$paragraphy_mb   = nexter_get_option('paragraph-mb');
	
	$style =array();
	
	$style  = array(
		'body, button, input, select,optgroup, textarea' => array(
			'font-family' => nexter_get_font_family_css($body_fontfamily),
			'font-weight' => esc_attr($body_fontweight),
			'font-size' => nexter_responsive_size_css($body_fontsize, 'desktop'),
			'line-height' => (!empty($body_lineheight['desktop'])) ? nexter_get_option_css_value( $body_lineheight['desktop'], '' ) : '',
			'text-transform' => esc_attr($body_transform)
		),
		'p, .entry-content p' => array(
			'margin-bottom' => nexter_get_option_css_value($paragraphy_mb, 'em')
		),
	);
	
	if( !empty($style)){
		$theme_css[]= $style;
	}
	
	//Tablet css
	$tablet_style = array(
		'body, button, input, select,optgroup, textarea' => array(
			'font-size' => nexter_responsive_size_css($body_fontsize, 'tablet'),
			'line-height' => (!empty($body_lineheight['tablet'])) ? nexter_get_option_css_value( $body_lineheight['tablet'], '' ) : ((!empty($body_lineheight['desktop'])) ? nexter_get_option_css_value( $body_lineheight['desktop'], '' ) : ''),
		),
	);
	
	
	if( !empty($tablet_style)){
		$theme_css['tablet'] = (!empty($theme_css['tablet']) && isset($theme_css['tablet'])) ? $theme_css['tablet'] : [];
		$theme_css['tablet']= array_merge_recursive($theme_css['tablet'],$tablet_style);
	}
	
	//Mobile css
	$mobile_style = array(
		'body, button, input, select,optgroup, textarea' => array(
			'font-size' => nexter_responsive_size_css($body_fontsize, 'mobile'),
			'line-height' => (!empty($body_lineheight['mobile'])) ? nexter_get_option_css_value( $body_lineheight['mobile'], '' ) : ((!empty($body_lineheight['tablet'])) ? nexter_get_option_css_value( $body_lineheight['tablet'], '' ) : ((!empty($body_lineheight['desktop'])) ? nexter_get_option_css_value( $body_lineheight['desktop'], '' ) : '' ) ),
		),
	);
	
	if( !empty($mobile_style)){
		$theme_css['mobile'] = (!empty($theme_css['mobile']) && isset($theme_css['mobile'])) ? $theme_css['mobile'] : [];
		$theme_css['mobile']= array_merge_recursive($theme_css['mobile'],$mobile_style);
	}
	
	return $theme_css;
}
function nxt_body_typo_editor_dynamic_css( $theme_css ){

	$body_fontfamily = nexter_get_body_fontfamily();
	$body_fontweight = nexter_get_option('body-font-weight');
	$body_fontsize   = nexter_get_option('font-size-body');
	$body_lineheight = nexter_get_option('body-line-height');
	$body_transform  = nexter_get_option('body-transform');
	$paragraphy_mb   = nexter_get_option('paragraph-mb');
	
	$style =array();
	
	$style  = array(
		'.editor-styles-wrapper' => array(
			'font-family' => nexter_get_font_family_css($body_fontfamily),
			'font-weight' => esc_attr($body_fontweight),
			'font-size' => nexter_responsive_size_css($body_fontsize, 'desktop'),
			'line-height' => (!empty($body_lineheight['desktop'])) ? nexter_get_option_css_value( $body_lineheight['desktop'], '' ) : '',
			'text-transform' => esc_attr($body_transform)
		),
		'.editor-styles-wrapper .block-editor-block-list__block' => array(
			'font-family' => nexter_get_font_family_css($body_fontfamily),
		),
		':where(.editor-styles-wrapper) p' => array(
			'margin-bottom' => nexter_get_option_css_value($paragraphy_mb, 'em')
		),
	);
	
	if( !empty($style)){
		$theme_css[]= $style;
	}
	
	//Tablet css
	$tablet_style = array(
		'.editor-styles-wrapper' => array(
			'font-size' => nexter_responsive_size_css($body_fontsize, 'tablet'),
			'line-height' => (!empty($body_lineheight['tablet'])) ? nexter_get_option_css_value( $body_lineheight['tablet'], '' ) : ((!empty($body_lineheight['desktop'])) ? nexter_get_option_css_value( $body_lineheight['desktop'], '' ) : ''),
		),
	);
	
	$theme_css['tablet'] = array();
	if( !empty($tablet_style)){
		$theme_css['tablet']= array_merge_recursive($theme_css['tablet'],$tablet_style);
	}
	
	//Mobile css
	$mobile_style = array(
		'.editor-styles-wrapper' => array(
			'font-size' => nexter_responsive_size_css($body_fontsize, 'mobile'),
			'line-height' => (!empty($body_lineheight['mobile'])) ? nexter_get_option_css_value( $body_lineheight['mobile'], '' ) : ((!empty($body_lineheight['tablet'])) ? nexter_get_option_css_value( $body_lineheight['tablet'], '' ) : ((!empty($body_lineheight['desktop'])) ? nexter_get_option_css_value( $body_lineheight['desktop'], '' ) : '' ) ),
		),
	);
	
	$theme_css['mobile'] = array();
	if( !empty($mobile_style)){
		$theme_css['mobile']= array_merge_recursive($theme_css['mobile'],$mobile_style);
	}
	
	return $theme_css;
}
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
add_filter( 'nxt_render_theme_css', 'nxt_body_dynamic_css' );
add_filter( 'nxt_gutenberg_render_theme_css','nxt_body_editor_dynamic_css',1 );
function nxt_body_dynamic_css( $theme_css ){
	
	$body_bgcolor           = nexter_get_option('body-bgcolor');
	$content_bgcolor           = nexter_get_option('content-bgcolor');
	
	$body_frame_padding     = nexter_get_option('body-frame-padding');
	$fixed_body_frame       = nexter_get_option('fixed-body-frame');
	$fixed_body_frame_color = nexter_get_option('body-frame-color');
	
	$option_frame = ['top' => 'height', 'bottom' => 'height', 'left' => 'width', 'right' => 'width'];
	$style = [];
	foreach($option_frame as $key => $val){
		$style['body']['padding-'.$key] = nexter_dimension_responsive_css($body_frame_padding, $key, 'md');
	}
	
	$body_content_bg_css  = array(
		'body' => nexter_get_background_css($body_bgcolor),
		'#content.site-content' => nexter_get_background_css($content_bgcolor)
	);
	
	$fixed_body_frame_css = [];
	if ($fixed_body_frame == 'on') {
		$fixed_body_frame_css = array(
			'.nxt-body-frame' => array(
				'background-color' => esc_attr($fixed_body_frame_color)
			),
		);
		foreach( $option_frame as $key => $val ){
			$fixed_body_frame_css['.nxt-body-frame.frame-'.$key] = [
				$val => nexter_dimension_responsive_css($body_frame_padding, $key, 'md')
			];
		}
	}
	
	$style = array_merge_recursive($style,$body_content_bg_css, $fixed_body_frame_css);
	
	if( !empty($style)){
		$theme_css[]= $style;
	}
	
	//Tablet Css
	$tablet_css = [];
	$tablet_fixed_css = [];
	
	foreach($option_frame as $key => $val){
		$tablet_css['body']['padding-'.$key] = nexter_dimension_responsive_css($body_frame_padding, $key, 'sm');
		if ($fixed_body_frame == 'on') {
			$tablet_fixed_css['.nxt-body-frame.frame-'.$key] = [
				$val => nexter_dimension_responsive_css($body_frame_padding, $key, 'sm')
			];
		}
	}
	
	if($tablet_css){
		if(!isset($theme_css['tablet'])){
			$theme_css['tablet'] = [];
		}
		$theme_css['tablet'] = array_merge_recursive($theme_css['tablet'],$tablet_css,$tablet_fixed_css);
	}
	
	//Mobile Css
	$mobile_css = [];
	$mobile_fixed_css = [];
	
	foreach($option_frame as $key => $val){
		$mobile_css['body']['padding-'.$key] = nexter_dimension_responsive_css($body_frame_padding, $key, 'xs');
		if ($fixed_body_frame == 'on') {
			$mobile_fixed_css['.nxt-body-frame.frame-'.$key] = [
				$val => nexter_dimension_responsive_css($body_frame_padding, $key, 'xs')
			];
		}
	}
	
	if($mobile_css){
		if(!isset($theme_css['mobile'])){
			$theme_css['mobile'] = [];
		}
		$theme_css['mobile'] = array_merge_recursive($theme_css['mobile'],$mobile_css,$mobile_fixed_css);
	}
	
	return $theme_css;
}

function nxt_body_editor_dynamic_css( $theme_css ){
	$body_bgcolor	= nexter_get_option('body-bgcolor');
	$content_bgcolor	= nexter_get_option('content-bgcolor');
	if(!empty($body_bgcolor)){
		$theme_css[]  = array(
			'body :where(.editor-styles-wrapper)' => nexter_get_background_css($body_bgcolor),
		);
	}
	if(!empty($content_bgcolor)){
		$theme_css[]  = array(
			'body :where(.editor-styles-wrapper)' => nexter_get_background_css($content_bgcolor)
		);
	}

	return $theme_css;
}

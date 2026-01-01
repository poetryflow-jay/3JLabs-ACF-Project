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

add_filter( 'nxt_render_theme_css', 'nxt_container_dynamic_css' );
add_filter( 'nxt_gutenberg_render_theme_css', 'nxt_container_editor_dynamic_css',1 );
function nxt_dimension_value($option='', $selector = '', $attr = '', $theme_css = [], $minus= ''){
	$option_dimension = ['left' => '', 'right' => ''];
	if(empty($option) || empty($selector) || empty($attr)){
		return $theme_css;
	}
	$fluid_pad = nexter_get_option($option);
	
	if(!empty($fluid_pad)){
		$style = ['dk' => [], 'tb' => [], 'mb' => []];
		foreach($option_dimension as $key => $val){
			$md_val = nexter_dimension_responsive_css($fluid_pad, $key, 'md');
			if($md_val!=''){
				$md_val = ($minus=='minus') ? '-'.$md_val : $md_val;
				$style['dk'][$selector][$attr.'-'.$key] = $md_val;
			}
			$sm_val = nexter_dimension_responsive_css($fluid_pad, $key, 'sm');
			if($sm_val!=''){
				$sm_val = ($minus=='minus') ? '-'.$sm_val : $sm_val;
				$style['tb'][$selector][$attr.'-'.$key] = $sm_val;
			}
			$xs_val = nexter_dimension_responsive_css($fluid_pad, $key, 'xs');
			if($xs_val!=''){
				$xs_val = ($minus=='minus') ? '-'.$xs_val : $xs_val;
				$style['mb'][$selector][$attr.'-'.$key] = $xs_val;
			}
		}
		if(!empty($style)){
			if( !empty($style['dk']) ){
				$theme_css[] = $style['dk'];
			}
			if( !empty($style['tb']) ){
				$tablet = isset($theme_css['tablet']) ? $theme_css['tablet'] : [];
				$theme_css['tablet'] = array_merge($tablet, $style['tb']);
			}
			if( !empty($style['mb']) ){
				$mobile = isset($theme_css['mobile']) ? $theme_css['mobile'] : [];
				$theme_css['mobile'] = array_merge($mobile, $style['mb']);
			}
		}
	}
	return $theme_css;
}

function nxt_container_dynamic_css( $theme_css ){
	$header_container_css = ['dk'=> [],'tb'=>[],'mb'=>[]];
	if ( nexter_settings_page_get( 'header_footer_css' ) ) {
		
		$header_container       = nexter_get_option('site-header-container');
		$header_container_width = nexter_get_option('site-header-container-width', []);
		$header_spacing = 'header-fluid-spacing';
		if ( $header_container === 'container-block-editor' ) {

			$header_container_width = wp_parse_args(
				$header_container_width,
				[
					'desktop'      => 1140,
					'tablet'       => 960,
					'mobile'       => '',
					'desktop-unit' => 'px',
					'tablet-unit'  => 'px',
					'mobile-unit'  => 'px',
				]
			);

		} elseif ( empty($header_container) ) {

			$site_layout_container = nexter_get_option('site-layout-container','container-block-editor');
			$layout_container      = nexter_get_option('layout-container', []);

			$header_container      = $site_layout_container;
			if($header_container == 'container-fluid'){
				$header_spacing = 'site-fluid-spacing';
			}
			$header_container_width = [
				'desktop'      => !empty($layout_container['desktop']) ? $layout_container['desktop'] : 1140,
				'tablet'       => !empty($layout_container['tablet'])  ? $layout_container['tablet']  : 960,
				'mobile'       => !empty($layout_container['mobile'])  ? $layout_container['mobile']  : '',
				'desktop-unit' => 'px',
				'tablet-unit'  => 'px',
				'mobile-unit'  => 'px',
			];
		}

		//Header Container
		if ( $header_container === 'container-block-editor' ) {

			$header_container_css['dk'] = [
				'#nxt-header .nxt-container-block-editor > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-template-load):not(.nxt-alignfull):not(.nxt-content-page-template),
				#nxt-header .nxt-container-block-editor > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull),
				.nxt-breadcrumb-wrap .nxt-container-block-editor > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull)' => [
					'max-width'    => 'calc(' . nexter_get_option_css_value($header_container_width['desktop'], 'px') . ' - 3rem)',
					'margin-right' => 'auto',
					'margin-left'  => 'auto',
				],
				'#nxt-header .nxt-container-block-editor .alignwide:not(.tpgb-container-row),
				#nxt-header .nxt-container-block-editor .tpgb-nxtcont-type,
				#nxt-header .tpgb-container' => [
					'max-width' => nexter_get_option_css_value($header_container_width['desktop'], 'px'),
				],
				'#nxt-header .nxt-container-block-editor .tpgb-nxtcont-type' => [
					'--tpgb-container-md' => nexter_get_option_css_value($header_container_width['desktop'], 'px'),
				],
			];

			$header_container_css['tb'] = [
				'#nxt-header .nxt-container-block-editor > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-template-load):not(.nxt-alignfull):not(.nxt-content-page-template),
				#nxt-header .nxt-container-block-editor > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull),
				.nxt-breadcrumb-wrap .nxt-container-block-editor > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull)' => [
					'max-width'    => 'calc(' . nexter_get_option_css_value($header_container_width['tablet'], 'px') . ' - 3rem)',
					'margin-right' => 'auto',
					'margin-left'  => 'auto',
				],
				'#nxt-header .nxt-container-block-editor .alignwide:not(.tpgb-container-row),
				#nxt-header .nxt-container-block-editor .tpgb-nxtcont-type,
				#nxt-header .tpgb-container' => [
					'max-width' => nexter_get_option_css_value($header_container_width['tablet'], 'px'),
				],
				'#nxt-header .nxt-container-block-editor .tpgb-nxtcont-type' => [
					'--tpgb-container-sm' => nexter_get_option_css_value($header_container_width['tablet'], 'px'),
				],
			];

			$header_container_css['mb'] = [
				'#nxt-header .nxt-container-block-editor > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-template-load):not(.nxt-alignfull):not(.nxt-content-page-template),
				#nxt-header .nxt-container-block-editor > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull),
				.nxt-breadcrumb-wrap .nxt-container-block-editor > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull)' => [
					'max-width'    => 'calc(' . nexter_get_option_css_value($header_container_width['mobile'], 'px') . ' - 3rem)',
					'margin-right' => 'auto',
					'margin-left'  => 'auto',
				],
				'#nxt-header .nxt-container-block-editor .alignwide:not(.tpgb-container-row),
				#nxt-header .nxt-container-block-editor .tpgb-nxtcont-type,
				#nxt-header .tpgb-container' => [
					'max-width' => nexter_get_option_css_value($header_container_width['mobile'], 'px'),
				],
				'#nxt-header .nxt-container-block-editor .tpgb-nxtcont-type' => [
					'--tpgb-container-xs' => nexter_get_option_css_value($header_container_width['mobile'], 'px'),
				],
			];

			$theme_css[] = [
				'#nxt-header .nxt-container-block-editor' => [
					'padding' => 0,
				],
			];

		} elseif ( $header_container === 'container' ) {

			$header_container_css['dk'] = [
				'#nxt-header .nxt-container, #nxt-header .tpgb-container' => [
					'max-width' => nexter_get_option_css_value($header_container_width['desktop'], 'px'),
				],
			];
			$header_container_css['tb'] = [
				'#nxt-header .nxt-container, #nxt-header .tpgb-container' => [
					'max-width' => nexter_get_option_css_value($header_container_width['tablet'], 'px'),
				],
			];
			$header_container_css['mb'] = [
				'#nxt-header .nxt-container, #nxt-header .tpgb-container' => [
					'max-width' => nexter_get_option_css_value($header_container_width['mobile'], 'px'),
				],
			];

		} elseif ( $header_container === 'container-fluid' ) {

			$theme_css = nxt_dimension_value(
				$header_spacing,
				'#nxt-header .nxt-container-fluid',
				'padding',
				$theme_css
			);
		}
	}

	$footer_container_css = ['dk'=> [],'tb'=>[],'mb'=>[]];
	if ( nexter_settings_page_get( 'header_footer_css' ) ) {

		$footer_container       = nexter_get_option('site-footer-container');
		$footer_container_width = nexter_get_option('site-footer-container-width', []);
		$footer_spacing = 'footer-fluid-spacing';
		if ($footer_container === 'container-block-editor') {
			$footer_container_width = wp_parse_args(
				$footer_container_width,
				[
					'desktop'      => 1140,
					'tablet'       => 960,
					'mobile'       => '',
					'desktop-unit' => 'px',
					'tablet-unit'  => 'px',
					'mobile-unit'  => 'px',
				]
			);

		} elseif ( empty($footer_container) ) {

			$site_layout_container = nexter_get_option('site-layout-container','container-block-editor');
			$layout_container      = nexter_get_option('layout-container', []);

			$footer_container      = $site_layout_container;
			if($footer_container == 'container-fluid'){
				$footer_spacing = 'site-fluid-spacing';
			}
			$footer_container_width = [
				'desktop'      => !empty($layout_container['desktop']) ? $layout_container['desktop'] : 1140,
				'tablet'       => !empty($layout_container['tablet'])  ? $layout_container['tablet']  : 960,
				'mobile'       => !empty($layout_container['mobile'])  ? $layout_container['mobile']  : '',
				'desktop-unit' => 'px',
				'tablet-unit'  => 'px',
				'mobile-unit'  => 'px',
			];
		}

		//Footer Container
		if (!empty($footer_container) && $footer_container == 'container-block-editor') {
			$footer_container_css['dk'] = array(
				'#nxt-footer .nxt-container-block-editor > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-template-load):not(.nxt-alignfull),#nxt-footer .nxt-container-block-editor > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce)' => array(
					'max-width' => (!empty($footer_container_width) && isset($footer_container_width['desktop']) && $footer_container_width['desktop']!='') ? 'calc('.nexter_get_option_css_value( $footer_container_width['desktop'], 'px' ).' - 3rem)' : 'calc(1140px - 3rem)',
					'margin-right' => 'auto',
					'margin-left' => 'auto'
				),
				'#nxt-footer .nxt-container-block-editor .alignwide:not(.tpgb-container-row), #nxt-footer .nxt-container-block-editor .tpgb-nxtcont-type, #nxt-footer .tpgb-container' => array(
					'max-width' => (!empty($footer_container_width) && isset($footer_container_width['desktop']) && $footer_container_width['desktop']!='') ? nexter_get_option_css_value( $footer_container_width['desktop'], 'px' ) : '1140px',
				),
				'#nxt-footer .nxt-container-block-editor .tpgb-nxtcont-type' => [
					'--tpgb-container-md' => (!empty($footer_container_width) && isset($footer_container_width['desktop']) && $footer_container_width['desktop']!='') ? nexter_get_option_css_value( $footer_container_width['desktop'], 'px' ) : '1140px',
				],
			);
			$footer_container_css['tb'] = array(
				'#nxt-footer .nxt-container-block-editor > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-template-load):not(.nxt-alignfull),#nxt-footer .nxt-container-block-editor > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce)' => array(
					'max-width' => (!empty($footer_container_width) && isset($footer_container_width['tablet']) && $footer_container_width['tablet']!='') ? nexter_get_option_css_value( $footer_container_width['tablet'], 'px' ) : 'calc(960px - 3rem)',
					'margin-right' => 'auto',
					'margin-left' => 'auto'
				),
				'#nxt-footer .nxt-container-block-editor .alignwide:not(.tpgb-container-row), #nxt-footer .nxt-container-block-editor .tpgb-nxtcont-type, #nxt-footer .tpgb-container' => array(
					'max-width' => (!empty($footer_container_width) && isset($footer_container_width['tablet']) && $footer_container_width['tablet']!='') ? nexter_get_option_css_value( $footer_container_width['tablet'], 'px' ) : '960px',
				),
				'#nxt-footer .nxt-container-block-editor .tpgb-nxtcont-type' => [
					'--tpgb-container-sm' => (!empty($footer_container_width) && isset($footer_container_width['tablet']) && $footer_container_width['tablet']!='') ? nexter_get_option_css_value( $footer_container_width['tablet'], 'px' ) : '960px',
				],
			);
			$footer_container_css['mb'] = array(
				'#nxt-footer .nxt-container-block-editor > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-template-load):not(.nxt-alignfull),#nxt-footer .nxt-container-block-editor > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce)' => array(
					'max-width' => (!empty($footer_container_width) && isset($footer_container_width['mobile']) && $footer_container_width['mobile']!='') ? nexter_get_option_css_value( $footer_container_width['mobile'], 'px' ) : 'calc(1140px - 3rem)',
					'margin-right' => 'auto',
					'margin-left' => 'auto'
				),
				'#nxt-footer .nxt-container-block-editor .alignwide:not(.tpgb-container-row), #nxt-footer .nxt-container-block-editor .tpgb-nxtcont-type, #nxt-footer .tpgb-container' => array(
					'max-width' => (!empty($footer_container_width) && isset($footer_container_width['mobile']) && $footer_container_width['mobile']!='') ? nexter_get_option_css_value( $footer_container_width['mobile'], 'px' ) : '1140px',
				),
				'#nxt-footer .nxt-container-block-editor .tpgb-nxtcont-type' => [
					'--tpgb-container-xs' => (!empty($footer_container_width) && isset($footer_container_width['mobile']) && $footer_container_width['mobile']!='') ? nexter_get_option_css_value( $footer_container_width['mobile'], 'px' ) : '1140px',
				],
			);
		}
		if (!empty($footer_container) && $footer_container == 'container' && !empty($footer_container_width['desktop'])) {
			$footer_container_css['dk'] = array(
				'#nxt-footer .nxt-container, #nxt-footer .tpgb-container' => array(
					'max-width' => (!empty($footer_container_width) && isset($footer_container_width['desktop']) && $footer_container_width['desktop']!='') ? nexter_get_option_css_value( $footer_container_width['desktop'], 'px' ) : '',
				),
			);
			$footer_container_css['tb'] = array(
				'#nxt-footer .nxt-container, #nxt-footer .tpgb-container' => array(
					'max-width' => (!empty($footer_container_width) && isset($footer_container_width['tablet']) && $footer_container_width['tablet']!='') ? nexter_get_option_css_value( $footer_container_width['tablet'], 'px' ) : '',
				),
			);
			$footer_container_css['mb'] = array(
				'#nxt-footer .nxt-container, #nxt-footer .tpgb-container' => array(
					'max-width' => (!empty($footer_container_width) && isset($footer_container_width['mobile']) && $footer_container_width['mobile']!='') ? nexter_get_option_css_value( $footer_container_width['mobile'], 'px' ) : '',
				),
			);
		}
		if(!empty($footer_container) && $footer_container == 'container-fluid'){
			$theme_css = nxt_dimension_value($footer_spacing, '#nxt-footer .nxt-container-fluid', 'padding', $theme_css );
		}
	}

	$site_layout_container = nexter_get_option('site-layout-container','container-block-editor');
	$layout_container = nexter_get_option('layout-container');
	
	//Site Layout Default Container
	$site_layout_container_css = ['dk'=> [],'tb'=>[],'mb'=>[]];
	if (!empty($site_layout_container) && $site_layout_container == 'container-block-editor') {
		$site_desktop_val = (!empty($layout_container) && isset($layout_container['desktop']) && $layout_container['desktop']!='') ? nexter_get_option_css_value($layout_container['desktop'], 'px') : '1140px';
		$site_desktop_calc = $site_desktop_val ? 'calc(' . $site_desktop_val . ' - 3rem)' : 'calc(1140px - 3rem)';
		
		$site_layout_container_css['dk'] = array(
			'.site-content .nxt-container-block-editor > .nxt-row article > .entry-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull):not(.tpgb-container-wide),.nxt-container-block-editor .site-main > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.nxt-blog-single-post):not(.error-404),.nxt-container-block-editor .site-main .nxt-blog-single-post > article > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.nxt-single-post-content), .nxt-container-block-editor .site-main .nxt-single-post-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull), .site-content > .nxt-container-block-editor > *:not(.content-area):not(.nxt-row):not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull):not(.nxt-content-page-template), .nxt-container-block-editor > .nxt-content-page-template > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.content-area),
			.nxt-container-block-editor .site-main > .error-404 > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.nxt-template-load):not(.elementor), .nxt-container-block-editor .site-main > .error-404 > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.elementor),.nxt-container-block-editor.nxt-archive-cont > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.content-area)' => array(
				'max-width' => $site_desktop_calc,
				'margin-right' => 'auto',
				'margin-left' => 'auto'
			),
			'.site-content .nxt-container-block-editor .alignwide:not(.tpgb-container-row), .site-content .nxt-container-block-editor .tpgb-nxtcont-type, .nxt-container.nxt-with-sidebar' => array(
				'max-width' => $site_desktop_val,
			),
			'.site-content .nxt-container-block-editor .tpgb-nxtcont-type' => [
				'--tpgb-container-md' => $site_desktop_val,
			],
			':root' => [
				'--nexter-content-width' => $site_desktop_calc,
				'--nexter-wide-content-width' => $site_desktop_val,
			]
		);

		$site_tablet_val = (!empty($layout_container) && isset($layout_container['tablet']) && $layout_container['tablet']!='') ? nexter_get_option_css_value($layout_container['tablet'], 'px') : '960px';
		$site_tablet_calc = $site_tablet_val ? 'calc(' . $site_tablet_val . ' - 3rem)' : 'calc(960px - 3rem)';

		$site_layout_container_css['tb'] = array(
			'.site-content .nxt-container-block-editor > .nxt-row article > .entry-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull):not(.tpgb-container-wide),.nxt-container-block-editor .site-main > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.nxt-blog-single-post):not(.error-404),.nxt-container-block-editor .site-main .nxt-blog-single-post > article > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.nxt-single-post-content), .nxt-container-block-editor .site-main .nxt-single-post-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull), .site-content > .nxt-container-block-editor > *:not(.content-area):not(.nxt-row):not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull):not(.nxt-content-page-template), .nxt-container-block-editor > .nxt-content-page-template > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.content-area), .nxt-container-block-editor .site-main > .error-404 > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.nxt-template-load):not(.elementor), .nxt-container-block-editor .site-main > .error-404 > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.elementor),.nxt-container-block-editor.nxt-archive-cont > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.content-area)' => array(
				'max-width' => $site_tablet_calc,
				'margin-right' => 'auto',
				'margin-left' => 'auto'
			),
			'.site-content .nxt-container-block-editor .alignwide:not(.tpgb-container-row), .site-content .nxt-container-block-editor .tpgb-nxtcont-type, .nxt-container.nxt-with-sidebar' => array(
				'max-width' => $site_tablet_val,
			),
			'.site-content .nxt-container-block-editor .tpgb-nxtcont-type' => [
				'--tpgb-container-sm' => $site_tablet_val,
			],
			':root' => [
				'--nexter-content-width' => $site_tablet_calc,
				'--nexter-wide-content-width' => $site_tablet_val,
			]
		);

		$site_mobile_val = (!empty($layout_container) && isset($layout_container['mobile']) && $layout_container['mobile']!='') ? nexter_get_option_css_value($layout_container['mobile'], 'px') : '1140px';
		$site_mobile_calc = $site_mobile_val ? 'calc(' . $site_mobile_val . ' - 3rem)' : 'calc(1140px - 3rem)';

		$site_layout_container_css['mb'] = array(
			'.site-content .nxt-container-block-editor > .nxt-row article > .entry-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull):not(.tpgb-container-wide), .nxt-container-block-editor .site-main > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.nxt-blog-single-post):not(.error-404),.nxt-container-block-editor .site-main .nxt-blog-single-post > article > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.nxt-single-post-content), .nxt-container-block-editor .site-main .nxt-single-post-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull), .site-content > .nxt-container-block-editor > *:not(.content-area):not(.nxt-row):not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull):not(.nxt-content-page-template), .nxt-container-block-editor > .nxt-content-page-template > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.content-area), .nxt-container-block-editor .site-main > .error-404 > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.nxt-template-load):not(.elementor), .nxt-container-block-editor .site-main > .error-404 > .nxt-template-load > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.elementor),.nxt-container-block-editor.nxt-archive-cont > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.content-area)' => array(
				'max-width' => $site_mobile_val,
				'margin-right' => 'auto',
				'margin-left' => 'auto'
			),
			'.site-content .nxt-container-block-editor .alignwide:not(.tpgb-container-row), .site-content .nxt-container-block-editor .tpgb-nxtcont-type, .nxt-container.nxt-with-sidebar' => array(
				'max-width' => $site_mobile_val,
			),
			'.site-content .nxt-container-block-editor .tpgb-nxtcont-type' => [
				'--tpgb-container-xs' => $site_mobile_val,
			],
			':root' => [
				'--nexter-content-width' => $site_mobile_calc,
				'--nexter-wide-content-width' => $site_mobile_val,
			]
		);
	}
	if (!empty($site_layout_container) && $site_layout_container == 'container') {
		if( isset($layout_container['desktop']) && !empty($layout_container['desktop']) ){
			$site_layout_container_css['dk'] = array(
				'.site-content .nxt-container' => array(
					'max-width' => nexter_get_option_css_value( $layout_container['desktop'], 'px' )
				)
			);
		}
		if( isset($layout_container['tablet']) && !empty($layout_container['tablet']) ){
			$site_layout_container_css['tb'] = array(
				'.site-content .nxt-container' => array(
					'max-width' => nexter_get_option_css_value( $layout_container['tablet'], 'px' )
				)
			);
		}
		if( isset($layout_container['mobile']) && !empty($layout_container['mobile']) ){
			$site_layout_container_css['mb'] = array(
				'.site-content .nxt-container' => array(
					'max-width' => nexter_get_option_css_value( $layout_container['mobile'], 'px' )
				)
			);
		}
	}
	
	if(!empty($site_layout_container) && $site_layout_container == 'container-fluid'){
		$theme_css = nxt_dimension_value('site-fluid-spacing', '.site-content .nxt-container-fluid:not(.nxt-archive-cont),.site-content .nxt-container-fluid:not(.nxt-archive-cont) .nxt-row .nxt-col', 'padding', $theme_css );
		$theme_css = nxt_dimension_value('site-fluid-spacing', '.site-content .nxt-container-fluid:not(.nxt-archive-cont) .nxt-row,.archive-page-header', 'margin', $theme_css, 'minus' );
	}

	$site_page_container = nexter_get_option('site-page-container');
	$layout_page_container = nexter_get_option('layout-page-container');
	
	//Page Container
	$page_layout_container_css = ['dk'=> [],'tb'=>[],'mb'=>[]];
	if (is_page() && !empty($site_page_container) && $site_page_container == 'container-block-editor') {
		
		$desktop_layout = !empty($layout_page_container['desktop']) ? nexter_get_option_css_value($layout_page_container['desktop'], 'px') : '';

		$page_layout_container_css['dk'] = [
			'.site-content .nxt-page-cont.nxt-container-block-editor > .nxt-row article > .entry-content >*:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce)' => [
				'max-width' => $desktop_layout ? 'calc('.$desktop_layout.' - 3rem)' : '',
			],
			'.site-content .nxt-page-cont.nxt-container-block-editor .alignwide:not(.tpgb-container-row), 
			.site-content .nxt-page-cont.nxt-container-block-editor .tpgb-nxtcont-type, 
			.nxt-container.nxt-with-sidebar' => [
				'max-width' => $desktop_layout,
			],
			'.site-content .nxt-page-cont.nxt-container-block-editor .tpgb-nxtcont-type' => [
				'--tpgb-container-md' => $desktop_layout,
			],
		];

		$tablet_val = '';
		if ( ! empty($layout_page_container['tablet']) ) {
			$tablet_val = nexter_get_option_css_value($layout_page_container['tablet'], 'px');
		}

		if ( $tablet_val ) {
			$page_layout_container_css['tb'] = [
				'.site-content .nxt-page-cont.nxt-container-block-editor > .nxt-row article > .entry-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce)' => [
					'max-width' => "calc('.$tablet_val.' - 3rem)",
				],
				'.site-content .nxt-page-cont.nxt-container-block-editor .alignwide:not(.tpgb-container-row), 
				.site-content .nxt-page-cont.nxt-container-block-editor .tpgb-nxtcont-type, 
				.nxt-container.nxt-with-sidebar' => [
					'max-width' => $tablet_val,
				],
			];
		}

		$mobile_val = '';
		if ( ! empty($layout_page_container['mobile']) ) {
			$mobile_val = nexter_get_option_css_value($layout_page_container['mobile'], 'px');
		}

		if ( $mobile_val ) {
			$page_layout_container_css['mb'] = [
				'.site-content .nxt-page-cont.nxt-container-block-editor > .nxt-row article > .entry-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce)' => [
					'max-width' => $mobile_val,
				],
				'.site-content .nxt-page-cont.nxt-container-block-editor .alignwide:not(.tpgb-container-row), 
				.site-content .nxt-page-cont.nxt-container-block-editor .tpgb-nxtcont-type, 
				.nxt-container.nxt-with-sidebar' => [
					'max-width' => $mobile_val,
				],
			];
		}
	}
	if (is_page() && !empty($site_page_container) && $site_page_container == 'container' ) {
		$breakpoints = [
			'dk' => 'desktop',
			'tb' => 'tablet',
			'mb' => 'mobile',
		];

		foreach ( $breakpoints as $key => $option_key ) {
			if (isset($layout_page_container[$option_key])  && !empty($layout_page_container[$option_key]) ) {
				$val = nexter_get_option_css_value($layout_page_container[$option_key], 'px');
				if($val){
					$page_layout_container_css[$key] = [
						'.nxt-page-cont.nxt-container' => [
							'max-width' => $val,
						],
					];
				}
				
			}
		}
	}

	if(is_page() && !empty($site_page_container) && $site_page_container == 'container-fluid'){
		$theme_css = nxt_dimension_value('page-fluid-spacing', '.site-content .nxt-container-fluid,.site-content .nxt-container-fluid .nxt-row .nxt-col', 'padding', $theme_css );
		$theme_css = nxt_dimension_value('page-fluid-spacing', '.site-content .nxt-container-fluid .nxt-row', 'margin', $theme_css, 'minus' );
	}
	
	$site_posts_container = nexter_get_option('site-posts-container');
	$layout_posts_container = nexter_get_option('layout-posts-container');
	
	//Post Container
	$post_layout_container_css = ['dk'=> [],'tb'=>[],'mb'=>[]];
	if (is_single() && !empty($site_posts_container) && $site_posts_container == 'container-block-editor') {
		$desktop_val = '';
		if ( isset($layout_posts_container['desktop']) && ! empty($layout_posts_container['desktop']) ) {
			$desktop_val = nexter_get_option_css_value($layout_posts_container['desktop'], 'px');
		}

		if ( $desktop_val ) {
			$post_layout_container_css['dk'] = [
				'.site-content > .nxt-post-cont.nxt-container-block-editor .site-main > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.nxt-blog-single-post),
				.nxt-container-block-editor .site-main .nxt-blog-single-post > article > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.nxt-single-post-content), 
				.nxt-container-block-editor .site-main .nxt-single-post-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull), 
				.site-content > .nxt-post-cont.nxt-container-block-editor > *:not(.content-area):not(.nxt-row):not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull):not(.nxt-content-page-template), 
				.nxt-container-block-editor > .nxt-content-page-template > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.content-area)' => [
					'max-width' => "calc('.$desktop_val.' - 3rem)",
				],
				'.site-content .nxt-post-cont.nxt-container-block-editor .alignwide:not(.tpgb-container-row), 
				.site-content .nxt-post-cont.nxt-container-block-editor .tpgb-nxtcont-type, 
				.nxt-container.nxt-with-sidebar' => [
					'max-width' => $desktop_val,
				],
				'.site-content .nxt-post-cont.nxt-container-block-editor .tpgb-nxtcont-type' => [
					'--tpgb-container-md' => $desktop_val,
				],
			];
		}
		$tablet_val = '';
		if ( isset($layout_posts_container['tablet']) && ! empty($layout_posts_container['tablet']) ) {
			$tablet_val = nexter_get_option_css_value($layout_posts_container['tablet'], 'px');
		}

		if ( $tablet_val ) {
			$post_layout_container_css['tb'] = [
				'.site-content > .nxt-post-cont.nxt-container-block-editor .site-main > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.nxt-blog-single-post),
				.nxt-container-block-editor .site-main .nxt-blog-single-post > article > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.nxt-single-post-content), 
				.nxt-container-block-editor .site-main .nxt-single-post-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull), 
				.site-content > .nxt-post-cont.nxt-container-block-editor > *:not(.content-area):not(.nxt-row):not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull):not(.nxt-content-page-template), 
				.nxt-container-block-editor > .nxt-content-page-template > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.content-area)' => [
					'max-width' => "calc('.$tablet_val.' - 3rem)",
				],

				'.site-content .nxt-post-cont.nxt-container-block-editor .alignwide:not(.tpgb-container-row), 
				.site-content .nxt-post-cont.nxt-container-block-editor .tpgb-nxtcont-type, 
				.nxt-container.nxt-with-sidebar' => [
					'max-width' => $tablet_val,
				],

				'.site-content .nxt-post-cont.nxt-container-block-editor .tpgb-nxtcont-type' => [
					'--tpgb-container-sm' => $tablet_val,
				],
			];
		}
		$mobile_val = '';
		if ( isset($layout_posts_container['mobile']) && ! empty($layout_posts_container['mobile']) ) {
			$mobile_val = nexter_get_option_css_value($layout_posts_container['mobile'], 'px');
		}

		if ( $mobile_val ) {
			$post_layout_container_css['mb'] = [
				'.site-content > .nxt-post-cont.nxt-container-block-editor .site-main > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.nxt-blog-single-post),
				.nxt-container-block-editor .site-main .nxt-blog-single-post > article > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.nxt-single-post-content), 
				.nxt-container-block-editor .site-main .nxt-single-post-content > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull), 
				.site-content > .nxt-post-cont.nxt-container-block-editor > *:not(.content-area):not(.nxt-row):not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull):not(.nxt-content-page-template), 
				.nxt-container-block-editor > .nxt-content-page-template > *:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull):not(.content-area)' => [
					'max-width' => $mobile_val,
				],
				'.site-content .nxt-post-cont.nxt-container-block-editor .alignwide:not(.tpgb-container-row), 
				.site-content .nxt-post-cont.nxt-container-block-editor .tpgb-nxtcont-type, 
				.nxt-container.nxt-with-sidebar' => [
					'max-width' => $mobile_val,
				],
				'.site-content .nxt-post-cont.nxt-container-block-editor .tpgb-nxtcont-type' => [
					'--tpgb-container-xs' => $mobile_val,
				],
			];
		}
	}
	if (is_single() && !empty($site_posts_container) && $site_posts_container == 'container' ) {
		$breakpoints = [
			'dk' => 'desktop',
			'tb' => 'tablet',
			'mb' => 'mobile',
		];

		foreach ($breakpoints as $key => $device) {
			$value = (!empty($layout_posts_container) && isset($layout_posts_container[$device]) && !empty($layout_posts_container[$device]))
				? nexter_get_option_css_value($layout_posts_container[$device], 'px')
				: '';
			if($value){
				$post_layout_container_css[$key] = [
					'.nxt-post-cont.nxt-container' => [
						'max-width' => $value,
					],
				];
			}
			
		}
	}

	if(is_single() && !empty($site_posts_container) && $site_posts_container == 'container-fluid'){
		$theme_css = nxt_dimension_value('post-fluid-spacing', '.site-content .nxt-container-fluid,.site-content .nxt-container-fluid .nxt-row .nxt-col', 'padding', $theme_css );
		$theme_css = nxt_dimension_value('post-fluid-spacing', '.site-content .nxt-container-fluid .nxt-row', 'margin', $theme_css, 'minus' );
	}

	$site_archive_container = nexter_get_option('site-archive-container');
	$layout_archive_container = nexter_get_option('layout-archive-container');
	
	//Archive Container
	$archive_layout_container_css = ['dk'=> [],'tb'=>[],'mb'=>[]];
	if ((is_home() || is_archive() || is_tax() || is_search() || (isset( $wp_query ) && (bool) $wp_query->is_posts_page)) && ( !function_exists('is_woocommerce') || !is_woocommerce() ) && !empty($site_archive_container) && $site_archive_container == 'container-block-editor') {
		
		$breakpoints = [
			'dk' => ['key' => 'desktop', 'var' => '--tpgb-container-md', 'calc' => true],
			'tb' => ['key' => 'tablet',  'var' => '--tpgb-container-sm', 'calc' => true],
			'mb' => ['key' => 'mobile',  'var' => '--tpgb-container-xs', 'calc' => false],
		];

		foreach ($breakpoints as $bp => $conf) {
			$device = $conf['key'];
			$value  = (isset($layout_archive_container[$device]) && !empty($layout_archive_container[$device]))
				? nexter_get_option_css_value($layout_archive_container[$device], 'px')
				: '';
			if($value){
				$archive_layout_container_css[$bp] = [
					// First selector
					'.site-content >.nxt-container-block-editor.nxt-archive-cont >*:not(.content-area):not(.nxt-row):not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(.nxt-alignfull), .nxt-container-block-editor.nxt-archive-cont .site-main >*:not(.alignwide):not(.alignfull):not(.alignleft):not(.alignright):not(.wp-block-separator):not(.woocommerce):not(article):not(.nxt-alignfull)'
						=> ['max-width' => ($conf['calc'] && $value) ? "calc('.$value.' - 3rem)" : $value],

					// Second selector
					'.site-content .nxt-archive-cont.nxt-container-block-editor .alignwide:not(.tpgb-container-row), .site-content .nxt-archive-cont.nxt-container-block-editor .tpgb-nxtcont-type, .nxt-container.nxt-with-sidebar'
						=> ['max-width' => $value],

					// CSS variable
					'.site-content .nxt-archive-cont.nxt-container-block-editor .tpgb-nxtcont-type'
						=> [$conf['var'] => $value],
				];
			}
		}

	}
	
	if ((is_home() || is_archive() || is_search() || (isset( $wp_query ) && (bool) $wp_query->is_posts_page)) && (!function_exists('is_shop') || !is_shop()) && !empty($site_archive_container) && $site_archive_container == 'container' ) {
		$breakpoints = [
			'dk' => 'desktop',
			'tb' => 'tablet',
			'mb' => 'mobile',
		];

		foreach ($breakpoints as $bp => $device) {
			$value = (isset($layout_archive_container[$device]) && !empty($layout_archive_container[$device]))
				? nexter_get_option_css_value($layout_archive_container[$device], 'px')
				: '';
			if($value){
				$archive_layout_container_css[$bp] = [
					'.nxt-archive-cont.nxt-container' => [
						'max-width' => $value,
					],
				];
			}
		}

	}
	
	if((is_home() || is_archive() || is_search() || (isset( $wp_query ) && (bool) $wp_query->is_posts_page)) && (!function_exists('is_shop') || !is_shop()) && !empty($site_archive_container) && $site_archive_container == 'container-fluid'){
		$theme_css = nxt_dimension_value('archive-fluid-spacing', '.site-content .nxt-container-fluid,.site-content .nxt-container-fluid .site-main > .nxt-row > .nxt-col', 'padding', $theme_css );
		$theme_css = nxt_dimension_value('archive-fluid-spacing', '.site-content .nxt-container-fluid .site-main > .nxt-row,.archive-page-header', 'margin', $theme_css, 'minus' );
	}
	
	$theme_css['container_m']= array_merge($site_layout_container_css['mb'], $header_container_css['mb'],$footer_container_css['mb'],$page_layout_container_css['mb'],$post_layout_container_css['mb'],$archive_layout_container_css['mb']);
	$theme_css['container_t']= array_merge($site_layout_container_css['tb'], $header_container_css['tb'],$footer_container_css['tb'],$page_layout_container_css['tb'],$post_layout_container_css['tb'],$archive_layout_container_css['tb']);
	$theme_css['container_d']= array_merge($site_layout_container_css['dk'], $header_container_css['dk'],$footer_container_css['dk'],$page_layout_container_css['dk'],$post_layout_container_css['dk'],$archive_layout_container_css['dk']);
	
	return $theme_css;
}

function nxt_container_editor_dynamic_css( $theme_css ){
	
	$site_layout_container = nexter_get_option('site-layout-container','container-block-editor');
	$layout_container	= nexter_get_option('layout-container', ['desktop' => 1140]);
	
	//Site Layout Default Container
	$site_layout_container_css = ['dk'=> [] ];
	if (!empty($site_layout_container) && $site_layout_container == 'container-block-editor') {
		$val = (!empty($layout_container) && isset($layout_container['desktop']) && $layout_container['desktop']!='') ? nexter_get_option_css_value( $layout_container['desktop'], 'px' ) : '1140px';
		//Nexter block Container
		$site_layout_container_css['dk'] = array(
			'.tpgb-nxtcont-type.alignfull' => [
				'--tpgb-container-md' => (!empty($layout_container) && isset($layout_container['desktop']) && $layout_container['desktop']!='') ? nexter_get_option_css_value( $layout_container['desktop'], 'px' ) : '',
			],
		);
		$theme_css['root'] = [
			':root' => [
				'--nexter-content-width' => (!empty($layout_container) && !empty($layout_container['desktop'])) ? 'calc('.nexter_get_option_css_value( $layout_container['desktop'], 'px' ).' - 3rem)' : 'calc(1140px - 3rem)',
				'--nexter-wide-content-width' => $val
			]
		];
		$theme_css['root_tablet'] = [
			':root' => [
				'--nexter-content-width' => (!empty($layout_container)) ? 'calc(768px - 3rem)' : '',
				'--nexter-wide-content-width' => '768px'
			]
		];
	}else{
		$val = (!empty($layout_container) && isset($layout_container['desktop']) && $layout_container['desktop']!='') ? nexter_get_option_css_value( $layout_container['desktop'], 'px' ) : '1140px';
		//Nexter block Container
		$site_layout_container_css['dk'] = array(
			'.tpgb-nxtcont-type.alignfull' => [
				'--tpgb-container-md' => (!empty($layout_container) && isset($layout_container['desktop']) && $layout_container['desktop']!='') ? nexter_get_option_css_value( $layout_container['desktop'], 'px' ) : '',
			],
		);
		$theme_css['root'] = [
			':root' => [
				'--nexter-content-width' => (!empty($layout_container) && !empty($layout_container['desktop'])) ? 'calc('.nexter_get_option_css_value( $layout_container['desktop'], 'px' ).' - 3rem)' : 'calc(1140px - 3rem)',
				'--nexter-wide-content-width' => $val
			]
		];
		$theme_css['root_tablet'] = [
			':root' => [
				'--nexter-content-width' => (!empty($layout_container)) ? 'calc(768px - 3rem)' : '',
				'--nexter-wide-content-width' => '768px'
			]
		];
	}
	if( !empty($site_layout_container_css['dk'])){
		$theme_css[] = $site_layout_container_css['dk'];
	}

	return $theme_css;
}
<?php
/**
 * Single Blog Options for Nexter Theme.
 *
 * @package     Nexter
 * @since       Nexter 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_filter( 'nxt_render_theme_css', 'single_blog_dynamic_css');
function single_blog_dynamic_css( $theme_css ){
	
	// Title CSS
	if ( is_single() && (nexter_get_option( 's-display-post-title' ) === 'on' || empty( nexter_get_option( 's-display-post-title' ) )) ) {
		$fs   = nexter_get_option( 'font-size-s-blog-title' );
		$lh   = nexter_get_option( 's-blog-title-line-height' );
		$ff   = nexter_get_option( 's-blog-title-font-family' );
		$fw   = nexter_get_option( 's-blog-title-font-weight' );
		$tf   = nexter_get_option( 's-blog-title-transform' );
		$ls   = nexter_get_option( 's-blog-title-letter-spacing' );
		$clr  = nexter_get_option( 's-blog-title-color' );

		$title = [
			'.single-post-title h1' => [
				'font-family'    => nexter_get_font_family_css( $ff ),
				'font-weight'    => esc_attr( $fw ),
				'font-size'      => nexter_responsive_size_css( $fs, 'desktop' ),
				'line-height'    => esc_attr( $lh ),
				'text-transform' => esc_attr( $tf ),
				'letter-spacing' => $ls !== '' ? esc_attr( $ls ) . 'px' : '',
				'color'          => esc_attr( $clr ),
			],
		];
		$theme_css['tablet']['.single-post-title h1']['font-size'] = nexter_responsive_size_css( $fs, 'tablet' );
		$theme_css['mobile']['.single-post-title h1']['font-size'] = nexter_responsive_size_css( $fs, 'mobile' );
	} else {
		$title = [];
	}

	// Post Meta CSS
	$post_primary = nexter_get_option( 's-blog-primary-color' );
	if (
		( is_single() && ( nexter_get_option( 's-display-post-meta' ) === 'on' || empty( nexter_get_option( 's-display-post-meta' ) ) ) )
		|| is_archive() || is_search() || is_home()
	) {
		$fs   = nexter_get_option( 'font-size-s-post-meta' );
		$lh   = nexter_get_option( 's-post-meta-line-height' );
		$ff   = nexter_get_option( 's-post-meta-font-family' );
		$fw   = nexter_get_option( 's-post-meta-font-weight' );
		$tf   = nexter_get_option( 's-post-meta-transform' );
		$ls   = nexter_get_option( 's-post-meta-letter-spacing' );
		$clr  = nexter_get_option( 's-post-meta-color' );

		$post_meta = [
			'.nxt-meta-info' => [
				'font-family'    => nexter_get_font_family_css( $ff ),
				'font-weight'    => esc_attr( $fw ),
				'font-size'      => nexter_responsive_size_css( $fs, 'desktop' ),
				'line-height'    => esc_attr( $lh ),
				'letter-spacing' => $ls !== '' ? esc_attr( $ls ) . 'px' : '',
				'text-transform' => esc_attr( $tf ),
			],
			'.nxt-meta-info,.nxt-meta-info a' => [
				'color' => esc_attr( $clr ),
			],
			'.nxt-meta-info a:focus, .nxt-meta-info a:hover' => [
				'color' => esc_attr( $post_primary ),
			],
		];
		$theme_css['tablet']['.nxt-meta-info']['font-size'] = nexter_responsive_size_css( $fs, 'tablet' );
		$theme_css['mobile']['.nxt-meta-info']['font-size'] = nexter_responsive_size_css( $fs, 'mobile' );
	} else {
		$post_meta = [];
	}

	// Primary Color Styles
	$extra = [];
	if ( $post_primary ) {
		$pc = esc_attr( $post_primary );
		$extra = [
			'.nxt-post-next-prev .prev:hover span:last-child, .nxt-post-next-prev .next:hover span:last-child, .author-meta-title:hover' => [ 'color' => $pc ],
			'.nxt-post-tags ul li a:hover' => [ 'background' => $pc, 'border-color' => $pc ],
			'input:focus, input[type="text"]:focus, input[type="email"]:focus, input[type="url"]:focus, input[type="password"]:focus, input[type="reset"]:focus, input[type="search"]:focus, textarea:focus' => [ 'border-color' => $pc ],
			'.button, input#submit, input[type="button"], input[type="submit"], input[type="reset"]' => [ 'background' => $pc, 'border-color' => $pc ],
		];
	}

	// Merge only once
	if ( $title || $post_meta || $extra ) {
		$theme_css[] = $title + $post_meta + $extra;
	}

	return $theme_css;
}
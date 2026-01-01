<?php
/**
 *  Nexter Theme Hooks.
 *
 * @see  https://github.com/zamoose/themehookalliance
 *
 * @package	Nexter
 * @since	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Declare theme support for Nexter hooks.
 */
add_theme_support( 'nexter_hooks', [
    'all',
    'html', 'body', 'head', 'header', 'content', 'entry', 'comments', 'sidebars', 'sidebar', 'footer'
] );

/**
 * Determines, whether the specific hook type is actually supported.
 */
function nexter_current_theme_supports( $bool, $args, $registered ) {
    return isset( $args[0] ) && ( in_array( $args[0], $registered[0], true ) || in_array( 'all', $registered[0], true ) );
}
add_filter( 'current_theme_supports-nexter_hooks', 'nexter_current_theme_supports', 10, 3 );

if ( ! function_exists( 'wp_body_open' ) ) {
    function wp_body_open() {
        do_action( 'wp_body_open' );
    }
}

/**
 * HTML <html> hook
 * Special case, useful for <DOCTYPE>, etc.
 * $nxt_supports[] = 'html;
 */
function nxt_html_before() {
	do_action( 'nxt_html_before' );
}

/**
 * HTML <head> hooks
 * $nxt_supports[] = 'head';
 */
function nxt_head_top() {
	do_action( 'nxt_head_top' );
}

/**
 * Head Bottom
 */
function nxt_head_bottom() {
	do_action( 'nxt_head_bottom' );
}

/**
 * HTML <body> hooks
 * $nxt_supports[] = 'body';
 */
function nxt_body_top() {
	do_action( 'nxt_body_top' );
}

/**
 * <header> hooks
 * Header Before
 * $nxt_supports[] = 'header';
 */
function nxt_header_before() {
	do_action( 'nxt_header_before' );
}
/**
 * Header After
 */
function nxt_header_after() {
	do_action( 'nxt_header_after' );
}


/**
 * Comments hooks
 * $nxt_supports[] = 'comments';
 */
function nxt_comments_before() {
	do_action( 'nxt_comments_before' );
}

/**
 * Comments after.
 */
function nxt_comments_after() {
	do_action( 'nxt_comments_after' );
}

/**
 * <sidebar> hooks
 * Sidebars Before
 * $nxt_supports[] = 'sidebar';
 */
function nxt_sidebars_before() {
	do_action( 'nxt_sidebars_before' );
}

/**
 * Sidebars after
 */
function nxt_sidebars_after() {
	do_action( 'nxt_sidebars_after' );
}

/**
 * <content> hooks
 * Content Top
 * $nxt_supports[] = 'content';
 */
function nxt_content_top() {
	do_action( 'nxt_content_top' );
}

/**
 * Content bottom
 */
function nxt_content_bottom() {
	do_action( 'nxt_content_bottom' );
}
/**
 * <footer> hooks
 * Footer Before
 * $nxt_supports[] = 'footer';
 */
function nxt_footer_before() {
	do_action( 'nxt_footer_before' );
}

/**
 * Footer after
 */
function nxt_footer_after() {
	do_action( 'nxt_footer_after' );
}

/**
 * HTML </body> hooks
 * $nxt_supports[] = 'body';
 */
function nxt_body_bottom() {
	do_action( 'nxt_body_bottom' );
}
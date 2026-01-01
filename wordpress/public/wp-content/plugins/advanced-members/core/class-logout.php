<?php
namespace AMem;

use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

/**
 * Class Logout
 *
 * @package AMem
 */
class Logout extends Module {

	protected $name = 'amem/logout';

	/**
	 * Logout constructor.
	 */
	function __construct() {
		add_action( 'template_redirect', array( $this, 'init_logout' ), 999 );
	}

	/**
	 * Logout via logout page
	 */
	function init_logout() {
		if ( is_home() ) {
			return;
		}

		if(  amem_is_core_page( 'logout' ) ){
			if ( is_user_logged_in() ) {
				$redirect_url = apply_filters( 'amem/redirects/logout', home_url() );
				if ( isset( $_REQUEST['redirect_to'] ) && '' !== sanitize_text_field($_REQUEST['redirect_to']) ) { // phpcs:disable WordPress.Security.NonceVerification
					$redirect_url = esc_url_raw( sanitize_text_field($_REQUEST['redirect_to']) ); // phpcs:disable WordPress.Security.NonceVerification
				}

				wp_destroy_current_session();
				wp_logout();
				session_unset();
				wp_safe_redirect( $redirect_url );
			} else {
				wp_safe_redirect( home_url() );
			}
		}
	}

}
amem()->register_module('logout', Logout::getInstance());

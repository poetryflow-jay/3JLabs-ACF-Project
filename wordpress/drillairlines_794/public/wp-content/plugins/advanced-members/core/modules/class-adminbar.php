<?php
/**
 * Manage Advanced Members for ACF Form Redirections
 *
 * @since 	1.0
 * 
 */
namespace AMem;

use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminBar extends Module {

	protected $inc = '';
	protected $name = 'amem/adminbar';


	function __construct() {
		if ( amem()->options->getmodule('_use_adminbar') ) {
			add_filter( 'show_admin_bar', [$this, 'show_admin_bar'], 88 );
		}
	}

	function show_admin_bar($bool) {
		if ( !is_user_logged_in() )
			return $bool;

		// global disable adminbar option
		// $bool = ! amem()->options->get('adminbar/global');

		// if ( amem()->options->get('adminbar/by_roles') ) {
			foreach ( amem()->user->get('roles') as $role ) {
				$role_op = amem()->options->get('adminbar/roles/'.$role, null );
				if ( $role_op ) {
					$bool = false;
					// $bool = $role_op == 'show';
				}
			}
		// }

		return $bool;
	}

}

amem()->register_module('adminbar', AdminBar::getInstance());
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

class Redirects extends Module {

	protected $inc = '';
	protected $name = 'amem/redirects';


	function __construct() {
		if ( amem()->options->getmodule('_use_redirects') ) {
			// apply after core form filters
			add_filter( 'amem/form/from_post', [$this, 'form_redirects'], 33 );
			add_filter( 'amem/form/from_local', [$this, 'form_redirects'], 33 );
			add_filter( 'amem/redirects/logout', [$this, 'logout_redirects'] );

			add_filter( "amem/form/submit/redirect/login", [$this, 'login_redirects'], 33, 2 );
		}
	}

	private function tab_to_form_type($form_type, $form) {
		if ( !empty($form['data']['tab']) ) {
			$form_type .= ( !in_array($form['data']['tab'], ['general', 'default']) ) ? '_' . $form['data']['tab'] : '';
		}
		return $form_type;
	}

	function logout_redirects($url) {
		if ( $opt_url = amem()->options->get("redirect/_after_logout", $url) ) {
			$url = $opt_url;
			$url = amem()->actions->after_submit_url( $url, ['redirect_url' => amem()->options->get("redirect/logout_redirect_url")] );
		}
		if ( amem()->options->get('redirect/apply_roles_redirection') ) {
			$roles = amem_user('roles');
			if ( $rule = $this->rule_for_roles($roles, 'logout') ) {
				$url = amem()->actions->after_submit_url($rule['after_submit'], $rule);
			}
		}

		return $url;
	}

	function login_redirects($redirect, $form) {
		// PHP cannot check logged in cookie before page reload or redirection
		// We can get loggedin user data from Login action class object
		if ( !amem()->options->get('redirect/apply_roles_redirection') || !amem()->get_action('login')->loggedin )
			return $redirect;

		$roles = amem()->get_action('login')->loggedin->roles ?? null;
		if ( !$roles )
			return $redirect;

		if ( $rule = $this->rule_for_roles($roles, 'login') ) {
			$form['data']['after_submit'] = $rule['after_submit'];
			$form['data']['redirect_url'] = $rule['redirect_url'];

			$new_redirect = amem()->actions->after_submit_url($rule['after_submit'], $rule);

			if ( $new_redirect )
				return $new_redirect;
		}

		return $redirect;
	}

	function form_redirects( $form ) {
		/* Leave code for future use */
		// if ( get_post_meta( $form_post->ID, 'redirect_override', true ) ) {
		// 	$after_submit = get_post_meta($form_post->ID, "after_$form_type", true);
		// 	$redirect_url = get_post_meta($form_post->ID, "{$form_type}_redirect_url", true);
		// } else {
		// 	$after_submit = amem()->options->get("redirect/_after_{$form_type}");
		// 	$redirect_url = amem()->options->get("redirect/{$form_type}_redirect_url");
		// }

		$form_type = $this->tab_to_form_type($form['type'], $form);

		$after_submit = amem()->options->get("redirect/_after_{$form_type}", $form['data']['after_submit']);
		$redirect_url = amem()->options->get("redirect/{$form_type}_redirect_url", $form['data']['redirect_url']);

		if ( amem()->options->get('redirect/apply_roles_redirection') ) {
			if ( $rule = $this->get_rule_for_role($form_type) ) {
				$after_submit = $rule['after_submit'];
				$redirect_url = $rule['redirect_url'];
			}
		}

		if ( $after_submit ) {
			$form['data']['after_submit'] = $after_submit;
			$redirect_url || $redirect_url = home_url();
			$form['data']['redirect_url'] = $redirect_url;
		}

		return $form;
	}

	private function get_rule_for_role($form_type) {
		switch ($form_type) {
			case 'registration':
			$roles = [ $form['data']['role'] ];
			break;
			case 'login':
			$roles = false;
			break;
			default:
			$roles = amem()->user->get('roles');
			break;
		}

		if ( !$roles )
			return $roles;

		return $this->rule_for_roles($roles, $form_type);
	}

	/**
	 * Redirect rule for givin roles
	 * $form_type should be type name with tab like account_delete
	 * 
	 * @param  array $roles
	 * @param  string $form_type
	 * @return mixed 	array or boolean
	 */
	function rule_for_roles($roles, $form_type) {
		$after_submit = false;

		foreach( (array)$roles as $role ) {
			$after_submit = amem()->options->get('redirect/roles/'.$role.'/_after_' . $form_type);
			if ( !$after_submit )
				continue;
			$redirect_url = amem()->options->get('redirect/roles/' .$role .'/'. $form_type . '_redirect_url');
			break;
		}

		return $after_submit ? compact( 'after_submit', 'redirect_url' ) : false;
	}

}

amem()->register_module('redirects', Redirects::getInstance());
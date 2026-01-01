<?php
/**
 * Form Handle Actions
 *
 * @since 1.0
 * @package Advanced Members for ACF \ Forms
 *
 */
namespace AMem\Forms;
use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class Actions extends Module {

	function __construct() {
		add_action( 'amem/form/submission', array( $this, 'handle_form' ), 10, 3 );
		add_action( 'amem/form/submit/after', array( $this, 'handle_redirect' ), 9999, 3 );

		add_action( 'amem/form/args', array( $this, 'dynamic_args' ), 10, 2 );

		add_action( 'amem/form/validate', array( $this, 'validate_user' ), 10, 3 );

		add_filter( 'amem/merge_tags/resolve', array( $this, 'resolve_merge_tag' ), 10, 2 );

		add_filter( 'amem/form/valid_form', array( $this, 'valid_form' ), 10, 1 );
		add_filter( 'amem/form/from_post', array( $this, 'form_from_post' ), 10, 2 );
		add_action( 'amem/form/to_post', array( $this, 'form_to_post' ), 10, 2 );

		add_filter( 'amem/form/acf_data', array( $this, 'acf_form_data' ), 10, 3 );

		add_filter( 'amem/form/restriction', array( $this, 'restriction_message'), 10, 3 );

		add_action( 'amem/register_actions', array( $this, 'load_actions' ), 10 );

		add_action( 'amem/form/hidden_fields', [$this, 'hidden_fields'], 10, 2 );

		add_filter( 'amem/form/submit/redirect_url', [$this, 'redirect_to_redirect_url'] );
	}

	function __get($key) {
		if ( $action = amem()->get_action($key) )
			return $action;
		return null;
	}

	function load_actions() {
		amem_include( 'core/actions/registration.php' );
		amem_include( 'core/actions/account.php' );
		amem_include( 'core/actions/login.php' );
		amem_include( 'core/actions/password-reset.php' );
	}

	/** @todo */
	function restriction_message($restriction, $form, $args) {
		$form_type = $form['type'];

		if ( in_array($form_type, ['registration', 'login', 'passwordreset']) && is_user_logged_in() ) {
			return amem()->errors->text('logged_in');
		} elseif ( $form_type == 'account' && !is_user_logged_in() ) {
			return amem()->errors->text('logged_out');
		} elseif ( $form_type == 'account' && amem()->account->get_current_tab() == 'delete' && is_super_admin() ) {
			// return __( 'Super administrators can not be deleted.', 'advanced-members' );
		}

		return $restriction;
	}

	function hidden_fields( $form, $args ) {
		$redirect = !empty($_REQUEST['redirect_to']) ? sanitize_text_field($_REQUEST['redirect_to']) : false;
		if ( !$redirect && !empty($args['redirect']) ) {
			$redirect = sanitize_text_field($args['redirect']);
		}

		if ( $redirect ) { // phpcs:disable WordPress.Security.NonceVerification
			printf( '<input type="hidden" name="redirect_to" value="%s">', esc_url( $redirect ) ); // phpcs:disable WordPress.Security.NonceVerification

			add_filter( 'amem/form/login/extra_button/url', function( $url ) use ($redirect) {
				if ( strpos( $url, 'redirect_to=' ) === false ) {
					$url = add_query_arg( 'redirect_to', $redirect, $url );
				}

				return $url;
			} );
		}
	}

	function redirect_to_redirect_url($url) {
		if ( !empty($_REQUEST['redirect_to']) ) { // phpcs:disable WordPress.Security.NonceVerification
			return sanitize_text_field($_REQUEST['redirect_to']); // phpcs:disable WordPress.Security.NonceVerification
		}
		return $url;
	}

	function dynamic_args( $args, $form ) {
		if ( !isset( $args['user'] ) ) {
			$form_type = $form['type'];
			/** @todo set user by form type */
			switch( $form_type ) {
				case 'registration':
				$args['user'] = 'new';
				break;
				case 'account':
				$args['user'] = 'current';
				break;
				default:
				break;
			}
		}

		// Add current user ID if user argument is "current"
		if ( isset( $args['user'] ) && 'current' == $args['user'] ) {
			if ( $current_user_id = get_current_user_id() ) {
				$args['user'] = $current_user_id;
			}
		}

		return $args;
	}

	/**
	 * Set form data for AMem
	 * Developers can filter more data with amem/form/acf_data hook
	 *
	 * @param  array $data
	 * @param  array $form
	 * @param  array $args
	 * @return array
	 */
	function acf_form_data( $data, $form, $args ) {
		$form_type = $form['type'];

		$data['user'] = $this->form_user($form, $args);
		$data['amem_type'] = $form_type;

		// Compat
		$data['_acf_user'] = $data['user'];
		$data['_acf_amem_type'] = $data['amem_type'];

		// For Addons
		$data = apply_filters( 'amem/form/data', $data, $form, $args );
		$data = apply_filters( 'amem/form/data/id=' . $form['id'], $data, $form, $args );

		return $data;
	}

	function form_user($form, $args=[]) {
		$form_type = $form['type'];

		if ( !empty($args['user']) ) {
			$user = $args['user'];
		} else {
			switch( $form_type ) {
				case 'registration':
				$user = 'new';
				break;
				case 'account':
				$user = get_current_user_id();
				break;
				case 'login':
				$user = 'login';
				break;
				case 'passwordreset':
				$user = 'passwordreset';
				break;
				default:
				$user = apply_filters( 'amem/form/user', 0, $form, $args );
				$user = apply_filters( 'amem/form/user/id=' . $form['id'], 0, $form, $args );
				break;
			}
		}

		return $user;
	}

	/**
	 * Handle AMem form submission
	 *
	 * @param  array $form
	 * @param  array $fields
	 * @param  array $args
	 * @return boolean
	 */
	function handle_form( $form, $fields, $args ) {
		$form_type = $form['type'];

		if ( ! ( acf_get_validation_errors() || amem_submission_failed() ) ) {
			do_action( "amem/form/submit/type=$form_type", $form, $fields, $args );
		}

		do_action( "amem/form/submit/after", $form, $fields, $args );
	}

	function redirect_url($form, $args=[]) {
		$form_type = $form['type'];

		$after_submit = $form['data']['after_submit'];
		if ( empty($after_submit) ) {
			$after_submit = 'refresh';
		}
		if ( $form_type == 'account' && wp_doing_ajax() && amem()->account->current_tab != 'delete' ) {
			$after_submit = 'success_message';
		}

		$redirect = $this->after_submit_url( $after_submit, $form );

		$redirect = apply_filters( 'amem/form/submit/redirect_url', $redirect, $form, $args );
		$redirect = apply_filters( "amem/form/submit/redirect/$form_type", $redirect, $form, $args );

		return $redirect;
	}

	function after_submit_url($after_submit, $form=[]) {
		switch ( $after_submit ) {
			case 'redirect_admin':
			$redirect = admin_url();
			break;
			case 'redirect_home':
			$redirect = home_url();
			break;
			case 'redirect_login':
			$redirect = amem_get_core_page('login');
			break;
			case 'redirect_register':
			$redirect = amem_get_core_page('register');
			break;
			case 'redirect_url':
			/* Check if given form is redirect rule */
			$redirect = !empty( $form['data']['redirect_url'] ) ? $form['data']['redirect_url'] : $form['redirect_url'];
			if ( !$redirect )
				$redirect = home_url();
			break;
			case 'success_message':
			case 'just_success_message':
			if ( wp_doing_ajax() ) {
				amem()->submissions->set_redirect($after_submit);
			}
			return $after_submit;
			break;
			case 'refresh':
			case 'reload_current':
			case 'reload':
			default:
			$redirect = amem_get_current_url();
			break;
		}

		return $redirect;
	}

	function handle_redirect( $form, $fields, $args ) {
		$form_type = $form['type'];
		$mode = ( acf_get_validation_errors() || amem_submission_failed() ) ? 'fail' : 'success';

		// AMemn currently does not support failed redirect
		if ( $mode != 'success' )
			return true;

		$redirect = $this->redirect_url( $form, $args );

		if ( in_array($redirect, ['success_message', 'just_success_message'], true) ) {
			if ( wp_doing_ajax() ) {
				amem()->submissions->set_redirect($redirect);
			}
			return;
		}

		if ( wp_doing_ajax() ) {
			amem()->submissions->set_redirect($redirect);
		} else {
			wp_safe_redirect( $redirect );
			exit;				
		}
	}

	/**
	 * Check user can be created from a form submission.
	 *
	 * @since 1.0
	 *
	 */
	function validate_user( $form, $args, $fields ) {
		$type = $form['type'];
		if ( $type == 'registration' && is_user_logged_in() ) {
			$allkeys = array_filter( array_values($form['amem']) );
			if ( $allkeys ) {
				$tmp_key = array_shift($allkeys);
				amem_add_error( $tmp_key, amem()->errors->text('logged_in') );
			} else {
				amem_add_submission_error( amem()->errors->text('logged_in') );
			}
		}
	}

	/**
	 * Add the editing fields to the default valid form
	 *
	 * @since 1.0
	 *
	 */
	function valid_form( $form ) {

		$form['amem'] = false;

		return $form;

	}


	/**
	 * Set predefined field values
	 *
	 * @since 1.0
	 *
	 */
	function form_from_post( $form, $post ) {
		$amem_fields = apply_filters( 'amem/form/from_post/amem', array_values( amem()->fields->predefined_fields() ), $form, $post );

		$form['amem'] = array_fill_keys( $amem_fields, false );

		$form['record'] = [];

		$fields = amem_get_form_fields($form['key']);
		if ( $fields ) {
			foreach($fields as $field) {
				if ( isset($form['amem'][$field['type']]) ) {
					$form['amem'][$field['type']] = $field['key'];
				}
			}
		}

		return $form;
	}

	function form_to_post( $form, $post ) {
		// $editing_type = 'user';

		// update_field( 'field_form_editing_user_role', $form['amem']['role'], $post->ID );
		// update_field( 'field_form_editing_user_email', $form['amem']['email'], $post->ID );
		// update_field( 'field_form_editing_user_username', $form['amem']['username'], $post->ID );
		// update_field( 'field_form_editing_user_first_name', $form['amem']['first_name'], $post->ID );
		// update_field( 'field_form_editing_user_last_name', $form['amem']['last_name'], $post->ID );
		// update_field( 'field_form_editing_user_password', $form['amem']['password'], $post->ID );
		// update_field( 'field_form_editing_user_send_notification', $form['amem']['send_notification'], $post->ID );

		// if ( isset( $form['amem']['custom_fields'] ) ) {
		// 	update_field( 'field_form_editing_custom_fields', $form['amem']['custom_fields'], $post->ID );
		// }
		return $form;
	}

}

amem()->register_module('actions', Actions::getInstance());
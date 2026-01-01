<?php
/**
 * Handle Registration Form
 *
 * @since 1.0
 * @package Advanced Members for ACF \ Forms
 * 
 */
namespace AMem\Action;
use AMem\Action;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class Registration extends Action {

	public $priority = 9;

	function __construct() {
		parent::__construct();

		add_action( 'amem/form/validate/type=registration', array( $this, 'validate'), 10, 3 );
		add_action( 'amem/form/submit/type=registration', array( $this, 'handle_form'), 10, 3 );

		add_action( 'amem/user/registration', array( $this, 'process'), 10, 3 );

		add_filter( 'amem/form/from_post/type=registration', [$this, 'from_post'], 10, 2 );

		add_filter( 'amem/error/messages', [$this, 'messages'] );
		// add_filter( 'amem/form/error_message', [$this, 'error_message'] );// Applied to restriction message and not used
	}

	function messages($messages) {
		$messages['awaiting_email_confirmation'] = __( 'Your account is awaiting e-mail verification. Please check your inbox', 'advanced-members' );

		return $messages;
	}

	function error_message($message) {
		// Do not append to existing
		if ( '' == $message && is_user_logged_in() ) {
			$message = amem()->errors->text( 'logged_in' );
		}

		return $message;
	}

	function from_post( $form, $post ) {
		$role = get_post_meta( $post->ID, 'regist_role', true );
		if ( !$role )
			$role = get_option( 'default_role' );

		$form['data']['role'] = $role;

		$status = get_post_meta( $post->ID, 'regist_status', true );
		if ( !$status )
			$status = 'approve';// approve || mailcheck

		$form['data']['account_status'] = $status;

		if ( $form['data']['after_submit'] == 'success_message' ) {
			$form['data']['after_submit'] = 'just_success_message';
		}

		$force_show_message = get_post_meta( $post->ID, 'regist_force_show_message', true );

		$form['data']['force_show_message'] = (bool) $force_show_message;

		return $form;
	}

	function validate( $form, $args, $fields ) {
		if ( is_user_logged_in() ) {
			amem_add_error( 'field_amem_errors', amem()->errors->text('logged_in') );
			return false;
		}

		$user_id = isset($args['user']) ? $args['user'] : null;

		if ( 'new' != $user_id && !$user_id ) {
			amem_add_error( 'field_amem_errors', amem()->errors->text('invalid_form') );
			return false;
		}
	}

	function handle_form( $form, $fields, $args ) {
		if ( $this->is_valid() ) {
			do_action( 'amem/user/registration', $form, $fields, $args );
		}

		do_action( 'amem/user/registration/after', $form, $fields, $args );
	}

	function process( $form, $fields, $args ) {
		$user_id = isset($args['user']) ? $args['user'] : null;
		$form_type = $form['type'];

		$user_data = array();

		$predefined_fields = amem()->fields->predefined_fields();

		$processed = [];

		foreach ( $fields as $i => $field ) {
			if ( $key = array_search($field['type'], $predefined_fields, true) ) {
				$user_data[$key] = $field['value'];
				// user_login cannot be modified
				if ( $form_type != 'registration' && $field['type'] == 'username' ) {
					unset($user_data[$key]);
				}
				if ( $field['type'] == $field['name'] || $field['type'] == 'user_password' || $field['type'] == 'user_password_confirm' || $field['type'] == 'username' ) {
					$processed[] = $field['key'];
				}
				if ( $field['type'] == 'user_email' && !empty($field['set_as_username']) ) {
					$user_data['user_login'] = $field['value'];
				}
			}
		}

		if ( isset($user_data['user_email']) && (!isset($user_data['username']) && !isset($user_data['user_login'])) ) {
			$user_data['user_login'] = $user_data['user_email'];
		}

		if ( empty($user_data['user_pass']) ) {
			$user_data['user_pass'] = wp_generate_password();
		}

		if ( empty($user_data['role']) && !empty($form['data']['role']) ) {
			$user_data['role'] = $form['data']['role'];
		}
	
		$user_data = wp_parse_args( $user_data, array(
			'role' => get_option('default_role'),
			'user_pass' => wp_generate_password(),
		));

		// Filter user data before insert/update
		$user_data = apply_filters( 'amem/form/user_data', $user_data, $form, $args );

		$inserted_id = wp_insert_user( $user_data );

		$error = __('Failed to process registration with the given data.', 'advanced-members' );
		if ( ! $inserted_id || is_wp_error( $inserted_id ) ) {
			if ( is_wp_error($inserted_id) ) {
				$error = $inserted_id->get_error_message();
			}
			amem()->errors->add( 'user_insert_error', $error );
			return false;
		}

		$user = get_user_by( 'id', $inserted_id );
		
		if ( ! $user || is_null( $user ) ) {
			amem()->errors->add( 'user_insert_error', $error );
			return false;
		}

		amem_save_all_fields( 'user_' . $user->ID, $fields );
		$encoded_fields = amem_encrypt( $fields );
		update_user_meta( $user->ID, '_amem_registration_submit', $encoded_fields );

		// if ( 'new' == $user_id ) {
		// 	wp_new_user_notification( $user->ID, null, 'both' );
		// }

		// Save user ID to submission object
		amem()->submission['user'] = $user->ID;
		$status = $form['data']['account_status'];

		/** @todo more status actions on Addons or core */
		switch( $status ) {
			case 'approve':
			amem()->user->approve( $user->ID );
			break;
			case 'mailcheck':
			amem()->user->email_pending( $user->ID );
			add_filter( 'amem/form/submit/redirect/registration', function($url, $form) {
				if ( $form['data']['force_show_message'] ) {
					// $url = amem_get_current_url();
					return 'success_message';
				}
				if ( '' == $url )
					return $url;
				$url = remove_query_arg( ['updated', 'errc', 'key', 'act', 'login'], $url );
				$url = add_query_arg( 'updated', 'awaiting_email_confirmation', $url );
				return $url;
			}, 50, 2 );
			break;
			default:
			break;
		}

		do_action( 'amem/user/registration/status_set/' . $status, $user->ID );
		
		do_action( 'amem/user/created', $user, $form, $args );

		// if ( $status == 'approve' ) {
		// 	amem()->actions->login->auto_login( amem_encrypt($user->ID) );
		// }

		return true;
	}

	function get_name() {
		return 'registration';
	}

	function get_label() {
		return __( 'Registration', 'advanced-members' );
	}

}

amem()->register_action('registration', Registration::getInstance());
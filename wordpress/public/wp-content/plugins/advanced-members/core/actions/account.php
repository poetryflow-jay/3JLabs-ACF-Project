<?php
/**
 * Handle Account Form
 *
 * @since 1.0
 * @package Advanced Members for ACF \ Forms
 * 
 */
namespace AMem\Action;
use AMem\Action;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class Account extends Action {

	public $priority = 9;

	public $current_tab;

	function __construct() {
		add_action( 'amem/form/validate/type=account', array( $this, 'validate'), 10, 3 );
		add_action( 'amem/form/validate/type=account', array( $this, 'check_fields'), 10, 3 );
		add_action( 'amem/form/submit/type=account', array( $this, 'handle_form'), 10, 3 );

		add_action( 'amem/account/content/general', [$this, 'content_general'], 10, 2 );
		add_action( 'amem/account/content/delete', [$this, 'content_delete'], 10, 2 );
		add_action( 'amem/account/content/password', [$this, 'content_password'], 10, 2 );
		add_action( 'amem/account/content/logged_out', [$this, 'content_logged_out'], 10, 2 );

		// add_action( 'amem/form/local_fields/type=account', [$this, 'local_fields'], 10, 2 );

		add_action( 'amem/account/update', array( $this, 'process'), 10, 3 );

		add_action( 'amem/form/hidden_fields/type=account', [$this, 'hidden_fields'], 10, 2 );

		add_action( 'amem/form/create_submission/before/type=account', [$this, 'register_local_fields'], 10, 2 );

		add_filter( 'amem/form/from_local/type=account', [$this, 'from_local'] );
		add_filter( 'amem/form/from_post/type=account', [$this, 'form_success_message'] );

		add_filter( 'amem/error/messages', [$this, 'messages'] );
		// add_filter( 'amem/form/error_message', [$this, 'error_message'] );

		$this->current_tab =& amem()->account->current_tab;
	}

	function validate( $form, $args, $fields ) {
		if ( !is_user_logged_in() ) {
			amem_add_error( 'field_amem_errors', amem()->errors->text('logged_out') );
		}
	}

	function from_local($form) {
		$form['amem'] = array(
			'username' => 'user_login',
			'user_email' => 'user_email',
			'user_password' => 'user_password',
			'user_password_current' => 'user_password_current',
		);

		$form['record'] = [];

		$form = $this->form_success_message($form);
		return $form;
	}

	function form_success_message($form) {
		switch( $this->current_tab ) {
			case 'general':
			$form['data']['success_message'] = __( 'Account updated', 'advanced-members' );
			break;
			case 'password':
			$form['data']['success_message'] = __( 'Password updated', 'advanced-members' );
			break;
			// case 'delete':
			// $form['data']['success_message'] = __( 'Account deleted', 'advanced-members' );
			// break;
			default:
			break;
		}
		return $form;
	}

	function messages($messages) {
		$array = [
			'account_general' => __( 'Account updated', 'advanced-members' ),
			'account_password' => __( 'Password updated', 'advanced-members' ),
			'account_delete' => __( 'Account deleted', 'advanced-members' ),
		];

		$messages = array_merge( $messages, $array );

		return $messages;
	}

	function error_message($message) {
		// Do not append to existing
		if ( '' == $message && !is_user_logged_in() ) {
			/* translators: Login page URL */
			$message = sprintf( __( 'Please <a href="%s" title="Login">login</a> to manage your account', 'advanced-members' ), amem_get_core_page('login') );
		}

		return $message;
	}

	function handle_form( $form, $fields, $args ) {
		if ( $this->is_valid() ) {
			do_action( 'amem/account/update', $form, $fields, $args );
		}

		do_action( 'amem/account/update/after', $form, $fields, $args );
	}

	/**
	 * Check login fields
	 * 
	 */
	function check_fields( $form, $args, $fields ) {
		$data = $form['record'];
		$amem = $form['amem'];

		$no_passwd = ! isset($data['user_password']);
		$user_password = $no_passwd ? '' : $data['user_password'];
		if ( $no_passwd ) {
			$amem['user_password'] = 'field_amem_errors';
		}

		if ( isset( $data['username'] ) && $data['username'] != '' ) {
			amem_add_error( $amem['username'], __( 'Username cannot be changed.', 'advanced-members' ) );
		}

		if ( isset( $data['user_login'] ) && $data['user_login'] != '' ) {
			amem_add_error( $amem['user_login'], __( 'User login cannot be changed.', 'advanced-members' ) );
		}

		if ( isset( $data['user_email'] ) && $data['user_email'] == '' ) {
			amem_add_error( $amem['user_email'], __( 'Please enter your email.', 'advanced-members' ) );
		}

		if ( isset( $data['username'] ) ) {
			unset($data['username']);
		} /*elseif ( isset( $data['user_email'] ) ) {
			$exists = email_exists($data['user_email']);
			if ( $exists && $exists != $args['user'] )
				amem_add_error( $amem['user_email'], __( 'Email already exists.', 'advanced-members' ) );
		}*/

		// Validation already processed from field
		// // Verify current user password to update account
		// if ( isset($data['user_password_current']) ) {
		//  $user = get_user_by( 'ID', $args['user'] );
		//  if ( wp_check_password( $data['user_password_current'], $user->data->user_pass, $user->ID ) ) {
		//    amem_add_error( $amem['user_password_current'], __( 'Current password is incorrect. Please try again.', 'advanced-members' ) );
		//  }
		// }

		if ( $this->current_tab == 'delete' && ( current_user_can('delete_users') || is_super_admin() ) ) {
			amem_add_error( 'field_amem_errors', __( 'Administrator users cannot be deleted on this page.', 'advanced-members' ) );
		}
	}

	function process($form, $fields, $args) {
		$user_id = isset($args['user']) ? $args['user'] : null;
		$form_type = $form['type'];

		if ( $this->current_tab == 'delete' ) {
			return $this->process_delete( $form, $fields, $args );
		}

		$user_data = array();

		$predefined_fields = amem()->fields->predefined_fields();
		$processed = [];

		foreach ( $fields as $i => $field ) {
			if ( $key = array_search($field['type'], $predefined_fields, true) ) {
				$user_data[$key] = $field['value'];
				// user_login cannot be modified
				if ( $field['type'] == 'username' ) {
					unset($user_data[$key]);
					unset($fields[$i]);
				}
				if ( $field['type'] == $field['name'] || $field['type'] == 'user_password' || $field['type'] == 'user_password_confirm' || $field['type'] == 'username' ) {
					$processed[] = $field['key'];
					unset($fields[$i]);
				}
				// if ( $field['type'] == 'user_email' && !empty($field['set_as_username']) ) {
				//  $user_data['user_login'] = $field['value'];
				// }
			}
		}
		
		// Double check user_login is not set
		if ( isset($user_data['user_login']) ) {
			unset($user_data['user_login']);
		}

		$user_data['ID'] = $user_id;

		// Filter user data before insert/update
		$user_data = apply_filters( 'amem/form/user_data', $user_data, $form, $args );

		$updated_user_id = wp_update_user( $user_data );

		$error = __('Failed to process account update with the given data.', 'advanced-members' );
		if ( ! $updated_user_id || is_wp_error( $updated_user_id ) ) {
			if ( is_wp_error($updated_user_id) ) {
				$error = $updated_user_id->get_error_message();
			}
			amem()->errors->add( 'user_update_error', $error );
			return false;
		}

		$user = get_user_by( 'id', $updated_user_id );
		
		if ( ! $user || is_null( $user ) ) {
			amem()->errors->add( 'user_update_error', $error );
			return false;
		}

		amem_save_all_fields( 'user_' . $user->ID, $fields );

		// Save user ID to submission object
		amem()->submission['user'] = $user->ID;
		
		do_action( 'amem/user/updated', $user, $form, $args );

		add_filter( 'amem/form/submit/redirect/account', [$this, 'updated_redirect'], 20, 3 );// after redirec_to detection

		return true;
	}

	function process_delete( $form, $fields, $args ) {
		$user_id = isset($args['user']) ? (int) $args['user'] : null;

		if ( ( user_can($user_id, 'delete_users') || is_super_admin($user_id) ) ) {
			amem_add_error( 'field_amem_errors', __( 'Administrator users cannot be deleted on this page.', 'advanced-members' ) );
			return;
		}

		do_action( 'amem/user/delete/before', $user_id, $form, $args );

		if ( !amem()->is_dev() )
		amem()->user->delete($user_id);

		do_action( 'amem/user/delete/after', $user_id, $form, $args );

		wp_destroy_current_session();
		wp_logout();
		session_unset();

		add_filter( 'amem/form/submit/redirect/account', [$this, 'updated_redirect'], 20, 3 );// after redirec_to detection
	}

	function hidden_fields($form, $args) {
		// Set current tab
		printf( '<input type="hidden" name="_amem_account_tab", value="%s">', $this->current_tab );
	}

	function updated_redirect($url, $form, $args) {
		if ( !$this->current_tab )
			return $url;

		$updated_code = "account_" . $this->current_tab;

		if ( !$url || in_array($form['data']['after_submit'], ['success_message', 'just_success_message'], true) )
			$url = amem_get_core_page( 'login' );

		$url = remove_query_arg( ['updadted', 'errc'], $url );		
		// if ( $this->current_tab == 'delete' ) {
		// 	$url = amem_get_core_page('register');
		// 	$user_id = isset($args['user']) ? $args['user'] : null;
		// 	$url = apply_filters( 'amem/user/deleted/redirect', $url, $user_id );
		// } else {
		// 	$url = remove_query_arg( ['updadted', 'errc'], $url );			
		// }

		// wp_safe_redirect( add_query_arg( 'updated', $updated_code, $url ) );
		// exit;
		$url = add_query_arg( 'updated', $updated_code, $url );

		return $url;
	}

	function register_local_fields($form, $args) {
		if ( $this->current_tab ) {
			if ( is_callable( [$this, 'local_fields_' . $this->current_tab] ) )
				call_user_func( [$this, 'local_fields_' . $this->current_tab] );
		} else {
			$this->local_fields_general($args);
			$this->local_fields_password($args);
			$this->local_fields_delete($args);
		}
	}

	function local_fields_general($args='') {
		$fields = array(
			'user_login' => array(
				'key' => 'user_login',
				'label' => __( 'Username', 'advanced-members' ),
				'name' => 'user_login',
				'type' => 'username',
				// 'readonly' => true,
				'disabled' => true,
				'required' => true,
				// 'custom_username' => true,
				// 'value' => amem()->user->get('user_login'),
				'_amem_local' => true,
			),
			'user_email' => array(
				'key' => 'user_email',
				'label' => __( 'User Email', 'advanced-members' ),
				'name' => 'user_email',
				'type' => 'user_email',
				'required' => true,
				// 'custom_email' => true,
				// 'value' => amem()->user->get('user_email'),
				'_amem_local' => true,
			),
			// 'first_name' => array(
			// 	'key' => 'first_name',
			// 	'label' => __( 'First Name', 'advanced-members' ),
			// 	'name' => 'first_name',
			// 	'type' => 'first_name',
			// 	// 'value' => amem()->user->get('first_name'),
			// 	'_amem_local' => true,
			// ),
			// 'last_name' => array(
			// 	'key' => 'last_name',
			// 	'label' => __( 'Last Name', 'advanced-members' ),
			// 	'name' => 'last_name',
			// 	'type' => 'last_name',
			// 	// 'value' => amem()->user->get('last_name'),
			// 	'_amem_local' => true,
			// ),
		);

		// 24/05/23: Remove all default fields
		$fields = [];

		$fields = apply_filters( 'amem/account/local_fields/general', $fields, $args );

		if ( $this->require_password_validation( 'general' ) ) {
			$fields['user_password_current'] = [
				'key' => 'user_password_current',
				'label' => __( 'Current Password', 'advanced-members' ),
				'name' => 'user_password_current',
				'type' => 'user_password_current',
				'placeholder' => __( 'Your Current Password', 'advanced-members' ),
				'required' => true,
				'_amem_local' => true,
			];
		}

		if ( empty($fields) )
			return;

		acf_add_local_field_group( array(
			'key' => 'group_amem_account_general',
			'title' => '',
			'fields' => $fields,
			'_amem_positon' => 'bottom',
		) );
	}

	function local_fields_password($args='') {
		$fields = [];

		$fields['user_password'] = [
			'key' => '_amem_local_user_password',
			'label' => __( 'New Password', 'advanced-members' ),
			'name' => 'user_password',
			'type' => 'user_password',
			'placeholder' => __( 'Your Password', 'advanced-members' ),
			'show_pass_confirm' => true,
			'required' => true,
			'_amem_local' => true,
		];

		if ( $this->require_password_validation( 'password' ) ) {
			$fields['user_password_current'] = [
				'key' => '_amem_local_user_password_current',
				'label' => __( 'Current Password', 'advanced-members' ),
				'name' => 'user_password_current',
				'type' => 'user_password_current',
				'placeholder' => __( 'Your Current Password', 'advanced-members' ),
				'required' => true,
				'_amem_local' => true,
			];
		}

		$fields = apply_filters( 'amem/account/local_fields/password', $fields, $args );

		if ( empty($fields) )
			return;

		acf_add_local_field_group( array(
			'key' => 'group_amem_account_password',
			'title' => '',
			'fields' => $fields,
			'_amem_positon' => 'top',
		) );
	}

	function local_fields_delete($args='') {
		$fields = [];

		$alert_text = amem()->options->get('account/account_delete_text');
		if ( !$alert_text ) {
			/* translators: Delete account explain message */
			$alert_text = __( 'By deleting your account, all of its data will be destroyed. This is not recoverable. %s', 'advanced-members' );
			$explain_process = __( 'To delete your account, click on the button below.', 'advanced-members' );
			$password_required = $this->require_password_validation( 'delete' );
			if ( $password_required ) {
				$explain_process = __( 'To delete your account enter your password below.', 'advanced-members' );
			}
			$alert_text = trim( sprintf( $alert_text, $explain_process ) );
		}

		$confirm_text = amem()->options->get('account/delete_account_label');
		if ( !$confirm_text ) {
	 		// $confirm_text = sprintf( __( 'I acknowledge that the account and data on &quot;%s&quot; will be deleted.', 'advanced-members' ), get_bloginfo('name') );
	 		$confirm_text = __( 'Account Delete Confirmation', 'advanced-members' );
		}

		/** @todo Provide filter hook or not */
		// $alert_text = apply_filters( 'amem/user/delete/instruction', $alert_text, $password_required );
		// $confirm_text = apply_filters( 'amem/user/delete/label', $confirm_text, $password_required );

		$fields['user_delete_confirm'] = [
		 'key' => 'user_delete_confirm',
		 'label' => $confirm_text,//__( 'Account Delete Confirmation', 'advanced-members' ),
		 'name' => 'user_delete_confirm',
		 'instructions' => $alert_text,
		 // 'message' => $confirm_text,
		 'type' => 'true_false',
		 'required' => true,
		 '_amem_local' => true,
		 'ui' => 1,
			'wrapper' => ['class' => 'with-instructions'],
		];

		if ( $this->require_password_validation( 'delete' ) ) {
			$fields['user_password_current'] = [
				'key' => 'user_password_current',
				'label' => __( 'Current Password', 'advanced-members' ),
				'name' => 'user_password_current',
				'type' => 'user_password_current',
				'required' => true,
				'_amem_local' => true,
			];
		}

		$fields = apply_filters( 'amem/account/local_fields/delete', $fields, $args );

		if ( empty($fields) )
			return;

		acf_add_local_field_group( array(
			'key' => 'group_amem_account_delete',
			'title' => '',
			'fields' => $fields,
			'_amem_positon' => 'top',
		) );
	}

	function local_fields($form, $args) {
		$field_group = acf_get_local_field_group('group_amem_account_' . $this->current_tab);

		if ( $field_group )
			amem()->render->render_field_group( $field_group, $form, $args );
	}

	function remove_local_fields_action() {
		remove_action( 'amem/form/after_fields/type=account', [$this, 'local_fields'], 50 );
		remove_action( 'amem/form/local_fields/type=account', [$this, 'local_fields'], 50 );
	}

	function content_general($args, $data) {
		if ( !acf_is_local_field_group('group_amem_account_general') )
			$this->local_fields_general();

		add_action( 'amem/form/after_fields/type=account', [$this, 'local_fields'], 50, 2 );
		add_action( 'amem/form/after_field_wrapper', [$this, 'remove_local_fields_action'] );

		if ( !amem()->account->get_form_id() && !acf_get_local_field_group('group_amem_account_general') ) {
			sprintf( '<div class="acf-notice -error amem-notice amem-error-message" aria-live="assertive" role="error">%s</div>', esc_html__( 'Default account form not set in settings.', 'advanced-members' ) );
		} else {
			amem()->render->render( amem()->account->get_form_id(), $args );			
		}

	}

	function content_password($args, $data) {
		if ( !acf_is_local_field_group('group_amem_account_password') )
			$this->local_fields_password();

		add_action( 'amem/form/local_fields/type=account', [$this, 'local_fields'], 10, 2 );
		add_action( 'amem/form/after_field_wrapper', [$this, 'remove_local_fields_action'] );

		amem()->render->render( amem()->account->get_form_id(), $args );
	}

	function content_delete($args, $data) {
		if ( !acf_is_local_field_group('group_amem_account_delete') )
			$this->local_fields_delete($args);

		add_action( 'amem/form/local_fields/type=account', [$this, 'local_fields'], 10, 2 );
		add_action( 'amem/form/after_field_wrapper', [$this, 'remove_local_fields_action'] );

		amem()->render->render( amem()->account->get_form_id(), $args );
	}

	function content_logged_out($args, $data) {
		// Always use local form key to avoid render account fields
		amem()->render->render( amem()->account->form_key, $args );
	}

	/**
	 * Checks account actions require current password.
	 */
	function require_password_validation( $tab_key ) {
		$is_required = true;

		switch ( $tab_key ) {
			case 'general':
			case 'password':
			$is_required = amem()->options->get( 'account/show_current_passwd' );
			break;
			case 'delete':
			$is_required = true;
			break;
			default:
			break;
		}

		return apply_filters( "amem/account/{$tab_key}/require_password", $is_required );
	}

	function get_name() {
		return 'account';
	}

	function get_label() {
		return __( 'Account', 'advanced-members' );
	}

}

amem()->register_action('account', Account::getInstance());
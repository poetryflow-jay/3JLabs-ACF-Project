<?php
/**
 * Advanced Members for ACF Password Reset
 *
 * Only works on core 'Password Reset' page
 *
 * @since 	1.0
 * 
 */
namespace AMem;

use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PasswordReset extends Module {

	protected $inc = '';
	protected $name = 'amem/passwordreset';

	protected $mode;

	protected $user;

	public $form_key = 'form_amem_passwordreset';

	public $cookie = 'wp-resetpass-' . COOKIEHASH;

	function __construct() {
		add_shortcode( 'advanced-members-pwreset', [$this, 'shortcode'] );

		add_action( 'amem/form/create_submission/pre', [$this, 'register_form'] );

		add_action( 'template_redirect', [$this, 'detect_mode'] );

		add_filter( 'amem/error/messages', [$this, 'messages'] );
	}

	function messages($messages) {
		$errors = [
			'pwreset_checkemail' => __( 'If an account matching the provided details exists, we will send a password reset link. Please check your inbox.', 'advanced-members' ),
			'pwreset_password_changed' => __( 'You have successfully changed your password.', 'advanced-members' ),
			'pwreset_invalidkey' => __( 'Your password reset link appears to be invalid. Please request a new link below.', 'advanced-members' ),
			'pwreset_expiredkey' => __( 'Your password reset link has expired. Please request a new link below.', 'advanced-members' ),
			'pwreset_unkownerror' => __( 'An unknown error occurred while resetting your password. Please request a new link below.', 'advanced-members' ),
		];

		return array_merge( $messages, $errors );
	}

	function shortcode($atts='') {
		$this->register_form();

		$updated = isset($_GET['updated']) ? sanitize_key($_GET['updated']) : false; // phpcs:disable WordPress.Security.NonceVerification
		$message = false;
		if ( !$updated ) {
			// $message = __( 'To reset your password, please enter your email address or username below.', 'advanced-members' );
		}
		
		ob_start(); ?>

		<?php if ( $message ) {
			printf( '<div class="amem-message amem-info" aria-live="assertive" role="info">%s</div>', $message );
		}
		if ( in_array($updated, ['pwreset_checkemail', 'pwreset_password_changed'], true) ) {
			// manually print message
			amem_form_updated_message( amem()->local->get_form( $this->form_key ), $atts, true );
		} else {
			amem()->render->render($this->form_key, $atts);
		}

		$output = ob_get_clean();

		return $output;
	}

	function get_mode() {
		if ( isset($this->mode) )
			return $this->mode;

		if ( $this->is_reset_request() ) {
			$this->mode = 'reset';
		} elseif ( $this->is_change_request() ) {
			$this->mode = 'change';
		} elseif ( amem_is_core_page('password-reset') || (!empty($_POST['_amem_password_reset']) && !empty($_POST['amem_form'])) ) { // phpcs:disable WordPress.Security.NonceVerification
			/** @todo Maybe safely removed later */
			$this->mode = 'ready';	
		} else {
			$this->mode = false;
		}

		return $this->mode;
	}

	function detect_mode() {
		// validate $this->cookie and hash via check_password_reset_key
		if ( amem_is_core_page( 'password-reset' ) && isset( $_REQUEST['act'] ) && 'reset_password' === sanitize_key( $_REQUEST['act'] ) ) { // phpcs:disable WordPress.Security.NonceVerification
			wp_fix_server_vars();

			if ( isset( $_GET['hash'] ) && isset( $_GET['login'] ) ) { // phpcs:disable WordPress.Security.NonceVerification
				$value = sprintf( '%s:%s', sanitize_text_field( $_GET['login'] ), sanitize_text_field( $_GET['hash'] ) ); // phpcs:disable WordPress.Security.NonceVerification
				$this->setcookie( $this->cookie, $value );
				wp_safe_redirect( remove_query_arg( array( 'hash', 'login', 'action' ), amem_get_current_url() ) );
				exit;
			}

			$user = $this->get_requested_user();

			if ( ! $user || is_wp_error( $user ) ) {
				$this->setcookie( $this->cookie, false );
				if ( $user && 'expired_key' === $user->get_error_code() ) {
					wp_redirect( add_query_arg( array( 'updated' => 'pwreset_expiredkey' ), amem_get_core_page( 'password-reset' ) ) );
				} else {
					wp_redirect( add_query_arg( array( 'updated' => 'pwreset_invalidkey' ), amem_get_core_page( 'password-reset' ) ) );
				}
				exit;
			}

			$this->mode = 'change';
		}
	}

	function get_requested_user() {
		if ( isset($this->user) )
			return $this->user;

		if ( isset( $_COOKIE[ $this->cookie ] ) && 0 < strpos( $_COOKIE[ $this->cookie ], ':' ) ) {
			list( $rp_login, $rp_key ) = explode( ':', sanitize_text_field( $_COOKIE[ $this->cookie ] ), 2 );

			$user = check_password_reset_key( $rp_key, $rp_login );

			if ( isset( $_POST['user_password'] ) && ! hash_equals( $rp_key, sanitize_key($_POST['rp_key']) ) ) { // phpcs:disable WordPress.Security.NonceVerification
				$user = false;
			}
		} else {
			$user = false;
		}

		if ( $user && !is_wp_error( $user ) ) {
			$this->user = $user;
		}
		return $user;
	}

	function setcookie( $name, $value = '', $expire = 0, $path = '' ) {
		if ( empty( $value ) ) {
			$expire = time() - YEAR_IN_SECONDS;
		}
		if ( empty( $path ) ) {
			list( $path ) = explode( '?', sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ); // phpcs:disable WordPress.Security.NonceVerification
		}

		$levels = ob_get_level();
		for ( $i = 0; $i < $levels; $i++ ) {
			@ob_end_clean();
		}

		$name = is_null($name) ? $this->cookie : $name;
		nocache_headers();
		setcookie( $name, $value, $expire, $path, COOKIE_DOMAIN, is_ssl(), true );
	}


	function register_form( $form_key = null ) {
		static $registered;

		if ( isset($registered) )
			return;

		if ( !$this->get_mode() && $form_key != $this->form_key )
			return;

		$form = array(
			'key' => $this->form_key,
			'post_id' => false,
			'title' => '',//__( 'Password Reset', 'advanced-members' ),
			'type' => 'passwordreset',
			'data' => array(
				'description' => '',
				'success_message' => '',
				'after_submit' => '',
				'redirect_url' => '',
			),
		);

		amem()->local->add_form($form);

		switch( $this->mode ) {
			case 'ready':
			case 'reset':
			$fields = array(
				'username' => array(
					'key' => 'username',
					'label' => __( 'Username or User Email', 'advanced-members' ),
					'name' => 'username',
					'type' => 'username',
					'required' => true,
					'_amem_local' => true,
				),
				// '_amem_password_reset' => array(
				// 	'key' => '_amem_password_reset',
				// 	'label' => '',
				// 	'name' => '_amem_password_reset',
				// 	'type' => 'hidden',
				// 	'value' => 1,
				// 	'default_value' => 1,
				// 	'_amem_local' => true,
				// ),
			);
			break;
			case 'change':
			$fields = array(
				'user_password' => array(
					'key' => 'user_password',
					'label' => __( 'New Password', 'advanced-members' ),
					'name' => 'user_password',
					'type' => 'user_password',
					'placehoder' => __( 'Your Password', 'advanced-members' ),
					'confirm_placeholder' => __( 'Password Confirm', 'advanced-members' ),
					'required' => true,
					'show_pass_confirm' => true,
					'_amem_local' => true,
				),
			);
			break;
			default:
			$fields = [];
			break;
		}

		amem()->local->add_group( array(
			'key' => 'group_amem_passwordreset',
			'title' => '',
			'_amem_form' => $this->form_key,
			'fields' => $fields
		) );

		$registered = true;
	}

	function is_reset_request() {
		$check = true;
		if ( did_action('wp') )
			$check = amem_is_core_page('password-reset');
		return isset( $_POST['amem_form'] ) && isset($_POST['_amem_password_reset']) && $check; // phpcs:disable WordPress.Security.NonceVerification
	}

	function is_change_request() {
		$check = true;
		if ( did_action('wp') )
			$check = amem_is_core_page('password-reset');
		return isset( $_POST['amem_form'] ) && isset($_POST['_amem_password_change']) && $check; // phpcs:disable WordPress.Security.NonceVerification
	}

	function add_placeholder( $placeholders ) {
		$placeholders[] = '{password_reset_link}';
		$placeholders[] = '{password}';
		return $placeholders;
	}

	function add_replace_placeholder( $replace_placeholders ) {
		$replace_placeholders[] = amem_user( 'password_reset_link' );
		$replace_placeholders[] = esc_html__( 'Your set password', 'advanced-members' );
		return $replace_placeholders;
	}

}

amem()->register_module('passwordreset', PasswordReset::getInstance());
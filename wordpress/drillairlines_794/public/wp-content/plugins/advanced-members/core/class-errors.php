<?php
/**
 * Manage Advanced Members for ACF Location Rules
 *
 * @since 	1.0
 *
 */
namespace AMem;

use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Errors extends Module {

	// Translated error strings
	public $messages = [];

	protected $errors = [];

	protected $form_errors = [];

	function __construct() {
		$this->messages = [
			'logged_in' => __( 'You are already logged in.', 'advanced-members' ),
			/* translators: Login URL */
			'logged_out' => sprintf( __( 'You have been logged out of the site. You can <a href="%s">log in here</a>.', 'advanced-members' ), esc_url( amem_get_core_page('login', false, 'current') ) ),
			'invalid_form' => __( 'Invalid form detected.', 'advanced-members' ),
			'empty_username_email' => __( 'Please enter your username or email', 'advanced-members' ),
			'empty_password' => __( 'Please enter your password', 'advanced-members' ),
			'empty_email' => __( 'Please enter your email', 'advanced-members' ),
			/* translators: Given username */
			'username_not_exists' => __( 'The username %s does not exist on this site. Please try a different username', 'advanced-members' ),
			'password_incorrect' => __( 'Password is incorrect. Please try again.', 'advanced-members' ),
			/* translators: Current URL */
			'nonce_failed' => sprintf( __( 'Your submission failed. Please <a href="%s">reload the page</a> and try again.', 'advanced-members' ), amem_get_current_url() ),
			'invalid_honeypot' => __( 'Spam Detected', 'advanced-members' ),
			'rejected'	=> __( 'rejected user', 'advanced-members' ),
			'form_disabled' => __( 'Form is disabled', 'advanced-members' ),
		];

		add_action( 'init', array( $this, 'messages' ), 50 );
	}

	function messages() {
		$this->messages = apply_filters('amem/error/messages', $this->messages);

		return $this->messages;
	}

	function add_text($key, $text) {
		if ( !isset($this->messages[$key]) )
			$this->messages[$key] = $text;
	}

	function text($key) {
		if ( isset($this->messages[$key]) )
			return $this->messages[$key];

		return '';
	}

	function add($input='', $message='') {
		if ( !$message && isset($this->messages[$input]) )
			$message = $this->messages[$input];

		$this->errors[$input] = $message;
	}

	function form_error($code, $message='') {
		$code = sanitize_key( $code );
		if ( !$message && !isset($this->messages[$code]) )
			return;

		if ( $message )
			$this->add_text($code, $message);

		$this->form_errors[] = $code;
	}

	function form_error_message( $form, $args, $echo=false ) {
		$error_code = isset($_GET['errc']) ? sanitize_key($_GET['errc']) : ''; // phpcs:disable WordPress.Security.NonceVerification

		if ( $error_code )
			$this->form_errors[] = $error_code;

		$this->form_errors = array_filter( array_unique( $this->form_errors ) );
		if ( !$this->form_errors )
			return;

		foreach ( $this->form_errors as $code ) {

			$message = amem()->errors->text($code);
			$message = apply_filters( 'amem/form/error_message', $message, $code, $form, $args );
			$message = apply_filters( 'amem/form/error_message/type=' . $form['type'], $message, $code, $form, $args );

			$message = amem_format_message( amem_resolve_merge_tags( $message ), false, false );

			$output .= "\n" . sprintf( '<div class="acf-notice -error amem-notice amem-error-message -dismiss" aria-live="assertive" role="error"><p>%s<a href="#" class="acf-notice-dismiss acf-icon -cancel small"></a></p></div>', $message );// message text escaped by amem_format_message()
		}

		if ( $echo )
			echo $output;

		return $output;
	}

	function get($input) {
		if ( isset($this->errors[$input]) )
			return isset($this->errors[$input]);
		return null;
	}

	function reset() {
		$this->errors = [];
	}

	function get_errors() {
		return $this->errors;
	}

	function has_errors() {
		return count($this->errors) > 0;
	}

	function to_wp_error() {
		$wp_error = new \WP_Error;

		$errors = acf_get_validation_errors();
		if ( $errors ) {
		  foreach ( $errors as $error ) {
		    $wp_error->add( $error['input'], $error['message'] );
		  }
		}

		if ( count($this->errors) > 0 ) {
			foreach ( $this->errors as $key => $val ) {
				$wp_error->add( $key, $val );
			}
		}

		return $wp_error;
	}

}

amem()->register_module('errors', Errors::getInstance());

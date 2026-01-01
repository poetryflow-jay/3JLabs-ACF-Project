<?php
/**
 * Handles members form validation,
 * Saving instant form data,
 * encrypt when needed
 *
 * @since 1.0
 * @package Advanced Members for ACF \ Forms
 *
 */
namespace AMem\Forms;
use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class Submissions extends Module {

	const COOKIE_NAME = 'amem_submission';

	const EXPIRY_MINUTES = 5;
	const DATA_PREFIX = 'amem_submission_data_';
	const EXPIRY_PREFIX = 'amem_submission_expiry_';

	protected $is_ajax = false;

	protected $redirect_url;

	function __construct() {
		// validate ajax nonce before acf
		add_action( 'wp_ajax_acf/validate_save_post', array( $this, 'ajax_validate' ), 5 );
		add_action( 'wp_ajax_nopriv_acf/validate_save_post', array( $this, 'ajax_validate' ), 5 );

		// process ajax submission
		add_action( 'wp_ajax_amem_submission', array( $this, 'ajax_submission' ), 10, 0 );
		add_action( 'wp_ajax_nopriv_amem_submission', array( $this, 'ajax_submission' ), 10, 0 );

		// process page submission
		add_action( 'init', array( $this, 'pre_form' ), 10, 0 );

		// load submission and validate form
		add_action( 'acf/validate_save_post', array( $this, 'validate' ), 4 );

		add_filter( 'acf/upload_prefilter', array( $this, 'intercept_upload_errors' ), 1000, 3 );
	}

	function ajax_validate() {
		if ( isset($_POST['amem_form_nonce']) && !amem_verify_ajax() ) { // phpcs:disable WordPress.Security.NonceVerification
			wp_send_json_success( array(
				'valid' => 0,
				'errors' => array(
					array( 'input' => 'acf[field_amem_errors]', 'message' => amem()->errors->text('nonce_failed') ),
				),
			) );
		}
	}

	function ajax_submission() {
		$this->is_ajax = true;
		// Validate the posted data. This validation has already been performed once over AJAX.
		if ( ! acf_validate_save_post() ) {
			// wp_send_json_error( array(
			// 	'errors' => array(
			// 		array( 'message' => 'Validation failed' ),
			// 	),
			// ), 400 );
			wp_send_json_success( array(
				'valid' => 0,
				'errors' => array(
					array( 'input' => 'acf[field_amem_errors]', 'message' => amem()->errors->text('nonce_failed') ),
				),
			) );
		}

		$form = amem()->submission['form'];
		$args = amem()->submission['args'];
		$fields = amem()->submission['fields'];

		if ( !$form['active'] ) {
			wp_send_json_success( array(
				'valid' => 0,
				'errors' => array(
					array( 'input' => 'acf[field_amem_errors]', 'message' => amem()->errors->text('form_disabled') ),
				),
			) );
		}

		// Process submission. If it fails we return all errors.
		if ( ! $this->process_submission( amem()->submission ) ) {
			$errors = array();
			foreach ( amem()->submission['errors'] as $message ) {
				$errors[] = array(
					'input' => 'field_amem_errors',
					'message' => $message,
				);
			}

			wp_send_json_success( array(
				'valid' => 0,
				'errors' => $errors,
			) );
		}

		$response = array(
			'type' => 'none',
		);

		// Redirect to different URL if redirect argument has been passed
		$redirect_url = $args['redirect'];

		if ( !is_null($this->redirect_url) )
			$redirect_url = $this->redirect_url;

		$_redirect_url = explode( '?', $redirect_url );
		$_redirect_url = $_redirect_url[0];

		$after_submit = $form['data']['after_submit'];
		$show_message = ['success_message', 'just_success_message'];

		if ( in_array( $after_submit, $show_message) )
			$redirect_url = '';

		if ( ! empty( $redirect_url ) && !in_array($_redirect_url, $show_message, true) ) {
			$response = array(
				'type' => 'redirect',
				'redirect_url' => $_redirect_url,
			);
		} else if ( ! empty( $args['filter_mode'] ) ) {
			// Using amem/form/ajax/response filter
		} else {
			$success_message = amem_form_success_message( $form, $args );
			if ( in_array($after_submit, ['success_message', 'just_success_message'], true) )
				$type = $after_submit;
			else
				$type = 'success_message';
			$response = array(
				'type' => $type,
				'success_message' => $success_message,
			);
		}

		$response = apply_filters( 'amem/form/ajax/response', $response, $form, $args );
		$response = apply_filters( 'amem/form/ajax/response/type=' . $form['type'], $response, $form, $args );

		wp_send_json_success( $response );
	}

	function is_ajax() {
		return $this->is_ajax || wp_doing_ajax();
	}

	function set_redirect(String $url) {
		/** @todo validate and format url */
		$this->redirect_url = $url;
	}

	/**
	 * Detect POST form data and ready for submission
	 * @todo Decide put validation to ajax || pre_form
	 *
	 * @since 1.0
	 *
	 */
	function pre_form() {
		// Make sure this is not an AJAX validation request
		if ( isset ( $_POST['action'] ) ) { // phpcs:disable WordPress.Security.NonceVerification
			return;
		}

		// Try loading submission data
		if ( ! $this->load_submission() ) {
			return;
		}

		// Validate the posted data, this validation has already been performed once over AJAX
		if ( ! acf_validate_save_post( true ) ) {
			return;
		}

		$this->process_submission( amem()->submission );

		// static::after_submission( amem()->submission );
	}

	static function after_submission( $submission ) {
		// Redirect to different URL if redirect argument has been passed
		$redirect_url = $submission['args']['redirect'];

		// By default the user is redirected back to the form page.
		// Some browsers will prompt to submit the form again if the form page is reloaded.
		// Redirecting back removes the risk of duplicate submissions.
		if ( null === $redirect_url ) {
			$redirect_url = $submission['origin_url'];
		}

		// do_action( 'amem/form/after_submission', $redirect_url, $submission );

		// if ( $redirect_url && '' !== $redirect_url ) {
		// 	static::clear_expired_submissions();
		// 	static::save_submission( $submission );

		// 	wp_redirect( $redirect_url );
		// 	exit;
		// }
	}

	/**
	 * 
	 * @since 1.0
	 * 
	 */
	function process_submission( $submission ) {
		$form = $submission['form'];
		$args = $submission['args'];
		$fields = $submission['fields'];

		do_action( 'amem/form/before_submission', $form, $fields, $args );
		do_action( 'amem/form/before_submission/type=' . $form['type'], $form, $fields, $args );

		if ( amem_submission_failed() ) {
			return false;
		}

		static::call_submission_handlers( $submission );

		return true;
	}

	static function call_submission_handlers( $submission ) {
		$form = $submission['form'];
		$args = $submission['args'];
		$fields = $submission['fields'];

		do_action( 'amem/form/submission', $form, $fields, $args );
		do_action( 'amem/form/submission/type=' . $form['type'], $form, $fields, $args );
	}

	function honeypot_check() {
		if ( isset( $_POST['_acmf_validate_email'] ) && ! empty( $_POST['_acmf_validate_email'] ) ) { // phpcs:disable WordPress.Security.NonceVerification
			return false;
		}
		return true;
	}

	/**
	 * Handles validation of a form.
	 * Adds custom validation actions specific to forms.
	 *
	 * @since 1.0
	 *
	 */
	function validate() {
		// Try loading submission data
		if ( ! $this->load_submission() ) {
			return;
		}

		$form = amem()->submission['form'];
		$fields = amem()->submission['fields'];
		$args = amem()->submission['args'];

		if ( !$this->honeypot_check() ) {
			amem_add_error( 'field_amem_errors', amem()->errors->text('invalid_honeypot') );
			return;
		}

		do_action( 'amem/form/validate', $form, $args, $fields );
		do_action( 'amem/form/validate/type=' . $form['type'], $form, $args, $fields );
	}

	/**
	 * Populate amem()->submission with submission data
	 * Returns boolean indicating whether a submission was loaded
	 *
	 * @since 1.0
	 *
	 */
	function load_submission() {
		// Check if there is a cookie-passed submission
		if ( $submission = $this->get_submission() ) {
			amem()->submission = $submission;

			// Return false to stop the submission from being processed again
			return false;
		}

		// Make sure a form was posted
		if ( ! ( isset( $_POST['amem_form'] ) ) ) { // phpcs:disable WordPress.Security.NonceVerification
			return false;
		}

		// Bail early if already loaded
		if ( amem()->submission ) {
			return true;
		}

		/**
		 * Upload all files in $_FILES using ACFs helper function. Required for basic uploads to work painlessly.
		 * @todo Move to amem_save_field() to avoid saving all files?
		 *
		 * @since 1.0
		 */
		if ( isset( $_FILES['acf'] ) ) { // phpcs:disable WordPress.Security.NonceVerification
			$this->clear_upload_errors();
			acf_upload_files();
			$this->handle_upload_errors();
		}

		// Generate submission from data
		$submission = $this->create_submission();
		if ( ! $submission ) {
			return false;
		}

		// Save submission data to the global AMem object
		amem()->submission = $submission;
		amem()->set_form($submission['form'], $submission['args']);

		return true;
	}

	/**
	 * Create a submission object from the request data.
	 * Returns a submission array or false on failure.
	 *
	 * @since 1.0
	 *
	 */
	function create_submission() {
		// Load form by key
		$form_key_or_id = sanitize_text_field($_POST['amem_form']); // phpcs:disable WordPress.Security.NonceVerification

		do_action( 'amem/form/create_submission/pre', $form_key_or_id );

		$form = amem_get_form( $form_key_or_id );

		if ( ! $form ) {
			return false;
		}

		// Retrieve the args used to display the form
		$encoded_args = sanitize_text_field($_POST['amem_form_args']); // phpcs:disable WordPress.Security.NonceVerification
		$args = amem_decrypt( $encoded_args );

		// Verify nonce
		$nonce = sanitize_text_field($_POST['amem_form_nonce']); // phpcs:disable WordPress.Security.NonceVerification
		$hashed_args = hash( 'sha256', $encoded_args );
		$nonce_value = sprintf( 'amem_submission_%s_%s', $form['key'], $hashed_args );

		if ( ! wp_verify_nonce( $nonce, $nonce_value ) ) {
			if ( doing_action( 'acf/validate_save_post') ) {
				amem_add_error( 'field_amem_errors', amem()->errors->text('nonce_failed') );
				return false;
			}	else {
				amem_add_submission_error( amem()->errors->text('nonce_failed') );
				// wp_die( 'Your submission failed. Please reload the page and try again.' );
				return false;
			}
		}

		// Retrieve all form fields and their values
		$fields = array();
		$record = array_filter($form['amem']);
		$record = array_flip($record);

		do_action( 'amem/form/create_submission/before', $form, $args );
		do_action( 'amem/form/create_submission/before/type=' . $form['type'], $form, $args );
		$use_raw = apply_filters( 'amem/form/use_raw_record', ['user_password'], $form, $args );

		if ( isset( $_POST['acf'] ) ) { // phpcs:disable WordPress.Security.NonceVerification
			$acfData = amem_sanitize_input( $_POST['acf'] );
			foreach ( $acfData as $k => $value ) { // phpcs:disable WordPress.Security.NonceVerification
				$field = acf_get_field( sanitize_key($k) );

				if ( empty( $field ) ) {
					continue;
				}

				$field['_input'] = $value;
				if ( !empty($field['type']) && in_array( $field['type'], $use_raw) ) {
					$field['_input'] = $_POST['acf'][$k];
				}
				$field['value'] = acf_format_value( $value, 0, $field );

				$fields[] = $field;

				if ( isset($record[$field['key']]) ) {
					$form['record'][$record[$field['key']]] = $field['_input'];
				}
			}
		}

		return array(
			'form' => $form,
			'args' => $args,
			'fields' => $fields,
			'errors' => array(),
			'origin_url' => sanitize_text_field($_POST['amem_origin_url']), // phpcs:disable WordPress.Security.NonceVerification
		);
	}

	/**
	 * Fetch a submission from options if the submission cookie is set.
	 * Will return false if the cookie is not set or the submission does not exist in the database.
	 *
	 * @since 1.0
	 *
	 */
	private function get_submission() {
		if ( ! isset( $_COOKIE[ static::get_cookie_name() ] ) ) {
			return false;
		}

		$key = sanitize_key( $_COOKIE[ static::get_cookie_name() ] );
		$submission = get_option( static::DATA_PREFIX . $key, false );

		static::delete_submission( $key );
		setcookie( static::get_cookie_name(), '', time() - HOUR_IN_SECONDS, '/' );

		return $submission;
	}

	/**
	 * Save a submission to options and set a cookie with a reference to it.
	 * Submissions are identified by a randomly generated key stored in a cookie.
	 *
	 * @since 1.0
	 *
	 */
	private static function save_submission( $submission ) {
		$key = wp_generate_password( 12, false, false );

		$expiration_time = time() + static::EXPIRY_MINUTES * MINUTE_IN_SECONDS;

		add_option( static::DATA_PREFIX . $key, $submission );
		add_option( static::EXPIRY_PREFIX . $key, $expiration_time );

		setcookie( static::get_cookie_name(), $key, $expiration_time, '/' );
	}

	/**
	 * Delete a submission from options based on key
	 *
	 * @since 1.0
	 *
	 */
	private static function delete_submission( $key ) {
		delete_option( static::DATA_PREFIX . $key );
		delete_option( static::EXPIRY_PREFIX . $key );
	}

	private static function get_cookie_name() {
		return apply_filters( 'amem/settings/cookie_name', static::COOKIE_NAME );
	}

	/**
	 * Remove any expired submission from options which have not been cleared automatically.
	 * If a request fails a created submission could potentially not be removed from the database.
	 *
	 * @since 1.0
	 *
	 */
	private static function clear_expired_submissions() {
		global $wpdb;

		$name_pattern = static::EXPIRY_PREFIX . '%';
		$current_time = time();

		// Find all expired submissions in the options table.
		// This query is very efficient because of the index on the name column.
		$expired_submissions = $wpdb->get_col( $wpdb->prepare( "
      SELECT option_name
      FROM {$wpdb->options}
      WHERE option_name LIKE %s
        AND option_value < %d
    ", $name_pattern, $current_time ) );

		foreach ( $expired_submissions as $option_name ) {
			// Find submission key by removing prefix from option name.
			$submission_key = substr( $option_name, strlen( static::EXPIRY_PREFIX ) );
			static::delete_submission( $submission_key );
		}
	}

	/**
	 * ACF doesn't provide a simple way of catching upload errors when using the basic uploader.
	 * This function is hooked into the "acf/upload_prefilter" with a high priority.
	 * It will intercept all upload errors and save them together with field data.
	 *
	 * @since 1.0
	 *
	 */
	function intercept_upload_errors( $errors, $file, $field ) {
		if ( ! empty( $errors ) ) {
			$this->upload_errors[ $field['key'] ] = array(
				'field' => $field,
				'messages' => $errors,
			);
		}

		return $errors;
	}

	/**
	 * Removes all intercepted upload errors.
	 * Should be run before handling uploads using "acf_upload_files()".
	 *
	 * @since 1.0
	 *
	 */
	private function clear_upload_errors() {
		$this->upload_errors = array();
	}

	/**
	 * Checks if any upload errors have been caught and stops the submission.
	 * This is a very rudimentary way of handling upload errors but it's necessary as ACF can't handle errors when using the basic uploader.
	 * The errors checks should in the future be implemented client-side for a good user experience and this is mostly meant to be a fallback.
	 *
	 *
	 * @since 1.0
	 *
	 */
	private function handle_upload_errors() {
		if ( empty( $this->upload_errors ) ) {
			return;
		}

		$message = sprintf( '<h2>%s</h2>', esc_html__( 'Validation failed', 'advanced-members' ) );
		$message .= '<ul>';
		foreach ( $this->upload_errors as $error ) {
			$field = $error['field'];
			foreach ( $error['messages'] as $error_message ) {
				$message .= '<li>' . sprintf( '%s: %s', $field['label'], $error_message ) . '</li>';
			}
		}
		$message .= '</ul>';

		wp_die( $message, esc_html__( 'Validation failed', 'advanced-members' ) );
	}
}

amem()->register_module('submissions', Submissions::getInstance());

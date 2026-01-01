<?php
/**
 * Render Form
 *
 * @since 1.0
 * @package Advanced Members for ACF \ Forms
 * 
 */
namespace AMem;
use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

abstract class Action extends Module {

	function __construct() {
		// add_action( 'amem/form/validate', array( $this, 'verify_nonce'), 10, 3 );
	}

	public function __get($key) {
	  if ( isset($this->$key) )
	    return $this->$key;

	  return null;
	}

	public function is_valid() {
		return ! ( acf_get_validation_errors() || amem_submission_failed() );
	}

	public function verify_nonce($form, $args, $fields) {
		// Retrieve the args used to display the form
		$encoded_args = $_POST['amem_form_args']; // phpcs:disable WordPress.Security.NonceVerification
		$args = amem_decrypt( $encoded_args );

		// Verify nonce
		$nonce = sanitize_key($_POST['amem_form_nonce']); // phpcs:disable WordPress.Security.NonceVerification
		$hashed_args = hash( 'sha256', $encoded_args );
		$nonce_value = sprintf( 'amem_submission_%s_%s', $form['key'], $hashed_args );
		if ( ! wp_verify_nonce( $nonce, $nonce_value ) ) {
			amem_add_error( 'field_amem_errors', amem()->errors->text('nonce_failed') );
		}
	}

	protected function disable_ajax() {
		add_filter( 'amem/form/acf_data/type=passwordreset', [$this, 'acf_disable_ajax'] );
	}

	public function acf_disable_ajax($acf_data) {
		$acf_data['ajax'] = false;

		return $acf_data;
	}

	// abstract public function get_name();

	// abstract public function get_label();

	// public function handle_form( $form ) {
	// 	return $settings;
	// }

}


<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'user_password_confirm' ) ) :

class amem_field_user_password_confirm extends amem_field_password {

	function initialize() {
		// vars
		$this->name       = 'user_password_confirm';
		$this->label      = __( 'User Password Confirm', 'advanced-members' );
		$this->category = 'Advanced Members';
		$this->public = false;
		$this->defaults   = array(
			'placeholder' => '',
			'prepend'     => '',
			'append'      => '',
		);

		add_filter( 'acf/load_field/type=password', array( $this, 'load_user_password_confirm_field' ) );
		add_filter( 'acf/update_value/type=' . $this->name, array( $this, 'pre_update_value' ), 9, 3 );

	}

	function load_user_password_confirm_field( $field ) {
		if ( ! empty( $field['custom_password_confirm'] ) ) {
			$field['type'] = 'user_password_confirm';
		}
		return $field;
	}

	function prepare_field( $field ) {
		$form = amem()->form;
		if ( isset( $field['wrapper']['class'] ) ) {
			$field['wrapper']['class'] .= ' amem-password-confirm amem-pwd';
		} else {
			$field['wrapper']['class'] = 'amem-password-confirm amem-pwd';
		}

		if ( isset( $form['approval'] ) ) {
			return false;
		}

		if ( ! $field['value'] ) {
			return $field;
		}
		if ( isset( $field['wrapper']['class'] ) ) {
			$field['wrapper']['class'] .= ' acf-hidden';
		}

		$field['required'] = false;
		$field['value']    = '';

		return $field;
	}

	public function load_value( $value, $post_id = false, $field = false ) {
		if ( $user_id = $this->_user_id($post_id) ) {
			$edit_user = get_user_by( 'ID', $user_id );
			if ( $edit_user instanceof \WP_User ) {
				$value = 'i';
			}
		}
		return $value;
	}

	function validate_value( $is_valid, $value, $field, $input ) {
		// if ( is_numeric( $_POST['_acf_user'] ) && ! isset( $_POST['edit_user_password'] ) ) {
		// 	return $is_valid;
		// }

		if ( empty( $_POST['custom_password'] ) ) { // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
			return false;
		}
		$password_field = sanitize_key( $_POST['custom_password'] ); // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
		if ( sanitize_text_field($_POST['amem']['user'][ $password_field ]) != $value ) { // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
			return __( 'The passwords do not match', 'advanced-members' );
		}

		return $is_valid;
	}

	function pre_update_value( $value, $post_id = false, $field = false ) {
		 return null;
	}

	function render_field( $field ) {
		$field['type'] = 'password';
		parent::render_field( $field );

		echo '<div class="pass-strength-result weak"></div>';
		echo '<input type="hidden" name="custom_password_confirm" placeholder="' . esc_attr__( 'Password Confirm', 'advanced-members' ) . '" value="' . esc_attr( $field['key'] ) . '"/>';
	}


}

// initialize
acf_register_field_type( 'amem_field_user_password_confirm' );

endif;



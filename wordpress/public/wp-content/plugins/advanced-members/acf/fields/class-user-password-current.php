<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_user_password_current' ) ) :

class amem_field_user_password_current extends amem_field_password {

	function initialize() {
		// vars
		$this->name       = 'user_password_current';
		$this->label      = __( 'User Password Current', 'advanced-members' );
		$this->category = 'Advanced Members';
		$this->public = false;
		$this->defaults   = array(
			'placeholder'       => __( 'Type your current password', 'advanced-members' ),
			'prepend'           => '',
			'append'            => '',
		);

		add_filter( 'acf/load_field/type=password', array( $this, 'load_user_password_field' ) );
		add_filter( 'acf/update_value/type=' . $this->name, array( $this, 'pre_update_value' ), 9, 3 );
		// add_filter( 'acf/validate_value/key=' . $this->name, array( $this, 'validate_value' ), 10, 4 );
	}

	function load_user_password_field( $field ) {
		if ( ! empty( $field['custom_password_current'] ) ) {
			$field['type'] = 'user_password_current';
		}
		return $field;
	}

	function prepare_field( $field ) {
		$field['required'] = true;
		if ( isset( $field['wrapper']['class'] ) ) {
			$field['wrapper']['class'] .= ' amem-password-current amem-pwd';
		} else {
			$field['wrapper']['class'] = 'amem-password-current amem-pwd';
		}

		if ( ! $field['value'] ) {
			return $field;
		}

		$form = amem()->form;
		if ( isset( $form['approval'] ) ) {
			return false;
		} else {
			$field['value'] = '';
		}
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
		if ( $this->mode() != 'account' ) {
			return $is_valid;
		}

		if ( empty($value) )
			return __( 'Current password is required', 'advanced-members' );

		$user_data = !empty(amem()->user->data) ? amem()->user->data : [];

		$user = false;
		if ( $user_data && wp_check_password( $value, $user_data['user_pass'], $user_data['ID'] ) ) {
			amem()->submission['form']['auth_id'] = $user_data['ID'];
		} else {
			$is_valid = amem()->errors->text('password_incorrect');//__( 'Current password is incorrect. Please try again.', 'advanced-members' );
			// amem_add_error( $this->name, __( 'Current password is incorrect. Please try again.', 'advanced-members' ) );
		}

		return $is_valid;
	}

	function pre_update_value( $value, $post_id = false, $field = false ) {
		// This field does not require udpates
		return null;
	}

	function update_value( $field ) {
		return null;
	}

	function render_field( $field ) {
		$field['type'] = 'password';

		parent::render_field( $field );
	}

}


// initialize
acf_register_field_type( 'amem_field_user_password_current' );

endif;



<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_user_password' ) ) :

class amem_field_user_password extends amem_field_password {

	function initialize() {
		// vars
		$this->name       = 'user_password';
		$this->label      = __( 'User Password', 'advanced-members' );
		$this->category = 'Advanced Members';
		$this->description = __( 'Login password of the user. Synced to `user_pass` user data.', 'advanced-members' );
		$this->preview_image = amem_get_url('images/field-type-previews/field-preview-password.png', 'assets');
		$this->defaults   = array(
			'placeholder'       => '',
			'prepend'           => '',
			'append'            => '',
			'force_edit'        => 0,
			'password_strength' => '3',
			'show_pass_confirm' => 0,
			'bypass_empty' => 0,
		);

		add_filter( 'acf/load_field/type=password', array( $this, 'load_user_password_field' ) );
		add_filter( 'acf/update_value/type=' . $this->name, array( $this, 'pre_update_value' ), 9, 3 );
	}

	function load_user_password_field( $field ) {
		if ( ! empty( $field['custom_password'] ) ) {
			$field['type'] = 'user_password';
		}
		return $field;
	}

	function prepare_field( $field ) {
		$field['required'] = 1;
		if ( !empty($field['bypass_empty']) )
			$field['required'] = 0;
		if ( isset( $field['wrapper']['class'] ) ) {
			$field['wrapper']['class'] .= ' amem-password-main amem-pwd';
		} else {
			$field['wrapper']['class'] = 'amem-password-main amem-pwd';
		}

		if ( ! $field['value'] ) {
			return $field;
		}

		// if ( empty( $field['force_edit'] ) ) {
		// 	$field['required']           = false;
		// 	$field['wrapper']['class']  .= ' edit_password';
		// 	$field['edit_user_password'] = true;
		// }

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
		// if ( is_numeric( $_POST['_acf_user'] ) && ! isset( $_POST['edit_user_password'] ) ) {
		// 	return $is_valid;
		// }

		if ( $this->mode() == 'login' ) {
			return $is_valid;
		}

		if ( !empty($field['bypass_empty']) && empty($value) )
			return $is_valid;

		if ( $field['show_pass_confirm'] && amem()->is_amem() ) {
			$ps_confirm_field = sanitize_key( $_POST['custom_password_confirm'] ); // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
			if ( !isset($_POST[ $ps_confirm_field ]) || sanitize_text_field($_POST[ $ps_confirm_field ]) != $value ) { // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
				return __( 'The passwords do not match', 'advanced-members' );
			}
		}
		if ( isset( $_POST['password-strength'] ) && isset( $_POST['required-strength'] ) ) { // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
			if( absint( $_POST['password-strength'] ) < absint( $_POST['required-strength'] ) ) { // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
				// if ( ! $field['required'] && $value == '' && ! isset( $_POST['edit_user_password'] ) ) {
				// 	return $is_valid;
				// }
				return __( 'The password is too weak. Please make it stronger.', 'advanced-members' );
			}
		}

		return $is_valid;
	}

	function pre_update_value( $value, $post_id = false, $field = false ) {
		// if ( empty( $_POST['edit_user_password'] ) ) {
		// 	return null;
		// }

		if ( $user_id = $this->_user_id($post_id) && !empty($value) ) {
			if ( !$this->_can_edit($user_id) )
				return null;
			remove_action( 'acf/save_post', '_acf_do_save_post' );
			wp_update_user(
				array(
					'ID'        => $user_id,
					'user_pass' => $value,
				)
			);
			add_action( 'acf/save_post', '_acf_do_save_post' );
		}
		return null;
	}

	function update_value( $field ) {
		return null;
	}

	function render_field( $field ) {
		$field['type'] = 'password';

		parent::render_field( $field );
		if ( $this->mode() == 'login' ) {
			return;
		}
		wp_enqueue_script( 'password-strength-meter' );
		wp_enqueue_script( 'amem-password-strength' );
		echo '<input type="hidden" name="custom_password" value="' . esc_attr( $field['key'] ) . '"/>';
		if ( isset( $field['password_strength'] ) ) {
			echo '<div class="pass-strength-result weak"></div>';
			echo '<div class="pass-strength-checker">';
			echo '<input type="hidden" value="' . esc_attr( $field['password_strength'] ) . '" name="required-strength"/>';
			echo '<input class="password-strength" type="hidden" value="" name="password-strength"/>';
			echo '</div>';
		}

		// if ( empty( $field['force_edit'] ) ) {
		// 	if ( ! empty( $field['edit_user_password'] ) ) {
		// 		$edit_text   = empty( $field['edit_password'] ) ? __( 'Edit Password', 'advanced-members' ) : $field['edit_password'];
		// 		$cancel_text = empty( $field['cancel_edit_password'] ) ? __( 'Cancel', 'advanced-members' ) : $field['cancel_edit_password'];
		// 		echo '<button class="cancel-edit" type="button">' . esc_html( $cancel_text ) . '</button><button class="acf-button button button-primary edit-password" type="button">' . esc_html( $edit_text ) . '</button>';
		// 	}
		// }

		if ( $field['show_pass_confirm'] ) {
			add_action( 'amem/field/after_field/key=' . $field['key'], [$this, 'print_confirm_field'], 10, 3 );
		}
	}

	function print_confirm_field($field, $form, $args) {
		$placeholder = !empty($field['confirm_placeholder']) ? $field['confirm_placeholder'] : __( 'Password Confirm', 'advanced-members' );
		acf_add_local_field( [
			'key' => 'user_password_confirm',
			'label' => $field['confirm_label'] ?? __( 'Password Confirm', 'advanced-members' ),
			'name' => 'user_password_confirm',
			'type' => 'user_password_confirm',
			'placeholder' => $placeholder,
    	'required' => !empty($field['bypass_empty']) ? false : true,
    	'wrapper' => $field['wrapper'] ?? null,
    	'_amem_local' => true,
		] );

		$confirm = acf_get_local_field('user_password_confirm');

		amem()->render->render_field( $confirm, $form, $args );

		remove_action( 'amem/field/after_field/key=' . $field['key'], [$this, 'print_confirm_field'] );
	}

	function render_field_settings( $field ) {
		parent::render_field_settings( $field );
		acf_render_field_setting(
			$field,
			array(
				'label'         => __( 'Password Strength', 'advanced-members' ),
				'name'          => 'password_strength',
				'type'          => 'select',
				'default_value' => '3',
				'choices'       => array(
					'1' => __( 'Very Weak', 'advanced-members' ),
					'2' => __( 'Weak', 'advanced-members' ),
					'3' => __( 'Medium', 'advanced-members' ),
					'4' => __( 'Strong', 'advanced-members' ),
				),
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Show password confirmation', 'advanced-members' ),
				'instructions' => __( 'Show password confirmation field for users to check that the password is not mistyped. (Only works with Advanced Members Forms)', 'advanced-members' ),
				'name'         => 'show_pass_confirm',
				'type'         => 'true_false',
				'ui'           => 1,
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Password Confirm Label', 'advanced-members' ),
				'instructions' => __( 'Label for the password confirmation field', 'advanced-members' ),
				'name'         => 'confirm_label',
				'type'         => 'text',
				'default_value' => __( 'Password Confirm', 'advanced-members' ),
				'default' => __( 'Password Confirm', 'advanced-members' ),
				'conditions' 	 => array(
					'field' => 'show_pass_confirm',
					'operator' => '==',
					'value' => '1',
				),
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Password Confirm Placeholder Text', 'advanced-members' ),
				'instructions' => __( 'Placeholder text for the password confirmation field', 'advanced-members' ),
				'name'         => 'confirm_placeholder',
				'type'         => 'text',
				'default_value' => __( 'Password Confirm', 'advanced-members' ),
				'default' => __( 'Password Confirm', 'advanced-members' ),
				'conditions' 	 => array(
					'field' => 'show_pass_confirm',
					'operator' => '==',
					'value' => '1',
				),
			)
		);
		// acf_render_field_setting(
		// 	$field,
		// 	array(
		// 		'label'        => __( 'Show current password', 'advanced-members' ),
		// 		'instructions' => __( 'Show current password field. Users can skip password changes if the current password field is left empty.', 'advanced-members' ),
		// 		'name'         => 'show_current_passwd',
		// 		'type'         => 'true_false',
		// 		'ui'           => 1,
		// 	)
		// );

	}

}


// initialize
acf_register_field_type( 'amem_field_user_password' );

endif;



<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_username' ) ) :

class amem_field_username extends amem_field_text {

	function initialize() {
		// vars
		$this->name       = 'username';
		$this->label      = __( 'Username or Email', 'advanced-members' );
		$this->category = 'Advanced Members';
		$this->public = true;
		$this->preview_image = amem_get_url('images/field-type-previews/field-preview-user.png', 'assets');
		$this->description = __( 'User login name. You can use this field as "Username or Email" on the Login/Registration form.', 'advanced-members' );
		$this->defaults   = array(
			'default_value' => '',
			'maxlength'     => '',
			'placeholder'   => '',
			'prepend'       => '',
			'append'        => '',
			'allow_edit'		=> 0,
			'required' 			=> 1,
		);

		add_filter( 'acf/load_field/type=text', array( $this, 'load_username_field' ) );
		add_filter( 'acf/load_field/type=' . $this->name, array( $this, 'force_required' ) );
		add_filter( 'acf/update_value/type=' . $this->name, array( $this, 'pre_update_value' ), 9, 3 );
	}

	function force_required( $field ) {
		$field['required'] = 1;
		return $field;
	}

	function load_username_field( $field ) {
		if ( ! empty( $field['custom_username'] ) ) {
			$field['type'] = 'username';
		}
		return $field;
	}

	public function prepare_field( $field ) {
		// make sure field is not disabled when no value exists
		if ( ! $field['value'] || !empty( amem()->submission ) ) {
			$field['disabled'] = 0;
		} else {
			if( empty( $field['allow_edit'] ) && $field['value'] ){
				$field['disabled'] = 1;
			}
		}
		$field['required'] = 1;
		$field['type'] = 'text';

		return $field;
	}

	public function load_value( $value, $post_id = false, $field = false ) {
		if ( $user_id = $this->_user_id($post_id) ) {
			$edit_user = get_user_by( 'ID', $user_id );
			if ( $edit_user instanceof \WP_User ) {
				$value = $edit_user->user_login;
			}
		}

		return $value;
	}

	public function validate_value( $is_valid, $value, $field, $input ) {
		if ( $field['required'] == 0 && $value == '' ) {
			return $is_valid;
		}

		if ( ! validate_username( $value ) ) {
			return __( 'The username contains illegal characters. Please enter only latin letters, numbers, @, -, . and _', 'advanced-members' );
		}

		if ( empty( $_POST['_acf_user'] ) ) { // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
			return $is_valid;
		}

		$user_id   = absint( $_POST['_acf_user'] ); // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
		if( $user_id ) {
			$edit_user = get_user_by( 'ID', $user_id );
		}

		if ( !in_array($this->mode(), ['login', 'passwordreset']) ) {
			/* translators: Username or email string */
			$username_taken = sprintf( __( 'The username or email %s is already in use. Please try a different username', 'advanced-members' ), $value );

			if ( username_exists( $value ) ) {
				if ( ! empty( $edit_user->user_login ) && $edit_user->user_login == $value ) {
					return $is_valid;
				}
				return $username_taken;
			}

			if ( email_exists( $value ) ) {
				if ( ! empty( $edit_user->user_email ) && $edit_user->user_email == $value ) {
					return $is_valid;
				}
				return $username_taken;
			}

		} else {
			if ( !username_exists( $value ) && !email_exists( $value ) ) {
				/* translators: Username or email string */
				$username_not_exists = sprintf( __( 'The username or email %s does not exist on this site. Please try a different username', 'advanced-members' ), $value );
				return $username_not_exists;
			}
		}

		return $is_valid;
	}

	function pre_update_value( $value, $post_id = false, $field = false ) {
		global $wpdb;
		// Disable user_login updates
		// if ( $user_id = $this->_user_id($post_id) ) {
		// 	$user_object = get_user_by( 'ID', $user_id );
		// 	if ( $user_object->user_login == $value ) {
		// 		return null;
		// 	}

		// 	if ( get_current_user_id() == $user_id ) {
		// 		$_POST['log_back_in'] = array( $user_id, $value );
		// 	}

		// 	$wpdb->update( $wpdb->users, array( 'user_login' => $value ), array( 'ID' => $user_id ) );
		// }

		return null;
	}

	function render_field_settings( $field ) {
		parent::render_field_settings( $field );

		$msg1 = __( 'The Username field works as a "Username or Email" field on the Login page.', 'advanced-members' );
		$msg2 = __( 'Users cannot change their username, and this field is displayed as disabled except on the registration form.', 'advanced-members' );

		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Field instructions', 'advanced-members' ),
				'message' => sprintf( '<ul style="list-style-type:disc;margin-left:1.3em;"><li>%s</li><li>%s</li></ul>', $msg1, $msg2 ),
				'type'         => 'message',
				'ui'           => 1,
			) 
		);

		// default_value
		// acf_render_field_setting(
		// 	$field,
		// 	array(
		// 		'label'        => __( 'Allow Edit', 'advanced-members' ),
		// 		'instructions' => __( 'Allow users to change their username. WARNING: allowing your users to change their username might affect existing urls and their SEO rating.', 'advanced-members' ),
		// 		'name'         => 'allow_edit',
		// 		'type'         => 'true_false',
		// 		'ui'           => 1,
		// 	) 
		// );
	}

}

// initialize
acf_register_field_type( 'amem_field_username' );

endif;



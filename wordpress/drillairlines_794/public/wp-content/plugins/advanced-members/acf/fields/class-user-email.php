<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_user_email' ) ) :

class amem_field_user_email extends amem_field_email {

	function initialize() {
		// vars
		$this->name       = 'user_email';
		$this->label      = __( 'User Email', 'advanced-members' );
		$this->category = 'Advanced Members';
		$this->description = __( 'Email of the user. Synced to `user_email` user data.', 'advanced-members' );
		$this->preview_image = amem_get_url('images/field-type-previews/field-preview-email.png', 'assets');
		$this->defaults   = array(
			'default_value' => '',
			'placeholder'   => '',
			'prepend'       => '',
			'append'        => '',
			'required' 			=> 1,
		);

		add_filter( 'acf/load_field/type=email', array( $this, 'load_user_email_field' ) );
		add_filter( 'acf/load_field/type=' . $this->name, array( $this, 'forece_required' ) );
		add_filter( 'acf/update_value/type=' . $this->name, array( $this, 'pre_update_value' ), 9, 3 );
	}

	function forece_required( $field ) {
		$field['required'] = 1;
		return $field;
	}

	function load_user_email_field( $field ) {
		if ( ! empty( $field['custom_email'] ) ) {
			$field['type'] = 'user_email';
		}
		return $field;
	}

	public function prepare_field( $field ) {
		$field['required'] = 1;
		return $field;
	}

	public function load_value( $value, $post_id = false, $field = false ) {
		if ( $user_id = $this->_user_id($post_id) ) {
			$edit_user = get_user_by( 'ID', $user_id );
			if ( $edit_user instanceof \WP_User ) {
				$value = esc_html( $edit_user->user_email );
			}
		}
		return $value;
	}

	public function validate_value( $is_valid, $value, $field, $input ) {
		if ( ! $field['required'] && $value == '' ) {
			return $is_valid;
		}

		if ( ! empty( $field['prepend'] ) ) {
			$value = $field['prepend'] . $value;
		}
		if ( ! empty( $field['append'] ) ) {
			$value .= $field['append'];
		}

		$flags = defined( 'FILTER_FLAG_EMAIL_UNICODE' ) ? FILTER_FLAG_EMAIL_UNICODE : 0;

		if ( $value && filter_var( wp_unslash( $value ), FILTER_VALIDATE_EMAIL, $flags ) === false ) {
			/* translators: Given email address */
			return sprintf( __( "'%s' is not a valid email address", 'advanced-members' ), esc_html( $value ) );
		}

		if ( empty( $_POST['_acf_user'] ) ) { // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
			return $is_valid;
		}

		$user_id   = absint( $_POST['_acf_user'] ); // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
		if( $user_id ){
			$edit_user = get_user_by( 'ID', $user_id );
		}

		if ( $this->mode() == 'login' ) {
			if ( !email_exists( $value ) ) {
				return sprintf( __( 'The email does not existing in this site. Please try a different email', 'advanced-members' ) );
			}
			// Validation will be passed to login action validator
			return $is_valid;
		}

		if ( email_exists( $value ) ) {
			if ( ! empty( $edit_user->user_email ) && $edit_user->user_email == $value ) {
				return $is_valid;
			}
			/* translators: email value string */
			return sprintf( __( 'The email %s is already assigned to an existing account. Please try a different email or login to your account', 'advanced-members' ), $value );
		}

		if ( ! empty( $field['set_as_username'] ) ) {
			if ( username_exists( $value ) ) {
				if ( ! empty( $edit_user->user_login ) && $edit_user->user_login == $value ) {
					return $is_valid;
				}
				/* translators: email value string */
				return sprintf( __( 'The username %s is taken. Please try a different username', 'advanced-members' ), $value );
			}
		}

		return $is_valid;
	}

	function pre_update_value( $value, $post_id = false, $field = false ) {
		if ( $user_id = $this->_user_id($post_id) ) {
			if ( !$this->_can_edit($user_id) )
				return null;
			remove_action( 'acf/save_post', '_acf_do_save_post' );
			wp_update_user(
				array(
					'ID'         => $user_id,
					'user_email' => esc_attr( $value ),
				)
			);
			add_action( 'acf/save_post', '_acf_do_save_post' );

			// if ( $this->mode() == 'registration' && ! empty( $field['set_as_username'] ) ) {
			// 	$user_object = get_user_by( 'ID', $user_id );
			// 	if ( $user_object->user_login == $value ) {
			// 		return null;
			// 	}

			// 	if ( get_current_user_id() == $user_id ) {
			// 		$_POST['log_back_in'] = array( $user_id, $value );
			// 	}
			// 	wp_clear_auth_cookie();
			// 	global $wpdb;
			// 	$wpdb->update( $wpdb->users, array( 'user_login' => $value ), array( 'ID' => $user_id ) );
			// }
		}

		return null;
	}

	function render_field( $field ) {
		$field['type'] = 'email';
		parent::render_field( $field );
	}

	function render_field_settings( $field ) {
		parent::render_field_settings( $field );
		// default_value
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Set as Username', 'advanced-members' ),
				'instructions' => __( 'Save value as username as well. (Only works with registration forms)', 'advanced-members' ),
				'name'         => 'set_as_username',
				'type'         => 'true_false',
				'ui'           => 1,
			)
		);
	}

}

// initialize
acf_register_field_type( 'amem_field_user_email' );

endif;



<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_first_name' ) ) :

class amem_field_first_name extends amem_field_text {

	function initialize() {
		// vars
		$this->name       = 'first_name';
		$this->label      = __( 'First Name', 'advanced-members' );
		$this->category = 'Advanced Members';
		$this->description = __( 'First name of the user. Synced to `first_name` user meta.', 'advanced-members' );
		$this->preview_image = amem_get_url('images/field-type-previews/field-preview-text.png', 'assets');
		$this->defaults   = array(
			'default_value' => '',
			'maxlength'     => '',
			'placeholder'   => '',
			'prepend'       => '',
			'append'        => '',
		);

		add_filter( 'acf/load_field/type=text', array( $this, 'load_first_name_field' ) );
		add_filter( 'acf/update_value/type=' . $this->name, array( $this, 'pre_update_value' ), 9, 3 );
	}

	function load_first_name_field( $field ) {
		if ( ! empty( $field['custom_first_name'] ) ) {
			$field['type'] = 'first_name';
		}
		return $field;
	}

	public function load_value( $value, $post_id = false, $field = false ) {
		if ( $user_id = $this->_user_id($post_id) ) {
			$edit_user = get_user_by( 'ID', $user_id );
			if ( $edit_user instanceof \WP_User ) {
				$value = $edit_user->first_name;
			}
		}
		return $value;
	}

	public function prepare_field( $field ) {
		$field['type'] = 'text';
		return $field;
	}

	function pre_update_value( $value, $post_id = false, $field = false ) {
		if ( $field['name'] == 'first_name' ) {
			return $value;
		}

		if ( $user_id = $this->_user_id($post_id) ) {
			if ( !$this->_can_edit($user_id) )
				return null;
			remove_action( 'acf/save_post', '_acf_do_save_post' );

			$value = trim($value);
			$value = apply_filters( 'pre_user_first_name', $value );
			update_post_meta( $user_id, 'first_name', $value );

			add_action( 'acf/save_post', '_acf_do_save_post' );
		}

		return null;
	}

}

// initialize
acf_register_field_type( 'amem_field_first_name' );

endif;



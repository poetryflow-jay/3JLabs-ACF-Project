<?php
/**
 * ACF Field for User Bio
 *
 * @since 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_user_bio' ) ) :

class amem_field_user_bio extends amem_field_textarea {

	function initialize() {
		// vars
		$this->name       = 'user_bio';
		$this->meta_key 	= 'description';
		$this->label      = __( 'User Bio', 'advanced-members' );
		$this->category = 'Advanced Members';
		$this->description = __( 'Biographical info of the user. Synced to `description` user meta.', 'advanced-members' );
		$this->preview_image = amem_get_url('images/field-type-previews/field-preview-textarea.png', 'assets');
		$this->defaults   = array(
			'default_value' => '',
			'new_lines'     => '',
			'maxlength'     => '',
			'placeholder'   => '',
			'rows'          => '',
		);

		add_filter( 'acf/load_field/type=textarea', array( $this, 'load_user_bio_field' ) );
		add_filter( 'acf/update_value/type=' . $this->name, array( $this, 'pre_update_value' ), 9, 3 );
	}

	function load_user_bio_field( $field ) {
		if ( ! empty( $field['custom_user_bio'] ) ) {
			$field['type'] = 'user_bio';
		}
		return $field;
	}

	function prepare_field( $field ) {
		$field['type'] = 'textarea';
		return $field;
	}

	function load_value( $value, $post_id = false, $field = false ) {
		$user = explode( '_', $post_id );
		if ( $user_id = $this->_user_id($post_id) ) {
			$value = get_user_meta( $user[1], 'description', true );
		}
		return $value;
	}

	function pre_update_value( $value, $post_id = false, $field = false ) {
		if ( $field['name'] == 'display_name' ) {
			return $value;
		}

		if ( $user_id = $this->_user_id($post_id) ) {
			if ( !$this->_can_edit($user_id) )
				return null;
			remove_action( 'acf/save_post', '_acf_do_save_post' );

			$description = apply_filters( 'pre_user_description', $value );
			update_user_meta( $user_id, 'description', $description );

			add_action( 'acf/save_post', '_acf_do_save_post' );
		}
		return null;
	}

	function update_value( $value, $post_id = false, $field = false ) {
		return null;
	}

	// function validate_value( $is_valid, $value, $field, $input ) {
	// 	return $is_valid;
	// }

}

// initialize
acf_register_field_type( 'amem_field_user_bio' );

endif;



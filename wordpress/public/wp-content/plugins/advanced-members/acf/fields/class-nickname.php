<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_nickname' ) ) :

class amem_field_nickname extends amem_field_text {

	function initialize() {
		// vars
		$this->name       = 'nickname';
		$this->label      = __( 'Nickname', 'advanced-members' );
		$this->category = 'Advanced Members';
		$this->description = __( 'Nickname of the user. Synced to `nickname` user meta.', 'advanced-members' );
		$this->preview_image = amem_get_url('images/field-type-previews/field-preview-text.png', 'assets');
		$this->defaults   = array(
			'default_value' => '',
			'maxlength'     => '',
			'placeholder'   => '',
			'prepend'       => '',
			'append'        => '',
		);
		add_filter( 'acf/load_field/type=text', array( $this, 'load_nickname_field' ) );
		add_filter( 'acf/update_value/type=' . $this->name, array( $this, 'pre_update_value' ), 9, 3 );

	}

	function load_nickname_field( $field ) {
		if ( ! empty( $field['custom_nickname'] ) ) {
			$field['type'] = 'nickname';
		}
		return $field;
	}

 	function load_value( $value, $post_id = false, $field = false ) {
		if ( $user_id = $this->_user_id($post_id) ) {
			$edit_user = get_user_by( 'ID', $user_id );
			if ( $edit_user instanceof \WP_User ) {
				$value = $edit_user->nickname;
			}
		}
		return $value;
	}

	function pre_update_value( $value, $post_id = false, $field = false ) {
		if ( $field['name'] == 'nickname' ) {
			return $value;
		}

		if ( $user_id = $this->_user_id($post_id) ) {
			if ( !$this->_can_edit($user_id) )
				return null;
			remove_action( 'acf/save_post', '_acf_do_save_post' );
			if ( empty( $value ) ) {
				$user = get_user_by('ID', $user_id);
				if ( $user && !is_wp_error($user) ) {
					$value = $user->user_login;
				}
			}

			$value = apply_filters( 'pre_user_nickname', $vallue );
			update_user_meta( $user_id, 'vallue', $value );

			add_action( 'acf/save_post', '_acf_do_save_post' );
		}
		return null;
	}

	public function prepare_field( $field ) {
		$field['type'] = 'text';
		// return
		return $field;
	}


}

// initialize
acf_register_field_type( 'amem_field_nickname' );

endif;



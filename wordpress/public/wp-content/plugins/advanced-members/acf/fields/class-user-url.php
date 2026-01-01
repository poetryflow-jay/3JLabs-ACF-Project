<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_user_url' ) ) :

class amem_field_user_url extends amem_field_url {

	function initialize() {
		// vars
		$this->name       = 'user_url';
		$this->label      = __( 'Website', 'advanced-members' );
		$this->category = 'Advanced Members';
		$this->description = __( 'Website URL of the user. Synced to `url` user meta.', 'advanced-members' );
		$this->preview_image = amem_get_url('images/field-type-previews/field-preview-url.png', 'assets');
		$this->defaults   = array(
			'default_value' => '',
			'placeholder'   => '',
			'prepend'       => '',
			'append'        => '',
		);
		add_filter( 'acf/update_value/type=' . $this->name, array( $this, 'pre_update_value' ), 9, 3 );

	}

	public function load_value( $value, $post_id = false, $field = false ) {
		if ( $user_id = $this->_user_id($post_id) ) {
			$edit_user = get_user_by( 'ID', $user_id );
			if ( $edit_user instanceof \WP_User ) {
				$value = esc_html( $edit_user->user_url );
			}
		}
		return $value;
	}

	function pre_update_value( $value, $post_id = false, $field = false ) {
		if ( $user_id = $this->_user_id($post_id) ) {
			if ( !$this->_can_edit($user_id) )
				return null;
			remove_action( 'acf/save_post', '_acf_do_save_post' );
			wp_update_user(
				array(
					'ID'       => $user_id,
					'user_url' => esc_attr( $value ),
				)
			);
			add_action( 'acf/save_post', '_acf_do_save_post' );
		}
		return null;
	}

	function render_field( $field ) {
		$field['type'] = 'url';
		parent::render_field( $field );
	}

}

// initialize
acf_register_field_type( 'amem_field_user_url' );

endif;



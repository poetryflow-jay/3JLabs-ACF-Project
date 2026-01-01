<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_display_name' ) ) :


class amem_field_display_name extends amem_field_select {

	protected $_user = null;

	function initialize() {
		// vars
		$this->name     = 'display_name';
		$this->label    = __( 'Display Name', 'advanced-members' );
		$this->category = 'Advanced Members';
		$this->description = __( 'Select the name to display publicly. Synced to `display_name` user data.', 'advanced-members' );
		$this->preview_image = amem_get_url('images/field-type-previews/field-preview-text.png', 'assets');
		$this->defaults = array(
			'multiple'      => 0,
			'allow_null'    => 0,
			'choices'       => array(),
			'default_value' => '',
			'allow_custom'  => 1,
			'ui'            => 1,
			'ajax'          => 0,
			'placeholder'   => __( 'Start typing or choose one of the options', 'advanced-members' ),
			'return_format' => 'value',
		);

		add_filter( 'acf/load_field/type=text', array( $this, 'load_display_name_field' ) );
		add_filter( 'acf/update_value/type=' . $this->name, array( $this, 'pre_update_value' ), 9, 3 );
	}

	function load_display_name_field( $field ) {
		if ( ! empty( $field['custom_display_name'] ) ) {
			$field['type'] = 'display_name';
		}
		return $field;
	}

	function load_value( $value, $post_id = false, $field = false ) {
		if ( $user_id = $this->_user_id($post_id) ) {
			$edit_user = get_user_by( 'ID', $user_id );
			if ( $edit_user instanceof \WP_User ) {
				$value = $edit_user->display_name;
				$this->_user = $edit_user;
			}
		}
		return $value;
	}

	function prepare_field( $field ) {
		if ( amem_user('ID') || $this->_user ) {
			if ( $this->_user ) {
				amem()->switch($this->_user->ID);
			}

			$user_id = amem_user('ID');

			if ( ! empty( $user_id ) ) {
				if ( amem_user('user_login') ) {
					$choices          = array(
						amem_user('user_login'),
						amem_user('user_email'),
						amem_user('first_name'),
						amem_user('last_name'),
						amem_user('first_name') . ' ' . amem_user('last_name'),
						amem_user('nickname'),
					);
					$field['choices'] = array();
					foreach ( $choices as $choice ) {
						$choice = trim($choice);
						if ( $choice ) {
							$field['choices'][ $choice ] = $choice;
						}
					}
				}
			}
		}

		// Allow Custom
		if ( acf_maybe_get( $field, 'allow_custom' ) ) {

			if ( $value = acf_maybe_get( $field, 'value' ) ) {
				$value = acf_get_array( $value );
				foreach ( $value as $v ) {
					if ( isset( $field['choices'][ $v ] ) ) {
						 continue;
					}
					$field['choices'][ $v ] = $v;
				}
			}

			if ( empty( $field['wrapper'] ) ) {
				$field['wrapper'] = array();
			}

			$field['wrapper']['data-allow-custom'] = 1;
		}

		if ( ! acf_maybe_get( $field, 'ajax' ) ) {
			if ( is_array( $field['choices'] ) ) {
				$found       = false;
				$found_array = array();
				foreach ( $field['choices'] as $k => $choice ) {
					if ( is_string( $choice ) ) {
						$choice = trim( $choice );
						if ( strpos( $choice, '##' ) === 0 ) {
							$choice = substr( $choice, 2 );
							$choice = trim( $choice );

							$found = $choice;
							$found_array[ $choice ] = array();
						} elseif ( ! empty( $found ) ) {
							$found_array[ $found ][ $k ] = $choice;
						}
					}
				}

				if ( ! empty( $found_array ) ) {
					$field['choices'] = $found_array;
				}
			}
		}
		if ( $this->_user ) {
			amem()->user->restore();
			$this->_user = null;
		}

		return $field;

	}

	function pre_update_value( $value, $post_id = false, $field = false ) {
		if ( $user_id = $this->_user_id($post_id) ) {
			if ( !$this->_can_edit($user_id) )
				return null;
			remove_action( 'acf/save_post', '_acf_do_save_post' );
			wp_update_user(
				array(
					'ID' => $user_id,
					'display_name' => trim($value),
				)
			);
			add_action( 'acf/save_post', '_acf_do_save_post' );
		}

		return null;
	}


}

// initialize
acf_register_field_type( 'amem_field_display_name' );

endif; // class_exists check



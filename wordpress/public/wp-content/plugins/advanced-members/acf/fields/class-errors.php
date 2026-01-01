<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_errors' ) ) :

	class amem_field_errors extends acf_field {
		use amem_field;

		function initialize() {
			// vars
			$this->name       = 'field_amem_errors';
			$this->label      = __( 'AMem Errors (Internal)', 'advanced-members' );
			$this->category = 'Advanced Members';
			$this->public = false;
			$this->defaults   = array(
				'required' => false,
				'readonly' => true,
			);

			add_filter( 'acf/update_value/type=' . $this->name, array( $this, 'pre_update_value' ), 9, 3 );
		}

		function load_value( $value, $post_id = false, $field = false ) {
			return '';
		}

		function validate_value( $is_valid, $value, $field, $input ) {
			return $is_valid;
		}

		function pre_update_value( $value, $post_id = false, $field = false ) {
			return null;
		}

		function update_value( $value, $post_id = false, $field = false ) {
			return null;
		}

		// function render_field( $field ) {
		// 	$field['type'] = $field['field_type'];
		// 	acf_render_field( $field );
		// }

		function render_field( $field ) {
			$html = '';

			// // Prepend text.
			// if ( $field['prepend'] !== '' ) {
			// 	$field['class'] .= ' acf-is-prepended
			// 	$html           .= '<div class="acf-input-prepend">' . acf_esc_html( $field['prepend'] ) . '</div>';
			// }

			// // Append text.
			// if ( $field['append'] !== '' ) {
			// 	$field['class'] .= ' acf-is-appended';
			// 	$html           .= '<div class="acf-input-append">' . acf_esc_html( $field['append'] ) . '</div>';
			// }

			// Input.
			$input_attrs = array();
			foreach ( array( 'type', 'id', 'class', 'name', 'value', 'placeholder', 'maxlength', 'pattern', 'readonly', 'disabled', 'required' ) as $k ) {
				if ( isset( $field[ $k ] ) ) {
					$input_attrs[ $k ] = $field[ $k ];
				}
			}

			if ( isset( $field['input-data'] ) && is_array( $field['input-data'] ) ) {
				foreach ( $field['input-data'] as $name => $attr ) {
					$input_attrs[ 'data-' . $name ] = $attr;
				}
			}

			echo '<div class="acf-input-wrap">' . acf_get_hidden_input( acf_filter_attrs( $input_attrs ) ) . '</div>';

			// Display.
			// echo $html;
		}

	}

// initialize
acf_register_field_type( 'amem_field_errors' );

endif;



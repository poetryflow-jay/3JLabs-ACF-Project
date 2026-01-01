<?php
/**
 * Render Form
 *
 * @since 1.0
 * @package Advanced Members for ACF \ Forms
 * 
 */
namespace AMem\Forms;
use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class Render extends Module {

	function __construct() {
		// add_shortcode( 'amemembers', array( $this, 'form_shortcode' ) );
		add_shortcode( 'advanced-members', array( $this, 'form_shortcode' ) );
		// add_shortcode( 'acf_members', array( $this, 'form_shortcode' ) );
		add_action( 'amem/form/render', array( $this, 'render' ), 10, 2 );

		add_filter( 'wp_kses_allowed_html', array( $this, 'allowed_html_tags' ), 10, 2 );
	}

	/**
	 * Registers the shortcode advanced_form which renders the form specified by the "form" attribute
	 *
	 * @since 1.0
	 */
	function form_shortcode( $atts ) {
		if ( isset( $atts['form'] ) ) {

			$form_id_or_key = $atts['form'];
			unset( $atts['form'] );

			ob_start();

			$this->render( $form_id_or_key, $atts );

			$output = ob_get_clean();

			return $output;
		}
	}

	function allowed_html_tags( $tags, $context ) {
		global $allowedposttags, $allowedamemtags;
		if ( $context != 'amem' )
			return $tags;

		if ( is_array( $allowedamemtags) )
			return $allowedamemtags;

		$allowedamemtags = $allowedposttags;
		$allowedamemtags['form'] = array(
			'action'         => true,
			'accept'         => true,
			'accept-charset' => true,
			'enctype'        => true,
			'method'         => true,
			'name'           => true,
			'target'         => true,
		);

		$atts = array(
      'type' => true,
      'class' => true,
      'id' => true,
      'data-*' => true,
      'aria-*' => true,
      'name' => true,
      'value' => true,
      'checked' => true,
      'disabled' => true,
      'value' => true,
      'multiple' => true,
    );

		$allowedamemtags['input'] = $atts;
		$allowedamemtags['select'] = $atts;
		$allowedamemtags['option'] = $atts;

		return $allowedamemtags;
	}

	/**
	 * Renders the form specified by ID
	 *
	 * @since 1.0
	 */
	function render( $form_id_or_key, $args ) {
		$form = amem_get_form( $form_id_or_key );

		if ( ! $form || !$form['active'] ) {
			return;
		}

		// Set global AMem Form
		amem()->set_form($form, $args);
		amem()->set_amem(true);

		// Enqueue scripts and styles
		amem_enqueue();

		do_action( 'amem/form/enqueue', $form, $args );
		do_action( 'amem/form/enqueue/type=' . $form['type'], $form, $args );

		// Allow the form to be modified before rendering form
		$form = apply_filters( 'amem/form/before_render', $form, $args );
		$form = apply_filters( 'amem/form/before_render/type=' . $form['type'], $form, $args );

		$args = wp_parse_args( $args, array(
			'display_title' => false,
			'display_description' => false,
			'id' => $form['key'],
			'values' => array(),
			'submit_text' => $form['data']['submit_text'],//__( 'Submit', 'advanced-members' ),
			'redirect' => null,
			'target' => amem_get_current_url(),
			'echo' => true,
			'exclude_fields' => array(),
			'uploader' => 'wp',
			'filter_mode' => false,
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'honeypot' => true,
		) );

		// Allow the arguments to be modified before rendering form
		$args = apply_filters( 'amem/form/args', $args, $form );
		$args = apply_filters( 'amem/form/args/type=' . $form['type'], $args, $form );

		if ( $args['redirect'] === 'current' )
			$args['redirect'] = amem_get_current_url();

		// Allow a comma separated string for excluded fields
		if ( is_string( $args['exclude_fields'] ) ) {
			$args['exclude_fields'] = explode( ',', $args['exclude_fields'] );
		}

		// Set ACF uploader type setting
		acf_update_setting( 'uploader', $args['uploader'] );

		// Form element
		$form_attributes = array(
			'class' => 'amem-form acf-form amem-form-type-' . $form['type'],
			'method' => 'POST',
			'action' => $args['target'],
			'id' => $args['id'],
			'data-key' => $form['key'],
		);

		$form_ajax = isset($args['ajax']) ? $args['ajax'] : $form['data']['ajax'];
		// Add data attribute to indicate that the form should use AJAX submissions
		if ( $form_ajax ) {
			$form_attributes['data-ajax'] = true;
		}

		$form_attributes = apply_filters( 'amem/form/attributes', $form_attributes, $form, $args );
		$form_attributes = apply_filters( 'amem/form/attributes/type=' . $form['type'], $form_attributes, $form, $args );

		echo sprintf( '<form %s>', acf_esc_atts( $form_attributes ) );

		do_action( 'amem/form/before_title', $form, $args );
		do_action( 'amem/form/before_title/type=' . $form['type'], $form, $args );

		// Render submission error message if one exists
		$this->render_submission_error( $form, $args );

		// Render title and description if they should be visible
		$this->render_title_and_description( $form, $args );

		/**
		 * Check if form should be restricted and not displayed.
		 * Filter will return false if no restriction is applied otherwise it will return a string to display.
		 */
		$restriction = false;
		$restriction = apply_filters( 'amem/form/restriction', $restriction, $form, $args );
		$restriction = apply_filters( 'amem/form/restriction/type=' . $form['type'], $restriction, $form, $args );

		// Display success message, restriction message, or fields
		$instance_hash = amem_form_instance_hash( $form['key'], $args );
		$show_success = amem_has_submission( $instance_hash ) && ! amem_submission_failed( $form['key'] );
		$just_success_message = $form['data']['after_submit'] == 'just_success_message';
		$force_success_message = !empty($form['data']['force_show_message']);

		if ( $show_success && $just_success_message && ! $args['filter_mode'] ) {

			amem_form_success_message( $form, $args, true );

		} elseif ( $restriction ) {

			$this->render_restriction_message( $restriction );

		} else {
			if ( $show_success || $amem_form_success_message ) {
				amem_form_success_message( $form, $args, true );
			}
			// Updated message
			amem_form_updated_message( $form, $args, true );
			// Error message (maybe useless...)
			amem_form_error_message( $form, $args, true );

			$this->render_fields( $form, $args );

		}

		// End form
		echo '</form>';

		do_action( 'amem/form/after_render', $form, $args );
		do_action( 'amem/form/after_render/type=' . $form['type'], $form, $args );

		amem()->set_amem(false);// End of AMem Form
	}

	/**
	 * Renders all submission errors if any exist.
	 *
	 * @since 1.0
	 */
	function render_submission_error( $form, $args ) {
		if ( amem_submission_failed( $form['key'] ) ) {
			// $errors = amem()->submission['errors'];
			$errors = amem()->errors->get_errors();

			foreach ( $errors as $error ) {
				echo '<div class="acf-notice -error -acf-error-message-dismiss">';
				echo sprintf( '<p>%s</p>', esc_html($error) );
				echo '</div>';
			}
		}
	}

	/**
	 * Renders title and description of form if they should be shown.
	 *
	 * @since 1.0
	 */
	function render_title_and_description( $form, $args ) {
		// Display title
		if ( $args['display_title'] ) {
			echo sprintf( '<h1 class="amem-title">%s</h1>', esc_html($form['title']) );
		}

		// Display description
		if ( $args['display_description'] ) {
			echo sprintf( '<div class="amem-description">%s</div>', esc_html($form['data']['description']) );
		}
	}

	/**
	 * Renders the restriction message for a form.
	 *
	 * @since 1.0
	 */
	function render_restriction_message( $message ) {
		echo '<div class="acf-notice -warning amem-notice amem-restricted-message">';
		echo amem_format_message( $message, true );// message text escaped by amem_format_message()
		echo '</div>';
	}

	/**
	 * Renders a field wrapper with all fields and a submit button.
	 *
	 * @since 1.0
	 */
	function render_fields( $form, $args ) {
		// Get field groups for the form and display their fields
		$field_groups = amem_get_form_field_groups( $form['key'] );

		do_action( 'amem/form/before_field_wrapper', $form, $args );
		do_action( 'amem/form/before_field_wrapper/type=' . $form['type'], $form, $args );

		echo sprintf( '<div class="amem-fields acf-fields acf-form-fields -%s">', esc_attr($args['label_placement']) );

		do_action( 'amem/form/before_fields', $form, $args );
		do_action( 'amem/form/before_fields/type=' . $form['type'], $form, $args );

		// Form data required by ACF for validation to work.
		$acf_form_data = array(
			'screen' => 'acf_form',
			'post_id' => false,
			'form' => false,
			'user' => 0,
			'ajax' => !empty($args['ajax']),
		);

		$acf_form_data = apply_filters( 'amem/form/acf_data', $acf_form_data, $form, $args );
		$acf_form_data = apply_filters( 'amem/form/acf_data/type=' . $form['type'], $acf_form_data, $form, $args );

		acf_form_data( $acf_form_data );

		// Hidden fields to identify form
		echo '<div class="acf-hidden">';

		amem_nonce_input();

		echo sprintf( '<input type="hidden" name="amem_form" value="%s">', esc_attr($form['key']) );

		$encoded_args = amem_encrypt( $args );
		echo sprintf( '<input type="hidden" name="amem_form_args" value="%s">', esc_attr($encoded_args) );
		echo sprintf( '<input type="hidden" name="_acf_form" value="%s">', esc_attr($encoded_args) );

		// Add nonce to ensure arguments can't be altered.
		$hashed_args = hash( 'sha256', $encoded_args );
		$nonce = wp_create_nonce( sprintf( 'amem_submission_%s_%s', esc_attr($form['key']), esc_attr($hashed_args) ) );
		echo sprintf( '<input type="hidden" name="amem_form_nonce" value="%s">', esc_attr($nonce) );

		// Add honeypot field that is not visible to users.
		// Bots should hopefully fill this in allowing them to be detected.
		if ( $args['honeypot'] ) {
			// echo sprintf( '<label for="acmf_validate_email_%s" aria-hidden="true">Email for non-humans</label>', $form['key'] );
			echo sprintf( '<input type="text" name="_acmf_validate_email" id="acmf_validate_email_%s" tabindex="-1" autocomplete="off" class="required" />', esc_attr($form['key']) );
		}

		// Add origin URL to enable an automatic redirect back to the form page after submission.
		echo sprintf( '<input type="hidden" name="amem_origin_url" value="%s" />', esc_attr( amem_get_current_url() ) );

		do_action( 'amem/form/hidden_fields', $form, $args );
		do_action( 'amem/form/hidden_fields/type=' . $form['type'], $form, $args );

		echo '</div>';

		// Print error field
		// @todo Prepare for multiple form in page
		$err_group = acf_get_local_field_group('group_amem_errors');
		$this->render_field_group( $err_group, $form, $args );

		do_action( 'amem/form/local_fields', $form, $args );
		do_action( 'amem/form/local_fields/type=' . $form['type'], $form, $args );

		foreach ( $field_groups as $field_group ) {
			$this->render_field_group( $field_group, $form, $args );
		}

		do_action( 'amem/form/after_fields', $form, $args );
		do_action( 'amem/form/after_fields/type=' . $form['type'], $form, $args );

		$this->render_submit_button( $form, $args );

		// End fields wrapper
		echo '</div>';

		do_action( 'amem/form/after_field_wrapper', $form, $args );
		do_action( 'amem/form/after_field_wrapper/type=' . $form['type'], $form, $args );
	}

	/**
	 * Renders a full field group with all fields that are not excluded.
	 *
	 * Powered by 'acf_render_fields()'
	 *
	 * @since 1.0
	 */
	function render_field_group( $field_group, $form, $args ) {
		do_action( 'amem/field_group/before_field_group', $field_group, $form, $args );

		// Get all fields for field group
		if ( ! $fields = amem()->local->get_group_fields($field_group) ) {
			$fields = acf_get_fields( $field_group );			
		}

		$post_id = 0;
		if ( !empty($args['user']) && is_numeric($args['user']) )
			$post_id = 'user_' . $args['user'];

		$fields = apply_filters( 'acf/pre_render_fields', $fields, $post_id );

		// Filter our false results.
		$fields = array_filter( $fields );

		foreach ( $fields as $field ) {

			// Skip field if it is in the exluded fields argument
			if ( isset( $args['exclude_fields'] ) && is_array( $args['exclude_fields'] ) ) {
				if ( empty($field['_amem_local']) ) {
					if ( in_array( $field['key'], $args['exclude_fields'] ) || in_array( $field['name'], $args['exclude_fields'] ) || in_array( $field['type'], $args['exclude_fields'] ) ) {
						continue;
					}
				}
			}

			$field = apply_filters( 'acf/pre_render_field', $field, $post_id );

			// Load value if not already loaded.
			if ( $field['value'] === null ) {
				$field['value'] = acf_get_value( $post_id, $field );
			}

			$this->render_field( $field, $form, $args );
		}

		do_action( 'amem/field_group/after_field_group', $field_group, $form, $args );
	}

	function get_label( $field ) {
		if ( function_exists( 'acf_get_field_label' ) )
			return acf_get_field_label( $field );

		$label = esc_html($field['label']);

		$label .= $field['required'] ? ' <span class="acf-required">*</span>' : '';

		$label = apply_filters( 'acf/get_field_label', $label, $field, $context );

		return $label;
	}

	/**
	 * Renders a single field as part of a form.
	 *
	 * @since 1.0
	 */
	function render_field( $field, $form, $args ) {
		// Ignore hide from admin value
		$field['hide_admin'] = false;

		// Include default value
		if ( empty( $field['value'] ) && isset( $field['default_value'] ) ) {
			$field['value'] = $field['default_value'];
		}

		// Include pre-fill values (either through args or filter)
		if ( isset( $args['values'][ $field['name'] ] ) ) {
			$field['value'] = $args['values'][ $field['name'] ];
		}

		if ( isset( $args['values'][ $field['key'] ] ) ) {
			$field['value'] = $args['values'][ $field['key'] ];
		}

		// $field['value'] = apply_filters( 'amem/field/prefill_value', $field['value'], $field, $form, $args );
		// $field['value'] = apply_filters( 'amem/field/prefill_value/type=' . $field['type'], $field['value'], $field, $form, $args );
		// $field['value'] = apply_filters( 'amem/field/prefill_value/name=' . $field['name'], $field['value'], $field, $form, $args );
		// $field['value'] = apply_filters( 'amem/field/prefill_value/key=' . $field['key'], $field['value'], $field, $form, $args );

		// Include any previously submitted value
		if ( isset( $_POST['acf'][ $field['key'] ] ) ) { // phpcs:disable WordPress.Security.NonceVerification
			$field['value'] = amem_sanitize_input($_POST['acf'][ $field['key'] ]); // phpcs:disable WordPress.Security.NonceVerification
		}

		// If the previous submission failed or filter mode is enabled, the submitted field values should be reused
		$instance_hash = amem_form_instance_hash( $form['key'], $args );
		if ( amem_has_submission( $instance_hash ) && ( $args['filter_mode'] || amem_submission_failed( $form['key'] ) ) ) {
			$field['value'] = amem_get_field( $field['name'] );
		}

		$field = apply_filters( 'amem/field/before_render', $field, $form, $args );
		$field = apply_filters( 'amem/field/before_render/type=' . $field['type'], $field, $form, $args );
		$field = apply_filters( 'amem/field/before_render/name=' . $field['name'], $field, $form, $args );
		$field = apply_filters( 'amem/field/before_render/key=' . $field['key'], $field, $form, $args );

		// Ensure field is complete (adds all settings).
		$field = acf_validate_field( $field );

		// Prepare field for input (modifies settings).
		$field = acf_prepare_field( $field );

		// Allow "amem/field/before_render" to remove a field by returning false
		if ( ! $field ) {
			return;
		}

		// Attributes to be used on the wrapper element
		$attributes = array();

		if ( ! empty( $field['wrapper']['id'] ) ) {
			$attributes['id'] = $field['wrapper']['id'];
		}

		$attributes['class'] = $field['wrapper']['class'];

		$attributes['class'] .= sprintf( ' amem-field amem-field-type-%s amem-field-%s acf-field acf-field-%s acf-field-%s', $field['type'], $field['name'], $field['type'], $field['key'] );

		if ( $field['required'] ) {
			$attributes['class'] .= ' amem-field-required';
		}

		// This is something ACF needs
		$attributes['class'] = str_replace( '_', '-', $attributes['class'] );
		$attributes['class'] = str_replace( 'field-field-', 'field-', $attributes['class'] );

		$width = $field['wrapper']['width'];

		if ( $width ) {
			$attributes['data-width'] = $width;
			$attributes['style'] = 'width: ' . $width . '%;';
		}

		$attributes['data-name'] = $field['name'];
		$attributes['data-key'] = $field['key'];
		$attributes['data-type'] = $field['type'];

		if ( ! empty( $field['conditional_logic'] ) ) {
			$field['conditions'] = $field['conditional_logic'];
		}

		if ( ! empty( $field['conditions'] ) ) {
			$attributes['data-conditions'] = $field['conditions'];
		}

		$attributes = apply_filters( 'amem/field/attributes', $attributes, $field, $form, $args );
		$attributes = apply_filters( 'amem/field/attributes/type=' . $field['type'], $attributes, $field, $form, $args );
		$attributes = apply_filters( 'amem/field/attributes/name=' . $field['name'], $attributes, $field, $form, $args );
		$attributes = apply_filters( 'amem/field/attributes/key=' . $field['key'], $attributes, $field, $form, $args );

		// Field instructions
		$instruction_placement = $args['instruction_placement'];
		$instruction_placement = apply_filters( 'amem/field/instruction_placement', $instruction_placement, $field, $form, $args );
		$instruction_placement = apply_filters( 'amem/field/instruction_placement/type=' . $field['type'], $instruction_placement, $field, $form, $args );
		$instruction_placement = apply_filters( 'amem/field/instruction_placement/name=' . $field['name'], $instruction_placement, $field, $form, $args );
		$instruction_placement = apply_filters( 'amem/field/instruction_placement/key=' . $field['key'], $instruction_placement, $field, $form, $args );

		if ( ! empty( $field['instructions'] ) ) {
			$instructions = sprintf( '<p class="amem-field-instructions -placement-%s">%s</p>', esc_attr($instruction_placement), wp_kses_post($field['instructions']) );
		} else {
			$instructions = '';
		}

		do_action( 'amem/field/before_field', $field, $form, $args );
		do_action( 'amem/field/before_field/type=' . $field['type'], $field, $form, $args );
		do_action( 'amem/field/before_field/name=' . $field['name'], $field, $form, $args );
		do_action( 'amem/field/before_field/key=' . $field['key'], $field, $form, $args );

		// Field wrapper
		echo sprintf( '<div %s>', acf_esc_atts( $attributes ) );

		echo '<div class="amem-label acf-label">';

		$label = $this->get_label( $field );

		echo sprintf( '<label for="acf-%s">%s</label>', esc_attr($field['key']), $label );

		if ( 'label' == $instruction_placement ) {
			echo $instructions;
		}

		echo '</div>';

		echo '<div class="amem-input acf-input">';

		// Render field with default ACF
		acf_render_field( $field );

		echo '</div>';

		if ( 'field' == $instruction_placement ) {
			echo $instructions;
		}

		// End field wrapper
		echo '</div>';

		do_action( 'amem/field/after_field', $field, $form, $args );
		do_action( 'amem/field/after_field/name=' . $field['name'], $field, $form, $args );
		do_action( 'amem/field/after_field/key=' . $field['key'], $field, $form, $args );
	}

	function render_submit_button( $form, $args ) {
		// Submit button and loading indicator
		$button_attributes = array();

		$button_attributes['class'] = 'acf-btn amem-btn';
		if ( !empty($args['submit_btn_atts']['class']) ) {
			$button_attributes['class'] .= ' ' . $args['submit_btn_atts']['class'];
			unset( $args['submit_btn_atts']['class'] );
		}

		if ( isset( $args['submit_btn_atts'] ) ) {
			$button_attributes = wp_parse_args( $args['submit_btn_atts'], $button_attributes );
		}

		$button_attributes = apply_filters( 'amem/form/button_attributes', $button_attributes, $form, $args );
		$button_attributes = apply_filters( 'amem/form/button_attributes/type=' . $form['type'], $button_attributes, $form, $args );

		$submit_button = apply_filters( 'amem/form/button_html', '', $button_attributes, $form, $args );
		$submit_button = apply_filters( 'amem/form/button_html/type=' . $form['type'], $submit_button, $button_attributes, $form, $args );

		if ( !empty($submit_button) ) {
			echo wp_kses($submit_button, 'amem');
			return;
		}

		if ( empty($args['submit_text']) ) {
			$args['submit_text'] = amem_submit_text_default( $form['post_id'] );
		}
		echo '<div class="amem-submit acf-form-submit">';
		echo sprintf( '<button type="submit" %s>%s</button>', acf_esc_atts( $button_attributes ), esc_html($args['submit_text']) );
		echo '<span class="acf-spinner amem-spinner"></span>';
		echo '</div>';
	}

}

amem()->register_module('render', Render::getInstance());

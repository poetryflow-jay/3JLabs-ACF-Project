<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

/**
 * The main function used to output a form
 *
 * @since 1.0
 */
function amem_form( $form_id, $args = array() ) {
	// Render form and catch output
	ob_start();
	do_action( 'amem/form/render', $form_id, $args );
	$output = ob_get_clean();

	if ( ! isset( $args['echo'] ) || $args['echo'] ) {
		echo $output;
	}

	return $output;
}

/**
 * Helper function to extract a specific field value from submitted fields
 *
 * @since 1.0
 */
function amem_get_field( $field_key_or_name, $fields = false ) {
	// Get fields from the global submission object if fields weren't passed
	if ( ! $fields && amem_has_submission() ) {
		$fields = amem()->submission['fields'];
	}

	foreach ( $fields as $field ) {
		if ( $field['key'] == $field_key_or_name || $field['name'] == $field_key_or_name ) {
			return $field['value'];
		}

		// Also search sub fields
		if ( isset( $field['sub_fields'] ) && is_array( $field['value'] ) ) {
			foreach ( $field['value'] as $sub_field_name => $sub_field_value ) {
				if ( $sub_field_name == $field_key_or_name ) {
					return $sub_field_value;
				}
			}
		}
	}

	return false;
}

/**
 * Save submitted field directly to post
 *
 * @since 1.0
 * @deprecated 1.3.0 Use amem_save_field()
 * @see amem_save_field()
 */
function amem_save_field_to_post( $field_key_or_name, $post_id ) {
	_deprecated_function( __FUNCTION__, '1.3.0', 'amem_save_field()' );
	amem_save_field( $field_key_or_name, $post_id );
}

/**
 * Save submitted field directly to an object (post, user, term) with ACF naming
 *
 * @since 1.0
 */
function amem_save_field( $field_key_or_name, $object_id ) {
	// Make sure we have a submission to work with
	if ( ! amem_has_submission() ) {
		return false;
	}

	$field = amem_get_field_object( $field_key_or_name );

	/**
	 * We save the field directly to the post using acf_update_value.
	 * This ensures that clone fields, repeaters etc. work as intended.
	 * $field['_input'] should match the raw $_POST value.
	 */
	if ( $field ) {
		$value = $field['_input'];
		acf_update_value( $value, $object_id, $field );
		return true;
	}

	return false;
}

/**
 * Save all submitted fields directly to an object (post, user, term) with ACF naming
 *
 * @since 1.0
 */
function amem_save_all_fields( $object_id, $excluded_fields = array() ) {
	// Make sure we have a submission to work with
	if ( ! amem_has_submission() ) {
		return false;
	}

	$fields = amem()->submission['fields'];

	// $excluded_fields = array_map( function($field) { return is_array($field) ? $field['name'] : $field; }, (array)$excluded_fields ); 
	$excluded_fields = [];
	$excluded_fields = array_merge( $excluded_fields, ['field_amem_errors', '_amem_errors', 'field_amem_updated', '_amem_updated'] );

	/**
	 * We save the fields directly to the user using acf_update_value.
	 * This ensures that clone fields, repeaters etc. work as intended.
	 * $field['_input'] should match the raw $_POST value.
	 */
	foreach ( $fields as $field ) {
		if ( in_array( $field['name'], $excluded_fields ) ) {
			continue;
		}

		$value = $field['_input'];
		acf_update_value( $value, $object_id, $field );
	}

	do_action( 'amem/save_all_fields', $fields, $object_id, $excluded_fields );

	return true;
}

/**
 * Helper function to extract a full field object from submitted fields
 *
 * @since 1.0
 */
function amem_get_field_object( $field_key_or_name, $fields = [] ) {
	if ( $field = acf_get_local_field( $field_key_or_name) ) {
		return acf_get_field($field_key_or_name);
	}

	// Get fields from the global submission object if fields weren't passed
	if ( ! $fields && amem_has_submission() ) {
		$fields = amem()->submission['fields'];
	}

	foreach ( $fields as $field ) {
		// Save submitted value to post using ACFs acf_update_value
		if ( $field['key'] == $field_key_or_name || $field['name'] == $field_key_or_name ) {
			return $field;
		}
	}

	return false;
}

/**
 * Get type of given form
 *
 * @param  number $form_id
 * @return string
 */
function amem_form_type( $form_id=null ) {
	static $formTypes = [];

	if ( is_array($form_id) && !empty($form_id['post_id']) )
		$form_id = $form_id['post_id'];

	if ( isset($formTypes[$form_id]) )
		return $formTypes[$form_id];

	if ( !is_numeric($form_id) ) {
		$form_id = amem_form_post_id_from_key($form_id);
	}

	if ( !$form_id ) {
		$formTypes[$form_id] = false;
		return false;
	}

	// $core_types = ['registration', 'login', 'account', 'passwordreset'];
	// $valid_types = apply_filters( 'amem/form/types', [] );
	// // Make sure built-in types are not unset
	// $valid_types = array_merge((array)$valid_types, $core_types);
	$valid_types = array_keys( amem_form_types('all') );

	$type = get_post_meta( $form_id, 'select_type', true );

	if ( !in_array($type, $valid_types) || !$type ) {
		$formTypes[$form_id] = false;
		return false;
	}

	$formTypes[$form_id] = $type;

	return $type;
}

function amem_form_types( $type='core' ) {
	$types = [];
	if ( $type == 'core' || $type == 'all' ) {
		$core = [
			'registration'	=> __( 'Registration', 'advanced-members' ),
			'login' 				=> __( 'Login', 'advanced-members' ),
			'account'				=> __( 'Account', 'advanced-members' ),
		];
		$core = apply_filters( 'amem/form_types/core', $core );
		$types = array_merge( $types, $core );
	}

	if ( $type == 'local' || $type == 'all' ) {
		$local = [
			'passwordreset' => __( 'Password Reset', 'advanced-members' ),
		];
		$local = apply_filters( 'amem/form_types/local', $local );
		$types = array_merge( $types, $local );
	}

	return $types;
}

/**
 * Used to register a form programmatically
 *
 * @since 1.0
 */
function amem_register_form( $form ) {
	$form = amem_get_valid_form( $form );

	if ( $form ) {
		amem()->local->add_form($form);
	}

	return $form;
}

/**
 * Checks if the passed key is a valid form key (begins with form_)
 *
 * @since 1.0
 */
function amem_is_valid_form_key( $key ) {
	if ( ! is_string( $key ) ) {
		return false;
	}

	if ( 'form_' == substr( $key, 0, 5 ) ) {
		return true;
	}

	return false;
}

/**
 * Validates and fills a form array with default values
 *
 * @since 1.0
 */
function amem_get_valid_form( $form ) {
	// A form key is always required
	if ( ! isset( $form['key'] ) ) {
		return false;
	}

	$args = array(
		'key' => '',
		'post_id' => false,
		'id' => false,
		'title' => '',
		'type' => false,
		'active' => true,
		'data' => array(
			'ajax' => amem()->options->get('ajax_submit'),
			'user' => 0,
			'description' => '',
			'success_message' => '',
			'after_submit' => '',
			'redirect_url' => '',
		),
	);

	$args = apply_filters( 'amem/form/valid_form', $args );

	return wp_parse_args( $form, $args );
}

function amem_form_from_local( $form ) {
	$form = amem_get_valid_form( $form );
	if ( !isset($form['active']) ) {
		$form['active'] =  true;
	}
	$form = amem_default_form_redirects($form);

	$form = apply_filters( 'amem/form/from_local', $form );
	$form = apply_filters( 'amem/form/from_local/type=' . $form['type'], $form );

	return $form;
}

function amem_default_form_redirects($form) {
	$form_type = $form['type'];//is_array($form) ? $form['type'] : amem_form_type( $form );

	$after_submit = $redirect_url = '';

	switch ( $form_type ) {
		case 'login':
		case 'registration':
		$after_submit = 'redirect_home';
		$redirect_url = '';
		break;
		case 'account':
		$after_submit = 'refresh';
		if ( !empty($form['data']['tab']) && $form['data']['tab'] == 'delete' )
			$after_submit = 'redirect_home';
		$redirect_url = '';
		break;
		default:
		$after_submit = 'redirect_home';
		$redirect_url = '';
		break;
	}

	$_redirects = compact( 'after_submit', 'redirect_url' );
	$_redirects = apply_filters( 'amem/form/redirects', $_redirects, $form_type, $form['post_id'] );
	$_redirects = apply_filters( 'amem/form/redirects/' . $form_type, $_redirects, $form['post_id'] );

	$form['data']['after_submit']  = $_redirects['after_submit'];
	$form['data']['redirect_url']  = $_redirects['redirect_url'];

	return $form;
}

/**
 * Generates a form array from a form post object
 *
 * @since 1.0
 */
function amem_form_from_post( $form_post ) {
	// Get post object if ID has been passed
	if ( is_numeric( $form_post ) ) {
		$form_post = get_post( $form_post );
	}

	// Make sure we have a post and that it's a form
	if ( ! $form_post || 'amem-form' != $form_post->post_type ) {
		return false;
	}

	$form_type = amem_form_type( $form_post->ID );
	$form_ajax = amem()->options->get('ajax_submit');

	if ( get_post_meta( $form_post->ID, 'ajax_override', true ) ) {
		$form_ajax = get_post_meta( $form_post->ID, 'ajax', true );
	}

	$form = amem_get_valid_form( array(
		'ID' => $form_post->ID,
		'post_id' => $form_post->ID,
		'title' => $form_post->post_title,
		'key' => get_post_meta( $form_post->ID, 'form_key', true ),
		'type' => $form_type,
		'active' => in_array( $form_post->post_status, array( 'publish'/*, 'auto-draft'*/ ) ),
		'data' => array(
			'ajax' => $form_ajax,
			'description' => '',//get_post_meta( $form_post->ID, 'description', true ),
			'submit_text' => get_post_meta( $form_post->ID, 'submit_text', true ),
			'success_message' => get_post_meta( $form_post->ID, "{$form_type}_show_message", true ),
			'after_submit' => '',
			'redirect_url' => '',
		),
	) );

	$form = amem_default_form_redirects($form);

	$form = apply_filters( 'amem/form/from_post', $form, $form_post );
	$form = apply_filters( 'amem/form/from_post/type=' . $form['type'], $form, $form_post );

	if ( empty($form['data']['submit_text']) ) {
		$form['data']['submit_text'] = amem_submit_text_default($form['ID']);
	}

	return $form;
}

/**
 * Save a form array to a form post.
 *
 * @since 1.0
 */
function amem_form_to_post( $form, $post ) {
	// Get post object if ID has been passed
	if ( is_numeric( $post ) ) {
		$post = get_post( $post );
	}

	wp_update_post( array(
		'ID' => $post->ID,
		'post_title' => $form['title'],
	) );

	$form = amem_get_valid_form( $form );

	update_post_meta( $post->ID, 'form_key', $form['key'] );

	update_field( 'field_form_description', $form['data']['description'], $post->ID );
	update_field( 'field_form_success_message', $form['data']['success_message'], $post->ID );

	$form = do_action( 'amem/form/to_post', $form, $post );

	return $post;
}

/**
 * Retrieves a form either
 *
 * @since 1.0
 */
function amem_form_from_key( $key ) {
	if ( $form = amem()->local->get_form($key) ) {
		return $form;
	}

	// Form not a registered one, search posts by key meta
	$post = amem_form_post_from_key( $key );
	if ( $post ) {
		return amem_form_from_post( $post );
	}

	return false;
}

/**
 * Retrieves a form post by key if one exists.
 *
 * @since 1.0
 */
function amem_form_post_from_key( $key ) {
	$args = array(
		'post_type' => 'amem-form',
		'posts_per_page' => '1',
		'meta_query' => array(
			array(
				'key' => 'form_key',
				'value' => $key,
			),
		),
	);

	$form_query = new WP_Query( $args );

	if ( $form_query->have_posts() ) {
		return $form_query->posts[0];
	}

	return false;
}

/**
 * Get form post ID from key
 *
 * @param  string $key
 * @return number
 */
function amem_form_post_id_from_key( $key ) {
	$args = array(
		'post_type' => 'amem-form',
		'posts_per_page' => '1',
		'meta_query' => array(
			array(
				'key' => 'form_key',
				'value' => $key,
			),
		),
		'fields' => 'ids',
	);

	$ids = get_posts( $args );

	if ( $ids ) {
		return $ids[0];
	}

	return false;
}

/**
 * Retrieves a form by form key or form ID
 *
 * @since 1.0
 */
function amem_get_form( $form_id_or_key ) {
	$form = false;

	if ( amem_is_valid_form_key( $form_id_or_key ) ) {
		$form = amem_form_from_key( $form_id_or_key );

	} elseif ( is_numeric( $form_id_or_key ) ) {
		$form = amem_form_from_post( $form_id_or_key );

	}

	return $form;
}

/**
 * Returns all forms, both those saved as posts and those registered
 *
 * @since 1.0
 */
function amem_get_forms($args='') {
	$forms = array();

	$args = wp_parse_args( $args, [
		'post_type' => 'amem-form',
		'posts_per_page' => - 1,
	] );

	// Make sure
	$args['post_type'] = 'amem-form';

	// Get all forms saved as posts
	$form_query = new WP_Query( $args );

	if ( $form_query->have_posts() ) {
		foreach ( $form_query->posts as $form_post ) {
			$form = amem_form_from_post( $form_post );
			$forms[] = $form;
		}
	}

	// Get all locally registered forms
	if ( $local_forms = amem()->local->get_forms() ) {
		foreach ( $local_forms as $form ) {
			$forms[] = $form;
		}
	}

	return $forms;
}

/**
 * Returns all fields groups used by specified form
 *
 * @since 1.0
 */
function amem_get_form_field_groups( $form_key ) {
	// If a full form array is passed
	if ( is_array( $form_key ) ) {
		$form_key = $form_key['key'];
	}

	if ( $groups = amem()->local->get_form_groups($form_key) ) {
		return $groups;
	}

	// Location rule filter
	$args = array(
		'amem_form' => $form_key,
	);

	$groups = acf_get_field_groups( $args );

	return amem_sort_form_field_groups( $groups, $form_key );
}

function amem_get_field_group_sort($form_key_or_id) {
	if ( !is_numeric($form_key_or_id) ) {
		$form_id = amem_form_post_id_from_key($form_key_or_id);
	} else {
		$form_id = (int) $form_key_or_id;
	}

	if ( !$form_id )
		return [];

	$sorted = (array) get_post_meta( $form_id, 'amem_field_group_sort', true );

	return array_filter( $sorted );
}

function amem_sort_form_field_groups( $groups, $form_key=null ) {
	if ( !$groups )
		return $groups;

	$sorted_groups = [];
	if ( $form_key ) {
		$sorted = amem_get_field_group_sort($form_key);
		if ( $sorted ) {
			foreach( $groups as $i => $group ) {
				$key = array_search( $group['ID'], $sorted );

				if ( $key !== false ) {
					$sorted_groups[$key] = $group;
					unset($groups[$i]);
				}
			}
			ksort( $sorted_groups );
		}
	}

	uasort($groups, function($a, $b) {
		return $a['menu_order'] > $b['menu_order'];
	} );

	if ( $sorted_groups ) {
		$groups = array_merge( $sorted_groups, $groups );
	}

	return $groups;
}

function amem_get_local_field_groups($form_key) {
	// If a full form array is passed
	if ( is_array( $form_key ) ) {
		$form_key = $form_key['key'];
	}
	return amem()->local->get_form_groups($form_key);
}

/**
 * Returns all fields assigned to a form
 *
 * @since 1.0
 */
function amem_get_form_fields( $form_key, $type = 'all' ) {
	static  $formFields = [];

	$cacheKey = md5($form_key . $type);
	if ( isset($formFields[$cacheKey]) ) {
		return $formFields[$cacheKey];
	}

	if ( $form_fields = amem()->local->get_form_fields($form_key) ) {
		$formFields[$cacheKey] = $form_fields;
		return $form_fields;
	}

	$exclude_types = array();

	// Only pick fields which can be properly stringified (not repeaters, flexible fields etc.)
	if ( 'regular' == $type ) {
		$exclude_types = array( 'repeater', 'clone', 'flexible_content' );
	}

	$form_fields = array();

	$field_groups = amem_get_form_field_groups( $form_key );

	if ( $field_groups ) {
		foreach ( $field_groups as $field_group ) {
			if ( ! $fields = amem()->local->get_group_fields($field_group) )
				$fields = acf_get_fields( $field_group );
			if ( ! empty ( $fields ) ) {
				foreach ( $fields as $field ) {
					if ( in_array( $field['type'], $exclude_types ) ) {
						continue;
					}
					$form_fields[] = $field;
				}
			}
		}
	}

	$formFields[$cacheKey] = $form_fields;

	return $form_fields;
}

/**
 * Renders the success message for a form. Requires that the submission has already been loaded.
 *
 * @todo Povide dismiss feature
 *
 * @since 1.0
 */
function amem_form_success_message( $form, $args, $echo=false ) {
	$success_message = $form['data']['success_message'];
	$success_message = apply_filters( 'amem/form/success_message', $success_message, $form, $args );
	$success_message = apply_filters( 'amem/form/success_message/type=' . $form['type'], $success_message, $form, $args );

	$success_message = amem_resolve_merge_tags( $success_message );
	// acf-notice -error acf-error-message -dismiss
	$output = sprintf( '<div class="acf-notice -success amem-notice amem-success-message" aria-live="assertive" role="alert">%s<a href="#" class="acf-notice-dismiss acf-icon -cancel small"></a></div>', amem_format_message($success_message, true) );// message text escaped by amem_format_message()

	if ( $echo )
		echo $output;

	return $output;
}

function amem_form_updated_message( $form, $args, $echo=false ) {
	$updated_code = isset($_GET['updated']) ? sanitize_key($_GET['updated']) : ''; // phpcs:disable WordPress.Security.NonceVerification
	if ( !$updated_code )
		return;

	$message = amem()->errors->text($updated_code);
	$message = apply_filters( 'amem/form/updated_message', $message, $updated_code, $form, $args );
	$message = apply_filters( 'amem/form/updated_message/type=' . $form['type'], $message, $updated_code, $form, $args );

	if ( '' == $message )
		return;

	$message = amem_resolve_merge_tags( $message );

	$output = sprintf( '<div class="acf-notice -success amem-notice amem-updated-message -dismiss" aria-live="assertive" role="alert">%s<a href="#" class="acf-notice-dismiss acf-icon -cancel small"></a></div>', amem_format_message($message, true) );// message text escaped by amem_format_message()

	if ( $echo )
		echo $output;

	return $output;
}

function amem_form_error_message( $form, $args, $echo=false ) {
	return amem()->errors->form_error_message( $form, $args, $echo );
}

/**
 * Enqueues the necessary scripts and styles for a form.
 *
 * @since 1.0
 */
function amem_enqueue() {
	$min = '';//defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

	// Enqueue the hotfix that prevents validation from firing across all forms on the same page when one is submitted.
	if ( apply_filters( 'amem/settings/enqueue_validation_hotfix', false ) ) {
		amem_enqueue_script( 'amem-multi-form-validation-hotfix', amem_get_url("multi-form-validation-hotfix{$min}.js", 'assets/js'), [ 'acf-input' ], AMEM_VERSION, ['in_footer' => true, 'asset_path' => amem_get_path('', 'assets/js')] );
	}

	// Enqueue ACF scripts and styles
	acf_enqueue_scripts();

	amem_register_script( 'amem-password-strength', amem_get_url("password-strength{$min}.js", 'assets/js'), array( 'password-strength-meter' ), AMEM_VERSION, ['in_footer' => true, 'asset_path' => amem_get_path('', 'assets/js')] );
	acf_localize_text( array( 'Passwords Match' => __( 'Passwords Match', 'advanced-members' ) ) );

	// ACF fails to include all translations when running "acf_enqueue_scripts", hence we need to do it manually.
	$acf_l10n = acf_get_instance( 'ACF_Assets' )->text;
	wp_localize_script( 'acf-input', 'acfL10n', $acf_l10n );

	amem()->fields->enqueue_scripts();

	$ver = amem_register_script( 'amem-forms-script', amem_get_url("forms{$min}.js", 'assets/js'), ['jquery', 'acf-input', 'amem-input'], AMEM_VERSION, ['in_footer' => true, 'asset_path' => amem_get_path('', 'assets/js')] );

	wp_enqueue_style( 'amem-form-style', amem_get_url("form{$min}.css", 'assets/css'), false, $ver );

	if ( $theme = amem()->options->get('load_theme', 'default') ) {
		wp_enqueue_style( 'amem-theme-default', amem_get_url("themes/{$theme}{$min}.css", 'assets/css'), ['amem-form-style'], $ver );
	}

	wp_enqueue_script( 'amem-forms-script' );

	do_action( 'amem/form/enqueue_scripts' );
}


function amem_submit_text_default($form_id) {
	$submit_text = __( 'Submit', 'advanced-members' );
	$form_type = get_post_meta( $form_id, 'select_type', true );
	switch ( $form_type ) {
		case 'login':
		$submit_text = __( 'Login', 'advanced-members' );
		break;
		case 'registration':
		$submit_text = __( 'Register', 'advanced-members' );
		break;
		case 'account':
		$submit_text = __( 'Update Account', 'advanced-members' );
		break;
		case 'social_login':
		$submit_text = __( 'Connect', 'advanced-members' );
		break;
		default:
		break;
	}

	$submit_text = apply_filters( 'amem/form/submit_text_default/type=' . $form_type, $submit_text, $form_id );

	return $submit_text;
}
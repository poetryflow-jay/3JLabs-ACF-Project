<?php
/**
 * Manage Advanced Members for ACF Fields
 *
 * @since 1.0
 *
 */
namespace AMem;

use AMem\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fields extends Module {
	/** @var string name of module */
	protected $name = 'fields';

	/** @var array core fields that synced to WP User data */
	protected $fields = [
		'user_email' => 'user_email',
		'user_login' => 'username',
		'first_name' => 'first_name',
		'last_name' => 'last_name',
		'user_pass' => 'user_password',
		'description' => 'user_bio',
		'user_url' => 'user_url',
		'nickname' => 'nickname',
		'display_name' => 'display_name',
		// 'avatar' => 'avatar',
	];

	function __construct() {
		$this->inc = 'acf/fields/';

		add_action( 'acf/include_field_types', [$this, 'include_field_types'] );

		add_filter( 'acf/get_field_types', [$this, 'get_field_types'] );

		add_filter( 'acf/localized_field_categories', [$this, 'localized_field_categories'] );

		add_action( 'acf/register_scripts', [$this, 'register_scripts'] );

		add_action( 'acf/enqueue_scripts', [$this, 'enqueue_scripts'] );

		add_action( 'save_post_acf-field-group', [$this, 'clear_cache'] );

		add_action( 'acf/render_field_settings', [$this, 'render_field_settings'] );
		add_filter( 'acf/get_field_label', [$this, 'hide_label'], 99, 3 );
	}

	function include_field_types() {
		acf_add_local_field_group( array(
			'key' => 'group_amem_errors',
			'title' => '',
			'fields' => array(
				array(
					'key' => 'field_amem_errors',
					'label' => '',
					'name' => '_amem_errors',
					'type' => 'field_amem_errors',
					'readonly' => true,
				),
				// array(
				// 	'key' => 'field_amem_updated',
				// 	'label' => '',
				// 	'name' => '_amem_updated',
				// 	'type' => 'message',
				// 	'message' => '',
				// 	'readonly' => true,
				// )
			)
		) );

		amem_include( $this->inc  . 'base/trait-amem-field.php' );
		amem_include( $this->inc  . 'class-errors.php' );
		$base_fields = [
			'base',
			'text',
			'textarea',
			'email',
			'url',
			'password',
			'select',
			'delete-object',
			'true_false',
			'seperator',
		];
		foreach ( $base_fields as $field ) {
			amem_include( $this->inc  . "base/class-{$field}.php" );
		}

		foreach ( $this->fields as $field ) {
			$field = str_replace('_', '-', $field);
			amem_include( $this->inc  . "class-{$field}.php" );
		}

		amem_include( $this->inc  . "class-user-tos.php" );

		amem_include( $this->inc  . "class-user-password-current.php" );
		amem_include( $this->inc  . "class-user-password-confirm.php" );

		do_action( 'amem/include_field_types', AMEM_VERSION );

	}

	/**
	 * Predefined fields that used by WordPress core user data
	 * 
	 * @return array
	 */
	function predefined_fields() {
		return apply_filters( 'amem/predefined_fields', $this->fields );
	}

	/**
	 * Clear all fields cacheq
	 * 
	 * @param  int $post_id
	 */
	function clear_cache($post_id) {
		$cacheKey = 'amem/fields/get_all_fields';
		delete_transient( $cacheKey );
	}

	/**
	 * Returns All registered fields with Members forms
	 * 
	 * @return array
	 */
	function get_all_fields() {

		$cacheKey = 'amem/fields/get_all_fields';
		if ( $cached = get_transient($cacheKey) ) {
			return $cached;
		}

		$field_groups = acf_get_field_groups( ['amem_form' => '__EXISTS__'] );

		$all_fields = [];

		foreach ( $field_groups as $group ) {
			$fields = acf_get_fields( $group['ID'] );
			foreach ( $fields as $field ) {
				if ( !isset($all_fields[$field['name']]) )
					$all_fields[$field['name']] = $field;
			}
		}

		// cache fields 1 hour
		set_transient( $cacheKey, $all_fields, HOUR_IN_SECONDS );
		return $all_fields;
	}

	/**
	 * Remove fields used by internal or local oly
	 * @param  array $groups
	 * @return array
	 */
	function get_field_types($groups) {
		$cat = 'Advanced Members';
		$hide_types = ['delete_user'/*, 'username', 'user_password'*/, 'user_password_confirm', 'user_password_current', 'field_amem_errors'];

		$hide_types = apply_filters( 'amem/fields/hidden_types', $hide_types );

		if ( isset($groups[$cat]) && is_array($groups[$cat]) ) {
			foreach ($groups[$cat] as $name => $label) {
				if ( in_array($name, $hide_types) )
					unset($groups[$cat][$name]);
			}
		}

		// $category = 'Advanced';
		// if (isset($groups['Adv. Members'])) {
		// 	$groups = amem_array_insert_after($groups, $category, 'Adv. Members', $groups['Adv. Members']);
		// }

		return $groups;
	}

	function get_field_type($field) {
		$type = '';
		if ( is_array($field) && isset($field['type']) ) {
			$type = $field['type'];
		} else {
			$_field = get_field_object($field);
			if ( !empty($_field['type']) )
				$type = $_field['type'];
		}

		return apply_filters( 'amem/fields/get_field_type', $type, $field );
	}

	function localized_field_categories($cats) {
		$cats = amem_array_insert_after($cats, 'advanced', 'Advanced Members', 'Advanced Members');

		return $cats;
	}

	function register_scripts() {
		$min = '';//defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		amem_register_script( 'amem-input', amem_get_url("amem-input{$min}.js", 'assets/js'), array( 'jquery', 'acf', 'acf-input' ), AMEM_VERSION, ['in_footer' => true, 'asset_path' => amem_get_path('', 'assets/js')] );

		do_action( 'amem/register_scripts' );
	}

	function enqueue_scripts() {
		wp_enqueue_script( 'amem-input' );

		$data = [
			'truefalse_types' => apply_filters( 'amem/fields/truefalse_types', ['user_tos'] ),// for conditional logic
		];

		wp_localize_script( 'amem-input', 'amemL10n', $data );

		do_action( 'amem/enqueue_scripts' );
	}

	function render_field_settings($field) {
    acf_render_field_setting( $field, array(
      'label'            => __('Hide Label', 'advanced-members'),
      'instructions'    => '',
      'name'            => 'hide_label',
      'type'            => 'true_false',
      'ui'            => 1,
    ), true );
	}

	function hide_label( $label, $field, $context ) {
		if ( $context != 'admin' ) {
			$hide_label = acf_maybe_get($field, 'hide_label');
			
			if ( $hide_label && amem_is_front() ) {
        $label = '';
			}
		}

		return $label;
	}

}

amem()->register_module('fields', Fields::getInstance());

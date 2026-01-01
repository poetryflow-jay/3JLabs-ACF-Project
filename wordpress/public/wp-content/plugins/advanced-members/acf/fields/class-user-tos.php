<?php
/**
 * ACF Field for User Bio
 *
 * @since 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( ! class_exists( 'amem_field_user_tos' ) ) :

class amem_field_user_tos extends amem_field_true_false {

	function initialize() {
		// vars
		$this->name       = 'user_tos';
		$this->meta_key 	= false;// You can set any metakey
		$this->label      = __( 'Consent', 'advanced-members' );
		$this->category = 'Advanced Members';
		$this->description = __( 'Provides an agreement field for terms of service or privacy policy.', 'advanced-members' );
		$this->preview_image = amem_get_url('images/field-type-previews/field-preview-true-false.png', 'assets');
		$this->defaults      = array(
			'default_value' => 0,
			/* translators: site name string */
			'message'       => sprintf( __( 'By creating an account, you agree to %s\'s {terms}.', 'advanced-members' ), get_bloginfo('name') ),
			'ui'            => 0,
			'ui_on_text'    => '',
			'ui_off_text'   => '',
			'tos_page_id' => 0,
			'tos_page_text' => '',
			'required' 			=> 1,
			'tos_page_id_2' => '',
		);

		// add_filter( 'acf/update_value/type=' . $this->name, array( $this, 'pre_update_value' ), 9, 3 );
		// add_filter( 'acf/load_field/type=' . $this->name, [$this, '_load_field'] );
	}

	function _load_field($field) {
		$field['required'] = 1;// force set required
		return $field;
	}

	function load_field($field) {
		// does not change meta key(name)
		return $field;
	}

	function update_field( $field ) {
		// does not change meta key(name)
		return $field;
	}

	function prepare_field( $field ) {
		$field['type'] = 'true_false';
		// Removed force for multiple use on single form
		// $field['required'] = 1;// force set require

		// Change Field into a select
		$field['type']    = 'true_false';
		$link = $link_alt = "";
		if ( $page_id = (int) $field['tos_page_id'] ) {

			$url = get_permalink( $page_id );
			if ( $url && !is_wp_error($url) ) {
				$title = get_the_title( $page_id );
				if ( $field['tos_page_text'] ) {
					$title = $field['tos_page_text'];
				}
				$link = sprintf( '<a href="%s" class="amem-agree-tos" target="_blank" title="%s">%s</a>', esc_url($url), esc_attr($title), esc_html($title) );
			}
		}

		// if ( isset($field['tos_page_id_2']) && ($page_id = (int) $field['tos_page_id_2']) ) {

		// 	$url = get_permalink( $page_id );
		// 	if ( $url && !is_wp_error($url) ) {
		// 		$title = get_the_title( $page_id );
		// 		if ( $field['tos_page_text_2'] ) {
		// 			$title = $field['tos_page_text_2'];
		// 		}
		// 		$link_alt = sprintf( '<a href="%s" class="amem-agree-tos -tos-alt" target="_blank" title="%s">%s</a>', esc_url($url), esc_attr($title), esc_html($title) );
		// 	}
		// }

		$field['message'] = str_replace( ['{terms}', '{terms2}', '{site_name}'], [$link, $link_alt, get_bloginfo('name')], $field['message'] );

		return $field;
	}

	function render_field_settings( $field ) {
		// parent::render_field_settings( $field );
		// $this->_render_field_settings( $field );
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Message', 'advanced-members' ),
				'instructions' => __( 'Displays text alongside the checkbox. You can use TOS page link with {terms}.', 'advanced-members' ),
				'type'         => 'text',
				'name'         => 'message',
			)
		);

		// acf_render_field_setting(
		// 	$field,
		// 	array(
		// 		'label'        => __( 'Default Value', 'advanced-members' ),
		// 		'instructions' => '',
		// 		'type'         => 'true_false',
		// 		'name'         => 'default_value',
		// 	)
		// );

		$pages = array(
			0 => __( 'Not Selected', 'advanced-members' )
		);
		foreach (get_pages(array(
			'sort_column' => 'post_title',
			'sort_order' => 'ASC',
		)) as $page ) {
			$pages[$page->ID] = $page->post_title;
		};

		acf_render_field_setting(
			$field,
			array(
				'label'         => __( 'Terms Page', 'advanced-members' ),
				'name'          => 'tos_page_id',
				'type'          => 'select',
				'instructions' 	=> __( 'Leave empty for no page link', 'advanced-members' ),
				'choices'       => $pages
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Terms page link text', 'advanced-members' ),
				'instructions' => __( 'Text for the page link text. Leave empty to use the page title.', 'advanced-members' ),
				'default_value' => '',
				'name'         => 'tos_page_text',
				'type'         => 'text',
				'ui'           => 1,
				'conditions' => array(
					'field' => 'tos_page_id',
					'operator' => '!=',
					'value' => '0',
				)
			)
		);
		// acf_render_field_setting(
		// 	$field,
		// 	array(
		// 		'label'         => __( 'Use secondary TOS page', 'advanced-members' ),
		// 		'name'          => 'use_extra_tos',
		// 		'type'          => 'true_false',
		// 		'ui' 						=> 1,
		// 		// 'instructions' 	=> __( 'Leave empty for no page link', 'advanced-members' ),
		// 		// 'choices'       => $pages
		// 		'conditions' => array(
		// 			'field' => 'tos_page_id',
		// 			'operator' => '!=',
		// 			'value' => '0',
		// 		)
		// 	)
		// );
		// acf_render_field_setting(
		// 	$field,
		// 	array(
		// 		'label'         => __( 'Extra TOS Page', 'advanced-members' ),
		// 		'name'          => 'tos_page_id_2',
		// 		'type'          => 'select',
		// 		'instructions' 	=> __( 'Leave empty for no page link', 'advanced-members' ),
		// 		'choices'       => $pages,
		// 		'conditions' 		=> array(
		// 			array(
		// 				'field' => 'tos_page_id',
		// 				'operator' => '!=',
		// 				'value' => '0',
		// 			),
		// 			array(
		// 				'field'    => 'use_extra_tos',
		// 				'operator' => '==',
		// 				'value'    => '1',
		// 			)
		// 		),
		// 	)
		// );
		// acf_render_field_setting(
		// 	$field,
		// 	array(
		// 		'label'        => __( 'Extra TOS page link text', 'advanced-members' ),
		// 		'instructions' => __( 'Text of page link text. Leave empty to use page title.', 'advanced-members' ),
		// 		'default_value' => '',
		// 		'name'         => 'tos_page_text_2',
		// 		'type'         => 'text',
		// 		'ui'           => 1,
		// 		'conditions' 		=> array(
		// 			array(
		// 				'field' => 'tos_page_id',
		// 				'operator' => '!=',
		// 				'value' => '0',
		// 			),
		// 			array(
		// 				'field'    => 'use_extra_tos',
		// 				'operator' => '==',
		// 				'value'    => '1',
		// 			),
		// 			array(
		// 				'field' => 'tos_page_id_2',
		// 				'operator' => '!=',
		// 				'value' => '0',
		// 			),
		// 		),
		// 	)
		// );
	}

	// function render_field( $field ) {

	// 	acf_render_field( $field );

	// 	// render
	// 	// acf_render_field( $field );
	// }

	// function load_value( $value, $post_id = false, $field = false ) {
	// 	$user = explode( '_', $post_id );
	// 	if ( $user_id = $this->_user_id($post_id) ) {
	// 		$value = get_user_meta( $user[1], 'description', true );
	// 	}
	// 	return $value;
	// }

	// function pre_update_value( $value, $post_id = false, $field = false ) {
	// 	if ( $field['name'] == 'display_name' ) {
	// 		return $value;
	// 	}

	// 	if ( $user_id = $this->_user_id($post_id) ) {
	// 		remove_action( 'acf/save_post', '_acf_do_save_post' );

	// 		$description = apply_filters( 'pre_user_description', $value );
	// 		update_user_meta( $user_id, 'description', $description );

	// 		add_action( 'acf/save_post', '_acf_do_save_post' );
	// 	}
	// 	return null;
	// }

	// function update_value( $value, $post_id = false, $field = false ) {
	// 	return null;
	// }

	// function validate_value( $is_valid, $value, $field, $input ) {
	// 	return $is_valid;
	// }

}

// initialize
acf_register_field_type( 'amem_field_user_tos' );

endif;



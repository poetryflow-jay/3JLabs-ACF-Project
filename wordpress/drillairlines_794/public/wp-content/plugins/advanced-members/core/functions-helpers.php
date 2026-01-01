<?php
/**
 * AMem Helper Functions
 *
 * @since 	1.0
 * @package Advanced Members for ACF
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

function amem_addon_slug( $slug ) {
	if ( ! function_exists( 'get_plugins' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$all_plugins = get_plugins();
	if ( $all_plugins ) {
		foreach ( $all_plugins as $path => $plugin ) {
			if ( $plugin['TextDomain'] == $slug ) {
				return $path;
			}
		}
	}
	return false;

}

/**
 * amemb_get_path
 *
 * Returns the plugin path to a specified file.
 *
 * @since   1.0
 *
 * @param   string $filename The specified file.
 * @return  string
 */
function amem_get_path( $filename = '', $base = null ) {
	if ( $base && strpos($base, 'pro') === 0 && function_exists('amem_pro') ) {
		switch ( $base ) {
			case 'pro':
			$base = AMEMPRO_PATH;
			break;
			case (strpos($base, 'pro/assets/') === 0):
			$base = amem()->assets_inc . preg_replace('!^pro/assets/!', '', $base, 1);
			break;
		}
	}

	switch ( $base ) {
		case null:
		$base = AMEM_PATH;
		break;
		case 'assets':
		$base = amem()->assets_inc;
		break;
		case (strpos($base, 'assets/') === 0):
		$base = amem()->assets_inc . preg_replace('!^assets/!', '', $base, 1);
		break;
	}

	return ( !$base ? AMEM_PATH : $base ) . ltrim( $filename, '/' );
}

/**
 * amemb_get_url
 *
 * Returns the plugin url to a specified file.
 * This function also defines the ACF_URL constant.

 * @since   1.0
 *
 * @param   string $filename The specified file.
 * @return  string
 */
function amem_get_url( $filename = '', $base = null ) {
	if ( strpos($base, 'pro') === 0 && function_exists('amem_pro') ) {
		switch ( $base ) {
			case 'pro':
			$base = AMEMPRO_URL;
			break;
			case 'pro/assets':
			$base = amem_pro()->assets_url;
			break;
			case (strpos($base, 'pro/assets/') === 0):
			$base = amem_pro()->assets_url . preg_replace('!^pro/assets/!', '', $base, 1);
			break;
		}
	}

	switch ( $base ) {
		case null:
		$base = AMEM_URL;
		break;
		case 'assets':
		$base = amem()->assets_url;
		break;
		case (strpos($base, 'assets/') === 0):
		$base = amem()->assets_url . preg_replace('!^assets/!', '', $base, 1);
		break;
	}

	return trailingslashit( !$base ? AMEM_URL : $base ) . ltrim( $filename, '/' );
}

/**
 * amemb_include
 *
 * @since   1.0
 *
 * @param   string $filename The specified file.
 * @param 	string $base_path Base path of filename
 */
function amem_include( $filename = '', $base = null ) {
	$file_path = amem_get_path( $filename, $base );
	if ( file_exists( $file_path ) ) {
		return include_once $file_path;
	}
}

/**
 * This function will load in a file from the 'admin/views' folder and allow variables to be passed through
 *
 * @since   1.0
 *
 * @param string $view_path
 * @param array  $view_args
 * @param string $base_path Base path of $view_path
 */
function amem_get_view( $view_path = '', $view_args = array(), $base_path = '' ) {
	// include
	$view_path = $base_path ? $base_path . $view_path : $view_path;
	if ( file_exists( $view_path ) ) {
		extract( $view_args, EXTR_SKIP );
		include $view_path;
	}
}

function amem_register_script( $handle, $src, $deps = array(), $ver = false, $args = array() ) {
	if ( !is_array($args) || empty( $args['asset_path']) ) {
		wp_register_script( $handle, $src, $deps, $ver, $args );
		return $ver;
	}

	if ( $data = _amem_asset_data($src, $args['asset_path']) ) {
		$ver = $data['version'];
	}

	wp_register_script( $handle, $src, $deps, $ver, $args );
	return $ver;
}

function amem_enqueue_script( $handle, $src, $deps = array(), $ver = false, $args = array() ) {
	if ( !is_array($args) || empty( $args['asset_path']) ) {
		wp_enqueue_script( $handle, $src, $deps, $ver, $args );
		return $ver;
	}

	if ( $data = _amem_asset_data($src, $args['asset_path']) ) {
		$ver = $data['version'];
	}

	wp_enqueue_script( $handle, $src, $deps, $ver, $args );
	return $ver;
}

function _amem_asset_data($src, $asset_path, $extensions = ['.js']) {
	if ( empty($extensions) )
		$extensions = ['.js'];
	if ( !is_array($extensions) )
		$extensions = explode( ',', $extensions );

	$extensions = array_map( 'trim', $extensions );

	$asset_file = basename( $src, '.js' );
	$asset_file = basename( $asset_file, '.JS' );

	$asset_file = $asset_file . '.asset.php';

	$data = amem_include( $asset_file, trailingslashit( $asset_path ) );
	if ( is_array( $data ) && !empty($data['version']) ) {
		return $data;
	}

	return false;
}

/**
 * Get core page url
 *
 * @param $slug
 * @param bool $updated
 *
 * @return bool|false|mixed|string|void
 */
function amem_get_core_page( $slug, $updated = false, $redirect_to = '' ) {
	$url = '';
	if ( $page_id = amem_get_core_page_id($slug) ) {
		$url = get_permalink( $page_id );
		if ( $updated ) {
			$url = add_query_arg( 'updated', esc_attr( $updated ), $url );
		} elseif ( $redirect_to ) {
			if ( $redirect_to == 'current' ) {
				$redirect_to = amem_get_current_url();
			}
			$url = add_query_arg( 'redirect_to', esc_attr( $redirect_to ), $url );
		}
	}

	return apply_filters( 'amem/core/page', $url, $slug, $updated );
}

function amem_get_core_page_id( $slug ) {
	if ( isset( amem()->options->permalinks[ $slug ] ) ) {
		return (int) amem()->options->permalinks[ $slug ];
	}
	return 0;
}

/**
 * Check if we are on a AMem Core Page or not
 *
 * Default AMem core pages slugs
 * 'login', 'register', 'logout', 'account', 'password-reset'
 *
 * @param string $page AMem core page slug
 *
 * @return bool
 */
function amem_is_core_page( $slug ) {
	$page_id = amem_get_core_page_id( $slug );

	return $page_id && is_page( $page_id );
}

function amem_allowed_roles() {
	global $wp_roles;
	static $roles;

	if ( isset($roles) )
		return $roles;

	$all_roles = $wp_roles->roles;
	$roles = array();
	foreach ($all_roles as $key => $role) {
		$roles[$key] = translate_user_role($role['name']);
	}

	$roles = apply_filters( 'amem/allowed_roles', $roles );

	return $roles;
}

function amem_is_core_post( $post, $slugs = [] ) {
	if ( empty($post->ID) || empty($slugs) )
		return false;

	if ( $slugs === 'all' ) {
		$core_pages = amem()->config->get_core_pages();
		$slugs = array_keys( $core_pages );
	}

	if ( !is_array($slugs) )
		$slugs = (array) $slugs;

	foreach( $slugs as $slug ) {

		$core_page_id = (int) amem_get_core_page_id( $slug );
		if ( !$core_page_id )
			continue;

		if ( $core_page_id == $post->ID ) {
			return true;
		}

		/** @todo Ready for WPML and PolyLang */
		/*if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			if ( get_post_meta( $post->ID, '_amem_wpml_' . $slug, true ) == 1 ) {
				return true;
			}

			$icl_duplicate = get_post_meta( $post->ID, '_icl_lang_duplicate_of', true );

			if ( ! empty( $icl_duplicate ) && $icl_duplicate == $core_page_id ) {
				return true;
			}
		}*/
	}

	return false;
}

function amem_get_role_label($role_slug) {
	global $wp_roles;

	if (!isset($wp_roles)) {
		$wp_roles = new WP_Roles();
	}

	$roles = $wp_roles->roles;
	$role = $roles[$role_slug];

	if ($role) {
		$role_label = $role['name']; // 해당 역할의 레이블을 가져옵니다.
		return $role_label;
	} else {
		return __('Cannot find the role', 'advanced-members');
	}
}


/**
 * Getting replace placeholders array
 *
 * @return array
 */
function amem_replace_placeholders() {

	$search = array(
		'{username}',
		'{display_name}',
		'{first_name}',
		'{last_name}',
		'{userid}',
		'{email}',
		'{site_name}',
		'{user_account_link}',
		'{register_date}',
	);

	$replace = array(
		amem_user('user_login'),
		amem_user('display_name'),
		amem_user('first_name'),
		amem_user('last_name'),
		amem_user('user_login'),
		amem_user('user_email'),
		get_bloginfo( 'name' ),
		amem_get_core_page('account'),
		amem_user('user_registered'),
	);

	$search = apply_filters( 'amem/template/merge_tags', $search );
	$replace = apply_filters( 'amem/template/merge_tags/replace', $replace );

	return array_combine( $search, $replace );
}


/**
 * Convert template tags
 *
 * @param $content
 * @param array $args
 * @param bool $with_kses
 *
 * @return mixed|string
 */
function amem_convert_tags( $content, $args = array(), $with_kses = true ) {
	$placeholders = amem_replace_placeholders();

	$content = str_replace( array_keys( $placeholders ), array_values( $placeholders ), $content );
	if ( $with_kses ) {
		$content = wp_kses_decode_entities( $content );
	}

	if ( isset( $args['tags'] ) && isset( $args['tags_replace'] ) ) {
		$content = str_replace( $args['tags'], $args['tags_replace'], $content );
	}

	$regex = '~\{(usermeta:[^}]*)\}~';
	preg_match_all( $regex, $content, $matches );

	// Support for all usermeta keys
	if ( ! empty( $matches[1] ) && is_array( $matches[1] ) ) {
		foreach ( $matches[1] as $match ) {
			$key = str_replace( 'usermeta:', '', $match );
			$value = amem_user( $key );
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}
			$content = str_replace( '{' . $match . '}', apply_filters( 'amem_convert_tags', $value, $key ), $content );
		}
	}
	return $content;
}

/**
 * @param $key
 *
 * @return int|string|array
 */
function amem_user( $key, $attrs = null ) {
	switch ($key) {
		case 'ID':
		return amem()->user->id;
		break;

		default:
		$value = maybe_unserialize( amem()->user->get($key) );
		if ( is_array( $value ) ) {
			$value = implode( ",", $value );
		}
		return apply_filters( "amem/user/{$key}", $value, amem_user('ID') );;
		break;

		case 'user_activation_link':
		if ( ! amem_user( 'registration_secretkey_hash' ) ) {
			return false;
		}

		$url =  home_url();
		$url =  add_query_arg( 'act', 'user_mail_active', $url );
		$url =  add_query_arg( 'key', amem_user( 'registration_secretkey_hash' ), $url );
		$url =  add_query_arg( 'login', amem_user( 'user_login' ), $url );

		return $url;
		break;

		case 'password_reset_link':
		$user_id = amem_user( 'ID' );
		delete_option( "amem_user_cachedata_{$user_id}" );

		$user_data = get_userdata( $user_id );
		$key = get_password_reset_key( $user_data );
		$url = add_query_arg(
			array(
				'act'   => 'reset_password',
				'hash'  	=> $key,
				'login' => $user_data->user_login,
			),
			amem_get_core_page( 'password-reset' )
		);
		return $url;
		break;
	}
}

/**
 * Searches input for tags {field:FIELD_NAME} and replaces with field values.
 * Also replaces general tags such as {all_fields}.
 *
 * @since 1.0
 *
 */
function amem_resolve_merge_tags( $input, $fields = false ) {
	// Get fields from the global submission object if fields weren't passed
	if ( ! $fields && amem_has_submission() ) {
		$fields = amem()->submission['fields'];
	}

	// Find all merge tags
	if ( preg_match_all( "/{(.*?)}/", $input, $matches ) ) {
		foreach ( $matches[1] as $i=>$tag ) {
			// Resolve each merge tag and insert the value
			$value = apply_filters( 'amem/merge_tags/resolve', '', $tag, $fields );
			$input = str_replace( $matches[0][ $i ], $value, $input );
		}
	}
	
	return $input;
}


/**
 * Renders a single field include (for emails, success messages etc.)
 *
 * @since 1.0
 */
function _amem_render_field_include( $field, $value = false ) {
	if ( ! $value ) {
		$value = $field['value'];
	}

	$output = '';
	
	if ( 'repeater' == $field['type'] && is_array( $value ) ) {
		$output .= '<table class="amem-field-include amem-field-include-repeater">';
		
		// Column headings
		$output .= '<thead><tr>';
		foreach ( $field['sub_fields'] as $sub_field ) {
			$output .= sprintf( '<th>%s</th>', $sub_field['label'] );
		}
		$output .= '</tr></thead>';
		
		// Rows
		$output .= '<tbody>';
		if ( is_array( $value ) ) {
			foreach ( $value as $row_values ) {
				$output .= '<tr>';
				foreach ( $field['sub_fields'] as $sub_field ) {
					$output .= sprintf( '<td>%s</td>', _amem_render_field_include( $sub_field, $row_values[ $sub_field['name'] ] ) );
				}
				$output .= '</tr>';
			}
		}
		$output .= '</tbody>';

		$output .= '</table>';
	} elseif ( 'flexible_content' === $field['type'] ) {
		$output .= '<table class="amem-field-include amem-field-include-flexible_content">';

		foreach ( $value as $row ) {
			$row_layout_name = $row['acf_fc_layout'];

			// Find layout based on name
			$row_layout = NULL;
			foreach ( $field['layouts'] as $layout ) {
				if ( $layout['name'] === $row_layout_name ) {
					$row_layout = $layout;
					break;
				}
			}

			// Output header with layout name for the row
			$output .= sprintf( '<tr><th>%s</th></tr>', $layout['label'] );
			$output .= '<tr><td>';

			// The subfield values will be displayed in a nested table, similar to a group field
			$output .= '<table class="amem-field-include amem-field-include-flexible_content-inner">';
			foreach ( $layout['sub_fields'] as $sub_field ) {
				if ( isset( $row[ $sub_field['name'] ] ) ) {
					$output .= sprintf( '<tr><th>%s</th></tr>', $sub_field['label'] );
					$output .= sprintf( '<tr><td>%s</td></tr>', _amem_render_field_include( $sub_field, $row[ $sub_field['name'] ] ) );
				}
			}
			$output .= '</table>';

			$output .= '</td></tr>';
		}

		$output .= '</table>';
	} elseif ( 'clone' == $field['type'] || 'group' == $field['type'] ) {
		$output .= sprintf( '<table class="amem-field-include amem-field-include-%s">', $field['type'] );
	
		foreach ( $field['sub_fields'] as $sub_field ) {
			if ( isset( $value[ $sub_field['name'] ] ) ) {
				$output .= sprintf( '<tr><th>%s</th></tr>', $sub_field['label'] );
				$output .= sprintf( '<tr><td>%s</td></tr>', _amem_render_field_include( $sub_field, $value[ $sub_field['name'] ] ) );
			}
		}
		
		$output .= '</table>';
	} elseif ( 'true_false' == $field['type'] ) {
		$true_text = isset( $field['ui_on_text'] ) && ! empty( $field['ui_on_text'] ) ? $field['ui_on_text'] : __( 'Yes', 'advanced-members' );
		$false_text = isset( $field['ui_off_text'] ) && ! empty( $field['ui_off_text'] ) ? $field['ui_off_text'] : __( 'No', 'advanced-members' );
		
		$output = esc_html( $value ? $true_text : $false_text );
	} elseif ( 'image' == $field['type'] ) {
		$output .= sprintf( '<img src="%s" alt="%s" />', esc_attr( $value['sizes']['medium'] ), esc_attr( $value['alt']));
	} elseif ( 'gallery' == $field['type'] && is_array( $value ) ) {
		foreach ( $value as $image ) {
			$output .= sprintf( '<img src="%s" alt="%s" />', esc_attr( $image['sizes']['medium'] ), esc_attr( $image['alt']));
		}
	} elseif ( 'file' == $field['type'] ) {
		if ( 'url' === $field['return_format'] ) {
			$output .= sprintf( '<a href="%s">Download</a>', esc_attr($value) );
		} else if ( 'array' == $field['return_format'] ) {
			$output .= sprintf( '<a href="%s">%s</a>', esc_url($value['url']), esc_html( $value['title'] ) );
		}
	} elseif ( in_array( $field['type'], array( 'wysiwyg', 'textarea', 'calculated' ) ) ) {
		// Sanitize input using kses
		$output .= wp_kses_post( stripslashes( $value ) );
	} else {
		$output = _amem_render_field_include_value( $value ); 
	}
	
	// Allow third-parties to alter rendered field
	$output = apply_filters( 'amem/field/render_include', $output, $field, $value );
	$output = apply_filters( 'amem/field/render_include/name=' . $field['name'], $output, $field, $value );
	$output = apply_filters( 'amem/field/render_include/key=' . $field['key'], $output, $field, $value );
	
	return $output;
}


/**
 * Handle the different shapes field values may take and create an appropriate string
 *
 * WP_Post 		- post title
 * WP_User 		- user first name and last name combined
 * User array	- user first name and last name combined
 * WP_Term 		- term name
 * Array 		- render each value and join with commas
 * Other 		- cast to string
 *
 * @since 1.0
 *
 */
function _amem_render_field_include_value( $value ) {
	$rendered_value = '';
	 
	if ( $value instanceof WP_Post ) {
		
		$rendered_value = $value->post_title;
		
	} elseif ( $value instanceof WP_User ) {
		
		$rendered_value = sprintf( '%s %s', $value->first_name, $value->last_name );
	
	} elseif ( is_array( $value ) && isset( $value['user_email'] ) ) {
		
		$rendered_value = sprintf( '%s %s', $value['user_firstname'], $value['user_lastname'] );
		
	} elseif ( $value instanceof WP_Term ) {
		
		$rendered_value = $value->name;
		
	} elseif ( is_array( $value ) ) {
		
		$rendered_values = array();
		
		foreach ( $value as $single_value ) {
			
			$rendered_values[] = _amem_render_field_include_value( $single_value );
			
		}
		
		$rendered_value = join( ', ', $rendered_values );
		
	} else {
		
		$rendered_value = (string)$value;
		
	}

	// Sanitize output to protect against XSS
	return esc_html( $rendered_value );
}


/**
 * Output an "Insert field" button populated with $fields
 * $floating adds class "floating" to the wrapper making the button float right in an input field
 *
 * @since 1.0
 *
 */
function _amem_field_inserter_button( $form, $type = 'all', $floating = false ) {	
	$classses = ( $floating ) ? 'floating' : '';
	
	echo '<a class="amem-field-dropdown ' . $classses . ' button">' . esc_html__( 'Insert field', 'advanced-members' );
		
	echo '<div class="amem-dropdown">';

	$custom_tags = apply_filters( 'amem/merge_tags/custom', array(), $form );
	foreach ( $custom_tags as $custom_tag ) {
		echo sprintf( '<div class="field-option" data-insert-value="{%s}">%s</div>', esc_attr($custom_tag['value']), esc_html($custom_tag['label']) );
	}

	if ( ! empty( $custom_tags ) ) {
		echo '<div class="field-divider"></div>';
	}
	
	if ( 'all' == $type ) {
		echo sprintf( '<div class="field-option" data-insert-value="{all_fields}">%s</div>', esc_html__( 'All fields', 'advanced-members' ) );
	}
	
	$fields = amem_get_form_fields( $form, $type );
	foreach ( $fields as $field ) {
		_amem_field_inserter_render_option( $field );
	}
	
	echo '</div>';
	echo '</a>';
}

function _amem_field_inserter_render_option( $field, $ancestors = array() ) {
	$insert_value = '';
	if ( empty( $ancestors ) ) {
		$insert_value = sprintf( '{field:%s}', $field['name'] );
	} else {
		$hierarchy = array_merge( $ancestors, array( $field['name'] ) );
		$top_level_name = array_shift( $hierarchy );
		$insert_value = sprintf( '{field:%s[%s]}', $top_level_name, join( '][', $hierarchy ) );
	}
	
	$label = wp_strip_all_tags( $field['label'] );
	$type = acf_get_field_type_label( $field['type'] );

	echo sprintf( '<div class="field-option" data-insert-value="%s" role="button">', esc_attr($insert_value) );
	echo sprintf( '<span class="field-name">%s</span><span class="field-type">%s</span>', esc_html($label), esc_html($type) );
	echo '</div>';

	// Append options for sub fields if they exist (and we are dealing with a group or clone field)
	$parent_field_types = array( 'group', 'clone' );
	if ( in_array( $field['type'], $parent_field_types ) && isset( $field['sub_fields'] ) ) {
		array_push( $ancestors, $field['name'] );

		echo '<div class="sub-fields-wrapper">';
		foreach ( $field['sub_fields'] as $sub_field ) {
			_amem_field_inserter_render_option( $sub_field, $ancestors );
		}
		echo '</div>';
	}
}


/**
 * Generates choices for a form field picker.
 * Returns an array with field key => field label suitable for usage with an ACF select field.
 *
 * $type can be either 'all' or 'regular'.
 *
 * @since 1.0
 *
 */
function _amem_form_field_choices( $form_key, $type = 'all' ) {
	$form_fields = amem_get_form_fields( $form_key, $type );
	
	$choices = array();
	
	if ( ! empty( $form_fields ) ) {
		foreach ( $form_fields as $field ) {
			$choices[ $field['key'] ] = $field['label'];
		}
	}

	return $choices;
}


/**
 * Get value of field picker from current form
 * Either from a picked field or custom format
 *
 * @since 1.0
 *
 */
function _amem_resolve_field_picker_value( $value ) {
	if ( ! isset( $value['field'] ) || ! isset( $value['format'] ) ) {
		return false;
	}
	
	if ( $value['field'] == 'custom' ) {
		return amem_resolve_merge_tags( $value['format'] );
	} elseif ( ! empty( $value['field'] ) ) {
		$field_object = amem_get_field_object( $value['field'] );
		if ( false !== $field_object ) {
			return _amem_render_field_include( $field_object );
		}
	}
	
	return false;
}
	

/**
 * Find a nested sub field based on some selector.
 * If the selector is ["g1", "g2", "f1"] then the function will find a
 * field named "f1" inside "g2" which is inside field "g1".
 * The $field parameter should be the top-level field, "g1" in the example.
 * 
 * @since 1.0
 * 
 */
function amem_pick_sub_field( $field, $selector ) {
	while ( ! empty( $selector ) && $field && isset( $field['sub_fields'] ) ) {
		$search = array_shift( $selector );
		$field = acf_search_fields( $search, $field['sub_fields'] );
	}

	return $field;
}


/**
 * Find a nested value of a sub field based on some selector.
 * If the selector is ["g1", "g2", "f1"] then the function will return the
 * value of field "f1" inside "g2" which is inside "g1".
 * The $field parameter should be the top-level field, "g1" in the example.
 * 
 * @since 1.0
 * 
 */
function amem_pick_sub_field_value( $field, $selector ) {
	$value = $field['value'];

	while ( ! empty( $selector ) ) {
		$search = array_shift( $selector );
		if ( isset( $value[ $search ] ) ) {
			$value = $value[ $search ];
		} else {
			return false;
		}
	}

	return $value;
}


/**
 * Retrieves full URL (with trailing slash) to the plugin assets folder
 *
 * @since 1.0
 * 
 */
function amem_assets_url( $path = '' ) {
	return plugin_dir_url( dirname( __FILE__ ) ) . 'assets/' . $path;
}

/**
 *  Create a basic nonce input
 *
 *  @since 	1.0
 *  
 */
function amem_nonce_input( $nonce = '_amem_nonce' ) {
	echo '<input type="hidden" name="_amem_nonce" value="' . esc_attr( wp_create_nonce( $nonce ) ) . '" />';
}

/**
 * Validate nonce from amem_nonce_input()
 * 
 * @since 	1.0
 * 
 */
function amem_verify_nonce( $value = '_amem_nonce' ) {
	$nonce = acf_maybe_get_POST( $value );

	if ( ! $nonce || ! wp_verify_nonce( $nonce, $value ) ) {
		return false;
	}

	return true;
}

/**
 * Check AJAX request is valid
 * 
 * @since 	1.0
 *  
 */
function amem_verify_ajax() {
	if ( function_exists('acf_verify_ajax') )
		return acf_verify_ajax();

	// bail early if not acf nonce
	if ( empty( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field($_REQUEST['nonce']), 'acf_nonce' ) ) { // phpcs:disable WordPress.Security.NonceVerification
		return false;
	}

	// action for 3rd party customization
	do_action( 'acf/verify_ajax' );

	// return
	return $ret;
}

function amem_sanitize_input( $data = false ) {
	if ( ! $data )
		return $data;

	if ( is_array( $data ) ) {
		return amem_sanitize_array( $data );
	} else {
		return wp_kses_post( $data );
	}
}

function amem_sanitize_array($data = array()) {
	if ( !is_array($data) || !count($data) ) {
		return array();
	}

	foreach ($data as $k => $v) {
		if ( !is_array($v) && !is_object($v) ) {
			$v = str_replace( [ '<[', ']>' ], [ '{-', '-}' ], $v ); 
			$v = wp_kses_post($v);
			$v = str_replace( [ '{-', '-}' ], [ '<[', ']>' ], $v ); 
			$data[$k] = $v;
		}
		if ( is_array($v) ) {
			$data[$k] = amem_sanitize_array($v);
		}
	}

	return $data;
}

/**
 * Sanitize vars with non-wp_kses
 * 
 * @param  array $vars 
 * @return array
 */
function amem_sanitize_vars($vars, $sanitize=null) {
	if ( !is_array($vars) ) {
		if ( is_string($vars) ) {
			switch( $sanitize ) {
				case 'kses':
				case 'kses_post':
				return wp_kses_post($vars);

				case 'number':
				case 'int':
				return absint($vars);
				break;

				case 'text':
				default:
				return sanitize_text_field($vars);
				break;
			}
		}
		return $vars;
	}

	$new_vars = [];
	foreach( $vars as $k => $v ) {
		$k = sanitize_key($k);
		if ( is_array($v) ) {
			$new_vars[$k] = amem_sanitize_vars($v, $sanitize);
		} else if ( is_string($v) ) {
			switch( $k ) {
				case 'body':
				case 'content':
				case 'message':
				case 'info':
				case 'msg':
				case 'instructions':
				$new_vars[$k] = amem_sanitize_vars( $v, ($sanitize ? $sanitize : 'kses') );
				break;
				default:
				$new_vars[$k] = amem_sanitize_vars($v, ($sanitize ? $sanitize : null));
				break;
			}
		} else {
			$new_vars[$k] = $v;
		}
	}

	return $new_vars;
}

/**
 * Sanitize $_POST or options data with defined sanitizers
 * @since  v0.9.16
 * 
 * @param  array $data       
 * @param  array  $sanitizers
 * @return array
 */
function amem_sanitize_data( $data, $sanitizers = [] ) {
	foreach( $data as $name => $val ) {
		$name = sanitize_key( $name );
		switch ($name) {
			// specific amem options
			case 'accform':
			case 'regform':
			if( isset($val['rules']) ){
				$i = 0;
				foreach ($val['rules'] as $key => $value) {
					$data[$name]['rules'][$i] = amem_sanitize_vars($value);
					unset($data[$name]['rules'][$key]);
					$i = $i + 1;
				}
			}
			break;

			case 'email':
			foreach ($val as $key => $mail_option) {
				if( isset($val[$key]['body'])) {
					$data[$name][$key]['body']  = amem_sanitize_html($mail_option['body']);
				}
			}
			break;

			case 'avatar':
			$sizes = trim( sanitize_text_field($data[$name]['avatar_sizes']) );
			if ( $sizes ) {
				$sizes = explode(',', $sizes );
				$sizes = array_filter( array_map('intval', $sizes), function($v) {
					return $v >= 80 && $v <= 512;
				} );

				sort($sizes);
				$sizes = implode(',', $sizes );
			}

			if ( !$sizes ) {
				$sizes = '96,150,300';
			}
			$data[$name]['avatar_sizes'] = $sizes;

			if ( amem()->avatar )
				$data[$name]['default_avatar'] = amem()->avatar->save_default_avatar($data[$name]['default_avatar']);
			break;

			default:
			// default sanitizers
			$sanitizers = $sanitizers ? $sanitizers : amem()->options->sanitizers();
			if ( isset($sanitizers[$name]) ) {
				switch ( $sanitizers[$name] ) {
					case 'int':
					if ( is_array( $val ) ) {
						$data[$name] = map_deep( $val, 'intval' );
					} else {
						$data[$name] = (int) $val;
					}
					break;

					case 'absint':
					if ( is_array( $val ) ) {
						$data[$name] = map_deep( $val, 'absint' );
					} else {
						$data[$name] = absint( $val );
					}
					break;

					case 'empty_absint':
					if ( is_array( $val ) ) {
						$data[$name] = map_deep( $val, 'absint' );
					} else {
						$data[$name] = ( '' !== $val ) ? absint( $val ) : '';
					}
					break;
					
					case 'key':
					if ( is_array( $val ) ) {
						$data[$name] = map_deep( $val, 'sanitize_key' );
					} else {
						$data[$name] = sanitize_key( $val );
					}
					break;

					case 'bool':
					if ( is_array($val) ) {
						$data[$name] = map_deep( $val, 'boolval' );
					} else {
						$data[$name] = (bool) $val;
					}
					break;

					case 'url':
					if ( is_array( $val ) ) {
						$data[$name] = map_deep( $val, 'esc_url_raw' );
					} else {
						$data[$name] = esc_url_raw( $val );
					}
					break;

					case 'wp_kses':
					if ( is_array( $val ) ) {
						$data[$name] = map_deep( $val, 'wp_kses_post' );
					} else {
						$data[$name] = wp_kses_post( $val );
					}
					break;

					case 'textarea':
					if ( is_array( $val ) ) {
						$data[$name] = map_deep( $val, 'sanitize_textarea_field' );
					} else {
						$data[$name] = sanitize_textarea_field( $val );
					}
					break;

					case 'text':
					if ( is_array( $val ) ) {
						$data[$name] = map_deep( $val, 'sanitize_text_field' );
					} else {
						$data[$name] = sanitize_text_field( $val );
					}
					break;

					case 'array_text':
					if ( $val )
						$data[$name] = map_deep( $val, 'sanitize_text_field' );
					else
						$data[$name] = [];
					break;

					case 'email_body':
					case 'html':
					if ( is_array( $val ) ) {
						$data[$name] = map_deep( $val, 'amem_sanitize_html' );
					} else {
						$data[$name] = amem_sanitize_html( $val );
					}
					break;

					case is_array($sanitizers[$name]):
					$data[$name] = amem_sanitize_data( $val, $sanitizers[$name] );
					break;

					default:
					$data[$name] = apply_filters( 'amem/sanitize/data/method=' . $sanitizers[$name] , $val );
					break;

				}
			} else {
				$data[$name] = apply_filters( 'amem/sanitize/data/name=' . $name, $val );
			}
			break;
		}
	}

	return $data;
}

/**
 * Sanitize HTML or Email Body
 * @since  v0.9.16
 *
 * @param  string $body 
 * @return string       
 */
function amem_sanitize_html($body) {
	$wp_default_protocols = wp_allowed_protocols();
	$protocols = array_merge( $wp_default_protocols, array( 'data' ) );

	return wp_kses( stripslashes( $body ), 'post', $protocols );
}

/**
 * Format and escape message text to print
 * 
 * @param  string  $message
 * @param  boolean $shortcode
 * @return string  Formatted and escaped string
 */
function amem_format_message( $message, $shortcode = false, $autop = true ) {
	if ( $shortcode )
		$message = do_shortcode( $message );
	$message = wptexturize( $message );
	if ( $autop ) {
		$message = wpautop( $message );
	}
	return wp_kses_post( $message );
}

/**
 * Get current url
 *
 * @since 1.0
 */
function amem_get_current_url() {
	if ( wp_doing_ajax() ) {
		if ( !empty($_REQUEST['amem_origin_url']) ) // phpcs:disable WordPress.Security.NonceVerification
			$url = sanitize_text_field($_REQUEST['amem_origin_url']); // phpcs:disable WordPress.Security.NonceVerification
		else 
			$url = wp_get_referer();
	} else {
		$url = acf_get_current_url();
	}

	$url = remove_query_arg( ['updated', 'errc', 'key', 'hash', 'login'], $url );

	return $url;
}

/**
 * Returns true if current screen id is match given
 * 
 * @param  mixed $id
 * @return bool
 * @since 1.0
 */
function amem_is_screen( $id = '' ) {
	// bail early if not defined
	if ( ! function_exists( 'get_current_screen' ) ) {
		return false;
	}

	// vars
	$current_screen = get_current_screen();

	// no screen
	if ( ! $current_screen ) {
		return false;

		// array
	} elseif ( is_array( $id ) ) {
		return in_array( $current_screen->id, $id );

		// string
	} else {
		return ( $id === $current_screen->id );
	}
}

function amem_is_front(){
  if ( !is_admin() || ( is_admin() && wp_doing_ajax() && (acf_maybe_get_POST('_acf_screen') === 'acfe_form' || acf_maybe_get_POST('_acf_screen') === 'acf_form') ) )
    return true;
  
  return false;
}

function amem_array_insert_after($array, $key, $new_key, $new_value = null){
  if ( !is_array($array) || !isset($array[ $key ]) ) {
		return $array;
  }
  
  $is_sequential = acf_is_sequential_array($array);
  $new_array = array();
  
  foreach( $array as $k => $value ) {
    if ($is_sequential) {
      $new_array[] = $value;
    } else {
      $new_array[ $k ] = $value;
    }
    
    if ( $k === $key ) {
      if ( $is_sequential ) {
        $new_value = $new_value === null ? $new_key : $new_value;
        $new_array[] = $new_value;
      } else {
        if ( $new_value === null && is_array($new_key) ) {
          reset($new_key);
          $new_value = current($new_key);
          $new_key = key($new_key);
        }
        $new_array[$new_key] = $new_value;
      }
    }
  }
  
  return $new_array;
}

function amem_is_pro() {
	return defined( 'AMEM_PRO' ) && AMEM_PRO;
}

<?php 
/*
 * Helper Function Extension Pro
 * @since 4.3.0
 */
defined('ABSPATH') or die();

class Nexter_Ext_Helper_Func {
    
	protected $options = [];

	/**
     * Constructor
     */
    public function __construct() {
		$this->options = get_option('nexter_extra_ext_options', []);
    }

	/**
	 * Remove HTML tags and their content from a string,
	 * excluding specific patterns like Freemius submenu items.
	 *
	 * @param string|null $input Input string to clean.
	 * @return string Sanitized string with HTML tags and content removed.
	 */
	public function remove_html_tags_and_content( $input ) {
		if ( is_null( $input ) || '' === $input ) {
			return '';
		}

		// Skip stripping if input contains Freemius submenu marker to avoid empty labels.
		if ( strpos( $input, 'fs-submenu-item' ) === false ) {
			// Remove HTML tags and their enclosed content
			// Reference: https://stackoverflow.com/a/39320168
			$input = preg_replace( '@<(\w+)\b[^>]*>.*?</\1>@si', '', $input );
		}

		// Strip any remaining HTML or PHP tags
		return strip_tags( $input );
	}

	/**
	 * Retrieve admin menu items hidden via toggle, excluding those hidden for all roles.
	 *
	 * @return array Decoded menu item IDs hidden by toggle.
	 */
	public function get_hidden_admin_menus_by_toggle() {
		$hidden_menu_ids = [];

		$admin_menu_settings = isset( $this->options['admin_menu'] ) ? $this->options['admin_menu'] : [];
		
		if ( empty( $admin_menu_settings['menu_always_hidden'] ) ) {
			return $hidden_menu_ids;
		}

		$hidden_menus_raw = json_decode( stripslashes( $admin_menu_settings['menu_always_hidden'] ), true );
		if ( ! is_array( $hidden_menus_raw ) ) {
			return $hidden_menu_ids;
		}

		foreach ( $hidden_menus_raw as $raw_menu_id => $visibility_rules ) {
			$hide_by_toggle = ! empty( $visibility_rules['hide_by_toggle'] );
			$hide_for_all_roles = isset( $visibility_rules['always_hide_for'] ) && $visibility_rules['always_hide_for'] === 'all-roles';

			if ( $hide_by_toggle && ! $hide_for_all_roles ) {
				$hidden_menu_ids[] = $this->decode_admin_menu_id( sanitize_text_field( $raw_menu_id ) );
			}
		}

		return $hidden_menu_ids;
	}

	/**
	 * Extended wp_kses ruleset to support SVG and embedded PDF viewer elements.
	 *
	 * @return array Allowed HTML tags and attributes.
	 */
	public function get_extended_kses_rules() {
		$allowed_html = wp_kses_allowed_html( 'post' );

		$svg_tags = [
			'svg'    => [
				'class'            => true,
				'aria-hidden'      => true,
				'aria-labelledby'  => true,
				'role'             => true,
				'xmlns'            => true,
				'width'            => true,
				'height'           => true,
				'viewbox'          => true,
				'viewBox'          => true,
			],
			'g'      => [
				'fill'             => true,
				'fill-rule'        => true,
				'stroke'           => true,
				'stroke-width'     => true,
				'stroke-linejoin'  => true,
				'stroke-linecap'   => true,
			],
			'title'  => [ 'title' => true ],
			'path'   => [
				'd'                => true,
				'fill'             => true,
				'stroke'           => true,
				'stroke-width'     => true,
				'stroke-linejoin'  => true,
				'stroke-linecap'   => true,
			],
			'rect'   => [
				'width'            => true,
				'height'           => true,
				'x'                => true,
				'y'                => true,
				'rx'               => true,
				'ry'               => true,
				'fill'             => true,
				'stroke'           => true,
				'stroke-width'     => true,
				'stroke-linejoin'  => true,
				'stroke-linecap'   => true,
			],
			'circle' => [
				'cx'               => true,
				'cy'               => true,
				'r'                => true,
				'stroke'           => true,
				'stroke-width'     => true,
				'stroke-linejoin'  => true,
				'stroke-linecap'   => true,
			],
		];

		$embed_tags = [
			'style'  => true,
			'script' => [ 'src' => true ],
		];

		return array_merge( $allowed_html, $svg_tags, $embed_tags );
	}

	/**
	 * Get submenu items hidden via toggle (excluding items hidden for all roles).
	 *
	 * @return array Decoded submenu IDs hidden by toggle.
	 */
	public function get_toggle_hidden_submenus() {
		$hidden_submenus = [];

		$options_menu = isset( $this->options['admin_menu'] ) ? $this->options['admin_menu'] : array();
		$admin_menu_options = isset($options_menu['submenu_always_hidden']) ? $options_menu['submenu_always_hidden'] : '';

		$submenu_data = json_decode( stripslashes( $admin_menu_options ), true );

		if ( is_array( $submenu_data ) ) {
			foreach ( $submenu_data as $submenu_id => $info ) {
				if (
					! empty( $info['hide_by_toggle'] ) &&
					( empty( $info['always_hide'] ) || ( $info['always_hide_for'] ?? '' ) !== 'all-roles' )
				) {
					$hidden_submenus[] = $this->decode_admin_menu_id( $submenu_id );
				}
			}
		}

		return $hidden_submenus;
	}

	/**
	 * Determine if the menu item is a custom item (menu or separator).
	 *
	 * @param string $menu_id Menu item ID.
	 * @return string 'yes' if custom, 'no' otherwise.
	 */
	public function is_custom_admin_menu_item( $menu_id ) {
		$options_menu = isset( $this->options['admin_menu'] ) ? $this->options['admin_menu'] : array();
		$custom_menus_json = isset($options_menu['change_menu_new_separators']) ? $options_menu['change_menu_new_separators'] : '';

		if (is_string($custom_menus_json)) {
			$custom_menus = json_decode(stripslashes($custom_menus_json), true);
		} elseif (is_array($custom_menus_json)) {
			$custom_menus = $custom_menus_json;
		} else {
			$custom_menus = [];
		}

		$custom_menu_ids = is_array( $custom_menus ) ? array_keys( $custom_menus ) : [];

		return in_array( $menu_id, $custom_menu_ids, true ) ? 'yes' : 'no';
	}

	/**
	 * Decode admin menu ID to original URL format.
	 */
	public function decode_admin_menu_id( $encoded_id ) {
		$search  = [ '_______', '______', '_____', '____', '___', '__' ];
		$replace = [ '=/','/', '&', '=', '?', '.' ];
		return str_replace( $search, $replace, $encoded_id );
	}

	/**
	 * Encode admin menu URL to a safe ID format.
	 */
	public function encode_admin_menu_id( $original_id ) {
		$search  = [ '.', '?', '=/','=', '&', '/' ];
		$replace = [ '__', '___', '_______', '____', '_____', '______' ];
		return str_replace( $search, $replace, $original_id );
	}
	
	/**
	 * Get user capabilities for which the "Show All/Less" menu toggle should be visible.
	 * @return array List of capabilities.
	 */
	public function get_capabilities_for_toggle_visibility() {
		global $menu, $submenu;

		$capabilities = [];

		// Parent menu items hidden by toggle
		$hidden_menus = $this->get_hidden_admin_menus_by_toggle();

		foreach ( $menu as $item ) {
			$menu_id = ( strpos( $item[4], 'wp-menu-separator' ) !== false ) ? $item[2] : $item[5];

			if ( in_array( $menu_id, $hidden_menus, true ) && isset( $item[1] ) ) {
				$capabilities[] = $item[1]; // Capability required
			}
		}

		// Premium-only: hidden submenus
		$hidden_submenus = $this->get_toggle_hidden_submenus();

		foreach ( $submenu as $parent_key => $submenu_items ) {
			foreach ( $submenu_items as $submenu_item ) {
				$title = isset( $submenu_item[0] ) ? sanitize_title( $submenu_item[0] ) : '';
				$url_fragment = $submenu_item[2] ?? '';
				$id = $parent_key . '_-_' . $title . '_-_' . strlen( $url_fragment );

				if ( in_array( $id, $hidden_submenus, true ) && isset( $submenu_item[1] ) ) {
					$capabilities[] = $submenu_item[1];
				}
			}
		}

		return array_unique( $capabilities );
	}

	/**
	 * Retrieve URL fragments of admin menu/submenu items that are always hidden.
	 */
	public function get_always_hidden_admin_menu_fragments() {
		$menu_fragments = [];

		$admin_menu_options = isset( $this->options['admin_menu'] ) ? $this->options['admin_menu'] : array();

		// Helper to extract hidden items
		$extract_hidden_fragments = function( $key ) use ( $admin_menu_options ) {
			$fragments = [];
			if ( isset( $admin_menu_options[ $key ] ) ) {
				$data = json_decode( stripslashes( $admin_menu_options[ $key ] ), true );
				if ( is_array( $data ) ) {
					foreach ( $data as $item ) {
						if (
							! empty( $item['always_hide'] )
							&& ! empty( $item['menu_url_fragment'] )
						) {
							$fragments[] = $item['menu_url_fragment'];
						}
					}
				}
			}
			return $fragments;
		};

		// Merge hidden parent and submenu fragments
		$menu_fragments = array_merge(
			$extract_hidden_fragments( 'menu_always_hidden' ),
			$extract_hidden_fragments( 'submenu_always_hidden' )
		);

		return array_values( array_filter( $menu_fragments ) );
	}

	/**
	 * Get the user roles for which the menu item is hidden.
	 *
	 * @param string  $menu_item_id    Unique ID of the menu/submenu item.
	 * @param boolean $is_parent_menu  Whether the item is a top-level menu.
	 * @return array  List of role slugs.
	 */
	public function get_hidden_roles_for_menu_item( $menu_item_id, $is_parent_menu ) {
		$hidden_roles = [];

		if ( empty( $menu_item_id ) ) {
			return $hidden_roles;
		}

		$admin_menu_options = $this->options['admin_menu'] ?? [];

		$key = $is_parent_menu ? 'menu_always_hidden' : 'submenu_always_hidden';
		$raw_hidden_data = $admin_menu_options[ $key ] ?? '';

		$hidden_data = json_decode( stripslashes( $raw_hidden_data ), true );

		if ( ! is_array( $hidden_data ) ) {
			return $hidden_roles;
		}

		if ( isset( $hidden_data[ $menu_item_id ]['which_roles'] ) && is_array( $hidden_data[ $menu_item_id ]['which_roles'] ) ) {
			$hidden_roles = array_values( $hidden_data[ $menu_item_id ]['which_roles'] );
		}

		return $hidden_roles;
	}


}

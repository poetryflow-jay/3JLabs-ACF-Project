<?php
/**
 * Menu item class.
 *
 * @package Advanced Members for ACF
 */

namespace AMem\Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Item {

	/**
	 * Get item options.
	 *
	 * @param int $item_id Item ID.
	 * @return array
	 */
	public static function get_options( $item_id = 0 ) {
		// Fetch all rules for this menu item.
		$item_options = get_post_meta( $item_id, '_amem_nav_item', true );

		return static::parse_options( $item_options );
	}

	/**
	 * Parse options.
	 *
	 * @param array $options Array of options to parse.
	 *
	 * @return array
	 */
	public static function parse_options( $options = [] ) {
		if ( ! is_array( $options ) ) {
			$options = [];
		}

		return wp_parse_args( $options, [
			'filter_users'   => '',
			'show_hide'       => 'show',
			'roles'         => [],
		] );
	}

}

<?php
/**
 * Menu items class.
 *
 * @package User Menus
 */

namespace AMem\Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

amem_include( 'core/modules/menu/class-item.php' );

use AMem\Menu\Item;
use AMem\Addon;

class Items extends Addon {

	/** @var object Current item */
	private static $current_item;

	public function __construct() {
		add_filter( 'wp_setup_nav_menu_item', [ $this, 'merge_item_data' ] );

		add_filter( 'wp_get_nav_menu_items', [ $this, 'exclude_menu_items' ] );
	}

	public static function get_types($only_keys = true) {
		static $types;

		if ( !isset($types) ) {
			$types = [
				'login' => __( 'Login', 'advanced-members' ), 
				'register' => __( 'Register', 'advanced-members' ), 
				'logout' => __( 'Logout', 'advanced-members' ), 
				'password-reset' => __( 'Password Reset', 'advanced-members' ), 
				'account' => __( 'Account', 'advanced-members' ), 
				'account-password' => __( 'Change Password', 'advanced-members' ), 
				'account-delete' => __( 'Delete Account', 'advanced-members' )
			];
		}

		return $only_keys ? array_keys($types) : $types;
	}

	/**
	 * Merge Item data into the $item object.
	 *
	 * @param object $item Item object.
	 * @return mixed
	 */
	public function merge_item_data( $item ) {
		self::$current_item = $item;

		// Merge Rules.
		foreach ( Item::get_options( $item->ID ) as $key => $value ) {
			$item->$key = $value;
		}

		// if ( in_array( $item->object, static::get_types(), true ) ) {
		// 	$item->type_label = __( 'Advanced Members for ACF Menu', 'advanced-members' );
		// 	$item->url = amem_get_core_page( $item->object, false, 'current' );
		// }

		// User text replacement.
		if ( ! is_admin() ) {
			$item->title = amem_convert_tags( $item->title );
		}

		return $item;
	}

	public function exclude_menu_items( $items = [] ) {
		if ( empty( $items ) || is_admin() ) {
			return $items;
		}

		$logged_in = is_user_logged_in();

		$excluded = [];

		foreach ( $items as $key => $item ) {
			// Exclude menu items that are children of excluded items.
			$exclude = in_array( (int) $item->menu_item_parent, $excluded, true );

			// if ( 'logout' === $menu_type ) {
			// 	$exclude = ! $logged_in;
			// } elseif ( 'login' === $menu_type || 'register' === $menu_type ) {
			// 	$exclude = $logged_in;
			// } else {
			// 	if ( is_object( $item ) && isset( $item->filter_users ) ) {
			// 		switch ( $item->filter_users ) {
			// 			case 'logged_in':
			// 				if ( ! $logged_in ) {
			// 					$exclude = true;
			// 				} elseif ( ! empty( $item->roles ) ) {

			// 					$can_see = 'show' === $item->show_hide;
			// 					$allowed_by_role = ! $can_see;

			// 					foreach ( $item->roles as $role ) {
			// 						if ( current_user_can( $role ) ) {
			// 							$allowed_by_role = $can_see;
			// 							break;
			// 						}
			// 					}

			// 					if ( ! $allowed_by_role ) {
			// 						$exclude = true;
			// 					}
			// 				}
			// 				break;

			// 			case 'logged_out':
			// 				$exclude = $logged_in;
			// 				break;
			// 		}
			// 	}
			// }

			if ( is_object( $item ) && isset( $item->filter_users ) ) {
				switch ( $item->filter_users ) {
					case 'logged_in':
					if ( ! $logged_in ) {
						$exclude = true;
					} elseif ( ! empty( $item->roles ) ) {
						$allowed_by_role = false;
						foreach ( $item->roles as $role ) {
							if ( current_user_can( $role ) ) {
								$allowed_by_role = true;
								break;
							}
						}

						if ( ! $allowed_by_role ) {
							$exclude = true;
						}
					}
					break;

					case 'logged_out':
					$exclude = $logged_in;
					break;
				}
			}

			$exclude = apply_filters( 'amem/menu/exclude_item', $exclude, $item );

			if ( $exclude ) {
				$excluded[] = $item->ID; // for parent item exclude check
				unset( $items[ $key ] );
			}
		}
		return $items;
	}

}

amem()->register_addon( 'menu/items', Items::getInstance() );

if ( is_admin() ) {
	amem_include( 'admin/class-menu.php' );
}
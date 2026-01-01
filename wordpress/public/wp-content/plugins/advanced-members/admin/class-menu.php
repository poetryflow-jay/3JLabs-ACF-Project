<?php
/**
 * Menu settings
 *
 * @package Advanced Members for ACF
 */

namespace AMem\Admin;

use AMem\Menu\Items;
use AMem\Menu\Item;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Menu {

	static function init() {
		add_action( 'wp_nav_menu_item_custom_fields', [ __CLASS__, 'fields' ], 10, 4 );
		add_action( 'wp_update_nav_menu_item', [ __CLASS__, 'save' ], 10, 2 );
		// add_action( 'admin_head-nav-menus.php', [ __CLASS__, 'print_scripts' ], 50 );
		add_action( 'admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_script'] );

		/** @todo Supports Customizer menu settings and save when WP core supports plugins */
		if ( amem()->is_dev() ) {
			add_action( 'wp_nav_menu_item_custom_fields_customize_template', [ __CLASS__, 'custom_fields_customize_template' ] );
			// add_action( 'customize_controls_print_footer_scripts', [ __CLASS__, 'print_scripts' ] );
		}
	}

	static function enqueue_admin_script($hook_suffix) {
	    if ($hook_suffix !== 'nav-menus.php') {
	        return;
	    }

	    amem_register_script( 'amem-menus', amem_get_url("amem-menus{$min}.js", 'assets/js'), ['jquery'], AMEM_VERSION, ['in_footer' => true, 'asset_path' => amem_get_path('', 'assets/js')] );

	    wp_enqueue_script( 'amem-menus' );
	}

	/**
	 * Render fields for each menu item.
	 *
	 * @param int    $item_id Item ID.
	 * @param object $item Item object.
	 * @param int    $depth Current menu item depth.
	 * @param array  $args Additional array of arguments.
	 */
	public static function fields( $item_id, $item, $depth, $args ) {
		$allowed_user_roles = amem_allowed_roles();

		wp_nonce_field( 'amem-menu-nonce', 'amem-menu-nonce' );
		$filter_users = [
			''           => __( 'Everyone', 'advanced-members' ),
			'logged_out' => __( 'Logged Out Users', 'advanced-members' ),
			'logged_in'  => __( 'Logged In Users', 'advanced-members' ),
		];

		$none_amem = true;
		$menu_type = false;
		$roleCSS = (isset($item->filter_users) && $item->filter_users == 'logged_in') ? '' : ' acf-hidden';

		// if ( $item->object == 'page' ) {
		// 	$menu_type = array_search( $item->object_id, amem()->options->get_core_pages() );
		// }

		?>
		<div class="amem-nav-edit">
			<div class="clear"></div>
			<h4 style="margin-bottom: 0.6em;"><?php esc_html_e( 'Advanced Members Settings', 'advanced-members' ) ?></h4>
		<?php
		if ( $menu_type ) : ?>
			<p class="nav_item_options-filter_users  description  description-wide">

				<label for="amem_nav_item-filter_users-<?php echo esc_attr( $item->ID ); ?>">

					<?php echo esc_html( __( 'Who can see this menu?', 'advanced-members' ) ); ?>

				</label>

				<select n id="amem_nav_item-filter_users-<?php echo esc_attr( $item->ID ); ?>" class="widefat" disabled="disabled">
					<option>
					<?php
					if ( in_array($menu_type, ['logout', 'account', 'account-password', 'account-delete']) ) {
						echo esc_html( $filter_users['logged_in'] );
					} else {
						echo esc_html( $filter_users['logged_out'] );
					}
					?>
					</option>
				</select>

			</p>

		<?php elseif ( $none_amem ) : ?>

			<p class="amem-nav-mode description description-wide">

				<label for="amem-filter_users-<?php echo esc_attr( $item->ID ); ?>">

					<?php echo esc_html( __( 'Who can see this menu?', 'advanced-members' ) ); ?><br />

					<select name="amem_nav_item[<?php echo esc_attr( $item->ID ); ?>][filter_users]" id="amem_nav_item-filter_users-<?php echo esc_attr( $item->ID ); ?>" class="widefat amem_nav_item-filter_users">
							<?php foreach ( $filter_users as $option => $label ) : ?>
							<option value="<?php echo esc_attr($option); ?>" <?php /*phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ selected( $option, $item->filter_users ); ?>>
									<?php echo esc_html( $label ); ?>
							</option>
						<?php endforeach; ?>
					</select>

				</label>

			</p>

			<p class="amem-nav-roles description description-wide<?php echo esc_attr($roleCSS); ?>">
				<span><?php esc_html_e( 'Select the user roles that can see this menu', 'advanced-members' ) ?></span>
				<br>

				<span class="amem-nav-roles-list">
				<?php foreach ( $allowed_user_roles as $option => $label ) : ?>
					<label> <input type="checkbox" name="amem_nav_item[<?php echo esc_attr( $item->ID ); ?>][roles][]" value="<?php echo esc_attr($option); ?>" <?php /*phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ checked( in_array( esc_attr( $option ), $item->roles, true ), true ); ?>/>
						<?php echo esc_html( $label ); ?>
					</label>
				<?php endforeach; ?>
				</span>
			</p>

			<?php
		endif;

		?>
		</div>
		<?php
	}

	/**
	 * Save menu item data.
	 *
	 * @param int $menu_id Menu ID.
	 * @param int $item_id Item ID.
	 */
	public static function save( $menu_id, $item_id ) {
		$allowed_roles = amem_allowed_roles();

		if ( empty( $_POST['amem_nav_item'][ $item_id ] ) || ! isset( $_POST['amem-menu-nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['amem-menu-nonce'] ) ), 'amem-menu-nonce' ) ) {
			return;
		}

		/* phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized */
		$item_options = Item::parse_options( amem_sanitize_vars( $_POST['amem_nav_item'][ $item_id ] ) );

		if ( 'logged_in' === $item_options['filter_users'] ) {
			// Validate chosen roles and remove non-allowed roles.
			foreach ( (array) $item_options['roles'] as $key => $role ) {
				if ( ! array_key_exists( $role, $allowed_roles ) ) {
					unset( $item_options['roles'][ $key ] );
				}
			}
		} else {
			unset( $item_options['roles'] );
		}

		// Remove empty options to save space.
		$item_options = array_filter( $item_options );

		if ( ! empty( $item_options ) ) {
			update_post_meta( $item_id, '_amem_nav_item', $item_options );
		} else {
			delete_post_meta( $item_id, '_amem_nav_item' );
		}
	}

	static function custom_fields_customize_template() {
		$allowed_user_roles = amem_allowed_roles();

		wp_nonce_field( 'amem-menu-nonce', 'amem-menu-nonce' );
		$filter_users = [
			''           => __( 'Everyone', 'advanced-members' ),
			'logged_out' => __( 'Logged Out Users', 'advanced-members' ),
			'logged_in'  => __( 'Logged In Users', 'advanced-members' ),
		];
		?>
		<# 
		var filterUsers = <?php echo wp_json_encode($filter_users) ?>,
		filterRoles = <?php echo wp_json_encode($allowed_user_roles) ?>,
		menuItemAMem = _wpCustomizeSettings.settings['nav_menu_item['+ data.menu_item_id +']'] || {},
		amemFilterUser = menuItemAMem.value.filter_users || {},
		amemFilterRole = menuItemAMem.value.roles || {},
		roleCSS = (amemFilterUser != 'logged_in') ? 'acf-hidden' : '';
		#>
		<div class="amem-nav-edit">
			<div class="clear"></div>

			<h4 style="margin-bottom: 0.6em;"><?php esc_html_e( 'Advanced Members Settings', 'advanced-members' ) ?></h4>

			<p class="amem-nav-mode description description-wide">

				<label for="amem-filter_users-{{ data.menu_item_id }}">

					<?php echo esc_html( __( 'Who can see this menu?', 'advanced-members' ) ); ?>
					<br />

					<select name="amem_nav_item[{{ data.menu_item_id }}][filter_users]" id="edit-menu-item-filter_users-{{ data.menu_item_id }}" class="widefat">
						<# _.each( filterUsers, function(field, key) { 
							selected = (key == amemFilterUser) ? 'selected="selected"' : '';
						#>
						<option value="{{ key }}" {{ selected }}>{{ field }}</option>
						<# } ); #>
					</select>

				</label>

			</p>

			<p class="amem-nav-roles description description-wide {{ roleCSS }}">
				<span><?php esc_html_e( 'Select the user roles that can see this menu', 'advanced-members' ) ?></span>
				<br>

				<span class="amem-nav-roles-list">
				<# _.each( filterRoles, function(value, key, $) {
					is_checked = _.some(amemFilterRole, function(f) {
						return f == key;
					});
					checked = (is_checked) ? 'checked="checked"' : '';
				#>
				<label> <input type="checkbox" name="amem_nav_item[{{ data.menu_item_id }}][roles][]" value="{{ key }}" {{ checked }}/>
					{{ value }}
				</label>

				<# } ); #>
				</span>
			</p>

		</div>
		<?php
	}
}

Menu::init();

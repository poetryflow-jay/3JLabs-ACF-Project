<?php 
/*
 * Admin Menu Organizer Extension Pro
 * @since 4.3.0
 */
defined('ABSPATH') or die();

class Nexter_Ext_Pro_Admin_Menu_Organize {
    
	protected $amo_option = [];

    /**
     * Constructor
     */
    public function __construct() {
		global $wp_post_types;
		$this->amo_option = get_option('nexter_extra_ext_options', []);
		add_action( 'admin_menu', [ $this, 'add_menu_item' ] );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_custom_admin_assets' ) );
		$admin_menu_options = isset( $this->amo_option['admin_menu'] ) ? $this->amo_option['admin_menu'] : array();
		if ( array_key_exists( 'custom_menu_order', $admin_menu_options ) ) {
			add_filter( 'custom_menu_order', '__return_true', PHP_INT_MAX );
			add_filter( 'menu_order', [ $this, 'reorder_admin_menu_items' ], PHP_INT_MAX );
		}

		if ( array_key_exists( 'menu_titles', $admin_menu_options ) && !empty($admin_menu_options['menu_titles'])) {
			add_action( 'admin_menu', [ $this, 'customize_admin_menu_titles' ], 9999999996 );

			$menu_titles = explode( ',', $admin_menu_options['menu_titles'] );

			foreach ( $menu_titles as $title_entry ) {
				if ( str_contains( $title_entry, 'menu-posts__' ) ) {
					[ , $custom_title ] = explode( '__', $title_entry );

					$default_title = $wp_post_types['post']->label;

					if ( $default_title !== $custom_title ) {
						add_filter( 'post_type_labels_post', [ $this, 'replace_default_post_labels' ] );
						add_action( 'init', [ $this, 'update_post_type_labels' ] );
						add_action( 'admin_menu', [ $this, 'update_post_menu_labels' ], PHP_INT_MAX );
						add_action( 'admin_bar_menu', [ $this, 'update_admin_bar_post_label' ], 80 );
					}
				}
			}
		}

		if ( array_key_exists( 'change_menu_hidden', $admin_menu_options ) 
			|| array_key_exists( 'menu_always_hidden', $admin_menu_options ) 
		) {
			add_action( 'admin_menu', [ $this, 'maybe_hide_admin_menu_items' ], 9999999997 );
			add_action( 'admin_menu', [ $this, 'maybe_add_menu_toggle_buttons' ], 9999999998 );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_hidden_menu_toggle_script' ] );
			add_action( 'plugins_loaded', [ $this, 'assign_cf7_capabilities_to_roles' ] );
			add_action( 'current_screen', [ $this, 'restrict_admin_page_access_by_role' ] );
		}

		add_action( 'wp_ajax_nxt_save_admin_menu', [ $this, 'ajax_save_admin_menu_settings' ] );
	
		if ( array_key_exists( 'submenu_always_hidden', $admin_menu_options ) ) {
			add_action( 'admin_menu', [ $this, 'hide_admin_submenu_items' ], 9999999996 );
		}
		
		if ( array_key_exists( 'change_menu_new_separators', $admin_menu_options ) ) {
			add_action( 'admin_menu', [ $this, 'register_admin_menu_separators' ], 9999999996 );
		}
		add_action( 'wp_ajax_nxt_reset_admin_menu', [ $this, 'reset_admin_menu_settings' ] );
		add_action( 'admin_head', [ $this, 'admin_menu_organizer_css' ] );
    }


	public function admin_menu_organizer_css(){
		?>
		<style type="text/css">
		/* Admin Interface >> Admin Menu Organizer */
		#adminmenuwrap {
			height: auto !important;
		}

		.current.menu-top.hidden,
		.wp-has-current-submenu.hidden {
			display: list-item;
		}

		#adminmenu a.menu-top.hidden,
		ul#adminmenu a.wp-has-current-submenu.hidden {
			display: block;
		}
		.always-hidden {
			display: none !important;
		}

		#adminmenu .wp-submenu a.hidden {
			display: none;
		}
		</style>
		<?php
	}

	/**
     * Add Admin Menu item Settings menu
     */
    public function add_menu_item() {
		add_submenu_page(
			'nexter_welcome',
			__( 'Admin Menu', 'nexter-pro-extensions' ),
			__( 'Admin Menu', 'nexter-pro-extensions' ),
			'manage_options',
			'nxt-admin-menu-organizer',
			array( $this, 'admin_menu_settings_page' ) 
		);
    }

	public function admin_menu_settings_page(){
        ?>
        <div class="wrap">
			<div class="nxt-admin-menu-organizer">
				
				<div class="nxt-admin-menu-header">
					<h3 class="nxt-heading-org"><?php echo esc_html__( 'Admin Menu Organizer', 'nexter-pro-extensions' ); ?></h3>
					<div class="nxt-amo-btn-action">
						<div class="nxt-amo-saving-data" style="display:none;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#2271b1" d="M12 1a11 11 0 1 0 11 11A11 11 0 0 0 12 1Zm0 19a8 8 0 1 1 8-8 8 8 0 0 1-8 8Z" opacity=".25"/><path fill="#2271b1" d="M12 4a8 8 0 0 1 7.89 6.7 1.53 1.53 0 0 0 1.49 1.3 1.5 1.5 0 0 0 1.48-1.75 11 11 0 0 0-21.72 0A1.5 1.5 0 0 0 2.62 12a1.53 1.53 0 0 0 1.49-1.3A8 8 0 0 1 12 4Z"><animateTransform attributeName="transform" dur="0.75s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12"/></path></svg></div>
						<div class="nxt-amo-saved-data" style="display:none;"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"><path fill="#2e8b57" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM9.29 16.29 5.7 12.7a.996.996 0 1 1 1.41-1.41L10 14.17l6.88-6.88a.996.996 0 1 1 1.41 1.41l-7.59 7.59a.996.996 0 0 1-1.41 0z"/></svg></div>
						<button id="nxt-amo-save-data" class="button button-primary button-large"><?php echo esc_html__( 'Save Changes', 'nexter-pro-extensions' ); ?></button>
					</div>
				</div>
				<div class="admin-menu-sortables-wrapper">
					<?php $this->nxt_admin_menu_render(); ?>
				</div>
			</div>
        </div>
        <?php
	}

	public function load_custom_admin_assets( $hook_suffix ) {
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'nxt-admin-menu-organizer' ) {
			wp_enqueue_style( 'nxt-admin-organizer', NXT_PRO_EXT_URI . 'assets/css/nxt-admin-organizer.css', array(), NXT_PRO_EXT_VER, 'all' );

			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-draggable' );

			wp_enqueue_script( 'nxt-custom-admin-menu', NXT_PRO_EXT_URI . 'assets/js/custom-admin-menu.js', array( 'jquery-ui-draggable' ), NXT_PRO_EXT_VER, false );

			wp_enqueue_script( 'nxt-admin-menu-organizer', NXT_PRO_EXT_URI . 'assets/js/admin-menu-organizer.js', array( 'nxt-custom-admin-menu' ), NXT_PRO_EXT_VER, false );

			wp_localize_script( 
				'nxt-admin-menu-organizer', 
				'nxt_admin_org_data',
				array(
					'amoSaveNonce' => wp_create_nonce( 'nxt-admin-menu-nonce' ),
					'amoResetNonce' => wp_create_nonce( 'nxt-reset-menu-nonce' ),
				)
			);
		}
	}

	/**
	 * Render sortable menu field
	 *
	 * @since 2.0.0
	 */
	function nxt_admin_menu_render() {
		$triangle_right_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" class="nxt-plus-icon" viewBox="0 0 16 16"><path stroke="#1A1A1A" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M12 6s-2.946 4-4 4c-1.054 0-4-4-4-4" fill="none" /></svg><svg class="nxt-minus-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 18 18"><path stroke="#1A1A1A" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M13.5 11.25s-3.314-4.5-4.5-4.5-4.5 4.5-4.5 4.5" fill="none"/></svg>';
		?>
		<ul id="nxt-admin-menu-org" class="menu ui-sortable">
		<?php
			global $menu, $submenu;
			
			$helper_func = new Nexter_Ext_Helper_Func;		
			
			$options = isset( $this->amo_option['admin_menu'] ) ? $this->amo_option['admin_menu'] : array();

			// Set menu items to be excluded from title renaming. These are from WordPress core.
			$renaming_not_allowed = array( 
				'menu-dashboard', 
				'menu-pages', 
				// 'menu-posts', 
				'menu-media', 
				'menu-comments', 
				'menu-appearance', 
				'menu-plugins', 
				'menu-users', 
				'menu-tools', 
				'menu-settings' 
			);

			// Get custom menu item titles
			if ( array_key_exists( 'menu_titles', $options ) ) {
				$menu_titles = $options['menu_titles'];

				if ( is_string( $menu_titles ) ) {
					$menu_titles = explode( ',', $menu_titles );
				} elseif ( ! is_array( $menu_titles ) ) {
					$menu_titles = array();
				}
				// If already an array, keep as is
			} else {
				$menu_titles = array();
			}


			// Get data on menu items to be hidden by user roles
			if ( isset( $options['menu_always_hidden'] ) ) {
				$menu_always_hidden = $options['menu_always_hidden'];
				$menu_always_hidden = json_decode( $menu_always_hidden, true );
			} else {
				$menu_always_hidden = array();
			}

			// Get menu items hidden by toggle
			$menu_hidden_by_toggle = $helper_func->get_hidden_admin_menus_by_toggle();

			$i = 1;

			// Check if there's an existing custom menu order data stored in options

			if ( array_key_exists( 'custom_menu_order', $options ) ) {

				$custom_menu = $options['custom_menu_order'] ?? '';

				if (is_string($custom_menu)) {
					$custom_menu = explode(',', $custom_menu);
				} elseif (!is_array($custom_menu)) {
					$custom_menu = array();
				}

				$menu_key_in_use = array();

				// Render sortables with data in custom menu order

				foreach ( $custom_menu as $custom_menu_item ) {

					foreach ( $menu as $menu_key => $menu_info ) {

						if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
							
							$menu_item_title = $menu_info[2];
							$menu_item_id = $menu_info[2];
							$submenu_sortable_id = '';
							$menu_url_fragment = '';
						} else {
							$menu_item_title = $menu_info[0];
							$menu_item_id = $menu_info[5];
							$submenu_sortable_id = $menu_info[2];
							$menu_url_fragment = $menu_info[2];
						}			        	

						// Check if submenu exists
						if ( array_key_exists( $menu_info[2], $submenu ) && @is_countable( $submenu[$menu_info[2]] ) && @sizeof( $submenu[$menu_info[2]] ) > 0 ) {
							$submenu_exists = true;
						} else {
							$submenu_exists = false;						
						}
						
						if ( $custom_menu_item == $menu_item_id ) {

							$menu_item_id_transformed = $helper_func->encode_admin_menu_id( $menu_item_id );

							$is_custom_menu = $helper_func->is_custom_admin_menu_item( $menu_item_id );						
							
							?>
							<li id="<?php echo esc_attr( $menu_item_id ); ?>" class="menu-item parent-menu-item menu-item-depth-0" data-custom-menu-item="<?php echo esc_attr( $is_custom_menu ); ?>">
								<div class="menu-item-bar">
									<div class="menu-item-handle">
										<span class="menu-drag-drop"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" fill="none" viewBox="0 0 8 12"><path stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1.5h.005M1 6h.005M1 10.5h.005m5.99-9H7M6.995 6H7m-.005 4.5H7"/></svg></span>
										<div class="item-title">
											<div class="title-wrapper">
												<span class="menu-item-title">
												<?php
												if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
													
													$separator_name_ori = $menu_info[2];
													$separator_name = str_replace( 'separator', 'Separator-', $separator_name_ori );
													$separator_name = str_replace( '--last', '-Last', $separator_name );
													$separator_name = str_replace( '--woocommerce', '--WooCommerce', $separator_name );
													echo '-- ' . esc_html( $separator_name ) . ' --';
												} else {
													if ( in_array( $menu_item_id, $renaming_not_allowed ) ) {
														$menu_item_title = $menu_info[0];
														echo wp_kses_post( $helper_func->remove_html_tags_and_content( $menu_item_title ) );
													} else {

														// Get defaul/custom menu item title
														foreach ( $menu_titles as $custom_menu_title ) {

															// At this point, $custom_menu_title value looks like toplevel_page_snippets__Code Snippets

															$custom_menu_title = explode( '__', $custom_menu_title );

															if ( $custom_menu_title[0] == $menu_item_id ) {
																$menu_item_title = $helper_func->remove_html_tags_and_content( $custom_menu_title[1] ); // e.g. Code Snippets
																break; // stop foreach loop so $menu_item_title is not overwritten in the next iteration
															} else {
																$menu_item_title = $helper_func->remove_html_tags_and_content( $menu_info[0] );
															}

														}

														?>
														<input type="text" value="<?php echo wp_kses_post( $menu_item_title ); ?>" class="menu-item-custom-title" data-menu-item-id="<?php echo esc_attr( $menu_item_id ); ?>">
														<?php
													}
												}
												?>
												</span><!-- end of .menu-item-title -->
											<?php
											if ( $submenu_exists ) {
												?>
												<div class="submenu-toggle"><span class="toggle-submenu-open"><?php echo wp_kses( $triangle_right_icon, $helper_func->get_extended_kses_rules() ); ?></span><span class="submenu-text"><?php echo esc_html__( '[ Submenu ]', 'nexter-pro-extensions' ); ?></span></div>
												<?php
											}
											?>
											</div><!-- end of .title-wrapper -->
											<div class="options-for-hiding">
												<?php
													$hide_text = __( 'Hide', 'nexter-pro-extensions' );
													$checkbox_class = 'hide-parent-menu-checkbox';
												?>
												<label class="menu-item-checkbox-label">
													<?php
														if ( in_array( $custom_menu_item, $menu_hidden_by_toggle ) ) {
														?>
													<input type="checkbox" id="hide-status-for-<?php echo esc_attr( $menu_item_id_transformed ); ?>" class="<?php echo esc_attr( $checkbox_class ); ?>" data-menu-item-title="<?php echo esc_attr( $helper_func->remove_html_tags_and_content( $menu_item_title ) ); ?>" data-menu-item-id="<?php echo esc_attr( $menu_item_id_transformed ); ?>" data-menu-item-id-ori="<?php echo esc_attr( $menu_item_id ); ?>" data-menu-url-fragment="<?php echo esc_attr( $menu_url_fragment ); ?>" checked>
													<span><?php //echo esc_html( $hide_text ); ?></span>
														<?php
														} else {
														?>
													<input type="checkbox" id="hide-status-for-<?php echo esc_attr( $menu_item_id_transformed ); ?>" class="<?php echo esc_attr( $checkbox_class ); ?>" data-menu-item-title="<?php echo esc_attr( $helper_func->remove_html_tags_and_content( $menu_item_title ) ); ?>" data-menu-item-id="<?php echo esc_attr( $menu_item_id_transformed ); ?>" data-menu-item-id-ori="<?php echo esc_attr( $menu_item_id ); ?>" data-menu-url-fragment="<?php echo esc_attr( $menu_url_fragment ); ?>">
													<span><?php //echo esc_html( $hide_text ); ?></span>
														<?php
														}
													?>
												</label>
												<div class="options-toggle" data-menu-item-id="<?php echo esc_attr( $menu_item_id_transformed ); ?>">
													<span class="toggle-submenu-open"><?php echo wp_kses( $triangle_right_icon, $helper_func->get_extended_kses_rules() ); ?></span>
												</div>
											</div><!-- end of .options-for-hiding -->
										</div><!-- end of .item-title -->
									</div><!-- end of .menu-item-handle -->
								</div><!-- end of .menu-item-bar -->
								<?php
							
								$menu_required_capability = $menu_info[1];
								
								$always_hide_checked_status = '';
								$all_or_selected_roles = '';
								if ( is_array( $menu_always_hidden ) && ! empty( $menu_always_hidden ) ) {
									foreach( $menu_always_hidden as $hidden_menu_item_id => $info ) {
										$hidden_menu_item_id = $helper_func->decode_admin_menu_id( $hidden_menu_item_id );
										if ( $hidden_menu_item_id == $menu_item_id) {
											if ( isset( $info['always_hide'] ) && $info['always_hide'] ) {
												$always_hide_checked_status = ' checked';
											}				
											if ( isset( $info['always_hide_for'] ) && $info['always_hide_for'] ) {
												$all_or_selected_roles = $info['always_hide_for'];
											}

										}
									}									
								}

								$this->render_admin_menu_hide_options( $menu_item_id, $menu_required_capability, $always_hide_checked_status, $all_or_selected_roles, true );

							$i = 1;

							if ( $submenu_exists ) {
								// Render submenu
								$menu_item_id = $menu_info[2];
								$this->render_sortable_submenu_items( $submenu, $menu_item_id, $submenu_sortable_id );
							}
							?>
								<div class="remove-menu-item"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"><path fill="#bbbbbb" d="M24 2.4L21.6 0L12 9.6L2.4 0L0 2.4L9.6 12L0 21.6L2.4 24l9.6-9.6l9.6 9.6l2.4-2.4l-9.6-9.6z"/></svg></div>
							</li>
							<?php
							$menu_key_in_use[] = $menu_key;
						}
					}
				}

				// Render the rest of the current menu towards the end of the sortables

				foreach ( $menu as $menu_key => $menu_info ) {

					if ( ! in_array( $menu_key, $menu_key_in_use ) ) {

						if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
							$menu_item_id = $menu_info[2];
							$menu_url_fragment = '';
						} else {
							$menu_item_id = $menu_info[5];
							$menu_url_fragment = $menu_info[2];
						}			        	
						$submenu_sortable_id = $menu_info[2];
						$menu_item_title = $menu_info[0];

						// Check if submenu exists
						if ( array_key_exists( $menu_info[2], $submenu ) && @is_countable( $submenu[$menu_info[2]] ) && @sizeof( $submenu[$menu_info[2]] ) > 0 ) {
							$submenu_exists = true;
						} else {
							$submenu_exists = false;						
						}
					
						// Strip tags
						$menu_item_title = $helper_func->remove_html_tags_and_content( $menu_item_title );

						// Exclude Show All/Less toggles

						if ( false === strpos( $menu_item_id, 'toplevel_page_nexter_' ) ) {

							$menu_item_id_transformed = $helper_func->encode_admin_menu_id( $menu_item_id );

							$is_custom_menu = $helper_func->is_custom_admin_menu_item( $menu_item_id );						
							
							?>
							<li id="<?php echo esc_attr( $menu_item_id ); ?>" class="menu-item parent-menu-item menu-item-depth-0" data-custom-menu-item="<?php echo esc_attr( $is_custom_menu ); ?>">
								<div class="menu-item-bar">
									<div class="menu-item-handle">
										<span class="menu-drag-drop"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" fill="none" viewBox="0 0 8 12"><path stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1.5h.005M1 6h.005M1 10.5h.005m5.99-9H7M6.995 6H7m-.005 4.5H7"/></svg></span>
										<div class="item-title">
											<div class="title-wrapper">
												<span class="menu-item-title">
													<?php
													if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
														$separator_name_ori = $menu_info[2];
														$separator_name = str_replace( 'separator', 'Separator-', $separator_name_ori );
														$separator_name = str_replace( '--last', '-Last', $separator_name );
														$separator_name = str_replace( '--woocommerce', '--WooCommerce', $separator_name );
														echo '-- ' . esc_html( $separator_name ) . ' --';
													} else {
													?>
														<input type="text" value="<?php echo wp_kses_post( $menu_item_title ); ?>" class="menu-item-custom-title" data-menu-item-id="<?php echo esc_attr( $menu_item_id ); ?>">
													<?php													
													}
													?>
												</span>
												<?php
												if ( $submenu_exists ) {
												?>
													<div class="submenu-toggle"><span class="toggle-submenu-open"><?php echo wp_kses( $triangle_right_icon, $helper_func->get_extended_kses_rules() ); ?></span><span class="submenu-text"><?php echo esc_html__( '[ Submenu ]', 'nexter-pro-extensions' ); ?></span></div>
												<?php
												}
												?>
											</div>
											<div class="options-for-hiding">
												<?php
													$hide_text = __( 'Hide', 'nexter-pro-extensions' );
													$checkbox_class = 'hide-parent-menu-checkbox';
												?>
												<label class="menu-item-checkbox-label">
													<input type="checkbox" id="hide-status-for-<?php echo esc_attr( $menu_item_id_transformed ); ?>" class="<?php echo esc_attr( $checkbox_class ); ?>" data-menu-item-title="<?php echo esc_attr( $helper_func->remove_html_tags_and_content( $menu_item_title ) ); ?>" data-menu-item-id="<?php echo esc_attr( $menu_item_id_transformed ); ?>" data-menu-item-id-ori="<?php echo esc_attr( $menu_item_id ); ?>" data-menu-url-fragment="<?php echo esc_attr( $menu_url_fragment ); ?>">
													<span><?php //echo esc_html( $hide_text ); ?></span>
												</label>
												
												<div class="options-toggle" data-menu-item-id="<?php echo esc_attr( $menu_item_id_transformed ); ?>">
													<span class="toggle-submenu-open"><?php echo wp_kses( $triangle_right_icon, $helper_func->get_extended_kses_rules() ); ?></span>
												</div>
												
											</div><!-- end of .options-for-hiding -->
										</div><!-- end of .item-title -->
									</div><!-- end of .menu-item-handle -->
								</div><!-- end of .menu-item-bar -->
								<?php
								$menu_required_capability = $menu_info[1];
								$always_hide_checked_status = '';
								$all_or_selected_roles = '';
								$this->render_admin_menu_hide_options( $menu_item_id, $menu_required_capability, $always_hide_checked_status, $all_or_selected_roles, true );

							$i = 1;

							if ( $submenu_exists ) {
								// Render submenu
								$menu_item_id = $menu_info[2];
								$this->render_sortable_submenu_items( $submenu, $menu_item_id, $submenu_sortable_id );
							}
							?>
								<div class="remove-menu-item"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"><path fill="#bbbbbb" d="M24 2.4L21.6 0L12 9.6L2.4 0L0 2.4L9.6 12L0 21.6L2.4 24l9.6-9.6l9.6 9.6l2.4-2.4l-9.6-9.6z"/></svg></div>
							</li><!-- end of .menu-item -->
							<?php
						}
					}
				}
			} else { // No custom menu order has been saved yet

				// Render sortables with existing items in the admin menu

				foreach ( $menu as $menu_key => $menu_info ) {

						if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
							$menu_item_id = $menu_info[2];
							$submenu_sortable_id = '';
							$menu_url_fragment = '';
						} else {
							$menu_item_id = $menu_info[5];
							$submenu_sortable_id = $menu_info[2];
							$menu_url_fragment = $menu_info[2];
						}			        	
						$menu_item_title = $menu_info[0];

						// Check if submenu exists
						if ( array_key_exists( $menu_info[2], $submenu ) && @is_countable( $submenu[$menu_info[2]] ) && @sizeof( $submenu[$menu_info[2]] ) > 0 ) {
							$submenu_exists = true;
						} else {
							$submenu_exists = false;						
						}
					

					$menu_item_id_transformed = $helper_func->encode_admin_menu_id( $menu_item_id );

					// Strip tags
					$menu_item_title = $helper_func->remove_html_tags_and_content( $menu_item_title );

					$is_custom_menu = $helper_func->is_custom_admin_menu_item( $menu_item_id );

					?>
					<li id="<?php echo esc_attr( $menu_item_id ); ?>" class="menu-item parent-menu-item menu-item-depth-0" data-custom-menu-item="<?php echo esc_attr( $is_custom_menu ); ?>">
						<div class="menu-item-bar">
							<div class="menu-item-handle">
								<span class="menu-drag-drop"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" fill="none" viewBox="0 0 8 12"><path stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1.5h.005M1 6h.005M1 10.5h.005m5.99-9H7M6.995 6H7m-.005 4.5H7"/></svg></span>
								<div class="item-title">
									<div class="title-wrapper">
										<span class="menu-item-title">
										<?php
										if ( false !== strpos( $menu_info[4], 'wp-menu-separator' ) ) {
											$separator_name_ori = $menu_info[2];
											$separator_name = str_replace( 'separator', 'Separator-', $separator_name_ori );
											$separator_name = str_replace( '--last', '-Last', $separator_name );
											$separator_name = str_replace( '--woocommerce', '--WooCommerce', $separator_name );
											echo '-- ' . esc_html( $separator_name ) . ' --';
										} else {
											if ( in_array( $menu_item_id, $renaming_not_allowed ) ) {
													echo wp_kses_post( $menu_item_title );
											} else {
												?>
												<input type="text" value="<?php echo wp_kses_post( $menu_item_title ); ?>" class="menu-item-custom-title" data-menu-item-id="<?php echo esc_attr( $menu_item_id ); ?>">
												<?php
											}
										}
										?>
										</span>
										<?php
										if ( $submenu_exists ) {
											?>
											<div class="submenu-toggle"><span class="toggle-submenu-open"><?php echo wp_kses( $triangle_right_icon, $helper_func->get_extended_kses_rules() ); ?></span><span class="submenu-text"><?php echo esc_html__( '[ Submenu ]', 'nexter-pro-extensions' ); ?></span></div>
											<?php
										}
										?>
									</div><!-- end of .title-wrapper -->
									<div class="options-for-hiding">
										<?php
										$hide_text = __( 'Hide', 'nexter-pro-extensions' );
										$checkbox_class = 'hide-parent-menu-checkbox';
										?>
										<label class="menu-item-checkbox-label">
											<input type="checkbox" id="hide-status-for-<?php echo esc_attr( $menu_item_id_transformed ); ?>" class="<?php echo esc_attr( $checkbox_class ); ?>" data-menu-item-title="<?php echo esc_attr( $helper_func->remove_html_tags_and_content( $menu_item_title ) ); ?>" data-menu-item-id="<?php echo esc_attr( $menu_item_id_transformed ); ?>" data-menu-item-id-ori="<?php echo esc_attr( $menu_item_id ); ?>" data-menu-url-fragment="<?php echo esc_attr( $menu_url_fragment ); ?>">
											<span><?php //echo esc_html( $hide_text ); ?></span>
										</label>
										<div class="options-toggle" data-menu-item-id="<?php echo esc_attr( $menu_item_id_transformed ); ?>">
											<span class="toggle-submenu-open"><?php echo wp_kses( $triangle_right_icon, $helper_func->get_extended_kses_rules() ); ?></span>
										</div>
									</div><!-- end of .options-for-hiding -->
								</div><!-- end of .item-title -->
							</div><!-- end of .menu-item-handle -->
						</div><!-- end of .menu-item-bar -->
					<?php
					$menu_required_capability = $menu_info[1];					
					$always_hide_checked_status = '';
					$all_or_selected_roles = '';
					$this->render_admin_menu_hide_options( $menu_item_id, $menu_required_capability, $always_hide_checked_status, $all_or_selected_roles, true );

					$i = 1;

					if ( $submenu_exists ) {
						// Render submenu
						$menu_item_id = $menu_info[2];
						$this->render_sortable_submenu_items( $submenu, $menu_item_id, $submenu_sortable_id );
					}
					?>
						<div class="remove-menu-item"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"><path fill="#bbbbbb" d="M24 2.4L21.6 0L12 9.6L2.4 0L0 2.4L9.6 12L0 21.6L2.4 24l9.6-9.6l9.6 9.6l2.4-2.4l-9.6-9.6z"/></svg></div>
					</li>
					<?php
				}
			}
		?>
		</ul>

		<div class="admin-menu-actions-wrapper">
			<div class="admin-menu-main-actions">
				<a id="nxt-add-menu-separator" class="admin-menu-action" href="#"><?php echo esc_html__( 'Add Separator', 'nexter-pro-extensions' ); ?></a>
			</div>
			<div class="reset-menu-wrapper">
				<img src="<?php echo esc_url( NXT_PRO_EXT_URI . 'assets/images/processing.svg' ); ?>" class="reset-menu-loader" style="display: none;" /><a href="#" id="nxt-amo-reset-menu"><?php echo esc_html__( 'Reset Menu', 'nexter-pro-extensions' ); ?></a>
			</div>
		</div>
		<?php

        $field_id = 'custom_menu_order';
		$field_option_value = ( isset( $options[$field_id] ) ) ? $options[$field_id] : '';

		echo '<input type="hidden" id="' . esc_attr( $field_id ) . '" class="nxt-ext-subfield-text" name="' . esc_attr( $field_id ) . '" value="' . esc_attr( $field_option_value ) . '">';

		// Hidden input field to store hidden menu items (from options as is, or 'Hide' checkbox clicks) upon clicking Save Changes.
		$this->nexter_output_admin_menu_hidden_field( 'change_menu_hidden' );

		// Hidden input field to store custom menu titles (from options as is, or custom values entered on each non-WP-default menu items.
		$this->nexter_output_admin_menu_hidden_field( 'menu_titles' );

		// Hidden input field to store menu items that should always be hidden for all or some roles upon clicking Save Changes.
		$this->nexter_output_admin_menu_hidden_field( 'menu_always_hidden' );
		
		// Hidden input field to store custom submenu items order upon clicking Save Changes.
		$this->nexter_output_admin_menu_hidden_field( 'change_submenus_order' );
		
		// Hidden input field to store submenu items that should always be hidden for all or some roles upon clicking Save Changes.
		$this->nexter_output_admin_menu_hidden_field( 'submenu_always_hidden' );
		
		// Hidden input field to store new separators upon clicking Save Changes.
		$this->nexter_output_admin_menu_hidden_field( 'change_menu_new_separators' );
		
	}
	
	/**
	 * Output a hidden input field for Admin Menu Organizer settings.
	 *
	 */
	public function nexter_output_admin_menu_hidden_field( $field_key ) {
		// Retrieve options safely
		
		$admin_menu_options = ! empty( $this->amo_option['admin_menu'] ) ? $this->amo_option['admin_menu'] : [];

		// Extract field value
		if (isset($admin_menu_options[$field_key])) {
			$field_value = is_string($admin_menu_options[$field_key])
				? stripslashes($admin_menu_options[$field_key])
				: $admin_menu_options[$field_key];  // keep array as is
		} else {
			$field_value = '';
		}

		// Prepare the value for output safely
		$print_value = is_string($field_value) ? stripslashes($field_value) : '';

		// Output sanitized hidden input
		printf(
			'<input type="hidden" id="%1$s" class="nxt-ext-subfield-text" name="%1$s" value="%2$s">',
			esc_attr($field_key),
			esc_attr($print_value)
		);

	}

	/**
	 * Render the options panel to hide admin menus by user roles or toggle.
	 *
	 * @param string  $menu_id               Original menu item ID.
	 * @param string  $required_cap          Required capability to view the menu.
	 * @param string  $always_hide_status    Checkbox status for always hiding.
	 * @param string  $role_condition        all-roles | all-roles-except | selected-roles
	 * @param boolean $is_parent_menu        Whether this is a parent menu.
	 */
	public function render_admin_menu_hide_options( $menu_id, $required_cap, $always_hide_status, $role_condition, $is_parent_menu = true ) {

		$helper = new Nexter_Ext_Helper_Func;

		// Encode menu ID
		$encoded_id = $helper->encode_admin_menu_id( $menu_id );
		$hidden_parents = $helper->get_hidden_admin_menus_by_toggle();
		$hide_toggle_checked = ( ! empty( $hidden_parents ) && in_array( $menu_id, $hidden_parents, true ) ) ? ' checked' : '';

		// Handle submenu visibility toggle
		if ( ! $is_parent_menu && strpos( $menu_id, '_-_') !== false ) {
			$parts = explode( '_-_', $menu_id );
			$decoded_parent = $helper->decode_admin_menu_id( $parts[0] ) . '_-_' . $parts[1];
			$hidden_submenus = $helper->get_toggle_hidden_submenus();
			$hide_toggle_checked = ( ! empty( $hidden_submenus ) && in_array( $decoded_parent, $hidden_submenus, true ) ) ? ' checked' : '';
		}

		// Determine menu context
		$menu_type = $is_parent_menu ? 'parent' : 'sub';
		$settings_class = $is_parent_menu ? 'parent-menu' : 'submenu';
		$toggle_class = $is_parent_menu ? 'hide-until-toggled-checkbox' : 'hide-until-toggled-submenu-checkbox';

		// Role condition status
		$all_roles_status = $role_condition === 'all-roles' ? ' checked' : '';
		$all_except_status = $role_condition === 'all-roles-except' ? ' checked' : '';
		$selected_roles_status = $role_condition === 'selected-roles' ? ' checked' : '';

		// Handle capability mapping for Tags
		if ( $required_cap === 'manage_post_tags' ) {
			$required_cap = 'manage_categories';
		}

		?>
		<div id="options-for-<?php echo esc_attr( $encoded_id ); ?>" class="menu-item-settings <?php echo esc_attr( $settings_class ); ?>-item-settings wp-clearfix" style="display:none;">
			
			<!-- Toggle Visibility -->
			<div class="hide-toggle-or-always">
				<label class="menu-item-checkbox-label">
					<input type="checkbox" id="hide-until-toggled-for-<?php echo esc_attr( $encoded_id ); ?>" class="<?php echo esc_attr( $toggle_class ); ?>" data-menu-item-id="<?php echo esc_attr( $encoded_id ); ?>"<?php echo esc_attr( $hide_toggle_checked ); ?>>
					<span><?php esc_html_e( 'Hide until toggled', 'nexter-pro-extensions' ); ?></span>
				</label>
			</div>

			<!-- Always Hide for Role(s) -->
			<div id="all-roles-selected-opt-<?php echo esc_attr( $encoded_id ); ?>" class="all-selected-roles-options" data-menu-item-id="<?php echo esc_attr( $menu_id ); ?>">
				<label class="menu-item-checkbox-label">
					<input type="checkbox" id="hide-by-role-for-<?php echo esc_attr( $encoded_id ); ?>" class="hide-by-role-checkbox" data-menu-item-id="<?php echo esc_attr( $encoded_id ); ?>" data-menu-type="<?php echo esc_attr( $menu_type ); ?>"<?php echo esc_attr( $always_hide_status ); ?>>
					<span><?php esc_html_e( 'Always hide for user role(s)', 'nexter-pro-extensions' ); ?></span>
				</label>

				<!-- Role Condition Radios -->
				<fieldset id="all-roles-selected-radio-<?php echo esc_attr( $encoded_id ); ?>" style="display:none;">
					<div>
						<input type="radio" id="all-roles-for-<?php echo esc_attr( $encoded_id ); ?>" class="all-selected-roles-radios" name="hide-for-<?php echo esc_attr( $encoded_id ); ?>" value="all-roles" data-menu-item-id="<?php echo esc_attr( $encoded_id ); ?>" data-menu-type="<?php echo esc_attr( $menu_type ); ?>"<?php echo esc_attr( $all_roles_status ); ?>>
						<label for="all-roles-for-<?php echo esc_attr( $encoded_id ); ?>"><?php esc_html_e( 'All Roles', 'nexter-pro-extensions' ); ?></label>
					</div>
					<div>
						<input type="radio" id="all-roles-except-for-<?php echo esc_attr( $encoded_id ); ?>" class="all-selected-roles-radios" name="hide-for-<?php echo esc_attr( $encoded_id ); ?>" value="all-roles-except" data-menu-item-id="<?php echo esc_attr( $encoded_id ); ?>" data-menu-type="<?php echo esc_attr( $menu_type ); ?>"<?php echo esc_attr( $all_except_status ); ?>>
						<label for="all-roles-except-for-<?php echo esc_attr( $encoded_id ); ?>"><?php esc_html_e( 'All Roles Except', 'nexter-pro-extensions' ); ?></label>
					</div>
					<div>
						<input type="radio" id="selected-roles-for-<?php echo esc_attr( $encoded_id ); ?>" class="all-selected-roles-radios" name="hide-for-<?php echo esc_attr( $encoded_id ); ?>" value="selected-roles" data-menu-item-id="<?php echo esc_attr( $encoded_id ); ?>" data-menu-type="<?php echo esc_attr( $menu_type ); ?>"<?php echo esc_attr( $selected_roles_status ); ?>>
						<label for="selected-roles-for-<?php echo esc_attr( $encoded_id ); ?>"><?php esc_html_e( 'Selected Roles', 'nexter-pro-extensions' ); ?></label>
					</div>
				</fieldset>
			</div>

			<!-- Roles List -->
			<div id="hide-for-roles-<?php echo esc_attr( $encoded_id ); ?>" class="hide-for-roles-options" data-menu-item-id="<?php echo esc_attr( $encoded_id ); ?>" data-menu-type="<?php echo esc_attr( $menu_type ); ?>" style="display:none;">
				<?php
				global $wp_roles;
				$roles = $wp_roles->get_names();
				$roles_to_hide = [];

				if ( in_array( $role_condition, [ 'all-roles-except', 'selected-roles' ], true ) ) {
					$roles_to_hide = $helper->get_hidden_roles_for_menu_item( $encoded_id, $is_parent_menu );
				}

				foreach ( $roles as $role_slug => $role_name ) {
					$role_obj = get_role( $role_slug );
					$capabilities = is_object( $role_obj ) ? array_keys( $role_obj->capabilities ) : [];

					if ( in_array( $required_cap, $capabilities, true ) ) {
						$checked = in_array( $role_slug, $roles_to_hide, true ) ? ' checked' : '';
						echo '<label class="menu-item-checkbox-label">';
						echo '<input type="checkbox" class="menu-item-checkbox role-checkbox" data-role="' . esc_attr( $role_slug ) . '"' . esc_attr( $checked ) . '>';
						echo '<span>' . esc_html( $role_name ) . '</span>';
						echo '</label>';
					}
				}
				?>
			</div>

			<!-- Capability Info -->
			<div id="menu-req-capability-<?php echo esc_attr( $encoded_id ); ?>" class="menu-required-capability" data-menu-item-id="<?php echo esc_attr( $encoded_id ); ?>" style="display:none;">
				<?php 
				echo wp_kses_post(
					sprintf(
						// Translators: %s is the required capability name (e.g., 'edit_posts').
						__( 'The role(s) specified above have sufficient <code>%s</code> access to view this menu item.', 'nexter-pro-extensions' ),
						esc_html( $required_cap )
					)
				); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the sortable submenu items for Admin Menu Organizer
	 */
	public function render_sortable_submenu_items( $submenu, $parent_menu_id, $sortable_submenu_id ) {
		$icons_toggle = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" class="nxt-plus-icon" viewBox="0 0 16 16"><path stroke="#1A1A1A" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M12 6s-2.946 4-4 4c-1.054 0-4-4-4-4" fill="none" /></svg><svg class="nxt-minus-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 18 18"><path stroke="#1A1A1A" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M13.5 11.25s-3.314-4.5-4.5-4.5-4.5 4.5-4.5 4.5" fill="none"/></svg>';

		$helper = new Nexter_Ext_Helper_Func;
		$sortable_submenu_id = $helper->encode_admin_menu_id( $sortable_submenu_id );
		?>
		<div class="submenu-wrapper" style="display:none;">
			<ul id="<?php echo esc_attr( $sortable_submenu_id ); ?>" class="submenu-sortable">
				<?php
				foreach ( $submenu[ $parent_menu_id ] as $key => $item ) {
					$title_raw = isset( $item[0] ) ? $item[0] : '';
					$title_clean = str_replace(
						[ '↳', '➤', '⭐️', '✛', '👋', '<img draggable="false" role="img" class="emoji" alt="👋" src="https://s.w.org/images/core/emoji/15.0.3/svg/1f44b.svg">' ],
						'',
						$title_raw
					);
					$title_slug = sanitize_title( $title_clean );
					$capability = isset( $item[1] ) ? $item[1] : '';
					$item_id = isset( $item[2] ) ? $item[2] : '';
					$id_length = strlen( $item_id );
					$url_fragment = $item_id;

					$unique_id = "{$sortable_submenu_id}_-_" . $title_slug . "_-_" . $id_length;
					$options_extra = isset( $this->amo_option['admin_menu'] ) ? $this->amo_option['admin_menu'] : array();
					$hidden_settings = isset( $options_extra['submenu_always_hidden'] )
						? json_decode( $options_extra['submenu_always_hidden'], true )
						: [];

					$is_always_hidden = '';
					$hidden_roles = '';

					if ( isset( $hidden_settings[ $unique_id ] ) ) {
						if ( ! empty( $hidden_settings[ $unique_id ]['always_hide'] ) ) {
							$is_always_hidden = ' checked';
						}
						if ( ! empty( $hidden_settings[ $unique_id ]['always_hide_for'] ) ) {
							$hidden_roles = $hidden_settings[ $unique_id ]['always_hide_for'];
						}
					}
					?>
					<li id="<?php echo esc_attr( $item_id ); ?>" class="menu-item menu-item-depth-0">
						<div class="menu-item-bar">
							<div class="menu-item-handle">
								<span class="menu-drag-drop"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" fill="none" viewBox="0 0 8 12"><path stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1.5h.005M1 6h.005M1 10.5h.005m5.99-9H7M6.995 6H7m-.005 4.5H7"/></svg></span>
								<div class="item-title">
									<div class="title-wrapper">
										<span class="menu-item-title"><?php echo wp_kses_post( $helper->remove_html_tags_and_content( $title_clean ) ); ?></span>
									</div>
									<div class="options-for-hiding">
										<label class="menu-item-checkbox-label">
											<input type="checkbox"
												id="hide-status-for-<?php echo esc_attr( $unique_id ); ?>"
												class="hide-submenu-checkbox"
												data-menu-item-title="<?php echo esc_attr( $helper->remove_html_tags_and_content( $title_clean ) ); ?>"
												data-menu-item-id="<?php echo esc_attr( $unique_id ); ?>"
												data-menu-item-id-ori="<?php echo esc_attr( $item_id ); ?>"
												data-menu-url-fragment="<?php echo esc_attr( $url_fragment ); ?>"
												<?php echo esc_attr($is_always_hidden); ?>
											>
											<span><?php //esc_html_e( 'Hide', 'nexter-pro-extensions' ); ?></span>
										</label>
										<div class="options-toggle" data-menu-item-id="<?php echo esc_attr( $unique_id ); ?>">
											<span class="toggle-submenu-open"><?php echo wp_kses( $icons_toggle, $helper->get_extended_kses_rules() ); ?></span>
										</div>
									</div><!-- .options-for-hiding -->
								</div><!-- .item-title -->
							</div><!-- .menu-item-handle -->
						</div><!-- .menu-item-bar -->
						<?php
						$this->render_admin_menu_hide_options(
							$unique_id,
							$capability,
							$is_always_hidden,
							$hidden_roles,
							false
						);
						?>
					</li>
					<?php
				}
				?>
			</ul><!-- .submenu-sortable -->
		</div><!-- .submenu-wrapper -->
		<?php
	}

	/**
	 * Reorder admin menu and submenu items based on saved settings.
	 *
	 * @param array $default_order Default WordPress menu order.
	 * @return array Reordered menu slugs.
	 * @link https://developer.wordpress.org/reference/hooks/menu_order/
	 */
	public function reorder_admin_menu_items( $default_order ) {
		global $menu, $submenu;

		$menu_config = isset($this->amo_option['admin_menu']) ? $this->amo_option['admin_menu'] : array();

		// Create map of current admin menu [unique_id => slug]
		$menu_map = array();
		foreach ( $menu as $entry ) {
			$unique_id = strpos( $entry[4], 'wp-menu-separator' ) !== false ? $entry[2] : $entry[5];
			$menu_map[ $unique_id ] = $entry[2];
		}

		// Load and sanitize custom top-level menu order
		$custom_menu_order = $menu_config['custom_menu_order'] ?? '';

		$saved_menu_ids = is_string($custom_menu_order)
		? array_map('trim', explode(',', $custom_menu_order))
		: (is_array($custom_menu_order) ? array_map('trim', $custom_menu_order) : []);


		// Build reordered top-level menu
		$ordered_menu = array();
		foreach ( $saved_menu_ids as $id ) {
			if ( isset( $menu_map[ $id ] ) ) {
				$ordered_menu[] = $menu_map[ $id ];
				unset( $menu_map[ $id ] );
			}
		}

		// Add remaining menu items (e.g., new plugin entries)
		$ordered_menu = array_merge( $ordered_menu, array_values( $menu_map ) );

		// Handle submenu order if premium is enabled
		$helper = new Nexter_Ext_Helper_Func();

		$saved_submenus_json = isset($menu_config['change_submenus_order']) ? $menu_config['change_submenus_order'] : '';
		$saved_submenus_array = json_decode( stripslashes( $saved_submenus_json ), true );

		if ( is_array( $saved_submenus_array ) ) {
			foreach ( $saved_submenus_array as $raw_key => $ordered_slugs ) {
				$parent_slug = $helper->decode_admin_menu_id( $raw_key );
				$ordered_slugs = array_map( 'trim', explode( ',', $ordered_slugs ) );

				if ( ! isset( $submenu[ $parent_slug ] ) ) {
					continue;
				}

				$original_items = $submenu[ $parent_slug ];
				$ordered_items = array();
				$added = array();

				// Apply saved submenu order
				foreach ( $ordered_slugs as $i => $slug ) {
					$slug_entity = htmlentities( $slug );
					foreach ( $original_items as $item ) {
						if ( isset( $item[2] ) && ( $item[2] === $slug || $item[2] === $slug_entity ) ) {
							$ordered_items[ $i ] = $item;
							$added[] = $item[2];
							break;
						}
					}
				}

				// Append unlisted submenu items
				foreach ( $original_items as $item ) {
					if ( isset( $item[2] ) && ! in_array( html_entity_decode( $item[2] ), $ordered_slugs, true ) ) {
						$ordered_items[] = $item;
					}
				}

				// Reassign ordered submenu
				ksort( $ordered_items );
				$submenu[ $parent_slug ] = array_values( $ordered_items );
			}
		}

		return $ordered_menu;
	}

	/**
	 * Customize WordPress admin menu item titles based on stored options.
	 */
	public function customize_admin_menu_titles() {
		global $menu;

		$admin_menu_options = isset($this->amo_option['admin_menu']) ? $this->amo_option['admin_menu'] : array();

		if ( empty( $admin_menu_options['menu_titles'] ) ) {
			return;
		}

		$custom_titles_raw = explode( ',', $admin_menu_options['menu_titles'] );
		$custom_titles_map = [];

		// Prepare a map for quick lookup: menu_id => custom_title
		foreach ( $custom_titles_raw as $entry ) {
			[ $menu_id, $custom_title ] = explode( '__', $entry );
			$custom_titles_map[ $menu_id ] = $custom_title;
		}

		foreach ( $menu as $key => $item ) {
			$menu_id = isset( $item[5] ) ? $item[5] : ( strpos( $item[4], 'wp-menu-separator' ) !== false ? $item[2] : null );

			if ( $menu_id && isset( $custom_titles_map[ $menu_id ] ) ) {
				$menu[ $key ][0] = $custom_titles_map[ $menu_id ];
			}
		}
	}

	/**
	 * Replace default 'Posts' labels with custom title.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/post_type_labels_post_type/
	 */
	public function replace_default_post_labels( $labels ) {
		$post_type_object = get_post_type_object( 'post' );

		if ( ! is_object( $post_type_object ) || ! isset( $post_type_object->labels ) ) {
			return $labels;
		}

		$default_plural   = $post_type_object->label ?? $post_type_object->labels->name;
		$default_singular = $post_type_object->labels->singular_name ?? $default_plural;

		$custom_title = $this->get_custom_post_menu_title();

		foreach ( $labels as $key => $value ) {
			if ( $value !== null ) {
				$labels->{$key} = str_replace(
					[ $default_plural, $default_singular ],
					$custom_title,
					$value
				);
			}
		}

		return $labels;
	}

	/**
	 * Get custom title for the 'Posts' menu item.
	 * @return string
	 */
	public function get_custom_post_menu_title() {
		$post_type_object = get_post_type_object( 'post' );
		$default_title = '';

		if ( is_object( $post_type_object ) ) {
			$default_title = $post_type_object->label ?? $post_type_object->labels->name ?? '';
		}

		$options = isset($this->amo_option['admin_menu']) ? $this->amo_option['admin_menu'] : array();
		$custom_titles = ! empty( $options['menu_titles'] )
			? explode( ',', $options['menu_titles'] )
			: [];

		foreach ( $custom_titles as $entry ) {
			if ( strpos( $entry, 'menu-posts__' ) !== false ) {
				$parts = explode( '__', $entry );
				return $parts[1] ?? $default_title;
			}
		}

		return $default_title;
	}

	/**
	 * Apply custom labels to the 'Posts' post type object.
	 */
	public function update_post_type_labels() {
		global $wp_post_types;

		$custom_title = $this->get_custom_post_menu_title();

		if ( empty( $wp_post_types['post'] ) || ! is_object( $wp_post_types['post'] ) ) {
			return;
		}

		$labels = &$wp_post_types['post']->labels;

		$labels->name               = $custom_title;
		$labels->singular_name      = $custom_title;
		// Translators: %s is the custom post type title (e.g., "Books").
		$labels->all_items          = sprintf( __( 'All %s', 'nexter-pro-extensions' ), $custom_title );
		$labels->add_new            = __( 'Add New', 'nexter-pro-extensions' );
		$labels->add_new_item       = __( 'Add New', 'nexter-pro-extensions' );
		$labels->edit_item          = __( 'Edit', 'nexter-pro-extensions' );
		$labels->new_item           = $custom_title;
		$labels->view_item          = __( 'View', 'nexter-pro-extensions' );
		// Translators: %s is the custom post type title (e.g., "Books").
		$labels->search_items = sprintf( __( 'Search %s', 'nexter-pro-extensions' ), $custom_title );

		// Translators: %s is the lowercase custom post type title (e.g., "books").
		$labels->not_found = sprintf( __( 'No %s found', 'nexter-pro-extensions' ), strtolower( $custom_title ) );

		// Translators: %s is the lowercase custom post type title (e.g., "books").
		$labels->not_found_in_trash = sprintf( __( 'No %s found in Trash', 'nexter-pro-extensions' ), strtolower( $custom_title ) );

	}

	/**
	 * Update 'Posts' menu and submenu labels with custom title.
	 */
	public function update_post_menu_labels() {
		global $submenu;

		$custom_title = $this->get_custom_post_menu_title();

		if ( isset( $submenu['edit.php'][5][0] ) ) {
			$submenu['edit.php'][5][0] = sprintf(
				/* translators: %s is the post type label */
				__( 'All %s', 'nexter-pro-extensions' ),
				$custom_title
			);
		}
	}

	/**
	 * Customize 'Posts' label in the admin bar.
	 * @param WP_Admin_Bar $wp_admin_bar WordPress admin bar object.
	 */
	public function update_admin_bar_post_label( $wp_admin_bar ) {
		$custom_title = $this->get_custom_post_menu_title();

		if ( $new_post_node = $wp_admin_bar->get_node( 'new-post' ) ) {
			$new_post_node->title = $custom_title;
			$wp_admin_bar->add_node( $new_post_node );
		}
	}

	/**
	 * Conditionally hide admin menu items based on toggle settings and user roles.
	 */
	public function maybe_hide_admin_menu_items() {
		global $menu, $current_user;

		$current_user_roles = $current_user->roles ?? [];

		$menu_items_to_always_hide = [];
		
		$menu_options = isset($this->amo_option['admin_menu']) ? $this->amo_option['admin_menu'] : array();

		if ( ! empty( $menu_options['menu_always_hidden'] ) ) {
			$menu_items_to_always_hide = json_decode( stripslashes( $menu_options['menu_always_hidden'] ), true ) ?? [];
		}

		$helper = new Nexter_Ext_Helper_Func();
		$menu_items_hidden_by_toggle = $helper->get_hidden_admin_menus_by_toggle();

		foreach ( $menu as $key => $item ) {
			$menu_id = ( strpos( $item[4], 'wp-menu-separator' ) !== false ) ? $item[2] : $item[5];

			// Apply toggle-based hidden class
			if ( in_array( $menu_id, $menu_items_hidden_by_toggle, true ) ) {
				$menu[$key][4] .= ' hidden nxt_hidden_menu';
			}

			if ( is_array( $menu_items_to_always_hide ) && ! empty( $menu_items_to_always_hide ) ) {
				foreach ( $menu_items_to_always_hide as $hidden_id => $settings ) {
					$restored_id = $helper->decode_admin_menu_id( $hidden_id );

					// Match current menu item or specific case for Yoast SEO
					if ( $menu_id !== $restored_id && ! ( $restored_id === 'toplevel_page_wpseo_dashboard' && $menu_id === 'toplevel_page_wpseo_workouts' ) ) {
						continue;
					}

					if ( ! isset( $settings['always_hide'] ) || ! $settings['always_hide'] ) {
						continue;
					}

					$hide_for = $settings['always_hide_for'] ?? '';
					$roles = $settings['which_roles'] ?? [];

					$should_hide = false;

					if ( $hide_for === 'all-roles' ) {
						$should_hide = true;

					} elseif ( $hide_for === 'all-roles-except' && is_array( $roles ) ) {
						$should_hide = ! array_intersect( $current_user_roles, $roles );

					} elseif ($hide_for === 'selected-roles') {
						// sanitize roles input if it is a string
						if (isset($roles) && is_string($roles)) {
							$roles = stripslashes($roles);
							$roles = explode(',', $roles); // if roles are comma-separated string
						}
						if (is_array($roles)) {
							$should_hide = (bool) array_intersect($current_user_roles, $roles);
						} else {
							$should_hide = false;
						}
					}


					if ( $should_hide ) {
						$menu[$key][4] .= ' always-hidden';
					}
				}
			}
		}
	}

	/**
	 * Add toggle menu items to show or hide hidden admin menus.
	 */
	public function maybe_add_menu_toggle_buttons() {
		global $current_user;

		$helper = new Nexter_Ext_Helper_Func();

		$hidden_parent_menus = $helper->get_hidden_admin_menus_by_toggle();
		$hidden_submenus = $helper->get_toggle_hidden_submenus();

		// Get allowed capabilities for showing the toggle
		$allowed_capabilities = $helper->get_capabilities_for_toggle_visibility();

		// Gather current user's capabilities from all roles
		$user_roles = $current_user->roles ?? [];
		$user_capabilities = [];

		foreach ( $user_roles as $role ) {
			$role_obj = get_role( $role );
			if ( $role_obj && is_array( $role_obj->capabilities ) ) {
				$user_capabilities = array_merge(
					$user_capabilities,
					array_keys( $role_obj->capabilities )
				);
			}
		}

		$user_capabilities = array_unique( $user_capabilities );

		// Determine if the toggle menu should be shown
		$show_toggle = !empty( array_intersect( $allowed_capabilities, $user_capabilities ) );

		if ( $show_toggle && ( ! empty( $hidden_parent_menus ) || ! empty( $hidden_submenus ) ) ) {
			add_menu_page(
				__( 'Show All', 'nexter-pro-extensions' ),
				__( 'Show All', 'nexter-pro-extensions' ),
				'read',
				'nxt_ext_show_hidden_menu',
				fn() => false,
				'dashicons-arrow-down-alt2',
				300
			);

			add_menu_page(
				__( 'Show Less', 'nexter-pro-extensions' ),
				__( 'Show Less', 'nexter-pro-extensions' ),
				'read',
				'nxt_ext_hide_hidden_menu',
				fn() => false,
				'dashicons-arrow-up-alt2',
				301
			);
		}
	}

	/**
	 * Enqueue script to toggle hidden admin menu items.
	 */
	public function enqueue_hidden_menu_toggle_script() {
		$helper = new Nexter_Ext_Helper_Func();

		$hidden_menus    = $helper->get_hidden_admin_menus_by_toggle();
		$hidden_submenus = $helper->get_toggle_hidden_submenus();

		if ( ! empty( $hidden_menus ) || ! empty( $hidden_submenus ) ) {
			wp_enqueue_script(
				'nxt-toggle-hidden-menu',
				NXT_PRO_EXT_URI . 'assets/js/toggle-hidden-menu.js',
				[],
				NXT_PRO_EXT_VER,
				false
			);
		}
	}

	/**
	 * Assign Contact Form 7 capabilities to default user roles if not properly added.
	 */
	public function assign_cf7_capabilities_to_roles() {
		if ( ! function_exists( 'is_plugin_active' ) || ! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
			return;
		}

		// List of WordPress core roles
		$default_roles = [ 'administrator', 'editor', 'author', 'contributor', 'subscriber' ];

		// Required CF7 capabilities not added by default
		$cf7_capabilities = [
			'wpcf7_read_contact_form',
			'wpcf7_read_contact_forms',
		];

		foreach ( $default_roles as $role_slug ) {
			$role = get_role( $role_slug );

			if ( ! $role ) {
				continue; // Skip if role doesn't exist
			}

			foreach ( $cf7_capabilities as $capability ) {
				if ( ! $role->has_cap( $capability ) ) {
					$role->add_cap( $capability, true );
				}
			}
		}
	}

	/**
	 * Restrict access to admin pages based on "always hide" settings and user roles.
	 */
	public function restrict_admin_page_access_by_role() {
		global $wp, $submenu;

		$current_user    = wp_get_current_user();
		$user_roles      = is_array($current_user->roles) ? array_values($current_user->roles) : [];
		$helper          = new Nexter_Ext_Helper_Func();
		$hidden_menu_ids = $helper->get_always_hidden_admin_menu_fragments();
		$current_url     = add_query_arg($wp->query_vars);

		// Resolve actual page slug
		if ( '/wp-admin/' == $current_url && ! in_array( 'index.php', $hidden_menu_ids ) ) {
            // do nothing, this is the dashboard at /wp-admin/ without index.php and it's not restricted for access
            $page_slug = $current_url; 
        } elseif ( '/wp-admin/' == $current_url && in_array( 'index.php', $hidden_menu_ids ) ) {
            // we're on the dashboard at /wp-admin/ and it (index.php) is restricted for access, so, change url ID to 'index.php'
            $page_slug = 'index.php';
        } elseif ( false !== strpos( $current_url, '?page=' ) ) {
            $current_urls = explode( '?page=', $current_url );
            $page_slug = $current_urls[1]; // e.g. some-admin-page-slug
        } elseif ( false !== strpos( $current_url, '?' ) ) {
            $page_slug = str_replace( '/wp-admin/', '', $current_url ); // e.g. post-new.php?post_type=todos
        } else {
            $page_slug = str_replace( '/wp-admin/', '', $current_url ); // e.g. edit-comments.php or plugins.php  
        }

		$page_slug = sanitize_key($page_slug);
		$restrict_access = false;

		// Top-level menu check
		if (in_array($page_slug, $hidden_menu_ids)) {
			$config_raw = $helper->get_hidden_admin_menus_by_toggle($page_slug);
			$config = isset($config_raw[$page_slug]) ? $config_raw[$page_slug] : [];

			$always_hide    = isset($config['always_hide']) ? (bool) $config['always_hide'] : false;
			$hide_for       = isset($config['always_hide_for']) ? $config['always_hide_for'] : '';
			$which_roles    = isset($config['which_roles']) && is_array($config['which_roles']) ? $config['which_roles'] : [];

			if ($always_hide) {
				if ($hide_for === 'all-roles') {
					$restrict_access = true;
				} elseif ($hide_for === 'all-roles-except') {
					if (empty(array_intersect($user_roles, $which_roles))) {
						$restrict_access = true;
					}
				} elseif ($hide_for === 'selected-roles') {
					if (!empty(array_intersect($user_roles, $which_roles))) {
						$restrict_access = true;
					}
				}
			}

			if ($restrict_access) {
				wp_die(esc_html__('Sorry, you are not allowed to access this page.', 'nexter-pro-extensions'));
				die(); // Fallback
			}
		}

		// Submenu (child page) check based on parent restrictions
		foreach ((array) $submenu as $parent_slug => $submenu_items) {
			if (in_array($parent_slug, $hidden_menu_ids, true)) {
				foreach ($submenu_items as $item) {
					if ($page_slug === $item[2]) {
						$config_raw = $helper->get_hidden_admin_menus_by_toggle($parent_slug);
						$config = isset($config_raw[$parent_slug]) ? $config_raw[$parent_slug] : [];

						$always_hide    = isset($config['always_hide']) ? (bool) $config['always_hide'] : false;
						$hide_for       = isset($config['always_hide_for']) ? $config['always_hide_for'] : '';
						$which_roles    = isset($config['which_roles']) && is_array($config['which_roles']) ? $config['which_roles'] : [];

						if ($always_hide) {
							if ($hide_for === 'all-roles') {
								$restrict_access = true;
							} elseif ($hide_for === 'all-roles-except') {
								if (empty(array_intersect($user_roles, $which_roles))) {
									$restrict_access = true;
								}
							} elseif ($hide_for === 'selected-roles') {
								if (!empty(array_intersect($user_roles, $which_roles))) {
									$restrict_access = true;
								}
							}
						}

						if ($restrict_access) {
							wp_die(esc_html__('Sorry, you are not allowed to access this page.', 'nexter-pro-extensions'));
							die(); // Fallback
						}
					}
				}
			}
		}

	}


	/**
	 * Handle AJAX request to save admin menu configuration.
	 */
	public function ajax_save_admin_menu_settings() {
		// Verify capability
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'You do not have permission to perform this action.' ] );
		}

		// Verify nonce
		if (
			! isset( $_REQUEST['nonce'] ) ||
			! check_ajax_referer( 'nxt-admin-menu-nonce', 'nonce', false )
		) {
			wp_send_json_error( [ 'message' => 'Invalid nonce.' ] );
		}

		// Get current settings
		$options_extra = get_option( 'nexter_extra_ext_options', [] );
		$menu_settings = $options_extra['admin_menu'] ?? [];

		// Sanitize and assign values
		$menu_settings['custom_menu_order']             = isset( $_REQUEST['custom_menu_order'] ) ? sanitize_text_field($_REQUEST['custom_menu_order']) : ( $menu_settings['custom_menu_order'] ?? [] );
		$menu_settings['menu_titles']            = (isset( $_REQUEST['menu_titles'] ) && !empty($_REQUEST['menu_titles'])) ? sanitize_text_field($_REQUEST['menu_titles'] ) : ( $menu_settings['menu_titles'] ?? [] );
		$menu_settings['change_submenus_order']         = isset( $_REQUEST['change_submenus_order'] ) ? sanitize_text_field( stripslashes( $_REQUEST['change_submenus_order'] ) ) : ( $menu_settings['change_submenus_order'] ?? '' );
		$menu_settings['menu_always_hidden']     = isset( $_REQUEST['menu_always_hidden'] ) ? sanitize_text_field( stripslashes( $_REQUEST['menu_always_hidden'] ) ) : ( $menu_settings['menu_always_hidden'] ?? '' );
		$menu_settings['submenu_always_hidden']  = isset( $_REQUEST['submenu_always_hidden'] ) ? sanitize_text_field( stripslashes( $_REQUEST['submenu_always_hidden'] ) ) : ( $menu_settings['submenu_always_hidden'] ?? '' );
		$menu_settings['change_menu_new_separators']    = isset( $_REQUEST['change_menu_new_separators'] ) ? $_REQUEST['change_menu_new_separators'] : ( $menu_settings['change_menu_new_separators'] ?? [] );

		// Save updated settings
		$options_extra['admin_menu'] = $menu_settings;
		$updated = update_option( 'nexter_extra_ext_options', $options_extra, true );

		if ( $updated ) {
			wp_send_json_success( [ 'message' => 'Menu settings saved.' ] );
		} else {
			wp_send_json_error( [ 'message' => 'Menu settings update failed.' ] );
		}
	}

	/**
	 * Hide submenu items based on toggle or role rules.
	 */
	public function hide_admin_submenu_items() {
		global $submenu, $current_user;

		// Get saved settings
		$menu_options = isset($this->amo_option['admin_menu']) ? $this->amo_option['admin_menu'] : array();

		// Decode hidden submenu data
		$hidden_submenus_raw = $menu_options['submenu_always_hidden'] ?? '';
		$hidden_submenus     = json_decode( stripslashes( $hidden_submenus_raw ), true ) ?? [];

		$user_roles = $current_user->roles;
		$helper     = new Nexter_Ext_Helper_Func();

		// Get submenu items hidden by toggle (checkbox toggle)
		$toggled_hidden_submenus = $helper->get_toggle_hidden_submenus();

		foreach ( $submenu as $parent_slug => $submenu_items ) {
			foreach ( $submenu_items as $index => $submenu_item ) {

				// Ensure submenu item label exists
				$label = isset( $submenu_item[0] ) ? $submenu_item[0] : '';
				
				// Remove unwanted characters/emojis for consistency with saved data
				$clean_label = str_replace(
					[ '↳', '➤', '⭐️', '✛', '👋', '<img draggable="false" role="img" class="emoji" alt="👋" src="https://s.w.org/images/core/emoji/15.0.3/svg/1f44b.svg">' ],
					'',
					$label
				);

				$sanitized_title = sanitize_title( $clean_label );
				$url_fragment    = isset( $submenu_item[2] ) ? sanitize_text_field( $submenu_item[2] ) : '';
				$item_id         = $parent_slug . '_-_' . $sanitized_title . '_-_' . strlen( $url_fragment );

				// Default to no class
				$submenu[$parent_slug][$index][4] = '';

				// If item is hidden via toggle
				if ( in_array( $item_id, $toggled_hidden_submenus, true ) ) {
					$submenu[$parent_slug][$index][4] .= 'nxt_hidden_menu hidden ';
				}

				// If item is hidden permanently via role settings
				foreach ( $hidden_submenus as $stored_id => $rule ) {
					$resolved_id = $helper->decode_admin_menu_id( sanitize_text_field( $stored_id ) );

					if ( $resolved_id === $item_id && ! empty( $rule['always_hide'] ) ) {

						$hide_for = $rule['always_hide_for'] ?? '';
						$role_list = $rule['which_roles'] ?? [];

						switch ( $hide_for ) {
							case 'all-roles':
								$submenu[$parent_slug][$index][4] .= 'always-hidden ';
								break;

							case 'all-roles-except':
								$should_hide = true;
								foreach ( $user_roles as $role ) {
									if ( in_array( $role, $role_list, true ) ) {
										$should_hide = false;
										break;
									}
								}
								if ( $should_hide ) {
									$submenu[$parent_slug][$index][4] .= 'always-hidden ';
								}
								break;

							case 'selected-roles':
								foreach ( $user_roles as $role ) {
									if ( in_array( $role, $role_list, true ) ) {
										$submenu[$parent_slug][$index][4] .= 'always-hidden ';
										break;
									}
								}
								break;
						}
					}
				}
			}
		}
	}

	/**
	 * Register custom admin menu separators added via UI.
	 */
	public function register_admin_menu_separators() {
		global $menu;

		// Get stored options
		$menu_options = isset($this->amo_option['admin_menu']) ? $this->amo_option['admin_menu'] : array();

		// Decode separator data
		$separators_json = $menu_options['change_menu_new_separators'] ?? '';
		if (is_array($separators_json)) {
			$new_separators = $separators_json;
		} else {
			$new_separators = json_decode(stripslashes($separators_json), true);
		}

		if ( ! is_array( $new_separators ) || empty( $new_separators ) ) {
			return;
		}

		foreach ( $new_separators as $separator_id => $info ) {
			//$separator_id_sanitized = sanitize_text_field( $separator_id );

			// Append a menu item representing the separator
			$menu[] = [
				'',
				'read',
				$separator_id,
				'',
				'wp-menu-separator additional-separator',
			];
		}
	}

	/**
	 * Reset admin menu settings via AJAX.
	 */
	public function reset_admin_menu_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'You do not have permission to perform this action.' ] );
		}

		if (
			! isset( $_REQUEST['nonce'] ) ||
			! check_ajax_referer( 'nxt-reset-menu-nonce', 'nonce', false )
		) {
			wp_send_json_error( [ 'message' => 'Invalid nonce.' ] );
		}

		$options_extra = get_option( 'nexter_extra_ext_options', [] );
		$admin_menu_settings = $options_extra['admin_menu'] ?? [];

		// Remove all custom menu configurations
		$keys_to_remove = [
			'custom_menu_order',
			'menu_titles',
			'change_submenus_order',
			'change_menu_hidden',
			'menu_always_hidden',
			'submenu_always_hidden',
			'change_menu_new_separators',
		];

		foreach ( $keys_to_remove as $key ) {
			unset( $admin_menu_settings[ $key ] );
		}

		$options_extra['admin_menu'] = $admin_menu_settings;
		$updated = update_option( 'nexter_extra_ext_options', $options_extra, true );

		if ( $updated ) {
			wp_send_json_success( [ 'message' => 'Menu settings reset.' ] );
		} else {
			wp_send_json_error( [ 'message' => 'Reset failed.' ] );
		}
	}

}

 new Nexter_Ext_Pro_Admin_Menu_Organize();
<?php
/**
 * Advanced Maintenance Mode.
 *
 * @package Nexter Extensions Pro
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Nexter_Pro_Maintenance_Mode' ) ) {

	class Nexter_Pro_Maintenance_Mode {

		/**
		 * Member Variable
		 */
		private static $instance;
		
		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
		
		/**
		 * Constructor
		 */
		public function __construct() {
			
			add_action('update_option_nxt_maintenance_mode', [ $this, 'nxt_update_mode' ], 10, 2);
			
			$display = $this->check_maintenance();
			if ( ! $display ) {
				return;
			}
			
			add_action( 'admin_bar_menu', [ $this, 'add_menu_admin_bar' ], 300 );
			add_action( 'admin_head', [ $this, 'admin_bar_maintenance_style' ] );
			add_action( 'wp_head', [ $this, 'admin_bar_maintenance_style' ] );

			add_action( 'template_redirect', [ $this, 'redirect_template_maintenance' ], 13 );

			if(!is_user_logged_in() && !is_admin()){
				add_action( 'pre_get_posts', function( $query ){
					global $wp;

					if(home_url( $wp->request ) != wp_login_url() && $query->is_main_query()){

						$user = wp_get_current_user();
						$maintenance_mode = self::get_options( 'nxt-maintenance-mode-opt', 'off' );
						$access_user = self::get_options( 'nxt-maintenance-access', 'logged_in' );

						// Woocommerce Account Page
						$woo_login = false;
						if ( class_exists( 'woocommerce' ) ) {
							if ( is_account_page() && ! is_user_logged_in() ) {
								$woo_login = true;
							}
						}
						
						if ( $woo_login ) {
							return;
						}

						if ( !empty($maintenance_mode) && $maintenance_mode == 'on' && $access_user === 'logged_in' && is_user_logged_in() ) {
							return;
						}
						
						if ( !empty($maintenance_mode) && $maintenance_mode == 'on' && $access_user === 'custom' ) {
							$custom_roles = self::get_options( 'nxt-maintenance-access-custom', '' );
							$user_roles = $user->roles;

							if ( is_multisite() && is_super_admin() ) {
								$user_roles[] = 'super_admin';
							}

							$check_roles = '';
							if(!empty($user_roles) && !empty($custom_roles) && is_array($custom_roles)){
								$check_roles = array_intersect( $user_roles, $custom_roles );
							}

							if ( ! empty( $check_roles ) ) {
								return;
							}
						}

						add_filter( 'body_class', [ $this, 'body_class_maintenance' ] );

						if ( self::get_options( 'nxt-maintenance-mode' ) === 'maintenance' && !empty($maintenance_mode) && $maintenance_mode == 'on' ) {
							$protocol = wp_get_server_protocol();
							header( "$protocol 503 Service Unavailable", true, 503 );
							header( 'Content-Type: text/html; charset=utf-8' );
							header( 'Retry-After: 600' );
						}

						$mode_template = self::get_options( 'nxt-maintenance-template', 'none' );
						
						remove_action( 'nexter_header', 'nexter_header_template' );
						remove_action( 'nexter_breadcrumb', 'nexter_breadcrumb_template' );
						remove_action( 'nexter_footer', 'nexter_footer_template' );
						
						if( !empty($mode_template) && $mode_template != 'none' ){

							$wpml_post_id = apply_filters( 'wpml_object_id', $mode_template, NXT_BUILD_POST, TRUE  );
							$query->set( 'post_type', NXT_BUILD_POST );
							$query->set('p', $wpml_post_id);
							return $query;
						}
					}
					return $query;
				} );
			}
		}
		

		public static function get_options($option, $default = '', $deprecate = ''){

			$nxt_theme_options = wp_parse_args( get_option( 'nxt-theme-options' ));

			$value = ( isset( $nxt_theme_options[ $option ] ) && $nxt_theme_options[ $option ] !== '' ) ? $nxt_theme_options[ $option ] : $default;

			return $value;
		}

		/*
		 * Check Maintenance Mode
		 */
		public static function check_maintenance() {
			$display = false;
			
			$maintenance_mode = self::get_options( 'nxt-maintenance-mode-opt', 'off' );
			
			if( !empty($maintenance_mode) && $maintenance_mode == 'on' ){
				$display = true;
			}
			
			return $display;
		}
		
		/**
		 * Add Body class
		 */
		public function body_class_maintenance( $classes ) {
			$classes[] = 'nxt-maintenance';
			return $classes;
		}
		
		/**
		 * redirect Template "Maintenance Mode"
		 */
		public function redirect_template_maintenance() {
			
			$user = wp_get_current_user();
			$maintenance_mode = self::get_options( 'nxt-maintenance-mode-opt', 'off' );
			$access_user = self::get_options( 'nxt-maintenance-access', 'logged_in' );

			// Woocommerce Account Page
			$woo_login = false;
			if ( class_exists( 'woocommerce' ) ) {
				if ( is_account_page() && ! is_user_logged_in() ) {
					$woo_login = true;
				}
			}
			
			if ( $woo_login ) {
				return;
			}

			if ( !empty($maintenance_mode) && $maintenance_mode == 'on' && $access_user === 'logged_in' && is_user_logged_in() ) {
				return;
			}
			
			if ( !empty($maintenance_mode) && $maintenance_mode == 'on' && $access_user === 'custom' ) {
				$custom_roles = self::get_options( 'nxt-maintenance-access-custom', '' );
				$user_roles = $user->roles;

				if ( is_multisite() && is_super_admin() ) {
					$user_roles[] = 'super_admin';
				}

				$check_roles = '';
				if(!empty($user_roles) && !empty($custom_roles) && is_array($custom_roles)){
					$check_roles = array_intersect( $user_roles, $custom_roles );
				}

				if ( ! empty( $check_roles ) ) {
					return;
				}
			}

			add_filter( 'body_class', [ $this, 'body_class_maintenance' ] );

			if ( self::get_options( 'nxt-maintenance-mode' ) === 'maintenance' && !empty($maintenance_mode) && $maintenance_mode == 'on' ) {
				$protocol = wp_get_server_protocol();
				header( "$protocol 503 Service Unavailable", true, 503 );
				header( 'Content-Type: text/html; charset=utf-8' );
				header( 'Retry-After: 600' );
			}

			$mode_template = self::get_options ( 'nxt-maintenance-template', 'none' );
			
			remove_action( 'nexter_header', 'nexter_header_template' );
			remove_action( 'nexter_breadcrumb', 'nexter_breadcrumb_template' );
			remove_action( 'nexter_footer', 'nexter_footer_template' );
			
			if( !empty($mode_template) && $mode_template != 'none' ){
				$GLOBALS['post'] = get_post( $mode_template );	// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$wpml_post_id = apply_filters( 'wpml_object_id', $mode_template, NXT_BUILD_POST, TRUE  );
				
				$query = new WP_Query( [
					'p' => $wpml_post_id,
					'post_type' => NXT_BUILD_POST,
				] );

				$settings = $query->have_posts() ? $query->posts : [];
				include_once NXT_BUILDER_HOOKS_DIR . 'maintenance/maintenance-page-template.php';
				exit;
			}else{
				include_once NXT_BUILDER_HOOKS_DIR . 'maintenance/maintenance-mode.php';
				exit;
			}
		}
		
		/** 
		 * Clear Third Party Caches
		 */
		public function nxt_update_mode( $old_value, $value ) {

			if ( $old_value !== $value ) {
				
				// WP Super Cache
				if (function_exists('wp_cache_clear_cache')) {
					wp_cache_clear_cache();
				}
				
				// W3 Total Cahce
				if ( function_exists( 'w3tc_flush_all' ) ) {
					w3tc_flush_all();
				}
				
				// Clear Litespeed cache
				method_exists('LiteSpeed_Cache_API', 'purge_all') && LiteSpeed_Cache_API::purge_all() ;

				// Site ground
				if (class_exists('SG_CachePress_Supercacher') && method_exists('SG_CachePress_Supercacher ', 'purge_cache')) {
					SG_CachePress_Supercacher::purge_cache(true);
				}

				// WP Fastest Cache
				if (isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')) {
					$GLOBALS['wp_fastest_cache']->deleteCache(true);
				}
			}
			
		}
		
		/**
		 * Admin Bar Maintenance CSS 'admin_head' and 'wp_head' filters
		 */
		public function admin_bar_maintenance_style() {
			?>
			<style>#wp-admin-bar-nxt-maintenance-on>a{background-color:#dc3232} #wp-admin-bar-nxt-maintenance-on>.ab-item:before{content:"\f160";top:2px;transition: all .3s }</style>
			<?php
		}
		
		/**
		 * Add menu "Maintenance Mode" in admin bar.
		 */
		public function add_menu_admin_bar( \WP_Admin_Bar $wp_admin_bar ) {
			$wp_admin_bar->add_node( [
				'id' => 'nxt-maintenance-on',
				'title' => __( 'Maintenance Mode ON', 'nexter-pro-extensions' ),
				'href' => esc_url(admin_url( '/customize.php?autofocus[panel]=panel-global-general' ))
			] );
		}
		
		/*
		 * Check Maintenance Mode User Logged_In Or Not
		 */
		public static function check_maintenance_header_footer() {
		
			$user = wp_get_current_user();
			$maintenance_mode = self::get_options( 'nxt-maintenance-mode-opt', 'off' );
			$access_user = self::get_options( 'nxt-maintenance-access', 'logged_in' );
			
			$display = false;
			if( !empty($maintenance_mode) && $maintenance_mode == 'on' ){
				
				$woo_login = false;
				if ( class_exists( 'woocommerce' ) ) {
					if ( is_account_page() && ! is_user_logged_in() ) {
						$woo_login = true;
					}
				}
				
				if ( $woo_login ) {
					$display = true;
				}
				
				if ( !empty($maintenance_mode) && $maintenance_mode == 'on' && $access_user === 'logged_in' && !is_user_logged_in() ) {
					$display = true;
				}
				
				if ( !empty($maintenance_mode) && $maintenance_mode == 'on' && $access_user === 'custom' ) {
					$custom_roles = self::get_options( 'nxt-maintenance-access-custom', '' );
					$user_roles = $user->roles;

					if ( is_multisite() && is_super_admin() ) {
						$user_roles[] = 'super_admin';
					}

					$check_roles = '';
					if(!empty($user_roles) && is_array($custom_roles) && !empty($custom_roles)){
						$check_roles = array_intersect( [$user_roles], $custom_roles );
					}

					if ( ! empty( $check_roles ) ) {
						$display = false;
					}
				}
			}
			
			return $display;
		}
		
	}
}

Nexter_Pro_Maintenance_Mode::get_instance();
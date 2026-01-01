<?php 
/**
 * Plugin Name: Nexter Extensions Pro
 * Plugin URI: https://nexterwp.com
 * Description: Extension for Nexter Theme to unlock all PRO features. Keep this active to use all its features
 * Version: 4.4.0
 * Author: POSIMYTH
 * Author URI: https://posimyth.com
 * Text Domain: nexter-pro-extensions
 * Requires at least: 5.8
 * Tested up to: 6.8.2
 * Requires PHP: 7.0
 * License: GPLv3
 * License URI: https://opensource.org/licenses/GPL-3.0
 * @package Nexter Extensions Pro
 */

/* Define Constants */
define( 'NXT_PRO_EXT_FILE', __FILE__ );
define( 'NXT_PRO_EXT', 'nxt-pro-ext' );
define( 'NXT_PRO_EXT_BASE', plugin_basename( NXT_PRO_EXT_FILE ) );
define( 'NXT_PRO_EXT_DIR', plugin_dir_path( NXT_PRO_EXT_FILE ) );
define( 'NXT_PRO_EXT_URI', plugins_url( '/', NXT_PRO_EXT_FILE ) );
define( 'NXT_PRO_EXT_VER', '4.4.0' );
if(!defined('NXT_BUILD_POST')){
	define( 'NXT_BUILD_POST', 'nxt_builder' );
}

/* Development Constants for Geolocation Testing */
if (!defined('NEXTER_DEV_COUNTRY')) {
	define('NEXTER_DEV_COUNTRY', 'India'); // Default development country
}
if (!defined('NEXTER_DEV_COUNTRY_CODE')) {
	define('NEXTER_DEV_COUNTRY_CODE', 'IN'); // Default development country code
}
if (!defined('NEXTER_DEV_CONTINENT')) {
	define('NEXTER_DEV_CONTINENT', 'Asia'); // Default development continent
}
/**
 * Nexter Pro Plugin Loaded.
 */
function nexter_pro_ext_plugin_loaded() {
	load_plugin_textdomain( 'nexter-pro-extensions', false, NXT_PRO_EXT_DIR . '/languages' );
	
	if(!defined("NEXTER_EXT_VER")){
		add_action( 'admin_notices', 'nexter_ext_load_notice' );
		return;
	}
	
	$get_theme = wp_get_theme();
	if ( ! version_compare( PHP_VERSION, '5.6', '>=' ) ) {
		add_action( 'admin_notices', 'nexter_pro_php_version_notice' );
	} else {
		require_once NXT_PRO_EXT_DIR . 'includes/nexter-load-extensions.php';
	}
	
}
add_action( 'plugins_loaded', 'nexter_pro_ext_plugin_loaded' );

/**
 * Nexter Pro notice for minimum PHP version.
 */
function nexter_pro_php_version_notice() {
	/* translators: %s: php require */
	$message = sprintf( esc_html__( 'Nexter Extensions Pro requires PHP version %s+, plugin is currently NOT RUNNING.', 'nexter-pro-extensions' ), '5.6' );
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}

/**
 * Nexter Extensions load Notice
 */
function nexter_ext_load_notice() {
	$plugin = 'nexter-extension/nexter-extension.php';
	if ( nexter_extension_activated() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) { return; }
		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
		$admin_notice = '<p>' . esc_html__( 'You are one step away from using Nexter Extensions. Please activate Nexter Extensions version.', 'nexter-pro-extensions' ) . '</p>';
		$admin_notice .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, esc_html__( 'Activate Nexter Extensions', 'nexter-pro-extensions' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) { return; }
		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=nexter-extension' ), 'install-plugin_nexter-extension' );
		$admin_notice = '<p>' . esc_html__( 'Nexter Extensions is missing. Would you please install that to make Nexter Extensions Pro working smoothly?', 'nexter-pro-extensions' ) . '</p>';
		$admin_notice .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, esc_html__( 'Install Nexter Extension', 'nexter-pro-extensions' ) ) . '</p>';
	}
	echo '<div class="notice notice-error is-dismissible">'.wp_kses_post($admin_notice).'</div>';
}

/**
 * Nexter Extensions activated or not
 */
if ( ! function_exists( 'nexter_extension_activated' ) ) {
	function nexter_extension_activated() {
		$file_path = 'nexter-extension/nexter-extension.php';
		$installed_plugins = get_plugins();
		
		return isset( $installed_plugins[ $file_path ] );
	}
}

/**
 * Plugin activated action
 * @since  1.0.0
 */
function nexter_pro_activated_plugin( $plugin ){
	if( $plugin == plugin_basename( __FILE__ ) ) {
		$activate_label=get_option( 'nexter_white_label' );			
		if ( !empty($activate_label["nxt_hidden_label"]) && $activate_label["nxt_hidden_label"] === 'on' ) {
			$activate_label["nxt_hidden_label"] = '';
			update_option('nexter_white_label', $activate_label);
		}
	}
}
add_action( 'activated_plugin', 'nexter_pro_activated_plugin', 10 );

if( !class_exists( 'Nexter_SL_Plugin_Updater' ) ) {
	require_once NXT_PRO_EXT_DIR . 'includes/settings-options/Nexter_SL_Plugin_Updater.php';
	function nexter_ext_pro_plugin_updater(){
		if(class_exists('Nexter_Pro_Ext_Activate')){
			$Nexter_Pro_Ext_Activate = Nexter_Pro_Ext_Activate::get_instance();
			$status = $Nexter_Pro_Ext_Activate->nexter_activate_status();
			if(class_exists( 'Nexter_SL_Plugin_Updater' ) && !empty($status) && $status=='valid'){
				$license = get_option( 'nexter_activate' );
				
				if ( !empty( $license ) && isset( $license['nexter_activate_key'] ) && !empty( $license['nexter_activate_key'] ) ) {
				
					$edd_updater = new Nexter_SL_Plugin_Updater( 'https://store.posimyth.com', __FILE__, array(
						'version' => NXT_PRO_EXT_VER,
						'license' => $license['nexter_activate_key'],		
						//'item_name' => 'Nexter WordPress Theme',
						'item_id' => 99121,
						'author' => 'POSIMYTH Themes',
						'url' => home_url(),
						'beta' => false,
					));
				}
			}
		}
	}
	add_action( 'admin_init', 'nexter_ext_pro_plugin_updater' , 0 );
}

/**
 * 2FA functionality
 * @since 3.1.0
 */
require_once NXT_PRO_EXT_DIR . 'includes/settings-options/nexter-two-factor-management.php';
Nexter_Two_Factor_Management::set_up_actions_filters();

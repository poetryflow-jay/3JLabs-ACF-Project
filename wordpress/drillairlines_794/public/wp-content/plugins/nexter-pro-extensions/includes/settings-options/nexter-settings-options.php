<?php 
/**
 * Nexter Pro Settings Options
 *
 * @package Nexter Extensions Pro
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

class Nexter_Ext_Pro_Settings_Options {
	
	/**
     * Constructor
     */
    public function __construct() {

		include_once NXT_PRO_EXT_DIR . 'includes/settings-options/nexter-ext-library.php';
		include_once NXT_PRO_EXT_DIR . 'includes/settings-options/nexter-ext-right-click-disable.php';
		include_once NXT_PRO_EXT_DIR . 'includes/settings-options/nexter-ext-login-email-notification.php';
		include_once NXT_PRO_EXT_DIR . 'includes/settings-options/nexter-ext-wp-login-white-label.php';
		include_once NXT_PRO_EXT_DIR . 'includes/settings-options/nexter-ext-performance-security.php';

		
        add_action( 'admin_init', [ $this, 'nxt_extension_export_data' ] );
		add_action('wp_ajax_nxt_import_extension_data', [ $this, 'nxt_extension_import_data' ]);
		
		$extension_option = get_option( 'nexter_extra_ext_options' );
		if( !empty($extension_option)){
			//Local User Avatar
			if( isset($extension_option['local-user-avatar']) && !empty($extension_option['local-user-avatar']['switch'])){
				require_once NXT_PRO_EXT_DIR . 'includes/settings-options/nexter-ext-local-user-avatar.php';
			}
			//Public Preview for Drafts
			if( isset($extension_option['public-preview-drafts']) && !empty($extension_option['public-preview-drafts']['switch'])){
				require_once NXT_PRO_EXT_DIR . 'includes/settings-options/nexter-ext-public-preview-drafts.php';
			}

			//Redirect 404 Page
			if( isset($extension_option['redirect-404-page']) && !empty($extension_option['redirect-404-page']['switch'])){
				require_once NXT_PRO_EXT_DIR . 'includes/settings-options/nexter-ext-redirect-404-page.php';
			}

			//Media Replacement
			if( isset($extension_option['media-replacement']) && !empty($extension_option['media-replacement']['switch']) && is_admin()){
				require_once NXT_PRO_EXT_DIR . 'includes/settings-options/nexter-ext-media-replacement.php';
			}

			//Clean User Profile
			if( isset($extension_option['clean-user-profile']) && !empty($extension_option['clean-user-profile']['switch']) ){
				require_once NXT_PRO_EXT_DIR . 'includes/settings-options/nexter-ext-clean-user-profile.php';
			}

			//Display Active Plugins First
			if( isset($extension_option['display-plugin-active-first']) && !empty($extension_option['display-plugin-active-first']['switch']) ){
				add_action( 'admin_head-plugins.php', function(){
					global $wp_list_table, $status;
					if ( !in_array( $status, array(
						'active',
						'inactive',
						'recently_activated',
						'mustuse'
					), true ) ) {
						if ( is_array( $wp_list_table->items ) ) {
							uksort( $wp_list_table->items, array($this, 'plugins_order_callback') );
						}
					}
				});
			}

			//Admin Menu Organizer
			if( isset($extension_option['admin-menu-organizer']) && !empty($extension_option['admin-menu-organizer']['switch']) ){
				require_once NXT_PRO_EXT_DIR . 'includes/settings-options/nexter-ext-admin-menu-organizer.php';
			}

			//Taxonomy Order
			if( isset($extension_option['taxonomy-order']) && !empty($extension_option['taxonomy-order']['switch']) && !empty($extension_option['taxonomy-order']['values']) && isset($extension_option['taxonomy-order']['values']->taxonomy) && !empty($extension_option['taxonomy-order']['values']->taxonomy)){
				require_once NXT_PRO_EXT_DIR . 'includes/settings-options/nexter-ext-taxonomy-order.php';
			}
		}

		Nexter_Pro_Ext_Activate::get_instance();
    }
	
	/*
	 * ReOrder Display Active Plugin First
	 **/
	public function plugins_order_callback( $a, $b ) {
        global $wp_list_table;
        $items = $wp_list_table->items;
        $a_active = is_plugin_active( $a );
        $b_active = is_plugin_active( $b );
        if ( $a_active && !$b_active ) {
            return -1;
        } elseif ( !$a_active && $b_active ) {
            return 1;
        } else {
            if ( isset( $items[$a] ) && isset( $items[$b] ) ) {
                return strcasecmp( $items[$a]['Name'], $items[$b]['Name'] );
            }
        }
    }

	/*
	* Export Nexter Extension Data Options
	* @since 4.3.0
	* */
	public function nxt_extension_export_data(){
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( !isset( $_POST['nexter_export_ext_nonce'] ) || !wp_verify_nonce( $_POST['nexter_export_ext_nonce'], 'nexter_admin_nonce' ) ) {
			return;
		}

		if ( empty( $_POST['nxt_extension_export_action'] ) || $_POST['nxt_extension_export_action'] !== 'nxt_export_ext' ) {
			return;
		}

		// Define the option keys
		$option_keys = [
			'nexter_disabled_images',
			'nexter_elementor_icons',
			'nexter_custom_image_sizes',
			'nexter_extra_ext_options',
			'nexter_site_performance',
			'nexter_site_security',
			'nexter_white_label',
			'nexter_activate',
			'nxt_license_status'
		];

		// Create merged export array
		$export_data = [];
		foreach ($option_keys as $key) {
			$export_data[$key] = get_option($key, []);
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=nexter-extension-export-' . gmdate( 'm-d-Y' ) . '.json' );
		header( 'Expires: 0' );
		echo wp_json_encode( $export_data );
		die();
	}

	/*
	* Import Nexter Extension Data Options
	* @since 4.3.0
	* */
	public function nxt_extension_import_data(){
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(esc_html__( 'Not a permission', 'nexter-extension' ));
		}
		if ( !isset( $_POST['nexter_import_ext_nonce'] ) || !wp_verify_nonce( $_POST['nexter_import_ext_nonce'], 'nexter_admin_nonce' ) ) {
			wp_send_json_error(esc_html__( 'Security check failed', 'nexter-extension' ));
		}

		// Check file upload
		if (!isset($_FILES['nxt_import_file']) || $_FILES['nxt_import_file']['error'] !== UPLOAD_ERR_OK) {
			wp_send_json_error(esc_html__( 'File Import failed', 'nexter-extension' ));
		}
		
		$filename = $_FILES['nxt_import_file']['name'];

		if ( empty( $filename ) ) {
			wp_send_json_error(esc_html__( 'File Import failed', 'nexter-extension' ));
		}
		
		$file_extension  = explode( '.', $filename );
		$extension = end( $file_extension );

		if ( $extension !== 'json' ) {
			wp_send_json_error( esc_html__( 'Valid .json file extension', 'nexter-extension' ) );
		}

		$nxt_import_file = $_FILES['nxt_import_file']['tmp_name'];

		if ( empty( $nxt_import_file ) ) {
			wp_send_json_error( esc_html__( 'Please upload a file', 'nexter-extension' ) );
		}

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}
		
		$get_contants = $wp_filesystem->get_contents( $nxt_import_file );
		$import_data = json_decode($get_contants, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			wp_send_json_error(esc_html__( 'Invalid JSON format', 'nexter-extension' ));
		}

		$check_keys = [
			'nexter_disabled_images',
			'nexter_elementor_icons',
			'nexter_custom_image_sizes',
			'nexter_extra_ext_options',
			'nexter_site_performance',
			'nexter_site_security',
			'nexter_white_label',
			'nexter_activate',
			'nxt_license_status'
		];
		
		if (!empty( $import_data ) && is_array($import_data)) {
			foreach ($import_data as $option_key => $option_value) {
				if(in_array($option_key, $check_keys)){
					update_option($option_key, $option_value);
				}
			}
			wp_send_json_success(esc_html__( 'Extension settings imported successfully', 'nexter-extension' ));
		}

		wp_send_json_error(esc_html__( 'No valid settings found in the file', 'nexter-extension' ));
	}
}

// Get it started
$Nexter_Ext_Pro_Settings_Options = new Nexter_Ext_Pro_Settings_Options();
?>
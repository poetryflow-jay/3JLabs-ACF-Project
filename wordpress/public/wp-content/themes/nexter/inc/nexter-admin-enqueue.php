<?php
/**
 * Nexter Admin Enqueue Styles And Scripts
 *
 * @package	Nexter
 * @since	4.2.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Nexter_Admin_Enqueue' ) ) {

	class Nexter_Admin_Enqueue {
		
		/** 
		 * Constructor
		 */
		public function __construct() {
			if( !defined( 'NEXTER_EXT' ) && empty( get_option( 'nexter-extension-load-notice' ) ) ) {
				global $pagenow;
				if(!empty( $pagenow ) && ! ( $pagenow == 'update.php' && isset($_GET['action']) && ($_GET['action'] === 'install-plugin' || $_GET['action'] === 'upgrade-plugin' ))){
					add_action( 'admin_notices', array( $this, 'nexter_extension_load_notice' ) );
				}
				add_action( 'wp_ajax_nexter_ext_dismiss_notice', array( $this, 'nexter_ext_dismiss_notice_ajax' ) );
			}
			
			if(!defined( 'NEXTER_EXT' )){
				add_action( 'wp_ajax_nexter_ext_install', array( $this, 'nexter_ext_install_ajax' ) );
			}
			add_action( 'wp_ajax_nexter_ext_plugin_install', [ $this, 'nexter_ext_plugin_install_ajax']);

			add_action( 'enqueue_block_assets', array( $this, 'gutenberg_assets_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_admin' ), 1 );
		}
		
		/**
		 * Check Local Google Font
		 * @since 1.1.0
		 */
		public function check_nxt_ext_local_google_font( $style = false){
			$check = false;
			$nxt_ext = get_option( 'nexter_extra_ext_options' );
			if( !empty($nxt_ext) && isset($nxt_ext['local-google-font']) && !empty($nxt_ext['local-google-font']['switch']) && !empty($nxt_ext['local-google-font']['values']) ){
				$check = true;
				if($style==true){
					return $nxt_ext['local-google-font']['style'];
				}
			}
			
			return $check;
		}

		/**
		 * Enqueue Gutenberg assets style.
		 */
		public function gutenberg_assets_styles(){
			// Use minified libraries if SCRIPT_DEBUG is turned off
			$minified = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			
			wp_enqueue_style( 'nexter-block-editor-styles', NXT_CSS_URI .'admin/block-editor'. $minified .'.css', false, NXT_VERSION, 'all' );
			
			if(!$this->check_nxt_ext_local_google_font()){
				Nexter_Get_Fonts::enqueue_load_fonts();
			}

			$custom_fonts_face = Nexter_Get_Fonts::get_custom_fonts_face();
			if( !empty( $custom_fonts_face ) ){
				wp_add_inline_style( 'nexter-block-editor-styles',nexter_minify_css_generate($custom_fonts_face) );
			}
			
			wp_add_inline_style( 'nexter-block-editor-styles', apply_filters( 'nexter_block_editor_dynamic_style', Nexter_Gutenberg_Dynamic_Css::render_theme_css() ) );
		}
		
		/**
		 * Theme Load Admin Css And Js
		 * @since 1.0.8
		 */
		public function enqueue_scripts_admin( $hook_suffix ){
			
			$minified = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			if ('post.php' != $hook_suffix && 'post-new.php' != $hook_suffix ) {
				wp_enqueue_style( 'nexter-select-css', NXT_CSS_URI .'extra/select2'. $minified .'.css', array(), NXT_VERSION );
				wp_enqueue_script( 'nexter-select-js', NXT_JS_URI . 'extra/select2'. $minified .'.js', array(), NXT_VERSION, false );
			}

			wp_enqueue_style( 'nxt-admin-css', NXT_CSS_URI .'admin/nexter-admin'. $minified .'.css', array(), NXT_VERSION );
			
			$screen = get_current_screen();
			if ( ('page' === $screen->post_type && 'post' === $screen->base) || ('post' === $screen->post_type && 'post' === $screen->base) ) {
				wp_enqueue_style( 'nxt-customizer-sidebar-css', NXT_CSS_URI .'admin/nexter-customizer-sidebar'. $minified .'.css', array(), NXT_VERSION );
				wp_enqueue_script( 'nxt-customizer-sidebar-js', NXT_JS_URI . 'admin/nexter-customizer-sidebar'. $minified .'.js', array(), NXT_VERSION, true );
			}
			
			if( !defined( 'NEXTER_EXT' ) ){
				wp_enqueue_script( 'nexter-admin-js', NXT_JS_URI . 'admin/nexter-admin'. $minified .'.js', array(), NXT_VERSION, false );

				$nexter_admin_localize = array(
					'ajaxurl' => admin_url('admin-ajax.php'),
					'ajax_nonce' => wp_create_nonce('nexter_admin_nonce'),
				);
				wp_localize_script( 'nexter-admin-js', 'nexter_admin_config', $nexter_admin_localize );
			}
			
			if(! did_action('wp_enqueue_media')){
				wp_enqueue_media();
			}
			
			if ( ! is_customize_preview() ) {
				wp_enqueue_style( 'wp-color-picker' );
			}
			
			$js_handle = apply_filters( 'nexter_admin_script_handles', array( 'jquery', 'wp-color-picker' ) );
			if ( is_customize_preview() === true ) {
				$js_handle[] = 'customize-base';
			}
			wp_register_script( 'nexter-color-picker', NXT_JS_URI . 'extra/wp-color-picker-alpha'. $minified .'.js', $js_handle, NXT_VERSION, true );
			
			wp_enqueue_style( 'nxt-metabox-editor-style', NXT_CSS_URI .'admin/metabox-editor-style'. $minified .'.css', array() );
		}
		
		/**
		 * Nexter Extension Load Notice
		 */
		public function nexter_extension_load_notice(){
			$plugin = 'nexter-extension/nexter-extension.php';	
			if ( $this->nexter_extension_activate() ) {
				if ( ! current_user_can( 'activate_plugins' ) ) { return; }
				$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
				$admin_notice = '<h2>' . esc_html__( 'Activate Nexter Extension Now !!!', 'nexter' ) . '</h2>';
				$admin_notice .= '<p>' . esc_html__( 'Finally, You are Done Installing Nexter Theme & Extension as Well. Now It’s Time to Press the Pedal. Activate Nexter Extension and Get Over With it.', 'nexter' ). '</p>';
				$admin_notice .= sprintf( '<a href="%s" class="nxt-nobtn-primary">%s</a>', $activation_url, esc_html__( 'Activate Nexter Extension', 'nexter' ) );
			} else {
				if ( ! current_user_can( 'install_plugins' ) ) { return; }
				$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=nexter-extension' ), 'install-plugin_nexter-extension' );
				$admin_notice = '<h2>' . esc_html__( 'It’s Time to Install Nexter Extension', 'nexter' ) . '</h2>';
				$admin_notice .= '<p>' . esc_html__( 'Now You’ve Already Installed Nexter Theme, You Need to Install Nexter Extension in Order to Get the Most of out From it. Nexter Extension is an Ultimate Solution to Your Page Building Experience Using Templates.', 'nexter' ) .sprintf( ' <a href="%s" target="_blank" rel="noopener noreferrer" >%s</a>', esc_url('https://nexterwp.com'), esc_html__( 'Visit Here', 'nexter' ) ). esc_html__( ' to Learn More About Nexter Extension.', 'nexter' ) . '</p>';
				$admin_notice .= sprintf( '<a href="%s" class="nxt-nobtn-primary">%s</a>', $install_url, esc_html__( 'Install Nexter Extension', 'nexter' ) );
			}
			echo '<div class="notice notice-info nexter-ext-notice is-dismissible nxt-notice-wrap" data-notice-id="nexter_ext_install"><div class="nexter-license-activate"><div class="nexter-license-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"><rect width="24" height="24" fill="#1717CC" rx="5"></rect><path fill="#fff" d="M12.605 17.374c.026 0 .038.013.039.038.102 0 .192.014.27.04.025 0 .05.012.076.037.128.077.23.167.307.27v.038c0 .026.013.051.039.077v.038a.63.63 0 0 1 .038.193l-.038 1.882h-2.652v-2.613h1.921Zm.308-13.414c.128 0 .23.038.308.115a.259.259 0 0 1 .115.23V15.26c.025.153-.052.295-.23.423a.872.872 0 0 1-.578.192h-1.844V3.96h2.23Z"></path></svg></div><div class="nexter-license-content">'.wp_kses_post($admin_notice).'</div></div></div>';
		}
		
		/**
		 * Check Activate Or Not Nexter Extension
		 */
		public function nexter_extension_activate(){
			$file_path = 'nexter-extension/nexter-extension.php';
			$installed_plugins = get_plugins();
			
			return isset( $installed_plugins[ $file_path ] );
		}
		
		/**
		 * Nexter Notice Dismiss Ajax
		 */
		public function nexter_ext_dismiss_notice_ajax(){
			check_ajax_referer( 'nexter_admin_nonce', 'nexter_nonce' );
			if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error();
			}
			update_option( 'nexter-extension-load-notice', 1 );
		}

		/**
		 * Nexter Extension Install Ajax
		 */
		public function nexter_ext_install_ajax(){
			check_ajax_referer( 'nexter_admin_nonce', 'nexter_nonce' ); 
	
			if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'content' => __( 'Insufficient permissions.', 'nexter' ) ) );
			}
	
			$plu_slug = 'nexter-extension';

			$phpFileName = $plu_slug;
	
			$installed_plugins = get_plugins();
	
			include_once ABSPATH . 'wp-admin/includes/file.php';
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			include_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';
			include_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
	
			$result   = array();
			$response = wp_remote_post(
				'http://api.wordpress.org/plugins/info/1.0/',
				array(
					'body' => array(
						'action'  => 'plugin_information',
						'request' => serialize(
							(object) array(
								'slug'   => $plu_slug,
								'fields' => array(
									'version' => false,
								),
							)
						),
					),
				)
			);
	
			$plugin_info = unserialize( wp_remote_retrieve_body( $response ) );
	
			if ( ! $plugin_info ) {
				wp_send_json_error( array( 'content' => __( 'Failed to retrieve plugin information.', 'nexter' ) ) );
			}
	
			$skin     = new \Automatic_Upgrader_Skin();
			$upgrader = new \Plugin_Upgrader( $skin );
	
			$plugin_basename = ''.$plu_slug.'/'.$phpFileName.'.php';
			
			if ( ! isset( $installed_plugins[ $plugin_basename ] ) && empty( $installed_plugins[ $plugin_basename ] ) ) {
				$installed = $upgrader->install( $plugin_info->download_link );
	
				$activation_result = activate_plugin( $plugin_basename );
	
				$success = null === $activation_result;
				wp_send_json([
					'Success' => true,
					'redirectUrl' => admin_url( 'admin.php?page=nexter_welcome' )
				]);
	
			}else if ( isset( $installed_plugins[ $plugin_basename ] ) ) {
				$activation_result = activate_plugin( $plugin_basename );
	
				$success = null === $activation_result;
				wp_send_json([
					'Success' => true,
					'redirectUrl' => admin_url( 'admin.php?page=nexter_welcome' )
				]);
			}
		}
		
		/**
         * Nexter Other Plugin Install
         */
        public function nexter_ext_plugin_install_ajax(){
            check_ajax_referer( 'nexter_admin_nonce', 'nexter_nonce' ); 
    
            if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error( array( 'content' => __( 'Insufficient permissions.', 'nexter' ) ) );
            }
    
            $plu_slug = ( isset( $_POST['slug'] ) && !empty( $_POST['slug'] ) ) ? sanitize_text_field( wp_unslash($_POST['slug']) ) : '';

            $phpFileName = $plu_slug;
            if(!empty($plu_slug) && $plu_slug == 'the-plus-addons-for-elementor-page-builder'){
                $phpFileName = 'theplus_elementor_addon';
            }
    
            $installed_plugins = get_plugins();
    
            include_once ABSPATH . 'wp-admin/includes/file.php';
            include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            include_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';
            include_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
    
            $result   = array();
            $response = wp_remote_post(
                'http://api.wordpress.org/plugins/info/1.0/',
                array(
                    'body' => array(
                        'action'  => 'plugin_information',
                        'request' => serialize(
                            (object) array(
                                'slug'   => $plu_slug,
                                'fields' => array(
                                    'version' => false,
                                ),
                            )
                        ),
                    ),
                )
            );
    
            $plugin_info = unserialize( wp_remote_retrieve_body( $response ) );
    
            if ( ! $plugin_info ) {
                wp_send_json_error( array( 'content' => __( 'Failed to retrieve plugin information.', 'nexter' ) ) );
            }
    
            $skin     = new \Automatic_Upgrader_Skin();
            $upgrader = new \Plugin_Upgrader( $skin );
    
            $plugin_basename = ''.$plu_slug.'/'.$phpFileName.'.php';
            
            
            if ( ! isset( $installed_plugins[ $plugin_basename ] ) && empty( $installed_plugins[ $plugin_basename ] ) ) {
                $installed = $upgrader->install( $plugin_info->download_link );
    
                $activation_result = activate_plugin( $plugin_basename );
                if(!empty($plu_slug) && $plu_slug == 'wdesignkit'){
                    $this->wdk_installed_settings_enable();
                }
                $success = null === $activation_result;
                wp_send_json(['Sucees' => true]);
    
            } elseif ( isset( $installed_plugins[ $plugin_basename ] ) ) {
                $activation_result = activate_plugin( $plugin_basename );
                if(!empty($plu_slug) && $plu_slug == 'wdesignkit'){
                    $this->wdk_installed_settings_enable();
                }
                $success = null === $activation_result;
                wp_send_json(['Sucees' => true]);
            }
        }

        public function wdk_installed_settings_enable(){
            if( defined( 'TPGB_VERSION' ) ){
                $settings = array('gutenberg_builder' => true,'gutenberg_template' => true);
                $builder = array( 'elementor' );
                do_action( 'wdkit_active_settings', $settings, $builder );
            }else if( defined('ELEMENTOR_VERSION') ){
                $settings = array('elementor_builder' => true,'elementor_template' => true);
                $builder = array( 'nexter-blocks');
                do_action( 'wdkit_active_settings', $settings, $builder );
            }
        }
        
	}
	new Nexter_Admin_Enqueue();
}
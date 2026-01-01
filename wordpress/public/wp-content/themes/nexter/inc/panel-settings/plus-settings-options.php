<?php 
/**
 * Nexter Settings Panel
 *
 * @package	Nexter
 * @since	1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

class Nexter_Settings_Panel {
	
    private $option_key = 'nexter_settings_opts';

	/**
     * Setting Name/Title
     * @var string
     */
    protected $setting_name = '';
    protected $setting_logo = '';

    // Field definitions: id => [ title, description ]
    private $fields = [
        'container_css'      => '',
        'header_footer_css'  => '',
        'reset_min_css'      => '',
        'sidebar_css'        => '',
        'theme_min_css'      => '',
        'woocommerce_min_css'=> '',
        'skip_link'=> '',
    ];

	/**
     * Constructor
     * @since 1.0.0
     */
    public function __construct() {
        add_action('init', [$this, 'initialize_plugin_name']);
        if ( is_admin() && current_user_can( 'manage_options' ) ) {
            if(!defined('NEXTER_EXT_VER') && !defined('TPGB_VERSION')){
                add_action('admin_menu', array( $this, 'nxt_add_menu_page' ));
                add_action('admin_enqueue_scripts', array( $this, 'enqueue_scripts_admin' ));
                add_action( 'wp_ajax_nexter_temp_api_call', [ $this, 'nexter_temp_api_call' ] );
            }
           
            add_action( 'wp_ajax_nexter_theme_white_label_save', [ $this, 'nexter_theme_white_label_save_data' ] );
            add_action( 'wp_ajax_nexter_theme_settings_save', [ $this, 'nexter_theme_settings_save_data' ] );
            add_filter( 'admin_body_class', function( $classes ) {
                if ( isset($_GET['page']) && $_GET['page'] === 'nxt_builder' ) {
                    $classes .= ' post-type-nxt_builder nxt-page-nexter-builder ';
                }
                return $classes;
            }, 11);
        }
    }
	
    public function initialize_plugin_name() : void {
        $this->setting_name = esc_html__('Nexter', 'nexter');
        $this->setting_logo = esc_url(NXT_THEME_URI . 'inc/panel-settings/dashboard/assets/svg/navbox/nexter-logo.svg');
		if(defined('NXT_PRO_EXT')){
			$options = get_option( 'nexter_white_label' );
			if ( ! empty( $options['brand_name'] ) ) {
                $brand = sanitize_text_field( wp_unslash( $options['brand_name'] ) );
                $this->setting_name = sprintf( '%s', $brand );
                $this->setting_logo = (!empty($options['theme_logo'])) ? $options['theme_logo'] : esc_url(NXT_THEME_URI . 'inc/panel-settings/dashboard/assets/svg/navbox/nexter-logo.svg');
            }
		}
	}

	/**
     * Add menu options page
     */
    public function nxt_add_menu_page() : void {
		
		global $submenu;
            unset($submenu['themes.php'][20]);
            unset($submenu['themes.php'][15]);
            add_menu_page( 
                esc_html( $this->setting_name ),
                esc_html( $this->setting_name ),
                'manage_options',
                'nexter_welcome',
                array( $this, 'nexter_ext_dashboard' ),
                'dashicons-nxt-builder-groups',
                58
            );
            add_submenu_page(
                'nexter_welcome',
                __( 'Dashboard', 'nexter' ),
                __( 'Dashboard', 'nexter' ),
                'manage_options',
                'nexter_welcome',
            );
            add_submenu_page(
                'nexter_welcome',
                __( 'Templates', 'nexter' ),
                __( 'Templates', 'nexter' ),
                'manage_options',
                'nexter_welcome#/templates',
                array( $this, 'nexter_ext_dashboard' ),
            );
            add_submenu_page(
                'nexter_welcome',
                __( 'Blocks', 'nexter' ),
                __( 'Blocks', 'nexter' ),
                'manage_options',
                'nexter_welcome#/blocks',
                array( $this, 'nexter_ext_dashboard' ),
            );
            add_submenu_page(
                'nexter_welcome',
                __( 'Theme Builder', 'nexter' ),
                __( 'Theme Builder', 'nexter' ),
                'manage_options',
                'nxt_builder',
                array($this, 'nexter_theme_builder_display')
            );
            
            add_submenu_page(
                'nexter_welcome',
                __( 'Code Snippets', 'nexter' ),
                __( 'Code Snippets', 'nexter' ),
                'manage_options',
                'nxt_code_snippets',
                array($this, 'nexter_code_snippet_display'),
            );
            add_submenu_page(
                'nexter_welcome',
                __( 'Extensions', 'nexter' ),
                __( 'Extensions', 'nexter' ),
                'manage_options',
                'nexter_welcome#/utilities',
                array( $this, 'nexter_ext_dashboard' ),
            );
            add_submenu_page(
                'nexter_welcome',
                __( 'Theme Customizer', 'nexter' ),
                __( 'Theme Customizer', 'nexter' ),
                'manage_options',
                'nexter_welcome#/theme_customizer',
                array( $this, 'nexter_ext_dashboard' ),
            );
            add_submenu_page( 
                'nexter_welcome', 
                esc_html__( 'Get Pro Nexter', 'nexter' ), 
                esc_html__( 'Get Pro Nexter', 'nexter' ), 
                'manage_options', 
                esc_url('https://nexterwp.com/pricing/?utm_source=wpbackend&utm_medium=blocks&utm_campaign=nextersettings')
            );

            if (isset($submenu['nexter_welcome'])) {
                // Find the Dashboard submenu
                foreach ($submenu['nexter_welcome'] as $key => $item) {
                    if ($item[2] === 'nexter_welcome') {
                        $dashboard_item = $item;
                        unset($submenu['nexter_welcome'][$key]);
                        // Prepend Dashboard manually
                        array_unshift($submenu['nexter_welcome'], $dashboard_item);
                        break;
                    }
                }
            }
    }

    public function enqueue_scripts_admin( $hook_suffix ){
        $user = wp_get_current_user();

        $nxtPlugin = false;
        $tpaePlugin = false;
        $wdkPlugin = false;
        $uichemyPlugin = false;

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $pluginslist = get_plugins();

        $tpgbactivate = false;
        if ( isset( $pluginslist[ 'the-plus-addons-for-block-editor/the-plus-addons-for-block-editor.php' ] ) && !empty( $pluginslist[ 'the-plus-addons-for-block-editor/the-plus-addons-for-block-editor.php' ] ) ) {
            if( is_plugin_active('the-plus-addons-for-block-editor/the-plus-addons-for-block-editor.php') ){
                $nxtPlugin = true;
            }else{
                $tpgbactivate = true;
            }
        }

        $extensioninstall = false;
        $extensionactivate = false;
        if ( isset( $pluginslist[ 'nexter-extension/nexter-extension.php' ] ) && !empty( $pluginslist[ 'nexter-extension/nexter-extension.php' ] ) ) {
            if( is_plugin_active('nexter-extension/nexter-extension.php') ){
                $extensioninstall = true;
            }else{
                $extensionactivate = true;
            }
        }
        
        $tpaeactive = false;
        if ( isset( $pluginslist[ 'the-plus-addons-for-elementor-page-builder/theplus_elementor_addon.php' ] ) && !empty( $pluginslist[ 'the-plus-addons-for-elementor-page-builder/theplus_elementor_addon.php' ] ) ) {
            if( is_plugin_active('the-plus-addons-for-elementor-page-builder/theplus_elementor_addon.php') ){
                $tpaePlugin = true;
            }else{
                $tpaeactive = true;
            }
        }

        $wdkactive = false;
        if ( isset( $pluginslist[ 'wdesignkit/wdesignkit.php' ] ) && !empty( $pluginslist[ 'wdesignkit/wdesignkit.php' ] ) ) {
            if( is_plugin_active('wdesignkit/wdesignkit.php') ){
                $wdkPlugin = true;
                // Get WDesignKit version
                $wdkVersion = '1.0.0'; // Default version
                if (defined('WDKIT_VERSION')) {
                    $wdkVersion = WDKIT_VERSION;
                } else {
                    // Try to get version from plugin data
                    $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/wdesignkit/wdesignkit.php');
                    if (isset($plugin_data['Version'])) {
                        $wdkVersion = $plugin_data['Version'];
                    }
                }
            }else{
                $wdkactive = true;
            }
        }
        $uichemyactive = false;
        if ( isset( $pluginslist[ 'uichemy/uichemy.php' ] ) && !empty( $pluginslist[ 'uichemy/uichemy.php' ] ) ) {
            if( is_plugin_active('uichemy/uichemy.php') ){
                $uichemyPlugin = true;
            }else{
                $uichemyactive = true;
            }
        }

        $themes = wp_get_themes();
        $nexterInstalled = array_key_exists('nexter', $themes);
        $theme_det_link = self::get_nexter_theme_details_link('nexter');
        
        wp_enqueue_style( 'nexter-dash-style', NXT_THEME_URI . 'inc/panel-settings/dashboard/build/index.css', array(), NXT_VERSION, 'all' );

        wp_enqueue_script( 'nexter-dashscript', NXT_THEME_URI . 'inc/panel-settings/dashboard/build/index.js', array( 'react', 'react-dom','wp-i18n', 'wp-dom-ready', 'wp-element','wp-components', 'wp-block-editor', 'wp-editor' ), NXT_VERSION, true );  //

        // Attach JavaScript translations
        wp_set_script_translations(
            'nexter-dashscript',  // Handle must match enqueue
            'nexter',                 // Your text domain
            NXT_THEME_URI . 'languages'
        );

        $dashData = [
            'userData' => [
                'userName' => esc_html($user->display_name),
                'profileLink' => esc_url( get_avatar_url( $user->ID ) )
            ],
            'whiteLabelData' => [
                'brandname' => $this->setting_name,
                'brandlogo' => $this->setting_logo
            ],
            'nexterThemeActive' => (defined('NXT_VERSION')) ? true : false,
            'nexterThemeIntall' =>  $nexterInstalled,
            'nexterThemeDet' => $theme_det_link,
            'nexterCustLink' => admin_url('customize.php'),
            'extensioninstall' => $extensioninstall,
            'extensionactivate' => $extensionactivate,
            'nexterBlock' => $nxtPlugin,
            'tpgbinstall' => $nxtPlugin,
            'tpgbactivate' => $tpgbactivate,
            'tpaeAddon' => $tpaePlugin,
            'tpaeactive' => $tpaeactive,
            'wdkPlugin' => $wdkPlugin,
            'wdkactive' => $wdkactive,
            'wdadded' => $wdkPlugin,
            'uichemy' => $uichemyPlugin,
            'uichemyactive' => $uichemyactive,
            'whiteLabel' => get_option('nexter_theme_white_label'),
            'nxtThemeSetting' => (array) get_option( $this->option_key, [] ),
            'nxt_wdkit_url' => 'https://api.wdesignkit.com/',
        ];

            $current_user_username = '';
        if (!empty($user) && isset($user->user_login) && !empty($user->user_login)) {
            $current_user_username = $user->user_login;
        }

        $locallize_data =array(
            'adminUrl' => admin_url(),
            'nxtex_url' => NXT_THEME_URI . 'inc/panel-settings/dashboard/',
            'ajax_url'    => admin_url( 'admin-ajax.php' ),
            'ajax_nonce' => wp_create_nonce('nexter_admin_nonce'),
            'pro' => (defined('NXT_PRO_EXT_VER') ? true : false),
            'dashData' => $dashData,
            'site_url' => site_url(),
            'username' => $current_user_username,
        );

        if(has_filter( 'nxt_dashboard_localize_data' )){
            $locallize_data = apply_filters( 'nxt_dashboard_localize_data', $locallize_data );
        }

        wp_localize_script(
            'nexter-dashscript',
            'nxtext_ajax_object',
            $locallize_data
        );

        if (isset($_GET['page']) && $_GET['page'] === 'nxt_builder') {
            // Theme Builder JS Enqueue
            wp_enqueue_style( 'nexter-theme-builder', NXT_THEME_URI . 'inc/panel-settings/theme-builder/build/index.css', array(), NXT_VERSION, 'all' );

            wp_enqueue_script( 'nexter-theme-builder', NXT_THEME_URI . 'inc/panel-settings/theme-builder/build/index.js', array('react', 'react-dom', 'wp-dom-ready', 'wp-i18n'), NXT_VERSION, true );

            // Attach JavaScript translations
            wp_set_script_translations(
                'nexter-theme-builder',  // Handle must match enqueue
                'nexter',                 // Your text domain
                NXT_THEME_URI . 'languages'
            );
            $nexter_theme_builder_config = array(
                'adminUrl' => admin_url(),
                'ajaxurl' => admin_url('admin-ajax.php'),
                'ajax_nonce' => wp_create_nonce('nexter_admin_nonce'),
                'assets' => NXT_THEME_URI . 'inc/panel-settings/theme-builder/assets/',
                'is_pro' => (defined('NXT_PRO_EXT')) ? true : false,
                'dashboard_url' => admin_url( 'admin.php?page=nexter_welcome' ),
                'version' => NXT_VERSION,
                'import_temp_nonce' => wp_create_nonce('nxt_ajax'),
                'wdkPlugin' => $wdkPlugin,
                'wdkactive' => $wdkactive,
                'extensioninstall' => $extensioninstall,
                'extensionactivate' => $extensionactivate,
            );

            wp_localize_script( 'nexter-theme-builder', 'nexter_theme_builder_config', $nexter_theme_builder_config );
        }

        if (isset($_GET['page']) && $_GET['page'] === 'nxt_code_snippets') {
            wp_enqueue_style( 'nxt-code-snippet-style', NXT_THEME_URI . 'inc/panel-settings/code-snippets/index.css', array(), NXT_VERSION, 'all' );

            wp_enqueue_script( 'nxt-code-snippet', NXT_THEME_URI . 'inc/panel-settings/code-snippets/index.js', array(), NXT_VERSION, true );
			
            wp_localize_script(
				'nxt-code-snippet',
				'nxt_code_snippet_data',
				array(
					'ajax_url'    => admin_url( 'admin-ajax.php' ),
                    'ajax_nonce' => wp_create_nonce('nexter_admin_nonce'),
					'extensioninstall' => $extensioninstall,
                    'extensionactivate' => $extensionactivate,
				)
			);
        }
    }

    /**
     * Nexter Theme Details Link
     */
    public function get_nexter_theme_details_link($theme_slug) {
        $admin_url = admin_url('themes.php');
        return add_query_arg('theme', $theme_slug, $admin_url);
    }

	/**
     * Render Dashboard Root Div
     * @since 4.0.0
     */
	public function nexter_ext_dashboard() : void {
        echo '<div id="nexter-dash"></div>';
        do_action('nxt_ext_new_update_notice');
	}

    /**
     * Theme Builder Render html
     * @since  1.0.0
     */
    public function nexter_theme_builder_display() {
        echo '<div id="nexter-theme-builder"></div>';
    }
    
    /**
     * Code Snippet Render html
     * @since  1.0.0
     */
    public function nexter_code_snippet_display() {

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $pluginslist = get_plugins();

        $extensioninstall = false;
        $extensionactivate = false;
        if ( isset( $pluginslist[ 'nexter-extension/nexter-extension.php' ] ) && !empty( $pluginslist[ 'nexter-extension/nexter-extension.php' ] ) ) {
            if( is_plugin_active('nexter-extension/nexter-extension.php') ){
                $extensioninstall = true;
            }else{
                $extensionactivate = true;
            }
        }

        $features = [
            __('Replace multiple small plugins with a single, unified snippet manager', 'nexter'),
            __('Add PHP, HTML, CSS safely without editing theme or core files', 'nexter'),
            __('Apply snippets site-wide or on specific pages, posts, or conditions', 'nexter'),
            __('Stay protected with built-in validation, version control & rollback', 'nexter'),
            __('Import or Organise snippets from our 1000+ Cloud Snippets Library', 'nexter'),
            __('Preview, test, and toggle snippets without affecting live visitors', 'nexter')
        ];

        echo '<div id="nexter-code-snippets">
            <div class="nxt-code-heading-title">'.esc_html__('Code Snippets','nexter').'</div>
            <section class="nxt-install-ext-sec">
                <div class="nxt-ins-ext-cover">
                    <div class="nxt-ins-ext-inner">
                        <span class="badge">' . esc_html__('Code Snippets', 'nexter') . '</span>
                        <h1 class="nxt-ins-heading">' . esc_html__('Add Custom PHP, HTML, CSS & JS Code to WordPress - No Child Theme or Extra Plugins Needed', 'nexter') . '</h1>
                        <p class="nxt-ins-desc">' . esc_html__('Skip the pain of editing core files or juggling dozens of mini plugins. Nexter Code Snippets lets you safely add and manage custom PHP, HTML, CSS right inside your dashboard, no child theme required. Build smarter, cleaner sites with total control and zero risk to performance.', 'nexter') . '</p>
                        <div class="nxt-ins-btns">
                            <button class="nxt-inst-ext-btn ins-btn-primary">' . (!empty($extensionactivate) ? esc_html__('Activate Nexter Extensions', 'nexter') : esc_html__('Install Nexter Extensions', 'nexter')) . '</button>
                            <a href="https://nexterwp.com/nexter-extension/features/#code%20snippets" rel="noopener noreferrer" target="_blank" class="ins-btn-outline">' . esc_html__('Explore All Features', 'nexter') . '</a>
                        </div>
                        <ul class="nxt-feature-list">';

                        foreach ($features as $feature) {
                            echo '<li>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" fill="none" viewBox="0 0 14 15">
                                    <path fill="#1717cc" d="M6.373.505a7.13 7.13 0 0 0-3.7 1.474c-.535.41-1.228 1.183-1.628 1.813a9.6 9.6 0 0 0-.63 1.29c-.135.367-.28.931-.351 1.367-.085.534-.085 1.547 0 2.081a6.9 6.9 0 0 0 .65 2.04c.361.738.736 1.26 1.34 1.866a6.5 6.5 0 0 0 1.866 1.34 7 7 0 0 0 2.042.649c.534.084 1.548.084 2.083 0a7 7 0 0 0 2.041-.65 6.5 6.5 0 0 0 1.867-1.339 6.5 6.5 0 0 0 1.34-1.865c.241-.49.362-.81.493-1.304C13.96 8.61 14 8.251 14 7.49c0-.762-.041-1.12-.214-1.778a6.3 6.3 0 0 0-.493-1.304 6.5 6.5 0 0 0-1.34-1.865 6.5 6.5 0 0 0-1.867-1.34A6.8 6.8 0 0 0 8.058.56C7.655.497 6.756.467 6.373.505"/>
                                    <path fill="#fff" d="M10.673 4.482c.137.044.348.249.406.4a.76.76 0 0 1-.017.561c-.088.178-4.599 4.654-4.802 4.766a.66.66 0 0 1-.624-.003c-.116-.06-2.193-1.92-2.547-2.279a.68.68 0 0 1-.205-.506c.005-.414.31-.693.72-.666.1.008.22.033.267.06.074.036 1.458 1.282 1.907 1.712l.129.126 2.05-2.046c1.126-1.123 2.086-2.062 2.135-2.087a.7.7 0 0 1 .58-.038"/>
                                </svg>
                                ' . esc_html($feature) . '
                            </li>';
                        }

                echo'</ul>
                    </div>
                    <div class="nxt-ins-ext-img">
                        <img src="' . esc_url(NXT_THEME_URI . 'assets/images/install-nexter-extension.png') . '" alt="' . esc_attr__('Nexter Extension', 'nexter') . '" />
                    </div>
                </div>
            </section>
        </div>';
    }

    public function nexter_theme_settings_save_data() {
        check_ajax_referer( 'nexter_admin_nonce', 'nexter_nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'unauthorized' ], 403 );
        }

        $themeopt = ( isset( $_POST['themeopt'] ) ) ? wp_unslash( $_POST['themeopt'] ) : '';

        $raw_fields = json_decode( stripslashes( $themeopt ?? '{}' ), true );
        if ( ! is_array( $raw_fields ) ) {
            wp_send_json_error( [ 'message' => 'invalid_data' ], 400 );
        }
        
        if(!empty($raw_fields) && !empty($raw_fields['values'])){
            foreach ( $raw_fields['values'] as $field => $value ) {
                if ( array_key_exists( $field, $this->fields ) ) {
                    $raw_fields['values'][ $field ] = ( $value == 1 ) ? 1 : 0;
                }
            }
        }

        //customizer header footer save option
        if(!empty($raw_fields) && isset($raw_fields['values']) && isset($raw_fields['values']['header_footer_css'])){
            $cus_options = get_option( 'nxt-theme-options', [] );
            if(!empty($cus_options) || $cus_options===false){
                $cus_value = 'off';
                if($raw_fields['values']['header_footer_css'] == 1){
                    $cus_value = 'on';
                }else if($raw_fields['values']['header_footer_css'] == 0){
                    $cus_value = 'off';
                }

                $cus_options['nxt-header-disable-opt'] = $cus_value;
                $cus_options['nxt-footer-disable-opt'] = $cus_value;

                update_option( 'nxt-theme-options', $cus_options );
            }
        }

        update_option( $this->option_key, $raw_fields );

        wp_send_json_success();
    }

    public function nexter_theme_white_label_save_data(){
        check_ajax_referer( 'nexter_admin_nonce', 'nexter_nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'unauthorized' ], 403 );
        }

        $options = ( isset( $_POST['options'] ) ) ? wp_unslash( $_POST['options'] ) : '';

        $whitelabel =  (array) json_decode($options, true);

        if ( ! is_array( $whitelabel ) ) {
            wp_send_json_error( [ 'message' => 'invalid_data' ], 400 );
        }

        if( !empty($whitelabel) && isset($whitelabel['theme_screenshot_id']) && !empty($whitelabel['theme_screenshot_id']) && isset($whitelabel['theme_screenshot'])){
            $fileName = basename(get_attached_file($whitelabel['theme_screenshot_id']));
            $filepathname = basename($whitelabel['theme_screenshot']);
            if(!empty($fileName) && !empty($filepathname)){
                $filetype = wp_check_filetype($fileName);
                $filepathtype = wp_check_filetype($filepathname);
                if(!empty($filetype) && isset($filetype['type']) && !empty($filepathtype) && isset($filepathtype['type'])){
                    if(!(strpos($filetype['type'], 'image') !== false) || !(strpos($filepathtype['type'], 'image') !== false)) {
                        $whitelabel['theme_screenshot'] = '';
                        $whitelabel['theme_screenshot_id'] = '';
                    }
                }
            }
        }

        if( !empty($whitelabel) && isset($whitelabel['theme_logo_id']) && !empty($whitelabel['theme_logo_id']) && isset($whitelabel['theme_logo'])){
            $fileName = basename(get_attached_file($whitelabel['theme_logo_id']));
            $filepathname = basename($whitelabel['theme_logo']);
            if(!empty($fileName) && !empty($filepathname)){
                $filetype = wp_check_filetype($fileName);
                $filepathtype = wp_check_filetype($filepathname);
                if(!empty($filetype) && isset($filetype['type']) && !empty($filepathtype) && isset($filepathtype['type'])){
                    if(!(strpos($filetype['type'], 'image') !== false) || !(strpos($filepathtype['type'], 'image') !== false)) {
                        $whitelabel['theme_logo'] = '';
                        $whitelabel['theme_logo_id'] = '';
                    }
                }
            }
        }

        if( False === get_option( 'nexter_theme_white_label' ) ){
            add_option('nexter_theme_white_label',$whitelabel );
        }else{
            update_option( 'nexter_theme_white_label', $whitelabel );
        }

        wp_send_json_success();
    }

    /*
    * Wdesignkit Templates load
    */
    public function nexter_temp_api_call() {

        check_ajax_referer( 'nexter_admin_nonce', 'nexter_nonce' );

        if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'content' => __( 'Insufficient permissions.', 'nexter' ) ) );
            wp_die();
        }

        $method  = isset( $_POST['method'] ) ? sanitize_text_field( wp_unslash( $_POST['method'] ) ) : 'POST';
        $api_url = isset( $_POST['api_url'] ) ? esc_url_raw( wp_unslash( $_POST['api_url'] ) ) : '';
        $body    = isset( $_POST['url_body'] ) ? json_decode( wp_unslash( $_POST['url_body'] ), true ) : array();

        $args = array(
            'method'  => $method,
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
        );

        if ( ! empty( $body ) ) {
            $args['body'] = wp_json_encode( $body );
        }

        // Make the request based on method
        if ( 'POST' === $method ) {
            $response = wp_remote_post( $api_url, $args );
        } elseif ( 'GET' === $method ) {
            $response = wp_remote_get( $api_url, $args );
        } else {
            wp_send_json_error( array(
                'HTTP_CODE' => 400,
                'error' => 'Invalid HTTP method'
            ) );
            wp_die();
        }

        $statuscode = wp_remote_retrieve_response_code( $response );
        $getdataone = wp_remote_retrieve_body( $response );
        
        $response_data = json_decode( $getdataone, true );

        $statuscode_array = array( 'HTTP_CODE' => $statuscode );

        // Merge status code with response data
        if ( is_array( $response_data ) ) {
            $final = array_merge( $statuscode_array, $response_data );
        } else {
            $final = array_merge( $statuscode_array, array( 'data' => $response_data ) );
        }

        wp_send_json( $final );
        wp_die();
    }
}

if ( ! function_exists( 'nexter_settings_panel_boot' ) ) {
    function nexter_settings_panel_boot() {
        static $instance = null;
        if ( null === $instance ) {
            $instance = new Nexter_Settings_Panel();
        }
        return $instance;
    }
}
nexter_settings_panel_boot();
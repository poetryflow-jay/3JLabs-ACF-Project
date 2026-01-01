<?php 
/**
 * Nexter Extensions Pro Load
 *
 * @package Nexter Extensions Pro
 * @since 1.0.0
 */
define( 'NXT_BUILDER_HOOKS_DIR', NXT_PRO_EXT_DIR . 'includes/sections-hooks/' );
if ( ! class_exists( 'Nexter_Pro_Extensions_Load' ) ) {

	class Nexter_Pro_Extensions_Load {

		/**
		 * Member Variable
		 */
		private static $instance;

		public static $white_label = '';
		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'init', [ $this, 'includes_required' ] );
			if( is_admin() ) {
				$nexter_white_label = get_option( 'nexter_white_label' );

				add_action( 'admin_head', [ $this, 'nexter_menu_icon_logo' ] );
				add_action( 'admin_footer', [ $this, 'nexter_theme_screenshot' ] );

				if( ( empty($nexter_white_label) || ( !empty($nexter_white_label['nxt_help_link']) && $nexter_white_label['nxt_help_link'] !== 'on') ) ) {
					add_filter( 'plugin_action_links_' . NXT_PRO_EXT_BASE, array( $this, 'add_settings_link' ) );
					add_filter( 'plugin_row_meta', array( $this, 'add_extra_links_plugin_row_meta' ), 10, 2 );
				}
				
				add_filter( 'all_plugins', [ $this, 'nexter_ext_white_label_config' ] );
				add_filter( 'wp_prepare_themes_for_js', [ $this, 'nexter_theme_white_label_config'] );
				add_filter( 'all_themes', [ $this, 'nexter_theme_white_label_config' ] );
				add_filter( 'update_right_now_text', [ $this, 'admin_dashboard_right_now' ] );
				add_filter( 'nexter_remove_branding', [ $this, 'hidden_brand_theme' ] );
				
				if($this->hidden_brand_theme()){
					add_action( 'customize_register', [ $this, 'remove_customize_theme_section' ], 30 );
				}

				add_action( 'wp_ajax_nexter_white_label_reset', [$this,'nexter_white_label_reset'] );

			}
		}
		
		/**
		 * White Label Check Hidden Branding
		 */
		public function hidden_brand_theme(){
			$nxt_hidden_label = $this->get_white_label_option('nxt_hidden_label');
			if($nxt_hidden_label == 'on'){
				return true;
			}
			return false;
		}
		
		/**
		 * White Label Change Menu Icon
		 */
		public function nexter_menu_icon_logo(){
			$nexter_white_label = get_option( 'nexter_white_label' );
			if( !empty($nexter_white_label) && !empty($nexter_white_label['theme_logo'])){
				?>
				<style>.wp-menu-image.dashicons-nxt-builder-groups,li.wp-has-current-submenu.wp-menu-open.menu-top .wp-menu-image.dashicons-nxt-builder-groups{background: url(<?php echo esc_url($nexter_white_label['theme_logo']); ?>);background-size: 17px;background-repeat: no-repeat;background-position: center;}</style>
			<?php }
		}

		/**
		 * White Label Change Theme Screenshot
		 */
		public function nexter_theme_screenshot(){
			$nexter_white_label = get_option( 'nexter_white_label' );
			if(!empty($nexter_white_label) && !empty($nexter_white_label['theme_screenshot'])){
				$screenshot_url = $nexter_white_label['theme_screenshot'];
				?>
				<script>
					document.addEventListener("DOMContentLoaded", function () {
						setTimeout(() => {
							let themeElement = document.querySelector('.theme[data-slug="nexter"] .theme-screenshot img');
							if (themeElement) {
								themeElement.src = "<?php echo esc_url($screenshot_url); ?>";
							}

							function updateThemeModalImage() {
								let modal = document.querySelector('.theme-overlay.active');
								if (modal) {
									let themeNameElement = document.querySelector('.theme.active');
									if (themeNameElement && themeNameElement.getAttribute('data-slug') && themeNameElement.getAttribute('data-slug') == "nexter") {
										let modalImg = modal.querySelector('.screenshot img');
										if (modalImg) {
											modalImg.src = "<?php echo esc_url($screenshot_url); ?>";
										}
									}
								}
							}

							setTimeout(updateThemeModalImage, 100);

							// Also update when user clicks on theme
							document.body.addEventListener("click", function () {
								setTimeout(updateThemeModalImage, 500);
							});
						}, 1000);
					});
				</script>
			<?php }
		}

		/**
		 * Init Includes Files
		 */
		public function includes_required() {
			require NXT_PRO_EXT_DIR .'includes/settings-options/nexter-ext-helper-function.php';			
			require NXT_PRO_EXT_DIR .'includes/settings-options/nexter-settings-options.php';			
			require_once NXT_PRO_EXT_DIR . 'includes/nexter-enqueue-scripts.php';
			require_once NXT_BUILDER_HOOKS_DIR . 'nexter-advanced-maintenance.php';
			
			// Check if code snippets are enabled before including Pro functionality
			$get_opt = get_option('nexter_extra_ext_options');
			$code_snippets_enabled = true;

			if (isset($get_opt['code-snippets']) && isset($get_opt['code-snippets']['switch'])) {
				$code_snippets_enabled = !empty($get_opt['code-snippets']['switch']);
			}
			
			$snippet_file = NXT_PRO_EXT_DIR . 'includes/code-snippets/nexter-code-snippet-render-pro.php';
			if ($code_snippets_enabled && file_exists($snippet_file)) {
				// Include Pro Code Snippets functionality
				require_once $snippet_file;
			}
		}
		
		public function get_white_label_option($field){
			$values='';
			
			if(isset(self::$white_label) && !empty(self::$white_label)){
				if(isset(self::$white_label[$field]) && !empty(self::$white_label[$field])){
					return self::$white_label[$field];
				}
			}

			self::$white_label = get_option( 'nexter_white_label' );
			if(isset(self::$white_label[$field]) && !empty(self::$white_label[$field])){
				$values=self::$white_label[$field];
			}

			return $values;
		}

		/**
		 * Nexter Extensions White Label Config
		 */
		public function nexter_ext_white_label_config( $all_plugins ){
			$free_plugin_name = $this->get_white_label_option('nxt_free_plugin_name');
			$free_plugin_desc = $this->get_white_label_option('nxt_free_plugin_desc');
			$pro_plugin_name = $this->get_white_label_option('nxt_pro_plugin_name');
			$pro_plugin_desc = $this->get_white_label_option('nxt_pro_plugin_desc');
			$author_name = $this->get_white_label_option('author_name');
			$author_uri = $this->get_white_label_option('author_uri');
			
			if(defined('NEXTER_EXT_BASE') && !empty($all_plugins[NEXTER_EXT_BASE]) && is_array($all_plugins[NEXTER_EXT_BASE])){
				$all_plugins[NEXTER_EXT_BASE]['Name']           = ! empty( $free_plugin_name )     ? $free_plugin_name      : $all_plugins[NEXTER_EXT_BASE]['Name'];
				$all_plugins[NEXTER_EXT_BASE]['PluginURI']      = ! empty( $author_uri )      ? $author_uri       : $all_plugins[NEXTER_EXT_BASE]['PluginURI'];
				$all_plugins[NEXTER_EXT_BASE]['Description']    = ! empty( $free_plugin_desc )     ? $free_plugin_desc      : $all_plugins[NEXTER_EXT_BASE]['Description'];
				$all_plugins[NEXTER_EXT_BASE]['Author']         = ! empty( $author_name )   ? $author_name    : $all_plugins[NEXTER_EXT_BASE]['Author'];
				$all_plugins[NEXTER_EXT_BASE]['AuthorURI']      = ! empty( $author_uri )      ? $author_uri       : $all_plugins[NEXTER_EXT_BASE]['AuthorURI'];
				$all_plugins[NEXTER_EXT_BASE]['Title']          = ! empty( $free_plugin_name )     ? $free_plugin_name      : $all_plugins[NEXTER_EXT_BASE]['Title'];
				$all_plugins[NEXTER_EXT_BASE]['AuthorName']     = ! empty( $author_name )   ? $author_name    : $all_plugins[NEXTER_EXT_BASE]['AuthorName'];
			}
			
			if(defined('NXT_PRO_EXT_BASE') && !empty($all_plugins[NXT_PRO_EXT_BASE]) && is_array($all_plugins[NXT_PRO_EXT_BASE])){
				$all_plugins[NXT_PRO_EXT_BASE]['Name']           = ! empty( $pro_plugin_name )     ? $pro_plugin_name      : $all_plugins[NXT_PRO_EXT_BASE]['Name'];
				$all_plugins[NXT_PRO_EXT_BASE]['PluginURI']      = ! empty( $author_uri )      ? $author_uri       : $all_plugins[NXT_PRO_EXT_BASE]['PluginURI'];
				$all_plugins[NXT_PRO_EXT_BASE]['Description']    = ! empty( $pro_plugin_desc )     ? $pro_plugin_desc      : $all_plugins[NXT_PRO_EXT_BASE]['Description'];
				$all_plugins[NXT_PRO_EXT_BASE]['Author']         = ! empty( $author_name )   ? $author_name    : $all_plugins[NXT_PRO_EXT_BASE]['Author'];
				$all_plugins[NXT_PRO_EXT_BASE]['AuthorURI']      = ! empty( $author_uri )      ? $author_uri       : $all_plugins[NXT_PRO_EXT_BASE]['AuthorURI'];
				$all_plugins[NXT_PRO_EXT_BASE]['Title']          = ! empty( $pro_plugin_name )     ? $pro_plugin_name      : $all_plugins[NXT_PRO_EXT_BASE]['Title'];
				$all_plugins[NXT_PRO_EXT_BASE]['AuthorName']     = ! empty( $author_name )   ? $author_name    : $all_plugins[NXT_PRO_EXT_BASE]['AuthorName'];
			}
			
			return $all_plugins;
		}
		
		/**
		 * Nexter Theme White Label Config
		 */
		public function nexter_theme_white_label_config( $themes ){
			$nexter_key = 'nexter';
			$nexter_child_key = 'nexter-child-theme';
			if( defined('NXT_VERSION') && ( isset( $themes[ $nexter_key ] ) || isset( $themes[ $nexter_child_key ] ) ) ){
				$theme_name = $this->get_white_label_option('theme_name');
				$theme_desc = $this->get_white_label_option('theme_desc');
				$author_name = $this->get_white_label_option('author_name');
				$author_uri = $this->get_white_label_option('author_uri');
				if(!empty($theme_name)){
					$themes[ $nexter_key ]['name'] = $theme_name;
					if( isset( $themes[$nexter_child_key] ) ){
						$themes[ $nexter_child_key ]['name'] = $theme_name .__(' Child Theme','nexter-pro-extensions');
					}
					foreach( $themes as $key => $theme){
						if( isset( $theme['parent'] ) && $theme['parent'] == 'Nexter' ){
							$themes[ $nexter_key ]['parent'] = $theme_name;
						}
					}
				}
				
				$themes[$nexter_key]['description'] = ! empty( $theme_desc ) ? $theme_desc : $themes[$nexter_key]['description'];
				if( isset( $themes[$nexter_child_key] ) ){
					$themes[$nexter_child_key]['description'] = ! empty( $theme_desc ) ? $theme_desc : $themes[$nexter_child_key]['description'];
				}
				
				$themes[$nexter_key]['author'] = ! empty( $author_name ) ? $author_name : $themes[$nexter_key]['author'];
				if( isset( $themes[$nexter_child_key] ) ){
					$themes[$nexter_child_key]['author'] = ! empty( $author_name ) ? $author_name : $themes[$nexter_child_key]['author'];
				}
				$themes[$nexter_key]['authorAndUri'] = ! empty( $author_uri ) ? '<a href="' . esc_url( $author_uri ) . '">' . $themes[$nexter_key]['author'] . '</a>' : '<a href="#">' . $themes[$nexter_key]['author'] . '</a>';
				if( isset( $themes[$nexter_child_key] ) ){
					$themes[$nexter_child_key]['authorAndUri'] = ! empty( $author_uri ) ? '<a href="' . esc_url( $author_uri ) . '">' . $themes[$nexter_child_key]['author'] . '</a>' : '<a href="#">' . $themes[$nexter_child_key]['author'] . '</a>';
				}
			}
			
			return $themes;
		}
		
		/**
		 * Theme Customizer Title
		 */
		public function remove_customize_theme_section( $wp_customize ){
			$wp_customize->remove_panel( 'themes' );
		}
		
		/** 
		 * White Label theme Dashboard 'At a Glance'
		 */
		public function admin_dashboard_right_now( $content ){
			$theme_name = $this->get_white_label_option('theme_name');
			if( is_admin() && (wp_get_theme()== 'Nexter' || wp_get_theme()== 'Nexter Child Theme') && !empty($theme_name) ){
				return sprintf( $content, get_bloginfo( 'version', 'display' ), '<a href="'.esc_url('themes.php').'">' . esc_html($theme_name) . '</a>' );
			}
			return $content;
		}

		/**
		 * Adds Links to the plugins page.
		 * @since 1.1.0
		 */
		public function add_settings_link( $links ) {
			// Settings link.
			if ( current_user_can( 'manage_options' ) ) {
				$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=nexter_welcome#/utilities' ), __( 'Settings', 'nexter-pro-extensions' ) );
				$links = (array) $links;
				array_unshift( $links, $settings_link );
			}

			// Need Help link.
			if ( !apply_filters('nexter_remove_branding',false) ) {
				$need_help = sprintf( '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', esc_url('https://store.posimyth.com/get-support/nexter/'), __( 'Need Help?', 'nexter-pro-extensions' ) );
				$license = sprintf( '<a href="%s" style="color:green;font-weight:600;">%s</a>',  admin_url( 'admin.php?page=nexter_welcome#/activate_PRO' ), __( 'License', 'nexter-pro-extensions' ) );
				$links = (array) $links;
				$links[] = $need_help;
				$links[] = $license;
			}

			return $links;
		}

		/**
		 * Adds Extra Links to the plugins row meta.
		 * @since 1.1.0
		 */
		public function add_extra_links_plugin_row_meta( $plugin_meta, $plugin_file ) {
 
			if ( strpos( $plugin_file, NXT_PRO_EXT_BASE ) !== false && current_user_can( 'manage_options' ) && !apply_filters('nexter_remove_branding',false) ) {
				$new_links = array(
						'docs' => '<a href="'.esc_url('https://docs.posimyth.com/nexterwp').'" target="_blank" rel="noopener noreferrer" style="color:green;">'.esc_html__( 'Docs', 'nexter-pro-extensions' ).'</a>',
						'join-community' => '<a href="'.esc_url('https://www.facebook.com/groups/139678088029161/').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Join Community', 'nexter-pro-extensions' ).'</a>',
						'whats-new' => '<a href="'.esc_url('https://roadmap.nexterwp.com/updates').'" target="_blank" rel="noopener noreferrer" style="color: orange;">'.esc_html__( 'What\'s New?', 'nexter-pro-extensions' ).'</a>',
						'req-feature' => '<a href="'.esc_url('https://roadmap.nexterwp.com/boards/feature-requests').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Request Feature', 'nexter-pro-extensions' ).'</a>',
						'rate-theme' => '<a href="'.esc_url('https://wordpress.org/support/theme/nexter/reviews/?filter=5').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Rate Theme', 'nexter-pro-extensions' ).'</a>'
						);
				 
				$plugin_meta = array_merge( $plugin_meta, $new_links );
			}
			 
			return $plugin_meta;
		}

		/**
		 * Reset Nexter White Label
		 */
		public function nexter_white_label_reset(){
			if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error();
			}
			if(isset($_POST["submit-key"]) && !empty($_POST["submit-key"]) && $_POST["submit-key"]=='reset'){
				if ( ! isset( $_POST['nexter_nonce'] ) || ! wp_verify_nonce( sanitize_key($_POST['nexter_nonce']), 'nexter_admin_nonce' ) ) {
					wp_send_json(['success' => false]);
				} else {
					if (get_option('nexter_white_label') !== false) {
						delete_option('nexter_white_label');
					}
					wp_send_json(['success' => true]);
				}
			}
		}
	}
}

Nexter_Pro_Extensions_Load::get_instance();
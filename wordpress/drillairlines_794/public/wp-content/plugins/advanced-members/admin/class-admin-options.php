<?php
namespace AMem;

use AMem\Module;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ADMIN_OPTIONS' ) ) :
class ADMIN_OPTIONS extends Module {

	protected $name = 'amem/admin_options';
	var $page;


	function __construct() {
		add_action( 'admin_init', array( $this, 'update_option' ), 10, 0 );
		add_action( 'admin_init', array( $this, 'update_dashboard' ), 10, 0 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );
		add_filter( 'parent_file', array( $this, 'ensure_menu_selection' ) );
		add_action( "wp_ajax_amem/add_default_rule", array( $this, 'add_option_default_rule' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 30, 0 );
		add_filter( 'acf/admin/toolbar', array( $this, 'remove_separator_menu') );

		// Core Pages option
		add_action( 'admin_init', [$this, 'register_core_page_option_button'] );
	}

	/**
	 *  admin_menu
	 *
	 *  @since   1.0.0
	 *
	 *  @param   void
	 *  @return  void
	 */
	function admin_menu() {
		// $page = add_submenu_page( 'edit.php?post_type=amem-form', __( 'Setting Pages', 'advanced-members' ), __( 'Setting Pages', 'advanced-members' ), 'manage_options', 'amem_settings', array( $this, 'render' ) );
		$parent_slug = 'edit.php?post_type=acf-field-group';

		$dashboard_page = add_submenu_page( $parent_slug, __( 'Advanced Members', 'advanced-members' ), __( 'Advanced Members', 'advanced-members' ), 'manage_options', 'amem_dashboard', array( $this, 'amem_dashboard' ) );
		add_action( 'load-' . $dashboard_page, array( $this, 'admin_load' ) );
		$page = add_submenu_page( $parent_slug, __( 'Advanced Members Settings', 'advanced-members' ), __( 'Settings', 'advanced-members' ), 'manage_options', 'amem_settings', array( $this, 'render' ) );
		add_action( 'load-' . $page, array( $this, 'admin_load' ) );
		add_submenu_page( $parent_slug, __( 'Members Forms', 'advanced-members' ), __( 'Forms', 'advanced-members' ), 'manage_options', 'edit.php?post_type=amem-form' );
		// $this->add_submenu_separator( $parent_slug, 5, 'members-eparator'); // 예시: 5번째 위치에 섹션 구분선 삽입
	}

	/**
	 * Ensure the ACF parent menu is selected for add-new.php
	 *
	 * @since 6.1
	 * @param string $parent_file The parent file checked against menu activation.
	 * @return string The modified parent file
	 */
	public function ensure_menu_selection( $parent_file ) {
		if ( ! is_string( $parent_file ) ) {
			return $parent_file;
		}
		if ( strpos( $parent_file, 'edit.php?post_type=amem-' ) === 0 ) {
			return 'edit.php?post_type=acf-field-group';
		}
		return $parent_file;
	}

	function add_submenu_separator($parent_slug, $position, $menu_title) {
    global $submenu;
    // 배열의 특정 위치에 섹션 구분선을 삽입
    $new_submenu_item = array('<hr class="submenu-separator">', 'read', '#'.sanitize_title($menu_title), '', 'wp-menu-separator amem-submenu-separator-wrap');

    // 해당 위치에 삽입
		if(isset($submenu[$parent_slug])){
			array_splice($submenu[$parent_slug], $position, 0, array($new_submenu_item));
		}
}


	/**
	 * Load the body class and scripts.
	 *
	 * @since 1.0.0
	 */
	public function admin_load() {

		add_action( 'admin_body_class', array( $this, 'admin_body_class' ) );
		add_action( 'admin_print_scripts', array( $this, 'admin_head' ) );
		// acf_enqueue_scripts();
	}

	public function update_dashboard() {
		if( !isset($_POST['dashboard_update_nonce']) || !wp_verify_nonce( sanitize_key($_POST['dashboard_update_nonce']), 'amem_dashboard_update' ) ){ // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
			return false;
		}

		if( isset($_POST['amem_modules'])){ // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
			$new_modules = amem_sanitize_vars( $_POST['amem_modules'] );

			update_option( 'amem_modules', $new_modules ); // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
			amem()->options->modules = $new_modules; // phpcs:disable WordPress.Security.NonceVerification -- already verified by ACF
			acf_add_admin_notice( __('Dashboard Update Success', 'advanced-members'), 'success' );
		}
	}

	public function update_option() {
		if( !isset($_POST['option_update_nonce']) || !wp_verify_nonce( sanitize_key($_POST['option_update_nonce']), 'amem_options_update' ) ) {
			return false;
		}

		if( !empty($_POST['amem_options']) || !is_array($_POST['amem_options']) ) {
			$new_options = $this->sanitize_options( $_POST['amem_options'] );
			$old_options = get_option( 'amem_options' );

			$new_options = $this->preserve_options( $new_options, $old_options );
			update_option( 'amem_options', $new_options );

			do_action( 'amem/admin/update_options' );

			acf_add_admin_notice( __('Options Update Success', 'advanced-members'), 'success' );
		}
	}

	protected function preserve_options($value, $old_value) {
		if ( !is_admin() || empty($_POST['amem_options']) || !is_array($old_value) || empty($old_value) )
			return $value;

		// Keep disabled moudle settings
		foreach( array_keys($old_value) as $key ) {
			if ( !isset($value[$key]) ) {
				$value[$key] = $old_value[$key];
			}

			$value[$key] = apply_filters( 'amem/validate_options/' . $key, $value[$key], (isset($old_value[$key]) ? $old_value[$key] : null ) );
		}

		return $value;
	}


	function sanitize_email_body($body) {
		return amem_sanitize_html($body);
	}

	function sanitize_options( $data, $sanitizers = null ) {
		return amem_sanitize_data($data, $sanitizers);
	}

	/**
	 * Modifies the admin body class.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Space-separated list of CSS classes.
	 * @return string
	 */
	public function admin_body_class( $classes ) {
		$classes .= ' amem-admin-page amem-option-page acf-admin-page';
		return $classes;
	}

	/**
	*  postbox_submitdiv
	*
	*  This function will render the submitdiv metabox
	*
	*  @type    function
	*  @since   1.0.0
	*
	*  @param   n/a
	*  @return  n/a
	**/

	function postbox_submitdiv( $post, $args ) {

		/**
		*   Fires before the major-publishing-actions div.
		*
		*  @since 1.0
		*
		*  @param array $page The current options page.
		*/
		do_action( 'acf/options_page/submitbox_before_major_actions' );
		?>
		<div id="major-publishing-actions">

			<div id="publishing-action">
				<span class="spinner"></span>
				<input type="submit" accesskey="p" value="<?php esc_attr_e('Update', 'advanced-members')?>" class="button button-primary button-large" id="publish" name="publish">
			</div>
		<?php
		/**
		 *   Fires before the major-publishing-actions div.
		 *
		 *  @since   1.0
		 *  @param array $page The current options page.
		 */
		do_action( 'acf/options_page/submitbox_major_actions' );
		?>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	*  postbox_submit_dashboard
	*
	*  This function will render the submitdiv metabox
	*
	*  @type    function
	*  @since   1.0.0
	*
	*  @param   n/a
	*  @return  n/a
	**/
	function postbox_submit_dashboard( $post, $args ) {

		/**
		*   Fires before the major-publishing-actions div.
		*
		*  @since 1.0
		*
		*  @param array $page The current options page.
		*/
		do_action( 'acf/options_page/submitbox_before_major_actions' );
		?>
		<div id="major-publishing-actions">

			<div id="publishing-action">
				<span class="spinner"></span>
				<input type="submit" accesskey="p" value="<?php esc_attr_e('Save Changes', 'advanced-members')?>" class="button button-primary button-large" id="publish" name="publish">
			</div>
		<?php
		/**
		 *   Fires before the major-publishing-actions div.
		 *
		 *  @since   1.0
		 *  @param array $page The current options page.
		 */
		do_action( 'acf/options_page/submitbox_major_actions' );
		?>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	*  postbox_dashboard_document
	*
	*  This function will render the submitdiv metabox
	*
	*  @type    function
	*  @since   1.0.0
	*
	*  @param   n/a
	*  @return  n/a
	**/
	function postbox_dashboard_document( $post, $args ){
		?>
		<div class="document_text">
			<p>Need Help?
			We have a knowledge
			base full of articles to get
			you started.</p>
			<a target="_blank" href="https://advanced-members.com/doc/getting-started/">Browse Documentation</a>
		</div>
		<?php
	}

	/**
	*  admin_head
	*
	*  This action will find and add field groups to the current edit page
	*
	*  @type    action (admin_head)
	*  @since   1.0.0
	*
	*  @param   n/a
	*  @return  n/a
	**/

	function admin_head() {
		// notices
		// if ( ! empty( $_GET['message'] ) && sanitize_key($_GET['message']) == '1' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Used to display a notice.
		// 	acf_add_admin_notice( $this->page['updated_message'], 'success' );
		// }

		// add submit div
		// add_meta_box( 'submitdiv', __( 'Update Settings', 'advanced-members' ), array( $this, 'postbox_submitdiv' ), 'amem_options_page', 'side', 'high' );
		// add_meta_box( 'submitdiv', __( 'Save', 'advanced-members' ), array( $this, 'postbox_submit_dashboard' ), 'amem_dashboard_page', 'side', 'high' );
		// add_meta_box( 'dashdocu', __( 'Documentation', 'advanced-members' ), array( $this, 'postbox_dashboard_document' ), 'amem_dashboard_page', 'side', 'low' );
	}

	function amem_dashboard() {
		/*
		$page = ! empty( $_REQUEST['page'] ) ? sanitize_key( $_REQUEST['page'] ) : '';

		if ( $page == 'amem_dashboard' ) { ?>
			<div id="amem-metaboxes-general" class="wrap">
				<h1><?php esc_html_e( 'Advanced Members for ACF', 'advanced-members' )?></h1>
				<div id="dashboard-widgets-wrap">
					<div id="dashboard-widgets" class="metabox-holder um-metabox-holder">
					</div>
				</div>
			</div>
			<div class="clear"></div>
		<?php
		}
*/
		$screen = get_current_screen();
		$view   = array( 'screen_id' => $screen->id );

		$default_settings_tabs = apply_filters( 'amem/member_dashboard/dashboard_tabs', array(
			'modules'		=> array( 'label' => __( 'Modules', 'advanced-members' ), 'link' => '#' ),
		));

		// _acf_apply_get_local_field_groups()
		$settings_data = array(
			'page_title' 	=> __( 'Advanced Members Dashboard' , 'advanced-members' ),
			'tabs' 				=> $default_settings_tabs,
		);

		amem_get_view( __DIR__ . '/views/html-dashboard-page.php', $settings_data );
	}

	/**
	 * The render for the options page preview view.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$screen = get_current_screen();
		$view   = array( 'screen_id' => $screen->id );
		$option_tabs = array(
			'general'     		=> __( 'General', 'advanced-members' ),
			'account'		=> __( 'Account', 'advanced-members' ),
		);
		if ( amem()->options->getmodule('_use_redirects') ) {
			$option_tabs['redirects'] = __( 'Redirects', 'advanced-members' );
		}
		if ( amem()->options->getmodule('_use_restriction') ) {
			$option_tabs['restriction'] = __( 'Content Restriction', 'advanced-members' );
		}
		if ( amem()->options->getmodule('_use_adminbar') ) {
			$option_tabs['adminbar'] = __( 'Admin Bar', 'advanced-members' );
		}
		if ( amem()->options->getmodule('_use_avatar') ) {
			$option_tabs['avatar'] = __( 'Avatar', 'advanced-members' );
		}
		if ( amem()->options->getmodule('_use_recaptcha') ) {
			$option_tabs['recaptcha'] = __( 'reCAPTCHA', 'advanced-members' );
		}

		$option_tabs['email'] = __( 'Emails', 'advanced-members' );

		// Re-initialize updated options
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' || !empty($_REQUEST['amem_do_action']) ) {
			amem()->options->init(true);
		}

		$default_settings_tabs = apply_filters( 'amem/member_settings/settings_tabs', $option_tabs);

		// _acf_apply_get_local_field_groups()
		$settings_data = array(
			'page_title' 	=> __( 'Members Settings' , 'advanced-members' ),
			'tabs' 				=> $default_settings_tabs,
			// 'fields'			=> $fields,
		);

		amem_get_view( __DIR__ . '/views/html-options-page.php', $settings_data );

	}

	function add_option_default_rule() {
		global $wp_roles;
		$all_roles = $wp_roles->roles;
		$roles = array();
		foreach ($all_roles as $key => $role) {
			if( sanitize_key($_POST['ruletab']) == 'regform' && $key == 'administrator') continue;

			$roles[$key] = translate_user_role($role['name']);
		}
		$account_forms = array(
			0 => __('Not Selected', 'advanced-members')
		);
		foreach ( get_posts(array(
				'post_type' => 'amem-form',
				'numberposts' => -1,
				'sort_column' => 'post_title',
				'sort_order' => 'ASC',
				'meta_query' => array(
						array(
								'key' => 'select_type',
								'value' => 'account',
								'compare' => '=',
						),
				),
		)) as $form ){
			$account_forms[$form->ID] = $form->post_title;
		};
		$rule = array('id'=> 'rule_0', 'role' => '','value' => '');
		$ruletab = amem_sanitize_vars($_POST['ruletab']);
		ob_start();
		amem_get_view(
			__DIR__ . '/views/location-rule.php',
			array(
				'ruletab'	=> $ruletab,
				'roles'		=> $roles,
				'forms'		=> $account_forms,
				'rule' 		=> $rule,
			)
		);
		$content = ob_get_clean();
		wp_send_json_success( array( 'content' => $content ) );
	}

	function enqueue_admin_scripts() {
		$min = '';//defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'amem-admin', amem_get_url("admin{$min}.css", 'assets/css'), ['acf-global', 'acf-input'], AMEM_VERSION );

		$field_types = acf_get_grouped_field_types();
		$css_fields = [];
		if ( isset($field_types[amem()->field_cat]) ) {
			foreach ( $field_types[amem()->field_cat] as $type => $name ) {
				$css_fields[] = '.field-type-icon.field-type-icon-' . str_replace('_', '-', $type) . ':before';
			}

			$inline_css = implode( ', ', $css_fields );
			$inline_css .= ' { -webkit-mask-image: url(' . amem()->icon_src . '); mask-image: url(' . amem()->icon_src . '); }';
			wp_add_inline_style( 'amem-admin', $inline_css );
		}

		$amem_screens = apply_filters( 'amem/admin/screens', ['acf_page_amem_settings', 'acf_page_amem_dashboard'] );
		$amem_edit_screens = apply_filters( 'amem/admin/edit_screens', ['amem-form', 'edit-amem-form'] );

		$check_screens = array_merge( amem()->admin->screens(), amem()->admin->edit_screens(), ['acf-field-group'] );

		if ( !amem_is_screen( $check_screens ) )
			return;

		amem()->fields->enqueue_scripts();

		amem_register_script( 'amem-admin', amem_get_url("amem-admin{$min}.js", 'assets/js'), ['jquery', 'acf-input', 'amem-input'], AMEM_VERSION, ['in_footer' => true, 'asset_path' => amem_get_path('', 'assets/js')] );

		$account_forms = array(
			0 => __('Not Selected', 'advanced-members')
		);
		foreach ( get_posts(array(
				'post_type' => 'amem-form',
				'numberposts' => 999,
				'sort_column' => 'post_title',
				'sort_order' => 'ASC',
				'meta_query' => array(
						array(
								'key' => 'select_type',
								'value' => 'account',
								'compare' => '=',
						),
				),
		)) as $form ){
			$account_forms[$form->ID] = $form->post_title;
		};

		wp_localize_script( 'amem-admin', 'amem_options',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'account_forms'	=> $account_forms,
				'nonce' => wp_create_nonce( 'amem-admin-ajax' ),
			)
		);

		wp_enqueue_script( 'amem-admin' );

		do_action( 'amem/admin/enqueue_scripts' );
	}

	function remove_separator_menu($more_items) {
		foreach ($more_items as $key => $item) {
			if( strpos($item['text'], 'submenu-separator') !== false ){
				unset($more_items[$key]);
			}
		}
		return $more_items;
	}

	function register_core_page_option_button() {
		foreach( array_keys(amem()->config->get_core_pages() ) as $page_key ) {
			$name = amem()->options->get_core_page_id($page_key);
			// $name = "amem_options[{$name}]";
			add_action( 'acf/render_field/name=' . $name, [$this, 'core_page_option_button'] );
		}
	}

	function core_page_option_button($field) {
		if ( $field['prefix'] == 'amem_options' && $field['value'] ) {
			$page_id = (int) $field['value'];
			echo edit_post_link( __('Edit Page', 'advanced-members'), '', '', $page_id, 'page-edit-link button' );

			// if ( $form_ids = $this->get_content_forms($page_id, $field['key']) ) {
			// 	$form_edit_link = edit_post_link( __('Edit Form', 'advanced-members'), '', '', $form_ids[0], 'form-edit-link button' );
			// 	echo $form_edit_link;
			// }
		}
	}

	protected function get_content_forms($page_id, $type='login') {
		$type = str_replace( 'core_', '', $type );

		switch( $type ) {
			case 'registration':
			case 'register':
			case 'login':
			$find = 'advanced-members';
			break;
			case 'account':
			if ( amem()->options->get('accform/default') )
				return amem()->options->get('accform/default');
			return false;
			break;
			default:
			return false;
			break;
		}

		$page = get_post( $page_id );
		if ( !$page )
			return false;

		$content = $page->post_content;
		$pattern = get_shortcode_regex([$find]);
		preg_match_all( "/$pattern/", $content, $matches );

		$found = [];
		foreach ( $matches[0] as $k => $tag ) {
			if ( !empty($matches[3][$k]) ) {
				$atts = shortcode_parse_atts($matches[3][$k]);
				if ( isset($atts['form']) )
					$found[] = (int) $atts['form'];
			}
		}

		return array_filter( array_unique($found) );
	}

}

amem()->register_module('admin_options', ADMIN_OPTIONS::getInstance());

endif; // class_exists check

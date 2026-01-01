<?php
/**
 * AMem Admin Form Class
 *
 * @class AMem\Admin\Form
 *
 * @package    AMem
 * @subpackage Admin
 */
namespace AMem\Admin;

use AMem\Admin\Post;

if ( ! class_exists( 'AMem\Admin\Form' ) ) :

	/**
	 * ACF Admin Post Type Class
	 *
	 * All the logic for editing a post type.
	 */
	class Form extends Post {
		/**
		 * The slug for the internal post type.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $post_type = 'amem-form';

		/**
		 * The admin body class used for the post type.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $admin_body_class = '';


		public function __construct() {
			parent::__construct();

			add_action( 'save_post_page', [$this, 'save_form_for_page'], 10, 2 );
		}

		/**
		 * This function will customize the message shown when editing a post type.
		 *
		 * @since 1.0.0
		 *
		 * @param array $messages Post type messages.
		 * @return array
		 */
		public function post_updated_messages( $messages ) {
			$messages['amem-form'] = array(
				0  => '', // Unused. Messages start at index 1.
				1  => $this->post_created_message(), // Updated.
				2  => $this->post_created_message(),
				3  => __( 'Form deleted.', 'advanced-members' ),
				4  => __( 'Form updated.', 'advanced-members' ),
				5  => false, // Post type does not support revisions.
				6  => $this->post_created_message( true ), // Created.
				7  => __( 'Form saved.', 'advanced-members' ),
				8  => __( 'Form submitted.', 'advanced-members' ),
				9  => __( 'Form scheduled for.', 'advanced-members' ),
				10 => __( 'Form draft updated.', 'advanced-members' ),
			);

			return $messages;
		}

		/**
		 * Renders the post type created message.
		 *
		 * @since 1.0.0
		 *
		 * @param boolean $created True if the post was just created.
		 * @return string
		 */
		public function post_created_message( $created = false ) {
			global $post_id;

			$title = get_the_title( $post_id );

			/* translators: %s post type name */
			$item_saved_text = sprintf( __( '%s form updated', 'advanced-members' ), $title );

			if ( $created ) {
				/* translators: %s post type name */
				$item_saved_text = sprintf( __( '%s form created', 'advanced-members' ), $title );
			}

			$add_fields_link = wp_nonce_url(
				admin_url( 'post-new.php?post_type=acf-field-group&use_post_type=' . $post_id ),
				'add-fields-' . $post_id
			);

			$create_form_link    = admin_url( 'post-new.php?post_type=amem-form' );
			$duplicate_form_link = wp_nonce_url(
				admin_url( 'post-new.php?post_type=amem-form&use_post_type=' . $post_id ),
				'acfduplicate-' . $post_id
			);
			ob_start(); ?>
			<p class="acf-item-saved-text"><?php echo esc_html( $item_saved_text ); ?></p>
			<?php
			return ob_get_clean();
		}

		function current_screen() {
			if ( ! acf_is_screen( $this->post_type ) ) {
				return;
			}

			add_filter( 'acf/prepare_field/name=amem_form_shortcode_message', array( $this, 'display_form_shortcode' ), 10, 1 );
			add_filter( 'add_post_metadata', array( $this, 'should_add_form_key_meta' ), 10, 3 );

			add_action( 'acf/add_meta_boxes', array( $this, 'add_fields_meta_box' ), 10, 0 );

			parent::current_screen();
		}

		/**
		 * Settings Fields
		 *
		 * @since 1.0.0
		 */
		public function register_fields() {
			global $post, $amem_current_post;
			static $registered;

			if ( isset($registered) )
				return;

			$amem_current_post = $post;

			$form_ajax = amem()->options->get('ajax_submit');

			$post_id = $amem_current_post->ID;
			$active = $this->is_active( $amem_current_post );

			$general_fields = [
				// General Tab
				array (
					'label' 							=> __( 'General', 'advanced-members' ),
					'prefix' => 'amem_form',
					'key' => 'general_tab',
					'name' 								=> '',
					'type' 								=> 'tab',
					'instructions' 				=> '',
					'required' 						=> 0,
					'conditional_logic' 	=> 0,
					'wrapper' 						=> array (
						'width' 		=> '',
						'class' 		=> '',
						'id' 				=> '',
					),
					'placement' 					=> 'top',
					'endpoint' 						=> 0,
				),

				array(
					'prefix' => 'amem_form',
					'key' => 'shortcode_message',
					'name' 								=> 'amem_form_shortcode_message',
					'label' 							=> __( 'Shortcode', 'advanced-members' ),
					'type' 								=> 'message',
				),

				array(
					'prefix' => 'amem_form',
					'key' => 'select_type',
					'name' 								=> 'select_type',
					// 'type' 								=> 'button_group',
					'type' 								=> 'radio',
					'layout' 							=> 'horizontal',
					'label' 							=> __( 'Form Type', 'advanced-members' ),
					// 'value'        => get_post_meta( $post_id, 'select_type', true ),
					'default'   					=> 'registration',
					'choices'         		=> array(
						'registration'	=> __( 'Registration', 'advanced-members' ),
						'login' 				=> __( 'Login', 'advanced-members' ),
						'account'				=> __( 'Account', 'advanced-members' ),
					),
				),
				array(
					'prefix' => 'amem_form',
					'key' => 'ajax_override',
					'name' 								=> 'ajax_override',
					'type' 								=> 'true_false',
					'label' 							=> __( 'Override Global AJAX setting', 'advanced-members' ),
					// 'value'        => get_post_meta( $post_id, 'ajax_override', true ),
					'default'   					=> 0,
					'default_value' 			=> 0,
					'instructions' 				=> __( 'Override the Global AJAX option and force the Form AJAX setting', 'advanced-members' ),
					// 'message' 						=> __( 'Enable/disable AJAX form submit instead of page load.', 'advanced-members' ),
					'ui' => 1,
				),
				array(
					'prefix' => 'amem_form',
					'key' => 'ajax',
					'name' 								=> 'ajax',
					'type' 								=> 'true_false',
					'label' 							=> __( 'AJAX Submit', 'advanced-members' ),
					// 'value'        => get_post_meta( $post_id, 'ajax', true ),
					'default'   					=> 0,
					'default_value' 			=> 0,
					'instructions' 				=> __( 'Enable/disable AJAX form submit instead of page load. This overrides the global option and is overridden by the shortcode attribute ajax="0"', 'advanced-members' ),
					// 'message' 						=> __( 'Enable/disable AJAX form submit instead of page load.', 'advanced-members' ),
					'ui' => 1,
					'conditions'   				=> array(
						'field'    => 'ajax_override',
						'operator' => '==',
						'value'    => '1',
					),
				),
			];

			if ( amem()->options->getmodule('_use_recaptcha') ) {
				if ( !amem()->recaptcha->is_ready() ) {
					$general_fields[] = array(
						'label' => __( 'reCAPTCHA disabled', 'advanced-members' ),
						'type' 								=> 'message',
						'name' => 'recaptcha_disabled',
						'key' => 'recaptcha_disabled',
						'prefix' 					 => 'amem_form',
						/* translators: 1: Settings URL, 2: reCAPTCHA console URL */
						'message' => '<div class="acf-notice -warning"><div>' . sprintf( __( 'Valid Google reCAPTCHA site key and secret key <a href="%1$s">should be set</a> before applying reCAPTCHA to the form. <a href="%2$s" target="_blank">reCAPTCHA API Admin</a>', 'advanced-members' ), '/wp-admin/edit.php?post_type=acf-field-group&page=amem_settings', 'https://www.google.com/recaptcha/admin' ) . '</div></div>',
					);
				} else {

					// $general_fields[] = array(
					// 	'prefix' => 'amem_form',
					// 	'key' => 'recaptcha_override',
					// 	'name' 								=> 'recaptcha_override',
					// 	'type' 								=> 'true_false',
					// 	'label' 							=> __( 'Override Global reCAPTCHA setting', 'advanced-members' ),
					// 	'default'   					=> 0,
					// 	'default_value' 			=> 0,
					// 	'ui' => 1,
					// );
					$general_fields[] = array(
						'prefix' => 'amem_form',
						'key' => 'recaptcha',
						'name' 								=> 'recaptcha',
						'type' 								=> 'true_false',
						'label' 							=> __( 'Use reCAPTCHA', 'advanced-members' ),
						'default'   					=> 0,
						'default_value' 			=> 0,
						'instructions' 				=> __( 'Check form submission with reCAPTCHA.', 'advanced-members' ),
						'ui' => 1,
						// 'conditions'   				=> array(
						// 	'field'    => 'recaptcha_override',
						// 	'operator' => '==',
						// 	'value'    => '1',
						// ),
					);
				}
			}

			$login_fields = [
				// show rememberme
				array(
					'type'         				=> 'true_false',
					'key' => 'login_rememberme',
					'name'         				=> 'login_rememberme',
					'prefix'       				=> 'amem_form',
					'label'        				=> __( 'Show &quot;Remember Me&quot;', 'advanced-members' ),
					'instructions' 				=> __( 'Allow users to choose If they want to stay signed in even after closing the browser.', 'advanced-members' ),
					// 'value'        => get_post_meta( $post_id, 'login_rememberme', true ),
					// 'default_value' => 1,
					'ui' => 1,
					'conditions'   				=> array(
						array(
							'field'    => 'select_type',
							'operator' => '==',
							'value'    => 'login',
						),
					),
				),
				// show forgot password
				array(
					'type'         				=> 'true_false',
					'key' => 'login_password_reset',
					'name'         				=> 'login_password_reset',
					'prefix'       				=> 'amem_form',
					'label'        				=> __( 'Forgot Password Link', 'advanced-members' ),
					'instructions' 				=> __( 'Show the forgot password link in the login form', 'advanced-members' ),
					// 'value'        => get_post_meta( $post_id, 'login_password_reset', true ),
					'ui' => 1,
					// 'default_value' => 1,
					'conditions'   				=> array(
						array(
							'field'    => 'select_type',
							'operator' => '==',
							'value'    => 'login',
						),
					),
				),
				array(
					'type'         				=> 'true_false',
					'key' => 'login_extra_button',
					'name'         				=> 'login_extra_button',
					'prefix'       				=> 'amem_form',
					'label'        				=> __( 'Extra Button', 'advanced-members' ),
					'instructions' 				=> __( 'Use a secondary button on the login form.', 'advanced-members' ),
					// 'value'        => get_post_meta( $post_id, 'login_extra_button', true ),
					'ui' => 1,
					'conditions'   				=> array(
						array(
							'field'    => 'select_type',
							'operator' => '==',
							'value'    => 'login',
						),
					),
				),

				// extra button text
				array(
					'type'         				=> 'text',
					'name'         				=> 'login_extra_text',
					'key'          				=> 'login_extra_text',
					'prefix'       				=> 'amem_form',
					'label'        				=> __( 'Secondary Button Text', 'advanced-members' ),
					'instructions' 				=> __( 'Secondary button text on the login form. Leave empty for &quot;Register&quot;', 'advanced-members' ),
					// 'value'        => get_post_meta( $post_id, 'login_extra_text', true ),
					'conditions'   				=> array(
						array(
							'field'    => 'select_type',
							'operator' => '==',
							'value'    => 'login',
						),
						array(
							'field'    => 'login_extra_button',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
				// extra button url
				array(
					'type'         				=> 'text',
					'name'         				=> 'login_extra_url',
					'key'          				=> 'login_extra_url',
					'prefix'       				=> 'amem_form',
					'label'        				=> __( 'Secondary Button URL', 'advanced-members' ),
					'instructions' 				=> __( 'Secondary button URL. Leave empty to use the Registration page URL', 'advanced-members' ),
					// 'value'        => get_post_meta( $post_id, 'login_extra_url', true ),
					'conditions'   				=> array(
						array(
							'field'    => 'select_type',
							'operator' => '==',
							'value'    => 'login',
						),
						array(
							'field'    => 'login_extra_button',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
			];

			$registration_fields = [
				array(
					'type'         				=> 'select',
					'name'         				=> 'regist_role',
					'key'          				=> 'regist_role',
					'prefix'       				=> 'amem_form',
					'label'        				=> __( 'Registration Role', 'advanced-members' ),
					'instructions' 				=> __( 'The role assigned upon registration through this sign-up form.', 'advanced-members' ),
					// 'value'        => get_post_meta( $post_id, 'regist_role', true ),
					'multiple'     				=> false,
					'default'							=> 'subscriber',
					'allow_null'   				=> 1,
					'ui'           				=> 0,
					'multiple' 						=> 0,
					// 'hide_search'  				=> true,
					'choices'         		=> $this->get_user_role_choice( array('administrator')),
					'conditions'   				=> [
						[
							'field'    => 'select_type',
							'operator' => '==',
							'value'    => 'registration',
						],
					],
				),
				array(
					'type'								=> 'select',
					'name'								=> 'regist_status',
					'key'          				=> 'regist_status',
					'prefix'       				=> 'amem_form',
					'label'        				=> __( 'Registration Status', 'advanced-members' ),
					'instructions' 				=> __( 'Select what action is taken after a person registers on your site. Depending on the status you can redirect them to their profile, a custom url or show a custom message', 'advanced-members' ),
					// 'value'        => get_post_meta( $post_id, 'regist_status', true ),
					'default'      				=> 1,
					'default_value' => 'mailcheck',
					'choices'         		=> array(
						'approve'						=> __( 'Auto Approve', 'advanced-members' ),
						'mailcheck' 				=> __( 'Requires Email Activation', 'advanced-members' ),
					),
					'conditions'   				=> array(
						'field'    => 'select_type',
						'operator' => '==',
						'value'    => 'registration',
					),
				),
				array(
					'type'								=> 'true_false',
					'name'								=> 'regist_force_show_message',
					'key'          				=> 'regist_force_show_message',
					'prefix'       				=> 'amem_form',
					'label'        				=> __( 'Force show success message', 'advanced-members' ),
					'instructions' 				=> __( 'Do not redirect after registration and show a message instead of the form. Useful when &quot;Requires Email Activation&quot; is selected above.', 'advanced-members' ),
					// 'value'        => get_post_meta( $post_id, 'regist_status', true ),
					'default'      				=> 0,
					'default_value' => 0,
					'ui' => 1,
					'conditions'   				=> array(
						array(
							'field'    => 'select_type',
							'operator' => '==',
							'value'    => 'registration',
						),
						array(
							'field'    => 'regist_status',
							'operator' => '==',
							'value'    => 'mailcheck',
						),
					),
				),
				array(
					'type'         				=> 'textarea',
					'name'         				=> 'registration_show_message',
					'key'          				=> 'registration_show_message',
					'prefix'       				=> 'amem_form',
					'label'        				=> __( 'The custom message', 'advanced-members' ),
					'value'        => get_post_meta( $post_id, 'registration_show_message', true ),
					'default_value'				=> __('Thank you for registering. Before you can login we need you to activate your account by clicking the activation link in the email we just sent you.', 'advanced-members'),
					'conditions'   				=> array(
						array(
							'field'    => 'select_type',
							'operator' => '==',
							'value'    => 'registration',
						),
						array(
							'field'    => 'regist_force_show_message',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
			];

			$account_fields = [];

			$account_fields[] = array(
				'type'         				=> 'message',
				'name'         				=> 'account_unset_fields',
				'key'          				=> 'account_unset_fields',
				'prefix'       				=> 'amem_form',
				'disabled' => true,
				'readonly' => true,
				'label'        				=> __( 'Unset Fields', 'advanced-members' ),
				'message' => __( 'Advanced Members will unset username, user email, and user password fields and show them with core fields.', 'advanced-members' ),
				'conditions'   				=> array(
					array(
						'field'    => 'select_type',
						'operator' => '==',
						'value'    => 'account',
					),
				),
			);

			$submit_text = __( 'Submit', 'advanced-members' );
			if ( !empty($_GET['post'])) {
				$submit_text = amem_submit_text_default( $post_id );
				// $form_type = get_field( 'select_type', (int) $_GET['post'] );
				// $form_type = get_post_meta( $post_id, 'select_type', true );
				// switch ( $form_type ) {
				// 	case 'login':
				// 	$submit_text = __( 'Login', 'advanced-members' );
				// 	break;
				// 	case 'registration':
				// 	$submit_text = __( 'Register', 'advanced-members' );
				// 	break;
				// 	case 'account':
				// 	$submit_text = __( 'Update Account', 'advanced-members' );
				// 	break;
				// 	default:
				// 	$submit_text = apply_filters( 'amem/form/submit_text_default/type=' . $form_type, $submit_text );
				// 	break;
				// }
			}
			$general_fields_more = [
				// array (
				// 	'key' 								=> 'description',
				// 	'label' 							=> __( 'Description', 'advanced-members' ),
				// 	'name' 								=> 'description',
				// 	'type' 								=> 'textarea',
				// 	'instructions' 				=> '',
				// 	'required' 						=> 0,
				// 	'conditional_logic' 	=> 0,
				// 	'wrapper' 						=> array (
				// 		'width' 	=> '',
				// 		'class' 	=> '',
				// 		'id' 			=> '',
				// 	),
				// 	'default_value' 			=> '',
				// 	'tabs' 								=> 'all',
				// 	'toolbar' 						=> 'full',
				// 	'media_upload' 				=> 1,
				// ),
				array(
					'type'         				=> 'text',
					'name'         				=> 'submit_text',
					'key'          				=> 'submit_text',
					'prefix'       				=> 'amem_form',
					'label'        				=> __( 'Submit Button Text', 'advanced-members' ),
					'instructions' 				=> __( 'Submit button text. Leave empty to use the default text.', 'advanced-members' ),
					// 'value'        => get_post_meta( $post_id, 'submit_text', true ),
					'placeholder' 				=> $submit_text,
					// 'conditions'   				=> array(
					// 	'field'    => 'select_type',
					// 	'operator' => '!=',
					// 	'value'    => 'account',
					// ),
				),
			];


			$general_tab = array_merge( $general_fields, $login_fields, $registration_fields, $account_fields, $general_fields_more );
			/* 차후 탭 확장용
			$visibility_tab = [
				// Visivility Tab
				array (
					'key' 								=> 'visibility_tab',
					'label' 							=> __( 'Visibility', 'advanced-members' ),
					'name' 								=> '',
					'type' 								=> 'tab',
					'instructions' 				=> '',
					'required' 						=> 0,
					'conditional_logic' 	=> 0,
					'wrapper' 						=> array (
						'width' 	=> '',
						'class' 	=> '',
						'id' 			=> '',
					),
					'placement' 					=> 'left',
					'endpoint' 						=> 0,
				),

				array (
					'key' => 'num_of_submissions',
					'label' => __( 'Number of submissions', 'advanced-members' ),
					'name' => 'num_of_submissions',
					'type' => 'number',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => 0,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'min' => '',
					'max' => '',
					'step' => '',
					'readonly' => true,
				),
				array (
					'key' => 'field_form_num_of_views',
					'label' => __( 'Number of times viewed', 'advanced-members' ),
					'name' => 'form_num_of_views',
					'type' => 'number',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'default_value' => 0,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'min' => '',
					'max' => '',
					'step' => '',
					'readonly' => true,
				),
			];

			$fields = array_merge($general_tab, $visibility_tab);
			*/
			$fields = $general_tab;//array_merge($general_tab);

			$fields[] = array(
				'prefix' => 'amem_form',
				'key' => 'active',
				'name'         => 'active',
				'label'        => __( 'Active', 'advanced-members' ),
				'instructions' => __( 'Enable or disable this form.', 'advanced-members' ),
				'type'         => 'true_false',
				'value'        => $active,
				'default_value'=> 1,
				'default' => 1,
				'ui'           => 1,
			);


			$settings_field_group = array (
				'key' 		=> 'amem-form-admin-settings',
				'title' 	=> __( 'Form settings', 'advanced-members' ),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'amem-form',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'field',
				'hide_on_screen' => '',
				'active' => 1,
				'description' => '',
				'fields' => $fields
			);

			$settings_field_group = apply_filters( 'amem/member_form/settings_fields', $settings_field_group );

			acf_add_local_field_group( $settings_field_group );

			$registered = true;
		}

		/**
		 *  User Role 목록을 반환
		 *
		 *  @since   1.0.0
		 *  @param array $unset role 목록에서 unset 할 role 목록
		 *  @return array $roles
		 */
		function get_user_role_choice( $unset = array() ) {
			global $wp_roles;

			$all_roles = $wp_roles->roles;
			$roles = array();
			foreach ($all_roles as $key => $role) {
				$roles[$key] = translate_user_role($role['name']);
			}

			foreach ($unset as $unsetkey) {
				unset($roles[$unsetkey]);
			}
			return $roles;
		}

		function render_settings_meta_box( $post, $metabox ) {
			// Render fields.

			// $this->register_fields();

			$field_group = acf_get_local_field_group( 'amem-form-admin-settings' );
			$fields = acf_get_fields( 'amem-form-admin-settings' );

			acf_render_fields( $fields, $post->ID, 'div', $field_group['instruction_placement'] );
		}

		/**
		 * amem-form post type 에 메타박스를 추가
		 *
		 * @since 1.0.0
		 *
		 */
		function add_fields_meta_box() {
			// add_meta_box( 'acf-amem-form-settings', 'Settings', array( $this, 'render_settings_meta_box' ), 'amem-form', 'normal', 'default' );

			// Connected Field Groups
			add_meta_box( 'amem-field-group-fields', __( 'Field Groups', 'advanced-members' ), array( $this, 'fields_meta_box_callback' ), 'amem-form', 'normal', 'default', null );

			// Connected Pages
			add_meta_box( 'amem-connected-pages', __( 'Form Pages', 'advanced-members' ), array( $this, 'pages_meta_box_callback' ), 'amem-form', 'normal', 'default', null );
		}

		/**
		 * FORM metabox callback
		 * 현재 폼 과 연결된 필드 그룹 과 필드 리스트
		 *
		 * @since 1.0.0
		 *
		 */
		function fields_meta_box_callback() {
			global $post;
			$form = amem_get_form( $post->ID );

			// Get field groups for the current form
			$field_groups = amem_get_form_field_groups( $form['key'] );
			$create_form = add_query_arg( 'amem_form', $form['key'], admin_url( 'post-new.php?post_type=acf-field-group' ) );
			if ( $post->post_title ) {
				$create_form = add_query_arg( 'post_title', $this->get_field_group_title($post->post_title), $create_form );
			}

			$all_fg_url = admin_url( 'edit.php?post_type=acf-field-group&amem_form=' . sanitize_key($form['key']) );
			?>
			<div class="advanced-members-field">
				<div class="advanced-members-label">
					<p class="description"><?php esc_html_e( 'Connect fields to the form by setting the location of your fields group to this form. You can connect multiple field groups to this form.', 'advanced-members' ); ?></p>
					<p><a href="<?php echo esc_url( $all_fg_url ) ?>">See all field groups for this form &raquo;</a></p>
				</div>

				<div class="advanced-members-input amem-fields-sortables">
					<?php 
					if ( $field_groups ):
					foreach ( $field_groups as $i => $field_group ) :
					// Get all fields for this field group
					$fields = acf_get_fields( $field_group );
					?>

					<div class="amem-sortable-item">
						<h3 class="amem-field-group-title"><span class="amem-icon amem-sortable-handle" title="Drag to reorder"><input type="hidden" name="amem_field_group_sort[]" value="<?php echo esc_attr($field_group['ID']) ?>" /></span> <a href="<?php echo esc_url( get_edit_post_link( $field_group['ID'] ) ); ?>" target="_blank"><?php echo esc_html($field_group['title']); ?></a></h3>

					<table class="widefat acf-field-group-table amem-field-list" data-form_id="<?php echo esc_attr($field_group['ID']) ?>" style="display: none;">
						<thead>
							<tr>
								<th scope="col" class="label"><?php esc_html_e( 'Label', 'advanced-members' ) ?></th>
								<th scope="col" class="name"><?php esc_html_e( 'Name', 'advanced-members' ) ?></th>
								<th scope="col" class="type"><?php esc_html_e( 'Type', 'advanced-members' ) ?></th>
								<?php do_action( 'amem/acf_field_group_th' , $form , $field_groups )?>
							</tr>
						</thead>
						<tbody>
						<?php foreach ( $fields as $field ) : ?>
							<tr>
								<td><?php echo esc_html($field['label']); ?></td>
								<td><?php echo esc_html($field['name']); ?></td>
								<td><?php echo esc_html( acf_get_field_type_label( $field['type'] ) ); ?></td>
								<?php do_action( 'amem/acf_forfield_group_td' , $form , $field )?>
							</tr>
						</tbody>
						<?php endforeach; ?>
					</table>
					</div>
						<?php endforeach; ?>
					<?php else: ?>
					<table class="widefat acf-field-group-table">
						<tr>
							<td colspan="3">
								<?php esc_html_e( 'No field groups connected to this form', 'advanced-members' ); ?>
							</td>
						</tr>
					</table>
					<?php endif; ?>
					<a href="<?php echo esc_url( $create_form ); ?>" class="button amem-create-field-group" target="_blank">
						<?php esc_html_e( 'Create field group', 'advanced-members' ); ?>
					</a>
				</div>
			</div>
			<?php
		}

		function save_form_for_page($post_id, $post) {
			// delete all existing
			if ( get_post_meta( $post_id, '_amem_form_inserted', true) )
				delete_post_meta( $post_id, '_amem_form_inserted' );

			if ( strpos($post->post_content, '[advanced-members') === false )
				return;

			$regex = '!\[advanced-members([^\]]+)form=("|\')?([0-9a-z_]+)([^\]]+)\]!';
			if ( preg_match_all( $regex, $post->post_content, $m ) && is_array($m[3]) ) {
				foreach( $m[3] as $match ) {
					add_post_meta( $post_id, '_amem_form_inserted', trim($match) );
				}
			}

			if ( preg_match_all( '!\[advanced-members-([^ ]+)\]!', $post->post_content, $m ) ) {
				foreach ( $m[1] as $match ) {
					add_post_meta( $post_id, '_amem_form_inserted', trim($match) );
				}
			}
		}

		function pages_meta_box_callback() {
			global $post, $wpdb;
			$form = amem_get_form( $post->ID );

			$needs_page = apply_filters( 'amem/admin/form/needs_page_connection', true, $form );

			if ( false === $needs_page ) 
				return;

			if ( true !== $needs_page ) {
				echo wp_kses_post($needs_page);
				return;
			}

			$post_id = $post->ID;
			$form_key = $form['key'];

			if ( $form['type'] == 'account' ) {
				$meta_values = ['account'];
			} else {
				$meta_values = [$post_id, $form_key];
			}

			$args = [
				'post_type' => 'page',
				'post_status' => 'any',
				'nopaging' => 1,
				'meta_query' => [
					[
						'key' => '_amem_form_inserted',
						'value' => $meta_values,
						'compare' => 'IN'
					]
				]
			];

			esc_html_e( 'Below are the pages where this form is embedded. You can view each Page on the frontend or edit it in the admin screen.', 'advanced-members' );

			$pages = new \WP_Query($args);
			$allPages = false;
			if ( $pages->have_posts() ) {
				$allPages = $pages->posts;
			} else {
				$allPages = $this->_hard_search_pages($form);
			}

			echo '<ul style="font-size: 1.1em; list-style-type:disc; margin-left: 1.1em;">';
			if ( $allPages ) {
				foreach( $allPages as $page ) {
					printf( 
						'<li><b>%s</b>: <a href="%s" target="_blank">%s</a> <a href="%s" target="_blank">%s</a>', 
						esc_html($page->post_title), 
						get_permalink( $page->ID ), 
						esc_html__( 'View Page', 'advanced-members' ),
						admin_url( 'post.php?post=' . $page->ID . '&action=edit' ),
						esc_html__( 'Edit Page', 'advanced-members' ) 
					);
				}
			} else {
				echo '<li>';
				esc_html_e( 'No connected Pages. You can embed this form into a Page with the Adv. Members Form block or the Shortcode displayed on top of this form settings section.', 'advanced-members' );
				echo '</li>';
			}
			echo '</ul>';

		}

		protected function _hard_search_pages($form) {
			global $wpdb;
			if ( get_post_meta( $form['post_id'], '_hard_search_pages_done', true ) )
				return false;

			if ( $form['type'] == 'account' ) {
				$where = "post_content LIKE '%%[advanced-members-account]%%'";
			} else {
				$where = "( 
					post_content LIKE '%%[advanced-members form=\"{$form['post_id']}\"%%' OR 
					post_content LIKE '%%[advanced-members form=\"{$form['key']}\"%%' 
				)";
			}
			$sql = "SELECT * FROM $wpdb->posts WHERE $where AND post_status NOT LIKE 'trashed' AND post_type LIKE 'page'";

			if ( $pages = $wpdb->get_results($sql) ) {
				foreach( $pages as $page ) {
					$this->save_form_for_page($page->ID, $page);
				}
			}

			update_post_meta( $form['post_id'], '_hard_search_pages_done', true );
		}

		function get_field_group_title($title) {
			global $wpdb;

			$i = 1;
			while( true ) {
				$exists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type LIKE 'acf-field-group' AND post_title LIKE %s LIMIT 1", $title ) );
				if ( $exists ) {
					$title = $title . ' ' . zeroise( $i, 3 );
				} else {
					break;
				}
			}

			return $title;
		}

		/**
		 * Set status, Save default form key and meta data outside ACF
		 *
		 * @since 1.0.0
		 * @param number $post_id
		 * @param object $post
		 */
		function save_post( $post_id, $post ) {
			$do_save = parent::save_post( $post_id, $post );

			if ( true !== $do_save )
				return;

			if( ! get_post_meta( $post->ID, 'form_key', true ) ) {
				$form_key = 'form_' . uniqid();
				update_post_meta( $post->ID, 'form_key', $form_key );
			}

			if ( isset($_POST['amem_field_group_sort']) && is_array($_POST['amem_field_group_sort']) ) {// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Validated in verify_save_post
				$sorted = array_map( 'intval', $_POST['amem_field_group_sort'] );
				update_post_meta( $post_id, 'amem_field_group_sort', $sorted );
			}
		}

		/**
		 * amem_form_shortcode_message 필드에서 보여줄 숏코드 를 반환
		 *
		 * @since 1.0.0
		 * @param   array $field The columns array.
		 * @return array $field
		 */
		function display_form_shortcode( $field ) {
			global $post;
			if ( $post ) {
				$code = sprintf( '[advanced-members form="%s"]', $post->ID );
				$message = '<code><span class="copyable">' . $code . '</span></code>';
				$field['message'] = $message;
			}

			return $field;
		}

		/**
		 * Stops new form keys from being saved to a form post if a key already exists.
		 * Some plugins that duplicate posts will cause trouble as forms will end up with multiple form keys.
		 *
		 * @since 1.0.0
		 * @param bool $check
		 * @param int $object_id
		 * @param string $meta_key
		 * @return bool $check
		 */
		function should_add_form_key_meta( $check, $object_id, $meta_key ) {
			if ( 'form_key' !== $meta_key ) {
				return $check;
			}

			// If a form key already exists, we don't want to save another one
			if ( metadata_exists( 'post', $object_id, $meta_key ) ) {
				return false;
			}
			return $check;
		}

	}

	Form::getInstance();
endif; // Class exists check.

?>

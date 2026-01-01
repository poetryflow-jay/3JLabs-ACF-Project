<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<div class="wrap acf-settings-wrap">

	<h1><?php echo esc_html($page_title); ?></h1>

	<form id="post" method="post" name="post">

		<?php
		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$all_roles = $wp_roles->roles;
		$roles = array();
		foreach ($all_roles as $key => $role) {
			$roles[$key] = translate_user_role( $role['name'] );
		}

		$pages = array(
			0 => __('&mdash; Select a Page &mdash;', 'advanced-members')
		);
		foreach (get_pages(array(
			'sort_column' => 'post_title',
			'sort_order' => 'ASC',
		)) as $page ) {
			$pages[$page->ID] = $page->post_title;
		};

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

		$redirections = [
			'registration' 	=> [
				'label' 				=> __( 'Registration', 'advanced-members' ),
				'choices' 			=> ['redirect_home', 'redirect_url'],
				'action' 				=> _x( 'registered.', 'user_action', 'advanced-members' ),
			],
			'login' 				=> [
				'label' 				=> __( 'Login', 'advanced-members' ),
				'choices' 			=> ['redirect_home', 'redirect_admin', 'refresh', 'redirect_url'],
				'action' 				=> _x( 'logged in.', 'user_action', 'advanced-members' ),
			],
			'logout' 				=> [
				'label' 				=> __( 'Logout', 'advanced-members' ),
				'choices' 			=> ['redirect_home', 'redirect_url'],
				'action' 				=> _x( 'logged out.', 'user_action', 'advanced-members' ),
			],
			'account_delete' => [
				'label' 				=> __( 'Delete Account', 'advanced-members' ),
				'choices' 			=> ['redirect_home', 'redirect_login', 'redirect_register', 'redirect_url'],
				'action' 				=> _x( 'delete account.', 'user_action', 'advanced-members' ),
			],
		];

		$choices = [
			'redirect_home' 		=> __( 'Go to Homepage', 'advanced-members' ),
			'refresh' 					=> __( 'Refresh active page', 'advanced-members' ),
			'redirect_admin' 		=> __( 'Go to Admin page', 'advanced-members' ),
			'redirect_login' 		=> __( 'Go to Login page', 'advanced-members' ),
			'redirect_register' => __( 'Go to Registration page', 'advanced-members' ),
			'redirect_url' 			=> __( 'Redirect to URL', 'advanced-members' ),
		];

		$is_first = true;

		wp_nonce_field( 'amem_options_update', 'option_update_nonce', false );

		?>

		<div id="poststuff" class="poststuff">

			<div id="post-body" class="metabox-holder columns-1">

				<div id="postbox-container-1" class="postbox-container">

					<?php do_meta_boxes( 'amem_options_page', 'side', null ); ?>

				</div>

				<div id="postbox-container-2" class="postbox-container">
					<div id="advanced-members-settings" class="postbox">
						<div class="inside">
					<?php
					foreach ( $tabs as $tab_key => $tab_label ) {
						acf_render_field_wrap(
							array(
								'type'  => 'tab',
								'label' => $tab_label,
								// 'key'   => 'amem_settings_tabs',
								// acf_ui_options_page_tabs does not exists in ACF 6.2.0(acf-input.js)
								'key'		=> 'acf_field_group_settings_tabs',//'acf_ui_options_page_tabs',
								'settings-type' => $tab_key,

							)
						);

						$wrapper_class = str_replace( '_', '-', $tab_key );

						echo '<div class="field-group-settings-tab amem-' . esc_attr( $wrapper_class ) . '-settings">';

						switch ( $tab_key ) {
							case 'general':
								echo '<h3>' . esc_html__( 'Advanced Members Pages', 'advanced-members' ) . '</h3>';
								foreach (amem()->config->get_core_pages() as $page_key => $page_value) {
									$page_id = amem()->options->get_core_page_id($page_key);
									// if ( $page_id && $page_key == 'account' ) {
									// 	$instructions = __('Edit form link for account is \'Default Account Form\' set form Account Forms settings.', 'advanced-members');
									// } else {
										$instructions = null;
									// }
									acf_render_field_wrap(
										array(
											'type'         => 'select',
											'name'         => $page_id,
											'key'          => $page_id,
											'prefix'       => 'amem_options',
											'value'        => amem()->options->get($page_id),
											'label'        => $page_value['label'],
											'choices'      => $pages,
											'hide_search'  => false,
											'instructions' => $instructions,
										),
										'div',
										'field',
										true
									);
								}

								acf_render_field_wrap( array( 'type' => 'seperator' ) );
								echo '<h3>' . esc_html__( 'Styles', 'advanced-members' ) . '</h3>';

								$amem_themes = [
									'' => __( 'No Styles', 'advanced-members' ),
									'default' => __( 'shadcn/ui', 'advanced-members' ),
									'acf' => __( 'ACF style', 'advanced-members' ),
								];

								acf_render_field_wrap(
									array(
										'type'         => 'select',
										'name'         => 'load_theme',
										'key'          => 'load_theme',
										'prefix'       => 'amem_options',
										'value'        => amem()->options->get('load_theme', 'default'),
										'label'        => __( 'Default Styles', 'advanced-members' ),
										'instructions' => __( 'Select default style for Advanced Members forms. You can also disable default style.', 'advanced-members' ),
										'default_value' => 'default',
										'ui'           => 0,
										'choices' => $amem_themes,
									),
									'div',
									'field',
									true
								);

								acf_render_field_wrap( array( 'type' => 'seperator' ) );
								echo '<h3>' . esc_html__( 'Etc.', 'advanced-members' ) . '</h3>';
								acf_render_field_wrap(
									array(
										'type'         => 'true_false',
										'name'         => 'ajax_submit',
										'key'          => 'ajax_submit',
										'prefix'       => 'amem_options',
										'value'        => amem()->options->get('ajax_submit'),
										'label'        => __( 'AJAX Submit', 'advanced-members' ),
										'instructions' => __( 'Enable/disable AJAX form submit instead of page load. This option is overridden by the Form and Shortcode option.', 'advanced-members' ),
										// 'message'			 => __( 'Enable/disable AJAX form submit instead of page load.', 'advanced-members' )
										'default'      => true,
										'default_value' => 0,
										'ui'           => 1,
									),
									'div'
								);

							break;
							case 'account':
								echo '<h3>' . esc_html__( 'Account Form Settings', 'advanced-members' ) . '</h3>';
								// acf_render_field_wrap(
								// 	array(
								// 		'type'         => 'true_false',
								// 		'name'         => 'account_form_showadmin',
								// 		'key'          => 'account_form_showadmin',
								// 		'prefix'       => 'amem_options[accform]',
								// 		'value'        => amem()->options->get('accform/account_form_showadmin'),
								// 		'label'        => __( 'User Profile', 'advanced-members' ),
								// 		'instructions' => __( 'Enable/disable used on the User Profile edit', 'advanced-members' ),
								// 		'default'      => true,
								// 		'ui'           => 1,
								// 	),
								// 	'div'
								// );
								acf_render_field_wrap(
									array(
										'type'         => 'true_false',
										'name'         => 'show_current_passwd',
										'key'          => 'show_current_passwd',
										'prefix'       => 'amem_options[account]',
										'value'        => amem()->options->get('account/show_current_passwd'),
										'label'        => __( 'Current Password on Account', 'advanced-members' ),
										'instructions' => __( 'Show the current password confirmation field on the general account page', 'advanced-members' ),
										'default'      => true,
										'ui'           => 1,
									),
									'div'
								);
								// acf_render_field_wrap(
								// 	array(
								// 		'type'         => 'true_false',
								// 		'name'         => 'use_password',
								// 		'key'          => 'use_password',
								// 		'prefix'       => 'amem_options[account]',
								// 		'value'        => amem()->options->get('account/use_password'),
								// 		'label'        => __( 'Account Password Change', 'advanced-members' ),
								// 		'instructions' => __( 'Enable/disable the Password Change for account. <code>[advanced-members-account tab="password"]</code>', 'advanced-members' ),
								// 		'default'      => true,
								// 		'ui'           => 1,
								// 	),
								// 	'div'
								// );
								// acf_render_field_wrap(
								// 	array(
								// 		'type'         => 'true_false',
								// 		'name'         => 'use_delete',
								// 		'key'          => 'use_delete',
								// 		'prefix'       => 'amem_options[account]',
								// 		'value'        => amem()->options->get('account/use_delete'),
								// 		'label'        => __( 'Account Deletion', 'advanced-members' ),
								// 		'instructions' => __( 'Enable/disable the Delete account for account. <code>[advanced-members-account tab="delete"]</code>', 'advanced-members' ),
								// 		'default'      => true,
								// 		'ui'           => 1,
								// 	),
								// 	'div'
								// );

								acf_render_field_wrap( array( 'type' => 'seperator' ) );
								echo '<h3>' . esc_html__( 'Account Form by User Roles', 'advanced-members' ) . '</h3>';
								acf_render_field_wrap(
									array(
										'type'         => 'select',
										'name'         => 'default',
										'key'          => 'default',
										'prefix'       => 'amem_options[accform]',
										'value'        => amem()->options->get('accform/default'),
										'label'        => __( 'Default Account Form', 'advanced-members' ),
										'choices'      => $account_forms,
										'hide_search'  => false,
									),
									'div',
									'field'
								);
								$view_args = array(
									'ruletab'	=> 'accform',
									'roles'		=> $roles,
									'forms'		=> $account_forms,
									'group' 	=> array(),

								);
								if( !empty( amem()->options->get('accform/rules') ) ) {
									$view_args['group'] = amem()->options->get('accform/rules');
								}
								amem_get_view( __DIR__ . '/html-options-rule-fields.php', $view_args );
								/*
								foreach ($roles as $role_key => $role_label) {
									acf_render_field_wrap(
										array(
											'type'         => 'select',
											'name'         => $role_key,
											'key'          => $role_key,
											'prefix'       => 'amem_options[accform]',
											'value'        => amem()->options->get('accform/role_key'),
											'label'        => $role_label,
											'choices'      => $account_forms,
											'hide_search'  => false,
										),
										'div',
										'field'
									);
								}
								*/
							
							acf_render_field_wrap( array( 'type' => 'seperator' ) );
							echo '<h3>' . esc_html__( 'Delete Account', 'advanced-members' ) . '</h3>';

							/* translators: Delete account explain message */
							$alert_text = __( 'By deleting your account, all of its data will be destroyed. This is not recoverable. %s', 'advanced-members' );
							$explain_process = __( 'To delete your account, click on the button below.', 'advanced-members' );
							$password_required = amem()->get_action('account')->require_password_validation( 'delete' );
							if ( $password_required ) {
								$explain_process = __( 'To delete your account enter your password below.', 'advanced-members' );
							}
							$alert_text = trim( sprintf( $alert_text, $explain_process ) );

					 		$confirm_text = __( 'Account Delete Confirmation', 'advanced-members' );

							acf_render_field_wrap(
								array(
									'type'         => 'textarea',
									'name'         => 'delete_account_text',
									'key'          => 'delete_account_text',
									'prefix'       => 'amem_options[account]',
									'value'        => amem()->options->get('account/delete_account_text'),
									'label'        => __( 'Account Deletion Custom Text', 'advanced-members' ),
									'instructions' => __( 'This is custom text that will be displayed to users before they delete their accounts from your site.', 'advanced-members' ),
									'placeholder'  => $alert_text,
									'conditions' => array(
										'field' => 'use_delete',
										'operator' => '==',
										'value' => '1',
									),
								),
								'div',
								'field'
							);
							acf_render_field_wrap(
								array(
									'type'         => 'text',
									'name'         => 'delete_account_label',
									'key'          => 'delete_account_label',
									'prefix'       => 'amem_options[account]',
									'value'        => amem()->options->get('account/delete_account_label'),
									'label'        => __( 'Account Deletion Confirmation Label', 'advanced-members' ),
									'instructions' => __( 'This is the label that will be displayed to the right of the account deletion agreement checkbox.', 'advanced-members' ),
									'placeholder'  => $confirm_text,
									'conditions' => array(
										'field' => 'use_delete',
										'operator' => '==',
										'value' => '1',
									),
								),
								'div',
								'field'
							);
							break;
							case 'redirects':
							echo '<h3>' . esc_html__( 'Redirection Settings', 'advanced-members' ) . '</h3>';

							foreach( $redirections as $act => $data ) {
								$_choices = array_intersect_key(
							    $choices,
							    array_flip($data['choices']) // keys to be extracted
								);
								/* translators: form action names string */
								$instuction = sprintf( __( 'Set a url to redirect user after they %s', 'advanced-members' ), $data['action'] );
								acf_render_field_wrap(
									[
										'type'         => 'select',
										'name'         => '_after_' . $act,
										'key'          => '_after_' . $act,
										'prefix'       => 'amem_options[redirect]',
										'value'        => amem()->options->get('redirect/_after_' . $act),
										'label'        => $data['label'],
										'choices'      => $_choices,
										'hide_search'  => false,
									],
									'div',
									'field'
								);
								acf_render_field_wrap(
									[
										'type'         => 'text',
										'name'         => $act . '_redirect_url',
										'key'          => $act . '_redirect_url',
										'prefix'       => 'amem_options[redirect]',
										'value'        => amem()->options->get('redirect/' . $act . '_redirect_url'),
										'label'        => __( 'Set Custom Redirect URL', 'advanced-members' ),
										'instructions' => $instuction,
										'conditions' 	 => [
											[
												'field'    => '_after_' . $act,
												'operator' => '==',
												'value'    => 'redirect_url',
											],
										],
									],
									'div',
									'field'
								);
							}
							acf_render_field_wrap( array( 'type' => 'seperator' ) );
							echo '<h3>' . esc_html__( 'User role redirection settings', 'advanced-members' ) . '</h3>';
							acf_render_field_wrap(
								array(
									'type'         => 'true_false',
									'name'         => 'apply_roles_redirection',
									'key'          => 'apply_roles_redirection',
									'prefix'       => 'amem_options[redirect]',
									'value'        => amem()->options->get('redirect/apply_roles_redirection'),
									'label'        => __( 'Enable redirection by role', 'advanced-members' ),
									'instructions' => __( 'Enable/disable redirection by user role', 'advanced-members' ),
									'default'      => true,
									'ui'           => 1,
									'wrapper'			 => [ 'class' => 'amem-field-toggle-group' ],
									'data' 				 => ['toggle-target' => '.role-redirection-table'],
								),
								'div'
							);
							$view_args = array(
								'all_roles' 					=> $all_roles,
								'redirections'				=> $redirections,
								'choices'							=> $choices,
								'table_wrap_class'		=> amem()->options->get('redirect/apply_roles_redirection', true) ? '' : ' acf-hidden'
							);
							amem_get_view( __DIR__ . '/html-options-roles-redirection.php', $view_args );

							break;
							case 'email':
								// $email_key = empty( $_GET['email'] ) ? '' : sanitize_key( $_GET['email'] );
								$email_notifications = amem()->config->email_notifications;
								if( $email_notifications ){
									$view_args = array(
										'email_notifications' => $email_notifications,
										'email_options'				=> amem()->options->get_emails(),
									);
									amem_get_view( __DIR__ . '/html-options-list-email.php', $view_args );
								}
							break;
							case 'adminbar':
							// acf_render_field_wrap(
							// 	array(
							// 		'type'         => 'true_false',
							// 		'name'         => 'global',
							// 		'key'          => 'global',
							// 		'prefix'       => 'amem_options[adminbar]',
							// 		'value'        => amem()->options->get('adminbar/global'),
							// 		'label'        => __( 'Disable Admin Bar', 'advanced-members' ),
							// 		'instructions' => __( 'Admin bar on frontend is disabled when this option is on', 'advanced-members' ),
							// 		'default'      => true,
							// 		'ui'           => 1,
							// 	),
							// 	'div'
							// );
							// acf_render_field_wrap(
							// 	array(
							// 		'type'         => 'true_false',
							// 		'name'         => 'by_roles',
							// 		'key'          => 'by_roles',
							// 		'prefix'       => 'amem_options[adminbar]',
							// 		'value'        => amem()->options->get('adminbar/by_roles'),
							// 		'label'        => __( 'Enable admin bar visibility by role', 'advanced-members' ),
							// 		'instructions' => __( 'Enable/disable show/hide admin bar by role', 'advanced-members' ),
							// 		'default'      => true,
							// 		'ui'           => 1,
							// 		'wrapper'			 => [ 'class' => 'amem-field-toggle-group' ],
							// 		'data' 				 => ['toggle-target' => '.amem-settings-role-adminbar'],
							// 	),
							// 	'div'
							// );

							$choices = [
								'' => __( 'Use global rule', 'advanced-members' ),
								'show' => __( 'Show', 'advanced-members' ),
								'hide' => __( 'Hide', 'advanced-members' ),
							];

							echo '<h3>' . esc_html__( 'Select the roles to disable the admin bar for', 'advanced-members' ) . '</h3>';
							echo '<div class="amem-settings-role-adminbar">' . PHP_EOL;
							foreach ($all_roles as $key => $role) {
								acf_render_field_wrap(
									[
										'type'         => 'true_false',
										'name'         => $key,
										'key'          => $key,
										'prefix'       => 'amem_options[adminbar][roles]',
										'value'        => amem()->options->get('adminbar/roles/'.$key),
										'label' 			 => $role['name'],
										// 'choices'      => $choices,
										'instructions' => null,
										'default_value' => 0,
										// 'ui' => 1,
									],
									'div',
									'field'
								);
							}
							echo '</div>';
							break;
							case 'avatar':

							$choices = [
								'' => __( 'Use global rule', 'advanced-members' ),
								'show' => __( 'Show', 'advanced-members' ),
								'hide' => __( 'Hide', 'advanced-members' ),
							];

							echo '<h3>' . esc_html__( 'Avatar Settings', 'advanced-members' ) . '</h3>';
							echo '<div class="amem-settings-avatar">' . PHP_EOL;
							// acf_render_field_wrap(
							// 	[
							// 		'type'         => 'true_false',
							// 		'name'         => 'rest_api_compat',
							// 		'key'          => 'rest_api_compat',
							// 		'prefix'       => 'amem_options[avatar]',
							// 		'value'        => amem()->options->get('avatar/rest_api_compat'),
							// 		'label' 			 => __( 'REST API compatibility mode', 'advanced-members' ),
							// 		'instructions' => __( 'When you enable the REST API compatibility mode, cropping in the WordPress administration interface will use admin-ajax.php instead of the REST API. Use this compatibility mode if you do not have REST API enabled. Please note that this is a temporary fix since the REST API is the way forward. The compatibility mode will be removed in a future major release of the plugin.', 'advanced-members' ),
							// 		'default_value' => 0,
							// 		'ui' => 1,
							// 	],
							// 	'div',
							// 	'label'
							// );

							$avatar_sizes = amem()->options->get('avatar/avatar_sizes') ? amem()->options->get('avatar/avatar_sizes') : '96,150,300';
							acf_render_field_wrap(
								[
									'type'         => 'text',
									'name'         => 'avatar_sizes',
									'key'          => 'avatar_sizes',
									'prefix'       => 'amem_options[avatar]',
									'value'        => $avatar_sizes,
									'label' 			 => __( 'Avatar Sizes', 'advanced-members' ),
									'instructions' => __( 'Comma-separated list of avatar sizes (numbers). Sizes should be between 80-512.', 'advanced-members' ),
									'default_value' => '96,150,300',
								],
								'div',
								'field'
							);

							acf_render_field_wrap(
								[
									'type'         => 'true_false',
									'name'         => 'set_default_avatar',
									'key'          => 'set_default_avatar',
									'prefix'       => 'amem_options[avatar]',
									'value'        => amem()->options->get('avatar/set_default_avatar'),
									'label' 			 => __( 'Set Default Avatar', 'advanced-members' ),
									'instructions' => __( 'Set default avatar for this site globally. This will replace gravatar defaults.', 'advanced-members' ),
									'default_value' => 0,
									'ui' => 1,
								],
								'div',
								'label'
							);

							acf_render_field_wrap(
								[
									'type'         => 'amem_avatar',
									'name'         => 'default_avatar',
									'key'          => 'default_avatar',
									'prefix'       => 'amem_options[avatar]',
									'value'        => amem()->options->get('avatar/default_avatar'),
									'label' 			 => __( 'Default Avatar Image', 'advanced-members' ),
									'default_value' => 0,
									'ui' => 1,
									'conditions' => array(
										'field' => 'set_default_avatar',
										'operator' => '==',
										'value' => '1',
									),
								],
								'div',
								'label'
							);

							echo '</div>';

							break;

							case 'restriction':

							$choices = [
								'' => __( 'Use global rule', 'advanced-members' ),
								'show' => __( 'Show', 'advanced-members' ),
								'hide' => __( 'Hide', 'advanced-members' ),
							];

							$post_types = get_post_types( ['public' => true], 'object' );
							foreach( $post_types as $k => $obj ) {
								$post_types[$k] = $obj->label;
							}

							$taxonomies = get_taxonomies( ['public' => true], 'object' );
							foreach( $taxonomies as $k => $obj ) {
								$taxonomies[$k] = $obj->label;
							}

							echo '<div class="amem-settings-restriction">' . PHP_EOL;

							echo '<h3>' . esc_html__( 'Enable the "Content Restriction" settings for post types', 'advanced-members' ) . '</h3>';

							acf_render_field_wrap(
								[
									'type'         => 'checkbox',
									'name'         => 'post_types',
									'key'          => 'post_types',
									'prefix'       => 'amem_options[restriction]',
									'value'        => amem()->options->get('restriction/post_types'),
									// 'label' 			 => __( 'Enable the "Content Restriction" settings for post types', 'advanced-members' ),
									'instructions' => __( 'Select post types to control content restriction.', 'advanced-members' ),
									'choices' 		 => $post_types,
									'default' 		 => true,
									'default_value' => ['page'],
									'multiple' => 1,
									'layout' => 'vertical',
								],
								'div',
								'field'
							);

							acf_render_field_wrap( array( 'type' => 'seperator' ) );
							echo '<h3>' . esc_html__( 'Enable the "Content Restriction" by Taxonomies', 'advanced-members' ) . '</h3>';

							acf_render_field_wrap(
								array(
									'type' => 'message',
									'label' => __('About Term Rule', 'advanced-members' ),
									'message' => __( '&#8251; The term access rule will be applied to posts connected to the term, not to the term itself.', 'advanced-members' ) . '<br>' . __( '&#8251; This means you can apply rules to all posts connected to this term at once, rather than individually.' , 'advanced-members' ),
								), 
								'div',
								'field'
							);
							acf_render_field_wrap(
								[
									'type'         => 'checkbox',
									'name'         => 'taxonomies',
									'key'          => 'taxonomies',
									'prefix'       => 'amem_options[restriction]',
									'value'        => amem()->options->get('restriction/taxonomies'),
									'instructions' => __( 'Select taxonomies to control content restriction.', 'advanced-members' ),
									'choices' 		 => $taxonomies,
									'default' 		 => true,
									'default_value' => ['page'],
									'multiple' => 1,
									'layout' => 'vertical',
								],
								'div',
								'field'
							);

							acf_render_field_wrap( array( 'type' => 'seperator' ) );
							echo '<h3>' . esc_html__( 'Content Restriction Methods', 'advanced-members' ) . '</h3>';

							acf_render_field_wrap(
								array(
									'type'         => 'true_false',
									'name'         => 'redirect_login',
									'key'          => 'redirect_login',
									'prefix'       => 'amem_options[restriction][methods]',
									'value'        => 1, //amem()->options->get('restriction/methods/redirect_login'),
									'label'        => __( 'Redirect to the Login Page', 'advanced-members' ),
									'readonly' 		 => 1,
									'default'      => true,
									'default_value' => 1,
									'ui'           => 1,
									'wrapper' 		 => [
										'class' => 'amem-readonly',
									],
								),
								'div'
							);

							acf_render_field_wrap(
								array(
									'type'         => 'true_false',
									'name'         => 'redirect_custom',
									'key'          => 'redirect_custom',
									'prefix'       => 'amem_options[restriction][methods]',
									'value'        => amem()->options->get('restriction/methods/redirect_custom'),
									'label'        => __( 'Redirect to custom URL', 'advanced-members' ),
									'default'      => true,
									'default_value' => 0,
									'ui'           => 1,
								),
								'div'
							);

							acf_render_field_wrap(
								array(
									'type'         => 'true_false',
									'name'         => 'show_message',
									'key'          => 'show_message',
									'prefix'       => 'amem_options[restriction][methods]',
									'value'        => amem()->options->get('restriction/methods/show_message'),
									'label'        => __( 'Show Restriction Message', 'advanced-members' ),
									'default'      => true,
									'default_value' => 0,
									'ui'           => 1,
								),
								'div'
							);

							acf_render_field_wrap(
								array(
									'type'         => 'true_false',
									'name'         => 'show_excerpt_message',
									'key'          => 'show_excerpt_message',
									'prefix'       => 'amem_options[restriction][methods]',
									'value'        => amem()->options->get('restriction/methods/show_excerpt_message'),
									'label'        => __( 'Show the Excerpt and Restriction Message', 'advanced-members' ),
									'default'      => true,
									'default_value' => 0,
									'ui'           => 1,
								),
								'div'
							);

							acf_render_field_wrap(
								array(
									'type' => 'wysiwyg',
									'name' => 'message',
									'key' => 'message',
									'prefix' => 'amem_options[restriction]',
									'label' => __( 'Default Restriction Message', 'advanced-members' ),
									'value' => amem()->options->get('restriction/message'),
									'default'      => true,
									'default_value' => __( 'We\'re sorry, but you don\'t currently have access to this content.', 'advanced-members' ),
									'placeholder' => __( 'We\'re sorry, but you don\'t currently have access to this content.', 'advanced-members' ),
								),
								'div',
								'field'
							);

							echo '</div>';
							break;

							case 'recaptcha':

							echo '<div class="amem-settings-recaptcha">' . PHP_EOL;

							$settings = amem()->recaptcha->get_settings(true);

							echo '<h3>' . esc_html__( 'API Key Settings', 'advanced-members' ) . '</h3>';

							acf_render_field_wrap(
								[
									'label'         => __('Site Key', 'advanced-members'),
								  /* translators: %s: reCAPTCHA console URL */
								  'instructions'  => sprintf( __('Enter the site key. <a href="%s" target="_blank">reCAPTCHA API Admin</a>', 'advanced-members'), 'https://www.google.com/recaptcha/admin' ),
								  'type'          => 'text',
								  'name'          => 'site_key',
									'key' => 'site_key',
									'prefix'       => 'amem_options[recaptcha]',
									'value'        => $settings['site_key'],
									'default' 		 => true,
									'default_value' => '',
									'wrapper' => [
										'id' => 'amem-recaptcha-site-key',
									],
								],
								'div',
								'field'
							);

							acf_render_field_wrap(
								[
									'label'         => __('Secret Key', 'advanced-members'),
								  /* translators: %s: reCAPTCHA console URL */
								  'instructions'  => sprintf( __('Enter the secret key. <a href="%s" target="_blank">reCAPTCHA API Admin</a>', 'advanced-members'), 'https://www.google.com/recaptcha/admin' ),
								  'type'          => 'text',
								  'name'          => 'secret_key',
									'key' => 'secret_key',
									'prefix'       => 'amem_options[recaptcha]',
									'value'        => $settings['secret_key'],
									'default' 		 => true,
									'default_value' => '',
									'wrapper' => [
										'id' => 'amem-recaptcha-secret-key',
									],
								],
								'div',
								'field'
							);

							echo '<div id="amem-recaptcha-key-verified" class="acf-field acf-field-hidden acf-field-key-verified" data-name="key_verified" data-type="text" data-key="key_verified">
								<div class="acf-input">';

							if ( !$settings['key_verified'] ) {
								echo '<div class="acf-notice -error acf-error-message"><p>' . __( 'reCAPTCHA will not function properly until validated Site Key and Secret Key are configured.', 'advanced-members' ) . '</p></div>';
							}

							echo '<div class="acf-input-wrap">';
							printf( '<input type="hidden" name="amem_options[recaptcha][key_verified]" value="%d" id="amem-recpatcha-key_verified" />', $settings['key_verified'] );
							echo '</div>
							</div>
							</div>';

							if ( !$settings['key_verified'] && ($settings['site_key'] || $settings['secret_key']) ) {
								acf_render_field_wrap(
									[
										'label' => __( 'Please review the following before using reCAPTCHA on your site:', 'advanced-members'),
										'message'  => '<ul style="list-style-type:disc;margin-left:1.3em;">
										<li>' . __( 'Make sure you\'ve entered valid API keys', 'advanced-members' ) . '</li>
										<li>' . /* translators: %s: reCAPTCHA admin URL */sprintf( __( 'Is your site domain added in the <a href="%s" target="_blank">reCAPTCHA API Admin</a> settings?', 'advanced-members' ), 'https://www.google.com/recaptcha/admin' ) . '</li>
										<li>' . __( 'Have you selected the correct type of v2 reCAPTCHA (Invisible or Checkbox)?', 'advanced-members' ) . '</li>
										</ul>',
										'type'          => 'message',
										// 'conditional_logic' => array(
										//   array(
										//     array(
										//       'field'     => 'version',
										//       'operator'  => '==',
										//       'value'     => 'v2',
										//     )
										//   )
										// ),
									],
									'div',
									'field'
								);
							}

							acf_render_field_wrap( array( 'type' => 'seperator' ) );
							echo '<h3>' . esc_html__( 'Global settings for Google reCAPTCHA', 'advanced-members' ) . '</h3>';

							acf_render_field_wrap(
								[
									'type'         => 'select',
									'name'         => 'version',
									'key'          => 'version',
									'prefix'       => 'amem_options[recaptcha]',
									'value'        => $settings['version'],
									'label' 			 => __( 'reCAPTCHA Version', 'advanced-members' ),
									/* translators: %s: Google document URL */
									'instructions'  => sprintf( __('Select the reCAPTCHA version. You can find details of verions form <a href="%s" target="_blank">Google Guide</a>.', 'advanced-members'), 'https://developers.google.com/recaptcha/docs/versions' ),
									'choices'       => [
									  'v3' => __('reCAPTCHA V3', 'advanced-members'),
									  'v2' => __('reCAPTCHA V2', 'advanced-members'),
									],
									'default' 		 => true,
									'default_value' => 'v3',
									'wrapper' => [
										'id' => 'amem-recaptcha-version',
									],
								],
								'div',
								'field'
							);

							acf_render_field_wrap(
								[
									'label'         => __('Type', 'advanced-members'),
									/* translators: %s: Google document URL */
									'instructions'  => sprintf( __('Select the reCAPTCHA type for v2. Checkbox: "I\'m not a robot", Invisible: Invisible reCAPTCHA badge. See <a href="%s">Google Guide</a> for more details.', 'advanced-members'), 'https://developers.google.com/recaptcha/docs/versions' ),
									'type'          => 'select',
									'name'          => 'v2_type',
									'key' => 'v2_type',
									'prefix'       => 'amem_options[recaptcha]',
									'value'        => $settings['v2_type'],
									'choices'       => array(
									  'checkbox'  => __('Checkbox', 'advanced-members'),
									  'invisible' => __('Invisible', 'advanced-members'),
									),
									'conditional_logic' => array(
									  array(
									    array(
									      'field'     => 'version',
									      'operator'  => '==',
									      'value'     => 'v2',
									    )
									  )
									),
									'default' 		 => true,
									'default_value' => 'checkbox',
									'wrapper' => [
										'id' => 'amem-recaptcha-type',
									],
								],
								'div',
								'field'
							);

							acf_render_field_wrap(
								[
									'label'         => __('Theme', 'advanced-members'),
									'instructions'  => __('Select the reCAPTCHA theme for v2', 'advanced-members'),
									'type'          => 'select',
									'name'          => 'theme',
									'key' => 'theme',
									'prefix'       => 'amem_options[recaptcha]',
									'value'        => $settings['theme'],
									'choices'       => array(
									  'light' => __('Light', 'advanced-members'),
									  'dark'  => __('Dark', 'advanced-members'),
									),
									'conditional_logic' => array(
									  array(
									    array(
									      'field'     => 'version',
									      'operator'  => '==',
									      'value'     => 'v2',
									    )
									  )
									),
									'default' 		 => true,
									'default_value' => 'light',
									'wrapper' => [
										'id' => 'amem-recaptcha-theme',
									],
								],
								'div',
								'field'
							);

							acf_render_field_wrap(
								[
									'label'         => __('Size', 'advanced-members'),
								  'instructions'  => __('Select the reCAPTCHA size of v2', 'advanced-members'),
								  'type'          => 'select',
								  'name'          => 'size',
								  'key' => 'size',
								  'prefix'       => 'amem_options[recaptcha]',
									'value'        => $settings['size'],
								  'choices'       => array(
								    'normal'        => __('Normal', 'advanced-members'),
								    'compact'       => __('Compact', 'advanced-members'),
								  ),
								  'conditional_logic' => array(
								    array(
								      array(
								        'field'     => 'version',
								        'operator'  => '==',
								        'value'     => 'v2',
								      ),
								      array(
								        'field'     => 'v2_type',
								        'operator'  => '==',
								        'value'     => 'checkbox',
								      )
								    )
								  ),
									'default' 		 => true,
									'default_value' => 'normal',
									'wrapper' => [
										'id' => 'amem-recaptcha-size',
									],
								],
								'div',
								'field'
							);

							acf_render_field_wrap(
								[
									'label'             => __('Hide Badge', 'advanced-members'),
									/* translators: %s: Google documnet URL */
									'instructions'      => sprintf( __('Hide the <a href="%s" target="_blank">reCAPTCHA v3 badge</a>', 'advanced-members'), 'https://developers.google.com/recaptcha/docs/faq#id-like-to-hide-the-recaptcha-badge.-what-is-allowed' ),
									'type'              => 'true_false',
									'name'              => 'hide_badge',
									'key' => 'hide_badge',
									'prefix'       => 'amem_options[recaptcha]',
									'value'        => $settings['hide_badge'],
									'ui'                => true,
									'conditional_logic' => array(
									  array(
									    array(
									      'field'     => 'version',
									      'operator'  => '==',
									      'value'     => 'v3',
									    )
									  )
									),
									'default' 		 => true,
									'default_value' => false,
									'wrapper' => [
										'id' => 'amem-recaptcha-hide-badge',
									],
								],
								'div',
								'label'
							);

							acf_render_field_wrap( [
							  'label'         => __('Score Threshold', 'advanced-members'),
							  /* translators: %s: Google document URL */
							  'instructions'  => sprintf( __('Select score threshold to verify. 0.0 mens very likely a bot and 1.0 very likely a human. Google\'s default value is 0.5. Check <a href="%s" target="_blank">Google guide</a>', 'advanced-members'), 'https://developers.google.com/recaptcha/docs/v3#interpreting_the_score' ),
							  'type'          => 'select',
							  'name'          => 'score',
							  'key' => 'score',
								'prefix'       => 'amem_options[recaptcha]',
								'value'        => $settings['score'],
							  'choices' => [
							  	'0.1' => '0.1',
							  	'0.2' => '0.2',
							  	'0.3' => '0.3',
							  	'0.4' => '0.4',
							  	'0.5' => '0.5',
							  	'0.6' => '0.6',
							  	'0.7' => '0.7',
							  	'0.8' => '0.8',
							  	'0.9' => '0.9',
							  ],
							  'default' => true,
							  'default_value' => '0.5',
							  'conditional_logic' => array(
							    array(
							      array(
							        'field'     => 'version',
							        'operator'  => '==',
							        'value'     => 'v3',
							      )
							    )
							  ),
							  'wrapper' => [
							  	'id' => 'amem-recaptcha-score',
							  ],
							],
							'div',
							'field'
						);

							// acf_render_field_wrap(
							// 	[
							// 		'label'         => __('Global Apply', 'advanced-members'),
							// 	  'instructions'  => __('Apply reCAPTCHA to all available forms.', 'advanced-members'),
							// 	  'type'          => 'true_false',
							// 	  'name'          => 'apply_global',
							// 		'key' 					=> 'apply_global',
							// 		'prefix'       => 'amem_options[recaptcha]',
							// 		'value'        => $settings['apply_global'],
							// 		'ui' 						=> 1,
							// 		'default' 		 => true,
							// 		'default_value' => false,
							// 	],
							// 	'div',
							// 	'label'
							// );


							acf_render_field_wrap(
								[
									'label'         => __('Built-in Forms', 'advanced-members'),
								  'instructions'  => __('Apply reCAPTCHA to all built-in forms like Change Password, Reset Password, Delete Account.', 'advanced-members'),
								  'type'          => 'true_false',
								  'name'          => 'apply_local_forms',
									'key' 					=> 'apply_local_forms',
									'prefix'       => 'amem_options[recaptcha]',
									'value'        => $settings['apply_local_forms'],
									'ui' 						=> 1,
									'default' 		 => true,
									'default_value' => true,
								],
								'div',
								'label'
							);

							echo '</div>';
							break;
						}

						do_action( "amem/settings/render_settings_tab/{$tab_key}" );
						echo '</div>';
					}

					?>
						</div>
					</div>
				</div>
			</div>

			<br class="clear">

		</div>

	</form>

</div>

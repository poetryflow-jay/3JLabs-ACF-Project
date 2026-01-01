<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<div class="wrap acf-settings-wrap acf-updates">

	<h1><?php echo esc_html($page_title); ?></h1>

	<form id="post" method="post" name="post" class="amem-dashboard">
		<?php wp_nonce_field( 'amem_dashboard_update', 'dashboard_update_nonce', false ); ?>
		<div class="amem-dashboard-flex left">
			<div class="acf-box" id="amem-modules-settings">
				<div class="inside">
					<div class="acf-tab-dashboard-wrap -top">
						<ul class="acf-hl acf-tab-dashboard-group">
							<?php
							$class = ' class="active"';
							foreach ( $tabs as $tab_key => $tab ) {
								echo sprintf('<li%2$s><a href="%3$s" class="acf-tab-custom" data-placement="top" data-endpoint="0" data-key="acf_field_group_settings_tabs">%1$s</a></li>', esc_html($tab['label']), $class, esc_url($tab['link']));
								$class = '';
							}
							?>
						</ul>
					</div>
					<?php
					foreach ( $tabs as $tab_key => $tab_label ) {
						$wrapper_class = str_replace( '_', '-', $tab_key );
						echo '<div class="field-group-settings-tab amem-' . esc_attr( $wrapper_class ) . '-settings">';
						switch ( $tab_key ) {
							case 'modules' :
								acf_render_field_wrap(
									array(
										'type'         => 'true_false',
										'name'         => '_use_redirects',
										'key'          => '_use_redirects',
										'prefix'       => 'amem_modules',
										'value'        => amem()->options->getmodule('_use_redirects'),
										'label'        => __( 'Redirects', 'advanced-members' ),
										'instructions' => __( 'Redirect users to different pages or URLs after they register, log in and log out based on user roles.', 'advanced-members' ),
										'default'      => true,
										'default_value' => true,
										'ui'           => 1,
									),
									'div'
								);
								global $wp_version;
								if ( version_compare( $wp_version, '5.4.0', '>=' ) ) {
									acf_render_field_wrap(
										array(
											'type'         => 'true_false',
											'name'         => '_use_menu',
											'key'          => '_use_menu',
											'prefix'       => 'amem_modules',
											'value'        => amem()->options->getmodule('_use_menu'),
											'label'        => __( 'Menu Item Visibility', 'advanced-members' ),
											'instructions' => __( 'Enable/disable menu visibility settings on the navigation menu screen. You can show or hide the menu by the user\'s login status and role.', 'advanced-members' ),
											'default'      => true,
											'default_value' => true,
											'ui'           => 1,
										),
										'div'
									);
								} else {
									acf_render_field_wrap(
										array(
											'type'         => 'message',
											'label'        => __( 'Menu Visibility', 'advanced-members' ),
											'message' => __( 'Menu Visibility feature is supported on WP 5.4.0 or later.', 'advanced-members' ),
										),
										'div'
									);
								}

								acf_render_field_wrap(
									array(
										'type'         => 'true_false',
										'name'         => '_use_restriction',
										'key'          => '_use_restriction',
										'prefix'       => 'amem_modules',
										'value'        => amem()->options->getmodule('_use_restriction'),
										'label'        => __( 'Content Restriction', 'advanced-members' ),
										'instructions' => __( 'Control content access based on login status and User Roles.', 'advanced-members' ),
										'default'      => true,
										'default_value' => 0,
										'ui'           => 1,
									),
									'div'
								);

								acf_render_field_wrap(
									array(
										'type'         => 'true_false',
										'name'         => '_use_adminbar',
										'key'          => '_use_adminbar',
										'prefix'       => 'amem_modules',
										'value'        => amem()->options->getmodule('_use_adminbar'),
										'label'        => __( 'Disable Admin Bar', 'advanced-members' ),
										'instructions' => __( 'Disable the admin bar based on user roles.', 'advanced-members' ),
										'default'      => true,
										'default_value' => true,
										'ui'           => 1,
									),
									'div'
								);

								acf_render_field_wrap(
									array(
										'type'         => 'true_false',
										'name'         => '_use_avatar',
										'key'          => '_use_avatar',
										'prefix'       => 'amem_modules',
										'value'        => amem()->options->getmodule('_use_avatar'),
										'label'        => __( 'Local Avatar', 'advanced-members' ),
										'instructions' => __( 'Allow users to upload a local avatar.', 'advanced-members' ),
										'default'      => false,
										'default_value' => false,
										'ui'           => 1,
									),
									'div'
								);

								acf_render_field_wrap(
									array(
										'type'         => 'true_false',
										'name'         => '_use_recaptcha',
										'key'          => '_use_recaptcha',
										'prefix'       => 'amem_modules',
										'value'        => amem()->options->getmodule('_use_recaptcha'),
										'label'        => __( 'Google reCAPTCHA', 'advanced-members' ),
										'instructions' => __( 'Check form submission with Google reCAPTCHA.', 'advanced-members' ),
										'default'      => false,
										'default_value' => false,
										'ui'           => 1,
									),
									'div'
								);

							break;
						}

						do_action( 'amem/admin/html_dashboard_page/tab=' . $tab_key );
						echo '</div>';
					}
					?>
				</div>
			</div>
		</div>
		<div class="amem-dashboard-flex right">
			<?php /* ?><div class="acf-box" id="amem-dashboard-update">
				<div class="title">
					<h3><?php esc_attr_e( 'Update Dashboard', 'advanced-members' ) ?></h3>
				</div>
				<?php
				do_action( 'acf/options_page/submitbox_before_major_actions' );
				?>
				<div class="dashboard-update-actions">

					<div id="publishing-action">
						<span class="spinner"></span>
						<input type="submit" accesskey="p" value="<?php esc_attr_e('Save Changes', 'advanced-members')?>" class="button button-primary button-large" id="publish" name="publish">
					</div>
				<?php
				do_action( 'acf/options_page/submitbox_major_actions' );
				?>
					<div class="clear"></div>
				</div>
			</div><?php */ ?>
			<div class="acf-box" id="amem-dashboard-documentation">
				<div class="title">
					<h3><?php esc_attr_e( 'Documentation', 'advanced-members' ) ?></h3>
				</div>
				<div class="inner">
					<div class="document_text">
						<p><?php _e('Need Help?
						We have a knowledge
						base full of articles to get
						you started.</p>
						<a target="_blank" href="https://advanced-members.com/doc/getting-started/">Browse Documentation</a>', 'advanced-members')?>
					</div>
				</div>
			</div>
		</div>
	</form>
	<div class="clear"></div>
</div>

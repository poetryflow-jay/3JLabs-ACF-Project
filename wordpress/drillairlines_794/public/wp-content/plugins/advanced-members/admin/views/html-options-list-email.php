<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<div class="advanced-members-field">
	<?php
	acf_render_field_wrap(
		array(
			'type'         => 'text',
			'name'         => 'admin_email',
			'key'          => 'admin_email',
			'prefix'       => 'amem_options[email]',
			'value'        => isset($email_options['admin_email'])? $email_options['admin_email'] : get_option('admin_email'),
			'label'        => __( 'Admin E-mail Address', 'advanced-members' ),
			'instructions' => __( "Set which email addresses admin notifications are sent to", 'advanced-members' ),
		),
		'div',
		'field'
	);
	acf_render_field_wrap(
		array(
			'type'         => 'text',
			'name'         => 'mail_from',
			'key'          => 'mail_from',
			'prefix'       => 'amem_options[email]',
			'value'        => isset($email_options['mail_from'])? $email_options['mail_from'] : get_bloginfo('name'),
			'label'        => __( 'Mail appears from', 'advanced-members' ),
			'instructions' => __( "Set the name that appears in the from text. By default, this will be the name of your WordPress site.", 'advanced-members' ),
		),
		'div',
		'field'
	);
	acf_render_field_wrap(
		array(
			'type'         => 'text',
			'name'         => 'mail_from_addr',
			'key'          => 'mail_from_addr',
			'prefix'       => 'amem_options[email]',
			'value'        => isset($email_options['mail_from_addr'])? $email_options['mail_from_addr'] : get_option('admin_email'),
			'label'        => __( 'Mail appears from address', 'advanced-members' ),
			'instructions' => __( "Set the email address from which the email notifications appear. By default, this will be the email from your site's admin", 'advanced-members' ),
		),
		'div',
		'field'
	);
	acf_render_field_wrap(
		array(
			'type'         => 'number',
			'name'         => 'activation_link_expiry_time',
			'key'          => 'activation_link_expiry_time',
			'prefix'       => 'amem_options',
			'value'        => amem()->options->get('activation_link_expiry_time', 0),
			'label'        => __( 'Email Activation Link Expiry', 'advanced-members' ),
			'instructions' => __( 'Set the email activation link expiry time limit in days.', 'advanced-members' ),
			'default_value' => '1',
			'ui'           => 1,
		),
		'div',
		'field',
	);
	acf_render_field_wrap(
		array(
			'type'         => 'true_false',
			'name'         => 'override_pass_changed_email',
			'key'          => 'override_pass_changed_email',
			'prefix'       => 'amem_options',
			'value'        => amem()->options->get('override_pass_changed_email', 0),
			'label'        => __( 'Override Core Password Changed Email', 'advanced-members' ),
			'instructions' => __( 'Override the WordPress core password changed email content template with Advanced Members.', 'advanced-members' ),
			'default_value' => 0,
			'ui'           => 1,
		),
		'div'
	);
	?>

	<div class="acf-setting-list-wrap email-table">
		<ul class="acf-hl acf-thead">
			<li class="li-field-order"></li>
			<li class="li-field-email"><?php esc_html_e( 'Email', 'advanced-members' ); ?></li>
			<li class="li-field-recipient"><?php esc_html_e( 'Recipient(s)', 'advanced-members' ); ?></li>
			<li class="li-field-email-status"><?php esc_html_e( 'Status', 'advanced-members' ); ?></li>
		</ul>
		<div class="acf-field-list">
		<?php
			foreach ($email_notifications as $key => $notification) {
				foreach ($email_options[$key] as $okey => $value) {
					$notification[$okey] = wp_kses_post($value);
				}
				if($notification['recipient'] == 'admin'){
					$notification['view_recipient'] = $email_options['admin_email'];
				}else{
					$notification['view_recipient'] = __( 'Member', 'advanced-members' );
				}
				amem_get_view(
					__DIR__ . '/html-option-single-email.php',
					array(
						'key'								=> $key,
						'notification'			=> $notification,
					)
				);
			}
		?>
		</div>
	</div>
</div>

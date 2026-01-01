<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

$prefix = sprintf('amem_options[email][%s]', $notification['key']);
$default_active = isset($notification['default_active'])? $notification['default_active'] : false;
$force_active = isset($notification['force_active']) && $notification['force_active'];
if ( !$force_active )
	$is_email_active = isset($notification['is_active'])? $notification['is_active'] : $default_active;
else
	$is_email_active = 1;
$email_status_class =  $is_email_active? 'amem-email-is-active' : 'amem-email-is-disable';
?>
<div class="email-notification-settings" data-emailkey="<?php echo esc_attr($notification['key']) ?>">
	<div class="handle">
		<ul class="acf-hl acf-tbody">
			<li class="li-field-order"></li>
			<li class="li-field-email" data-colname="Email"><strong><?php echo esc_html($notification['title']) ?></strong></li>
			<li class="li-field-recipient" data-colname="Recipient(s)"><?php echo isset($notification['view_recipient']) ? esc_html($notification['view_recipient']) : ''; ?></li>
			<li class="li-field-email-status" data-colname="Status"><span class="dashicons amem-email-status <?php echo esc_attr($email_status_class) ?>"></span></li>
		</ul>
	</div>
	<div class="settings">
		<div class="acf-field-editor">
			<div class="acf-field-settings-main">
				<?php

				acf_render_field_wrap(
					array(
						'type'         => 'true_false',
						'name'         => 'is_active',
						'key'          => 'is_active',
						'prefix'       => $prefix,
						'value'        => $is_email_active,
						'label'        => __( 'Active', 'advanced-members' ),
						'instructions' => $notification['description'],
						'default'      => true,
						'ui'           => 1,//($force_active ? 0 : 1),
						'wrapper' => [
							'class' 			 => ($force_active ? 'amem-email-active amem-readonly' : 'amem-email-active'),
						],
					),
					'div'
				);
				acf_render_field_wrap(
					array(
						'type'         => 'text',
						'name'         => 'subject',
						'key'          => 'subject',
						'prefix'       => $prefix,
						'value'        => $notification['subject'],
						'label'        => __( 'Subject Line', 'advanced-members' ),
						'instructions' => __( "This is the subject line of the e-mail", 'advanced-members' ),
						// 'conditions'   				=> array(
						// 	'field'    => 'is_active',
						// 	'operator' => '==',
						// 	'value'    => 1,
						// ),
					),
					'div',
					'field'
				);
				acf_render_field_wrap(
					array(
						'type'         => 'wysiwyg',
						'media_upload'	=> false,
						'name'         => 'body',
						'key'          => 'body',
						'prefix'       => $prefix,
						'value'        => format_to_edit($notification['body'], true),
						'label'        => __( 'Message Body', 'advanced-members' ),
						'instructions' => __( "This is the content of the e-mail", 'advanced-members' ),
						// 'conditions'   				=> array(
						// 	'field'    => 'is_active',
						// 	'operator' => '==',
						// 	'value'    => 1,
						// ),
					),
					'div',
					'field'
				);
				?>
			</div>
		</div>
	</div>
</div>

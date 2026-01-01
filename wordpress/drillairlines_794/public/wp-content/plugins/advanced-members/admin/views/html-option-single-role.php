<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

$prefix = sprintf('amem_options[redirect][roles][%s]', $key);
?>
<div class="role-redirection-settings" data-rolekey="<?php echo esc_attr($key) ?>">
	<div class="handle">
		<ul class="acf-hl acf-tbody">
			<li class="li-field-order"></li>
			<li class="li-field-rolename" data-colname="Role"><strong><?php echo esc_html($role['name']) ?></strong></li>
		</ul>
	</div>
	<div class="settings">
		<div class="acf-field-editor">
			<div class="acf-field-settings-main">
			<?php
			foreach( $redirections as $act => $data ) {
				$_choices = array_intersect_key(
					$choices,
					array_flip($data['choices']) // keys to be extracted
				);
				$_choices = ['' => __( 'Use global rule', 'advanced-members')] + $_choices;
				/* translators: form action names string */
				$instuction = sprintf( __( 'Set a URL to redirect user after they %s', 'advanced-members' ), $data['action'] );
				acf_render_field_wrap(
					[
						'type'         => 'select',
						'name'         => '_after_' . $act,
						'key'          => '_after_' . $act,
						'prefix'       => $prefix,
						'value'        => amem()->options->get('redirect/roles/'.$key.'/_after_' . $act),
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
						'prefix'       => $prefix,
						'value'        => amem()->options->get('redirect/roles/' .$key .'/'. $act . '_redirect_url'),
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
			?>
			</div>
		</div>
	</div>
</div>

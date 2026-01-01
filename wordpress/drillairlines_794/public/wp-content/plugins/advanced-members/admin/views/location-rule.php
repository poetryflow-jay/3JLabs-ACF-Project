<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

// vars
$prefix = 'amem_options['.$ruletab.'][rules][' . $rule['id'] . ']';
?>
<tr data-id="<?php echo esc_attr($rule['id']); ?>">
	<td class="role">
		<?php
		// array
		if ( is_array( $roles ) ) {
			acf_render_field(
				array(
					'type'    => 'select',
					'name'    => 'role',
					'prefix'  => $prefix,
					'value'   => $rule['role'],
					'choices' => $roles,
					'class'   => 'role-location-rule',
				)
			);
		}

		?>
	</td>
	<td class="operator">
		<span><?php esc_html_e( 'Form is', 'advanced-members' ); ?></span>
	</td>
	<td class="value">
		<?php

		// array
		if ( is_array( $forms ) ) {
			acf_render_field(
				array(
					'type'    => 'select',
					'name'    => 'value',
					'class'   => 'location-rule-value',
					'prefix'  => $prefix,
					'value'   => $rule['value'],
					'choices' => $forms,
				)
			);

			// custom
		} else {
			// Forms is escaped themselves
			echo $forms;
		}

		?>
	</td>
	<td class="remove">
		<a href="#" class="acf-icon -minus remove-location-rule"></a>
	</td>
</tr>

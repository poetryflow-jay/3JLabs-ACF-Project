<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<div class="advanced-members-field role-redirection-table<?php echo esc_attr($table_wrap_class) ?>">
	<div class="acf-setting-list-wrap roles-table">
		<ul class="acf-hl acf-thead">
			<li class="li-field-order"></li>
			<li class="li-field-role"><?php esc_html_e( 'User Roles', 'advanced-members' ); ?></li>
		</ul>
		<div class="acf-field-list">
			<?php
				foreach ($all_roles as $key => $role) {
					amem_get_view(
						__DIR__ . '/html-option-single-role.php',
						array(
							'key'								=> $key,
							'role'							=> $role,
							'redirections'			=> $redirections,
							'choices'						=> $choices,
						)
					);
				}
			?>
		</div>
	</div>
</div>

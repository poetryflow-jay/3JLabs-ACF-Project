<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<div class="rule-group">

	<table class="acf-table -clear">
		<tbody>
			<?php
			foreach ( $group as $i => $rule ) :

				// validate rule
				$rule = acf_validate_location_rule( $rule );

				// append id and group
				$rule['id']    = "rule_{$i}";

				// view
				amem_get_view(
					__DIR__ . '/location-rule.php',
					array(
						'ruletab'	=> $ruletab,
						'roles'		=> $roles,
						'forms'		=> $forms,
						'rule' 		=> $rule,
					)
				);
			endforeach;
			?>
		</tbody>
	</table>
	<a href="#" class="button add-role-rule"><?php esc_html_e( 'Add role', 'advanced-members' ); ?></a>

</div>

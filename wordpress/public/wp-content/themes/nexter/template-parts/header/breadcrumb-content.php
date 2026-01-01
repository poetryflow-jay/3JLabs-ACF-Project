<?php
/**
 * Breadcrumb
 *
 * The Header part of breadcrumb For Nexter.
 *
 * @package	Nexter
 * @since	1.0.0
 */
?>
<div class="nxt-breadcrumb-wrap">
	<?php
		if ( nexter_settings_page_get( 'header_footer_css' ) ) {
			echo '<div class="' . esc_attr( nexter_get_container_class( 'site-header-container' ) ) . '">';
		}

		do_action( 'nexter_breadcrumb_content' );

		if ( nexter_settings_page_get( 'header_footer_css' ) ) {
			echo '</div>';
		}
	?>
</div><!-- Nexter Breadcrumb -->
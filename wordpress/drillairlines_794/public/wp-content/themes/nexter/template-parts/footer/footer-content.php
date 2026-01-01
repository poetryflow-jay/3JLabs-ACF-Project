<?php
/**
 * Template Footer
 *
 * The Footer For Nexter.
 *
 * @package	Nexter
 * @since	1.0.0
 */
?>
<div class="nxt-footer-wrap" style="padding:0;">
	<?php
		if ( nexter_settings_page_get( 'header_footer_css' ) ) {
			echo '<div class="' . esc_attr( nexter_get_container_class( 'site-footer-container' ) ) . '">';
		}

		do_action( 'nexter_footer_content' );

		if ( nexter_settings_page_get( 'header_footer_css' ) ) {
			echo '</div>';
		}
	?>
</div><!-- Footer Content -->
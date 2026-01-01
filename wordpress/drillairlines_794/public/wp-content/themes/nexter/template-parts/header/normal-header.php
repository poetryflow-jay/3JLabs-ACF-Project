<?php
/**
 * Template Header
 *
 * The Header part of Normal Header For Nexter.
 *
 * @package	Nexter
 * @since	1.0.0
 */
?>
<div class="nxt-normal-header <?php echo (nexter_settings_page_get( 'header_footer_css' ) ? esc_attr(nexter_get_container_class('site-header-container')) : ''); ?>">
	<?php do_action( 'nexter_normal_header_content' ); ?>			
</div> <!-- Normal Header Content -->
<?php
/**
 * Template Header
 *
 * The Header part of Sticky Header For Nexter.
 *
 * @package	Nexter
 * @since	1.0.10
 */
?>
<div class="nxt-sticky-header <?php echo (nexter_settings_page_get( 'header_footer_css' ) ? esc_attr(nexter_get_container_class('site-header-container')) : ''); ?>">
	<?php do_action('nexter_sticky_header_content'); ?>			
</div> <!-- sticky Header Content -->
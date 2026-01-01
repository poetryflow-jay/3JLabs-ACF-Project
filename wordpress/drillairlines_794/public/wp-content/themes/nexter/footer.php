<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Nexter
 * @since	1.0.0
 */
$get_sidebar = nexter_site_sidebar_layout();

$has_sidebar = !empty($get_sidebar) && ($get_sidebar['layout'] === 'left-sidebar' || $get_sidebar['layout'] === 'right-sidebar');
?>
			<?php nxt_content_bottom(); ?>
		<?php if(nexter_settings_page_get( 'container_css' ) || $has_sidebar){ ?>
			</div><!--nxt container-->
		<?php } ?>
	</div><!-- content -->

	<?php nxt_footer_before(); ?>	
	<?php do_action( 'nexter_footer' ); ?>
	<?php nxt_footer_after(); ?>
	
</div><!-- wrapper-main -->

<?php wp_footer(); ?>
<?php nxt_body_bottom(); ?>
</body>
</html>
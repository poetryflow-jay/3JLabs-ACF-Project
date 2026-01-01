<?php
/**
 * Template Maintenance Mode
 *
 * @package Nexter Extensions Pro
 * @since 1.0.0
 */
get_header();
?>
<style>.nexter-maintenance-mode{position:relative;display:block;width:100%;padding:40px 0;text-align:center} .maintenance-mode-img{max-width:500px} .nexter-maintenance-mode .page-title{position:relative;text-align:center;font-size:30px;line-height:1.2;color:#333;margin:30px 0}</style>
<div class="nexter-maintenance-mode">
	
	<header class="page-header">
	
		<img src="<?php echo esc_url(NXT_PRO_EXT_URI .'/assets/images/under-construction.svg'); ?>" class="maintenance-mode-img" alt="<?php echo esc_attr__('maintenance mode','nexter-pro-extensions'); ?>" />
		<?php if( class_exists('Nexter_Pro_Maintenance_Mode') && Nexter_Pro_Maintenance_Mode::get_options('nxt-maintenance-mode','maintenance') === 'maintenance' ){ ?>
			<h1 class="page-title"><?php esc_html_e( 'Website is Under Maintenance.', 'nexter-pro-extensions' ); ?></h1>
		<?php }else if( class_exists('Nexter_Pro_Maintenance_Mode') && Nexter_Pro_Maintenance_Mode::get_options('nxt-maintenance-mode','maintenance') === 'coming_soon' ){ ?>
			<h1 class="page-title"><?php esc_html_e( 'We are coming soon.', 'nexter-pro-extensions' ); ?></h1>
		<?php } ?>
		
	</header><!-- .page-header -->

</div>
<?php get_footer(); ?>
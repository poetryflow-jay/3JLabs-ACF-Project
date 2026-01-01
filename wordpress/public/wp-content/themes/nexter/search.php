<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Nexter
 * @since	1.0.0
 */

get_header();

$get_sidebar = nexter_site_sidebar_layout();
$layout      = isset($get_sidebar['layout']) ? $get_sidebar['layout'] : '';
$has_sidebar = !empty($layout) && in_array($layout, array('left-sidebar', 'right-sidebar'), true);
$load_container = nexter_settings_page_get('container_css');
// Column setup
$content_column = $has_sidebar ? 'nxt-col-md-8 nxt-col-sm-12' : 'nxt-col-md-12';
?>
	
<div id="primary" class="content-area">
	<main id="main" class="site-main">

	<?php if ( have_posts() ) : ?>

		<header class="nxt-search-header">
			<h2 class="page-title">
				<?php
				/* translators: %s: search query. */
				printf( esc_html__( 'Search Results for : %s', 'nexter' ), '<span>' . get_search_query() . '</span>' );
				?>
			</h2>
		</header><!-- .page-header -->

		<?php
			if ($load_container || $has_sidebar) :
			echo '<div class="nxt-row">';
			
				/* Left Sidebar */
				if ( $layout == 'left-sidebar' ) :
					get_sidebar();
				endif;
				/* Left Sidebar */
				
				echo '<div class="nxt-col '.esc_attr($content_column).'">';
			endif;
					echo '<div class="nxt-blog-post-listing">';
					
						echo '<div class="nxt-row m-0">';
						/* Start the Loop */
							while ( have_posts() ) :
								the_post();
								
								get_template_part( 'template-parts/archive/archive', 'layout-1' ); 
								
							endwhile;
							
						echo '</div>'; //End nxt-row
						$pagination = nexter_pagination();
						if ($pagination !== null) {
							echo wp_kses_post($pagination);
						}
					echo '</div>'; //End blog-post-listing
			if ($load_container || $has_sidebar) :
				echo '</div>'; //End nxt-col
				
				/* Right Sidebar */
				if ( $layout == 'right-sidebar' ) :
					get_sidebar();
				endif;
				/* Right Sidebar */
				
			echo '</div>'; //End nxt-row
			endif;
	else :

		get_template_part( 'template-parts/content', 'none' );

	endif;
	?>

	</main><!-- #main -->
</div><!-- #primary -->
		
<?php
get_footer();
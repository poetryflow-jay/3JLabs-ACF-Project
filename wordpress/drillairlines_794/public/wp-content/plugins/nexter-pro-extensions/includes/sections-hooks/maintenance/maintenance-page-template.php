<?php
/**
 * Template Name: Maintenance Mode Page
 */
get_header();

echo '<div class="nxt-row">';
	echo '<div class="nxt-col nxt-col-md-12">';
		echo '<div id="primary" class="content-area">';
			echo '<main id="main" class="site-main">';
				echo '<article>';
					echo '<div class="entry-content">';
						if ( class_exists( 'Nexter_Builder_Compatibility' ) ) {
							if(!empty($settings) && isset($settings[0]) && isset($settings[0]->post_content) && !empty($settings[0]->post_content)){
								$post_id_data = $settings[0]->ID;

								$page_base_instance = Nexter_Builder_Compatibility::get_instance();
								$page_builder_instance = $page_base_instance->get_active_page_builder( $post_id_data );
								
								$page_builder_instance->render_content( $post_id_data );
								
								if ( is_callable( array( $page_builder_instance, 'enqueue_scripts' ) ) ) {
									$page_builder_instance->enqueue_scripts( $post_id_data );
								}
							}else{
								echo get_the_content();
							}
						}
					echo '</div>';
				echo '</article>';
			echo '</main>';
		echo '</div>';
	echo '</div>';
echo '</div>';

get_footer();
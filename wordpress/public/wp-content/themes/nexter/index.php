<?php
/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Nexter
 * @since   1.0.0
 */

get_header();

$get_sidebar    = nexter_site_sidebar_layout();
$layout         = isset($get_sidebar['layout']) ? $get_sidebar['layout'] : '';
$has_sidebar    = in_array($layout, array('left-sidebar', 'right-sidebar'), true);
$load_container = nexter_settings_page_get('container_css');

// Column setup
$content_column = $has_sidebar ? 'nxt-col-md-8 nxt-col-sm-12' : 'nxt-col-md-12';
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

    <?php if (have_posts()) : ?>

        <?php if ($load_container || $has_sidebar) : ?>
            <div class="nxt-row">
                <?php if ($layout === 'left-sidebar') : ?>
                    <?php get_sidebar(); ?>
                <?php endif; ?>

                <div class="nxt-col <?php echo esc_attr($content_column); ?>">
        <?php endif; ?>

                    <div class="nxt-blog-post-listing mt-2">
                        <div class="nxt-row m-0">
                            <?php
                            while (have_posts()) :
                                the_post();
                                get_template_part('template-parts/archive/archive', 'layout-1');
                            endwhile;
                            ?>
                        </div>

                        <?php
                        $pagination = nexter_pagination();
                        if ($pagination !== null) {
                            echo wp_kses_post($pagination);
                        }
                        ?>
                    </div><!-- .nxt-blog-post-listing -->

        <?php if ($load_container || $has_sidebar) : ?>
                </div><!-- .nxt-col -->

                <?php if ($layout === 'right-sidebar') : ?>
                    <?php get_sidebar(); ?>
                <?php endif; ?>
            </div><!-- .nxt-row -->
        <?php endif; ?>

    <?php else : ?>
        <?php get_template_part('template-parts/content', 'none'); ?>
    <?php endif; ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
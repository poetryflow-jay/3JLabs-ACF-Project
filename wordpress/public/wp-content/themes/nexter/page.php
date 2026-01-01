<?php
/**
 * The template for displaying all pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Nexter
 * @since   1.0.0
 */

get_header();

$get_sidebar   = nexter_site_sidebar_layout();
$layout        = isset($get_sidebar['layout']) ? $get_sidebar['layout'] : '';
$has_sidebar   = !empty($layout) && in_array($layout, array('left-sidebar', 'right-sidebar'), true);

// Column setup
$content_column = $has_sidebar ? 'nxt-col-md-8 nxt-col-sm-12' : 'nxt-col-md-12';
?>

<?php if (nexter_settings_page_get('container_css') || $has_sidebar) : ?>
    <div class="nxt-row">

        <?php if ($layout === 'left-sidebar') : ?>
            <?php get_sidebar(); ?>
        <?php endif; ?>

        <div class="nxt-col <?php echo esc_attr($content_column); ?>">
            <div id="primary" class="content-area">
                <main id="main" class="site-main">
                    <?php
                    while (have_posts()) :
                        the_post();

                        do_action('nexter_single_page_content');

                        if (comments_open() || get_comments_number()) :
                            comments_template();
                        endif;

                    endwhile;
                    ?>
                </main>
            </div>
        </div>

        <?php if ($layout === 'right-sidebar') : ?>
            <?php get_sidebar(); ?>
        <?php endif; ?>

    </div>
<?php else : ?>
    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <?php
            while (have_posts()) :
                the_post();

                do_action('nexter_single_page_content');

                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;

            endwhile;
            ?>
        </main>
    </div>
<?php endif; ?>

<?php get_footer(); ?>
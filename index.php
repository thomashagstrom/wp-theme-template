<?php
/**
 * Main template file.
 *
 * @package EditorialStarter
 */

get_header();
?>
<main id="primary" class="site-main container">
    <div class="content-layout">
        <div class="content-area">
            <?php if ( have_posts() ) : ?>
                <header class="page-header">
                    <?php if ( is_home() && ! is_front_page() ) : ?>
                        <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
                    <?php endif; ?>
                </header>

                <div class="post-grid">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        get_template_part( 'template-parts/content', 'excerpt' );
                    endwhile;
                    ?>
                </div>

                <?php the_posts_pagination(); ?>
            <?php else : ?>
                <?php get_template_part( 'template-parts/content', 'none' ); ?>
            <?php endif; ?>
        </div>

        <?php get_sidebar(); ?>
    </div>
</main>
<?php
get_footer();

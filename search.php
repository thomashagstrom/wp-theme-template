<?php
/**
 * The template for displaying search results pages.
 *
 * @package EditorialStarter
 */

get_header();
?>
<main id="primary" class="site-main container">
    <header class="page-header">
        <h1 class="page-title">
            <?php
            printf(
                /* translators: %s: search query. */
                esc_html__( 'Search results for: %s', 'editorial-starter' ),
                '<span>' . get_search_query() . '</span>'
            );
            ?>
        </h1>
    </header>

    <?php if ( have_posts() ) : ?>
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
</main>
<?php
get_footer();

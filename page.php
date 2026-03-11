<?php
/**
 * The template for displaying all pages.
 *
 * @package EditorialStarter
 */

get_header();
?>
<main id="primary" class="site-main site-main--singular">
    <?php
    while ( have_posts() ) :
        the_post();

        get_template_part( 'template-parts/content', 'page' );

        if ( comments_open() || get_comments_number() ) {
            echo '<div class="container container--reading container--comments">';
            comments_template();
            echo '</div>';
        }
    endwhile;
    ?>
</main>
<?php
get_footer();

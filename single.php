<?php
/**
 * The template for displaying all single posts
 *
 * @package EditorialStarter
 */

get_header();
?>
<main id="primary" class="site-main site-main--singular">
    <?php
    while ( have_posts() ) :
        the_post();

        get_template_part( 'template-parts/content', 'single' );

        if ( comments_open() || get_comments_number() ) {
            echo '<div class="container container--reading container--comments">';
            comments_template();
            echo '</div>';
        }

        $navigation = get_the_post_navigation(
            array(
                'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous story', 'editorial-starter' ) . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next story', 'editorial-starter' ) . '</span> <span class="nav-title">%title</span>',
            )
        );

        if ( $navigation ) {
            echo '<div class="container container--reading post-navigation__container">';
            echo $navigation; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- navigation contains sanitized markup.
            echo '</div>';
        }
    endwhile;
    ?>
</main>
<?php
get_footer();

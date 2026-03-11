<?php
/**
 * The template for displaying comments
 *
 * This contains both the current comments and the comment form.
 *
 * @package EditorialStarter
 */

if ( post_password_required() ) {
    return;
}
?>
<section id="comments" class="comments-area">
    <?php if ( have_comments() ) : ?>
        <h2 class="comments-title">
            <?php
            $comments_number = get_comments_number();
            if ( '1' === $comments_number ) {
                printf( esc_html__( 'One comment', 'editorial-starter' ) );
            } else {
                printf(
                    esc_html(
                        /* translators: %s: number of comments. */
                        _nx( '%s comment', '%s comments', $comments_number, 'comments title', 'editorial-starter' )
                    ),
                    esc_html( number_format_i18n( $comments_number ) )
                );
            }
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments(
                array(
                    'style'       => 'ol',
                    'short_ping'  => true,
                    'avatar_size' => 48,
                )
            );
            ?>
        </ol>

        <?php the_comments_navigation(); ?>
    <?php endif; ?>

    <?php
    if ( ! comments_open() && get_comments_number() ) :
        ?>
        <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'editorial-starter' ); ?></p>
        <?php
    endif;

    comment_form(
        array(
            'class_submit' => 'button',
            'title_reply'  => esc_html__( 'Join the discussion', 'editorial-starter' ),
        )
    );
    ?>
</section>

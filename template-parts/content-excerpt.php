<?php
/**
 * Template part for displaying posts within loops.
 *
 * @package EditorialStarter
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
        <a class="post-card__thumb" href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail( 'large' ); ?>
        </a>
    <?php endif; ?>

    <header class="entry-header">
        <?php the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h3>' ); ?>
    </header>

    <div class="post-meta">
        <span><?php echo esc_html( get_the_date() ); ?></span>
        <span><?php echo wp_kses_post( get_the_category_list( ', ' ) ); ?></span>
    </div>

    <div class="entry-summary">
        <?php the_excerpt(); ?>
    </div>

    <a class="read-more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read story', 'editorial-starter' ); ?></a>
</article>

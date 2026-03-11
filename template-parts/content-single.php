<?php
/**
 * Template part for displaying single post content.
 *
 * @package EditorialStarter
 */
?>
<?php
$byline_markup   = array_filter(
    array(
        editorial_starter_get_posted_on_markup(),
        editorial_starter_get_posted_by_markup(),
        editorial_starter_get_estimated_reading_time_markup(),
    )
);
$taxonomy_markup = editorial_starter_get_entry_taxonomy_markup();
$footer_markup   = editorial_starter_get_entry_footer_markup();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--single' ); ?>>
    <header class="entry-hero alignfull entry-hero--post<?php echo has_post_thumbnail() ? ' has-featured-media' : ''; ?>">
        <?php if ( has_post_thumbnail() ) : ?>
            <figure class="entry-hero__media" aria-hidden="true">
                <?php
                the_post_thumbnail(
                    'full',
                    array(
						'class'   => 'entry-hero__image',
						'loading' => 'eager',
                    )
                );
				?>
            </figure>
        <?php endif; ?>

        <div class="entry-hero__inner container">
            <?php
            if ( $taxonomy_markup ) {
				echo wp_kses_post( $taxonomy_markup ); }
			?>
            <?php the_title( '<h1 class="entry-hero__title">', '</h1>' ); ?>

            <?php if ( $byline_markup ) : ?>
                <div class="entry-hero__byline">
                    <?php
                    foreach ( $byline_markup as $meta_item ) {
						echo wp_kses_post( $meta_item ); }
					?>
                </div>
            <?php endif; ?>

            <?php if ( has_excerpt() ) : ?>
                <div class="entry-hero__excerpt">
                    <?php echo wp_kses_post( get_the_excerpt() ); ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="container container--reading">
        <div class="entry-content entry-content--article is-layout-flow">
            <?php
            the_content();

            wp_link_pages(
                array(
                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'editorial-starter' ),
                    'after'  => '</div>',
                )
            );
            ?>
        </div>
    </div>

    <?php if ( editorial_starter_is_primary_cta_enabled() ) : ?>
        <div class="container container--reading">
            <section class="entry-primary-cta card card--primary-cta" aria-label="<?php esc_attr_e( 'Primary call to action', 'editorial-starter' ); ?>">
                <h2 class="section-heading"><?php echo esc_html( editorial_starter_get_primary_card_heading() ); ?></h2>
                <p><?php echo esc_html( editorial_starter_get_primary_card_description() ); ?></p>
                <div class="cta-group">
                    <a class="button" href="<?php echo esc_url( editorial_starter_get_primary_cta_url() ); ?>" data-cta-type="primary" data-cta-placement="inline-post">
                        <?php echo esc_html( editorial_starter_get_primary_cta_label() ); ?>
                    </a>
                    <a class="button button--ghost" href="<?php echo esc_url( editorial_starter_get_subscribe_cta_url() ); ?>" data-cta-type="newsletter" data-cta-placement="inline-post">
                        <?php echo esc_html( editorial_starter_get_secondary_cta_label() ); ?>
                    </a>
                </div>
            </section>
        </div>
    <?php endif; ?>

    <?php if ( $footer_markup ) : ?>
        <div class="container container--reading">
            <footer class="entry-footer entry-footer--post">
                <?php echo wp_kses_post( $footer_markup ); ?>
            </footer>
        </div>
    <?php endif; ?>
</article>

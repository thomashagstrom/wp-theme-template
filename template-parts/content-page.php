<?php
/**
 * Template part for displaying page content in page.php
 *
 * @package EditorialStarter
 */
?>
<?php
$footer_markup = editorial_starter_get_entry_footer_markup();
$meta_markup   = array_filter(
    array(
        editorial_starter_get_posted_on_markup(),
    )
);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--page' ); ?>>
    <header class="entry-hero alignfull entry-hero--page<?php echo has_post_thumbnail() ? ' has-featured-media' : ''; ?>">
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
            <span class="entry-hero__label"><?php echo esc_html_x( 'Featured Page', 'page hero eyebrow label', 'editorial-starter' ); ?></span>
            <?php the_title( '<h1 class="entry-hero__title">', '</h1>' ); ?>

            <?php if ( $meta_markup ) : ?>
                <div class="entry-hero__byline">
                    <?php
                    foreach ( $meta_markup as $meta_item ) {
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
        <div class="entry-content entry-content--page is-layout-flow">
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
                    <a class="button" href="<?php echo esc_url( editorial_starter_get_primary_cta_url() ); ?>" data-cta-type="primary" data-cta-placement="inline-page">
                        <?php echo esc_html( editorial_starter_get_primary_cta_label() ); ?>
                    </a>
                    <a class="button button--ghost" href="<?php echo esc_url( editorial_starter_get_subscribe_cta_url() ); ?>" data-cta-type="newsletter" data-cta-placement="inline-page">
                        <?php echo esc_html( editorial_starter_get_secondary_cta_label() ); ?>
                    </a>
                </div>
            </section>
        </div>
    <?php endif; ?>

    <?php if ( $footer_markup ) : ?>
        <div class="container container--reading">
            <footer class="entry-footer entry-footer--page">
                <?php echo wp_kses_post( $footer_markup ); ?>
            </footer>
        </div>
    <?php endif; ?>
</article>

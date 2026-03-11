<?php
/**
 * The front page template
 *
 * @package EditorialStarter
 */

get_header();
?>
<main id="primary" class="site-main">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--page entry--front-page' ); ?>>
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
                        <span class="entry-hero__label"><?php echo esc_html_x( 'Homepage', 'front page hero eyebrow label', 'editorial-starter' ); ?></span>
                        <?php the_title( '<h1 class="entry-hero__title">', '</h1>' ); ?>

                        <?php if ( has_excerpt() ) : ?>
                            <div class="entry-hero__excerpt">
                                <?php echo wp_kses_post( get_the_excerpt() ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </header>

                <div class="container container--reading">
                    <div class="entry-content entry-content--page is-layout-flow">
                        <?php the_content(); ?>
                    </div>
                </div>
            </article>
            <?php
        endwhile;
    else :
        get_template_part( 'template-parts/content', 'none' );
    endif;
    ?>
</main>
<?php
get_footer();

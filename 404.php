<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package EditorialStarter
 */

get_header();
?>
<main id="primary" class="site-main container">
    <section class="error-404 not-found">
        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e( 'Page not found', 'editorial-starter' ); ?></h1>
        </header>

        <div class="page-content">
            <p><?php esc_html_e( 'The page you were looking for could not be found. Try searching or head back to the homepage.', 'editorial-starter' ); ?></p>
            <?php get_search_form(); ?>
            <a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'editorial-starter' ); ?></a>
        </div>
    </section>
</main>
<?php
get_footer();

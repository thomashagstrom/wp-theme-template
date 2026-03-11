<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @package EditorialStarter
 */
?>
<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e( 'Nothing published yet.', 'editorial-starter' ); ?></h1>
    </header>

    <div class="page-content">
        <p><?php esc_html_e( 'There is no content to show here yet. Try searching or publish your first entry.', 'editorial-starter' ); ?></p>
        <?php get_search_form(); ?>
    </div>
</section>

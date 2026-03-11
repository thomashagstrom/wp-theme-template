<?php
/**
 * Newsletter CTA pattern.
 *
 * @package EditorialStarter
 */

return array(
    'title'       => __( 'Newsletter Signup', 'editorial-starter' ),
    'slug'        => 'editorial-starter/newsletter-signup',
    'description' => __( 'Lead-capture block for newsletters, product updates, and launch lists.', 'editorial-starter' ),
    'categories'  => array( 'editorial-starter', 'editorial-starter-cta' ),
    'keywords'    => array( __( 'newsletter', 'editorial-starter' ), __( 'signup', 'editorial-starter' ), __( 'email', 'editorial-starter' ) ),
    'content'     => sprintf(
        <<<'HTML'
<!-- wp:group {"anchor":"newsletter-signup","className":"container newsletter","layout":{"type":"constrained"}} -->
<div class="wp-block-group container newsletter" id="newsletter-signup"><div class="wp-block-group__inner-container">
    <!-- wp:heading {"level":2,"className":"section-heading"} -->
    <h2 class="wp-block-heading section-heading">%1$s</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph -->
    <p>%2$s</p>
    <!-- /wp:paragraph -->
    <!-- wp:jetpack/subscriptions {"showSubscribersTotal":false,"className":"newsletter-jetpack"} /-->
    <!-- wp:paragraph {"fontSize":"small"} -->
    <p class="has-small-font-size">%3$s</p>
    <!-- /wp:paragraph -->
</div></div>
<!-- /wp:group -->
HTML,
        esc_html__( 'Join the newsletter', 'editorial-starter' ),
        esc_html__( 'Use this section to collect subscribers for stories, product launches, or campaign updates.', 'editorial-starter' ),
        esc_html__( 'Short, clear value propositions usually convert better than generic “stay updated” copy.', 'editorial-starter' )
    ),
);

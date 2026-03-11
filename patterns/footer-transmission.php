<?php
/**
 * Footer utility pattern.
 *
 * @package EditorialStarter
 */

return array(
    'title'       => __( 'Footer Signoff', 'editorial-starter' ),
    'slug'        => 'editorial-starter/footer-signoff',
    'description' => __( 'Compact footer content with navigation and a short editorial signoff.', 'editorial-starter' ),
    'categories'  => array( 'footer', 'editorial-starter' ),
    'keywords'    => array(
        __( 'footer', 'editorial-starter' ),
        __( 'links', 'editorial-starter' ),
        __( 'signoff', 'editorial-starter' ),
    ),
    'content'     => sprintf(
        <<<'HTML'
<!-- wp:group {"className":"footer-signoff-pattern","layout":{"type":"constrained"}} -->
<div class="wp-block-group footer-signoff-pattern">
    <!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","justifyContent":"center"}} /-->
    <!-- wp:paragraph {"align":"center","fontSize":"small"} -->
    <p class="has-text-align-center has-small-font-size">%1$s</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML,
        esc_html__( 'Use this area for policy links, credits, or a concise brand line.', 'editorial-starter' )
    ),
);

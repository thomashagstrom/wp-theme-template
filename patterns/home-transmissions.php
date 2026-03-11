<?php
/**
 * Complete homepage layout pattern.
 *
 * @package EditorialStarter
 */

$primary_cta_url = function_exists( 'editorial_starter_get_primary_cta_url' )
    ? editorial_starter_get_primary_cta_url()
    : '/featured-story/';

$primary_cta_label = function_exists( 'editorial_starter_get_primary_cta_label' )
    ? editorial_starter_get_primary_cta_label()
    : 'Read the featured story';

$secondary_cta_label = function_exists( 'editorial_starter_get_secondary_cta_label' )
    ? editorial_starter_get_secondary_cta_label()
    : 'Join the newsletter';

$escaped_primary_cta_label = function_exists( 'esc_html' )
    ? esc_html( $primary_cta_label )
    : $primary_cta_label;

$escaped_secondary_cta_label = function_exists( 'esc_html' )
    ? esc_html( $secondary_cta_label )
    : $secondary_cta_label;

return array(
    'title'       => __( 'Homepage Editorial Layout', 'editorial-starter' ),
    'slug'        => 'editorial-starter/homepage-editorial-layout',
    'description' => __( 'Full homepage layout for editorial brands, campaigns, and product storytelling sites.', 'editorial-starter' ),
    'categories'  => array( 'editorial-starter', 'editorial-starter-layouts' ),
    'keywords'    => array( __( 'homepage', 'editorial-starter' ), __( 'layout', 'editorial-starter' ), __( 'editorial', 'editorial-starter' ) ),
    'content'     => sprintf(
        <<<'HTML'
<!-- wp:group {"align":"full","className":"hero"} -->
<div class="wp-block-group alignfull hero">
    <!-- wp:columns {"className":"container hero__grid"} -->
    <div class="wp-block-columns container hero__grid">
        <!-- wp:column {"className":"hero__content"} -->
        <div class="wp-block-column hero__content">
            <!-- wp:paragraph {"className":"hero__preheading"} -->
            <p class="hero__preheading">%1$s</p>
            <!-- /wp:paragraph -->
            <!-- wp:heading {"level":1,"className":"hero__title"} -->
            <h1 class="wp-block-heading hero__title"><span data-glitch="%2$s">%2$s</span><br><span data-glitch="%3$s">%3$s</span></h1>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"className":"hero__description"} -->
            <p class="hero__description">%4$s</p>
            <!-- /wp:paragraph -->
            <!-- wp:buttons {"className":"cta-group","layout":{"type":"flex","justifyContent":"left"}} -->
            <div class="wp-block-buttons cta-group">
                <!-- wp:button -->
                <div class="wp-block-button"><a class="wp-block-button__link button" href="%5$s" data-cta-type="primary" data-cta-placement="hero">%6$s</a></div>
                <!-- /wp:button -->
                <!-- wp:button -->
                <div class="wp-block-button"><a class="wp-block-button__link button button--ghost" href="#latest-heading">%7$s</a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->
        </div>
        <!-- /wp:column -->
        <!-- wp:column {"className":"hero__visual"} -->
        <div class="wp-block-column hero__visual">
            <!-- wp:group {"className":"data-orbit","layout":{"type":"default"}} -->
            <div class="wp-block-group data-orbit">
                <div class="wp-block-group__inner-container">
                    <!-- wp:group {"className":"data-orbit__core","layout":{"type":"default"}} -->
                    <div class="wp-block-group data-orbit__core">
                        <div class="wp-block-group__inner-container">
                            <!-- wp:heading {"level":3} -->
                            <h3 class="wp-block-heading">%8$s</h3>
                            <!-- /wp:heading -->
                            <!-- wp:paragraph -->
                            <p>%9$s</p>
                            <!-- /wp:paragraph -->
                        </div>
                    </div>
                    <!-- /wp:group -->
                    <!-- wp:paragraph {"className":"orbit-node"} -->
                    <p class="orbit-node">%10$s</p>
                    <!-- /wp:paragraph -->
                    <!-- wp:paragraph {"className":"orbit-node"} -->
                    <p class="orbit-node">%11$s</p>
                    <!-- /wp:paragraph -->
                    <!-- wp:paragraph {"className":"orbit-node"} -->
                    <p class="orbit-node">%12$s</p>
                    <!-- /wp:paragraph -->
                    <!-- wp:paragraph {"className":"orbit-node"} -->
                    <p class="orbit-node">%13$s</p>
                    <!-- /wp:paragraph -->
                </div>
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"container section--grid","layout":{"type":"constrained"}} -->
<div class="wp-block-group container section--grid"><div class="wp-block-group__inner-container">
    <!-- wp:heading {"level":2,"className":"section-heading"} -->
    <h2 class="wp-block-heading section-heading">%14$s</h2>
    <!-- /wp:heading -->
    <!-- wp:query {"queryId":1,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false,"metaQuery":[{"key":"featured_post","value":"1","compare":"="}]},"displayLayout":{"type":"flex","columns":3}} -->
    <div class="wp-block-query">
        <!-- wp:post-template {"className":"featured-grid"} -->
        <!-- wp:group {"className":"card","layout":{"type":"constrained"}} -->
        <div class="wp-block-group card">
            <!-- wp:post-featured-image {"isLink":true,"height":"220px"} /-->
            <!-- wp:post-title {"level":3,"isLink":true,"className":"entry-title"} /-->
            <!-- wp:group {"className":"post-meta","layout":{"type":"flex","justifyContent":"left"}} -->
            <div class="wp-block-group post-meta">
                <!-- wp:post-date /-->
                <!-- wp:post-terms {"term":"category"} /-->
            </div>
            <!-- /wp:group -->
            <!-- wp:post-excerpt {"moreText":"%15$s","excerptLength":24} /-->
        </div>
        <!-- /wp:group -->
        <!-- /wp:post-template -->
        <!-- wp:query-no-results -->
        <!-- wp:paragraph -->
        <p>%16$s</p>
        <!-- /wp:paragraph -->
        <!-- /wp:query-no-results -->
    </div>
    <!-- /wp:query -->
</div></div>
<!-- /wp:group -->

<!-- wp:group {"className":"container section--grid","layout":{"type":"constrained"}} -->
<div class="wp-block-group container section--grid"><div class="wp-block-group__inner-container">
    <!-- wp:heading {"level":2,"className":"section-heading"} -->
    <h2 class="wp-block-heading section-heading">%17$s</h2>
    <!-- /wp:heading -->
    <!-- wp:group {"className":"card card--briefing","layout":{"type":"constrained"}} -->
    <div class="wp-block-group card card--briefing">
        <!-- wp:paragraph -->
        <p>%18$s</p>
        <!-- /wp:paragraph -->
        <!-- wp:group {"className":"status-board","layout":{"type":"flex","orientation":"horizontal","justifyContent":"left"}} -->
        <div class="wp-block-group status-board">
            <!-- wp:group {"tagName":"article","className":"status-card","layout":{"type":"constrained"}} -->
            <article class="wp-block-group status-card"><div class="wp-block-group__inner-container">
                <!-- wp:paragraph {"fontSize":"body"} -->
                <p class="has-body-font-size">%19$s</p>
                <!-- /wp:paragraph -->
                <!-- wp:heading {"level":3} -->
                <h3 class="wp-block-heading">%20$s</h3>
                <!-- /wp:heading -->
                <!-- wp:paragraph -->
                <p>%21$s</p>
                <!-- /wp:paragraph -->
            </div></article>
            <!-- /wp:group -->
            <!-- wp:group {"tagName":"article","className":"status-card","layout":{"type":"constrained"}} -->
            <article class="wp-block-group status-card"><div class="wp-block-group__inner-container">
                <!-- wp:paragraph {"fontSize":"body"} -->
                <p class="has-body-font-size">%22$s</p>
                <!-- /wp:paragraph -->
                <!-- wp:heading {"level":3} -->
                <h3 class="wp-block-heading">%23$s</h3>
                <!-- /wp:heading -->
                <!-- wp:paragraph -->
                <p>%24$s</p>
                <!-- /wp:paragraph -->
            </div></article>
            <!-- /wp:group -->
            <!-- wp:group {"tagName":"article","className":"status-card","layout":{"type":"constrained"}} -->
            <article class="wp-block-group status-card"><div class="wp-block-group__inner-container">
                <!-- wp:paragraph {"fontSize":"body"} -->
                <p class="has-body-font-size">%25$s</p>
                <!-- /wp:paragraph -->
                <!-- wp:heading {"level":3} -->
                <h3 class="wp-block-heading">%26$s</h3>
                <!-- /wp:heading -->
                <!-- wp:paragraph -->
                <p>%27$s</p>
                <!-- /wp:paragraph -->
            </div></article>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
</div></div>
<!-- /wp:group -->

<!-- wp:group {"anchor":"newsletter-signup","className":"container newsletter","layout":{"type":"constrained"}} -->
<div class="wp-block-group container newsletter" id="newsletter-signup"><div class="wp-block-group__inner-container">
    <!-- wp:heading {"level":2,"className":"section-heading"} -->
    <h2 class="wp-block-heading section-heading">%28$s</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph -->
    <p>%29$s</p>
    <!-- /wp:paragraph -->
    <!-- wp:jetpack/subscriptions {"showSubscribersTotal":false,"className":"newsletter-jetpack"} /-->
    <!-- wp:paragraph {"fontSize":"small"} -->
    <p class="has-small-font-size">%30$s</p>
    <!-- /wp:paragraph -->
</div></div>
<!-- /wp:group -->

<!-- wp:group {"className":"container feature-panel","layout":{"type":"constrained"}} -->
<div class="wp-block-group container feature-panel"><div class="wp-block-group__inner-container">
    <!-- wp:group {"className":"card card--spotlight","layout":{"type":"constrained"}} -->
    <div class="wp-block-group card card--spotlight">
        <!-- wp:heading {"level":2,"className":"section-heading"} -->
        <h2 class="wp-block-heading section-heading">%31$s</h2>
        <!-- /wp:heading -->
        <!-- wp:paragraph -->
        <p>%32$s</p>
        <!-- /wp:paragraph -->
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left"}} -->
        <div class="wp-block-buttons">
            <!-- wp:button -->
            <div class="wp-block-button"><a class="wp-block-button__link button" href="%5$s" data-cta-type="primary" data-cta-placement="feature-panel">%6$s</a></div>
            <!-- /wp:button -->
            <!-- wp:button -->
            <div class="wp-block-button"><a class="wp-block-button__link button button--ghost" href="#newsletter-signup" data-cta-type="newsletter" data-cta-placement="feature-panel">%33$s</a></div>
            <!-- /wp:button -->
        </div>
        <!-- /wp:buttons -->
    </div>
    <!-- /wp:group -->
</div></div>
<!-- /wp:group -->

<!-- wp:group {"className":"container section--grid","layout":{"type":"constrained"}} -->
<div class="wp-block-group container section--grid"><div class="wp-block-group__inner-container">
    <!-- wp:heading {"level":2,"className":"section-heading","anchor":"latest-heading"} -->
    <h2 class="wp-block-heading section-heading" id="latest-heading">%34$s</h2>
    <!-- /wp:heading -->
    <!-- wp:query {"queryId":2,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false},"displayLayout":{"type":"flex","columns":3}} -->
    <div class="wp-block-query">
        <!-- wp:post-template {"className":"featured-grid"} -->
        <!-- wp:group {"className":"card","layout":{"type":"constrained"}} -->
        <div class="wp-block-group card">
            <!-- wp:post-title {"level":3,"isLink":true,"className":"entry-title"} /-->
            <!-- wp:post-excerpt {"moreText":"%15$s","excerptLength":20} /-->
        </div>
        <!-- /wp:group -->
        <!-- /wp:post-template -->
        <!-- wp:query-no-results -->
        <!-- wp:paragraph -->
        <p>%35$s</p>
        <!-- /wp:paragraph -->
        <!-- /wp:query-no-results -->
    </div>
    <!-- /wp:query -->
</div></div>
<!-- /wp:group -->
HTML,
        esc_html__( 'Editorial Starter', 'editorial-starter' ),
        esc_html__( 'Stories with Shape', 'editorial-starter' ),
        esc_html__( 'Campaigns with Momentum', 'editorial-starter' ),
        esc_html__( 'Start with a flexible homepage that can support a flagship story, an evergreen editorial brand, or a product-led campaign.', 'editorial-starter' ),
        esc_url( $primary_cta_url ),
        $escaped_primary_cta_label,
        esc_html__( 'Browse latest posts', 'editorial-starter' ),
        esc_html__( 'Starter Kit', 'editorial-starter' ),
        esc_html__( 'Patterns, commerce blocks, analytics hooks, and release checks ready for customization.', 'editorial-starter' ),
        esc_html__( 'Patterns', 'editorial-starter' ),
        esc_html__( 'Commerce', 'editorial-starter' ),
        esc_html__( 'Newsletter', 'editorial-starter' ),
        esc_html__( 'SEO', 'editorial-starter' ),
        esc_html__( 'Featured Stories', 'editorial-starter' ),
        esc_html__( 'Read story', 'editorial-starter' ),
        esc_html__( 'No featured stories yet. Add the `featured_post` meta key to surface priority content here.', 'editorial-starter' ),
        esc_html__( 'What ships with the starter', 'editorial-starter' ),
        esc_html__( 'Use these content rails to explain how the homepage is organized before you replace them with customer material.', 'editorial-starter' ),
        esc_html__( 'Storytelling', 'editorial-starter' ),
        esc_html__( 'Lead with one strong narrative', 'editorial-starter' ),
        esc_html__( 'Frame a hero moment, support it with featured articles, and keep the page focused on one audience outcome.', 'editorial-starter' ),
        esc_html__( 'Commerce', 'editorial-starter' ),
        esc_html__( 'Drop in a Shopify block only when needed', 'editorial-starter' ),
        esc_html__( 'The product grid stays optional, so content-first builds do not inherit storefront complexity by accident.', 'editorial-starter' ),
        esc_html__( 'Retention', 'editorial-starter' ),
        esc_html__( 'Capture interest before launch day', 'editorial-starter' ),
        esc_html__( 'Newsletter forms and CTA analytics are already wired so campaign pages can be instrumented from the start.', 'editorial-starter' ),
        esc_html__( 'Join the newsletter', 'editorial-starter' ),
        esc_html__( 'Swap this copy for a specific lead magnet, editorial promise, or campaign update cadence.', 'editorial-starter' ),
        esc_html__( 'Keep the ask concise and make the benefit specific.', 'editorial-starter' ),
        esc_html__( 'Campaign Spotlight', 'editorial-starter' ),
        esc_html__( 'Use this panel for your primary conversion path: a flagship article, service page, pre-order flow, or seasonal promotion.', 'editorial-starter' ),
        $escaped_secondary_cta_label,
        esc_html__( 'Latest Posts', 'editorial-starter' ),
        esc_html__( 'Publish your first stories to replace this starter grid.', 'editorial-starter' )
    ),
);

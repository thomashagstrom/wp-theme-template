<?php
/**
 * Hero showcase block pattern.
 *
 * @package EditorialStarter
 */

$primary_cta_url = function_exists( 'editorial_starter_get_primary_cta_url' )
    ? editorial_starter_get_primary_cta_url()
    : '/featured-story/';

$primary_cta_label = function_exists( 'editorial_starter_get_primary_cta_label' )
    ? editorial_starter_get_primary_cta_label()
    : 'Read the featured story';

$escaped_primary_cta_label = function_exists( 'esc_html' )
    ? esc_html( $primary_cta_label )
    : $primary_cta_label;

return array(
    'title'         => __( 'Editorial Hero Showcase', 'editorial-starter' ),
    'slug'          => 'editorial-starter/editorial-hero-showcase',
    'description'   => __( 'Homepage hero with headline controls, supporting stats, and a configurable primary CTA.', 'editorial-starter' ),
    'categories'    => array( 'editorial-starter', 'editorial-starter-layouts' ),
    'keywords'      => array( __( 'hero', 'editorial-starter' ), __( 'cta', 'editorial-starter' ), __( 'homepage', 'editorial-starter' ) ),
    'viewportWidth' => 1440,
    'content'       => sprintf(
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
HTML,
        esc_html__( 'Editorial Starter', 'editorial-starter' ),
        esc_html__( 'Independent Ideas', 'editorial-starter' ),
        esc_html__( 'Ready to Launch', 'editorial-starter' ),
        esc_html__( 'Use this hero to frame your main story, product drop, or campaign message with a clear narrative and one primary action.', 'editorial-starter' ),
        esc_url( $primary_cta_url ),
        $escaped_primary_cta_label,
        esc_html__( 'Browse latest posts', 'editorial-starter' ),
        esc_html__( 'Starter Kit', 'editorial-starter' ),
        esc_html__( 'Patterns, commerce blocks, newsletter capture, and release automation.', 'editorial-starter' ),
        esc_html__( 'Patterns', 'editorial-starter' ),
        esc_html__( 'Commerce', 'editorial-starter' ),
        esc_html__( 'Newsletter', 'editorial-starter' ),
        esc_html__( 'SEO', 'editorial-starter' )
    ),
);

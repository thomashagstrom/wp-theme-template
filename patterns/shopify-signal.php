<?php
/**
 * Shopify product grid pattern.
 *
 * @package EditorialStarter
 */

return array(
    'title'       => __( 'Shopify Product Grid', 'editorial-starter' ),
    'slug'        => 'editorial-starter/shopify-product-grid',
    'description' => __( 'Responsive Shopify grid with configurable storefront, collection, and fallback states.', 'editorial-starter' ),
    'categories'  => array( 'editorial-starter', 'editorial-starter-layouts' ),
    'keywords'    => array(
        __( 'shopify', 'editorial-starter' ),
        __( 'commerce', 'editorial-starter' ),
        __( 'products', 'editorial-starter' ),
    ),
    'content'     => sprintf(
        <<<'HTML'
<!-- wp:group {"className":"container section--grid shopify-signal","layout":{"type":"constrained"}} -->
<div class="wp-block-group container section--grid shopify-signal">
    <!-- wp:heading {"level":2,"className":"section-heading"} -->
    <h2 class="wp-block-heading section-heading">%1$s</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>%2$s</p>
    <!-- /wp:paragraph -->

    <!-- wp:html -->
    <div class="shopify-products" data-shopify-products data-shopify-shop="shop.example.com" data-shopify-limit="3" data-shopify-locale="en-US" data-shopify-currency="USD" data-shopify-cta-label="%3$s" data-shopify-empty-title="%4$s" data-shopify-empty-cta="%5$s" data-shopify-fallback-url="https://shop.example.com" data-shopify-storefront-token="" data-shopify-storefront-api-version="2023-10">
        <div class="shopify-products__grid">
            <article class="card shopify-product shopify-product--placeholder">
                <div class="shopify-product__media shopify-product__media--placeholder"></div>
                <div class="shopify-product__content">
                    <h3 class="shopify-product__title"></h3>
                    <p class="shopify-product__price"></p>
                    <p class="shopify-product__excerpt"></p>
                    <span class="shopify-product__cta"></span>
                </div>
            </article>
            <article class="card shopify-product shopify-product--placeholder">
                <div class="shopify-product__media shopify-product__media--placeholder"></div>
                <div class="shopify-product__content">
                    <h3 class="shopify-product__title"></h3>
                    <p class="shopify-product__price"></p>
                    <p class="shopify-product__excerpt"></p>
                    <span class="shopify-product__cta"></span>
                </div>
            </article>
            <article class="card shopify-product shopify-product--placeholder">
                <div class="shopify-product__media shopify-product__media--placeholder"></div>
                <div class="shopify-product__content">
                    <h3 class="shopify-product__title"></h3>
                    <p class="shopify-product__price"></p>
                    <p class="shopify-product__excerpt"></p>
                    <span class="shopify-product__cta"></span>
                </div>
            </article>
        </div>
        <p class="shopify-products__error" hidden>%6$s</p>
        <div class="shopify-products__actions">
            <a class="shopify-products__more" href="https://shop.example.com" target="_blank" rel="noopener">%7$s</a>
        </div>
    </div>
    <!-- /wp:html -->
</div>
<!-- /wp:group -->
HTML,
        esc_html__( 'Featured Products', 'editorial-starter' ),
        esc_html__( 'Point this block at any Shopify storefront to highlight merch, books, subscriptions, or a focused collection.', 'editorial-starter' ),
        esc_html__( 'View product', 'editorial-starter' ),
        esc_html__( 'Products are unavailable right now.', 'editorial-starter' ),
        esc_html__( 'Visit the shop', 'editorial-starter' ),
        esc_html__( 'The storefront could not be loaded.', 'editorial-starter' ),
        esc_html__( 'Browse the full shop', 'editorial-starter' )
    ),
);

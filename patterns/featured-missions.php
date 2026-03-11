<?php
/**
 * Featured stories query pattern.
 *
 * @package EditorialStarter
 */

return array(
    'title'       => __( 'Featured Stories Grid', 'editorial-starter' ),
    'slug'        => 'editorial-starter/featured-stories-grid',
    'description' => __( 'Three-column featured post grid for homepages, magazines, and campaign landers.', 'editorial-starter' ),
    'categories'  => array( 'editorial-starter', 'editorial-starter-layouts' ),
    'keywords'    => array( __( 'featured', 'editorial-starter' ), __( 'query', 'editorial-starter' ), __( 'stories', 'editorial-starter' ) ),
    'content'     => sprintf(
        <<<'HTML'
<!-- wp:group {"className":"container section--grid"} -->
<div class="wp-block-group container section--grid">
    <!-- wp:heading {"level":2,"className":"section-heading"} -->
    <h2 class="wp-block-heading section-heading">%1$s</h2>
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
            <!-- wp:post-excerpt {"moreText":"%2$s","excerptLength":24} /-->
        </div>
        <!-- /wp:group -->
        <!-- /wp:post-template -->
        <!-- wp:query-no-results -->
        <!-- wp:paragraph -->
        <p>%3$s</p>
        <!-- /wp:paragraph -->
        <!-- /wp:query-no-results -->
    </div>
    <!-- /wp:query -->
</div>
<!-- /wp:group -->
HTML,
        esc_html__( 'Featured Stories', 'editorial-starter' ),
        esc_html__( 'Read story', 'editorial-starter' ),
        esc_html__( 'No featured stories yet. Add the `featured_post` meta key to surface priority content here.', 'editorial-starter' )
    ),
);

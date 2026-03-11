<?php
/**
 * Editorial Starter theme functions and definitions
 *
 * @package EditorialStarter
 */

if ( ! defined( 'EDITORIAL_STARTER_VERSION' ) ) {
    $editorial_starter_theme = wp_get_theme();
    $theme_version        = $editorial_starter_theme->get( 'Version' );

    define( 'EDITORIAL_STARTER_VERSION', $theme_version ? $theme_version : '1.0.0' );
}

if ( ! function_exists( 'editorial_starter_setup' ) ) :
    /**
     * Set up theme defaults and registers support for WordPress features.
     */
    function editorial_starter_setup() {
        load_theme_textdomain( 'editorial-starter', get_template_directory() . '/languages' );

        add_theme_support( 'automatic-feed-links' );
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'editor-styles' );
        add_editor_style(
            array(
                'style.css',
                'assets/css/theme.css',
            )
        );
        add_theme_support( 'wp-block-styles' );
        add_theme_support( 'align-wide' );
        add_theme_support( 'responsive-embeds' );
        add_theme_support( 'custom-spacing' );
        add_theme_support( 'custom-units', array( 'px', 'rem', 'vw', 'vh' ) );
        add_theme_support( 'block-template-parts' );

        add_theme_support(
            'editor-color-palette',
            array(
                array(
                    'name'  => __( 'Copper Accent', 'editorial-starter' ),
                    'slug'  => 'accent',
                    'color' => '#d0915d',
                ),
                array(
                    'name'  => __( 'Sage Accent', 'editorial-starter' ),
                    'slug'  => 'accent-strong',
                    'color' => '#7c9b92',
                ),
                array(
                    'name'  => __( 'Ink Surface', 'editorial-starter' ),
                    'slug'  => 'surface',
                    'color' => '#1d1917',
                ),
                array(
                    'name'  => __( 'Deep Ink', 'editorial-starter' ),
                    'slug'  => 'surface-strong',
                    'color' => '#14110f',
                ),
                array(
                    'name'  => __( 'Paper Text', 'editorial-starter' ),
                    'slug'  => 'text-primary',
                    'color' => '#f4efe6',
                ),
                array(
                    'name'  => __( 'Muted Text', 'editorial-starter' ),
                    'slug'  => 'text-secondary',
                    'color' => '#c1b5a7',
                ),
            )
        );

        add_theme_support(
            'editor-gradient-presets',
            array(
                array(
                    'name'     => __( 'Copper Wash', 'editorial-starter' ),
                    'gradient' => 'linear-gradient(135deg,#d0915d 0%,#7c9b92 100%)',
                    'slug'     => 'copper-wash',
                ),
                array(
                    'name'     => __( 'Ink Fade', 'editorial-starter' ),
                    'gradient' => 'linear-gradient(145deg,rgba(208,145,93,0.24) 0%,rgba(20,17,15,0.92) 100%)',
                    'slug'     => 'ink-fade',
                ),
            )
        );

        add_theme_support(
            'editor-font-sizes',
            array(
                array(
                    'name' => __( 'Small', 'editorial-starter' ),
                    'size' => 12,
                    'slug' => 'small',
                ),
                array(
                    'name' => __( 'Body', 'editorial-starter' ),
                    'size' => 16,
                    'slug' => 'body',
                ),
                array(
                    'name' => __( 'Lead', 'editorial-starter' ),
                    'size' => 22,
                    'slug' => 'lead',
                ),
                array(
                    'name' => __( 'Display', 'editorial-starter' ),
                    'size' => 32,
                    'slug' => 'display',
                ),
                array(
                    'name' => __( 'Poster', 'editorial-starter' ),
                    'size' => 48,
                    'slug' => 'poster',
                ),
            )
        );

        register_nav_menus(
            array(
                'primary' => __( 'Primary Menu', 'editorial-starter' ),
                'footer'  => __( 'Footer Menu', 'editorial-starter' ),
            )
        );

        add_theme_support(
            'html5',
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'style',
                'script',
            )
        );

        add_theme_support(
            'custom-logo',
            array(
                'height'      => 120,
                'width'       => 120,
                'flex-height' => true,
                'flex-width'  => true,
            )
        );

        add_theme_support( 'customize-selective-refresh-widgets' );
    }
endif;
add_action( 'after_setup_theme', 'editorial_starter_setup' );

/**
 * Register widget area.
 */
function editorial_starter_widgets_init() {
    register_sidebar(
        array(
            'name'          => __( 'Sidebar', 'editorial-starter' ),
            'id'            => 'sidebar-1',
            'description'   => __( 'Add widgets here to appear in your sidebar.', 'editorial-starter' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );

    register_sidebar(
        array(
            'name'          => __( 'Footer Widgets', 'editorial-starter' ),
            'id'            => 'sidebar-footer',
            'description'   => __( 'Widgets displayed in the footer.', 'editorial-starter' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}
add_action( 'widgets_init', 'editorial_starter_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function editorial_starter_scripts() {
    $theme_version = EDITORIAL_STARTER_VERSION;

    wp_enqueue_style(
        'editorial-starter-fonts',
        'https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Source+Serif+4:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap',
        array(),
        null
    );

    wp_enqueue_style( 'editorial-starter-style', get_stylesheet_uri(), array( 'editorial-starter-fonts' ), $theme_version );
    wp_enqueue_style( 'editorial-starter-theme', get_template_directory_uri() . '/assets/css/theme.css', array( 'editorial-starter-style' ), $theme_version );

    wp_enqueue_script(
        'editorial-starter-theme',
        get_template_directory_uri() . '/assets/js/theme.js',
        array(),
        $theme_version,
        array(
            'in_footer' => true,
            'strategy'  => 'defer',
        )
    );

    if ( editorial_starter_should_enqueue_shopify_products() ) {
        wp_enqueue_script(
            'editorial-starter-shopify-products',
            get_template_directory_uri() . '/assets/js/shopify-products.js',
            array(),
            $theme_version,
            array(
                'in_footer' => true,
                'strategy'  => 'defer',
            )
        );
    }

    wp_localize_script(
        'editorial-starter-theme',
        'editorialStarterNav',
        array(
            'expand'  => __( 'Expand submenu', 'editorial-starter' ),
            'collapse' => __( 'Collapse submenu', 'editorial-starter' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'editorial_starter_scripts' );

/**
 * Add resource hints for external font providers.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array
 */
function editorial_starter_resource_hints( $urls, $relation_type ) {
    if ( 'preconnect' === $relation_type ) {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = array(
            'href'        => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        );
    }

    return $urls;
}
add_filter( 'wp_resource_hints', 'editorial_starter_resource_hints', 10, 2 );

/**
 * Ensure threaded comments script is available when required.
 */
function editorial_starter_maybe_enqueue_comment_reply() {
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'editorial_starter_maybe_enqueue_comment_reply' );

/**
 * Determine whether the current request needs the Shopify products script.
 *
 * @return bool
 */
function editorial_starter_should_enqueue_shopify_products() {
    if ( is_admin() ) {
        return false;
    }

    if ( is_singular() ) {
        $post = get_queried_object();

        if ( $post instanceof WP_Post ) {
            return false !== strpos( (string) $post->post_content, 'data-shopify-products' );
        }
    }

    return false;
}

/**
 * Whether the primary CTA is enabled.
 *
 * @return bool
 */
function editorial_starter_is_primary_cta_enabled() {
    return (bool) get_theme_mod( 'editorial_starter_primary_cta_enabled', true );
}

/**
 * Get the primary CTA URL.
 *
 * @return string
 */
function editorial_starter_get_primary_cta_url() {
    $default_url = home_url( '/featured-story/' );
    $cta_url     = get_theme_mod( 'editorial_starter_primary_cta_url', $default_url );

    if ( empty( $cta_url ) ) {
        $cta_url = $default_url;
    }

    return esc_url( $cta_url );
}

/**
 * Get the primary CTA label.
 *
 * @return string
 */
function editorial_starter_get_primary_cta_label() {
    $fallback = __( 'Read the featured story', 'editorial-starter' );
    $label    = trim( (string) get_theme_mod( 'editorial_starter_primary_cta_label', $fallback ) );

    return '' !== $label ? $label : $fallback;
}

/**
 * Get the inline primary CTA heading.
 *
 * @return string
 */
function editorial_starter_get_primary_card_heading() {
    $fallback = __( 'Ready to launch your main story?', 'editorial-starter' );
    $heading  = trim( (string) get_theme_mod( 'editorial_starter_primary_card_heading', $fallback ) );

    return '' !== $heading ? $heading : $fallback;
}

/**
 * Get the inline primary CTA description.
 *
 * @return string
 */
function editorial_starter_get_primary_card_description() {
    $fallback    = __( 'Point this section at a flagship article, service page, product detail, or campaign landing page.', 'editorial-starter' );
    $description = trim( (string) get_theme_mod( 'editorial_starter_primary_card_description', $fallback ) );

    return '' !== $description ? $description : $fallback;
}

/**
 * Get the newsletter CTA label.
 *
 * @return string
 */
function editorial_starter_get_secondary_cta_label() {
    $fallback = __( 'Join the newsletter', 'editorial-starter' );
    $label    = trim( (string) get_theme_mod( 'editorial_starter_secondary_cta_label', $fallback ) );

    return '' !== $label ? $label : $fallback;
}

/**
 * Get the newsletter CTA anchor URL.
 *
 * @return string
 */
function editorial_starter_get_subscribe_cta_url() {
    return esc_url( home_url( '/#newsletter-signup' ) );
}

/**
 * Helper: build published and updated date meta pills.
 */
function editorial_starter_get_posted_on_markup() {
    $published = get_the_date();

    if ( ! $published ) {
        return '';
    }

    $markup  = sprintf(
        '<span class="meta-pill meta-pill--date"><span class="meta-pill__label">%1$s</span><time class="meta-pill__value entry-date published" datetime="%2$s">%3$s</time></span>',
        esc_html__( 'Published', 'editorial-starter' ),
        esc_attr( get_the_date( DATE_W3C ) ),
        esc_html( $published )
    );

    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
        $markup .= sprintf(
            '<span class="meta-pill meta-pill--updated"><span class="meta-pill__label">%1$s</span><time class="meta-pill__value updated" datetime="%2$s">%3$s</time></span>',
            esc_html__( 'Updated', 'editorial-starter' ),
            esc_attr( get_the_modified_date( DATE_W3C ) ),
            esc_html( get_the_modified_date() )
        );
    }

    return $markup;
}

/**
 * Helper: build author meta pill markup.
 */
function editorial_starter_get_posted_by_markup() {
    if ( 'post' !== get_post_type() ) {
        return '';
    }

    return sprintf(
        '<span class="meta-pill meta-pill--author"><span class="meta-pill__label">%1$s</span><a class="meta-pill__value" href="%2$s">%3$s</a></span>',
        esc_html__( 'Author', 'editorial-starter' ),
        esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
        esc_html( get_the_author() )
    );
}

/**
 * Helper: return an estimated reading-time pill.
 */
function editorial_starter_get_estimated_reading_time_markup() {
    if ( 'post' !== get_post_type() ) {
        return '';
    }

    $content   = get_post_field( 'post_content', get_the_ID() );
    $wordcount = str_word_count( wp_strip_all_tags( $content ) );

    if ( $wordcount < 1 ) {
        return '';
    }

    $words_per_minute = (int) apply_filters( 'editorial_starter_reading_words_per_minute', 225 );

    if ( $words_per_minute <= 0 ) {
        $words_per_minute = 225;
    }

    $minutes = max( 1, (int) ceil( $wordcount / $words_per_minute ) );
    $label   = sprintf(
        /* translators: %s: the estimated minutes to read a post. */
        _n( '%s minute read', '%s minutes read', $minutes, 'editorial-starter' ),
        number_format_i18n( $minutes )
    );

    return sprintf( '<span class="meta-pill meta-pill--reading">%s</span>', esc_html( $label ) );
}

/**
 * Helper: display category taxonomy chips.
 */
function editorial_starter_get_entry_taxonomy_markup() {
    if ( 'post' !== get_post_type() ) {
        return '';
    }

    $terms = get_the_terms( get_the_ID(), 'category' );

    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        return '';
    }

    $chips = array();

    foreach ( $terms as $term ) {
        $term_link = get_term_link( $term );

        if ( is_wp_error( $term_link ) ) {
            continue;
        }

        $chips[] = sprintf(
            '<a class="taxonomy-chip" href="%1$s">%2$s</a>',
            esc_url( $term_link ),
            esc_html( $term->name )
        );
    }

    if ( empty( $chips ) ) {
        return '';
    }

    return sprintf(
        '<div class="entry-hero__taxonomy" aria-label="%1$s">%2$s</div>',
        esc_attr__( 'Categories', 'editorial-starter' ),
        implode( '', $chips )
    );
}

/**
 * Helper: build entry footer markup for tags and edit links.
 */
function editorial_starter_get_entry_footer_markup() {
    $output    = '';
    $post_type = get_post_type();

    if ( 'post' === $post_type ) {
        $tags = get_the_tags();

        if ( $tags ) {
            $tag_links = array();

            foreach ( $tags as $tag ) {
                $tag_link = get_tag_link( $tag->term_id );

                if ( is_wp_error( $tag_link ) ) {
                    continue;
                }

                $tag_links[] = sprintf(
                    '<a class="taxonomy-chip taxonomy-chip--tag" href="%1$s">%2$s</a>',
                    esc_url( $tag_link ),
                    esc_html( $tag->name )
                );
            }

            if ( ! empty( $tag_links ) ) {
                $output .= sprintf(
                    '<div class="entry-footer__tags" aria-label="%1$s">%2$s</div>',
                    esc_attr__( 'Tags', 'editorial-starter' ),
                    implode( '', $tag_links )
                );
            }
        }
    }

    $edit_link = get_edit_post_link( get_the_ID() );

    if ( $edit_link ) {
        $output .= sprintf(
            '<a class="meta-pill meta-pill--edit" href="%1$s">%2$s</a>',
            esc_url( $edit_link ),
            esc_html__( 'Edit this entry', 'editorial-starter' )
        );
    }

    return trim( $output );
}

if ( ! function_exists( 'editorial_starter_get_menu_toggle_icon_markup' ) ) {
    /**
     * Retrieve the markup for the mobile menu toggle icon.
     *
     * Prefers the site icon when one is configured and falls back to a
     * custom glyph so the toggle always has a visual anchor.
     *
     * @return string
     */
    function editorial_starter_get_menu_toggle_icon_markup() {
        $icon_markup = '';

        if ( has_site_icon() ) {
            $icon_url = get_site_icon_url( 96 );

            if ( $icon_url ) {
                $icon_markup = sprintf(
                    '<img src="%1$s" alt="" class="menu-toggle__icon-image" loading="lazy" decoding="async" />',
                    esc_url( $icon_url )
                );
            }
        }

        if ( ! $icon_markup ) {
            $icon_markup = '<svg class="menu-toggle__icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" /></svg>';
        }

        /**
         * Filter the mobile menu toggle icon markup.
         *
         * @since 1.0.0
         *
         * @param string $icon_markup Icon HTML.
         */
        return apply_filters( 'editorial_starter_menu_toggle_icon_markup', $icon_markup );
    }
}

/**
 * Include additional PHP files.
 */
require get_template_directory() . '/inc/seo.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/patterns.php';

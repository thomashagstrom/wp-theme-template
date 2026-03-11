<?php
/**
 * SEO helpers and meta output for Editorial Starter.
 *
 * @package EditorialStarter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'editorial_starter_get_default_meta_description' ) ) {
    /**
     * Default homepage meta description text.
     *
     * @return string
     */
    function editorial_starter_get_default_meta_description() {
        return __( 'Editorial Starter is a flexible WordPress theme for publishers, campaigns, and product storytelling sites.', 'editorial-starter' );
    }
}

if ( ! function_exists( 'editorial_starter_get_meta_description' ) ) {
    /**
     * Resolve the best available meta description for the current request.
     *
     * @return string
     */
    function editorial_starter_get_meta_description() {
        $description = '';

        if ( is_singular() ) {
            $post = get_post();

            if ( $post ) {
                if ( has_excerpt( $post ) ) {
                    $description = $post->post_excerpt;
                } else {
                    $description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 32, '…' );
                }
            }
        } elseif ( is_category() || is_tag() || is_tax() ) {
            $term = get_queried_object();

            if ( $term && ! is_wp_error( $term ) && ! empty( $term->description ) ) {
                $description = $term->description;
            }
        }

        if ( '' === $description ) {
            if ( is_front_page() || is_home() ) {
                $description = get_theme_mod( 'editorial_starter_meta_description', editorial_starter_get_default_meta_description() );
            } else {
                $description = get_bloginfo( 'description', 'display' );
            }
        }

        $description = apply_filters( 'editorial_starter_meta_description', $description );

        return trim( wp_strip_all_tags( $description ) );
    }
}

if ( ! function_exists( 'editorial_starter_get_primary_image_data' ) ) {
    /**
     * Get the best social image for the current request.
     *
     * @return array<int, int|string>|null
     */
    function editorial_starter_get_primary_image_data() {
        if ( is_singular() && has_post_thumbnail() ) {
            $image_data = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

            if ( $image_data ) {
                return $image_data;
            }
        }

        if ( has_site_icon() ) {
            $site_icon_id = (int) get_option( 'site_icon' );

            if ( $site_icon_id > 0 ) {
                $image_data = wp_get_attachment_image_src( $site_icon_id, 'full' );

                if ( $image_data ) {
                    return $image_data;
                }
            }
        }

        return null;
    }
}

if ( ! function_exists( 'editorial_starter_get_organization_schema' ) ) {
    /**
     * Build organization schema data.
     *
     * @return array<string, mixed>
     */
    function editorial_starter_get_organization_schema() {
        $organization = array(
            '@id'   => home_url( '/#organization' ),
            '@type' => 'Organization',
            'name'  => get_bloginfo( 'name' ),
            'url'   => home_url( '/' ),
        );

        if ( has_site_icon() ) {
            $organization['logo'] = array(
                '@type' => 'ImageObject',
                'url'   => esc_url_raw( get_site_icon_url( 512 ) ),
            );
        }

        if ( function_exists( 'editorial_starter_get_social_links' ) ) {
            $social_links = editorial_starter_get_social_links();

            if ( ! empty( $social_links ) ) {
                $organization['sameAs'] = array_values(
                    array_filter(
                        array_map(
                            static function ( $link ) {
                                return isset( $link['url'] ) ? esc_url_raw( $link['url'] ) : '';
                            },
                            $social_links
                        )
                    )
                );
            }
        }

        return $organization;
    }
}

if ( ! function_exists( 'editorial_starter_get_breadcrumb_list_schema' ) ) {
    /**
     * Build breadcrumb schema for current request.
     *
     * @return array<string, mixed>|array<string, never>
     */
    function editorial_starter_get_breadcrumb_list_schema() {
        $items   = array();
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => get_bloginfo( 'name' ),
            'item'     => home_url( '/' ),
        );

        if ( is_singular( 'post' ) ) {
            $categories = get_the_category();

            if ( ! empty( $categories ) ) {
                $items[] = array(
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => $categories[0]->name,
                    'item'     => get_category_link( $categories[0] ),
                );
            }

            $items[] = array(
                '@type'    => 'ListItem',
                'position' => count( $items ) + 1,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            );
        } elseif ( is_singular() ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => 2,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            );
        } elseif ( is_category() || is_tag() || is_tax() ) {
            $term = get_queried_object();

            if ( $term && ! is_wp_error( $term ) ) {
                $term_link = get_term_link( $term );

                if ( is_wp_error( $term_link ) ) {
                    return array();
                }

                $items[] = array(
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => $term->name,
                    'item'     => $term_link,
                );
            }
        } elseif ( is_search() ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => 2,
                'name'     => sprintf(
                    /* translators: %s: search query */
                    __( 'Search: %s', 'editorial-starter' ),
                    get_search_query()
                ),
                'item'     => get_search_link(),
            );
        } elseif ( is_home() ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => 2,
                'name'     => get_the_title( (int) get_option( 'page_for_posts' ) )
                    ? get_the_title( (int) get_option( 'page_for_posts' ) )
                    : __( 'Blog', 'editorial-starter' ),
                'item'     => home_url( '/' ),
            );
        } elseif ( is_archive() ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => 2,
                'name'     => wp_get_document_title(),
                'item'     => get_pagenum_link( 1 ),
            );
        }

        if ( count( $items ) < 2 ) {
            return array();
        }

        return array(
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        );
    }
}

if ( ! function_exists( 'editorial_starter_get_article_schema' ) ) {
    /**
     * Build article schema for singular posts.
     *
     * @param string $canonical Canonical URL.
     * @param string $description Meta description.
     * @param array<string, mixed> $organization Organization schema.
     * @param array<int, int|string>|null $image_data Image data.
     * @return array<string, mixed>|array<string, never>
     */
    function editorial_starter_get_article_schema( $canonical, $description, $organization, $image_data ) {
        if ( ! is_singular( 'post' ) ) {
            return array();
        }

        $article_schema = array(
            '@type'            => 'Article',
            'headline'         => get_the_title(),
            'mainEntityOfPage' => $canonical,
            'url'              => $canonical,
            'description'      => $description,
            'datePublished'    => get_the_date( DATE_W3C ),
            'dateModified'     => get_the_modified_date( DATE_W3C ),
            'inLanguage'       => get_bloginfo( 'language' ),
            'author'           => array(
                '@type' => 'Person',
                'name'  => get_the_author(),
                'url'   => get_author_posts_url( (int) get_the_author_meta( 'ID' ) ),
            ),
            'publisher'        => array(
                '@id' => $organization['@id'],
            ),
        );

        if ( $image_data ) {
            $article_schema['image'] = array(
                '@type'  => 'ImageObject',
                'url'    => $image_data[0],
                'width'  => isset( $image_data[1] ) ? (int) $image_data[1] : null,
                'height' => isset( $image_data[2] ) ? (int) $image_data[2] : null,
            );
        }

        return $article_schema;
    }
}

if ( ! function_exists( 'editorial_starter_output_seo_meta' ) ) {
    /**
     * Output SEO, social, and schema tags.
     *
     * @return void
     */
    function editorial_starter_output_seo_meta() {
        if ( is_admin() ) {
            return;
        }

        $description = editorial_starter_get_meta_description();
        $title       = wp_get_document_title();
        $canonical   = wp_get_canonical_url();

        if ( ! $canonical ) {
            if ( is_front_page() ) {
                $canonical = home_url( '/' );
            } elseif ( is_home() ) {
                $page_for_posts = (int) get_option( 'page_for_posts' );
                $canonical      = $page_for_posts ? get_permalink( $page_for_posts ) : home_url( '/' );
            } elseif ( is_singular() ) {
                $canonical = get_permalink();
            }
        }

        $canonical = $canonical ? $canonical : home_url( '/' );

        $og_type   = is_singular() ? 'article' : 'website';
        $site_name = get_bloginfo( 'name' );
        $locale    = str_replace( '_', '-', get_locale() );

        $image_data = editorial_starter_get_primary_image_data();

        if ( $description ) {
            echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
        }

        echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";

        if ( $description ) {
            echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
        }

        echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url( $canonical ) . '" />' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
        echo '<meta property="og:locale" content="' . esc_attr( $locale ) . '" />' . "\n";

        if ( is_singular( 'post' ) ) {
            echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( DATE_W3C ) ) . '" />' . "\n";
            echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( DATE_W3C ) ) . '" />' . "\n";
            echo '<meta property="article:author" content="' . esc_attr( get_the_author() ) . '" />' . "\n";
        }

        if ( $image_data ) {
            echo '<meta property="og:image" content="' . esc_url( $image_data[0] ) . '" />' . "\n";

            if ( ! empty( $image_data[1] ) && ! empty( $image_data[2] ) ) {
                echo '<meta property="og:image:width" content="' . esc_attr( (string) (int) $image_data[1] ) . '" />' . "\n";
                echo '<meta property="og:image:height" content="' . esc_attr( (string) (int) $image_data[2] ) . '" />' . "\n";
            }
        }

        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";

        if ( $description ) {
            echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
        }

        if ( $image_data ) {
            echo '<meta name="twitter:image" content="' . esc_url( $image_data[0] ) . '" />' . "\n";
        }

        $organization_schema = editorial_starter_get_organization_schema();

        $schema_graph = array(
            array(
                '@id'             => home_url( '/#website' ),
                '@type'           => 'WebSite',
                'url'             => home_url( '/' ),
                'name'            => $site_name,
                'description'     => $description,
                'inLanguage'      => get_bloginfo( 'language' ),
                'publisher'       => array(
                    '@id' => $organization_schema['@id'],
                ),
                'potentialAction' => array(
                    '@type'       => 'SearchAction',
                    'target'      => home_url( '?s={search_term_string}' ),
                    'query-input' => 'required name=search_term_string',
                ),
            ),
            $organization_schema,
        );

        $article_schema = editorial_starter_get_article_schema( $canonical, $description, $organization_schema, $image_data );

        if ( ! empty( $article_schema ) ) {
            $schema_graph[] = $article_schema;
        }

        $breadcrumb_schema = editorial_starter_get_breadcrumb_list_schema();

        if ( ! empty( $breadcrumb_schema ) ) {
            $schema_graph[] = $breadcrumb_schema;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@graph'   => $schema_graph,
        );

        echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'editorial_starter_output_seo_meta', 5 );

if ( ! function_exists( 'editorial_starter_robots_directives' ) ) {
    /**
     * Set indexing directives by context.
     *
     * @param array<string, bool> $robots Existing robots directives.
     * @return array<string, bool>
     */
    function editorial_starter_robots_directives( $robots ) {
        if ( is_search() || is_404() ) {
            $robots['noindex'] = true;
            $robots['follow']  = true;
            unset( $robots['index'] );
        }

        return $robots;
    }
}
add_filter( 'wp_robots', 'editorial_starter_robots_directives' );

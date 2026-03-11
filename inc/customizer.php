<?php
/**
 * Customizer additions for Editorial Starter.
 *
 * @package EditorialStarter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Neon_Chronicle_Social_Links_Control' ) ) {
    /**
     * Custom repeater control for managing footer social links.
     */
    class Neon_Chronicle_Social_Links_Control extends WP_Customize_Control {
        /**
         * Control type.
         *
         * @var string
         */
        public $type = 'editorial-starter-social-links';

        /**
         * Enqueue control scripts and styles.
         */
        public function enqueue() {
            wp_enqueue_script(
                'editorial-starter-customizer-social-links',
                get_template_directory_uri() . '/assets/js/customizer-social-links.js',
                array( 'jquery', 'customize-controls' ),
                defined( 'EDITORIAL_STARTER_VERSION' ) ? EDITORIAL_STARTER_VERSION : false,
                true
            );

            wp_enqueue_style(
                'editorial-starter-customizer-social-links',
                get_template_directory_uri() . '/assets/css/customizer-social-links.css',
                array(),
                defined( 'EDITORIAL_STARTER_VERSION' ) ? EDITORIAL_STARTER_VERSION : false
            );
        }

        /**
         * Render the control content.
         */
        public function render_content() {
            $value = $this->value();

            if ( is_string( $value ) ) {
                $value = wp_unslash( $value );
            }

            $links = json_decode( $value, true );

            if ( ! is_array( $links ) ) {
                $links = editorial_starter_get_default_social_links();
            }

            $encoded_value = wp_json_encode( $links );
            $display_links = ! empty( $links ) ? $links : array(
                array(
                    'label' => '',
                    'url'   => '',
                    'icon'  => '',
                ),
            );

            if ( ! empty( $this->label ) ) {
                echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
            }

            if ( ! empty( $this->description ) ) {
                echo '<span class="description customize-control-description">' . wp_kses_post( $this->description ) . '</span>';
            }

            ?>
            <div class="editorial-starter-social-links" data-control="<?php echo esc_attr( $this->id ); ?>">
                <ul class="editorial-starter-social-links-list">
                    <?php
                    foreach ( $display_links as $link ) {
                        echo $this->get_link_row_markup( $link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    }
                    ?>
                </ul>

                <button type="button" class="button add-social-link"><?php esc_html_e( 'Add link', 'editorial-starter' ); ?></button>

                <input type="hidden" class="editorial-starter-social-input" <?php $this->link(); ?> value='<?php echo esc_attr( $encoded_value ); ?>' />

                <script type="text/html" class="editorial-starter-social-link-template">
                    <?php echo $this->get_link_row_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </script>
            </div>
            <?php
        }

        /**
         * Get markup for a single social link row.
         *
         * @param array $link Social link data.
         * @return string
         */
        protected function get_link_row_markup( $link = array() ) {
            $link = wp_parse_args(
                $link,
                array(
                    'label' => '',
                    'url'   => '',
                    'icon'  => '',
                )
            );

            ob_start();
            ?>
            <li class="editorial-starter-social-link">
                <div class="editorial-starter-social-field">
                    <label>
                        <span class="editorial-starter-social-field-title"><?php esc_html_e( 'Label', 'editorial-starter' ); ?></span>
                        <input type="text" class="editorial-starter-social-label" value="<?php echo esc_attr( $link['label'] ); ?>" placeholder="<?php esc_attr_e( 'Display name', 'editorial-starter' ); ?>" />
                    </label>
                </div>
                <div class="editorial-starter-social-field">
                    <label>
                        <span class="editorial-starter-social-field-title"><?php esc_html_e( 'URL', 'editorial-starter' ); ?></span>
                        <input type="url" class="editorial-starter-social-url" value="<?php echo esc_attr( $link['url'] ); ?>" placeholder="https://" />
                    </label>
                </div>
                <div class="editorial-starter-social-field">
                    <label>
                        <span class="editorial-starter-social-field-title"><?php esc_html_e( 'Badge text', 'editorial-starter' ); ?></span>
                        <input type="text" class="editorial-starter-social-icon" value="<?php echo esc_attr( $link['icon'] ); ?>" placeholder="<?php esc_attr_e( 'Optional', 'editorial-starter' ); ?>" />
                    </label>
                </div>
                <button type="button" class="button-link delete editorial-starter-remove-social-link">
                    <?php esc_html_e( 'Remove', 'editorial-starter' ); ?>
                </button>
            </li>
            <?php
            return (string) ob_get_clean();
        }
    }
}

if ( ! function_exists( 'editorial_starter_get_default_social_links' ) ) {
    /**
     * Default footer social links.
     *
     * @return array
     */
    function editorial_starter_get_default_social_links() {
        return array(
            array(
                'label' => __( 'Instagram', 'editorial-starter' ),
                'url'   => 'https://www.instagram.com/',
                'icon'  => 'IG',
            ),
            array(
                'label' => __( 'X (Twitter)', 'editorial-starter' ),
                'url'   => 'https://www.twitter.com/',
                'icon'  => 'X',
            ),
            array(
                'label' => __( 'Email', 'editorial-starter' ),
                'url'   => 'mailto:hello@example.com',
                'icon'  => '@',
            ),
        );
    }
}

if ( ! function_exists( 'editorial_starter_sanitize_social_links' ) ) {
    /**
     * Sanitize social links payload from the Customizer control.
     *
     * @param mixed $value Raw value.
     * @return string
     */
    function editorial_starter_sanitize_social_links( $value ) {
        if ( empty( $value ) ) {
            return wp_json_encode( array() );
        }

        if ( is_array( $value ) ) {
            $value = wp_json_encode( $value );
        }

        $decoded = json_decode( wp_unslash( (string) $value ), true );

        if ( ! is_array( $decoded ) ) {
            return wp_json_encode( array() );
        }

        $sanitized = array();

        foreach ( $decoded as $link ) {
            $label = isset( $link['label'] ) ? sanitize_text_field( $link['label'] ) : '';
            $url   = isset( $link['url'] ) ? esc_url_raw( $link['url'] ) : '';
            $icon  = isset( $link['icon'] ) ? sanitize_text_field( $link['icon'] ) : '';

            if ( empty( $label ) && empty( $url ) && empty( $icon ) ) {
                continue;
            }

            $sanitized[] = array(
                'label' => $label,
                'url'   => $url,
                'icon'  => $icon,
            );
        }

        return wp_json_encode( $sanitized );
    }
}

if ( ! function_exists( 'editorial_starter_get_social_links' ) ) {
    /**
     * Retrieve sanitized social links for display.
     *
     * @return array
     */
    function editorial_starter_get_social_links() {
        $raw = get_theme_mod(
            'editorial_starter_footer_social_links',
            wp_json_encode( editorial_starter_get_default_social_links() )
        );

        $decoded = json_decode( (string) $raw, true );

        if ( ! is_array( $decoded ) ) {
            return array();
        }

        $links = array();

        foreach ( $decoded as $link ) {
            $label = isset( $link['label'] ) ? sanitize_text_field( $link['label'] ) : '';
            $url   = isset( $link['url'] ) ? esc_url( $link['url'] ) : '';
            $icon  = isset( $link['icon'] ) ? sanitize_text_field( $link['icon'] ) : '';

            if ( empty( $label ) || empty( $url ) ) {
                continue;
            }

            $links[] = array(
                'label' => $label,
                'url'   => $url,
                'icon'  => $icon,
            );
        }

        return $links;
    }
}

if ( ! function_exists( 'editorial_starter_get_social_icon_display' ) ) {
    /**
     * Determine the icon or fallback badge for a social link.
     *
     * @param string $label Social network label.
     * @param string $icon  Optional custom badge text supplied via the Customizer.
     * @param string $url   Social link URL.
     * @return array{
     *     content: string,
     *     is_svg: bool,
     * }
     */
    function editorial_starter_get_social_icon_display( $label, $icon = '', $url = '' ) {
        $icon = trim( (string) $icon );

        if ( '' !== $icon ) {
            return array(
                'content' => strtoupper( $icon ),
                'is_svg'  => false,
            );
        }

        $service = editorial_starter_detect_social_service( $url );

        if ( $service ) {
            $svg = editorial_starter_get_social_icon_svg( $service );

            if ( $svg ) {
                return array(
                    'content' => $svg,
                    'is_svg'  => true,
                );
            }
        }

        $label = trim( (string) $label );

        if ( '' === $label ) {
            return array(
                'content' => '',
                'is_svg'  => false,
            );
        }

        $words = preg_split( '/\s+/', $label );

        if ( $words && count( $words ) > 1 ) {
            $first = strtoupper( editorial_starter_mb_substr_safe( $words[0], 0, 1 ) );
            $last  = strtoupper( editorial_starter_mb_substr_safe( end( $words ), 0, 1 ) );

            return array(
                'content' => $first . $last,
                'is_svg'  => false,
            );
        }

        return array(
            'content' => strtoupper( editorial_starter_mb_substr_safe( $label, 0, 2 ) ),
            'is_svg'  => false,
        );
    }
}

if ( ! function_exists( 'editorial_starter_detect_social_service' ) ) {
    /**
     * Attempt to detect the social platform represented by a URL.
     *
     * @param string $url Link URL.
     * @return string Detected service slug or empty string if none matched.
     */
    function editorial_starter_detect_social_service( $url ) {
        $url = trim( (string) $url );

        if ( '' === $url ) {
            return '';
        }

        $parsed = wp_parse_url( $url );

        if ( ! $parsed || empty( $parsed['host'] ) ) {
            return '';
        }

        $host = strtolower( $parsed['host'] );
        $host = preg_replace( '/^www\./', '', $host );

        $map = apply_filters(
            'editorial_starter_social_service_map',
            array(
                'instagram' => array( 'instagram.com' ),
                'youtube'   => array( 'youtube.com', 'youtu.be' ),
                'tiktok'    => array( 'tiktok.com' ),
                'facebook'  => array( 'facebook.com', 'fb.com' ),
                'x'         => array( 'x.com', 'twitter.com' ),
                'threads'   => array( 'threads.net' ),
                'linkedin'  => array( 'linkedin.com' ),
                'twitch'    => array( 'twitch.tv' ),
                'spotify'   => array( 'spotify.com' ),
                'bandcamp'  => array( 'bandcamp.com' ),
                'patreon'   => array( 'patreon.com' ),
                'goodreads' => array( 'goodreads.com' ),
                'pinterest' => array( 'pinterest.com' ),
            )
        );

        foreach ( $map as $service => $domains ) {
            foreach ( (array) $domains as $domain ) {
                $domain = strtolower( trim( (string) $domain ) );

                if ( '' === $domain ) {
                    continue;
                }

                $length = strlen( $domain );

                if ( $length > 0 && substr( $host, -$length ) === $domain ) {
                    return $service;
                }
            }
        }

        return '';
    }
}

if ( ! function_exists( 'editorial_starter_get_social_icon_svg' ) ) {
    /**
     * Retrieve inline SVG markup for a supported social platform.
     *
     * @param string $service Service slug as returned by editorial_starter_detect_social_service().
     * @return string
     */
    function editorial_starter_get_social_icon_svg( $service ) {
        $service = strtolower( (string) $service );

        $icons = apply_filters(
            'editorial_starter_social_icons',
            array(
                'instagram' => '<svg class="footer-social__icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path fill="currentColor" d="M7.03.084C5.753.144 4.881.347 4.12.647c-.789.307-1.458.72-2.123 1.388C1.332 2.703.922 3.372.616 4.162c-.296.764-.496 1.637-.552 2.915C.009 8.354-.004 8.764.002 12.023c.006 3.259.02 3.667.082 4.947.061 1.276.264 2.148.564 2.91.308.79.72 1.457 1.388 2.123.668.666 1.337 1.075 2.129 1.38.763.295 1.636.496 2.913.552 1.277.056 1.688.069 4.946.063 3.258-.006 3.668-.021 4.948-.082 1.28-.061 2.147-.265 2.91-.563.789-.309 1.458-.72 2.123-1.388.665-.668 1.075-1.338 1.38-2.128.296-.763.497-1.636.552-2.913.056-1.281.07-1.69.063-4.948-.006-3.258-.021-3.667-.082-4.947-.06-1.28-.264-2.149-.563-2.912-.309-.789-.72-1.457-1.388-2.123C21.298 1.33 20.629.921 19.838.617c-.764-.296-1.636-.497-2.914-.552C15.647.009 15.236-.005 11.977.002 8.718.008 8.31.022 7.03.084Zm.14 21.693c-1.17-.051-1.805-.245-2.229-.408-.56-.216-.96-.477-1.382-.895-.422-.418-.681-.819-.9-1.378-.164-.424-.362-1.058-.417-2.228-.06-1.265-.072-1.645-.07-4.849-.007-3.204.005-3.583.06-4.848.05-1.169.246-1.805.408-2.228.216-.561.476-.96.895-1.382.419-.422.818-.681 1.378-.9.423-.165 1.058-.361 2.227-.417 1.266-.06 1.645-.072 4.848-.079 3.204-.007 3.584.005 4.85.061 1.169.051 1.805.245 2.229.408.56.216.96.476 1.381.895.422.42.682.818.9 1.379.165.422.362 1.056.417 2.226.06 1.265.074 1.645.08 4.848.006 3.203-.005 3.583-.061 4.848-.05 1.17-.245 1.806-.408 2.229-.216.56-.476.96-.895 1.382-.419.422-.818.681-1.378.9-.422.165-1.058.362-2.226.417-1.266.06-1.645.072-4.85.079-3.204.007-3.582-.006-4.848-.061Zm9.783-16.19a1.44 1.44 0 1 0 1.437-1.442 1.44 1.44 0 0 0-1.437 1.442ZM5.839 12.012c.007 3.403 2.77 6.156 6.173 6.15 3.403-.007 6.157-2.77 6.15-6.173-.007-3.403-2.771-6.157-6.174-6.15-3.403.007-6.156 2.771-6.15 6.174Zm2.162-.004a4 4 0 1 1 4.008 3.992 4 4 0 0 1-4.008-3.992Z"/></svg>',
                'youtube'   => '<svg class="footer-social__icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path fill="currentColor" d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814ZM9.545 15.568V8.432L15.818 12Z"/></svg>',
                'tiktok'    => '<svg class="footer-social__icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path fill="currentColor" d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07Z"/></svg>',
                'goodreads' => '<svg class="footer-social__icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path fill="currentColor" d="M12.057 18.668c-3.46 0-4.458-3.222-4.458-6.198 0-3.651 1.63-6.18 4.434-6.18 2.567 0 4.277 2.337 4.277 5.994 0 4.401-1.928 6.384-4.253 6.384Zm7.95-18.668v15.72c0 1.143.051 2.286.205 3.43.047.36.104.72.17 1.08h-1.43l-.242-1.383c-.945 1.068-2.438 1.734-4.37 1.734-3.866 0-6.914-3.092-6.914-9.174 0-5.622 2.516-9.428 6.752-9.428 1.95 0 3.44.703 4.395 1.828l.242-1.807zm-3.181 10.506c0-3.407-1.185-5.556-3.723-5.556-2.55 0-3.938 2.1-3.938 5.702 0 3.966 1.355 6.02 3.91 6.02 2.716 0 3.752-2.41 3.752-6.166Zm-4.666 8.196c1.55 0 2.567-.806 3.094-1.526-.078-.43-.124-.88-.17-1.32-.422.586-1.61 1.385-2.924 1.385-2.515 0-3.854-2.075-3.854-5.98 0-3.506 1.3-5.525 3.77-5.525 1.66 0 2.65.963 3.008 1.75v-1.642h1.61v11.18c0 2.372-1.97 4.22-4.534 4.22-1.289 0-2.887-.457-3.76-1.123l.391-1.276c.817.508 1.957.857 3.359.857Z"/></svg>',
                'pinterest' => '<svg class="footer-social__icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path fill="currentColor" d="M12 0C5.373 0 0 5.372 0 12c0 4.99 3.657 9.128 8.438 10.125-.117-.857-.223-2.172.047-3.107.242-.83 1.557-5.3 1.557-5.3s-.397-.793-.397-1.963c0-1.838 1.067-3.214 2.394-3.214 1.13 0 1.674.849 1.674 1.866 0 1.136-.723 2.838-1.096 4.417-.312 1.319.663 2.395 1.963 2.395 2.355 0 3.936-3.028 3.936-6.608 0-2.727-1.838-4.771-5.183-4.771-3.772 0-6.135 2.807-6.135 5.938 0 1.081.312 1.841.802 2.428.225.266.257.373.175.679-.059.223-.194.761-.25.974-.082.31-.334.421-.615.307-1.716-.7-2.506-2.58-2.506-4.696 0-3.492 2.944-7.676 8.772-7.676 4.687 0 7.781 3.394 7.781 7.037 0 4.819-2.685 8.424-6.645 8.424-1.329 0-2.577-.718-3.005-1.533l-.815 3.104c-.297 1.116-1.105 2.512-1.65 3.367 1.245.384 2.56.592 3.932.592 6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>',
            )
        );

        return isset( $icons[ $service ] ) ? $icons[ $service ] : '';
    }
}

if ( ! function_exists( 'editorial_starter_mb_substr_safe' ) ) {
    /**
     * Multibyte-safe substring helper with ASCII fallback.
     *
     * @param string $string Input string.
     * @param int    $start  Starting index.
     * @param int    $length Length.
     * @return string
     */
    function editorial_starter_mb_substr_safe( $string, $start, $length ) {
        if ( function_exists( 'mb_substr' ) ) {
            return mb_substr( $string, $start, $length );
        }

        return substr( $string, $start, $length );
    }
}

/**
 * Register Customizer options.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function editorial_starter_customize_register( $wp_customize ) {
    $wp_customize->add_section(
        'editorial_starter_hero',
        array(
            'title'       => __( 'Hero Banner', 'editorial-starter' ),
            'description' => __( 'Tune the homepage hero headline, supporting copy, and lead action.', 'editorial-starter' ),
            'priority'    => 30,
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_hero_headline',
        array(
            'default'           => __( 'Independent Ideas', 'editorial-starter' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'editorial_starter_hero_headline',
        array(
            'label'       => __( 'Hero headline (first line)', 'editorial-starter' ),
            'description' => __( 'Displayed as the first line in the homepage hero.', 'editorial-starter' ),
            'section'     => 'editorial_starter_hero',
            'type'        => 'text',
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_hero_subheadline',
        array(
            'default'           => __( 'Ready to Launch', 'editorial-starter' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'editorial_starter_hero_subheadline',
        array(
            'label'   => __( 'Hero headline (second line)', 'editorial-starter' ),
            'section' => 'editorial_starter_hero',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_hero_tagline',
        array(
            'default'           => __( 'Editorial Starter', 'editorial-starter' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'editorial_starter_hero_tagline',
        array(
            'label'       => __( 'Hero tagline', 'editorial-starter' ),
            'description' => __( 'Displayed beneath the main headline.', 'editorial-starter' ),
            'section'     => 'editorial_starter_hero',
            'type'        => 'text',
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_hero_description',
        array(
            'default'           => __( 'Use this hero to frame your flagship story, campaign, or product launch with one clear message and one clear next step.', 'editorial-starter' ),
            'sanitize_callback' => 'wp_kses_post',
        )
    );

    $wp_customize->add_control(
        'editorial_starter_hero_description',
        array(
            'label'       => __( 'Hero description', 'editorial-starter' ),
            'type'        => 'textarea',
            'section'     => 'editorial_starter_hero',
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_hero_button_label',
        array(
            'default'           => __( 'Browse latest posts', 'editorial-starter' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'editorial_starter_hero_button_label',
        array(
            'label'   => __( 'Primary button text', 'editorial-starter' ),
            'section' => 'editorial_starter_hero',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_hero_button_url',
        array(
            'default'           => '#latest-heading',
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        'editorial_starter_hero_button_url',
        array(
            'label'       => __( 'Primary button link', 'editorial-starter' ),
            'description' => __( 'Enter a full URL or an on-page anchor such as #latest-heading.', 'editorial-starter' ),
            'section'     => 'editorial_starter_hero',
            'type'        => 'text',
            'input_attrs' => array(
                'placeholder' => '#latest-heading',
            ),
        )
    );

    $wp_customize->add_section(
        'editorial_starter_primary_cta',
        array(
            'title'       => __( 'Primary CTA', 'editorial-starter' ),
            'description' => __( 'Manage the global call-to-action shown in the header and inline content card.', 'editorial-starter' ),
            'priority'    => 34,
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_primary_cta_enabled',
        array(
            'default'           => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        )
    );

    $wp_customize->add_control(
        'editorial_starter_primary_cta_enabled',
        array(
            'label'   => __( 'Enable primary CTA', 'editorial-starter' ),
            'section' => 'editorial_starter_primary_cta',
            'type'    => 'checkbox',
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_primary_cta_url',
        array(
            'default'           => home_url( '/featured-story/' ),
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        'editorial_starter_primary_cta_url',
        array(
            'label'       => __( 'Primary CTA destination URL', 'editorial-starter' ),
            'description' => __( 'Point this at your flagship article, product page, or campaign landing page.', 'editorial-starter' ),
            'section'     => 'editorial_starter_primary_cta',
            'type'        => 'url',
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_primary_cta_label',
        array(
            'default'           => __( 'Read the featured story', 'editorial-starter' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'editorial_starter_primary_cta_label',
        array(
            'label'       => __( 'Primary CTA label', 'editorial-starter' ),
            'description' => __( 'Used in the header button, hero pattern, and inline content card.', 'editorial-starter' ),
            'section'     => 'editorial_starter_primary_cta',
            'type'        => 'text',
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_primary_card_heading',
        array(
            'default'           => __( 'Ready to launch your main story?', 'editorial-starter' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'editorial_starter_primary_card_heading',
        array(
            'label'       => __( 'Inline card heading', 'editorial-starter' ),
            'description' => __( 'Headline used in the inline CTA card on posts and pages.', 'editorial-starter' ),
            'section'     => 'editorial_starter_primary_cta',
            'type'        => 'text',
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_primary_card_description',
        array(
            'default'           => __( 'Point this section at a flagship article, service page, product detail, or campaign landing page.', 'editorial-starter' ),
            'sanitize_callback' => 'sanitize_textarea_field',
        )
    );

    $wp_customize->add_control(
        'editorial_starter_primary_card_description',
        array(
            'label'       => __( 'Inline card description', 'editorial-starter' ),
            'description' => __( 'Supporting copy used beneath the inline CTA heading.', 'editorial-starter' ),
            'section'     => 'editorial_starter_primary_cta',
            'type'        => 'textarea',
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_secondary_cta_label',
        array(
            'default'           => __( 'Join the newsletter', 'editorial-starter' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'editorial_starter_secondary_cta_label',
        array(
            'label'       => __( 'Secondary CTA label', 'editorial-starter' ),
            'description' => __( 'Used for the inline newsletter button and feature-panel newsletter action.', 'editorial-starter' ),
            'section'     => 'editorial_starter_primary_cta',
            'type'        => 'text',
        )
    );

    $wp_customize->add_section(
        'editorial_starter_seo',
        array(
            'title'       => __( 'SEO & Indexing', 'editorial-starter' ),
            'description' => __( 'Tune homepage metadata and indexing behavior for search engines.', 'editorial-starter' ),
            'priority'    => 35,
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_meta_description',
        array(
            'default'           => editorial_starter_get_default_meta_description(),
            'sanitize_callback' => 'sanitize_textarea_field',
        )
    );

    $wp_customize->add_control(
        'editorial_starter_meta_description',
        array(
            'label'       => __( 'Homepage meta description', 'editorial-starter' ),
            'description' => __( 'One or two sentences summarising the site for search results and social previews.', 'editorial-starter' ),
            'section'     => 'editorial_starter_seo',
            'type'        => 'textarea',
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_accent_color',
        array(
            'default'           => '#d0915d',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'editorial_starter_accent_color',
            array(
                'label'   => __( 'Accent color', 'editorial-starter' ),
                'section' => 'colors',
            )
        )
    );

    $wp_customize->add_section(
        'editorial_starter_footer',
        array(
            'title'       => __( 'Footer', 'editorial-starter' ),
            'description' => __( 'Control the social badges displayed beneath the footer sign-off.', 'editorial-starter' ),
            'priority'    => 40,
        )
    );

    $wp_customize->add_setting(
        'editorial_starter_footer_social_links',
        array(
            'default'           => wp_json_encode( editorial_starter_get_default_social_links() ),
            'sanitize_callback' => 'editorial_starter_sanitize_social_links',
        )
    );

    $wp_customize->add_control(
        new Neon_Chronicle_Social_Links_Control(
            $wp_customize,
            'editorial_starter_footer_social_links',
            array(
                'label'       => __( 'Footer social badges', 'editorial-starter' ),
                'description' => __( 'Add, remove, or rename the circular footer links. Leave the badge text empty to automatically use initials.', 'editorial-starter' ),
                'section'     => 'editorial_starter_footer',
            )
        )
    );
}
add_action( 'customize_register', 'editorial_starter_customize_register' );

/**
 * Inject dynamic Customizer styles.
 */
function editorial_starter_customizer_css() {
    $accent = get_theme_mod( 'editorial_starter_accent_color', '#d0915d' );

    $css = sprintf(
        ':root { --accent: %1$s; } .button, .wp-block-button__link, input[type="submit"] { background: linear-gradient(120deg, %1$s, var(--accent-strong)); }
        .primary-navigation a::after, .section-heading::before, .read-more::after, .pagination .current { background: linear-gradient(120deg, %1$s, var(--accent-strong)); }
        .post-meta span::before { color: %1$s; }
        .orbit-node, .timeline::before, blockquote { border-color: %1$s; }
        .footer-social a { border-color: color-mix(in srgb, %1$s 25%, transparent); }',
        esc_html( $accent )
    );

    wp_add_inline_style( 'editorial-starter-theme', $css );
}
add_action( 'wp_enqueue_scripts', 'editorial_starter_customizer_css', 20 );

<?php
/**
 * Footer template
 *
 * @package EditorialStarter
 */
?>
        </div><!-- #content -->

        <footer class="site-footer">
            <div class="container">
                <div class="footer-brand">
                    <h2 class="section-heading"><?php esc_html_e( 'Stay in the loop', 'editorial-starter' ); ?></h2>
                    <p class="footer-credits">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — <?php esc_html_e( 'A flexible starting point for stories, products, and campaigns.', 'editorial-starter' ); ?></p>
                </div>

                <?php if ( has_nav_menu( 'footer' ) ) : ?>
                    <nav class="footer-nav" aria-label="<?php esc_attr_e( 'Footer menu', 'editorial-starter' ); ?>">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'footer',
                                'menu_id'        => 'footer-menu',
                                'container'      => false,
                            )
                        );
                        ?>
                    </nav>
                <?php endif; ?>

                <?php
                if ( function_exists( 'block_template_part' ) && current_theme_supports( 'block-template-parts' ) ) {
                    block_template_part( 'footer-widgets' );
                } elseif ( is_active_sidebar( 'sidebar-footer' ) ) {
                    ?>
                    <div class="widget-area">
                        <?php dynamic_sidebar( 'sidebar-footer' ); ?>
                    </div>
                    <?php
                }
                ?>

                <?php
                $footer_social_links = editorial_starter_get_social_links();

                if ( ! empty( $footer_social_links ) ) :
                    ?>
                    <div class="footer-social" aria-label="<?php esc_attr_e( 'Social media links', 'editorial-starter' ); ?>">
                        <?php
                        foreach ( $footer_social_links as $social_link ) {
                            $badge   = editorial_starter_get_social_icon_display( $social_link['label'], $social_link['icon'], $social_link['url'] );
                            $is_http = preg_match( '/^https?:/i', $social_link['url'] );

                            $attributes = '';

                            if ( $is_http ) {
                                $attributes = ' target="_blank" rel="noopener"';
                            }
                            ?>
                            <a href="<?php echo esc_url( $social_link['url'] ); ?>"<?php echo $attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                                <span class="footer-social__icon" aria-hidden="true">
                                    <?php
                                    if ( ! empty( $badge['is_svg'] ) && ! empty( $badge['content'] ) ) {
                                        echo wp_kses(
                                            $badge['content'],
                                            array(
                                                'svg'  => array(
                                                    'class' => true,
                                                    'xmlns' => true,
                                                    'viewBox' => true,
                                                    'focusable' => true,
                                                    'aria-hidden' => true,
                                                    'role' => true,
                                                ),
                                                'path' => array(
                                                    'd'    => true,
                                                    'fill' => true,
                                                    'fill-rule' => true,
                                                    'clip-rule' => true,
                                                    'stroke' => true,
                                                    'stroke-linecap' => true,
                                                    'stroke-linejoin' => true,
                                                    'stroke-width' => true,
                                                ),
                                            )
                                        );
                                    } elseif ( ! empty( $badge['content'] ) ) {
                                        echo esc_html( $badge['content'] );
                                    }
                                    ?>
                                </span>
                                <span class="screen-reader-text"><?php echo esc_html( $social_link['label'] ); ?></span>
                            </a>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                endif;
                ?>

            </div>
        </footer>
    </div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>

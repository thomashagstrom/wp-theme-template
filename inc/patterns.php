<?php
/**
 * Registers custom block pattern categories and patterns for Editorial Starter.
 *
 * @package EditorialStarter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'editorial_starter_register_block_patterns' ) ) {
    /**
     * Load block patterns and categories.
     *
     * @return void
     */
    function editorial_starter_register_block_patterns() {
        if ( ! function_exists( 'register_block_pattern_category' ) || ! function_exists( 'register_block_pattern' ) ) {
            return;
        }

        register_block_pattern_category(
            'editorial-starter',
            array(
                'label' => __( 'Editorial Starter', 'editorial-starter' ),
            )
        );

        register_block_pattern_category(
            'editorial-starter-cta',
            array(
                'label' => __( 'Calls to Action', 'editorial-starter' ),
            )
        );

        register_block_pattern_category(
            'editorial-starter-layouts',
            array(
                'label' => __( 'Layouts', 'editorial-starter' ),
            )
        );

        $pattern_files = glob( get_template_directory() . '/patterns/*.php' );

        if ( empty( $pattern_files ) ) {
            return;
        }

        natsort( $pattern_files );

        foreach ( $pattern_files as $pattern_file ) {
            $pattern = require $pattern_file;

            if ( ! is_array( $pattern ) || empty( $pattern['content'] ) ) {
                continue;
            }

            $pattern_slug = ! empty( $pattern['slug'] ) && is_string( $pattern['slug'] )
                ? $pattern['slug']
                : 'editorial-starter/' . basename( $pattern_file, '.php' );

            register_block_pattern( $pattern_slug, $pattern );
        }
    }
}
add_action( 'init', 'editorial_starter_register_block_patterns', 9 );

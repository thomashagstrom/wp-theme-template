<?php
/**
 * CLI helper to refresh block pattern preview markup.
 *
 * Usage: php scripts/update-pattern-previews.php
 */

declare( strict_types=1 );

$root_dir     = dirname( __DIR__ );
$patterns_dir = $root_dir . '/patterns';
$previews_dir = $root_dir . '/pattern-previews';

if ( ! is_dir( $patterns_dir ) ) {
    fwrite( STDERR, "Patterns directory not found.\n" );
    exit( 1 );
}

if ( ! is_dir( $previews_dir ) && ! mkdir( $previews_dir, 0755, true ) && ! is_dir( $previews_dir ) ) {
    fwrite( STDERR, "Unable to create preview directory.\n" );
    exit( 1 );
}

if ( ! function_exists( '__' ) ) {
    function __( string $text, string $domain = 'default' ): string { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
        return $text;
    }
}

if ( ! function_exists( 'esc_html__' ) ) {
    function esc_html__( string $text, string $domain = 'default' ): string { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
        return $text;
    }
}

if ( ! function_exists( 'esc_attr__' ) ) {
    function esc_attr__( string $text, string $domain = 'default' ): string { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
        return $text;
    }
}

if ( ! function_exists( 'esc_url' ) ) {
    function esc_url( string $url ): string { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
        return $url;
    }
}

$pattern_files = glob( $patterns_dir . '/*.php' );

if ( empty( $pattern_files ) ) {
    fwrite( STDOUT, "No pattern files discovered.\n" );
    exit( 0 );
}

natsort( $pattern_files );

foreach ( $pattern_files as $pattern_file ) {
    $pattern = require $pattern_file;

    if ( ! is_array( $pattern ) || empty( $pattern['content'] ) ) {
        fwrite( STDERR, sprintf( "Skipping %s because it does not return a valid pattern array.\n", basename( $pattern_file ) ) );
        continue;
    }

    $slug = basename( $pattern_file, '.php' );

    if ( ! empty( $pattern['slug'] ) && is_string( $pattern['slug'] ) ) {
        $slug = str_replace( '/', '-', $pattern['slug'] );
    } else {
        $slug = 'editorial-starter-' . $slug;
    }

    $preview_path = $previews_dir . '/' . $slug . '.html';
    $header       = sprintf( "<!-- Auto-generated preview: %s -->\n", gmdate( 'c' ) );
    $content      = $header . trim( (string) $pattern['content'] ) . "\n";

    file_put_contents( $preview_path, $content );
}

echo sprintf( "Updated %d pattern preview file(s).\n", count( $pattern_files ) );

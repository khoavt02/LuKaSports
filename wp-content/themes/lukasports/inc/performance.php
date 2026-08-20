<?php
/**
 * Performance: strip default WP payload we don't use, keep the JS
 * footprint minimal, preload the hero image on the front page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_disable_unused_head_output() {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'rest_output_link_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}
add_action( 'init', 'lukasports_disable_unused_head_output' );

/**
 * jQuery migrate is dead weight for a theme with no jQuery-dependent JS.
 */
function lukasports_remove_jquery_migrate( $scripts ) {
	if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
		$scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
	}
}
add_action( 'wp_default_scripts', 'lukasports_remove_jquery_migrate' );

function lukasports_preload_hero_image() {
	if ( ! is_front_page() ) {
		return;
	}

	printf( '<link rel="preload" as="image" href="%s" fetchpriority="high" />' . "\n", esc_url( lukasports_get_hero_image_url() ) );
}
add_action( 'wp_head', 'lukasports_preload_hero_image', 2 );

/**
 * Every <img> we render through lukasports_attachment_image() already
 * requests loading="lazy"; make sure any editor/content images do too.
 */
add_filter( 'wp_lazy_loading_enabled', '__return_true' );

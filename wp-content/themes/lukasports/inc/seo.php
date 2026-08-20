<?php
/**
 * Baseline SEO output for when no SEO plugin is active. Everything
 * here checks for Yoast/Rank Math first and backs off completely to
 * avoid duplicate meta tags.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' );
}

function lukasports_meta_description() {
	if ( lukasports_seo_plugin_active() ) {
		return;
	}

	$description = '';

	if ( is_front_page() ) {
		$description = get_bloginfo( 'description' );
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		$description = has_excerpt( $post ) ? get_the_excerpt( $post ) : lukasports_excerpt( $post->post_content, 30 );
	} elseif ( is_category() || is_tax() || is_post_type_archive( 'product' ) ) {
		$description = term_description() ? wp_strip_all_tags( term_description() ) : '';
	}

	$description = trim( wp_strip_all_tags( $description ) );

	if ( '' === $description ) {
		return;
	}

	printf( '<meta name="description" content="%s" />' . "\n", esc_attr( wp_trim_words( $description, 40 ) ) );
}
add_action( 'wp_head', 'lukasports_meta_description', 1 );

function lukasports_canonical_url() {
	if ( lukasports_seo_plugin_active() ) {
		return;
	}

	$canonical = wp_get_canonical_url();
	if ( is_front_page() ) {
		$canonical = home_url( '/' );
	}
	if ( $canonical ) {
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $canonical ) );
	}
}
add_action( 'wp_head', 'lukasports_canonical_url', 1 );

function lukasports_open_graph_tags() {
	if ( lukasports_seo_plugin_active() ) {
		return;
	}

	$title = wp_get_document_title();
	$image = '';

	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( get_queried_object_id(), 'lukasports-hero' );
	}

	printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:type" content="%s" />' . "\n", is_singular( 'product' ) ? 'product' : 'website' );
	printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( lukasports_current_url() ) );
	if ( $image ) {
		printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $image ) );
	}
}
add_action( 'wp_head', 'lukasports_open_graph_tags', 1 );

function lukasports_current_url() {
	global $wp;
	return home_url( add_query_arg( array(), $wp->request ) );
}

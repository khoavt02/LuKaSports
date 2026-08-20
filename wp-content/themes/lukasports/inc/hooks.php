<?php
/**
 * WooCommerce hook adjustments. We render single-product and card
 * markup ourselves (see woocommerce/*.php), so this file only touches
 * behavior we can't otherwise control from a template: tabs, the
 * default placeholder image, and turning off the review system (not
 * implemented this phase — see #64, don't ship a feature that isn't real).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_product_tabs( $tabs ) {
	unset( $tabs['reviews'] );

	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['title'] = __( 'Mô tả', 'lukasports' );
	}
	if ( isset( $tabs['additional_information'] ) ) {
		$tabs['additional_information']['title'] = __( 'Thông số', 'lukasports' );
	}

	$tabs['size_guide'] = array(
		'title'    => __( 'Hướng dẫn chọn size', 'lukasports' ),
		'priority' => 30,
		'callback' => 'lukasports_size_guide_tab_content',
	);

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'lukasports_product_tabs' );

function lukasports_size_guide_tab_content() {
	get_template_part( 'template-parts/product/size-guide' );
}

/**
 * Reviews are out of scope for Phase 1 — no fake ratings, no partial UI.
 */
add_filter( 'woocommerce_review_ratings_enabled', '__return_false' );
add_filter( 'woocommerce_product_tabs_reviews_priority', '__return_null' );

function lukasports_placeholder_image_src() {
	return LUKASPORTS_THEME_URI . '/assets/images/placeholder-product.svg';
}
add_filter( 'woocommerce_placeholder_img_src', 'lukasports_placeholder_image_src' );

/**
 * Related products: keep the WooCommerce query, but hand rendering to
 * our own card template so it matches best-sellers/category grids.
 */
function lukasports_related_products_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'lukasports_related_products_args' );

/**
 * WooCommerce's own product-category archive (/product-category/slug/)
 * always exists alongside the clean top-level URL served by
 * template-category.php (see that file for why). Redirect to the
 * clean URL so there's exactly one canonical address per category.
 */
function lukasports_redirect_native_category_url() {
	if ( ! is_product_category() ) {
		return;
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term ) {
		return;
	}

	$target = home_url( '/' . $term->slug . '/' );
	$paged  = (int) get_query_var( 'paged' );
	if ( $paged > 1 ) {
		$target = trailingslashit( $target ) . 'page/' . $paged . '/';
	}

	wp_safe_redirect( $target, 301 );
	exit;
}
add_action( 'template_redirect', 'lukasports_redirect_native_category_url' );

/**
 * Breadcrumb trail (visible link + BreadcrumbList schema @id) would
 * otherwise point at the /product-category/slug/ URL that the
 * redirect above immediately 301s away from — rewrite it to the
 * clean URL so neither users nor search engines hit that extra hop.
 */
function lukasports_rewrite_breadcrumb_category_urls( $crumbs ) {
	foreach ( $crumbs as &$crumb ) {
		if ( empty( $crumb[1] ) ) {
			continue;
		}
		$path = wp_parse_url( $crumb[1], PHP_URL_PATH );
		if ( ! is_string( $path ) || '' === $path ) {
			continue;
		}
		$term = get_term_by( 'slug', basename( untrailingslashit( $path ) ), 'product_cat' );
		if ( $term instanceof WP_Term ) {
			$crumb[1] = home_url( '/' . $term->slug . '/' );
		}
	}
	return $crumbs;
}
add_filter( 'woocommerce_get_breadcrumb', 'lukasports_rewrite_breadcrumb_category_urls' );

/**
 * Upsells are a recommendation feature — explicitly out of scope for
 * Phase 1 (see #16, "Advanced recommendation engine").
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

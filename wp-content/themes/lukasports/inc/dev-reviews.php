<?php
/**
 * Preview-only: re-enables WooCommerce product reviews/ratings, which
 * inc/hooks.php deliberately turns off for Phase 1 ("no fake ratings,
 * no partial UI" — see lukasports_product_tabs()). Requested for local
 * design review together with the seeded reviews from
 * bin/seed-reviews-blog.php. Delete this file (and its require line
 * in functions.php) to go back to the Phase 1 behavior.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'woocommerce_review_ratings_enabled', '__return_true' );

function lukasports_dev_restore_reviews_tab( $tabs ) {
	global $product;

	if ( isset( $tabs['reviews'] ) || ! $product instanceof WC_Product ) {
		return $tabs;
	}

	$tabs['reviews'] = array(
		/* translators: %d: reviews count */
		'title'    => sprintf( __( 'Đánh giá (%d)', 'lukasports' ), $product->get_review_count() ),
		'priority' => 30,
		'callback' => 'comments_template',
	);

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'lukasports_dev_restore_reviews_tab', 20 );

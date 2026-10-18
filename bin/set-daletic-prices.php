<?php
/**
 * Price for every product in the "DALETIC.VN 2026" collection:
 * 360.000đ, on sale 189.000đ (all variations).
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/set-daletic-prices.php
 */

$ids = wc_get_products(
	array(
		'status'    => 'publish',
		'limit'     => -1,
		'return'    => 'ids',
		'tax_query' => array( array( 'taxonomy' => 'product_collection', 'field' => 'slug', 'terms' => 'daletic-vn-2026' ) ), // phpcs:ignore
	)
);

foreach ( $ids as $id ) {
	$product = wc_get_product( $id );
	$targets = $product->is_type( 'variable' ) ? $product->get_children() : array( $id );
	foreach ( $targets as $target_id ) {
		$item = wc_get_product( $target_id );
		$item->set_regular_price( '360000' );
		$item->set_sale_price( '189000' );
		$item->save();
	}
	if ( $product->is_type( 'variable' ) ) {
		WC_Product_Variable::sync( $id );
	}
	wc_delete_product_transients( $id );
	WP_CLI::log( 'Priced: ' . $product->get_slug() );
}
WP_CLI::success( count( $ids ) . ' product(s) set to 360.000đ → 189.000đ.' );

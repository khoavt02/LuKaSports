<?php
/**
 * Base price for every custom-design jersey (category ao-bong-da-thiet-ke):
 * 150.000đ (cheapest fabric, Thun đẹp), no sale price (all variations).
 * The real price per fabric is picked on the product page — see
 * lukasports_fabric_tiers() in the theme's inc/helpers.php.
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/set-daletic-prices.php
 */

$ids = wc_get_products(
	array(
		'status'    => 'any',
		'limit'     => -1,
		'return'    => 'ids',
		'tax_query' => array( array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => 'ao-bong-da-thiet-ke' ) ), // phpcs:ignore
	)
);

foreach ( $ids as $id ) {
	$product = wc_get_product( $id );
	$targets = $product->is_type( 'variable' ) ? $product->get_children() : array( $id );
	foreach ( $targets as $target_id ) {
		$item = wc_get_product( $target_id );
		$item->set_regular_price( '150000' );
		$item->set_sale_price( '' );
		$item->save();
	}
	if ( $product->is_type( 'variable' ) ) {
		WC_Product_Variable::sync( $id );
	}
	wc_delete_product_transients( $id );
	WP_CLI::log( 'Priced: ' . $product->get_slug() );
}
WP_CLI::success( count( $ids ) . ' product(s) set to 360.000đ → 189.000đ.' );

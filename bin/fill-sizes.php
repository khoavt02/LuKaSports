<?php
/**
 * Every apparel product offers the full DALETIC size chart: S, M, L, XL,
 * XXL, 3XL, 4XL (template-parts/product/size-guide.php). Accessories
 * (phu-kien) are left alone. Simple apparel products become variable
 * (same price on every size); missing size variations are added with
 * the prices of the product's existing variations. Safe to re-run.
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/fill-sizes.php
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce is not active.' );
}

$sizes = array( 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL' );

// Size terms, in chart order (WooCommerce sorts attribute terms by "order").
$size_terms = array();
foreach ( $sizes as $i => $size ) {
	$term = term_exists( $size, 'pa_size' );
	if ( ! $term ) {
		$term = wp_insert_term( $size, 'pa_size' );
	}
	$term_id = (int) $term['term_id'];
	update_term_meta( $term_id, 'order', $i );
	$size_terms[ $size ] = get_term( $term_id, 'pa_size' );
}

$updated = 0;
foreach ( wc_get_products( array( 'limit' => -1, 'status' => array( 'publish', 'draft', 'private' ) ) ) as $product ) {
	$id = $product->get_id();
	if ( has_term( 'phu-kien', 'product_cat', $id ) ) {
		continue;
	}

	// Price to use for new variations.
	if ( $product->is_type( 'simple' ) ) {
		$regular = $product->get_regular_price();
		$sale    = $product->get_sale_price();
		wp_set_object_terms( $id, 'variable', 'product_type' );
		wc_delete_product_transients( $id );
		$product = new WC_Product_Variable( $id );
	} else {
		$children = $product->get_children();
		$sample   = $children ? wc_get_product( $children[0] ) : null;
		$regular  = $sample ? $sample->get_regular_price() : '';
		$sale     = $sample ? $sample->get_sale_price() : '';
	}

	// Size attribute with all 7 sizes, used for variations.
	$attributes = $product->get_attributes();
	// A fresh object: editing the stored one in place isn't seen as a change.
	$size_attr  = isset( $attributes['pa_size'] ) ? clone $attributes['pa_size'] : new WC_Product_Attribute();
	$size_attr->set_id( wc_attribute_taxonomy_id_by_name( 'size' ) );
	$size_attr->set_name( 'pa_size' );
	$size_attr->set_options( array_map( fn( $t ) => $t->term_id, array_values( $size_terms ) ) );
	$size_attr->set_visible( true );
	$size_attr->set_variation( true );
	$attributes['pa_size'] = $size_attr;
	$product->set_attributes( $attributes );
	$product->save();
	wp_set_object_terms( $id, array_map( fn( $t ) => $t->term_id, array_values( $size_terms ) ), 'pa_size' );

	// Other variation attributes (e.g. color) → every combination × size.
	$axes = array( 'pa_size' => array_map( fn( $t ) => $t->slug, array_values( $size_terms ) ) );
	foreach ( $attributes as $name => $attr ) {
		if ( 'pa_size' !== $name && $attr->get_variation() && $attr->is_taxonomy() ) {
			$axes[ $name ] = array_map( fn( $tid ) => get_term( $tid )->slug, $attr->get_options() );
		}
	}
	$combos = array( array() );
	foreach ( $axes as $name => $values ) {
		$next = array();
		foreach ( $combos as $combo ) {
			foreach ( $values as $value ) {
				$next[] = $combo + array( $name => $value );
			}
		}
		$combos = $next;
	}

	$existing = array();
	foreach ( $product->get_children() as $child_id ) {
		$attrs = wc_get_product( $child_id )->get_attributes();
		ksort( $attrs );
		$existing[ wp_json_encode( $attrs ) ] = true;
	}

	$added = 0;
	foreach ( $combos as $combo ) {
		ksort( $combo );
		if ( isset( $existing[ wp_json_encode( $combo ) ] ) ) {
			continue;
		}
		$variation = new WC_Product_Variation();
		$variation->set_parent_id( $id );
		$variation->set_attributes( $combo );
		$variation->set_regular_price( $regular );
		if ( '' !== $sale ) {
			$variation->set_sale_price( $sale );
		}
		$variation->set_stock_status( 'instock' );
		$variation->save();
		++$added;
	}

	WC_Product_Variable::sync( $id );
	wc_delete_product_transients( $id );
	if ( $added ) {
		++$updated;
		WP_CLI::log( sprintf( '%s: +%d variation(s)', $product->get_slug(), $added ) );
	}
}

WP_CLI::success( "$updated product(s) now offer S–4XL." );

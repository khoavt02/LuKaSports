<?php
/**
 * Dòng "Thể Thao SVIP": fixed price 360.000đ → 189.000đ (not fabric-priced),
 * free printing, 4 colourways in one product. Creates the category
 * "Áo Thể Thao SVIP" and the collection "SVIP"; products also join
 * DALETIC.VN 2026 so they lead the homepage. Safe to re-run (skips existing).
 * Add a product = one line in $items + bin/product-photos/{slug}.jpg.
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/add-svip-sports.php
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce is not active.' );
}

$items = array(
	array( 'bo-the-thao-svip-tu-sac', 'Bộ Thể Thao SVIP DALETIC Tứ Sắc', array( 'Xanh Lá', 'Xanh Dương', 'Đỏ', 'Trắng' ), 'Một mẫu, bốn phối màu: Emerald, Royal Blue, Burgundy, White/Gold. Miễn phí in ấn.' ),
);

$sizes = array( 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL' );

$ensure_term = function ( $slug, $name, $taxonomy, $desc = '' ) {
	$t = term_exists( $slug, $taxonomy );
	if ( ! $t ) {
		$t = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug, 'description' => $desc ) );
	}
	return (int) ( is_array( $t ) ? $t['term_id'] : $t );
};
$cat_id  = $ensure_term( 'ao-the-thao-svip', 'Áo Thể Thao SVIP', 'product_cat', 'Dòng thể thao SVIP của DALETIC: nhiều phối màu, miễn phí in ấn.' );
$col_svip = $ensure_term( 'svip', 'SVIP', 'product_collection', 'Dòng thể thao SVIP: thiết kế chevron nhiều phối màu, giá ưu đãi, miễn phí in ấn.' );
$col_2026 = $ensure_term( 'daletic-vn-2026', 'DALETIC.VN 2026', 'product_collection' );

$term_id = function ( $taxonomy, $name ) {
	$term = term_exists( $name, $taxonomy );
	if ( ! $term ) {
		$term = wp_insert_term( $name, $taxonomy );
	}
	return (int) $term['term_id'];
};
$attribute = function ( $key, $values, $variation = false ) use ( $term_id ) {
	$attr = new WC_Product_Attribute();
	$attr->set_id( wc_attribute_taxonomy_id_by_name( $key ) );
	$attr->set_name( 'pa_' . $key );
	$attr->set_options( array_map( fn( $v ) => $term_id( 'pa_' . $key, $v ), (array) $values ) );
	$attr->set_visible( true );
	$attr->set_variation( $variation );
	return $attr;
};

foreach ( $items as list( $slug, $name, $colors, $short ) ) {
	$existing = get_page_by_path( $slug, OBJECT, 'product' );
	if ( $existing ) {
		WP_CLI::log( "Exists: $slug" );
		continue;
	}
	$product = new WC_Product_Variable();
	$product->set_name( $name );
	$product->set_slug( $slug );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_short_description( $short );
	$product->set_description( "<p>{$short}</p>\n<p>Chọn phối màu và size, nhắn DALETIC để nhận mockup tên và số trước khi in. In tên, số, logo đội miễn phí.</p>" );
	$product->set_category_ids( array( $cat_id ) );
	$product->set_attributes(
		array(
			$attribute( 'sport', 'Bóng đá' ),
			$attribute( 'season', '2025-2026' ),
			$attribute( 'gender', 'Unisex' ),
			$attribute( 'color', $colors, true ),
			$attribute( 'size', $sizes, true ),
		)
	);
	$id = $product->save();
	wp_set_object_terms( $id, array( $col_svip, $col_2026 ), 'product_collection' );
	update_post_meta( $id, '_lukasports_benefits', implode( "\n", array( 'Miễn phí in tên, số, logo đội', '4 phối màu trong một mẫu', 'Thiết kế độc quyền DALETIC' ) ) );
	update_post_meta( $id, '_lukasports_printing_available', '1' );
	update_post_meta( $id, '_lukasports_badge', 'svip' );

	foreach ( $sizes as $size ) {
		$v = new WC_Product_Variation();
		$v->set_parent_id( $id );
		$v->set_regular_price( '360000' );
		$v->set_sale_price( '189000' );
		$v->set_stock_status( 'instock' );
		$v->set_attributes( array( 'pa_size' => get_term( $term_id( 'pa_size', $size ), 'pa_size' )->slug ) );
		$v->save();
	}
	WC_Product_Variable::sync( $id );
	wc_delete_product_transients( $id );
	WP_CLI::log( "Created: $slug (#$id)" );
}

WP_CLI::success( 'SVIP done.' );
require __DIR__ . '/apply-product-photos.php';

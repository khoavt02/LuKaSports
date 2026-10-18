<?php
/**
 * DALETIC.VN in-house designs (Oct 2026): creates the products once
 * (skipped when the slug already exists) in "Áo Bóng Đá Thiết Kế Theo
 * Yêu Cầu" and the "DALETIC.VN 2026" collection, with S–XL sizes.
 * Photos come from bin/product-photos/{slug}.jpg via
 * apply-product-photos.php, which this script runs at the end.
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/add-daletic-designs.php
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce is not active.' );
}

$designs = array(
	array( 'ao-thiet-ke-daletic-reu-san-ho', 'Áo Thiết Kế DALETIC Rêu San Hô', array( 'Xanh Rêu', 'Cam' ), 'Nền xanh rêu đậm phối họa tiết san hô cam nổi bật.' ),
	array( 'ao-thiet-ke-daletic-song-trang', 'Áo Thiết Kế DALETIC Sóng Trắng', array( 'Trắng', 'Xanh Da Trời' ), 'Nền trắng vân sóng xám, tay áo xanh da trời.' ),
	array( 'ao-thiet-ke-daletic-ngoc-bich', 'Áo Thiết Kế DALETIC Ngọc Bích', array( 'Xanh Ngọc', 'Trắng' ), 'Họa tiết loang xanh ngọc chạy chéo trên nền trắng.' ),
	array( 'ao-thiet-ke-daletic-rang-cua-do', 'Áo Thiết Kế DALETIC Răng Cưa Đỏ', array( 'Trắng', 'Đỏ' ), 'Nền trắng, vai và tay áo họa tiết răng cưa đỏ.' ),
	array( 'ao-thiet-ke-daletic-hoa-van-vang', 'Áo Thiết Kế DALETIC Hoa Văn Vàng', array( 'Trắng', 'Vàng' ), 'Hoa văn hình học vàng trên nền trắng, tay áo vàng.' ),
	array( 'ao-thiet-ke-daletic-tia-chop-cam', 'Áo Thiết Kế DALETIC Tia Chớp Cam', array( 'Cam', 'Xanh Lá' ), 'Tia chớp trắng trên nền cam, chuyển dần sang vàng chanh ở gấu áo.' ),
	array( 'ao-thiet-ke-daletic-song-xanh', 'Áo Thiết Kế DALETIC Sóng Xanh', array( 'Xanh Dương', 'Xanh Da Trời' ), 'Vân sóng xanh dương đậm, hai bên sườn xanh da trời.' ),
	array( 'ao-thiet-ke-daletic-ngoi-sao-xanh', 'Áo Thiết Kế DALETIC Ngôi Sao Xanh', array( 'Xanh Dương', 'Trắng' ), 'Nền xanh dương sọc mảnh, ngôi sao xanh nhạt ở thân dưới.' ),
	array( 'bo-dong-phuc-daletic-soc-xanh-troi', 'Bộ Đồng Phục DALETIC Sọc Xanh Trời', array( 'Xanh Da Trời', 'Trắng' ), 'Trọn bộ áo, quần và tất sọc ngang xanh trời – trắng, in tên và số cầu thủ.', true ),
);

$sizes    = array( 'S', 'M', 'L', 'XL' );
$category = get_term_by( 'slug', 'ao-bong-da-thiet-ke', 'product_cat' );
if ( ! $category ) {
	WP_CLI::error( 'Category ao-bong-da-thiet-ke not found.' );
}

$collection = term_exists( 'DALETIC.VN 2026', 'product_collection' );
if ( ! $collection ) {
	$collection = wp_insert_term( 'DALETIC.VN 2026', 'product_collection', array( 'slug' => 'daletic-vn-2026', 'description' => 'Các mẫu áo do DALETIC tự thiết kế và sản xuất.' ) );
}
$collection_id = (int) ( is_array( $collection ) ? $collection['term_id'] : $collection );

/** Term id in an attribute taxonomy, created if missing. */
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

$created = 0;
foreach ( $designs as $design ) {
	list( $slug, $name, $colors, $short ) = $design;
	$is_set = ! empty( $design[4] );

	if ( get_page_by_path( $slug, OBJECT, 'product' ) ) {
		WP_CLI::log( "Exists: $slug" );
		continue;
	}

	$regular = $is_set ? 329000 : 259000;
	$sale    = $is_set ? 289000 : 229000;

	$product = new WC_Product_Variable();
	$product->set_name( $name );
	$product->set_slug( $slug );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_short_description( $short );
	$product->set_description(
		"<p>{$short} Thiết kế độc quyền của DALETIC, in chuyển nhiệt sắc nét, không bong tróc.</p>\n"
		. '<p>Có thể đổi màu, thêm logo đội, tên và số cầu thủ theo yêu cầu. Nhắn DALETIC để nhận mockup trước khi sản xuất.</p>'
	);
	$product->set_category_ids( array( $category->term_id ) );
	$product->set_attributes(
		array(
			$attribute( 'material', 'Polyester lạnh' ),
			$attribute( 'sport', 'Bóng đá' ),
			$attribute( 'season', '2025-2026' ),
			$attribute( 'gender', 'Unisex' ),
			$attribute( 'color', $colors ),
			$attribute( 'size', $sizes, true ),
		)
	);
	$product_id = $product->save();

	wp_set_object_terms( $product_id, array( $collection_id ), 'product_collection' );
	update_post_meta( $product_id, '_lukasports_benefits', implode( "\n", array( 'Thiết kế độc quyền DALETIC', 'In tên, số, logo đội theo yêu cầu', $is_set ? 'Trọn bộ áo, quần và tất' : 'Vải polyester lạnh thoáng khí' ) ) );
	update_post_meta( $product_id, '_lukasports_printing_available', '1' );

	foreach ( $sizes as $size ) {
		$variation = new WC_Product_Variation();
		$variation->set_parent_id( $product_id );
		$variation->set_regular_price( (string) $regular );
		$variation->set_sale_price( (string) $sale );
		$variation->set_stock_status( 'instock' );
		$variation->set_attributes( array( 'pa_size' => get_term( $term_id( 'pa_size', $size ), 'pa_size' )->slug ) );
		$variation->save();
	}
	WC_Product_Variable::sync( $product_id );
	wc_delete_product_transients( $product_id );

	WP_CLI::log( "Created: $slug (#$product_id)" );
	++$created;
}

WP_CLI::success( "Created $created product(s)." );

require __DIR__ . '/apply-product-photos.php';

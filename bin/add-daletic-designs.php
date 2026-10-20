<?php
/**
 * DALETIC.VN in-house designs (Oct 2026): creates the products once
 * (skipped when the slug already exists) in "Áo Bóng Đá Thiết Kế Theo
 * Yêu Cầu" and the "DALETIC.VN 2026" collection (or the collection named
 * in a design's 6th element, e.g. "Sắc Việt"), with S–4XL sizes.
 * Photos come from bin/product-photos/{slug}.jpg via
 * apply-product-photos.php, which this script runs at the end.
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/add-daletic-designs.php
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce is not active.' );
}

$designs = array(
	array( 'ao-thiet-ke-daletic-reu-san-ho', 'Áo Thiết Kế DALETIC Sơn Lâm Rêu Cam', array( 'Xanh Rêu', 'Cam' ), 'Nền xanh rêu đậm phối họa tiết san hô cam nổi bật.', false, 'son-thuy' ),
	array( 'ao-thiet-ke-daletic-song-trang', 'Áo Thiết Kế DALETIC Bạch Lãng Xanh Trắng', array( 'Trắng', 'Xanh Da Trời' ), 'Nền trắng vân sóng xám, tay áo xanh da trời.', false, 'thien-phong' ),
	array( 'ao-thiet-ke-daletic-ngoc-bich', 'Áo Thiết Kế DALETIC Bích Ngọc Xanh Ngọc', array( 'Xanh Ngọc', 'Trắng' ), 'Họa tiết loang xanh ngọc chạy chéo trên nền trắng.', false, 'son-thuy' ),
	array( 'ao-thiet-ke-daletic-rang-cua-do', 'Áo Thiết Kế DALETIC Xích Phong Đỏ Trắng', array( 'Trắng', 'Đỏ' ), 'Nền trắng, vai và tay áo họa tiết răng cưa đỏ.', false, 'sac-viet' ),
	array( 'ao-thiet-ke-daletic-hoa-van-vang', 'Áo Thiết Kế DALETIC Kim Văn Vàng Trắng', array( 'Trắng', 'Vàng' ), 'Hoa văn hình học vàng trên nền trắng, tay áo vàng.', false, 'hoa-toc' ),
	array( 'ao-thiet-ke-daletic-tia-chop-cam', 'Áo Thiết Kế DALETIC Hỏa Tốc Cam Chanh', array( 'Cam', 'Xanh Lá' ), 'Tia chớp trắng trên nền cam, chuyển dần sang vàng chanh ở gấu áo.', false, 'hoa-toc' ),
	array( 'ao-thiet-ke-daletic-song-xanh', 'Áo Thiết Kế DALETIC Hải Lam Xanh Dương', array( 'Xanh Dương', 'Xanh Da Trời' ), 'Vân sóng xanh dương đậm, hai bên sườn xanh da trời.', false, 'hai-lam' ),
	array( 'ao-thiet-ke-daletic-ngoi-sao-xanh', 'Áo Thiết Kế DALETIC Tinh Hải Xanh Sao', array( 'Xanh Dương', 'Trắng' ), 'Nền xanh dương sọc mảnh, ngôi sao xanh nhạt ở thân dưới.', false, 'hai-lam' ),
	array( 'bo-dong-phuc-daletic-soc-xanh-troi', 'Bộ Đồng Phục DALETIC Thanh Vân Sọc Xanh Trời', array( 'Xanh Da Trời', 'Trắng' ), 'Trọn bộ áo, quần và tất sọc ngang xanh trời – trắng, in tên và số cầu thủ.', true , 'thien-phong' ),
	array( 'bo-dong-phuc-daletic-do-sac-viet', 'Bộ Đồng Phục DALETIC Đỏ Sắc Việt', array( 'Đỏ', 'Trắng' ), 'Trọn bộ áo đỏ họa tiết thoi, hai bên viền răng cưa trắng, cờ đỏ sao vàng ở ngực; quần đỏ và tất trắng, in tên và số cầu thủ.', true, 'sac-viet' ),
	array( 'bo-dong-phuc-daletic-trang-soc-do', 'Bộ Đồng Phục DALETIC Trắng Sọc Đỏ', array( 'Trắng', 'Đỏ' ), 'Trọn bộ áo trắng sọc dọc đỏ, tay áo đỏ họa tiết xương cá, cờ đỏ sao vàng ở ngực; quần đỏ và tất trắng, in tên và số cầu thủ.', true, 'sac-viet' ),
	array( 'bo-dong-phuc-daletic-trang-do-vuon-cao', 'Bộ Đồng Phục DALETIC Trắng Đỏ Vươn Cao', array( 'Trắng', 'Đỏ' ), 'Trọn bộ áo trắng họa tiết lục giác, thân dưới chuyển đỏ với các mũi tên vươn lên, cờ đỏ sao vàng ở ngực; quần đỏ và tất trắng, in tên và số cầu thủ.', true, 'sac-viet' ),
	array( 'bo-dong-phuc-daletic-do-trang-xung-kich', 'Bộ Đồng Phục DALETIC Đỏ Trắng Xung Kích', array( 'Trắng', 'Đỏ' ), 'Trọn bộ áo trắng chuyển dần sang đỏ ở thân dưới, họa tiết zigzag đỏ trên vai và tay, cờ đỏ sao vàng ở ngực; quần đỏ và tất trắng, in tên và số cầu thủ.', true, 'sac-viet' ),
	array( 'bo-dong-phuc-daletic-sai-canh-xanh-trang', 'Bộ Đồng Phục DALETIC Sải Cánh Xanh Trắng', array( 'Trắng', 'Xanh Da Trời', 'Đen' ), 'Trọn bộ áo trắng tay xanh da trời, họa tiết đen mạnh mẽ như đôi cánh dang rộng ở hai bên sườn, cờ đỏ sao vàng ở ngực; quần đen và tất trắng, in tên và số cầu thủ.', true, 'thien-phong' ),
	array( 'bo-dong-phuc-daletic-tang-may-xanh-trang', 'Bộ Đồng Phục DALETIC Tầng Mây Xanh Trắng', array( 'Trắng', 'Xanh Da Trời' ), 'Trọn bộ áo trắng tay xanh da trời, họa tiết bậc thang chéo xanh nhạt chạy dọc thân áo, cờ đỏ sao vàng ở ngực; quần và tất trắng, in tên và số cầu thủ.', true, 'thien-phong' ),
	array( 'bo-dong-phuc-daletic-pha-le-xanh-ngoc', 'Bộ Đồng Phục DALETIC Pha Lê Xanh Ngọc', array( 'Xanh Ngọc', 'Trắng' ), 'Trọn bộ áo xanh ngọc họa tiết tinh thể ở vai và ngực, thân dưới trắng vân mê cung, cờ đỏ sao vàng ở ngực; quần và tất trắng, in tên và số cầu thủ.', true, 'thien-phong' ),
	array( 'bo-dong-phuc-daletic-son-thuy-xanh-co-vit', 'Bộ Đồng Phục DALETIC Sơn Thủy Xanh Cổ Vịt', array( 'Xanh Ngọc', 'Trắng' ), 'Trọn bộ áo xanh cổ vịt đậm với nét cọ đen mạnh mẽ, vai trắng họa tiết đường chéo và đỉnh núi, cờ đỏ sao vàng ở ngực; quần cùng màu, tất trắng, in tên và số cầu thủ.', true, 'son-thuy' ),
	array( 'bo-dong-phuc-daletic-loi-van-xanh-co-vit', 'Bộ Đồng Phục DALETIC Lôi Văn Xanh Cổ Vịt', array( 'Trắng', 'Xanh Ngọc' ), 'Trọn bộ áo trắng họa tiết lôi văn xanh cổ vịt chạy dọc thân, tay áo xanh đậm, cờ đỏ sao vàng ở ngực; quần xanh cổ vịt và tất trắng, in tên và số cầu thủ.', true, 'son-thuy' ),
	array( 'bo-dong-phuc-daletic-suong-vu-xanh-ngoc', 'Bộ Đồng Phục DALETIC Sương Vũ Xanh Ngọc', array( 'Trắng', 'Xanh Ngọc' ), 'Trọn bộ áo trắng sọc chéo xanh ngọc điểm giọt màu li ti, tay áo họa tiết hình học, cờ đỏ sao vàng ở ngực; quần xanh cổ vịt và tất trắng, in tên và số cầu thủ.', true, 'son-thuy' ),
	array( 'bo-dong-phuc-daletic-tho-hoang-be-nau', 'Bộ Đồng Phục DALETIC Thổ Hoàng Be Nâu', array( 'Be', 'Đen' ), 'Trọn bộ áo tông be – nâu họa tiết chấm bán sắc và nét cào cam phong cách retro, cờ đỏ sao vàng ở ngực; quần đen và tất trắng, in tên và số cầu thủ.', true, 'dai-dia' ),
	array( 'bo-dong-phuc-daletic-tuyet-van-xanh-trang', 'Bộ Đồng Phục DALETIC Tuyết Vân Xanh Trắng', array( 'Trắng', 'Xanh Da Trời' ), 'Trọn bộ áo sọc dọc xanh trắng họa tiết mây loang, tay áo xanh da trời, cờ đỏ sao vàng ở ngực; quần và tất xanh nhạt, in tên và số cầu thủ.', true, 'thien-phong' ),
	array( 'bo-dong-phuc-daletic-trieu-lam-xanh-thep', 'Bộ Đồng Phục DALETIC Triều Lam Xanh Thép', array( 'Xanh Da Trời', 'Xám' ), 'Trọn bộ áo xanh thép vân sóng chìm, mảng loang xám ở ngực, cờ đỏ sao vàng ở ngực; quần xanh đậm và tất trắng, in tên và số cầu thủ.', true, 'hai-lam' ),
	array( 'bo-dong-phuc-daletic-thao-nguyen-xanh-reu-nhat', 'Bộ Đồng Phục DALETIC Thảo Nguyên Xanh Rêu Nhạt', array( 'Xanh Rêu', 'Trắng' ), 'Trọn bộ áo xanh rêu nhạt sọc dọc mảnh, họa tiết vòm cung chìm ở thân dưới, cờ đỏ sao vàng ở ngực; quần xanh rêu và tất trắng, in tên và số cầu thủ.', true, 'son-thuy' ),
	array( 'bo-dong-phuc-daletic-vang-xanh', 'Bộ Đồng Phục DALETIC Kim Hoàng Vàng Xanh', array( 'Vàng', 'Xanh Da Trời' ), 'Trọn bộ áo vàng họa tiết zigzag cổ tim, quần xanh da trời và tất vàng – navy, in tên và số cầu thủ.', true , 'hoa-toc' ),
);

$sizes    = array( 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL' );
$category = get_term_by( 'slug', 'ao-bong-da-thiet-ke', 'product_cat' );
if ( ! $category ) {
	WP_CLI::error( 'Category ao-bong-da-thiet-ke not found.' );
}

// Collections a design can belong to (6th element of a design; default 2026).
$collection_defs = array(
	'daletic-vn-2026' => array( 'DALETIC.VN 2026', 'Các mẫu áo do DALETIC tự thiết kế và sản xuất.' ),
	'dai-dia'         => array( 'Đại Địa', 'Dòng thiết kế tông đất be – nâu với họa tiết chấm bán sắc và nét cào, phong cách retro mạnh mẽ.' ),
	'hoa-toc'         => array( 'Hỏa Tốc', 'Dòng thiết kế rực rỡ cam – vàng với đường nét bùng nổ, năng động trên sân.' ),
	'hai-lam'         => array( 'Hải Lam', 'Dòng thiết kế xanh dương đậm với họa tiết sóng và sao, mạnh mẽ như biển lớn.' ),
	'son-thuy'        => array( 'Sơn Thủy', 'Dòng thiết kế sắc xanh thẫm của rừng và biển — rêu, ngọc, cổ vịt — với nét cọ và họa tiết tự nhiên.' ),
	'thien-phong'     => array( 'Thiên Phong', 'Dòng thiết kế nền trắng phối xanh da trời và họa tiết đen mạnh mẽ, lấy cảm hứng từ đôi cánh dang rộng.' ),
	'sac-viet'        => array( 'Sắc Việt', 'Dòng thiết kế phối đỏ – trắng, mang lá cờ đỏ sao vàng lên áo đấu.' ),
);
$collection_ids = array();
foreach ( $collection_defs as $collection_slug => list( $collection_name, $collection_desc ) ) {
	$collection = term_exists( $collection_slug, 'product_collection' );
	if ( ! $collection ) {
		$collection = wp_insert_term( $collection_name, 'product_collection', array( 'slug' => $collection_slug, 'description' => $collection_desc ) );
	} else {
		wp_update_term( (int) ( is_array( $collection ) ? $collection['term_id'] : $collection ), 'product_collection', array( 'description' => $collection_desc ) );
	}
	$collection_ids[ $collection_slug ] = (int) ( is_array( $collection ) ? $collection['term_id'] : $collection );
}

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

	$existing = get_page_by_path( $slug, OBJECT, 'product' );
	if ( $existing ) {
		// Re-running refreshes the name and collections of existing designs.
		wp_update_post( array( 'ID' => $existing->ID, 'post_title' => $name ) );
		wp_set_object_terms( $existing->ID, array_unique( array( $collection_ids[ $design[5] ?? 'daletic-vn-2026' ], $collection_ids['daletic-vn-2026'] ) ), 'product_collection' );
		WP_CLI::log( "Updated: $slug" );
		continue;
	}

	$regular = 150000; // lowest fabric (Thun đẹp); each fabric's price lives in lukasports_fabric_tiers()
	$sale    = '';

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

	wp_set_object_terms( $product_id, array_unique( array( $collection_ids[ $design[5] ?? 'daletic-vn-2026' ], $collection_ids['daletic-vn-2026'] ) ), 'product_collection' );
	update_post_meta( $product_id, '_lukasports_benefits', implode( "\n", array( 'Thiết kế độc quyền DALETIC', 'In tên, số, logo đội theo yêu cầu', $is_set ? 'Trọn bộ áo, quần và tất' : 'Vải polyester lạnh thoáng khí' ) ) );
	update_post_meta( $product_id, '_lukasports_printing_available', '1' );

	foreach ( $sizes as $size ) {
		$variation = new WC_Product_Variation();
		$variation->set_parent_id( $product_id );
		$variation->set_regular_price( (string) $regular );
		$variation->set_sale_price( $sale );
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

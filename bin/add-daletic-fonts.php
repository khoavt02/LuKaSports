<?php
/**
 * "Font Chữ & Số In Áo" gallery (Oct 2026): creates the category and one
 * preview product per font (skipped when the slug already exists). The
 * products are preview-only: no price, size or color. Preview images are
 * bin/product-photos/{slug}.jpg, applied by apply-product-photos.php; the
 * category thumbnail by apply-category-images.php (both run at the end).
 *
 * Add a font = one line in $fonts + bin/product-photos/{slug}.jpg (1200px).
 * Re-running updates names/descriptions of existing slugs.
 * Previews are rendered from free Google Fonts (OFL): Big Shoulders Display,
 * Teko, Anton, Russo One, Graduate, Saira Extra Condensed.
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/add-daletic-fonts.php
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce is not active.' );
}

$fonts = array(
	array( 'font-daletic-01-vang', 'Font DALETIC 01 – Số Hẹp Vai Vuông (Big Shoulders)', 'Số và chữ hẹp, vai vuông, nét đậm — kiểu số áo đấu giải chuyên nghiệp châu Âu, Mỹ.' ),
	array( 'font-daletic-02-do', 'Font DALETIC 02 – Số Hẹp Góc Cạnh (Teko)', 'Số và chữ hẹp, góc cạnh, nét dày — hiện đại, dễ đọc từ xa trên sân.' ),
	array( 'font-daletic-03-xanh-troi', 'Font DALETIC 03 – Số Siêu Đậm (Anton)', 'Số cực đậm, thân cao — nổi bật trên mọi màu áo, in decal và ép nhiệt đều rõ nét.' ),
	array( 'font-daletic-04-xanh-la', 'Font DALETIC 04 – Số Vát Cạnh Công Nghệ (Russo One)', 'Số vát góc kiểu áo đấu công nghệ, nét đều, bản vừa — hợp áo thi đấu trẻ trung.' ),
	array( 'font-daletic-05-cam', 'Font DALETIC 05 – Số Varsity Cổ Điển (Graduate)', 'Số kiểu varsity có chân vuông, phong cách đội trường, bóng rổ và áo retro.' ),
	array( 'font-daletic-06-trang', 'Font DALETIC 06 – Số Hẹp Vát Cạnh (Saira)', 'Số hẹp, cạnh vát gọn — kiểu áo đua và thể thao điện tử, hợp tên dài trên lưng áo.' ),
);

$term = term_exists( 'font-chu-in-ao', 'product_cat' );
if ( ! $term ) {
	$term = wp_insert_term(
		'Font Chữ & Số In Áo',
		'product_cat',
		array(
			'slug'        => 'font-chu-in-ao',
			'description' => 'Thư viện font chữ và số in áo của DALETIC. Chọn font ưng ý, nhắn DALETIC để in tên và số lên áo.',
		)
	);
	if ( is_wp_error( $term ) ) {
		WP_CLI::error( $term->get_error_message() );
	}
}
$category_id = (int) ( is_array( $term ) ? $term['term_id'] : $term );

$created = 0;
foreach ( $fonts as list( $slug, $name, $short ) ) {
	$existing = get_page_by_path( $slug, OBJECT, 'product' );
	if ( $existing ) {
		$product = wc_get_product( $existing->ID );
		$product->set_name( $name );
		$product->set_short_description( $short );
		$product->set_description( "<p>{$short}</p>\n<p>Nhắn DALETIC để chọn font, nhận mockup tên và số trước khi in. Có thể in tên, số, logo đội theo yêu cầu.</p>" );
		$product->save();
		WP_CLI::log( "Updated: $slug" );
		continue;
	}
	$product = new WC_Product_Simple();
	$product->set_name( $name );
	$product->set_slug( $slug );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_short_description( $short );
	$product->set_description( "<p>{$short}</p>\n<p>Nhắn DALETIC để chọn font, nhận mockup tên và số trước khi in. Có thể in tên, số, logo đội theo yêu cầu.</p>" );
	$product->set_category_ids( array( $category_id ) );
	$product_id = $product->save();
	WP_CLI::log( "Created: $slug (#$product_id)" );
	++$created;
}

WP_CLI::success( "Created $created font product(s)." );

require __DIR__ . '/apply-product-photos.php';
require __DIR__ . '/apply-category-images.php';

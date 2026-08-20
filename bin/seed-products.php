<?php
/**
 * Phase 1 development data: 7 product categories + 10 realistic
 * products (mix of simple/variable, on-sale/regular) covering every
 * card/gallery/variation case the theme needs to be tested against.
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/seed-products.php
 * Safe to re-run: it deletes anything it previously seeded first
 * (tagged with the `_lukasports_seed` postmeta) before recreating it.
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce must be active before seeding products.' );
}

/**
 * ---------------------------------------------------------------
 * Cleanup: remove anything from a previous run.
 * ---------------------------------------------------------------
 */
$previous = get_posts(
	array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'meta_key'       => '_lukasports_seed',
		'fields'         => 'ids',
	)
);
foreach ( $previous as $product_id ) {
	$product = wc_get_product( $product_id );
	if ( $product ) {
		foreach ( $product->get_children() as $variation_id ) {
			wp_delete_post( $variation_id, true );
		}
	}
	wp_delete_post( $product_id, true );
}
WP_CLI::log( sprintf( 'Removed %d previously seeded product(s).', count( $previous ) ) );

/**
 * ---------------------------------------------------------------
 * Categories
 * ---------------------------------------------------------------
 */
$categories = array(
	'ao-bong-da'          => array( 'Áo Bóng Đá', 'Áo đấu CLB, đội tuyển và áo không logo — chất liệu thể thao thoáng mát, form chuẩn thi đấu.' ),
	'ao-bong-da-thiet-ke' => array( 'Áo Bóng Đá Thiết Kế', 'Mẫu thiết kế riêng, họa tiết độc quyền của LukaSports — in tên và số theo yêu cầu.' ),
	'ao-team'             => array( 'Áo Team', 'Đồng phục cho team phong trào, công ty, giải đấu — nhận đơn từ số lượng nhỏ.' ),
	'ao-bong-chuyen'      => array( 'Áo Bóng Chuyền', 'Áo thi đấu bóng chuyền, vải thoáng khí và co giãn tốt.' ),
	'ao-bong-ro'          => array( 'Áo Bóng Rổ', 'Áo bóng rổ phong cách streetball và thi đấu, vải lưới thoáng mát.' ),
	'ao-cau-long'         => array( 'Áo Cầu Lông', 'Áo cầu lông nhẹ, khô nhanh — phù hợp cả thi đấu và tập luyện.' ),
	'ao-pickerball'       => array( 'Áo Pickerball', 'Áo pickerball vải thoáng khí, co giãn tốt — phù hợp thi đấu và giao lưu.' ),
	'phu-kien'            => array( 'Phụ Kiện', 'Tất, phụ kiện thể thao đi kèm cho bộ trang phục hoàn chỉnh.' ),
);

$category_ids = array();
foreach ( $categories as $slug => list( $name, $description ) ) {
	$term = term_exists( $slug, 'product_cat' );
	if ( ! $term ) {
		$term = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug, 'description' => $description ) );
	}
	$category_ids[ $slug ] = is_wp_error( $term ) ? 0 : ( is_array( $term ) ? $term['term_id'] : $term );
}
WP_CLI::log( 'Categories ready: ' . implode( ', ', array_keys( $categories ) ) );

/**
 * ---------------------------------------------------------------
 * Attribute terms (taxonomies themselves are created by LukaSports
 * Core's activation hook — see includes/taxonomies.php)
 * ---------------------------------------------------------------
 */
function lukasports_seed_term_id( $taxonomy, $name ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		WP_CLI::warning( "Taxonomy $taxonomy not registered — is lukasports-core active?" );
		return 0;
	}
	$term = term_exists( $name, $taxonomy );
	if ( ! $term ) {
		$term = wp_insert_term( $name, $taxonomy );
	}
	if ( is_wp_error( $term ) ) {
		return 0;
	}
	return (int) ( is_array( $term ) ? $term['term_id'] : $term );
}

/**
 * The term SLUG for a value, creating the term first if needed —
 * variation attributes are matched by slug, never by display name.
 */
function lukasports_seed_term_slug( $taxonomy, $name ) {
	$term_id = lukasports_seed_term_id( $taxonomy, $name );
	$term    = $term_id ? get_term( $term_id, $taxonomy ) : null;
	return $term && ! is_wp_error( $term ) ? $term->slug : sanitize_title( $name );
}

$sizes     = array( 'S', 'M', 'L', 'XL' );
$colors    = array( 'Trắng', 'Đen', 'Đỏ', 'Xanh Dương', 'Vàng' );
$materials = array( 'Polyester lạnh', 'Thun cá sấu', 'Vải lưới thoáng khí' );
$sports    = array( 'Bóng đá', 'Bóng chuyền', 'Bóng rổ', 'Cầu lông', 'Pickerball' );

foreach ( $sizes as $v ) {
	lukasports_seed_term_id( 'pa_size', $v );
}
foreach ( $colors as $v ) {
	lukasports_seed_term_id( 'pa_color', $v );
}
foreach ( $materials as $v ) {
	lukasports_seed_term_id( 'pa_material', $v );
}
foreach ( $sports as $v ) {
	lukasports_seed_term_id( 'pa_sport', $v );
}
lukasports_seed_term_id( 'pa_season', '2025-2026' );
lukasports_seed_term_id( 'pa_gender', 'Unisex' );

/**
 * ---------------------------------------------------------------
 * Placeholder image generator (GD) — clearly a dev placeholder,
 * never real product photography or any reference-site asset.
 * ---------------------------------------------------------------
 */
function lukasports_seed_draw_sock( $image, $ox, $oy, $fg, $bg ) {
	$points = array(
		$ox - 35, $oy - 280,
		$ox + 35, $oy - 280,
		$ox + 35, $oy + 60,
		$ox + 130, $oy + 90,
		$ox + 130, $oy + 160,
		$ox - 10, $oy + 160,
		$ox - 35, $oy + 90,
	);
	imagefilledpolygon( $image, $points, $fg );
	imagepolygon( $image, $points, $bg );
	imagesetthickness( $image, 6 );
	imageline( $image, $ox - 35, $oy - 210, $ox + 35, $oy - 210, $bg );
	imagesetthickness( $image, 3 );
}

/**
 * Bình nước (water bottle): capped cylinder with a label band.
 */
function lukasports_seed_draw_bottle( $image, $cx, $cy, $fg, $bg ) {
	imagefilledrectangle( $image, $cx - 24, $cy - 310, $cx + 24, $cy - 265, $fg );
	imagefilledrectangle( $image, $cx - 40, $cy - 265, $cx + 40, $cy - 205, $fg );
	$points = array(
		$cx - 110, $cy - 205,
		$cx + 110, $cy - 205,
		$cx + 132, $cy - 150,
		$cx + 132, $cy + 260,
		$cx + 90, $cy + 300,
		$cx - 90, $cy + 300,
		$cx - 132, $cy + 260,
		$cx - 132, $cy - 150,
	);
	imagefilledpolygon( $image, $points, $fg );
	imagepolygon( $image, $points, $bg );
	imagefilledrectangle( $image, $cx - 132, $cy + 10, $cx + 132, $cy + 90, $bg );
}

/**
 * Túi đựng giày (drawstring shoe bag): rounded pouch with a scalloped
 * drawstring top instead of a flat edge, so the silhouette reads as a
 * bag rather than a generic blob.
 */
function lukasports_seed_draw_bag( $image, $cx, $cy, $fg, $bg ) {
	$points = array(
		$cx - 150, $cy - 90,
		$cx + 150, $cy - 90,
		$cx + 172, $cy + 250,
		$cx + 100, $cy + 300,
		$cx - 100, $cy + 300,
		$cx - 172, $cy + 250,
	);
	imagefilledpolygon( $image, $points, $fg );
	imagepolygon( $image, $points, $bg );
	// Scalloped drawstring notches along the top edge — each circle's
	// upper half falls outside the bag and blends into the page
	// background, leaving a semicircle "bite" out of the top edge.
	foreach ( array( -100, 0, 100 ) as $ox ) {
		imagefilledellipse( $image, $cx + $ox, $cy - 90, 70, 60, $bg );
	}
}

/**
 * Băng đô (headband): a flattened ring so it reads as a loop of fabric
 * rather than a solid disc.
 */
function lukasports_seed_draw_headband( $image, $cx, $cy, $fg, $bg ) {
	imagefilledellipse( $image, $cx, $cy, 480, 300, $fg );
	imagefilledellipse( $image, $cx, $cy, 340, 170, $bg );
	imageellipse( $image, $cx, $cy, 480, 300, $bg );
}

function lukasports_seed_draw_rounded_rect( $image, $x1, $y1, $x2, $y2, $radius, $color ) {
	imagefilledrectangle( $image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color );
	imagefilledrectangle( $image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color );
	imagefilledellipse( $image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color );
	imagefilledellipse( $image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color );
	imagefilledellipse( $image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color );
	imagefilledellipse( $image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color );
}

function lukasports_seed_placeholder_image( $label, $hex_bg, $hex_fg, $shape = 'jersey' ) {
	$width  = 900;
	$height = 1125;
	$image  = imagecreatetruecolor( $width, $height );
	imageantialias( $image, true );

	list( $r1, $g1, $b1 ) = sscanf( $hex_bg, '%02x%02x%02x' );
	list( $r2, $g2, $b2 ) = sscanf( $hex_fg, '%02x%02x%02x' );
	$fg = imagecolorallocate( $image, $r2, $g2, $b2 );

	// Soft top-to-bottom backdrop gradient (a per-row blend — GD has no
	// native 2D gradient fill) so the placeholder reads as a lit product
	// shot rather than a flat technical swatch.
	$r_end = max( 0, $r1 - 22 );
	$g_end = max( 0, $g1 - 20 );
	$b_end = max( 0, $b1 - 18 );
	for ( $y = 0; $y < $height; $y++ ) {
		$t   = $y / $height;
		$row = imagecolorallocate(
			$image,
			(int) ( $r1 + ( $r_end - $r1 ) * $t ),
			(int) ( $g1 + ( $g_end - $g1 ) * $t ),
			(int) ( $b1 + ( $b_end - $b1 ) * $t )
		);
		imageline( $image, 0, $y, $width, $y, $row );
	}

	// A raised card panel (rounded corners + soft contact shadow) behind
	// the icon, rather than the icon sitting directly on the gradient —
	// the same "product on a card" language as the site's real photography.
	$margin       = 60;
	$radius       = 32;
	$panel_bottom = $height - 90;
	$shadow       = imagecolorallocatealpha( $image, 0, 0, 0, 105 );
	imagefilledellipse( $image, (int) ( $width / 2 ), $panel_bottom + 18, $width - $margin * 2 - 40, 60, $shadow );

	$bg = imagecolorallocate( $image, $r1, $g1, $b1 );
	lukasports_seed_draw_rounded_rect( $image, $margin, $margin, $width - $margin, $panel_bottom, $radius, $bg );

	$cx = (int) ( $width / 2 );
	$cy = (int) ( $margin + ( $panel_bottom - $margin ) / 2 ) - 20;
	imagesetthickness( $image, 4 );

	if ( 'socks' === $shape ) {
		lukasports_seed_draw_sock( $image, $cx - 90, $cy, $fg, $bg );
		lukasports_seed_draw_sock( $image, $cx + 90, $cy, $fg, $bg );
	} elseif ( 'bottle' === $shape ) {
		lukasports_seed_draw_bottle( $image, $cx, $cy, $fg, $bg );
	} elseif ( 'bag' === $shape ) {
		lukasports_seed_draw_bag( $image, $cx, $cy, $fg, $bg );
	} elseif ( 'headband' === $shape ) {
		lukasports_seed_draw_headband( $image, $cx, $cy, $fg, $bg );
	} elseif ( 'tank' === $shape ) {
		$points = array(
			$cx - 140, $cy - 260,
			$cx - 40, $cy - 300,
			$cx + 40, $cy - 300,
			$cx + 140, $cy - 260,
			$cx + 108, $cy - 170,
			$cx + 150, $cy + 300,
			$cx - 150, $cy + 300,
			$cx - 108, $cy - 170,
		);
		// Filled garment silhouette (not just an outline) with a thin
		// edge stroke for definition — this is what reads as an actual
		// product shape instead of a technical wireframe drawing.
		imagefilledpolygon( $image, $points, $fg );
		imagepolygon( $image, $points, $bg );
	} else {
		$points = array(
			$cx - 190, $cy - 260,
			$cx - 90, $cy - 300,
			$cx - 45, $cy - 240,
			$cx + 45, $cy - 240,
			$cx + 90, $cy - 300,
			$cx + 190, $cy - 260,
			$cx + 230, $cy - 140,
			$cx + 150, $cy - 96,
			$cx + 150, $cy + 300,
			$cx - 150, $cy + 300,
			$cx - 150, $cy - 96,
			$cx - 230, $cy - 140,
		);
		// Filled garment silhouette + thin edge stroke, same treatment
		// as the tank top above.
		imagefilledpolygon( $image, $points, $fg );
		imagepolygon( $image, $points, $bg );

		// Diagonal accent stripe across the chest, in the contrasting
		// $bg tone so it actually shows up against the now-solid body
		// (previously both were the same color, invisible against an
		// outline-only shirt).
		imagefilledpolygon( $image, array(
			$cx - 150, $cy - 60,
			$cx - 80, $cy - 60,
			$cx + 150, $cy + 10,
			$cx + 150, $cy + 50,
			$cx - 150, $cy - 20,
		), $bg );
	}

	// Brand accent bar — same red used for buttons/badges/category art,
	// so a product tile reads as part of the same visual system instead
	// of a generic dev-doodle placeholder.
	$accent = imagecolorallocate( $image, 0x14, 0x6e, 0xf5 );
	imagefilledrectangle( $image, 0, $height - 8, $width, $height, $accent );

	ob_start();
	imagejpeg( $image, null, 88 );
	$data = ob_get_clean();
	imagedestroy( $image );

	$filename = 'lukasports-dev-' . sanitize_title( $label ) . '-' . wp_generate_password( 6, false ) . '.jpg';
	$upload   = wp_upload_bits( $filename, null, $data );

	if ( $upload['error'] ) {
		WP_CLI::warning( 'Image upload failed: ' . $upload['error'] );
		return 0;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => $label,
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	update_post_meta( $attachment_id, '_lukasports_seed', '1' );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $label );

	return $attachment_id;
}

/**
 * Rule-based promotion lines instead of hand-typing them for every one
 * of ~40 products — varies by whether the product is on sale, supports
 * printing, or has size/color variations, so it still reads as
 * product-specific rather than one repeated banner.
 *
 * @return string[]
 */
function lukasports_seed_promotions_for( $data ) {
	$promos = array();

	if ( ! empty( $data['sale'] ) && $data['regular'] > 0 ) {
		$percent  = (int) round( ( 1 - $data['sale'] / $data['regular'] ) * 100 );
		$promos[] = sprintf( 'Giảm %d%% khi đặt trong tháng này', $percent );
	}

	if ( ! empty( $data['printing'] ) ) {
		$promos[] = 'Miễn phí in tên + số cho đơn từ 5 sản phẩm';
	}

	if ( 'variable' === $data['type'] ) {
		$promos[] = 'Đổi size miễn phí trong 7 ngày nếu không vừa';
	}

	$promos[] = 'Freeship nội thành cho đơn từ 500.000đ';

	return array_slice( $promos, 0, 3 );
}

/**
 * Appends the same two closing sections (size guidance + return
 * policy) that a real product page would carry, so descriptions read
 * as complete product info rather than a single marketing sentence.
 */
function lukasports_seed_extended_description( $desc ) {
	return $desc . "\n\n" .
		'Hướng dẫn chọn size: Nếu bạn phân vân giữa hai size liền kề, nên chọn size lớn hơn để thoải mái khi vận động mạnh. Xem bảng thông số chi tiết ở tab "Hướng dẫn chọn size" bên dưới, hoặc để lại số đo để được tư vấn size phù hợp.' . "\n\n" .
		'Chính sách đổi trả: Hỗ trợ đổi size miễn phí trong vòng 7 ngày kể từ ngày nhận hàng, áp dụng cho sản phẩm còn nguyên tem mác và chưa qua sử dụng. Liên hệ hotline hoặc Zalo để được hỗ trợ đổi trả nhanh nhất.';
}

/**
 * ---------------------------------------------------------------
 * Product definitions
 * ---------------------------------------------------------------
 */

// Fallback values for any key a shorter product entry below omits —
// combined via the `+` array-union operator, which keeps the entry's
// own value for any key it does specify.
$default_product = array(
	'sale'       => 0,
	'type'       => 'simple',
	'material'   => 'Polyester lạnh',
	'printing'   => true,
	'benefits'   => array( 'Chất liệu thể thao thoáng mát', 'Form áo chuẩn thi đấu', 'In tên số theo yêu cầu' ),
);

$products = array(
	array(
		'name'       => 'Áo Bóng Đá CLB Sông Hồng FC – Sân Nhà',
		'cat'        => 'ao-bong-da',
		'regular'    => 259000,
		'sale'       => 219000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Đỏ',
		'material'   => 'Polyester lạnh',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Chất liệu thoáng mát, khô nhanh', 'Form áo chuẩn thi đấu', 'In tên số theo yêu cầu' ),
		'printing'   => true,
		'short_desc' => 'Áo đấu sân nhà CLB Sông Hồng FC, phối màu đỏ trắng, vải Polyester lạnh 2 lớp.',
		'desc'       => 'Áo bóng đá CLB Sông Hồng FC phiên bản sân nhà, thiết kế bám sát phong cách thi đấu chuyên nghiệp. Vải Polyester lạnh 2 lớp, thấm hút mồ hôi nhanh, form áo rộng vừa phải phù hợp vận động mạnh. Hỗ trợ in tên và số cầu thủ theo yêu cầu.',
	),
	array(
		'name'       => 'Áo Bóng Đá CLB Sông Hồng FC – Sân Khách',
		'cat'        => 'ao-bong-da',
		'regular'    => 259000,
		'sale'       => 0,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Xanh Dương',
		'material'   => 'Polyester lạnh',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Chất liệu thoáng mát, khô nhanh', 'Form áo chuẩn thi đấu', 'In tên số theo yêu cầu' ),
		'printing'   => true,
		'short_desc' => 'Áo đấu sân khách CLB Sông Hồng FC, phối màu xanh dương, vải Polyester lạnh 2 lớp.',
		'desc'       => 'Phiên bản sân khách của CLB Sông Hồng FC, tông xanh dương chủ đạo. Cùng chất liệu và form dáng với bản sân nhà, phù hợp mặc thi đấu hoặc mặc thường ngày.',
	),
	array(
		'name'       => 'Áo Đấu Đội Tuyển Đại Việt – Sân Nhà',
		'cat'        => 'ao-bong-da',
		'regular'    => 279000,
		'sale'       => 229000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Đỏ',
		'material'   => 'Polyester lạnh',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Bản phối màu đội tuyển', 'Vải nhẹ, co giãn 4 chiều', 'In tên số theo yêu cầu' ),
		'printing'   => true,
		'short_desc' => 'Áo đấu đội tuyển Đại Việt sân nhà, tông đỏ truyền thống.',
		'desc'       => 'Mẫu áo lấy cảm hứng từ màu cờ sắc áo đội tuyển quốc gia, tông đỏ chủ đạo phối chi tiết vàng. Vải nhẹ, co giãn 4 chiều, phù hợp cổ vũ và thi đấu phong trào.',
	),
	array(
		'name'       => 'Áo Thiết Kế Sọc Chớp Zento',
		'cat'        => 'ao-bong-da-thiet-ke',
		'regular'    => 249000,
		'sale'       => 199000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Xanh Dương', 'Đen' ) ),
		'material'   => 'Thun cá sấu',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Thiết kế độc quyền LukaSports', 'Vải 4 chiều co giãn', 'Nhận đặt từ 10 áo' ),
		'printing'   => true,
		'short_desc' => 'Mẫu thiết kế độc quyền LukaSports, họa tiết sọc chớp hiện đại.',
		'desc'       => 'Áo Zento là mẫu thiết kế độc quyền của LukaSports với họa tiết sọc chớp bất đối xứng, phù hợp cho team muốn một bộ nhận diện khác biệt. Có 2 tuỳ chọn màu, nhận đặt từ 10 áo trở lên kèm in tên số.',
	),
	array(
		'name'       => 'Áo Thiết Kế Họa Tiết Rồng Việt',
		'cat'        => 'ao-bong-da-thiet-ke',
		'regular'    => 249000,
		'sale'       => 0,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Vàng',
		'material'   => 'Thun cá sấu',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Họa tiết lấy cảm hứng văn hoá Việt', 'Vải 4 chiều co giãn', 'In tên số theo yêu cầu' ),
		'printing'   => true,
		'short_desc' => 'Thiết kế độc quyền họa tiết rồng thời Lý, phối vàng đỏ.',
		'desc'       => 'Mẫu áo lấy cảm hứng từ hoa văn rồng thời Lý, phối vàng đỏ nổi bật. Thích hợp cho các đội muốn một mẫu áo có câu chuyện văn hoá riêng.',
	),
	array(
		'name'       => 'Áo Đồng Phục Team Phượng Hoàng',
		'cat'        => 'ao-team',
		'regular'    => 219000,
		'sale'       => 189000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Vàng', 'Đen' ) ),
		'material'   => 'Polyester lạnh',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Giá tốt cho đơn từ 10 áo', 'Thêu/in logo công ty, CLB', 'Giao hàng 3-5 ngày' ),
		'printing'   => true,
		'short_desc' => 'Đồng phục cho team phong trào/công ty, đặt số lượng từ nhỏ đến lớn.',
		'desc'       => 'Mẫu đồng phục linh hoạt cho team phong trào, công ty hoặc giải đấu nội bộ. Hỗ trợ thêu hoặc in logo riêng, nhận đơn từ số lượng nhỏ với thời gian giao 3-5 ngày làm việc.',
	),
	array(
		'name'       => 'Áo Bóng Chuyền Nam Thăng Long',
		'cat'        => 'ao-bong-chuyen',
		'regular'    => 189000,
		'sale'       => 0,
		'type'       => 'simple',
		'color'      => 'Xanh Dương',
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Bóng chuyền',
		'benefits'   => array( 'Vải lưới thoáng khí', 'Co giãn tốt khi bật nhảy', 'In tên số theo yêu cầu' ),
		'printing'   => true,
		'short_desc' => 'Áo bóng chuyền nam, vải lưới thoáng khí, co giãn tốt.',
		'desc'       => 'Thiết kế cho vận động viên bóng chuyền cần độ thoáng khí và co giãn cao khi bật nhảy, đập bóng. Cổ tròn, tay raglan giảm cản vận động.',
	),
	array(
		'name'       => 'Áo Bóng Rổ Streetball Cobra',
		'cat'        => 'ao-bong-ro',
		'regular'    => 229000,
		'sale'       => 179000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Đen', 'Trắng' ) ),
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Bóng rổ',
		'benefits'   => array( 'Phong cách streetball', 'Vải lưới thoáng khí toàn phần', 'In số cầu thủ theo yêu cầu' ),
		'printing'   => true,
		'short_desc' => 'Áo bóng rổ phong cách streetball, vải lưới thoáng khí toàn phần.',
		'desc'       => 'Mẫu áo bóng rổ hai màu tương phản mạnh, phong cách streetball. Vải lưới toàn phần giúp thoáng khí tối đa khi thi đấu ngoài trời.',
	),
	array(
		'name'       => 'Áo Cầu Lông Air Light',
		'cat'        => 'ao-cau-long',
		'regular'    => 179000,
		'sale'       => 149000,
		'type'       => 'simple',
		'color'      => 'Trắng',
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Cầu lông',
		'benefits'   => array( 'Trọng lượng nhẹ, khô nhanh', 'Thoáng khí toàn thân', 'Form áo thi đấu chuẩn' ),
		'printing'   => true,
		'short_desc' => 'Áo cầu lông siêu nhẹ, khô nhanh, phù hợp thi đấu và tập luyện.',
		'desc'       => 'Áo cầu lông Air Light được làm từ vải siêu nhẹ, khô nhanh, phù hợp cho cả thi đấu và tập luyện hàng ngày. Đường cắt may ôm nhẹ, không gây cản trở khi di chuyển.',
	),
	array(
		'name'       => 'Bộ Tất Thể Thao LukaSports (3 đôi)',
		'cat'        => 'phu-kien',
		'regular'    => 99000,
		'sale'       => 0,
		'type'       => 'simple',
		'material'   => 'Thun cá sấu',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Vải cotton pha, thấm hút tốt', 'Ôm cổ chân, không tuột gót' ),
		'printing'   => false,
		'short_desc' => 'Bộ 3 đôi tất thể thao, ôm cổ chân, thấm hút tốt.',
		'desc'       => 'Bộ 3 đôi tất thể thao chất liệu cotton pha, phù hợp mặc cùng giày thi đấu hoặc giày chạy hàng ngày. Cổ tất ôm vừa, hạn chế tuột gót khi vận động mạnh.',
	),

	// ---- Áo Bóng Đá: thêm 3 mẫu ----
	array(
		'name'       => 'Áo Bóng Đá CLB Hải Âu – Sân Nhà',
		'cat'        => 'ao-bong-da',
		'regular'    => 249000,
		'sale'       => 209000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Xanh Dương',
		'sport'      => 'Bóng đá',
		'short_desc' => 'Áo đấu CLB Hải Âu sân nhà, tông xanh dương – trắng.',
		'desc'       => 'Áo đấu CLB Hải Âu phiên bản sân nhà, phối xanh dương chủ đạo cùng chi tiết trắng. Vải Polyester lạnh nhẹ, thoáng khí, form chuẩn thi đấu.',
	) + $default_product,
	array(
		'name'       => 'Áo Đấu Futsal Kim Ngưu',
		'cat'        => 'ao-bong-da',
		'regular'    => 219000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Vàng', 'Đen' ) ),
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Vải nhẹ chuyên futsal', 'Co giãn 4 chiều', 'In tên số theo yêu cầu' ),
		'short_desc' => 'Áo futsal Kim Ngưu, vải nhẹ co giãn 4 chiều.',
		'desc'       => 'Thiết kế riêng cho các đội futsal phong trào, vải nhẹ co giãn 4 chiều giúp thoải mái khi di chuyển liên tục trong không gian nhỏ.',
	) + $default_product,
	array(
		'name'       => 'Áo Bóng Đá Không Logo Basic',
		'cat'        => 'ao-bong-da',
		'regular'    => 189000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Trắng', 'Đen', 'Đỏ' ) ),
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Không logo — tự do in ấn theo ý muốn', 'Vải Polyester lạnh cơ bản', 'Phù hợp đặt số lượng lớn' ),
		'short_desc' => 'Áo bóng đá trơn không logo, dễ in ấn theo yêu cầu riêng.',
		'desc'       => 'Mẫu áo cơ bản không có họa tiết sẵn, phù hợp cho các đội muốn tự thiết kế logo/họa tiết riêng hoặc chỉ cần in tên số đơn giản. Giá tốt khi đặt số lượng lớn.',
	) + $default_product,

	// ---- Áo Bóng Đá Thiết Kế: thêm 3 mẫu ----
	array(
		'name'       => 'Áo Thiết Kế Hoa Văn Sóng Biển',
		'cat'        => 'ao-bong-da-thiet-ke',
		'regular'    => 259000,
		'sale'       => 219000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Xanh Dương',
		'material'   => 'Thun cá sấu',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Họa tiết độc quyền LukaSports', 'Cảm hứng từ sóng biển miền Trung', 'In tên số theo yêu cầu' ),
		'short_desc' => 'Thiết kế độc quyền họa tiết sóng biển, tông xanh dương gradient.',
		'desc'       => 'Mẫu áo lấy cảm hứng từ những con sóng miền Trung, họa tiết gradient xanh dương độc quyền của LukaSports — dành cho team muốn một bộ nhận diện khác biệt trên sân.',
	) + $default_product,
	array(
		'name'       => 'Áo Thiết Kế Camo Rừng',
		'cat'        => 'ao-bong-da-thiet-ke',
		'regular'    => 259000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Đen',
		'material'   => 'Thun cá sấu',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Họa tiết camo độc quyền', 'Phong cách cá tính, khác biệt', 'In tên số theo yêu cầu' ),
		'short_desc' => 'Thiết kế camo rừng cá tính, phối tông xanh rêu – đen.',
		'desc'       => 'Mẫu áo phong cách quân đội với họa tiết camo rừng, phối tông xanh rêu và đen. Phù hợp cho các đội muốn hình ảnh mạnh mẽ, cá tính trên sân.',
	) + $default_product,
	array(
		'name'       => 'Áo Thiết Kế Gradient Hoàng Hôn',
		'cat'        => 'ao-bong-da-thiet-ke',
		'regular'    => 259000,
		'sale'       => 229000,
		'type'       => 'simple',
		'color'      => 'Đỏ',
		'material'   => 'Thun cá sấu',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Họa tiết gradient độc quyền', 'Tông màu hoàng hôn nổi bật', 'In tên số theo yêu cầu' ),
		'short_desc' => 'Họa tiết gradient cam – đỏ lấy cảm hứng hoàng hôn.',
		'desc'       => 'Mẫu áo với hiệu ứng chuyển màu cam sang đỏ lấy cảm hứng từ hoàng hôn, tạo điểm nhấn nổi bật trên sân đấu.',
	) + $default_product,

	// ---- Áo Team: thêm 4 mẫu ----
	array(
		'name'       => 'Đồng Phục Team Bão Đêm',
		'cat'        => 'ao-team',
		'regular'    => 219000,
		'sale'       => 179000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Đen', 'Xanh Dương' ) ),
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Giá tốt cho đơn từ 10 áo', 'Thêu/in logo team', 'Giao hàng 3-5 ngày' ),
		'short_desc' => 'Đồng phục team phong trào, tông tối cá tính.',
		'desc'       => 'Mẫu đồng phục dành cho các team đá đêm/phong trào, tông màu tối cá tính. Hỗ trợ thêu hoặc in logo riêng, nhận đơn từ số lượng nhỏ.',
	) + $default_product,
	array(
		'name'       => 'Đồng Phục Giải Nội Bộ Doanh Nghiệp',
		'cat'        => 'ao-team',
		'regular'    => 199000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Xanh Dương',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Phù hợp giải đấu công ty', 'In logo doanh nghiệp', 'Đặt số lượng lớn có ưu đãi' ),
		'short_desc' => 'Đồng phục cho giải đấu thể thao nội bộ doanh nghiệp.',
		'desc'       => 'Thiết kế trung tính, dễ phối cùng logo và màu nhận diện doanh nghiệp — phù hợp cho các giải đấu thể thao nội bộ, team building công ty.',
	) + $default_product,
	array(
		'name'       => 'Đồng Phục Lớp Học – Mẫu Basic',
		'cat'        => 'ao-team',
		'regular'    => 149000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Trắng', 'Đỏ', 'Xanh Dương' ) ),
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Giá tốt cho lớp học, nhóm bạn', 'Nhiều màu lựa chọn', 'In tên lớp/tên riêng theo yêu cầu' ),
		'short_desc' => 'Đồng phục lớp học/nhóm bạn, giá tốt, nhiều màu.',
		'desc'       => 'Mẫu đồng phục cơ bản, giá tốt cho các lớp học hoặc nhóm bạn đặt đồng phục dã ngoại, thể thao. Nhiều màu lựa chọn, hỗ trợ in tên riêng.',
	) + $default_product,
	array(
		'name'       => 'Đồng Phục Team Nữ Hoa Hồng',
		'cat'        => 'ao-team',
		'regular'    => 219000,
		'sale'       => 189000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Đỏ',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Form áo nữ ôm nhẹ', 'Vải mềm mịn, thoáng khí', 'In tên số theo yêu cầu' ),
		'short_desc' => 'Đồng phục team nữ, form ôm nhẹ, tông đỏ hồng.',
		'desc'       => 'Thiết kế riêng cho các team thể thao nữ, form áo ôm nhẹ hơn bản nam, vải mềm mịn và thoáng khí, tông đỏ hồng nữ tính.',
	) + $default_product,

	// ---- Áo Bóng Chuyền: thêm 4 mẫu ----
	array(
		'name'       => 'Áo Bóng Chuyền Nữ Hải Đăng',
		'cat'        => 'ao-bong-chuyen',
		'regular'    => 189000,
		'sale'       => 159000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Trắng',
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Bóng chuyền',
		'benefits'   => array( 'Form áo nữ, ôm nhẹ', 'Vải lưới thoáng khí', 'In tên số theo yêu cầu' ),
		'short_desc' => 'Áo bóng chuyền nữ, form ôm nhẹ, tông trắng thanh lịch.',
		'desc'       => 'Thiết kế riêng cho các đội bóng chuyền nữ, form áo ôm nhẹ, chất liệu lưới thoáng khí giúp thoải mái khi di chuyển và bật nhảy.',
	) + $default_product,
	array(
		'name'       => 'Áo Bóng Chuyền Libero',
		'cat'        => 'ao-bong-chuyen',
		'regular'    => 189000,
		'type'       => 'simple',
		'color'      => 'Vàng',
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Bóng chuyền',
		'benefits'   => array( 'Màu riêng biệt cho vị trí Libero', 'Vải lưới thoáng khí', 'In số theo yêu cầu' ),
		'short_desc' => 'Áo Libero màu riêng biệt theo đúng luật thi đấu.',
		'desc'       => 'Mẫu áo dành riêng cho vị trí chuyền hai tự do (Libero), màu sắc khác biệt so với đồng đội theo đúng quy định thi đấu bóng chuyền.',
	) + $default_product,
	array(
		'name'       => 'Áo Bóng Chuyền Bãi Biển',
		'cat'        => 'ao-bong-chuyen',
		'regular'    => 179000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Xanh Dương', 'Vàng' ) ),
		'sport'      => 'Bóng chuyền',
		'benefits'   => array( 'Chất liệu khô nhanh', 'Chống nắng nhẹ', 'Phù hợp thi đấu ngoài trời' ),
		'short_desc' => 'Áo bóng chuyền bãi biển, khô nhanh, chống nắng nhẹ.',
		'desc'       => 'Thiết kế cho bóng chuyền bãi biển, chất liệu khô nhanh và có khả năng chống nắng nhẹ, phù hợp thi đấu ngoài trời nắng gắt.',
	) + $default_product,
	array(
		'name'       => 'Áo Bóng Chuyền Không Logo Basic',
		'cat'        => 'ao-bong-chuyen',
		'regular'    => 159000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Trắng', 'Đen' ) ),
		'sport'      => 'Bóng chuyền',
		'benefits'   => array( 'Không logo — tự do in ấn', 'Giá tốt cho đơn số lượng lớn', 'In tên số theo yêu cầu' ),
		'short_desc' => 'Áo bóng chuyền trơn không logo, giá tốt số lượng lớn.',
		'desc'       => 'Mẫu áo cơ bản không họa tiết sẵn, phù hợp các đội muốn tự thiết kế hoặc chỉ cần in tên số đơn giản với chi phí tốt.',
	) + $default_product,

	// ---- Áo Bóng Rổ: thêm 4 mẫu ----
	array(
		'name'       => 'Áo Bóng Rổ Storm Warriors',
		'cat'        => 'ao-bong-ro',
		'regular'    => 229000,
		'sale'       => 189000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Xanh Dương', 'Trắng' ) ),
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Bóng rổ',
		'benefits'   => array( 'Phong cách thi đấu chuyên nghiệp', 'Vải lưới thoáng khí toàn phần', 'In số cầu thủ theo yêu cầu' ),
		'short_desc' => 'Áo bóng rổ Storm Warriors, phong cách thi đấu chuyên nghiệp.',
		'desc'       => 'Mẫu áo bóng rổ phối màu tương phản mạnh, phong cách thi đấu chuyên nghiệp, chất liệu lưới thoáng khí toàn phần.',
	) + $default_product,
	array(
		'name'       => 'Áo Bóng Rổ Nữ Panther',
		'cat'        => 'ao-bong-ro',
		'regular'    => 219000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Đen',
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Bóng rổ',
		'benefits'   => array( 'Form áo nữ ôm nhẹ', 'Vải lưới thoáng khí', 'In số theo yêu cầu' ),
		'short_desc' => 'Áo bóng rổ nữ Panther, form ôm nhẹ, tông đen cá tính.',
		'desc'       => 'Thiết kế riêng cho các đội bóng rổ nữ, form áo ôm nhẹ hơn bản nam, tông đen cá tính phối chi tiết tương phản.',
	) + $default_product,
	array(
		'name'       => 'Áo Bóng Rổ 3x3 Basic',
		'cat'        => 'ao-bong-ro',
		'regular'    => 179000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Đỏ', 'Đen', 'Trắng' ) ),
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Bóng rổ',
		'benefits'   => array( 'Phù hợp thi đấu 3x3 phong trào', 'Giá tốt', 'In số theo yêu cầu' ),
		'short_desc' => 'Áo bóng rổ cơ bản, phù hợp thi đấu 3x3 phong trào.',
		'desc'       => 'Mẫu áo bóng rổ đơn giản, giá tốt, phù hợp cho các giải 3x3 phong trào hoặc tập luyện hàng ngày.',
	) + $default_product,
	array(
		'name'       => 'Bộ Áo Quần Bóng Rổ Thi Đấu',
		'cat'        => 'ao-bong-ro',
		'regular'    => 289000,
		'sale'       => 249000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Xanh Dương',
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Bóng rổ',
		'benefits'   => array( 'Bộ đầy đủ áo + quần thi đấu', 'Vải lưới thoáng khí toàn phần', 'In số theo yêu cầu' ),
		'short_desc' => 'Bộ đầy đủ áo và quần bóng rổ thi đấu, đồng bộ màu sắc.',
		'desc'       => 'Trọn bộ áo và quần bóng rổ thi đấu, đồng bộ màu sắc và chất liệu, tiện lợi cho các đội muốn trang bị nhanh cho cả team.',
	) + $default_product,

	// ---- Áo Cầu Lông: thêm 4 mẫu ----
	array(
		'name'       => 'Áo Cầu Lông Nữ Breeze',
		'cat'        => 'ao-cau-long',
		'regular'    => 179000,
		'sale'       => 149000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Trắng',
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Cầu lông',
		'benefits'   => array( 'Form áo nữ nhẹ nhàng', 'Thoáng khí, khô nhanh', 'Form áo thi đấu chuẩn' ),
		'short_desc' => 'Áo cầu lông nữ Breeze, nhẹ nhàng, thoáng khí.',
		'desc'       => 'Thiết kế riêng cho vận động viên cầu lông nữ, chất liệu nhẹ và thoáng khí, tông trắng thanh lịch dễ phối trang phục.',
	) + $default_product,
	array(
		'name'       => 'Áo Cầu Lông Polo Thi Đấu',
		'cat'        => 'ao-cau-long',
		'regular'    => 199000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Trắng', 'Xanh Dương' ) ),
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Cầu lông',
		'benefits'   => array( 'Cổ polo lịch sự', 'Thoáng khí, khô nhanh', 'Phù hợp thi đấu và giao lưu' ),
		'short_desc' => 'Áo cầu lông cổ polo, lịch sự, phù hợp thi đấu và giao lưu.',
		'desc'       => 'Mẫu áo cầu lông cổ polo lịch sự hơn form cổ tròn, vẫn giữ khả năng thoáng khí và khô nhanh cho vận động liên tục.',
	) + $default_product,
	array(
		'name'       => 'Áo Cầu Lông Không Logo Basic',
		'cat'        => 'ao-cau-long',
		'regular'    => 139000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Trắng', 'Đen' ) ),
		'sport'      => 'Cầu lông',
		'material'   => 'Vải lưới thoáng khí',
		'benefits'   => array( 'Không logo — tự do in ấn', 'Giá tốt cho đơn số lượng lớn', 'Thoáng khí, khô nhanh' ),
		'short_desc' => 'Áo cầu lông trơn không logo, giá tốt số lượng lớn.',
		'desc'       => 'Mẫu áo cơ bản không họa tiết sẵn, phù hợp câu lạc bộ cầu lông muốn tự thiết kế hoặc chỉ cần in tên/logo riêng.',
	) + $default_product,
	array(
		'name'       => 'Bộ Áo Quần Cầu Lông Thi Đấu',
		'cat'        => 'ao-cau-long',
		'regular'    => 259000,
		'sale'       => 219000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Xanh Dương',
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Cầu lông',
		'benefits'   => array( 'Bộ đầy đủ áo + quần thi đấu', 'Thoáng khí, khô nhanh', 'Form áo thi đấu chuẩn' ),
		'short_desc' => 'Bộ đầy đủ áo và quần cầu lông thi đấu, đồng bộ màu sắc.',
		'desc'       => 'Trọn bộ áo và quần cầu lông thi đấu, đồng bộ màu sắc và chất liệu, phù hợp cho giải đấu hoặc câu lạc bộ.',
	) + $default_product,

	// ---- Áo Pickerball: danh mục mới, 5 mẫu ----
	array(
		'name'       => 'Áo Pickerball Ace Basic',
		'cat'        => 'ao-pickerball',
		'regular'    => 199000,
		'sale'       => 169000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Trắng',
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Pickerball',
		'benefits'   => array( 'Vải thoáng khí, khô nhanh', 'Co giãn tốt khi di chuyển ngang', 'In tên số theo yêu cầu' ),
		'short_desc' => 'Áo pickerball cơ bản, thoáng khí, khô nhanh.',
		'desc'       => 'Mẫu áo pickerball cơ bản, chất liệu thoáng khí và khô nhanh, phù hợp cho các buổi thi đấu và giao lưu pickerball ngày càng phổ biến.',
	) + $default_product,
	array(
		'name'       => 'Áo Pickerball Nữ Sunrise',
		'cat'        => 'ao-pickerball',
		'regular'    => 209000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Vàng',
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Pickerball',
		'benefits'   => array( 'Form áo nữ, ôm nhẹ', 'Tông màu tươi sáng', 'Thoáng khí, khô nhanh' ),
		'short_desc' => 'Áo pickerball nữ Sunrise, tông vàng tươi sáng.',
		'desc'       => 'Thiết kế riêng cho các chị em chơi pickerball, form áo ôm nhẹ, tông màu vàng tươi sáng năng động.',
	) + $default_product,
	array(
		'name'       => 'Áo Polo Pickerball Thi Đấu',
		'cat'        => 'ao-pickerball',
		'regular'    => 229000,
		'sale'       => 199000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Trắng', 'Xanh Dương' ) ),
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Pickerball',
		'benefits'   => array( 'Cổ polo lịch sự', 'Phù hợp thi đấu và giao lưu CLB', 'In logo CLB theo yêu cầu' ),
		'short_desc' => 'Áo polo pickerball, lịch sự, phù hợp thi đấu CLB.',
		'desc'       => 'Mẫu áo cổ polo dành cho các buổi thi đấu hoặc giao lưu CLB pickerball, form áo gọn gàng và lịch sự hơn form cổ tròn.',
	) + $default_product,
	array(
		'name'       => 'Bộ Áo Quần Pickerball Thi Đấu',
		'cat'        => 'ao-pickerball',
		'regular'    => 289000,
		'sale'       => 249000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes ),
		'color'      => 'Xanh Dương',
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Pickerball',
		'benefits'   => array( 'Bộ đầy đủ áo + quần thi đấu', 'Đồng bộ màu sắc', 'In tên số theo yêu cầu' ),
		'short_desc' => 'Bộ đầy đủ áo và quần pickerball, đồng bộ màu sắc.',
		'desc'       => 'Trọn bộ áo và quần pickerball thi đấu, đồng bộ màu sắc và chất liệu — phù hợp cho CLB hoặc nhóm chơi cố định.',
	) + $default_product,
	array(
		'name'       => 'Áo Pickerball Không Logo Basic',
		'cat'        => 'ao-pickerball',
		'regular'    => 169000,
		'type'       => 'variable',
		'variation'  => array( 'size' => $sizes, 'color' => array( 'Trắng', 'Đen' ) ),
		'sport'      => 'Pickerball',
		'material'   => 'Vải lưới thoáng khí',
		'benefits'   => array( 'Không logo — tự do in ấn', 'Giá tốt cho đơn số lượng lớn', 'Thoáng khí, khô nhanh' ),
		'short_desc' => 'Áo pickerball trơn không logo, giá tốt số lượng lớn.',
		'desc'       => 'Mẫu áo pickerball cơ bản không họa tiết sẵn, phù hợp nhóm/CLB muốn tự thiết kế hoặc chỉ cần in tên riêng.',
	) + $default_product,

	// ---- Phụ Kiện: thêm 4 mẫu ----
	array(
		'name'       => 'Bộ Tất Thể Thao Cổ Cao',
		'cat'        => 'phu-kien',
		'regular'    => 109000,
		'type'       => 'simple',
		'material'   => 'Thun cá sấu',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Cổ cao, bảo vệ ống chân', 'Vải cotton pha, thấm hút tốt' ),
		'printing'   => false,
		'short_desc' => 'Tất thể thao cổ cao, bảo vệ ống chân khi thi đấu.',
		'desc'       => 'Tất thể thao cổ cao, phù hợp mặc cùng ốp ống chân khi thi đấu bóng đá, chất liệu cotton pha thấm hút tốt.',
	) + $default_product,
	array(
		'name'       => 'Băng Đô Thể Thao LukaSports',
		'cat'        => 'phu-kien',
		'shape'      => 'headband',
		'regular'    => 39000,
		'type'       => 'simple',
		'material'   => 'Thun cá sấu',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Thấm hút mồ hôi tốt', 'Co giãn ôm đầu vừa vặn' ),
		'printing'   => false,
		'short_desc' => 'Băng đô thể thao thấm hút mồ hôi, co giãn tốt.',
		'desc'       => 'Băng đô thể thao nhỏ gọn, giúp thấm hút mồ hôi vùng đầu khi vận động mạnh, chất liệu co giãn ôm vừa vặn.',
	) + $default_product,
	array(
		'name'       => 'Túi Đựng Giày Thể Thao',
		'cat'        => 'phu-kien',
		'shape'      => 'bag',
		'regular'    => 79000,
		'type'       => 'simple',
		'material'   => 'Vải lưới thoáng khí',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Ngăn riêng đựng giày, tránh bám bẩn balo', 'Vải lưới thoáng khí' ),
		'printing'   => false,
		'short_desc' => 'Túi đựng giày thể thao, ngăn riêng gọn gàng.',
		'desc'       => 'Túi đựng giày nhỏ gọn, ngăn riêng biệt giúp giày không làm bẩn các vật dụng khác trong balo, chất liệu lưới thoáng khí.',
	) + $default_product,
	array(
		'name'       => 'Bình Nước Thể Thao 750ml',
		'cat'        => 'phu-kien',
		'shape'      => 'bottle',
		'regular'    => 89000,
		'type'       => 'simple',
		'material'   => 'Thun cá sấu',
		'sport'      => 'Bóng đá',
		'benefits'   => array( 'Dung tích 750ml', 'Chất liệu an toàn, dễ vệ sinh' ),
		'printing'   => false,
		'short_desc' => 'Bình nước thể thao 750ml, tiện lợi mang theo khi tập luyện.',
		'desc'       => 'Bình nước thể thao dung tích 750ml, thiết kế tiện lợi mang theo khi tập luyện hoặc thi đấu, dễ vệ sinh.',
	) + $default_product,
);

/**
 * ---------------------------------------------------------------
 * Create products
 * ---------------------------------------------------------------
 */
$created = 0;

foreach ( $products as $data ) {
	$is_variable = 'variable' === $data['type'];
	$product     = $is_variable ? new WC_Product_Variable() : new WC_Product_Simple();

	$product->set_name( $data['name'] );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_short_description( $data['short_desc'] );
	$product->set_description( lukasports_seed_extended_description( $data['desc'] ) );
	$product->set_category_ids( array( $category_ids[ $data['cat'] ] ) );
	$product->set_manage_stock( false );
	$product->set_stock_status( 'instock' );

	if ( ! $is_variable ) {
		$product->set_regular_price( (string) $data['regular'] );
		if ( ! empty( $data['sale'] ) ) {
			$product->set_sale_price( (string) $data['sale'] );
		}
	}

	// Descriptive (non-variation) attributes: Material, Sport, Season, Gender.
	$descriptive_attributes = array();

	$attr_material = new WC_Product_Attribute();
	$attr_material->set_id( wc_attribute_taxonomy_id_by_name( 'material' ) );
	$attr_material->set_name( 'pa_material' );
	$attr_material->set_options( array( lukasports_seed_term_id( 'pa_material', $data['material'] ) ) );
	$attr_material->set_visible( true );
	$descriptive_attributes[] = $attr_material;

	$attr_sport = new WC_Product_Attribute();
	$attr_sport->set_id( wc_attribute_taxonomy_id_by_name( 'sport' ) );
	$attr_sport->set_name( 'pa_sport' );
	$attr_sport->set_options( array( lukasports_seed_term_id( 'pa_sport', $data['sport'] ) ) );
	$attr_sport->set_visible( true );
	$descriptive_attributes[] = $attr_sport;

	$attr_season = new WC_Product_Attribute();
	$attr_season->set_id( wc_attribute_taxonomy_id_by_name( 'season' ) );
	$attr_season->set_name( 'pa_season' );
	$attr_season->set_options( array( lukasports_seed_term_id( 'pa_season', '2025-2026' ) ) );
	$attr_season->set_visible( true );
	$descriptive_attributes[] = $attr_season;

	$attr_gender = new WC_Product_Attribute();
	$attr_gender->set_id( wc_attribute_taxonomy_id_by_name( 'gender' ) );
	$attr_gender->set_name( 'pa_gender' );
	$attr_gender->set_options( array( lukasports_seed_term_id( 'pa_gender', 'Unisex' ) ) );
	$attr_gender->set_visible( true );
	$descriptive_attributes[] = $attr_gender;

	$variation_attributes = array();

	if ( $is_variable ) {
		foreach ( $data['variation'] as $attr_key => $values ) {
			$taxonomy = 'pa_' . $attr_key;
			$attr     = new WC_Product_Attribute();
			$attr->set_id( wc_attribute_taxonomy_id_by_name( $attr_key ) );
			$attr->set_name( $taxonomy );
			$attr->set_options( array_map( fn( $v ) => lukasports_seed_term_id( $taxonomy, $v ), $values ) );
			$attr->set_visible( true );
			$attr->set_variation( true );
			$variation_attributes[ $attr_key ] = $values;
			$descriptive_attributes[]          = $attr;
		}
	}

	$product->set_attributes( $descriptive_attributes );
	$product_id = $product->save();
	update_post_meta( $product_id, '_lukasports_seed', '1' );
	update_post_meta( $product_id, '_lukasports_benefits', implode( "\n", $data['benefits'] ) );
	update_post_meta( $product_id, '_lukasports_printing_available', $data['printing'] ? '1' : '' );
	update_post_meta( $product_id, '_lukasports_promotions', implode( "\n", lukasports_seed_promotions_for( $data ) ) );

	// Images: main + 2 gallery variants (color-tinted placeholders).
	$palette = array(
		'Đỏ'         => array( 'ffffff', 'e4231b' ),
		'Đen'        => array( 'f4f5f7', '0e1013' ),
		'Trắng'      => array( '0e1013', 'ffffff' ),
		'Xanh Dương' => array( 'ffffff', '1b4fbf' ),
		'Vàng'       => array( '101b33', 'f2c14e' ),
	);
	$base_color = $data['color'] ?? 'Đen';
	list( $bg, $fg ) = $palette[ $base_color ] ?? array( 'f4f5f7', '0e1013' );

	// Match the illustration to what the product actually is instead
	// of drawing every product as the same crew-neck jersey outline.
	// "phu-kien" (accessories) covers several unrelated item types, so
	// it needs a per-product override rather than one category-wide
	// shape — otherwise a water bottle, a shoe bag and a headband all
	// render as the same sock illustration.
	$shape_by_category = array(
		'ao-bong-ro' => 'tank',
		'phu-kien'   => 'socks',
	);
	$shape = $data['shape'] ?? ( $shape_by_category[ $data['cat'] ] ?? 'jersey' );

	$main_image_id = lukasports_seed_placeholder_image( $data['name'], $bg, $fg, $shape );
	if ( $main_image_id ) {
		$product->set_image_id( $main_image_id );
	}
	$gallery_ids = array();
	foreach ( array( 1, 2 ) as $i ) {
		$gallery_ids[] = lukasports_seed_placeholder_image( $data['name'] . ' ' . $i, $fg, $bg, $shape );
	}
	$product->set_gallery_image_ids( array_filter( $gallery_ids ) );
	$product->save();

	// Variations for variable products.
	if ( $is_variable ) {
		$combinations = array( array() );
		foreach ( $variation_attributes as $attr_key => $values ) {
			$next = array();
			foreach ( $combinations as $combo ) {
				foreach ( $values as $value ) {
					$next[] = $combo + array( $attr_key => $value );
				}
			}
			$combinations = $next;
		}

		foreach ( $combinations as $combo ) {
			$variation = new WC_Product_Variation();
			$variation->set_parent_id( $product_id );
			$variation->set_regular_price( (string) $data['regular'] );
			if ( ! empty( $data['sale'] ) ) {
				$variation->set_sale_price( (string) $data['sale'] );
			}
			$variation->set_stock_status( 'instock' );
			$attributes = array();
			foreach ( $combo as $attr_key => $value ) {
				$taxonomy                 = 'pa_' . $attr_key;
				$attributes[ $taxonomy ] = lukasports_seed_term_slug( $taxonomy, $value );
			}
			$variation->set_attributes( $attributes );
			$variation_id = $variation->save();
			update_post_meta( $variation_id, '_lukasports_seed', '1' );
		}

		wc_delete_product_transients( $product_id );
	}

	$created++;
	WP_CLI::log( "Created: {$data['name']} (#{$product_id})" );
}

WP_CLI::success( "Seeded {$created} products across " . count( $categories ) . ' categories.' );

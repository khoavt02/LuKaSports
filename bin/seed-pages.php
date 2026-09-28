<?php
/**
 * Site structure: every Page the theme's header, footer and homepage
 * link to, WooCommerce pages under Vietnamese slugs, Settings → Reading
 * (static front page + /blog/ posts page), and a few product
 * collections so /bo-suu-tap/ and the homepage "LukaSports tuyển chọn" section have
 * real data.
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/seed-pages.php
 * Run it after seed-products.php (collections are assigned by category).
 *
 * Safe to re-run: pages are matched by slug and only created when
 * missing — an existing page keeps its content (admin edits survive),
 * only its template is re-applied.
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce must be active before seeding pages.' );
}

/**
 * Create a page if no page with this slug exists yet; otherwise just
 * make sure the template is set. Returns the page ID.
 */
function lukasports_seed_page( $slug, $title, $content = '', $template = '' ) {
	$page = get_page_by_path( $slug );

	if ( $page ) {
		$page_id = $page->ID;
		if ( 'publish' !== $page->post_status ) {
			wp_update_post( array( 'ID' => $page_id, 'post_status' => 'publish' ) );
		}
		WP_CLI::log( "Exists:  /{$slug}/ (#{$page_id})" );
	} else {
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_name'    => $slug,
				'post_title'   => $title,
				'post_content' => $content,
			),
			true
		);
		if ( is_wp_error( $page_id ) ) {
			WP_CLI::warning( "Could not create /{$slug}/: " . $page_id->get_error_message() );
			return 0;
		}
		update_post_meta( $page_id, '_lukasports_seed', '1' );
		WP_CLI::log( "Created: /{$slug}/ (#{$page_id})" );
	}

	if ( $template ) {
		update_post_meta( $page_id, '_wp_page_template', $template );
	}

	return (int) $page_id;
}

/**
 * Move an existing page (found by option ID, e.g. a WooCommerce page)
 * to a new slug/title. Falls back to creating it when the option is
 * empty or points at a deleted page.
 */
function lukasports_seed_rename_page( $option, $slug, $title, $content = '' ) {
	$page_id = (int) get_option( $option );
	$page    = $page_id ? get_post( $page_id ) : null;

	if ( ! $page || 'trash' === $page->post_status ) {
		$page_id = lukasports_seed_page( $slug, $title, $content );
		update_option( $option, $page_id );
		return $page_id;
	}

	$update = array(
		'ID'          => $page_id,
		'post_name'   => $slug,
		'post_title'  => $title,
		'post_status' => 'publish',
	);
	if ( '' !== $content ) {
		$update['post_content'] = $content;
	}
	wp_update_post( $update );
	WP_CLI::log( "Renamed: {$option} → /{$slug}/ (#{$page_id})" );

	return $page_id;
}

/**
 * ---------------------------------------------------------------
 * WooCommerce pages → Vietnamese slugs
 * ---------------------------------------------------------------
 */
lukasports_seed_rename_page( 'woocommerce_shop_page_id', 'san-pham', 'Sản phẩm' );
lukasports_seed_rename_page( 'woocommerce_cart_page_id', 'gio-hang', 'Giỏ hàng', '[woocommerce_cart]' );
lukasports_seed_rename_page( 'woocommerce_checkout_page_id', 'thanh-toan', 'Thanh toán', '[woocommerce_checkout]' );
lukasports_seed_rename_page( 'woocommerce_myaccount_page_id', 'tai-khoan', 'Tài khoản', '[woocommerce_my_account]' );

/**
 * ---------------------------------------------------------------
 * Content pages
 * ---------------------------------------------------------------
 */
$front_id = lukasports_seed_page( 'trang-chu', 'Trang chủ' );
$blog_id  = lukasports_seed_page( 'blog', 'Blog' );

lukasports_seed_page(
	've-lukasports',
	'Về LukaSports',
	'<p>LukaSports bắt đầu từ một đội bóng phong trào: mỗi mùa giải lại mất hàng tuần để tìm một xưởng may áo đấu vừa đẹp, vừa bền, vừa đúng hẹn. Không tìm được, chúng tôi tự làm.</p>
<p>Hôm nay LukaSports may áo cho bóng đá, bóng chuyền, bóng rổ, cầu lông và pickleball — từ một chiếc áo cho người chơi cá nhân đến cả bộ đồng phục cho đội, CLB và công ty.</p>
<p>Mỗi sản phẩm đều được chọn vải theo cường độ vận động của môn thể thao đó, thử form trên người chơi thật trước khi lên kệ, và in bằng công nghệ giữ màu sau nhiều mùa giặt.</p>',
	'template-about.php'
);

lukasports_seed_page(
	'lien-he',
	'Liên hệ',
	'<p>Cần tư vấn size, đặt đồng phục cho đội hay thiết kế áo riêng? Chọn kênh tiện nhất cho bạn hoặc để lại thông tin — LukaSports sẽ liên hệ lại sớm.</p>',
	'template-contact.php'
);

lukasports_seed_page(
	'bo-suu-tap',
	'Bộ sưu tập',
	'<p>Những nhóm sản phẩm được LukaSports tuyển chọn theo mùa giải, theo nhu cầu và theo phong cách.</p>',
	'template-collections.php'
);

/**
 * ---------------------------------------------------------------
 * Policy pages — reuse the WordPress privacy page and WooCommerce's
 * sample refund page where they exist, so their special roles
 * (privacy link in checkout, etc.) keep working.
 * ---------------------------------------------------------------
 */
$policy_returns = '<p>LukaSports hỗ trợ đổi hàng trong vòng <strong>7 ngày</strong> kể từ khi bạn nhận hàng.</p>
<h2>Điều kiện đổi hàng</h2>
<ul>
<li>Sản phẩm còn nguyên tem, mác, chưa qua sử dụng hoặc giặt.</li>
<li>Sản phẩm không bị dơ, hư hỏng do người dùng.</li>
<li>Có hóa đơn hoặc thông tin đơn hàng.</li>
</ul>
<h2>Trường hợp không áp dụng</h2>
<ul>
<li>Sản phẩm đã in tên, số hoặc logo theo yêu cầu riêng.</li>
<li>Đơn đồng phục đội được sản xuất theo thiết kế riêng.</li>
</ul>
<h2>Chi phí</h2>
<p>LukaSports miễn phí đổi hàng nếu lỗi đến từ nhà sản xuất (sai size, sai mẫu, lỗi may). Các trường hợp đổi theo nhu cầu cá nhân, khách hàng thanh toán phí vận chuyển hai chiều.</p>';

$policy_shipping = '<h2>Phạm vi giao hàng</h2>
<p>LukaSports giao hàng toàn quốc qua các đơn vị vận chuyển đối tác.</p>
<h2>Thời gian giao hàng</h2>
<ul>
<li>Nội thành Hà Nội: 1–2 ngày làm việc.</li>
<li>Các tỉnh thành khác: 2–5 ngày làm việc.</li>
<li>Đơn đồng phục in theo yêu cầu: 7–10 ngày làm việc kể từ khi duyệt mẫu.</li>
</ul>
<h2>Phí giao hàng</h2>
<p>Miễn phí giao hàng cho đơn từ 500.000đ. Đơn dưới 500.000đ áp dụng phí theo bảng giá của đơn vị vận chuyển, được báo trước khi xác nhận đơn.</p>
<h2>Kiểm tra hàng</h2>
<p>Bạn được kiểm tra hàng trước khi thanh toán. Nếu sản phẩm không đúng đơn đặt, vui lòng từ chối nhận và liên hệ hotline để được hỗ trợ.</p>';

$policy_privacy = '<p>LukaSports tôn trọng và cam kết bảo vệ thông tin cá nhân của khách hàng.</p>
<h2>Thông tin chúng tôi thu thập</h2>
<ul>
<li>Họ tên, số điện thoại, địa chỉ giao hàng khi bạn đặt hàng hoặc gửi yêu cầu tư vấn.</li>
<li>Nguồn truy cập (ví dụ: quảng cáo, mạng xã hội) để cải thiện chất lượng tư vấn.</li>
</ul>
<h2>Mục đích sử dụng</h2>
<ul>
<li>Xử lý đơn hàng, giao hàng và chăm sóc sau bán.</li>
<li>Liên hệ tư vấn theo yêu cầu của bạn.</li>
</ul>
<h2>Cam kết</h2>
<p>LukaSports không bán, trao đổi hay chia sẻ thông tin cá nhân của bạn cho bên thứ ba, ngoại trừ đơn vị vận chuyển để giao hàng hoặc khi pháp luật yêu cầu.</p>
<h2>Liên hệ</h2>
<p>Nếu bạn muốn xem, chỉnh sửa hoặc xóa thông tin của mình, vui lòng liên hệ LukaSports qua trang Liên hệ.</p>';

$refund_page = get_page_by_path( 'refund_returns' );
if ( $refund_page && ! get_page_by_path( 'chinh-sach-doi-tra' ) ) {
	wp_update_post(
		array(
			'ID'           => $refund_page->ID,
			'post_name'    => 'chinh-sach-doi-tra',
			'post_title'   => 'Chính sách đổi trả',
			'post_content' => $policy_returns,
			'post_status'  => 'publish', // WooCommerce creates this sample page as a draft.
		)
	);
	WP_CLI::log( "Renamed: refund_returns → /chinh-sach-doi-tra/ (#{$refund_page->ID})" );
} else {
	lukasports_seed_page( 'chinh-sach-doi-tra', 'Chính sách đổi trả', $policy_returns );
}

lukasports_seed_page( 'chinh-sach-giao-hang', 'Chính sách giao hàng', $policy_shipping );

$privacy_id   = (int) get_option( 'wp_page_for_privacy_policy' );
$privacy_page = $privacy_id ? get_post( $privacy_id ) : null;
if ( $privacy_page && 'chinh-sach-bao-mat' !== $privacy_page->post_name && ! get_page_by_path( 'chinh-sach-bao-mat' ) ) {
	wp_update_post(
		array(
			'ID'           => $privacy_id,
			'post_name'    => 'chinh-sach-bao-mat',
			'post_title'   => 'Chính sách bảo mật',
			'post_content' => $policy_privacy,
			'post_status'  => 'publish',
		)
	);
	WP_CLI::log( "Renamed: privacy page → /chinh-sach-bao-mat/ (#{$privacy_id})" );
} else {
	update_option( 'wp_page_for_privacy_policy', lukasports_seed_page( 'chinh-sach-bao-mat', 'Chính sách bảo mật', $policy_privacy ) );
}

/**
 * ---------------------------------------------------------------
 * WordPress sample content → trash (recoverable from wp-admin).
 * ---------------------------------------------------------------
 */
foreach ( array( 'sample-page' => 'page', 'hello-world' => 'post' ) as $slug => $type ) {
	$sample = get_page_by_path( $slug, OBJECT, $type );
	if ( $sample && 'trash' !== $sample->post_status ) {
		wp_trash_post( $sample->ID );
		WP_CLI::log( "Trashed sample {$type}: {$slug}" );
	}
}

/**
 * ---------------------------------------------------------------
 * Settings → Reading: static front page + posts page at /blog/.
 * ---------------------------------------------------------------
 */
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $front_id );
update_option( 'page_for_posts', $blog_id );

// Fresh WooCommerce installs start in "coming soon" mode, which hides
// the shop from logged-out visitors behind a placeholder page.
update_option( 'woocommerce_coming_soon', 'no' );

/**
 * ---------------------------------------------------------------
 * Product collections, assigned by category.
 * ---------------------------------------------------------------
 */
$collections = array(
	'mua-he-2026'         => array(
		'name'        => 'Hè 2026',
		'description' => 'Áo mỏng nhẹ, thoáng khí cho mùa hè — lựa chọn hàng đầu cho các môn trong nhà và sân cát.',
		'categories'  => array( 'ao-bong-chuyen', 'ao-cau-long', 'ao-pickerball' ),
	),
	'doi-bong-phong-trao' => array(
		'name'        => 'Đồng phục đội phong trào',
		'description' => 'Mẫu áo được các đội bóng phong trào, công ty và CLB đặt nhiều nhất — in tên, số, logo theo yêu cầu.',
		'categories'  => array( 'ao-team', 'ao-bong-da-thiet-ke' ),
	),
	'san-dau-hang-tuan'   => array(
		'name'        => 'Ra sân mỗi tuần',
		'description' => 'Áo đấu và phụ kiện cơ bản cho những buổi ra sân đều đặn mỗi tuần.',
		'categories'  => array( 'ao-bong-da', 'ao-bong-ro', 'phu-kien' ),
	),
);

if ( taxonomy_exists( 'product_collection' ) ) {
	foreach ( $collections as $slug => $collection ) {
		$term = term_exists( $slug, 'product_collection' );
		if ( ! $term ) {
			$term = wp_insert_term( $collection['name'], 'product_collection', array( 'slug' => $slug, 'description' => $collection['description'] ) );
		}
		if ( is_wp_error( $term ) ) {
			WP_CLI::warning( "Collection {$slug}: " . $term->get_error_message() );
			continue;
		}

		$product_ids = wc_get_products(
			array(
				'status'   => 'publish',
				'limit'    => -1,
				'return'   => 'ids',
				'category' => $collection['categories'],
			)
		);
		foreach ( $product_ids as $product_id ) {
			wp_set_object_terms( $product_id, $slug, 'product_collection', true );
		}
		WP_CLI::log( sprintf( 'Collection: %s (%d products)', $collection['name'], count( $product_ids ) ) );
	}
} else {
	WP_CLI::warning( 'Taxonomy product_collection not registered — is lukasports-core active?' );
}

flush_rewrite_rules();

WP_CLI::success( 'Pages, reading settings and collections are ready.' );

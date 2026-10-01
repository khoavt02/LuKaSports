<?php
/**
 * Preview-only seed data: product reviews + blog posts, requested for
 * local design review of the review/rating UI and the blog sections.
 * Not real customer feedback or editorial content. Safe to re-run —
 * deletes anything it previously seeded (tagged `_lukasports_seed`)
 * before recreating it. Pairs with inc/dev-reviews.php (re-enables
 * the reviews tab/ratings) and inc/dev-social-proof.php (testimonial
 * quotes) — remove all three plus this file to fully revert.
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/seed-reviews-blog.php
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce must be active before seeding reviews.' );
}

/**
 * ---------------------------------------------------------------
 * Cleanup: remove anything from a previous run.
 * ---------------------------------------------------------------
 */
$previous_comments = get_comments(
	array(
		'meta_key' => '_lukasports_seed', // phpcs:ignore
		'fields'   => 'ids',
	)
);
foreach ( $previous_comments as $comment_id ) {
	wp_delete_comment( $comment_id, true );
}
WP_CLI::log( sprintf( 'Removed %d previously seeded review(s).', count( $previous_comments ) ) );

$previous_posts = get_posts(
	array(
		'post_type'      => 'post',
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'meta_key'       => '_lukasports_seed', // phpcs:ignore
		'fields'         => 'ids',
	)
);
foreach ( $previous_posts as $post_id ) {
	wp_delete_post( $post_id, true );
}
WP_CLI::log( sprintf( 'Removed %d previously seeded blog post(s).', count( $previous_posts ) ) );

/**
 * ---------------------------------------------------------------
 * Product reviews
 * ---------------------------------------------------------------
 */
function lukasports_seed_recalc_rating( $product_id ) {
	$review_comments = get_comments(
		array(
			'post_id' => $product_id,
			'type'    => 'review',
			'status'  => 'approve',
		)
	);

	$counts = array();
	$sum    = 0;
	$total  = 0;
	foreach ( $review_comments as $comment ) {
		$rating = (int) get_comment_meta( $comment->comment_ID, 'rating', true );
		if ( $rating < 1 ) {
			continue;
		}
		$counts[ $rating ] = ( $counts[ $rating ] ?? 0 ) + 1;
		$sum               += $rating;
		++$total;
	}

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return;
	}
	$product->set_average_rating( $total ? round( $sum / $total, 2 ) : 0 );
	$product->set_rating_counts( $counts );
	$product->set_review_count( $total );
	$product->save();
}

$reviewers = array(
	'Nguyễn Văn An', 'Trần Thị Bích', 'Lê Hoàng Nam', 'Phạm Thu Hà', 'Đỗ Quang Huy',
	'Vũ Thị Lan', 'Bùi Đức Thắng', 'Ngô Minh Trang', 'Hoàng Văn Long', 'Đặng Thị Mai',
	'Trịnh Công Sơn', 'Lý Thị Ngọc', 'Phan Anh Tuấn', 'Dương Thị Thu', 'Mai Xuân Bách',
	'Đinh Văn Khoa', 'Cao Thị Diễm', 'Lâm Quốc Bảo', 'Tô Thị Hằng', 'Huỳnh Văn Phúc',
);

$comments_5 = array(
	'Áo đẹp, đúng như hình, chất vải mát, đá bóng không bị bí.',
	'Giao hàng nhanh, đóng gói cẩn thận, sẽ ủng hộ shop tiếp.',
	'In tên số sắc nét, form áo vừa vặn, rất ưng ý.',
	'Đặt cho cả đội, ai cũng khen chất lượng tốt hơn mong đợi.',
	'Tư vấn nhiệt tình, đổi size nhanh gọn không rắc rối.',
	'Vải dày dặn, đường may chắc chắn, giặt vài lần vẫn đẹp.',
	'Second-order rồi, chất lượng ổn định như lần đầu.',
);
$comments_4 = array(
	'Áo ổn, chỉ hơi rộng so với size thường mặc, lần sau chọn nhỏ hơn.',
	'Chất lượng tốt nhưng giao hơi trễ hẹn 1 ngày.',
	'Màu áo hơi khác một chút so với hình nhưng vẫn đẹp.',
	'Nhìn chung hài lòng, sẽ mua thêm áo khác của shop.',
);
$comments_3 = array(
	'Áo tạm ổn, giá hợp lý, không có gì đặc biệt.',
	'Vải hơi mỏng hơn mình nghĩ nhưng mặc vẫn được.',
);

$products     = wc_get_products( array( 'status' => 'publish', 'limit' => -1 ) );
$review_total = 0;

foreach ( $products as $product ) {
	// ~75% of products get reviews — leaves some with none, which is
	// more realistic than every single product having a perfect record.
	if ( wp_rand( 1, 100 ) > 75 ) {
		continue;
	}

	$num_reviews = wp_rand( 1, 3 );
	for ( $i = 0; $i < $num_reviews; $i++ ) {
		$roll = wp_rand( 1, 100 );
		if ( $roll <= 55 ) {
			$rating = 5;
			$pool   = $comments_5;
		} elseif ( $roll <= 88 ) {
			$rating = 4;
			$pool   = $comments_4;
		} else {
			$rating = 3;
			$pool   = $comments_3;
		}

		$days_ago  = wp_rand( 3, 180 );
		$name      = $reviewers[ array_rand( $reviewers ) ];
		$comment_id = wp_insert_comment(
			array(
				'comment_post_ID'      => $product->get_id(),
				'comment_author'       => $name,
				'comment_author_email' => 'khach' . wp_rand( 1000, 9999 ) . '@example.com',
				'comment_content'      => $pool[ array_rand( $pool ) ],
				'comment_type'         => 'review',
				'comment_approved'     => 1,
				'comment_date'         => gmdate( 'Y-m-d H:i:s', strtotime( "-{$days_ago} days" ) ),
			)
		);

		if ( $comment_id ) {
			update_comment_meta( $comment_id, 'rating', $rating );
			update_comment_meta( $comment_id, 'verified', 1 );
			update_comment_meta( $comment_id, '_lukasports_seed', '1' );
			++$review_total;
		}
	}

	lukasports_seed_recalc_rating( $product->get_id() );
}
WP_CLI::log( "Seeded {$review_total} product review(s)." );

/**
 * ---------------------------------------------------------------
 * Blog posts — reuses already-seeded product photos as featured
 * images rather than generating new placeholder art.
 * ---------------------------------------------------------------
 */
$image_ids = get_posts(
	array(
		'post_type'      => 'attachment',
		'posts_per_page' => -1,
		'meta_key'       => '_lukasports_seed', // phpcs:ignore
		'fields'         => 'ids',
	)
);

$posts = array(
	array(
		'title'   => 'Cách chọn size áo bóng đá chuẩn không cần đo ni',
		'excerpt' => 'Không có cân đo tại nhà? Đây là cách ước lượng size áo đấu chính xác chỉ bằng một chiếc áo đang mặc vừa.',
		'content' => "Nhiều bạn ngại đặt áo online vì sợ sai size, nhưng thực ra chỉ cần một mẹo nhỏ là ước lượng khá chính xác.\n\nCách đơn giản nhất: lấy một chiếc áo thun đang mặc vừa, đo chiều rộng ngang ngực (đo phẳng, không kéo căng) và chiều dài từ vai xuống gấu áo. So với bảng size ở tab \"Hướng dẫn chọn size\" trên từng sản phẩm để chọn size gần nhất.\n\nMột lưu ý quan trọng: áo đấu bóng đá thường có form ôm hơn áo thun thường ngày để giảm cản gió khi vận động. Nếu bạn thích mặc rộng rãi hoặc mặc lót thêm áo giữ nhiệt bên trong mùa đông, nên chọn lớn hơn 1 size so với form áo thường.\n\nNếu vẫn phân vân giữa hai size, đừng ngại nhắn Zalo hoặc Messenger để được đo tư vấn — đổi size miễn phí trong 7 ngày nếu không vừa.",
	),
	array(
		'title'   => '5 mẹo giữ áo đấu bền màu, không bai form',
		'excerpt' => 'Áo đấu in tên số mà giặt sai cách rất dễ bong chữ, phai màu. Vài mẹo đơn giản giúp áo bền như mới sau nhiều trận.',
		'content' => "1. Giặt riêng lần đầu: áo mới nên giặt riêng 1-2 lần đầu để tránh phai màu loang sang quần áo khác.\n\n2. Lộn mặt trái khi giặt: đặc biệt với áo có in tên số, lộn trái giúp bảo vệ lớp in không bị cọ xát trực tiếp với các áo khác trong máy giặt.\n\n3. Giặt nước lạnh hoặc ấm nhẹ: nước quá nóng làm giãn sợi vải Polyester, khiến áo nhanh bai form.\n\n4. Không dùng nước xả vải lên phần in: nước xả có thể làm giảm độ bám dính của mực in theo thời gian.\n\n5. Phơi trong bóng râm: nắng gắt trực tiếp là nguyên nhân chính khiến áo bạc màu nhanh, đặc biệt các màu đậm như đỏ, đen.",
	),
	array(
		'title'   => 'Áo đấu sân nhà và sân khách khác nhau thế nào?',
		'excerpt' => 'Vì sao các CLB luôn có ít nhất 2 mẫu áo mỗi mùa? Câu chuyện đằng sau áo sân nhà và sân khách.',
		'content' => "Theo quy định của hầu hết các giải đấu, khi hai đội có áo trùng màu hoặc dễ gây nhầm lẫn, đội khách phải đổi sang bộ áo dự phòng — đó là lý do áo sân khách thường có tông màu đối lập hoàn toàn với áo sân nhà.\n\nÁo sân nhà thường mang màu sắc truyền thống, gắn liền với bản sắc CLB hoặc đội tuyển. Áo sân khách linh hoạt hơn, nhiều đội dùng để thử nghiệm phối màu hoặc họa tiết mới trước khi đưa vào mẫu chính.\n\nVới các đội phong trào, việc có 2 bộ áo còn giúp dễ phân biệt đội nhà — đội khách khi thi đấu giao hữu hoặc giải nội bộ, tránh nhầm lẫn khi theo dõi trận đấu.",
	),
	array(
		'title'   => 'Kinh nghiệm đặt đồng phục cho đội bóng phong trào',
		'excerpt' => 'Đặt áo cho cả đội khác với mua áo cá nhân — vài kinh nghiệm giúp buổi đặt áo suôn sẻ, không phát sinh đổi trả.',
		'content' => "Thu thập số đo trước, không chỉ hỏi size: mỗi người có thói quen mặc rộng/ôm khác nhau, nên hỏi thêm \"bạn muốn mặc ôm hay rộng rãi\" thay vì chỉ hỏi size S/M/L.\n\nChốt mẫu và màu trước khi thu tiền: tránh tình trạng nửa chừng đổi ý khiến đơn hàng bị chậm.\n\nGửi danh sách tên + số áo dạng bảng tính: giúp việc in ấn chính xác, hạn chế sai sót khi có hơn 10 người trong đội.\n\nChừa thời gian dự phòng: nên đặt trước ít nhất 7-10 ngày so với ngày cần dùng, đặc biệt với đơn có in tên số riêng từng người.\n\nDALETIC nhận tư vấn đặt áo đội từ số lượng nhỏ, hỗ trợ file mẫu tên số để đội trưởng dễ tổng hợp.",
	),
	array(
		'title'   => 'Chất liệu vải thể thao: Polyester lạnh và Thun cá sấu khác gì nhau?',
		'excerpt' => 'Hai chất liệu phổ biến nhất trong áo đấu — chọn loại nào cho phù hợp với nhu cầu thi đấu hay mặc hàng ngày?',
		'content' => "Polyester lạnh là chất liệu chủ lực của hầu hết áo đấu hiện nay: nhẹ, khô nhanh, bề mặt hơi mát khi chạm vào, phù hợp vận động cường độ cao và thời tiết nóng.\n\nThun cá sấu (thường dùng cho áo polo, áo thiết kế) có độ dày và form cứng cáp hơn, giữ dáng tốt, phù hợp mặc hàng ngày hoặc các mẫu áo cần in họa tiết chi tiết vì bề mặt vải mịn hơn.\n\nNếu ưu tiên thi đấu, thấm hút mồ hôi tốt: chọn Polyester lạnh. Nếu ưu tiên mặc phố, form áo đứng dáng: Thun cá sấu là lựa chọn hợp lý hơn.",
	),
);

foreach ( $posts as $index => $data ) {
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'post',
			'post_title'   => $data['title'],
			'post_excerpt' => $data['excerpt'],
			'post_content' => $data['content'],
			'post_status'  => 'publish',
			'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( '-' . ( ( $index + 1 ) * 9 + wp_rand( 0, 3 ) ) . ' days' ) ),
		)
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		continue;
	}

	update_post_meta( $post_id, '_lukasports_seed', '1' );

	if ( ! empty( $image_ids ) ) {
		set_post_thumbnail( $post_id, $image_ids[ array_rand( $image_ids ) ] );
	}

	WP_CLI::log( "Created post: {$data['title']} (#{$post_id})" );
}

WP_CLI::success( 'Seeded product reviews and blog posts.' );

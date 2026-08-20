<?php
/**
 * Preview-only testimonial data for the "Khách hàng nói gì?" section
 * (see template-parts/sections/social-proof.php) — requested for local
 * design review. Not real customer quotes. Delete this file (and its
 * require line in functions.php) before launch; the section already
 * hides itself automatically if this filter returns nothing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_dev_social_proof_items( $items ) {
	if ( ! empty( $items ) ) {
		return $items;
	}

	return array(
		array(
			'quote'  => 'Đặt đồng phục cho cả đội bóng công ty, LukaSports tư vấn size rất kỹ nên không ai phải đổi lại. Áo thoáng mát, form đẹp.',
			'author' => 'Anh Minh — Đội bóng FPT Software',
		),
		array(
			'quote'  => 'Chất liệu thấm hút tốt thật, đá 90 phút vẫn không dính rít. Thêu tên số sắc nét, giao đúng hẹn.',
			'author' => 'Chị Hồng Nhung — CLB bóng đá nữ Thanh Xuân',
		),
		array(
			'quote'  => 'Lần đầu đặt áo online mà hồi hộp, nhưng nhắn Zalo được tư vấn nhiệt tình, áo về đúng như hình, sẽ ủng hộ tiếp.',
			'author' => 'Bạn Quốc Huy — sinh viên Đại học Bách Khoa',
		),
	);
}
add_filter( 'lukasports_social_proof_items', 'lukasports_dev_social_proof_items' );

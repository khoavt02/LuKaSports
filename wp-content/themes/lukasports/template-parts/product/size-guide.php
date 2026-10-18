<?php
/**
 * Shared size chart across the catalog — sizing is standardized across
 * DALETIC's product line, so one chart serves every product. The chart
 * is DALETIC's own designed image (assets/images/bang-size.webp); the
 * alt text carries the same figures for screen readers and search.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$alt = __( 'Bảng size áo bóng đá thiết kế DALETIC: S 1m55–1m60, 45–55kg; M 1m61–1m67, 55–65kg; L 1m68–1m75, 65–75kg; XL 1m76–1m81, 75–85kg; XXL 1m82–1m87, 85–95kg; 3XL 1m88–1m93, trên 95kg; 4XL 1m88–1m93, trên 110kg. Một người có thể mặc 2 size: mặc body chọn size bé, mặc thoải mái chọn size to.', 'lukasports' );
?>
<div class="sk-size-guide">
	<img class="sk-size-guide__image" src="<?php echo esc_url( lukasports_image_url( 'bang-size.webp' ) ); ?>" alt="<?php echo esc_attr( $alt ); ?>" width="1600" height="2000" loading="lazy" decoding="async" />
	<p><?php esc_html_e( 'Chưa chắc chọn size nào? Nhắn Zalo/Messenger cho DALETIC — tư vấn size miễn phí.', 'lukasports' ); ?></p>
</div>

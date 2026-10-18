<?php
/**
 * Zalo store connection: the official Zalo business-card QR
 * (assets/images/zalo-qr.jpg) plus an "Mở Zalo" button.
 *
 * Args: variant 'full' (contact page card) | 'compact' (footer thumbnail).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$variant = isset( $args['variant'] ) && 'compact' === $args['variant'] ? 'compact' : 'full';
$contact = lukasports_get_contact_info();
$qr      = lukasports_image_url( 'zalo-qr.jpg' );
$alt     = __( 'Mã QR danh thiếp Zalo DALETIC', 'lukasports' );

if ( 'compact' === $variant ) :
	?>
	<a class="sk-zalo-mini" href="<?php echo esc_url( $contact['zalo_url'] ); ?>" target="_blank" rel="noopener">
		<img src="<?php echo esc_url( $qr ); ?>" alt="<?php echo esc_attr( $alt ); ?>" width="72" height="72" loading="lazy" decoding="async" />
		<span><?php esc_html_e( 'Quét mã QR để kết nối Zalo cửa hàng', 'lukasports' ); ?></span>
	</a>
	<?php
	return;
endif;
?>
<section class="sk-container sk-zalo" aria-labelledby="sk-zalo-title">
	<div class="sk-zalo__card">
		<div class="sk-zalo__text">
			<span class="sk-eyebrow"><?php esc_html_e( 'Zalo cửa hàng', 'lukasports' ); ?></span>
			<h2 class="sk-zalo__title sk-display" id="sk-zalo-title"><?php esc_html_e( 'Kết nối Zalo DALETIC', 'lukasports' ); ?></h2>
			<p class="sk-zalo__desc"><?php esc_html_e( 'Mở Zalo, quét mã QR để kết nối trực tiếp với cửa hàng. Gửi mẫu áo, số lượng và nhận báo giá, mockup ngay trong cuộc trò chuyện.', 'lukasports' ); ?></p>
			<ol class="sk-zalo__steps">
				<li><?php esc_html_e( 'Mở Zalo → biểu tượng quét QR', 'lukasports' ); ?></li>
				<li><?php esc_html_e( 'Quét mã bên cạnh', 'lukasports' ); ?></li>
				<li><?php esc_html_e( 'Nhắn tin để được tư vấn', 'lukasports' ); ?></li>
			</ol>
			<a class="sk-btn sk-btn--primary" href="<?php echo esc_url( $contact['zalo_url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Mở Zalo ngay', 'lukasports' ); ?></a>
		</div>
		<img class="sk-zalo__qr" src="<?php echo esc_url( $qr ); ?>" alt="<?php echo esc_attr( $alt ); ?>" width="260" height="260" loading="lazy" decoding="async" />
	</div>
</section>

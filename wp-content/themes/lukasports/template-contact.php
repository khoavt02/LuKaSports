<?php
/**
 * Template Name: DALETIC — Liên hệ
 *
 * Contact channels (all from DALETIC → Settings, same source as
 * every other CTA) next to an inline copy of the consultation form,
 * plus a map block. The form posts to the same AJAX endpoint as the
 * modal and lands in the same leads table, tagged source=contact_page.
 * Styles: pages.css (Contact).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$contact = lukasports_get_contact_info();
$map_url = $contact['address'] ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $contact['address'] ) : '';

$icons = array(
	'phone'     => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
	'messenger' => '<path d="M12 3C7 3 3 6.7 3 11.3c0 2.6 1.3 4.9 3.3 6.4V21l3-1.7c.9.3 1.8.4 2.7.4 5 0 9-3.7 9-8.4S17 3 12 3z"/><path d="M7.5 13.5l3-3 2.5 2 3.5-3.5"/>',
	'facebook'  => '<path d="M14 8.5V6.8c0-.8.5-1.3 1.3-1.3H17V2.3h-2.6C11.7 2.3 10.4 4 10.4 6.6v1.9H8v3.3h2.4V22H14V11.8h2.7l.4-3.3z"/>',
	'pin'       => '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
);

// Messenger link shown as its short form (m.me/page) when possible.
$messenger_label = preg_replace( '#^https?://(www\.)?#', '', untrailingslashit( $contact['messenger_url'] ) );
if ( preg_match( '#^m\.me/\d+$#', $messenger_label ) ) {
	$messenger_label = __( 'Nhắn tin Fanpage', 'lukasports' ); // numeric page id reads badly
}

// Zalo number from the zalo.me link; same as the hotline → reuse its formatting.
$zalo_label = __( 'Nhắn Zalo', 'lukasports' );
if ( preg_match( '#zalo\.me/(\d{9,12})#', $contact['zalo_url'], $m ) ) {
	$hotline_digits = preg_replace( '/\D/', '', $contact['hotline_display'] );
	$zalo_label     = ( $hotline_digits === $m[1] ) ? $contact['hotline_display'] : $m[1];
}

$channels = array(
	array(
		'key'   => 'hotline',
		'label' => __( 'Hotline', 'lukasports' ),
		'value' => $contact['hotline_display'],
		'href'  => 'tel:' . $contact['hotline_tel'],
		'note'  => __( '8:00 – 21:00, tất cả các ngày', 'lukasports' ),
		'icon'  => 'phone',
	),
	array(
		'key'   => 'zalo',
		'label' => __( 'Zalo', 'lukasports' ),
		'value' => $zalo_label,
		'href'  => $contact['zalo_url'],
		'note'  => __( 'Gửi mẫu áo hoặc logo để được báo giá nhanh.', 'lukasports' ),
		'icon'  => '',
	),
	array(
		'key'   => 'messenger',
		'label' => __( 'Messenger', 'lukasports' ),
		'value' => $messenger_label,
		'href'  => $contact['messenger_url'],
		'note'  => __( 'Phản hồi trong giờ làm việc.', 'lukasports' ),
		'icon'  => 'messenger',
	),
);
if ( ! empty( $contact['facebook_url'] ) ) {
	$channels[] = array(
		'key'   => 'facebook',
		'label' => __( 'Fanpage', 'lukasports' ),
		'value' => __( 'DALETIC Sports', 'lukasports' ),
		'href'  => $contact['facebook_url'],
		'note'  => __( 'Xem mẫu mới và hình ảnh đội đã đặt áo.', 'lukasports' ),
		'icon'  => 'facebook',
	);
}
if ( $map_url ) {
	$channels[] = array(
		'key'   => 'store',
		'label' => __( 'Showroom DALETIC', 'lukasports' ),
		'value' => $contact['address'],
		'href'  => $map_url,
		'note'  => __( 'Xem bản đồ →', 'lukasports' ),
		'icon'  => 'pin',
	);
}

// "Sản phẩm quan tâm": top-level product categories, sent as the
// existing free-text product_name field (no handler change needed).
$interest = function_exists( 'wc_get_page_permalink' ) ? get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'parent'     => 0,
		'hide_empty' => true,
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
	)
) : array();
if ( is_wp_error( $interest ) ) {
	$interest = array();
}

// Page content (if any) becomes the intro of the dark band.
the_post();
$sk_intro = get_the_content() ? apply_filters( 'the_content', get_the_content() ) : '';
rewind_posts();
get_template_part(
	'template-parts/page-band',
	null,
	array(
		'title' => get_the_title(),
		'dot'   => true,
		'intro' => wp_kses_post( $sk_intro ),
	)
);
?>
<div class="sk-contact">
	<div class="sk-container sk-contact__grid">
		<ul class="sk-contact__channels">
			<?php foreach ( $channels as $channel ) : ?>
				<?php $external = 0 !== strpos( $channel['href'], 'tel:' ); ?>
				<li class="sk-contact__channel-item sk-contact__channel-item--<?php echo esc_attr( $channel['key'] ); ?>">
					<a class="sk-contact__channel sk-contact__channel--<?php echo esc_attr( $channel['key'] ); ?>" href="<?php echo esc_url( $channel['href'] ); ?>"<?php echo $external ? ' target="_blank" rel="noopener"' : ''; ?>>
						<span class="sk-contact__channel-icon" aria-hidden="true">
							<?php if ( $channel['icon'] ) : ?>
								<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?php echo $icons[ $channel['icon'] ]; // phpcs:ignore -- static markup ?></svg>
							<?php else : ?>
								Zalo
							<?php endif; ?>
						</span>
						<span class="sk-contact__channel-text">
							<span class="sk-contact__channel-label"><?php echo esc_html( $channel['label'] ); ?></span>
							<span class="sk-contact__channel-value"><?php echo esc_html( $channel['value'] ); ?></span>
							<span class="sk-contact__channel-note"><?php echo esc_html( $channel['note'] ); ?></span>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<section class="sk-contact__form-card" aria-labelledby="sk-contact-form-title">
			<h2 class="sk-contact__form-title sk-display" id="sk-contact-form-title"><?php esc_html_e( 'Nhận tư vấn từ DALETIC', 'lukasports' ); ?></h2>
			<p class="sk-contact__form-desc"><?php esc_html_e( 'Nhân viên sẽ liên hệ tư vấn size, số lượng và giá.', 'lukasports' ); ?></p>

			<form class="sk-contact__form" data-sk-lead-form novalidate data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
				<?php // No wp_nonce_field() here: its id="nonce" would duplicate the modal form's. ?>
				<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'lukasports_submit_lead' ) ); ?>" />
				<input type="hidden" name="source" value="contact_page" />

				<div class="sk-contact__row">
					<p class="sk-field-group">
						<label for="sk-contact-name"><?php esc_html_e( 'Tên', 'lukasports' ); ?> <span aria-hidden="true">*</span></label>
						<input type="text" class="sk-field" id="sk-contact-name" name="name" required autocomplete="name" placeholder="<?php esc_attr_e( 'Nguyễn Văn A', 'lukasports' ); ?>" />
						<span class="sk-field-error" data-error-for="name"></span>
					</p>

					<p class="sk-field-group">
						<label for="sk-contact-phone"><?php esc_html_e( 'Số điện thoại', 'lukasports' ); ?> <span aria-hidden="true">*</span></label>
						<input type="tel" class="sk-field" id="sk-contact-phone" name="phone" required autocomplete="tel" placeholder="<?php esc_attr_e( '09xx xxx xxx', 'lukasports' ); ?>" />
						<span class="sk-field-error" data-error-for="phone"></span>
					</p>
				</div>

				<p class="sk-field-group">
					<label for="sk-contact-product"><?php esc_html_e( 'Sản phẩm quan tâm', 'lukasports' ); ?></label>
					<?php if ( $interest ) : ?>
						<select class="sk-field sk-contact__select" id="sk-contact-product" name="product_name">
							<option value=""><?php esc_html_e( 'Chọn loại sản phẩm (không bắt buộc)', 'lukasports' ); ?></option>
							<?php foreach ( $interest as $term ) : ?>
								<option value="<?php echo esc_attr( $term->name ); ?>"><?php echo esc_html( $term->name ); ?></option>
							<?php endforeach; ?>
							<option value="<?php esc_attr_e( 'Khác', 'lukasports' ); ?>"><?php esc_html_e( 'Khác', 'lukasports' ); ?></option>
						</select>
					<?php else : ?>
						<input type="text" class="sk-field" id="sk-contact-product" name="product_name" placeholder="<?php esc_attr_e( 'VD: 15 bộ áo bóng đá cho đội công ty', 'lukasports' ); ?>" />
					<?php endif; ?>
				</p>

				<p class="sk-field-group">
					<label for="sk-contact-message"><?php esc_html_e( 'Nội dung', 'lukasports' ); ?></label>
					<textarea class="sk-field" id="sk-contact-message" name="message" rows="4" placeholder="<?php esc_attr_e( 'Số lượng, màu sắc, thời gian cần áo…', 'lukasports' ); ?>"></textarea>
				</p>

				<div class="sk-contact__submit">
					<button type="submit" class="sk-btn sk-btn--primary sk-btn--lg"><?php esc_html_e( 'Gửi yêu cầu tư vấn', 'lukasports' ); ?></button>
					<span class="sk-contact__hint"><?php esc_html_e( 'Tên và số điện thoại là bắt buộc.', 'lukasports' ); ?></span>
				</div>
				<p class="sk-modal__form-error" data-form-error hidden></p>
			</form>

			<div class="sk-modal__success sk-contact__success" data-sk-modal-success hidden role="status">
				<span class="sk-contact__success-icon" aria-hidden="true"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5 9-10"/></svg></span>
				<h2 class="sk-contact__form-title sk-display"><?php esc_html_e( 'Đã nhận yêu cầu!', 'lukasports' ); ?></h2>
				<p data-sk-modal-success-message></p>
				<p class="sk-contact__success-call">
					<?php esc_html_e( 'Cần gấp? Gọi ngay', 'lukasports' ); ?>
					<a href="tel:<?php echo esc_attr( $contact['hotline_tel'] ); ?>"><?php echo esc_html( $contact['hotline_display'] ); ?></a>.
				</p>
			</div>
		</section>
	</div>

	<?php get_template_part( 'template-parts/zalo-connect' ); ?>

	<?php if ( $map_url ) : ?>
		<section class="sk-container sk-contact__map-wrap" aria-label="<?php esc_attr_e( 'Bản đồ', 'lukasports' ); ?>">
			<div class="sk-contact__map">
				<div class="sk-contact__map-art" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>
				<svg class="sk-contact__map-pin" width="44" height="56" viewBox="0 0 24 30" aria-hidden="true"><path d="M12 30S2 18.5 2 11a10 10 0 0 1 20 0c0 7.5-10 19-10 19z" fill="currentColor"/><circle cx="12" cy="11" r="4" fill="#fff"/></svg>
				<div class="sk-contact__map-card">
					<div class="sk-contact__map-label"><?php esc_html_e( 'Showroom DALETIC', 'lukasports' ); ?></div>
					<div class="sk-contact__map-address"><?php echo esc_html( $contact['address'] ); ?></div>
					<a class="sk-contact__map-link" href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Chỉ đường trên Google Maps →', 'lukasports' ); ?></a>
				</div>
			</div>
		</section>
	<?php endif; ?>
</div>

<?php get_footer(); ?>

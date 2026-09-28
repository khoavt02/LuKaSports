<?php
/**
 * Template Name: LukaSports — Liên hệ
 *
 * Contact channels (all from LukaSports → Settings, same source as
 * every other CTA) next to an inline copy of the consultation form.
 * The form posts to the same AJAX endpoint as the modal and lands in
 * the same leads table, tagged source=contact_page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$contact  = lukasports_get_contact_info();
$map_url  = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $contact['address'] );
$channels = array(
	array(
		'label' => __( 'Hotline', 'lukasports' ),
		'value' => $contact['hotline_display'],
		'href'  => 'tel:' . $contact['hotline_tel'],
		'note'  => __( '8:00 – 21:00, tất cả các ngày', 'lukasports' ),
	),
	array(
		'label' => __( 'Zalo', 'lukasports' ),
		'value' => __( 'Nhắn Zalo', 'lukasports' ),
		'href'  => $contact['zalo_url'],
		'note'  => __( 'Gửi mẫu áo, logo để được báo giá nhanh', 'lukasports' ),
	),
	array(
		'label' => __( 'Messenger', 'lukasports' ),
		'value' => __( 'Nhắn Messenger', 'lukasports' ),
		'href'  => $contact['messenger_url'],
		'note'  => __( 'Phản hồi trong giờ làm việc', 'lukasports' ),
	),
	array(
		'label' => __( 'Cửa hàng', 'lukasports' ),
		'value' => $contact['address'],
		'href'  => $map_url,
		'note'  => __( 'Xem bản đồ →', 'lukasports' ),
	),
);
?>

<section class="sk-section sk-contact">
	<div class="sk-container">
		<nav class="sk-breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Trang chủ', 'lukasports' ); ?></a> / <?php the_title(); ?>
		</nav>

		<header class="sk-contact__header">
			<h1 class="sk-contact__title"><?php the_title(); ?></h1>
			<?php
			while ( have_posts() ) :
				the_post();
				if ( get_the_content() ) :
					?>
					<div class="sk-contact__intro"><?php the_content(); ?></div>
					<?php
				endif;
			endwhile;
			?>
		</header>

		<div class="sk-contact__grid">
			<ul class="sk-contact__channels">
				<?php foreach ( $channels as $channel ) : ?>
					<?php $external = 0 !== strpos( $channel['href'], 'tel:' ); ?>
					<li>
						<a class="sk-contact__channel" href="<?php echo esc_url( $channel['href'] ); ?>"<?php echo $external ? ' target="_blank" rel="noopener"' : ''; ?>>
							<span class="sk-contact__channel-label"><?php echo esc_html( $channel['label'] ); ?></span>
							<span class="sk-contact__channel-value"><?php echo esc_html( $channel['value'] ); ?></span>
							<span class="sk-contact__channel-note"><?php echo esc_html( $channel['note'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<div class="sk-contact__form-card">
				<h2 class="sk-contact__form-title"><?php esc_html_e( 'Gửi yêu cầu tư vấn', 'lukasports' ); ?></h2>
				<p class="sk-contact__form-desc"><?php esc_html_e( 'Để lại thông tin, LukaSports sẽ gọi lại tư vấn size, số lượng và báo giá.', 'lukasports' ); ?></p>

				<form data-sk-lead-form novalidate data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
					<?php // No wp_nonce_field() here: its id="nonce" would duplicate the modal form's. ?>
					<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'lukasports_submit_lead' ) ); ?>" />
					<input type="hidden" name="source" value="contact_page" />

					<p class="sk-field-group">
						<label for="sk-contact-name"><?php esc_html_e( 'Tên', 'lukasports' ); ?> <span aria-hidden="true">*</span></label>
						<input type="text" class="sk-field" id="sk-contact-name" name="name" required autocomplete="name" />
						<span class="sk-field-error" data-error-for="name"></span>
					</p>

					<p class="sk-field-group">
						<label for="sk-contact-phone"><?php esc_html_e( 'Số điện thoại', 'lukasports' ); ?> <span aria-hidden="true">*</span></label>
						<input type="tel" class="sk-field" id="sk-contact-phone" name="phone" required autocomplete="tel" placeholder="<?php echo esc_attr( $contact['hotline_display'] ); ?>" />
						<span class="sk-field-error" data-error-for="phone"></span>
					</p>

					<p class="sk-field-group">
						<label for="sk-contact-product"><?php esc_html_e( 'Sản phẩm quan tâm', 'lukasports' ); ?></label>
						<input type="text" class="sk-field" id="sk-contact-product" name="product_name" placeholder="<?php esc_attr_e( 'VD: 15 bộ áo bóng đá cho đội công ty', 'lukasports' ); ?>" />
					</p>

					<p class="sk-field-group">
						<label for="sk-contact-message"><?php esc_html_e( 'Nội dung', 'lukasports' ); ?></label>
						<textarea class="sk-field" id="sk-contact-message" name="message" rows="4" placeholder="<?php esc_attr_e( 'Số lượng, màu sắc, thời gian cần hàng… (không bắt buộc)', 'lukasports' ); ?>"></textarea>
					</p>

					<button type="submit" class="sk-btn sk-btn--primary sk-btn--lg sk-btn--block"><?php esc_html_e( 'Gửi yêu cầu', 'lukasports' ); ?></button>
					<p class="sk-modal__form-error" data-form-error hidden></p>
				</form>

				<div class="sk-modal__success" data-sk-modal-success hidden>
					<svg width="48" height="48" viewBox="0 0 48 48" fill="none" aria-hidden="true"><circle cx="24" cy="24" r="23" stroke="currentColor" stroke-width="2"/><path d="M14 25l7 7 13-15" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
					<p data-sk-modal-success-message></p>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>

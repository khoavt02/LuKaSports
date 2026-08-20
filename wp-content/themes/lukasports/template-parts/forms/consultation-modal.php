<?php
/**
 * Consultation modal — the destination for every `data-lukasports-cta="consult"`
 * button site-wide (see assets/js/main.js). Rendered once here in the
 * footer rather than per-CTA, so there's exactly one instance no
 * matter how many trigger buttons exist on a page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sk_contact = lukasports_get_contact_info();
?>
<div class="sk-modal" id="sk-consultation-modal" hidden aria-hidden="true">
	<div class="sk-modal__overlay" data-sk-modal-close></div>
	<div class="sk-modal__panel" role="dialog" aria-modal="true" aria-labelledby="sk-modal-title">
		<button type="button" class="sk-modal__close" data-sk-modal-close>
			<span class="sk-visually-hidden"><?php esc_html_e( 'Đóng', 'lukasports' ); ?></span>
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 4l12 12M16 4L4 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
		</button>

		<div class="sk-modal__body" data-sk-modal-content>
			<h2 class="sk-modal__title" id="sk-modal-title"><?php esc_html_e( 'Nhận tư vấn từ LukaSports', 'lukasports' ); ?></h2>
			<p class="sk-modal__subtitle"><?php esc_html_e( 'Để lại thông tin, nhân viên LukaSports sẽ liên hệ tư vấn size, số lượng và giá trong thời gian sớm nhất.', 'lukasports' ); ?></p>

			<form id="sk-consultation-form" novalidate data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
				<?php wp_nonce_field( 'lukasports_submit_lead', 'nonce' ); ?>
				<input type="hidden" name="product_id" id="sk-lead-product-id" value="" />
				<input type="hidden" name="source" id="sk-lead-source" value="website" />

				<p class="sk-field-group">
					<label for="sk-lead-name"><?php esc_html_e( 'Tên', 'lukasports' ); ?> <span aria-hidden="true">*</span></label>
					<input type="text" class="sk-field" id="sk-lead-name" name="name" required autocomplete="name" />
					<span class="sk-field-error" data-error-for="name"></span>
				</p>

				<p class="sk-field-group">
					<label for="sk-lead-phone"><?php esc_html_e( 'Số điện thoại', 'lukasports' ); ?> <span aria-hidden="true">*</span></label>
					<input type="tel" class="sk-field" id="sk-lead-phone" name="phone" required autocomplete="tel" placeholder="<?php echo esc_attr( $sk_contact['hotline_display'] ); ?>" />
					<span class="sk-field-error" data-error-for="phone"></span>
				</p>

				<p class="sk-field-group">
					<label for="sk-lead-product-name"><?php esc_html_e( 'Sản phẩm quan tâm', 'lukasports' ); ?></label>
					<input type="text" class="sk-field" id="sk-lead-product-name" name="product_name" placeholder="<?php esc_attr_e( 'VD: Áo bóng đá đội tuyển Việt Nam, size L', 'lukasports' ); ?>" />
				</p>

				<p class="sk-field-group">
					<label for="sk-lead-message"><?php esc_html_e( 'Nội dung', 'lukasports' ); ?></label>
					<textarea class="sk-field" id="sk-lead-message" name="message" rows="3" placeholder="<?php esc_attr_e( 'Số lượng, màu sắc, ghi chú thêm… (không bắt buộc)', 'lukasports' ); ?>"></textarea>
				</p>

				<button type="submit" class="sk-btn sk-btn--primary sk-btn--lg sk-btn--block">
					<?php esc_html_e( 'Gửi yêu cầu tư vấn', 'lukasports' ); ?>
				</button>
				<p class="sk-modal__form-error" data-form-error hidden></p>
			</form>

			<div class="sk-modal__success" data-sk-modal-success hidden>
				<svg width="48" height="48" viewBox="0 0 48 48" fill="none" aria-hidden="true"><circle cx="24" cy="24" r="23" stroke="#E4231B" stroke-width="2"/><path d="M14 25l7 7 13-15" stroke="#E4231B" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
				<p data-sk-modal-success-message></p>
			</div>
		</div>
	</div>
</div>

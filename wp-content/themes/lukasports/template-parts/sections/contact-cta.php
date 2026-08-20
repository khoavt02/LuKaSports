<?php
/**
 * Final, quiet contact moment — light blue background, the counterpart
 * to the dark "Custom Design" CTA earlier in the homepage flow.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact = lukasports_get_contact_info();
?>
<section class="sk-contact-cta">
	<div class="sk-container sk-contact-cta__inner">
		<h2 class="sk-contact-cta__title"><?php esc_html_e( 'Bạn đang tìm mẫu áo cho đội?', 'lukasports' ); ?></h2>
		<p class="sk-contact-cta__subtitle">
			<?php esc_html_e( 'Gửi nhu cầu của bạn. LukaSports sẽ tư vấn mẫu phù hợp.', 'lukasports' ); ?>
		</p>
		<div class="sk-contact-cta__actions">
			<a class="sk-btn sk-btn--primary sk-btn--lg" href="<?php echo esc_url( $contact['messenger_url'] ); ?>" target="_blank" rel="noopener" data-lukasports-cta="consult" data-source="contact_cta">
				<?php echo esc_html( $contact['primary_cta_text'] ); ?>
			</a>
			<div class="sk-contact-cta__channels">
				<a class="sk-btn sk-btn--messenger" href="<?php echo esc_url( $contact['messenger_url'] ); ?>" target="_blank" rel="noopener">Messenger</a>
				<a class="sk-btn sk-btn--zalo" href="<?php echo esc_url( $contact['zalo_url'] ); ?>" target="_blank" rel="noopener">Zalo</a>
				<a class="sk-btn sk-btn--call" href="tel:<?php echo esc_attr( $contact['hotline_tel'] ); ?>"><?php echo esc_html( $contact['hotline_display'] ); ?></a>
			</div>
		</div>
	</div>
</section>

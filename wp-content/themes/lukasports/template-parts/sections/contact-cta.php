<?php
/**
 * Red CTA band — "Bạn đang tìm mẫu áo cho đội?" with the consult modal
 * button plus Messenger / Zalo / hotline from the site's contact
 * settings. Also used on the About page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact = lukasports_get_contact_info();
?>
<section class="sk-cta-band" aria-labelledby="sk-cta-band-title">
	<span class="sk-cta-band__slab" aria-hidden="true"></span>
	<div class="sk-container sk-cta-band__inner">
		<div class="sk-cta-band__text">
			<h2 id="sk-cta-band-title" class="sk-cta-band__title sk-display"><?php esc_html_e( 'Bạn đang tìm mẫu áo cho đội?', 'lukasports' ); ?></h2>
			<p class="sk-cta-band__lead"><?php esc_html_e( 'Gửi ý tưởng hoặc logo, DALETIC tư vấn size, số lượng và báo giá cho cả đội.', 'lukasports' ); ?></p>
		</div>
		<div class="sk-cta-band__actions">
			<a class="sk-btn sk-cta-band__primary" href="<?php echo esc_url( home_url( '/lien-he/' ) ); ?>" data-lukasports-cta="consult" data-source="contact_cta">
				<?php echo esc_html( $contact['primary_cta_text'] ); ?>
			</a>
			<div class="sk-cta-band__channels">
				<a class="sk-btn sk-btn--outline-light sk-btn--sm" href="<?php echo esc_url( $contact['messenger_url'] ); ?>" target="_blank" rel="noopener">Messenger</a>
				<a class="sk-btn sk-btn--outline-light sk-btn--sm" href="<?php echo esc_url( $contact['zalo_url'] ); ?>" target="_blank" rel="noopener">Zalo</a>
			</div>
			<a class="sk-cta-band__phone" href="tel:<?php echo esc_attr( $contact['hotline_tel'] ); ?>">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg>
				<?php echo esc_html( $contact['hotline_display'] ); ?>
			</a>
		</div>
	</div>
</section>

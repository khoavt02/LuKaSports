<?php
/**
 * Consult strip — full-bleed dark navy band (not a bordered light
 * card), so it reads as a distinct "talk to us" moment rather than a
 * boxed form section. `data-lukasports-cta` is the hook the
 * consultation modal (assets/js/main.js) listens for — clicking the
 * primary button opens the on-page form instead of navigating away;
 * Messenger/Zalo/Gọi ngay stay as direct links.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$contact       = lukasports_get_contact_info();
$is_consult    = $product instanceof WC_Product && has_term( 'ao-team', 'product_cat', $product->get_id() );
$primary_label = $is_consult ? $contact['secondary_cta_text'] : $contact['primary_cta_text'];
?>
<div class="sk-product-cta">
	<button
		type="button"
		class="sk-product-cta__primary"
		data-lukasports-cta="consult"
		data-source="product_page"
		data-sk-consult-product
		<?php if ( $product instanceof WC_Product ) : ?>
			data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
			data-product-name="<?php echo esc_attr( $product->get_name() ); ?>"
		<?php endif; ?>
	>
		<?php echo esc_html( $primary_label ); ?> <span class="sk-btn__arrow" aria-hidden="true">→</span>
	</button>
	<div class="sk-product-cta__links">
		<a class="sk-product-cta__link sk-product-cta__link--messenger" href="<?php echo esc_url( $contact['messenger_url'] ); ?>" target="_blank" rel="noopener">
			<svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2C5.6 2 2 5.3 2 9.4c0 2.3 1.1 4.3 2.9 5.7L4.6 18l2.9-1.6c.8.2 1.6.3 2.5.3 4.4 0 8-3.3 8-7.3S14.4 2 10 2z"/></svg>
			<span><?php esc_html_e( 'Messenger', 'lukasports' ); ?></span>
		</a>
		<a class="sk-product-cta__link sk-product-cta__link--zalo" href="<?php echo esc_url( $contact['zalo_url'] ); ?>" target="_blank" rel="noopener">
			<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="2" y="2" width="16" height="16" rx="4" stroke="currentColor" stroke-width="1.7"/><path d="M6.5 7h5.5l-5.5 6h5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
			<span><?php esc_html_e( 'Zalo', 'lukasports' ); ?></span>
		</a>
		<a class="sk-product-cta__link sk-product-cta__link--call" href="tel:<?php echo esc_attr( $contact['hotline_tel'] ); ?>">
			<svg width="17" height="17" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M4 3h3l1.5 4L6.7 8.6a10 10 0 005.7 5.7L14 12.5l4 1.5v3a1.5 1.5 0 01-1.6 1.5A15 15 0 013 5.6 1.5 1.5 0 014.5 4z"/></svg>
			<span><?php echo esc_html( $contact['hotline_display'] ); ?></span>
		</a>
	</div>
</div>

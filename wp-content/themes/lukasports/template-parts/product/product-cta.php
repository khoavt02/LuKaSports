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
		<?php if ( $product instanceof WC_Product ) : ?>
			data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
			data-product-name="<?php echo esc_attr( $product->get_name() ); ?>"
		<?php endif; ?>
	>
		<?php echo esc_html( $primary_label ); ?> <span aria-hidden="true">→</span>
	</button>
	<div class="sk-product-cta__links">
		<a href="<?php echo esc_url( $contact['messenger_url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Messenger', 'lukasports' ); ?></a>
		<a href="<?php echo esc_url( $contact['zalo_url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Zalo', 'lukasports' ); ?></a>
		<a href="tel:<?php echo esc_attr( $contact['hotline_tel'] ); ?>"><?php echo esc_html( $contact['hotline_display'] ); ?></a>
	</div>
</div>

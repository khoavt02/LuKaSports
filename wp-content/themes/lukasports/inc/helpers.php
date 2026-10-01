<?php
/**
 * Small, dependency-free helper functions shared across templates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site-wide contact channels + CTA copy used by every CTA button on
 * the front end. Single source of truth: DALETIC Core → Cài đặt liên
 * hệ (Products → DALETIC → Settings). Falls back to sensible
 * defaults if the plugin is inactive so the theme never fatals on its
 * own, but the admin-editable values always win when present.
 *
 * @return array{hotline_display:string, hotline_tel:string, messenger_url:string, zalo_url:string, facebook_url:string, address:string, primary_cta_text:string, secondary_cta_text:string}
 */
function lukasports_get_contact_info() {
	if ( function_exists( 'lukasports_core_get_settings' ) ) {
		return apply_filters( 'lukasports_contact_info', lukasports_core_get_settings() );
	}

	$defaults = array(
		'hotline_display'    => '0987 000 111',
		'hotline_tel'        => '+84987000111',
		'messenger_url'      => 'https://m.me/daletic.vn',
		'zalo_url'           => 'https://zalo.me/0987000111',
		'facebook_url'       => 'https://facebook.com/daletic.vn',
		'address'            => 'Số 12, Ngõ 88, Đường Láng, Đống Đa, Hà Nội',
		'primary_cta_text'   => 'Tư vấn ngay',
		'secondary_cta_text' => 'Đặt áo cho đội',
	);

	return apply_filters( 'lukasports_contact_info', $defaults );
}

/**
 * Homepage hero banner image URL — admin-editable via DALETIC → Settings
 * (media library picker, stored as an attachment ID), falling back to the
 * theme's bundled banner if nothing has been chosen yet.
 */
function lukasports_get_hero_image_url() {
	$attachment_id = 0;

	if ( function_exists( 'lukasports_core_get_settings' ) ) {
		$settings      = lukasports_core_get_settings();
		$attachment_id = isset( $settings['hero_image_id'] ) ? (int) $settings['hero_image_id'] : 0;
	}

	$url = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'lukasports-hero-banner' ) : '';

	if ( ! $url ) {
		$url = LUKASPORTS_THEME_URI . '/assets/images/hero-placeholder.svg';
	}

	return apply_filters( 'lukasports_hero_image_url', $url );
}

/**
 * Formats a WooCommerce price string, falling back gracefully outside
 * a product context so templates never have to null-check.
 */
function lukasports_format_price( $amount ) {
	if ( ! function_exists( 'wc_price' ) || null === $amount ) {
		return '';
	}

	return wc_price( $amount );
}

/**
 * Truncates plain text to a word-safe length for card excerpts.
 */
function lukasports_excerpt( $text, $words = 18 ) {
	$text = wp_strip_all_tags( $text );

	return wp_trim_words( $text, $words, '…' );
}

/**
 * WC_Product_Variable::get_regular_price() is empty on the parent —
 * only its variations carry a regular/sale price. Card-level price
 * display and the sale badge both need "what's the regular price to
 * show", so that branching lives here once instead of twice.
 */
function lukasports_get_regular_price_for_display( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return 0.0;
	}

	if ( $product->is_type( 'variable' ) ) {
		return (float) $product->get_variation_regular_price( 'min', true );
	}

	return (float) $product->get_regular_price();
}

/**
 * "-20%" style badge text for a product on sale. Returns '' if the
 * product isn't on sale or the regular price is zero (can't divide).
 */
function lukasports_sale_badge_text( $product ) {
	if ( ! $product instanceof WC_Product || ! $product->is_on_sale() ) {
		return '';
	}

	$regular = lukasports_get_regular_price_for_display( $product );
	$sale    = (float) $product->get_price();

	if ( $regular <= 0 || $regular <= $sale ) {
		return '';
	}

	$percent = round( ( ( $regular - $sale ) / $regular ) * 100 );

	return sprintf( '-%d%%', $percent );
}

/**
 * Renders an <img> with width/height and lazy-loading for anything that
 * isn't the first visible image on the page (see inc/performance.php).
 */
function lukasports_attachment_image( $attachment_id, $size = 'lukasports-card', $loading = 'lazy', $extra_class = '', $alt = '' ) {
	if ( ! $attachment_id ) {
		return '';
	}

	$attr = array(
		'loading'  => $loading,
		'class'    => trim( 'sk-img ' . $extra_class ),
		'decoding' => 'async',
	);

	// Explicit $alt always wins — callers showing a real product/post
	// pass its name here rather than relying on attachment metadata,
	// which is often blank for programmatically-uploaded images.
	if ( '' !== $alt ) {
		$attr['alt'] = $alt;
	}

	return wp_get_attachment_image( $attachment_id, $size, false, $attr );
}

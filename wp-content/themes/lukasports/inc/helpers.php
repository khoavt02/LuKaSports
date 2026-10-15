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
		$url = lukasports_image_url( 'hero-jerseys.webp' );
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

/**
 * URL of a bundled image (path relative to assets/images/) with a
 * ?ver= of its modification time, so browsers fetch the new file as
 * soon as it's replaced instead of showing a cached copy.
 */
function lukasports_image_url( $relative_path ) {
	return add_query_arg( 'ver', lukasports_asset_version( 'images/' . $relative_path ), LUKASPORTS_THEME_URI . '/assets/images/' . $relative_path );
}

/**
 * Swatch color for a color attribute term name (pa_color). Kept in one
 * place for product cards; assets/js/product-variations.js mirrors it.
 */
function lukasports_color_hex( $name ) {
	$map = array(
		'trắng'      => '#ffffff',
		'đen'        => '#141414',
		'đỏ'         => '#e10600',
		'xanh dương' => '#1b4fbf',
		'xanh navy'  => '#1b2a4a',
		'vàng'       => '#f2c14e',
		'cam'        => '#f47c20',
		'xám'        => '#9ca3af',
		'xám than'   => '#4a4a4a',
		'be'         => '#d8cbb0',
		'xanh rêu'   => '#5c5f4a',
		'xanh lá'    => '#16a34a',
		'hồng'       => '#ec4899',
	);
	$key = function_exists( 'mb_strtolower' ) ? mb_strtolower( trim( $name ) ) : strtolower( trim( $name ) );
	return $map[ $key ] ?? '#cbd5e1';
}

/**
 * Card/gallery badge: sale percentage first, then "Bán chạy" for
 * featured products, then "Mới" for anything published in the last
 * 30 days. Returns array( label, modifier ) or null.
 */
function lukasports_product_badge( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return null;
	}
	$sale = lukasports_sale_badge_text( $product );
	if ( '' !== $sale ) {
		return array( $sale, 'sale' );
	}
	if ( $product->is_featured() ) {
		return array( __( 'Bán chạy', 'lukasports' ), 'hot' );
	}
	$created = $product->get_date_created();
	if ( $created && $created->getTimestamp() > time() - 30 * DAY_IN_SECONDS ) {
		return array( __( 'Mới', 'lukasports' ), 'new' );
	}
	return null;
}

/** Category slugs that make up the "Thiết kế theo yêu cầu" quick filter. */
function lukasports_custom_design_categories() {
	return apply_filters( 'lukasports_custom_design_categories', array( 'ao-bong-da-thiet-ke', 'ao-team' ) );
}

/** Most recently created product collection (the "Bộ sưu tập mới" pill). */
function lukasports_newest_collection() {
	if ( ! taxonomy_exists( 'product_collection' ) ) {
		return null;
	}
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_collection',
			'hide_empty' => true,
			'orderby'    => 'term_id',
			'order'      => 'DESC',
			'number'     => 1,
		)
	);
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
}

/**
 * Lowest and highest product price in the catalog (for the price range
 * filter), from WooCommerce's lookup table. Cached for an hour.
 *
 * @return array{0:int,1:int}
 */
function lukasports_catalog_price_range() {
	$range = get_transient( 'lukasports_price_range' );
	if ( false === $range ) {
		global $wpdb;
		$row   = $wpdb->get_row( "SELECT MIN(min_price) AS mn, MAX(max_price) AS mx FROM {$wpdb->wc_product_meta_lookup}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$range = array( (int) floor( (float) ( $row->mn ?? 0 ) ), (int) ceil( (float) ( $row->mx ?? 0 ) ) );
		set_transient( 'lukasports_price_range', $range, HOUR_IN_SECONDS );
	}
	return $range;
}

/**
 * Attachment ID of a product category's picture. "banner" prefers the
 * wide action/lifestyle photo (term meta lukasports_banner_id), "thumb"
 * the category thumbnail (Products → Categories → Thumbnail); each
 * falls back to the other. 0 when the category has neither.
 */
function lukasports_category_image_id( $term, $prefer = 'thumb' ) {
	if ( ! $term instanceof WP_Term ) {
		$term = get_term_by( 'slug', (string) $term, 'product_cat' );
	}
	if ( ! $term instanceof WP_Term ) {
		return 0;
	}
	$banner = (int) get_term_meta( $term->term_id, 'lukasports_banner_id', true );
	$thumb  = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
	return 'banner' === $prefer ? ( $banner ? $banner : $thumb ) : ( $thumb ? $thumb : $banner );
}

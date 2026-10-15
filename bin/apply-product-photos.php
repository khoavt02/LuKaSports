<?php
/**
 * Real product photos: every bin/product-photos/{product-slug}.jpg
 * becomes that product's main image (the generated illustration moves
 * into the gallery).
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/apply-product-photos.php
 * Also called at the end of seed-products.php. Safe to re-run: a photo
 * already attached (same file hash) is not uploaded twice.
 */

if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce must be active.' );
}

require_once ABSPATH . 'wp-admin/includes/image.php';

$photo_dir = __DIR__ . '/product-photos';
$applied   = 0;

foreach ( glob( $photo_dir . '/*.jpg' ) as $file ) {
	$slug    = basename( $file, '.jpg' );
	$product = get_page_by_path( $slug, OBJECT, 'product' );
	if ( ! $product ) {
		WP_CLI::warning( "No product with slug {$slug}" );
		continue;
	}
	$wc_product = wc_get_product( $product->ID );
	$hash       = md5_file( $file );

	$current = (int) $wc_product->get_image_id();
	if ( $current && get_post_meta( $current, '_lukasports_photo_hash', true ) === $hash ) {
		continue;
	}

	$upload = wp_upload_bits( 'daletic-photo-' . $slug . '.jpg', null, file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( $upload['error'] ) {
		WP_CLI::warning( $upload['error'] );
		continue;
	}
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => $wc_product->get_name(),
			'post_status'    => 'inherit',
			// Never let an attachment slug collide with a category/page URL.
			'post_name'      => 'daletic-photo-' . $slug,
		),
		$upload['file'],
		$product->ID
	);
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $wc_product->get_name() );
	update_post_meta( $attachment_id, '_lukasports_photo_hash', $hash );

	// Keep the previous main image as the first gallery image.
	$gallery = $wc_product->get_gallery_image_ids();
	if ( $current && ! in_array( $current, $gallery, true ) ) {
		array_unshift( $gallery, $current );
	}
	$wc_product->set_gallery_image_ids( $gallery );
	$wc_product->set_image_id( $attachment_id );
	$wc_product->save();
	$applied++;
	WP_CLI::log( "Photo applied: {$slug} (#{$attachment_id})" );
}

WP_CLI::success( "Applied {$applied} product photo(s)." );

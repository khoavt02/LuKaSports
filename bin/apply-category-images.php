<?php
/**
 * Category pictures from bin/category-images/:
 *   thumb-{category-slug}.jpg  → category thumbnail (homepage tiles, cards)
 *   banner-{category-slug}.jpg → lukasports_banner_id term meta (dark page
 *                                band on the category page, "Câu chuyện thể
 *                                thao" / lookbook sections)
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/apply-category-images.php
 * Safe to re-run: an unchanged file (same hash) is not uploaded again.
 */

require_once ABSPATH . 'wp-admin/includes/image.php';

$applied = 0;
foreach ( glob( __DIR__ . '/category-images/*.jpg' ) as $file ) {
	if ( ! preg_match( '/^(thumb|banner)-(.+)\.jpg$/', basename( $file ), $m ) ) {
		continue;
	}
	list( , $kind, $slug ) = $m;
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( ! $term ) {
		WP_CLI::warning( "No product category {$slug}" );
		continue;
	}
	$meta_key = 'thumb' === $kind ? 'thumbnail_id' : 'lukasports_banner_id';
	$hash     = md5_file( $file );
	$current  = (int) get_term_meta( $term->term_id, $meta_key, true );
	if ( $current && get_post_meta( $current, '_lukasports_photo_hash', true ) === $hash ) {
		continue;
	}

	$upload = wp_upload_bits( 'daletic-' . $kind . '-' . $slug . '.jpg', null, file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( $upload['error'] ) {
		WP_CLI::warning( $upload['error'] );
		continue;
	}
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => $term->name,
			'post_status'    => 'inherit',
			// Never let an attachment slug collide with a category/page URL.
			'post_name'      => 'daletic-' . $kind . '-' . $slug,
		),
		$upload['file']
	);
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $term->name );
	update_post_meta( $attachment_id, '_lukasports_photo_hash', $hash );
	update_term_meta( $term->term_id, $meta_key, $attachment_id );
	$applied++;
	WP_CLI::log( "{$kind}: {$slug} (#{$attachment_id})" );
}

WP_CLI::success( "Applied {$applied} category image(s)." );

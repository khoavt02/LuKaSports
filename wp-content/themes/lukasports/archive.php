<?php
/**
 * Category, tag, date and author archives for post-type "post" — same
 * layout as the blog index (template-parts/blog/listing.php), without
 * the feature card. WooCommerce product archives use
 * woocommerce/archive-product.php instead.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

get_template_part(
	'template-parts/page-band',
	null,
	array(
		'title' => wp_strip_all_tags( single_term_title( '', false ) ? single_term_title( '', false ) : get_the_archive_title() ),
		'dot'   => true,
		'intro' => wp_kses_post( get_the_archive_description() ),
	)
);

get_template_part( 'template-parts/blog/listing', null, array( 'feature' => false ) );

get_footer();

<?php
/**
 * Blog index (used for the page set as "Posts page" in Settings →
 * Reading — /blog/ in this site's URL structure). front-page.php
 * still governs the actual site root regardless of that setting.
 * Body: template-parts/blog/listing.php. Styles: blog.css.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$posts_page = (int) get_option( 'page_for_posts' );

get_template_part(
	'template-parts/page-band',
	null,
	array(
		'title' => $posts_page ? get_the_title( $posts_page ) : __( 'Blog', 'lukasports' ),
		'dot'   => true,
		'intro' => '<p>' . esc_html__( 'Kinh nghiệm chọn size, đặt đồng phục và chăm sóc áo đấu từ DALETIC.', 'lukasports' ) . '</p>',
	)
);

get_template_part( 'template-parts/blog/listing', null, array( 'feature' => true ) );

get_footer();

<?php
/**
 * Initial editorial badges (thẻ) for the custom designs. Safe to re-run;
 * edit any product's tag afterwards in wp-admin (General → "Thẻ sản phẩm").
 * Products not listed keep no tag ("Mới" is automatic for the newest 3).
 *
 * Keys: ban-chay, yeu-thich, noi-bat, doc-quyen, tron-bo, moi.
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/set-product-badges.php
 */

$badges = array(
	'bo-dong-phuc-daletic-do-sac-viet'          => 'yeu-thich',
	'bo-dong-phuc-daletic-trang-soc-do'         => 'ban-chay',
	'bo-dong-phuc-daletic-trang-do-vuon-cao'    => 'noi-bat',
	'bo-dong-phuc-daletic-do-trang-xung-kich'   => 'doc-quyen',
	'bo-dong-phuc-daletic-sai-canh-xanh-trang'  => 'ban-chay',
	'bo-dong-phuc-daletic-soc-xanh-troi'        => 'tron-bo',
	'bo-dong-phuc-daletic-vang-xanh'            => 'tron-bo',
	'ao-thiet-ke-daletic-reu-san-ho'            => 'doc-quyen',
	'ao-thiet-ke-daletic-song-trang'            => 'yeu-thich',
	'ao-thiet-ke-daletic-ngoc-bich'             => 'noi-bat',
	'ao-thiet-ke-daletic-hoa-van-vang'          => 'yeu-thich',
	'ao-thiet-ke-daletic-tia-chop-cam'          => 'ban-chay',
);

foreach ( $badges as $slug => $key ) {
	$post = get_page_by_path( $slug, OBJECT, 'product' );
	if ( ! $post ) {
		WP_CLI::warning( "No product $slug" );
		continue;
	}
	update_post_meta( $post->ID, '_lukasports_badge', $key );
	WP_CLI::log( "$slug → $key" );
}
WP_CLI::success( 'Badges set.' );

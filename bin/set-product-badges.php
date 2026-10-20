<?php
/**
 * Initial hand-picked badges (thẻ) for the custom designs. Safe to re-run;
 * edit any product's tag afterwards in wp-admin (General → "Thẻ sản phẩm").
 * Products not listed get an automatic tag by rule (see
 * lukasports_auto_badges() in the theme's inc/product-badges.php).
 *
 * Keys: ban-chay, yeu-thich, noi-bat, xu-huong, doc-quyen, tron-bo,
 * quan-tam, bst-moi, moi. "Bán chạy" has no sales data behind it yet, so it
 * is only for tags you decide on yourself.
 *
 * Run via: docker compose run --rm wpcli eval-file lukasports-bin/set-product-badges.php
 */

$badges = array(
	'bo-dong-phuc-daletic-do-sac-viet'         => 'yeu-thich',
	'bo-dong-phuc-daletic-trang-soc-do'        => 'noi-bat',
	'bo-dong-phuc-daletic-trang-do-vuon-cao'   => 'xu-huong',
	'bo-dong-phuc-daletic-do-trang-xung-kich'  => 'doc-quyen',
	'bo-dong-phuc-daletic-sai-canh-xanh-trang' => 'yeu-thich',
	'ao-thiet-ke-daletic-reu-san-ho'           => 'doc-quyen',
	'ao-thiet-ke-daletic-song-trang'           => 'xu-huong',
	'ao-thiet-ke-daletic-ngoc-bich'            => 'noi-bat',
	'ao-thiet-ke-daletic-hoa-van-vang'         => 'yeu-thich',
	'ao-thiet-ke-daletic-tia-chop-cam'         => 'xu-huong',
	'bo-dong-phuc-daletic-suong-vu-xanh-ngoc'  => 'xu-huong',
	'bo-dong-phuc-daletic-loi-van-xanh-co-vit' => 'noi-bat',
	'bo-dong-phuc-daletic-son-thuy-xanh-co-vit' => 'yeu-thich',
	'bo-dong-phuc-daletic-pha-le-xanh-ngoc'    => 'doc-quyen',
	'bo-dong-phuc-daletic-tang-may-xanh-trang' => 'xu-huong',
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
delete_transient( 'lukasports_auto_badges' );
WP_CLI::success( 'Badges set.' );

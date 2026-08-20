<?php
/**
 * Theme supports, nav menus, image sizes, WooCommerce declaration.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' )
	);

	// WooCommerce integration: we own the markup, so opt out of the
	// default gallery/zoom/lightbox JS and default cart-icon fragment.
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'gallery_thumbnail_image_width' => 150,
			'single_image_width'    => 900,
			'product_grid'          => array(
				'default_rows'    => 2,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 4,
			),
		)
	);
	// Deliberately no wc-product-gallery-zoom/lightbox/slider support: the
	// single-product gallery is a custom scroll-snap component (see
	// template-parts/product/gallery.php) instead of WooCommerce's
	// Flexslider/PhotoSwipe bundle, so we don't ship a JS gallery library
	// twice or restyle one we don't otherwise use.

	register_nav_menus(
		array(
			'primary' => __( 'Menu chính', 'lukasports' ),
			'footer'  => __( 'Menu footer', 'lukasports' ),
		)
	);

	add_image_size( 'lukasports-card', 640, 800, true );
	add_image_size( 'lukasports-hero', 1600, 1000, true );
	add_image_size( 'lukasports-thumb', 160, 200, true );
	// Product gallery main image — matches the 4:5 aspect-ratio the
	// gallery CSS actually renders at (.sk-gallery__slide). Using
	// lukasports-hero (16:10) here double-cropped every product photo:
	// once server-side to a landscape ratio, then again by object-fit
	// on the portrait CSS box.
	add_image_size( 'lukasports-gallery', 1000, 1250, true );
	// Not hard-cropped: category thumbnails are admin-uploaded photos of
	// unpredictable aspect ratio, so this is scaled to fit within bounds
	// rather than cropped, and the card CSS shows it via object-fit:
	// contain — nothing gets cut off regardless of the source shape.
	add_image_size( 'lukasports-category', 800, 800 );
	// Also uncropped: the homepage hero banner is admin-uploaded and
	// framed with object-fit: cover in CSS, so a fixed server-side crop
	// here would just crop it a second time at the wrong ratio.
	add_image_size( 'lukasports-hero-banner', 1920, 1920 );

	set_post_thumbnail_size( 640, 800 );
}
add_action( 'after_setup_theme', 'lukasports_theme_setup' );

/**
 * wp_nav_menu() never puts a class on the <a> itself, only on the <li>
 * — without this, .sk-nav__link in header.css never actually matches
 * the real menu output (only the no-menu-assigned fallback below).
 */
function lukasports_nav_menu_link_class( $atts ) {
	$atts['class'] = isset( $atts['class'] ) ? $atts['class'] . ' sk-nav__link' : 'sk-nav__link';
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'lukasports_nav_menu_link_class' );

/**
 * Default menu fallback so a fresh install doesn't render an empty
 * header before an admin has assigned a menu.
 */
function lukasports_primary_menu_fallback() {
	echo '<ul class="sk-nav__list">';
	$links = array(
		'/san-pham/'          => 'Sản phẩm',
		'/bo-suu-tap/'        => 'Bộ sưu tập',
		'/ao-bong-da-thiet-ke/' => 'Thiết kế áo',
		'/ve-lukasports/'     => 'Về LukaSports',
		'/blog/'              => 'Blog',
	);
	foreach ( $links as $url => $label ) {
		printf( '<li class="sk-nav__item"><a class="sk-nav__link" href="%s">%s</a></li>', esc_url( home_url( $url ) ), esc_html( $label ) );
	}
	echo '</ul>';
}

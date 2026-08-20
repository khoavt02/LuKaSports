<?php
/**
 * Style/script registration. Everything is versioned off the theme
 * version so cache-busting on deploy is automatic; nothing site-wide
 * loads on templates that don't need it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_enqueue_assets() {
	$css = LUKASPORTS_THEME_URI . '/assets/css';
	$js  = LUKASPORTS_THEME_URI . '/assets/js';
	$ver = LUKASPORTS_THEME_VERSION;

	// Loaded everywhere: tokens, reset, layout, buttons/cards/forms, header, footer.
	wp_enqueue_style( 'lukasports-core', $css . '/core.css', array(), $ver );
	wp_enqueue_style( 'lukasports-header', $css . '/header.css', array( 'lukasports-core' ), $ver );
	wp_enqueue_style( 'lukasports-footer', $css . '/footer.css', array( 'lukasports-core' ), $ver );

	if ( is_front_page() ) {
		wp_enqueue_style( 'lukasports-home', $css . '/home.css', array( 'lukasports-core' ), $ver );
	}

	// The category landing pages (template-category.php) are plain Pages
	// that render a product grid + shop header, so they need the same
	// styles as a real WooCommerce archive even though is_woocommerce()
	// doesn't consider them one.
	$is_category_landing = is_page_template( 'template-category.php' );
	if ( $is_category_landing || ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() ) ) ) {
		wp_enqueue_style( 'lukasports-woocommerce', $css . '/woocommerce.css', array( 'lukasports-core' ), $ver );
	}

	// Covers post/page content typography, blog archive, search results
	// and the 404 page — everything that isn't the homepage or a
	// WooCommerce template.
	if ( ! is_front_page() && ! ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) ) {
		wp_enqueue_style( 'lukasports-blog', $css . '/blog.css', array( 'lukasports-core' ), $ver );
	}

	wp_enqueue_script( 'lukasports-main', $js . '/main.js', array(), $ver, array( 'strategy' => 'defer', 'in_footer' => true ) );

	if ( function_exists( 'is_product' ) && is_product() ) {
		wp_enqueue_script( 'lukasports-product-variations', $js . '/product-variations.js', array(), $ver, array( 'strategy' => 'defer', 'in_footer' => true ) );
	}
}
add_action( 'wp_enqueue_scripts', 'lukasports_enqueue_assets' );

/**
 * We fully restyle WooCommerce markup, so its default stylesheet is
 * unused, conflicting CSS. Frontend JS (variation form, cart fragments)
 * stays intact — the variable-product selector on the product page
 * depends on it.
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

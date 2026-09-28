<?php
/**
 * Style/script registration. Every file is versioned off its own
 * modification time so cache-busting is automatic whenever a file
 * changes (the theme version rarely gets bumped between edits);
 * nothing site-wide loads on templates that don't need it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ?ver= value for a theme asset: the file's mtime, falling back to the
 * theme version if the file can't be stat'ed.
 */
function lukasports_asset_version( $relative_path ) {
	$mtime = @filemtime( LUKASPORTS_THEME_DIR . '/assets/' . $relative_path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	return $mtime ? (string) $mtime : LUKASPORTS_THEME_VERSION;
}

function lukasports_enqueue_assets() {
	$css = LUKASPORTS_THEME_URI . '/assets/css';
	$js  = LUKASPORTS_THEME_URI . '/assets/js';

	// Loaded everywhere: tokens, reset, layout, buttons/cards/forms, header, footer.
	wp_enqueue_style( 'lukasports-core', $css . '/core.css', array(), lukasports_asset_version( 'css/core.css' ) );
	wp_enqueue_style( 'lukasports-header', $css . '/header.css', array( 'lukasports-core' ), lukasports_asset_version( 'css/header.css' ) );
	wp_enqueue_style( 'lukasports-footer', $css . '/footer.css', array( 'lukasports-core' ), lukasports_asset_version( 'css/footer.css' ) );

	// The about page reuses homepage sections (team CTA, trust bar,
	// contact CTA), so it needs their styles too.
	$is_about = is_page_template( 'template-about.php' );
	if ( is_front_page() || $is_about ) {
		wp_enqueue_style( 'lukasports-home', $css . '/home.css', array( 'lukasports-core' ), lukasports_asset_version( 'css/home.css' ) );
	}

	if ( $is_about || is_page_template( array( 'template-contact.php', 'template-collections.php' ) ) ) {
		wp_enqueue_style( 'lukasports-pages', $css . '/pages.css', array( 'lukasports-core' ), lukasports_asset_version( 'css/pages.css' ) );
	}

	// The category landing pages (template-category.php) and the
	// collections index are plain Pages that render a shop header /
	// product grid, so they need the same styles as a real WooCommerce
	// archive even though is_woocommerce() doesn't consider them one.
	$is_category_landing = is_page_template( array( 'template-category.php', 'template-collections.php' ) );
	if ( $is_category_landing || ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() ) ) ) {
		wp_enqueue_style( 'lukasports-woocommerce', $css . '/woocommerce.css', array( 'lukasports-core' ), lukasports_asset_version( 'css/woocommerce.css' ) );
	}

	// Covers post/page content typography, blog archive, search results
	// and the 404 page — everything that isn't the homepage or a
	// WooCommerce template.
	if ( ! is_front_page() && ! ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) ) {
		wp_enqueue_style( 'lukasports-blog', $css . '/blog.css', array( 'lukasports-core' ), lukasports_asset_version( 'css/blog.css' ) );
	}

	wp_enqueue_script( 'lukasports-main', $js . '/main.js', array(), lukasports_asset_version( 'js/main.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );

	if ( function_exists( 'is_product' ) && is_product() ) {
		wp_enqueue_script( 'lukasports-product-variations', $js . '/product-variations.js', array(), lukasports_asset_version( 'js/product-variations.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
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

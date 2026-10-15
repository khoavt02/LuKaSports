<?php
/**
 * WooCommerce hook adjustments. We render single-product and card
 * markup ourselves (see woocommerce/*.php), so this file only touches
 * behavior we can't otherwise control from a template: tabs, the
 * default placeholder image, and turning off the review system (not
 * implemented this phase — see #64, don't ship a feature that isn't real).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_product_tabs( $tabs ) {
	unset( $tabs['reviews'] );

	$tabs['description'] = array(
		'title'    => __( 'Thông tin sản phẩm', 'lukasports' ),
		'priority' => 10,
		'callback' => 'lukasports_product_info_tab',
	);

	if ( isset( $tabs['additional_information'] ) ) {
		$tabs['additional_information']['title']    = __( 'Chất liệu', 'lukasports' );
		$tabs['additional_information']['priority'] = 20;
	}

	$tabs['size_guide'] = array(
		'title'    => __( 'Bảng size', 'lukasports' ),
		'priority' => 30,
		'callback' => 'lukasports_size_guide_tab_content',
	);

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'lukasports_product_tabs' );

/**
 * "Thông tin sản phẩm" tab: benefit tiles (product meta), promotion
 * tags, then the product description.
 */
function lukasports_product_info_tab() {
	global $product;
	$id         = $product instanceof WC_Product ? $product->get_id() : get_the_ID();
	$benefits   = function_exists( 'lukasports_core_get_product_benefits' ) ? lukasports_core_get_product_benefits( $id ) : array();
	$promotions = function_exists( 'lukasports_core_get_product_promotions' ) ? lukasports_core_get_product_promotions( $id ) : array();
	if ( function_exists( 'lukasports_core_product_supports_printing' ) && lukasports_core_product_supports_printing( $id ) ) {
		$benefits[] = __( 'Hỗ trợ in tên, số, logo theo yêu cầu', 'lukasports' );
	}

	if ( $benefits ) {
		echo '<ul class="sk-benefits">';
		foreach ( $benefits as $benefit ) {
			printf(
				'<li class="sk-benefits__item"><span class="sk-benefits__icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 20 20" fill="none"><path d="M4 10.5l4 4 8-9" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span>%s</span></li>',
				esc_html( $benefit )
			);
		}
		echo '</ul>';
	}

	if ( $promotions ) {
		echo '<ul class="sk-product__tags">';
		foreach ( $promotions as $promo ) {
			printf( '<li class="sk-tag">%s</li>', esc_html( $promo ) );
		}
		echo '</ul>';
	}

	echo '<div class="sk-tabs__content">';
	the_content();
	echo '</div>';
}

function lukasports_size_guide_tab_content() {
	get_template_part( 'template-parts/product/size-guide' );
}

/**
 * Reviews are out of scope for Phase 1 — no fake ratings, no partial UI.
 */
add_filter( 'woocommerce_review_ratings_enabled', '__return_false' );
add_filter( 'woocommerce_product_tabs_reviews_priority', '__return_null' );

function lukasports_placeholder_image_src() {
	return lukasports_image_url( 'placeholder-product.svg' );
}
add_filter( 'woocommerce_placeholder_img_src', 'lukasports_placeholder_image_src' );

/**
 * Related products: keep the WooCommerce query, but hand rendering to
 * our own card template so it matches best-sellers/category grids.
 */
function lukasports_related_products_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'lukasports_related_products_args' );

/**
 * Clean top-level category URLs (/ao-bong-da/) without needing a Page
 * per category: when a single-segment request matches a product_cat
 * slug and no Page owns that slug, swap the query over to the native
 * category archive. The shop template (woocommerce/archive-product.php)
 * then renders it — same toolbar, sorting and pagination as /san-pham/.
 * A Page using template-category.php still wins if an admin creates one.
 */
function lukasports_route_clean_category_url( $query_vars ) {
	if ( is_admin() || ! taxonomy_exists( 'product_cat' ) ) {
		return $query_vars;
	}

	$slug = '';
	if ( ! empty( $query_vars['pagename'] ) ) {
		$slug = $query_vars['pagename'];
	} elseif ( ! empty( $query_vars['name'] ) && empty( $query_vars['post_type'] ) ) {
		$slug = $query_vars['name'];
	}

	if ( '' === $slug || false !== strpos( $slug, '/' ) ) {
		return $query_vars;
	}

	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( ! $term instanceof WP_Term ) {
		return $query_vars;
	}
	// get_page_by_path() also matches attachments (an image titled like
	// the category); only a real Page may take over the clean URL.
	$page = get_page_by_path( $slug );
	if ( $page && 'page' === $page->post_type ) {
		return $query_vars;
	}

	$routed = array( 'product_cat' => $term->slug );
	if ( ! empty( $query_vars['paged'] ) ) {
		$routed['paged'] = (int) $query_vars['paged'];
	}

	return $routed;
}
add_filter( 'request', 'lukasports_route_clean_category_url' );

/**
 * WooCommerce's own product-category archive (/product-category/slug/)
 * always exists alongside the clean top-level URL. Redirect to the
 * clean URL so there's exactly one canonical address per category —
 * but only when the request isn't already on it, since the routing
 * above serves the clean URL through that very same archive.
 */
function lukasports_redirect_native_category_url() {
	if ( ! is_product_category() ) {
		return;
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term ) {
		return;
	}

	$target = home_url( '/' . $term->slug . '/' );
	$paged  = (int) get_query_var( 'paged' );
	if ( $paged > 1 ) {
		$target = trailingslashit( $target ) . 'page/' . $paged . '/';
	}

	$request_path = (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( untrailingslashit( $request_path ) === untrailingslashit( (string) wp_parse_url( $target, PHP_URL_PATH ) ) ) {
		return;
	}

	// Keep ?orderby= etc. across the hop.
	if ( ! empty( $_GET ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$target = add_query_arg( map_deep( wp_unslash( $_GET ), 'sanitize_text_field' ), $target ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	wp_safe_redirect( $target, 301 );
	exit;
}
add_action( 'template_redirect', 'lukasports_redirect_native_category_url' );

/**
 * Category links generated anywhere by WordPress/WooCommerce (product
 * meta, widgets, breadcrumbs) point straight at the clean URL instead
 * of taking a 301 hop through /product-category/.
 */
function lukasports_clean_category_term_link( $url, $term, $taxonomy ) {
	if ( 'product_cat' !== $taxonomy ) {
		return $url;
	}
	return home_url( '/' . $term->slug . '/' );
}
add_filter( 'term_link', 'lukasports_clean_category_term_link', 10, 3 );

/**
 * Breadcrumb trail (visible link + BreadcrumbList schema @id) would
 * otherwise point at the /product-category/slug/ URL that the
 * redirect above immediately 301s away from — rewrite it to the
 * clean URL so neither users nor search engines hit that extra hop.
 */
function lukasports_rewrite_breadcrumb_category_urls( $crumbs ) {
	foreach ( $crumbs as &$crumb ) {
		if ( empty( $crumb[1] ) ) {
			continue;
		}
		$path = wp_parse_url( $crumb[1], PHP_URL_PATH );
		if ( ! is_string( $path ) || '' === $path ) {
			continue;
		}
		$term = get_term_by( 'slug', basename( untrailingslashit( $path ) ), 'product_cat' );
		if ( $term instanceof WP_Term ) {
			$crumb[1] = home_url( '/' . $term->slug . '/' );
		}
	}
	unset( $crumb );

	// WooCommerce only adds the shop crumb when the shop slug is part of
	// the product permalink base; ours isn't, so category and product
	// pages would skip straight from "Trang chủ" to the category.
	$shop_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
	if ( $shop_id > 0 && ( is_product_taxonomy() || is_product() ) ) {
		$shop_url = get_permalink( $shop_id );
		if ( ! in_array( $shop_url, wp_list_pluck( $crumbs, 1 ), true ) ) {
			array_splice( $crumbs, 1, 0, array( array( get_the_title( $shop_id ), $shop_url ) ) );
		}
	}

	return $crumbs;
}
add_filter( 'woocommerce_get_breadcrumb', 'lukasports_rewrite_breadcrumb_category_urls' );

/**
 * Upsells are a recommendation feature — explicitly out of scope for
 * Phase 1 (see #16, "Advanced recommendation engine").
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

/**
 * Favicon from the bundled brand mark (assets/images/brand/) until an admin sets a Site Icon
 * (Giao diện → Tùy biến → Nhận diện site), which WordPress then outputs itself.
 */
function lukasports_fallback_favicon() {
	if ( has_site_icon() ) {
		return;
	}
	printf( '<link rel="icon" type="image/png" sizes="48x48" href="%s" />' . "\n", esc_url( lukasports_image_url( 'brand/favicon-48.png' ) ) );
	printf( '<link rel="apple-touch-icon" href="%s" />' . "\n", esc_url( lukasports_image_url( 'brand/apple-touch-icon.png' ) ) );
}
add_action( 'wp_head', 'lukasports_fallback_favicon' );
add_action( 'admin_head', 'lukasports_fallback_favicon' );

/**
 * Quick-filter pills on product listings (?loai=):
 *   co-san       — ready-made models (everything outside the custom-design categories)
 *   theo-yeu-cau — custom design / team uniform categories
 * Price range (?min_price= / ?max_price=) is handled by WooCommerce itself.
 */
function lukasports_quick_filter_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( ! ( $query->is_post_type_archive( 'product' ) || $query->is_tax( array( 'product_cat', 'product_collection' ) ) ) ) {
		return;
	}
	$type = isset( $_GET['loai'] ) ? sanitize_key( wp_unslash( $_GET['loai'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! in_array( $type, array( 'co-san', 'theo-yeu-cau' ), true ) ) {
		return;
	}
	$tax_query   = (array) $query->get( 'tax_query' );
	$tax_query[] = array(
		'taxonomy' => 'product_cat',
		'field'    => 'slug',
		'terms'    => lukasports_custom_design_categories(),
		'operator' => 'theo-yeu-cau' === $type ? 'IN' : 'NOT IN',
	);
	$query->set( 'tax_query', $tax_query );
}
add_action( 'woocommerce_product_query', 'lukasports_quick_filter_query' );

/** Price range cache must follow product price changes. */
function lukasports_flush_price_range() {
	delete_transient( 'lukasports_price_range' );
}
add_action( 'woocommerce_update_product', 'lukasports_flush_price_range' );
add_action( 'woocommerce_new_product', 'lukasports_flush_price_range' );


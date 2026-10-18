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
 * "Thông tin sản phẩm" tab: feature list (product benefits meta) with
 * icon tiles, promotion tags and the description on the left; dark
 * detail tiles on the right — the product's extra gallery photos, each
 * captioned with one of its real attributes (Chất liệu, Bộ môn …).
 * Anything without data is simply left out.
 */
function lukasports_product_info_tab() {
	global $product;
	$id         = $product instanceof WC_Product ? $product->get_id() : get_the_ID();
	$benefits   = function_exists( 'lukasports_core_get_product_benefits' ) ? lukasports_core_get_product_benefits( $id ) : array();
	$promotions = function_exists( 'lukasports_core_get_product_promotions' ) ? lukasports_core_get_product_promotions( $id ) : array();
	if ( function_exists( 'lukasports_core_product_supports_printing' ) && lukasports_core_product_supports_printing( $id ) ) {
		$benefits[] = __( 'Hỗ trợ in tên, số, logo theo yêu cầu', 'lukasports' );
	}

	// Line icons cycled over the feature list (fabric, shield, team, print).
	$icons = array(
		'<path d="M3 8c3-2 6 2 9 0s6-2 9 0"/><path d="M3 13c3-2 6 2 9 0s6-2 9 0"/><path d="M3 18c3-2 6 2 9 0s6-2 9 0"/>',
		'<path d="M12 3l8 3v6c0 4.5-3.4 8.3-8 9-4.6-.7-8-4.5-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
		'<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.8-3.6 3.4-5.5 6.5-5.5s5.7 1.9 6.5 5.5"/><path d="M16 4.8a3.5 3.5 0 0 1 0 6.4"/><path d="M18 14.8c1.8.7 3 2.4 3.5 5.2"/>',
		'<path d="M4 20l1-4L16.5 4.5a2.1 2.1 0 0 1 3 3L8 19l-4 1z"/><path d="M14.5 6.5l3 3"/>',
	);

	// Detail tiles: extra photos paired with visible attributes.
	$tiles = array();
	if ( $product instanceof WC_Product ) {
		$captions = array();
		foreach ( $product->get_attributes() as $attribute ) {
			if ( ! $attribute->get_visible() || $attribute->get_variation() ) {
				continue;
			}
			$values = $attribute->is_taxonomy()
				? wc_get_product_terms( $id, $attribute->get_name(), array( 'fields' => 'names' ) )
				: $attribute->get_options();
			if ( $values ) {
				$captions[] = array( wc_attribute_label( $attribute->get_name(), $product ), implode( ', ', $values ) );
			}
		}
		foreach ( array_slice( $product->get_gallery_image_ids(), 0, 3 ) as $index => $image_id ) {
			$tiles[] = array( $image_id, $captions[ $index ] ?? null );
		}
	}

	echo '<div class="sk-info-tab' . ( $tiles ? ' sk-info-tab--has-tiles' : '' ) . '">';
	echo '<div class="sk-info-tab__main">';

	if ( $benefits ) {
		echo '<ul class="sk-features">';
		foreach ( array_values( $benefits ) as $index => $benefit ) {
			printf(
				'<li class="sk-features__item"><span class="sk-features__icon" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">%1$s</svg></span><h3 class="sk-features__title">%2$s</h3></li>',
				$icons[ $index % count( $icons ) ], // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
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
	echo '</div>';

	if ( $tiles ) {
		echo '<div class="sk-detail-tiles">';
		foreach ( $tiles as $tile ) {
			echo '<figure class="sk-detail-tile">';
			echo '<div class="sk-detail-tile__media">' . lukasports_attachment_image( $tile[0], 'lukasports-card', 'lazy', '', $product->get_name() ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( $tile[1] ) {
				printf(
					'<figcaption class="sk-detail-tile__caption"><span class="sk-detail-tile__label">%1$s</span><span class="sk-detail-tile__text">%2$s</span></figcaption>',
					esc_html( $tile[1][0] ),
					esc_html( $tile[1][1] )
				);
			}
			echo '</figure>';
		}
		echo '</div>';
	}

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


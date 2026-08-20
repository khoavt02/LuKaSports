<?php
/**
 * Global WooCommerce product attributes (Size, Color, Sport, Material,
 * Season, Gender). WooCommerce registers the pa_* taxonomies itself
 * once rows exist in the wc_attribute_taxonomies table — we only need
 * to seed those rows, which happens once on plugin activation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_core_default_attributes() {
	return array(
		'size'     => __( 'Size', 'lukasports-core' ),
		'color'    => __( 'Màu sắc', 'lukasports-core' ),
		'sport'    => __( 'Loại hình vận động', 'lukasports-core' ),
		'material' => __( 'Chất liệu', 'lukasports-core' ),
		'season'   => __( 'Năm ra mắt', 'lukasports-core' ),
		'gender'   => __( 'Giới tính', 'lukasports-core' ),
	);
}

/**
 * Idempotent: safe to call multiple times (checked against
 * wc_get_attribute_taxonomies() before inserting).
 */
function lukasports_core_register_default_attributes() {
	if ( ! function_exists( 'wc_create_attribute' ) || ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
		return;
	}

	$existing_slugs = wp_list_pluck( (array) wc_get_attribute_taxonomies(), 'attribute_name' );

	foreach ( lukasports_core_default_attributes() as $slug => $label ) {
		if ( in_array( $slug, $existing_slugs, true ) ) {
			continue;
		}

		wc_create_attribute(
			array(
				'name'         => $label,
				'slug'         => $slug,
				'type'         => 'select',
				'order_by'     => 'menu_order',
				'has_archives' => false,
			)
		);
	}

	delete_transient( 'wc_attribute_taxonomies' );
}

/**
 * "Collections" (e.g. a seasonal drop, "World Cup 2026") are a
 * cross-cutting grouping distinct from Product Category — a product
 * keeps its one category but can belong to any number of collections.
 * Plain tag-style taxonomy so admins can create new ones freely
 * from the product editor without a separate management screen.
 */
function lukasports_core_register_collection_taxonomy() {
	register_taxonomy(
		'product_collection',
		'product',
		array(
			'label'             => __( 'Bộ sưu tập', 'lukasports-core' ),
			'labels'            => array(
				'name'          => __( 'Bộ sưu tập', 'lukasports-core' ),
				'singular_name' => __( 'Bộ sưu tập', 'lukasports-core' ),
				'add_new_item'  => __( 'Thêm bộ sưu tập', 'lukasports-core' ),
				'search_items'  => __( 'Tìm bộ sưu tập', 'lukasports-core' ),
			),
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'public'            => true,
			'rewrite'           => array( 'slug' => 'bo-suu-tap' ),
		)
	);
}
add_action( 'init', 'lukasports_core_register_collection_taxonomy' );

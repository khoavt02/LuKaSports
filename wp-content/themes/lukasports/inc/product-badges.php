<?php
/**
 * Product badges ("thẻ"): an editorial label per product, chosen in the
 * product editor (General tab → "Thẻ sản phẩm") instead of the old
 * automatic "Mới" on everything published in the last 30 days.
 *
 * Shown on cards and the product gallery via lukasports_product_badge().
 * Without a manual tag, lukasports_auto_badges() assigns one by rule.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Badge meta key. */
const LUKASPORTS_BADGE_META = '_lukasports_badge';

/**
 * Available badges: key => [ label, css modifier ]. Add a line here (and a
 * .sk-badge--{modifier} rule in core.css) to introduce a new tag.
 */
function lukasports_badge_options() {
	return array(
		'ban-chay'  => array( __( 'Bán chạy', 'lukasports' ), 'hot' ),
		'yeu-thich' => array( __( 'Yêu thích', 'lukasports' ), 'fav' ),
		'noi-bat'   => array( __( 'Nổi bật', 'lukasports' ), 'star' ),
		'xu-huong'  => array( __( 'Xu hướng', 'lukasports' ), 'trend' ),
		'doc-quyen' => array( __( 'Độc quyền', 'lukasports' ), 'exclusive' ),
		'tron-bo'   => array( __( 'Trọn bộ', 'lukasports' ), 'set' ),
		'quan-tam'  => array( __( 'Quan tâm nhiều', 'lukasports' ), 'interest' ),
		'bst-moi'   => array( __( 'BST mới', 'lukasports' ), 'collection' ),
		'moi'       => array( __( 'Mới', 'lukasports' ), 'new' ),
	);
}

/**
 * Automatic badges, computed from real data and cached for an hour
 * (product id => badge key). Rules, in priority order:
 *   1. moi       — the 3 newest published products.
 *   2. quan-tam  — the 3 products with the most consultation requests
 *                  (lead form) in the last 30 days, at least 2 each.
 *   3. bst-moi   — products in the newest collection.
 *   4. tron-bo   — full sets (the name starts with "Bộ").
 * A tag picked by hand in the product editor always wins. Filter
 * `lukasports_auto_badges` to change the result.
 *
 * @return array<int,string>
 */
function lukasports_auto_badges() {
	$map = get_transient( 'lukasports_auto_badges' );
	if ( false !== $map ) {
		return $map;
	}
	$map = array();
	if ( function_exists( 'wc_get_products' ) ) {
		$products = wc_get_products(
			array(
				'status'  => 'publish',
				'limit'   => -1,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);

		// Lowest priority first, so higher rules overwrite.
		foreach ( $products as $product ) {
			if ( 0 === strpos( $product->get_name(), 'Bộ ' ) ) {
				$map[ $product->get_id() ] = 'tron-bo';
			}
		}

		$collection = function_exists( 'lukasports_newest_collection' ) ? lukasports_newest_collection() : null;
		if ( $collection ) {
			$in_collection = wc_get_products(
				array(
					'status'    => 'publish',
					'limit'     => -1,
					'return'    => 'ids',
					'tax_query' => array( array( 'taxonomy' => 'product_collection', 'field' => 'term_id', 'terms' => $collection->term_id ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
				)
			);
			foreach ( $in_collection as $id ) {
				$map[ $id ] = 'bst-moi';
			}
		}

		if ( function_exists( 'lukasports_core_leads_table' ) ) {
			global $wpdb;
			$table = lukasports_core_leads_table();
			$rows  = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->prepare(
					"SELECT product_id, COUNT(*) AS n FROM {$table} WHERE product_id IS NOT NULL AND created_at >= %s GROUP BY product_id HAVING n >= 2 ORDER BY n DESC LIMIT 3", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					gmdate( 'Y-m-d H:i:s', time() - 30 * DAY_IN_SECONDS )
				)
			);
			foreach ( (array) $rows as $row ) {
				if ( 'publish' === get_post_status( (int) $row->product_id ) ) {
					$map[ (int) $row->product_id ] = 'quan-tam';
				}
			}
		}

		foreach ( array_slice( $products, 0, 3 ) as $product ) {
			$map[ $product->get_id() ] = 'moi';
		}
	}
	$map = (array) apply_filters( 'lukasports_auto_badges', $map );
	set_transient( 'lukasports_auto_badges', $map, HOUR_IN_SECONDS );
	return $map;
}

/** Recompute when products change or a consultation request arrives. */
function lukasports_flush_auto_badges() {
	delete_transient( 'lukasports_auto_badges' );
}
add_action( 'woocommerce_update_product', 'lukasports_flush_auto_badges' );
add_action( 'woocommerce_new_product', 'lukasports_flush_auto_badges' );
add_action( 'lukasports_core_lead_created', 'lukasports_flush_auto_badges' );
add_action( 'set_object_terms', 'lukasports_flush_auto_badges' );

/** Editor field. */
add_action(
	'woocommerce_product_options_general_product_data',
	function () {
		$options = array( '' => __( 'Tự động (chỉ "Mới" cho mẫu mới nhất)', 'lukasports' ) );
		foreach ( lukasports_badge_options() as $key => $badge ) {
			$options[ $key ] = $badge[0];
		}
		woocommerce_wp_select(
			array(
				'id'          => LUKASPORTS_BADGE_META,
				'label'       => __( 'Thẻ sản phẩm', 'lukasports' ),
				'description' => __( 'Nhãn hiện ở góc ảnh sản phẩm trên web.', 'lukasports' ),
				'desc_tip'    => true,
				'options'     => $options,
			)
		);
	}
);

add_action(
	'woocommerce_process_product_meta',
	function ( $post_id ) {
		$key = isset( $_POST[ LUKASPORTS_BADGE_META ] ) ? sanitize_key( wp_unslash( $_POST[ LUKASPORTS_BADGE_META ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the nonce.
		if ( '' !== $key && isset( lukasports_badge_options()[ $key ] ) ) {
			update_post_meta( $post_id, LUKASPORTS_BADGE_META, $key );
		} else {
			delete_post_meta( $post_id, LUKASPORTS_BADGE_META );
		}
	}
);

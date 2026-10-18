<?php
/**
 * Product badges ("thẻ"): an editorial label per product, chosen in the
 * product editor (General tab → "Thẻ sản phẩm") instead of the old
 * automatic "Mới" on everything published in the last 30 days.
 *
 * Shown on cards and the product gallery via lukasports_product_badge().
 * "Mới" is automatic but only for the newest few products.
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
		'doc-quyen' => array( __( 'Độc quyền', 'lukasports' ), 'exclusive' ),
		'tron-bo'   => array( __( 'Trọn bộ', 'lukasports' ), 'set' ),
		'moi'       => array( __( 'Mới', 'lukasports' ), 'new' ),
	);
}

/** IDs of the newest published products that get the automatic "Mới". */
function lukasports_newest_product_ids() {
	$ids = get_transient( 'lukasports_newest_products' );
	if ( false === $ids ) {
		$ids = function_exists( 'wc_get_products' ) ? wc_get_products(
			array(
				'status'  => 'publish',
				'limit'   => 3,
				'orderby' => 'date',
				'order'   => 'DESC',
				'return'  => 'ids',
			)
		) : array();
		set_transient( 'lukasports_newest_products', $ids, HOUR_IN_SECONDS );
	}
	return $ids;
}
add_action(
	'woocommerce_update_product',
	function () {
		delete_transient( 'lukasports_newest_products' );
	}
);
add_action(
	'woocommerce_new_product',
	function () {
		delete_transient( 'lukasports_newest_products' );
	}
);

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

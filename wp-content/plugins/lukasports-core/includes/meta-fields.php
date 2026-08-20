<?php
/**
 * Product-level content that WooCommerce has no field for: the
 * checklist of benefits shown next to price, and whether a product
 * supports name/number printing (see #4 in the Phase 1 brief).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LUKASPORTS_CORE_META_BENEFITS', '_lukasports_benefits' );
define( 'LUKASPORTS_CORE_META_PRINTING', '_lukasports_printing_available' );
define( 'LUKASPORTS_CORE_META_PROMOTIONS', '_lukasports_promotions' );

function lukasports_core_register_product_metabox() {
	add_meta_box(
		'lukasports-product-details',
		__( 'LukaSports — Thông tin bổ sung', 'lukasports-core' ),
		'lukasports_core_render_product_metabox',
		'product',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'lukasports_core_register_product_metabox' );

function lukasports_core_render_product_metabox( $post ) {
	wp_nonce_field( 'lukasports_core_save_product_meta', 'lukasports_core_product_meta_nonce' );

	$benefits   = get_post_meta( $post->ID, LUKASPORTS_CORE_META_BENEFITS, true );
	$printing   = get_post_meta( $post->ID, LUKASPORTS_CORE_META_PRINTING, true );
	$promotions = get_post_meta( $post->ID, LUKASPORTS_CORE_META_PROMOTIONS, true );
	?>
	<p>
		<label for="lukasports_benefits"><strong><?php esc_html_e( 'Lợi ích sản phẩm (mỗi dòng một ý, hiển thị cạnh giá)', 'lukasports-core' ); ?></strong></label><br />
		<textarea id="lukasports_benefits" name="lukasports_benefits" rows="4" style="width:100%;"><?php echo esc_textarea( $benefits ); ?></textarea>
	</p>
	<p>
		<label for="lukasports_promotions"><strong><?php esc_html_e( 'Khuyến mãi (mỗi dòng một khuyến mãi, hiển thị trong khung riêng cạnh giá)', 'lukasports-core' ); ?></strong></label><br />
		<textarea id="lukasports_promotions" name="lukasports_promotions" rows="3" style="width:100%;" placeholder="<?php esc_attr_e( "Giảm 10% khi đặt trong tháng này\nMiễn phí thêu tên cho đơn từ 5 sản phẩm", 'lukasports-core' ); ?>"><?php echo esc_textarea( $promotions ); ?></textarea>
	</p>
	<p>
		<label>
			<input type="checkbox" name="lukasports_printing_available" value="1" <?php checked( $printing, '1' ); ?> />
			<?php esc_html_e( 'Sản phẩm này hỗ trợ thêu tên theo yêu cầu', 'lukasports-core' ); ?>
		</label>
	</p>
	<?php
}

function lukasports_core_save_product_meta( $post_id ) {
	if ( ! isset( $_POST['lukasports_core_product_meta_nonce'] ) ||
		! wp_verify_nonce( $_POST['lukasports_core_product_meta_nonce'], 'lukasports_core_save_product_meta' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_product', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['lukasports_benefits'] ) ) {
		update_post_meta( $post_id, LUKASPORTS_CORE_META_BENEFITS, sanitize_textarea_field( wp_unslash( $_POST['lukasports_benefits'] ) ) );
	}

	if ( isset( $_POST['lukasports_promotions'] ) ) {
		update_post_meta( $post_id, LUKASPORTS_CORE_META_PROMOTIONS, sanitize_textarea_field( wp_unslash( $_POST['lukasports_promotions'] ) ) );
	}

	update_post_meta( $post_id, LUKASPORTS_CORE_META_PRINTING, isset( $_POST['lukasports_printing_available'] ) ? '1' : '' );
}
add_action( 'save_post_product', 'lukasports_core_save_product_meta' );

/**
 * @return string[] Benefit lines, trimmed, empty lines removed.
 */
function lukasports_core_get_product_benefits( $product_id ) {
	$raw = get_post_meta( $product_id, LUKASPORTS_CORE_META_BENEFITS, true );

	if ( ! $raw ) {
		return array();
	}

	$lines = array_map( 'trim', explode( "\n", $raw ) );

	return array_values( array_filter( $lines ) );
}

function lukasports_core_product_supports_printing( $product_id ) {
	return '1' === get_post_meta( $product_id, LUKASPORTS_CORE_META_PRINTING, true );
}

/**
 * @return string[] Promotion lines, trimmed, empty lines removed.
 */
function lukasports_core_get_product_promotions( $product_id ) {
	$raw = get_post_meta( $product_id, LUKASPORTS_CORE_META_PROMOTIONS, true );

	if ( ! $raw ) {
		return array();
	}

	$lines = array_map( 'trim', explode( "\n", $raw ) );

	return array_values( array_filter( $lines ) );
}

<?php
/**
 * Override of WooCommerce's templates/single-product/meta.php.
 *
 * The only change from core: the SKU row is shown only when the
 * product actually has a SKU. Core also shows the row (with a literal
 * "N/A" / "Không áp dụng" label) for every variable product even when
 * it has no SKU at all, which reads as a data-error placeholder rather
 * than intentional "no SKU" design — we'd rather just not show the
 * row than show an empty-looking value.
 *
 * @version 9.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && $product->get_sku() ) : ?>

		<span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo esc_html( $product->get_sku() ); ?></span></span>

	<?php endif; ?>

	<?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

	<?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>

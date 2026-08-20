<?php
/**
 * Related products, rendered with the same div/grid markup as every
 * other product grid on the site (avoids nesting our card markup
 * inside WooCommerce's default <ul class="products"> list item model).
 *
 * @var WC_Product[] $related_products
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $related_products ) ) {
	return;
}
?>
<section class="sk-section sk-related-products">
	<div class="sk-section-head">
		<h2 class="sk-section-head__title"><?php echo esc_html( apply_filters( 'woocommerce_product_related_products_heading', __( 'Sản phẩm liên quan', 'lukasports' ) ) ); ?></h2>
	</div>
	<div class="sk-grid">
		<?php
		global $product;
		foreach ( $related_products as $related_product ) {
			$product = is_a( $related_product, 'WC_Product' ) ? $related_product : wc_get_product( $related_product );
			if ( $product ) {
				get_template_part( 'template-parts/product/product-card' );
			}
		}
		?>
	</div>
</section>
<?php
$product = wc_get_product( get_the_ID() );

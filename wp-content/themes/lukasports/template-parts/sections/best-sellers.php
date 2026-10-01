<?php
/**
 * Real WooCommerce query, latest products first — no curated/fake
 * "best seller" list here (that's the separate "DALETIC Selected"
 * section, sourced from the product_collection taxonomy instead).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wc_get_products' ) ) {
	return;
}

$products = wc_get_products(
	array(
		'status'  => 'publish',
		'limit'   => 8,
		'orderby' => 'date',
		'order'   => 'DESC',
	)
);

if ( empty( $products ) ) {
	return;
}

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' );
?>
<section class="sk-section sk-best-sellers">
	<div class="sk-container">
		<div class="sk-section-head">
			<h2 class="sk-section-head__title"><?php esc_html_e( 'Sản phẩm mới', 'lukasports' ); ?></h2>
			<a class="sk-section-head__link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Xem tất cả →', 'lukasports' ); ?></a>
		</div>
		<div class="sk-grid">
			<?php
			global $product;
			foreach ( $products as $product ) {
				get_template_part( 'template-parts/product/product-card' );
			}
			?>
		</div>
	</div>
</section>

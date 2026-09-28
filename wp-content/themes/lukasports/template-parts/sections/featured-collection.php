<?php
/**
 * Curated picks — sourced from the `product_collection` taxonomy
 * (admin-assignable per product under Products → Bộ sưu tập) rather
 * than a hardcoded product list, so it's real data an admin can
 * change without touching a template. Section hides itself if no
 * collection has been tagged yet.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wc_get_products' ) || ! taxonomy_exists( 'product_collection' ) ) {
	return;
}

$terms = get_terms(
	array(
		'taxonomy'   => 'product_collection',
		'hide_empty' => true,
		'number'     => 1,
	)
);

if ( empty( $terms ) || is_wp_error( $terms ) ) {
	return;
}

$term     = $terms[0];
$products = wc_get_products(
	array(
		'status'   => 'publish',
		'limit'    => 6,
		'tax_query' => array( // phpcs:ignore
			array(
				'taxonomy' => 'product_collection',
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			),
		),
	)
);

if ( empty( $products ) ) {
	return;
}
?>
<section class="sk-section sk-featured">
	<div class="sk-container">
		<div class="sk-section-head">
			<div>
				<h2 class="sk-section-head__title"><?php esc_html_e( 'LukaSports tuyển chọn', 'lukasports' ); ?></h2>
				<p class="sk-section-head__desc"><?php echo esc_html( lukasports_excerpt( $term->description ? $term->description : $term->name, 16 ) ); ?></p>
			</div>
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

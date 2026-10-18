<?php
/**
 * "DALETIC tuyển chọn / Mẫu bán chạy" — curated picks sourced from the
 * `product_collection` taxonomy (admin-assignable per product under
 * Products → Bộ sưu tập) rather than a hardcoded product list. Shows
 * the in-house "DALETIC.VN 2026" collection first, topped up from
 * "Đồng phục đội phong trào" to two full rows of 6. Falls back to the
 * first collection with products; hides itself if none is tagged.
 * The products shown are recorded so "Sản phẩm mới" doesn't repeat them.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wc_get_products' ) || ! taxonomy_exists( 'product_collection' ) ) {
	return;
}

$term = get_term_by( 'slug', apply_filters( 'lukasports_home_picks_collection', 'daletic-vn-2026' ), 'product_collection' );
if ( ! $term instanceof WP_Term || ! $term->count ) {
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
	$term = $terms[0];
}

$products = wc_get_products(
	array(
		'status'    => 'publish',
		'limit'     => 12,
		'orderby'   => 'date',
		'order'     => 'ASC',
		'tax_query' => array( // phpcs:ignore
			array(
				'taxonomy' => 'product_collection',
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			),
		),
	)
);

// Top up to a multiple of 6 (one desktop row) from the team collection.
$missing = ( 6 - count( $products ) % 6 ) % 6;
$extra   = $missing ? get_term_by( 'slug', 'doi-bong-phong-trao', 'product_collection' ) : null;
if ( $extra instanceof WP_Term && $extra->term_id !== $term->term_id ) {
	$products = array_merge(
		$products,
		wc_get_products(
			array(
				'status'    => 'publish',
				'limit'     => $missing,
				'exclude'   => array_map( fn( $p ) => $p->get_id(), $products ),
				'tax_query' => array( // phpcs:ignore
					array(
						'taxonomy' => 'product_collection',
						'field'    => 'term_id',
						'terms'    => $extra->term_id,
					),
				),
			)
		)
	);
}
$GLOBALS['lukasports_home_shown'] = array_map( fn( $p ) => $p->get_id(), $products );

if ( empty( $products ) ) {
	return;
}

$term_link = get_term_link( $term );
if ( is_wp_error( $term_link ) ) {
	$term_link = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' );
}
?>
<section class="sk-home-section sk-picks" aria-labelledby="sk-picks-title">
	<div class="sk-container">
		<div class="sk-section-head">
			<div>
				<p class="sk-section-head__eyebrow"><?php esc_html_e( 'DALETIC tuyển chọn', 'lukasports' ); ?></p>
				<h2 id="sk-picks-title" class="sk-section-head__title"><?php esc_html_e( 'Mẫu bán chạy', 'lukasports' ); ?></h2>
				<?php if ( $term->description ) : ?>
					<p class="sk-section-head__lead"><?php echo esc_html( lukasports_excerpt( $term->description, 30 ) ); ?></p>
				<?php endif; ?>
			</div>
			<a class="sk-section-head__link" href="<?php echo esc_url( $term_link ); ?>"><?php esc_html_e( 'Xem tất cả', 'lukasports' ); ?></a>
		</div>
		<div class="sk-home-grid sk-home-grid--6">
			<?php
			global $product;
			foreach ( $products as $product ) {
				get_template_part( 'template-parts/product/product-card' );
			}
			?>
		</div>
	</div>
</section>

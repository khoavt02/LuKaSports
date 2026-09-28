<?php
/**
 * Template Name: LukaSports — Category Landing
 *
 * WooCommerce won't let a product category archive live at the site
 * root (its permalink settings always fall back to a "product-category/"
 * base — see wc_get_permalink_structure()), but the approved IA calls
 * for clean top-level URLs like /ao-bong-da/. Rather than fight that
 * with rewrite-rule hacks, this is a plain WordPress Page assigned
 * this template, whose slug is used, by convention, to look up the
 * matching product_cat term and render its products. The real
 * WooCommerce taxonomy archive still exists underneath (redirected
 * here for canonicalization — see inc/hooks.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$page = get_post();
$term = get_term_by( 'slug', $page->post_name, 'product_cat' );
$paged = max( 1, (int) get_query_var( 'paged' ) );

$query_args = array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'paged'          => $paged,
	'posts_per_page' => wc_get_default_products_per_row() * wc_get_default_products_per_row(),
);
if ( $term instanceof WP_Term ) {
	$query_args['tax_query'] = array(
		array(
			'taxonomy' => 'product_cat',
			'field'    => 'term_id',
			'terms'    => $term->term_id,
		),
	);
}
$products_query = new WP_Query( $query_args );
?>
<section class="sk-section sk-shop">
	<div class="sk-container">
		<nav class="sk-breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Trang chủ', 'lukasports' ); ?></a> / <?php echo esc_html( $term ? $term->name : $page->post_title ); ?>
		</nav>

		<header class="sk-shop__header">
			<h1 class="sk-shop__title"><?php echo esc_html( $term ? $term->name : $page->post_title ); ?></h1>
			<?php if ( $term && $term->description ) : ?>
				<div class="sk-shop__intro"><?php echo wp_kses_post( wpautop( $term->description ) ); ?></div>
			<?php elseif ( get_the_content( null, false, $page ) ) : ?>
				<div class="sk-shop__intro"><?php echo apply_filters( 'the_content', $page->post_content ); // phpcs:ignore ?></div>
			<?php endif; ?>
		</header>

		<?php get_template_part( 'template-parts/shop/category-filter', null, array( 'active' => $term instanceof WP_Term ? $term->term_id : 0 ) ); ?>

		<?php if ( $products_query->have_posts() ) : ?>
			<div class="sk-grid">
				<?php
				global $product;
				while ( $products_query->have_posts() ) :
					$products_query->the_post();
					$product = wc_setup_product_data( get_the_ID() );
					get_template_part( 'template-parts/product/product-card' );
				endwhile;
				?>
			</div>
			<div class="sk-pagination">
				<?php
				echo paginate_links(
					array(
						'total'   => $products_query->max_num_pages,
						'current' => $paged,
					)
				);
				?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Danh mục này chưa có sản phẩm.', 'lukasports' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>

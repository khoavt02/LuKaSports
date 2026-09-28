<?php
/**
 * Product archive (shop page + product category/tag archives).
 * Sorting, result count and pagination reuse WooCommerce's own hooked
 * functions (styled to match our design in assets/css/woocommerce.css)
 * rather than being reimplemented — no reason to duplicate logic that
 * already handles orderby/pagination edge cases correctly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term = is_tax() || is_category() || is_tag() ? get_queried_object() : null;

// Highlight the top-level ancestor's chip when browsing a subcategory.
$active_cat = 0;
if ( $term instanceof WP_Term && 'product_cat' === $term->taxonomy ) {
	$ancestors  = get_ancestors( $term->term_id, 'product_cat', 'taxonomy' );
	$active_cat = $ancestors ? (int) end( $ancestors ) : $term->term_id;
}
?>

<section class="sk-section sk-shop">
	<div class="sk-container">
		<?php if ( function_exists( 'woocommerce_breadcrumb' ) ) : ?>
			<nav class="sk-breadcrumb"><?php woocommerce_breadcrumb( array( 'delimiter' => ' / ', 'wrap_before' => '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Đường dẫn', 'lukasports' ) . '">' ) ); ?></nav>
		<?php endif; ?>

		<header class="sk-shop__header">
			<h1 class="sk-shop__title"><?php woocommerce_page_title(); ?></h1>
			<?php if ( $term instanceof WP_Term && $term->description ) : ?>
				<div class="sk-shop__intro"><?php echo wp_kses_post( wpautop( $term->description ) ); ?></div>
			<?php endif; ?>
		</header>

		<?php get_template_part( 'template-parts/shop/category-filter', null, array( 'active' => $active_cat ) ); ?>

		<?php if ( woocommerce_product_loop() ) : ?>
			<div class="sk-shop__toolbar">
				<?php do_action( 'woocommerce_before_shop_loop' ); ?>
			</div>

			<div class="sk-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					wc_get_template_part( 'content', 'product' );
				endwhile;
				?>
			</div>

			<?php do_action( 'woocommerce_after_shop_loop' ); ?>
		<?php else : ?>
			<?php do_action( 'woocommerce_no_products_found' ); ?>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>

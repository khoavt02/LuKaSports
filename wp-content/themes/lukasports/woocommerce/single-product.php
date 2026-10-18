<?php
/**
 * Single product page shell: breadcrumb, then content-single-product.php,
 * which lays out its own containers (buy area, full-width tabs band,
 * "Sản phẩm tương tự").
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="sk-product-page">
	<?php if ( function_exists( 'woocommerce_breadcrumb' ) ) : ?>
		<div class="sk-container sk-product-page__crumbs">
			<div class="sk-breadcrumb">
				<?php
				woocommerce_breadcrumb(
					array(
						'delimiter'   => '<span class="sk-crumb-sep" aria-hidden="true">›</span>',
						'wrap_before' => '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Đường dẫn', 'lukasports' ) . '">',
						'before'      => '<span class="sk-crumb">',
						'after'       => '</span>',
					)
				);
				?>
			</div>
		</div>
	<?php endif; ?>

	<?php
	while ( have_posts() ) :
		the_post();
		wc_get_template_part( 'content', 'single-product' );
	endwhile;
	?>
</div>
<?php get_footer(); ?>

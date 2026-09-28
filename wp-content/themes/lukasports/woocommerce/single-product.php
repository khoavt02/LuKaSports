<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="sk-section sk-product-page">
	<div class="sk-container">
		<?php if ( function_exists( 'woocommerce_breadcrumb' ) ) : ?>
			<nav class="sk-breadcrumb"><?php woocommerce_breadcrumb( array( 'delimiter' => ' / ', 'wrap_before' => '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Đường dẫn', 'lukasports' ) . '">' ) ); ?></nav>
		<?php endif; ?>

		<?php
		while ( have_posts() ) :
			the_post();
			wc_get_template_part( 'content', 'single-product' );
		endwhile;
		?>
	</div>
</section>
<?php get_footer(); ?>

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="sk-section sk-search-results">
	<div class="sk-container">
		<header class="sk-blog-archive__header">
			<h1 class="sk-blog-archive__title">
				<?php
				printf(
					/* translators: %s: search query */
					esc_html__( 'Kết quả tìm kiếm cho: %s', 'lukasports' ),
					'<span>' . esc_html( get_search_query() ) . '</span>'
				);
				?>
			</h1>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="sk-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					if ( 'product' === get_post_type() ) {
						global $product;
						$product = wc_setup_product_data( get_the_ID() );
						get_template_part( 'template-parts/product/product-card' );
					} else {
						get_template_part( 'template-parts/blog/post-card' );
					}
				endwhile;
				?>
			</div>
			<div class="sk-pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<p><?php esc_html_e( 'Không tìm thấy kết quả phù hợp. Thử một từ khóa khác hoặc xem sản phẩm bán chạy dưới đây.', 'lukasports' ); ?></p>
			<?php get_template_part( 'template-parts/sections/best-sellers' ); ?>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>

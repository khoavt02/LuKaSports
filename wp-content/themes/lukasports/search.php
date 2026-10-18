<?php
/**
 * Search results: products use the product card, posts/pages the blog
 * card. Styles: blog.css (`.sk-search-results`).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

get_template_part(
	'template-parts/page-band',
	null,
	array(
		'title'  => __( 'Kết quả tìm kiếm', 'lukasports' ),
		'accent' => '“' . get_search_query() . '”',
		'intro'  => '<p>' . esc_html(
			sprintf(
				/* translators: %d: number of results */
				_n( '%d kết quả phù hợp.', '%d kết quả phù hợp.', (int) $GLOBALS['wp_query']->found_posts, 'lukasports' ),
				(int) $GLOBALS['wp_query']->found_posts
			)
		) . '</p>',
	)
);
?>
<section class="sk-search-results">
	<div class="sk-container">
		<form class="sk-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="sk-visually-hidden" for="sk-search-again"><?php esc_html_e( 'Tìm kiếm', 'lukasports' ); ?></label>
			<input type="search" class="sk-field" id="sk-search-again" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Tìm áo bóng đá, áo team, phụ kiện…', 'lukasports' ); ?>" />
			<button type="submit" class="sk-btn sk-btn--primary sk-btn--md"><?php esc_html_e( 'Tìm', 'lukasports' ); ?></button>
		</form>

		<?php if ( have_posts() ) : ?>
			<div class="sk-search-results__grid">
				<?php
				while ( have_posts() ) :
					the_post();
					if ( 'product' === get_post_type() && function_exists( 'wc_setup_product_data' ) ) {
						global $product;
						$product = wc_setup_product_data( get_the_ID() );
						get_template_part( 'template-parts/product/product-card' );
					} else {
						get_template_part( 'template-parts/blog/blog-card' );
					}
				endwhile;
				?>
			</div>
			<?php the_posts_pagination( array( 'class' => 'sk-pagination' ) ); ?>
		<?php else : ?>
			<p class="sk-search-results__empty"><?php esc_html_e( 'Không tìm thấy kết quả phù hợp. Thử một từ khóa khác hoặc xem sản phẩm mới dưới đây.', 'lukasports' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
if ( ! have_posts() ) {
	get_template_part( 'template-parts/sections/best-sellers' );
}

get_footer();

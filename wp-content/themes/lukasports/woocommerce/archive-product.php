<?php
/**
 * Product archive (shop page, product category, collection, product
 * search). Dark page band on top, then a two-column layout: filter
 * sidebar (template-parts/shop/sidebar-filters.php) and the toolbar +
 * product grid. Sorting, result count and pagination are WooCommerce's
 * own functions, styled in assets/css/woocommerce.css.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term = is_tax() || is_category() || is_tag() ? get_queried_object() : null;

// Highlight the top-level ancestor when browsing a subcategory.
$active_cat = 0;
if ( $term instanceof WP_Term && 'product_cat' === $term->taxonomy ) {
	$ancestors  = get_ancestors( $term->term_id, 'product_cat', 'taxonomy' );
	$active_cat = $ancestors ? (int) end( $ancestors ) : $term->term_id;
}

if ( is_search() ) {
	$band_title  = __( 'Kết quả tìm kiếm', 'lukasports' );
	$band_accent = '“' . get_search_query() . '”';
	$band_intro  = '';
} elseif ( $term instanceof WP_Term ) {
	$band_title  = $term->name;
	$band_accent = 'product_collection' === $term->taxonomy ? __( 'Bộ sưu tập', 'lukasports' ) : __( 'Thiết kế theo yêu cầu', 'lukasports' );
	$band_intro  = $term->description ? wp_kses_post( wpautop( $term->description ) ) : '';
} else {
	$band_title  = __( 'Tất cả sản phẩm', 'lukasports' );
	$band_accent = __( 'Áo đấu & đồng phục', 'lukasports' );
	$band_intro  = '<p>' . esc_html__( 'Tùy chỉnh màu sắc, họa tiết, logo, tên và số áo để tạo nên bộ đồng phục riêng cho đội bóng, câu lạc bộ hoặc doanh nghiệp của bạn.', 'lukasports' ) . '</p>';
}

// Category-specific picture when one is set (bin/apply-category-images.php).
$band_image = ( $term instanceof WP_Term && 'product_cat' === $term->taxonomy ) ? lukasports_category_image_id( $term, 'banner' ) : 0;
$band_media = $band_image ? wp_get_attachment_image_url( $band_image, 'large' ) : lukasports_image_url( 'hero-jerseys.webp' );

get_template_part(
	'template-parts/page-band',
	null,
	array(
		'title'    => $band_title,
		'accent'   => $band_accent,
		'intro'    => $band_intro,
		'media'    => $band_media,
		'features' => false,
	)
);
?>

<section class="sk-shop">
	<div class="sk-container sk-shop__layout">
		<?php get_template_part( 'template-parts/shop/sidebar-filters', null, array( 'active_cat' => $active_cat ) ); ?>
		<div class="sk-filters-backdrop" data-sk-filters-close hidden></div>

		<div class="sk-shop__main">
			<div class="sk-shop__bar">
				<?php
				// Category chips: phones only (a horizontal switcher above
				// "Bộ lọc" + sort), the desktop sidebar already lists them.
				get_template_part( 'template-parts/shop/category-filter', null, array( 'active' => $active_cat ) );
				get_template_part( 'template-parts/shop/toolbar' );
				?>
			</div>
			<?php woocommerce_output_all_notices(); ?>

			<?php if ( woocommerce_product_loop() ) : ?>
				<div class="sk-shop__meta"><?php woocommerce_result_count(); ?></div>
				<div class="sk-grid sk-grid--products" data-sk-product-grid>
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
	</div>
</section>

<?php get_footer(); ?>

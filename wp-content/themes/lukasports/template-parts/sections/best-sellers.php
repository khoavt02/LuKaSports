<?php
/**
 * "Sản phẩm mới" — real WooCommerce query, latest products first.
 *
 * Homepage passes array( 'layout' => 'promo' ): a dark "Vừa lên kệ"
 * promo card next to the newest product plus the 3 newest not already shown in "Mẫu bán chạy". Without args (search, 404)
 * it renders a plain section head + 8-up grid that only needs core.css.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wc_get_products' ) ) {
	return;
}

$is_promo = isset( $args['layout'] ) && 'promo' === $args['layout'];

$products = wc_get_products(
	array(
		'status'  => 'publish',
		'limit'   => $is_promo ? 4 : 8,
		'exclude' => $is_promo ? lukasports_home_new_exclusions() : array(),
		'orderby' => 'date',
		'order'   => 'DESC',
	)
);

if ( empty( $products ) ) {
	return;
}

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' );

global $product;

if ( $is_promo ) :
	?>
	<section class="sk-home-section sk-new" aria-labelledby="sk-new-title">
		<div class="sk-container sk-new__inner">
			<div class="sk-new__promo">
				<span class="sk-new__slab" aria-hidden="true"></span>
				<div class="sk-new__text">
					<p class="sk-new__eyebrow"><?php esc_html_e( 'Vừa lên kệ', 'lukasports' ); ?></p>
					<h2 id="sk-new-title" class="sk-new__title sk-display"><?php esc_html_e( 'Sản phẩm mới', 'lukasports' ); ?></h2>
					<p class="sk-new__lead"><?php esc_html_e( 'Những mẫu áo đấu và phụ kiện ra sân vừa được cập nhật — xem ngay trước khi lên đồ cho cả đội.', 'lukasports' ); ?></p>
				</div>
				<a class="sk-btn sk-btn--primary sk-btn--md sk-new__btn" href="<?php echo esc_url( add_query_arg( 'orderby', 'date', $shop_url ) ); ?>"><?php esc_html_e( 'Xem hàng mới', 'lukasports' ); ?></a>
			</div>
			<div class="sk-new__grid">
				<?php
				foreach ( $products as $product ) {
					get_template_part( 'template-parts/product/product-card' );
				}
				?>
			</div>
		</div>
	</section>
	<?php
	return;
endif;
?>
<section class="sk-section sk-best-sellers">
	<div class="sk-container">
		<div class="sk-section-head">
			<h2 class="sk-section-head__title"><?php esc_html_e( 'Sản phẩm mới', 'lukasports' ); ?></h2>
			<a class="sk-section-head__link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Xem tất cả', 'lukasports' ); ?></a>
		</div>
		<div class="sk-grid">
			<?php
			foreach ( $products as $product ) {
				get_template_part( 'template-parts/product/product-card' );
			}
			?>
		</div>
	</div>
</section>

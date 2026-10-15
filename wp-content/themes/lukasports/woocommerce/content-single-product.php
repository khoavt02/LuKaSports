<?php
/**
 * Single product: gallery (vertical thumbs) + buy box, then the detail
 * tabs next to a "Sản phẩm tương tự" column.
 *
 * Buy box order: category, title, intro, price + saving, rating, size /
 * color pickers (variable products), the consult CTA strip, trust row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$product = wc_setup_product_data( get_the_ID() );

if ( ! $product ) {
	return;
}

$category_terms = get_the_terms( $product->get_id(), 'product_cat' );
$category       = ( $category_terms && ! is_wp_error( $category_terms ) ) ? $category_terms[0] : null;
$saving         = lukasports_sale_badge_text( $product );
$rating         = (float) $product->get_average_rating();
$review_count   = (int) $product->get_review_count();

/**
 * We never fire woocommerce_single_product_summary, which is what
 * normally triggers WC_Structured_Data::generate_product_data(). Call it
 * directly so Product rich-result JSON-LD still ships (via wp_footer).
 */
if ( function_exists( 'WC' ) && isset( WC()->structured_data ) ) {
	WC()->structured_data->generate_product_data( $product );
}
?>
<div class="sk-product">
	<div class="sk-product__gallery">
		<?php get_template_part( 'template-parts/product/gallery' ); ?>
	</div>

	<div class="sk-product__info">
		<?php if ( $category ) : ?>
			<a class="sk-product__cat" href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
		<?php endif; ?>

		<h1 class="product_title sk-product__title sk-display"><?php the_title(); ?></h1>

		<?php if ( $product->get_short_description() ) : ?>
			<div class="sk-product__intro"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
		<?php endif; ?>

		<div class="sk-product__price-row">
			<div class="sk-product__price"><?php woocommerce_template_single_price(); ?></div>
			<?php if ( '' !== $saving ) : ?>
				<span class="sk-product__saving"><?php echo esc_html( sprintf( /* translators: %s: e.g. 12% */ __( 'Tiết kiệm %s', 'lukasports' ), ltrim( $saving, '-' ) ) ); ?></span>
			<?php endif; ?>
		</div>

		<?php if ( $review_count > 0 ) : ?>
			<a class="sk-rating sk-product__rating" href="#tab-reviews" data-sk-open-tab="reviews">
				<span class="sk-rating__stars" style="--rating: <?php echo esc_attr( $rating ); ?>" aria-hidden="true"></span>
				<span class="sk-rating__value"><?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></span>
				<span class="sk-rating__count">(<?php echo esc_html( sprintf( /* translators: %d: reviews */ _n( '%d đánh giá', '%d đánh giá', $review_count, 'lukasports' ), $review_count ) ); ?>)</span>
			</a>
		<?php endif; ?>

		<div class="sk-product__order-form">
			<a class="sk-product__size-link" href="#tab-size_guide" data-sk-open-tab="size_guide">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 15.5L15.5 3 21 8.5 8.5 21z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M7 12.5l1.8 1.8M10 9.5l1.8 1.8M13 6.5l1.8 1.8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
				<?php esc_html_e( 'Bảng size', 'lukasports' ); ?>
			</a>
			<?php
			// Consultation-led store: variable products keep the size/color
			// pickers (WooCommerce's variation form — its quantity and cart
			// buttons are hidden in CSS) so the chosen options can travel
			// into the consult form; simple products need no form at all.
			if ( $product->is_type( 'variable' ) ) {
				woocommerce_template_single_add_to_cart();
			}
			?>
		</div>

		<?php get_template_part( 'template-parts/product/product-cta' ); ?>

		<ul class="sk-product__trust">
			<li>
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 6.5h11v9h-11zM13.5 9.5h4l3 3v3h-7z"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/></svg>
				<span><strong><?php esc_html_e( 'Giao hàng toàn quốc', 'lukasports' ); ?></strong><?php esc_html_e( '2–5 ngày làm việc', 'lukasports' ); ?></span>
			</li>
			<li>
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 4.4-3 8.3-7 10-4-1.7-7-5.6-7-10V6l7-3z"/><path d="M8.5 12l2.4 2.4L15.5 9.8"/></svg>
				<span><strong><?php esc_html_e( 'Đổi trả trong 7 ngày', 'lukasports' ); ?></strong><?php esc_html_e( 'nếu lỗi sản xuất', 'lukasports' ); ?></span>
			</li>
			<li>
				<button type="button" class="sk-product__trust-btn" data-lukasports-cta="consult" data-source="product_page" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" data-product-name="<?php echo esc_attr( $product->get_name() ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 13v-1a8 8 0 0116 0v1"/><rect x="3" y="13" width="4" height="6" rx="1.5"/><rect x="17" y="13" width="4" height="6" rx="1.5"/><path d="M19 19c0 1.5-1.5 2.5-4 2.5h-2"/></svg>
					<span><strong><?php esc_html_e( 'Tư vấn thiết kế miễn phí', 'lukasports' ); ?></strong><?php esc_html_e( 'cho đội bóng, CLB', 'lukasports' ); ?></span>
				</button>
			</li>
		</ul>

	</div>
</div>

<div class="sk-product-details">
	<div class="sk-product-details__tabs">
		<?php woocommerce_output_product_data_tabs(); ?>
	</div>
	<?php
	$similar = wc_get_related_products( $product->get_id(), 4 );
	if ( $similar ) :
		?>
		<aside class="sk-similar" aria-labelledby="sk-similar-title">
			<div class="sk-similar__head">
				<h2 class="sk-similar__title" id="sk-similar-title"><?php esc_html_e( 'Sản phẩm tương tự', 'lukasports' ); ?></h2>
				<?php if ( $category ) : ?>
					<a class="sk-similar__all" href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php esc_html_e( 'Xem tất cả →', 'lukasports' ); ?></a>
				<?php endif; ?>
			</div>
			<ul class="sk-similar__list">
				<?php
				foreach ( $similar as $similar_id ) :
					$item = wc_get_product( $similar_id );
					if ( ! $item || ! $item->is_visible() ) {
						continue;
					}
					$item_saving = lukasports_sale_badge_text( $item );
					?>
					<li>
						<a class="sk-similar__item" href="<?php echo esc_url( $item->get_permalink() ); ?>">
							<span class="sk-similar__media">
								<?php echo $item->get_image_id() ? lukasports_attachment_image( $item->get_image_id(), 'lukasports-thumb', 'lazy', '', $item->get_name() ) : ''; // phpcs:ignore ?>
								<?php if ( '' !== $item_saving ) : ?>
									<span class="sk-badge sk-badge--sale"><?php echo esc_html( $item_saving ); ?></span>
								<?php endif; ?>
							</span>
							<span class="sk-similar__body">
								<span class="sk-similar__name"><?php echo esc_html( $item->get_name() ); ?></span>
								<span class="sk-price">
									<span class="sk-price__current"><?php echo lukasports_format_price( $item->get_price() ); // phpcs:ignore ?></span>
									<?php if ( $item->is_on_sale() ) : ?>
										<span class="sk-price__original"><?php echo lukasports_format_price( lukasports_get_regular_price_for_display( $item ) ); // phpcs:ignore ?></span>
									<?php endif; ?>
								</span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</aside>
	<?php endif; ?>
</div>

<script>
(window.lukasportsEventQueue = window.lukasportsEventQueue || []).push( [
	'view_product',
	<?php
	echo wp_json_encode(
		array(
			'product_id' => $product->get_id(),
			'name'       => $product->get_name(),
			'price'      => $product->get_price(),
		)
	);
	?>
] );
</script>

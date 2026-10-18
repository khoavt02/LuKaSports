<?php
/**
 * Single product: gallery (vertical thumbs) + buy box, then the detail
 * tabs on a full-width white band and a "Sản phẩm tương tự" card grid.
 *
 * Buy box order: category, title, intro, price + saving, rating, color /
 * size pickers (variable products), the consult CTA (in place of the
 * design's quantity + cart buttons — consult-only store), the "Đặt áo
 * cho cả đội?" box, trust row.
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
<div class="sk-container">
<div class="sk-product">
	<div class="sk-product__gallery">
		<?php get_template_part( 'template-parts/product/gallery' ); ?>
	</div>

	<div class="sk-product__info">
		<div class="sk-product__head">
			<?php if ( $category ) : ?>
				<a class="sk-product__cat" href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
			<?php endif; ?>

			<h1 class="product_title sk-product__title"><?php the_title(); ?></h1>

			<?php if ( $review_count > 0 ) : ?>
				<?php // Phones show the rating under the title (design), desktop under the price. ?>
				<a class="sk-rating sk-product__rating sk-product__rating--top" href="#tab-reviews" data-sk-open-tab="reviews">
					<span class="sk-rating__stars" style="--rating: <?php echo esc_attr( $rating ); ?>" aria-hidden="true"></span>
					<span class="sk-rating__value"><?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></span>
					<span class="sk-rating__count">(<?php echo esc_html( sprintf( /* translators: %d: reviews */ _n( '%d đánh giá', '%d đánh giá', $review_count, 'lukasports' ), $review_count ) ); ?>)</span>
				</a>
			<?php endif; ?>

			<?php if ( $product->get_short_description() ) : ?>
				<div class="sk-product__intro"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
			<?php endif; ?>
		</div>

		<div class="sk-product__price-row">
			<div class="sk-product__price"><?php woocommerce_template_single_price(); ?></div>
			<?php if ( '' !== $saving ) : ?>
				<span class="sk-product__saving"><span><?php echo esc_html( sprintf( /* translators: %s: e.g. 12% */ __( 'Tiết kiệm %s', 'lukasports' ), ltrim( $saving, '-' ) ) ); ?></span></span>
			<?php endif; ?>
		</div>

		<?php if ( $review_count > 0 ) : ?>
			<a class="sk-rating sk-product__rating sk-product__rating--bottom" href="#tab-reviews" data-sk-open-tab="reviews">
				<span class="sk-rating__stars" style="--rating: <?php echo esc_attr( $rating ); ?>" aria-hidden="true"></span>
				<span class="sk-rating__value"><?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></span>
				<span class="sk-rating__count">(<?php echo esc_html( sprintf( /* translators: %d: reviews */ _n( '%d đánh giá', '%d đánh giá', $review_count, 'lukasports' ), $review_count ) ); ?>)</span>
			</a>
		<?php endif; ?>

		<div class="sk-product__order-form<?php echo $product->is_type( 'variable' ) ? '' : ' sk-product__order-form--simple'; ?>">
			<?php // product-variations.js moves this link into the size row when there is one. ?>
			<?php if ( ! lukasports_is_font_product( $product ) ) : ?>
			<a class="sk-product__size-link" href="#tab-size_guide" data-sk-open-tab="size_guide">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 16L16 3l5 5L8 21z"/><path d="M7 12l2 2M10 9l2 2M13 6l2 2"/></svg>
				<?php esc_html_e( 'Bảng size', 'lukasports' ); ?>
			</a>
			<?php endif; ?>
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
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 6h11v10H2z"/><path d="M13 9h4l4 4v3h-8"/><circle cx="6.5" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/></svg>
				<span><strong><?php esc_html_e( 'Giao hàng toàn quốc', 'lukasports' ); ?></strong> <span class="sk-product__trust-sub"><?php esc_html_e( '2–5 ngày', 'lukasports' ); ?></span></span>
			</li>
			<li>
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l8 3v6c0 4.5-3.4 8.3-8 9-4.6-.7-8-4.5-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/></svg>
				<span><strong><?php esc_html_e( 'Đổi trả trong 7 ngày', 'lukasports' ); ?></strong> <span class="sk-product__trust-sub"><?php esc_html_e( 'Nếu lỗi sản xuất', 'lukasports' ); ?></span></span>
			</li>
			<li>
				<button type="button" class="sk-product__trust-btn" data-lukasports-cta="consult" data-source="product_page" data-sk-consult-product data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" data-product-name="<?php echo esc_attr( $product->get_name() ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 14v-2a8 8 0 0 1 16 0v2"/><rect x="3" y="14" width="4" height="6" rx="1.5"/><rect x="17" y="14" width="4" height="6" rx="1.5"/></svg>
					<span><strong><?php esc_html_e( 'Tư vấn thiết kế miễn phí', 'lukasports' ); ?></strong> <span class="sk-product__trust-sub"><?php esc_html_e( 'Cho đội bóng', 'lukasports' ); ?></span></span>
				</button>
			</li>
		</ul>
	</div>
</div>
</div>

<section class="sk-product-details" aria-label="<?php esc_attr_e( 'Thông tin chi tiết', 'lukasports' ); ?>">
	<div class="sk-container">
		<?php woocommerce_output_product_data_tabs(); ?>
	</div>
</section>

<?php
$similar_ids = wc_get_related_products( $product->get_id(), 4 );
if ( $similar_ids ) :
	$main_product = $product;
	?>
	<section class="sk-similar" aria-labelledby="sk-similar-title">
		<div class="sk-container">
			<div class="sk-similar__head">
				<h2 class="sk-similar__title" id="sk-similar-title"><?php esc_html_e( 'Sản phẩm tương tự', 'lukasports' ); ?></h2>
				<a class="sk-similar__all" href="<?php echo esc_url( $category ? get_term_link( $category ) : wc_get_page_permalink( 'shop' ) ); ?>">
					<span class="sk-similar__all-long"><?php esc_html_e( 'Xem tất cả', 'lukasports' ); ?></span><span class="sk-similar__all-short"><?php esc_html_e( 'Tất cả', 'lukasports' ); ?></span>
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
				</a>
			</div>
			<div class="sk-grid sk-grid--products sk-similar__grid">
				<?php
				// The card reads the global $product. wc_setup_product_data()
				// above unset/re-created that global, so the local $product
				// is no longer bound to it — write through $GLOBALS.
				foreach ( $similar_ids as $similar_id ) {
					$similar_product = wc_get_product( $similar_id );
					if ( $similar_product && $similar_product->is_visible() ) {
						$GLOBALS['product'] = $similar_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						get_template_part( 'template-parts/product/product-card' );
					}
				}
				$GLOBALS['product'] = $main_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

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

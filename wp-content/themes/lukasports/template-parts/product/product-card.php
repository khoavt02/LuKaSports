<?php
/**
 * Reusable product card (shop/category grids, homepage sections,
 * related products). Expects the global $product to be set.
 *
 * Badge (sale % / Bán chạy / Mới), wishlist heart (saved in the
 * visitor's browser — assets/js/shop.js), available color dots, rating,
 * price and a "Xem chi tiết" bar. A "Thiết kế theo yêu cầu" button under
 * the heart opens the consultation form pre-filled with this product.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
	return;
}

$permalink    = $product->get_permalink();
$name         = $product->get_name();
$thumbnail_id = $product->get_image_id();
$gallery_ids  = $product->get_gallery_image_ids();
$alt_image_id = ! empty( $gallery_ids ) ? $gallery_ids[0] : 0;
$badge        = lukasports_product_badge( $product );
$colors       = array_filter( array_map( 'trim', explode( ',', (string) $product->get_attribute( 'pa_color' ) ) ) );
$rating       = (float) $product->get_average_rating();
$review_count = (int) $product->get_review_count();
$regular      = lukasports_get_regular_price_for_display( $product );
$excerpt      = $product->get_short_description();
?>
<article class="sk-card sk-product-card">
	<div class="sk-product-card__media">
		<a class="sk-product-card__image-link" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
			<?php
			if ( $thumbnail_id ) {
				echo lukasports_attachment_image( $thumbnail_id, 'woocommerce_thumbnail', 'lazy', 'sk-product-card__img--main', $name ); // phpcs:ignore
			} else {
				echo '<img class="sk-product-card__img--main" src="' . esc_url( lukasports_image_url( 'placeholder-product.svg' ) ) . '" alt="" loading="lazy" />';
			}
			if ( $alt_image_id ) {
				echo lukasports_attachment_image( $alt_image_id, 'woocommerce_thumbnail', 'lazy', 'sk-product-card__img--alt', $name ); // phpcs:ignore
			}
			?>
		</a>
		<?php if ( $badge ) : ?>
			<span class="sk-badge sk-badge--<?php echo esc_attr( $badge[1] ); ?> sk-product-card__badge"><?php echo esc_html( $badge[0] ); ?></span>
		<?php endif; ?>
		<button type="button" class="sk-wish" data-sk-wish="<?php echo esc_attr( $product->get_id() ); ?>" aria-pressed="false">
			<span class="sk-visually-hidden"><?php echo esc_html( sprintf( /* translators: %s: product name */ __( 'Yêu thích %s', 'lukasports' ), $name ) ); ?></span>
			<svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20.3l-1.3-1.2C5.9 14.7 3 12 3 8.6 3 5.9 5.1 3.8 7.8 3.8c1.5 0 3 .7 4.2 1.9 1.2-1.2 2.7-1.9 4.2-1.9 2.7 0 4.8 2.1 4.8 4.8 0 3.4-2.9 6.1-7.7 10.5L12 20.3z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
		</button>
		<button
			type="button"
			class="sk-product-card__design"
			data-lukasports-cta="consult"
			data-source="product_card"
			data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
			data-product-name="<?php echo esc_attr( $name ); ?>"
			title="<?php esc_attr_e( 'Thiết kế theo yêu cầu', 'lukasports' ); ?>"
		>
			<span class="sk-visually-hidden"><?php esc_html_e( 'Thiết kế theo yêu cầu', 'lukasports' ); ?></span>
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20l1-4L16.5 4.5a2.1 2.1 0 013 3L8 19l-4 1z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M14.5 6.5l3 3" stroke="currentColor" stroke-width="1.8"/></svg>
		</button>
	</div>

	<div class="sk-product-card__body">
		<?php if ( $colors ) : ?>
			<ul class="sk-product-card__colors" aria-label="<?php esc_attr_e( 'Màu có sẵn', 'lukasports' ); ?>">
				<?php foreach ( array_slice( $colors, 0, 5 ) as $color ) : ?>
					<li><span class="sk-color-dot" style="--dot: <?php echo esc_attr( lukasports_color_hex( $color ) ); ?>" title="<?php echo esc_attr( $color ); ?>"></span></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<h3 class="sk-product-card__title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $name ); ?></a>
		</h3>

		<?php if ( $review_count > 0 ) : ?>
			<div class="sk-rating" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: rating 2: count */ __( 'Đánh giá %1$s/5 từ %2$d khách', 'lukasports' ), number_format_i18n( $rating, 1 ), $review_count ) ); ?>">
				<span class="sk-rating__stars" style="--rating: <?php echo esc_attr( $rating ); ?>" aria-hidden="true"></span>
				<span class="sk-rating__value"><?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></span>
				<span class="sk-rating__count">(<?php echo esc_html( sprintf( /* translators: %d: reviews */ _n( '%d đánh giá', '%d đánh giá', $review_count, 'lukasports' ), $review_count ) ); ?>)</span>
			</div>
		<?php endif; ?>

		<?php if ( $excerpt ) : ?>
			<p class="sk-product-card__excerpt"><?php echo esc_html( lukasports_excerpt( $excerpt, 22 ) ); ?></p>
		<?php endif; ?>

		<div class="sk-price sk-product-card__price">
			<span class="sk-price__current"><?php echo lukasports_format_price( $product->get_price() ); // phpcs:ignore ?></span>
			<?php if ( $product->is_on_sale() && $regular > 0 ) : ?>
				<span class="sk-price__original"><?php echo lukasports_format_price( $regular ); // phpcs:ignore ?></span>
			<?php endif; ?>
		</div>

		<a class="sk-product-card__cta" href="<?php echo esc_url( $permalink ); ?>">
			<span class="sk-product-card__cta-label"><?php esc_html_e( 'Xem chi tiết', 'lukasports' ); ?></span>
			<span class="sk-product-card__cta-arrow" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
		</a>
	</div>
</article>

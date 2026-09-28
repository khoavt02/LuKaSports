<?php
/**
 * Reusable product card. Expects the global WooCommerce loop to be
 * set up ($product available) — used by best-sellers, category
 * archives, and related products so every grid looks identical.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
	return;
}

$permalink     = $product->get_permalink();
$categories    = wc_get_product_category_list( $product->get_id(), ', ' );
$categories    = wp_strip_all_tags( $categories );
$thumbnail_id  = $product->get_image_id();
$gallery_ids   = $product->get_gallery_image_ids();
$alt_image_id  = ! empty( $gallery_ids ) ? $gallery_ids[0] : 0;
?>
<div class="sk-card sk-product-card">
	<a class="sk-product-card__media" href="<?php echo esc_url( $permalink ); ?>">
		<?php if ( $product->is_on_sale() ) : ?>
			<span class="sk-badge sk-badge--sale sk-product-card__badge"><?php echo esc_html( lukasports_sale_badge_text( $product ) ); ?></span>
		<?php elseif ( $product->is_featured() ) : ?>
			<span class="sk-badge sk-badge--featured sk-product-card__badge"><?php esc_html_e( 'Nổi bật', 'lukasports' ); ?></span>
		<?php endif; ?>
		<?php
		if ( $thumbnail_id ) {
			echo lukasports_attachment_image( $thumbnail_id, 'lukasports-card', 'lazy', 'sk-product-card__img--main', $product->get_name() ); // phpcs:ignore
		} else {
			echo '<img class="sk-product-card__img--main" src="' . esc_url( LUKASPORTS_THEME_URI . '/assets/images/placeholder-product.svg' ) . '" alt="" loading="lazy" />';
		}
		if ( $alt_image_id ) {
			echo lukasports_attachment_image( $alt_image_id, 'lukasports-card', 'lazy', 'sk-product-card__img--alt', $product->get_name() ); // phpcs:ignore
		}
		?>
		<span class="sk-product-card__quickview"><?php esc_html_e( 'Xem nhanh', 'lukasports' ); ?></span>
	</a>
	<div class="sk-product-card__body">
		<?php if ( $categories ) : ?>
			<p class="sk-product-card__cat"><?php echo esc_html( $categories ); ?></p>
		<?php endif; ?>
		<h3 class="sk-product-card__title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
		</h3>
		<?php $sk_card_regular_price = lukasports_get_regular_price_for_display( $product ); ?>
		<div class="sk-price sk-product-card__price">
			<span class="sk-price__current"><?php echo lukasports_format_price( $product->get_price() ); // phpcs:ignore ?></span>
			<?php if ( $product->is_on_sale() && $sk_card_regular_price > 0 ) : ?>
				<span class="sk-price__original"><?php echo lukasports_format_price( $sk_card_regular_price ); // phpcs:ignore ?></span>
			<?php endif; ?>
		</div>
		<a class="sk-product-card__cta" href="<?php echo esc_url( $permalink ); ?>">
			<?php esc_html_e( 'Xem chi tiết', 'lukasports' ); ?> <span aria-hidden="true">→</span>
		</a>
	</div>
</div>

<?php
/**
 * Product gallery: scroll-snap main strip (swipes natively on touch),
 * vertical thumbnails on desktop / horizontal under the image on
 * phones, prev/next arrows and the sale badge. Thumbnail clicks and
 * arrows are wired in assets/js/product-page.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

$image_ids = array_values( array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) ) );
if ( empty( $image_ids ) ) {
	$image_ids = array( 0 );
}
$badge = lukasports_product_badge( $product );
$many  = count( $image_ids ) > 1;
?>
<div class="sk-gallery<?php echo $many ? ' sk-gallery--many' : ''; ?>" data-sk-gallery>
	<?php if ( $many ) : ?>
		<div class="sk-gallery__thumbs" role="tablist" aria-label="<?php esc_attr_e( 'Ảnh sản phẩm', 'lukasports' ); ?>">
			<?php foreach ( $image_ids as $index => $image_id ) : ?>
				<button type="button" class="sk-gallery__thumb<?php echo 0 === $index ? ' is-active' : ''; ?>" data-sk-gallery-target="sk-gallery-slide-<?php echo esc_attr( $index ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: image number */ __( 'Ảnh %d', 'lukasports' ), $index + 1 ) ); ?>">
					<?php if ( $image_id ) : ?>
						<?php echo lukasports_attachment_image( $image_id, 'lukasports-thumb', 'lazy', '', '' ); // phpcs:ignore ?>
					<?php else : ?>
						<img src="<?php echo esc_url( lukasports_image_url( 'placeholder-product.svg' ) ); ?>" alt="" loading="lazy" />
					<?php endif; ?>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="sk-gallery__stage">
		<div class="sk-gallery__main" id="sk-gallery-main">
			<?php foreach ( $image_ids as $index => $image_id ) : ?>
				<div class="sk-gallery__slide" id="sk-gallery-slide-<?php echo esc_attr( $index ); ?>">
					<?php
					if ( $image_id ) {
						echo lukasports_attachment_image( $image_id, 'lukasports-gallery', 0 === $index ? 'eager' : 'lazy', '', $product->get_name() ); // phpcs:ignore
					} else {
						echo '<img src="' . esc_url( lukasports_image_url( 'placeholder-product.svg' ) ) . '" alt="" />';
					}
					?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( $badge ) : ?>
			<span class="sk-badge sk-badge--<?php echo esc_attr( $badge[1] ); ?> sk-gallery__badge"><?php echo esc_html( $badge[0] ); ?></span>
		<?php endif; ?>
		<?php if ( $many ) : ?>
			<div class="sk-gallery__nav">
				<button type="button" class="sk-gallery__arrow" data-sk-gallery-step="-1">
					<span class="sk-visually-hidden"><?php esc_html_e( 'Ảnh trước', 'lukasports' ); ?></span>
					<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M12.5 4.5L7 10l5.5 5.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<button type="button" class="sk-gallery__arrow" data-sk-gallery-step="1">
					<span class="sk-visually-hidden"><?php esc_html_e( 'Ảnh sau', 'lukasports' ); ?></span>
					<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M7.5 4.5L13 10l-5.5 5.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
			</div>
		<?php endif; ?>
	</div>
</div>

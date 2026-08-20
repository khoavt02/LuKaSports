<?php
/**
 * Custom scroll-snap gallery — swipes natively on touch, no JS needed
 * for mobile. Thumbnail clicks scroll the main strip into view.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

$image_ids = array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) );

if ( empty( $image_ids ) ) {
	$image_ids = array( 0 );
}
?>
<div class="sk-gallery">
	<div class="sk-gallery__main" id="sk-gallery-main">
		<?php foreach ( $image_ids as $index => $image_id ) : ?>
			<div class="sk-gallery__slide" id="sk-gallery-slide-<?php echo esc_attr( $index ); ?>">
				<?php
				if ( $image_id ) {
					echo lukasports_attachment_image( $image_id, 'lukasports-gallery', 0 === $index ? 'eager' : 'lazy', '', $product->get_name() ); // phpcs:ignore
				} else {
					echo '<img src="' . esc_url( LUKASPORTS_THEME_URI . '/assets/images/placeholder-product.svg' ) . '" alt="" />';
				}
				?>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( count( $image_ids ) > 1 ) : ?>
		<div class="sk-gallery__thumbs">
			<?php foreach ( $image_ids as $index => $image_id ) : ?>
				<button type="button" class="sk-gallery__thumb" data-sk-gallery-target="sk-gallery-slide-<?php echo esc_attr( $index ); ?>">
					<?php if ( $image_id ) : ?>
						<?php echo lukasports_attachment_image( $image_id, 'lukasports-thumb', 'lazy', '', $product->get_name() ); // phpcs:ignore ?>
					<?php else : ?>
						<img src="<?php echo esc_url( LUKASPORTS_THEME_URI . '/assets/images/placeholder-product.svg' ); ?>" alt="" loading="lazy" />
					<?php endif; ?>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>

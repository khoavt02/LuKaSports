<?php
/**
 * Single product layout — deliberately a different information order
 * than the shared SportKit/Linea ancestor template: category chip →
 * title/price → promo tags → variant picker + add-to-cart happen
 * together as one action group, then a full-bleed navy contact strip
 * (not a bordered card) carries the consult CTA, then benefit tiles.
 * The consult channel still outranks the cart visually (dark, full-
 * width, sits right under the fold) even though it now follows the
 * variant picker instead of preceding it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$product = wc_setup_product_data( get_the_ID() );

if ( ! $product ) {
	return;
}

$categories = wc_get_product_category_list( $product->get_id(), ', ' );
$benefits   = function_exists( 'lukasports_core_get_product_benefits' ) ? lukasports_core_get_product_benefits( $product->get_id() ) : array();
$printing   = function_exists( 'lukasports_core_product_supports_printing' ) && lukasports_core_product_supports_printing( $product->get_id() );
$promotions = function_exists( 'lukasports_core_get_product_promotions' ) ? lukasports_core_get_product_promotions( $product->get_id() ) : array();

/**
 * We never fire woocommerce_single_product_summary (see file docblock),
 * which is what normally triggers WC_Structured_Data::generate_product_data().
 * Call it directly so Product rich-result JSON-LD still ships — it's
 * still output the normal way, via wp_footer.
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
		<?php if ( $categories ) : ?>
			<span class="sk-product__cat"><?php echo wp_kses_post( $categories ); ?></span>
		<?php endif; ?>

		<?php woocommerce_template_single_title(); ?>
		<?php woocommerce_template_single_rating(); ?>

		<div class="sk-product__price">
			<?php woocommerce_template_single_price(); ?>
		</div>

		<?php if ( ! empty( $promotions ) ) : ?>
			<ul class="sk-product__tags">
				<?php foreach ( $promotions as $promo ) : ?>
					<li class="sk-tag"><?php echo esc_html( $promo ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php woocommerce_template_single_excerpt(); ?>

		<div class="sk-product__order-form">
			<?php woocommerce_template_single_add_to_cart(); ?>
		</div>

		<?php get_template_part( 'template-parts/product/product-cta' ); ?>

		<?php if ( ! empty( $benefits ) || $printing ) : ?>
			<div class="sk-product__features">
				<?php
				foreach ( $benefits as $benefit ) :
					?>
					<div class="sk-feature">
						<svg class="sk-feature__icon" width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10.5l4 4 8-9" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<span><?php echo esc_html( $benefit ); ?></span>
					</div>
				<?php endforeach; ?>
				<?php if ( $printing ) : ?>
					<div class="sk-feature">
						<svg class="sk-feature__icon" width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10.5l4 4 8-9" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<span><?php esc_html_e( 'Hỗ trợ thêu tên theo yêu cầu', 'lukasports' ); ?></span>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php woocommerce_template_single_meta(); ?>
	</div>
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

<?php
/**
 * Fires, in order: product data tabs (description/specs/size guide),
 * then related products. Upsells are unhooked in inc/hooks.php —
 * recommendation logic is out of scope for Phase 1.
 */
do_action( 'woocommerce_after_single_product_summary' );
?>

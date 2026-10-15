<?php
/**
 * Product data as horizontal tabs (Thông tin sản phẩm / Chất liệu /
 * Bảng size / Đánh giá), from the standard `woocommerce_product_tabs`
 * filter (titles and callbacks set in inc/hooks.php).
 *
 * Without JS every panel is visible, stacked with its heading; with JS
 * (assets/js/product-page.js) only the selected panel shows.
 */

defined( 'ABSPATH' ) || exit;

$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( empty( $product_tabs ) ) {
	return;
}

$first = array_key_first( $product_tabs );
?>
<div class="sk-tabs woocommerce-tabs" data-sk-tabs>
	<div class="sk-tabs__list" role="tablist" aria-label="<?php esc_attr_e( 'Thông tin sản phẩm', 'lukasports' ); ?>">
		<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
			<button
				type="button"
				class="sk-tabs__tab"
				role="tab"
				id="tab-title-<?php echo esc_attr( $key ); ?>"
				aria-controls="tab-<?php echo esc_attr( $key ); ?>"
				aria-selected="<?php echo $key === $first ? 'true' : 'false'; ?>"
				tabindex="<?php echo $key === $first ? '0' : '-1'; ?>"
				data-tab="<?php echo esc_attr( $key ); ?>"
			>
				<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
			</button>
		<?php endforeach; ?>
	</div>

	<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
		<div
			class="sk-tabs__panel woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?>"
			role="tabpanel"
			id="tab-<?php echo esc_attr( $key ); ?>"
			aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>"
			data-tab="<?php echo esc_attr( $key ); ?>"
			<?php echo $key === $first ? 'data-first' : ''; ?>
		>
			<?php
			if ( isset( $product_tab['callback'] ) ) {
				call_user_func( $product_tab['callback'], $key, $product_tab );
			}
			?>
		</div>
	<?php endforeach; ?>
</div>

<?php
/**
 * Product data tabs rendered as a vertical accordion instead of
 * WooCommerce's default horizontal tab bar — native <details>/<summary>,
 * no JS. Same `woocommerce_product_tabs` filter data (description,
 * additional_information, our custom size_guide tab from inc/hooks.php)
 * just presented as stacked, one-open-at-a-time sections.
 */

defined( 'ABSPATH' ) || exit;

$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( empty( $product_tabs ) ) {
	return;
}
?>
<div class="sk-accordion woocommerce-tabs">
	<?php
	$sk_first = true;
	foreach ( $product_tabs as $key => $product_tab ) :
		?>
		<details class="sk-accordion__item" <?php echo $sk_first ? 'open' : ''; // phpcs:ignore ?>>
			<summary class="sk-accordion__summary">
				<span><?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $product_tab['title'] ), $key ) ); ?></span>
				<span class="sk-accordion__icon" aria-hidden="true"></span>
			</summary>
			<div class="sk-accordion__panel woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?>">
				<?php
				if ( isset( $product_tab['callback'] ) ) {
					call_user_func( $product_tab['callback'], $key, $product_tab );
				}
				?>
			</div>
		</details>
		<?php
		$sk_first = false;
	endforeach;
	?>
</div>

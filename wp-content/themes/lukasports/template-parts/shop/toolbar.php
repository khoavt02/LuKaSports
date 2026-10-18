<?php
/**
 * Listing toolbar: quick-filter pills (template-parts/shop/quick-pills),
 * sorting (WooCommerce's own ordering form) and grid/list toggle.
 * On phones the pills move into the filter drawer and the toolbar is
 * just "Bộ lọc" + the sort select side by side.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sk-toolbar">
	<button type="button" class="sk-toolbar__filter-btn" aria-controls="sk-filters" aria-expanded="false" data-sk-filters-open>
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
		<?php esc_html_e( 'Bộ lọc', 'lukasports' ); ?>
	</button>

	<?php get_template_part( 'template-parts/shop/quick-pills', null, array( 'class' => 'sk-toolbar__pills' ) ); ?>

	<div class="sk-toolbar__end">
		<?php if ( function_exists( 'woocommerce_catalog_ordering' ) ) : ?>
			<div class="sk-toolbar__sort">
				<span class="sk-toolbar__sort-label"><?php esc_html_e( 'Sắp xếp:', 'lukasports' ); ?></span>
				<?php woocommerce_catalog_ordering(); ?>
			</div>
		<?php endif; ?>
		<div class="sk-view-toggle" role="group" aria-label="<?php esc_attr_e( 'Kiểu hiển thị', 'lukasports' ); ?>">
			<button type="button" class="sk-view-toggle__btn is-active" data-sk-view="grid" aria-pressed="true">
				<span class="sk-visually-hidden"><?php esc_html_e( 'Dạng lưới', 'lukasports' ); ?></span>
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="4" y="4" width="6.5" height="6.5"/><rect x="13.5" y="4" width="6.5" height="6.5"/><rect x="4" y="13.5" width="6.5" height="6.5"/><rect x="13.5" y="13.5" width="6.5" height="6.5"/></svg>
			</button>
			<button type="button" class="sk-view-toggle__btn" data-sk-view="list" aria-pressed="false">
				<span class="sk-visually-hidden"><?php esc_html_e( 'Dạng danh sách', 'lukasports' ); ?></span>
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M9 6h11M9 12h11M9 18h11M4 6h1M4 12h1M4 18h1"/></svg>
			</button>
		</div>
	</div>
</div>

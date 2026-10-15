<?php
/**
 * Listing toolbar: quick-filter pills (?loai=, newest collection),
 * sorting (WooCommerce's own ordering form) and grid/list toggle.
 * The "Bộ lọc" button only shows on small screens, where the sidebar
 * becomes a drawer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$type       = isset( $_GET['loai'] ) ? sanitize_key( wp_unslash( $_GET['loai'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$newest     = lukasports_newest_collection();
$on_newest  = $newest && is_tax( 'product_collection', $newest->term_id );
$base_url   = remove_query_arg( array( 'loai', 'paged', 'product-page' ) );
$base_url   = preg_replace( '#/page/\d+/?#', '/', $base_url );
$pills      = array(
	''             => __( 'Tất cả', 'lukasports' ),
	'co-san'       => __( 'Mẫu có sẵn', 'lukasports' ),
	'theo-yeu-cau' => __( 'Thiết kế theo yêu cầu', 'lukasports' ),
);
?>
<div class="sk-toolbar">
	<button type="button" class="sk-toolbar__filter-btn" aria-controls="sk-filters" aria-expanded="false" data-sk-filters-open>
		<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M3 5h14M6 10h8M8.5 15h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
		<?php esc_html_e( 'Bộ lọc', 'lukasports' ); ?>
	</button>

	<nav class="sk-pills" aria-label="<?php esc_attr_e( 'Lọc nhanh', 'lukasports' ); ?>">
		<?php foreach ( $pills as $value => $label ) : ?>
			<?php $active = ! $on_newest && $type === $value; ?>
			<a class="sk-pill<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( '' === $value ? $base_url : add_query_arg( 'loai', $value, $base_url ) ); ?>"<?php echo $active ? ' aria-current="true"' : ''; ?>>
				<?php echo esc_html( $label ); ?>
			</a>
		<?php endforeach; ?>
		<?php if ( $newest ) : ?>
			<a class="sk-pill<?php echo $on_newest ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $newest ) ); ?>">
				<?php esc_html_e( 'Bộ sưu tập mới', 'lukasports' ); ?>
				<span class="sk-pill__badge"><?php esc_html_e( 'Mới', 'lukasports' ); ?></span>
			</a>
		<?php endif; ?>
	</nav>

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
				<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="3" y="3" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.7"/><rect x="11" y="3" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.7"/><rect x="3" y="11" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.7"/><rect x="11" y="11" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.7"/></svg>
			</button>
			<button type="button" class="sk-view-toggle__btn" data-sk-view="list" aria-pressed="false">
				<span class="sk-visually-hidden"><?php esc_html_e( 'Dạng danh sách', 'lukasports' ); ?></span>
				<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M7 5h10M7 10h10M7 15h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="3.5" cy="5" r="1.2" fill="currentColor"/><circle cx="3.5" cy="10" r="1.2" fill="currentColor"/><circle cx="3.5" cy="15" r="1.2" fill="currentColor"/></svg>
			</button>
		</div>
	</div>
</div>

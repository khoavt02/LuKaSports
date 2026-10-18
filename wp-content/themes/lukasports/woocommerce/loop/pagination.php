<?php
/**
 * Override of WooCommerce's templates/loop/pagination.php — same
 * paginate_links() call, with chevron icons for previous / next.
 *
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$total   = isset( $total ) ? $total : wc_get_loop_prop( 'total_pages' );
$current = isset( $current ) ? $current : wc_get_loop_prop( 'current_page' );
$base    = isset( $base ) ? $base : esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
$format  = isset( $format ) ? $format : '';

if ( $total <= 1 ) {
	return;
}

$chevron = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="%s"/></svg>';
?>
<nav class="woocommerce-pagination" aria-label="<?php esc_attr_e( 'Phân trang', 'lukasports' ); ?>">
	<?php
	echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		apply_filters(
			'woocommerce_pagination_args',
			array(
				'base'      => $base,
				'format'    => $format,
				'add_args'  => false,
				'current'   => max( 1, $current ),
				'total'     => $total,
				'prev_text' => sprintf( $chevron, 'M15 6l-6 6 6 6' ) . '<span class="sk-visually-hidden">' . esc_html__( 'Trang trước', 'lukasports' ) . '</span>',
				'next_text' => sprintf( $chevron, 'M9 6l6 6-6 6' ) . '<span class="sk-visually-hidden">' . esc_html__( 'Trang sau', 'lukasports' ) . '</span>',
				'type'      => 'list',
				'end_size'  => 1,
				'mid_size'  => 1,
			)
		)
	);
	?>
</nav>

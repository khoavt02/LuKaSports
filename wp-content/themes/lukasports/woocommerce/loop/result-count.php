<?php
/**
 * Override of WooCommerce's templates/loop/result-count.php — same
 * data, worded like the design: "Hiển thị 1–12 trong 41 sản phẩm".
 *
 * @version 10.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$total    = (int) $total;
$per_page = (int) $per_page;
$current  = (int) $current;
if ( $total <= $per_page || -1 === $per_page ) {
	$first = $total ? 1 : 0;
	$last  = $total;
} else {
	$first = ( $per_page * $current ) - $per_page + 1;
	$last  = min( $total, $per_page * $current );
}
?>
<p class="woocommerce-result-count" role="status" aria-relevant="all">
	<?php
	printf(
		/* translators: 1: first–last shown, 2: total products */
		esc_html__( 'Hiển thị %1$s trong %2$s sản phẩm', 'lukasports' ),
		'<strong>' . esc_html( $first === $last ? (string) $first : $first . '–' . $last ) . '</strong>',
		'<strong>' . esc_html( number_format_i18n( $total ) ) . '</strong>'
	);
	?>
</p>

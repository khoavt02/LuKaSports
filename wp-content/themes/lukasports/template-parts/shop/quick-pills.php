<?php
/**
 * Quick-filter pills (?loai=, newest collection). Shown in the listing
 * toolbar on desktop and inside the filter drawer on phones.
 *
 * @var array $args { class?: string } extra class on the wrapper.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$type      = isset( $_GET['loai'] ) ? sanitize_key( wp_unslash( $_GET['loai'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$newest    = lukasports_newest_collection();
$on_newest = $newest && is_tax( 'product_collection', $newest->term_id );
$base_url  = remove_query_arg( array( 'loai', 'paged', 'product-page' ) );
$base_url  = preg_replace( '#/page/\d+/?#', '/', $base_url );
$pills     = array(
	''             => __( 'Tất cả', 'lukasports' ),
	'co-san'       => __( 'Mẫu có sẵn', 'lukasports' ),
	'theo-yeu-cau' => __( 'Thiết kế theo yêu cầu', 'lukasports' ),
);
$extra     = isset( $args['class'] ) ? ' ' . $args['class'] : '';
?>
<nav class="sk-pills<?php echo esc_attr( $extra ); ?>" aria-label="<?php esc_attr_e( 'Lọc nhanh', 'lukasports' ); ?>">
	<?php foreach ( $pills as $value => $label ) : ?>
		<?php $active = ! $on_newest && $type === $value; ?>
		<a class="sk-pill<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( '' === $value ? $base_url : add_query_arg( 'loai', $value, $base_url ) ); ?>"<?php echo $active ? ' aria-current="true"' : ''; ?>>
			<?php echo esc_html( $label ); ?>
		</a>
	<?php endforeach; ?>
	<?php if ( $newest ) : ?>
		<a class="sk-pill<?php echo $on_newest ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $newest ) ); ?>"<?php echo $on_newest ? ' aria-current="true"' : ''; ?>>
			<?php esc_html_e( 'Bộ sưu tập mới', 'lukasports' ); ?>
		</a>
	<?php endif; ?>
</nav>

<?php
/**
 * "Môn thể thao / Chọn đúng sân chơi" — one dark tile per sport, using
 * real WooCommerce product categories and their photos. The six sport
 * categories come first in a fixed order (short labels as in the
 * design); any other top-level category fills in if one is missing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! taxonomy_exists( 'product_cat' ) ) {
	return;
}

$terms = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'parent'     => 0,
		'exclude'    => array( get_option( 'default_product_cat', 0 ) ),
		'number'     => 12,
	)
);

if ( empty( $terms ) || is_wp_error( $terms ) ) {
	return;
}

// Preferred order + short display label per slug.
$preferred = array(
	'ao-bong-da'     => __( 'Bóng đá', 'lukasports' ),
	'ao-bong-chuyen' => __( 'Bóng chuyền', 'lukasports' ),
	'ao-bong-ro'     => __( 'Bóng rổ', 'lukasports' ),
	'ao-cau-long'    => __( 'Cầu lông', 'lukasports' ),
	'ao-pickerball'  => __( 'Pickleball', 'lukasports' ),
	'ao-team'        => __( 'Áo team', 'lukasports' ),
);

$by_slug = array();
foreach ( $terms as $term ) {
	$by_slug[ $term->slug ] = $term;
}
$tiles = array();
foreach ( $preferred as $slug => $label ) {
	if ( isset( $by_slug[ $slug ] ) ) {
		$tiles[] = array( $by_slug[ $slug ], $label );
		unset( $by_slug[ $slug ] );
	}
}
foreach ( $by_slug as $term ) {
	if ( count( $tiles ) >= 6 ) {
		break;
	}
	$tiles[] = array( $term, $term->name );
}

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' );
?>
<section class="sk-home-section sk-sports" aria-labelledby="sk-sports-title">
	<div class="sk-container">
		<div class="sk-section-head">
			<div>
				<p class="sk-section-head__eyebrow"><?php esc_html_e( 'Môn thể thao', 'lukasports' ); ?></p>
				<h2 id="sk-sports-title" class="sk-section-head__title"><?php esc_html_e( 'Chọn đúng sân chơi', 'lukasports' ); ?></h2>
				<p class="sk-section-head__lead"><?php esc_html_e( 'Mỗi môn một form riêng — chọn đúng sân chơi của bạn.', 'lukasports' ); ?></p>
			</div>
			<a class="sk-section-head__link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Tất cả sản phẩm', 'lukasports' ); ?></a>
		</div>
	</div>
	<div class="sk-sports__scroller">
		<div class="sk-container sk-sports__grid">
			<?php foreach ( $tiles as $tile ) : ?>
				<?php
				list( $term, $label ) = $tile;
				$image_id             = lukasports_category_image_id( $term, 'thumb' );
				?>
				<a class="sk-sport-tile" href="<?php echo esc_url( home_url( '/' . $term->slug . '/' ) ); ?>">
					<span class="sk-sport-tile__media" aria-hidden="true">
						<?php
						if ( $image_id ) {
							echo lukasports_attachment_image( $image_id, 'lukasports-card', 'lazy', '', '' ); // phpcs:ignore
						}
						?>
					</span>
					<span class="sk-sport-tile__slab" aria-hidden="true"></span>
					<span class="sk-sport-tile__foot">
						<span class="sk-sport-tile__name"><?php echo esc_html( $label ); ?></span>
						<span class="sk-sport-tile__arrow" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

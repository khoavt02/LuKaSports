<?php
/**
 * Pulls real WooCommerce product categories rather than a hardcoded
 * list, so this section always reflects what's actually in the catalog.
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

/**
 * Category-specific fallback art per slug — used until an admin sets a
 * real category image (Products → Categories → Thumbnail).
 */
$lukasports_category_art = array(
	'ao-bong-da'          => 'football.svg',
	'ao-bong-da-thiet-ke' => 'jersey-design.svg',
	'ao-team'             => 'team.svg',
	'ao-bong-chuyen'      => 'volleyball.svg',
	'ao-bong-ro'          => 'basketball.svg',
	'ao-cau-long'         => 'badminton.svg',
	'ao-pickerball'       => 'pickleball.svg',
	'phu-kien'            => 'accessory.svg',
);

/**
 * Bento layout, not a uniform grid: one featured category (football,
 * if present — it's the anchor of the catalog) fills the large tile,
 * everything else fills the smaller side tiles.
 */
$featured_index = 0;
foreach ( $terms as $i => $term ) {
	if ( 'ao-bong-da' === $term->slug ) {
		$featured_index = $i;
		break;
	}
}
$featured_term = $terms[ $featured_index ];
$side_terms    = array_slice( array_merge( array_slice( $terms, $featured_index + 1 ), array_slice( $terms, 0, $featured_index ) ), 0, 5 );

function lukasports_category_tile_image( $term, $art_map ) {
	$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
	if ( $thumbnail_id ) {
		return lukasports_attachment_image( $thumbnail_id, 'lukasports-category', 'lazy', '', $term->name );
	}
	$art = $art_map[ $term->slug ] ?? 'jersey-design.svg';
	return '<img src="' . esc_url( LUKASPORTS_THEME_URI . '/assets/images/categories/' . $art ) . '" alt="" loading="lazy" />';
}
?>
<section class="sk-section sk-categories">
	<div class="sk-container">
		<div class="sk-section-head">
			<div>
				<h2 class="sk-section-head__title"><?php esc_html_e( 'Môn thể thao', 'lukasports' ); ?></h2>
				<p class="sk-section-head__desc"><?php esc_html_e( 'Mỗi môn một form riêng — chọn đúng sân chơi của bạn.', 'lukasports' ); ?></p>
			</div>
		</div>
		<div class="sk-categories__bento">
			<a class="sk-category-card sk-category-card--featured" href="<?php echo esc_url( home_url( '/' . $featured_term->slug . '/' ) ); ?>">
				<div class="sk-category-card__media"><?php echo lukasports_category_tile_image( $featured_term, $lukasports_category_art ); // phpcs:ignore ?></div>
				<div class="sk-category-card__body">
					<h3 class="sk-category-card__title"><?php echo esc_html( $featured_term->name ); ?></h3>
					<span class="sk-category-card__cta"><?php esc_html_e( 'Khám phá', 'lukasports' ); ?> <span aria-hidden="true">→</span></span>
				</div>
			</a>
			<div class="sk-categories__side">
				<?php foreach ( $side_terms as $term ) : ?>
					<a class="sk-category-card sk-category-card--small" href="<?php echo esc_url( home_url( '/' . $term->slug . '/' ) ); ?>">
						<div class="sk-category-card__media"><?php echo lukasports_category_tile_image( $term, $lukasports_category_art ); // phpcs:ignore ?></div>
						<div class="sk-category-card__body">
							<h3 class="sk-category-card__title"><?php echo esc_html( $term->name ); ?></h3>
							<span class="sk-category-card__cta" aria-hidden="true">→</span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

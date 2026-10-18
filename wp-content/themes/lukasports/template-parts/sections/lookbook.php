<?php
/**
 * "DALETIC trên sân đấu / Đồng hành cùng các đội" — the site's stat
 * cards (trust-bar.php, embedded) above a dark photo wall of category
 * photos (banner, else thumbnail), each linking to its category.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slugs = array( 'ao-bong-da', 'ao-team', 'ao-bong-ro', 'ao-bong-da-thiet-ke', 'ao-bong-chuyen', 'ao-cau-long' );
$wall  = array();
foreach ( $slugs as $slug ) {
	$photo = lukasports_category_image_id( $slug, 'banner' );
	if ( $photo ) {
		$wall[ $slug ] = $photo;
	}
}
?>
<section class="sk-home-section sk-onfield" aria-labelledby="sk-onfield-title">
	<div class="sk-container">
		<div class="sk-section-head">
			<div>
				<p class="sk-section-head__eyebrow"><?php esc_html_e( 'DALETIC trên sân đấu', 'lukasports' ); ?></p>
				<h2 id="sk-onfield-title" class="sk-section-head__title"><?php esc_html_e( 'Đồng hành cùng các đội', 'lukasports' ); ?></h2>
			</div>
		</div>
		<?php get_template_part( 'template-parts/sections/trust-bar', null, array( 'embedded' => true ) ); ?>
		<?php if ( $wall ) : ?>
			<div class="sk-onfield__wall">
				<?php foreach ( $wall as $slug => $photo ) : ?>
					<?php $term = get_term_by( 'slug', $slug, 'product_cat' ); ?>
					<a class="sk-onfield__photo" href="<?php echo esc_url( home_url( '/' . $slug . '/' ) ); ?>">
						<?php echo lukasports_attachment_image( $photo, 'lukasports-card', 'lazy', '', $term ? $term->name : '' ); // phpcs:ignore ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

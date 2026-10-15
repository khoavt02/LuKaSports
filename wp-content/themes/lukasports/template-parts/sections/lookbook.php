<?php
/**
 * "DALETIC trên sân đấu" — asymmetric editorial grid of category
 * photos (banner, else thumbnail), each linking to its category; the
 * line-art is only a fallback for categories without a photo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Category slug => line-art fallback when the category has no photo yet.
$items = array(
	'ao-bong-da'          => 'football.svg',
	'ao-team'             => 'team.svg',
	'ao-bong-ro'          => 'basketball.svg',
	'ao-bong-da-thiet-ke' => 'jersey-design.svg',
	'ao-bong-chuyen'      => 'volleyball.svg',
	'ao-cau-long'         => 'badminton.svg',
);
?>
<section class="sk-section sk-lookbook">
	<div class="sk-container">
		<div class="sk-section-head">
			<h2 class="sk-section-head__title"><?php esc_html_e( 'DALETIC trên sân đấu', 'lukasports' ); ?></h2>
		</div>
		<div class="sk-lookbook__grid">
			<?php foreach ( $items as $slug => $art ) : ?>
				<a class="sk-lookbook__item" href="<?php echo esc_url( home_url( '/' . $slug . '/' ) ); ?>">
					<?php
					$photo = lukasports_category_image_id( $slug, 'banner' );
					if ( $photo ) {
						echo lukasports_attachment_image( $photo, 'lukasports-category', 'lazy' ); // phpcs:ignore
					} else {
						echo '<img src="' . esc_url( lukasports_image_url( 'categories/' . $art ) ) . '" alt="" loading="lazy" />';
					}
					?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

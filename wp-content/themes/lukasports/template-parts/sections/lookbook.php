<?php
/**
 * "LukaSports In Action" — asymmetric editorial grid. Uses the same
 * category line-art as elsewhere on the site (no real action
 * photography available yet) so it still reads as intentional rather
 * than a broken image grid; swap for real photos when available.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = array( 'football.svg', 'team.svg', 'basketball.svg', 'jersey-design.svg', 'volleyball.svg', 'badminton.svg' );
?>
<section class="sk-section sk-lookbook">
	<div class="sk-container">
		<div class="sk-section-head">
			<h2 class="sk-section-head__title"><?php esc_html_e( 'LukaSports In Action', 'lukasports' ); ?></h2>
		</div>
		<div class="sk-lookbook__grid">
			<?php foreach ( $items as $art ) : ?>
				<div class="sk-lookbook__item">
					<img src="<?php echo esc_url( LUKASPORTS_THEME_URI . '/assets/images/categories/' . $art ); ?>" alt="" loading="lazy" />
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

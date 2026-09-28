<?php
/**
 * Editorial per-sport blocks — links out to the matching product
 * category. Purely presentational (no query), so it always renders
 * regardless of catalog state; each block's target category simply
 * 404s gracefully via WordPress if that category doesn't exist yet.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stories = array(
	array(
		'title' => 'Bóng Đá',
		'desc'  => 'Áo đấu và đồng phục CLB, đội tuyển.',
		'slug'  => 'ao-bong-da',
		'art'   => 'football.svg',
	),
	array(
		'title' => 'Bóng Rổ',
		'desc'  => 'Form áo streetball, vải lưới thoáng khí.',
		'slug'  => 'ao-bong-ro',
		'art'   => 'basketball.svg',
	),
	array(
		'title' => 'Bóng Chuyền',
		'desc'  => 'Áo thi đấu co giãn, vận động linh hoạt.',
		'slug'  => 'ao-bong-chuyen',
		'art'   => 'volleyball.svg',
	),
	array(
		'title' => 'Pickleball',
		'desc'  => 'Áo pickleball nhẹ, khô nhanh.',
		'slug'  => 'ao-pickerball',
		'art'   => 'pickleball.svg',
	),
);
?>
<section class="sk-section sk-stories sk-section--surface">
	<div class="sk-container">
		<div class="sk-section-head">
			<h2 class="sk-section-head__title"><?php esc_html_e( 'Câu chuyện thể thao', 'lukasports' ); ?></h2>
		</div>
		<div class="sk-stories__grid">
			<?php foreach ( $stories as $story ) : ?>
				<a class="sk-story-card" href="<?php echo esc_url( home_url( '/' . $story['slug'] . '/' ) ); ?>">
					<div class="sk-story-card__media">
						<img src="<?php echo esc_url( LUKASPORTS_THEME_URI . '/assets/images/categories/' . $story['art'] ); ?>" alt="" loading="lazy" />
					</div>
					<div class="sk-story-card__overlay">
						<h3 class="sk-story-card__title"><?php echo esc_html( $story['title'] ); ?></h3>
						<p class="sk-story-card__desc"><?php echo esc_html( $story['desc'] ); ?></p>
						<span class="sk-story-card__link"><?php esc_html_e( 'Khám phá →', 'lukasports' ); ?></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

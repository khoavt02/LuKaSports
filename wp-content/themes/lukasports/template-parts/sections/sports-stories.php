<?php
/**
 * "Câu chuyện thể thao / Mỗi sân một chất riêng" — four tall photo
 * cards (category banner photo, else thumbnail) with a dark gradient,
 * linking to the matching product category. No product query, so it
 * always renders; cards whose category has no photo get a dark tile.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stories = array(
	array(
		'title' => __( 'Bóng đá', 'lukasports' ),
		'desc'  => __( 'Form ôm gọn, vải thoáng cho trọn 90 phút.', 'lukasports' ),
		'slug'  => 'ao-bong-da',
	),
	array(
		'title' => __( 'Bóng rổ', 'lukasports' ),
		'desc'  => __( 'Thoáng vai, nhẹ người cho từng cú bật nhảy.', 'lukasports' ),
		'slug'  => 'ao-bong-ro',
	),
	array(
		'title' => __( 'Bóng chuyền', 'lukasports' ),
		'desc'  => __( 'Co giãn 4 chiều cho từng pha đập bóng.', 'lukasports' ),
		'slug'  => 'ao-bong-chuyen',
	),
	array(
		'title' => __( 'Pickleball', 'lukasports' ),
		'desc'  => __( 'Gọn gàng, lịch sự mà vẫn thoải mái.', 'lukasports' ),
		'slug'  => 'ao-pickerball',
	),
);
?>
<section class="sk-home-section sk-stories" aria-labelledby="sk-stories-title">
	<div class="sk-container">
		<div class="sk-section-head">
			<div>
				<p class="sk-section-head__eyebrow"><?php esc_html_e( 'Câu chuyện thể thao', 'lukasports' ); ?></p>
				<h2 id="sk-stories-title" class="sk-section-head__title"><?php esc_html_e( 'Mỗi sân một chất riêng', 'lukasports' ); ?></h2>
			</div>
		</div>
	</div>
	<div class="sk-stories__scroller">
		<div class="sk-container sk-stories__grid">
			<?php foreach ( $stories as $story ) : ?>
				<a class="sk-story-card" href="<?php echo esc_url( home_url( '/' . $story['slug'] . '/' ) ); ?>">
					<span class="sk-story-card__media" aria-hidden="true">
						<?php
						$story_image = lukasports_category_image_id( $story['slug'], 'banner' );
						if ( $story_image ) {
							echo lukasports_attachment_image( $story_image, 'lukasports-category', 'lazy', '', '' ); // phpcs:ignore
						}
						?>
					</span>
					<span class="sk-story-card__body">
						<span class="sk-story-card__title"><?php echo esc_html( $story['title'] ); ?></span>
						<span class="sk-story-card__desc"><?php echo esc_html( $story['desc'] ); ?></span>
						<span class="sk-story-card__link"><?php esc_html_e( 'Khám phá', 'lukasports' ); ?> <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

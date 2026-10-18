<?php
/**
 * Homepage hero — dark stage with skewed slabs, "BỘ SƯU TẬP 2026" tag,
 * two-line italic headline, intro, two skewed CTAs over the store photo
 * (full-bleed background on desktop, a cropped strip on phones); a four-item features strip closes the section.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' );
$contact  = lukasports_get_contact_info();

// Only claims the site already makes elsewhere (product page / policies).
$features = array(
	array(
		'title' => __( 'Chất liệu thi đấu thật', 'lukasports' ),
		'desc'  => __( 'Thun lạnh, mè thoáng khí, co giãn 4 chiều.', 'lukasports' ),
		'icon'  => '<path d="M12 3l8 3v6c0 4.5-3.4 8.3-8 9-4.6-.7-8-4.5-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
	),
	array(
		'title' => __( 'Thiết kế theo yêu cầu', 'lukasports' ),
		'desc'  => __( 'Duyệt mockup trước khi sản xuất.', 'lukasports' ),
		'icon'  => '<path d="M4 20h4L19 9l-4-4L4 16v4z"/><path d="M14 6l4 4"/>',
	),
	array(
		'title' => __( 'Giao hàng toàn quốc', 'lukasports' ),
		'desc'  => __( '2–5 ngày làm việc.', 'lukasports' ),
		'icon'  => '<path d="M2 6h11v10H2z"/><path d="M13 9h4l4 4v3h-8"/><circle cx="6.5" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
	),
	array(
		'title' => __( 'Đổi trả trong 7 ngày', 'lukasports' ),
		'desc'  => __( 'Nếu lỗi từ nhà sản xuất.', 'lukasports' ),
		'icon'  => '<path d="M4 12a8 8 0 0 1 14-5.3M20 12a8 8 0 0 1-14 5.3"/><path d="M18 3v4h-4M6 21v-4h4"/>',
	),
);
?>
<section class="sk-hero" aria-labelledby="sk-hero-title">
	<div class="sk-hero__slabs" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
	<div class="sk-container sk-hero__inner">
		<div class="sk-hero__content">
			<p class="sk-eyebrow sk-hero__label">
				<span class="sk-eyebrow__tag"><?php esc_html_e( 'Bộ sưu tập 2026', 'lukasports' ); ?></span>
				<span class="sk-hero__slashes" aria-hidden="true">///</span>
			</p>
			<h1 id="sk-hero-title" class="sk-hero__title sk-display">
				<span class="sk-hero__line"><?php esc_html_e( 'Mặc chất riêng', 'lukasports' ); ?><span class="sk-accent-text">.</span></span>
				<span class="sk-hero__line"><?php esc_html_e( 'Chơi hết mình', 'lukasports' ); ?><span class="sk-accent-text">.</span></span>
			</h1>
			<p class="sk-hero__subtitle">
				<?php esc_html_e( 'Đồng phục và áo đấu cho cá nhân, đội bóng, CLB – chất liệu thi đấu thật, thiết kế theo yêu cầu.', 'lukasports' ); ?>
			</p>
			<div class="sk-hero__actions">
				<a class="sk-btn sk-btn--primary" href="<?php echo esc_url( $shop_url ); ?>">
					<?php esc_html_e( 'Khám phá sản phẩm', 'lukasports' ); ?>
					<svg class="sk-btn__arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
				</a>
				<a class="sk-btn sk-btn--outline-light" href="<?php echo esc_url( home_url( '/lien-he/' ) ); ?>" data-lukasports-cta="consult" data-source="home_hero">
					<?php echo esc_html( $contact['primary_cta_text'] ); ?>
				</a>
			</div>
		</div>
		<picture class="sk-hero__media">
			<source media="(max-width: 767px)" srcset="<?php echo esc_url( lukasports_image_url( 'hero-store-mobile.webp' ) ); ?>" width="776" height="770" />
			<img
				src="<?php echo esc_url( lukasports_get_hero_image_url() ); ?>"
				alt="<?php esc_attr_e( 'Cửa hàng DALETIC – thiết kế và sản xuất áo đấu thể thao', 'lukasports' ); ?>"
				width="1280" height="476"
				fetchpriority="high"
				loading="eager"
			/>
		</picture>
	</div>
	<div class="sk-hero__features">
		<ul class="sk-container sk-hero__features-list">
			<?php foreach ( $features as $feature ) : ?>
				<li class="sk-hero__feature">
					<span class="sk-hero__feature-icon" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?php echo $feature['icon']; // phpcs:ignore -- static SVG markup above. ?></svg></span>
					<span class="sk-hero__feature-text">
						<span class="sk-hero__feature-title"><?php echo esc_html( $feature['title'] ); ?></span>
						<span class="sk-hero__feature-desc"><?php echo esc_html( $feature['desc'] ); ?></span>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

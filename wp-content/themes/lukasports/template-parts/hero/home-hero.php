<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shop_url    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' );
$consult_url = home_url( '/lien-he/' );
$contact     = lukasports_get_contact_info();
?>
<section class="sk-hero">
	<div class="sk-hero__slashes" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
	<div class="sk-hero__media">
		<img
			src="<?php echo esc_url( lukasports_get_hero_image_url() ); ?>"
			alt="<?php esc_attr_e( 'Áo đấu DALETIC đen đỏ', 'lukasports' ); ?>"
			width="1400" height="991"
			fetchpriority="high"
			loading="eager"
		/>
	</div>
	<div class="sk-container sk-hero__overlay">
		<div class="sk-hero__content">
			<p class="sk-eyebrow sk-hero__label">
				<span class="sk-eyebrow__tag"><?php esc_html_e( 'Bộ sưu tập 2026', 'lukasports' ); ?></span>
				<span class="sk-eyebrow__slashes" aria-hidden="true"><i></i><i></i><i></i></span>
			</p>
			<h1 class="sk-hero__title sk-display">
				<span class="sk-hero__line"><?php esc_html_e( 'Mặc chất riêng', 'lukasports' ); ?><span class="sk-accent-text">.</span></span>
				<span class="sk-hero__line"><?php esc_html_e( 'Chơi hết mình', 'lukasports' ); ?><span class="sk-accent-text">.</span></span>
			</h1>
			<p class="sk-hero__subtitle">
				<?php esc_html_e( 'Đồng phục và áo đấu cho cá nhân, đội bóng, CLB — chất liệu thi đấu thật, thiết kế theo yêu cầu.', 'lukasports' ); ?>
			</p>
			<div class="sk-hero__actions">
				<a class="sk-btn sk-btn--primary sk-btn--lg" href="<?php echo esc_url( $shop_url ); ?>">
					<?php esc_html_e( 'Khám phá sản phẩm', 'lukasports' ); ?> <span class="sk-btn__arrow" aria-hidden="true">→</span>
				</a>
				<a class="sk-btn sk-btn--outline-light sk-btn--lg" href="<?php echo esc_url( $consult_url ); ?>" data-lukasports-cta="design_team">
					<?php echo esc_html( $contact['primary_cta_text'] ); ?> <span class="sk-btn__arrow" aria-hidden="true">→</span>
				</a>
			</div>
		</div>
	</div>
</section>

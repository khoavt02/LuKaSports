<?php
/**
 * Template Name: LukaSports — Giới thiệu
 *
 * Brand story page. The story paragraphs are the Page's own content
 * (editable in wp-admin); the surrounding structure — numbers, values,
 * the 3-step custom-design process and the closing CTA — is fixed
 * layout, reusing the homepage section parts so both stay in sync.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' );

$stats = array(
	array( 'value' => '5', 'label' => __( 'môn thể thao', 'lukasports' ) ),
	array( 'value' => '40+', 'label' => __( 'mẫu áo sẵn có', 'lukasports' ) ),
	array( 'value' => '7–10', 'label' => __( 'ngày cho đơn đồng phục đội', 'lukasports' ) ),
	array( 'value' => '1:1', 'label' => __( 'tư vấn size và thiết kế', 'lukasports' ) ),
);

$values = array(
	array(
		'title' => __( 'Chất liệu thi đấu thật', 'lukasports' ),
		'desc'  => __( 'Vải thun lạnh, mè thoáng khí, co giãn 4 chiều — chọn theo cường độ vận động của từng môn.', 'lukasports' ),
	),
	array(
		'title' => __( 'Form chuẩn người Việt', 'lukasports' ),
		'desc'  => __( 'Bảng size đo theo vóc dáng thực tế, có hướng dẫn chọn size trên từng sản phẩm.', 'lukasports' ),
	),
	array(
		'title' => __( 'In ấn bền màu', 'lukasports' ),
		'desc'  => __( 'In chuyển nhiệt và in lụa cao cấp — tên, số, logo không bong tróc sau nhiều lần giặt.', 'lukasports' ),
	),
	array(
		'title' => __( 'Tư vấn tận tâm', 'lukasports' ),
		'desc'  => __( 'Đội ngũ LukaSports đồng hành từ lúc lên ý tưởng đến khi áo tới tay cả đội.', 'lukasports' ),
	),
);
?>

<section class="sk-page-hero">
	<div class="sk-container sk-page-hero__inner">
		<p class="sk-page-hero__eyebrow"><?php esc_html_e( 'Về LukaSports', 'lukasports' ); ?></p>
		<h1 class="sk-page-hero__title"><?php esc_html_e( 'Đồ thi đấu cho người chơi thật.', 'lukasports' ); ?></h1>
		<p class="sk-page-hero__lead"><?php esc_html_e( 'LukaSports làm áo đấu và đồng phục cho cá nhân, đội bóng, CLB — từ sân phủi cuối tuần đến giải phong trào.', 'lukasports' ); ?></p>
		<div class="sk-page-hero__actions">
			<a class="sk-btn sk-btn--primary sk-btn--lg" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Xem sản phẩm', 'lukasports' ); ?></a>
			<a class="sk-btn sk-btn--outline-light sk-btn--lg" href="<?php echo esc_url( home_url( '/lien-he/' ) ); ?>" data-lukasports-cta="consult" data-source="about_hero"><?php esc_html_e( 'Nhận tư vấn', 'lukasports' ); ?></a>
		</div>
	</div>
</section>

<section class="sk-section sk-about-story">
	<div class="sk-container sk-about-story__inner">
		<h2 class="sk-about-story__title"><?php esc_html_e( 'Câu chuyện của chúng tôi', 'lukasports' ); ?></h2>
		<div class="sk-about-story__content sk-page__content">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</div>
</section>

<section class="sk-about-stats">
	<div class="sk-container">
		<dl class="sk-about-stats__grid">
			<?php foreach ( $stats as $stat ) : ?>
				<div class="sk-about-stats__item">
					<dt class="sk-about-stats__value"><?php echo esc_html( $stat['value'] ); ?></dt>
					<dd class="sk-about-stats__label"><?php echo esc_html( $stat['label'] ); ?></dd>
				</div>
			<?php endforeach; ?>
		</dl>
	</div>
</section>

<section class="sk-section sk-about-values">
	<div class="sk-container">
		<div class="sk-section-head">
			<div>
				<h2 class="sk-section-head__title"><?php esc_html_e( 'Điều chúng tôi cam kết', 'lukasports' ); ?></h2>
				<p class="sk-section-head__desc"><?php esc_html_e( 'Bốn nguyên tắc cho mọi chiếc áo rời xưởng LukaSports.', 'lukasports' ); ?></p>
			</div>
		</div>
		<div class="sk-about-values__grid">
			<?php foreach ( $values as $i => $value ) : ?>
				<div class="sk-about-values__card">
					<span class="sk-about-values__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					<h3 class="sk-about-values__title"><?php echo esc_html( $value['title'] ); ?></h3>
					<p class="sk-about-values__desc"><?php echo esc_html( $value['desc'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/sections/team-cta' ); ?>
<?php get_template_part( 'template-parts/sections/trust-bar' ); ?>
<?php get_template_part( 'template-parts/sections/contact-cta' ); ?>

<?php get_footer(); ?>

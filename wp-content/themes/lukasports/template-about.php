<?php
/**
 * Template Name: DALETIC — Giới thiệu
 *
 * Brand story page. The story paragraphs are the Page's own content
 * (editable in wp-admin); the surrounding structure — hero, numbers,
 * commitments, the 3-step custom-design process and the closing CTA —
 * is fixed layout. Styles: pages.css (About).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' );
$contact  = lukasports_get_contact_info();

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
		'desc'  => __( 'Đội ngũ DALETIC đồng hành từ lúc lên ý tưởng đến khi áo tới tay cả đội.', 'lukasports' ),
	),
);

$steps = array(
	array(
		'title' => __( 'Chọn ý tưởng', 'lukasports' ),
		'desc'  => __( 'Gửi mẫu tham khảo, màu sắc, logo đội bạn muốn thể hiện.', 'lukasports' ),
	),
	array(
		'title' => __( 'Thiết kế', 'lukasports' ),
		'desc'  => __( 'DALETIC dựng mẫu, gửi bản duyệt trước khi sản xuất.', 'lukasports' ),
	),
	array(
		'title' => __( 'Hoàn thiện', 'lukasports' ),
		'desc'  => __( 'In tên, số, giao hàng đúng hẹn cho cả đội.', 'lukasports' ),
	),
);

ob_start();
?>
<a class="sk-btn sk-btn--primary sk-btn--lg" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Xem sản phẩm', 'lukasports' ); ?></a>
<a class="sk-btn sk-btn--outline-light sk-btn--lg" href="<?php echo esc_url( home_url( '/lien-he/' ) ); ?>" data-lukasports-cta="consult" data-source="about_hero"><?php echo esc_html( $contact['primary_cta_text'] ); ?></a>
<?php
get_template_part(
	'template-parts/page-band',
	null,
	array(
		'label'   => __( 'Về DALETIC', 'lukasports' ),
		'title'   => __( 'Đồ thi đấu cho người chơi thật', 'lukasports' ),
		'dot'     => true,
		'hero'    => true,
		'intro'   => '<p>' . esc_html__( 'DALETIC làm áo đấu và đồng phục cho cá nhân, đội bóng, CLB — từ sân phủi cuối tuần đến giải phong trào.', 'lukasports' ) . '</p>',
		'media'   => lukasports_image_url( 'hero-jerseys.webp' ),
		'actions' => ob_get_clean(),
	)
);
?>

<section class="sk-about-story" aria-labelledby="sk-story-title">
	<div class="sk-container">
		<div class="sk-about-story__grid">
			<div class="sk-about-story__head">
				<p class="sk-section-head__eyebrow"><?php esc_html_e( 'Câu chuyện', 'lukasports' ); ?></p>
				<h2 class="sk-about-story__title sk-display" id="sk-story-title"><?php esc_html_e( 'Bắt đầu từ sân bóng', 'lukasports' ); ?></h2>
			</div>
			<div class="sk-about-story__content">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</div>

		<dl class="sk-about-tiles">
			<?php foreach ( $stats as $stat ) : ?>
				<div class="sk-about-tiles__item">
					<dd class="sk-about-tiles__value"><?php echo esc_html( $stat['value'] ); ?></dd>
					<dt class="sk-about-tiles__label"><?php echo esc_html( $stat['label'] ); ?></dt>
				</div>
			<?php endforeach; ?>
		</dl>
	</div>
</section>

<section class="sk-about-values" aria-labelledby="sk-values-title">
	<div class="sk-container">
		<p class="sk-section-head__eyebrow"><?php esc_html_e( 'Cam kết', 'lukasports' ); ?></p>
		<h2 class="sk-section-head__title" id="sk-values-title"><?php esc_html_e( 'Điều DALETIC giữ vững', 'lukasports' ); ?></h2>
		<ol class="sk-about-values__grid">
			<?php foreach ( $values as $i => $value ) : ?>
				<li class="sk-about-values__card">
					<span class="sk-about-values__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					<div>
						<h3 class="sk-about-values__title"><?php echo esc_html( $value['title'] ); ?></h3>
						<p class="sk-about-values__desc"><?php echo esc_html( $value['desc'] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<section class="sk-about-process sk-on-dark" aria-labelledby="sk-process-title">
	<div class="sk-container sk-about-process__inner">
		<div class="sk-about-process__head">
			<p class="sk-section-head__eyebrow"><?php esc_html_e( 'Quy trình', 'lukasports' ); ?></p>
			<h2 class="sk-about-process__title sk-display" id="sk-process-title"><?php esc_html_e( 'Thiết kế theo cách của bạn', 'lukasports' ); ?></h2>
			<div class="sk-about-process__actions">
				<a class="sk-btn sk-btn--primary sk-btn--md" href="<?php echo esc_url( home_url( '/lien-he/' ) ); ?>" data-lukasports-cta="consult" data-source="about_process"><?php esc_html_e( 'Tư vấn thiết kế', 'lukasports' ); ?></a>
				<a class="sk-btn sk-btn--outline-light sk-btn--md" href="<?php echo esc_url( $contact['messenger_url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Nhắn tin tư vấn', 'lukasports' ); ?></a>
			</div>
		</div>
		<ol class="sk-about-process__steps">
			<?php foreach ( $steps as $i => $step ) : ?>
				<li class="sk-about-process__step">
					<span class="sk-about-process__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					<div>
						<h3 class="sk-about-process__step-title"><?php echo esc_html( $step['title'] ); ?></h3>
						<p class="sk-about-process__step-desc"><?php echo esc_html( $step['desc'] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<?php
get_template_part(
	'template-parts/content-cta',
	null,
	array(
		'title' => __( 'Bạn đang tìm mẫu áo cho đội?', 'lukasports' ),
		/* translators: %s: hotline number */
		'text'  => sprintf( __( 'Nhắn DALETIC qua Messenger, Zalo hoặc gọi %s.', 'lukasports' ), $contact['hotline_display'] ),
		'call'  => true,
	)
);
?>

<?php get_footer(); ?>

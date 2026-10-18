<?php
/**
 * "Áo đội theo yêu cầu / Thiết kế theo cách của bạn" — dark section,
 * intro + two CTAs on the left, three numbered steps on the right.
 * Also used on the About page. No query involved.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact = lukasports_get_contact_info();

$steps = array(
	array(
		'num'   => '01',
		'title' => __( 'Chọn ý tưởng', 'lukasports' ),
		'desc'  => __( 'Gửi hình tham khảo, màu sắc và logo của đội.', 'lukasports' ),
	),
	array(
		'num'   => '02',
		'title' => __( 'Thiết kế', 'lukasports' ),
		'desc'  => __( 'DALETIC dựng mockup và gửi bạn duyệt trước khi sản xuất.', 'lukasports' ),
	),
	array(
		'num'   => '03',
		'title' => __( 'Hoàn thiện', 'lukasports' ),
		'desc'  => __( 'In tên, số áo và giao đúng hẹn cho cả đội.', 'lukasports' ),
	),
);
?>
<section class="sk-process sk-on-dark" aria-labelledby="sk-process-title">
	<span class="sk-process__slab" aria-hidden="true"></span>
	<div class="sk-container sk-process__inner">
		<div class="sk-process__intro">
			<p class="sk-section-head__eyebrow"><?php esc_html_e( 'Áo đội theo yêu cầu', 'lukasports' ); ?></p>
			<h2 id="sk-process-title" class="sk-process__title sk-display"><?php esc_html_e( 'Thiết kế theo cách của bạn', 'lukasports' ); ?></h2>
			<p class="sk-process__lead"><?php esc_html_e( 'Bạn có ý tưởng, DALETIC lo phần còn lại – từ mockup đến tên số và giao hàng cho cả đội.', 'lukasports' ); ?></p>
			<div class="sk-process__actions">
				<a class="sk-btn sk-btn--primary sk-btn--md" href="<?php echo esc_url( home_url( '/lien-he/' ) ); ?>" data-lukasports-cta="consult" data-source="custom_design">
					<?php esc_html_e( 'Tư vấn thiết kế', 'lukasports' ); ?>
				</a>
				<a class="sk-btn sk-btn--outline-light sk-btn--md" href="<?php echo esc_url( $contact['messenger_url'] ); ?>" target="_blank" rel="noopener">
					<?php esc_html_e( 'Nhắn tin tư vấn', 'lukasports' ); ?>
				</a>
			</div>
		</div>
		<ol class="sk-process__steps">
			<?php foreach ( $steps as $step ) : ?>
				<li class="sk-process__step">
					<span class="sk-process__num"><?php echo esc_html( $step['num'] ); ?></span>
					<div>
						<h3 class="sk-process__step-title"><?php echo esc_html( $step['title'] ); ?></h3>
						<p class="sk-process__step-desc"><?php echo esc_html( $step['desc'] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

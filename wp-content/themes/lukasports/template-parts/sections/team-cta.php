<?php
/**
 * "Custom Design" — the identity section for team/club orders. Dark
 * navy, numbered 3-step process, single strong CTA. Renders the same
 * regardless of catalog state (no query involved).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$consult_url = home_url( '/lien-he/' );
$contact     = lukasports_get_contact_info();

$steps = array(
	array(
		'num'   => '01',
		'title' => 'Chọn ý tưởng',
		'desc'  => 'Gửi mẫu tham khảo, màu sắc, logo đội bạn muốn thể hiện.',
	),
	array(
		'num'   => '02',
		'title' => 'Thiết kế',
		'desc'  => 'LukaSports dựng mẫu, gửi bản duyệt trước khi sản xuất.',
	),
	array(
		'num'   => '03',
		'title' => 'Hoàn thiện',
		'desc'  => 'In tên, số, giao hàng đúng hẹn cho cả đội.',
	),
);
?>
<section class="sk-custom-design">
	<div class="sk-container sk-custom-design__inner">
		<h2 class="sk-custom-design__title"><?php esc_html_e( 'Thiết kế theo cách của bạn.', 'lukasports' ); ?></h2>
		<p class="sk-custom-design__subtitle">
			<?php esc_html_e( 'Đồng phục riêng cho đội bóng, CLB và nhóm thể thao.', 'lukasports' ); ?>
		</p>
		<div class="sk-custom-design__steps">
			<?php foreach ( $steps as $step ) : ?>
				<div class="sk-custom-design__step">
					<div class="sk-custom-design__step-num"><?php echo esc_html( $step['num'] ); ?></div>
					<h3 class="sk-custom-design__step-title"><?php echo esc_html( $step['title'] ); ?></h3>
					<p class="sk-custom-design__step-desc"><?php echo esc_html( $step['desc'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="sk-custom-design__actions">
			<a class="sk-btn sk-btn--primary sk-btn--lg" href="<?php echo esc_url( $consult_url ); ?>" data-lukasports-cta="design_team">
				<?php esc_html_e( 'Tư vấn thiết kế', 'lukasports' ); ?>
			</a>
			<a class="sk-btn sk-btn--outline-light sk-btn--lg" href="<?php echo esc_url( $contact['messenger_url'] ); ?>" target="_blank" rel="noopener" data-lukasports-cta="consult" data-source="custom_design">
				<?php esc_html_e( 'Nhắn tin tư vấn', 'lukasports' ); ?>
			</a>
		</div>
	</div>
</section>

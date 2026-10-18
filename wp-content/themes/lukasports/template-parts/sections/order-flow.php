<?php
/**
 * "Quy trình đặt hàng" — five numbered cards (Tư vấn → Thiết kế → Duyệt
 * mẫu → Sản xuất → Giao hàng), each with a line icon. Homepage section;
 * styles in home.css (Order flow). Icons are inline SVG, drawn on a
 * 48×48 grid: strokes use currentColor, red details use .sk-ico-a.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact = lukasports_get_contact_info();

$steps = array(
	array(
		'title' => __( 'Tư vấn', 'lukasports' ),
		'desc'  => __( 'Gửi ý tưởng, màu sắc, logo và số lượng. DALETIC tư vấn chất vải và báo giá.', 'lukasports' ),
		'icon'  => '<path d="M9 26a15 15 0 0 1 30 0"/><rect x="6" y="25" width="7" height="12" rx="3.5"/><rect x="35" y="25" width="7" height="12" rx="3.5"/><path d="M38.5 37v1.5a4.5 4.5 0 0 1-4.5 4.5h-6"/><circle class="sk-ico-a" cx="19" cy="22" r="1.4"/><circle class="sk-ico-a" cx="24" cy="22" r="1.4"/><circle class="sk-ico-a" cx="29" cy="22" r="1.4"/>',
	),
	array(
		'title' => __( 'Thiết kế', 'lukasports' ),
		'desc'  => __( 'Dựng mockup áo, quần, tất theo yêu cầu của đội.', 'lukasports' ),
		'icon'  => '<rect x="5" y="7" width="31" height="34" rx="4"/><path class="sk-ico-a" d="M13 18l5-2.5c.8 1.8 2 2.5 3 2.5s2.2-.7 3-2.5l5 2.5-2 5-3-1v11h-6V22l-3 1z"/><path class="sk-ico-a" d="M31 38l1-4 11-11 3 3-11 11z"/>',
	),
	array(
		'title' => __( 'Duyệt mẫu', 'lukasports' ),
		'desc'  => __( 'Xem mockup, góp ý và chỉnh sửa đến khi bạn ưng ý.', 'lukasports' ),
		'icon'  => '<rect x="8" y="7" width="27" height="34" rx="4"/><rect x="16" y="4" width="11" height="6" rx="2"/><path d="M14 19h15M14 25h15M14 31h8"/><circle class="sk-ico-a" cx="35" cy="35" r="9"/><path class="sk-ico-a" d="M30.5 35l3 3 6-6"/>',
	),
	array(
		'title' => __( 'Sản xuất', 'lukasports' ),
		'desc'  => __( 'Chốt mẫu, tên, số và size rồi mới bắt đầu sản xuất.', 'lukasports' ),
		'icon'  => '<path d="M7 38V14a2 2 0 0 1 2-2h19a2 2 0 0 1 2 2v7h10v17"/><path d="M4 41h40"/><circle cx="15" cy="20" r="3.2"/><path class="sk-ico-a" d="M24 22v13"/><path class="sk-ico-a" d="M18.2 20c3.2 0 5 1.2 5.8 3"/>',
	),
	array(
		'title' => __( 'Giao hàng', 'lukasports' ),
		'desc'  => __( 'Đóng gói và giao tận nơi trên toàn quốc.', 'lukasports' ),
		'icon'  => '<rect x="3" y="11" width="25" height="23" rx="2.5"/><path d="M28 18h9l7 8.5V34H28z"/><circle cx="13" cy="36" r="4"/><circle cx="36" cy="36" r="4"/><path class="sk-ico-a" d="M32 22h4l3.5 4.5H32z"/><path class="sk-ico-a" d="M9 19h12M9 25h8"/>',
	),
);
?>
<section class="sk-section sk-order-flow" aria-labelledby="sk-order-flow-title">
	<div class="sk-container">
		<header class="sk-order-flow__head">
			<h2 class="sk-order-flow__title sk-display" id="sk-order-flow-title">
				<span class="sk-order-flow__slashes" aria-hidden="true">///</span>
				<?php esc_html_e( 'Quy trình', 'lukasports' ); ?> <span class="sk-accent-text"><?php esc_html_e( 'đặt hàng', 'lukasports' ); ?></span>
				<span class="sk-order-flow__slashes" aria-hidden="true">///</span>
			</h2>
			<p class="sk-order-flow__lead"><?php esc_html_e( 'Từ ý tưởng đến áo đấu của đội trong 5 bước', 'lukasports' ); ?></p>
		</header>

		<ol class="sk-order-flow__list">
			<?php foreach ( $steps as $i => $step ) : ?>
				<li class="sk-order-flow__step">
					<span class="sk-order-flow__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					<span class="sk-order-flow__icon" aria-hidden="true">
						<svg viewBox="0 0 48 48" width="52" height="52" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?php echo $step['icon']; // phpcs:ignore -- static markup ?></svg>
					</span>
					<div class="sk-order-flow__text">
						<h3 class="sk-order-flow__step-title"><?php echo esc_html( $step['title'] ); ?></h3>
						<p class="sk-order-flow__step-desc"><?php echo esc_html( $step['desc'] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>

		<div class="sk-order-flow__actions">
			<a class="sk-btn sk-btn--primary sk-btn--md" href="<?php echo esc_url( home_url( '/lien-he/' ) ); ?>" data-lukasports-cta="consult" data-source="order_flow"><?php esc_html_e( 'Tư vấn thiết kế', 'lukasports' ); ?></a>
			<a class="sk-btn sk-btn--outline-light sk-btn--md" href="<?php echo esc_url( $contact['zalo_url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Nhắn Zalo', 'lukasports' ); ?></a>
		</div>
	</div>
</section>

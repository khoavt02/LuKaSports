<?php
/**
 * "Quy trình đặt hàng" — five numbered cards (Tư vấn → Thiết kế → Duyệt
 * mẫu → Sản xuất → Giao hàng), each with a line icon. Homepage section;
 * styles in home.css (Order flow). Icons are 3D-style inline SVG drawn on
 * a 96×96 grid; their shared gradients live in the hidden <defs> svg below.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact = lukasports_get_contact_info();

$steps = array(
	array(
		'title' => __( 'Tư vấn', 'lukasports' ),
		'desc'  => __( 'Gửi ý tưởng, màu sắc, logo và số lượng. DALETIC tư vấn chất vải và báo giá.', 'lukasports' ),
		'icon'  => '<ellipse cx="48" cy="90" rx="26" ry="3.5" fill="#000" opacity=".25"/><rect x="22" y="11" width="44" height="80" rx="10" fill="url(#skg-dark-d)"/><rect x="22" y="8" width="44" height="80" rx="10" fill="url(#skg-dark)"/><rect x="26" y="12" width="36" height="72" rx="6.5" fill="url(#skg-light)"/><rect x="37" y="14.500" width="14" height="3" rx="1.500" fill="#2c3340"/><rect x="30" y="26" width="22" height="11" rx="5.500" fill="url(#skg-red)"/><rect x="36" y="42" width="22" height="11" rx="5.500" fill="#b4bcc9"/><rect x="30" y="58" width="16" height="11" rx="5.500" fill="url(#skg-red)"/><path d="M58 20h20a11 11 0 0 1 11 11v5a11 11 0 0 1-11 11h-9l-9 8v-8h-2a11 11 0 0 1-11-11v-5a11 11 0 0 1 11-11z" fill="url(#skg-red-d)" transform="translate(0 3)"/><path d="M58 20h20a11 11 0 0 1 11 11v5a11 11 0 0 1-11 11h-9l-9 8v-8h-2a11 11 0 0 1-11-11v-5a11 11 0 0 1 11-11z" fill="url(#skg-red)"/><path d="M58 20h20a11 11 0 0 1 11 11v2H47v-2a11 11 0 0 1 11-11z" fill="url(#skg-shine)" opacity=".6"/><circle cx="58" cy="35" r="2.800" fill="#fff"/><circle cx="68" cy="35" r="2.800" fill="#fff"/><circle cx="78" cy="35" r="2.800" fill="#fff"/>',
	),
	array(
		'title' => __( 'Thiết kế', 'lukasports' ),
		'desc'  => __( 'Dựng mockup áo, quần, tất theo yêu cầu của đội.', 'lukasports' ),
		'icon'  => '<path d="M46 14C25 14 8 29 8 48c0 17 13 31 29 31 6 0 8-3 7-7-1-5 2-9 7-9h12c12 0 21-8 21-19C84 27 68 14 46 14z" fill="url(#skg-dark-d)" transform="translate(0 4)"/><path d="M46 14C25 14 8 29 8 48c0 17 13 31 29 31 6 0 8-3 7-7-1-5 2-9 7-9h12c12 0 21-8 21-19C84 27 68 14 46 14z" fill="url(#skg-light)"/><path d="M46 14C25 14 8 29 8 48c0 1 .1 2 .2 3C14 36 28 24 46 24c20 0 33 8 37 20-2-16-17-30-37-30z" fill="url(#skg-shine)" opacity=".7"/><circle cx="22" cy="46" r="6.500" fill="url(#skg-red)"/><circle cx="35" cy="31" r="6.500" fill="url(#skg-gold)"/><circle cx="52" cy="29" r="6.500" fill="url(#skg-sky)"/><circle cx="68" cy="38" r="6.500" fill="url(#skg-dark)"/><ellipse cx="30" cy="63" rx="6" ry="5" fill="#12161e"/><g transform="rotate(42 70 28)"><rect x="65" y="-6" width="10" height="52" rx="5" fill="url(#skg-dark-d)" transform="translate(1.500 2)"/><rect x="65" y="-6" width="10" height="46" rx="5" fill="url(#skg-red)"/><rect x="67" y="-4" width="3" height="42" rx="1.500" fill="url(#skg-shine)" opacity=".7"/><rect x="64" y="36" width="12" height="9" rx="2" fill="url(#skg-light)"/><path d="M65 45h10c0 7-2.500 12-5 15-2.500-3-5-8-5-15z" fill="url(#skg-red-d)"/><path d="M66.500 45h4c0 6-.8 10-2 13-1.700-3-2-7-2-13z" fill="url(#skg-red)"/></g>',
	),
	array(
		'title' => __( 'Duyệt mẫu', 'lukasports' ),
		'desc'  => __( 'Xem mockup, góp ý và chỉnh sửa đến khi bạn ưng ý.', 'lukasports' ),
		'icon'  => '<ellipse cx="46" cy="90" rx="30" ry="3.5" fill="#000" opacity=".25"/><g transform="rotate(-45 70 70)"><rect x="64" y="52" width="13" height="42" rx="6.500" fill="url(#skg-dark-d)" transform="translate(1.500 2)"/><rect x="64" y="52" width="13" height="42" rx="6.500" fill="url(#skg-red)"/><rect x="66" y="55" width="3.500" height="34" rx="1.700" fill="url(#skg-shine)" opacity=".7"/><rect x="62" y="48" width="17" height="9" rx="4" fill="url(#skg-dark)"/></g><circle cx="42" cy="42" r="32" fill="url(#skg-dark-d)" transform="translate(0 3)"/><circle cx="42" cy="42" r="32" fill="url(#skg-dark)"/><circle cx="42" cy="42" r="25" fill="url(#skg-glass)"/><path d="M20 36a24 24 0 0 1 36-12c-8-2-20 0-30 9-3 3-5 6-6 9z" fill="#fff" opacity=".65"/><circle cx="42" cy="44" r="14" fill="url(#skg-red-d)" transform="translate(0 2)"/><circle cx="42" cy="44" r="14" fill="url(#skg-red)"/><path d="M34.500 44.500l6 6 11-12.500" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>',
	),
	array(
		'title' => __( 'Sản xuất', 'lukasports' ),
		'desc'  => __( 'Chốt mẫu, tên, số và size rồi mới bắt đầu sản xuất.', 'lukasports' ),
		'icon'  => '<ellipse cx="40" cy="88" rx="30" ry="4" fill="#000" opacity=".25"/><ellipse cx="40" cy="73" rx="25" ry="8" fill="url(#skg-light)"/><rect x="15" y="62" width="50" height="11" fill="url(#skg-light)"/><ellipse cx="40" cy="62" rx="25" ry="8" fill="#c5ccd8"/><path d="M18 22v40c0 4 9 7 22 7s22-3 22-7V22z" fill="url(#skg-red-d)"/><path d="M18 22v40c0 4 9 7 22 7s22-3 22-7V22z" fill="url(#skg-red)"/><g stroke="#fff" stroke-opacity=".28" stroke-width="1.800" fill="none"><path d="M18 30c8 5 36 5 44 0M18 38c8 5 36 5 44 0M18 46c8 5 36 5 44 0M18 54c8 5 36 5 44 0M18 62c8 5 36 5 44 0"/></g><rect x="20" y="24" width="8" height="40" fill="url(#skg-shine)" opacity=".5"/><ellipse cx="40" cy="22" rx="25" ry="8" fill="url(#skg-light)"/><ellipse cx="40" cy="22" rx="25" ry="8" fill="url(#skg-shine)" opacity=".5"/><ellipse cx="40" cy="22" rx="8" ry="3" fill="#2c3340"/><path d="M40 24c14 6 22 18 20 34-1 8 4 12 12 8" fill="none" stroke="url(#skg-red)" stroke-width="2.800" stroke-linecap="round"/><g transform="rotate(24 76 40)"><rect x="74.500" y="6" width="3.600" height="62" rx="1.800" fill="url(#skg-light)"/><rect x="74.500" y="6" width="1.600" height="62" rx=".8" fill="#fff" opacity=".7"/><ellipse cx="76.300" cy="13" rx="1.300" ry="4" fill="#2c3340"/><path d="M74.500 66h3.600l-1.800 12z" fill="#aab2c0"/></g>',
	),
	array(
		'title' => __( 'Giao hàng', 'lukasports' ),
		'desc'  => __( 'Đóng gói và giao tận nơi trên toàn quốc.', 'lukasports' ),
		'icon'  => '<ellipse cx="52" cy="88" rx="38" ry="4" fill="#000" opacity=".25"/><g stroke="#fff" stroke-opacity=".55" stroke-width="3" stroke-linecap="round"><path d="M3 36h12M1 46h10M5 56h9"/></g><rect x="14" y="20" width="46" height="45" rx="6" fill="url(#skg-dark-d)" transform="translate(0 3)"/><rect x="14" y="20" width="46" height="45" rx="6" fill="url(#skg-light)"/><rect x="14" y="20" width="46" height="14" rx="6" fill="url(#skg-shine)" opacity=".65"/><rect x="14" y="42" width="46" height="10" fill="url(#skg-red)"/><rect x="53" y="20" width="7" height="45" rx="3" fill="#000" opacity=".08"/><path d="M60 33h16q7 0 10.500 6.500L92 53v12H60z" fill="url(#skg-red-d)" transform="translate(0 3)"/><path d="M60 33h16q7 0 10.500 6.500L92 53v12H60z" fill="url(#skg-red)"/><path d="M66 38h10.500q3.500 0 5.200 3.200L85 49H66z" fill="url(#skg-glass)"/><path d="M66 38h10.500q3.500 0 5.200 3.200L83 43H66z" fill="url(#skg-shine)" opacity=".6"/><rect x="86" y="54" width="7" height="5" rx="2" fill="url(#skg-gold)"/><rect x="14" y="63" width="80" height="8" rx="4" fill="url(#skg-dark)"/><circle cx="33" cy="72" r="11" fill="url(#skg-dark-d)"/><circle cx="33" cy="72" r="11" fill="none" stroke="#12161e" stroke-width="2"/><circle cx="33" cy="72" r="5" fill="url(#skg-light)"/><circle cx="76" cy="72" r="11" fill="url(#skg-dark-d)"/><circle cx="76" cy="72" r="11" fill="none" stroke="#12161e" stroke-width="2"/><circle cx="76" cy="72" r="5" fill="url(#skg-light)"/>',
	),
);
?>
<section class="sk-section sk-order-flow" aria-labelledby="sk-order-flow-title">
	<?php lukasports_icon_defs(); ?>
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
						<svg viewBox="0 0 96 96" width="86" height="86"><?php echo $step['icon']; // phpcs:ignore -- static markup ?></svg>
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

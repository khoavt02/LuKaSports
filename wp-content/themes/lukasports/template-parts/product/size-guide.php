<?php
/**
 * Shared size charts across the catalog — sizing is standardized across
 * DALETIC's product line, so one set serves every product. Plain HTML
 * tables (black / white / red, follow the light/dark theme) so they stay
 * readable, searchable and easy to edit in the $charts list below.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$charts = array(
	array(
		'title' => __( 'Bảng size áo bóng đá thiết kế', 'lukasports' ),
		'cols'  => array( __( 'Size', 'lukasports' ), __( 'Chiều cao', 'lukasports' ), __( 'Cân nặng', 'lukasports' ) ),
		'rows'  => array(
			array( 'S', '1m45 – 1m59', '45 – 55 kg' ),
			array( 'M', '1m60 – 1m66', '55 – 61 kg' ),
			array( 'L', '1m66 – 1m71', '62 – 69 kg' ),
			array( 'XL', '1m72 – 1m78', '70 – 77 kg' ),
			array( 'XXL', '1m79 – 1m85', '78 – 85 kg' ),
			array( '3XL', '1m85 – 1m90', '86 – 90 kg' ),
			array( '4XL', '1m90 – 1m94', '91 – 95 kg' ),
		),
	),
	array(
		'title' => __( 'Bảng size áo trẻ em', 'lukasports' ),
		'cols'  => array( __( 'Size', 'lukasports' ), __( 'Độ tuổi', 'lukasports' ), __( 'Cân nặng', 'lukasports' ) ),
		'rows'  => array(
			array( '1', 'Đến 1 tuổi', '5 – 10 kg' ),
			array( '3', 'Đến 3 tuổi', '10 – 15 kg' ),
			array( '5', 'Đến 5 tuổi', '16 – 21 kg' ),
			array( '7', 'Đến 7 tuổi', '22 – 28 kg' ),
			array( '9', 'Đến 9 tuổi', '29 – 33 kg' ),
			array( '11', 'Đến 11 tuổi', '34 – 39 kg' ),
			array( '13', 'Đến 13 tuổi', '40 – 45 kg' ),
		),
	),
);
?>
<div class="sk-size-guide">
	<?php foreach ( $charts as $chart ) : ?>
		<section class="sk-size-guide__chart">
			<h3 class="sk-size-guide__title"><?php echo esc_html( $chart['title'] ); ?></h3>
			<table class="sk-size-guide__table">
				<thead>
					<tr>
						<?php foreach ( $chart['cols'] as $col ) : ?>
							<th scope="col"><?php echo esc_html( $col ); ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $chart['rows'] as list( $size, $range, $weight ) ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $size ); ?></th>
							<td><?php echo esc_html( $range ); ?></td>
							<td><?php echo esc_html( $weight ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</section>
	<?php endforeach; ?>
	<p class="sk-size-guide__tip"><strong><?php esc_html_e( 'Một người có thể mặc 2 size.', 'lukasports' ); ?></strong> <?php esc_html_e( 'Mặc ôm (body) chọn size nhỏ hơn, mặc thoải mái chọn size lớn hơn.', 'lukasports' ); ?></p>
	<p><?php esc_html_e( 'Chưa chắc chọn size nào? Nhắn Zalo/Messenger cho DALETIC — tư vấn size miễn phí.', 'lukasports' ); ?></p>
</div>

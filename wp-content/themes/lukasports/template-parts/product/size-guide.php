<?php
/**
 * Shared size chart across the catalog — sizing is standardized across
 * DALETIC's product line, so one chart serves every product. Plain HTML
 * table (black / white / red, follows the light/dark theme) so it stays
 * readable, searchable and easy to edit in the $rows list below.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// size, height, weight
$rows = array(
	array( 'S', '1m45 – 1m59', '45 – 55 kg' ),
	array( 'M', '1m60 – 1m66', '55 – 61 kg' ),
	array( 'L', '1m66 – 1m71', '62 – 69 kg' ),
	array( 'XL', '1m72 – 1m78', '70 – 77 kg' ),
	array( 'XXL', '1m79 – 1m85', '78 – 85 kg' ),
	array( '3XL', '1m85 – 1m90', '86 – 90 kg' ),
	array( '4XL', '1m90 – 1m94', '91 – 95 kg' ),
);
?>
<div class="sk-size-guide">
	<h3 class="sk-size-guide__title"><?php esc_html_e( 'Bảng size áo bóng đá thiết kế', 'lukasports' ); ?></h3>
	<table class="sk-size-guide__table">
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'Size', 'lukasports' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Chiều cao', 'lukasports' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Cân nặng', 'lukasports' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $rows as list( $size, $height, $weight ) ) : ?>
				<tr>
					<th scope="row"><?php echo esc_html( $size ); ?></th>
					<td><?php echo esc_html( $height ); ?></td>
					<td><?php echo esc_html( $weight ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<p class="sk-size-guide__tip"><strong><?php esc_html_e( 'Một người có thể mặc 2 size.', 'lukasports' ); ?></strong> <?php esc_html_e( 'Mặc ôm (body) chọn size nhỏ hơn, mặc thoải mái chọn size lớn hơn.', 'lukasports' ); ?></p>
	<p><?php esc_html_e( 'Chưa chắc chọn size nào? Nhắn Zalo/Messenger cho DALETIC — tư vấn size miễn phí.', 'lukasports' ); ?></p>
</div>

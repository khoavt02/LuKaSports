<?php
/**
 * Shared sizing table across the catalog — sizing is standardized
 * across DALETIC's product line, so one guide serves every product
 * rather than per-product data entry.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// From DALETIC's "Bảng size áo bóng đá thiết kế" (Oct 2026).
$rows = array(
	array( 'S', '1m55 – 1m60', '45 – 55kg' ),
	array( 'M', '1m61 – 1m67', '55 – 65kg' ),
	array( 'L', '1m68 – 1m75', '65 – 75kg' ),
	array( 'XL', '1m76 – 1m81', '75 – 85kg' ),
	array( 'XXL', '1m82 – 1m87', '85 – 95kg' ),
	array( '3XL', '1m88 – 1m93', 'Trên 95kg' ),
	array( '4XL', '1m88 – 1m93', 'Trên 110kg' ),
);
?>
<div class="sk-size-guide">
	<p><?php esc_html_e( 'Bảng size áo bóng đá thiết kế DALETIC. Chọn theo chiều cao và cân nặng của bạn.', 'lukasports' ); ?></p>
	<div class="sk-table-scroll">
		<table class="sk-size-guide__table">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Size', 'lukasports' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Chiều cao', 'lukasports' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Cân nặng', 'lukasports' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $rows as $row ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $row[0] ); ?></th>
						<?php foreach ( array_slice( $row, 1 ) as $cell ) : ?>
							<td><?php echo esc_html( $cell ); ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<p class="sk-size-guide__tip"><strong><?php esc_html_e( 'Áo bóng đá 1 người có thể mặc 2 size:', 'lukasports' ); ?></strong> <?php esc_html_e( 'mặc body thì chọn size bé, mặc thoải mái thì chọn size to.', 'lukasports' ); ?></p>
	<p><?php esc_html_e( 'Chưa chắc chọn size nào? Nhắn Zalo/Messenger cho DALETIC — tư vấn size miễn phí.', 'lukasports' ); ?></p>
</div>

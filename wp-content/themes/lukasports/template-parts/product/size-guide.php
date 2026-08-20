<?php
/**
 * Shared sizing table across the catalog — sizing is standardized
 * across LukaSports's product line, so one guide serves every product
 * rather than per-product data entry.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rows = array(
	array( 'S', '56-64', '160-168', '48-58' ),
	array( 'M', '64-72', '168-174', '58-68' ),
	array( 'L', '72-80', '174-180', '68-78' ),
	array( 'XL', '80-88', '178-184', '78-88' ),
);
?>
<div class="sk-size-guide">
	<p><?php esc_html_e( 'Áo có form thể thao (ôm nhẹ). Nếu bạn thích mặc rộng rãi, nên chọn size lớn hơn 1 mức so với bảng dưới.', 'lukasports' ); ?></p>
	<div class="sk-table-scroll">
		<table class="sk-size-guide__table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Size', 'lukasports' ); ?></th>
					<th><?php esc_html_e( 'Cân nặng (kg)', 'lukasports' ); ?></th>
					<th><?php esc_html_e( 'Chiều cao (cm)', 'lukasports' ); ?></th>
					<th><?php esc_html_e( 'Vòng ngực (cm)', 'lukasports' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $rows as $row ) : ?>
					<tr>
						<?php foreach ( $row as $cell ) : ?>
							<td><?php echo esc_html( $cell ); ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<p><?php esc_html_e( 'Chưa chắc chọn size nào? Nhắn Zalo/Messenger cho LukaSports — tư vấn size miễn phí.', 'lukasports' ); ?></p>
</div>

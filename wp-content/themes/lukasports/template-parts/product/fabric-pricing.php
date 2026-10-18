<?php
/**
 * Fabric picker + team-order discount table for the custom-design jerseys.
 * Picking a fabric (assets/js/product-page.js) updates the headline price,
 * the per-tier prices below and the product name sent to the consult form.
 * Data: lukasports_fabric_tiers() / lukasports_team_discounts().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="sk-fabric" data-sk-fabric aria-labelledby="sk-fabric-title">
	<h3 class="sk-fabric__title" id="sk-fabric-title"><?php esc_html_e( 'Chọn chất vải', 'lukasports' ); ?> <span><?php esc_html_e( 'giá theo bộ, từ cơ bản đến cao cấp', 'lukasports' ); ?></span></h3>

	<?php foreach ( lukasports_fabric_tiers() as $tier ) : ?>
		<div class="sk-fabric__tier">
			<span class="sk-fabric__tier-label"><?php echo esc_html( $tier['label'] ); ?></span>
			<div class="sk-fabric__options">
				<?php foreach ( $tier['items'] as list( $name, $price ) ) : ?>
					<label class="sk-fabric__option">
						<input type="radio" name="sk_fabric" value="<?php echo esc_attr( $name ); ?>" data-price="<?php echo esc_attr( $price ); ?>" />
						<span class="sk-fabric__name"><?php echo esc_html( $name ); ?></span>
						<span class="sk-fabric__price"><?php echo esc_html( number_format_i18n( $price ) ); ?>đ</span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach; ?>

	<div class="sk-fabric__team">
		<h4 class="sk-fabric__team-title"><?php esc_html_e( 'Ưu đãi đặt theo đội', 'lukasports' ); ?></h4>
		<table class="sk-fabric__table">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Số lượng', 'lukasports' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Giảm', 'lukasports' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Giá / bộ', 'lukasports' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( lukasports_team_discounts() as list( $min, $max, $percent ) ) : ?>
					<tr data-percent="<?php echo esc_attr( $percent ); ?>">
						<th scope="row"><?php echo esc_html( $max ? sprintf( /* translators: 1,2: bộ range */ __( '%1$d – %2$d bộ', 'lukasports' ), $min, $max ) : sprintf( /* translators: %d: min bộ */ __( 'Từ %d bộ', 'lukasports' ), $min ) ); ?></th>
						<td>−<?php echo esc_html( $percent ); ?>%</td>
						<td data-sk-tier-price><?php esc_html_e( 'Chọn chất vải', 'lukasports' ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p class="sk-fabric__note"><?php esc_html_e( 'Giá làm tròn đến nghìn đồng. Nhắn DALETIC để chốt chất vải và báo giá cuối cho đội của bạn.', 'lukasports' ); ?></p>
	</div>
</section>

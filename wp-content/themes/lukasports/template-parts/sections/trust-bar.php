<?php
/**
 * Plain stat numbers — placeholders until real order/review data
 * exists (no sales/CRM data to pull from yet; see README). Update the
 * values below directly once real numbers are available.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stats = array(
	array( 'num' => '10K+', 'label' => 'Khách hàng' ),
	array( 'num' => '1000+', 'label' => 'Mẫu thiết kế' ),
	array( 'num' => '50+', 'label' => 'Đội / CLB' ),
	array( 'num' => '4.9/5', 'label' => 'Đánh giá' ),
);
?>
<section class="sk-trust" aria-label="<?php esc_attr_e( 'DALETIC theo con số', 'lukasports' ); ?>">
	<ul class="sk-container sk-trust__list">
		<?php foreach ( $stats as $stat ) : ?>
			<li class="sk-trust__item">
				<span class="sk-trust__num"><?php echo esc_html( $stat['num'] ); ?></span>
				<span class="sk-trust__label"><?php echo esc_html( $stat['label'] ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</section>

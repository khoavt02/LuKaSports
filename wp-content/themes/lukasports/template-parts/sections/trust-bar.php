<?php
/**
 * Plain stat numbers — placeholders until real order/review data
 * exists (no sales/CRM data to pull from yet; see README). Update the
 * values below directly once real numbers are available.
 *
 * Standalone (About page) it renders its own band. The homepage
 * "DALETIC trên sân đấu" section (lookbook.php) passes
 * array( 'embedded' => true ) to get just the stat cards.
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

if ( ! empty( $args['embedded'] ) ) :
	?>
	<dl class="sk-stats">
		<?php foreach ( $stats as $stat ) : ?>
			<div class="sk-stats__item">
				<dt class="sk-stats__label"><?php echo esc_html( $stat['label'] ); ?></dt>
				<dd class="sk-stats__num"><?php echo esc_html( $stat['num'] ); ?></dd>
			</div>
		<?php endforeach; ?>
	</dl>
	<?php
	return;
endif;
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

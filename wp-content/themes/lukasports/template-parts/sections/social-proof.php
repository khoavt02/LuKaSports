<?php
/**
 * "Đánh giá / Khách hàng nói gì?" — deliberately empty by default (see
 * #3 in the Phase 1 brief: no fake reviews, sold counts, or customer
 * logos). Real testimonials get added by hooking
 * `lukasports_social_proof_items` (each item: quote, author — "Name —
 * Team" is split into name and team line). The whole section stays out
 * of the page until real testimonials exist.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = apply_filters( 'lukasports_social_proof_items', array() );

if ( empty( $items ) ) {
	return;
}
?>
<section class="sk-home-section sk-reviews" aria-labelledby="sk-reviews-title">
	<div class="sk-container">
		<div class="sk-section-head">
			<div>
				<p class="sk-section-head__eyebrow"><?php esc_html_e( 'Đánh giá', 'lukasports' ); ?></p>
				<h2 id="sk-reviews-title" class="sk-section-head__title"><?php esc_html_e( 'Khách hàng nói gì?', 'lukasports' ); ?></h2>
			</div>
		</div>
	</div>
	<div class="sk-reviews__scroller">
		<div class="sk-container sk-reviews__grid">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$parts   = array_map( 'trim', preg_split( '/\s+[—–-]\s+/u', (string) $item['author'], 2 ) );
				$name    = $parts[0];
				$team    = isset( $parts[1] ) ? $parts[1] : '';
				$words   = preg_split( '/\s+/u', $name );
				$initial = mb_strtoupper( mb_substr( (string) end( $words ), 0, 1 ) );
				?>
				<figure class="sk-review">
					<div class="sk-review__top">
						<svg class="sk-review__quote-mark" width="36" height="28" viewBox="0 0 36 28" aria-hidden="true"><path d="M0 28V16C0 7 5 1 14 0l1 5c-5 1-8 4-8 9h7v14zM21 28V16c0-9 5-15 14-16l1 5c-5 1-8 4-8 9h7v14z" fill="currentColor"/></svg>
						<span class="sk-review__stars" role="img" aria-label="<?php esc_attr_e( '5 sao', 'lukasports' ); ?>">★★★★★</span>
					</div>
					<blockquote class="sk-review__text"><?php echo esc_html( $item['quote'] ); ?></blockquote>
					<figcaption class="sk-review__author">
						<span class="sk-review__avatar" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
						<span>
							<span class="sk-review__name"><?php echo esc_html( $name ); ?></span>
							<?php if ( $team ) : ?>
								<span class="sk-review__team"><?php echo esc_html( $team ); ?></span>
							<?php endif; ?>
						</span>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>

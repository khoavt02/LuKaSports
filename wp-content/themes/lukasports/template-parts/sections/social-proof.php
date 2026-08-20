<?php
/**
 * Deliberately empty by default — see #3 in the Phase 1 brief: no fake
 * reviews, sold counts, or customer logos. Real testimonials get added
 * later by hooking `lukasports_social_proof_items` (e.g. once a review
 * or testimonial source exists). Rather than show a placeholder ("no
 * reviews yet") that reads as a broken/unfinished site, the whole
 * section stays out of the page until real testimonials exist —
 * an empty state here would be more visible than an absent section.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = apply_filters( 'lukasports_social_proof_items', array() );

if ( empty( $items ) ) {
	return;
}
?>
<section class="sk-section sk-social-proof">
	<div class="sk-container">
		<div class="sk-section-head">
			<h2 class="sk-section-head__title"><?php esc_html_e( 'Khách hàng nói gì?', 'lukasports' ); ?></h2>
		</div>

		<div class="sk-grid sk-grid--3">
			<?php foreach ( $items as $item ) : ?>
				<div class="sk-card sk-testimonial">
					<p class="sk-testimonial__quote">&ldquo;<?php echo esc_html( $item['quote'] ); ?>&rdquo;</p>
					<p class="sk-testimonial__author"><?php echo esc_html( $item['author'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

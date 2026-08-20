<?php
/**
 * Fallback template — required by WordPress for a valid theme. Every
 * real page type on this site is matched by a more specific template
 * (front-page.php, page.php, single.php, archive.php, woocommerce/*),
 * so this only ever renders for a request none of those catch.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="sk-section">
	<div class="sk-container">
		<?php if ( have_posts() ) : ?>
			<div class="sk-grid sk-grid--3">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/blog/post-card' );
				endwhile;
				?>
			</div>
			<div class="sk-pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<p><?php esc_html_e( 'Không có nội dung.', 'lukasports' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>

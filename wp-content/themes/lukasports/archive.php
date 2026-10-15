<?php
/**
 * Blog index, category, tag, date and author archives for post-type
 * "post". WooCommerce product archives use woocommerce/archive-product.php
 * instead — WordPress picks whichever is more specific automatically.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<?php
get_template_part(
	'template-parts/page-band',
	null,
	array(
		'title'   => wp_strip_all_tags( get_the_archive_title() ),
		'intro'   => wp_kses_post( get_the_archive_description() ),
		'compact' => true,
	)
);
?>
<section class="sk-section sk-blog-archive">
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
			<p><?php esc_html_e( 'Chưa có bài viết nào.', 'lukasports' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>

<?php
/**
 * Blog index (used for the page set as "Posts page" in Settings →
 * Reading — /blog/ in this site's URL structure). front-page.php
 * still governs the actual site root regardless of that setting.
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
		'title'   => __( 'Blog', 'lukasports' ),
		'accent'  => __( 'Kinh nghiệm sân cỏ', 'lukasports' ),
		'intro'   => '<p>' . esc_html__( 'Chọn size, chọn chất liệu, đặt đồng phục cho đội — mẹo thực tế từ DALETIC.', 'lukasports' ) . '</p>',
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

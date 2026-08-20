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
<section class="sk-section sk-blog-archive">
	<div class="sk-container">
		<header class="sk-blog-archive__header">
			<h1 class="sk-blog-archive__title"><?php the_archive_title(); ?></h1>
			<?php the_archive_description( '<div class="sk-blog-archive__desc">', '</div>' ); ?>
		</header>

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

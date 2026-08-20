<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 6,
		'no_found_rows'  => true,
		'ignore_sticky_posts' => true,
	)
);

if ( ! $posts->have_posts() ) {
	return;
}
?>
<section class="sk-section sk-blog-preview sk-section--surface">
	<div class="sk-container">
		<div class="sk-section-head">
			<h2 class="sk-section-head__title"><?php esc_html_e( 'Bài viết mới nhất', 'lukasports' ); ?></h2>
			<a class="sk-section-head__link" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Xem tất cả →', 'lukasports' ); ?></a>
		</div>
		<div class="sk-grid sk-grid--3">
			<?php
			while ( $posts->have_posts() ) :
				$posts->the_post();
				get_template_part( 'template-parts/blog/post-card' );
			endwhile;
			?>
		</div>
	</div>
</section>
<?php wp_reset_postdata(); ?>

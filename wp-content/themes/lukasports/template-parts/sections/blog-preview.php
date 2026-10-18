<?php
/**
 * "Blog / Bài viết mới nhất" — newest post as a large feature card,
 * the next four as compact rows in a panel beside it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 5,
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
	)
);

if ( ! $posts->have_posts() ) {
	return;
}

if ( ! function_exists( 'lukasports_home_post_tag' ) ) {
	/** Label for a post: first real category, else first tag, else ''. */
	function lukasports_home_post_tag( $post_id ) {
		$default = (int) get_option( 'default_category' );
		foreach ( (array) get_the_category( $post_id ) as $cat ) {
			if ( (int) $cat->term_id !== $default ) {
				return $cat->name;
			}
		}
		$tags = get_the_tags( $post_id );
		return ( $tags && ! is_wp_error( $tags ) ) ? $tags[0]->name : '';
	}
}

$blog_url = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/blog/' );
$index    = 0;
?>
<section class="sk-home-section sk-blog-home" aria-labelledby="sk-blog-home-title">
	<div class="sk-container">
		<div class="sk-section-head">
			<div>
				<p class="sk-section-head__eyebrow"><?php esc_html_e( 'Blog', 'lukasports' ); ?></p>
				<h2 id="sk-blog-home-title" class="sk-section-head__title"><?php esc_html_e( 'Bài viết mới nhất', 'lukasports' ); ?></h2>
			</div>
			<a class="sk-section-head__link" href="<?php echo esc_url( $blog_url ); ?>"><span class="sk-blog-home__link-long"><?php esc_html_e( 'Xem tất cả bài viết', 'lukasports' ); ?></span><span class="sk-blog-home__link-short"><?php esc_html_e( 'Tất cả', 'lukasports' ); ?></span></a>
		</div>
		<div class="sk-blog-home__layout">
			<?php
			while ( $posts->have_posts() ) :
				$posts->the_post();
				$tag   = lukasports_home_post_tag( get_the_ID() );
				$thumb = has_post_thumbnail() ? lukasports_attachment_image( get_post_thumbnail_id(), 0 === $index ? 'lukasports-card' : 'lukasports-thumb', 'lazy', '', '' ) : '';

				if ( 0 === $index ) :
					?>
					<article class="sk-blog-feature">
						<a class="sk-blog-feature__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<span class="sk-blog-feature__slab" aria-hidden="true"></span>
							<span class="sk-blog-feature__slab sk-blog-feature__slab--thin" aria-hidden="true"></span>
							<?php if ( $thumb ) : ?>
								<span class="sk-blog-feature__photo"><?php echo $thumb; // phpcs:ignore ?></span>
							<?php endif; ?>
							<?php if ( $tag ) : ?>
								<span class="sk-blog-feature__tag"><span><?php echo esc_html( $tag ); ?></span></span>
							<?php endif; ?>
						</a>
						<div class="sk-blog-feature__body">
							<time class="sk-blog-feature__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
								<?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?>
							</time>
							<h3 class="sk-blog-feature__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="sk-blog-feature__excerpt"><?php echo esc_html( lukasports_excerpt( get_the_excerpt(), 30 ) ); ?></p>
							<a class="sk-blog-feature__more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Đọc tiếp', 'lukasports' ); ?> <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
						</div>
					</article>
					<div class="sk-blog-home__list">
					<?php
				else :
					?>
					<article class="sk-blog-row">
						<a class="sk-blog-row__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php echo $thumb; // phpcs:ignore ?></a>
						<div class="sk-blog-row__body">
							<?php if ( $tag ) : ?>
								<span class="sk-blog-row__tag"><?php echo esc_html( $tag ); ?></span>
							<?php endif; ?>
							<h3 class="sk-blog-row__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<time class="sk-blog-row__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></time>
						</div>
					</article>
					<?php
				endif;
				++$index;
			endwhile;
			?>
			</div>
		</div>
	</div>
</section>
<?php wp_reset_postdata(); ?>

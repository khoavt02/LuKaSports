<?php
/**
 * Blog card (blog index, archives, search, "Bài viết khác"). Must be
 * called inside the loop / after setup_postdata(). Styles: blog.css
 * `.sk-blog-card`.
 *
 * @var array $args {
 *     @type string $layout 'card' (default), 'feature' (large, with excerpt) or 'row' (compact list item).
 *     @type string $tag    Heading tag, default h3 (h2 for the feature card).
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/helpers.php';

$layout = isset( $args['layout'] ) && in_array( $args['layout'], array( 'card', 'feature', 'row' ), true ) ? $args['layout'] : 'card';
$tag    = isset( $args['tag'] ) ? $args['tag'] : ( 'feature' === $layout ? 'h2' : 'h3' );
$tag    = in_array( $tag, array( 'h2', 'h3', 'h4' ), true ) ? $tag : 'h3';
$topic  = lukasports_post_topic();
$link   = get_permalink();
$size   = 'row' === $layout ? 'lukasports-thumb' : 'lukasports-card';
?>
<article class="sk-blog-card sk-blog-card--<?php echo esc_attr( $layout ); ?>">
	<a class="sk-blog-card__media" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
		<span class="sk-blog-card__slash" aria-hidden="true"></span>
		<span class="sk-blog-card__slash sk-blog-card__slash--thin" aria-hidden="true"></span>
		<span class="sk-blog-card__shot">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php echo lukasports_attachment_image( get_post_thumbnail_id(), $size, 'lazy', '', '' ); // phpcs:ignore ?>
			<?php else : ?>
				<img src="<?php echo esc_url( lukasports_image_url( 'hero-jerseys.webp' ) ); ?>" alt="" loading="lazy" />
			<?php endif; ?>
		</span>
		<?php if ( 'row' !== $layout ) : ?>
			<span class="sk-blog-card__chip"><span><?php echo esc_html( $topic ); ?></span></span>
		<?php endif; ?>
	</a>
	<div class="sk-blog-card__body">
		<span class="sk-blog-card__topic"><?php echo esc_html( $topic ); ?></span>
		<time class="sk-blog-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
			<?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?>
		</time>
		<<?php echo $tag; // phpcs:ignore ?> class="sk-blog-card__title"><a href="<?php echo esc_url( $link ); ?>"><?php the_title(); ?></a></<?php echo $tag; // phpcs:ignore ?>>
		<?php if ( 'feature' === $layout && has_excerpt() ) : ?>
			<p class="sk-blog-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
		<?php if ( 'row' !== $layout ) : ?>
			<a class="sk-blog-card__more" href="<?php echo esc_url( $link ); ?>" aria-hidden="true" tabindex="-1">
				<?php esc_html_e( 'Đọc tiếp', 'lukasports' ); ?>
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
			</a>
		<?php endif; ?>
	</div>
</article>

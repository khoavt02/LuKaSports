<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<a class="sk-card sk-post-card" href="<?php the_permalink(); ?>">
	<div class="sk-post-card__media">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php echo lukasports_attachment_image( get_post_thumbnail_id(), 'lukasports-card', 'lazy', '', get_the_title() ); // phpcs:ignore ?>
		<?php else : ?>
			<img src="<?php echo esc_url( LUKASPORTS_THEME_URI . '/assets/images/placeholder-product.svg' ); ?>" alt="" loading="lazy" />
		<?php endif; ?>
	</div>
	<div class="sk-post-card__body">
		<time class="sk-post-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		<h3 class="sk-post-card__title"><?php the_title(); ?></h3>
	</div>
</a>

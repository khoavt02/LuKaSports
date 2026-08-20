<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<article class="sk-section sk-post">
	<div class="sk-container sk-post__container">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<nav class="sk-breadcrumb">
				<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Blog', 'lukasports' ); ?></a> / <?php the_title(); ?>
			</nav>

			<h1 class="sk-post__title"><?php the_title(); ?></h1>
			<p class="sk-post__meta">
				<?php echo esc_html( get_the_date() ); ?>
				<?php if ( get_the_author() ) : ?>
					· <?php the_author(); ?>
				<?php endif; ?>
			</p>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="sk-post__thumb"><?php the_post_thumbnail( 'lukasports-hero' ); ?></div>
			<?php endif; ?>

			<div class="sk-post__content"><?php the_content(); ?></div>
		<?php endwhile; ?>
	</div>
</article>
<?php get_footer(); ?>

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<article class="sk-section sk-page">
	<div class="sk-container sk-page__container">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<h1 class="sk-page__title"><?php the_title(); ?></h1>
			<div class="sk-page__content"><?php the_content(); ?></div>
			<?php
		endwhile;
		?>
	</div>
</article>
<?php get_footer(); ?>

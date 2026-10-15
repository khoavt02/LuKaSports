<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<?php get_template_part( 'template-parts/page-band', null, array( 'title' => get_the_title(), 'compact' => true ) ); ?>
<article class="sk-section sk-page">
	<div class="sk-container sk-page__container">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<div class="sk-page__content"><?php the_content(); ?></div>
			<?php
		endwhile;
		?>
	</div>
</article>
<?php get_footer(); ?>

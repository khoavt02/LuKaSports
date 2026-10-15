<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	get_template_part(
		'template-parts/page-band',
		null,
		array(
			'title'   => get_the_title(),
			'intro'   => '<p class="sk-post__meta">' . esc_html( get_the_date() ) . '</p>',
			'compact' => true,
		)
	);
	?>
	<article class="sk-section sk-post">
		<div class="sk-container sk-post__container">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="sk-post__thumb"><?php the_post_thumbnail( 'lukasports-hero' ); ?></div>
			<?php endif; ?>

			<div class="sk-post__content"><?php the_content(); ?></div>
		</div>
	</article>
	<?php
endwhile;

get_footer();

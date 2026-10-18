<?php
/**
 * Default page template. The policy pages (đổi trả / giao hàng / bảo
 * mật — kept as separate URLs) get the policy layout: a sidebar linking
 * between them, a help box and the numbered-section typography.
 * Styles: pages.css (`.sk-page`, `.sk-policy`).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$policy_slugs = apply_filters( 'lukasports_policy_page_slugs', array( 'chinh-sach-doi-tra', 'chinh-sach-giao-hang', 'chinh-sach-bao-mat' ) );
$policy_pages = array();
foreach ( $policy_slugs as $slug ) {
	$found = get_page_by_path( $slug );
	if ( $found && 'publish' === $found->post_status ) {
		$policy_pages[] = $found;
	}
}
$is_policy = in_array( get_queried_object_id(), wp_list_pluck( $policy_pages, 'ID' ), true );

if ( $is_policy ) :
	get_template_part(
		'template-parts/page-band',
		null,
		array(
			'title'   => get_the_title(),
			'dot'     => true,
			'compact' => true,
			'intro'   => '<p>' . esc_html__( 'Áp dụng cho mọi đơn hàng tại DALETIC.', 'lukasports' ) . '</p>',
		)
	);
	?>
	<div class="sk-policy">
		<div class="sk-container sk-policy__layout">
			<?php get_template_part( 'template-parts/policy-nav', null, array( 'pages' => $policy_pages ) ); ?>
			<article class="sk-policy__content sk-prose sk-prose--numbered">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</article>
		</div>
	</div>
	<?php
else :
	get_template_part(
		'template-parts/page-band',
		null,
		array(
			'title'   => get_the_title(),
			'dot'     => true,
			'compact' => true,
		)
	);
	?>
	<div class="sk-page">
		<div class="sk-container">
			<article class="sk-page__content sk-prose">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</article>
		</div>
	</div>
	<?php
endif;

get_footer();

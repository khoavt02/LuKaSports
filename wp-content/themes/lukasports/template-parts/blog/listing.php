<?php
/**
 * Blog listing body shared by home.php (blog index) and archive.php
 * (category / tag / date archives): topic pills (real categories, plain
 * links), an optional large feature card for the newest post, the card
 * grid, pagination and the red CTA band. Styles: blog.css.
 *
 * @var array $args {
 *     @type bool $feature Show the first post of page 1 as a feature card.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/helpers.php';

$show_feature = ! empty( $args['feature'] ) && ! is_paged();
$topics       = lukasports_blog_topics();
$posts_page   = (int) get_option( 'page_for_posts' );
$blog_url     = $posts_page ? get_permalink( $posts_page ) : home_url( '/' );
$current_cat  = is_category() ? get_queried_object_id() : 0;
$contact      = lukasports_get_contact_info();
?>
<div class="sk-blog">
	<?php if ( $topics ) : ?>
		<nav class="sk-blog__topics" aria-label="<?php esc_attr_e( 'Chủ đề', 'lukasports' ); ?>">
			<div class="sk-container">
				<div class="sk-pill-links sk-pill-links--scroll">
					<a class="sk-pill-link<?php echo ( is_home() ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( $blog_url ); ?>"<?php echo is_home() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Tất cả', 'lukasports' ); ?></a>
					<?php foreach ( $topics as $topic ) : ?>
						<?php $active = $current_cat === (int) $topic->term_id; ?>
						<a class="sk-pill-link<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $topic ) ); ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $topic->name ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		</nav>
	<?php endif; ?>

	<section class="sk-container sk-blog__posts" aria-label="<?php esc_attr_e( 'Bài viết', 'lukasports' ); ?>">
		<?php if ( have_posts() ) : ?>
			<?php
			if ( $show_feature ) {
				the_post();
				get_template_part( 'template-parts/blog/blog-card', null, array( 'layout' => 'feature' ) );
			}
			?>
			<?php if ( have_posts() ) : ?>
				<div class="sk-blog__grid">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/blog/blog-card' );
					endwhile;
					?>
				</div>
			<?php endif; ?>
			<?php
			the_posts_pagination(
				array(
					'class'     => 'sk-pagination',
					'prev_text' => '←<span class="sk-visually-hidden"> ' . esc_html__( 'Trang trước', 'lukasports' ) . '</span>',
					'next_text' => '<span class="sk-visually-hidden">' . esc_html__( 'Trang sau', 'lukasports' ) . ' </span>→',
				)
			);
			?>
		<?php else : ?>
			<p class="sk-blog__empty"><?php esc_html_e( 'Chưa có bài viết nào.', 'lukasports' ); ?></p>
		<?php endif; ?>
	</section>
</div>

<?php
get_template_part(
	'template-parts/content-cta',
	null,
	array(
		'title'   => __( 'Cần tư vấn size cho cả đội?', 'lukasports' ),
		'text'    => __( 'Gửi chiều cao, cân nặng từng thành viên – DALETIC chọn size giúp bạn.', 'lukasports' ),
		'buttons' => array(
			array( __( 'Nhắn Zalo', 'lukasports' ), $contact['zalo_url'], 'white', 'target="_blank" rel="noopener"' ),
			array( __( 'Gửi yêu cầu', 'lukasports' ), home_url( '/lien-he/' ), 'outline', 'data-lukasports-cta="consult" data-source="blog_cta"' ),
		),
	)
);

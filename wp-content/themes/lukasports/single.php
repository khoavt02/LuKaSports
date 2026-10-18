<?php
/**
 * Single blog post: dark header (topic chip, title, author/date, cover
 * image), lead + content typography, tags and share buttons, a consult
 * box and "Bài viết khác" sidebar, then "Có thể bạn cần" products.
 * Styles: blog.css (`.sk-post-hero`, `.sk-post`).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/template-parts/blog/helpers.php';

get_header();

$contact    = lukasports_get_contact_info();
$posts_page = (int) get_option( 'page_for_posts' );
$blog_url   = $posts_page ? get_permalink( $posts_page ) : home_url( '/' );
$blog_name  = $posts_page ? get_the_title( $posts_page ) : __( 'Blog', 'lukasports' );
$shop_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' );

while ( have_posts() ) :
	the_post();
	$post_id    = get_the_ID();
	$topic_term = lukasports_post_topic_term();
	$topic      = lukasports_post_topic();
	$permalink  = get_permalink();
	$tags       = get_the_tags();
	?>
	<section class="sk-post-hero" aria-labelledby="sk-post-title">
		<span class="sk-post-hero__slash" aria-hidden="true"></span>
		<span class="sk-post-hero__slash sk-post-hero__slash--thin" aria-hidden="true"></span>
		<div class="sk-container sk-post-hero__inner">
			<nav class="sk-breadcrumb sk-post-hero__crumbs" aria-label="<?php esc_attr_e( 'Đường dẫn', 'lukasports' ); ?>">
				<a class="sk-hide-mobile" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Trang chủ', 'lukasports' ); ?></a>
				<span class="sk-hide-mobile" aria-hidden="true">›</span>
				<a href="<?php echo esc_url( $blog_url ); ?>"><?php echo esc_html( $blog_name ); ?></a>
				<span aria-hidden="true">›</span>
				<span aria-current="page"><?php echo esc_html( wp_trim_words( get_the_title(), 7, '…' ) ); ?></span>
			</nav>
			<div class="sk-post-hero__grid">
				<div class="sk-post-hero__text">
					<?php if ( $topic_term ) : ?>
						<a class="sk-eyebrow sk-post-hero__chip" href="<?php echo esc_url( get_term_link( $topic_term ) ); ?>"><span class="sk-eyebrow__tag"><?php echo esc_html( $topic ); ?></span></a>
					<?php else : ?>
						<span class="sk-eyebrow sk-post-hero__chip"><span class="sk-eyebrow__tag"><?php echo esc_html( $topic ); ?></span></span>
					<?php endif; ?>
					<h1 class="sk-post-hero__title sk-display" id="sk-post-title"><?php the_title(); ?></h1>
					<div class="sk-post-hero__meta">
						<span>DALETIC</span>
						<span aria-hidden="true">·</span>
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></time>
					</div>
				</div>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="sk-post-hero__media">
						<?php echo lukasports_attachment_image( get_post_thumbnail_id(), 'lukasports-card', 'eager', '', '' ); // phpcs:ignore ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<div class="sk-post<?php echo has_post_thumbnail() ? ' sk-post--has-media' : ''; ?>">
		<div class="sk-container sk-post__layout">
			<article class="sk-post__article">
				<?php if ( has_excerpt() ) : ?>
					<p class="sk-post__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>

				<div class="sk-post__content sk-prose sk-prose--numbered">
					<?php the_content(); ?>
				</div>

				<footer class="sk-post__footer">
					<?php if ( $tags ) : ?>
						<div class="sk-post__tags">
							<?php foreach ( $tags as $post_tag ) : ?>
								<a class="sk-post__tag" href="<?php echo esc_url( get_term_link( $post_tag ) ); ?>">#<?php echo esc_html( $post_tag->slug ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<div class="sk-post__share">
						<span class="sk-hide-mobile"><?php esc_html_e( 'Chia sẻ:', 'lukasports' ); ?></span>
						<span class="sk-show-mobile"><?php esc_html_e( 'Chia sẻ bài viết', 'lukasports' ); ?></span>
						<span class="sk-post__share-buttons">
							<a class="sk-post__share-btn" href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $permalink ) ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chia sẻ Facebook', 'lukasports' ); ?>">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 8.5V6.8c0-.8.5-1.3 1.3-1.3H17V2.3h-2.6C11.7 2.3 10.4 4 10.4 6.6v1.9H8v3.3h2.4V22H14V11.8h2.7l.4-3.3z"/></svg>
							</a>
							<button type="button" class="sk-post__share-btn" data-sk-copy-link="<?php echo esc_url( $permalink ); ?>" aria-label="<?php esc_attr_e( 'Sao chép liên kết', 'lukasports' ); ?>">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/></svg>
								<span class="sk-post__copied" role="status" aria-live="polite"></span>
							</button>
						</span>
					</div>
				</footer>
			</article>

			<aside class="sk-post__aside">
				<div class="sk-post__consult">
					<span class="sk-post__consult-slash" aria-hidden="true"></span>
					<h2 class="sk-post__consult-title sk-display"><?php esc_html_e( 'Cần tư vấn size?', 'lukasports' ); ?></h2>
					<p class="sk-post__consult-text"><?php esc_html_e( 'Gửi chiều cao, cân nặng – DALETIC chọn size giúp bạn.', 'lukasports' ); ?></p>
					<div class="sk-post__consult-actions">
						<a class="sk-btn sk-btn--primary sk-btn--md" href="<?php echo esc_url( $contact['zalo_url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Nhắn Zalo', 'lukasports' ); ?></a>
						<a class="sk-btn sk-btn--outline-light sk-btn--md" href="<?php echo esc_url( $contact['messenger_url'] ); ?>" target="_blank" rel="noopener">Messenger</a>
					</div>
				</div>

				<?php
				$others = get_posts(
					array(
						'post_type'           => 'post',
						'posts_per_page'      => 4,
						'post__not_in'        => array( $post_id ),
						'ignore_sticky_posts' => true,
					)
				);
				if ( $others ) :
					?>
					<div class="sk-post__others">
						<h2 class="sk-post__others-title"><?php esc_html_e( 'Bài viết khác', 'lukasports' ); ?></h2>
						<ul class="sk-post__others-list">
							<?php
							global $post;
							foreach ( $others as $post ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride
								setup_postdata( $post );
								?>
								<li><?php get_template_part( 'template-parts/blog/blog-card', null, array( 'layout' => 'row' ) ); ?></li>
								<?php
							endforeach;
							wp_reset_postdata();
							?>
						</ul>
					</div>
				<?php endif; ?>
			</aside>
		</div>
	</div>

	<?php
	$related = function_exists( 'wc_get_products' ) ? wc_get_products(
		array(
			'status'   => 'publish',
			'limit'    => 4,
			'orderby'  => 'date',
			'order'    => 'DESC',
			'featured' => true,
		)
	) : array();
	if ( function_exists( 'wc_get_products' ) && count( $related ) < 4 ) {
		$related = array_merge(
			$related,
			wc_get_products(
				array(
					'status'  => 'publish',
					'limit'   => 4 - count( $related ),
					'orderby' => 'date',
					'order'   => 'DESC',
					'exclude' => array_map( fn( $p ) => $p->get_id(), $related ),
				)
			)
		);
	}
	if ( $related ) :
		?>
		<section class="sk-post-related" aria-labelledby="sk-related-title">
			<div class="sk-container">
				<div class="sk-section-head">
					<h2 class="sk-section-head__title" id="sk-related-title"><?php esc_html_e( 'Có thể bạn cần', 'lukasports' ); ?></h2>
					<a class="sk-section-head__link sk-hide-mobile" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Xem sản phẩm', 'lukasports' ); ?></a>
				</div>
				<div class="sk-grid sk-post-related__grid">
					<?php
					global $product;
					foreach ( $related as $product ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride
						get_template_part( 'template-parts/product/product-card' );
					}
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
endwhile;
?>
<script>
document.addEventListener( 'click', function ( e ) {
	var btn = e.target.closest( '[data-sk-copy-link]' );
	if ( ! btn ) { return; }
	var url = btn.getAttribute( 'data-sk-copy-link' );
	var note = btn.querySelector( '.sk-post__copied' );
	var done = function () {
		btn.classList.add( 'is-copied' );
		if ( note ) { note.textContent = <?php echo wp_json_encode( __( 'Đã sao chép liên kết', 'lukasports' ) ); ?>; }
		setTimeout( function () { btn.classList.remove( 'is-copied' ); if ( note ) { note.textContent = ''; } }, 2000 );
	};
	if ( navigator.clipboard && window.isSecureContext ) {
		navigator.clipboard.writeText( url ).then( done );
	} else {
		var field = document.createElement( 'textarea' );
		field.value = url;
		field.setAttribute( 'readonly', '' );
		field.style.position = 'fixed';
		field.style.opacity = '0';
		document.body.appendChild( field );
		field.select();
		try { document.execCommand( 'copy' ); done(); } catch ( err ) {}
		document.body.removeChild( field );
	}
} );
</script>
<?php
get_footer();

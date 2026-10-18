<?php
/**
 * Template Name: DALETIC — Bộ sưu tập
 *
 * Index of every `product_collection` term that has products: one wide
 * card per collection (product photos on a dark stage + number, count,
 * name, description, CTA), then the category shortcuts and a red CTA.
 * Each card links to the collection archive (/bo-suu-tap/{slug}/).
 * Styles: pages.css (Collections).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$contact     = lukasports_get_contact_info();
$shop_url    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' );
$collections = taxonomy_exists( 'product_collection' ) ? get_terms(
	array(
		'taxonomy'   => 'product_collection',
		'hide_empty' => true,
	)
) : array();
if ( is_wp_error( $collections ) ) {
	$collections = array();
}

$categories = taxonomy_exists( 'product_cat' ) ? get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'parent'     => 0,
		'hide_empty' => true,
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
	)
) : array();
if ( is_wp_error( $categories ) ) {
	$categories = array();
}

if ( ! function_exists( 'lukasports_collection_images' ) ) {
	/**
	 * Up to $limit image attachment IDs for a collection card: the term's
	 * own thumbnail first, then its newest products' photos.
	 */
	function lukasports_collection_images( $term, $limit = 3 ) {
		$ids   = array();
		$thumb = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
		if ( $thumb ) {
			$ids[] = $thumb;
		}
		if ( count( $ids ) < $limit && function_exists( 'wc_get_products' ) ) {
			$products = wc_get_products(
				array(
					'status'    => 'publish',
					'limit'     => $limit,
					'return'    => 'ids',
					'tax_query' => array( // phpcs:ignore
						array(
							'taxonomy' => 'product_collection',
							'field'    => 'term_id',
							'terms'    => $term->term_id,
						),
					),
				)
			);
			foreach ( $products as $product_id ) {
				$image_id = (int) get_post_thumbnail_id( $product_id );
				if ( $image_id && ! in_array( $image_id, $ids, true ) ) {
					$ids[] = $image_id;
				}
			}
		}
		return array_slice( $ids, 0, $limit );
	}
}

// Page content (if any) becomes the intro of the dark band.
the_post();
$sk_intro = get_the_content() ? apply_filters( 'the_content', get_the_content() ) : '';
rewind_posts();
get_template_part(
	'template-parts/page-band',
	null,
	array(
		'title' => get_the_title(),
		'dot'   => true,
		'intro' => wp_kses_post( $sk_intro ),
	)
);
?>
<div class="sk-collections">
	<div class="sk-container">
		<?php if ( $collections ) : ?>
			<div class="sk-collections__list">
				<?php foreach ( $collections as $i => $collection ) : ?>
					<?php
					$link   = get_term_link( $collection );
					$images = lukasports_collection_images( $collection );
					?>
					<article class="sk-collection-card sk-collection-card--<?php echo esc_attr( $i % 3 ); ?><?php echo 1 === $i % 2 ? ' sk-collection-card--reverse' : ''; ?>">
						<a class="sk-collection-card__media sk-collection-card__media--<?php echo esc_attr( count( $images ) ); ?>" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
							<span class="sk-collection-card__slash" aria-hidden="true"></span>
							<span class="sk-collection-card__slash sk-collection-card__slash--thin" aria-hidden="true"></span>
							<?php foreach ( $images as $n => $image_id ) : ?>
								<span class="sk-collection-card__shot sk-collection-card__shot--<?php echo esc_attr( $n + 1 ); ?>">
									<?php echo lukasports_attachment_image( $image_id, 'lukasports-card', 'lazy', '', '' ); // phpcs:ignore ?>
								</span>
							<?php endforeach; ?>
						</a>
						<div class="sk-collection-card__body">
							<div class="sk-collection-card__meta">
								<span class="sk-collection-card__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
								<span class="sk-collection-card__count">
									<?php
									/* translators: %d: number of products */
									echo esc_html( sprintf( _n( '%d sản phẩm', '%d sản phẩm', $collection->count, 'lukasports' ), $collection->count ) );
									?>
								</span>
							</div>
							<h2 class="sk-collection-card__title sk-display"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $collection->name ); ?></a></h2>
							<?php if ( $collection->description ) : ?>
								<p class="sk-collection-card__desc"><?php echo esc_html( wp_strip_all_tags( $collection->description ) ); ?></p>
							<?php endif; ?>
							<a class="sk-btn sk-btn--primary sk-btn--md sk-collection-card__cta" href="<?php echo esc_url( $link ); ?>">
								<?php esc_html_e( 'Xem bộ sưu tập', 'lukasports' ); ?>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
							</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="sk-collections__empty"><?php esc_html_e( 'Chưa có bộ sưu tập nào.', 'lukasports' ); ?></p>
		<?php endif; ?>

		<section class="sk-collections__cats" aria-labelledby="sk-cats-title">
			<h2 class="sk-collections__cats-title sk-display" id="sk-cats-title">
				<span class="sk-hide-mobile"><?php esc_html_e( 'Hoặc xem theo danh mục', 'lukasports' ); ?></span>
				<span class="sk-show-mobile"><?php esc_html_e( 'Xem theo danh mục', 'lukasports' ); ?></span>
			</h2>
			<div class="sk-pill-links">
				<?php foreach ( $categories as $category ) : ?>
					<a class="sk-pill-link" href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
				<?php endforeach; ?>
				<a class="sk-pill-link is-active" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Tất cả sản phẩm →', 'lukasports' ); ?></a>
			</div>
		</section>
	</div>
</div>

<?php
get_template_part(
	'template-parts/content-cta',
	null,
	array(
		'title'   => __( 'Chưa thấy mẫu ưng ý?', 'lukasports' ),
		'text'    => __( 'Gửi ý tưởng, DALETIC dựng mockup riêng cho đội bạn trước khi sản xuất.', 'lukasports' ),
		'buttons' => array(
			array( __( 'Tư vấn thiết kế', 'lukasports' ), home_url( '/lien-he/' ), 'white', 'data-lukasports-cta="consult" data-source="collections_cta"' ),
			array( __( 'Gửi mẫu qua Zalo', 'lukasports' ), $contact['zalo_url'], 'outline', 'target="_blank" rel="noopener"' ),
		),
	)
);
?>

<?php get_footer(); ?>

<?php
/**
 * Template Name: DALETIC — Bộ sưu tập
 *
 * Index of every `product_collection` term that has products. Each
 * card links to the collection archive (/bo-suu-tap/{slug}/), which
 * WooCommerce renders with woocommerce/archive-product.php like any
 * other product taxonomy. Card image falls back to the newest product
 * in the collection until an admin gives the term its own thumbnail.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$collections = taxonomy_exists( 'product_collection' ) ? get_terms(
	array(
		'taxonomy'   => 'product_collection',
		'hide_empty' => true,
	)
) : array();

if ( is_wp_error( $collections ) ) {
	$collections = array();
}

function lukasports_collection_cover( $term ) {
	$thumbnail_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );

	if ( ! $thumbnail_id && function_exists( 'wc_get_products' ) ) {
		$products = wc_get_products(
			array(
				'status'    => 'publish',
				'limit'     => 1,
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
		$thumbnail_id = $products ? (int) get_post_thumbnail_id( $products[0] ) : 0;
	}

	if ( $thumbnail_id ) {
		return lukasports_attachment_image( $thumbnail_id, 'lukasports-card', 'lazy', '', $term->name );
	}

	return '<img src="' . esc_url( LUKASPORTS_THEME_URI . '/assets/images/placeholder-product.svg' ) . '" alt="" loading="lazy" />';
}
?>

<section class="sk-section sk-collections">
	<div class="sk-container">
		<nav class="sk-breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Trang chủ', 'lukasports' ); ?></a> / <?php the_title(); ?>
		</nav>

		<header class="sk-shop__header">
			<h1 class="sk-shop__title"><?php the_title(); ?></h1>
			<?php
			while ( have_posts() ) :
				the_post();
				if ( get_the_content() ) :
					?>
					<div class="sk-shop__intro"><?php the_content(); ?></div>
					<?php
				endif;
			endwhile;
			?>
		</header>

		<?php if ( $collections ) : ?>
			<div class="sk-collections__grid">
				<?php foreach ( $collections as $collection ) : ?>
					<a class="sk-collection-card" href="<?php echo esc_url( get_term_link( $collection ) ); ?>">
						<div class="sk-collection-card__media"><?php echo lukasports_collection_cover( $collection ); // phpcs:ignore ?></div>
						<div class="sk-collection-card__body">
							<h2 class="sk-collection-card__title"><?php echo esc_html( $collection->name ); ?></h2>
							<?php if ( $collection->description ) : ?>
								<p class="sk-collection-card__desc"><?php echo esc_html( lukasports_excerpt( $collection->description, 20 ) ); ?></p>
							<?php endif; ?>
							<span class="sk-collection-card__meta">
								<?php
								/* translators: %d: number of products */
								echo esc_html( sprintf( _n( '%d sản phẩm', '%d sản phẩm', $collection->count, 'lukasports' ), $collection->count ) );
								?>
								<span aria-hidden="true">→</span>
							</span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="woocommerce-info"><?php esc_html_e( 'Chưa có bộ sưu tập nào.', 'lukasports' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>

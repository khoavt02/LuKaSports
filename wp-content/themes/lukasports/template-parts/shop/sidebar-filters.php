<?php
/**
 * Product listing sidebar: categories (with counts), price range,
 * collections. Every filter is a plain link or GET form, so results
 * stay shareable and work without JS; assets/js/shop.js only adds the
 * two-handle price slider and the mobile drawer.
 *
 * @var array $args { active_cat?: int } term_id of the current category.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_cat = isset( $args['active_cat'] ) ? (int) $args['active_cat'] : 0;
$shop_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' );

$categories = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'exclude'    => array( get_option( 'default_product_cat', 0 ) ),
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);
$categories = is_wp_error( $categories ) ? array() : $categories;

$collections = taxonomy_exists( 'product_collection' ) ? get_terms( array( 'taxonomy' => 'product_collection', 'hide_empty' => true ) ) : array();
$collections = is_wp_error( $collections ) ? array() : $collections;
$current     = is_tax( 'product_collection' ) ? get_queried_object_id() : 0;

list( $price_floor, $price_ceil ) = lukasports_catalog_price_range();
$min_price = isset( $_GET['min_price'] ) ? max( $price_floor, (int) $_GET['min_price'] ) : $price_floor; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$max_price = isset( $_GET['max_price'] ) ? min( $price_ceil, (int) $_GET['max_price'] ) : $price_ceil; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$step      = 10000;
?>
<aside class="sk-filters" id="sk-filters" aria-label="<?php esc_attr_e( 'Bộ lọc sản phẩm', 'lukasports' ); ?>">
	<div class="sk-filters__head">
		<span class="sk-filters__head-title"><?php esc_html_e( 'Bộ lọc', 'lukasports' ); ?></span>
		<button type="button" class="sk-filters__close" data-sk-filters-close>
			<span class="sk-visually-hidden"><?php esc_html_e( 'Đóng bộ lọc', 'lukasports' ); ?></span>
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 4l12 12M16 4L4 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
		</button>
	</div>

	<?php // Phones: the quick pills live here instead of the toolbar. ?>
	<div class="sk-filters__group sk-filters__group--quick">
		<?php get_template_part( 'template-parts/shop/quick-pills' ); ?>
	</div>

	<section class="sk-filters__group sk-filters__group--cats">
		<h2 class="sk-filters__title"><?php esc_html_e( 'Danh mục', 'lukasports' ); ?></h2>
		<ul class="sk-filters__cats">
			<li>
				<a class="sk-filters__cat<?php echo 0 === $active_cat && ! $current ? ' is-active' : ''; ?>" href="<?php echo esc_url( $shop_url ); ?>"<?php echo 0 === $active_cat && ! $current && is_shop() ? ' aria-current="page"' : ''; ?>>
					<span class="sk-filters__cat-name"><?php esc_html_e( 'Tất cả sản phẩm', 'lukasports' ); ?></span>
				</a>
			</li>
			<?php foreach ( $categories as $term ) : ?>
				<?php $is_active = $active_cat === $term->term_id; ?>
				<li>
					<a class="sk-filters__cat<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/' . $term->slug . '/' ) ); ?>"<?php echo $is_active ? ' aria-current="page"' : ''; ?>>
						<span class="sk-filters__cat-name"><?php echo esc_html( $term->name ); ?></span>
						<span class="sk-filters__count">(<?php echo esc_html( $term->count ); ?>)</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>

	<?php if ( $price_ceil > $price_floor ) : ?>
		<section class="sk-filters__group">
			<h2 class="sk-filters__title"><?php esc_html_e( 'Khoảng giá', 'lukasports' ); ?></h2>
			<form class="sk-price-filter" id="sk-price-filter" method="get" data-sk-price-filter>
				<?php
				// Keep sorting / quick filter / search when applying a price range.
				foreach ( array( 'orderby', 'loai', 's', 'post_type' ) as $keep ) :
					if ( isset( $_GET[ $keep ] ) && '' !== $_GET[ $keep ] ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						?>
						<input type="hidden" name="<?php echo esc_attr( $keep ); ?>" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET[ $keep ] ) ) ); // phpcs:ignore ?>" />
						<?php
					endif;
				endforeach;
				?>
				<div class="sk-price-filter__slider">
					<div class="sk-price-filter__track"><span class="sk-price-filter__fill" data-sk-price-fill></span></div>
					<input type="range" name="min_price" min="<?php echo esc_attr( $price_floor ); ?>" max="<?php echo esc_attr( $price_ceil ); ?>" step="<?php echo esc_attr( $step ); ?>" value="<?php echo esc_attr( $min_price ); ?>" aria-label="<?php esc_attr_e( 'Giá thấp nhất', 'lukasports' ); ?>" data-sk-price-min />
					<input type="range" name="max_price" min="<?php echo esc_attr( $price_floor ); ?>" max="<?php echo esc_attr( $price_ceil ); ?>" step="<?php echo esc_attr( $step ); ?>" value="<?php echo esc_attr( $max_price ); ?>" aria-label="<?php esc_attr_e( 'Giá cao nhất', 'lukasports' ); ?>" data-sk-price-max />
				</div>
				<?php // Typed values (assets/js/shop.js keeps them in sync with the slider). ?>
				<div class="sk-price-filter__fields">
					<label class="sk-price-filter__field"><?php esc_html_e( 'Từ', 'lukasports' ); ?>
						<input type="text" inputmode="numeric" autocomplete="off" value="<?php echo esc_attr( number_format_i18n( $min_price ) . 'đ' ); ?>" data-sk-price-min-input />
					</label>
					<label class="sk-price-filter__field"><?php esc_html_e( 'Đến', 'lukasports' ); ?>
						<input type="text" inputmode="numeric" autocomplete="off" value="<?php echo esc_attr( number_format_i18n( $max_price ) . 'đ' ); ?>" data-sk-price-max-input />
					</label>
				</div>
				<button type="submit" class="sk-btn sk-btn--dark sk-btn--sm sk-btn--block sk-price-filter__submit"><?php esc_html_e( 'Áp dụng', 'lukasports' ); ?></button>
			</form>
		</section>
	<?php endif; ?>

	<?php if ( $collections ) : ?>
		<section class="sk-filters__group">
			<h2 class="sk-filters__title"><?php esc_html_e( 'Bộ sưu tập', 'lukasports' ); ?></h2>
			<ul class="sk-filters__links">
				<?php foreach ( $collections as $term ) : ?>
					<?php $on = $current === $term->term_id; ?>
					<li>
						<a class="sk-filters__link<?php echo $on ? ' is-active' : ''; ?>" href="<?php echo esc_url( $on ? $shop_url : get_term_link( $term ) ); ?>"<?php echo $on ? ' aria-current="page"' : ''; ?>>
							<span class="sk-filters__check" aria-hidden="true"></span>
							<span class="sk-filters__cat-name"><?php echo esc_html( $term->name ); ?></span>
							<span class="sk-filters__count">(<?php echo esc_html( $term->count ); ?>)</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>

	<?php // Phones: drawer actions, like the design's "Xóa lọc / Áp dụng". ?>
	<div class="sk-filters__actions">
		<a class="sk-filters__reset" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Xóa lọc', 'lukasports' ); ?></a>
		<?php if ( $price_ceil > $price_floor ) : ?>
			<button type="submit" form="sk-price-filter" class="sk-filters__apply"><?php esc_html_e( 'Áp dụng', 'lukasports' ); ?></button>
		<?php else : ?>
			<button type="button" class="sk-filters__apply" data-sk-filters-close><?php esc_html_e( 'Áp dụng', 'lukasports' ); ?></button>
		<?php endif; ?>
	</div>
</aside>

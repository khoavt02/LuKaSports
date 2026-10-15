<?php
/**
 * Product listing sidebar: categories (icon + count), price range,
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

// Simple line icons per sport; anything unknown gets the jersey.
$icons = array(
	'ao-bong-da'          => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5l3.2 2.3-1.2 3.7h-4l-1.2-3.7zM12 3.5v4M20.2 9.6l-5 .2M17 19l-3-5.5M7 19l3-5.5M3.8 9.6l5 .2"/>',
	'ao-bong-da-thiet-ke' => '<path d="M4 20l1-4L16.5 4.5a2.1 2.1 0 013 3L8 19l-4 1z"/><path d="M14.5 6.5l3 3"/>',
	'ao-team'             => '<circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.4"/><path d="M3.5 19c.6-3 2.8-5 5.5-5s4.9 2 5.5 5M14.5 14.2c.8-.4 1.6-.6 2.5-.6 2.2 0 3.9 1.6 4.4 4.4"/>',
	'ao-bong-chuyen'      => '<circle cx="12" cy="12" r="8.5"/><path d="M12 3.5c0 5 2.5 8.5 7.5 11M5 17c4-2.5 6.5-6 7-9.5M4.5 9c4 .5 8 2.5 10.5 6.5"/>',
	'ao-bong-ro'          => '<circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17M12 3.5v17M6 6c2.5 2.3 2.5 9.7 0 12M18 6c-2.5 2.3-2.5 9.7 0 12"/>',
	'ao-cau-long'         => '<path d="M14 4l6 6-5 2-3-3zM12 9l-7.5 7.5a2 2 0 002.8 2.8L14.8 12"/>',
	'ao-pickerball'       => '<rect x="5" y="3" width="10" height="12" rx="5"/><path d="M10 15v5"/><circle cx="18" cy="18" r="2.5"/>',
	'phu-kien'            => '<path d="M6 8h12l-1 12H7zM9 8V6a3 3 0 016 0v2"/>',
);
$jersey = '<path d="M8 3l4 2 4-2 5 3-2 4-2-1v12H7V9l-2 1-2-4z"/>';

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

	<details class="sk-filters__group" open>
		<summary class="sk-filters__title"><?php esc_html_e( 'Danh mục', 'lukasports' ); ?></summary>
		<ul class="sk-filters__cats">
			<li>
				<a class="sk-filters__cat<?php echo 0 === $active_cat && ! $current ? ' is-active' : ''; ?>" href="<?php echo esc_url( $shop_url ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true"><?php echo $jersey; // phpcs:ignore ?></svg>
					<span class="sk-filters__cat-name"><?php esc_html_e( 'Tất cả sản phẩm', 'lukasports' ); ?></span>
				</a>
			</li>
			<?php foreach ( $categories as $term ) : ?>
				<?php $is_active = $active_cat === $term->term_id; ?>
				<li>
					<a class="sk-filters__cat<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/' . $term->slug . '/' ) ); ?>"<?php echo $is_active ? ' aria-current="page"' : ''; ?>>
						<svg viewBox="0 0 24 24" aria-hidden="true"><?php echo $icons[ $term->slug ] ?? $jersey; // phpcs:ignore ?></svg>
						<span class="sk-filters__cat-name"><?php echo esc_html( $term->name ); ?></span>
						<span class="sk-filters__count">(<?php echo esc_html( $term->count ); ?>)</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</details>

	<?php if ( $price_ceil > $price_floor ) : ?>
		<details class="sk-filters__group" open>
			<summary class="sk-filters__title"><?php esc_html_e( 'Khoảng giá', 'lukasports' ); ?></summary>
			<form class="sk-price-filter" method="get" data-sk-price-filter>
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
				<div class="sk-price-filter__values">
					<span data-sk-price-min-label><?php echo wp_kses_post( wc_price( $min_price ) ); ?></span>
					<span data-sk-price-max-label><?php echo wp_kses_post( wc_price( $max_price ) ); ?></span>
				</div>
				<button type="submit" class="sk-btn sk-btn--dark sk-btn--sm sk-btn--block"><?php esc_html_e( 'Áp dụng', 'lukasports' ); ?></button>
			</form>
		</details>
	<?php endif; ?>

	<?php if ( $collections ) : ?>
		<details class="sk-filters__group" open>
			<summary class="sk-filters__title"><?php esc_html_e( 'Bộ sưu tập', 'lukasports' ); ?></summary>
			<ul class="sk-filters__links">
				<?php foreach ( $collections as $term ) : ?>
					<li>
						<a class="<?php echo $current === $term->term_id ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
							<?php echo esc_html( $term->name ); ?> <span class="sk-filters__count">(<?php echo esc_html( $term->count ); ?>)</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</details>
	<?php endif; ?>
</aside>

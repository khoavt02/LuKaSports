<?php
/**
 * Category chips above every product listing (shop, category archive,
 * category landing page). Each chip is a plain link to the category's
 * clean URL — filtering is a real navigation, so it's shareable,
 * crawlable and keeps sorting/pagination working without any JS.
 *
 * @var array $args { active?: int } term_id of the current category, 0 for "all".
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! taxonomy_exists( 'product_cat' ) ) {
	return;
}

$terms = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'exclude'    => array( get_option( 'default_product_cat', 0 ) ),
		'orderby'    => 'name',
	)
);

if ( empty( $terms ) || is_wp_error( $terms ) ) {
	return;
}

$active   = isset( $args['active'] ) ? (int) $args['active'] : 0;
$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' );
?>
<nav class="sk-filter" aria-label="<?php esc_attr_e( 'Lọc theo danh mục', 'lukasports' ); ?>">
	<ul class="sk-filter__list">
		<li>
			<a class="sk-filter__chip<?php echo 0 === $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( $shop_url ); ?>"<?php echo 0 === $active ? ' aria-current="page"' : ''; ?>>
				<?php esc_html_e( 'Tất cả', 'lukasports' ); ?>
			</a>
		</li>
		<?php foreach ( $terms as $term ) : ?>
			<?php $is_active = $active === $term->term_id; ?>
			<li>
				<a class="sk-filter__chip<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/' . $term->slug . '/' ) ); ?>"<?php echo $is_active ? ' aria-current="page"' : ''; ?>>
					<?php echo esc_html( $term->name ); ?>
					<span class="sk-filter__count"><?php echo esc_html( $term->count ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php
/**
 * 404 — dark band + search + newest products. Styles: blog.css (`.sk-404`).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' );

ob_start();
?>
<a class="sk-btn sk-btn--primary sk-btn--lg" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Về trang chủ', 'lukasports' ); ?></a>
<a class="sk-btn sk-btn--outline-light sk-btn--lg" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Xem sản phẩm', 'lukasports' ); ?></a>
<?php
get_template_part(
	'template-parts/page-band',
	null,
	array(
		'label'   => __( 'Lỗi 404', 'lukasports' ),
		'title'   => __( 'Không tìm thấy trang', 'lukasports' ),
		'dot'     => true,
		'intro'   => '<p>' . esc_html__( 'Trang này có thể đã bị xoá hoặc đổi địa chỉ. Thử tìm sản phẩm bên dưới.', 'lukasports' ) . '</p>',
		'actions' => ob_get_clean(),
	)
);
?>
<section class="sk-404">
	<div class="sk-container">
		<form class="sk-search-form sk-404__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="sk-visually-hidden" for="sk-404-search"><?php esc_html_e( 'Tìm kiếm', 'lukasports' ); ?></label>
			<input type="search" class="sk-field" id="sk-404-search" name="s" placeholder="<?php esc_attr_e( 'Tìm áo bóng đá, áo team, phụ kiện…', 'lukasports' ); ?>" />
			<button type="submit" class="sk-btn sk-btn--primary sk-btn--md"><?php esc_html_e( 'Tìm', 'lukasports' ); ?></button>
		</form>
	</div>
</section>

<?php get_template_part( 'template-parts/sections/best-sellers' ); ?>

<?php get_footer(); ?>

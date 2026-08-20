<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="sk-section sk-404">
	<div class="sk-container sk-404__inner">
		<h1 class="sk-404__title"><?php esc_html_e( 'Không tìm thấy trang bạn cần', 'lukasports' ); ?></h1>
		<p class="sk-404__subtitle"><?php esc_html_e( 'Trang này có thể đã bị xoá hoặc đổi địa chỉ. Thử tìm sản phẩm bên dưới.', 'lukasports' ); ?></p>

		<form class="sk-search-panel__form sk-404__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="search" class="sk-field" name="s" placeholder="<?php esc_attr_e( 'Tìm áo bóng đá, áo team, phụ kiện…', 'lukasports' ); ?>" />
			<button type="submit" class="sk-btn sk-btn--dark"><?php esc_html_e( 'Tìm', 'lukasports' ); ?></button>
		</form>
	</div>
</section>

<?php get_template_part( 'template-parts/sections/best-sellers' ); ?>

<?php get_footer(); ?>

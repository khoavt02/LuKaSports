<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact = lukasports_get_contact_info();
?>
<div class="sk-footer__grid">
	<div class="sk-footer__col sk-footer__col--brand">
		<a class="sk-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
				<img class="sk-brand__logo" src="<?php echo esc_url( LUKASPORTS_THEME_URI . '/assets/images/brand/logo-light.svg' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="210" height="40" loading="lazy" />
			<?php endif; ?>
		</a>
		<p class="sk-footer__tagline"><?php esc_html_e( 'Đồng phục và áo đấu thể thao — chất liệu thi đấu thật, thiết kế theo yêu cầu.', 'lukasports' ); ?></p>
	</div>

	<div class="sk-footer__col">
		<h3 class="sk-footer__heading"><?php esc_html_e( 'Sản phẩm', 'lukasports' ); ?></h3>
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/ao-bong-da/' ) ); ?>"><?php esc_html_e( 'Áo bóng đá', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/ao-bong-da-thiet-ke/' ) ); ?>"><?php esc_html_e( 'Áo bóng đá thiết kế', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/ao-team/' ) ); ?>"><?php esc_html_e( 'Áo team', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/phu-kien/' ) ); ?>"><?php esc_html_e( 'Phụ kiện', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' ) ); ?>"><?php esc_html_e( 'Tất cả sản phẩm', 'lukasports' ); ?></a></li>
		</ul>
	</div>

	<div class="sk-footer__col">
		<h3 class="sk-footer__heading"><?php esc_html_e( 'Hỗ trợ', 'lukasports' ); ?></h3>
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/chinh-sach-doi-tra/' ) ); ?>"><?php esc_html_e( 'Chính sách đổi trả', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/chinh-sach-giao-hang/' ) ); ?>"><?php esc_html_e( 'Chính sách giao hàng', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/chinh-sach-bao-mat/' ) ); ?>"><?php esc_html_e( 'Chính sách bảo mật', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/lien-he/' ) ); ?>"><?php esc_html_e( 'Liên hệ', 'lukasports' ); ?></a></li>
		</ul>
	</div>

	<div class="sk-footer__col">
		<h3 class="sk-footer__heading"><?php esc_html_e( 'Kết nối', 'lukasports' ); ?></h3>
		<ul class="sk-footer__contact">
			<li><a href="tel:<?php echo esc_attr( $contact['hotline_tel'] ); ?>"><?php echo esc_html( $contact['hotline_display'] ); ?></a></li>
			<li><a href="<?php echo esc_url( $contact['facebook_url'] ); ?>" target="_blank" rel="noopener">Facebook</a></li>
			<li><a href="<?php echo esc_url( $contact['zalo_url'] ); ?>" target="_blank" rel="noopener">Zalo</a></li>
			<li><?php echo esc_html( $contact['address'] ); ?></li>
		</ul>
	</div>
</div>

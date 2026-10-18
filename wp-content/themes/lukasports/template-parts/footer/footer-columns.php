<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact = lukasports_get_contact_info();
?>
<div class="sk-footer__grid">
	<div class="sk-footer__col sk-footer__col--brand">
		<?php if ( has_custom_logo() ) : ?>
			<div class="sk-brand sk-brand--footer"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a class="sk-brand sk-brand--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img class="sk-brand__logo custom-logo" src="<?php echo esc_url( lukasports_image_url( 'brand/logo.png' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="900" height="227" loading="lazy" />
			</a>
		<?php endif; ?>
		<p class="sk-footer__tagline"><?php esc_html_e( 'Đồng phục và áo đấu thể thao — chất liệu thi đấu thật, thiết kế theo yêu cầu.', 'lukasports' ); ?></p>
		<div class="sk-footer__social">
			<a href="<?php echo esc_url( $contact['facebook_url'] ); ?>" target="_blank" rel="noopener" aria-label="Facebook"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 8.5V6.8c0-.8.5-1.3 1.3-1.3H17V2.3h-2.6C11.7 2.3 10.4 4 10.4 6.6v1.9H8v3.3h2.4V22H14V11.8h2.7l.4-3.3z"/></svg></a>
			<a href="<?php echo esc_url( $contact['messenger_url'] ); ?>" target="_blank" rel="noopener" aria-label="Messenger"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" aria-hidden="true"><path d="M12 3C7 3 3 6.7 3 11.3c0 2.6 1.3 4.9 3.3 6.4V21l3-1.7c.9.3 1.8.4 2.7.4 5 0 9-3.7 9-8.4S17 3 12 3z"/><path d="M7.5 13.5l3-3 2.5 2 3.5-3.5" stroke-linecap="round"/></svg></a>
			<a href="<?php echo esc_url( $contact['zalo_url'] ); ?>" target="_blank" rel="noopener" aria-label="Zalo" class="sk-footer__social-zalo">Zalo</a>
		</div>
	</div>

	<div class="sk-footer__col">
		<h2 class="sk-footer__heading"><?php esc_html_e( 'Sản phẩm', 'lukasports' ); ?></h2>
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/ao-bong-da/' ) ); ?>"><?php esc_html_e( 'Áo bóng đá', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/ao-bong-da-thiet-ke/' ) ); ?>"><?php esc_html_e( 'Áo bóng đá thiết kế', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/ao-team/' ) ); ?>"><?php esc_html_e( 'Áo team', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/phu-kien/' ) ); ?>"><?php esc_html_e( 'Phụ kiện', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/san-pham/' ) ); ?>"><?php esc_html_e( 'Tất cả sản phẩm', 'lukasports' ); ?></a></li>
		</ul>
	</div>

	<div class="sk-footer__col">
		<h2 class="sk-footer__heading"><?php esc_html_e( 'Hỗ trợ', 'lukasports' ); ?></h2>
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/chinh-sach-doi-tra/' ) ); ?>"><?php esc_html_e( 'Chính sách đổi trả', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/chinh-sach-giao-hang/' ) ); ?>"><?php esc_html_e( 'Chính sách giao hàng', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/chinh-sach-bao-mat/' ) ); ?>"><?php esc_html_e( 'Chính sách bảo mật', 'lukasports' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/lien-he/' ) ); ?>"><?php esc_html_e( 'Liên hệ', 'lukasports' ); ?></a></li>
		</ul>
	</div>

	<div class="sk-footer__col">
		<h2 class="sk-footer__heading"><?php esc_html_e( 'Kết nối', 'lukasports' ); ?></h2>
		<div class="sk-footer__contact">
			<a class="sk-footer__hotline" href="tel:<?php echo esc_attr( $contact['hotline_tel'] ); ?>"><?php echo esc_html( $contact['hotline_display'] ); ?></a>
			<span class="sk-footer__hours"><?php esc_html_e( 'Hotline 8:00 – 21:00 hằng ngày', 'lukasports' ); ?></span>
			<?php if ( ! empty( $contact['facebook_url'] ) ) : ?>
				<a class="sk-footer__fanpage" href="<?php echo esc_url( $contact['facebook_url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Fanpage Facebook DALETIC →', 'lukasports' ); ?></a>
			<?php endif; ?>
			<?php if ( $contact['address'] ) : ?>
				<span class="sk-footer__address"><?php echo esc_html( sprintf( /* translators: %s: store address */ __( 'Showroom: %s', 'lukasports' ), $contact['address'] ) ); ?></span>
			<?php endif; ?>
			<?php get_template_part( 'template-parts/zalo-connect', null, array( 'variant' => 'compact' ) ); ?>
			<a class="sk-btn sk-btn--primary sk-btn--sm sk-footer__cta" href="<?php echo esc_url( $contact['messenger_url'] ); ?>" target="_blank" rel="noopener" data-lukasports-cta="consult" data-source="footer"><?php echo esc_html( $contact['primary_cta_text'] ); ?></a>
		</div>
	</div>
</div>

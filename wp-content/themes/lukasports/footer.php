<?php
/**
 * Site footer + mobile floating contact bar + consultation modal.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sk_contact = lukasports_get_contact_info();
?>
</main>

<footer class="sk-footer">
	<div class="sk-container">
		<?php get_template_part( 'template-parts/footer/footer-columns' ); ?>

		<div class="sk-footer__bottom">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Bản quyền thuộc về DALETIC.', 'lukasports' ); ?></p>
		</div>
	</div>
</footer>

<div class="sk-floating-contact" aria-label="<?php esc_attr_e( 'Liên hệ nhanh', 'lukasports' ); ?>">
	<button type="button" class="sk-floating-contact__toggle" id="sk-floating-toggle" aria-expanded="false" aria-controls="sk-floating-menu">
		<svg width="24" height="24" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M10 2.5c-4.14 0-7.5 2.86-7.5 6.4 0 2 1.08 3.77 2.78 4.96-.1.86-.42 1.86-.96 2.7a.4.4 0 00.46.6c1.36-.4 2.53-1.02 3.33-1.53.6.1 1.23.16 1.89.16 4.14 0 7.5-2.87 7.5-6.4S14.14 2.5 10 2.5z" fill="currentColor"/><circle cx="6.8" cy="9" r=".9" fill="#fff"/><circle cx="10" cy="9" r=".9" fill="#fff"/><circle cx="13.2" cy="9" r=".9" fill="#fff"/></svg>
		<span class="sk-visually-hidden"><?php esc_html_e( 'Mở liên hệ nhanh', 'lukasports' ); ?></span>
	</button>
	<div class="sk-floating-contact__menu" id="sk-floating-menu">
		<a class="sk-floating-contact__btn sk-floating-contact__btn--messenger" href="<?php echo esc_url( $sk_contact['messenger_url'] ); ?>" target="_blank" rel="noopener">
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M10 2C5.6 2 2 5.3 2 9.4c0 2.3 1.1 4.3 2.9 5.7L4.6 18l2.9-1.6c.8.2 1.6.3 2.5.3 4.4 0 8-3.3 8-7.3S14.4 2 10 2z" fill="currentColor"/></svg>
			<span><?php esc_html_e( 'Messenger', 'lukasports' ); ?></span>
		</a>
		<a class="sk-floating-contact__btn sk-floating-contact__btn--zalo" href="<?php echo esc_url( $sk_contact['zalo_url'] ); ?>" target="_blank" rel="noopener">
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="2" y="2" width="16" height="16" rx="4" stroke="currentColor" stroke-width="1.6"/><path d="M6.5 7h5.5l-5.5 6h5.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
			<span><?php esc_html_e( 'Zalo', 'lukasports' ); ?></span>
		</a>
		<a class="sk-floating-contact__btn sk-floating-contact__btn--call" href="tel:<?php echo esc_attr( $sk_contact['hotline_tel'] ); ?>">
			<svg width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 3h3l1.5 4L6.7 8.6a10 10 0 005.7 5.7L14 12.5l4 1.5v3a1.5 1.5 0 01-1.6 1.5A15 15 0 013 5.6 1.5 1.5 0 014.5 4z" fill="currentColor"/></svg>
			<span><?php esc_html_e( 'Gọi ngay', 'lukasports' ); ?></span>
		</a>
	</div>
</div>

<?php get_template_part( 'template-parts/forms/consultation-modal' ); ?>

<?php wp_footer(); ?>
</body>
</html>

<?php
/**
 * Policy pages sidebar: links between the separate policy pages (active
 * = current page) and the "Cần hỗ trợ thêm?" help box. On phones the
 * links become a horizontal pill bar under the band and the help box
 * drops below the article (pages.css `.sk-policy`).
 *
 * @var array $args {
 *     @type WP_Post[] $pages Policy pages in display order.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$policy_pages = $args['pages'] ?? array();
$contact      = lukasports_get_contact_info();
$current_id   = get_queried_object_id();
?>
<aside class="sk-policy__aside">
	<nav class="sk-policy__tabs" aria-label="<?php esc_attr_e( 'Các chính sách', 'lukasports' ); ?>">
		<?php foreach ( $policy_pages as $policy_page ) : ?>
			<?php $is_current = (int) $policy_page->ID === (int) $current_id; ?>
			<a class="sk-policy__tab<?php echo $is_current ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_permalink( $policy_page ) ); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?>>
				<span><?php echo esc_html( get_the_title( $policy_page ) ); ?></span>
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
			</a>
		<?php endforeach; ?>
	</nav>
	<div class="sk-policy__help">
		<div>
			<div class="sk-policy__help-label"><?php esc_html_e( 'Cần hỗ trợ thêm?', 'lukasports' ); ?></div>
			<a class="sk-policy__help-phone" href="tel:<?php echo esc_attr( $contact['hotline_tel'] ); ?>"><?php echo esc_html( $contact['hotline_display'] ); ?></a>
		</div>
		<a class="sk-policy__help-link" href="<?php echo esc_url( home_url( '/lien-he/' ) ); ?>">
			<span class="sk-hide-mobile"><?php esc_html_e( 'Trang liên hệ →', 'lukasports' ); ?></span>
			<span class="sk-show-mobile"><?php esc_html_e( 'Liên hệ', 'lukasports' ); ?></span>
		</a>
	</div>
</aside>

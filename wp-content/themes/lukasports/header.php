<?php
/**
 * Site header: logo, primary nav, search, contact shortcuts, mobile menu.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sk_contact = lukasports_get_contact_info();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
<link rel="profile" href="https://gmpg.org/xfn/11" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="sk-skip-link" href="#sk-main"><?php esc_html_e( 'Bỏ qua để xem nội dung chính', 'lukasports' ); ?></a>

<header class="sk-header" id="sk-header">
	<div class="sk-container sk-header__bar">
		<button class="sk-header__burger" type="button" aria-expanded="false" aria-controls="sk-mobile-nav" data-sk-nav-toggle>
			<span class="sk-visually-hidden"><?php esc_html_e( 'Mở menu', 'lukasports' ); ?></span>
			<span class="sk-header__burger-line"></span>
			<span class="sk-header__burger-line"></span>
			<span class="sk-header__burger-line"></span>
		</button>

		<?php get_template_part( 'template-parts/header/site-branding' ); ?>

		<nav class="sk-nav sk-nav--desktop" aria-label="<?php esc_attr_e( 'Menu chính', 'lukasports' ); ?>">
			<?php get_template_part( 'template-parts/header/primary-menu' ); ?>
		</nav>

		<div class="sk-header__actions">
			<button class="sk-header__icon-btn" type="button" aria-expanded="false" aria-controls="sk-search-panel" data-sk-search-toggle>
				<span class="sk-visually-hidden"><?php esc_html_e( 'Tìm kiếm', 'lukasports' ); ?></span>
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="8.5" cy="8.5" r="6" stroke="currentColor" stroke-width="1.6"/><path d="M13.3 13.3L18 18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
			</button>

			<a class="sk-btn sk-btn--primary sk-header__cta" href="<?php echo esc_url( $sk_contact['messenger_url'] ); ?>" target="_blank" rel="noopener" data-lukasports-cta="consult" data-source="header">
				<?php echo esc_html( $sk_contact['primary_cta_text'] ); ?> <span aria-hidden="true">→</span>
			</a>
		</div>
	</div>

	<div class="sk-search-panel" id="sk-search-panel" hidden>
		<form class="sk-container sk-search-panel__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="search" class="sk-field" name="s" placeholder="<?php esc_attr_e( 'Tìm áo bóng đá, áo team, phụ kiện…', 'lukasports' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" />
			<button type="submit" class="sk-btn sk-btn--dark"><?php esc_html_e( 'Tìm', 'lukasports' ); ?></button>
		</form>
	</div>
</header>

<nav class="sk-mobile-nav" id="sk-mobile-nav" aria-label="<?php esc_attr_e( 'Menu di động', 'lukasports' ); ?>" hidden>
	<?php get_template_part( 'template-parts/header/primary-menu' ); ?>
	<div class="sk-mobile-nav__contact">
		<a class="sk-btn sk-btn--outline sk-btn--block" href="tel:<?php echo esc_attr( $sk_contact['hotline_tel'] ); ?>">
			<?php echo esc_html( $sk_contact['hotline_display'] ); ?>
		</a>
	</div>
</nav>

<main id="sk-main">

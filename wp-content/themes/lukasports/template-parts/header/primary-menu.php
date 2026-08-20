<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( has_nav_menu( 'primary' ) ) {
	wp_nav_menu(
		array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'sk-nav__list',
			'depth'          => 2,
		)
	);
} else {
	lukasports_primary_menu_fallback();
}

<?php
/**
 * DALETIC theme bootstrap.
 *
 * This file only wires up modules — no business logic lives here.
 * Business logic (leads, inquiries, design requests) belongs in the
 * lukasports-core plugin, never in the theme.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LUKASPORTS_THEME_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'LUKASPORTS_THEME_DIR', get_template_directory() );
define( 'LUKASPORTS_THEME_URI', get_template_directory_uri() );

$lukasports_modules = array(
	'inc/helpers.php',
	'inc/setup.php',
	'inc/enqueue.php',
	'inc/hooks.php',
	'inc/seo.php',
	'inc/performance.php',
	'inc/security.php',
	'inc/tracking.php',
	'inc/dev-social-proof.php',
	'inc/dev-reviews.php',
);

foreach ( $lukasports_modules as $lukasports_module ) {
	require_once LUKASPORTS_THEME_DIR . '/' . $lukasports_module;
}
unset( $lukasports_modules, $lukasports_module );

<?php
/**
 * Plugin Name: LukaSports Core
 * Plugin URI: https://example.com/lukasports
 * Description: Business logic for the LukaSports storefront — product data, lead capture and consultation tracking, contact settings, admin tools. Never edits WordPress or WooCommerce core.
 * Version: 0.2.0
 * Requires at least: 6.4
 * Requires PHP: 8.2
 * Author: LukaSports Team
 * Text Domain: lukasports-core
 *
 * This file only bootstraps modules — see includes/. Design
 * Request / advanced CRM / payment / shipping remain out of scope —
 * see README "Recommended next phase" for what's intentionally
 * postponed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LUKASPORTS_CORE_VERSION', '0.2.0' );
define( 'LUKASPORTS_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'LUKASPORTS_CORE_URL', plugin_dir_url( __FILE__ ) );
define( 'LUKASPORTS_CORE_FILE', __FILE__ );

function lukasports_core_load() {
	$modules = array(
		'includes/helpers.php',
		'includes/taxonomies.php',
		'includes/meta-fields.php',
		'includes/post-types.php',
		'includes/shortcodes.php',
		'includes/leads.php',
		'includes/ajax.php',
		'includes/settings.php',
		'includes/admin.php',
	);

	foreach ( $modules as $module ) {
		require_once LUKASPORTS_CORE_DIR . $module;
	}
}
add_action( 'plugins_loaded', 'lukasports_core_load', 5 );

/**
 * WooCommerce is a hard dependency (product attributes/meta only make
 * sense with it active) — fail loudly in admin rather than fataling.
 */
function lukasports_core_check_dependencies() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-error"><p>' .
					esc_html__( 'LukaSports Core requires WooCommerce to be installed and active.', 'lukasports-core' ) .
					'</p></div>';
			}
		);
	}
}
add_action( 'admin_init', 'lukasports_core_check_dependencies' );

register_activation_hook( __FILE__, 'lukasports_core_on_activate' );
function lukasports_core_on_activate() {
	require_once LUKASPORTS_CORE_DIR . 'includes/taxonomies.php';
	require_once LUKASPORTS_CORE_DIR . 'includes/leads.php';
	lukasports_core_register_default_attributes();
	lukasports_core_create_leads_table();
	flush_rewrite_rules();
}

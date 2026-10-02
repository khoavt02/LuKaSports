<?php
/**
 * Baseline hardening. None of this touches WordPress core files —
 * it only disables risky defaults and hides version fingerprinting.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_action( 'wp_head', 'wp_generator' ); // also removed in performance.php; harmless if duplicated.

add_filter( 'the_generator', '__return_empty_string' );

function lukasports_remove_pingback_header( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'lukasports_remove_pingback_header' );

/**
 * XML-RPC is not used by this site and is a common brute-force vector.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Block direct access to any template file if somehow requested
 * outside the WordPress loading process (defense in depth — every
 * template file in this theme also opens with the ABSPATH guard).
 */
function lukasports_block_direct_file_access() {
	if ( ! defined( 'ABSPATH' ) ) {
		http_response_code( 403 );
		exit;
	}
}

/**
 * The public REST users endpoint lists every author's login slug — the
 * same leak as /?author=1. Only logged-in users (the block editor) need it.
 */
function lukasports_hide_rest_users( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
}
add_filter( 'rest_endpoints', 'lukasports_hide_rest_users' );

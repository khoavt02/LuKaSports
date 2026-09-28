<?php
/**
 * Prepended to every PHP request (see forwarded-host.ini).
 *
 * Behind a reverse proxy — GitHub Codespaces port forwarding, a
 * Cloudflare tunnel — the request reaches Apache with the proxy's
 * internal Host, while the public hostname only arrives in
 * X-Forwarded-Host. WordPress builds every URL (and, with the dynamic
 * WP_HOME set by bin/setup.sh, its home URL) from HTTP_HOST, so copy
 * the public host over before WordPress boots. HTTPS detection is
 * already handled by the official image's wp-config.php via
 * X-Forwarded-Proto.
 *
 * Dev/demo only: this trusts the header from any client, which is
 * fine for a throwaway demo stack but not for a production server.
 */

if ( ! empty( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) {
	$lukasports_forwarded = trim( explode( ',', $_SERVER['HTTP_X_FORWARDED_HOST'] )[0] );
	if ( preg_match( '/^[A-Za-z0-9.-]+(:\d+)?$/', $lukasports_forwarded ) ) {
		$_SERVER['HTTP_HOST'] = $lukasports_forwarded;
	}
	unset( $lukasports_forwarded );
}

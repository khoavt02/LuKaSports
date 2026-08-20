<?php
/**
 * Small, dependency-free helpers shared by other modules in this plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_core_is_woocommerce_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * Strips spaces/dashes/dots and validates against common Vietnamese
 * mobile/landline number shapes. Not exhaustive — good enough to
 * reject obvious junk without blocking real customers.
 */
function lukasports_core_normalize_phone( $phone ) {
	return preg_replace( '/[\s.\-()]/', '', (string) $phone );
}

function lukasports_core_is_valid_vn_phone( $phone ) {
	$phone = lukasports_core_normalize_phone( $phone );
	return (bool) preg_match( '/^(\+84|0)(3|5|7|8|9)[0-9]{8}$/', $phone );
}

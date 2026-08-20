<?php
/**
 * Public AJAX endpoint for the consultation form (Quick Inquiry).
 * No customer account, no checkout — see Part 5 of the Phase 2 brief.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LUKASPORTS_CORE_LEAD_NONCE_ACTION', 'lukasports_submit_lead' );
define( 'LUKASPORTS_CORE_ATTRIBUTION_COOKIE', 'lukasports_attribution' );

function lukasports_core_handle_submit_lead() {
	check_ajax_referer( LUKASPORTS_CORE_LEAD_NONCE_ACTION, 'nonce' );

	$name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';

	$errors = array();
	if ( '' === trim( $name ) ) {
		$errors['name'] = __( 'Vui lòng nhập tên của bạn.', 'lukasports-core' );
	}
	if ( ! lukasports_core_is_valid_vn_phone( $phone ) ) {
		$errors['phone'] = __( 'Số điện thoại chưa đúng, vui lòng kiểm tra lại.', 'lukasports-core' );
	}

	if ( $errors ) {
		wp_send_json_error( array( 'errors' => $errors ), 400 );
	}

	$product_id   = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$product_name = isset( $_POST['product_name'] ) ? sanitize_text_field( wp_unslash( $_POST['product_name'] ) ) : '';

	if ( $product_id && 'product' !== get_post_type( $product_id ) ) {
		$product_id = 0;
	}
	if ( $product_id && '' === $product_name ) {
		$product_name = get_the_title( $product_id );
	}

	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$source  = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : 'website';

	$attribution = lukasports_core_read_attribution_cookie();

	$lead_id = lukasports_core_create_lead(
		array(
			'name'         => $name,
			'phone'        => lukasports_core_normalize_phone( $phone ),
			'product_id'   => $product_id ?: null,
			'product_name' => $product_name ?: null,
			'message'      => $message ?: null,
			'source'       => $source ?: 'website',
			'utm_source'   => $attribution['utm_source'],
			'utm_medium'   => $attribution['utm_medium'],
			'utm_campaign' => $attribution['utm_campaign'],
			'utm_content'  => $attribution['utm_content'],
			'utm_term'     => $attribution['utm_term'],
			'fbclid'       => $attribution['fbclid'],
			'landing_page' => $attribution['landing_page'],
			'referrer'     => $attribution['referrer'],
		)
	);

	if ( ! $lead_id ) {
		wp_send_json_error( array( 'message' => __( 'Có lỗi xảy ra, vui lòng thử lại.', 'lukasports-core' ) ), 500 );
	}

	do_action( 'lukasports_core_lead_created', $lead_id );

	wp_send_json_success(
		array(
			'message' => __( 'Đã gửi yêu cầu tư vấn! LukaSports sẽ liên hệ với bạn sớm nhất.', 'lukasports-core' ),
		)
	);
}
add_action( 'wp_ajax_lukasports_submit_lead', 'lukasports_core_handle_submit_lead' );
add_action( 'wp_ajax_nopriv_lukasports_submit_lead', 'lukasports_core_handle_submit_lead' );

/**
 * The attribution cookie is written client-side (see assets/js/main.js)
 * on first touch and never overwritten, so whatever's in it reflects
 * the visitor's original ad click even if they browsed for a while
 * before requesting a consultation.
 */
function lukasports_core_read_attribution_cookie() {
	$defaults = array(
		'utm_source'   => null,
		'utm_medium'   => null,
		'utm_campaign' => null,
		'utm_content'  => null,
		'utm_term'     => null,
		'fbclid'       => null,
		'landing_page' => null,
		'referrer'     => null,
	);

	if ( empty( $_COOKIE[ LUKASPORTS_CORE_ATTRIBUTION_COOKIE ] ) ) {
		return $defaults;
	}

	$raw     = wp_unslash( $_COOKIE[ LUKASPORTS_CORE_ATTRIBUTION_COOKIE ] );
	$decoded = json_decode( $raw, true );

	if ( ! is_array( $decoded ) ) {
		return $defaults;
	}

	$clean = array();
	foreach ( $defaults as $key => $default ) {
		$clean[ $key ] = isset( $decoded[ $key ] ) ? substr( sanitize_text_field( $decoded[ $key ] ), 0, 255 ) : $default;
	}

	return $clean;
}

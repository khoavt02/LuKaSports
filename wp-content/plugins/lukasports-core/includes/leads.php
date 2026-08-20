<?php
/**
 * Lead storage. A custom table rather than a CPT — leads need fast
 * filtering by status/source/date, which would mean querying
 * wp_postmeta row-by-row otherwise (see Phase 0 DB design).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LUKASPORTS_CORE_LEADS_TABLE', 'lukasports_leads' );

function lukasports_core_leads_table() {
	global $wpdb;
	return $wpdb->prefix . LUKASPORTS_CORE_LEADS_TABLE;
}

/**
 * @return array<string,string> status slug => Vietnamese label
 */
function lukasports_core_lead_statuses() {
	return array(
		'new'        => __( 'Mới', 'lukasports-core' ),
		'contacted'  => __( 'Đã liên hệ', 'lukasports-core' ),
		'consulting' => __( 'Đang tư vấn', 'lukasports-core' ),
		'won'        => __( 'Đã chốt', 'lukasports-core' ),
		'lost'       => __( 'Không chốt', 'lukasports-core' ),
	);
}

function lukasports_core_lead_status_label( $status ) {
	$statuses = lukasports_core_lead_statuses();
	return $statuses[ $status ] ?? $status;
}

function lukasports_core_create_leads_table() {
	global $wpdb;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$table_name      = lukasports_core_leads_table();
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table_name} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		name VARCHAR(190) NOT NULL,
		phone VARCHAR(32) NOT NULL,
		product_id BIGINT UNSIGNED NULL,
		product_name VARCHAR(255) NULL,
		message TEXT NULL,
		source VARCHAR(64) NULL,
		utm_source VARCHAR(190) NULL,
		utm_medium VARCHAR(190) NULL,
		utm_campaign VARCHAR(190) NULL,
		utm_content VARCHAR(190) NULL,
		utm_term VARCHAR(190) NULL,
		fbclid VARCHAR(190) NULL,
		landing_page VARCHAR(255) NULL,
		referrer VARCHAR(255) NULL,
		status VARCHAR(20) NOT NULL DEFAULT 'new',
		notes TEXT NULL,
		created_at DATETIME NOT NULL,
		updated_at DATETIME NOT NULL,
		PRIMARY KEY  (id),
		KEY status (status),
		KEY created_at (created_at),
		KEY product_id (product_id)
	) {$charset_collate};";

	dbDelta( $sql );
}

/**
 * Inserts a lead. $data keys are trusted to already be sanitized by
 * the caller (the AJAX handler) — this function only deals with
 * storage, not request input.
 *
 * @return int|false New lead ID, or false on failure.
 */
function lukasports_core_create_lead( array $data ) {
	global $wpdb;

	$now = current_time( 'mysql' );

	$row = wp_parse_args(
		$data,
		array(
			'name'         => '',
			'phone'        => '',
			'product_id'   => null,
			'product_name' => null,
			'message'      => null,
			'source'       => 'website',
			'utm_source'   => null,
			'utm_medium'   => null,
			'utm_campaign' => null,
			'utm_content'  => null,
			'utm_term'     => null,
			'fbclid'       => null,
			'landing_page' => null,
			'referrer'     => null,
			'status'       => 'new',
			'notes'        => null,
		)
	);

	$row['created_at'] = $now;
	$row['updated_at'] = $now;

	$inserted = $wpdb->insert( lukasports_core_leads_table(), $row ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

	return $inserted ? (int) $wpdb->insert_id : false;
}

function lukasports_core_get_lead( $id ) {
	global $wpdb;
	$table = lukasports_core_leads_table();
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * @param array $args {
 *   @type string $status   Filter by status (optional).
 *   @type string $search   Match name/phone (optional).
 *   @type int    $per_page
 *   @type int    $page
 *   @type string $orderby
 *   @type string $order
 * }
 * @return array{items: array, total: int}
 */
function lukasports_core_query_leads( array $args = array() ) {
	global $wpdb;
	$table = lukasports_core_leads_table();

	$args = wp_parse_args(
		$args,
		array(
			'status'   => '',
			'search'   => '',
			'per_page' => 20,
			'page'     => 1,
			'orderby'  => 'created_at',
			'order'    => 'DESC',
		)
	);

	$where  = array( '1=1' );
	$params = array();

	if ( $args['status'] ) {
		$where[]  = 'status = %s';
		$params[] = $args['status'];
	}

	if ( $args['search'] ) {
		$where[]  = '(name LIKE %s OR phone LIKE %s)';
		$like     = '%' . $wpdb->esc_like( $args['search'] ) . '%';
		$params[] = $like;
		$params[] = $like;
	}

	$where_sql = implode( ' AND ', $where );

	$allowed_orderby = array( 'created_at', 'status', 'name' );
	$orderby         = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'created_at';
	$order           = 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC';

	$per_page = max( 1, (int) $args['per_page'] );
	$offset   = max( 0, ( (int) $args['page'] - 1 ) * $per_page );

	$count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
	$list_sql  = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";

	if ( $params ) {
		$total = (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $params ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$items = $wpdb->get_results( $wpdb->prepare( $list_sql, array_merge( $params, array( $per_page, $offset ) ) ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	} else {
		$total = (int) $wpdb->get_var( $count_sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$items = $wpdb->get_results( $wpdb->prepare( $list_sql, $per_page, $offset ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	return array(
		'items' => $items,
		'total' => $total,
	);
}

function lukasports_core_count_leads_by_status( $status ) {
	global $wpdb;
	$table = lukasports_core_leads_table();
	return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE status = %s", $status ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

function lukasports_core_update_lead( $id, array $data ) {
	global $wpdb;
	$data['updated_at'] = current_time( 'mysql' );
	return false !== $wpdb->update( lukasports_core_leads_table(), $data, array( 'id' => (int) $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
}

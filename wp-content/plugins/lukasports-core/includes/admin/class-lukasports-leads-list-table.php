<?php
/**
 * WP_List_Table for the Leads screen — gives us sortable columns,
 * pagination and a search box for free instead of hand-rolling them.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class LukaSports_Leads_List_Table extends WP_List_Table {

	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'lead',
				'plural'   => 'leads',
				'ajax'     => false,
			)
		);
	}

	public function get_columns() {
		return array(
			'created_at' => __( 'Ngày tạo', 'lukasports-core' ),
			'name'       => __( 'Khách hàng', 'lukasports-core' ),
			'phone'      => __( 'Số điện thoại', 'lukasports-core' ),
			'product'    => __( 'Sản phẩm', 'lukasports-core' ),
			'source'     => __( 'Nguồn', 'lukasports-core' ),
			'status'     => __( 'Trạng thái', 'lukasports-core' ),
		);
	}

	protected function get_sortable_columns() {
		return array(
			'created_at' => array( 'created_at', true ),
			'name'       => array( 'name', false ),
			'status'     => array( 'status', false ),
		);
	}

	protected function get_views() {
		$current = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$base_url = remove_query_arg( array( 'status', 'paged' ) );

		$views  = array();
		$total  = lukasports_core_query_leads( array( 'per_page' => 1 ) )['total'];
		$views['all'] = sprintf(
			'<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
			esc_url( $base_url ),
			'' === $current ? 'current' : '',
			esc_html__( 'Tất cả', 'lukasports-core' ),
			$total
		);

		foreach ( lukasports_core_lead_statuses() as $slug => $label ) {
			$count           = lukasports_core_count_leads_by_status( $slug );
			$views[ $slug ]  = sprintf(
				'<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
				esc_url( add_query_arg( 'status', $slug, $base_url ) ),
				$current === $slug ? 'current' : '',
				esc_html( $label ),
				$count
			);
		}

		return $views;
	}

	public function prepare_items() {
		$per_page = 20;
		$paged    = $this->get_pagenum();
		$status   = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$search   = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$orderby  = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'created_at'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$order    = isset( $_GET['order'] ) ? sanitize_key( wp_unslash( $_GET['order'] ) ) : 'desc'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$result = lukasports_core_query_leads(
			array(
				'status'   => $status,
				'search'   => $search,
				'per_page' => $per_page,
				'page'     => $paged,
				'orderby'  => $orderby,
				'order'    => $order,
			)
		);

		$this->items = $result['items'];

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		$this->set_pagination_args(
			array(
				'total_items' => $result['total'],
				'per_page'    => $per_page,
			)
		);
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'created_at':
				return esc_html( mysql2date( 'd/m/Y H:i', $item->created_at ) );
			case 'phone':
				return esc_html( $item->phone );
			case 'product':
				return $item->product_name ? esc_html( $item->product_name ) : '—';
			case 'source':
				$bits = array_filter( array( $item->source, $item->utm_campaign ) );
				return $bits ? esc_html( implode( ' / ', $bits ) ) : '—';
			default:
				return '';
		}
	}

	public function column_name( $item ) {
		$view_url = add_query_arg(
			array(
				'page'    => 'lukasports-core-leads',
				'action'  => 'view',
				'lead_id' => $item->id,
			),
			admin_url( 'admin.php' )
		);

		return sprintf(
			'<a href="%s"><strong>%s</strong></a>',
			esc_url( $view_url ),
			esc_html( $item->name )
		);
	}

	public function column_status( $item ) {
		return sprintf(
			'<span class="lukasports-status lukasports-status--%s">%s</span>',
			esc_attr( $item->status ),
			esc_html( lukasports_core_lead_status_label( $item->status ) )
		);
	}
}

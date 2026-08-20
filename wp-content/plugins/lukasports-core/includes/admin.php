<?php
/**
 * "LukaSports" admin menu: Dashboard, Leads, Settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_core_register_admin_menu() {
	add_menu_page(
		__( 'LukaSports', 'lukasports-core' ),
		__( 'LukaSports', 'lukasports-core' ),
		'manage_woocommerce',
		'lukasports-core',
		'lukasports_core_render_dashboard_page',
		'dashicons-shield',
		56
	);

	add_submenu_page(
		'lukasports-core',
		__( 'Dashboard', 'lukasports-core' ),
		__( 'Dashboard', 'lukasports-core' ),
		'manage_woocommerce',
		'lukasports-core',
		'lukasports_core_render_dashboard_page'
	);

	add_submenu_page(
		'lukasports-core',
		__( 'Leads', 'lukasports-core' ),
		__( 'Leads', 'lukasports-core' ),
		'manage_woocommerce',
		'lukasports-core-leads',
		'lukasports_core_render_leads_page'
	);

	add_submenu_page(
		'lukasports-core',
		__( 'Cài đặt liên hệ', 'lukasports-core' ),
		__( 'Settings', 'lukasports-core' ),
		'manage_woocommerce',
		'lukasports-core-settings',
		'lukasports_core_render_settings_page'
	);
}
add_action( 'admin_menu', 'lukasports_core_register_admin_menu' );

require_once LUKASPORTS_CORE_DIR . 'includes/admin/leads-page.php';

function lukasports_core_admin_styles( $hook ) {
	if ( ! str_contains( (string) $hook, 'lukasports-core' ) ) {
		return;
	}

	if ( str_contains( (string) $hook, 'lukasports-core-settings' ) ) {
		wp_enqueue_media();
		wp_add_inline_script( 'media-editor', lukasports_core_hero_image_picker_script() );
	}

	wp_add_inline_style(
		'common',
		'.lukasports-dashboard__stats{display:flex;flex-wrap:wrap;gap:16px;margin:16px 0;}
		.lukasports-dashboard__stat{background:#fff;border:1px solid #ccd0d4;border-radius:4px;padding:16px 20px;min-width:160px;}
		.lukasports-dashboard__stat strong{display:block;font-size:24px;line-height:1.2;}
		.lukasports-status{display:inline-block;padding:2px 8px;border-radius:3px;font-size:12px;font-weight:600;background:#eee;}
		.lukasports-status--new{background:#d7e9ff;color:#0a4f9e;}
		.lukasports-status--contacted{background:#fff3cd;color:#8a6100;}
		.lukasports-status--consulting{background:#e6d9ff;color:#4b1f9e;}
		.lukasports-status--won{background:#d6f5df;color:#0a7a35;}
		.lukasports-status--lost{background:#f5d6d6;color:#9e1f1f;}'
	);
}
add_action( 'admin_enqueue_scripts', 'lukasports_core_admin_styles' );

function lukasports_core_render_dashboard_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	$total_products     = (int) wp_count_posts( 'product' )->publish + (int) wp_count_posts( 'product' )->draft;
	$published_products = (int) wp_count_posts( 'product' )->publish;

	$new_leads        = lukasports_core_count_leads_by_status( 'new' );
	$consulting_leads = lukasports_core_count_leads_by_status( 'consulting' );
	$won_leads        = lukasports_core_count_leads_by_status( 'won' );

	$recent = lukasports_core_query_leads( array( 'per_page' => 8 ) )['items'];
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'LukaSports — Dashboard', 'lukasports-core' ); ?></h1>

		<div class="lukasports-dashboard__stats">
			<div class="lukasports-dashboard__stat">
				<strong><?php echo esc_html( $total_products ); ?></strong>
				<?php esc_html_e( 'Tổng sản phẩm', 'lukasports-core' ); ?>
			</div>
			<div class="lukasports-dashboard__stat">
				<strong><?php echo esc_html( $published_products ); ?></strong>
				<?php esc_html_e( 'Sản phẩm đang bán', 'lukasports-core' ); ?>
			</div>
			<div class="lukasports-dashboard__stat">
				<strong><?php echo esc_html( $new_leads ); ?></strong>
				<?php esc_html_e( 'Lead mới', 'lukasports-core' ); ?>
			</div>
			<div class="lukasports-dashboard__stat">
				<strong><?php echo esc_html( $consulting_leads ); ?></strong>
				<?php esc_html_e( 'Lead đang tư vấn', 'lukasports-core' ); ?>
			</div>
			<div class="lukasports-dashboard__stat">
				<strong><?php echo esc_html( $won_leads ); ?></strong>
				<?php esc_html_e( 'Lead đã chốt', 'lukasports-core' ); ?>
			</div>
		</div>

		<h2><?php esc_html_e( 'Lead gần đây', 'lukasports-core' ); ?></h2>
		<?php if ( empty( $recent ) ) : ?>
			<p><?php esc_html_e( 'Chưa có lead nào.', 'lukasports-core' ); ?></p>
		<?php else : ?>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Khách hàng', 'lukasports-core' ); ?></th>
						<th><?php esc_html_e( 'SĐT', 'lukasports-core' ); ?></th>
						<th><?php esc_html_e( 'Sản phẩm', 'lukasports-core' ); ?></th>
						<th><?php esc_html_e( 'Nguồn', 'lukasports-core' ); ?></th>
						<th><?php esc_html_e( 'Trạng thái', 'lukasports-core' ); ?></th>
						<th><?php esc_html_e( 'Ngày tạo', 'lukasports-core' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $recent as $lead ) : ?>
						<tr>
							<td>
								<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lukasports-core-leads', 'action' => 'view', 'lead_id' => $lead->id ), admin_url( 'admin.php' ) ) ); ?>">
									<?php echo esc_html( $lead->name ); ?>
								</a>
							</td>
							<td><?php echo esc_html( $lead->phone ); ?></td>
							<td><?php echo esc_html( $lead->product_name ?: '—' ); ?></td>
							<td><?php echo esc_html( $lead->source ?: '—' ); ?></td>
							<td><span class="lukasports-status lukasports-status--<?php echo esc_attr( $lead->status ); ?>"><?php echo esc_html( lukasports_core_lead_status_label( $lead->status ) ); ?></span></td>
							<td><?php echo esc_html( mysql2date( 'd/m/Y H:i', $lead->created_at ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=lukasports-core-leads' ) ); ?>">
				<?php esc_html_e( 'Xem tất cả leads →', 'lukasports-core' ); ?>
			</a></p>
		<?php endif; ?>
	</div>
	<?php
}

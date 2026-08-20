<?php
/**
 * Renders the Leads screen: either the list table, or a single
 * lead's detail view (status + notes editable there).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_core_render_leads_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'Bạn không có quyền truy cập trang này.', 'lukasports-core' ) );
	}

	$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( 'view' === $action && ! empty( $_GET['lead_id'] ) ) {
		lukasports_core_render_lead_detail( absint( $_GET['lead_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	require_once LUKASPORTS_CORE_DIR . 'includes/admin/class-lukasports-leads-list-table.php';
	$table = new LukaSports_Leads_List_Table();
	$table->prepare_items();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Leads', 'lukasports-core' ); ?></h1>
		<form method="get">
			<input type="hidden" name="page" value="lukasports-core-leads" />
			<?php $table->views(); ?>
			<?php $table->search_box( __( 'Tìm theo tên/SĐT', 'lukasports-core' ), 'lukasports-lead' ); ?>
			<?php $table->display(); ?>
		</form>
	</div>
	<?php
}

function lukasports_core_render_lead_detail( $lead_id ) {
	$lead = lukasports_core_get_lead( $lead_id );

	if ( ! $lead ) {
		echo '<div class="wrap"><p>' . esc_html__( 'Không tìm thấy lead này.', 'lukasports-core' ) . '</p></div>';
		return;
	}

	$updated = false;
	if ( isset( $_POST['lukasports_lead_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['lukasports_lead_nonce'] ), 'lukasports_update_lead_' . $lead_id ) ) {
		$new_status = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : $lead->status;
		$new_notes  = isset( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : $lead->notes;

		if ( ! array_key_exists( $new_status, lukasports_core_lead_statuses() ) ) {
			$new_status = $lead->status;
		}

		lukasports_core_update_lead( $lead_id, array( 'status' => $new_status, 'notes' => $new_notes ) );
		$lead    = lukasports_core_get_lead( $lead_id );
		$updated = true;
	}

	$back_url = remove_query_arg( array( 'action', 'lead_id' ) );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Chi tiết lead', 'lukasports-core' ); ?></h1>
		<p><a href="<?php echo esc_url( $back_url ); ?>">&larr; <?php esc_html_e( 'Quay lại danh sách', 'lukasports-core' ); ?></a></p>

		<?php if ( $updated ) : ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'Đã lưu thay đổi.', 'lukasports-core' ); ?></p></div>
		<?php endif; ?>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<table class="form-table" role="presentation">
						<tr>
							<th><?php esc_html_e( 'Khách hàng', 'lukasports-core' ); ?></th>
							<td><?php echo esc_html( $lead->name ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Số điện thoại', 'lukasports-core' ); ?></th>
							<td>
								<a href="tel:<?php echo esc_attr( $lead->phone ); ?>"><?php echo esc_html( $lead->phone ); ?></a>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Sản phẩm quan tâm', 'lukasports-core' ); ?></th>
							<td>
								<?php if ( $lead->product_id && get_post( $lead->product_id ) ) : ?>
									<a href="<?php echo esc_url( get_edit_post_link( $lead->product_id ) ); ?>"><?php echo esc_html( $lead->product_name ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $lead->product_name ?: '—' ); ?>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Nội dung khách gửi', 'lukasports-core' ); ?></th>
							<td><?php echo $lead->message ? nl2br( esc_html( $lead->message ) ) : '—'; ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Nguồn', 'lukasports-core' ); ?></th>
							<td><?php echo esc_html( $lead->source ?: '—' ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'UTM Source', 'lukasports-core' ); ?></th>
							<td><?php echo esc_html( $lead->utm_source ?: '—' ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'UTM Medium', 'lukasports-core' ); ?></th>
							<td><?php echo esc_html( $lead->utm_medium ?: '—' ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'UTM Campaign', 'lukasports-core' ); ?></th>
							<td><?php echo esc_html( $lead->utm_campaign ?: '—' ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'UTM Content', 'lukasports-core' ); ?></th>
							<td><?php echo esc_html( $lead->utm_content ?: '—' ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'UTM Term', 'lukasports-core' ); ?></th>
							<td><?php echo esc_html( $lead->utm_term ?: '—' ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Facebook Click ID', 'lukasports-core' ); ?></th>
							<td><?php echo esc_html( $lead->fbclid ?: '—' ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Landing page', 'lukasports-core' ); ?></th>
							<td>
								<?php if ( $lead->landing_page ) : ?>
									<a href="<?php echo esc_url( $lead->landing_page ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $lead->landing_page ); ?></a>
								<?php else : ?>
									—
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Referrer', 'lukasports-core' ); ?></th>
							<td><?php echo esc_html( $lead->referrer ?: '—' ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Ngày tạo', 'lukasports-core' ); ?></th>
							<td><?php echo esc_html( mysql2date( 'd/m/Y H:i', $lead->created_at ) ); ?></td>
						</tr>
					</table>
				</div>

				<div id="postbox-container-1" class="postbox-container">
					<form method="post">
						<?php wp_nonce_field( 'lukasports_update_lead_' . $lead_id, 'lukasports_lead_nonce' ); ?>
						<div class="postbox">
							<h2 class="hndle"><span><?php esc_html_e( 'Trạng thái & ghi chú', 'lukasports-core' ); ?></span></h2>
							<div class="inside">
								<p>
									<label for="status"><strong><?php esc_html_e( 'Trạng thái', 'lukasports-core' ); ?></strong></label><br />
									<select name="status" id="status" style="width:100%;">
										<?php foreach ( lukasports_core_lead_statuses() as $slug => $label ) : ?>
											<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $lead->status, $slug ); ?>><?php echo esc_html( $label ); ?></option>
										<?php endforeach; ?>
									</select>
								</p>
								<p>
									<label for="notes"><strong><?php esc_html_e( 'Ghi chú nội bộ', 'lukasports-core' ); ?></strong></label><br />
									<textarea name="notes" id="notes" rows="6" style="width:100%;"><?php echo esc_textarea( $lead->notes ?? '' ); ?></textarea>
								</p>
								<?php submit_button( __( 'Lưu thay đổi', 'lukasports-core' ), 'primary', 'submit', false ); ?>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php
}

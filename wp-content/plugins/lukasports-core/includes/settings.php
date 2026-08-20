<?php
/**
 * Contact channels + CTA copy, admin-editable — see Part 3 of the
 * Phase 2 brief. Every frontend template reads these through
 * lukasports_get_contact_info() (theme inc/helpers.php) rather than
 * touching the option directly, so there's one source of truth and
 * no hardcoded phone numbers/URLs in template files.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LUKASPORTS_CORE_SETTINGS_OPTION', 'lukasports_settings' );

function lukasports_core_settings_defaults() {
	return array(
		'hotline_display'   => '0987 000 111',
		'hotline_tel'       => '+84987000111',
		'messenger_url'     => 'https://m.me/lukasports.vn',
		'zalo_url'          => 'https://zalo.me/0987000111',
		'facebook_url'      => 'https://facebook.com/lukasports.vn',
		'address'           => 'Số 12, Ngõ 88, Đường Láng, Đống Đa, Hà Nội',
		'primary_cta_text'  => 'Tư vấn ngay',
		'secondary_cta_text' => 'Đặt áo cho đội',
		'meta_pixel_id'      => '',
		'ga4_measurement_id' => '',
		'tiktok_pixel_id'    => '',
		'hero_image_id'      => 0,
	);
}

function lukasports_core_get_settings() {
	$saved = get_option( LUKASPORTS_CORE_SETTINGS_OPTION, array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	return array_merge( lukasports_core_settings_defaults(), array_filter( $saved, fn( $v ) => '' !== $v ) );
}

function lukasports_core_register_settings() {
	register_setting(
		'lukasports_settings_group',
		LUKASPORTS_CORE_SETTINGS_OPTION,
		array( 'sanitize_callback' => 'lukasports_core_sanitize_settings' )
	);
}
add_action( 'admin_init', 'lukasports_core_register_settings' );

function lukasports_core_sanitize_settings( $input ) {
	$clean = array();
	$input = (array) $input;

	$text_fields = array( 'hotline_display', 'address', 'primary_cta_text', 'secondary_cta_text' );
	foreach ( $text_fields as $field ) {
		$clean[ $field ] = isset( $input[ $field ] ) ? sanitize_text_field( $input[ $field ] ) : '';
	}

	$url_fields = array( 'messenger_url', 'zalo_url', 'facebook_url' );
	foreach ( $url_fields as $field ) {
		$clean[ $field ] = isset( $input[ $field ] ) ? sanitize_url( $input[ $field ] ) : '';
	}

	if ( isset( $input['hotline_tel'] ) ) {
		$clean['hotline_tel'] = preg_replace( '/[^0-9+]/', '', $input['hotline_tel'] );
	}

	// Tracking IDs: alphanumeric + hyphen only — they're echoed inside
	// inline <script> tags, so this is deliberately strict.
	$id_fields = array( 'meta_pixel_id', 'ga4_measurement_id', 'tiktok_pixel_id' );
	foreach ( $id_fields as $field ) {
		$clean[ $field ] = isset( $input[ $field ] ) ? preg_replace( '/[^A-Za-z0-9\-]/', '', $input[ $field ] ) : '';
	}

	$clean['hero_image_id'] = isset( $input['hero_image_id'] ) ? absint( $input['hero_image_id'] ) : 0;

	return $clean;
}

/**
 * wp.media picker wiring for the "hero_image_id" field — vanilla JS,
 * no build step, mirrors the pattern WordPress core uses for the
 * custom logo/site icon controls.
 */
function lukasports_core_hero_image_picker_script() {
	return <<<'JS'
	(function () {
		var frame;
		var input   = document.getElementById( 'hero_image_id' );
		var preview = document.getElementById( 'lukasports-hero-image-preview' );
		var chooseBtn = document.getElementById( 'lukasports-hero-image-choose' );
		var removeBtn = document.getElementById( 'lukasports-hero-image-remove' );
		if ( ! input || ! chooseBtn ) {
			return;
		}
		chooseBtn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			if ( frame ) {
				frame.open();
				return;
			}
			frame = wp.media( {
				title: 'Chọn ảnh banner trang chủ',
				button: { text: 'Dùng ảnh này' },
				multiple: false,
				library: { type: 'image' }
			} );
			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				input.value = attachment.id;
				var sizeUrl = ( attachment.sizes && attachment.sizes.medium ) ? attachment.sizes.medium.url : attachment.url;
				preview.innerHTML = '<img src="' + sizeUrl + '" style="max-width:320px;height:auto;display:block;border:1px solid #ccd0d4;" />';
				removeBtn.style.display = '';
			} );
			frame.open();
		} );
		removeBtn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			input.value = '0';
			preview.innerHTML = '';
			removeBtn.style.display = 'none';
		} );
	})();
	JS;
}

function lukasports_core_render_settings_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	$settings = lukasports_core_get_settings();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'LukaSports — Cài đặt liên hệ', 'lukasports-core' ); ?></h1>
		<p><?php esc_html_e( 'Toàn bộ nút liên hệ trên website (header, trang sản phẩm, thanh liên hệ di động, footer) đều lấy dữ liệu từ đây.', 'lukasports-core' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'lukasports_settings_group' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="hotline_display"><?php esc_html_e( 'Số hotline (hiển thị)', 'lukasports-core' ); ?></label></th>
					<td><input type="text" class="regular-text" id="hotline_display" name="<?php echo esc_attr( LUKASPORTS_CORE_SETTINGS_OPTION ); ?>[hotline_display]" value="<?php echo esc_attr( $settings['hotline_display'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="hotline_tel"><?php esc_html_e( 'Số hotline (dùng cho link tel:)', 'lukasports-core' ); ?></label></th>
					<td><input type="text" class="regular-text" id="hotline_tel" name="<?php echo esc_attr( LUKASPORTS_CORE_SETTINGS_OPTION ); ?>[hotline_tel]" value="<?php echo esc_attr( $settings['hotline_tel'] ); ?>" placeholder="+84987000111" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="messenger_url"><?php esc_html_e( 'Messenger URL', 'lukasports-core' ); ?></label></th>
					<td><input type="url" class="regular-text" id="messenger_url" name="<?php echo esc_attr( LUKASPORTS_CORE_SETTINGS_OPTION ); ?>[messenger_url]" value="<?php echo esc_attr( $settings['messenger_url'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="zalo_url"><?php esc_html_e( 'Zalo URL', 'lukasports-core' ); ?></label></th>
					<td><input type="url" class="regular-text" id="zalo_url" name="<?php echo esc_attr( LUKASPORTS_CORE_SETTINGS_OPTION ); ?>[zalo_url]" value="<?php echo esc_attr( $settings['zalo_url'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="facebook_url"><?php esc_html_e( 'Facebook URL', 'lukasports-core' ); ?></label></th>
					<td><input type="url" class="regular-text" id="facebook_url" name="<?php echo esc_attr( LUKASPORTS_CORE_SETTINGS_OPTION ); ?>[facebook_url]" value="<?php echo esc_attr( $settings['facebook_url'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="address"><?php esc_html_e( 'Địa chỉ', 'lukasports-core' ); ?></label></th>
					<td><input type="text" class="regular-text" id="address" name="<?php echo esc_attr( LUKASPORTS_CORE_SETTINGS_OPTION ); ?>[address]" value="<?php echo esc_attr( $settings['address'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="primary_cta_text"><?php esc_html_e( 'CTA chính (nút tư vấn)', 'lukasports-core' ); ?></label></th>
					<td><input type="text" class="regular-text" id="primary_cta_text" name="<?php echo esc_attr( LUKASPORTS_CORE_SETTINGS_OPTION ); ?>[primary_cta_text]" value="<?php echo esc_attr( $settings['primary_cta_text'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="secondary_cta_text"><?php esc_html_e( 'CTA phụ (nút đặt áo team)', 'lukasports-core' ); ?></label></th>
					<td><input type="text" class="regular-text" id="secondary_cta_text" name="<?php echo esc_attr( LUKASPORTS_CORE_SETTINGS_OPTION ); ?>[secondary_cta_text]" value="<?php echo esc_attr( $settings['secondary_cta_text'] ); ?>" /></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Ảnh banner trang chủ', 'lukasports-core' ); ?></h2>
			<p><?php esc_html_e( 'Ảnh hiển thị toàn chiều ngang ở đầu trang chủ, phía sau tiêu đề. Để trống sẽ dùng ảnh mặc định của theme.', 'lukasports-core' ); ?></p>
			<?php
			$sk_hero_image_id  = (int) $settings['hero_image_id'];
			$sk_hero_image_url = $sk_hero_image_id ? wp_get_attachment_image_url( $sk_hero_image_id, 'medium' ) : '';
			?>
			<div id="lukasports-hero-image-field">
				<input type="hidden" id="hero_image_id" name="<?php echo esc_attr( LUKASPORTS_CORE_SETTINGS_OPTION ); ?>[hero_image_id]" value="<?php echo esc_attr( $sk_hero_image_id ); ?>" />
				<div id="lukasports-hero-image-preview" style="margin-bottom:10px;">
					<?php if ( $sk_hero_image_url ) : ?>
						<img src="<?php echo esc_url( $sk_hero_image_url ); ?>" style="max-width:320px;height:auto;display:block;border:1px solid #ccd0d4;" />
					<?php endif; ?>
				</div>
				<button type="button" class="button" id="lukasports-hero-image-choose"><?php esc_html_e( 'Chọn ảnh', 'lukasports-core' ); ?></button>
				<button type="button" class="button" id="lukasports-hero-image-remove" <?php echo $sk_hero_image_id ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Xóa ảnh', 'lukasports-core' ); ?></button>
			</div>

			<h2><?php esc_html_e( 'Analytics & Pixel (không bắt buộc)', 'lukasports-core' ); ?></h2>
			<p><?php esc_html_e( 'Để trống nếu chưa dùng — chỉ khi có ID, script tương ứng mới được tải. Các sự kiện tư vấn/Messenger/Zalo/gọi điện đã có sẵn, sẽ tự động gửi sang các nền tảng này khi bạn điền ID.', 'lukasports-core' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="meta_pixel_id"><?php esc_html_e( 'Meta Pixel ID', 'lukasports-core' ); ?></label></th>
					<td><input type="text" class="regular-text" id="meta_pixel_id" name="<?php echo esc_attr( LUKASPORTS_CORE_SETTINGS_OPTION ); ?>[meta_pixel_id]" value="<?php echo esc_attr( $settings['meta_pixel_id'] ); ?>" placeholder="1234567890123456" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="ga4_measurement_id"><?php esc_html_e( 'GA4 Measurement ID', 'lukasports-core' ); ?></label></th>
					<td><input type="text" class="regular-text" id="ga4_measurement_id" name="<?php echo esc_attr( LUKASPORTS_CORE_SETTINGS_OPTION ); ?>[ga4_measurement_id]" value="<?php echo esc_attr( $settings['ga4_measurement_id'] ); ?>" placeholder="G-XXXXXXXXXX" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="tiktok_pixel_id"><?php esc_html_e( 'TikTok Pixel ID', 'lukasports-core' ); ?></label></th>
					<td><input type="text" class="regular-text" id="tiktok_pixel_id" name="<?php echo esc_attr( LUKASPORTS_CORE_SETTINGS_OPTION ); ?>[tiktok_pixel_id]" value="<?php echo esc_attr( $settings['tiktok_pixel_id'] ); ?>" placeholder="XXXXXXXXXXXXXXXXXXXX" /></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

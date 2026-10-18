<?php
/**
 * Brand colors editable from Giao diện → Tùy biến → Màu thương hiệu.
 *
 * The whole stylesheet is built on a handful of CSS custom properties
 * (assets/css/core.css :root). Three of them are exposed here; the rest
 * (hover shade, *-rgb triplets for translucent overlays, text color on
 * accent buttons) are derived so a single pick stays consistent and
 * readable. Nothing is printed while the defaults are in use.
 *
 * Images with baked-in colors (bundled illustrations, generated product
 * art) don't follow these settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_brand_color_defaults() {
	return array(
		'dark'    => '#0b0e14',
		'accent'  => '#d9141c',
		'surface' => '#eef0f3',
	);
}

function lukasports_brand_color( $key ) {
	$defaults = lukasports_brand_color_defaults();
	$value    = sanitize_hex_color( get_theme_mod( 'lukasports_color_' . $key, $defaults[ $key ] ) );
	return $value ? $value : $defaults[ $key ];
}

function lukasports_customize_brand_colors( $wp_customize ) {
	$wp_customize->add_section(
		'lukasports_brand_colors',
		array(
			'title'       => __( 'Màu thương hiệu', 'lukasports' ),
			'description' => __( 'Áp dụng cho nút, link, nền tối, footer và các khối nền nhạt trên toàn site. Bấm "Mặc định" để quay về màu gốc. Logo, ảnh minh hoạ và ảnh sản phẩm có màu riêng, không đổi theo.', 'lukasports' ),
			'priority'    => 25,
		)
	);

	$wp_customize->add_setting(
		'lukasports_color_scheme',
		array(
			'default'           => 'dark',
			'sanitize_callback' => function ( $value ) {
				return 'dark' === $value ? 'dark' : 'light';
			},
		)
	);
	$wp_customize->add_control(
		'lukasports_color_scheme',
		array(
			'label'       => __( 'Phông nền nội dung', 'lukasports' ),
			'description' => __( 'Giao diện mặc định cho khách mới. Sáng: nội dung nền trắng (header, banner, footer vẫn tối). Tối: toàn bộ site nền đen. Khách có thể tự chuyển bằng nút mặt trăng / mặt trời trên header (trình duyệt sẽ nhớ lựa chọn).', 'lukasports' ),
			'section'     => 'lukasports_brand_colors',
			'type'        => 'radio',
			'choices'     => array(
				'light' => __( 'Sáng', 'lukasports' ),
				'dark'  => __( 'Tối', 'lukasports' ),
			),
			'priority'    => 1,
		)
	);

	$controls = array(
		'dark'    => array( __( 'Màu chính (nền tối)', 'lukasports' ), __( 'Banner, footer, khối nền tối, nút phụ.', 'lukasports' ) ),
		'accent'  => array( __( 'Màu nhấn', 'lukasports' ), __( 'Nút chính, link, nhãn giảm giá, danh mục đang chọn. Chữ trên nút tự chọn trắng hoặc đen cho dễ đọc.', 'lukasports' ) ),
		'surface' => array( __( 'Màu nền phụ', 'lukasports' ), __( 'Nền ảnh sản phẩm, khối số liệu, form liên hệ. Nên chọn màu rất nhạt.', 'lukasports' ) ),
	);

	foreach ( $controls as $key => list( $label, $description ) ) {
		$setting = 'lukasports_color_' . $key;
		$wp_customize->add_setting(
			$setting,
			array(
				'default'           => lukasports_brand_color_defaults()[ $key ],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$setting,
				array(
					'label'       => $label,
					'description' => $description,
					'section'     => 'lukasports_brand_colors',
				)
			)
		);
	}
}
add_action( 'customize_register', 'lukasports_customize_brand_colors' );

function lukasports_hex_to_rgb( $hex ) {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	return array_map( 'hexdec', str_split( $hex, 2 ) );
}

function lukasports_rgb_to_hex( $rgb ) {
	return '#' . implode( '', array_map( fn( $c ) => str_pad( dechex( max( 0, min( 255, (int) round( $c ) ) ) ), 2, '0', STR_PAD_LEFT ), $rgb ) );
}

/** Darken by mixing toward black; $amount 0..1. */
function lukasports_shade( $hex, $amount ) {
	return lukasports_rgb_to_hex( array_map( fn( $c ) => $c * ( 1 - $amount ), lukasports_hex_to_rgb( $hex ) ) );
}

/** WCAG relative luminance. */
function lukasports_luminance( $hex ) {
	$channels = array_map(
		function ( $c ) {
			$c /= 255;
			return $c <= 0.03928 ? $c / 12.92 : ( ( $c + 0.055 ) / 1.055 ) ** 2.4;
		},
		lukasports_hex_to_rgb( $hex )
	);
	return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
}

/** White or near-black, whichever contrasts more with $hex. */
function lukasports_readable_ink( $hex ) {
	$l          = lukasports_luminance( $hex );
	$with_white = 1.05 / ( $l + 0.05 );
	$with_black = ( $l + 0.05 ) / ( lukasports_luminance( '#12151c' ) + 0.05 );
	return $with_white >= $with_black ? '#ffffff' : '#12151c';
}

function lukasports_print_brand_colors() {
	$defaults = lukasports_brand_color_defaults();
	$dark     = lukasports_brand_color( 'dark' );
	$accent   = lukasports_brand_color( 'accent' );
	$surface  = lukasports_brand_color( 'surface' );

	if ( strtolower( $dark ) === $defaults['dark'] && strtolower( $accent ) === $defaults['accent'] && strtolower( $surface ) === $defaults['surface'] ) {
		return;
	}

	$vars = array(
		'--sk-navy'         => $dark,
		'--sk-navy-rgb'     => implode( ', ', lukasports_hex_to_rgb( $dark ) ),
		'--sk-navy-dark'    => lukasports_shade( $dark, 0.35 ),
		'--sk-accent'       => $accent,
		'--sk-accent-rgb'   => implode( ', ', lukasports_hex_to_rgb( $accent ) ),
		'--sk-accent-dark'  => lukasports_shade( $accent, 0.18 ),
		'--sk-accent-ink'   => lukasports_readable_ink( $accent ),
		'--sk-surface'      => $surface,
	);

	$css = ':root{';
	foreach ( $vars as $name => $value ) {
		$css .= $name . ':' . $value . ';';
	}
	$css .= '}';

	printf( '<style id="lukasports-brand-colors">%s</style>' . "\n", esc_html( $css ) );
}
// After enqueued stylesheets (printed at wp_head priority 8) so these win.
add_action( 'wp_head', 'lukasports_print_brand_colors', 20 );

/** Browser UI color on mobile (address bar) follows the dark brand color. */
function lukasports_theme_color_meta() {
	printf( '<meta name="theme-color" content="%s" />' . "\n", esc_attr( lukasports_brand_color( 'dark' ) ) );
}
add_action( 'wp_head', 'lukasports_theme_color_meta', 2 );

/**
 * Active color scheme: the Customizer choice, unless this visitor is
 * previewing the other one via ?giao-dien=toi|sang (remembered in a
 * cookie for a day so navigation keeps it; ?giao-dien=mac-dinh clears).
 */
function lukasports_color_scheme() {
	static $scheme = null;
	if ( null !== $scheme ) {
		return $scheme;
	}
	$scheme  = 'dark' === get_theme_mod( 'lukasports_color_scheme', 'dark' ) ? 'dark' : 'light';
	$preview = isset( $_GET['giao-dien'] ) ? sanitize_key( wp_unslash( $_GET['giao-dien'] ) ) : ( isset( $_COOKIE['sk_scheme'] ) ? sanitize_key( wp_unslash( $_COOKIE['sk_scheme'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( in_array( $preview, array( 'toi', 'dark' ), true ) ) {
		$scheme = 'dark';
	} elseif ( in_array( $preview, array( 'sang', 'light' ), true ) ) {
		$scheme = 'light';
	}
	return $scheme;
}

function lukasports_remember_scheme_preview() {
	if ( ! isset( $_GET['giao-dien'] ) || headers_sent() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	$value = sanitize_key( wp_unslash( $_GET['giao-dien'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'mac-dinh' === $value ) {
		setcookie( 'sk_scheme', '', time() - HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		unset( $_COOKIE['sk_scheme'] );
	} elseif ( in_array( $value, array( 'toi', 'sang' ), true ) ) {
		setcookie( 'sk_scheme', $value, time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
	}
}
add_action( 'template_redirect', 'lukasports_remember_scheme_preview', 1 );

function lukasports_scheme_html_attribute( $output ) {
	return 'dark' === lukasports_color_scheme() ? $output . ' data-theme="dark"' : $output;
}
add_filter( 'language_attributes', 'lukasports_scheme_html_attribute' );

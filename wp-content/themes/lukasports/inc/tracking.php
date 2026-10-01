<?php
/**
 * Meta Pixel / GA4 / TikTok Pixel — config-only, no hardcoded IDs.
 * Each platform's base snippet only loads if its ID is set in
 * DALETIC → Settings; with nothing configured (the default), this
 * file outputs nothing at all. See assets/js/main.js for the
 * lukasportsTrack()/`lukasports:track` event abstraction these platforms
 * hook into once active.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_tracking_ids() {
	$settings = function_exists( 'lukasports_core_get_settings' ) ? lukasports_core_get_settings() : array();

	return array(
		'meta_pixel_id'      => $settings['meta_pixel_id'] ?? '',
		'ga4_measurement_id' => $settings['ga4_measurement_id'] ?? '',
		'tiktok_pixel_id'    => $settings['tiktok_pixel_id'] ?? '',
	);
}

function lukasports_output_tracking_snippets() {
	$ids = lukasports_tracking_ids();

	if ( $ids['meta_pixel_id'] ) {
		?>
		<script>
		!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '<?php echo esc_js( $ids['meta_pixel_id'] ); ?>');
		fbq('track', 'PageView');
		</script>
		<?php
	}

	if ( $ids['ga4_measurement_id'] ) {
		?>
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ids['ga4_measurement_id'] ); ?>"></script>
		<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){ dataLayer.push(arguments); }
		gtag('js', new Date());
		gtag('config', '<?php echo esc_js( $ids['ga4_measurement_id'] ); ?>');
		</script>
		<?php
	}

	if ( $ids['tiktok_pixel_id'] ) {
		?>
		<script>
		!function (w, d, t) {
			w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
			ttq.load('<?php echo esc_js( $ids['tiktok_pixel_id'] ); ?>');
			ttq.page();
		}(window, document, 'ttq');
		</script>
		<?php
	}
}
add_action( 'wp_head', 'lukasports_output_tracking_snippets', 5 );

/**
 * Maps our own lukasportsTrack() events onto whichever platforms are
 * actually active. Only enqueued when at least one ID is configured,
 * so visitors carry zero extra JS while nothing is set up.
 */
function lukasports_enqueue_tracking_bridge() {
	$ids = lukasports_tracking_ids();

	if ( ! $ids['meta_pixel_id'] && ! $ids['ga4_measurement_id'] && ! $ids['tiktok_pixel_id'] ) {
		return;
	}

	$bridge = <<<'JS'
	document.addEventListener('lukasports:track', function (e) {
		var name = e.detail.name;
		var data = e.detail.data || {};

		if (window.fbq) {
			var metaMap = { submit_consultation: 'Lead', view_product: 'ViewContent', click_consultation: 'Contact', click_messenger: 'Contact', click_zalo: 'Contact', click_phone: 'Contact' };
			if (metaMap[name]) { fbq('track', metaMap[name]); }
		}
		if (window.gtag) {
			gtag('event', name, data);
		}
		if (window.ttq) {
			var tiktokMap = { submit_consultation: 'SubmitForm', view_product: 'ViewContent', click_consultation: 'Contact' };
			ttq.track(tiktokMap[name] || 'ClickButton', { description: name });
		}
	});
	JS;

	wp_add_inline_script( 'lukasports-main', $bridge );
}
add_action( 'wp_enqueue_scripts', 'lukasports_enqueue_tracking_bridge', 20 );

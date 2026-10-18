/**
 * Replaces the native <select> dropdowns in WooCommerce's variation
 * form with pill/color swatch buttons. The <select> elements stay in
 * the DOM and fully functional — clicking a swatch just sets the
 * select's value and dispatches a native "change" event, which is
 * exactly what WooCommerce's own variation-matching script listens
 * for. If this script fails to run for any reason, the native
 * dropdowns are simply left visible and the form still works.
 */
(function () {
	'use strict';

	// Mirrors lukasports_color_hex() in inc/helpers.php.
	var COLOR_MAP = {
		'Trắng': '#ffffff',
		'Đen': '#141414',
		'Đỏ': '#e10600',
		'Xanh Dương': '#1b4fbf',
		'Xanh Navy': '#1b2a4a',
		'Vàng': '#f2c14e',
		'Cam': '#f47c20',
		'Xám': '#9ca3af',
		'Xám Than': '#4a4a4a',
		'Be': '#d8cbb0',
		'Xanh Rêu': '#5c5f4a',
		'Xanh Lá': '#16a34a',
		'Hồng': '#ec4899'
	};

	function buildSwatches( select ) {
		var td = select.closest( 'td.value' );
		if ( ! td || td.querySelector( '.sk-variation-swatches' ) ) {
			return;
		}

		var attrName = ( select.getAttribute( 'name' ) || '' ).toLowerCase();
		var isColor  = attrName.indexOf( 'color' ) !== -1 || attrName.indexOf( 'mau' ) !== -1;

		var wrap = document.createElement( 'div' );
		wrap.className = 'sk-variation-swatches';

		Array.prototype.forEach.call( select.options, function ( option ) {
			if ( ! option.value ) {
				return;
			}

			var label = option.textContent.trim();
			var btn   = document.createElement( 'button' );
			btn.type  = 'button';
			btn.className = 'sk-swatch';
			btn.dataset.value = option.value;

			var hex = isColor ? COLOR_MAP[ label ] : null;
			if ( hex ) {
				btn.classList.add( 'sk-swatch--color' );
				btn.style.backgroundColor = hex;
				btn.setAttribute( 'aria-label', label );
				btn.title = label;
			} else {
				btn.textContent = label;
			}

			btn.addEventListener( 'click', function () {
				select.value = option.value;
				select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			} );

			wrap.appendChild( btn );
		} );

		td.appendChild( wrap );
		td.classList.add( 'sk-has-swatches' );

		// WooCommerce renders its "Xóa" (reset) link right after the
		// select, which would otherwise float between the now-hidden
		// select and the swatches — move it after the swatches instead.
		var resetLink = td.querySelector( '.reset_variations' );
		if ( resetLink ) {
			td.appendChild( resetLink );
		}

		function syncActive() {
			var current = select.value;
			Array.prototype.forEach.call( wrap.children, function ( btn ) {
				btn.classList.toggle( 'is-active', '' !== current && btn.dataset.value === current );
			} );
		}

		// "MÀU SẮC: Đen" — the picked value next to the row label.
		var row   = select.closest( 'tr' );
		var label = row ? row.querySelector( 'th.label label, .label label' ) : null;
		var shown = null;
		if ( label ) {
			shown = document.createElement( 'span' );
			shown.className = 'sk-variation-current';
			shown.setAttribute( 'aria-hidden', 'true' );
			label.appendChild( shown );
		}

		// The "Bảng size" link sits at the end of the size row's header.
		var isSize = attrName.indexOf( 'size' ) !== -1 || attrName.indexOf( 'kich' ) !== -1;
		var sizeLink = document.querySelector( '.sk-product__size-link' );
		if ( isSize && sizeLink && row ) {
			var th = row.querySelector( 'th.label' ) || row.firstElementChild;
			if ( th ) {
				th.classList.add( 'sk-has-size-link' );
				th.appendChild( sizeLink );
			}
		}

		function syncLabel() {
			if ( ! shown ) {
				return;
			}
			var opt = select.value ? select.options[ select.selectedIndex ] : null;
			shown.textContent = opt ? ': ' + opt.textContent.trim() : '';
		}

		select.addEventListener( 'change', syncActive );
		select.addEventListener( 'change', syncLabel );
		// WooCommerce resets selects programmatically ("Xóa") and fires
		// its own jQuery events; resync on those too.
		if ( window.jQuery ) {
			window.jQuery( select ).on( 'change', function () { syncActive(); syncLabel(); } );
			window.jQuery( select.form ).on( 'reset_data woocommerce_update_variation_values', function () { syncActive(); syncLabel(); } );
		}
		syncActive();
		syncLabel();
	}

	function init() {
		var selects = document.querySelectorAll( 'table.variations select' );
		Array.prototype.forEach.call( selects, buildSwatches );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
})();

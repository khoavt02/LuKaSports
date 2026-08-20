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

	var COLOR_MAP = {
		'Trắng': '#ffffff',
		'Đen': '#1a1a1a',
		'Be': '#d8cbb0',
		'Xám Than': '#4a4a4a',
		'Xanh Rêu': '#5c5f4a'
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

		select.addEventListener( 'change', syncActive );
		syncActive();
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

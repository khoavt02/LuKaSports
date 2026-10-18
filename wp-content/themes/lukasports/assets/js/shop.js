/**
 * Product listing enhancements (loaded on shop / category / collection
 * pages only): two-handle price slider, grid/list toggle remembered per
 * visitor, and the mobile filter drawer. Every control degrades to a
 * plain link or GET form without this script.
 */
(function () {
	'use strict';

	function store( key, value ) {
		try {
			if ( undefined === value ) {
				return window.localStorage.getItem( key );
			}
			window.localStorage.setItem( key, value );
		} catch ( e ) {
			return null;
		}
		return null;
	}

	function formatVnd( value ) {
		return Number( value ).toLocaleString( 'vi-VN' ) + 'đ';
	}

	/* ---- Price slider + typed range ---------------------------------- */
	function initPriceFilter( form ) {
		var min   = form.querySelector( '[data-sk-price-min]' );
		var max   = form.querySelector( '[data-sk-price-max]' );
		var fill  = form.querySelector( '[data-sk-price-fill]' );
		var minLb = form.querySelector( '[data-sk-price-min-label]' );
		var maxLb = form.querySelector( '[data-sk-price-max-label]' );
		var minIn = form.querySelector( '[data-sk-price-min-input]' );
		var maxIn = form.querySelector( '[data-sk-price-max-input]' );
		// Desktop sidebar has no Apply button in the design: submit as soon
		// as a handle is released or a typed value is committed. In the
		// phone drawer the "Áp dụng" button submits instead.
		var desktop = window.matchMedia( '(min-width: 1024px)' );
		if ( ! min || ! max ) {
			return;
		}
		var floor = Number( min.min );
		var ceil  = Number( min.max );

		function paint( moved ) {
			var a = Number( min.value );
			var b = Number( max.value );
			if ( a > b ) {
				if ( moved === min ) {
					min.value = b;
					a = b;
				} else {
					max.value = a;
					b = a;
				}
			}
			var span = ceil - floor || 1;
			if ( fill ) {
				fill.style.left  = ( ( a - floor ) / span * 100 ) + '%';
				fill.style.right = ( 100 - ( b - floor ) / span * 100 ) + '%';
			}
			if ( minLb ) {
				minLb.textContent = formatVnd( a );
			}
			if ( maxLb ) {
				maxLb.textContent = formatVnd( b );
			}
			if ( minIn && document.activeElement !== minIn ) {
				minIn.value = formatVnd( a );
			}
			if ( maxIn && document.activeElement !== maxIn ) {
				maxIn.value = formatVnd( b );
			}
			// Keep the handle being dragged on top when both meet.
			min.style.zIndex = a >= ceil - Number( min.step || 1 ) ? 3 : 2;
		}

		min.addEventListener( 'input', function () { paint( min ); } );
		max.addEventListener( 'input', function () { paint( max ); } );

		function autoSubmit() {
			if ( desktop.matches ) {
				form.requestSubmit ? form.requestSubmit() : form.submit();
			}
		}
		min.addEventListener( 'change', autoSubmit );
		max.addEventListener( 'change', autoSubmit );

		// Typed "Từ / Đến" values drive the (named) range inputs.
		[ [ minIn, min ], [ maxIn, max ] ].forEach( function ( pair ) {
			var input = pair[ 0 ];
			var range = pair[ 1 ];
			if ( ! input ) {
				return;
			}
			input.addEventListener( 'focus', function () {
				input.value = String( range.value );
				input.select();
			} );
			input.addEventListener( 'change', function () {
				var digits = input.value.replace( /\D/g, '' );
				if ( digits ) {
					range.value = Math.min( ceil, Math.max( floor, Number( digits ) ) );
				}
				paint( range );
				input.value = formatVnd( range.value );
				autoSubmit();
			} );
			input.addEventListener( 'blur', function () {
				input.value = formatVnd( range.value );
			} );
		} );

		form.addEventListener( 'submit', function () {
			// Don't put the untouched full range into the URL.
			if ( Number( min.value ) <= floor ) {
				min.disabled = true;
			}
			if ( Number( max.value ) >= ceil ) {
				max.disabled = true;
			}
		} );

		paint();
	}

	/* ---- Grid / list toggle ------------------------------------------ */
	function initViewToggle() {
		var grid    = document.querySelector( '[data-sk-product-grid]' );
		var buttons = document.querySelectorAll( '[data-sk-view]' );
		if ( ! grid || ! buttons.length ) {
			return;
		}

		function apply( view ) {
			grid.classList.toggle( 'is-list', 'list' === view );
			buttons.forEach( function ( btn ) {
				var on = btn.getAttribute( 'data-sk-view' ) === view;
				btn.classList.toggle( 'is-active', on );
				btn.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
			} );
		}

		buttons.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var view = btn.getAttribute( 'data-sk-view' );
				store( 'sk_view', view );
				apply( view );
			} );
		} );

		apply( 'list' === store( 'sk_view' ) ? 'list' : 'grid' );
	}

	/* ---- Mobile filter drawer ---------------------------------------- */
	function initFilterDrawer() {
		var panel    = document.getElementById( 'sk-filters' );
		var openers  = document.querySelectorAll( '[data-sk-filters-open]' );
		var closers  = document.querySelectorAll( '[data-sk-filters-close]' );
		var backdrop = document.querySelector( '.sk-filters-backdrop' );
		if ( ! panel || ! openers.length ) {
			return;
		}

		function toggle( open ) {
			panel.classList.toggle( 'is-open', open );
			if ( backdrop ) {
				backdrop.hidden = ! open;
			}
			openers.forEach( function ( btn ) {
				btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			} );
			document.documentElement.classList.toggle( 'sk-modal-open', open );
		}

		openers.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () { toggle( true ); } );
		} );
		closers.forEach( function ( el ) {
			el.addEventListener( 'click', function () { toggle( false ); } );
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && panel.classList.contains( 'is-open' ) ) {
				toggle( false );
			}
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '[data-sk-price-filter]' ).forEach( initPriceFilter );
		initViewToggle();
		initFilterDrawer();
	} );
}());

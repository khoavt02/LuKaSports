/**
 * Single product page: gallery (thumbnails, arrows, active-thumb sync),
 * detail tabs (with deep links like #tab-size_guide), and the chosen
 * size/color passed on to the consult form. Everything still works as
 * plain scrolling / anchors without this script.
 */
(function () {
	'use strict';

	function initGallery( gallery ) {
		var main   = gallery.querySelector( '.sk-gallery__main' );
		var thumbs = Array.prototype.slice.call( gallery.querySelectorAll( '[data-sk-gallery-target]' ) );
		var counter = gallery.querySelector( '[data-sk-gallery-counter]' );
		if ( ! main ) {
			return;
		}
		var slides = Array.prototype.slice.call( main.children );

		function current() {
			return Math.round( main.scrollLeft / ( main.clientWidth || 1 ) );
		}
		function go( index ) {
			index = ( index + slides.length ) % slides.length;
			main.scrollTo( { left: slides[ index ].offsetLeft, behavior: 'smooth' } );
		}

		thumbs.forEach( function ( thumb, index ) {
			thumb.addEventListener( 'click', function () { go( index ); } );
		} );
		gallery.querySelectorAll( '[data-sk-gallery-step]' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				go( current() + Number( btn.getAttribute( 'data-sk-gallery-step' ) ) );
			} );
		} );

		var ticking = false;
		main.addEventListener( 'scroll', function () {
			if ( ticking ) {
				return;
			}
			ticking = true;
			window.requestAnimationFrame( function () {
				var i = current();
				thumbs.forEach( function ( t, n ) {
					t.classList.toggle( 'is-active', n === i );
					t.setAttribute( 'aria-pressed', n === i ? 'true' : 'false' );
				} );
				if ( counter ) {
					counter.textContent = ( i + 1 ) + '/' + slides.length;
				}
				ticking = false;
			} );
		} );
	}

	function initTabs( root ) {
		var tabs   = Array.prototype.slice.call( root.querySelectorAll( '[role="tab"]' ) );
		var panels = Array.prototype.slice.call( root.querySelectorAll( '[role="tabpanel"]' ) );
		if ( ! tabs.length ) {
			return null;
		}

		function select( key, focus ) {
			tabs.forEach( function ( tab ) {
				var on = tab.getAttribute( 'data-tab' ) === key;
				tab.setAttribute( 'aria-selected', on ? 'true' : 'false' );
				tab.tabIndex = on ? 0 : -1;
				if ( on && focus ) {
					tab.focus();
				}
			} );
			panels.forEach( function ( panel ) {
				panel.hidden = panel.getAttribute( 'data-tab' ) !== key;
			} );
		}

		tabs.forEach( function ( tab, i ) {
			tab.addEventListener( 'click', function () { select( tab.getAttribute( 'data-tab' ) ); } );
			tab.addEventListener( 'keydown', function ( e ) {
				var step = 'ArrowRight' === e.key ? 1 : ( 'ArrowLeft' === e.key ? -1 : 0 );
				if ( step ) {
					e.preventDefault();
					select( tabs[ ( i + step + tabs.length ) % tabs.length ].getAttribute( 'data-tab' ), true );
				}
			} );
		} );

		select( tabs[ 0 ].getAttribute( 'data-tab' ) );
		root.classList.add( 'is-ready' );
		return select;
	}

	function money( value ) {
		return Number( value ).toLocaleString( 'vi-VN' ) + 'đ';
	}

	/**
	 * The consult buttons carry the product name into the lead form;
	 * append what the visitor picked, e.g.
	 * "Áo Bóng Đá Basic – Size: L, Màu sắc: Đỏ, Vải: Mè Hàn 2C (230.000đ/bộ)".
	 * Reads the WooCommerce variation selects (size/color) and the fabric
	 * picker (data-sk-fabric), which also updates the displayed prices.
	 */
	function initConsultOptions( form, fabric ) {
		var buttons = Array.prototype.slice.call( document.querySelectorAll( '[data-sk-consult-product]' ) );
		if ( ! buttons.length ) {
			return;
		}
		var base = buttons[ 0 ].getAttribute( 'data-product-name' ) || '';

		function updateFabricPrices( input ) {
			var price = input ? Number( input.getAttribute( 'data-price' ) ) : 0;
			var head  = document.querySelector( '[data-sk-fabric-price]' );
			if ( head ) {
				head.textContent = money( price || head.getAttribute( 'data-from' ) );
				var from = document.querySelector( '.sk-product__price-from' );
				var unit = document.querySelector( '[data-sk-fabric-unit]' );
				if ( from ) {
					from.hidden = !! price;
				}
				if ( unit ) {
					unit.textContent = price ? '/ bộ — giá chất vải đã chọn, xem ưu đãi theo số lượng bên dưới' : unit.getAttribute( 'data-default' );
				}
			}
			fabric.querySelectorAll( '[data-percent]' ).forEach( function ( row ) {
				var cell = row.querySelector( '[data-sk-tier-price]' );
				if ( ! cell ) {
					return;
				}
				var off = Number( row.getAttribute( 'data-percent' ) ) / 100;
				cell.textContent = price ? money( Math.round( price * ( 1 - off ) / 1000 ) * 1000 ) : 'Chọn chất vải';
			} );
		}

		function sync() {
			var picked = [];
			if ( form ) {
				form.querySelectorAll( 'table.variations select' ).forEach( function ( select ) {
					if ( ! select.value ) {
						return;
					}
					var row   = select.closest( 'tr' );
					var label = row ? row.querySelector( 'th label, .label label' ) : null;
					var text  = select.options[ select.selectedIndex ].textContent.trim();
					// The label also holds the ": value" span from product-variations.js.
					var name  = label ? ( label.firstChild ? label.firstChild.textContent : label.textContent ).trim() : '';
					picked.push( ( name ? name + ': ' : '' ) + text );
				} );
			}
			if ( fabric ) {
				var chosen = fabric.querySelector( 'input[name="sk_fabric"]:checked' );
				updateFabricPrices( chosen );
				if ( chosen ) {
					picked.push( 'Vải: ' + chosen.value + ' (' + money( chosen.getAttribute( 'data-price' ) ) + '/bộ)' );
				}
			}
			buttons.forEach( function ( button ) {
				button.setAttribute( 'data-product-name', picked.length ? base + ' – ' + picked.join( ', ' ) : base );
			} );
		}
		if ( form ) {
			form.addEventListener( 'change', sync );
		}
		if ( fabric ) {
			fabric.addEventListener( 'change', sync );
		}
		sync();
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '[data-sk-gallery]' ).forEach( initGallery );
		initConsultOptions( document.querySelector( 'form.variations_form' ), document.querySelector( '[data-sk-fabric]' ) );

		var tabsRoot = document.querySelector( '[data-sk-tabs]' );
		var select   = tabsRoot ? initTabs( tabsRoot ) : null;
		if ( ! select ) {
			return;
		}
		function openFromHash( hash ) {
			var match = /^#tab-(.+)$/.exec( hash || '' );
			if ( match && tabsRoot.querySelector( '[data-tab="' + match[ 1 ] + '"]' ) ) {
				select( match[ 1 ] );
				tabsRoot.scrollIntoView( { behavior: 'smooth', block: 'start' } );
				return true;
			}
			return false;
		}
		document.querySelectorAll( '[data-sk-open-tab]' ).forEach( function ( link ) {
			link.addEventListener( 'click', function ( e ) {
				if ( openFromHash( '#tab-' + link.getAttribute( 'data-sk-open-tab' ) ) ) {
					e.preventDefault();
				}
			} );
		} );
		openFromHash( window.location.hash );
	} );
}());

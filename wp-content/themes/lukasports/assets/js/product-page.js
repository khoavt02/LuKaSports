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
				} );
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

	/**
	 * The consult button carries the product name into the lead form;
	 * append the size/color the visitor picked, e.g.
	 * "Áo Bóng Đá Basic – Size: L, Màu sắc: Đỏ".
	 */
	function initConsultOptions( form ) {
		var button = document.querySelector( '[data-sk-consult-product]' );
		if ( ! button ) {
			return;
		}
		var base = button.getAttribute( 'data-product-name' ) || '';
		function sync() {
			var picked = [];
			form.querySelectorAll( 'table.variations select' ).forEach( function ( select ) {
				if ( ! select.value ) {
					return;
				}
				var row   = select.closest( 'tr' );
				var label = row ? row.querySelector( 'th label, .label label' ) : null;
				var text  = select.options[ select.selectedIndex ].textContent.trim();
				picked.push( ( label ? label.textContent.trim() + ': ' : '' ) + text );
			} );
			button.setAttribute( 'data-product-name', picked.length ? base + ' – ' + picked.join( ', ' ) : base );
		}
		form.addEventListener( 'change', sync );
		sync();
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '[data-sk-gallery]' ).forEach( initGallery );
		document.querySelectorAll( 'form.variations_form' ).forEach( initConsultOptions );

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

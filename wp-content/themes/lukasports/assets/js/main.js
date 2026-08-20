(function () {
	'use strict';

	/* ==========================================================================
	   Event abstraction (Phase 2, Part 7)
	   Every trackable interaction funnels through lukasportsTrack(). Meta
	   Pixel / GA4 can be wired in later by listening for the
	   "lukasports:track" DOM event — no call site here needs to change.
	   ========================================================================== */
	window.lukasportsEvents = window.lukasportsEvents || [];

	function lukasportsTrack( name, data ) {
		data = data || {};
		window.lukasportsEvents.push( { name: name, data: data, ts: Date.now() } );
		document.dispatchEvent( new CustomEvent( 'lukasports:track', { detail: { name: name, data: data } } ) );
	}
	window.lukasportsTrack = lukasportsTrack;

	function flushEventQueue() {
		var queue = window.lukasportsEventQueue || [];
		queue.forEach( function ( item ) {
			lukasportsTrack( item[ 0 ], item[ 1 ] );
		} );
		window.lukasportsEventQueue = [];
	}
	flushEventQueue();

	/* ==========================================================================
	   First-touch UTM / fbclid attribution (Phase 2, Part 6)
	   Written once per visitor and never overwritten, so a Facebook Ad
	   click that lands on the homepage still gets credited if the
	   consultation happens several pages later.
	   ========================================================================== */
	var ATTRIBUTION_COOKIE = 'lukasports_attribution';
	var ATTRIBUTION_DAYS   = 30;

	function getCookie( name ) {
		var match = document.cookie.match( '(?:^|; )' + name.replace( /([.$?*|{}()[\]\\/+^])/g, '\\$1' ) + '=([^;]*)' );
		return match ? decodeURIComponent( match[ 1 ] ) : null;
	}

	function setCookie( name, value, days ) {
		var expires = new Date( Date.now() + days * 24 * 60 * 60 * 1000 ).toUTCString();
		document.cookie = name + '=' + encodeURIComponent( value ) + '; expires=' + expires + '; path=/; SameSite=Lax';
	}

	function captureAttribution() {
		if ( getCookie( ATTRIBUTION_COOKIE ) ) {
			return;
		}

		var params = new URLSearchParams( window.location.search );
		var data   = {
			utm_source:   params.get( 'utm_source' ) || '',
			utm_medium:   params.get( 'utm_medium' ) || '',
			utm_campaign: params.get( 'utm_campaign' ) || '',
			utm_content:  params.get( 'utm_content' ) || '',
			utm_term:     params.get( 'utm_term' ) || '',
			fbclid:       params.get( 'fbclid' ) || '',
			landing_page: window.location.href,
			referrer:     document.referrer || '',
		};

		setCookie( ATTRIBUTION_COOKIE, JSON.stringify( data ), ATTRIBUTION_DAYS );
	}
	captureAttribution();

	/* ==========================================================================
	   Click tracking: Messenger/Zalo/phone links are detected by href
	   pattern, so any such link anywhere on the site is tracked
	   automatically without extra markup.
	   ========================================================================== */
	function hrefEventName( href ) {
		if ( ! href ) {
			return null;
		}
		if ( href.indexOf( 'm.me/' ) !== -1 || href.indexOf( 'messenger.com' ) !== -1 ) {
			return 'click_messenger';
		}
		if ( href.indexOf( 'zalo.me' ) !== -1 ) {
			return 'click_zalo';
		}
		if ( href.indexOf( 'tel:' ) === 0 ) {
			return 'click_phone';
		}
		return null;
	}

	document.addEventListener( 'click', function ( e ) {
		var el = e.target.closest( 'a[href], [data-lukasports-cta]' );
		if ( ! el ) {
			return;
		}

		var ctaType = el.getAttribute( 'data-lukasports-cta' );

		if ( 'consult' === ctaType ) {
			e.preventDefault();
			lukasportsTrack( 'click_consultation', { source: el.getAttribute( 'data-source' ) || 'unknown' } );
			openConsultationModal( el );
			return;
		}

		if ( 'design_team' === ctaType ) {
			lukasportsTrack( 'click_design_team', {} );
		}

		var evt = hrefEventName( el.getAttribute( 'href' ) );
		if ( evt ) {
			lukasportsTrack( evt, { href: el.getAttribute( 'href' ) } );
		}
	} );

	/* ==========================================================================
	   Consultation modal
	   ========================================================================== */
	function openConsultationModal( trigger ) {
		var modal = document.getElementById( 'sk-consultation-modal' );
		if ( ! modal ) {
			return;
		}

		var form    = document.getElementById( 'sk-consultation-form' );
		var success = modal.querySelector( '[data-sk-modal-success]' );
		if ( form ) {
			form.hidden = false;
			clearFormErrors( form );
		}
		if ( success ) {
			success.hidden = true;
		}

		var productIdField   = document.getElementById( 'sk-lead-product-id' );
		var productNameField = document.getElementById( 'sk-lead-product-name' );
		var sourceField       = document.getElementById( 'sk-lead-source' );

		if ( productIdField ) {
			productIdField.value = ( trigger && trigger.getAttribute( 'data-product-id' ) ) || '';
		}
		if ( productNameField ) {
			productNameField.value = ( trigger && trigger.getAttribute( 'data-product-name' ) ) || '';
		}
		if ( sourceField ) {
			sourceField.value = ( trigger && trigger.getAttribute( 'data-source' ) ) || 'website';
		}

		modal.hidden = false;
		modal.setAttribute( 'aria-hidden', 'false' );
		document.documentElement.classList.add( 'sk-modal-open' );

		var nameField = document.getElementById( 'sk-lead-name' );
		if ( nameField ) {
			nameField.focus();
		}
	}

	function closeConsultationModal() {
		var modal = document.getElementById( 'sk-consultation-modal' );
		if ( ! modal ) {
			return;
		}
		modal.hidden = true;
		modal.setAttribute( 'aria-hidden', 'true' );
		document.documentElement.classList.remove( 'sk-modal-open' );
	}

	function clearFormErrors( form ) {
		form.querySelectorAll( '[data-error-for]' ).forEach( function ( el ) {
			el.textContent = '';
		} );
		form.querySelectorAll( '.sk-field--invalid' ).forEach( function ( el ) {
			el.classList.remove( 'sk-field--invalid' );
		} );
		var formError = form.querySelector( '[data-form-error]' );
		if ( formError ) {
			formError.hidden = true;
			formError.textContent = '';
		}
	}

	function showFieldError( form, field, message ) {
		var errorEl = form.querySelector( '[data-error-for="' + field + '"]' );
		if ( errorEl ) {
			errorEl.textContent = message;
		}
		var input = form.elements[ field ];
		if ( input ) {
			input.classList.add( 'sk-field--invalid' );
		}
	}

	function showFormError( form, message ) {
		var formError = form.querySelector( '[data-form-error]' );
		if ( formError ) {
			formError.hidden = false;
			formError.textContent = message;
		}
	}

	function isValidVnPhone( phone ) {
		var normalized = phone.replace( /[\s.\-()]/g, '' );
		return /^(\+84|0)(3|5|7|8|9)[0-9]{8}$/.test( normalized );
	}

	function initConsultationForm() {
		var form = document.getElementById( 'sk-consultation-form' );
		if ( ! form ) {
			return;
		}

		document.querySelectorAll( '[data-sk-modal-close]' ).forEach( function ( el ) {
			el.addEventListener( 'click', closeConsultationModal );
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key ) {
				closeConsultationModal();
			}
		} );

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			clearFormErrors( form );

			var name  = form.elements.name.value.trim();
			var phone = form.elements.phone.value.trim();
			var hasError = false;

			if ( '' === name ) {
				showFieldError( form, 'name', 'Vui lòng nhập tên của bạn.' );
				hasError = true;
			}
			if ( ! isValidVnPhone( phone ) ) {
				showFieldError( form, 'phone', 'Số điện thoại chưa đúng, vui lòng kiểm tra lại.' );
				hasError = true;
			}
			if ( hasError ) {
				return;
			}

			var submitBtn      = form.querySelector( 'button[type="submit"]' );
			var originalLabel  = submitBtn ? submitBtn.textContent : '';
			if ( submitBtn ) {
				submitBtn.disabled = true;
				submitBtn.textContent = 'Đang gửi…';
			}

			var formData = new FormData( form );
			formData.set( 'action', 'lukasports_submit_lead' );

			fetch( form.getAttribute( 'data-ajax-url' ), {
				method: 'POST',
				body: formData,
				credentials: 'same-origin',
			} )
				.then( function ( res ) {
					return res.json();
				} )
				.then( function ( json ) {
					if ( submitBtn ) {
						submitBtn.disabled = false;
						submitBtn.textContent = originalLabel;
					}
					if ( json && json.success ) {
						lukasportsTrack( 'submit_consultation', {} );
						form.hidden = true;
						var success = document.querySelector( '[data-sk-modal-success]' );
						var message = document.querySelector( '[data-sk-modal-success-message]' );
						if ( message ) {
							message.textContent = ( json.data && json.data.message ) || 'Đã gửi yêu cầu tư vấn!';
						}
						if ( success ) {
							success.hidden = false;
						}
						form.reset();
					} else if ( json && json.data && json.data.errors ) {
						Object.keys( json.data.errors ).forEach( function ( field ) {
							showFieldError( form, field, json.data.errors[ field ] );
						} );
					} else {
						showFormError( form, ( json && json.data && json.data.message ) || 'Có lỗi xảy ra, vui lòng thử lại.' );
					}
				} )
				.catch( function () {
					if ( submitBtn ) {
						submitBtn.disabled = false;
						submitBtn.textContent = originalLabel;
					}
					showFormError( form, 'Không thể gửi yêu cầu, vui lòng kiểm tra kết nối và thử lại.' );
				} );
		} );
	}

	/* ==========================================================================
	   Existing UI: mobile nav / search toggles, gallery, sticky header
	   ========================================================================== */
	function toggle( button, panel ) {
		if ( ! button || ! panel ) {
			return;
		}
		button.addEventListener( 'click', function () {
			var expanded = button.getAttribute( 'aria-expanded' ) === 'true';
			button.setAttribute( 'aria-expanded', String( ! expanded ) );
			if ( expanded ) {
				panel.setAttribute( 'hidden', '' );
			} else {
				panel.removeAttribute( 'hidden' );
			}
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		toggle( document.querySelector( '[data-sk-nav-toggle]' ), document.getElementById( 'sk-mobile-nav' ) );
		toggle( document.querySelector( '[data-sk-search-toggle]' ), document.getElementById( 'sk-search-panel' ) );

		initConsultationForm();

		var galleryMain = document.getElementById( 'sk-gallery-main' );
		if ( galleryMain ) {
			document.querySelectorAll( '[data-sk-gallery-target]' ).forEach( function ( thumb ) {
				thumb.addEventListener( 'click', function () {
					var target = document.getElementById( thumb.getAttribute( 'data-sk-gallery-target' ) );
					if ( target ) {
						target.scrollIntoView( { behavior: 'smooth', inline: 'start', block: 'nearest' } );
					}
				} );
			} );
		}

		var header = document.getElementById( 'sk-header' );
		if ( header ) {
			var onScroll = function () {
				header.classList.toggle( 'is-scrolled', window.scrollY > 4 );
			};
			window.addEventListener( 'scroll', onScroll, { passive: true } );
			onScroll();
		}

		// Floating contact — collapsed FAB on desktop (see footer.css);
		// on mobile the menu is always visible via CSS so this class
		// toggle has no visible effect there, which is intentional.
		var floatingWrap   = document.querySelector( '.sk-floating-contact' );
		var floatingToggle = document.getElementById( 'sk-floating-toggle' );
		if ( floatingWrap && floatingToggle ) {
			floatingToggle.addEventListener( 'click', function () {
				var isOpen = floatingWrap.classList.toggle( 'is-open' );
				floatingToggle.setAttribute( 'aria-expanded', String( isOpen ) );
			} );
			document.addEventListener( 'click', function ( e ) {
				if ( floatingWrap.classList.contains( 'is-open' ) && ! floatingWrap.contains( e.target ) ) {
					floatingWrap.classList.remove( 'is-open' );
					floatingToggle.setAttribute( 'aria-expanded', 'false' );
				}
			} );
		}
	} );
} )();

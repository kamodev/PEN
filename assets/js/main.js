/**
 * Preparedness Education Network theme scripts.
 * Mobile nav, search panel, dismissible announcement bar, back-to-top.
 */
( function () {
	'use strict';

	var body = document.body;

	// Mobile navigation.
	var nav = document.getElementById( 'pen-nav' );
	var menuToggle = document.querySelector( '.pen-menu-toggle' );

	function setNav( open ) {
		if ( ! nav || ! menuToggle ) {
			return;
		}
		nav.classList.toggle( 'is-open', open );
		body.classList.toggle( 'pen-nav-open', open );
		menuToggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
	}

	if ( menuToggle && nav ) {
		menuToggle.addEventListener( 'click', function () {
			setNav( ! nav.classList.contains( 'is-open' ) );
		} );
		nav.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( 'a' ) ) {
				setNav( false );
			}
		} );
	}

	// Search panel.
	var searchToggle = document.querySelector( '.pen-search-toggle' );
	var searchPanel = document.getElementById( 'pen-search-panel' );

	if ( searchToggle && searchPanel ) {
		searchToggle.addEventListener( 'click', function () {
			var open = searchPanel.hasAttribute( 'hidden' );
			searchPanel.toggleAttribute( 'hidden', ! open );
			searchToggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( open ) {
				var input = searchPanel.querySelector( 'input[type="search"]' );
				if ( input ) {
					input.focus();
				}
			}
		} );
	}

	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' !== e.key ) {
			return;
		}
		if ( nav && nav.classList.contains( 'is-open' ) ) {
			setNav( false );
			menuToggle.focus();
		}
		if ( searchPanel && ! searchPanel.hasAttribute( 'hidden' ) ) {
			searchPanel.setAttribute( 'hidden', '' );
			searchToggle.setAttribute( 'aria-expanded', 'false' );
			searchToggle.focus();
		}
	} );

	// Dismissible announcement bar (remembered per message).
	var bar = document.getElementById( 'pen-announcement' );
	if ( bar ) {
		var storageKey = 'pen-announce-' + bar.getAttribute( 'data-key' );
		try {
			if ( window.localStorage.getItem( storageKey ) ) {
				bar.classList.add( 'is-hidden' );
			}
		} catch ( err ) {}

		var close = bar.querySelector( '.pen-announcement__close' );
		if ( close ) {
			close.addEventListener( 'click', function () {
				bar.classList.add( 'is-hidden' );
				try {
					window.localStorage.setItem( storageKey, '1' );
				} catch ( err ) {}
			} );
		}
	}

	// Back to top.
	var toTop = document.querySelector( '.pen-to-top' );
	if ( toTop ) {
		var onScroll = function () {
			toTop.classList.toggle( 'is-visible', window.scrollY > 600 );
		};
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
		toTop.addEventListener( 'click', function () {
			window.scrollTo( { top: 0 } );
		} );
	}
} )();

/**
 * Theme Settings screens: color pickers, presets, live preview and contrast checks.
 */
( function ( $ ) {
	'use strict';

	var i18n = window.penSettings || {};

	function hexToRgb( hex ) {
		hex = String( hex || '' ).replace( '#', '' );
		if ( 3 === hex.length ) {
			hex = hex.replace( /(.)/g, '$1$1' );
		}
		if ( ! /^[0-9a-f]{6}$/i.test( hex ) ) {
			return null;
		}
		var n = parseInt( hex, 16 );
		return [ ( n >> 16 ) & 255, ( n >> 8 ) & 255, n & 255 ];
	}

	function luminance( rgb ) {
		var c = rgb.map( function ( v ) {
			v /= 255;
			return v <= 0.03928 ? v / 12.92 : Math.pow( ( v + 0.055 ) / 1.055, 2.4 );
		} );
		return 0.2126 * c[ 0 ] + 0.7152 * c[ 1 ] + 0.0722 * c[ 2 ];
	}

	function contrast( a, b ) {
		var x = hexToRgb( a );
		var y = hexToRgb( b );
		if ( ! x || ! y ) {
			return null;
		}
		var l1 = luminance( x );
		var l2 = luminance( y );
		return ( Math.max( l1, l2 ) + 0.05 ) / ( Math.min( l1, l2 ) + 0.05 );
	}

	function initColors( $wrap ) {
		var $inputs = $wrap.find( '.pen-color-field' );
		var $preview = $wrap.find( '.pen-preview' );
		var $contrast = $wrap.find( '.pen-contrast' );
		var pairs = $contrast.data( 'pairs' ) || [];
		var locked = '1' === String( $wrap.data( 'disabled' ) );
		var pending = null;

		// Effective color for a key: typed value, else the inherited one.
		function resolved( key ) {
			if ( '#' === key.charAt( 0 ) ) {
				return key;
			}
			var $input = $inputs.filter( '[data-key="' + key + '"]' );
			return $.trim( $input.val() ) || $input.data( 'inherit' );
		}

		function update() {
			pending = null;
			$inputs.each( function () {
				var $input = $( this );
				$preview[ 0 ].style.setProperty( $input.data( 'var' ), resolved( $input.data( 'key' ) ) );
			} );

			$contrast.empty();
			pairs.forEach( function ( pair ) {
				var fg = resolved( pair[ 1 ] );
				var bg = resolved( pair[ 2 ] );
				var ratio = contrast( fg, bg );
				if ( null === ratio ) {
					return;
				}
				var low = ratio < 4.5;
				var $li = $( '<li/>' ).toggleClass( 'is-low', low );
				$( '<span class="pen-contrast__sample" aria-hidden="true">Aa</span>' ).css( { color: fg, background: bg } ).appendTo( $li );
				$( '<span class="pen-contrast__label"/>' ).text( pair[ 0 ] ).appendTo( $li );
				$( '<span class="pen-contrast__ratio"/>' ).text( ratio.toFixed( 1 ) + ':1' ).appendTo( $li );
				$( '<span class="pen-contrast__status"/>' ).text( low ? i18n.low : i18n.ok ).appendTo( $li );
				$contrast.append( $li );
			} );
		}

		function queue() {
			if ( ! pending ) {
				pending = window.setTimeout( update, 30 );
			}
		}

		if ( ! locked ) {
			$inputs.wpColorPicker( {
				change: queue,
				clear: queue,
			} );
			$inputs.on( 'input change', queue );

			$wrap.on( 'click', '.pen-preset', function () {
				var colors = $( this ).data( 'colors' ) || {};
				$inputs.each( function () {
					var key = $( this ).data( 'key' );
					if ( colors[ key ] ) {
						$( this ).wpColorPicker( 'color', colors[ key ] );
					}
				} );
				queue();
			} );

			$wrap.on( 'click', '.pen-clear-colors', function () {
				$inputs.each( function () {
					var $input = $( this );
					if ( $input.val() ) {
						$input.closest( '.wp-picker-container' ).find( '.wp-picker-clear' ).trigger( 'click' );
					}
				} );
				queue();
			} );
		}

		update();
	}

	$( function () {
		$( '.pen-colors' ).each( function () {
			initColors( $( this ) );
		} );
	} );
} )( jQuery );

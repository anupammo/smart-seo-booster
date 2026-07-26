/**
 * Smart SEO Booster — accessible tabs for the settings screen.
 * Implements the WAI-ARIA tabs pattern: roving tabindex + arrow/Home/End keys.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var tablist = document.querySelector( '.smart-seo-tabs[role="tablist"]' );
		if ( ! tablist ) {
			return;
		}

		var tabs = Array.prototype.slice.call( tablist.querySelectorAll( '[role="tab"]' ) );

		function panelFor( tab ) {
			return document.getElementById( tab.getAttribute( 'aria-controls' ) );
		}

		function activate( tab, setFocus ) {
			tabs.forEach( function ( t ) {
				var selected = t === tab;
				t.setAttribute( 'aria-selected', selected ? 'true' : 'false' );
				t.setAttribute( 'tabindex', selected ? '0' : '-1' );
				t.classList.toggle( 'nav-tab-active', selected );

				var panel = panelFor( t );
				if ( panel ) {
					panel.hidden = ! selected;
					panel.classList.toggle( 'is-active', selected );
				}
			} );

			if ( setFocus ) {
				tab.focus();
			}
			if ( window.history && window.history.replaceState ) {
				window.history.replaceState( null, '', '#' + tab.getAttribute( 'aria-controls' ) );
			}
		}

		tabs.forEach( function ( tab, index ) {
			tab.addEventListener( 'click', function () {
				activate( tab, false );
			} );

			tab.addEventListener( 'keydown', function ( e ) {
				var next;
				switch ( e.key ) {
					case 'ArrowRight':
					case 'ArrowDown':
						next = tabs[ ( index + 1 ) % tabs.length ];
						break;
					case 'ArrowLeft':
					case 'ArrowUp':
						next = tabs[ ( index - 1 + tabs.length ) % tabs.length ];
						break;
					case 'Home':
						next = tabs[ 0 ];
						break;
					case 'End':
						next = tabs[ tabs.length - 1 ];
						break;
					default:
						return;
				}
				e.preventDefault();
				activate( next, true );
			} );
		} );

		// Restore the tab referenced in the URL hash, if any.
		if ( window.location.hash ) {
			var target = tabs.filter( function ( t ) {
				return '#' + t.getAttribute( 'aria-controls' ) === window.location.hash;
			} )[ 0 ];
			if ( target ) {
				activate( target, false );
			}
		}
	} );
} )();

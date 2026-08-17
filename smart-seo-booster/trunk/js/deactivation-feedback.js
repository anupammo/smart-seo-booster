/**
 * Smart SEO Booster — deactivation feedback.
 *
 * Intercepts the Deactivate link on OUR plugin row only, offers a short
 * survey, and never prevents deactivation: every path out of the modal
 * either deactivates immediately or returns the user to the page untouched.
 */
( function () {
	'use strict';

	if ( typeof smartSeoFeedback === 'undefined' ) {
		return;
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		var overlay = document.getElementById( 'ssb-fb-overlay' );
		if ( ! overlay ) {
			return;
		}

		// Find the Deactivate link belonging to this plugin. WordPress gives
		// each row an id of `data-slug` or a deactivate link containing the
		// plugin file, so match on the href rather than on row position.
		var link = document.querySelector(
			'#the-list tr[data-slug="' + smartSeoFeedback.slug + '"] .deactivate a, ' +
			'#the-list tr[data-plugin*="' + smartSeoFeedback.slug + '/"] .deactivate a'
		);
		if ( ! link ) {
			return;
		}

		var deactivateUrl = link.getAttribute( 'href' );
		var lastFocused = null;

		function open( e ) {
			e.preventDefault();
			lastFocused = document.activeElement;
			overlay.hidden = false;
			document.body.classList.add( 'ssb-fb-open' );
			var first = overlay.querySelector( 'input[type="radio"]' );
			if ( first ) {
				first.focus();
			}
			document.addEventListener( 'keydown', onKeydown );
		}

		function close() {
			overlay.hidden = true;
			document.body.classList.remove( 'ssb-fb-open' );
			document.removeEventListener( 'keydown', onKeydown );
			if ( lastFocused ) {
				lastFocused.focus();
			}
		}

		function onKeydown( e ) {
			if ( e.key === 'Escape' ) {
				close();
			}
		}

		function deactivate() {
			window.location.href = deactivateUrl;
		}

		link.addEventListener( 'click', open );

		document.getElementById( 'ssb-fb-cancel' ).addEventListener( 'click', close );
		document.getElementById( 'ssb-fb-skip' ).addEventListener( 'click', deactivate );

		// Clicking the backdrop cancels (does NOT deactivate) — the safer
		// interpretation of an accidental click.
		overlay.addEventListener( 'click', function ( e ) {
			if ( e.target === overlay ) {
				close();
			}
		} );

		document.getElementById( 'ssb-fb-send' ).addEventListener( 'click', function () {
			var checked = overlay.querySelector( 'input[name="ssb_fb_reason"]:checked' );
			var details = document.getElementById( 'ssb-fb-details' ).value.trim();

			// No reason picked behaves exactly like "skip" rather than
			// nagging the user into answering.
			if ( ! checked ) {
				deactivate();
				return;
			}

			var url = smartSeoFeedback.feedbackUrl +
				( smartSeoFeedback.feedbackUrl.indexOf( '?' ) === -1 ? '?' : '&' ) +
				'utm_source=plugin&utm_medium=deactivation&utm_campaign=feedback' +
				'&reason=' + encodeURIComponent( checked.value );

			if ( details ) {
				url += '&details=' + encodeURIComponent( details.substring( 0, 500 ) );
			}

			// Open the feedback page in a new tab so the user can see what is
			// being sent, then deactivate in this one.
			window.open( url, '_blank', 'noopener' );
			deactivate();
		} );
	} );
} )();

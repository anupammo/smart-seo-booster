/**
 * Smart SEO Booster — front-end block behavior (copy-link button).
 */
( function () {
	'use strict';
	document.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest && e.target.closest( '.ssb-share-copy' );
		if ( ! btn ) {
			return;
		}
		e.preventDefault();
		var url = btn.getAttribute( 'data-url' );
		if ( navigator.clipboard && url ) {
			navigator.clipboard.writeText( url );
		}
		btn.classList.add( 'is-copied' );
		setTimeout( function () {
			btn.classList.remove( 'is-copied' );
		}, 1500 );
	} );
} )();

/**
 * Smart SEO Booster — SEO score surfaces (post list + edit screen).
 * Handles the "Refresh Analysis" action and the full-report modal.
 */
( function () {
	'use strict';

	var data = window.SmartSEOScore || { ajaxUrl: window.ajaxurl, nonce: '' };

	window.smartSeoRefreshScore = function ( postId ) {
		var button = jQuery( '#smart-seo-refresh-btn' );
		var label = button.find( '.smart-seo-refresh-label' );
		var originalText = label.text();
		label.text( data.refreshingText || 'Refreshing…' );
		button.prop( 'disabled', true );

		function restore() {
			label.text( originalText );
			button.prop( 'disabled', false );
		}

		jQuery
			.post( data.ajaxUrl, {
				action: 'get_seo_score',
				post_id: postId,
				nonce: data.nonce,
			}, function ( response ) {
				if ( response.success ) {
					location.reload();
				} else {
					window.alert( data.refreshErrorText || 'Error refreshing SEO analysis. Please try again.' );
					restore();
				}
			} )
			.fail( function () {
				window.alert( data.refreshErrorText || 'Error refreshing SEO analysis. Please try again.' );
				restore();
			} );
	};

	window.smartSeoShowFullReport = function ( postId ) {
		var modal = jQuery( '<div>', {
			id: 'smart-seo-modal',
			css: {
				position: 'fixed',
				top: 0,
				left: 0,
				width: '100%',
				height: '100%',
				backgroundColor: 'rgba(0,0,0,0.7)',
				zIndex: 999999,
				display: 'flex',
				alignItems: 'center',
				justifyContent: 'center',
			},
		} );

		var content = jQuery( '<div>', {
			css: {
				backgroundColor: 'white',
				padding: '30px',
				borderRadius: '8px',
				maxWidth: '800px',
				maxHeight: '80vh',
				overflow: 'auto',
				position: 'relative',
				boxSizing: 'border-box',
			},
			html: '<div style="text-align: center;"><h2>Full SEO Report</h2><p>Loading detailed analysis…</p></div>',
		} );

		// A static close button markup string, re-inserted whenever content
		// is replaced. Its click handler is bound once via delegation on the
		// modal itself (below), so it keeps working no matter how many times
		// .html() swaps the button out for a fresh element.
		var closeBtnHtml = '<button type="button" class="smart-seo-modal-close" aria-label="Close">&times;</button>';

		content.append( closeBtnHtml );
		modal.append( content );
		jQuery( 'body' ).append( modal );

		// Delegated handlers survive content.html() replacing the DOM nodes.
		modal.on( 'click', '.smart-seo-modal-close', function () {
			modal.remove();
		} );
		modal.on( 'click', function ( e ) {
			if ( e.target === modal[ 0 ] ) {
				modal.remove();
			}
		} );
		jQuery( document ).on( 'keydown.smartSeoModal', function ( e ) {
			if ( e.key === 'Escape' ) {
				modal.remove();
				jQuery( document ).off( 'keydown.smartSeoModal' );
			}
		} );

		jQuery
			.post( data.ajaxUrl, {
				action: 'get_full_seo_report',
				post_id: postId,
				nonce: data.nonce,
			}, function ( response ) {
				if ( response.success ) {
					content.html( response.data.html + closeBtnHtml );
				} else {
					content.html( '<h2>Error</h2><p>Could not load SEO report.</p>' + closeBtnHtml );
				}
			} )
			.fail( function () {
				content.html( '<h2>Error</h2><p>Could not load SEO report.</p>' + closeBtnHtml );
			} );
	};
} )();

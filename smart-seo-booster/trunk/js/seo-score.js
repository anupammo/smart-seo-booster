/**
 * Smart SEO Booster — SEO score surfaces (post list + edit screen).
 * Handles the "Refresh Analysis" action and the full-report modal.
 */
( function () {
	'use strict';

	var data = window.SmartSEOScore || { ajaxUrl: window.ajaxurl, nonce: '' };

	window.smartSeoRefreshScore = function ( postId ) {
		var button = jQuery( 'button:contains("Refresh Analysis")' );
		var originalText = button.text();
		button.text( '🔄 Refreshing...' ).prop( 'disabled', true );

		jQuery
			.post( data.ajaxUrl, {
				action: 'get_seo_score',
				post_id: postId,
				nonce: data.nonce,
			}, function ( response ) {
				if ( response.success ) {
					location.reload();
				} else {
					window.alert( 'Error refreshing SEO analysis. Please try again.' );
					button.text( originalText ).prop( 'disabled', false );
				}
			} )
			.fail( function () {
				window.alert( 'Error refreshing SEO analysis. Please try again.' );
				button.text( originalText ).prop( 'disabled', false );
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
			},
			html: '<div style="text-align: center;"><h2>📊 Full SEO Report</h2><p>Loading detailed analysis...</p></div>',
		} );

		var closeBtn = jQuery( '<button>', {
			text: '×',
			css: {
				position: 'absolute',
				top: '10px',
				right: '15px',
				background: 'none',
				border: 'none',
				fontSize: '24px',
				cursor: 'pointer',
				color: '#666',
			},
			click: function () {
				modal.remove();
			},
		} );

		content.append( closeBtn );
		modal.append( content );
		jQuery( 'body' ).append( modal );

		jQuery
			.post( data.ajaxUrl, {
				action: 'get_full_seo_report',
				post_id: postId,
				nonce: data.nonce,
			}, function ( response ) {
				if ( response.success ) {
					content.html( response.data.html + closeBtn[ 0 ].outerHTML );
				} else {
					content.html(
						'<h2>Error</h2><p>Could not load SEO report.</p>' + closeBtn[ 0 ].outerHTML
					);
				}
			} )
			.fail( function () {
				content.html(
					'<h2>Error</h2><p>Could not load SEO report.</p>' + closeBtn[ 0 ].outerHTML
				);
			} );

		modal.on( 'click', function ( e ) {
			if ( e.target === modal[ 0 ] ) {
				modal.remove();
			}
		} );
	};
} )();

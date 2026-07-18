/**
 * Smart SEO Booster — SEO meta box (post/page edit screen).
 * Tabs, character counters, live search/social previews, media upload,
 * focus-keyword analysis, and the on-demand full SEO report.
 */
( function ( $ ) {
	'use strict';

	var data = window.SmartSEOMeta || { ajaxUrl: window.ajaxurl, nonce: '' };

	$( function () {
		// Tab switching
		$( '.seo-tab-btn' ).on( 'click', function () {
			var tab = $( this ).data( 'tab' );
			$( '.seo-tab-btn' ).removeClass( 'active' );
			$( '.seo-tab-content' ).removeClass( 'active' );
			$( this ).addClass( 'active' );
			$( '#' + tab + '-tab' ).addClass( 'active' );

			if ( tab === 'analysis' ) {
				loadSeoAnalysis();
			}
		} );

		// Character counters
		function updateCounter( inputId, counterId, min, max ) {
			var input = $( '#' + inputId );
			var counter = $( '#' + counterId );
			var length = input.val().length;

			counter.text( length + ' characters (' + min + '-' + max + ' optimal)' );

			if ( length >= min && length <= max ) {
				counter.removeClass( 'warning error' ).addClass( 'good' );
			} else if ( length >= min * 0.8 && length <= max * 1.2 ) {
				counter.removeClass( 'good error' ).addClass( 'warning' );
			} else {
				counter.removeClass( 'good warning' ).addClass( 'error' );
			}
		}

		$( '#smart_seo_title' ).on( 'input', function () {
			updateCounter( 'smart_seo_title', 'title-counter', 30, 60 );
			updatePreview();
		} );

		$( '#smart_seo_description' ).on( 'input', function () {
			updateCounter( 'smart_seo_description', 'description-counter', 120, 160 );
			updatePreview();
		} );

		$( '#smart_seo_og_title' ).on( 'input', function () {
			updateCounter( 'smart_seo_og_title', 'og-title-counter', 40, 60 );
			updateSocialPreview();
		} );

		$( '#smart_seo_og_description' ).on( 'input', function () {
			updateCounter( 'smart_seo_og_description', 'og-description-counter', 130, 160 );
			updateSocialPreview();
		} );

		// Initial counter updates
		updateCounter( 'smart_seo_title', 'title-counter', 30, 60 );
		updateCounter( 'smart_seo_description', 'description-counter', 120, 160 );
		updateCounter( 'smart_seo_og_title', 'og-title-counter', 40, 60 );
		updateCounter( 'smart_seo_og_description', 'og-description-counter', 130, 160 );

		function updatePreview() {
			var title = $( '#smart_seo_title' ).val() || 'Your SEO Title';
			var description =
				$( '#smart_seo_description' ).val() ||
				'Your meta description will appear here...';

			$( '#preview-title' ).text( title );
			$( '#preview-description' ).text( description );
		}

		function updateSocialPreview() {
			var ogTitle = $( '#smart_seo_og_title' ).val() || 'Your OG Title';
			var ogDescription = $( '#smart_seo_og_description' ).val() || 'Your OG description...';
			var twitterTitle = $( '#smart_seo_twitter_title' ).val() || ogTitle;
			var twitterDescription = $( '#smart_seo_twitter_description' ).val() || ogDescription;

			$( '#facebook-preview-title' ).text( ogTitle );
			$( '#facebook-preview-desc' ).text( ogDescription );
			$( '#twitter-preview-title' ).text( twitterTitle );
			$( '#twitter-preview-desc' ).text( twitterDescription );
		}

		// Image upload handlers
		var mediaUploader;

		$( '#upload-og-image' ).on( 'click', function ( e ) {
			e.preventDefault();

			if ( mediaUploader ) {
				mediaUploader.open();
				return;
			}

			mediaUploader = wp.media( {
				title: 'Choose OG Image',
				button: { text: 'Choose Image' },
				multiple: false,
			} );

			mediaUploader.on( 'select', function () {
				var attachment = mediaUploader.state().get( 'selection' ).first().toJSON();
				$( '#smart_seo_og_image' ).val( attachment.url );
				$( '#og-image-preview' ).html(
					$( '<img>', { src: attachment.url, alt: 'OG Image Preview' } )
				);
				updateSocialPreview();
			} );

			mediaUploader.open();
		} );

		$( '#upload-twitter-image' ).on( 'click', function ( e ) {
			e.preventDefault();

			mediaUploader = wp.media( {
				title: 'Choose Twitter Image',
				button: { text: 'Choose Image' },
				multiple: false,
			} );

			mediaUploader.on( 'select', function () {
				var attachment = mediaUploader.state().get( 'selection' ).first().toJSON();
				$( '#smart_seo_twitter_image' ).val( attachment.url );
				$( '#twitter-image-preview' ).html(
					$( '<img>', { src: attachment.url, alt: 'Twitter Image Preview' } )
				);
				updateSocialPreview();
			} );

			mediaUploader.open();
		} );

		// Focus keyword analysis
		$( '#smart_seo_focus_keyword' ).on( 'input', function () {
			var keyword = $( this ).val();
			if ( keyword.length > 2 ) {
				analyzeKeyword( keyword );
			} else {
				$( '#keyword-analysis' ).hide();
			}
		} );

		function analyzeKeyword( keyword ) {
			var content = '';
			if ( typeof wp !== 'undefined' && wp.data && wp.data.select( 'core/editor' ) ) {
				content = wp.data.select( 'core/editor' ).getEditedPostContent();
			} else {
				content = $( '#content' ).val() || '';
			}

			var title = $( '#smart_seo_title' ).val() || $( '#title' ).val() || '';
			var description = $( '#smart_seo_description' ).val() || '';

			var keywordRegex = new RegExp( keyword.toLowerCase().replace( /[.*+?^${}()|[\]\\]/g, '\\$&' ), 'gi' );
			var contentLower = content.toLowerCase();
			var titleLower = title.toLowerCase();
			var descLower = description.toLowerCase();

			var contentMatches = ( contentLower.match( keywordRegex ) || [] ).length;
			var titleMatches = ( titleLower.match( keywordRegex ) || [] ).length;
			var descMatches = ( descLower.match( keywordRegex ) || [] ).length;

			var wordCount = content.split( /\s+/ ).length;
			var density = wordCount > 0 ? ( ( contentMatches / wordCount ) * 100 ).toFixed( 2 ) : 0;

			var analysis = '<strong>Keyword Analysis:</strong><br>';
			analysis += 'Content: ' + contentMatches + ' times ';
			analysis +=
				'<span class="seo-score-indicator ' +
				( contentMatches >= 1 ? 'seo-score-good' : 'seo-score-error' ) +
				'">' +
				( contentMatches >= 1 ? 'Good' : 'Add more' ) +
				'</span><br>';
			analysis += 'Title: ' + titleMatches + ' times ';
			analysis +=
				'<span class="seo-score-indicator ' +
				( titleMatches >= 1 ? 'seo-score-good' : 'seo-score-warning' ) +
				'">' +
				( titleMatches >= 1 ? 'Good' : 'Consider adding' ) +
				'</span><br>';
			analysis += 'Description: ' + descMatches + ' times ';
			analysis +=
				'<span class="seo-score-indicator ' +
				( descMatches >= 1 ? 'seo-score-good' : 'seo-score-warning' ) +
				'">' +
				( descMatches >= 1 ? 'Good' : 'Consider adding' ) +
				'</span><br>';
			analysis += 'Density: ' + density + '% ';
			analysis +=
				'<span class="seo-score-indicator ' +
				( density >= 0.5 && density <= 2.5 ? 'seo-score-good' : 'seo-score-warning' ) +
				'">' +
				( density >= 0.5 && density <= 2.5 ? 'Optimal' : 'Adjust' ) +
				'</span>';

			$( '#keyword-density' ).html( analysis );
			$( '#keyword-analysis' ).show();
		}

		function loadSeoAnalysis() {
			var postId = $( '#post_ID' ).val();
			if ( ! postId ) {
				return;
			}

			$( '#seo-analysis-content' ).html( '<p>Loading comprehensive SEO analysis...</p>' );

			$.post(
				data.ajaxUrl,
				{
					action: 'get_full_seo_report',
					post_id: postId,
					nonce: data.nonce,
				},
				function ( response ) {
					if ( response.success ) {
						$( '#seo-analysis-content' ).html( response.data.html );
					} else {
						$( '#seo-analysis-content' ).html( '<p>Error loading analysis.</p>' );
					}
				}
			);
		}

		// Refresh preview when switching between visual/text editor
		$( '#content-tmce, #content-html' ).on( 'click', function () {
			setTimeout( updatePreview, 500 );
		} );
	} );
} )( jQuery );

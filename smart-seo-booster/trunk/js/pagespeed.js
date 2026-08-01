/**
 * Smart SEO Booster — Page Speed report.
 * Calls the smart_seo_pagespeed_check AJAX action and renders category
 * gauges + a Core Web Vitals table using the same visual language as the
 * SEO Audit Report (ssb-gauge / ssb-pill / ssb-card).
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var runBtn    = document.getElementById( 'ssb-ps-run' );
		var urlInput  = document.getElementById( 'ssb-ps-url' );
		var strategy  = document.getElementById( 'ssb-ps-strategy' );
		var status    = document.getElementById( 'ssb-ps-status' );
		var results   = document.getElementById( 'ssb-ps-results' );
		var scoresBox = document.getElementById( 'ssb-ps-scores' );
		var vitalsBox = document.getElementById( 'ssb-ps-vitals' );
		var vitalsSrc = document.getElementById( 'ssb-ps-vitals-source' );

		if ( ! runBtn || typeof smartSeoPageSpeed === 'undefined' ) {
			return;
		}

		function gaugeSvg( score ) {
			var color = score >= 90 ? '#0cce6b' : ( score >= 50 ? '#ffa400' : '#ff4e42' );
			var r = 34, circ = 2 * Math.PI * r;
			var off = circ * ( 1 - ( score || 0 ) / 100 );
			return '<svg width="80" height="80" viewBox="0 0 80 80">' +
				'<circle cx="40" cy="40" r="' + r + '" fill="none" stroke="var(--ssb-line,#dcdcde)" stroke-width="7"/>' +
				'<circle cx="40" cy="40" r="' + r + '" fill="none" stroke="' + color + '" stroke-width="7" stroke-linecap="round" ' +
				'stroke-dasharray="' + circ + '" stroke-dashoffset="' + off + '" transform="rotate(-90 40 40)"/>' +
				'<text x="40" y="46" text-anchor="middle" font-size="20" font-weight="700" fill="' + color + '">' + ( score === null ? '–' : score ) + '</text>' +
				'</svg>';
		}

		function scoreCard( label, score ) {
			var div = document.createElement( 'div' );
			div.className = 'ssb-card ssb-ps-score-card';
			div.innerHTML = '<h2>' + label + '</h2><div class="ssb-ps-gauge-wrap">' + gaugeSvg( score ) + '</div>';
			return div;
		}

		function fmtVital( key, value ) {
			if ( value === null || typeof value === 'undefined' ) {
				return '—';
			}
			if ( key === 'lcp' ) {
				return value.toFixed( 1 ) + ' s';
			}
			if ( key === 'cls' ) {
				return value.toFixed( 3 );
			}
			return Math.round( value ) + ' ms';
		}

		function bucket( key, value ) {
			if ( value === null || typeof value === 'undefined' ) {
				return 'unknown';
			}
			if ( key === 'lcp' ) {
				return value <= 2.5 ? 'good' : ( value <= 4 ? 'needs' : 'poor' );
			}
			if ( key === 'cls' ) {
				return value <= 0.1 ? 'good' : ( value <= 0.25 ? 'needs' : 'poor' );
			}
			return value <= 200 ? 'good' : ( value <= 500 ? 'needs' : 'poor' );
		}

		function bucketLabel( b ) {
			return { good: 'Good', needs: 'Needs improvement', poor: 'Poor', unknown: '—' }[ b ] || '—';
		}

		function vitalRow( label, key, value ) {
			var b = bucket( key, value );
			var tr = document.createElement( 'tr' );
			tr.innerHTML = '<td>' + label + '</td><td>' + fmtVital( key, value ) + '</td>' +
				'<td><span class="ssb-pill ' + ( b === 'unknown' ? '' : b ) + '">' + bucketLabel( b ) + '</span></td>';
			return tr;
		}

		function render( data ) {
			scoresBox.innerHTML = '';
			scoresBox.appendChild( scoreCard( 'Performance', data.scores.performance ) );
			scoresBox.appendChild( scoreCard( 'SEO', data.scores.seo ) );
			scoresBox.appendChild( scoreCard( 'Accessibility', data.scores.accessibility ) );
			scoresBox.appendChild( scoreCard( 'Best Practices', data.scores.best_practices ) );

			vitalsBox.innerHTML = '';
			vitalsBox.appendChild( vitalRow( 'Largest Contentful Paint (LCP)', 'lcp', data.vitals.lcp ) );
			vitalsBox.appendChild( vitalRow( 'Cumulative Layout Shift (CLS)', 'cls', data.vitals.cls ) );
			vitalsBox.appendChild( vitalRow( data.has_field_data ? 'Interaction to Next Paint (INP)' : 'Total Blocking Time (TBT, lab)', data.has_field_data ? 'inp' : 'tbt', data.has_field_data ? data.vitals.inp : data.vitals.tbt ) );

			vitalsSrc.textContent = data.has_field_data
				? 'Based on real-user (CrUX) field data for this URL, plus the ' + ( data.cached ? 'cached' : 'latest' ) + ' Lighthouse run.'
				: 'No Chrome UX Report field data yet for this URL (low traffic) — showing this Lighthouse run’s lab data instead.';

			results.hidden = false;
		}

		runBtn.addEventListener( 'click', function () {
			var url = urlInput.value || smartSeoPageSpeed.homeUrl;

			runBtn.disabled = true;
			status.hidden = false;
			status.textContent = smartSeoPageSpeed.strings.checking;
			results.hidden = true;

			var body = new URLSearchParams();
			body.set( 'action', 'smart_seo_pagespeed_check' );
			body.set( 'nonce', smartSeoPageSpeed.nonce );
			body.set( 'url', url );
			body.set( 'strategy', strategy.value );

			fetch( smartSeoPageSpeed.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body.toString(),
			} )
				.then( function ( r ) { return r.json(); } )
				.then( function ( json ) {
					runBtn.disabled = false;
					if ( json && json.success ) {
						status.hidden = true;
						render( json.data );
					} else {
						status.textContent = ( json && json.data && json.data.message ) ? json.data.message : smartSeoPageSpeed.strings.error;
					}
				} )
				.catch( function () {
					runBtn.disabled = false;
					status.textContent = smartSeoPageSpeed.strings.error;
				} );
		} );
	} );
} )();

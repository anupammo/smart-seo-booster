/**
 * Smart SEO Booster — Local Business block (dynamic / server-rendered).
 * No JSX/build step. The front end is rendered by PHP; the editor shows a
 * branded placeholder pointing to the settings.
 */
( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.blocks || ! wp.element || ! wp.blockEditor ) {
		return;
	}

	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var c = wp.components;

	// Same brand mark used as the Placeholder icon for every Smart SEO block.
	var ROCKET_MARK = el(
		'svg',
		{ viewBox: '0 0 256 256', width: 24, height: 24, fill: 'currentColor', 'aria-hidden': 'true', focusable: 'false' },
		el( 'path', { d: 'M112 150 L98 176 L112 171 Z' } ),
		el( 'path', { d: 'M144 150 L158 176 L144 171 Z' } ),
		el( 'ellipse', { cx: 128, cy: 186, rx: 17, ry: 12 } ),
		el( 'ellipse', { cx: 128, cy: 183, rx: 9, ry: 8 } ),
		el( 'path', { d: 'M128 72 L112 106 L112 170 Q112 178 120 178 L136 178 Q144 178 144 170 L144 106 Z' } ),
		el( 'circle', { cx: 128, cy: 112, r: 13, opacity: 0.35 } )
	);

	wp.blocks.registerBlockType( 'smart-seo/local-business', {
		apiVersion: 2,
		title: __( 'Local Business (Smart SEO)', 'smart-seo-booster' ),
		description: __( 'Displays your business address, phone and hours, with LocalBusiness schema.', 'smart-seo-booster' ),
		icon: { background: '#eff6ff', foreground: '#2563eb', src: 'store' },
		category: 'smart-seo',
		supports: { html: false },

		edit: function () {
			var settingsUrl = ( window.smartSeoLocalBusiness && window.smartSeoLocalBusiness.settingsUrl ) || '';
			return el(
				'div',
				useBlockProps( { className: 'smart-seo-local-business-editor' } ),
				el( c.Placeholder, {
					icon: ROCKET_MARK,
					label: __( 'Local Business', 'smart-seo-booster' ),
					instructions: __( 'Address, phone and hours are pulled from Smart SEO → Settings → Schema Details.', 'smart-seo-booster' ),
					className: 'ssb-block-placeholder',
				},
					settingsUrl
						? el( c.Button, { variant: 'secondary', href: settingsUrl, target: '_blank' }, __( 'Go to Settings', 'smart-seo-booster' ) )
						: null
				)
			);
		},

		// Dynamic block — rendered server-side.
		save: function () {
			return null;
		},
	} );
} )( window.wp );

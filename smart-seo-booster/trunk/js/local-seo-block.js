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
	// Sourced from PHP via wp_localize_script (class-local-seo.php).
	var BRAND_ICON_URL = ( window.smartSeoLocalBusiness && window.smartSeoLocalBusiness.iconUrl ) || '';
	var ROCKET_MARK = BRAND_ICON_URL
		? el( 'img', { src: BRAND_ICON_URL, width: 24, height: 24, alt: '' } )
		: null;

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

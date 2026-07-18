/**
 * Smart SEO Booster — Local Business block (dynamic / server-rendered).
 * No JSX/build step. The front end is rendered by PHP; the editor shows a
 * simple placeholder pointing to the settings.
 */
( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.blocks || ! wp.element || ! wp.blockEditor ) {
		return;
	}

	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;

	wp.blocks.registerBlockType( 'smart-seo/local-business', {
		apiVersion: 2,
		title: __( 'Local Business (Smart SEO)', 'smart-seo-booster' ),
		description: __( 'Displays your business address, phone and hours, with LocalBusiness schema.', 'smart-seo-booster' ),
		icon: 'store',
		category: 'widgets',
		supports: { html: false },

		edit: function () {
			var blockProps = useBlockProps( { className: 'smart-seo-local-business-editor' } );
			return el(
				'div',
				blockProps,
				el( 'p', { style: { margin: 0, fontWeight: 600 } }, __( 'Local Business', 'smart-seo-booster' ) ),
				el(
					'p',
					{ style: { margin: '4px 0 0', opacity: 0.7 } },
					__( 'Address, phone and hours are pulled from Smart SEO → Settings → Schema Details.', 'smart-seo-booster' )
				)
			);
		},

		// Dynamic block — rendered server-side.
		save: function () {
			return null;
		},
	} );
} )( window.wp );

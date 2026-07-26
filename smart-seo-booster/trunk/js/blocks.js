/**
 * Smart SEO Booster — block editor registrations (server-rendered blocks).
 * No JSX/build step; uses wp.element.createElement.
 */
( function ( wp ) {
	'use strict';
	if ( ! wp || ! wp.blocks || ! wp.element || ! wp.blockEditor ) {
		return;
	}

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var c = wp.components;
	var registerBlockType = wp.blocks.registerBlockType;

	/**
	 * Brand mark (the Smart SEO Booster rocket) reused as every block's
	 * Placeholder icon, so the editor canvas reads as clearly "ours" — the
	 * same brand touch used across the plugin's admin screens.
	 */
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

	/**
	 * Inserter/list-view icon: a recognizable dashicon tinted in the plugin's
	 * brand blue on a light-blue tile — the same colored-tile treatment used
	 * for stat icons on the SEO Audit Report dashboard (.ssb-ico).
	 */
	function brandIcon( dashicon ) {
		return { background: '#eff6ff', foreground: '#2563eb', src: dashicon };
	}

	function placeholder( props ) {
		return el( c.Placeholder, {
			icon: ROCKET_MARK,
			label: props.label,
			instructions: props.instructions,
			className: 'ssb-block-placeholder',
		}, props.children || null );
	}

	/* ---------------- Social share ---------------- */
	var NETWORKS = [
		[ 'x', 'X' ], [ 'facebook', 'Facebook' ], [ 'linkedin', 'LinkedIn' ],
		[ 'whatsapp', 'WhatsApp' ], [ 'reddit', 'Reddit' ], [ 'email', 'Email' ], [ 'copy', __( 'Copy link', 'smart-seo-booster' ) ],
	];
	registerBlockType( 'smart-seo/social-share', {
		apiVersion: 2,
		title: __( 'Social Share (Smart SEO)', 'smart-seo-booster' ),
		description: __( 'Adds social sharing buttons (X, Facebook, LinkedIn, WhatsApp, email) to this post.', 'smart-seo-booster' ),
		icon: brandIcon( 'share' ),
		category: 'smart-seo',
		supports: { html: false },
		attributes: {
			networks: { type: 'array', default: [ 'x', 'facebook', 'linkedin', 'whatsapp', 'email', 'copy' ] },
			align: { type: 'string', default: 'left' },
		},
		edit: function ( props ) {
			var a = props.attributes;
			var toggle = function ( key ) {
				return function ( on ) {
					var set = a.networks.slice();
					var i = set.indexOf( key );
					if ( on && i === -1 ) { set.push( key ); }
					if ( ! on && i !== -1 ) { set.splice( i, 1 ); }
					props.setAttributes( { networks: set } );
				};
			};
			var hasNetworks = a.networks && a.networks.length > 0;
			return el( Fragment, {},
				el( InspectorControls, {},
					el( c.PanelBody, { title: __( 'Networks', 'smart-seo-booster' ), initialOpen: true },
						NETWORKS.map( function ( n ) {
							return el( c.ToggleControl, {
								key: n[ 0 ], label: n[ 1 ],
								checked: a.networks.indexOf( n[ 0 ] ) !== -1,
								onChange: toggle( n[ 0 ] ),
							} );
						} ),
						el( c.SelectControl, {
							label: __( 'Alignment', 'smart-seo-booster' ), value: a.align,
							options: [ { label: 'Left', value: 'left' }, { label: 'Center', value: 'center' }, { label: 'Right', value: 'right' } ],
							onChange: function ( v ) { props.setAttributes( { align: v } ); },
						} )
					)
				),
				el( 'div', useBlockProps( { className: 'ssb-share-editor is-' + a.align } ),
					hasNetworks
						? el( 'div', { className: 'ssb-share-preview' },
							a.networks.map( function ( key ) {
								var label = ( NETWORKS.filter( function ( n ) { return n[ 0 ] === key; } )[ 0 ] || [ key, key ] )[ 1 ];
								return el( 'span', { key: key, className: 'ssb-share-chip is-' + key }, label );
							} )
						)
						: placeholder( {
							label: __( 'Social Share', 'smart-seo-booster' ),
							instructions: __( 'Select at least one network in the sidebar to preview the share buttons.', 'smart-seo-booster' ),
						} )
				)
			);
		},
		save: function () { return null; },
	} );

	/* ---------------- Call to action ---------------- */
	registerBlockType( 'smart-seo/cta', {
		apiVersion: 2,
		title: __( 'Call to Action (Smart SEO)', 'smart-seo-booster' ),
		description: __( 'Configure the call to action in the sidebar.', 'smart-seo-booster' ),
		icon: brandIcon( 'megaphone' ),
		category: 'smart-seo',
		supports: { html: false },
		attributes: {
			heading: { type: 'string', default: '' },
			text: { type: 'string', default: '' },
			buttonText: { type: 'string', default: '' },
			buttonUrl: { type: 'string', default: '' },
			variant: { type: 'string', default: 'primary' },
		},
		edit: function ( props ) {
			var a = props.attributes;
			var set = function ( k ) { return function ( v ) { var o = {}; o[ k ] = v; props.setAttributes( o ); }; };
			var isEmpty = ! a.heading && ! a.text && ! a.buttonText;
			return el( Fragment, {},
				el( InspectorControls, {},
					el( c.PanelBody, { title: __( 'Content', 'smart-seo-booster' ), initialOpen: true },
						el( c.TextControl, { label: __( 'Heading', 'smart-seo-booster' ), value: a.heading, onChange: set( 'heading' ) } ),
						el( c.TextareaControl, { label: __( 'Text', 'smart-seo-booster' ), value: a.text, onChange: set( 'text' ) } ),
						el( c.TextControl, { label: __( 'Button label', 'smart-seo-booster' ), value: a.buttonText, onChange: set( 'buttonText' ) } ),
						el( c.TextControl, { label: __( 'Button URL', 'smart-seo-booster' ), value: a.buttonUrl, onChange: set( 'buttonUrl' ) } ),
						el( c.SelectControl, {
							label: __( 'Style', 'smart-seo-booster' ), value: a.variant,
							options: [ { label: 'Primary', value: 'primary' }, { label: 'Gradient', value: 'gradient' }, { label: 'Outline', value: 'outline' } ],
							onChange: set( 'variant' ),
						} )
					)
				),
				el( 'div', useBlockProps(),
					isEmpty
						? placeholder( {
							label: __( 'Call to Action', 'smart-seo-booster' ),
							instructions: __( 'Configure the call to action in the sidebar.', 'smart-seo-booster' ),
						} )
						: el( 'div', { className: 'ssb-cta is-' + a.variant },
							a.heading ? el( 'h3', { className: 'ssb-cta-title' }, a.heading ) : null,
							a.text ? el( 'div', { className: 'ssb-cta-text' }, a.text ) : null,
							a.buttonText ? el( 'span', { className: 'ssb-cta-btn' }, a.buttonText ) : null
						)
				)
			);
		},
		save: function () { return null; },
	} );

	/* ---------------- Breadcrumb ---------------- */
	registerBlockType( 'smart-seo/breadcrumb', {
		apiVersion: 2,
		title: __( 'Breadcrumb (Smart SEO)', 'smart-seo-booster' ),
		description: __( 'Renders the breadcrumb trail with schema on the front end.', 'smart-seo-booster' ),
		icon: brandIcon( 'admin-links' ),
		category: 'smart-seo',
		supports: { html: false },
		edit: function () {
			return el( 'div', useBlockProps(),
				placeholder( {
					label: __( 'Breadcrumb', 'smart-seo-booster' ),
					instructions: __( 'Renders the breadcrumb trail with schema on the front end.', 'smart-seo-booster' ),
				} )
			);
		},
		save: function () { return null; },
	} );

	/* ---------------- Post dates ---------------- */
	registerBlockType( 'smart-seo/post-dates', {
		apiVersion: 2,
		title: __( 'Publish / Updated Dates (Smart SEO)', 'smart-seo-booster' ),
		description: __( 'Shows the real dates on the front end.', 'smart-seo-booster' ),
		icon: brandIcon( 'calendar-alt' ),
		category: 'smart-seo',
		supports: { html: false },
		attributes: {
			showPublished: { type: 'boolean', default: true },
			showModified: { type: 'boolean', default: true },
		},
		edit: function ( props ) {
			var a = props.attributes;
			var parts = [];
			if ( a.showPublished ) { parts.push( __( 'Published date', 'smart-seo-booster' ) ); }
			if ( a.showModified ) { parts.push( __( 'Updated date', 'smart-seo-booster' ) ); }
			var summary = parts.length
				? parts.join( ' + ' )
				: __( 'Shows the real dates on the front end.', 'smart-seo-booster' );
			return el( Fragment, {},
				el( InspectorControls, {},
					el( c.PanelBody, { title: __( 'Dates', 'smart-seo-booster' ), initialOpen: true },
						el( c.ToggleControl, { label: __( 'Show published date', 'smart-seo-booster' ), checked: a.showPublished, onChange: function ( v ) { props.setAttributes( { showPublished: v } ); } } ),
						el( c.ToggleControl, { label: __( 'Show updated date', 'smart-seo-booster' ), checked: a.showModified, onChange: function ( v ) { props.setAttributes( { showModified: v } ); } } )
					)
				),
				el( 'div', useBlockProps(),
					placeholder( {
						label: __( 'Publish / Updated dates', 'smart-seo-booster' ),
						instructions: summary,
					} )
				)
			);
		},
		save: function () { return null; },
	} );
} )( window.wp );

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

	function note( text ) {
		return el( 'p', { style: { margin: '4px 0 0', opacity: 0.7, fontSize: '12px' } }, text );
	}

	/* ---------------- Social share ---------------- */
	var NETWORKS = [
		[ 'x', 'X' ], [ 'facebook', 'Facebook' ], [ 'linkedin', 'LinkedIn' ],
		[ 'whatsapp', 'WhatsApp' ], [ 'reddit', 'Reddit' ], [ 'email', 'Email' ], [ 'copy', __( 'Copy link', 'smart-seo-booster' ) ],
	];
	registerBlockType( 'smart-seo/social-share', {
		apiVersion: 2,
		title: __( 'Social Share (Smart SEO)', 'smart-seo-booster' ),
		icon: 'share',
		category: 'widgets',
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
				el( 'div', useBlockProps(),
					el( 'strong', {}, __( 'Social Share', 'smart-seo-booster' ) ),
					note( a.networks.join( ', ' ) )
				)
			);
		},
		save: function () { return null; },
	} );

	/* ---------------- Call to action ---------------- */
	registerBlockType( 'smart-seo/cta', {
		apiVersion: 2,
		title: __( 'Call to Action (Smart SEO)', 'smart-seo-booster' ),
		icon: 'megaphone',
		category: 'widgets',
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
				el( 'div', useBlockProps( { className: 'ssb-cta is-' + a.variant } ),
					a.heading ? el( 'h3', { className: 'ssb-cta-title' }, a.heading ) : null,
					a.text ? el( 'div', { className: 'ssb-cta-text' }, a.text ) : null,
					a.buttonText ? el( 'span', { className: 'ssb-cta-btn' }, a.buttonText ) : null,
					( ! a.heading && ! a.text && ! a.buttonText ) ? note( __( 'Configure the call to action in the sidebar.', 'smart-seo-booster' ) ) : null
				)
			);
		},
		save: function () { return null; },
	} );

	/* ---------------- Breadcrumb ---------------- */
	registerBlockType( 'smart-seo/breadcrumb', {
		apiVersion: 2,
		title: __( 'Breadcrumb (Smart SEO)', 'smart-seo-booster' ),
		icon: 'admin-links',
		category: 'widgets',
		edit: function () {
			return el( 'div', useBlockProps(),
				el( 'strong', {}, __( 'Breadcrumb', 'smart-seo-booster' ) ),
				note( __( 'Renders the breadcrumb trail with schema on the front end.', 'smart-seo-booster' ) )
			);
		},
		save: function () { return null; },
	} );

	/* ---------------- Post dates ---------------- */
	registerBlockType( 'smart-seo/post-dates', {
		apiVersion: 2,
		title: __( 'Publish / Updated Dates (Smart SEO)', 'smart-seo-booster' ),
		icon: 'calendar-alt',
		category: 'widgets',
		attributes: {
			showPublished: { type: 'boolean', default: true },
			showModified: { type: 'boolean', default: true },
		},
		edit: function ( props ) {
			var a = props.attributes;
			return el( Fragment, {},
				el( InspectorControls, {},
					el( c.PanelBody, { title: __( 'Dates', 'smart-seo-booster' ), initialOpen: true },
						el( c.ToggleControl, { label: __( 'Show published date', 'smart-seo-booster' ), checked: a.showPublished, onChange: function ( v ) { props.setAttributes( { showPublished: v } ); } } ),
						el( c.ToggleControl, { label: __( 'Show updated date', 'smart-seo-booster' ), checked: a.showModified, onChange: function ( v ) { props.setAttributes( { showModified: v } ); } } )
					)
				),
				el( 'div', useBlockProps(),
					el( 'strong', {}, __( 'Publish / Updated dates', 'smart-seo-booster' ) ),
					note( __( 'Shows the real dates on the front end.', 'smart-seo-booster' ) )
				)
			);
		},
		save: function () { return null; },
	} );
} )( window.wp );

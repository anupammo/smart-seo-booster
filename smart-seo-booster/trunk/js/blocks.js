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
	 * Brand mark (the Smart SEO Booster logo) reused as every block's
	 * Placeholder icon, so the editor canvas reads as clearly "ours" — the
	 * same brand touch used across the plugin's admin screens. Sourced from
	 * PHP via wp_localize_script (class-blocks.php) so there's one image
	 * file behind every brand-identity icon in the plugin.
	 */
	var BRAND_ICON_URL = ( window.smartSeoBlocksBrand && window.smartSeoBlocksBrand.iconUrl ) || '';
	var ROCKET_MARK = BRAND_ICON_URL
		? el( 'img', { src: BRAND_ICON_URL, width: 24, height: 24, alt: '' } )
		: null;

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

	/* ---------------- FAQ ---------------- */
	registerBlockType( 'smart-seo/faq', {
		apiVersion: 2,
		title: __( 'FAQ (Smart SEO)', 'smart-seo-booster' ),
		description: __( 'A frequently-asked-questions accordion that also outputs FAQPage schema for rich results.', 'smart-seo-booster' ),
		icon: brandIcon( 'editor-help' ),
		category: 'smart-seo',
		supports: { html: false },
		attributes: {
			title: { type: 'string', default: __( 'Frequently Asked Questions', 'smart-seo-booster' ) },
			items: { type: 'array', default: [ { question: '', answer: '' } ] },
		},
		edit: function ( props ) {
			var a = props.attributes;
			var items = a.items && a.items.length ? a.items : [ { question: '', answer: '' } ];

			var setItem = function ( index, key ) {
				return function ( value ) {
					var next = items.slice();
					next[ index ] = Object.assign( {}, next[ index ], ( function () { var o = {}; o[ key ] = value; return o; } )() );
					props.setAttributes( { items: next } );
				};
			};
			var addItem = function () {
				props.setAttributes( { items: items.concat( [ { question: '', answer: '' } ] ) } );
			};
			var removeItem = function ( index ) {
				return function () {
					var next = items.slice();
					next.splice( index, 1 );
					props.setAttributes( { items: next.length ? next : [ { question: '', answer: '' } ] } );
				};
			};

			return el( Fragment, {},
				el( InspectorControls, {},
					el( c.PanelBody, { title: __( 'Heading', 'smart-seo-booster' ), initialOpen: true },
						el( c.TextControl, { label: __( 'Title', 'smart-seo-booster' ), value: a.title, onChange: function ( v ) { props.setAttributes( { title: v } ); } } )
					)
				),
				el( 'div', useBlockProps( { className: 'ssb-faq-editor' } ),
					el( 'p', { className: 'ssb-block-editor-label' }, a.title || __( 'FAQ', 'smart-seo-booster' ) ),
					items.map( function ( item, index ) {
						return el( 'div', { key: index, className: 'ssb-repeater-row' },
							el( c.TextControl, { label: __( 'Question', 'smart-seo-booster' ), value: item.question, onChange: setItem( index, 'question' ) } ),
							el( c.TextareaControl, { label: __( 'Answer', 'smart-seo-booster' ), value: item.answer, onChange: setItem( index, 'answer' ) } ),
							el( c.Button, { isDestructive: true, isSmall: true, variant: 'link', onClick: removeItem( index ) }, __( 'Remove question', 'smart-seo-booster' ) )
						);
					} ),
					el( c.Button, { variant: 'secondary', onClick: addItem }, __( 'Add question', 'smart-seo-booster' ) )
				)
			);
		},
		save: function () { return null; },
	} );

	/* ---------------- HowTo ---------------- */
	registerBlockType( 'smart-seo/howto', {
		apiVersion: 2,
		title: __( 'How-To Guide (Smart SEO)', 'smart-seo-booster' ),
		description: __( 'A numbered step-by-step guide that also outputs HowTo schema for rich results.', 'smart-seo-booster' ),
		icon: brandIcon( 'list-view' ),
		category: 'smart-seo',
		supports: { html: false },
		attributes: {
			title: { type: 'string', default: '' },
			description: { type: 'string', default: '' },
			totalTime: { type: 'string', default: '' },
			steps: { type: 'array', default: [ { name: '', text: '' } ] },
		},
		edit: function ( props ) {
			var a = props.attributes;
			var steps = a.steps && a.steps.length ? a.steps : [ { name: '', text: '' } ];
			var set = function ( k ) { return function ( v ) { var o = {}; o[ k ] = v; props.setAttributes( o ); }; };

			var setStep = function ( index, key ) {
				return function ( value ) {
					var next = steps.slice();
					next[ index ] = Object.assign( {}, next[ index ], ( function () { var o = {}; o[ key ] = value; return o; } )() );
					props.setAttributes( { steps: next } );
				};
			};
			var addStep = function () {
				props.setAttributes( { steps: steps.concat( [ { name: '', text: '' } ] ) } );
			};
			var removeStep = function ( index ) {
				return function () {
					var next = steps.slice();
					next.splice( index, 1 );
					props.setAttributes( { steps: next.length ? next : [ { name: '', text: '' } ] } );
				};
			};

			return el( Fragment, {},
				el( InspectorControls, {},
					el( c.PanelBody, { title: __( 'Guide details', 'smart-seo-booster' ), initialOpen: true },
						el( c.TextControl, { label: __( 'Title', 'smart-seo-booster' ), value: a.title, onChange: set( 'title' ) } ),
						el( c.TextareaControl, { label: __( 'Description', 'smart-seo-booster' ), value: a.description, onChange: set( 'description' ) } ),
						el( c.TextControl, {
							label: __( 'Total time (ISO 8601, optional)', 'smart-seo-booster' ),
							help: __( 'e.g. PT30M for 30 minutes, PT1H30M for 1.5 hours.', 'smart-seo-booster' ),
							value: a.totalTime, onChange: set( 'totalTime' ),
						} )
					)
				),
				el( 'div', useBlockProps( { className: 'ssb-howto-editor' } ),
					el( 'p', { className: 'ssb-block-editor-label' }, a.title || __( 'How-To Guide', 'smart-seo-booster' ) ),
					steps.map( function ( step, index ) {
						return el( 'div', { key: index, className: 'ssb-repeater-row' },
							el( c.TextControl, { label: __( 'Step name', 'smart-seo-booster' ), value: step.name, onChange: setStep( index, 'name' ) } ),
							el( c.TextareaControl, { label: __( 'Step details', 'smart-seo-booster' ), value: step.text, onChange: setStep( index, 'text' ) } ),
							el( c.Button, { isDestructive: true, isSmall: true, variant: 'link', onClick: removeStep( index ) }, __( 'Remove step', 'smart-seo-booster' ) )
						);
					} ),
					el( c.Button, { variant: 'secondary', onClick: addStep }, __( 'Add step', 'smart-seo-booster' ) )
				)
			);
		},
		save: function () { return null; },
	} );

	/* ---------------- Table of Contents ---------------- */
	registerBlockType( 'smart-seo/toc', {
		apiVersion: 2,
		title: __( 'Table of Contents (Smart SEO)', 'smart-seo-booster' ),
		description: __( 'Auto-generates a jump-list from this post’s headings.', 'smart-seo-booster' ),
		icon: brandIcon( 'editor-ol' ),
		category: 'smart-seo',
		supports: { html: false },
		attributes: {
			title: { type: 'string', default: __( 'Table of Contents', 'smart-seo-booster' ) },
			minLevel: { type: 'number', default: 2 },
			maxLevel: { type: 'number', default: 3 },
		},
		edit: function ( props ) {
			var a = props.attributes;
			var set = function ( k ) { return function ( v ) { var o = {}; o[ k ] = v; props.setAttributes( o ); }; };
			var levelOptions = [ { label: 'H2', value: 2 }, { label: 'H3', value: 3 }, { label: 'H4', value: 4 } ];

			return el( Fragment, {},
				el( InspectorControls, {},
					el( c.PanelBody, { title: __( 'Table of Contents', 'smart-seo-booster' ), initialOpen: true },
						el( c.TextControl, { label: __( 'Title', 'smart-seo-booster' ), value: a.title, onChange: set( 'title' ) } ),
						el( c.SelectControl, { label: __( 'From heading level', 'smart-seo-booster' ), value: a.minLevel, options: levelOptions, onChange: function ( v ) { props.setAttributes( { minLevel: parseInt( v, 10 ) } ); } } ),
						el( c.SelectControl, { label: __( 'To heading level', 'smart-seo-booster' ), value: a.maxLevel, options: levelOptions, onChange: function ( v ) { props.setAttributes( { maxLevel: parseInt( v, 10 ) } ); } } )
					)
				),
				el( 'div', useBlockProps(),
					placeholder( {
						label: __( 'Table of Contents', 'smart-seo-booster' ),
						instructions: __( 'Automatically built from this post’s headings when viewed on the front end — nothing to configure here beyond the levels to include.', 'smart-seo-booster' ),
					} )
				)
			);
		},
		save: function () { return null; },
	} );
} )( window.wp );

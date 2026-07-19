/**
 * Smart SEO Booster — block-editor (Gutenberg) sidebar.
 * Native SEO panel bound to the post's registered REST meta. No JSX/build step.
 */
( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.plugins || ! wp.editPost || ! wp.element || ! wp.components || ! wp.data ) {
		return;
	}

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var registerPlugin = wp.plugins.registerPlugin;
	var PluginSidebar = wp.editPost.PluginSidebar;
	var PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var SelectControl = wp.components.SelectControl;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;
	var __ = wp.i18n.__;

	var ROBOTS = [
		{ label: 'Index, Follow (default)', value: 'index,follow' },
		{ label: 'No Index, Follow', value: 'noindex,follow' },
		{ label: 'Index, No Follow', value: 'index,nofollow' },
		{ label: 'No Index, No Follow', value: 'noindex,nofollow' },
		{ label: 'No Archive', value: 'noarchive' },
		{ label: 'No Snippet', value: 'nosnippet' },
	];

	var SCHEMA = [
		'Article', 'BlogPosting', 'NewsArticle', 'WebPage', 'Product', 'Service',
		'Organization', 'Person', 'LocalBusiness', 'Recipe', 'Review', 'Event', 'FAQ', 'HowTo',
	].map( function ( t ) {
		return { label: t, value: t };
	} );

	function counter( value, min, max ) {
		var len = ( value || '' ).length;
		/* translators: 1: current length, 2: min, 3: max */
		return len + ' / ' + min + '–' + max;
	}

	function Sidebar() {
		var meta = useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
		}, [] );
		var permalink = useSelect( function ( select ) {
			return select( 'core/editor' ).getPermalink();
		}, [] );
		var editPost = useDispatch( 'core/editor' ).editPost;

		function set( key, value ) {
			var patch = {};
			patch[ key ] = value;
			editPost( { meta: patch } );
		}

		function field( Control, key, label, extra ) {
			var props = Object.assign(
				{
					label: label,
					value: meta[ key ] || '',
					onChange: function ( v ) {
						set( key, v );
					},
				},
				extra || {}
			);
			return el( Control, props );
		}

		var previewTitle = meta._smart_seo_title || __( 'Your SEO title', 'smart-seo-booster' );
		var previewDesc = meta._smart_seo_description || __( 'Your meta description preview…', 'smart-seo-booster' );

		return el(
			Fragment,
			{},
			el( PluginSidebarMoreMenuItem, { target: 'smart-seo-sidebar', icon: 'chart-line' }, __( 'Smart SEO', 'smart-seo-booster' ) ),
			el(
				PluginSidebar,
				{ name: 'smart-seo-sidebar', icon: 'chart-line', title: __( 'Smart SEO', 'smart-seo-booster' ) },

				el( PanelBody, { title: __( 'Search Appearance', 'smart-seo-booster' ), initialOpen: true },
					el( 'div', { className: 'smart-seo-gb-preview', style: { border: '1px solid #dcdcde', borderRadius: '4px', padding: '10px', marginBottom: '12px' } },
						el( 'div', { style: { color: '#1a0dab', fontSize: '15px' } }, previewTitle ),
						el( 'div', { style: { color: '#006621', fontSize: '12px' } }, permalink || '' ),
						el( 'div', { style: { color: '#4d5156', fontSize: '12px' } }, previewDesc )
					),
					field( TextControl, '_smart_seo_focus_keyword', __( 'Focus keyword', 'smart-seo-booster' ) ),
					field( TextControl, '_smart_seo_title', __( 'SEO title', 'smart-seo-booster' ), { help: counter( meta._smart_seo_title, 30, 60 ) } ),
					field( TextareaControl, '_smart_seo_description', __( 'Meta description', 'smart-seo-booster' ), { help: counter( meta._smart_seo_description, 120, 160 ) } ),
					field( TextControl, '_smart_seo_canonical', __( 'Canonical URL', 'smart-seo-booster' ) )
				),

				el( PanelBody, { title: __( 'Social', 'smart-seo-booster' ), initialOpen: false },
					field( TextControl, '_smart_seo_og_title', __( 'OG title', 'smart-seo-booster' ) ),
					field( TextareaControl, '_smart_seo_og_description', __( 'OG description', 'smart-seo-booster' ) ),
					field( TextControl, '_smart_seo_og_image', __( 'OG image URL', 'smart-seo-booster' ) )
				),

				el( PanelBody, { title: __( 'Advanced', 'smart-seo-booster' ), initialOpen: false },
					field( SelectControl, '_smart_seo_robots', __( 'Robots', 'smart-seo-booster' ), { options: ROBOTS } ),
					field( SelectControl, '_smart_seo_schema_type', __( 'Schema type', 'smart-seo-booster' ), { options: SCHEMA } )
				)
			)
		);
	}

	registerPlugin( 'smart-seo-booster', { render: Sidebar, icon: 'chart-line' } );
} )( window.wp );

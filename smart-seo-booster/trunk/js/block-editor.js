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
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var SelectControl = wp.components.SelectControl;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;
	var __ = wp.i18n.__;

	// Brand mark (the Smart SEO Booster logo) — same icon used everywhere
	// else in the plugin's admin UI, in place of the generic "chart-line"
	// Dashicon this panel used previously. Sourced from PHP via
	// wp_localize_script (class-meta-fields.php).
	var BRAND_ICON_URL = ( window.smartSeoBlocksBrand && window.smartSeoBlocksBrand.iconUrl ) || '';
	var ROCKET_MARK = BRAND_ICON_URL
		? el( 'img', { src: BRAND_ICON_URL, width: 20, height: 20, alt: '' } )
		: null;

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

	/**
	 * Shared hook: current SEO meta + a `set(key, value)` writer, so both the
	 * quick Document panel and the full sidebar operate on the same state.
	 */
	function useSeoMeta() {
		var meta = useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
		}, [] );
		var editPost = useDispatch( 'core/editor' ).editPost;

		function set( key, value ) {
			var patch = {};
			patch[ key ] = value;
			editPost( { meta: patch } );
		}

		return { meta: meta, set: set };
	}

	function field( meta, set, Control, key, label, extra ) {
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

	/**
	 * Quick-access panel shown immediately in the default Document sidebar —
	 * no extra click needed to discover that SEO controls exist at all.
	 */
	function QuickPanel() {
		var state = useSeoMeta();
		var meta = state.meta;
		var set = state.set;

		return el( PluginDocumentSettingPanel, {
			name: 'smart-seo-quick-panel',
			title: __( 'Smart SEO', 'smart-seo-booster' ),
			icon: ROCKET_MARK,
		},
			field( meta, set, TextControl, '_smart_seo_focus_keyword', __( 'Focus keyword', 'smart-seo-booster' ) ),
			field( meta, set, TextControl, '_smart_seo_title', __( 'SEO title', 'smart-seo-booster' ), { help: counter( meta._smart_seo_title, 30, 60 ) } ),
			field( meta, set, TextareaControl, '_smart_seo_description', __( 'Meta description', 'smart-seo-booster' ), { help: counter( meta._smart_seo_description, 120, 160 ) } ),
			el( 'p', { className: 'smart-seo-quick-panel-more' },
				__( 'More SEO options (social preview, schema, robots) are in the Smart SEO sidebar — click the rocket icon in the toolbar above.', 'smart-seo-booster' )
			)
		);
	}

	/**
	 * Full sidebar: search preview, social preview card, and advanced options.
	 */
	function Sidebar() {
		var state = useSeoMeta();
		var meta = state.meta;
		var set = state.set;
		var permalink = useSelect( function ( select ) {
			return select( 'core/editor' ).getPermalink();
		}, [] );

		var previewTitle = meta._smart_seo_title || __( 'Your SEO title', 'smart-seo-booster' );
		var previewDesc = meta._smart_seo_description || __( 'Your meta description preview…', 'smart-seo-booster' );

		var ogTitle = meta._smart_seo_og_title || previewTitle;
		var ogDesc = meta._smart_seo_og_description || previewDesc;
		var ogImage = meta._smart_seo_og_image;

		return el(
			Fragment,
			{},
			el( PluginSidebarMoreMenuItem, { target: 'smart-seo-sidebar', icon: ROCKET_MARK }, __( 'Smart SEO', 'smart-seo-booster' ) ),
			el(
				PluginSidebar,
				{ name: 'smart-seo-sidebar', icon: ROCKET_MARK, title: __( 'Smart SEO', 'smart-seo-booster' ) },

				el( PanelBody, { title: __( 'Search Appearance', 'smart-seo-booster' ), initialOpen: true },
					el( 'div', { className: 'smart-seo-gb-preview', style: { border: '1px solid #dcdcde', borderRadius: '4px', padding: '10px', marginBottom: '12px' } },
						el( 'div', { style: { color: '#1a0dab', fontSize: '15px' } }, previewTitle ),
						el( 'div', { style: { color: '#006621', fontSize: '12px' } }, permalink || '' ),
						el( 'div', { style: { color: '#4d5156', fontSize: '12px' } }, previewDesc )
					),
					field( meta, set, TextControl, '_smart_seo_focus_keyword', __( 'Focus keyword', 'smart-seo-booster' ) ),
					field( meta, set, TextControl, '_smart_seo_title', __( 'SEO title', 'smart-seo-booster' ), { help: counter( meta._smart_seo_title, 30, 60 ) } ),
					field( meta, set, TextareaControl, '_smart_seo_description', __( 'Meta description', 'smart-seo-booster' ), { help: counter( meta._smart_seo_description, 120, 160 ) } ),
					field( meta, set, TextControl, '_smart_seo_canonical', __( 'Canonical URL', 'smart-seo-booster' ) )
				),

				el( PanelBody, { title: __( 'Social', 'smart-seo-booster' ), initialOpen: false },
					el( 'div', { className: 'seo-preview-box' },
						el( 'h4', { style: { margin: '0 0 10px' } }, __( 'Social Media Preview', 'smart-seo-booster' ) ),
						el( 'div', { className: 'seo-social-preview' },
							el( 'div', { className: 'preview-card' },
								el( 'div', { className: 'preview-image' },
									ogImage
										? el( 'img', { src: ogImage, alt: '', style: { width: '100%', height: '100%', objectFit: 'cover' } } )
										: __( 'No image', 'smart-seo-booster' )
								),
								el( 'div', { className: 'preview-content' },
									el( 'div', { className: 'preview-title' }, ogTitle ),
									el( 'div', { className: 'preview-desc' }, ogDesc )
								)
							)
						)
					),
					field( meta, set, TextControl, '_smart_seo_og_title', __( 'OG title', 'smart-seo-booster' ) ),
					field( meta, set, TextareaControl, '_smart_seo_og_description', __( 'OG description', 'smart-seo-booster' ) ),
					field( meta, set, TextControl, '_smart_seo_og_image', __( 'OG image URL', 'smart-seo-booster' ) )
				),

				el( PanelBody, { title: __( 'Advanced', 'smart-seo-booster' ), initialOpen: false },
					field( meta, set, SelectControl, '_smart_seo_robots', __( 'Robots', 'smart-seo-booster' ), { options: ROBOTS } ),
					field( meta, set, SelectControl, '_smart_seo_schema_type', __( 'Schema type', 'smart-seo-booster' ), { options: SCHEMA } )
				)
			)
		);
	}

	function SmartSeoPlugin() {
		return el( Fragment, {}, el( QuickPanel, {} ), el( Sidebar, {} ) );
	}

	registerPlugin( 'smart-seo-booster', { render: SmartSeoPlugin, icon: ROCKET_MARK } );
} )( window.wp );

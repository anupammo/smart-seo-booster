=== Smart SEO Booster ===
Contributors: anupam-mondal
Tags: seo, xml sitemap, schema, open graph, breadcrumbs
Requires at least: 5.9
Requires PHP: 8.0
Tested up to: 7.0
Stable tag: 2.6.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightweight, complete SEO — meta tags, schema, XML sitemaps, breadcrumbs, social previews & content scoring. Fast, automated, no bloat, no upsells.

== Description ==

Smart SEO Booster is a lightweight, modular plugin that gives you every SEO fundamental that actually moves rankings — without the bloat or upsell walls of the big plugins. It automates titles, meta descriptions, Open Graph & Twitter Cards, JSON-LD schema, XML sitemaps and breadcrumbs, and shows you a live search/social preview with a real-time content score right in the editor.

**Features:**
* XML sitemaps (posts, pages, custom post types & images) at /sitemap.xml, referenced from robots.txt
* Breadcrumbs via shortcode `[smart_seo_breadcrumbs]`, template tag, and BreadcrumbList schema
* JSON-LD schema — 14 per-post types (Article, BlogPosting, Product, Recipe, Event, HowTo, and more)
* Automatic titles & meta descriptions with a template variable system (%%title%%, %%sitename%%, %%sep%%, %%excerpt%%…)
* Open Graph & Twitter Cards with live preview and featured-image fallback
* Per-post SEO meta box: focus keyword, canonical, robots, social — with live Google & social previews
* Real-time SEO score: admin bar badge, dashboard widget, post-list column, per-post analysis
* Search-engine verification for Google, Bing, Pinterest & Yandex
* Content audit: word count, headings, image alt coverage, internal links
* One-click import from Yoast SEO and Rank Math (never overwrites your work)
* Redirection manager (301/302/307) with a 404 log
* Bulk SEO editor to edit titles & descriptions across posts at once
* WooCommerce Product schema and product Open Graph tags
* Local SEO shortcode/block with LocalBusiness schema (geo & opening hours)
* Site-wide SEO audit dashboard with score gauge, distribution chart & opportunities
* Google Analytics 4 & Microsoft Clarity integration; Google/Bing/Yandex/Baidu/Pinterest verification
* GEO / AI SEO: llms.txt for AI assistants, AI-crawler training opt-out, speakable data
* Automatic image alt text; publish/updated date structured data
* Blocks: social share, call-to-action, breadcrumb, and dates — with ready-made patterns
* Professional admin UI with Dashicons (no emoji), translation-ready

Built by Anupam Mondal — Full Stack Developer & SEO Consultant — to automate clarity and boost discoverability.

Developer & documentation: https://anupammondal.in/wordpress-plugin/smart-seo-booster

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Smart SEO → Settings** to enable schema and view audit reports

== Frequently Asked Questions ==

= Does this plugin support custom post types? =
Yes, schema detection works for posts, pages, and can be extended to CPTs.

= Can I customize the schema output? =
Yes, schema templates are modular PHP files located in `/schema/`.

= Will this plugin slow down my site? =
No. It uses minimal hooks and outputs schema only in the footer.

== Screenshots ==

1. Admin settings panel
2. SEO audit report table
3. Schema markup preview

== Changelog ==

= 2.6.0 =
* Feature: Rebuilt SEO Audit as a site-wide dashboard (score gauge, distribution chart, opportunities, lowest-scoring list) — fixes the previously blank report
* Feature: Google Analytics 4 and Microsoft Clarity integration; added Baidu verification
* Feature: GEO / AI SEO — /llms.txt for AI assistants, AI-crawler training opt-out in robots.txt, optional speakable structured data
* Feature: Automatic image alt text on the front end; images-missing-alt surfaced in the audit
* Feature: Blocks — social share, call-to-action, breadcrumb, and publish/updated dates, plus block patterns
* Feature: Publish and modified dates added to structured data for all schema types
* Improvement: Professional admin design system; replaced all emoji with Dashicons
* Fix: Audit report page no longer renders blank

= 2.5.0 =
* Feature: One-click import from Yoast SEO and Rank Math (batched; never overwrites existing values)
* Feature: Redirection manager — 301/302/307 redirects with a 404 log and one-click redirect creation
* Feature: Bulk SEO editor to edit titles and meta descriptions across content on one screen
* Feature: WooCommerce Product schema (offers, availability, SKU, ratings) and product Open Graph tags
* Feature: Local SEO — [smart_seo_local_business] shortcode and block; LocalBusiness schema with geo and opening hours
* Improvement: Open Graph type is now filterable (smart_seo_og_type)

= 2.4.0 =
* Feature: 4-step setup wizard for guided onboarding on activation
* Feature: Native Gutenberg (block editor) SEO sidebar with live search preview
* Feature: Redesigned tabbed, card-based settings screen (accessible & responsive)
* Feature: Global no-index controls for archives, author, date, search and paginated pages
* Improvement: Robots directives now handled centrally via the wp_robots filter (no duplicate tags)
* Improvement: Accessibility — WAI-ARIA tabs with full keyboard support on settings and the SEO metabox
* Improvement: Regenerated translation template (.pot); block sidebar is translation-ready

= 2.3.0 =
* Feature: XML sitemaps — index + per-type sitemaps (posts, pages, CPTs) with images, at /sitemap.xml, referenced from robots.txt
* Feature: Breadcrumbs — shortcode, template tag, and BreadcrumbList JSON-LD
* Feature: Per-post schema type selector now wired to real JSON-LD output (14 types)
* Feature: Global title & meta description templates with variables (%%title%%, %%sitename%%, %%sep%%, %%excerpt%%, and more)
* Feature: Search-engine verification fields for Google, Bing, Pinterest and Yandex
* Improvement: Expanded, config-driven settings screen (General, Titles & Metas, Webmaster, Schema Details) with whitelist sanitization
* Improvement: Organization and LocalBusiness schema now populated from settings

= 2.1.0 =
* Feature: Per-post SEO meta box with live search & social previews (Basic / Social / Advanced / Analysis tabs)
* Feature: SEO score engine — admin bar badge, dashboard widget, post-list column, and per-post metabox
* Feature: Open Graph & Twitter Card fields with featured-image fallback
* Feature: Automatic meta description (custom field → excerpt → trimmed content)
* Improvement: Consolidated all front-end meta output into a single authority to eliminate duplicate tags
* Improvement: De-personalized and made all JSON-LD schema templates dynamic and filterable
* Improvement: Moved admin CSS/JS into properly enqueued asset files (no inline blobs)
* Fix: Corrected UTF-8 emoji encoding across the admin interface
* Fix: Aligned plugin version across header, constant, and readme

= 1.0.1 =
* Security: Added ABSPATH checks to all schema files
* Security: Added path traversal protection in schema file loading
* Security: Enhanced capability checks in admin render methods
* Security: Sanitized all schema output (URLs and text)
* Fix: Added null check for get_current_screen() to prevent fatal errors
* Fix: Added missing class-meta-fields.php and class-seo-score-display.php to loader
* Improvement: Code compliance with WordPress.org security standards

= 1.0.0 =
* Initial release with schema injection, audit report, and internal link analysis

== Upgrade Notice ==

= 2.6.0 =
Big update: a real SEO audit dashboard, Google Analytics 4 & Clarity, GEO/AI SEO (llms.txt), image alt automation, and new blocks (social share, CTA, breadcrumb, dates). After updating, visit Settings → Permalinks once if /llms.txt does not load.

= 2.5.0 =
Adds import from Yoast/Rank Math, a redirection manager with 404 log, a bulk SEO editor, WooCommerce product schema, and Local SEO.

= 2.4.0 =
Adds a setup wizard, a native block-editor SEO sidebar, a redesigned tabbed settings screen, and global no-index controls.

= 2.3.0 =
Adds XML sitemaps, breadcrumbs, per-post schema types, title/meta templates, and search-engine verification. After updating, visit Settings → Permalinks once if /sitemap.xml does not load.

= 2.1.0 =
Major update: per-post SEO controls, live previews, SEO scoring, dynamic schema, and cleaner meta output. Recommended for all users.

= 1.0.1 =
Security and stability update. Recommended for all users. Fixes fatal error and adds enterprise-level security hardening.

= 1.0.0 =
First release. Safe to install and test on WordPress 5.2+.

== Localization ==

This plugin is translation-ready. `.pot` file included in `/languages/`.


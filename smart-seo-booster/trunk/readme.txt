=== Smart SEO Booster ===
Contributors: anupam-mondal
Tags: seo, schema, xml sitemap, breadcrumbs, audit
Requires at least: 5.9
Requires PHP: 7.4
Tested up to: 7.0
Stable tag: 2.1.1
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

1. Site-wide SEO Audit dashboard — average-score gauge, distribution chart and opportunities.
2. Per-post SEO meta box with live Google and social previews.
3. Native block-editor (Gutenberg) SEO sidebar.
4. Tabbed settings — titles &amp; metas, webmaster verification, analytics and AI/GEO.
5. Setup wizard for guided onboarding.

== Changelog ==

= 2.1.1 =
* Fix: Resolved a fatal error on activation caused by an incomplete package that was missing several class files. All modules are now included.
* New: WordPress Playground preview blueprint, enabling the "Live Preview" button on the plugin page.
* Improvement: Refreshed listing icon and banner; updated screenshots and captions.

= 2.1.0 =
Complete SEO toolkit release.

Content & meta
* Per-post SEO meta box with live search & social previews (Basic / Social / Advanced / Analysis)
* Automatic titles & meta descriptions with a template variable system (%%title%%, %%sitename%%, %%sep%%, %%excerpt%%, and more)
* Open Graph & Twitter Cards with featured-image fallback; single, de-duplicated meta output
* SEO score engine — admin bar badge, dashboard widget, post-list column, and per-post analysis

Technical SEO
* XML sitemaps (posts, pages, CPTs, images) at /sitemap.xml, referenced from robots.txt
* Breadcrumbs — shortcode, template tag, block, and BreadcrumbList JSON-LD
* JSON-LD schema — 14 per-post types; dynamic, de-personalized, filterable; publish/modified dates on all types
* Global no-index controls (archives, author, date, search, paginated) via the wp_robots filter
* Redirection manager (301/302/307) with a 404 log; one-click import from Yoast SEO and Rank Math

Growth & integrations
* Site-wide SEO audit dashboard (score gauge, distribution chart, opportunities, lowest-scoring list)
* Google Analytics 4 & Microsoft Clarity; verification for Google, Bing, Yandex, Baidu and Pinterest
* GEO / AI SEO — /llms.txt for AI assistants, AI-crawler training opt-out, optional speakable data
* Automatic image alt text with a missing-alt audit
* WooCommerce Product schema and product Open Graph
* Local SEO shortcode/block with LocalBusiness schema (geo & opening hours)

Editor & experience
* Native Gutenberg SEO sidebar with live preview; 4-step setup wizard
* Blocks: social share, call-to-action, breadcrumb, and publish/updated dates, with ready-made patterns
* Bulk SEO editor for titles & descriptions across content
* Professional, accessible admin UI (WAI-ARIA tabs, Dashicons, RTL-safe); fully translation-ready

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

= 2.1.1 =
Important fix: resolves a fatal error on activation from an incomplete 2.1.0 package. All users should update.

= 2.1.0 =
Complete SEO toolkit: audit dashboard, XML sitemaps, breadcrumbs, schema, analytics, GEO/AI SEO, blocks, and more. After activating, visit Settings → Permalinks once if /sitemap.xml or /llms.txt does not load.

= 1.0.1 =
Security and stability update. Recommended for all users. Fixes fatal error and adds enterprise-level security hardening.

= 1.0.0 =
First release. Safe to install and test on WordPress 5.2+.

== Localization ==

This plugin is translation-ready. `.pot` file included in `/languages/`.


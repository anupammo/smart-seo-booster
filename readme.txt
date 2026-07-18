=== Smart SEO Booster ===
Contributors: anupam-mondal  
Tags: seo, schema, json-ld, content audit, wordpress seo  
Requires at least: 5.9  
Requires PHP: 8.0
Tested up to: 6.9
Stable tag: 2.1.0
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Modular SEO plugin with schema injection, content audit, and internal link analysis. Built for clarity, automation, and performance.

== Description ==

Smart SEO Booster is a lightweight, modular plugin designed to improve your website's SEO with minimal effort. It automatically injects JSON-LD schema, audits content structure, and analyzes internal links — all from a clean admin interface.

**Features:**
* Dynamic JSON-LD schema for Article, FAQ, LocalBusiness, Organization, and ProfilePage
* Title and meta description injection
* Content audit: word count, headings, image alt coverage
* Internal link analysis
* Admin dashboard with audit report
* Localization-ready

Built by Anupam Mondal — Full Stack Developer & SEO Consultant — to automate clarity and boost discoverability.

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

= 2.1.0 =
Major update: per-post SEO controls, live previews, SEO scoring, dynamic schema, and cleaner meta output. Recommended for all users.

= 1.0.1 =
Security and stability update. Recommended for all users. Fixes fatal error and adds enterprise-level security hardening.

= 1.0.0 =
First release. Safe to install and test on WordPress 5.2+.

== Localization ==

This plugin is translation-ready. `.pot` file included in `/languages/`.


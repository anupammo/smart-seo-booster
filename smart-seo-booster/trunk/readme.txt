=== Smart SEO Booster ===
Contributors: anupamwp
Tags: ai seo, llms txt, core web vitals, faq schema, breadcrumbs
Requires at least: 5.9
Requires PHP: 7.4
Tested up to: 7.0
Stable tag: 2.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A free WordPress SEO plugin built for AI search, with an llms.txt file, AI crawler controls, schema markup, XML sitemaps, breadcrumbs and more.

== Description ==

**Smart SEO Booster is a free WordPress SEO plugin built for AI search.** It ships the **GEO (Generative Engine Optimization) / AI SEO** tools most SEO plugins still don't have — an `/llms.txt` file that tells AI assistants what your site is about, one-click opt-out for AI crawlers (GPTBot, ClaudeBot, CCBot, Google-Extended), and speakable structured data for voice assistants — on top of a complete, conventional SEO toolkit.

That toolkit covers every ranking fundamental that actually moves the needle: schema markup (including FAQ and How-To rich results), XML sitemaps, meta tags, Open Graph & Twitter Cards, breadcrumbs, redirects, content scoring, and a **Core Web Vitals** report powered by Google PageSpeed Insights — without the bloat, paywalls, or upsell nags of the big-name SEO plugins. Every feature is free, forever. No "Pro" tier, no locked settings.

It automates titles and meta descriptions, injects 14 types of JSON-LD schema, generates XML sitemaps, and shows you a live search/social preview with a real-time SEO score right inside the block editor. One-click import from Yoast SEO or Rank Math means switching takes minutes, not hours — and your existing SEO data is never overwritten.

**Why site owners switch to Smart SEO Booster:**
* **100% free** — every feature above is included, always. No premium upsells, no drip-fed "unlock with Pro" nags.
* **Lightweight & fast** — modular architecture, minimal database footprint, nothing loads unless you enable it.
* **Beginner-friendly** — a 4-step setup wizard gets schema, sitemaps and breadcrumbs configured in under two minutes.
* **Translated into 17 languages** — Catalan, Chinese (Taiwan), Dutch, English (US/Australia), French, German, Italian, Japanese, Persian, Polish, Portuguese (Brazil), Russian, Spanish (Spain/Chile), Swedish, and Ukrainian.
* **Future-proofed for AI search** — GEO/llms.txt tools most competitors haven't built yet.

**Core SEO features:**
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
* Blocks: social share, call-to-action, breadcrumb, dates, FAQ (FAQPage schema), How-To (HowTo schema), and Table of Contents — with ready-made patterns
* Page Speed report: Google PageSpeed Insights scores and Core Web Vitals (LCP, CLS, INP) for any page on your site, no API key required
* Flesch reading-ease score in the content analysis, alongside word count, headings, image alt coverage & internal links
* Professional admin UI with brand icons for every social/analytics/webmaster integration (no emoji), fully translated into 17 languages

Built by Anupam Mondal — Full Stack Developer & SEO Consultant — to automate clarity and boost discoverability.

Developer & documentation: https://anupammondal.in/wordpress-plugin/smart-seo-booster

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Smart SEO → Settings** to enable schema and view audit reports

== Frequently Asked Questions ==

= Is Smart SEO Booster really free? Is there a paid/Pro version? =
Yes, it's really free — every feature listed above, including schema markup, XML sitemaps, redirects, GA4 integration, and GEO/AI SEO tools, is included at no cost. There is no premium tier and no features are locked behind an upsell.

= Is this a good free alternative to Yoast SEO or Rank Math? =
It depends on what you need. Yoast and Rank Math are mature, widely used plugins with large support communities — if you are happy with either, switching will gain you little. Smart SEO Booster is aimed at people who want the same fundamentals (titles, meta descriptions, schema markup, XML sitemaps, breadcrumbs) without a paid tier: redirects, the bulk editor and every schema type are free here, and there are no upsell notices in the admin. It also ships AI/GEO tools that most SEO plugins do not have yet. The one-click importer means you can try it without losing your existing data.

= Can I switch from Yoast SEO or Rank Math without losing my data? =
Yes. Go to **Smart SEO → Import**, choose Yoast SEO or Rank Math, and Smart SEO Booster copies your existing titles, meta descriptions, social tags, and canonical URLs. It never overwrites a Smart SEO value that's already set, so you can trial it safely alongside your current plugin.

= Does this plugin add schema markup (JSON-LD) automatically? =
Yes. It automatically outputs JSON-LD structured data for 14 content types — Article, BlogPosting, Product, Recipe, Event, HowTo, LocalBusiness, and more — with no manual coding required.

= How do I generate an XML sitemap? =
Enable **XML Sitemap** in Smart SEO → Settings and your sitemap is available instantly at `/sitemap.xml`, automatically referenced from `robots.txt` for search engine discovery.

= How do I check my Core Web Vitals and PageSpeed score? =
Go to **Smart SEO → Page Speed** and enter any URL on your site. The report shows Google PageSpeed Insights scores for Performance, SEO, Accessibility and Best Practices, plus Core Web Vitals (LCP, CLS and INP) rated against Google's own thresholds. It uses real-user field data where Google has it for your site, and lab data otherwise. No API key is required, though you can add a free one for higher-volume checks. Your site must be publicly reachable — local and staging installs cannot be tested this way.

= How do I add FAQ schema to a page? =
Add the **FAQ (Smart SEO)** block in the block editor and fill in your questions and answers. The block renders an accessible accordion on the front end and outputs FAQPage JSON-LD automatically, which is what Google reads for FAQ rich results. There is a matching How-To block that outputs HowTo schema the same way, and a Table of Contents block that builds a jump list from your headings.

= Does this plugin support custom post types? =
Yes, schema detection, sitemaps, and meta box support all work for posts, pages, WooCommerce products, and can be extended to any custom post type.

= Can I customize the schema output? =
Yes, schema templates are modular PHP files located in `/schema/`, so developers can override or extend any schema type.

= Is there a setup wizard for beginners? =
Yes, a 4-step Setup Wizard walks you through site type, schema defaults, and titles/meta templates right after activation — no SEO experience required.

= Does Smart SEO Booster work with the block editor (Gutenberg)? =
Yes. It ships a native Gutenberg SEO sidebar with live title/meta editing and social previews, plus ready-made blocks for breadcrumbs, social share, call-to-action, and publish/updated dates.

= What languages is this plugin available in? =
Smart SEO Booster's admin interface is fully translated into 17 languages: Catalan, Chinese (Taiwan), Dutch, English (US), English (Australia), French, German, Italian, Japanese, Persian, Polish, Portuguese (Brazil), Russian, Spanish (Spain), Spanish (Chile), Swedish, and Ukrainian.

= What is GEO / AI SEO, and why does it matter? =
GEO (Generative Engine Optimization) helps your content perform well when AI assistants like ChatGPT, Claude, and Google AI Overviews summarize or cite it. Smart SEO Booster can publish an `/llms.txt` file for AI assistants, let you opt out of AI-crawler training (GPTBot, ClaudeBot, CCBot, Google-Extended), and mark content as speakable for voice assistants.

= How do I add an llms.txt file to my WordPress site? =
Enable **Serve an llms.txt file** under Smart SEO → Settings → AI &amp; GEO. The file is generated from your published content and served at `/llms.txt`, so it stays current as you publish instead of going stale like a hand-written one. If it does not load, visit Settings → Permalinks and click Save once to flush your rewrite rules.

= How do I stop ChatGPT and other AI crawlers from training on my content? =
Turn on **Ask AI crawlers not to train on this site** under Smart SEO → Settings → AI &amp; GEO. This adds robots.txt rules for GPTBot, Google-Extended, ClaudeBot, CCBot and others. Two things worth knowing: Google-Extended controls Gemini training only and has no effect on your Google Search rankings, and blocking training crawlers can also reduce how often AI assistants cite your site — so it is worth deciding deliberately rather than by default.

= Will this plugin conflict with other SEO plugins? =
Running two SEO plugins that both output meta tags and schema at the same time can cause duplicates. Use the built-in importer to migrate away from your previous SEO plugin, then deactivate it.

= Will this plugin slow down my site? =
No. It uses minimal hooks and outputs schema only in the footer, with no external API calls or render-blocking assets.

== Screenshots ==

1. Site-wide SEO Audit dashboard — average SEO score gauge, score distribution chart, and content opportunities at a glance.
2. Native Gutenberg SEO sidebar — live Google search preview and Facebook/Twitter social preview card while you write.
3. Full SEO score breakdown — word count, headings, alt text, internal/external links and priority recommendations for any post.
4. Guided 4-step Setup Wizard — schema, titles, sitemaps and breadcrumbs configured in under two minutes.
5. Step-by-step Tutorial — a Next/Previous walkthrough of every major feature, right in your dashboard.
6. General settings — schema markup, XML sitemap, breadcrumbs and separator options in one screen.
7. Titles &amp; Metas — template-driven SEO titles and meta descriptions with reusable variables.
8. AI &amp; GEO settings — Generative Engine Optimization, llms.txt, and AI-crawler controls for ChatGPT, Claude and Google AI Overviews.
9. Social Media Accounts — connect Facebook, X, LinkedIn, Instagram, YouTube and Pinterest to your Organization schema.
10. Smart SEO blocks — breadcrumbs, social share, call-to-action, dates and Local Business, grouped in their own inserter category.
11. Bulk SEO Editor — edit SEO titles and meta descriptions across your whole site from one table.
12. Redirection manager with a live 404 log — catch broken links before search engines do.

== Changelog ==

= 2.2.0 =
* New: Page Speed report — real Google PageSpeed Insights data (Performance/SEO/Accessibility/Best-Practices scores plus Core Web Vitals: LCP, CLS, INP) for any URL on your site, no API key required.
* New: FAQ block — an accordion that also outputs FAQPage JSON-LD schema for rich results.
* New: How-To block — a numbered step guide that also outputs HowTo JSON-LD schema for rich results.
* New: Table of Contents block — auto-builds a jump-list from a post's headings.
* Improvement: Readability scoring now uses the Flesch Reading Ease formula instead of a sentence-length heuristic.
* Improvement: Recognizable brand icons next to every social, webmaster-verification and analytics field in Settings.
* Improvement: Tutorial and Setup Wizard now use the plugin's outline "line icon" mark on completion/empty states.

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

= 2.2.0 =
New Page Speed report (Core Web Vitals), FAQ/How-To/Table of Contents blocks, and Flesch reading-ease scoring.

= 2.1.1 =
Important fix: resolves a fatal error on activation from an incomplete 2.1.0 package. All users should update.

= 2.1.0 =
Complete SEO toolkit: audit dashboard, XML sitemaps, breadcrumbs, schema, analytics, GEO/AI SEO, blocks, and more. After activating, visit Settings → Permalinks once if /sitemap.xml or /llms.txt does not load.

= 1.0.1 =
Security and stability update. Recommended for all users. Fixes fatal error and adds enterprise-level security hardening.

= 1.0.0 =
First release. Safe to install and test on WordPress 5.2+.

== Localization ==

Smart SEO Booster is fully translated and ready to use in 17 languages:

* Catalan (ca)
* Chinese (Taiwan) (zh_TW)
* Dutch (nl_NL)
* English (Australia) (en_AU)
* English (US) (en_US)
* French (France) (fr_FR)
* German (de_DE)
* Italian (it_IT)
* Japanese (ja)
* Persian (fa_IR)
* Polish (pl_PL)
* Portuguese (Brazil) (pt_BR)
* Russian (ru_RU)
* Spanish (Chile) (es_CL)
* Spanish (Spain) (es_ES)
* Swedish (sv_SE)
* Ukrainian (uk)

Translation files (`.po`/`.mo`) are included in `/languages/`, and the plugin loads the matching translation automatically based on your site's WordPress language setting. A `.pot` template is also included for translators who want to add another language — contributions via translate.wordpress.org are welcome.


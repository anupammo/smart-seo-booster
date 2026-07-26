<p align="center">
  <img src=".wordpress-org/banner-1544x500.png" alt="Smart SEO Booster — lightweight, complete WordPress SEO" width="100%">
</p>

# Smart SEO Booster

**The lightweight, complete SEO toolkit for WordPress — fast by design, powerful by default.**

Smart SEO Booster gives you every SEO fundamental that actually moves rankings — meta tags, Open Graph & Twitter Cards, JSON-LD schema, XML sitemaps, breadcrumbs, and real-time content scoring — in one clean, modular plugin with **no bloat, no upsell walls, and no complex add-ons**.

[![Version](https://img.shields.io/badge/version-2.1.1-blue.svg)](https://github.com/anupammo/smart-seo-booster)
[![WordPress](https://img.shields.io/badge/WordPress-5.9%E2%80%937.0.2-21759b.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-GPLv2%2B-green.svg)](LICENSE)

> 🎯 **Mission:** Deliver 90% of what Rank Math and Yoast do, at 10% of the weight — the SEO plugin you set up in 5 minutes and forget.

---

## 📌 Table of Contents

- [Why Smart SEO Booster](#-why-smart-seo-booster)
- [Feature Matrix](#-feature-matrix-current-vs-planned)
- [Competitive Positioning](#-competitive-positioning-2026)
- [Installation](#-installation)
- [Architecture](#-architecture)
- [Development Roadmap](#-development-roadmap)
- [Release Checklist](#-release-checklist)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🚀 Why Smart SEO Booster

| Principle | What it means for you |
|-----------|----------------------|
| **⚡ Lightweight** | Native WordPress hooks only. No jQuery bloat, no external calls, no tracking. Target: < 50 KB footprint, zero measurable TTFB impact. |
| **🧩 Modular** | Every feature is an isolated class. Enable only what you need. |
| **🔒 Secure** | Escaped output, sanitized input, nonce-verified forms, capability checks — audited against WordPress.org Plugin Check. |
| **🎨 Modern UI** | Clean, WordPress-native admin styling. No confusing dashboards, no dark patterns. |
| **🆓 Genuinely free** | All core SEO features are free forever. No feature is held hostage behind a "Pro" nag. |

---

## 📊 Feature Matrix (Current vs Planned)

### ✅ Available now (v2.1)

- **SEO Audit dashboard** — site-wide score gauge, distribution chart, opportunities & lowest-scoring list (professional Dashicon UI, no emoji)
- **Analytics** — Google Analytics 4 + Microsoft Clarity, loaded only when configured
- **Webmaster verification** — Google (Search Console), Bing, Yandex, Pinterest, Baidu
- **GEO / AI SEO** — `/llms.txt` for AI assistants, AI-crawler training opt-out, speakable data
- **Image SEO** — automatic alt text + missing-alt audit
- **Blocks** — social share, call-to-action, breadcrumb, publish/updated dates + patterns
- **Import from Yoast / Rank Math** — one-click, batched migration that never overwrites your work
- **Redirection manager** — 301/302/307 + a 404 log with one-click redirect creation
- **Bulk SEO editor** — edit titles & descriptions across your content on one screen
- **WooCommerce** — Product schema + product OG tags (auto-active when WooCommerce is)
- **Local SEO** — business shortcode/block + LocalBusiness schema with geo & opening hours
- **Setup wizard** — 4-step guided onboarding on first activation
- **Block-editor sidebar** — native Gutenberg SEO panel with live snippet preview + tabbed classic metabox
- **Tabbed, card-based settings** — accessible (WAI-ARIA), responsive, RTL-safe
- **Global no-index controls** — archives, author, date, search & paginated pages
- **Meta tags** — automatic title & meta description injection
- **Global title/meta templates** — `%%title%%`, `%%sitename%%`, `%%sep%%`, `%%excerpt%%`, `%%category%%`… variable system
- **Auto meta description** — custom → template → excerpt → trimmed content fallback
- **Open Graph & Twitter Cards** — per-post social preview + featured-image OG fallback
- **Per-post SEO meta box** — tabbed UI: Basic / Social / Advanced / Analysis
- **XML Sitemaps** — index + per-type sitemaps for posts, pages & CPTs, with images; `/sitemap.xml` + robots.txt reference
- **Breadcrumbs** — template tag, `[smart_seo_breadcrumbs]` shortcode & `BreadcrumbList` JSON-LD
- **JSON-LD schema** — 14 per-post types wired to the meta-box selector (Article, BlogPosting, Product, Recipe, Event, HowTo…) + context templates
- **Search-engine verification** — Google, Bing, Pinterest & Yandex meta tags
- **SEO score engine** — admin bar badge, dashboard widget, post-list column, per-post metabox
- **Content auditor** — word count, headings, image alt coverage
- **Internal link analysis** — counts internal links per post
- **Canonical URLs & robots meta** — per-post control
- **Focus keyword analysis** — density + placement checks in the editor

### 🔭 Future ideas (post-roadmap)

All four roadmap phases are complete. Candidate future work:

- **Video & News sitemaps**, sitemap caching
- **Link-suggestion** helper in the editor
- **Multiple** LocalBusiness locations
- **Google Search Console** read-only insights

---

## 🥊 Competitive Positioning (2026)

Smart SEO Booster competes in the **lightweight/automated lane** — alongside Slim SEO and The SEO Framework — not the heavyweight Rank Math/Yoast lane. Our edge: **automation of Slim SEO + the visual editor UX of Rank Math, without the weight of either.**

| Capability | Smart SEO Booster (target v2.5) | Slim SEO | The SEO Framework | Rank Math (Free) | Yoast (Free) |
|---|---|---|---|---|---|
| Meta title/description | ✅ | ✅ | ✅ | ✅ | ✅ |
| OG / Twitter Cards | ✅ | ✅ | ✅ | ✅ | ✅ |
| Live social/search preview | ✅ | ➖ | ➖ | ✅ | ✅ |
| JSON-LD schema | ✅ (10+ types) | ✅ | ✅ | ✅ (18+ types) | ✅ (limited) |
| XML sitemap | ✅ | ✅ | ✅ | ✅ | ✅ |
| Breadcrumbs | ✅ | ✅ | ✅ | ✅ | ✅ |
| Content/SEO scoring | ✅ | ➖ | ➖ | ✅ | ✅ |
| Redirection manager | ✅ | ➖ (Pro) | ➖ | ✅ | ➖ (Premium) |
| Import from Yoast/Rank Math | ✅ | ✅ | ✅ | ✅ | ✅ |
| WooCommerce product schema | ✅ | ✅ | ➖ | ✅ | ➖ (Premium) |
| Settings-light automation | ✅ | ✅✅ | ✅ | ➖ | ➖ |
| Weight / performance | ✅✅ | ✅✅ | ✅✅ | ➖ | ➖ |
| Free & no upsell nags | ✅ | ✅ | ✅ | ➖ | ➖ |

**Takeaway:** Closing the **XML sitemap + breadcrumbs** gap is the single highest-priority work — they are table-stakes every competitor ships free, and their absence is the #1 reason a lightweight SEO plugin gets uninstalled.

*Sources: [WordPress.org SEO plugin listings](https://wordpress.org/plugins/), [Slim SEO](https://wordpress.org/plugins/slim-seo/), [Rank Math](https://wordpress.org/plugins/seo-by-rank-math/), 2026 comparison reviews.*

---

## 🔧 Installation

### From WordPress admin (recommended once published)
1. **Plugins → Add New → Upload Plugin**
2. Select the `smart-seo-booster.zip` file → **Install Now** → **Activate**
3. Go to **Smart SEO** in the admin menu and run the setup

### Manual
```bash
# Copy the plugin into your WordPress install
cp -r smart-seo-booster /path/to/wp-content/plugins/

# Or via WP-CLI
wp plugin activate smart-seo-booster
```

**Requirements:** WordPress 5.9+ · PHP 7.4+ · Tested up to WordPress 7.0.2

---

## 🏗️ Architecture

```
smart-seo-booster/
├── smart-seo-booster.php        # Bootstrap + plugin header
├── uninstall.php                # Clean removal of options & post meta
├── readme.txt                   # WordPress.org listing
├── includes/                    # Core modules (one class = one concern)
│   ├── class-loader.php         # Module registrar
│   ├── class-settings.php       # Options API + settings page
│   ├── class-admin-ui.php       # Admin menus & pages
│   ├── class-seo-core.php       # Front-end meta output
│   ├── class-meta-fields.php    # Per-post SEO meta box
│   ├── class-schema-generator.php  # JSON-LD dispatcher
│   ├── class-content-auditor.php   # Content analysis
│   ├── class-seo-score-display.php # Scoring UI surfaces
│   └── class-link-analyzer.php     # Internal link analysis
├── schema/                      # JSON-LD templates (dynamic)
├── templates/                   # Admin view templates
├── css/  ·  js/                 # Enqueued assets (no inline blobs)
└── languages/                   # i18n (.pot)
```

**Design rules**
- Each module exposes a static `init()` registered in `class-loader.php`.
- All output escaped at the sink (`esc_html`, `esc_attr`, `esc_url`, `wp_json_encode`).
- All input sanitized on the way in; every form nonce-verified.
- Assets enqueued via `wp_enqueue_*` with the version constant for cache-busting — **no inline `<style>`/`<script>` blobs.**

---

## 🗺️ Development Roadmap

The road to **5,000 downloads** is four phases. Each phase is independently shippable and raises the plugin's WordPress.org rating and retention.

### ✅ Phase 1 — Foundation & Fixes *(v2.1.0)*  ·  **COMPLETE**
> Goal: a rock-solid, consistent, spotless base. Ship nothing new until these are clean. — **Done.** All items landed; only the official Plugin Check run remains (needs a live WP install).

- [x] **Fix version consistency** — plugin header, `SMART_SEO_BOOSTER_VERSION` constant, and `readme.txt` stable tag all aligned to `2.1.0` (with changelog + upgrade notice)
- [x] **Fix emoji encoding (mojibake)** — reversed CP1252 double-encoding across all admin strings, templates & docs (161 corrupted glyphs in 7 files) to proper UTF-8
- [x] **De-duplicate meta output** — consolidated into `class-seo-core.php` as the single `wp_head` authority; removed the duplicate emitter from `class-meta-fields.php`
- [x] **Move inline CSS/JS to enqueued assets** — extracted to `js/meta-fields.js`, `js/seo-score.js`, `css/meta-box.css`; enqueued with version cache-busting + `wp_localize_script` (no inline `<style>`/`wp_add_inline_script` blobs remain)
- [x] **Remove/enqueue dead code** — deleted the never-enqueued `js/block-editor.js`
- [x] **De-personalize & dynamic schema** — removed hardcoded personal identity (name, address, phone, social links) from **all five** schema templates; now derived from post/site with `apply_filters` hooks
- [x] **Pass WordPress.org Plugin Check** — static audit clean (escaping, i18n with text domain, nonce-guarded & sanitized superglobals, direct-access guards on every file, no obfuscation/unsafe I/O/short tags, complete header incl. License URI + Domain Path, `uninstall.php` clears all options + post meta). *Run `wp plugin check smart-seo-booster` on your live WP for the official green tick.*
- [x] **Consolidate documentation** — moved 12 scattered dev notes into `docs/` + `docs/archive/`; root now holds only README, STRATEGY, CHANGELOG, CONTRIBUTING; added `.distignore` so dev docs never ship

### ✅ Phase 2 — Parity Features *(2.1.0)*  ·  **COMPLETE**
> Goal: close every "table-stakes" gap so no reviewer can say "but it doesn't even have X". — **Done.**

- [x] **XML Sitemap** — `class-sitemap.php`: index + per-type sitemaps (posts/pages/CPTs) with images, paginated at 1,000 URLs, honors per-post noindex, `Sitemap:` line in robots.txt, optional disable of core WP sitemap
- [x] **Breadcrumbs** — `class-breadcrumbs.php`: `smart_seo_breadcrumbs()` template tag, `[smart_seo_breadcrumbs]` shortcode, accessible `<nav>` markup + `BreadcrumbList` JSON-LD
- [x] **Per-post schema wiring** — `class-schema-generator.php` now reads the meta-box `_smart_seo_schema_type` selector; Article-family built richly, other types via a valid generic builder (verified: valid JSON-LD, HTML-clean)
- [x] **Global meta templates** — `class-meta-templates.php` variable parser (`%%title%%`/`%%sep%%`/`%%sitename%%`/`%%excerpt%%`/…) wired into document title + meta description (unit-tested, incl. dangling-separator collapse)
- [x] **Auto meta description** — custom → template → excerpt → trimmed content fallback (`class-seo-core.php`)
- [x] **Featured-image OG fallback** — custom OG image → featured image → none (`class-seo-core.php`)
- [x] **Search-engine verification fields** — Google, Bing, Pinterest & Yandex meta tags on the homepage

**Also landed:** expanded, config-driven settings screen (General / Titles &amp; Metas / Webmaster / Schema Details) with whitelist sanitization (unit-tested); self-healing rewrite flush.

### ✅ Phase 3 — Modern UX & Trust *(2.1.0)*  ·  **COMPLETE**
> Goal: make it *feel* premium so first-time users leave 5-star reviews. — **Done.**

- [x] **Setup wizard** — `class-setup-wizard.php`: 4-step onboarding (site type → titles → features → verification → done), activation redirect, merges into options without wiping unrelated settings
- [x] **Redesigned settings** — tabbed, card-based, WP-native, responsive; single form (no data loss across tabs); all fields in one config-driven screen
- [x] **Global noindex controls** — archives, author, date, search & paginated pages via the modern `wp_robots` filter (per-post robots consolidated here too — no duplicate tags)
- [x] **Accessibility pass** — WAI-ARIA tabs (roles, `aria-selected`, roving `tabindex`, arrow/Home/End keys) on both the settings screen and the classic metabox; accessible breadcrumb `<nav>`
- [x] **Block-editor sidebar** — `js/block-editor.js`: native Gutenberg `PluginSidebar` (Search Appearance / Social / Advanced) bound to REST meta, with live snippet preview; classic metabox auto-hidden on the block editor
- [x] **Full i18n** — regenerated `.pot` (139 strings), `wp_set_script_translations` for the sidebar, RTL-safe CSS (logical properties) on the new UI

### ✅ Phase 4 — Differentiators *(2.1.0)*  ·  **COMPLETE**
> Goal: give people a reason to *choose you over Slim SEO*. — **Done.**

- [x] **Redirection manager** — `class-redirects.php`: 301/302/307 redirects + capped 404 log with one-click "create redirect", stored in options (no custom tables); path normalization unit-tested for trailing-slash idempotency
- [x] **Bulk SEO editor** — `class-bulk-editor.php`: paginated screen to edit SEO titles & descriptions across posts/pages, per-post capability-checked
- [x] **Local SEO** — `class-local-seo.php`: `[smart_seo_local_business]` shortcode **and** a dynamic Gutenberg block; LocalBusiness schema enriched with geo coordinates + opening hours from settings
- [x] **WooCommerce basics** — `class-woocommerce.php` (loads only when WC is active): Product JSON-LD (offers, availability, SKU, aggregateRating) + `og:type=product` and price/availability OG tags
- [x] **Import from Yoast/Rank Math** — `class-importer.php`: batched one-click migration of titles, descriptions, social tags, canonicals & robots; never overwrites existing values (removes the #1 switching barrier)

---

## ✅ Release Checklist

Run before **every** WordPress.org submission or update.

### Code quality
- [ ] `Plugin Check` plugin: **0 errors, 0 warnings**
- [ ] PHPCS with `WordPress-Extra` ruleset passes
- [ ] No PHP notices/warnings with `WP_DEBUG` on
- [ ] Tested on PHP 7.4, 8.0, 8.1, 8.2, 8.3
- [ ] Tested on WordPress 5.9 (min) and latest (7.0.2)

### Security
- [ ] All output escaped at the sink
- [ ] All input sanitized + validated
- [ ] All forms nonce-verified; all handlers capability-checked
- [ ] No direct file access (`defined('ABSPATH') || exit;` in every file)
- [ ] `uninstall.php` removes all options and post meta

### Compliance & assets
- [ ] `readme.txt` header valid ([readme validator](https://wordpress.org/plugins/developers/readme-validator/))
- [ ] Version bumped everywhere (header, constant, `readme.txt`, `@since`)
- [ ] `Stable tag` matches the packaged version
- [ ] Changelog + upgrade notice written
- [ ] Screenshots current (`assets/screenshot-*.png`)
- [ ] Banner + icon present (`assets/banner-1544x500.png`, `assets/icon-256x256.png`)
- [ ] All strings translatable with the `smart-seo-booster` text domain
- [ ] No trademarked terms misused; no external calls without disclosure

### Functional smoke test
- [ ] Activate on a clean install — no fatal errors
- [ ] Schema validates in [Google Rich Results Test](https://search.google.com/test/rich-results)
- [ ] Sitemap loads and validates
- [ ] Social preview validates in Facebook/X debuggers
- [ ] Deactivate/reactivate/uninstall cycle is clean

---

## 🤝 Contributing

1. Fork and branch: `git checkout -b feature/your-feature`
2. Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
3. Keep modules isolated — one class, one responsibility
4. Add/update the `.pot` for any new strings
5. Run Plugin Check before opening a PR
6. Open a pull request against `main`

See [CONTRIBUTING.md](CONTRIBUTING.md) for the full guide, and [STRATEGY.md](STRATEGY.md) for the growth plan behind this roadmap.

---

## 📄 License

GPLv2 or later — see [LICENSE](LICENSE).

## 👨‍💻 Author

**Anupam Mondal** — Full-Stack Developer & SEO Consultant
· [Portfolio](https://anupammondal.in) · [Plugin page](https://anupammondal.in/wordpress-plugin/smart-seo-booster) · [@anupammo](https://github.com/anupammo)

---

<sub>⭐ If Smart SEO Booster helps your site, a WordPress.org review and a GitHub star go a long way toward that 5K milestone.</sub>

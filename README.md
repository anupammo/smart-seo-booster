# Smart SEO Booster

**The lightweight, complete SEO toolkit for WordPress — fast by design, powerful by default.**

Smart SEO Booster gives you every SEO fundamental that actually moves rankings — meta tags, Open Graph & Twitter Cards, JSON-LD schema, XML sitemaps, breadcrumbs, and real-time content scoring — in one clean, modular plugin with **no bloat, no upsell walls, and no complex add-ons**.

[![Version](https://img.shields.io/badge/version-2.1.0-blue.svg)](https://github.com/anupammo/smart-seo-booster)
[![WordPress](https://img.shields.io/badge/WordPress-5.9%E2%80%936.9-21759b.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb4.svg)](https://www.php.net/)
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

- **Meta tags** — automatic title & meta description injection
- **Open Graph & Twitter Cards** — per-post social preview with live editor
- **Per-post SEO meta box** — tabbed UI: Basic / Social / Advanced / Analysis
- **JSON-LD schema** — Article, FAQ, LocalBusiness, Organization, ProfilePage
- **Content auditor** — word count, headings, image alt coverage
- **SEO score engine** — admin bar badge, dashboard widget, post-list column, per-post metabox
- **Internal link analysis** — counts internal links per post
- **Canonical URLs & robots meta** — per-post control
- **Focus keyword analysis** — density + placement checks in the editor

### 🛠️ Planned (see [Roadmap](#-development-roadmap))

- **XML Sitemaps** — posts, pages, CPTs, images *(critical parity feature)*
- **Breadcrumbs** — shortcode, block & `BreadcrumbList` schema
- **Global title/meta templates** — `%%title%%`, `%%sitename%%`, `%%sep%%` variables
- **Dynamic per-post schema** — wire the schema-type selector to real output
- **Auto meta description** — smart fallback from excerpt/content
- **Redirection manager** — simple 301/302 + 404 log
- **Search-engine verification** — Google / Bing / Pinterest meta fields
- **Featured-image OG fallback** — never ship a blank social preview
- **Sitewide noindex controls** — archives, tags, search, paginated pages

---

## 🥊 Competitive Positioning (2026)

Smart SEO Booster competes in the **lightweight/automated lane** — alongside Slim SEO and The SEO Framework — not the heavyweight Rank Math/Yoast lane. Our edge: **automation of Slim SEO + the visual editor UX of Rank Math, without the weight of either.**

| Capability | Smart SEO Booster (target v2.5) | Slim SEO | The SEO Framework | Rank Math (Free) | Yoast (Free) |
|---|---|---|---|---|---|
| Meta title/description | ✅ | ✅ | ✅ | ✅ | ✅ |
| OG / Twitter Cards | ✅ | ✅ | ✅ | ✅ | ✅ |
| Live social/search preview | ✅ | ➖ | ➖ | ✅ | ✅ |
| JSON-LD schema | ✅ (10+ types) | ✅ | ✅ | ✅ (18+ types) | ✅ (limited) |
| XML sitemap | 🛠️ planned | ✅ | ✅ | ✅ | ✅ |
| Breadcrumbs | 🛠️ planned | ✅ | ✅ | ✅ | ✅ |
| Content/SEO scoring | ✅ | ➖ | ➖ | ✅ | ✅ |
| Redirection manager | 🛠️ planned | ➖ (Pro) | ➖ | ✅ | ➖ (Premium) |
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

**Requirements:** WordPress 5.9+ · PHP 8.0+ · Tested up to WordPress 6.9

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

### 🔴 Phase 1 — Foundation & Fixes *(v2.1.x → v2.2)*  ·  **Priority: CRITICAL**
> Goal: a rock-solid, consistent, spotless base. Ship nothing new until these are clean.

- [ ] **Fix version consistency** — header, constant, `readme.txt`, and `@since` tags all agree (`2.1.0`)
- [ ] **Fix emoji encoding (mojibake)** — replace corrupted `ðŸŽ¯`/`âœ…` byte sequences with proper UTF-8 emoji or dashicons
- [ ] **De-duplicate meta output** — `class-seo-core.php` and `class-meta-fields.php` both emit `og:*`/description on singular views; consolidate into one authority
- [ ] **Move inline CSS/JS to enqueued assets** — remove `wp_add_inline_script` mega-strings and inline `<style>` blocks
- [ ] **Remove/enqueue dead code** — `js/block-editor.js` is shipped but never enqueued
- [ ] **Dynamic Article schema** — remove hardcoded author name; pull from post author
- [ ] **Pass WordPress.org Plugin Check** with zero errors/warnings
- [ ] **Consolidate documentation** — fold the 15+ scattered `*.md` dev notes into this README + `/docs`

### 🟠 Phase 2 — Parity Features *(v2.3)*  ·  **Priority: HIGH**
> Goal: close every "table-stakes" gap so no reviewer can say "but it doesn't even have X".

- [ ] **XML Sitemap** — posts, pages, CPTs, images; `/sitemap.xml`; robots.txt reference
- [ ] **Breadcrumbs** — function, shortcode, block + `BreadcrumbList` JSON-LD
- [ ] **Per-post schema wiring** — connect the existing schema-type selector to real output
- [ ] **Global meta templates** — `%%title%% %%sep%% %%sitename%%` variable system
- [ ] **Auto meta description** — excerpt → trimmed content fallback
- [ ] **Featured-image OG fallback**
- [ ] **Search-engine verification fields** — Google / Bing / Pinterest

### 🟡 Phase 3 — Modern UX & Trust *(v2.4)*  ·  **Priority: MEDIUM**
> Goal: make it *feel* premium so first-time users leave 5-star reviews.

- [ ] **Setup wizard** — 4-step onboarding (site type, defaults, verification, done)
- [ ] **Redesigned settings** — tabbed, card-based, WP-native, fully responsive
- [ ] **Global noindex controls** — archives, tags, search, paginated pages
- [ ] **Accessibility pass** — WCAG 2.1 AA, keyboard nav, screen-reader labels
- [ ] **Block-editor sidebar plugin** — native Gutenberg SEO panel (replaces classic metabox on modern editor)
- [ ] **Full i18n** — updated `.pot`, RTL-safe CSS

### 🟢 Phase 4 — Differentiators *(v2.5+)*  ·  **Priority: GROWTH**
> Goal: give people a reason to *choose you over Slim SEO*.

- [ ] **Redirection manager** — 301/302 + 404 log (kept simple, no external DB bloat)
- [ ] **Bulk SEO editor** — edit titles/descriptions across posts in one screen
- [ ] **Local SEO block** — LocalBusiness with hours/geo
- [ ] **WooCommerce basics** — Product schema, OG for products
- [ ] **Import from Yoast/Rank Math** — one-click migration (removes switching friction)

---

## ✅ Release Checklist

Run before **every** WordPress.org submission or update.

### Code quality
- [ ] `Plugin Check` plugin: **0 errors, 0 warnings**
- [ ] PHPCS with `WordPress-Extra` ruleset passes
- [ ] No PHP notices/warnings with `WP_DEBUG` on
- [ ] Tested on PHP 8.0, 8.1, 8.2, 8.3
- [ ] Tested on WordPress 5.9 (min) and latest (6.9)

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

**Anupam Mondal** — Full-Stack Developer & SEO Consultant · [@anupammo](https://github.com/anupammo)

---

<sub>⭐ If Smart SEO Booster helps your site, a WordPress.org review and a GitHub star go a long way toward that 5K milestone.</sub>

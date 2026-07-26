# Changelog

All notable changes to Smart SEO Booster will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1.1] - 2026-07-24

### Added
- WordPress Playground preview blueprint (`assets/blueprints/blueprint.json`), enabling the "Live Preview" button on the wp.org listing.

### Changed
- Refreshed listing icon and banner; updated screenshots and captions. No functional code changes.

## [2.1.0] - 2026-07-19

Complete SEO toolkit release. All capabilities below ship together in 2.1.0.

### Added
- Per-post SEO meta box (live search & social previews) and an SEO score engine (admin bar, dashboard widget, post-list column, per-post analysis)
- Automatic titles & meta descriptions with a template variable system; Open Graph & Twitter Cards with featured-image fallback; single de-duplicated meta output
- XML sitemaps (posts, pages, CPTs, images) at /sitemap.xml + robots.txt reference
- Breadcrumbs (shortcode, template tag, block, BreadcrumbList JSON-LD)
- JSON-LD schema: 14 per-post types, dynamic/de-personalized/filterable, with publish & modified dates on all types
- Global no-index controls via the wp_robots filter; redirection manager (301/302/307) + 404 log; one-click import from Yoast SEO and Rank Math
- Site-wide SEO audit dashboard (score gauge, distribution chart, opportunities, lowest-scoring list)
- Google Analytics 4 & Microsoft Clarity; verification for Google, Bing, Yandex, Baidu, Pinterest
- GEO / AI SEO: /llms.txt, AI-crawler training opt-out, optional speakable data
- Automatic image alt text with a missing-alt audit; WooCommerce Product schema & OG; Local SEO shortcode/block
- Native Gutenberg SEO sidebar; 4-step setup wizard; blocks (social share, CTA, breadcrumb, dates) with patterns; bulk SEO editor
- Developer credit (Plugin URI / Author URI, admin footer, opt-in front-end link)

### Changed
- Professional, accessible admin UI (WAI-ARIA tabs, Dashicons instead of emoji, RTL-safe); properly enqueued assets; translation-ready

### Fixed
- SEO Audit report no longer renders blank (now a site-wide report)
- Corrected UTF-8 emoji encoding; aligned version across header, constant and readme

## [2.0.0] - 2025-01-01

### Added
- Complete UI redesign with WordPress admin consistency
- Advanced schema markup detection and validation
- Internal and external link analysis functionality
- Broken link detection algorithms
- Content structure analysis and recommendations
- Enhanced scoring algorithm with weighted factors
- Modern CSS with WordPress admin color variables
- Responsive design for mobile and tablet devices

### Changed
- Menu structure reorganization for better user experience
- Plugin architecture refactoring for modularity
- Database queries optimization for better performance
- Template system enhancement for easier customization

### Fixed
- Compatibility issues with WordPress 6.4+
- PHP 8.0+ compatibility improvements
- Template loading errors in certain configurations
- CSS conflicts with admin themes

## [1.1.0] - 2024-12-01

### Added
- Basic SEO audit functionality
- Title and meta description analysis
- Image alt text validation
- H1 tag structure analysis
- Basic robots.txt validation
- Initial plugin settings framework

### Improved
- Plugin architecture for better extensibility
- Error handling and user feedback
- Code documentation and comments

### Fixed
- Minor bugs in data processing
- Template rendering issues
- Localization string corrections

## [1.0.0] - 2024-11-01

### Added
- Initial release of Smart SEO Booster
- Dynamic JSON-LD schema injection for multiple types
- Basic content audit features
- Internal link analysis functionality
- WordPress admin dashboard integration
- Localization support with .pot file
- Basic settings and configuration options

### Features
- Schema types: Article, FAQ, LocalBusiness, Organization, ProfilePage
- Content metrics: word count, headings, image coverage
- Link analysis: internal link mapping and quality assessment
- Admin interface: clean, intuitive dashboard design
- Compatibility: WordPress 5.2+ and PHP 7.4+

---

## Release Information

### Versioning Strategy
- **Major versions** (x.0.0): Significant feature additions, major UI changes, or breaking changes
- **Minor versions** (x.y.0): New features, enhancements, or notable improvements
- **Patch versions** (x.y.z): Bug fixes, security updates, or minor improvements

### Support Policy
- **Latest major version**: Full support with new features and security updates
- **Previous major version**: Security updates and critical bug fixes for 12 months
- **Older versions**: Community support only

### WordPress Compatibility
- **Current target**: WordPress 5.0 - 6.8+
- **PHP requirement**: 7.4 - 8.3+
- **Testing matrix**: Latest 3 WordPress versions with PHP 7.4, 8.0, 8.1, 8.2, 8.3

### Security Updates
Security vulnerabilities are addressed with highest priority. Users are encouraged to update immediately when security releases are available.

## Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details on:
- Code standards and style guides
- Testing requirements
- Submission process
- Bug reporting guidelines

## Links

- [WordPress Plugin Directory](https://wordpress.org/plugins/smart-seo-booster/)
- [GitHub Repository](https://github.com/anupammo/smart-seo-booster)
- [Documentation](https://github.com/anupammo/smart-seo-booster/wiki)
- [Issue Tracker](https://github.com/anupammo/smart-seo-booster/issues)
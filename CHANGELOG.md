# Changelog

All notable changes to Smart SEO Booster will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.4.0] - 2026-07-18

### Added
- Setup wizard: 4-step guided onboarding (site type → titles → features → verification), shown on activation, merging into options without wiping unrelated settings
- Native block-editor (Gutenberg) SEO sidebar bound to REST meta, with a live search-snippet preview
- Redesigned tabbed, card-based settings screen — accessible (WAI-ARIA tabs, keyboard navigation) and responsive
- Global no-index controls for category/tag/taxonomy, author, date, search and paginated archives

### Changed
- Robots directives (per-post + global) now handled centrally through the `wp_robots` filter, eliminating duplicate robots tags
- Classic SEO metabox is hidden on the block editor (the sidebar replaces it); its tabs gained full ARIA + keyboard support
- Regenerated `.pot`; the block sidebar registers script translations

## [2.3.0] - 2026-07-18

### Added
- XML sitemaps: index + per-type sitemaps (posts, pages, CPTs) with images at `/sitemap.xml`, referenced from robots.txt; option to disable the core WordPress sitemap
- Breadcrumbs: `smart_seo_breadcrumbs()` template tag, `[smart_seo_breadcrumbs]` shortcode, and `BreadcrumbList` JSON-LD
- Per-post schema type selector wired to real JSON-LD output (14 types; Article-family built richly, others via a valid generic builder)
- Global title & meta description templates with a variable system (`%%title%%`, `%%sitename%%`, `%%sep%%`, `%%excerpt%%`, `%%category%%`, and more)
- Search-engine verification meta tags for Google, Bing, Pinterest and Yandex
- Expanded, config-driven settings screen (General, Titles & Metas, Webmaster, Schema Details) with whitelist sanitization

### Changed
- Organization and LocalBusiness schema now populated from plugin settings
- Version bumped to 2.3.0 with a self-healing rewrite-rule flush so `/sitemap.xml` resolves after updates

## [2.1.0] - 2025-01-04

### Added
- WordPress 6.8 compatibility and testing
- Comprehensive 20+ point SEO analysis framework
- PageSpeed Insights-style modern interface design
- Advanced content quality assessment algorithms
- Image optimization analysis and recommendations
- Social media optimization checks (Open Graph, Twitter Cards)
- Technical SEO validation (robots.txt, SSL, URL structure)
- Security enhancements with proper nonce verification
- Input sanitization and output escaping throughout
- Comprehensive error handling and user feedback
- Enhanced WordPress coding standards compliance
- Performance optimizations for large sites

### Improved
- Security: Added nonce verification for all form submissions
- Security: Implemented proper input sanitization and output escaping
- Security: Enhanced user capability checks throughout admin area
- Performance: Optimized database queries and caching
- UI/UX: Modern WordPress admin design consistency
- Accessibility: Enhanced keyboard navigation and screen reader support
- Code quality: Full PSR-12 and WordPress coding standards compliance

### Fixed
- All PHP warnings and notices in audit report template
- Undefined variable errors in comprehensive analysis
- Compatibility issues with latest WordPress versions
- Template file loading with proper security checks
- Asset loading with proper versioning for cache busting

### Security
- Implemented WordPress security best practices
- Added proper user capability verification
- Enhanced data validation and sanitization
- Prevented direct file access throughout plugin

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
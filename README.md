# Smart SEO Booster

**A comprehensive WordPress SEO plugin designed for optimal performance and extensibility**

WordPress plugin that enhances SEO with features like title/meta optimization, schema markup injection, content auditing, and internal link analysis. Built with a clean, modular architecture following WordPress best practices.

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/anupammo/smart-seo-booster)
[![License](https://img.shields.io/badge/license-GPL2+-green.svg)](LICENSE)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)

## 🚀 Features

### ✅ Core SEO Features
- **Meta Tags Management**: Automatic title and description injection
- **Schema Markup**: JSON-LD structured data for better search visibility
- **Content Auditing**: Real-time SEO analysis with actionable insights
- **Internal Link Analysis**: Track and optimize internal linking structure
- **Admin Dashboard**: Clean, intuitive settings interface

### 🎯 Schema Types Supported
- **Article Schema**: For blog posts and articles
- **Organization Schema**: For business information
- **Local Business Schema**: For location-based businesses
- **FAQ Schema**: For FAQ pages
- **Profile Page Schema**: For author/profile pages

### 📊 Audit Metrics
- Word count analysis
- Heading structure evaluation
- Image alt text validation
- Internal link assessment
- Schema markup status

## 🔧 Installation

### Method 1: WordPress Admin (Recommended)
1. Download the plugin ZIP file
2. Go to **Plugins > Add New** in WordPress admin
3. Click **Upload Plugin** and select the ZIP file
4. Activate the plugin

### Method 2: Manual Installation
1. Upload the `smart-seo-booster` folder to `/wp-content/plugins/`
2. Activate the plugin through WordPress admin

### Method 3: Development Setup
```bash
# Clone the repository
git clone https://github.com/anupammo/smart-seo-booster.git

# Move to WordPress plugins directory
mv smart-seo-booster /path/to/wordpress/wp-content/plugins/

# Activate via WP-CLI (optional)
wp plugin activate smart-seo-booster
```

## 🎛️ Configuration

### Basic Setup
1. Navigate to **Smart SEO** in WordPress admin
2. Enable **Schema Markup** in settings
3. Configure additional options as needed

### Advanced Configuration
The plugin automatically detects page types and applies appropriate schema:
- Blog posts → Article schema
- FAQ pages → FAQ schema
- About pages → Profile schema
- Contact/Services → Local Business schema
- Homepage → Organization schema

## 🧪 Testing Features

### 1. Test Schema Markup
```bash
# View page source and look for JSON-LD script tags
# Or use Google's Rich Results Test
https://search.google.com/test/rich-results
```

### 2. Test Content Auditor
1. Edit any post/page in WordPress
2. Check for SEO audit notices in the editor
3. Verify metrics: word count, headings, images, links

### 3. Test Admin Interface
1. Go to **Smart SEO** menu
2. Access **Audit Report** submenu
3. Verify settings are saved correctly

### 4. Test Link Analyzer
1. Create content with internal links
2. Check admin notices for link analysis
3. Verify internal link counting accuracy

## 📁 Project Structure

```
smart-seo-booster/
├── 📄 smart-seo-booster.php      # Main plugin file with bootstrap logic
├── 📄 uninstall.php              # Cleanup logic for plugin deletion
├── 📄 readme.txt                 # WordPress.org plugin description
├── 📄 README.md                  # This documentation file
├── 📄 LICENSE                    # GPL2+ license file
├── 📂 includes/                  # Core PHP classes
│   ├── 🔧 class-loader.php       # Autoloader for modular classes
│   ├── 🎨 class-admin-ui.php     # Admin panel UI and menu logic
│   ├── ⚡ class-seo-core.php     # Core SEO functionality
│   ├── 📋 class-schema-generator.php # JSON-LD schema builder
│   ├── 🔍 class-content-auditor.php  # Content analysis engine
│   ├── 🔗 class-link-analyzer.php    # Internal link analysis
│   └── ⚙️ class-settings.php     # Plugin settings management
├── 📂 templates/                 # UI templates
│   └── 📊 audit-report.php       # SEO audit report interface
├── 📂 schema/                    # JSON-LD schema templates
│   ├── 📰 article-schema.php     # Article schema template
│   ├── ❓ faq-schema.php         # FAQ schema template
│   ├── 🏢 local-business-schema.php # Local business schema
│   ├── 🏛️ organization-schema.php   # Organization schema
│   └── 👤 profile-page-schema.php   # Profile page schema
├── 📂 css/                       # Stylesheets
│   └── 🎨 admin.css              # Admin interface styles
└── 📂 languages/                 # Internationalization
    └── 🌐 smart-seo-booster.pot  # Translation template
```

## 🔧 Development Principles

### 🎯 **Modularity**
Each feature is isolated in separate classes for easy maintenance and extension.

### 📈 **Scalability**
Schema templates are reusable and dynamically injected based on page context.

### ⚡ **Performance**
Minimal WordPress compatibility using native hooks and lightweight operations.

### 🔒 **Security**
- Input sanitization via `esc_html()`, `sanitize_text_field()`
- Nonce verification for admin forms
- Proper capability checks

## 🐛 Troubleshooting

### Common Issues

**❌ Plugin Activation Error**
```
Fatal error: Cannot redeclare Smart_SEO_Admin_UI::init()
```
**✅ Solution**: This has been fixed in the latest version. Ensure you're using the updated files.

**❌ Schema Not Appearing**
- Verify schema is enabled in settings
- Check page type matches schema conditions
- Use browser dev tools to inspect page source

**❌ Admin Menu Missing**
- Ensure user has `manage_options` capability
- Check for plugin conflicts
- Verify all files are uploaded correctly

### Debug Mode
Enable WordPress debug mode for detailed error logging:
```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 Changelog

### Version 1.0.0
- ✅ Initial release
- ✅ Core SEO functionality with enhanced meta tags
- ✅ Schema markup generation (5 types supported)
- ✅ Advanced content auditing system with SEO scoring
- ✅ Admin interface with improved styling
- ✅ Internal link analysis
- ✅ Open Graph meta tags for social sharing
- ✅ Comprehensive testing framework
- ✅ Fixed duplicate method declaration issue

### Recent Improvements
- **Enhanced Meta Tags**: Added Open Graph tags for better social media sharing
- **Advanced Content Auditor**: Now provides SEO scoring and actionable recommendations
- **Improved Admin UI**: Enhanced styling with better visual feedback
- **Comprehensive Testing**: Added detailed testing guide and automated validation
- **Better Error Handling**: Improved code safety and WordPress compatibility

## 📄 License

This project is licensed under the GPL2+ License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Anupam Mondal**
- GitHub: [@anupammo](https://github.com/anupammo)

## 🙏 Acknowledgments

- WordPress community for best practices
- Schema.org for structured data standards
- Contributors and testers

---

**⭐ If this plugin helps your SEO efforts, please consider starring the repository!**

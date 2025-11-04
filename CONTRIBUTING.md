# Contributing to Smart SEO Booster

Thank you for your interest in contributing to Smart SEO Booster! We welcome contributions from the community and are grateful for any help you can provide.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Contributing Guidelines](#contributing-guidelines)
- [Coding Standards](#coding-standards)
- [Reporting Issues](#reporting-issues)
- [Feature Requests](#feature-requests)
- [Pull Request Process](#pull-request-process)
- [Testing](#testing)
- [Documentation](#documentation)

## Code of Conduct

This project and everyone participating in it is governed by our commitment to creating a welcoming and inclusive environment. Please be respectful and professional in all interactions.

## Getting Started

1. **Fork the repository** on GitHub
2. **Clone your fork** locally
3. **Create a new branch** for your feature or bugfix
4. **Make your changes** following our coding standards
5. **Test thoroughly** using our testing guidelines
6. **Submit a pull request** with a clear description

## Development Setup

### Prerequisites

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **Node.js**: 14+ (for development tools)
- **Composer**: For PHP dependencies (if any)

### Local Development Environment

1. Set up a local WordPress installation
2. Clone the repository into your plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/anupammo/smart-seo-booster.git
   ```
3. Activate the plugin in WordPress admin
4. Enable WP_DEBUG in your wp-config.php:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```

## Contributing Guidelines

### Types of Contributions

- **Bug fixes**: Help us improve the plugin stability
- **New features**: Enhance functionality within the plugin's scope
- **Documentation**: Improve guides, comments, and README files
- **Translations**: Add or improve language translations
- **Performance**: Optimize code for better performance
- **Accessibility**: Improve accessibility compliance

### Before You Start

1. **Check existing issues** to avoid duplicate work
2. **Discuss major changes** by creating an issue first
3. **Follow WordPress best practices** throughout development
4. **Maintain backward compatibility** when possible

## Coding Standards

### PHP Standards

We follow **WordPress Coding Standards** with some additions:

```php
<?php
/**
 * File description
 * 
 * @package SmartSEOBooster
 * @since X.X.X
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class Smart_SEO_Example {
    
    /**
     * Method description
     * 
     * @param string $param Parameter description
     * @return array Returns description
     * @since X.X.X
     */
    public function example_method($param) {
        // Implementation
    }
}
```

### Key Requirements

1. **Security First**: Always sanitize input and escape output
2. **Documentation**: Use PHPDoc blocks for all functions and classes
3. **Naming**: Use descriptive names with proper WordPress conventions
4. **Internationalization**: Make all strings translatable
5. **Performance**: Write efficient code that scales well

### Code Formatting

- **Indentation**: 4 spaces (no tabs)
- **Line length**: Maximum 120 characters
- **Braces**: Opening brace on same line for functions/classes
- **Comments**: Use `//` for single-line, `/* */` for multi-line

### Security Guidelines

```php
// Sanitize input
$user_input = sanitize_text_field($_POST['field_name']);

// Escape output
echo esc_html($user_data);
echo wp_kses_post($html_content);

// Verify nonces
if (!wp_verify_nonce($_POST['nonce'], 'action_name')) {
    wp_die('Security check failed');
}

// Check capabilities
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}
```

## Reporting Issues

### Bug Reports

When reporting bugs, please include:

1. **WordPress version**
2. **PHP version**
3. **Plugin version**
4. **Active theme and plugins**
5. **Steps to reproduce**
6. **Expected vs actual behavior**
7. **Error messages or logs**
8. **Screenshots if applicable**

### Security Issues

For security vulnerabilities:
- **DO NOT** create public issues
- Email security concerns privately
- Allow reasonable time for fixes before disclosure

## Feature Requests

Before requesting features:

1. **Check existing issues** for similar requests
2. **Explain the use case** and benefits
3. **Consider the scope** - does it fit the plugin's purpose?
4. **Provide examples** or mockups if helpful

## Pull Request Process

### Before Submitting

1. **Rebase your branch** on the latest main branch
2. **Test thoroughly** across different environments
3. **Update documentation** if needed
4. **Add/update tests** for new functionality
5. **Check coding standards** compliance

### PR Description

Include in your pull request:

```markdown
## Summary
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Tested on WordPress 5.0+
- [ ] Tested on PHP 7.4+
- [ ] No new PHP errors/warnings
- [ ] Accessibility tested

## Checklist
- [ ] Code follows WordPress standards
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] Backward compatibility maintained
```

### Review Process

1. **Automated checks** must pass
2. **Code review** by maintainers
3. **Testing verification**
4. **Documentation review**
5. **Final approval and merge**

## Testing

### Manual Testing

Test your changes with:

- **Different WordPress versions** (5.0, 6.0, 6.8+)
- **Different PHP versions** (7.4, 8.0, 8.1, 8.2, 8.3)
- **Popular themes** (Twenty Twenty-Four, Astra, etc.)
- **Common plugins** (Yoast SEO, WooCommerce, etc.)

### Testing Checklist

- [ ] Plugin activation/deactivation works
- [ ] No PHP errors in debug log
- [ ] Admin interface functions properly
- [ ] Frontend doesn't break
- [ ] Database operations work correctly
- [ ] Uninstall process is clean

## Documentation

### Required Documentation

1. **Code comments**: Explain complex logic
2. **PHPDoc blocks**: For all public methods
3. **README updates**: For new features
4. **Changelog entries**: For all changes
5. **Help documentation**: For user-facing features

### Documentation Style

- **Clear and concise** language
- **Step-by-step instructions** when appropriate
- **Code examples** for developers
- **Screenshots** for visual features
- **Proper markdown formatting**

## Translation

### Adding Translations

1. **Use translation functions**: `__()`, `_e()`, `esc_html__()`, etc.
2. **Text domain**: Always use `'smart-seo-booster'`
3. **Context**: Add context for ambiguous strings
4. **Placeholders**: Use numbered placeholders for variables

Example:
```php
printf(
    /* translators: 1: number of pages, 2: issue type */
    __('%1$d pages have %2$s issues.', 'smart-seo-booster'),
    $page_count,
    $issue_type
);
```

## Release Process

### Version Numbering

We use [Semantic Versioning](https://semver.org/):

- **Major** (X.0.0): Breaking changes
- **Minor** (X.Y.0): New features, backward compatible
- **Patch** (X.Y.Z): Bug fixes, backward compatible

### Release Checklist

- [ ] Version numbers updated
- [ ] Changelog updated
- [ ] readme.txt updated
- [ ] Translation files updated
- [ ] Testing completed
- [ ] Documentation reviewed

## Questions?

- **General questions**: Use GitHub Discussions
- **Bug reports**: Create GitHub Issues
- **Security concerns**: Email privately
- **Development chat**: Consider joining our community

## Recognition

Contributors will be:
- **Credited** in release notes
- **Listed** in plugin credits
- **Mentioned** in relevant documentation
- **Invited** to be ongoing contributors

Thank you for contributing to Smart SEO Booster! 🚀
# Smart SEO Booster - Testing Guide

This guide helps you verify that all features of the Smart SEO Booster plugin are working correctly.

## 🧪 Pre-Testing Setup

### WordPress Requirements
- WordPress 5.0 or higher
- PHP 8.0 or higher
- Admin access to WordPress dashboard

### Test Environment Setup
1. Install the plugin in a WordPress test environment
2. Activate the plugin
3. Create test content (posts, pages) for comprehensive testing

## 🔍 Feature Testing Checklist

### ✅ 1. Plugin Activation Test
- [ ] Plugin activates without errors
- [ ] No fatal errors in error logs
- [ ] Admin menu "Smart SEO" appears

**Expected Result**: Plugin activates successfully with admin menu visible.

### ✅ 2. Admin Interface Test
```
Navigate to: WordPress Admin → Smart SEO
```
- [ ] Settings page loads correctly
- [ ] "Enable Schema Markup" checkbox is functional
- [ ] Settings can be saved
- [ ] "Audit Report" submenu is accessible

**Expected Result**: Clean admin interface with working settings.

### ✅ 3. Schema Markup Test

#### Test Article Schema
1. Create a blog post
2. View page source
3. Search for `<script type="application/ld+json">`

**Expected Result**: JSON-LD Article schema with:
```json
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "Your Post Title",
  "author": {...},
  "datePublished": "...",
  "publisher": {...}
}
```

#### Test Organization Schema
1. Visit homepage
2. Check page source for schema

**Expected Result**: Organization schema with business information.

#### Test FAQ Schema
1. Create a page with slug "faq"
2. Check for FAQ schema in source

**Expected Result**: FAQPage schema structure.

### ✅ 4. Content Auditor Test

#### Test in Block Editor
1. Edit any post/page
2. Look for admin notices

**Expected Result**: Notice showing:
- Word count
- Heading count
- Image count
- Alt text count

**Sample Notice**:
```
SEO Audit: Words: 345, Headings: 3, Images: 2, Alt Texts: 2
```

### ✅ 5. Link Analyzer Test

#### Test Internal Link Detection
1. Create content with internal links pointing to your site
2. Edit the post in WordPress editor
3. Check for link analysis notice

**Expected Result**: Notice showing:
```
Internal Links: X found in this post.
```

### ✅ 6. Audit Report Test

#### Test Full Audit Report
1. Go to **Smart SEO → Audit Report**
2. Verify all metrics are displayed

**Expected Result**: Table showing:
- Word Count with status
- Headings with recommendations
- Images with alt text validation
- Internal Links with suggestions
- Schema Markup status

### ✅ 7. Meta Tags Test

#### Test Title and Description Injection
1. Visit any page/post on frontend
2. View page source
3. Check `<head>` section

**Expected Result**: Meta tags present:
```html
<title>Your Page Title</title>
<meta name='description' content='Site description' />
```

### ✅ 8. CSS Assets Test

#### Test Admin Styling
1. Go to any Smart SEO admin page
2. Check browser developer tools
3. Verify CSS is loaded

**Expected Result**: `smart-seo-admin` CSS file loaded without 404 errors.

## 🔧 Advanced Testing

### Schema Validation
Use Google's Rich Results Test:
1. Go to https://search.google.com/test/rich-results
2. Enter your page URL
3. Verify schema is detected and valid

### Performance Testing
1. Check page load times before/after plugin activation
2. Verify no significant performance impact
3. Test with caching plugins enabled

## 🐛 Troubleshooting Tests

### Test Common Issues

#### 1. Test Duplicate Method Error (Fixed)
- [ ] Plugin activates without "Cannot redeclare" errors
- [ ] All classes load properly

#### 2. Test Schema Output
If schema isn't appearing:
- [ ] Verify "Enable Schema Markup" is checked in settings
- [ ] Check if page type matches schema conditions
- [ ] Inspect page source for JSON-LD script tags

#### 3. Test Admin Notices
If auditor notices aren't showing:
- [ ] Verify you're using block editor (Gutenberg)
- [ ] Check if global `$post` object exists
- [ ] Test with different post types

## 📋 Test Results Template

Use this template to document your testing:

```
=== Smart SEO Booster Test Results ===
Date: ___________
WordPress Version: ___________
PHP Version: ___________
Theme: ___________

✅ Plugin Activation: PASS/FAIL
✅ Admin Interface: PASS/FAIL
✅ Schema Markup: PASS/FAIL
   - Article Schema: PASS/FAIL
   - Organization Schema: PASS/FAIL
   - FAQ Schema: PASS/FAIL
✅ Content Auditor: PASS/FAIL
✅ Link Analyzer: PASS/FAIL
✅ Audit Report: PASS/FAIL
✅ Meta Tags: PASS/FAIL
✅ CSS Assets: PASS/FAIL

Issues Found:
- 
- 

Notes:
- 
- 
```

## 🚀 Automated Testing

For developers, run the included test script:

```bash
php test-plugin.php
```

This script validates:
- File structure integrity
- Class method declarations
- Schema file validity
- Plugin header correctness

## 📞 Support

If tests fail or you encounter issues:
1. Check WordPress error logs
2. Enable WordPress debug mode
3. Verify file permissions
4. Test with default theme
5. Deactivate other plugins temporarily

---

**✅ All tests passing? Your Smart SEO Booster plugin is ready for production!**
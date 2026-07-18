# WordPress Plugin Check Fixes Guide

## Issue Summary
Plugin Check found multiple violations in your extended `audit-report.php` file:
- **100+ unprefixed global variables**
- **Unescaped output** in `class-content-auditor.php` (line 117)
- **Unescaped output** in `class-link-analyzer.php` (line 31)  
- **Missing translator comment** (line 768 in audit-report.php)
- **Invalid Network header** in main plugin file

## Quick Fix: Variable Prefixing

### Option 1: Wrap in Function (Recommended)
Encapsulate all audit logic in a function to avoid global scope pollution:

```php
<?php
defined('ABSPATH') || exit;

function smart_seo_render_audit_report() {
    global $post;
    if (!$post) return;

    // All your variables here are now function-scoped
    $content = $post->post_content;
    $word_count = str_word_count(strip_tags($content));
    $posts_analyzed = 0;
    // ... rest of logic
    
    // HTML output
    ?>
    <div class="wrap">
        <!-- Your template -->
    </div>
    <?php
}

// Call the function
smart_seo_render_audit_report();
```

### Option 2: Prefix All Variables
Use find-and-replace to add `smart_seo_` prefix:

**Find:** `$posts_analyzed`  
**Replace:** `$smart_seo_posts_analyzed`

Repeat for all 100+ variables listed in the error log.

## Specific Fixes

### 1. Fix Unescaped Output in class-content-auditor.php

**Current (Line ~117):**
```php
echo "<div class='notice {$score_class}'>";
```

**Fixed:**
```php
// Whitelist and escape
$allowed_classes = ['notice-success', 'notice-warning', 'notice-error'];
if (!in_array($score_class, $allowed_classes, true)) {
    $score_class = 'notice-warning';
}
echo '<div class="notice ' . esc_attr($score_class) . '">';
```

### 2. Fix Unescaped Output in class-link-analyzer.php

**Current (Line ~31):**
```php
echo "<p><strong>{$status_icon} Internal Links:</strong> {$count} found in this post. ({$status_text})</p>";
```

**Fixed:**
```php
echo '<p><strong>' . esc_html($status_icon) . ' ' . esc_html__('Internal Links:', 'smart-seo-booster') . '</strong> ';
// translators: 1: number of links, 2: status text
echo sprintf(
    esc_html__('%1$d found in this post. (%2$s)', 'smart-seo-booster'),
    (int) $count,
    esc_html($status_text)
) . '</p>';
```

### 3. Add Missing Translator Comment (Line 768)

**Before:**
```php
$message = sprintf(__('Found %d issues in %s', 'smart-seo-booster'), $count, $type);
```

**After:**
```php
// translators: 1: number of issues, 2: issue type
$message = sprintf(__('Found %d issues in %s', 'smart-seo-booster'), $count, $type);
```

### 4. Remove Invalid Network Header

**In smart-seo-booster.php header, remove:**
```php
Network: false
```

**Or set to true only if needed:**
```php
Network: true
```

## Complete Variable Prefix Map

Apply these replacements globally in `audit-report.php`:

```
$posts_analyzed → $smart_seo_posts_analyzed
$meta_descriptions_missing → $smart_seo_meta_descriptions_missing
$meta_descriptions_short → $smart_seo_meta_descriptions_short
$meta_descriptions_long → $smart_seo_meta_descriptions_long
$title_tags_missing → $smart_seo_title_tags_missing
$title_tags_short → $smart_seo_title_tags_short
$title_tags_long → $smart_seo_title_tags_long
$h1_tags_missing → $smart_seo_h1_tags_missing
$h1_tags_multiple → $smart_seo_h1_tags_multiple
$images_missing_alt → $smart_seo_images_missing_alt
$pages_blocked_indexing → $smart_seo_pages_blocked_indexing
$pages_without_title → $smart_seo_pages_without_title
$pages_without_meta_desc → $smart_seo_pages_without_meta_desc
$pages_non_200_status → $smart_seo_pages_non_200_status
$links_without_descriptive_text → $smart_seo_links_without_descriptive_text
$non_crawlable_links → $smart_seo_non_crawlable_links
$canonical_issues → $smart_seo_canonical_issues
$hreflang_issues → $smart_seo_hreflang_issues
$keyword_in_title_missing → $smart_seo_keyword_in_title_missing
$keyword_in_meta_desc_missing → $smart_seo_keyword_in_meta_desc_missing
$keyword_in_first_100_words_missing → $smart_seo_keyword_in_first_100_words_missing
$short_content_pages → $smart_seo_short_content_pages
$pages_without_headings → $smart_seo_pages_without_headings
$images_without_optimization → $smart_seo_images_without_optimization
$images_without_descriptive_names → $smart_seo_images_without_descriptive_names
$pages_without_internal_links → $smart_seo_pages_without_internal_links
$pages_without_external_links → $smart_seo_pages_without_external_links
$non_ssl_links → $smart_seo_non_ssl_links
$broken_internal_links → $smart_seo_broken_internal_links
$pages_without_schema → $smart_seo_pages_without_schema
$slow_loading_pages → $smart_seo_slow_loading_pages
$non_mobile_friendly_pages → $smart_seo_non_mobile_friendly_pages
$pages_with_long_urls → $smart_seo_pages_with_long_urls
$duplicate_content_issues → $smart_seo_duplicate_content_issues
$total_words → $smart_seo_total_words
$total_internal_links → $smart_seo_total_internal_links
$total_external_links → $smart_seo_total_external_links
$total_images → $smart_seo_total_images
$short_content_count → $smart_seo_short_content_count
$large_images_count → $smart_seo_large_images_count
$poor_internal_linking_count → $smart_seo_poor_internal_linking_count
$broken_external_links_count → $smart_seo_broken_external_links_count
$missing_schema_count → $smart_seo_missing_schema_count
$missing_og_tags_count → $smart_seo_missing_og_tags_count
$technical_issues_count → $smart_seo_technical_issues_count
$total_issues → $smart_seo_total_issues
$overall_score → $smart_seo_overall_score
$robots_txt_valid → $smart_seo_robots_txt_valid
$robots_url → $smart_seo_robots_url
$robots_response → $smart_seo_robots_response
$args → $smart_seo_args
$post_ids → $smart_seo_post_ids
$post_content → $smart_seo_post_content
$post_url → $smart_seo_post_url
$meta_robots → $smart_seo_meta_robots
$seo_title → $smart_seo_seo_title
$final_title → $smart_seo_final_title
$meta_description → $smart_seo_meta_description
$response → $smart_seo_response
$link_text → $smart_seo_link_text
$generic_texts → $smart_seo_generic_texts
$canonical → $smart_seo_canonical
$h1_count → $smart_seo_h1_count
$img → $smart_seo_img
$hreflang → $smart_seo_hreflang
$focus_keyword → $smart_seo_focus_keyword
$title_lower → $smart_seo_title_lower
$meta_desc_lower → $smart_seo_meta_desc_lower
$content_text → $smart_seo_content_text
$first_100_words → $smart_seo_first_100_words
$first_100_lower → $smart_seo_first_100_lower
$word_count → $smart_seo_word_count
$heading_count → $smart_seo_heading_count
$img_match → $smart_seo_img_match
$img_src → $smart_seo_img_src
$img_filename → $smart_seo_img_filename
$internal_link_count → $smart_seo_internal_link_count
$external_link_count → $smart_seo_external_link_count
$broken_links → $smart_seo_broken_links
$href → $smart_seo_href
$post_slug → $smart_seo_post_slug
$has_schema → $smart_seo_has_schema
$yoast_schema → $smart_seo_yoast_schema
$og_title → $smart_seo_og_title
$og_description → $smart_seo_og_description
$og_image → $smart_seo_og_image
$similar_titles → $smart_seo_similar_titles
$similar_id → $smart_seo_similar_id
$similar_title → $smart_seo_similar_title
$similarity → $smart_seo_similarity
$max_possible_issues → $smart_seo_max_possible_issues
$score_percentage → $smart_seo_score_percentage
$score_class → $smart_seo_score_class
$score_color → $smart_seo_score_color
$indexing_score → $smart_seo_indexing_score
$title_score → $smart_seo_title_score
$meta_desc_score → $smart_seo_meta_desc_score
$http_status_score → $smart_seo_http_status_score
$alt_text_score → $smart_seo_alt_text_score
$robots_score → $smart_seo_robots_score
$canonical_score → $smart_seo_canonical_score
$h1_score → $smart_seo_h1_score
$keyword_optimization_score → $smart_seo_keyword_optimization_score
$content_quality_score → $smart_seo_content_quality_score
$image_optimization_score → $smart_seo_image_optimization_score
$internal_linking_score → $smart_seo_internal_linking_score
$external_links_score → $smart_seo_external_links_score
$schema_markup_score → $smart_seo_schema_markup_score
$social_media_score → $smart_seo_social_media_score
$technical_seo_score → $smart_seo_technical_seo_score
$content_uniqueness_score → $smart_seo_content_uniqueness_score
$avg_words_per_page → $smart_seo_avg_words_per_page
$avg_internal_links_per_page → $smart_seo_avg_internal_links_per_page
$avg_external_links_per_page → $smart_seo_avg_external_links_per_page
$avg_images_per_page → $smart_seo_avg_images_per_page
```

## PowerShell Batch Rename Script

Save as `fix-variables.ps1` and run in the plugin directory:

```powershell
$file = "C:\Users\anupa\Local Sites\test-wp\app\public\wp-content\plugins\smart-seo-booster\templates\audit-report.php"
$content = Get-Content $file -Raw

$replacements = @{
    '$posts_analyzed' = '$smart_seo_posts_analyzed'
    '$meta_descriptions_missing' = '$smart_seo_meta_descriptions_missing'
    # ... add all other mappings
}

foreach ($old in $replacements.Keys) {
    $new = $replacements[$old]
    $content = $content -replace [regex]::Escape($old), $new
}

Set-Content $file $content -NoNewline
Write-Host "✅ Variables prefixed successfully"
```

## Additional Escaping Fixes

### class-seo-core.php (Line 68)
**Issue:** Unescaped string interpolation in meta tag output

**Current:**
```php
echo "<meta name='description' content='{$desc}' />\n";
```

**Fixed:**
```php
echo '<meta name="description" content="' . esc_attr($desc) . '" />' . "\n";
```

### class-settings.php (Line 515)
**Issue:** Unescaped variable in checkbox input

**Current:**
```php
echo "<input type='checkbox' name='smart_seo_options[{$args['name']}]' $checked />";
```

**Fixed:**
```php
$field_name = 'smart_seo_options[' . esc_attr($args['name']) . ']';
echo '<input type="checkbox" name="' . esc_attr($field_name) . '" value="1" ' . $checked . ' />';
```

### class-seo-score-display.php (Line 303)
**Issue:** Unescaped `get_edit_post_link()` output

**Fix:**
```php
// Before
echo get_edit_post_link($post_id);

// After
echo esc_url(get_edit_post_link($post_id));
```

### help-page.php (Line 37)
**Issue:** Unescaped `admin_url()` output

**Fix:**
```php
// Before
echo admin_url('admin.php?page=smart-seo');

// After
echo esc_url(admin_url('admin.php?page=smart-seo'));
```

## Summary Checklist

**Repository Files (Fixed):**
- [x] ✅ class-seo-core.php - Escaped all meta tag outputs
- [x] ✅ class-settings.php - Escaped checkbox field name
- [x] ✅ class-content-auditor.php - Escaped score_class with whitelist
- [x] ✅ class-link-analyzer.php - All outputs escaped
- [x] ✅ All translation functions have translator comments
- [x] ✅ POT file updated

**Local Site Files (Need Manual Fix):**
- [ ] Wrap audit-report.php logic in `smart_seo_render_audit_report()` function
- [ ] OR prefix all 100+ variables with `smart_seo_`
- [ ] Fix class-seo-score-display.php line 303 (escape `get_edit_post_link`)
- [ ] Fix help-page.php line 37 (escape `admin_url`)
- [ ] Add translator comment at audit-report.php line 768
- [ ] Remove or fix Network header if present in main plugin file
- [ ] Re-run Plugin Check to verify fixes

**Time Estimate:** 15-30 minutes with find-replace automation.

## WordPress Function Replacements

### Replace strip_tags() with wp_strip_all_tags()

**Issue:** `strip_tags()` is discouraged in WordPress plugins.

**Files Fixed:**
- ✅ `class-seo-core.php` (line 58)
- ✅ `class-content-auditor.php` (line 76)
- ✅ `audit-report.php` (line 8)

**Pattern:**
```php
// Before
$clean = strip_tags($content);

// After
$clean = wp_strip_all_tags($content);
```

### Remove load_plugin_textdomain() Call

**Issue:** WordPress.org auto-loads translations since WP 4.6.

**Fixed in:** `smart-seo-booster.php` (line 164)

**Before:**
```php
load_plugin_textdomain('smart-seo-booster', false, dirname(plugin_basename(__FILE__)) . '/languages');
```

**After:**
```php
// WordPress.org automatically loads translations since WP 4.6
// Manual load_plugin_textdomain() call is no longer needed for wp.org hosted plugins
```

**Note:** Keep the call ONLY if distributing outside WordPress.org or need custom translation loading.

## $_POST Validation Fixes

### class-seo-score-display.php (Lines 675, 697)

**Issue:** Accessing `$_POST['post_id']` without validation.

**Pattern to Find:**
```php
$post_id = sanitize_text_field($_POST['post_id']);
```

**Fix:**
```php
// Validate existence first
if (!isset($_POST['post_id'])) {
    wp_send_json_error('Missing post ID');
    return;
}

// Then sanitize
$post_id = absint($_POST['post_id']);

// Optionally verify it's a valid post
if (!get_post($post_id)) {
    wp_send_json_error('Invalid post ID');
    return;
}
```

**Complete AJAX Handler Pattern:**
```php
public static function ajax_handler() {
    // Verify nonce
    check_ajax_referer('smart_seo_nonce', 'nonce');
    
    // Check capabilities
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Insufficient permissions');
    }
    
    // Validate input exists
    if (!isset($_POST['post_id'])) {
        wp_send_json_error('Missing post ID');
    }
    
    // Sanitize and validate
    $post_id = absint($_POST['post_id']);
    if (!$post_id || !get_post($post_id)) {
        wp_send_json_error('Invalid post ID');
    }
    
    // Process the request
    $result = do_something($post_id);
    wp_send_json_success($result);
}
```

**Alternative with wp_unslash():**
```php
// For text fields
$field = isset($_POST['field']) ? sanitize_text_field(wp_unslash($_POST['field'])) : '';

// For post IDs
$post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;

// For arrays
$data = isset($_POST['data']) && is_array($_POST['data']) 
    ? array_map('sanitize_text_field', wp_unslash($_POST['data'])) 
    : [];
```

## Update URI Header Warning

**Issue:** Custom update checkers not allowed for wp.org plugins.

**Fix:** Remove any of these from plugin header:
```php
// Remove these lines if present:
Update URI: https://example.com/updates
Network: false
```

**If using external updater library:**
- Remove includes like `plugin-update-checker` or similar
- Or keep for non-wp.org distribution only

**Time Estimate:** 15-30 minutes with find-replace automation.

# Smart SEO Booster - Final Fixes for Local Site

## Quick Reference for Remaining Issues

### 1. uninstall.php - Remove Debug Code (Line 47)

**Issue:** `error_log()` should not be in production.

**Option A - Remove entirely:**
```php
// Delete this line:
error_log( 'Smart SEO Booster uninstall: ' . $message );
```

**Option B - Make conditional:**
```php
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
    error_log( 'Smart SEO Booster uninstall: ' . $message );
}
```

---

### 2. readme.txt - Reduce Tags (FIXED ✅)

Changed from 6 tags to 5 (removed "internal links").

---

### 3. audit-report.php - Global Variable Prefixing (Lines 36-100+)

**Solution A: Function Wrapper (Easiest)**
```php
<?php
defined('ABSPATH') || exit;

function smart_seo_render_extended_audit_report() {
    // All existing code here - variables become function-scoped
    $posts_analyzed = 0;
    $meta_descriptions_missing = 0;
    // ... rest of your logic
    
    ?>
    <div class="wrap">
        <!-- Your existing HTML -->
    </div>
    <?php
}

// Call the function
smart_seo_render_extended_audit_report();
```

**Solution B: PowerShell Mass Rename**
```powershell
$file = "C:\Users\anupa\Local Sites\test-wp\app\public\wp-content\plugins\smart-seo-booster\templates\audit-report.php"
$content = Get-Content $file -Raw

$vars = @(
    'posts_analyzed', 'meta_descriptions_missing', 'meta_descriptions_short',
    'meta_descriptions_long', 'title_tags_missing', 'title_tags_short',
    'title_tags_long', 'h1_tags_missing', 'h1_tags_multiple',
    'images_missing_alt', 'pages_blocked_indexing', 'pages_without_title'
    # Add all other variable names from error list
)

foreach ($var in $vars) {
    $content = $content -replace "(?<!\w)\`$$var\b", "`$smart_seo_$var"
}

Set-Content $file $content -NoNewline
Write-Host "✅ Variables prefixed"
```

---

### 4. audit-report.php - Security & Functions (Lines 25, 26, 160, 260)

**Line 25 - $_SERVER validation:**
```php
// Before
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

// After
if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
```

**Line 26 - Nonce handling:**
```php
// Before
wp_verify_nonce( $_POST['smart_seo_nonce'], 'action' );

// After
if ( ! isset( $_POST['smart_seo_nonce'] ) ) {
    wp_die( esc_html__( 'Missing nonce.', 'smart-seo-booster' ) );
}
$nonce = sanitize_text_field( wp_unslash( $_POST['smart_seo_nonce'] ) );
if ( ! wp_verify_nonce( $nonce, 'smart_seo_audit_action' ) ) {
    wp_die( esc_html__( 'Security check failed.', 'smart-seo-booster' ) );
}
```

**Line 160 - strip_tags:**
```php
// Before
$clean = strip_tags( $html );

// After
$clean = wp_strip_all_tags( $html );
```

**Line 260 - parse_url:**
```php
// Before
$parts = parse_url( $url );

// After
$parts = wp_parse_url( $url );
$host = isset( $parts['host'] ) ? $parts['host'] : '';
```

---

### 5. class-admin-ui.php - Request Validation (Lines 190-191)

**Current (Unsafe):**
```php
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && wp_verify_nonce( $_POST['_wpnonce'], 'action' ) ) {
    // Process
}
```

**Fixed:**
```php
if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
    if ( ! isset( $_POST['_wpnonce'] ) ) {
        wp_die( esc_html__( 'Missing nonce.', 'smart-seo-booster' ) );
    }
    $nonce = sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) );
    if ( ! wp_verify_nonce( $nonce, 'smart_seo_admin_action' ) ) {
        wp_die( esc_html__( 'Security verification failed.', 'smart-seo-booster' ) );
    }
    // Safe to process
}
```

**Or use WordPress helper:**
```php
if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
    check_admin_referer( 'smart_seo_admin_action' );
    // Process
}
```

---

### 6. class-meta-fields.php - POST Sanitization (Lines 376, 412, 416, 421)

**Line 376 - Nonce:**
```php
// Before
if ( wp_verify_nonce( $_POST['smart_seo_meta_nonce_field'], 'action' ) ) {

// After
if ( ! isset( $_POST['smart_seo_meta_nonce_field'] ) ) {
    return;
}
$nonce = sanitize_text_field( wp_unslash( $_POST['smart_seo_meta_nonce_field'] ) );
if ( ! wp_verify_nonce( $nonce, 'smart_seo_meta_save' ) ) {
    return;
}
```

**Lines 412, 416, 421 - Field values:**
```php
// Before
$value = sanitize_text_field( $_POST[ $field ] );

// After
if ( isset( $_POST[ $field ] ) ) {
    $value = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
    update_post_meta( $post_id, $field, $value );
}
```

**Complete safe meta save handler:**
```php
public static function save_meta( $post_id ) {
    // Autosave check
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Capability check
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    // Nonce verification
    if ( ! isset( $_POST['smart_seo_meta_nonce_field'] ) ) {
        return;
    }
    $nonce = sanitize_text_field( wp_unslash( $_POST['smart_seo_meta_nonce_field'] ) );
    if ( ! wp_verify_nonce( $nonce, 'smart_seo_meta_save' ) ) {
        return;
    }
    
    // Define allowed fields with types
    $fields = [
        'smart_seo_focus_keyword'    => 'text',
        'smart_seo_meta_description' => 'text',
        'smart_seo_custom_html'      => 'html',
    ];
    
    foreach ( $fields as $field => $type ) {
        if ( isset( $_POST[ $field ] ) ) {
            $raw = wp_unslash( $_POST[ $field ] );
            
            switch ( $type ) {
                case 'html':
                    $value = wp_kses_post( $raw );
                    break;
                case 'url':
                    $value = esc_url_raw( $raw );
                    break;
                case 'int':
                    $value = absint( $raw );
                    break;
                default: // 'text'
                    $value = sanitize_text_field( $raw );
                    break;
            }
            
            update_post_meta( $post_id, $field, $value );
        } else {
            delete_post_meta( $post_id, $field );
        }
    }
}
```

---

## Complete Checklist

**Repository Files (DONE ✅):**
- [x] strip_tags() → wp_strip_all_tags()
- [x] Removed load_plugin_textdomain()
- [x] Fixed all escaping
- [x] Added translator comments
- [x] Updated POT file
- [x] Reduced readme.txt tags to 5

**Local Site Files (TODO):**
- [ ] uninstall.php: Remove error_log() at line 47
- [ ] audit-report.php: Wrap in function OR prefix 100+ variables
- [ ] audit-report.php: Fix $_SERVER validation at line 25
- [ ] audit-report.php: Fix nonce handling at line 26
- [ ] audit-report.php: Replace strip_tags() at line 160
- [ ] audit-report.php: Replace parse_url() at line 260
- [ ] class-admin-ui.php: Add isset() checks at lines 190-191
- [ ] class-meta-fields.php: Add wp_unslash() at lines 376, 412, 416, 421

---

## Quick Apply Script

```powershell
# Navigate to plugin directory
cd "C:\Users\anupa\Local Sites\test-wp\app\public\wp-content\plugins\smart-seo-booster"

# 1. Backup
Copy-Item . "../smart-seo-booster-backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')" -Recurse

# 2. Fix strip_tags (if any remain)
Get-ChildItem -Recurse -Filter *.php | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    $updated = $content -replace 'strip_tags\s*\(', 'wp_strip_all_tags('
    Set-Content $_.FullName $updated -NoNewline
}

# 3. Fix parse_url
Get-ChildItem -Recurse -Filter *.php | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    $updated = $content -replace 'parse_url\s*\(', 'wp_parse_url('
    Set-Content $_.FullName $updated -NoNewline
}

Write-Host "✅ Automated fixes applied. Manual nonce/validation fixes still required."
```

---

## Time Estimate

- **Automated replacements:** 5 minutes
- **Manual nonce/validation fixes:** 20-30 minutes
- **Testing:** 10 minutes
- **Total:** 35-45 minutes

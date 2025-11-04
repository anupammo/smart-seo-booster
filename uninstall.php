<?php
/**
 * Smart SEO Booster Uninstall Script
 * 
 * Fired when the plugin is uninstalled via WordPress admin.
 * Cleans up plugin data and options.
 * 
 * @package SmartSEOBooster
 * @since 2.1.0
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit('Direct access forbidden.');
}

// Check if user has the capability to uninstall plugins
if (!current_user_can('activate_plugins')) {
    exit('Insufficient permissions.');
}

/**
 * Clean up plugin options and data
 */

// Delete plugin settings
delete_option('smart_seo_options');
delete_option('smart_seo_version');
delete_option('smart_seo_installed_date');

// Delete site options (for multisite)
delete_site_option('smart_seo_options');

// Clean up any transients
delete_transient('smart_seo_audit_cache');
delete_transient('smart_seo_schema_cache');

// Optional: Clean up post meta data (uncomment if needed)
// global $wpdb;
// $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_smart_seo_%'");

// Optional: Clean up user meta (uncomment if needed)
// $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'smart_seo_%'");

// Log uninstall for debugging (optional)
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('Smart SEO Booster: Plugin uninstalled and data cleaned up.');
}

<?php
/**
 * Smart SEO Booster Uninstall Script
 * 
 * Fired when the plugin is uninstalled via WordPress admin.
 * Cleans up plugin data and options.
 * 
 * @package SmartSEOBooster
 * @since 1.0
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
delete_option('anupamwp_ssb_options');
delete_option('anupamwp_ssb_version');
delete_option('anupamwp_ssb_installed_date');

// Delete site options (for multisite)
delete_site_option('anupamwp_ssb_options');

// Clean up any transients
delete_transient('anupamwp_ssb_audit_cache');
delete_transient('anupamwp_ssb_schema_cache');

/**
 * Optional: Clean up post meta data
 * 
 * Uncomment the following code to remove all Smart SEO meta fields from posts.
 * WARNING: This will permanently delete all SEO meta data for posts/pages.
 */
/*
global $wpdb;
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
        $wpdb->esc_like('_anupamwp_ssb_') . '%'
    )
);
*/

/**
 * Optional: Clean up user meta
 * 
 * Uncomment the following code to remove all Smart SEO user preferences.
 */
/*
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
        $wpdb->esc_like('anupamwp_ssb_') . '%'
    )
);
*/

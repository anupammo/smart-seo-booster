<?php
/**
 * Uninstall handler — runs when the plugin is deleted from WordPress.
 * Removes all plugin options and per-post SEO meta so nothing is left behind.
 */
defined('WP_UNINSTALL_PLUGIN') || exit;

// Delete plugin options.
delete_option('smart_seo_options');
delete_option('smart_seo_redirects');
delete_option('smart_seo_404_log');
delete_option('smart_seo_rewrite_v');

// Delete short-lived, fixed-name transients (self-expire anyway, but no
// reason to leave them behind on an explicit uninstall).
delete_transient('smart_seo_activation_redirect');
delete_transient('smart_seo_audit_data');

// Delete cached Page Speed results — one transient per URL+strategy checked,
// so the keys are unbounded and can't be deleted by name; a direct query
// against the options table is the standard way WordPress itself expects
// this to be cleaned up (delete_transient() only handles one key at a time).
global $wpdb;
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
        $wpdb->esc_like('_transient_smart_seo_psi_') . '%',
        $wpdb->esc_like('_transient_timeout_smart_seo_psi_') . '%'
    )
);

// Delete all per-post SEO meta (uses the core API — no direct DB queries).
$smart_seo_meta_keys = [
    '_smart_seo_title',
    '_smart_seo_description',
    '_smart_seo_keywords',
    '_smart_seo_canonical',
    '_smart_seo_robots',
    '_smart_seo_og_title',
    '_smart_seo_og_description',
    '_smart_seo_og_image',
    '_smart_seo_og_type',
    '_smart_seo_twitter_card',
    '_smart_seo_twitter_title',
    '_smart_seo_twitter_description',
    '_smart_seo_twitter_image',
    '_smart_seo_schema_type',
    '_smart_seo_focus_keyword',
];

foreach ($smart_seo_meta_keys as $smart_seo_meta_key) {
    delete_post_meta_by_key($smart_seo_meta_key);
}

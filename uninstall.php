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

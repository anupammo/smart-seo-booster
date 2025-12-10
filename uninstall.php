<?php
defined('WP_UNINSTALL_PLUGIN') || exit;

// Delete plugin options
delete_option('smart_seo_options');

// Optional: delete post meta if you store schema type per post
// global $wpdb;
// $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_smart_seo_schema_type'");


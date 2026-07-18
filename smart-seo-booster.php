<?php
/**
 * Plugin Name: Smart SEO Booster
 * Description: Lightweight, complete SEO — meta tags, schema, XML sitemaps, breadcrumbs, social previews & content scoring. Fast, automated, no bloat.
 * Version: 2.4.0
 * Author: Anupam Mondal
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smart-seo-booster
 * Domain Path: /languages
 * Requires at least: 5.9
 * Requires PHP: 8.0
 * Tested up to: 6.9
 */

defined('ABSPATH') || exit;

// Define plugin version constant for cache busting in enqueued assets.
if (!defined('SMART_SEO_BOOSTER_VERSION')) {
    define('SMART_SEO_BOOSTER_VERSION', '2.4.0');
}

// Autoload classes
require_once plugin_dir_path(__FILE__) . 'includes/class-loader.php';

// Initialize plugin
add_action('plugins_loaded', function () {
    // WordPress.org automatically loads translations since WP 4.6
    // Manual load_plugin_textdomain() call is no longer needed for wp.org hosted plugins

    // Initialize plugin modules
    Smart_SEO_Loader::init();
});

// Flush rewrite rules on activation/deactivation so /sitemap.xml resolves.
register_activation_hook(__FILE__, function () {
    require_once plugin_dir_path(__FILE__) . 'includes/class-sitemap.php';
    Smart_SEO_Sitemap::add_rewrite_rules();
    flush_rewrite_rules();

    // Trigger the one-time setup-wizard redirect (single-site activations only).
    set_transient('smart_seo_activation_redirect', 1, 30);
});

register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});


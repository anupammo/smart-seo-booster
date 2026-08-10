<?php
/**
 * Plugin Name: Smart SEO Booster
 * Plugin URI: https://anupammondal.in/wordpress-plugin/smart-seo-booster
 * Description: Free WordPress SEO plugin built for AI search — llms.txt, AI-crawler control & speakable data, plus schema, XML sitemaps, breadcrumbs & Core Web Vitals.
 * Version: 2.2.1
 * Author: Anupam Mondal
 * Author URI: https://anupammondal.in
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smart-seo-booster
 * Domain Path: /languages
 * Requires at least: 5.9
 * Requires PHP: 7.4
 * Tested up to: 7.0
 */

defined('ABSPATH') || exit;

// Define plugin version constant for cache busting in enqueued assets.
if (!defined('SMART_SEO_BOOSTER_VERSION')) {
    define('SMART_SEO_BOOSTER_VERSION', '2.2.1');
}

// Developer credit / portfolio links.
if (!defined('SMART_SEO_BOOSTER_URL')) {
    define('SMART_SEO_BOOSTER_URL', 'https://anupammondal.in/wordpress-plugin/smart-seo-booster');
}
if (!defined('SMART_SEO_BOOSTER_AUTHOR_URL')) {
    define('SMART_SEO_BOOSTER_AUTHOR_URL', 'https://anupammondal.in');
}
if (!defined('SMART_SEO_BOOSTER_FILE')) {
    define('SMART_SEO_BOOSTER_FILE', __FILE__);
}

// The plugin's own brand icon (logo), used everywhere the UI needs to show
// "this is Smart SEO Booster" — page headings, empty-state illustrations,
// the admin menu, and block icons — in place of generic Dashicons/emoji.
// Deliberately the plain wp.org listing PNG (not a custom SVG variant), so
// every one of these surfaces shows the exact same recognizable mark.
if (!defined('SMART_SEO_BOOSTER_ICON_URL')) {
    define('SMART_SEO_BOOSTER_ICON_URL', plugin_dir_url(__FILE__) . 'assets/icon-256x256.png');
}

// Admin-menu icon. WordPress core's own admin CSS constrains any image
// passed as add_menu_page()'s icon_url to 20x20 (#adminmenu .wp-menu-image
// img), so a plain PNG URL renders correctly sized — it just won't get the
// dark/light admin-scheme auto-recolor a monochrome SVG data URI would.
// That trade-off is intentional here: a consistent full-color logo everywhere
// beats a scheme-adapted abstract mark that looks different from the rest
// of the plugin's branding.
if (!defined('SMART_SEO_BOOSTER_MENU_ICON')) {
    define('SMART_SEO_BOOSTER_MENU_ICON', SMART_SEO_BOOSTER_ICON_URL);
}

// Autoload classes
require_once plugin_dir_path(__FILE__) . 'includes/class-loader.php';

// Initialize plugin
add_action('plugins_loaded', function () {
    // WordPress.org automatically loads translations for hosted plugins once
    // a locale is >90% complete on translate.wordpress.org, so this is
    // intentionally NOT an unconditional load_plugin_textdomain() call (Plugin
    // Check flags that as discouraged/redundant, and it is for locales the
    // automatic loader already serves). This only steps in as a fallback for
    // the locales among our 17 bundled languages that aren't being served
    // that way yet, and backs off the moment they are.
    if (!is_textdomain_loaded('smart-seo-booster')) {
        // phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound -- Intentional fallback, guarded by is_textdomain_loaded() above, only for locales WP.org's automatic loader hasn't picked up yet.
        load_plugin_textdomain('smart-seo-booster', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

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


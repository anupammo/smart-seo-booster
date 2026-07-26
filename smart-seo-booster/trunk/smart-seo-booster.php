<?php
/**
 * Plugin Name: Smart SEO Booster
 * Plugin URI: https://anupammondal.in/wordpress-plugin/smart-seo-booster
 * Description: Complete WordPress SEO plugin — schema markup, XML sitemaps, meta tags, breadcrumbs, Open Graph & AI SEO. 100% free, no upsells.
 * Version: 2.1.1
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
    define('SMART_SEO_BOOSTER_VERSION', '2.1.1');
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

// The plugin's own brand icon (rocket mark), used in place of generic
// Dashicons/emoji on page headings and editor UI (rendered via <img> with
// explicit width/height, so any image URL is safe here).
if (!defined('SMART_SEO_BOOSTER_ICON_URL')) {
    define('SMART_SEO_BOOSTER_ICON_URL', plugin_dir_url(__FILE__) . 'assets/icon.svg');
}

// Admin-menu icon, specifically: WordPress only reliably auto-sizes and
// recolors (to match the admin color scheme) a base64-encoded monochrome SVG
// data URI passed as add_menu_page()'s icon_url — a plain image URL can
// render at its native size, clipped inside the small menu-icon slot, which
// looks like a broken/oversized icon. Same rocket silhouette as elsewhere,
// just flattened to one color since WP recolors this itself.
if (!defined('SMART_SEO_BOOSTER_MENU_ICON')) {
    $smart_seo_menu_icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="black">'
        . '<path d="M112 150 L98 176 L112 171 Z"/>'
        . '<path d="M144 150 L158 176 L144 171 Z"/>'
        . '<ellipse cx="128" cy="186" rx="17" ry="12"/>'
        . '<ellipse cx="128" cy="183" rx="9" ry="8"/>'
        . '<path d="M128 72 L112 106 L112 170 Q112 178 120 178 L136 178 Q144 178 144 170 L144 106 Z"/>'
        . '<circle cx="128" cy="112" r="13" fill-opacity="0.35"/>'
        . '</svg>';
    define('SMART_SEO_BOOSTER_MENU_ICON', 'data:image/svg+xml;base64,' . base64_encode($smart_seo_menu_icon_svg));
    unset($smart_seo_menu_icon_svg);
}

// Autoload classes
require_once plugin_dir_path(__FILE__) . 'includes/class-loader.php';

// Initialize plugin
add_action('plugins_loaded', function () {
    // Load bundled translations as a fallback for locales not yet served
    // by WordPress.org's automatic language packs (translate.wordpress.org).
    load_plugin_textdomain('smart-seo-booster', false, dirname(plugin_basename(__FILE__)) . '/languages');

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


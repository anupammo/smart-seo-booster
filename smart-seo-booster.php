<?php
/**
 * Plugin Name: Smart SEO Booster
 * Description: Modular SEO plugin with schema injection, content audit, and internal link analysis.
 * Version: 1.0.0
 * Author: Anupam Mondal
 * License: GPL2+
 * Text Domain: smart-seo-booster-1
 */

defined('ABSPATH') || exit;

// Define plugin version constant for cache busting in enqueued assets.
if (!defined('SMART_SEO_BOOSTER_VERSION')) {
    define('SMART_SEO_BOOSTER_VERSION', '1.0.0');
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

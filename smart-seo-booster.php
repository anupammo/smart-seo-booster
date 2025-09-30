<?php
/**
 * Plugin Name: Smart SEO Booster
 * Description: Modular SEO plugin with schema injection, content audit, and internal link analysis.
 * Version: 1.0.0
 * Author: Anupam Mondal
 * License: GPL2+
 * Text Domain: smart-seo-booster
 */

defined('ABSPATH') || exit;

// Autoload classes
require_once plugin_dir_path(__FILE__) . 'includes/class-loader.php';

// Initialize plugin
add_action('plugins_loaded', function () {
    Smart_SEO_Loader::init();
});

<?php
/**
 * Plugin Name: Smart SEO Booster
 * Description: Modular SEO plugin with schema injection, content audit, and internal link analysis. Compatible with WordPress 6.4+ and block themes.
 * Version: 2.0.0
 * Author: Anupam Mondal
 * License: GPL2+
 * Text Domain: smart-seo-booster
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.4
 * Requires PHP: 8.0
 * Network: false
 * Update URI: https://github.com/anupammo/smart-seo-booster
 */

defined('ABSPATH') || exit;

// Plugin version and compatibility check
define('SMART_SEO_VERSION', '2.0.0');
define('SMART_SEO_MIN_WP_VERSION', '6.0');
define('SMART_SEO_MIN_PHP_VERSION', '8.0');

// Check WordPress and PHP compatibility
function smart_seo_check_compatibility() {
    global $wp_version;
    
    if (version_compare(PHP_VERSION, SMART_SEO_MIN_PHP_VERSION, '<')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>';
            echo sprintf(
                __('Smart SEO Booster requires PHP version %s or higher. You are running version %s.', 'smart-seo-booster'),
                SMART_SEO_MIN_PHP_VERSION,
                PHP_VERSION
            );
            echo '</p></div>';
        });
        return false;
    }
    
    if (version_compare($wp_version, SMART_SEO_MIN_WP_VERSION, '<')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>';
            echo sprintf(
                __('Smart SEO Booster requires WordPress version %s or higher. You are running version %s.', 'smart-seo-booster'),
                SMART_SEO_MIN_WP_VERSION,
                $GLOBALS['wp_version']
            );
            echo '</p></div>';
        });
        return false;
    }
    
    return true;
}

// Exit early if compatibility check fails
if (!smart_seo_check_compatibility()) {
    return;
}

// Autoload classes
require_once plugin_dir_path(__FILE__) . 'includes/class-loader.php';

// Set default options on activation
register_activation_hook(__FILE__, function() {
    $default_options = [
        'enable_schema' => 1,
        'enable_meta_tags' => 1,
        'enable_og_tags' => 1,
        'enable_content_audit' => 1,
        'enable_link_analysis' => 1,
        'enable_score_display' => 1,
        'default_schema_type' => 'organization',
        'min_word_count' => 300,
        'default_description' => get_bloginfo('description')
    ];
    
    $existing_options = get_option('smart_seo_options', []);
    $merged_options = array_merge($default_options, $existing_options);
    update_option('smart_seo_options', $merged_options);
    
    // Create capabilities for advanced users
    $role = get_role('administrator');
    if ($role) {
        $role->add_cap('manage_smart_seo');
    }
});

// Initialize plugin
add_action('plugins_loaded', function () {
    // Load translation files
    load_plugin_textdomain('smart-seo-booster', false, dirname(plugin_basename(__FILE__)) . '/languages');

    // Initialize plugin modules
    Smart_SEO_Loader::init();
});

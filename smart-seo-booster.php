<?php
/**
 * Plugin Name: Smart SEO Booster
 * Plugin URI: https://github.com/anupammo/smart-seo-booster
 * Description: Comprehensive SEO plugin with advanced content audit, schema markup, internal link analysis, and PageSpeed Insights-style reporting. Compatible with WordPress 5.0+ through 6.8+ and modern block themes.
 * Version: 2.1.0
 * Author: Anupam Mondal
 * Author URI: https://github.com/anupammo
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smart-seo-booster
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * Network: false
 * 
 * @package SmartSEOBooster
 * @author Anupam Mondal
 * @license GPL-2.0+
 * @link https://github.com/anupammo/smart-seo-booster
 * @copyright 2025 Anupam Mondal
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// Plugin version and compatibility constants
define('SMART_SEO_VERSION', '2.1.0');
define('SMART_SEO_MIN_WP_VERSION', '5.0');
define('SMART_SEO_MIN_PHP_VERSION', '7.4');
define('SMART_SEO_PLUGIN_FILE', __FILE__);
define('SMART_SEO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SMART_SEO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SMART_SEO_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Check WordPress and PHP compatibility
function smart_seo_check_compatibility() {
    global $wp_version;
    
    // Check PHP version
    if (version_compare(PHP_VERSION, SMART_SEO_MIN_PHP_VERSION, '<')) {
        add_action('admin_notices', function() {
            $message = sprintf(
                /* translators: 1: required PHP version, 2: current PHP version */
                __('Smart SEO Booster requires PHP version %1$s or higher. You are running version %2$s. Please update PHP to use this plugin.', 'smart-seo-booster'),
                SMART_SEO_MIN_PHP_VERSION,
                PHP_VERSION
            );
            printf('<div class="notice notice-error"><p>%s</p></div>', wp_kses_post($message));
        });
        return false;
    }
    
    // Check WordPress version
    if (version_compare($wp_version, SMART_SEO_MIN_WP_VERSION, '<')) {
        add_action('admin_notices', function() {
            $message = sprintf(
                /* translators: 1: required WordPress version, 2: current WordPress version */
                __('Smart SEO Booster requires WordPress version %1$s or higher. You are running version %2$s. Please update WordPress to use this plugin.', 'smart-seo-booster'),
                SMART_SEO_MIN_WP_VERSION,
                $GLOBALS['wp_version']
            );
            printf('<div class="notice notice-error"><p>%s</p></div>', wp_kses_post($message));
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

/**
 * Plugin activation hook
 * 
 * @since 2.1.0
 */
function smart_seo_activate() {
    // Check compatibility again on activation
    if (!smart_seo_check_compatibility()) {
        wp_die(
            esc_html__('Smart SEO Booster cannot be activated due to compatibility issues. Please check WordPress and PHP versions.', 'smart-seo-booster'),
            esc_html__('Plugin Activation Error', 'smart-seo-booster'),
            ['back_link' => true]
        );
    }

    // Set default options
    $default_options = [
        'enable_schema' => 1,
        'enable_meta_tags' => 1,
        'enable_og_tags' => 1,
        'enable_content_audit' => 1,
        'enable_link_analysis' => 1,
        'enable_score_display' => 1,
        'default_schema_type' => 'organization',
        'min_word_count' => 300,
        'default_description' => get_bloginfo('description'),
        'cache_duration' => 3600, // 1 hour
        'enable_cache' => 1
    ];
    
    $existing_options = get_option('smart_seo_options', []);
    $merged_options = array_merge($default_options, $existing_options);
    update_option('smart_seo_options', $merged_options);
    
    // Store plugin version and activation date
    update_option('smart_seo_version', SMART_SEO_VERSION);
    update_option('smart_seo_installed_date', current_time('mysql'));
    
    // Create capabilities for advanced users
    $role = get_role('administrator');
    if ($role) {
        $role->add_cap('manage_smart_seo');
    }

    // Clear any existing caches
    delete_transient('smart_seo_audit_cache');
    delete_transient('smart_seo_schema_cache');

    // Schedule cleanup event (if needed in future)
    if (!wp_next_scheduled('smart_seo_cleanup')) {
        wp_schedule_event(time(), 'daily', 'smart_seo_cleanup');
    }
}

/**
 * Plugin deactivation hook
 * 
 * @since 2.1.0
 */
function smart_seo_deactivate() {
    // Clear scheduled events
    wp_clear_scheduled_hook('smart_seo_cleanup');
    
    // Clear all transients
    delete_transient('smart_seo_audit_cache');
    delete_transient('smart_seo_schema_cache');
    
    // Note: We don't delete options on deactivation, only on uninstall
}

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'smart_seo_activate');
register_deactivation_hook(__FILE__, 'smart_seo_deactivate');

/**
 * Initialize plugin
 * 
 * @since 2.1.0
 */
function smart_seo_init() {
    // Initialize plugin modules
    if (class_exists('Smart_SEO_Loader')) {
        Smart_SEO_Loader::init();
    }
}

// Initialize plugin after WordPress is fully loaded
add_action('plugins_loaded', 'smart_seo_init');

/**
 * Plugin loaded hook for third-party integrations
 * 
 * @since 2.1.0
 */
do_action('smart_seo_booster_loaded');

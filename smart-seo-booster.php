<?php
/**
 * Plugin Name: Smart SEO Booster
 * Plugin URI: https://anupammondal.in/wordpress-plugin/smart-seo-booster
 * Description: Comprehensive SEO plugin with advanced content audit, schema markup, internal link analysis, and PageSpeed Insights-style reporting. Compatible with WordPress 5.0+ through 6.9+ and modern block themes.
 * Version: 1.1
 * Author: Anupam Mondal
 * Author URI: https://anupammondal.in/
 * Author Profile: https://profiles.wordpress.org/anupamwp/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smart-seo-booster
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * 
 * @package SmartSEOBooster
 * @author Anupam Mondal
 * @license GPL-2.0+
 * @link https://profiles.wordpress.org/anupamwp/
 * @link https://anupammondal.in/wordpress-plugin/smart-seo-booster
 * @copyright 2025 Anupam Mondal
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// Plugin version and compatibility constants
define('ANUPAMWP_SSB_VERSION', '1.1');
define('ANUPAMWP_SSB_MIN_WP_VERSION', '5.0');
define('ANUPAMWP_SSB_MIN_PHP_VERSION', '7.4');
define('ANUPAMWP_SSB_PLUGIN_FILE', __FILE__);
define('ANUPAMWP_SSB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ANUPAMWP_SSB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ANUPAMWP_SSB_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Check WordPress and PHP compatibility
function anupamwp_ssb_check_compatibility() {
    global $wp_version;
    
    // Check PHP version
    if (version_compare(PHP_VERSION, ANUPAMWP_SSB_MIN_PHP_VERSION, '<')) {
        add_action('admin_notices', function() {
            if (!function_exists('get_current_screen')) {
                return;
            }
            $screen = get_current_screen();
            if (!$screen || $screen->id !== 'plugins') {
                return;
            }
            $message = sprintf(
                /* translators: 1: required PHP version, 2: current PHP version */
                __('Smart SEO Booster requires PHP version %1$s or higher. You are running version %2$s. Please update PHP to use this plugin.', 'smart-seo-booster'),
                ANUPAMWP_SSB_MIN_PHP_VERSION,
                PHP_VERSION
            );
            printf('<div class="notice notice-error"><p>%s</p></div>', wp_kses_post($message));
        });
        return false;
    }
    
    // Check WordPress version
    if (version_compare($wp_version, ANUPAMWP_SSB_MIN_WP_VERSION, '<')) {
        add_action('admin_notices', function() {
            if (!function_exists('get_current_screen')) {
                return;
            }
            $screen = get_current_screen();
            if (!$screen || $screen->id !== 'plugins') {
                return;
            }
            $message = sprintf(
                /* translators: 1: required WordPress version, 2: current WordPress version */
                __('Smart SEO Booster requires WordPress version %1$s or higher. You are running version %2$s. Please update WordPress to use this plugin.', 'smart-seo-booster'),
                ANUPAMWP_SSB_MIN_WP_VERSION,
                $GLOBALS['wp_version']
            );
            printf('<div class="notice notice-error"><p>%s</p></div>', wp_kses_post($message));
        });
        return false;
    }
    
    return true;
}

// Exit early if compatibility check fails
if (!anupamwp_ssb_check_compatibility()) {
    return;
}

// Autoload classes
require_once plugin_dir_path(__FILE__) . 'includes/class-loader.php';

/**
 * Plugin activation hook
 * 
 * @since 2.1.0
 */
function anupamwp_ssb_activate() {
    // Check compatibility again on activation
    if (!anupamwp_ssb_check_compatibility()) {
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
    
    $existing_options = get_option('anupamwp_ssb_options', []);
    $legacy_option_key = 'smart' . '_' . 'seo' . '_' . 'options';
    $legacy_options = get_option($legacy_option_key, []);
    if (empty($existing_options) && is_array($legacy_options) && !empty($legacy_options)) {
        $existing_options = $legacy_options;
    }
    $merged_options = array_merge($default_options, $existing_options);
    update_option('anupamwp_ssb_options', $merged_options);
    
    // Store plugin version and activation date
    update_option('anupamwp_ssb_version', ANUPAMWP_SSB_VERSION);
    update_option('anupamwp_ssb_installed_date', current_time('mysql'));
    
    // Create capabilities for advanced users
    $role = get_role('administrator');
    if ($role) {
        $role->add_cap('manage_anupamwp_ssb');
    }

    // Clear any existing caches
    delete_transient('anupamwp_ssb_audit_cache');
    delete_transient('anupamwp_ssb_schema_cache');

    // Schedule cleanup event (if needed in future)
    if (!wp_next_scheduled('anupamwp_ssb_cleanup')) {
        wp_schedule_event(time(), 'daily', 'anupamwp_ssb_cleanup');
    }
}

/**
 * Migrate legacy option keys once for existing installs.
 */
function anupamwp_ssb_maybe_migrate_legacy_options() {
    $already_migrated = get_option('anupamwp_ssb_migrated_legacy_options', false);
    if ($already_migrated) {
        return;
    }

    $current_options = get_option('anupamwp_ssb_options', []);
    $legacy_option_key = 'smart' . '_' . 'seo' . '_' . 'options';
    $legacy_options = get_option($legacy_option_key, []);

    if (empty($current_options) && is_array($legacy_options) && !empty($legacy_options)) {
        update_option('anupamwp_ssb_options', $legacy_options);
    }

    update_option('anupamwp_ssb_migrated_legacy_options', 1);
}

/**
 * Plugin deactivation hook
 * 
 * @since 2.1.0
 */
function anupamwp_ssb_deactivate() {
    // Clear scheduled events
    wp_clear_scheduled_hook('anupamwp_ssb_cleanup');
    
    // Clear all transients
    delete_transient('anupamwp_ssb_audit_cache');
    delete_transient('anupamwp_ssb_schema_cache');
    
    // Note: We don't delete options on deactivation, only on uninstall
}

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'anupamwp_ssb_activate');
register_deactivation_hook(__FILE__, 'anupamwp_ssb_deactivate');

/**
 * Initialize plugin
 * 
 * @since 2.1.0
 */
function anupamwp_ssb_init() {
    anupamwp_ssb_maybe_migrate_legacy_options();

    // Initialize plugin modules
    if (class_exists('AnupamWP_SSB_Loader')) {
        AnupamWP_SSB_Loader::init();
    }
}

// Initialize plugin after WordPress is fully loaded
add_action('plugins_loaded', 'anupamwp_ssb_init');

/**
 * Plugin loaded hook for third-party integrations
 * 
 * @since 2.1.0
 */
do_action('anupamwp_ssb_booster_loaded');

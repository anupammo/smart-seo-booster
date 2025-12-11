<?php
/**
 * Smart SEO Booster Admin UI Class
 * 
 * Handles admin interface, menus, and page rendering
 * 
 * @package SmartSEOBooster
 * @since 2.1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

/**
 * Class Smart_SEO_Admin_UI
 * 
 * Manages WordPress admin interface for Smart SEO Booster
 */
class Smart_SEO_Admin_UI {
    
    /**
     * Initialize admin UI hooks
     * 
     * @since 2.1.0
     */
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('admin_footer', [__CLASS__, 'add_developer_footer']);
    }

    /**
     * Add admin menu pages
     * 
     * @since 2.1.0
     */
    public static function add_menu() {
        // Main menu page
        add_menu_page(
            esc_html__('Smart SEO Booster', 'smart-seo-booster'),
            esc_html__('Smart SEO Booster', 'smart-seo-booster'),
            'manage_options',
            'smart-seo',
            [__CLASS__, 'render_settings_page'],
            'dashicons-chart-line',
            80
        );

        // Settings submenu
        add_submenu_page(
            'smart-seo',
            esc_html__('SEO Settings', 'smart-seo-booster'),
            esc_html__('Settings', 'smart-seo-booster'),
            'manage_options',
            'smart-seo',
            [__CLASS__, 'render_settings_page']
        );

        // Audit report submenu
        add_submenu_page(
            'smart-seo',
            esc_html__('SEO Audit Report', 'smart-seo-booster'),
            esc_html__('Audit Report', 'smart-seo-booster'),
            'manage_options',
            'smart-seo-audit',
            [__CLASS__, 'render_audit_report']
        );

        // Help submenu
        add_submenu_page(
            'smart-seo',
            esc_html__('Help & Documentation', 'smart-seo-booster'),
            esc_html__('Help', 'smart-seo-booster'),
            'manage_options',
            'smart-seo-help',
            [__CLASS__, 'render_help_page']
        );
    }

    /**
     * Register plugin settings
     * 
     * @since 2.1.0
     */
    public static function register_settings() {
        register_setting(
            'smart_seo_settings',
            'smart_seo_options',
            [
                'type' => 'array',
                'sanitize_callback' => [__CLASS__, 'sanitize_settings'],
                'default' => []
            ]
        );
    }

    /**
     * Sanitize settings input
     * 
     * @param array $input Raw settings input
     * @return array Sanitized settings
     * @since 2.1.0
     */
    public static function sanitize_settings($input) {
        if (!is_array($input)) {
            return [];
        }

        $sanitized = [];
        
        // Add specific sanitization for each setting
        foreach ($input as $key => $value) {
            switch ($key) {
                case 'enable_schema':
                case 'enable_audit':
                case 'enable_link_analysis':
                    $sanitized[$key] = (bool) $value;
                    break;
                default:
                    $sanitized[$key] = sanitize_text_field($value);
                    break;
            }
        }

        return $sanitized;
    }

    /**
     * Render settings page with proper security
     * 
     * @since 2.1.0
     */
    public static function render_settings_page() {
        // Verify user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'smart-seo-booster'));
        }

        ?>
        <div class="wrap smart-seo-wrapper smart-seo-settings-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form method="post" action="options.php" class="smart-seo-settings-form">
                <?php
                settings_fields('smart_seo_settings');
                do_settings_sections('smart_seo');
                submit_button(
                    esc_html__('Save SEO Settings', 'smart-seo-booster'),
                    'primary',
                    'submit',
                    true,
                    ['id' => 'smart-seo-submit']
                );
                ?>
            </form>
            
            <!-- Footer with author credit -->
            <div style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-left: 4px solid #2271b1; border-radius: 4px;">
                <p style="margin: 0; color: #666; font-size: 14px;">
                    <strong><?php esc_html_e('Smart SEO Booster', 'smart-seo-booster'); ?></strong> 
                    <?php printf(
                        /* translators: %s: plugin version */
                        esc_html__('version %s - Developed with ❤️ for better WordPress SEO', 'smart-seo-booster'),
                        esc_html(SMART_SEO_VERSION)
                    ); ?>
                </p>
                <p style="margin: 5px 0 0 0; color: #666; font-size: 12px;">
                    <?php esc_html_e('Thank you for using Smart SEO Booster. For support and documentation, visit our website.', 'smart-seo-booster'); ?>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Render audit report page with security checks
     * 
     * @since 2.1.0
     */
    public static function render_audit_report() {
        // Verify user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'smart-seo-booster'));
        }

        // Verify nonce if this is a form submission
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $nonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
            if (!wp_verify_nonce($nonce, 'smart_seo_audit_action')) {
                wp_die(esc_html__('Security check failed. Please try again.', 'smart-seo-booster'));
            }
        }

        $template_file = SMART_SEO_PLUGIN_DIR . 'templates/audit-report.php';
        if (file_exists($template_file)) {
            include $template_file;
        } else {
            echo '<div class="notice notice-error"><p>' . esc_html__('Template file not found.', 'smart-seo-booster') . '</p></div>';
        }
    }

    /**
     * Render help page with security checks
     * 
     * @since 2.1.0
     */
    public static function render_help_page() {
        // Verify user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'smart-seo-booster'));
        }

        $template_file = SMART_SEO_PLUGIN_DIR . 'templates/help-page.php';
        if (file_exists($template_file)) {
            include $template_file;
        } else {
            echo '<div class="notice notice-error"><p>' . esc_html__('Template file not found.', 'smart-seo-booster') . '</p></div>';
        }
    }

    /**
     * Enqueue admin assets with proper versioning
     * 
     * @param string $hook Current admin page hook
     * @since 2.1.0
     */
    public static function enqueue_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'smart-seo') === false) {
            return;
        }

        // Enqueue CSS with version for cache busting
        wp_enqueue_style(
            'smart-seo-admin',
            SMART_SEO_PLUGIN_URL . 'css/admin.css',
            [],
            SMART_SEO_VERSION,
            'all'
        );

        // Add inline CSS for WordPress admin colors compatibility
        $custom_css = "
            .smart-seo-wrapper .ps-card {
                background: #fff;
                border: 1px solid #c3c4c7;
            }
            .smart-seo-wrapper .status-icon.good::before {
                color: #00a32a;
            }
        ";
        wp_add_inline_style('smart-seo-admin', $custom_css);
    }

    /**
     * Add developer credit footer on specific plugin pages only
     * 
     * @since 2.1.0
     */
    public static function add_developer_footer() {
        $screen = get_current_screen();
        
        // Only show on specific plugin pages: settings, audit report, and help
        $allowed_pages = [
            'toplevel_page_smart-seo',      // Settings page
            'smart-seo-booster_page_smart-seo-audit',  // Audit report page
            'smart-seo-booster_page_smart-seo-help'    // Help page
        ];
        
        if (!in_array($screen->id, $allowed_pages)) {
            return;
        }

        ?>
        <div id="smart-seo-developer-footer" style="
            position: fixed; 
            bottom: 0; 
            right: 20px; 
            background: #fff; 
            border: 1px solid #c3c4c7; 
            border-bottom: none;
            border-radius: 6px 6px 0 0; 
            padding: 8px 15px; 
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
            z-index: 1000;
            font-size: 12px;
            color: #666;
        ">
            <?php esc_html_e('Developed by', 'smart-seo-booster'); ?> 
            <a href="https://anupammondal.in/?utm_source=smart-seo-booster&utm_medium=plugin&utm_campaign=footer" 
               target="_blank" 
               style="color: #0073aa; text-decoration: none; font-weight: 500;">
                Anupam Mondal
            </a>
            <span style="margin: 0 5px;">•</span>
            <a href="https://anupammondal.in/contact/?utm_source=smart-seo-booster&utm_medium=plugin&utm_campaign=footer-help" 
               target="_blank" 
               style="color: #d63638; text-decoration: none; font-size: 11px;">
                <?php esc_html_e('Need Help?', 'smart-seo-booster'); ?>
            </a>
            <button onclick="document.getElementById('smart-seo-developer-footer').style.display='none'" 
                    style="background: none; border: none; color: #666; cursor: pointer; margin-left: 10px; font-size: 14px;">
                ×
            </button>
        </div>
        <?php
    }
}

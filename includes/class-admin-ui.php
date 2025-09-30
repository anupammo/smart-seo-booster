<?php
defined('ABSPATH') || exit;

class Smart_SEO_Admin_UI {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
    }

    public static function add_menu() {
        add_menu_page(
            'Smart SEO Booster',
            'Smart SEO',
            'manage_options',
            'smart-seo',
            [__CLASS__, 'render_settings_page'],
            'dashicons-chart-line',
            80
        );

        add_submenu_page(
            'smart-seo',
            'SEO Audit Report',
            'Audit Report',
            'manage_options',
            'smart-seo-audit',
            [__CLASS__, 'render_audit_report']
        );
    }

    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Smart SEO Booster Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('smart_seo_settings');
                do_settings_sections('smart_seo');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public static function render_audit_report() {
        include plugin_dir_path(__FILE__) . '../templates/audit-report.php';
    }

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }
    
    public static function enqueue_assets($hook) {
        if (strpos($hook, 'smart-seo') !== false) {
            wp_enqueue_style('smart-seo-admin', plugin_dir_url(__FILE__) . '../css/admin.css');
        }
    }
}

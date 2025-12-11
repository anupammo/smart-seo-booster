<?php
defined('ABSPATH') || exit;

class Smart_SEO_Admin_UI {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }

    public static function add_menu() {
        add_menu_page(
            __( 'Smart SEO Booster', 'smart-seo-booster' ),
            __( 'Smart SEO', 'smart-seo-booster' ),
            'manage_options',
            'smart-seo',
            [__CLASS__, 'render_settings_page'],
            'dashicons-chart-line',
            80
        );

        add_submenu_page(
            'smart-seo',
            __( 'SEO Audit Report', 'smart-seo-booster' ),
            __( 'Audit Report', 'smart-seo-booster' ),
            'manage_options',
            'smart-seo-audit',
            [__CLASS__, 'render_audit_report']
        );
    }

    public static function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die( esc_html__('You do not have sufficient permissions to access this page.', 'smart-seo-booster') );
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__( 'Smart SEO Booster Settings', 'smart-seo-booster' ); ?></h1>
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
        if (!current_user_can('manage_options')) {
            wp_die( esc_html__('You do not have sufficient permissions to access this page.', 'smart-seo-booster') );
        }
        include plugin_dir_path(__FILE__) . '../templates/audit-report.php';
    }
    
    public static function enqueue_assets($hook) {
        if (strpos($hook, 'smart-seo') !== false) {
            wp_enqueue_style(
                'smart-seo-admin',
                plugin_dir_url(__FILE__) . '../css/admin.css',
                [],
                defined('SMART_SEO_BOOSTER_VERSION') ? SMART_SEO_BOOSTER_VERSION : null
            );
        }
    }
}


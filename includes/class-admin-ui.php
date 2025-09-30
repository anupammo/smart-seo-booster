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
}

<?php
defined('ABSPATH') || exit;

class Smart_SEO_Settings {
    public static function init() {
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    public static function register_settings() {
        register_setting('smart_seo_settings', 'smart_seo_options');
        add_settings_section('smart_seo_main', 'Smart SEO Settings', null, 'smart_seo');
        add_settings_field('enable_schema', 'Enable Schema Markup', [__CLASS__, 'checkbox'], 'smart_seo', 'smart_seo_main', ['name' => 'enable_schema']);
    }

    public static function checkbox($args) {
        $options = get_option('smart_seo_options');
        $checked = isset($options[$args['name']]) ? 'checked' : '';
        echo "<input type='checkbox' name='smart_seo_options[{$args['name']}]' $checked />";
    }
}

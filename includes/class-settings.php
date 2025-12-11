<?php
defined('ABSPATH') || exit;

class Smart_SEO_Settings {
    public static function init() {
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    public static function register_settings() {
        register_setting('smart_seo_settings', 'smart_seo_options', [
            'sanitize_callback' => [__CLASS__, 'sanitize_options']
        ]);
        add_settings_section('smart_seo_main', __( 'Smart SEO Settings', 'smart-seo-booster' ), null, 'smart_seo');
        add_settings_field('enable_schema', __( 'Enable Schema Markup', 'smart-seo-booster' ), [__CLASS__, 'checkbox'], 'smart_seo', 'smart_seo_main', ['name' => 'enable_schema']);
    }

    public static function checkbox($args) {
        $options = get_option('smart_seo_options');
        $is_checked = !empty($options[$args['name']]);
        $field_name = 'smart_seo_options[' . esc_attr($args['name']) . ']';
        echo '<input type="checkbox" name="' . esc_attr($field_name) . '" value="1" ' . checked( $is_checked, true, false ) . ' />';
    }

    public static function sanitize_options($input) {
        $output = [];
        if (!is_array($input)) {
            return $output;
        }
        // Only allow known keys and cast types safely
        $allowed_keys = [
            'enable_schema',
        ];
        foreach ($allowed_keys as $key) {
            if (isset($input[$key])) {
                // checkboxes: store as 1 or 0
                $output[$key] = !empty($input[$key]) ? 1 : 0;
            }
        }
        return $output;
    }
}


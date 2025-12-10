<?php
defined('ABSPATH') || exit;

class Smart_SEO_Schema_Generator {
    public static function init() {
        add_action('wp_footer', [__CLASS__, 'output_schema']);
    }

    public static function output_schema() {
        if (is_admin()) return;

        $options = get_option('smart_seo_options');
        if (empty($options['enable_schema'])) return;

        $schema_type = self::detect_schema_type();
        
        // Whitelist validation for security (prevent path traversal)
        $allowed_types = ['article', 'faq', 'profile-page', 'organization', 'local-business'];
        if (!in_array($schema_type, $allowed_types, true)) {
            return;
        }

        $schema_file = plugin_dir_path(__FILE__) . "../schema/{$schema_type}-schema.php";
        if (!file_exists($schema_file)) return;

        $schema_data = require $schema_file;
        if (!is_array($schema_data)) return;

        echo "<script type='application/ld+json'>" . wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
    }

    private static function detect_schema_type() {
        // Priority: Admin selection (future enhancement), then page context
        if (is_singular('post')) return 'article';
        if (is_page('faq')) return 'faq';
        if (is_page('about')) return 'profile-page';
        if (is_front_page()) return 'organization';
        if (is_page('contact') || is_page('services')) return 'local-business';

        return 'organization'; // Default fallback
    }
}


<?php
defined('ABSPATH') || exit;

class Smart_SEO_Schema_Generator {
    public static function init() {
        add_action('wp_footer', [__CLASS__, 'output_schema']);
    }

    public static function output_schema() {
        $options = get_option('smart_seo_options');
        if (empty($options['enable_schema'])) return;

        $schema = [
            "@context" => "https://schema.org",
            "@type" => "WebSite",
            "name" => get_bloginfo('name'),
            "url" => home_url(),
            "description" => get_bloginfo('description')
        ];

        echo "<script type='application/ld+json'>" . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
    }
}

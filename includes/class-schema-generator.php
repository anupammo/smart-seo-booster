<?php
defined('ABSPATH') || exit;

class Smart_SEO_Schema_Generator {
    public static function init() {
        add_action('wp_footer', [__CLASS__, 'output_schema']);
    }

    public static function output_schema() {
        if (is_admin()) return;

        $options = get_option('smart_seo_options', []);
        if (empty($options['enable_schema'])) return;

        $schema_type = self::detect_schema_type();

        $schema_file = plugin_dir_path(__FILE__) . "../schema/{$schema_type}-schema.php";
        if (!file_exists($schema_file)) return;

        $schema_data = require $schema_file;
        if (!is_array($schema_data)) return;

        echo "<script type='application/ld+json'>" . wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
    }

    private static function detect_schema_type() {
        $options = get_option('smart_seo_options', []);
        $default_type = isset($options['default_schema_type']) ? $options['default_schema_type'] : 'organization';
        
        // Support for custom post types
        $post_type = get_post_type();
        
        // Priority: Page context, then post type, then admin selection
        if (is_singular('post')) return 'article';
        if (is_singular(['page', 'product', 'event', 'portfolio'])) {
            // Check page templates and slugs for more specific schemas
            global $post;
            if ($post) {
                $slug = $post->post_name;
                $template = get_page_template_slug($post->ID);
                
                // Template-based detection
                if (strpos($template, 'faq') !== false || $slug === 'faq') return 'faq';
                if (strpos($template, 'about') !== false || $slug === 'about') return 'profile-page';
                if (strpos($template, 'contact') !== false || $slug === 'contact') return 'local-business';
                if (strpos($template, 'services') !== false || $slug === 'services') return 'local-business';
            }
        }
        if (is_front_page()) return 'organization';
        if (is_page('contact') || is_page('services')) return 'local-business';
        if (is_page('faq')) return 'faq';
        if (is_page('about')) return 'profile-page';
        
        // Custom post type handling
        if ($post_type === 'product') return 'article'; // WooCommerce products as articles
        if ($post_type === 'event') return 'article'; // Events as articles
        if (in_array($post_type, ['portfolio', 'project', 'case-study'])) return 'article';

        return $default_type; // Use admin-selected default
    }
}

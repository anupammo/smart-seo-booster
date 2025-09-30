<?php
defined('ABSPATH') || exit;

class Smart_SEO_Core {
    public static function init() {
        add_action('wp_head', [__CLASS__, 'inject_meta_tags'], 1);
    }

    public static function inject_meta_tags() {
        if (is_admin()) return;

        $title = get_the_title();
        $desc = get_bloginfo('description');

        echo "<title>" . esc_html($title) . "</title>\n";
        echo "<meta name='description' content='" . esc_attr($desc) . "' />\n";
    }
}

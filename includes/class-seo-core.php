<?php
defined('ABSPATH') || exit;

class Smart_SEO_Core {
    public static function init() {
        add_action('wp_head', [__CLASS__, 'inject_meta_tags'], 1);
    }

    public static function inject_meta_tags() {
        if (is_admin()) return;

        // Get current post/page data
        global $post;
        $title = is_singular() && $post ? get_the_title() : get_bloginfo('name');
        $desc = is_singular() && $post ? wp_trim_words(strip_tags($post->post_content), 20) : get_bloginfo('description');
        
        // Sanitize and output meta tags
        $title = esc_html($title);
        $desc = esc_attr($desc);
        
        echo "<meta name='description' content='{$desc}' />\n";
        
        // Add Open Graph tags for better social sharing
        echo "<meta property='og:title' content='{$title}' />\n";
        echo "<meta property='og:description' content='{$desc}' />\n";
        echo "<meta property='og:type' content='" . (is_singular('post') ? 'article' : 'website') . "' />\n";
        echo "<meta property='og:url' content='" . esc_url(get_permalink()) . "' />\n";
        
        // Add site name
        echo "<meta property='og:site_name' content='" . esc_attr(get_bloginfo('name')) . "' />\n";
    }
}

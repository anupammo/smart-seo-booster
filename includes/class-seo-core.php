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
        $desc_raw = is_singular() && $post ? $post->post_content : get_bloginfo('description');
        $desc_clean = wp_strip_all_tags( $desc_raw );
        $desc = is_singular() && $post ? wp_trim_words( $desc_clean, 20 ) : $desc_clean;
        
        // Sanitize and output meta tags
        $title_escaped = esc_attr($title);
        $desc_escaped = esc_attr($desc);
        
        echo '<meta name="description" content="' . $desc_escaped . '" />' . "\n";
        
        // Add Open Graph tags for better social sharing
        echo '<meta property="og:title" content="' . $title_escaped . '" />' . "\n";
        echo '<meta property="og:description" content="' . $desc_escaped . '" />' . "\n";
        echo '<meta property="og:type" content="' . esc_attr( is_singular('post') ? 'article' : 'website' ) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '" />' . "\n";
        
        // Add site name
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
    }
}

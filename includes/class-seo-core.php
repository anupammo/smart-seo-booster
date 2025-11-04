<?php
defined('ABSPATH') || exit;

class Smart_SEO_Core {
    public static function init() {
        // Use wp_head for both classic and block themes
        add_action('wp_head', [__CLASS__, 'inject_meta_tags'], 1);
        
        // Additional hook for block themes that might use different head structure
        add_filter('wp_head', [__CLASS__, 'ensure_meta_tags_for_block_themes'], 999);
        
        // Support for custom post types
        add_filter('document_title_parts', [__CLASS__, 'modify_title_parts'], 10, 1);
    }
    
    public static function ensure_meta_tags_for_block_themes() {
        // Additional check for block themes that might interfere with meta tag injection
        if (wp_is_block_theme()) {
            // Block themes might need additional handling
            $options = get_option('smart_seo_options', []);
            if (!empty($options['enable_meta_tags'])) {
                // Ensure our meta tags are present
                $existing_description = false;
                ob_start();
                wp_head();
                $head_content = ob_get_clean();
                
                if (strpos($head_content, 'name="description"') === false) {
                    // Meta description not found, inject it
                    self::inject_meta_tags();
                }
            }
        }
    }
    
    public static function modify_title_parts($title_parts) {
        $options = get_option('smart_seo_options', []);
        if (empty($options['enable_meta_tags'])) return $title_parts;
        
        // Custom title optimization for different post types
        if (is_singular()) {
            global $post;
            if ($post) {
                $custom_title = get_post_meta($post->ID, '_smart_seo_title', true);
                if (!empty($custom_title)) {
                    $title_parts['title'] = $custom_title;
                }
            }
        }
        
        return $title_parts;
    }

    public static function inject_meta_tags() {
        if (is_admin()) return;

        $options = get_option('smart_seo_options', []);
        
        // Check if meta tags are enabled
        if (empty($options['enable_meta_tags'])) return;

        // Get current post/page data
        global $post;
        $title = is_singular() && $post ? get_the_title() : get_bloginfo('name');
        
        // Get description with fallback to default
        $desc = '';
        if (is_singular() && $post) {
            $desc = $post->post_excerpt ?: wp_trim_words(strip_tags($post->post_content), 20);
        }
        if (empty($desc)) {
            $desc = isset($options['default_description']) ? $options['default_description'] : get_bloginfo('description');
        }
        
        // Sanitize and output meta tags
        $title = esc_html($title);
        $desc = esc_attr($desc);
        
        echo "<meta name='description' content='{$desc}' />\n";
        
        // Add Open Graph tags if enabled
        if (!empty($options['enable_og_tags'])) {
            echo "<meta property='og:title' content='{$title}' />\n";
            echo "<meta property='og:description' content='{$desc}' />\n";
            echo "<meta property='og:type' content='" . (is_singular('post') ? 'article' : 'website') . "' />\n";
            echo "<meta property='og:url' content='" . esc_url(get_permalink()) . "' />\n";
            
            // Add site name
            echo "<meta property='og:site_name' content='" . esc_attr(get_bloginfo('name')) . "' />\n";
        }
    }
}

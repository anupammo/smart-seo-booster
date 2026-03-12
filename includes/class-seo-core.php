<?php
defined('ABSPATH') || exit;

class anupamwp_ssb_Core {
    public static function init() {
        // Use wp_head for both classic and block themes
        add_action('wp_head', [__CLASS__, 'inject_meta_tags'], 1);
        
        // Support for custom post types
        add_filter('document_title_parts', [__CLASS__, 'modify_title_parts'], 10, 1);
    }
    
    public static function modify_title_parts($title_parts) {
        $options = get_option('anupamwp_ssb_options', []);
        if (empty($options['enable_meta_tags'])) return $title_parts;
        
        // Custom title optimization for different post types
        if (is_singular()) {
            global $post;
            if ($post) {
                $custom_title = get_post_meta($post->ID, '_anupamwp_ssb_title', true);
                if (!empty($custom_title)) {
                    $title_parts['title'] = $custom_title;
                }
            }
        }
        
        return $title_parts;
    }

    public static function inject_meta_tags() {
        if (is_admin()) return;

        $options = get_option('anupamwp_ssb_options', []);
        
        // Check if meta tags are enabled
        if (empty($options['enable_meta_tags'])) return;

        // Get current post/page data
        global $post;
        
        // Only output if we don't have custom meta fields (to avoid duplication)
        if (is_singular() && $post) {
            $custom_title = get_post_meta($post->ID, '_anupamwp_ssb_title', true);
            $custom_description = get_post_meta($post->ID, '_anupamwp_ssb_description', true);
            
            // If custom meta fields exist, let the meta fields class handle output
            if ($custom_title || $custom_description) {
                return;
            }
        }
        
        $title = is_singular() && $post ? get_the_title() : get_bloginfo('name');
        
        // Get description with fallback to default
        $desc = '';
        if (is_singular() && $post) {
            $desc = $post->post_excerpt ?: wp_trim_words(wp_strip_all_tags($post->post_content), 20);
        }
        if (empty($desc)) {
            $desc = isset($options['default_description']) ? $options['default_description'] : get_bloginfo('description');
        }
        
        // Sanitize and output meta tags
        $title = esc_attr($title);
        $desc = esc_attr($desc);
        
        echo '<meta name="description" content="' . esc_attr($desc) . '" />' . "\n";
        
        // Add Open Graph tags if enabled
        if (!empty($options['enable_og_tags'])) {
            echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
            echo '<meta property="og:description" content="' . esc_attr($desc) . '" />' . "\n";
            $og_type = is_singular('post') ? 'article' : 'website';
            echo '<meta property="og:type" content="' . esc_attr($og_type) . '" />' . "\n";
            echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '" />' . "\n";
            
            // Add site name
            echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
        }
    }
}

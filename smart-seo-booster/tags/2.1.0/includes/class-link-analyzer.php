<?php
defined('ABSPATH') || exit;

class Smart_SEO_Link_Analyzer {
    public static function init() {
        add_action('admin_notices', [__CLASS__, 'show_link_summary']);
    }

    public static function show_link_summary() {
        if (!is_admin() || !function_exists('get_current_screen')) return;
        
        $screen = get_current_screen();
        if (!$screen) return;
        
        // Check if in block editor (Gutenberg)
        $is_block_editor = method_exists($screen, 'is_block_editor') ? $screen->is_block_editor() : false;
        if (!$is_block_editor && !in_array($screen->base, ['post', 'page'])) return;

        global $post;
        if (!$post) return;

        preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\']/i', $post->post_content, $matches);
        $internal_links = array_filter($matches[1], function ($url) {
            return strpos($url, home_url()) !== false;
        });

        $count = count($internal_links);
        // translators: %d is the number of internal links found
        echo "<div class='notice notice-success'><p><strong>" . esc_html__( 'Internal Links:', 'smart-seo-booster' ) . "</strong> " . sprintf( esc_html__( '%d found in this post.', 'smart-seo-booster' ), absint( $count ) ) . "</p></div>";
    }
}


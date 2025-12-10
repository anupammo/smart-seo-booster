<?php
defined('ABSPATH') || exit;

class Smart_SEO_Link_Analyzer {
    public static function init() {
        add_action('admin_notices', [__CLASS__, 'show_link_summary']);
    }

    public static function show_link_summary() {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!is_admin() || !$screen || !$screen->is_block_editor()) return;

        global $post;
        if (!$post) return;

        preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\']/i', $post->post_content, $matches);
        $internal_links = array_filter($matches[1], function ($url) {
            return strpos($url, home_url()) !== false;
        });

        $count = count($internal_links);
        // translators: %d is the number of internal links found
        echo "<div class='notice notice-success'><p><strong>" . esc_html__( 'Internal Links:', 'smart-seo-booster-1' ) . "</strong> " . sprintf( esc_html__( '%d found in this post.', 'smart-seo-booster-1' ), absint( $count ) ) . "</p></div>";
    }
}

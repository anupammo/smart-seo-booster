<?php
defined('ABSPATH') || exit;

class Smart_SEO_Link_Analyzer {
    public static function init() {
        add_action('admin_notices', [__CLASS__, 'show_link_summary']);
    }

    public static function show_link_summary() {
        if (!is_admin() || !get_current_screen()->is_block_editor()) return;

        global $post;
        if (!$post) return;

        preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\']/i', $post->post_content, $matches);
        $internal_links = array_filter($matches[1], function ($url) {
            return strpos($url, home_url()) !== false;
        });

        $count = count($internal_links);
        echo "<div class='notice notice-success'><p><strong>Internal Links:</strong> $count found in this post.</p></div>";
    }
}

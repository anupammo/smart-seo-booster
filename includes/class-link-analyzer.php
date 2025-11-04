<?php
defined('ABSPATH') || exit;

class Smart_SEO_Link_Analyzer {
    public static function init() {
        add_action('admin_notices', [__CLASS__, 'show_link_summary']);
    }

    public static function show_link_summary() {
        if (!is_admin() || !get_current_screen()->is_block_editor()) return;

        $options = get_option('smart_seo_options', []);
        if (empty($options['enable_link_analysis'])) return;

        global $post;
        if (!$post) return;

        preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\']/i', $post->post_content, $matches);
        $internal_links = array_filter($matches[1], function ($url) {
            return strpos($url, home_url()) !== false || strpos($url, '/') === 0;
        });

        $count = count($internal_links);
        $status_icon = $count >= 3 ? '✅' : ($count >= 1 ? '⚠️' : '❌');
        $status_text = $count >= 3 ? 'Good' : ($count >= 1 ? 'Could be better' : 'Needs improvement');
        
        echo "<div class='notice notice-info'>";
        echo "<p><strong>{$status_icon} Internal Links:</strong> {$count} found in this post. ({$status_text})</p>";
        echo "</div>";
    }
}

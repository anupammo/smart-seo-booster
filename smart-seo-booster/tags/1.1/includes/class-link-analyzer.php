<?php
defined('ABSPATH') || exit;

class anupamwp_ssb_Link_Analyzer {
    public static function init() {
    }

    public static function show_link_summary() {
        if (!is_admin() || !function_exists('get_current_screen')) return;
        
        $screen = get_current_screen();
        if (!$screen || !$screen->is_block_editor()) return;

        $options = get_option('anupamwp_ssb_options', []);
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
        
        echo '<div class="notice notice-info">';
        echo '<p><strong>' . esc_html($status_icon) . ' ' . esc_html__('Internal Links:', 'smart-seo-booster') . '</strong> ' . esc_html($count) . ' ' . esc_html__('found in this post.', 'smart-seo-booster') . ' (' . esc_html($status_text) . ')</p>';
        echo '</div>';
    }
}

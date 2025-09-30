<?php
defined('ABSPATH') || exit;

class Smart_SEO_Content_Auditor {
    public static function init() {
        add_action('admin_notices', [__CLASS__, 'show_audit_summary']);
    }

    public static function show_audit_summary() {
        if (!is_admin() || !get_current_screen()->is_block_editor()) return;

        global $post;
        if (!$post) return;

        $word_count = str_word_count(strip_tags($post->post_content));
        $headings = substr_count($post->post_content, '<h');
        $images = substr_count($post->post_content, '<img');
        $alts = substr_count($post->post_content, 'alt=');

        echo "<div class='notice notice-info'><p><strong>SEO Audit:</strong> Words: $word_count, Headings: $headings, Images: $images, Alt Texts: $alts</p></div>";
    }
}

<?php
defined('ABSPATH') || exit;

class Smart_SEO_Content_Auditor {
    public static function init() {
        add_action('admin_notices', [__CLASS__, 'show_audit_summary']);
    }

    public static function show_audit_summary() {
        if (!is_admin() || !function_exists('get_current_screen')) return;
        
        $screen = get_current_screen();
        if (!$screen || !$screen->is_block_editor()) return;

        global $post;
        if (!$post) return;

        $content = $post->post_content;
        $word_count = str_word_count(strip_tags($content));
        $headings = substr_count($content, '<h');
        $images = substr_count($content, '<img');
        $alts = substr_count($content, 'alt=');
        
        // Calculate SEO score
        $score = 0;
        $recommendations = [];
        
        // Word count scoring
        if ($word_count >= 300) {
            $score += 25;
        } else {
            $recommendations[] = "Add more content (minimum 300 words recommended)";
        }
        
        // Heading scoring
        if ($headings >= 2) {
            $score += 25;
        } else {
            $recommendations[] = "Add more headings for better structure";
        }
        
        // Image alt text scoring
        if ($images > 0 && $images === $alts) {
            $score += 25;
        } elseif ($images > 0) {
            $recommendations[] = "Add alt text to all images";
        }
        
        // Basic content length bonus
        if ($word_count >= 500) {
            $score += 25;
        }
        
        $score_class = $score >= 75 ? 'notice-success' : ($score >= 50 ? 'notice-warning' : 'notice-error');
        $score_emoji = $score >= 75 ? '🎉' : ($score >= 50 ? '⚠️' : '❌');
        
        echo "<div class='notice {$score_class}'>";
        echo "<p><strong>{$score_emoji} SEO Score: {$score}/100</strong></p>";
        echo "<p><strong>Stats:</strong> Words: {$word_count} | Headings: {$headings} | Images: {$images} | Alt Texts: {$alts}</p>";
        
        if (!empty($recommendations)) {
            echo "<p><strong>Recommendations:</strong> " . implode(' • ', $recommendations) . "</p>";
        }
        
        echo "</div>";
    }
}

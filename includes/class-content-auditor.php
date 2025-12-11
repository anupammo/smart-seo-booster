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
        $word_count = str_word_count( wp_strip_all_tags( $content ) );
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
            $recommendations[] = __( 'Add more content (minimum 300 words recommended)', 'smart-seo-booster' );
        }
        
        // Heading scoring
        if ($headings >= 2) {
            $score += 25;
        } else {
            $recommendations[] = __( 'Add more headings for better structure', 'smart-seo-booster' );
        }
        
        // Image alt text scoring
        if ($images > 0 && $images === $alts) {
            $score += 25;
        } elseif ($images > 0) {
            $recommendations[] = __( 'Add alt text to all images', 'smart-seo-booster' );
        }
        
        // Basic content length bonus
        if ($word_count >= 500) {
            $score += 25;
        }
        
        $score_class = $score >= 75 ? 'notice-success' : ($score >= 50 ? 'notice-warning' : 'notice-error');
        $score_emoji = $score >= 75 ? 'ðŸŽ‰' : ($score >= 50 ? 'âš ï¸' : 'âŒ');
        
        // Restrict the CSS class to an allowed whitelist and escape output.
        $allowed_notice_classes = [ 'notice-success', 'notice-warning', 'notice-error' ];
        if ( ! in_array( $score_class, $allowed_notice_classes, true ) ) {
            $score_class = 'notice-warning';
        }
        echo '<div class="notice ' . esc_attr( $score_class ) . '">';
        // translators: 1: emoji icon, 2: current score, 3: maximum score
        echo '<p><strong>' . sprintf( esc_html__( '%1$s SEO Score: %2$d/%3$d', 'smart-seo-booster' ), esc_html( $score_emoji ), absint( $score ), 100 ) . '</strong></p>';
        // translators: 1: word count, 2: heading count, 3: image count, 4: alt text count
        echo '<p><strong>' . esc_html__( 'Stats:', 'smart-seo-booster' ) . '</strong> ' . sprintf( esc_html__( 'Words: %1$d | Headings: %2$d | Images: %3$d | Alt Texts: %4$d', 'smart-seo-booster' ), absint( $word_count ), absint( $headings ), absint( $images ), absint( $alts ) ) . '</p>';
        
        if (!empty($recommendations)) {
            echo "<p><strong>" . esc_html__( 'Recommendations:', 'smart-seo-booster' ) . "</strong> " . esc_html( implode( ' â€¢ ', $recommendations ) ) . "</p>";
        }
        
        echo "</div>";
    }
}


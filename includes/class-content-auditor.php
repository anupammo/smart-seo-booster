<?php
defined('ABSPATH') || exit;

class Smart_SEO_Content_Auditor {
    public static function init() {
        add_action('admin_notices', [__CLASS__, 'show_audit_summary']);
        add_action('enqueue_block_editor_assets', [__CLASS__, 'enqueue_block_editor_assets']);
        add_action('rest_api_init', [__CLASS__, 'register_rest_endpoints']);
    }
    
    public static function enqueue_block_editor_assets() {
        if (!current_user_can('edit_posts')) return;
        
        $options = get_option('smart_seo_options', []);
        if (empty($options['enable_content_audit'])) return;
        
        // Register and enqueue the block editor script
        wp_enqueue_script(
            'smart-seo-block-editor',
            plugin_dir_url(__FILE__) . '../js/block-editor.js',
            ['wp-blocks', 'wp-element', 'wp-data', 'wp-plugins', 'wp-edit-post', 'wp-i18n'],
            SMART_SEO_VERSION,
            true
        );
        
        wp_localize_script('smart-seo-block-editor', 'smartSeoData', [
            'restUrl' => rest_url('smart-seo/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'minWordCount' => isset($options['min_word_count']) ? intval($options['min_word_count']) : 300
        ]);
    }
    
    public static function register_rest_endpoints() {
        register_rest_route('smart-seo/v1', '/score/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'rest_get_seo_score'],
            'permission_callback' => function($request) {
                return current_user_can('edit_post', $request['id']);
            },
            'args' => [
                'id' => [
                    'validate_callback' => function($param) {
                        return is_numeric($param);
                    }
                ]
            ]
        ]);
    }
    
    public static function rest_get_seo_score($request) {
        $post_id = $request['id'];
        $post = get_post($post_id);
        
        if (!$post) {
            return new WP_Error('post_not_found', 'Post not found', ['status' => 404]);
        }
        
        $score_data = Smart_SEO_Score_Display::calculate_detailed_seo_score($post_id);
        
        return rest_ensure_response($score_data);
    }

    public static function show_audit_summary() {
        if (!is_admin() || !function_exists('get_current_screen')) return;
        
        $options = get_option('smart_seo_options', []);
        if (empty($options['enable_content_audit'])) return;
        
        $screen = get_current_screen();
        if (!$screen || !$screen->is_block_editor()) return;

        global $post;
        if (!$post) return;

        $content = $post->post_content;
        $word_count = str_word_count(wp_strip_all_tags($content));
        $headings = substr_count($content, '<h');
        $images = substr_count($content, '<img');
        $alts = substr_count($content, 'alt=');
        
        // Get minimum word count from settings
        $min_words = isset($options['min_word_count']) ? intval($options['min_word_count']) : 300;
        
        // Calculate SEO score
        $score = 0;
        $recommendations = [];
        
        // Word count scoring
        if ($word_count >= $min_words) {
            $score += 25;
        } else {
            $recommendations[] = "Add more content (minimum {$min_words} words recommended)";
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
        if ($word_count >= ($min_words * 1.5)) {
            $score += 25;
        }
        
        $score_class = $score >= 75 ? 'notice-success' : ($score >= 50 ? 'notice-warning' : 'notice-error');
        $score_emoji = $score >= 75 ? '🎉' : ($score >= 50 ? '⚠️' : '❌');
        
        $output = '<div class="notice ' . esc_attr($score_class) . '">';
        $output .= '<p><strong>' . esc_html($score_emoji) . ' ' . esc_html__('SEO Score:', 'smart-seo-booster') . ' ' . esc_html($score) . '/100</strong></p>';
        $output .= '<p><strong>' . esc_html__('Stats:', 'smart-seo-booster') . '</strong> ' . 
             esc_html__('Words:', 'smart-seo-booster') . ' ' . esc_html($word_count) . ' | ' .
             esc_html__('Headings:', 'smart-seo-booster') . ' ' . esc_html($headings) . ' | ' .
             esc_html__('Images:', 'smart-seo-booster') . ' ' . esc_html($images) . ' | ' .
             esc_html__('Alt Texts:', 'smart-seo-booster') . ' ' . esc_html($alts) . '</p>';
        
        if (!empty($recommendations)) {
            $escaped_recommendations = array_map('esc_html', $recommendations);
            $output .= '<p><strong>' . esc_html__('Recommendations:', 'smart-seo-booster') . '</strong> ' . implode(' • ', $escaped_recommendations) . '</p>';
        }
        
        $output .= '</div>';
        
        echo wp_kses_post($output);
    }
}

<?php
defined('ABSPATH') || exit;

class Smart_SEO_Score_Display {
    
    public static function init() {
        // Add admin bar SEO score
        add_action('admin_bar_menu', [__CLASS__, 'add_admin_bar_seo_score'], 100);
        
        // Add dashboard widget
        add_action('wp_dashboard_setup', [__CLASS__, 'add_dashboard_widget']);
        
        // Add SEO score to post/page edit screens
        add_action('add_meta_boxes', [__CLASS__, 'add_seo_score_metabox']);
        
        // Add SEO score column to posts/pages list
        add_filter('manage_posts_columns', [__CLASS__, 'add_seo_score_column']);
        add_filter('manage_pages_columns', [__CLASS__, 'add_seo_score_column']);
        add_action('manage_posts_custom_column', [__CLASS__, 'display_seo_score_column'], 10, 2);
        add_action('manage_pages_custom_column', [__CLASS__, 'display_seo_score_column'], 10, 2);
        
        // Add AJAX handler for real-time score updates
        add_action('wp_ajax_get_seo_score', [__CLASS__, 'ajax_get_seo_score']);
        
        // Enqueue scripts for real-time updates
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_score_scripts']);
    }
    
    public static function calculate_seo_score($post_id) {
        $post = get_post($post_id);
        if (!$post) return 0;
        
        $options = get_option('smart_seo_options', []);
        $min_words = isset($options['min_word_count']) ? intval($options['min_word_count']) : 300;
        
        $content = $post->post_content;
        $word_count = str_word_count(strip_tags($content));
        $headings = substr_count($content, '<h');
        $images = substr_count($content, '<img');
        $alts = substr_count($content, 'alt=');
        
        // Internal links calculation
        preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\']/i', $content, $matches);
        $internal_links = 0;
        if (!empty($matches[1])) {
            foreach ($matches[1] as $url) {
                if (strpos($url, home_url()) !== false || strpos($url, '/') === 0) {
                    $internal_links++;
                }
            }
        }
        
        $score = 0;
        $max_score = 100;
        
        // Word count (30 points)
        if ($word_count >= $min_words) {
            $score += 30;
        } elseif ($word_count >= ($min_words * 0.7)) {
            $score += 20;
        } elseif ($word_count >= ($min_words * 0.5)) {
            $score += 10;
        }
        
        // Headings (25 points)
        if ($headings >= 3) {
            $score += 25;
        } elseif ($headings >= 2) {
            $score += 15;
        } elseif ($headings >= 1) {
            $score += 10;
        }
        
        // Image alt text (20 points)
        if ($images > 0) {
            $alt_ratio = $alts / $images;
            if ($alt_ratio >= 1.0) {
                $score += 20;
            } elseif ($alt_ratio >= 0.8) {
                $score += 15;
            } elseif ($alt_ratio >= 0.5) {
                $score += 10;
            }
        } else {
            $score += 10; // No images is okay
        }
        
        // Internal links (15 points)
        if ($internal_links >= 3) {
            $score += 15;
        } elseif ($internal_links >= 2) {
            $score += 10;
        } elseif ($internal_links >= 1) {
            $score += 5;
        }
        
        // Meta description check (10 points)
        $excerpt = $post->post_excerpt;
        if (!empty($excerpt) && strlen($excerpt) >= 120 && strlen($excerpt) <= 160) {
            $score += 10;
        } elseif (!empty($excerpt)) {
            $score += 5;
        }
        
        return min($score, $max_score);
    }
    
    public static function calculate_detailed_seo_score($post_id) {
        $post = get_post($post_id);
        if (!$post) return null;
        
        $options = get_option('smart_seo_options', []);
        $min_words = isset($options['min_word_count']) ? intval($options['min_word_count']) : 300;
        
        $content = $post->post_content;
        $word_count = str_word_count(strip_tags($content));
        $headings = substr_count($content, '<h');
        $images = substr_count($content, '<img');
        $alts = substr_count($content, 'alt=');
        
        // Internal links calculation
        preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\']/i', $content, $matches);
        $internal_links = 0;
        if (!empty($matches[1])) {
            foreach ($matches[1] as $url) {
                if (strpos($url, home_url()) !== false || strpos($url, '/') === 0) {
                    $internal_links++;
                }
            }
        }
        
        $score = 0;
        $breakdown = [];
        
        // Word count scoring
        if ($word_count >= $min_words) {
            $score += 30;
            $breakdown['word_count'] = ['score' => 30, 'max' => 30, 'status' => 'good'];
        } elseif ($word_count >= ($min_words * 0.7)) {
            $score += 20;
            $breakdown['word_count'] = ['score' => 20, 'max' => 30, 'status' => 'okay'];
        } else {
            $score += 10;
            $breakdown['word_count'] = ['score' => 10, 'max' => 30, 'status' => 'poor'];
        }
        
        // Headings scoring
        if ($headings >= 3) {
            $score += 25;
            $breakdown['headings'] = ['score' => 25, 'max' => 25, 'status' => 'good'];
        } elseif ($headings >= 2) {
            $score += 15;
            $breakdown['headings'] = ['score' => 15, 'max' => 25, 'status' => 'okay'];
        } else {
            $score += 5;
            $breakdown['headings'] = ['score' => 5, 'max' => 25, 'status' => 'poor'];
        }
        
        // Image alt text scoring
        if ($images > 0) {
            $alt_ratio = $alts / $images;
            if ($alt_ratio >= 1.0) {
                $score += 20;
                $breakdown['images'] = ['score' => 20, 'max' => 20, 'status' => 'good'];
            } elseif ($alt_ratio >= 0.8) {
                $score += 15;
                $breakdown['images'] = ['score' => 15, 'max' => 20, 'status' => 'okay'];
            } else {
                $score += 10;
                $breakdown['images'] = ['score' => 10, 'max' => 20, 'status' => 'poor'];
            }
        } else {
            $score += 10;
            $breakdown['images'] = ['score' => 10, 'max' => 20, 'status' => 'good'];
        }
        
        // Internal links scoring
        if ($internal_links >= 3) {
            $score += 15;
            $breakdown['internal_links'] = ['score' => 15, 'max' => 15, 'status' => 'good'];
        } elseif ($internal_links >= 2) {
            $score += 10;
            $breakdown['internal_links'] = ['score' => 10, 'max' => 15, 'status' => 'okay'];
        } else {
            $score += 5;
            $breakdown['internal_links'] = ['score' => 5, 'max' => 15, 'status' => 'poor'];
        }
        
        // Meta description scoring
        $excerpt = $post->post_excerpt;
        if (!empty($excerpt) && strlen($excerpt) >= 120 && strlen($excerpt) <= 160) {
            $score += 10;
            $breakdown['meta_description'] = ['score' => 10, 'max' => 10, 'status' => 'good'];
        } elseif (!empty($excerpt)) {
            $score += 5;
            $breakdown['meta_description'] = ['score' => 5, 'max' => 10, 'status' => 'okay'];
        } else {
            $breakdown['meta_description'] = ['score' => 0, 'max' => 10, 'status' => 'poor'];
        }
        
        return [
            'score' => min($score, 100),
            'word_count' => $word_count,
            'headings' => $headings,
            'images' => $images,
            'alts' => $alts,
            'internal_links' => $internal_links,
            'breakdown' => $breakdown,
            'status' => self::get_score_status(min($score, 100)),
            'color' => self::get_score_color(min($score, 100))
        ];
    }
    
    public static function get_score_color($score) {
        if ($score >= 80) return '#10b981'; // Green
        if ($score >= 60) return '#f59e0b'; // Yellow
        if ($score >= 40) return '#f97316'; // Orange
        return '#ef4444'; // Red
    }
    
    public static function get_score_status($score) {
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Good';
        if ($score >= 40) return 'Needs Work';
        return 'Poor';
    }
    
    public static function add_admin_bar_seo_score($wp_admin_bar) {
        if (!current_user_can('edit_posts')) return;
        
        global $post;
        if (!$post || !in_array(get_current_screen()->base, ['post', 'page'])) return;
        
        $score = self::calculate_seo_score($post->ID);
        $color = self::get_score_color($score);
        $status = self::get_score_status($score);
        
        $wp_admin_bar->add_node([
            'id' => 'smart-seo-score',
            'title' => sprintf(
                '<span style="color: %s;">📊 SEO: %d/100</span>',
                $color,
                $score
            ),
            'href' => admin_url('admin.php?page=smart-seo-audit'),
            'meta' => [
                'title' => "SEO Score: {$score}/100 ({$status})"
            ]
        ]);
    }
    
    public static function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'smart_seo_dashboard',
            '📈 Smart SEO Overview',
            [__CLASS__, 'dashboard_widget_content']
        );
    }
    
    public static function dashboard_widget_content() {
        $recent_posts = get_posts([
            'numberposts' => 5,
            'post_status' => 'publish',
            'post_type' => ['post', 'page']
        ]);
        
        $total_score = 0;
        $post_count = 0;
        
        echo '<div class="smart-seo-dashboard-widget">';
        echo '<h4>Recent Content SEO Scores</h4>';
        
        if (!empty($recent_posts)) {
            echo '<table class="widefat" style="margin-top: 10px;">';
            echo '<thead><tr><th>Content</th><th>Score</th><th>Status</th></tr></thead>';
            echo '<tbody>';
            
            foreach ($recent_posts as $post) {
                $score = self::calculate_seo_score($post->ID);
                $color = self::get_score_color($score);
                $status = self::get_score_status($score);
                $total_score += $score;
                $post_count++;
                
                echo '<tr>';
                echo '<td><a href="' . get_edit_post_link($post->ID) . '">' . esc_html($post->post_title) . '</a></td>';
                echo '<td><span style="color: ' . $color . '; font-weight: bold;">' . $score . '/100</span></td>';
                echo '<td><span style="color: ' . $color . ';">' . $status . '</span></td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
            
            $avg_score = $post_count > 0 ? round($total_score / $post_count) : 0;
            $avg_color = self::get_score_color($avg_score);
            
            echo '<div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">';
            echo '<strong>Average Score: <span style="color: ' . $avg_color . ';">' . $avg_score . '/100</span></strong>';
            echo '</div>';
        } else {
            echo '<p>No content found. Start creating posts and pages to see SEO scores!</p>';
        }
        
        echo '<div style="margin-top: 15px; text-align: center;">';
        echo '<a href="' . admin_url('admin.php?page=smart-seo-audit') . '" class="button button-primary">View Full Audit Report</a>';
        echo '</div>';
        echo '</div>';
    }
    
    public static function add_seo_score_metabox() {
        $post_types = ['post', 'page'];
        foreach ($post_types as $post_type) {
            add_meta_box(
                'smart_seo_score',
                '📊 SEO Score',
                [__CLASS__, 'seo_score_metabox_content'],
                $post_type,
                'side',
                'high'
            );
        }
    }
    
    public static function seo_score_metabox_content($post) {
        $score = self::calculate_seo_score($post->ID);
        $color = self::get_score_color($score);
        $status = self::get_score_status($score);
        
        echo '<div class="smart-seo-score-metabox" style="text-align: center;">';
        echo '<div class="seo-score-circle" style="margin: 10px auto; width: 80px; height: 80px; border-radius: 50%; background: conic-gradient(' . $color . ' ' . ($score * 3.6) . 'deg, #e5e7eb 0deg); display: flex; align-items: center; justify-content: center;">';
        echo '<div style="width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-direction: column;">';
        echo '<strong style="font-size: 18px; color: ' . $color . ';">' . $score . '</strong>';
        echo '<small style="color: #666;">/ 100</small>';
        echo '</div>';
        echo '</div>';
        echo '<p><strong style="color: ' . $color . ';">' . $status . '</strong></p>';
        
        // Show recommendations
        echo '<div style="text-align: left; margin-top: 15px;">';
        echo '<h5>Quick Improvements:</h5>';
        echo '<ul style="font-size: 12px; margin: 0; padding-left: 20px;">';
        
        $content = $post->post_content;
        $word_count = str_word_count(strip_tags($content));
        $headings = substr_count($content, '<h');
        $images = substr_count($content, '<img');
        $alts = substr_count($content, 'alt=');
        
        $options = get_option('smart_seo_options', []);
        $min_words = isset($options['min_word_count']) ? intval($options['min_word_count']) : 300;
        
        if ($word_count < $min_words) {
            echo '<li>Add more content (' . ($min_words - $word_count) . ' words needed)</li>';
        }
        if ($headings < 2) {
            echo '<li>Add more headings (H2, H3 tags)</li>';
        }
        if ($images > 0 && $alts < $images) {
            echo '<li>Add alt text to ' . ($images - $alts) . ' images</li>';
        }
        if (empty($post->post_excerpt)) {
            echo '<li>Add a meta description (excerpt)</li>';
        }
        
        echo '</ul>';
        echo '</div>';
        
        echo '<div style="margin-top: 15px;">';
        echo '<button type="button" class="button button-small" onclick="smartSeoRefreshScore(' . $post->ID . ')">Refresh Score</button>';
        echo '</div>';
        echo '</div>';
    }
    
    public static function add_seo_score_column($columns) {
        $columns['seo_score'] = '📊 SEO Score';
        return $columns;
    }
    
    public static function display_seo_score_column($column, $post_id) {
        if ($column === 'seo_score') {
            $score = self::calculate_seo_score($post_id);
            $color = self::get_score_color($score);
            $status = self::get_score_status($score);
            
            echo '<div>';
            echo '<strong style="color: ' . $color . '; font-size: 14px;">' . $score . '/100</strong><br>';
            echo '<small style="color: ' . $color . ';">' . $status . '</small>';
            echo '</div>';
        }
    }
    
    public static function ajax_get_seo_score() {
        check_ajax_referer('smart_seo_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        if (!$post_id || !current_user_can('edit_post', $post_id)) {
            wp_die('Access denied');
        }
        
        $score = self::calculate_seo_score($post_id);
        $color = self::get_score_color($score);
        $status = self::get_score_status($score);
        
        wp_send_json_success([
            'score' => $score,
            'color' => $color,
            'status' => $status
        ]);
    }
    
    public static function enqueue_score_scripts($hook) {
        if (in_array($hook, ['post.php', 'post-new.php', 'edit.php'])) {
            wp_enqueue_script('jquery');
            
            $script = "
            function smartSeoRefreshScore(postId) {
                jQuery.post(ajaxurl, {
                    action: 'get_seo_score',
                    post_id: postId,
                    nonce: '" . wp_create_nonce('smart_seo_nonce') . "'
                }, function(response) {
                    if (response.success) {
                        location.reload(); // Simple reload for now
                    }
                });
            }
            
            // Auto-refresh score when content changes (for block editor)
            if (typeof wp !== 'undefined' && wp.data) {
                let timeout;
                wp.data.subscribe(() => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        const postId = wp.data.select('core/editor').getCurrentPostId();
                        if (postId && jQuery('.smart-seo-score-metabox').length) {
                            // Could add real-time updates here
                        }
                    }, 1000);
                });
            }
            ";
            
            wp_add_inline_script('jquery', $script);
        }
    }
}
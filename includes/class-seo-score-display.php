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
        
        // Add AJAX handlers for SEO analysis
        add_action('wp_ajax_get_seo_score', [__CLASS__, 'ajax_get_seo_score']);
        add_action('wp_ajax_get_full_seo_report', [__CLASS__, 'ajax_get_full_seo_report']);
        
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
        // Only show on admin or for users who can edit posts
        if (!current_user_can('edit_posts')) return;
        
        global $post;
        
        // Check if we're in admin and have a valid screen
        if (is_admin() && function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if (!$post || !$screen || !in_array($screen->base, ['post', 'page'])) {
                return;
            }
        } elseif (!is_admin()) {
            // On frontend, only show for singular posts/pages
            if (!is_singular() || !$post) {
                return;
            }
        } else {
            // No valid context
            return;
        }
        
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
        
        // Get detailed analysis
        $analysis = self::get_detailed_seo_analysis($post);
        
        ?>
        <div class="smart-seo-score-metabox">
            <style>
                .seo-section { margin-bottom: 15px; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; }
                .seo-section:last-child { border-bottom: none; }
                .seo-check { display: flex; justify-content: space-between; align-items: center; margin: 5px 0; }
                .seo-check-status { font-weight: bold; }
                .seo-good { color: #059669; }
                .seo-warning { color: #d97706; }
                .seo-error { color: #dc2626; }
                .seo-score-big { font-size: 24px; font-weight: bold; text-align: center; }
                .seo-recommendations { background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px; }
            </style>
            
            <!-- Overall Score -->
            <div class="seo-section" style="text-align: center;">
                <div class="seo-score-circle" style="margin: 10px auto; width: 80px; height: 80px; border-radius: 50%; background: conic-gradient(<?php echo $color; ?> <?php echo ($score * 3.6); ?>deg, #e5e7eb 0deg); display: flex; align-items: center; justify-content: center;">
                    <div style="width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                        <strong style="font-size: 18px; color: <?php echo $color; ?>;"><?php echo $score; ?></strong>
                        <small style="color: #666;">/ 100</small>
                    </div>
                </div>
                <p><strong style="color: <?php echo $color; ?>;"><?php echo $status; ?></strong></p>
            </div>

            <!-- Content Analysis -->
            <div class="seo-section">
                <h4 style="margin: 0 0 10px 0;">📝 Content Analysis</h4>
                <div class="seo-check">
                    <span>Word Count</span>
                    <span class="seo-check-status <?php echo $analysis['word_count']['status']; ?>">
                        <?php echo $analysis['word_count']['value']; ?> words <?php echo $analysis['word_count']['icon']; ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>Paragraphs</span>
                    <span class="seo-check-status <?php echo $analysis['paragraphs']['status']; ?>">
                        <?php echo $analysis['paragraphs']['value']; ?> <?php echo $analysis['paragraphs']['icon']; ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>Headings</span>
                    <span class="seo-check-status <?php echo $analysis['headings']['status']; ?>">
                        <?php echo $analysis['headings']['value']; ?> <?php echo $analysis['headings']['icon']; ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>Reading Level</span>
                    <span class="seo-check-status <?php echo $analysis['readability']['status']; ?>">
                        <?php echo $analysis['readability']['value']; ?> <?php echo $analysis['readability']['icon']; ?>
                    </span>
                </div>
            </div>

            <!-- SEO Elements -->
            <div class="seo-section">
                <h4 style="margin: 0 0 10px 0;">🎯 SEO Elements</h4>
                <div class="seo-check">
                    <span>Title Length</span>
                    <span class="seo-check-status <?php echo $analysis['title']['status']; ?>">
                        <?php echo $analysis['title']['value']; ?> chars <?php echo $analysis['title']['icon']; ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>Meta Description</span>
                    <span class="seo-check-status <?php echo $analysis['description']['status']; ?>">
                        <?php echo $analysis['description']['value']; ?> <?php echo $analysis['description']['icon']; ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>URL Structure</span>
                    <span class="seo-check-status <?php echo $analysis['url']['status']; ?>">
                        <?php echo $analysis['url']['value']; ?> <?php echo $analysis['url']['icon']; ?>
                    </span>
                </div>
            </div>

            <!-- Media & Links -->
            <div class="seo-section">
                <h4 style="margin: 0 0 10px 0;">🖼️ Media & Links</h4>
                <div class="seo-check">
                    <span>Images</span>
                    <span class="seo-check-status <?php echo $analysis['images']['status']; ?>">
                        <?php echo $analysis['images']['value']; ?> <?php echo $analysis['images']['icon']; ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>Alt Text</span>
                    <span class="seo-check-status <?php echo $analysis['alt_text']['status']; ?>">
                        <?php echo $analysis['alt_text']['value']; ?> <?php echo $analysis['alt_text']['icon']; ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>Internal Links</span>
                    <span class="seo-check-status <?php echo $analysis['internal_links']['status']; ?>">
                        <?php echo $analysis['internal_links']['value']; ?> <?php echo $analysis['internal_links']['icon']; ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>External Links</span>
                    <span class="seo-check-status <?php echo $analysis['external_links']['status']; ?>">
                        <?php echo $analysis['external_links']['value']; ?> <?php echo $analysis['external_links']['icon']; ?>
                    </span>
                </div>
            </div>

            <!-- Recommendations -->
            <?php if (!empty($analysis['recommendations'])): ?>
            <div class="seo-section">
                <h4 style="margin: 0 0 10px 0;">💡 Recommendations</h4>
                <div class="seo-recommendations">
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach ($analysis['recommendations'] as $recommendation): ?>
                            <li><?php echo $recommendation; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <!-- Actions -->
            <div style="text-align: center; margin-top: 15px;">
                <button type="button" class="button button-primary button-small" onclick="smartSeoRefreshScore(<?php echo $post->ID; ?>)">
                    🔄 Refresh Analysis
                </button>
                <button type="button" class="button button-small" onclick="smartSeoShowFullReport(<?php echo $post->ID; ?>)" style="margin-left: 5px;">
                    📊 Full Report
                </button>
            </div>
        </div>
        <?php
    }

    /**
     * Get detailed SEO analysis for a post
     * 
     * @param WP_Post $post The post object
     * @return array Detailed analysis data
     */
    public static function get_detailed_seo_analysis($post) {
        $content = $post->post_content;
        $title = $post->post_title;
        $excerpt = $post->post_excerpt;
        $slug = $post->post_name;
        
        // Content Analysis
        $word_count = str_word_count(strip_tags($content));
        $paragraph_count = substr_count($content, '</p>');
        $heading_count = substr_count($content, '<h');
        
        // SEO Elements
        $title_length = strlen($title);
        $description_length = strlen($excerpt);
        $slug_length = strlen($slug);
        
        // Media & Links
        preg_match_all('/<img[^>]*>/i', $content, $images);
        $image_count = count($images[0]);
        
        preg_match_all('/<img[^>]*alt=["\'][^"\']*["\'][^>]*>/i', $content, $alts);
        $alt_count = count($alts[0]);
        
        preg_match_all('/<a[^>]*href=["\']([^"\']*)["\'][^>]*>/i', $content, $links);
        $internal_links = 0;
        $external_links = 0;
        $home_url = home_url();
        
        foreach ($links[1] as $link) {
            if (strpos($link, $home_url) !== false || strpos($link, '/') === 0) {
                $internal_links++;
            } elseif (strpos($link, 'http') === 0) {
                $external_links++;
            }
        }
        
        // Options
        $options = get_option('smart_seo_options', []);
        $min_words = isset($options['min_word_count']) ? intval($options['min_word_count']) : 300;
        
        // Analysis Results
        $analysis = [
            'word_count' => [
                'value' => $word_count,
                'status' => $word_count >= $min_words ? 'seo-good' : ($word_count >= ($min_words * 0.7) ? 'seo-warning' : 'seo-error'),
                'icon' => $word_count >= $min_words ? '✅' : ($word_count >= ($min_words * 0.7) ? '⚠️' : '❌')
            ],
            'paragraphs' => [
                'value' => $paragraph_count,
                'status' => $paragraph_count >= 3 ? 'seo-good' : ($paragraph_count >= 2 ? 'seo-warning' : 'seo-error'),
                'icon' => $paragraph_count >= 3 ? '✅' : ($paragraph_count >= 2 ? '⚠️' : '❌')
            ],
            'headings' => [
                'value' => $heading_count,
                'status' => $heading_count >= 2 ? 'seo-good' : ($heading_count >= 1 ? 'seo-warning' : 'seo-error'),
                'icon' => $heading_count >= 2 ? '✅' : ($heading_count >= 1 ? '⚠️' : '❌')
            ],
            'readability' => [
                'value' => self::calculate_readability_score($content),
                'status' => 'seo-good', // Simplified for now
                'icon' => '✅'
            ],
            'title' => [
                'value' => $title_length,
                'status' => ($title_length >= 30 && $title_length <= 60) ? 'seo-good' : (($title_length >= 20 && $title_length <= 80) ? 'seo-warning' : 'seo-error'),
                'icon' => ($title_length >= 30 && $title_length <= 60) ? '✅' : (($title_length >= 20 && $title_length <= 80) ? '⚠️' : '❌')
            ],
            'description' => [
                'value' => $description_length > 0 ? $description_length . ' chars' : 'Missing',
                'status' => ($description_length >= 120 && $description_length <= 160) ? 'seo-good' : (($description_length >= 100 && $description_length <= 200) ? 'seo-warning' : 'seo-error'),
                'icon' => ($description_length >= 120 && $description_length <= 160) ? '✅' : (($description_length >= 100 && $description_length <= 200) ? '⚠️' : '❌')
            ],
            'url' => [
                'value' => strlen($slug) <= 50 ? 'Good' : 'Too long',
                'status' => strlen($slug) <= 50 ? 'seo-good' : 'seo-warning',
                'icon' => strlen($slug) <= 50 ? '✅' : '⚠️'
            ],
            'images' => [
                'value' => $image_count,
                'status' => $image_count > 0 ? 'seo-good' : 'seo-warning',
                'icon' => $image_count > 0 ? '✅' : '⚠️'
            ],
            'alt_text' => [
                'value' => $image_count > 0 ? $alt_count . '/' . $image_count : 'N/A',
                'status' => $image_count === 0 ? 'seo-good' : ($alt_count === $image_count ? 'seo-good' : ($alt_count >= ($image_count * 0.7) ? 'seo-warning' : 'seo-error')),
                'icon' => $image_count === 0 ? '✅' : ($alt_count === $image_count ? '✅' : ($alt_count >= ($image_count * 0.7) ? '⚠️' : '❌'))
            ],
            'internal_links' => [
                'value' => $internal_links,
                'status' => $internal_links >= 2 ? 'seo-good' : ($internal_links >= 1 ? 'seo-warning' : 'seo-error'),
                'icon' => $internal_links >= 2 ? '✅' : ($internal_links >= 1 ? '⚠️' : '❌')
            ],
            'external_links' => [
                'value' => $external_links,
                'status' => $external_links >= 1 ? 'seo-good' : 'seo-warning',
                'icon' => $external_links >= 1 ? '✅' : '⚠️'
            ]
        ];
        
        // Generate recommendations
        $recommendations = [];
        
        if ($word_count < $min_words) {
            $recommendations[] = 'Add ' . ($min_words - $word_count) . ' more words to reach optimal content length';
        }
        
        if ($heading_count < 2) {
            $recommendations[] = 'Add more headings (H2, H3) to improve content structure';
        }
        
        if ($title_length < 30) {
            $recommendations[] = 'Make your title longer (30-60 characters is optimal)';
        } elseif ($title_length > 60) {
            $recommendations[] = 'Shorten your title (30-60 characters is optimal)';
        }
        
        if ($description_length === 0) {
            $recommendations[] = 'Add a meta description (excerpt) between 120-160 characters';
        } elseif ($description_length < 120) {
            $recommendations[] = 'Make your meta description longer (120-160 characters)';
        } elseif ($description_length > 160) {
            $recommendations[] = 'Shorten your meta description (120-160 characters)';
        }
        
        if ($image_count > 0 && $alt_count < $image_count) {
            $recommendations[] = 'Add alt text to ' . ($image_count - $alt_count) . ' images';
        }
        
        if ($internal_links < 2) {
            $recommendations[] = 'Add more internal links to related content';
        }
        
        if ($external_links === 0) {
            $recommendations[] = 'Consider adding 1-2 relevant external links';
        }
        
        $analysis['recommendations'] = $recommendations;
        
        return $analysis;
    }

    /**
     * Calculate basic readability score
     * 
     * @param string $content The content to analyze
     * @return string Readability level
     */
    public static function calculate_readability_score($content) {
        $text = strip_tags($content);
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $words = str_word_count($text);
        $sentence_count = count($sentences);
        
        if ($sentence_count === 0) return 'No content';
        
        $avg_words_per_sentence = $words / $sentence_count;
        
        if ($avg_words_per_sentence <= 15) {
            return 'Easy';
        } elseif ($avg_words_per_sentence <= 20) {
            return 'Medium';
        } else {
            return 'Difficult';
        }
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

    /**
     * AJAX handler for full SEO report
     */
    public static function ajax_get_full_seo_report() {
        check_ajax_referer('smart_seo_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        if (!$post_id || !current_user_can('edit_post', $post_id)) {
            wp_die('Access denied');
        }
        
        $post = get_post($post_id);
        if (!$post) {
            wp_send_json_error('Post not found');
        }
        
        $analysis = self::get_detailed_seo_analysis($post);
        $score = self::calculate_seo_score($post_id);
        $color = self::get_score_color($score);
        $status = self::get_score_status($score);
        
        ob_start();
        ?>
        <div class="smart-seo-full-report">
            <h2 style="margin-top: 0;">📊 Complete SEO Analysis</h2>
            <h3><?php echo esc_html($post->post_title); ?></h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <div style="font-size: 48px; font-weight: bold; color: <?php echo $color; ?>;"><?php echo $score; ?>/100</div>
                    <div style="font-size: 18px; font-weight: bold; color: <?php echo $color; ?>;"><?php echo $status; ?></div>
                </div>
                <div style="padding: 20px;">
                    <h4>Quick Stats</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li><strong>Published:</strong> <?php echo get_the_date('M j, Y', $post); ?></li>
                        <li><strong>Last Modified:</strong> <?php echo get_the_modified_date('M j, Y', $post); ?></li>
                        <li><strong>Word Count:</strong> <?php echo $analysis['word_count']['value']; ?> words</li>
                        <li><strong>Reading Time:</strong> <?php echo ceil($analysis['word_count']['value'] / 200); ?> minutes</li>
                    </ul>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h4>📝 Content Quality</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td>Word Count</td><td style="text-align: right;"><span class="<?php echo $analysis['word_count']['status']; ?>"><?php echo $analysis['word_count']['value']; ?> <?php echo $analysis['word_count']['icon']; ?></span></td></tr>
                        <tr><td>Paragraphs</td><td style="text-align: right;"><span class="<?php echo $analysis['paragraphs']['status']; ?>"><?php echo $analysis['paragraphs']['value']; ?> <?php echo $analysis['paragraphs']['icon']; ?></span></td></tr>
                        <tr><td>Headings</td><td style="text-align: right;"><span class="<?php echo $analysis['headings']['status']; ?>"><?php echo $analysis['headings']['value']; ?> <?php echo $analysis['headings']['icon']; ?></span></td></tr>
                        <tr><td>Readability</td><td style="text-align: right;"><span class="<?php echo $analysis['readability']['status']; ?>"><?php echo $analysis['readability']['value']; ?> <?php echo $analysis['readability']['icon']; ?></span></td></tr>
                    </table>
                    
                    <h4>🎯 SEO Elements</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td>Title Length</td><td style="text-align: right;"><span class="<?php echo $analysis['title']['status']; ?>"><?php echo $analysis['title']['value']; ?> <?php echo $analysis['title']['icon']; ?></span></td></tr>
                        <tr><td>Meta Description</td><td style="text-align: right;"><span class="<?php echo $analysis['description']['status']; ?>"><?php echo $analysis['description']['value']; ?> <?php echo $analysis['description']['icon']; ?></span></td></tr>
                        <tr><td>URL Structure</td><td style="text-align: right;"><span class="<?php echo $analysis['url']['status']; ?>"><?php echo $analysis['url']['value']; ?> <?php echo $analysis['url']['icon']; ?></span></td></tr>
                    </table>
                </div>
                
                <div>
                    <h4>🖼️ Media & Links</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td>Images</td><td style="text-align: right;"><span class="<?php echo $analysis['images']['status']; ?>"><?php echo $analysis['images']['value']; ?> <?php echo $analysis['images']['icon']; ?></span></td></tr>
                        <tr><td>Alt Text Coverage</td><td style="text-align: right;"><span class="<?php echo $analysis['alt_text']['status']; ?>"><?php echo $analysis['alt_text']['value']; ?> <?php echo $analysis['alt_text']['icon']; ?></span></td></tr>
                        <tr><td>Internal Links</td><td style="text-align: right;"><span class="<?php echo $analysis['internal_links']['status']; ?>"><?php echo $analysis['internal_links']['value']; ?> <?php echo $analysis['internal_links']['icon']; ?></span></td></tr>
                        <tr><td>External Links</td><td style="text-align: right;"><span class="<?php echo $analysis['external_links']['status']; ?>"><?php echo $analysis['external_links']['value']; ?> <?php echo $analysis['external_links']['icon']; ?></span></td></tr>
                    </table>
                    
                    <?php if (!empty($analysis['recommendations'])): ?>
                    <h4>💡 Priority Recommendations</h4>
                    <ol style="padding-left: 20px;">
                        <?php foreach (array_slice($analysis['recommendations'], 0, 5) as $recommendation): ?>
                            <li style="margin-bottom: 5px;"><?php echo $recommendation; ?></li>
                        <?php endforeach; ?>
                    </ol>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button type="button" class="button button-primary" onclick="window.print()">🖨️ Print Report</button>
                <button type="button" class="button" onclick="jQuery('#smart-seo-modal').remove()">Close</button>
            </div>
        </div>
        
        <style>
            .smart-seo-full-report table td { padding: 8px; border-bottom: 1px solid #f0f0f0; }
            .smart-seo-full-report .seo-good { color: #059669; font-weight: bold; }
            .smart-seo-full-report .seo-warning { color: #d97706; font-weight: bold; }
            .smart-seo-full-report .seo-error { color: #dc2626; font-weight: bold; }
        </style>
        <?php
        
        $html = ob_get_clean();
        
        wp_send_json_success([
            'html' => $html
        ]);
    }
    
    public static function enqueue_score_scripts($hook) {
        if (in_array($hook, ['post.php', 'post-new.php', 'edit.php'])) {
            wp_enqueue_script('jquery');
            
            $script = "
            function smartSeoRefreshScore(postId) {
                const button = jQuery('button:contains(\"Refresh Analysis\")');
                const originalText = button.text();
                button.text('🔄 Refreshing...').prop('disabled', true);
                
                jQuery.post(ajaxurl, {
                    action: 'get_seo_score',
                    post_id: postId,
                    nonce: '" . wp_create_nonce('smart_seo_nonce') . "'
                }, function(response) {
                    if (response.success) {
                        location.reload(); // Reload to show updated analysis
                    } else {
                        alert('Error refreshing SEO analysis. Please try again.');
                        button.text(originalText).prop('disabled', false);
                    }
                }).fail(function() {
                    alert('Error refreshing SEO analysis. Please try again.');
                    button.text(originalText).prop('disabled', false);
                });
            }
            
            function smartSeoShowFullReport(postId) {
                // Create a modal/popup with detailed SEO report
                const modal = jQuery('<div>', {
                    id: 'smart-seo-modal',
                    css: {
                        position: 'fixed',
                        top: 0,
                        left: 0,
                        width: '100%',
                        height: '100%',
                        backgroundColor: 'rgba(0,0,0,0.7)',
                        zIndex: 999999,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center'
                    }
                });
                
                const content = jQuery('<div>', {
                    css: {
                        backgroundColor: 'white',
                        padding: '30px',
                        borderRadius: '8px',
                        maxWidth: '800px',
                        maxHeight: '80vh',
                        overflow: 'auto',
                        position: 'relative'
                    },
                    html: '<div style=\"text-align: center;\"><h2>📊 Full SEO Report</h2><p>Loading detailed analysis...</p></div>'
                });
                
                const closeBtn = jQuery('<button>', {
                    text: '×',
                    css: {
                        position: 'absolute',
                        top: '10px',
                        right: '15px',
                        background: 'none',
                        border: 'none',
                        fontSize: '24px',
                        cursor: 'pointer',
                        color: '#666'
                    },
                    click: function() {
                        modal.remove();
                    }
                });
                
                content.append(closeBtn);
                modal.append(content);
                jQuery('body').append(modal);
                
                // Load full report data
                jQuery.post(ajaxurl, {
                    action: 'get_full_seo_report',
                    post_id: postId,
                    nonce: '" . wp_create_nonce('smart_seo_nonce') . "'
                }, function(response) {
                    if (response.success) {
                        content.html(response.data.html + closeBtn[0].outerHTML);
                    } else {
                        content.html('<h2>Error</h2><p>Could not load SEO report.</p>' + closeBtn[0].outerHTML);
                    }
                }).fail(function() {
                    content.html('<h2>Error</h2><p>Could not load SEO report.</p>' + closeBtn[0].outerHTML);
                });
                
                // Close modal when clicking outside
                modal.click(function(e) {
                    if (e.target === modal[0]) {
                        modal.remove();
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
                            // Could add real-time updates here without full reload
                            // smartSeoRefreshScore(postId);
                        }
                    }, 3000); // Check every 3 seconds instead of 1
                });
            }
            ";
            
            wp_add_inline_script('jquery', $script);
        }
    }
}
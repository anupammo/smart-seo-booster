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
        add_filter('manage_posts_columns', [__CLASS__, 'smart_seo_add_seo_score_column']);
        add_filter('manage_pages_columns', [__CLASS__, 'smart_seo_add_seo_score_column']);
        add_action('manage_posts_custom_column', [__CLASS__, 'smart_seo_display_seo_score_column'], 10, 2);
        add_action('manage_pages_custom_column', [__CLASS__, 'smart_seo_display_seo_score_column'], 10, 2);
        
        // Add AJAX handlers for SEO analysis
        add_action('wp_ajax_get_seo_score', [__CLASS__, 'smart_seo_ajax_get_seo_score']);
        add_action('wp_ajax_get_full_seo_report', [__CLASS__, 'smart_seo_ajax_get_full_seo_report']);
        
        // Enqueue scripts for real-time updates
        add_action('admin_enqueue_scripts', [__CLASS__, 'smart_seo_enqueue_score_scripts']);
    }
    
    public static function calculate_seo_score($post_id) {
        $post = get_post($post_id);
        if (!$post) return 0;
        
        $options = get_option('smart_seo_options', []);
        $min_words = isset($options['min_word_count']) ? intval($options['min_word_count']) : 300;
        
        $content = $post->post_content;
        $word_count = str_word_count( Smart_SEO_Meta_Templates::plain_text( $content ) );
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
        $word_count = str_word_count( Smart_SEO_Meta_Templates::plain_text( $content ) );
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
                '<span style="color: %s;"><span class="dashicons dashicons-chart-bar" aria-hidden="true"></span> SEO: %d/100</span>',
                esc_attr( $color ),
                absint( $score )
            ),
            'href' => admin_url('admin.php?page=smart-seo-audit'),
            'meta' => [
                'title' => 'SEO Score: ' . absint($score) . '/100 (' . sanitize_text_field($status) . ')'
            ]
        ]);
    }
    
    public static function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'smart_seo_dashboard',
            '<img src="' . esc_url( SMART_SEO_BOOSTER_ICON_URL ) . '" width="18" height="18" alt="" style="vertical-align:text-bottom;border-radius:4px;" /> ' . esc_html__( 'Smart SEO Overview', 'smart-seo-booster' ),
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
                echo '<td><a href="' . esc_url( get_edit_post_link($post->ID) ) . '">' . esc_html($post->post_title) . '</a></td>';
                echo '<td><span style="color: ' . esc_attr($color) . '; font-weight: bold;">' . absint($score) . '/100</span></td>';
                echo '<td><span style="color: ' . esc_attr($color) . ';">' . esc_html($status) . '</span></td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
            
            $avg_score = $post_count > 0 ? round($total_score / $post_count) : 0;
            $avg_color = self::get_score_color($avg_score);
            
            echo '<div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">';
            echo '<strong>Average Score: <span style="color: ' . esc_attr($avg_color) . ';">' . absint($avg_score) . '/100</span></strong>';
            echo '</div>';
        } else {
            echo '<p>No content found.</p>';
        }
        
        echo '<div style="margin-top: 15px; text-align: center;">';
        echo '<a href="' . esc_url( admin_url('admin.php?page=smart-seo-audit') ) . '" class="button button-primary">View Full Audit Report</a>';
        echo '</div>';
        echo '</div>';
    }
    
    public static function add_seo_score_metabox() {
        $post_types = ['post', 'page'];
        foreach ($post_types as $post_type) {
            add_meta_box(
                'smart_seo_score',
                '<span class="dashicons dashicons-chart-bar" aria-hidden="true"></span> SEO Score',
                [__CLASS__, 'seo_score_metabox_content'],
                $post_type,
                'side',
                'high'
            );
        }
    }
    
    public static function seo_score_metabox_content($post) {
        $score   = self::calculate_seo_score($post->ID);
        $color   = self::get_score_color($score);
        $status  = self::get_score_status($score);
        $analysis = self::smart_seo_get_detailed_seo_analysis($post);
        ?>
        <div class="smart-seo-score-metabox">

            <!-- Overall Score -->
            <div class="seo-section" style="text-align: center;">
                <div class="seo-score-circle" style="margin: 10px auto; width: 80px; height: 80px; border-radius: 50%; background: conic-gradient(<?php echo esc_attr( $color ); ?> <?php echo esc_attr( $score * 3.6 ); ?>deg, #e5e7eb 0deg); display: flex; align-items: center; justify-content: center;">
                    <div style="width: 60px; height: 60px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                        <strong style="font-size: 18px; color: <?php echo esc_attr( $color ); ?>; "><?php echo absint( $score ); ?></strong>
                        <small style="color: #666;">/ 100</small>
                    </div>
                </div>
                <p><strong style="color: <?php echo esc_attr( $color ); ?>; "><?php echo esc_html( $status ); ?></strong></p>
            </div>

            <!-- Content Analysis -->
            <div class="seo-section">
                <h4 style="margin: 0 0 10px 0;"><span class="dashicons dashicons-edit" aria-hidden="true"></span> Content Analysis</h4>
                <div class="seo-check">
                    <span>Word Count</span>
                    <span class="seo-check-status <?php echo esc_attr( $analysis['word_count']['status'] ?? '' ); ?>">
                        <?php echo isset($analysis['word_count']['value']) ? absint( $analysis['word_count']['value'] ) : 0; ?> <?php echo wp_kses_post( $analysis['word_count']['icon'] ?? '' ); ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>Paragraphs</span>
                    <span class="seo-check-status <?php echo esc_attr( $analysis['paragraphs']['status'] ?? '' ); ?>">
                        <?php echo isset($analysis['paragraphs']['value']) ? absint( $analysis['paragraphs']['value'] ) : 0; ?> <?php echo wp_kses_post( $analysis['paragraphs']['icon'] ?? '' ); ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>Headings</span>
                    <span class="seo-check-status <?php echo esc_attr( $analysis['headings']['status'] ?? '' ); ?>">
                        <?php echo isset($analysis['headings']['value']) ? absint( $analysis['headings']['value'] ) : 0; ?> <?php echo wp_kses_post( $analysis['headings']['icon'] ?? '' ); ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>Reading Level</span>
                    <span class="seo-check-status <?php echo esc_attr( $analysis['readability']['status'] ?? '' ); ?>">
                        <?php echo esc_html( $analysis['readability']['value'] ?? '' ); ?> <?php echo wp_kses_post( $analysis['readability']['icon'] ?? '' ); ?>
                    </span>
                </div>
            </div>

            <!-- SEO Elements -->
            <div class="seo-section">
                <h4 style="margin: 0 0 10px 0;"><span class="dashicons dashicons-marker" aria-hidden="true"></span> SEO Elements</h4>
                <div class="seo-check">
                    <span>Title Length</span>
                    <span class="seo-check-status <?php echo esc_attr( $analysis['title']['status'] ?? '' ); ?>">
                        <?php echo isset($analysis['title']['value']) ? absint( $analysis['title']['value'] ) : 0; ?> chars <?php echo wp_kses_post( $analysis['title']['icon'] ?? '' ); ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>Meta Description</span>
                    <span class="seo-check-status <?php echo esc_attr( $analysis['description']['status'] ?? '' ); ?>">
                        <?php echo esc_html( $analysis['description']['value'] ?? '' ); ?> <?php echo wp_kses_post( $analysis['description']['icon'] ?? '' ); ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>URL Structure</span>
                    <span class="seo-check-status <?php echo esc_attr( $analysis['url']['status'] ?? '' ); ?>">
                        <?php echo esc_html( $analysis['url']['value'] ?? '' ); ?> <?php echo wp_kses_post( $analysis['url']['icon'] ?? '' ); ?>
                    </span>
                </div>
            </div>

            <!-- Media & Links -->
            <div class="seo-section">
                <h4 style="margin: 0 0 10px 0;"><span class="dashicons dashicons-format-image" aria-hidden="true"></span> Media & Links</h4>
                <div class="seo-check">
                    <span>Images</span>
                    <span class="seo-check-status <?php echo esc_attr( $analysis['images']['status'] ?? '' ); ?>">
                        <?php echo isset($analysis['images']['value']) ? absint( $analysis['images']['value'] ) : 0; ?> <?php echo wp_kses_post( $analysis['images']['icon'] ?? '' ); ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>Alt Text</span>
                    <span class="seo-check-status <?php echo esc_attr( $analysis['alt_text']['status'] ?? '' ); ?>">
                        <?php echo esc_html( $analysis['alt_text']['value'] ?? '' ); ?> <?php echo wp_kses_post( $analysis['alt_text']['icon'] ?? '' ); ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>Internal Links</span>
                    <span class="seo-check-status <?php echo esc_attr( $analysis['internal_links']['status'] ?? '' ); ?>">
                        <?php echo isset($analysis['internal_links']['value']) ? absint( $analysis['internal_links']['value'] ) : 0; ?> <?php echo wp_kses_post( $analysis['internal_links']['icon'] ?? '' ); ?>
                    </span>
                </div>
                <div class="seo-check">
                    <span>External Links</span>
                    <span class="seo-check-status <?php echo esc_attr( $analysis['external_links']['status'] ?? '' ); ?>">
                        <?php echo esc_html( $analysis['external_links']['value'] ?? '' ); ?> <?php echo wp_kses_post( $analysis['external_links']['icon'] ?? '' ); ?>
                    </span>
                </div>
            </div>

            <!-- Recommendations -->
            <?php if (!empty($analysis['recommendations']) && is_array($analysis['recommendations'])): ?>
            <div class="seo-section">
                <h4 style="margin: 0 0 10px 0;"><span class="dashicons dashicons-lightbulb" aria-hidden="true"></span> Recommendations</h4>
                <div class="seo-recommendations">
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach ($analysis['recommendations'] as $recommendation): ?>
                            <li><?php echo esc_html( $recommendation ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="smart-seo-score-actions">
                <button type="button" id="smart-seo-refresh-btn" class="button button-primary" onclick="smartSeoRefreshScore(<?php echo absint( $post->ID ); ?>)">
                    <span class="dashicons dashicons-update" aria-hidden="true"></span> <span class="smart-seo-refresh-label"><?php esc_html_e( 'Refresh Analysis', 'smart-seo-booster' ); ?></span>
                </button>
                <button type="button" class="button" onclick="smartSeoShowFullReport(<?php echo absint( $post->ID ); ?>)">
                    <span class="dashicons dashicons-chart-bar" aria-hidden="true"></span> <?php esc_html_e( 'Full Report', 'smart-seo-booster' ); ?>
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
    public static function smart_seo_get_detailed_seo_analysis($post) {
        $content = $post->post_content;
        $title = $post->post_title;
        $excerpt = $post->post_excerpt;
        $slug = $post->post_name;
        
        // Content Analysis
        $word_count = str_word_count( Smart_SEO_Meta_Templates::plain_text( $content ) );
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
                'icon' => $word_count >= $min_words ? '<span class="dashicons dashicons-yes-alt" style="color:#059669" aria-hidden="true"></span>' : ($word_count >= ($min_words * 0.7) ? '<span class="dashicons dashicons-warning" style="color:#d97706" aria-hidden="true"></span>' : '<span class="dashicons dashicons-dismiss" style="color:#dc2626" aria-hidden="true"></span>')
            ],
            'paragraphs' => [
                'value' => $paragraph_count,
                'status' => $paragraph_count >= 3 ? 'seo-good' : ($paragraph_count >= 2 ? 'seo-warning' : 'seo-error'),
                'icon' => $paragraph_count >= 3 ? '<span class="dashicons dashicons-yes-alt" style="color:#059669" aria-hidden="true"></span>' : ($paragraph_count >= 2 ? '<span class="dashicons dashicons-warning" style="color:#d97706" aria-hidden="true"></span>' : '<span class="dashicons dashicons-dismiss" style="color:#dc2626" aria-hidden="true"></span>')
            ],
            'headings' => [
                'value' => $heading_count,
                'status' => $heading_count >= 2 ? 'seo-good' : ($heading_count >= 1 ? 'seo-warning' : 'seo-error'),
                'icon' => $heading_count >= 2 ? '<span class="dashicons dashicons-yes-alt" style="color:#059669" aria-hidden="true"></span>' : ($heading_count >= 1 ? '<span class="dashicons dashicons-warning" style="color:#d97706" aria-hidden="true"></span>' : '<span class="dashicons dashicons-dismiss" style="color:#dc2626" aria-hidden="true"></span>')
            ],
            'readability' => self::smart_seo_readability_field($content),
            'title' => [
                'value' => $title_length,
                'status' => ($title_length >= 30 && $title_length <= 60) ? 'seo-good' : (($title_length >= 20 && $title_length <= 80) ? 'seo-warning' : 'seo-error'),
                'icon' => ($title_length >= 30 && $title_length <= 60) ? '<span class="dashicons dashicons-yes-alt" style="color:#059669" aria-hidden="true"></span>' : (($title_length >= 20 && $title_length <= 80) ? '<span class="dashicons dashicons-warning" style="color:#d97706" aria-hidden="true"></span>' : '<span class="dashicons dashicons-dismiss" style="color:#dc2626" aria-hidden="true"></span>')
            ],
            'description' => [
                'value' => $description_length > 0 ? $description_length . ' chars' : 'Missing',
                'status' => ($description_length >= 120 && $description_length <= 160) ? 'seo-good' : (($description_length >= 100 && $description_length <= 200) ? 'seo-warning' : 'seo-error'),
                'icon' => ($description_length >= 120 && $description_length <= 160) ? '<span class="dashicons dashicons-yes-alt" style="color:#059669" aria-hidden="true"></span>' : (($description_length >= 100 && $description_length <= 200) ? '<span class="dashicons dashicons-warning" style="color:#d97706" aria-hidden="true"></span>' : '<span class="dashicons dashicons-dismiss" style="color:#dc2626" aria-hidden="true"></span>')
            ],
            'url' => [
                'value' => strlen($slug) <= 50 ? 'Good' : 'Too long',
                'status' => strlen($slug) <= 50 ? 'seo-good' : 'seo-warning',
                'icon' => strlen($slug) <= 50 ? '<span class="dashicons dashicons-yes-alt" style="color:#059669" aria-hidden="true"></span>' : '<span class="dashicons dashicons-warning" style="color:#d97706" aria-hidden="true"></span>'
            ],
            'images' => [
                'value' => $image_count,
                'status' => $image_count > 0 ? 'seo-good' : 'seo-warning',
                'icon' => $image_count > 0 ? '<span class="dashicons dashicons-yes-alt" style="color:#059669" aria-hidden="true"></span>' : '<span class="dashicons dashicons-warning" style="color:#d97706" aria-hidden="true"></span>'
            ],
            'alt_text' => [
                'value' => $image_count > 0 ? $alt_count . '/' . $image_count : 'N/A',
                'status' => $image_count === 0 ? 'seo-good' : ($alt_count === $image_count ? 'seo-good' : ($alt_count >= ($image_count * 0.7) ? 'seo-warning' : 'seo-error')),
                'icon' => $image_count === 0 ? '<span class="dashicons dashicons-yes-alt" style="color:#059669" aria-hidden="true"></span>' : ($alt_count === $image_count ? '<span class="dashicons dashicons-yes-alt" style="color:#059669" aria-hidden="true"></span>' : ($alt_count >= ($image_count * 0.7) ? '<span class="dashicons dashicons-warning" style="color:#d97706" aria-hidden="true"></span>' : '<span class="dashicons dashicons-dismiss" style="color:#dc2626" aria-hidden="true"></span>'))
            ],
            'internal_links' => [
                'value' => $internal_links,
                'status' => $internal_links >= 2 ? 'seo-good' : ($internal_links >= 1 ? 'seo-warning' : 'seo-error'),
                'icon' => $internal_links >= 2 ? '<span class="dashicons dashicons-yes-alt" style="color:#059669" aria-hidden="true"></span>' : ($internal_links >= 1 ? '<span class="dashicons dashicons-warning" style="color:#d97706" aria-hidden="true"></span>' : '<span class="dashicons dashicons-dismiss" style="color:#dc2626" aria-hidden="true"></span>')
            ],
            'external_links' => [
                'value' => $external_links,
                'status' => $external_links >= 1 ? 'seo-good' : 'seo-warning',
                'icon' => $external_links >= 1 ? '<span class="dashicons dashicons-yes-alt" style="color:#059669" aria-hidden="true"></span>' : '<span class="dashicons dashicons-warning" style="color:#d97706" aria-hidden="true"></span>'
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

        if ( isset( $analysis['readability']['score'] ) && $analysis['readability']['score'] < 60 ) {
            $recommendations[] = 'Simplify your writing (shorter sentences, simpler words) to improve readability';
        }

        $analysis['recommendations'] = $recommendations;
        
        return $analysis;
    }

    /**
     * Flesch Reading Ease score (standard 0–100 scale; higher = easier),
     * the same metric Yoast/RankMath surface as "readability". Built from
     * an approximate English syllable count since PHP has no dictionary
     * lookup available here — accurate enough to bucket content, not meant
     * to be exact for every word.
     *
     * @param string $content Raw post content (HTML allowed; stripped here).
     * @return array{score:?int,label:string}
     */
    public static function smart_seo_flesch_reading_ease( $content ) {
        $text = Smart_SEO_Meta_Templates::plain_text( $content );

        $words = preg_split( '/\s+/', trim( $text ), -1, PREG_SPLIT_NO_EMPTY );
        $word_count = count( $words );

        $sentences = preg_split( '/[.!?]+(?:\s|$)/', $text, -1, PREG_SPLIT_NO_EMPTY );
        $sentence_count = count( $sentences );

        if ( 0 === $word_count || 0 === $sentence_count ) {
            return [ 'score' => null, 'label' => __( 'No content', 'smart-seo-booster' ) ];
        }

        $syllable_count = 0;
        foreach ( $words as $word ) {
            $syllable_count += self::smart_seo_count_syllables( $word );
        }

        $score = 206.835 - 1.015 * ( $word_count / $sentence_count ) - 84.6 * ( $syllable_count / $word_count );
        $score = (int) round( max( 0, min( 100, $score ) ) );

        if ( $score >= 80 ) {
            $label = __( 'Easy to read', 'smart-seo-booster' );
        } elseif ( $score >= 60 ) {
            $label = __( 'Standard', 'smart-seo-booster' );
        } elseif ( $score >= 30 ) {
            $label = __( 'Fairly difficult', 'smart-seo-booster' );
        } else {
            $label = __( 'Difficult', 'smart-seo-booster' );
        }

        return [ 'score' => $score, 'label' => $label ];
    }

    /**
     * Rough English syllable estimate (vowel-group heuristic with common
     * silent-e/-es/-ed trimming) — the standard approximation used by most
     * Flesch-score implementations that don't ship a pronunciation dictionary.
     */
    private static function smart_seo_count_syllables( $word ) {
        $word = strtolower( preg_replace( '/[^a-zA-Z]/', '', $word ) );
        if ( '' === $word ) {
            return 0;
        }
        if ( strlen( $word ) <= 3 ) {
            return 1;
        }
        $word = preg_replace( '/(?:[^laeiouy]es|ed|[^laeiouy]e)$/', '', $word );
        $word = preg_replace( '/^y/', '', $word );
        preg_match_all( '/[aeiouy]{1,2}/', $word, $m );
        return max( 1, count( $m[0] ) );
    }

    /**
     * Build the 'readability' row for the analysis table from a Flesch score.
     */
    private static function smart_seo_readability_field( $content ) {
        $result = self::smart_seo_flesch_reading_ease( $content );
        $score  = $result['score'];

        if ( null === $score ) {
            return [
                'value'  => $result['label'],
                'status' => 'seo-warning',
                'icon'   => '<span class="dashicons dashicons-warning" style="color:#d97706" aria-hidden="true"></span>',
            ];
        }

        $status = $score >= 60 ? 'seo-good' : ( $score >= 30 ? 'seo-warning' : 'seo-error' );
        $icon   = $score >= 60
            ? '<span class="dashicons dashicons-yes-alt" style="color:#059669" aria-hidden="true"></span>'
            : ( $score >= 30
                ? '<span class="dashicons dashicons-warning" style="color:#d97706" aria-hidden="true"></span>'
                : '<span class="dashicons dashicons-dismiss" style="color:#dc2626" aria-hidden="true"></span>' );

        return [
            /* translators: 1: Flesch reading-ease score (0-100), 2: difficulty label */
            'value'  => sprintf( __( '%1$d/100 (%2$s)', 'smart-seo-booster' ), $score, $result['label'] ),
            'status' => $status,
            'icon'   => $icon,
            'score'  => $score,
        ];
    }

    public static function smart_seo_add_seo_score_column($columns) {
        $columns['seo_score'] = '<span class="dashicons dashicons-chart-bar" aria-hidden="true"></span> SEO Score';
        return $columns;
    }
    
    public static function smart_seo_display_seo_score_column($column, $post_id) {
        if ($column === 'seo_score') {
            $score = self::calculate_seo_score($post_id);
            $color = self::get_score_color($score);
            $status = self::get_score_status($score);
            
            echo '<div>';
            echo '<strong style="color: ' . esc_attr($color) . '; font-size: 14px;">' . absint($score) . '/100</strong><br>';
            echo '<small style="color: ' . esc_attr($color) . ';">' . esc_html($status) . '</small>';
            echo '</div>';
        }
    }
    
    public static function smart_seo_ajax_get_seo_score() {
        check_ajax_referer('smart_seo_nonce', 'nonce');
        
        $post_id = 0;
        if ( isset( $_POST['post_id'] ) ) {
            $post_id = absint( wp_unslash( $_POST['post_id'] ) );
        }
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
    public static function smart_seo_ajax_get_full_seo_report() {
        check_ajax_referer('smart_seo_nonce', 'nonce');
        
        $post_id = 0;
        if ( isset( $_POST['post_id'] ) ) {
            $post_id = absint( wp_unslash( $_POST['post_id'] ) );
        }
        if (!$post_id || !current_user_can('edit_post', $post_id)) {
            wp_die('Access denied');
        }
        
        $post = get_post($post_id);
        if (!$post) {
            wp_send_json_error('Post not found');
        }
        
        $analysis = self::smart_seo_get_detailed_seo_analysis($post);
        $score = self::calculate_seo_score($post_id);
        $color = self::get_score_color($score);
        $status = self::get_score_status($score);

        // Same circular-gauge geometry as the site-wide Audit Report, so the
        // per-post report reads as the same product rather than a bolted-on
        // afterthought.
        $smart_seo_r    = 52;
        $smart_seo_circ = 2 * M_PI * $smart_seo_r;
        $smart_seo_off  = $smart_seo_circ * ( 1 - $score / 100 );

        ob_start();
        ?>
        <?php /* Note: intentionally .ssb-app only (no ssb-adapt) — this modal's
         * container background is a hardcoded white div created in seo-score.js,
         * so opting into the dark-mode ink-color override here without a matching
         * dark background would reproduce the exact contrast bug fixed elsewhere. */ ?>
        <div class="smart-seo-full-report ssb-app">
            <h2 style="margin-top: 0;"><img src="<?php echo esc_url( SMART_SEO_BOOSTER_ICON_URL ); ?>" width="22" height="22" alt="" style="vertical-align:text-bottom;border-radius:4px;" /> <?php esc_html_e( 'Complete SEO Analysis', 'smart-seo-booster' ); ?></h2>
            <h3><?php echo esc_html($post->post_title); ?></h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="ssb-card" style="text-align: center;">
                    <div class="ssb-gauge" style="justify-content: center;">
                        <svg width="120" height="120" viewBox="0 0 120 120" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: score */ __( 'Score %d out of 100', 'smart-seo-booster' ), $score ) ); ?>">
                            <circle cx="60" cy="60" r="<?php echo esc_attr( $smart_seo_r ); ?>" fill="none" stroke="var(--ssb-line)" stroke-width="12" />
                            <circle cx="60" cy="60" r="<?php echo esc_attr( $smart_seo_r ); ?>" fill="none" stroke="<?php echo esc_attr( $color ); ?>" stroke-width="12" stroke-linecap="round"
                                stroke-dasharray="<?php echo esc_attr( $smart_seo_circ ); ?>" stroke-dashoffset="<?php echo esc_attr( $smart_seo_off ); ?>"
                                transform="rotate(-90 60 60)" />
                        </svg>
                    </div>
                    <div style="font-size: 24px; font-weight: bold; color: <?php echo esc_attr( $color ); ?>; margin-top: 10px;"><?php echo absint( $score ); ?><span style="font-size:14px;color:var(--ssb-muted);">/100</span></div>
                    <div style="font-size: 15px; font-weight: bold; color: <?php echo esc_attr( $color ); ?>;"><?php echo esc_html( $status ); ?></div>
                </div>
                <div class="ssb-card">
                    <h4><?php esc_html_e( 'Quick Stats', 'smart-seo-booster' ); ?></h4>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li><strong><?php esc_html_e( 'Published', 'smart-seo-booster' ); ?>:</strong> <?php echo esc_html( get_the_date('M j, Y', $post) ); ?></li>
                        <li><strong><?php esc_html_e( 'Last Modified', 'smart-seo-booster' ); ?>:</strong> <?php echo esc_html( get_the_modified_date('M j, Y', $post) ); ?></li>
                        <li><strong><?php esc_html_e( 'Word Count', 'smart-seo-booster' ); ?>:</strong> <?php echo isset($analysis['word_count']['value']) ? absint( $analysis['word_count']['value'] ) : 0; ?></li>
                        <li><strong><?php esc_html_e( 'Reading Time', 'smart-seo-booster' ); ?>:</strong> <?php echo isset($analysis['word_count']['value']) ? absint( ceil($analysis['word_count']['value'] / 200) ) : 0; ?> <?php esc_html_e( 'minutes', 'smart-seo-booster' ); ?></li>
                    </ul>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="ssb-card">
                    <h4><span class="dashicons dashicons-edit" aria-hidden="true"></span> Content Quality</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td>Word Count</td><td style="text-align: right;"><span class="<?php echo esc_attr( $analysis['word_count']['status'] ?? '' ); ?>"><?php echo isset($analysis['word_count']['value']) ? absint( $analysis['word_count']['value'] ) : 0; ?> <?php echo wp_kses_post( $analysis['word_count']['icon'] ?? '' ); ?></span></td></tr>
                        <tr><td>Paragraphs</td><td style="text-align: right;"><span class="<?php echo esc_attr( $analysis['paragraphs']['status'] ?? '' ); ?>"><?php echo isset($analysis['paragraphs']['value']) ? absint( $analysis['paragraphs']['value'] ) : 0; ?> <?php echo wp_kses_post( $analysis['paragraphs']['icon'] ?? '' ); ?></span></td></tr>
                        <tr><td>Headings</td><td style="text-align: right;"><span class="<?php echo esc_attr( $analysis['headings']['status'] ?? '' ); ?>"><?php echo isset($analysis['headings']['value']) ? absint( $analysis['headings']['value'] ) : 0; ?> <?php echo wp_kses_post( $analysis['headings']['icon'] ?? '' ); ?></span></td></tr>
                        <tr><td>Readability</td><td style="text-align: right;"><span class="<?php echo esc_attr( $analysis['readability']['status'] ?? '' ); ?>"><?php echo esc_html( $analysis['readability']['value'] ?? '' ); ?> <?php echo wp_kses_post( $analysis['readability']['icon'] ?? '' ); ?></span></td></tr>
                    </table>
                    
                    <h4><span class="dashicons dashicons-marker" aria-hidden="true"></span> SEO Elements</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td>Title Length</td><td style="text-align: right;"><span class="<?php echo esc_attr( $analysis['title']['status'] ); ?>"><?php echo isset($analysis['title']['value']) ? absint( $analysis['title']['value'] ) : 0; ?> <?php echo wp_kses_post( $analysis['title']['icon'] ); ?></span></td></tr>
                        <tr><td>Meta Description</td><td style="text-align: right;"><span class="<?php echo esc_attr( $analysis['description']['status'] ); ?>"><?php echo esc_html( $analysis['description']['value'] ); ?> <?php echo wp_kses_post( $analysis['description']['icon'] ); ?></span></td></tr>
                        <tr><td>URL Structure</td><td style="text-align: right;"><span class="<?php echo esc_attr( $analysis['url']['status'] ); ?>"><?php echo esc_html( $analysis['url']['value'] ); ?> <?php echo wp_kses_post( $analysis['url']['icon'] ); ?></span></td></tr>
                    </table>
                </div>
                
                <div class="ssb-card">
                    <h4><span class="dashicons dashicons-format-image" aria-hidden="true"></span> Media & Links</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td>Images</td><td style="text-align: right;"><span class="<?php echo esc_attr( $analysis['images']['status'] ); ?>"><?php echo isset($analysis['images']['value']) ? absint( $analysis['images']['value'] ) : 0; ?> <?php echo wp_kses_post( $analysis['images']['icon'] ); ?></span></td></tr>
                        <tr><td>Alt Text Coverage</td><td style="text-align: right;"><span class="<?php echo esc_attr( $analysis['alt_text']['status'] ); ?>"><?php echo esc_html( $analysis['alt_text']['value'] ); ?> <?php echo wp_kses_post( $analysis['alt_text']['icon'] ); ?></span></td></tr>
                        <tr><td>Internal Links</td><td style="text-align: right;"><span class="<?php echo esc_attr( $analysis['internal_links']['status'] ); ?>"><?php echo isset($analysis['internal_links']['value']) ? absint( $analysis['internal_links']['value'] ) : 0; ?> <?php echo wp_kses_post( $analysis['internal_links']['icon'] ); ?></span></td></tr>
                        <tr><td>External Links</td><td style="text-align: right;"><span class="<?php echo esc_attr( $analysis['external_links']['status'] ); ?>"><?php echo isset($analysis['external_links']['value']) ? absint( $analysis['external_links']['value'] ) : 0; ?> <?php echo wp_kses_post( $analysis['external_links']['icon'] ); ?></span></td></tr>
                    </table>
                    
                    <?php if (!empty($analysis['recommendations'])): ?>
                    <h4><span class="dashicons dashicons-lightbulb" aria-hidden="true"></span> Priority Recommendations</h4>
                    <ol style="padding-left: 20px;">
                        <?php foreach (array_slice($analysis['recommendations'], 0, 5) as $recommendation): ?>
                            <li style="margin-bottom: 5px;"><?php echo esc_html( $recommendation ); ?></li>
                        <?php endforeach; ?>
                    </ol>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="display: flex; justify-content: center; gap: 10px; margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--ssb-line);">
                <button type="button" class="button button-primary" onclick="window.print()"><span class="dashicons dashicons-printer" aria-hidden="true"></span> <?php esc_html_e( 'Print Report', 'smart-seo-booster' ); ?></button>
                <button type="button" class="button smart-seo-modal-close"><?php esc_html_e( 'Close', 'smart-seo-booster' ); ?></button>
            </div>
        </div>
        
        <?php
        
        $html = ob_get_clean();
        
        wp_send_json_success([
            'html' => $html
        ]);
    }
    
    public static function smart_seo_enqueue_score_scripts($hook) {
        if (!in_array($hook, ['post.php', 'post-new.php', 'edit.php'])) {
            return;
        }

        $ver     = defined('SMART_SEO_BOOSTER_VERSION') ? SMART_SEO_BOOSTER_VERSION : false;
        $css_url = plugin_dir_url(__FILE__) . '../css/meta-box.css';
        $js_url  = plugin_dir_url(__FILE__) . '../js/seo-score.js';

        wp_enqueue_style('smart-seo-meta-box', $css_url, ['dashicons'], $ver);

        wp_enqueue_script('smart-seo-score', $js_url, ['jquery'], $ver, true);
        wp_localize_script('smart-seo-score', 'SmartSEOScore', [
            'ajaxUrl'          => admin_url('admin-ajax.php'),
            'nonce'            => wp_create_nonce('smart_seo_nonce'),
            'refreshingText'   => __( 'Refreshing…', 'smart-seo-booster' ),
            'refreshErrorText' => __( 'Error refreshing SEO analysis. Please try again.', 'smart-seo-booster' ),
        ]);
    }
}

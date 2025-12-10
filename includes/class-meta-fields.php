<?php
/**
 * Smart SEO Meta Fields Class
 * 
 * Handles custom meta fields for SEO optimization
 * 
 * @package SmartSEOBooster
 * @since 2.1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

/**
 * Class Smart_SEO_Meta_Fields
 * 
 * Manages SEO meta fields for posts and pages
 */
class Smart_SEO_Meta_Fields {
    
    /**
     * Initialize meta fields functionality
     * 
     * @since 2.1.0
     */
    public static function init() {
        // Add meta boxes
        add_action('add_meta_boxes', [__CLASS__, 'add_seo_meta_boxes']);
        
        // Save meta fields
        add_action('save_post', [__CLASS__, 'save_seo_meta_fields']);
        
        // Enqueue scripts and styles
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_meta_scripts']);
        
        // Override WordPress SEO output
        add_action('wp_head', [__CLASS__, 'output_custom_meta_tags'], 1);
        
        // Add meta fields to REST API for block editor
        add_action('init', [__CLASS__, 'register_meta_fields_for_rest']);
    }
    
    /**
     * Add SEO meta boxes to post/page edit screens
     * 
     * @since 2.1.0
     */
    public static function add_seo_meta_boxes() {
        $post_types = ['post', 'page'];
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'smart_seo_meta_fields',
                '🎯 SEO Meta Tags & Social Preview',
                [__CLASS__, 'meta_fields_metabox_content'],
                $post_type,
                'normal',
                'high'
            );
        }
    }
    
    /**
     * Render the SEO meta fields metabox
     * 
     * @param WP_Post $post Current post object
     * @since 2.1.0
     */
    public static function meta_fields_metabox_content($post) {
        // Add nonce for security
        wp_nonce_field('smart_seo_meta_nonce', 'smart_seo_meta_nonce_field');
        
        // Get current values
        $meta_title = get_post_meta($post->ID, '_smart_seo_title', true);
        $meta_description = get_post_meta($post->ID, '_smart_seo_description', true);
        $meta_keywords = get_post_meta($post->ID, '_smart_seo_keywords', true);
        $canonical_url = get_post_meta($post->ID, '_smart_seo_canonical', true);
        $robots_meta = get_post_meta($post->ID, '_smart_seo_robots', true);
        
        // OG Tags
        $og_title = get_post_meta($post->ID, '_smart_seo_og_title', true);
        $og_description = get_post_meta($post->ID, '_smart_seo_og_description', true);
        $og_image = get_post_meta($post->ID, '_smart_seo_og_image', true);
        $og_type = get_post_meta($post->ID, '_smart_seo_og_type', true) ?: 'article';
        
        // Twitter Cards
        $twitter_card = get_post_meta($post->ID, '_smart_seo_twitter_card', true) ?: 'summary_large_image';
        $twitter_title = get_post_meta($post->ID, '_smart_seo_twitter_title', true);
        $twitter_description = get_post_meta($post->ID, '_smart_seo_twitter_description', true);
        $twitter_image = get_post_meta($post->ID, '_smart_seo_twitter_image', true);
        
        // Schema markup
        $schema_type = get_post_meta($post->ID, '_smart_seo_schema_type', true) ?: 'Article';
        
        // Focus keyword
        $focus_keyword = get_post_meta($post->ID, '_smart_seo_focus_keyword', true);
        
        // Defaults
        if (empty($meta_title)) $meta_title = $post->post_title;
        if (empty($meta_description)) $meta_description = $post->post_excerpt;
        if (empty($og_title)) $og_title = $meta_title;
        if (empty($og_description)) $og_description = $meta_description;
        if (empty($twitter_title)) $twitter_title = $meta_title;
        if (empty($twitter_description)) $twitter_description = $meta_description;
        if (empty($canonical_url)) $canonical_url = get_permalink($post->ID);
        
        ?>
        <div class="smart-seo-meta-fields">
            <style>
                .smart-seo-meta-fields { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
                .seo-tab-nav { display: flex; border-bottom: 1px solid #ddd; margin-bottom: 20px; }
                .seo-tab-nav button { background: none; border: none; padding: 12px 20px; cursor: pointer; border-bottom: 2px solid transparent; font-weight: 500; }
                .seo-tab-nav button.active { border-bottom-color: #0073aa; color: #0073aa; }
                .seo-tab-nav button:hover { background: #f6f7f7; }
                .seo-tab-content { display: none; }
                .seo-tab-content.active { display: block; }
                .seo-field-group { margin-bottom: 20px; }
                .seo-field-group label { display: block; font-weight: 600; margin-bottom: 5px; color: #1d2327; }
                .seo-field-group input, .seo-field-group textarea, .seo-field-group select { width: 100%; padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px; }
                .seo-field-group textarea { resize: vertical; min-height: 80px; }
                .seo-field-help { font-size: 12px; color: #646970; margin-top: 5px; }
                .seo-field-counter { font-size: 11px; text-align: right; margin-top: 2px; }
                .seo-field-counter.good { color: #00a32a; }
                .seo-field-counter.warning { color: #dba617; }
                .seo-field-counter.error { color: #d63638; }
                .seo-preview-box { background: #f6f7f7; border: 1px solid #c3c4c7; border-radius: 4px; padding: 15px; margin-top: 10px; }
                .seo-preview-title { color: #1a0dab; font-size: 18px; font-weight: normal; margin: 0 0 5px 0; text-decoration: none; }
                .seo-preview-url { color: #006621; font-size: 14px; margin: 0 0 5px 0; }
                .seo-preview-description { color: #4d5156; font-size: 14px; line-height: 1.4; margin: 0; }
                .seo-social-preview { display: flex; gap: 15px; margin-top: 15px; }
                .seo-social-preview .preview-card { flex: 1; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; background: white; }
                .seo-social-preview .preview-image { width: 100%; height: 120px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #666; font-size: 12px; }
                .seo-social-preview .preview-content { padding: 12px; }
                .seo-social-preview .preview-title { font-weight: 600; font-size: 14px; margin: 0 0 5px 0; }
                .seo-social-preview .preview-desc { font-size: 12px; color: #666; margin: 0; line-height: 1.3; }
                .seo-score-indicator { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; margin-left: 10px; }
                .seo-score-good { background: #d1e7dd; color: #0a3622; }
                .seo-score-warning { background: #fff3cd; color: #664d03; }
                .seo-score-error { background: #f8d7da; color: #58151c; }
                .seo-keyword-analysis { background: #f8f9fa; border-radius: 4px; padding: 12px; margin-top: 10px; }
                .seo-keyword-density { font-size: 12px; margin-top: 5px; }
                .seo-image-upload { display: flex; align-items: center; gap: 10px; }
                .seo-image-preview { width: 100px; height: 60px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #666; }
                .seo-image-preview img { max-width: 100%; max-height: 100%; object-fit: cover; border-radius: 3px; }
            </style>
            
            <!-- Tab Navigation -->
            <div class="seo-tab-nav">
                <button type="button" class="seo-tab-btn active" data-tab="basic">📝 Basic SEO</button>
                <button type="button" class="seo-tab-btn" data-tab="social">📱 Social Media</button>
                <button type="button" class="seo-tab-btn" data-tab="advanced">⚙️ Advanced</button>
                <button type="button" class="seo-tab-btn" data-tab="analysis">📊 Analysis</button>
            </div>
            
            <!-- Basic SEO Tab -->
            <div class="seo-tab-content active" id="basic-tab">
                <div class="seo-field-group">
                    <label for="smart_seo_focus_keyword">🎯 Focus Keyword</label>
                    <input type="text" id="smart_seo_focus_keyword" name="smart_seo_focus_keyword" value="<?php echo esc_attr($focus_keyword); ?>" placeholder="Enter your target keyword">
                    <div class="seo-field-help">The main keyword you want this content to rank for.</div>
                    <div id="keyword-analysis" class="seo-keyword-analysis" style="display: none;">
                        <div id="keyword-density"></div>
                    </div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_title">📄 SEO Title</label>
                    <input type="text" id="smart_seo_title" name="smart_seo_title" value="<?php echo esc_attr($meta_title); ?>" placeholder="Enter SEO title">
                    <div class="seo-field-counter" id="title-counter">0 characters (30-60 optimal)</div>
                    <div class="seo-field-help">This title will appear in search engine results. Keep it between 30-60 characters.</div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_description">📝 Meta Description</label>
                    <textarea id="smart_seo_description" name="smart_seo_description" placeholder="Enter meta description"><?php echo esc_textarea($meta_description); ?></textarea>
                    <div class="seo-field-counter" id="description-counter">0 characters (120-160 optimal)</div>
                    <div class="seo-field-help">A brief description that appears in search results. Keep it between 120-160 characters.</div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_keywords">🏷️ Meta Keywords</label>
                    <input type="text" id="smart_seo_keywords" name="smart_seo_keywords" value="<?php echo esc_attr($meta_keywords); ?>" placeholder="keyword1, keyword2, keyword3">
                    <div class="seo-field-help">Comma-separated keywords related to your content. Limited SEO value but can be useful for internal organization.</div>
                </div>
                
                <!-- Search Preview -->
                <div class="seo-preview-box">
                    <h4 style="margin: 0 0 10px 0;">🔍 Search Engine Preview</h4>
                    <div id="search-preview">
                        <div class="seo-preview-title" id="preview-title"><?php echo esc_html($meta_title); ?></div>
                        <div class="seo-preview-url" id="preview-url"><?php echo esc_url(get_permalink($post->ID)); ?></div>
                        <div class="seo-preview-description" id="preview-description"><?php echo esc_html($meta_description); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Social Media Tab -->
            <div class="seo-tab-content" id="social-tab">
                <h4>📘 Open Graph (Facebook, LinkedIn)</h4>
                
                <div class="seo-field-group">
                    <label for="smart_seo_og_title">📄 OG Title</label>
                    <input type="text" id="smart_seo_og_title" name="smart_seo_og_title" value="<?php echo esc_attr($og_title); ?>" placeholder="Open Graph title">
                    <div class="seo-field-counter" id="og-title-counter">0 characters (40-60 optimal)</div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_og_description">📝 OG Description</label>
                    <textarea id="smart_seo_og_description" name="smart_seo_og_description" placeholder="Open Graph description"><?php echo esc_textarea($og_description); ?></textarea>
                    <div class="seo-field-counter" id="og-description-counter">0 characters (130-160 optimal)</div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_og_image">🖼️ OG Image</label>
                    <div class="seo-image-upload">
                        <input type="url" id="smart_seo_og_image" name="smart_seo_og_image" value="<?php echo esc_url($og_image); ?>" placeholder="Image URL">
                        <button type="button" class="button" id="upload-og-image">Upload Image</button>
                    </div>
                    <div class="seo-image-preview" id="og-image-preview">
                        <?php if ($og_image): ?>
                            <img src="<?php echo esc_url($og_image); ?>" alt="OG Image Preview">
                        <?php else: ?>
                            No image selected
                        <?php endif; ?>
                    </div>
                    <div class="seo-field-help">Recommended: 1200x630 pixels. Will be used when sharing on Facebook, LinkedIn, etc.</div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_og_type">📋 OG Type</label>
                    <select id="smart_seo_og_type" name="smart_seo_og_type">
                        <option value="article" <?php selected($og_type, 'article'); ?>>Article</option>
                        <option value="website" <?php selected($og_type, 'website'); ?>>Website</option>
                        <option value="product" <?php selected($og_type, 'product'); ?>>Product</option>
                        <option value="video" <?php selected($og_type, 'video'); ?>>Video</option>
                        <option value="book" <?php selected($og_type, 'book'); ?>>Book</option>
                    </select>
                </div>
                
                <h4 style="margin-top: 30px;">🐦 Twitter Cards</h4>
                
                <div class="seo-field-group">
                    <label for="smart_seo_twitter_card">📋 Card Type</label>
                    <select id="smart_seo_twitter_card" name="smart_seo_twitter_card">
                        <option value="summary" <?php selected($twitter_card, 'summary'); ?>>Summary</option>
                        <option value="summary_large_image" <?php selected($twitter_card, 'summary_large_image'); ?>>Summary Large Image</option>
                        <option value="app" <?php selected($twitter_card, 'app'); ?>>App</option>
                        <option value="player" <?php selected($twitter_card, 'player'); ?>>Player</option>
                    </select>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_twitter_title">📄 Twitter Title</label>
                    <input type="text" id="smart_seo_twitter_title" name="smart_seo_twitter_title" value="<?php echo esc_attr($twitter_title); ?>" placeholder="Twitter title">
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_twitter_description">📝 Twitter Description</label>
                    <textarea id="smart_seo_twitter_description" name="smart_seo_twitter_description" placeholder="Twitter description"><?php echo esc_textarea($twitter_description); ?></textarea>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_twitter_image">🖼️ Twitter Image</label>
                    <div class="seo-image-upload">
                        <input type="url" id="smart_seo_twitter_image" name="smart_seo_twitter_image" value="<?php echo esc_url($twitter_image); ?>" placeholder="Image URL">
                        <button type="button" class="button" id="upload-twitter-image">Upload Image</button>
                    </div>
                    <div class="seo-image-preview" id="twitter-image-preview">
                        <?php if ($twitter_image): ?>
                            <img src="<?php echo esc_url($twitter_image); ?>" alt="Twitter Image Preview">
                        <?php else: ?>
                            No image selected
                        <?php endif; ?>
                    </div>
                    <div class="seo-field-help">Recommended: 1200x675 pixels for large image cards.</div>
                </div>
                
                <!-- Social Preview -->
                <div class="seo-preview-box">
                    <h4 style="margin: 0 0 10px 0;">📱 Social Media Preview</h4>
                    <div class="seo-social-preview">
                        <div class="preview-card">
                            <div class="preview-image" id="facebook-preview-image">
                                <?php if ($og_image): ?>
                                    <img src="<?php echo esc_url($og_image); ?>" alt="Facebook Preview" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    No image
                                <?php endif; ?>
                            </div>
                            <div class="preview-content">
                                <div class="preview-title" id="facebook-preview-title"><?php echo esc_html($og_title); ?></div>
                                <div class="preview-desc" id="facebook-preview-desc"><?php echo esc_html($og_description); ?></div>
                            </div>
                        </div>
                        <div class="preview-card">
                            <div class="preview-image" id="twitter-preview-image">
                                <?php if ($twitter_image): ?>
                                    <img src="<?php echo esc_url($twitter_image); ?>" alt="Twitter Preview" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    No image
                                <?php endif; ?>
                            </div>
                            <div class="preview-content">
                                <div class="preview-title" id="twitter-preview-title"><?php echo esc_html($twitter_title); ?></div>
                                <div class="preview-desc" id="twitter-preview-desc"><?php echo esc_html($twitter_description); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Advanced Tab -->
            <div class="seo-tab-content" id="advanced-tab">
                <div class="seo-field-group">
                    <label for="smart_seo_canonical">🔗 Canonical URL</label>
                    <input type="url" id="smart_seo_canonical" name="smart_seo_canonical" value="<?php echo esc_url($canonical_url); ?>" placeholder="https://example.com/page">
                    <div class="seo-field-help">The preferred URL for this content. Helps prevent duplicate content issues.</div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_robots">🤖 Robots Meta</label>
                    <select id="smart_seo_robots" name="smart_seo_robots">
                        <option value="index,follow" <?php selected($robots_meta, 'index,follow'); ?>>Index, Follow (Default)</option>
                        <option value="noindex,follow" <?php selected($robots_meta, 'noindex,follow'); ?>>No Index, Follow</option>
                        <option value="index,nofollow" <?php selected($robots_meta, 'index,nofollow'); ?>>Index, No Follow</option>
                        <option value="noindex,nofollow" <?php selected($robots_meta, 'noindex,nofollow'); ?>>No Index, No Follow</option>
                        <option value="noarchive" <?php selected($robots_meta, 'noarchive'); ?>>No Archive</option>
                        <option value="nosnippet" <?php selected($robots_meta, 'nosnippet'); ?>>No Snippet</option>
                    </select>
                    <div class="seo-field-help">Controls how search engines crawl and index this page.</div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_schema_type">📋 Schema Type</label>
                    <select id="smart_seo_schema_type" name="smart_seo_schema_type">
                        <option value="Article" <?php selected($schema_type, 'Article'); ?>>Article</option>
                        <option value="BlogPosting" <?php selected($schema_type, 'BlogPosting'); ?>>Blog Posting</option>
                        <option value="NewsArticle" <?php selected($schema_type, 'NewsArticle'); ?>>News Article</option>
                        <option value="WebPage" <?php selected($schema_type, 'WebPage'); ?>>Web Page</option>
                        <option value="Product" <?php selected($schema_type, 'Product'); ?>>Product</option>
                        <option value="Service" <?php selected($schema_type, 'Service'); ?>>Service</option>
                        <option value="Organization" <?php selected($schema_type, 'Organization'); ?>>Organization</option>
                        <option value="Person" <?php selected($schema_type, 'Person'); ?>>Person</option>
                        <option value="LocalBusiness" <?php selected($schema_type, 'LocalBusiness'); ?>>Local Business</option>
                        <option value="Recipe" <?php selected($schema_type, 'Recipe'); ?>>Recipe</option>
                        <option value="Review" <?php selected($schema_type, 'Review'); ?>>Review</option>
                        <option value="Event" <?php selected($schema_type, 'Event'); ?>>Event</option>
                        <option value="FAQ" <?php selected($schema_type, 'FAQ'); ?>>FAQ</option>
                        <option value="HowTo" <?php selected($schema_type, 'HowTo'); ?>>How-To</option>
                    </select>
                    <div class="seo-field-help">Schema markup type for structured data.</div>
                </div>
            </div>
            
            <!-- Analysis Tab -->
            <div class="seo-tab-content" id="analysis-tab">
                <div id="seo-analysis-content">
                    <p>Loading SEO analysis...</p>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Save SEO meta fields
     * 
     * @param int $post_id Post ID
     * @since 2.1.0
     */
    public static function save_seo_meta_fields($post_id) {
        // Check nonce: unslash and sanitize before verify
        if (!isset($_POST['smart_seo_meta_nonce_field'])) {
            return;
        }
        $nonce = sanitize_text_field( wp_unslash( $_POST['smart_seo_meta_nonce_field'] ) );
        if ( ! wp_verify_nonce( $nonce, 'smart_seo_meta_nonce' ) ) {
            return;
        }
        
        // Check if user can edit post
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Check for autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // List of meta fields to save
        $meta_fields = [
            'smart_seo_title',
            'smart_seo_description', 
            'smart_seo_keywords',
            'smart_seo_canonical',
            'smart_seo_robots',
            'smart_seo_og_title',
            'smart_seo_og_description',
            'smart_seo_og_image',
            'smart_seo_og_type',
            'smart_seo_twitter_card',
            'smart_seo_twitter_title',
            'smart_seo_twitter_description',
            'smart_seo_twitter_image',
            'smart_seo_schema_type',
            'smart_seo_focus_keyword'
        ];
        
        // Save each field
        foreach ($meta_fields as $field) {
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Access checked via isset; value is unslashed and fully sanitized before use
            if (isset($_POST[$field])) {
                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Value is immediately sanitized into $value based on field type
                $raw = wp_unslash( $_POST[$field] );
                $value = sanitize_text_field( $raw );
                
                // Special handling for textarea fields
                if (in_array($field, ['smart_seo_description', 'smart_seo_og_description', 'smart_seo_twitter_description'])) {
                    $value = sanitize_textarea_field( $raw );
                }
                
                // Special handling for URL fields
                if (in_array($field, ['smart_seo_canonical', 'smart_seo_og_image', 'smart_seo_twitter_image'])) {
                    $value = esc_url_raw( $raw );
                }
                
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }
    
    /**
     * Enqueue scripts and styles for meta fields
     * 
     * @param string $hook Current admin page hook
     * @since 2.1.0
     */
    public static function enqueue_meta_scripts($hook) {
        if (!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }
        
        wp_enqueue_media(); // For image uploads
        wp_enqueue_script('jquery');
        
        $script = "
        jQuery(document).ready(function($) {
            // Tab switching
            $('.seo-tab-btn').click(function() {
                var tab = $(this).data('tab');
                $('.seo-tab-btn').removeClass('active');
                $('.seo-tab-content').removeClass('active');
                $(this).addClass('active');
                $('#' + tab + '-tab').addClass('active');
                
                // Load analysis tab content when clicked
                if (tab === 'analysis') {
                    loadSeoAnalysis();
                }
            });
            
            // Character counters
            function updateCounter(inputId, counterId, min, max) {
                var input = $('#' + inputId);
                var counter = $('#' + counterId);
                var length = input.val().length;
                
                counter.text(length + ' characters (' + min + '-' + max + ' optimal)');
                
                if (length >= min && length <= max) {
                    counter.removeClass('warning error').addClass('good');
                } else if (length >= min * 0.8 && length <= max * 1.2) {
                    counter.removeClass('good error').addClass('warning');
                } else {
                    counter.removeClass('good warning').addClass('error');
                }
            }
            
            // Setup counters
            $('#smart_seo_title').on('input', function() {
                updateCounter('smart_seo_title', 'title-counter', 30, 60);
                updatePreview();
            });
            
            $('#smart_seo_description').on('input', function() {
                updateCounter('smart_seo_description', 'description-counter', 120, 160);
                updatePreview();
            });
            
            $('#smart_seo_og_title').on('input', function() {
                updateCounter('smart_seo_og_title', 'og-title-counter', 40, 60);
                updateSocialPreview();
            });
            
            $('#smart_seo_og_description').on('input', function() {
                updateCounter('smart_seo_og_description', 'og-description-counter', 130, 160);
                updateSocialPreview();
            });
            
            // Initial counter updates
            updateCounter('smart_seo_title', 'title-counter', 30, 60);
            updateCounter('smart_seo_description', 'description-counter', 120, 160);
            updateCounter('smart_seo_og_title', 'og-title-counter', 40, 60);
            updateCounter('smart_seo_og_description', 'og-description-counter', 130, 160);
            
            // Update search preview
            function updatePreview() {
                var title = $('#smart_seo_title').val() || 'Your SEO Title';
                var description = $('#smart_seo_description').val() || 'Your meta description will appear here...';
                
                $('#preview-title').text(title);
                $('#preview-description').text(description);
            }
            
            // Update social preview
            function updateSocialPreview() {
                var ogTitle = $('#smart_seo_og_title').val() || 'Your OG Title';
                var ogDescription = $('#smart_seo_og_description').val() || 'Your OG description...';
                var twitterTitle = $('#smart_seo_twitter_title').val() || ogTitle;
                var twitterDescription = $('#smart_seo_twitter_description').val() || ogDescription;
                
                $('#facebook-preview-title').text(ogTitle);
                $('#facebook-preview-desc').text(ogDescription);
                $('#twitter-preview-title').text(twitterTitle);
                $('#twitter-preview-desc').text(twitterDescription);
            }
            
            // Image upload handlers
            var mediaUploader;
            
            $('#upload-og-image').click(function(e) {
                e.preventDefault();
                
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                mediaUploader = wp.media({
                    title: 'Choose OG Image',
                    button: { text: 'Choose Image' },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#smart_seo_og_image').val(attachment.url);
                    $('#og-image-preview').html('<img src=\"' + attachment.url + '\" alt=\"OG Image Preview\">');
                    updateSocialPreview();
                });
                
                mediaUploader.open();
            });
            
            $('#upload-twitter-image').click(function(e) {
                e.preventDefault();
                
                mediaUploader = wp.media({
                    title: 'Choose Twitter Image',
                    button: { text: 'Choose Image' },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#smart_seo_twitter_image').val(attachment.url);
                    $('#twitter-image-preview').html('<img src=\"' + attachment.url + '\" alt=\"Twitter Image Preview\">');
                    updateSocialPreview();
                });
                
                mediaUploader.open();
            });
            
            // Focus keyword analysis
            $('#smart_seo_focus_keyword').on('input', function() {
                var keyword = $(this).val();
                if (keyword.length > 2) {
                    analyzeKeyword(keyword);
                } else {
                    $('#keyword-analysis').hide();
                }
            });
            
            function analyzeKeyword(keyword) {
                // Get post content
                var content = '';
                if (typeof wp !== 'undefined' && wp.data && wp.data.select('core/editor')) {
                    content = wp.data.select('core/editor').getEditedPostContent();
                } else {
                    content = $('#content').val() || '';
                }
                
                var title = $('#smart_seo_title').val() || $('#title').val() || '';
                var description = $('#smart_seo_description').val() || '';
                
                // Calculate keyword density
                var keywordRegex = new RegExp(keyword.toLowerCase(), 'gi');
                var contentLower = content.toLowerCase();
                var titleLower = title.toLowerCase();
                var descLower = description.toLowerCase();
                
                var contentMatches = (contentLower.match(keywordRegex) || []).length;
                var titleMatches = (titleLower.match(keywordRegex) || []).length;
                var descMatches = (descLower.match(keywordRegex) || []).length;
                
                var wordCount = content.split(/\s+/).length;
                var density = wordCount > 0 ? ((contentMatches / wordCount) * 100).toFixed(2) : 0;
                
                var analysis = '<strong>Keyword Analysis:</strong><br>';
                analysis += 'Content: ' + contentMatches + ' times ';
                analysis += '<span class=\"seo-score-indicator ' + (contentMatches >= 1 ? 'seo-score-good' : 'seo-score-error') + '\">' + (contentMatches >= 1 ? 'Good' : 'Add more') + '</span><br>';
                analysis += 'Title: ' + titleMatches + ' times ';
                analysis += '<span class=\"seo-score-indicator ' + (titleMatches >= 1 ? 'seo-score-good' : 'seo-score-warning') + '\">' + (titleMatches >= 1 ? 'Good' : 'Consider adding') + '</span><br>';
                analysis += 'Description: ' + descMatches + ' times ';
                analysis += '<span class=\"seo-score-indicator ' + (descMatches >= 1 ? 'seo-score-good' : 'seo-score-warning') + '\">' + (descMatches >= 1 ? 'Good' : 'Consider adding') + '</span><br>';
                analysis += 'Density: ' + density + '% ';
                analysis += '<span class=\"seo-score-indicator ' + (density >= 0.5 && density <= 2.5 ? 'seo-score-good' : 'seo-score-warning') + '\">' + (density >= 0.5 && density <= 2.5 ? 'Optimal' : 'Adjust') + '</span>';
                
                $('#keyword-density').html(analysis);
                $('#keyword-analysis').show();
            }
            
            function loadSeoAnalysis() {
                var postId = $('#post_ID').val();
                if (!postId) return;
                
                $('#seo-analysis-content').html('<p>Loading comprehensive SEO analysis...</p>');
                
                $.post(ajaxurl, {
                    action: 'get_full_seo_report',
                    post_id: postId,
                    nonce: '" . wp_create_nonce('smart_seo_nonce') . "'
                }, function(response) {
                    if (response.success) {
                        $('#seo-analysis-content').html(response.data.html);
                    } else {
                        $('#seo-analysis-content').html('<p>Error loading analysis.</p>');
                    }
                });
            }
            
            // Update all fields when switching between visual/text editor
            $('#content-tmce, #content-html').click(function() {
                setTimeout(updatePreview, 500);
            });
        });
        ";
        
        wp_add_inline_script('jquery', $script);
    }
    
    /**
     * Output custom meta tags in head
     * 
     * @since 2.1.0
     */
    public static function output_custom_meta_tags() {
        if (is_admin() || !is_singular()) return;
        
        global $post;
        if (!$post) return;
        
        // Get meta values
        $meta_title = get_post_meta($post->ID, '_smart_seo_title', true);
        $meta_description = get_post_meta($post->ID, '_smart_seo_description', true);
        $meta_keywords = get_post_meta($post->ID, '_smart_seo_keywords', true);
        $canonical_url = get_post_meta($post->ID, '_smart_seo_canonical', true);
        $robots_meta = get_post_meta($post->ID, '_smart_seo_robots', true);
        
        // OG Tags
        $og_title = get_post_meta($post->ID, '_smart_seo_og_title', true);
        $og_description = get_post_meta($post->ID, '_smart_seo_og_description', true);
        $og_image = get_post_meta($post->ID, '_smart_seo_og_image', true);
        $og_type = get_post_meta($post->ID, '_smart_seo_og_type', true);
        
        // Twitter Cards
        $twitter_card = get_post_meta($post->ID, '_smart_seo_twitter_card', true);
        $twitter_title = get_post_meta($post->ID, '_smart_seo_twitter_title', true);
        $twitter_description = get_post_meta($post->ID, '_smart_seo_twitter_description', true);
        $twitter_image = get_post_meta($post->ID, '_smart_seo_twitter_image', true);
        
        // Output meta description
        if ($meta_description) {
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
        }
        
        // Output meta keywords
        if ($meta_keywords) {
            echo '<meta name="keywords" content="' . esc_attr($meta_keywords) . '">' . "\n";
        }
        
        // Output robots meta
        if ($robots_meta) {
            echo '<meta name="robots" content="' . esc_attr($robots_meta) . '">' . "\n";
        }
        
        // Output canonical URL
        if ($canonical_url) {
            echo '<link rel="canonical" href="' . esc_url($canonical_url) . '">' . "\n";
        }
        
        // Output Open Graph tags
        if ($og_title) {
            echo '<meta property="og:title" content="' . esc_attr($og_title) . '">' . "\n";
        }
        if ($og_description) {
            echo '<meta property="og:description" content="' . esc_attr($og_description) . '">' . "\n";
        }
        if ($og_image) {
            echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
        }
        if ($og_type) {
            echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";
        }
        
        // Always output these OG tags
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
        
        // Output Twitter Card tags
        if ($twitter_card) {
            echo '<meta name="twitter:card" content="' . esc_attr($twitter_card) . '">' . "\n";
        }
        if ($twitter_title) {
            echo '<meta name="twitter:title" content="' . esc_attr($twitter_title) . '">' . "\n";
        }
        if ($twitter_description) {
            echo '<meta name="twitter:description" content="' . esc_attr($twitter_description) . '">' . "\n";
        }
        if ($twitter_image) {
            echo '<meta name="twitter:image" content="' . esc_url($twitter_image) . '">' . "\n";
        }
    }
    
    /**
     * Register meta fields for REST API (Block Editor support)
     * 
     * @since 2.1.0
     */
    public static function register_meta_fields_for_rest() {
        $meta_fields = [
            'smart_seo_title',
            'smart_seo_description',
            'smart_seo_keywords',
            'smart_seo_canonical',
            'smart_seo_robots',
            'smart_seo_og_title',
            'smart_seo_og_description',
            'smart_seo_og_image',
            'smart_seo_og_type',
            'smart_seo_twitter_card',
            'smart_seo_twitter_title',
            'smart_seo_twitter_description',
            'smart_seo_twitter_image',
            'smart_seo_schema_type',
            'smart_seo_focus_keyword'
        ];
        
        foreach ($meta_fields as $field) {
            register_post_meta('post', '_' . $field, [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
                'auth_callback' => function() {
                    return current_user_can('edit_posts');
                }
            ]);
            
            register_post_meta('page', '_' . $field, [
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
                'auth_callback' => function() {
                    return current_user_can('edit_pages');
                }
            ]);
        }
    }
}
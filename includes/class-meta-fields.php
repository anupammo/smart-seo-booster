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
        // Add meta boxes (classic editor only — the block editor uses the sidebar).
        add_action('add_meta_boxes', [__CLASS__, 'add_seo_meta_boxes']);

        // Save meta fields
        add_action('save_post', [__CLASS__, 'save_seo_meta_fields']);

        // Enqueue scripts and styles for the classic metabox.
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_meta_scripts']);

        // Native block-editor sidebar.
        add_action('enqueue_block_editor_assets', [__CLASS__, 'enqueue_block_sidebar']);

        // Front-end meta output is handled centrally by Smart_SEO_Core to avoid
        // duplicate tags. This class only manages the admin editing UI + REST.

        // Add meta fields to REST API for block editor
        add_action('init', [__CLASS__, 'register_meta_fields_for_rest']);
    }

    /**
     * Whether the current admin screen uses the block editor.
     *
     * @return bool
     */
    private static function is_block_editor() {
        if ( ! function_exists('get_current_screen') ) {
            return false;
        }
        $screen = get_current_screen();
        return $screen && method_exists($screen, 'is_block_editor') && $screen->is_block_editor();
    }

    /**
     * Enqueue the Gutenberg SEO sidebar.
     *
     * @since 2.4.0
     */
    public static function enqueue_block_sidebar() {
        $ver = defined('SMART_SEO_BOOSTER_VERSION') ? SMART_SEO_BOOSTER_VERSION : false;
        wp_enqueue_script(
            'smart-seo-block-editor',
            plugin_dir_url(__FILE__) . '../js/block-editor.js',
            ['wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-i18n'],
            $ver,
            true
        );
        if ( function_exists('wp_set_script_translations') ) {
            wp_set_script_translations('smart-seo-block-editor', 'smart-seo-booster');
        }
    }

    /**
     * Add SEO meta boxes to post/page edit screens
     *
     * @since 2.1.0
     */
    public static function add_seo_meta_boxes() {
        // On the block editor the native sidebar replaces this metabox.
        if ( self::is_block_editor() ) {
            return;
        }

        $post_types = ['post', 'page'];

        foreach ($post_types as $post_type) {
            add_meta_box(
                'smart_seo_meta_fields',
                '<span class="dashicons dashicons-marker" aria-hidden="true"></span> SEO Meta Tags & Social Preview',
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
            
            <!-- Tab Navigation -->
            <div class="seo-tab-nav" role="tablist" aria-label="<?php esc_attr_e( 'SEO settings', 'smart-seo-booster' ); ?>">
                <button type="button" class="seo-tab-btn active" data-tab="basic" role="tab" id="seo-tab-basic" aria-controls="basic-tab" aria-selected="true" tabindex="0"><span class="dashicons dashicons-edit" aria-hidden="true"></span> <?php esc_html_e( 'Basic SEO', 'smart-seo-booster' ); ?></button>
                <button type="button" class="seo-tab-btn" data-tab="social" role="tab" id="seo-tab-social" aria-controls="social-tab" aria-selected="false" tabindex="-1"><span class="dashicons dashicons-smartphone" aria-hidden="true"></span> <?php esc_html_e( 'Social Media', 'smart-seo-booster' ); ?></button>
                <button type="button" class="seo-tab-btn" data-tab="advanced" role="tab" id="seo-tab-advanced" aria-controls="advanced-tab" aria-selected="false" tabindex="-1"><span class="dashicons dashicons-admin-generic" aria-hidden="true"></span> <?php esc_html_e( 'Advanced', 'smart-seo-booster' ); ?></button>
                <button type="button" class="seo-tab-btn" data-tab="analysis" role="tab" id="seo-tab-analysis" aria-controls="analysis-tab" aria-selected="false" tabindex="-1"><span class="dashicons dashicons-chart-bar" aria-hidden="true"></span> <?php esc_html_e( 'Analysis', 'smart-seo-booster' ); ?></button>
            </div>
            
            <!-- Basic SEO Tab -->
            <div class="seo-tab-content active" id="basic-tab" role="tabpanel" aria-labelledby="seo-tab-basic">
                <div class="seo-field-group">
                    <label for="smart_seo_focus_keyword"><span class="dashicons dashicons-marker" aria-hidden="true"></span> Focus Keyword</label>
                    <input type="text" id="smart_seo_focus_keyword" name="smart_seo_focus_keyword" value="<?php echo esc_attr($focus_keyword); ?>" placeholder="Enter your target keyword">
                    <div class="seo-field-help">The main keyword you want this content to rank for.</div>
                    <div id="keyword-analysis" class="seo-keyword-analysis" style="display: none;">
                        <div id="keyword-density"></div>
                    </div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_title"><span class="dashicons dashicons-media-text" aria-hidden="true"></span> SEO Title</label>
                    <input type="text" id="smart_seo_title" name="smart_seo_title" value="<?php echo esc_attr($meta_title); ?>" placeholder="Enter SEO title">
                    <div class="seo-field-counter" id="title-counter">0 characters (30-60 optimal)</div>
                    <div class="seo-field-help">This title will appear in search engine results. Keep it between 30-60 characters.</div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_description"><span class="dashicons dashicons-edit" aria-hidden="true"></span> Meta Description</label>
                    <textarea id="smart_seo_description" name="smart_seo_description" placeholder="Enter meta description"><?php echo esc_textarea($meta_description); ?></textarea>
                    <div class="seo-field-counter" id="description-counter">0 characters (120-160 optimal)</div>
                    <div class="seo-field-help">A brief description that appears in search results. Keep it between 120-160 characters.</div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_keywords"><span class="dashicons dashicons-tag" aria-hidden="true"></span> Meta Keywords</label>
                    <input type="text" id="smart_seo_keywords" name="smart_seo_keywords" value="<?php echo esc_attr($meta_keywords); ?>" placeholder="keyword1, keyword2, keyword3">
                    <div class="seo-field-help">Comma-separated keywords related to your content. Limited SEO value but can be useful for internal organization.</div>
                </div>
                
                <!-- Search Preview -->
                <div class="seo-preview-box">
                    <h4 style="margin: 0 0 10px 0;"><span class="dashicons dashicons-search" aria-hidden="true"></span> Search Engine Preview</h4>
                    <div id="search-preview">
                        <div class="seo-preview-title" id="preview-title"><?php echo esc_html($meta_title); ?></div>
                        <div class="seo-preview-url" id="preview-url"><?php echo esc_url(get_permalink($post->ID)); ?></div>
                        <div class="seo-preview-description" id="preview-description"><?php echo esc_html($meta_description); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Social Media Tab -->
            <div class="seo-tab-content" id="social-tab" role="tabpanel" aria-labelledby="seo-tab-social" hidden>
                <h4><span class="dashicons dashicons-facebook" aria-hidden="true"></span> Open Graph (Facebook, LinkedIn)</h4>
                
                <div class="seo-field-group">
                    <label for="smart_seo_og_title"><span class="dashicons dashicons-media-text" aria-hidden="true"></span> OG Title</label>
                    <input type="text" id="smart_seo_og_title" name="smart_seo_og_title" value="<?php echo esc_attr($og_title); ?>" placeholder="Open Graph title">
                    <div class="seo-field-counter" id="og-title-counter">0 characters (40-60 optimal)</div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_og_description"><span class="dashicons dashicons-edit" aria-hidden="true"></span> OG Description</label>
                    <textarea id="smart_seo_og_description" name="smart_seo_og_description" placeholder="Open Graph description"><?php echo esc_textarea($og_description); ?></textarea>
                    <div class="seo-field-counter" id="og-description-counter">0 characters (130-160 optimal)</div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_og_image"><span class="dashicons dashicons-format-image" aria-hidden="true"></span> OG Image</label>
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
                    <label for="smart_seo_og_type"><span class="dashicons dashicons-clipboard" aria-hidden="true"></span> OG Type</label>
                    <select id="smart_seo_og_type" name="smart_seo_og_type">
                        <option value="article" <?php selected($og_type, 'article'); ?>>Article</option>
                        <option value="website" <?php selected($og_type, 'website'); ?>>Website</option>
                        <option value="product" <?php selected($og_type, 'product'); ?>>Product</option>
                        <option value="video" <?php selected($og_type, 'video'); ?>>Video</option>
                        <option value="book" <?php selected($og_type, 'book'); ?>>Book</option>
                    </select>
                </div>
                
                <h4 style="margin-top: 30px;"><span class="dashicons dashicons-twitter" aria-hidden="true"></span> Twitter Cards</h4>
                
                <div class="seo-field-group">
                    <label for="smart_seo_twitter_card"><span class="dashicons dashicons-clipboard" aria-hidden="true"></span> Card Type</label>
                    <select id="smart_seo_twitter_card" name="smart_seo_twitter_card">
                        <option value="summary" <?php selected($twitter_card, 'summary'); ?>>Summary</option>
                        <option value="summary_large_image" <?php selected($twitter_card, 'summary_large_image'); ?>>Summary Large Image</option>
                        <option value="app" <?php selected($twitter_card, 'app'); ?>>App</option>
                        <option value="player" <?php selected($twitter_card, 'player'); ?>>Player</option>
                    </select>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_twitter_title"><span class="dashicons dashicons-media-text" aria-hidden="true"></span> Twitter Title</label>
                    <input type="text" id="smart_seo_twitter_title" name="smart_seo_twitter_title" value="<?php echo esc_attr($twitter_title); ?>" placeholder="Twitter title">
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_twitter_description"><span class="dashicons dashicons-edit" aria-hidden="true"></span> Twitter Description</label>
                    <textarea id="smart_seo_twitter_description" name="smart_seo_twitter_description" placeholder="Twitter description"><?php echo esc_textarea($twitter_description); ?></textarea>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_twitter_image"><span class="dashicons dashicons-format-image" aria-hidden="true"></span> Twitter Image</label>
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
                    <h4 style="margin: 0 0 10px 0;"><span class="dashicons dashicons-smartphone" aria-hidden="true"></span> Social Media Preview</h4>
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
            <div class="seo-tab-content" id="advanced-tab" role="tabpanel" aria-labelledby="seo-tab-advanced" hidden>
                <div class="seo-field-group">
                    <label for="smart_seo_canonical"><span class="dashicons dashicons-admin-links" aria-hidden="true"></span> Canonical URL</label>
                    <input type="url" id="smart_seo_canonical" name="smart_seo_canonical" value="<?php echo esc_url($canonical_url); ?>" placeholder="https://example.com/page">
                    <div class="seo-field-help">The preferred URL for this content. Helps prevent duplicate content issues.</div>
                </div>
                
                <div class="seo-field-group">
                    <label for="smart_seo_robots"><span class="dashicons dashicons-admin-generic" aria-hidden="true"></span> Robots Meta</label>
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
                    <label for="smart_seo_schema_type"><span class="dashicons dashicons-clipboard" aria-hidden="true"></span> Schema Type</label>
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
            <div class="seo-tab-content" id="analysis-tab" role="tabpanel" aria-labelledby="seo-tab-analysis" hidden>
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

        // The block editor uses the sidebar; skip the classic metabox assets there.
        if (self::is_block_editor()) {
            return;
        }

        $ver     = defined('SMART_SEO_BOOSTER_VERSION') ? SMART_SEO_BOOSTER_VERSION : false;
        $css_url = plugin_dir_url(__FILE__) . '../css/meta-box.css';
        $js_url  = plugin_dir_url(__FILE__) . '../js/meta-fields.js';

        wp_enqueue_media(); // For image uploads

        wp_enqueue_style('smart-seo-meta-box', $css_url, ['dashicons'], $ver);

        wp_enqueue_script('smart-seo-meta-fields', $js_url, ['jquery'], $ver, true);
        wp_localize_script('smart-seo-meta-fields', 'SmartSEOMeta', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('smart_seo_nonce'),
        ]);
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

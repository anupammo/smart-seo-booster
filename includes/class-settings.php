<?php
defined('ABSPATH') || exit;

class Smart_SEO_Settings {
    public static function init() {
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_settings_scripts']);
    }

    /**
     * Enqueue scripts and styles for settings page
     * 
     * @param string $hook Current admin page hook
     * @since 2.1.0
     */
    public static function enqueue_settings_scripts($hook) {
        if (strpos($hook, 'smart-seo') === false) {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_script('jquery');
        
        $script = "
        jQuery(document).ready(function($) {
            // Image upload functionality
            var mediaUploader;
            
            $('.seo-upload-image-btn').click(function(e) {
                e.preventDefault();
                
                var button = $(this);
                var fieldName = button.data('field');
                var inputField = $('input[name=\"smart_seo_options[' + fieldName + ']\"]');
                
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                mediaUploader = wp.media({
                    title: 'Choose SEO Image',
                    button: { text: 'Choose Image' },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    inputField.val(attachment.url);
                    
                    // Update preview
                    var preview = button.siblings('.seo-image-preview');
                    if (preview.length) {
                        preview.html('<img src=\"' + attachment.url + '\" alt=\"Preview\" style=\"max-width: 300px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;\" />');
                    } else {
                        button.after('<div class=\"seo-image-preview\" style=\"margin-top: 10px;\"><img src=\"' + attachment.url + '\" alt=\"Preview\" style=\"max-width: 300px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;\" /></div>');
                    }
                });
                
                mediaUploader.open();
            });
            
            // Settings page organization
            $('.form-table').each(function() {
                var table = $(this);
                var section = table.prev('h3');
                
                // Add collapsible functionality to sections
                if (section.length) {
                    section.css({
                        'cursor': 'pointer',
                        'padding': '10px',
                        'background': '#f8f9fa',
                        'border': '1px solid #c3c4c7',
                        'border-radius': '4px',
                        'margin': '20px 0 10px 0'
                    }).append(' <span class=\"dashicons dashicons-arrow-down-alt2\" style=\"float: right;\"></span>');
                    
                    section.click(function() {
                        var icon = $(this).find('.dashicons');
                        var table = $(this).next('.form-table');
                        
                        table.slideToggle();
                        icon.toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
                    });
                }
            });
            
            // Add save status indicator
            var form = $('.smart-seo-settings-form');
            if (form.length) {
                form.on('submit', function() {
                    var submitBtn = $(this).find('input[type=\"submit\"]');
                    submitBtn.val('Saving Settings...').prop('disabled', true);
                });
            }
            
            // Help tooltips
            $('.description').each(function() {
                $(this).prepend('<span class=\"dashicons dashicons-info\" style=\"color: #72aee6; margin-right: 5px;\"></span>');
            });
            
            // Real-time validation for numeric fields
            $('input[type=\"number\"]').on('input', function() {
                var input = $(this);
                var value = parseFloat(input.val());
                var min = parseFloat(input.attr('min'));
                var max = parseFloat(input.attr('max'));
                
                if (min && value < min) {
                    input.css('border-color', '#d63638');
                } else if (max && value > max) {
                    input.css('border-color', '#d63638');
                } else {
                    input.css('border-color', '');
                }
            });
        });
        ";
        
        wp_add_inline_script('jquery', $script);
        
        // Add custom CSS for better settings page appearance
        $css = "
        .smart-seo-settings-wrap {
            max-width: 1200px;
        }
        
        .smart-seo-settings-wrap .form-table th {
            width: 220px;
            padding: 15px 10px;
        }
        
        .smart-seo-settings-wrap .form-table td {
            padding: 15px 10px;
        }
        
        .smart-seo-settings-wrap .notice.inline {
            display: block;
            margin: 5px 0 15px 0;
            padding: 8px 12px;
        }
        
        .smart-seo-settings-wrap .seo-image-upload-field {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .smart-seo-settings-wrap .seo-image-preview {
            margin-top: 10px;
        }
        
        .smart-seo-settings-wrap fieldset label {
            font-weight: normal;
        }
        
        .smart-seo-settings-wrap .submit {
            padding-top: 20px;
            border-top: 1px solid #c3c4c7;
            margin-top: 30px;
        }
        
        .smart-seo-settings-wrap .submit .button-primary {
            background: #2271b1;
            border-color: #2271b1;
            color: #fff;
            text-decoration: none;
            text-shadow: none;
            padding: 8px 20px;
            font-size: 14px;
            line-height: 1.4;
            border-radius: 4px;
        }
        
        .smart-seo-settings-wrap h3 {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .smart-seo-settings-wrap h3 .dashicons {
            color: #2271b1;
        }
        
        @media (max-width: 768px) {
            .smart-seo-settings-wrap .form-table th,
            .smart-seo-settings-wrap .form-table td {
                display: block;
                width: 100%;
                padding: 10px 0;
            }
            
            .smart-seo-settings-wrap .seo-image-upload-field {
                flex-direction: column;
                align-items: stretch;
            }
        }
        ";
        
        wp_add_inline_style('admin-menu', $css);
    }

    public static function register_settings() {
        register_setting('smart_seo_settings', 'smart_seo_options', [
            'sanitize_callback' => [__CLASS__, 'sanitize_options']
        ]);
        
        // Schema Settings Section
        add_settings_section(
            'smart_seo_schema', 
            'Schema Markup Settings', 
            [__CLASS__, 'schema_section_callback'], 
            'smart_seo'
        );
        
        add_settings_field(
            'enable_schema', 
            'Enable Schema Markup', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_schema', 
            ['name' => 'enable_schema', 'description' => 'Automatically inject JSON-LD schema markup']
        );
        
        add_settings_field(
            'default_schema_type', 
            'Default Schema Type', 
            [__CLASS__, 'select'], 
            'smart_seo', 
            'smart_seo_schema', 
            [
                'name' => 'default_schema_type',
                'options' => [
                    'organization' => 'Organization',
                    'local-business' => 'Local Business',
                    'article' => 'Article'
                ],
                'description' => 'Default schema for pages without specific schema detection'
            ]
        );

        // Meta Tags Section
        add_settings_section(
            'smart_seo_meta', 
            'Meta Tags Settings', 
            [__CLASS__, 'meta_section_callback'], 
            'smart_seo'
        );
        
        add_settings_field(
            'enable_meta_tags', 
            'Enable Meta Tags', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_meta', 
            ['name' => 'enable_meta_tags', 'description' => 'Inject title and description meta tags']
        );
        
        add_settings_field(
            'enable_og_tags', 
            'Enable Open Graph Tags', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_meta', 
            ['name' => 'enable_og_tags', 'description' => 'Add Open Graph meta tags for social media sharing']
        );
        
        add_settings_field(
            'default_description', 
            'Default Meta Description', 
            [__CLASS__, 'textarea'], 
            'smart_seo', 
            'smart_seo_meta', 
            ['name' => 'default_description', 'description' => 'Used when post/page has no excerpt or content']
        );

        // Content Audit Section
        add_settings_section(
            'smart_seo_audit', 
            'Content Audit Settings', 
            [__CLASS__, 'audit_section_callback'], 
            'smart_seo'
        );
        
        add_settings_field(
            'enable_content_audit', 
            'Enable Content Audit', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_audit', 
            ['name' => 'enable_content_audit', 'description' => 'Show SEO audit notices in post editor']
        );
        
        add_settings_field(
            'min_word_count', 
            'Minimum Word Count', 
            [__CLASS__, 'number'], 
            'smart_seo', 
            'smart_seo_audit', 
            ['name' => 'min_word_count', 'default' => 300, 'description' => 'Minimum words for good SEO score']
        );
        
        add_settings_field(
            'enable_link_analysis', 
            'Enable Link Analysis', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_audit', 
            ['name' => 'enable_link_analysis', 'description' => 'Analyze internal links in content']
        );

        // Meta Fields Section
        add_settings_section(
            'smart_seo_meta_fields', 
            'Custom Meta Fields Settings', 
            [__CLASS__, 'meta_fields_section_callback'], 
            'smart_seo'
        );

        add_settings_field(
            'enable_custom_meta_fields', 
            'Enable Custom Meta Fields', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_meta_fields', 
            ['name' => 'enable_custom_meta_fields', 'description' => 'Show custom meta fields metabox in post/page editor']
        );

        add_settings_field(
            'meta_fields_post_types', 
            'Enabled Post Types', 
            [__CLASS__, 'post_types_checkboxes'], 
            'smart_seo', 
            'smart_seo_meta_fields', 
            ['name' => 'meta_fields_post_types', 'description' => 'Select which post types should have SEO meta fields']
        );

        add_settings_field(
            'default_og_image', 
            'Default Open Graph Image', 
            [__CLASS__, 'image_upload'], 
            'smart_seo', 
            'smart_seo_meta_fields', 
            ['name' => 'default_og_image', 'description' => 'Default image for social media sharing (1200x630px recommended)']
        );

        // Social Media Section
        add_settings_section(
            'smart_seo_social', 
            'Social Media Settings', 
            [__CLASS__, 'social_section_callback'], 
            'smart_seo'
        );

        add_settings_field(
            'enable_twitter_cards', 
            'Enable Twitter Cards', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_social', 
            ['name' => 'enable_twitter_cards', 'description' => 'Add Twitter Card meta tags']
        );

        add_settings_field(
            'default_twitter_card_type', 
            'Default Twitter Card Type', 
            [__CLASS__, 'select'], 
            'smart_seo', 
            'smart_seo_social', 
            [
                'name' => 'default_twitter_card_type',
                'options' => [
                    'summary' => 'Summary',
                    'summary_large_image' => 'Summary Large Image',
                    'app' => 'App',
                    'player' => 'Player'
                ],
                'description' => 'Default Twitter Card type for content'
            ]
        );

        add_settings_field(
            'twitter_site', 
            'Twitter Site Handle', 
            [__CLASS__, 'text'], 
            'smart_seo', 
            'smart_seo_social', 
            ['name' => 'twitter_site', 'description' => 'Your website\'s Twitter handle (e.g., @yoursite)']
        );

        add_settings_field(
            'facebook_app_id', 
            'Facebook App ID', 
            [__CLASS__, 'text'], 
            'smart_seo', 
            'smart_seo_social', 
            ['name' => 'facebook_app_id', 'description' => 'Facebook App ID for social analytics']
        );

        // Advanced SEO Section
        add_settings_section(
            'smart_seo_advanced', 
            'Advanced SEO Settings', 
            [__CLASS__, 'advanced_section_callback'], 
            'smart_seo'
        );

        add_settings_field(
            'enable_schema_breadcrumbs', 
            'Enable Schema Breadcrumbs', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_advanced', 
            ['name' => 'enable_schema_breadcrumbs', 'description' => 'Add breadcrumb schema markup']
        );

        add_settings_field(
            'enable_auto_canonical', 
            'Auto-Generate Canonical URLs', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_advanced', 
            ['name' => 'enable_auto_canonical', 'description' => 'Automatically add canonical URLs to prevent duplicate content']
        );

        add_settings_field(
            'robots_txt_enhancement', 
            'Enhance Robots.txt', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_advanced', 
            ['name' => 'robots_txt_enhancement', 'description' => 'Add SEO-friendly rules to robots.txt']
        );

        add_settings_field(
            'xml_sitemap_generation', 
            'Generate XML Sitemap', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_advanced', 
            ['name' => 'xml_sitemap_generation', 'description' => 'Automatically generate and update XML sitemap']
        );

        add_settings_field(
            'focus_keyword_analysis', 
            'Enable Focus Keyword Analysis', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_advanced', 
            ['name' => 'focus_keyword_analysis', 'description' => 'Real-time keyword density and optimization analysis']
        );

        add_settings_field(
            'max_keyword_density', 
            'Maximum Keyword Density (%)', 
            [__CLASS__, 'number'], 
            'smart_seo', 
            'smart_seo_advanced', 
            ['name' => 'max_keyword_density', 'default' => 2.5, 'step' => 0.1, 'description' => 'Maximum keyword density before warning (recommended: 2.5%)']
        );

        // Performance Section
        add_settings_section(
            'smart_seo_performance', 
            'Performance & Display Settings', 
            [__CLASS__, 'performance_section_callback'], 
            'smart_seo'
        );

        add_settings_field(
            'enable_seo_score_column', 
            'Show SEO Score in Post Lists', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_performance', 
            ['name' => 'enable_seo_score_column', 'description' => 'Display SEO scores in posts/pages admin lists']
        );

        add_settings_field(
            'enable_admin_bar_score', 
            'Show SEO Score in Admin Bar', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_performance', 
            ['name' => 'enable_admin_bar_score', 'description' => 'Display current page SEO score in admin bar']
        );

        add_settings_field(
            'cache_seo_analysis', 
            'Cache SEO Analysis Results', 
            [__CLASS__, 'checkbox'], 
            'smart_seo', 
            'smart_seo_performance', 
            ['name' => 'cache_seo_analysis', 'description' => 'Cache analysis results for better performance']
        );

        add_settings_field(
            'analysis_cache_duration', 
            'Cache Duration (hours)', 
            [__CLASS__, 'number'], 
            'smart_seo', 
            'smart_seo_performance', 
            ['name' => 'analysis_cache_duration', 'default' => 24, 'description' => 'How long to cache SEO analysis results']
        );
    }

    public static function checkbox($args) {
        $options = get_option('smart_seo_options', []);
        $checked = isset($options[$args['name']]) && $options[$args['name']] ? 'checked' : '';
        $description = isset($args['description']) ? '<p class="description">' . wp_kses_post($args['description']) . '</p>' : '';
        
        echo '<label>';
        echo '<input type="checkbox" name="smart_seo_options[' . esc_attr($args['name']) . ']" value="1" ' . esc_attr($checked) . ' />';
        echo ' ' . esc_html__('Enable this option', 'smart-seo-booster');
        echo '</label>';
        echo wp_kses_post($description);
    }
    
    public static function select($args) {
        $options = get_option('smart_seo_options', []);
        $current = isset($options[$args['name']]) ? $options[$args['name']] : '';
        $description = isset($args['description']) ? '<p class="description">' . wp_kses_post($args['description']) . '</p>' : '';
        
        echo '<select name="smart_seo_options[' . esc_attr($args['name']) . ']">';
        foreach ($args['options'] as $value => $label) {
            $selected = selected($current, $value, false);
            echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        echo wp_kses_post($description);
    }
    
    public static function textarea($args) {
        $options = get_option('smart_seo_options', []);
        $value = isset($options[$args['name']]) ? esc_textarea($options[$args['name']]) : '';
        $description = isset($args['description']) ? '<p class="description">' . wp_kses_post($args['description']) . '</p>' : '';
        
        echo '<textarea name="smart_seo_options[' . esc_attr($args['name']) . ']" rows="3" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
        echo wp_kses_post($description);
    }
    
    public static function number($args) {
        $options = get_option('smart_seo_options', []);
        $value = isset($options[$args['name']]) ? floatval($options[$args['name']]) : ($args['default'] ?? 0);
        $step = isset($args['step']) ? floatval($args['step']) : 1;
        $description = isset($args['description']) ? '<p class="description">' . wp_kses_post($args['description']) . '</p>' : '';
        
        echo '<input type="number" name="smart_seo_options[' . esc_attr($args['name']) . ']" value="' . esc_attr($value) . '" min="0" step="' . esc_attr($step) . '" class="small-text" />';
        echo wp_kses_post($description);
    }

    public static function text($args) {
        $options = get_option('smart_seo_options', []);
        $value = isset($options[$args['name']]) ? esc_attr($options[$args['name']]) : '';
        $description = isset($args['description']) ? '<p class="description">' . wp_kses_post($args['description']) . '</p>' : '';
        
        echo '<input type="text" name="smart_seo_options[' . esc_attr($args['name']) . ']" value="' . esc_attr($value) . '" class="regular-text" />';
        echo wp_kses_post($description);
    }

    public static function post_types_checkboxes($args) {
        $options = get_option('smart_seo_options', []);
        $selected_types = isset($options[$args['name']]) ? $options[$args['name']] : ['post', 'page'];
        $description = isset($args['description']) ? '<p class="description">' . wp_kses_post($args['description']) . '</p>' : '';
        
        $post_types = get_post_types(['public' => true], 'objects');
        
        echo '<fieldset>';
        foreach ($post_types as $post_type) {
            $checked = in_array($post_type->name, $selected_types) ? 'checked' : '';
            echo '<label style="display: block; margin-bottom: 5px;">';
            echo '<input type="checkbox" name="smart_seo_options[' . esc_attr($args['name']) . '][]" value="' . esc_attr($post_type->name) . '" ' . esc_attr($checked) . ' />';
            echo ' ' . esc_html($post_type->label);
            echo '</label>';
        }
        echo '</fieldset>';
        echo wp_kses_post($description);
    }

    public static function image_upload($args) {
        $options = get_option('smart_seo_options', []);
        $value = isset($options[$args['name']]) ? esc_url($options[$args['name']]) : '';
        $description = isset($args['description']) ? '<p class="description">' . wp_kses_post($args['description']) . '</p>' : '';
        
        echo '<div class="seo-image-upload-field">';
        echo '<input type="url" name="smart_seo_options[' . esc_attr($args['name']) . ']" value="' . esc_url($value) . '" class="regular-text" />';
        echo '<button type="button" class="button seo-upload-image-btn" data-field="' . esc_attr($args['name']) . '">' . esc_html__('Upload Image', 'smart-seo-booster') . '</button>';
        
        if ($value) {
            echo '<div class="seo-image-preview" style="margin-top: 10px;">';
            echo '<img src="' . esc_url($value) . '" alt="' . esc_attr__('Preview', 'smart-seo-booster') . '" style="max-width: 300px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;" />';
            echo '</div>';
        }
        
        echo '</div>';
        echo wp_kses_post($description);
    }
    
    public static function schema_section_callback() {
        echo '<p>Configure how schema markup is generated and injected into your pages.</p>';
    }
    
    public static function meta_section_callback() {
        echo '<p>Control meta tag generation including titles, descriptions, and Open Graph tags.</p>';
    }
    
    public static function audit_section_callback() {
        echo '<p>Set up content audit parameters and thresholds for SEO recommendations.</p>';
    }

    public static function meta_fields_section_callback() {
        echo '<p>Configure custom meta fields for advanced SEO control on individual posts and pages.</p>';
        echo '<div class="notice notice-info inline" style="margin: 10px 0; padding: 10px;"><p><strong>New!</strong> Custom meta fields allow you to set unique titles, descriptions, and social media tags for each post/page.</p></div>';
    }

    public static function social_section_callback() {
        echo '<p>Configure social media optimization settings for better sharing and engagement.</p>';
        echo '<div class="notice notice-info inline" style="margin: 10px 0; padding: 10px;"><p><strong>Enhanced!</strong> Now includes Twitter Cards, Facebook integration, and social media previews.</p></div>';
    }

    public static function advanced_section_callback() {
        echo '<p>Advanced SEO features for technical optimization and enhanced search engine visibility.</p>';
        echo '<div class="notice notice-warning inline" style="margin: 10px 0; padding: 10px;"><p><strong>Advanced Users:</strong> These settings affect technical SEO aspects. Use with caution.</p></div>';
    }

    public static function performance_section_callback() {
        echo '<p>Control plugin performance and display options for optimal user experience.</p>';
        echo '<div class="notice notice-success inline" style="margin: 10px 0; padding: 10px;"><p><strong>Performance:</strong> Enable caching for faster SEO analysis on large sites.</p></div>';
    }
    
    public static function sanitize_options($input) {
        $sanitized = [];
        
        // Checkbox fields
        $checkboxes = [
            'enable_schema', 
            'enable_meta_tags', 
            'enable_og_tags', 
            'enable_content_audit', 
            'enable_link_analysis',
            'enable_custom_meta_fields',
            'enable_twitter_cards',
            'enable_schema_breadcrumbs',
            'enable_auto_canonical',
            'robots_txt_enhancement',
            'xml_sitemap_generation',
            'focus_keyword_analysis',
            'enable_seo_score_column',
            'enable_admin_bar_score',
            'cache_seo_analysis'
        ];
        
        foreach ($checkboxes as $checkbox) {
            $sanitized[$checkbox] = isset($input[$checkbox]) ? 1 : 0;
        }
        
        // Text fields
        $text_fields = ['default_description', 'twitter_site', 'facebook_app_id'];
        foreach ($text_fields as $field) {
            if (isset($input[$field])) {
                if ($field === 'default_description') {
                    $sanitized[$field] = sanitize_textarea_field($input[$field]);
                } else {
                    $sanitized[$field] = sanitize_text_field($input[$field]);
                }
            }
        }
        
        // URL fields
        if (isset($input['default_og_image'])) {
            $sanitized['default_og_image'] = esc_url_raw($input['default_og_image']);
        }
        
        // Select fields
        if (isset($input['default_schema_type'])) {
            $allowed_types = ['organization', 'local-business', 'article'];
            $sanitized['default_schema_type'] = in_array($input['default_schema_type'], $allowed_types) 
                ? $input['default_schema_type'] 
                : 'organization';
        }
        
        if (isset($input['default_twitter_card_type'])) {
            $allowed_cards = ['summary', 'summary_large_image', 'app', 'player'];
            $sanitized['default_twitter_card_type'] = in_array($input['default_twitter_card_type'], $allowed_cards) 
                ? $input['default_twitter_card_type'] 
                : 'summary_large_image';
        }
        
        // Number fields
        if (isset($input['min_word_count'])) {
            $sanitized['min_word_count'] = max(0, intval($input['min_word_count']));
        }
        
        if (isset($input['max_keyword_density'])) {
            $sanitized['max_keyword_density'] = max(0.1, min(10.0, floatval($input['max_keyword_density'])));
        }
        
        if (isset($input['analysis_cache_duration'])) {
            $sanitized['analysis_cache_duration'] = max(1, min(168, intval($input['analysis_cache_duration']))); // 1 hour to 1 week
        }
        
        // Post types array
        if (isset($input['meta_fields_post_types']) && is_array($input['meta_fields_post_types'])) {
            $allowed_post_types = get_post_types(['public' => true]);
            $sanitized['meta_fields_post_types'] = array_intersect($input['meta_fields_post_types'], array_keys($allowed_post_types));
        } else {
            $sanitized['meta_fields_post_types'] = ['post', 'page']; // Default
        }
        
        return $sanitized;
    }
}

<?php
defined('ABSPATH') || exit;

class Smart_SEO_Settings {
    public static function init() {
        add_action('admin_init', [__CLASS__, 'register_settings']);
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
    }

    public static function checkbox($args) {
        $options = get_option('smart_seo_options', []);
        $checked = isset($options[$args['name']]) && $options[$args['name']] ? 'checked' : '';
        $description = isset($args['description']) ? '<p class="description">' . $args['description'] . '</p>' : '';
        
        echo "<label>";
        echo "<input type='checkbox' name='smart_seo_options[{$args['name']}]' value='1' $checked />";
        echo " Enable this option";
        echo "</label>";
        echo $description;
    }
    
    public static function select($args) {
        $options = get_option('smart_seo_options', []);
        $current = isset($options[$args['name']]) ? $options[$args['name']] : '';
        $description = isset($args['description']) ? '<p class="description">' . $args['description'] . '</p>' : '';
        
        echo "<select name='smart_seo_options[{$args['name']}]'>";
        foreach ($args['options'] as $value => $label) {
            $selected = selected($current, $value, false);
            echo "<option value='$value' $selected>$label</option>";
        }
        echo "</select>";
        echo $description;
    }
    
    public static function textarea($args) {
        $options = get_option('smart_seo_options', []);
        $value = isset($options[$args['name']]) ? esc_textarea($options[$args['name']]) : '';
        $description = isset($args['description']) ? '<p class="description">' . $args['description'] . '</p>' : '';
        
        echo "<textarea name='smart_seo_options[{$args['name']}]' rows='3' cols='50' class='large-text'>$value</textarea>";
        echo $description;
    }
    
    public static function number($args) {
        $options = get_option('smart_seo_options', []);
        $value = isset($options[$args['name']]) ? intval($options[$args['name']]) : ($args['default'] ?? 0);
        $description = isset($args['description']) ? '<p class="description">' . $args['description'] . '</p>' : '';
        
        echo "<input type='number' name='smart_seo_options[{$args['name']}]' value='$value' min='0' step='1' class='small-text' />";
        echo $description;
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
    
    public static function sanitize_options($input) {
        $sanitized = [];
        
        // Checkbox fields
        $checkboxes = ['enable_schema', 'enable_meta_tags', 'enable_og_tags', 'enable_content_audit', 'enable_link_analysis'];
        foreach ($checkboxes as $checkbox) {
            $sanitized[$checkbox] = isset($input[$checkbox]) ? 1 : 0;
        }
        
        // Text fields
        if (isset($input['default_description'])) {
            $sanitized['default_description'] = sanitize_textarea_field($input['default_description']);
        }
        
        // Select fields
        if (isset($input['default_schema_type'])) {
            $allowed_types = ['organization', 'local-business', 'article'];
            $sanitized['default_schema_type'] = in_array($input['default_schema_type'], $allowed_types) 
                ? $input['default_schema_type'] 
                : 'organization';
        }
        
        // Number fields
        if (isset($input['min_word_count'])) {
            $sanitized['min_word_count'] = max(0, intval($input['min_word_count']));
        }
        
        return $sanitized;
    }
}

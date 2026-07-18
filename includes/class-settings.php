<?php
defined('ABSPATH') || exit;

/**
 * Plugin settings — registers the option, sections and fields.
 *
 * All options live under the single `smart_seo_options` array and are
 * rendered with a config-driven generic field callback.
 */
class Smart_SEO_Settings {

    const OPTION = 'smart_seo_options';

    public static function init() {
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    /**
     * Field definitions grouped by section. Drives both rendering and sanitization.
     *
     * @return array
     */
    public static function fields() {
        return [
            'smart_seo_general' => [
                'title'  => __( 'General', 'smart-seo-booster' ),
                'fields' => [
                    'enable_schema'      => [ 'type' => 'checkbox', 'label' => __( 'Enable Schema Markup (JSON-LD)', 'smart-seo-booster' ) ],
                    'enable_sitemap'     => [ 'type' => 'checkbox', 'label' => __( 'Enable XML Sitemap', 'smart-seo-booster' ), 'desc' => __( 'Available at /sitemap.xml', 'smart-seo-booster' ) ],
                    'disable_wp_sitemap' => [ 'type' => 'checkbox', 'label' => __( 'Disable the default WordPress sitemap', 'smart-seo-booster' ), 'desc' => __( 'Prevents duplicate sitemaps when the plugin sitemap is on.', 'smart-seo-booster' ) ],
                    'enable_breadcrumbs' => [ 'type' => 'checkbox', 'label' => __( 'Enable Breadcrumbs', 'smart-seo-booster' ), 'desc' => __( 'Use [smart_seo_breadcrumbs] or the smart_seo_breadcrumbs() template tag.', 'smart-seo-booster' ) ],
                    'breadcrumb_separator'  => [ 'type' => 'text', 'label' => __( 'Breadcrumb separator', 'smart-seo-booster' ), 'default' => '/', 'class' => 'small-text' ],
                    'breadcrumb_home_label' => [ 'type' => 'text', 'label' => __( 'Breadcrumb home label', 'smart-seo-booster' ), 'default' => __( 'Home', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'min_word_count'     => [ 'type' => 'number', 'label' => __( 'Minimum word count', 'smart-seo-booster' ), 'default' => 300 ],
                ],
            ],
            'smart_seo_titles' => [
                'title'  => __( 'Titles &amp; Metas', 'smart-seo-booster' ),
                'fields' => [
                    'separator'           => [ 'type' => 'text', 'label' => __( 'Title separator', 'smart-seo-booster' ), 'default' => '-', 'class' => 'small-text' ],
                    'title_template'       => [ 'type' => 'text', 'label' => __( 'Title template', 'smart-seo-booster' ), 'default' => '%%title%% %%sep%% %%sitename%%', 'vars' => true ],
                    'description_template' => [ 'type' => 'textarea', 'label' => __( 'Meta description template', 'smart-seo-booster' ), 'default' => '%%excerpt%%', 'vars' => true ],
                ],
            ],
            'smart_seo_webmaster' => [
                'title'  => __( 'Webmaster Verification', 'smart-seo-booster' ),
                'desc'   => __( 'Paste only the verification code (the content value), not the whole meta tag.', 'smart-seo-booster' ),
                'fields' => [
                    'verify_google'    => [ 'type' => 'text', 'label' => __( 'Google', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'verify_bing'      => [ 'type' => 'text', 'label' => __( 'Bing', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'verify_pinterest' => [ 'type' => 'text', 'label' => __( 'Pinterest', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'verify_yandex'    => [ 'type' => 'text', 'label' => __( 'Yandex', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                ],
            ],
            'smart_seo_indexing' => [
                'title'  => __( 'Indexing', 'smart-seo-booster' ),
                'desc'   => __( 'Tell search engines not to index low-value archive pages. Links are still followed.', 'smart-seo-booster' ),
                'fields' => [
                    'noindex_archives'  => [ 'type' => 'checkbox', 'label' => __( 'No-index category, tag &amp; taxonomy archives', 'smart-seo-booster' ) ],
                    'noindex_author'    => [ 'type' => 'checkbox', 'label' => __( 'No-index author archives', 'smart-seo-booster' ) ],
                    'noindex_date'      => [ 'type' => 'checkbox', 'label' => __( 'No-index date archives', 'smart-seo-booster' ) ],
                    'noindex_search'    => [ 'type' => 'checkbox', 'label' => __( 'No-index search results', 'smart-seo-booster' ) ],
                    'noindex_paginated' => [ 'type' => 'checkbox', 'label' => __( 'No-index paginated pages (page 2, 3…)', 'smart-seo-booster' ) ],
                ],
            ],
            'smart_seo_schema_data' => [
                'title'  => __( 'Schema Details', 'smart-seo-booster' ),
                'desc'   => __( 'Optional data used to enrich Organization and LocalBusiness schema.', 'smart-seo-booster' ),
                'fields' => [
                    'organization_same_as' => [ 'type' => 'textarea', 'label' => __( 'Social profile URLs', 'smart-seo-booster' ), 'desc' => __( 'One URL per line (Facebook, X, LinkedIn, etc.).', 'smart-seo-booster' ) ],
                    'business_phone'       => [ 'type' => 'text', 'label' => __( 'Business phone', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'business_street'      => [ 'type' => 'text', 'label' => __( 'Street address', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'business_locality'    => [ 'type' => 'text', 'label' => __( 'City / locality', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'business_region'      => [ 'type' => 'text', 'label' => __( 'Region / state', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'business_postal'      => [ 'type' => 'text', 'label' => __( 'Postal code', 'smart-seo-booster' ), 'class' => 'small-text' ],
                    'business_country'     => [ 'type' => 'text', 'label' => __( 'Country code', 'smart-seo-booster' ), 'class' => 'small-text', 'desc' => __( 'Two-letter code, e.g. US, GB, IN.', 'smart-seo-booster' ) ],
                ],
            ],
        ];
    }

    public static function register_settings() {
        register_setting('smart_seo_settings', self::OPTION, [
            'sanitize_callback' => [__CLASS__, 'sanitize_options'],
        ]);

        foreach ( self::fields() as $section_id => $section ) {
            add_settings_section(
                $section_id,
                $section['title'],
                function () use ( $section ) {
                    if ( ! empty( $section['desc'] ) ) {
                        echo '<p class="description">' . esc_html( $section['desc'] ) . '</p>';
                    }
                },
                'smart_seo'
            );

            foreach ( $section['fields'] as $name => $field ) {
                $field['name'] = $name;
                add_settings_field(
                    $name,
                    $field['label'],
                    [__CLASS__, 'render_field'],
                    'smart_seo',
                    $section_id,
                    $field
                );
            }
        }
    }

    /**
     * Generic field renderer.
     *
     * @param array $field Field definition (with 'name').
     */
    public static function render_field( $field ) {
        $options = get_option( self::OPTION, [] );
        $name    = $field['name'];
        $value   = isset( $options[ $name ] ) ? $options[ $name ] : ( $field['default'] ?? '' );
        $attr    = 'smart_seo_options[' . esc_attr( $name ) . ']';
        $class   = isset( $field['class'] ) ? $field['class'] : 'regular-text';

        switch ( $field['type'] ) {
            case 'checkbox':
                echo '<label><input type="checkbox" name="' . esc_attr( $attr ) . '" value="1" ' . checked( ! empty( $value ), true, false ) . ' /> ';
                if ( ! empty( $field['desc'] ) ) {
                    echo esc_html( $field['desc'] );
                }
                echo '</label>';
                break;

            case 'number':
                echo '<input type="number" min="0" step="1" class="small-text" name="' . esc_attr( $attr ) . '" value="' . esc_attr( $value ) . '" />';
                break;

            case 'textarea':
                echo '<textarea class="large-text" rows="4" name="' . esc_attr( $attr ) . '">' . esc_textarea( $value ) . '</textarea>';
                break;

            case 'text':
            default:
                echo '<input type="text" class="' . esc_attr( $class ) . '" name="' . esc_attr( $attr ) . '" value="' . esc_attr( $value ) . '" />';
                break;
        }

        if ( ! empty( $field['desc'] ) && $field['type'] !== 'checkbox' ) {
            echo '<p class="description">' . esc_html( $field['desc'] ) . '</p>';
        }

        if ( ! empty( $field['vars'] ) ) {
            echo '<p class="description">' . esc_html__( 'Variables:', 'smart-seo-booster' ) . ' <code>'
                . esc_html( implode( '</code> <code>', array_keys( Smart_SEO_Meta_Templates::variables() ) ) )
                . '</code></p>';
        }
    }

    /**
     * Sanitize every known option by its declared type.
     *
     * @param array $input Raw input.
     * @return array
     */
    public static function sanitize_options( $input ) {
        $output = [];
        if ( ! is_array( $input ) ) {
            return $output;
        }

        foreach ( self::fields() as $section ) {
            foreach ( $section['fields'] as $name => $field ) {
                switch ( $field['type'] ) {
                    case 'checkbox':
                        $output[ $name ] = ! empty( $input[ $name ] ) ? 1 : 0;
                        break;

                    case 'number':
                        $output[ $name ] = isset( $input[ $name ] ) ? absint( $input[ $name ] ) : ( $field['default'] ?? 0 );
                        break;

                    case 'textarea':
                        $output[ $name ] = isset( $input[ $name ] ) ? sanitize_textarea_field( $input[ $name ] ) : '';
                        break;

                    case 'text':
                    default:
                        $output[ $name ] = isset( $input[ $name ] ) ? sanitize_text_field( $input[ $name ] ) : '';
                        break;
                }
            }
        }

        return $output;
    }
}

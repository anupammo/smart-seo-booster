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
                    'breadcrumb_separator'  => [ 'type' => 'select', 'label' => __( 'Breadcrumb separator', 'smart-seo-booster' ), 'default' => '/', 'options' => self::separator_options() ],
                    'breadcrumb_home_label' => [ 'type' => 'text', 'label' => __( 'Breadcrumb home label', 'smart-seo-booster' ), 'default' => __( 'Home', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'auto_image_alt'     => [ 'type' => 'checkbox', 'label' => __( 'Auto-add missing image alt text', 'smart-seo-booster' ), 'desc' => __( 'Fills empty alt attributes from the image or post title on the front end.', 'smart-seo-booster' ) ],
                    'frontend_credit'    => [ 'type' => 'checkbox', 'locked' => true, 'label' => __( 'Show a small credit link in the footer', 'smart-seo-booster' ), 'desc' => __( 'Included free. Displays “SEO by Smart SEO Booster” linking to the developer. Removing this link requires Smart SEO Booster Pro.', 'smart-seo-booster' ) ],
                    'min_word_count'     => [ 'type' => 'number', 'label' => __( 'Minimum word count', 'smart-seo-booster' ), 'default' => 300 ],
                ],
            ],
            'smart_seo_titles' => [
                'title'  => __( 'Titles &amp; Metas', 'smart-seo-booster' ),
                'fields' => [
                    'separator'           => [ 'type' => 'select', 'label' => __( 'Title separator', 'smart-seo-booster' ), 'default' => '-', 'options' => self::separator_options() ],
                    'title_template'       => [ 'type' => 'text', 'label' => __( 'Title template', 'smart-seo-booster' ), 'default' => '%%title%% %%sep%% %%sitename%%', 'vars' => true ],
                    'description_template' => [ 'type' => 'textarea', 'label' => __( 'Meta description template', 'smart-seo-booster' ), 'default' => '%%excerpt%%', 'vars' => true ],
                ],
            ],
            'smart_seo_webmaster' => [
                'title'  => __( 'Webmaster Verification', 'smart-seo-booster' ),
                'desc'   => __( 'Paste only the verification code (the content value), not the whole meta tag. Google = Search Console, Bing = Webmaster Tools.', 'smart-seo-booster' ),
                'fields' => [
                    'verify_google'    => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'google' ) . __( 'Google (Search Console)', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'verify_bing'      => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'bing' ) . __( 'Bing (Webmaster Tools)', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'verify_pinterest' => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'pinterest' ) . __( 'Pinterest', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'verify_yandex'    => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'yandex' ) . __( 'Yandex', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'verify_baidu'     => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'baidu' ) . __( 'Baidu', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                ],
            ],
            'smart_seo_analytics' => [
                'title'  => __( 'Analytics', 'smart-seo-booster' ),
                'desc'   => __( 'Add tracking without touching your theme. Scripts load only when an ID is set.', 'smart-seo-booster' ),
                'fields' => [
                    'ga4_id'                   => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'ga4' ) . __( 'Google Analytics 4 ID', 'smart-seo-booster' ), 'class' => 'regular-text', 'desc' => __( 'Measurement ID, e.g. G-XXXXXXXXXX', 'smart-seo-booster' ) ],
                    'clarity_id'               => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'clarity' ) . __( 'Microsoft Clarity ID', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'analytics_exclude_admins' => [ 'type' => 'checkbox', 'label' => __( 'Do not track logged-in administrators', 'smart-seo-booster' ) ],
                    'pagespeed_api_key'        => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'pagespeed' ) . __( 'PageSpeed Insights API key', 'smart-seo-booster' ), 'class' => 'regular-text', 'desc' => __( 'Optional. The Page Speed report works without one (Google\'s public quota); add a free API key from Google Cloud Console for higher-volume checks.', 'smart-seo-booster' ) ],
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
            'smart_seo_ai' => [
                'title'  => __( 'AI &amp; GEO', 'smart-seo-booster' ),
                'desc'   => __( 'Generative Engine Optimization — help (or limit) how AI assistants use your content.', 'smart-seo-booster' ),
                'fields' => [
                    'enable_llms_txt'   => [ 'type' => 'checkbox', 'label' => __( 'Serve an llms.txt file', 'smart-seo-booster' ), 'desc' => __( 'A machine-readable summary of your site for AI assistants, at /llms.txt', 'smart-seo-booster' ) ],
                    'block_ai_training' => [ 'type' => 'checkbox', 'label' => __( 'Ask AI crawlers not to train on this site', 'smart-seo-booster' ), 'desc' => __( 'Adds Disallow rules for GPTBot, Google-Extended, ClaudeBot, CCBot and others to robots.txt.', 'smart-seo-booster' ) ],
                    'enable_speakable'  => [ 'type' => 'checkbox', 'label' => __( 'Add speakable structured data', 'smart-seo-booster' ), 'desc' => __( 'Marks the title and description as voice-assistant friendly.', 'smart-seo-booster' ) ],
                ],
            ],
            'smart_seo_social' => [
                'title'  => __( 'Social Media Accounts', 'smart-seo-booster' ),
                'desc'   => __( 'Your site\'s official profiles, added to Organization schema (sameAs) and available for future social features.', 'smart-seo-booster' ),
                'fields' => [
                    'social_facebook'  => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'facebook' ) . __( 'Facebook', 'smart-seo-booster' ), 'class' => 'regular-text', 'desc' => __( 'https://facebook.com/yourpage', 'smart-seo-booster' ) ],
                    'social_twitter'   => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'twitter' ) . __( 'X (Twitter)', 'smart-seo-booster' ), 'class' => 'regular-text', 'desc' => __( 'https://x.com/yourhandle', 'smart-seo-booster' ) ],
                    'social_linkedin'  => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'linkedin' ) . __( 'LinkedIn', 'smart-seo-booster' ), 'class' => 'regular-text', 'desc' => __( 'https://linkedin.com/company/yourcompany', 'smart-seo-booster' ) ],
                    'social_instagram' => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'instagram' ) . __( 'Instagram', 'smart-seo-booster' ), 'class' => 'regular-text', 'desc' => __( 'https://instagram.com/yourhandle', 'smart-seo-booster' ) ],
                    'social_youtube'   => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'youtube' ) . __( 'YouTube', 'smart-seo-booster' ), 'class' => 'regular-text', 'desc' => __( 'https://youtube.com/@yourchannel', 'smart-seo-booster' ) ],
                    'social_pinterest' => [ 'type' => 'text', 'label' => Smart_SEO_Brand_Icons::icon( 'pinterest' ) . __( 'Pinterest', 'smart-seo-booster' ), 'class' => 'regular-text', 'desc' => __( 'https://pinterest.com/yourhandle', 'smart-seo-booster' ) ],
                ],
            ],
            'smart_seo_schema_data' => [
                'title'  => __( 'Schema Details', 'smart-seo-booster' ),
                'desc'   => __( 'Optional data used to enrich Organization and LocalBusiness schema.', 'smart-seo-booster' ),
                'fields' => [
                    'organization_same_as' => [ 'type' => 'textarea', 'label' => __( 'Other social profile URLs', 'smart-seo-booster' ), 'desc' => __( 'One URL per line, for any network not listed under Social Media Accounts.', 'smart-seo-booster' ) ],
                    'business_phone'       => [ 'type' => 'text', 'label' => __( 'Business phone', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'business_street'      => [ 'type' => 'text', 'label' => __( 'Street address', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'business_locality'    => [ 'type' => 'text', 'label' => __( 'City / locality', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'business_region'      => [ 'type' => 'text', 'label' => __( 'Region / state', 'smart-seo-booster' ), 'class' => 'regular-text' ],
                    'business_postal'      => [ 'type' => 'text', 'label' => __( 'Postal code', 'smart-seo-booster' ), 'class' => 'small-text' ],
                    'business_country'     => [ 'type' => 'text', 'label' => __( 'Country code', 'smart-seo-booster' ), 'class' => 'small-text', 'desc' => __( 'Two-letter code, e.g. US, GB, IN.', 'smart-seo-booster' ) ],
                    'business_hours'       => [ 'type' => 'textarea', 'label' => __( 'Opening hours', 'smart-seo-booster' ), 'desc' => __( 'One rule per line, e.g. Mo-Fr 09:00-17:00', 'smart-seo-booster' ) ],
                    'business_lat'         => [ 'type' => 'text', 'label' => __( 'Latitude', 'smart-seo-booster' ), 'class' => 'small-text' ],
                    'business_lng'         => [ 'type' => 'text', 'label' => __( 'Longitude', 'smart-seo-booster' ), 'class' => 'small-text' ],
                ],
            ],
        ];
    }

    /**
     * Common separator characters offered as a dropdown for title/breadcrumb
     * separators, instead of free-text (which invited typos and inconsistent
     * spacing).
     *
     * @return array<string,string> value => display label.
     */
    public static function separator_options() {
        return [
            '-' => '- ' . __( '(hyphen)', 'smart-seo-booster' ),
            '|' => '| ' . __( '(pipe)', 'smart-seo-booster' ),
            '/' => '/ ' . __( '(slash)', 'smart-seo-booster' ),
            '»' => '» ' . __( '(guillemet)', 'smart-seo-booster' ),
            '•' => '• ' . __( '(bullet)', 'smart-seo-booster' ),
            '·' => '· ' . __( '(middle dot)', 'smart-seo-booster' ),
            ':' => ': ' . __( '(colon)', 'smart-seo-booster' ),
            '~' => '~ ' . __( '(tilde)', 'smart-seo-booster' ),
            '—' => '— ' . __( '(em dash)', 'smart-seo-booster' ),
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
                $locked = ! empty( $field['locked'] );
                if ( $locked ) {
                    // Always on; not user-togglable. A hidden field preserves the
                    // value on save since disabled inputs aren't POSTed.
                    echo '<input type="hidden" name="' . esc_attr( $attr ) . '" value="1" />';
                    echo '<label><input type="checkbox" checked="checked" disabled="disabled" /> ';
                    echo '<span class="smart-seo-pro-badge">' . esc_html__( 'Pro', 'smart-seo-booster' ) . '</span> ';
                } else {
                    echo '<label><input type="checkbox" name="' . esc_attr( $attr ) . '" value="1" ' . checked( ! empty( $value ), true, false ) . ' /> ';
                }
                if ( ! empty( $field['desc'] ) ) {
                    echo esc_html( $field['desc'] );
                }
                echo '</label>';
                break;

            case 'select':
                echo '<select name="' . esc_attr( $attr ) . '">';
                foreach ( (array) ( $field['options'] ?? [] ) as $opt_value => $opt_label ) {
                    echo '<option value="' . esc_attr( $opt_value ) . '" ' . selected( $value, $opt_value, false ) . '>' . esc_html( $opt_label ) . '</option>';
                }
                echo '</select>';
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
                        // Locked checkboxes (e.g. the free-tier footer credit)
                        // always save as enabled — there is no user-facing way
                        // to submit a falsy value for them.
                        $output[ $name ] = ( ! empty( $field['locked'] ) || ! empty( $input[ $name ] ) ) ? 1 : 0;
                        break;

                    case 'select':
                        $allowed = array_keys( (array) ( $field['options'] ?? [] ) );
                        $val     = isset( $input[ $name ] ) ? (string) $input[ $name ] : '';
                        $output[ $name ] = in_array( $val, $allowed, true ) ? $val : ( $field['default'] ?? ( $allowed[0] ?? '' ) );
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

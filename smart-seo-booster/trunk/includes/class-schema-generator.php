<?php
defined('ABSPATH') || exit;

/**
 * JSON-LD schema dispatcher.
 *
 * Priority:
 *   1. The per-post schema type chosen in the SEO meta box.
 *   2. Page-context detection (front page, FAQ page, etc.) via /schema templates.
 */
class Smart_SEO_Schema_Generator {

    public static function init() {
        add_action('wp_footer', [__CLASS__, 'output_schema']);
    }

    public static function output_schema() {
        if (is_admin()) {
            return;
        }

        $options = get_option('smart_seo_options');
        if (empty($options['enable_schema'])) {
            return;
        }

        // WooCommerce product pages are handled by the dedicated integration.
        if (function_exists('is_product') && is_product()) {
            return;
        }

        $schema_data = null;

        // 1. Per-post selection.
        if (is_singular()) {
            $post = get_queried_object();
            if ($post instanceof WP_Post) {
                $selected = get_post_meta($post->ID, '_smart_seo_schema_type', true);
                if ($selected) {
                    $schema_data = self::build_for_type($selected, $post);
                }
            }
        }

        // 2. Context-based fallback (file templates).
        if ($schema_data === null) {
            $schema_data = self::load_context_schema();
        }

        if (!is_array($schema_data) || empty($schema_data)) {
            return;
        }

        echo "<script type='application/ld+json'>" . wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
    }

    /**
     * Build schema for a per-post selected type.
     *
     * @param string  $selected Schema type from the meta box.
     * @param WP_Post $post
     * @return array|null
     */
    private static function build_for_type($selected, $post) {
        switch ($selected) {
            case 'Organization':
                return self::load_file('organization');
            case 'LocalBusiness':
                return self::load_file('local-business');
            case 'Person':
                return self::load_file('profile-page');
            case 'FAQ':
                return self::load_file('faq');
            case 'Article':
            case 'BlogPosting':
            case 'NewsArticle':
            case 'WebPage':
                return self::build_article($selected, $post);
            default:
                return self::build_generic($selected, $post);
        }
    }

    /**
     * Build an Article-family schema for the post.
     */
    private static function build_article($type, $post) {
        $author_id = (int) $post->post_author;

        $schema = [
            "@context"      => "https://schema.org",
            "@type"         => $type,
            "headline"      => wp_strip_all_tags( get_the_title( $post ) ),
            "author"        => [
                "@type" => "Person",
                "name"  => wp_strip_all_tags( get_the_author_meta( 'display_name', $author_id ) ),
            ],
            "datePublished" => get_the_date( 'c', $post ),
            "dateModified"  => get_the_modified_date( 'c', $post ),
            "mainEntityOfPage" => [
                "@type" => "WebPage",
                "@id"   => esc_url_raw( get_permalink( $post ) ),
            ],
            "publisher"     => [
                "@type" => "Organization",
                "name"  => wp_strip_all_tags( get_bloginfo( 'name' ) ),
            ],
        ];

        $logo = get_site_icon_url();
        if ( $logo ) {
            $schema['publisher']['logo'] = [ "@type" => "ImageObject", "url" => esc_url_raw( $logo ) ];
        }

        if ( has_post_thumbnail( $post ) ) {
            $img = wp_get_attachment_image_url( get_post_thumbnail_id( $post ), 'full' );
            if ( $img ) {
                $schema['image'] = esc_url_raw( $img );
            }
        }

        return apply_filters( 'smart_seo_post_schema', $schema, $post, $type );
    }

    /**
     * Build a minimal, valid schema for any other selected @type.
     */
    private static function build_generic($type, $post) {
        $schema = [
            "@context"      => "https://schema.org",
            "@type"         => $type,
            "name"          => wp_strip_all_tags( get_the_title( $post ) ),
            "url"           => esc_url_raw( get_permalink( $post ) ),
            "datePublished" => get_the_date( 'c', $post ),
            "dateModified"  => get_the_modified_date( 'c', $post ),
        ];

        $desc = has_excerpt( $post )
            ? get_the_excerpt( $post )
            : wp_trim_words( Smart_SEO_Meta_Templates::plain_text( $post->post_content ), 30, '' );
        if ( $desc ) {
            $schema['description'] = wp_strip_all_tags( $desc );
        }

        if ( has_post_thumbnail( $post ) ) {
            $img = wp_get_attachment_image_url( get_post_thumbnail_id( $post ), 'full' );
            if ( $img ) {
                $schema['image'] = esc_url_raw( $img );
            }
        }

        return apply_filters( 'smart_seo_post_schema', $schema, $post, $type );
    }

    /**
     * Load a schema file template by page context.
     *
     * @return array|null
     */
    private static function load_context_schema() {
        return self::load_file( self::detect_schema_type() );
    }

    /**
     * Safely load a /schema/{type}-schema.php template.
     *
     * @param string $type
     * @return array|null
     */
    private static function load_file($type) {
        $allowed_types = ['article', 'faq', 'profile-page', 'organization', 'local-business'];
        if (!in_array($type, $allowed_types, true)) {
            return null;
        }

        $schema_file = plugin_dir_path(__FILE__) . "../schema/{$type}-schema.php";
        if (!file_exists($schema_file)) {
            return null;
        }

        $schema_data = require $schema_file;
        return is_array($schema_data) ? $schema_data : null;
    }

    private static function detect_schema_type() {
        if (is_singular('post')) return 'article';
        if (is_page('faq')) return 'faq';
        if (is_page('about')) return 'profile-page';
        if (is_front_page()) return 'organization';
        if (is_page('contact') || is_page('services')) return 'local-business';

        return 'organization'; // Default fallback
    }
}

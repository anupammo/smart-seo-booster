<?php
defined('ABSPATH') || exit;

/**
 * Front-end meta output authority.
 *
 * This is the single source of truth for <head> SEO tags (description,
 * canonical, robots, Open Graph, Twitter Cards). Per-post overrides from the
 * SEO meta box are merged with automatic fallbacks so every page ships one
 * clean, non-duplicated set of tags.
 */
class Smart_SEO_Core {

    public static function init() {
        add_action('wp_head', [__CLASS__, 'inject_meta_tags'], 1);
        add_filter('pre_get_document_title', [__CLASS__, 'filter_document_title']);
        add_filter('wp_robots', [__CLASS__, 'filter_robots']);
    }

    /**
     * Central robots directive control: per-post robots meta + global no-index rules.
     *
     * @param array $robots wp_robots directive array.
     * @return array
     */
    public static function filter_robots($robots) {
        $options = get_option('smart_seo_options', []);

        // Per-post robots (from the SEO meta box).
        if (is_singular()) {
            $post = get_queried_object();
            if ($post instanceof WP_Post) {
                $directive = get_post_meta($post->ID, '_smart_seo_robots', true);
                if ($directive) {
                    if (strpos($directive, 'noindex') !== false) {
                        $robots['noindex'] = true;
                    }
                    if (strpos($directive, 'nofollow') !== false) {
                        $robots['nofollow'] = true;
                    }
                    if (strpos($directive, 'noarchive') !== false) {
                        $robots['noarchive'] = true;
                    }
                    if (strpos($directive, 'nosnippet') !== false) {
                        $robots['nosnippet'] = true;
                    }
                }
            }
        }

        // Global no-index rules for low-value archive views.
        $rules = [
            'noindex_archives'  => is_category() || is_tag() || is_tax(),
            'noindex_author'    => is_author(),
            'noindex_date'      => is_date(),
            'noindex_search'    => is_search(),
            'noindex_paginated' => is_paged(),
        ];
        foreach ($rules as $key => $matches) {
            if ($matches && !empty($options[$key])) {
                $robots['noindex'] = true;
            }
        }

        if (!empty($robots['noindex'])) {
            unset($robots['index']);
            if (empty($robots['nofollow'])) {
                $robots['follow'] = true;
            }
        }

        return $robots;
    }

    /**
     * Apply the per-post SEO title, then the global title template.
     * Returns '' to let WordPress compute the title normally.
     *
     * @param string $title Incoming (usually empty) title.
     * @return string
     */
    public static function filter_document_title($title) {
        if (is_admin()) {
            return $title;
        }

        if (is_singular()) {
            $post    = get_queried_object();
            $post_id = ($post instanceof WP_Post) ? $post->ID : 0;

            if ($post_id) {
                $custom = get_post_meta($post_id, '_smart_seo_title', true);
                if ($custom) {
                    return $custom;
                }
            }

            $options = get_option('smart_seo_options', []);
            if (!empty($options['title_template'])) {
                $parsed = Smart_SEO_Meta_Templates::parse($options['title_template'], $post instanceof WP_Post ? $post : null);
                if ($parsed !== '') {
                    return $parsed;
                }
            }
        }

        return $title;
    }

    public static function inject_meta_tags() {
        if (is_admin()) {
            return;
        }

        $is_singular = is_singular();
        $post        = $is_singular ? get_queried_object() : null;
        $post_id     = ($post instanceof WP_Post) ? $post->ID : 0;

        // Webmaster verification tags (homepage).
        if (is_front_page()) {
            self::output_verification();
        }

        // --- Resolve values: per-post override → automatic fallback ---------
        $title = $is_singular ? get_the_title($post_id) : get_bloginfo('name');
        $desc  = self::resolve_description($is_singular, $post, $post_id);

        $canonical = $post_id ? get_post_meta($post_id, '_smart_seo_canonical', true) : '';
        if (!$canonical && $is_singular) {
            $canonical = get_permalink($post_id);
        }

        $keywords = $post_id ? get_post_meta($post_id, '_smart_seo_keywords', true) : '';
        // Robots directives are handled centrally via the wp_robots filter.

        // Open Graph
        $og_title = $post_id ? get_post_meta($post_id, '_smart_seo_og_title', true) : '';
        $og_title = $og_title ?: $title;
        $og_desc  = $post_id ? get_post_meta($post_id, '_smart_seo_og_description', true) : '';
        $og_desc  = $og_desc ?: $desc;
        $og_type  = $post_id ? get_post_meta($post_id, '_smart_seo_og_type', true) : '';
        $og_type  = $og_type ?: (is_singular('post') ? 'article' : 'website');
        $og_image = self::resolve_og_image($post_id);
        $og_url   = $is_singular ? get_permalink($post_id) : home_url('/');

        // Twitter
        $tw_card  = $post_id ? get_post_meta($post_id, '_smart_seo_twitter_card', true) : '';
        $tw_card  = $tw_card ?: 'summary_large_image';
        $tw_title = $post_id ? get_post_meta($post_id, '_smart_seo_twitter_title', true) : '';
        $tw_title = $tw_title ?: $og_title;
        $tw_desc  = $post_id ? get_post_meta($post_id, '_smart_seo_twitter_description', true) : '';
        $tw_desc  = $tw_desc ?: $og_desc;
        $tw_image = $post_id ? get_post_meta($post_id, '_smart_seo_twitter_image', true) : '';
        $tw_image = $tw_image ?: $og_image;

        // --- Output (each tag exactly once) ---------------------------------
        if ($desc) {
            printf('<meta name="description" content="%s" />' . "\n", esc_attr($desc));
        }
        if ($keywords) {
            printf('<meta name="keywords" content="%s" />' . "\n", esc_attr($keywords));
        }
        if ($canonical) {
            printf('<link rel="canonical" href="%s" />' . "\n", esc_url($canonical));
        }

        // Open Graph
        printf('<meta property="og:title" content="%s" />' . "\n", esc_attr($og_title));
        if ($og_desc) {
            printf('<meta property="og:description" content="%s" />' . "\n", esc_attr($og_desc));
        }
        printf('<meta property="og:type" content="%s" />' . "\n", esc_attr($og_type));
        printf('<meta property="og:url" content="%s" />' . "\n", esc_url($og_url));
        printf('<meta property="og:site_name" content="%s" />' . "\n", esc_attr(get_bloginfo('name')));
        if ($og_image) {
            printf('<meta property="og:image" content="%s" />' . "\n", esc_url($og_image));
        }

        // Twitter Cards
        printf('<meta name="twitter:card" content="%s" />' . "\n", esc_attr($tw_card));
        printf('<meta name="twitter:title" content="%s" />' . "\n", esc_attr($tw_title));
        if ($tw_desc) {
            printf('<meta name="twitter:description" content="%s" />' . "\n", esc_attr($tw_desc));
        }
        if ($tw_image) {
            printf('<meta name="twitter:image" content="%s" />' . "\n", esc_url($tw_image));
        }
    }

    /**
     * Resolve the meta description: custom field → excerpt → trimmed content → tagline.
     */
    private static function resolve_description($is_singular, $post, $post_id) {
        if ($is_singular && $post_id) {
            $custom = get_post_meta($post_id, '_smart_seo_description', true);
            if ($custom) {
                return $custom;
            }

            $options = get_option('smart_seo_options', []);
            if (!empty($options['description_template'])) {
                $parsed = Smart_SEO_Meta_Templates::parse($options['description_template'], $post instanceof WP_Post ? $post : null);
                if ($parsed !== '') {
                    return $parsed;
                }
            }

            if ($post instanceof WP_Post && $post->post_excerpt) {
                return wp_strip_all_tags($post->post_excerpt);
            }
            if ($post instanceof WP_Post) {
                return wp_trim_words(wp_strip_all_tags($post->post_content), 30, '…');
            }
        }
        return wp_strip_all_tags(get_bloginfo('description'));
    }

    /**
     * Output webmaster verification meta tags on the homepage.
     */
    private static function output_verification() {
        $options = get_option('smart_seo_options', []);
        $map = [
            'verify_google'    => 'google-site-verification',
            'verify_bing'      => 'msvalidate.01',
            'verify_pinterest' => 'p:domain_verify',
            'verify_yandex'    => 'yandex-verification',
        ];
        foreach ($map as $key => $meta_name) {
            if (!empty($options[$key])) {
                printf(
                    '<meta name="%s" content="%s" />' . "\n",
                    esc_attr($meta_name),
                    esc_attr($options[$key])
                );
            }
        }
    }

    /**
     * Resolve the OG image: custom field → featured image → none.
     */
    private static function resolve_og_image($post_id) {
        if (!$post_id) {
            return '';
        }
        $custom = get_post_meta($post_id, '_smart_seo_og_image', true);
        if ($custom) {
            return $custom;
        }
        if (has_post_thumbnail($post_id)) {
            $src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large');
            if ($src && !empty($src[0])) {
                return $src[0];
            }
        }
        return '';
    }
}

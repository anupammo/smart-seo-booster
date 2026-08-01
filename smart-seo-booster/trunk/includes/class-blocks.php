<?php
defined('ABSPATH') || exit;

/**
 * Gutenberg blocks (server-rendered, no build step) + block patterns.
 *
 * Blocks: social share, call-to-action, breadcrumb, and post dates.
 * All output is semantic, theme-agnostic HTML with self-contained SVG icons.
 */
class Smart_SEO_Blocks {

    /**
     * FAQ/HowTo JSON-LD collected while blocks render mid-content, flushed
     * together in wp_footer (consistent with how Smart_SEO_Schema_Generator
     * outputs the page's main schema, and safer than echoing <script> tags
     * from inside render_callback while content is still being assembled).
     */
    private static $schema_queue = [];

    public static function init() {
        add_action('init', [__CLASS__, 'register']);
        add_action('init', [__CLASS__, 'register_patterns'], 20);
        add_action('wp_enqueue_scripts', [__CLASS__, 'front_styles']);
        add_filter('block_categories_all', [__CLASS__, 'block_category']);
        add_action('wp_footer', [__CLASS__, 'output_block_schema']);
    }

    /**
     * Groups all Smart SEO blocks under their own inserter category (instead
     * of the generic "Widgets" bucket) so they're easy to find as a set.
     * Block category icons are registered as a plain dashicon slug — there's
     * no PHP-side way to pass a custom image here, unlike individual block/
     * Placeholder icons — so this stays a dashicon rather than the brand PNG.
     */
    public static function block_category( $categories ) {
        return array_merge(
            [ [ 'slug' => 'smart-seo', 'title' => __( 'Smart SEO', 'smart-seo-booster' ), 'icon' => 'chart-line' ] ],
            $categories
        );
    }

    public static function register() {
        if ( ! function_exists('register_block_type') ) {
            return;
        }

        $ver = defined('SMART_SEO_BOOSTER_VERSION') ? SMART_SEO_BOOSTER_VERSION : false;
        wp_register_script(
            'smart-seo-blocks',
            plugin_dir_url(__FILE__) . '../js/blocks.js',
            ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
            $ver,
            true
        );
        if ( function_exists('wp_set_script_translations') ) {
            wp_set_script_translations('smart-seo-blocks', 'smart-seo-booster');
        }
        wp_localize_script( 'smart-seo-blocks', 'smartSeoBlocksBrand', [
            'iconUrl' => SMART_SEO_BOOSTER_ICON_URL,
        ] );
        wp_register_style('smart-seo-blocks', plugin_dir_url(__FILE__) . '../css/blocks.css', [], $ver);
        wp_register_script('smart-seo-blocks-front', plugin_dir_url(__FILE__) . '../js/blocks-front.js', [], $ver, true);

        register_block_type('smart-seo/social-share', [
            'editor_script'   => 'smart-seo-blocks',
            'style'           => 'smart-seo-blocks',
            'render_callback' => [__CLASS__, 'render_social'],
            'attributes'      => [
                'networks' => [ 'type' => 'array', 'default' => ['x', 'facebook', 'linkedin', 'whatsapp', 'email', 'copy'] ],
                'align'    => [ 'type' => 'string', 'default' => 'left' ],
            ],
        ]);

        register_block_type('smart-seo/cta', [
            'editor_script'   => 'smart-seo-blocks',
            'style'           => 'smart-seo-blocks',
            'render_callback' => [__CLASS__, 'render_cta'],
            'attributes'      => [
                'heading'    => [ 'type' => 'string', 'default' => '' ],
                'text'       => [ 'type' => 'string', 'default' => '' ],
                'buttonText' => [ 'type' => 'string', 'default' => '' ],
                'buttonUrl'  => [ 'type' => 'string', 'default' => '' ],
                'variant'    => [ 'type' => 'string', 'default' => 'primary' ],
            ],
        ]);

        register_block_type('smart-seo/breadcrumb', [
            'editor_script'   => 'smart-seo-blocks',
            'style'           => 'smart-seo-blocks',
            'render_callback' => [__CLASS__, 'render_breadcrumb'],
        ]);

        register_block_type('smart-seo/post-dates', [
            'editor_script'   => 'smart-seo-blocks',
            'style'           => 'smart-seo-blocks',
            'render_callback' => [__CLASS__, 'render_dates'],
            'attributes'      => [
                'showPublished' => [ 'type' => 'boolean', 'default' => true ],
                'showModified'  => [ 'type' => 'boolean', 'default' => true ],
            ],
        ]);

        register_block_type('smart-seo/faq', [
            'editor_script'   => 'smart-seo-blocks',
            'style'           => 'smart-seo-blocks',
            'render_callback' => [__CLASS__, 'render_faq'],
            'attributes'      => [
                'title' => [ 'type' => 'string', 'default' => __( 'Frequently Asked Questions', 'smart-seo-booster' ) ],
                'items' => [ 'type' => 'array', 'default' => [ [ 'question' => '', 'answer' => '' ] ] ],
            ],
        ]);

        register_block_type('smart-seo/howto', [
            'editor_script'   => 'smart-seo-blocks',
            'style'           => 'smart-seo-blocks',
            'render_callback' => [__CLASS__, 'render_howto'],
            'attributes'      => [
                'title'       => [ 'type' => 'string', 'default' => '' ],
                'description' => [ 'type' => 'string', 'default' => '' ],
                'totalTime'   => [ 'type' => 'string', 'default' => '' ],
                'steps'       => [ 'type' => 'array', 'default' => [ [ 'name' => '', 'text' => '' ] ] ],
            ],
        ]);

        register_block_type('smart-seo/toc', [
            'editor_script'   => 'smart-seo-blocks',
            'style'           => 'smart-seo-blocks',
            'render_callback' => [ 'Smart_SEO_TOC', 'render' ],
            'attributes'      => [
                'title'    => [ 'type' => 'string', 'default' => __( 'Table of Contents', 'smart-seo-booster' ) ],
                'minLevel' => [ 'type' => 'number', 'default' => 2 ],
                'maxLevel' => [ 'type' => 'number', 'default' => 3 ],
            ],
        ]);
    }

    public static function front_styles() {
        // Registered above; enqueue when a block is present (WP auto-enqueues
        // block styles, but this guarantees availability for older cores).
        if ( function_exists('has_block') && ( has_block('smart-seo/social-share') || has_block('smart-seo/cta') || has_block('smart-seo/breadcrumb') || has_block('smart-seo/post-dates') || has_block('smart-seo/local-business') || has_block('smart-seo/faq') || has_block('smart-seo/howto') || has_block('smart-seo/toc') ) ) {
            wp_enqueue_style('smart-seo-blocks');
        }
    }

    /* --------------------------------------------------------------------- */

    private static function share_networks() {
        $url   = rawurlencode( get_permalink() ?: home_url( add_query_arg( [] ) ) );
        $title = rawurlencode( wp_get_document_title() );
        return [
            'x'        => [ 'label' => 'X', 'href' => "https://twitter.com/intent/tweet?url={$url}&text={$title}" ],
            'facebook' => [ 'label' => 'Facebook', 'href' => "https://www.facebook.com/sharer/sharer.php?u={$url}" ],
            'linkedin' => [ 'label' => 'LinkedIn', 'href' => "https://www.linkedin.com/sharing/share-offsite/?url={$url}" ],
            'whatsapp' => [ 'label' => 'WhatsApp', 'href' => "https://api.whatsapp.com/send?text={$title}%20{$url}" ],
            'reddit'   => [ 'label' => 'Reddit', 'href' => "https://www.reddit.com/submit?url={$url}&title={$title}" ],
            'email'    => [ 'label' => 'Email', 'href' => "mailto:?subject={$title}&body={$url}" ],
            'copy'     => [ 'label' => __( 'Copy link', 'smart-seo-booster' ), 'href' => '' ],
        ];
    }

    private static function icon( $key ) {
        // Compact, self-contained SVG glyphs (24x24). Decorative.
        $icons = [
            'x'        => '<path d="M18.9 2H22l-7.5 8.6L23 22h-6.8l-5.3-6.9L4.8 22H2l8-9.2L1.3 2h6.9l4.8 6.3L18.9 2Zm-2.4 18h1.9L7.6 4H5.6l10.9 16Z"/>',
            'facebook' => '<path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12Z"/>',
            'linkedin' => '<path d="M6.9 8.4H3.5V21h3.4V8.4ZM5.2 3a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm15.3 10.7c0-3-1.6-4.4-3.8-4.4-1.7 0-2.5.9-2.9 1.6V8.4H10.5V21h3.4v-6.9c0-1.5.6-2.4 1.9-2.4s1.8.9 1.8 2.4V21h3.4v-7.3Z"/>',
            'whatsapp' => '<path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.8 5-1.3A10 10 0 1 0 12 2Zm5.6 14.1c-.2.6-1.2 1.2-1.7 1.2-.4 0-1 .1-3.2-.9-2.7-1.2-4.4-4-4.5-4.2-.1-.2-1-1.4-1-2.6 0-1.2.6-1.8.9-2 .2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2.1.4 0 .5l-.4.5c-.2.2-.3.4-.1.7.2.3.9 1.4 1.9 2.3 1.3 1.1 2.3 1.4 2.6 1.6.2.1.4.1.5-.1l.7-.8c.2-.2.3-.2.6-.1l1.9.9c.3.1.4.2.5.3.1.3.1.6-.1 1.1Z"/>',
            'reddit'   => '<path d="M22 12c0-1.2-1-2.2-2.2-2.2-.6 0-1.1.2-1.5.6a10.7 10.7 0 0 0-5.4-1.7l.9-4.2 2.9.6a1.6 1.6 0 1 0 .2-1l-3.3-.7c-.2 0-.4.1-.4.3l-1 4.7c-2 .1-3.9.7-5.4 1.7-.4-.4-.9-.6-1.5-.6A2.2 2.2 0 0 0 2 12c0 .8.5 1.6 1.2 2v.6C4.2 17.7 7.8 20 12 20s7.8-2.3 8.8-5.4V14c.7-.4 1.2-1.2 1.2-2Zm-14 1.5a1.4 1.4 0 1 1 2.8 0 1.4 1.4 0 0 1-2.8 0Zm7.8 3.6c-1 1-3 1.1-3.8 1.1-.8 0-2.8-.1-3.8-1.1a.4.4 0 0 1 .6-.6c.6.6 2 .8 3.2.8 1.2 0 2.6-.2 3.2-.8a.4.4 0 1 1 .6.6Zm-.2-2.2a1.4 1.4 0 1 1 0-2.8 1.4 1.4 0 0 1 0 2.8Z"/>',
            'email'    => '<path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/>',
            'copy'     => '<path d="M16 1H4a2 2 0 0 0-2 2v12h2V3h12V1Zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Zm0 16H8V7h11v14Z"/>',
        ];
        $path = $icons[ $key ] ?? '';
        return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true" focusable="false">' . $path . '</svg>';
    }

    public static function render_social( $attributes ) {
        $networks = ! empty( $attributes['networks'] ) && is_array( $attributes['networks'] ) ? $attributes['networks'] : ['x', 'facebook', 'linkedin', 'email', 'copy'];
        $align    = isset( $attributes['align'] ) ? sanitize_html_class( $attributes['align'] ) : 'left';
        $all      = self::share_networks();

        $out = '<div class="ssb-share is-' . esc_attr( $align ) . '">';
        foreach ( $networks as $key ) {
            $key = sanitize_key( $key );
            if ( ! isset( $all[ $key ] ) ) {
                continue;
            }
            $n = $all[ $key ];
            if ( 'copy' === $key ) {
                $out .= '<button type="button" class="ssb-share-btn ssb-share-copy" data-url="' . esc_url( get_permalink() ) . '" aria-label="' . esc_attr( $n['label'] ) . '">' . self::icon( $key ) . '<span>' . esc_html( $n['label'] ) . '</span></button>';
            } else {
                $out .= '<a class="ssb-share-btn is-' . esc_attr( $key ) . '" href="' . esc_url( $n['href'] ) . '" target="_blank" rel="noopener nofollow" aria-label="' . esc_attr( $n['label'] ) . '">' . self::icon( $key ) . '<span>' . esc_html( $n['label'] ) . '</span></a>';
            }
        }
        $out .= '</div>';

        // Copy-to-clipboard behavior is provided by the enqueued front script.
        if ( function_exists('wp_enqueue_script') ) {
            wp_enqueue_script('smart-seo-blocks-front');
        }

        return $out;
    }

    public static function render_cta( $attributes ) {
        $heading = isset( $attributes['heading'] ) ? wp_strip_all_tags( $attributes['heading'] ) : '';
        $text    = isset( $attributes['text'] ) ? wp_kses_post( $attributes['text'] ) : '';
        $btn     = isset( $attributes['buttonText'] ) ? wp_strip_all_tags( $attributes['buttonText'] ) : '';
        $url     = isset( $attributes['buttonUrl'] ) ? esc_url( $attributes['buttonUrl'] ) : '';
        $variant = isset( $attributes['variant'] ) ? sanitize_html_class( $attributes['variant'] ) : 'primary';

        if ( '' === $heading && '' === $text && '' === $btn ) {
            return '';
        }

        $out  = '<div class="ssb-cta is-' . esc_attr( $variant ) . '">';
        if ( $heading ) {
            $out .= '<h3 class="ssb-cta-title">' . esc_html( $heading ) . '</h3>';
        }
        if ( $text ) {
            $out .= '<div class="ssb-cta-text">' . $text . '</div>';
        }
        if ( $btn && $url ) {
            $out .= '<a class="ssb-cta-btn" href="' . $url . '">' . esc_html( $btn ) . '</a>';
        }
        $out .= '</div>';
        return $out;
    }

    public static function render_breadcrumb() {
        if ( ! class_exists('Smart_SEO_Breadcrumbs') ) {
            return '';
        }
        // Force render regardless of the global toggle when placed explicitly.
        add_filter('smart_seo_force_breadcrumbs', '__return_true');
        return Smart_SEO_Breadcrumbs::render();
    }

    public static function render_dates( $attributes ) {
        if ( ! is_singular() ) {
            return '';
        }
        $post = get_queried_object();
        if ( ! $post instanceof WP_Post ) {
            return '';
        }
        $show_pub = ! isset( $attributes['showPublished'] ) || $attributes['showPublished'];
        $show_mod = ! isset( $attributes['showModified'] ) || $attributes['showModified'];

        $out = '<div class="ssb-dates">';
        if ( $show_pub ) {
            $out .= '<span class="ssb-date ssb-date-pub"><time datetime="' . esc_attr( get_the_date( 'c', $post ) ) . '">'
                . esc_html( sprintf( /* translators: %s: date */ __( 'Published %s', 'smart-seo-booster' ), get_the_date( '', $post ) ) ) . '</time></span>';
        }
        if ( $show_mod && get_the_modified_time( 'U', $post ) > get_the_time( 'U', $post ) ) {
            $out .= '<span class="ssb-date ssb-date-mod"><time datetime="' . esc_attr( get_the_modified_date( 'c', $post ) ) . '">'
                . esc_html( sprintf( /* translators: %s: date */ __( 'Updated %s', 'smart-seo-booster' ), get_the_modified_date( '', $post ) ) ) . '</time></span>';
        }
        $out .= '</div>';
        return $out;
    }

    /**
     * Render an FAQ accordion (native <details>/<summary>, no JS required)
     * and queue FAQPage schema — the same feature Yoast/RankMath ship as a
     * premium/core block, since "how do FAQ rich results happen" is one of
     * the most common asks from users of every SEO plugin.
     */
    public static function render_faq( $attributes ) {
        $title = isset( $attributes['title'] ) ? wp_strip_all_tags( $attributes['title'] ) : '';
        $items = self::clean_qa_items( $attributes['items'] ?? [] );
        if ( empty( $items ) ) {
            return '';
        }

        $out = '<div class="ssb-faq">';
        if ( $title ) {
            $out .= '<h2 class="ssb-faq-title">' . esc_html( $title ) . '</h2>';
        }
        foreach ( $items as $item ) {
            $out .= '<details class="ssb-faq-item"><summary>' . esc_html( $item['question'] ) . '</summary><div class="ssb-faq-answer">' . wp_kses_post( $item['answer'] ) . '</div></details>';
        }
        $out .= '</div>';

        $options = get_option( 'smart_seo_options', [] );
        if ( ! empty( $options['enable_schema'] ) ) {
            self::$schema_queue[] = [
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => array_map( static function ( $item ) {
                    return [
                        '@type'          => 'Question',
                        'name'           => $item['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text'  => wp_strip_all_tags( $item['answer'] ),
                        ],
                    ];
                }, $items ),
            ];
        }

        return $out;
    }

    /**
     * Render a numbered HowTo guide and queue HowTo schema.
     */
    public static function render_howto( $attributes ) {
        $title       = isset( $attributes['title'] ) ? wp_strip_all_tags( $attributes['title'] ) : '';
        $description = isset( $attributes['description'] ) ? wp_kses_post( $attributes['description'] ) : '';
        $total_time  = isset( $attributes['totalTime'] ) ? trim( $attributes['totalTime'] ) : '';
        $steps       = self::clean_steps( $attributes['steps'] ?? [] );
        if ( empty( $steps ) ) {
            return '';
        }

        $out = '<div class="ssb-howto">';
        if ( $title ) {
            $out .= '<h2 class="ssb-howto-title">' . esc_html( $title ) . '</h2>';
        }
        if ( $description ) {
            $out .= '<div class="ssb-howto-desc">' . $description . '</div>';
        }
        $out .= '<ol class="ssb-howto-steps">';
        foreach ( $steps as $step ) {
            $out .= '<li><span class="ssb-howto-step-name">' . esc_html( $step['name'] ) . '</span>';
            if ( '' !== $step['text'] ) {
                $out .= '<div class="ssb-howto-step-text">' . wp_kses_post( $step['text'] ) . '</div>';
            }
            $out .= '</li>';
        }
        $out .= '</ol></div>';

        $options = get_option( 'smart_seo_options', [] );
        if ( ! empty( $options['enable_schema'] ) ) {
            $schema = [
                '@context' => 'https://schema.org',
                '@type'    => 'HowTo',
                'name'     => $title ?: __( 'How to', 'smart-seo-booster' ),
                'step'     => array_map( static function ( $step ) {
                    return [
                        '@type' => 'HowToStep',
                        'name'  => $step['name'],
                        'text'  => wp_strip_all_tags( $step['text'] ),
                    ];
                }, $steps ),
            ];
            if ( $description ) {
                $schema['description'] = wp_strip_all_tags( $description );
            }
            // Only pass through a totalTime that actually looks like an ISO
            // 8601 duration (e.g. PT30M) — schema.org requires that format,
            // and a free-text value would just be silently invalid.
            if ( $total_time && preg_match( '/^P(?:\d+Y)?(?:\d+M)?(?:\d+D)?(?:T(?:\d+H)?(?:\d+M)?(?:\d+S)?)?$/', $total_time ) ) {
                $schema['totalTime'] = $total_time;
            }
            self::$schema_queue[] = $schema;
        }

        return $out;
    }

    /**
     * Print any FAQ/HowTo schema queued by blocks rendered earlier in the
     * page, once, in the footer.
     */
    public static function output_block_schema() {
        foreach ( self::$schema_queue as $schema ) {
            echo "<script type='application/ld+json'>" . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
        }
        self::$schema_queue = [];
    }

    private static function clean_qa_items( $items ) {
        if ( ! is_array( $items ) ) {
            return [];
        }
        $out = [];
        foreach ( $items as $item ) {
            $q = isset( $item['question'] ) ? wp_strip_all_tags( $item['question'] ) : '';
            $a = isset( $item['answer'] ) ? wp_kses_post( $item['answer'] ) : '';
            if ( '' !== $q && '' !== $a ) {
                $out[] = [ 'question' => $q, 'answer' => $a ];
            }
        }
        return $out;
    }

    private static function clean_steps( $steps ) {
        if ( ! is_array( $steps ) ) {
            return [];
        }
        $out = [];
        foreach ( $steps as $step ) {
            $name = isset( $step['name'] ) ? wp_strip_all_tags( $step['name'] ) : '';
            $text = isset( $step['text'] ) ? wp_kses_post( $step['text'] ) : '';
            if ( '' !== $name ) {
                $out[] = [ 'name' => $name, 'text' => $text ];
            }
        }
        return $out;
    }

    /* --------------------------------------------------------------------- */

    public static function register_patterns() {
        if ( ! function_exists('register_block_pattern') ) {
            return;
        }
        if ( function_exists('register_block_pattern_category') ) {
            register_block_pattern_category('smart-seo', [ 'label' => __( 'Smart SEO', 'smart-seo-booster' ) ]);
        }

        register_block_pattern('smart-seo/article-footer', [
            'title'      => __( 'Article footer (dates + share)', 'smart-seo-booster' ),
            'categories' => [ 'smart-seo' ],
            'content'    => "<!-- wp:separator --><hr class=\"wp-block-separator\"/><!-- /wp:separator -->\n<!-- wp:smart-seo/post-dates /-->\n<!-- wp:smart-seo/social-share /-->",
        ]);

        register_block_pattern('smart-seo/cta-banner', [
            'title'      => __( 'CTA banner', 'smart-seo-booster' ),
            'categories' => [ 'smart-seo' ],
            'content'    => '<!-- wp:smart-seo/cta {"heading":"Ready to get started?","text":"Join thousands of happy users today.","buttonText":"Get started","buttonUrl":"#","variant":"gradient"} /-->',
        ]);

        register_block_pattern('smart-seo/breadcrumb-top', [
            'title'      => __( 'Breadcrumb bar', 'smart-seo-booster' ),
            'categories' => [ 'smart-seo' ],
            'content'    => '<!-- wp:smart-seo/breadcrumb /-->',
        ]);
    }
}

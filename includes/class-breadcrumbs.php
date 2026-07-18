<?php
defined('ABSPATH') || exit;

/**
 * Breadcrumbs — HTML trail, shortcode, template tag, and BreadcrumbList JSON-LD.
 */
class Smart_SEO_Breadcrumbs {

    public static function init() {
        add_shortcode('smart_seo_breadcrumbs', [__CLASS__, 'shortcode']);
        add_action('wp_head', [__CLASS__, 'output_schema'], 5);
    }

    public static function enabled() {
        $options = get_option('smart_seo_options', []);
        return ! empty( $options['enable_breadcrumbs'] );
    }

    /**
     * Build the ordered trail as an array of [ 'name' => , 'url' => ] items.
     * The final (current) item has an empty url.
     *
     * @return array<int,array{name:string,url:string}>
     */
    public static function trail() {
        $options   = get_option('smart_seo_options', []);
        $home_label = isset( $options['breadcrumb_home_label'] ) && $options['breadcrumb_home_label'] !== ''
            ? $options['breadcrumb_home_label']
            : __( 'Home', 'smart-seo-booster' );

        $items = [ [ 'name' => $home_label, 'url' => home_url( '/' ) ] ];

        if ( is_front_page() ) {
            return $items;
        }

        if ( is_singular() ) {
            $post = get_queried_object();

            if ( $post instanceof WP_Post ) {
                // Post-type archive link (for non-page types with an archive).
                $pt = get_post_type_object( $post->post_type );
                if ( $pt && ! empty( $pt->has_archive ) ) {
                    $items[] = [ 'name' => $pt->labels->name, 'url' => get_post_type_archive_link( $post->post_type ) ];
                } elseif ( $post->post_type === 'post' ) {
                    $cats = get_the_category( $post->ID );
                    if ( ! empty( $cats ) ) {
                        $items[] = [ 'name' => $cats[0]->name, 'url' => get_category_link( $cats[0]->term_id ) ];
                    }
                }

                // Ancestor pages.
                foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor_id ) {
                    $items[] = [ 'name' => get_the_title( $ancestor_id ), 'url' => get_permalink( $ancestor_id ) ];
                }

                $items[] = [ 'name' => get_the_title( $post ), 'url' => '' ];
            }
        } elseif ( is_category() || is_tag() || is_tax() ) {
            $term = get_queried_object();
            if ( $term instanceof WP_Term ) {
                $items[] = [ 'name' => $term->name, 'url' => '' ];
            }
        } elseif ( is_post_type_archive() ) {
            $items[] = [ 'name' => post_type_archive_title( '', false ), 'url' => '' ];
        } elseif ( is_author() ) {
            $items[] = [ 'name' => get_the_author(), 'url' => '' ];
        } elseif ( is_search() ) {
            /* translators: %s: search query */
            $items[] = [ 'name' => sprintf( __( 'Search: %s', 'smart-seo-booster' ), get_search_query() ), 'url' => '' ];
        } elseif ( is_404() ) {
            $items[] = [ 'name' => __( '404 Not Found', 'smart-seo-booster' ), 'url' => '' ];
        } elseif ( is_archive() ) {
            $items[] = [ 'name' => get_the_archive_title(), 'url' => '' ];
        }

        /**
         * Filter the breadcrumb trail items.
         *
         * @param array $items
         */
        return apply_filters( 'smart_seo_breadcrumb_trail', $items );
    }

    /**
     * Render the breadcrumb HTML.
     *
     * @param array $args Optional { separator }.
     * @return string
     */
    public static function render( $args = [] ) {
        if ( ! self::enabled() ) {
            return '';
        }

        $options   = get_option('smart_seo_options', []);
        $separator = isset( $args['separator'] ) ? $args['separator']
            : ( isset( $options['breadcrumb_separator'] ) && $options['breadcrumb_separator'] !== '' ? $options['breadcrumb_separator'] : '/' );

        $items = self::trail();
        if ( count( $items ) < 2 && ! is_front_page() ) {
            return '';
        }

        $html  = '<nav class="smart-seo-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'smart-seo-booster' ) . '">';
        $last  = count( $items ) - 1;

        foreach ( $items as $i => $item ) {
            if ( $i > 0 ) {
                $html .= ' <span class="sep" aria-hidden="true">' . esc_html( $separator ) . '</span> ';
            }
            if ( ! empty( $item['url'] ) && $i !== $last ) {
                $html .= '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['name'] ) . '</a>';
            } else {
                $html .= '<span class="current" aria-current="page">' . esc_html( $item['name'] ) . '</span>';
            }
        }

        $html .= '</nav>';
        return $html;
    }

    public static function shortcode( $atts ) {
        $atts = shortcode_atts( [ 'separator' => '' ], $atts, 'smart_seo_breadcrumbs' );
        return self::render( array_filter( $atts ) );
    }

    /**
     * Output BreadcrumbList JSON-LD for the current view.
     */
    public static function output_schema() {
        if ( is_admin() || ! self::enabled() || is_front_page() ) {
            return;
        }

        $items = self::trail();
        if ( count( $items ) < 2 ) {
            return;
        }

        $list     = [];
        $position = 1;
        foreach ( $items as $item ) {
            $entry = [
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => wp_strip_all_tags( $item['name'] ),
            ];
            if ( ! empty( $item['url'] ) ) {
                $entry['item'] = esc_url_raw( $item['url'] );
            }
            $list[] = $entry;
            $position++;
        }

        $schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $list,
        ];

        echo "<script type='application/ld+json'>" . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
    }
}

/**
 * Template tag: echo the breadcrumb trail from a theme.
 */
function smart_seo_breadcrumbs( $args = [] ) {
    echo wp_kses_post( Smart_SEO_Breadcrumbs::render( $args ) );
}

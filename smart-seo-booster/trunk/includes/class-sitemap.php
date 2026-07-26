<?php
defined('ABSPATH') || exit;

/**
 * Lightweight XML sitemap.
 *
 * Serves a sitemap index at /sitemap.xml linking to per-post-type sitemaps
 * (/sitemap-{type}.xml, paginated as /sitemap-{type}-{n}.xml), including
 * featured images. No physical files are written — everything is served via
 * rewrite rules and template_redirect.
 */
class Smart_SEO_Sitemap {

    /** Max URLs per sub-sitemap. */
    const PER_PAGE = 1000;

    public static function init() {
        add_action('init', [__CLASS__, 'add_rewrite_rules']);
        add_filter('query_vars', [__CLASS__, 'add_query_vars']);
        add_action('template_redirect', [__CLASS__, 'maybe_render'], 0);
        add_filter('robots_txt', [__CLASS__, 'add_to_robots'], 10, 2);

        // Optionally disable the core WordPress sitemap to avoid duplicates.
        if ( self::enabled() && ! empty( get_option('smart_seo_options', [])['disable_wp_sitemap'] ) ) {
            add_filter('wp_sitemaps_enabled', '__return_false');
        }
    }

    public static function enabled() {
        $options = get_option('smart_seo_options', []);
        return ! empty( $options['enable_sitemap'] );
    }

    public static function add_rewrite_rules() {
        add_rewrite_rule('^sitemap\.xml$', 'index.php?smart_seo_sitemap=index', 'top');
        add_rewrite_rule('^sitemap-([a-z0-9_-]+?)-(\d+)\.xml$', 'index.php?smart_seo_sitemap=type&smart_seo_sitemap_type=$matches[1]&smart_seo_sitemap_page=$matches[2]', 'top');
        add_rewrite_rule('^sitemap-([a-z0-9_-]+?)\.xml$', 'index.php?smart_seo_sitemap=type&smart_seo_sitemap_type=$matches[1]', 'top');

        // Self-heal after a plugin update: flush once per version so the rules
        // above are registered without the user having to re-save permalinks.
        $version = defined('SMART_SEO_BOOSTER_VERSION') ? SMART_SEO_BOOSTER_VERSION : '0';
        if (get_option('smart_seo_rewrite_v') !== $version) {
            flush_rewrite_rules(false);
            update_option('smart_seo_rewrite_v', $version, false);
        }
    }

    public static function add_query_vars( $vars ) {
        $vars[] = 'smart_seo_sitemap';
        $vars[] = 'smart_seo_sitemap_type';
        $vars[] = 'smart_seo_sitemap_page';
        return $vars;
    }

    /**
     * Post types included in the sitemap.
     *
     * @return string[]
     */
    public static function post_types() {
        $types = get_post_types( [ 'public' => true ], 'names' );
        unset( $types['attachment'] );
        /**
         * Filter the post types included in the sitemap.
         *
         * @param string[] $types
         */
        return apply_filters( 'smart_seo_sitemap_post_types', array_values( $types ) );
    }

    public static function maybe_render() {
        $which = get_query_var('smart_seo_sitemap');
        if ( ! $which ) {
            return;
        }
        if ( ! self::enabled() ) {
            return;
        }

        header('Content-Type: application/xml; charset=UTF-8', true);
        header('X-Robots-Tag: noindex, follow', true);

        if ( $which === 'index' ) {
            self::render_index();
        } else {
            $type = sanitize_key( get_query_var('smart_seo_sitemap_type') );
            $page = max( 1, (int) get_query_var('smart_seo_sitemap_page') );
            self::render_type( $type, $page );
        }
        exit;
    }

    private static function render_index() {
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ( self::post_types() as $type ) {
            $count = (int) wp_count_posts( $type )->publish;
            if ( $count < 1 ) {
                continue;
            }
            $pages = (int) ceil( $count / self::PER_PAGE );
            for ( $p = 1; $p <= $pages; $p++ ) {
                $slug = $pages > 1 ? "sitemap-{$type}-{$p}.xml" : "sitemap-{$type}.xml";
                echo "\t<sitemap>\n";
                echo "\t\t<loc>" . esc_url( home_url( '/' . $slug ) ) . "</loc>\n";
                echo "\t</sitemap>\n";
            }
        }

        echo '</sitemapindex>';
    }

    private static function render_type( $type, $page ) {
        if ( ! in_array( $type, self::post_types(), true ) ) {
            status_header(404);
            exit;
        }

        $query = new WP_Query([
            'post_type'           => $type,
            'post_status'         => 'publish',
            'posts_per_page'      => self::PER_PAGE,
            'paged'               => $page,
            'orderby'             => 'modified',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
            'update_post_term_cache' => false,
        ]);

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ( $query->posts as $post ) {
            // Respect per-post noindex.
            $robots = get_post_meta( $post->ID, '_smart_seo_robots', true );
            if ( $robots && strpos( $robots, 'noindex' ) !== false ) {
                continue;
            }

            echo "\t<url>\n";
            echo "\t\t<loc>" . esc_url( get_permalink( $post ) ) . "</loc>\n";
            echo "\t\t<lastmod>" . esc_html( get_post_modified_time( 'c', true, $post ) ) . "</lastmod>\n";

            if ( has_post_thumbnail( $post ) ) {
                $img = wp_get_attachment_image_url( get_post_thumbnail_id( $post ), 'full' );
                if ( $img ) {
                    echo "\t\t<image:image>\n";
                    echo "\t\t\t<image:loc>" . esc_url( $img ) . "</image:loc>\n";
                    echo "\t\t</image:image>\n";
                }
            }

            echo "\t</url>\n";
        }

        echo '</urlset>';
    }

    /**
     * Append the sitemap reference to robots.txt.
     */
    public static function add_to_robots( $output, $public ) {
        if ( self::enabled() && $public ) {
            $output .= 'Sitemap: ' . esc_url( home_url( '/sitemap.xml' ) ) . "\n";
        }
        return $output;
    }
}

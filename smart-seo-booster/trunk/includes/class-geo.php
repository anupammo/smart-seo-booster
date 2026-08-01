<?php
defined('ABSPATH') || exit;

/**
 * GEO / AI SEO — Generative Engine Optimization.
 *
 * - Serves /llms.txt (a machine-readable site summary for AI assistants).
 * - Optionally asks AI crawlers not to train on the site (robots.txt).
 * - Optionally adds speakable structured data for voice assistants.
 */
class Smart_SEO_GEO {

    /** AI crawler user-agents used for the training opt-out. */
    const AI_BOTS = [
        'GPTBot', 'ChatGPT-User', 'OAI-SearchBot', 'Google-Extended', 'ClaudeBot',
        'Claude-Web', 'anthropic-ai', 'CCBot', 'PerplexityBot', 'Applebot-Extended',
        'Bytespider', 'Amazonbot', 'Meta-ExternalAgent', 'cohere-ai',
    ];

    public static function init() {
        add_action('init', [__CLASS__, 'add_rewrite']);
        add_filter('query_vars', [__CLASS__, 'query_vars']);
        add_action('template_redirect', [__CLASS__, 'maybe_render_llms'], 0);
        add_filter('robots_txt', [__CLASS__, 'robots'], 20, 2);
        add_filter('smart_seo_article_schema', [__CLASS__, 'add_speakable'], 10, 2);
        add_filter('smart_seo_post_schema', [__CLASS__, 'add_speakable'], 10, 2);
    }

    private static function opt( $key ) {
        $o = get_option('smart_seo_options', []);
        return ! empty( $o[ $key ] );
    }

    public static function add_rewrite() {
        add_rewrite_rule('^llms\.txt$', 'index.php?smart_seo_llms=1', 'top');
    }

    public static function query_vars( $vars ) {
        $vars[] = 'smart_seo_llms';
        return $vars;
    }

    public static function robots( $output, $public ) {
        if ( $public && self::opt('block_ai_training') ) {
            $output .= "\n# AI training opt-out (Smart SEO Booster)\n";
            foreach ( self::AI_BOTS as $bot ) {
                $output .= 'User-agent: ' . $bot . "\nDisallow: /\n";
            }
        }
        return $output;
    }

    public static function add_speakable( $schema, $post ) {
        if ( ! self::opt('enable_speakable') || ! is_array( $schema ) ) {
            return $schema;
        }
        $schema['speakable'] = [
            '@type'       => 'SpeakableSpecification',
            'cssSelector' => [ 'h1', '.entry-title', '.entry-content p:first-of-type' ],
        ];
        return $schema;
    }

    public static function maybe_render_llms() {
        if ( ! get_query_var('smart_seo_llms') ) {
            return;
        }
        if ( ! self::opt('enable_llms_txt') ) {
            return;
        }

        header('Content-Type: text/plain; charset=UTF-8', true);
        header('X-Robots-Tag: noindex', true);

        echo '# ' . esc_html( get_bloginfo('name') ) . "\n";
        $tagline = get_bloginfo('description');
        if ( $tagline ) {
            echo '> ' . esc_html( wp_strip_all_tags( $tagline ) ) . "\n";
        }
        echo "\n" . esc_html__( 'Site URL:', 'smart-seo-booster' ) . ' ' . esc_url( home_url('/') ) . "\n\n";

        // Key pages.
        $pages = get_posts([
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => 25,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
            'no_found_rows'  => true,
        ]);
        if ( $pages ) {
            echo "## " . esc_html__( 'Pages', 'smart-seo-booster' ) . "\n";
            foreach ( $pages as $p ) {
                self::line( $p );
            }
            echo "\n";
        }

        // Recent posts.
        $posts = get_posts([
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'no_found_rows'  => true,
        ]);
        if ( $posts ) {
            echo "## " . esc_html__( 'Posts', 'smart-seo-booster' ) . "\n";
            foreach ( $posts as $p ) {
                self::line( $p );
            }
        }

        exit;
    }

    private static function line( $post ) {
        // Respect per-post noindex.
        $robots = get_post_meta( $post->ID, '_smart_seo_robots', true );
        if ( $robots && false !== strpos( $robots, 'noindex' ) ) {
            return;
        }
        $title = wp_strip_all_tags( get_the_title( $post ) );
        $url   = get_permalink( $post );
        $desc  = get_post_meta( $post->ID, '_smart_seo_description', true );
        if ( '' === $desc ) {
            $desc = $post->post_excerpt ? $post->post_excerpt : wp_trim_words( Smart_SEO_Meta_Templates::plain_text( $post->post_content ), 25, '' );
        }
        $desc = wp_strip_all_tags( $desc );

        // Plain-text markdown line; esc_url on the URL, text is stripped.
        echo '- [' . esc_html( $title ) . '](' . esc_url( $url ) . ')';
        if ( $desc ) {
            echo ': ' . esc_html( $desc );
        }
        echo "\n";
    }
}

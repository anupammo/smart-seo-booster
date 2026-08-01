<?php
defined('ABSPATH') || exit;

/**
 * One-click importer from Yoast SEO and Rank Math.
 *
 * Copies existing per-post SEO meta into Smart SEO Booster's fields so users
 * can switch without losing their work. Runs in batches to stay within PHP
 * limits, and never overwrites SEO values that are already set.
 */
class Smart_SEO_Importer {

    const NONCE      = 'smart_seo_import';
    const BATCH_SIZE = 50;

    /** source => [ source_meta_key => target_meta_key ] */
    private static function map( $source ) {
        $maps = [
            'yoast' => [
                '_yoast_wpseo_title'                 => '_smart_seo_title',
                '_yoast_wpseo_metadesc'              => '_smart_seo_description',
                '_yoast_wpseo_focuskw'               => '_smart_seo_focus_keyword',
                '_yoast_wpseo_canonical'             => '_smart_seo_canonical',
                '_yoast_wpseo_opengraph-title'       => '_smart_seo_og_title',
                '_yoast_wpseo_opengraph-description' => '_smart_seo_og_description',
                '_yoast_wpseo_opengraph-image'       => '_smart_seo_og_image',
                '_yoast_wpseo_twitter-title'         => '_smart_seo_twitter_title',
                '_yoast_wpseo_twitter-description'   => '_smart_seo_twitter_description',
                '_yoast_wpseo_twitter-image'         => '_smart_seo_twitter_image',
            ],
            'rankmath' => [
                'rank_math_title'                => '_smart_seo_title',
                'rank_math_description'          => '_smart_seo_description',
                'rank_math_focus_keyword'        => '_smart_seo_focus_keyword',
                'rank_math_canonical_url'        => '_smart_seo_canonical',
                'rank_math_facebook_title'       => '_smart_seo_og_title',
                'rank_math_facebook_description' => '_smart_seo_og_description',
                'rank_math_facebook_image'       => '_smart_seo_og_image',
                'rank_math_twitter_title'        => '_smart_seo_twitter_title',
                'rank_math_twitter_description'  => '_smart_seo_twitter_description',
                'rank_math_twitter_image'        => '_smart_seo_twitter_image',
            ],
        ];
        return $maps[ $source ] ?? [];
    }

    public static function init() {
        add_action('admin_post_smart_seo_import', [__CLASS__, 'handle']);
    }

    public static function render_page() {
        if ( ! current_user_can('manage_options') ) {
            wp_die( esc_html__('You do not have sufficient permissions to access this page.', 'smart-seo-booster') );
        }

        // Auto-continue a multi-batch import.
        $auto_source = isset($_GET['auto_source']) ? sanitize_key( wp_unslash( $_GET['auto_source'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- continuation routing, action itself is nonce-checked
        if ( $auto_source ) {
            $auto_offset = isset($_GET['auto_offset']) ? absint( wp_unslash( $_GET['auto_offset'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- continuation routing
            echo '<div class="wrap ssb-app ssb-adapt"><h1><span class="dashicons dashicons-update" aria-hidden="true"></span> ' . esc_html__( 'Importing…', 'smart-seo-booster' ) . '</h1>';
            echo '<p>' . esc_html__( 'Processing the next batch, please wait…', 'smart-seo-booster' ) . '</p>';
            echo '<form id="smart-seo-auto" method="post" action="' . esc_url( admin_url('admin-post.php') ) . '">';
            wp_nonce_field( self::NONCE, 'smart_seo_import_nonce' );
            echo '<input type="hidden" name="action" value="smart_seo_import" />';
            echo '<input type="hidden" name="source" value="' . esc_attr( $auto_source ) . '" />';
            echo '<input type="hidden" name="offset" value="' . esc_attr( $auto_offset ) . '" />';
            echo '<noscript>';
            submit_button( __( 'Continue', 'smart-seo-booster' ) );
            echo '</noscript>';
            echo '</form>';
            echo '<script>document.getElementById("smart-seo-auto").submit();</script>';
            echo '</div>';
            return;
        }

        $has_yoast    = self::source_available('yoast');
        $has_rankmath = self::source_available('rankmath');

        echo '<div class="wrap smart-seo-settings ssb-app ssb-adapt">';
        echo '<h1><img src="' . esc_url( SMART_SEO_BOOSTER_LINE_ICON_URL ) . '" width="26" height="26" alt="" class="ssb-h1-icon" /> ' . esc_html__( 'Import SEO Data', 'smart-seo-booster' ) . '</h1>';
        echo '<p>' . esc_html__( 'Copy your existing SEO titles, descriptions, social tags and canonicals from another plugin. Existing Smart SEO values are never overwritten.', 'smart-seo-booster' ) . '</p>';

        // Progress notice after a batch.
        $done = isset($_GET['imported']) ? absint( wp_unslash( $_GET['imported'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only
        if ( isset($_GET['complete']) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only
            echo '<div class="notice notice-success"><p>' . esc_html__( 'Import complete.', 'smart-seo-booster' ) . '</p></div>';
        }

        echo '<div class="card smart-seo-card">';
        self::render_source_row( 'yoast', __( 'Yoast SEO', 'smart-seo-booster' ), $has_yoast );
        self::render_source_row( 'rankmath', __( 'Rank Math', 'smart-seo-booster' ), $has_rankmath );
        echo '</div>';
        echo '</div>';
    }

    private static function render_source_row( $source, $label, $available ) {
        echo '<p>';
        echo '<strong>' . esc_html( $label ) . '</strong> — ';
        if ( $available ) {
            echo '<form method="post" action="' . esc_url( admin_url('admin-post.php') ) . '" style="display:inline;">';
            wp_nonce_field( self::NONCE, 'smart_seo_import_nonce' );
            echo '<input type="hidden" name="action" value="smart_seo_import" />';
            echo '<input type="hidden" name="source" value="' . esc_attr( $source ) . '" />';
            echo '<input type="hidden" name="offset" value="0" />';
            submit_button( sprintf( /* translators: %s: source plugin name */ __( 'Import from %s', 'smart-seo-booster' ), $label ), 'primary', 'submit', false );
            echo '</form>';
        } else {
            echo '<em>' . esc_html__( 'No data found.', 'smart-seo-booster' ) . '</em>';
        }
        echo '</p>';
    }

    /**
     * Whether any posts carry meta from the given source.
     * Uses WP_Query (no direct DB) with a short-lived cache.
     */
    private static function source_available( $source ) {
        $keys = array_keys( self::map( $source ) );
        if ( empty( $keys ) ) {
            return false;
        }

        $cache_key = 'smart_seo_import_has_' . $source;
        $found     = wp_cache_get( $cache_key, 'smart_seo' );
        if ( false !== $found ) {
            return (bool) $found;
        }

        $meta_query = [ 'relation' => 'OR' ];
        foreach ( $keys as $key ) {
            $meta_query[] = [ 'key' => $key, 'compare' => 'EXISTS' ];
        }

        $query = new WP_Query([
            'post_type'      => 'any',
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'meta_query'     => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- one-off admin existence check, cached
        ]);

        $found = ! empty( $query->posts );
        wp_cache_set( $cache_key, $found ? 1 : 0, 'smart_seo', 300 );

        return $found;
    }

    public static function handle() {
        if ( ! current_user_can('manage_options') ) {
            wp_die( esc_html__('You do not have sufficient permissions.', 'smart-seo-booster') );
        }
        check_admin_referer( self::NONCE, 'smart_seo_import_nonce' );

        $source = isset($_POST['source']) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : '';
        $offset = isset($_POST['offset']) ? absint( wp_unslash( $_POST['offset'] ) ) : 0;
        $map    = self::map( $source );

        if ( empty( $map ) ) {
            wp_safe_redirect( admin_url('admin.php?page=smart-seo-import') );
            exit;
        }

        $query = new WP_Query([
            'post_type'      => 'any',
            'post_status'    => 'any',
            'posts_per_page' => self::BATCH_SIZE,
            'offset'         => $offset,
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ]);

        $imported = 0;
        foreach ( $query->posts as $post_id ) {
            foreach ( $map as $from => $to ) {
                $value = get_post_meta( $post_id, $from, true );
                if ( $value === '' || $value === false ) {
                    continue;
                }
                // Never overwrite an existing Smart SEO value.
                if ( get_post_meta( $post_id, $to, true ) !== '' ) {
                    continue;
                }
                update_post_meta( $post_id, $to, sanitize_text_field( is_array( $value ) ? implode( ',', $value ) : $value ) );
                $imported++;
            }
            self::import_robots( $post_id, $source );
        }

        $count     = count( $query->posts );
        $newOffset = $offset + self::BATCH_SIZE;

        $args = [ 'page' => 'smart-seo-import', 'imported' => $imported ];
        if ( $count < self::BATCH_SIZE ) {
            $args['complete'] = 1;
            wp_safe_redirect( add_query_arg( $args, admin_url('admin.php') ) );
            exit;
        }

        // More to process — auto-continue via a self-submitting redirect page.
        $continue = add_query_arg(
            [ 'page' => 'smart-seo-import', 'auto_source' => $source, 'auto_offset' => $newOffset ],
            admin_url('admin.php')
        );
        wp_safe_redirect( $continue );
        exit;
    }

    /**
     * Translate source robots meta into our combined robots string.
     */
    private static function import_robots( $post_id, $source ) {
        if ( get_post_meta( $post_id, '_smart_seo_robots', true ) !== '' ) {
            return;
        }

        $directives = [];
        if ( $source === 'yoast' ) {
            if ( get_post_meta( $post_id, '_yoast_wpseo_meta-robots-noindex', true ) === '1' ) {
                $directives[] = 'noindex';
            }
            if ( get_post_meta( $post_id, '_yoast_wpseo_meta-robots-nofollow', true ) === '1' ) {
                $directives[] = 'nofollow';
            }
        } elseif ( $source === 'rankmath' ) {
            $robots = get_post_meta( $post_id, 'rank_math_robots', true );
            if ( is_array( $robots ) ) {
                foreach ( [ 'noindex', 'nofollow', 'noarchive', 'nosnippet' ] as $d ) {
                    if ( in_array( $d, $robots, true ) ) {
                        $directives[] = $d;
                    }
                }
            }
        }

        if ( ! empty( $directives ) ) {
            update_post_meta( $post_id, '_smart_seo_robots', implode( ',', $directives ) );
        }
    }
}

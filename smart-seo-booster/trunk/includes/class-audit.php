<?php
defined('ABSPATH') || exit;

/**
 * Site-wide SEO audit data.
 *
 * Aggregates per-post scores and issues into a dashboard-ready dataset,
 * cached briefly to keep repeat views fast.
 */
class Smart_SEO_Audit {

    const CACHE_KEY = 'smart_seo_audit_data';
    const MAX_SCAN  = 300;

    /**
     * Gather aggregate audit data across published content.
     *
     * @param bool $fresh Bypass the cache.
     * @return array
     */
    public static function gather( $fresh = false ) {
        if ( ! $fresh ) {
            $cached = get_transient( self::CACHE_KEY );
            if ( is_array( $cached ) ) {
                return $cached;
            }
        }

        $query = new WP_Query([
            'post_type'      => [ 'post', 'page' ],
            'post_status'    => 'publish',
            'posts_per_page' => self::MAX_SCAN,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ]);

        $data = [
            'total'         => 0,
            'score_sum'     => 0,
            'buckets'       => [ 'excellent' => 0, 'good' => 0, 'needs' => 0, 'poor' => 0 ],
            'missing_desc'  => 0,
            'missing_title' => 0,
            'no_schema'     => 0,
            'images_total'  => 0,
            'images_no_alt' => 0,
            'thin_content'  => 0,
            'noindex'       => 0,
            'lowest'        => [],
            'opportunities' => [],
        ];

        $options       = get_option( 'smart_seo_options', [] );
        $schema_global = ! empty( $options['enable_schema'] );
        $min_words     = isset( $options['min_word_count'] ) ? (int) $options['min_word_count'] : 300;

        foreach ( $query->posts as $post ) {
            $data['total']++;

            $score = Smart_SEO_Score_Display::calculate_seo_score( $post->ID );
            $data['score_sum'] += $score;

            if ( $score >= 80 ) {
                $data['buckets']['excellent']++;
            } elseif ( $score >= 60 ) {
                $data['buckets']['good']++;
            } elseif ( $score >= 40 ) {
                $data['buckets']['needs']++;
            } else {
                $data['buckets']['poor']++;
            }

            // Meta description present? (custom field or excerpt)
            $desc = get_post_meta( $post->ID, '_smart_seo_description', true );
            if ( '' === $desc && '' === $post->post_excerpt ) {
                $data['missing_desc']++;
            }

            // SEO title present?
            if ( '' === get_post_meta( $post->ID, '_smart_seo_title', true ) ) {
                $data['missing_title']++;
            }

            // Schema type set for this post?
            if ( ! $schema_global && '' === get_post_meta( $post->ID, '_smart_seo_schema_type', true ) ) {
                $data['no_schema']++;
            }

            // Images + alt coverage.
            if ( preg_match_all( '/<img\b[^>]*>/i', $post->post_content, $imgs ) ) {
                foreach ( $imgs[0] as $img_tag ) {
                    $data['images_total']++;
                    if ( ! preg_match( '/\balt\s*=\s*("|\')(.*?)\1/i', $img_tag, $m ) || '' === trim( $m[2] ) ) {
                        $data['images_no_alt']++;
                    }
                }
            }

            // Thin content.
            $words = str_word_count( Smart_SEO_Meta_Templates::plain_text( $post->post_content ) );
            if ( $words < $min_words ) {
                $data['thin_content']++;
            }

            // Noindex.
            $robots = get_post_meta( $post->ID, '_smart_seo_robots', true );
            if ( $robots && false !== strpos( $robots, 'noindex' ) ) {
                $data['noindex']++;
            }

            $data['lowest'][] = [
                'id'    => $post->ID,
                'title' => get_the_title( $post ),
                'score' => $score,
            ];
        }

        // Lowest-scoring first, keep top 10.
        usort( $data['lowest'], static function ( $a, $b ) {
            return $a['score'] <=> $b['score'];
        } );
        $data['lowest'] = array_slice( $data['lowest'], 0, 10 );

        $data['avg']    = $data['total'] > 0 ? (int) round( $data['score_sum'] / $data['total'] ) : 0;
        $data['scanned_at'] = time();

        $data['opportunities'] = self::opportunities( $data );

        set_transient( self::CACHE_KEY, $data, 5 * MINUTE_IN_SECONDS );
        return $data;
    }

    /**
     * Turn raw counts into a prioritized, human list of opportunities.
     */
    private static function opportunities( $data ) {
        $out = [];
        if ( $data['images_no_alt'] > 0 ) {
            $out[] = [
                'icon'  => 'format-image',
                'level' => 'warning',
                /* translators: %d: number of images */
                'text'  => sprintf( _n( '%d image is missing alt text', '%d images are missing alt text', $data['images_no_alt'], 'smart-seo-booster' ), $data['images_no_alt'] ),
            ];
        }
        if ( $data['missing_desc'] > 0 ) {
            $out[] = [
                'icon'  => 'editor-alignleft',
                'level' => 'warning',
                /* translators: %d: number of posts */
                'text'  => sprintf( _n( '%d item has no meta description', '%d items have no meta description', $data['missing_desc'], 'smart-seo-booster' ), $data['missing_desc'] ),
            ];
        }
        if ( $data['thin_content'] > 0 ) {
            $out[] = [
                'icon'  => 'text-page',
                'level' => 'notice',
                /* translators: %d: number of posts */
                'text'  => sprintf( _n( '%d item has thin content', '%d items have thin content', $data['thin_content'], 'smart-seo-booster' ), $data['thin_content'] ),
            ];
        }
        if ( empty( $out ) ) {
            $out[] = [ 'icon' => 'yes-alt', 'level' => 'good', 'text' => __( 'No major issues found. Nice work!', 'smart-seo-booster' ) ];
        }
        return $out;
    }
}

<?php
defined('ABSPATH') || exit;

/**
 * Table of Contents block support: parses h2–h4 headings out of the post
 * content and renders an anchored jump-list, auto-assigning heading ids
 * (the same approach Yoast/RankMath use) so the links land somewhere even
 * when the theme/editor never gave those headings an id.
 */
class Smart_SEO_TOC {

    public static function init() {
        // Priority 20: after do_blocks() (9) and wpautop() (10), so this sees
        // the fully rendered heading markup for the page, not raw block
        // comments.
        add_filter( 'the_content', [ __CLASS__, 'inject_heading_ids' ], 20 );
    }

    /**
     * Add id="" to any h2–h4 in the rendered content that doesn't already
     * have one.
     */
    public static function inject_heading_ids( $content ) {
        if ( false === stripos( $content, '<h2' ) && false === stripos( $content, '<h3' ) && false === stripos( $content, '<h4' ) ) {
            return $content;
        }

        $used = [];
        return preg_replace_callback(
            '/<h([2-4])([^>]*)>(.*?)<\/h\1>/is',
            static function ( $m ) use ( &$used ) {
                if ( preg_match( '/\bid\s*=/i', $m[2] ) ) {
                    return $m[0];
                }
                $slug = self::unique_slug( wp_strip_all_tags( $m[3] ), $used );
                return '<h' . $m[1] . $m[2] . ' id="' . esc_attr( $slug ) . '">' . $m[3] . '</h' . $m[1] . '>';
            },
            $content
        );
    }

    /**
     * @param string $text Heading text (already stripped of tags).
     * @param array  $used Slugs already assigned this pass, by reference.
     */
    private static function unique_slug( $text, &$used ) {
        $base = sanitize_title( $text );
        if ( '' === $base ) {
            $base = 'section';
        }
        $slug = $base;
        $i    = 2;
        while ( isset( $used[ $slug ] ) ) {
            $slug = $base . '-' . $i;
            $i++;
        }
        $used[ $slug ] = true;
        return $slug;
    }

    /**
     * Pull headings out of raw post content, assigning the same ids
     * inject_heading_ids() will give the rendered page (same source content,
     * same slug algorithm, same left-to-right order).
     *
     * @return array<int, array{level:int,text:string,id:string}>
     */
    public static function extract_headings( $content, $min_level = 2, $max_level = 4 ) {
        if ( ! preg_match_all( '/<h([2-4])([^>]*)>(.*?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER ) ) {
            return [];
        }

        $used = [];
        $out  = [];
        foreach ( $matches as $m ) {
            $level = (int) $m[1];
            $text  = wp_strip_all_tags( $m[3] );

            if ( preg_match( '/\bid\s*=\s*("|\')(.*?)\1/i', $m[2], $id_m ) ) {
                $slug          = $id_m[2];
                $used[ $slug ] = true;
            } else {
                $slug = self::unique_slug( $text, $used );
            }

            if ( $level < $min_level || $level > $max_level || '' === $text ) {
                continue;
            }

            $out[] = [ 'level' => $level, 'text' => $text, 'id' => $slug ];
        }
        return $out;
    }

    public static function render( $attributes ) {
        $post = get_post();
        if ( ! $post ) {
            return '';
        }

        $min   = isset( $attributes['minLevel'] ) ? max( 2, (int) $attributes['minLevel'] ) : 2;
        $max   = isset( $attributes['maxLevel'] ) ? min( 4, (int) $attributes['maxLevel'] ) : 3;
        $title = isset( $attributes['title'] ) ? wp_strip_all_tags( $attributes['title'] ) : '';

        $headings = self::extract_headings( $post->post_content, $min, $max );
        if ( count( $headings ) < 2 ) {
            return '';
        }

        $base = min( array_column( $headings, 'level' ) );

        $out = '<nav class="ssb-toc">';
        if ( $title ) {
            $out .= '<p class="ssb-toc-title">' . esc_html( $title ) . '</p>';
        }
        $out .= '<ol>';
        foreach ( $headings as $h ) {
            $out .= '<li class="ssb-toc-lvl-' . (int) ( $h['level'] - $base ) . '"><a href="#' . esc_attr( $h['id'] ) . '">' . esc_html( $h['text'] ) . '</a></li>';
        }
        $out .= '</ol></nav>';
        return $out;
    }
}

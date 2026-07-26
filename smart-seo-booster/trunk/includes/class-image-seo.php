<?php
defined('ABSPATH') || exit;

/**
 * Image SEO — fill missing alt text on the front end so images aren't
 * invisible to search engines, AI crawlers, and screen readers.
 */
class Smart_SEO_Image_SEO {

    public static function init() {
        $options = get_option('smart_seo_options', []);
        if ( empty( $options['auto_image_alt'] ) ) {
            return;
        }
        add_filter('the_content', [__CLASS__, 'filter_content'], 20);
        add_filter('post_thumbnail_html', [__CLASS__, 'filter_thumbnail'], 20, 3);
    }

    /**
     * Add alt text to images in post content that are missing it.
     */
    public static function filter_content( $content ) {
        if ( is_admin() || false === strpos( $content, '<img' ) ) {
            return $content;
        }

        $fallback = self::context_title();

        return preg_replace_callback(
            '/<img\b[^>]*>/i',
            function ( $m ) use ( $fallback ) {
                $tag = $m[0];
                // Already has non-empty alt? Leave it.
                if ( preg_match( '/\balt\s*=\s*("|\')(.*?)\1/i', $tag, $a ) && '' !== trim( $a[2] ) ) {
                    return $tag;
                }

                $alt = '';
                // Prefer the attachment's own alt via its wp-image-ID class.
                if ( preg_match( '/wp-image-(\d+)/', $tag, $idm ) ) {
                    $attachment_id = (int) $idm[1];
                    $alt = (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
                    if ( '' === $alt ) {
                        $alt = get_the_title( $attachment_id );
                    }
                }
                if ( '' === $alt ) {
                    $alt = $fallback;
                }
                $alt = trim( wp_strip_all_tags( $alt ) );
                if ( '' === $alt ) {
                    return $tag;
                }

                // Replace an empty alt, or inject one if absent.
                if ( preg_match( '/\balt\s*=\s*("|\')\s*\1/i', $tag ) ) {
                    return preg_replace( '/\balt\s*=\s*("|\')\s*\1/i', 'alt="' . esc_attr( $alt ) . '"', $tag, 1 );
                }
                return preg_replace( '/<img\b/i', '<img alt="' . esc_attr( $alt ) . '"', $tag, 1 );
            },
            $content
        );
    }

    /**
     * Ensure the featured image has alt text.
     */
    public static function filter_thumbnail( $html, $post_id, $thumbnail_id ) {
        if ( '' === $html || ( preg_match( '/\balt\s*=\s*("|\')(.*?)\1/i', $html, $a ) && '' !== trim( $a[2] ) ) ) {
            return $html;
        }
        $alt = (string) get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
        if ( '' === $alt ) {
            $alt = get_the_title( $post_id );
        }
        $alt = trim( wp_strip_all_tags( $alt ) );
        if ( '' === $alt ) {
            return $html;
        }
        if ( preg_match( '/\balt\s*=\s*("|\')\s*\1/i', $html ) ) {
            return preg_replace( '/\balt\s*=\s*("|\')\s*\1/i', 'alt="' . esc_attr( $alt ) . '"', $html, 1 );
        }
        return preg_replace( '/<img\b/i', '<img alt="' . esc_attr( $alt ) . '"', $html, 1 );
    }

    private static function context_title() {
        if ( is_singular() ) {
            $obj = get_queried_object();
            if ( $obj instanceof WP_Post ) {
                return wp_strip_all_tags( get_the_title( $obj ) );
            }
        }
        return wp_strip_all_tags( get_bloginfo('name') );
    }
}

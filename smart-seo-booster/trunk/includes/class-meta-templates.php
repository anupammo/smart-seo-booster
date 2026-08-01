<?php
defined('ABSPATH') || exit;

/**
 * Meta template variable parser.
 *
 * Expands placeholders like %%title%%, %%sitename%%, %%sep%% in the global
 * title/description templates configured on the settings screen.
 */
class Smart_SEO_Meta_Templates {

    /**
     * Strip HTML from a content blob into plain text, without running
     * adjacent blocks together. wp_strip_all_tags() alone turns
     * "<h2>Title</h2><p>Body</p>" into "TitleBody" whenever the source HTML
     * has no whitespace between tags (common with imported/pasted content
     * and some page builders) — this inserts a space at block-level tag
     * boundaries first, then collapses whitespace runs to one space.
     *
     * @param string $html Raw content HTML (typically $post->post_content).
     * @return string Plain text, single-spaced, trimmed.
     */
    public static function plain_text( $html ) {
        $html = (string) $html;
        $html = preg_replace( '#</(p|div|h[1-6]|li|blockquote|section|article|header|footer|figcaption|td|th|tr)>#i', '$0 ', $html );
        $html = preg_replace( '#<br\s*/?>#i', ' ', $html );
        $text = wp_strip_all_tags( $html );
        $text = preg_replace( '/\s+/u', ' ', $text );
        return trim( $text );
    }

    /**
     * Available variables, for the settings-screen help text.
     *
     * @return array<string,string> token => description
     */
    public static function variables() {
        return [
            '%%title%%'     => __( 'Post/page title', 'smart-seo-booster' ),
            '%%sitename%%'  => __( 'Site name', 'smart-seo-booster' ),
            '%%tagline%%'   => __( 'Site tagline', 'smart-seo-booster' ),
            '%%sep%%'       => __( 'Separator', 'smart-seo-booster' ),
            '%%excerpt%%'   => __( 'Post excerpt', 'smart-seo-booster' ),
            '%%category%%'  => __( 'First category', 'smart-seo-booster' ),
            '%%tag%%'       => __( 'First tag', 'smart-seo-booster' ),
            '%%author%%'    => __( 'Author name', 'smart-seo-booster' ),
            '%%date%%'      => __( 'Published date', 'smart-seo-booster' ),
            '%%currentyear%%' => __( 'Current year', 'smart-seo-booster' ),
        ];
    }

    /**
     * Parse a template string for a given post (or the current context).
     *
     * @param string       $template The template containing %%tokens%%.
     * @param WP_Post|null $post     The post context, if any.
     * @return string Parsed, whitespace-collapsed string.
     */
    public static function parse( $template, $post = null ) {
        if ( ! is_string( $template ) || $template === '' ) {
            return '';
        }

        $options   = get_option( 'smart_seo_options', [] );
        $separator = isset( $options['separator'] ) && $options['separator'] !== ''
            ? $options['separator']
            : '-';

        $title    = '';
        $excerpt  = '';
        $category = '';
        $tag      = '';
        $author   = '';
        $date     = '';

        if ( $post instanceof WP_Post ) {
            $title   = get_the_title( $post );
            $excerpt = has_excerpt( $post ) ? get_the_excerpt( $post ) : '';
            $author  = get_the_author_meta( 'display_name', (int) $post->post_author );
            $date    = get_the_date( '', $post );

            $cats = get_the_category( $post->ID );
            if ( ! empty( $cats ) ) {
                $category = $cats[0]->name;
            }
            $tags = get_the_tags( $post->ID );
            if ( ! empty( $tags ) ) {
                $tag = $tags[0]->name;
            }
        }

        $replacements = [
            '%%title%%'       => $title,
            '%%sitename%%'    => get_bloginfo( 'name' ),
            '%%tagline%%'     => get_bloginfo( 'description' ),
            '%%sep%%'         => $separator,
            '%%excerpt%%'     => wp_strip_all_tags( $excerpt ),
            '%%category%%'    => $category,
            '%%tag%%'         => $tag,
            '%%author%%'      => $author,
            '%%date%%'        => $date,
            '%%currentyear%%' => gmdate( 'Y' ),
        ];

        $output = strtr( $template, $replacements );

        // Collapse whitespace and trim dangling separators left by empty tokens.
        $output = preg_replace( '/\s+/', ' ', $output );
        $sep_q  = preg_quote( $separator, '/' );
        $output = preg_replace( '/(\s*' . $sep_q . '\s*)+/', ' ' . $separator . ' ', $output );
        $output = trim( $output, " \t\n\r\0\x0B" . $separator );

        return trim( $output );
    }
}

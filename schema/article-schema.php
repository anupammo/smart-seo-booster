<?php
defined('ABSPATH') || exit;

// Derive data from the current post — never hardcode identity.
$smart_seo_post = get_queried_object();
if ( ! $smart_seo_post instanceof WP_Post ) {
    return null;
}

$smart_seo_author_id   = (int) $smart_seo_post->post_author;
$smart_seo_author_name = $smart_seo_author_id
    ? get_the_author_meta( 'display_name', $smart_seo_author_id )
    : get_bloginfo( 'name' );

$smart_seo_logo = get_site_icon_url();

$smart_seo_article = [
    "@context"      => "https://schema.org",
    "@type"         => "Article",
    "headline"      => wp_strip_all_tags( get_the_title( $smart_seo_post ) ),
    "author"        => [
        "@type" => "Person",
        "name"  => wp_strip_all_tags( $smart_seo_author_name ),
    ],
    "datePublished" => get_the_date( 'c', $smart_seo_post ),
    "dateModified"  => get_the_modified_date( 'c', $smart_seo_post ),
    "mainEntityOfPage" => [
        "@type" => "WebPage",
        "@id"   => esc_url_raw( get_permalink( $smart_seo_post ) ),
    ],
    "publisher"     => [
        "@type" => "Organization",
        "name"  => wp_strip_all_tags( get_bloginfo( 'name' ) ),
    ],
];

// Only add a logo when the site actually has one (schema.org requires a valid URL).
if ( $smart_seo_logo ) {
    $smart_seo_article['publisher']['logo'] = [
        "@type" => "ImageObject",
        "url"   => esc_url_raw( $smart_seo_logo ),
    ];
}

/**
 * Filter the Article JSON-LD schema before output.
 *
 * @param array   $smart_seo_article The schema array.
 * @param WP_Post $smart_seo_post    The current post.
 */
return apply_filters( 'smart_seo_article_schema', $smart_seo_article, $smart_seo_post );

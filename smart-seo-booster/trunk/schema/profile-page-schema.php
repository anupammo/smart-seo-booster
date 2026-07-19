<?php
defined('ABSPATH') || exit;

// Build the Person from the page author (or fall back to the site's first admin).
$smart_seo_post   = get_queried_object();
$smart_seo_author = 0;

if ( $smart_seo_post instanceof WP_Post ) {
    $smart_seo_author = (int) $smart_seo_post->post_author;
}

if ( ! $smart_seo_author ) {
    return null;
}

$smart_seo_person = [
    "@context" => "https://schema.org",
    "@type"    => "ProfilePage",
    "mainEntity" => [
        "@type" => "Person",
        "name"  => wp_strip_all_tags( get_the_author_meta( 'display_name', $smart_seo_author ) ),
        "url"   => esc_url_raw( get_author_posts_url( $smart_seo_author ) ),
        "image" => esc_url_raw( get_avatar_url( $smart_seo_author ) ),
    ],
];

// Add the author's bio as description when present.
$smart_seo_bio = get_the_author_meta( 'description', $smart_seo_author );
if ( $smart_seo_bio ) {
    $smart_seo_person['mainEntity']['description'] = wp_strip_all_tags( $smart_seo_bio );
}

/**
 * Filter the ProfilePage JSON-LD schema before output.
 *
 * @param array $smart_seo_person The schema array.
 * @param int   $smart_seo_author The author user ID.
 */
return apply_filters( 'smart_seo_profile_page_schema', $smart_seo_person, $smart_seo_author );

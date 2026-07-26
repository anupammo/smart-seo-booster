<?php
defined('ABSPATH') || exit;

$smart_seo_logo = get_site_icon_url();

$smart_seo_org = [
    "@context" => "https://schema.org",
    "@type"    => "Organization",
    "name"     => wp_strip_all_tags( get_bloginfo( 'name' ) ),
    "url"      => esc_url_raw( home_url() ),
];

if ( $smart_seo_logo ) {
    $smart_seo_org['logo'] = esc_url_raw( $smart_seo_logo );
}

/**
 * Social/profile URLs for the site's Organization schema (sameAs).
 * Pulled from the plugin settings (one URL per line); also filterable.
 *
 * @param string[] $urls List of absolute profile URLs.
 */
$smart_seo_org_options = get_option( 'smart_seo_options', [] );
$smart_seo_same_as     = [];
foreach ( [ 'social_facebook', 'social_twitter', 'social_linkedin', 'social_instagram', 'social_youtube', 'social_pinterest' ] as $smart_seo_social_key ) {
    if ( ! empty( $smart_seo_org_options[ $smart_seo_social_key ] ) ) {
        $smart_seo_same_as[] = $smart_seo_org_options[ $smart_seo_social_key ];
    }
}
if ( ! empty( $smart_seo_org_options['organization_same_as'] ) ) {
    $smart_seo_same_as = array_merge( $smart_seo_same_as, preg_split( '/\r\n|\r|\n/', $smart_seo_org_options['organization_same_as'] ) );
}
$smart_seo_same_as = array_unique( $smart_seo_same_as );
$smart_seo_same_as = apply_filters( 'smart_seo_organization_same_as', $smart_seo_same_as );
$smart_seo_same_as = array_values( array_filter( array_map( 'esc_url_raw', (array) $smart_seo_same_as ) ) );
if ( ! empty( $smart_seo_same_as ) ) {
    $smart_seo_org['sameAs'] = $smart_seo_same_as;
}

/**
 * Filter the Organization JSON-LD schema before output.
 *
 * @param array $smart_seo_org The schema array.
 */
return apply_filters( 'smart_seo_organization_schema', $smart_seo_org );

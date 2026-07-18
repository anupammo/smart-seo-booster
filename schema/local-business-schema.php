<?php
defined('ABSPATH') || exit;

$smart_seo_options = get_option( 'smart_seo_options', [] );
$smart_seo_logo    = get_site_icon_url();

// Generic, site-derived defaults — no hardcoded business identity.
$smart_seo_business = [
    "@context" => "https://schema.org",
    "@type"    => "LocalBusiness",
    "name"     => wp_strip_all_tags( get_bloginfo( 'name' ) ),
    "url"      => esc_url_raw( home_url() ),
];

if ( $smart_seo_logo ) {
    $smart_seo_business['image'] = esc_url_raw( $smart_seo_logo );
}

// Optional address from settings (only added when fully configured).
$smart_seo_address = isset( $smart_seo_options['business_address'] ) && is_array( $smart_seo_options['business_address'] )
    ? $smart_seo_options['business_address']
    : [];

if ( ! empty( $smart_seo_address['streetAddress'] ) ) {
    $smart_seo_business['address'] = [
        "@type"           => "PostalAddress",
        "streetAddress"   => sanitize_text_field( $smart_seo_address['streetAddress'] ?? '' ),
        "addressLocality" => sanitize_text_field( $smart_seo_address['addressLocality'] ?? '' ),
        "addressRegion"   => sanitize_text_field( $smart_seo_address['addressRegion'] ?? '' ),
        "postalCode"      => sanitize_text_field( $smart_seo_address['postalCode'] ?? '' ),
        "addressCountry"  => sanitize_text_field( $smart_seo_address['addressCountry'] ?? '' ),
    ];
}

if ( ! empty( $smart_seo_options['business_phone'] ) ) {
    $smart_seo_business['telephone'] = sanitize_text_field( $smart_seo_options['business_phone'] );
}

/**
 * Filter the LocalBusiness JSON-LD schema before output.
 *
 * @param array $smart_seo_business The schema array.
 */
return apply_filters( 'smart_seo_local_business_schema', $smart_seo_business );

<?php
defined('ABSPATH') || exit;

return [
    "@context" => "https://schema.org",
    "@type" => "LocalBusiness",
    "name" => "Anupam Mondal Web Solutions",
    "image" => esc_url_raw( get_site_icon_url() ),
    "address" => [
        "@type" => "PostalAddress",
        "streetAddress" => "Metiari, Protapnagar, Sonarpur",
        "addressLocality" => "South 24 Parganas",
        "addressRegion" => "West Bengal",
        "postalCode" => "700150",
        "addressCountry" => "IN"
    ],
    "url" => esc_url_raw( home_url() ),
    "telephone" => "+91-XXXXXXXXXX"
];


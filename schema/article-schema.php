<?php
/**
 * Article Schema Template
 * 
 * @package SmartSEOBooster
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

return [
    "@context" => "https://schema.org",
    "@type" => "Article",
    "headline" => get_the_title(),
    "author" => [
        "@type" => "Person",
        "name" => "Anupam Mondal"
    ],
    "datePublished" => get_the_date('c'),
    "dateModified" => get_the_modified_date('c'),
    "mainEntityOfPage" => [
        "@type" => "WebPage",
        "@id" => get_permalink()
    ],
    "publisher" => [
        "@type" => "Organization",
        "name" => get_bloginfo('name'),
        "logo" => [
            "@type" => "ImageObject",
            "url" => get_site_icon_url()
        ]
    ]
];

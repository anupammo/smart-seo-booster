<?php
defined('ABSPATH') || exit;

return [
    "@context" => "https://schema.org",
    "@type" => "Article",
    "headline" => wp_strip_all_tags( get_the_title() ),
    "author" => [
        "@type" => "Person",
        "name" => "Anupam Mondal"
    ],
    "datePublished" => get_the_date('c'),
    "dateModified" => get_the_modified_date('c'),
    "mainEntityOfPage" => [
        "@type" => "WebPage",
        "@id" => esc_url_raw( get_permalink() )
    ],
    "publisher" => [
        "@type" => "Organization",
        "name" => wp_strip_all_tags( get_bloginfo('name') ),
        "logo" => [
            "@type" => "ImageObject",
            "url" => esc_url_raw( get_site_icon_url() )
        ]
    ]
];

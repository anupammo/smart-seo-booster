<?php
defined('ABSPATH') || exit;

return [
    "@context" => "https://schema.org",
    "@type" => "Person",
    "name" => "Anupam Mondal",
    "url" => esc_url_raw( home_url('/about') ),
    "image" => esc_url_raw( get_site_icon_url() ),
    "jobTitle" => "Full Stack Developer & SEO Consultant",
    "worksFor" => [
        "@type" => "Organization",
        "name" => wp_strip_all_tags( get_bloginfo('name') )
    ],
    "sameAs" => [
        "https://github.com/anupam-mondal",
        "https://linkedin.com/in/anupam-mondal"
    ]
];


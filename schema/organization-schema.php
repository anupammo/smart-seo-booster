<?php
defined('ABSPATH') || exit;

return [
    "@context" => "https://schema.org",
    "@type" => "Organization",
    "name" => wp_strip_all_tags( get_bloginfo('name') ),
    "url" => esc_url_raw( home_url() ),
    "logo" => esc_url_raw( get_site_icon_url() ),
    "sameAs" => [
        "https://github.com/anupam-mondal",
        "https://linkedin.com/in/anupam-mondal"
    ]
];


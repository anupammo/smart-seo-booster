<?php
return [
    "@context" => "https://schema.org",
    "@type" => "Organization",
    "name" => get_bloginfo('name'),
    "url" => home_url(),
    "logo" => get_site_icon_url(),
    "sameAs" => [
        "https://github.com/anupam-mondal",
        "https://linkedin.com/in/anupam-mondal"
    ]
];

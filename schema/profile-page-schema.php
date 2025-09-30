<?php
return [
    "@context" => "https://schema.org",
    "@type" => "Person",
    "name" => "Anupam Mondal",
    "url" => home_url('/about'),
    "image" => get_site_icon_url(),
    "jobTitle" => "Full Stack Developer & SEO Consultant",
    "worksFor" => [
        "@type" => "Organization",
        "name" => get_bloginfo('name')
    ],
    "sameAs" => [
        "https://github.com/anupam-mondal",
        "https://linkedin.com/in/anupam-mondal"
    ]
];

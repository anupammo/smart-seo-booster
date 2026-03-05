<?php
/**
 * FAQ Schema Template
 * 
 * @package SmartSEOBooster
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

return [
    "@context" => "https://schema.org",
    "@type" => "FAQPage",
    "mainEntity" => [
        [
            "@type" => "Question",
            "name" => "What services does Anupam Mondal offer?",
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => "Web design, development, and SEO automation."
            ]
        ],
        [
            "@type" => "Question",
            "name" => "Does Anupam provide schema automation?",
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => "Yes, including Article, FAQ, LocalBusiness, and more."
            ]
        ]
    ]
];

<?php
defined('ABSPATH') || exit;

/**
 * FAQ items for the current page.
 *
 * There is no hardcoded content here — a valid FAQPage must reflect real
 * questions on the page. Supply items via this filter (or a future settings
 * screen / block parser). Each item: [ 'question' => '', 'answer' => '' ].
 *
 * @param array $items FAQ items for the queried object.
 */
$smart_seo_faq_items = apply_filters( 'smart_seo_faq_items', [], get_queried_object() );

if ( empty( $smart_seo_faq_items ) || ! is_array( $smart_seo_faq_items ) ) {
    // Nothing to describe — skip output rather than emit an invalid empty FAQPage.
    return null;
}

$smart_seo_main_entity = [];
foreach ( $smart_seo_faq_items as $smart_seo_item ) {
    if ( empty( $smart_seo_item['question'] ) || empty( $smart_seo_item['answer'] ) ) {
        continue;
    }
    $smart_seo_main_entity[] = [
        "@type"          => "Question",
        "name"           => wp_strip_all_tags( $smart_seo_item['question'] ),
        "acceptedAnswer" => [
            "@type" => "Answer",
            "text"  => wp_strip_all_tags( $smart_seo_item['answer'] ),
        ],
    ];
}

if ( empty( $smart_seo_main_entity ) ) {
    return null;
}

return [
    "@context"   => "https://schema.org",
    "@type"      => "FAQPage",
    "mainEntity" => $smart_seo_main_entity,
];

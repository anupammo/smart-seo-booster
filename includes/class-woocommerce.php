<?php
defined('ABSPATH') || exit;

/**
 * WooCommerce basics — Product JSON-LD and Open Graph product tags.
 * Only active when WooCommerce is installed.
 */
class Smart_SEO_WooCommerce {

    public static function init() {
        if ( ! class_exists('WooCommerce') ) {
            return;
        }
        add_filter('smart_seo_og_type', [__CLASS__, 'og_type']);
        add_action('wp_head', [__CLASS__, 'product_og'], 6);
        add_action('wp_footer', [__CLASS__, 'product_schema']);
    }

    public static function og_type( $type ) {
        return ( function_exists('is_product') && is_product() ) ? 'product' : $type;
    }

    private static function get_product() {
        if ( ! function_exists('is_product') || ! is_product() ) {
            return null;
        }
        $product = wc_get_product( get_queried_object_id() );
        return $product ?: null;
    }

    public static function product_og() {
        $product = self::get_product();
        if ( ! $product ) {
            return;
        }
        printf('<meta property="product:price:amount" content="%s" />' . "\n", esc_attr( $product->get_price() ));
        printf('<meta property="product:price:currency" content="%s" />' . "\n", esc_attr( get_woocommerce_currency() ));
        printf('<meta property="product:availability" content="%s" />' . "\n", esc_attr( $product->is_in_stock() ? 'in stock' : 'out of stock' ));
    }

    public static function product_schema() {
        $options = get_option('smart_seo_options');
        if ( empty( $options['enable_schema'] ) ) {
            return;
        }
        $product = self::get_product();
        if ( ! $product ) {
            return;
        }

        $schema = [
            "@context" => "https://schema.org",
            "@type"    => "Product",
            "name"     => wp_strip_all_tags( $product->get_name() ),
            "url"      => esc_url_raw( get_permalink( $product->get_id() ) ),
        ];

        $desc = $product->get_short_description() ?: $product->get_description();
        if ( $desc ) {
            $schema['description'] = wp_strip_all_tags( $desc );
        }

        if ( $product->get_sku() ) {
            $schema['sku'] = $product->get_sku();
        }

        $image = wp_get_attachment_image_url( $product->get_image_id(), 'full' );
        if ( $image ) {
            $schema['image'] = esc_url_raw( $image );
        }

        $price = $product->get_price();
        if ( $price !== '' ) {
            $schema['offers'] = [
                "@type"         => "Offer",
                "price"         => (string) wc_get_price_to_display( $product ),
                "priceCurrency" => get_woocommerce_currency(),
                "availability"  => $product->is_in_stock() ? "https://schema.org/InStock" : "https://schema.org/OutOfStock",
                "url"           => esc_url_raw( get_permalink( $product->get_id() ) ),
            ];
        }

        // Aggregate rating when reviews exist.
        if ( $product->get_rating_count() > 0 ) {
            $schema['aggregateRating'] = [
                "@type"       => "AggregateRating",
                "ratingValue" => (string) $product->get_average_rating(),
                "reviewCount" => (int) $product->get_review_count(),
            ];
        }

        $schema = apply_filters( 'smart_seo_product_schema', $schema, $product );

        echo "<script type='application/ld+json'>" . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
    }
}

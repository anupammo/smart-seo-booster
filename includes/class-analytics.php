<?php
defined('ABSPATH') || exit;

/**
 * Front-end analytics: Google Analytics 4 (gtag) and Microsoft Clarity.
 * Scripts are enqueued only when a valid ID is configured.
 */
class Smart_SEO_Analytics {

    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue']);
        add_filter('script_loader_tag', [__CLASS__, 'async_tag'], 10, 2);
    }

    private static function excluded() {
        $options = get_option('smart_seo_options', []);
        return ! empty( $options['analytics_exclude_admins'] ) && current_user_can('manage_options');
    }

    public static function enqueue() {
        if ( is_admin() || self::excluded() ) {
            return;
        }

        $options = get_option('smart_seo_options', []);

        // --- Google Analytics 4 ---
        $ga = isset( $options['ga4_id'] ) ? trim( $options['ga4_id'] ) : '';
        if ( $ga && preg_match( '/^G-[A-Z0-9]+$/i', $ga ) ) {
            wp_enqueue_script(
                'smart-seo-ga4',
                'https://www.googletagmanager.com/gtag/js?id=' . rawurlencode( $ga ),
                [],
                null,
                false
            );
            wp_add_inline_script(
                'smart-seo-ga4',
                "window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','" . esc_js( $ga ) . "');",
                'after'
            );
        }

        // --- Microsoft Clarity ---
        $clarity = isset( $options['clarity_id'] ) ? trim( $options['clarity_id'] ) : '';
        if ( $clarity && preg_match( '/^[a-z0-9]+$/i', $clarity ) ) {
            wp_register_script( 'smart-seo-clarity', '', [], SMART_SEO_BOOSTER_VERSION, false );
            wp_enqueue_script( 'smart-seo-clarity' );
            wp_add_inline_script(
                'smart-seo-clarity',
                '(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script","' . esc_js( $clarity ) . '");'
            );
        }
    }

    /**
     * Add async to the gtag loader tag.
     */
    public static function async_tag( $tag, $handle ) {
        if ( 'smart-seo-ga4' === $handle && false === strpos( $tag, ' async' ) ) {
            $tag = str_replace( ' src=', ' async src=', $tag );
        }
        return $tag;
    }
}

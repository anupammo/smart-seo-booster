<?php
defined('ABSPATH') || exit;

/**
 * Page Speed report — wraps Google's public PageSpeed Insights v5 API
 * (Lighthouse) so users can see Core Web Vitals and category scores without
 * leaving wp-admin, the way Yoast/RankMath's dashboards surface CWV data.
 *
 * The keyless endpoint has a modest shared quota; results are cached per
 * URL+strategy for a few hours, and an optional API key (Settings →
 * Analytics) raises that quota for busier sites.
 */
class Smart_SEO_PageSpeed {

    const TRANSIENT_PREFIX = 'smart_seo_psi_';
    const CACHE_TTL         = 6 * HOUR_IN_SECONDS;

    public static function init() {
        add_action('wp_ajax_smart_seo_pagespeed_check', [__CLASS__, 'ajax_check']);
    }

    /**
     * AJAX: run (or return cached) PageSpeed Insights data for a URL.
     */
    public static function ajax_check() {
        check_ajax_referer('smart_seo_nonce', 'nonce');

        if ( ! current_user_can('manage_options') ) {
            wp_send_json_error( [ 'message' => __( 'Access denied.', 'smart-seo-booster' ) ] );
        }

        $url      = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : home_url( '/' );
        $strategy = isset( $_POST['strategy'] ) && 'desktop' === $_POST['strategy'] ? 'desktop' : 'mobile';
        $fresh    = ! empty( $_POST['fresh'] );

        // Restrict to this site's own host — this box proxies the request,
        // so an arbitrary attacker-supplied URL would make it an open relay.
        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
        $req_host  = wp_parse_url( $url, PHP_URL_HOST );
        if ( ! $req_host || ! $site_host || strtolower( $req_host ) !== strtolower( $site_host ) ) {
            wp_send_json_error( [ 'message' => __( 'Only URLs on this site can be checked.', 'smart-seo-booster' ) ] );
        }

        $result = self::fetch( $url, $strategy, $fresh );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( [ 'message' => $result->get_error_message() ] );
        }

        wp_send_json_success( $result );
    }

    /**
     * Fetch (with cache) and reduce a PSI response to what the report needs.
     *
     * @return array|WP_Error
     */
    public static function fetch( $url, $strategy = 'mobile', $fresh = false ) {
        $cache_key = self::TRANSIENT_PREFIX . md5( $url . '|' . $strategy );

        if ( ! $fresh ) {
            $cached = get_transient( $cache_key );
            if ( is_array( $cached ) ) {
                $cached['cached'] = true;
                return $cached;
            }
        }

        $options = get_option( 'smart_seo_options', [] );
        $api_key = isset( $options['pagespeed_api_key'] ) ? trim( $options['pagespeed_api_key'] ) : '';

        $endpoint = add_query_arg(
            array_filter([
                'url'      => rawurlencode( $url ),
                'strategy' => $strategy,
                'category' => 'performance', // repeated below for the others
                'key'      => $api_key ?: null,
            ]),
            'https://www.googleapis.com/pagespeedonline/v5/runPagespeed'
        );
        // add_query_arg() only keeps one value per key, so append the extra
        // repeated `category` params by hand.
        $endpoint .= '&category=seo&category=accessibility&category=best-practices';

        $response = wp_remote_get( $endpoint, [
            'timeout' => 45,
        ] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error(
                'smart_seo_psi_unreachable',
                __( 'Could not reach the PageSpeed Insights API. Note: this check requires your site to be publicly reachable on the internet — local or staging installs (e.g. localhost/XAMPP) cannot be tested this way.', 'smart-seo-booster' )
            );
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( 200 !== $code || ! is_array( $body ) || empty( $body['lighthouseResult'] ) ) {
            $message = $body['error']['message'] ?? sprintf(
                /* translators: %d: HTTP status code */
                __( 'PageSpeed Insights returned an unexpected response (HTTP %d).', 'smart-seo-booster' ),
                (int) $code
            );
            return new WP_Error( 'smart_seo_psi_error', $message );
        }

        $data = self::reduce( $body );
        set_transient( $cache_key, $data, self::CACHE_TTL );
        $data['cached'] = false;
        return $data;
    }

    /**
     * Pull just the scores/metrics the report displays out of the (large)
     * raw Lighthouse payload.
     */
    private static function reduce( $body ) {
        $lh         = $body['lighthouseResult'];
        $categories = $lh['categories'] ?? [];
        $audits     = $lh['audits'] ?? [];

        $score_of = static function ( $cat ) use ( $categories ) {
            return isset( $categories[ $cat ]['score'] ) ? (int) round( $categories[ $cat ]['score'] * 100 ) : null;
        };

        // Prefer real-user field data (CrUX) for Core Web Vitals when
        // available; fall back to this run's lab data for low-traffic sites
        // Google has no field data for yet.
        $field = $body['loadingExperience']['metrics'] ?? [];

        $lcp = isset( $field['LARGEST_CONTENTFUL_PAINT_MS']['percentile'] )
            ? $field['LARGEST_CONTENTFUL_PAINT_MS']['percentile'] / 1000
            : ( isset( $audits['largest-contentful-paint']['numericValue'] ) ? $audits['largest-contentful-paint']['numericValue'] / 1000 : null );

        $cls = isset( $field['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'] )
            ? $field['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'] / 100
            : ( isset( $audits['cumulative-layout-shift']['numericValue'] ) ? (float) $audits['cumulative-layout-shift']['numericValue'] : null );

        $inp_field = isset( $field['INTERACTION_TO_NEXT_PAINT']['percentile'] ) ? $field['INTERACTION_TO_NEXT_PAINT']['percentile'] : null;
        $tbt_lab   = isset( $audits['total-blocking-time']['numericValue'] ) ? $audits['total-blocking-time']['numericValue'] : null;

        return [
            'scores' => [
                'performance'    => $score_of( 'performance' ),
                'seo'            => $score_of( 'seo' ),
                'accessibility'  => $score_of( 'accessibility' ),
                'best_practices' => $score_of( 'best-practices' ),
            ],
            'vitals' => [
                'lcp' => $lcp,
                'cls' => $cls,
                'inp' => $inp_field,
                'tbt' => $tbt_lab,
            ],
            'has_field_data'  => ! empty( $field ),
            'final_url'       => $lh['finalUrl'] ?? '',
            'fetch_time'      => $lh['fetchTime'] ?? '',
        ];
    }

    /**
     * Bucket a Core Web Vital reading against Google's published thresholds.
     *
     * @return string 'good'|'needs'|'poor'
     */
    public static function bucket( $metric, $value ) {
        if ( null === $value ) {
            return 'unknown';
        }
        switch ( $metric ) {
            case 'lcp': // seconds
                return $value <= 2.5 ? 'good' : ( $value <= 4.0 ? 'needs' : 'poor' );
            case 'cls': // unitless
                return $value <= 0.1 ? 'good' : ( $value <= 0.25 ? 'needs' : 'poor' );
            case 'inp': // ms (field) — same thresholds apply to lab TBT as a rough proxy
            case 'tbt':
                return $value <= 200 ? 'good' : ( $value <= 500 ? 'needs' : 'poor' );
            default:
                return 'unknown';
        }
    }
}

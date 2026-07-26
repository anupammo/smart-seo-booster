<?php
defined('ABSPATH') || exit;

/**
 * Developer credit / attribution.
 *
 * - Admin footer credit on the plugin's own screens.
 * - A "Portfolio" link on the Plugins list row.
 * - An OPTIONAL front-end credit link (off by default) so a site owner can
 *   choose to display attribution. Per WordPress.org guidelines, public links
 *   are never output without the user opting in.
 */
class Smart_SEO_Credits {

    public static function init() {
        add_filter('admin_footer_text', [__CLASS__, 'admin_footer'], 20);
        add_filter('plugin_row_meta', [__CLASS__, 'row_meta'], 10, 2);
        add_action('wp_footer', [__CLASS__, 'frontend_credit'], 99);
    }

    private static function url() {
        return defined('SMART_SEO_BOOSTER_URL') ? SMART_SEO_BOOSTER_URL : 'https://anupammondal.in';
    }

    /**
     * Replace the admin footer text on Smart SEO screens only.
     */
    public static function admin_footer( $text ) {
        if ( ! function_exists('get_current_screen') ) {
            return $text;
        }
        $screen = get_current_screen();
        if ( ! $screen || false === strpos( (string) $screen->id, 'smart-seo' ) ) {
            return $text;
        }

        return sprintf(
            /* translators: %s: developer link */
            esc_html__( 'Smart SEO Booster — crafted by %s', 'smart-seo-booster' ),
            '<a href="' . esc_url( self::url() ) . '" target="_blank" rel="noopener">Anupam Mondal</a>'
        );
    }

    /**
     * Add a Portfolio link to the plugin's row on the Plugins screen.
     */
    public static function row_meta( $links, $file ) {
        if ( plugin_basename( SMART_SEO_BOOSTER_FILE ) === $file ) {
            $links[] = '<a href="' . esc_url( self::url() ) . '" target="_blank" rel="noopener">' . esc_html__( 'Developer Portfolio', 'smart-seo-booster' ) . '</a>';
        }
        return $links;
    }

    /**
     * Optional, opt-in front-end attribution link.
     */
    public static function frontend_credit() {
        $options = get_option('smart_seo_options', []);
        if ( empty( $options['frontend_credit'] ) ) {
            return;
        }

        $link = '<a href="' . esc_url( self::url() ) . '" target="_blank" rel="noopener">Smart SEO Booster</a>';
        $html = sprintf(
            /* translators: %s: plugin/developer link */
            esc_html__( 'SEO by %s', 'smart-seo-booster' ),
            $link
        );

        echo '<div class="smart-seo-credit" style="text-align:center;font-size:12px;opacity:.7;padding:10px 0;">'
            . wp_kses_post( $html )
            . '</div>' . "\n";
    }
}

<?php
defined('ABSPATH') || exit;

/**
 * Developer credit / attribution.
 *
 * - Admin footer credit on the plugin's own screens.
 * - A front-end credit link ("SEO by Smart SEO Booster"). Included with the
 *   free plugin; a Pro license is required to remove it — the equivalent
 *   setting is shown as always-on/locked in Settings, never hidden.
 */
class Smart_SEO_Credits {

    public static function init() {
        add_filter('admin_footer_text', [__CLASS__, 'admin_footer'], 20);
        add_action('wp_footer', [__CLASS__, 'frontend_credit'], 99);
    }

    private static function url() {
        return defined('SMART_SEO_BOOSTER_AUTHOR_URL') ? SMART_SEO_BOOSTER_AUTHOR_URL : 'https://anupammondal.in';
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
     * Front-end attribution link. Included with the free plugin; removing it
     * requires Pro (see the locked toggle in Settings → General).
     */
    public static function frontend_credit() {
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

<?php
defined('ABSPATH') || exit;

/**
 * "What's new" notice after the plugin is updated.
 *
 * Points users at the plugin's home page — where the changelog and feature
 * write-ups live — the first time they load wp-admin on a new version.
 *
 * Note on why this is a notice rather than a redirect: WordPress.org's
 * guidelines are explicit that plugins must not hijack the admin, and
 * bouncing someone to an *external* site on activation or on every update is
 * the textbook example. It also breaks bulk updates — updating ten plugins
 * would fire ten redirects — and any popup-based version is silently killed
 * by browser popup blockers, since the click that opened it was not the
 * user's. A dismissible notice with a real link reaches the same page,
 * survives bulk updates, is one click for the user, and cannot get the
 * plugin pulled from the directory.
 */
class Smart_SEO_Whats_New {

    /** Last version for which the notice was shown. */
    const OPT_SEEN = 'smart_seo_last_seen_version';
    const NONCE    = 'smart_seo_whats_new';

    public static function init() {
        add_action('admin_init', [__CLASS__, 'handle_dismiss']);
        add_action('admin_init', [__CLASS__, 'maybe_prime'], 5);
        add_action('admin_notices', [__CLASS__, 'maybe_render']);
    }

    private static function current_version() {
        return defined('SMART_SEO_BOOSTER_VERSION') ? SMART_SEO_BOOSTER_VERSION : '0';
    }

    /**
     * On a brand-new install there is nothing to announce — the setup wizard
     * already runs. Record the current version silently so the first *upgrade*
     * is the first time this notice ever appears.
     */
    public static function maybe_prime() {
        if ( false === get_option( self::OPT_SEEN, false ) && ! get_option('smart_seo_options') ) {
            update_option( self::OPT_SEEN, self::current_version(), false );
        }
    }

    private static function should_show() {
        if ( ! current_user_can('manage_options') ) {
            return false;
        }
        $seen = get_option( self::OPT_SEEN, false );

        // Never seen a version at all (upgraded from a release before this
        // class existed), or seen an older one — either way, announce.
        if ( false === $seen ) {
            return true;
        }
        return version_compare( (string) $seen, self::current_version(), '<' );
    }

    public static function handle_dismiss() {
        if ( empty($_GET['smart_seo_whats_new_dismiss']) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nonce verified immediately below
            return;
        }
        if ( ! current_user_can('manage_options') ) {
            return;
        }
        check_admin_referer( self::NONCE, 'smart_seo_whats_new_nonce' );

        update_option( self::OPT_SEEN, self::current_version(), false );

        wp_safe_redirect( remove_query_arg( [ 'smart_seo_whats_new_dismiss', 'smart_seo_whats_new_nonce' ] ) );
        exit;
    }

    public static function maybe_render() {
        if ( ! self::should_show() ) {
            return;
        }

        $dismiss_url = wp_nonce_url(
            add_query_arg( 'smart_seo_whats_new_dismiss', '1' ),
            self::NONCE,
            'smart_seo_whats_new_nonce'
        );

        $changelog_url = add_query_arg(
            [
                'utm_source'   => 'plugin',
                'utm_medium'   => 'whats-new',
                'utm_campaign' => 'v' . self::current_version(),
            ],
            SMART_SEO_BOOSTER_URL
        );
        ?>
        <div class="notice notice-info is-dismissible smart-seo-whats-new">
            <p>
                <img src="<?php echo esc_url( SMART_SEO_BOOSTER_ICON_URL ); ?>" width="20" height="20" alt="" style="vertical-align:text-bottom;margin-inline-end:6px;" />
                <strong>
                    <?php
                    printf(
                        /* translators: %s: plugin version number */
                        esc_html__( 'Smart SEO Booster is now on version %s.', 'smart-seo-booster' ),
                        esc_html( self::current_version() )
                    );
                    ?>
                </strong>
                <?php esc_html_e( 'See what changed, and what is coming next.', 'smart-seo-booster' ); ?>
            </p>
            <p>
                <a href="<?php echo esc_url( $changelog_url ); ?>" class="button button-primary" target="_blank" rel="noopener">
                    <?php esc_html_e( "See what's new", 'smart-seo-booster' ); ?>
                </a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=smart-seo') ); ?>" class="button">
                    <?php esc_html_e( 'Open settings', 'smart-seo-booster' ); ?>
                </a>
                <a href="<?php echo esc_url( $dismiss_url ); ?>" class="button-link" style="margin-inline-start:8px;">
                    <?php esc_html_e( 'Dismiss', 'smart-seo-booster' ); ?>
                </a>
            </p>
        </div>
        <?php
    }
}

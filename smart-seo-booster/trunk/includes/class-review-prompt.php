<?php
defined('ABSPATH') || exit;

/**
 * A single, quiet request for a WordPress.org review.
 *
 * Deliberately conservative: it waits until the plugin has actually been used
 * for a while, appears only on this plugin's own screens plus the dashboard
 * (never on every admin page), never blocks anything, and can be dismissed
 * permanently in one click. Asking badly costs more goodwill than not asking
 * at all — and WordPress.org guidelines prohibit incentivising or requiring
 * reviews, so this only ever asks.
 */
class Smart_SEO_Review_Prompt {

    /** Timestamp of first install/upgrade to a version that tracks this. */
    const OPT_INSTALLED   = 'smart_seo_installed_at';
    /** '1' once the user has opted out permanently. */
    const OPT_DISMISSED   = 'smart_seo_review_dismissed';
    /** Timestamp before which the notice stays hidden ("maybe later"). */
    const OPT_SNOOZE      = 'smart_seo_review_snooze';

    const NONCE           = 'smart_seo_review_action';
    /** Days of use before asking the first time. */
    const DELAY_DAYS      = 14;
    /** Days to wait again after "maybe later". */
    const SNOOZE_DAYS     = 21;

    const REVIEW_URL      = 'https://wordpress.org/support/plugin/smart-seo-booster/reviews/#new-post';

    public static function init() {
        add_action('admin_init', [__CLASS__, 'record_install_time']);
        add_action('admin_init', [__CLASS__, 'handle_action']);
        add_action('admin_notices', [__CLASS__, 'maybe_render']);
    }

    /**
     * Stamp the install time the first time an admin loads a screen.
     *
     * Done lazily rather than only in the activation hook so that sites which
     * *upgrade* into this version also get a sensible clock start, instead of
     * being asked immediately.
     */
    public static function record_install_time() {
        if ( ! get_option( self::OPT_INSTALLED ) ) {
            update_option( self::OPT_INSTALLED, time(), false );
        }
    }

    /**
     * Whether the notice should appear on this request.
     */
    private static function should_show() {
        if ( ! current_user_can('manage_options') ) {
            return false;
        }
        if ( get_option( self::OPT_DISMISSED ) ) {
            return false;
        }

        $snooze = (int) get_option( self::OPT_SNOOZE, 0 );
        if ( $snooze && time() < $snooze ) {
            return false;
        }

        // Long enough to have formed an opinion?
        $installed = (int) get_option( self::OPT_INSTALLED, 0 );
        if ( ! $installed || ( time() - $installed ) < ( self::DELAY_DAYS * DAY_IN_SECONDS ) ) {
            return false;
        }

        // Actually configured, not just installed and forgotten — asking someone
        // who never set the plugin up invites a poor review, not a fair one.
        $options = get_option('smart_seo_options', []);
        if ( ! is_array( $options ) || empty( $options ) ) {
            return false;
        }

        return self::is_relevant_screen();
    }

    /**
     * Limit to this plugin's own screens plus the dashboard. Showing a review
     * request on every admin page is the behaviour that makes these hated.
     */
    private static function is_relevant_screen() {
        if ( ! function_exists('get_current_screen') ) {
            return false;
        }
        $screen = get_current_screen();
        if ( ! $screen ) {
            return false;
        }
        if ( 'dashboard' === $screen->id ) {
            return true;
        }
        return false !== strpos( (string) $screen->id, 'smart-seo' );
    }

    /**
     * Handle "maybe later" / "don't show again" links.
     */
    public static function handle_action() {
        if ( empty($_GET['smart_seo_review']) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nonce verified immediately below
            return;
        }
        if ( ! current_user_can('manage_options') ) {
            return;
        }
        check_admin_referer( self::NONCE, 'smart_seo_review_nonce' );

        $action = sanitize_key( wp_unslash( $_GET['smart_seo_review'] ) );
        if ( 'later' === $action ) {
            update_option( self::OPT_SNOOZE, time() + ( self::SNOOZE_DAYS * DAY_IN_SECONDS ), false );
        } elseif ( 'dismiss' === $action || 'done' === $action ) {
            update_option( self::OPT_DISMISSED, 1, false );
        }

        // Drop the query args so a refresh doesn't re-trigger the action.
        wp_safe_redirect( remove_query_arg( [ 'smart_seo_review', 'smart_seo_review_nonce' ] ) );
        exit;
    }

    private static function action_url( $action ) {
        return wp_nonce_url(
            add_query_arg( 'smart_seo_review', $action ),
            self::NONCE,
            'smart_seo_review_nonce'
        );
    }

    public static function maybe_render() {
        if ( ! self::should_show() ) {
            return;
        }
        ?>
        <div class="notice notice-info smart-seo-review-notice">
            <p>
                <img src="<?php echo esc_url( SMART_SEO_BOOSTER_ICON_URL ); ?>" width="20" height="20" alt="" style="vertical-align:text-bottom;margin-inline-end:6px;" />
                <strong><?php esc_html_e( 'Enjoying Smart SEO Booster?', 'smart-seo-booster' ); ?></strong>
                <?php esc_html_e( 'It is built and maintained by one developer, and given away free. A short review on WordPress.org helps other site owners find it — it is the only thing that really moves the needle for a small plugin.', 'smart-seo-booster' ); ?>
            </p>
            <p>
                <a href="<?php echo esc_url( self::REVIEW_URL ); ?>" class="button button-primary" target="_blank" rel="noopener">
                    <?php esc_html_e( 'Leave a review', 'smart-seo-booster' ); ?>
                </a>
                <a href="<?php echo esc_url( self::action_url('later') ); ?>" class="button">
                    <?php esc_html_e( 'Maybe later', 'smart-seo-booster' ); ?>
                </a>
                <a href="<?php echo esc_url( self::action_url('dismiss') ); ?>" class="button-link" style="margin-inline-start:8px;">
                    <?php esc_html_e( 'Do not show this again', 'smart-seo-booster' ); ?>
                </a>
            </p>
        </div>
        <?php
    }
}

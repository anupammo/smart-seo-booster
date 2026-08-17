<?php
defined('ABSPATH') || exit;

/**
 * Ask why, when someone deactivates the plugin.
 *
 * Uninstall feedback is the most honest signal a small plugin gets — people
 * who leave rarely open a support thread. The rules this follows:
 *
 * - Deactivation is NEVER blocked. "Skip & deactivate" is always one click.
 * - Nothing is transmitted anywhere unless the user explicitly presses
 *   "Send feedback". There is no silent phone-home, so the plugin needs no
 *   privacy disclosure for this and stays inside the WordPress.org
 *   guideline against tracking users without consent.
 * - Submitting simply opens the developer's feedback page in a new tab with
 *   the answer pre-filled. The user can see exactly what is being sent
 *   before it goes anywhere, and can close the tab to send nothing.
 */
class Smart_SEO_Deactivation_Feedback {

    public static function init() {
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue']);
        add_action('admin_footer-plugins.php', [__CLASS__, 'render_modal']);
    }

    /**
     * Reason options. Keys are sent as a slug; labels are translated.
     *
     * @return array<string,string>
     */
    public static function reasons() {
        return [
            'temporary'    => __( 'I only needed it temporarily', 'smart-seo-booster' ),
            'not_working'  => __( 'It did not work the way I expected', 'smart-seo-booster' ),
            'missing'      => __( 'It is missing a feature I need', 'smart-seo-booster' ),
            'better'       => __( 'I found a plugin I like better', 'smart-seo-booster' ),
            'broke'        => __( 'It broke my site or caused an error', 'smart-seo-booster' ),
            'confusing'    => __( 'I could not work out how to use it', 'smart-seo-booster' ),
            'other'        => __( 'Something else', 'smart-seo-booster' ),
        ];
    }

    public static function enqueue( $hook ) {
        if ( 'plugins.php' !== $hook || ! current_user_can('activate_plugins') ) {
            return;
        }

        $ver = defined('SMART_SEO_BOOSTER_VERSION') ? SMART_SEO_BOOSTER_VERSION : false;

        wp_enqueue_style(
            'smart-seo-deactivation-feedback',
            plugin_dir_url(__FILE__) . '../css/deactivation-feedback.css',
            [],
            $ver
        );
        wp_enqueue_script(
            'smart-seo-deactivation-feedback',
            plugin_dir_url(__FILE__) . '../js/deactivation-feedback.js',
            [],
            $ver,
            true
        );
        wp_localize_script( 'smart-seo-deactivation-feedback', 'smartSeoFeedback', [
            // Identifies our own row on the plugins screen so we never
            // intercept the Deactivate link of somebody else's plugin.
            'slug'        => 'smart-seo-booster',
            'feedbackUrl' => SMART_SEO_BOOSTER_URL,
        ] );
    }

    public static function render_modal() {
        if ( ! current_user_can('activate_plugins') ) {
            return;
        }
        ?>
        <div class="ssb-fb-overlay" id="ssb-fb-overlay" hidden>
            <div class="ssb-fb-modal" role="dialog" aria-modal="true" aria-labelledby="ssb-fb-title">
                <div class="ssb-fb-head">
                    <img src="<?php echo esc_url( SMART_SEO_BOOSTER_ICON_URL ); ?>" width="28" height="28" alt="" />
                    <h2 id="ssb-fb-title"><?php esc_html_e( 'Before you go — what went wrong?', 'smart-seo-booster' ); ?></h2>
                </div>

                <p class="ssb-fb-intro">
                    <?php esc_html_e( 'Smart SEO Booster is built and maintained by one developer. Knowing why you are leaving is the single most useful thing you can share — it decides what gets fixed next.', 'smart-seo-booster' ); ?>
                </p>

                <ul class="ssb-fb-reasons">
                    <?php foreach ( self::reasons() as $key => $label ) : ?>
                        <li>
                            <label>
                                <input type="radio" name="ssb_fb_reason" value="<?php echo esc_attr( $key ); ?>" />
                                <span><?php echo esc_html( $label ); ?></span>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <label class="ssb-fb-details-label" for="ssb-fb-details">
                    <?php esc_html_e( 'Anything else? (optional)', 'smart-seo-booster' ); ?>
                </label>
                <textarea id="ssb-fb-details" rows="3" maxlength="500"></textarea>

                <p class="ssb-fb-privacy">
                    <?php esc_html_e( 'Nothing is sent anywhere unless you choose "Send feedback". It opens a page where you can see exactly what is being shared before submitting it. No site data, URLs or personal details are collected.', 'smart-seo-booster' ); ?>
                </p>

                <div class="ssb-fb-actions">
                    <button type="button" class="button button-primary" id="ssb-fb-send">
                        <?php esc_html_e( 'Send feedback &amp; deactivate', 'smart-seo-booster' ); ?>
                    </button>
                    <button type="button" class="button" id="ssb-fb-skip">
                        <?php esc_html_e( 'Skip &amp; deactivate', 'smart-seo-booster' ); ?>
                    </button>
                    <button type="button" class="button-link ssb-fb-cancel" id="ssb-fb-cancel">
                        <?php esc_html_e( 'Cancel', 'smart-seo-booster' ); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
}

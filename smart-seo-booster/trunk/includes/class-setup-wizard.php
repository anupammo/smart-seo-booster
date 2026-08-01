<?php
defined('ABSPATH') || exit;

/**
 * Guided setup wizard.
 *
 * A lightweight multi-step onboarding that writes sensible defaults into
 * smart_seo_options. Each step merges only its own keys, so re-running the
 * wizard never wipes unrelated settings.
 */
class Smart_SEO_Setup_Wizard {

    const NONCE  = 'smart_seo_wizard';
    const STEPS  = 4;

    public static function init() {
        add_action('admin_init', [__CLASS__, 'maybe_handle_submit']);
        add_action('admin_init', [__CLASS__, 'maybe_redirect_after_activation']);
    }

    /**
     * Fields saved by each step (name => sanitizer callable|type).
     */
    private static function step_fields( $step ) {
        $map = [
            1 => [ 'site_type' => 'key', 'enable_schema' => 'bool' ],
            2 => [ 'separator' => 'text', 'title_template' => 'text', 'description_template' => 'text' ],
            3 => [ 'enable_sitemap' => 'bool', 'enable_breadcrumbs' => 'bool' ],
            4 => [ 'verify_google' => 'text', 'verify_bing' => 'text' ],
        ];
        return $map[ $step ] ?? [];
    }

    public static function maybe_redirect_after_activation() {
        if ( ! get_transient('smart_seo_activation_redirect') ) {
            return;
        }
        delete_transient('smart_seo_activation_redirect');

        // Don't hijack bulk or network activations.
        if ( isset($_GET['activate-multi']) || is_network_admin() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only routing check
            return;
        }
        if ( ! current_user_can('manage_options') ) {
            return;
        }

        wp_safe_redirect( admin_url('admin.php?page=smart-seo-setup') );
        exit;
    }

    public static function maybe_handle_submit() {
        if ( empty($_POST['smart_seo_wizard_nonce']) ) {
            return;
        }
        $nonce = sanitize_text_field( wp_unslash( $_POST['smart_seo_wizard_nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, self::NONCE ) ) {
            return;
        }
        if ( ! current_user_can('manage_options') ) {
            return;
        }

        $step = isset($_POST['smart_seo_step']) ? absint( wp_unslash( $_POST['smart_seo_step'] ) ) : 1;

        $options = get_option('smart_seo_options', []);
        if ( ! is_array($options) ) {
            $options = [];
        }

        foreach ( self::step_fields( $step ) as $name => $type ) {
            switch ( $type ) {
                case 'bool':
                    $options[ $name ] = ! empty($_POST[ $name ]) ? 1 : 0;
                    break;
                case 'key':
                    $options[ $name ] = isset($_POST[ $name ]) ? sanitize_key( wp_unslash( $_POST[ $name ] ) ) : '';
                    break;
                case 'text':
                default:
                    $options[ $name ] = isset($_POST[ $name ]) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : '';
                    break;
            }
        }

        update_option('smart_seo_options', $options);

        $next = $step + 1;
        $target = $next > self::STEPS
            ? admin_url('admin.php?page=smart-seo-setup&step=done')
            : admin_url('admin.php?page=smart-seo-setup&step=' . $next);

        wp_safe_redirect( $target );
        exit;
    }

    public static function render() {
        $step_raw = isset($_GET['step']) ? sanitize_key( wp_unslash( $_GET['step'] ) ) : '1'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only step routing
        $options  = get_option('smart_seo_options', []);

        echo '<div class="wrap smart-seo-settings smart-seo-wizard ssb-app ssb-adapt">';
        echo '<h1><img src="' . esc_url( SMART_SEO_BOOSTER_ICON_URL ) . '" width="26" height="26" alt="" class="ssb-h1-icon" /> ' . esc_html__( 'Smart SEO Booster — Setup', 'smart-seo-booster' ) . '</h1>';

        if ( $step_raw === 'done' ) {
            self::render_done();
            echo '</div>';
            return;
        }

        $step = max( 1, min( self::STEPS, (int) $step_raw ) );
        self::render_progress( $step );

        echo '<div class="card smart-seo-card">';
        echo '<form method="post" action="' . esc_url( admin_url('admin.php?page=smart-seo-setup') ) . '">';
        wp_nonce_field( self::NONCE, 'smart_seo_wizard_nonce' );
        echo '<input type="hidden" name="smart_seo_step" value="' . esc_attr( $step ) . '" />';

        switch ( $step ) {
            case 1:
                self::step_site_type( $options );
                break;
            case 2:
                self::step_titles( $options );
                break;
            case 3:
                self::step_features( $options );
                break;
            case 4:
                self::step_verification( $options );
                break;
        }

        submit_button( $step === self::STEPS ? __( 'Finish', 'smart-seo-booster' ) : __( 'Continue', 'smart-seo-booster' ) );
        echo ' <a class="button-link" href="' . esc_url( admin_url('admin.php?page=smart-seo') ) . '">' . esc_html__( 'Skip', 'smart-seo-booster' ) . '</a>';
        echo '</form></div></div>';
    }

    private static function render_progress( $step ) {
        $labels = [
            1 => __( 'Site type', 'smart-seo-booster' ),
            2 => __( 'Titles', 'smart-seo-booster' ),
            3 => __( 'Features', 'smart-seo-booster' ),
            4 => __( 'Verification', 'smart-seo-booster' ),
        ];
        echo '<ol class="smart-seo-steps">';
        foreach ( $labels as $n => $label ) {
            $class = $n === $step ? ' class="current"' : ( $n < $step ? ' class="done"' : '' );
            echo '<li' . $class . '>' . esc_html( $n . '. ' . $label ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $class is a fixed literal
        }
        echo '</ol>';
    }

    private static function step_site_type( $options ) {
        $current = $options['site_type'] ?? 'blog';
        $types = [
            'blog'      => __( 'Blog / Publisher', 'smart-seo-booster' ),
            'business'  => __( 'Business / Organization', 'smart-seo-booster' ),
            'store'     => __( 'Online Store', 'smart-seo-booster' ),
            'portfolio' => __( 'Portfolio / Personal', 'smart-seo-booster' ),
        ];
        echo '<h2>' . esc_html__( 'What kind of site is this?', 'smart-seo-booster' ) . '</h2>';
        echo '<p class="description">' . esc_html__( 'This sets sensible schema defaults. You can change everything later.', 'smart-seo-booster' ) . '</p>';
        echo '<fieldset>';
        foreach ( $types as $value => $label ) {
            echo '<label style="display:block;margin:8px 0;"><input type="radio" name="site_type" value="' . esc_attr( $value ) . '" ' . checked( $current, $value, false ) . ' /> ' . esc_html( $label ) . '</label>';
        }
        echo '</fieldset>';
        echo '<p><label><input type="checkbox" name="enable_schema" value="1" ' . checked( ! empty( $options['enable_schema'] ), true, false ) . ' checked /> ' . esc_html__( 'Enable JSON-LD schema markup', 'smart-seo-booster' ) . '</label></p>';
    }

    private static function step_titles( $options ) {
        $sep   = $options['separator'] ?? '-';
        $title = $options['title_template'] ?? '%%title%% %%sep%% %%sitename%%';
        $desc  = $options['description_template'] ?? '%%excerpt%%';
        echo '<h2>' . esc_html__( 'Titles &amp; meta descriptions', 'smart-seo-booster' ) . '</h2>';
        echo '<p><label>' . esc_html__( 'Separator', 'smart-seo-booster' ) . '<br><select name="separator">';
        foreach ( Smart_SEO_Settings::separator_options() as $sep_value => $sep_label ) {
            echo '<option value="' . esc_attr( $sep_value ) . '" ' . selected( $sep, $sep_value, false ) . '>' . esc_html( $sep_label ) . '</option>';
        }
        echo '</select></label></p>';
        echo '<p><label>' . esc_html__( 'Title template', 'smart-seo-booster' ) . '<br><input type="text" class="large-text" name="title_template" value="' . esc_attr( $title ) . '" /></label></p>';
        echo '<p><label>' . esc_html__( 'Description template', 'smart-seo-booster' ) . '<br><input type="text" class="large-text" name="description_template" value="' . esc_attr( $desc ) . '" /></label></p>';
        echo '<p class="description">' . esc_html__( 'Variables:', 'smart-seo-booster' ) . ' <code>' . esc_html( implode( '</code> <code>', array_keys( Smart_SEO_Meta_Templates::variables() ) ) ) . '</code></p>';
    }

    private static function step_features( $options ) {
        echo '<h2>' . esc_html__( 'Turn on the essentials', 'smart-seo-booster' ) . '</h2>';
        echo '<p><label><input type="checkbox" name="enable_sitemap" value="1" ' . checked( ! empty( $options['enable_sitemap'] ), true, false ) . ' checked /> ' . esc_html__( 'XML Sitemap (at /sitemap.xml)', 'smart-seo-booster' ) . '</label></p>';
        echo '<p><label><input type="checkbox" name="enable_breadcrumbs" value="1" ' . checked( ! empty( $options['enable_breadcrumbs'] ), true, false ) . ' checked /> ' . esc_html__( 'Breadcrumbs', 'smart-seo-booster' ) . '</label></p>';
    }

    private static function step_verification( $options ) {
        echo '<h2>' . esc_html__( 'Search engine verification (optional)', 'smart-seo-booster' ) . '</h2>';
        echo '<p class="description">' . esc_html__( 'Paste only the verification code, not the whole meta tag.', 'smart-seo-booster' ) . '</p>';
        echo '<p><label>' . esc_html__( 'Google', 'smart-seo-booster' ) . '<br><input type="text" class="regular-text" name="verify_google" value="' . esc_attr( $options['verify_google'] ?? '' ) . '" /></label></p>';
        echo '<p><label>' . esc_html__( 'Bing', 'smart-seo-booster' ) . '<br><input type="text" class="regular-text" name="verify_bing" value="' . esc_attr( $options['verify_bing'] ?? '' ) . '" /></label></p>';
    }

    private static function render_done() {
        echo '<div class="card smart-seo-card smart-seo-done">';
        echo '<img src="' . esc_url( SMART_SEO_BOOSTER_LINE_ICON_URL ) . '" width="96" height="96" alt="" class="ssb-line-icon" />';
        echo '<h2><span class="dashicons dashicons-awards" aria-hidden="true"></span> ' . esc_html__( 'You are all set!', 'smart-seo-booster' ) . '</h2>';
        echo '<p>' . esc_html__( 'Smart SEO Booster is configured. Here is what to do next:', 'smart-seo-booster' ) . '</p>';
        echo '<ul style="list-style:disc;margin-inline-start:20px;">';
        echo '<li>' . esc_html__( 'Edit a post and open the SEO panel to fine-tune titles and social previews.', 'smart-seo-booster' ) . '</li>';
        echo '<li>' . wp_kses( sprintf( /* translators: %s: sitemap URL */ __( 'Submit your sitemap: %s', 'smart-seo-booster' ), '<a href="' . esc_url( home_url('/sitemap.xml') ) . '" target="_blank" rel="noopener">' . esc_html( home_url('/sitemap.xml') ) . '</a>' ), [ 'a' => [ 'href' => [], 'target' => [], 'rel' => [] ] ] ) . '</li>';
        echo '</ul>';
        echo '<p><a class="button button-primary" href="' . esc_url( admin_url('admin.php?page=smart-seo') ) . '">' . esc_html__( 'Go to Settings', 'smart-seo-booster' ) . '</a></p>';
        echo '</div>';
    }
}

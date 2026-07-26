<?php
defined('ABSPATH') || exit;

/**
 * Simple redirection manager + 404 logger.
 *
 * Redirects and the 404 log are stored in options (no custom tables). Suited to
 * the small-to-medium sites this plugin targets; the log is capped to stay lean.
 */
class Smart_SEO_Redirects {

    const OPTION_REDIRECTS = 'smart_seo_redirects';
    const OPTION_LOG       = 'smart_seo_404_log';
    const NONCE            = 'smart_seo_redirects';
    const LOG_CAP          = 200;

    public static function init() {
        add_action('template_redirect', [__CLASS__, 'maybe_redirect'], 1);
        add_action('template_redirect', [__CLASS__, 'log_404'], 999);
        add_action('admin_post_smart_seo_save_redirect', [__CLASS__, 'save_redirect']);
        add_action('admin_post_smart_seo_delete_redirect', [__CLASS__, 'delete_redirect']);
    }

    private static function current_path() {
        $uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
        $path = wp_parse_url( $uri, PHP_URL_PATH );
        return self::normalize( $path ? $path : '/' );
    }

    private static function normalize( $path ) {
        $path = '/' . ltrim( (string) $path, '/' );
        if ( strlen( $path ) > 1 ) {
            $path = rtrim( $path, '/' );
        }
        return $path;
    }

    public static function get_redirects() {
        $redirects = get_option( self::OPTION_REDIRECTS, [] );
        return is_array( $redirects ) ? $redirects : [];
    }

    public static function maybe_redirect() {
        if ( is_admin() ) {
            return;
        }
        $path = self::current_path();
        foreach ( self::get_redirects() as $r ) {
            if ( empty( $r['source'] ) || empty( $r['target'] ) ) {
                continue;
            }
            if ( self::normalize( $r['source'] ) === $path ) {
                $type   = in_array( (int) $r['type'], [ 301, 302, 307 ], true ) ? (int) $r['type'] : 301;
                $target = $r['target'];
                // Allow relative or absolute targets.
                if ( strpos( $target, 'http' ) !== 0 ) {
                    $target = home_url( '/' . ltrim( $target, '/' ) );
                }
                wp_safe_redirect( $target, $type );
                exit;
            }
        }
    }

    public static function log_404() {
        if ( is_admin() || ! is_404() ) {
            return;
        }
        $path = self::current_path();
        if ( $path === '/' ) {
            return;
        }

        $log = get_option( self::OPTION_LOG, [] );
        if ( ! is_array( $log ) ) {
            $log = [];
        }

        if ( isset( $log[ $path ] ) ) {
            $log[ $path ]['count'] = (int) $log[ $path ]['count'] + 1;
            $log[ $path ]['last']  = time();
        } else {
            // Cap the log size — drop the oldest entry when full.
            if ( count( $log ) >= self::LOG_CAP ) {
                array_shift( $log );
            }
            $log[ $path ] = [ 'count' => 1, 'last' => time() ];
        }

        update_option( self::OPTION_LOG, $log, false );
    }

    public static function save_redirect() {
        if ( ! current_user_can('manage_options') ) {
            wp_die( esc_html__('You do not have sufficient permissions.', 'smart-seo-booster') );
        }
        check_admin_referer( self::NONCE, 'smart_seo_redirects_nonce' );

        $source = isset($_POST['source']) ? esc_url_raw( wp_unslash( $_POST['source'] ) ) : '';
        $target = isset($_POST['target']) ? esc_url_raw( wp_unslash( $_POST['target'] ) ) : '';
        $type   = isset($_POST['type']) ? absint( wp_unslash( $_POST['type'] ) ) : 301;

        // Keep source as a path; strip any host the user pasted.
        $source_path = wp_parse_url( $source, PHP_URL_PATH );
        $source      = $source_path ? self::normalize( $source_path ) : self::normalize( $source );

        if ( $source && $target ) {
            $redirects = self::get_redirects();
            $redirects[] = [
                'source' => $source,
                'target' => $target,
                'type'   => in_array( $type, [ 301, 302, 307 ], true ) ? $type : 301,
            ];
            update_option( self::OPTION_REDIRECTS, $redirects, false );

            // Remove the now-resolved URL from the 404 log.
            $log = get_option( self::OPTION_LOG, [] );
            if ( is_array( $log ) && isset( $log[ $source ] ) ) {
                unset( $log[ $source ] );
                update_option( self::OPTION_LOG, $log, false );
            }
        }

        wp_safe_redirect( admin_url('admin.php?page=smart-seo-redirects&saved=1') );
        exit;
    }

    public static function delete_redirect() {
        if ( ! current_user_can('manage_options') ) {
            wp_die( esc_html__('You do not have sufficient permissions.', 'smart-seo-booster') );
        }
        check_admin_referer( self::NONCE, 'smart_seo_redirects_nonce' );

        $index     = isset($_POST['index']) ? absint( wp_unslash( $_POST['index'] ) ) : -1;
        $redirects = self::get_redirects();
        if ( isset( $redirects[ $index ] ) ) {
            unset( $redirects[ $index ] );
            update_option( self::OPTION_REDIRECTS, array_values( $redirects ), false );
        }

        wp_safe_redirect( admin_url('admin.php?page=smart-seo-redirects') );
        exit;
    }

    public static function render_page() {
        if ( ! current_user_can('manage_options') ) {
            wp_die( esc_html__('You do not have sufficient permissions to access this page.', 'smart-seo-booster') );
        }

        $redirects = self::get_redirects();
        $log       = get_option( self::OPTION_LOG, [] );
        $log       = is_array( $log ) ? $log : [];
        arsort( $log );

        echo '<div class="wrap smart-seo-settings">';
        echo '<h1>' . esc_html__( 'Redirections', 'smart-seo-booster' ) . '</h1>';

        // Add form.
        echo '<div class="card smart-seo-card"><h2>' . esc_html__( 'Add redirect', 'smart-seo-booster' ) . '</h2>';
        echo '<form method="post" action="' . esc_url( admin_url('admin-post.php') ) . '">';
        wp_nonce_field( self::NONCE, 'smart_seo_redirects_nonce' );
        echo '<input type="hidden" name="action" value="smart_seo_save_redirect" />';
        echo '<p><label>' . esc_html__( 'Source path', 'smart-seo-booster' ) . '<br><input type="text" name="source" class="regular-text" placeholder="/old-url" required></label></p>';
        echo '<p><label>' . esc_html__( 'Target URL', 'smart-seo-booster' ) . '<br><input type="text" name="target" class="regular-text" placeholder="/new-url" required></label></p>';
        echo '<p><label>' . esc_html__( 'Type', 'smart-seo-booster' ) . ' <select name="type">';
        echo '<option value="301">301 — ' . esc_html__( 'Permanent', 'smart-seo-booster' ) . '</option>';
        echo '<option value="302">302 — ' . esc_html__( 'Temporary', 'smart-seo-booster' ) . '</option>';
        echo '<option value="307">307 — ' . esc_html__( 'Temporary (strict)', 'smart-seo-booster' ) . '</option>';
        echo '</select></label></p>';
        submit_button( __( 'Add Redirect', 'smart-seo-booster' ) );
        echo '</form></div>';

        // Redirects table.
        echo '<h2>' . esc_html__( 'Active redirects', 'smart-seo-booster' ) . '</h2>';
        echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Source', 'smart-seo-booster' ) . '</th><th>' . esc_html__( 'Target', 'smart-seo-booster' ) . '</th><th>' . esc_html__( 'Type', 'smart-seo-booster' ) . '</th><th></th></tr></thead><tbody>';
        if ( empty( $redirects ) ) {
            echo '<tr><td colspan="4">' . esc_html__( 'No redirects yet.', 'smart-seo-booster' ) . '</td></tr>';
        } else {
            foreach ( $redirects as $i => $r ) {
                echo '<tr><td><code>' . esc_html( $r['source'] ) . '</code></td><td>' . esc_html( $r['target'] ) . '</td><td>' . esc_html( $r['type'] ) . '</td><td>';
                echo '<form method="post" action="' . esc_url( admin_url('admin-post.php') ) . '" style="display:inline;">';
                wp_nonce_field( self::NONCE, 'smart_seo_redirects_nonce' );
                echo '<input type="hidden" name="action" value="smart_seo_delete_redirect" />';
                echo '<input type="hidden" name="index" value="' . esc_attr( $i ) . '" />';
                submit_button( __( 'Delete', 'smart-seo-booster' ), 'delete small', 'submit', false );
                echo '</form></td></tr>';
            }
        }
        echo '</tbody></table>';

        // 404 log.
        echo '<h2 style="margin-top:24px;">' . esc_html__( '404 Log', 'smart-seo-booster' ) . '</h2>';
        echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'URL', 'smart-seo-booster' ) . '</th><th>' . esc_html__( 'Hits', 'smart-seo-booster' ) . '</th><th>' . esc_html__( 'Last seen', 'smart-seo-booster' ) . '</th><th></th></tr></thead><tbody>';
        if ( empty( $log ) ) {
            echo '<tr><td colspan="4">' . esc_html__( 'No 404s logged yet.', 'smart-seo-booster' ) . '</td></tr>';
        } else {
            foreach ( $log as $url => $data ) {
                echo '<tr><td><code>' . esc_html( $url ) . '</code></td><td>' . absint( $data['count'] ) . '</td><td>' . esc_html( human_time_diff( (int) $data['last'] ) . ' ' . __( 'ago', 'smart-seo-booster' ) ) . '</td><td>';
                echo '<form method="post" action="' . esc_url( admin_url('admin-post.php') ) . '" style="display:inline;">';
                wp_nonce_field( self::NONCE, 'smart_seo_redirects_nonce' );
                echo '<input type="hidden" name="action" value="smart_seo_save_redirect" />';
                echo '<input type="hidden" name="source" value="' . esc_attr( $url ) . '" />';
                echo '<input type="text" name="target" class="small-text" placeholder="/new-url" required>';
                echo '<input type="hidden" name="type" value="301" />';
                submit_button( __( 'Redirect', 'smart-seo-booster' ), 'secondary small', 'submit', false );
                echo '</form></td></tr>';
            }
        }
        echo '</tbody></table>';
        echo '</div>';
    }
}

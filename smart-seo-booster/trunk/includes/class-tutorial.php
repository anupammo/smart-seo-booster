<?php
defined('ABSPATH') || exit;

/**
 * Tutorial — a guided, step-by-step tour of the plugin's main features.
 * Read-only (nothing is saved); each step just links out to the real screen
 * it describes. Replaces the old, unreachable "Help & Documentation" page,
 * which referenced constants and options that no longer exist.
 */
class Smart_SEO_Tutorial {

    const STEPS = 6;

    public static function init() {
        // No hooks needed beyond the submenu registration in class-admin-ui.php,
        // which calls render() directly.
    }

    /**
     * @return array<int, array{title:string, body:callable}>
     */
    private static function steps() {
        return [
            1 => [
                'label' => __( 'Welcome', 'smart-seo-booster' ),
                'render' => [ __CLASS__, 'step_welcome' ],
            ],
            2 => [
                'label' => __( 'Titles &amp; Metas', 'smart-seo-booster' ),
                'render' => [ __CLASS__, 'step_titles' ],
            ],
            3 => [
                'label' => __( 'Schema', 'smart-seo-booster' ),
                'render' => [ __CLASS__, 'step_schema' ],
            ],
            4 => [
                'label' => __( 'Sitemaps &amp; Breadcrumbs', 'smart-seo-booster' ),
                'render' => [ __CLASS__, 'step_technical' ],
            ],
            5 => [
                'label' => __( 'Content Audit', 'smart-seo-booster' ),
                'render' => [ __CLASS__, 'step_audit' ],
            ],
            6 => [
                'label' => __( 'Block Editor', 'smart-seo-booster' ),
                'render' => [ __CLASS__, 'step_editor' ],
            ],
        ];
    }

    public static function render() {
        if ( ! current_user_can('manage_options') ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'smart-seo-booster' ) );
        }

        $steps = self::steps();
        $step  = isset($_GET['step']) ? absint( wp_unslash( $_GET['step'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only tour navigation
        $step  = max( 1, min( self::STEPS, $step ) );

        echo '<div class="wrap smart-seo-settings smart-seo-wizard ssb-app ssb-adapt">';
        echo '<h1><img src="' . esc_url( SMART_SEO_BOOSTER_ICON_URL ) . '" width="26" height="26" alt="" class="ssb-h1-icon" /> ' . esc_html__( 'Smart SEO Booster — Tutorial', 'smart-seo-booster' ) . '</h1>';

        echo '<ol class="smart-seo-steps">';
        foreach ( $steps as $n => $s ) {
            $class = $n === $step ? ' class="current"' : ( $n < $step ? ' class="done"' : '' );
            $url   = esc_url( admin_url( 'admin.php?page=smart-seo-tutorial&step=' . $n ) );
            echo '<li' . $class . '><a href="' . $url . '">' . esc_html( $n . '. ' . $s['label'] ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $class is a fixed literal
        }
        echo '</ol>';

        echo '<div class="card smart-seo-card smart-seo-tutorial-step">';
        call_user_func( $steps[ $step ]['render'] );
        echo '</div>';

        echo '<p class="smart-seo-tutorial-nav">';
        if ( $step > 1 ) {
            echo '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=smart-seo-tutorial&step=' . ( $step - 1 ) ) ) . '">&larr; ' . esc_html__( 'Previous', 'smart-seo-booster' ) . '</a> ';
        }
        if ( $step < self::STEPS ) {
            echo '<a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=smart-seo-tutorial&step=' . ( $step + 1 ) ) ) . '">' . esc_html__( 'Next', 'smart-seo-booster' ) . ' &rarr;</a>';
        } else {
            echo '<a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=smart-seo' ) ) . '">' . esc_html__( 'Go to Settings', 'smart-seo-booster' ) . '</a>';
        }
        echo '</p>';

        echo '</div>';
    }

    private static function feature_icon( $dashicon ) {
        echo '<span class="ssb-ico"><span class="dashicons dashicons-' . esc_attr( $dashicon ) . '" aria-hidden="true"></span></span>';
    }

    private static function step_welcome() {
        echo '<h2>' . esc_html__( 'Welcome to Smart SEO Booster', 'smart-seo-booster' ) . '</h2>';
        echo '<p class="description">' . esc_html__( 'This short tour covers the six things worth knowing before you start. Use Next/Previous below, or jump to any step above.', 'smart-seo-booster' ) . '</p>';
        echo '<div class="ssb-grid">';
        $items = [
            [ 'admin-page', __( 'Titles &amp; Metas', 'smart-seo-booster' ), __( 'Automatic, template-driven titles and descriptions.', 'smart-seo-booster' ) ],
            [ 'schema', __( 'Schema (JSON-LD)', 'smart-seo-booster' ), __( '14 structured data types, generated automatically.', 'smart-seo-booster' ) ],
            [ 'admin-links', __( 'Sitemaps &amp; Breadcrumbs', 'smart-seo-booster' ), __( 'Technical SEO, on by default.', 'smart-seo-booster' ) ],
            [ 'chart-area', __( 'Content Audit', 'smart-seo-booster' ), __( 'A live score for every post and page.', 'smart-seo-booster' ) ],
        ];
        foreach ( $items as $item ) {
            echo '<div class="ssb-card"><div class="ssb-stat">';
            self::feature_icon( $item[0] );
            echo '<div><div class="ssb-num" style="font-size:14px;">' . wp_kses_post( $item[1] ) . '</div><div class="ssb-lbl">' . esc_html( $item[2] ) . '</div></div>';
            echo '</div></div>';
        }
        echo '</div>';
        echo '<p><a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=smart-seo-setup' ) ) . '">' . esc_html__( 'Run Setup Wizard', 'smart-seo-booster' ) . '</a></p>';
        echo '<p class="description">' . esc_html(
            sprintf(
                /* translators: %s: plugin version number, e.g. 2.1.1 */
                __( 'Smart SEO Booster version %s', 'smart-seo-booster' ),
                defined('SMART_SEO_BOOSTER_VERSION') ? SMART_SEO_BOOSTER_VERSION : ''
            )
        ) . '</p>';
    }

    private static function step_titles() {
        echo '<h2>' . esc_html__( 'Titles &amp; meta descriptions', 'smart-seo-booster' ) . '</h2>';
        echo '<p>' . esc_html__( 'Set a title/description template once in Settings, and every post uses it automatically. Variables like %%title%%, %%sitename%% and %%excerpt%% get replaced per-post; override any single post from its own SEO panel.', 'smart-seo-booster' ) . '</p>';
        echo '<p><a class="button" href="' . esc_url( admin_url( 'admin.php?page=smart-seo#panel-smart_seo_titles' ) ) . '">' . esc_html__( 'Go to Settings', 'smart-seo-booster' ) . '</a></p>';
    }

    private static function step_schema() {
        echo '<h2>' . esc_html__( 'Schema type', 'smart-seo-booster' ) . '</h2>';
        echo '<p>' . esc_html__( 'This sets sensible schema defaults. You can change everything later.', 'smart-seo-booster' ) . ' ' . esc_html__( 'Choose a specific type per post from the SEO panel\'s Advanced tab (Article, Product, Recipe, Event, and more), or rely on the automatic default.', 'smart-seo-booster' ) . '</p>';
        echo '<p><a class="button" href="' . esc_url( admin_url( 'admin.php?page=smart-seo#panel-smart_seo_schema_data' ) ) . '">' . esc_html__( 'Schema Details', 'smart-seo-booster' ) . '</a></p>';
    }

    private static function step_technical() {
        echo '<h2>' . esc_html__( 'XML Sitemap (at /sitemap.xml)', 'smart-seo-booster' ) . ' &amp; ' . esc_html__( 'Breadcrumbs', 'smart-seo-booster' ) . '</h2>';
        echo '<p>' . esc_html__( 'Renders the breadcrumb trail with schema on the front end.', 'smart-seo-booster' ) . ' ' . esc_html__( 'Use [smart_seo_breadcrumbs] or the smart_seo_breadcrumbs() template tag.', 'smart-seo-booster' ) . '</p>';
        echo '<p><a class="button" href="' . esc_url( home_url( '/sitemap.xml' ) ) . '" target="_blank" rel="noopener">' . esc_html( home_url( '/sitemap.xml' ) ) . '</a></p>';
    }

    private static function step_audit() {
        echo '<h2>' . esc_html__( 'SEO Audit', 'smart-seo-booster' ) . '</h2>';
        echo '<p>' . esc_html__( 'A site-wide dashboard with an average score gauge, distribution chart, and the content that most needs attention. Every post/page also gets a compact score box in its own edit screen, with a full breakdown available from there.', 'smart-seo-booster' ) . '</p>';
        echo '<p><a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=smart-seo-audit' ) ) . '">' . esc_html__( 'View Audit Report', 'smart-seo-booster' ) . '</a></p>';
    }

    private static function step_editor() {
        echo '<h2>' . esc_html__( 'Search Appearance', 'smart-seo-booster' ) . '</h2>';
        echo '<p>' . esc_html__( 'While editing a post or page, look for the rocket icon in the block editor\'s top toolbar — it opens the full Smart SEO sidebar with a live search preview, social preview card, and advanced options. A quick-access panel with the essentials is also always visible under the Post/Page Document tab.', 'smart-seo-booster' ) . '</p>';
        echo '<p><a class="button" href="' . esc_url( admin_url( 'edit.php' ) ) . '">' . esc_html__( 'Edit Posts', 'smart-seo-booster' ) . '</a></p>';
    }
}

<?php
defined('ABSPATH') || exit;

class Smart_SEO_Admin_UI {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }

    public static function add_menu() {
        add_menu_page(
            __( 'Smart SEO Booster', 'smart-seo-booster' ),
            __( 'Smart SEO', 'smart-seo-booster' ),
            'manage_options',
            'smart-seo',
            [__CLASS__, 'render_settings_page'],
            'dashicons-chart-line',
            80
        );

        add_submenu_page(
            'smart-seo',
            __( 'Settings', 'smart-seo-booster' ),
            __( 'Settings', 'smart-seo-booster' ),
            'manage_options',
            'smart-seo',
            [__CLASS__, 'render_settings_page']
        );

        add_submenu_page(
            'smart-seo',
            __( 'SEO Audit Report', 'smart-seo-booster' ),
            __( 'Audit Report', 'smart-seo-booster' ),
            'manage_options',
            'smart-seo-audit',
            [__CLASS__, 'render_audit_report']
        );

        add_submenu_page(
            'smart-seo',
            __( 'Redirections', 'smart-seo-booster' ),
            __( 'Redirections', 'smart-seo-booster' ),
            'manage_options',
            'smart-seo-redirects',
            [ 'Smart_SEO_Redirects', 'render_page' ]
        );

        add_submenu_page(
            'smart-seo',
            __( 'Bulk SEO Editor', 'smart-seo-booster' ),
            __( 'Bulk Editor', 'smart-seo-booster' ),
            'edit_others_posts',
            'smart-seo-bulk',
            [ 'Smart_SEO_Bulk_Editor', 'render_page' ]
        );

        add_submenu_page(
            'smart-seo',
            __( 'Import SEO Data', 'smart-seo-booster' ),
            __( 'Import', 'smart-seo-booster' ),
            'manage_options',
            'smart-seo-import',
            [ 'Smart_SEO_Importer', 'render_page' ]
        );

        add_submenu_page(
            'smart-seo',
            __( 'Setup Wizard', 'smart-seo-booster' ),
            __( 'Setup Wizard', 'smart-seo-booster' ),
            'manage_options',
            'smart-seo-setup',
            [__CLASS__, 'render_setup_wizard']
        );
    }

    public static function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die( esc_html__('You do not have sufficient permissions to access this page.', 'smart-seo-booster') );
        }

        $sections = Smart_SEO_Settings::fields();
        ?>
        <div class="wrap smart-seo-settings">
            <h1><?php echo esc_html__( 'Smart SEO Booster', 'smart-seo-booster' ); ?></h1>

            <p>
                <a href="<?php echo esc_url( admin_url('admin.php?page=smart-seo-setup') ); ?>" class="button">
                    <?php esc_html_e( 'Run Setup Wizard', 'smart-seo-booster' ); ?>
                </a>
            </p>

            <h2 class="nav-tab-wrapper smart-seo-tabs" role="tablist">
                <?php $first = true; foreach ( $sections as $sid => $section ) : ?>
                    <button type="button"
                        class="nav-tab<?php echo $first ? ' nav-tab-active' : ''; ?>"
                        id="tab-<?php echo esc_attr( $sid ); ?>"
                        role="tab"
                        aria-controls="panel-<?php echo esc_attr( $sid ); ?>"
                        aria-selected="<?php echo $first ? 'true' : 'false'; ?>"
                        tabindex="<?php echo $first ? '0' : '-1'; ?>">
                        <?php echo esc_html( $section['title'] ); ?>
                    </button>
                <?php $first = false; endforeach; ?>
            </h2>

            <form method="post" action="options.php">
                <?php settings_fields('smart_seo_settings'); ?>

                <?php $first = true; foreach ( $sections as $sid => $section ) : ?>
                    <div class="smart-seo-panel<?php echo $first ? ' is-active' : ''; ?>"
                        id="panel-<?php echo esc_attr( $sid ); ?>"
                        role="tabpanel"
                        aria-labelledby="tab-<?php echo esc_attr( $sid ); ?>"
                        <?php echo $first ? '' : 'hidden'; ?>>
                        <div class="card smart-seo-card">
                            <h2><?php echo esc_html( $section['title'] ); ?></h2>
                            <?php if ( ! empty( $section['desc'] ) ) : ?>
                                <p class="description"><?php echo esc_html( $section['desc'] ); ?></p>
                            <?php endif; ?>
                            <table class="form-table" role="presentation">
                                <?php do_settings_fields('smart_seo', $sid); ?>
                            </table>
                        </div>
                    </div>
                <?php $first = false; endforeach; ?>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public static function render_audit_report() {
        if (!current_user_can('manage_options')) {
            wp_die( esc_html__('You do not have sufficient permissions to access this page.', 'smart-seo-booster') );
        }
        include plugin_dir_path(__FILE__) . '../templates/audit-report.php';
    }

    public static function render_setup_wizard() {
        if (!current_user_can('manage_options')) {
            wp_die( esc_html__('You do not have sufficient permissions to access this page.', 'smart-seo-booster') );
        }
        Smart_SEO_Setup_Wizard::render();
    }

    public static function enqueue_assets($hook) {
        $ver = defined('SMART_SEO_BOOSTER_VERSION') ? SMART_SEO_BOOSTER_VERSION : false;

        if (strpos($hook, 'smart-seo') !== false) {
            wp_enqueue_style(
                'smart-seo-admin',
                plugin_dir_url(__FILE__) . '../css/admin.css',
                [],
                $ver
            );
        }

        // Settings/wizard/tools styling — all plugin admin pages.
        if (strpos($hook, 'smart-seo') !== false) {
            wp_enqueue_style(
                'smart-seo-settings',
                plugin_dir_url(__FILE__) . '../css/settings.css',
                ['smart-seo-admin', 'dashicons'],
                $ver
            );
        }

        // Audit dashboard design system.
        if ($hook === 'smart-seo_page_smart-seo-audit') {
            wp_enqueue_style(
                'smart-seo-dashboard',
                plugin_dir_url(__FILE__) . '../css/dashboard.css',
                ['dashicons'],
                $ver
            );
        }

        // Accessible tab JS, only on the tabbed settings screen.
        if ($hook === 'toplevel_page_smart-seo') {
            wp_enqueue_script(
                'smart-seo-settings',
                plugin_dir_url(__FILE__) . '../js/settings.js',
                [],
                $ver,
                true
            );
        }
    }
}

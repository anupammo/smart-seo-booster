<?php
defined('ABSPATH') || exit;

class anupamwp_ssb_Loader {
    public static function init() {
        require_once plugin_dir_path(__FILE__) . 'class-settings.php';
        require_once plugin_dir_path(__FILE__) . 'class-admin-ui.php';
        require_once plugin_dir_path(__FILE__) . 'class-seo-core.php';
        require_once plugin_dir_path(__FILE__) . 'class-schema-generator.php';
        require_once plugin_dir_path(__FILE__) . 'class-content-auditor.php';
        require_once plugin_dir_path(__FILE__) . 'class-link-analyzer.php';
        require_once plugin_dir_path(__FILE__) . 'class-seo-score-display.php';
        require_once plugin_dir_path(__FILE__) . 'class-meta-fields.php';

        anupamwp_ssb_Settings::init();
        anupamwp_ssb_Admin_UI::init();
        anupamwp_ssb_Core::init();
        anupamwp_ssb_Schema_Generator::init();
        anupamwp_ssb_Content_Auditor::init();
        anupamwp_ssb_Link_Analyzer::init();
        anupamwp_ssb_Score_Display::init();
        anupamwp_ssb_Meta_Fields::init();
    }
}

<?php
defined('ABSPATH') || exit;

class Smart_SEO_Loader {
    public static function init() {
        require_once plugin_dir_path(__FILE__) . 'class-settings.php';
        require_once plugin_dir_path(__FILE__) . 'class-admin-ui.php';
        require_once plugin_dir_path(__FILE__) . 'class-seo-core.php';
        require_once plugin_dir_path(__FILE__) . 'class-schema-generator.php';
        require_once plugin_dir_path(__FILE__) . 'class-content-auditor.php';
        require_once plugin_dir_path(__FILE__) . 'class-link-analyzer.php';
        require_once plugin_dir_path(__FILE__) . 'class-seo-score-display.php';
        require_once plugin_dir_path(__FILE__) . 'class-meta-fields.php';

        Smart_SEO_Settings::init();
        Smart_SEO_Admin_UI::init();
        Smart_SEO_Core::init();
        Smart_SEO_Schema_Generator::init();
        Smart_SEO_Content_Auditor::init();
        Smart_SEO_Link_Analyzer::init();
        Smart_SEO_Score_Display::init();
        Smart_SEO_Meta_Fields::init();
    }
}

<?php
defined('ABSPATH') || exit;

class Smart_SEO_Loader {
    public static function init() {
        require_once plugin_dir_path(__FILE__) . 'class-meta-templates.php';
        require_once plugin_dir_path(__FILE__) . 'class-brand-icons.php';
        require_once plugin_dir_path(__FILE__) . 'class-settings.php';
        require_once plugin_dir_path(__FILE__) . 'class-admin-ui.php';
        require_once plugin_dir_path(__FILE__) . 'class-seo-core.php';
        require_once plugin_dir_path(__FILE__) . 'class-schema-generator.php';
        require_once plugin_dir_path(__FILE__) . 'class-content-auditor.php';
        require_once plugin_dir_path(__FILE__) . 'class-link-analyzer.php';
        require_once plugin_dir_path(__FILE__) . 'class-meta-fields.php';
        require_once plugin_dir_path(__FILE__) . 'class-seo-score-display.php';
        require_once plugin_dir_path(__FILE__) . 'class-audit.php';
        require_once plugin_dir_path(__FILE__) . 'class-sitemap.php';
        require_once plugin_dir_path(__FILE__) . 'class-breadcrumbs.php';
        require_once plugin_dir_path(__FILE__) . 'class-setup-wizard.php';
        require_once plugin_dir_path(__FILE__) . 'class-tutorial.php';
        require_once plugin_dir_path(__FILE__) . 'class-redirects.php';
        require_once plugin_dir_path(__FILE__) . 'class-bulk-editor.php';
        require_once plugin_dir_path(__FILE__) . 'class-importer.php';
        require_once plugin_dir_path(__FILE__) . 'class-woocommerce.php';
        require_once plugin_dir_path(__FILE__) . 'class-local-seo.php';
        require_once plugin_dir_path(__FILE__) . 'class-analytics.php';
        require_once plugin_dir_path(__FILE__) . 'class-geo.php';
        require_once plugin_dir_path(__FILE__) . 'class-image-seo.php';
        require_once plugin_dir_path(__FILE__) . 'class-toc.php';
        require_once plugin_dir_path(__FILE__) . 'class-blocks.php';
        require_once plugin_dir_path(__FILE__) . 'class-credits.php';
        require_once plugin_dir_path(__FILE__) . 'class-pagespeed.php';

        Smart_SEO_Settings::init();
        Smart_SEO_Admin_UI::init();
        Smart_SEO_Core::init();
        Smart_SEO_Schema_Generator::init();
        Smart_SEO_Content_Auditor::init();
        Smart_SEO_Link_Analyzer::init();
        Smart_SEO_Meta_Fields::init();
        Smart_SEO_Score_Display::init();
        Smart_SEO_Sitemap::init();
        Smart_SEO_Breadcrumbs::init();
        Smart_SEO_Setup_Wizard::init();
        Smart_SEO_Tutorial::init();
        Smart_SEO_Redirects::init();
        Smart_SEO_Bulk_Editor::init();
        Smart_SEO_Importer::init();
        Smart_SEO_WooCommerce::init();
        Smart_SEO_Local_SEO::init();
        Smart_SEO_Analytics::init();
        Smart_SEO_GEO::init();
        Smart_SEO_Image_SEO::init();
        Smart_SEO_TOC::init();
        Smart_SEO_Blocks::init();
        Smart_SEO_Credits::init();
        Smart_SEO_PageSpeed::init();
    }
}

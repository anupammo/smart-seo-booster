<?php
/**
 * Page Speed report screen — form + JS-rendered results (js/pagespeed.js).
 * Included from Smart_SEO_Admin_UI::render_pagespeed().
 */
defined('ABSPATH') || exit;
?>
<div class="wrap smart-seo-pagespeed">
    <div class="ssb-app ssb-adapt">

        <div class="ssb-head">
            <h1><?php echo Smart_SEO_Brand_Icons::icon( 'pagespeed', 24 ); ?> <?php esc_html_e( 'Page Speed', 'smart-seo-booster' ); ?></h1>
        </div>

        <div class="ssb-card">
            <h2><?php esc_html_e( 'Run a Lighthouse check', 'smart-seo-booster' ); ?></h2>
            <p class="description">
                <?php esc_html_e( 'Powered by Google PageSpeed Insights. Your site must be publicly reachable on the internet — local/staging installs cannot be tested this way.', 'smart-seo-booster' ); ?>
            </p>
            <div class="ssb-pagespeed-form">
                <input type="url" id="ssb-ps-url" class="regular-text" value="<?php echo esc_url( home_url( '/' ) ); ?>" />
                <select id="ssb-ps-strategy">
                    <option value="mobile"><?php esc_html_e( 'Mobile', 'smart-seo-booster' ); ?></option>
                    <option value="desktop"><?php esc_html_e( 'Desktop', 'smart-seo-booster' ); ?></option>
                </select>
                <button type="button" class="button button-primary" id="ssb-ps-run">
                    <?php esc_html_e( 'Run Test', 'smart-seo-booster' ); ?>
                </button>
            </div>
            <div id="ssb-ps-status" class="ssb-pagespeed-status" hidden></div>
        </div>

        <div id="ssb-ps-results" hidden>
            <div class="ssb-grid" id="ssb-ps-scores"></div>

            <div class="ssb-card">
                <h2><?php esc_html_e( 'Core Web Vitals', 'smart-seo-booster' ); ?></h2>
                <p class="description" id="ssb-ps-vitals-source"></p>
                <table class="ssb-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Metric', 'smart-seo-booster' ); ?></th>
                            <th><?php esc_html_e( 'Value', 'smart-seo-booster' ); ?></th>
                            <th><?php esc_html_e( 'Rating', 'smart-seo-booster' ); ?></th>
                        </tr>
                    </thead>
                    <tbody id="ssb-ps-vitals"></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

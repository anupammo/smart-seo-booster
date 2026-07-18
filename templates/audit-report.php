<?php
defined('ABSPATH') || exit;

global $post;
if (!$post) return;

$smart_seo_content = $post->post_content;
$smart_seo_word_count = str_word_count( wp_strip_all_tags( $smart_seo_content ) );
$smart_seo_heading_count = substr_count($smart_seo_content, '<h');
$smart_seo_image_count = substr_count($smart_seo_content, '<img');
$smart_seo_alt_count = substr_count($smart_seo_content, 'alt=');
$smart_seo_link_matches = [];
$smart_seo_link_count = preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\']/i', $smart_seo_content, $smart_seo_link_matches);

$smart_seo_schema_enabled = get_option('smart_seo_options')['enable_schema'] ?? false;
?>

<div class="wrap">
    <h1><?php echo esc_html__( 'Smart SEO Audit Report', 'smart-seo-booster' ); ?></h1>
    <table class="widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Metric', 'smart-seo-booster' ); ?></th>
                <th><?php esc_html_e( 'Value', 'smart-seo-booster' ); ?></th>
                <th><?php esc_html_e( 'Status', 'smart-seo-booster' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php esc_html_e( 'Word Count', 'smart-seo-booster' ); ?></td>
                <td><?php echo absint( $smart_seo_word_count ); ?></td>
                <td><?php echo $smart_seo_word_count >= 500 ? esc_html__( '✅ Good', 'smart-seo-booster' ) : esc_html__( '⚠️ Consider adding more content', 'smart-seo-booster' ); ?></td>
            </tr>
            <tr>
                <td><?php esc_html_e( 'Headings', 'smart-seo-booster' ); ?></td>
                <td><?php echo absint( $smart_seo_heading_count ); ?></td>
                <td><?php echo $smart_seo_heading_count >= 3 ? esc_html__( '✅ Structured', 'smart-seo-booster' ) : esc_html__( '⚠️ Add more headings', 'smart-seo-booster' ); ?></td>
            </tr>
            <tr>
                <td><?php esc_html_e( 'Images', 'smart-seo-booster' ); ?></td>
                <td><?php echo absint( $smart_seo_image_count ); ?></td>
                <td><?php echo $smart_seo_image_count === $smart_seo_alt_count ? esc_html__( '✅ All images have alt text', 'smart-seo-booster' ) : esc_html__( '⚠️ Missing alt attributes', 'smart-seo-booster' ); ?></td>
            </tr>
            <tr>
                <td><?php esc_html_e( 'Internal Links', 'smart-seo-booster' ); ?></td>
                <td><?php echo absint( count($smart_seo_link_matches[1] ?? []) ); ?></td>
                <td><?php echo ( count($smart_seo_link_matches[1] ?? []) ) >= 5 ? esc_html__( '✅ Good linking', 'smart-seo-booster' ) : esc_html__( '⚠️ Add more internal links', 'smart-seo-booster' ); ?></td>
            </tr>
            <tr>
                <td><?php esc_html_e( 'Schema Markup', 'smart-seo-booster' ); ?></td>
                <td><?php echo $smart_seo_schema_enabled ? esc_html__( 'Enabled', 'smart-seo-booster' ) : esc_html__( 'Disabled', 'smart-seo-booster' ); ?></td>
                <td><?php echo $smart_seo_schema_enabled ? esc_html__( '✅ Active', 'smart-seo-booster' ) : esc_html__( '⚠️ Enable in settings', 'smart-seo-booster' ); ?></td>
            </tr>
        </tbody>
    </table>
</div>


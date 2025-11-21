<?php
defined('ABSPATH') || exit;

global $post;
if (!$post) return;

$content = $post->post_content;
$word_count = str_word_count( wp_strip_all_tags( $content ) );
$heading_count = substr_count($content, '<h');
$image_count = substr_count($content, '<img');
$alt_count = substr_count($content, 'alt=');
$link_count = preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\']/i', $content, $matches);

$schema_enabled = get_option('smart_seo_options')['enable_schema'] ?? false;
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
                <td><?php echo esc_html( $word_count ); ?></td>
                <td><?php echo $word_count >= 500 ? esc_html__( '✅ Good', 'smart-seo-booster' ) : esc_html__( '⚠️ Consider adding more content', 'smart-seo-booster' ); ?></td>
            </tr>
            <tr>
                <td><?php esc_html_e( 'Headings', 'smart-seo-booster' ); ?></td>
                <td><?php echo esc_html( $heading_count ); ?></td>
                <td><?php echo $heading_count >= 3 ? esc_html__( '✅ Structured', 'smart-seo-booster' ) : esc_html__( '⚠️ Add more headings', 'smart-seo-booster' ); ?></td>
            </tr>
            <tr>
                <td><?php esc_html_e( 'Images', 'smart-seo-booster' ); ?></td>
                <td><?php echo esc_html( $image_count ); ?></td>
                <td><?php echo $image_count === $alt_count ? esc_html__( '✅ All images have alt text', 'smart-seo-booster' ) : esc_html__( '⚠️ Missing alt attributes', 'smart-seo-booster' ); ?></td>
            </tr>
            <tr>
                <td><?php esc_html_e( 'Internal Links', 'smart-seo-booster' ); ?></td>
                <td><?php echo esc_html( count($matches[1]) ); ?></td>
                <td><?php echo count($matches[1]) >= 5 ? esc_html__( '✅ Good linking', 'smart-seo-booster' ) : esc_html__( '⚠️ Add more internal links', 'smart-seo-booster' ); ?></td>
            </tr>
            <tr>
                <td><?php esc_html_e( 'Schema Markup', 'smart-seo-booster' ); ?></td>
                <td><?php echo $schema_enabled ? esc_html__( 'Enabled', 'smart-seo-booster' ) : esc_html__( 'Disabled', 'smart-seo-booster' ); ?></td>
                <td><?php echo $schema_enabled ? esc_html__( '✅ Active', 'smart-seo-booster' ) : esc_html__( '⚠️ Enable in settings', 'smart-seo-booster' ); ?></td>
            </tr>
        </tbody>
    </table>
</div>

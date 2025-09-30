<?php
defined('ABSPATH') || exit;

global $post;
if (!$post) return;

$content = $post->post_content;
$word_count = str_word_count(strip_tags($content));
$heading_count = substr_count($content, '<h');
$image_count = substr_count($content, '<img');
$alt_count = substr_count($content, 'alt=');
$link_count = preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\']/i', $content, $matches);

$schema_enabled = get_option('smart_seo_options')['enable_schema'] ?? false;
?>

<div class="wrap">
    <h1>Smart SEO Audit Report</h1>
    <table class="widefat fixed striped">
        <thead>
            <tr>
                <th>Metric</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Word Count</td>
                <td><?php echo $word_count; ?></td>
                <td><?php echo $word_count >= 500 ? '✅ Good' : '⚠️ Consider adding more content'; ?></td>
            </tr>
            <tr>
                <td>Headings</td>
                <td><?php echo $heading_count; ?></td>
                <td><?php echo $heading_count >= 3 ? '✅ Structured' : '⚠️ Add more headings'; ?></td>
            </tr>
            <tr>
                <td>Images</td>
                <td><?php echo $image_count; ?></td>
                <td><?php echo $image_count === $alt_count ? '✅ All images have alt text' : '⚠️ Missing alt attributes'; ?></td>
            </tr>
            <tr>
                <td>Internal Links</td>
                <td><?php echo count($matches[1]); ?></td>
                <td><?php echo count($matches[1]) >= 5 ? '✅ Good linking' : '⚠️ Add more internal links'; ?></td>
            </tr>
            <tr>
                <td>Schema Markup</td>
                <td><?php echo $schema_enabled ? 'Enabled' : 'Disabled'; ?></td>
                <td><?php echo $schema_enabled ? '✅ Active' : '⚠️ Enable in settings'; ?></td>
            </tr>
        </tbody>
    </table>
</div>

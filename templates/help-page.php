<?php
defined('ABSPATH') || exit;
?>

<div class="wrap smart-seo-help">
    <h1>🚀 Smart SEO Booster - Help & Documentation</h1>
    
    <div class="help-navigation">
        <nav class="nav-tab-wrapper">
            <a href="#getting-started" class="nav-tab nav-tab-active" data-tab="getting-started">Getting Started</a>
            <a href="#settings-guide" class="nav-tab" data-tab="settings-guide">Settings Guide</a>
            <a href="#schema-markup" class="nav-tab" data-tab="schema-markup">Schema Markup</a>
            <a href="#content-audit" class="nav-tab" data-tab="content-audit">Content Audit</a>
            <a href="#troubleshooting" class="nav-tab" data-tab="troubleshooting">Troubleshooting</a>
            <a href="#faq" class="nav-tab" data-tab="faq">FAQ</a>
        </nav>
    </div>

    <!-- Getting Started Tab -->
    <div id="getting-started" class="help-tab-content active">
        <div class="help-hero">
            <h2>🎯 Welcome to Smart SEO Booster!</h2>
            <p>Your comprehensive SEO solution for WordPress. Get started in just 3 simple steps!</p>
        </div>

        <div class="help-steps">
            <div class="step-card">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>📋 Configure Basic Settings</h3>
                    <p>Navigate to <strong>Smart SEO → Settings</strong> and enable the features you want:</p>
                    <ul>
                        <li>✅ Enable Schema Markup for better search visibility</li>
                        <li>✅ Enable Meta Tags for title and description optimization</li>
                        <li>✅ Enable Content Audit for real-time SEO feedback</li>
                    </ul>
                    <a href="<?php echo esc_url( admin_url('admin.php?page=smart-seo') ); ?>" class="button button-primary">Go to Settings</a>
                </div>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>📊 Run Your First Audit</h3>
                    <p>Check your site's SEO performance with our comprehensive audit:</p>
                    <ul>
                        <li>📈 View overall site statistics</li>
                        <li>🎯 Get specific improvement recommendations</li>
                        <li>📝 Identify content gaps and opportunities</li>
                    </ul>
                    <a href="<?php echo esc_url( admin_url('admin.php?page=smart-seo-audit') ); ?>" class="button button-primary">View Audit Report</a>
                </div>
            </div>

            <div class="step-card">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>✍️ Optimize Your Content</h3>
                    <p>Use our real-time feedback while editing posts and pages:</p>
                    <ul>
                        <li>📝 SEO score appears in the editor</li>
                        <li>💡 Get instant recommendations</li>
                        <li>🔗 Monitor internal links automatically</li>
                    </ul>
                    <a href="<?php echo esc_url( admin_url('edit.php') ); ?>" class="button button-primary">Edit Posts</a>
                </div>
            </div>
        </div>

        <div class="quick-tips">
            <h3>💡 Quick Tips for Success</h3>
            <div class="tips-grid">
                <div class="tip-card">
                    <h4>🎯 Content Length</h4>
                    <p>Aim for at least 300 words per post. Longer content (500+ words) typically ranks better.</p>
                </div>
                <div class="tip-card">
                    <h4>📋 Use Headings</h4>
                    <p>Structure your content with H2, H3, H4 tags. This helps both readers and search engines.</p>
                </div>
                <div class="tip-card">
                    <h4>🖼️ Image Alt Text</h4>
                    <p>Always add descriptive alt text to images for accessibility and SEO benefits.</p>
                </div>
                <div class="tip-card">
                    <h4>🔗 Internal Links</h4>
                    <p>Link to other relevant pages on your site to improve navigation and SEO.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Guide Tab -->
    <div id="settings-guide" class="help-tab-content">
        <h2>⚙️ Complete Settings Guide</h2>
        
        <div class="settings-section">
            <h3>📱 Schema Markup Settings</h3>
            <div class="setting-item">
                <h4>Enable Schema Markup</h4>
                <p>Automatically adds structured data (JSON-LD) to your pages, helping search engines understand your content better. This can result in rich snippets in search results.</p>
                <div class="code-example">
                    <strong>Result:</strong> Star ratings, breadcrumbs, and enhanced search listings
                </div>
            </div>
            
            <div class="setting-item">
                <h4>Default Schema Type</h4>
                <p>Choose the primary schema type for your website:</p>
                <ul>
                    <li><strong>Organization:</strong> Best for businesses and brands</li>
                    <li><strong>Local Business:</strong> Perfect for location-based businesses</li>
                    <li><strong>Article:</strong> Ideal for blogs and news sites</li>
                </ul>
            </div>
        </div>

        <div class="settings-section">
            <h3>🏷️ Meta Tags Settings</h3>
            <div class="setting-item">
                <h4>Enable Meta Tags</h4>
                <p>Automatically generates optimized title and description meta tags for better search visibility.</p>
            </div>
            
            <div class="setting-item">
                <h4>Enable Open Graph Tags</h4>
                <p>Adds social media sharing tags for Facebook, LinkedIn, and other platforms. This ensures your content looks great when shared.</p>
            </div>
            
            <div class="setting-item">
                <h4>Default Meta Description</h4>
                <p>Fallback description used when posts/pages don't have excerpts. Keep it under 160 characters for best results.</p>
            </div>
        </div>

        <div class="settings-section">
            <h3>🔍 Content Audit Settings</h3>
            <div class="setting-item">
                <h4>Enable Content Audit</h4>
                <p>Shows real-time SEO feedback while editing posts and pages in the WordPress editor.</p>
            </div>
            
            <div class="setting-item">
                <h4>Minimum Word Count</h4>
                <p>Set the target word count for your content. Default is 300 words, but you can adjust based on your content strategy.</p>
            </div>
            
            <div class="setting-item">
                <h4>Enable Link Analysis</h4>
                <p>Automatically counts and analyzes internal links in your content to improve site navigation.</p>
            </div>
        </div>
    </div>

    <!-- Schema Markup Tab -->
    <div id="schema-markup" class="help-tab-content">
        <h2>🏗️ Understanding Schema Markup</h2>
        
        <div class="schema-intro">
            <p>Schema markup is structured data that helps search engines understand your content better. Smart SEO Booster automatically adds the right schema based on your page type.</p>
        </div>

        <div class="schema-types">
            <div class="schema-card">
                <h3>📰 Article Schema</h3>
                <p><strong>Applied to:</strong> Blog posts and articles</p>
                <p><strong>Benefits:</strong> Rich snippets, author information, publish dates</p>
                <div class="schema-example">
                    <strong>What it includes:</strong>
                    <ul>
                        <li>Article headline and content</li>
                        <li>Author information</li>
                        <li>Publication and modification dates</li>
                        <li>Publisher details</li>
                    </ul>
                </div>
            </div>

            <div class="schema-card">
                <h3>🏢 Organization Schema</h3>
                <p><strong>Applied to:</strong> Homepage and about pages</p>
                <p><strong>Benefits:</strong> Brand recognition, social media links</p>
                <div class="schema-example">
                    <strong>What it includes:</strong>
                    <ul>
                        <li>Company name and logo</li>
                        <li>Website URL</li>
                        <li>Social media profiles</li>
                        <li>Contact information</li>
                    </ul>
                </div>
            </div>

            <div class="schema-card">
                <h3>📍 Local Business Schema</h3>
                <p><strong>Applied to:</strong> Contact and service pages</p>
                <p><strong>Benefits:</strong> Local search visibility, Google My Business integration</p>
                <div class="schema-example">
                    <strong>What it includes:</strong>
                    <ul>
                        <li>Business address and location</li>
                        <li>Phone number</li>
                        <li>Business hours</li>
                        <li>Service areas</li>
                    </ul>
                </div>
            </div>

            <div class="schema-card">
                <h3>❓ FAQ Schema</h3>
                <p><strong>Applied to:</strong> FAQ pages and Q&A content</p>
                <p><strong>Benefits:</strong> Featured snippets, expandable search results</p>
                <div class="schema-example">
                    <strong>What it includes:</strong>
                    <ul>
                        <li>Question and answer pairs</li>
                        <li>Structured FAQ content</li>
                        <li>Enhanced search visibility</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="schema-testing">
            <h3>🧪 Testing Your Schema</h3>
            <p>Use these tools to verify your schema markup is working correctly:</p>
            <ul>
                <li><a href="https://search.google.com/test/rich-results" target="_blank">Google Rich Results Test</a></li>
                <li><a href="https://validator.schema.org/" target="_blank">Schema.org Validator</a></li>
                <li><a href="https://developers.facebook.com/tools/debug/" target="_blank">Facebook Sharing Debugger</a></li>
            </ul>
        </div>
    </div>

    <!-- Content Audit Tab -->
    <div id="content-audit" class="help-tab-content">
        <h2>📊 Content Audit Guide</h2>
        
        <div class="audit-overview">
            <p>Our content audit system provides real-time feedback to help you create SEO-optimized content. Here's how to interpret and use the feedback:</p>
        </div>

        <div class="audit-metrics">
            <div class="metric-card">
                <h3>📝 Word Count Analysis</h3>
                <div class="metric-details">
                    <p><strong>What it measures:</strong> Total words in your content (excluding HTML tags)</p>
                    <p><strong>Scoring:</strong></p>
                    <ul>
                        <li>🟢 <strong>300+ words:</strong> Good foundation for SEO</li>
                        <li>🟡 <strong>200-299 words:</strong> Consider adding more content</li>
                        <li>🔴 <strong>Under 200 words:</strong> Too short for effective SEO</li>
                    </ul>
                    <p><strong>Pro tip:</strong> Longer content (500+ words) typically performs better in search results.</p>
                </div>
            </div>

            <div class="metric-card">
                <h3>📋 Heading Structure</h3>
                <div class="metric-details">
                    <p><strong>What it measures:</strong> Use of H1, H2, H3, H4, H5, H6 tags</p>
                    <p><strong>Best practices:</strong></p>
                    <ul>
                        <li>Use one H1 tag per page (usually the title)</li>
                        <li>Include at least 2-3 H2 tags for main sections</li>
                        <li>Use H3-H6 for subsections</li>
                        <li>Maintain hierarchical structure</li>
                    </ul>
                    <p><strong>Why it matters:</strong> Headings help search engines understand content structure and improve readability.</p>
                </div>
            </div>

            <div class="metric-card">
                <h3>🖼️ Image Optimization</h3>
                <div class="metric-details">
                    <p><strong>What it measures:</strong> Images with proper alt text attributes</p>
                    <p><strong>Scoring:</strong></p>
                    <ul>
                        <li>🟢 <strong>100% coverage:</strong> All images have alt text</li>
                        <li>🟡 <strong>70-99% coverage:</strong> Most images optimized</li>
                        <li>🔴 <strong>Under 70%:</strong> Many images missing alt text</li>
                    </ul>
                    <p><strong>How to add alt text:</strong> In the WordPress media library, add descriptive text in the "Alt Text" field.</p>
                </div>
            </div>

            <div class="metric-card">
                <h3>🔗 Internal Link Analysis</h3>
                <div class="metric-details">
                    <p><strong>What it measures:</strong> Links to other pages on your website</p>
                    <p><strong>Recommendations:</strong></p>
                    <ul>
                        <li>Include 3-5 internal links per 500 words</li>
                        <li>Link to relevant, related content</li>
                        <li>Use descriptive anchor text</li>
                        <li>Avoid over-optimization</li>
                    </ul>
                    <p><strong>Benefits:</strong> Improves site navigation, distributes page authority, and keeps visitors engaged.</p>
                </div>
            </div>
        </div>

        <div class="seo-score-guide">
            <h3>🎯 Understanding Your SEO Score</h3>
            <div class="score-breakdown">
                <div class="score-range">
                    <span class="score-indicator excellent">90-100</span>
                    <div>
                        <strong>Excellent:</strong> Your content is well-optimized for SEO. Great job! 🎉
                    </div>
                </div>
                <div class="score-range">
                    <span class="score-indicator good">75-89</span>
                    <div>
                        <strong>Good:</strong> Solid SEO foundation with room for minor improvements. ✅
                    </div>
                </div>
                <div class="score-range">
                    <span class="score-indicator okay">50-74</span>
                    <div>
                        <strong>Needs Work:</strong> Several areas need attention to improve SEO performance. ⚠️
                    </div>
                </div>
                <div class="score-range">
                    <span class="score-indicator poor">0-49</span>
                    <div>
                        <strong>Poor:</strong> Significant SEO issues that should be addressed immediately. ❌
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Troubleshooting Tab -->
    <div id="troubleshooting" class="help-tab-content">
        <h2>🔧 Troubleshooting Guide</h2>
        
        <div class="troubleshooting-section">
            <h3>Common Issues & Solutions</h3>
            
            <div class="trouble-item">
                <h4>❌ Schema markup not appearing in search results</h4>
                <div class="solution">
                    <p><strong>Possible causes:</strong></p>
                    <ul>
                        <li>Schema markup is disabled in settings</li>
                        <li>Conflicting SEO plugins</li>
                        <li>Theme modifications blocking output</li>
                    </ul>
                    <p><strong>Solutions:</strong></p>
                    <ol>
                        <li>Check that "Enable Schema Markup" is enabled in settings</li>
                        <li>Test with <a href="https://search.google.com/test/rich-results" target="_blank">Google Rich Results Test</a></li>
                        <li>Temporarily deactivate other SEO plugins to check for conflicts</li>
                        <li>Switch to a default theme temporarily to test</li>
                    </ol>
                </div>
            </div>

            <div class="trouble-item">
                <h4>📊 SEO audit notices not showing in editor</h4>
                <div class="solution">
                    <p><strong>Check these settings:</strong></p>
                    <ol>
                        <li>Ensure "Enable Content Audit" is checked in settings</li>
                        <li>Verify you're using the Block Editor (Gutenberg), not Classic Editor</li>
                        <li>Check that you have sufficient user permissions</li>
                        <li>Clear any caching plugins and refresh the page</li>
                    </ol>
                </div>
            </div>

            <div class="trouble-item">
                <h4>🔗 Internal links not being detected</h4>
                <div class="solution">
                    <p><strong>Requirements for link detection:</strong></p>
                    <ul>
                        <li>Links must point to your domain (<?php echo esc_url( home_url() ); ?>)</li>
                        <li>Links must be properly formatted HTML anchor tags</li>
                        <li>"Enable Link Analysis" must be checked in settings</li>
                    </ul>
                    <p><strong>Example of detectable link:</strong></p>
                    <code>&lt;a href="<?php echo esc_url( home_url('/about') ); ?>"&gt;About Us&lt;/a&gt;</code>
                </div>
            </div>

            <div class="trouble-item">
                <h4>🎨 Admin styles not loading properly</h4>
                <div class="solution">
                    <p><strong>Try these steps:</strong></p>
                    <ol>
                        <li>Clear browser cache and hard refresh (Ctrl+F5)</li>
                        <li>Check browser console for JavaScript errors</li>
                        <li>Ensure file permissions are correct (644 for files, 755 for directories)</li>
                        <li>Verify all plugin files are uploaded correctly</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="debug-info">
            <h3>🐛 Debug Information</h3>
            <p>If you need support, please include this information:</p>
            <div class="debug-box">
                <strong>WordPress Version:</strong> <?php echo esc_html( get_bloginfo('version') ); ?><br>
                <strong>PHP Version:</strong> <?php echo esc_html( PHP_VERSION ); ?><br>
                <strong>Plugin Version:</strong> 1.0.0<br>
                <strong>Active Theme:</strong> <?php echo esc_html( wp_get_theme()->get('Name') ); ?><br>
                <strong>Site URL:</strong> <?php echo esc_url( home_url() ); ?><br>
                <strong>Admin URL:</strong> <?php echo esc_url( admin_url() ); ?>
            </div>
        </div>
    </div>

    <!-- FAQ Tab -->
    <div id="faq" class="help-tab-content">
        <h2>❓ Frequently Asked Questions</h2>
        
        <div class="faq-section">
            <div class="faq-item">
                <h3>🚀 Will this plugin slow down my website?</h3>
                <p>No! Smart SEO Booster is designed for performance. It uses minimal resources and only loads admin scripts on admin pages. The schema markup and meta tags add negligible overhead to your pages.</p>
            </div>

            <div class="faq-item">
                <h3>🔧 Can I use this with other SEO plugins?</h3>
                <p>While it's possible, we recommend using Smart SEO Booster as your primary SEO solution to avoid conflicts. If you must use multiple plugins, disable overlapping features (like schema markup or meta tags) in one of them.</p>
            </div>

            <div class="faq-item">
                <h3>📱 Does it work with mobile and responsive themes?</h3>
                <p>Absolutely! The plugin works with all properly coded WordPress themes, including mobile-responsive and block themes. The admin interface is also fully responsive.</p>
            </div>

            <div class="faq-item">
                <h3>🌍 Is the plugin translation-ready?</h3>
                <p>Yes! Smart SEO Booster includes translation files and is ready for internationalization. You can translate it into any language using WordPress translation tools.</p>
            </div>

            <div class="faq-item">
                <h3>📊 How often should I check the audit report?</h3>
                <p>We recommend checking the audit report monthly or after making significant content changes. The real-time feedback in the editor is more important for day-to-day content creation.</p>
            </div>

            <div class="faq-item">
                <h3>🎯 What's the difference between this and Yoast SEO?</h3>
                <p>Smart SEO Booster focuses on automation and simplicity. While Yoast offers more manual controls, our plugin automatically handles schema markup, provides cleaner interfaces, and offers real-time scoring without cluttering your editor.</p>
            </div>

            <div class="faq-item">
                <h3>🔄 Can I export/import settings?</h3>
                <p>Currently, settings are stored in your WordPress database. For migrations, you can copy the 'smart_seo_options' option using database tools or plugins like WP Migrate DB.</p>
            </div>

            <div class="faq-item">
                <h3>📈 How long before I see SEO improvements?</h3>
                <p>SEO is a long-term strategy. You might see technical improvements (like schema markup) reflected in search results within 2-4 weeks, but significant ranking improvements typically take 3-6 months of consistent optimization.</p>
            </div>
        </div>

        <div class="support-section">
            <h3>🤝 Need More Help?</h3>
            <p>If you can't find the answer you're looking for, here are additional resources:</p>
            <div class="support-links">
                <a href="https://github.com/anupammo/smart-seo-booster" target="_blank" class="support-link">
                    📚 GitHub Documentation
                </a>
                <a href="https://github.com/anupammo/smart-seo-booster/issues" target="_blank" class="support-link">
                    🐛 Report a Bug
                </a>
                <a href="https://wordpress.org/support/plugin/smart-seo-booster/" target="_blank" class="support-link">
                    💬 WordPress Support Forum
                </a>
            </div>
        </div>

        <!-- Developer Services Section -->
        <div class="developer-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 12px; margin: 30px 0; text-align: center;">
            <h3 style="color: white; margin-bottom: 15px;">🚀 Need Professional WordPress & SEO Services?</h3>
            <div style="display: grid; grid-template-columns: 80px 1fr; gap: 20px; align-items: center; max-width: 600px; margin: 0 auto;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold;">
                    AM
                </div>
                <div style="text-align: left;">
                    <h4 style="color: white; margin: 0 0 5px 0;">Anupam Mondal</h4>
                    <p style="margin: 0 0 10px 0; opacity: 0.9;">WordPress Developer & SEO Expert</p>
                    <p style="margin: 0 0 15px 0; font-size: 14px; opacity: 0.8;">Get custom WordPress development, SEO optimization, plugin customization, and technical support from the creator of Smart SEO Booster.</p>
                    <div style="display: flex; gap: 10px;">
                        <a href="https://anupammondal.in/?utm_source=smart-seo-booster&utm_medium=plugin&utm_campaign=help-page" 
                           target="_blank" 
                           style="background: rgba(255,255,255,0.2); color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; border: 1px solid rgba(255,255,255,0.3);">
                            🌐 Visit Website
                        </a>
                        <a href="https://anupammondal.in/contact/?utm_source=smart-seo-booster&utm_medium=plugin&utm_campaign=help-contact" 
                           target="_blank" 
                           style="background: white; color: #667eea; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500;">
                            � Get Quote
                        </a>
                    </div>
                </div>
            </div>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.2);">
                <p style="margin: 0; font-size: 13px; opacity: 0.8;">
                    Services: Custom Development • SEO Optimization • Plugin Development • Performance Optimization • Technical Support
                </p>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Tab switching functionality
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all tabs and content
        $('.nav-tab').removeClass('nav-tab-active');
        $('.help-tab-content').removeClass('active');
        
        // Add active class to clicked tab
        $(this).addClass('nav-tab-active');
        
        // Show corresponding content
        var tabId = $(this).data('tab');
        $('#' + tabId).addClass('active');
        
        // Update URL hash without jumping
        history.replaceState(null, null, '#' + tabId);
    });
    
    // Handle direct hash links
    if (window.location.hash) {
        var hash = window.location.hash.substring(1);
        var $tab = $('.nav-tab[data-tab="' + hash + '"]');
        if ($tab.length) {
            $tab.trigger('click');
        }
    }
    
    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(e) {
        if ($(this).hasClass('nav-tab')) return; // Skip tab navigation
        
        var target = this.hash;
        var $target = $(target);
        
        if ($target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $target.offset().top - 50
            }, 500);
        }
    });
});
</script>

<!-- Footer with author credit -->
<div style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-left: 4px solid #2271b1; border-radius: 4px;">
    <p style="margin: 0; color: #666; font-size: 14px;">
        <strong><?php esc_html_e('Smart SEO Booster', 'smart-seo-booster-1'); ?></strong> 
        <?php printf(
            /* translators: %s: plugin version */
            esc_html__('version %s - Developed with ❤️ for better WordPress SEO', 'smart-seo-booster-1'),
            esc_html(SMART_SEO_VERSION)
        ); ?>
    </p>
    <p style="margin: 5px 0 0 0; color: #666; font-size: 12px;">
        <?php esc_html_e('Thank you for using Smart SEO Booster. For support and documentation, visit our website.', 'smart-seo-booster-1'); ?>
    </p>
</div>
</div>
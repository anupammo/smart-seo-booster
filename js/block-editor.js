(function() {
    'use strict';
    
    const { registerPlugin } = wp.plugins;
    const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
    const { PanelBody, PanelRow, Button, Spinner } = wp.components;
    const { select, subscribe } = wp.data;
    const { createElement, useState, useEffect } = wp.element;
    const { __ } = wp.i18n;
    
    const SmartSEOSidebar = () => {
        const [seoData, setSeoData] = useState(null);
        const [loading, setLoading] = useState(false);
        const [lastContent, setLastContent] = useState('');
        
        const getCurrentPost = () => select('core/editor').getCurrentPost();
        const getEditedContent = () => select('core/editor').getEditedPostContent();
        
        const calculateLocalScore = () => {
            const content = getEditedContent();
            const post = getCurrentPost();
            
            if (!content || !post) return null;
            
            // Simple client-side calculations for immediate feedback
            const wordCount = content.replace(/<[^>]*>/g, '').split(/\s+/).filter(word => word.length > 0).length;
            const headingCount = (content.match(/<h[1-6][^>]*>/gi) || []).length;
            const imageCount = (content.match(/<img[^>]*>/gi) || []).length;
            const altCount = (content.match(/alt\s*=\s*["'][^"']*["']/gi) || []).length;
            const linkMatches = content.match(/<a\s[^>]*href\s*=\s*["']([^"']*)["'][^>]*>/gi) || [];
            
            let internalLinks = 0;
            linkMatches.forEach(link => {
                const hrefMatch = link.match(/href\s*=\s*["']([^"']*)["']/i);
                if (hrefMatch && hrefMatch[1]) {
                    const url = hrefMatch[1];
                    if (url.startsWith('/') || url.includes(window.location.hostname)) {
                        internalLinks++;
                    }
                }
            });
            
            let score = 0;
            const minWords = anupamwpSsbData.minWordCount || 300;
            
            // Word count scoring (30 points)
            if (wordCount >= minWords) score += 30;
            else if (wordCount >= minWords * 0.7) score += 20;
            else if (wordCount >= minWords * 0.5) score += 10;
            
            // Headings scoring (25 points)
            if (headingCount >= 3) score += 25;
            else if (headingCount >= 2) score += 15;
            else if (headingCount >= 1) score += 10;
            
            // Image alt text scoring (20 points)
            if (imageCount > 0) {
                const altRatio = altCount / imageCount;
                if (altRatio >= 1.0) score += 20;
                else if (altRatio >= 0.8) score += 15;
                else if (altRatio >= 0.5) score += 10;
            } else {
                score += 10; // No images is acceptable
            }
            
            // Internal links scoring (15 points)
            if (internalLinks >= 3) score += 15;
            else if (internalLinks >= 2) score += 10;
            else if (internalLinks >= 1) score += 5;
            
            // Meta description scoring (10 points)
            const excerpt = post.excerpt;
            if (excerpt && excerpt.length >= 120 && excerpt.length <= 160) {
                score += 10;
            } else if (excerpt && excerpt.length > 0) {
                score += 5;
            }
            
            return {
                score: Math.min(score, 100),
                wordCount,
                headingCount,
                imageCount,
                altCount,
                internalLinks,
                recommendations: generateRecommendations(wordCount, headingCount, imageCount, altCount, internalLinks, excerpt, minWords)
            };
        };
        
        const generateRecommendations = (wordCount, headingCount, imageCount, altCount, internalLinks, excerpt, minWords) => {
            const recommendations = [];
            
            if (wordCount < minWords) {
                recommendations.push(`Add ${minWords - wordCount} more words for better SEO`);
            }
            
            if (headingCount < 2) {
                recommendations.push('Add more headings (H2, H3) to structure your content');
            }
            
            if (imageCount > 0 && altCount < imageCount) {
                recommendations.push(`Add alt text to ${imageCount - altCount} images`);
            }
            
            if (internalLinks < 2) {
                recommendations.push('Add more internal links to related content');
            }
            
            if (!excerpt || excerpt.length === 0) {
                recommendations.push('Add a meta description in the excerpt field');
            } else if (excerpt.length < 120) {
                recommendations.push('Make your meta description longer (120-160 characters)');
            } else if (excerpt.length > 160) {
                recommendations.push('Shorten your meta description (120-160 characters)');
            }
            
            return recommendations;
        };
        
        const getScoreColor = (score) => {
            if (score >= 80) return '#10b981';
            if (score >= 60) return '#f59e0b';
            if (score >= 40) return '#f97316';
            return '#ef4444';
        };
        
        const getScoreStatus = (score) => {
            if (score >= 80) return 'Excellent';
            if (score >= 60) return 'Good';
            if (score >= 40) return 'Needs Work';
            return 'Poor';
        };
        
        const refreshScore = () => {
            const localData = calculateLocalScore();
            setSeoData(localData);
        };
        
        // Update score when content changes
        useEffect(() => {
            const unsubscribe = subscribe(() => {
                const content = getEditedContent();
                if (content !== lastContent) {
                    setLastContent(content);
                    refreshScore();
                }
            });
            
            // Initial calculation
            refreshScore();
            
            return unsubscribe;
        }, [lastContent]);
        
        const ScoreCircle = ({ score }) => {
            const color = getScoreColor(score);
            const circumference = 2 * Math.PI * 40;
            const strokeDasharray = circumference;
            const strokeDashoffset = circumference - (score / 100) * circumference;
            
            return createElement('div', { style: { textAlign: 'center', margin: '20px 0' } }, [
                createElement('svg', { 
                    key: 'svg',
                    width: 100, 
                    height: 100,
                    style: { transform: 'rotate(-90deg)' }
                }, [
                    createElement('circle', {
                        key: 'bg',
                        cx: 50,
                        cy: 50,
                        r: 40,
                        stroke: '#e5e7eb',
                        strokeWidth: 8,
                        fill: 'transparent'
                    }),
                    createElement('circle', {
                        key: 'progress',
                        cx: 50,
                        cy: 50,
                        r: 40,
                        stroke: color,
                        strokeWidth: 8,
                        fill: 'transparent',
                        strokeLinecap: 'round',
                        strokeDasharray: strokeDasharray,
                        strokeDashoffset: strokeDashoffset,
                        style: { transition: 'stroke-dashoffset 0.5s ease-in-out' }
                    })
                ]),
                createElement('div', { 
                    key: 'score',
                    style: { 
                        position: 'relative', 
                        top: '-70px', 
                        fontSize: '24px', 
                        fontWeight: 'bold',
                        color: color
                    }
                }, score),
                createElement('div', {
                    key: 'status',
                    style: {
                        position: 'relative',
                        top: '-70px',
                        fontSize: '12px',
                        color: color,
                        fontWeight: '500'
                    }
                }, getScoreStatus(score))
            ]);
        };
        
        return createElement(PluginSidebar, {
            name: 'smart-seo-sidebar',
            title: __('Smart SEO', 'smart-seo-booster'),
            icon: '📈'
        }, [
            createElement(PanelBody, {
                key: 'score-panel',
                title: __('SEO Score', 'smart-seo-booster'),
                initialOpen: true
            }, [
                seoData ? [
                    createElement(ScoreCircle, { key: 'circle', score: seoData.score }),
                    createElement(PanelRow, { key: 'stats' }, 
                        createElement('div', { style: { width: '100%' } }, [
                            createElement('h4', { key: 'stats-title', style: { margin: '10px 0 5px 0' } }, __('Content Stats', 'smart-seo-booster')),
                            createElement('ul', { key: 'stats-list', style: { margin: 0, paddingLeft: '20px', fontSize: '13px' } }, [
                                createElement('li', { key: 'words' }, `Words: ${seoData.wordCount}`),
                                createElement('li', { key: 'headings' }, `Headings: ${seoData.headingCount}`),
                                createElement('li', { key: 'images' }, `Images: ${seoData.imageCount} (${seoData.altCount} with alt text)`),
                                createElement('li', { key: 'links' }, `Internal Links: ${seoData.internalLinks}`)
                            ])
                        ])
                    )
                ] : [createElement(Spinner, { key: 'spinner' })]
            ]),
            
            seoData && seoData.recommendations.length > 0 && createElement(PanelBody, {
                key: 'recommendations-panel',
                title: __('Recommendations', 'smart-seo-booster'),
                initialOpen: true
            }, 
                createElement('ul', { style: { margin: 0, paddingLeft: '20px', fontSize: '13px', lineHeight: '1.4' } }, 
                    seoData.recommendations.map((rec, index) => 
                        createElement('li', { key: index, style: { marginBottom: '5px' } }, rec)
                    )
                )
            ),
            
            createElement(PanelBody, {
                key: 'actions-panel',
                title: __('Quick Actions', 'smart-seo-booster'),
                initialOpen: false
            }, [
                createElement(Button, {
                    key: 'refresh',
                    isPrimary: true,
                    onClick: refreshScore,
                    style: { marginBottom: '10px', width: '100%' }
                }, __('Refresh Score', 'smart-seo-booster')),
                createElement(Button, {
                    key: 'audit',
                    isSecondary: true,
                    href: `${window.location.origin}/wp-admin/admin.php?page=smart-seo-audit`,
                    target: '_blank',
                    style: { width: '100%' }
                }, __('View Full Audit', 'smart-seo-booster'))
            ])
        ]);
    };
    
    // Register the plugin
    registerPlugin('smart-seo', {
        render: () => [
            createElement(PluginSidebarMoreMenuItem, {
                key: 'menu-item',
                target: 'smart-seo-sidebar',
                icon: '📈'
            }, __('Smart SEO', 'smart-seo-booster')),
            createElement(SmartSEOSidebar, { key: 'sidebar' })
        ]
    });
    
})();
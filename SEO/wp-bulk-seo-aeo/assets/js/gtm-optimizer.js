/**
 * WP Bulk SEO & AEO - GTM Style Optimizer
 * 
 * One-line code injection that enables automatic SEO optimizations
 * Works like Google Tag Manager - minimal code, maximum impact
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 */

(function(window, document) {
    'use strict';

    var WPBulkSEOAEO = window.WPBulkSEOAEO || {};
    var siteId = WPBulkSEOAEO.site || '';

    if (!siteId) {
        return; // Site ID not provided
    }

    /**
     * Initialize optimizer
     */
    function init() {
        // Auto-optimize meta tags
        optimizeMetaTags();

        // Auto-optimize images
        optimizeImages();

        // Auto-add schema
        injectSchema();

        // Track page performance
        trackPerformance();

        // Sync data with WordPress
        syncData();
    }

    /**
     * Optimize meta tags
     */
    function optimizeMetaTags() {
        // Title optimization
        var title = document.querySelector('title');
        if (title) {
            var currentTitle = title.textContent || title.innerText;
            var optimizedTitle = optimizeTitle(currentTitle);
            if (optimizedTitle !== currentTitle) {
                title.textContent = optimizedTitle;
            }
        }

        // Meta description optimization
        var metaDesc = document.querySelector('meta[name="description"]');
        if (metaDesc) {
            var currentDesc = metaDesc.getAttribute('content') || '';
            var optimizedDesc = optimizeDescription(currentDesc);
            if (optimizedDesc !== currentDesc) {
                metaDesc.setAttribute('content', optimizedDesc);
            }
        }
    }

    /**
     * Optimize title
     */
    function optimizeTitle(title) {
        // Ensure optimal length (50-60 chars)
        if (title.length > 60) {
            return title.substring(0, 57) + '...';
        }
        return title;
    }

    /**
     * Optimize description
     */
    function optimizeDescription(desc) {
        // Ensure optimal length (150-160 chars)
        if (desc.length > 160) {
            return desc.substring(0, 157) + '...';
        }
        if (desc.length < 120) {
            // Try to extend from page content
            var content = document.body ? document.body.innerText : '';
            if (content) {
                var extended = content.substring(0, 160).trim();
                if (extended.length > desc.length) {
                    return extended.substring(0, 157) + '...';
                }
            }
        }
        return desc;
    }

    /**
     * Optimize images
     */
    function optimizeImages() {
        var images = document.querySelectorAll('img:not([alt])');
        images.forEach(function(img) {
            // Try to get alt from title or filename
            var alt = img.getAttribute('title') || 
                     img.getAttribute('src')?.split('/').pop()?.replace(/\.[^/.]+$/, '') || 
                     'Image';
            img.setAttribute('alt', alt);
        });

        // Add lazy loading
        var imagesWithoutLoading = document.querySelectorAll('img:not([loading])');
        imagesWithoutLoading.forEach(function(img) {
            img.setAttribute('loading', 'lazy');
        });
    }

    /**
     * Inject schema
     */
    function injectSchema() {
        // Check if schema already exists
        if (document.querySelector('script[type="application/ld+json"]')) {
            return;
        }

        // Generate basic schema
        var schema = {
            '@context': 'https://schema.org',
            '@type': 'WebPage',
            'name': document.title,
            'url': window.location.href,
        };

        var metaDesc = document.querySelector('meta[name="description"]');
        if (metaDesc) {
            schema.description = metaDesc.getAttribute('content');
        }

        // Inject schema
        var script = document.createElement('script');
        script.type = 'application/ld+json';
        script.textContent = JSON.stringify(schema);
        document.head.appendChild(script);
    }

    /**
     * Track page performance
     */
    function trackPerformance() {
        if ('PerformanceObserver' in window) {
            // Track Core Web Vitals
            try {
                var observer = new PerformanceObserver(function(list) {
                    var entries = list.getEntries();
                    entries.forEach(function(entry) {
                        if (entry.entryType === 'largest-contentful-paint') {
                            trackMetric('lcp', entry.renderTime || entry.loadTime);
                        }
                        if (entry.entryType === 'first-input') {
                            trackMetric('fid', entry.processingStart - entry.startTime);
                        }
                        if (entry.entryType === 'layout-shift' && !entry.hadRecentInput) {
                            trackMetric('cls', entry.value);
                        }
                    });
                });

                observer.observe({ entryTypes: ['largest-contentful-paint', 'first-input', 'layout-shift'] });
            } catch (e) {
                // PerformanceObserver not fully supported
            }
        }
    }

    /**
     * Track metric
     */
    function trackMetric(name, value) {
        // Send to WordPress API
        if (typeof fetch !== 'undefined') {
            fetch(WPBulkSEOAEO.apiUrl || '/wp-json/wp-bulk-seo-aeo/v1/track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    site: siteId,
                    metric: name,
                    value: value,
                    url: window.location.href,
                }),
            }).catch(function() {
                // Silent fail
            });
        }
    }

    /**
     * Sync data with WordPress
     */
    function syncData() {
        var pageData = {
            url: window.location.href,
            title: document.title,
            description: document.querySelector('meta[name="description"]')?.getAttribute('content') || '',
            wordCount: document.body ? document.body.innerText.split(/\s+/).length : 0,
            imageCount: document.querySelectorAll('img').length,
            linkCount: document.querySelectorAll('a').length,
        };

        // Send to WordPress
        if (typeof fetch !== 'undefined' && WPBulkSEOAEO.apiUrl) {
            fetch(WPBulkSEOAEO.apiUrl + '/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Site-Token': siteId,
                },
                body: JSON.stringify(pageData),
            }).catch(function() {
                // Silent fail
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose API
    window.WPBulkSEOAEO = WPBulkSEOAEO;
    WPBulkSEOAEO.init = init;
    WPBulkSEOAEO.optimize = function() {
        optimizeMetaTags();
        optimizeImages();
        injectSchema();
    };

})(window, document);

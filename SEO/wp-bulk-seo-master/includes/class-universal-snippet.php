<?php
/**
 * WP Bulk SEO Master - Universal Snippet
 *
 * Provides "one line of code" SEO solution for non-WordPress sites
 * Works like GTM - inject once, manage remotely
 *
 * @package WP_Bulk_SEO_Master
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Universal_Snippet {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // API endpoint for snippet to fetch instructions
        add_action('rest_api_init', [$this, 'register_api_routes']);
        
        // Serve the snippet JavaScript
        add_action('init', [$this, 'maybe_serve_snippet_js']);
    }

    /**
     * Register REST API routes
     */
    public function register_api_routes() {
        register_rest_route('seo-master/v1', '/snippet/config', [
            'methods' => 'GET',
            'callback' => [$this, 'get_snippet_config'],
            'permission_callback' => '__return_true',
            'args' => [
                'key' => ['required' => true, 'type' => 'string']
            ]
        ]);

        register_rest_route('seo-master/v1', '/snippet/report', [
            'methods' => 'POST',
            'callback' => [$this, 'receive_report'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('seo-master/v1', '/snippet/changes-applied', [
            'methods' => 'POST',
            'callback' => [$this, 'mark_changes_applied'],
            'permission_callback' => '__return_true'
        ]);
    }

    /**
     * Get configuration for snippet
     */
    public function get_snippet_config($request) {
        global $wpdb;

        $snippet_key = sanitize_text_field($request->get_param('key'));

        $site = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}seo_master_remote_sites WHERE snippet_key = %s AND status = 'active'",
            $snippet_key
        ));

        if (!$site) {
            return new WP_REST_Response(['error' => 'Invalid key'], 401);
        }

        // Get pending changes
        $pending_changes = $wpdb->get_results($wpdb->prepare(
            "SELECT id, change_type, target_selector, new_value 
             FROM {$wpdb->prefix}seo_master_bulk_queue 
             WHERE site_id = %d AND status = 'pending'",
            $site->id
        ));

        // Get site settings
        $settings = json_decode($site->settings, true) ?: [];

        return new WP_REST_Response([
            'site_id' => $site->id,
            'settings' => [
                'auto_optimize_title' => $settings['auto_optimize_title'] ?? false,
                'auto_optimize_description' => $settings['auto_optimize_description'] ?? false,
                'auto_add_schema' => $settings['auto_add_schema'] ?? true,
                'auto_fix_images' => $settings['auto_fix_images'] ?? true,
                'auto_add_canonical' => $settings['auto_add_canonical'] ?? true,
                'inject_og_tags' => $settings['inject_og_tags'] ?? true,
                'inject_twitter_cards' => $settings['inject_twitter_cards'] ?? true
            ],
            'changes' => array_map(function($c) {
                return [
                    'id' => $c->id,
                    'type' => $c->change_type,
                    'selector' => $c->target_selector,
                    'value' => $c->new_value
                ];
            }, $pending_changes),
            'schema_templates' => $this->get_schema_templates($site),
            'timestamp' => time()
        ]);
    }

    /**
     * Receive report from snippet
     */
    public function receive_report($request) {
        global $wpdb;

        $data = $request->get_json_params();
        $snippet_key = sanitize_text_field($data['key'] ?? '');

        $site = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}seo_master_remote_sites WHERE snippet_key = %s",
            $snippet_key
        ));

        if (!$site) {
            return new WP_REST_Response(['error' => 'Invalid key'], 401);
        }

        // Update site with reported data
        $wpdb->update(
            $wpdb->prefix . 'seo_master_remote_sites',
            [
                'last_crawl' => current_time('mysql'),
                'seo_score' => $data['score'] ?? null,
                'updated_at' => current_time('mysql')
            ],
            ['id' => $site->id]
        );

        // Save audit if provided
        if (!empty($data['audit'])) {
            $wpdb->insert(
                $wpdb->prefix . 'seo_master_audits',
                [
                    'site_id' => $site->id,
                    'audit_type' => 'snippet',
                    'overall_score' => $data['score'] ?? 0,
                    'technical_score' => $data['audit']['technical'] ?? 0,
                    'content_score' => $data['audit']['content'] ?? 0,
                    'issues_count' => count($data['audit']['issues'] ?? []),
                    'audit_data' => json_encode($data['audit']),
                    'created_at' => current_time('mysql')
                ]
            );
        }

        return new WP_REST_Response(['success' => true]);
    }

    /**
     * Mark changes as applied
     */
    public function mark_changes_applied($request) {
        global $wpdb;

        $data = $request->get_json_params();
        $change_ids = array_map('intval', $data['change_ids'] ?? []);

        if (!empty($change_ids)) {
            $ids = implode(',', $change_ids);
            $wpdb->query(
                "UPDATE {$wpdb->prefix}seo_master_bulk_queue 
                 SET status = 'applied', applied_at = NOW() 
                 WHERE id IN ($ids)"
            );
        }

        return new WP_REST_Response(['success' => true]);
    }

    /**
     * Get schema templates for site
     */
    private function get_schema_templates($site) {
        $settings = json_decode($site->settings, true) ?: [];

        $templates = [];

        // Organization schema
        if (!empty($settings['organization'])) {
            $templates['Organization'] = [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => $settings['organization']['name'] ?? '',
                'url' => $site->site_url,
                'logo' => $settings['organization']['logo'] ?? ''
            ];
        }

        // Website schema
        $templates['WebSite'] = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $site->site_name ?: parse_url($site->site_url, PHP_URL_HOST),
            'url' => $site->site_url
        ];

        return $templates;
    }

    /**
     * Serve snippet JavaScript
     */
    public function maybe_serve_snippet_js() {
        if (!isset($_GET['jjseo-snippet'])) {
            return;
        }

        header('Content-Type: application/javascript');
        header('Cache-Control: public, max-age=3600');
        
        echo $this->get_snippet_javascript();
        exit;
    }

    /**
     * Get the universal snippet JavaScript
     */
    private function get_snippet_javascript() {
        $api_url = rest_url('seo-master/v1/snippet');

        return <<<JS
/**
 * 3J SEO Universal Snippet
 * Automatically optimizes SEO for any website
 */
(function() {
    'use strict';

    var JJSEO = window.JJSEO || {};
    var API_URL = '{$api_url}';
    var snippetKey = document.currentScript?.getAttribute('data-key') || '';

    if (!snippetKey) {
        console.warn('3J SEO: Missing snippet key');
        return;
    }

    JJSEO.init = function() {
        this.fetchConfig();
        this.analyzeCurrentPage();
    };

    JJSEO.fetchConfig = function() {
        fetch(API_URL + '/config?key=' + encodeURIComponent(snippetKey))
            .then(function(r) { return r.json(); })
            .then(function(config) {
                if (config.error) {
                    console.warn('3J SEO:', config.error);
                    return;
                }
                JJSEO.config = config;
                JJSEO.applyOptimizations();
                JJSEO.applyPendingChanges();
            })
            .catch(function(e) {
                console.warn('3J SEO: Failed to fetch config', e);
            });
    };

    JJSEO.analyzeCurrentPage = function() {
        var data = {
            url: window.location.href,
            title: document.title,
            description: '',
            h1: [],
            images: { total: 0, withoutAlt: 0 },
            hasCanonical: false,
            hasSchema: false,
            wordCount: 0
        };

        // Meta description
        var metaDesc = document.querySelector('meta[name="description"]');
        if (metaDesc) data.description = metaDesc.content;

        // H1 tags
        document.querySelectorAll('h1').forEach(function(h1) {
            data.h1.push(h1.textContent.trim());
        });

        // Images
        document.querySelectorAll('img').forEach(function(img) {
            data.images.total++;
            if (!img.alt || img.alt.trim() === '') {
                data.images.withoutAlt++;
            }
        });

        // Canonical
        data.hasCanonical = !!document.querySelector('link[rel="canonical"]');

        // Schema
        data.hasSchema = !!document.querySelector('script[type="application/ld+json"]');

        // Word count
        var bodyText = document.body?.innerText || '';
        data.wordCount = bodyText.split(/\\s+/).filter(function(w) { return w.length > 0; }).length;

        JJSEO.pageData = data;
    };

    JJSEO.applyOptimizations = function() {
        var settings = JJSEO.config?.settings || {};

        // Auto-add canonical
        if (settings.auto_add_canonical && !JJSEO.pageData.hasCanonical) {
            var canonical = document.createElement('link');
            canonical.rel = 'canonical';
            canonical.href = window.location.href.split('?')[0];
            document.head.appendChild(canonical);
        }

        // Auto-add OG tags
        if (settings.inject_og_tags) {
            JJSEO.injectOGTags();
        }

        // Auto-add Twitter cards
        if (settings.inject_twitter_cards) {
            JJSEO.injectTwitterCards();
        }

        // Auto-add schema
        if (settings.auto_add_schema && !JJSEO.pageData.hasSchema) {
            JJSEO.injectSchema();
        }

        // Auto-fix images
        if (settings.auto_fix_images) {
            JJSEO.fixImageAlts();
        }
    };

    JJSEO.injectOGTags = function() {
        var tags = {
            'og:title': document.title,
            'og:description': JJSEO.pageData.description,
            'og:url': window.location.href,
            'og:type': 'website'
        };

        for (var property in tags) {
            if (!document.querySelector('meta[property="' + property + '"]') && tags[property]) {
                var meta = document.createElement('meta');
                meta.setAttribute('property', property);
                meta.content = tags[property];
                document.head.appendChild(meta);
            }
        }
    };

    JJSEO.injectTwitterCards = function() {
        var tags = {
            'twitter:card': 'summary_large_image',
            'twitter:title': document.title,
            'twitter:description': JJSEO.pageData.description
        };

        for (var name in tags) {
            if (!document.querySelector('meta[name="' + name + '"]') && tags[name]) {
                var meta = document.createElement('meta');
                meta.name = name;
                meta.content = tags[name];
                document.head.appendChild(meta);
            }
        }
    };

    JJSEO.injectSchema = function() {
        var templates = JJSEO.config?.schema_templates || {};
        
        for (var type in templates) {
            var script = document.createElement('script');
            script.type = 'application/ld+json';
            script.text = JSON.stringify(templates[type]);
            document.head.appendChild(script);
        }
    };

    JJSEO.fixImageAlts = function() {
        document.querySelectorAll('img:not([alt]), img[alt=""]').forEach(function(img) {
            // Generate alt from filename or nearby text
            var src = img.src || '';
            var filename = src.split('/').pop().split('?')[0];
            var alt = filename.replace(/[-_]/g, ' ').replace(/\\.[^.]+$/, '');
            
            if (alt) {
                img.alt = alt.charAt(0).toUpperCase() + alt.slice(1);
            }
        });
    };

    JJSEO.applyPendingChanges = function() {
        var changes = JJSEO.config?.changes || [];
        var appliedIds = [];

        changes.forEach(function(change) {
            try {
                switch (change.type) {
                    case 'meta_title':
                        document.title = change.value;
                        break;
                    case 'meta_description':
                        var desc = document.querySelector('meta[name="description"]');
                        if (desc) {
                            desc.content = change.value;
                        } else {
                            var newDesc = document.createElement('meta');
                            newDesc.name = 'description';
                            newDesc.content = change.value;
                            document.head.appendChild(newDesc);
                        }
                        break;
                    case 'element_text':
                        var el = document.querySelector(change.selector);
                        if (el) el.textContent = change.value;
                        break;
                    case 'element_attribute':
                        var parts = change.selector.split('@');
                        var elem = document.querySelector(parts[0]);
                        if (elem && parts[1]) {
                            elem.setAttribute(parts[1], change.value);
                        }
                        break;
                    case 'inject_html':
                        var target = document.querySelector(change.selector);
                        if (target) {
                            target.insertAdjacentHTML('beforeend', change.value);
                        }
                        break;
                }
                appliedIds.push(change.id);
            } catch (e) {
                console.warn('3J SEO: Failed to apply change', change, e);
            }
        });

        // Report applied changes
        if (appliedIds.length > 0) {
            fetch(API_URL + '/changes-applied', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ key: snippetKey, change_ids: appliedIds })
            });
        }
    };

    JJSEO.sendReport = function() {
        var score = JJSEO.calculateScore();
        
        fetch(API_URL + '/report', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                key: snippetKey,
                url: window.location.href,
                score: score,
                audit: {
                    technical: JJSEO.getTechnicalScore(),
                    content: JJSEO.getContentScore(),
                    issues: JJSEO.getIssues()
                }
            })
        });
    };

    JJSEO.calculateScore = function() {
        var score = 0;
        var data = JJSEO.pageData;

        if (data.title) score += 15;
        if (data.description) score += 15;
        if (data.h1.length === 1) score += 10;
        if (data.hasCanonical) score += 10;
        if (data.hasSchema) score += 10;
        if (data.wordCount >= 300) score += 10;
        if (data.wordCount >= 800) score += 10;
        if (data.images.total > 0 && data.images.withoutAlt === 0) score += 10;

        return Math.min(100, score);
    };

    JJSEO.getTechnicalScore = function() {
        var score = 0;
        if (JJSEO.pageData.title) score += 25;
        if (JJSEO.pageData.description) score += 25;
        if (JJSEO.pageData.hasCanonical) score += 25;
        if (JJSEO.pageData.hasSchema) score += 25;
        return score;
    };

    JJSEO.getContentScore = function() {
        var score = 0;
        var data = JJSEO.pageData;
        if (data.h1.length === 1) score += 30;
        if (data.wordCount >= 300) score += 20;
        if (data.wordCount >= 800) score += 20;
        if (data.images.total > 0) score += 15;
        if (data.images.withoutAlt === 0) score += 15;
        return Math.min(100, score);
    };

    JJSEO.getIssues = function() {
        var issues = [];
        var data = JJSEO.pageData;

        if (!data.title) issues.push({ type: 'missing_title', severity: 'critical' });
        if (!data.description) issues.push({ type: 'missing_description', severity: 'critical' });
        if (data.h1.length === 0) issues.push({ type: 'missing_h1', severity: 'critical' });
        if (data.h1.length > 1) issues.push({ type: 'multiple_h1', severity: 'warning' });
        if (data.images.withoutAlt > 0) issues.push({ type: 'missing_alt', severity: 'warning', count: data.images.withoutAlt });
        if (!data.hasCanonical) issues.push({ type: 'missing_canonical', severity: 'warning' });
        if (!data.hasSchema) issues.push({ type: 'missing_schema', severity: 'info' });
        if (data.wordCount < 300) issues.push({ type: 'thin_content', severity: 'warning' });

        return issues;
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { JJSEO.init(); });
    } else {
        JJSEO.init();
    }

    // Send report after page load
    window.addEventListener('load', function() {
        setTimeout(function() { JJSEO.sendReport(); }, 2000);
    });

    window.JJSEO = JJSEO;
})();
JS;
    }
}

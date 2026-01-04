<?php
/**
 * WP Bulk SEO Master - Remote Site Manager
 *
 * Manages both WordPress and non-WordPress sites
 *
 * @package WP_Bulk_SEO_Master
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Remote_Site_Manager {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_add_remote_site', [$this, 'ajax_add_site']);
        add_action('wp_ajax_remove_remote_site', [$this, 'ajax_remove_site']);
        add_action('wp_ajax_crawl_remote_site', [$this, 'ajax_crawl_site']);
        add_action('wp_ajax_push_seo_changes', [$this, 'ajax_push_changes']);
    }

    /**
     * Add a new remote site
     */
    public function add_site($data) {
        global $wpdb;

        $site_url = trailingslashit(esc_url_raw($data['url']));
        $snippet_key = $this->generate_snippet_key();

        // Check if site already exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}seo_master_remote_sites WHERE site_url = %s",
            $site_url
        ));

        if ($existing) {
            return new WP_Error('exists', 'Site already registered');
        }

        // Detect site type
        $site_type = $this->detect_site_type($site_url);

        $result = $wpdb->insert(
            $wpdb->prefix . 'seo_master_remote_sites',
            [
                'site_url' => $site_url,
                'site_name' => sanitize_text_field($data['name'] ?? ''),
                'site_type' => $site_type,
                'snippet_key' => $snippet_key,
                'settings' => json_encode($data['settings'] ?? []),
                'status' => 'pending',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ]
        );

        if (!$result) {
            return new WP_Error('db_error', 'Failed to add site');
        }

        return [
            'site_id' => $wpdb->insert_id,
            'snippet_key' => $snippet_key,
            'site_type' => $site_type,
            'snippet_code' => $this->get_snippet_code($snippet_key)
        ];
    }

    /**
     * Detect site platform type
     */
    private function detect_site_type($url) {
        $response = wp_remote_get($url, ['timeout' => 15]);
        
        if (is_wp_error($response)) {
            return 'unknown';
        }

        $body = wp_remote_retrieve_body($response);
        $headers = wp_remote_retrieve_headers($response);

        // WordPress detection
        if (strpos($body, 'wp-content') !== false || strpos($body, 'wp-includes') !== false) {
            return 'wordpress';
        }

        // Webflow detection
        if (strpos($body, 'webflow') !== false || isset($headers['x-wf-site'])) {
            return 'webflow';
        }

        // Framer detection
        if (strpos($body, 'framer') !== false) {
            return 'framer';
        }

        // Ghost detection
        if (strpos($body, 'ghost') !== false || strpos($body, 'Ghost') !== false) {
            return 'ghost';
        }

        // Imweb (Korea)
        if (strpos($body, 'imweb') !== false || strpos($url, 'imweb.me') !== false) {
            return 'imweb';
        }

        // Cafe24 (Korea)
        if (strpos($body, 'cafe24') !== false || strpos($url, 'cafe24.com') !== false) {
            return 'cafe24';
        }

        // Shopify
        if (strpos($body, 'shopify') !== false || strpos($body, 'Shopify') !== false) {
            return 'shopify';
        }

        // Wix
        if (strpos($body, 'wix') !== false) {
            return 'wix';
        }

        // Squarespace
        if (strpos($body, 'squarespace') !== false) {
            return 'squarespace';
        }

        return 'other';
    }

    /**
     * Generate snippet key
     */
    private function generate_snippet_key() {
        return 'jjseo_' . bin2hex(random_bytes(16));
    }

    /**
     * Get snippet code for site
     */
    public function get_snippet_code($snippet_key) {
        $master_url = home_url();
        
        return <<<HTML
<!-- 3J SEO Optimizer - Add this to your site's <head> section -->
<script>
(function(){
    var s=document.createElement('script');
    s.src='{$master_url}/wp-content/plugins/wp-bulk-seo-master/assets/js/seo-snippet.min.js';
    s.setAttribute('data-key','{$snippet_key}');
    s.async=true;
    document.head.appendChild(s);
})();
</script>
<!-- End 3J SEO Optimizer -->
HTML;
    }

    /**
     * Crawl and analyze a remote site
     */
    public function crawl_site($site_id) {
        global $wpdb;

        $site = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}seo_master_remote_sites WHERE id = %d",
            $site_id
        ));

        if (!$site) {
            return new WP_Error('not_found', 'Site not found');
        }

        $url = $site->site_url;

        // Fetch page
        $response = wp_remote_get($url, ['timeout' => 30]);

        if (is_wp_error($response)) {
            return $response;
        }

        $html = wp_remote_retrieve_body($response);
        $analysis = $this->analyze_page($html, $url);

        // Calculate score
        $score = $this->calculate_seo_score($analysis);

        // Update site
        $wpdb->update(
            $wpdb->prefix . 'seo_master_remote_sites',
            [
                'last_crawl' => current_time('mysql'),
                'seo_score' => $score,
                'status' => 'active',
                'updated_at' => current_time('mysql')
            ],
            ['id' => $site_id]
        );

        // Save audit
        $wpdb->insert(
            $wpdb->prefix . 'seo_master_audits',
            [
                'site_id' => $site_id,
                'audit_type' => 'crawl',
                'overall_score' => $score,
                'technical_score' => $analysis['scores']['technical'] ?? 0,
                'content_score' => $analysis['scores']['content'] ?? 0,
                'performance_score' => $analysis['scores']['performance'] ?? 0,
                'issues_count' => count($analysis['issues'] ?? []),
                'audit_data' => json_encode($analysis),
                'created_at' => current_time('mysql')
            ]
        );

        return [
            'score' => $score,
            'analysis' => $analysis
        ];
    }

    /**
     * Analyze page HTML
     */
    private function analyze_page($html, $url) {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($doc);

        $analysis = [
            'url' => $url,
            'title' => '',
            'meta_description' => '',
            'h1' => [],
            'h2_count' => 0,
            'h3_count' => 0,
            'images' => ['total' => 0, 'without_alt' => 0],
            'links' => ['internal' => 0, 'external' => 0, 'nofollow' => 0],
            'word_count' => 0,
            'has_schema' => false,
            'canonical' => '',
            'robots' => '',
            'og_tags' => [],
            'issues' => [],
            'scores' => []
        ];

        // Title
        $title = $xpath->query('//title');
        if ($title->length > 0) {
            $analysis['title'] = trim($title->item(0)->textContent);
        }

        // Meta description
        $metaDesc = $xpath->query('//meta[@name="description"]/@content');
        if ($metaDesc->length > 0) {
            $analysis['meta_description'] = $metaDesc->item(0)->textContent;
        }

        // H1
        $h1s = $xpath->query('//h1');
        foreach ($h1s as $h1) {
            $analysis['h1'][] = trim($h1->textContent);
        }

        // H2, H3 count
        $analysis['h2_count'] = $xpath->query('//h2')->length;
        $analysis['h3_count'] = $xpath->query('//h3')->length;

        // Images
        $images = $xpath->query('//img');
        $analysis['images']['total'] = $images->length;
        foreach ($images as $img) {
            $alt = $img->getAttribute('alt');
            if (empty($alt)) {
                $analysis['images']['without_alt']++;
            }
        }

        // Links
        $urlParts = parse_url($url);
        $domain = $urlParts['host'] ?? '';
        
        $links = $xpath->query('//a[@href]');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $rel = $link->getAttribute('rel');

            if (strpos($rel, 'nofollow') !== false) {
                $analysis['links']['nofollow']++;
            }

            $linkParts = parse_url($href);
            if (isset($linkParts['host'])) {
                if ($linkParts['host'] === $domain || strpos($linkParts['host'], $domain) !== false) {
                    $analysis['links']['internal']++;
                } else {
                    $analysis['links']['external']++;
                }
            } else {
                $analysis['links']['internal']++;
            }
        }

        // Word count
        $body = $xpath->query('//body');
        if ($body->length > 0) {
            $text = preg_replace('/\s+/', ' ', strip_tags($body->item(0)->textContent));
            $analysis['word_count'] = str_word_count($text);
        }

        // Schema
        $schema = $xpath->query('//script[@type="application/ld+json"]');
        $analysis['has_schema'] = $schema->length > 0;

        // Canonical
        $canonical = $xpath->query('//link[@rel="canonical"]/@href');
        if ($canonical->length > 0) {
            $analysis['canonical'] = $canonical->item(0)->textContent;
        }

        // Robots
        $robots = $xpath->query('//meta[@name="robots"]/@content');
        if ($robots->length > 0) {
            $analysis['robots'] = $robots->item(0)->textContent;
        }

        // OG tags
        $ogTags = $xpath->query('//meta[starts-with(@property, "og:")]');
        foreach ($ogTags as $og) {
            $property = str_replace('og:', '', $og->getAttribute('property'));
            $analysis['og_tags'][$property] = $og->getAttribute('content');
        }

        // Identify issues
        $analysis['issues'] = $this->identify_issues($analysis);

        // Calculate scores
        $analysis['scores'] = $this->calculate_module_scores($analysis);

        return $analysis;
    }

    /**
     * Identify SEO issues
     */
    private function identify_issues($analysis) {
        $issues = [];

        // Title issues
        if (empty($analysis['title'])) {
            $issues[] = ['type' => 'missing_title', 'severity' => 'critical', 'message' => 'Page is missing a title tag'];
        } elseif (strlen($analysis['title']) < 30) {
            $issues[] = ['type' => 'short_title', 'severity' => 'warning', 'message' => 'Title is too short (under 30 characters)'];
        } elseif (strlen($analysis['title']) > 60) {
            $issues[] = ['type' => 'long_title', 'severity' => 'warning', 'message' => 'Title is too long (over 60 characters)'];
        }

        // Description issues
        if (empty($analysis['meta_description'])) {
            $issues[] = ['type' => 'missing_description', 'severity' => 'critical', 'message' => 'Page is missing a meta description'];
        } elseif (strlen($analysis['meta_description']) < 120) {
            $issues[] = ['type' => 'short_description', 'severity' => 'warning', 'message' => 'Meta description is too short'];
        } elseif (strlen($analysis['meta_description']) > 160) {
            $issues[] = ['type' => 'long_description', 'severity' => 'warning', 'message' => 'Meta description is too long'];
        }

        // H1 issues
        if (count($analysis['h1']) === 0) {
            $issues[] = ['type' => 'missing_h1', 'severity' => 'critical', 'message' => 'Page is missing an H1 tag'];
        } elseif (count($analysis['h1']) > 1) {
            $issues[] = ['type' => 'multiple_h1', 'severity' => 'warning', 'message' => 'Page has multiple H1 tags'];
        }

        // Image alt issues
        if ($analysis['images']['without_alt'] > 0) {
            $issues[] = [
                'type' => 'missing_alt',
                'severity' => 'warning',
                'message' => $analysis['images']['without_alt'] . ' images are missing alt attributes'
            ];
        }

        // Content issues
        if ($analysis['word_count'] < 300) {
            $issues[] = ['type' => 'thin_content', 'severity' => 'warning', 'message' => 'Page has thin content (under 300 words)'];
        }

        // Link issues
        if ($analysis['links']['internal'] < 2) {
            $issues[] = ['type' => 'few_internal_links', 'severity' => 'info', 'message' => 'Page has few internal links'];
        }
        if ($analysis['links']['external'] === 0) {
            $issues[] = ['type' => 'no_external_links', 'severity' => 'info', 'message' => 'Page has no external links'];
        }

        // Schema
        if (!$analysis['has_schema']) {
            $issues[] = ['type' => 'no_schema', 'severity' => 'info', 'message' => 'Page has no structured data'];
        }

        // Canonical
        if (empty($analysis['canonical'])) {
            $issues[] = ['type' => 'missing_canonical', 'severity' => 'warning', 'message' => 'Page is missing a canonical URL'];
        }

        // OG tags
        if (empty($analysis['og_tags'])) {
            $issues[] = ['type' => 'missing_og', 'severity' => 'info', 'message' => 'Page is missing Open Graph tags'];
        }

        return $issues;
    }

    /**
     * Calculate module scores
     */
    private function calculate_module_scores($analysis) {
        $scores = ['technical' => 0, 'content' => 0, 'performance' => 50];

        // Technical score
        $technical = 0;
        if (!empty($analysis['title'])) $technical += 20;
        if (!empty($analysis['meta_description'])) $technical += 20;
        if (!empty($analysis['canonical'])) $technical += 15;
        if ($analysis['has_schema']) $technical += 15;
        if (!empty($analysis['og_tags'])) $technical += 10;
        if (count($analysis['h1']) === 1) $technical += 20;
        $scores['technical'] = min(100, $technical);

        // Content score
        $content = 0;
        if ($analysis['word_count'] >= 1500) $content += 30;
        elseif ($analysis['word_count'] >= 800) $content += 20;
        elseif ($analysis['word_count'] >= 300) $content += 10;

        if ($analysis['h2_count'] >= 3) $content += 20;
        elseif ($analysis['h2_count'] >= 1) $content += 10;

        if ($analysis['images']['total'] > 0) {
            $altRatio = ($analysis['images']['total'] - $analysis['images']['without_alt']) / $analysis['images']['total'];
            $content += intval($altRatio * 20);
        }

        if ($analysis['links']['internal'] >= 3) $content += 15;
        if ($analysis['links']['external'] >= 1) $content += 15;

        $scores['content'] = min(100, $content);

        return $scores;
    }

    /**
     * Calculate overall SEO score
     */
    private function calculate_seo_score($analysis) {
        $scores = $analysis['scores'];
        return intval(($scores['technical'] * 0.4) + ($scores['content'] * 0.4) + ($scores['performance'] * 0.2));
    }

    /**
     * Push SEO changes to remote site
     */
    public function push_changes($site_id, $changes) {
        global $wpdb;

        // Queue changes for the universal snippet to apply
        foreach ($changes as $change) {
            $wpdb->insert(
                $wpdb->prefix . 'seo_master_bulk_queue',
                [
                    'site_id' => $site_id,
                    'change_type' => $change['type'],
                    'target_selector' => $change['selector'] ?? '',
                    'old_value' => $change['old_value'] ?? '',
                    'new_value' => $change['new_value'] ?? '',
                    'status' => 'pending',
                    'created_at' => current_time('mysql')
                ]
            );
        }

        return ['success' => true, 'queued' => count($changes)];
    }

    /**
     * Get pending changes for a site
     */
    public function get_pending_changes($snippet_key) {
        global $wpdb;

        $site = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}seo_master_remote_sites WHERE snippet_key = %s",
            $snippet_key
        ));

        if (!$site) {
            return [];
        }

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}seo_master_bulk_queue 
             WHERE site_id = %d AND status = 'pending'
             ORDER BY created_at ASC",
            $site->id
        ));
    }

    /**
     * Mark changes as applied
     */
    public function mark_changes_applied($change_ids) {
        global $wpdb;

        if (empty($change_ids)) return;

        $ids = implode(',', array_map('intval', $change_ids));
        $wpdb->query(
            "UPDATE {$wpdb->prefix}seo_master_bulk_queue 
             SET status = 'applied', applied_at = NOW() 
             WHERE id IN ($ids)"
        );
    }

    /**
     * Get all remote sites
     */
    public function get_sites($args = []) {
        global $wpdb;

        $defaults = ['status' => '', 'type' => '', 'per_page' => 20, 'page' => 1];
        $args = wp_parse_args($args, $defaults);

        $where = ['1=1'];
        $params = [];

        if ($args['status']) {
            $where[] = 'status = %s';
            $params[] = $args['status'];
        }

        if ($args['type']) {
            $where[] = 'site_type = %s';
            $params[] = $args['type'];
        }

        $where_sql = implode(' AND ', $where);
        $offset = ($args['page'] - 1) * $args['per_page'];

        $params[] = $args['per_page'];
        $params[] = $offset;

        $sql = "SELECT * FROM {$wpdb->prefix}seo_master_remote_sites WHERE $where_sql ORDER BY created_at DESC LIMIT %d OFFSET %d";

        return $wpdb->get_results($wpdb->prepare($sql, $params));
    }

    /**
     * AJAX handlers
     */
    public function ajax_add_site() {
        check_ajax_referer('seo_master_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        $result = $this->add_site([
            'url' => $_POST['url'] ?? '',
            'name' => $_POST['name'] ?? ''
        ]);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success($result);
    }

    public function ajax_remove_site() {
        check_ajax_referer('seo_master_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        global $wpdb;
        $site_id = intval($_POST['site_id'] ?? 0);

        $wpdb->delete($wpdb->prefix . 'seo_master_remote_sites', ['id' => $site_id]);
        $wpdb->delete($wpdb->prefix . 'seo_master_bulk_queue', ['site_id' => $site_id]);

        wp_send_json_success(['message' => 'Site removed']);
    }

    public function ajax_crawl_site() {
        check_ajax_referer('seo_master_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        $site_id = intval($_POST['site_id'] ?? 0);
        $result = $this->crawl_site($site_id);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success($result);
    }

    public function ajax_push_changes() {
        check_ajax_referer('seo_master_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        $site_id = intval($_POST['site_id'] ?? 0);
        $changes = json_decode(stripslashes($_POST['changes'] ?? '[]'), true);

        $result = $this->push_changes($site_id, $changes);
        wp_send_json_success($result);
    }
}

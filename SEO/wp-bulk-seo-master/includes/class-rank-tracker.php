<?php
/**
 * WP Bulk SEO Master - Rank Tracker
 *
 * Monitors keyword rankings with configurable frequency (2-12 times per day)
 *
 * @package WP_Bulk_SEO_Master
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Rank_Tracker {

    private static $instance = null;

    const SEARCH_ENGINES = [
        'google' => 'https://www.google.com/search',
        'google_kr' => 'https://www.google.co.kr/search',
        'google_jp' => 'https://www.google.co.jp/search',
        'bing' => 'https://www.bing.com/search'
    ];

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_bulk_seo_rank_check', [$this, 'run_scheduled_check']);
        add_action('wp_ajax_check_keyword_rank', [$this, 'ajax_check_rank']);
        add_action('wp_ajax_add_keyword', [$this, 'ajax_add_keyword']);
        add_action('wp_ajax_remove_keyword', [$this, 'ajax_remove_keyword']);
        add_action('wp_ajax_get_rank_history', [$this, 'ajax_get_history']);
    }

    /**
     * Add keyword to track
     */
    public function add_keyword($data) {
        global $wpdb;

        $result = $wpdb->insert(
            $wpdb->prefix . 'seo_master_keywords',
            [
                'site_id' => $data['site_id'] ?? null,
                'keyword' => sanitize_text_field($data['keyword']),
                'target_url' => esc_url_raw($data['target_url'] ?? ''),
                'search_engine' => $data['search_engine'] ?? 'google',
                'country' => $data['country'] ?? 'us',
                'language' => $data['language'] ?? 'en',
                'device' => $data['device'] ?? 'desktop',
                'tags' => sanitize_text_field($data['tags'] ?? ''),
                'status' => 'active',
                'created_at' => current_time('mysql')
            ]
        );

        if (!$result) {
            return new WP_Error('db_error', 'Failed to add keyword');
        }

        $keyword_id = $wpdb->insert_id;

        // Immediately check rank
        $this->check_keyword_rank($keyword_id);

        return ['keyword_id' => $keyword_id];
    }

    /**
     * Remove keyword
     */
    public function remove_keyword($keyword_id) {
        global $wpdb;

        $wpdb->update(
            $wpdb->prefix . 'seo_master_keywords',
            ['status' => 'deleted'],
            ['id' => $keyword_id]
        );

        return ['success' => true];
    }

    /**
     * Check rank for a single keyword
     */
    public function check_keyword_rank($keyword_id) {
        global $wpdb;

        $keyword = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}seo_master_keywords WHERE id = %d",
            $keyword_id
        ));

        if (!$keyword) {
            return new WP_Error('not_found', 'Keyword not found');
        }

        // Get previous position
        $previous = $wpdb->get_var($wpdb->prepare(
            "SELECT position FROM {$wpdb->prefix}seo_master_rank_history 
             WHERE keyword_id = %d ORDER BY checked_at DESC LIMIT 1",
            $keyword_id
        ));

        // Perform search
        $result = $this->search_google($keyword->keyword, [
            'country' => $keyword->country,
            'language' => $keyword->language,
            'device' => $keyword->device,
            'target_url' => $keyword->target_url
        ]);

        if (is_wp_error($result)) {
            return $result;
        }

        // Save to history
        $wpdb->insert(
            $wpdb->prefix . 'seo_master_rank_history',
            [
                'keyword_id' => $keyword_id,
                'position' => $result['position'],
                'previous_position' => $previous,
                'url' => $result['url'] ?? '',
                'serp_features' => json_encode($result['features'] ?? []),
                'checked_at' => current_time('mysql')
            ]
        );

        return [
            'position' => $result['position'],
            'previous_position' => $previous,
            'change' => $previous ? ($previous - $result['position']) : null,
            'url' => $result['url'],
            'features' => $result['features']
        ];
    }

    /**
     * Search Google and find position
     */
    private function search_google($keyword, $options = []) {
        $country = $options['country'] ?? 'us';
        $language = $options['language'] ?? 'en';
        $device = $options['device'] ?? 'desktop';
        $target_url = $options['target_url'] ?? '';

        // Build search URL
        $params = [
            'q' => $keyword,
            'num' => 100,
            'hl' => $language,
            'gl' => $country
        ];

        $search_url = 'https://www.google.com/search?' . http_build_query($params);

        // User agent based on device
        $user_agents = [
            'desktop' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'mobile' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'
        ];

        $response = wp_remote_get($search_url, [
            'timeout' => 30,
            'user-agent' => $user_agents[$device] ?? $user_agents['desktop'],
            'headers' => [
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => $language . ',' . $language . '-' . strtoupper($country) . ';q=0.9'
            ]
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $html = wp_remote_retrieve_body($response);
        
        return $this->parse_serp_results($html, $target_url);
    }

    /**
     * Parse SERP results to find position
     */
    private function parse_serp_results($html, $target_url) {
        $position = null;
        $found_url = null;
        $features = [];

        // Parse target domain
        $target_domain = '';
        if ($target_url) {
            $parsed = parse_url($target_url);
            $target_domain = $parsed['host'] ?? '';
        }

        // Find organic results
        // Pattern: look for result URLs in the HTML
        preg_match_all('/<a[^>]+href="([^"]+)"[^>]*>/i', $html, $matches);

        $organic_position = 0;
        $seen_urls = [];

        foreach ($matches[1] as $url) {
            // Skip Google internal URLs
            if (strpos($url, 'google.com') !== false) continue;
            if (strpos($url, '/search?') !== false) continue;
            if (strpos($url, 'webcache') !== false) continue;
            if (strpos($url, 'translate.google') !== false) continue;

            // Clean URL
            if (strpos($url, '/url?') === 0) {
                parse_str(parse_url($url, PHP_URL_QUERY), $params);
                $url = $params['q'] ?? $url;
            }

            // Skip if not a valid URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) continue;

            // Skip duplicates
            if (in_array($url, $seen_urls)) continue;
            $seen_urls[] = $url;

            $organic_position++;

            // Check if matches target
            if ($target_domain) {
                $url_domain = parse_url($url, PHP_URL_HOST);
                if ($url_domain && strpos($url_domain, $target_domain) !== false) {
                    if ($position === null) {
                        $position = $organic_position;
                        $found_url = $url;
                    }
                }
            }

            // Limit to top 100
            if ($organic_position >= 100) break;
        }

        // Detect SERP features
        if (strpos($html, 'featured-snippet') !== false || strpos($html, 'kp-blk') !== false) {
            $features[] = 'featured_snippet';
        }
        if (strpos($html, 'related-question-pair') !== false) {
            $features[] = 'people_also_ask';
        }
        if (strpos($html, 'kp-wholepage') !== false) {
            $features[] = 'knowledge_panel';
        }
        if (strpos($html, 'local-pack') !== false || strpos($html, 'VkpGBb') !== false) {
            $features[] = 'local_pack';
        }

        return [
            'position' => $position,
            'url' => $found_url,
            'features' => $features,
            'total_results' => $organic_position
        ];
    }

    /**
     * Run scheduled rank checks
     */
    public function run_scheduled_check() {
        global $wpdb;

        $settings = get_option('wp_bulk_seo_master_settings', []);
        $frequency = $settings['rank_check_frequency'] ?? 2;

        // Determine which keywords to check this hour
        $hour = (int) date('G');
        $check_hours = $this->get_check_hours($frequency);

        if (!in_array($hour, $check_hours)) {
            return;
        }

        // Get active keywords
        $keywords = $wpdb->get_results(
            "SELECT id FROM {$wpdb->prefix}seo_master_keywords WHERE status = 'active'"
        );

        foreach ($keywords as $keyword) {
            $this->check_keyword_rank($keyword->id);
            
            // Rate limiting - wait between requests
            usleep(500000); // 0.5 seconds
        }
    }

    /**
     * Get hours to check based on frequency
     */
    private function get_check_hours($frequency) {
        $hours = [];
        $interval = intval(24 / $frequency);
        
        for ($i = 0; $i < $frequency; $i++) {
            $hours[] = ($i * $interval) % 24;
        }

        return $hours;
    }

    /**
     * Get rank history for a keyword
     */
    public function get_history($keyword_id, $days = 30) {
        global $wpdb;

        $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $wpdb->get_results($wpdb->prepare(
            "SELECT position, previous_position, url, serp_features, checked_at 
             FROM {$wpdb->prefix}seo_master_rank_history 
             WHERE keyword_id = %d AND checked_at >= %s 
             ORDER BY checked_at ASC",
            $keyword_id, $since
        ));
    }

    /**
     * Get all keywords with current rank
     */
    public function get_keywords($args = []) {
        global $wpdb;

        $defaults = ['site_id' => null, 'status' => 'active', 'per_page' => 50, 'page' => 1];
        $args = wp_parse_args($args, $defaults);

        $where = ['k.status = %s'];
        $params = [$args['status']];

        if ($args['site_id']) {
            $where[] = 'k.site_id = %d';
            $params[] = $args['site_id'];
        }

        $where_sql = implode(' AND ', $where);
        $offset = ($args['page'] - 1) * $args['per_page'];

        $params[] = $args['per_page'];
        $params[] = $offset;

        $sql = "SELECT k.*, 
                       h.position as current_position,
                       h.previous_position,
                       h.checked_at as last_checked
                FROM {$wpdb->prefix}seo_master_keywords k
                LEFT JOIN (
                    SELECT keyword_id, position, previous_position, checked_at,
                           ROW_NUMBER() OVER (PARTITION BY keyword_id ORDER BY checked_at DESC) as rn
                    FROM {$wpdb->prefix}seo_master_rank_history
                ) h ON k.id = h.keyword_id AND h.rn = 1
                WHERE $where_sql
                ORDER BY k.created_at DESC
                LIMIT %d OFFSET %d";

        return $wpdb->get_results($wpdb->prepare($sql, $params));
    }

    /**
     * Get rank statistics
     */
    public function get_statistics($site_id = null) {
        global $wpdb;

        $where = $site_id ? $wpdb->prepare("WHERE k.site_id = %d", $site_id) : '';

        // Total keywords
        $total = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}seo_master_keywords k $where"
        );

        // Keywords in top 10
        $top10 = $wpdb->get_var(
            "SELECT COUNT(DISTINCT k.id) 
             FROM {$wpdb->prefix}seo_master_keywords k
             INNER JOIN (
                 SELECT keyword_id, position,
                        ROW_NUMBER() OVER (PARTITION BY keyword_id ORDER BY checked_at DESC) as rn
                 FROM {$wpdb->prefix}seo_master_rank_history
             ) h ON k.id = h.keyword_id AND h.rn = 1
             $where AND h.position <= 10"
        );

        // Keywords improved
        $improved = $wpdb->get_var(
            "SELECT COUNT(DISTINCT k.id) 
             FROM {$wpdb->prefix}seo_master_keywords k
             INNER JOIN (
                 SELECT keyword_id, position, previous_position,
                        ROW_NUMBER() OVER (PARTITION BY keyword_id ORDER BY checked_at DESC) as rn
                 FROM {$wpdb->prefix}seo_master_rank_history
             ) h ON k.id = h.keyword_id AND h.rn = 1
             $where AND h.position < h.previous_position"
        );

        // Average position
        $avg_position = $wpdb->get_var(
            "SELECT AVG(h.position) 
             FROM {$wpdb->prefix}seo_master_keywords k
             INNER JOIN (
                 SELECT keyword_id, position,
                        ROW_NUMBER() OVER (PARTITION BY keyword_id ORDER BY checked_at DESC) as rn
                 FROM {$wpdb->prefix}seo_master_rank_history
             ) h ON k.id = h.keyword_id AND h.rn = 1
             $where AND h.position IS NOT NULL"
        );

        return [
            'total_keywords' => (int) $total,
            'in_top_10' => (int) $top10,
            'improved' => (int) $improved,
            'average_position' => round((float) $avg_position, 1)
        ];
    }

    /**
     * AJAX handlers
     */
    public function ajax_check_rank() {
        check_ajax_referer('seo_master_nonce', 'nonce');
        
        $keyword_id = intval($_POST['keyword_id'] ?? 0);
        $result = $this->check_keyword_rank($keyword_id);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success($result);
    }

    public function ajax_add_keyword() {
        check_ajax_referer('seo_master_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        $result = $this->add_keyword([
            'keyword' => $_POST['keyword'] ?? '',
            'target_url' => $_POST['target_url'] ?? '',
            'site_id' => $_POST['site_id'] ?? null,
            'country' => $_POST['country'] ?? 'us',
            'device' => $_POST['device'] ?? 'desktop'
        ]);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success($result);
    }

    public function ajax_remove_keyword() {
        check_ajax_referer('seo_master_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }

        $keyword_id = intval($_POST['keyword_id'] ?? 0);
        $this->remove_keyword($keyword_id);

        wp_send_json_success(['message' => 'Keyword removed']);
    }

    public function ajax_get_history() {
        check_ajax_referer('seo_master_nonce', 'nonce');

        $keyword_id = intval($_POST['keyword_id'] ?? 0);
        $days = intval($_POST['days'] ?? 30);

        $history = $this->get_history($keyword_id, $days);

        wp_send_json_success(['history' => $history]);
    }
}

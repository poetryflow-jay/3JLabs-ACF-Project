<?php
/**
 * Ranking Monitor Class
 *
 * Monitors keyword rankings 2-12 times per day
 * Tracks position changes over time
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Ranking_Monitor {

    /**
     * Monitoring frequency (times per day)
     */
    private $frequency;

    /**
     * Constructor
     */
    public function __construct() {
        $this->frequency = get_option('wp_bulk_seo_aeo_ranking_frequency', 2); // Default: 2 times per day
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Schedule monitoring cron
        add_action('wp_bulk_seo_aeo_ranking_monitor', [$this, 'run_monitoring']);
        
        // Schedule based on frequency
        $this->schedule_monitoring();
    }

    /**
     * Schedule monitoring based on frequency
     */
    private function schedule_monitoring() {
        // Clear existing schedules
        wp_clear_scheduled_hook('wp_bulk_seo_aeo_ranking_monitor');

        // Calculate interval based on frequency (2-12 times per day)
        $frequency = max(2, min(12, $this->frequency));
        $interval_hours = 24 / $frequency;

        // Schedule recurring event
        if (!wp_next_scheduled('wp_bulk_seo_aeo_ranking_monitor')) {
            wp_schedule_event(time(), 'hourly', 'wp_bulk_seo_aeo_ranking_monitor');
        }
    }

    /**
     * Run monitoring
     */
    public function run_monitoring() {
        // Get keywords to monitor
        $keywords = $this->get_keywords_to_monitor();

        if (empty($keywords)) {
            return;
        }

        // Calculate how many to check this run (based on frequency)
        $frequency = max(2, min(12, $this->frequency));
        $per_run = ceil(count($keywords) / $frequency);
        $keywords_to_check = array_slice($keywords, 0, $per_run);

        foreach ($keywords_to_check as $keyword_data) {
            $this->check_keyword_ranking($keyword_data);
        }
    }

    /**
     * Get keywords to monitor
     *
     * @return array Keywords
     */
    private function get_keywords_to_monitor() {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_keywords';

        // Get active keywords
        $keywords = $wpdb->get_results(
            "SELECT * FROM $table WHERE is_active = 1 ORDER BY priority DESC, keyword ASC",
            ARRAY_A
        );

        return $keywords;
    }

    /**
     * Check keyword ranking
     *
     * @param array $keyword_data Keyword data
     * @return array|WP_Error Result
     */
    public function check_keyword_ranking($keyword_data) {
        $keyword = $keyword_data['keyword'] ?? '';
        $target_url = $keyword_data['target_url'] ?? '';

        if (empty($keyword)) {
            return new WP_Error('no_keyword', 'Keyword not provided');
        }

        // Use Google Search Console API if available
        $gsc = new WP_Bulk_SEO_AEO_Google_Search_Console();
        
        if ($gsc->is_configured()) {
            $ranking = $this->get_ranking_from_gsc($gsc, $keyword, $target_url);
        } else {
            // Fallback: Use extension data or estimate
            $ranking = $this->get_ranking_from_extension($keyword, $target_url);
        }

        // Save ranking
        if ($ranking) {
            $this->save_ranking($keyword_data['id'], $keyword, $target_url, $ranking);
        }

        return $ranking;
    }

    /**
     * Get ranking from Google Search Console
     *
     * @param WP_Bulk_SEO_AEO_Google_Search_Console $gsc GSC instance
     * @param string $keyword Keyword
     * @param string $target_url Target URL
     * @return array|WP_Error Ranking data
     */
    private function get_ranking_from_gsc($gsc, $keyword, $target_url) {
        $params = [
            'startDate' => date('Y-m-d', strtotime('-7 days')),
            'endDate' => date('Y-m-d'),
            'dimensions' => ['query', 'page'],
            'dimensionFilterGroups' => [
                [
                    'filters' => [
                        [
                            'dimension' => 'query',
                            'expression' => $keyword,
                        ],
                    ],
                ],
            ],
        ];

        $data = $gsc->get_search_analytics($params);

        if (is_wp_error($data)) {
            return $data;
        }

        // Find position for target URL
        $position = null;
        if (isset($data['rows'])) {
            foreach ($data['rows'] as $row) {
                if (isset($row['keys'][1]) && strpos($row['keys'][1], $target_url) !== false) {
                    $position = $row['position'] ?? null;
                    break;
                }
            }
        }

        return [
            'keyword' => $keyword,
            'url' => $target_url,
            'position' => $position,
            'source' => 'gsc',
            'checked_at' => current_time('mysql'),
        ];
    }

    /**
     * Get ranking from extension data
     *
     * @param string $keyword Keyword
     * @param string $target_url Target URL
     * @return array|null Ranking data
     */
    private function get_ranking_from_extension($keyword, $target_url) {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_rankings';

        $ranking = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table 
            WHERE keyword = %s 
            AND url = %s 
            ORDER BY tracked_at DESC 
            LIMIT 1",
            $keyword,
            $target_url
        ), ARRAY_A);

        if ($ranking) {
            return [
                'keyword' => $keyword,
                'url' => $target_url,
                'position' => $ranking['position'],
                'source' => 'extension',
                'checked_at' => current_time('mysql'),
            ];
        }

        return null;
    }

    /**
     * Save ranking data
     *
     * @param int $keyword_id Keyword ID
     * @param string $keyword Keyword
     * @param string $url URL
     * @param array $ranking_data Ranking data
     */
    private function save_ranking($keyword_id, $keyword, $url, $ranking_data) {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_ranking_history';

        $wpdb->insert($table, [
            'keyword_id' => $keyword_id,
            'keyword' => $keyword,
            'url' => $url,
            'position' => intval($ranking_data['position'] ?? 0),
            'source' => sanitize_text_field($ranking_data['source'] ?? 'unknown'),
            'checked_at' => current_time('mysql'),
            'time_of_day' => $this->get_time_of_day(),
        ], [
            '%d', '%s', '%s', '%d', '%s', '%s', '%s',
        ]);
    }

    /**
     * Get time of day label
     *
     * @return string Time of day
     */
    private function get_time_of_day() {
        $hour = (int)date('G');
        
        if ($hour >= 6 && $hour < 12) return 'morning';
        if ($hour >= 12 && $hour < 18) return 'afternoon';
        if ($hour >= 18 && $hour < 24) return 'evening';
        return 'night';
    }

    /**
     * Get ranking history
     *
     * @param int $keyword_id Keyword ID
     * @param int $days Number of days
     * @return array History
     */
    public function get_ranking_history($keyword_id, $days = 30) {
        global $wpdb;
        $table = $wpdb->prefix . 'bulk_seo_aeo_ranking_history';

        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $history = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
            WHERE keyword_id = %d 
            AND checked_at >= %s 
            ORDER BY checked_at ASC",
            $keyword_id,
            $cutoff
        ), ARRAY_A);

        return $history;
    }

    /**
     * Get ranking trends
     *
     * @param int $keyword_id Keyword ID
     * @return array Trends
     */
    public function get_ranking_trends($keyword_id) {
        $history = $this->get_ranking_history($keyword_id, 30);

        if (empty($history)) {
            return [];
        }

        $trends = [
            'current_position' => end($history)['position'] ?? null,
            'best_position' => min(array_column($history, 'position')),
            'worst_position' => max(array_column($history, 'position')),
            'average_position' => round(array_sum(array_column($history, 'position')) / count($history), 1),
            'trend' => $this->calculate_trend($history),
            'by_time_of_day' => $this->group_by_time_of_day($history),
        ];

        return $trends;
    }

    /**
     * Calculate trend (improving/declining/stable)
     *
     * @param array $history History data
     * @return string Trend
     */
    private function calculate_trend($history) {
        if (count($history) < 2) {
            return 'stable';
        }

        $recent = array_slice($history, -7); // Last 7 checks
        $older = array_slice($history, 0, 7); // First 7 checks

        $recent_avg = array_sum(array_column($recent, 'position')) / count($recent);
        $older_avg = array_sum(array_column($older, 'position')) / count($older);

        $diff = $older_avg - $recent_avg;

        if ($diff > 2) return 'improving';
        if ($diff < -2) return 'declining';
        return 'stable';
    }

    /**
     * Group rankings by time of day
     *
     * @param array $history History data
     * @return array Grouped data
     */
    private function group_by_time_of_day($history) {
        $grouped = [
            'morning' => [],
            'afternoon' => [],
            'evening' => [],
            'night' => [],
        ];

        foreach ($history as $entry) {
            $time = $entry['time_of_day'] ?? 'unknown';
            if (isset($grouped[$time])) {
                $grouped[$time][] = $entry['position'];
            }
        }

        // Calculate averages
        foreach ($grouped as $time => $positions) {
            if (!empty($positions)) {
                $grouped[$time] = [
                    'average' => round(array_sum($positions) / count($positions), 1),
                    'count' => count($positions),
                ];
            } else {
                $grouped[$time] = ['average' => null, 'count' => 0];
            }
        }

        return $grouped;
    }

    /**
     * Set monitoring frequency
     *
     * @param int $frequency Times per day (2-12)
     */
    public function set_frequency($frequency) {
        $frequency = max(2, min(12, intval($frequency)));
        update_option('wp_bulk_seo_aeo_ranking_frequency', $frequency);
        $this->frequency = $frequency;
        $this->schedule_monitoring();
    }
}

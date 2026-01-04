<?php
/**
 * Google Analytics API Integration
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Google_Analytics {

    /**
     * API Base URL
     */
    private const API_BASE = 'https://analyticsreporting.googleapis.com/v4';

    /**
     * OAuth Token
     */
    private $access_token;

    /**
     * View ID
     */
    private $view_id;

    /**
     * Constructor
     */
    public function __construct() {
        $this->access_token = get_option('wp_bulk_seo_aeo_ga_token', '');
        $this->view_id = get_option('wp_bulk_seo_aeo_ga_view_id', '');
    }

    /**
     * Set access token
     */
    public function set_access_token($token) {
        $this->access_token = $token;
        update_option('wp_bulk_seo_aeo_ga_token', $token);
    }

    /**
     * Set view ID
     */
    public function set_view_id($view_id) {
        $this->view_id = $view_id;
        update_option('wp_bulk_seo_aeo_ga_view_id', $view_id);
    }

    /**
     * Get page views and engagement metrics
     *
     * @param string $url Page URL (optional)
     * @param array $date_range Date range ['start' => 'YYYY-MM-DD', 'end' => 'YYYY-MM-DD']
     * @return array|WP_Error
     */
    public function get_page_metrics($url = '', $date_range = []) {
        if (empty($this->access_token) || empty($this->view_id)) {
            return new WP_Error('no_credentials', 'Google Analytics credentials not configured');
        }

        $default_range = [
            'start' => date('Y-m-d', strtotime('-30 days')),
            'end' => date('Y-m-d'),
        ];
        $date_range = wp_parse_args($date_range, $default_range);

        $request_body = [
            'reportRequests' => [
                [
                    'viewId' => $this->view_id,
                    'dateRanges' => [
                        [
                            'startDate' => $date_range['start'],
                            'endDate' => $date_range['end'],
                        ],
                    ],
                    'metrics' => [
                        ['expression' => 'ga:pageviews'],
                        ['expression' => 'ga:uniquePageviews'],
                        ['expression' => 'ga:avgTimeOnPage'],
                        ['expression' => 'ga:bounceRate'],
                        ['expression' => 'ga:exitRate'],
                    ],
                    'dimensions' => [
                        ['name' => 'ga:pagePath'],
                    ],
                ],
            ],
        ];

        // Filter by URL if provided
        if (!empty($url)) {
            $page_path = parse_url($url, PHP_URL_PATH);
            $request_body['reportRequests'][0]['dimensionFilterClauses'] = [
                'filters' => [
                    [
                        'dimensionName' => 'ga:pagePath',
                        'operator' => 'EXACT',
                        'expressions' => [$page_path],
                    ],
                ],
            ];
        }

        $url = self::API_BASE . '/reports:batchGet';

        $response = wp_remote_post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($request_body),
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            return new WP_Error('api_error', $data['error']['message']);
        }

        return $this->parse_reports($data);
    }

    /**
     * Parse Analytics reports
     *
     * @param array $data Raw API response
     * @return array Parsed data
     */
    private function parse_reports($data) {
        $parsed = [];

        if (isset($data['reports'][0]['data']['rows'])) {
            foreach ($data['reports'][0]['data']['rows'] as $row) {
                $page_path = $row['dimensions'][0] ?? '';
                $metrics = $row['metrics'][0]['values'] ?? [];

                $parsed[] = [
                    'page_path' => $page_path,
                    'pageviews' => (int)($metrics[0] ?? 0),
                    'unique_pageviews' => (int)($metrics[1] ?? 0),
                    'avg_time_on_page' => (float)($metrics[2] ?? 0),
                    'bounce_rate' => (float)($metrics[3] ?? 0),
                    'exit_rate' => (float)($metrics[4] ?? 0),
                ];
            }
        }

        return $parsed;
    }

    /**
     * Get user engagement metrics
     *
     * @param array $date_range Date range
     * @return array|WP_Error
     */
    public function get_engagement_metrics($date_range = []) {
        if (empty($this->access_token) || empty($this->view_id)) {
            return new WP_Error('no_credentials', 'Google Analytics credentials not configured');
        }

        $default_range = [
            'start' => date('Y-m-d', strtotime('-30 days')),
            'end' => date('Y-m-d'),
        ];
        $date_range = wp_parse_args($date_range, $default_range);

        $request_body = [
            'reportRequests' => [
                [
                    'viewId' => $this->view_id,
                    'dateRanges' => [
                        [
                            'startDate' => $date_range['start'],
                            'endDate' => $date_range['end'],
                        ],
                    ],
                    'metrics' => [
                        ['expression' => 'ga:sessions'],
                        ['expression' => 'ga:users'],
                        ['expression' => 'ga:avgSessionDuration'],
                        ['expression' => 'ga:bounceRate'],
                        ['expression' => 'ga:pageviewsPerSession'],
                    ],
                ],
            ],
        ];

        $url = self::API_BASE . '/reports:batchGet';

        $response = wp_remote_post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($request_body),
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            return new WP_Error('api_error', $data['error']['message']);
        }

        if (isset($data['reports'][0]['data']['totals'][0]['values'])) {
            $values = $data['reports'][0]['data']['totals'][0]['values'];
            return [
                'sessions' => (int)($values[0] ?? 0),
                'users' => (int)($values[1] ?? 0),
                'avg_session_duration' => (float)($values[2] ?? 0),
                'bounce_rate' => (float)($values[3] ?? 0),
                'pageviews_per_session' => (float)($values[4] ?? 0),
            ];
        }

        return [];
    }

    /**
     * Check if API is configured
     *
     * @return bool
     */
    public function is_configured() {
        return !empty($this->access_token) && !empty($this->view_id);
    }
}

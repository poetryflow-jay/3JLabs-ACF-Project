<?php
/**
 * WP Bulk SEO - Google Search Console API Integration
 *
 * Google Search Console API v1 통합
 * - 검색 성과 데이터 (CTR, 노출수, 클릭수, 평균 순위)
 * - 인덱싱 상태
 * - URL 검사
 * - 코어 웹 바이탈 데이터
 *
 * @package WP_Bulk_SEO
 * @subpackage API
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_Google_Search_Console {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * OAuth client
     */
    private $client;

    /**
     * Service instance
     */
    private $service;

    /**
     * Access token
     */
    private $access_token;

    /**
     * Cache duration (seconds)
     */
    private const CACHE_DURATION = 3600; // 1 hour

    /**
     * Get singleton instance
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_oauth();
    }

    /**
     * Initialize OAuth client
     */
    private function init_oauth() {
        // Check if Google API Client library is available
        if (!class_exists('Google_Client')) {
            // Try to load from vendor directory
            $vendor_path = WP_BULK_SEO_PLUGIN_DIR . 'vendor/autoload.php';
            if (file_exists($vendor_path)) {
                require_once $vendor_path;
            }
        }

        if (!class_exists('Google_Client')) {
            return; // Library not available
        }

        $client_id = get_option('wp_bulk_seo_gsc_client_id', '');
        $client_secret = get_option('wp_bulk_seo_gsc_client_secret', '');
        $redirect_uri = admin_url('admin.php?page=wp-bulk-seo-settings&tab=search_console');

        if (empty($client_id) || empty($client_secret)) {
            return;
        }

        $this->client = new Google_Client();
        $this->client->setClientId($client_id);
        $this->client->setClientSecret($client_secret);
        $this->client->setRedirectUri($redirect_uri);
        $this->client->addScope('https://www.googleapis.com/auth/webmasters.readonly');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');

        // Get stored token
        $token = get_option('wp_bulk_seo_gsc_token', '');
        if (!empty($token)) {
            $this->client->setAccessToken($token);
            if ($this->client->isAccessTokenExpired()) {
                $this->refresh_token();
            }
        }

        $this->service = new Google_Service_SearchConsole($this->client);
    }

    /**
     * Refresh access token
     */
    private function refresh_token() {
        if (!$this->client) return false;

        try {
            $refresh_token = $this->client->getRefreshToken();
            if ($refresh_token) {
                $this->client->refreshToken($refresh_token);
                $new_token = $this->client->getAccessToken();
                update_option('wp_bulk_seo_gsc_token', $new_token);
                return true;
            }
        } catch (Exception $e) {
            error_log('GSC Token Refresh Error: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Get authorization URL
     */
    public function get_auth_url() {
        if (!$this->client) {
            return '';
        }

        return $this->client->createAuthUrl();
    }

    /**
     * Handle OAuth callback
     */
    public function handle_oauth_callback($code) {
        if (!$this->client) {
            return false;
        }

        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            if (isset($token['error'])) {
                return false;
            }

            update_option('wp_bulk_seo_gsc_token', $token);
            $this->client->setAccessToken($token);

            // Get site URL
            $site_url = get_site_url();
            $this->verify_site($site_url);

            return true;
        } catch (Exception $e) {
            error_log('GSC OAuth Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify site in Search Console
     */
    public function verify_site($site_url) {
        if (!$this->service) return false;

        try {
            $site_entry = new Google_Service_SearchConsole_Site();
            $site_entry->setSiteUrl($site_url);
            $this->service->sites->add($site_entry);
            return true;
        } catch (Exception $e) {
            // Site might already be verified
            return false;
        }
    }

    /**
     * Get search analytics data
     *
     * @param string $start_date Start date (YYYY-MM-DD)
     * @param string $end_date End date (YYYY-MM-DD)
     * @param array $dimensions Dimensions to group by (query, page, device, country, date)
     * @param int $row_limit Maximum rows to return
     * @return array Search analytics data
     */
    public function get_search_analytics($start_date, $end_date, $dimensions = ['query'], $row_limit = 1000) {
        if (!$this->service) {
            return ['error' => 'Search Console not connected'];
        }

        $site_url = get_site_url();
        $cache_key = 'wp_bulk_seo_gsc_' . md5($site_url . $start_date . $end_date . implode(',', $dimensions) . $row_limit);
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        try {
            $request = new Google_Service_SearchConsole_SearchAnalyticsQueryRequest();
            $request->setStartDate($start_date);
            $request->setEndDate($end_date);
            $request->setDimensions($dimensions);
            $request->setRowLimit($row_limit);

            $response = $this->service->searchanalytics->query($site_url, $request);

            $data = [
                'rows' => [],
                'response_aggregation_type' => $response->getResponseAggregationType(),
            ];

            foreach ($response->getRows() as $row) {
                $data['rows'][] = [
                    'keys' => $row->getKeys(),
                    'clicks' => $row->getClicks(),
                    'impressions' => $row->getImpressions(),
                    'ctr' => $row->getCtr(),
                    'position' => $row->getPosition(),
                ];
            }

            set_transient($cache_key, $data, self::CACHE_DURATION);

            return $data;
        } catch (Exception $e) {
            error_log('GSC Search Analytics Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get URL inspection data
     *
     * @param string $url URL to inspect
     * @return array Inspection data
     */
    public function inspect_url($url) {
        if (!$this->service) {
            return ['error' => 'Search Console not connected'];
        }

        $cache_key = 'wp_bulk_seo_gsc_inspect_' . md5($url);
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        try {
            $request = new Google_Service_SearchConsole_UrlInspection_Index_UrlInspectionRequest();
            $request->setInspectionUrl($url);
            $request->setSiteUrl(get_site_url());
            $request->setLanguageCode('ko');

            $response = $this->service->urlInspection_index->inspect($request);

            $data = [
                'inspection_result' => [
                    'index_status_result' => [
                        'verdict' => $response->getInspectionResult()->getIndexStatusResult()->getVerdict(),
                        'coverage_state' => $response->getInspectionResult()->getIndexStatusResult()->getCoverageState(),
                        'last_crawl_time' => $response->getInspectionResult()->getIndexStatusResult()->getLastCrawlTime(),
                        'page_fetch_state' => $response->getInspectionResult()->getIndexStatusResult()->getPageFetchState(),
                    ],
                    'amp_result' => $response->getInspectionResult()->getAmpResult(),
                    'mobile_usability_result' => $response->getInspectionResult()->getMobileUsabilityResult(),
                    'rich_results_result' => $response->getInspectionResult()->getRichResultsResult(),
                ],
            ];

            set_transient($cache_key, $data, 7200); // Cache for 2 hours

            return $data;
        } catch (Exception $e) {
            error_log('GSC URL Inspection Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get Core Web Vitals data
     *
     * @param string $start_date Start date
     * @param string $end_date End date
     * @return array Core Web Vitals data
     */
    public function get_core_web_vitals($start_date, $end_date) {
        if (!$this->service) {
            return ['error' => 'Search Console not connected'];
        }

        $site_url = get_site_url();
        $cache_key = 'wp_bulk_seo_gsc_cwv_' . md5($site_url . $start_date . $end_date);
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        try {
            $request = new Google_Service_SearchConsole_UrlCrawlErrorsCountsQueryRequest();
            $request->setStartDate($start_date);
            $request->setEndDate($end_date);

            // Note: Core Web Vitals API might be different
            // This is a placeholder - actual implementation depends on GSC API version
            $data = [
                'lcp' => [],
                'fid' => [],
                'cls' => [],
            ];

            set_transient($cache_key, $data, self::CACHE_DURATION);

            return $data;
        } catch (Exception $e) {
            error_log('GSC Core Web Vitals Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get page-specific search data
     *
     * @param string $page_url Page URL
     * @param string $start_date Start date
     * @param string $end_date End date
     * @return array Page search data
     */
    public function get_page_search_data($page_url, $start_date, $end_date) {
        $analytics = $this->get_search_analytics($start_date, $end_date, ['query'], 100);

        if (isset($analytics['error'])) {
            return $analytics;
        }

        // Filter by page URL
        $page_data = [];
        foreach ($analytics['rows'] as $row) {
            // Match page URL in keys
            if (isset($row['keys'][1]) && strpos($row['keys'][1], $page_url) !== false) {
                $page_data[] = $row;
            }
        }

        return [
            'page_url' => $page_url,
            'total_clicks' => array_sum(array_column($page_data, 'clicks')),
            'total_impressions' => array_sum(array_column($page_data, 'impressions')),
            'avg_ctr' => count($page_data) > 0 ? array_sum(array_column($page_data, 'ctr')) / count($page_data) : 0,
            'avg_position' => count($page_data) > 0 ? array_sum(array_column($page_data, 'position')) / count($page_data) : 0,
            'top_queries' => array_slice($page_data, 0, 10),
        ];
    }

    /**
     * Check if connected
     */
    public function is_connected() {
        if (!$this->client) return false;

        $token = get_option('wp_bulk_seo_gsc_token', '');
        if (empty($token)) return false;

        $this->client->setAccessToken($token);
        return !$this->client->isAccessTokenExpired();
    }

    /**
     * Disconnect
     */
    public function disconnect() {
        delete_option('wp_bulk_seo_gsc_token');
        delete_option('wp_bulk_seo_gsc_client_id');
        delete_option('wp_bulk_seo_gsc_client_secret');
    }
}

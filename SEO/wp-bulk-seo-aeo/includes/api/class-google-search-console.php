<?php
/**
 * Google Search Console API Integration
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_Google_Search_Console {

    /**
     * API Base URL
     */
    private const API_BASE = 'https://www.googleapis.com/webmasters/v3';

    /**
     * OAuth Token
     */
    private $access_token;

    /**
     * Site URL
     */
    private $site_url;

    /**
     * Constructor
     */
    public function __construct() {
        $this->access_token = get_option('wp_bulk_seo_aeo_gsc_token', '');
        $this->site_url = get_option('wp_bulk_seo_aeo_gsc_site_url', '');
    }

    /**
     * Set access token
     */
    public function set_access_token($token) {
        $this->access_token = $token;
        update_option('wp_bulk_seo_aeo_gsc_token', $token);
    }

    /**
     * Set site URL
     */
    public function set_site_url($url) {
        $this->site_url = $url;
        update_option('wp_bulk_seo_aeo_gsc_site_url', $url);
    }

    /**
     * Get search analytics data
     *
     * @param array $params Query parameters
     * @return array|WP_Error
     */
    public function get_search_analytics($params = []) {
        if (empty($this->access_token) || empty($this->site_url)) {
            return new WP_Error('no_credentials', 'Google Search Console credentials not configured');
        }

        $defaults = [
            'startDate' => date('Y-m-d', strtotime('-30 days')),
            'endDate' => date('Y-m-d'),
            'dimensions' => ['query', 'page'],
            'rowLimit' => 1000,
        ];

        $params = wp_parse_args($params, $defaults);
        $site_url = urlencode($this->site_url);

        $url = self::API_BASE . "/sites/{$site_url}/searchAnalytics/query";

        $response = wp_remote_post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($params),
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

        return $data;
    }

    /**
     * Get URL inspection data
     *
     * @param string $url URL to inspect
     * @return array|WP_Error
     */
    public function inspect_url($url) {
        if (empty($this->access_token) || empty($this->site_url)) {
            return new WP_Error('no_credentials', 'Google Search Console credentials not configured');
        }

        $site_url = urlencode($this->site_url);
        $inspect_url = urlencode($url);

        $url = self::API_BASE . "/urlInspection/index:inspect?inspectionUrl={$inspect_url}";

        $response = wp_remote_get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
            ],
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

        return $data;
    }

    /**
     * Get sitemap list
     *
     * @return array|WP_Error
     */
    public function get_sitemaps() {
        if (empty($this->access_token) || empty($this->site_url)) {
            return new WP_Error('no_credentials', 'Google Search Console credentials not configured');
        }

        $site_url = urlencode($this->site_url);
        $url = self::API_BASE . "/sites/{$site_url}/sitemaps";

        $response = wp_remote_get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
            ],
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

        return $data;
    }

    /**
     * Check if API is configured
     *
     * @return bool
     */
    public function is_configured() {
        return !empty($this->access_token) && !empty($this->site_url);
    }
}

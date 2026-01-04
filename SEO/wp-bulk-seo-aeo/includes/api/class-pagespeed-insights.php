<?php
/**
 * Google PageSpeed Insights API v5 Integration
 *
 * @package WP_Bulk_SEO_AEO
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Bulk_SEO_AEO_PageSpeed_Insights {

    /**
     * API Base URL
     */
    private const API_BASE = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

    /**
     * API Key
     */
    private $api_key;

    /**
     * Constructor
     */
    public function __construct() {
        $this->api_key = get_option('wp_bulk_seo_aeo_pagespeed_api_key', '');
    }

    /**
     * Set API key
     */
    public function set_api_key($key) {
        $this->api_key = $key;
        update_option('wp_bulk_seo_aeo_pagespeed_api_key', $key);
    }

    /**
     * Analyze URL with PageSpeed Insights
     *
     * @param string $url URL to analyze
     * @param string $strategy 'desktop' or 'mobile'
     * @param array $categories Categories to analyze
     * @return array|WP_Error
     */
    public function analyze_url($url, $strategy = 'mobile', $categories = ['performance', 'seo', 'accessibility', 'best-practices']) {
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', 'PageSpeed Insights API key not configured');
        }

        $params = [
            'url' => $url,
            'strategy' => $strategy,
            'category' => $categories,
            'key' => $this->api_key,
        ];

        $api_url = add_query_arg($params, self::API_BASE);

        $response = wp_remote_get($api_url, [
            'timeout' => 60,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            return new WP_Error('api_error', $data['error']['message']);
        }

        return $this->parse_response($data);
    }

    /**
     * Parse PageSpeed API response
     *
     * @param array $data Raw API response
     * @return array Parsed data
     */
    private function parse_response($data) {
        $parsed = [
            'url' => $data['id'] ?? '',
            'strategy' => $data['loadingExperience'] ? 'mobile' : 'desktop',
            'timestamp' => current_time('mysql'),
        ];

        // Lighthouse Result
        if (isset($data['lighthouseResult'])) {
            $lighthouse = $data['lighthouseResult'];

            // Categories scores
            if (isset($lighthouse['categories'])) {
                foreach ($lighthouse['categories'] as $category => $info) {
                    $parsed['lighthouse_scores'][$category] = [
                        'score' => isset($info['score']) ? round($info['score'] * 100) : 0,
                        'title' => $info['title'] ?? '',
                    ];
                }
            }

            // Core Web Vitals from Audits
            if (isset($lighthouse['audits'])) {
                $audits = $lighthouse['audits'];

                // LCP (Largest Contentful Paint)
                if (isset($audits['largest-contentful-paint'])) {
                    $parsed['core_web_vitals']['lcp'] = [
                        'value' => $audits['largest-contentful-paint']['numericValue'] ?? 0,
                        'score' => $audits['largest-contentful-paint']['score'] ?? 0,
                        'displayValue' => $audits['largest-contentful-paint']['displayValue'] ?? '',
                    ];
                }

                // FID (First Input Delay) - Note: FID is now INP in Lighthouse
                if (isset($audits['interactive'])) {
                    $parsed['core_web_vitals']['fid'] = [
                        'value' => $audits['interactive']['numericValue'] ?? 0,
                        'score' => $audits['interactive']['score'] ?? 0,
                        'displayValue' => $audits['interactive']['displayValue'] ?? '',
                    ];
                }

                // CLS (Cumulative Layout Shift)
                if (isset($audits['cumulative-layout-shift'])) {
                    $parsed['core_web_vitals']['cls'] = [
                        'value' => $audits['cumulative-layout-shift']['numericValue'] ?? 0,
                        'score' => $audits['cumulative-layout-shift']['score'] ?? 0,
                        'displayValue' => $audits['cumulative-layout-shift']['displayValue'] ?? '',
                    ];
                }

                // FCP (First Contentful Paint)
                if (isset($audits['first-contentful-paint'])) {
                    $parsed['core_web_vitals']['fcp'] = [
                        'value' => $audits['first-contentful-paint']['numericValue'] ?? 0,
                        'score' => $audits['first-contentful-paint']['score'] ?? 0,
                        'displayValue' => $audits['first-contentful-paint']['displayValue'] ?? '',
                    ];
                }

                // TTFB (Time to First Byte)
                if (isset($audits['server-response-time'])) {
                    $parsed['core_web_vitals']['ttfb'] = [
                        'value' => $audits['server-response-time']['numericValue'] ?? 0,
                        'score' => $audits['server-response-time']['score'] ?? 0,
                        'displayValue' => $audits['server-response-time']['displayValue'] ?? '',
                    ];
                }

                // TBT (Total Blocking Time)
                if (isset($audits['total-blocking-time'])) {
                    $parsed['core_web_vitals']['tbt'] = [
                        'value' => $audits['total-blocking-time']['numericValue'] ?? 0,
                        'score' => $audits['total-blocking-time']['score'] ?? 0,
                        'displayValue' => $audits['total-blocking-time']['displayValue'] ?? '',
                    ];
                }
            }
        }

        // Loading Experience (Real User Metrics)
        if (isset($data['loadingExperience'])) {
            $loading = $data['loadingExperience'];

            if (isset($loading['metrics'])) {
                foreach ($loading['metrics'] as $metric => $data) {
                    $parsed['loading_experience'][$metric] = [
                        'percentile' => $data['percentile'] ?? 0,
                        'median' => $data['median'] ?? 0,
                        'distributions' => $data['distributions'] ?? [],
                    ];
                }
            }

            $parsed['loading_experience']['overall_category'] = $loading['overall_category'] ?? 'UNKNOWN';
        }

        // Origin Loading Experience
        if (isset($data['originLoadingExperience'])) {
            $origin = $data['originLoadingExperience'];

            if (isset($origin['metrics'])) {
                foreach ($origin['metrics'] as $metric => $data) {
                    $parsed['origin_loading_experience'][$metric] = [
                        'percentile' => $data['percentile'] ?? 0,
                        'median' => $data['median'] ?? 0,
                        'distributions' => $data['distributions'] ?? [],
                    ];
                }
            }

            $parsed['origin_loading_experience']['overall_category'] = $origin['overall_category'] ?? 'UNKNOWN';
        }

        return $parsed;
    }

    /**
     * Get Core Web Vitals scores
     *
     * @param string $url URL to analyze
     * @return array|WP_Error
     */
    public function get_core_web_vitals($url) {
        $result = $this->analyze_url($url, 'mobile', ['performance']);

        if (is_wp_error($result)) {
            return $result;
        }

        return [
            'lcp' => $result['core_web_vitals']['lcp'] ?? null,
            'fid' => $result['core_web_vitals']['fid'] ?? null,
            'cls' => $result['core_web_vitals']['cls'] ?? null,
            'fcp' => $result['core_web_vitals']['fcp'] ?? null,
            'ttfb' => $result['core_web_vitals']['ttfb'] ?? null,
            'tbt' => $result['core_web_vitals']['tbt'] ?? null,
        ];
    }

    /**
     * Get Lighthouse scores
     *
     * @param string $url URL to analyze
     * @param string $strategy 'desktop' or 'mobile'
     * @return array|WP_Error
     */
    public function get_lighthouse_scores($url, $strategy = 'mobile') {
        $result = $this->analyze_url($url, $strategy);

        if (is_wp_error($result)) {
            return $result;
        }

        return $result['lighthouse_scores'] ?? [];
    }

    /**
     * Check if API is configured
     *
     * @return bool
     */
    public function is_configured() {
        return !empty($this->api_key);
    }
}

<?php
/**
 * 대시보드 관리자 클래스
 *
 * @package JJ_Marketing_Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 대시보드 관리 기능
 */
class JJ_Dashboard_Admin {

    /**
     * 생성자
     */
    public function __construct() {
        // 관리자 기능 초기화
    }

    /**
     * 대시보드 데이터 가져오기
     */
    public function get_dashboard_data() {
        return array(
            'analytics' => $this->get_analytics_summary(),
            'seo' => $this->get_seo_summary(),
            'campaigns' => $this->get_campaigns_summary(),
        );
    }

    /**
     * 분석 요약 가져오기
     */
    private function get_analytics_summary() {
        if ( class_exists( 'JJ_Analytics_Collector' ) ) {
            $collector = new JJ_Analytics_Collector();
            return $collector->get_comprehensive_stats();
        }
        return array();
    }

    /**
     * SEO 요약 가져오기
     */
    private function get_seo_summary() {
        if ( class_exists( 'JJ_SEO_Optimizer' ) ) {
            $seo = new JJ_SEO_Optimizer();
            return $seo->get_summary();
        }
        return array();
    }

    /**
     * 캠페인 요약 가져오기
     */
    private function get_campaigns_summary() {
        if ( class_exists( 'JJ_Campaign_Tracker' ) ) {
            $tracker = new JJ_Campaign_Tracker();
            return $tracker->get_campaign_stats();
        }
        return array();
    }
}

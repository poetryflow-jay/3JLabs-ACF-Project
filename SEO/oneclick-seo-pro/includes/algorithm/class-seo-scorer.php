<?php
/**
 * WP Bulk SEO - Unified SEO Scoring Engine
 *
 * Integrates ranking factors from:
 * - Google Algorithm Leak (May 2024) - NavBoost, siteAuthority, CompressedQualitySignals
 * - PageSpeed API v5 - Core Web Vitals, Lighthouse scores
 * - Search Console API - CTR, impressions, position data
 *
 * @package OneClick_SEO_Pro
 * @subpackage Algorithm
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class OneClick_SEO_Pro_Scorer {

    /**
     * Ranking factors database instance
     */
    private $factors_db;

    /**
     * Cached PageSpeed API data
     */
    private $pagespeed_cache = [];

    /**
     * Cached Search Console data
     */
    private $search_console_cache = [];

    /**
     * Priority multipliers
     */
    private const PRIORITY_MULTIPLIERS = [
        'P0' => 1.0,   // Critical
        'P1' => 0.85,  // High
        'P2' => 0.6,   // Medium
        'P3' => 0.35,  // Low
    ];

    /**
     * Module category mappings
     */
    private const MODULE_CATEGORIES = [
        'CoreWebVitals'     => 'Technical SEO',
        'Lighthouse'        => 'Technical SEO',
        'PageSpeed'         => 'Technical SEO',
        'NavBoost'          => 'User Engagement',
        'QualitySignals'    => 'Content Quality',
        'Links'             => 'Link Authority',
        'Topical'           => 'Topical Authority',
        'OnPage'            => 'On-Page SEO',
        'Schema'            => 'Structured Data',
        'EEAT'              => 'E-E-A-T',
        'Freshness'         => 'Content Freshness',
        'Mobile'            => 'Mobile SEO',
        'Security'          => 'Security',
        'Demotions'         => 'Risk Assessment',
        'AEO'               => 'AI Engine Optimization',
    ];

    /**
     * Constructor
     */
    public function __construct() {
        $this->factors_db = new OneClick_SEO_Pro_Ranking_Factors_DB();
    }

    /**
     * Calculate comprehensive SEO score for a page
     *
     * @param array $page_data Page analysis data from Analyzer
     * @param array $api_data Optional external API data (PageSpeed, Search Console)
     * @return array Comprehensive score report
     */
    public function calculate_score($page_data, $api_data = []) {
        $scores = [];
        $total_weighted_score = 0;
        $total_weight = 0;

        // Merge API data if available
        if (!empty($api_data['pagespeed'])) {
            $page_data = $this->merge_pagespeed_data($page_data, $api_data['pagespeed']);
        }
        if (!empty($api_data['search_console'])) {
            $page_data = $this->merge_search_console_data($page_data, $api_data['search_console']);
        }

        // Calculate scores by module
        $scores['core_web_vitals'] = $this->score_core_web_vitals($page_data);
        $scores['lighthouse'] = $this->score_lighthouse($page_data);
        $scores['technical'] = $this->score_technical($page_data);
        $scores['on_page'] = $this->score_on_page($page_data);
        $scores['content'] = $this->score_content($page_data);
        $scores['links'] = $this->score_links($page_data);
        $scores['topical'] = $this->score_topical($page_data);
        $scores['freshness'] = $this->score_freshness($page_data);
        $scores['eeat'] = $this->score_eeat($page_data);
        $scores['schema'] = $this->score_schema($page_data);
        $scores['engagement'] = $this->score_engagement($page_data);
        $scores['demotions'] = $this->score_demotions($page_data);
        $scores['aeo'] = $this->score_aeo($page_data);

        // Calculate overall weighted score
        foreach ($scores as $module => $module_data) {
            if (isset($module_data['score']) && isset($module_data['weight'])) {
                $weight = $module_data['weight'];
                $score = $module_data['score'];
                $total_weighted_score += $score * $weight;
                $total_weight += $weight;
            }
        }

        $overall_score = $total_weight > 0 ? round($total_weighted_score / $total_weight, 1) : 0;

        return [
            'overall_score' => $overall_score,
            'grade' => $this->calculate_grade($overall_score),
            'modules' => $scores,
            'recommendations' => $this->generate_recommendations($scores),
            'priority_actions' => $this->get_priority_actions($scores),
            'score_breakdown' => $this->get_score_breakdown($scores),
            'api_sources' => $this->get_api_sources_used($api_data),
            'calculated_at' => current_time('mysql'),
        ];
    }

    /**
     * Score Core Web Vitals (from PageSpeed API)
     */
    private function score_core_web_vitals($data) {
        $factors = $this->factors_db->get_module_factors('CoreWebVitals');
        $scored_factors = [];
        $total_score = 0;
        $total_weight = 0;

        foreach ($factors as $name => $config) {
            $value = $data[$this->get_data_key($name)] ?? null;
            $score = $this->score_cwv_metric($name, $value, $config);

            $scored_factors[] = [
                'name' => $name,
                'label' => $this->get_factor_label($name),
                'label_kr' => $this->get_factor_label_kr($name),
                'value' => $value,
                'score' => $score,
                'weight' => $config['weight'],
                'priority' => $this->get_priority_label($config['priority']),
                'threshold_good' => $config['threshold_good'] ?? null,
                'threshold_poor' => $config['threshold_poor'] ?? null,
                'status' => $this->get_cwv_status($value, $config),
            ];

            $weight = $config['weight'] * self::PRIORITY_MULTIPLIERS[$this->get_priority_label($config['priority'])];
            $total_score += $score * $weight;
            $total_weight += $weight;
        }

        return [
            'category' => 'Core Web Vitals',
            'category_kr' => '코어 웹 바이탈',
            'score' => $total_weight > 0 ? round($total_score / $total_weight, 1) : 0,
            'weight' => 9, // High priority module
            'factors' => $scored_factors,
            'api_source' => 'PageSpeed API v5',
        ];
    }

    /**
     * Score individual Core Web Vital metric
     */
    private function score_cwv_metric($name, $value, $config) {
        if ($value === null) {
            return 50; // Default mid-score when data unavailable
        }

        $good = $config['threshold_good'] ?? null;
        $poor = $config['threshold_poor'] ?? null;

        if ($good === null || $poor === null) {
            return min(100, max(0, $value));
        }

        // CLS uses lower-is-better (decimal)
        if ($name === 'CLS') {
            if ($value <= $good) return 100;
            if ($value >= $poor) return 0;
            return round(100 - (($value - $good) / ($poor - $good)) * 100);
        }

        // Other metrics: lower milliseconds = better
        if ($value <= $good) return 100;
        if ($value >= $poor) return 0;
        return round(100 - (($value - $good) / ($poor - $good)) * 100);
    }

    /**
     * Get CWV status (good/needs-improvement/poor)
     */
    private function get_cwv_status($value, $config) {
        if ($value === null) return 'unknown';

        $good = $config['threshold_good'] ?? null;
        $poor = $config['threshold_poor'] ?? null;

        if ($good === null) return 'unknown';

        if ($value <= $good) return 'good';
        if ($poor && $value >= $poor) return 'poor';
        return 'needs-improvement';
    }

    /**
     * Score Lighthouse metrics
     */
    private function score_lighthouse($data) {
        $factors = $this->factors_db->get_module_factors('Lighthouse');
        $scored_factors = [];
        $total_score = 0;
        $total_weight = 0;

        foreach ($factors as $name => $config) {
            $value = $data[$this->get_data_key($name)] ?? null;

            // Lighthouse scores are already 0-100
            $score = $value !== null ? min(100, max(0, $value)) : 50;

            $scored_factors[] = [
                'name' => $name,
                'label' => $this->get_factor_label($name),
                'label_kr' => $this->get_factor_label_kr($name),
                'value' => $value,
                'score' => $score,
                'weight' => $config['weight'],
                'priority' => $this->get_priority_label($config['priority']),
            ];

            $weight = $config['weight'] * self::PRIORITY_MULTIPLIERS[$this->get_priority_label($config['priority'])];
            $total_score += $score * $weight;
            $total_weight += $weight;
        }

        return [
            'category' => 'Lighthouse Scores',
            'category_kr' => '라이트하우스 점수',
            'score' => $total_weight > 0 ? round($total_score / $total_weight, 1) : 0,
            'weight' => 8,
            'factors' => $scored_factors,
            'api_source' => 'PageSpeed API v5',
        ];
    }

    /**
     * Score Technical SEO factors
     */
    private function score_technical($data) {
        $factors = [];

        // Indexability
        $indexable = $this->check_indexable($data);
        $factors[] = [
            'name' => 'indexable',
            'label' => 'Indexability',
            'label_kr' => '인덱싱 가능 여부',
            'score' => $indexable,
            'weight' => 10,
            'priority' => 'P0',
        ];

        // Crawl Status
        $crawl = $this->check_crawl_status($data);
        $factors[] = [
            'name' => 'crawl_status',
            'label' => 'Crawl Status',
            'label_kr' => '크롤링 상태',
            'score' => $crawl,
            'weight' => 9,
            'priority' => 'P0',
        ];

        // HTTPS
        $https = ($data['is_https'] ?? false) ? 100 : 0;
        $factors[] = [
            'name' => 'https',
            'label' => 'HTTPS Security',
            'label_kr' => 'HTTPS 보안',
            'score' => $https,
            'weight' => 8,
            'priority' => 'P0',
        ];

        // Mobile Viewport
        $viewport = ($data['has_mobile_viewport'] ?? false) ? 100 : 0;
        $factors[] = [
            'name' => 'mobile_viewport',
            'label' => 'Mobile Viewport',
            'label_kr' => '모바일 뷰포트',
            'score' => $viewport,
            'weight' => 8,
            'priority' => 'P0',
        ];

        // Canonical
        $canonical = $this->check_canonical($data);
        $factors[] = [
            'name' => 'canonical',
            'label' => 'Canonical URL',
            'label_kr' => '캐노니컬 URL',
            'score' => $canonical,
            'weight' => 8,
            'priority' => 'P0',
        ];

        // Robots Meta
        $robots = $this->check_robots_meta($data);
        $factors[] = [
            'name' => 'robots_meta',
            'label' => 'Robots Meta',
            'label_kr' => '로봇 메타 태그',
            'score' => $robots,
            'weight' => 9,
            'priority' => 'P0',
        ];

        // Hreflang (international)
        if (isset($data['has_hreflang'])) {
            $factors[] = [
                'name' => 'hreflang',
                'label' => 'Hreflang Tags',
                'label_kr' => 'Hreflang 태그',
                'score' => $data['has_hreflang'] ? 100 : 50,
                'weight' => 5,
                'priority' => 'P2',
            ];
        }

        return [
            'category' => 'Technical SEO',
            'category_kr' => '기술적 SEO',
            'score' => $this->calculate_module_score($factors),
            'weight' => 9,
            'factors' => $factors,
        ];
    }

    /**
     * Score On-Page SEO factors
     */
    private function score_on_page($data) {
        $factors = [];

        // Title Optimization
        $title_score = $this->score_title($data);
        $factors[] = [
            'name' => 'title',
            'label' => 'Title Optimization',
            'label_kr' => '제목 최적화',
            'score' => $title_score,
            'weight' => 9,
            'priority' => 'P0',
            'details' => $this->get_title_details($data),
        ];

        // Meta Description
        $desc_score = $this->score_meta_description($data);
        $factors[] = [
            'name' => 'meta_description',
            'label' => 'Meta Description',
            'label_kr' => '메타 설명',
            'score' => $desc_score,
            'weight' => 7,
            'priority' => 'P1',
            'details' => $this->get_meta_description_details($data),
        ];

        // Heading Structure
        $heading_score = $this->score_headings($data);
        $factors[] = [
            'name' => 'headings',
            'label' => 'Heading Structure',
            'label_kr' => '제목 구조',
            'score' => $heading_score,
            'weight' => 7,
            'priority' => 'P1',
        ];

        // Keyword Optimization
        $keyword_score = $this->score_keyword_usage($data);
        $factors[] = [
            'name' => 'keyword_usage',
            'label' => 'Keyword Usage',
            'label_kr' => '키워드 사용',
            'score' => $keyword_score,
            'weight' => 6,
            'priority' => 'P1',
        ];

        // Image Optimization
        $image_score = $this->score_images($data);
        $factors[] = [
            'name' => 'images',
            'label' => 'Image Optimization',
            'label_kr' => '이미지 최적화',
            'score' => $image_score,
            'weight' => 6,
            'priority' => 'P1',
        ];

        // URL Structure
        $url_score = $this->score_url_structure($data);
        $factors[] = [
            'name' => 'url_structure',
            'label' => 'URL Structure',
            'label_kr' => 'URL 구조',
            'score' => $url_score,
            'weight' => 5,
            'priority' => 'P2',
        ];

        return [
            'category' => 'On-Page SEO',
            'category_kr' => '온페이지 SEO',
            'score' => $this->calculate_module_score($factors),
            'weight' => 8,
            'factors' => $factors,
        ];
    }

    /**
     * Score Content Quality factors (CompressedQualitySignals)
     */
    private function score_content($data) {
        $factors = [];

        // Content Length
        $length_score = $this->score_content_length($data);
        $factors[] = [
            'name' => 'content_length',
            'label' => 'Content Length',
            'label_kr' => '콘텐츠 길이',
            'score' => $length_score,
            'weight' => 6,
            'priority' => 'P1',
            'value' => $data['word_count'] ?? 0,
        ];

        // Content Depth
        $depth_score = $this->score_content_depth($data);
        $factors[] = [
            'name' => 'content_depth',
            'label' => 'Content Depth',
            'label_kr' => '콘텐츠 깊이',
            'score' => $depth_score,
            'weight' => 7,
            'priority' => 'P1',
        ];

        // Readability
        $readability_score = $this->score_readability($data);
        $factors[] = [
            'name' => 'readability',
            'label' => 'Readability',
            'label_kr' => '가독성',
            'score' => $readability_score,
            'weight' => 6,
            'priority' => 'P2',
            'value' => $data['flesch_reading_ease'] ?? null,
        ];

        // Original Content (from Google's 0-512 scale)
        $originality_score = $this->score_originality($data);
        $factors[] = [
            'name' => 'originality',
            'label' => 'Content Originality',
            'label_kr' => '콘텐츠 원본성',
            'score' => $originality_score,
            'weight' => 9,
            'priority' => 'P0',
        ];

        // avgTermWeight (font emphasis from Google leak)
        if (isset($data['avg_term_weight'])) {
            $factors[] = [
                'name' => 'avg_term_weight',
                'label' => 'Term Weight',
                'label_kr' => '용어 가중치',
                'score' => min(100, $data['avg_term_weight']),
                'weight' => 5,
                'priority' => 'P2',
            ];
        }

        return [
            'category' => 'Content Quality',
            'category_kr' => '콘텐츠 품질',
            'score' => $this->calculate_module_score($factors),
            'weight' => 8,
            'factors' => $factors,
        ];
    }

    /**
     * Score Link factors
     */
    private function score_links($data) {
        $factors = [];

        // Internal Links
        $internal_score = $this->score_internal_links($data);
        $factors[] = [
            'name' => 'internal_links',
            'label' => 'Internal Linking',
            'label_kr' => '내부 링크',
            'score' => $internal_score,
            'weight' => 7,
            'priority' => 'P1',
            'count' => $data['internal_link_count'] ?? 0,
        ];

        // External Links
        $external_score = $this->score_external_links($data);
        $factors[] = [
            'name' => 'external_links',
            'label' => 'External Links',
            'label_kr' => '외부 링크',
            'score' => $external_score,
            'weight' => 6,
            'priority' => 'P2',
            'count' => $data['external_link_count'] ?? 0,
        ];

        // Anchor Text Quality
        $anchor_score = $this->score_anchor_quality($data);
        $factors[] = [
            'name' => 'anchor_quality',
            'label' => 'Anchor Text Quality',
            'label_kr' => '앵커 텍스트 품질',
            'score' => $anchor_score,
            'weight' => 7,
            'priority' => 'P1',
        ];

        // Backlink Diversity (if data available from external sources)
        if (isset($data['backlinks']) || isset($data['referring_domains'])) {
            $factors[] = [
                'name' => 'backlink_diversity',
                'label' => 'Backlink Diversity',
                'label_kr' => '백링크 다양성',
                'score' => $this->score_backlink_diversity($data),
                'weight' => 8,
                'priority' => 'P0',
            ];
        }

        return [
            'category' => 'Link Profile',
            'category_kr' => '링크 프로필',
            'score' => $this->calculate_module_score($factors),
            'weight' => 8,
            'factors' => $factors,
        ];
    }

    /**
     * Score Topical Authority (SiteTopics from Google leak)
     */
    private function score_topical($data) {
        $factors = [];

        // Topic Focus
        $focus_score = $this->score_topic_focus($data);
        $factors[] = [
            'name' => 'topic_focus',
            'label' => 'Topic Focus',
            'label_kr' => '주제 집중도',
            'score' => $focus_score,
            'weight' => 8,
            'priority' => 'P0',
        ];

        // Entity Coverage
        $entity_score = $this->score_entity_coverage($data);
        $factors[] = [
            'name' => 'entity_coverage',
            'label' => 'Entity Coverage',
            'label_kr' => '엔티티 커버리지',
            'score' => $entity_score,
            'weight' => 7,
            'priority' => 'P1',
        ];

        // Topic Relevance
        $relevance_score = $data['topic_relevance'] ?? 70;
        $factors[] = [
            'name' => 'topic_relevance',
            'label' => 'Topic Relevance',
            'label_kr' => '주제 관련성',
            'score' => $relevance_score,
            'weight' => 7,
            'priority' => 'P1',
        ];

        return [
            'category' => 'Topical Authority',
            'category_kr' => '주제별 권위',
            'score' => $this->calculate_module_score($factors),
            'weight' => 7,
            'factors' => $factors,
        ];
    }

    /**
     * Score Content Freshness
     */
    private function score_freshness($data) {
        $factors = [];

        // Last Significant Update
        $update_score = $this->score_last_update($data);
        $factors[] = [
            'name' => 'last_update',
            'label' => 'Last Significant Update',
            'label_kr' => '마지막 의미있는 업데이트',
            'score' => $update_score,
            'weight' => 8,
            'priority' => 'P0',
        ];

        // Date Clarity
        $date_score = $this->score_date_clarity($data);
        $factors[] = [
            'name' => 'date_clarity',
            'label' => 'Date Clarity',
            'label_kr' => '날짜 명확성',
            'score' => $date_score,
            'weight' => 7,
            'priority' => 'P1',
        ];

        return [
            'category' => 'Content Freshness',
            'category_kr' => '콘텐츠 신선도',
            'score' => $this->calculate_module_score($factors),
            'weight' => 7,
            'factors' => $factors,
        ];
    }

    /**
     * Score E-E-A-T signals
     */
    private function score_eeat($data) {
        $factors = [];

        // Author Information
        $author_score = $this->score_author_info($data);
        $factors[] = [
            'name' => 'author_info',
            'label' => 'Author Information',
            'label_kr' => '저자 정보',
            'score' => $author_score,
            'weight' => 7,
            'priority' => 'P1',
        ];

        // Expertise Signals
        $expertise_score = $this->score_expertise($data);
        $factors[] = [
            'name' => 'expertise',
            'label' => 'Expertise Signals',
            'label_kr' => '전문성 신호',
            'score' => $expertise_score,
            'weight' => 7,
            'priority' => 'P1',
        ];

        // Trust Signals
        $trust_score = $this->score_trust_signals($data);
        $factors[] = [
            'name' => 'trust_signals',
            'label' => 'Trust Signals',
            'label_kr' => '신뢰도 신호',
            'score' => $trust_score,
            'weight' => 8,
            'priority' => 'P1',
        ];

        return [
            'category' => 'E-E-A-T',
            'category_kr' => 'E-E-A-T',
            'score' => $this->calculate_module_score($factors),
            'weight' => 8,
            'factors' => $factors,
        ];
    }

    /**
     * Score Structured Data (Schema.org)
     */
    private function score_schema($data) {
        $factors = [];

        // Schema Presence
        $presence_score = ($data['has_schema'] ?? false) ? 100 : 0;
        $factors[] = [
            'name' => 'schema_presence',
            'label' => 'Schema.org Markup',
            'label_kr' => 'Schema.org 마크업',
            'score' => $presence_score,
            'weight' => 7,
            'priority' => 'P1',
            'types' => $data['schema_types'] ?? [],
        ];

        // Schema Validity
        $validity_score = ($data['schema_valid'] ?? false) ? 100 : 30;
        $factors[] = [
            'name' => 'schema_validity',
            'label' => 'Schema Validity',
            'label_kr' => '스키마 유효성',
            'score' => $validity_score,
            'weight' => 6,
            'priority' => 'P2',
        ];

        // Rich Results Eligibility
        $rich_score = ($data['rich_results_eligible'] ?? false) ? 100 : 40;
        $factors[] = [
            'name' => 'rich_results',
            'label' => 'Rich Results Eligible',
            'label_kr' => '리치 결과 적격성',
            'score' => $rich_score,
            'weight' => 6,
            'priority' => 'P2',
        ];

        // FAQ Schema (important for AEO)
        if (isset($data['has_faq_schema'])) {
            $factors[] = [
                'name' => 'faq_schema',
                'label' => 'FAQ Schema',
                'label_kr' => 'FAQ 스키마',
                'score' => $data['has_faq_schema'] ? 100 : 0,
                'weight' => 6,
                'priority' => 'P1',
            ];
        }

        return [
            'category' => 'Structured Data',
            'category_kr' => '구조화 데이터',
            'score' => $this->calculate_module_score($factors),
            'weight' => 6,
            'factors' => $factors,
        ];
    }

    /**
     * Score User Engagement (NavBoost proxy)
     */
    private function score_engagement($data) {
        $factors = [];

        // CTR Potential (estimated or from Search Console)
        $ctr_score = $this->score_ctr_potential($data);
        $factors[] = [
            'name' => 'ctr_potential',
            'label' => 'CTR Potential',
            'label_kr' => 'CTR 잠재력',
            'score' => $ctr_score,
            'weight' => 9,
            'priority' => 'P0',
            'actual_ctr' => $data['ctr'] ?? null,
        ];

        // Dwell Time Potential
        $dwell_score = $this->score_dwell_potential($data);
        $factors[] = [
            'name' => 'dwell_potential',
            'label' => 'Dwell Time Potential',
            'label_kr' => '체류시간 잠재력',
            'score' => $dwell_score,
            'weight' => 10,
            'priority' => 'P0',
        ];

        // Pogo-sticking Risk (inverse)
        $pogo_risk = $this->estimate_pogo_risk($data);
        $factors[] = [
            'name' => 'pogo_risk',
            'label' => 'Bounce Risk',
            'label_kr' => '이탈 위험',
            'score' => 100 - $pogo_risk,
            'weight' => 10,
            'priority' => 'P0',
            'is_penalty' => $pogo_risk > 50,
        ];

        return [
            'category' => 'User Engagement',
            'category_kr' => '사용자 참여',
            'score' => $this->calculate_module_score($factors),
            'weight' => 9, // NavBoost is critical
            'factors' => $factors,
            'note' => 'Based on NavBoost signals from Google Algorithm Leak',
        ];
    }

    /**
     * Score Demotion Risks
     */
    private function score_demotions($data) {
        $factors = [];
        $has_risk = false;

        // SpamBrain Risk
        $spam_risk = $this->check_spam_signals($data);
        $factors[] = [
            'name' => 'spam_risk',
            'label' => 'SpamBrain Risk',
            'label_kr' => '스팸브레인 위험',
            'score' => 100 - $spam_risk,
            'weight' => 9,
            'priority' => 'P0',
            'is_penalty' => $spam_risk > 30,
        ];
        if ($spam_risk > 30) $has_risk = true;

        // Thin Content Risk (Panda)
        $thin_risk = $this->check_thin_content($data);
        $factors[] = [
            'name' => 'thin_content_risk',
            'label' => 'Thin Content Risk',
            'label_kr' => '저품질 콘텐츠 위험',
            'score' => 100 - $thin_risk,
            'weight' => 9,
            'priority' => 'P0',
            'is_penalty' => $thin_risk > 40,
        ];
        if ($thin_risk > 40) $has_risk = true;

        // Keyword Stuffing
        $stuffing_risk = $this->check_keyword_stuffing($data);
        $factors[] = [
            'name' => 'keyword_stuffing',
            'label' => 'Keyword Stuffing Risk',
            'label_kr' => '키워드 스터핑 위험',
            'score' => 100 - $stuffing_risk,
            'weight' => 8,
            'priority' => 'P0',
            'is_penalty' => $stuffing_risk > 30,
        ];
        if ($stuffing_risk > 30) $has_risk = true;

        // UX Issues
        $ux_risk = $this->check_ux_issues($data);
        $factors[] = [
            'name' => 'ux_issues',
            'label' => 'UX Issues Risk',
            'label_kr' => 'UX 문제 위험',
            'score' => 100 - $ux_risk,
            'weight' => 8,
            'priority' => 'P0',
            'is_penalty' => $ux_risk > 30,
        ];
        if ($ux_risk > 30) $has_risk = true;

        return [
            'category' => 'Demotion Risks',
            'category_kr' => '강등 위험',
            'score' => $this->calculate_module_score($factors),
            'weight' => 9, // Critical - penalties can tank rankings
            'factors' => $factors,
            'has_risk' => $has_risk,
        ];
    }

    /**
     * Score AEO (Answer Engine Optimization)
     */
    private function score_aeo($data) {
        $factors = [];

        // FAQ Content
        $faq_score = $this->score_faq_content($data);
        $factors[] = [
            'name' => 'faq_content',
            'label' => 'FAQ Content',
            'label_kr' => 'FAQ 콘텐츠',
            'score' => $faq_score,
            'weight' => 8,
            'priority' => 'P0',
        ];

        // Conversational Content
        $conversational_score = $this->score_conversational_content($data);
        $factors[] = [
            'name' => 'conversational_content',
            'label' => 'Conversational Content',
            'label_kr' => '대화형 콘텐츠',
            'score' => $conversational_score,
            'weight' => 7,
            'priority' => 'P1',
        ];

        // Direct Answers
        $direct_score = $this->score_direct_answers($data);
        $factors[] = [
            'name' => 'direct_answers',
            'label' => 'Direct Answers',
            'label_kr' => '직접 답변',
            'score' => $direct_score,
            'weight' => 8,
            'priority' => 'P0',
        ];

        // Featured Snippet Potential
        $snippet_score = $this->score_featured_snippet_potential($data);
        $factors[] = [
            'name' => 'featured_snippet_potential',
            'label' => 'Featured Snippet Potential',
            'label_kr' => '추천 스니펫 잠재력',
            'score' => $snippet_score,
            'weight' => 7,
            'priority' => 'P1',
        ];

        return [
            'category' => 'AI Engine Optimization',
            'category_kr' => 'AI 엔진 최적화',
            'score' => $this->calculate_module_score($factors),
            'weight' => 7,
            'factors' => $factors,
            'note' => 'For AI search engines like ChatGPT, Perplexity, Claude',
        ];
    }

    // ========================================
    // Helper Scoring Methods
    // ========================================

    private function check_indexable($data) {
        if (!isset($data['robots_meta'])) return 50;
        $robots = strtolower($data['robots_meta']);
        if (strpos($robots, 'noindex') !== false) return 0;
        return 100;
    }

    private function check_crawl_status($data) {
        if (isset($data['http_status'])) {
            if ($data['http_status'] === 200) return 100;
            if ($data['http_status'] >= 300 && $data['http_status'] < 400) return 70;
            if ($data['http_status'] >= 400) return 0;
        }
        return isset($data['is_crawlable']) ? ($data['is_crawlable'] ? 100 : 0) : 50;
    }

    private function check_canonical($data) {
        if (!isset($data['canonical'])) return 50;
        if (empty($data['canonical'])) return 30;
        if ($data['canonical'] === $data['url']) return 100;
        return 70;
    }

    private function check_robots_meta($data) {
        if (!isset($data['robots_meta'])) return 50;
        $robots = strtolower($data['robots_meta']);
        if (strpos($robots, 'noindex') !== false) return 0;
        if (strpos($robots, 'nofollow') !== false) return 50;
        return 100;
    }

    private function score_title($data) {
        if (!isset($data['title'])) return 0;

        $title = $data['title'];
        $length = mb_strlen($title);
        $score = 0;

        // Length: optimal 50-60 chars
        if ($length >= 50 && $length <= 60) $score += 40;
        elseif ($length >= 30 && $length <= 70) $score += 25;
        else $score += 10;

        // Keyword presence
        if (isset($data['focus_keyword']) && !empty($data['focus_keyword'])) {
            if (stripos($title, $data['focus_keyword']) !== false) {
                $score += 40;
                if (stripos($title, $data['focus_keyword']) < 20) $score += 20;
            }
        } else {
            $score += 30; // Neutral if no keyword set
        }

        return min(100, $score);
    }

    private function get_title_details($data) {
        $title = $data['title'] ?? '';
        return [
            'length' => mb_strlen($title),
            'optimal_length' => '50-60 characters',
            'has_keyword' => isset($data['focus_keyword']) && stripos($title, $data['focus_keyword']) !== false,
        ];
    }

    private function score_meta_description($data) {
        if (!isset($data['meta_description']) || empty($data['meta_description'])) return 0;

        $desc = $data['meta_description'];
        $length = mb_strlen($desc);
        $score = 0;

        // Length: optimal 150-160 chars
        if ($length >= 150 && $length <= 160) $score += 50;
        elseif ($length >= 120 && $length <= 170) $score += 35;
        elseif ($length >= 50) $score += 20;

        // Keyword presence
        if (isset($data['focus_keyword']) && stripos($desc, $data['focus_keyword']) !== false) {
            $score += 30;
        }

        // CTA words
        $cta_words = ['learn', 'discover', 'find', 'get', 'explore', 'click', 'read', 'start', 'try'];
        foreach ($cta_words as $word) {
            if (stripos($desc, $word) !== false) {
                $score += 20;
                break;
            }
        }

        return min(100, $score);
    }

    private function get_meta_description_details($data) {
        $desc = $data['meta_description'] ?? '';
        return [
            'length' => mb_strlen($desc),
            'optimal_length' => '150-160 characters',
            'has_keyword' => isset($data['focus_keyword']) && stripos($desc, $data['focus_keyword']) !== false,
        ];
    }

    private function score_headings($data) {
        $score = 0;

        // H1 count (exactly 1 is ideal)
        $h1_count = $data['h1_count'] ?? 0;
        if ($h1_count === 1) $score += 40;
        elseif ($h1_count > 1) $score += 20;

        // H2 usage
        $h2_count = $data['h2_count'] ?? 0;
        if ($h2_count >= 2) $score += 30;
        elseif ($h2_count >= 1) $score += 15;

        // H3 usage
        if (($data['h3_count'] ?? 0) >= 1) $score += 15;

        // Hierarchy validation
        if ($data['heading_hierarchy_valid'] ?? false) $score += 15;

        return min(100, $score);
    }

    private function score_keyword_usage($data) {
        if (!isset($data['focus_keyword']) || empty($data['focus_keyword'])) {
            return 60; // Neutral without keyword
        }

        $score = 0;
        $keyword = strtolower($data['focus_keyword']);
        $content = strtolower($data['content'] ?? '');

        // Keyword in title
        if (isset($data['title']) && stripos($data['title'], $keyword) !== false) {
            $score += 25;
        }

        // Keyword in H1
        if (isset($data['h1']) && is_array($data['h1'])) {
            foreach ($data['h1'] as $h1) {
                if (stripos($h1, $keyword) !== false) {
                    $score += 20;
                    break;
                }
            }
        }

        // Keyword in first 100 words
        $first_100_words = implode(' ', array_slice(explode(' ', $content), 0, 100));
        if (stripos($first_100_words, $keyword) !== false) {
            $score += 20;
        }

        // Keyword density (1-2.5% ideal)
        $word_count = $data['word_count'] ?? 0;
        if ($word_count > 0) {
            $keyword_count = substr_count($content, $keyword);
            $density = ($keyword_count / $word_count) * 100;
            if ($density >= 1 && $density <= 2.5) {
                $score += 35;
            } elseif ($density > 0 && $density < 3) {
                $score += 20;
            }
        }

        return min(100, $score);
    }

    private function score_images($data) {
        if (!isset($data['images']) || count($data['images']) === 0) {
            return 50; // Neutral if no images
        }

        $images = $data['images'];
        $total = count($images);
        $score = 0;

        // Alt text coverage
        $with_alt = 0;
        $with_lazy = 0;
        $with_dimensions = 0;

        foreach ($images as $img) {
            if (!empty($img['has_alt'])) $with_alt++;
            if (!empty($img['lazy_loading'])) $with_lazy++;
            if (!empty($img['has_dimensions'])) $with_dimensions++;
        }

        // Alt text (50% of score)
        $score += ($with_alt / $total) * 50;

        // Lazy loading (25% of score)
        $score += ($with_lazy / $total) * 25;

        // Dimensions (25% of score)
        $score += ($with_dimensions / $total) * 25;

        return round($score);
    }

    private function score_url_structure($data) {
        $url = $data['url'] ?? '';
        if (empty($url)) return 50;

        $path = parse_url($url, PHP_URL_PATH);
        if (empty($path) || $path === '/') return 80;

        $score = 60;

        // Short URL (under 75 chars)
        if (strlen($url) < 75) $score += 15;

        // Contains keywords (if set)
        if (isset($data['focus_keyword'])) {
            $keyword_slug = sanitize_title($data['focus_keyword']);
            if (stripos($path, $keyword_slug) !== false) {
                $score += 15;
            }
        }

        // No special characters
        if (!preg_match('/[^a-zA-Z0-9\-\/]/', $path)) {
            $score += 10;
        }

        return min(100, $score);
    }

    private function score_content_length($data) {
        $word_count = $data['word_count'] ?? 0;

        if ($word_count >= 2000) return 100;
        if ($word_count >= 1500) return 90;
        if ($word_count >= 1000) return 80;
        if ($word_count >= 500) return 60;
        if ($word_count >= 300) return 40;
        return 20;
    }

    private function score_content_depth($data) {
        $score = 0;

        // Word count contribution
        $word_count = $data['word_count'] ?? 0;
        if ($word_count >= 2000) $score += 30;
        elseif ($word_count >= 1000) $score += 20;

        // Heading structure
        $heading_count = ($data['h2_count'] ?? 0) + ($data['h3_count'] ?? 0);
        if ($heading_count >= 5) $score += 25;
        elseif ($heading_count >= 3) $score += 15;

        // Lists
        if ($data['has_lists'] ?? false) $score += 15;

        // Tables
        if ($data['has_tables'] ?? false) $score += 15;

        // Images
        if (($data['image_count'] ?? 0) >= 3) $score += 15;

        return min(100, $score);
    }

    private function score_readability($data) {
        if (isset($data['flesch_reading_ease'])) {
            $flesch = $data['flesch_reading_ease'];
            if ($flesch >= 60 && $flesch <= 70) return 100;
            if ($flesch >= 50 && $flesch <= 80) return 80;
            if ($flesch >= 30 && $flesch <= 90) return 60;
            return 40;
        }

        if (isset($data['avg_sentence_length'])) {
            $asl = $data['avg_sentence_length'];
            if ($asl >= 15 && $asl <= 20) return 100;
            if ($asl >= 10 && $asl <= 25) return 75;
            return 50;
        }

        return 60;
    }

    private function score_originality($data) {
        if (isset($data['originality_score'])) {
            return min(100, ($data['originality_score'] / 512) * 100);
        }
        if (isset($data['unique_phrases_ratio'])) {
            return $data['unique_phrases_ratio'] * 100;
        }
        return 70; // Default mid-high
    }

    private function score_internal_links($data) {
        $count = $data['internal_link_count'] ?? 0;

        if ($count >= 10) return 100;
        if ($count >= 5) return 80;
        if ($count >= 3) return 60;
        if ($count >= 1) return 40;
        return 20;
    }

    private function score_external_links($data) {
        $count = $data['external_link_count'] ?? 0;
        $has_authority = $data['has_authority_external_links'] ?? false;

        if ($count >= 3 && $has_authority) return 100;
        if ($count >= 2) return 80;
        if ($count >= 1) return 60;
        return 40;
    }

    private function score_anchor_quality($data) {
        if (!isset($data['anchor_texts'])) return 50;

        $anchors = $data['anchor_texts'];
        $total = count($anchors);
        if ($total === 0) return 50;

        $quality_anchors = 0;
        $generic_terms = ['click here', 'read more', 'learn more', 'here', 'link', 'this'];

        foreach ($anchors as $anchor) {
            if (!in_array(strtolower($anchor), $generic_terms) && strlen($anchor) > 3) {
                $quality_anchors++;
            }
        }

        return round(($quality_anchors / $total) * 100);
    }

    private function score_backlink_diversity($data) {
        if (!isset($data['referring_domains'])) return 50;
        $domains = $data['referring_domains'];

        if ($domains >= 100) return 100;
        if ($domains >= 50) return 90;
        if ($domains >= 20) return 75;
        if ($domains >= 10) return 60;
        if ($domains >= 5) return 45;
        return 30;
    }

    private function score_topic_focus($data) {
        if (isset($data['topic_focus_score'])) {
            return $data['topic_focus_score'];
        }

        if (!isset($data['focus_keyword']) || !isset($data['content'])) {
            return 60;
        }

        $score = 0;
        $keyword = strtolower($data['focus_keyword']);
        $content = strtolower($data['content'] ?? '');

        // Keyword in title
        if (isset($data['title']) && stripos($data['title'], $keyword) !== false) {
            $score += 25;
        }

        // Keyword in H1
        if (isset($data['h1']) && is_array($data['h1'])) {
            foreach ($data['h1'] as $h1) {
                if (stripos($h1, $keyword) !== false) {
                    $score += 25;
                    break;
                }
            }
        }

        // First 200 words
        $first_200 = implode(' ', array_slice(explode(' ', $content), 0, 200));
        if (stripos($first_200, $keyword) !== false) $score += 25;

        // Last 200 words
        $words = explode(' ', $content);
        $last_200 = implode(' ', array_slice($words, -200));
        if (stripos($last_200, $keyword) !== false) $score += 25;

        return $score;
    }

    private function score_entity_coverage($data) {
        if (isset($data['entities'])) {
            $count = count($data['entities']);
            if ($count >= 10) return 100;
            if ($count >= 5) return 80;
            if ($count >= 3) return 60;
            return 40;
        }
        return 50;
    }

    private function score_last_update($data) {
        if (!isset($data['last_modified']) && !isset($data['post_modified'])) {
            return 50;
        }

        $modified = $data['last_modified'] ?? $data['post_modified'] ?? '';
        $last_modified = strtotime($modified);
        if (!$last_modified) return 50;

        $days_old = (time() - $last_modified) / (60 * 60 * 24);

        if ($days_old <= 7) return 100;
        if ($days_old <= 30) return 90;
        if ($days_old <= 90) return 75;
        if ($days_old <= 180) return 60;
        if ($days_old <= 365) return 40;
        return 20;
    }

    private function score_date_clarity($data) {
        $score = 0;

        if (isset($data['published_date']) || isset($data['post_date'])) $score += 50;
        if (isset($data['modified_date']) || isset($data['post_modified'])) $score += 30;
        if ($data['has_date_schema'] ?? false) $score += 20;

        return min(100, $score);
    }

    private function score_author_info($data) {
        $score = 0;

        if (isset($data['author_name']) || isset($data['post_author'])) $score += 30;
        if (isset($data['author_bio'])) $score += 25;
        if (isset($data['author_image'])) $score += 15;
        if ($data['has_author_schema'] ?? false) $score += 20;
        if (isset($data['author_social_links'])) $score += 10;

        return min(100, $score);
    }

    private function score_expertise($data) {
        $score = 40;

        if ($data['has_citations'] ?? false) $score += 20;
        if ($data['has_statistics'] ?? false) $score += 15;
        if ($data['has_expert_quotes'] ?? false) $score += 15;
        if ($data['has_original_research'] ?? false) $score += 10;

        return min(100, $score);
    }

    private function score_trust_signals($data) {
        $score = 30;

        if ($data['has_about_page'] ?? false) $score += 15;
        if ($data['has_contact_page'] ?? false) $score += 15;
        if ($data['has_privacy_policy'] ?? false) $score += 15;
        if ($data['has_terms_of_service'] ?? false) $score += 10;
        if ($data['is_https'] ?? false) $score += 15;

        return min(100, $score);
    }

    private function score_ctr_potential($data) {
        // If we have actual CTR from Search Console
        if (isset($data['ctr'])) {
            $ctr = $data['ctr'];
            if ($ctr >= 10) return 100;
            if ($ctr >= 5) return 85;
            if ($ctr >= 3) return 70;
            if ($ctr >= 1) return 50;
            return 30;
        }

        // Estimate based on page elements
        $score = 50;
        $title_score = $this->score_title($data);
        $score += ($title_score / 100) * 25;

        $desc_score = $this->score_meta_description($data);
        $score += ($desc_score / 100) * 15;

        if (($data['rich_results_eligible'] ?? false)) $score += 10;

        return min(100, $score);
    }

    private function score_dwell_potential($data) {
        $score = 50;

        $depth_score = $this->score_content_depth($data);
        $score += ($depth_score / 100) * 25;

        $readability_score = $this->score_readability($data);
        $score += ($readability_score / 100) * 15;

        if ($data['has_video'] ?? false) $score += 10;

        return min(100, $score);
    }

    private function estimate_pogo_risk($data) {
        $risk = 0;

        // Slow page
        $lcp = $data['lcp'] ?? null;
        if ($lcp && $lcp > 4000) $risk += 30;

        // Thin content
        if (($data['word_count'] ?? 0) < 300) $risk += 30;

        // Poor mobile
        $mobile_score = $data['lighthouse_mobile_score'] ?? $data['mobile_score'] ?? 100;
        if ($mobile_score < 50) $risk += 20;

        // Intrusive elements
        if ($data['has_intrusive_interstitials'] ?? false) $risk += 20;

        return min(100, $risk);
    }

    private function check_spam_signals($data) {
        $risk = 0;

        $stuffing = $this->check_keyword_stuffing($data);
        $risk += ($stuffing / 100) * 40;

        if ($data['has_hidden_text'] ?? false) $risk += 30;
        if (isset($data['ad_ratio']) && $data['ad_ratio'] > 0.3) $risk += 30;

        return min(100, $risk);
    }

    private function check_thin_content($data) {
        $word_count = $data['word_count'] ?? 0;

        if ($word_count < 100) return 100;
        if ($word_count < 200) return 80;
        if ($word_count < 300) return 50;
        if ($word_count < 500) return 30;
        return 0;
    }

    private function check_keyword_stuffing($data) {
        if (!isset($data['content']) || !isset($data['focus_keyword'])) return 0;

        $content = strtolower($data['content']);
        $keyword = strtolower($data['focus_keyword']);
        $word_count = str_word_count($content);

        if ($word_count === 0) return 0;

        $keyword_count = substr_count($content, $keyword);
        $density = ($keyword_count / $word_count) * 100;

        if ($density > 5) return 100;
        if ($density > 3) return 60;
        if ($density > 2.5) return 30;
        return 0;
    }

    private function check_ux_issues($data) {
        $risk = 0;

        // Mobile issues
        $mobile_score = $data['lighthouse_mobile_score'] ?? $data['mobile_score'] ?? 100;
        if ($mobile_score < 50) $risk += 30;

        // Speed issues
        $lcp = $data['lcp'] ?? null;
        if ($lcp && $lcp > 4000) $risk += 25;

        // CLS issues
        $cls = $data['cls'] ?? null;
        if ($cls && $cls > 0.25) $risk += 25;

        if ($data['has_intrusive_interstitials'] ?? false) $risk += 20;

        return min(100, $risk);
    }

    private function score_faq_content($data) {
        $score = 0;

        if ($data['has_faq_schema'] ?? false) $score += 40;

        // Check for FAQ patterns in content
        $content = strtolower($data['content'] ?? '');
        $question_patterns = ['what is', 'how to', 'why do', 'when should', 'where can', 'who can'];
        $question_count = 0;

        foreach ($question_patterns as $pattern) {
            $question_count += substr_count($content, $pattern);
        }

        if ($question_count >= 5) $score += 40;
        elseif ($question_count >= 3) $score += 25;
        elseif ($question_count >= 1) $score += 15;

        // H2/H3 questions
        $h2s = $data['h2'] ?? [];
        $h3s = $data['h3'] ?? [];
        $headings = array_merge($h2s, $h3s);

        foreach ($headings as $heading) {
            if (strpos($heading, '?') !== false) {
                $score += 5;
            }
        }

        return min(100, $score);
    }

    private function score_conversational_content($data) {
        $content = strtolower($data['content'] ?? '');

        // Check for conversational patterns
        $conversational_patterns = ['you can', 'you should', 'we recommend', 'let\'s', 'here\'s how', 'for example'];
        $matches = 0;

        foreach ($conversational_patterns as $pattern) {
            if (strpos($content, $pattern) !== false) {
                $matches++;
            }
        }

        $score = min(100, $matches * 20 + 40);
        return $score;
    }

    private function score_direct_answers($data) {
        $score = 40;

        // Short paragraphs at start (direct answer format)
        $paragraphs = $data['paragraph_count'] ?? 0;
        $word_count = $data['word_count'] ?? 0;

        if ($paragraphs > 0 && $word_count > 0) {
            $avg_words_per_paragraph = $word_count / $paragraphs;
            if ($avg_words_per_paragraph <= 50) $score += 20;
        }

        // Lists (good for direct answers)
        if ($data['has_lists'] ?? false) $score += 20;

        // Tables (good for data answers)
        if ($data['has_tables'] ?? false) $score += 20;

        return min(100, $score);
    }

    private function score_featured_snippet_potential($data) {
        $score = 0;

        // List format
        if ($data['has_lists'] ?? false) $score += 25;

        // Table format
        if ($data['has_tables'] ?? false) $score += 25;

        // Short paragraphs
        $paragraphs = $data['paragraph_count'] ?? 0;
        $word_count = $data['word_count'] ?? 0;
        if ($paragraphs > 0 && ($word_count / $paragraphs) <= 60) {
            $score += 20;
        }

        // Definition format (starts with "X is" pattern)
        if (isset($data['content'])) {
            $first_100 = substr($data['content'], 0, 200);
            if (preg_match('/^.{0,50}\sis\s/', $first_100)) {
                $score += 15;
            }
        }

        // FAQ schema
        if ($data['has_faq_schema'] ?? false) $score += 15;

        return min(100, $score);
    }

    // ========================================
    // Utility Methods
    // ========================================

    private function calculate_module_score($factors) {
        $total_weight = 0;
        $total_score = 0;

        foreach ($factors as $factor) {
            $priority = $factor['priority'] ?? 'P2';
            $weight = ($factor['weight'] ?? 5) * (self::PRIORITY_MULTIPLIERS[$priority] ?? 0.5);
            $total_weight += $weight;
            $total_score += ($factor['score'] ?? 0) * $weight;
        }

        return $total_weight > 0 ? round($total_score / $total_weight, 1) : 0;
    }

    private function calculate_grade($score) {
        if ($score >= 90) return 'A+';
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'F';
    }

    private function get_data_key($factor_name) {
        $key_mappings = [
            'LCP' => 'lcp',
            'FID' => 'fid',
            'CLS' => 'cls',
            'FCP' => 'fcp',
            'TTFB' => 'ttfb',
            'TBT' => 'tbt',
            'performanceScore' => 'lighthouse_performance_score',
            'seoScore' => 'lighthouse_seo_score',
            'accessibilityScore' => 'lighthouse_accessibility_score',
            'bestPracticesScore' => 'lighthouse_best_practices_score',
        ];

        return $key_mappings[$factor_name] ?? strtolower($factor_name);
    }

    private function get_factor_label($name) {
        $labels = [
            'LCP' => 'Largest Contentful Paint',
            'FID' => 'First Input Delay',
            'CLS' => 'Cumulative Layout Shift',
            'FCP' => 'First Contentful Paint',
            'TTFB' => 'Time to First Byte',
            'TBT' => 'Total Blocking Time',
            'performanceScore' => 'Performance Score',
            'seoScore' => 'SEO Score',
            'accessibilityScore' => 'Accessibility Score',
            'bestPracticesScore' => 'Best Practices Score',
        ];

        return $labels[$name] ?? $name;
    }

    private function get_factor_label_kr($name) {
        $labels = [
            'LCP' => '최대 콘텐츠 페인트',
            'FID' => '첫 입력 지연',
            'CLS' => '누적 레이아웃 이동',
            'FCP' => '첫 콘텐츠 페인트',
            'TTFB' => '첫 바이트 시간',
            'TBT' => '총 차단 시간',
            'performanceScore' => '성능 점수',
            'seoScore' => 'SEO 점수',
            'accessibilityScore' => '접근성 점수',
            'bestPracticesScore' => '모범 사례 점수',
        ];

        return $labels[$name] ?? $name;
    }

    private function get_priority_label($priority_const) {
        $map = [
            OneClick_SEO_Pro_Ranking_Factors_DB::PRIORITY_P0 => 'P0',
            OneClick_SEO_Pro_Ranking_Factors_DB::PRIORITY_P1 => 'P1',
            OneClick_SEO_Pro_Ranking_Factors_DB::PRIORITY_P2 => 'P2',
            OneClick_SEO_Pro_Ranking_Factors_DB::PRIORITY_P3 => 'P3',
        ];

        return $map[$priority_const] ?? 'P2';
    }

    private function merge_pagespeed_data($page_data, $pagespeed_data) {
        // Merge Core Web Vitals from PageSpeed API response
        if (isset($pagespeed_data['loadingExperience']['metrics'])) {
            $metrics = $pagespeed_data['loadingExperience']['metrics'];

            if (isset($metrics['LARGEST_CONTENTFUL_PAINT_MS']['percentile'])) {
                $page_data['lcp'] = $metrics['LARGEST_CONTENTFUL_PAINT_MS']['percentile'];
            }
            if (isset($metrics['FIRST_INPUT_DELAY_MS']['percentile'])) {
                $page_data['fid'] = $metrics['FIRST_INPUT_DELAY_MS']['percentile'];
            }
            if (isset($metrics['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'])) {
                $page_data['cls'] = $metrics['CUMULATIVE_LAYOUT_SHIFT_SCORE']['percentile'] / 100;
            }
            if (isset($metrics['FIRST_CONTENTFUL_PAINT_MS']['percentile'])) {
                $page_data['fcp'] = $metrics['FIRST_CONTENTFUL_PAINT_MS']['percentile'];
            }
            if (isset($metrics['EXPERIMENTAL_TIME_TO_FIRST_BYTE']['percentile'])) {
                $page_data['ttfb'] = $metrics['EXPERIMENTAL_TIME_TO_FIRST_BYTE']['percentile'];
            }
        }

        // Merge Lighthouse scores
        if (isset($pagespeed_data['lighthouseResult']['categories'])) {
            $categories = $pagespeed_data['lighthouseResult']['categories'];

            if (isset($categories['performance']['score'])) {
                $page_data['lighthouse_performance_score'] = $categories['performance']['score'] * 100;
            }
            if (isset($categories['seo']['score'])) {
                $page_data['lighthouse_seo_score'] = $categories['seo']['score'] * 100;
            }
            if (isset($categories['accessibility']['score'])) {
                $page_data['lighthouse_accessibility_score'] = $categories['accessibility']['score'] * 100;
            }
            if (isset($categories['best-practices']['score'])) {
                $page_data['lighthouse_best_practices_score'] = $categories['best-practices']['score'] * 100;
            }
        }

        // Merge TBT from Lighthouse audits
        if (isset($pagespeed_data['lighthouseResult']['audits']['total-blocking-time']['numericValue'])) {
            $page_data['tbt'] = $pagespeed_data['lighthouseResult']['audits']['total-blocking-time']['numericValue'];
        }

        return $page_data;
    }

    private function merge_search_console_data($page_data, $sc_data) {
        if (isset($sc_data['ctr'])) {
            $page_data['ctr'] = $sc_data['ctr'];
        }
        if (isset($sc_data['impressions'])) {
            $page_data['impressions'] = $sc_data['impressions'];
        }
        if (isset($sc_data['position'])) {
            $page_data['avg_position'] = $sc_data['position'];
        }
        if (isset($sc_data['clicks'])) {
            $page_data['clicks'] = $sc_data['clicks'];
        }
        if (isset($sc_data['coverage_state'])) {
            $page_data['coverage_state'] = $sc_data['coverage_state'];
        }

        return $page_data;
    }

    private function generate_recommendations($scores) {
        $recommendations = [];

        foreach ($scores as $module_name => $module_data) {
            if (!isset($module_data['factors'])) continue;

            foreach ($module_data['factors'] as $factor) {
                $score = $factor['score'] ?? 100;
                if ($score < 60) {
                    $recommendations[] = [
                        'module' => $module_name,
                        'factor' => $factor['name'],
                        'label' => $factor['label'] ?? $factor['name'],
                        'label_kr' => $factor['label_kr'] ?? $factor['name'],
                        'current_score' => $score,
                        'priority' => $factor['priority'] ?? 'P2',
                        'weight' => $factor['weight'] ?? 5,
                        'category' => $module_data['category'] ?? 'Unknown',
                        'is_penalty' => $factor['is_penalty'] ?? false,
                    ];
                }
            }
        }

        // Sort by priority and weight
        usort($recommendations, function($a, $b) {
            $priority_order = ['P0' => 0, 'P1' => 1, 'P2' => 2, 'P3' => 3];
            $a_priority = $priority_order[$a['priority']] ?? 4;
            $b_priority = $priority_order[$b['priority']] ?? 4;

            if ($a_priority !== $b_priority) {
                return $a_priority - $b_priority;
            }

            return $b['weight'] - $a['weight'];
        });

        return array_slice($recommendations, 0, 15);
    }

    private function get_priority_actions($scores) {
        $actions = [];

        foreach ($scores as $module_name => $module_data) {
            if (!isset($module_data['factors'])) continue;

            foreach ($module_data['factors'] as $factor) {
                $priority = $factor['priority'] ?? 'P2';
                $score = $factor['score'] ?? 100;

                if ($priority === 'P0' && $score < 50) {
                    $actions[] = [
                        'type' => 'critical',
                        'module' => $module_name,
                        'factor' => $factor['label'] ?? $factor['name'],
                        'factor_kr' => $factor['label_kr'] ?? $factor['name'],
                        'score' => $score,
                        'message' => sprintf('Critical: %s needs immediate attention (Score: %d)',
                            $factor['label'] ?? $factor['name'], $score),
                        'message_kr' => sprintf('긴급: %s 즉시 개선 필요 (점수: %d)',
                            $factor['label_kr'] ?? $factor['name'], $score),
                    ];
                }
            }
        }

        return $actions;
    }

    private function get_score_breakdown($scores) {
        $breakdown = [];

        foreach ($scores as $module_name => $module_data) {
            $breakdown[$module_name] = [
                'score' => $module_data['score'] ?? 0,
                'weight' => $module_data['weight'] ?? 5,
                'category' => $module_data['category'] ?? 'Unknown',
                'category_kr' => $module_data['category_kr'] ?? 'Unknown',
                'factor_count' => count($module_data['factors'] ?? []),
            ];
        }

        return $breakdown;
    }

    private function get_api_sources_used($api_data) {
        $sources = [];

        if (!empty($api_data['pagespeed'])) {
            $sources[] = [
                'name' => 'PageSpeed API v5',
                'type' => 'external',
                'data_types' => ['Core Web Vitals', 'Lighthouse Scores'],
            ];
        }

        if (!empty($api_data['search_console'])) {
            $sources[] = [
                'name' => 'Search Console API',
                'type' => 'external',
                'data_types' => ['CTR', 'Impressions', 'Position', 'Coverage'],
            ];
        }

        $sources[] = [
            'name' => 'Internal Analysis',
            'type' => 'internal',
            'data_types' => ['On-Page', 'Content', 'Links', 'Schema'],
        ];

        return $sources;
    }
}

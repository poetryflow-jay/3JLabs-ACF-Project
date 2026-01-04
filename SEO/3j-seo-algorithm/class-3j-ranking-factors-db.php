<?php
/**
 * 3J Labs Ranking Factors Database
 *
 * Programmatic access to the Google ranking factors database.
 * Based on Google Algorithm Leak Analysis (May 2024).
 *
 * @package 3J_SEO_Algorithm
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class ThreeJ_Ranking_Factors_DB {

    /**
     * Ranking factors database
     * Structure: ModuleName => [factors]
     */
    private static $factors = null;

    /**
     * Priority levels
     */
    const PRIORITY_P0 = 'P0'; // Critical
    const PRIORITY_P1 = 'P1'; // High
    const PRIORITY_P2 = 'P2'; // Medium
    const PRIORITY_P3 = 'P3'; // Low

    /**
     * Impact types
     */
    const IMPACT_POSITIVE = 'Positive';
    const IMPACT_NEGATIVE = 'Negative';
    const IMPACT_NEUTRAL = 'Neutral';
    const IMPACT_VARIABLE = 'Variable';
    const IMPACT_CORE = 'Core';

    /**
     * Initialize the database
     */
    public static function init() {
        if (self::$factors !== null) {
            return;
        }

        self::$factors = [
            // ========================================
            // NavBoost - User Engagement Signals
            // ========================================
            'NavBoost' => [
                'goodClicks' => [
                    'description_en' => 'Long dwell time clicks indicating user satisfaction',
                    'description_kr' => '사용자가 만족한 클릭 (긴 체류시간)',
                    'weight' => 10,
                    'category' => 'User Engagement',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'navboost.good_clicks',
                    'scoring_method' => 'engagement_score',
                ],
                'badClicks' => [
                    'description_en' => 'Quick return clicks (pogo-sticking)',
                    'description_kr' => '빠르게 이탈한 클릭 (포고스틱)',
                    'weight' => 10,
                    'category' => 'User Engagement',
                    'impact' => self::IMPACT_NEGATIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'navboost.bad_clicks',
                    'scoring_method' => 'pogo_risk',
                ],
                'lastLongestClicks' => [
                    'description_en' => 'Final dwelled-upon result signaling search completion',
                    'description_kr' => '검색 세션의 마지막이자 가장 오래 머문 클릭',
                    'weight' => 10,
                    'category' => 'User Engagement',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'navboost.last_longest',
                    'scoring_method' => 'session_completion',
                ],
                'unsquashedClicks' => [
                    'description_en' => 'Verified genuine user interactions',
                    'description_kr' => '검증된 실제 클릭',
                    'weight' => 9,
                    'category' => 'User Engagement',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'navboost.unsquashed',
                    'scoring_method' => 'click_quality',
                ],
                'impressions' => [
                    'description_en' => 'Search result impressions count',
                    'description_kr' => '검색 결과 노출 횟수',
                    'weight' => 7,
                    'category' => 'Visibility',
                    'impact' => self::IMPACT_NEUTRAL,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'navboost.impressions',
                    'scoring_method' => 'visibility_score',
                ],
                'ctr' => [
                    'description_en' => 'Click-through rate',
                    'description_kr' => '클릭률',
                    'weight' => 9,
                    'category' => 'User Engagement',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'navboost.ctr',
                    'scoring_method' => 'ctr_potential',
                ],
            ],

            // ========================================
            // CompressedQualitySignals - Quality Metrics
            // ========================================
            'CompressedQualitySignals' => [
                'siteAuthority' => [
                    'description_en' => 'Domain-level trust and authority score',
                    'description_kr' => '도메인 레벨 신뢰도/권위도 점수',
                    'weight' => 10,
                    'category' => 'Domain Authority',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'quality.site_authority',
                    'scoring_method' => 'authority_score',
                ],
                'pandaDemotion' => [
                    'description_en' => 'Panda algorithm demotion for low-quality content',
                    'description_kr' => '저품질 콘텐츠 Panda 강등',
                    'weight' => 9,
                    'category' => 'Content Quality',
                    'impact' => self::IMPACT_NEGATIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'quality.panda_demotion',
                    'scoring_method' => 'thin_content_risk',
                ],
                'navDemotion' => [
                    'description_en' => 'Navigation/UX issues demotion',
                    'description_kr' => '네비게이션/UX 문제 강등',
                    'weight' => 8,
                    'category' => 'User Experience',
                    'impact' => self::IMPACT_NEGATIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'quality.nav_demotion',
                    'scoring_method' => 'ux_issues_risk',
                ],
                'originalContentScore' => [
                    'description_en' => 'Content originality score (0-512 scale)',
                    'description_kr' => '콘텐츠 원본성 점수',
                    'weight' => 9,
                    'category' => 'Content Quality',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'quality.original_content',
                    'scoring_method' => 'originality_score',
                    'max_score' => 512,
                ],
                'ugcDiscussionEffortScore' => [
                    'description_en' => 'UGC page quality signal',
                    'description_kr' => 'UGC 페이지 품질 신호',
                    'weight' => 6,
                    'category' => 'Content Quality',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P2,
                    'algorithm_key' => 'quality.ugc_effort',
                    'scoring_method' => 'ugc_quality',
                ],
                'productReviewPDemoteSite' => [
                    'description_en' => 'Product review site demotion',
                    'description_kr' => '제품 리뷰 사이트 강등',
                    'weight' => 8,
                    'category' => 'Content Quality',
                    'impact' => self::IMPACT_NEGATIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'quality.review_demotion',
                    'scoring_method' => 'review_quality_risk',
                ],
            ],

            // ========================================
            // SiteTopics - Topical Authority
            // ========================================
            'SiteTopics' => [
                'siteEmbeddings' => [
                    'description_en' => 'Vector embeddings of site core topics',
                    'description_kr' => '사이트 핵심 주제 벡터 임베딩',
                    'weight' => 8,
                    'category' => 'Topical Authority',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'topics.embeddings',
                    'scoring_method' => 'topic_vector',
                ],
                'siteFocusScore' => [
                    'description_en' => 'Topic focus concentration score',
                    'description_kr' => '주제 집중도 점수',
                    'weight' => 8,
                    'category' => 'Topical Authority',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'topics.focus',
                    'scoring_method' => 'topic_focus',
                ],
                'siteRadius' => [
                    'description_en' => 'Deviation from core site embeddings',
                    'description_kr' => '핵심 주제로부터의 편차',
                    'weight' => 7,
                    'category' => 'Topical Authority',
                    'impact' => self::IMPACT_NEGATIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'topics.radius',
                    'scoring_method' => 'topic_deviation',
                ],
                'site2vec' => [
                    'description_en' => 'Site-wide topic vector representation',
                    'description_kr' => '사이트 전체 주제 벡터 표현',
                    'weight' => 7,
                    'category' => 'Topical Authority',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'topics.site2vec',
                    'scoring_method' => 'site_vector',
                ],
            ],

            // ========================================
            // Freshness - Content Freshness
            // ========================================
            'Freshness' => [
                'bylineDate' => [
                    'description_en' => 'Explicitly set publication date',
                    'description_kr' => '명시적 작성 날짜',
                    'weight' => 7,
                    'category' => 'Content Freshness',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'freshness.byline',
                    'scoring_method' => 'date_clarity',
                ],
                'syntacticDate' => [
                    'description_en' => 'Date extracted from URL or title',
                    'description_kr' => 'URL/제목에서 추출한 날짜',
                    'weight' => 6,
                    'category' => 'Content Freshness',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'freshness.syntactic',
                    'scoring_method' => 'url_date',
                ],
                'semanticDate' => [
                    'description_en' => 'Date derived from content context',
                    'description_kr' => '콘텐츠에서 해석된 날짜',
                    'weight' => 6,
                    'category' => 'Content Freshness',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'freshness.semantic',
                    'scoring_method' => 'content_date',
                ],
                'lastSignificantUpdate' => [
                    'description_en' => 'Last meaningful content update',
                    'description_kr' => '마지막 의미있는 업데이트',
                    'weight' => 8,
                    'category' => 'Content Freshness',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'freshness.last_update',
                    'scoring_method' => 'freshness_score',
                ],
                'FreshnessTwiddler' => [
                    'description_en' => 'Freshness-based re-ranking function',
                    'description_kr' => '신선도 기반 재랭킹 함수',
                    'weight' => 7,
                    'category' => 'Content Freshness',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'freshness.twiddler',
                    'scoring_method' => 'freshness_boost',
                ],
            ],

            // ========================================
            // LinkSignals - Link Authority
            // ========================================
            'LinkSignals' => [
                'PageRankNS' => [
                    'description_en' => 'Namespace PageRank variant',
                    'description_kr' => '네임스페이스 PageRank 변형',
                    'weight' => 9,
                    'category' => 'Link Authority',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'links.pagerank_ns',
                    'scoring_method' => 'pr_score',
                ],
                'sourceType' => [
                    'description_en' => 'Anchor source quality (HIGH/MEDIUM/LOW)',
                    'description_kr' => '앵커 소스 품질',
                    'weight' => 8,
                    'category' => 'Link Quality',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'links.source_type',
                    'scoring_method' => 'anchor_quality',
                ],
                'IndyRank' => [
                    'description_en' => 'Independent rank calculation',
                    'description_kr' => '독립 랭크 계산',
                    'weight' => 7,
                    'category' => 'Link Authority',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'links.indyrank',
                    'scoring_method' => 'indy_score',
                ],
                'linkDiversity' => [
                    'description_en' => 'Link source diversity',
                    'description_kr' => '링크 소스 다양성',
                    'weight' => 8,
                    'category' => 'Link Quality',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'links.diversity',
                    'scoring_method' => 'diversity_score',
                ],
                'indexTier' => [
                    'description_en' => 'Link quality tier classification',
                    'description_kr' => '링크 품질 티어',
                    'weight' => 8,
                    'category' => 'Link Quality',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'links.tier',
                    'scoring_method' => 'tier_score',
                ],
            ],

            // ========================================
            // TechnicalSEO - Technical Factors
            // ========================================
            'TechnicalSEO' => [
                'crawlStatus' => [
                    'description_en' => 'Crawling status',
                    'description_kr' => '크롤링 상태',
                    'weight' => 9,
                    'category' => 'Crawlability',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'tech.crawl_status',
                    'scoring_method' => 'crawl_score',
                ],
                'indexable' => [
                    'description_en' => 'Indexability flag',
                    'description_kr' => '인덱싱 가능 여부',
                    'weight' => 10,
                    'category' => 'Indexability',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'tech.indexable',
                    'scoring_method' => 'index_score',
                ],
                'robotsMeta' => [
                    'description_en' => 'Robots meta tag',
                    'description_kr' => 'robots 메타 태그',
                    'weight' => 9,
                    'category' => 'Crawl Directives',
                    'impact' => self::IMPACT_VARIABLE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'tech.robots',
                    'scoring_method' => 'robots_score',
                ],
                'canonicalUrl' => [
                    'description_en' => 'Canonical URL setting',
                    'description_kr' => '캐노니컬 URL',
                    'weight' => 8,
                    'category' => 'Duplicate Content',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'tech.canonical',
                    'scoring_method' => 'canonical_score',
                ],
                'mobileScore' => [
                    'description_en' => 'Mobile optimization score',
                    'description_kr' => '모바일 최적화 점수',
                    'weight' => 8,
                    'category' => 'Mobile SEO',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'tech.mobile',
                    'scoring_method' => 'mobile_score',
                ],
                'pagespeed' => [
                    'description_en' => 'Page loading speed',
                    'description_kr' => '페이지 로딩 속도',
                    'weight' => 7,
                    'category' => 'User Experience',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'tech.speed',
                    'scoring_method' => 'speed_score',
                ],
                'httpsStatus' => [
                    'description_en' => 'HTTPS security status',
                    'description_kr' => 'HTTPS 보안 상태',
                    'weight' => 7,
                    'category' => 'Security',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'tech.https',
                    'scoring_method' => 'https_score',
                ],
                'clutterScore' => [
                    'description_en' => 'Page clutter and ads score',
                    'description_kr' => '페이지 혼잡도/광고 점수',
                    'weight' => 7,
                    'category' => 'User Experience',
                    'impact' => self::IMPACT_NEGATIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'tech.clutter',
                    'scoring_method' => 'clutter_penalty',
                ],
            ],

            // ========================================
            // Author - E-E-A-T Signals
            // ========================================
            'Author' => [
                'isAuthor' => [
                    'description_en' => 'Entity identified as author',
                    'description_kr' => '엔티티가 저자인지 여부',
                    'weight' => 7,
                    'category' => 'E-E-A-T',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'author.is_author',
                    'scoring_method' => 'author_exists',
                ],
                'author' => [
                    'description_en' => 'Document author text',
                    'description_kr' => '문서 저자 텍스트',
                    'weight' => 6,
                    'category' => 'E-E-A-T',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'author.text',
                    'scoring_method' => 'author_text',
                ],
                'authorEntity' => [
                    'description_en' => 'Author entity connection',
                    'description_kr' => '저자 엔티티 연결',
                    'weight' => 7,
                    'category' => 'E-E-A-T',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'author.entity',
                    'scoring_method' => 'entity_score',
                ],
            ],

            // ========================================
            // Schema - Structured Data
            // ========================================
            'Schema' => [
                'richSnippets' => [
                    'description_en' => 'Rich snippet eligibility',
                    'description_kr' => '리치 스니펫 적격성',
                    'weight' => 6,
                    'category' => 'Structured Data',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P2,
                    'algorithm_key' => 'schema.rich',
                    'scoring_method' => 'rich_eligible',
                ],
                'schemaOrg' => [
                    'description_en' => 'Schema.org markup presence',
                    'description_kr' => 'Schema.org 마크업 존재',
                    'weight' => 7,
                    'category' => 'Structured Data',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'schema.org',
                    'scoring_method' => 'schema_score',
                ],
                'faqSchema' => [
                    'description_en' => 'FAQ schema markup',
                    'description_kr' => 'FAQ 스키마 마크업',
                    'weight' => 6,
                    'category' => 'Structured Data',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P2,
                    'algorithm_key' => 'schema.faq',
                    'scoring_method' => 'faq_score',
                ],
                'productSchema' => [
                    'description_en' => 'Product schema markup',
                    'description_kr' => '제품 스키마 마크업',
                    'weight' => 7,
                    'category' => 'Structured Data',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'schema.product',
                    'scoring_method' => 'product_schema',
                ],
                'reviewSchema' => [
                    'description_en' => 'Review schema markup',
                    'description_kr' => '리뷰 스키마 마크업',
                    'weight' => 7,
                    'category' => 'Structured Data',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'schema.review',
                    'scoring_method' => 'review_schema',
                ],
                'localBusinessSchema' => [
                    'description_en' => 'Local business schema',
                    'description_kr' => '로컬 비즈니스 스키마',
                    'weight' => 8,
                    'category' => 'Local SEO',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'schema.local',
                    'scoring_method' => 'local_schema',
                ],
            ],

            // ========================================
            // Demotions - Penalty Signals
            // ========================================
            'Demotions' => [
                'exactMatchDomainDemotion' => [
                    'description_en' => 'Exact match domain penalty',
                    'description_kr' => '정확 일치 도메인 페널티',
                    'weight' => 7,
                    'category' => 'Domain Penalty',
                    'impact' => self::IMPACT_NEGATIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'demotion.emd',
                    'scoring_method' => 'emd_penalty',
                ],
                'spamBrain' => [
                    'description_en' => 'AI spam detection system',
                    'description_kr' => 'AI 스팸 감지 시스템',
                    'weight' => 9,
                    'category' => 'Spam Detection',
                    'impact' => self::IMPACT_NEGATIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'demotion.spambrain',
                    'scoring_method' => 'spam_score',
                ],
                'manualAction' => [
                    'description_en' => 'Manual penalty',
                    'description_kr' => '수동 페널티',
                    'weight' => 10,
                    'category' => 'Penalty',
                    'impact' => self::IMPACT_NEGATIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'demotion.manual',
                    'scoring_method' => 'manual_penalty',
                ],
            ],
        ];
    }

    /**
     * Get all ranking factors
     */
    public static function get_all_factors() {
        self::init();
        return self::$factors;
    }

    /**
     * Get factors by module
     */
    public static function get_module($module_name) {
        self::init();
        return self::$factors[$module_name] ?? [];
    }

    /**
     * Get factors by priority
     */
    public static function get_by_priority($priority) {
        self::init();
        $result = [];

        foreach (self::$factors as $module => $factors) {
            foreach ($factors as $name => $data) {
                if ($data['priority'] === $priority) {
                    $result[$module . '.' . $name] = $data;
                }
            }
        }

        return $result;
    }

    /**
     * Get factors by category
     */
    public static function get_by_category($category) {
        self::init();
        $result = [];

        foreach (self::$factors as $module => $factors) {
            foreach ($factors as $name => $data) {
                if ($data['category'] === $category) {
                    $result[$module . '.' . $name] = $data;
                }
            }
        }

        return $result;
    }

    /**
     * Get factors by impact type
     */
    public static function get_by_impact($impact) {
        self::init();
        $result = [];

        foreach (self::$factors as $module => $factors) {
            foreach ($factors as $name => $data) {
                if ($data['impact'] === $impact) {
                    $result[$module . '.' . $name] = $data;
                }
            }
        }

        return $result;
    }

    /**
     * Get all P0 (critical) factors
     */
    public static function get_critical_factors() {
        return self::get_by_priority(self::PRIORITY_P0);
    }

    /**
     * Get all negative (demotion) factors
     */
    public static function get_demotion_factors() {
        return self::get_by_impact(self::IMPACT_NEGATIVE);
    }

    /**
     * Get factor by algorithm key
     */
    public static function get_by_algorithm_key($key) {
        self::init();

        foreach (self::$factors as $module => $factors) {
            foreach ($factors as $name => $data) {
                if ($data['algorithm_key'] === $key) {
                    return array_merge($data, [
                        'module' => $module,
                        'name' => $name,
                    ]);
                }
            }
        }

        return null;
    }

    /**
     * Get all unique categories
     */
    public static function get_categories() {
        self::init();
        $categories = [];

        foreach (self::$factors as $module => $factors) {
            foreach ($factors as $name => $data) {
                $categories[] = $data['category'];
            }
        }

        return array_unique($categories);
    }

    /**
     * Get category weights (sum of factor weights per category)
     */
    public static function get_category_weights() {
        self::init();
        $weights = [];

        foreach (self::$factors as $module => $factors) {
            foreach ($factors as $name => $data) {
                $category = $data['category'];
                if (!isset($weights[$category])) {
                    $weights[$category] = 0;
                }
                $weights[$category] += $data['weight'];
            }
        }

        arsort($weights);
        return $weights;
    }

    /**
     * Export to CSV format
     */
    public static function export_to_csv() {
        self::init();

        $csv = "ModuleName,AttributeName,DescriptionEN,DescriptionKR,Weight,Category,Impact,Priority,AlgorithmKey,ScoringMethod\n";

        foreach (self::$factors as $module => $factors) {
            foreach ($factors as $name => $data) {
                $csv .= sprintf(
                    "%s,%s,\"%s\",\"%s\",%d,%s,%s,%s,%s,%s\n",
                    $module,
                    $name,
                    str_replace('"', '""', $data['description_en']),
                    str_replace('"', '""', $data['description_kr']),
                    $data['weight'],
                    $data['category'],
                    $data['impact'],
                    $data['priority'],
                    $data['algorithm_key'],
                    $data['scoring_method']
                );
            }
        }

        return $csv;
    }

    /**
     * Get factor count statistics
     */
    public static function get_statistics() {
        self::init();

        $stats = [
            'total_factors' => 0,
            'by_priority' => [
                'P0' => 0,
                'P1' => 0,
                'P2' => 0,
                'P3' => 0,
            ],
            'by_impact' => [
                'Positive' => 0,
                'Negative' => 0,
                'Neutral' => 0,
                'Variable' => 0,
            ],
            'total_weight' => 0,
            'avg_weight' => 0,
        ];

        foreach (self::$factors as $module => $factors) {
            foreach ($factors as $name => $data) {
                $stats['total_factors']++;
                $stats['by_priority'][$data['priority']]++;
                $stats['by_impact'][$data['impact']]++;
                $stats['total_weight'] += $data['weight'];
            }
        }

        $stats['avg_weight'] = $stats['total_factors'] > 0
            ? round($stats['total_weight'] / $stats['total_factors'], 2)
            : 0;

        return $stats;
    }
}

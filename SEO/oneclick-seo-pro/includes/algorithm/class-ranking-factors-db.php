<?php
/**
 * WP Bulk SEO - Ranking Factors Database
 *
 * Programmatic access to the Google ranking factors database.
 * Based on Google Algorithm Leak Analysis (May 2024) + PageSpeed API v5.
 *
 * @package OneClick_SEO_Pro
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class OneClick_SEO_Pro_Ranking_Factors_DB {

    /**
     * Ranking factors database
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
     * API Sources
     */
    const API_SEARCH_CONSOLE = 'Search Console API';
    const API_PAGESPEED = 'PageSpeed API';
    const API_INTERNAL = 'Internal Analysis';

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
                    'api_source' => self::API_SEARCH_CONSOLE,
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
                    'api_source' => self::API_SEARCH_CONSOLE,
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
                    'api_source' => self::API_SEARCH_CONSOLE,
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
                    'api_source' => self::API_SEARCH_CONSOLE,
                ],
                'impressions' => [
                    'description_en' => 'Search result impressions count',
                    'description_kr' => '검색 결과 노출 횟수',
                    'weight' => 8,
                    'category' => 'Visibility',
                    'impact' => self::IMPACT_NEUTRAL,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'navboost.impressions',
                    'scoring_method' => 'visibility_score',
                    'api_source' => self::API_SEARCH_CONSOLE,
                ],
                'position' => [
                    'description_en' => 'Average search ranking position',
                    'description_kr' => '평균 검색 순위',
                    'weight' => 8,
                    'category' => 'User Engagement',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'navboost.position',
                    'scoring_method' => 'position_score',
                    'api_source' => self::API_SEARCH_CONSOLE,
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
                    'api_source' => self::API_SEARCH_CONSOLE,
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
                    'api_source' => self::API_INTERNAL,
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
                    'api_source' => self::API_INTERNAL,
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
                    'api_source' => self::API_INTERNAL,
                    'max_score' => 512,
                ],
            ],

            // ========================================
            // CoreWebVitals - PageSpeed API Metrics
            // ========================================
            'CoreWebVitals' => [
                'LCP' => [
                    'description_en' => 'Largest Contentful Paint - main content load time',
                    'description_kr' => '최대 콘텐츠풀 페인트 - 메인 콘텐츠 로딩 시간',
                    'weight' => 9,
                    'category' => 'Page Speed',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'cwv.lcp',
                    'scoring_method' => 'lcp_score',
                    'api_source' => self::API_PAGESPEED,
                    'threshold_good' => 2500,
                    'threshold_poor' => 4000,
                ],
                'FID' => [
                    'description_en' => 'First Input Delay - interactivity response time',
                    'description_kr' => '첫 입력 지연 - 상호작용 응답 시간',
                    'weight' => 9,
                    'category' => 'Page Speed',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'cwv.fid',
                    'scoring_method' => 'fid_score',
                    'api_source' => self::API_PAGESPEED,
                    'threshold_good' => 100,
                    'threshold_poor' => 300,
                ],
                'CLS' => [
                    'description_en' => 'Cumulative Layout Shift - visual stability',
                    'description_kr' => '누적 레이아웃 이동 - 시각적 안정성',
                    'weight' => 9,
                    'category' => 'Page Speed',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'cwv.cls',
                    'scoring_method' => 'cls_score',
                    'api_source' => self::API_PAGESPEED,
                    'threshold_good' => 0.1,
                    'threshold_poor' => 0.25,
                ],
                'FCP' => [
                    'description_en' => 'First Contentful Paint - first render time',
                    'description_kr' => '첫 콘텐츠풀 페인트 - 첫 렌더링 시간',
                    'weight' => 9,
                    'category' => 'Page Speed',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'cwv.fcp',
                    'scoring_method' => 'fcp_score',
                    'api_source' => self::API_PAGESPEED,
                    'threshold_good' => 1800,
                    'threshold_poor' => 3000,
                ],
                'TTFB' => [
                    'description_en' => 'Time to First Byte - server response time',
                    'description_kr' => '첫 바이트 수신 시간 - 서버 응답 시간',
                    'weight' => 8,
                    'category' => 'Page Speed',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'cwv.ttfb',
                    'scoring_method' => 'ttfb_score',
                    'api_source' => self::API_PAGESPEED,
                    'threshold_good' => 800,
                    'threshold_poor' => 1800,
                ],
                'TBT' => [
                    'description_en' => 'Total Blocking Time - JavaScript execution blocking',
                    'description_kr' => '총 차단 시간 - JavaScript 실행 차단',
                    'weight' => 8,
                    'category' => 'Page Speed',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'cwv.tbt',
                    'scoring_method' => 'tbt_score',
                    'api_source' => self::API_PAGESPEED,
                    'threshold_good' => 200,
                    'threshold_poor' => 600,
                ],
            ],

            // ========================================
            // Lighthouse - Performance Scores
            // ========================================
            'Lighthouse' => [
                'performanceScore' => [
                    'description_en' => 'Lighthouse Performance Score (0-100)',
                    'description_kr' => 'Lighthouse 성능 점수 (0-100)',
                    'weight' => 9,
                    'category' => 'Page Speed',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'lighthouse.performance',
                    'scoring_method' => 'performance_score',
                    'api_source' => self::API_PAGESPEED,
                ],
                'seoScore' => [
                    'description_en' => 'Lighthouse SEO Score (0-100)',
                    'description_kr' => 'Lighthouse SEO 점수 (0-100)',
                    'weight' => 9,
                    'category' => 'SEO Score',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'lighthouse.seo',
                    'scoring_method' => 'seo_audit_score',
                    'api_source' => self::API_PAGESPEED,
                ],
                'accessibilityScore' => [
                    'description_en' => 'Lighthouse Accessibility Score (0-100)',
                    'description_kr' => 'Lighthouse 접근성 점수 (0-100)',
                    'weight' => 8,
                    'category' => 'Accessibility',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'lighthouse.accessibility',
                    'scoring_method' => 'accessibility_score',
                    'api_source' => self::API_PAGESPEED,
                ],
                'bestPracticesScore' => [
                    'description_en' => 'Lighthouse Best Practices Score (0-100)',
                    'description_kr' => 'Lighthouse 모범 사례 점수 (0-100)',
                    'weight' => 8,
                    'category' => 'Best Practices',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'lighthouse.best_practices',
                    'scoring_method' => 'best_practices_score',
                    'api_source' => self::API_PAGESPEED,
                ],
            ],

            // ========================================
            // TechnicalSEO - Technical Factors
            // ========================================
            'TechnicalSEO' => [
                'indexable' => [
                    'description_en' => 'Indexability flag',
                    'description_kr' => '인덱싱 가능 여부',
                    'weight' => 10,
                    'category' => 'Indexability',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'tech.indexable',
                    'scoring_method' => 'index_score',
                    'api_source' => self::API_SEARCH_CONSOLE,
                ],
                'mobileScore' => [
                    'description_en' => 'Mobile optimization score',
                    'description_kr' => '모바일 최적화 점수',
                    'weight' => 10,
                    'category' => 'Mobile SEO',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'tech.mobile',
                    'scoring_method' => 'mobile_score',
                    'api_source' => self::API_PAGESPEED,
                ],
                'mobileUsability' => [
                    'description_en' => 'Mobile usability inspection result',
                    'description_kr' => '모바일 사용성 검사 결과',
                    'weight' => 9,
                    'category' => 'Mobile SEO',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'tech.mobile_usability',
                    'scoring_method' => 'usability_score',
                    'api_source' => self::API_SEARCH_CONSOLE,
                ],
                'crawlStatus' => [
                    'description_en' => 'Crawling status',
                    'description_kr' => '크롤링 상태',
                    'weight' => 9,
                    'category' => 'Crawlability',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'tech.crawl_status',
                    'scoring_method' => 'crawl_score',
                    'api_source' => self::API_SEARCH_CONSOLE,
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
                    'api_source' => self::API_INTERNAL,
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
                    'api_source' => self::API_INTERNAL,
                ],
                'httpsStatus' => [
                    'description_en' => 'HTTPS security status',
                    'description_kr' => 'HTTPS 보안 상태',
                    'weight' => 8,
                    'category' => 'Security',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'tech.https',
                    'scoring_method' => 'https_score',
                    'api_source' => self::API_PAGESPEED,
                ],
                'pagespeed' => [
                    'description_en' => 'Page loading speed',
                    'description_kr' => '페이지 로딩 속도',
                    'weight' => 8,
                    'category' => 'User Experience',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'tech.speed',
                    'scoring_method' => 'speed_score',
                    'api_source' => self::API_PAGESPEED,
                ],
            ],

            // ========================================
            // SiteTopics - Topical Authority
            // ========================================
            'SiteTopics' => [
                'siteFocusScore' => [
                    'description_en' => 'Topic focus concentration score',
                    'description_kr' => '주제 집중도 점수',
                    'weight' => 8,
                    'category' => 'Topical Authority',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'topics.focus',
                    'scoring_method' => 'topic_focus',
                    'api_source' => self::API_INTERNAL,
                ],
                'siteEmbeddings' => [
                    'description_en' => 'Vector embeddings of site core topics',
                    'description_kr' => '사이트 핵심 주제 벡터 임베딩',
                    'weight' => 8,
                    'category' => 'Topical Authority',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'topics.embeddings',
                    'scoring_method' => 'topic_vector',
                    'api_source' => self::API_INTERNAL,
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
                    'api_source' => self::API_INTERNAL,
                ],
            ],

            // ========================================
            // ContentAnalysis - On-Page SEO
            // ========================================
            'ContentAnalysis' => [
                'titlematchScore' => [
                    'description_en' => 'Title to query match score',
                    'description_kr' => '제목과 쿼리 일치도',
                    'weight' => 8,
                    'category' => 'On-Page SEO',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'content.title_match',
                    'scoring_method' => 'title_score',
                    'api_source' => self::API_INTERNAL,
                ],
                'keywordStuffingScore' => [
                    'description_en' => 'Keyword over-optimization penalty',
                    'description_kr' => '키워드 과다 최적화 페널티',
                    'weight' => 8,
                    'category' => 'Spam Detection',
                    'impact' => self::IMPACT_NEGATIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'content.keyword_stuffing',
                    'scoring_method' => 'stuffing_penalty',
                    'api_source' => self::API_INTERNAL,
                ],
                'avgTermWeight' => [
                    'description_en' => 'Average weighted font size of terms',
                    'description_kr' => '용어의 평균 가중 폰트 크기',
                    'weight' => 7,
                    'category' => 'On-Page SEO',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'content.term_weight',
                    'scoring_method' => 'emphasis_score',
                    'api_source' => self::API_INTERNAL,
                ],
                'contentDepth' => [
                    'description_en' => 'Content depth and comprehensiveness',
                    'description_kr' => '콘텐츠 깊이와 포괄성',
                    'weight' => 8,
                    'category' => 'Content Quality',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'content.depth',
                    'scoring_method' => 'depth_score',
                    'api_source' => self::API_INTERNAL,
                ],
                'readability' => [
                    'description_en' => 'Reading ease and accessibility',
                    'description_kr' => '가독성과 접근성',
                    'weight' => 6,
                    'category' => 'Content Quality',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P2,
                    'algorithm_key' => 'content.readability',
                    'scoring_method' => 'readability_score',
                    'api_source' => self::API_INTERNAL,
                ],
            ],

            // ========================================
            // Freshness - Content Freshness
            // ========================================
            'Freshness' => [
                'lastSignificantUpdate' => [
                    'description_en' => 'Last meaningful content update',
                    'description_kr' => '마지막 의미있는 업데이트',
                    'weight' => 8,
                    'category' => 'Content Freshness',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'freshness.last_update',
                    'scoring_method' => 'freshness_score',
                    'api_source' => self::API_INTERNAL,
                ],
                'semanticDate' => [
                    'description_en' => 'Date derived from content context',
                    'description_kr' => '콘텐츠에서 해석된 날짜',
                    'weight' => 7,
                    'category' => 'Content Freshness',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'freshness.semantic',
                    'scoring_method' => 'content_date',
                    'api_source' => self::API_INTERNAL,
                ],
                'bylineDate' => [
                    'description_en' => 'Explicitly set publication date',
                    'description_kr' => '명시적 작성 날짜',
                    'weight' => 7,
                    'category' => 'Content Freshness',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'freshness.byline',
                    'scoring_method' => 'date_clarity',
                    'api_source' => self::API_INTERNAL,
                ],
            ],

            // ========================================
            // LinkSignals - Link Authority
            // ========================================
            'LinkSignals' => [
                'PageRankNS' => [
                    'description_en' => 'Namespace PageRank variant',
                    'description_kr' => '네임스페이스 PageRank 변형',
                    'weight' => 10,
                    'category' => 'Link Authority',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'links.pagerank_ns',
                    'scoring_method' => 'pr_score',
                    'api_source' => self::API_INTERNAL,
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
                    'api_source' => self::API_INTERNAL,
                ],
                'backlinkQuality' => [
                    'description_en' => 'Overall backlink profile quality',
                    'description_kr' => '백링크 프로필 품질',
                    'weight' => 8,
                    'category' => 'Link SEO',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'links.backlink_quality',
                    'scoring_method' => 'backlink_score',
                    'api_source' => self::API_INTERNAL,
                ],
                'internalLinking' => [
                    'description_en' => 'Internal link structure quality',
                    'description_kr' => '내부 링크 구조 품질',
                    'weight' => 7,
                    'category' => 'On-Page SEO',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'links.internal',
                    'scoring_method' => 'internal_link_score',
                    'api_source' => self::API_INTERNAL,
                ],
            ],

            // ========================================
            // Author - E-E-A-T Signals
            // ========================================
            'Author' => [
                'authorEntity' => [
                    'description_en' => 'Author entity connection',
                    'description_kr' => '저자 엔티티 연결',
                    'weight' => 7,
                    'category' => 'E-E-A-T',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'author.entity',
                    'scoring_method' => 'entity_score',
                    'api_source' => self::API_INTERNAL,
                ],
                'authorCredentials' => [
                    'description_en' => 'Author credentials visibility',
                    'description_kr' => '저자 자격 가시성',
                    'weight' => 7,
                    'category' => 'E-E-A-T',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'eeat.author',
                    'scoring_method' => 'author_cred_score',
                    'api_source' => self::API_INTERNAL,
                ],
                'trustSignals' => [
                    'description_en' => 'Trust indicators presence',
                    'description_kr' => '신뢰 지표 존재',
                    'weight' => 8,
                    'category' => 'E-E-A-T',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'eeat.trust',
                    'scoring_method' => 'trust_indicator_score',
                    'api_source' => self::API_INTERNAL,
                ],
            ],

            // ========================================
            // Schema - Structured Data
            // ========================================
            'Schema' => [
                'schemaOrg' => [
                    'description_en' => 'Schema.org markup presence',
                    'description_kr' => 'Schema.org 마크업 존재',
                    'weight' => 7,
                    'category' => 'Structured Data',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'schema.org',
                    'scoring_method' => 'schema_score',
                    'api_source' => self::API_PAGESPEED,
                ],
                'richSnippets' => [
                    'description_en' => 'Rich snippet eligibility',
                    'description_kr' => '리치 스니펫 적격성',
                    'weight' => 7,
                    'category' => 'Structured Data',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'schema.rich',
                    'scoring_method' => 'rich_eligible',
                    'api_source' => self::API_SEARCH_CONSOLE,
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
                    'api_source' => self::API_INTERNAL,
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
                    'api_source' => self::API_INTERNAL,
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
                    'api_source' => self::API_INTERNAL,
                ],
            ],

            // ========================================
            // Brand - Brand Signals
            // ========================================
            'Brand' => [
                'brandRecognition' => [
                    'description_en' => 'Brand recognition and signals',
                    'description_kr' => '브랜드 인지도 및 신호',
                    'weight' => 9,
                    'category' => 'Social Signals',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'brand.recognition',
                    'scoring_method' => 'brand_score',
                    'api_source' => self::API_INTERNAL,
                ],
                'socialSignals' => [
                    'description_en' => 'Social media presence and engagement',
                    'description_kr' => '소셜 미디어 존재감 및 참여',
                    'weight' => 6,
                    'category' => 'Social Signals',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P2,
                    'algorithm_key' => 'brand.social',
                    'scoring_method' => 'social_score',
                    'api_source' => self::API_INTERNAL,
                ],
            ],

            // ========================================
            // Demotions - Penalty Signals
            // ========================================
            'Demotions' => [
                'spamBrain' => [
                    'description_en' => 'AI spam detection system',
                    'description_kr' => 'AI 스팸 감지 시스템',
                    'weight' => 9,
                    'category' => 'Spam Detection',
                    'impact' => self::IMPACT_NEGATIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'demotion.spambrain',
                    'scoring_method' => 'spam_score',
                    'api_source' => self::API_INTERNAL,
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
                    'api_source' => self::API_SEARCH_CONSOLE,
                ],
                'productReviewDemotion' => [
                    'description_en' => 'Low-quality product review penalty',
                    'description_kr' => '저품질 제품 리뷰 페널티',
                    'weight' => 8,
                    'category' => 'Content Quality',
                    'impact' => self::IMPACT_NEGATIVE,
                    'priority' => self::PRIORITY_P0,
                    'algorithm_key' => 'demotion.review',
                    'scoring_method' => 'review_penalty',
                    'api_source' => self::API_INTERNAL,
                ],
            ],

            // ========================================
            // LocalSEO - Local Business
            // ========================================
            'LocalSEO' => [
                'localBusiness' => [
                    'description_en' => 'Local business optimization',
                    'description_kr' => '로컬 비즈니스 최적화',
                    'weight' => 8,
                    'category' => 'Local SEO',
                    'impact' => self::IMPACT_POSITIVE,
                    'priority' => self::PRIORITY_P1,
                    'algorithm_key' => 'local.business',
                    'scoring_method' => 'local_business_score',
                    'api_source' => self::API_INTERNAL,
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
                    $result[$module . '.' . $name] = array_merge($data, [
                        'module' => $module,
                        'name' => $name,
                    ]);
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
                    $result[$module . '.' . $name] = array_merge($data, [
                        'module' => $module,
                        'name' => $name,
                    ]);
                }
            }
        }

        return $result;
    }

    /**
     * Get factors by API source
     */
    public static function get_by_api_source($source) {
        self::init();
        $result = [];

        foreach (self::$factors as $module => $factors) {
            foreach ($factors as $name => $data) {
                if (isset($data['api_source']) && $data['api_source'] === $source) {
                    $result[$module . '.' . $name] = array_merge($data, [
                        'module' => $module,
                        'name' => $name,
                    ]);
                }
            }
        }

        return $result;
    }

    /**
     * Get Core Web Vitals factors
     */
    public static function get_core_web_vitals() {
        return self::get_module('CoreWebVitals');
    }

    /**
     * Get Lighthouse factors
     */
    public static function get_lighthouse_factors() {
        return self::get_module('Lighthouse');
    }

    /**
     * Get PageSpeed API factors
     */
    public static function get_pagespeed_factors() {
        return self::get_by_api_source(self::API_PAGESPEED);
    }

    /**
     * Get Search Console API factors
     */
    public static function get_search_console_factors() {
        return self::get_by_api_source(self::API_SEARCH_CONSOLE);
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
        self::init();
        $result = [];

        foreach (self::$factors as $module => $factors) {
            foreach ($factors as $name => $data) {
                if ($data['impact'] === self::IMPACT_NEGATIVE) {
                    $result[$module . '.' . $name] = array_merge($data, [
                        'module' => $module,
                        'name' => $name,
                    ]);
                }
            }
        }

        return $result;
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
     * Get category weights
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
     * Get statistics
     */
    public static function get_statistics() {
        self::init();

        $stats = [
            'total_factors' => 0,
            'by_priority' => ['P0' => 0, 'P1' => 0, 'P2' => 0, 'P3' => 0],
            'by_impact' => ['Positive' => 0, 'Negative' => 0, 'Neutral' => 0, 'Variable' => 0],
            'by_api_source' => [
                'Search Console API' => 0,
                'PageSpeed API' => 0,
                'Internal Analysis' => 0,
            ],
            'total_weight' => 0,
            'avg_weight' => 0,
        ];

        foreach (self::$factors as $module => $factors) {
            foreach ($factors as $name => $data) {
                $stats['total_factors']++;
                $stats['by_priority'][$data['priority']]++;
                $stats['by_impact'][$data['impact']] = ($stats['by_impact'][$data['impact']] ?? 0) + 1;
                $stats['total_weight'] += $data['weight'];

                if (isset($data['api_source'])) {
                    $stats['by_api_source'][$data['api_source']]++;
                }
            }
        }

        $stats['avg_weight'] = $stats['total_factors'] > 0
            ? round($stats['total_weight'] / $stats['total_factors'], 2)
            : 0;

        return $stats;
    }

    /**
     * Export to CSV format
     */
    public static function export_to_csv() {
        self::init();

        $csv = "ModuleName,AttributeName,DescriptionEN,DescriptionKR,Weight,Category,Impact,Priority,AlgorithmKey,ScoringMethod,APISource\n";

        foreach (self::$factors as $module => $factors) {
            foreach ($factors as $name => $data) {
                $csv .= sprintf(
                    "%s,%s,\"%s\",\"%s\",%d,%s,%s,%s,%s,%s,%s\n",
                    $module,
                    $name,
                    str_replace('"', '""', $data['description_en']),
                    str_replace('"', '""', $data['description_kr']),
                    $data['weight'],
                    $data['category'],
                    $data['impact'],
                    $data['priority'],
                    $data['algorithm_key'],
                    $data['scoring_method'],
                    $data['api_source'] ?? ''
                );
            }
        }

        return $csv;
    }
}

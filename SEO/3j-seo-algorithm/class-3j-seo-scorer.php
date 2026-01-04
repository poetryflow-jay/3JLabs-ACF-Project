<?php
/**
 * 3J Labs SEO Scoring Algorithm
 *
 * Based on Google Algorithm Leak Analysis (May 2024)
 * Implements scoring based on NavBoost, CompressedQualitySignals,
 * SiteTopics, and other confirmed ranking factors.
 *
 * @package 3J_SEO_Algorithm
 * @version 1.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class ThreeJ_SEO_Scorer {

    /**
     * Weight configurations based on Google ranking factor analysis
     * Scale: 1-10 (10 = highest impact)
     */
    private const WEIGHT_CONFIG = [
        // NavBoost Signals (User Engagement) - Highest Priority
        'navboost' => [
            'good_clicks'          => 10,  // Long dwell time clicks
            'bad_clicks'           => -10, // Pogo-sticking penalty
            'last_longest_clicks'  => 10,  // Session completion signal
            'ctr'                  => 9,   // Click-through rate
            'impressions'          => 7,   // Visibility
        ],

        // CompressedQualitySignals - Domain Authority
        'quality_signals' => [
            'site_authority'       => 10,  // Domain trust/authority
            'panda_demotion'       => -9,  // Low-quality content penalty
            'nav_demotion'         => -8,  // UX issues penalty
            'original_content'     => 9,   // Content originality (0-512)
        ],

        // Technical SEO Factors
        'technical' => [
            'indexable'            => 10,  // Must be indexable
            'crawl_status'         => 9,   // Crawlability
            'mobile_score'         => 8,   // Mobile optimization
            'page_speed'           => 7,   // Loading speed
            'https_status'         => 7,   // Security
            'canonical_url'        => 8,   // Duplicate content handling
            'robots_meta'          => 9,   // Crawl directives
        ],

        // On-Page SEO Factors
        'on_page' => [
            'title_match'          => 8,   // Title to query match
            'keyword_stuffing'     => -8,  // Over-optimization penalty
            'avg_term_weight'      => 5,   // Font size emphasis
            'content_length'       => 5,   // Token count
        ],

        // Link Signals
        'links' => [
            'pagerank_ns'          => 9,   // Namespace PageRank
            'source_type'          => 8,   // Anchor source quality
            'link_diversity'       => 8,   // Source diversity
            'index_tier'           => 8,   // Link quality tier
            'anchor_mismatch'      => -7,  // Anchor-target mismatch
            'spam_anchor_ratio'    => -8,  // Spam anchor penalty
        ],

        // Topical Authority (SiteTopics)
        'topical' => [
            'site_embeddings'      => 8,   // Topic vector embeddings
            'site_focus_score'     => 8,   // Topic concentration
            'site_radius'          => -7,  // Topic deviation penalty
        ],

        // Freshness Signals
        'freshness' => [
            'last_significant_update' => 8,  // Recent meaningful update
            'byline_date'            => 7,   // Publication date
            'syntactic_date'         => 6,   // URL/title date
            'semantic_date'          => 6,   // Content context date
        ],

        // E-E-A-T Signals
        'eeat' => [
            'author_entity'        => 7,   // Author entity connection
            'is_author'            => 7,   // Identified author
            'author_text'          => 6,   // Author text presence
        ],

        // Structured Data (Schema)
        'schema' => [
            'schema_org'           => 7,   // Schema.org markup
            'product_schema'       => 7,   // Product schema
            'review_schema'        => 7,   // Review schema
            'local_business'       => 8,   // Local SEO schema
            'faq_schema'           => 6,   // FAQ schema
            'rich_snippets'        => 6,   // Rich snippet eligibility
        ],

        // Demotions
        'demotions' => [
            'spam_brain'           => -9,  // AI spam detection
            'manual_action'        => -10, // Manual penalty
            'emd_demotion'         => -7,  // Exact match domain
            'product_review_demotion' => -8, // Low-quality review
        ],
    ];

    /**
     * Priority levels for implementation
     */
    private const PRIORITY_LEVELS = [
        'P0' => 1.0,   // Critical - must implement
        'P1' => 0.8,   // High priority
        'P2' => 0.5,   // Medium priority
        'P3' => 0.3,   // Low priority
    ];

    /**
     * SEO Category definitions
     */
    private const SEO_CATEGORIES = [
        'User Engagement',
        'Domain Authority',
        'Content Quality',
        'Technical SEO',
        'On-Page SEO',
        'Link Authority',
        'Link Quality',
        'Topical Authority',
        'Content Freshness',
        'E-E-A-T',
        'Structured Data',
        'Mobile SEO',
        'Security',
        'Spam Detection',
        'Local SEO',
        'YMYL',
    ];

    /**
     * Analyze a page and return SEO score
     *
     * @param array $page_data Page analysis data
     * @return array Comprehensive SEO score report
     */
    public function analyze_page($page_data) {
        $scores = [];
        $total_weight = 0;
        $total_score = 0;

        // Technical SEO Analysis
        $scores['technical'] = $this->analyze_technical($page_data);

        // On-Page SEO Analysis
        $scores['on_page'] = $this->analyze_on_page($page_data);

        // Content Quality Analysis
        $scores['content'] = $this->analyze_content($page_data);

        // Link Profile Analysis
        $scores['links'] = $this->analyze_links($page_data);

        // Topical Authority Analysis
        $scores['topical'] = $this->analyze_topical($page_data);

        // Freshness Analysis
        $scores['freshness'] = $this->analyze_freshness($page_data);

        // E-E-A-T Analysis
        $scores['eeat'] = $this->analyze_eeat($page_data);

        // Schema/Structured Data Analysis
        $scores['schema'] = $this->analyze_schema($page_data);

        // User Engagement Estimation
        $scores['engagement'] = $this->estimate_engagement($page_data);

        // Demotion Risk Analysis
        $scores['demotions'] = $this->analyze_demotion_risks($page_data);

        // Calculate overall score
        foreach ($scores as $category => $category_scores) {
            foreach ($category_scores['factors'] as $factor) {
                $weight = abs($factor['weight']);
                $total_weight += $weight;
                $total_score += $factor['score'] * $weight;
            }
        }

        $overall_score = $total_weight > 0 ? round($total_score / $total_weight, 1) : 0;

        return [
            'overall_score' => $overall_score,
            'grade' => $this->get_grade($overall_score),
            'categories' => $scores,
            'recommendations' => $this->generate_recommendations($scores),
            'priority_actions' => $this->get_priority_actions($scores),
            'timestamp' => current_time('mysql'),
        ];
    }

    /**
     * Analyze technical SEO factors
     */
    private function analyze_technical($data) {
        $factors = [];

        // Indexability Check
        $factors[] = [
            'name' => 'indexable',
            'label' => 'Indexability',
            'label_kr' => '인덱싱 가능 여부',
            'score' => $this->check_indexable($data),
            'weight' => self::WEIGHT_CONFIG['technical']['indexable'],
            'priority' => 'P0',
        ];

        // Crawl Status
        $factors[] = [
            'name' => 'crawl_status',
            'label' => 'Crawl Status',
            'label_kr' => '크롤링 상태',
            'score' => $this->check_crawl_status($data),
            'weight' => self::WEIGHT_CONFIG['technical']['crawl_status'],
            'priority' => 'P0',
        ];

        // Mobile Score
        $factors[] = [
            'name' => 'mobile_score',
            'label' => 'Mobile Optimization',
            'label_kr' => '모바일 최적화',
            'score' => $data['mobile_score'] ?? 0,
            'weight' => self::WEIGHT_CONFIG['technical']['mobile_score'],
            'priority' => 'P0',
        ];

        // Page Speed
        $factors[] = [
            'name' => 'page_speed',
            'label' => 'Page Speed',
            'label_kr' => '페이지 속도',
            'score' => $this->calculate_speed_score($data),
            'weight' => self::WEIGHT_CONFIG['technical']['page_speed'],
            'priority' => 'P1',
        ];

        // HTTPS Status
        $factors[] = [
            'name' => 'https_status',
            'label' => 'HTTPS Security',
            'label_kr' => 'HTTPS 보안',
            'score' => ($data['is_https'] ?? false) ? 100 : 0,
            'weight' => self::WEIGHT_CONFIG['technical']['https_status'],
            'priority' => 'P1',
        ];

        // Canonical URL
        $factors[] = [
            'name' => 'canonical_url',
            'label' => 'Canonical URL',
            'label_kr' => '캐노니컬 URL',
            'score' => $this->check_canonical($data),
            'weight' => self::WEIGHT_CONFIG['technical']['canonical_url'],
            'priority' => 'P0',
        ];

        // Robots Meta
        $factors[] = [
            'name' => 'robots_meta',
            'label' => 'Robots Meta',
            'label_kr' => '로봇 메타 태그',
            'score' => $this->check_robots_meta($data),
            'weight' => self::WEIGHT_CONFIG['technical']['robots_meta'],
            'priority' => 'P0',
        ];

        return [
            'category' => 'Technical SEO',
            'category_kr' => '기술적 SEO',
            'score' => $this->calculate_category_score($factors),
            'factors' => $factors,
        ];
    }

    /**
     * Analyze on-page SEO factors
     */
    private function analyze_on_page($data) {
        $factors = [];

        // Title Match Score
        $factors[] = [
            'name' => 'title_match',
            'label' => 'Title Optimization',
            'label_kr' => '제목 최적화',
            'score' => $this->calculate_title_score($data),
            'weight' => self::WEIGHT_CONFIG['on_page']['title_match'],
            'priority' => 'P0',
        ];

        // Keyword Stuffing Check
        $keyword_stuffing = $this->check_keyword_stuffing($data);
        $factors[] = [
            'name' => 'keyword_stuffing',
            'label' => 'Keyword Density',
            'label_kr' => '키워드 밀도',
            'score' => 100 - $keyword_stuffing, // Inverse - lower stuffing = higher score
            'weight' => self::WEIGHT_CONFIG['on_page']['keyword_stuffing'],
            'priority' => 'P0',
            'is_penalty' => $keyword_stuffing > 30,
        ];

        // Content Length
        $factors[] = [
            'name' => 'content_length',
            'label' => 'Content Length',
            'label_kr' => '콘텐츠 길이',
            'score' => $this->calculate_content_length_score($data),
            'weight' => self::WEIGHT_CONFIG['on_page']['content_length'],
            'priority' => 'P2',
        ];

        // Heading Structure
        $factors[] = [
            'name' => 'heading_structure',
            'label' => 'Heading Structure',
            'label_kr' => '제목 구조',
            'score' => $this->check_heading_structure($data),
            'weight' => 6,
            'priority' => 'P1',
        ];

        // Meta Description
        $factors[] = [
            'name' => 'meta_description',
            'label' => 'Meta Description',
            'label_kr' => '메타 설명',
            'score' => $this->check_meta_description($data),
            'weight' => 7,
            'priority' => 'P1',
        ];

        // Image Optimization
        $factors[] = [
            'name' => 'image_optimization',
            'label' => 'Image Optimization',
            'label_kr' => '이미지 최적화',
            'score' => $this->check_image_optimization($data),
            'weight' => 6,
            'priority' => 'P1',
        ];

        return [
            'category' => 'On-Page SEO',
            'category_kr' => '온페이지 SEO',
            'score' => $this->calculate_category_score($factors),
            'factors' => $factors,
        ];
    }

    /**
     * Analyze content quality (CompressedQualitySignals)
     */
    private function analyze_content($data) {
        $factors = [];

        // Original Content Score (0-512 scale from Google, normalized to 0-100)
        $factors[] = [
            'name' => 'original_content',
            'label' => 'Content Originality',
            'label_kr' => '콘텐츠 원본성',
            'score' => $this->calculate_originality_score($data),
            'weight' => self::WEIGHT_CONFIG['quality_signals']['original_content'],
            'priority' => 'P0',
        ];

        // Site Authority Estimation
        $factors[] = [
            'name' => 'site_authority',
            'label' => 'Site Authority',
            'label_kr' => '사이트 권위도',
            'score' => $data['site_authority'] ?? 50, // Default mid-range
            'weight' => self::WEIGHT_CONFIG['quality_signals']['site_authority'],
            'priority' => 'P0',
        ];

        // Content Depth
        $factors[] = [
            'name' => 'content_depth',
            'label' => 'Content Depth',
            'label_kr' => '콘텐츠 깊이',
            'score' => $this->calculate_content_depth($data),
            'weight' => 7,
            'priority' => 'P1',
        ];

        // Readability Score
        $factors[] = [
            'name' => 'readability',
            'label' => 'Readability',
            'label_kr' => '가독성',
            'score' => $this->calculate_readability($data),
            'weight' => 6,
            'priority' => 'P2',
        ];

        return [
            'category' => 'Content Quality',
            'category_kr' => '콘텐츠 품질',
            'score' => $this->calculate_category_score($factors),
            'factors' => $factors,
        ];
    }

    /**
     * Analyze link signals
     */
    private function analyze_links($data) {
        $factors = [];

        // Internal Links
        $factors[] = [
            'name' => 'internal_links',
            'label' => 'Internal Linking',
            'label_kr' => '내부 링크',
            'score' => $this->check_internal_links($data),
            'weight' => 7,
            'priority' => 'P1',
        ];

        // External Links
        $factors[] = [
            'name' => 'external_links',
            'label' => 'External Links',
            'label_kr' => '외부 링크',
            'score' => $this->check_external_links($data),
            'weight' => 6,
            'priority' => 'P2',
        ];

        // Link Diversity (if backlink data available)
        if (isset($data['backlinks'])) {
            $factors[] = [
                'name' => 'link_diversity',
                'label' => 'Backlink Diversity',
                'label_kr' => '백링크 다양성',
                'score' => $this->calculate_link_diversity($data),
                'weight' => self::WEIGHT_CONFIG['links']['link_diversity'],
                'priority' => 'P0',
            ];
        }

        // Anchor Text Quality
        $factors[] = [
            'name' => 'anchor_quality',
            'label' => 'Anchor Text Quality',
            'label_kr' => '앵커 텍스트 품질',
            'score' => $this->check_anchor_quality($data),
            'weight' => 7,
            'priority' => 'P1',
        ];

        return [
            'category' => 'Link Profile',
            'category_kr' => '링크 프로필',
            'score' => $this->calculate_category_score($factors),
            'factors' => $factors,
        ];
    }

    /**
     * Analyze topical authority (SiteTopics)
     */
    private function analyze_topical($data) {
        $factors = [];

        // Topic Focus Score
        $factors[] = [
            'name' => 'topic_focus',
            'label' => 'Topic Focus',
            'label_kr' => '주제 집중도',
            'score' => $this->calculate_topic_focus($data),
            'weight' => self::WEIGHT_CONFIG['topical']['site_focus_score'],
            'priority' => 'P0',
        ];

        // Topic Relevance
        $factors[] = [
            'name' => 'topic_relevance',
            'label' => 'Topic Relevance',
            'label_kr' => '주제 관련성',
            'score' => $data['topic_relevance'] ?? 70,
            'weight' => 7,
            'priority' => 'P1',
        ];

        // Entity Coverage
        $factors[] = [
            'name' => 'entity_coverage',
            'label' => 'Entity Coverage',
            'label_kr' => '엔티티 커버리지',
            'score' => $this->calculate_entity_coverage($data),
            'weight' => 6,
            'priority' => 'P2',
        ];

        return [
            'category' => 'Topical Authority',
            'category_kr' => '주제별 권위',
            'score' => $this->calculate_category_score($factors),
            'factors' => $factors,
        ];
    }

    /**
     * Analyze content freshness
     */
    private function analyze_freshness($data) {
        $factors = [];

        // Last Update
        $factors[] = [
            'name' => 'last_update',
            'label' => 'Last Significant Update',
            'label_kr' => '마지막 의미있는 업데이트',
            'score' => $this->calculate_freshness_score($data),
            'weight' => self::WEIGHT_CONFIG['freshness']['last_significant_update'],
            'priority' => 'P0',
        ];

        // Publication Date Clarity
        $factors[] = [
            'name' => 'date_clarity',
            'label' => 'Date Clarity',
            'label_kr' => '날짜 명확성',
            'score' => $this->check_date_clarity($data),
            'weight' => self::WEIGHT_CONFIG['freshness']['byline_date'],
            'priority' => 'P1',
        ];

        return [
            'category' => 'Content Freshness',
            'category_kr' => '콘텐츠 신선도',
            'score' => $this->calculate_category_score($factors),
            'factors' => $factors,
        ];
    }

    /**
     * Analyze E-E-A-T signals
     */
    private function analyze_eeat($data) {
        $factors = [];

        // Author Information
        $factors[] = [
            'name' => 'author_info',
            'label' => 'Author Information',
            'label_kr' => '저자 정보',
            'score' => $this->check_author_info($data),
            'weight' => self::WEIGHT_CONFIG['eeat']['author_entity'],
            'priority' => 'P1',
        ];

        // Expertise Signals
        $factors[] = [
            'name' => 'expertise',
            'label' => 'Expertise Signals',
            'label_kr' => '전문성 신호',
            'score' => $this->check_expertise_signals($data),
            'weight' => 7,
            'priority' => 'P1',
        ];

        // Trust Signals
        $factors[] = [
            'name' => 'trust_signals',
            'label' => 'Trust Signals',
            'label_kr' => '신뢰도 신호',
            'score' => $this->check_trust_signals($data),
            'weight' => 8,
            'priority' => 'P1',
        ];

        return [
            'category' => 'E-E-A-T',
            'category_kr' => 'E-E-A-T',
            'score' => $this->calculate_category_score($factors),
            'factors' => $factors,
        ];
    }

    /**
     * Analyze structured data (Schema.org)
     */
    private function analyze_schema($data) {
        $factors = [];

        // Schema.org Presence
        $factors[] = [
            'name' => 'schema_presence',
            'label' => 'Schema.org Markup',
            'label_kr' => 'Schema.org 마크업',
            'score' => $this->check_schema_presence($data),
            'weight' => self::WEIGHT_CONFIG['schema']['schema_org'],
            'priority' => 'P1',
        ];

        // Schema Validity
        $factors[] = [
            'name' => 'schema_validity',
            'label' => 'Schema Validity',
            'label_kr' => '스키마 유효성',
            'score' => $this->check_schema_validity($data),
            'weight' => 6,
            'priority' => 'P2',
        ];

        // Rich Results Eligibility
        $factors[] = [
            'name' => 'rich_results',
            'label' => 'Rich Results Eligible',
            'label_kr' => '리치 결과 적격성',
            'score' => $this->check_rich_results($data),
            'weight' => self::WEIGHT_CONFIG['schema']['rich_snippets'],
            'priority' => 'P2',
        ];

        return [
            'category' => 'Structured Data',
            'category_kr' => '구조화 데이터',
            'score' => $this->calculate_category_score($factors),
            'factors' => $factors,
        ];
    }

    /**
     * Estimate user engagement signals (NavBoost proxy)
     */
    private function estimate_engagement($data) {
        $factors = [];

        // Estimated CTR Potential
        $factors[] = [
            'name' => 'ctr_potential',
            'label' => 'CTR Potential',
            'label_kr' => 'CTR 잠재력',
            'score' => $this->estimate_ctr_potential($data),
            'weight' => self::WEIGHT_CONFIG['navboost']['ctr'],
            'priority' => 'P0',
        ];

        // Dwell Time Potential
        $factors[] = [
            'name' => 'dwell_time_potential',
            'label' => 'Dwell Time Potential',
            'label_kr' => '체류시간 잠재력',
            'score' => $this->estimate_dwell_time_potential($data),
            'weight' => self::WEIGHT_CONFIG['navboost']['good_clicks'],
            'priority' => 'P0',
        ];

        // Pogo-sticking Risk
        $pogo_risk = $this->estimate_pogo_risk($data);
        $factors[] = [
            'name' => 'pogo_risk',
            'label' => 'Pogo-sticking Risk',
            'label_kr' => '포고스틱 위험',
            'score' => 100 - $pogo_risk, // Inverse
            'weight' => self::WEIGHT_CONFIG['navboost']['bad_clicks'],
            'priority' => 'P0',
            'is_penalty' => $pogo_risk > 50,
        ];

        return [
            'category' => 'User Engagement',
            'category_kr' => '사용자 참여',
            'score' => $this->calculate_category_score($factors),
            'factors' => $factors,
        ];
    }

    /**
     * Analyze demotion risks
     */
    private function analyze_demotion_risks($data) {
        $factors = [];
        $has_risk = false;

        // SpamBrain Risk
        $spam_risk = $this->check_spam_signals($data);
        $factors[] = [
            'name' => 'spam_brain_risk',
            'label' => 'SpamBrain Risk',
            'label_kr' => '스팸브레인 위험',
            'score' => 100 - $spam_risk,
            'weight' => self::WEIGHT_CONFIG['demotions']['spam_brain'],
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
            'weight' => self::WEIGHT_CONFIG['quality_signals']['panda_demotion'],
            'priority' => 'P0',
            'is_penalty' => $thin_risk > 40,
        ];
        if ($thin_risk > 40) $has_risk = true;

        // UX Issues Risk
        $ux_risk = $this->check_ux_issues($data);
        $factors[] = [
            'name' => 'ux_issues_risk',
            'label' => 'UX Issues Risk',
            'label_kr' => 'UX 문제 위험',
            'score' => 100 - $ux_risk,
            'weight' => self::WEIGHT_CONFIG['quality_signals']['nav_demotion'],
            'priority' => 'P0',
            'is_penalty' => $ux_risk > 30,
        ];
        if ($ux_risk > 30) $has_risk = true;

        return [
            'category' => 'Demotion Risks',
            'category_kr' => '강등 위험',
            'score' => $this->calculate_category_score($factors),
            'factors' => $factors,
            'has_risk' => $has_risk,
        ];
    }

    // ========================================
    // Helper Methods
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

    private function calculate_speed_score($data) {
        if (isset($data['lcp'])) {
            // LCP based scoring
            $lcp = $data['lcp'];
            if ($lcp <= 2500) return 100;
            if ($lcp <= 4000) return 70;
            return 30;
        }
        if (isset($data['page_speed_score'])) {
            return $data['page_speed_score'];
        }
        return 50;
    }

    private function check_canonical($data) {
        if (!isset($data['canonical'])) return 50;
        if (empty($data['canonical'])) return 30;
        if ($data['canonical'] === $data['url']) return 100;
        return 70; // Has canonical, points elsewhere
    }

    private function check_robots_meta($data) {
        if (!isset($data['robots_meta'])) return 50;
        $robots = strtolower($data['robots_meta']);
        if (strpos($robots, 'noindex') !== false) return 0;
        if (strpos($robots, 'nofollow') !== false) return 50;
        return 100;
    }

    private function calculate_title_score($data) {
        $score = 0;
        if (!isset($data['title'])) return 0;

        $title = $data['title'];
        $length = mb_strlen($title);

        // Length check (optimal 50-60 chars)
        if ($length >= 50 && $length <= 60) $score += 40;
        elseif ($length >= 30 && $length <= 70) $score += 25;
        else $score += 10;

        // Keyword presence
        if (isset($data['focus_keyword']) && stripos($title, $data['focus_keyword']) !== false) {
            $score += 40;
            // Keyword position (front-loaded is better)
            if (stripos($title, $data['focus_keyword']) < 20) $score += 20;
        } else {
            $score += 20; // Neutral if no focus keyword set
        }

        return min(100, $score);
    }

    private function check_keyword_stuffing($data) {
        if (!isset($data['content']) || !isset($data['focus_keyword'])) return 0;

        $content = strtolower($data['content']);
        $keyword = strtolower($data['focus_keyword']);
        $word_count = str_word_count($content);

        if ($word_count === 0) return 0;

        $keyword_count = substr_count($content, $keyword);
        $density = ($keyword_count / $word_count) * 100;

        // Density over 3% is considered stuffing
        if ($density > 5) return 100;
        if ($density > 3) return 60;
        if ($density > 2.5) return 30;
        return 0;
    }

    private function calculate_content_length_score($data) {
        $word_count = $data['word_count'] ?? 0;

        if ($word_count >= 2000) return 100;
        if ($word_count >= 1500) return 90;
        if ($word_count >= 1000) return 80;
        if ($word_count >= 500) return 60;
        if ($word_count >= 300) return 40;
        return 20;
    }

    private function check_heading_structure($data) {
        $score = 0;

        // Has H1
        if (isset($data['h1_count']) && $data['h1_count'] === 1) $score += 40;
        elseif (isset($data['h1_count']) && $data['h1_count'] > 1) $score += 20;

        // Has H2s
        if (isset($data['h2_count']) && $data['h2_count'] >= 2) $score += 30;
        elseif (isset($data['h2_count']) && $data['h2_count'] >= 1) $score += 15;

        // Has H3s
        if (isset($data['h3_count']) && $data['h3_count'] >= 1) $score += 15;

        // Proper hierarchy
        if (isset($data['heading_hierarchy_valid']) && $data['heading_hierarchy_valid']) $score += 15;

        return min(100, $score);
    }

    private function check_meta_description($data) {
        if (!isset($data['meta_description'])) return 0;

        $desc = $data['meta_description'];
        $length = mb_strlen($desc);
        $score = 0;

        // Length check (optimal 150-160 chars)
        if ($length >= 150 && $length <= 160) $score += 50;
        elseif ($length >= 120 && $length <= 170) $score += 35;
        elseif ($length >= 50) $score += 20;

        // Keyword presence
        if (isset($data['focus_keyword']) && stripos($desc, $data['focus_keyword']) !== false) {
            $score += 30;
        }

        // Call to action
        $cta_words = ['learn', 'discover', 'find', 'get', 'explore', 'click', 'read'];
        foreach ($cta_words as $word) {
            if (stripos($desc, $word) !== false) {
                $score += 20;
                break;
            }
        }

        return min(100, $score);
    }

    private function check_image_optimization($data) {
        if (!isset($data['images'])) return 50;

        $total = count($data['images']);
        if ($total === 0) return 100; // No images to optimize

        $optimized = 0;
        foreach ($data['images'] as $img) {
            $img_score = 0;
            if (!empty($img['alt'])) $img_score += 50;
            if (!empty($img['lazy_loading'])) $img_score += 25;
            if (!empty($img['compressed'])) $img_score += 25;
            $optimized += $img_score / 100;
        }

        return round(($optimized / $total) * 100);
    }

    private function calculate_originality_score($data) {
        // If we have plagiarism check data
        if (isset($data['originality_score'])) {
            return min(100, ($data['originality_score'] / 512) * 100);
        }

        // Estimate based on content uniqueness signals
        $score = 70; // Default mid-high

        if (isset($data['unique_phrases_ratio'])) {
            $score = $data['unique_phrases_ratio'] * 100;
        }

        return $score;
    }

    private function calculate_content_depth($data) {
        $score = 0;

        // Word count
        $word_count = $data['word_count'] ?? 0;
        if ($word_count >= 2000) $score += 30;
        elseif ($word_count >= 1000) $score += 20;

        // Heading count (content structure)
        $heading_count = ($data['h2_count'] ?? 0) + ($data['h3_count'] ?? 0);
        if ($heading_count >= 5) $score += 25;
        elseif ($heading_count >= 3) $score += 15;

        // Has lists
        if (isset($data['has_lists']) && $data['has_lists']) $score += 15;

        // Has tables
        if (isset($data['has_tables']) && $data['has_tables']) $score += 15;

        // Has images
        if (isset($data['images']) && count($data['images']) >= 3) $score += 15;

        return min(100, $score);
    }

    private function calculate_readability($data) {
        if (isset($data['flesch_reading_ease'])) {
            // Flesch Reading Ease: 60-70 is ideal for web
            $flesch = $data['flesch_reading_ease'];
            if ($flesch >= 60 && $flesch <= 70) return 100;
            if ($flesch >= 50 && $flesch <= 80) return 80;
            if ($flesch >= 30 && $flesch <= 90) return 60;
            return 40;
        }

        // Estimate from average sentence length
        if (isset($data['avg_sentence_length'])) {
            $asl = $data['avg_sentence_length'];
            if ($asl >= 15 && $asl <= 20) return 100;
            if ($asl >= 10 && $asl <= 25) return 75;
            return 50;
        }

        return 60; // Default
    }

    private function check_internal_links($data) {
        $count = $data['internal_link_count'] ?? 0;

        if ($count >= 10) return 100;
        if ($count >= 5) return 80;
        if ($count >= 3) return 60;
        if ($count >= 1) return 40;
        return 20;
    }

    private function check_external_links($data) {
        $count = $data['external_link_count'] ?? 0;

        // Check for nofollow on external links
        $has_authority_links = $data['has_authority_external_links'] ?? false;

        if ($count >= 3 && $has_authority_links) return 100;
        if ($count >= 2) return 80;
        if ($count >= 1) return 60;
        return 40;
    }

    private function calculate_link_diversity($data) {
        if (!isset($data['backlinks'])) return 50;

        $backlinks = $data['backlinks'];
        $unique_domains = count(array_unique(array_column($backlinks, 'domain')));
        $total = count($backlinks);

        if ($total === 0) return 50;

        $diversity_ratio = $unique_domains / $total;
        return round($diversity_ratio * 100);
    }

    private function check_anchor_quality($data) {
        if (!isset($data['anchor_texts'])) return 50;

        $anchors = $data['anchor_texts'];
        $total = count($anchors);
        if ($total === 0) return 50;

        $quality_anchors = 0;
        foreach ($anchors as $anchor) {
            // Check if anchor is descriptive (not generic like "click here")
            $generic = ['click here', 'read more', 'learn more', 'here', 'link'];
            if (!in_array(strtolower($anchor), $generic) && strlen($anchor) > 3) {
                $quality_anchors++;
            }
        }

        return round(($quality_anchors / $total) * 100);
    }

    private function calculate_topic_focus($data) {
        if (isset($data['topic_focus_score'])) {
            return $data['topic_focus_score'];
        }

        // Estimate from keyword consistency
        if (isset($data['focus_keyword']) && isset($data['content'])) {
            $keyword = strtolower($data['focus_keyword']);
            $content = strtolower($data['content']);

            // Check presence in title, h1, first paragraph
            $score = 0;
            if (isset($data['title']) && stripos($data['title'], $keyword) !== false) $score += 25;
            if (isset($data['h1']) && stripos($data['h1'], $keyword) !== false) $score += 25;

            // First 200 words
            $first_200 = implode(' ', array_slice(explode(' ', $content), 0, 200));
            if (stripos($first_200, $keyword) !== false) $score += 25;

            // Last 200 words
            $words = explode(' ', $content);
            $last_200 = implode(' ', array_slice($words, -200));
            if (stripos($last_200, $keyword) !== false) $score += 25;

            return $score;
        }

        return 60;
    }

    private function calculate_entity_coverage($data) {
        if (isset($data['entities'])) {
            $entities = $data['entities'];
            if (count($entities) >= 10) return 100;
            if (count($entities) >= 5) return 80;
            if (count($entities) >= 3) return 60;
            return 40;
        }
        return 50;
    }

    private function calculate_freshness_score($data) {
        if (!isset($data['last_modified'])) return 50;

        $last_modified = strtotime($data['last_modified']);
        $now = time();
        $days_old = ($now - $last_modified) / (60 * 60 * 24);

        if ($days_old <= 7) return 100;
        if ($days_old <= 30) return 90;
        if ($days_old <= 90) return 75;
        if ($days_old <= 180) return 60;
        if ($days_old <= 365) return 40;
        return 20;
    }

    private function check_date_clarity($data) {
        $score = 0;

        if (isset($data['published_date'])) $score += 50;
        if (isset($data['modified_date'])) $score += 30;
        if (isset($data['date_in_url'])) $score += 10;
        if (isset($data['date_schema'])) $score += 10;

        return min(100, $score);
    }

    private function check_author_info($data) {
        $score = 0;

        if (isset($data['author_name'])) $score += 30;
        if (isset($data['author_bio'])) $score += 25;
        if (isset($data['author_image'])) $score += 15;
        if (isset($data['author_schema'])) $score += 20;
        if (isset($data['author_social_links'])) $score += 10;

        return min(100, $score);
    }

    private function check_expertise_signals($data) {
        $score = 40; // Base score

        if (isset($data['has_citations'])) $score += 20;
        if (isset($data['has_statistics'])) $score += 15;
        if (isset($data['has_expert_quotes'])) $score += 15;
        if (isset($data['has_original_research'])) $score += 10;

        return min(100, $score);
    }

    private function check_trust_signals($data) {
        $score = 30; // Base score

        if (isset($data['has_about_page'])) $score += 15;
        if (isset($data['has_contact_page'])) $score += 15;
        if (isset($data['has_privacy_policy'])) $score += 15;
        if (isset($data['has_terms_of_service'])) $score += 10;
        if ($data['is_https'] ?? false) $score += 15;

        return min(100, $score);
    }

    private function check_schema_presence($data) {
        if (!isset($data['schema_types'])) return 0;

        $types = $data['schema_types'];
        if (count($types) >= 3) return 100;
        if (count($types) >= 2) return 80;
        if (count($types) >= 1) return 60;
        return 0;
    }

    private function check_schema_validity($data) {
        if (!isset($data['schema_valid'])) return 50;
        return $data['schema_valid'] ? 100 : 30;
    }

    private function check_rich_results($data) {
        if (isset($data['rich_results_eligible']) && $data['rich_results_eligible']) {
            return 100;
        }
        if (isset($data['has_faq_schema']) || isset($data['has_howto_schema'])) {
            return 80;
        }
        return 40;
    }

    private function estimate_ctr_potential($data) {
        $score = 50; // Base score

        // Title quality affects CTR
        $title_score = $this->calculate_title_score($data);
        $score += ($title_score / 100) * 25;

        // Meta description quality
        $desc_score = $this->check_meta_description($data);
        $score += ($desc_score / 100) * 15;

        // Rich results eligible
        if ($this->check_rich_results($data) >= 80) $score += 10;

        return min(100, $score);
    }

    private function estimate_dwell_time_potential($data) {
        $score = 50;

        // Content depth = longer dwell time
        $content_depth = $this->calculate_content_depth($data);
        $score += ($content_depth / 100) * 25;

        // Readability affects engagement
        $readability = $this->calculate_readability($data);
        $score += ($readability / 100) * 15;

        // Visual content keeps users engaged
        if (isset($data['has_video'])) $score += 10;

        return min(100, $score);
    }

    private function estimate_pogo_risk($data) {
        $risk = 0;

        // Slow page = high bounce risk
        $speed_score = $this->calculate_speed_score($data);
        if ($speed_score < 50) $risk += 30;

        // Thin content = bounce
        if (($data['word_count'] ?? 0) < 300) $risk += 30;

        // No mobile optimization = mobile bounces
        if (($data['mobile_score'] ?? 100) < 50) $risk += 20;

        // Intrusive ads
        if (isset($data['has_intrusive_interstitials']) && $data['has_intrusive_interstitials']) {
            $risk += 20;
        }

        return min(100, $risk);
    }

    private function check_spam_signals($data) {
        $risk = 0;

        // Keyword stuffing
        $stuffing = $this->check_keyword_stuffing($data);
        $risk += ($stuffing / 100) * 40;

        // Hidden text (if detected)
        if (isset($data['has_hidden_text']) && $data['has_hidden_text']) {
            $risk += 30;
        }

        // Excessive ads
        if (isset($data['ad_ratio']) && $data['ad_ratio'] > 0.3) {
            $risk += 30;
        }

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

    private function check_ux_issues($data) {
        $risk = 0;

        // Mobile issues
        if (($data['mobile_score'] ?? 100) < 50) $risk += 30;

        // Page speed
        if ($this->calculate_speed_score($data) < 50) $risk += 25;

        // Layout shift
        if (isset($data['cls']) && $data['cls'] > 0.25) $risk += 25;

        // Interstitials
        if (isset($data['has_intrusive_interstitials']) && $data['has_intrusive_interstitials']) {
            $risk += 20;
        }

        return min(100, $risk);
    }

    private function calculate_category_score($factors) {
        $total_weight = 0;
        $total_score = 0;

        foreach ($factors as $factor) {
            $weight = abs($factor['weight']);
            $total_weight += $weight;
            $total_score += $factor['score'] * $weight;
        }

        return $total_weight > 0 ? round($total_score / $total_weight, 1) : 0;
    }

    private function get_grade($score) {
        if ($score >= 90) return 'A+';
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'F';
    }

    private function generate_recommendations($scores) {
        $recommendations = [];

        foreach ($scores as $category => $data) {
            foreach ($data['factors'] as $factor) {
                if ($factor['score'] < 60) {
                    $recommendations[] = [
                        'factor' => $factor['name'],
                        'label' => $factor['label'],
                        'label_kr' => $factor['label_kr'],
                        'current_score' => $factor['score'],
                        'priority' => $factor['priority'],
                        'category' => $data['category'],
                        'impact' => $factor['weight'],
                    ];
                }
            }
        }

        // Sort by priority and impact
        usort($recommendations, function($a, $b) {
            $priority_order = ['P0' => 0, 'P1' => 1, 'P2' => 2, 'P3' => 3];
            $a_priority = $priority_order[$a['priority']] ?? 4;
            $b_priority = $priority_order[$b['priority']] ?? 4;

            if ($a_priority !== $b_priority) {
                return $a_priority - $b_priority;
            }

            return $b['impact'] - $a['impact'];
        });

        return array_slice($recommendations, 0, 10); // Top 10 recommendations
    }

    private function get_priority_actions($scores) {
        $actions = [];

        // Critical issues (P0)
        foreach ($scores as $category => $data) {
            foreach ($data['factors'] as $factor) {
                if ($factor['priority'] === 'P0' && $factor['score'] < 50) {
                    $actions[] = [
                        'type' => 'critical',
                        'factor' => $factor['label'],
                        'factor_kr' => $factor['label_kr'],
                        'message' => sprintf('Critical: %s needs immediate attention (Score: %d)',
                            $factor['label'], $factor['score']),
                        'message_kr' => sprintf('긴급: %s 즉시 개선 필요 (점수: %d)',
                            $factor['label_kr'], $factor['score']),
                    ];
                }
            }
        }

        return $actions;
    }

    /**
     * Get scoring configuration for external use
     */
    public function get_weight_config() {
        return self::WEIGHT_CONFIG;
    }

    /**
     * Get SEO categories
     */
    public function get_categories() {
        return self::SEO_CATEGORIES;
    }
}

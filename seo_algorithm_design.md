# WP Bulk SEO & AEO(AIO) - SEO 알고리즘 설계 문서

**작성일**: 2026-01-03  
**버전**: 1.0.0  
**기반**: Google 알고리즘 유출 문서 + Airtable 데이터베이스 + 리서치 자료

---

## 🧮 SEO 랭킹 알고리즘 설계

### 알고리즘 개요

WP Bulk SEO & AEO(AIO) 플러그인은 Google의 실제 랭킹 요소를 기반으로 한 종합 SEO 점수 계산 알고리즘을 구현합니다.

### 핵심 알고리즘 공식

```
종합 SEO 점수 = Σ(요소 점수 × 가중치) / Σ(가중치)

요소 점수 = (현재 값 / 최적 값) × 100
최종 점수 = 0 ~ 100 (100 = 완벽)
```

### 가중치 기반 점수 계산

#### Tier 1 요소 (가중치 9-10)

```php
$tier1_weight = 9.5; // 평균 가중치
$tier1_factors = [
    'domain_authority' => 10,      // siteAuthority, PageRank
    'mobile_friendliness' => 10,   // Mobile SEO
    'core_web_vitals' => 9,        // LCP, FID, CLS
    'brand_recognition' => 9,      // Brand Signals
    'user_engagement' => 9         // Good/Bad Clicks, Navboost
];
```

#### Tier 2 요소 (가중치 8)

```php
$tier2_weight = 8;
$tier2_factors = [
    'content_quality' => 8,        // Originality, YMYL
    'content_relevance' => 8,      // siteFocusScore
    'page_speed' => 8,             // Page Speed
    'https_security' => 8,         // HTTPS/SSL
    'backlink_quality' => 8       // PageRank, Link Quality
];
```

#### Tier 3 요소 (가중치 6-7)

```php
$tier3_weight = 6.5; // 평균 가중치
$tier3_factors = [
    'structured_data' => 7,        // Schema.org
    'title_optimization' => 7,     // titlematchScore
    'content_freshness' => 7,      // semanticDate, bylineDate
    'keyword_usage' => 7,          // avgTermWeight
    'author_authority' => 6,       // Author Information
    'internal_linking' => 7,       // Internal Links
    'image_optimization' => 6     // Image SEO
];
```

### 세부 알고리즘 구현

#### 1. 도메인 권위 점수 (가중치 10)

```php
function calculate_domain_authority_score($site) {
    $factors = [
        'pagerank' => 0.30,           // PageRank 추정 (0-100)
        'backlink_quality' => 0.25,   // 백링크 품질 점수
        'backlink_diversity' => 0.20,  // 백링크 다양성
        'domain_age' => 0.15,          // 도메인 나이 (Host Age)
        'registration_info' => 0.10    // 등록 정보 품질
    ];
    
    $score = 0;
    foreach ($factors as $factor => $weight) {
        $score += $site[$factor] * $weight;
    }
    
    return min(100, max(0, $score));
}
```

#### 2. 사용자 참여 점수 (가중치 9)

```php
function calculate_user_engagement_score($metrics) {
    $factors = [
        'ctr' => 0.25,                 // Click-Through Rate
        'dwell_time' => 0.25,          // 체류 시간
        'good_clicks_ratio' => 0.20,   // Good Clicks / Total Clicks
        'last_longest_clicks' => 0.15,  // Last Longest Clicks
        'unsquashed_clicks' => 0.15    // Unsquashed Clicks
    ];
    
    $score = 0;
    foreach ($factors as $factor => $weight) {
        $normalized = normalize_metric($metrics[$factor]);
        $score += $normalized * $weight;
    }
    
    return min(100, max(0, $score));
}
```

#### 3. 콘텐츠 품질 점수 (가중치 8)

```php
function calculate_content_quality_score($content) {
    $factors = [
        'originality_score' => 0.30,    // Originality Score (0-512) → 0-100
        'ymyl_score' => 0.25,          // YMYL Score
        'avg_term_weight' => 0.20,     // avgTermWeight
        'content_depth' => 0.15,       // 콘텐츠 깊이
        'readability' => 0.10          // 가독성
    ];
    
    $score = 0;
    foreach ($factors as $factor => $weight) {
        $normalized = normalize_content_factor($content[$factor]);
        $score += $normalized * $weight;
    }
    
    return min(100, max(0, $score));
}
```

#### 4. Core Web Vitals 점수 (가중치 9)

```php
function calculate_core_web_vitals_score($metrics) {
    $factors = [
        'lcp' => 0.40,  // Largest Contentful Paint (목표: < 2.5초)
        'fid' => 0.30,  // First Input Delay (목표: < 100ms)
        'cls' => 0.30   // Cumulative Layout Shift (목표: < 0.1)
    ];
    
    $lcp_score = $metrics['lcp'] <= 2.5 ? 100 : max(0, 100 - (($metrics['lcp'] - 2.5) * 20));
    $fid_score = $metrics['fid'] <= 100 ? 100 : max(0, 100 - (($metrics['fid'] - 100) * 0.5));
    $cls_score = $metrics['cls'] <= 0.1 ? 100 : max(0, 100 - (($metrics['cls'] - 0.1) * 500));
    
    $score = ($lcp_score * $factors['lcp']) + 
             ($fid_score * $factors['fid']) + 
             ($cls_score * $factors['cls']);
    
    return min(100, max(0, $score));
}
```

#### 5. 콘텐츠 신선도 점수 (가중치 7)

```php
function calculate_content_freshness_score($dates) {
    $factors = [
        'semantic_date' => 0.40,   // 페이지 콘텐츠 내 날짜
        'syntactic_date' => 0.30,  // URL 내 날짜
        'byline_date' => 0.20,     // 작성자 날짜
        'last_update' => 0.10      // 마지막 업데이트
    ];
    
    $score = 0;
    $now = time();
    
    foreach ($factors as $factor => $weight) {
        if (isset($dates[$factor])) {
            $age_days = ($now - strtotime($dates[$factor])) / 86400;
            // 30일 이내: 100점, 90일: 70점, 180일: 40점, 365일: 10점
            $factor_score = max(0, 100 - ($age_days / 3.65));
            $score += $factor_score * $weight;
        }
    }
    
    return min(100, max(0, $score));
}
```

### 종합 점수 계산

```php
function calculate_overall_seo_score($site_data) {
    $scores = [
        'domain_authority' => [
            'score' => calculate_domain_authority_score($site_data),
            'weight' => 10
        ],
        'user_engagement' => [
            'score' => calculate_user_engagement_score($site_data['engagement']),
            'weight' => 9
        ],
        'core_web_vitals' => [
            'score' => calculate_core_web_vitals_score($site_data['web_vitals']),
            'weight' => 9
        ],
        'mobile_friendliness' => [
            'score' => calculate_mobile_score($site_data['mobile']),
            'weight' => 10
        ],
        'content_quality' => [
            'score' => calculate_content_quality_score($site_data['content']),
            'weight' => 8
        ],
        'content_relevance' => [
            'score' => calculate_content_relevance_score($site_data),
            'weight' => 8
        ],
        'page_speed' => [
            'score' => calculate_page_speed_score($site_data['speed']),
            'weight' => 8
        ],
        'backlink_quality' => [
            'score' => calculate_backlink_score($site_data['backlinks']),
            'weight' => 8
        ],
        'https_security' => [
            'score' => calculate_https_score($site_data['security']),
            'weight' => 8
        ],
        'structured_data' => [
            'score' => calculate_schema_score($site_data['schema']),
            'weight' => 7
        ],
        'title_optimization' => [
            'score' => calculate_title_score($site_data['title']),
            'weight' => 7
        ],
        'content_freshness' => [
            'score' => calculate_content_freshness_score($site_data['dates']),
            'weight' => 7
        ],
        'keyword_usage' => [
            'score' => calculate_keyword_score($site_data['keywords']),
            'weight' => 7
        ],
        'internal_linking' => [
            'score' => calculate_internal_linking_score($site_data['links']),
            'weight' => 7
        ],
        'image_optimization' => [
            'score' => calculate_image_score($site_data['images']),
            'weight' => 6
        ],
        'author_authority' => [
            'score' => calculate_author_score($site_data['author']),
            'weight' => 6
        ]
    ];
    
    // 가중 평균 계산
    $total_score = 0;
    $total_weight = 0;
    
    foreach ($scores as $factor => $data) {
        $total_score += $data['score'] * $data['weight'];
        $total_weight += $data['weight'];
    }
    
    $overall_score = $total_score / $total_weight;
    
    return [
        'overall_score' => round($overall_score, 2),
        'factor_scores' => $scores,
        'grade' => get_seo_grade($overall_score)
    ];
}

function get_seo_grade($score) {
    if ($score >= 90) return 'A+';
    if ($score >= 80) return 'A';
    if ($score >= 70) return 'B';
    if ($score >= 60) return 'C';
    if ($score >= 50) return 'D';
    return 'F';
}
```

### 최적화 제안 알고리즘

```php
function generate_optimization_suggestions($site_data, $overall_score) {
    $suggestions = [];
    
    // 가중치가 높고 점수가 낮은 요소 우선 제안
    foreach ($overall_score['factor_scores'] as $factor => $data) {
        if ($data['score'] < 70 && $data['weight'] >= 8) {
            $suggestions[] = [
                'priority' => 'high',
                'factor' => $factor,
                'current_score' => $data['score'],
                'weight' => $data['weight'],
                'suggestions' => get_factor_suggestions($factor, $site_data)
            ];
        }
    }
    
    // 가중치 순으로 정렬
    usort($suggestions, function($a, $b) {
        return $b['weight'] - $a['weight'];
    });
    
    return $suggestions;
}
```

---

## 📊 데이터베이스 구조 설계

### WordPress 데이터베이스 테이블

```sql
-- SEO 요소 데이터베이스
CREATE TABLE wp_wp_bulk_seo_factors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    module_name VARCHAR(255),
    category ENUM('Technical SEO', 'Content SEO', 'Link SEO', 'User Experience', 'Social Signals', 'Local SEO', 'International SEO', 'Other'),
    seo_factor VARCHAR(255),
    estimated_weight TINYINT UNSIGNED, -- 1-10
    impact VARCHAR(50), -- High, Medium, Low
    explanation TEXT,
    google_element VARCHAR(255), -- Google 유출 문서 요소명
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_weight (estimated_weight),
    INDEX idx_google_element (google_element)
);

-- 사이트 SEO 점수 기록
CREATE TABLE wp_wp_bulk_seo_scores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    site_id BIGINT UNSIGNED,
    post_id BIGINT UNSIGNED,
    overall_score DECIMAL(5,2),
    grade VARCHAR(2),
    factor_scores JSON,
    calculated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_site (site_id),
    INDEX idx_post (post_id),
    INDEX idx_score (overall_score)
);

-- 최적화 제안 기록
CREATE TABLE wp_wp_bulk_seo_suggestions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    site_id BIGINT UNSIGNED,
    post_id BIGINT UNSIGNED,
    factor VARCHAR(255),
    priority ENUM('high', 'medium', 'low'),
    suggestion TEXT,
    status ENUM('pending', 'in_progress', 'completed', 'dismissed'),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_site (site_id),
    INDEX idx_post (post_id),
    INDEX idx_priority (priority),
    INDEX idx_status (status)
);
```

---

## 🔌 플러그인 아키텍처 설계

### 디렉토리 구조

```
wp-bulk-seo-aeo/
├── wp-bulk-seo-aeo.php (메인 파일)
├── includes/
│   ├── class-wp-bulk-seo-core.php (핵심 클래스)
│   ├── class-seo-algorithm.php (SEO 알고리즘)
│   ├── class-seo-scorer.php (점수 계산)
│   ├── class-optimization-suggestions.php (최적화 제안)
│   ├── modules/
│   │   ├── class-domain-authority.php
│   │   ├── class-user-engagement.php
│   │   ├── class-content-quality.php
│   │   ├── class-core-web-vitals.php
│   │   ├── class-mobile-seo.php
│   │   └── ...
│   ├── admin/
│   │   ├── class-admin.php
│   │   ├── views/
│   │   └── assets/
│   └── api/
│       ├── class-google-api.php
│       └── class-airtable-sync.php
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
└── languages/
```

### 핵심 클래스 설계

#### 1. WP_Bulk_SEO_Core

```php
class WP_Bulk_SEO_Core {
    private $algorithm;
    private $scorer;
    private $suggestions;
    
    public function __construct() {
        $this->algorithm = new WP_Bulk_SEO_Algorithm();
        $this->scorer = new WP_Bulk_SEO_Scorer();
        $this->suggestions = new WP_Bulk_SEO_Optimization_Suggestions();
    }
    
    public function calculate_seo_score($post_id = null) {
        $site_data = $this->collect_site_data($post_id);
        return $this->scorer->calculate($site_data);
    }
    
    public function get_optimization_suggestions($post_id = null) {
        $score = $this->calculate_seo_score($post_id);
        return $this->suggestions->generate($score);
    }
}
```

#### 2. WP_Bulk_SEO_Algorithm

```php
class WP_Bulk_SEO_Algorithm {
    private $factors;
    
    public function __construct() {
        $this->load_factors_from_database();
    }
    
    private function load_factors_from_database() {
        global $wpdb;
        $this->factors = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}wp_bulk_seo_factors ORDER BY estimated_weight DESC"
        );
    }
    
    public function calculate_factor_score($factor_name, $data) {
        // 각 요소별 점수 계산 로직
    }
}
```

---

## 📈 데이터 수집 및 분석

### 자동 데이터 수집

1. **Google Search Console API**: 
   - SearchAnalytics: clicks, ctr, impressions, position
   - UrlInspection: indexStatusResult, mobileUsabilityResult, ampResult, richResultsResult
   
2. **Google Analytics API**: 
   - 체류 시간, 이탈률
   - 사용자 행동 분석
   
3. **PageSpeed Insights API v5** (핵심 통합):
   - **LighthouseResult**: 
     - categories.performance.score (성능 점수)
     - categories.seo.score (SEO 점수)
     - categories.accessibility.score (접근성 점수)
     - categories.best-practices.score (모범 사례 점수)
     - audits (상세 성능 메트릭: TTFB, TBT, FCP 등)
   - **LoadingExperience**: 
     - metrics.LARGEST_CONTENTFUL_PAINT_MS (실제 사용자 LCP)
     - metrics.FIRST_INPUT_DELAY_MS (실제 사용자 FID)
     - metrics.CUMULATIVE_LAYOUT_SHIFT_SCORE (실제 사용자 CLS)
     - metrics.FIRST_CONTENTFUL_PAINT_MS (실제 사용자 FCP)
     - overall_category (전체 속도 카테고리)
   - **OriginLoadingExperience**: 도메인 전체 평균 성능
   
4. **모바일 친화성 테스트 API**: 모바일 점수
5. **백링크 분석 API**: 백링크 품질 (선택적)

### 실시간 모니터링

- 일일 자동 점수 계산
- 주간 트렌드 분석
- 월간 리포트 생성

---

**다음 단계**: 실제 플러그인 코드 구현 시작

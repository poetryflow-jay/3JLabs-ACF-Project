# WP Bulk SEO & AEO - 통합 설계 문서

**작성일**: 2026-01-04
**버전**: 2.0.0
**작성자**: 3J Labs
**기반**: Google 알고리즘 유출 문서 + Airtable 데이터베이스 + PageSpeed API v5 + 벤치마킹 리서치

---

## 1. 프로젝트 개요

### 1.1 플러그인 정보

- **이름**: WP Bulk SEO & AEO
- **설명**: WordPress Really Simple and Easy Search Engine Optimization and AI Optimization Automation. 1, 2, 3 Click Auto Optimization.
- **핵심 차별점**: Google 알고리즘 유출 문서(2024) 기반 실제 랭킹 팩터 적용
- **타겟**: 개인 블로거 ~ 대규모 에이전시

### 1.2 핵심 기능

1. **1-Click SEO**: 기본 SEO 설정 자동화
2. **2-Click AI**: AI 기반 최적화 제안
3. **3-Click 완전 자동화**: 모든 SEO 작업 자동화
4. **실시간 SEO 점수**: Google 알고리즘 기반 종합 점수
5. **대량 최적화**: Bulk Analyzer & Optimizer
6. **AEO (Answer Engine Optimization)**: AI 검색 엔진 최적화

---

## 2. 경쟁사 벤치마킹 요약

### 2.1 시장 현황

| 플러그인 | 활성 설치 | 강점 | 약점 |
|---------|----------|------|------|
| **Yoast SEO** | 5M+ | 시장 점유율, 안정성 | 무거움, 제한적 무료 |
| **Rank Math** | 2M+ | 모듈화, 무료 기능 풍부 | 상대적 짧은 역사 |
| **AIOSEO** | 3M+ | AI 기능, 사용 편의성 | 무료 기능 제한 |
| **Alli AI** | SaaS | 최강 자동화, 대량 처리 | 월 $399+ 고가 |

### 2.2 3J Labs 차별화 전략

1. **실제 Google 랭킹 팩터**: 유출 문서 기반 109개 팩터 구현
2. **PageSpeed API 완전 통합**: Core Web Vitals 실시간 측정
3. **대량 처리**: Alli AI 수준의 Bulk 최적화 (WordPress 네이티브)
4. **AI 통합**: OpenAI/Anthropic API로 콘텐츠 최적화
5. **AEO 기능**: ChatGPT/Claude 등 AI 검색 엔진 최적화

---

## 3. SEO 알고리즘 설계

### 3.1 핵심 공식

```
종합 SEO 점수 = Σ(요소 점수 × 가중치) / Σ(가중치)
최종 점수 = 0 ~ 100 (100 = 완벽)
```

### 3.2 Tier별 가중치 체계

#### Tier 1 요소 (가중치 9-10) - Critical

| 요소 | 가중치 | Google 요소 | API 소스 |
|------|--------|------------|---------|
| Domain Authority | 10 | siteAuthority | Search Console |
| PageRank | 10 | homepagePagerankNs | Internal |
| Mobile Friendliness | 10 | mobileScore | PageSpeed API |
| User Engagement - Good Clicks | 10 | goodClicks | Search Console |
| Indexability | 10 | indexable | Search Console |
| Core Web Vitals - LCP | 9 | LCP | PageSpeed API |
| Core Web Vitals - FID | 9 | FID | PageSpeed API |
| Core Web Vitals - CLS | 9 | CLS | PageSpeed API |
| Brand Recognition | 9 | Brand Signals | Internal |
| CTR | 9 | ctr | Search Console |
| Original Content Score | 9 | originalContentScore | Internal |
| SpamBrain Risk | -9 | spambrainLavcScores | Internal |

#### Tier 2 요소 (가중치 8)

| 요소 | 가중치 | Google 요소 | API 소스 |
|------|--------|------------|---------|
| Content Quality | 8 | OriginalContentScore | Internal |
| Content Relevance | 8 | siteFocusScore | Internal |
| Page Speed | 8 | pagespeed | PageSpeed API |
| HTTPS Security | 8 | httpsStatus | PageSpeed API |
| Backlink Quality | 8 | backlinkQuality | Internal |
| Title Optimization | 8 | titlematchScore | Internal |
| Last Significant Update | 8 | lastSignificantUpdate | Internal |
| Trust Signals | 8 | trustSignals | Internal |
| Local Business Schema | 8 | localBusinessSchema | Internal |

#### Tier 3 요소 (가중치 6-7)

| 요소 | 가중치 | Google 요소 | API 소스 |
|------|--------|------------|---------|
| Schema.org Markup | 7 | schemaOrg | PageSpeed API |
| Content Freshness | 7 | semanticDate, bylineDate | Internal |
| Keyword Usage | 7 | avgTermWeight | Internal |
| Internal Linking | 7 | internalLinking | Internal |
| Author Authority | 7 | authorEntity | Internal |
| E-E-A-T Expertise | 7 | expertiseSignals | Internal |
| Image Optimization | 6 | imageOptimization | PageSpeed API |
| Social Signals | 6 | socialSignals | Internal |

### 3.3 등급 시스템

| 등급 | 점수 범위 | 설명 |
|-----|----------|------|
| A+ | 90-100 | Excellent - 상위 1% |
| A | 80-89 | Great - 상위 10% |
| B | 70-79 | Good - 평균 이상 |
| C | 60-69 | Fair - 개선 필요 |
| D | 50-59 | Poor - 긴급 개선 |
| F | 0-49 | Critical - 심각한 문제 |

---

## 4. 데이터베이스 설계

### 4.1 랭킹 팩터 통합 데이터베이스

**총 109개 팩터** (통합 CSV: `3j-unified-ranking-factors.csv`)

**모듈별 분류:**
- NavBoost: 7개 (User Engagement)
- CompressedQualitySignals: 6개 (Quality Metrics)
- PerDocData: 4개 (Domain Data)
- QualityNsrNsr: 3개 (NSR Signals)
- ContentAnalysis: 6개 (Content Metrics)
- SiteTopics: 4개 (Topical Authority)
- Freshness: 5개 (Content Freshness)
- Chrome: 3개 (User Data)
- LinkSignals: 6개 (Link Authority)
- AnchorSpam: 4개 (Link Spam)
- Author: 3개 (E-E-A-T)
- TechnicalSEO: 10개 (Technical Factors)
- CoreWebVitals: 6개 (Performance)
- Lighthouse: 4개 (Performance Scores)
- Schema: 7개 (Structured Data)
- Demotions: 5개 (Penalties)
- SpecialModules: 4개 (Special Flags)
- Brand: 2개 (Brand Signals)
- OnPage: 6개 (On-Page SEO)
- LocalSEO: 3개 (Local SEO)
- EEAT: 4개 (E-E-A-T)
- RankingSystems: 4개 (Core Algorithm)
- RealTime: 3개 (Real-time Signals)

### 4.2 WordPress 데이터베이스 테이블

```sql
-- SEO 요소 테이블
CREATE TABLE wp_bulk_seo_factors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    factor_id VARCHAR(10) NOT NULL,
    module_name VARCHAR(100) NOT NULL,
    attribute_name VARCHAR(100) NOT NULL,
    description_en TEXT,
    description_kr TEXT,
    weight TINYINT UNSIGNED DEFAULT 5,
    seo_category VARCHAR(50),
    impact_type ENUM('Positive', 'Negative', 'Neutral', 'Variable', 'Core'),
    priority ENUM('P0', 'P1', 'P2', 'P3'),
    algorithm_key VARCHAR(100),
    scoring_method VARCHAR(100),
    api_source VARCHAR(100),
    google_element VARCHAR(100),
    max_score INT UNSIGNED DEFAULT 100,
    INDEX idx_module (module_name),
    INDEX idx_priority (priority),
    INDEX idx_weight (weight)
);

-- SEO 점수 테이블
CREATE TABLE wp_bulk_seo_scores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id BIGINT UNSIGNED NOT NULL,
    overall_score TINYINT UNSIGNED DEFAULT 0,
    grade VARCHAR(5),
    technical_score TINYINT UNSIGNED,
    on_page_score TINYINT UNSIGNED,
    content_score TINYINT UNSIGNED,
    links_score TINYINT UNSIGNED,
    schema_score TINYINT UNSIGNED,
    engagement_score TINYINT UNSIGNED,
    freshness_score TINYINT UNSIGNED,
    eeat_score TINYINT UNSIGNED,
    cwv_score TINYINT UNSIGNED,
    demotion_risk TINYINT(1) DEFAULT 0,
    analysis_data LONGTEXT,
    recommendations LONGTEXT,
    analyzed_at DATETIME,
    UNIQUE KEY post_id (post_id),
    INDEX idx_score (overall_score)
);

-- SEO 이슈 테이블
CREATE TABLE wp_bulk_seo_issues (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id BIGINT UNSIGNED NOT NULL,
    issue_type VARCHAR(50),
    severity ENUM('critical', 'high', 'medium', 'low'),
    factor_key VARCHAR(100),
    message TEXT,
    message_kr TEXT,
    fix_suggestion TEXT,
    is_resolved TINYINT(1) DEFAULT 0,
    detected_at DATETIME,
    resolved_at DATETIME,
    INDEX idx_post (post_id),
    INDEX idx_severity (severity)
);

-- Bulk 작업 로그 테이블
CREATE TABLE wp_bulk_seo_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    operation_type VARCHAR(50),
    status ENUM('pending', 'running', 'completed', 'failed'),
    total_items INT UNSIGNED DEFAULT 0,
    processed_items INT UNSIGNED DEFAULT 0,
    success_items INT UNSIGNED DEFAULT 0,
    failed_items INT UNSIGNED DEFAULT 0,
    details LONGTEXT,
    started_at DATETIME,
    completed_at DATETIME,
    user_id BIGINT UNSIGNED
);
```

---

## 5. API 통합

### 5.1 Google Search Console API

**사용 데이터:**
- `searchanalytics`: clicks, ctr, impressions, position
- `urlInspection`: indexStatusResult, mobileUsabilityResult, ampResult, richResultsResult

**활용:**
- 사용자 참여 지표 (CTR, 클릭, 노출)
- 인덱싱 상태 확인
- 모바일 사용성 검사
- Rich Results 검증

### 5.2 PageSpeed Insights API v5

**LighthouseResult:**
- `categories.performance.score` (성능 점수)
- `categories.seo.score` (SEO 점수)
- `categories.accessibility.score` (접근성 점수)
- `categories.best-practices.score` (모범 사례 점수)
- `audits` (상세 성능 메트릭: TTFB, TBT, FCP 등)

**LoadingExperience:**
- `metrics.LARGEST_CONTENTFUL_PAINT_MS` (실제 사용자 LCP)
- `metrics.FIRST_INPUT_DELAY_MS` (실제 사용자 FID)
- `metrics.CUMULATIVE_LAYOUT_SHIFT_SCORE` (실제 사용자 CLS)
- `metrics.FIRST_CONTENTFUL_PAINT_MS` (실제 사용자 FCP)
- `overall_category` (전체 속도 카테고리)

**OriginLoadingExperience:**
- 도메인 전체 평균 성능

### 5.3 AI API (OpenAI / Anthropic)

**OpenAI:**
- GPT-4o, GPT-4o-mini, GPT-3.5 Turbo
- 타이틀/메타 설명 최적화
- FAQ 생성
- 키워드 제안

**Anthropic:**
- Claude 3.5 Sonnet, Claude 3.5 Haiku
- 콘텐츠 개선 제안
- 스키마 추천

---

## 6. 플러그인 아키텍처

### 6.1 디렉토리 구조

```
wp-bulk-seo/
├── wp-bulk-seo.php                       # 메인 플러그인
├── includes/
│   ├── algorithm/
│   │   ├── class-ranking-factors-db.php  # 랭킹 팩터 DB
│   │   ├── class-seo-scorer.php          # 스코어링 엔진
│   │   └── class-seo-analyzer.php        # 페이지 분석기
│   ├── class-bulk-optimizer.php          # 대량 최적화
│   ├── class-ai-engine.php               # AI 통합
│   ├── class-schema-generator.php        # 스키마 생성
│   ├── class-sitemap-manager.php         # 사이트맵
│   ├── class-content-optimizer.php       # 콘텐츠 최적화
│   ├── aeo/
│   │   ├── class-aeo-engine.php          # AEO 엔진
│   │   ├── class-faq-generator.php       # FAQ 생성
│   │   └── class-featured-snippet-optimizer.php
│   ├── api/
│   │   ├── class-rest-api.php            # REST API
│   │   ├── class-search-console.php      # GSC API
│   │   └── class-pagespeed-api.php       # PageSpeed API
│   └── integrations/
│       ├── class-acf-integration.php     # ACF 통합
│       └── class-woocommerce-integration.php
├── admin/
│   ├── class-admin.php
│   ├── class-metabox.php
│   ├── class-bulk-actions.php
│   ├── class-dashboard.php
│   └── views/
│       ├── dashboard.php
│       ├── analyzer.php
│       ├── optimizer.php
│       ├── aeo.php
│       ├── schema.php
│       ├── factors.php
│       └── settings.php
├── assets/
│   ├── css/
│   └── js/
└── languages/
```

### 6.2 핵심 클래스

1. **WP_Bulk_SEO** (Main): 플러그인 코어
2. **ThreeJ_SEO_Scorer**: SEO 스코어링 엔진
3. **ThreeJ_SEO_Analyzer**: 페이지 데이터 수집
4. **ThreeJ_Ranking_Factors_DB**: 랭킹 팩터 DB
5. **WP_Bulk_SEO_Bulk_Optimizer**: 대량 분석/최적화
6. **WP_Bulk_SEO_AI_Engine**: AI API 통합
7. **WP_Bulk_SEO_Schema_Generator**: 스키마 마크업

---

## 7. 세부 알고리즘 구현

### 7.1 Core Web Vitals 점수 계산

```php
function calculate_core_web_vitals_score($metrics) {
    $factors = [
        'lcp' => 0.40,  // Largest Contentful Paint (목표: < 2.5초)
        'fid' => 0.30,  // First Input Delay (목표: < 100ms)
        'cls' => 0.30   // Cumulative Layout Shift (목표: < 0.1)
    ];

    $lcp_score = $metrics['lcp'] <= 2500 ? 100 :
                 ($metrics['lcp'] <= 4000 ? 70 : 30);
    $fid_score = $metrics['fid'] <= 100 ? 100 :
                 ($metrics['fid'] <= 300 ? 70 : 30);
    $cls_score = $metrics['cls'] <= 0.1 ? 100 :
                 ($metrics['cls'] <= 0.25 ? 70 : 30);

    return ($lcp_score * $factors['lcp']) +
           ($fid_score * $factors['fid']) +
           ($cls_score * $factors['cls']);
}
```

### 7.2 사용자 참여 점수 계산 (NavBoost Proxy)

```php
function calculate_user_engagement_score($metrics) {
    $factors = [
        'ctr' => 0.25,              // Click-Through Rate
        'dwell_time' => 0.25,       // 체류 시간
        'good_clicks_ratio' => 0.20, // Good Clicks / Total
        'last_longest_clicks' => 0.15,
        'unsquashed_clicks' => 0.15
    ];

    // CTR 점수 (평균 CTR 대비)
    $ctr_score = min(100, ($metrics['ctr'] / 0.03) * 100);

    // 체류 시간 점수 (목표: 3분 이상)
    $dwell_score = min(100, ($metrics['avg_time_on_page'] / 180) * 100);

    // ... 추가 계산

    return weighted_average($scores, $factors);
}
```

### 7.3 콘텐츠 신선도 점수 계산

```php
function calculate_content_freshness_score($dates) {
    $now = time();
    $last_update = strtotime($dates['last_modified']);
    $days_old = ($now - $last_update) / 86400;

    // 30일 이내: 100점, 90일: 75점, 180일: 50점, 365일: 25점
    if ($days_old <= 7) return 100;
    if ($days_old <= 30) return 90;
    if ($days_old <= 90) return 75;
    if ($days_old <= 180) return 50;
    if ($days_old <= 365) return 25;
    return 10;
}
```

---

## 8. 개발 로드맵

### Phase 1: Core Algorithm (Week 1-4)
- [x] 랭킹 팩터 데이터베이스 설계
- [x] SEO Scorer 클래스 구현
- [x] SEO Analyzer 클래스 구현
- [ ] WordPress 테이블 생성
- [ ] 기본 Admin UI

### Phase 2: Bulk Operations (Week 5-8)
- [ ] Bulk Analyzer 구현
- [ ] Bulk Optimizer 구현
- [ ] 예약 분석 (Cron)
- [ ] 진행률 추적 UI

### Phase 3: API Integration (Week 9-12)
- [ ] Search Console API 통합
- [ ] PageSpeed API v5 통합
- [ ] Real-time CWV 측정
- [ ] 데이터 캐싱

### Phase 4: AI Integration (Week 13-16)
- [ ] OpenAI API 통합
- [ ] Anthropic API 통합
- [ ] 타이틀/메타 AI 최적화
- [ ] FAQ 자동 생성

### Phase 5: AEO Features (Week 17-20)
- [ ] Featured Snippet Optimizer
- [ ] Voice Search Analysis
- [ ] AI Overview Optimization
- [ ] LLMs.txt 지원

### Phase 6: Polish & Launch (Week 21-24)
- [ ] 성능 최적화
- [ ] 다국어 지원
- [ ] 문서화
- [ ] 베타 테스트
- [ ] 출시

---

## 9. 생성된 파일 목록

### 핵심 알고리즘 파일

| 파일 | 위치 | 설명 |
|------|------|------|
| `class-3j-seo-scorer.php` | `SEO/3j-seo-algorithm/` | SEO 스코어링 엔진 |
| `class-3j-seo-analyzer.php` | `SEO/3j-seo-algorithm/` | 페이지 분석기 |
| `class-3j-ranking-factors-db.php` | `SEO/3j-seo-algorithm/` | 랭킹 팩터 DB 클래스 |
| `3j-unified-ranking-factors.csv` | `SEO/3j-seo-algorithm/` | 통합 랭킹 팩터 (109개) |
| `3j-ranking-factors-database.csv` | `SEO/3j-seo-algorithm/` | 알고리즘 연동 CSV |

### 플러그인 파일

| 파일 | 위치 | 설명 |
|------|------|------|
| `wp-bulk-seo.php` | `SEO/wp-bulk-seo/` | 메인 플러그인 |
| `class-bulk-optimizer.php` | `SEO/wp-bulk-seo/includes/` | 대량 최적화 |
| `class-ai-engine.php` | `SEO/wp-bulk-seo/includes/` | AI 통합 |

### 기존 문서 (참고용)

| 파일 | 설명 |
|------|------|
| `seo_factors_sample_data.csv` | SEO 요소 샘플 (40개) |
| `seo_algorithm_design.md` | 알고리즘 설계 문서 |
| `wp_bulk_seo_plugin_design.md` | 플러그인 설계 문서 |
| `SEO_ALGORITHM_IMPLEMENTATION.md` | 구현 가이드 |
| `SEO_PROJECT_SUMMARY.md` | 프로젝트 요약 |
| `20260103-SEO-Plugins-Detailed-Research.md` | 벤치마킹 연구 |

### 메모리 문서

| 파일 | 설명 |
|------|------|
| `20260104-Google-Algorithm-Leak-Analysis.md` | Google 유출 분석 |
| `20260104-Google-Ranking-Factors-Database.md` | 랭킹 팩터 상세 |
| `20260104-WP-Bulk-SEO-AEO-Business-Plan.md` | 사업 계획 |
| `20260104-SEO-Plugins-Comprehensive-Benchmarking.md` | 벤치마킹 |
| `20260104-3J-SEO-Algorithm-Design.md` | 알고리즘 설계 |
| `google-ranking-factors.csv` | 기존 랭킹 팩터 CSV |

---

## 10. 결론

WP Bulk SEO & AEO는 다음과 같은 차별점을 가집니다:

1. **실제 Google 랭킹 팩터 기반**: 2024년 유출 문서의 109개 확인된 팩터
2. **PageSpeed API v5 완전 통합**: Core Web Vitals 실시간 측정
3. **Alli AI 수준의 대량 처리**: WordPress 네이티브로 구현
4. **AI 콘텐츠 최적화**: OpenAI/Anthropic API 통합
5. **AEO 기능**: AI 검색 엔진(ChatGPT, Claude) 최적화

이 플러그인은 경쟁사 대비 가장 정확한 Google 랭킹 시뮬레이션을 제공하며, 대규모 사이트의 SEO 자동화를 가능하게 합니다.

---

**프로젝트 상태**: 설계 완료, 개발 준비 완료
**다음 단계**: WordPress 플러그인 구조 생성 및 핵심 클래스 구현

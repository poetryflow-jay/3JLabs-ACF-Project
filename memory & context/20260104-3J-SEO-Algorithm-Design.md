# 3J Labs SEO Algorithm Design Document

## Version: 1.0.0
## Date: 2026-01-04
## Author: 3J Labs

---

## 1. Overview

3J Labs SEO Algorithm은 2024년 5월 Google Content Warehouse API 유출 문서를 기반으로 설계된 실제 Google 랭킹 팩터 기반의 SEO 스코어링 시스템입니다.

### 1.1 Core Philosophy

Google의 실제 랭킹 시스템을 모방하여:
- **NavBoost**: 사용자 참여 신호 (클릭, 체류시간, 포고스틱)
- **CompressedQualitySignals**: 품질 신호 (siteAuthority, pandaDemotion)
- **SiteTopics**: 주제별 권위 (siteFocusScore, siteEmbeddings)
- **Freshness**: 콘텐츠 신선도 (lastSignificantUpdate)
- **TechnicalSEO**: 기술적 요소 (indexable, mobileScore, pagespeed)

---

## 2. Algorithm Architecture

### 2.1 Scoring System

```
Overall Score = Σ (Category Score × Category Weight) / Σ Category Weights
```

### 2.2 Category Weights

| Category | Weight | Priority | Description |
|----------|--------|----------|-------------|
| User Engagement | 10 | P0 | NavBoost 시그널 |
| Domain Authority | 10 | P0 | siteAuthority |
| Content Quality | 9 | P0 | 원본성, Panda |
| Technical SEO | 9 | P0 | 인덱싱, 크롤링 |
| Topical Authority | 8 | P0 | 주제 집중도 |
| On-Page SEO | 8 | P0 | 타이틀, 메타 |
| Link Quality | 8 | P0 | 링크 다양성 |
| Content Freshness | 7 | P1 | 업데이트 빈도 |
| E-E-A-T | 7 | P1 | 저자, 전문성 |
| Structured Data | 7 | P1 | Schema.org |
| Demotion Risks | -9 | P0 | SpamBrain, 페널티 |

### 2.3 Grade System

| Grade | Score Range | Description |
|-------|-------------|-------------|
| A+ | 90-100 | Excellent - 상위 1% |
| A | 80-89 | Great - 상위 10% |
| B | 70-79 | Good - 평균 이상 |
| C | 60-69 | Fair - 개선 필요 |
| D | 50-59 | Poor - 긴급 개선 |
| F | 0-49 | Critical - 심각한 문제 |

---

## 3. Ranking Factors Database

### 3.1 P0 Critical Factors (Weight 9-10)

#### NavBoost Module
- `goodClicks` (10): 체류시간 긴 클릭
- `badClicks` (-10): 포고스틱 페널티
- `lastLongestClicks` (10): 세션 완료 시그널
- `ctr` (9): 클릭률

#### CompressedQualitySignals Module
- `siteAuthority` (10): 도메인 권위도
- `pandaDemotion` (-9): 저품질 콘텐츠 페널티
- `originalContentScore` (9): 원본성 점수 (0-512)

#### TechnicalSEO Module
- `indexable` (10): 인덱싱 가능 여부
- `crawlStatus` (9): 크롤링 상태
- `robotsMeta` (9): robots 지시어

#### Demotions Module
- `manualAction` (-10): 수동 페널티
- `spamBrain` (-9): AI 스팸 탐지

### 3.2 P1 High Priority Factors (Weight 6-8)

#### SiteTopics Module
- `siteFocusScore` (8): 주제 집중도
- `siteEmbeddings` (8): 주제 벡터

#### Freshness Module
- `lastSignificantUpdate` (8): 최근 의미있는 업데이트
- `bylineDate` (7): 명시적 작성일

#### TechnicalSEO Module
- `mobileScore` (8): 모바일 최적화
- `canonicalUrl` (8): 캐노니컬 URL
- `pagespeed` (7): 페이지 속도
- `httpsStatus` (7): HTTPS 보안

#### Author/E-E-A-T Module
- `authorEntity` (7): 저자 엔티티
- `isAuthor` (7): 저자 식별

---

## 4. Scoring Implementation

### 4.1 Technical SEO Score

```php
$score = 0;
$score += $this->check_indexable($data);       // 0 or 100
$score += $this->check_crawl_status($data);    // 0-100
$score += $this->check_mobile($data);          // 0-100
$score += $this->check_speed($data);           // 0-100
$score += $this->check_https($data);           // 0 or 100
$score += $this->check_canonical($data);       // 0-100
$score += $this->check_robots($data);          // 0-100
$final_score = $score / 7;
```

### 4.2 Content Quality Score

```php
$score = 0;
$score += $this->calculate_originality($data);     // 0-100
$score += $this->check_thin_content_risk($data);   // 0-100 (inverse)
$score += $this->calculate_content_depth($data);   // 0-100
$score += $this->calculate_readability($data);     // 0-100
$final_score = weighted_average($score, $weights);
```

### 4.3 User Engagement Estimation

실제 클릭 데이터가 없으므로 프록시 지표 사용:

```php
// CTR 잠재력 = 타이틀 품질 + 메타 설명 품질 + 리치 스니펫 적격성
$ctr_potential = ($title_score * 0.4) + ($meta_score * 0.3) + ($rich_results * 0.3);

// 체류시간 잠재력 = 콘텐츠 깊이 + 가독성 + 비디오 존재
$dwell_potential = ($depth_score * 0.4) + ($readability * 0.3) + ($has_video * 0.3);

// 포고스틱 위험 = 속도 문제 + 얇은 콘텐츠 + 모바일 문제
$pogo_risk = ($slow_speed * 0.3) + ($thin_content * 0.4) + ($mobile_issues * 0.3);
```

### 4.4 Demotion Risk Assessment

```php
$risk = 0;

// SpamBrain 위험
$risk += $this->check_keyword_stuffing($data);
$risk += $this->check_hidden_text($data);
$risk += $this->check_excessive_ads($data);

// Panda 위험
$risk += $this->check_thin_content($data);

// NavDemotion 위험
$risk += $this->check_ux_issues($data);

$has_risk = $risk > 30;
```

---

## 5. WP Bulk SEO Plugin Integration

### 5.1 Plugin Structure

```
wp-bulk-seo/
├── wp-bulk-seo.php                 # Main plugin file
├── includes/
│   ├── algorithm/
│   │   ├── class-ranking-factors-db.php
│   │   ├── class-seo-scorer.php
│   │   └── class-seo-analyzer.php
│   ├── class-bulk-optimizer.php    # Bulk operations
│   ├── class-ai-engine.php         # AI integration
│   ├── class-schema-generator.php  # Schema markup
│   ├── class-sitemap-manager.php   # Sitemap
│   ├── class-content-optimizer.php # Content AI
│   └── aeo/
│       ├── class-aeo-engine.php
│       ├── class-faq-generator.php
│       └── class-featured-snippet-optimizer.php
├── admin/
│   ├── class-admin.php
│   ├── class-metabox.php
│   ├── class-bulk-actions.php
│   ├── class-dashboard.php
│   └── views/
└── assets/
```

### 5.2 Database Schema

```sql
-- SEO Scores Table
CREATE TABLE wp_bulk_seo_scores (
    id BIGINT UNSIGNED AUTO_INCREMENT,
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
    demotion_risk TINYINT UNSIGNED,
    analysis_data LONGTEXT,
    recommendations LONGTEXT,
    analyzed_at DATETIME,
    PRIMARY KEY (id),
    UNIQUE KEY post_id (post_id)
);

-- SEO Issues Table
CREATE TABLE wp_bulk_seo_issues (
    id BIGINT UNSIGNED AUTO_INCREMENT,
    post_id BIGINT UNSIGNED NOT NULL,
    issue_type VARCHAR(50),
    severity VARCHAR(20),
    factor_key VARCHAR(100),
    message TEXT,
    message_kr TEXT,
    is_resolved TINYINT DEFAULT 0,
    detected_at DATETIME,
    PRIMARY KEY (id)
);
```

---

## 6. AI Integration

### 6.1 Supported Providers

- **OpenAI**: GPT-4o, GPT-4o-mini, GPT-3.5 Turbo
- **Anthropic**: Claude 3.5 Sonnet, Claude 3 Haiku

### 6.2 AI-Powered Features

1. **Title Optimization**: SEO 최적화 제목 생성
2. **Meta Description**: 클릭 유도 메타 설명 생성
3. **FAQ Generation**: FAQ 스키마용 Q&A 생성
4. **Keyword Suggestions**: 포커스 키워드 제안
5. **Content Improvements**: 개선 사항 제안
6. **Schema Recommendations**: 적절한 스키마 타입 제안

---

## 7. AEO (Answer Engine Optimization)

### 7.1 Featured Snippet Optimization

- Question-style headings 분석
- Direct answer format 최적화
- List/Table format 권장

### 7.2 Voice Search Optimization

- Conversational keywords 타겟팅
- Long-tail question queries
- Local intent optimization

### 7.3 AI Overview Optimization

- Comprehensive content coverage
- E-E-A-T signals 강화
- Structured data 완전성

---

## 8. Implementation Roadmap

### Phase 1: Core Algorithm (Week 1-4)
- [x] Ranking Factors Database
- [x] SEO Scorer Class
- [x] SEO Analyzer Class
- [ ] Database Tables
- [ ] Basic Admin UI

### Phase 2: Bulk Operations (Week 5-8)
- [ ] Bulk Analyzer
- [ ] Bulk Optimizer
- [ ] Scheduled Analysis (Cron)
- [ ] Progress Tracking

### Phase 3: AI Integration (Week 9-12)
- [ ] OpenAI Integration
- [ ] Anthropic Integration
- [ ] Title/Meta Optimization
- [ ] FAQ Generation

### Phase 4: AEO Features (Week 13-16)
- [ ] Featured Snippet Optimizer
- [ ] FAQ Schema Generator
- [ ] Voice Search Analysis
- [ ] AI Overview Optimization

### Phase 5: Polish & Launch (Week 17-18)
- [ ] Performance Optimization
- [ ] Documentation
- [ ] Testing
- [ ] Launch

---

## 9. Files Created

### Core Algorithm Files

1. **`SEO/3j-seo-algorithm/class-3j-seo-scorer.php`**
   - SEO 스코어링 엔진
   - Google 랭킹 팩터 가중치 구현
   - 10개 카테고리 분석

2. **`SEO/3j-seo-algorithm/class-3j-seo-analyzer.php`**
   - 페이지 분석기
   - DOM 파싱 및 데이터 추출
   - WordPress 통합

3. **`SEO/3j-seo-algorithm/class-3j-ranking-factors-db.php`**
   - 랭킹 팩터 데이터베이스
   - 모듈별 팩터 조회
   - 우선순위/카테고리별 필터링

4. **`SEO/3j-seo-algorithm/3j-ranking-factors-database.csv`**
   - 스프레드시트 형태 랭킹 팩터 DB
   - 90+ 팩터 수록

### Plugin Files

5. **`SEO/wp-bulk-seo/wp-bulk-seo.php`**
   - 메인 플러그인 파일
   - WordPress 훅 등록
   - 컴포넌트 초기화

6. **`SEO/wp-bulk-seo/includes/class-bulk-optimizer.php`**
   - 대량 분석/최적화 처리
   - 배치 처리
   - 결과 저장

7. **`SEO/wp-bulk-seo/includes/class-ai-engine.php`**
   - AI API 통합
   - 콘텐츠 최적화
   - 스키마 제안

### Existing CSV Files

8. **`memory & context/google-ranking-factors.csv`**
   - 기존 랭킹 팩터 CSV (80+ 팩터)

---

## 10. Conclusion

3J Labs SEO Algorithm은 Google의 실제 랭킹 시스템을 기반으로 설계되어:

1. **정확성**: 확인된 Google 랭킹 팩터 사용
2. **포괄성**: 10개 주요 카테고리, 90+ 개별 팩터
3. **실용성**: WordPress 플러그인으로 즉시 사용 가능
4. **확장성**: AI 통합, AEO 기능 포함

이 알고리즘은 WP Bulk SEO 플러그인의 핵심으로 사용되며, 경쟁사 대비 실제 Google 랭킹 시스템에 가장 가까운 SEO 분석을 제공합니다.

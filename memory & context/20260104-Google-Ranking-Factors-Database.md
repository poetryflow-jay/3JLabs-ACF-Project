# Google 검색 랭킹 팩터 데이터베이스

**작성일**: 2026년 1월 4일
**출처**: Google Content Warehouse API 유출 (2024년 5월)
**총 모듈 수**: 2,596개 모듈, 14,014개 속성
**목적**: WP Bulk SEO & AEO 개발 참고 자료

---

## 데이터베이스 구조

| 컬럼 | 설명 |
|------|------|
| ModuleName | Google 내부 모듈/속성 이름 |
| Impact on Search Ranking | 검색 랭킹에 미치는 영향 설명 |
| Estimated Weight (1-10) | 추정 가중치 (AI 생성) |
| SEO Factor/Focus Area | SEO 영역/초점 |

---

## Part 1: 핵심 랭킹 시스템 (Core Ranking Systems)

### 1.1 NavBoost 시스템 (클릭 기반) - 가중치 9-10

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **NavBoost** | 클릭 데이터 기반 검색 결과 재랭킹. 13개월 롤링 윈도우 사용. Google의 가장 중요한 랭킹 신호 중 하나 | 10 | User Engagement / CTR |
| **goodClicks** | 사용자가 만족한 클릭 (오래 머문 클릭). 해당 페이지 랭킹 상승 | 9 | User Satisfaction |
| **badClicks** | 빠르게 이탈한 클릭 (pogo-sticking). 해당 페이지 랭킹 하락 | 9 | Bounce Rate |
| **lastLongestClicks** | 검색 세션의 마지막이자 가장 오래 머문 클릭. 가장 강력한 만족 신호 | 10 | Dwell Time |
| **unsquashedClicks** | 검증된 실제 클릭. 봇이나 조작되지 않은 순수 클릭 | 8 | Click Authenticity |
| **squashedClicks** | 비정상적으로 판단되어 무시된 클릭 | 7 | Spam Detection |
| **impressions** | 검색 결과에서의 노출 횟수. CTR 계산에 사용 | 6 | Visibility |
| **ctr** | 클릭률 (클릭/노출). NavBoost 핵심 지표 | 9 | Click-Through Rate |

### 1.2 Mustang 시스템 (초기 랭킹) - 가중치 8-10

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **Mustang** | 1차 스코어링 및 랭킹 시스템. 모든 검색 쿼리의 초기 랭킹 결정 | 10 | Core Ranking Algorithm |
| **CompressedQualitySignals** | 핵심 품질 신호 집합 (siteAuthority, pandaDemotion 등) | 9 | Quality Signals |
| **Ascorer** | 정보 검색 점수 계산 알고리즘 | 8 | Relevance Scoring |

### 1.3 Twiddlers (재랭킹 함수) - 가중치 7-9

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **FreshnessTwiddler** | 콘텐츠 신선도 기반 재랭킹. 뉴스성 쿼리에 특히 중요 | 8 | Content Freshness |
| **QualityBoost** | 품질 신호 기반 랭킹 향상 | 9 | Content Quality |
| **RealTimeBoost** | 실시간 이벤트/트렌드 관련 콘텐츠 부스트 | 7 | Trending Topics |
| **WebImageBoost** | 이미지 포함 웹페이지 부스트 | 6 | Visual Content |
| **VideoBoost** | 비디오 콘텐츠 부스트 | 6 | Video Content |
| **LocalBoost** | 로컬 검색 결과 부스트 | 8 | Local SEO |

---

## Part 2: 사이트 권위도 (Site Authority) - 가중치 8-10

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **siteAuthority** | 도메인 레벨 신뢰도/권위도 점수. Google이 공식적으로 부인했지만 실제 존재 확인 | 10 | Domain Authority |
| **siteFocusScore** | 사이트가 특정 주제에 얼마나 집중하는지 평가 | 8 | Topical Authority |
| **siteRadius** | 페이지 콘텐츠가 사이트 핵심 주제로부터 얼마나 벗어나는지 측정 | 7 | Topic Consistency |
| **siteEmbeddings** | 사이트 핵심 주제를 나타내는 압축 벡터 임베딩 | 8 | Topic Modeling |
| **site2vec** | 사이트 전체의 주제 벡터 표현 | 7 | Site Representation |
| **hostAge** | 도메인 나이. 신규 사이트 샌드박스 적용에 사용 | 7 | Domain Age |
| **pageQuality** | 개별 페이지 품질 점수 | 9 | Page Quality |
| **Host NSR** | 호스트 레벨 사이트 랭크. 도메인 섹션별 품질 평가 | 8 | Domain Section Quality |

---

## Part 3: 콘텐츠 품질 (Content Quality) - 가중치 7-10

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **OriginalContentScore** | 콘텐츠 원본성 점수. 중복/도용 콘텐츠 감지 | 9 | Content Originality |
| **contentEffort** | LLM 기반 콘텐츠 노력도 추정 | 7 | Content Effort |
| **pandaDemotion** | Panda 알고리즘 강등. 저품질 콘텐츠 페널티 | 9 | Quality Penalty |
| **titlematchScore** | 제목과 콘텐츠의 일치도 점수 | 8 | Title Relevance |
| **bodyMatch** | 본문과 쿼리의 일치도 | 8 | Content Relevance |
| **headingMatch** | 헤딩 태그와 쿼리 일치도 | 7 | Heading Optimization |
| **tokenCount** | 콘텐츠 토큰(단어) 수 | 5 | Content Length |
| **charCount** | 문자 수 | 4 | Content Size |
| **wordWeight** | 단어별 가중치 | 6 | Keyword Importance |
| **fontSizeWeight** | 글자 크기에 따른 가중치 (큰 글자 = 중요) | 5 | Typography |
| **anchorWeight** | 앵커텍스트 가중치 | 7 | Anchor Text |

---

## Part 4: 링크 신호 (Link Signals) - 가중치 7-10

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **PageRank** | 페이지 랭크. 여전히 홈페이지 레벨에서 활용 | 9 | Link Authority |
| **sourceType** | 링크 소스 유형 분류 | 7 | Link Classification |
| **linkDiversity** | 링크 다양성 (여러 도메인에서 오는 링크) | 8 | Link Diversity |
| **anchorText** | 앵커텍스트 분석 | 8 | Anchor Optimization |
| **anchorMismatch** | 앵커텍스트 불일치 (강등 신호) | 7 | Anchor Penalty |
| **indexTier** | 인덱스 티어 (상위 티어 링크 = 높은 가중치) | 8 | Link Quality Tier |
| **internalLinks** | 내부 링크 구조 | 7 | Internal Linking |
| **externalLinks** | 외부 링크 분석 | 6 | Outbound Links |
| **homepagePageRank** | 홈페이지 PageRank (도메인 전체에 영향) | 8 | Homepage Authority |

---

## Part 5: Chrome 및 사용자 데이터 - 가중치 6-8

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **ChromeInTotal** | Chrome 브라우저의 총 페이지뷰 | 7 | Chrome Usage Data |
| **topUrl** | Chrome 클릭 데이터 기반 가장 많이 방문한 URL | 8 | Popular Pages |
| **chrome_trans_clicks** | Chrome 전환 클릭 | 7 | Chrome Clicks |
| **dwellTime** | 페이지 체류 시간 | 8 | User Engagement |
| **scrollDepth** | 스크롤 깊이 (얼마나 아래로 스크롤했는지) | 6 | Content Consumption |
| **returnToSerp** | SERP로 돌아가는 비율 (높으면 부정적) | 7 | User Satisfaction |

---

## Part 6: 신선도 및 시간 신호 (Freshness) - 가중치 5-8

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **bylineDate** | 작성 날짜 (작성자명 옆 날짜) | 7 | Publication Date |
| **syntacticDate** | 구문적으로 추출된 날짜 | 6 | Date Extraction |
| **semanticDate** | 의미적으로 해석된 날짜 | 6 | Date Understanding |
| **lastModified** | 마지막 수정 날짜 | 7 | Update Frequency |
| **freshness** | 콘텐츠 신선도 종합 점수 | 8 | Content Freshness |
| **firstSeen** | Google이 처음 발견한 날짜 | 5 | Discovery Date |

---

## Part 7: 기술적 SEO (Technical SEO) - 가중치 6-9

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **crawlStatus** | 크롤링 상태 | 8 | Crawlability |
| **indexable** | 인덱싱 가능 여부 | 9 | Indexability |
| **robotsMeta** | robots 메타 태그 | 8 | Crawl Directives |
| **canonicalUrl** | 캐노니컬 URL | 8 | Canonical Tags |
| **mobileScore** | 모바일 점수 | 8 | Mobile Optimization |
| **pagespeed** | 페이지 속도 | 7 | Page Speed |
| **coreWebVitals** | Core Web Vitals (LCP, FID, CLS) | 8 | User Experience |
| **httpsStatus** | HTTPS 보안 여부 | 7 | Security |
| **clutterScore** | 혼잡도 점수 (광고, 팝업 등) | 7 | Page Clutter |
| **violatesMobileInterstitialPolicy** | 모바일 인터스티셜 정책 위반 | 7 | Mobile UX Penalty |

---

## Part 8: Schema 및 구조화된 데이터 - 가중치 6-8

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **richSnippets** | 리치 스니펫 적격성 | 7 | Rich Results |
| **schemaOrg** | Schema.org 마크업 | 7 | Structured Data |
| **faqSchema** | FAQ 스키마 | 6 | FAQ Markup |
| **productSchema** | 제품 스키마 | 7 | Product Markup |
| **reviewSchema** | 리뷰 스키마 | 7 | Review Markup |
| **breadcrumbSchema** | 브레드크럼 스키마 | 6 | Navigation Markup |
| **localBusinessSchema** | 로컬 비즈니스 스키마 | 8 | Local SEO |

---

## Part 9: E-E-A-T 및 저자 신호 - 가중치 6-8

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **isAuthor** | 저자 식별 여부 | 7 | Author Identification |
| **authorEntity** | 저자 엔티티 연결 | 7 | Author Entity |
| **authorTrust** | 저자 신뢰도 | 8 | Author Authority |
| **Connectedness** | 엔티티 연결성 | 6 | Entity Connections |
| **DocScore** | 문서 점수 | 7 | Document Quality |
| **NormalizedTopicality** | 정규화된 주제 관련성 | 7 | Topical Relevance |
| **RelevanceScore** | 관련성 점수 | 8 | Content Relevance |
| **entityMentions** | 엔티티 언급 | 6 | Entity Recognition |

---

## Part 10: 강등 및 페널티 신호 - 가중치 7-9

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **navDemotion** | 네비게이션 문제로 인한 강등 | 8 | Navigation Issues |
| **exactMatchDomainDemotion** | 정확 일치 도메인 강등 | 7 | EMD Penalty |
| **productReviewDemotion** | 저품질 제품 리뷰 강등 | 8 | Review Quality |
| **localMismatch** | 지역 불일치 강등 | 7 | Local Relevance |
| **spamBrain** | 스팸 감지 AI 시스템 | 9 | Spam Detection |
| **manualAction** | 수동 조치 (패널티) | 10 | Manual Penalty |
| **linkSpam** | 링크 스팸 감지 | 9 | Link Spam |

---

## Part 11: 특수 모듈 및 화이트리스트 - 가중치 5-8

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **isElectionAuthority** | 선거 관련 권위 사이트 화이트리스트 | 8 | Election Content |
| **isCovidLocalAuthority** | 코로나 관련 권위 사이트 화이트리스트 | 8 | Health Content |
| **smallPersonalSite** | 소규모 개인 사이트 식별 | 6 | Small Site Treatment |
| **isYMYL** | Your Money Your Life 콘텐츠 식별 | 8 | YMYL Content |
| **newsSite** | 뉴스 사이트 식별 | 7 | News Classification |
| **blogSite** | 블로그 사이트 식별 | 6 | Blog Classification |
| **ecommerceSite** | 이커머스 사이트 식별 | 7 | E-commerce |

---

## Part 12: 컨텐츠 조정/안전 모듈 - 가중치 6-9

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **AbuseiamAbuseType** | 악성 콘텐츠 분류 및 제거 | 8 | Content Moderation/Safety |
| **AbuseiamAgeRestriction** | 연령 기반 콘텐츠 제한 | 7 | Age-Targeted Search |
| **AbuseiamAndRestriction** | 복합 제한 로직 | 6 | Content Filtering Logic |
| **AbuseiamClient** | 콘텐츠 소스 식별 | 7 | Source Identification |
| **AbuseiamConstantRestriction** | 상수 제한 규칙 | 6 | Content Filtering Rules |
| **AbuseiamContentRestriction** | 콘텐츠 삭제 지정 | 9 | Manual Action |
| **AbuseiamEvaluation** | 콘텐츠 판정 설명 | 5 | Content Filtering Feedback |
| **AbuseiamGeoRestriction** | 지역 기반 콘텐츠 제한 | 7 | Geo-Targeting |
| **AbuseiamHash** | 중복/유사 콘텐츠 필터링 | 6 | Duplicate Detection |
| **AbuseiamNameValuePair** | 기타 데이터 쌍 저장 | 4 | Data Storage |
| **AbuseiamNotRestriction** | 제한 부정 (확장) | 5 | Content Filtering Logic |
| **AbuseiamOrRestriction** | 다중 제한 적용 | 5 | Content Filtering Logic |

---

## Part 13: 크롤링 및 인덱싱 시스템

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **Trawler** | 웹 크롤링 시스템 | 8 | Crawl System |
| **Alexandria** | 인덱싱 시스템 | 8 | Indexing System |
| **TeraGoogle** | 대규모 인덱스 저장 | 7 | Index Storage |
| **crawlFrequency** | 크롤 빈도 | 7 | Crawl Frequency |
| **crawlBudget** | 크롤 예산 | 7 | Crawl Budget |
| **robotsAccess** | robots.txt 접근 | 8 | Crawl Access |

---

## Part 14: 검색 품질 및 SERP 조립

| ModuleName | Impact on Search Ranking | Weight | SEO Focus Area |
|------------|-------------------------|--------|----------------|
| **Glue** | 유니버설 검색 결과 랭킹 | 8 | Universal Search |
| **Tangram** | SERP 조립 시스템 | 7 | SERP Assembly |
| **SuperRoot** | 쿼리 처리 시스템 | 8 | Query Processing |
| **diversityBoost** | 결과 다양성 부스트 | 6 | Result Diversity |
| **featuredSnippet** | 추천 스니펫 적격성 | 7 | Featured Snippets |
| **knowledgePanel** | 지식 패널 적격성 | 7 | Knowledge Graph |

---

## 활용 가이드: WP Bulk SEO & AEO 개발

### 필수 구현 모듈 (가중치 9-10)

```php
// Priority 1: 가중치 9-10 모듈
$critical_modules = [
    'NavBoost'              => 'A/B 테스트, CTR 최적화',
    'goodClicks'            => '체류 시간 분석',
    'lastLongestClicks'     => '검색 종료 신호 분석',
    'siteAuthority'         => '도메인 권위도 점수',
    'OriginalContentScore'  => '콘텐츠 원본성 체커',
    'pandaDemotion'         => 'Panda 위험 평가',
    'PageRank'              => '링크 권위도 분석',
    'spamBrain'             => '스팸 위험 감지',
    'manualAction'          => '수동 조치 알림',
];
```

### 중요 구현 모듈 (가중치 7-8)

```php
// Priority 2: 가중치 7-8 모듈
$important_modules = [
    'titlematchScore'       => '제목 최적화 점수',
    'siteFocusScore'        => '주제 전문성 분석',
    'ChromeInTotal'         => '사용자 참여 지표',
    'freshness'             => '콘텐츠 신선도',
    'coreWebVitals'         => '페이지 경험',
    'authorTrust'           => 'E-E-A-T 점수',
    'linkDiversity'         => '백링크 다양성',
];
```

### 보조 구현 모듈 (가중치 5-6)

```php
// Priority 3: 가중치 5-6 모듈
$secondary_modules = [
    'tokenCount'            => '콘텐츠 길이 분석',
    'scrollDepth'           => '스크롤 깊이',
    'breadcrumbSchema'      => '스키마 검증',
    'smallPersonalSite'     => '사이트 유형 분류',
];
```

---

## 참고 자료

- [Search Engine Land: Google Search Document Leak](https://searchengineland.com/google-search-document-leak-ranking-442617)
- [Growfusely: Confirmed Ranking Factors](https://growfusely.com/blog/google-api-leak/)
- [Hobo Web: Google Content Warehouse Leak](https://www.hobo-web.co.uk/the-google-content-warehouse-leak-2024/)
- [AIOSEO: Google Search Algorithm Leak](https://aioseo.com/google-search-algorithm-leak/)
- [GrowthSRC: Search Ranking Factors Database](https://searchrankingfactors.com)
- GitHub 원본: [elixir-google-api commit](https://github.com/googleapis/elixir-google-api/commit/d7a637f4391b2174a2cf43ee11e6577a204a161e)

---

**작성일**: 2026-01-04
**작성자**: Jason (CTO, 3J Labs)
**버전**: 1.0

---

**© 2026 3J Labs. All rights reserved.**

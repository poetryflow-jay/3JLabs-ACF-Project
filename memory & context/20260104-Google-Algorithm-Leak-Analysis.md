# Google 검색 알고리즘 유출 문서 분석 보고서

**작성일**: 2026년 1월 4일
**작성자**: Jason (CTO, 3J Labs)
**버전**: 1.0
**목적**: WP Bulk SEO & AEO 개발을 위한 실제 Google 랭킹 시그널 분석

---

## Executive Summary

2024년 5월, Google Content Warehouse API 내부 문서가 GitHub를 통해 실수로 공개되었습니다. 이 유출은 **2,596개 모듈**과 **14,014개 속성**을 포함하며, Google이 공개적으로 부인해온 많은 랭킹 요소들이 실제로 사용되고 있음을 확인했습니다.

```
유출 문서 핵심 발견
├── NavBoost: 클릭 데이터 기반 랭킹 시스템 (84회 언급)
├── SiteAuthority: 도메인 권위도 점수 존재 확인
├── Chrome 데이터: 브라우저 사용자 행동 수집
├── Sandbox: 신규 사이트 제한 (hostAge)
└── 클릭 신호: goodClicks, badClicks, lastLongestClicks
```

---

## Part 1: Google 랭킹 아키텍처

### 1.1 멀티스테이지 랭킹 파이프라인

```
Google 검색 랭킹 5단계 파이프라인
├── Stage 1: Discovery & Fetching (Trawler)
│   └── 웹 크롤링, 인덱싱 여부 결정
│
├── Stage 2: Indexing & Tiering (Alexandria, TeraGoogle)
│   ├── 콘텐츠 품질 등급 분류
│   └── 상위 티어 링크 = 더 높은 가중치
│
├── Stage 3: Initial Scoring (Mustang)
│   ├── 1차 랭킹 점수 계산
│   └── CompressedQualitySignals 활용
│
├── Stage 4: Re-ranking (Twiddlers)
│   ├── NavBoost (클릭 기반)
│   ├── FreshnessTwiddler (신선도)
│   ├── QualityBoost (품질)
│   └── 사용자 행동 기반 재조정
│
└── Stage 5: SERP Assembly (Glue, Tangram)
    ├── 유니버설 검색 기능 랭킹
    └── 최종 결과 페이지 조립
```

### 1.2 핵심 데이터 구조

| 구조 | 역할 | 포함 정보 |
|------|------|----------|
| **CompositeDoc** | URL별 마스터 레코드 | 모든 정보 집계 |
| **PerDocData** | 문서 레벨 시그널 | 온페이지 요소, 품질 점수, 스팸 지표, 사용자 참여 |
| **CompressedQualitySignals** | 핵심 품질 신호 | siteAuthority, pandaDemotion, navDemotion |

---

## Part 2: 확인된 랭킹 시그널 (14,014개 중 핵심)

### 2.1 NavBoost 시스템 (가장 중요)

```
NavBoost 시스템 분석
├── 정의: 클릭 데이터 기반 재랭킹 Twiddler
├── 데이터 기간: 13개월 롤링 윈도우
├── DOJ 재판 증언: "가장 중요한 신호 중 하나"
│
├── 클릭 메트릭
│   ├── goodClicks: 만족스러운 사용자 상호작용
│   ├── badClicks: 빠른 이탈 (pogo-sticking)
│   ├── lastLongestClicks: 검색 종료 신호 (가장 강력)
│   └── unsquashedClicks: 검증된 실제 클릭
│
└── 작동 방식
    ├── 초기 Mustang 랭킹에 직접 영향 X
    ├── 재랭킹 단계에서 순위 조정
    └── 국가/지역/디바이스별 세분화
```

**WP Bulk SEO 적용 포인트**:
- CTR 최적화 도구 (제목/설명 A/B 테스트)
- 사용자 체류 시간 분석
- 이탈률 모니터링

### 2.2 사이트 권위도 (SiteAuthority)

```php
// 유출 문서에서 확인된 siteAuthority 관련 속성
$site_authority_signals = [
    'siteAuthority'    => '도메인 레벨 신뢰도 점수',
    'siteFocusScore'   => '주제 전문성 점수',
    'siteRadius'       => '핵심 주제로부터의 거리',
    'site2vec'         => '사이트 주제 벡터 표현',
];
```

**Google의 공식 입장 vs 현실**:
| 공식 입장 | 유출 문서 확인 |
|----------|---------------|
| "Domain Authority 사용 안 함" | ✅ siteAuthority 존재 확인 |
| "PageRank 더 이상 중요하지 않음" | ✅ 홈페이지 PageRank 여전히 활용 |

**WP Bulk SEO 적용 포인트**:
- 사이트 권위도 분석 대시보드
- 주제 전문성 점수 계산 도구

### 2.3 Chrome 데이터 활용

```
Chrome 데이터 수집 확인
├── 공식 입장: "Chrome 데이터 랭킹에 사용 안 함"
├── 유출 확인: ❌ 거짓 - 실제 사용 중
│
├── 수집 데이터
│   ├── 페이지 체류 시간
│   ├── 스크롤 깊이
│   ├── 클릭 패턴
│   └── topURL (가장 많이 클릭된 페이지)
│
└── ChromeInTotal 모듈 존재 확인
```

### 2.4 Sandbox 효과 (hostAge)

```
신규 사이트 샌드박스
├── 공식 입장: "샌드박스 없음"
├── 유출 확인: ✅ hostAge 속성 존재
│
├── 작동 방식
│   ├── 새 도메인의 순위 상승 제한
│   ├── 신뢰성 입증 기간 필요
│   └── 시간이 지나면서 제한 완화
│
└── SEO 시사점
    ├── 신규 사이트는 인내심 필요
    ├── 초기 고품질 콘텐츠 집중
    └── 신뢰할 수 있는 백링크 확보
```

### 2.5 콘텐츠 품질 신호

| 신호 | 설명 | 측정 방식 |
|------|------|----------|
| **OriginalContentScore** | 콘텐츠 원본성 | 중복/도용 감지 |
| **contentEffort** | 콘텐츠 노력도 | LLM 기반 추정 |
| **pandaDemotion** | Panda 강등 | 저품질 콘텐츠 페널티 |
| **clutterScore** | 혼잡도 점수 | 광고/방해 요소 |
| **titlematchScore** | 제목 일치도 | 제목↔콘텐츠 정렬 |

```php
// 콘텐츠 품질 평가 모델 예시
class JJ_Content_Quality_Analyzer {

    // 원본성 점수 계산
    public function calculate_originality_score( $content ) {
        // 중복 콘텐츠 검사
        // 인용/참조 vs 복사 구분
        // 고유 가치 평가
    }

    // 콘텐츠 노력도 추정
    public function estimate_content_effort( $content ) {
        // 단어 수
        // 이미지/비디오 포함
        // 연구/데이터 인용
        // 구조화 수준
    }
}
```

### 2.6 백링크 및 앵커텍스트

```
백링크 시그널 분석
├── 링크 다양성과 관련성 여전히 핵심
├── PageRank 홈페이지 레벨에서 작동
│
├── 앵커텍스트 분석
│   ├── 정확 일치 키워드 과다 = 페널티 위험
│   ├── 자연스러운 앵커 분포 선호
│   └── 링크 맥락의 품질 평가
│
├── 티어링 시스템
│   ├── 상위 티어 사이트 링크 = 높은 가중치
│   ├── 하위 티어 사이트 링크 = 낮은 가중치
│   └── 스팸 사이트 링크 = 무시 또는 페널티
│
└── sourceType 속성
    ├── 링크 출처 유형 분류
    └── 신뢰도에 따른 가중치 차등
```

### 2.7 E-E-A-T 신호 (저자 전문성)

```
저자/전문성 신호
├── isAuthor 메트릭: 저자 식별 및 저장
├── 저자 엔티티 연관성
│
├── 측정 요소
│   ├── Connectedness: 엔티티 연결성
│   ├── DocScore: 문서 점수
│   ├── NormalizedTopicality: 정규화된 주제 관련성
│   └── RelevanceScore: 관련성 점수
│
└── SEO 시사점
    ├── 저자 프로필 구축
    ├── 전문성 증명 콘텐츠
    └── 저자 온라인 입지 강화
```

### 2.8 신선도 신호 (Freshness)

```
콘텐츠 신선도 평가
├── bylineDate: 작성 날짜
├── syntacticDate: 구문적 날짜 추출
├── semanticDate: 의미적 날짜 해석
│
├── FreshnessTwiddler
│   ├── 쿼리 유형별 신선도 요구 차등
│   ├── 뉴스성 쿼리 = 최신 콘텐츠 우선
│   └── 에버그린 쿼리 = 품질 우선
│
└── 페이지 업데이트 추적
    ├── 자주 업데이트된 콘텐츠 우선순위
    └── 의미 있는 변경 vs 표면적 변경 구분
```

### 2.9 강등 신호 (Demotions)

| 강등 유형 | 설명 |
|----------|------|
| **anchorMismatch** | 앵커텍스트 불일치 |
| **navDemotion** | 네비게이션 문제 |
| **exactMatchDomain** | 정확 일치 도메인 남용 |
| **productReviewDemotion** | 저품질 제품 리뷰 |
| **localMismatch** | 지역 관련성 부족 |
| **clutterScore** | 광고/혼잡 요소 과다 |
| **violatesMobileInterstitialPolicy** | 모바일 인터스티셜 위반 |

---

## Part 3: WP Bulk SEO & AEO 개발 반영 사항

### 3.1 필수 구현 기능 (Google 유출 기반)

```
Priority 1: 클릭/사용자 행동 최적화
├── 제목 A/B 테스트 (titlematchScore 최적화)
├── 메타 설명 A/B 테스트 (CTR 향상)
├── SERP 미리보기 (클릭 유도)
└── 이탈률/체류시간 분석

Priority 2: 콘텐츠 품질 분석
├── 원본성 점수 (OriginalContentScore)
├── 콘텐츠 노력도 추정 (contentEffort)
├── 중복 콘텐츠 감지
└── 가독성 분석

Priority 3: 사이트 권위도
├── 사이트 전문성 점수 (siteFocusScore)
├── 주제 일관성 분석 (siteRadius)
├── 내부 링크 구조 최적화
└── 외부 링크 품질 분석

Priority 4: 기술적 SEO
├── 모바일 경험 최적화
├── 페이지 속도 분석 (Core Web Vitals)
├── 구조화된 데이터 (Schema)
└── 크롤링 최적화
```

### 3.2 새로운 모듈 설계

```php
/**
 * Google 유출 문서 기반 SEO 분석 모듈
 */
class JJ_Google_Signals_Analyzer {

    /**
     * NavBoost 최적화 분석
     */
    public function analyze_click_optimization( $post_id ) {
        return [
            'title_ctr_potential'       => $this->estimate_title_ctr( $post_id ),
            'description_ctr_potential' => $this->estimate_description_ctr( $post_id ),
            'suggested_improvements'    => $this->get_ctr_suggestions( $post_id ),
        ];
    }

    /**
     * 사이트 권위도 분석
     */
    public function analyze_site_authority() {
        return [
            'estimated_site_authority' => $this->calculate_site_authority(),
            'site_focus_score'         => $this->calculate_focus_score(),
            'topic_consistency'        => $this->analyze_topic_consistency(),
        ];
    }

    /**
     * 콘텐츠 품질 분석 (Panda 기준)
     */
    public function analyze_content_quality( $post_id ) {
        return [
            'originality_score' => $this->check_originality( $post_id ),
            'content_effort'    => $this->estimate_effort( $post_id ),
            'clutter_score'     => $this->check_clutter( $post_id ),
            'panda_risk'        => $this->assess_panda_risk( $post_id ),
        ];
    }

    /**
     * E-E-A-T 신호 분석
     */
    public function analyze_eeat( $post_id ) {
        return [
            'author_authority'    => $this->get_author_authority( $post_id ),
            'entity_connections'  => $this->get_entity_connections( $post_id ),
            'expertise_signals'   => $this->detect_expertise_signals( $post_id ),
        ];
    }
}
```

### 3.3 UI/대시보드 설계

```
WP Bulk SEO 대시보드 (Google 유출 기반)
├── Overview
│   ├── 사이트 권위도 점수
│   ├── 평균 콘텐츠 품질 점수
│   ├── 클릭 최적화 점수
│   └── 강등 위험 알림
│
├── NavBoost Optimizer
│   ├── 제목 A/B 테스트 관리
│   ├── CTR 예측 분석
│   ├── SERP 미리보기
│   └── 클릭 성과 추적
│
├── Content Quality
│   ├── 원본성 점수 그래프
│   ├── Panda 위험 평가
│   ├── 저품질 콘텐츠 목록
│   └── 개선 제안
│
├── Authority Builder
│   ├── 주제 전문성 분석
│   ├── 내부 링크 맵
│   ├── 백링크 품질 분석
│   └── 권위도 트렌드
│
├── E-E-A-T Analyzer
│   ├── 저자 프로필 현황
│   ├── 전문성 신호 감지
│   ├── 엔티티 연결 맵
│   └── 개선 권장사항
│
└── Demotion Monitor
    ├── 강등 위험 스캔
    ├── 문제 페이지 목록
    ├── 자동 수정 제안
    └── 이력 추적
```

---

## Part 4: 경쟁사 대비 차별화 전략

### 4.1 현재 SEO 플러그인 한계

| 플러그인 | NavBoost 최적화 | 사이트 권위도 | 강등 모니터링 |
|----------|----------------|--------------|--------------|
| Rank Math | ❌ | ❌ | ❌ |
| Yoast SEO | ❌ | ❌ | ❌ |
| AIOSEO | ❌ | ❌ | ❌ |
| **WP Bulk SEO (목표)** | ✅ | ✅ | ✅ |

### 4.2 WP Bulk SEO 고유 가치

```
"Google 유출 문서 기반 최초의 SEO 플러그인"

차별화 포인트
├── 1. NavBoost 최적화 도구
│   ├── 제목 A/B 테스트 (titlematchScore 기반)
│   ├── CTR 예측 AI
│   └── 클릭 성과 분석
│
├── 2. 사이트 권위도 분석
│   ├── siteFocusScore 시뮬레이션
│   ├── 주제 일관성 점수
│   └── 권위도 향상 로드맵
│
├── 3. Panda/강등 방지
│   ├── 콘텐츠 품질 점수
│   ├── 강등 위험 사전 감지
│   └── 자동 수정 제안
│
├── 4. E-E-A-T 빌더
│   ├── 저자 프로필 관리
│   ├── 전문성 신호 강화
│   └── 엔티티 최적화
│
└── 5. 실시간 모니터링
    ├── Google 알고리즘 업데이트 감지
    ├── 순위 변동 알림
    └── 경쟁사 분석
```

---

## Part 5: 마케팅 메시지 전략

### 5.1 핵심 메시지

```
"Google이 숨긴 랭킹 시그널, 이제 당신도 활용하세요"

서브 메시지
├── "NavBoost가 뭔지 아시나요? 우리는 압니다"
├── "14,014개 랭킹 신호 기반 최적화"
├── "Google이 부인했던 것들, 이제 활용하세요"
└── "경쟁사가 모르는 SEO 인사이트"
```

### 5.2 콘텐츠 마케팅 전략

```
블로그 시리즈 (SEO 리더십)
├── "Google 알고리즘 유출: 무엇이 밝혀졌나"
├── "NavBoost 완전 분석: 클릭이 랭킹에 미치는 영향"
├── "siteAuthority의 비밀: Google이 숨긴 도메인 권위도"
├── "Sandbox는 실재했다: 신규 사이트 전략"
├── "Panda 2024: 콘텐츠 품질 점수의 실체"
└── "Chrome 데이터의 진실: Google이 당신을 추적하는 방법"

웨비나 시리즈
├── "Google 유출 문서 깊이 분석"
├── "NavBoost 최적화 실전 가이드"
├── "사이트 권위도 높이는 전략"
└── "E-E-A-T 2025: 전문성 구축 전략"
```

---

## Part 6: 기술 로드맵 업데이트

### 6.1 Google 유출 기반 기능 우선순위

```
Phase 1 (즉시): 기본 분석 도구
├── titlematchScore 분석
├── 콘텐츠 원본성 체커
├── 제목/설명 품질 점수
└── 기본 강등 위험 감지

Phase 2 (단기): NavBoost 최적화
├── 제목 A/B 테스트 엔진
├── CTR 예측 AI
├── SERP 미리보기
└── 클릭 성과 추적

Phase 3 (중기): 권위도 빌더
├── 사이트 전문성 분석
├── 주제 일관성 점수
├── 내부 링크 최적화
└── 권위도 트렌드 추적

Phase 4 (장기): 고급 분석
├── E-E-A-T 분석 도구
├── 경쟁사 비교 분석
├── 알고리즘 업데이트 감지
└── 예측 SEO 인사이트
```

---

## Part 7: 참고 자료 및 출처

### 7.1 주요 출처

| 출처 | URL | 내용 |
|------|-----|------|
| GitHub 원본 | [elixir-google-api commit](https://github.com/googleapis/elixir-google-api/commit/d7a637f4391b2174a2cf43ee11e6577a204a161e) | 유출 문서 원본 |
| Search Engine Land | [분석 기사](https://searchengineland.com/google-search-document-leak-ranking-442617) | 종합 분석 |
| Hobo Web | [상세 분석](https://www.hobo-web.co.uk/the-google-content-warehouse-leak-2024/) | 기술적 세부사항 |
| Growfusely | [랭킹 팩터 정리](https://growfusely.com/blog/google-api-leak/) | 확인된 랭킹 요소 |

### 7.2 Google 공식 입장 vs 유출 확인

| 항목 | Google 공식 입장 | 유출 문서 확인 |
|------|-----------------|---------------|
| 클릭 데이터 | "랭킹에 사용 안 함" | ✅ NavBoost로 사용 |
| Domain Authority | "없음" | ✅ siteAuthority 존재 |
| Chrome 데이터 | "사용 안 함" | ✅ ChromeInTotal 모듈 |
| Sandbox | "없음" | ✅ hostAge 속성 |
| PageRank | "더 이상 중요하지 않음" | ✅ 홈페이지 레벨 활용 |

---

## Part 8: 결론

### 8.1 핵심 인사이트

1. **클릭이 중요하다**: NavBoost는 Google의 가장 강력한 랭킹 신호 중 하나
2. **도메인 권위도는 실재**: siteAuthority가 실제로 존재하고 사용됨
3. **신규 사이트는 불리**: Sandbox 효과가 hostAge로 구현됨
4. **Chrome이 추적 중**: 사용자 행동 데이터가 랭킹에 영향
5. **원본 콘텐츠가 승리**: OriginalContentScore로 측정됨

### 8.2 WP Bulk SEO & AEO 개발 방향

```
"Google이 실제로 사용하는 랭킹 신호를 기반으로
가장 정확한 SEO 최적화 도구를 개발한다"

핵심 가치
├── NavBoost 최적화 (클릭/CTR)
├── 사이트 권위도 분석
├── 콘텐츠 품질 평가 (Panda 기준)
├── 강등 위험 사전 감지
└── E-E-A-T 신호 강화
```

---

**작성일**: 2026-01-04
**작성자**: Jason (CTO, 3J Labs)
**검토**: Jay (CEO, 3J Labs)
**버전**: 1.0

---

**© 2026 3J Labs. All rights reserved.**

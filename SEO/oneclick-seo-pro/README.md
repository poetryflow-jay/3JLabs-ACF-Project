# WP 1-Click SEO

워드프레스용 올인원 SEO & AEO 최적화 플러그인

## 개요

WP 1-Click SEO는 Google Algorithm Leak (2024.05) 분석을 기반으로 구축된 차세대 SEO 플러그인입니다. 200개 이상의 랭킹 요소를 실시간으로 분석하고, AI를 활용한 콘텐츠 최적화, Answer Engine Optimization(AEO)까지 지원합니다.

## 주요 기능

### 1. SEO 분석 엔진
- **200+ 랭킹 요소 분석**: NavBoost, siteAuthority, pageQuality 등 핵심 시그널 평가
- **Core Web Vitals 통합**: LCP, FID, CLS, FCP, TTFB, TBT 측정
- **PageSpeed API v5 연동**: Google 공식 API로 성능 점수 수집
- **실시간 점수 계산**: 0-100점 + A-F 등급 시스템

### 2. AEO (Answer Engine Optimization)
- **FAQ 자동 생성**: AI가 콘텐츠 분석 후 관련 FAQ 생성
- **Featured Snippet 최적화**: 스니펫 노출을 위한 구조화 가이드
- **Schema.org 마크업**: FAQ, HowTo, Article 등 자동 생성
- **AI 검색 엔진 대응**: ChatGPT, Perplexity, Gemini 최적화

### 3. 벌크 최적화
- **일괄 분석**: 전체 포스트/페이지 한 번에 분석
- **필터링**: 점수별, 상태별, 카테고리별 필터
- **빠른 편집**: 테이블에서 바로 메타 태그 수정
- **진행률 표시**: 실시간 최적화 진행 상황

### 4. AI 콘텐츠 지원
- **타이틀 제안**: 클릭률 높은 제목 AI 생성
- **메타 설명 생성**: SEO 최적화된 설명문 자동 작성
- **키워드 분석**: 포커스 키워드 밀도 및 배치 분석
- **경쟁사 비교**: SERP 상위 결과와 비교 분석

### 5. 스키마 & 사이트맵
- **자동 스키마**: Article, Product, LocalBusiness 등
- **XML 사이트맵**: 자동 생성 및 검색엔진 제출
- **Open Graph**: 소셜 미디어 최적화 메타 태그
- **Twitter Cards**: 트위터 공유 최적화

## 설치 방법

1. WordPress 관리자 > 플러그인 > 새로 추가
2. `wp-1-click-seo-v1.0.0.zip` 업로드
3. 플러그인 활성화
4. SEO > 설정에서 API 키 입력 (선택사항)

## 시스템 요구사항

- WordPress 5.8+
- PHP 7.4+
- MySQL 5.7+ / MariaDB 10.3+

## 파일 구조

```
wp-bulk-seo/
├── wp-bulk-seo.php              # 메인 플러그인 파일
├── admin/
│   ├── class-admin.php          # 관리자 클래스
│   └── views/
│       ├── dashboard.php        # 대시보드 뷰
│       ├── analyzer.php         # 분석기 뷰
│       ├── optimizer.php        # 벌크 최적화 뷰
│       ├── aeo.php              # AEO 뷰
│       └── settings.php         # 설정 뷰
├── includes/
│   ├── algorithm/
│   │   ├── class-ranking-factors-db.php   # 랭킹 요소 DB
│   │   ├── class-seo-analyzer.php         # SEO 분석기
│   │   └── class-seo-scorer.php           # 점수 계산기
│   ├── aeo/
│   │   ├── class-aeo-engine.php           # AEO 엔진
│   │   ├── class-faq-generator.php        # FAQ 생성기
│   │   └── class-featured-snippet-optimizer.php
│   ├── api/
│   │   ├── class-rest-controller.php      # REST API
│   │   └── class-ai-provider.php          # AI 연동
│   ├── class-database.php       # DB 관리
│   ├── class-frontend.php       # 프론트엔드 출력
│   ├── class-schema-generator.php  # 스키마 생성
│   └── class-sitemap-manager.php   # 사이트맵 관리
└── assets/
    ├── css/admin.css            # 관리자 스타일
    └── js/admin.js              # 관리자 스크립트
```

## REST API 엔드포인트

| 엔드포인트 | 메서드 | 설명 |
|-----------|--------|------|
| `/wp-json/wp-bulk-seo/v1/analyze/{id}` | POST | 개별 포스트 분석 |
| `/wp-json/wp-bulk-seo/v1/scores` | GET | 전체 점수 목록 |
| `/wp-json/wp-bulk-seo/v1/bulk-analyze` | POST | 벌크 분석 |
| `/wp-json/wp-bulk-seo/v1/generate-faq` | POST | FAQ 생성 |
| `/wp-json/wp-bulk-seo/v1/settings` | GET/POST | 설정 조회/저장 |

## 설정 옵션

```php
// 기본 설정
'enable_auto_analysis' => true,      // 자동 분석 활성화
'analysis_on_save' => true,          // 저장 시 분석
'show_metabox' => true,              // 메타박스 표시
'default_schema_type' => 'Article',  // 기본 스키마 타입

// AI 설정
'ai_provider' => 'openai',           // openai | anthropic
'openai_api_key' => '',
'anthropic_api_key' => '',

// AEO 설정
'enable_faq_schema' => true,
'auto_generate_faq' => false,
'featured_snippet_hints' => true,
```

## 라이선스

GPL-2.0+

## 지원

- 웹사이트: https://3jlabs.com
- 이메일: support@3jlabs.com
- GitHub: https://github.com/3jlabs/wp-1-click-seo

## 버전 히스토리

### v1.0.0 (2026-01-04)
- 초기 릴리스
- SEO 분석 엔진 (200+ 요소)
- AEO 기능 (FAQ, Featured Snippet)
- AI 콘텐츠 생성 (OpenAI, Anthropic)
- 벌크 최적화
- REST API

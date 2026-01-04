# WP 1-Click SEO 개발 메모리

**작성일**: 2026-01-04
**프로젝트**: 3J SEO 에코시스템
**상태**: v1.0.0 완료

---

## 프로젝트 개요

Alli SEO를 벤치마크하여 WordPress 니치 마켓을 타겟으로 한 올인원 SEO 솔루션 개발. Google Algorithm Leak (2024.05) 분석 기반으로 200개 이상 랭킹 요소를 구현.

### 시장 전략
1. **1차 타겟**: WordPress 마켓 집중 공략
2. **확장 계획**: Webflow → Framer → Ghost → 아임웹 → 카페24

---

## 에코시스템 구성 (3개 컴포넌트)

### 1. WP 1-Click SEO (클라이언트 플러그인)
**경로**: `SEO/wp-bulk-seo/`
**배포파일**: `dist/wp-1-click-seo-v1.0.0.zip` (112KB)

#### 핵심 기능
- SEO 분석 엔진 (200+ 랭킹 요소)
- AEO (Answer Engine Optimization)
- AI 콘텐츠 생성 (OpenAI/Anthropic)
- 벌크 최적화
- 스키마 & 사이트맵 자동 생성

#### 주요 파일
```
wp-bulk-seo/
├── wp-bulk-seo.php                 # 메인 플러그인
├── includes/algorithm/             # SEO 분석 알고리즘
│   ├── class-ranking-factors-db.php
│   ├── class-seo-analyzer.php
│   └── class-seo-scorer.php
├── includes/aeo/                   # AEO 기능
│   ├── class-aeo-engine.php
│   ├── class-faq-generator.php
│   └── class-featured-snippet-optimizer.php
├── includes/api/                   # REST API
│   ├── class-rest-controller.php
│   └── class-ai-provider.php
└── admin/views/                    # 관리자 뷰
```

---

### 2. WP 1-Click SEO Master (서버 플러그인)
**경로**: `SEO/wp-bulk-seo-master/`
**배포파일**: `dist/wp-1-click-seo-master-v1.0.0.zip` (44KB)

#### 핵심 기능
- **라이선스 관리**: 4단계 (Starter/Professional/Agency/Enterprise)
- **원격 사이트 관리**: 플랫폼 자동 감지
- **순위 추적**: 1일 2~12회 모니터링
- **Universal Snippet**: GTM 스타일 한 줄 설치

#### 라이선스 타입
| 타입 | 최대 사이트 |
|------|------------|
| Starter | 1 |
| Professional | 5 |
| Agency | 25 |
| Enterprise | 무제한 |

#### 주요 파일
```
wp-bulk-seo-master/
├── wp-bulk-seo-master.php          # 메인 플러그인
├── includes/
│   ├── class-license-manager.php   # 라이선스 CRUD
│   ├── class-rank-tracker.php      # 순위 추적 엔진
│   ├── class-remote-site-manager.php
│   └── class-universal-snippet.php # 비WP용 스니펫
└── assets/js/seo-snippet.min.js    # Universal Snippet (minified)
```

#### Universal Snippet 사용법
```html
<script src="https://master-site.com/seo-snippet.min.js" 
        data-site-key="YOUR_SITE_KEY"></script>
```

---

### 3. Chrome Extension
**경로**: `SEO/chrome-extension/`
**배포파일**: `dist/wp-1-click-seo-chrome-extension-v1.0.0.zip` (22KB)

#### 핵심 기능
- SERP 실시간 분석 (오버레이)
- WordPress 사이트 연동
- 자동 순위 추적
- 경쟁사 분석

#### Manifest V3 구조
```
chrome-extension/
├── manifest.json           # Manifest V3
├── js/
│   ├── background.js       # Service Worker
│   └── serp-analyzer.js    # Content Script
├── popup/                  # 팝업 UI
└── options/                # 설정 페이지
```

---

## 기술 스택

### 백엔드
- PHP 7.4+
- WordPress REST API
- MySQL/MariaDB

### 프론트엔드
- Vanilla JavaScript (jQuery 호환)
- CSS3 (CSS Variables)
- Chart.js (차트)

### AI 통합
- OpenAI API (GPT-4)
- Anthropic API (Claude)

### 외부 API
- Google PageSpeed Insights API v5
- Google Search Console API
- SERP API (순위 추적)

---

## 데이터베이스 스키마 (Master)

```sql
-- 라이선스 테이블
seo_master_licenses (
    id, license_key, type, email, 
    max_sites, status, expires_at
)

-- 활성화 기록
seo_master_activations (
    id, license_id, site_url, activated_at
)

-- 원격 사이트
seo_master_remote_sites (
    id, license_id, url, site_type, 
    site_key, seo_score, last_sync
)

-- 키워드
seo_master_keywords (
    id, site_id, keyword, country
)

-- 순위 히스토리
seo_master_rank_history (
    id, keyword_id, position, url, checked_at
)
```

---

## REST API 엔드포인트

### 클라이언트 플러그인
| 엔드포인트 | 설명 |
|-----------|------|
| `POST /wp-bulk-seo/v1/analyze/{id}` | 포스트 분석 |
| `GET /wp-bulk-seo/v1/scores` | 점수 목록 |
| `POST /wp-bulk-seo/v1/generate-faq` | FAQ 생성 |

### 마스터 플러그인
| 엔드포인트 | 설명 |
|-----------|------|
| `POST /seo-master/v1/license/validate` | 라이선스 검증 |
| `POST /seo-master/v1/license/activate` | 활성화 |
| `POST /seo-master/v1/site/report` | SEO 데이터 리포트 |
| `POST /seo-master/v1/ranks/check` | 순위 체크 |

---

## 배포 파일 목록

| 파일명 | 크기 | 설명 |
|--------|------|------|
| `wp-1-click-seo-v1.0.0.zip` | 112KB | 클라이언트 플러그인 |
| `wp-1-click-seo-master-v1.0.0.zip` | 44KB | 마스터 플러그인 |
| `wp-1-click-seo-chrome-extension-v1.0.0.zip` | 22KB | Chrome 확장 |

---

## 향후 개발 계획

### v1.1.0 예정
- [ ] Webflow 공식 연동
- [ ] Framer 플러그인 지원
- [ ] Ghost 테마 통합

### v1.2.0 예정
- [ ] 아임웹 연동
- [ ] 카페24 앱 개발
- [ ] 다국어 지원 (EN, JP)

### v2.0.0 예정
- [ ] AI 자동 최적화 (Auto-Pilot)
- [ ] A/B 테스트 기능
- [ ] 백링크 분석

---

## 관련 문서

- `memory & context/20260104-Google-Ranking-Factors-Database.md` - 랭킹 요소 DB
- `memory & context/20260104-3J-SEO-Algorithm-Design.md` - 알고리즘 설계
- `memory & context/20260104-SEO-Plugins-Comprehensive-Benchmarking.md` - 경쟁사 분석

---

## 참고 자료

- Google Algorithm Leak (2024.05): NavBoost, siteAuthority 등 핵심 시그널
- Core Web Vitals: LCP, FID, CLS, FCP, TTFB, TBT
- AEO (Answer Engine Optimization): AI 검색 엔진 최적화

---

## 메모

- PowerShell `Compress-Archive` 명령으로 압축 (zip 명령 미지원 환경)
- 플러그인 이름을 `wp-1-click-seo`로 통일하여 기존 `wp-bulk-seo-aeo`와 구분
- Universal Snippet은 GTM처럼 한 줄 코드로 비WP 사이트 지원

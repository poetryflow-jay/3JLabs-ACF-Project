# 3J Labs ACF CSS 플러그인 패밀리 - 릴리즈 노트

## 릴리즈 개요

**릴리즈 날짜**: 2026년 1월 7일
**릴리즈 버전**: Phase 51 - Visual Command Center 섹션 전환 핫픽스
**개발팀**: 3J Labs (제이x제니x제이슨 연구소) - Mikael(Algorithm) + Jason(Implementation) + Jenny(UX)

---

## 🔧 Phase 51 (v26.0.10) - Visual Command Center 섹션 전환 핫픽스 (2026-01-07)

### 작업 요약

Visual Command Center에서 섹션 전환(네비게이션 클릭)이 작동하지 않는 문제를 수정했습니다. 탭 시스템은 이미 수정되었지만, 섹션 전환 로직이 누락되어 있었습니다.

### 문제 상황

- **증상**: 네비게이션 메뉴(Colors, Typography, Buttons 등) 클릭 시 섹션이 전환되지 않음
- **영향 범위**: 모든 메인 섹션 전환
- **근본 원인**: CSS `!important` 규칙이 JavaScript 인라인 스타일(`style.display`)을 오버라이드
- **이전 수정**: 탭 시스템은 v26.0.9에서 수정되었으나, 섹션 전환 로직은 누락됨

### 수정 내용

**문제 코드**:
```javascript
sec.style.display = 'block';  // CSS !important에 의해 무시됨
targetSection.style.display = 'block';  // CSS !important에 의해 무시됨
```

**수정 코드**:
```javascript
// 섹션 표시
sec.style.setProperty('display', 'block', 'important');
sec.style.setProperty('opacity', '1', 'important');
sec.style.setProperty('visibility', 'visible', 'important');

// 섹션 숨기기
sec.style.setProperty('display', 'none', 'important');
```

### 버전 업데이트

| 플러그인 | 이전 버전 | 새 버전 |
|---------|---------|---------|
| ACF CSS Manager | 26.0.9 | **26.0.10** |

### 수정된 파일

1. **includes/class-jj-simple-style-guide.php**
   - 섹션 초기화 로직: `setProperty`로 `!important` 인라인 스타일 적용
   - 섹션 전환 핸들러: 동일하게 수정
   - 버전 로그 업데이트: v26.0.10

2. **acf-css-really-simple-style-guide.php**
   - 버전 번호 업데이트: 26.0.9 → 26.0.10

### 교훈

탭 시스템뿐만 아니라 섹션 전환 로직도 동일한 문제가 있었습니다. CSS에 `!important`가 있는 경우, 모든 JavaScript 스타일 조작은 `setProperty()` 메서드를 사용해야 합니다.

---

## 🔧 Phase 51 (v26.0.9) - Visual Command Center 탭 시스템 핫픽스 (2026-01-07)

### 작업 요약

Visual Command Center(스타일 센터)에서 탭 콘텐츠가 표시되지 않는 치명적 버그를 수정했습니다.

### 문제 상황

- **증상**: 탭 버튼(브랜드 팔레트, 시스템 팔레트 등)은 보이지만, 클릭해도 콘텐츠가 표시되지 않음
- **영향 범위**: Colors 섹션 5개 탭, Buttons 섹션 3개 탭 모두 영향
- **근본 원인**: CSS `!important` 규칙이 JavaScript 인라인 스타일을 오버라이드

### 수정 과정

| 버전 | 시도 | 결과 |
|------|------|------|
| v26.0.8 | `style.display = 'block'` 직접 설정 | 실패 - CSS !important가 우선 |
| v26.0.9 | `style.setProperty('display', 'block', 'important')` | 성공 |

### 기술적 해결책

**문제 코드**:
```javascript
content.style.display = 'block';  // CSS !important에 의해 무시됨
```

**수정 코드**:
```javascript
content.style.setProperty('display', 'block', 'important');
content.style.setProperty('opacity', '1', 'important');
content.style.setProperty('visibility', 'visible', 'important');
content.style.setProperty('height', 'auto', 'important');
```

### 추가 수정사항

- **넛지/툴팁 위치 조정**: Visual Command Center에서 bottom-right, top-right 위치 여백 40px로 조정
- **콘솔 로그 개선**: 탭 초기화 및 활성화 상태 추적 로그 추가

### 버전 업데이트

| 플러그인 | 이전 버전 | 새 버전 |
|---------|---------|---------|
| ACF CSS Manager | 26.0.7 | **26.0.9** |

### 수정된 파일

1. **includes/class-jj-simple-style-guide.php**
   - 탭 초기화 로직: `setProperty`로 `!important` 인라인 스타일 적용
   - 탭 클릭 핸들러: 동일하게 수정
   - 버전 로그 업데이트: v26.0.9

2. **assets/css/jj-nudge-system.css**
   - Visual Command Center 호환성 CSS 추가
   - 넛지 컨테이너 위치 조정

3. **acf-css-really-simple-style-guide.php**
   - 버전 번호 업데이트: 26.0.7 → 26.0.9

### 교훈

CSS `!important` 규칙은 일반 인라인 스타일(`element.style.property`)보다 우선순위가 높습니다. JavaScript에서 `!important`를 오버라이드하려면 반드시 `setProperty()` 메서드의 세 번째 인자에 `'important'`를 전달해야 합니다.

```
우선순위 (높음 → 낮음):
1. !important 인라인 스타일 (setProperty)
2. !important CSS 규칙
3. 일반 인라인 스타일
4. 일반 CSS 규칙
```

---

## 🎛️ Phase 50B - 통합 대시보드 & 멀티사이트 허브 (2026-01-06)

### 작업 요약

Phase 50A의 크로스 플러그인 인프라를 기반으로, 14개 플러그인을 한 곳에서 관리하는 Command Center와 멀티사이트 환경을 위한 중앙 관리 허브를 구축했습니다.

### P50-3: 통합 대시보드 Command Center

**새 파일들**:
- `jj-analytics-dashboard/includes/class-unified-dashboard.php` (~1,000줄)
- `jj-analytics-dashboard/assets/css/unified-dashboard.css` (~500줄)
- `jj-analytics-dashboard/assets/js/unified-dashboard.js` (~450줄)

**주요 기능**:
- **통합 플러그인 관리**: 14개 3J Labs 플러그인 상태 한눈에 확인
- **시스템 헬스 체크**: PHP, WordPress, 메모리, DB 상태 모니터링
- **실시간 이벤트 로그**: Phase 50A Event Bus와 연동한 활동 추적
- **빠른 작업**: 업데이트 확인, 캐시 초기화, 설정 내보내기
- **WordPress 위젯**: 대시보드 위젯으로 핵심 지표 표시
- **Chart.js 통합**: 플러그인 분포 시각화

**관리자 메뉴 구조**:
```
3J Labs (메인 메뉴)
├── Command Center (개요)
├── 플러그인 관리 (상세 목록)
├── 시스템 상태 (헬스 체크)
├── 이벤트 로그 (활동 기록)
└── 설정 (통합 설정)
```

**AJAX 핸들러**:
- `3j_unified_get_overview` - 전체 개요 데이터
- `3j_unified_get_health` - 시스템 상태
- `3j_unified_quick_action` - 빠른 작업 실행

### P50-4: 멀티사이트 중앙관리 허브

**새 파일들**:
- `acf-css-neural-link/includes/class-jj-multisite-hub.php` (~1,100줄)
- `acf-css-neural-link/assets/css/multisite-hub.css` (~600줄)
- `acf-css-neural-link/assets/js/multisite-hub.js` (~350줄)

**주요 기능**:
- **사이트 관리**: 네트워크 내 모든 사이트의 플러그인 상태 확인
- **일괄 동기화**: 선택한 플러그인을 여러 사이트에 동시 배포
- **네트워크 상태**: 전체 네트워크 헬스 체크 대시보드
- **자동 동기화**: 새 사이트 생성 시 자동 플러그인 설치
- **진행률 표시**: 실시간 동기화 진행 상황 및 로그

**네트워크 관리자 메뉴**:
```
3J Multisite Hub
├── 사이트 관리 (메인 대시보드)
├── 플러그인 동기화 (일괄 배포)
├── 네트워크 상태 (헬스 체크)
└── 설정 (자동 동기화 설정)
```

**REST API 엔드포인트**:
- `GET /wp-json/3j-multisite/v1/sites` - 사이트 목록
- `GET /wp-json/3j-multisite/v1/sites/{id}` - 사이트 상세
- `POST /wp-json/3j-multisite/v1/sync` - 플러그인 동기화
- `GET /wp-json/3j-multisite/v1/stats` - 네트워크 통계

### 버전 업데이트

| 플러그인 | 이전 버전 | 새 버전 |
|---------|---------|---------|
| JJ Analytics Dashboard | 1.1.0 | 1.2.0 |
| 3J Neural Link | 8.1.0 | 8.2.0 |

---

## 🔗 Phase 50A - 크로스 플러그인 인프라 (2026-01-06)

### 작업 요약

모든 3J Labs 플러그인이 공유하는 통합 REST API, 플러그인 레지스트리, 이벤트 버스 시스템을 구축했습니다.

### P50-1: 통합 REST API 표준화

**새 파일**: `shared-ui-assets/php/class-3j-rest-api.php` (~850줄)

**REST 엔드포인트**:
- `GET /wp-json/3j-labs/v1/plugins` - 플러그인 목록
- `GET /wp-json/3j-labs/v1/health` - 시스템 상태
- `GET /wp-json/3j-labs/v1/settings` - 통합 설정
- `GET /wp-json/3j-labs/v1/analytics` - 크로스 플러그인 분석
- `POST /wp-json/3j-labs/v1/events` - 이벤트 기록

### P50-2: 크로스 플러그인 연동 시스템

**새 파일들**:
- `shared-ui-assets/php/class-3j-plugin-registry.php` (~650줄)
- `shared-ui-assets/php/class-3j-event-bus.php` (~700줄)

**주요 기능**:
- 플러그인 등록/상태 관리/의존성 검증
- Pub/Sub 이벤트 발행/구독
- 편의 함수: `jj_registry()`, `jj_plugin_active()`, `jj_publish()`, `jj_subscribe()`

**Shared Loader 업데이트**: v1.0.0 → v1.1.0

---

## 🚀 Phase 49 - AI/자동화/분석 기능 대폭 강화 (2026-01-06)

### 작업 요약

5개의 주요 플러그인에 AI 기반 기능, 자동화 도구, 실시간 분석 기능을 추가하여 사용자 경험을 대폭 향상시켰습니다.

### P49-1: ACF CSS AI Extension - AI 컬러 팔레트 추천 (v3.4.0)

**새 파일**: `includes/class-ai-color-palette.php` (~600줄)

**주요 기능**:
- AI 기반 브랜드 컬러 분석 및 자동 팔레트 생성
- 60-30-10 색상 비율 자동 적용 (주색 60%, 보조색 30%, 강조색 10%)
- 접근성 대비율 검사 (WCAG AA 4.5:1, AAA 7:1)
- 무드보드 스타일 추천 (modern/classic/minimal/bold)
- 산업별 최적화 팔레트 제안
- 보색/유사색/삼색조 자동 계산
- AJAX 기반 실시간 팔레트 생성
- REST API 엔드포인트: `/acf-ai/v1/palette`

**기술 세부사항**:
```php
// 색상 조화 알고리즘
- complementary (보색)
- analogous (유사색)
- triadic (삼색조)
- split_complementary (분리보색)
- tetradic (사색조)
```

### P49-2: ACF Mail SMTP - 비주얼 이메일 템플릿 빌더 (v2.3.0)

**새 파일들**:
- `includes/class-template-builder.php` (~700줄)
- `assets/css/template-builder.css` (~400줄)
- `assets/js/template-builder.js` (~500줄)

**주요 기능**:
- 드래그 앤 드롭 템플릿 빌더
- 12+ 블록 타입 지원:
  - text (텍스트)
  - image (이미지)
  - button (버튼)
  - spacer (여백)
  - divider (구분선)
  - columns (2열/3열 레이아웃)
  - social (소셜 아이콘)
  - header (헤더)
  - footer (푸터)
  - logo (로고)
  - video (비디오 링크)
  - html (커스텀 HTML)
- 실시간 미리보기
- 반응형 이메일 출력
- 템플릿 저장 및 복제
- JSON 기반 템플릿 구조

### P49-3: JJ Analytics Dashboard - 실시간 대시보드 위젯 (v1.1.0)

**새 파일들**:
- `includes/class-realtime-dashboard.php` (기존 확장)
- `assets/js/realtime-dashboard.js` (~450줄)

**주요 기능**:
- 실시간 데이터 모니터링 (AJAX 폴링)
- 새로고침 간격 설정 (5초/15초/30초/1분/5분)
- 자동 갱신 타이머 표시 (카운트다운)
- 실시간 플러그인 상태 추적
- 최근 활동 피드 (10개 항목)
- Chart.js 기반 실시간 차트
- 성능 지표 실시간 업데이트
- REST API 엔드포인트

**UI 컴포넌트**:
- 개요 카드 (총 플러그인, 활성화, 업데이트 필요)
- 플러그인 상태 목록
- 최근 활동 타임라인
- 성능 차트

### P49-4: ACF CSS WooCommerce Toolkit - 동적 가격 표시 (v2.5.0)

**새 파일들**:
- `includes/class-dynamic-price-display.php` (~850줄)
- `assets/css/dynamic-price.css` (~300줄)
- `assets/js/dynamic-price.js` (~280줄)

**주요 기능**:
- **세일 카운트다운 타이머**: 할인 종료까지 남은 시간 표시
- **재고 긴급도 표시**: 재고량에 따른 긴급도 배지 (critical/high/medium)
- **대량 구매 가격표**: 수량별 할인율 테이블 자동 생성
- **가격 히스토리 추적**: 최근 30일 가격 변동 기록
- **사용자별 맞춤 가격**: 로그인 사용자/역할별 가격 표시
- REST API 지원: `/acf-wc/v1/dynamic-price/{id}`
- AJAX 실시간 가격 업데이트
- 상품 편집 페이지 메타박스

**설정 옵션**:
```php
- enable_countdown: 카운트다운 활성화
- enable_stock_urgency: 긴급도 표시
- enable_bulk_pricing: 대량 구매 가격
- enable_user_pricing: 사용자별 가격
- enable_price_history: 가격 히스토리
- countdown_style: modern/minimal
- urgency_threshold: 긴급 재고 임계값
```

### P49-5: ACF Code Snippets Box - 고급 버전 관리 (v5.1.0)

**확장된 파일**: `includes/class-acf-csb-version-history.php` (+470줄)

**새 메서드들**:
```php
// 태깅 시스템
add_version_tag($post_id, $version, $tag_name, $tag_color)
remove_version_tag($post_id, $version, $tag_name)
find_tagged_version($post_id, $tag_name)

// 브랜치 관리
create_branch($post_id, $version, $branch_name)
merge_branch($branch_id, $target_id, $merge_mode)
get_current_version($post_id)

// 스냅샷 & 백업
create_snapshot($post_id, $description)
restore_snapshot($post_id, $snapshot_id)
schedule_auto_backup($post_id, $interval)

// 통계 & 내보내기
get_version_stats($post_id)
export_versions($post_id, $options)
import_versions($post_id, $json_data)
```

**기능 설명**:
- **버전 태깅**: 버전에 컬러 태그 추가 (production, staging, hotfix 등)
- **브랜치 생성**: 특정 버전에서 새 브랜치 분기
- **브랜치 병합**: replace/append 모드로 브랜치 병합
- **스냅샷**: 현재 상태의 수동 백업 생성
- **자동 백업**: hourly/daily/weekly 스케줄 설정
- **버전 통계**: 총 버전 수, 태그 수, 브랜치 수 등
- **내보내기/가져오기**: JSON 형식으로 버전 히스토리 이동

### 버전 업데이트 요약

| 플러그인 | 이전 버전 | 새 버전 | 주요 변경 |
|----------|-----------|---------|-----------|
| ACF CSS AI Extension | 3.3.3 | **3.4.0** | AI 컬러 팔레트 추천 |
| ACF Mail SMTP | 2.2.0 | **2.3.0** | 비주얼 템플릿 빌더 |
| JJ Analytics Dashboard | 1.0.3 | **1.1.0** | 실시간 대시보드 위젯 |
| ACF CSS WooCommerce Toolkit | 2.4.3 | **2.5.0** | 동적 가격 표시 |
| ACF Code Snippets Box | 4.0.2 | **5.1.0** | 고급 버전 관리 |

### 수정된 파일 목록

1. **ACF CSS AI Extension**
   - `acf-css-ai-extension.php` (버전 업데이트)
   - `includes/class-ai-color-palette.php` (신규)

2. **ACF Mail SMTP**
   - `acf-mail-smtp.php` (버전 업데이트, 클래스 로드)
   - `includes/class-template-builder.php` (신규)
   - `assets/css/template-builder.css` (신규)
   - `assets/js/template-builder.js` (신규)

3. **JJ Analytics Dashboard**
   - `jj-analytics-dashboard.php` (버전 업데이트, 탭 추가)
   - `assets/js/realtime-dashboard.js` (신규)

4. **ACF CSS WooCommerce Toolkit**
   - `acf-css-woocommerce-toolkit.php` (버전 업데이트, 클래스 로드)
   - `includes/class-dynamic-price-display.php` (신규)
   - `assets/css/dynamic-price.css` (신규)
   - `assets/js/dynamic-price.js` (신규)

5. **ACF Code Snippets Box**
   - `acf-code-snippets-box.php` (버전 업데이트)
   - `includes/class-acf-csb-version-history.php` (확장)

### PHP 검증

모든 새 파일 문법 검사 통과:
```bash
✅ class-ai-color-palette.php - No syntax errors
✅ class-template-builder.php - No syntax errors
✅ class-dynamic-price-display.php - No syntax errors
✅ class-acf-csb-version-history.php - No syntax errors
```

---

# CSS 최적화로 PageSpeed 점수 올리기: Tree Shaking 실전 가이드

**카테고리:** 성능 최적화  
**태그:** PageSpeed, CSS, 성능, Tree Shaking  
**읽기 시간:** 6분

---

## 🚀 요약

- 불필요한 CSS는 페이지 로딩 속도를 저하시킴
- CSS Tree Shaking으로 사용하지 않는 규칙 자동 제거
- PageSpeed Insights 점수 10-20점 향상 가능
- ACF CSS Manager PRO에서 원클릭 활성화

---

## 문제: 비대해진 CSS

WordPress 사이트의 평균 CSS 크기는 **300KB 이상**입니다.

### 어디서 오는 CSS인가?

```
테마 기본 CSS: 150KB
페이지 빌더: 100KB+
각종 플러그인: 50KB+
커스텀 스타일: 50KB+
--------------------------------
총합: 350KB+ (압축 전)
```

**문제는?** 이 중 실제로 사용되는 CSS는 **20-30%**에 불과합니다.

---

## CSS가 성능에 미치는 영향

### Render-Blocking Resource

CSS는 렌더링 차단 리소스입니다.

```
HTML 파싱 시작
    ↓
CSS 발견 → 다운로드 시작
    ↓
CSS 다운로드 완료 (대기)
    ↓
CSSOM 생성
    ↓
렌더링 시작
```

CSS가 클수록 "대기" 시간이 길어집니다.

### Core Web Vitals 영향

- **LCP (Largest Contentful Paint)**: CSS 로딩 지연 → LCP 지연
- **CLS (Cumulative Layout Shift)**: 늦게 로드되는 CSS → 레이아웃 변경
- **FID (First Input Delay)**: 메인 스레드 차단 → 상호작용 지연

---

## 해결책: CSS Tree Shaking

### Tree Shaking이란?

사용하지 않는 코드를 "흔들어서 떨어뜨리는" 기술입니다.

```
Before (350KB):
├── 사용되는 규칙 (70KB)
├── 미사용 규칙 (200KB) ← 제거 대상
└── 중복 규칙 (80KB) ← 제거 대상

After (70KB):
└── 사용되는 규칙만
```

### ACF CSS Manager의 Tree Shaking

1. **분석 단계**: 페이지에서 실제 사용되는 CSS 선택자 탐지
2. **제거 단계**: 미사용 규칙 제거
3. **최적화 단계**: 중복 제거, 병합, Minify

---

## 📊 실제 결과

### 테스트 환경
- 테마: flavor flavor
- 페이지 빌더: Elementor Pro
- 플러그인: WooCommerce, Jetpack 등 15개

### Before
```
CSS 크기: 420KB
PageSpeed Mobile: 52점
LCP: 4.2초
```

### After (Tree Shaking 적용)
```
CSS 크기: 85KB (-80%)
PageSpeed Mobile: 78점 (+26)
LCP: 2.1초 (-2.1초)
```

---

## 🛠️ 설정 방법

### Step 1: Tree Shaking 활성화

```
ACF CSS Manager → 설정 → 성능
→ "CSS Tree Shaking 활성화" 체크
→ 저장
```

### Step 2: 동작 확인

개발자 도구에서 생성된 CSS 확인:
```html
<style id="jj-style-guide-css">
/* Optimized by ACF CSS Manager */
/* Original: 420KB → Optimized: 85KB */
</style>
```

### Step 3: 캐시 삭제

캐싱 플러그인 캐시를 삭제하여 최적화된 CSS 적용

---

## ⚠️ 주의사항

### 동적 콘텐츠

JavaScript로 추가되는 요소의 스타일이 제거될 수 있습니다.

**해결책:**
```php
// Safelist에 선택자 추가
add_filter( 'jj_css_tree_shake_safelist', function( $safelist ) {
    $safelist[] = '.dynamic-popup';
    $safelist[] = '.modal-overlay';
    return $safelist;
} );
```

### 페이지별 다른 스타일

각 페이지에서 사용되는 CSS가 다를 수 있습니다.

**해결책:**
- 페이지별 분석 모드 사용 (Premium 기능)
- 또는 공통 요소는 Safelist에 추가

---

## 추가 최적화 팁

### 1. CSS Minify
공백, 주석 제거로 추가 10-15% 절감

```
ACF CSS Manager → 설정 → 성능
→ "CSS Minify 활성화" 체크
```

### 2. 인라인 Critical CSS
Above-the-fold CSS만 인라인으로 삽입

### 3. 지연 로딩
나머지 CSS는 비동기 로드

---

## 📈 PageSpeed 점수 목표

| 점수 | 등급 | 목표 |
|------|------|------|
| 90-100 | Good | 최적 |
| 50-89 | Needs Improvement | 수용 가능 |
| 0-49 | Poor | 개선 필요 |

**현실적 목표:**
- Mobile: 70점 이상
- Desktop: 85점 이상

---

## 체크리스트

- [ ] Tree Shaking 활성화
- [ ] Minify 활성화
- [ ] 캐싱 플러그인 설정
- [ ] CDN 사용
- [ ] 폰트 최적화 (display: swap)
- [ ] 이미지 최적화
- [ ] Lazy Loading

---

## 다음 단계

CSS 최적화 후에도 점수가 낮다면 이미지, JavaScript를 최적화하세요.

**관련 글:** [WordPress 이미지 최적화 완벽 가이드](#)

---

*Tree Shaking 적용 후 결과를 댓글로 공유해주세요! Before/After 비교를 환영합니다.*


# 3J Labs ACF CSS 프로젝트 세션 메모리
> 작성일: 2026-01-07
> 세션: Phase 51 - Visual Command Center 탭 시스템 디버깅 및 수정

---

## 1. 세션 개요

### 1.1 작업 목표
ACF CSS 스타일 센터(Visual Command Center)의 탭 콘텐츠가 표시되지 않는 치명적 버그 수정

### 1.2 시작 버전
- ACF CSS Manager: v26.0.7

### 1.3 최종 버전
- ACF CSS Manager: v26.0.9

---

## 2. 문제 상황 분석

### 2.1 증상
1. **탭 버튼은 보임**: "1. 브랜드 팔레트", "2. 시스템 팔레트", "3. 얼터너티브 팔레트" 등
2. **탭 콘텐츠는 안 보임**: 버튼 클릭해도 아래 내용이 완전히 빈 상태
3. **섹션 전환은 정상**: 사이드바에서 Colors → Typography → Buttons 전환은 작동
4. **넛지/툴팁 위치 이상**: 화면 하단 구석에 몰려있음
5. **저장/프리셋 적용 안됨**: AJAX 요청은 200 OK지만 실제 저장 안됨

### 2.2 디버깅 과정

#### 시도 1: CSS 규칙 추가 (v26.0.6)
```css
.jj-tabs-container .jj-tab-content {
    display: none !important;
}
.jj-tabs-container .jj-tab-content.is-active {
    display: block !important;
}
```
**결과**: 실패 - CSS 클래스만으로는 작동 안 함

#### 시도 2: JavaScript에서 style.display 직접 설정 (v26.0.8)
```javascript
content.style.display = 'block';
content.style.opacity = '1';
content.style.visibility = 'visible';
```
**결과**: 실패 - CSS의 `!important`가 인라인 스타일을 오버라이드

#### 시도 3: setProperty로 !important 강제 (v26.0.9)
```javascript
content.style.setProperty('display', 'block', 'important');
content.style.setProperty('opacity', '1', 'important');
content.style.setProperty('visibility', 'visible', 'important');
content.style.setProperty('height', 'auto', 'important');
```
**결과**: 테스트 중 (v26.0.9)

### 2.3 근본 원인

**CSS 우선순위 충돌**:
1. CSS에 `display: none !important` 규칙이 있음
2. JavaScript에서 `element.style.display = 'block'`은 일반 인라인 스타일
3. CSS의 `!important`가 일반 인라인 스타일보다 우선순위 높음
4. 따라서 JavaScript가 display를 설정해도 CSS가 오버라이드

**해결책**:
- `element.style.setProperty('display', 'block', 'important')` 사용
- 이렇게 하면 인라인 스타일에도 `!important`가 붙어서 CSS를 오버라이드

---

## 3. 코드 변경 내역

### 3.1 v26.0.8 변경사항

#### 파일: `includes/class-jj-simple-style-guide.php`
- 탭 초기화 시 `style.display = 'block'` 직접 설정
- 탭 클릭 핸들러에서도 동일하게 설정

#### 파일: `assets/css/jj-nudge-system.css`
- Visual Command Center 호환성 CSS 추가
- bottom-right, top-right 넛지 위치 조정 (40px 여백)
- Toast 알림 위치 조정

### 3.2 v26.0.9 변경사항 (핵심 수정)

#### 파일: `includes/class-jj-simple-style-guide.php`

**초기화 코드 (lines ~624-637)**:
```javascript
// [v26.0.9] setProperty로 !important 오버라이드
subContents.forEach(function(content) {
    var contentName = content.getAttribute('data-tab-content');
    if (contentName === activeTabName) {
        content.classList.add('is-active');
        content.style.setProperty('display', 'block', 'important');
        content.style.setProperty('opacity', '1', 'important');
        content.style.setProperty('visibility', 'visible', 'important');
        content.style.setProperty('height', 'auto', 'important');
    } else {
        content.classList.remove('is-active');
        content.style.setProperty('display', 'none', 'important');
    }
});
```

**클릭 핸들러 코드 (lines ~650-666)**:
```javascript
// [v26.0.9] 숨기기
subContents.forEach(function(content) {
    content.classList.remove('is-active');
    content.style.setProperty('display', 'none', 'important');
});

// [v26.0.9] 타겟 표시
if (targetContent) {
    targetContent.classList.add('is-active');
    targetContent.style.setProperty('display', 'block', 'important');
    targetContent.style.setProperty('opacity', '1', 'important');
    targetContent.style.setProperty('visibility', 'visible', 'important');
    targetContent.style.setProperty('height', 'auto', 'important');
}
```

---

## 4. 콘솔 로그 분석

### 4.1 정상 작동하는 부분
```
[JJ Command Center v26.0.8] Initializing...
[JJ Command Center] Found 5 nav items, 5 sections
[JJ Command Center] Found 4 tab containers
[JJ Command Center] Container 0 : 5 tabs, 5 contents  ← Colors 섹션
[JJ Command Center] Container 1 : 3 tabs, 3 contents  ← Buttons 섹션
[JJ Command Center] Container 2 : 0 tabs, 1 contents  ← Forms 섹션 (탭 없음)
[JJ Command Center] Container 3 : 0 tabs, 1 contents  ← Fields 섹션 (탭 없음)
[JJ Command Center] Initial tab shown: brand
[JJ Command Center] Initial tab shown: btn-primary
[JJ Command Center v26.0.8] Initialization complete!
```

### 4.2 탭 클릭 시 (정상 작동하는 부분)
```
[JJ Command Center] Sub-tab clicked: system
[JJ Command Center] Sub-tab content activated: system
```

### 4.3 문제점
- JavaScript에서는 "activated" 되었다고 로그 찍힘
- 실제 화면에는 안 보임
- → CSS `!important`가 인라인 스타일을 오버라이드하는 것이 원인

---

## 5. 파일 구조 정리

### 5.1 탭 시스템 관련 파일들

| 파일 | 역할 |
|------|------|
| `includes/class-jj-simple-style-guide.php` | 메인 렌더링 + 인라인 JS |
| `includes/editor-views/view-section-colors.php` | 팔레트 섹션 (5개 탭) |
| `includes/editor-views/view-section-buttons.php` | 버튼 섹션 (3개 탭) |
| `assets/css/jj-ui-system-2026.css` | 탭 CSS 스타일 |
| `assets/js/jj-style-guide-editor.js` | 에디터 JS (섹션 탭용) |

### 5.2 두 가지 탭 시스템

1. **섹션 탭** (메인 네비게이션)
   - 클래스: `.jj-section-tab-button`, `.jj-section-wrapper`
   - 핸들러: `jj-style-guide-editor.js`의 `initSectionTabs()`
   - 상태: 정상 작동

2. **서브 탭** (섹션 내부)
   - 클래스: `.jj-tab-button`, `.jj-tab-content`
   - 핸들러: `class-jj-simple-style-guide.php`의 인라인 JS
   - 상태: v26.0.9에서 수정

---

## 6. 남은 이슈

### 6.1 확인 필요
- [ ] v26.0.9 탭 콘텐츠 표시 여부 테스트
- [ ] 저장하기 버튼 AJAX 응답 내용 확인
- [ ] 프리셋 적용하기 기능 확인

### 6.2 잠재적 문제
- 저장/프리셋 AJAX 핸들러가 없거나 오류 발생 가능
- `jj_save_style_guide` 액션 핸들러 확인 필요

---

## 7. 기술적 교훈

### 7.1 CSS `!important` vs JavaScript 인라인 스타일

```
우선순위 (높음 → 낮음):
1. !important가 있는 인라인 스타일
2. !important가 있는 CSS 규칙
3. 일반 인라인 스타일
4. 일반 CSS 규칙
```

**잘못된 방법**:
```javascript
element.style.display = 'block';  // CSS !important에 의해 무시됨
```

**올바른 방법**:
```javascript
element.style.setProperty('display', 'block', 'important');
```

### 7.2 디버깅 접근법

1. **콘솔 로그 확인**: JavaScript 실행 여부 파악
2. **Network 탭**: AJAX 요청/응답 확인
3. **Elements 탭**: 실제 적용된 CSS 확인 (Computed Styles)
4. **CSS 우선순위**: `!important` 충돌 확인

---

## 8. 빌드 기록

| 버전 | 시간 | 변경 내용 |
|------|------|-----------|
| v26.0.8 | 15:28 | style.display 직접 설정 시도 |
| v26.0.8 | 15:29 | 넛지 위치 CSS 추가 후 리빌드 |
| v26.0.9 | 15:37 | setProperty로 !important 오버라이드 |

---

## 9. 다음 세션 권장 작업

1. **v26.0.9 테스트 결과 확인**
   - 탭 콘텐츠 표시 여부
   - 모든 5개 팔레트 탭 전환

2. **저장 기능 디버깅**
   - AJAX 응답 본문 확인
   - PHP 핸들러 코드 검토

3. **프리셋 적용 기능 디버깅**
   - 클릭 이벤트 핸들러 확인
   - AJAX 요청/응답 확인

---

*작성: Claude Opus 4 (Sisyphus Mode)*
*최종 업데이트: 2026-01-07 15:37 KST*

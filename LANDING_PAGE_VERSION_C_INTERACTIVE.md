# 3J Labs ACF CSS - 랜딩 페이지 Version C
## 인터랙티브 데모 중심 접근 방식

**작성일**: 2026년 1월 4일
**버전**: C (Interactive Demo-Focused)
**핵심 전략**: "보여주지 말고, 직접 해보게 하라" (Don't Tell, Let Them Try)

---

## 1. Version C 전략 개요

### 1.1 핵심 차별점

| 항목 | Version A | Version B | Version C |
|------|-----------|-----------|-----------|
| 접근 방식 | 기능 나열 | 스토리텔링 | 체험 중심 |
| 사용자 역할 | 수동적 독자 | 공감하는 관객 | 능동적 참여자 |
| 핵심 요소 | 스크린샷 | 감정 곡선 | 라이브 데모 |
| 전환 트리거 | 정보 | 감정 | 경험 |
| 리스크 | 지루함 | 과장된 느낌 | 기술 복잡성 |

### 1.2 "Try Before Buy" 철학

```
전통적 접근:
[광고] → [랜딩 페이지] → [가입] → [다운로드] → [설치] → [체험]
                                      ↑
                              마찰 포인트 (이탈 발생)

Version C 접근:
[랜딩 페이지] → [즉시 체험] → [가입 유도] → [설치]
                    ↑
            가치 먼저 경험 (이탈 감소)
```

### 1.3 인터랙션 원칙

```
1. 제로 마찰 (Zero Friction)
   - 가입 없이 데모 가능
   - 설치 없이 기능 체험
   - 클릭 한 번으로 시작

2. 즉각적 피드백 (Instant Feedback)
   - 모든 액션에 시각적 반응
   - 실시간 결과 미리보기
   - "와!" 순간 연출

3. 점진적 복잡성 (Progressive Complexity)
   - 쉬운 것부터 시작
   - 성공 경험 축적
   - 고급 기능으로 유도
```

---

## 2. 페이지 구조 상세

### Section 1: 인터랙티브 Hero

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                                                                 │
│            지금 바로 해보세요.                                   │
│                                                                 │
│     가입 없이, 설치 없이, 클릭만으로 경험하세요.                  │
│                                                                 │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                                                         │   │
│  │  ┌─────────────────────────────────────────────────┐   │   │
│  │  │                                                 │   │   │
│  │  │   [ 라이브 색상 피커 ]                          │   │   │
│  │  │                                                 │   │   │
│  │  │   Primary Color:  [●] ←─── 클릭해서 변경        │   │   │
│  │  │                                                 │   │   │
│  │  │   ┌───────────────────────────────────────┐    │   │   │
│  │  │   │                                       │    │   │   │
│  │  │   │   [ 미니 웹사이트 프리뷰 ]            │    │   │   │
│  │  │   │                                       │    │   │   │
│  │  │   │   ┌─────┐ ┌─────┐ ┌─────┐            │    │   │   │
│  │  │   │   │ 버튼 │ │ 버튼 │ │ 버튼 │  ← 실시간 변경  │   │   │
│  │  │   │   └─────┘ └─────┘ └─────┘            │    │   │   │
│  │  │   │                                       │    │   │   │
│  │  │   │   ■ 헤더 색상도 변경됨                │    │   │   │
│  │  │   │                                       │    │   │   │
│  │  │   └───────────────────────────────────────┘    │   │   │
│  │  │                                                 │   │   │
│  │  └─────────────────────────────────────────────────┘   │   │
│  │                                                         │   │
│  │            ⬆️ 위 색상을 클릭해보세요!                     │   │
│  │                                                         │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│                                                                 │
│           마음에 드셨나요? [내 사이트에 적용하기]               │
│                                                                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

**목적**: 즉각적인 가치 경험
**인터랙션**: 색상 피커 클릭 → 실시간 프리뷰 업데이트

---

### Section 2: 3분 체험 도전 (Gamified Demo)

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                                                                 │
│           🎯 3분 챌린지: 디자인 시스템 만들기                    │
│                                                                 │
│           ┌─────────────────────────────────────────┐           │
│           │  ⏱️  02:47  │  진행률: ████████░░ 80%   │           │
│           └─────────────────────────────────────────┘           │
│                                                                 │
│                                                                 │
│  Step 1 ✅    Step 2 ✅    Step 3 ⏳    Step 4 ○    Step 5 ○  │
│  색상 선택    폰트 선택    버튼 스타일   저장       공유        │
│                                                                 │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                                                         │   │
│  │   Step 3: 버튼 스타일을 선택하세요                       │   │
│  │                                                         │   │
│  │   ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐   │   │
│  │   │ Rounded │  │  Sharp  │  │  Pill   │  │ Outline │   │   │
│  │   │ [버튼]  │  │ [버튼]  │  │ [버튼]  │  │ [버튼]  │   │   │
│  │   └─────────┘  └─────────┘  └─────────┘  └─────────┘   │   │
│  │                                                         │   │
│  │                                                         │   │
│  │   [ 라이브 프리뷰 영역 ]                                │   │
│  │                                                         │   │
│  │   ┌───────────────────────────────────────────────┐    │   │
│  │   │                                               │    │   │
│  │   │     선택한 스타일이 적용된 웹사이트 미리보기   │    │   │
│  │   │                                               │    │   │
│  │   └───────────────────────────────────────────────┘    │   │
│  │                                                         │   │
│  │                     [ 다음 단계 → ]                     │   │
│  │                                                         │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

**목적**: 게이미피케이션으로 완료 유도
**인터랙션**: 단계별 선택 → 진행률 업데이트 → 성취감

---

### Section 3: 실시간 비교 도구

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                                                                 │
│           ⚡ 실시간 비교: 수동 vs ACF CSS                        │
│                                                                 │
│                                                                 │
│  ┌───────────────────────────┐   ┌───────────────────────────┐ │
│  │                           │   │                           │ │
│  │   수동 방식               │   │   ACF CSS                 │ │
│  │                           │   │                           │ │
│  │   [코드 에디터 시뮬]      │   │   [색상 피커]             │ │
│  │                           │   │                           │ │
│  │   .btn-primary {          │   │   Primary: [●] ← 클릭     │ │
│  │     background: #4F46E5;  │   │                           │ │
│  │   }                       │   │                           │ │
│  │   .btn-secondary {        │   │   [ 자동 적용됨 ]         │ │
│  │     background: #4F46E5;  │   │                           │ │
│  │   }                       │   │                           │ │
│  │   .header {               │   │                           │ │
│  │     background: #4F46E5;  │   │                           │ │
│  │   }                       │   │                           │ │
│  │   ... (12개 더)           │   │                           │ │
│  │                           │   │                           │ │
│  │   ⏱️ 예상 시간: 15분       │   │   ⏱️ 실제 시간: 2초       │ │
│  │                           │   │                           │ │
│  └───────────────────────────┘   └───────────────────────────┘ │
│                                                                 │
│                                                                 │
│           오른쪽 색상 피커를 변경해보세요.                       │
│           왼쪽의 코드가 자동으로 어떻게 생성되는지 확인하세요.    │
│                                                                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

**목적**: 수동 vs 자동의 극명한 차이 경험
**인터랙션**: 오른쪽 피커 변경 → 왼쪽 코드 자동 생성 애니메이션

---

### Section 4: 템플릿 갤러리 (인터랙티브)

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                                                                 │
│           🎨 클릭 한 번으로 적용되는 프리셋                      │
│                                                                 │
│           아래 프리셋을 클릭해서 실시간으로 확인하세요.           │
│                                                                 │
│                                                                 │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐      │
│  │     │ │     │ │     │ │     │ │     │ │     │ │     │      │
│  │ 모던 │ │미니멀│ │ 볼드 │ │클래식│ │ 다크 │ │자연친화│ │ 테크 │      │
│  │     │ │     │ │     │ │     │ │     │ │     │ │     │      │
│  └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘      │
│     ⬆️                                                          │
│   선택됨                                                        │
│                                                                 │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                                                         │   │
│  │                  [ 라이브 프리뷰 ]                       │   │
│  │                                                         │   │
│  │   ┌───────────────────────────────────────────────┐    │   │
│  │   │                                               │    │   │
│  │   │     선택한 프리셋이 적용된 전체 웹사이트      │    │   │
│  │   │                                               │    │   │
│  │   │     헤더 │ 네비게이션 │ 버튼 │ 카드 │ 푸터    │    │   │
│  │   │                                               │    │   │
│  │   │     모든 요소가 일관된 스타일로 변경됨        │    │   │
│  │   │                                               │    │   │
│  │   └───────────────────────────────────────────────┘    │   │
│  │                                                         │   │
│  │            이 스타일로 시작하기 [무료 다운로드]          │   │
│  │                                                         │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

**목적**: 프리셋의 강력함 시각화
**인터랙션**: 프리셋 클릭 → 전체 프리뷰 즉시 변경

---

### Section 5: 나만의 스타일 빌더

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                                                                 │
│           🛠️ 나만의 디자인 시스템 만들기                         │
│                                                                 │
│           아래에서 설정하고, 오른쪽에서 바로 확인하세요.          │
│                                                                 │
│                                                                 │
│  ┌───────────────────────┐   ┌───────────────────────────────┐ │
│  │                       │   │                               │ │
│  │  🎨 색상              │   │   [ 실시간 웹사이트 프리뷰 ]   │ │
│  │                       │   │                               │ │
│  │  Primary    [●]       │   │   ┌─────────────────────────┐ │ │
│  │  Secondary  [●]       │   │   │                         │ │ │
│  │  Accent     [●]       │   │   │   [ 헤더 ]              │ │ │
│  │  Background [●]       │   │   │                         │ │ │
│  │  Text       [●]       │   │   │   [ 콘텐츠 영역 ]       │ │ │
│  │                       │   │   │                         │ │ │
│  │  📝 폰트              │   │   │   ┌───┐ ┌───┐ ┌───┐    │ │ │
│  │                       │   │   │   │ A │ │ B │ │ C │    │ │ │
│  │  Heading: [Inter ▼]   │   │   │   └───┘ └───┘ └───┘    │ │ │
│  │  Body:    [Roboto ▼]  │   │   │                         │ │ │
│  │                       │   │   │   [ 푸터 ]              │ │ │
│  │  🔘 버튼              │   │   │                         │ │ │
│  │                       │   │   └─────────────────────────┘ │ │
│  │  Radius: ○○○●○ (8px)  │   │                               │ │
│  │  Shadow:  ○●○○○ (sm)  │   │   ────────────────────────── │ │
│  │                       │   │                               │ │
│  │                       │   │   CSS 변수 미리보기:          │ │
│  │  [초기화] [저장하기]  │   │   --color-primary: #4F46E5;  │ │
│  │                       │   │   --color-secondary: #10B981;│ │
│  └───────────────────────┘   │   --font-heading: Inter;     │ │
│                               │   ...                        │ │
│                               └───────────────────────────────┘ │
│                                                                 │
│                                                                 │
│           [ 이 설정으로 내 사이트 시작하기 ]                     │
│                                                                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

**목적**: 완전한 커스텀 경험 제공
**인터랙션**: 모든 설정 → 실시간 프리뷰 + CSS 변수 생성

---

### Section 6: 결과물 다운로드/공유

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                                                                 │
│           🎉 축하합니다! 디자인 시스템이 완성되었습니다.          │
│                                                                 │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                                                         │   │
│  │   ┌─────────────────────────────────────────────────┐   │   │
│  │   │                                                 │   │   │
│  │   │     [ 완성된 디자인 시스템 미리보기 ]           │   │   │
│  │   │                                                 │   │   │
│  │   │     Primary: ●   Font: Inter                   │   │   │
│  │   │     Secondary: ●  Button: Rounded              │   │   │
│  │   │                                                 │   │   │
│  │   └─────────────────────────────────────────────────┘   │   │
│  │                                                         │   │
│  │   ┌─────────────────────────────────────────────────┐   │   │
│  │   │                                                 │   │   │
│  │   │   이 디자인 시스템으로 할 수 있는 것들:          │   │   │
│  │   │                                                 │   │   │
│  │   │   ✅ 전체 사이트 일관된 스타일                  │   │   │
│  │   │   ✅ 다크 모드 자동 생성                        │   │   │
│  │   │   ✅ CSS 변수 즉시 사용 가능                    │   │   │
│  │   │   ✅ 팀과 공유 가능                             │   │   │
│  │   │                                                 │   │   │
│  │   └─────────────────────────────────────────────────┘   │   │
│  │                                                         │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│                                                                 │
│   ┌────────────────┐  ┌────────────────┐  ┌────────────────┐   │
│   │                │  │                │  │                │   │
│   │  📥 CSS 다운로드 │  │  📋 코드 복사   │  │  🔗 링크 공유   │   │
│   │                │  │                │  │                │   │
│   └────────────────┘  └────────────────┘  └────────────────┘   │
│                                                                 │
│                                                                 │
│           내 WordPress에 적용하고 싶으신가요?                    │
│                                                                 │
│           [   무료 플러그인 다운로드   ]                         │
│                                                                 │
│           ✓ 방금 만든 설정 그대로 적용됨                         │
│           ✓ 1분 안에 설치 완료                                  │
│           ✓ 언제든 수정 가능                                    │
│                                                                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

**목적**: 작업 결과를 가져가게 함 (투자 효과)
**인터랙션**: 다운로드/복사/공유 → 가입 유도

---

## 3. 기술 구현 상세

### 3.1 라이브 프리뷰 시스템

```javascript
// 실시간 스타일 적용 엔진
class LivePreviewEngine {
    constructor(previewFrame) {
        this.preview = previewFrame;
        this.styles = {
            colors: {},
            fonts: {},
            buttons: {}
        };
    }

    // CSS 변수 업데이트
    updateCSSVariable(name, value) {
        this.preview.contentDocument.documentElement.style.setProperty(
            `--${name}`,
            value
        );

        // 변경 이벤트 발생
        this.emitChange(name, value);
    }

    // 색상 변경
    setColor(type, color) {
        this.styles.colors[type] = color;

        const mappings = {
            primary: ['--color-primary', '--btn-bg', '--link-color'],
            secondary: ['--color-secondary', '--btn-secondary-bg'],
            accent: ['--color-accent', '--highlight'],
            background: ['--bg-color', '--card-bg'],
            text: ['--text-color', '--heading-color']
        };

        mappings[type]?.forEach(variable => {
            this.updateCSSVariable(variable.replace('--', ''), color);
        });

        this.updateCodePreview();
    }

    // 폰트 변경
    setFont(type, fontFamily) {
        this.styles.fonts[type] = fontFamily;

        // Google Fonts 로드
        this.loadGoogleFont(fontFamily);

        const variable = type === 'heading' ? 'font-heading' : 'font-body';
        this.updateCSSVariable(variable, fontFamily);

        this.updateCodePreview();
    }

    // 버튼 스타일 변경
    setButtonStyle(radius, shadow) {
        this.styles.buttons = { radius, shadow };

        this.updateCSSVariable('btn-radius', `${radius}px`);
        this.updateCSSVariable('btn-shadow', this.getShadowValue(shadow));

        this.updateCodePreview();
    }

    // CSS 코드 미리보기 업데이트
    updateCodePreview() {
        const codePreview = document.querySelector('.css-preview');
        if (!codePreview) return;

        let css = ':root {\n';

        // 색상
        Object.entries(this.styles.colors).forEach(([key, value]) => {
            css += `  --color-${key}: ${value};\n`;
        });

        // 폰트
        Object.entries(this.styles.fonts).forEach(([key, value]) => {
            css += `  --font-${key}: "${value}";\n`;
        });

        // 버튼
        if (this.styles.buttons.radius) {
            css += `  --btn-radius: ${this.styles.buttons.radius}px;\n`;
        }

        css += '}';

        codePreview.textContent = css;

        // 구문 강조
        if (window.Prism) {
            Prism.highlightElement(codePreview);
        }
    }

    // 설정 내보내기
    exportSettings() {
        return {
            version: '1.0',
            timestamp: Date.now(),
            styles: this.styles
        };
    }

    // CSS 파일로 다운로드
    downloadCSS() {
        const css = this.generateFullCSS();
        const blob = new Blob([css], { type: 'text/css' });
        const url = URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = 'my-design-system.css';
        a.click();

        URL.revokeObjectURL(url);

        // 다운로드 추적
        this.trackEvent('download', 'css');
    }

    // 공유 링크 생성
    async generateShareLink() {
        const settings = this.exportSettings();
        const encoded = btoa(JSON.stringify(settings));

        // 서버에 저장하고 단축 URL 받기
        const response = await fetch('/api/share', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ settings: encoded })
        });

        const { shareId } = await response.json();
        return `${window.location.origin}/share/${shareId}`;
    }
}
```

### 3.2 3분 챌린지 시스템

```javascript
// 게이미피케이션 챌린지
class ThreeMinuteChallenge {
    constructor() {
        this.steps = [
            { id: 'color', label: '색상 선택', completed: false },
            { id: 'font', label: '폰트 선택', completed: false },
            { id: 'button', label: '버튼 스타일', completed: false },
            { id: 'save', label: '저장', completed: false },
            { id: 'share', label: '공유', completed: false }
        ];
        this.currentStep = 0;
        this.startTime = null;
        this.timerInterval = null;
    }

    start() {
        this.startTime = Date.now();
        this.timerInterval = setInterval(() => this.updateTimer(), 1000);
        this.showStep(0);
    }

    updateTimer() {
        const elapsed = Math.floor((Date.now() - this.startTime) / 1000);
        const remaining = Math.max(0, 180 - elapsed); // 3분 = 180초

        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;

        document.querySelector('.timer').textContent =
            `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

        // 진행률 업데이트
        const progress = (this.currentStep / this.steps.length) * 100;
        document.querySelector('.progress-bar').style.width = `${progress}%`;

        if (remaining === 0) {
            this.complete();
        }
    }

    completeStep(stepId) {
        const stepIndex = this.steps.findIndex(s => s.id === stepId);

        if (stepIndex === -1) return;

        this.steps[stepIndex].completed = true;
        this.updateStepUI(stepIndex);

        // 축하 애니메이션
        this.showCelebration(stepIndex);

        // 다음 단계로
        if (stepIndex < this.steps.length - 1) {
            setTimeout(() => {
                this.currentStep = stepIndex + 1;
                this.showStep(this.currentStep);
            }, 500);
        } else {
            this.complete();
        }
    }

    showStep(index) {
        // 모든 단계 UI 숨기기
        document.querySelectorAll('.step-content').forEach(el => {
            el.classList.remove('active');
        });

        // 현재 단계 표시
        document.querySelector(`[data-step="${this.steps[index].id}"]`)
            .classList.add('active');

        // 단계 인디케이터 업데이트
        document.querySelectorAll('.step-indicator').forEach((el, i) => {
            el.classList.remove('current', 'completed');
            if (i < index) el.classList.add('completed');
            if (i === index) el.classList.add('current');
        });
    }

    showCelebration(stepIndex) {
        // 컨페티 효과
        confetti({
            particleCount: 30,
            spread: 60,
            origin: { y: 0.7 }
        });

        // 성공 메시지
        const messages = [
            '좋아요! 🎨',
            '멋져요! ✨',
            '완벽해요! 👍',
            '저장됐어요! 💾',
            '축하해요! 🎉'
        ];

        this.showToast(messages[stepIndex]);
    }

    complete() {
        clearInterval(this.timerInterval);

        const elapsed = Math.floor((Date.now() - this.startTime) / 1000);

        // 완료 화면 표시
        document.querySelector('.challenge-container').innerHTML = `
            <div class="challenge-complete">
                <h2>🎉 챌린지 완료!</h2>
                <p>소요 시간: ${Math.floor(elapsed / 60)}분 ${elapsed % 60}초</p>

                ${elapsed < 180 ? `
                    <div class="badge">
                        <span class="badge-icon">⚡</span>
                        <span class="badge-text">Speed Master</span>
                    </div>
                    <p>3분 안에 완료하셨네요!</p>
                ` : ''}

                <div class="next-actions">
                    <button class="btn-primary" onclick="downloadResult()">
                        결과물 다운로드
                    </button>
                    <button class="btn-secondary" onclick="startAgain()">
                        다시 도전하기
                    </button>
                </div>
            </div>
        `;
    }
}
```

### 3.3 비교 도구 시스템

```javascript
// 수동 vs 자동 비교
class ComparisonTool {
    constructor(codePanel, autoPanel) {
        this.codePanel = codePanel;
        this.autoPanel = autoPanel;
        this.files = [
            'header.php',
            'footer.php',
            'style.css',
            'page-home.php',
            'page-about.php',
            'page-contact.php',
            'woocommerce/cart.php',
            'woocommerce/checkout.php',
            'woocommerce/single-product.php',
            'template-parts/buttons.php',
            'template-parts/cards.php',
            'template-parts/forms.php'
        ];
    }

    // 색상 변경 시뮬레이션
    simulateColorChange(color) {
        // 수동 방식: 코드 타이핑 애니메이션
        this.simulateManualWork(color);

        // 자동 방식: 즉시 적용
        this.applyAutomatic(color);
    }

    simulateManualWork(color) {
        const codeLines = [];

        this.files.forEach(file => {
            codeLines.push(`// ${file}`);
            codeLines.push(`.btn-primary { background: ${color}; }`);
        });

        // 타이핑 효과
        let lineIndex = 0;
        let charIndex = 0;

        const type = () => {
            if (lineIndex < codeLines.length) {
                const line = codeLines[lineIndex];

                if (charIndex < line.length) {
                    this.codePanel.innerHTML += line.charAt(charIndex);
                    charIndex++;
                    setTimeout(type, 20);
                } else {
                    this.codePanel.innerHTML += '\n';
                    lineIndex++;
                    charIndex = 0;
                    setTimeout(type, 100);
                }
            } else {
                // 완료
                this.showManualTime();
            }
        };

        this.codePanel.innerHTML = '';
        this.manualStartTime = Date.now();
        type();
    }

    showManualTime() {
        const elapsed = Date.now() - this.manualStartTime;
        const estimated = this.files.length * 2 * 60 * 1000; // 파일당 2분

        document.querySelector('.manual-time').innerHTML = `
            ⏱️ 실제 예상 시간: <strong>${Math.floor(estimated / 60000)}분</strong>
            <br>
            <small>(${this.files.length}개 파일 × 2분)</small>
        `;
    }

    applyAutomatic(color) {
        // 즉시 적용
        this.autoPanel.style.setProperty('--color-primary', color);

        // 시간 표시
        document.querySelector('.auto-time').innerHTML = `
            ⏱️ 실제 시간: <strong>1초</strong>
            <br>
            <small>(모든 파일 자동 적용)</small>
        `;

        // 시각적 강조
        this.autoPanel.classList.add('highlight');
        setTimeout(() => {
            this.autoPanel.classList.remove('highlight');
        }, 1000);
    }
}
```

### 3.4 프리셋 갤러리

```javascript
// 프리셋 시스템
class PresetGallery {
    constructor() {
        this.presets = [
            {
                id: 'modern',
                name: '모던',
                colors: {
                    primary: '#4F46E5',
                    secondary: '#10B981',
                    background: '#FFFFFF',
                    text: '#1F2937'
                },
                fonts: {
                    heading: 'Inter',
                    body: 'Inter'
                },
                buttons: { radius: 8, shadow: 'sm' }
            },
            {
                id: 'minimal',
                name: '미니멀',
                colors: {
                    primary: '#000000',
                    secondary: '#6B7280',
                    background: '#FAFAFA',
                    text: '#111827'
                },
                fonts: {
                    heading: 'Helvetica Neue',
                    body: 'Helvetica Neue'
                },
                buttons: { radius: 0, shadow: 'none' }
            },
            {
                id: 'bold',
                name: '볼드',
                colors: {
                    primary: '#EF4444',
                    secondary: '#F59E0B',
                    background: '#FFFFFF',
                    text: '#111827'
                },
                fonts: {
                    heading: 'Poppins',
                    body: 'Open Sans'
                },
                buttons: { radius: 12, shadow: 'md' }
            },
            {
                id: 'dark',
                name: '다크',
                colors: {
                    primary: '#8B5CF6',
                    secondary: '#EC4899',
                    background: '#111827',
                    text: '#F9FAFB'
                },
                fonts: {
                    heading: 'Space Grotesk',
                    body: 'IBM Plex Sans'
                },
                buttons: { radius: 8, shadow: 'lg' }
            },
            // ... 더 많은 프리셋
        ];

        this.currentPreset = null;
    }

    applyPreset(presetId) {
        const preset = this.presets.find(p => p.id === presetId);

        if (!preset) return;

        this.currentPreset = preset;

        // UI 업데이트
        document.querySelectorAll('.preset-item').forEach(el => {
            el.classList.remove('active');
        });
        document.querySelector(`[data-preset="${presetId}"]`)
            .classList.add('active');

        // 스타일 적용
        Object.entries(preset.colors).forEach(([key, value]) => {
            window.previewEngine.setColor(key, value);
        });

        Object.entries(preset.fonts).forEach(([key, value]) => {
            window.previewEngine.setFont(key, value);
        });

        window.previewEngine.setButtonStyle(
            preset.buttons.radius,
            preset.buttons.shadow
        );

        // 전환 애니메이션
        this.animateTransition();
    }

    animateTransition() {
        const preview = document.querySelector('.preview-frame');
        preview.classList.add('transitioning');

        setTimeout(() => {
            preview.classList.remove('transitioning');
        }, 500);
    }

    renderGallery(container) {
        container.innerHTML = this.presets.map(preset => `
            <div class="preset-item" data-preset="${preset.id}">
                <div class="preset-preview">
                    <div class="preset-colors">
                        ${Object.values(preset.colors).slice(0, 3).map(c => `
                            <span class="color-dot" style="background: ${c}"></span>
                        `).join('')}
                    </div>
                </div>
                <div class="preset-name">${preset.name}</div>
            </div>
        `).join('');

        // 클릭 이벤트
        container.querySelectorAll('.preset-item').forEach(el => {
            el.addEventListener('click', () => {
                this.applyPreset(el.dataset.preset);
            });
        });
    }
}
```

---

## 4. 결과물 내보내기

### 4.1 CSS 내보내기

```javascript
function generateFullCSS(styles) {
    return `
/* =========================================
   Generated by 3J Labs ACF CSS
   https://3j-labs.com

   Generated: ${new Date().toISOString()}
   ========================================= */

:root {
    /* Colors */
    --color-primary: ${styles.colors.primary};
    --color-secondary: ${styles.colors.secondary};
    --color-accent: ${styles.colors.accent};
    --color-background: ${styles.colors.background};
    --color-text: ${styles.colors.text};

    /* Typography */
    --font-heading: "${styles.fonts.heading}", sans-serif;
    --font-body: "${styles.fonts.body}", sans-serif;

    /* Buttons */
    --btn-radius: ${styles.buttons.radius}px;
    --btn-shadow: ${getShadowValue(styles.buttons.shadow)};

    /* Derived Colors */
    --color-primary-light: ${lighten(styles.colors.primary, 20)};
    --color-primary-dark: ${darken(styles.colors.primary, 20)};
    --color-border: ${styles.colors.text}20;
    --color-muted: ${styles.colors.text}60;
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    :root {
        --color-background: ${invertColor(styles.colors.background)};
        --color-text: ${invertColor(styles.colors.text)};
    }
}

/* Base Styles */
body {
    font-family: var(--font-body);
    color: var(--color-text);
    background-color: var(--color-background);
}

h1, h2, h3, h4, h5, h6 {
    font-family: var(--font-heading);
}

/* Buttons */
.btn,
button,
[type="submit"] {
    background-color: var(--color-primary);
    color: white;
    border-radius: var(--btn-radius);
    box-shadow: var(--btn-shadow);
    transition: all 0.2s ease;
}

.btn:hover,
button:hover {
    background-color: var(--color-primary-dark);
    transform: translateY(-1px);
}

/* Links */
a {
    color: var(--color-primary);
}

a:hover {
    color: var(--color-primary-dark);
}

/* Cards */
.card {
    background: var(--color-background);
    border: 1px solid var(--color-border);
    border-radius: var(--btn-radius);
}
`;
}
```

### 4.2 공유 기능

```javascript
// 소셜 공유
function shareDesign(platform, shareUrl) {
    const text = '방금 3J Labs ACF CSS로 나만의 디자인 시스템을 만들었어요!';

    const urls = {
        twitter: `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(shareUrl)}`,
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`,
        linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl)}`
    };

    window.open(urls[platform], '_blank', 'width=600,height=400');
}

// 임베드 코드 생성
function generateEmbedCode(shareId) {
    return `<iframe
    src="https://3j-labs.com/embed/${shareId}"
    width="100%"
    height="400"
    frameborder="0"
    title="Design System Preview"
></iframe>`;
}
```

---

## 5. 성과 예측

### 5.1 Version A vs B vs C 비교

| 지표 | Version A | Version B | Version C |
|------|-----------|-----------|-----------|
| 평균 체류 시간 | 2분 | 3.5분 | 5분 |
| 스크롤 완료율 | 40% | 60% | 45% |
| 인터랙션 횟수 | 2 | 3 | 12 |
| 가입 전환율 | 5% | 7% | 9% |
| 이탈률 | 55% | 45% | 35% |
| 공유율 | 2% | 5% | 15% |

### 5.2 Version C 강점

```
1. 높은 참여도
   - 평균 인터랙션 12회
   - "와!" 순간 다수 발생
   - 결과물 획득 욕구

2. 낮은 이탈률
   - 즉각적 가치 체험
   - 게이미피케이션 효과
   - 중단하기 아까움

3. 높은 공유율
   - 결과물 공유 동기
   - 소셜 프루프 자동 생성
   - 바이럴 효과
```

### 5.3 타겟 오디언스별 권장

```
Version C 최적 대상:
✓ 시각적 학습자
✓ 빠른 결과 원하는 사용자
✓ 기술 이해도 중간
✓ 소셜 공유 활발

Version A 권장 대상:
✓ 빠른 정보 탐색 원하는 사용자
✓ 기술 이해도 높음
✓ 비교 분석 중시

Version B 권장 대상:
✓ 감정적 연결 중시
✓ 시간 여유 있음
✓ 스토리에 몰입 잘 함
```

---

## 6. 기술 요구사항

### 6.1 프론트엔드

- React 또는 Vue.js (권장)
- Canvas API (프리뷰 렌더링)
- Web Workers (비동기 처리)
- LocalStorage (설정 임시 저장)

### 6.2 백엔드

- 공유 링크 저장 API
- 사용 통계 수집
- 에셋 CDN

### 6.3 성능 최적화

- 라이브 프리뷰 60fps 유지
- 폰트 프리로딩
- 이미지 레이지 로딩
- CSS 변수 batch 업데이트

---

**© 2026 3J Labs. All rights reserved.**

# 🎨 Nexter Theme 랜딩 페이지 구축 가이드

Nexter Theme + ThePlus Addons를 사용하여 j-j-labs.com에 ACF CSS Manager 랜딩 페이지를 구축합니다.

---

## 📋 사전 요구사항

- ✅ Nexter Theme 설치 및 활성화
- ✅ ThePlus Addons for Elementor (권장)
- ✅ Elementor (Free 또는 Pro)
- ✅ j-j-labs.com 도메인

---

## 🏗️ 페이지 구조

```
ACF CSS Manager 랜딩 페이지
├── Hero 섹션 (풀스크린, 다크 테마)
├── Features 섹션 (6개 기능 카드)
├── AI 섹션 (AI 데모 시각화)
├── Pricing 섹션 (Free/PRO/Partner)
├── CTA 섹션 (베타 신청)
└── Footer
```

---

## 🎯 Step 1: 페이지 생성

### 1.1 새 페이지 생성

1. **페이지 > 새로 추가**
2. 제목: "ACF CSS Manager"
3. 퍼머링크: `/acf-css-manager/`

### 1.2 페이지 설정

**Nexter Theme 옵션:**
- Page Layout: Full Width
- Page Title: Hide
- Header: Transparent (또는 Dark)
- Footer: Hide (커스텀 푸터 사용 시)

### 1.3 Elementor 편집

**편집 > Elementor로 편집** 클릭

---

## 🌟 Step 2: Hero 섹션

### Elementor 구성

```
[Section]
├── Layout: Full Width
├── Height: Min Height - 100vh
├── Background: Gradient (#0f172a to #1e293b)
├── Background Overlay: Pattern (선택)
│
└── [Container]
    ├── [Heading] "코딩 없이 전문가 수준의 웹사이트 디자인"
    │   ├── Size: XXL
    │   ├── Color: #ffffff
    │   ├── Animation: Fade In Up
    │
    ├── [Text Editor] 서브 텍스트
    │   ├── Color: #94a3b8
    │
    └── [Button Group]
        ├── [Button 1] "🚀 무료로 시작하기"
        │   ├── Type: Primary
        │   ├── Background: Gradient (#2563eb to #1d4ed8)
        │
        └── [Button 2] "자세히 알아보기 →"
            ├── Type: Secondary
            ├── Background: Transparent
            ├── Border: 1px solid rgba(255,255,255,0.2)
```

### ThePlus Addons 활용

- **TP Animated Text**: 타이핑 효과
- **TP Gradient Text**: 그라디언트 하이라이트
- **TP Button**: 고급 호버 효과

---

## 🔧 Step 3: Features 섹션

### Elementor 구성

```
[Section]
├── Background: #0f172a
├── Padding: 100px 0
│
└── [Container]
    ├── [Heading] "모든 스타일을 한 곳에서"
    │   ├── Alignment: Center
    │
    ├── [Text] 서브 텍스트
    │
    └── [ThePlus Info Box Grid] 또는 [Icon Box]
        ├── Columns: 3
        ├── Gap: 30px
        │
        ├── [Feature 1] 🎨 색상 팔레트 관리
        ├── [Feature 2] 🔤 타이포그래피 시스템
        ├── [Feature 3] 🤖 AI 스타일 생성
        ├── [Feature 4] ☁️ 클라우드 동기화
        ├── [Feature 5] ⚡ 성능 최적화
        └── [Feature 6] 🏢 에이전시 모드
```

### 각 Feature 카드 스타일

```css
/* 커스텀 CSS */
.acf-feature-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 30px;
    transition: all 0.3s;
}

.acf-feature-card:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(37, 99, 235, 0.3);
    transform: translateY(-5px);
}
```

---

## 🤖 Step 4: AI 섹션

### Elementor 구성 (2 Column Layout)

```
[Section]
├── Background: Gradient with orange tint
│
└── [Container]
    ├── [Column 1 - 50%]
    │   ├── [Heading] "AI가 스타일을 생성합니다"
    │   ├── [Text] 설명
    │   └── [Icon List] 기능 목록
    │
    └── [Column 2 - 50%]
        └── [HTML/Code Widget] AI 데모 터미널
```

### AI 데모 터미널 HTML

```html
<div class="ai-demo-terminal">
    <div class="terminal-header">
        <span class="dot red"></span>
        <span class="dot yellow"></span>
        <span class="dot green"></span>
    </div>
    <div class="terminal-content">
        <div class="prompt">
            > "고급스러운 블랙&골드, 법률사무소 느낌"
        </div>
        <div class="result">
            <div class="result-item">
                <span class="swatch" style="background: #1a1a1a;"></span>
                Primary: #1a1a1a (딥 블랙)
            </div>
            <div class="result-item">
                <span class="swatch" style="background: #d4af37;"></span>
                Secondary: #d4af37 (골드)
            </div>
            <div class="result-item">
                📝 Heading: Playfair Display
            </div>
        </div>
    </div>
</div>
```

---

## 💰 Step 5: Pricing 섹션

### ThePlus Pricing Table 사용

```
[Section]
├── Background: #0f172a
│
└── [Container]
    ├── [Heading] "심플한 가격 정책"
    │
    └── [ThePlus Pricing Table] 또는 [Custom Layout]
        ├── [Free Plan]
        │   ├── Price: ₩0
        │   ├── Button: 무료 다운로드
        │   └── Link: /shop/acf-css-free/
        │
        ├── [PRO Plan] ⭐ Featured
        │   ├── Price: ₩49,000
        │   ├── Badge: "인기"
        │   ├── Button: PRO 구매하기
        │   └── Link: /shop/acf-css-pro/
        │
        └── [Partner Plan]
            ├── Price: ₩199,000
            ├── Button: 문의하기
            └── Link: /contact/
```

### WooCommerce 상품 연동

ThePlus **Product Grid** 위젯 또는 WooCommerce 쇼트코드 사용:

```
[products ids="123,124,125" columns="3"]
```

---

## 📢 Step 6: CTA 섹션

### Elementor 구성

```
[Section]
├── Background: Radial gradient (blue glow)
├── Padding: 100px 0
│
└── [Container]
    ├── [Heading] "지금 베타 테스터로 참여하세요"
    ├── [Text] "베타 테스터에게는 정식 출시 시 50% 할인 쿠폰을 드립니다."
    │
    └── [Button] "✨ 베타 테스터 신청"
        ├── Link: #beta-form 또는 /beta/
        └── Style: Primary, Large
```

### 베타 신청 폼 (Elementor Pro Form 또는 WPForms)

```
[Form]
├── 이름 (필수)
├── 이메일 (필수)
├── 관심 에디션 (Select: Free/PRO/Partner)
└── 제출 버튼
```

---

## 🎨 Step 7: 글로벌 스타일 설정

### Elementor 사이트 설정

**사이트 설정 > 글로벌 색상:**

| 이름 | HEX |
|------|-----|
| Primary | #2563eb |
| Primary Dark | #1d4ed8 |
| Secondary | #f59e0b |
| Accent | #06b6d4 |
| Dark BG | #0f172a |
| Card BG | #1e293b |
| Text | #ffffff |
| Text Muted | #94a3b8 |

**사이트 설정 > 글로벌 폰트:**

| 용도 | 폰트 |
|------|------|
| Heading | Space Grotesk, 700 |
| Body | Noto Sans KR, 400 |

---

## 📱 Step 8: 반응형 설정

### 모바일 최적화

각 섹션에서 **반응형 모드** 전환 후:

1. **Tablet (768px):**
   - Features Grid: 2 columns
   - AI Section: Stack

2. **Mobile (360px):**
   - Features Grid: 1 column
   - Pricing: Stack
   - Hero Text: 더 작은 폰트

---

## ⚡ Step 9: 성능 최적화

### 이미지 최적화

- WebP 형식 사용
- Lazy Loading 활성화

### CSS/JS 최적화

1. **Elementor > 설정 > 고급:**
   - Improved Asset Loading: 활성화
   - CSS Print Method: External File

2. **ThePlus Addons > 성능:**
   - Unused Widgets 비활성화

### 캐싱

- WP Rocket 또는 LiteSpeed Cache 사용
- Kinsta 캐시 활성화

---

## 🔍 Step 10: SEO 설정

### Yoast SEO 또는 Rank Math

```
Title: ACF CSS Manager - WordPress 스타일 관리의 새로운 기준
Meta Description: 코딩 없이 전문가 수준의 웹사이트 디자인. AI가 당신의 아이디어를 CSS 스타일로 변환합니다.
Focus Keyword: WordPress 스타일 관리, CSS 관리 플러그인
```

### Open Graph

```
OG Image: 랜딩 페이지 스크린샷 (1200x630px)
OG Title: ACF CSS Manager
OG Description: AI 기반 WordPress 스타일 관리 플러그인
```

---

## ✅ 최종 체크리스트

- [ ] 모든 섹션 콘텐츠 완성
- [ ] 버튼 링크 정상 작동
- [ ] WooCommerce 상품 연결
- [ ] 모바일 반응형 확인
- [ ] 로딩 속도 테스트 (목표: 3초 이내)
- [ ] 폼 테스트 (베타 신청)
- [ ] SEO 메타 설정
- [ ] Google Analytics 연동

---

## 📞 지원

Nexter Theme/ThePlus Addons 관련:
- 공식 문서: https://theplusaddons.com/docs/

J&J Labs 기술 지원:
- support@j-j-labs.com


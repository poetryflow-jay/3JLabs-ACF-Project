# WordPress에서 디자인 시스템 구축하기: 실용 가이드

**카테고리:** 디자인 시스템  
**태그:** 디자인 시스템, CSS 변수, 일관성, 브랜딩  
**읽기 시간:** 10분

---

## 🎯 디자인 시스템이란?

디자인 시스템은 **일관된 사용자 경험**을 만들기 위한 재사용 가능한 컴포넌트와 스타일의 모음입니다.

### 왜 필요한가?

**Without 디자인 시스템:**
- 페이지마다 다른 버튼 색상
- "이 폰트 사이즈 뭐였지?" 반복
- 디자이너-개발자 커뮤니케이션 비용 증가
- 유지보수 악몽

**With 디자인 시스템:**
- 일관된 브랜드 경험
- "Primary Button 사용해" 한마디로 끝
- 빠른 개발 속도
- 쉬운 유지보수

---

## 📐 디자인 시스템의 구성 요소

### 1. 색상 (Color Palette)

```
Foundation Colors:
├── Primary: #2563EB (브랜드 메인)
├── Secondary: #64748B (서브/보조)
├── Accent: #F59E0B (강조/CTA)
└── Neutral: #0F172A ~ #F8FAFC (회색 스케일)

Semantic Colors:
├── Success: #10B981
├── Warning: #F59E0B
├── Error: #EF4444
└── Info: #3B82F6
```

### 2. 타이포그래피 (Typography)

```
Font Families:
├── Heading: 'Space Grotesk', sans-serif
└── Body: 'Noto Sans KR', sans-serif

Font Sizes (Type Scale):
├── xs: 0.75rem (12px)
├── sm: 0.875rem (14px)
├── base: 1rem (16px)
├── lg: 1.125rem (18px)
├── xl: 1.25rem (20px)
├── 2xl: 1.5rem (24px)
├── 3xl: 1.875rem (30px)
├── 4xl: 2.25rem (36px)
└── 5xl: 3rem (48px)
```

### 3. 간격 (Spacing)

```
Spacing Scale:
├── 1: 0.25rem (4px)
├── 2: 0.5rem (8px)
├── 3: 0.75rem (12px)
├── 4: 1rem (16px)
├── 6: 1.5rem (24px)
├── 8: 2rem (32px)
├── 12: 3rem (48px)
└── 16: 4rem (64px)
```

### 4. 컴포넌트 (Components)

```
Buttons:
├── Primary: bg-primary, text-white
├── Secondary: bg-transparent, border-primary
└── Ghost: bg-transparent, text-primary

Cards:
├── Default: bg-white, shadow-sm, rounded-lg
├── Elevated: bg-white, shadow-lg, rounded-xl
└── Dark: bg-slate-800, text-white
```

---

## 🛠️ ACF CSS Manager로 구현하기

### Step 1: 색상 정의

```
ACF CSS Manager → Colors
├── Primary Color: #2563EB
├── Secondary Color: #64748B
├── Accent Color: #F59E0B
├── Background Color: #FFFFFF
└── Text Color: #0F172A
```

이 색상들은 자동으로 CSS 변수로 변환됩니다:

```css
:root {
  --jj-primary-color: #2563EB;
  --jj-secondary-color: #64748B;
  --jj-accent-color: #F59E0B;
  --jj-background-color: #FFFFFF;
  --jj-text-color: #0F172A;
}
```

### Step 2: 타이포그래피 설정

```
ACF CSS Manager → Typography
├── Heading Font: Space Grotesk
├── Body Font: Noto Sans KR
├── Base Font Size: 16px
└── Line Height: 1.6
```

### Step 3: 버튼 스타일

```
ACF CSS Manager → Buttons
├── Border Radius: 12px
├── Padding: 12px 24px
├── Primary BG: var(--jj-primary-color)
└── Hover Effect: darken 10%
```

---

## 📝 디자인 토큰 문서화

### 방법 1: Notion/Confluence

```markdown
# 색상 팔레트

| 이름 | 값 | 용도 |
|------|-----|------|
| Primary | #2563EB | CTA 버튼, 링크 |
| Secondary | #64748B | 보조 텍스트, 아이콘 |
| Accent | #F59E0B | 배지, 알림 |
```

### 방법 2: 스타일 가이드 페이지

WordPress 페이지로 스타일 가이드를 만들어 실제 컴포넌트를 보여줍니다.

### 방법 3: ACF CSS Manager 내보내기

```
ACF CSS Manager → Cloud → Export
→ JSON 또는 CSS 변수 형식으로 내보내기
```

---

## 🎨 확장: 다크 모드

### CSS 변수 활용

```css
:root {
  --jj-background-color: #FFFFFF;
  --jj-text-color: #0F172A;
}

[data-theme="dark"] {
  --jj-background-color: #0F172A;
  --jj-text-color: #F8FAFC;
}
```

### ACF CSS Manager에서 설정

```
ACF CSS Manager → Advanced → Dark Mode
→ 다크 모드 색상 설정
→ 자동 전환 또는 토글 버튼
```

---

## 📊 팀 협업

### 디자이너 → 개발자

1. **Figma에서 디자인 시스템 정의**
   - 색상, 폰트, 컴포넌트

2. **ACF CSS Manager로 가져오기**
   - 수동 입력 또는 JSON Import

3. **자동 CSS 변수 생성**
   - 개발자는 변수만 사용

### 예시 워크플로우

```
디자이너: "Primary를 #2563EB로 변경했어요"
    ↓
관리자: ACF CSS Manager에서 색상 변경
    ↓
자동: 전체 사이트에 새 색상 적용
    ↓
개발자: 추가 작업 없음 ✓
```

---

## ✅ 디자인 시스템 체크리스트

### 기초 단계
- [ ] 브랜드 색상 정의 (3-5개)
- [ ] 폰트 패밀리 선택 (1-2개)
- [ ] 기본 간격 단위 결정

### 중급 단계
- [ ] 전체 색상 팔레트 (Semantic 포함)
- [ ] 타입 스케일 정의
- [ ] 버튼 스타일 통일
- [ ] 카드/박스 스타일

### 고급 단계
- [ ] 다크 모드 지원
- [ ] 반응형 타이포그래피
- [ ] 애니메이션/트랜지션 가이드라인
- [ ] 문서화 및 스타일 가이드 페이지

---

## 🚀 시작하기

디자인 시스템은 거창하게 시작할 필요 없습니다.

**오늘 할 것:**
1. ACF CSS Manager 설치
2. 3가지 색상 정의 (Primary, Secondary, Accent)
3. 1가지 폰트 선택
4. 저장

**다음 주:**
- 타입 스케일 정리
- 버튼 스타일 통일

**다음 달:**
- 전체 컴포넌트 정리
- 팀 공유

---

## 결론

디자인 시스템은 "완벽하게 만들고 시작"이 아니라 "작게 시작하고 점진적으로 확장"하는 것입니다.

ACF CSS Manager와 함께라면 코드 없이도 체계적인 디자인 시스템을 구축할 수 있습니다.

**지금 시작하세요!**

[ACF CSS Manager 무료 다운로드 →](#download)

---

*디자인 시스템을 어떻게 관리하고 계신가요? 팁을 댓글로 공유해주세요!*


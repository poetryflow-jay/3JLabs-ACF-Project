# ACF CSS Manager - 스크린샷 & 더미 데이터 가이드

> 마케팅 자료, 문서, 마켓플레이스 등록을 위한 스크린샷 촬영 가이드

**최종 업데이트**: 2025년 12월 18일

---

## 📸 필수 스크린샷 목록

### 1. 메인 화면 (Hero Shot)

**파일명**: `01-hero-admin-center.png`

**촬영 위치**: 설정 > ACF CSS Manager

**설정**:
- 브라우저: 1920x1080 (Full HD)
- 확대: 100%
- 테마: WordPress 기본 다크 모드 OFF

**체크리스트**:
- [ ] Admin Center 탭 전체 보이도록
- [ ] Colors 탭 활성화 상태
- [ ] 색상 피커가 열린 상태로
- [ ] 좌측 사이드바에 "ACF CSS" 메뉴 하이라이트

---

### 2. AI 스타일 생성 (Phase 7 핵심)

**파일명**: `02-ai-generation.png`

**촬영 위치**: 설정 > ACF CSS AI

**더미 데이터**:
```
프롬프트: "고급스러운 블랙&골드, 법률사무소 느낌. 버튼은 선명하게."
```

**체크리스트**:
- [ ] 프롬프트 입력란에 위 텍스트 입력
- [ ] "AI 제안 생성" 버튼 클릭 후 결과 표시
- [ ] Before/After Diff가 시각적으로 보이도록
- [ ] 색상 스와치(🔵→⬛)가 명확히 보이도록

---

### 3. 스마트 프리뷰 (Diff 상세)

**파일명**: `03-smart-preview-diff.png`

**촬영 위치**: 설정 > ACF CSS AI > 결과 영역

**더미 데이터 (AI 응답 예시)**:
```json
{
  "explanation": "고급스러운 법률사무소 느낌을 위해 딥 블랙(#1a1a1a)을 Primary Color로, 골드(#d4af37)를 Accent Color로 설정했습니다.",
  "settings_patch": {
    "palettes": {
      "brand_primary": "#1a1a1a",
      "brand_secondary": "#d4af37",
      "brand_accent": "#8b7355"
    },
    "typography": {
      "heading_font": "Playfair Display",
      "body_font": "Noto Sans KR"
    }
  }
}
```

**체크리스트**:
- [ ] Diff 컨테이너가 명확히 보이도록
- [ ] 변경된 항목이 하이라이트
- [ ] "적용(저장)" 버튼과 "☁️ Cloud 저장" 버튼 모두 보이도록

---

### 4. Cloud 저장 성공

**파일명**: `04-cloud-export-success.png`

**촬영 위치**: 설정 > ACF CSS AI > Cloud 저장 후 Alert

**더미 데이터**:
```
Alert 메시지: "클라우드에 저장되었습니다! 공유 코드: ABC123XYZ"
```

**체크리스트**:
- [ ] 성공 Alert 창이 보이도록
- [ ] 공유 코드가 명확히 읽히도록

---

### 5. 템플릿 마켓

**파일명**: `05-template-market.png`

**촬영 위치**: 설정 > ACF CSS Manager > Cloud 탭

**더미 데이터 (미리 등록된 템플릿)**:
| 이름 | 카테고리 | 가격 |
|------|---------|------|
| Elegant Law Firm | 비즈니스 | 무료 |
| Cozy Cafe Vibes | 카페/레스토랑 | $9 |
| Tech Startup Dark | 기술/개발 | 무료 |

**체크리스트**:
- [ ] 템플릿 카드 3개 이상 보이도록
- [ ] 검색창과 카테고리 필터 보이도록
- [ ] "내 스타일 판매하기" 버튼 보이도록

---

### 6. Partner Hub (에이전시용)

**파일명**: `06-partner-hub.png`

**촬영 위치**: 설정 > ACF CSS Manager > Partner Hub 탭

**더미 데이터 (연결된 사이트)**:
| 사이트 | 상태 | 마지막 동기화 |
|--------|------|--------------|
| client-a.com | ✅ 활성 | 방금 전 |
| client-b.kr | ✅ 활성 | 1시간 전 |
| client-c.io | ⚠️ 오류 | 3일 전 |

**체크리스트**:
- [ ] 사이트 목록 테이블 보이도록
- [ ] 상태 배지(활성/오류) 색상 명확히
- [ ] "전체 동기화" 버튼 보이도록

---

### 7. Customizer 연동

**파일명**: `07-customizer-live.png`

**촬영 위치**: 외모 > 사용자 정의하기 > ACF CSS

**체크리스트**:
- [ ] Customizer 사이드바에 ACF CSS 패널 열린 상태
- [ ] 우측에 실시간 미리보기 보이도록
- [ ] 색상 변경 시 미리보기 반영된 상태

---

### 8. 플러그인 목록 (Quick Links)

**파일명**: `08-plugin-list-quicklinks.png`

**촬영 위치**: 플러그인 > 설치된 플러그인

**체크리스트**:
- [ ] ACF CSS 플러그인 행 보이도록
- [ ] Quick Links (Settings | Styles | Menu | Visual) 보이도록
- [ ] 플러그인 이름 "(Master ver.)" 또는 "PRO" 표시 보이도록

---

### 9. 멀티사이트 네트워크 관리

**파일명**: `09-multisite-network.png`

**촬영 위치**: 네트워크 관리자 > 설정 > ACF CSS Network

**더미 데이터**:
- 네트워크 활성화: ON
- 사이트 오버라이드 허용: OFF
- 연결된 사이트 수: 5

**체크리스트**:
- [ ] 네트워크 설정 폼 보이도록
- [ ] "전체 사이트 적용" 버튼 보이도록
- [ ] 사이트 목록 테이블 보이도록

---

### 10. Webhook 설정

**파일명**: `10-webhook-settings.png`

**촬영 위치**: 설정 > ACF CSS Manager > 업데이트 탭

**더미 데이터**:
```
Webhook URL: https://hooks.zapier.com/...
Secret: ●●●●●●●●
Events: ☑️ 스타일 저장 ☑️ Admin Center 저장
```

**체크리스트**:
- [ ] Webhook 설정 폼 보이도록
- [ ] "테스트 Webhook" 버튼 보이도록
- [ ] 이벤트 체크박스 보이도록

---

## 🎨 더미 데이터 세트

### 색상 팔레트 예시

**법률사무소 테마**:
```json
{
  "brand_primary": "#1a1a1a",
  "brand_secondary": "#d4af37",
  "brand_accent": "#8b7355",
  "text_primary": "#ffffff",
  "text_secondary": "#b8b8b8",
  "background_primary": "#0d0d0d",
  "background_secondary": "#1f1f1f"
}
```

**카페 테마**:
```json
{
  "brand_primary": "#8b5a2b",
  "brand_secondary": "#d4a574",
  "brand_accent": "#2d5a3d",
  "text_primary": "#3d2914",
  "text_secondary": "#6b4423",
  "background_primary": "#faf6f0",
  "background_secondary": "#f5ebe0"
}
```

**테크 스타트업 테마**:
```json
{
  "brand_primary": "#6366f1",
  "brand_secondary": "#8b5cf6",
  "brand_accent": "#06b6d4",
  "text_primary": "#e2e8f0",
  "text_secondary": "#94a3b8",
  "background_primary": "#0f172a",
  "background_secondary": "#1e293b"
}
```

### 타이포그래피 예시

```json
{
  "heading_font": "Playfair Display",
  "body_font": "Noto Sans KR",
  "button_font": "Montserrat",
  "heading_weight": "700",
  "body_weight": "400",
  "heading_size": "2.5rem",
  "body_size": "1rem"
}
```

### AI 프롬프트 예시

| 용도 | 프롬프트 |
|------|---------|
| 법률사무소 | "고급스러운 블랙&골드, 법률사무소 느낌. 버튼은 선명하게." |
| 카페 | "따뜻한 파스텔, 카페 웹사이트에 어울리는 부드러운 분위기" |
| 스타트업 | "미니멀하고 모던한 다크 테마, 테크 스타트업 느낌" |
| 병원 | "깨끗하고 신뢰감 있는 블루&화이트, 병원/클리닉 느낌" |
| 쇼핑몰 | "활기차고 젊은 느낌, 패션 쇼핑몰에 어울리는 트렌디한 색상" |

---

## 🖼️ 이미지 사양

| 용도 | 해상도 | 형식 | 품질 |
|------|--------|------|------|
| 마켓플레이스 메인 | 1920x1080 | PNG | 최고 |
| 썸네일 | 590x300 | PNG | 높음 |
| 모바일 | 750x1334 | PNG | 높음 |
| 문서 내 삽입 | 800x600 | PNG | 중간 |
| 소셜 미디어 | 1200x630 | PNG | 높음 |

---

## 📹 영상 촬영 가이드

### 30초 티저 영상

1. **0-5초**: 로고 인트로
2. **5-15초**: 문제 제시 ("CSS 수정하느라 시간 낭비하고 계신가요?")
3. **15-25초**: 솔루션 데모 (AI 생성 → Diff 확인 → 적용)
4. **25-30초**: CTA ("지금 무료로 시작하세요")

### 2분 데모 영상

1. **0-20초**: 인트로 + 제품 소개
2. **20-50초**: 기본 사용법 (색상/폰트 변경)
3. **50-80초**: AI 스타일 생성 데모
4. **80-100초**: Cloud 저장 및 템플릿 마켓
5. **100-120초**: CTA + 가격 안내

---

## ✅ 촬영 전 체크리스트

- [ ] WordPress 기본 테마(Twenty Twenty-Four) 설치
- [ ] 더미 콘텐츠 생성 (Lorem Ipsum)
- [ ] ACF CSS Manager 최신 버전 설치 및 활성화
- [ ] AI Extension 설치 및 API Key 설정
- [ ] 브라우저 확장 프로그램 비활성화 (깔끔한 화면)
- [ ] 불필요한 플러그인 비활성화
- [ ] 알림/배지 숨기기 (업데이트 알림 등)
- [ ] 스크린샷 도구 준비 (Snagit, Lightshot 등)

---

**© 2025 J&J Labs. All rights reserved.**


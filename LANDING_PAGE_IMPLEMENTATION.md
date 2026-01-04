# 3J Labs ACF CSS - 랜딩 페이지 상세 구현 계획서

**작성일**: 2026년 1월 4일
**버전**: 1.0
**목표**: 전환율 최적화 랜딩 페이지 구현

---

## 1. 랜딩 페이지 전략 개요

### 1.1 목표 및 KPI

| 목표 | 지표 | 현재 | 1개월 | 3개월 | 6개월 |
|------|------|------|-------|-------|-------|
| 트래픽 | 월간 방문자 | 0 | 500 | 3,000 | 10,000 |
| 전환율 | 가입 전환 | 0% | 5% | 8% | 12% |
| 이탈률 | Bounce Rate | - | < 60% | < 50% | < 40% |
| 체류시간 | Avg. Session | - | 2분 | 3분 | 4분 |
| Pro 전환 | Free→Pro | 0% | 3% | 5% | 8% |

### 1.2 타겟 페르소나

#### Persona 1: 프리랜서 웹 개발자 "민수"
```
나이: 28세
경력: 3년
특징:
- 월 5-10개 WordPress 사이트 제작
- 효율성 극대화 필요
- 가격 민감도 높음
- 기술 이해도 높음

Pain Points:
- 매번 같은 CSS 설정 반복
- 클라이언트 요청에 빠른 대응 필요
- 일관된 스타일 유지 어려움

동기:
- 시간 절약
- 작업 품질 향상
- 클라이언트 만족도 증가
```

#### Persona 2: 에이전시 디자이너 "지현"
```
나이: 32세
역할: 디자인 리드
팀 규모: 8명

특징:
- 디자인 시스템 구축 경험
- 개발자와 협업 빈번
- 브랜드 일관성 중시
- 툴 도입 결정권

Pain Points:
- 디자인→개발 핸드오프 비효율
- 팀 간 스타일 불일치
- 디자인 시스템 관리 부재

동기:
- 팀 생산성 향상
- 협업 효율화
- 디자인 시스템 구축
```

#### Persona 3: 쇼핑몰 운영자 "영희"
```
나이: 38세
역할: WooCommerce 쇼핑몰 대표
직원: 3명

특징:
- 기술 이해도 중간
- 비용 대비 효과 중시
- 빠른 결과 원함
- 매출 증대 목표

Pain Points:
- 상품 페이지 디자인 불일치
- 전문가 없이 직접 관리
- 전환율 개선 필요

동기:
- 프로페셔널한 외관
- 전환율 향상
- 쉬운 관리
```

### 1.3 페이지 전략

```
전략: Product-Led Growth (PLG) 최적화

핵심 원칙:
1. 가치 먼저 보여주기 (Demo/Preview)
2. 마찰 최소화 (Free 버전 즉시 제공)
3. 사회적 증거 강화
4. 긴급성/희소성 활용
5. 명확한 CTA 경로
```

---

## 2. 페이지 구조 및 와이어프레임

### 2.1 전체 구조

```
┌──────────────────────────────────────────────────────────┐
│                    HEADER (STICKY)                       │
│  Logo   |   Features   Pricing   Docs   |   Login  CTA  │
├──────────────────────────────────────────────────────────┤
│                                                          │
│                   1. HERO SECTION                        │
│         헤드라인 + 서브헤드 + CTA + 제품 이미지          │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│                   2. SOCIAL PROOF                        │
│           사용자 수 + 평점 + 로고 배열                   │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│                   3. PAIN POINTS                         │
│            문제 제시 (3가지 페인 포인트)                 │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│                   4. SOLUTION                            │
│         핵심 기능 3가지 + 데모 GIF/비디오                │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│                   5. HOW IT WORKS                        │
│              3단계 프로세스 설명                         │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│                   6. FEATURES GRID                       │
│            전체 기능 그리드 (6-9개)                      │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│                   7. INTEGRATIONS                        │
│          호환 플러그인/테마 로고 배열                    │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│                   8. TESTIMONIALS                        │
│               사용자 후기 슬라이더                       │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│                   9. PRICING                             │
│           요금제 비교표 (Free/Pro/Agency)                │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│                   10. FAQ                                │
│             자주 묻는 질문 아코디언                      │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│                   11. FINAL CTA                          │
│              마지막 전환 유도 섹션                       │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                      FOOTER                              │
│   Links   |   Social   |   Newsletter   |   Copyright   │
└──────────────────────────────────────────────────────────┘
```

### 2.2 상세 와이어프레임

#### Section 1: Hero

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                                                                 │
│     WordPress 디자인 시스템,                                     │
│         5분 만에 완성하세요.                                     │
│                                                                 │
│     색상, 폰트, 버튼을 한 곳에서 관리하고                        │
│     모든 페이지에 일관되게 적용하세요.                           │
│                                                                 │
│     [  무료로 시작하기  ]    [ 데모 보기 ]                       │
│                                                                 │
│     ✓ 신용카드 불필요   ✓ 5분 설정   ✓ 14일 Pro 체험            │
│                                                                 │
│     ┌───────────────────────────────────────────────────┐       │
│     │                                                   │       │
│     │          [제품 스크린샷 / 데모 GIF]              │       │
│     │                                                   │       │
│     │      색상 팔레트 에디터 화면                      │       │
│     │                                                   │       │
│     └───────────────────────────────────────────────────┘       │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### Section 2: Social Proof Bar

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│    200+ 사이트      ⭐ 4.9/5 평점      1,000+ 다운로드          │
│                                                                 │
│    ┌────┐  ┌────┐  ┌────┐  ┌────┐  ┌────┐  ┌────┐              │
│    │Logo│  │Logo│  │Logo│  │Logo│  │Logo│  │Logo│              │
│    └────┘  └────┘  └────┘  └────┘  └────┘  └────┘              │
│                                                                 │
│    "WordPress 개발자 500+명이 신뢰합니다"                        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### Section 3: Pain Points

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│              이런 문제, 겪어보셨나요?                            │
│                                                                 │
│  ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐   │
│  │                 │ │                 │ │                 │   │
│  │    🔄 반복       │ │    🎨 불일치    │ │    ⏰ 시간      │   │
│  │                 │ │                 │ │                 │   │
│  │  매번 같은      │ │  페이지마다     │ │  스타일 변경    │   │
│  │  CSS 코드를     │ │  다른 색상,     │ │  하나에         │   │
│  │  반복 작성      │ │  다른 폰트      │ │  30분 이상      │   │
│  │                 │ │                 │ │                 │   │
│  └─────────────────┘ └─────────────────┘ └─────────────────┘   │
│                                                                 │
│               더 이상 시간 낭비하지 마세요.                      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### Section 4: Solution

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│              ACF CSS로 모든 것을 해결하세요                      │
│                                                                 │
│  ┌────────────────────────┐    ┌────────────────────────────┐  │
│  │                        │    │                            │  │
│  │    기능 1: 색상 관리    │    │                            │  │
│  │                        │    │                            │  │
│  │  • 무제한 팔레트       │    │     [데모 GIF 영역]         │  │
│  │  • CSS 변수 자동 생성  │    │                            │  │
│  │  • 실시간 프리뷰       │    │     색상 팔레트 에디터      │  │
│  │                        │    │     실제 작동 화면         │  │
│  │    기능 2: 폰트 관리    │    │                            │  │
│  │                        │    │                            │  │
│  │  • Google Fonts 통합   │    │                            │  │
│  │  • 사용자 정의 폰트    │    │                            │  │
│  │  • 반응형 타이포그래피 │    │                            │  │
│  │                        │    │                            │  │
│  │    기능 3: 버튼 스타일  │    │                            │  │
│  │                        │    │                            │  │
│  │  • 일관된 버튼 디자인  │    │                            │  │
│  │  • 호버 효과           │    │                            │  │
│  │  • 원클릭 적용         │    │                            │  │
│  │                        │    │                            │  │
│  └────────────────────────┘    └────────────────────────────┘  │
│                                                                 │
│                [ 지금 무료로 시작하기 ]                          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### Section 5: How It Works

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                    3단계로 끝내세요                              │
│                                                                 │
│     ┌──────────┐        ┌──────────┐        ┌──────────┐       │
│     │    1     │   ──►  │    2     │   ──►  │    3     │       │
│     │          │        │          │        │          │       │
│     │  설치    │        │  설정    │        │  완료!   │       │
│     │          │        │          │        │          │       │
│     └──────────┘        └──────────┘        └──────────┘       │
│                                                                 │
│     WordPress에서        색상, 폰트,        모든 페이지에       │
│     플러그인 설치        버튼 스타일 설정   자동 적용           │
│                                                                 │
│     소요 시간: 1분       소요 시간: 3분     소요 시간: 즉시     │
│                                                                 │
│                                                                 │
│           ⏱️ 총 소요 시간: 5분 미만                              │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### Section 6: Features Grid

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                    모든 기능 한눈에                              │
│                                                                 │
│  ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐   │
│  │ 🎨 색상 팔레트   │ │ 📝 타이포그래피 │ │ 🔘 버튼 스타일   │   │
│  │                 │ │                 │ │                 │   │
│  │ 무제한 팔레트   │ │ Google Fonts    │ │ 일관된 버튼     │   │
│  │ CSS 변수 출력   │ │ 커스텀 폰트     │ │ 호버 효과       │   │
│  │ 실시간 프리뷰   │ │ 반응형 크기     │ │ 원클릭 적용     │   │
│  └─────────────────┘ └─────────────────┘ └─────────────────┘   │
│                                                                 │
│  ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐   │
│  │ 🌙 다크 모드     │ │ 📦 프리셋       │ │ 👥 팀 협업      │   │
│  │                 │ │                 │ │                 │   │
│  │ 자동 다크 모드  │ │ 10+ 프리셋     │ │ 역할 기반 권한  │   │
│  │ 시스템 연동     │ │ 원클릭 적용     │ │ 변경 이력 추적  │   │
│  │ 커스텀 색상     │ │ 커스텀 생성     │ │ 실시간 동기화   │   │
│  └─────────────────┘ └─────────────────┘ └─────────────────┘   │
│                                                                 │
│  ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐   │
│  │ 🤖 AI 추천       │ │ 💾 백업/복원    │ │ 📊 분석         │   │
│  │        [PRO]    │ │                 │ │        [PRO]    │   │
│  │ AI 색상 추천    │ │ 자동 백업       │ │ 스타일 사용량   │   │
│  │ 트렌드 분석     │ │ 버전 관리       │ │ 성능 리포트     │   │
│  │ 조화로운 팔레트 │ │ 원클릭 복원     │ │ 개선 제안       │   │
│  └─────────────────┘ └─────────────────┘ └─────────────────┘   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### Section 7: Integrations

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│           좋아하는 도구와 완벽하게 호환됩니다                    │
│                                                                 │
│  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐        │
│  │Elementor│ │ Divi  │ │Astra  │ │GenerateP│ │Kadence │        │
│  └────────┘ └────────┘ └────────┘ └────────┘ └────────┘        │
│                                                                 │
│  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐        │
│  │ Woo    │ │Gutenberg│ │Beaver │ │Bricks  │ │Oxygen  │        │
│  │Commerce│ │ Blocks │ │Builder │ │ Builder│ │ Builder│        │
│  └────────┘ └────────┘ └────────┘ └────────┘ └────────┘        │
│                                                                 │
│                  + 더 많은 플러그인 지원                         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### Section 8: Testimonials

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│              사용자들의 생생한 후기                              │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                                                         │   │
│  │  "ACF CSS를 도입하고 나서 사이트 제작 시간이           │   │
│  │   50% 줄었습니다. 특히 색상 팔레트 기능이 최고예요!"   │   │
│  │                                                         │   │
│  │         ⭐⭐⭐⭐⭐                                        │   │
│  │                                                         │   │
│  │   [사진]  김민수 - 프리랜서 웹 개발자                   │   │
│  │          "월 15개 사이트를 납품합니다"                  │   │
│  │                                                         │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│           ◀  [1] [2] [3] [4] [5]  ▶                             │
│                                                                 │
│  ┌───────────────┐ ┌───────────────┐ ┌───────────────┐         │
│  │ "팀 협업이    │ │ "WooCommerce │ │ "다크모드     │         │
│  │  훨씬 쉬워    │ │  스타일링이  │ │  기능 완벽!"  │         │
│  │  졌어요"      │ │  간편해요"   │ │               │         │
│  │  - 이지현     │ │  - 박영희    │ │  - 최철수     │         │
│  └───────────────┘ └───────────────┘ └───────────────┘         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### Section 9: Pricing

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│              심플한 요금제, 숨겨진 비용 없음                     │
│                                                                 │
│  ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐   │
│  │                 │ │  MOST POPULAR   │ │                 │   │
│  │     FREE        │ │    BUSINESS     │ │    AGENCY       │   │
│  │                 │ │                 │ │                 │   │
│  │     $0          │ │    $19/월       │ │    $39/월       │   │
│  │                 │ │    $149/년      │ │    $299/년      │   │
│  │                 │ │    (17% 할인)   │ │    (36% 할인)   │   │
│  │                 │ │                 │ │                 │   │
│  │ ✓ 5개 팔레트    │ │ ✓ 무제한 팔레트 │ │ ✓ 모든 기능     │   │
│  │ ✓ 3개 프리셋   │ │ ✓ AI 추천       │ │ ✓ 25개 사이트   │   │
│  │ ✓ 기본 폰트    │ │ ✓ 팀 협업 3명   │ │ ✓ 화이트라벨    │   │
│  │ ✓ 라이트 모드  │ │ ✓ 다크 모드     │ │ ✓ 클라이언트    │   │
│  │ ✓ 이메일 지원  │ │ ✓ 우선 지원     │ │   대시보드      │   │
│  │                 │ │ ✓ 5개 사이트   │ │ ✓ 전용 지원     │   │
│  │                 │ │                 │ │                 │   │
│  │ [ 무료 시작 ]   │ │ [ Pro 시작 ]    │ │ [ 문의하기 ]    │   │
│  │                 │ │                 │ │                 │   │
│  └─────────────────┘ └─────────────────┘ └─────────────────┘   │
│                                                                 │
│           ✓ 14일 환불 보장   ✓ 언제든 취소 가능                 │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                    LIFETIME                              │   │
│  │                                                          │   │
│  │     $799 (일회성) - 평생 업데이트, 무제한 사이트         │   │
│  │                                                          │   │
│  │     [ Lifetime 구매하기 ]                                │   │
│  │                                                          │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### Section 10: FAQ

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                    자주 묻는 질문                                │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ▼ ACF CSS는 무엇인가요?                                 │   │
│  ├─────────────────────────────────────────────────────────┤   │
│  │   ACF CSS는 WordPress 사이트의 디자인 시스템을 쉽게    │   │
│  │   관리할 수 있는 플러그인입니다. 색상 팔레트, 폰트,    │   │
│  │   버튼 스타일 등을 한 곳에서 관리하고 모든 페이지에    │   │
│  │   일관되게 적용할 수 있습니다.                         │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ▶ 내 테마와 호환되나요?                                 │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ▶ Free와 Pro의 차이점은 무엇인가요?                     │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ▶ 환불 정책이 어떻게 되나요?                            │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ▶ 기술 지원은 어떻게 받나요?                            │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ▶ 업데이트는 어떻게 되나요?                             │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│           더 궁금한 점이 있으신가요? [문의하기]                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### Section 11: Final CTA

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                                                                 │
│          지금 바로 시작하세요.                                   │
│                                                                 │
│     1,000+ 개발자와 함께 WordPress 디자인을                      │
│            더 쉽게 관리하세요.                                   │
│                                                                 │
│                                                                 │
│           [   무료로 시작하기   ]                               │
│                                                                 │
│     ✓ 신용카드 불필요   ✓ 5분 설정   ✓ 14일 Pro 체험            │
│                                                                 │
│                                                                 │
│     💬 "5분 만에 설정 완료! 이제 매일 30분씩 절약해요."         │
│         - 김개발, 프리랜서                                      │
│                                                                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 3. 콘텐츠 상세

### 3.1 헤드라인 옵션 (A/B 테스트용)

```
Option A (기능 중심):
- H1: "WordPress 디자인 시스템, 5분 만에 완성"
- Sub: "색상, 폰트, 버튼을 한 곳에서 관리하고 모든 페이지에 일관되게 적용하세요."

Option B (결과 중심):
- H1: "사이트 제작 시간을 50% 줄이세요"
- Sub: "ACF CSS로 디자인 시스템을 자동화하고 반복 작업에서 해방되세요."

Option C (문제 중심):
- H1: "매번 같은 CSS 코드, 지겹지 않으세요?"
- Sub: "ACF CSS로 한 번 설정하고 모든 페이지에 자동 적용하세요."

Option D (사회적 증거):
- H1: "1,000+ 개발자가 선택한 WordPress 디자인 도구"
- Sub: "시간을 절약하고 일관된 디자인을 유지하는 가장 쉬운 방법"
```

### 3.2 CTA 버튼 텍스트

```
Primary CTA:
- "무료로 시작하기" (기본)
- "지금 다운로드" (WordPress.org용)
- "14일 무료 체험" (Pro 강조)

Secondary CTA:
- "데모 보기"
- "기능 살펴보기"
- "가격 확인하기"
```

### 3.3 신뢰 배지 (Trust Badges)

```
상단 배지:
- "WordPress 호환" 배지
- "4.9/5 평점" 별점
- "GDPR 준수" 배지
- "SSL 보안" 아이콘

결제 페이지 배지:
- Stripe 보안 결제
- PayPal 지원
- 14일 환불 보장
- 256-bit SSL
```

### 3.4 사회적 증거 문구

```
숫자 기반:
- "200+ 사이트에서 사용 중"
- "1,000+ 다운로드"
- "4.9/5 평점 (50+ 리뷰)"
- "5분 평균 설정 시간"

인용구:
- "매일 30분씩 절약해요" - 김민수, 프리랜서
- "팀 협업이 훨씬 쉬워졌어요" - 이지현, 에이전시 리드
- "WooCommerce와 완벽한 호환" - 박영희, 쇼핑몰 운영자
```

---

## 4. 기술 구현

### 4.1 HTML 구조 (시맨틱)

```html
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACF CSS - WordPress 디자인 시스템 | 3J Labs</title>
    <meta name="description" content="WordPress 사이트의 색상, 폰트, 버튼 스타일을 한 곳에서 관리하세요. 5분 설정으로 일관된 디자인 시스템을 구축하세요.">

    <!-- Open Graph -->
    <meta property="og:title" content="ACF CSS - WordPress 디자인 시스템">
    <meta property="og:description" content="색상, 폰트, 버튼을 한 곳에서 관리. 5분 설정으로 모든 페이지에 자동 적용.">
    <meta property="og:image" content="https://3j-labs.com/og-image.jpg">
    <meta property="og:url" content="https://3j-labs.com">
    <meta property="og:type" content="website">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="ACF CSS - WordPress 디자인 시스템">
    <meta name="twitter:description" content="WordPress 디자인 관리의 새로운 기준">
    <meta name="twitter:image" content="https://3j-labs.com/twitter-card.jpg">

    <!-- Schema.org -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "ACF CSS Manager",
        "operatingSystem": "WordPress",
        "applicationCategory": "WebApplication",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "USD"
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.9",
            "ratingCount": "50"
        }
    }
    </script>

    <link rel="stylesheet" href="css/landing.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
</head>
<body>
    <!-- Header -->
    <header class="site-header" id="site-header">
        <div class="container">
            <nav class="nav-main">
                <a href="/" class="logo">
                    <img src="images/logo.svg" alt="3J Labs" width="120" height="40">
                </a>
                <ul class="nav-links">
                    <li><a href="#features">기능</a></li>
                    <li><a href="#pricing">가격</a></li>
                    <li><a href="/docs">문서</a></li>
                    <li><a href="/blog">블로그</a></li>
                </ul>
                <div class="nav-actions">
                    <a href="/login" class="btn-text">로그인</a>
                    <a href="/signup" class="btn-primary">무료 시작</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero" id="hero">
            <div class="container">
                <div class="hero-content">
                    <h1 class="hero-title">
                        WordPress 디자인 시스템,<br>
                        <span class="gradient-text">5분 만에 완성하세요.</span>
                    </h1>
                    <p class="hero-subtitle">
                        색상, 폰트, 버튼을 한 곳에서 관리하고<br>
                        모든 페이지에 일관되게 적용하세요.
                    </p>
                    <div class="hero-cta">
                        <a href="/signup" class="btn-primary btn-lg">
                            무료로 시작하기
                        </a>
                        <a href="#demo" class="btn-secondary btn-lg">
                            <span class="icon-play"></span> 데모 보기
                        </a>
                    </div>
                    <ul class="hero-badges">
                        <li>✓ 신용카드 불필요</li>
                        <li>✓ 5분 설정</li>
                        <li>✓ 14일 Pro 체험</li>
                    </ul>
                </div>
                <div class="hero-media">
                    <div class="product-preview">
                        <img src="images/product-preview.png"
                             alt="ACF CSS 색상 팔레트 에디터"
                             loading="eager">
                    </div>
                </div>
            </div>
        </section>

        <!-- Social Proof -->
        <section class="social-proof" id="social-proof">
            <div class="container">
                <div class="stats-bar">
                    <div class="stat">
                        <span class="stat-number">200+</span>
                        <span class="stat-label">사이트 사용 중</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">⭐ 4.9/5</span>
                        <span class="stat-label">평균 평점</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">1,000+</span>
                        <span class="stat-label">다운로드</span>
                    </div>
                </div>
                <div class="logo-wall">
                    <p class="logo-wall-title">WordPress 개발자 500+명이 신뢰합니다</p>
                    <div class="logo-scroll">
                        <!-- 로고들 -->
                    </div>
                </div>
            </div>
        </section>

        <!-- Pain Points -->
        <section class="pain-points" id="problems">
            <div class="container">
                <h2 class="section-title">이런 문제, 겪어보셨나요?</h2>
                <div class="pain-grid">
                    <div class="pain-card">
                        <div class="pain-icon">🔄</div>
                        <h3>반복 작업</h3>
                        <p>매번 같은 CSS 코드를 복사하고 붙여넣기 하고 계신가요?</p>
                    </div>
                    <div class="pain-card">
                        <div class="pain-icon">🎨</div>
                        <h3>스타일 불일치</h3>
                        <p>페이지마다 다른 색상, 다른 폰트로 일관성이 없나요?</p>
                    </div>
                    <div class="pain-card">
                        <div class="pain-icon">⏰</div>
                        <h3>시간 낭비</h3>
                        <p>작은 스타일 변경에도 30분 이상 걸리나요?</p>
                    </div>
                </div>
                <p class="pain-conclusion">더 이상 시간 낭비하지 마세요.</p>
            </div>
        </section>

        <!-- Solution -->
        <section class="solution" id="solution">
            <div class="container">
                <h2 class="section-title">ACF CSS로 모든 것을 해결하세요</h2>
                <div class="solution-grid">
                    <div class="solution-features">
                        <div class="feature-item">
                            <h3>🎨 색상 관리</h3>
                            <ul>
                                <li>무제한 색상 팔레트</li>
                                <li>CSS 변수 자동 생성</li>
                                <li>실시간 프리뷰</li>
                            </ul>
                        </div>
                        <div class="feature-item">
                            <h3>📝 폰트 관리</h3>
                            <ul>
                                <li>Google Fonts 통합</li>
                                <li>커스텀 폰트 업로드</li>
                                <li>반응형 타이포그래피</li>
                            </ul>
                        </div>
                        <div class="feature-item">
                            <h3>🔘 버튼 스타일</h3>
                            <ul>
                                <li>일관된 버튼 디자인</li>
                                <li>호버/포커스 효과</li>
                                <li>원클릭 전역 적용</li>
                            </ul>
                        </div>
                    </div>
                    <div class="solution-demo">
                        <div class="demo-video">
                            <video autoplay muted loop playsinline>
                                <source src="videos/demo.mp4" type="video/mp4">
                            </video>
                        </div>
                    </div>
                </div>
                <div class="solution-cta">
                    <a href="/signup" class="btn-primary btn-lg">지금 무료로 시작하기</a>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="how-it-works" id="how-it-works">
            <div class="container">
                <h2 class="section-title">3단계로 끝내세요</h2>
                <div class="steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h3>설치</h3>
                        <p>WordPress에서 플러그인을 설치하세요.</p>
                        <span class="step-time">⏱️ 1분</span>
                    </div>
                    <div class="step-arrow">→</div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <h3>설정</h3>
                        <p>색상, 폰트, 버튼 스타일을 설정하세요.</p>
                        <span class="step-time">⏱️ 3분</span>
                    </div>
                    <div class="step-arrow">→</div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <h3>완료!</h3>
                        <p>모든 페이지에 자동 적용됩니다.</p>
                        <span class="step-time">⏱️ 즉시</span>
                    </div>
                </div>
                <p class="total-time">⏱️ 총 소요 시간: <strong>5분 미만</strong></p>
            </div>
        </section>

        <!-- Features Grid -->
        <section class="features" id="features">
            <!-- 기능 그리드 구현 -->
        </section>

        <!-- Integrations -->
        <section class="integrations" id="integrations">
            <!-- 호환성 섹션 구현 -->
        </section>

        <!-- Testimonials -->
        <section class="testimonials" id="testimonials">
            <!-- 후기 슬라이더 구현 -->
        </section>

        <!-- Pricing -->
        <section class="pricing" id="pricing">
            <!-- 가격표 구현 -->
        </section>

        <!-- FAQ -->
        <section class="faq" id="faq">
            <!-- FAQ 아코디언 구현 -->
        </section>

        <!-- Final CTA -->
        <section class="final-cta" id="cta">
            <div class="container">
                <h2>지금 바로 시작하세요.</h2>
                <p>1,000+ 개발자와 함께 WordPress 디자인을 더 쉽게 관리하세요.</p>
                <a href="/signup" class="btn-primary btn-xl">무료로 시작하기</a>
                <ul class="final-badges">
                    <li>✓ 신용카드 불필요</li>
                    <li>✓ 5분 설정</li>
                    <li>✓ 14일 Pro 체험</li>
                </ul>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <img src="images/logo-white.svg" alt="3J Labs" width="120">
                    <p>WordPress 디자인 관리의 새로운 기준</p>
                    <div class="social-links">
                        <a href="https://twitter.com/3jlabs">Twitter</a>
                        <a href="https://facebook.com/3jlabs">Facebook</a>
                        <a href="https://github.com/3jlabs">GitHub</a>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>제품</h4>
                    <ul>
                        <li><a href="/features">기능</a></li>
                        <li><a href="/pricing">가격</a></li>
                        <li><a href="/docs">문서</a></li>
                        <li><a href="/changelog">업데이트</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>회사</h4>
                    <ul>
                        <li><a href="/about">소개</a></li>
                        <li><a href="/blog">블로그</a></li>
                        <li><a href="/contact">문의</a></li>
                        <li><a href="/affiliate">제휴</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>지원</h4>
                    <ul>
                        <li><a href="/help">도움말</a></li>
                        <li><a href="/faq">FAQ</a></li>
                        <li><a href="/status">상태</a></li>
                        <li><a href="/privacy">개인정보</a></li>
                    </ul>
                </div>
                <div class="footer-newsletter">
                    <h4>뉴스레터</h4>
                    <p>최신 업데이트와 팁을 받아보세요.</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="이메일 주소">
                        <button type="submit">구독</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 3J Labs. All rights reserved.</p>
                <ul>
                    <li><a href="/terms">이용약관</a></li>
                    <li><a href="/privacy">개인정보처리방침</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <script src="js/landing.js"></script>
</body>
</html>
```

### 4.2 CSS 스타일 (핵심)

```css
/* css/landing.css */

/* ===========================================
   Variables
   =========================================== */
:root {
    /* Colors */
    --color-primary: #4F46E5;
    --color-primary-dark: #4338CA;
    --color-primary-light: #818CF8;
    --color-secondary: #10B981;
    --color-accent: #F59E0B;

    --color-gray-50: #F9FAFB;
    --color-gray-100: #F3F4F6;
    --color-gray-200: #E5E7EB;
    --color-gray-300: #D1D5DB;
    --color-gray-400: #9CA3AF;
    --color-gray-500: #6B7280;
    --color-gray-600: #4B5563;
    --color-gray-700: #374151;
    --color-gray-800: #1F2937;
    --color-gray-900: #111827;

    --color-white: #FFFFFF;
    --color-black: #000000;

    /* Typography */
    --font-family: 'Pretendard', 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif;
    --font-size-xs: 0.75rem;
    --font-size-sm: 0.875rem;
    --font-size-base: 1rem;
    --font-size-lg: 1.125rem;
    --font-size-xl: 1.25rem;
    --font-size-2xl: 1.5rem;
    --font-size-3xl: 1.875rem;
    --font-size-4xl: 2.25rem;
    --font-size-5xl: 3rem;
    --font-size-6xl: 3.75rem;

    /* Spacing */
    --spacing-xs: 0.25rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --spacing-xl: 2rem;
    --spacing-2xl: 3rem;
    --spacing-3xl: 4rem;
    --spacing-4xl: 6rem;

    /* Border Radius */
    --radius-sm: 0.25rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
    --radius-full: 9999px;

    /* Shadows */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);

    /* Transitions */
    --transition-fast: 150ms ease;
    --transition-normal: 300ms ease;
    --transition-slow: 500ms ease;
}

/* ===========================================
   Base Styles
   =========================================== */
*,
*::before,
*::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

html {
    scroll-behavior: smooth;
}

body {
    font-family: var(--font-family);
    font-size: var(--font-size-base);
    line-height: 1.6;
    color: var(--color-gray-800);
    background-color: var(--color-white);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
}

/* ===========================================
   Header
   =========================================== */
.site-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--color-gray-100);
    transition: var(--transition-normal);
}

.site-header.scrolled {
    box-shadow: var(--shadow-md);
}

.nav-main {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 72px;
}

.nav-links {
    display: flex;
    gap: var(--spacing-xl);
    list-style: none;
}

.nav-links a {
    color: var(--color-gray-600);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition-fast);
}

.nav-links a:hover {
    color: var(--color-primary);
}

.nav-actions {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

/* ===========================================
   Buttons
   =========================================== */
.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm) var(--spacing-lg);
    background: var(--color-primary);
    color: var(--color-white);
    border: none;
    border-radius: var(--radius-md);
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition-fast);
}

.btn-primary:hover {
    background: var(--color-primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm) var(--spacing-lg);
    background: var(--color-white);
    color: var(--color-gray-700);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--radius-md);
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition-fast);
}

.btn-secondary:hover {
    background: var(--color-gray-50);
    border-color: var(--color-gray-400);
}

.btn-lg {
    padding: var(--spacing-md) var(--spacing-xl);
    font-size: var(--font-size-lg);
}

.btn-xl {
    padding: var(--spacing-lg) var(--spacing-2xl);
    font-size: var(--font-size-xl);
}

/* ===========================================
   Hero Section
   =========================================== */
.hero {
    padding: 180px 0 100px;
    background: linear-gradient(180deg, var(--color-gray-50) 0%, var(--color-white) 100%);
}

.hero .container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-3xl);
    align-items: center;
}

.hero-title {
    font-size: var(--font-size-5xl);
    font-weight: 800;
    line-height: 1.2;
    color: var(--color-gray-900);
    margin-bottom: var(--spacing-lg);
}

.gradient-text {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: var(--font-size-xl);
    color: var(--color-gray-600);
    margin-bottom: var(--spacing-xl);
}

.hero-cta {
    display: flex;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
}

.hero-badges {
    display: flex;
    gap: var(--spacing-lg);
    list-style: none;
    color: var(--color-gray-500);
    font-size: var(--font-size-sm);
}

.product-preview {
    position: relative;
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-xl);
}

.product-preview img {
    width: 100%;
    height: auto;
    display: block;
}

/* ===========================================
   Social Proof
   =========================================== */
.social-proof {
    padding: var(--spacing-3xl) 0;
    background: var(--color-gray-900);
    color: var(--color-white);
}

.stats-bar {
    display: flex;
    justify-content: center;
    gap: var(--spacing-4xl);
    margin-bottom: var(--spacing-2xl);
}

.stat {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: var(--font-size-3xl);
    font-weight: 700;
}

.stat-label {
    color: var(--color-gray-400);
    font-size: var(--font-size-sm);
}

/* ===========================================
   Pain Points
   =========================================== */
.pain-points {
    padding: var(--spacing-4xl) 0;
}

.section-title {
    font-size: var(--font-size-3xl);
    font-weight: 700;
    text-align: center;
    margin-bottom: var(--spacing-3xl);
    color: var(--color-gray-900);
}

.pain-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-xl);
    margin-bottom: var(--spacing-2xl);
}

.pain-card {
    padding: var(--spacing-xl);
    background: var(--color-gray-50);
    border-radius: var(--radius-lg);
    text-align: center;
}

.pain-icon {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--spacing-md);
}

.pain-card h3 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-sm);
}

.pain-card p {
    color: var(--color-gray-600);
}

.pain-conclusion {
    text-align: center;
    font-size: var(--font-size-xl);
    font-weight: 600;
    color: var(--color-primary);
}

/* ===========================================
   Pricing
   =========================================== */
.pricing {
    padding: var(--spacing-4xl) 0;
    background: var(--color-gray-50);
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-xl);
}

.pricing-card {
    background: var(--color-white);
    border-radius: var(--radius-xl);
    padding: var(--spacing-2xl);
    text-align: center;
    box-shadow: var(--shadow-md);
    transition: var(--transition-normal);
}

.pricing-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.pricing-card.popular {
    border: 2px solid var(--color-primary);
    position: relative;
}

.pricing-card.popular::before {
    content: 'MOST POPULAR';
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--color-primary);
    color: var(--color-white);
    padding: var(--spacing-xs) var(--spacing-md);
    border-radius: var(--radius-full);
    font-size: var(--font-size-xs);
    font-weight: 700;
}

.pricing-name {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin-bottom: var(--spacing-md);
}

.pricing-price {
    font-size: var(--font-size-4xl);
    font-weight: 800;
    color: var(--color-gray-900);
}

.pricing-price span {
    font-size: var(--font-size-base);
    font-weight: 400;
    color: var(--color-gray-500);
}

.pricing-features {
    list-style: none;
    margin: var(--spacing-xl) 0;
    text-align: left;
}

.pricing-features li {
    padding: var(--spacing-sm) 0;
    color: var(--color-gray-600);
}

.pricing-features li::before {
    content: '✓';
    color: var(--color-secondary);
    margin-right: var(--spacing-sm);
}

/* ===========================================
   FAQ
   =========================================== */
.faq {
    padding: var(--spacing-4xl) 0;
}

.faq-list {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    border-bottom: 1px solid var(--color-gray-200);
}

.faq-question {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-lg) 0;
    cursor: pointer;
    font-weight: 600;
}

.faq-question::after {
    content: '+';
    font-size: var(--font-size-xl);
    color: var(--color-primary);
}

.faq-item.open .faq-question::after {
    content: '−';
}

.faq-answer {
    display: none;
    padding-bottom: var(--spacing-lg);
    color: var(--color-gray-600);
}

.faq-item.open .faq-answer {
    display: block;
}

/* ===========================================
   Final CTA
   =========================================== */
.final-cta {
    padding: var(--spacing-4xl) 0;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
    color: var(--color-white);
    text-align: center;
}

.final-cta h2 {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--spacing-md);
}

.final-cta p {
    font-size: var(--font-size-xl);
    opacity: 0.9;
    margin-bottom: var(--spacing-xl);
}

.final-cta .btn-primary {
    background: var(--color-white);
    color: var(--color-primary);
}

.final-cta .btn-primary:hover {
    background: var(--color-gray-100);
}

.final-badges {
    display: flex;
    justify-content: center;
    gap: var(--spacing-xl);
    list-style: none;
    margin-top: var(--spacing-lg);
    opacity: 0.8;
}

/* ===========================================
   Responsive
   =========================================== */
@media (max-width: 1024px) {
    .hero .container {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .hero-cta {
        justify-content: center;
    }

    .hero-badges {
        justify-content: center;
    }

    .pain-grid,
    .pricing-grid {
        grid-template-columns: 1fr;
        max-width: 400px;
        margin: 0 auto;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: var(--font-size-3xl);
    }

    .section-title {
        font-size: var(--font-size-2xl);
    }

    .nav-links {
        display: none;
    }

    .stats-bar {
        flex-direction: column;
        gap: var(--spacing-lg);
    }
}
```

### 4.3 JavaScript 인터랙션

```javascript
// js/landing.js

// ===========================================
// Header Scroll Effect
// ===========================================
const header = document.getElementById('site-header');

window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

// ===========================================
// Smooth Scroll for Anchor Links
// ===========================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const headerHeight = header.offsetHeight;
            const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});

// ===========================================
// FAQ Accordion
// ===========================================
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const item = question.parentElement;
        const isOpen = item.classList.contains('open');

        // Close all items
        document.querySelectorAll('.faq-item').forEach(i => {
            i.classList.remove('open');
        });

        // Open clicked item if it wasn't open
        if (!isOpen) {
            item.classList.add('open');
        }
    });
});

// ===========================================
// Intersection Observer for Animations
// ===========================================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-in');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

document.querySelectorAll('.animate-on-scroll').forEach(el => {
    observer.observe(el);
});

// ===========================================
// Stats Counter Animation
// ===========================================
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);

    function updateCounter() {
        start += increment;
        if (start < target) {
            element.textContent = Math.floor(start).toLocaleString();
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target.toLocaleString();
        }
    }

    updateCounter();
}

// Trigger counter animation when stats section is visible
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.querySelectorAll('.stat-number').forEach(stat => {
                const target = parseInt(stat.dataset.target);
                if (target) {
                    animateCounter(stat, target);
                }
            });
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

const statsSection = document.querySelector('.stats-bar');
if (statsSection) {
    statsObserver.observe(statsSection);
}

// ===========================================
// Testimonial Slider
// ===========================================
class TestimonialSlider {
    constructor(container) {
        this.container = container;
        this.slides = container.querySelectorAll('.testimonial-slide');
        this.currentIndex = 0;
        this.autoplayInterval = null;

        this.init();
    }

    init() {
        this.createDots();
        this.startAutoplay();
        this.addEventListeners();
    }

    createDots() {
        const dotsContainer = document.createElement('div');
        dotsContainer.className = 'testimonial-dots';

        this.slides.forEach((_, index) => {
            const dot = document.createElement('button');
            dot.className = 'dot';
            dot.dataset.index = index;
            if (index === 0) dot.classList.add('active');
            dotsContainer.appendChild(dot);
        });

        this.container.appendChild(dotsContainer);
        this.dots = dotsContainer.querySelectorAll('.dot');
    }

    goToSlide(index) {
        this.slides[this.currentIndex].classList.remove('active');
        this.dots[this.currentIndex].classList.remove('active');

        this.currentIndex = index;

        this.slides[this.currentIndex].classList.add('active');
        this.dots[this.currentIndex].classList.add('active');
    }

    next() {
        const nextIndex = (this.currentIndex + 1) % this.slides.length;
        this.goToSlide(nextIndex);
    }

    startAutoplay() {
        this.autoplayInterval = setInterval(() => this.next(), 5000);
    }

    stopAutoplay() {
        clearInterval(this.autoplayInterval);
    }

    addEventListeners() {
        this.dots.forEach(dot => {
            dot.addEventListener('click', () => {
                this.stopAutoplay();
                this.goToSlide(parseInt(dot.dataset.index));
                this.startAutoplay();
            });
        });
    }
}

const testimonialSlider = document.querySelector('.testimonial-slider');
if (testimonialSlider) {
    new TestimonialSlider(testimonialSlider);
}

// ===========================================
// Analytics Tracking
// ===========================================
function trackEvent(category, action, label) {
    if (typeof gtag !== 'undefined') {
        gtag('event', action, {
            event_category: category,
            event_label: label
        });
    }
}

// Track CTA clicks
document.querySelectorAll('.btn-primary, .btn-secondary').forEach(btn => {
    btn.addEventListener('click', () => {
        const label = btn.textContent.trim();
        trackEvent('CTA', 'click', label);
    });
});

// Track scroll depth
let scrollDepth = 0;
window.addEventListener('scroll', () => {
    const newDepth = Math.floor((window.scrollY + window.innerHeight) / document.body.scrollHeight * 100);
    if (newDepth > scrollDepth && [25, 50, 75, 100].includes(newDepth)) {
        trackEvent('Scroll', 'depth', `${newDepth}%`);
        scrollDepth = newDepth;
    }
});
```

---

## 5. A/B 테스트 계획

### 5.1 테스트 요소

| 테스트 | 변수 A | 변수 B | 목표 지표 |
|--------|--------|--------|-----------|
| 헤드라인 | 기능 중심 | 결과 중심 | 가입 전환율 |
| CTA 색상 | 보라색 | 초록색 | 클릭률 |
| CTA 텍스트 | "무료로 시작하기" | "지금 다운로드" | 클릭률 |
| 가격 표시 | 월간 강조 | 연간 강조 | 결제 전환율 |
| 사회적 증거 | 숫자 중심 | 후기 중심 | 체류 시간 |
| Hero 이미지 | 스크린샷 | 데모 GIF | 가입 전환율 |

### 5.2 테스트 일정

```
Week 1-2: 헤드라인 테스트
Week 3-4: CTA 테스트
Week 5-6: 가격 표시 테스트
Week 7-8: 사회적 증거 테스트
```

---

## 6. 구현 일정

### Phase 1: 기본 구조 (Week 1)
| 일자 | 작업 |
|------|------|
| Day 1-2 | HTML 구조 + 시맨틱 마크업 |
| Day 3-4 | CSS 스타일 (데스크톱) |
| Day 5 | 반응형 스타일 |

### Phase 2: 인터랙션 (Week 2)
| 일자 | 작업 |
|------|------|
| Day 1-2 | JavaScript 기능 |
| Day 3 | 애니메이션 |
| Day 4-5 | 성능 최적화 |

### Phase 3: 콘텐츠 & 테스트 (Week 3)
| 일자 | 작업 |
|------|------|
| Day 1-2 | 콘텐츠 작성 |
| Day 3 | 이미지/비디오 제작 |
| Day 4-5 | QA 및 버그 수정 |

---

**© 2026 3J Labs. All rights reserved.**

# ACF Nudge Flow - 트리거 기반 넛지 마케팅 플러그인 기획서

> **Advanced Custom Flow - Nudge Marketing Automation for WordPress & WooCommerce**

## 🎯 플러그인 개요

### 이름 후보
1. **ACF Nudge Flow** - Advanced Custom Flow: Nudge Marketing Automation ⭐ 추천
2. **ACF Magnet** - Advanced Custom Flow: Magnet Marketing Booster
3. **ACF Trigger Flow** - Advanced Custom Flow: Trigger-based Marketing
4. **3J Marketing Flow** - 3J Labs Marketing Automation

### 핵심 컨셉
"**IF** 조건이 충족되면, **DO** 액션을 실행한다" - 시각적 워크플로우 빌더

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   TRIGGER   │ ──▶ │  CONDITION  │ ──▶ │   ACTION    │
│   (시작점)   │     │   (조건)    │     │   (실행)    │
└─────────────┘     └─────────────┘     └─────────────┘
```

---

## 🔧 트리거 시스템

### 1. 방문자 트리거 (Visitor Triggers)

| 트리거 | 설명 | 예시 |
|--------|------|------|
| `first_visit` | 첫 방문자 | 웰컴 팝업 |
| `returning_visitor` | 재방문자 | "다시 오셨네요!" 메시지 |
| `visit_count` | N회 이상 방문 | 3회 방문 시 특별 할인 |
| `time_on_site` | 사이트 체류 시간 | 30초 이상 시 뉴스레터 팝업 |
| `scroll_depth` | 스크롤 깊이 | 50% 스크롤 시 CTA 표시 |
| `exit_intent` | 이탈 의도 감지 | 마우스가 브라우저 밖으로 |

### 2. 트래픽 소스 트리거 (Traffic Source Triggers)

| 트리거 | 설명 | 예시 |
|--------|------|------|
| `referrer_type` | 리퍼러 유형 | 광고/오가닉/다이렉트/소셜 |
| `utm_source` | UTM 소스 | google, facebook, naver |
| `utm_medium` | UTM 매체 | cpc, email, social |
| `utm_campaign` | UTM 캠페인명 | black_friday_2026 |
| `ad_platform` | 광고 플랫폼 | Google Ads, Meta Ads, Naver AD |
| `search_keyword` | 검색 키워드 | SEO 유입 키워드 |

### 3. 사용자 상태 트리거 (User State Triggers)

| 트리거 | 설명 | 예시 |
|--------|------|------|
| `logged_in` | 로그인 상태 | 회원 전용 혜택 표시 |
| `user_role` | 사용자 역할 | 구독자/고객/VIP |
| `member_since` | 가입 기간 | 1년 이상 회원 |
| `last_login` | 마지막 로그인 | 30일 이상 미접속 |

### 4. 커머스 트리거 (Commerce Triggers - WooCommerce)

| 트리거 | 설명 | 예시 |
|--------|------|------|
| `has_purchased` | 구매 이력 | 기존 고객 vs 신규 |
| `purchase_count` | 구매 횟수 | 재구매 고객 식별 |
| `total_spent` | 총 구매액 | VIP 고객 식별 |
| `cart_value` | 장바구니 금액 | 5만원 이상 시 무료배송 안내 |
| `cart_items` | 장바구니 상품 수 | 3개 이상 시 번들 할인 |
| `abandoned_cart` | 장바구니 이탈 | 결제 미완료 후 재방문 |
| `viewed_product` | 상품 조회 | 특정 카테고리 관심 |
| `has_inquiry` | 문의 이력 | 문의 후 미구매 |

### 5. 시간 기반 트리거 (Time-based Triggers)

| 트리거 | 설명 | 예시 |
|--------|------|------|
| `time_of_day` | 시간대 | 점심시간 특별 프로모션 |
| `day_of_week` | 요일 | 주말 한정 |
| `date_range` | 날짜 범위 | 블랙프라이데이 기간 |
| `countdown` | 카운트다운 | 마감 N시간 전 |

---

## ⚡ 액션 시스템

### 1. 팝업/모달 (Popups & Modals)

```
┌─────────────────────────────────┐
│  🎉 첫 방문 환영 할인!          │
│                                 │
│  지금 가입하시면 10% 할인!      │
│                                 │
│  [이메일 입력]     [받기]       │
│                                 │
│           나중에 ✕              │
└─────────────────────────────────┘
```

- **Center Modal**: 화면 중앙 팝업
- **Slide-in**: 측면 슬라이드
- **Bottom Bar**: 하단 고정 바
- **Full Screen**: 전체 화면 오버레이
- **Exit Intent Popup**: 이탈 시 팝업

### 2. 인라인 넛지 (Inline Nudges)

- **Floating CTA**: 떠다니는 버튼
- **Sticky Header/Footer**: 고정 헤더/푸터
- **Product Badge**: 상품 배지 (SALE, 한정수량)
- **Timer Bar**: 카운트다운 타이머
- **Social Proof**: "N명이 보고 있습니다"

### 3. 알림 (Notifications)

- **Toast Message**: 토스트 알림
- **Browser Push**: 브라우저 푸시 알림 동의
- **In-App Notification**: 인앱 알림

### 4. 페이지 리다이렉트 (Redirects)

- **Redirect to Page**: 특정 페이지로 이동
- **Show Hidden Content**: 숨겨진 콘텐츠 표시
- **Personalized Content**: 개인화 콘텐츠

### 5. 커머스 액션 (Commerce Actions)

- **Upsell Popup**: 업셀링 추천
- **Cross-sell**: 크로스셀링 추천
- **Cart Reminder**: 장바구니 리마인더
- **Discount Code**: 할인 코드 표시
- **Free Shipping Bar**: 무료배송 프로그레스 바

---

## 🎨 비주얼 워크플로우 빌더 UI

### 노드 기반 에디터 (Node-based Editor)

```
┌──────────────────────────────────────────────────────────────┐
│  📊 워크플로우: 첫 방문자 뉴스레터 유도                        │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│   ┌─────────┐                                                │
│   │ START   │                                                │
│   │ 🎯      │                                                │
│   └────┬────┘                                                │
│        │                                                     │
│        ▼                                                     │
│   ┌─────────────┐    YES    ┌─────────────┐                  │
│   │ IF          │ ────────▶ │ WAIT        │                  │
│   │ 첫 방문자?  │           │ 15초        │                  │
│   └──────┬──────┘           └──────┬──────┘                  │
│          │ NO                      │                         │
│          ▼                         ▼                         │
│   ┌─────────────┐           ┌─────────────┐                  │
│   │ IF          │           │ DO          │                  │
│   │ 재방문자?   │           │ 팝업 표시   │                  │
│   └─────────────┘           │ 🎁 10% 할인 │                  │
│                             └─────────────┘                  │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

### 드래그 앤 드롭 인터페이스

- **좌측 패널**: 트리거/조건/액션 노드 팔레트
- **중앙 캔버스**: 워크플로우 드래그 앤 드롭 영역
- **우측 패널**: 선택된 노드의 상세 설정
- **하단 패널**: 로그 및 테스트 결과

---

## 📦 플러그인 구조

### 무료 버전 (Free)
- 기본 트리거 5개
- 기본 액션 3개
- 워크플로우 3개 제한
- 워터마크 표시

### Pro Basic
- 모든 트리거
- 모든 액션
- 워크플로우 10개
- A/B 테스트

### Pro Premium
- 무제한 워크플로우
- 고급 분석 대시보드
- WooCommerce 연동
- 우선 지원

### Pro Unlimited
- 모든 기능
- 화이트 라벨
- 멀티사이트
- API 액세스

---

## 🔗 기존 플러그인 연동

### ACF CSS Manager에 기본 넛지 탑재

```php
// 기본 넛지: ACF Nudge Flow 플러그인 설치 유도
if ( ! class_exists( 'ACF_Nudge_Flow' ) ) {
    // 3회 이상 스타일 가이드 페이지 방문 시
    if ( $visit_count >= 3 ) {
        show_nudge( 'upgrade_to_nudge_flow' );
    }
}
```

### ACF Code Snippets Box 연동

- 스니펫에서 넛지 트리거 사용 가능
- 조건부 스니펫 실행과 연동

### ACF CSS WooCommerce Toolkit 연동

- 가격 표시와 넛지 연동
- 할인율 표시 + 긴급성 메시지

---

## 🗓️ 개발 로드맵

### Phase 1: 기본 구조 (1주)
- [ ] 플러그인 기본 아키텍처
- [ ] 데이터베이스 스키마
- [ ] 관리자 페이지 기본 UI

### Phase 2: 트리거 시스템 (1주)
- [ ] 방문자 트리거 구현
- [ ] 트래픽 소스 트리거 구현
- [ ] 쿠키/세션 관리

### Phase 3: 액션 시스템 (1주)
- [ ] 팝업/모달 컴포넌트
- [ ] 토스트/알림 시스템
- [ ] 프론트엔드 렌더링

### Phase 4: 워크플로우 빌더 (2주)
- [ ] 노드 기반 에디터 UI
- [ ] 드래그 앤 드롭 구현
- [ ] 워크플로우 저장/로드

### Phase 5: WooCommerce 연동 (1주)
- [ ] 커머스 트리거
- [ ] 업셀/크로스셀 액션
- [ ] 장바구니 연동

### Phase 6: 분석 및 최적화 (1주)
- [ ] 분석 대시보드
- [ ] A/B 테스트
- [ ] 성능 최적화

---

## 📝 기술 스택

- **Frontend**: React (워크플로우 빌더), Vanilla JS (프론트엔드 렌더링)
- **Backend**: PHP 8.0+, WordPress 6.0+
- **Database**: WordPress Options API, Custom Tables
- **UI Library**: 자체 컴포넌트 (ACF CSS 변수 활용)

---

## 💡 경쟁 분석

| 기능 | OptinMonster | Hustle | ACF Nudge Flow |
|------|-------------|--------|----------------|
| 팝업 빌더 | ✅ | ✅ | ✅ |
| 트리거 조건 | ✅ | 제한적 | ✅ 고급 |
| 워크플로우 | ❌ | ❌ | ✅ 시각적 |
| WooCommerce | 제한적 | 제한적 | ✅ 완전 연동 |
| 가격 | $19+/월 | 무료/Pro | 무료/Pro |
| 한국어 | ❌ | ❌ | ✅ 네이티브 |

---

*작성일: 2026-01-02*
*작성자: Jason (CTO) @ 3J Labs*

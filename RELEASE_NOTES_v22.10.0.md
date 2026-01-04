# Release Notes v22.10.0 - Phase 42

**릴리즈 날짜**: 2026년 1월 5일
**버전**: Phase 42 - User Journey Analytics 분리 및 플러그인 연동
**개발팀**: 3J Labs (제이x제니x제이슨 연구소)

---

## 주요 변경사항

### 1. ACF User Journey Map Analytics Dashboard v1.0.0 (신규)

**무료 배포** 플러그인으로, 트래픽 분석 및 사용자 여정 추적 기능을 제공합니다.

#### 핵심 기능

- **50+ 광고 플랫폼 자동 감지**
  - Google Ads (gclid, gad_source, gbraid, wbraid)
  - Meta/Facebook Ads (fbclid, fb_action_ids)
  - Naver Ads (n_media, n_query, n_keyword, na_source)
  - Kakao Ads (kakao_campaign, kakao_adgrp, dkwid)
  - TikTok Ads (ttclid)
  - Microsoft/Bing Ads (msclkid)
  - Twitter/X Ads (twclid)
  - LinkedIn Ads (li_fat_id)
  - Pinterest Ads (epik)
  - Snapchat Ads (ScCid)
  - Criteo (cto_pld)
  - 외 다수

- **AI 검색엔진 트래픽 추적**
  - ChatGPT (chatgpt.com)
  - Perplexity (perplexity.ai)
  - Claude (claude.ai)
  - You.com (you.com)
  - Neeva, Phind 등

- **5단계 전환 퍼널 분석**
  1. 랜딩 (Landing)
  2. 참여 (Engagement) - 30초 이상 체류
  3. 관심 (Interest) - 3페이지 이상 조회
  4. 고려 (Consideration) - 장바구니 추가 등
  5. 전환 (Conversion) - 구매 완료

- **ROI 분석**
  - ROAS (Return on Ad Spend)
  - CPA (Cost per Acquisition)
  - CPC (Cost per Click)
  - 광고 비용 대비 수익률

- **실시간 시각화**
  - Chart.js 기반 대시보드
  - 일별/시간별 트래픽 트렌드
  - 트래픽 소스 분포 (도넛 차트)
  - 디바이스별 통계

- **데이터 내보내기**
  - CSV 내보내기 (UTF-8 BOM 지원)
  - 한글 Excel 호환

#### 크로스 프로모션 전략

무료 플러그인으로 배포하여 유료 플러그인 홍보:
- 플러그인 목록 페이지에서 Nudge Flow 및 1-Click SEO 링크 표시
- 대시보드 하단에 프로모션 배너 표시

---

### 2. ACF Nudge Flow v22.10.0 업데이트

#### 트래픽 분석 기능 분리

- **기존 트래픽 분석 페이지 제거**
  - 자체 트래픽 분석 기능을 User Journey Analytics로 이전
  - 중복 기능 제거로 플러그인 경량화

- **User Journey Analytics 연동 페이지 추가**
  - 메뉴명 변경: "트래픽 분석" → "트래픽 연동"
  - 아이콘 변경: "🔍" → "📊"

#### 연동 페이지 기능

**User Journey Analytics 설치 시:**
- 실시간 트래픽 요약 (방문자, 세션, 전환, 전환율)
- 트래픽 소스별 현황 테이블
- 전환 퍼널 요약 테이블
- "전체 대시보드 보기" 링크

**User Journey Analytics 미설치 시:**
- 설치 안내 배너 표시
- 플러그인 주요 기능 소개
- "무료" 배지 강조
- 원클릭 설치 버튼

#### 활용 가이드

Nudge Flow에서 트래픽 데이터를 활용한 워크플로우 트리거 예시:
- 네이버 광고로 유입된 방문자에게 특별 할인 넛지 표시
- Google 검색 유입 고객에게 SEO 관련 컨텐츠 추천
- 소셜 미디어 유입 고객에게 팔로우 요청

---

### 3. WP 1-Click SEO Pro 연동 준비

- Analytics 탭 추가 (User Journey Analytics 연동)
- 플러그인 목록 페이지 크로스 프로모션 링크

---

## 파일 변경 내역

### 신규 생성 파일

```
acf-user-journey-analytics/
├── acf-user-journey-analytics.php          # 메인 플러그인 파일
├── includes/
│   ├── class-database.php                  # DB 스키마 및 관리
│   ├── class-traffic-tracker.php           # 트래픽 추적
│   ├── class-traffic-reporter.php          # 보고서 생성
│   └── class-traffic-source-definitions.php # 광고 플랫폼 정의
├── admin/
│   ├── class-admin.php                     # 관리자 페이지
│   └── views/
│       ├── dashboard.php                   # 대시보드
│       ├── sources.php                     # 소스 분석
│       ├── funnel.php                      # 퍼널 분석
│       ├── roi.php                         # ROI 분석
│       ├── settings.php                    # 설정
│       └── dashboard-widget.php            # 위젯
└── assets/
    ├── css/admin.css                       # 관리자 스타일
    ├── js/admin.js                         # 관리자 스크립트
    └── js/tracker.js                       # 프론트엔드 추적
```

### 수정된 파일

```
acf-nudge-flow/
├── acf-nudge-flow.php                      # v22.10.0 버전 업데이트
├── admin/
│   ├── class-admin.php                     # 메뉴 변경 (트래픽 연동)
│   └── views/
│       └── traffic-analytics-integration.php # 연동 페이지 (신규)
```

---

## 업그레이드 가이드

### Nudge Flow 사용자

1. Nudge Flow를 v22.10.0으로 업데이트
2. **ACF User Journey Analytics** 플러그인 설치 (무료)
3. "트래픽 연동" 메뉴에서 연동 상태 확인

### 신규 사용자

1. ACF User Journey Analytics 설치 (무료)
2. 원하는 유료 플러그인 설치 (Nudge Flow, 1-Click SEO 등)
3. 각 플러그인에서 Analytics 연동 메뉴 확인

---

## API 사용법

### 트래픽 데이터 조회

```php
// User Journey Analytics가 활성화되어 있는지 확인
if ( class_exists( 'ACF_User_Journey_Analytics' ) ) {

    // 트래픽 요약 데이터
    $summary = ACF_User_Journey_Analytics::get_traffic_data(
        '2026-01-01',
        '2026-01-05',
        'summary'
    );

    // 트래픽 소스별 데이터
    $sources = ACF_User_Journey_Analytics::get_traffic_data(
        '2026-01-01',
        '2026-01-05',
        'sources'
    );

    // 퍼널 분석 데이터
    $funnel = ACF_User_Journey_Analytics::get_traffic_data(
        '2026-01-01',
        '2026-01-05',
        'funnel'
    );

    // ROI 분석 데이터
    $roi = ACF_User_Journey_Analytics::get_traffic_data(
        '2026-01-01',
        '2026-01-05',
        'roi'
    );
}
```

---

## 알려진 이슈

1. **SEO 플러그인 개발 중**
   - WP 1-Click SEO Pro의 핵심 기능은 아직 개발 중
   - Analytics 연동 페이지만 선행 구현

2. **퍼널 분석 정확도**
   - JavaScript 기반 추적으로 일부 사용자 이탈 감지 누락 가능
   - 서버 사이드 보완 예정

---

## 다음 릴리즈 예고 (Phase 43)

- SEO 플러그인 핵심 기능 개발
- User Journey Analytics WordPress.org 등록
- 통합 테스트 및 성능 최적화

---

**개발팀**: 3J Labs (제이x제니x제이슨 연구소)
**문의**: https://3j-labs.com/support

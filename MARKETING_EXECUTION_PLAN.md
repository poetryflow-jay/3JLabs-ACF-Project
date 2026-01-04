# 3J Labs ACF CSS - 마케팅 실행 계획서

**작성일**: 2026년 1월 4일
**버전**: 1.0
**실행 기간**: 2026년 Q1~Q2

---

## 1. Executive Summary

### 목표
- **6개월 내 MRR $8,000 달성**
- **Free 버전 2,000+ 설치**
- **Pro 전환율 8%**

### 핵심 전략
1. **Product-Led Growth (PLG)**: 제품 자체가 마케팅 도구
2. **Organic First**: SEO, 콘텐츠, 커뮤니티 중심
3. **Viral Mechanics**: 레퍼럴, 공유 기능 내장

---

## 2. Week 1-2: Foundation

### 2.1 WordPress.org 제출 준비

**Free 버전 기능 구성:**
```
✅ 포함:
- 기본 색상 팔레트 관리 (5개)
- 폰트 선택 (Google Fonts 무료)
- 3개 디자인 프리셋
- 기본 CSS 변수 출력
- 라이트 모드 지원

❌ Pro 전용:
- 무제한 색상 팔레트
- AI 색상 추천
- 팀 협업
- 디자인 시스템 내보내기
- 프리미엄 프리셋 (10개+)
- 다크 모드
- 우선 지원
```

**제출 체크리스트:**
- [ ] readme.txt 작성 (WordPress 형식)
- [ ] 스크린샷 5장
- [ ] 배너 이미지 (1544x500, 772x250)
- [ ] 아이콘 (256x256)
- [ ] GPL 라이센스 확인
- [ ] 모든 외부 리소스 로컬화

### 2.2 웹사이트 준비

**랜딩 페이지 구조:**
```
1. Hero Section
   - 헤드라인: "WordPress 디자인 시스템, 5분 만에 완성"
   - 서브: "색상, 폰트, 버튼을 한 곳에서. 200+ 사이트가 신뢰합니다."
   - CTA: "무료로 시작하기"

2. 문제 제시
   - "매번 같은 CSS 작업을 반복하고 계신가요?"
   - 3가지 페인 포인트 나열

3. 솔루션
   - 제품 데모 GIF
   - 핵심 기능 3가지

4. 사회적 증거
   - 사용자 후기
   - 로고 배열

5. 요금제
   - Free vs Pro 비교표

6. FAQ

7. 최종 CTA
```

---

## 3. Week 3-4: Content Marketing

### 3.1 블로그 콘텐츠 계획

| 주차 | 제목 | 타입 | 타겟 키워드 |
|------|------|------|-------------|
| W3 | "WordPress 디자인 시스템 구축 완벽 가이드" | 에버그린 | wordpress design system |
| W3 | "WooCommerce 상품 페이지 스타일링 10분 컷" | 튜토리얼 | woocommerce styling |
| W4 | "3J Labs vs Elementor: 스타일 관리 비교" | 비교 | elementor alternative |
| W4 | "무료 WordPress CSS 플러그인 TOP 5" | 리스트 | free css plugin wordpress |

### 3.2 YouTube 콘텐츠

**시리즈: "5분 퀵스타트"**
1. 설치 및 첫 설정
2. 색상 팔레트 만들기
3. 타이포그래피 설정
4. 버튼 스타일 통일
5. 디자인 시스템 내보내기

**제작 포맷:**
- 길이: 5분 이내
- 스크린 레코딩 + 음성
- 한글 자막 필수
- 영어 자막 옵션

### 3.3 Case Study

**타겟 케이스:**
1. 프리랜서: "혼자서 월 10개 사이트 납품하는 방법"
2. 에이전시: "디자인 가이드라인 공유로 협업 시간 50% 단축"
3. 쇼핑몰: "WooCommerce 스타일 통일로 전환율 20% 상승"

---

## 4. Week 5-8: Community & Organic Growth

### 4.1 커뮤니티 활동

**Reddit:**
- r/Wordpress (2.5M members)
- r/webdev
- r/WooCommerce

**Facebook Groups:**
- WordPress 한국 사용자 모임
- WooCommerce Korea
- 웹디자인/개발 커뮤니티

**활동 전략:**
```
1. 가치 먼저 제공 (답변, 팁 공유)
2. 자연스러운 제품 언급 (해결책으로)
3. 직접 홍보는 월 1-2회 제한
4. AMA (Ask Me Anything) 개최
```

### 4.2 SEO 최적화

**온페이지 SEO:**
- 모든 페이지 메타 태그 최적화
- 구조화 데이터 (Schema.org)
- 내부 링크 구조

**키워드 클러스터:**
```
[메인] wordpress css management
    ├── wordpress design system plugin
    ├── wordpress color palette manager
    ├── wordpress typography plugin
    └── woocommerce styling plugin

[롱테일]
    ├── how to create design system wordpress
    ├── best css variables plugin wordpress
    └── wordpress global styles plugin
```

---

## 5. Month 2-3: Referral & Viral

### 5.1 레퍼럴 시스템 구현

**기술 구현:**
```php
// 레퍼럴 코드 생성
function generate_referral_code($user_id) {
    return 'JJ-' . strtoupper(substr(md5($user_id . time()), 0, 8));
}

// 레퍼럴 추적
function track_referral($code, $new_user_id) {
    // 추천인 찾기
    // 크레딧 적용
    // 이메일 알림
}
```

**레퍼럴 보상 구조:**
| 액션 | 추천인 | 피추천인 |
|------|--------|----------|
| 무료 가입 | 100 포인트 | 50 포인트 |
| Pro 결제 | 20% 수익 (1년) | 10% 할인 |
| 연간 결제 | $50 크레딧 | 1개월 무료 |

### 5.2 바이럴 기능 구현

**"Share My Design" 기능:**
```javascript
// 공유 가능한 스타일 가이드 생성
function generateShareableGuide() {
    const styles = getCurrentStyles();
    const shareUrl = `https://3j-labs.com/shared/${generateHash(styles)}`;

    return {
        url: shareUrl,
        preview: generatePreviewImage(styles),
        embedCode: `<iframe src="${shareUrl}" />`
    };
}
```

**소셜 공유 통합:**
- Twitter/X: "내 WordPress 디자인 시스템 확인하기"
- LinkedIn: 전문가용 프레이밍
- Facebook: 시각적 프리뷰 강조

### 5.3 이메일 마케팅

**자동화 시퀀스:**

**1. 웰컴 시리즈 (7일)**
```
Day 0: 환영 + 퀵스타트 가이드
Day 1: 첫 번째 팔레트 만들기 튜토리얼
Day 3: Pro 기능 미리보기
Day 5: 성공 사례 공유
Day 7: 특별 할인 제안
```

**2. 인게이지먼트 리마케팅**
```
비활성 7일: "무엇이 막히셨나요?"
비활성 14일: "새로운 기능 업데이트"
비활성 30일: "다시 시작하기 50% 할인"
```

**3. 업셀 시퀀스**
```
Free 사용 30일: "Pro 기능 체험 초대"
기능 제한 도달: "더 많은 팔레트가 필요하신가요?"
팀원 초대 시도: "Team 플랜으로 함께하세요"
```

---

## 6. Month 3-6: Scale & Partnership

### 6.1 Affiliate 프로그램

**조건:**
- 기본 수익률: 30%
- 쿠키 기간: 60일
- 최소 지급: $50

**모집 대상:**
1. WordPress 블로거/유튜버
2. 웹 개발 교육 플랫폼
3. 테마/플러그인 리뷰 사이트
4. 프리랜서 마켓플레이스

**제공 자료:**
- 배너 세트 (다양한 사이즈)
- 이메일 템플릿
- 소셜 미디어 이미지
- 제품 비디오 소스

### 6.2 파트너십

**호스팅 업체:**
- 클라우드웨이즈, 사이트그라운드, WP Engine
- 번들 제안: 호스팅 가입 시 Pro 3개월 무료

**테마 개발사:**
- 인기 테마와 호환성 인증
- 공동 마케팅

**에이전시:**
- 볼륨 라이센스 할인
- 화이트라벨 옵션

### 6.3 PR & 미디어

**타겟 매체:**
- WPBeginner
- Elegant Themes Blog
- WooCommerce Blog
- CSS-Tricks
- Smashing Magazine

**PR 전략:**
- 제품 출시 보도자료
- 업데이트/기능 추가 시 기사
- 전문가 기고

---

## 7. Budget Allocation

### 월간 예산: $2,000

| 항목 | 금액 | 비중 |
|------|------|------|
| 콘텐츠 제작 | $600 | 30% |
| 유료 광고 (테스트) | $400 | 20% |
| 도구/인프라 | $300 | 15% |
| Affiliate 지급 | $400 | 20% |
| 기타 | $300 | 15% |

### ROI 목표

```
투자: $12,000 (6개월)
목표 MRR: $8,000
목표 고객 수: 100 (Pro)
LTV (예상): $150

예상 수익: $8,000 × 6 = $48,000
ROI: 300%
```

---

## 8. Metrics & Dashboard

### 8.1 핵심 지표 (North Star)

**Primary:**
- MRR (Monthly Recurring Revenue)

**Secondary:**
- Free 설치 수
- Free → Pro 전환율
- K-factor (바이럴 계수)

### 8.2 Weekly Tracking

| 지표 | W1 | W2 | W3 | W4 | Goal |
|------|----|----|----|----|------|
| 신규 설치 | | | | | 50/주 |
| 활성 사용자 | | | | | 30% |
| Pro 전환 | | | | | 2/주 |
| 레퍼럴 | | | | | 5/주 |
| NPS | | | | | 40+ |

### 8.3 도구

- **분석**: Google Analytics 4, Mixpanel
- **이메일**: Mailchimp 또는 ConvertKit
- **고객 지원**: Crisp 또는 Intercom
- **레퍼럴**: ReferralCandy 또는 커스텀
- **A/B 테스트**: Google Optimize

---

## 9. Risk Mitigation

| 리스크 | 가능성 | 영향 | 대응 |
|--------|--------|------|------|
| WordPress.org 거절 | 중 | 높음 | 가이드라인 철저 준수, 피드백 즉시 반영 |
| 경쟁사 가격 인하 | 높음 | 중 | 기능 차별화, 니치 집중 |
| 낮은 전환율 | 중 | 높음 | A/B 테스트, 온보딩 개선 |
| 기술 이슈 | 낮음 | 높음 | 모니터링, 빠른 대응 체계 |

---

## 10. Execution Timeline

```
2026년 1월
├── W1-2: WordPress.org 제출, 랜딩 페이지
└── W3-4: 콘텐츠 마케팅 시작

2026년 2월
├── W5-6: 커뮤니티 활동, SEO
└── W7-8: 레퍼럴 시스템 런칭

2026년 3월
├── W9-10: 이메일 자동화
└── W11-12: Affiliate 프로그램

2026년 4-6월
├── 파트너십 확대
├── 유료 광고 테스트
└── 스케일 업
```

---

**© 2026 3J Labs. All rights reserved.**

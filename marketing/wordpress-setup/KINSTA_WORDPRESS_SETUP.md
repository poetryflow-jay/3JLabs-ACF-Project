# 🚀 ACF CSS Manager - Kinsta WordPress 런칭 가이드

이 가이드는 Kinsta 호스팅에 이미 설치된 WordPress + WooCommerce 사이트에서 ACF CSS Manager 판매 사이트를 구축하는 과정을 안내합니다.

## 📋 사전 요구사항

- ✅ Kinsta 호스팅 계정
- ✅ WordPress 사이트 (4개 중 1개 선택)
- ✅ WooCommerce 설치 및 활성화
- ✅ Kadence / Flavor / GeneratePress 테마
- ✅ SSL 인증서 (Kinsta 자동 제공)

---

## 🎯 Phase 1: 사이트 선택 및 기본 설정 (10분)

### 1.1 사이트 선택

4개의 대기 중인 WordPress 사이트 중 ACF CSS Manager 판매용으로 사용할 사이트를 선택합니다.

**권장 도메인 예시:**
- `acf-css.com`
- `acfcss.io`
- `j-j-labs.com`

### 1.2 Kinsta 대시보드 설정

1. [Kinsta 대시보드](https://my.kinsta.com) 로그인
2. 선택한 사이트 클릭
3. **도구** 탭에서 다음 확인:
   - ✅ PHP 버전: 8.0 이상
   - ✅ SSL/TLS: 활성화
   - ✅ 캐시: 활성화

### 1.3 필수 플러그인 설치

WordPress 관리자 > 플러그인 > 새로 추가:

| 플러그인 | 용도 | 필수 여부 |
|---------|------|----------|
| WooCommerce | 결제 시스템 | ✅ 필수 |
| Kadence Blocks | 페이지 빌더 | ✅ 권장 |
| WP Mail SMTP | 이메일 발송 | ✅ 권장 |
| Wordfence | 보안 | ✅ 권장 |
| UpdraftPlus | 백업 | ✅ 권장 |

---

## 🎨 Phase 2: 테마 및 랜딩 페이지 설정 (30분)

### 2.1 자식 테마 설치 (선택사항)

제공된 `acf-css-landing` 자식 테마를 사용하려면:

1. `marketing/wordpress-theme/acf-css-landing` 폴더를 ZIP으로 압축
2. WordPress 관리자 > 외모 > 테마 > 새로 추가 > 테마 업로드
3. 자식 테마 활성화

**또는 Kadence 테마 직접 커스터마이징:**

### 2.2 랜딩 페이지 생성 (Kadence Blocks 사용)

1. **페이지 > 새로 추가**
2. 제목: "ACF CSS Manager"
3. 페이지 속성:
   - 템플릿: Full Width / No Sidebar
   - 페이지 타이틀: 숨김
   
4. **Kadence Blocks로 섹션 구성:**

#### Hero 섹션
```
[Kadence Row Layout]
├── Background: 다크 그라디언트 (#0f172a)
├── Min Height: 100vh
└── Content:
    ├── [Badge] "🎉 v6.2.1 출시 — AI 스타일 생성"
    ├── [Heading] "코딩 없이 전문가 수준의 웹사이트 디자인"
    ├── [Paragraph] 서브 텍스트
    └── [Buttons] CTA 버튼 2개
```

#### Features 섹션
```
[Kadence Row Layout]
├── 3-Column Grid
└── 6개 Feature Card:
    ├── 🎨 색상 팔레트 관리
    ├── 🔤 타이포그래피 시스템
    ├── 🤖 AI 스타일 생성
    ├── ☁️ 클라우드 동기화
    ├── ⚡ 성능 최적화
    └── 🏢 에이전시 모드
```

#### Pricing 섹션
```
[Kadence Row Layout]
├── 3-Column Grid
└── [WooCommerce Products] 또는 수동 가격 카드
```

### 2.3 CSS 커스터마이징

**외모 > 커스터마이즈 > 추가 CSS:**

```css
/* ACF CSS Landing 다크 테마 */
body.page-template-full-width {
    background: #0f172a;
    color: #ffffff;
}

/* Hero 섹션 */
.acf-hero {
    background: radial-gradient(ellipse at 20% 50%, rgba(37, 99, 235, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 50%, rgba(6, 182, 212, 0.1) 0%, transparent 50%),
                #0f172a;
}

/* 버튼 스타일 */
.wp-block-button__link {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border-radius: 12px;
    padding: 1rem 2rem;
    font-weight: 600;
    transition: all 0.3s;
}

.wp-block-button__link:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(37, 99, 235, 0.5);
}
```

---

## 🛒 Phase 3: WooCommerce 상품 설정 (20분)

### 3.1 상품 생성

**상품 > 새로 추가**

#### Free 에디션

| 필드 | 값 |
|-----|-----|
| 상품명 | ACF CSS Manager - Free |
| SKU | acf-css-free |
| 가격 | 0 (무료 다운로드) |
| 가상 상품 | ✅ 체크 |
| 다운로드 가능 | ✅ 체크 |
| 다운로드 파일 | `acf-css-free-v6.2.1.zip` 업로드 |

**ACF CSS 라이센스 설정 (플러그인 설치 후):**
- ACF CSS 라이센스 상품: ✅
- 에디션: Free
- 라이센스 기간: 0 (영구)
- 사이트 수 제한: 1

#### PRO 에디션 (Basic/Premium/Unlimited)

| 필드 | 값 |
|-----|-----|
| 상품명 | ACF CSS Manager PRO |
| SKU | acf-css-pro |
| 가격 | $49 |
| 정기 결제 | 1년 (선택사항) |
| ACF CSS 라이센스 | ✅ |
| 에디션 | Premium |
| 라이센스 기간 | 365일 |
| 사이트 수 제한 | 0 (무제한) |

#### Partner 에디션

| 필드 | 값 |
|-----|-----|
| 상품명 | ACF CSS Manager Partner |
| SKU | acf-css-partner |
| 가격 | $199 |
| ACF CSS 라이센스 | ✅ |
| 에디션 | Partner |
| 라이센스 기간 | 365일 |
| 사이트 수 제한 | 0 (무제한) |

### 3.2 결제 게이트웨이 설정

**WooCommerce > 설정 > 결제:**

1. **Stripe** (권장)
   - Stripe 계정 연결
   - 신용카드, Apple Pay, Google Pay 활성화

2. **PayPal**
   - PayPal Business 계정 연결
   - PayPal Checkout 활성화

### 3.3 이메일 설정

**WooCommerce > 설정 > 이메일:**

- 주문 완료 이메일 템플릿 커스터마이징
- 라이센스 키 안내 문구 추가

---

## 🔗 Phase 4: Neural Link 연동 (30분)

### 4.1 Neural Link 서버 설치

**별도의 WordPress 사이트** (또는 동일 사이트의 서브도메인)에 Neural Link 설치:

1. `acf-css-neural-link-v3.1.0.zip` 업로드 및 활성화
2. **설정 > ACF Neural Link**에서 API Key 생성
3. API Key 복사

### 4.2 WooCommerce License Bridge 설치

판매 사이트에 설치:

1. `marketing/wordpress-plugins/acf-css-woo-license/` 폴더를 ZIP으로 압축
2. **플러그인 > 새로 추가 > 플러그인 업로드**
3. **WooCommerce > ACF CSS 라이센스**에서 설정:
   - Neural Link URL: `https://your-neural-link-server.com`
   - API Key: (복사한 키 입력)
4. **연결 테스트** 버튼 클릭

### 4.3 연동 테스트

1. 테스트 주문 생성 (PRO 상품 구매)
2. 주문 상태를 "완료"로 변경
3. 확인 사항:
   - ✅ 라이센스 키가 주문 상세에 표시
   - ✅ 이메일로 라이센스 키 발송
   - ✅ Neural Link 서버에 라이센스 기록

---

## 📄 Phase 5: 추가 페이지 설정 (20분)

### 5.1 필수 페이지

| 페이지 | 설명 |
|--------|------|
| `/shop/` | WooCommerce 상품 목록 |
| `/cart/` | 장바구니 |
| `/checkout/` | 결제 |
| `/my-account/` | 마이 페이지 |
| `/docs/` | 사용자 문서 |
| `/support/` | 지원 문의 |
| `/privacy-policy/` | 개인정보처리방침 |
| `/terms/` | 이용약관 |

### 5.2 베타 신청 폼 설정

1. **Formspree** 또는 **Contact Form 7** 사용
2. `/beta/` 페이지 생성
3. 폼 필드: 이름, 이메일, 관심 에디션

---

## 🔒 Phase 6: 보안 및 성능 최적화 (15분)

### 6.1 보안 설정

**Wordfence 설정:**
- 방화벽 활성화
- 브루트포스 방지 활성화
- 2FA 설정 (관리자)

**추가 보안:**
```php
// wp-config.php에 추가
define('DISALLOW_FILE_EDIT', true);
define('WP_DEBUG', false);
```

### 6.2 성능 최적화

**Kinsta 캐시:**
- 페이지 캐시 활성화
- CDN 활성화

**추가 최적화:**
- 이미지 최적화 (Imagify, ShortPixel)
- Lazy Loading 활성화

---

## 🚀 Phase 7: 런칭 체크리스트

### 런칭 전 확인

- [ ] 모든 상품 가격 및 설명 확인
- [ ] 결제 테스트 완료
- [ ] 라이센스 발행 테스트 완료
- [ ] 이메일 발송 테스트 완료
- [ ] 모바일 반응형 확인
- [ ] SEO 메타 태그 설정
- [ ] Google Analytics 연동
- [ ] 법적 페이지 (약관, 개인정보) 확인

### 런칭 후 모니터링

- [ ] Kinsta Analytics 모니터링
- [ ] WooCommerce 주문 상태 확인
- [ ] Neural Link 로그 확인
- [ ] 고객 문의 대응 체계 구축

---

## 🛠️ 문제 해결

### 라이센스 발행 실패

1. Neural Link URL 확인
2. API Key 유효성 확인
3. SSL 인증서 확인
4. 서버 방화벽 확인

### 이메일 발송 실패

1. WP Mail SMTP 설정 확인
2. SMTP 자격 증명 확인
3. 이메일 로그 확인

### 결제 오류

1. Stripe/PayPal 계정 상태 확인
2. 테스트 모드 비활성화 확인
3. SSL 인증서 확인

---

## 📞 지원

- 기술 문서: `/docs/`
- 이메일: support@j-j-labs.com
- GitHub: https://github.com/j-j-labs/acf-css-manager

---

*마지막 업데이트: 2025년 12월 19일*


# Phase 40 - 통합 전략 문서 (CEO-CTO 협의 버전)

**작성일**: 2026년 1월 4일
**협의자**: Jay (CEO) & Jason (CTO)
**상태**: 최종 확정

---

## 📊 Executive Summary (통합)

### 현재 상태 종합
| 항목 | 상태 | 세부 |
|------|------|------|
| **플러그인** | 11개 | 모두 안정 작동 |
| **완성도** | 80-95% | 주요 플러그인 85%+ |
| **보안** | ✅ 강화 | Phase 37, 39.3 완료 |
| **UI/UX** | ✅ 개선 | UI System 2026 적용 |
| **신규** | 2개 | Analytics, Marketing Dashboard |

### 핵심 메트릭 목표

| 기간 | 설치 수 | Pro 전환 | MRR | K-Factor |
|------|---------|----------|-----|----------|
| 3개월 | 750 | 45 (6%) | $2,500 | 0.8 |
| 6개월 | 2,500 | 150 (8%) | $8,000 | 1.2 |
| 12개월 | 10,000 | 800 (10%) | $50,000 | 1.5 |

---

## 1. 요금제 구조 (절충안)

### 1.1 최종 확정 요금제

기존 두 제안을 분석한 결과, 다음 구조로 확정:

| 플랜 | 월간 | 연간 | 사이트 | 핵심 기능 |
|------|------|------|--------|-----------|
| **Free** | $0 | $0 | 1 | 기본 기능, 5개 팔레트 |
| **Pro Starter** | $9 | **$79** | 1 | 무제한 팔레트, AI 추천 |
| **Pro Business** | $19 | **$149** | 5 | 팀 협업 3명, 우선 지원 |
| **Agency** | $39 | **$299** | 25 | 화이트라벨, API, 클라이언트 대시보드 |
| **Lifetime** | - | **$799** | ∞ | 모든 기능 영구, 평생 업데이트 |

**차별화 포인트:**
- Jason 제안 ($29/$79/$199) vs Mikael 제안 ($79/$149/$299) → 절충안
- Lifetime $999 → $799로 조정 (더 매력적인 가격점)
- 연간 결제 17% 할인 유지

### 1.2 가격 전략

#### 앵커링 순서
```
[화면 좌→우]
Lifetime ($799) → Agency ($39/월) → Business ($19/월) → Starter ($9/월) → Free

효과:
- $799가 앵커 역할 → $19/월이 매우 합리적으로 느껴짐
- Business 플랜에 "MOST POPULAR" 배지
```

#### 업셀 트리거
```
Free → Starter: 6번째 팔레트 생성 시도
Starter → Business: 팀원 초대 시도
Business → Agency: 화이트라벨 문의
Any → Lifetime: 연간 2회 결제 시
```

---

## 2. 바이럴 루프 구조 (강화 버전)

### 2.1 레퍼럴 인센티브 매트릭스

| 레벨 | 추천인 보상 | 피추천인 보상 | 누적 효과 |
|------|-------------|---------------|-----------|
| **Level 1** | Pro 30일 무료 | 20% 할인 | - |
| **Level 2** | Pro 60일 무료 | 30% 할인 | 90일 누적 |
| **Level 3** | Pro 90일 무료 | 40% 할인 | 180일 누적 |
| **Level 3+** | 연간 결제 50% 할인 | Lifetime 20% 할인 | 무제한 |

#### 레퍼럴 트리 시스템
```
나 (추천인)
├── 친구 A (Level 1) → 30일 무료
│   ├── 친구 A-1 → 나에게 +10일
│   └── 친구 A-2 → 나에게 +10일
├── 친구 B (Level 2) → 60일 무료
└── 친구 C (Level 3) → 90일 무료

총 혜택: 30 + 60 + 90 + 20 = 200일 무료
```

### 2.2 제품 내 바이럴 포인트 (7가지)

| # | 트리거 | 액션 | 인센티브 |
|---|--------|------|----------|
| 1 | 첫 팔레트 생성 | 공유 팝업 | 포인트 100 |
| 2 | 디자인 점수 80+ | 배지 획득 | 소셜 공유 |
| 3 | 팀원 초대 | 이메일 발송 | 양쪽 1개월 무료 |
| 4 | 기능 제한 도달 | 레퍼럴 제안 | 기능 잠금 해제 |
| 5 | Pro 결제 완료 | 공유 프롬프트 | 다음 결제 10% |
| 6 | 100일 연속 사용 | 성과 공유 | 특별 배지 |
| 7 | 템플릿 업로드 | 마켓 등록 | 판매 수익 70% |

### 2.3 게이미피케이션 시스템

#### 레벨 시스템
```
Level 1 (Newbie):     0-100 XP    → 기본 배지
Level 2 (Designer):   100-500 XP  → 추가 프리셋 2개
Level 3 (Expert):     500-1000 XP → 템플릿 업로드 권한
Level 4 (Master):     1000-2500 XP → 베타 기능 액세스
Level 5 (Legend):     2500+ XP    → 공식 파트너 자격
```

#### XP 획득 방법
| 액션 | XP |
|------|-----|
| 일일 로그인 | 5 |
| 팔레트 생성 | 10 |
| 스타일 적용 | 20 |
| 친구 초대 | 100 |
| Pro 업그레이드 | 200 |
| 템플릿 공유 | 50 |
| 리뷰 작성 | 150 |

#### 리더보드
- **월간 Top 10**: 특별 배지 + 1개월 무료
- **분기별 챔피언**: 3J Labs 인터뷰 + 블로그 피처

---

## 3. 마케팅 실행 전략 (통합)

### 3.1 Phase별 실행 계획

#### Phase 40.1 (이번 주) - Foundation
| 날짜 | 작업 | 담당 | 상태 |
|------|------|------|------|
| Day 1 | WordPress.org Free 버전 준비 | Jason | ⬜ |
| Day 2 | 랜딩 페이지 업데이트 | Jenny | ⬜ |
| Day 3 | 레퍼럴 DB 스키마 설계 | Jason | ⬜ |
| Day 4 | 레퍼럴 UI 디자인 | Jenny | ⬜ |
| Day 5 | 레퍼럴 시스템 구현 | Jason | ⬜ |
| Day 6-7 | 테스트 및 배포 | Team | ⬜ |

#### Phase 40.2 (2주차) - 콘텐츠 & SEO
| 작업 | 세부 | 담당 |
|------|------|------|
| 블로그 3개 | 디자인 시스템, WooCommerce, 비교 | Jay |
| YouTube 3개 | 퀵스타트, 색상, 타이포그래피 | Jenny |
| SEO 최적화 | 메타태그, 스키마, 내부링크 | Jason |

#### Phase 40.3 (3-4주차) - 커뮤니티 & 레퍼럴
| 작업 | 목표 |
|------|------|
| Reddit 참여 | 주 3회 가치 포스팅 |
| Facebook 그룹 | 한국/글로벌 그룹 5개 |
| 레퍼럴 런칭 | 첫 50명 Early Adopter |

### 3.2 채널별 전략

#### 오가닉 (Organic)
```
[WordPress.org]
- Free 버전 등록 (2주 내)
- 목표: 100+ 설치, 4.5+ 평점

[SEO]
- 메인 키워드: "wordpress design system plugin"
- 롱테일: "how to create color palette wordpress"
- 목표: 3개월 내 Page 1

[콘텐츠]
- 블로그: 주 2회
- YouTube: 주 1회
- 케이스 스터디: 월 2회
```

#### 레퍼럴 (Referral)
```
[개인 추천]
- 인센티브: 양쪽 혜택
- 목표: K-Factor 1.2

[파트너 프로그램]
- 커미션: 30% (첫해)
- 대상: 에이전시, 프리랜서

[어필리에이트]
- 커미션: 25%
- 쿠키: 60일
- 대상: 블로거, 유튜버
```

#### 소셜 미디어
```
[LinkedIn]
- 대상: B2B, 에이전시
- 콘텐츠: 케이스 스터디, 팁
- 빈도: 주 3회

[Twitter/X]
- 대상: 개발자, 디자이너
- 콘텐츠: 기능 업데이트, 팁
- 빈도: 일 1-2회

[Facebook]
- 대상: 한국 사용자
- 콘텐츠: 튜토리얼, Q&A
- 빈도: 주 2-3회
```

---

## 4. 미완료 항목 우선순위 (재정립)

### 🔴 Tier 1 - 이번 주 (Critical)

| # | 항목 | 예상 시간 | 담당 |
|---|------|----------|------|
| 1 | WordPress.org Free 준비 | 8h | Jason |
| 2 | 레퍼럴 시스템 구현 | 12h | Jason |
| 3 | 랜딩 페이지 업데이트 | 6h | Jenny |

### 🟠 Tier 2 - 다음 주 (High)

| # | 항목 | 예상 시간 | 담당 |
|---|------|----------|------|
| 4 | 공유 기능 UI 구현 | 8h | Jenny |
| 5 | 게이미피케이션 기본 구조 | 6h | Jason |
| 6 | Analytics Dashboard 데이터 연동 | 8h | Jason |

### 🟡 Tier 3 - 이번 달 (Medium)

| # | 항목 | 예상 시간 | 담당 |
|---|------|----------|------|
| 7 | 통합 테스트 시나리오 | 10h | Jason |
| 8 | 콘텐츠 제작 (블로그 6개) | 12h | Jay |
| 9 | 커뮤니티 활동 시작 | 지속 | Team |

### 🟢 Tier 4 - 다음 달 (Low)

| # | 항목 | 예상 시간 | 담당 |
|---|------|----------|------|
| 10 | Figma 플러그인 개발 | 40h | Jason |
| 11 | Marketing Dashboard 완성 | 20h | Jason |
| 12 | 다국어 번역 동기화 | 10h | Jenny |

---

## 5. 기술 구현 상세

### 5.1 레퍼럴 추적 시스템 DB 스키마

```sql
-- 레퍼럴 코드 테이블
CREATE TABLE jj_referral_codes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id)
);

-- 레퍼럴 추적 테이블
CREATE TABLE jj_referral_tracking (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    referrer_id BIGINT NOT NULL,
    referred_id BIGINT,
    code VARCHAR(20) NOT NULL,
    status ENUM('clicked', 'signed_up', 'converted') DEFAULT 'clicked',
    clicked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    signed_up_at DATETIME,
    converted_at DATETIME,
    purchase_amount DECIMAL(10,2),
    commission_amount DECIMAL(10,2),
    INDEX idx_referrer (referrer_id),
    INDEX idx_code (code)
);

-- 레퍼럴 보상 테이블
CREATE TABLE jj_referral_rewards (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    type ENUM('free_days', 'discount', 'credit', 'commission') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    source_referral_id BIGINT,
    status ENUM('pending', 'applied', 'expired') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME,
    applied_at DATETIME,
    INDEX idx_user_id (user_id)
);

-- 게이미피케이션 테이블
CREATE TABLE jj_user_achievements (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    xp_points INT DEFAULT 0,
    level INT DEFAULT 1,
    badges JSON,
    streak_days INT DEFAULT 0,
    last_active DATETIME,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user (user_id)
);
```

### 5.2 레퍼럴 시스템 핵심 로직

```php
class JJ_Referral_System {

    // 레퍼럴 코드 생성
    public static function generate_code($user_id) {
        $prefix = 'JJ';
        $hash = strtoupper(substr(md5($user_id . wp_salt()), 0, 6));
        return $prefix . $hash;
    }

    // 레퍼럴 추적
    public static function track_click($code) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_tracking';

        // 방문 기록
        $wpdb->insert($table, [
            'code' => $code,
            'referrer_id' => self::get_referrer_by_code($code),
            'status' => 'clicked',
            'clicked_at' => current_time('mysql')
        ]);

        // 쿠키 설정 (30일)
        setcookie('jj_ref', $code, time() + (30 * DAY_IN_SECONDS), '/');

        return true;
    }

    // 전환 처리
    public static function process_conversion($user_id, $purchase_amount = 0) {
        $code = $_COOKIE['jj_ref'] ?? '';
        if (!$code) return false;

        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_tracking';

        // 레코드 업데이트
        $wpdb->update($table, [
            'referred_id' => $user_id,
            'status' => $purchase_amount > 0 ? 'converted' : 'signed_up',
            'signed_up_at' => current_time('mysql'),
            'converted_at' => $purchase_amount > 0 ? current_time('mysql') : null,
            'purchase_amount' => $purchase_amount,
            'commission_amount' => $purchase_amount * 0.20
        ], ['code' => $code, 'referred_id' => null]);

        // 보상 지급
        $referrer_id = self::get_referrer_by_code($code);
        self::grant_reward($referrer_id, $user_id, $purchase_amount);

        // 쿠키 삭제
        setcookie('jj_ref', '', time() - 3600, '/');

        return true;
    }

    // 보상 지급
    private static function grant_reward($referrer_id, $referred_id, $amount) {
        global $wpdb;
        $rewards_table = $wpdb->prefix . 'jj_referral_rewards';

        // 추천인 레벨 계산
        $level = self::get_referrer_level($referrer_id);

        // 레벨별 무료 일수
        $free_days_map = [1 => 30, 2 => 60, 3 => 90];
        $free_days = $free_days_map[$level] ?? 90;

        if ($amount > 0) {
            // 유료 전환: 커미션 + 무료 일수
            $wpdb->insert($rewards_table, [
                'user_id' => $referrer_id,
                'type' => 'commission',
                'value' => $amount * 0.20,
                'status' => 'pending'
            ]);
        }

        // 무료 일수 지급
        $wpdb->insert($rewards_table, [
            'user_id' => $referrer_id,
            'type' => 'free_days',
            'value' => $free_days,
            'status' => 'pending',
            'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days'))
        ]);

        // 피추천인 할인
        $discount_map = [1 => 20, 2 => 30, 3 => 40];
        $discount = $discount_map[$level] ?? 40;

        $wpdb->insert($rewards_table, [
            'user_id' => $referred_id,
            'type' => 'discount',
            'value' => $discount,
            'status' => 'pending',
            'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days'))
        ]);

        // 이메일 알림
        self::send_reward_notification($referrer_id, $referred_id);
    }
}
```

### 5.3 공유 기능 UI

```php
// 공유 팝업 렌더링
function render_share_popup($style_id) {
    $share_url = home_url('/share/' . $style_id);
    $style_name = get_the_title($style_id);

    ?>
    <div class="jj-share-modal" id="jj-share-modal">
        <div class="jj-share-modal-content">
            <span class="jj-close">&times;</span>

            <h2>🎨 디자인 시스템 공유하기</h2>

            <div class="jj-share-preview">
                <!-- 미리보기 이미지 -->
            </div>

            <div class="jj-share-url">
                <input type="text" value="<?php echo esc_url($share_url); ?>" readonly>
                <button class="jj-copy-btn">📋 복사</button>
            </div>

            <div class="jj-share-social">
                <button class="jj-share-twitter">
                    𝕏 Twitter
                </button>
                <button class="jj-share-linkedin">
                    💼 LinkedIn
                </button>
                <button class="jj-share-facebook">
                    👥 Facebook
                </button>
            </div>

            <div class="jj-share-embed">
                <h4>임베드 코드</h4>
                <textarea readonly>&lt;iframe src="<?php echo esc_url($share_url); ?>?embed=1" width="400" height="300"&gt;&lt;/iframe&gt;</textarea>
            </div>
        </div>
    </div>
    <?php
}
```

---

## 6. 성공 지표 (KPI)

### 6.1 주간 트래킹 대시보드

| 지표 | W1 목표 | W2 목표 | W3 목표 | W4 목표 |
|------|---------|---------|---------|---------|
| 신규 설치 | 30 | 50 | 75 | 100 |
| 활성 사용자 | 20 | 40 | 60 | 80 |
| 레퍼럴 클릭 | 10 | 30 | 50 | 80 |
| Pro 전환 | 2 | 4 | 6 | 10 |
| MRR | $150 | $350 | $600 | $900 |

### 6.2 월간 핵심 지표

| 월 | 누적 설치 | 활성 사용자 | Pro 사용자 | MRR | K-Factor |
|----|-----------|-------------|------------|-----|----------|
| M1 | 250 | 150 | 20 | $1,500 | 0.5 |
| M2 | 600 | 400 | 45 | $3,500 | 0.7 |
| M3 | 1,200 | 800 | 80 | $6,000 | 0.9 |
| M4 | 2,000 | 1,300 | 120 | $9,000 | 1.0 |
| M5 | 3,200 | 2,100 | 170 | $13,000 | 1.1 |
| M6 | 5,000 | 3,500 | 250 | $18,000 | 1.2 |

---

## 7. 리스크 관리

| 리스크 | 가능성 | 영향 | 대응 전략 |
|--------|--------|------|-----------|
| WordPress.org 거절 | 중 | 높음 | 가이드라인 철저 준수, 빠른 피드백 반영 |
| 경쟁사 가격 인하 | 높음 | 중 | 기능 차별화, 커뮤니티 강화 |
| 낮은 전환율 | 중 | 높음 | A/B 테스트, 온보딩 개선 |
| 레퍼럴 악용 | 중 | 중 | 중복 감지, IP 제한, 이메일 인증 |
| 서버 비용 증가 | 높음 | 중 | 효율 최적화, 캐싱, CDN |

---

## 8. 체크리스트 (오늘 완료 필요)

### Jay (CEO)
- [ ] 요금제 구조 최종 승인
- [ ] 마케팅 예산 확정
- [ ] 콘텐츠 제작 시작 (블로그 1개)

### Jason (CTO)
- [ ] 레퍼럴 DB 스키마 생성
- [ ] 레퍼럴 핵심 로직 구현
- [ ] WordPress.org 제출 파일 준비

### Jenny (UX)
- [ ] 공유 팝업 UI 디자인
- [ ] 레퍼럴 대시보드 와이어프레임
- [ ] 랜딩 페이지 업데이트 기획

---

## 9. 최종 결론

### 합의 사항
1. **요금제**: Free / $9 / $19 / $39 / $799(Lifetime)
2. **레퍼럴**: 3단계 보상 시스템 + 트리 구조
3. **바이럴**: 7개 트리거 포인트 + 게이미피케이션
4. **우선순위**: WordPress.org > 레퍼럴 > 콘텐츠

### 다음 리뷰
- **일시**: 2026년 1월 11일 (1주 후)
- **안건**: Phase 40.1 완료 검토, Phase 40.2 계획

---

**© 2026 3J Labs. All rights reserved.**

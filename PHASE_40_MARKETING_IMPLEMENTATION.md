# Phase 40 - 마케팅 및 바이럴 루프 구현 계획

**작성일**: 2026년 1월 4일  
**작성자**: Jason (CTO, 3J Labs)  
**목표**: 오늘 안에 모든 계획 수립 및 구현 시작

---

## 🎯 바이럴 루프 구현 상세 설계

### 1. 레퍼럴 추적 시스템 (Referral Tracking System)

#### 데이터베이스 구조
```sql
-- 레퍼럴 관계 테이블
CREATE TABLE wp_jj_referrals (
    id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    referrer_id BIGINT(20) UNSIGNED NOT NULL,
    referred_id BIGINT(20) UNSIGNED NOT NULL,
    referral_level TINYINT(3) UNSIGNED NOT NULL DEFAULT 1,
    referral_code VARCHAR(50) NOT NULL,
    status ENUM('pending', 'completed', 'rewarded') DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    completed_at DATETIME NULL,
    reward_issued_at DATETIME NULL,
    INDEX idx_referrer (referrer_id),
    INDEX idx_referred (referred_id),
    INDEX idx_code (referral_code)
);

-- 레퍼럴 보상 이력
CREATE TABLE wp_jj_referral_rewards (
    id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    referral_id BIGINT(20) UNSIGNED NOT NULL,
    reward_type ENUM('premium_days', 'discount', 'credit') NOT NULL,
    reward_value VARCHAR(50) NOT NULL,
    issued_at DATETIME NOT NULL,
    expires_at DATETIME NULL,
    used_at DATETIME NULL,
    INDEX idx_user (user_id)
);
```

#### PHP 클래스 구조
```php
// includes/class-jj-referral-tracker.php
class JJ_Referral_Tracker {
    public function generate_referral_code($user_id);
    public function track_referral($referrer_id, $referred_id, $referral_code);
    public function get_referral_tree($user_id);
    public function calculate_rewards($user_id);
    public function issue_reward($user_id, $reward_type, $reward_value);
    public function get_referral_stats($user_id);
}
```

### 2. 공유 기능 UI/UX

#### 위치: 모든 플러그인 대시보드
```php
// 각 플러그인의 admin 페이지에 추가
<div class="jj-viral-share-widget">
    <div class="jj-share-header">
        <h3>🎁 친구를 초대하고 Premium 무료로 받기</h3>
        <p>추천 링크를 공유하면 양쪽 모두 혜택을 받습니다!</p>
    </div>
    
    <div class="jj-referral-link-box">
        <input type="text" 
               id="jj-referral-link" 
               value="<?php echo esc_attr( $referral_link ); ?>" 
               readonly>
        <button class="button button-primary" onclick="copyReferralLink()">
            링크 복사
        </button>
    </div>
    
    <div class="jj-share-buttons">
        <button class="jj-share-btn kakao" onclick="shareToKakao()">
            카카오톡
        </button>
        <button class="jj-share-btn facebook" onclick="shareToFacebook()">
            페이스북
        </button>
        <button class="jj-share-btn twitter" onclick="shareToTwitter()">
            트위터
        </button>
        <button class="jj-share-btn email" onclick="shareToEmail()">
            이메일
        </button>
    </div>
    
    <div class="jj-referral-stats">
        <div class="stat-item">
            <span class="stat-value"><?php echo $referral_count; ?></span>
            <span class="stat-label">추천 성공</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?php echo $premium_days_left; ?></span>
            <span class="stat-label">Premium 남은 일수</span>
        </div>
    </div>
</div>
```

### 3. 게이미피케이션 시스템

#### 배지 시스템
```php
// includes/class-jj-gamification.php
class JJ_Gamification {
    public function check_achievements($user_id);
    public function award_badge($user_id, $badge_type);
    public function get_user_level($user_id);
    public function get_leaderboard($type = 'referrals');
    public function share_achievement($user_id, $achievement_id);
}
```

#### 배지 종류
- 🏆 "첫 스타일": 첫 번째 스타일 생성
- 🎨 "템플릿 제작자": 템플릿 5개 공유
- 👥 "커뮤니티 리더": 추천 10명 성공
- 🌟 "인플루언서": 추천 50명 성공
- 💎 "마스터": 추천 100명 성공

---

## 💰 요금제 시스템 구현

### WooCommerce 제품 설정

#### 제품 구조
```
1. Starter (Free) - $0
   - Product ID: 1001
   - Virtual: Yes
   - Downloadable: Yes
   - License Type: Free

2. Professional - $29/월
   - Product ID: 1002
   - Subscription: Yes (월간)
   - License Type: Professional
   - Sites: 5

3. Business - $79/월
   - Product ID: 1003
   - Subscription: Yes (월간)
   - License Type: Business
   - Sites: Unlimited

4. Agency - $199/월
   - Product ID: 1004
   - Subscription: Yes (월간)
   - License Type: Agency
   - Sites: Unlimited
   - White Label: Yes

5. Lifetime - $999
   - Product ID: 1005
   - One-time Payment
   - License Type: Lifetime
   - Sites: Unlimited
```

### 가격 테이블 UI
```php
// templates/pricing-table.php
<div class="jj-pricing-table">
    <div class="pricing-card starter">
        <div class="pricing-header">
            <h3>Starter</h3>
            <div class="price">$0<span>/월</span></div>
        </div>
        <ul class="features">
            <li>✅ 기본 스타일 관리</li>
            <li>✅ 1개 사이트</li>
            <li>✅ 커뮤니티 지원</li>
            <li>✅ 기본 템플릿 5개</li>
        </ul>
        <button class="button">무료로 시작하기</button>
    </div>
    
    <!-- Professional, Business, Agency, Lifetime 카드들 -->
</div>
```

---

## 📱 소셜 미디어 공유 최적화

### Open Graph 메타 태그
```php
// 각 플러그인에 추가
function jj_add_og_tags() {
    if ( is_admin() && isset( $_GET['page'] ) && strpos( $_GET['page'], 'jj-' ) === 0 ) {
        ?>
        <meta property="og:title" content="ACF CSS Manager - WordPress 스타일 관리">
        <meta property="og:description" content="CSS 없이 전문가 수준의 웹디자인을 만드세요. AI가 자동으로 스타일을 생성합니다.">
        <meta property="og:image" content="<?php echo JJ_PLUGIN_URL; ?>assets/images/og-image.jpg">
        <meta property="og:url" content="<?php echo admin_url( 'admin.php?page=' . $_GET['page'] ); ?>">
        <?php
    }
}
add_action( 'admin_head', 'jj_add_og_tags' );
```

### 공유 버튼 JavaScript
```javascript
// assets/js/jj-viral-share.js
function shareToKakao() {
    Kakao.Share.sendDefault({
        objectType: 'feed',
        content: {
            title: 'ACF CSS Manager 추천',
            description: 'CSS 없이 전문가 수준의 웹디자인을 만드세요!',
            imageUrl: 'https://3j-labs.com/assets/images/share-image.jpg',
            link: {
                mobileWebUrl: referralLink,
                webUrl: referralLink
            }
        },
        buttons: [
            {
                title: '지금 시작하기',
                link: {
                    mobileWebUrl: referralLink,
                    webUrl: referralLink
                }
            }
        ]
    });
}

function shareToFacebook() {
    window.open(
        'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(referralLink),
        'facebook-share-dialog',
        'width=626,height=436'
    );
}

function shareToTwitter() {
    window.open(
        'https://twitter.com/intent/tweet?text=' + encodeURIComponent('ACF CSS Manager 추천!') + '&url=' + encodeURIComponent(referralLink),
        'twitter-share-dialog',
        'width=626,height=436'
    );
}
```

---

## 🔄 레퍼럴 플로우 다이어그램

```
사용자 A (추천인)
    ↓
추천 링크 생성: 3j-labs.com/ref/A123
    ↓
소셜 미디어 공유
    ↓
사용자 B (피추천인) 클릭
    ↓
쿠키에 referrer_id 저장 (30일)
    ↓
사용자 B 가입
    ↓
레퍼럴 관계 생성
    ↓
사용자 B 첫 결제
    ↓
인센티브 지급:
- 사용자 A: 30일 Premium 무료
- 사용자 B: 20% 할인 쿠폰
    ↓
사용자 B가 또 추천 (Level 2)
    ↓
사용자 A: 60일 Premium 무료
```

---

## 📊 마케팅 대시보드

### 레퍼럴 통계 대시보드
```php
// admin/views/view-referrals.php
<div class="jj-referral-dashboard">
    <h2>📊 레퍼럴 통계</h2>
    
    <div class="jj-stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_referrals; ?></div>
            <div class="stat-label">총 추천</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $successful_referrals; ?></div>
            <div class="stat-label">성공한 추천</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $premium_days_earned; ?></div>
            <div class="stat-label">획득한 Premium 일수</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $referral_commission; ?>원</div>
            <div class="stat-label">예상 커미션</div>
        </div>
    </div>
    
    <div class="jj-referral-tree">
        <h3>추천 트리</h3>
        <!-- 트리 구조 시각화 -->
    </div>
    
    <div class="jj-leaderboard">
        <h3>추천 리더보드</h3>
        <!-- 주간/월간 랭킹 -->
    </div>
</div>
```

---

## 🎨 UI/UX 디자인 (Jenny)

### 색상 팔레트
- Primary: #6366f1 (Indigo)
- Success: #10b981 (Emerald)
- Warning: #f59e0b (Amber)
- Gradient 1: #667eea → #764ba2
- Gradient 2: #f093fb → #f5576c

### 애니메이션
- 버튼 호버: 0.2s ease
- 카드 호버: scale(1.02)
- 성공 알림: slide-in from top

---

## 🚀 구현 우선순위 (오늘 완료)

### 1단계: 레퍼럴 추적 시스템 (2-3시간)
- [ ] 데이터베이스 테이블 생성
- [ ] JJ_Referral_Tracker 클래스 구현
- [ ] Neural Link 통합
- [ ] 기본 추적 로직 테스트

### 2단계: 공유 UI 구현 (1-2시간)
- [ ] 공유 위젯 HTML/CSS
- [ ] JavaScript 공유 함수
- [ ] 각 플러그인 대시보드에 통합

### 3단계: 인센티브 시스템 (2-3시간)
- [ ] 보상 계산 로직
- [ ] Premium 일수 자동 활성화
- [ ] 할인 쿠폰 생성 시스템

### 4단계: 게이미피케이션 (1-2시간)
- [ ] 배지 시스템 기본 구조
- [ ] 레벨 시스템
- [ ] 리더보드 기본 구조

---

*작성일: 2026-01-04*  
*작성자: Jason (CTO, 3J Labs)*

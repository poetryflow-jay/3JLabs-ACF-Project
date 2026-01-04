# 3J Labs - 레퍼럴 시스템 상세 구현 계획서

**작성일**: 2026년 1월 4일
**버전**: 2.0 (Phase 40.2 보강)
**목표**: 바이럴 성장을 위한 레퍼럴 시스템 구현
**상태**: Phase 40.2 구현 대기 중

---

## 0. 현재 프로젝트 통합 컨텍스트

### 0.1 Phase 40.1 완료 항목 연동
- **Code Snippets Box v2.3.4**: 프리셋 토글 기능 - 레퍼럴 코드 공유 프리셋 추가 가능
- **Nudge Flow v22.4.6**: 워크플로우 빌더 개선 - 레퍼럴 이벤트 기반 넛지 생성 가능
- **JJ Marketing Automation v1.0.2**: 레퍼럴 데이터 연동 준비 완료

### 0.2 기존 플러그인 통합 포인트
| 플러그인 | 통합 방식 | 역할 |
|----------|-----------|------|
| ACF CSS Manager v22.5.1 | Neural Link 통신 | 마스터 라이센스 상태 공유 |
| Neural Link v6.3.5 | REST API 브릿지 | 라이센스 연동 및 무료 일수 적용 |
| Woo License v22.0.5 | WooCommerce 훅 | 구매 전환 추적 및 커미션 계산 |
| Nudge Flow v22.4.6 | 이벤트 트리거 | 레퍼럴 성과 기반 마케팅 자동화 |
| JJ Analytics v1.0.1 | 데이터 수집 | 레퍼럴 통계 시각화 |

### 0.3 공통 유틸리티 활용 (Phase 39.2)
```php
// JJ_Ajax_Helper 활용 (shared-ui-assets/php/)
$ajax = JJ_Shared_Loader::ajax();
if ( ! $ajax->verify_request( 'jj_referral_nonce', 'nonce' ) ) {
    return; // 보안 검증 실패
}

// JJ_Singleton_Trait 활용
class JJ_Referral_Core {
    use JJ_Singleton_Trait;
    // 싱글톤 패턴 자동 적용
}
```

---

## 1. 시스템 아키텍처

### 1.1 전체 구조

```
┌─────────────────────────────────────────────────────────────────┐
│                    3J Labs Referral System                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐       │
│  │   Frontend   │    │   Backend    │    │   Database   │       │
│  │              │    │              │    │              │       │
│  │  - React/JS  │◄──►│  - PHP API   │◄──►│  - MariaDB   │       │
│  │  - Dashboard │    │  - WP Plugin │    │  - Redis     │       │
│  │  - Widgets   │    │  - REST API  │    │  (캐시)       │       │
│  └──────────────┘    └──────────────┘    └──────────────┘       │
│                                                                   │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐       │
│  │   Email      │    │   Analytics  │    │   Payment    │       │
│  │              │    │              │    │              │       │
│  │  - 알림 발송  │    │  - 추적/분석 │    │  - 보상 지급  │       │
│  │  - 템플릿    │    │  - 리포트    │    │  - WooCommerce│       │
│  └──────────────┘    └──────────────┘    └──────────────┘       │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

### 1.2 플러그인 구조

```
jj-referral-system/
├── jj-referral-system.php          # 메인 플러그인 파일
├── includes/
│   ├── class-jj-referral-core.php      # 핵심 로직
│   ├── class-jj-referral-code.php      # 코드 생성/관리
│   ├── class-jj-referral-tracker.php   # 추적 시스템
│   ├── class-jj-referral-rewards.php   # 보상 시스템
│   ├── class-jj-referral-api.php       # REST API
│   ├── class-jj-referral-emails.php    # 이메일 알림
│   ├── class-jj-referral-analytics.php # 분석/리포팅
│   └── class-jj-referral-admin.php     # 관리자 페이지
├── assets/
│   ├── css/
│   │   ├── referral-dashboard.css
│   │   └── referral-widgets.css
│   └── js/
│       ├── referral-dashboard.js
│       ├── referral-share.js
│       └── referral-analytics.js
├── templates/
│   ├── dashboard.php               # 사용자 대시보드
│   ├── share-widget.php            # 공유 위젯
│   ├── leaderboard.php             # 리더보드
│   └── emails/
│       ├── welcome.php
│       ├── referral-success.php
│       ├── reward-earned.php
│       └── milestone-reached.php
└── languages/
    └── jj-referral-ko_KR.po
```

---

## 2. 데이터베이스 설계

### 2.1 테이블 스키마

```sql
-- =============================================
-- 1. 레퍼럴 코드 테이블
-- =============================================
CREATE TABLE {prefix}jj_referral_codes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    custom_code VARCHAR(50) DEFAULT NULL,          -- 사용자 정의 코드 (선택)
    is_active TINYINT(1) DEFAULT 1,
    click_count INT UNSIGNED DEFAULT 0,
    signup_count INT UNSIGNED DEFAULT 0,
    conversion_count INT UNSIGNED DEFAULT 0,
    total_revenue DECIMAL(10,2) DEFAULT 0.00,
    total_commission DECIMAL(10,2) DEFAULT 0.00,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_user_id (user_id),
    INDEX idx_code (code),
    INDEX idx_custom_code (custom_code),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. 레퍼럴 추적 테이블
-- =============================================
CREATE TABLE {prefix}jj_referral_tracking (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    referrer_id BIGINT UNSIGNED NOT NULL,          -- 추천인 ID
    referred_id BIGINT UNSIGNED DEFAULT NULL,       -- 피추천인 ID (가입 후)
    referral_code VARCHAR(20) NOT NULL,

    -- 추적 데이터
    ip_address VARCHAR(45),                         -- IPv4/IPv6 지원
    user_agent TEXT,
    referrer_url TEXT,                              -- 유입 URL
    landing_page TEXT,                              -- 랜딩 페이지
    utm_source VARCHAR(100),
    utm_medium VARCHAR(100),
    utm_campaign VARCHAR(100),

    -- 상태
    status ENUM('clicked', 'signed_up', 'converted', 'expired') DEFAULT 'clicked',

    -- 타임스탬프
    clicked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    signed_up_at DATETIME DEFAULT NULL,
    converted_at DATETIME DEFAULT NULL,
    expires_at DATETIME DEFAULT NULL,               -- 쿠키 만료

    -- 결제 정보
    order_id BIGINT UNSIGNED DEFAULT NULL,
    product_id BIGINT UNSIGNED DEFAULT NULL,
    purchase_amount DECIMAL(10,2) DEFAULT 0.00,
    commission_rate DECIMAL(5,2) DEFAULT 20.00,     -- 커미션 비율 (%)
    commission_amount DECIMAL(10,2) DEFAULT 0.00,

    INDEX idx_referrer_id (referrer_id),
    INDEX idx_referred_id (referred_id),
    INDEX idx_code (referral_code),
    INDEX idx_status (status),
    INDEX idx_clicked_at (clicked_at),
    INDEX idx_converted_at (converted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 3. 보상 테이블
-- =============================================
CREATE TABLE {prefix}jj_referral_rewards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    tracking_id BIGINT UNSIGNED DEFAULT NULL,       -- 연결된 추적 ID

    -- 보상 유형
    reward_type ENUM(
        'free_days',        -- 무료 사용 일수
        'discount_percent', -- 할인율 (%)
        'discount_amount',  -- 할인 금액
        'credit',           -- 크레딧/포인트
        'commission',       -- 커미션
        'badge',            -- 배지
        'feature_unlock'    -- 기능 잠금 해제
    ) NOT NULL,

    -- 보상 값
    value DECIMAL(10,2) NOT NULL,
    description VARCHAR(255),

    -- 상태
    status ENUM('pending', 'active', 'applied', 'expired', 'cancelled') DEFAULT 'pending',

    -- 타임스탬프
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME DEFAULT NULL,
    applied_at DATETIME DEFAULT NULL,

    -- 적용 정보
    applied_to_order BIGINT UNSIGNED DEFAULT NULL,
    applied_note TEXT,

    INDEX idx_user_id (user_id),
    INDEX idx_tracking_id (tracking_id),
    INDEX idx_type (reward_type),
    INDEX idx_status (status),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 4. 레퍼럴 레벨/티어 테이블
-- =============================================
CREATE TABLE {prefix}jj_referral_tiers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tier_name VARCHAR(50) NOT NULL,                 -- 'Bronze', 'Silver', 'Gold', 'Platinum'
    min_referrals INT UNSIGNED DEFAULT 0,           -- 최소 추천 수
    commission_rate DECIMAL(5,2) DEFAULT 20.00,     -- 커미션 비율
    free_days_per_referral INT UNSIGNED DEFAULT 30, -- 추천 당 무료 일수
    discount_for_referred DECIMAL(5,2) DEFAULT 20.00, -- 피추천인 할인율
    badge_image VARCHAR(255),
    color_code VARCHAR(7),                          -- #RRGGBB
    is_active TINYINT(1) DEFAULT 1,

    INDEX idx_min_referrals (min_referrals)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 기본 티어 데이터 삽입
INSERT INTO {prefix}jj_referral_tiers
(tier_name, min_referrals, commission_rate, free_days_per_referral, discount_for_referred, color_code) VALUES
('Starter', 0, 20.00, 30, 20.00, '#CD7F32'),
('Bronze', 3, 22.00, 45, 25.00, '#CD7F32'),
('Silver', 10, 25.00, 60, 30.00, '#C0C0C0'),
('Gold', 25, 28.00, 75, 35.00, '#FFD700'),
('Platinum', 50, 30.00, 90, 40.00, '#E5E4E2');

-- =============================================
-- 5. 사용자 레퍼럴 통계 테이블
-- =============================================
CREATE TABLE {prefix}jj_referral_user_stats (
    user_id BIGINT UNSIGNED PRIMARY KEY,
    tier_id INT UNSIGNED DEFAULT 1,

    -- 누적 통계
    total_clicks INT UNSIGNED DEFAULT 0,
    total_signups INT UNSIGNED DEFAULT 0,
    total_conversions INT UNSIGNED DEFAULT 0,
    total_revenue DECIMAL(12,2) DEFAULT 0.00,
    total_commission_earned DECIMAL(12,2) DEFAULT 0.00,
    total_commission_paid DECIMAL(12,2) DEFAULT 0.00,
    total_free_days_earned INT UNSIGNED DEFAULT 0,

    -- 현재 상태
    current_free_days INT UNSIGNED DEFAULT 0,       -- 남은 무료 일수
    pending_commission DECIMAL(10,2) DEFAULT 0.00,  -- 미지급 커미션

    -- 게이미피케이션
    xp_points INT UNSIGNED DEFAULT 0,
    level INT UNSIGNED DEFAULT 1,
    badges JSON,                                     -- 획득한 배지 목록
    streak_days INT UNSIGNED DEFAULT 0,             -- 연속 활동 일수

    -- 타임스탬프
    last_referral_at DATETIME DEFAULT NULL,
    last_conversion_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_tier_id (tier_id),
    INDEX idx_total_conversions (total_conversions),
    INDEX idx_xp_points (xp_points)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 6. 레퍼럴 트리 (2차 추천) 테이블
-- =============================================
CREATE TABLE {prefix}jj_referral_tree (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,               -- 대상 사용자
    parent_id BIGINT UNSIGNED DEFAULT NULL,         -- 직접 추천인
    grandparent_id BIGINT UNSIGNED DEFAULT NULL,    -- 2차 추천인
    level INT UNSIGNED DEFAULT 1,                   -- 트리 레벨 (1: 직접, 2: 간접)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_user (user_id),
    INDEX idx_parent_id (parent_id),
    INDEX idx_grandparent_id (grandparent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 7. 이벤트 로그 테이블
-- =============================================
CREATE TABLE {prefix}jj_referral_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    event_type VARCHAR(50) NOT NULL,                -- 'click', 'signup', 'conversion', 'reward', etc.
    event_data JSON,
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user_id (user_id),
    INDEX idx_event_type (event_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.2 인덱스 최적화

```sql
-- 성능을 위한 복합 인덱스
ALTER TABLE {prefix}jj_referral_tracking
ADD INDEX idx_referrer_status_date (referrer_id, status, clicked_at);

ALTER TABLE {prefix}jj_referral_rewards
ADD INDEX idx_user_status_expires (user_id, status, expires_at);
```

---

## 3. 핵심 클래스 구현

### 3.1 JJ_Referral_Code - 코드 생성/관리

```php
<?php
/**
 * 레퍼럴 코드 생성 및 관리
 */
class JJ_Referral_Code {

    const CODE_PREFIX = 'JJ';
    const CODE_LENGTH = 8;

    /**
     * 레퍼럴 코드 생성
     */
    public static function generate( $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_codes';

        // 이미 코드가 있는지 확인
        $existing = $wpdb->get_var( $wpdb->prepare(
            "SELECT code FROM $table WHERE user_id = %d AND is_active = 1",
            $user_id
        ) );

        if ( $existing ) {
            return $existing;
        }

        // 새 코드 생성
        $code = self::create_unique_code( $user_id );

        $wpdb->insert( $table, array(
            'user_id' => $user_id,
            'code'    => $code,
        ), array( '%d', '%s' ) );

        return $code;
    }

    /**
     * 고유 코드 생성
     */
    private static function create_unique_code( $user_id ) {
        $hash = strtoupper( substr(
            md5( $user_id . wp_salt() . microtime() ),
            0,
            self::CODE_LENGTH - strlen( self::CODE_PREFIX )
        ) );

        return self::CODE_PREFIX . $hash;
    }

    /**
     * 코드로 사용자 찾기
     */
    public static function get_user_by_code( $code ) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_codes';

        $code = sanitize_text_field( $code );

        // 표준 코드 또는 커스텀 코드 검색
        return $wpdb->get_var( $wpdb->prepare(
            "SELECT user_id FROM $table
             WHERE (code = %s OR custom_code = %s) AND is_active = 1",
            $code, $code
        ) );
    }

    /**
     * 사용자 정의 코드 설정
     */
    public static function set_custom_code( $user_id, $custom_code ) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_codes';

        // 유효성 검사
        $custom_code = sanitize_title( $custom_code );

        if ( strlen( $custom_code ) < 4 || strlen( $custom_code ) > 20 ) {
            return new WP_Error( 'invalid_length', '코드는 4-20자여야 합니다.' );
        }

        // 중복 확인
        $exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM $table WHERE custom_code = %s AND user_id != %d",
            $custom_code, $user_id
        ) );

        if ( $exists ) {
            return new WP_Error( 'duplicate_code', '이미 사용 중인 코드입니다.' );
        }

        $wpdb->update(
            $table,
            array( 'custom_code' => $custom_code ),
            array( 'user_id' => $user_id ),
            array( '%s' ),
            array( '%d' )
        );

        return true;
    }

    /**
     * 레퍼럴 링크 생성
     */
    public static function get_referral_url( $user_id, $landing = '' ) {
        $code = self::generate( $user_id );

        $base_url = empty( $landing )
            ? home_url( '/pricing/' )
            : esc_url( $landing );

        return add_query_arg( 'ref', $code, $base_url );
    }

    /**
     * 코드 통계 업데이트
     */
    public static function update_stats( $code, $field, $increment = 1 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_codes';

        $allowed_fields = array( 'click_count', 'signup_count', 'conversion_count' );

        if ( ! in_array( $field, $allowed_fields ) ) {
            return false;
        }

        return $wpdb->query( $wpdb->prepare(
            "UPDATE $table SET $field = $field + %d WHERE code = %s",
            $increment, $code
        ) );
    }
}
```

### 3.2 JJ_Referral_Tracker - 추적 시스템

```php
<?php
/**
 * 레퍼럴 추적 시스템
 */
class JJ_Referral_Tracker {

    const COOKIE_NAME = 'jj_ref';
    const COOKIE_DURATION = 30; // 일

    /**
     * 클릭 추적
     */
    public static function track_click( $code ) {
        global $wpdb;

        $referrer_id = JJ_Referral_Code::get_user_by_code( $code );

        if ( ! $referrer_id ) {
            return false;
        }

        // 자기 추천 방지
        if ( is_user_logged_in() && get_current_user_id() == $referrer_id ) {
            return false;
        }

        // 중복 클릭 방지 (같은 IP, 24시간 내)
        $table = $wpdb->prefix . 'jj_referral_tracking';
        $ip = self::get_client_ip();

        $recent = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM $table
             WHERE referral_code = %s AND ip_address = %s
             AND clicked_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)",
            $code, $ip
        ) );

        if ( ! $recent ) {
            // 새 클릭 기록
            $wpdb->insert( $table, array(
                'referrer_id'   => $referrer_id,
                'referral_code' => $code,
                'ip_address'    => $ip,
                'user_agent'    => isset( $_SERVER['HTTP_USER_AGENT'] )
                    ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',
                'referrer_url'  => isset( $_SERVER['HTTP_REFERER'] )
                    ? esc_url_raw( $_SERVER['HTTP_REFERER'] ) : '',
                'landing_page'  => esc_url_raw( home_url( $_SERVER['REQUEST_URI'] ) ),
                'utm_source'    => isset( $_GET['utm_source'] )
                    ? sanitize_text_field( $_GET['utm_source'] ) : null,
                'utm_medium'    => isset( $_GET['utm_medium'] )
                    ? sanitize_text_field( $_GET['utm_medium'] ) : null,
                'utm_campaign'  => isset( $_GET['utm_campaign'] )
                    ? sanitize_text_field( $_GET['utm_campaign'] ) : null,
                'expires_at'    => date( 'Y-m-d H:i:s',
                    time() + ( self::COOKIE_DURATION * DAY_IN_SECONDS ) ),
            ) );

            // 코드 통계 업데이트
            JJ_Referral_Code::update_stats( $code, 'click_count' );
        }

        // 쿠키 설정
        self::set_cookie( $code );

        // 이벤트 로그
        self::log_event( 0, 'click', array(
            'code'      => $code,
            'referrer'  => $referrer_id,
            'ip'        => $ip,
        ) );

        return true;
    }

    /**
     * 가입 추적
     */
    public static function track_signup( $user_id ) {
        global $wpdb;

        $code = self::get_cookie();

        if ( ! $code ) {
            return false;
        }

        $referrer_id = JJ_Referral_Code::get_user_by_code( $code );

        if ( ! $referrer_id || $referrer_id == $user_id ) {
            return false;
        }

        $table = $wpdb->prefix . 'jj_referral_tracking';
        $ip = self::get_client_ip();

        // 기존 클릭 기록 업데이트 또는 새로 생성
        $tracking = $wpdb->get_row( $wpdb->prepare(
            "SELECT id FROM $table
             WHERE referral_code = %s AND ip_address = %s
             AND status = 'clicked' AND expires_at > NOW()
             ORDER BY clicked_at DESC LIMIT 1",
            $code, $ip
        ) );

        if ( $tracking ) {
            $wpdb->update(
                $table,
                array(
                    'referred_id'   => $user_id,
                    'status'        => 'signed_up',
                    'signed_up_at'  => current_time( 'mysql' ),
                ),
                array( 'id' => $tracking->id )
            );
        } else {
            // 새 레코드 생성 (쿠키만 있는 경우)
            $wpdb->insert( $table, array(
                'referrer_id'   => $referrer_id,
                'referred_id'   => $user_id,
                'referral_code' => $code,
                'ip_address'    => $ip,
                'status'        => 'signed_up',
                'signed_up_at'  => current_time( 'mysql' ),
            ) );
        }

        // 레퍼럴 트리 업데이트
        self::update_referral_tree( $user_id, $referrer_id );

        // 통계 업데이트
        JJ_Referral_Code::update_stats( $code, 'signup_count' );

        // 추천인에게 보상 지급
        JJ_Referral_Rewards::grant_signup_reward( $referrer_id, $user_id );

        // 피추천인에게 할인 보상
        JJ_Referral_Rewards::grant_welcome_discount( $user_id, $referrer_id );

        // 이메일 알림
        JJ_Referral_Emails::send_referral_success( $referrer_id, $user_id );

        // 쿠키 유지 (전환 추적용)

        return true;
    }

    /**
     * 전환 (구매) 추적
     */
    public static function track_conversion( $order_id, $user_id = null ) {
        global $wpdb;

        if ( ! $user_id ) {
            $order = wc_get_order( $order_id );
            $user_id = $order->get_user_id();
        }

        if ( ! $user_id ) {
            return false;
        }

        $table = $wpdb->prefix . 'jj_referral_tracking';

        // 사용자의 레퍼럴 기록 찾기
        $tracking = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table
             WHERE referred_id = %d AND status IN ('clicked', 'signed_up')
             ORDER BY clicked_at DESC LIMIT 1",
            $user_id
        ) );

        if ( ! $tracking ) {
            // 쿠키에서 확인
            $code = self::get_cookie();
            if ( $code ) {
                $referrer_id = JJ_Referral_Code::get_user_by_code( $code );
                if ( $referrer_id && $referrer_id != $user_id ) {
                    // 새 전환 기록 생성
                    $tracking = (object) array(
                        'referrer_id'   => $referrer_id,
                        'referral_code' => $code,
                    );
                }
            }
        }

        if ( ! $tracking ) {
            return false;
        }

        $order = wc_get_order( $order_id );
        $amount = $order->get_total();

        // 커미션 계산
        $tier = JJ_Referral_Rewards::get_user_tier( $tracking->referrer_id );
        $commission_rate = $tier->commission_rate;
        $commission = $amount * ( $commission_rate / 100 );

        // 업데이트
        if ( isset( $tracking->id ) ) {
            $wpdb->update(
                $table,
                array(
                    'status'            => 'converted',
                    'converted_at'      => current_time( 'mysql' ),
                    'order_id'          => $order_id,
                    'purchase_amount'   => $amount,
                    'commission_rate'   => $commission_rate,
                    'commission_amount' => $commission,
                ),
                array( 'id' => $tracking->id )
            );
        } else {
            $wpdb->insert( $table, array(
                'referrer_id'       => $tracking->referrer_id,
                'referred_id'       => $user_id,
                'referral_code'     => $tracking->referral_code,
                'status'            => 'converted',
                'converted_at'      => current_time( 'mysql' ),
                'order_id'          => $order_id,
                'purchase_amount'   => $amount,
                'commission_rate'   => $commission_rate,
                'commission_amount' => $commission,
            ) );
        }

        // 통계 업데이트
        JJ_Referral_Code::update_stats( $tracking->referral_code, 'conversion_count' );

        // 커미션 보상
        JJ_Referral_Rewards::grant_commission( $tracking->referrer_id, $commission, $order_id );

        // 2차 추천 보상 (있는 경우)
        self::process_second_level_reward( $tracking->referrer_id, $amount );

        // 이메일 알림
        JJ_Referral_Emails::send_conversion_notification( $tracking->referrer_id, $order_id, $commission );

        // 쿠키 삭제
        self::clear_cookie();

        return true;
    }

    /**
     * 레퍼럴 트리 업데이트
     */
    private static function update_referral_tree( $user_id, $parent_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_tree';

        // 부모의 부모 (grandparent) 찾기
        $grandparent_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT parent_id FROM $table WHERE user_id = %d",
            $parent_id
        ) );

        $wpdb->replace( $table, array(
            'user_id'        => $user_id,
            'parent_id'      => $parent_id,
            'grandparent_id' => $grandparent_id,
            'level'          => $grandparent_id ? 2 : 1,
        ) );
    }

    /**
     * 2차 추천 보상 처리
     */
    private static function process_second_level_reward( $referrer_id, $amount ) {
        global $wpdb;
        $tree_table = $wpdb->prefix . 'jj_referral_tree';

        // 2차 추천인 찾기
        $grandparent = $wpdb->get_var( $wpdb->prepare(
            "SELECT parent_id FROM $tree_table WHERE user_id = %d",
            $referrer_id
        ) );

        if ( $grandparent ) {
            // 2차 커미션 (5%)
            $second_commission = $amount * 0.05;
            JJ_Referral_Rewards::grant_commission(
                $grandparent,
                $second_commission,
                null,
                'second_level'
            );

            // 추가 무료 일수 (10일)
            JJ_Referral_Rewards::grant_free_days( $grandparent, 10, 'second_level_bonus' );
        }
    }

    // 유틸리티 메서드들

    private static function set_cookie( $code ) {
        $expires = time() + ( self::COOKIE_DURATION * DAY_IN_SECONDS );
        setcookie( self::COOKIE_NAME, $code, $expires, '/', '', is_ssl(), true );
    }

    private static function get_cookie() {
        return isset( $_COOKIE[ self::COOKIE_NAME ] )
            ? sanitize_text_field( $_COOKIE[ self::COOKIE_NAME ] )
            : null;
    }

    private static function clear_cookie() {
        setcookie( self::COOKIE_NAME, '', time() - 3600, '/' );
    }

    private static function get_client_ip() {
        $ip_keys = array( 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR' );

        foreach ( $ip_keys as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ip = explode( ',', $_SERVER[ $key ] )[0];
                $ip = trim( $ip );

                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }

    private static function log_event( $user_id, $type, $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_events';

        $wpdb->insert( $table, array(
            'user_id'    => $user_id,
            'event_type' => $type,
            'event_data' => wp_json_encode( $data ),
            'ip_address' => self::get_client_ip(),
        ) );
    }
}
```

### 3.3 JJ_Referral_Rewards - 보상 시스템

```php
<?php
/**
 * 레퍼럴 보상 시스템
 */
class JJ_Referral_Rewards {

    /**
     * 가입 보상 지급 (추천인)
     */
    public static function grant_signup_reward( $referrer_id, $referred_id ) {
        $tier = self::get_user_tier( $referrer_id );
        $free_days = $tier->free_days_per_referral;

        // 무료 일수 지급
        self::grant_free_days( $referrer_id, $free_days, 'referral_signup' );

        // XP 지급
        self::add_xp( $referrer_id, 100, 'referral_signup' );

        // 통계 업데이트
        self::update_user_stats( $referrer_id, 'total_signups', 1 );

        return true;
    }

    /**
     * 환영 할인 지급 (피추천인)
     */
    public static function grant_welcome_discount( $user_id, $referrer_id ) {
        $tier = self::get_user_tier( $referrer_id );
        $discount = $tier->discount_for_referred;

        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_rewards';

        $wpdb->insert( $table, array(
            'user_id'     => $user_id,
            'reward_type' => 'discount_percent',
            'value'       => $discount,
            'description' => sprintf( '추천 가입 %d%% 할인', $discount ),
            'status'      => 'active',
            'expires_at'  => date( 'Y-m-d H:i:s', strtotime( '+7 days' ) ),
        ) );

        return true;
    }

    /**
     * 커미션 지급
     */
    public static function grant_commission( $user_id, $amount, $order_id = null, $source = 'direct' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_rewards';

        $wpdb->insert( $table, array(
            'user_id'          => $user_id,
            'reward_type'      => 'commission',
            'value'            => $amount,
            'description'      => $source === 'second_level'
                ? '2차 추천 커미션'
                : '추천 커미션',
            'status'           => 'pending',
            'applied_to_order' => $order_id,
        ) );

        // 사용자 통계 업데이트
        self::update_user_stats( $user_id, 'total_commission_earned', $amount );
        self::update_user_stats( $user_id, 'pending_commission', $amount );

        return true;
    }

    /**
     * 무료 일수 지급
     */
    public static function grant_free_days( $user_id, $days, $source = 'referral' ) {
        global $wpdb;
        $rewards_table = $wpdb->prefix . 'jj_referral_rewards';
        $stats_table = $wpdb->prefix . 'jj_referral_user_stats';

        // 보상 기록
        $wpdb->insert( $rewards_table, array(
            'user_id'     => $user_id,
            'reward_type' => 'free_days',
            'value'       => $days,
            'description' => sprintf( '%d일 무료 사용', $days ),
            'status'      => 'active',
            'expires_at'  => date( 'Y-m-d H:i:s', strtotime( '+1 year' ) ),
        ) );

        // 현재 무료 일수 업데이트
        $wpdb->query( $wpdb->prepare(
            "INSERT INTO $stats_table (user_id, current_free_days, total_free_days_earned)
             VALUES (%d, %d, %d)
             ON DUPLICATE KEY UPDATE
                current_free_days = current_free_days + %d,
                total_free_days_earned = total_free_days_earned + %d",
            $user_id, $days, $days, $days, $days
        ) );

        return true;
    }

    /**
     * XP 지급
     */
    public static function add_xp( $user_id, $points, $source = '' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_user_stats';

        $wpdb->query( $wpdb->prepare(
            "INSERT INTO $table (user_id, xp_points)
             VALUES (%d, %d)
             ON DUPLICATE KEY UPDATE xp_points = xp_points + %d",
            $user_id, $points, $points
        ) );

        // 레벨 업 체크
        self::check_level_up( $user_id );

        return true;
    }

    /**
     * 레벨 업 체크
     */
    private static function check_level_up( $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_user_stats';

        $stats = $wpdb->get_row( $wpdb->prepare(
            "SELECT xp_points, level FROM $table WHERE user_id = %d",
            $user_id
        ) );

        if ( ! $stats ) return;

        // 레벨 계산 (로그 스케일)
        $new_level = self::calculate_level( $stats->xp_points );

        if ( $new_level > $stats->level ) {
            $wpdb->update(
                $table,
                array( 'level' => $new_level ),
                array( 'user_id' => $user_id )
            );

            // 레벨 업 보상
            self::grant_level_up_reward( $user_id, $new_level );

            // 이메일 알림
            JJ_Referral_Emails::send_level_up_notification( $user_id, $new_level );
        }
    }

    /**
     * XP로 레벨 계산
     */
    private static function calculate_level( $xp ) {
        // Level 1: 0-100 XP
        // Level 2: 100-500 XP
        // Level 3: 500-1000 XP
        // Level 4: 1000-2500 XP
        // Level 5: 2500+ XP

        if ( $xp >= 2500 ) return 5;
        if ( $xp >= 1000 ) return 4;
        if ( $xp >= 500 ) return 3;
        if ( $xp >= 100 ) return 2;
        return 1;
    }

    /**
     * 사용자 티어 조회
     */
    public static function get_user_tier( $user_id ) {
        global $wpdb;
        $stats_table = $wpdb->prefix . 'jj_referral_user_stats';
        $tiers_table = $wpdb->prefix . 'jj_referral_tiers';

        $conversions = $wpdb->get_var( $wpdb->prepare(
            "SELECT total_conversions FROM $stats_table WHERE user_id = %d",
            $user_id
        ) );

        $conversions = $conversions ?: 0;

        $tier = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $tiers_table
             WHERE min_referrals <= %d AND is_active = 1
             ORDER BY min_referrals DESC LIMIT 1",
            $conversions
        ) );

        if ( ! $tier ) {
            // 기본 티어
            $tier = $wpdb->get_row( "SELECT * FROM $tiers_table ORDER BY id LIMIT 1" );
        }

        return $tier;
    }

    /**
     * 사용자 통계 업데이트
     */
    private static function update_user_stats( $user_id, $field, $value ) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_user_stats';

        $wpdb->query( $wpdb->prepare(
            "INSERT INTO $table (user_id, $field)
             VALUES (%d, %f)
             ON DUPLICATE KEY UPDATE $field = $field + %f",
            $user_id, $value, $value
        ) );
    }

    /**
     * 활성 할인 조회
     */
    public static function get_active_discount( $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_rewards';

        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table
             WHERE user_id = %d
             AND reward_type = 'discount_percent'
             AND status = 'active'
             AND (expires_at IS NULL OR expires_at > NOW())
             ORDER BY value DESC LIMIT 1",
            $user_id
        ) );
    }

    /**
     * 할인 적용
     */
    public static function apply_discount( $user_id, $reward_id, $order_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_rewards';

        $wpdb->update(
            $table,
            array(
                'status'           => 'applied',
                'applied_at'       => current_time( 'mysql' ),
                'applied_to_order' => $order_id,
            ),
            array( 'id' => $reward_id )
        );
    }
}
```

---

## 4. REST API 엔드포인트

### 4.1 API 구조

```php
<?php
/**
 * REST API 엔드포인트
 */
class JJ_Referral_API {

    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
        $namespace = 'jj-referral/v1';

        // 내 레퍼럴 코드
        register_rest_route( $namespace, '/code', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_my_code' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ) );

        // 레퍼럴 통계
        register_rest_route( $namespace, '/stats', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_my_stats' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ) );

        // 레퍼럴 목록
        register_rest_route( $namespace, '/referrals', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_my_referrals' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ) );

        // 보상 목록
        register_rest_route( $namespace, '/rewards', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_my_rewards' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ) );

        // 리더보드
        register_rest_route( $namespace, '/leaderboard', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_leaderboard' ),
            'permission_callback' => '__return_true',
        ) );

        // 커스텀 코드 설정
        register_rest_route( $namespace, '/code/custom', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'set_custom_code' ),
            'permission_callback' => array( $this, 'check_user_permission' ),
        ) );
    }

    public function get_my_code( $request ) {
        $user_id = get_current_user_id();
        $code = JJ_Referral_Code::generate( $user_id );
        $url = JJ_Referral_Code::get_referral_url( $user_id );

        return rest_ensure_response( array(
            'code' => $code,
            'url'  => $url,
        ) );
    }

    public function get_my_stats( $request ) {
        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'jj_referral_user_stats';

        $stats = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d",
            $user_id
        ) );

        $tier = JJ_Referral_Rewards::get_user_tier( $user_id );

        return rest_ensure_response( array(
            'stats' => $stats,
            'tier'  => $tier,
        ) );
    }

    public function get_leaderboard( $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'jj_referral_user_stats';

        $period = $request->get_param( 'period' ) ?: 'all';
        $limit = min( (int) $request->get_param( 'limit' ) ?: 10, 100 );

        $leaders = $wpdb->get_results( $wpdb->prepare(
            "SELECT u.ID, u.display_name, s.total_conversions, s.xp_points, s.level
             FROM $table s
             JOIN {$wpdb->users} u ON s.user_id = u.ID
             ORDER BY s.total_conversions DESC
             LIMIT %d",
            $limit
        ) );

        return rest_ensure_response( $leaders );
    }

    public function check_user_permission() {
        return is_user_logged_in();
    }
}
```

---

## 5. 프론트엔드 구현

### 5.1 사용자 대시보드

```html
<!-- templates/dashboard.php -->
<div class="jj-referral-dashboard">
    <div class="jj-ref-header">
        <h2>추천 프로그램</h2>
        <div class="jj-ref-tier">
            <span class="tier-badge" style="background: <?php echo $tier->color_code; ?>">
                <?php echo esc_html( $tier->tier_name ); ?>
            </span>
        </div>
    </div>

    <!-- 통계 카드 -->
    <div class="jj-ref-stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👆</div>
            <div class="stat-value"><?php echo number_format( $stats->total_clicks ); ?></div>
            <div class="stat-label">총 클릭</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-value"><?php echo number_format( $stats->total_signups ); ?></div>
            <div class="stat-label">가입</div>
        </div>
        <div class="stat-card highlight">
            <div class="stat-icon">💰</div>
            <div class="stat-value"><?php echo number_format( $stats->total_conversions ); ?></div>
            <div class="stat-label">전환</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎁</div>
            <div class="stat-value">$<?php echo number_format( $stats->total_commission_earned, 2 ); ?></div>
            <div class="stat-label">총 수익</div>
        </div>
    </div>

    <!-- 레퍼럴 링크 -->
    <div class="jj-ref-link-section">
        <h3>내 추천 링크</h3>
        <div class="link-input-group">
            <input type="text" id="referral-url" value="<?php echo esc_url( $referral_url ); ?>" readonly>
            <button class="btn-copy" data-copy="referral-url">복사</button>
        </div>
        <div class="share-buttons">
            <button class="btn-share twitter" data-network="twitter">Twitter</button>
            <button class="btn-share facebook" data-network="facebook">Facebook</button>
            <button class="btn-share linkedin" data-network="linkedin">LinkedIn</button>
            <button class="btn-share email" data-network="email">Email</button>
        </div>
    </div>

    <!-- 보상 현황 -->
    <div class="jj-ref-rewards-section">
        <h3>활성 보상</h3>
        <div class="rewards-list">
            <?php foreach ( $active_rewards as $reward ) : ?>
            <div class="reward-item <?php echo $reward->reward_type; ?>">
                <div class="reward-icon"><?php echo self::get_reward_icon( $reward->reward_type ); ?></div>
                <div class="reward-details">
                    <div class="reward-title"><?php echo esc_html( $reward->description ); ?></div>
                    <div class="reward-expires">만료: <?php echo date( 'Y-m-d', strtotime( $reward->expires_at ) ); ?></div>
                </div>
                <div class="reward-value"><?php echo self::format_reward_value( $reward ); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 레벨/XP -->
    <div class="jj-ref-level-section">
        <h3>레벨 <?php echo $stats->level; ?></h3>
        <div class="xp-bar">
            <div class="xp-progress" style="width: <?php echo $xp_percent; ?>%"></div>
        </div>
        <div class="xp-text"><?php echo number_format( $stats->xp_points ); ?> XP</div>
    </div>
</div>
```

### 5.2 공유 위젯 JavaScript

```javascript
// assets/js/referral-share.js

class JJReferralShare {
    constructor() {
        this.init();
    }

    init() {
        // 복사 버튼
        document.querySelectorAll('.btn-copy').forEach(btn => {
            btn.addEventListener('click', (e) => this.copyToClipboard(e));
        });

        // 공유 버튼
        document.querySelectorAll('.btn-share').forEach(btn => {
            btn.addEventListener('click', (e) => this.share(e));
        });
    }

    copyToClipboard(e) {
        const targetId = e.target.dataset.copy;
        const input = document.getElementById(targetId);

        navigator.clipboard.writeText(input.value).then(() => {
            const originalText = e.target.textContent;
            e.target.textContent = '복사됨!';
            e.target.classList.add('copied');

            setTimeout(() => {
                e.target.textContent = originalText;
                e.target.classList.remove('copied');
            }, 2000);
        });
    }

    share(e) {
        const network = e.target.dataset.network;
        const url = document.getElementById('referral-url').value;
        const text = '3J Labs ACF CSS로 WordPress 디자인을 더 쉽게 관리하세요!';

        let shareUrl = '';

        switch (network) {
            case 'twitter':
                shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
                break;
            case 'facebook':
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                break;
            case 'linkedin':
                shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
                break;
            case 'email':
                shareUrl = `mailto:?subject=${encodeURIComponent('3J Labs 추천')}&body=${encodeURIComponent(text + '\n\n' + url)}`;
                break;
        }

        if (shareUrl) {
            window.open(shareUrl, '_blank', 'width=600,height=400');
        }
    }
}

// 초기화
document.addEventListener('DOMContentLoaded', () => {
    new JJReferralShare();
});
```

---

## 6. 구현 일정

### Phase 1: 기반 구축 (Week 1)
| 일자 | 작업 |
|------|------|
| Day 1 | DB 스키마 생성, 기본 클래스 구조 |
| Day 2 | JJ_Referral_Code 클래스 완성 |
| Day 3 | JJ_Referral_Tracker 클래스 완성 |
| Day 4 | JJ_Referral_Rewards 클래스 완성 |
| Day 5 | WordPress 훅 연동 (가입, 주문) |

### Phase 2: API & UI (Week 2)
| 일자 | 작업 |
|------|------|
| Day 1 | REST API 엔드포인트 구현 |
| Day 2 | 사용자 대시보드 UI |
| Day 3 | 관리자 대시보드 UI |
| Day 4 | 공유 위젯, 복사 기능 |
| Day 5 | 이메일 템플릿 |

### Phase 3: 테스트 & 배포 (Week 3)
| 일자 | 작업 |
|------|------|
| Day 1-2 | 단위/통합 테스트 |
| Day 3 | 버그 수정 |
| Day 4 | 문서화 |
| Day 5 | 배포 |

---

## 7. KPI 목표

| 지표 | 1개월 | 3개월 | 6개월 |
|------|-------|-------|-------|
| 레퍼럴 코드 생성 | 100 | 500 | 2,000 |
| 총 클릭 | 500 | 3,000 | 15,000 |
| 가입 전환율 | 8% | 12% | 15% |
| 구매 전환율 | 3% | 5% | 8% |
| K-Factor | 0.5 | 0.8 | 1.2 |
| MRR 기여 | $500 | $2,000 | $10,000 |

---

## 8. Neural Link 통합 상세

### 8.1 라이센스 연동 (무료 일수 자동 적용)

```php
/**
 * Neural Link와 레퍼럴 무료 일수 연동
 * /wp-json/jj-neural-link/v1/ 엔드포인트 활용
 */
class JJ_Referral_Neural_Bridge {

    /**
     * 무료 일수를 Neural Link 라이센스에 적용
     */
    public static function apply_free_days_to_license( $user_id, $days ) {
        // 사용자의 활성 라이센스 조회
        $license = JJ_License_Manager::get_user_license( $user_id );

        if ( ! $license ) {
            // 라이센스가 없으면 Free 버전 확장
            return self::extend_free_trial( $user_id, $days );
        }

        // 라이센스 만료일 연장
        $current_expiry = strtotime( $license->expires_at );
        $new_expiry = $current_expiry + ( $days * DAY_IN_SECONDS );

        return JJ_License_Manager::update_expiry(
            $license->license_key,
            date( 'Y-m-d H:i:s', $new_expiry )
        );
    }

    /**
     * 레퍼럴 이벤트를 Neural Link에 전송
     */
    public static function sync_referral_event( $event_type, $data ) {
        $endpoint = rest_url( 'jj-neural-link/v1/referral-event' );

        return wp_remote_post( $endpoint, array(
            'body' => array(
                'event_type' => $event_type,
                'data'       => $data,
                'site_url'   => home_url(),
                'timestamp'  => current_time( 'timestamp' ),
            ),
            'headers' => array(
                'X-JJ-License' => JJ_License_Manager::get_site_license(),
            ),
        ) );
    }
}
```

### 8.2 보안 검증 통합 (Phase 39.3)

```php
/**
 * 레퍼럴 보안 검증 (License Tampering Detection 활용)
 */
class JJ_Referral_Security {

    /**
     * 레퍼럴 보상 지급 전 무결성 검증
     */
    public static function verify_before_reward( $user_id, $reward_type ) {
        // 1. 라이센스 무결성 검증
        if ( class_exists( 'JJ_License_Security' ) ) {
            $integrity = JJ_License_Security::verify_license_integrity();
            if ( is_wp_error( $integrity ) ) {
                return new WP_Error( 'license_tampered', '라이센스 변조가 감지되었습니다.' );
            }
        }

        // 2. 비정상 사용 패턴 감지
        if ( class_exists( 'JJ_License_Security' ) ) {
            $abnormal = JJ_License_Security::detect_abnormal_usage( $user_id );
            if ( $abnormal ) {
                self::log_security_event( $user_id, 'abnormal_referral_activity' );
                return new WP_Error( 'suspicious_activity', '비정상적인 활동이 감지되었습니다.' );
            }
        }

        return true;
    }
}
```

---

## 9. Nudge Flow 연동

### 9.1 레퍼럴 기반 넛지 트리거

```php
/**
 * Nudge Flow 워크플로우에서 사용할 레퍼럴 트리거
 */
class JJ_Referral_Nudge_Triggers {

    /**
     * 레퍼럴 트리거 등록
     */
    public static function register_triggers() {
        // 가입 완료 트리거
        add_action( 'jj_referral_signup', function( $referrer_id, $referred_id ) {
            do_action( 'jj_nudge_trigger', 'referral_signup', array(
                'referrer_id'  => $referrer_id,
                'referred_id'  => $referred_id,
                'timestamp'    => current_time( 'timestamp' ),
            ) );
        }, 10, 2 );

        // 전환 완료 트리거
        add_action( 'jj_referral_conversion', function( $referrer_id, $order_id ) {
            do_action( 'jj_nudge_trigger', 'referral_conversion', array(
                'referrer_id' => $referrer_id,
                'order_id'    => $order_id,
            ) );
        }, 10, 2 );

        // 티어 업그레이드 트리거
        add_action( 'jj_referral_tier_up', function( $user_id, $new_tier ) {
            do_action( 'jj_nudge_trigger', 'referral_tier_up', array(
                'user_id'  => $user_id,
                'new_tier' => $new_tier,
            ) );
        }, 10, 2 );
    }
}
```

### 9.2 레퍼럴 넛지 프리셋 템플릿

```php
/**
 * 사전 정의된 레퍼럴 넛지 템플릿
 */
$referral_nudge_presets = array(
    'referral_welcome' => array(
        'title'       => '추천 프로그램 안내',
        'trigger'     => 'user_login',
        'condition'   => 'referral_count == 0',
        'content'     => '친구를 추천하고 30일 무료 사용을 받으세요! 🎁',
        'cta'         => '추천 링크 받기',
        'cta_url'     => '/my-account/referrals/',
        'nudge_type'  => 'toast',
        'position'    => 'bottom-right',
    ),
    'referral_success' => array(
        'title'       => '추천 성공!',
        'trigger'     => 'jj_referral_signup',
        'content'     => '축하합니다! 새로운 친구가 가입했어요. 30일 무료 사용이 추가되었습니다! 🎉',
        'nudge_type'  => 'modal',
        'auto_close'  => 5000,
    ),
    'referral_tier_up' => array(
        'title'       => '레벨 업!',
        'trigger'     => 'jj_referral_tier_up',
        'content'     => '축하합니다! {{tier_name}} 티어로 승급했습니다. 더 큰 보상을 받으세요!',
        'nudge_type'  => 'spotlight',
        'confetti'    => true,
    ),
);
```

---

## 10. Analytics Dashboard 연동

### 10.1 레퍼럴 위젯 데이터 제공

```php
/**
 * JJ Analytics Dashboard에 레퍼럴 데이터 제공
 */
class JJ_Referral_Analytics_Widget {

    /**
     * 대시보드 위젯 데이터
     */
    public static function get_widget_data() {
        global $wpdb;

        $stats = $wpdb->get_row( "
            SELECT
                COUNT(DISTINCT referral_code) as total_codes,
                SUM(click_count) as total_clicks,
                SUM(signup_count) as total_signups,
                SUM(conversion_count) as total_conversions,
                SUM(total_revenue) as total_revenue,
                AVG(CASE WHEN click_count > 0
                    THEN signup_count / click_count * 100
                    ELSE 0 END) as avg_signup_rate,
                AVG(CASE WHEN signup_count > 0
                    THEN conversion_count / signup_count * 100
                    ELSE 0 END) as avg_conversion_rate
            FROM {$wpdb->prefix}jj_referral_codes
            WHERE is_active = 1
        " );

        // K-Factor 계산
        $k_factor = self::calculate_k_factor();

        return array(
            'total_codes'        => (int) $stats->total_codes,
            'total_clicks'       => (int) $stats->total_clicks,
            'total_signups'      => (int) $stats->total_signups,
            'total_conversions'  => (int) $stats->total_conversions,
            'total_revenue'      => (float) $stats->total_revenue,
            'avg_signup_rate'    => round( $stats->avg_signup_rate, 2 ),
            'avg_conversion_rate'=> round( $stats->avg_conversion_rate, 2 ),
            'k_factor'           => $k_factor,
            'trend'              => self::get_trend_data( 30 ),
        );
    }

    /**
     * K-Factor (바이럴 계수) 계산
     * K = i * c (초대 수 × 전환율)
     */
    private static function calculate_k_factor() {
        global $wpdb;

        $data = $wpdb->get_row( "
            SELECT
                AVG(signup_count) as avg_invites,
                AVG(CASE WHEN signup_count > 0
                    THEN conversion_count / signup_count
                    ELSE 0 END) as avg_conversion
            FROM {$wpdb->prefix}jj_referral_user_stats
            WHERE total_signups > 0
        " );

        return round( $data->avg_invites * $data->avg_conversion, 2 );
    }
}
```

---

## 11. 공유 기능 UI (Phase 40.3 연동)

### 11.1 소셜 공유 위젯

```html
<!-- 카카오톡, 페이스북, 트위터 공유 위젯 -->
<div class="jj-share-widget" data-referral-code="<?php echo $code; ?>">
    <h4>친구에게 공유하기</h4>

    <!-- 카카오톡 -->
    <button class="share-btn kakao" onclick="JJShare.kakao()">
        <img src="<?php echo plugins_url('assets/icons/kakao.svg', __FILE__); ?>" alt="카카오톡">
        카카오톡
    </button>

    <!-- 페이스북 -->
    <button class="share-btn facebook" onclick="JJShare.facebook()">
        <svg><!-- FB icon --></svg>
        페이스북
    </button>

    <!-- 트위터/X -->
    <button class="share-btn twitter" onclick="JJShare.twitter()">
        <svg><!-- X icon --></svg>
        트위터
    </button>

    <!-- 이메일 -->
    <button class="share-btn email" onclick="JJShare.email()">
        <svg><!-- Email icon --></svg>
        이메일
    </button>

    <!-- 링크 복사 -->
    <div class="copy-link-section">
        <input type="text" value="<?php echo $referral_url; ?>" readonly>
        <button class="btn-copy" onclick="JJShare.copyLink()">복사</button>
    </div>
</div>
```

### 11.2 Open Graph 메타 태그

```php
/**
 * 레퍼럴 랜딩 페이지 Open Graph 태그
 */
function jj_referral_og_meta() {
    if ( ! isset( $_GET['ref'] ) ) return;

    $referrer = JJ_Referral_Code::get_user_by_code( $_GET['ref'] );
    $referrer_name = $referrer ? get_userdata( $referrer )->display_name : '친구';

    echo '<meta property="og:title" content="' . $referrer_name . '님이 추천하는 3J Labs ACF CSS">' . "\n";
    echo '<meta property="og:description" content="WordPress 디자인을 더 쉽게! 지금 가입하면 20% 할인">' . "\n";
    echo '<meta property="og:image" content="https://3j-labs.com/images/og-referral.png">' . "\n";
    echo '<meta property="og:type" content="website">' . "\n";
}
add_action( 'wp_head', 'jj_referral_og_meta' );
```

---

## 12. 게이미피케이션 확장 (Phase 40.5 연동)

### 12.1 배지 시스템

```php
/**
 * 레퍼럴 배지 정의
 */
$referral_badges = array(
    'first_referral' => array(
        'name'        => '첫 추천',
        'description' => '첫 번째 친구를 추천했습니다',
        'icon'        => '🌟',
        'condition'   => 'total_signups >= 1',
        'xp_reward'   => 50,
    ),
    'referral_5' => array(
        'name'        => '추천 마스터',
        'description' => '5명의 친구를 추천했습니다',
        'icon'        => '🏆',
        'condition'   => 'total_signups >= 5',
        'xp_reward'   => 200,
    ),
    'referral_10' => array(
        'name'        => '추천 챔피언',
        'description' => '10명의 친구를 추천했습니다',
        'icon'        => '👑',
        'condition'   => 'total_signups >= 10',
        'xp_reward'   => 500,
    ),
    'conversion_king' => array(
        'name'        => '전환왕',
        'description' => '10건의 유료 전환을 달성했습니다',
        'icon'        => '💎',
        'condition'   => 'total_conversions >= 10',
        'xp_reward'   => 1000,
    ),
    'streak_7' => array(
        'name'        => '7일 연속',
        'description' => '7일 연속으로 추천 클릭이 발생했습니다',
        'icon'        => '🔥',
        'condition'   => 'streak_days >= 7',
        'xp_reward'   => 300,
    ),
);
```

### 12.2 리더보드

```php
/**
 * 실시간 리더보드 (월간/전체)
 */
class JJ_Referral_Leaderboard {

    public static function get_monthly_leaders( $limit = 10 ) {
        global $wpdb;

        return $wpdb->get_results( $wpdb->prepare( "
            SELECT
                u.ID,
                u.display_name,
                COUNT(t.id) as monthly_conversions,
                SUM(t.commission_amount) as monthly_earnings
            FROM {$wpdb->prefix}jj_referral_tracking t
            JOIN {$wpdb->users} u ON t.referrer_id = u.ID
            WHERE t.status = 'converted'
            AND t.converted_at >= DATE_FORMAT(NOW(), '%%Y-%%m-01')
            GROUP BY t.referrer_id
            ORDER BY monthly_conversions DESC
            LIMIT %d
        ", $limit ) );
    }
}
```

---

## 13. 우선순위 및 Phase 매핑

### Phase 40.2 (즉시) - 레퍼럴 기반 구축
- [ ] DB 테이블 7개 생성
- [ ] JJ_Referral_Code 클래스 구현
- [ ] JJ_Referral_Tracker 클래스 구현
- [ ] JJ_Referral_Rewards 클래스 구현
- [ ] WordPress 훅 연동 (user_register, woocommerce_order_status_completed)

### Phase 40.3 (다음) - 공유 기능 UI
- [ ] 소셜 공유 위젯 (카카오톡, 페이스북, 트위터)
- [ ] Open Graph 메타 태그
- [ ] 공유 성과 추적

### Phase 40.4 (이후) - 인센티브 시스템
- [ ] Neural Link 무료 일수 연동
- [ ] WooCommerce 쿠폰 자동 생성
- [ ] 커미션 자동 계산 및 지급

### Phase 40.5 (단기) - 게이미피케이션
- [ ] 배지 시스템
- [ ] 리더보드
- [ ] XP/레벨 시스템

---

**© 2026 3J Labs. All rights reserved.**

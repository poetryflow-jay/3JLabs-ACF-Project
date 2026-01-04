-- ============================================
-- ACF Nudge Flow - 외부 데이터 소스 통합 확장
-- 버전: 1.0.0
-- 생성일: 2026-01-04
-- 용도: WooCommerce, 회원 데이터, LMS, Analytics 등 통합
-- ============================================
--
-- 지원 데이터 소스:
-- [E-Commerce] WooCommerce
-- [Membership] Ultimate Member, Advanced CF for ACF
-- [Community] BuddyPress, Fluent Community
-- [LMS] LearnDash, LifterLMS, Tutor LMS
-- [Gamification] GamiPress, myCRED
-- [Analytics] Google Analytics (Monster Insights), Microsoft Clarity, Hotjar
-- ============================================


-- ============================================
-- 1. 데이터 소스 레지스트리 테이블
-- 활성화된 외부 데이터 소스 관리
-- ============================================
DROP TABLE IF EXISTS {$wpdb->prefix}nf_data_sources;
CREATE TABLE {$wpdb->prefix}nf_data_sources (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    source_key VARCHAR(50) NOT NULL UNIQUE COMMENT '데이터 소스 키',
    source_name VARCHAR(100) NOT NULL COMMENT '데이터 소스명',
    source_type ENUM('ecommerce', 'membership', 'community', 'lms', 'gamification', 'analytics', 'custom') NOT NULL COMMENT '소스 타입',
    plugin_slug VARCHAR(100) COMMENT '플러그인 슬러그',
    is_detected TINYINT(1) DEFAULT 0 COMMENT '플러그인 감지 여부',
    is_enabled TINYINT(1) DEFAULT 0 COMMENT '통합 활성화 여부',
    api_version VARCHAR(20) COMMENT 'API 버전',
    connection_config JSON COMMENT '연결 설정',
    sync_interval INT DEFAULT 3600 COMMENT '동기화 주기 (초)',
    last_sync_at TIMESTAMP NULL COMMENT '마지막 동기화',
    sync_status ENUM('pending', 'running', 'success', 'error') DEFAULT 'pending',
    error_message TEXT COMMENT '오류 메시지',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_source_type (source_type),
    INDEX idx_enabled (is_enabled),
    INDEX idx_detected (is_detected)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='외부 데이터 소스 레지스트리';

-- 지원 데이터 소스 초기 데이터
INSERT INTO {$wpdb->prefix}nf_data_sources
(source_key, source_name, source_type, plugin_slug, api_version) VALUES
-- E-Commerce
('woocommerce', 'WooCommerce', 'ecommerce', 'woocommerce/woocommerce.php', '8.0'),
('edd', 'Easy Digital Downloads', 'ecommerce', 'easy-digital-downloads/easy-digital-downloads.php', '3.0'),

-- Membership
('ultimate_member', 'Ultimate Member', 'membership', 'ultimate-member/ultimate-member.php', '2.8'),
('acf_member', 'Advanced CF for ACF', 'membership', 'acf-extended-pro/acf-extended-pro.php', '1.0'),
('memberpress', 'MemberPress', 'membership', 'memberpress/memberpress.php', '1.0'),
('pmpro', 'Paid Memberships Pro', 'membership', 'paid-memberships-pro/paid-memberships-pro.php', '2.0'),
('restrict_content_pro', 'Restrict Content Pro', 'membership', 'restrict-content-pro/restrict-content-pro.php', '3.0'),
('woo_memberships', 'WooCommerce Memberships', 'membership', 'woocommerce-memberships/woocommerce-memberships.php', '1.0'),

-- Community
('buddypress', 'BuddyPress', 'community', 'buddypress/bp-loader.php', '12.0'),
('buddyboss', 'BuddyBoss Platform', 'community', 'buddyboss-platform/bp-loader.php', '2.0'),
('fluent_community', 'Fluent Community', 'community', 'fluent-community/fluent-community.php', '1.0'),
('peepso', 'PeepSo', 'community', 'peepso-core/peepso.php', '6.0'),

-- LMS
('learndash', 'LearnDash', 'lms', 'sfwd-lms/sfwd_lms.php', '4.0'),
('lifterlms', 'LifterLMS', 'lms', 'lifterlms/lifterlms.php', '7.0'),
('tutor_lms', 'Tutor LMS', 'lms', 'tutor/tutor.php', '2.6'),
('learnpress', 'LearnPress', 'lms', 'learnpress/learnpress.php', '4.0'),
('sensei', 'Sensei LMS', 'lms', 'sensei-lms/sensei-lms.php', '4.0'),

-- Gamification
('gamipress', 'GamiPress', 'gamification', 'gamipress/gamipress.php', '2.0'),
('mycred', 'myCRED', 'gamification', 'mycred/mycred.php', '2.0'),

-- Analytics
('monster_insights', 'MonsterInsights (GA4)', 'analytics', 'google-analytics-for-wordpress/googleanalytics.php', '8.0'),
('ga4', 'Google Analytics 4', 'analytics', NULL, '4.0'),
('clarity', 'Microsoft Clarity', 'analytics', NULL, '1.0'),
('hotjar', 'Hotjar', 'analytics', NULL, '1.0'),
('matomo', 'Matomo Analytics', 'analytics', 'matomo/matomo.php', '5.0'),
('plausible', 'Plausible Analytics', 'analytics', 'plausible-analytics/plausible-analytics.php', '2.0');


-- ============================================
-- 2. 통합 고객 프로필 테이블
-- 모든 데이터 소스의 통합 뷰
-- ============================================
DROP TABLE IF EXISTS {$wpdb->prefix}nf_unified_customer_profile;
CREATE TABLE {$wpdb->prefix}nf_unified_customer_profile (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- 기본 식별자
    wp_user_id BIGINT UNSIGNED COMMENT 'WordPress 사용자 ID',
    email VARCHAR(100) COMMENT '이메일 (비회원 포함)',
    visitor_id VARCHAR(100) COMMENT '익명 방문자 ID',

    -- ============================================
    -- WooCommerce 데이터
    -- ============================================
    wc_customer_id BIGINT UNSIGNED COMMENT 'WooCommerce 고객 ID',
    wc_total_orders INT DEFAULT 0 COMMENT '총 주문 수',
    wc_total_spent DECIMAL(12,2) DEFAULT 0 COMMENT '총 구매액',
    wc_avg_order_value DECIMAL(10,2) DEFAULT 0 COMMENT '평균 주문액',
    wc_last_order_date DATE COMMENT '마지막 주문일',
    wc_first_order_date DATE COMMENT '첫 주문일',
    wc_favorite_category VARCHAR(100) COMMENT '주로 구매 카테고리',
    wc_favorite_product_ids JSON COMMENT '자주 구매 상품 ID 목록',
    wc_cart_value DECIMAL(10,2) DEFAULT 0 COMMENT '현재 장바구니 금액',
    wc_cart_items INT DEFAULT 0 COMMENT '현재 장바구니 상품 수',
    wc_wishlist_count INT DEFAULT 0 COMMENT '위시리스트 상품 수',

    -- RFM 점수
    rfm_r_score TINYINT DEFAULT 1,
    rfm_f_score TINYINT DEFAULT 1,
    rfm_m_score TINYINT DEFAULT 1,
    rfm_segment_id INT UNSIGNED COMMENT 'RFM 세그먼트 ID',

    -- ============================================
    -- Ultimate Member / Membership 데이터
    -- ============================================
    um_membership_level VARCHAR(100) COMMENT '회원 등급',
    um_membership_status ENUM('active', 'expired', 'pending', 'cancelled') COMMENT '멤버십 상태',
    um_membership_start_date DATE COMMENT '멤버십 시작일',
    um_membership_end_date DATE COMMENT '멤버십 종료일',
    um_profile_completeness TINYINT DEFAULT 0 COMMENT '프로필 완성도 (%)',
    um_custom_fields JSON COMMENT '커스텀 필드 데이터',

    -- ============================================
    -- BuddyPress / Community 데이터
    -- ============================================
    bp_friends_count INT DEFAULT 0 COMMENT '친구 수',
    bp_groups_count INT DEFAULT 0 COMMENT '가입 그룹 수',
    bp_activity_count INT DEFAULT 0 COMMENT '활동 수',
    bp_messages_sent INT DEFAULT 0 COMMENT '보낸 메시지 수',
    bp_last_activity_date DATE COMMENT '마지막 활동일',
    bp_profile_views INT DEFAULT 0 COMMENT '프로필 조회수',

    -- Fluent Community
    fc_community_score INT DEFAULT 0 COMMENT '커뮤니티 점수',
    fc_posts_count INT DEFAULT 0 COMMENT '작성 글 수',
    fc_comments_count INT DEFAULT 0 COMMENT '댓글 수',
    fc_reactions_received INT DEFAULT 0 COMMENT '받은 반응 수',

    -- ============================================
    -- LearnDash / LMS 데이터
    -- ============================================
    ld_enrolled_courses INT DEFAULT 0 COMMENT '등록 강좌 수',
    ld_completed_courses INT DEFAULT 0 COMMENT '수료 강좌 수',
    ld_course_progress_avg TINYINT DEFAULT 0 COMMENT '평균 진도율 (%)',
    ld_quiz_scores_avg DECIMAL(5,2) DEFAULT 0 COMMENT '평균 퀴즈 점수',
    ld_certificates_earned INT DEFAULT 0 COMMENT '획득 수료증 수',
    ld_last_lesson_date DATE COMMENT '마지막 수강일',
    ld_total_learning_time INT DEFAULT 0 COMMENT '총 학습 시간 (분)',
    ld_course_ids JSON COMMENT '등록된 강좌 ID 목록',

    -- ============================================
    -- GamiPress / Gamification 데이터
    -- ============================================
    gp_points INT DEFAULT 0 COMMENT 'GamiPress 포인트',
    gp_achievements INT DEFAULT 0 COMMENT '획득 업적 수',
    gp_rank VARCHAR(50) COMMENT '현재 랭크',
    gp_rank_id INT UNSIGNED COMMENT '랭크 ID',
    gp_badges JSON COMMENT '획득 배지 목록',
    gp_last_activity_date DATE COMMENT '마지막 게임화 활동일',

    -- myCRED
    mc_balance DECIMAL(12,2) DEFAULT 0 COMMENT 'myCRED 잔액',
    mc_total_earned DECIMAL(12,2) DEFAULT 0 COMMENT '총 획득 포인트',
    mc_total_spent DECIMAL(12,2) DEFAULT 0 COMMENT '총 사용 포인트',

    -- ============================================
    -- Analytics 데이터 (GA4, Clarity, Hotjar)
    -- ============================================
    -- Google Analytics / Monster Insights
    ga_sessions_count INT DEFAULT 0 COMMENT 'GA 세션 수 (30일)',
    ga_pageviews INT DEFAULT 0 COMMENT 'GA 페이지뷰 (30일)',
    ga_avg_session_duration INT DEFAULT 0 COMMENT '평균 세션 시간 (초)',
    ga_bounce_rate DECIMAL(5,2) DEFAULT 0 COMMENT '이탈률 (%)',
    ga_events_count INT DEFAULT 0 COMMENT '이벤트 수',
    ga_conversions INT DEFAULT 0 COMMENT '전환 수',
    ga_traffic_source VARCHAR(100) COMMENT '주요 유입 소스',
    ga_device_category VARCHAR(20) COMMENT '주요 디바이스',
    ga_country VARCHAR(50) COMMENT '주요 국가',

    -- Microsoft Clarity
    clarity_sessions INT DEFAULT 0 COMMENT 'Clarity 세션 수',
    clarity_pages_per_session DECIMAL(4,2) DEFAULT 0 COMMENT '세션당 페이지',
    clarity_scroll_depth_avg TINYINT DEFAULT 0 COMMENT '평균 스크롤 깊이 (%)',
    clarity_rage_clicks INT DEFAULT 0 COMMENT 'Rage 클릭 수',
    clarity_dead_clicks INT DEFAULT 0 COMMENT 'Dead 클릭 수',
    clarity_quick_backs INT DEFAULT 0 COMMENT 'Quick Back 수',
    clarity_excessive_scrolling TINYINT(1) DEFAULT 0 COMMENT '과도한 스크롤 여부',
    clarity_frustration_score TINYINT DEFAULT 0 COMMENT '불만족 점수 (0-100)',
    clarity_engagement_score TINYINT DEFAULT 0 COMMENT '참여도 점수 (0-100)',

    -- Hotjar
    hotjar_sessions INT DEFAULT 0 COMMENT 'Hotjar 세션 수',
    hotjar_recordings_count INT DEFAULT 0 COMMENT '녹화 세션 수',
    hotjar_feedback_count INT DEFAULT 0 COMMENT '피드백 수',
    hotjar_survey_responses INT DEFAULT 0 COMMENT '설문 응답 수',
    hotjar_nps_score TINYINT COMMENT 'NPS 점수',
    hotjar_heatmap_clicks JSON COMMENT '히트맵 클릭 데이터',

    -- ============================================
    -- 행동 점수 (통합 계산)
    -- ============================================
    engagement_score TINYINT DEFAULT 0 COMMENT '참여도 점수 (0-100)',
    loyalty_score TINYINT DEFAULT 0 COMMENT '충성도 점수 (0-100)',
    purchase_intent_score TINYINT DEFAULT 0 COMMENT '구매 의향 점수 (0-100)',
    churn_risk_score TINYINT DEFAULT 0 COMMENT '이탈 위험 점수 (0-100)',
    lifetime_value_score TINYINT DEFAULT 0 COMMENT 'LTV 점수 (0-100)',

    -- 예측 데이터
    predicted_next_purchase_date DATE COMMENT '예상 다음 구매일',
    predicted_ltv DECIMAL(12,2) COMMENT '예상 고객 생애 가치',
    predicted_churn_probability DECIMAL(5,4) COMMENT '이탈 확률',

    -- ============================================
    -- 세그먼트 및 태그
    -- ============================================
    auto_segments JSON COMMENT '자동 부여 세그먼트',
    manual_tags JSON COMMENT '수동 태그',
    custom_attributes JSON COMMENT '커스텀 속성',

    -- 메타 정보
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_sync_woocommerce TIMESTAMP NULL,
    last_sync_membership TIMESTAMP NULL,
    last_sync_community TIMESTAMP NULL,
    last_sync_lms TIMESTAMP NULL,
    last_sync_gamification TIMESTAMP NULL,
    last_sync_analytics TIMESTAMP NULL,

    UNIQUE KEY uk_wp_user (wp_user_id),
    UNIQUE KEY uk_email (email),
    UNIQUE KEY uk_visitor (visitor_id),
    INDEX idx_wc_customer (wc_customer_id),
    INDEX idx_rfm (rfm_segment_id, rfm_r_score, rfm_f_score),
    INDEX idx_membership (um_membership_status, um_membership_level),
    INDEX idx_scores (engagement_score, loyalty_score, churn_risk_score),
    INDEX idx_updated (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='통합 고객 프로필';


-- ============================================
-- 3. 데이터 소스별 상세 테이블
-- ============================================

-- 3.1 WooCommerce 주문 상세
DROP TABLE IF EXISTS {$wpdb->prefix}nf_wc_order_analytics;
CREATE TABLE {$wpdb->prefix}nf_wc_order_analytics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profile_id BIGINT UNSIGNED NOT NULL COMMENT '통합 프로필 FK',
    order_id BIGINT UNSIGNED NOT NULL COMMENT 'WC 주문 ID',
    order_date DATETIME NOT NULL,
    order_status VARCHAR(50),
    order_total DECIMAL(12,2),
    order_items_count INT,
    payment_method VARCHAR(50),
    shipping_method VARCHAR(100),
    coupon_used VARCHAR(100),
    discount_amount DECIMAL(10,2) DEFAULT 0,
    product_categories JSON COMMENT '주문 상품 카테고리',
    product_ids JSON COMMENT '주문 상품 ID',
    is_first_order TINYINT(1) DEFAULT 0,
    days_since_last_order INT COMMENT '이전 주문 이후 일수',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_profile (profile_id),
    INDEX idx_order (order_id),
    INDEX idx_date (order_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.2 LMS 학습 활동 상세
DROP TABLE IF EXISTS {$wpdb->prefix}nf_lms_activity;
CREATE TABLE {$wpdb->prefix}nf_lms_activity (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profile_id BIGINT UNSIGNED NOT NULL,
    lms_source ENUM('learndash', 'lifterlms', 'tutor_lms', 'learnpress', 'sensei') NOT NULL,
    course_id BIGINT UNSIGNED,
    lesson_id BIGINT UNSIGNED,
    quiz_id BIGINT UNSIGNED,
    activity_type ENUM('enroll', 'start', 'progress', 'complete', 'quiz_attempt', 'certificate') NOT NULL,
    activity_date DATETIME NOT NULL,
    progress_percent TINYINT COMMENT '진도율',
    quiz_score DECIMAL(5,2) COMMENT '퀴즈 점수',
    time_spent INT COMMENT '소요 시간 (초)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_profile (profile_id),
    INDEX idx_course (course_id),
    INDEX idx_date (activity_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.3 Gamification 활동 상세
DROP TABLE IF EXISTS {$wpdb->prefix}nf_gamification_log;
CREATE TABLE {$wpdb->prefix}nf_gamification_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profile_id BIGINT UNSIGNED NOT NULL,
    gami_source ENUM('gamipress', 'mycred') NOT NULL,
    activity_type ENUM('earn_points', 'spend_points', 'earn_achievement', 'rank_up', 'badge_earned') NOT NULL,
    points_change INT DEFAULT 0,
    achievement_id BIGINT UNSIGNED,
    rank_id BIGINT UNSIGNED,
    activity_date DATETIME NOT NULL,
    trigger_event VARCHAR(100) COMMENT '트리거 이벤트',
    meta_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_profile (profile_id),
    INDEX idx_date (activity_date),
    INDEX idx_type (activity_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.4 Analytics 세션 데이터
DROP TABLE IF EXISTS {$wpdb->prefix}nf_analytics_sessions;
CREATE TABLE {$wpdb->prefix}nf_analytics_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profile_id BIGINT UNSIGNED,
    visitor_id VARCHAR(100) NOT NULL COMMENT '익명 방문자 ID',
    analytics_source ENUM('ga4', 'clarity', 'hotjar', 'matomo') NOT NULL,
    session_id VARCHAR(100) NOT NULL,
    session_start DATETIME NOT NULL,
    session_end DATETIME,
    session_duration INT COMMENT '세션 시간 (초)',
    page_views INT DEFAULT 0,
    events_count INT DEFAULT 0,

    -- 디바이스/환경 정보
    device_type VARCHAR(20),
    os VARCHAR(50),
    browser VARCHAR(50),
    screen_resolution VARCHAR(20),
    language VARCHAR(10),
    country VARCHAR(50),
    city VARCHAR(100),

    -- 유입 정보
    traffic_source VARCHAR(100),
    traffic_medium VARCHAR(50),
    campaign VARCHAR(100),
    referrer VARCHAR(500),
    landing_page VARCHAR(500),
    exit_page VARCHAR(500),

    -- Clarity/Hotjar 특화
    rage_clicks INT DEFAULT 0,
    dead_clicks INT DEFAULT 0,
    scroll_depth_max TINYINT DEFAULT 0,
    frustration_events INT DEFAULT 0,
    recording_url VARCHAR(500) COMMENT '녹화 URL',

    -- 전환
    goal_completions JSON COMMENT '목표 달성',
    ecommerce_actions JSON COMMENT '이커머스 액션',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_profile (profile_id),
    INDEX idx_visitor (visitor_id),
    INDEX idx_session (session_id),
    INDEX idx_date (session_start),
    INDEX idx_source (analytics_source)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.5 Community 활동 데이터
DROP TABLE IF EXISTS {$wpdb->prefix}nf_community_activity;
CREATE TABLE {$wpdb->prefix}nf_community_activity (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profile_id BIGINT UNSIGNED NOT NULL,
    community_source ENUM('buddypress', 'buddyboss', 'fluent_community', 'peepso') NOT NULL,
    activity_type ENUM('post', 'comment', 'like', 'share', 'friend_add', 'group_join', 'message', 'mention', 'follow') NOT NULL,
    target_id BIGINT UNSIGNED COMMENT '대상 ID (글, 사용자, 그룹 등)',
    target_type VARCHAR(50) COMMENT '대상 타입',
    activity_date DATETIME NOT NULL,
    content_preview VARCHAR(500),
    reactions_count INT DEFAULT 0,
    replies_count INT DEFAULT 0,
    is_public TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_profile (profile_id),
    INDEX idx_date (activity_date),
    INDEX idx_type (activity_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- 4. 세그먼트 확장 테이블
-- 외부 데이터 기반 세그먼트 조건
-- ============================================
DROP TABLE IF EXISTS {$wpdb->prefix}nf_extended_segment_conditions;
CREATE TABLE {$wpdb->prefix}nf_extended_segment_conditions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    segment_id INT UNSIGNED NOT NULL COMMENT '세그먼트 FK',
    data_source VARCHAR(50) NOT NULL COMMENT '데이터 소스',
    field_name VARCHAR(100) NOT NULL COMMENT '필드명',
    operator ENUM('equals', 'not_equals', 'greater', 'less', 'greater_eq', 'less_eq', 'between', 'in', 'not_in', 'contains', 'not_contains', 'is_null', 'is_not_null') NOT NULL,
    field_value TEXT COMMENT '비교값',
    field_value_end TEXT COMMENT '범위 끝값 (between용)',
    condition_group INT DEFAULT 0 COMMENT '조건 그룹 (AND 내 OR)',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_segment (segment_id),
    INDEX idx_source (data_source),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 세그먼트 조건 예시 데이터
INSERT INTO {$wpdb->prefix}nf_extended_segment_conditions
(segment_id, data_source, field_name, operator, field_value, condition_group) VALUES
-- VIP 고객: WooCommerce 고액 구매자 + 높은 참여도
(1, 'woocommerce', 'wc_total_spent', 'greater_eq', '500000', 1),
(1, 'analytics', 'engagement_score', 'greater_eq', '70', 1),

-- 학습 열정가: LearnDash 활발한 수강생
(5, 'learndash', 'ld_enrolled_courses', 'greater_eq', '3', 1),
(5, 'learndash', 'ld_course_progress_avg', 'greater_eq', '50', 1),

-- 커뮤니티 리더: BuddyPress 활동적 사용자
(6, 'buddypress', 'bp_activity_count', 'greater_eq', '20', 1),
(6, 'buddypress', 'bp_friends_count', 'greater_eq', '10', 1),

-- 이탈 위험: 불만족 신호 감지
(7, 'clarity', 'clarity_frustration_score', 'greater_eq', '60', 1),
(7, 'clarity', 'clarity_rage_clicks', 'greater_eq', '5', 2),

-- 게임 마스터: GamiPress 고레벨 사용자
(8, 'gamipress', 'gp_points', 'greater_eq', '5000', 1),
(8, 'gamipress', 'gp_achievements', 'greater_eq', '10', 1);


-- ============================================
-- 5. 저장 프로시저: 통합 프로필 동기화
-- ============================================

DELIMITER //

-- 5.1 WooCommerce 데이터 동기화
DROP PROCEDURE IF EXISTS {$wpdb->prefix}nf_sync_woocommerce//
CREATE PROCEDURE {$wpdb->prefix}nf_sync_woocommerce(IN p_profile_id BIGINT UNSIGNED)
BEGIN
    DECLARE v_user_id BIGINT;
    DECLARE v_total_orders INT;
    DECLARE v_total_spent DECIMAL(12,2);
    DECLARE v_last_order DATE;
    DECLARE v_first_order DATE;
    DECLARE v_cart_value DECIMAL(10,2);
    DECLARE v_cart_items INT;

    -- 프로필에서 user_id 가져오기
    SELECT wp_user_id INTO v_user_id
    FROM {$wpdb->prefix}nf_unified_customer_profile
    WHERE id = p_profile_id;

    IF v_user_id IS NOT NULL THEN
        -- WooCommerce 주문 집계
        SELECT
            COUNT(*),
            COALESCE(SUM(CAST(meta_total.meta_value AS DECIMAL(12,2))), 0),
            MAX(DATE(orders.post_date)),
            MIN(DATE(orders.post_date))
        INTO v_total_orders, v_total_spent, v_last_order, v_first_order
        FROM {$wpdb->prefix}posts orders
        JOIN {$wpdb->prefix}postmeta meta_customer
            ON orders.ID = meta_customer.post_id
            AND meta_customer.meta_key = '_customer_user'
            AND meta_customer.meta_value = v_user_id
        LEFT JOIN {$wpdb->prefix}postmeta meta_total
            ON orders.ID = meta_total.post_id
            AND meta_total.meta_key = '_order_total'
        WHERE orders.post_type = 'shop_order'
        AND orders.post_status IN ('wc-completed', 'wc-processing');

        -- 장바구니 데이터 (세션 기반이므로 별도 처리 필요)
        -- 이 부분은 PHP에서 WC()->cart 사용 필요

        -- 프로필 업데이트
        UPDATE {$wpdb->prefix}nf_unified_customer_profile
        SET
            wc_total_orders = v_total_orders,
            wc_total_spent = v_total_spent,
            wc_avg_order_value = IF(v_total_orders > 0, v_total_spent / v_total_orders, 0),
            wc_last_order_date = v_last_order,
            wc_first_order_date = v_first_order,
            last_sync_woocommerce = CURRENT_TIMESTAMP
        WHERE id = p_profile_id;

        -- RFM 재계산
        CALL {$wpdb->prefix}nf_calculate_customer_rfm(v_user_id);
    END IF;
END//

-- 5.2 점수 통합 계산
DROP PROCEDURE IF EXISTS {$wpdb->prefix}nf_calculate_scores//
CREATE PROCEDURE {$wpdb->prefix}nf_calculate_scores(IN p_profile_id BIGINT UNSIGNED)
BEGIN
    DECLARE v_engagement TINYINT DEFAULT 0;
    DECLARE v_loyalty TINYINT DEFAULT 0;
    DECLARE v_purchase_intent TINYINT DEFAULT 0;
    DECLARE v_churn_risk TINYINT DEFAULT 0;
    DECLARE v_ltv TINYINT DEFAULT 0;

    -- 참여도 점수 계산
    SELECT
        LEAST(100, (
            COALESCE(ga_sessions_count, 0) * 2 +
            COALESCE(ga_pageviews, 0) * 0.5 +
            COALESCE(bp_activity_count, 0) * 3 +
            COALESCE(ld_course_progress_avg, 0) * 0.5 +
            COALESCE(gp_points, 0) * 0.01
        )) INTO v_engagement
    FROM {$wpdb->prefix}nf_unified_customer_profile
    WHERE id = p_profile_id;

    -- 충성도 점수 계산
    SELECT
        LEAST(100, (
            COALESCE(rfm_f_score, 1) * 15 +
            COALESCE(wc_total_orders, 0) * 5 +
            COALESCE(um_profile_completeness, 0) * 0.3 +
            COALESCE(bp_friends_count, 0) * 2
        )) INTO v_loyalty
    FROM {$wpdb->prefix}nf_unified_customer_profile
    WHERE id = p_profile_id;

    -- 구매 의향 점수 계산
    SELECT
        LEAST(100, (
            IF(wc_cart_items > 0, 30, 0) +
            IF(wc_wishlist_count > 0, 20, 0) +
            COALESCE(rfm_r_score, 1) * 10 +
            IF(wc_total_orders > 0, 20, 0) +
            (100 - COALESCE(clarity_frustration_score, 50)) * 0.2
        )) INTO v_purchase_intent
    FROM {$wpdb->prefix}nf_unified_customer_profile
    WHERE id = p_profile_id;

    -- 이탈 위험 점수 계산 (높을수록 위험)
    SELECT
        LEAST(100, GREATEST(0, (
            (5 - COALESCE(rfm_r_score, 1)) * 15 +
            COALESCE(clarity_frustration_score, 0) * 0.5 +
            COALESCE(clarity_rage_clicks, 0) * 3 +
            IF(DATEDIFF(CURDATE(), wc_last_order_date) > 90, 20, 0) +
            IF(um_membership_status = 'cancelled', 30, 0)
        ))) INTO v_churn_risk
    FROM {$wpdb->prefix}nf_unified_customer_profile
    WHERE id = p_profile_id;

    -- LTV 점수 계산
    SELECT
        LEAST(100, (
            COALESCE(rfm_m_score, 1) * 15 +
            LOG10(COALESCE(wc_total_spent, 1) + 1) * 10 +
            COALESCE(wc_total_orders, 0) * 3 +
            IF(um_membership_status = 'active', 20, 0)
        )) INTO v_ltv
    FROM {$wpdb->prefix}nf_unified_customer_profile
    WHERE id = p_profile_id;

    -- 점수 업데이트
    UPDATE {$wpdb->prefix}nf_unified_customer_profile
    SET
        engagement_score = v_engagement,
        loyalty_score = v_loyalty,
        purchase_intent_score = v_purchase_intent,
        churn_risk_score = v_churn_risk,
        lifetime_value_score = v_ltv,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_profile_id;
END//

-- 5.3 전체 프로필 일괄 동기화
DROP PROCEDURE IF EXISTS {$wpdb->prefix}nf_sync_all_profiles//
CREATE PROCEDURE {$wpdb->prefix}nf_sync_all_profiles()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_profile_id BIGINT;
    DECLARE cur CURSOR FOR
        SELECT id FROM {$wpdb->prefix}nf_unified_customer_profile;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO v_profile_id;
        IF done THEN
            LEAVE read_loop;
        END IF;

        CALL {$wpdb->prefix}nf_sync_woocommerce(v_profile_id);
        CALL {$wpdb->prefix}nf_calculate_scores(v_profile_id);
    END LOOP;

    CLOSE cur;

    SELECT
        COUNT(*) as synced_profiles,
        NOW() as sync_completed_at
    FROM {$wpdb->prefix}nf_unified_customer_profile;
END//

DELIMITER ;


-- ============================================
-- 6. 뷰: 통합 분석 대시보드
-- ============================================

-- 6.1 고객 360도 뷰
DROP VIEW IF EXISTS {$wpdb->prefix}nf_view_customer_360;
CREATE VIEW {$wpdb->prefix}nf_view_customer_360 AS
SELECT
    p.id,
    p.wp_user_id,
    p.email,
    u.display_name,
    u.user_registered,

    -- RFM 세그먼트
    s.segment_name as rfm_segment,
    s.segment_name_en as rfm_segment_en,
    s.marketing_action,
    CONCAT(p.rfm_r_score, '-', p.rfm_f_score, '-', p.rfm_m_score) as rfm_score,

    -- WooCommerce
    p.wc_total_orders,
    p.wc_total_spent,
    p.wc_avg_order_value,
    p.wc_last_order_date,
    DATEDIFF(CURDATE(), p.wc_last_order_date) as days_since_purchase,
    p.wc_cart_value,
    p.wc_wishlist_count,

    -- Membership
    p.um_membership_level,
    p.um_membership_status,
    p.um_profile_completeness,

    -- Community
    p.bp_friends_count,
    p.bp_groups_count,
    p.bp_activity_count,

    -- LMS
    p.ld_enrolled_courses,
    p.ld_completed_courses,
    p.ld_course_progress_avg,

    -- Gamification
    p.gp_points,
    p.gp_achievements,
    p.gp_rank,

    -- Analytics
    p.ga_sessions_count,
    p.ga_avg_session_duration,
    p.clarity_frustration_score,
    p.clarity_engagement_score,

    -- 점수
    p.engagement_score,
    p.loyalty_score,
    p.purchase_intent_score,
    p.churn_risk_score,
    p.lifetime_value_score,

    -- 예측
    p.predicted_next_purchase_date,
    p.predicted_ltv,
    p.predicted_churn_probability,

    p.updated_at
FROM {$wpdb->prefix}nf_unified_customer_profile p
LEFT JOIN {$wpdb->prefix}users u ON p.wp_user_id = u.ID
LEFT JOIN {$wpdb->prefix}nf_rfm_segments s ON p.rfm_segment_id = s.id;


-- 6.2 세그먼트별 통합 통계
DROP VIEW IF EXISTS {$wpdb->prefix}nf_view_segment_insights;
CREATE VIEW {$wpdb->prefix}nf_view_segment_insights AS
SELECT
    s.segment_key,
    s.segment_name,
    s.segment_name_en,
    s.priority_rank,
    s.color_code,
    COUNT(p.id) as customer_count,

    -- WooCommerce 평균
    COALESCE(AVG(p.wc_total_orders), 0) as avg_orders,
    COALESCE(SUM(p.wc_total_spent), 0) as total_revenue,
    COALESCE(AVG(p.wc_total_spent), 0) as avg_customer_value,
    COALESCE(AVG(p.wc_avg_order_value), 0) as avg_order_value,

    -- 참여도 평균
    COALESCE(AVG(p.engagement_score), 0) as avg_engagement,
    COALESCE(AVG(p.loyalty_score), 0) as avg_loyalty,
    COALESCE(AVG(p.churn_risk_score), 0) as avg_churn_risk,

    -- LMS 평균
    COALESCE(AVG(p.ld_enrolled_courses), 0) as avg_courses_enrolled,
    COALESCE(AVG(p.ld_course_progress_avg), 0) as avg_course_progress,

    -- Community 평균
    COALESCE(AVG(p.bp_activity_count), 0) as avg_community_activity,

    -- Gamification 평균
    COALESCE(AVG(p.gp_points), 0) as avg_gami_points,

    -- Analytics 평균
    COALESCE(AVG(p.ga_sessions_count), 0) as avg_sessions,
    COALESCE(AVG(p.clarity_frustration_score), 0) as avg_frustration

FROM {$wpdb->prefix}nf_rfm_segments s
LEFT JOIN {$wpdb->prefix}nf_unified_customer_profile p ON s.id = p.rfm_segment_id
GROUP BY s.id, s.segment_key, s.segment_name, s.segment_name_en, s.priority_rank, s.color_code
ORDER BY s.priority_rank;


-- ============================================
-- 7. 트리거: 자동 데이터 업데이트
-- ============================================

DELIMITER //

-- 7.1 주문 완료 시 프로필 자동 업데이트 (WooCommerce hook에서 호출)
DROP PROCEDURE IF EXISTS {$wpdb->prefix}nf_on_order_complete//
CREATE PROCEDURE {$wpdb->prefix}nf_on_order_complete(
    IN p_order_id BIGINT UNSIGNED,
    IN p_user_id BIGINT UNSIGNED
)
BEGIN
    DECLARE v_profile_id BIGINT;

    -- 프로필 ID 찾기 또는 생성
    SELECT id INTO v_profile_id
    FROM {$wpdb->prefix}nf_unified_customer_profile
    WHERE wp_user_id = p_user_id;

    IF v_profile_id IS NULL THEN
        INSERT INTO {$wpdb->prefix}nf_unified_customer_profile (wp_user_id)
        VALUES (p_user_id);
        SET v_profile_id = LAST_INSERT_ID();
    END IF;

    -- 동기화 실행
    CALL {$wpdb->prefix}nf_sync_woocommerce(v_profile_id);
    CALL {$wpdb->prefix}nf_calculate_scores(v_profile_id);
END//

DELIMITER ;


-- ============================================
-- 완료 메시지
-- ============================================
-- 외부 데이터 소스 통합 설치 완료!
--
-- 지원 플러그인:
-- [E-Commerce] WooCommerce, Easy Digital Downloads
-- [Membership] Ultimate Member, MemberPress, PMPro, RCP, WC Memberships
-- [Community] BuddyPress, BuddyBoss, Fluent Community, PeepSo
-- [LMS] LearnDash, LifterLMS, Tutor LMS, LearnPress, Sensei
-- [Gamification] GamiPress, myCRED
-- [Analytics] GA4, Monster Insights, Microsoft Clarity, Hotjar, Matomo
--
-- 사용 방법:
-- 1. 데이터 소스 활성화: UPDATE nf_data_sources SET is_enabled=1 WHERE source_key='woocommerce';
-- 2. 전체 동기화: CALL wp_nf_sync_all_profiles();
-- 3. 개별 동기화: CALL wp_nf_sync_woocommerce(profile_id);
-- 4. 360도 뷰 조회: SELECT * FROM wp_nf_view_customer_360;
-- 5. 세그먼트 인사이트: SELECT * FROM wp_nf_view_segment_insights;

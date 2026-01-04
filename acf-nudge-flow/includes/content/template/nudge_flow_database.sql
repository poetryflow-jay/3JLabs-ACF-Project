-- =====================================================
-- 넛지 플로우 - 고객 세그먼트 & 넛지 메시지 템플릿 데이터베이스
-- Version: 1.0.0
-- Created: 2026-01-03
-- Description: IFDO 데이터 기반 고객 세그먼트 및 넛지 템플릿 관리
-- =====================================================

-- 테이블 생성 전 기존 테이블 삭제 (개발 환경용)
-- 주의: 프로덕션 환경에서는 이 부분을 주석 처리하세요
-- DROP TABLE IF EXISTS `wp_acf_nudge_segment_template_mapping`;
-- DROP TABLE IF EXISTS `wp_acf_nudge_templates`;
-- DROP TABLE IF EXISTS `wp_acf_nudge_rfm_segments`;
-- DROP TABLE IF EXISTS `wp_acf_nudge_customer_segments`;
-- DROP TABLE IF EXISTS `wp_acf_nudge_rfm_scoring`;

-- =====================================================
-- 1. 고객 세그먼트 테이블 (Customer Segments)
-- =====================================================
CREATE TABLE IF NOT EXISTS `wp_acf_nudge_customer_segments` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `segment_id` VARCHAR(50) NOT NULL COMMENT '세그먼트 고유 ID',
    `category` VARCHAR(50) NOT NULL COMMENT '카테고리 (visit_start, external_referrer, page_view, behavior, ecommerce)',
    `name` VARCHAR(255) NOT NULL COMMENT '세그먼트명',
    `description` TEXT COMMENT '설명',
    `trigger_type` VARCHAR(50) NOT NULL COMMENT '트리거 타입',
    `trigger_settings` TEXT COMMENT '트리거 설정 (JSON)',
    `parameters` TEXT COMMENT '파라미터 설명 (JSON)',
    `priority` INT(11) DEFAULT 0 COMMENT '우선순위',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '활성화 여부',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `segment_id` (`segment_id`),
    KEY `category` (`category`),
    KEY `trigger_type` (`trigger_type`),
    KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='고객 세그먼트 정의';

-- =====================================================
-- 2. RFM 세그먼트 테이블 (RFM Segments)
-- =====================================================
CREATE TABLE IF NOT EXISTS `wp_acf_nudge_rfm_segments` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `segment_code` VARCHAR(50) NOT NULL COMMENT 'RFM 세그먼트 코드',
    `segment_name` VARCHAR(255) NOT NULL COMMENT '세그먼트명',
    `description` TEXT COMMENT '설명',
    `r_score_min` INT(1) DEFAULT 1 COMMENT 'Recency 최소 점수 (1-5)',
    `r_score_max` INT(1) DEFAULT 5 COMMENT 'Recency 최대 점수 (1-5)',
    `f_score_min` INT(1) DEFAULT 1 COMMENT 'Frequency 최소 점수 (1-5)',
    `f_score_max` INT(1) DEFAULT 5 COMMENT 'Frequency 최대 점수 (1-5)',
    `m_score_min` INT(1) DEFAULT 1 COMMENT 'Monetary 최소 점수 (1-5)',
    `m_score_max` INT(1) DEFAULT 5 COMMENT 'Monetary 최대 점수 (1-5)',
    `r_days_max` INT(11) COMMENT 'Recency 최대 일수 (예: 30일 이내)',
    `r_days_min` INT(11) COMMENT 'Recency 최소 일수',
    `f_count_min` INT(11) COMMENT 'Frequency 최소 구매 횟수',
    `f_count_max` INT(11) COMMENT 'Frequency 최대 구매 횟수',
    `m_amount_min` DECIMAL(15,2) COMMENT 'Monetary 최소 구매액',
    `m_amount_max` DECIMAL(15,2) COMMENT 'Monetary 최대 구매액',
    `strategy` VARCHAR(50) COMMENT '마케팅 전략 (retention, win_back, acquisition, etc.)',
    `priority` INT(11) DEFAULT 0 COMMENT '우선순위',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '활성화 여부',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `segment_code` (`segment_code`),
    KEY `strategy` (`strategy`),
    KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='RFM 세그먼트 정의';

-- =====================================================
-- 3. 넛지 템플릿 테이블 (Nudge Templates)
-- =====================================================
CREATE TABLE IF NOT EXISTS `wp_acf_nudge_templates` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `template_id` VARCHAR(50) NOT NULL COMMENT '템플릿 고유 ID',
    `title` VARCHAR(255) NOT NULL COMMENT '템플릿 제목',
    `description` TEXT COMMENT '설명',
    `type` VARCHAR(20) DEFAULT 'free' COMMENT '타입 (free, premium)',
    `category` VARCHAR(50) COMMENT '카테고리 (RFM, Segment, Design Store, E-commerce)',
    `icon` VARCHAR(50) COMMENT '아이콘 클래스',
    `price_krw` DECIMAL(10,2) COMMENT '가격 (원)',
    `price_usd` DECIMAL(10,2) COMMENT '가격 (USD)',
    `price_eur` DECIMAL(10,2) COMMENT '가격 (EUR)',
    `trigger_type` VARCHAR(50) NOT NULL COMMENT '트리거 타입',
    `trigger_settings` TEXT COMMENT '트리거 설정 (JSON)',
    `action_type` VARCHAR(50) NOT NULL COMMENT '액션 타입',
    `action_settings` TEXT COMMENT '액션 설정 (JSON)',
    `design_settings` TEXT COMMENT '디자인 설정 (JSON)',
    `preview_image` VARCHAR(255) COMMENT '프리뷰 이미지 URL',
    `usage_count` INT(11) DEFAULT 0 COMMENT '사용 횟수',
    `success_rate` DECIMAL(5,2) DEFAULT 0.00 COMMENT '성공률 (%)',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT '활성화 여부',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `template_id` (`template_id`),
    KEY `type` (`type`),
    KEY `category` (`category`),
    KEY `trigger_type` (`trigger_type`),
    KEY `action_type` (`action_type`),
    KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='넛지 템플릿 정의';

-- =====================================================
-- 4. 세그먼트-템플릿 매핑 테이블 (Segment-Template Mapping)
-- =====================================================
CREATE TABLE IF NOT EXISTS `wp_acf_nudge_segment_template_mapping` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `segment_id` INT(11) UNSIGNED COMMENT '고객 세그먼트 ID (FK)',
    `rfm_segment_id` INT(11) UNSIGNED COMMENT 'RFM 세그먼트 ID (FK)',
    `template_id` INT(11) UNSIGNED NOT NULL COMMENT '템플릿 ID (FK)',
    `match_priority` INT(11) DEFAULT 0 COMMENT '매칭 우선순위',
    `is_recommended` TINYINT(1) DEFAULT 0 COMMENT '추천 여부',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `segment_id` (`segment_id`),
    KEY `rfm_segment_id` (`rfm_segment_id`),
    KEY `template_id` (`template_id`),
    KEY `is_recommended` (`is_recommended`),
    CONSTRAINT `fk_mapping_segment` FOREIGN KEY (`segment_id`) REFERENCES `wp_acf_nudge_customer_segments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_mapping_rfm` FOREIGN KEY (`rfm_segment_id`) REFERENCES `wp_acf_nudge_rfm_segments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_mapping_template` FOREIGN KEY (`template_id`) REFERENCES `wp_acf_nudge_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='세그먼트-템플릿 매핑';

-- =====================================================
-- 5. RFM 점수 계산 기준 테이블 (RFM Scoring Criteria)
-- =====================================================
CREATE TABLE IF NOT EXISTS `wp_acf_nudge_rfm_scoring` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `score` INT(1) NOT NULL COMMENT '점수 (1-5)',
    `metric_type` VARCHAR(20) NOT NULL COMMENT '지표 타입 (recency, frequency, monetary)',
    `recency_days_max` INT(11) COMMENT 'Recency 최대 일수',
    `recency_days_min` INT(11) COMMENT 'Recency 최소 일수',
    `frequency_count_min` INT(11) COMMENT 'Frequency 최소 구매 횟수',
    `frequency_count_max` INT(11) COMMENT 'Frequency 최대 구매 횟수',
    `monetary_amount_min` DECIMAL(15,2) COMMENT 'Monetary 최소 구매액',
    `monetary_amount_max` DECIMAL(15,2) COMMENT 'Monetary 최대 구매액',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `score_metric` (`score`, `metric_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='RFM 점수 계산 기준';

-- =====================================================
-- 초기 데이터 삽입: RFM 점수 계산 기준
-- =====================================================
-- Recency 점수 (최근 구매일로부터 경과일)
INSERT IGNORE INTO `wp_acf_nudge_rfm_scoring` (`score`, `metric_type`, `recency_days_max`, `recency_days_min`, `frequency_count_min`, `frequency_count_max`, `monetary_amount_min`, `monetary_amount_max`) VALUES
(5, 'recency', 29, 0, NULL, NULL, NULL, NULL),
(4, 'recency', 89, 30, NULL, NULL, NULL, NULL),
(3, 'recency', 179, 90, NULL, NULL, NULL, NULL),
(2, 'recency', 364, 180, NULL, NULL, NULL, NULL),
(1, 'recency', NULL, 365, NULL, NULL, NULL, NULL);

-- Frequency 점수 (구매 횟수)
INSERT IGNORE INTO `wp_acf_nudge_rfm_scoring` (`score`, `metric_type`, `recency_days_max`, `recency_days_min`, `frequency_count_min`, `frequency_count_max`, `monetary_amount_min`, `monetary_amount_max`) VALUES
(5, 'frequency', NULL, NULL, 13, NULL, NULL, NULL),
(4, 'frequency', NULL, NULL, 7, 12, NULL, NULL),
(3, 'frequency', NULL, NULL, 4, 6, NULL, NULL),
(2, 'frequency', NULL, NULL, 2, 3, NULL, NULL),
(1, 'frequency', NULL, NULL, 0, 1, NULL, NULL);

-- Monetary 점수 (총 구매액)
INSERT IGNORE INTO `wp_acf_nudge_rfm_scoring` (`score`, `metric_type`, `recency_days_max`, `recency_days_min`, `frequency_count_min`, `frequency_count_max`, `monetary_amount_min`, `monetary_amount_max`) VALUES
(5, 'monetary', NULL, NULL, NULL, NULL, 1000000, NULL),
(4, 'monetary', NULL, NULL, NULL, NULL, 500000, 999999),
(3, 'monetary', NULL, NULL, NULL, NULL, 200000, 499999),
(2, 'monetary', NULL, NULL, NULL, NULL, 50000, 199999),
(1, 'monetary', NULL, NULL, NULL, NULL, 0, 49999);

-- =====================================================
-- 초기 데이터 삽입: RFM 세그먼트
-- =====================================================
INSERT IGNORE INTO `wp_acf_nudge_rfm_segments` (`segment_code`, `segment_name`, `description`, `r_score_min`, `r_score_max`, `f_score_min`, `f_score_max`, `m_score_min`, `m_score_max`, `strategy`, `priority`) VALUES
('diamond_vip', '다이아몬드 VIP', '최근 구매 + 고빈도 + 고금액 (R5-F5-M5)', 5, 5, 5, 5, 5, 5, 'retention', 100),
('platinum_vip', '플래티넘 VIP', '최근 구매 + 고빈도 + 중고금액 (R5-F5-M4)', 5, 5, 5, 5, 4, 4, 'retention', 90),
('gold_vip', '골드 VIP', '최근 구매 + 중빈도 + 고금액 (R5-F4-M5)', 5, 5, 4, 4, 5, 5, 'retention', 80),
('silver_vip', '실버 VIP', '최근 구매 + 중빈도 + 중금액 (R5-F4-M4)', 5, 5, 4, 4, 4, 4, 'retention', 70),
('cannot_lose', '절대 놓치지 마세요', '과거 VIP였으나 최근 구매 없음 (R1-2-F4-5-M4-5)', 1, 2, 4, 5, 4, 5, 'win_back', 95),
('at_risk', '갈림길 고객', '이탈 위험 고객 (R2-3-F3-4-M3-4)', 2, 3, 3, 4, 3, 4, 'retention', 60),
('new_customer', '신규 고객', '최근 첫 구매 (R5-F1-M1-3)', 5, 5, 1, 1, 1, 3, 'acquisition', 50),
('hibernating', '겨울잠 고객', '장기간 미구매 휴면 고객 (R1-F1-2-M1-3)', 1, 1, 1, 2, 1, 3, 'win_back', 40),
('lost_customer', '이탈 고객', '완전 이탈 고객 (R1-F1-M1)', 1, 1, 1, 1, 1, 1, 'win_back', 30),
('champion', '챔피언', '최근 구매 + 고빈도 + 중금액 (R4-5-F4-5-M3-4)', 4, 5, 4, 5, 3, 4, 'upsell', 85),
('loyal_customer', '충성 고객', '정기 구매 고객 (R3-5-F3-5-M2-4)', 3, 5, 3, 5, 2, 4, 'retention', 75),
('potential_loyalist', '잠재 충성 고객', '최근 구매 + 낮은 빈도 (R4-5-F1-2-M2-3)', 4, 5, 1, 2, 2, 3, 'upsell', 65),
('need_attention', '관심 필요', '중간 수준 고객 (R3-4-F2-3-M2-3)', 3, 4, 2, 3, 2, 3, 'retention', 55),
('about_to_sleep', '곧 휴면', '구매 빈도 감소 중 (R2-3-F2-3-M2-3)', 2, 3, 2, 3, 2, 3, 'retention', 45);

-- =====================================================
-- 초기 데이터 삽입: 고객 세그먼트 (스프레드시트 기반 50개)
-- =====================================================

-- 카테고리 1: 방문이 시작되었을 때
INSERT IGNORE INTO `wp_acf_nudge_customer_segments` (`segment_id`, `category`, `name`, `description`, `trigger_type`, `trigger_settings`, `parameters`, `priority`) VALUES
('visit_first_time', 'visit_start', '사이트를 처음 방문한 방문객', '첫 방문자 타겟팅', 'first_visit', '{}', '{"description": "첫 방문 여부"}', 100),
('visit_returning', 'visit_start', '사이트를 다시 방문한 방문객', '재방문자 타겟팅', 'returning_visit', '{}', '{"description": "재방문 여부"}', 90),
('visit_count_max', 'visit_start', '누적 방문수가 OO회 이하인 방문객', '방문 횟수 제한', 'visit_count', '{"operator": "<=", "count": 5}', '{"visit_count_max": "최대 방문 횟수"}', 80),
('visit_count_min', 'visit_start', '누적 방문수가 OO회 이상인 방문객', '충성 방문자', 'visit_count', '{"operator": ">=", "count": 10}', '{"visit_count_min": "최소 방문 횟수"}', 85),
('visit_count_range', 'visit_start', '누적 방문수가 OO회~OO회 사이인 방문객', '구간 타겟팅', 'visit_count', '{"min": 5, "max": 10}', '{"visit_count_min": "최소 방문 횟수", "visit_count_max": "최대 방문 횟수"}', 75),
('days_since_last_visit', 'visit_start', '마지막 방문 이후 OO일 만에 다시 방문한 방문객', '재방문 주기', 'days_since_last_visit', '{"operator": "=", "days": 7}', '{"days": "경과 일수"}', 70),
('min_days_absence', 'visit_start', '마지막 방문 이후 OO일 이상 방문이 없었다가 다시 방문한 방문객', '휴면 복귀자', 'days_since_last_visit', '{"operator": ">=", "days": 30}', '{"min_days_absence": "최소 미방문 일수"}', 65),
('active_user_period', 'visit_start', '최근 OO일 동안 사이트를 OO회 이상 방문한 방문객', '활성 사용자', 'visit_count_period', '{"days": 30, "count": 5}', '{"days_period": "기간(일)", "visit_count_min": "최소 방문 횟수"}', 60),
('avg_stay_time_max', 'visit_start', '평균 체류시간이 OO초 이내인 방문객', '이탈 위험', 'avg_stay_time', '{"operator": "<=", "seconds": 30}', '{"avg_stay_seconds_max": "최대 체류 시간(초)"}', 55),
('avg_stay_time_min', 'visit_start', '평균 체류시간이 OO분 이상인 방문객', '관심 고객', 'avg_stay_time', '{"operator": ">=", "minutes": 5}', '{"avg_stay_minutes_min": "최소 체류 시간(분)"}', 50),
('avg_pageview_max', 'visit_start', '평균 페이지뷰가 OO회 이하인 방문객', '낮은 관심도', 'avg_pageview', '{"operator": "<=", "count": 2}', '{"avg_pageview_max": "최대 페이지뷰"}', 45),
('avg_pageview_min', 'visit_start', '평균 페이지뷰가 OO회 이상인 방문객', '높은 관심도', 'avg_pageview', '{"operator": ">=", "count": 5}', '{"avg_pageview_min": "최소 페이지뷰"}', 40),
('visit_hour', 'visit_start', 'OO시간대에 방문하는 방문객', '시간대 타겟팅', 'visit_hour', '{"hour": 9}', '{"visit_hour": "방문 시간대(0-23)"}', 35),
('landing_page', 'visit_start', '#페이지에서 방문이 시작된 방문객', '랜딩페이지 기반', 'landing_page', '{"page_url": ""}', '{"landing_page": "랜딩 페이지 URL"}', 30),
('url_parameter', 'visit_start', '#파라미터를 포함한 페이지에서 방문이 시작된 방문객', 'UTM 등 파라미터', 'url_parameter', '{"parameter": "utm_source", "value": ""}', '{"url_parameter": "URL 파라미터명", "value": "파라미터 값"}', 25),
('device_pc', 'visit_start', 'PC로 방문한 방문객', '디바이스 필터', 'device_type', '{"device": "pc"}', '{"device": "pc"}', 20),
('device_mobile', 'visit_start', '모바일로 방문한 방문객', '디바이스 필터', 'device_type', '{"device": "mobile"}', '{"device": "mobile"}', 20),
('os_type', 'visit_start', '#운영체제로 방문한 방문객', 'OS 타겟팅', 'os_type', '{"os": ""}', '{"os_type": "운영체제 (windows, mac, ios, android)"}', 15),
('browser_type', 'visit_start', '#브라우저로 방문한 방문객', '브라우저 타겟팅', 'browser_type', '{"browser": ""}', '{"browser_type": "브라우저 (chrome, firefox, safari, edge)"}', 10);

-- 카테고리 2: 다른 사이트에서 방문했을 때
INSERT IGNORE INTO `wp_acf_nudge_customer_segments` (`segment_id`, `category`, `name`, `description`, `trigger_type`, `trigger_settings`, `parameters`, `priority`) VALUES
('referrer_search_engine', 'external_referrer', '#검색엔진에서 방문한 방문객', '검색유입', 'referrer_type', '{"type": "search_engine"}', '{"search_engine": "검색엔진 (google, naver, daum)"}', 100),
('referrer_search_keyword', 'external_referrer', '#검색엔진에서 #검색어로 방문한 방문객', '키워드 타겟팅', 'referrer_search', '{"engine": "", "keyword": ""}', '{"search_engine": "검색엔진", "keyword": "검색어"}', 95),
('referrer_search_ad', 'external_referrer', '#검색광고로 방문한 방문객', '검색광고 유입', 'referrer_type', '{"type": "search_ad"}', '{"ad_source": "광고 소스"}', 90),
('referrer_campaign', 'external_referrer', '#캠페인으로 방문한 방문객', '캠페인 추적', 'utm_campaign', '{"campaign": ""}', '{"campaign": "캠페인명"}', 85),
('referrer_social_media', 'external_referrer', '#소셜미디어에서 방문한 방문객', 'SNS 유입', 'referrer_type', '{"type": "social_media"}', '{"social_media": "소셜미디어 (facebook, instagram, twitter)"}', 80),
('referrer_domain', 'external_referrer', '#외부도메인 또는 URL에서 방문한 방문객', '레퍼러 추적', 'referrer_domain', '{"domain": ""}', '{"referrer_domain": "레퍼러 도메인 또는 URL"}', 75);

-- 카테고리 3: 지정된 페이지를 봤을 때
INSERT IGNORE INTO `wp_acf_nudge_customer_segments` (`segment_id`, `category`, `name`, `description`, `trigger_type`, `trigger_settings`, `parameters`, `priority`) VALUES
('page_view_count', 'page_view', '#특정 페이지를 OO회 이상 보고 있는 방문객', '페이지 관심도', 'page_view_count', '{"page_url": "", "count": 3}', '{"page_url": "페이지 URL", "view_count_min": "최소 조회 횟수"}', 100),
('page_stay_time', 'page_view', '#특정 페이지를 OO초 이상 보고 있는 방문객', '체류 기반', 'page_stay_time', '{"page_url": "", "seconds": 60}', '{"page_url": "페이지 URL", "stay_seconds_min": "최소 체류 시간(초)"}', 95),
('content_group_view', 'page_view', '#콘텐츠 그룹을 OO일 동안 OO회 이상 본 방문객', '콘텐츠 그룹', 'content_group_view', '{"content_group": "", "days": 30, "count": 5}', '{"content_group": "콘텐츠 그룹", "days_period": "기간(일)", "view_count_min": "최소 조회 횟수"}', 90),
('session_pageview', 'page_view', '방문 시작 후 페이지뷰가 OO회 이상인 방문객', '세션 활동', 'session_pageview', '{"count": 5}', '{"session_pageview_min": "최소 페이지뷰"}', 85),
('page_flow', 'page_view', '#페이지(A)에서 #페이지(B)로 이동한 방문객', '이동 경로', 'page_flow', '{"from": "", "to": ""}', '{"page_from": "출발 페이지", "page_to": "도착 페이지"}', 80);

-- 카테고리 4: 특정 행동에 해당될 때
INSERT IGNORE INTO `wp_acf_nudge_customer_segments` (`segment_id`, `category`, `name`, `description`, `trigger_type`, `trigger_settings`, `parameters`, `priority`) VALUES
('internal_search', 'behavior', '사이트에서 #내부검색어를 검색한 방문객', '내부 검색', 'internal_search', '{"keyword": ""}', '{"internal_search_keyword": "검색어"}', 100),
('internal_search_no_result', 'behavior', '사이트 내부 검색을 했으나 검색결과가 없는 경우', '검색 실패', 'internal_search', '{"has_results": false}', '{}', 95),
('signup_complete', 'behavior', '#회원가입 완료 페이지에 도달했을 때', '회원가입 완료', 'signup_complete', '{"page": ""}', '{"signup_complete_page": "회원가입 완료 페이지"}', 90),
('signup_abandon', 'behavior', '회원가입 정보입력 페이지를 보았으나 완료페이지까지 도달하지 못한 방문객', '가입 이탈', 'signup_abandon', '{"form_page": "", "complete_page": ""}', '{"signup_form_page": "가입 폼 페이지", "signup_complete_page": "완료 페이지"}', 85),
('new_member', 'behavior', '회원가입일이 최근 OO일 이내인 회원', '신규 회원', 'days_since_signup', '{"operator": "<=", "days": 7}', '{"days_since_signup": "가입 후 경과 일수"}', 80),
('logged_in', 'behavior', '로그인을 한 회원', '로그인 회원', 'user_logged_in', '{}', '{}', 75),
('gender', 'behavior', '성별이 #남 #여 인 방문객', '성별 타겟팅', 'user_gender', '{"gender": ""}', '{"gender": "성별 (male, female)"}', 70),
('age_group', 'behavior', '연령이 #10대이하~#60대이상 인 회원', '연령 타겟팅', 'user_age', '{"min": 10, "max": 20}', '{"age_group": "연령대 (10s, 20s, 30s, 40s, 50s, 60s+)"}', 65),
('withdrawal_risk', 'behavior', '#회원탈퇴 페이지에 도달한 회원', '탈퇴 위험', 'withdrawal_page', '{"page": ""}', '{"withdrawal_page": "탈퇴 페이지"}', 60);

-- 카테고리 5: 전자 상거래
INSERT IGNORE INTO `wp_acf_nudge_customer_segments` (`segment_id`, `category`, `name`, `description`, `trigger_type`, `trigger_settings`, `parameters`, `priority`) VALUES
('product_view_repeat', 'ecommerce', '방문 시작 후 동일한 상품을 OO회 이상 본 방문객', '상품 관심', 'product_view_count', '{"product_id": "", "count": 3}', '{"product_view_count_min": "최소 조회 횟수"}', 100),
('category_view_time', 'ecommerce', '동일한 카테고리의 상품을 OO시간 동안 OO회 이상 본 방문객', '카테고리 관심', 'category_view', '{"category_id": "", "hours": 24, "count": 5}', '{"category_id": "카테고리 ID", "hours_period": "기간(시간)", "view_count_min": "최소 조회 횟수"}', 95),
('product_view_specific', 'ecommerce', '#특정 상품을 OO회 이상 보고 있는 방문객', '특정 상품', 'product_view_count', '{"product_id": "", "count": 3}', '{"product_id": "상품 ID", "view_count_min": "최소 조회 횟수"}', 90),
('category_view_specific', 'ecommerce', '#특정 카테고리를 OO회 이상 보고 있는 방문객', '특정 카테고리', 'category_view_count', '{"category_id": "", "count": 5}', '{"category_id": "카테고리 ID", "view_count_min": "최소 조회 횟수"}', 85),
('wishlist_count', 'ecommerce', '위시리스트에 보관중인 상품이 OO개 이상인 방문객', '위시리스트', 'wishlist_count', '{"operator": ">=", "count": 5}', '{"wishlist_count_min": "최소 위시리스트 개수"}', 80),
('cart_item_count', 'ecommerce', '장바구니에 담긴 상품이 OO개 이상인 방문객', '장바구니', 'cart_item_count', '{"operator": ">=", "count": 3}', '{"cart_item_count_min": "최소 장바구니 개수"}', 75),
('cart_abandonment', 'ecommerce', '최근 OO일 이내 장바구니에 상품을 추가했으나 구매하지 않은 방문객', '장바구니 이탈', 'cart_abandonment', '{"days": 3}', '{"days_since_cart_add": "장바구니 추가 후 경과 일수"}', 70),
('purchase_count_one', 'ecommerce', '지금까지 구매수가 1회인 방문객', '1회 구매자', 'purchase_count', '{"operator": "=", "count": 1}', '{}', 65),
('purchase_count_repeat', 'ecommerce', '누적 구매수가 2회 이상인 방문객', '재구매자', 'purchase_count', '{"operator": ">=", "count": 2}', '{}', 60),
('purchase_count_zero', 'ecommerce', '구매 경험이 없는 방문객', '비구매자', 'purchase_count', '{"operator": "=", "count": 0}', '{}', 55),
('total_purchase_amount', 'ecommerce', '누적 구매액이 OO원 이상인 방문객', 'VIP', 'total_spent', '{"operator": ">=", "amount": 1000000}', '{"total_purchase_amount_min": "최소 구매액"}', 50);

-- =====================================================
-- 초기 데이터 삽입: 넛지 템플릿 (IFDO 데이터 기반 22종)
-- =====================================================

-- === RFM 기반 넛지 템플릿 ===
INSERT IGNORE INTO `wp_acf_nudge_templates` (`template_id`, `title`, `description`, `type`, `category`, `icon`, `price_krw`, `price_usd`, `price_eur`, `trigger_type`, `trigger_settings`, `action_type`, `action_settings`, `design_settings`) VALUES
('rfm_diamond_vip', '다이아몬드 VIP 리워드', '최근 구매 + 고빈도 + 고금액 다이아몬드 고객에게 VIP 전용 리워드를 제공합니다.', 'premium', 'RFM', 'dashicons-awards', 35000, 25, 23, 'rfm_segment', '{"segment": "diamond_vip", "r_score": 5, "f_score": 5, "m_score": 5}', 'discount_reveal', '{"title": "💎 다이아몬드 VIP 전용 혜택", "content": "최고 등급 고객님께 감사드립니다. 오늘만 25% 추가 할인!", "coupon_code": "DIAMOND25", "auto_apply": true}', '{}'),
('rfm_cannot_lose', '절대 놓치지 마세요 (SOS)', '과거 VIP였으나 최근 구매 없는 고객을 긴급 재활성화합니다.', 'premium', 'RFM', 'dashicons-warning', 29000, 21, 19, 'rfm_segment', '{"segment": "cannot_lose", "r_score_max": 2, "f_score_min": 4}', 'popup_center', '{"title": "🚨 오랜만이에요! 돌아와주세요", "content": "저희가 많이 그리웠어요. 특별 복귀 할인 30%를 준비했습니다!", "cta_text": "다시 만나기", "style": "urgent"}', '{}'),
('rfm_new_customer_welcome', '신규 고객 웰컴 시리즈', '최근 첫 구매 신규 고객에게 웰컴 시리즈와 두번째 구매 유도 메시지를 전송합니다.', 'free', 'RFM', 'dashicons-heart', 0, 0, 0, 'rfm_segment', '{"segment": "new_customer", "r_score": 5, "f_score": 1}', 'popup_slide_in', '{"position": "bottom-right", "title": "🎉 첫 구매 감사합니다!", "content": "다음 구매 시 사용 가능한 15% 쿠폰을 드려요.", "cta_text": "쿠폰 받기"}', '{}'),
('rfm_hibernating', '겨울잠 고객 재활성화', '장기간 미구매 휴면 고객에게 재활성화 캠페인과 특별 혜택을 전송합니다.', 'premium', 'RFM', 'dashicons-clock', 19000, 14, 13, 'rfm_segment', '{"segment": "hibernating", "r_score": 1, "f_score_max": 2}', 'toast', '{"message": "😴 오랜만이에요! 복귀 기념 무료배송 쿠폰을 확인하세요", "type": "promo", "position": "bottom-left", "duration": 10}', '{}'),
('rfm_at_risk', '갈림길 고객 리텐션', '이탈 위험 고객에게 리텐션 캠페인과 혜택을 제공합니다.', 'premium', 'RFM', 'dashicons-admin-network', 25000, 18, 17, 'rfm_segment', '{"segment": "at_risk", "r_score_range": "2-3", "f_score_range": "3-4"}', 'popup_center', '{"title": "🤔 뭔가 놓치신 건 없으신가요?", "content": "관심 있으셨던 상품들이 기다리고 있어요. 지금 확인해보세요!", "cta_text": "상품 보러 가기", "style": "friendly"}', '{}'),

-- === 세그먼트 스토어 기반 넛지 템플릿 ===
('segment_loyal_visitor', '충성 방문자 감사 메시지', '누적 방문수 10회 이상인 충성 방문자에게 감사 메시지와 혜택을 제공합니다.', 'free', 'Segment', 'dashicons-groups', 0, 0, 0, 'visit_count', '{"operator": ">=", "count": 10}', 'popup_slide_in', '{"position": "bottom-right", "title": "🙏 자주 방문해 주셔서 감사해요!", "content": "충성 고객님께 특별 할인 쿠폰을 드립니다.", "cta_text": "쿠폰 받기"}', '{}'),
('segment_bounce_risk', '이탈 위험 방문자 잡기', '평균 체류시간 30초 이내인 이탈 위험 방문자에게 관심 유도 콘텐츠를 제공합니다.', 'free', 'Segment', 'dashicons-migrate', 0, 0, 0, 'avg_stay_time', '{"operator": "<=", "seconds": 30}', 'toast', '{"message": "👀 잠깐만요! 놓치기 아까운 인기 상품을 확인해보세요", "type": "info", "position": "top-center", "duration": 5}', '{}'),
('segment_search_engine', '검색엔진 유입자 환영', '검색엔진에서 유입된 방문자에게 맞춤 환영 메시지를 제공합니다.', 'free', 'Segment', 'dashicons-search', 0, 0, 0, 'referrer_type', '{"type": "search_engine"}', 'popup_slide_in', '{"position": "bottom-right", "title": "🔍 찾으시던 정보를 발견하셨나요?", "content": "검색으로 오셨군요! 원하시는 상품을 빠르게 찾아드릴게요.", "cta_text": "상품 검색"}', '{}'),
('segment_campaign_visitor', '캠페인 유입자 특별 오퍼', '특정 캠페인으로 유입된 방문자에게 캠페인 전용 혜택을 제공합니다.', 'premium', 'Segment', 'dashicons-megaphone', 15000, 11, 10, 'utm_campaign', '{"campaign": ""}', 'popup_center', '{"title": "🎯 캠페인 전용 혜택!", "content": "이 링크로 오셨다면 특별 혜택을 받으실 수 있어요.", "cta_text": "혜택 확인하기"}', '{}'),
('segment_dormant_return', '휴면 복귀자 환영', '마지막 방문 이후 30일 이상 방문이 없었다가 다시 방문한 고객을 환영합니다.', 'free', 'Segment', 'dashicons-update', 0, 0, 0, 'days_since_last_visit', '{"operator": ">=", "days": 30}', 'popup_center', '{"title": "🎊 다시 오셨군요!", "content": "오랜만에 방문해 주셔서 감사해요. 복귀 기념 할인 쿠폰을 드립니다!", "cta_text": "쿠폰 받기"}', '{}'),

-- === 디자인 스토어 기반 넛지 템플릿 ===
('design_round_floating_kakao', '카카오톡 채널 추가 유도', '라운드 플로팅 디자인으로 카카오톡 채널 추가를 유도합니다.', 'free', 'Design Store', 'dashicons-format-chat', 0, 0, 0, 'time_on_site', '{"seconds": 10}', 'floating_button', '{"type": "kakao_channel", "message": "🎁 카톡채널 추가하고 경품받기", "icon": "gift", "position": "bottom-right"}', '{}'),
('design_round_floating_phone', '전화 상담 연결 버튼', '라운드 플로팅 디자인으로 무료 상담 전화 연결을 유도합니다.', 'free', 'Design Store', 'dashicons-phone', 0, 0, 0, 'scroll_depth', '{"percentage": 30}', 'floating_button', '{"type": "phone_call", "message": "📞 무료 상담 전화 안내", "phone_number": "080-123-1234", "position": "bottom-right"}', '{}'),
('design_rich_popup_video', '동영상 리치 팝업', '리치팝업 동영상 전용 디자인으로 이벤트 영상을 자동 재생합니다.', 'premium', 'Design Store', 'dashicons-video-alt3', 25000, 18, 17, 'first_visit', '{}', 'video_popup', '{"title": "🎬 오늘의 이벤트", "video_url": "", "autoplay": true}', '{}'),
('design_coupon_box', '쿠폰박스 할인 증정', '쿠폰박스 디자인으로 할인 쿠폰 다운로드를 유도합니다.', 'free', 'Design Store', 'dashicons-tickets-alt', 0, 0, 0, 'time_on_site', '{"seconds": 15}', 'coupon_box', '{"title": "🎟️ 할인 쿠폰 증정", "coupon_code": "WELCOME10", "discount_text": "10% 할인", "cta_text": "쿠폰 다운로드"}', '{}'),
('design_welcome_bar', '웰컴바 상단 공지', '웰컴바 공지형 디자인으로 중요 공지나 프로모션을 상단에 표시합니다.', 'free', 'Design Store', 'dashicons-info-outline', 0, 0, 0, 'page_load', '{}', 'welcome_bar', '{"message": "📢 오늘만! 전 품목 무료배송 이벤트 진행중", "position": "top", "dismissible": true, "link_text": "자세히 보기"}', '{}'),
('design_product_recommend_pc', 'PC 맞춤 상품 추천', 'PC 화면에 최적화된 상품 추천 디자인으로 맞춤 상품을 노출합니다.', 'premium', 'Design Store', 'dashicons-desktop', 29000, 21, 19, 'device_type', '{"device": "pc"}', 'product_recommend', '{"title": "👀 이 상품은 어떠세요?", "recommendation_type": "personalized", "layout": "horizontal", "max_products": 4}', '{}'),
('design_product_recommend_mobile', '모바일 맞춤 상품 추천', '모바일 화면에 최적화된 상품 추천 디자인으로 맞춤 상품을 노출합니다.', 'premium', 'Design Store', 'dashicons-smartphone', 29000, 21, 19, 'device_type', '{"device": "mobile"}', 'product_recommend', '{"title": "📱 추천 상품", "recommendation_type": "personalized", "layout": "vertical", "max_products": 3}', '{}'),

-- === 전자상거래 세그먼트 기반 넛지 템플릿 ===
('ecom_cart_abandon', '장바구니 이탈 긴급 알림', '장바구니에 상품을 담은 후 3일 이상 구매하지 않은 방문자에게 리마인드 메시지를 보냅니다.', 'free', 'E-commerce', 'dashicons-cart', 0, 0, 0, 'cart_abandonment', '{"days": 3}', 'popup_center', '{"title": "🛒 장바구니를 잊으셨나요?", "content": "담아두신 상품이 기다리고 있어요. 지금 결제하면 추가 5% 할인!", "cta_text": "장바구니 보기"}', '{}'),
('ecom_wishlist_promo', '위시리스트 상품 할인 알림', '위시리스트에 5개 이상 상품을 보관 중인 방문자에게 할인 알림을 보냅니다.', 'premium', 'E-commerce', 'dashicons-heart', 19000, 14, 13, 'wishlist_count', '{"operator": ">=", "count": 5}', 'toast', '{"message": "💝 찜한 상품 중 3개가 지금 할인 중이에요!", "type": "promo", "position": "bottom-right", "duration": 8}', '{}'),
('ecom_repeat_buyer', '재구매자 감사 메시지', '누적 구매 2회 이상인 재구매 고객에게 감사 메시지와 추가 혜택을 제공합니다.', 'free', 'E-commerce', 'dashicons-thumbs-up', 0, 0, 0, 'purchase_count', '{"operator": ">=", "count": 2}', 'popup_slide_in', '{"position": "bottom-right", "title": "🙏 재구매 감사합니다!", "content": "다시 찾아주신 고객님께 특별 쿠폰을 드려요.", "cta_text": "쿠폰 받기"}', '{}'),
('ecom_high_value_customer', '고가치 고객 전용 오퍼', '누적 구매액 100만원 이상인 고가치 고객에게 전용 혜택을 제공합니다.', 'premium', 'E-commerce', 'dashicons-money-alt', 35000, 25, 23, 'total_spent', '{"operator": ">=", "amount": 1000000}', 'discount_reveal', '{"title": "👑 프리미엄 고객 전용", "content": "최상위 고객님께만 드리는 비밀 쿠폰입니다.", "coupon_code": "PREMIUM30", "auto_apply": true}', '{}');

-- =====================================================
-- 세그먼트-템플릿 매핑 데이터 삽입
-- =====================================================

-- RFM 세그먼트와 템플릿 매핑
INSERT IGNORE INTO `wp_acf_nudge_segment_template_mapping` (`rfm_segment_id`, `template_id`, `match_priority`, `is_recommended`)
SELECT r.id, t.id, 100, 1
FROM `wp_acf_nudge_rfm_segments` r
INNER JOIN `wp_acf_nudge_templates` t ON (
    (r.segment_code = 'diamond_vip' AND t.template_id = 'rfm_diamond_vip') OR
    (r.segment_code = 'cannot_lose' AND t.template_id = 'rfm_cannot_lose') OR
    (r.segment_code = 'new_customer' AND t.template_id = 'rfm_new_customer_welcome') OR
    (r.segment_code = 'hibernating' AND t.template_id = 'rfm_hibernating') OR
    (r.segment_code = 'at_risk' AND t.template_id = 'rfm_at_risk')
);

-- 고객 세그먼트와 템플릿 매핑
INSERT IGNORE INTO `wp_acf_nudge_segment_template_mapping` (`segment_id`, `template_id`, `match_priority`, `is_recommended`)
SELECT s.id, t.id, 90, 1
FROM `wp_acf_nudge_customer_segments` s
INNER JOIN `wp_acf_nudge_templates` t ON (
    (s.segment_id = 'visit_count_min' AND t.template_id = 'segment_loyal_visitor') OR
    (s.segment_id = 'avg_stay_time_max' AND t.template_id = 'segment_bounce_risk') OR
    (s.segment_id = 'referrer_search_engine' AND t.template_id = 'segment_search_engine') OR
    (s.segment_id = 'referrer_campaign' AND t.template_id = 'segment_campaign_visitor') OR
    (s.segment_id = 'min_days_absence' AND t.template_id = 'segment_dormant_return') OR
    (s.segment_id = 'cart_abandonment' AND t.template_id = 'ecom_cart_abandon') OR
    (s.segment_id = 'wishlist_count' AND t.template_id = 'ecom_wishlist_promo') OR
    (s.segment_id = 'purchase_count_repeat' AND t.template_id = 'ecom_repeat_buyer') OR
    (s.segment_id = 'total_purchase_amount' AND t.template_id = 'ecom_high_value_customer')
);

-- =====================================================
-- 인덱스 최적화
-- =====================================================
-- 추가 인덱스는 필요에 따라 생성

-- =====================================================
-- 뷰 생성 (편의를 위한 뷰)
-- =====================================================

-- RFM 세그먼트 상세 뷰
CREATE OR REPLACE VIEW `wp_acf_nudge_rfm_segments_view` AS
SELECT 
    r.*,
    CONCAT('R', r.r_score_min, '-', r.r_score_max, ' F', r.f_score_min, '-', r.f_score_max, ' M', r.m_score_min, '-', r.m_score_max) AS rfm_code
FROM `wp_acf_nudge_rfm_segments` r
WHERE r.is_active = 1;

-- 템플릿-세그먼트 매핑 뷰
CREATE OR REPLACE VIEW `wp_acf_nudge_template_segment_view` AS
SELECT 
    t.id AS template_id,
    t.template_id AS template_code,
    t.title,
    t.category,
    t.type,
    COALESCE(s.segment_id, r.segment_code) AS segment_code,
    COALESCE(s.name, r.segment_name) AS segment_name,
    m.match_priority,
    m.is_recommended
FROM `wp_acf_nudge_templates` t
LEFT JOIN `wp_acf_nudge_segment_template_mapping` m ON t.id = m.template_id
LEFT JOIN `wp_acf_nudge_customer_segments` s ON m.segment_id = s.id
LEFT JOIN `wp_acf_nudge_rfm_segments` r ON m.rfm_segment_id = r.id
WHERE t.is_active = 1;

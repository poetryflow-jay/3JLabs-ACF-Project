-- ============================================
-- ACF Nudge Flow - 고객 세그먼트 & 넛지 메시지 데이터베이스
-- 버전: 1.0.0
-- 생성일: 2026-01-04
-- 용도: 마케팅 자동화 세그먼트, RFM 분석, 디자인 템플릿 관리
-- 참고: https://docs.google.com/spreadsheets/d/1qpBth6BG26T8mGk801Y-D9gDgQJeZoBOh0nf_l2caUo
-- ============================================

-- WordPress 테이블 접두사 변수 (설치 시 교체)
-- {$wpdb->prefix} = wp_

-- ============================================
-- 1. RFM 세그먼트 정의 테이블
-- RFM 분석 기반 고객 세그먼트 11종
-- ============================================
DROP TABLE IF EXISTS {$wpdb->prefix}nf_rfm_segments;
CREATE TABLE {$wpdb->prefix}nf_rfm_segments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    segment_key VARCHAR(50) NOT NULL UNIQUE COMMENT '세그먼트 고유 키',
    segment_name VARCHAR(100) NOT NULL COMMENT '한글 세그먼트명',
    segment_name_en VARCHAR(100) NOT NULL COMMENT '영문 세그먼트명',
    r_score_min TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'R 점수 최소값 (1-5)',
    r_score_max TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT 'R 점수 최대값 (1-5)',
    f_score_min TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'F 점수 최소값 (1-5)',
    f_score_max TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT 'F 점수 최대값 (1-5)',
    m_level ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium' COMMENT '구매 금액 수준',
    priority_rank TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT '우선순위 (1=최고)',
    description TEXT COMMENT '세그먼트 설명',
    marketing_action TEXT COMMENT '권장 마케팅 액션',
    color_code VARCHAR(7) DEFAULT '#6366f1' COMMENT 'UI 표시 색상',
    icon VARCHAR(50) DEFAULT 'star' COMMENT 'UI 아이콘',
    is_active TINYINT(1) DEFAULT 1 COMMENT '활성 상태',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_segment_key (segment_key),
    INDEX idx_priority (priority_rank),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='RFM 세그먼트 정의';

INSERT INTO {$wpdb->prefix}nf_rfm_segments
(id, segment_key, segment_name, segment_name_en, r_score_min, r_score_max, f_score_min, f_score_max, m_level, priority_rank, description, marketing_action, color_code, icon) VALUES
(1, 'diamond', '다이아몬드', 'Diamond', 5, 5, 5, 5, 'high', 1, '최근 구매 + 고빈도 + 고금액. 최상위 VIP 고객으로 전체 매출의 핵심 기여자', 'VIP 전용 리워드 프로그램, 1:1 컨시어지 서비스, 신제품 우선 체험, 특별 이벤트 초대', '#fbbf24', 'crown'),
(2, 'loyal_customer', '충성 고객', 'Loyal Customer', 4, 5, 4, 5, 'high', 2, '지속적으로 구매하는 우수 고객. 안정적인 매출 기반', '멤버십 혜택 강화, 등급 유지 보너스, 리퍼럴 프로그램 참여 유도', '#22c55e', 'heart'),
(3, 'cannot_lose', '절대 놓치지 마세요', 'Cannot Lose', 1, 2, 4, 5, 'high', 3, '과거 VIP였으나 최근 구매 없음. 이탈 시 큰 손실 예상', '긴급 재활성화 캠페인, 특별 할인 쿠폰, 1:1 연락, Win-back 오퍼', '#ef4444', 'alert-triangle'),
(4, 'potential_loyalist', '잠재적인 충성 고객', 'Potential Loyalist', 4, 5, 2, 3, 'medium', 4, '최근 구매 활발하고 빈도 성장 중. 충성 고객으로 전환 가능성 높음', '구매 빈도 증가 유도, 정기 구독 제안, 크로스셀 추천', '#8b5cf6', 'trending-up'),
(5, 'promising', '유망주', 'Promising', 3, 4, 2, 3, 'medium', 5, '성장 잠재력 있는 고객. 적절한 육성으로 충성 고객화 가능', '크로스셀/업셀 캠페인, 관련 상품 추천, 리뷰 작성 유도', '#3b82f6', 'star'),
(6, 'at_risk', '갈림길 고객', 'At Risk', 2, 3, 3, 4, 'medium', 6, '이탈 위험이 있는 고객. 과거 활동적이었으나 최근 감소세', '리텐션 캠페인, 쿠폰 제공, 설문조사를 통한 불만 파악', '#f97316', 'alert-circle'),
(7, 'need_attention', '위기 관리 필요', 'Need Attention', 1, 2, 2, 3, 'medium', 7, '구매 감소 추세 고객. 빠른 대응 필요', '재구매 유도 프로모션, 맞춤형 추천, 이메일/SMS 리마인드', '#dc2626', 'clock'),
(8, 'new_customer', '신규 고객', 'New Customer', 5, 5, 1, 1, 'low', 8, '최근 첫 구매 고객. 두 번째 구매 전환이 핵심', '웰컴 시리즈, 두 번째 구매 쿠폰, 사용 가이드, 온보딩 이메일', '#06b6d4', 'user-plus'),
(9, 'one_time_buyer', '1회성 고객', 'One-time Buyer', 2, 3, 1, 1, 'low', 9, '단발성 구매 후 이탈. 재구매 전환 필요', '재구매 인센티브, 제품 리뷰 요청, 관련 상품 추천', '#94a3b8', 'shopping-bag'),
(10, 'hibernating', '겨울잠 고객', 'Hibernating', 1, 1, 1, 2, 'low', 10, '장기간 미구매 고객. 재활성화 여부 결정 필요', '재활성화 캠페인, 특별 할인, 브랜드 뉴스레터', '#64748b', 'moon'),
(11, 'about_to_sleep', '리마인드가 필요해요', 'About to Sleep', 2, 2, 1, 2, 'low', 11, '이탈 직전 단계 고객. 즉각적인 개입 필요', '리마인드 메시지, 할인 쿠폰, 장바구니 알림', '#a855f7', 'bell');


-- ============================================
-- 2. 세그먼트 스토어 테이블
-- 방문자 행동 기반 세그먼트 50종
-- ============================================
DROP TABLE IF EXISTS {$wpdb->prefix}nf_segment_store;
CREATE TABLE {$wpdb->prefix}nf_segment_store (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL COMMENT '카테고리',
    category_en VARCHAR(50) NOT NULL COMMENT '영문 카테고리',
    segment_name VARCHAR(200) NOT NULL COMMENT '세그먼트명',
    segment_name_en VARCHAR(200) COMMENT '영문 세그먼트명',
    parameters JSON COMMENT '필요 파라미터 (JSON 배열)',
    parameter_types JSON COMMENT '파라미터 타입 정의',
    description VARCHAR(200) COMMENT '설명',
    use_case VARCHAR(100) COMMENT '활용 사례',
    trigger_type ENUM('visit_start', 'referrer', 'page_view', 'behavior', 'ecommerce') NOT NULL DEFAULT 'visit_start' COMMENT '트리거 타입',
    complexity ENUM('simple', 'medium', 'advanced') DEFAULT 'simple' COMMENT '복잡도',
    is_premium TINYINT(1) DEFAULT 0 COMMENT '프리미엄 기능 여부',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_trigger (trigger_type),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='방문자 세그먼트 정의';

INSERT INTO {$wpdb->prefix}nf_segment_store
(id, category, category_en, segment_name, segment_name_en, parameters, parameter_types, description, use_case, trigger_type, complexity, is_premium, sort_order) VALUES
-- 방문이 시작되었을 때 (Visit Start)
(1, '방문이 시작되었을 때', 'Visit Start', '사이트를 처음 방문한 방문객', 'First-time Visitors', NULL, NULL, '첫 방문자 타겟팅', '신규 방문자 환영', 'visit_start', 'simple', 0, 1),
(2, '방문이 시작되었을 때', 'Visit Start', '사이트를 다시 방문한 방문객', 'Returning Visitors', NULL, NULL, '재방문자 타겟팅', '재방문 감사 메시지', 'visit_start', 'simple', 0, 2),
(3, '방문이 시작되었을 때', 'Visit Start', '누적 방문수가 OO회 이하인 방문객', 'Visitors with Max Visit Count', '["visit_count_max"]', '{"visit_count_max": {"type": "number", "label": "최대 방문 횟수", "default": 3}}', '방문 횟수 제한', '초기 단계 방문자', 'visit_start', 'simple', 0, 3),
(4, '방문이 시작되었을 때', 'Visit Start', '누적 방문수가 OO회 이상인 방문객', 'Visitors with Min Visit Count', '["visit_count_min"]', '{"visit_count_min": {"type": "number", "label": "최소 방문 횟수", "default": 5}}', '충성 방문자', '단골 고객 혜택', 'visit_start', 'simple', 0, 4),
(5, '방문이 시작되었을 때', 'Visit Start', '누적 방문수가 OO회~OO회 사이인 방문객', 'Visitors in Visit Range', '["visit_count_min", "visit_count_max"]', '{"visit_count_min": {"type": "number", "label": "최소"}, "visit_count_max": {"type": "number", "label": "최대"}}', '구간 타겟팅', '특정 방문 구간', 'visit_start', 'medium', 0, 5),
(6, '방문이 시작되었을 때', 'Visit Start', '마지막 방문 이후 OO일 만에 다시 방문한 방문객', 'Visitors After N Days', '["days_since_last_visit"]', '{"days_since_last_visit": {"type": "number", "label": "경과 일수", "default": 7}}', '재방문 주기', '장기 미방문 후 복귀', 'visit_start', 'medium', 0, 6),
(7, '방문이 시작되었을 때', 'Visit Start', '마지막 방문 이후 OO일 이상 방문이 없었다가 다시 방문한 방문객', 'Dormant Returning Visitors', '["min_days_absence"]', '{"min_days_absence": {"type": "number", "label": "최소 미방문 일수", "default": 30}}', '휴면 복귀자', '휴면 고객 재활성화', 'visit_start', 'medium', 0, 7),
(8, '방문이 시작되었을 때', 'Visit Start', '최근 OO일 동안 사이트를 OO회 이상 방문한 방문객', 'Active Visitors in Period', '["days_period", "visit_count_min"]', '{"days_period": {"type": "number", "label": "기간(일)"}, "visit_count_min": {"type": "number", "label": "최소 방문"}}', '활성 사용자', '활동적 사용자 보상', 'visit_start', 'medium', 0, 8),
(9, '방문이 시작되었을 때', 'Visit Start', '평균 체류시간이 OO초 이내인 방문객', 'Low Engagement Visitors', '["avg_stay_seconds_max"]', '{"avg_stay_seconds_max": {"type": "number", "label": "최대 체류 초", "default": 30}}', '이탈 위험', '이탈 방지', 'visit_start', 'simple', 0, 9),
(10, '방문이 시작되었을 때', 'Visit Start', '평균 체류시간이 OO분 이상인 방문객', 'High Engagement Visitors', '["avg_stay_minutes_min"]', '{"avg_stay_minutes_min": {"type": "number", "label": "최소 체류 분", "default": 5}}', '관심 고객', '관심 고객 전환', 'visit_start', 'simple', 0, 10),
(11, '방문이 시작되었을 때', 'Visit Start', '평균 페이지뷰가 OO회 이하인 방문객', 'Low Pageview Visitors', '["avg_pageview_max"]', '{"avg_pageview_max": {"type": "number", "label": "최대 페이지뷰"}}', '낮은 참여', '콘텐츠 유도', 'visit_start', 'simple', 0, 11),
(12, '방문이 시작되었을 때', 'Visit Start', '평균 페이지뷰가 OO회 이상인 방문객', 'High Pageview Visitors', '["avg_pageview_min"]', '{"avg_pageview_min": {"type": "number", "label": "최소 페이지뷰"}}', '높은 참여', '전환 유도', 'visit_start', 'simple', 0, 12),
(13, '방문이 시작되었을 때', 'Visit Start', 'OO시간대에 방문하는 방문객', 'Time-based Visitors', '["visit_hour"]', '{"visit_hour": {"type": "select", "label": "방문 시간대", "options": [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]}}', '시간대 타겟팅', '시간대별 프로모션', 'visit_start', 'simple', 0, 13),
(14, '방문이 시작되었을 때', 'Visit Start', '#페이지에서 방문이 시작된 방문객', 'Landing Page Visitors', '["landing_page"]', '{"landing_page": {"type": "url", "label": "랜딩 페이지 URL"}}', '랜딩페이지 기반', '페이지별 메시지', 'visit_start', 'medium', 0, 14),
(15, '방문이 시작되었을 때', 'Visit Start', '#파라미터를 포함한 페이지에서 방문이 시작된 방문객', 'URL Parameter Visitors', '["url_parameter"]', '{"url_parameter": {"type": "text", "label": "URL 파라미터", "placeholder": "utm_source=google"}}', 'UTM 등 파라미터', '캠페인 추적', 'visit_start', 'medium', 0, 15),
(16, '방문이 시작되었을 때', 'Visit Start', 'PC로 방문한 방문객', 'Desktop Visitors', NULL, NULL, '디바이스 필터', 'PC 전용 프로모션', 'visit_start', 'simple', 0, 16),
(17, '방문이 시작되었을 때', 'Visit Start', '모바일로 방문한 방문객', 'Mobile Visitors', NULL, NULL, '디바이스 필터', '모바일 최적화', 'visit_start', 'simple', 0, 17),
(18, '방문이 시작되었을 때', 'Visit Start', '#운영체제로 방문한 방문객', 'OS-based Visitors', '["os_type"]', '{"os_type": {"type": "select", "label": "운영체제", "options": ["Windows", "macOS", "iOS", "Android", "Linux"]}}', 'OS 타겟팅', 'OS별 최적화', 'visit_start', 'simple', 0, 18),
(19, '방문이 시작되었을 때', 'Visit Start', '#브라우저로 방문한 방문객', 'Browser-based Visitors', '["browser_type"]', '{"browser_type": {"type": "select", "label": "브라우저", "options": ["Chrome", "Safari", "Firefox", "Edge", "Samsung Internet"]}}', '브라우저 타겟팅', '브라우저 호환성', 'visit_start', 'simple', 0, 19),

-- 다른 사이트에서 방문했을 때 (Referrer)
(20, '다른 사이트에서 방문했을 때', 'Referrer', '#검색엔진에서 방문한 방문객', 'Search Engine Visitors', '["search_engine"]', '{"search_engine": {"type": "select", "label": "검색엔진", "options": ["Google", "Naver", "Daum", "Bing", "Yahoo"]}}', '검색유입', 'SEO 성과 추적', 'referrer', 'simple', 0, 20),
(21, '다른 사이트에서 방문했을 때', 'Referrer', '#검색엔진에서 #검색어로 방문한 방문객', 'Keyword Search Visitors', '["search_engine", "keyword"]', '{"search_engine": {"type": "select", "label": "검색엔진"}, "keyword": {"type": "text", "label": "검색어"}}', '키워드 타겟팅', '키워드 맞춤 랜딩', 'referrer', 'advanced', 1, 21),
(22, '다른 사이트에서 방문했을 때', 'Referrer', '#검색광고로 방문한 방문객', 'Paid Search Visitors', '["ad_source"]', '{"ad_source": {"type": "select", "label": "광고 소스", "options": ["Google Ads", "Naver SA", "Kakao Moment", "Facebook Ads"]}}', '검색광고 유입', '광고 ROI 분석', 'referrer', 'medium', 0, 22),
(23, '다른 사이트에서 방문했을 때', 'Referrer', '#캠페인으로 방문한 방문객', 'Campaign Visitors', '["campaign"]', '{"campaign": {"type": "text", "label": "캠페인명"}}', '캠페인 추적', '캠페인별 메시지', 'referrer', 'medium', 0, 23),
(24, '다른 사이트에서 방문했을 때', 'Referrer', '#소셜미디어에서 방문한 방문객', 'Social Media Visitors', '["social_media"]', '{"social_media": {"type": "select", "label": "소셜미디어", "options": ["Facebook", "Instagram", "Twitter", "LinkedIn", "TikTok", "YouTube"]}}', 'SNS 유입', '소셜 전환 추적', 'referrer', 'simple', 0, 24),
(25, '다른 사이트에서 방문했을 때', 'Referrer', '#외부도메인 또는 URL에서 방문한 방문객', 'External Referrer Visitors', '["referrer_domain"]', '{"referrer_domain": {"type": "text", "label": "레퍼러 도메인"}}', '레퍼러 추적', '파트너 제휴 추적', 'referrer', 'medium', 0, 25),

-- 지정된 페이지를 봤을 때 (Page View)
(26, '지정된 페이지를 봤을 때', 'Page View', '#특정 페이지를 OO회 이상 보고 있는 방문객', 'Repeated Page Viewers', '["page_url", "view_count_min"]', '{"page_url": {"type": "url", "label": "페이지 URL"}, "view_count_min": {"type": "number", "label": "최소 조회수"}}', '페이지 관심도', '관심 상품 프로모션', 'page_view', 'medium', 0, 26),
(27, '지정된 페이지를 봤을 때', 'Page View', '#특정 페이지를 OO초 이상 보고 있는 방문객', 'Long Stay Page Viewers', '["page_url", "stay_seconds_min"]', '{"page_url": {"type": "url", "label": "페이지 URL"}, "stay_seconds_min": {"type": "number", "label": "최소 체류 초"}}', '체류 기반', '깊은 관심 유도', 'page_view', 'medium', 0, 27),
(28, '지정된 페이지를 봤을 때', 'Page View', '#콘텐츠 그룹을 OO일 동안 OO회 이상 본 방문객', 'Content Group Viewers', '["content_group", "days_period", "view_count_min"]', '{"content_group": {"type": "text", "label": "콘텐츠 그룹"}, "days_period": {"type": "number", "label": "기간(일)"}, "view_count_min": {"type": "number", "label": "최소 조회수"}}', '콘텐츠 그룹', '콘텐츠 관심 분석', 'page_view', 'advanced', 1, 28),
(29, '지정된 페이지를 봤을 때', 'Page View', '방문 시작 후 페이지뷰가 OO회 이상인 방문객', 'Active Session Visitors', '["session_pageview_min"]', '{"session_pageview_min": {"type": "number", "label": "최소 세션 PV"}}', '세션 활동', '활발한 탐색자', 'page_view', 'simple', 0, 29),
(30, '지정된 페이지를 봤을 때', 'Page View', '#페이지(A)에서 #페이지(B)로 이동한 방문객', 'Page Flow Visitors', '["page_from", "page_to"]', '{"page_from": {"type": "url", "label": "시작 페이지"}, "page_to": {"type": "url", "label": "도착 페이지"}}', '이동 경로', '퍼널 분석', 'page_view', 'advanced', 1, 30),

-- 특정 행동에 해당될 때 (Behavior)
(31, '특정 행동에 해당될 때', 'Behavior', '사이트에서 #내부검색어를 검색한 방문객', 'Internal Search Users', '["internal_search_keyword"]', '{"internal_search_keyword": {"type": "text", "label": "검색어"}}', '내부 검색', '검색 의도 파악', 'behavior', 'medium', 0, 31),
(32, '특정 행동에 해당될 때', 'Behavior', '사이트 내부 검색을 했으나 검색결과가 없는 경우', 'Failed Search Users', NULL, NULL, '검색 실패', '대안 제시', 'behavior', 'simple', 0, 32),
(33, '특정 행동에 해당될 때', 'Behavior', '#회원가입 완료 페이지에 도달했을 때', 'Signup Complete Visitors', '["signup_complete_page"]', '{"signup_complete_page": {"type": "url", "label": "가입완료 페이지"}}', '회원가입 완료', '환영 메시지', 'behavior', 'simple', 0, 33),
(34, '특정 행동에 해당될 때', 'Behavior', '회원가입 정보입력 페이지를 보았으나 완료페이지까지 도달하지 못한 방문객', 'Signup Abandoners', '["signup_form_page", "signup_complete_page"]', '{"signup_form_page": {"type": "url", "label": "가입폼 페이지"}, "signup_complete_page": {"type": "url", "label": "가입완료 페이지"}}', '가입 이탈', '가입 완료 유도', 'behavior', 'advanced', 1, 34),
(35, '특정 행동에 해당될 때', 'Behavior', '회원가입일이 최근 OO일 이내인 회원', 'New Members', '["days_since_signup"]', '{"days_since_signup": {"type": "number", "label": "가입 후 일수", "default": 7}}', '신규 회원', '온보딩 시리즈', 'behavior', 'simple', 0, 35),
(36, '특정 행동에 해당될 때', 'Behavior', '로그인을 한 회원', 'Logged-in Members', NULL, NULL, '로그인 회원', '회원 전용 혜택', 'behavior', 'simple', 0, 36),
(37, '특정 행동에 해당될 때', 'Behavior', '성별이 #남 #여 인 방문객', 'Gender-based Targeting', '["gender"]', '{"gender": {"type": "select", "label": "성별", "options": ["male", "female"]}}', '성별 타겟팅', '성별 맞춤 상품', 'behavior', 'simple', 0, 37),
(38, '특정 행동에 해당될 때', 'Behavior', '연령이 #10대이하~#60대이상 인 회원', 'Age-based Targeting', '["age_group"]', '{"age_group": {"type": "select", "label": "연령대", "options": ["10s", "20s", "30s", "40s", "50s", "60s+"]}}', '연령 타겟팅', '연령대별 마케팅', 'behavior', 'simple', 0, 38),
(39, '특정 행동에 해당될 때', 'Behavior', '#회원탈퇴 페이지에 도달한 회원', 'Withdrawal Intent Visitors', '["withdrawal_page"]', '{"withdrawal_page": {"type": "url", "label": "탈퇴 페이지"}}', '탈퇴 위험', '탈퇴 방지', 'behavior', 'medium', 0, 39),

-- 전자 상거래 (E-commerce)
(40, '전자 상거래', 'E-commerce', '방문 시작 후 동일한 상품을 OO회 이상 본 방문객', 'Product Interest Visitors', '["product_view_count_min"]', '{"product_view_count_min": {"type": "number", "label": "최소 조회수", "default": 3}}', '상품 관심', '관심 상품 프로모션', 'ecommerce', 'medium', 0, 40),
(41, '전자 상거래', 'E-commerce', '동일한 카테고리의 상품을 OO시간 동안 OO회 이상 본 방문객', 'Category Interest Visitors', '["category_id", "hours_period", "view_count_min"]', '{"category_id": {"type": "text", "label": "카테고리"}, "hours_period": {"type": "number", "label": "기간(시간)"}, "view_count_min": {"type": "number", "label": "최소 조회수"}}', '카테고리 관심', '카테고리 프로모션', 'ecommerce', 'advanced', 1, 41),
(42, '전자 상거래', 'E-commerce', '#특정 상품을 OO회 이상 보고 있는 방문객', 'Specific Product Viewers', '["product_id", "view_count_min"]', '{"product_id": {"type": "text", "label": "상품 ID"}, "view_count_min": {"type": "number", "label": "최소 조회수"}}', '특정 상품', '상품별 프로모션', 'ecommerce', 'medium', 0, 42),
(43, '전자 상거래', 'E-commerce', '#특정 카테고리를 OO회 이상 보고 있는 방문객', 'Specific Category Viewers', '["category_id", "view_count_min"]', '{"category_id": {"type": "text", "label": "카테고리"}, "view_count_min": {"type": "number", "label": "최소 조회수"}}', '특정 카테고리', '카테고리 타겟팅', 'ecommerce', 'medium', 0, 43),
(44, '전자 상거래', 'E-commerce', '위시리스트에 보관중인 상품이 OO개 이상인 방문객', 'Wishlist Users', '["wishlist_count_min"]', '{"wishlist_count_min": {"type": "number", "label": "최소 위시리스트 수", "default": 3}}', '위시리스트', '위시리스트 프로모션', 'ecommerce', 'medium', 0, 44),
(45, '전자 상거래', 'E-commerce', '장바구니에 담긴 상품이 OO개 이상인 방문객', 'Cart Users', '["cart_item_count_min"]', '{"cart_item_count_min": {"type": "number", "label": "최소 장바구니 수", "default": 1}}', '장바구니', '장바구니 전환 유도', 'ecommerce', 'simple', 0, 45),
(46, '전자 상거래', 'E-commerce', '최근 OO일 이내 장바구니에 상품을 추가했으나 구매하지 않은 방문객', 'Cart Abandoners', '["days_since_cart_add"]', '{"days_since_cart_add": {"type": "number", "label": "경과 일수", "default": 3}}', '장바구니 이탈', '장바구니 리마인드', 'ecommerce', 'medium', 0, 46),
(47, '전자 상거래', 'E-commerce', '지금까지 구매수가 1회인 방문객', 'One-time Buyers', NULL, NULL, '1회 구매자', '재구매 유도', 'ecommerce', 'simple', 0, 47),
(48, '전자 상거래', 'E-commerce', '누적 구매수가 2회 이상인 방문객', 'Repeat Buyers', NULL, NULL, '재구매자', '충성 고객 혜택', 'ecommerce', 'simple', 0, 48),
(49, '전자 상거래', 'E-commerce', '구매 경험이 없는 방문객', 'Non-buyers', NULL, NULL, '비구매자', '첫 구매 유도', 'ecommerce', 'simple', 0, 49),
(50, '전자 상거래', 'E-commerce', '누적 구매액이 OO원 이상인 방문객', 'High Value Customers', '["total_purchase_amount_min"]', '{"total_purchase_amount_min": {"type": "number", "label": "최소 구매액", "default": 100000}}', 'VIP', 'VIP 혜택', 'ecommerce', 'medium', 0, 50);


-- ============================================
-- 3. 고객 세분화 필터 테이블
-- 상세 필터 조건 101종
-- ============================================
DROP TABLE IF EXISTS {$wpdb->prefix}nf_customer_filters;
CREATE TABLE {$wpdb->prefix}nf_customer_filters (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL COMMENT '대분류',
    category_en VARCHAR(50) NOT NULL COMMENT '영문 대분류',
    filter_name VARCHAR(100) NOT NULL COMMENT '필터명',
    filter_name_en VARCHAR(100) COMMENT '영문 필터명',
    filter_key VARCHAR(50) NOT NULL UNIQUE COMMENT '필터 고유 키',
    description VARCHAR(200) COMMENT '설명',
    use_case VARCHAR(100) COMMENT '활용 사례',
    value_type ENUM('boolean', 'number', 'range', 'select', 'text', 'date', 'multi_select') DEFAULT 'number' COMMENT '값 타입',
    value_options JSON COMMENT '선택 옵션 (select 타입)',
    default_value VARCHAR(100) COMMENT '기본값',
    is_premium TINYINT(1) DEFAULT 0 COMMENT '프리미엄 기능',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_filter_key (filter_key),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='고객 세분화 필터';

INSERT INTO {$wpdb->prefix}nf_customer_filters
(id, category, category_en, filter_name, filter_name_en, filter_key, description, use_case, value_type, value_options, default_value, is_premium, sort_order) VALUES
-- RFM 세그먼트 (1-12)
(1, 'RFM 세그먼트', 'RFM Segment', '절대 놓치지 마세요', 'Cannot Lose', 'rfm_cannot_lose', '과거 VIP, 최근 구매 없음', '긴급 재활성화', 'boolean', NULL, NULL, 0, 1),
(2, 'RFM 세그먼트', 'RFM Segment', '잠재적인 충성 고객', 'Potential Loyalist', 'rfm_potential_loyalist', '최근 구매 활발, 빈도 성장 중', '구매 빈도 증가 유도', 'boolean', NULL, NULL, 0, 2),
(3, 'RFM 세그먼트', 'RFM Segment', '충성 고객', 'Loyal Customer', 'rfm_loyal_customer', '지속적으로 구매하는 우수 고객', '멤버십 혜택', 'boolean', NULL, NULL, 0, 3),
(4, 'RFM 세그먼트', 'RFM Segment', '다이아몬드', 'Diamond', 'rfm_diamond', '최근+고빈도+고금액', 'VIP 관리', 'boolean', NULL, NULL, 0, 4),
(5, 'RFM 세그먼트', 'RFM Segment', '모든 고객', 'All Customers', 'rfm_all', '전체 고객', '기본 타겟', 'boolean', NULL, NULL, 0, 5),
(6, 'RFM 세그먼트', 'RFM Segment', '유망주', 'Promising', 'rfm_promising', '성장 잠재력 있는 고객', '크로스셀/업셀', 'boolean', NULL, NULL, 0, 6),
(7, 'RFM 세그먼트', 'RFM Segment', '갈림길 고객', 'At Risk', 'rfm_at_risk', '이탈 위험이 있는 고객', '리텐션 캠페인', 'boolean', NULL, NULL, 0, 7),
(8, 'RFM 세그먼트', 'RFM Segment', '겨울잠 고객', 'Hibernating', 'rfm_hibernating', '장기간 미구매 고객', '재활성화 캠페인', 'boolean', NULL, NULL, 0, 8),
(9, 'RFM 세그먼트', 'RFM Segment', '리마인드가 필요해요', 'About to Sleep', 'rfm_about_to_sleep', '이탈 직전 단계 고객', '리마인드 메시지', 'boolean', NULL, NULL, 0, 9),
(10, 'RFM 세그먼트', 'RFM Segment', '신규 고객', 'New Customer', 'rfm_new_customer', '최근 첫 구매 고객', '웰컴 시리즈', 'boolean', NULL, NULL, 0, 10),
(11, 'RFM 세그먼트', 'RFM Segment', '위기 관리 필요', 'Need Attention', 'rfm_need_attention', '구매 감소 추세 고객', '재구매 유도', 'boolean', NULL, NULL, 0, 11),
(12, 'RFM 세그먼트', 'RFM Segment', '1회성 고객', 'One-time Buyer', 'rfm_one_time_buyer', '단발성 구매 후 이탈', '재구매 인센티브', 'boolean', NULL, NULL, 0, 12),

-- 트래픽 (13-23)
(13, '트래픽', 'Traffic', '모든 방문객', 'All Visitors', 'traffic_all', '전체 방문객', '기본 타겟', 'boolean', NULL, NULL, 0, 13),
(14, '트래픽', 'Traffic', '방문 횟수', 'Visit Count', 'traffic_visit_count', '누적 방문 횟수 기준', '충성도 분석', 'range', NULL, NULL, 0, 14),
(15, '트래픽', 'Traffic', '방문 시간대', 'Visit Hour', 'traffic_visit_hour', '방문 시간대별 분석', '시간대 타겟팅', 'multi_select', '["0-6", "6-12", "12-18", "18-24"]', NULL, 0, 15),
(16, '트래픽', 'Traffic', '재방문 간격', 'Revisit Interval', 'traffic_revisit_interval', '방문 간격 일수', '이탈 예측', 'range', NULL, NULL, 0, 16),
(17, '트래픽', 'Traffic', '첫방문/재방문', 'First/Return Visit', 'traffic_visit_type', '첫방문 vs 재방문 구분', '신규/기존 분리', 'select', '["first", "return"]', NULL, 0, 17),
(18, '트래픽', 'Traffic', '최근 방문일', 'Last Visit Date', 'traffic_last_visit', '마지막 방문일 기준', '활성도 분석', 'date', NULL, NULL, 0, 18),
(19, '트래픽', 'Traffic', '방문 요일', 'Visit Day', 'traffic_visit_day', '요일별 방문 패턴', '요일 타겟팅', 'multi_select', '["mon", "tue", "wed", "thu", "fri", "sat", "sun"]', NULL, 0, 19),
(20, '트래픽', 'Traffic', '세션 체류시간', 'Session Duration', 'traffic_session_duration', '현재 세션 체류시간', '관심도 분석', 'range', NULL, NULL, 0, 20),
(21, '트래픽', 'Traffic', '평균 체류시간', 'Avg Duration', 'traffic_avg_duration', '평균 체류시간', '참여도 분석', 'range', NULL, NULL, 0, 21),
(22, '트래픽', 'Traffic', '세션 페이지뷰', 'Session Pageview', 'traffic_session_pv', '현재 세션 PV수', '탐색 활동', 'range', NULL, NULL, 0, 22),
(23, '트래픽', 'Traffic', '평균 페이지뷰', 'Avg Pageview', 'traffic_avg_pv', '평균 PV수', '참여도 분석', 'range', NULL, NULL, 0, 23),

-- 유입 (24-36)
(24, '유입', 'Referrer', '유입출처', 'Traffic Source', 'referrer_source', '유입 채널 분류', '채널 분석', 'select', '["direct", "organic", "paid", "social", "referral", "email"]', NULL, 0, 24),
(25, '유입', 'Referrer', '유입 도메인', 'Referrer Domain', 'referrer_domain', '레퍼러 도메인', '외부 링크 추적', 'text', NULL, NULL, 0, 25),
(26, '유입', 'Referrer', '검색엔진', 'Search Engine', 'referrer_search_engine', '검색엔진 유입', 'SEO 분석', 'select', '["google", "naver", "daum", "bing", "yahoo"]', NULL, 0, 26),
(27, '유입', 'Referrer', '검색광고 상품', 'Ad Product', 'referrer_ad_product', '광고 상품 유입', '광고 성과', 'select', '["google_ads", "naver_sa", "kakao", "facebook"]', NULL, 0, 27),
(28, '유입', 'Referrer', '검색영역', 'Search Area', 'referrer_search_area', '검색 영역 구분', '검색 성과', 'select', '["organic", "paid", "shopping", "local"]', NULL, 0, 28),
(29, '유입', 'Referrer', '검색어(선택형)', 'Keyword Select', 'referrer_keyword_select', '검색어 선택', '키워드 분석', 'select', NULL, NULL, 1, 29),
(30, '유입', 'Referrer', '검색어(입력형)', 'Keyword Input', 'referrer_keyword_input', '검색어 직접 입력', '키워드 분석', 'text', NULL, NULL, 0, 30),
(31, '유입', 'Referrer', '직전 검색어(네이버)', 'Naver Last Keyword', 'referrer_naver_keyword', '네이버 직전 검색어', '네이버 분석', 'text', NULL, NULL, 1, 31),
(32, '유입', 'Referrer', 'SNS', 'Social Media', 'referrer_sns', 'SNS 유입', '소셜 분석', 'select', '["facebook", "instagram", "twitter", "linkedin", "tiktok", "youtube"]', NULL, 0, 32),
(33, '유입', 'Referrer', '광고채널', 'Ad Channel', 'referrer_ad_channel', '광고 채널별', '광고 성과', 'select', NULL, NULL, 0, 33),
(34, '유입', 'Referrer', '캠페인', 'Campaign', 'referrer_campaign', '캠페인 유입', '캠페인 분석', 'text', NULL, NULL, 0, 34),
(35, '유입', 'Referrer', '구글 UTM(선택형)', 'UTM Select', 'referrer_utm_select', 'UTM 선택', '캠페인 추적', 'select', NULL, NULL, 0, 35),
(36, '유입', 'Referrer', '구글 UTM(입력형)', 'UTM Input', 'referrer_utm_input', 'UTM 직접 입력', '캠페인 추적', 'text', NULL, NULL, 0, 36),

-- 콘텐츠 (37-45)
(37, '콘텐츠', 'Content', '시작 페이지(선택형)', 'Landing Page Select', 'content_landing_select', '랜딩페이지 선택', '랜딩 분석', 'select', NULL, NULL, 0, 37),
(38, '콘텐츠', 'Content', '시작 페이지(입력형)', 'Landing Page Input', 'content_landing_input', '랜딩페이지 직접 입력', '랜딩 분석', 'text', NULL, NULL, 0, 38),
(39, '콘텐츠', 'Content', '전환 페이지', 'Conversion Page', 'content_conversion_page', '전환 발생 페이지', '전환 분석', 'text', NULL, NULL, 0, 39),
(40, '콘텐츠', 'Content', '경로상 페이지', 'Path Page', 'content_path_page', '이동 경로 페이지', '여정 분석', 'text', NULL, NULL, 1, 40),
(41, '콘텐츠', 'Content', '경로상 페이지 반복 횟수', 'Path Page Repeat', 'content_path_repeat', '페이지 반복 조회', '관심도 분석', 'range', NULL, NULL, 1, 41),
(42, '콘텐츠', 'Content', '콘텐츠 그룹', 'Content Group', 'content_group', '콘텐츠 그룹별', '콘텐츠 분석', 'text', NULL, NULL, 0, 42),
(43, '콘텐츠', 'Content', '콘텐츠 그룹 반복 횟수', 'Content Group Repeat', 'content_group_repeat', '그룹 반복 조회', '관심도 분석', 'range', NULL, NULL, 1, 43),
(44, '콘텐츠', 'Content', '내부 검색어(선택형)', 'Internal Search Select', 'content_search_select', '사이트 내 검색어', '검색 분석', 'select', NULL, NULL, 0, 44),
(45, '콘텐츠', 'Content', '내부 검색어(입력형)', 'Internal Search Input', 'content_search_input', '검색어 직접 입력', '검색 분석', 'text', NULL, NULL, 0, 45),

-- 구매 (46-53)
(46, '구매', 'Purchase', '누적 구매수', 'Total Purchase Count', 'purchase_total_count', '총 구매 횟수', '구매력 분석', 'range', NULL, NULL, 0, 46),
(47, '구매', 'Purchase', '누적 구매액', 'Total Purchase Amount', 'purchase_total_amount', '총 구매 금액', '고객 가치', 'range', NULL, NULL, 0, 47),
(48, '구매', 'Purchase', '최근 구매액', 'Last Purchase Amount', 'purchase_last_amount', '마지막 구매 금액', '최근 활동', 'range', NULL, NULL, 0, 48),
(49, '구매', 'Purchase', '평균 구매액', 'Avg Purchase Amount', 'purchase_avg_amount', '평균 주문 금액', '객단가 분석', 'range', NULL, NULL, 0, 49),
(50, '구매', 'Purchase', '최근 구매 품목수', 'Last Purchase Items', 'purchase_last_items', '마지막 구매 품목', '장바구니 분석', 'range', NULL, NULL, 0, 50),
(51, '구매', 'Purchase', '구매 금액(M)', 'RFM M Score', 'purchase_rfm_m', 'RFM M 점수', '구매력 등급', 'select', '["1", "2", "3", "4", "5"]', NULL, 0, 51),
(52, '구매', 'Purchase', '구매 빈도(F)', 'RFM F Score', 'purchase_rfm_f', 'RFM F 점수', '충성도 등급', 'select', '["1", "2", "3", "4", "5"]', NULL, 0, 52),
(53, '구매', 'Purchase', '최근 구매(R)', 'RFM R Score', 'purchase_rfm_r', 'RFM R 점수', '활성도 등급', 'select', '["1", "2", "3", "4", "5"]', NULL, 0, 53),

-- 상품 (54-65)
(54, '상품', 'Product', '최근 조회한 상품(선택형)', 'Viewed Product Select', 'product_viewed_select', '조회 상품 선택', '상품 관심', 'select', NULL, NULL, 0, 54),
(55, '상품', 'Product', '최근 조회한 상품(입력형)', 'Viewed Product Input', 'product_viewed_input', '조회 상품 직접 입력', '상품 관심', 'text', NULL, NULL, 0, 55),
(56, '상품', 'Product', '최근 조회한 브랜드', 'Viewed Brand', 'product_viewed_brand', '조회 브랜드', '브랜드 관심', 'text', NULL, NULL, 0, 56),
(57, '상품', 'Product', '최근 구매한 상품(선택형)', 'Purchased Product Select', 'product_purchased_select', '구매 상품 선택', '구매 이력', 'select', NULL, NULL, 0, 57),
(58, '상품', 'Product', '최근 구매한 상품(입력형)', 'Purchased Product Input', 'product_purchased_input', '구매 상품 직접 입력', '구매 이력', 'text', NULL, NULL, 0, 58),
(59, '상품', 'Product', '특정 상품 조회수(상품명)', 'Product View Count Name', 'product_view_count_name', '상품명 기준 조회수', '상품 인기도', 'range', NULL, NULL, 0, 59),
(60, '상품', 'Product', '특정 상품 조회수(상품코드)', 'Product View Count Code', 'product_view_count_code', '상품코드 기준 조회수', '상품 인기도', 'range', NULL, NULL, 0, 60),
(61, '상품', 'Product', '모든 상품 조회수', 'All Product Views', 'product_all_views', '전체 상품 조회', '탐색 활동', 'range', NULL, NULL, 0, 61),
(62, '상품', 'Product', '카테고리 상품 조회수', 'Category Product Views', 'product_category_views', '카테고리별 조회', '카테고리 관심', 'range', NULL, NULL, 0, 62),
(63, '상품', 'Product', '최근 구매한 브랜드', 'Purchased Brand', 'product_purchased_brand', '구매 브랜드', '브랜드 충성도', 'text', NULL, NULL, 0, 63),
(64, '상품', 'Product', '특정 상품 구매수(상품명)', 'Product Purchase Count Name', 'product_purchase_count_name', '상품명 기준 구매수', '상품 판매', 'range', NULL, NULL, 0, 64),
(65, '상품', 'Product', '특정 상품 구매수(상품코드)', 'Product Purchase Count Code', 'product_purchase_count_code', '상품코드 기준 구매수', '상품 판매', 'range', NULL, NULL, 0, 65),

-- 장바구니 (66-71)
(66, '장바구니', 'Cart', '최근 담긴 상품수', 'Cart Item Count', 'cart_item_count', '장바구니 상품 수', '구매 의향', 'range', NULL, NULL, 0, 66),
(67, '장바구니', 'Cart', '최근 담긴 상품총액', 'Cart Total Amount', 'cart_total_amount', '장바구니 금액', '구매 가치', 'range', NULL, NULL, 0, 67),
(68, '장바구니', 'Cart', '최근 담긴 상품(선택형)', 'Cart Product Select', 'cart_product_select', '상품 선택', '장바구니 분석', 'select', NULL, NULL, 0, 68),
(69, '장바구니', 'Cart', '최근 담긴 상품(입력형)', 'Cart Product Input', 'cart_product_input', '상품 직접 입력', '장바구니 분석', 'text', NULL, NULL, 0, 69),
(70, '장바구니', 'Cart', '최근 담은일', 'Last Cart Date', 'cart_last_date', '마지막 담은 날짜', '이탈 예측', 'date', NULL, NULL, 0, 70),
(71, '장바구니', 'Cart', '최근 담긴 브랜드', 'Cart Brand', 'cart_brand', '담긴 브랜드', '브랜드 관심', 'text', NULL, NULL, 0, 71),

-- 주문서 (72)
(72, '주문서', 'Order', '주문서', 'Order Page', 'order_page', '주문서 페이지 도달', '구매 퍼널', 'boolean', NULL, NULL, 0, 72),

-- 회원 (73-79)
(73, '회원', 'Member', '로그인', 'Login Status', 'member_login', '로그인 여부', '회원 구분', 'boolean', NULL, NULL, 0, 73),
(74, '회원', 'Member', '회원 아이디(선택형)', 'Member ID Select', 'member_id_select', '회원 ID 선택', '특정 회원', 'select', NULL, NULL, 1, 74),
(75, '회원', 'Member', '회원 아이디(입력형)', 'Member ID Input', 'member_id_input', '회원 ID 직접 입력', '특정 회원', 'text', NULL, NULL, 0, 75),
(76, '회원', 'Member', '최근 로그인 날짜', 'Last Login Date', 'member_last_login', '마지막 로그인', '활성도', 'date', NULL, NULL, 0, 76),
(77, '회원', 'Member', '누적 로그인 횟수', 'Total Login Count', 'member_login_count', '총 로그인 수', '충성도', 'range', NULL, NULL, 0, 77),
(78, '회원', 'Member', '최근 가입일', 'Signup Date', 'member_signup_date', '가입일 기준', '신규 회원', 'date', NULL, NULL, 0, 78),
(79, '회원', 'Member', '최근 가입일(일수)', 'Days Since Signup', 'member_days_since_signup', '가입 후 경과일', '회원 연차', 'range', NULL, NULL, 0, 79),

-- RFM (80-82)
(80, 'RFM', 'RFM', 'RFM 등급', 'RFM Grade', 'rfm_grade', 'RFM 종합 등급', '고객 가치', 'select', '["diamond", "loyal", "potential", "promising", "at_risk", "need_attention", "new", "one_time", "hibernating", "about_to_sleep", "cannot_lose"]', NULL, 0, 80),
(81, 'RFM', 'RFM', 'RFM 구매 빈도', 'RFM Frequency', 'rfm_frequency', 'F 점수 상세', '충성도 상세', 'select', '["1", "2", "3", "4", "5"]', NULL, 0, 81),
(82, 'RFM', 'RFM', 'RFM 구매 금액', 'RFM Monetary', 'rfm_monetary', 'M 점수 상세', '구매력 상세', 'select', '["1", "2", "3", "4", "5"]', NULL, 0, 82),

-- 위시 리스트 (83-87)
(83, '위시 리스트', 'Wishlist', '최근 상품수', 'Wishlist Item Count', 'wishlist_item_count', '위시리스트 상품 수', '관심도', 'range', NULL, NULL, 0, 83),
(84, '위시 리스트', 'Wishlist', '최근 상품', 'Wishlist Products', 'wishlist_products', '위시리스트 상품', '상품 관심', 'text', NULL, NULL, 0, 84),
(85, '위시 리스트', 'Wishlist', '최근 브랜드', 'Wishlist Brands', 'wishlist_brands', '위시리스트 브랜드', '브랜드 관심', 'text', NULL, NULL, 0, 85),
(86, '위시 리스트', 'Wishlist', '최근 카테고리', 'Wishlist Categories', 'wishlist_categories', '위시리스트 카테고리', '카테고리 관심', 'text', NULL, NULL, 0, 86),
(87, '위시 리스트', 'Wishlist', '최근 담은일', 'Wishlist Last Date', 'wishlist_last_date', '마지막 담은 날짜', '활성도', 'date', NULL, NULL, 0, 87),

-- 브랜드/카테고리 (88-90)
(88, '브랜드/카테고리', 'Brand/Category', '최근 조회한 브랜드', 'Viewed Brand', 'brand_viewed', '조회 브랜드', '브랜드 관심', 'text', NULL, NULL, 0, 88),
(89, '브랜드/카테고리', 'Brand/Category', '카테고리 상품 조회수', 'Category View Count', 'category_view_count', '카테고리별 조회', '카테고리 분석', 'range', NULL, NULL, 0, 89),
(90, '브랜드/카테고리', 'Brand/Category', '최근 구매한 브랜드', 'Purchased Brand', 'brand_purchased', '구매 브랜드', '브랜드 충성도', 'text', NULL, NULL, 0, 90),

-- 사용 환경 (91-97)
(91, '사용 환경', 'Environment', '기기 구분', 'Device Type', 'env_device', 'PC/모바일/태블릿', '디바이스 분석', 'select', '["desktop", "mobile", "tablet"]', NULL, 0, 91),
(92, '사용 환경', 'Environment', '운영체제', 'Operating System', 'env_os', 'OS 종류', '환경 분석', 'select', '["windows", "macos", "ios", "android", "linux"]', NULL, 0, 92),
(93, '사용 환경', 'Environment', '브라우저', 'Browser', 'env_browser', '브라우저 종류', '환경 분석', 'select', '["chrome", "safari", "firefox", "edge", "samsung"]', NULL, 0, 93),
(94, '사용 환경', 'Environment', '해상도', 'Screen Resolution', 'env_resolution', '화면 해상도', 'UX 최적화', 'select', '["1920x1080", "1366x768", "1440x900", "mobile"]', NULL, 0, 94),
(95, '사용 환경', 'Environment', '사용 언어', 'Browser Language', 'env_language', '브라우저 언어', '지역화', 'select', '["ko", "en", "ja", "zh"]', NULL, 0, 95),
(96, '사용 환경', 'Environment', 'IP주소', 'IP Address', 'env_ip', 'IP 기반 분석', '위치 분석', 'text', NULL, NULL, 1, 96),
(97, '사용 환경', 'Environment', '국가', 'Country', 'env_country', '국가별 분류', '지역 타겟팅', 'select', '["KR", "US", "JP", "CN", "other"]', NULL, 0, 97),

-- 기타 (98-101)
(98, '기타', 'Other', '클릭한 오토메시지', 'Clicked Auto Message', 'other_clicked_message', '반응한 메시지', '메시지 효과', 'text', NULL, NULL, 0, 98),
(99, '기타', 'Other', '사용자 정의 트리거', 'Custom Trigger', 'other_custom_trigger', '커스텀 트리거', '고급 분석', 'text', NULL, NULL, 1, 99),
(100, '기타', 'Other', '태그', 'User Tag', 'other_tag', '사용자 태그', '세그먼트 관리', 'text', NULL, NULL, 0, 100),
(101, '기타', 'Other', '태그/ID/기기', 'Complex ID', 'other_complex_id', '복합 식별자', '고급 타겟팅', 'text', NULL, NULL, 1, 101);


-- ============================================
-- 4. 디자인 스토어 테이블
-- 넛지 메시지 디자인 템플릿 14종
-- ============================================
DROP TABLE IF EXISTS {$wpdb->prefix}nf_design_store;
CREATE TABLE {$wpdb->prefix}nf_design_store (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    design_type VARCHAR(50) NOT NULL COMMENT '디자인 타입',
    design_type_en VARCHAR(50) NOT NULL COMMENT '영문 디자인 타입',
    template_name VARCHAR(100) NOT NULL COMMENT '템플릿명',
    template_name_en VARCHAR(100) COMMENT '영문 템플릿명',
    cta_type VARCHAR(50) COMMENT 'CTA 타입',
    icon_image VARCHAR(200) COMMENT '아이콘/이미지',
    sample_text TEXT COMMENT '샘플 텍스트',
    html_template TEXT COMMENT 'HTML 템플릿',
    css_styles TEXT COMMENT 'CSS 스타일',
    default_position ENUM('top', 'bottom', 'center', 'top-left', 'top-right', 'bottom-left', 'bottom-right') DEFAULT 'bottom-right' COMMENT '기본 위치',
    supports_mobile TINYINT(1) DEFAULT 1 COMMENT '모바일 지원',
    animation_type VARCHAR(50) DEFAULT 'fade' COMMENT '애니메이션 타입',
    is_premium TINYINT(1) DEFAULT 0 COMMENT '프리미엄 여부',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_design_type (design_type),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='넛지 디자인 템플릿';

INSERT INTO {$wpdb->prefix}nf_design_store
(id, design_type, design_type_en, template_name, template_name_en, cta_type, icon_image, sample_text, default_position, animation_type, is_premium, sort_order) VALUES
(1, '라운드 플로팅', 'Round Floating', '스타벅스 상품권', 'Starbucks Gift Card', '클릭 없음', 'starbucks-logo', '스타벅스 상품권 받아가세요 - 오늘 회원 가입 하시면 100% 증정', 'bottom-right', 'bounce', 0, 1),
(2, '라운드 플로팅', 'Round Floating', '카카오톡 채널', 'KakaoTalk Channel', '카카오채널 추가', 'gift-emoji', '카톡채널 추가하고 경품받기', 'bottom-right', 'bounce', 0, 2),
(3, '라운드 플로팅', 'Round Floating', '카카오톡 상담', 'KakaoTalk Chat', '카카오톡 상담', 'kakao-logo', '카톡으로 문의주시면 빛보다 빠르게 회신드립니다!', 'bottom-right', 'bounce', 0, 3),
(4, '라운드 플로팅', 'Round Floating', '전화 연결', 'Phone Call', '전화 연결', 'phone-emoji', '무료 상담 전화 안내 - 080-123-1234 로 문의주세요!', 'bottom-right', 'bounce', 0, 4),
(5, '라운드 플로팅', 'Round Floating', '링크 연결', 'Link Redirect', '링크 이동', 'link-icon', '특별 이벤트 페이지로 이동', 'bottom-right', 'bounce', 0, 5),
(6, '리치팝업', 'Rich Popup', '동영상 전용', 'Video Popup', '동영상 재생', NULL, '오늘의 이벤트 (동영상 자동재생)', 'center', 'zoom', 1, 6),
(7, '푸시노트', 'Push Note', '기본형', 'Basic Push', '확인', NULL, '알림 메시지', 'top-right', 'slide', 0, 7),
(8, '팝콘', 'Pop Corn', '기본형', 'Basic Pop', '확인', NULL, '팝업 메시지', 'center', 'fade', 0, 8),
(9, '쿠폰박스', 'Coupon Box', '할인쿠폰', 'Discount Coupon', '쿠폰 다운로드', 'coupon-icon', '할인 쿠폰 증정', 'center', 'zoom', 0, 9),
(10, '웰컴바', 'Welcome Bar', '공지형', 'Announcement', '닫기', NULL, '상단 배너 공지', 'top', 'slide', 0, 10),
(11, '상품추천 PC', 'Product Recommend PC', '추천상품', 'Recommended Products', '상품 클릭', 'product-image', '맞춤 상품 추천', 'bottom', 'slide', 1, 11),
(12, '상품추천 모바일', 'Product Recommend Mobile', '추천상품', 'Recommended Products', '상품 클릭', 'product-image', '모바일 상품 추천', 'bottom', 'slide', 1, 12),
(13, '개인화 상품 추천 PC', 'AI Recommend PC', 'AI 추천', 'AI Recommendation', '상품 클릭', 'ai-icon', '개인화 추천', 'bottom-right', 'slide', 1, 13),
(14, '개인화 상품 추천 모바일', 'AI Recommend Mobile', 'AI 추천', 'AI Recommendation', '상품 클릭', 'ai-icon', '모바일 개인화 추천', 'bottom', 'slide', 1, 14);


-- ============================================
-- 5. 넛지 메시지 템플릿 테이블
-- 세그먼트-디자인 조합 템플릿
-- ============================================
DROP TABLE IF EXISTS {$wpdb->prefix}nf_message_templates;
CREATE TABLE {$wpdb->prefix}nf_message_templates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_key VARCHAR(50) NOT NULL UNIQUE COMMENT '템플릿 고유 키',
    template_name VARCHAR(200) NOT NULL COMMENT '템플릿명',
    template_name_en VARCHAR(200) COMMENT '영문 템플릿명',
    category ENUM('rfm', 'segment', 'design', 'ecommerce', 'custom') NOT NULL DEFAULT 'custom' COMMENT '카테고리',
    rfm_segment_id INT UNSIGNED COMMENT 'RFM 세그먼트 FK',
    segment_store_id INT UNSIGNED COMMENT '세그먼트 스토어 FK',
    design_store_id INT UNSIGNED COMMENT '디자인 스토어 FK',
    title VARCHAR(200) NOT NULL COMMENT '메시지 제목',
    message TEXT NOT NULL COMMENT '메시지 본문',
    cta_text VARCHAR(100) COMMENT 'CTA 버튼 텍스트',
    cta_url VARCHAR(500) COMMENT 'CTA 링크',
    trigger_config JSON COMMENT '트리거 설정',
    style_config JSON COMMENT '스타일 설정',
    schedule_config JSON COMMENT '스케줄 설정',
    price_krw INT UNSIGNED DEFAULT 0 COMMENT '가격 (원)',
    price_usd DECIMAL(10,2) DEFAULT 0 COMMENT '가격 (달러)',
    price_eur DECIMAL(10,2) DEFAULT 0 COMMENT '가격 (유로)',
    is_premium TINYINT(1) DEFAULT 0 COMMENT '프리미엄 여부',
    is_active TINYINT(1) DEFAULT 1,
    usage_count INT UNSIGNED DEFAULT 0 COMMENT '사용 횟수',
    conversion_rate DECIMAL(5,2) DEFAULT 0 COMMENT '평균 전환율',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_rfm (rfm_segment_id),
    INDEX idx_segment (segment_store_id),
    INDEX idx_design (design_store_id),
    INDEX idx_active (is_active),
    INDEX idx_premium (is_premium)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='넛지 메시지 템플릿';

-- RFM 기반 템플릿 (1-5)
INSERT INTO {$wpdb->prefix}nf_message_templates
(template_key, template_name, template_name_en, category, rfm_segment_id, title, message, cta_text, trigger_config, style_config, price_krw, price_usd, price_eur, is_premium) VALUES
('rfm_diamond_vip', '다이아몬드 VIP 전용', 'Diamond VIP Exclusive', 'rfm', 1, '최고의 고객님을 위한 특별 혜택', '다이아몬드 등급 고객님만을 위한 VIP 전용 프리미엄 혜택을 준비했습니다. 특별한 경험을 누려보세요.', 'VIP 혜택 보기', '{"trigger": "visit_start", "delay": 2000}', '{"theme": "luxury", "color": "#fbbf24"}', 35000, 25, 23, 1),
('rfm_cannot_lose_sos', '절대 놓치지 마세요 SOS', 'Cannot Lose SOS', 'rfm', 3, '다시 만나서 반갑습니다', '오랜만에 방문해 주셔서 감사합니다! 고객님만을 위한 특별 할인 쿠폰을 준비했어요.', '쿠폰 받기', '{"trigger": "visit_start", "condition": "days_absence > 30"}', '{"theme": "urgent", "color": "#ef4444"}', 29000, 21, 19, 1),
('rfm_new_customer_welcome', '신규 고객 웰컴', 'New Customer Welcome', 'rfm', 8, '환영합니다! 첫 구매 감사 쿠폰', '첫 구매를 축하드립니다! 다음 구매 시 사용 가능한 감사 쿠폰을 드립니다.', '쿠폰 확인하기', '{"trigger": "purchase_complete", "condition": "purchase_count == 1"}', '{"theme": "welcome", "color": "#06b6d4"}', 15000, 11, 10, 0),
('rfm_hibernating_reactivate', '휴면 고객 재활성화', 'Hibernating Reactivation', 'rfm', 10, '보고 싶었어요!', '오랫동안 방문이 없으셨네요. 특별 할인과 함께 다시 만나요!', '할인 받기', '{"trigger": "visit_start", "condition": "days_absence > 90"}', '{"theme": "warm", "color": "#64748b"}', 25000, 18, 17, 1),
('rfm_at_risk_retention', '이탈 위험 리텐션', 'At Risk Retention', 'rfm', 6, '떠나지 마세요!', '고객님의 방문이 줄어들고 있어요. 특별한 혜택으로 다시 찾아주세요!', '혜택 확인', '{"trigger": "exit_intent"}', '{"theme": "alert", "color": "#f97316"}', 19000, 14, 13, 0);

-- 세그먼트 기반 템플릿 (6-10)
INSERT INTO {$wpdb->prefix}nf_message_templates
(template_key, template_name, template_name_en, category, segment_store_id, title, message, cta_text, trigger_config, style_config, price_krw, price_usd, price_eur, is_premium) VALUES
('segment_loyal_visitor', '충성 방문자 감사', 'Loyal Visitor Thanks', 'segment', 4, '단골 고객님 감사합니다!', '5회 이상 방문해 주셨네요! 단골 고객님께만 드리는 특별 혜택입니다.', '혜택 확인', '{"trigger": "visit_start", "condition": "visit_count >= 5"}', '{"theme": "appreciation", "color": "#22c55e"}', 15000, 11, 10, 0),
('segment_bounce_risk', '이탈 위험 방문자', 'Bounce Risk Visitor', 'segment', 9, '잠깐만요!', '떠나시기 전에 이 정보를 확인해 보세요. 도움이 될 거예요!', '자세히 보기', '{"trigger": "exit_intent", "delay": 0}', '{"theme": "urgent", "color": "#ef4444"}', 19000, 14, 13, 0),
('segment_search_engine', '검색엔진 유입자', 'Search Engine Visitor', 'segment', 20, '찾으시는 정보가 있으신가요?', '검색을 통해 방문해 주셨군요! 원하시는 정보를 빠르게 찾아드릴게요.', '도움 받기', '{"trigger": "visit_start", "condition": "referrer == search"}', '{"theme": "helpful", "color": "#3b82f6"}', 15000, 11, 10, 0),
('segment_campaign_visitor', '캠페인 유입자', 'Campaign Visitor', 'segment', 23, '특별 프로모션 안내', '캠페인을 통해 방문해 주셨군요! 지금만 제공되는 특별 할인을 놓치지 마세요.', '할인 받기', '{"trigger": "visit_start", "condition": "has_utm_campaign"}', '{"theme": "promo", "color": "#8b5cf6"}', 19000, 14, 13, 0),
('segment_dormant_return', '휴면 복귀자 환영', 'Dormant Returner Welcome', 'segment', 7, '다시 돌아오셨군요!', '30일 만에 방문해 주셨네요. 환영합니다! 그동안 새로워진 점들을 확인해 보세요.', '새로운 소식 보기', '{"trigger": "visit_start", "condition": "days_absence >= 30"}', '{"theme": "welcome", "color": "#06b6d4"}', 15000, 11, 10, 0);

-- 디자인 스토어 기반 템플릿 (11-17)
INSERT INTO {$wpdb->prefix}nf_message_templates
(template_key, template_name, template_name_en, category, design_store_id, title, message, cta_text, trigger_config, style_config, price_krw, price_usd, price_eur, is_premium) VALUES
('design_kakao_channel', '카카오톡 채널 추가', 'KakaoTalk Channel Add', 'design', 2, '카톡 친구가 되어주세요!', '카카오톡 채널을 추가하시면 실시간 혜택과 이벤트 소식을 받아보실 수 있어요!', '채널 추가하기', '{"trigger": "scroll_depth", "value": 50}', '{"design_type": "round_floating", "position": "bottom-right"}', 15000, 11, 10, 0),
('design_phone_call', '전화 상담 연결', 'Phone Call Connect', 'design', 4, '궁금하신 점이 있으신가요?', '전문 상담원이 친절하게 안내해 드립니다. 지금 바로 무료 상담 전화 주세요!', '전화하기', '{"trigger": "time_on_page", "value": 60000}', '{"design_type": "round_floating", "position": "bottom-right"}', 15000, 11, 10, 0),
('design_rich_video', '리치 동영상 팝업', 'Rich Video Popup', 'design', 6, '특별 영상을 확인하세요', '지금 진행 중인 이벤트의 특별 영상입니다. 놓치지 마세요!', '영상 보기', '{"trigger": "visit_start", "delay": 5000}', '{"design_type": "rich_popup", "auto_play": true}', 35000, 25, 23, 1),
('design_coupon_box', '쿠폰 박스', 'Coupon Box', 'design', 9, '특별 할인 쿠폰', '지금 바로 사용 가능한 할인 쿠폰을 받아가세요!', '쿠폰 받기', '{"trigger": "exit_intent"}', '{"design_type": "coupon_box", "coupon_code": "WELCOME10"}', 19000, 14, 13, 0),
('design_welcome_bar', '상단 웰컴바', 'Top Welcome Bar', 'design', 10, '오늘의 특별 공지', '전 상품 무료배송 이벤트 진행 중! 놓치지 마세요.', '자세히', '{"trigger": "visit_start", "delay": 0}', '{"design_type": "welcome_bar", "position": "top"}', 15000, 11, 10, 0),
('design_product_recommend_pc', 'PC 상품 추천', 'PC Product Recommend', 'design', 11, '고객님을 위한 추천 상품', '최근 관심을 보이신 상품과 유사한 인기 상품을 추천해 드립니다.', '상품 보기', '{"trigger": "scroll_depth", "value": 70}', '{"design_type": "product_recommend", "device": "desktop"}', 29000, 21, 19, 1),
('design_product_recommend_mobile', '모바일 상품 추천', 'Mobile Product Recommend', 'design', 12, '추천 상품을 확인하세요', '모바일 전용 추천 상품입니다. 스와이프해서 확인해 보세요!', '확인하기', '{"trigger": "scroll_depth", "value": 50}', '{"design_type": "product_recommend", "device": "mobile"}', 29000, 21, 19, 1);

-- 이커머스 기반 템플릿 (18-22)
INSERT INTO {$wpdb->prefix}nf_message_templates
(template_key, template_name, template_name_en, category, segment_store_id, title, message, cta_text, trigger_config, style_config, price_krw, price_usd, price_eur, is_premium) VALUES
('ecommerce_cart_abandon', '장바구니 이탈 방지', 'Cart Abandonment Prevention', 'ecommerce', 46, '장바구니에 담긴 상품이 있어요!', '결제를 완료하지 않으셨네요. 지금 구매하시면 추가 할인을 드려요!', '결제하기', '{"trigger": "exit_intent", "condition": "cart_count > 0"}', '{"theme": "cart", "color": "#f97316"}', 25000, 18, 17, 1),
('ecommerce_wishlist_promo', '위시리스트 프로모션', 'Wishlist Promotion', 'ecommerce', 44, '찜한 상품이 할인 중!', '위시리스트에 담아두신 상품이 특가 할인 중입니다. 지금 구매하세요!', '할인 상품 보기', '{"trigger": "visit_start", "condition": "wishlist_count > 0"}', '{"theme": "promo", "color": "#ec4899"}', 19000, 14, 13, 0),
('ecommerce_repeat_buyer', '재구매 고객 감사', 'Repeat Buyer Thanks', 'ecommerce', 48, '다시 찾아주셔서 감사해요!', '재구매 고객님께 드리는 특별 적립금! 다음 구매 시 바로 사용하세요.', '적립금 확인', '{"trigger": "visit_start", "condition": "purchase_count >= 2"}', '{"theme": "loyalty", "color": "#22c55e"}', 15000, 11, 10, 0),
('ecommerce_high_value', '고가치 고객 VIP', 'High Value Customer VIP', 'ecommerce', 50, 'VIP 고객님 전용 혜택', '누적 구매금액 상위 고객님께 드리는 프리미엄 혜택입니다.', 'VIP 혜택 보기', '{"trigger": "visit_start", "condition": "total_purchase >= 500000"}', '{"theme": "vip", "color": "#fbbf24"}', 35000, 25, 23, 1);


-- ============================================
-- 6. RFM 분석 알고리즘 테이블
-- RFM 점수 계산 및 세그먼트 매핑
-- ============================================

-- 6.1 RFM 설정 테이블
DROP TABLE IF EXISTS {$wpdb->prefix}nf_rfm_settings;
CREATE TABLE {$wpdb->prefix}nf_rfm_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    description VARCHAR(200),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='RFM 분석 설정';

INSERT INTO {$wpdb->prefix}nf_rfm_settings (setting_key, setting_value, description) VALUES
-- Recency 점수 기준 (일 기준)
('r_score_5', '7', 'R=5: 최근 7일 이내 구매'),
('r_score_4', '30', 'R=4: 최근 30일 이내 구매'),
('r_score_3', '60', 'R=3: 최근 60일 이내 구매'),
('r_score_2', '90', 'R=2: 최근 90일 이내 구매'),
('r_score_1', '999', 'R=1: 90일 이상 미구매'),

-- Frequency 점수 기준 (구매 횟수)
('f_score_5', '10', 'F=5: 10회 이상 구매'),
('f_score_4', '6', 'F=4: 6-9회 구매'),
('f_score_3', '3', 'F=3: 3-5회 구매'),
('f_score_2', '2', 'F=2: 2회 구매'),
('f_score_1', '1', 'F=1: 1회 구매'),

-- Monetary 점수 기준 (누적 금액, 원화)
('m_score_5', '500000', 'M=5: 50만원 이상'),
('m_score_4', '300000', 'M=4: 30-50만원'),
('m_score_3', '150000', 'M=3: 15-30만원'),
('m_score_2', '50000', 'M=2: 5-15만원'),
('m_score_1', '0', 'M=1: 5만원 미만'),

-- 분석 주기 설정
('analysis_period_days', '365', '분석 대상 기간 (일)'),
('auto_update_interval', '24', '자동 업데이트 주기 (시간)');


-- 6.2 고객 RFM 점수 테이블
DROP TABLE IF EXISTS {$wpdb->prefix}nf_customer_rfm;
CREATE TABLE {$wpdb->prefix}nf_customer_rfm (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL COMMENT 'WooCommerce 고객 ID',
    user_id BIGINT UNSIGNED COMMENT 'WordPress 사용자 ID (비회원은 NULL)',
    email VARCHAR(100) COMMENT '이메일',

    -- Raw RFM 데이터
    last_purchase_date DATE COMMENT '마지막 구매일',
    days_since_purchase INT UNSIGNED DEFAULT 0 COMMENT '구매 후 경과일',
    total_orders INT UNSIGNED DEFAULT 0 COMMENT '총 주문 횟수',
    total_amount DECIMAL(12,2) DEFAULT 0 COMMENT '총 구매 금액',
    avg_order_value DECIMAL(10,2) DEFAULT 0 COMMENT '평균 주문 금액',

    -- RFM 점수 (1-5)
    r_score TINYINT UNSIGNED DEFAULT 1 COMMENT 'Recency 점수',
    f_score TINYINT UNSIGNED DEFAULT 1 COMMENT 'Frequency 점수',
    m_score TINYINT UNSIGNED DEFAULT 1 COMMENT 'Monetary 점수',
    rfm_score VARCHAR(5) COMMENT 'RFM 조합 점수 (예: 5-5-5)',
    rfm_total TINYINT UNSIGNED COMMENT 'RFM 합계 점수 (3-15)',

    -- 세그먼트 매핑
    segment_id INT UNSIGNED COMMENT '매핑된 RFM 세그먼트 ID',
    segment_key VARCHAR(50) COMMENT '세그먼트 키',

    -- 추가 분석 데이터
    first_purchase_date DATE COMMENT '첫 구매일',
    customer_lifetime_days INT UNSIGNED COMMENT '고객 수명 (일)',
    purchase_frequency DECIMAL(5,2) COMMENT '구매 빈도 (월 평균)',

    -- 메타 정보
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '계산 시점',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_customer (customer_id),
    INDEX idx_user (user_id),
    INDEX idx_email (email),
    INDEX idx_segment (segment_id),
    INDEX idx_rfm_score (r_score, f_score, m_score),
    INDEX idx_calculated (calculated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='고객 RFM 점수';


-- 6.3 RFM 세그먼트 매핑 규칙 테이블
DROP TABLE IF EXISTS {$wpdb->prefix}nf_rfm_mapping_rules;
CREATE TABLE {$wpdb->prefix}nf_rfm_mapping_rules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rule_name VARCHAR(100) NOT NULL COMMENT '규칙명',
    segment_id INT UNSIGNED NOT NULL COMMENT '매핑될 세그먼트 ID',
    r_min TINYINT UNSIGNED NOT NULL DEFAULT 1,
    r_max TINYINT UNSIGNED NOT NULL DEFAULT 5,
    f_min TINYINT UNSIGNED NOT NULL DEFAULT 1,
    f_max TINYINT UNSIGNED NOT NULL DEFAULT 5,
    m_condition ENUM('any', 'low', 'medium', 'high') DEFAULT 'any' COMMENT 'M 조건',
    priority INT UNSIGNED DEFAULT 10 COMMENT '우선순위 (낮을수록 먼저)',
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_segment (segment_id),
    INDEX idx_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='RFM 세그먼트 매핑 규칙';

INSERT INTO {$wpdb->prefix}nf_rfm_mapping_rules
(rule_name, segment_id, r_min, r_max, f_min, f_max, m_condition, priority) VALUES
('다이아몬드', 1, 5, 5, 5, 5, 'high', 1),
('충성 고객', 2, 4, 5, 4, 5, 'high', 2),
('절대 놓치지 마세요', 3, 1, 2, 4, 5, 'high', 3),
('잠재적인 충성 고객', 4, 4, 5, 2, 3, 'medium', 4),
('유망주', 5, 3, 4, 2, 3, 'medium', 5),
('갈림길 고객', 6, 2, 3, 3, 4, 'medium', 6),
('위기 관리 필요', 7, 1, 2, 2, 3, 'medium', 7),
('신규 고객', 8, 5, 5, 1, 1, 'low', 8),
('1회성 고객', 9, 2, 3, 1, 1, 'low', 9),
('겨울잠 고객', 10, 1, 1, 1, 2, 'low', 10),
('리마인드가 필요해요', 11, 2, 2, 1, 2, 'low', 11);


-- ============================================
-- 7. 저장 프로시저: RFM 점수 계산
-- ============================================

DELIMITER //

-- 7.1 개별 고객 RFM 점수 계산
DROP PROCEDURE IF EXISTS {$wpdb->prefix}nf_calculate_customer_rfm//
CREATE PROCEDURE {$wpdb->prefix}nf_calculate_customer_rfm(
    IN p_customer_id BIGINT UNSIGNED
)
BEGIN
    DECLARE v_last_purchase DATE;
    DECLARE v_days_since INT;
    DECLARE v_total_orders INT;
    DECLARE v_total_amount DECIMAL(12,2);
    DECLARE v_r_score TINYINT;
    DECLARE v_f_score TINYINT;
    DECLARE v_m_score TINYINT;
    DECLARE v_segment_id INT;

    -- RFM 설정값 가져오기
    DECLARE v_r5 INT DEFAULT 7;
    DECLARE v_r4 INT DEFAULT 30;
    DECLARE v_r3 INT DEFAULT 60;
    DECLARE v_r2 INT DEFAULT 90;
    DECLARE v_f5 INT DEFAULT 10;
    DECLARE v_f4 INT DEFAULT 6;
    DECLARE v_f3 INT DEFAULT 3;
    DECLARE v_f2 INT DEFAULT 2;
    DECLARE v_m5 INT DEFAULT 500000;
    DECLARE v_m4 INT DEFAULT 300000;
    DECLARE v_m3 INT DEFAULT 150000;
    DECLARE v_m2 INT DEFAULT 50000;

    -- 고객의 구매 데이터 집계 (WooCommerce 테이블에서)
    SELECT
        MAX(DATE(post_date)),
        COUNT(*),
        COALESCE(SUM(meta_total.meta_value), 0)
    INTO v_last_purchase, v_total_orders, v_total_amount
    FROM {$wpdb->prefix}posts orders
    LEFT JOIN {$wpdb->prefix}postmeta meta_customer ON orders.ID = meta_customer.post_id AND meta_customer.meta_key = '_customer_user'
    LEFT JOIN {$wpdb->prefix}postmeta meta_total ON orders.ID = meta_total.post_id AND meta_total.meta_key = '_order_total'
    WHERE orders.post_type = 'shop_order'
    AND orders.post_status IN ('wc-completed', 'wc-processing')
    AND meta_customer.meta_value = p_customer_id;

    -- 구매 후 경과일 계산
    SET v_days_since = DATEDIFF(CURDATE(), COALESCE(v_last_purchase, '2000-01-01'));

    -- R 점수 계산
    SET v_r_score = CASE
        WHEN v_days_since <= v_r5 THEN 5
        WHEN v_days_since <= v_r4 THEN 4
        WHEN v_days_since <= v_r3 THEN 3
        WHEN v_days_since <= v_r2 THEN 2
        ELSE 1
    END;

    -- F 점수 계산
    SET v_f_score = CASE
        WHEN v_total_orders >= v_f5 THEN 5
        WHEN v_total_orders >= v_f4 THEN 4
        WHEN v_total_orders >= v_f3 THEN 3
        WHEN v_total_orders >= v_f2 THEN 2
        ELSE 1
    END;

    -- M 점수 계산
    SET v_m_score = CASE
        WHEN v_total_amount >= v_m5 THEN 5
        WHEN v_total_amount >= v_m4 THEN 4
        WHEN v_total_amount >= v_m3 THEN 3
        WHEN v_total_amount >= v_m2 THEN 2
        ELSE 1
    END;

    -- 세그먼트 매핑 (우선순위 기반)
    SELECT segment_id INTO v_segment_id
    FROM {$wpdb->prefix}nf_rfm_mapping_rules
    WHERE is_active = 1
    AND v_r_score BETWEEN r_min AND r_max
    AND v_f_score BETWEEN f_min AND f_max
    AND (
        m_condition = 'any'
        OR (m_condition = 'high' AND v_m_score >= 4)
        OR (m_condition = 'medium' AND v_m_score BETWEEN 2 AND 4)
        OR (m_condition = 'low' AND v_m_score <= 2)
    )
    ORDER BY priority ASC
    LIMIT 1;

    -- 결과 저장
    INSERT INTO {$wpdb->prefix}nf_customer_rfm (
        customer_id, last_purchase_date, days_since_purchase,
        total_orders, total_amount, avg_order_value,
        r_score, f_score, m_score,
        rfm_score, rfm_total, segment_id
    ) VALUES (
        p_customer_id, v_last_purchase, v_days_since,
        v_total_orders, v_total_amount,
        IF(v_total_orders > 0, v_total_amount / v_total_orders, 0),
        v_r_score, v_f_score, v_m_score,
        CONCAT(v_r_score, '-', v_f_score, '-', v_m_score),
        v_r_score + v_f_score + v_m_score,
        v_segment_id
    )
    ON DUPLICATE KEY UPDATE
        last_purchase_date = v_last_purchase,
        days_since_purchase = v_days_since,
        total_orders = v_total_orders,
        total_amount = v_total_amount,
        avg_order_value = IF(v_total_orders > 0, v_total_amount / v_total_orders, 0),
        r_score = v_r_score,
        f_score = v_f_score,
        m_score = v_m_score,
        rfm_score = CONCAT(v_r_score, '-', v_f_score, '-', v_m_score),
        rfm_total = v_r_score + v_f_score + v_m_score,
        segment_id = v_segment_id,
        updated_at = CURRENT_TIMESTAMP;
END//


-- 7.2 전체 고객 RFM 일괄 계산
DROP PROCEDURE IF EXISTS {$wpdb->prefix}nf_calculate_all_rfm//
CREATE PROCEDURE {$wpdb->prefix}nf_calculate_all_rfm()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_customer_id BIGINT;
    DECLARE cur_customers CURSOR FOR
        SELECT DISTINCT meta_value
        FROM {$wpdb->prefix}postmeta
        WHERE meta_key = '_customer_user'
        AND meta_value > 0;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur_customers;

    read_loop: LOOP
        FETCH cur_customers INTO v_customer_id;
        IF done THEN
            LEAVE read_loop;
        END IF;

        CALL {$wpdb->prefix}nf_calculate_customer_rfm(v_customer_id);
    END LOOP;

    CLOSE cur_customers;

    -- 결과 요약 반환
    SELECT
        s.segment_name,
        COUNT(*) as customer_count,
        AVG(c.total_amount) as avg_total_amount,
        AVG(c.total_orders) as avg_orders
    FROM {$wpdb->prefix}nf_customer_rfm c
    LEFT JOIN {$wpdb->prefix}nf_rfm_segments s ON c.segment_id = s.id
    GROUP BY c.segment_id, s.segment_name
    ORDER BY s.priority_rank;
END//

DELIMITER ;


-- ============================================
-- 8. 뷰: RFM 분석 대시보드용
-- ============================================

-- 8.1 고객 RFM 상세 뷰
DROP VIEW IF EXISTS {$wpdb->prefix}nf_view_customer_rfm_detail;
CREATE VIEW {$wpdb->prefix}nf_view_customer_rfm_detail AS
SELECT
    c.customer_id,
    c.user_id,
    c.email,
    c.last_purchase_date,
    c.days_since_purchase,
    c.total_orders,
    c.total_amount,
    c.avg_order_value,
    c.r_score,
    c.f_score,
    c.m_score,
    c.rfm_score,
    c.rfm_total,
    s.segment_key,
    s.segment_name,
    s.segment_name_en,
    s.marketing_action,
    s.color_code,
    s.icon,
    c.calculated_at
FROM {$wpdb->prefix}nf_customer_rfm c
LEFT JOIN {$wpdb->prefix}nf_rfm_segments s ON c.segment_id = s.id;


-- 8.2 세그먼트별 통계 뷰
DROP VIEW IF EXISTS {$wpdb->prefix}nf_view_segment_stats;
CREATE VIEW {$wpdb->prefix}nf_view_segment_stats AS
SELECT
    s.id as segment_id,
    s.segment_key,
    s.segment_name,
    s.segment_name_en,
    s.priority_rank,
    s.color_code,
    COUNT(c.id) as customer_count,
    COALESCE(SUM(c.total_amount), 0) as total_revenue,
    COALESCE(AVG(c.total_amount), 0) as avg_customer_value,
    COALESCE(AVG(c.total_orders), 0) as avg_orders,
    COALESCE(AVG(c.days_since_purchase), 0) as avg_days_since_purchase
FROM {$wpdb->prefix}nf_rfm_segments s
LEFT JOIN {$wpdb->prefix}nf_customer_rfm c ON s.id = c.segment_id
GROUP BY s.id, s.segment_key, s.segment_name, s.segment_name_en, s.priority_rank, s.color_code
ORDER BY s.priority_rank;


-- ============================================
-- 9. 인덱스 최적화
-- ============================================

-- 복합 인덱스 추가 (성능 최적화)
ALTER TABLE {$wpdb->prefix}nf_customer_rfm
    ADD INDEX idx_rfm_composite (segment_id, r_score, f_score, m_score),
    ADD INDEX idx_purchase_analysis (last_purchase_date, total_amount, total_orders);

ALTER TABLE {$wpdb->prefix}nf_message_templates
    ADD INDEX idx_template_lookup (category, is_active, is_premium);

ALTER TABLE {$wpdb->prefix}nf_segment_store
    ADD INDEX idx_segment_lookup (trigger_type, is_active, complexity);


-- ============================================
-- 완료 메시지
-- ============================================
-- 데이터베이스 설치 완료!
--
-- 설치된 테이블:
-- 1. nf_rfm_segments - RFM 세그먼트 정의 (11개)
-- 2. nf_segment_store - 방문자 세그먼트 (50개)
-- 3. nf_customer_filters - 고객 필터 (101개)
-- 4. nf_design_store - 디자인 템플릿 (14개)
-- 5. nf_message_templates - 메시지 템플릿 (22개)
-- 6. nf_rfm_settings - RFM 설정
-- 7. nf_customer_rfm - 고객 RFM 점수
-- 8. nf_rfm_mapping_rules - RFM 매핑 규칙
--
-- 사용 방법:
-- CALL wp_nf_calculate_all_rfm(); -- 전체 고객 RFM 계산
-- CALL wp_nf_calculate_customer_rfm(123); -- 특정 고객 RFM 계산
-- SELECT * FROM wp_nf_view_customer_rfm_detail; -- 고객별 상세 조회
-- SELECT * FROM wp_nf_view_segment_stats; -- 세그먼트별 통계

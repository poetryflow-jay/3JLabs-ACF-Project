<?php
/**
 * 외부 데이터 소스 통합 클래스
 *
 * WooCommerce, LearnDash, GamiPress, BuddyPress, Ultimate Member,
 * Fluent Community, Monster Insights, Google Analytics, Microsoft Clarity, Hotjar 등
 * 다양한 외부 데이터 소스와의 통합을 관리합니다.
 *
 * @package ACF_Nudge_Flow
 * @since 22.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Nudge_Flow_Data_Source_Integration {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 등록된 데이터 소스 핸들러
     */
    private $handlers = array();

    /**
     * 활성화된 데이터 소스 목록
     */
    private $active_sources = array();

    /**
     * 캐시된 통합 프로필
     */
    private $profile_cache = array();

    /**
     * 캐시 TTL (초)
     */
    private $cache_ttl = 300; // 5분

    /**
     * 데이터 소스 정의
     */
    private $data_source_definitions = array(
        // 이커머스
        'woocommerce' => array(
            'name' => 'WooCommerce',
            'category' => 'ecommerce',
            'class_check' => 'WooCommerce',
            'priority' => 100,
            'provides' => array( 'orders', 'revenue', 'products', 'cart', 'wishlist' ),
        ),

        // LMS 플랫폼
        'learndash' => array(
            'name' => 'LearnDash',
            'category' => 'lms',
            'class_check' => 'SFWD_LMS',
            'priority' => 90,
            'provides' => array( 'courses', 'lessons', 'quizzes', 'certificates', 'progress' ),
        ),
        'tutor_lms' => array(
            'name' => 'Tutor LMS',
            'category' => 'lms',
            'class_check' => 'TUTOR\\Tutor',
            'priority' => 85,
            'provides' => array( 'courses', 'lessons', 'quizzes', 'progress' ),
        ),
        'lifter_lms' => array(
            'name' => 'LifterLMS',
            'category' => 'lms',
            'class_check' => 'LifterLMS',
            'priority' => 85,
            'provides' => array( 'courses', 'memberships', 'achievements', 'progress' ),
        ),

        // 멤버십
        'ultimate_member' => array(
            'name' => 'Ultimate Member',
            'category' => 'membership',
            'function_check' => 'UM',
            'priority' => 80,
            'provides' => array( 'profile', 'roles', 'activity', 'followers' ),
        ),
        'pmpro' => array(
            'name' => 'Paid Memberships Pro',
            'category' => 'membership',
            'function_check' => 'pmpro_hasMembershipLevel',
            'priority' => 80,
            'provides' => array( 'membership_level', 'subscription', 'history' ),
        ),
        'memberpress' => array(
            'name' => 'MemberPress',
            'category' => 'membership',
            'class_check' => 'MeprCtrlFactory',
            'priority' => 80,
            'provides' => array( 'memberships', 'subscriptions', 'transactions' ),
        ),
        'wc_memberships' => array(
            'name' => 'WooCommerce Memberships',
            'category' => 'membership',
            'function_check' => 'wc_memberships',
            'priority' => 75,
            'provides' => array( 'membership_plans', 'access_rules' ),
        ),
        'advanced_member_acf' => array(
            'name' => 'Advanced Member for ACF',
            'category' => 'membership',
            'class_check' => 'ACF_Advanced_Member',
            'priority' => 75,
            'provides' => array( 'custom_fields', 'profile_data' ),
        ),

        // 커뮤니티
        'buddypress' => array(
            'name' => 'BuddyPress',
            'category' => 'community',
            'class_check' => 'BuddyPress',
            'priority' => 70,
            'provides' => array( 'activity', 'groups', 'friends', 'messages', 'notifications' ),
        ),
        'buddyboss' => array(
            'name' => 'BuddyBoss',
            'category' => 'community',
            'class_check' => 'BuddyBoss_Platform',
            'priority' => 70,
            'provides' => array( 'activity', 'groups', 'forums', 'media', 'notifications' ),
        ),
        'fluent_community' => array(
            'name' => 'Fluent Community',
            'category' => 'community',
            'class_check' => 'FluentCommunity\\App\\App',
            'priority' => 65,
            'provides' => array( 'posts', 'comments', 'reactions', 'follows' ),
        ),
        'bbpress' => array(
            'name' => 'bbPress',
            'category' => 'community',
            'class_check' => 'bbPress',
            'priority' => 60,
            'provides' => array( 'forums', 'topics', 'replies' ),
        ),

        // 게이미피케이션
        'gamipress' => array(
            'name' => 'GamiPress',
            'category' => 'gamification',
            'class_check' => 'GamiPress',
            'priority' => 70,
            'provides' => array( 'points', 'achievements', 'ranks', 'logs' ),
        ),
        'mycred' => array(
            'name' => 'myCred',
            'category' => 'gamification',
            'function_check' => 'mycred',
            'priority' => 65,
            'provides' => array( 'points', 'badges', 'ranks', 'logs' ),
        ),

        // 애널리틱스
        'monster_insights' => array(
            'name' => 'Monster Insights',
            'category' => 'analytics',
            'class_check' => 'MonsterInsights',
            'priority' => 50,
            'provides' => array( 'pageviews', 'sessions', 'events' ),
        ),
        'google_analytics' => array(
            'name' => 'Google Analytics (GA4)',
            'category' => 'analytics',
            'option_check' => 'acf_nudge_ga4_api_key',
            'priority' => 50,
            'provides' => array( 'pageviews', 'sessions', 'events', 'conversions' ),
        ),
        'ms_clarity' => array(
            'name' => 'Microsoft Clarity',
            'category' => 'analytics',
            'option_check' => 'acf_nudge_clarity_project_id',
            'priority' => 45,
            'provides' => array( 'heatmaps', 'recordings', 'insights' ),
        ),
        'hotjar' => array(
            'name' => 'Hotjar',
            'category' => 'analytics',
            'option_check' => 'acf_nudge_hotjar_site_id',
            'priority' => 45,
            'provides' => array( 'heatmaps', 'recordings', 'surveys', 'feedback' ),
        ),

        // 런닝/피트니스
        'rundash' => array(
            'name' => 'RunDash',
            'category' => 'fitness',
            'class_check' => 'RunDash_Core',
            'priority' => 60,
            'provides' => array( 'runs', 'distance', 'achievements', 'challenges' ),
        ),

        // 이메일 마케팅
        'fluentcrm' => array(
            'name' => 'FluentCRM',
            'category' => 'email',
            'class_check' => 'FluentCrm\\App\\App',
            'priority' => 55,
            'provides' => array( 'contacts', 'tags', 'lists', 'campaigns', 'sequences' ),
        ),
        'mailchimp' => array(
            'name' => 'Mailchimp for WP',
            'category' => 'email',
            'class_check' => 'MC4WP',
            'priority' => 50,
            'provides' => array( 'lists', 'segments', 'campaigns' ),
        ),

        // 폼 빌더
        'fluentforms' => array(
            'name' => 'Fluent Forms',
            'category' => 'forms',
            'class_check' => 'FluentForm\\App\\App',
            'priority' => 40,
            'provides' => array( 'submissions', 'entries', 'conversions' ),
        ),
        'gravityforms' => array(
            'name' => 'Gravity Forms',
            'category' => 'forms',
            'class_check' => 'GFFormsModel',
            'priority' => 40,
            'provides' => array( 'entries', 'forms', 'fields' ),
        ),
    );

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 생성자
     */
    private function __construct() {
        $this->detect_active_sources();
        $this->register_default_handlers();
        $this->init_hooks();
    }

    /**
     * 활성화된 데이터 소스 감지
     */
    private function detect_active_sources() {
        foreach ( $this->data_source_definitions as $source_id => $definition ) {
            $is_active = false;

            // 클래스 존재 확인
            if ( ! empty( $definition['class_check'] ) ) {
                $is_active = class_exists( $definition['class_check'] );
            }
            // 함수 존재 확인
            elseif ( ! empty( $definition['function_check'] ) ) {
                $is_active = function_exists( $definition['function_check'] );
            }
            // 옵션 값 확인
            elseif ( ! empty( $definition['option_check'] ) ) {
                $is_active = ! empty( get_option( $definition['option_check'] ) );
            }

            if ( $is_active ) {
                $this->active_sources[ $source_id ] = $definition;
            }
        }

        // 우선순위별 정렬
        uasort( $this->active_sources, function( $a, $b ) {
            return $b['priority'] - $a['priority'];
        });
    }

    /**
     * 기본 핸들러 등록
     */
    private function register_default_handlers() {
        // WooCommerce 핸들러
        $this->register_handler( 'woocommerce', array( $this, 'handle_woocommerce_data' ) );

        // LearnDash 핸들러
        $this->register_handler( 'learndash', array( $this, 'handle_learndash_data' ) );

        // GamiPress 핸들러
        $this->register_handler( 'gamipress', array( $this, 'handle_gamipress_data' ) );

        // BuddyPress 핸들러
        $this->register_handler( 'buddypress', array( $this, 'handle_buddypress_data' ) );

        // Ultimate Member 핸들러
        $this->register_handler( 'ultimate_member', array( $this, 'handle_ultimate_member_data' ) );

        // Fluent Community 핸들러
        $this->register_handler( 'fluent_community', array( $this, 'handle_fluent_community_data' ) );

        // RunDash 핸들러
        $this->register_handler( 'rundash', array( $this, 'handle_rundash_data' ) );

        // FluentCRM 핸들러
        $this->register_handler( 'fluentcrm', array( $this, 'handle_fluentcrm_data' ) );

        // Monster Insights 핸들러
        $this->register_handler( 'monster_insights', array( $this, 'handle_monster_insights_data' ) );

        // 확장 가능한 핸들러 등록 허용
        do_action( 'acf_nudge_flow_register_data_handlers', $this );
    }

    /**
     * 데이터 핸들러 등록
     *
     * @param string $source_id 데이터 소스 ID
     * @param callable $handler 핸들러 콜백
     */
    public function register_handler( $source_id, $handler ) {
        if ( is_callable( $handler ) ) {
            $this->handlers[ $source_id ] = $handler;
        }
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // WooCommerce 이벤트 훅
        if ( $this->is_source_active( 'woocommerce' ) ) {
            add_action( 'woocommerce_order_status_completed', array( $this, 'on_woo_order_completed' ), 10, 1 );
            add_action( 'woocommerce_order_status_processing', array( $this, 'on_woo_order_processing' ), 10, 1 );
            add_action( 'woocommerce_add_to_cart', array( $this, 'on_woo_add_to_cart' ), 10, 6 );
            add_action( 'woocommerce_cart_item_removed', array( $this, 'on_woo_cart_item_removed' ), 10, 2 );
        }

        // LearnDash 이벤트 훅
        if ( $this->is_source_active( 'learndash' ) ) {
            add_action( 'learndash_course_completed', array( $this, 'on_ld_course_completed' ), 10, 1 );
            add_action( 'learndash_lesson_completed', array( $this, 'on_ld_lesson_completed' ), 10, 1 );
            add_action( 'learndash_quiz_completed', array( $this, 'on_ld_quiz_completed' ), 10, 2 );
        }

        // GamiPress 이벤트 훅
        if ( $this->is_source_active( 'gamipress' ) ) {
            add_action( 'gamipress_award_points', array( $this, 'on_gamipress_points_awarded' ), 10, 4 );
            add_action( 'gamipress_award_achievement', array( $this, 'on_gamipress_achievement_awarded' ), 10, 5 );
        }

        // BuddyPress 이벤트 훅
        if ( $this->is_source_active( 'buddypress' ) ) {
            add_action( 'bp_activity_posted_update', array( $this, 'on_bp_activity_posted' ), 10, 3 );
            add_action( 'friends_friendship_accepted', array( $this, 'on_bp_friendship_accepted' ), 10, 4 );
        }

        // AJAX 엔드포인트
        add_action( 'wp_ajax_acf_nf_get_unified_profile', array( $this, 'ajax_get_unified_profile' ) );
        add_action( 'wp_ajax_acf_nf_sync_data_source', array( $this, 'ajax_sync_data_source' ) );
        add_action( 'wp_ajax_acf_nf_get_active_sources', array( $this, 'ajax_get_active_sources' ) );

        // REST API 엔드포인트
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

        // 캐시 정리 스케줄
        add_action( 'acf_nudge_flow_cleanup_cache', array( $this, 'cleanup_expired_cache' ) );
        if ( ! wp_next_scheduled( 'acf_nudge_flow_cleanup_cache' ) ) {
            wp_schedule_event( time(), 'hourly', 'acf_nudge_flow_cleanup_cache' );
        }
    }

    /**
     * REST API 라우트 등록
     */
    public function register_rest_routes() {
        register_rest_route( 'acf-nudge-flow/v1', '/profile/(?P<user_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_user_profile' ),
            'permission_callback' => array( $this, 'rest_permission_check' ),
            'args' => array(
                'user_id' => array(
                    'required' => true,
                    'type' => 'integer',
                ),
            ),
        ));

        register_rest_route( 'acf-nudge-flow/v1', '/sources', array(
            'methods' => 'GET',
            'callback' => array( $this, 'rest_get_active_sources' ),
            'permission_callback' => array( $this, 'rest_permission_check' ),
        ));

        register_rest_route( 'acf-nudge-flow/v1', '/sync', array(
            'methods' => 'POST',
            'callback' => array( $this, 'rest_sync_user_data' ),
            'permission_callback' => array( $this, 'rest_permission_check' ),
        ));
    }

    /**
     * REST API 권한 확인
     */
    public function rest_permission_check( $request ) {
        return current_user_can( 'manage_options' );
    }

    /**
     * 데이터 소스 활성화 여부 확인
     *
     * @param string $source_id 데이터 소스 ID
     * @return bool
     */
    public function is_source_active( $source_id ) {
        return isset( $this->active_sources[ $source_id ] );
    }

    /**
     * 활성화된 모든 데이터 소스 반환
     *
     * @return array
     */
    public function get_active_sources() {
        return $this->active_sources;
    }

    /**
     * 통합 사용자 프로필 조회
     *
     * @param int $user_id 사용자 ID
     * @param bool $force_refresh 강제 새로고침
     * @return array 통합 프로필 데이터
     */
    public function get_unified_profile( $user_id, $force_refresh = false ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( ! $user_id ) {
            return $this->get_empty_profile();
        }

        // 캐시 확인
        $cache_key = 'unified_profile_' . $user_id;
        if ( ! $force_refresh && isset( $this->profile_cache[ $cache_key ] ) ) {
            $cached = $this->profile_cache[ $cache_key ];
            if ( $cached['expires'] > time() ) {
                return $cached['data'];
            }
        }

        // 트랜지언트 캐시 확인
        if ( ! $force_refresh ) {
            $transient = get_transient( 'acf_nf_profile_' . $user_id );
            if ( $transient !== false ) {
                $this->profile_cache[ $cache_key ] = array(
                    'data' => $transient,
                    'expires' => time() + $this->cache_ttl,
                );
                return $transient;
            }
        }

        // 프로필 데이터 수집
        $profile = $this->collect_profile_data( $user_id );

        // 캐시 저장
        $this->profile_cache[ $cache_key ] = array(
            'data' => $profile,
            'expires' => time() + $this->cache_ttl,
        );
        set_transient( 'acf_nf_profile_' . $user_id, $profile, $this->cache_ttl );

        return $profile;
    }

    /**
     * 프로필 데이터 수집
     *
     * @param int $user_id 사용자 ID
     * @return array
     */
    private function collect_profile_data( $user_id ) {
        $profile = $this->get_empty_profile();
        $profile['user_id'] = $user_id;
        $profile['collected_at'] = current_time( 'mysql' );

        // 기본 사용자 정보
        $user = get_userdata( $user_id );
        if ( $user ) {
            $profile['basic'] = array(
                'display_name' => $user->display_name,
                'email' => $user->user_email,
                'registered' => $user->user_registered,
                'roles' => $user->roles,
            );
        }

        // 각 활성화된 데이터 소스에서 데이터 수집
        foreach ( $this->active_sources as $source_id => $definition ) {
            if ( isset( $this->handlers[ $source_id ] ) ) {
                try {
                    $source_data = call_user_func( $this->handlers[ $source_id ], $user_id );
                    if ( ! empty( $source_data ) ) {
                        $profile['sources'][ $source_id ] = $source_data;
                    }
                } catch ( Exception $e ) {
                    $profile['errors'][ $source_id ] = $e->getMessage();
                }
            }
        }

        // 행동 점수 계산
        $profile['scores'] = $this->calculate_behavioral_scores( $profile );

        // 세그먼트 추론
        $profile['inferred_segments'] = $this->infer_segments( $profile );

        return apply_filters( 'acf_nudge_flow_unified_profile', $profile, $user_id );
    }

    /**
     * 빈 프로필 구조 반환
     *
     * @return array
     */
    private function get_empty_profile() {
        return array(
            'user_id' => 0,
            'collected_at' => null,
            'basic' => array(),
            'sources' => array(),
            'scores' => array(
                'engagement' => 0,
                'loyalty' => 0,
                'value' => 0,
                'churn_risk' => 0,
                'purchase_intent' => 0,
            ),
            'inferred_segments' => array(),
            'errors' => array(),
        );
    }

    // ==========================================
    // 데이터 소스 핸들러
    // ==========================================

    /**
     * WooCommerce 데이터 핸들러
     *
     * @param int $user_id 사용자 ID
     * @return array
     */
    public function handle_woocommerce_data( $user_id ) {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return array();
        }

        global $wpdb;

        // 주문 통계
        $order_stats = $wpdb->get_row( $wpdb->prepare(
            "SELECT
                COUNT(DISTINCT p.ID) as total_orders,
                SUM(CAST(pm_total.meta_value AS DECIMAL(15,2))) as total_spent,
                AVG(CAST(pm_total.meta_value AS DECIMAL(15,2))) as avg_order_value,
                MAX(p.post_date) as last_order_date,
                MIN(p.post_date) as first_order_date
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm_user ON p.ID = pm_user.post_id
             INNER JOIN {$wpdb->postmeta} pm_total ON p.ID = pm_total.post_id
             WHERE p.post_type = 'shop_order'
             AND p.post_status IN ('wc-completed', 'wc-processing')
             AND pm_user.meta_key = '_customer_user'
             AND pm_user.meta_value = %d
             AND pm_total.meta_key = '_order_total'",
            $user_id
        ) );

        // 경과일 계산
        $days_since_last_order = null;
        if ( $order_stats && $order_stats->last_order_date ) {
            $days_since_last_order = floor( ( time() - strtotime( $order_stats->last_order_date ) ) / DAY_IN_SECONDS );
        }

        // 장바구니 정보
        $cart_data = array();
        if ( function_exists( 'WC' ) && WC()->cart ) {
            $cart = WC()->cart;
            $cart_data = array(
                'item_count' => $cart->get_cart_contents_count(),
                'total' => $cart->get_cart_contents_total(),
            );
        }

        // 위시리스트 (YITH WooCommerce Wishlist 지원)
        $wishlist_count = 0;
        if ( function_exists( 'YITH_WCWL' ) ) {
            $wishlist_count = YITH_WCWL()->count_products( $user_id );
        }

        // 최근 본 상품
        $recently_viewed = array();
        $viewed_products = get_user_meta( $user_id, '_woocommerce_recently_viewed', true );
        if ( ! empty( $viewed_products ) ) {
            $recently_viewed = array_map( 'absint', explode( '|', $viewed_products ) );
        }

        // 구매한 카테고리
        $purchased_categories = $wpdb->get_col( $wpdb->prepare(
            "SELECT DISTINCT t.term_id
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm_user ON p.ID = pm_user.post_id
             INNER JOIN {$wpdb->prefix}woocommerce_order_items oi ON p.ID = oi.order_id
             INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oi.order_item_id = oim.order_item_id
             INNER JOIN {$wpdb->term_relationships} tr ON oim.meta_value = tr.object_id
             INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
             INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
             WHERE p.post_type = 'shop_order'
             AND p.post_status IN ('wc-completed', 'wc-processing')
             AND pm_user.meta_key = '_customer_user'
             AND pm_user.meta_value = %d
             AND oi.order_item_type = 'line_item'
             AND oim.meta_key = '_product_id'
             AND tt.taxonomy = 'product_cat'",
            $user_id
        ) );

        return array(
            'total_orders' => intval( $order_stats->total_orders ?? 0 ),
            'total_spent' => floatval( $order_stats->total_spent ?? 0 ),
            'avg_order_value' => floatval( $order_stats->avg_order_value ?? 0 ),
            'last_order_date' => $order_stats->last_order_date ?? null,
            'first_order_date' => $order_stats->first_order_date ?? null,
            'days_since_last_order' => $days_since_last_order,
            'cart' => $cart_data,
            'wishlist_count' => $wishlist_count,
            'recently_viewed' => $recently_viewed,
            'purchased_categories' => $purchased_categories,
            'is_paying_customer' => ( intval( $order_stats->total_orders ?? 0 ) > 0 ),
        );
    }

    /**
     * LearnDash 데이터 핸들러
     *
     * @param int $user_id 사용자 ID
     * @return array
     */
    public function handle_learndash_data( $user_id ) {
        if ( ! class_exists( 'SFWD_LMS' ) ) {
            return array();
        }

        // 등록된 코스
        $enrolled_courses = learndash_user_get_enrolled_courses( $user_id );

        // 완료한 코스
        $completed_courses = array();
        foreach ( $enrolled_courses as $course_id ) {
            if ( learndash_course_completed( $user_id, $course_id ) ) {
                $completed_courses[] = $course_id;
            }
        }

        // 진행 중인 코스
        $in_progress = array_diff( $enrolled_courses, $completed_courses );

        // 퀴즈 통계
        $quiz_attempts = learndash_get_user_quiz_attempts( $user_id );
        $total_quizzes = count( $quiz_attempts );
        $passed_quizzes = 0;
        $total_score = 0;

        foreach ( $quiz_attempts as $attempt ) {
            if ( isset( $attempt['pass'] ) && $attempt['pass'] ) {
                $passed_quizzes++;
            }
            if ( isset( $attempt['percentage'] ) ) {
                $total_score += $attempt['percentage'];
            }
        }

        $avg_quiz_score = $total_quizzes > 0 ? round( $total_score / $total_quizzes, 2 ) : 0;

        // 인증서
        $certificates = array();
        foreach ( $completed_courses as $course_id ) {
            $cert_link = learndash_get_course_certificate_link( $course_id, $user_id );
            if ( $cert_link ) {
                $certificates[] = array(
                    'course_id' => $course_id,
                    'link' => $cert_link,
                );
            }
        }

        return array(
            'enrolled_courses' => count( $enrolled_courses ),
            'completed_courses' => count( $completed_courses ),
            'in_progress_courses' => count( $in_progress ),
            'completion_rate' => count( $enrolled_courses ) > 0
                ? round( count( $completed_courses ) / count( $enrolled_courses ) * 100, 2 )
                : 0,
            'total_quiz_attempts' => $total_quizzes,
            'passed_quizzes' => $passed_quizzes,
            'avg_quiz_score' => $avg_quiz_score,
            'certificates_count' => count( $certificates ),
            'course_ids' => array(
                'enrolled' => $enrolled_courses,
                'completed' => $completed_courses,
                'in_progress' => array_values( $in_progress ),
            ),
        );
    }

    /**
     * GamiPress 데이터 핸들러
     *
     * @param int $user_id 사용자 ID
     * @return array
     */
    public function handle_gamipress_data( $user_id ) {
        if ( ! class_exists( 'GamiPress' ) ) {
            return array();
        }

        // 포인트 타입별 잔액
        $points = array();
        $points_types = gamipress_get_points_types();
        foreach ( $points_types as $type_slug => $type ) {
            $points[ $type_slug ] = gamipress_get_user_points( $user_id, $type_slug );
        }

        // 총 포인트
        $total_points = array_sum( $points );

        // 획득한 업적
        $achievements = array();
        $achievement_types = gamipress_get_achievement_types();
        foreach ( $achievement_types as $type_slug => $type ) {
            $user_achievements = gamipress_get_user_achievements( array(
                'user_id' => $user_id,
                'achievement_type' => $type_slug,
            ) );
            $achievements[ $type_slug ] = count( $user_achievements );
        }

        // 총 업적
        $total_achievements = array_sum( $achievements );

        // 현재 랭크
        $ranks = array();
        $rank_types = gamipress_get_rank_types();
        foreach ( $rank_types as $type_slug => $type ) {
            $user_rank = gamipress_get_user_rank( $user_id, $type_slug );
            if ( $user_rank ) {
                $ranks[ $type_slug ] = array(
                    'id' => $user_rank->ID,
                    'title' => $user_rank->post_title,
                );
            }
        }

        return array(
            'total_points' => $total_points,
            'points_by_type' => $points,
            'total_achievements' => $total_achievements,
            'achievements_by_type' => $achievements,
            'ranks' => $ranks,
        );
    }

    /**
     * BuddyPress 데이터 핸들러
     *
     * @param int $user_id 사용자 ID
     * @return array
     */
    public function handle_buddypress_data( $user_id ) {
        if ( ! class_exists( 'BuddyPress' ) ) {
            return array();
        }

        // 활동 수
        $activity_count = 0;
        if ( bp_is_active( 'activity' ) ) {
            $activity_count = bp_activity_get_total_activity_count( $user_id );
        }

        // 친구 수
        $friends_count = 0;
        if ( bp_is_active( 'friends' ) ) {
            $friends_count = friends_get_friend_count_for_user( $user_id );
        }

        // 그룹
        $groups = array();
        $groups_count = 0;
        if ( bp_is_active( 'groups' ) ) {
            $user_groups = groups_get_groups( array(
                'user_id' => $user_id,
                'per_page' => 100,
            ) );
            $groups_count = $user_groups['total'];
            foreach ( $user_groups['groups'] as $group ) {
                $groups[] = array(
                    'id' => $group->id,
                    'name' => $group->name,
                    'is_admin' => groups_is_user_admin( $user_id, $group->id ),
                );
            }
        }

        // 메시지 수
        $messages_count = 0;
        if ( bp_is_active( 'messages' ) ) {
            $messages_count = BP_Messages_Thread::get_total_threads_for_user( $user_id );
        }

        // 알림 수
        $notifications_count = 0;
        if ( bp_is_active( 'notifications' ) ) {
            $notifications_count = bp_notifications_get_unread_notification_count( $user_id );
        }

        // 마지막 활동
        $last_activity = bp_get_user_last_activity( $user_id );

        return array(
            'activity_count' => $activity_count,
            'friends_count' => $friends_count,
            'groups_count' => $groups_count,
            'groups' => $groups,
            'messages_count' => $messages_count,
            'unread_notifications' => $notifications_count,
            'last_activity' => $last_activity,
        );
    }

    /**
     * Ultimate Member 데이터 핸들러
     *
     * @param int $user_id 사용자 ID
     * @return array
     */
    public function handle_ultimate_member_data( $user_id ) {
        if ( ! function_exists( 'UM' ) ) {
            return array();
        }

        um_fetch_user( $user_id );

        // 프로필 완성도
        $profile_completeness = 0;
        if ( function_exists( 'um_profile_completeness_get_progress' ) ) {
            $profile_completeness = um_profile_completeness_get_progress( $user_id );
        }

        // 역할
        $roles = um_user( 'role' );

        // 계정 상태
        $account_status = um_user( 'account_status' );

        // 프로필 조회수 (Ultimate Member - Profile Views 확장 필요)
        $profile_views = get_user_meta( $user_id, '_um_profile_views', true );

        // 커버 사진 및 프로필 사진 여부
        $has_cover = ! empty( get_user_meta( $user_id, 'cover_photo', true ) );
        $has_profile_photo = ! empty( get_user_meta( $user_id, 'profile_photo', true ) );

        // 팔로워/팔로잉 (Ultimate Member - Followers 확장 필요)
        $followers = 0;
        $following = 0;
        if ( function_exists( 'um_followers_count' ) ) {
            $followers = um_followers_count( $user_id );
            $following = um_following_count( $user_id );
        }

        return array(
            'profile_completeness' => $profile_completeness,
            'roles' => $roles,
            'account_status' => $account_status,
            'profile_views' => intval( $profile_views ),
            'has_cover_photo' => $has_cover,
            'has_profile_photo' => $has_profile_photo,
            'followers' => $followers,
            'following' => $following,
        );
    }

    /**
     * Fluent Community 데이터 핸들러
     *
     * @param int $user_id 사용자 ID
     * @return array
     */
    public function handle_fluent_community_data( $user_id ) {
        if ( ! class_exists( 'FluentCommunity\\App\\App' ) ) {
            return array();
        }

        global $wpdb;
        $table_prefix = $wpdb->prefix . 'fcom_';

        // 게시글 수
        $posts_count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_prefix}posts WHERE user_id = %d",
            $user_id
        ) );

        // 댓글 수
        $comments_count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_prefix}comments WHERE user_id = %d",
            $user_id
        ) );

        // 좋아요 수
        $likes_received = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_prefix}reactions r
             INNER JOIN {$table_prefix}posts p ON r.object_id = p.id
             WHERE p.user_id = %d AND r.object_type = 'post'",
            $user_id
        ) );

        // 팔로워
        $followers = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_prefix}user_follows WHERE following_id = %d",
            $user_id
        ) );

        return array(
            'posts_count' => intval( $posts_count ),
            'comments_count' => intval( $comments_count ),
            'likes_received' => intval( $likes_received ),
            'followers' => intval( $followers ),
        );
    }

    /**
     * RunDash 데이터 핸들러
     *
     * @param int $user_id 사용자 ID
     * @return array
     */
    public function handle_rundash_data( $user_id ) {
        if ( ! class_exists( 'RunDash_Core' ) ) {
            return array();
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'rundash_activities';

        // 런닝 통계
        $stats = $wpdb->get_row( $wpdb->prepare(
            "SELECT
                COUNT(*) as total_runs,
                SUM(distance) as total_distance,
                SUM(duration) as total_duration,
                AVG(distance) as avg_distance,
                MAX(distance) as longest_run,
                MAX(created_at) as last_run_date
             FROM $table_name
             WHERE user_id = %d",
            $user_id
        ) );

        // 이번 달 런닝
        $monthly_stats = $wpdb->get_row( $wpdb->prepare(
            "SELECT
                COUNT(*) as runs,
                SUM(distance) as distance
             FROM $table_name
             WHERE user_id = %d
             AND MONTH(created_at) = MONTH(CURRENT_DATE())
             AND YEAR(created_at) = YEAR(CURRENT_DATE())",
            $user_id
        ) );

        return array(
            'total_runs' => intval( $stats->total_runs ?? 0 ),
            'total_distance' => floatval( $stats->total_distance ?? 0 ),
            'total_duration' => intval( $stats->total_duration ?? 0 ),
            'avg_distance' => floatval( $stats->avg_distance ?? 0 ),
            'longest_run' => floatval( $stats->longest_run ?? 0 ),
            'last_run_date' => $stats->last_run_date ?? null,
            'monthly_runs' => intval( $monthly_stats->runs ?? 0 ),
            'monthly_distance' => floatval( $monthly_stats->distance ?? 0 ),
        );
    }

    /**
     * FluentCRM 데이터 핸들러
     *
     * @param int $user_id 사용자 ID
     * @return array
     */
    public function handle_fluentcrm_data( $user_id ) {
        if ( ! class_exists( 'FluentCrm\\App\\App' ) ) {
            return array();
        }

        $subscriber = FluentCrmApi( 'contacts' )->getContactByUserRef( $user_id );

        if ( ! $subscriber ) {
            return array();
        }

        return array(
            'contact_id' => $subscriber->id,
            'status' => $subscriber->status,
            'tags' => $subscriber->tags ? $subscriber->tags->pluck( 'title' )->toArray() : array(),
            'lists' => $subscriber->lists ? $subscriber->lists->pluck( 'title' )->toArray() : array(),
            'email_stats' => array(
                'sent' => $subscriber->stats['total_emails'] ?? 0,
                'opened' => $subscriber->stats['total_opens'] ?? 0,
                'clicked' => $subscriber->stats['total_clicks'] ?? 0,
            ),
            'created_at' => $subscriber->created_at,
            'last_activity' => $subscriber->last_activity,
        );
    }

    /**
     * Monster Insights 데이터 핸들러
     *
     * @param int $user_id 사용자 ID
     * @return array
     */
    public function handle_monster_insights_data( $user_id ) {
        // Monster Insights는 주로 사이트 레벨 데이터를 제공
        // 사용자별 데이터는 제한적
        return array(
            'tracking_enabled' => class_exists( 'MonsterInsights' ),
            'note' => 'User-level data requires Google Analytics integration',
        );
    }

    // ==========================================
    // 행동 점수 계산
    // ==========================================

    /**
     * 행동 점수 계산
     *
     * @param array $profile 통합 프로필
     * @return array 점수 배열
     */
    private function calculate_behavioral_scores( $profile ) {
        $scores = array(
            'engagement' => $this->calculate_engagement_score( $profile ),
            'loyalty' => $this->calculate_loyalty_score( $profile ),
            'value' => $this->calculate_value_score( $profile ),
            'churn_risk' => $this->calculate_churn_risk_score( $profile ),
            'purchase_intent' => $this->calculate_purchase_intent_score( $profile ),
        );

        return apply_filters( 'acf_nudge_flow_behavioral_scores', $scores, $profile );
    }

    /**
     * 참여도 점수 계산 (0-100)
     */
    private function calculate_engagement_score( $profile ) {
        $score = 0;
        $factors = 0;

        // WooCommerce 참여
        if ( ! empty( $profile['sources']['woocommerce'] ) ) {
            $woo = $profile['sources']['woocommerce'];
            if ( $woo['total_orders'] > 0 ) {
                $score += min( 20, $woo['total_orders'] * 2 );
            }
            if ( ! empty( $woo['cart']['item_count'] ) ) {
                $score += 10;
            }
            $factors++;
        }

        // LearnDash 참여
        if ( ! empty( $profile['sources']['learndash'] ) ) {
            $ld = $profile['sources']['learndash'];
            $score += min( 20, $ld['enrolled_courses'] * 4 );
            $score += $ld['completion_rate'] * 0.2; // 최대 20점
            $factors++;
        }

        // BuddyPress 참여
        if ( ! empty( $profile['sources']['buddypress'] ) ) {
            $bp = $profile['sources']['buddypress'];
            $score += min( 15, $bp['activity_count'] * 0.5 );
            $score += min( 10, $bp['friends_count'] * 0.2 );
            $factors++;
        }

        // GamiPress 참여
        if ( ! empty( $profile['sources']['gamipress'] ) ) {
            $gp = $profile['sources']['gamipress'];
            $score += min( 15, $gp['total_achievements'] * 3 );
            $factors++;
        }

        // 평균 계산
        return $factors > 0 ? min( 100, round( $score / $factors * 2 ) ) : 0;
    }

    /**
     * 충성도 점수 계산 (0-100)
     */
    private function calculate_loyalty_score( $profile ) {
        $score = 0;

        // 가입 기간 (최대 30점)
        if ( ! empty( $profile['basic']['registered'] ) ) {
            $days_since_registration = floor( ( time() - strtotime( $profile['basic']['registered'] ) ) / DAY_IN_SECONDS );
            $score += min( 30, $days_since_registration / 10 );
        }

        // 반복 구매 (최대 40점)
        if ( ! empty( $profile['sources']['woocommerce'] ) ) {
            $woo = $profile['sources']['woocommerce'];
            $score += min( 40, $woo['total_orders'] * 4 );
        }

        // 코스 완료 (최대 20점)
        if ( ! empty( $profile['sources']['learndash'] ) ) {
            $ld = $profile['sources']['learndash'];
            $score += min( 20, $ld['completed_courses'] * 5 );
        }

        // 커뮤니티 참여 (최대 10점)
        if ( ! empty( $profile['sources']['buddypress'] ) ) {
            $score += min( 10, $profile['sources']['buddypress']['groups_count'] * 2 );
        }

        return min( 100, round( $score ) );
    }

    /**
     * 가치 점수 계산 (0-100)
     */
    private function calculate_value_score( $profile ) {
        $score = 0;

        // 총 구매액 (최대 50점)
        if ( ! empty( $profile['sources']['woocommerce'] ) ) {
            $total_spent = $profile['sources']['woocommerce']['total_spent'];
            // 100만원 이상 = 50점
            $score += min( 50, ( $total_spent / 1000000 ) * 50 );
        }

        // 평균 주문 금액 (최대 30점)
        if ( ! empty( $profile['sources']['woocommerce']['avg_order_value'] ) ) {
            $aov = $profile['sources']['woocommerce']['avg_order_value'];
            // 10만원 이상 = 30점
            $score += min( 30, ( $aov / 100000 ) * 30 );
        }

        // GamiPress 포인트 (최대 20점)
        if ( ! empty( $profile['sources']['gamipress'] ) ) {
            $points = $profile['sources']['gamipress']['total_points'];
            $score += min( 20, ( $points / 10000 ) * 20 );
        }

        return min( 100, round( $score ) );
    }

    /**
     * 이탈 위험 점수 계산 (0-100, 높을수록 위험)
     */
    private function calculate_churn_risk_score( $profile ) {
        $risk = 0;

        // 마지막 구매 이후 경과일
        if ( ! empty( $profile['sources']['woocommerce']['days_since_last_order'] ) ) {
            $days = $profile['sources']['woocommerce']['days_since_last_order'];
            if ( $days > 365 ) {
                $risk += 40;
            } elseif ( $days > 180 ) {
                $risk += 25;
            } elseif ( $days > 90 ) {
                $risk += 10;
            }
        }

        // 마지막 활동 이후 경과일
        if ( ! empty( $profile['sources']['buddypress']['last_activity'] ) ) {
            $last_activity = strtotime( $profile['sources']['buddypress']['last_activity'] );
            $days_inactive = floor( ( time() - $last_activity ) / DAY_IN_SECONDS );
            if ( $days_inactive > 90 ) {
                $risk += 30;
            } elseif ( $days_inactive > 30 ) {
                $risk += 15;
            }
        }

        // 코스 미완료
        if ( ! empty( $profile['sources']['learndash'] ) ) {
            $ld = $profile['sources']['learndash'];
            if ( $ld['enrolled_courses'] > 0 && $ld['completion_rate'] < 20 ) {
                $risk += 20;
            }
        }

        // 읽지 않은 알림 (참여 저하 신호)
        if ( ! empty( $profile['sources']['buddypress']['unread_notifications'] ) ) {
            if ( $profile['sources']['buddypress']['unread_notifications'] > 10 ) {
                $risk += 10;
            }
        }

        return min( 100, $risk );
    }

    /**
     * 구매 의도 점수 계산 (0-100)
     */
    private function calculate_purchase_intent_score( $profile ) {
        $score = 0;

        if ( ! empty( $profile['sources']['woocommerce'] ) ) {
            $woo = $profile['sources']['woocommerce'];

            // 장바구니에 상품 있음 (강한 신호)
            if ( ! empty( $woo['cart']['item_count'] ) && $woo['cart']['item_count'] > 0 ) {
                $score += 40;
            }

            // 위시리스트에 상품 있음
            if ( ! empty( $woo['wishlist_count'] ) && $woo['wishlist_count'] > 0 ) {
                $score += 20;
            }

            // 최근 상품 조회
            if ( ! empty( $woo['recently_viewed'] ) && count( $woo['recently_viewed'] ) > 0 ) {
                $score += min( 20, count( $woo['recently_viewed'] ) * 2 );
            }

            // 최근 구매 이력 (재구매 가능성)
            if ( ! empty( $woo['days_since_last_order'] ) && $woo['days_since_last_order'] < 30 ) {
                $score += 20;
            }
        }

        return min( 100, $score );
    }

    // ==========================================
    // 세그먼트 추론
    // ==========================================

    /**
     * 세그먼트 추론
     *
     * @param array $profile 통합 프로필
     * @return array 추론된 세그먼트 코드 배열
     */
    private function infer_segments( $profile ) {
        $segments = array();

        // 점수 기반 세그먼트
        $scores = $profile['scores'];

        // VIP 고객
        if ( $scores['value'] >= 80 && $scores['loyalty'] >= 70 ) {
            $segments[] = 'VIP';
        }

        // 충성 고객
        if ( $scores['loyalty'] >= 70 && $scores['churn_risk'] < 30 ) {
            $segments[] = 'LOYAL';
        }

        // 이탈 위험 고객
        if ( $scores['churn_risk'] >= 60 ) {
            $segments[] = 'AT_RISK';
        }

        // 높은 구매 의도
        if ( $scores['purchase_intent'] >= 60 ) {
            $segments[] = 'HIGH_INTENT';
        }

        // 새로운 고객
        if ( ! empty( $profile['basic']['registered'] ) ) {
            $days = floor( ( time() - strtotime( $profile['basic']['registered'] ) ) / DAY_IN_SECONDS );
            if ( $days <= 30 ) {
                $segments[] = 'NEW_USER';
            }
        }

        // 활성 학습자
        if ( ! empty( $profile['sources']['learndash'] ) ) {
            $ld = $profile['sources']['learndash'];
            if ( $ld['in_progress_courses'] > 0 ) {
                $segments[] = 'ACTIVE_LEARNER';
            }
            if ( $ld['completed_courses'] >= 3 ) {
                $segments[] = 'POWER_LEARNER';
            }
        }

        // 커뮤니티 리더
        if ( ! empty( $profile['sources']['buddypress'] ) ) {
            $bp = $profile['sources']['buddypress'];
            if ( $bp['activity_count'] > 50 && $bp['friends_count'] > 20 ) {
                $segments[] = 'COMMUNITY_LEADER';
            }
        }

        // 장바구니 포기자
        if ( ! empty( $profile['sources']['woocommerce'] ) ) {
            $woo = $profile['sources']['woocommerce'];
            if ( ! empty( $woo['cart']['item_count'] ) && $woo['cart']['item_count'] > 0 ) {
                $segments[] = 'CART_ABANDONER';
            }
        }

        return array_unique( $segments );
    }

    // ==========================================
    // 이벤트 핸들러
    // ==========================================

    /**
     * WooCommerce 주문 완료 이벤트
     */
    public function on_woo_order_completed( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( ! $order ) return;

        $user_id = $order->get_customer_id();
        if ( $user_id ) {
            $this->invalidate_user_cache( $user_id );
            do_action( 'acf_nudge_flow_customer_order_completed', $user_id, $order_id );
        }
    }

    /**
     * WooCommerce 주문 처리 중 이벤트
     */
    public function on_woo_order_processing( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( ! $order ) return;

        $user_id = $order->get_customer_id();
        if ( $user_id ) {
            $this->invalidate_user_cache( $user_id );
            do_action( 'acf_nudge_flow_customer_order_processing', $user_id, $order_id );
        }
    }

    /**
     * WooCommerce 장바구니 추가 이벤트
     */
    public function on_woo_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
        $user_id = get_current_user_id();
        if ( $user_id ) {
            do_action( 'acf_nudge_flow_cart_updated', $user_id, 'add', $product_id );
        }
    }

    /**
     * WooCommerce 장바구니 제거 이벤트
     */
    public function on_woo_cart_item_removed( $cart_item_key, $cart ) {
        $user_id = get_current_user_id();
        if ( $user_id ) {
            do_action( 'acf_nudge_flow_cart_updated', $user_id, 'remove' );
        }
    }

    /**
     * LearnDash 코스 완료 이벤트
     */
    public function on_ld_course_completed( $data ) {
        $user_id = $data['user']->ID ?? 0;
        if ( $user_id ) {
            $this->invalidate_user_cache( $user_id );
            do_action( 'acf_nudge_flow_course_completed', $user_id, $data['course']->ID ?? 0 );
        }
    }

    /**
     * LearnDash 레슨 완료 이벤트
     */
    public function on_ld_lesson_completed( $data ) {
        $user_id = $data['user']->ID ?? 0;
        if ( $user_id ) {
            do_action( 'acf_nudge_flow_lesson_completed', $user_id, $data['lesson']->ID ?? 0 );
        }
    }

    /**
     * LearnDash 퀴즈 완료 이벤트
     */
    public function on_ld_quiz_completed( $quiz_data, $user ) {
        $user_id = $user->ID ?? 0;
        if ( $user_id ) {
            $this->invalidate_user_cache( $user_id );
            do_action( 'acf_nudge_flow_quiz_completed', $user_id, $quiz_data );
        }
    }

    /**
     * GamiPress 포인트 획득 이벤트
     */
    public function on_gamipress_points_awarded( $user_id, $points, $points_type, $args ) {
        $this->invalidate_user_cache( $user_id );
        do_action( 'acf_nudge_flow_points_earned', $user_id, $points, $points_type );
    }

    /**
     * GamiPress 업적 획득 이벤트
     */
    public function on_gamipress_achievement_awarded( $user_id, $achievement_id, $trigger, $site_id, $args ) {
        $this->invalidate_user_cache( $user_id );
        do_action( 'acf_nudge_flow_achievement_earned', $user_id, $achievement_id );
    }

    /**
     * BuddyPress 활동 게시 이벤트
     */
    public function on_bp_activity_posted( $content, $user_id, $activity_id ) {
        do_action( 'acf_nudge_flow_activity_posted', $user_id, $activity_id );
    }

    /**
     * BuddyPress 친구 요청 수락 이벤트
     */
    public function on_bp_friendship_accepted( $id, $initiator_user_id, $friend_user_id, $friendship ) {
        do_action( 'acf_nudge_flow_friendship_accepted', $initiator_user_id, $friend_user_id );
    }

    // ==========================================
    // 캐시 관리
    // ==========================================

    /**
     * 사용자 캐시 무효화
     *
     * @param int $user_id 사용자 ID
     */
    public function invalidate_user_cache( $user_id ) {
        $cache_key = 'unified_profile_' . $user_id;
        unset( $this->profile_cache[ $cache_key ] );
        delete_transient( 'acf_nf_profile_' . $user_id );
    }

    /**
     * 만료된 캐시 정리
     */
    public function cleanup_expired_cache() {
        $current_time = time();
        foreach ( $this->profile_cache as $key => $data ) {
            if ( $data['expires'] < $current_time ) {
                unset( $this->profile_cache[ $key ] );
            }
        }
    }

    // ==========================================
    // AJAX 핸들러
    // ==========================================

    /**
     * AJAX: 통합 프로필 조회
     */
    public function ajax_get_unified_profile() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permission denied' );
        }

        $user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
        $force_refresh = isset( $_POST['force_refresh'] ) && $_POST['force_refresh'] === 'true';

        $profile = $this->get_unified_profile( $user_id, $force_refresh );
        wp_send_json_success( $profile );
    }

    /**
     * AJAX: 데이터 소스 동기화
     */
    public function ajax_sync_data_source() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Permission denied' );
        }

        $user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
        $source_id = isset( $_POST['source_id'] ) ? sanitize_text_field( $_POST['source_id'] ) : '';

        if ( ! $user_id || ! $source_id ) {
            wp_send_json_error( 'Invalid parameters' );
        }

        if ( ! isset( $this->handlers[ $source_id ] ) ) {
            wp_send_json_error( 'Unknown data source' );
        }

        $data = call_user_func( $this->handlers[ $source_id ], $user_id );

        // 캐시 업데이트
        $this->invalidate_user_cache( $user_id );

        wp_send_json_success( $data );
    }

    /**
     * AJAX: 활성 데이터 소스 조회
     */
    public function ajax_get_active_sources() {
        check_ajax_referer( 'acf_nudge_flow_nonce', 'nonce' );

        wp_send_json_success( $this->active_sources );
    }

    // ==========================================
    // REST API 핸들러
    // ==========================================

    /**
     * REST: 사용자 프로필 조회
     */
    public function rest_get_user_profile( $request ) {
        $user_id = $request->get_param( 'user_id' );
        $profile = $this->get_unified_profile( $user_id );
        return new WP_REST_Response( $profile, 200 );
    }

    /**
     * REST: 활성 데이터 소스 조회
     */
    public function rest_get_active_sources( $request ) {
        return new WP_REST_Response( $this->active_sources, 200 );
    }

    /**
     * REST: 사용자 데이터 동기화
     */
    public function rest_sync_user_data( $request ) {
        $user_id = $request->get_param( 'user_id' );

        if ( ! $user_id ) {
            return new WP_REST_Response( array( 'error' => 'User ID required' ), 400 );
        }

        $this->invalidate_user_cache( $user_id );
        $profile = $this->get_unified_profile( $user_id, true );

        return new WP_REST_Response( array(
            'message' => 'Data synced successfully',
            'profile' => $profile,
        ), 200 );
    }

    // ==========================================
    // 유틸리티 메서드
    // ==========================================

    /**
     * RFM 계산을 위한 구매 데이터 제공
     *
     * @param int $user_id 사용자 ID
     * @return array RFM Calculator용 데이터
     */
    public function get_purchase_data_for_rfm( $user_id ) {
        $profile = $this->get_unified_profile( $user_id );

        $data = array(
            'total_orders' => 0,
            'total_spent' => 0,
            'days_since_last_order' => null,
            'last_order_date' => null,
        );

        // WooCommerce 데이터
        if ( ! empty( $profile['sources']['woocommerce'] ) ) {
            $woo = $profile['sources']['woocommerce'];
            $data['total_orders'] = $woo['total_orders'];
            $data['total_spent'] = $woo['total_spent'];
            $data['days_since_last_order'] = $woo['days_since_last_order'];
            $data['last_order_date'] = $woo['last_order_date'];
        }

        // RunDash 데이터 추가
        if ( ! empty( $profile['sources']['rundash'] ) ) {
            $data['rundash'] = $profile['sources']['rundash'];
        }

        // GamiPress 데이터 추가
        if ( ! empty( $profile['sources']['gamipress'] ) ) {
            $data['gamipress'] = $profile['sources']['gamipress'];
        }

        return $data;
    }

    /**
     * 데이터 소스 정의 조회
     *
     * @return array
     */
    public function get_data_source_definitions() {
        return $this->data_source_definitions;
    }

    /**
     * 카테고리별 활성 소스 조회
     *
     * @param string $category 카테고리
     * @return array
     */
    public function get_active_sources_by_category( $category ) {
        return array_filter( $this->active_sources, function( $source ) use ( $category ) {
            return $source['category'] === $category;
        });
    }
}

/**
 * 전역 함수: 데이터 소스 통합 인스턴스 반환
 *
 * @return ACF_Nudge_Flow_Data_Source_Integration
 */
function acf_nudge_flow_data_source() {
    return ACF_Nudge_Flow_Data_Source_Integration::instance();
}

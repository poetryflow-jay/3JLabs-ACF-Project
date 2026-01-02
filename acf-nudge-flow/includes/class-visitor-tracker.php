<?php
/**
 * 방문자 트래커 클래스
 *
 * @package ACF_Nudge_Flow
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 방문자 트래커
 */
class ACF_Nudge_Visitor_Tracker {

    /**
     * 방문자 ID
     */
    private $visitor_id;

    /**
     * 생성자
     */
    public function __construct() {
        $this->visitor_id = $this->get_or_create_visitor_id();
    }

    /**
     * 방문자 ID 가져오기 또는 생성
     */
    private function get_or_create_visitor_id() {
        if ( isset( $_COOKIE['acf_nudge_visitor'] ) ) {
            return sanitize_text_field( $_COOKIE['acf_nudge_visitor'] );
        }

        $visitor_id = wp_generate_uuid4();

        // 쿠키 설정 (30일)
        $settings = get_option( 'acf_nudge_flow_settings', array() );
        $duration = isset( $settings['cookie_duration'] ) ? intval( $settings['cookie_duration'] ) : 30;

        if ( ! headers_sent() ) {
            setcookie( 'acf_nudge_visitor', $visitor_id, time() + ( DAY_IN_SECONDS * $duration ), COOKIEPATH, COOKIE_DOMAIN );
        }

        return $visitor_id;
    }

    /**
     * 방문자 데이터 수집
     */
    public function get_data() {
        global $wpdb;

        $data = array(
            'visitor_id'    => $this->visitor_id,
            'is_first_visit' => $this->is_first_visit(),
            'visit_count'   => $this->get_visit_count(),
            'referrer'      => $this->get_referrer(),
            'referrer_type' => $this->get_referrer_type(),
            'utm'           => $this->get_all_utm(),
            'is_logged_in'  => is_user_logged_in(),
            'user_id'       => get_current_user_id(),
            'device'        => $this->get_device_type(),
            'dismissed'     => $this->get_dismissed_nudges(),
        );

        // WooCommerce 데이터
        if ( class_exists( 'WooCommerce' ) ) {
            $data['woo'] = array(
                'has_purchased'   => $this->has_purchased(),
                'purchase_count'  => $this->get_purchase_count(),
                'total_spent'     => $this->get_total_spent(),
                'cart_count'      => $this->get_cart_count(),
                'cart_total'      => $this->get_cart_total(),
                'has_abandoned'   => $this->has_abandoned_cart(),
            );
        }

        return $data;
    }

    /**
     * 첫 방문 여부
     */
    public function is_first_visit() {
        return ! isset( $_COOKIE['acf_nudge_visitor'] );
    }

    /**
     * 방문 횟수
     */
    public function get_visit_count() {
        global $wpdb;

        $table = $wpdb->prefix . 'acf_nudge_visitors';
        $count = $wpdb->get_var( $wpdb->prepare(
            "SELECT visit_count FROM $table WHERE visitor_id = %s",
            $this->visitor_id
        ) );

        return $count ? intval( $count ) : 1;
    }

    /**
     * 리퍼러 가져오기
     */
    public function get_referrer() {
        if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
            return esc_url_raw( $_SERVER['HTTP_REFERER'] );
        }
        return '';
    }

    /**
     * 리퍼러 유형 판단
     */
    public function get_referrer_type() {
        $referrer = $this->get_referrer();

        if ( empty( $referrer ) ) {
            return 'direct';
        }

        $host = parse_url( $referrer, PHP_URL_HOST );
        $site_host = parse_url( home_url(), PHP_URL_HOST );

        // 내부 링크
        if ( $host === $site_host ) {
            return 'internal';
        }

        // UTM으로 판단
        if ( isset( $_GET['utm_medium'] ) ) {
            $medium = strtolower( $_GET['utm_medium'] );
            if ( in_array( $medium, array( 'cpc', 'ppc', 'paid', 'paidsearch' ) ) ) {
                return 'paid';
            }
            if ( $medium === 'email' ) {
                return 'email';
            }
            if ( in_array( $medium, array( 'social', 'social-media' ) ) ) {
                return 'social';
            }
        }

        // 검색엔진
        $search_engines = array( 'google', 'bing', 'yahoo', 'naver', 'daum', 'baidu', 'yandex' );
        foreach ( $search_engines as $engine ) {
            if ( strpos( $host, $engine ) !== false ) {
                return 'organic';
            }
        }

        // 소셜 미디어
        $social_platforms = array( 'facebook', 'instagram', 'twitter', 'linkedin', 'pinterest', 'youtube', 'tiktok' );
        foreach ( $social_platforms as $platform ) {
            if ( strpos( $host, $platform ) !== false ) {
                return 'social';
            }
        }

        return 'referral';
    }

    /**
     * 모든 UTM 파라미터
     */
    public function get_all_utm() {
        return array(
            'source'   => isset( $_GET['utm_source'] ) ? sanitize_text_field( $_GET['utm_source'] ) : '',
            'medium'   => isset( $_GET['utm_medium'] ) ? sanitize_text_field( $_GET['utm_medium'] ) : '',
            'campaign' => isset( $_GET['utm_campaign'] ) ? sanitize_text_field( $_GET['utm_campaign'] ) : '',
            'term'     => isset( $_GET['utm_term'] ) ? sanitize_text_field( $_GET['utm_term'] ) : '',
            'content'  => isset( $_GET['utm_content'] ) ? sanitize_text_field( $_GET['utm_content'] ) : '',
        );
    }

    /**
     * 특정 UTM 가져오기
     */
    public function get_utm( $key ) {
        $utm = $this->get_all_utm();
        return isset( $utm[ $key ] ) ? $utm[ $key ] : '';
    }

    /**
     * 디바이스 유형
     */
    public function get_device_type() {
        if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
            return 'unknown';
        }

        $ua = $_SERVER['HTTP_USER_AGENT'];

        if ( preg_match( '/mobile|android|iphone|ipad|phone/i', $ua ) ) {
            if ( preg_match( '/ipad|tablet/i', $ua ) ) {
                return 'tablet';
            }
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * 닫은 넛지 목록
     */
    public function get_dismissed_nudges() {
        $dismissed = isset( $_COOKIE['acf_nudge_dismissed'] ) ? $_COOKIE['acf_nudge_dismissed'] : '';
        if ( empty( $dismissed ) ) {
            return array();
        }
        return array_map( 'intval', explode( ',', $dismissed ) );
    }

    /**
     * 넛지 닫기 저장
     */
    public function dismiss_nudge( $nudge_id, $type = 'session' ) {
        $dismissed = $this->get_dismissed_nudges();
        $dismissed[] = $nudge_id;
        $dismissed = array_unique( $dismissed );

        $expiry = ( $type === 'forever' ) ? time() + YEAR_IN_SECONDS : 0;

        if ( ! headers_sent() ) {
            setcookie(
                'acf_nudge_dismissed',
                implode( ',', $dismissed ),
                $expiry,
                COOKIEPATH,
                COOKIE_DOMAIN
            );
        }
    }

    /**
     * 이벤트 추적
     */
    public function track_event( $type, $data = array() ) {
        global $wpdb;

        $table = $wpdb->prefix . 'acf_nudge_events';

        $wpdb->insert( $table, array(
            'visitor_id'  => $this->visitor_id,
            'event_type'  => sanitize_text_field( $type ),
            'event_data'  => wp_json_encode( $data ),
            'workflow_id' => isset( $data['workflow_id'] ) ? intval( $data['workflow_id'] ) : null,
            'created_at'  => current_time( 'mysql' ),
        ) );
    }

    /**
     * 방문 기록
     */
    public function record_visit() {
        global $wpdb;

        $table = $wpdb->prefix . 'acf_nudge_visitors';
        $exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM $table WHERE visitor_id = %s",
            $this->visitor_id
        ) );

        if ( $exists ) {
            // 업데이트
            $wpdb->query( $wpdb->prepare(
                "UPDATE $table SET visit_count = visit_count + 1, last_visit = %s WHERE visitor_id = %s",
                current_time( 'mysql' ),
                $this->visitor_id
            ) );
        } else {
            // 새로 생성
            $wpdb->insert( $table, array(
                'visitor_id'   => $this->visitor_id,
                'user_id'      => get_current_user_id(),
                'first_visit'  => current_time( 'mysql' ),
                'last_visit'   => current_time( 'mysql' ),
                'visit_count'  => 1,
                'referrer'     => $this->get_referrer(),
                'utm_source'   => $this->get_utm( 'source' ),
                'utm_medium'   => $this->get_utm( 'medium' ),
                'utm_campaign' => $this->get_utm( 'campaign' ),
            ) );
        }
    }

    // === WooCommerce 메서드 ===

    public function has_purchased() {
        if ( ! is_user_logged_in() || ! function_exists( 'wc_get_customer_order_count' ) ) {
            return false;
        }
        return wc_get_customer_order_count( get_current_user_id() ) > 0;
    }

    public function get_purchase_count() {
        if ( ! is_user_logged_in() || ! function_exists( 'wc_get_customer_order_count' ) ) {
            return 0;
        }
        return wc_get_customer_order_count( get_current_user_id() );
    }

    public function get_total_spent() {
        if ( ! is_user_logged_in() || ! function_exists( 'wc_get_customer_total_spent' ) ) {
            return 0;
        }
        return wc_get_customer_total_spent( get_current_user_id() );
    }

    public function get_cart_count() {
        if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
            return 0;
        }
        return WC()->cart->get_cart_contents_count();
    }

    public function get_cart_total() {
        if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
            return 0;
        }
        return WC()->cart->get_cart_contents_total();
    }

    public function has_abandoned_cart() {
        // 세션에 장바구니가 있고, 마지막 활동이 30분 이상 전인 경우
        if ( ! function_exists( 'WC' ) || ! WC()->session ) {
            return false;
        }

        $last_activity = WC()->session->get( 'acf_nudge_last_activity' );
        if ( ! $last_activity ) {
            return false;
        }

        $cart_count = $this->get_cart_count();
        $time_diff = time() - intval( $last_activity );

        return ( $cart_count > 0 && $time_diff > 1800 ); // 30분
    }
}

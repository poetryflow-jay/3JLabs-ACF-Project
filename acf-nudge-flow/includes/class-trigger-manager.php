<?php
/**
 * 트리거 매니저 클래스
 *
 * @package ACF_Nudge_Flow
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 트리거 매니저
 */
class ACF_Nudge_Trigger_Manager {

    /**
     * 등록된 트리거 목록
     */
    private $triggers = array();

    /**
     * 생성자
     */
    public function __construct() {
        $this->register_default_triggers();
    }

    /**
     * 기본 트리거 등록
     */
    private function register_default_triggers() {
        // === 방문자 트리거 ===
        $this->register( 'first_visit', array(
            'label'       => __( '첫 방문자', 'acf-nudge-flow' ),
            'description' => __( '처음 방문한 사용자에게 적용', 'acf-nudge-flow' ),
            'category'    => 'visitor',
            'icon'        => '👋',
            'callback'    => array( $this, 'check_first_visit' ),
        ) );

        $this->register( 'returning_visitor', array(
            'label'       => __( '재방문자', 'acf-nudge-flow' ),
            'description' => __( '이전에 방문한 적이 있는 사용자', 'acf-nudge-flow' ),
            'category'    => 'visitor',
            'icon'        => '🔄',
            'callback'    => array( $this, 'check_returning_visitor' ),
        ) );

        $this->register( 'visit_count', array(
            'label'       => __( '방문 횟수', 'acf-nudge-flow' ),
            'description' => __( 'N회 이상 방문한 사용자', 'acf-nudge-flow' ),
            'category'    => 'visitor',
            'icon'        => '📊',
            'fields'      => array(
                'operator' => array( 'type' => 'select', 'options' => array( '>=', '<=', '==' ) ),
                'count'    => array( 'type' => 'number', 'default' => 3 ),
            ),
            'callback'    => array( $this, 'check_visit_count' ),
        ) );

        $this->register( 'time_on_site', array(
            'label'       => __( '체류 시간', 'acf-nudge-flow' ),
            'description' => __( 'N초 이상 사이트에 머문 경우', 'acf-nudge-flow' ),
            'category'    => 'visitor',
            'icon'        => '⏱️',
            'fields'      => array(
                'seconds' => array( 'type' => 'number', 'default' => 30 ),
            ),
            'callback'    => array( $this, 'check_time_on_site' ),
        ) );

        $this->register( 'scroll_depth', array(
            'label'       => __( '스크롤 깊이', 'acf-nudge-flow' ),
            'description' => __( '페이지 N% 이상 스크롤', 'acf-nudge-flow' ),
            'category'    => 'visitor',
            'icon'        => '📜',
            'fields'      => array(
                'percentage' => array( 'type' => 'number', 'default' => 50, 'min' => 0, 'max' => 100 ),
            ),
            'callback'    => array( $this, 'check_scroll_depth' ),
        ) );

        $this->register( 'exit_intent', array(
            'label'       => __( '이탈 의도', 'acf-nudge-flow' ),
            'description' => __( '마우스가 브라우저 밖으로 이동할 때', 'acf-nudge-flow' ),
            'category'    => 'visitor',
            'icon'        => '🚪',
            'callback'    => array( $this, 'check_exit_intent' ),
        ) );

        // === 트래픽 소스 트리거 ===
        $this->register( 'referrer_type', array(
            'label'       => __( '유입 경로 유형', 'acf-nudge-flow' ),
            'description' => __( '광고, 오가닉, 다이렉트, 소셜 등', 'acf-nudge-flow' ),
            'category'    => 'traffic',
            'icon'        => '🔗',
            'fields'      => array(
                'type' => array(
                    'type'    => 'select',
                    'options' => array(
                        'direct'  => __( '다이렉트', 'acf-nudge-flow' ),
                        'organic' => __( '오가닉 검색', 'acf-nudge-flow' ),
                        'paid'    => __( '유료 광고', 'acf-nudge-flow' ),
                        'social'  => __( '소셜 미디어', 'acf-nudge-flow' ),
                        'email'   => __( '이메일', 'acf-nudge-flow' ),
                        'referral' => __( '외부 링크', 'acf-nudge-flow' ),
                    ),
                ),
            ),
            'callback'    => array( $this, 'check_referrer_type' ),
        ) );

        $this->register( 'utm_source', array(
            'label'       => __( 'UTM 소스', 'acf-nudge-flow' ),
            'description' => __( '특정 UTM 소스에서 유입된 경우', 'acf-nudge-flow' ),
            'category'    => 'traffic',
            'icon'        => '📍',
            'fields'      => array(
                'source' => array( 'type' => 'text', 'placeholder' => 'google, facebook, naver' ),
            ),
            'callback'    => array( $this, 'check_utm_source' ),
        ) );

        $this->register( 'utm_campaign', array(
            'label'       => __( 'UTM 캠페인', 'acf-nudge-flow' ),
            'description' => __( '특정 캠페인에서 유입된 경우', 'acf-nudge-flow' ),
            'category'    => 'traffic',
            'icon'        => '🎯',
            'fields'      => array(
                'campaign' => array( 'type' => 'text', 'placeholder' => 'black_friday_2026' ),
            ),
            'callback'    => array( $this, 'check_utm_campaign' ),
        ) );

        // === 사용자 상태 트리거 ===
        $this->register( 'logged_in', array(
            'label'       => __( '로그인 상태', 'acf-nudge-flow' ),
            'description' => __( '로그인한 사용자인지 여부', 'acf-nudge-flow' ),
            'category'    => 'user',
            'icon'        => '👤',
            'fields'      => array(
                'status' => array(
                    'type'    => 'select',
                    'options' => array(
                        'logged_in'  => __( '로그인 상태', 'acf-nudge-flow' ),
                        'logged_out' => __( '로그아웃 상태', 'acf-nudge-flow' ),
                    ),
                ),
            ),
            'callback'    => array( $this, 'check_logged_in' ),
        ) );

        $this->register( 'user_role', array(
            'label'       => __( '사용자 역할', 'acf-nudge-flow' ),
            'description' => __( '특정 역할의 사용자에게 적용', 'acf-nudge-flow' ),
            'category'    => 'user',
            'icon'        => '🎭',
            'fields'      => array(
                'roles' => array( 'type' => 'multiselect', 'options' => 'get_editable_roles' ),
            ),
            'callback'    => array( $this, 'check_user_role' ),
        ) );

        // === WooCommerce 트리거 ===
        if ( class_exists( 'WooCommerce' ) ) {
            $this->register( 'has_purchased', array(
                'label'       => __( '구매 이력', 'acf-nudge-flow' ),
                'description' => __( '한 번이라도 구매한 적이 있는 고객', 'acf-nudge-flow' ),
                'category'    => 'woocommerce',
                'icon'        => '🛒',
                'fields'      => array(
                    'has_purchased' => array(
                        'type'    => 'select',
                        'options' => array(
                            'yes' => __( '구매 이력 있음', 'acf-nudge-flow' ),
                            'no'  => __( '구매 이력 없음', 'acf-nudge-flow' ),
                        ),
                    ),
                ),
                'callback'    => array( $this, 'check_has_purchased' ),
            ) );

            $this->register( 'cart_value', array(
                'label'       => __( '장바구니 금액', 'acf-nudge-flow' ),
                'description' => __( '장바구니 금액이 특정 값 이상/이하', 'acf-nudge-flow' ),
                'category'    => 'woocommerce',
                'icon'        => '💰',
                'fields'      => array(
                    'operator' => array( 'type' => 'select', 'options' => array( '>=', '<=', '==' ) ),
                    'amount'   => array( 'type' => 'number', 'default' => 50000 ),
                ),
                'callback'    => array( $this, 'check_cart_value' ),
            ) );

            $this->register( 'abandoned_cart', array(
                'label'       => __( '장바구니 이탈', 'acf-nudge-flow' ),
                'description' => __( '장바구니에 상품을 담고 결제하지 않은 경우', 'acf-nudge-flow' ),
                'category'    => 'woocommerce',
                'icon'        => '🛑',
                'callback'    => array( $this, 'check_abandoned_cart' ),
            ) );

            $this->register( 'total_spent', array(
                'label'       => __( '총 구매 금액', 'acf-nudge-flow' ),
                'description' => __( '누적 구매 금액 기준', 'acf-nudge-flow' ),
                'category'    => 'woocommerce',
                'icon'        => '💎',
                'fields'      => array(
                    'operator' => array( 'type' => 'select', 'options' => array( '>=', '<=', '==' ) ),
                    'amount'   => array( 'type' => 'number', 'default' => 100000 ),
                ),
                'callback'    => array( $this, 'check_total_spent' ),
            ) );

            $this->register( 'has_inquiry', array(
                'label'       => __( '문의 이력', 'acf-nudge-flow' ),
                'description' => __( '상품 문의를 남긴 적이 있는 고객', 'acf-nudge-flow' ),
                'category'    => 'woocommerce',
                'icon'        => '💬',
                'callback'    => array( $this, 'check_has_inquiry' ),
            ) );
        }

        // === 시간 기반 트리거 ===
        $this->register( 'time_of_day', array(
            'label'       => __( '시간대', 'acf-nudge-flow' ),
            'description' => __( '특정 시간대에 방문한 경우', 'acf-nudge-flow' ),
            'category'    => 'time',
            'icon'        => '🕐',
            'fields'      => array(
                'start_hour' => array( 'type' => 'number', 'min' => 0, 'max' => 23, 'default' => 9 ),
                'end_hour'   => array( 'type' => 'number', 'min' => 0, 'max' => 23, 'default' => 18 ),
            ),
            'callback'    => array( $this, 'check_time_of_day' ),
        ) );

        $this->register( 'date_range', array(
            'label'       => __( '날짜 범위', 'acf-nudge-flow' ),
            'description' => __( '특정 기간 동안만 적용', 'acf-nudge-flow' ),
            'category'    => 'time',
            'icon'        => '📅',
            'fields'      => array(
                'start_date' => array( 'type' => 'date' ),
                'end_date'   => array( 'type' => 'date' ),
            ),
            'callback'    => array( $this, 'check_date_range' ),
        ) );

        // 필터로 추가 트리거 등록 허용
        do_action( 'acf_nudge_flow_register_triggers', $this );
    }

    /**
     * 트리거 등록
     */
    public function register( $id, $args ) {
        $this->triggers[ $id ] = wp_parse_args( $args, array(
            'label'       => '',
            'description' => '',
            'category'    => 'general',
            'icon'        => '⚡',
            'fields'      => array(),
            'callback'    => null,
            'pro'         => false,
        ) );
    }

    /**
     * 모든 트리거 반환
     */
    public function get_all() {
        return $this->triggers;
    }

    /**
     * 카테고리별 트리거 반환
     */
    public function get_by_category( $category ) {
        return array_filter( $this->triggers, function( $trigger ) use ( $category ) {
            return $trigger['category'] === $category;
        } );
    }

    /**
     * 트리거 평가
     */
    public function evaluate( $trigger_id, $settings = array() ) {
        if ( ! isset( $this->triggers[ $trigger_id ] ) ) {
            return false;
        }

        $trigger = $this->triggers[ $trigger_id ];

        if ( is_callable( $trigger['callback'] ) ) {
            return call_user_func( $trigger['callback'], $settings );
        }

        return false;
    }

    // === 트리거 체크 콜백 ===

    public function check_first_visit( $settings ) {
        $visitor_id = isset( $_COOKIE['acf_nudge_visitor'] ) ? $_COOKIE['acf_nudge_visitor'] : '';
        return empty( $visitor_id );
    }

    public function check_returning_visitor( $settings ) {
        return ! $this->check_first_visit( $settings );
    }

    public function check_visit_count( $settings ) {
        $tracker = new ACF_Nudge_Visitor_Tracker();
        $count = $tracker->get_visit_count();
        $target = isset( $settings['count'] ) ? intval( $settings['count'] ) : 3;
        $operator = isset( $settings['operator'] ) ? $settings['operator'] : '>=';

        switch ( $operator ) {
            case '>=': return $count >= $target;
            case '<=': return $count <= $target;
            case '==': return $count == $target;
            default:   return false;
        }
    }

    public function check_time_on_site( $settings ) {
        // JavaScript에서 처리
        return true; // 프론트엔드에서 평가
    }

    public function check_scroll_depth( $settings ) {
        // JavaScript에서 처리
        return true; // 프론트엔드에서 평가
    }

    public function check_exit_intent( $settings ) {
        // JavaScript에서 처리
        return true; // 프론트엔드에서 평가
    }

    public function check_referrer_type( $settings ) {
        $tracker = new ACF_Nudge_Visitor_Tracker();
        $referrer_type = $tracker->get_referrer_type();
        $target_type = isset( $settings['type'] ) ? $settings['type'] : '';

        return $referrer_type === $target_type;
    }

    public function check_utm_source( $settings ) {
        $source = isset( $_GET['utm_source'] ) ? sanitize_text_field( $_GET['utm_source'] ) : '';
        if ( empty( $source ) ) {
            $tracker = new ACF_Nudge_Visitor_Tracker();
            $source = $tracker->get_utm( 'source' );
        }
        $target = isset( $settings['source'] ) ? $settings['source'] : '';

        return strtolower( $source ) === strtolower( $target );
    }

    public function check_utm_campaign( $settings ) {
        $campaign = isset( $_GET['utm_campaign'] ) ? sanitize_text_field( $_GET['utm_campaign'] ) : '';
        if ( empty( $campaign ) ) {
            $tracker = new ACF_Nudge_Visitor_Tracker();
            $campaign = $tracker->get_utm( 'campaign' );
        }
        $target = isset( $settings['campaign'] ) ? $settings['campaign'] : '';

        return strtolower( $campaign ) === strtolower( $target );
    }

    public function check_logged_in( $settings ) {
        $target = isset( $settings['status'] ) ? $settings['status'] : 'logged_in';

        if ( $target === 'logged_in' ) {
            return is_user_logged_in();
        } else {
            return ! is_user_logged_in();
        }
    }

    public function check_user_role( $settings ) {
        if ( ! is_user_logged_in() ) {
            return false;
        }

        $user = wp_get_current_user();
        $target_roles = isset( $settings['roles'] ) ? (array) $settings['roles'] : array();

        return ! empty( array_intersect( $user->roles, $target_roles ) );
    }

    public function check_has_purchased( $settings ) {
        if ( ! is_user_logged_in() || ! function_exists( 'wc_get_customer_order_count' ) ) {
            return false;
        }

        $order_count = wc_get_customer_order_count( get_current_user_id() );
        $target = isset( $settings['has_purchased'] ) ? $settings['has_purchased'] : 'yes';

        return ( $target === 'yes' ) ? ( $order_count > 0 ) : ( $order_count === 0 );
    }

    public function check_cart_value( $settings ) {
        if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
            return false;
        }

        $cart_total = WC()->cart->get_cart_contents_total();
        $target = isset( $settings['amount'] ) ? floatval( $settings['amount'] ) : 0;
        $operator = isset( $settings['operator'] ) ? $settings['operator'] : '>=';

        switch ( $operator ) {
            case '>=': return $cart_total >= $target;
            case '<=': return $cart_total <= $target;
            case '==': return $cart_total == $target;
            default:   return false;
        }
    }

    public function check_abandoned_cart( $settings ) {
        // 장바구니 이탈 확인 로직
        if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
            return false;
        }

        $tracker = new ACF_Nudge_Visitor_Tracker();
        return $tracker->has_abandoned_cart();
    }

    public function check_total_spent( $settings ) {
        if ( ! is_user_logged_in() || ! function_exists( 'wc_get_customer_total_spent' ) ) {
            return false;
        }

        $total = wc_get_customer_total_spent( get_current_user_id() );
        $target = isset( $settings['amount'] ) ? floatval( $settings['amount'] ) : 0;
        $operator = isset( $settings['operator'] ) ? $settings['operator'] : '>=';

        switch ( $operator ) {
            case '>=': return $total >= $target;
            case '<=': return $total <= $target;
            case '==': return $total == $target;
            default:   return false;
        }
    }

    public function check_has_inquiry( $settings ) {
        if ( ! is_user_logged_in() ) {
            return false;
        }

        // 상품 문의 확인 (댓글 기반)
        $user_id = get_current_user_id();
        $comments = get_comments( array(
            'user_id' => $user_id,
            'type'    => 'product_inquiry',
            'count'   => true,
        ) );

        return $comments > 0;
    }

    public function check_time_of_day( $settings ) {
        $current_hour = intval( current_time( 'G' ) );
        $start = isset( $settings['start_hour'] ) ? intval( $settings['start_hour'] ) : 0;
        $end = isset( $settings['end_hour'] ) ? intval( $settings['end_hour'] ) : 23;

        return ( $current_hour >= $start && $current_hour <= $end );
    }

    public function check_date_range( $settings ) {
        $today = current_time( 'Y-m-d' );
        $start = isset( $settings['start_date'] ) ? $settings['start_date'] : '';
        $end = isset( $settings['end_date'] ) ? $settings['end_date'] : '';

        if ( ! empty( $start ) && $today < $start ) {
            return false;
        }

        if ( ! empty( $end ) && $today > $end ) {
            return false;
        }

        return true;
    }
}

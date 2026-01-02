<?php
/**
 * 액션 매니저 클래스
 *
 * @package ACF_Nudge_Flow
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 액션 매니저
 */
class ACF_Nudge_Action_Manager {

    /**
     * 등록된 액션 목록
     */
    private $actions = array();

    /**
     * 생성자
     */
    public function __construct() {
        $this->register_default_actions();
    }

    /**
     * 기본 액션 등록
     */
    private function register_default_actions() {
        // === 팝업/모달 ===
        $this->register( 'popup_center', array(
            'label'       => __( '중앙 팝업', 'acf-nudge-flow' ),
            'description' => __( '화면 중앙에 모달 팝업 표시', 'acf-nudge-flow' ),
            'category'    => 'popup',
            'icon'        => '📢',
            'fields'      => array(
                'title'       => array( 'type' => 'text', 'label' => __( '제목', 'acf-nudge-flow' ) ),
                'content'     => array( 'type' => 'wysiwyg', 'label' => __( '내용', 'acf-nudge-flow' ) ),
                'cta_text'    => array( 'type' => 'text', 'label' => __( 'CTA 버튼 텍스트', 'acf-nudge-flow' ) ),
                'cta_url'     => array( 'type' => 'url', 'label' => __( 'CTA 버튼 링크', 'acf-nudge-flow' ) ),
                'image'       => array( 'type' => 'image', 'label' => __( '이미지', 'acf-nudge-flow' ) ),
                'close_text'  => array( 'type' => 'text', 'label' => __( '닫기 버튼 텍스트', 'acf-nudge-flow' ), 'default' => __( '나중에', 'acf-nudge-flow' ) ),
                'style'       => array( 'type' => 'select', 'label' => __( '스타일', 'acf-nudge-flow' ), 'options' => array(
                    'default' => __( '기본', 'acf-nudge-flow' ),
                    'minimal' => __( '미니멀', 'acf-nudge-flow' ),
                    'bold'    => __( '볼드', 'acf-nudge-flow' ),
                    'festive' => __( '축제', 'acf-nudge-flow' ),
                ) ),
            ),
        ) );

        $this->register( 'popup_slide_in', array(
            'label'       => __( '슬라이드 팝업', 'acf-nudge-flow' ),
            'description' => __( '화면 측면에서 슬라이드되어 나타남', 'acf-nudge-flow' ),
            'category'    => 'popup',
            'icon'        => '📥',
            'fields'      => array(
                'position'    => array( 'type' => 'select', 'label' => __( '위치', 'acf-nudge-flow' ), 'options' => array(
                    'bottom-right' => __( '우하단', 'acf-nudge-flow' ),
                    'bottom-left'  => __( '좌하단', 'acf-nudge-flow' ),
                    'top-right'    => __( '우상단', 'acf-nudge-flow' ),
                    'top-left'     => __( '좌상단', 'acf-nudge-flow' ),
                ) ),
                'title'       => array( 'type' => 'text', 'label' => __( '제목', 'acf-nudge-flow' ) ),
                'content'     => array( 'type' => 'textarea', 'label' => __( '내용', 'acf-nudge-flow' ) ),
                'cta_text'    => array( 'type' => 'text', 'label' => __( 'CTA 버튼 텍스트', 'acf-nudge-flow' ) ),
                'cta_url'     => array( 'type' => 'url', 'label' => __( 'CTA 버튼 링크', 'acf-nudge-flow' ) ),
            ),
        ) );

        $this->register( 'popup_fullscreen', array(
            'label'       => __( '전체화면 오버레이', 'acf-nudge-flow' ),
            'description' => __( '전체 화면을 덮는 오버레이', 'acf-nudge-flow' ),
            'category'    => 'popup',
            'icon'        => '🖥️',
            'pro'         => true,
            'fields'      => array(
                'background'  => array( 'type' => 'color', 'label' => __( '배경색', 'acf-nudge-flow' ), 'default' => 'rgba(0,0,0,0.8)' ),
                'content'     => array( 'type' => 'wysiwyg', 'label' => __( '내용', 'acf-nudge-flow' ) ),
            ),
        ) );

        // === 바/배너 ===
        $this->register( 'bar_top', array(
            'label'       => __( '상단 바', 'acf-nudge-flow' ),
            'description' => __( '페이지 상단에 고정된 알림 바', 'acf-nudge-flow' ),
            'category'    => 'bar',
            'icon'        => '📍',
            'fields'      => array(
                'text'        => array( 'type' => 'text', 'label' => __( '메시지', 'acf-nudge-flow' ) ),
                'cta_text'    => array( 'type' => 'text', 'label' => __( 'CTA 텍스트', 'acf-nudge-flow' ) ),
                'cta_url'     => array( 'type' => 'url', 'label' => __( 'CTA 링크', 'acf-nudge-flow' ) ),
                'bg_color'    => array( 'type' => 'color', 'label' => __( '배경색', 'acf-nudge-flow' ), 'default' => '#667eea' ),
                'text_color'  => array( 'type' => 'color', 'label' => __( '텍스트색', 'acf-nudge-flow' ), 'default' => '#ffffff' ),
                'dismissible' => array( 'type' => 'checkbox', 'label' => __( '닫기 가능', 'acf-nudge-flow' ), 'default' => true ),
            ),
        ) );

        $this->register( 'bar_bottom', array(
            'label'       => __( '하단 바', 'acf-nudge-flow' ),
            'description' => __( '페이지 하단에 고정된 알림 바', 'acf-nudge-flow' ),
            'category'    => 'bar',
            'icon'        => '📌',
            'fields'      => array(
                'text'        => array( 'type' => 'text', 'label' => __( '메시지', 'acf-nudge-flow' ) ),
                'cta_text'    => array( 'type' => 'text', 'label' => __( 'CTA 텍스트', 'acf-nudge-flow' ) ),
                'cta_url'     => array( 'type' => 'url', 'label' => __( 'CTA 링크', 'acf-nudge-flow' ) ),
                'bg_color'    => array( 'type' => 'color', 'label' => __( '배경색', 'acf-nudge-flow' ), 'default' => '#1a1a2e' ),
            ),
        ) );

        $this->register( 'countdown_bar', array(
            'label'       => __( '카운트다운 바', 'acf-nudge-flow' ),
            'description' => __( '마감 시간까지 카운트다운 표시', 'acf-nudge-flow' ),
            'category'    => 'bar',
            'icon'        => '⏰',
            'pro'         => true,
            'fields'      => array(
                'text'        => array( 'type' => 'text', 'label' => __( '메시지', 'acf-nudge-flow' ), 'default' => '🔥 특가 마감까지' ),
                'end_date'    => array( 'type' => 'datetime', 'label' => __( '마감 시간', 'acf-nudge-flow' ) ),
                'cta_text'    => array( 'type' => 'text', 'label' => __( 'CTA 텍스트', 'acf-nudge-flow' ) ),
                'cta_url'     => array( 'type' => 'url', 'label' => __( 'CTA 링크', 'acf-nudge-flow' ) ),
            ),
        ) );

        // === 토스트/알림 ===
        $this->register( 'toast', array(
            'label'       => __( '토스트 알림', 'acf-nudge-flow' ),
            'description' => __( '작은 알림 메시지 표시', 'acf-nudge-flow' ),
            'category'    => 'notification',
            'icon'        => '🔔',
            'fields'      => array(
                'message'     => array( 'type' => 'text', 'label' => __( '메시지', 'acf-nudge-flow' ) ),
                'type'        => array( 'type' => 'select', 'label' => __( '유형', 'acf-nudge-flow' ), 'options' => array(
                    'info'    => __( '정보', 'acf-nudge-flow' ),
                    'success' => __( '성공', 'acf-nudge-flow' ),
                    'warning' => __( '경고', 'acf-nudge-flow' ),
                    'promo'   => __( '프로모션', 'acf-nudge-flow' ),
                ) ),
                'position'    => array( 'type' => 'select', 'label' => __( '위치', 'acf-nudge-flow' ), 'options' => array(
                    'top-right'    => __( '우상단', 'acf-nudge-flow' ),
                    'top-left'     => __( '좌상단', 'acf-nudge-flow' ),
                    'bottom-right' => __( '우하단', 'acf-nudge-flow' ),
                    'bottom-left'  => __( '좌하단', 'acf-nudge-flow' ),
                ) ),
                'duration'    => array( 'type' => 'number', 'label' => __( '표시 시간 (초)', 'acf-nudge-flow' ), 'default' => 5 ),
            ),
        ) );

        $this->register( 'social_proof', array(
            'label'       => __( '소셜 프루프', 'acf-nudge-flow' ),
            'description' => __( '"N명이 이 상품을 보고 있습니다" 표시', 'acf-nudge-flow' ),
            'category'    => 'notification',
            'icon'        => '👥',
            'pro'         => true,
            'fields'      => array(
                'message_template' => array( 'type' => 'text', 'label' => __( '메시지 템플릿', 'acf-nudge-flow' ), 'default' => '{{count}}명이 지금 이 페이지를 보고 있습니다' ),
                'min_count'        => array( 'type' => 'number', 'label' => __( '최소 표시 수', 'acf-nudge-flow' ), 'default' => 5 ),
                'max_count'        => array( 'type' => 'number', 'label' => __( '최대 표시 수', 'acf-nudge-flow' ), 'default' => 30 ),
            ),
        ) );

        $this->register( 'browser_push_prompt', array(
            'label'       => __( '브라우저 알림 동의', 'acf-nudge-flow' ),
            'description' => __( '브라우저 푸시 알림 수신 동의 요청', 'acf-nudge-flow' ),
            'category'    => 'notification',
            'icon'        => '🔕',
            'fields'      => array(
                'title'       => array( 'type' => 'text', 'label' => __( '제목', 'acf-nudge-flow' ), 'default' => '알림을 받으시겠습니까?' ),
                'message'     => array( 'type' => 'textarea', 'label' => __( '메시지', 'acf-nudge-flow' ), 'default' => '할인 및 이벤트 소식을 빠르게 받아보세요!' ),
            ),
        ) );

        // === 폼/리드 ===
        $this->register( 'newsletter_popup', array(
            'label'       => __( '뉴스레터 팝업', 'acf-nudge-flow' ),
            'description' => __( '이메일 구독 폼 팝업', 'acf-nudge-flow' ),
            'category'    => 'form',
            'icon'        => '📧',
            'fields'      => array(
                'title'       => array( 'type' => 'text', 'label' => __( '제목', 'acf-nudge-flow' ), 'default' => '뉴스레터 구독' ),
                'description' => array( 'type' => 'textarea', 'label' => __( '설명', 'acf-nudge-flow' ) ),
                'incentive'   => array( 'type' => 'text', 'label' => __( '인센티브', 'acf-nudge-flow' ), 'default' => '가입 시 10% 할인 쿠폰 증정!' ),
                'button_text' => array( 'type' => 'text', 'label' => __( '버튼 텍스트', 'acf-nudge-flow' ), 'default' => '구독하기' ),
                'integration' => array( 'type' => 'select', 'label' => __( '연동 서비스', 'acf-nudge-flow' ), 'options' => array(
                    'wordpress' => __( 'WordPress 사용자', 'acf-nudge-flow' ),
                    'mailchimp' => 'Mailchimp',
                    'stibee'    => 'Stibee',
                    'custom'    => __( '커스텀 웹훅', 'acf-nudge-flow' ),
                ) ),
            ),
        ) );

        $this->register( 'contact_form_popup', array(
            'label'       => __( '문의 폼 팝업', 'acf-nudge-flow' ),
            'description' => __( '연락처 수집 폼 팝업', 'acf-nudge-flow' ),
            'category'    => 'form',
            'icon'        => '📝',
            'fields'      => array(
                'title'       => array( 'type' => 'text', 'label' => __( '제목', 'acf-nudge-flow' ) ),
                'fields_list' => array( 'type' => 'repeater', 'label' => __( '입력 필드', 'acf-nudge-flow' ) ),
                'button_text' => array( 'type' => 'text', 'label' => __( '제출 버튼', 'acf-nudge-flow' ), 'default' => '보내기' ),
            ),
        ) );

        // === WooCommerce 액션 ===
        if ( class_exists( 'WooCommerce' ) ) {
            $this->register( 'upsell_popup', array(
                'label'       => __( '업셀링 팝업', 'acf-nudge-flow' ),
                'description' => __( '더 높은 가격의 상품 추천', 'acf-nudge-flow' ),
                'category'    => 'woocommerce',
                'icon'        => '⬆️',
                'pro'         => true,
                'fields'      => array(
                    'title'       => array( 'type' => 'text', 'label' => __( '제목', 'acf-nudge-flow' ), 'default' => '이 상품도 추천드려요!' ),
                    'product_ids' => array( 'type' => 'product_select', 'label' => __( '추천 상품', 'acf-nudge-flow' ), 'multiple' => true ),
                    'auto_select' => array( 'type' => 'checkbox', 'label' => __( '자동 선택', 'acf-nudge-flow' ), 'description' => __( '장바구니 상품과 관련된 상품 자동 표시', 'acf-nudge-flow' ) ),
                ),
            ) );

            $this->register( 'crosssell_popup', array(
                'label'       => __( '크로스셀링 팝업', 'acf-nudge-flow' ),
                'description' => __( '관련 상품 추천', 'acf-nudge-flow' ),
                'category'    => 'woocommerce',
                'icon'        => '🔀',
                'pro'         => true,
                'fields'      => array(
                    'title'       => array( 'type' => 'text', 'label' => __( '제목', 'acf-nudge-flow' ), 'default' => '함께 구매하면 좋은 상품' ),
                    'discount'    => array( 'type' => 'number', 'label' => __( '세트 할인율 (%)', 'acf-nudge-flow' ) ),
                ),
            ) );

            $this->register( 'free_shipping_bar', array(
                'label'       => __( '무료배송 프로그레스 바', 'acf-nudge-flow' ),
                'description' => __( '무료배송까지 남은 금액 표시', 'acf-nudge-flow' ),
                'category'    => 'woocommerce',
                'icon'        => '🚚',
                'fields'      => array(
                    'threshold'   => array( 'type' => 'number', 'label' => __( '무료배송 기준 금액', 'acf-nudge-flow' ), 'default' => 50000 ),
                    'message_before' => array( 'type' => 'text', 'label' => __( '미달성 메시지', 'acf-nudge-flow' ), 'default' => '{{remaining}}원 더 담으면 무료배송!' ),
                    'message_after'  => array( 'type' => 'text', 'label' => __( '달성 메시지', 'acf-nudge-flow' ), 'default' => '🎉 무료배송 적용!' ),
                ),
            ) );

            $this->register( 'cart_reminder', array(
                'label'       => __( '장바구니 리마인더', 'acf-nudge-flow' ),
                'description' => __( '장바구니에 상품이 있음을 알림', 'acf-nudge-flow' ),
                'category'    => 'woocommerce',
                'icon'        => '🛒',
                'fields'      => array(
                    'title'       => array( 'type' => 'text', 'label' => __( '제목', 'acf-nudge-flow' ), 'default' => '장바구니에 상품이 있어요!' ),
                    'message'     => array( 'type' => 'textarea', 'label' => __( '메시지', 'acf-nudge-flow' ) ),
                    'show_items'  => array( 'type' => 'checkbox', 'label' => __( '상품 목록 표시', 'acf-nudge-flow' ), 'default' => true ),
                ),
            ) );

            $this->register( 'discount_reveal', array(
                'label'       => __( '할인 코드 공개', 'acf-nudge-flow' ),
                'description' => __( '할인 쿠폰 코드 표시', 'acf-nudge-flow' ),
                'category'    => 'woocommerce',
                'icon'        => '🎁',
                'fields'      => array(
                    'title'       => array( 'type' => 'text', 'label' => __( '제목', 'acf-nudge-flow' ), 'default' => '🎉 특별 할인!' ),
                    'coupon_code' => array( 'type' => 'text', 'label' => __( '쿠폰 코드', 'acf-nudge-flow' ) ),
                    'description' => array( 'type' => 'textarea', 'label' => __( '설명', 'acf-nudge-flow' ) ),
                    'auto_apply'  => array( 'type' => 'checkbox', 'label' => __( '자동 적용', 'acf-nudge-flow' ) ),
                ),
            ) );

            $this->register( 'limited_stock', array(
                'label'       => __( '한정 수량 알림', 'acf-nudge-flow' ),
                'description' => __( '재고가 적을 때 긴급성 표시', 'acf-nudge-flow' ),
                'category'    => 'woocommerce',
                'icon'        => '⚠️',
                'fields'      => array(
                    'threshold'   => array( 'type' => 'number', 'label' => __( '표시 기준 재고', 'acf-nudge-flow' ), 'default' => 10 ),
                    'message'     => array( 'type' => 'text', 'label' => __( '메시지', 'acf-nudge-flow' ), 'default' => '⚠️ 단 {{stock}}개 남았습니다!' ),
                ),
            ) );
        }

        // === 리다이렉트/페이지 ===
        $this->register( 'redirect', array(
            'label'       => __( '페이지 이동', 'acf-nudge-flow' ),
            'description' => __( '특정 페이지로 리다이렉트', 'acf-nudge-flow' ),
            'category'    => 'redirect',
            'icon'        => '↪️',
            'fields'      => array(
                'url'         => array( 'type' => 'url', 'label' => __( '이동할 URL', 'acf-nudge-flow' ) ),
                'delay'       => array( 'type' => 'number', 'label' => __( '지연 시간 (초)', 'acf-nudge-flow' ), 'default' => 0 ),
                'new_tab'     => array( 'type' => 'checkbox', 'label' => __( '새 탭에서 열기', 'acf-nudge-flow' ) ),
            ),
        ) );

        $this->register( 'show_element', array(
            'label'       => __( '요소 표시', 'acf-nudge-flow' ),
            'description' => __( '숨겨진 페이지 요소 표시', 'acf-nudge-flow' ),
            'category'    => 'page',
            'icon'        => '👁️',
            'fields'      => array(
                'selector'    => array( 'type' => 'text', 'label' => __( 'CSS 선택자', 'acf-nudge-flow' ), 'placeholder' => '.hidden-offer' ),
                'animation'   => array( 'type' => 'select', 'label' => __( '애니메이션', 'acf-nudge-flow' ), 'options' => array(
                    'fade'  => 'Fade In',
                    'slide' => 'Slide Down',
                    'zoom'  => 'Zoom In',
                ) ),
            ),
        ) );

        // 필터로 추가 액션 등록 허용
        do_action( 'acf_nudge_flow_register_actions', $this );
    }

    /**
     * 액션 등록
     */
    public function register( $id, $args ) {
        $this->actions[ $id ] = wp_parse_args( $args, array(
            'label'       => '',
            'description' => '',
            'category'    => 'general',
            'icon'        => '⚡',
            'fields'      => array(),
            'pro'         => false,
        ) );
    }

    /**
     * 모든 액션 반환
     */
    public function get_all() {
        return $this->actions;
    }

    /**
     * 카테고리별 액션 반환
     */
    public function get_by_category( $category ) {
        return array_filter( $this->actions, function( $action ) use ( $category ) {
            return $action['category'] === $category;
        } );
    }

    /**
     * 액션 실행 데이터 생성
     */
    public function prepare_action_data( $action_id, $settings = array() ) {
        if ( ! isset( $this->actions[ $action_id ] ) ) {
            return null;
        }

        return array(
            'id'       => $action_id,
            'type'     => $action_id,
            'settings' => $settings,
            'meta'     => $this->actions[ $action_id ],
        );
    }
}

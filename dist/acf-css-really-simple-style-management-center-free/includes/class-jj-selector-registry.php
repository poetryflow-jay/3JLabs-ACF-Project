<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CSS 선택자 레지스트리 클래스
 * 
 * 전역에서 사용되는 공통 CSS 선택자를 중앙 관리하여
 * 어댑터 간 충돌을 방지하고 유지보수성을 향상시킵니다.
 * 
 * @since v3.5.0
 */
final class JJ_Selector_Registry {
    
    private static $instance = null;
    private $selectors = array();
    
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_default_selectors();
    }
    
    /**
     * 기본 선택자 등록
     */
    private function init_default_selectors() {
        // 전역 버튼 선택자
        $this->selectors['buttons']['primary'] = array(
            'base' => array(
                '.button',
                'button',
                'input[type="submit"]',
                '.wp-block-button__link',
                'a.button',
                // Elementor 버튼
                '.elementor-button',
                '.elementor-button-link',
            ),
            'hover' => array(
                '.button:hover',
                'button:hover',
                'input[type="submit"]:hover',
                '.wp-block-button__link:hover',
                'a.button:hover',
                '.elementor-button:hover',
                '.elementor-button-link:hover',
            ),
        );
        
        $this->selectors['buttons']['secondary'] = array(
            'base' => array(
                '.button-secondary',
                '.button.alt',
                '.btn-secondary',
                '.wp-block-button.is-style-outline .wp-block-button__link',
            ),
            'hover' => array(
                '.button-secondary:hover',
                '.button.alt:hover',
                '.btn-secondary:hover',
                '.wp-block-button.is-style-outline .wp-block-button__link:hover',
            ),
        );
        
        // 전역 폼 선택자
        $this->selectors['forms']['input'] = array(
            'base' => array(
                'input[type="text"]',
                'input[type="email"]',
                'input[type="password"]',
                'input[type="number"]',
                'input[type="url"]',
                'input[type="tel"]',
                'input[type="search"]',
                'textarea',
                'select',
                '.form-control',
                '.input-text',
            ),
            'focus' => array(
                'input[type="text"]:focus',
                'input[type="email"]:focus',
                'input[type="password"]:focus',
                'textarea:focus',
                'select:focus',
                '.form-control:focus',
                '.input-text:focus',
            ),
        );
        
        // 로그인/회원가입 폼
        $this->selectors['forms']['login'] = array(
            'base' => array(
                '#loginform',
                '.login-form',
                '.woocommerce-form-login',
                '.um-form',
                '.um-login',
            ),
        );
        
        // 댓글 폼
        $this->selectors['forms']['comment'] = array(
            'base' => array(
                '#commentform',
                '.comment-form',
                'textarea#comment',
            ),
        );
    }
    
    /**
     * 선택자 가져오기
     * 
     * @param string $category 카테고리 (buttons, forms 등)
     * @param string $type 타입 (primary, secondary 등)
     * @param string $state 상태 (base, hover, focus 등)
     * @return array 선택자 배열
     */
    public function get_selectors( $category, $type = '', $state = 'base' ) {
        if ( empty( $type ) ) {
            return $this->selectors[ $category ] ?? array();
        }
        
        if ( empty( $state ) ) {
            return $this->selectors[ $category ][ $type ] ?? array();
        }
        
        return $this->selectors[ $category ][ $type ][ $state ] ?? array();
    }
    
    /**
     * 선택자 추가 (어댑터에서 사용)
     * 
     * @param string $category 카테고리
     * @param string $type 타입
     * @param string $state 상태
     * @param array $selectors 추가할 선택자 배열
     */
    public function add_selectors( $category, $type, $state, $selectors ) {
        if ( ! isset( $this->selectors[ $category ] ) ) {
            $this->selectors[ $category ] = array();
        }
        if ( ! isset( $this->selectors[ $category ][ $type ] ) ) {
            $this->selectors[ $category ][ $type ] = array();
        }
        if ( ! isset( $this->selectors[ $category ][ $type ][ $state ] ) ) {
            $this->selectors[ $category ][ $type ][ $state ] = array();
        }
        
        $this->selectors[ $category ][ $type ][ $state ] = array_merge(
            $this->selectors[ $category ][ $type ][ $state ],
            $selectors
        );
    }
    
    /**
     * 선택자를 CSS 규칙으로 변환
     * 
     * @param string $category 카테고리
     * @param string $type 타입
     * @param string $state 상태
     * @param string $css_properties CSS 속성
     * @return string CSS 규칙
     */
    public function build_css_rule( $category, $type, $state, $css_properties ) {
        $selectors = $this->get_selectors( $category, $type, $state );
        if ( empty( $selectors ) ) {
            return '';
        }
        
        $selector_string = implode( ', ', $selectors );
        return "$selector_string { $css_properties }";
    }
}


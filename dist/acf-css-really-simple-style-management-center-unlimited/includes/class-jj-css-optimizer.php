<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ CSS Optimizer
 * 
 * 생성된 CSS를 압축(Minify)하고 전송 방식을 최적화하는 성능 엔진입니다.
 * 
 * @since v5.9.0
 */
class JJ_CSS_Optimizer {

    private static $instance = null;
    private $options = array();

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->options = get_option( 'jj_style_guide_options', array() );
        
        // CSS 출력 필터링 (Tree Shaking -> Minify)
        // - Tree Shaking으로 불필요 규칙 제거 후 Minify로 최종 압축
        add_filter( 'jj_generated_css_output', array( $this, 'tree_shake_css' ), 998 );
        add_filter( 'jj_generated_css_output', array( $this, 'minify_css' ), 999 );
        
        // CSS 로드 최적화 (Premium 이상)
        if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'optimize_css_delivery' ), 999 );
        }
    }

    /**
     * CSS 압축 (Minification)
     * 공백, 줄바꿈, 주석 제거
     */
    public function minify_css( $css ) {
        // 주석 제거
        $css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
        
        // 공백 제거: 콜론(:) 뒤, 콤마(,) 뒤, 괄호({, }) 앞뒤
        $css = str_replace( array( ': ', ', ', ' {', '} ', '{ ', '; ' ), array( ':', ',', '{', '}', '{', ';' ), $css );
        
        // 줄바꿈, 탭 제거
        $css = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $css );
        
        return trim( $css );
    }

    /**
     * CSS Tree Shaking: 사용하지 않는 CSS 제거
     * 
     * 옵션에서 활성화되지 않은 기능의 CSS 규칙을 제거하여 파일 크기 감소
     * 
     * @since v6.1.0
     */
    public function tree_shake_css( $css ) {
        // Tree Shaking 활성화 여부 확인
        $enable_tree_shaking = isset( $this->options['performance']['enable_tree_shaking'] ) 
            ? $this->options['performance']['enable_tree_shaking'] 
            : true; // 기본값: 활성화
        
        if ( ! $enable_tree_shaking ) {
            return $css;
        }
        
        // 사용되지 않는 CSS 규칙 제거
        $css = $this->remove_unused_rules( $css );
        
        return $css;
    }
    
    /**
     * 사용되지 않는 CSS 규칙 제거
     */
    private function remove_unused_rules( $css ) {
        $removed_count = 0;
        
        // 1. 활성화되지 않은 팔레트 관련 CSS 제거
        if ( ! $this->is_palette_active( 'system' ) ) {
            $css = preg_replace( '/\.jj-palette-system[^{]*\{[^}]*\}/', '', $css );
            $removed_count++;
        }
        if ( ! $this->is_palette_active( 'alternative' ) ) {
            $css = preg_replace( '/\.jj-palette-alternative[^{]*\{[^}]*\}/', '', $css );
            $removed_count++;
        }
        
        // 2. 활성화되지 않은 타이포그래피 관련 CSS 제거
        $typography_keys = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'body', 'p' );
        foreach ( $typography_keys as $key ) {
            if ( ! $this->is_typography_active( $key ) ) {
                $css = preg_replace( '/\.jj-typography-' . preg_quote( $key, '/' ) . '[^{]*\{[^}]*\}/', '', $css );
                $removed_count++;
            }
        }
        
        // 3. 활성화되지 않은 버튼 스타일 제거
        $button_types = array( 'primary', 'secondary', 'text' );
        foreach ( $button_types as $type ) {
            if ( ! $this->is_button_active( $type ) ) {
                $css = preg_replace( '/\.jj-button-' . preg_quote( $type, '/' ) . '[^{]*\{[^}]*\}/', '', $css );
                $removed_count++;
            }
        }
        
        // 4. 빈 규칙 블록 제거
        $css = preg_replace( '/[^{}]*\{\s*\}/', '', $css );
        
        // 5. 연속된 공백 정리
        $css = preg_replace( '/\s+/', ' ', $css );
        
        return trim( $css );
    }
    
    /**
     * 팔레트 활성화 여부 확인
     */
    private function is_palette_active( $palette_name ) {
        if ( ! isset( $this->options['palettes'][ $palette_name ] ) ) {
            return false;
        }
        
        $palette = $this->options['palettes'][ $palette_name ];
        
        // 팔레트에 하나라도 색상이 설정되어 있으면 활성화
        foreach ( $palette as $color ) {
            if ( ! empty( $color ) ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 타이포그래피 활성화 여부 확인
     */
    private function is_typography_active( $key ) {
        if ( ! isset( $this->options['typography'][ $key ] ) ) {
            return false;
        }
        
        $typography = $this->options['typography'][ $key ];
        
        // 폰트 패밀리나 크기가 설정되어 있으면 활성화
        return ! empty( $typography['font_family'] ) || ! empty( $typography['font_size'] );
    }
    
    /**
     * 버튼 활성화 여부 확인
     */
    private function is_button_active( $type ) {
        if ( ! isset( $this->options['buttons'][ $type ] ) ) {
            return false;
        }
        
        $button = $this->options['buttons'][ $type ];
        
        // 배경색이나 텍스트 색상이 설정되어 있으면 활성화
        return ! empty( $button['bg_color'] ) || ! empty( $button['text_color'] );
    }
    
    /**
     * CSS 로드 최적화 (비동기 로드 등)
     * 현재는 예시로 'preload' 속성 추가 로직 구현
     */
    public function optimize_css_delivery() {
        // 이 기능은 플러그인이 생성한 CSS 파일 핸들에 대해 동작해야 함
        // JJ_CSS_Injector에서 핸들명을 확인해야 함 (보통 'jj-style-guide-main')
        
        // 여기서는 예시로, 특정 조건 하에 비동기 로드 태그를 추가하는 방식을 고려할 수 있음.
        // 하지만 WordPress 표준 enqueue 시스템을 건드리는 건 신중해야 함.
        
        // 대신, Critical CSS 모드를 위한 훅을 준비해둡니다.
        // (실제 Critical CSS 생성은 복잡한 HTML 파싱이 필요하므로 추후 고도화)
    }
}

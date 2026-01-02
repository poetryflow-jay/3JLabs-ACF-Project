<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Login Customizer
 * 
 * 워드프레스 로그인 화면을 커스터마이징하는 엔진입니다.
 * 로고 교체, 배경 변경, 폼 스타일링을 지원합니다.
 * 
 * @since v5.6.0
 */
class JJ_Login_Customizer {

    private static $instance = null;
    private $options = array();

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 옵션 로드 (아직 UI는 없으므로 기본값 사용 또는 DB 조회)
        $this->options = get_option( 'jj_style_guide_visual_options', array() );
        
        add_action( 'login_enqueue_scripts', array( $this, 'enqueue_login_styles' ) );
        add_filter( 'login_headerurl', array( $this, 'custom_login_logo_url' ) );
        add_filter( 'login_headertext', array( $this, 'custom_login_logo_title' ) );
    }

    /**
     * 로그인 화면용 CSS 출력
     */
    public function enqueue_login_styles() {
        // [설정값 예시]
        // 실제로는 $this->options에서 가져와야 함
        $logo_url = isset( $this->options['login_logo_url'] ) ? $this->options['login_logo_url'] : '';
        $bg_color = isset( $this->options['login_bg_color'] ) ? $this->options['login_bg_color'] : '#f0f0f1';
        $form_bg_color = isset( $this->options['login_form_bg_color'] ) ? $this->options['login_form_bg_color'] : '#ffffff';
        $button_color = isset( $this->options['login_button_color'] ) ? $this->options['login_button_color'] : '#2271b1';

        echo '<style type="text/css">';
        
        // 배경 설정
        echo "body.login { background-color: {$bg_color}; }";
        
        // 로고 설정
        if ( ! empty( $logo_url ) ) {
            echo ".login h1 a { 
                background-image: url('{$logo_url}'); 
                background-size: contain; 
                background-position: center; 
                width: 100%; 
                height: 80px; 
            }";
        }

        // 폼 스타일
        echo ".login form { background: {$form_bg_color}; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border-radius: 8px; }";
        
        // 버튼 스타일
        echo ".wp-core-ui .button-primary { 
            background-color: {$button_color}; 
            border-color: {$button_color}; 
            transition: all 0.3s ease;
        }";
        echo ".wp-core-ui .button-primary:hover { 
            background-color: " . $this->adjust_brightness($button_color, -20) . "; 
            border-color: " . $this->adjust_brightness($button_color, -20) . "; 
        }";

        echo '</style>';
    }

    /**
     * 로고 클릭 시 이동할 URL (홈페이지로 변경)
     */
    public function custom_login_logo_url() {
        return home_url();
    }

    /**
     * 로고 마우스 오버 텍스트 (사이트 이름으로 변경)
     */
    public function custom_login_logo_title() {
        return get_bloginfo( 'name' );
    }

    /**
     * 색상 밝기 조절 (Hex -> New Hex)
     * 간단한 유틸리티 함수
     */
    private function adjust_brightness( $hex, $steps ) {
        $steps = max( -255, min( 255, $steps ) );
        $hex = str_replace( '#', '', $hex );
        if ( strlen( $hex ) == 3 ) {
            $hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
        }
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );

        $r = max( 0, min( 255, $r + $steps ) );
        $g = max( 0, min( 255, $g + $steps ) );
        $b = max( 0, min( 255, $b + $steps ) );

        return '#' . str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT ) . str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT ) . str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );
    }
}


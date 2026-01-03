<?php
/**
 * JJ Palette Manager - 색상 팔레트 관리 클래스
 * 
 * 색상 팔레트의 생성, 저장, 적용을 담당합니다.
 * 색상 조화, 대비 계산, 접근성 검사 기능을 제공합니다.
 * 
 * @package ACF_CSS_Style_Guide
 * @version 22.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class JJ_Palette_Manager
 */
class JJ_Palette_Manager {

    /**
     * 싱글톤 인스턴스
     * @var JJ_Palette_Manager|null
     */
    private static $instance = null;

    /**
     * 팔레트 옵션 키
     * @var string
     */
    private $option_key = 'jj_style_guide_palettes';

    /**
     * 기본 팔레트
     * @var array
     */
    private $default_palette = array();

    /**
     * 생성자
     */
    private function __construct() {
        $this->default_palette = $this->get_default_palette();
        $this->init_hooks();
    }

    /**
     * 싱글톤 인스턴스 반환
     * @return JJ_Palette_Manager
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_save_palette', array( $this, 'ajax_save_palette' ) );
        add_action( 'wp_ajax_jj_get_palettes', array( $this, 'ajax_get_palettes' ) );
        add_action( 'wp_ajax_jj_delete_palette', array( $this, 'ajax_delete_palette' ) );
        add_action( 'wp_ajax_jj_generate_palette', array( $this, 'ajax_generate_palette' ) );
        
        // REST API
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
    }

    /**
     * 기본 팔레트 정의
     * @return array
     */
    public function get_default_palette() {
        return array(
            'primary' => array(
                'name'   => __( '주요 색상', 'acf-css-really-simple-style-management-center' ),
                'colors' => array(
                    '50'  => '#eff6ff',
                    '100' => '#dbeafe',
                    '200' => '#bfdbfe',
                    '300' => '#93c5fd',
                    '400' => '#60a5fa',
                    '500' => '#3b82f6',
                    '600' => '#2563eb',
                    '700' => '#1d4ed8',
                    '800' => '#1e40af',
                    '900' => '#1e3a8a',
                    '950' => '#172554',
                ),
            ),
            'secondary' => array(
                'name'   => __( '보조 색상', 'acf-css-really-simple-style-management-center' ),
                'colors' => array(
                    '50'  => '#f8fafc',
                    '100' => '#f1f5f9',
                    '200' => '#e2e8f0',
                    '300' => '#cbd5e1',
                    '400' => '#94a3b8',
                    '500' => '#64748b',
                    '600' => '#475569',
                    '700' => '#334155',
                    '800' => '#1e293b',
                    '900' => '#0f172a',
                    '950' => '#020617',
                ),
            ),
            'accent' => array(
                'name'   => __( '강조 색상', 'acf-css-really-simple-style-management-center' ),
                'colors' => array(
                    '50'  => '#fffbeb',
                    '100' => '#fef3c7',
                    '200' => '#fde68a',
                    '300' => '#fcd34d',
                    '400' => '#fbbf24',
                    '500' => '#f59e0b',
                    '600' => '#d97706',
                    '700' => '#b45309',
                    '800' => '#92400e',
                    '900' => '#78350f',
                    '950' => '#451a03',
                ),
            ),
            'success' => array(
                'name'   => __( '성공', 'acf-css-really-simple-style-management-center' ),
                'colors' => array(
                    '500' => '#22c55e',
                    '600' => '#16a34a',
                    '700' => '#15803d',
                ),
            ),
            'warning' => array(
                'name'   => __( '경고', 'acf-css-really-simple-style-management-center' ),
                'colors' => array(
                    '500' => '#eab308',
                    '600' => '#ca8a04',
                    '700' => '#a16207',
                ),
            ),
            'error' => array(
                'name'   => __( '오류', 'acf-css-really-simple-style-management-center' ),
                'colors' => array(
                    '500' => '#ef4444',
                    '600' => '#dc2626',
                    '700' => '#b91c1c',
                ),
            ),
            'info' => array(
                'name'   => __( '정보', 'acf-css-really-simple-style-management-center' ),
                'colors' => array(
                    '500' => '#06b6d4',
                    '600' => '#0891b2',
                    '700' => '#0e7490',
                ),
            ),
        );
    }

    /**
     * 저장된 팔레트 가져오기
     * @return array
     */
    public function get_palettes() {
        $palettes = get_option( $this->option_key, array() );
        
        if ( empty( $palettes ) ) {
            return array( 'default' => $this->default_palette );
        }
        
        return $palettes;
    }

    /**
     * 특정 팔레트 가져오기
     * @param string $palette_id
     * @return array|null
     */
    public function get_palette( $palette_id ) {
        $palettes = $this->get_palettes();
        return isset( $palettes[ $palette_id ] ) ? $palettes[ $palette_id ] : null;
    }

    /**
     * 팔레트 저장
     * @param string $palette_id
     * @param array $palette_data
     * @return bool
     */
    public function save_palette( $palette_id, $palette_data ) {
        $palettes = $this->get_palettes();
        $palettes[ $palette_id ] = $palette_data;
        return update_option( $this->option_key, $palettes );
    }

    /**
     * 팔레트 삭제
     * @param string $palette_id
     * @return bool
     */
    public function delete_palette( $palette_id ) {
        if ( $palette_id === 'default' ) {
            return false; // 기본 팔레트는 삭제 불가
        }
        
        $palettes = $this->get_palettes();
        
        if ( isset( $palettes[ $palette_id ] ) ) {
            unset( $palettes[ $palette_id ] );
            return update_option( $this->option_key, $palettes );
        }
        
        return false;
    }

    /**
     * 색상 조화 팔레트 생성 (보색, 유사색 등)
     * @param string $base_color HEX 기본 색상
     * @param string $harmony_type 조화 유형 (complementary, analogous, triadic, split-complementary)
     * @return array
     */
    public function generate_harmony( $base_color, $harmony_type = 'complementary' ) {
        $hsl = $this->hex_to_hsl( $base_color );
        
        if ( ! $hsl ) {
            return array( $base_color );
        }
        
        $colors = array( $base_color );
        
        switch ( $harmony_type ) {
            case 'complementary':
                // 보색 (180도)
                $colors[] = $this->hsl_to_hex( ( $hsl['h'] + 180 ) % 360, $hsl['s'], $hsl['l'] );
                break;
                
            case 'analogous':
                // 유사색 (30도 간격)
                $colors[] = $this->hsl_to_hex( ( $hsl['h'] + 30 ) % 360, $hsl['s'], $hsl['l'] );
                $colors[] = $this->hsl_to_hex( ( $hsl['h'] - 30 + 360 ) % 360, $hsl['s'], $hsl['l'] );
                break;
                
            case 'triadic':
                // 삼색 조화 (120도 간격)
                $colors[] = $this->hsl_to_hex( ( $hsl['h'] + 120 ) % 360, $hsl['s'], $hsl['l'] );
                $colors[] = $this->hsl_to_hex( ( $hsl['h'] + 240 ) % 360, $hsl['s'], $hsl['l'] );
                break;
                
            case 'split-complementary':
                // 분할 보색 (150도, 210도)
                $colors[] = $this->hsl_to_hex( ( $hsl['h'] + 150 ) % 360, $hsl['s'], $hsl['l'] );
                $colors[] = $this->hsl_to_hex( ( $hsl['h'] + 210 ) % 360, $hsl['s'], $hsl['l'] );
                break;
                
            case 'tetradic':
                // 사색 조화 (90도 간격)
                $colors[] = $this->hsl_to_hex( ( $hsl['h'] + 90 ) % 360, $hsl['s'], $hsl['l'] );
                $colors[] = $this->hsl_to_hex( ( $hsl['h'] + 180 ) % 360, $hsl['s'], $hsl['l'] );
                $colors[] = $this->hsl_to_hex( ( $hsl['h'] + 270 ) % 360, $hsl['s'], $hsl['l'] );
                break;
        }
        
        return $colors;
    }

    /**
     * 색상 스케일 생성 (밝기 변화)
     * @param string $base_color HEX 기본 색상
     * @param int $steps 단계 수
     * @return array
     */
    public function generate_scale( $base_color, $steps = 11 ) {
        $hsl = $this->hex_to_hsl( $base_color );
        
        if ( ! $hsl ) {
            return array();
        }
        
        $scale = array();
        $labels = array( '50', '100', '200', '300', '400', '500', '600', '700', '800', '900', '950' );
        
        // 밝기 값 매핑 (50 = 가장 밝음, 950 = 가장 어두움)
        $lightness_map = array( 97, 94, 86, 76, 64, 50, 40, 32, 24, 17, 10 );
        
        for ( $i = 0; $i < min( $steps, count( $labels ) ); $i++ ) {
            $label = $labels[ $i ];
            $lightness = $lightness_map[ $i ];
            $scale[ $label ] = $this->hsl_to_hex( $hsl['h'], $hsl['s'], $lightness );
        }
        
        return $scale;
    }

    /**
     * WCAG 접근성 검사
     * @param string $foreground 전경색 HEX
     * @param string $background 배경색 HEX
     * @return array 검사 결과
     */
    public function check_accessibility( $foreground, $background ) {
        if ( ! class_exists( 'JJ_Common_Utils' ) ) {
            return array( 'error' => 'JJ_Common_Utils not available' );
        }
        
        $ratio = JJ_Common_Utils::get_contrast_ratio( $foreground, $background );
        
        return array(
            'contrast_ratio' => round( $ratio, 2 ),
            'aa_normal'      => $ratio >= 4.5,    // WCAG AA 일반 텍스트
            'aa_large'       => $ratio >= 3.0,    // WCAG AA 큰 텍스트
            'aaa_normal'     => $ratio >= 7.0,    // WCAG AAA 일반 텍스트
            'aaa_large'      => $ratio >= 4.5,    // WCAG AAA 큰 텍스트
            'grade'          => $this->get_contrast_grade( $ratio ),
        );
    }

    /**
     * 대비 등급 반환
     * @param float $ratio
     * @return string
     */
    private function get_contrast_grade( $ratio ) {
        if ( $ratio >= 7.0 ) return 'AAA';
        if ( $ratio >= 4.5 ) return 'AA';
        if ( $ratio >= 3.0 ) return 'AA Large';
        return 'Fail';
    }

    /**
     * HEX를 HSL로 변환
     * @param string $hex
     * @return array|false
     */
    private function hex_to_hsl( $hex ) {
        if ( ! class_exists( 'JJ_Common_Utils' ) ) {
            return false;
        }
        
        $rgb = JJ_Common_Utils::hex_to_rgb( $hex );
        if ( ! $rgb ) {
            return false;
        }
        
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;
        
        $max = max( $r, $g, $b );
        $min = min( $r, $g, $b );
        $l = ( $max + $min ) / 2;
        
        if ( $max === $min ) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min );
            
            switch ( $max ) {
                case $r:
                    $h = ( $g - $b ) / $d + ( $g < $b ? 6 : 0 );
                    break;
                case $g:
                    $h = ( $b - $r ) / $d + 2;
                    break;
                case $b:
                    $h = ( $r - $g ) / $d + 4;
                    break;
            }
            
            $h *= 60;
        }
        
        return array(
            'h' => round( $h ),
            's' => round( $s * 100 ),
            'l' => round( $l * 100 ),
        );
    }

    /**
     * HSL을 HEX로 변환
     * @param int $h Hue (0-360)
     * @param int $s Saturation (0-100)
     * @param int $l Lightness (0-100)
     * @return string
     */
    private function hsl_to_hex( $h, $s, $l ) {
        $s = $s / 100;
        $l = $l / 100;
        
        $c = ( 1 - abs( 2 * $l - 1 ) ) * $s;
        $x = $c * ( 1 - abs( fmod( $h / 60, 2 ) - 1 ) );
        $m = $l - $c / 2;
        
        if ( $h < 60 ) {
            $r = $c; $g = $x; $b = 0;
        } elseif ( $h < 120 ) {
            $r = $x; $g = $c; $b = 0;
        } elseif ( $h < 180 ) {
            $r = 0; $g = $c; $b = $x;
        } elseif ( $h < 240 ) {
            $r = 0; $g = $x; $b = $c;
        } elseif ( $h < 300 ) {
            $r = $x; $g = 0; $b = $c;
        } else {
            $r = $c; $g = 0; $b = $x;
        }
        
        $r = round( ( $r + $m ) * 255 );
        $g = round( ( $g + $m ) * 255 );
        $b = round( ( $b + $m ) * 255 );
        
        return sprintf( '#%02x%02x%02x', $r, $g, $b );
    }

    /**
     * REST API 라우트 등록
     */
    public function register_rest_routes() {
        register_rest_route( 'jj-style-guide/v1', '/palettes', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'rest_get_palettes' ),
                'permission_callback' => '__return_true',
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'rest_save_palette' ),
                'permission_callback' => array( $this, 'rest_permission_check' ),
            ),
        ) );
    }

    /**
     * REST 권한 체크
     */
    public function rest_permission_check() {
        return current_user_can( 'manage_options' );
    }

    /**
     * REST: 팔레트 목록
     */
    public function rest_get_palettes() {
        return rest_ensure_response( $this->get_palettes() );
    }

    /**
     * REST: 팔레트 저장
     */
    public function rest_save_palette( $request ) {
        $params = $request->get_json_params();
        $id = isset( $params['id'] ) ? sanitize_key( $params['id'] ) : '';
        $data = isset( $params['data'] ) ? $params['data'] : array();
        
        if ( empty( $id ) ) {
            return new WP_Error( 'invalid_id', __( '유효하지 않은 팔레트 ID', 'acf-css-really-simple-style-management-center' ), array( 'status' => 400 ) );
        }
        
        $result = $this->save_palette( $id, $data );
        
        return rest_ensure_response( array( 'success' => $result ) );
    }

    /**
     * AJAX: 팔레트 저장
     */
    public function ajax_save_palette() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $id = isset( $_POST['palette_id'] ) ? sanitize_key( $_POST['palette_id'] ) : '';
        $data = isset( $_POST['palette_data'] ) ? json_decode( stripslashes( $_POST['palette_data'] ), true ) : array();
        
        if ( empty( $id ) ) {
            wp_send_json_error( __( '팔레트 ID가 필요합니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $result = $this->save_palette( $id, $data );
        
        if ( $result ) {
            wp_send_json_success( __( '팔레트가 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) );
        } else {
            wp_send_json_error( __( '저장에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
    }

    /**
     * AJAX: 팔레트 목록
     */
    public function ajax_get_palettes() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        wp_send_json_success( $this->get_palettes() );
    }

    /**
     * AJAX: 팔레트 삭제
     */
    public function ajax_delete_palette() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $id = isset( $_POST['palette_id'] ) ? sanitize_key( $_POST['palette_id'] ) : '';
        $result = $this->delete_palette( $id );
        
        if ( $result ) {
            wp_send_json_success( __( '팔레트가 삭제되었습니다.', 'acf-css-really-simple-style-management-center' ) );
        } else {
            wp_send_json_error( __( '삭제에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
    }

    /**
     * AJAX: 팔레트 자동 생성
     */
    public function ajax_generate_palette() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        $base_color = isset( $_POST['base_color'] ) ? sanitize_hex_color( $_POST['base_color'] ) : '#3b82f6';
        $harmony = isset( $_POST['harmony'] ) ? sanitize_key( $_POST['harmony'] ) : 'complementary';
        
        $harmony_colors = $this->generate_harmony( $base_color, $harmony );
        $scale = $this->generate_scale( $base_color );
        
        wp_send_json_success( array(
            'harmony' => $harmony_colors,
            'scale'   => $scale,
        ) );
    }
}

// 인스턴스 초기화
add_action( 'init', function() {
    JJ_Palette_Manager::instance();
}, 5 );

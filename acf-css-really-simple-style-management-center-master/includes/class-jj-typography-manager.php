<?php
/**
 * JJ Typography Manager - 타이포그래피 관리 클래스
 * 
 * 폰트, 텍스트 스타일, 타이포그래피 스케일을 관리합니다.
 * 
 * @package ACF_CSS_Style_Guide
 * @version 22.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class JJ_Typography_Manager
 */
class JJ_Typography_Manager {

    /**
     * 싱글톤 인스턴스
     * @var JJ_Typography_Manager|null
     */
    private static $instance = null;

    /**
     * 옵션 키
     * @var string
     */
    private $option_key = 'jj_style_guide_typography';

    /**
     * 기본 타이포그래피 설정
     * @var array
     */
    private $defaults = array();

    /**
     * 생성자
     */
    private function __construct() {
        $this->defaults = $this->get_defaults();
        $this->init_hooks();
    }

    /**
     * 싱글톤 인스턴스 반환
     * @return JJ_Typography_Manager
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
        add_action( 'wp_ajax_jj_save_typography', array( $this, 'ajax_save_typography' ) );
        add_action( 'wp_ajax_jj_get_typography', array( $this, 'ajax_get_typography' ) );
        add_action( 'wp_ajax_jj_reset_typography', array( $this, 'ajax_reset_typography' ) );
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
    }

    /**
     * 기본값 정의
     * @return array
     */
    public function get_defaults() {
        return array(
            // 폰트 패밀리
            'font_families' => array(
                'primary' => array(
                    'name'  => __( '주요 폰트', 'acf-css-really-simple-style-management-center' ),
                    'stack' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                ),
                'secondary' => array(
                    'name'  => __( '보조 폰트', 'acf-css-really-simple-style-management-center' ),
                    'stack' => 'Georgia, Cambria, "Times New Roman", Times, serif',
                ),
                'mono' => array(
                    'name'  => __( '고정폭 폰트', 'acf-css-really-simple-style-management-center' ),
                    'stack' => 'ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas, "Liberation Mono", monospace',
                ),
            ),
            // 폰트 크기 스케일
            'font_sizes' => array(
                'xs'   => array( 'size' => '0.75rem',  'line_height' => '1rem' ),
                'sm'   => array( 'size' => '0.875rem', 'line_height' => '1.25rem' ),
                'base' => array( 'size' => '1rem',     'line_height' => '1.5rem' ),
                'lg'   => array( 'size' => '1.125rem', 'line_height' => '1.75rem' ),
                'xl'   => array( 'size' => '1.25rem',  'line_height' => '1.75rem' ),
                '2xl'  => array( 'size' => '1.5rem',   'line_height' => '2rem' ),
                '3xl'  => array( 'size' => '1.875rem', 'line_height' => '2.25rem' ),
                '4xl'  => array( 'size' => '2.25rem',  'line_height' => '2.5rem' ),
                '5xl'  => array( 'size' => '3rem',     'line_height' => '1' ),
                '6xl'  => array( 'size' => '3.75rem',  'line_height' => '1' ),
            ),
            // 폰트 굵기
            'font_weights' => array(
                'thin'       => 100,
                'extralight' => 200,
                'light'      => 300,
                'normal'     => 400,
                'medium'     => 500,
                'semibold'   => 600,
                'bold'       => 700,
                'extrabold'  => 800,
                'black'      => 900,
            ),
            // 행간
            'line_heights' => array(
                'none'    => '1',
                'tight'   => '1.25',
                'snug'    => '1.375',
                'normal'  => '1.5',
                'relaxed' => '1.625',
                'loose'   => '2',
            ),
            // 자간
            'letter_spacings' => array(
                'tighter' => '-0.05em',
                'tight'   => '-0.025em',
                'normal'  => '0em',
                'wide'    => '0.025em',
                'wider'   => '0.05em',
                'widest'  => '0.1em',
            ),
            // 제목 스타일
            'headings' => array(
                'h1' => array(
                    'font_size'      => '2.25rem',
                    'font_weight'    => '800',
                    'line_height'    => '1.2',
                    'letter_spacing' => '-0.025em',
                    'margin_bottom'  => '1rem',
                ),
                'h2' => array(
                    'font_size'      => '1.875rem',
                    'font_weight'    => '700',
                    'line_height'    => '1.25',
                    'letter_spacing' => '-0.025em',
                    'margin_bottom'  => '0.875rem',
                ),
                'h3' => array(
                    'font_size'      => '1.5rem',
                    'font_weight'    => '600',
                    'line_height'    => '1.3',
                    'letter_spacing' => '0',
                    'margin_bottom'  => '0.75rem',
                ),
                'h4' => array(
                    'font_size'      => '1.25rem',
                    'font_weight'    => '600',
                    'line_height'    => '1.4',
                    'letter_spacing' => '0',
                    'margin_bottom'  => '0.625rem',
                ),
                'h5' => array(
                    'font_size'      => '1.125rem',
                    'font_weight'    => '600',
                    'line_height'    => '1.5',
                    'letter_spacing' => '0',
                    'margin_bottom'  => '0.5rem',
                ),
                'h6' => array(
                    'font_size'      => '1rem',
                    'font_weight'    => '600',
                    'line_height'    => '1.5',
                    'letter_spacing' => '0',
                    'margin_bottom'  => '0.5rem',
                ),
            ),
            // 본문 스타일
            'body' => array(
                'font_family'    => 'primary',
                'font_size'      => '1rem',
                'font_weight'    => '400',
                'line_height'    => '1.75',
                'letter_spacing' => '0',
            ),
        );
    }

    /**
     * 타이포그래피 설정 가져오기
     * @return array
     */
    public function get_typography() {
        $saved = get_option( $this->option_key, array() );
        return wp_parse_args( $saved, $this->defaults );
    }

    /**
     * 타이포그래피 설정 저장
     * @param array $settings
     * @return bool
     */
    public function save_typography( $settings ) {
        $merged = wp_parse_args( $settings, $this->defaults );
        return update_option( $this->option_key, $merged );
    }

    /**
     * 타이포그래피 설정 초기화
     * @return bool
     */
    public function reset_typography() {
        return update_option( $this->option_key, $this->defaults );
    }

    /**
     * CSS 변수 생성
     * @return string
     */
    public function generate_css_variables() {
        $settings = $this->get_typography();
        $css = '';

        // 폰트 패밀리
        if ( ! empty( $settings['font_families'] ) ) {
            foreach ( $settings['font_families'] as $key => $font ) {
                $css .= "  --jj-font-{$key}: {$font['stack']};\n";
            }
        }

        // 폰트 크기
        if ( ! empty( $settings['font_sizes'] ) ) {
            foreach ( $settings['font_sizes'] as $key => $size ) {
                $css .= "  --jj-text-{$key}: {$size['size']};\n";
                $css .= "  --jj-leading-{$key}: {$size['line_height']};\n";
            }
        }

        // 폰트 굵기
        if ( ! empty( $settings['font_weights'] ) ) {
            foreach ( $settings['font_weights'] as $key => $weight ) {
                $css .= "  --jj-font-{$key}: {$weight};\n";
            }
        }

        // 행간
        if ( ! empty( $settings['line_heights'] ) ) {
            foreach ( $settings['line_heights'] as $key => $height ) {
                $css .= "  --jj-leading-{$key}: {$height};\n";
            }
        }

        // 자간
        if ( ! empty( $settings['letter_spacings'] ) ) {
            foreach ( $settings['letter_spacings'] as $key => $spacing ) {
                $css .= "  --jj-tracking-{$key}: {$spacing};\n";
            }
        }

        return $css;
    }

    /**
     * 제목 스타일 CSS 생성
     * @return string
     */
    public function generate_heading_styles() {
        $settings = $this->get_typography();
        $css = '';

        if ( ! empty( $settings['headings'] ) ) {
            foreach ( $settings['headings'] as $tag => $styles ) {
                $css .= "{$tag} {\n";
                $css .= "  font-size: {$styles['font_size']};\n";
                $css .= "  font-weight: {$styles['font_weight']};\n";
                $css .= "  line-height: {$styles['line_height']};\n";
                $css .= "  letter-spacing: {$styles['letter_spacing']};\n";
                $css .= "  margin-bottom: {$styles['margin_bottom']};\n";
                $css .= "}\n\n";
            }
        }

        return $css;
    }

    /**
     * 모듈러 스케일 계산
     * @param float $base 기본 크기 (px)
     * @param float $ratio 스케일 비율
     * @param int $steps 단계 수
     * @return array
     */
    public function calculate_modular_scale( $base = 16, $ratio = 1.25, $steps = 10 ) {
        $scale = array();
        
        for ( $i = -2; $i <= $steps; $i++ ) {
            $size = $base * pow( $ratio, $i );
            $scale[ $i ] = array(
                'px'  => round( $size, 2 ) . 'px',
                'rem' => round( $size / $base, 4 ) . 'rem',
            );
        }
        
        return $scale;
    }

    /**
     * 프리셋 스케일 가져오기
     * @param string $preset_name
     * @return array
     */
    public function get_preset_scale( $preset_name = 'default' ) {
        $presets = array(
            'minor-second'  => 1.067,
            'major-second'  => 1.125,
            'minor-third'   => 1.200,
            'major-third'   => 1.250,
            'perfect-fourth'=> 1.333,
            'augmented-fourth' => 1.414,
            'perfect-fifth' => 1.500,
            'golden-ratio'  => 1.618,
            'default'       => 1.250, // Major Third
        );

        $ratio = isset( $presets[ $preset_name ] ) ? $presets[ $preset_name ] : $presets['default'];
        return $this->calculate_modular_scale( 16, $ratio );
    }

    /**
     * REST API 라우트 등록
     */
    public function register_rest_routes() {
        register_rest_route( 'jj-style-guide/v1', '/typography', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'rest_get_typography' ),
                'permission_callback' => '__return_true',
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'rest_save_typography' ),
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
     * REST: 타이포그래피 가져오기
     */
    public function rest_get_typography() {
        return rest_ensure_response( $this->get_typography() );
    }

    /**
     * REST: 타이포그래피 저장
     */
    public function rest_save_typography( $request ) {
        $params = $request->get_json_params();
        $result = $this->save_typography( $params );
        return rest_ensure_response( array( 'success' => $result ) );
    }

    /**
     * AJAX: 타이포그래피 저장
     */
    public function ajax_save_typography() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $settings = isset( $_POST['settings'] ) ? json_decode( stripslashes( $_POST['settings'] ), true ) : array();
        $result = $this->save_typography( $settings );
        
        if ( $result ) {
            wp_send_json_success( __( '타이포그래피 설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) );
        } else {
            wp_send_json_error( __( '저장에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
    }

    /**
     * AJAX: 타이포그래피 가져오기
     */
    public function ajax_get_typography() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        wp_send_json_success( $this->get_typography() );
    }

    /**
     * AJAX: 타이포그래피 초기화
     */
    public function ajax_reset_typography() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $result = $this->reset_typography();
        
        if ( $result ) {
            wp_send_json_success( array(
                'message'  => __( '타이포그래피 설정이 초기화되었습니다.', 'acf-css-really-simple-style-management-center' ),
                'settings' => $this->defaults,
            ) );
        } else {
            wp_send_json_error( __( '초기화에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
    }
}

// 인스턴스 초기화
add_action( 'init', function() {
    JJ_Typography_Manager::instance();
}, 5 );

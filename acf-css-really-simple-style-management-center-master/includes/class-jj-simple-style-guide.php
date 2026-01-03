<?php
/**
 * JJ Simple Style Guide - 메인 스타일 가이드 클래스
 * 
 * WordPress 스타일 관리의 핵심 클래스입니다.
 * 옵션 관리, 스타일 적용, 프론트엔드/백엔드 통합을 담당합니다.
 * 
 * @package ACF_CSS_Style_Guide
 * @version 22.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class JJ_Simple_Style_Guide
 * 
 * 스타일 가이드의 메인 컨트롤러 클래스
 */
class JJ_Simple_Style_Guide {

    /**
     * 싱글톤 인스턴스
     * @var JJ_Simple_Style_Guide|null
     */
    private static $instance = null;

    /**
     * 플러그인 옵션
     * @var array
     */
    private $options = array();

    /**
     * 옵션 키
     * @var string
     */
    private $option_key = 'jj_style_guide_options';

    /**
     * 생성자
     */
    public function __construct() {
        $this->load_options();
        $this->init_hooks();
    }

    /**
     * 싱글톤 인스턴스 반환
     * @return JJ_Simple_Style_Guide
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 옵션 로드
     */
    private function load_options() {
        $this->options = get_option( $this->option_key, $this->get_default_options() );
    }

    /**
     * 기본 옵션 반환
     * @return array
     */
    public function get_default_options() {
        return array(
            // 색상 팔레트
            'colors' => array(
                'primary'    => '#3b82f6',
                'secondary'  => '#64748b',
                'accent'     => '#f59e0b',
                'success'    => '#22c55e',
                'warning'    => '#eab308',
                'error'      => '#ef4444',
                'info'       => '#06b6d4',
                'background' => '#ffffff',
                'foreground' => '#1e293b',
                'muted'      => '#f1f5f9',
            ),
            // 타이포그래피
            'typography' => array(
                'font_family_primary'   => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                'font_family_secondary' => 'Georgia, "Times New Roman", serif',
                'font_family_mono'      => 'ui-monospace, SFMono-Regular, "SF Mono", Menlo, monospace',
                'font_size_base'        => '16px',
                'font_size_sm'          => '14px',
                'font_size_lg'          => '18px',
                'font_size_xl'          => '20px',
                'font_size_2xl'         => '24px',
                'font_size_3xl'         => '30px',
                'line_height_base'      => '1.5',
                'line_height_tight'     => '1.25',
                'line_height_loose'     => '1.75',
            ),
            // 간격
            'spacing' => array(
                'base'   => '16px',
                'xs'     => '4px',
                'sm'     => '8px',
                'md'     => '16px',
                'lg'     => '24px',
                'xl'     => '32px',
                '2xl'    => '48px',
            ),
            // 테두리
            'borders' => array(
                'radius_none' => '0',
                'radius_sm'   => '4px',
                'radius_md'   => '8px',
                'radius_lg'   => '12px',
                'radius_full' => '9999px',
                'width'       => '1px',
                'color'       => '#e2e8f0',
            ),
            // 그림자
            'shadows' => array(
                'sm'  => '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                'md'  => '0 4px 6px -1px rgb(0 0 0 / 0.1)',
                'lg'  => '0 10px 15px -3px rgb(0 0 0 / 0.1)',
                'xl'  => '0 20px 25px -5px rgb(0 0 0 / 0.1)',
            ),
            // 버튼 스타일
            'buttons' => array(
                'padding_x'      => '16px',
                'padding_y'      => '8px',
                'border_radius'  => '8px',
                'font_weight'    => '500',
                'transition'     => 'all 0.2s ease',
            ),
            // 폼 스타일
            'forms' => array(
                'input_padding'       => '10px 12px',
                'input_border_radius' => '6px',
                'input_border_color'  => '#d1d5db',
                'input_focus_color'   => '#3b82f6',
            ),
            // 설정
            'settings' => array(
                'apply_to_frontend'   => true,
                'apply_to_admin'      => false,
                'apply_to_customizer' => true,
                'css_output_method'   => 'inline', // inline, file, both
                'cache_enabled'       => true,
            ),
        );
    }

    /**
     * 훅 초기화
     */
    private function init_hooks() {
        // 프론트엔드 스타일 적용
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ), 5 );
        
        // 관리자 스타일 적용 (옵션에 따라)
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ), 5 );
        
        // 커스터마이저 스타일
        add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_styles' ) );
        
        // REST API 엔드포인트
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
        
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_style_guide_save', array( $this, 'ajax_save_options' ) );
        add_action( 'wp_ajax_jj_style_guide_reset', array( $this, 'ajax_reset_options' ) );
        add_action( 'wp_ajax_jj_style_guide_export', array( $this, 'ajax_export_options' ) );
        add_action( 'wp_ajax_jj_style_guide_import', array( $this, 'ajax_import_options' ) );
    }

    /**
     * 프론트엔드 스타일 enqueue
     */
    public function enqueue_frontend_styles() {
        if ( empty( $this->options['settings']['apply_to_frontend'] ) ) {
            return;
        }
        
        $css = $this->generate_css_variables();
        wp_add_inline_style( 'wp-block-library', $css );
    }

    /**
     * 관리자 스타일 enqueue
     */
    public function enqueue_admin_styles() {
        if ( empty( $this->options['settings']['apply_to_admin'] ) ) {
            return;
        }
        
        $css = $this->generate_css_variables();
        wp_add_inline_style( 'common', $css );
    }

    /**
     * 커스터마이저 스타일 enqueue
     */
    public function enqueue_customizer_styles() {
        if ( empty( $this->options['settings']['apply_to_customizer'] ) ) {
            return;
        }
        
        $css = $this->generate_css_variables();
        wp_add_inline_style( 'customize-controls', $css );
    }

    /**
     * CSS 변수 생성
     * @return string
     */
    public function generate_css_variables() {
        $css = ":root {\n";
        
        // 색상
        if ( ! empty( $this->options['colors'] ) ) {
            foreach ( $this->options['colors'] as $key => $value ) {
                $css .= "  --jj-color-{$key}: {$value};\n";
            }
        }
        
        // 타이포그래피
        if ( ! empty( $this->options['typography'] ) ) {
            foreach ( $this->options['typography'] as $key => $value ) {
                $var_name = str_replace( '_', '-', $key );
                $css .= "  --jj-{$var_name}: {$value};\n";
            }
        }
        
        // 간격
        if ( ! empty( $this->options['spacing'] ) ) {
            foreach ( $this->options['spacing'] as $key => $value ) {
                $css .= "  --jj-spacing-{$key}: {$value};\n";
            }
        }
        
        // 테두리
        if ( ! empty( $this->options['borders'] ) ) {
            foreach ( $this->options['borders'] as $key => $value ) {
                $var_name = str_replace( '_', '-', $key );
                $css .= "  --jj-border-{$var_name}: {$value};\n";
            }
        }
        
        // 그림자
        if ( ! empty( $this->options['shadows'] ) ) {
            foreach ( $this->options['shadows'] as $key => $value ) {
                $css .= "  --jj-shadow-{$key}: {$value};\n";
            }
        }
        
        $css .= "}\n";
        
        return $css;
    }

    /**
     * 옵션 가져오기
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get_option( $key = '', $default = null ) {
        if ( empty( $key ) ) {
            return $this->options;
        }
        
        // 점 표기법 지원 (예: 'colors.primary')
        $keys = explode( '.', $key );
        $value = $this->options;
        
        foreach ( $keys as $k ) {
            if ( isset( $value[ $k ] ) ) {
                $value = $value[ $k ];
            } else {
                return $default;
            }
        }
        
        return $value;
    }

    /**
     * 옵션 설정
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set_option( $key, $value ) {
        $keys = explode( '.', $key );
        $options = &$this->options;
        
        foreach ( $keys as $i => $k ) {
            if ( $i === count( $keys ) - 1 ) {
                $options[ $k ] = $value;
            } else {
                if ( ! isset( $options[ $k ] ) || ! is_array( $options[ $k ] ) ) {
                    $options[ $k ] = array();
                }
                $options = &$options[ $k ];
            }
        }
        
        return update_option( $this->option_key, $this->options );
    }

    /**
     * 전체 옵션 저장
     * @param array $options
     * @return bool
     */
    public function save_options( $options ) {
        $this->options = wp_parse_args( $options, $this->get_default_options() );
        return update_option( $this->option_key, $this->options );
    }

    /**
     * 옵션 초기화
     * @return bool
     */
    public function reset_options() {
        $this->options = $this->get_default_options();
        return update_option( $this->option_key, $this->options );
    }

    /**
     * REST API 라우트 등록
     */
    public function register_rest_routes() {
        register_rest_route( 'jj-style-guide/v1', '/options', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'rest_get_options' ),
                'permission_callback' => array( $this, 'rest_permission_check' ),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'rest_save_options' ),
                'permission_callback' => array( $this, 'rest_permission_check' ),
            ),
        ) );
        
        register_rest_route( 'jj-style-guide/v1', '/css', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'rest_get_css' ),
            'permission_callback' => '__return_true',
        ) );
    }

    /**
     * REST 권한 체크
     * @return bool
     */
    public function rest_permission_check() {
        return current_user_can( 'manage_options' );
    }

    /**
     * REST: 옵션 가져오기
     * @return WP_REST_Response
     */
    public function rest_get_options() {
        return rest_ensure_response( $this->options );
    }

    /**
     * REST: 옵션 저장
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function rest_save_options( $request ) {
        $options = $request->get_json_params();
        $result = $this->save_options( $options );
        
        return rest_ensure_response( array(
            'success' => $result,
            'options' => $this->options,
        ) );
    }

    /**
     * REST: CSS 가져오기
     * @return WP_REST_Response
     */
    public function rest_get_css() {
        return rest_ensure_response( array(
            'css' => $this->generate_css_variables(),
        ) );
    }

    /**
     * AJAX: 옵션 저장
     */
    public function ajax_save_options() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $options = isset( $_POST['options'] ) ? json_decode( stripslashes( $_POST['options'] ), true ) : array();
        $result = $this->save_options( $options );
        
        if ( $result ) {
            wp_send_json_success( array(
                'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ),
                'options' => $this->options,
            ) );
        } else {
            wp_send_json_error( __( '저장에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
    }

    /**
     * AJAX: 옵션 초기화
     */
    public function ajax_reset_options() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $result = $this->reset_options();
        
        if ( $result ) {
            wp_send_json_success( array(
                'message' => __( '설정이 초기화되었습니다.', 'acf-css-really-simple-style-management-center' ),
                'options' => $this->options,
            ) );
        } else {
            wp_send_json_error( __( '초기화에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
    }

    /**
     * AJAX: 옵션 내보내기
     */
    public function ajax_export_options() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        wp_send_json_success( array(
            'data' => $this->options,
            'filename' => 'jj-style-guide-export-' . date( 'Y-m-d' ) . '.json',
        ) );
    }

    /**
     * AJAX: 옵션 가져오기
     */
    public function ajax_import_options() {
        check_ajax_referer( 'jj_style_guide_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $import_data = isset( $_POST['data'] ) ? json_decode( stripslashes( $_POST['data'] ), true ) : null;
        
        if ( ! $import_data || ! is_array( $import_data ) ) {
            wp_send_json_error( __( '유효하지 않은 데이터입니다.', 'acf-css-really-simple-style-management-center' ) );
        }
        
        $result = $this->save_options( $import_data );
        
        if ( $result ) {
            wp_send_json_success( array(
                'message' => __( '설정을 가져왔습니다.', 'acf-css-really-simple-style-management-center' ),
                'options' => $this->options,
            ) );
        } else {
            wp_send_json_error( __( '가져오기에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) );
        }
    }

    /**
     * 색상 팔레트 가져오기
     * @return array
     */
    public function get_colors() {
        return isset( $this->options['colors'] ) ? $this->options['colors'] : array();
    }

    /**
     * 타이포그래피 설정 가져오기
     * @return array
     */
    public function get_typography() {
        return isset( $this->options['typography'] ) ? $this->options['typography'] : array();
    }

    /**
     * 간격 설정 가져오기
     * @return array
     */
    public function get_spacing() {
        return isset( $this->options['spacing'] ) ? $this->options['spacing'] : array();
    }

    /**
     * 버튼 스타일 가져오기
     * @return array
     */
    public function get_button_styles() {
        return isset( $this->options['buttons'] ) ? $this->options['buttons'] : array();
    }

    /**
     * 폼 스타일 가져오기
     * @return array
     */
    public function get_form_styles() {
        return isset( $this->options['forms'] ) ? $this->options['forms'] : array();
    }
}

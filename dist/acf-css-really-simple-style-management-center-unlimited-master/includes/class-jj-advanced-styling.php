<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.3] Advanced Styling
 * 
 * 고급 스타일링 기능
 * - CSS 변수 고급 관리
 * - 미디어 쿼리 관리
 * - 반응형 브레이크포인트 관리
 * 
 * @since 9.3.0
 */
class JJ_Advanced_Styling {

    private static $instance = null;
    private $option_key = 'jj_style_guide_advanced_styling';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_save_css_variables', array( $this, 'ajax_save_css_variables' ) );
        add_action( 'wp_ajax_jj_save_breakpoints', array( $this, 'ajax_save_breakpoints' ) );
        add_action( 'wp_ajax_jj_calculate_css_variable', array( $this, 'ajax_calculate_css_variable' ) );
    }

    /**
     * 에셋 로드
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'jj-admin-center' ) === false && 
             strpos( $hook, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'jj-advanced-styling',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-advanced-styling.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '9.3.0',
            true
        );

        wp_localize_script(
            'jj-advanced-styling',
            'jjAdvancedStyling',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'jj_advanced_styling_action' ),
                'default_breakpoints' => $this->get_default_breakpoints(),
                'strings'  => array(
                    'saved' => __( '저장되었습니다.', 'acf-css-really-simple-style-management-center' ),
                ),
            )
        );
    }

    /**
     * 기본 브레이크포인트 가져오기
     */
    public function get_default_breakpoints() {
        return array(
            // [v10.6.0] 타이포그래피/반응형을 위한 세분화 기본 브레이크포인트
            // - 기존 키(desktop/tablet/mobile)는 유지(하위 호환)
            // - 추가 키: laptop/phablet/phone_small/qhd/uhd
            'phone_small' => array( 'max' => 374, 'label' => __( '구형 스마트폰(작은 화면)', 'acf-css-really-simple-style-management-center' ) ),
            'mobile'      => array( 'max' => 479, 'label' => __( '모바일(스마트폰)', 'acf-css-really-simple-style-management-center' ) ),
            'phablet'     => array( 'max' => 767, 'label' => __( '패블릿/큰 폰', 'acf-css-really-simple-style-management-center' ) ),
            'tablet'      => array( 'max' => 1024, 'label' => __( '태블릿', 'acf-css-really-simple-style-management-center' ) ),
            'laptop'      => array( 'max' => 1279, 'label' => __( '랩탑', 'acf-css-really-simple-style-management-center' ) ),
            // desktop은 기본(HD/FHD 포함)으로 두고, 필요 시 QHD/UHD에서 별도 오버라이드
            'desktop'     => array( 'min' => 1280, 'label' => __( '데스크톱(기본/HD+)', 'acf-css-really-simple-style-management-center' ) ),
            'desktop_qhd' => array( 'min' => 2560, 'label' => __( '데스크톱(QHD+)', 'acf-css-really-simple-style-management-center' ) ),
            'desktop_uhd' => array( 'min' => 3840, 'label' => __( '데스크톱(UHD+)', 'acf-css-really-simple-style-management-center' ) ),
            // [v10.6.1] 초고해상도 단계(4K/5K가 흔해진 환경) — 최대 8K까지
            'desktop_5k'  => array( 'min' => 5120, 'label' => __( '데스크톱(5K+)', 'acf-css-really-simple-style-management-center' ) ),
            'desktop_8k'  => array( 'min' => 7680, 'label' => __( '데스크톱(8K+)', 'acf-css-really-simple-style-management-center' ) ),
        );
    }

    /**
     * 브레이크포인트 가져오기
     */
    public function get_breakpoints() {
        $defaults = $this->get_default_breakpoints();
        $stored = get_option( $this->option_key . '_breakpoints', array() );

        if ( empty( $stored ) || ! is_array( $stored ) ) {
            return $defaults;
        }

        // 저장된 값은 유지하되, 새로 추가된 기본 브레이크포인트(예: 5K/8K)는 자동으로 합류
        $merged = array_merge( $defaults, $stored );

        // 라벨이 비어 있으면 기본 라벨로 보정
        foreach ( $merged as $k => $bp ) {
            if ( ! is_array( $bp ) ) {
                continue;
            }
            if ( empty( $bp['label'] ) && isset( $defaults[ $k ]['label'] ) ) {
                $merged[ $k ]['label'] = $defaults[ $k ]['label'];
            }
        }

        return $merged;
    }

    /**
     * 브레이크포인트 저장
     */
    public function save_breakpoints( $breakpoints ) {
        return update_option( $this->option_key . '_breakpoints', $breakpoints );
    }

    /**
     * CSS 변수 그룹 가져오기
     */
    public function get_css_variable_groups() {
        return get_option( $this->option_key . '_css_variables', array() );
    }

    /**
     * CSS 변수 그룹 저장
     */
    public function save_css_variable_groups( $groups ) {
        return update_option( $this->option_key . '_css_variables', $groups );
    }

    /**
     * CSS 변수 의존성 계산
     */
    public function calculate_variable_dependencies( $variable_name, $value ) {
        $dependencies = array();
        
        // CSS 변수 참조 찾기 (예: var(--primary-color))
        if ( preg_match_all( '/var\(--([a-zA-Z0-9_-]+)\)/', $value, $matches ) ) {
            $dependencies = $matches[1];
        }

        return $dependencies;
    }

    /**
     * 동적 CSS 변수 계산
     */
    public function calculate_dynamic_variable( $formula, $variables = array() ) {
        // 간단한 수식 계산 (예: "calc(var(--base-size) * 1.5)")
        // 실제 구현은 더 복잡한 파서가 필요할 수 있음
        
        if ( empty( $variables ) ) {
            $variables = $this->get_all_css_variables();
        }

        // var(--variable-name) 치환
        $formula = preg_replace_callback( '/var\(--([a-zA-Z0-9_-]+)\)/', function( $matches ) use ( $variables ) {
            $var_name = $matches[1];
            return isset( $variables[ $var_name ] ) ? $variables[ $var_name ] : $matches[0];
        }, $formula );

        // calc() 함수 처리 (간단한 버전)
        if ( strpos( $formula, 'calc(' ) !== false ) {
            // 실제로는 더 정교한 계산이 필요하지만, 여기서는 기본적인 처리만
            $formula = str_replace( array( 'calc(', ')' ), '', $formula );
        }

        return $formula;
    }

    /**
     * 모든 CSS 변수 가져오기
     */
    private function get_all_css_variables() {
        $groups = $this->get_css_variable_groups();
        $variables = array();

        foreach ( $groups as $group ) {
            if ( isset( $group['variables'] ) && is_array( $group['variables'] ) ) {
                foreach ( $group['variables'] as $var ) {
                    if ( isset( $var['name'] ) && isset( $var['value'] ) ) {
                        $variables[ $var['name'] ] = $var['value'];
                    }
                }
            }
        }

        return $variables;
    }

    /**
     * 미디어 쿼리 CSS 생성
     */
    public function generate_media_query_css( $breakpoint_key, $styles ) {
        $breakpoints = $this->get_breakpoints();
        
        if ( ! isset( $breakpoints[ $breakpoint_key ] ) ) {
            return '';
        }

        $breakpoint = $breakpoints[ $breakpoint_key ];
        $media_query = '@media ';

        if ( isset( $breakpoint['min'] ) && isset( $breakpoint['max'] ) ) {
            $media_query .= sprintf( '(min-width: %dpx) and (max-width: %dpx)', $breakpoint['min'], $breakpoint['max'] );
        } elseif ( isset( $breakpoint['min'] ) ) {
            $media_query .= sprintf( '(min-width: %dpx)', $breakpoint['min'] );
        } elseif ( isset( $breakpoint['max'] ) ) {
            $media_query .= sprintf( '(max-width: %dpx)', $breakpoint['max'] );
        } else {
            return '';
        }

        $css = $media_query . " {\n";
        foreach ( $styles as $selector => $properties ) {
            $css .= "  " . $selector . " {\n";
            foreach ( $properties as $property => $value ) {
                $css .= "    " . $property . ": " . $value . ";\n";
            }
            $css .= "  }\n";
        }
        $css .= "}\n";

        return $css;
    }

    /**
     * AJAX: CSS 변수 저장
     */
    public function ajax_save_css_variables() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_advanced_styling_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $groups = isset( $_POST['groups'] ) ? json_decode( stripslashes( $_POST['groups'] ), true ) : array();

        if ( ! is_array( $groups ) ) {
            wp_send_json_error( array( 'message' => __( '잘못된 데이터 형식입니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $this->save_css_variable_groups( $groups );

        wp_send_json_success( array(
            'message' => __( 'CSS 변수가 저장되었습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }

    /**
     * AJAX: 브레이크포인트 저장
     */
    public function ajax_save_breakpoints() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_advanced_styling_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $breakpoints = isset( $_POST['breakpoints'] ) ? json_decode( stripslashes( $_POST['breakpoints'] ), true ) : array();

        if ( ! is_array( $breakpoints ) ) {
            wp_send_json_error( array( 'message' => __( '잘못된 데이터 형식입니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $this->save_breakpoints( $breakpoints );

        wp_send_json_success( array(
            'message' => __( '브레이크포인트가 저장되었습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }

    /**
     * AJAX: CSS 변수 계산
     */
    public function ajax_calculate_css_variable() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_advanced_styling_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $formula = isset( $_POST['formula'] ) ? sanitize_text_field( $_POST['formula'] ) : '';
        $variables = isset( $_POST['variables'] ) ? json_decode( stripslashes( $_POST['variables'] ), true ) : array();

        if ( ! $formula ) {
            wp_send_json_error( array( 'message' => __( '수식이 필요합니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $result = $this->calculate_dynamic_variable( $formula, $variables );

        wp_send_json_success( array(
            'result' => $result,
        ) );
    }
}

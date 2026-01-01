<?php
/**
 * ACF Code Snippets Box - ACF CSS Bridge
 * 
 * ACF CSS 플러그인과의 연동
 *
 * @package ACF_Code_Snippets_Box
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ACF CSS Bridge 클래스
 */
class ACF_CSB_ACF_CSS_Bridge {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 싱글톤 인스턴스 반환
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 초기화
     */
    public function init() {
        // ACF CSS 활성화 확인
        if ( ! ACF_Code_Snippets_Box::is_acf_css_active() ) {
            return;
        }

        // CSS 변수 자동완성 데이터 제공
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_css_vars_autocomplete' ) );

        // 에디터에 ACF CSS 변수 힌트 추가
        add_filter( 'acf_csb_editor_hints', array( $this, 'add_css_var_hints' ) );
    }

    /**
     * CSS 변수 자동완성 스크립트 로드
     */
    public function enqueue_css_vars_autocomplete( $hook ) {
        global $post_type;

        if ( $post_type !== ACF_CSB_Post_Type::POST_TYPE ) {
            return;
        }

        $css_vars = $this->get_acf_css_variables();

        wp_localize_script( 'acf-csb-editor', 'acfCsbCssVars', array(
            'variables' => $css_vars,
            'enabled'   => true,
        ) );
    }

    /**
     * ACF CSS 변수 목록 가져오기
     */
    public function get_acf_css_variables() {
        $variables = array();

        // 색상 변수
        $colors = get_option( 'jj_style_guide_colors', array() );
        if ( ! empty( $colors ) ) {
            foreach ( $colors as $key => $color ) {
                $variables[] = array(
                    'name'  => '--jj-' . sanitize_title( $key ),
                    'value' => $color,
                    'type'  => 'color',
                );
            }
        }

        // 타이포그래피 변수
        $typography = get_option( 'jj_style_guide_typography', array() );
        if ( ! empty( $typography ) ) {
            if ( isset( $typography['font_family_primary'] ) ) {
                $variables[] = array(
                    'name'  => '--jj-font-family-primary',
                    'value' => $typography['font_family_primary'],
                    'type'  => 'font',
                );
            }
            if ( isset( $typography['font_family_secondary'] ) ) {
                $variables[] = array(
                    'name'  => '--jj-font-family-secondary',
                    'value' => $typography['font_family_secondary'],
                    'type'  => 'font',
                );
            }
            if ( isset( $typography['font_size_base'] ) ) {
                $variables[] = array(
                    'name'  => '--jj-font-size-base',
                    'value' => $typography['font_size_base'],
                    'type'  => 'size',
                );
            }
        }

        // 간격 변수
        $spacing = get_option( 'jj_style_guide_spacing', array() );
        if ( ! empty( $spacing ) ) {
            foreach ( $spacing as $key => $value ) {
                $variables[] = array(
                    'name'  => '--jj-spacing-' . sanitize_title( $key ),
                    'value' => $value,
                    'type'  => 'spacing',
                );
            }
        }

        // 기본 변수 (ACF CSS 옵션이 없을 경우)
        if ( empty( $variables ) ) {
            $variables = array(
                array( 'name' => '--jj-primary-color', 'value' => '#0073aa', 'type' => 'color' ),
                array( 'name' => '--jj-secondary-color', 'value' => '#23282d', 'type' => 'color' ),
                array( 'name' => '--jj-accent-color', 'value' => '#00a0d2', 'type' => 'color' ),
                array( 'name' => '--jj-text-color', 'value' => '#333333', 'type' => 'color' ),
                array( 'name' => '--jj-background-color', 'value' => '#ffffff', 'type' => 'color' ),
                array( 'name' => '--jj-font-family-primary', 'value' => 'system-ui, sans-serif', 'type' => 'font' ),
                array( 'name' => '--jj-font-family-secondary', 'value' => 'Georgia, serif', 'type' => 'font' ),
                array( 'name' => '--jj-font-size-base', 'value' => '16px', 'type' => 'size' ),
                array( 'name' => '--jj-line-height-base', 'value' => '1.6', 'type' => 'number' ),
                array( 'name' => '--jj-spacing-xs', 'value' => '4px', 'type' => 'spacing' ),
                array( 'name' => '--jj-spacing-sm', 'value' => '8px', 'type' => 'spacing' ),
                array( 'name' => '--jj-spacing-md', 'value' => '16px', 'type' => 'spacing' ),
                array( 'name' => '--jj-spacing-lg', 'value' => '24px', 'type' => 'spacing' ),
                array( 'name' => '--jj-spacing-xl', 'value' => '32px', 'type' => 'spacing' ),
                array( 'name' => '--jj-border-radius', 'value' => '4px', 'type' => 'size' ),
                array( 'name' => '--jj-box-shadow', 'value' => '0 2px 4px rgba(0,0,0,0.1)', 'type' => 'shadow' ),
            );
        }

        return $variables;
    }

    /**
     * 에디터 힌트에 CSS 변수 추가
     */
    public function add_css_var_hints( $hints ) {
        $css_vars = $this->get_acf_css_variables();

        foreach ( $css_vars as $var ) {
            $hints[] = array(
                'text'        => 'var(' . $var['name'] . ')',
                'displayText' => $var['name'] . ' → ' . $var['value'],
                'className'   => 'acf-csb-hint-' . $var['type'],
            );
        }

        return $hints;
    }

    /**
     * ACF CSS 스타일 가이드 URL 가져오기
     */
    public function get_style_guide_url() {
        if ( ! ACF_Code_Snippets_Box::is_acf_css_active() ) {
            return '';
        }

        return admin_url( 'admin.php?page=jj-style-center' );
    }
}

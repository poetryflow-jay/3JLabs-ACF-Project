<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 8] 스포크 어댑터: Breakdance
 * - Breakdance Builder의 버튼, 타이포그래피, 폼 등을 허브 설정과 연결
 * - Breakdance는 CSS 클래스 기반으로 스타일링되며, 자체 디자인 시스템 보유
 * 
 * @since 6.2.0
 */
final class JJ_Adapter_Spoke_Breakdance {

    private static $instance = null;
    private $options = array();
    private $error_handler;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 어댑터 초기화
     *
     * @param array $options 허브 옵션 전체 배열
     */
    public function init( $options ) {
        $this->options = is_array( $options ) ? $options : array();
        
        if ( class_exists( 'JJ_Error_Handler' ) ) {
            $this->error_handler = JJ_Error_Handler::instance();
        }

        // Breakdance가 활성화되어 있지 않으면 즉시 종료 (안전 모드)
        if ( ! defined( '__BREAKDANCE_VERSION' ) && ! class_exists( '\Breakdance\Setup' ) ) {
            return;
        }

        $this->add_adapter_hooks();
    }

    /**
     * 필요한 훅 등록
     */
    private function add_adapter_hooks() {
        // 1) Breakdance 전용 선택자를 레지스트리에 추가
        if ( class_exists( 'JJ_Selector_Registry' ) ) {
            $registry = JJ_Selector_Registry::instance();

            // 버튼: Breakdance 버튼 선택자
            $registry->add_selectors(
                'buttons',
                'primary',
                'base',
                array(
                    '.breakdance .bde-button',
                    '.breakdance .bde-button__button',
                    '.bde-button',
                    '.bde-button__button',
                    '[data-breakdance] .bde-button',
                )
            );
            $registry->add_selectors(
                'buttons',
                'primary',
                'hover',
                array(
                    '.breakdance .bde-button:hover',
                    '.bde-button:hover',
                    '.bde-button__button:hover',
                )
            );

            // 폼 입력 필드: Breakdance Form Element
            $registry->add_selectors(
                'forms',
                'input',
                'base',
                array(
                    '.bde-form input[type="text"]',
                    '.bde-form input[type="email"]',
                    '.bde-form input[type="tel"]',
                    '.bde-form input[type="password"]',
                    '.bde-form input[type="number"]',
                    '.bde-form textarea',
                    '.bde-form select',
                    '.breakdance .bde-form-field',
                )
            );
            $registry->add_selectors(
                'forms',
                'input',
                'focus',
                array(
                    '.bde-form input:focus',
                    '.bde-form textarea:focus',
                    '.bde-form select:focus',
                )
            );

            // 제목: Breakdance Heading Element
            $registry->add_selectors(
                'typography',
                'heading',
                'base',
                array(
                    '.bde-heading',
                    '.bde-heading h1',
                    '.bde-heading h2',
                    '.bde-heading h3',
                    '.bde-heading h4',
                    '.bde-heading h5',
                    '.bde-heading h6',
                    '.breakdance h1',
                    '.breakdance h2',
                    '.breakdance h3',
                )
            );

            // 본문: Breakdance Text/Rich Text Element
            $registry->add_selectors(
                'typography',
                'body',
                'base',
                array(
                    '.bde-text',
                    '.bde-rich-text',
                    '.breakdance p',
                    '.breakdance .bde-text-content',
                )
            );

            // 링크: Breakdance 링크 스타일
            $registry->add_selectors(
                'typography',
                'link',
                'base',
                array(
                    '.breakdance a',
                    '.bde-text a',
                    '.bde-rich-text a',
                )
            );
        }

        // 2) Breakdance 전용 CSS 변수 오버라이드
        add_action( 'wp_head', array( $this, 'output_breakdance_css_vars' ), 100 );
        
        // 3) Breakdance의 글로벌 스타일과 연동
        add_filter( 'breakdance_global_settings', array( $this, 'sync_global_settings' ), 10, 1 );
    }

    /**
     * Breakdance 전용 CSS 변수 출력
     */
    public function output_breakdance_css_vars() {
        $palettes = isset( $this->options['palettes'] ) ? $this->options['palettes'] : array();
        $typography = isset( $this->options['typography'] ) ? $this->options['typography'] : array();
        $buttons = isset( $this->options['buttons'] ) ? $this->options['buttons'] : array();

        if ( empty( $palettes ) && empty( $typography ) && empty( $buttons ) ) {
            return;
        }

        $css = '<style id="jj-breakdance-adapter-vars">' . PHP_EOL;
        $css .= ':root {' . PHP_EOL;

        // 색상 변수 - Breakdance 컨벤션에 맞춤
        if ( ! empty( $palettes['brand_primary'] ) ) {
            $css .= '  --bd-palette-color-1: ' . esc_attr( $palettes['brand_primary'] ) . ';' . PHP_EOL;
            $css .= '  --breakdance-primary-color: ' . esc_attr( $palettes['brand_primary'] ) . ';' . PHP_EOL;
        }
        if ( ! empty( $palettes['brand_secondary'] ) ) {
            $css .= '  --bd-palette-color-2: ' . esc_attr( $palettes['brand_secondary'] ) . ';' . PHP_EOL;
            $css .= '  --breakdance-secondary-color: ' . esc_attr( $palettes['brand_secondary'] ) . ';' . PHP_EOL;
        }
        if ( ! empty( $palettes['brand_accent'] ) ) {
            $css .= '  --bd-palette-color-3: ' . esc_attr( $palettes['brand_accent'] ) . ';' . PHP_EOL;
            $css .= '  --breakdance-accent-color: ' . esc_attr( $palettes['brand_accent'] ) . ';' . PHP_EOL;
        }
        if ( ! empty( $palettes['text_primary'] ) ) {
            $css .= '  --breakdance-text-color: ' . esc_attr( $palettes['text_primary'] ) . ';' . PHP_EOL;
        }
        if ( ! empty( $palettes['background_primary'] ) ) {
            $css .= '  --breakdance-bg-color: ' . esc_attr( $palettes['background_primary'] ) . ';' . PHP_EOL;
        }

        // 타이포그래피 변수
        if ( ! empty( $typography['heading_font'] ) ) {
            $css .= '  --breakdance-heading-font-family: ' . esc_attr( $typography['heading_font'] ) . ';' . PHP_EOL;
        }
        if ( ! empty( $typography['body_font'] ) ) {
            $css .= '  --breakdance-body-font-family: ' . esc_attr( $typography['body_font'] ) . ';' . PHP_EOL;
        }

        // 버튼 스타일
        if ( ! empty( $buttons['border_radius'] ) ) {
            $css .= '  --breakdance-button-border-radius: ' . esc_attr( $buttons['border_radius'] ) . ';' . PHP_EOL;
        }

        $css .= '}' . PHP_EOL;

        // Breakdance 버튼 직접 스타일링
        if ( ! empty( $palettes['brand_primary'] ) ) {
            $css .= '.bde-button__button {' . PHP_EOL;
            $css .= '  background-color: var(--breakdance-primary-color, ' . esc_attr( $palettes['brand_primary'] ) . ') !important;' . PHP_EOL;
            $css .= '}' . PHP_EOL;
        }

        $css .= '</style>' . PHP_EOL;

        echo $css;
    }

    /**
     * Breakdance 글로벌 설정과 동기화 (필터)
     */
    public function sync_global_settings( $settings ) {
        // Breakdance의 글로벌 설정에 우리의 팔레트/타이포를 주입
        // 참고: Breakdance의 설정 구조에 따라 조정 필요
        return $settings;
    }

    /**
     * 어댑터 정보 반환
     */
    public static function get_info() {
        return array(
            'id'          => 'breakdance',
            'name'        => 'Breakdance',
            'description' => 'Breakdance Builder의 버튼, 폼, 타이포그래피 스타일을 통합 관리합니다.',
            'version'     => '1.0.0',
            'author'      => 'J&J Labs',
            'type'        => 'spoke',
            'category'    => 'page-builder',
            'requires'    => array(
                'plugin'   => 'breakdance/plugin.php',
                'class'    => '\Breakdance\Setup',
                'constant' => '__BREAKDANCE_VERSION',
            ),
            'supports'    => array(
                'buttons'    => true,
                'forms'      => true,
                'typography' => true,
                'colors'     => true,
            ),
        );
    }
}


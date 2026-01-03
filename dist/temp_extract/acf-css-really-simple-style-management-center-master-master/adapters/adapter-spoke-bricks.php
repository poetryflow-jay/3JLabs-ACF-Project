<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 8] 스포크 어댑터: Bricks Builder
 * - Bricks Builder의 버튼, 타이포그래피, 폼 등을 허브 설정과 연결
 * - Bricks는 자체 CSS 변수 시스템을 사용하므로 이를 활용
 * 
 * @since 6.2.0
 */
final class JJ_Adapter_Spoke_Bricks {

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

        // Bricks Builder가 활성화되어 있지 않으면 즉시 종료 (안전 모드)
        if ( ! defined( 'BRICKS_VERSION' ) ) {
            return;
        }

        $this->add_adapter_hooks();
    }

    /**
     * 필요한 훅 등록
     */
    private function add_adapter_hooks() {
        // 1) Bricks 전용 선택자를 레지스트리에 추가
        if ( class_exists( 'JJ_Selector_Registry' ) ) {
            $registry = JJ_Selector_Registry::instance();

            // 버튼: Bricks 버튼 선택자
            $registry->add_selectors(
                'buttons',
                'primary',
                'base',
                array(
                    '.brxe-button',
                    '.bricks-button',
                    '[class*="brxe-"] .bricks-button',
                    '.brxe-button a',
                )
            );
            $registry->add_selectors(
                'buttons',
                'primary',
                'hover',
                array(
                    '.brxe-button:hover',
                    '.bricks-button:hover',
                    '[class*="brxe-"] .bricks-button:hover',
                )
            );

            // 폼 입력 필드: Bricks Form Element
            $registry->add_selectors(
                'forms',
                'input',
                'base',
                array(
                    '.brxe-form input[type="text"]',
                    '.brxe-form input[type="email"]',
                    '.brxe-form input[type="tel"]',
                    '.brxe-form input[type="password"]',
                    '.brxe-form input[type="number"]',
                    '.brxe-form textarea',
                    '.brxe-form select',
                )
            );
            $registry->add_selectors(
                'forms',
                'input',
                'focus',
                array(
                    '.brxe-form input:focus',
                    '.brxe-form textarea:focus',
                    '.brxe-form select:focus',
                )
            );

            // 제목: Bricks Heading Element
            $registry->add_selectors(
                'typography',
                'heading',
                'base',
                array(
                    '.brxe-heading',
                    '.brxe-heading h1',
                    '.brxe-heading h2',
                    '.brxe-heading h3',
                    '.brxe-heading h4',
                    '.brxe-heading h5',
                    '.brxe-heading h6',
                )
            );

            // 본문: Bricks Text Element
            $registry->add_selectors(
                'typography',
                'body',
                'base',
                array(
                    '.brxe-text',
                    '.brxe-text-basic',
                    '.brxe-rich-text',
                )
            );
        }

        // 2) Bricks 전용 CSS 변수 오버라이드
        add_action( 'wp_head', array( $this, 'output_bricks_css_vars' ), 100 );
        
        // 3) Bricks 에디터 내에서도 스타일 적용
        add_action( 'bricks/frontend/render_data', array( $this, 'inject_editor_styles' ), 10, 2 );
    }

    /**
     * Bricks 전용 CSS 변수 출력
     */
    public function output_bricks_css_vars() {
        $palettes   = isset( $this->options['palettes'] ) && is_array( $this->options['palettes'] ) ? $this->options['palettes'] : array();
        $brand      = isset( $palettes['brand'] ) && is_array( $palettes['brand'] ) ? $palettes['brand'] : array();
        $system     = isset( $palettes['system'] ) && is_array( $palettes['system'] ) ? $palettes['system'] : array();
        $typography = isset( $this->options['typography'] ) && is_array( $this->options['typography'] ) ? $this->options['typography'] : array();

        if ( empty( $brand ) && empty( $system ) && empty( $typography ) ) {
            return;
        }

        $css = '<style id="jj-bricks-adapter-vars">' . PHP_EOL;
        $css .= ':root {' . PHP_EOL;

        // 색상 변수 - Bricks 컨벤션에 맞춤(허브 옵션 구조 기반)
        if ( ! empty( $brand['primary_color'] ) ) {
            $css .= '  --bricks-color-primary: ' . esc_attr( $brand['primary_color'] ) . ';' . PHP_EOL;
        }
        if ( ! empty( $brand['secondary_color'] ) ) {
            $css .= '  --bricks-color-secondary: ' . esc_attr( $brand['secondary_color'] ) . ';' . PHP_EOL;
        }
        $accent = '';
        if ( ! empty( $brand['primary_color_hover'] ) ) {
            $accent = $brand['primary_color_hover'];
        } elseif ( ! empty( $brand['primary_color'] ) ) {
            $accent = $brand['primary_color'];
        }
        if ( $accent ) {
            $css .= '  --bricks-color-accent: ' . esc_attr( $accent ) . ';' . PHP_EOL;
        }
        if ( ! empty( $system['text_color'] ) ) {
            $css .= '  --bricks-text-color: ' . esc_attr( $system['text_color'] ) . ';' . PHP_EOL;
        }
        $bg = '';
        if ( ! empty( $system['site_bg'] ) ) {
            $bg = $system['site_bg'];
        } elseif ( ! empty( $system['content_bg'] ) ) {
            $bg = $system['content_bg'];
        }
        if ( $bg ) {
            $css .= '  --bricks-bg-color: ' . esc_attr( $bg ) . ';' . PHP_EOL;
        }

        // 타이포그래피 변수
        $heading_family = '';
        if ( isset( $typography['h2']['font_family'] ) && $typography['h2']['font_family'] !== '' ) {
            $heading_family = (string) $typography['h2']['font_family'];
        } elseif ( isset( $typography['h1']['font_family'] ) && $typography['h1']['font_family'] !== '' ) {
            $heading_family = (string) $typography['h1']['font_family'];
        }
        if ( $heading_family ) {
            $css .= '  --bricks-heading-font: ' . esc_attr( $heading_family ) . ';' . PHP_EOL;
        }
        if ( isset( $typography['p']['font_family'] ) && $typography['p']['font_family'] !== '' ) {
            $css .= '  --bricks-body-font: ' . esc_attr( (string) $typography['p']['font_family'] ) . ';' . PHP_EOL;
        }

        $css .= '}' . PHP_EOL;
        $css .= '</style>' . PHP_EOL;

        echo $css;
    }

    /**
     * Bricks 에디터 내 스타일 주입
     */
    public function inject_editor_styles( $content, $post_id ) {
        // Bricks 에디터 모드에서는 추가 처리 가능
        // 현재는 프론트엔드와 동일하게 처리
        return $content;
    }

    /**
     * 어댑터 정보 반환
     */
    public static function get_info() {
        return array(
            'id'          => 'bricks',
            'name'        => 'Bricks Builder',
            'description' => 'Bricks Builder 페이지 빌더의 버튼, 폼, 타이포그래피 스타일을 통합 관리합니다.',
            'version'     => '1.0.0',
            'author'      => 'J&J Labs',
            'type'        => 'spoke',
            'category'    => 'page-builder',
            'requires'    => array(
                'plugin' => 'bricks/bricks.php',
                'class'  => '',
                'constant' => 'BRICKS_VERSION',
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


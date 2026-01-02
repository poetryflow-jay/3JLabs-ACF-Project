<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * [Phase 10.2] Block Editor Integration
 *
 * 목표:
 * - Gutenberg(블록 에디터)에 JJ 디자인 토큰(팔레트/타이포)을 주입(theme.json bridge)
 * - 에디터/페이지빌더 편집 화면에서도 CSS 변수(:root) 가용성 보장
 * - JJ 전용 블록 3종 제공
 *   - jj-style-guide/palette
 *   - jj-style-guide/typography
 *   - jj-style-guide/mini-guide
 *
 * 지원(호환성/토큰 주입):
 * - Gutenberg 기반 블록 플러그인: GenerateBlocks, Kadence Blocks, Nexter Blocks (Gutenberg 팔레트/폰트 프리셋을 공유)
 * - 페이지 빌더: Elementor, Bricks, Beaver Builder, Divi Builder, WPBakery (편집 UI/프리뷰에서 토큰 주입)
 *
 * @since 10.2.0
 */
class JJ_Block_Editor_Integration {

    private static $instance = null;
    private $text_domain = 'acf-css-really-simple-style-management-center';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Gutenberg / Block Editor
        add_action( 'after_setup_theme', array( $this, 'setup_theme_json_bridge' ), 20 );
        add_action( 'init', array( $this, 'register_blocks' ), 20 );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
        add_action( 'enqueue_block_assets', array( $this, 'enqueue_block_front_assets' ) );
        add_filter( 'block_categories_all', array( $this, 'register_block_category' ), 20, 2 );

        // Builders (Elementor/Bricks/Beaver/Divi/WPBakery) - 토큰 주입
        $this->register_builder_hooks();
        $this->register_builder_deep_integration();
    }

    /**
     * [Phase 10.3] 빌더 심화 통합(안전형)
     * - 빌더가 사용하는 "글로벌 변수" alias를 JJ 토큰에 연결
     * - 빌더 버튼/폼 셀렉터를 레지스트리에 추가하여 Strategy 1의 전역 스타일이 자연스럽게 적용되도록 함
     */
    private function register_builder_deep_integration() {
        // alias vars 주입(토큰 CSS 생성 파이프라인에 붙임)
        add_filter( 'jj_block_editor_css_vars', array( $this, 'filter_builder_alias_vars' ), 20, 2 );

        // 셀렉터 확장(빌더별 컴포넌트에 대한 적용 범위 확대)
        if ( class_exists( 'JJ_Selector_Registry' ) ) {
            try {
                $this->register_builder_selectors( JJ_Selector_Registry::instance() );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
    }

    /**
     * JJ 토큰 → 빌더 글로벌 변수 alias 매핑
     *
     * @param array $vars
     * @param array $options
     * @return array
     */
    public function filter_builder_alias_vars( $vars, $options ) {
        $enabled = apply_filters( 'jj_builder_alias_vars_enabled', true, $options );
        if ( ! $enabled || ! is_array( $vars ) ) {
            return $vars;
        }

        // Elementor Global Colors (CSS 변수 기반)
        $vars['--e-global-color-primary']   = 'var(--jj-primary-color, #2271b1)';
        $vars['--e-global-color-secondary'] = 'var(--jj-secondary-color, #50575e)';
        $vars['--e-global-color-text']      = 'var(--jj-sys-text, #1d2327)';
        $vars['--e-global-color-accent']    = 'var(--jj-primary-color-hover, #135e96)';

        // Elementor Global Typography (대표만 매핑)
        $vars['--e-global-typography-primary-font-family'] = 'var(--jj-font-h2-family, inherit)';
        $vars['--e-global-typography-text-font-family']    = 'var(--jj-font-p-family, inherit)';
        $vars['--e-global-typography-primary-font-size']   = 'var(--jj-font-h2-size, inherit)';
        $vars['--e-global-typography-text-font-size']      = 'var(--jj-font-p-size, inherit)';

        // Bricks
        $vars['--bricks-color-primary']   = 'var(--jj-primary-color, #2271b1)';
        $vars['--bricks-color-secondary'] = 'var(--jj-secondary-color, #50575e)';
        $vars['--bricks-color-accent']    = 'var(--jj-primary-color-hover, #135e96)';
        $vars['--bricks-text-color']      = 'var(--jj-sys-text, #1d2327)';
        $vars['--bricks-bg-color']        = 'var(--jj-sys-site-bg, transparent)';
        $vars['--bricks-heading-font']    = 'var(--jj-font-h2-family, inherit)';
        $vars['--bricks-body-font']       = 'var(--jj-font-p-family, inherit)';

        /**
         * 빌더 alias 변수 추가/수정 필터
         */
        return apply_filters( 'jj_builder_alias_vars', $vars, $options );
    }

    /**
     * 빌더별 버튼/폼 선택자 확장
     *
     * @param JJ_Selector_Registry $registry
     */
    private function register_builder_selectors( $registry ) {
        $enabled = apply_filters( 'jj_builder_selector_bridge_enabled', true );
        if ( ! $enabled || ! $registry || ! method_exists( $registry, 'add_selectors' ) ) {
            return;
        }

        // Buttons: Bricks / Breakdance / Beaver / Divi / WPBakery
        $registry->add_selectors( 'buttons', 'primary', 'base', array(
            // Bricks
            '.brxe-button',
            '.bricks-button',
            // Breakdance
            '.bde-button',
            '.bde-button__button',
            // Beaver Builder
            '.fl-button',
            '.fl-builder-content a.fl-button',
            // Divi
            '.et_pb_button',
            '.et_pb_button_module_wrapper a.et_pb_button',
            // WPBakery
            '.vc_btn3',
            'a.vc_btn3',
        ) );
        $registry->add_selectors( 'buttons', 'primary', 'hover', array(
            '.brxe-button:hover',
            '.bricks-button:hover',
            '.bde-button:hover',
            '.bde-button__button:hover',
            '.fl-button:hover',
            '.fl-builder-content a.fl-button:hover',
            '.et_pb_button:hover',
            '.et_pb_button_module_wrapper a.et_pb_button:hover',
            '.vc_btn3:hover',
            'a.vc_btn3:hover',
        ) );

        // Forms: Divi contact form 등
        $registry->add_selectors( 'forms', 'input', 'base', array(
            '.et_pb_contact_form_container input[type="text"]',
            '.et_pb_contact_form_container input[type="email"]',
            '.et_pb_contact_form_container input[type="tel"]',
            '.et_pb_contact_form_container input[type="url"]',
            '.et_pb_contact_form_container input[type="number"]',
            '.et_pb_contact_form_container input[type="search"]',
            '.et_pb_contact_form_container textarea',
            '.et_pb_contact_form_container select',
        ) );
        $registry->add_selectors( 'forms', 'input', 'focus', array(
            '.et_pb_contact_form_container input:focus',
            '.et_pb_contact_form_container textarea:focus',
            '.et_pb_contact_form_container select:focus',
        ) );
    }

    /**
     * Gutenberg 블록 카테고리 등록 (Inserter에서 JJ 블록을 묶어서 표시)
     */
    public function register_block_category( $categories, $post ) {
        if ( ! is_array( $categories ) ) {
            $categories = array();
        }

        foreach ( $categories as $cat ) {
            if ( is_array( $cat ) && ( $cat['slug'] ?? '' ) === 'acf-css-really-simple-style-management-center' ) {
                return $categories;
            }
        }

        $categories[] = array(
            'slug'  => 'acf-css-really-simple-style-management-center',
            'title' => __( 'JJ Style Guide', 'acf-css-really-simple-style-management-center' ),
        );

        return $categories;
    }

    /**
     * theme.json bridge 활성화
     * - Gutenberg 기반 블록 플러그인(GenerateBlocks/Kadence/Nexter 등)이 WP 프리셋을 재사용할 수 있게 함
     */
    public function setup_theme_json_bridge() {
        if ( ! class_exists( 'WP_Theme_JSON_Data' ) || ! function_exists( 'add_filter' ) ) {
            return;
        }
        add_filter( 'wp_theme_json_data_theme', array( $this, 'filter_theme_json_data_theme' ), 20 );
    }

    /**
     * theme.json 데이터에 팔레트/폰트사이즈 프리셋 병합
     * - update_with()를 사용하되, 기존 프리셋을 덮지 않도록 먼저 병합 후 반영
     *
     * @param WP_Theme_JSON_Data $theme_json Theme JSON data object.
     * @return WP_Theme_JSON_Data
     */
    public function filter_theme_json_data_theme( $theme_json ) {
        if ( ! is_object( $theme_json ) || ! method_exists( $theme_json, 'get_data' ) || ! method_exists( $theme_json, 'update_with' ) ) {
            return $theme_json;
        }

        $options = $this->get_hub_options();

        $data = $theme_json->get_data();
        if ( ! is_array( $data ) ) {
            $data = array();
        }

        $existing_palette   = array();
        $existing_fontSizes = array();

        if ( isset( $data['settings']['color']['palette'] ) && is_array( $data['settings']['color']['palette'] ) ) {
            $existing_palette = $data['settings']['color']['palette'];
        }
        if ( isset( $data['settings']['typography']['fontSizes'] ) && is_array( $data['settings']['typography']['fontSizes'] ) ) {
            $existing_fontSizes = $data['settings']['typography']['fontSizes'];
        }

        $jj_palette    = $this->build_wp_palette( $options );
        $jj_font_sizes = $this->build_wp_font_sizes( $options );

        $merged_palette    = $this->merge_preset_arrays_by_slug( $existing_palette, $jj_palette );
        $merged_font_sizes = $this->merge_preset_arrays_by_slug( $existing_fontSizes, $jj_font_sizes );

        $new_data = array(
            'version'  => 2,
            'settings' => array(
                'color' => array(
                    'palette' => $merged_palette,
                ),
                'typography' => array(
                    'fontSizes' => $merged_font_sizes,
                ),
            ),
        );

        /**
         * theme.json 주입 데이터 필터
         *
         * @param array $new_data theme.json-like array.
         * @param array $options  JJ Style Guide options.
         */
        $new_data = apply_filters( 'jj_block_editor_theme_json_update', $new_data, $options );

        try {
            return $theme_json->update_with( $new_data );
        } catch ( Exception $e ) {
            return $theme_json;
        } catch ( Error $e ) {
            return $theme_json;
        }
    }

    /**
     * 에디터에 토큰(:root CSS 변수) 주입
     */
    public function enqueue_block_editor_assets() {
        $ver = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '10.2.0';

        // Inline style handle
        wp_register_style( 'jj-design-tokens-editor', false, array(), $ver );
        wp_enqueue_style( 'jj-design-tokens-editor' );

        $css = $this->build_tokens_css();
        if ( $css ) {
            wp_add_inline_style( 'jj-design-tokens-editor', $css );
        }
    }

    /**
     * 프런트엔드에서도 블록 스타일 로드 (블록이 포함된 경우를 대비)
     */
    public function enqueue_block_front_assets() {
        if ( wp_style_is( 'jj-style-guide-blocks', 'registered' ) ) {
            wp_enqueue_style( 'jj-style-guide-blocks' );
        }
    }

    /**
     * JJ 블록 등록
     */
    public function register_blocks() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        $ver      = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '10.2.0';
        $base_url = defined( 'JJ_STYLE_GUIDE_URL' ) ? JJ_STYLE_GUIDE_URL : '';

        wp_register_script(
            'jj-style-guide-blocks',
            $base_url . 'assets/js/jj-style-guide-blocks.js',
            array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render' ),
            $ver,
            true
        );

        // 블록 UI용 동적 옵션/문구 전달 (관리자 권한 불필요)
        $options = $this->get_hub_options();
        $block_ui = array(
            'version'   => $ver,
            'category'  => 'acf-css-really-simple-style-management-center',
            'palettes'  => $this->get_palette_options_for_editor( $options ),
            'typography' => $this->get_typography_options_for_editor( $options ),
            'strings'   => array(
                'panel_settings'        => __( '설정', 'acf-css-really-simple-style-management-center' ),
                'panel_display'         => __( '표시', 'acf-css-really-simple-style-management-center' ),
                'panel_content'         => __( '콘텐츠', 'acf-css-really-simple-style-management-center' ),
                'palette_label'         => __( '팔레트', 'acf-css-really-simple-style-management-center' ),
                'palette_for_mini_guide'=> __( '팔레트(미니 가이드용)', 'acf-css-really-simple-style-management-center' ),
                'show_labels'           => __( '라벨 표시', 'acf-css-really-simple-style-management-center' ),
                'show_values'           => __( '값 표시', 'acf-css-really-simple-style-management-center' ),
                'show_title'            => __( '제목 표시', 'acf-css-really-simple-style-management-center' ),
                'layout'                => __( '레이아웃', 'acf-css-really-simple-style-management-center' ),
                'layout_auto'           => __( '자동', 'acf-css-really-simple-style-management-center' ),
                'layout_compact'        => __( '컴팩트', 'acf-css-really-simple-style-management-center' ),
                'columns'               => __( '컬럼', 'acf-css-really-simple-style-management-center' ),
                'swatch_height'         => __( '스와치 높이(px)', 'acf-css-really-simple-style-management-center' ),
                'typography_key'        => __( '스타일 키', 'acf-css-really-simple-style-management-center' ),
                'text'                  => __( '텍스트', 'acf-css-really-simple-style-management-center' ),
                'include_palettes'      => __( '팔레트', 'acf-css-really-simple-style-management-center' ),
                'include_typography'    => __( '타이포그래피', 'acf-css-really-simple-style-management-center' ),
                'include_buttons'       => __( '버튼', 'acf-css-really-simple-style-management-center' ),
                'preview_unavailable'   => __( '미리보기를 불러올 수 없습니다.', 'acf-css-really-simple-style-management-center' ),
                'no_palette_available'  => __( '사용 가능한 팔레트가 없습니다. (Admin Center에서 팔레트를 먼저 설정하세요)', 'acf-css-really-simple-style-management-center' ),
                'no_typography_available' => __( '사용 가능한 타이포그래피가 없습니다. (Style Center에서 타이포그래피를 먼저 설정하세요)', 'acf-css-really-simple-style-management-center' ),
            ),
        );
        wp_localize_script( 'jj-style-guide-blocks', 'jjStyleGuideBlocks', $block_ui );

        wp_register_style(
            'jj-style-guide-blocks',
            $base_url . 'assets/css/jj-style-guide-blocks.css',
            array(),
            $ver
        );

        register_block_type( 'jj-style-guide/palette', array(
            'api_version'     => 3,
            'editor_script'   => 'jj-style-guide-blocks',
            'style'           => 'jj-style-guide-blocks',
            'render_callback' => array( $this, 'render_palette_block' ),
            'attributes'      => array(
                'paletteKey' => array(
                    'type'    => 'string',
                    'default' => 'brand',
                ),
                'showTitle' => array(
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'showLabels' => array(
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'showValues' => array(
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'layout' => array(
                    'type'    => 'string',
                    'default' => 'auto', // auto | compact
                ),
                'columns' => array(
                    'type'    => 'number',
                    'default' => 0, // 0=auto
                ),
                'swatchHeight' => array(
                    'type'    => 'number',
                    'default' => 54,
                ),
            ),
        ) );

        register_block_type( 'jj-style-guide/typography', array(
            'api_version'     => 3,
            'editor_script'   => 'jj-style-guide-blocks',
            'style'           => 'jj-style-guide-blocks',
            'render_callback' => array( $this, 'render_typography_block' ),
            'attributes'      => array(
                'tag' => array(
                    'type'    => 'string',
                    'default' => 'h2',
                ),
                'text' => array(
                    'type'    => 'string',
                    'default' => __( '타이포그래피 미리보기', 'acf-css-really-simple-style-management-center' ),
                ),
                'align' => array(
                    'type'    => 'string',
                    'default' => '',
                ),
            ),
        ) );

        register_block_type( 'jj-style-guide/mini-guide', array(
            'api_version'     => 3,
            'editor_script'   => 'jj-style-guide-blocks',
            'style'           => 'jj-style-guide-blocks',
            'render_callback' => array( $this, 'render_mini_guide_block' ),
            'attributes'      => array(
                'includePalettes' => array(
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'includeTypography' => array(
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'includeButtons' => array(
                    'type'    => 'boolean',
                    'default' => true,
                ),
                'paletteKey' => array(
                    'type'    => 'string',
                    'default' => 'brand',
                ),
                'compact' => array(
                    'type'    => 'boolean',
                    'default' => false,
                ),
            ),
        ) );

        // [v13.4.2] 스타일 가이드 라이브 페이지 블록 (숏코드 대체)
        register_block_type( 'jj-style-guide/live-page', array(
            'api_version'     => 3,
            'editor_script'   => 'jj-style-guide-blocks',
            'style'           => 'jj-style-guide-blocks',
            'render_callback' => array( $this, 'render_live_page_block' ),
            'attributes'      => array(),
        ) );
    }

    /**
     * 스타일 가이드 라이브 페이지 블록 렌더
     * @since 13.4.2
     */
    public function render_live_page_block( $attributes, $content, $block ) {
        // 블록 에디터에서 미리보기 렌더링 (REST API 컨텍스트)
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            // 에디터에서는 간략한 미리보기 표시
            $preview_html = '<div class="jj-live-page-editor-preview" style="'
                . 'padding: 40px 20px; '
                . 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); '
                . 'border-radius: 12px; '
                . 'text-align: center; '
                . 'color: #fff; '
                . 'font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', sans-serif;'
                . '">';
            $preview_html .= '<div style="font-size: 48px; margin-bottom: 16px;">📘</div>';
            $preview_html .= '<h3 style="margin: 0 0 12px; font-size: 20px; font-weight: 600; color: #fff;">'
                . esc_html__( 'ACF CSS 스타일 가이드 라이브', 'acf-css-really-simple-style-management-center' )
                . '</h3>';
            $preview_html .= '<p style="margin: 0 0 16px; opacity: 0.9; font-size: 14px;">'
                . esc_html__( '이 블록은 프론트엔드에서 전체 스타일 가이드를 표시합니다.', 'acf-css-really-simple-style-management-center' )
                . '</p>';
            $preview_html .= '<p style="margin: 0; font-size: 12px; opacity: 0.75;">'
                . esc_html__( '💡 페이지를 게시/저장 후 "미리보기" 또는 실제 페이지에서 확인하세요.', 'acf-css-really-simple-style-management-center' )
                . '</p>';
            $preview_html .= '</div>';
            return $preview_html;
        }

        // 프론트엔드에서는 실제 숏코드 실행
        if ( class_exists( 'JJ_Style_Guide_Live_Page' ) ) {
            $live_page = JJ_Style_Guide_Live_Page::instance();
            if ( method_exists( $live_page, 'render_shortcode' ) ) {
                return $live_page->render_shortcode();
            }
        }

        // Fallback: 숏코드 직접 호출
        return do_shortcode( '[jj_style_guide_live]' );
    }

    /**
     * 팔레트 블록 렌더
     */
    public function render_palette_block( $attributes ) {
        $palette_key = isset( $attributes['paletteKey'] ) ? sanitize_key( $attributes['paletteKey'] ) : 'brand';
        $show_title  = isset( $attributes['showTitle'] ) ? (bool) $attributes['showTitle'] : true;
        $show_labels = isset( $attributes['showLabels'] ) ? (bool) $attributes['showLabels'] : true;
        $show_values = isset( $attributes['showValues'] ) ? (bool) $attributes['showValues'] : true;
        $layout      = isset( $attributes['layout'] ) ? sanitize_key( $attributes['layout'] ) : 'auto';
        $columns     = isset( $attributes['columns'] ) ? (int) $attributes['columns'] : 0;
        $swatch_h    = isset( $attributes['swatchHeight'] ) ? (int) $attributes['swatchHeight'] : 54;

        $options  = $this->get_hub_options();
        $palette  = isset( $options['palettes'][ $palette_key ] ) && is_array( $options['palettes'][ $palette_key ] )
            ? $options['palettes'][ $palette_key ]
            : array();

        if ( empty( $palette ) ) {
            return '';
        }

        $title = $this->get_palette_label( $palette_key );

        $classes = array(
            'jj-block',
            'jj-block-palette',
            'jj-block-palette-' . $palette_key,
        );
        if ( 'compact' === $layout ) {
            $classes[] = 'jj-block-palette--compact';
        }
        if ( $columns > 0 ) {
            $classes[] = 'jj-block-palette--fixed-cols';
        }

        $style_vars = array();
        if ( $columns > 0 ) {
            $style_vars[] = '--jj-palette-cols:' . (int) max( 1, min( 12, $columns ) );
        }
        if ( $swatch_h > 0 ) {
            $style_vars[] = '--jj-palette-swatch-height:' . (int) max( 16, min( 220, $swatch_h ) ) . 'px';
        }

        $html  = '<div class="' . esc_attr( implode( ' ', $classes ) ) . '"' . ( $style_vars ? ' style="' . esc_attr( implode( ';', $style_vars ) ) . '"' : '' ) . '>';
        if ( $show_title ) {
            $html .= '<div class="jj-block__header"><strong>' . esc_html( $title ) . '</strong></div>';
        }
        $html .= '<div class="jj-block-palette__grid">';

        foreach ( $palette as $key => $value ) {
            $color = $this->sanitize_css_color( $value );
            if ( ! $color ) {
                continue;
            }
            $label = $this->humanize_key( $key );
            $html .= '<div class="jj-block-palette__item">';
            $html .= '<div class="jj-block-palette__swatch" style="background:' . esc_attr( $color ) . ';"></div>';
            if ( $show_labels ) {
                $html .= '<div class="jj-block-palette__meta">';
                $html .= '<div class="jj-block-palette__label">' . esc_html( $label ) . '</div>';
                if ( $show_values ) {
                    $html .= '<code class="jj-block-palette__value">' . esc_html( $color ) . '</code>';
                }
                $html .= '</div>';
            }
            $html .= '</div>';
        }

        $html .= '</div></div>';
        return $html;
    }

    /**
     * 타이포그래피 블록 렌더
     */
    public function render_typography_block( $attributes ) {
        $tag = isset( $attributes['tag'] ) ? sanitize_key( $attributes['tag'] ) : 'h2';
        $text = isset( $attributes['text'] ) ? wp_kses_post( $attributes['text'] ) : __( '타이포그래피 미리보기', 'acf-css-really-simple-style-management-center' );
        $align = isset( $attributes['align'] ) ? sanitize_key( $attributes['align'] ) : '';

        $allowed_keys = array( 'h1','h2','h3','h4','h5','h6','p','body','small' );
        if ( ! in_array( $tag, $allowed_keys, true ) ) {
            $tag = 'h2';
        }

        $html_tag = $tag;
        if ( 'body' === $tag ) {
            $html_tag = 'p';
        } elseif ( 'small' === $tag ) {
            $html_tag = 'p';
        }

        $classes = array(
            'jj-block',
            'jj-block-typography',
            'jj-block-typography--' . $tag,
        );
        if ( $align ) {
            $classes[] = 'has-text-align-' . $align;
        }

        $style = $this->build_typography_inline_style( $tag );

        return sprintf(
            '<%1$s class="%2$s" style="%3$s">%4$s</%1$s>',
            tag_escape( $html_tag ),
            esc_attr( implode( ' ', $classes ) ),
            esc_attr( $style ),
            $text
        );
    }

    /**
     * 미니 가이드 블록 렌더
     */
    public function render_mini_guide_block( $attributes ) {
        $include_palettes   = isset( $attributes['includePalettes'] ) ? (bool) $attributes['includePalettes'] : true;
        $include_typography = isset( $attributes['includeTypography'] ) ? (bool) $attributes['includeTypography'] : true;
        $include_buttons    = isset( $attributes['includeButtons'] ) ? (bool) $attributes['includeButtons'] : true;
        $palette_key        = isset( $attributes['paletteKey'] ) ? sanitize_key( $attributes['paletteKey'] ) : 'brand';
        $compact            = isset( $attributes['compact'] ) ? (bool) $attributes['compact'] : false;

        $html  = '<div class="jj-block jj-block-mini-guide' . ( $compact ? ' jj-block-mini-guide--compact' : '' ) . '">';
        $html .= '<div class="jj-block-mini-guide__title"><strong>' . esc_html__( 'JJ 스타일 가이드', 'acf-css-really-simple-style-management-center' ) . '</strong></div>';

        if ( $include_palettes ) {
            $html .= '<div class="jj-block-mini-guide__section">';
            $html .= $this->render_palette_block( array(
                'paletteKey' => $palette_key,
                'showTitle'  => true,
                'showLabels' => true,
                'showValues' => true,
                'layout'     => $compact ? 'compact' : 'auto',
                'columns'    => $compact ? 4 : 0,
                'swatchHeight' => $compact ? 36 : 54,
            ) );
            $html .= '</div>';
        }

        if ( $include_typography ) {
            $html .= '<div class="jj-block-mini-guide__section">';
            $html .= '<div class="jj-block-mini-guide__subtitle">' . esc_html__( '타이포그래피', 'acf-css-really-simple-style-management-center' ) . '</div>';
            foreach ( array( 'h1','h2','h3','p' ) as $tag ) {
                $html .= $this->render_typography_block( array(
                    'tag'  => $tag,
                    'text' => esc_html( strtoupper( $tag ) . ' — ' . __( '미리보기', 'acf-css-really-simple-style-management-center' ) ),
                ) );
            }
            $html .= '</div>';
        }

        if ( $include_buttons ) {
            $html .= '<div class="jj-block-mini-guide__section">';
            $html .= '<div class="jj-block-mini-guide__subtitle">' . esc_html__( '버튼', 'acf-css-really-simple-style-management-center' ) . '</div>';
            $html .= '<div class="jj-block-mini-guide__buttons">';
            $html .= '<a class="jj-block-button jj-block-button--primary" href="#">' . esc_html__( 'Primary', 'acf-css-really-simple-style-management-center' ) . '</a>';
            $html .= '<a class="jj-block-button jj-block-button--secondary" href="#">' . esc_html__( 'Secondary', 'acf-css-really-simple-style-management-center' ) . '</a>';
            $html .= '<a class="jj-block-button jj-block-button--text" href="#">' . esc_html__( 'Text', 'acf-css-really-simple-style-management-center' ) . '</a>';
            $html .= '</div></div>';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * ===== Builders compatibility =====
     */
    private function register_builder_hooks() {
        // Elementor
        add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_builder_tokens' ) );
        add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_builder_tokens' ) );

        // Bricks
        add_action( 'bricks/builder/enqueue_styles', array( $this, 'enqueue_builder_tokens' ) );
        add_action( 'bricks/frontend/enqueue_styles', array( $this, 'enqueue_builder_tokens' ) );

        // Beaver Builder
        add_action( 'fl_builder_ui_enqueue_styles', array( $this, 'enqueue_builder_tokens' ) );
        add_action( 'fl_builder_enqueue_styles_scripts', array( $this, 'enqueue_builder_tokens' ) );

        // Divi Builder
        add_action( 'et_fb_enqueue_assets', array( $this, 'enqueue_builder_tokens' ) );
        add_action( 'et_builder_ready', array( $this, 'enqueue_builder_tokens' ) );

        // WPBakery (Visual Composer)
        add_action( 'vc_backend_editor_enqueue_js_css', array( $this, 'enqueue_builder_tokens' ) );
        add_action( 'vc_frontend_editor_enqueue_js_css', array( $this, 'enqueue_builder_tokens' ) );
    }

    /**
     * 빌더/에디터 UI에서 토큰(:root CSS 변수) 주입
     */
    public function enqueue_builder_tokens() {
        $ver = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '10.2.0';

        wp_register_style( 'jj-design-tokens-builder', false, array(), $ver );
        wp_enqueue_style( 'jj-design-tokens-builder' );

        $css = $this->build_tokens_css();
        if ( $css ) {
            wp_add_inline_style( 'jj-design-tokens-builder', $css );
        }
    }

    /**
     * ===== Tokens helpers =====
     */

    private function get_hub_options() {
        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        return get_option( $key, array() );
    }

    private function build_tokens_css() {
        $options = $this->get_hub_options();
        $vars = $this->build_css_var_map( $options );
        if ( empty( $vars ) ) {
            return '';
        }

        $css = ':root{';
        foreach ( $vars as $name => $value ) {
            $css .= $name . ':' . $value . ';';
        }
        $css .= '}';

        // Block editor wrapper에도 동일하게 주입 (일부 환경에서 :root 변수가 전파되지 않는 경우 대비)
        $css .= '.editor-styles-wrapper{';
        foreach ( $vars as $name => $value ) {
            $css .= $name . ':' . $value . ';';
        }
        $css .= '}';

        return $css;
    }

    /**
     * JJ 옵션에서 필요한 변수만 추출하여 :root CSS 변수 맵 생성
     */
    private function build_css_var_map( $options ) {
        $vars = array();

        // Typography output unit(px/rem/em) + base px(1rem/1em)
        $typo_unit = 'px';
        $typo_base = 16.0;
        if ( isset( $options['typography_settings'] ) && is_array( $options['typography_settings'] ) ) {
            $ts = $options['typography_settings'];
            if ( isset( $ts['unit'] ) && in_array( (string) $ts['unit'], array( 'px', 'rem', 'em' ), true ) ) {
                $typo_unit = (string) $ts['unit'];
            }
            if ( isset( $ts['base_px'] ) && is_numeric( $ts['base_px'] ) && (float) $ts['base_px'] > 0 ) {
                $typo_base = (float) $ts['base_px'];
            }
        }

        // Brand palette
        if ( isset( $options['palettes']['brand'] ) && is_array( $options['palettes']['brand'] ) ) {
            $brand = $options['palettes']['brand'];
            $map = array(
                'primary_color'         => '--jj-primary-color',
                'primary_color_hover'   => '--jj-primary-color-hover',
                'secondary_color'       => '--jj-secondary-color',
                'secondary_color_hover' => '--jj-secondary-color-hover',
            );
            foreach ( $map as $opt_key => $var_name ) {
                if ( ! empty( $brand[ $opt_key ] ) ) {
                    $color = $this->sanitize_css_color( $brand[ $opt_key ] );
                    if ( $color ) {
                        $vars[ $var_name ] = $color;
                    }
                }
            }
        }

        // System palette
        if ( isset( $options['palettes']['system'] ) && is_array( $options['palettes']['system'] ) ) {
            $sys = $options['palettes']['system'];
            $map = array(
                'site_bg'     => '--jj-sys-site-bg',
                'content_bg'  => '--jj-sys-content-bg',
                'text_color'  => '--jj-sys-text',
                'link_color'  => '--jj-sys-link',
            );
            foreach ( $map as $opt_key => $var_name ) {
                if ( ! empty( $sys[ $opt_key ] ) ) {
                    $color = $this->sanitize_css_color( $sys[ $opt_key ] );
                    if ( $color ) {
                        $vars[ $var_name ] = $color;
                    }
                }
            }
        }

        // Typography vars (desktop only)
        if ( isset( $options['typography'] ) && is_array( $options['typography'] ) ) {
            foreach ( $options['typography'] as $tag => $props ) {
                $tag = sanitize_key( $tag );
                if ( ! is_array( $props ) ) {
                    continue;
                }
                $prefix = '--jj-font-' . $tag;
                if ( ! empty( $props['font_family'] ) ) {
                    $vars[ $prefix . '-family' ] = $this->sanitize_css_font_family( $props['font_family'] );
                }
                if ( ! empty( $props['font_weight'] ) ) {
                    $vars[ $prefix . '-weight' ] = preg_replace( '/[^0-9a-zA-Z]/', '', (string) $props['font_weight'] );
                }
                if ( ! empty( $props['font_style'] ) ) {
                    $vars[ $prefix . '-style' ] = preg_replace( '/[^a-zA-Z]/', '', (string) $props['font_style'] );
                }
                if ( isset( $props['line_height'] ) && $props['line_height'] !== '' && is_numeric( $props['line_height'] ) ) {
                    $vars[ $prefix . '-line-height' ] = (float) $props['line_height'] . 'em';
                }
                if ( isset( $props['letter_spacing'] ) && $props['letter_spacing'] !== '' && is_numeric( $props['letter_spacing'] ) ) {
                    $vars[ $prefix . '-letter-spacing' ] = (float) $props['letter_spacing'] . 'px';
                }
                if ( ! empty( $props['text_transform'] ) ) {
                    $vars[ $prefix . '-text-transform' ] = preg_replace( '/[^a-zA-Z-]/', '', (string) $props['text_transform'] );
                }

                // font size (desktop)
                $size = null;
                if ( isset( $props['font_size']['desktop'] ) && is_numeric( $props['font_size']['desktop'] ) ) {
                    $size = (float) $props['font_size']['desktop'];
                } elseif ( isset( $props['font_size'] ) && is_numeric( $props['font_size'] ) ) {
                    $size = (float) $props['font_size'];
                }
                if ( $size ) {
                    $vars[ $prefix . '-size' ] = $this->format_font_size_value( $size, $typo_unit, $typo_base );
                }
            }
        }

        // Buttons vars (primary/secondary/text)
        if ( isset( $options['buttons'] ) && is_array( $options['buttons'] ) ) {
            $btn_maps = array(
                'primary'   => '--jj-btn-primary',
                'secondary' => '--jj-btn-secondary',
                'text'      => '--jj-btn-text',
            );
            foreach ( $btn_maps as $btn_key => $prefix ) {
                if ( empty( $options['buttons'][ $btn_key ] ) || ! is_array( $options['buttons'][ $btn_key ] ) ) {
                    continue;
                }
                $btn = $options['buttons'][ $btn_key ];
                if ( ! empty( $btn['background_color'] ) ) {
                    $color = $this->sanitize_css_color( $btn['background_color'] );
                    if ( $color ) $vars[ $prefix . '-bg' ] = $color;
                }
                if ( ! empty( $btn['text_color'] ) ) {
                    $color = $this->sanitize_css_color( $btn['text_color'] );
                    if ( $color ) $vars[ $prefix . '-text' ] = $color;
                }
                if ( ! empty( $btn['border_color'] ) ) {
                    $color = $this->sanitize_css_color( $btn['border_color'] );
                    if ( $color ) $vars[ $prefix . '-border' ] = $color;
                }
                if ( ! empty( $btn['background_color_hover'] ) ) {
                    $color = $this->sanitize_css_color( $btn['background_color_hover'] );
                    if ( $color ) $vars[ $prefix . '-bg-hover' ] = $color;
                }
                if ( ! empty( $btn['text_color_hover'] ) ) {
                    $color = $this->sanitize_css_color( $btn['text_color_hover'] );
                    if ( $color ) $vars[ $prefix . '-text-hover' ] = $color;
                }
                if ( ! empty( $btn['border_color_hover'] ) ) {
                    $color = $this->sanitize_css_color( $btn['border_color_hover'] );
                    if ( $color ) $vars[ $prefix . '-border-hover' ] = $color;
                }
                if ( isset( $btn['border_radius'] ) && $btn['border_radius'] !== '' && is_numeric( $btn['border_radius'] ) ) {
                    $vars[ $prefix . '-border-radius' ] = (float) $btn['border_radius'] . 'px';
                }
            }
        }

        return apply_filters( 'jj_block_editor_css_vars', $vars, $options );
    }

    /**
     * px 값(숫자)을 px/rem/em으로 변환
     */
    private function format_font_size_value( $px, $unit, $base_px ) {
        $px = is_numeric( $px ) ? (float) $px : 0;
        if ( $px <= 0 ) {
            return '';
        }
        $unit = (string) $unit;
        $base_px = is_numeric( $base_px ) ? (float) $base_px : 16.0;
        if ( $base_px <= 0 ) {
            $base_px = 16.0;
        }
        if ( 'rem' === $unit || 'em' === $unit ) {
            $ratio = round( $px / $base_px, 4 );
            return (string) $ratio . $unit;
        }
        return (string) round( $px, 4 ) . 'px';
    }

    /**
     * Gutenberg 팔레트 프리셋 생성
     *
     * @return array[]
     */
    private function build_wp_palette( $options ) {
        $items = array();

        if ( isset( $options['palettes'] ) && is_array( $options['palettes'] ) ) {
            foreach ( $options['palettes'] as $group_key => $palette ) {
                if ( ! is_array( $palette ) ) {
                    continue;
                }
                foreach ( $palette as $color_key => $color_value ) {
                    $color = $this->sanitize_css_color( $color_value );
                    if ( ! $color ) {
                        continue;
                    }
                    $slug = $this->preset_slug( $group_key . '-' . $color_key );
                    $name = $this->preset_name( $group_key, $color_key );
                    $items[] = array(
                        'slug'  => $slug,
                        'color' => $color,
                        'name'  => $name,
                    );
                }
            }
        }

        // 너무 많은 팔레트는 에디터 UX에 악영향 → 기본 24개 제한
        $limit = (int) apply_filters( 'jj_block_editor_palette_limit', 24 );
        if ( $limit > 0 && count( $items ) > $limit ) {
            $items = array_slice( $items, 0, $limit );
        }

        return $items;
    }

    /**
     * Gutenberg 폰트 사이즈 프리셋 생성
     *
     * @return array[]
     */
    private function build_wp_font_sizes( $options ) {
        $items = array();

        if ( isset( $options['typography'] ) && is_array( $options['typography'] ) ) {
            foreach ( $options['typography'] as $tag => $props ) {
                if ( ! is_array( $props ) ) {
                    continue;
                }
                $tag = sanitize_key( $tag );

                $size = null;
                if ( isset( $props['font_size']['desktop'] ) && is_numeric( $props['font_size']['desktop'] ) ) {
                    $size = (float) $props['font_size']['desktop'];
                } elseif ( isset( $props['font_size'] ) && is_numeric( $props['font_size'] ) ) {
                    $size = (float) $props['font_size'];
                }
                if ( ! $size ) {
                    continue;
                }

                $items[] = array(
                    'slug' => $this->preset_slug( $tag ),
                    'size' => $size,
                    'name' => strtoupper( $tag ),
                );
            }
        }

        // 슬러그 중복 제거
        $items = $this->merge_preset_arrays_by_slug( array(), $items );

        // 기본 16개 제한
        $limit = (int) apply_filters( 'jj_block_editor_font_sizes_limit', 16 );
        if ( $limit > 0 && count( $items ) > $limit ) {
            $items = array_slice( $items, 0, $limit );
        }

        return $items;
    }

    /**
     * theme.json preset 배열 병합 (slug 기준)
     *
     * @param array $existing
     * @param array $additions
     * @return array
     */
    private function merge_preset_arrays_by_slug( $existing, $additions ) {
        $merged = array();
        $index = array();

        if ( is_array( $existing ) ) {
            foreach ( $existing as $item ) {
                if ( ! is_array( $item ) || empty( $item['slug'] ) ) {
                    continue;
                }
                $slug = sanitize_key( $item['slug'] );
                $index[ $slug ] = true;
                $merged[] = $item;
            }
        }

        if ( is_array( $additions ) ) {
            foreach ( $additions as $item ) {
                if ( ! is_array( $item ) || empty( $item['slug'] ) ) {
                    continue;
                }
                $slug = sanitize_key( $item['slug'] );
                if ( isset( $index[ $slug ] ) ) {
                    continue;
                }
                $index[ $slug ] = true;
                $merged[] = $item;
            }
        }

        return $merged;
    }

    private function preset_slug( $raw ) {
        $raw = strtolower( (string) $raw );
        $raw = preg_replace( '/[^a-z0-9\\-\\_]/', '-', $raw );
        $raw = preg_replace( '/[\\-\\_]+/', '-', $raw );
        return trim( $raw, '-' );
    }

    private function preset_name( $group_key, $color_key ) {
        $group_key = sanitize_key( $group_key );
        $color_key = sanitize_key( $color_key );
        $group_label = $this->get_palette_label( $group_key );
        $color_label = $this->humanize_key( $color_key );
        return sprintf( '%s: %s', $group_label, $color_label );
    }

    private function get_palette_label( $palette_key ) {
        $palette_key = sanitize_key( $palette_key );
        $labels = array(
            'brand'       => __( '브랜드', 'acf-css-really-simple-style-management-center' ),
            'system'      => __( '시스템', 'acf-css-really-simple-style-management-center' ),
            'alternative' => __( '얼터너티브', 'acf-css-really-simple-style-management-center' ),
            'another'     => __( '어나더', 'acf-css-really-simple-style-management-center' ),
            'temporary'   => __( '임시', 'acf-css-really-simple-style-management-center' ),
        );
        return isset( $labels[ $palette_key ] ) ? $labels[ $palette_key ] : strtoupper( $palette_key );
    }

    private function humanize_key( $key ) {
        $key = (string) $key;
        $key = str_replace( array( '-', '_' ), ' ', $key );
        $key = preg_replace( '/\\s+/', ' ', $key );
        $key = trim( $key );
        return $key ? $key : __( '색상', 'acf-css-really-simple-style-management-center' );
    }

    private function sanitize_css_color( $value ) {
        if ( ! is_string( $value ) && ! is_numeric( $value ) ) {
            return '';
        }
        $value = trim( (string) $value );
        if ( $value === '' ) {
            return '';
        }

        $hex = sanitize_hex_color( $value );
        if ( $hex ) {
            return $hex;
        }

        // rgb()/rgba()/hsl()/hsla() 허용
        if ( preg_match( '/^(rgba?|hsla?)\\(.*\\)$/i', $value ) ) {
            // 매우 느슨한 허용이므로 기본적인 위험 문자만 제거
            $value = preg_replace( '/[;"<>]/', '', $value );
            return $value;
        }

        // 키워드 허용
        $keywords = array( 'transparent', 'currentColor', 'inherit' );
        if ( in_array( $value, $keywords, true ) ) {
            return $value;
        }

        return '';
    }

    private function sanitize_css_font_family( $value ) {
        if ( ! is_string( $value ) ) {
            return 'inherit';
        }
        $value = trim( $value );
        if ( $value === '' ) {
            return 'inherit';
        }
        // font-family는 쉼표/따옴표 허용
        $value = preg_replace( '/[<>;]/', '', $value );
        return $value;
    }

    private function build_typography_inline_style( $tag ) {
        $tag = sanitize_key( $tag );
        $prefix = '--jj-font-' . $tag;

        $rules = array(
            'font-family: var(' . $prefix . '-family, inherit)',
            'font-weight: var(' . $prefix . '-weight, inherit)',
            'font-style: var(' . $prefix . '-style, inherit)',
            'line-height: var(' . $prefix . '-line-height, normal)',
            'letter-spacing: var(' . $prefix . '-letter-spacing, normal)',
            'text-transform: var(' . $prefix . '-text-transform, none)',
            'font-size: var(' . $prefix . '-size, inherit)',
            'margin: 0 0 0.5em 0',
        );

        return implode( ';', $rules ) . ';';
    }

    /**
     * 블록 UI: 사용 가능한 팔레트 옵션 목록
     */
    private function get_palette_options_for_editor( $options ) {
        $items = array();
        if ( isset( $options['palettes'] ) && is_array( $options['palettes'] ) ) {
            foreach ( $options['palettes'] as $key => $palette ) {
                if ( ! is_array( $palette ) || empty( $palette ) ) {
                    continue;
                }
                $items[] = array(
                    'value' => sanitize_key( $key ),
                    'label' => $this->get_palette_label( $key ),
                );
            }
        }

        // 기본 팔레트가 없으면 하드코딩 폴백(UX)
        if ( empty( $items ) ) {
            $items = array(
                array( 'value' => 'brand', 'label' => $this->get_palette_label( 'brand' ) ),
                array( 'value' => 'system', 'label' => $this->get_palette_label( 'system' ) ),
            );
        }

        return $items;
    }

    /**
     * 블록 UI: 사용 가능한 타이포그래피 옵션 목록
     */
    private function get_typography_options_for_editor( $options ) {
        $items = array();

        if ( isset( $options['typography'] ) && is_array( $options['typography'] ) ) {
            foreach ( $options['typography'] as $key => $props ) {
                if ( ! is_array( $props ) ) {
                    continue;
                }
                $key = sanitize_key( $key );
                $items[] = array(
                    'value' => $key,
                    'label' => strtoupper( $key ),
                );
            }
        }

        // 폴백
        if ( empty( $items ) ) {
            foreach ( array( 'h1','h2','h3','p' ) as $k ) {
                $items[] = array(
                    'value' => $k,
                    'label' => strtoupper( $k ),
                );
            }
        }

        return $items;
    }
}


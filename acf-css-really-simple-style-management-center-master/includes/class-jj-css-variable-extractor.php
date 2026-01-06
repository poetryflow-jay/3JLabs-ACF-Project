<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [v25.2.0] CSS Variable Extractor & Generator
 *
 * CSS 변수 자동 추출/생성 시스템
 * - 외부 CSS/테마에서 CSS 변수 추출
 * - 현재 설정에서 CSS 변수 목록 생성
 * - CSS 변수 내보내기/가져오기
 * - 디자인 토큰 호환 포맷 지원
 *
 * @since 25.2.0
 */
class JJ_CSS_Variable_Extractor {

    private static $instance = null;

    /**
     * CSS 변수 카테고리 정의
     */
    private $variable_categories = array(
        'colors'     => array( 'label' => '색상', 'prefix' => '--jj-' ),
        'typography' => array( 'label' => '타이포그래피', 'prefix' => '--jj-font-' ),
        'spacing'    => array( 'label' => '간격', 'prefix' => '--jj-spacing-' ),
        'buttons'    => array( 'label' => '버튼', 'prefix' => '--jj-btn-' ),
        'forms'      => array( 'label' => '폼', 'prefix' => '--jj-form-' ),
        'system'     => array( 'label' => '시스템', 'prefix' => '--jj-sys-' ),
    );

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_jj_extract_css_variables', array( $this, 'ajax_extract_variables' ) );
        add_action( 'wp_ajax_jj_export_css_variables', array( $this, 'ajax_export_variables' ) );
        add_action( 'wp_ajax_jj_import_css_variables', array( $this, 'ajax_import_variables' ) );
        add_action( 'wp_ajax_jj_get_current_variables', array( $this, 'ajax_get_current_variables' ) );
        add_action( 'wp_ajax_jj_scan_theme_variables', array( $this, 'ajax_scan_theme_variables' ) );
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
            'jj-css-variable-extractor',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-css-variable-extractor.js',
            array( 'jquery', 'jj-common-utils' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '25.2.0',
            true
        );

        wp_localize_script(
            'jj-css-variable-extractor',
            'jjCSSVariableExtractor',
            array(
                'ajax_url'   => admin_url( 'admin-ajax.php' ),
                'nonce'      => wp_create_nonce( 'jj_css_variable_extractor_action' ),
                'categories' => $this->variable_categories,
                'strings'    => array(
                    'extracting'      => __( 'CSS 변수 추출 중...', 'acf-css-really-simple-style-management-center' ),
                    'extracted'       => __( 'CSS 변수가 추출되었습니다.', 'acf-css-really-simple-style-management-center' ),
                    'exporting'       => __( '내보내기 중...', 'acf-css-really-simple-style-management-center' ),
                    'exported'        => __( '내보내기가 완료되었습니다.', 'acf-css-really-simple-style-management-center' ),
                    'importing'       => __( '가져오기 중...', 'acf-css-really-simple-style-management-center' ),
                    'imported'        => __( '가져오기가 완료되었습니다.', 'acf-css-really-simple-style-management-center' ),
                    'scanning'        => __( '테마 스캔 중...', 'acf-css-really-simple-style-management-center' ),
                    'scanned'         => __( '테마 스캔이 완료되었습니다.', 'acf-css-really-simple-style-management-center' ),
                    'copy_success'    => __( '클립보드에 복사되었습니다.', 'acf-css-really-simple-style-management-center' ),
                    'no_variables'    => __( 'CSS 변수를 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ),
                    'confirm_import'  => __( '기존 설정을 덮어씁니다. 계속하시겠습니까?', 'acf-css-really-simple-style-management-center' ),
                ),
            )
        );

        wp_enqueue_style(
            'jj-css-variable-extractor',
            JJ_STYLE_GUIDE_URL . 'assets/css/jj-css-variable-extractor.css',
            array(),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '25.2.0'
        );
    }

    /**
     * 현재 저장된 설정에서 CSS 변수 목록 생성
     *
     * @return array
     */
    public function get_current_variables() {
        $options = get_option( JJ_STYLE_GUIDE_OPTIONS_KEY, array() );
        $variables = array();

        // 1. 브랜드 팔레트
        if ( isset( $options['palettes']['brand'] ) && is_array( $options['palettes']['brand'] ) ) {
            $brand = $options['palettes']['brand'];
            if ( ! empty( $brand['primary_color'] ) ) {
                $variables['--jj-primary-color'] = array(
                    'value'    => $brand['primary_color'],
                    'category' => 'colors',
                    'label'    => __( 'Primary 색상', 'acf-css-really-simple-style-management-center' ),
                );
            }
            if ( ! empty( $brand['primary_color_hover'] ) ) {
                $variables['--jj-primary-color-hover'] = array(
                    'value'    => $brand['primary_color_hover'],
                    'category' => 'colors',
                    'label'    => __( 'Primary 색상 (호버)', 'acf-css-really-simple-style-management-center' ),
                );
            }
            if ( ! empty( $brand['secondary_color'] ) ) {
                $variables['--jj-secondary-color'] = array(
                    'value'    => $brand['secondary_color'],
                    'category' => 'colors',
                    'label'    => __( 'Secondary 색상', 'acf-css-really-simple-style-management-center' ),
                );
            }
            if ( ! empty( $brand['secondary_color_hover'] ) ) {
                $variables['--jj-secondary-color-hover'] = array(
                    'value'    => $brand['secondary_color_hover'],
                    'category' => 'colors',
                    'label'    => __( 'Secondary 색상 (호버)', 'acf-css-really-simple-style-management-center' ),
                );
            }
        }

        // 2. 시스템 팔레트
        if ( isset( $options['palettes']['system'] ) && is_array( $options['palettes']['system'] ) ) {
            $system = $options['palettes']['system'];
            $system_vars = array(
                'site_bg'    => array( 'var' => '--jj-sys-site-bg', 'label' => __( '사이트 배경', 'acf-css-really-simple-style-management-center' ) ),
                'content_bg' => array( 'var' => '--jj-sys-content-bg', 'label' => __( '콘텐츠 배경', 'acf-css-really-simple-style-management-center' ) ),
                'text_color' => array( 'var' => '--jj-sys-text', 'label' => __( '텍스트 색상', 'acf-css-really-simple-style-management-center' ) ),
                'link_color' => array( 'var' => '--jj-sys-link', 'label' => __( '링크 색상', 'acf-css-really-simple-style-management-center' ) ),
            );
            foreach ( $system_vars as $key => $info ) {
                if ( ! empty( $system[ $key ] ) ) {
                    $variables[ $info['var'] ] = array(
                        'value'    => $system[ $key ],
                        'category' => 'system',
                        'label'    => $info['label'],
                    );
                }
            }
        }

        // 3. 타이포그래피
        if ( isset( $options['typography'] ) && is_array( $options['typography'] ) ) {
            foreach ( $options['typography'] as $tag => $props ) {
                $prefix = '--jj-font-' . esc_attr( $tag );
                $tag_upper = strtoupper( $tag );

                if ( ! empty( $props['font_family'] ) ) {
                    $variables[ $prefix . '-family' ] = array(
                        'value'    => $props['font_family'],
                        'category' => 'typography',
                        'label'    => sprintf( __( '%s 폰트 패밀리', 'acf-css-really-simple-style-management-center' ), $tag_upper ),
                    );
                }
                if ( ! empty( $props['font_weight'] ) ) {
                    $variables[ $prefix . '-weight' ] = array(
                        'value'    => $props['font_weight'],
                        'category' => 'typography',
                        'label'    => sprintf( __( '%s 폰트 굵기', 'acf-css-really-simple-style-management-center' ), $tag_upper ),
                    );
                }
                if ( isset( $props['font_size'] ) ) {
                    $size = is_array( $props['font_size'] ) && isset( $props['font_size']['desktop'] )
                        ? $props['font_size']['desktop']
                        : ( is_numeric( $props['font_size'] ) ? $props['font_size'] : '' );
                    if ( $size !== '' ) {
                        $variables[ $prefix . '-size' ] = array(
                            'value'    => $size . ( is_numeric( $size ) ? 'px' : '' ),
                            'category' => 'typography',
                            'label'    => sprintf( __( '%s 폰트 크기', 'acf-css-really-simple-style-management-center' ), $tag_upper ),
                        );
                    }
                }
                if ( isset( $props['line_height'] ) && $props['line_height'] !== '' ) {
                    $variables[ $prefix . '-line-height' ] = array(
                        'value'    => $props['line_height'] . ( is_numeric( $props['line_height'] ) ? 'em' : '' ),
                        'category' => 'typography',
                        'label'    => sprintf( __( '%s 행간', 'acf-css-really-simple-style-management-center' ), $tag_upper ),
                    );
                }
                if ( isset( $props['letter_spacing'] ) && $props['letter_spacing'] !== '' ) {
                    $variables[ $prefix . '-letter-spacing' ] = array(
                        'value'    => $props['letter_spacing'] . ( is_numeric( $props['letter_spacing'] ) ? 'px' : '' ),
                        'category' => 'typography',
                        'label'    => sprintf( __( '%s 자간', 'acf-css-really-simple-style-management-center' ), $tag_upper ),
                    );
                }
            }
        }

        // 4. 버튼 스타일
        if ( isset( $options['buttons'] ) && is_array( $options['buttons'] ) ) {
            foreach ( $options['buttons'] as $btn_type => $btn ) {
                $prefix = '--jj-btn-' . esc_attr( $btn_type );
                $btn_label = ucfirst( $btn_type );

                $btn_vars = array(
                    'background_color'       => array( 'suffix' => '-bg', 'label' => __( '배경색', 'acf-css-really-simple-style-management-center' ) ),
                    'text_color'             => array( 'suffix' => '-text', 'label' => __( '텍스트', 'acf-css-really-simple-style-management-center' ) ),
                    'border_color'           => array( 'suffix' => '-border', 'label' => __( '테두리', 'acf-css-really-simple-style-management-center' ) ),
                    'background_color_hover' => array( 'suffix' => '-bg-hover', 'label' => __( '배경색 (호버)', 'acf-css-really-simple-style-management-center' ) ),
                    'text_color_hover'       => array( 'suffix' => '-text-hover', 'label' => __( '텍스트 (호버)', 'acf-css-really-simple-style-management-center' ) ),
                    'border_color_hover'     => array( 'suffix' => '-border-hover', 'label' => __( '테두리 (호버)', 'acf-css-really-simple-style-management-center' ) ),
                    'border_radius'          => array( 'suffix' => '-border-radius', 'label' => __( '모서리 반경', 'acf-css-really-simple-style-management-center' ), 'unit' => 'px' ),
                );

                foreach ( $btn_vars as $key => $info ) {
                    if ( isset( $btn[ $key ] ) && $btn[ $key ] !== '' ) {
                        $value = $btn[ $key ];
                        if ( isset( $info['unit'] ) && is_numeric( $value ) ) {
                            $value .= $info['unit'];
                        }
                        $variables[ $prefix . $info['suffix'] ] = array(
                            'value'    => $value,
                            'category' => 'buttons',
                            'label'    => sprintf( __( '%s 버튼 - %s', 'acf-css-really-simple-style-management-center' ), $btn_label, $info['label'] ),
                        );
                    }
                }

                // 패딩
                if ( isset( $btn['padding'] ) && is_array( $btn['padding'] ) ) {
                    foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
                        if ( isset( $btn['padding'][ $side ] ) && $btn['padding'][ $side ] !== '' ) {
                            $variables[ $prefix . '-padding-' . $side ] = array(
                                'value'    => $btn['padding'][ $side ] . 'px',
                                'category' => 'buttons',
                                'label'    => sprintf( __( '%s 버튼 - 패딩 (%s)', 'acf-css-really-simple-style-management-center' ), $btn_label, $side ),
                            );
                        }
                    }
                }
            }
        }

        // 5. 폼 스타일
        if ( isset( $options['forms'] ) && is_array( $options['forms'] ) ) {
            // 라벨
            if ( isset( $options['forms']['label'] ) && is_array( $options['forms']['label'] ) ) {
                $label = $options['forms']['label'];
                $label_vars = array(
                    'font_weight'    => array( 'suffix' => '-weight', 'label' => __( '굵기', 'acf-css-really-simple-style-management-center' ) ),
                    'font_size'      => array( 'suffix' => '-size', 'label' => __( '크기', 'acf-css-really-simple-style-management-center' ), 'unit' => 'px' ),
                    'text_color'     => array( 'suffix' => '-color', 'label' => __( '색상', 'acf-css-really-simple-style-management-center' ) ),
                    'text_transform' => array( 'suffix' => '-text-transform', 'label' => __( '텍스트 변환', 'acf-css-really-simple-style-management-center' ) ),
                );
                foreach ( $label_vars as $key => $info ) {
                    if ( isset( $label[ $key ] ) && $label[ $key ] !== '' ) {
                        $value = $label[ $key ];
                        if ( isset( $info['unit'] ) && is_numeric( $value ) ) {
                            $value .= $info['unit'];
                        }
                        $variables[ '--jj-form-label' . $info['suffix'] ] = array(
                            'value'    => $value,
                            'category' => 'forms',
                            'label'    => sprintf( __( '폼 라벨 - %s', 'acf-css-really-simple-style-management-center' ), $info['label'] ),
                        );
                    }
                }
            }

            // 입력 필드
            if ( isset( $options['forms']['field'] ) && is_array( $options['forms']['field'] ) ) {
                $field = $options['forms']['field'];
                $field_vars = array(
                    'background_color'   => array( 'suffix' => '-bg', 'label' => __( '배경색', 'acf-css-really-simple-style-management-center' ) ),
                    'text_color'         => array( 'suffix' => '-text', 'label' => __( '텍스트', 'acf-css-really-simple-style-management-center' ) ),
                    'border_color'       => array( 'suffix' => '-border', 'label' => __( '테두리', 'acf-css-really-simple-style-management-center' ) ),
                    'border_color_focus' => array( 'suffix' => '-border-focus', 'label' => __( '테두리 (포커스)', 'acf-css-really-simple-style-management-center' ) ),
                    'border_radius'      => array( 'suffix' => '-border-radius', 'label' => __( '모서리 반경', 'acf-css-really-simple-style-management-center' ), 'unit' => 'px' ),
                    'border_width'       => array( 'suffix' => '-border-width', 'label' => __( '테두리 두께', 'acf-css-really-simple-style-management-center' ), 'unit' => 'px' ),
                );
                foreach ( $field_vars as $key => $info ) {
                    if ( isset( $field[ $key ] ) && $field[ $key ] !== '' ) {
                        $value = $field[ $key ];
                        if ( isset( $info['unit'] ) && is_numeric( $value ) ) {
                            $value .= $info['unit'];
                        }
                        $variables[ '--jj-form-input' . $info['suffix'] ] = array(
                            'value'    => $value,
                            'category' => 'forms',
                            'label'    => sprintf( __( '폼 입력 - %s', 'acf-css-really-simple-style-management-center' ), $info['label'] ),
                        );
                    }
                }

                // 패딩
                if ( isset( $field['padding'] ) && is_array( $field['padding'] ) ) {
                    foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
                        if ( isset( $field['padding'][ $side ] ) && $field['padding'][ $side ] !== '' ) {
                            $variables[ '--jj-form-input-padding-' . $side ] = array(
                                'value'    => $field['padding'][ $side ] . 'px',
                                'category' => 'forms',
                                'label'    => sprintf( __( '폼 입력 - 패딩 (%s)', 'acf-css-really-simple-style-management-center' ), $side ),
                            );
                        }
                    }
                }
            }
        }

        return $variables;
    }

    /**
     * CSS 문자열에서 CSS 변수 추출
     *
     * @param string $css CSS 문자열
     * @return array
     */
    public function extract_from_css( $css ) {
        $variables = array();

        // :root { --var-name: value; } 패턴 매칭
        $pattern = '/--([a-zA-Z0-9_-]+)\s*:\s*([^;]+);/';

        if ( preg_match_all( $pattern, $css, $matches, PREG_SET_ORDER ) ) {
            foreach ( $matches as $match ) {
                $var_name = '--' . $match[1];
                $value = trim( $match[2] );

                // 카테고리 추론
                $category = $this->infer_category( $var_name, $value );

                $variables[ $var_name ] = array(
                    'value'    => $value,
                    'category' => $category,
                    'label'    => $this->generate_label( $var_name ),
                    'source'   => 'extracted',
                );
            }
        }

        return $variables;
    }

    /**
     * URL에서 CSS를 가져와 변수 추출
     *
     * @param string $url CSS 파일 URL
     * @return array
     */
    public function extract_from_url( $url ) {
        $response = wp_remote_get( $url, array(
            'timeout' => 15,
            'sslverify' => false,
        ) );

        if ( is_wp_error( $response ) ) {
            return array( 'error' => $response->get_error_message() );
        }

        $css = wp_remote_retrieve_body( $response );
        return $this->extract_from_css( $css );
    }

    /**
     * 현재 테마의 CSS 파일들에서 변수 스캔
     *
     * @return array
     */
    public function scan_theme_variables() {
        $variables = array();
        $theme_dir = get_stylesheet_directory();

        // 스캔할 CSS 파일 목록
        $css_files = array(
            $theme_dir . '/style.css',
            $theme_dir . '/assets/css/style.css',
            $theme_dir . '/css/style.css',
            $theme_dir . '/assets/css/theme.css',
            $theme_dir . '/assets/css/variables.css',
            $theme_dir . '/assets/css/custom-properties.css',
        );

        // Glob으로 추가 CSS 파일 찾기
        $glob_patterns = array(
            $theme_dir . '/*.css',
            $theme_dir . '/css/*.css',
            $theme_dir . '/assets/css/*.css',
        );

        foreach ( $glob_patterns as $pattern ) {
            $found_files = glob( $pattern );
            if ( is_array( $found_files ) ) {
                $css_files = array_merge( $css_files, $found_files );
            }
        }

        $css_files = array_unique( $css_files );

        foreach ( $css_files as $file ) {
            if ( file_exists( $file ) && is_readable( $file ) ) {
                $css = file_get_contents( $file );
                $file_vars = $this->extract_from_css( $css );

                foreach ( $file_vars as $name => $data ) {
                    $data['source_file'] = basename( $file );
                    $variables[ $name ] = $data;
                }
            }
        }

        return $variables;
    }

    /**
     * CSS 변수를 다양한 포맷으로 내보내기
     *
     * @param string $format css|json|scss|design-tokens
     * @param array|null $variables 내보낼 변수 (null이면 현재 설정 사용)
     * @return string
     */
    public function export_variables( $format = 'css', $variables = null ) {
        if ( is_null( $variables ) ) {
            $variables = $this->get_current_variables();
        }

        switch ( $format ) {
            case 'json':
                return $this->export_as_json( $variables );
            case 'scss':
                return $this->export_as_scss( $variables );
            case 'design-tokens':
                return $this->export_as_design_tokens( $variables );
            case 'css':
            default:
                return $this->export_as_css( $variables );
        }
    }

    /**
     * CSS 포맷으로 내보내기
     */
    private function export_as_css( $variables ) {
        $css = "/**\n * ACF CSS Style Guide - CSS Variables\n * Generated: " . date( 'Y-m-d H:i:s' ) . "\n * https://3j-labs.com/\n */\n\n:root {\n";

        // 카테고리별 정렬
        $categorized = array();
        foreach ( $variables as $name => $data ) {
            $cat = isset( $data['category'] ) ? $data['category'] : 'other';
            if ( ! isset( $categorized[ $cat ] ) ) {
                $categorized[ $cat ] = array();
            }
            $categorized[ $cat ][ $name ] = $data;
        }

        foreach ( $categorized as $category => $vars ) {
            $cat_label = isset( $this->variable_categories[ $category ]['label'] )
                ? $this->variable_categories[ $category ]['label']
                : ucfirst( $category );

            $css .= "\n  /* === {$cat_label} === */\n";

            foreach ( $vars as $name => $data ) {
                $label = isset( $data['label'] ) ? $data['label'] : '';
                if ( $label ) {
                    $css .= "  /* {$label} */\n";
                }
                $css .= "  {$name}: {$data['value']};\n";
            }
        }

        $css .= "\n}\n";

        return $css;
    }

    /**
     * JSON 포맷으로 내보내기
     */
    private function export_as_json( $variables ) {
        $export = array(
            'version'    => '1.0',
            'generator'  => 'ACF CSS Style Guide',
            'generated'  => date( 'c' ),
            'variables'  => $variables,
        );

        return wp_json_encode( $export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    /**
     * SCSS 포맷으로 내보내기
     */
    private function export_as_scss( $variables ) {
        $scss = "//\n// ACF CSS Style Guide - SCSS Variables\n// Generated: " . date( 'Y-m-d H:i:s' ) . "\n// https://3j-labs.com/\n//\n\n";

        foreach ( $variables as $name => $data ) {
            // CSS 변수명을 SCSS 변수명으로 변환 (--jj-primary-color → $jj-primary-color)
            $scss_name = str_replace( '--', '$', $name );
            $scss .= "{$scss_name}: {$data['value']};\n";
        }

        $scss .= "\n// CSS 변수 매핑\n:root {\n";
        foreach ( $variables as $name => $data ) {
            $scss_name = str_replace( '--', '$', $name );
            $scss .= "  {$name}: {$scss_name};\n";
        }
        $scss .= "}\n";

        return $scss;
    }

    /**
     * Design Tokens (W3C 표준) 포맷으로 내보내기
     * @see https://design-tokens.github.io/community-group/format/
     */
    private function export_as_design_tokens( $variables ) {
        $tokens = array(
            '$schema' => 'https://design-tokens.github.io/community-group/format/',
            '$description' => 'ACF CSS Style Guide Design Tokens',
        );

        foreach ( $variables as $name => $data ) {
            // CSS 변수명을 토큰 경로로 변환 (--jj-primary-color → jj.primary.color)
            $token_path = str_replace( array( '--', '-' ), array( '', '.' ), $name );
            $path_parts = explode( '.', $token_path );

            // 중첩 구조 생성
            $current = &$tokens;
            foreach ( $path_parts as $i => $part ) {
                if ( $i === count( $path_parts ) - 1 ) {
                    // 마지막 레벨: 토큰 값 설정
                    $current[ $part ] = array(
                        '$value'       => $data['value'],
                        '$type'        => $this->infer_token_type( $data['value'] ),
                        '$description' => isset( $data['label'] ) ? $data['label'] : '',
                    );
                } else {
                    if ( ! isset( $current[ $part ] ) ) {
                        $current[ $part ] = array();
                    }
                    $current = &$current[ $part ];
                }
            }
        }

        return wp_json_encode( $tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    /**
     * JSON 또는 CSS에서 변수 가져오기
     *
     * @param string $content 가져올 콘텐츠
     * @param string $format json|css
     * @return array
     */
    public function import_variables( $content, $format = 'auto' ) {
        // 포맷 자동 감지
        if ( $format === 'auto' ) {
            $content_trimmed = trim( $content );
            if ( strpos( $content_trimmed, '{' ) === 0 || strpos( $content_trimmed, '[' ) === 0 ) {
                $format = 'json';
            } else {
                $format = 'css';
            }
        }

        if ( $format === 'json' ) {
            return $this->import_from_json( $content );
        }

        return $this->extract_from_css( $content );
    }

    /**
     * JSON에서 변수 가져오기
     */
    private function import_from_json( $json ) {
        $data = json_decode( $json, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return array( 'error' => __( 'JSON 파싱 오류: ', 'acf-css-really-simple-style-management-center' ) . json_last_error_msg() );
        }

        // 일반 JSON 포맷
        if ( isset( $data['variables'] ) ) {
            return $data['variables'];
        }

        // Design Tokens 포맷 파싱
        if ( isset( $data['$schema'] ) ) {
            return $this->parse_design_tokens( $data );
        }

        // 단순 키-값 쌍
        $variables = array();
        foreach ( $data as $name => $value ) {
            if ( strpos( $name, '--' ) === 0 ) {
                $variables[ $name ] = array(
                    'value'    => is_array( $value ) ? ( $value['value'] ?? $value['$value'] ?? '' ) : $value,
                    'category' => $this->infer_category( $name, '' ),
                    'label'    => $this->generate_label( $name ),
                );
            }
        }

        return $variables;
    }

    /**
     * Design Tokens 포맷 파싱
     */
    private function parse_design_tokens( $tokens, $prefix = '--' ) {
        $variables = array();

        foreach ( $tokens as $key => $value ) {
            if ( strpos( $key, '$' ) === 0 ) {
                continue; // 메타데이터 스킵
            }

            if ( is_array( $value ) ) {
                if ( isset( $value['$value'] ) ) {
                    // 토큰 값
                    $var_name = $prefix . str_replace( '.', '-', $key );
                    $variables[ $var_name ] = array(
                        'value'    => $value['$value'],
                        'category' => $this->infer_category( $var_name, $value['$value'] ),
                        'label'    => isset( $value['$description'] ) ? $value['$description'] : $this->generate_label( $var_name ),
                    );
                } else {
                    // 중첩 구조 재귀 처리
                    $nested = $this->parse_design_tokens( $value, $prefix . $key . '-' );
                    $variables = array_merge( $variables, $nested );
                }
            }
        }

        return $variables;
    }

    /**
     * 변수명/값으로 카테고리 추론
     */
    private function infer_category( $name, $value ) {
        $name_lower = strtolower( $name );

        // 접두사로 판단
        if ( strpos( $name_lower, '--jj-btn-' ) !== false ) return 'buttons';
        if ( strpos( $name_lower, '--jj-form-' ) !== false ) return 'forms';
        if ( strpos( $name_lower, '--jj-font-' ) !== false ) return 'typography';
        if ( strpos( $name_lower, '--jj-sys-' ) !== false ) return 'system';
        if ( strpos( $name_lower, '--jj-spacing-' ) !== false ) return 'spacing';

        // 키워드로 판단
        if ( preg_match( '/color|bg|background|border-color/i', $name_lower ) ) return 'colors';
        if ( preg_match( '/font|text|letter|line-height/i', $name_lower ) ) return 'typography';
        if ( preg_match( '/padding|margin|gap|spacing/i', $name_lower ) ) return 'spacing';
        if ( preg_match( '/button|btn/i', $name_lower ) ) return 'buttons';
        if ( preg_match( '/form|input|label|field/i', $name_lower ) ) return 'forms';

        // 값으로 판단
        if ( preg_match( '/^#[0-9a-f]{3,8}$/i', $value ) || preg_match( '/^rgba?\(/i', $value ) || preg_match( '/^hsla?\(/i', $value ) ) {
            return 'colors';
        }

        return 'other';
    }

    /**
     * Design Tokens 타입 추론
     */
    private function infer_token_type( $value ) {
        if ( preg_match( '/^#[0-9a-f]{3,8}$/i', $value ) || preg_match( '/^rgba?\(/i', $value ) || preg_match( '/^hsla?\(/i', $value ) ) {
            return 'color';
        }
        if ( preg_match( '/^\d+(\.\d+)?(px|em|rem|%|vw|vh)?$/i', $value ) ) {
            return 'dimension';
        }
        if ( preg_match( '/^\d+(\.\d+)?$/i', $value ) ) {
            return 'number';
        }
        if ( preg_match( '/font-family|sans-serif|serif|monospace/i', $value ) ) {
            return 'fontFamily';
        }
        if ( preg_match( '/^\d+$/i', $value ) && (int) $value >= 100 && (int) $value <= 900 ) {
            return 'fontWeight';
        }

        return 'string';
    }

    /**
     * 변수명으로 라벨 생성
     */
    private function generate_label( $name ) {
        // --jj-primary-color → Primary Color
        $label = str_replace( array( '--jj-', '--', '-' ), array( '', '', ' ' ), $name );
        return ucwords( trim( $label ) );
    }

    /**
     * 가져온 변수를 플러그인 설정에 적용
     *
     * @param array $variables
     * @param bool $merge 기존 설정과 병합할지 여부
     * @return bool
     */
    public function apply_variables_to_options( $variables, $merge = true ) {
        $options = $merge ? get_option( JJ_STYLE_GUIDE_OPTIONS_KEY, array() ) : array();

        foreach ( $variables as $name => $data ) {
            $value = isset( $data['value'] ) ? $data['value'] : $data;
            $this->set_option_from_variable( $options, $name, $value );
        }

        return update_option( JJ_STYLE_GUIDE_OPTIONS_KEY, $options );
    }

    /**
     * CSS 변수명으로 옵션 설정
     */
    private function set_option_from_variable( &$options, $var_name, $value ) {
        // 브랜드 색상
        $brand_map = array(
            '--jj-primary-color'        => 'primary_color',
            '--jj-primary-color-hover'  => 'primary_color_hover',
            '--jj-secondary-color'      => 'secondary_color',
            '--jj-secondary-color-hover'=> 'secondary_color_hover',
        );
        if ( isset( $brand_map[ $var_name ] ) ) {
            if ( ! isset( $options['palettes']['brand'] ) ) {
                $options['palettes']['brand'] = array();
            }
            $options['palettes']['brand'][ $brand_map[ $var_name ] ] = $value;
            return;
        }

        // 시스템 색상
        $system_map = array(
            '--jj-sys-site-bg'    => 'site_bg',
            '--jj-sys-content-bg' => 'content_bg',
            '--jj-sys-text'       => 'text_color',
            '--jj-sys-link'       => 'link_color',
        );
        if ( isset( $system_map[ $var_name ] ) ) {
            if ( ! isset( $options['palettes']['system'] ) ) {
                $options['palettes']['system'] = array();
            }
            $options['palettes']['system'][ $system_map[ $var_name ] ] = $value;
            return;
        }

        // 타이포그래피 (--jj-font-{tag}-{property})
        if ( preg_match( '/^--jj-font-([a-z0-9]+)-(.+)$/', $var_name, $matches ) ) {
            $tag = $matches[1];
            $prop = $matches[2];

            $prop_map = array(
                'family'         => 'font_family',
                'weight'         => 'font_weight',
                'size'           => 'font_size',
                'line-height'    => 'line_height',
                'letter-spacing' => 'letter_spacing',
                'style'          => 'font_style',
                'text-transform' => 'text_transform',
            );

            if ( isset( $prop_map[ $prop ] ) ) {
                if ( ! isset( $options['typography'][ $tag ] ) ) {
                    $options['typography'][ $tag ] = array();
                }
                // 숫자값에서 단위 제거
                $clean_value = preg_replace( '/^(\d+\.?\d*)(px|em|rem)?$/i', '$1', $value );
                $options['typography'][ $tag ][ $prop_map[ $prop ] ] = $clean_value;
            }
            return;
        }

        // 버튼 (--jj-btn-{type}-{property})
        if ( preg_match( '/^--jj-btn-([a-z]+)-(.+)$/', $var_name, $matches ) ) {
            $btn_type = $matches[1];
            $prop = $matches[2];

            $prop_map = array(
                'bg'            => 'background_color',
                'text'          => 'text_color',
                'border'        => 'border_color',
                'bg-hover'      => 'background_color_hover',
                'text-hover'    => 'text_color_hover',
                'border-hover'  => 'border_color_hover',
                'border-radius' => 'border_radius',
            );

            if ( isset( $prop_map[ $prop ] ) ) {
                if ( ! isset( $options['buttons'][ $btn_type ] ) ) {
                    $options['buttons'][ $btn_type ] = array();
                }
                $clean_value = preg_replace( '/^(\d+\.?\d*)(px)?$/i', '$1', $value );
                $options['buttons'][ $btn_type ][ $prop_map[ $prop ] ] = $clean_value;
                return;
            }

            // 패딩
            if ( preg_match( '/^padding-(top|right|bottom|left)$/', $prop, $pad_match ) ) {
                if ( ! isset( $options['buttons'][ $btn_type ]['padding'] ) ) {
                    $options['buttons'][ $btn_type ]['padding'] = array();
                }
                $clean_value = preg_replace( '/^(\d+\.?\d*)(px)?$/i', '$1', $value );
                $options['buttons'][ $btn_type ]['padding'][ $pad_match[1] ] = $clean_value;
            }
            return;
        }

        // 폼 라벨 (--jj-form-label-{property})
        if ( preg_match( '/^--jj-form-label-(.+)$/', $var_name, $matches ) ) {
            $prop = $matches[1];

            $prop_map = array(
                'weight'         => 'font_weight',
                'size'           => 'font_size',
                'color'          => 'text_color',
                'text-transform' => 'text_transform',
            );

            if ( isset( $prop_map[ $prop ] ) ) {
                if ( ! isset( $options['forms']['label'] ) ) {
                    $options['forms']['label'] = array();
                }
                $clean_value = preg_replace( '/^(\d+\.?\d*)(px)?$/i', '$1', $value );
                $options['forms']['label'][ $prop_map[ $prop ] ] = $clean_value;
            }
            return;
        }

        // 폼 입력 (--jj-form-input-{property})
        if ( preg_match( '/^--jj-form-input-(.+)$/', $var_name, $matches ) ) {
            $prop = $matches[1];

            $prop_map = array(
                'bg'            => 'background_color',
                'text'          => 'text_color',
                'border'        => 'border_color',
                'border-focus'  => 'border_color_focus',
                'border-radius' => 'border_radius',
                'border-width'  => 'border_width',
            );

            if ( isset( $prop_map[ $prop ] ) ) {
                if ( ! isset( $options['forms']['field'] ) ) {
                    $options['forms']['field'] = array();
                }
                $clean_value = preg_replace( '/^(\d+\.?\d*)(px)?$/i', '$1', $value );
                $options['forms']['field'][ $prop_map[ $prop ] ] = $clean_value;
                return;
            }

            // 패딩
            if ( preg_match( '/^padding-(top|right|bottom|left)$/', $prop, $pad_match ) ) {
                if ( ! isset( $options['forms']['field']['padding'] ) ) {
                    $options['forms']['field']['padding'] = array();
                }
                $clean_value = preg_replace( '/^(\d+\.?\d*)(px)?$/i', '$1', $value );
                $options['forms']['field']['padding'][ $pad_match[1] ] = $clean_value;
            }
            return;
        }
    }

    // ====== AJAX 핸들러 ======

    /**
     * AJAX: CSS에서 변수 추출
     */
    public function ajax_extract_variables() {
        if ( ! check_ajax_referer( 'jj_css_variable_extractor_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $css = isset( $_POST['css'] ) ? wp_unslash( $_POST['css'] ) : '';
        $url = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : '';

        if ( $url ) {
            $variables = $this->extract_from_url( $url );
        } elseif ( $css ) {
            $variables = $this->extract_from_css( $css );
        } else {
            wp_send_json_error( array( 'message' => __( 'CSS 또는 URL을 입력해주세요.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        if ( isset( $variables['error'] ) ) {
            wp_send_json_error( $variables );
            return;
        }

        wp_send_json_success( array(
            'variables' => $variables,
            'count'     => count( $variables ),
        ) );
    }

    /**
     * AJAX: CSS 변수 내보내기
     */
    public function ajax_export_variables() {
        if ( ! check_ajax_referer( 'jj_css_variable_extractor_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $format = isset( $_POST['format'] ) ? sanitize_text_field( $_POST['format'] ) : 'css';
        $output = $this->export_variables( $format );

        wp_send_json_success( array(
            'output' => $output,
            'format' => $format,
        ) );
    }

    /**
     * AJAX: CSS 변수 가져오기
     */
    public function ajax_import_variables() {
        if ( ! check_ajax_referer( 'jj_css_variable_extractor_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $content = isset( $_POST['content'] ) ? wp_unslash( $_POST['content'] ) : '';
        $format = isset( $_POST['format'] ) ? sanitize_text_field( $_POST['format'] ) : 'auto';
        $apply = isset( $_POST['apply'] ) && $_POST['apply'] === 'true';
        $merge = isset( $_POST['merge'] ) && $_POST['merge'] === 'true';

        if ( empty( $content ) ) {
            wp_send_json_error( array( 'message' => __( '가져올 내용을 입력해주세요.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $variables = $this->import_variables( $content, $format );

        if ( isset( $variables['error'] ) ) {
            wp_send_json_error( $variables );
            return;
        }

        if ( $apply ) {
            $this->apply_variables_to_options( $variables, $merge );
        }

        wp_send_json_success( array(
            'variables' => $variables,
            'count'     => count( $variables ),
            'applied'   => $apply,
        ) );
    }

    /**
     * AJAX: 현재 설정의 CSS 변수 조회
     */
    public function ajax_get_current_variables() {
        if ( ! check_ajax_referer( 'jj_css_variable_extractor_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $variables = $this->get_current_variables();

        wp_send_json_success( array(
            'variables' => $variables,
            'count'     => count( $variables ),
        ) );
    }

    /**
     * AJAX: 테마 CSS 변수 스캔
     */
    public function ajax_scan_theme_variables() {
        if ( ! check_ajax_referer( 'jj_css_variable_extractor_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            return;
        }

        $variables = $this->scan_theme_variables();

        wp_send_json_success( array(
            'variables' => $variables,
            'count'     => count( $variables ),
            'theme'     => wp_get_theme()->get( 'Name' ),
        ) );
    }
}

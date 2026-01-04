<?php
/**
 * JJ Style Guide Frontend - 프론트엔드 스타일 적용 클래스
 * 
 * 저장된 스타일 가이드 옵션을 프론트엔드에 적용합니다.
 * CSS 변수 생성, 인라인 스타일 출력, 캐싱을 담당합니다.
 * 
 * @package ACF_CSS_Style_Guide
 * @version 22.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class JJ_Style_Guide_Frontend
 */
class JJ_Style_Guide_Frontend {

    /**
     * 싱글톤 인스턴스
     * @var JJ_Style_Guide_Frontend|null
     */
    private static $instance = null;

    /**
     * 옵션 키
     * @var string
     */
    private $option_key = 'jj_style_guide_options';

    /**
     * 캐시 키
     * @var string
     */
    private $cache_key = 'jj_style_guide_css_cache';

    /**
     * 생성자
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * 싱글톤 인스턴스 반환
     * @return JJ_Style_Guide_Frontend
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
        // 프론트엔드 CSS 출력
        add_action( 'wp_head', array( $this, 'output_css_variables' ), 1 );
        
        // 블록 에디터 지원
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_styles' ) );
        
        // 커스터마이저 프리뷰
        add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );
        
        // 옵션 업데이트 시 캐시 삭제
        add_action( 'update_option_' . $this->option_key, array( $this, 'clear_css_cache' ) );
    }

    /**
     * CSS 변수 출력
     */
    public function output_css_variables() {
        $css = $this->get_cached_css();
        
        if ( ! empty( $css ) ) {
            echo '<style id="jj-style-guide-css-vars">' . "\n";
            echo $css;
            echo '</style>' . "\n";
        }
    }

    /**
     * 캐시된 CSS 가져오기
     * @return string
     */
    private function get_cached_css() {
        $options = get_option( $this->option_key, array() );
        
        // 캐시 비활성화 시 바로 생성
        if ( empty( $options['settings']['cache_enabled'] ) ) {
            return $this->generate_css();
        }
        
        $cached = get_transient( $this->cache_key );
        
        if ( false !== $cached ) {
            return $cached;
        }
        
        $css = $this->generate_css();
        set_transient( $this->cache_key, $css, DAY_IN_SECONDS );
        
        return $css;
    }

    /**
     * CSS 생성
     * @return string
     */
    public function generate_css() {
        $options = get_option( $this->option_key, array() );
        
        if ( empty( $options ) ) {
            return '';
        }
        
        $css = ":root {\n";
        
        // 색상 변수
        if ( ! empty( $options['colors'] ) ) {
            $css .= "  /* Colors */\n";
            foreach ( $options['colors'] as $name => $value ) {
                $css .= "  --jj-color-{$name}: {$value};\n";
                
                // RGB 변환 값도 추가 (투명도 조절용)
                if ( class_exists( 'JJ_Common_Utils' ) ) {
                    $rgb = JJ_Common_Utils::hex_to_rgb( $value );
                    if ( $rgb ) {
                        $css .= "  --jj-color-{$name}-rgb: {$rgb['r']}, {$rgb['g']}, {$rgb['b']};\n";
                    }
                }
            }
        }
        
        // 타이포그래피 변수
        if ( ! empty( $options['typography'] ) ) {
            $css .= "\n  /* Typography */\n";
            foreach ( $options['typography'] as $name => $value ) {
                $var_name = str_replace( '_', '-', $name );
                // [v22.4.8] 배열을 문자열로 변환하는 오류 수정 - 배열인 경우 처리
                if ( is_array( $value ) ) {
                    // 배열인 경우 JSON으로 변환하거나 첫 번째 값 사용
                    $value = ! empty( $value ) ? ( is_string( $value[0] ) ? $value[0] : json_encode( $value ) ) : '';
                }
                $css .= "  --jj-{$var_name}: {$value};\n";
            }
        }
        
        // 간격 변수
        if ( ! empty( $options['spacing'] ) ) {
            $css .= "\n  /* Spacing */\n";
            foreach ( $options['spacing'] as $name => $value ) {
                $css .= "  --jj-spacing-{$name}: {$value};\n";
            }
        }
        
        // 테두리 변수
        if ( ! empty( $options['borders'] ) ) {
            $css .= "\n  /* Borders */\n";
            foreach ( $options['borders'] as $name => $value ) {
                $var_name = str_replace( '_', '-', $name );
                $css .= "  --jj-border-{$var_name}: {$value};\n";
            }
        }
        
        // 그림자 변수
        if ( ! empty( $options['shadows'] ) ) {
            $css .= "\n  /* Shadows */\n";
            foreach ( $options['shadows'] as $name => $value ) {
                $css .= "  --jj-shadow-{$name}: {$value};\n";
            }
        }
        
        $css .= "}\n";
        
        // 유틸리티 클래스 생성
        $css .= $this->generate_utility_classes( $options );
        
        return $css;
    }

    /**
     * 유틸리티 클래스 생성
     * @param array $options
     * @return string
     */
    private function generate_utility_classes( $options ) {
        $css = "\n/* Utility Classes */\n";
        
        // 색상 유틸리티
        if ( ! empty( $options['colors'] ) ) {
            foreach ( $options['colors'] as $name => $value ) {
                $css .= ".jj-text-{$name} { color: var(--jj-color-{$name}); }\n";
                $css .= ".jj-bg-{$name} { background-color: var(--jj-color-{$name}); }\n";
                $css .= ".jj-border-{$name} { border-color: var(--jj-color-{$name}); }\n";
            }
        }
        
        // 간격 유틸리티
        if ( ! empty( $options['spacing'] ) ) {
            foreach ( $options['spacing'] as $name => $value ) {
                $css .= ".jj-m-{$name} { margin: var(--jj-spacing-{$name}); }\n";
                $css .= ".jj-p-{$name} { padding: var(--jj-spacing-{$name}); }\n";
                $css .= ".jj-gap-{$name} { gap: var(--jj-spacing-{$name}); }\n";
            }
        }
        
        return $css;
    }

    /**
     * 블록 에디터 스타일 enqueue
     */
    public function enqueue_editor_styles() {
        $css = $this->generate_css();
        
        if ( ! empty( $css ) ) {
            wp_add_inline_style( 'wp-edit-blocks', $css );
        }
    }

    /**
     * 커스터마이저 프리뷰 초기화
     */
    public function customize_preview_init() {
        add_action( 'wp_footer', array( $this, 'customize_preview_script' ), 100 );
    }

    /**
     * 커스터마이저 프리뷰 스크립트
     */
    public function customize_preview_script() {
        ?>
        <script type="text/javascript">
            (function($) {
                wp.customize.bind('preview-ready', function() {
                    // 스타일 변경 시 실시간 업데이트
                    wp.customize.preview.bind('jj-style-guide-update', function(data) {
                        var styleEl = document.getElementById('jj-style-guide-css-vars');
                        if (styleEl && data.css) {
                            styleEl.textContent = data.css;
                        }
                    });
                });
            })(jQuery);
        </script>
        <?php
    }

    /**
     * CSS 캐시 삭제
     */
    public function clear_css_cache() {
        delete_transient( $this->cache_key );
    }

    /**
     * 특정 색상 변수 값 가져오기
     * @param string $name
     * @param string $default
     * @return string
     */
    public function get_color( $name, $default = '' ) {
        $options = get_option( $this->option_key, array() );
        return isset( $options['colors'][ $name ] ) ? $options['colors'][ $name ] : $default;
    }

    /**
     * 특정 타이포그래피 변수 값 가져오기
     * @param string $name
     * @param string $default
     * @return string
     */
    public function get_typography( $name, $default = '' ) {
        $options = get_option( $this->option_key, array() );
        return isset( $options['typography'][ $name ] ) ? $options['typography'][ $name ] : $default;
    }

    /**
     * 모든 CSS 변수를 배열로 반환
     * @return array
     */
    public function get_all_variables() {
        $options = get_option( $this->option_key, array() );
        $variables = array();
        
        if ( ! empty( $options['colors'] ) ) {
            foreach ( $options['colors'] as $name => $value ) {
                $variables["--jj-color-{$name}"] = $value;
            }
        }
        
        if ( ! empty( $options['typography'] ) ) {
            foreach ( $options['typography'] as $name => $value ) {
                $var_name = str_replace( '_', '-', $name );
                $variables["--jj-{$var_name}"] = $value;
            }
        }
        
        if ( ! empty( $options['spacing'] ) ) {
            foreach ( $options['spacing'] as $name => $value ) {
                $variables["--jj-spacing-{$name}"] = $value;
            }
        }
        
        return $variables;
    }
}

// 인스턴스 초기화 (init 훅에서)
add_action( 'init', function() {
    JJ_Style_Guide_Frontend::instance();
}, 5 );

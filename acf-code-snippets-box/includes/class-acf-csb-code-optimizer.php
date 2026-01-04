<?php
/**
 * ACF Code Snippets Box - Code Optimizer
 *
 * 자동 코드 최적화: CSS/JS 압축, 불필요한 코드 제거, Critical CSS 추출
 *
 * @package ACF_Code_Snippets_Box
 * @since 4.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Code Optimizer 클래스
 */
class ACF_CSB_Code_Optimizer {

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
     * 생성자
     */
    private function __construct() {
        add_action( 'wp_ajax_acf_csb_optimize_code', array( $this, 'ajax_optimize_code' ) );
        add_action( 'wp_ajax_acf_csb_extract_critical_css', array( $this, 'ajax_extract_critical_css' ) );
        add_action( 'wp_ajax_acf_csb_minify_code', array( $this, 'ajax_minify_code' ) );
    }

    /**
     * 코드 최적화 (종합)
     * 
     * @param string $code 코드
     * @param string $code_type 코드 타입
     * @param array $options 최적화 옵션
     * @return array 최적화 결과
     */
    public function optimize( $code, $code_type = 'css', $options = array() ) {
        $defaults = array(
            'minify'           => true,
            'remove_comments'  => true,
            'remove_whitespace' => true,
            'optimize_selectors' => false, // CSS only
            'remove_unused'    => false,
        );

        $options = wp_parse_args( $options, $defaults );

        $original_size = strlen( $code );
        $optimized_code = $code;

        switch ( $code_type ) {
            case 'css':
                $optimized_code = $this->optimize_css( $code, $options );
                break;
            case 'js':
                $optimized_code = $this->optimize_javascript( $code, $options );
                break;
            case 'php':
                $optimized_code = $this->optimize_php( $code, $options );
                break;
            case 'html':
                $optimized_code = $this->optimize_html( $code, $options );
                break;
        }

        $optimized_size = strlen( $optimized_code );
        $savings = $original_size - $optimized_size;
        $savings_percent = $original_size > 0 ? round( ( $savings / $original_size ) * 100, 2 ) : 0;

        return array(
            'original_code'    => $code,
            'optimized_code'   => $optimized_code,
            'original_size'    => $original_size,
            'optimized_size'   => $optimized_size,
            'savings'          => $savings,
            'savings_percent'  => $savings_percent,
            'code_type'        => $code_type,
        );
    }

    /**
     * CSS 최적화
     */
    private function optimize_css( $code, $options ) {
        $optimized = $code;

        // 주석 제거
        if ( $options['remove_comments'] ) {
            $optimized = preg_replace( '/\/\*[^*]*\*+([^\/][^*]*\*+)*\//', '', $optimized );
        }

        // 불필요한 공백 제거
        if ( $options['remove_whitespace'] ) {
            // 선택자와 중괄호 사이 공백 제거
            $optimized = preg_replace( '/\s*{\s*/', '{', $optimized );
            $optimized = preg_replace( '/\s*}\s*/', '}', $optimized );
            $optimized = preg_replace( '/\s*:\s*/', ':', $optimized );
            $optimized = preg_replace( '/\s*;\s*/', ';', $optimized );
            $optimized = preg_replace( '/\s*,\s*/', ',', $optimized );
            
            // 여러 공백을 하나로
            $optimized = preg_replace( '/\s+/', ' ', $optimized );
            
            // 줄바꿈 제거
            $optimized = str_replace( array( "\r\n", "\r", "\n" ), '', $optimized );
        }

        // 선택자 최적화
        if ( $options['optimize_selectors'] ) {
            // 중복 선택자 병합
            $optimized = $this->merge_duplicate_selectors( $optimized );
        }

        // 미사용 코드 제거 (기본적인 것만)
        if ( $options['remove_unused'] ) {
            // 빈 규칙 제거
            $optimized = preg_replace( '/[^{}]+{\s*}/', '', $optimized );
        }

        return trim( $optimized );
    }

    /**
     * JavaScript 최적화
     */
    private function optimize_javascript( $code, $options ) {
        $optimized = $code;

        // 주석 제거
        if ( $options['remove_comments'] ) {
            // 한 줄 주석
            $optimized = preg_replace( '/\/\/.*$/m', '', $optimized );
            // 블록 주석
            $optimized = preg_replace( '/\/\*[^*]*\*+([^\/][^*]*\*+)*\//', '', $optimized );
        }

        // 불필요한 공백 제거
        if ( $options['remove_whitespace'] ) {
            // 문자열 내부는 보호
            $strings = array();
            $optimized = preg_replace_callback( '/(["\'])(?:(?=(\\?))\2.)*?\1/', function( $match ) use ( &$strings ) {
                $key = '___STRING_' . count( $strings ) . '___';
                $strings[ $key ] = $match[0];
                return $key;
            }, $optimized );

            // 공백 정리
            $optimized = preg_replace( '/\s+/', ' ', $optimized );
            $optimized = preg_replace( '/\s*([{}();,=+\-*\/])\s*/', '$1', $optimized );
            $optimized = str_replace( array( "\r\n", "\r", "\n" ), '', $optimized );

            // 문자열 복원
            foreach ( $strings as $key => $value ) {
                $optimized = str_replace( $key, $value, $optimized );
            }
        }

        // console.log 제거 (프로덕션)
        if ( $options['remove_unused'] ) {
            $optimized = preg_replace( '/console\.(log|debug|info)\s*\([^)]*\)\s*;?/i', '', $optimized );
        }

        return trim( $optimized );
    }

    /**
     * PHP 최적화
     */
    private function optimize_php( $code, $options ) {
        $optimized = $code;

        // 주석 제거
        if ( $options['remove_comments'] ) {
            // 한 줄 주석
            $optimized = preg_replace( '/\/\/.*$/m', '', $optimized );
            // 블록 주석
            $optimized = preg_replace( '/\/\*[^*]*\*+([^\/][^*]*\*+)*\//', '', $optimized );
            // # 주석
            $optimized = preg_replace( '/#.*$/m', '', $optimized );
        }

        // 불필요한 공백 제거
        if ( $options['remove_whitespace'] ) {
            // 여러 공백을 하나로
            $optimized = preg_replace( '/\s+/', ' ', $optimized );
            // 줄 끝 공백 제거
            $optimized = preg_replace( '/[ \t]+$/m', '', $optimized );
        }

        return trim( $optimized );
    }

    /**
     * HTML 최적화
     */
    private function optimize_html( $code, $options ) {
        $optimized = $code;

        // 주석 제거
        if ( $options['remove_comments'] ) {
            $optimized = preg_replace( '/<!--[^>]*-->/', '', $optimized );
        }

        // 불필요한 공백 제거
        if ( $options['remove_whitespace'] ) {
            // 태그 사이 공백 정리
            $optimized = preg_replace( '/>\s+</', '><', $optimized );
            // 여러 공백을 하나로
            $optimized = preg_replace( '/\s+/', ' ', $optimized );
        }

        return trim( $optimized );
    }

    /**
     * 중복 선택자 병합 (CSS)
     */
    private function merge_duplicate_selectors( $css ) {
        // 간단한 구현: 실제로는 더 복잡한 파싱이 필요
        $rules = array();
        $pattern = '/([^{]+)\{([^}]+)\}/';

        preg_match_all( $pattern, $css, $matches, PREG_SET_ORDER );

        foreach ( $matches as $match ) {
            $selector = trim( $match[1] );
            $properties = trim( $match[2] );

            if ( ! isset( $rules[ $selector ] ) ) {
                $rules[ $selector ] = array();
            }

            $props = explode( ';', $properties );
            foreach ( $props as $prop ) {
                $prop = trim( $prop );
                if ( ! empty( $prop ) ) {
                    $rules[ $selector ][] = $prop;
                }
            }
        }

        // 병합된 CSS 재구성
        $merged = '';
        foreach ( $rules as $selector => $properties ) {
            $merged .= $selector . '{' . implode( ';', array_unique( $properties ) ) . '}';
        }

        return $merged;
    }

    /**
     * Critical CSS 추출
     * 
     * @param string $css CSS 코드
     * @param array $selectors 중요한 선택자 목록
     * @return string Critical CSS
     */
    public function extract_critical_css( $css, $selectors = array() ) {
        if ( empty( $selectors ) ) {
            // 기본적으로 위쪽에 있는 스타일 추출
            $lines = explode( "\n", $css );
            $critical_lines = array_slice( $lines, 0, min( 50, count( $lines ) ) );
            return implode( "\n", $critical_lines );
        }

        $critical_css = '';
        $rules = array();

        // CSS 규칙 파싱
        preg_match_all( '/([^{]+)\{([^}]+)\}/', $css, $matches, PREG_SET_ORDER );

        foreach ( $matches as $match ) {
            $selector = trim( $match[1] );
            $properties = trim( $match[2] );

            // 중요한 선택자인지 확인
            foreach ( $selectors as $important_selector ) {
                if ( strpos( $selector, $important_selector ) !== false ) {
                    $rules[] = $selector . '{' . $properties . '}';
                    break;
                }
            }
        }

        return implode( "\n", $rules );
    }

    /**
     * 코드 압축 (Minify)
     */
    public function minify( $code, $code_type = 'css' ) {
        return $this->optimize( $code, $code_type, array(
            'minify'           => true,
            'remove_comments'  => true,
            'remove_whitespace' => true,
        ) )['optimized_code'];
    }

    /**
     * AJAX: 코드 최적화
     */
    public function ajax_optimize_code() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $code = isset( $_POST['code'] ) ? wp_unslash( $_POST['code'] ) : '';
        $code_type = isset( $_POST['code_type'] ) ? sanitize_text_field( $_POST['code_type'] ) : 'css';
        $options = isset( $_POST['options'] ) ? $_POST['options'] : array();

        $result = $this->optimize( $code, $code_type, $options );

        wp_send_json_success( $result );
    }

    /**
     * AJAX: Critical CSS 추출
     */
    public function ajax_extract_critical_css() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $css = isset( $_POST['css'] ) ? wp_unslash( $_POST['css'] ) : '';
        $selectors = isset( $_POST['selectors'] ) ? $_POST['selectors'] : array();

        $critical_css = $this->extract_critical_css( $css, $selectors );

        wp_send_json_success( array(
            'critical_css' => $critical_css,
            'original_size' => strlen( $css ),
            'critical_size' => strlen( $critical_css ),
        ) );
    }

    /**
     * AJAX: 코드 압축
     */
    public function ajax_minify_code() {
        check_ajax_referer( 'acf_csb_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( '권한이 없습니다.' );
        }

        $code = isset( $_POST['code'] ) ? wp_unslash( $_POST['code'] ) : '';
        $code_type = isset( $_POST['code_type'] ) ? sanitize_text_field( $_POST['code_type'] ) : 'css';

        $result = $this->optimize( $code, $code_type, array(
            'minify'           => true,
            'remove_comments'  => true,
            'remove_whitespace' => true,
        ) );

        wp_send_json_success( $result );
    }
}

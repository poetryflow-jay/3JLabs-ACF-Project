<?php
/**
 * JJ Common Utils - 공통 유틸리티 클래스
 * 
 * 플러그인 전체에서 사용하는 헬퍼 함수와 유틸리티를 제공합니다.
 * 
 * @package ACF_CSS_Style_Guide
 * @version 22.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class JJ_Common_Utils
 * 
 * 정적 유틸리티 메서드 모음
 */
class JJ_Common_Utils {

    /**
     * 색상을 HEX에서 RGB로 변환
     * 
     * @param string $hex HEX 색상 코드 (예: #ff5733 또는 ff5733)
     * @return array|false RGB 배열 [r, g, b] 또는 실패 시 false
     */
    public static function hex_to_rgb( $hex ) {
        $hex = ltrim( $hex, '#' );
        
        if ( strlen( $hex ) === 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        if ( strlen( $hex ) !== 6 ) {
            return false;
        }
        
        return array(
            'r' => hexdec( substr( $hex, 0, 2 ) ),
            'g' => hexdec( substr( $hex, 2, 2 ) ),
            'b' => hexdec( substr( $hex, 4, 2 ) ),
        );
    }

    /**
     * RGB를 HEX로 변환
     * 
     * @param int $r Red (0-255)
     * @param int $g Green (0-255)
     * @param int $b Blue (0-255)
     * @return string HEX 색상 코드
     */
    public static function rgb_to_hex( $r, $g, $b ) {
        return sprintf( '#%02x%02x%02x', $r, $g, $b );
    }

    /**
     * 색상 명도 조절
     * 
     * @param string $hex HEX 색상 코드
     * @param int $percent 밝기 조절 (-100 ~ 100)
     * @return string 조절된 HEX 색상 코드
     */
    public static function adjust_brightness( $hex, $percent ) {
        $rgb = self::hex_to_rgb( $hex );
        if ( ! $rgb ) {
            return $hex;
        }
        
        $adjust = function( $color ) use ( $percent ) {
            $color = max( 0, min( 255, $color + ( $color * $percent / 100 ) ) );
            return round( $color );
        };
        
        return self::rgb_to_hex(
            $adjust( $rgb['r'] ),
            $adjust( $rgb['g'] ),
            $adjust( $rgb['b'] )
        );
    }

    /**
     * 색상의 상대적 밝기 계산 (WCAG)
     * 
     * @param string $hex HEX 색상 코드
     * @return float 상대적 밝기 (0-1)
     */
    public static function get_luminance( $hex ) {
        $rgb = self::hex_to_rgb( $hex );
        if ( ! $rgb ) {
            return 0;
        }
        
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;
        
        $r = $r <= 0.03928 ? $r / 12.92 : pow( ( $r + 0.055 ) / 1.055, 2.4 );
        $g = $g <= 0.03928 ? $g / 12.92 : pow( ( $g + 0.055 ) / 1.055, 2.4 );
        $b = $b <= 0.03928 ? $b / 12.92 : pow( ( $b + 0.055 ) / 1.055, 2.4 );
        
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /**
     * 두 색상의 대비 비율 계산 (WCAG)
     * 
     * @param string $hex1 첫 번째 HEX 색상
     * @param string $hex2 두 번째 HEX 색상
     * @return float 대비 비율 (1-21)
     */
    public static function get_contrast_ratio( $hex1, $hex2 ) {
        $l1 = self::get_luminance( $hex1 );
        $l2 = self::get_luminance( $hex2 );
        
        $lighter = max( $l1, $l2 );
        $darker = min( $l1, $l2 );
        
        return ( $lighter + 0.05 ) / ( $darker + 0.05 );
    }

    /**
     * 배경색에 따른 최적의 텍스트 색상 반환
     * 
     * @param string $background HEX 배경색
     * @return string '#000000' 또는 '#ffffff'
     */
    public static function get_contrast_color( $background ) {
        $luminance = self::get_luminance( $background );
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }

    /**
     * CSS 값 sanitize
     * 
     * @param string $value CSS 값
     * @return string Sanitized CSS 값
     */
    public static function sanitize_css_value( $value ) {
        // 위험한 패턴 제거
        $value = preg_replace( '/expression\s*\(/i', '', $value );
        $value = preg_replace( '/javascript\s*:/i', '', $value );
        $value = preg_replace( '/url\s*\(\s*[\'"]?\s*data:/i', '', $value );
        $value = preg_replace( '/@import/i', '', $value );
        
        return trim( $value );
    }

    /**
     * CSS 단위 파싱
     * 
     * @param string $value CSS 값 (예: '16px', '1.5rem')
     * @return array ['value' => float, 'unit' => string]
     */
    public static function parse_css_unit( $value ) {
        preg_match( '/^([\d.]+)\s*([a-z%]*)$/i', trim( $value ), $matches );
        
        return array(
            'value' => isset( $matches[1] ) ? floatval( $matches[1] ) : 0,
            'unit'  => isset( $matches[2] ) ? strtolower( $matches[2] ) : 'px',
        );
    }

    /**
     * 배열을 CSS 변수 선언문으로 변환
     * 
     * @param array $vars ['name' => 'value', ...]
     * @param string $prefix 변수 접두사 (기본: 'jj')
     * @return string CSS 변수 선언문
     */
    public static function array_to_css_vars( $vars, $prefix = 'jj' ) {
        $css = '';
        
        foreach ( $vars as $name => $value ) {
            $var_name = str_replace( '_', '-', $name );
            $css .= "  --{$prefix}-{$var_name}: {$value};\n";
        }
        
        return $css;
    }

    /**
     * 폰트 스택 생성
     * 
     * @param array $fonts 폰트 이름 배열
     * @return string CSS font-family 값
     */
    public static function build_font_stack( $fonts ) {
        $processed = array_map( function( $font ) {
            $font = trim( $font );
            // 공백이 포함된 폰트 이름은 따옴표로 감싸기
            if ( strpos( $font, ' ' ) !== false && strpos( $font, '"' ) === false && strpos( $font, "'" ) === false ) {
                return '"' . $font . '"';
            }
            return $font;
        }, $fonts );
        
        return implode( ', ', $processed );
    }

    /**
     * 깊은 배열 병합 (재귀적)
     * 
     * @param array $array1 기본 배열
     * @param array $array2 병합할 배열
     * @return array 병합된 배열
     */
    public static function deep_merge( $array1, $array2 ) {
        $merged = $array1;
        
        foreach ( $array2 as $key => $value ) {
            if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
                $merged[ $key ] = self::deep_merge( $merged[ $key ], $value );
            } else {
                $merged[ $key ] = $value;
            }
        }
        
        return $merged;
    }

    /**
     * 점 표기법으로 배열 값 가져오기
     * 
     * @param array $array 대상 배열
     * @param string $key 점 표기법 키 (예: 'colors.primary')
     * @param mixed $default 기본값
     * @return mixed
     */
    public static function array_get( $array, $key, $default = null ) {
        if ( ! is_array( $array ) ) {
            return $default;
        }
        
        if ( isset( $array[ $key ] ) ) {
            return $array[ $key ];
        }
        
        $keys = explode( '.', $key );
        
        foreach ( $keys as $k ) {
            if ( ! is_array( $array ) || ! array_key_exists( $k, $array ) ) {
                return $default;
            }
            $array = $array[ $k ];
        }
        
        return $array;
    }

    /**
     * 점 표기법으로 배열 값 설정
     * 
     * @param array &$array 대상 배열 (참조)
     * @param string $key 점 표기법 키
     * @param mixed $value 설정할 값
     * @return void
     */
    public static function array_set( &$array, $key, $value ) {
        $keys = explode( '.', $key );
        $current = &$array;
        
        foreach ( $keys as $i => $k ) {
            if ( $i === count( $keys ) - 1 ) {
                $current[ $k ] = $value;
            } else {
                if ( ! isset( $current[ $k ] ) || ! is_array( $current[ $k ] ) ) {
                    $current[ $k ] = array();
                }
                $current = &$current[ $k ];
            }
        }
    }

    /**
     * 캐시된 트랜지언트 가져오기
     * 
     * @param string $key 트랜지언트 키
     * @param callable $callback 데이터 생성 콜백
     * @param int $expiration 만료 시간 (초)
     * @return mixed
     */
    public static function get_cached( $key, $callback, $expiration = HOUR_IN_SECONDS ) {
        $cached = get_transient( $key );
        
        if ( false !== $cached ) {
            return $cached;
        }
        
        $data = call_user_func( $callback );
        set_transient( $key, $data, $expiration );
        
        return $data;
    }

    /**
     * 에러 로깅 (디버그 모드에서만)
     * 
     * @param string $message 로그 메시지
     * @param mixed $data 추가 데이터
     * @return void
     */
    public static function log( $message, $data = null ) {
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }
        
        $log = '[JJ Style Guide] ' . $message;
        
        if ( $data !== null ) {
            $log .= ' | Data: ' . ( is_array( $data ) || is_object( $data ) ? wp_json_encode( $data ) : $data );
        }
        
        error_log( $log );
    }

    /**
     * 플러그인 버전 비교
     * 
     * @param string $version1
     * @param string $version2
     * @param string $operator
     * @return bool
     */
    public static function version_compare( $version1, $version2, $operator = '>' ) {
        return version_compare( $version1, $version2, $operator );
    }

    /**
     * 현재 관리자 색상 스킴 가져오기
     * 
     * @return array 색상 배열
     */
    public static function get_admin_colors() {
        global $_wp_admin_css_colors;
        
        $user_admin_color = get_user_option( 'admin_color' );
        
        if ( empty( $user_admin_color ) || ! isset( $_wp_admin_css_colors[ $user_admin_color ] ) ) {
            $user_admin_color = 'fresh';
        }
        
        if ( isset( $_wp_admin_css_colors[ $user_admin_color ] ) ) {
            return $_wp_admin_css_colors[ $user_admin_color ]->colors;
        }
        
        return array( '#222', '#333', '#0073aa', '#00a0d2' );
    }

    /**
     * 문자열을 슬러그로 변환
     * 
     * @param string $text
     * @return string
     */
    public static function slugify( $text ) {
        $text = preg_replace( '~[^\pL\d]+~u', '-', $text );
        $text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );
        $text = preg_replace( '~[^-\w]+~', '', $text );
        $text = trim( $text, '-' );
        $text = preg_replace( '~-+~', '-', $text );
        $text = strtolower( $text );
        
        return $text;
    }

    /**
     * 파일 확장자 검증
     * 
     * @param string $filename
     * @param array $allowed_extensions
     * @return bool
     */
    public static function validate_file_extension( $filename, $allowed_extensions = array( 'json' ) ) {
        $ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
        return in_array( $ext, $allowed_extensions, true );
    }

    /**
     * 안전한 리다이렉트
     * 
     * @param string $url
     * @param int $status
     * @return void
     */
    public static function safe_redirect( $url, $status = 302 ) {
        if ( wp_safe_redirect( $url, $status ) ) {
            exit;
        }
    }

    /**
     * AJAX 응답 전송 (success)
     * 
     * @param mixed $data
     * @param string $message
     * @return void
     */
    public static function ajax_success( $data = null, $message = '' ) {
        wp_send_json_success( array(
            'data'    => $data,
            'message' => $message,
        ) );
    }

    /**
     * AJAX 응답 전송 (error)
     * 
     * @param string $message
     * @param mixed $data
     * @return void
     */
    public static function ajax_error( $message, $data = null ) {
        wp_send_json_error( array(
            'message' => $message,
            'data'    => $data,
        ) );
    }
}

// 글로벌 헬퍼 함수들

if ( ! function_exists( 'jj_get_admin_colors' ) ) {
    /**
     * 현재 관리자 색상 스킴 가져오기 (폴백)
     * 
     * @return array
     */
    function jj_get_admin_colors() {
        if ( class_exists( 'JJ_Common_Utils' ) ) {
            return JJ_Common_Utils::get_admin_colors();
        }
        return array( '#222', '#333', '#0073aa', '#00a0d2' );
    }
}

if ( ! function_exists( 'get_admin_colors' ) ) {
    /**
     * get_admin_colors 폴백 함수
     * 
     * @return array
     */
    function get_admin_colors() {
        return jj_get_admin_colors();
    }
}

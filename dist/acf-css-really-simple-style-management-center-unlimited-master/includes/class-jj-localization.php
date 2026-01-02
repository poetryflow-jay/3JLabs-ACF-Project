<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.1] Localization Helper
 * 
 * 날짜/시간, 숫자, 통화 형식 현지화
 * 
 * @since 9.1.0
 */
class JJ_Localization {

    /**
     * 날짜 형식화 (현지화)
     * 
     * @param string|int $date 날짜 (문자열 또는 타임스탬프)
     * @param string $format 날짜 형식 (선택적)
     * @return string 형식화된 날짜
     */
    public static function format_date( $date, $format = null ) {
        if ( ! $date ) {
            return '';
        }

        // WordPress 날짜 형식 사용
        if ( ! $format ) {
            $format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
        }

        // 타임스탬프 변환
        if ( is_numeric( $date ) ) {
            $timestamp = $date;
        } else {
            $timestamp = strtotime( $date );
        }

        if ( ! $timestamp ) {
            return '';
        }

        return date_i18n( $format, $timestamp );
    }

    /**
     * 숫자 형식화 (현지화)
     * 
     * @param float|int $number 숫자
     * @param int $decimals 소수점 자릿수
     * @return string 형식화된 숫자
     */
    public static function format_number( $number, $decimals = 2 ) {
        if ( ! is_numeric( $number ) ) {
            return '';
        }

        return number_format_i18n( $number, $decimals );
    }

    /**
     * 통화 형식화 (현지화)
     * 
     * @param float|int $amount 금액
     * @param string $currency 통화 코드
     * @return string 형식화된 통화
     */
    public static function format_currency( $amount, $currency = 'USD' ) {
        if ( ! is_numeric( $amount ) ) {
            return '';
        }

        // WooCommerce 사용 가능한 경우
        if ( function_exists( 'wc_price' ) ) {
            return wc_price( $amount, array( 'currency' => $currency ) );
        }

        // 기본 형식화
        $locale = get_locale();
        $symbol = self::get_currency_symbol( $currency );
        $formatted = self::format_number( $amount, 2 );
        
        // 통화 심볼 위치 결정 (로케일별)
        if ( in_array( $locale, array( 'en_US', 'en_GB', 'en_CA', 'en_AU' ) ) ) {
            return $symbol . $formatted;
        } else {
            return $formatted . ' ' . $symbol;
        }
    }

    /**
     * 통화 심볼 가져오기
     * 
     * @param string $currency 통화 코드
     * @return string 통화 심볼
     */
    private static function get_currency_symbol( $currency ) {
        $symbols = array(
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CNY' => '¥',
            'KRW' => '₩',
            'AUD' => 'A$',
            'CAD' => 'C$',
            'CHF' => 'CHF',
            'INR' => '₹',
        );

        return isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : $currency;
    }

    /**
     * 파일 크기 형식화 (현지화)
     * 
     * @param int $bytes 바이트
     * @param int $precision 소수점 자릿수
     * @return string 형식화된 파일 크기
     */
    public static function format_file_size( $bytes, $precision = 2 ) {
        if ( ! is_numeric( $bytes ) || $bytes < 0 ) {
            return '';
        }

        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
        $base = log( $bytes, 1024 );
        $unit = floor( $base );
        
        if ( $unit >= count( $units ) ) {
            $unit = count( $units ) - 1;
        }

        $size = pow( 1024, $base - $unit );
        $formatted = self::format_number( $size, $precision );

        return $formatted . ' ' . $units[ $unit ];
    }

    /**
     * 상대 시간 형식화 (예: "2시간 전", "3일 전")
     * 
     * @param string|int $date 날짜
     * @return string 상대 시간 문자열
     */
    public static function format_relative_time( $date ) {
        if ( ! $date ) {
            return '';
        }

        // 타임스탬프 변환
        if ( is_numeric( $date ) ) {
            $timestamp = $date;
        } else {
            $timestamp = strtotime( $date );
        }

        if ( ! $timestamp ) {
            return '';
        }

        $current_time = current_time( 'timestamp' );
        $diff = $current_time - $timestamp;

        if ( $diff < 60 ) {
            return sprintf( __( '%d초 전', 'acf-css-really-simple-style-management-center' ), $diff );
        } elseif ( $diff < 3600 ) {
            $minutes = floor( $diff / 60 );
            return sprintf( __( '%d분 전', 'acf-css-really-simple-style-management-center' ), $minutes );
        } elseif ( $diff < 86400 ) {
            $hours = floor( $diff / 3600 );
            return sprintf( __( '%d시간 전', 'acf-css-really-simple-style-management-center' ), $hours );
        } elseif ( $diff < 604800 ) {
            $days = floor( $diff / 86400 );
            return sprintf( __( '%d일 전', 'acf-css-really-simple-style-management-center' ), $days );
        } elseif ( $diff < 2592000 ) {
            $weeks = floor( $diff / 604800 );
            return sprintf( __( '%d주 전', 'acf-css-really-simple-style-management-center' ), $weeks );
        } elseif ( $diff < 31536000 ) {
            $months = floor( $diff / 2592000 );
            return sprintf( __( '%d개월 전', 'acf-css-really-simple-style-management-center' ), $months );
        } else {
            $years = floor( $diff / 31536000 );
            return sprintf( __( '%d년 전', 'acf-css-really-simple-style-management-center' ), $years );
        }
    }

    /**
     * 시간대 정보 가져오기
     * 
     * @return array 시간대 정보
     */
    public static function get_timezone_info() {
        $timezone_string = get_option( 'timezone_string' );
        $gmt_offset = get_option( 'gmt_offset' );

        if ( $timezone_string ) {
            $timezone = new DateTimeZone( $timezone_string );
        } else {
            $timezone = timezone_open( sprintf( 'UTC%+d', $gmt_offset ) );
        }

        return array(
            'timezone'     => $timezone_string ? $timezone_string : 'UTC' . ( $gmt_offset >= 0 ? '+' : '' ) . $gmt_offset,
            'gmt_offset'   => $gmt_offset,
            'abbreviation' => $timezone_string ? date( 'T', time() ) : 'UTC',
        );
    }

    /**
     * 로케일 정보 가져오기
     * 
     * @return array 로케일 정보
     */
    public static function get_locale_info() {
        $locale = get_locale();
        
        // 로케일에서 언어 코드 추출
        $lang_code = substr( $locale, 0, 2 );
        
        // 언어 이름 매핑
        $language_names = array(
            'en' => __( 'English', 'acf-css-really-simple-style-management-center' ),
            'ko' => __( '한국어', 'acf-css-really-simple-style-management-center' ),
            'ja' => __( '日本語', 'acf-css-really-simple-style-management-center' ),
            'zh' => __( '中文', 'acf-css-really-simple-style-management-center' ),
            'es' => __( 'Español', 'acf-css-really-simple-style-management-center' ),
            'fr' => __( 'Français', 'acf-css-really-simple-style-management-center' ),
            'de' => __( 'Deutsch', 'acf-css-really-simple-style-management-center' ),
            'it' => __( 'Italiano', 'acf-css-really-simple-style-management-center' ),
            'pt' => __( 'Português', 'acf-css-really-simple-style-management-center' ),
            'ru' => __( 'Русский', 'acf-css-really-simple-style-management-center' ),
        );

        return array(
            'locale'        => $locale,
            'lang_code'    => $lang_code,
            'language'     => isset( $language_names[ $lang_code ] ) ? $language_names[ $lang_code ] : $lang_code,
            'date_format' => get_option( 'date_format' ),
            'time_format'  => get_option( 'time_format' ),
        );
    }
}

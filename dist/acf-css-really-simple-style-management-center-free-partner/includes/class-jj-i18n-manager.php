<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 9.1] i18n (Internationalization) Manager
 * 
 * JavaScript 번역 지원 및 동적 번역 로딩
 * 
 * @since 9.1.0
 */
class JJ_I18n_Manager {

    private static $instance = null;
    private $text_domain = 'acf-css-really-simple-style-management-center';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_i18n_script' ) );
        add_action( 'wp_ajax_jj_load_translations', array( $this, 'ajax_load_translations' ) );
        add_action( 'wp_ajax_jj_get_translation_strings', array( $this, 'ajax_get_translation_strings' ) );
    }

    /**
     * i18n 스크립트 enqueue
     */
    public function enqueue_i18n_script( $hook ) {
        // Admin Center 및 관련 페이지에서만 로드
        if ( strpos( $hook, 'jj-admin-center' ) === false && 
             strpos( $hook, 'jj-labs-center' ) === false &&
             strpos( $hook, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        // WordPress i18n 스크립트 로드 (wp.i18n 사용)
        wp_enqueue_script( 'wp-i18n' );
        wp_add_inline_script( 'wp-i18n', 'wp.i18n.setLocaleData( ' . wp_json_encode( $this->get_translation_data() ) . ', "' . $this->text_domain . '" );' );

        // JJ i18n 유틸리티 스크립트
        wp_enqueue_script(
            'jj-i18n',
            JJ_STYLE_GUIDE_URL . 'assets/js/jj-i18n.js',
            array( 'jquery', 'wp-i18n' ),
            defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '9.1.0',
            true
        );

        // 번역 데이터 로컬라이즈
        wp_localize_script(
            'jj-i18n',
            'jjI18nData',
            array(
                'locale'     => get_locale(),
                'textDomain' => $this->text_domain,
                'ajax_url'   => admin_url( 'admin-ajax.php' ),
                'nonce'      => wp_create_nonce( 'jj_i18n_action' ),
                'translations' => $this->get_common_translations(),
            )
        );
    }

    /**
     * WordPress i18n 형식의 번역 데이터 가져오기
     */
    private function get_translation_data() {
        $locale = get_locale();
        $translations = array();

        // PO 파일에서 번역 로드
        $po_file = JJ_STYLE_GUIDE_PATH . 'languages/' . $this->text_domain . '-' . $locale . '.po';
        
        if ( file_exists( $po_file ) ) {
            $translations = $this->parse_po_file( $po_file );
        }

        return $translations;
    }

    /**
     * PO 파일 파싱
     */
    private function parse_po_file( $file_path ) {
        $translations = array();
        
        if ( ! file_exists( $file_path ) ) {
            return $translations;
        }

        $content = file_get_contents( $file_path );
        $lines = explode( "\n", $content );
        
        $current_msgid = null;
        $current_msgstr = null;
        $in_msgid = false;
        $in_msgstr = false;

        foreach ( $lines as $line ) {
            $line = trim( $line );

            if ( strpos( $line, 'msgid ' ) === 0 ) {
                $in_msgid = true;
                $in_msgstr = false;
                $current_msgid = $this->extract_string( $line );
            } elseif ( strpos( $line, 'msgstr ' ) === 0 ) {
                $in_msgid = false;
                $in_msgstr = true;
                $current_msgstr = $this->extract_string( $line );
                
                if ( $current_msgid && $current_msgstr ) {
                    $translations[ $current_msgid ] = array( $current_msgstr );
                }
            } elseif ( ( $in_msgid || $in_msgstr ) && strpos( $line, '"' ) === 0 ) {
                $str = $this->extract_string( $line );
                if ( $in_msgid ) {
                    $current_msgid .= $str;
                } elseif ( $in_msgstr ) {
                    $current_msgstr .= $str;
                }
            } elseif ( empty( $line ) ) {
                if ( $current_msgid && $current_msgstr ) {
                    $translations[ $current_msgid ] = array( $current_msgstr );
                }
                $current_msgid = null;
                $current_msgstr = null;
                $in_msgid = false;
                $in_msgstr = false;
            }
        }

        return $translations;
    }

    /**
     * PO 파일에서 문자열 추출
     */
    private function extract_string( $line ) {
        // "로 시작하고 끝나는 문자열 추출
        if ( preg_match( '/^"(.+)"$/', $line, $matches ) ) {
            return stripcslashes( $matches[1] );
        }
        return '';
    }

    /**
     * 공통 번역 문자열 가져오기 (즉시 사용 가능)
     */
    private function get_common_translations() {
        return array(
            '소중한 의견 감사합니다!' => __( '소중한 의견 감사합니다!', $this->text_domain ),
            '피드백 전송 중 오류가 발생했습니다.' => __( '피드백 전송 중 오류가 발생했습니다.', $this->text_domain ),
            '브라우저에서 스포이드 기능을 지원하지 않습니다.' => __( '브라우저에서 스포이드 기능을 지원하지 않습니다.', $this->text_domain ),
            '팔레트를 불러오는 중 오류가 발생했습니다.' => __( '팔레트를 불러오는 중 오류가 발생했습니다.', $this->text_domain ),
            '선택한 팔레트에 사용 가능한 색상이 없습니다.' => __( '선택한 팔레트에 사용 가능한 색상이 없습니다.', $this->text_domain ),
            '라이센스 키가 저장되었습니다.' => __( '라이센스 키가 저장되었습니다.', $this->text_domain ),
            '라이센스 키 저장에 실패했습니다.' => __( '라이센스 키 저장에 실패했습니다.', $this->text_domain ),
            '라이센스 검증이 완료되었습니다.' => __( '라이센스 검증이 완료되었습니다.', $this->text_domain ),
            '라이센스 검증에 실패했습니다.' => __( '라이센스 검증에 실패했습니다.', $this->text_domain ),
        );
    }

    /**
     * AJAX: 번역 데이터 로드
     */
    public function ajax_load_translations() {
        // 보안 검증
        if ( ! check_ajax_referer( 'jj_i18n_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', $this->text_domain ) ) );
            return;
        }

        $domain = isset( $_POST['domain'] ) ? sanitize_text_field( $_POST['domain'] ) : $this->text_domain;
        $locale = isset( $_POST['locale'] ) ? sanitize_text_field( $_POST['locale'] ) : get_locale();

        $translations = $this->get_translation_data();

        wp_send_json_success( array(
            'translations' => $translations,
            'locale'      => $locale,
            'domain'      => $domain,
        ) );
    }

    /**
     * AJAX: 번역 문자열 목록 가져오기 (번역 관리용)
     */
    public function ajax_get_translation_strings() {
        // 관리자 권한 확인
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', $this->text_domain ) ) );
            return;
        }

        // 보안 검증
        if ( ! check_ajax_referer( 'jj_i18n_action', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', $this->text_domain ) ) );
            return;
        }

        $locale = isset( $_POST['locale'] ) ? sanitize_text_field( $_POST['locale'] ) : get_locale();
        $translations = $this->get_translation_data();

        wp_send_json_success( array(
            'strings' => array_keys( $translations ),
            'count'   => count( $translations ),
            'locale'  => $locale,
        ) );
    }

    /**
     * 날짜 형식화 (현지화)
     */
    public static function format_date( $date, $format = null ) {
        if ( ! $date ) {
            return '';
        }

        $format = $format ? $format : get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
        
        if ( is_numeric( $date ) ) {
            $timestamp = $date;
        } else {
            $timestamp = strtotime( $date );
        }

        return date_i18n( $format, $timestamp );
    }

    /**
     * 숫자 형식화 (현지화)
     */
    public static function format_number( $number, $decimals = 2 ) {
        if ( ! is_numeric( $number ) ) {
            return '';
        }

        $locale = get_locale();
        
        // WordPress number_format_i18n 사용
        return number_format_i18n( $number, $decimals );
    }

    /**
     * 통화 형식화 (현지화)
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
        $symbol = '$'; // 기본 USD 심볼
        $formatted = self::format_number( $amount, 2 );
        
        return $symbol . $formatted;
    }
}

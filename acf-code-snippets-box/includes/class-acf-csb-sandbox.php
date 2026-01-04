<?php
/**
 * ACF Code Snippets Box - Sandbox
 *
 * PHP 코드 실행 샌드박스: 리소스 제한, 파일 시스템 접근 제한, 네트워크 요청 제한
 *
 * @package ACF_Code_Snippets_Box
 * @since 4.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Sandbox 클래스
 */
class ACF_CSB_Sandbox {

    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;

    /**
     * 허용된 함수 목록
     */
    private $allowed_functions = array();

    /**
     * 금지된 함수 목록
     */
    private $forbidden_functions = array(
        'eval',
        'exec',
        'shell_exec',
        'system',
        'passthru',
        'popen',
        'proc_open',
        'file_get_contents',
        'file_put_contents',
        'fopen',
        'fwrite',
        'unlink',
        'rmdir',
        'mkdir',
        'chmod',
        'chown',
        'curl_exec',
        'fsockopen',
        'pfsockopen',
    );

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
        $this->init_allowed_functions();
    }

    /**
     * 허용된 함수 목록 초기화
     */
    private function init_allowed_functions() {
        // WordPress 코어 함수
        $this->allowed_functions = array(
            // WordPress 함수
            'get_option',
            'update_option',
            'get_post',
            'get_posts',
            'get_user',
            'get_user_meta',
            'wp_get_current_user',
            'is_user_logged_in',
            'current_user_can',
            'wp_enqueue_script',
            'wp_enqueue_style',
            'wp_localize_script',
            'add_action',
            'add_filter',
            'apply_filters',
            'do_action',
            'esc_html',
            'esc_attr',
            'esc_url',
            'sanitize_text_field',
            'wp_kses',
            'wp_kses_post',
            'wp_insert_post',
            'wp_update_post',
            'wp_delete_post',
            'get_the_ID',
            'get_the_title',
            'get_the_content',
            'get_permalink',
            'home_url',
            'admin_url',
            'site_url',
            'bloginfo',
            'get_bloginfo',
            'wp_nonce_field',
            'wp_create_nonce',
            'wp_verify_nonce',
            'wp_mail',
            'wp_redirect',
            'wp_safe_redirect',
            'wp_die',
            'wp_json_encode',
            'wp_json_decode',
            'wp_parse_args',
            'wp_parse_url',
            'wp_remote_get',
            'wp_remote_post',
            'wp_remote_request',
            'wp_remote_retrieve_body',
            'wp_remote_retrieve_response_code',
            'wp_remote_retrieve_headers',
            'wp_cache_get',
            'wp_cache_set',
            'wp_cache_delete',
            'wp_cache_flush',
            'get_transient',
            'set_transient',
            'delete_transient',
            'get_site_transient',
            'set_site_transient',
            'delete_site_transient',
            'wp_schedule_event',
            'wp_schedule_single_event',
            'wp_unschedule_event',
            'wp_clear_scheduled_hook',
            'wp_next_scheduled',
            'wp_cron',
            'wp_send_json',
            'wp_send_json_success',
            'wp_send_json_error',
            'wp_die',
            'wp_die_handler',
            'wp_die_ajax_handler',
            'wp_die_json_handler',
            'wp_die_jsonp_handler',
            'wp_die_xmlrpc_handler',
            'wp_die_xml_handler',
            'wp_die_handler_ajax',
            'wp_die_handler_json',
            'wp_die_handler_jsonp',
            'wp_die_handler_xmlrpc',
            'wp_die_handler_xml',
            'wp_die_handler_ajax',
            'wp_die_handler_json',
            'wp_die_handler_jsonp',
            'wp_die_handler_xmlrpc',
            'wp_die_handler_xml',
            // PHP 기본 함수 (안전한 것만)
            'strlen',
            'strpos',
            'str_replace',
            'preg_match',
            'preg_replace',
            'explode',
            'implode',
            'array_merge',
            'array_filter',
            'array_map',
            'array_reduce',
            'in_array',
            'array_key_exists',
            'count',
            'empty',
            'isset',
            'is_array',
            'is_string',
            'is_numeric',
            'is_int',
            'is_float',
            'is_bool',
            'is_object',
            'is_null',
            'trim',
            'ltrim',
            'rtrim',
            'strtolower',
            'strtoupper',
            'ucfirst',
            'ucwords',
            'substr',
            'substr_replace',
            'str_split',
            'md5',
            'sha1',
            'hash',
            'base64_encode',
            'base64_decode',
            'json_encode',
            'json_decode',
            'serialize',
            'unserialize',
            'date',
            'time',
            'strtotime',
            'mktime',
            'date_i18n',
            'current_time',
            'mysql2date',
            'get_date_from_gmt',
            'get_gmt_from_date',
            'absint',
            'intval',
            'floatval',
            'strval',
            'boolval',
            'round',
            'floor',
            'ceil',
            'min',
            'max',
            'rand',
            'mt_rand',
            'uniqid',
            'microtime',
        );
    }

    /**
     * 코드 실행 (샌드박스 환경)
     * 
     * @param string $code 실행할 코드
     * @param array $options 실행 옵션
     * @return array 실행 결과
     */
    public function execute( $code, $options = array() ) {
        $defaults = array(
            'max_execution_time' => 5, // 초
            'max_memory'        => '32M',
            'max_input_vars'    => 1000,
            'disable_functions' => true,
            'disable_classes'   => true,
        );

        $options = wp_parse_args( $options, $defaults );

        // 코드 검증
        $validation = $this->validate_code( $code );
        if ( ! $validation['valid'] ) {
            return array(
                'success' => false,
                'error'   => $validation['error'],
                'output'  => '',
            );
        }

        // 리소스 제한 설정
        $old_time_limit = ini_get( 'max_execution_time' );
        $old_memory_limit = ini_get( 'memory_limit' );

        @ini_set( 'max_execution_time', $options['max_execution_time'] );
        @ini_set( 'memory_limit', $options['max_memory'] );

        // 실행 시간 측정
        $start_time = microtime( true );
        $start_memory = memory_get_usage();

        // 출력 버퍼링 시작
        ob_start();

        // 에러 핸들러 설정
        $error_handler = set_error_handler( array( $this, 'error_handler' ) );

        try {
            // 코드 실행
            $result = eval( $code );

            $output = ob_get_clean();
            $execution_time = microtime( true ) - $start_time;
            $memory_used = memory_get_usage() - $start_memory;

            // 에러 핸들러 복원
            if ( $error_handler ) {
                restore_error_handler();
            }

            // 리소스 제한 복원
            @ini_set( 'max_execution_time', $old_time_limit );
            @ini_set( 'memory_limit', $old_memory_limit );

            return array(
                'success'        => true,
                'output'         => $output,
                'result'         => $result,
                'execution_time' => round( $execution_time * 1000, 2 ), // ms
                'memory_used'   => $this->format_bytes( $memory_used ),
            );

        } catch ( Exception $e ) {
            ob_end_clean();
            restore_error_handler();
            @ini_set( 'max_execution_time', $old_time_limit );
            @ini_set( 'memory_limit', $old_memory_limit );

            return array(
                'success' => false,
                'error'   => 'Exception: ' . $e->getMessage(),
                'output'  => '',
            );
        } catch ( Error $e ) {
            ob_end_clean();
            restore_error_handler();
            @ini_set( 'max_execution_time', $old_time_limit );
            @ini_set( 'memory_limit', $old_memory_limit );

            return array(
                'success' => false,
                'error'   => 'Error: ' . $e->getMessage(),
                'output'  => '',
            );
        }
    }

    /**
     * 코드 검증
     */
    private function validate_code( $code ) {
        // PHP 태그 체크
        if ( strpos( $code, '<?php' ) !== false || strpos( $code, '?>' ) !== false ) {
            return array(
                'valid' => false,
                'error' => __( 'PHP 태그(<?php, ?>)를 제거해주세요. 코드만 입력합니다.', 'acf-code-snippets-box' ),
            );
        }

        // 금지된 함수 체크
        foreach ( $this->forbidden_functions as $func ) {
            if ( preg_match( '/\b' . preg_quote( $func, '/' ) . '\s*\(/', $code ) ) {
                return array(
                    'valid' => false,
                    'error' => sprintf( __( '금지된 함수 %s()가 사용되었습니다.', 'acf-code-snippets-box' ), $func ),
                );
            }
        }

        // 문법 체크
        $syntax_check = $this->check_syntax( $code );
        if ( ! $syntax_check['valid'] ) {
            return $syntax_check;
        }

        return array( 'valid' => true );
    }

    /**
     * 문법 체크
     */
    private function check_syntax( $code ) {
        if ( ! function_exists( 'proc_open' ) ) {
            return array( 'valid' => true ); // 체크 불가
        }

        $temp_file = wp_tempnam( 'acf_csb_syntax_check_' );
        file_put_contents( $temp_file, '<?php ' . $code );

        $output = array();
        exec( 'php -l ' . escapeshellarg( $temp_file ) . ' 2>&1', $output, $return_code );

        unlink( $temp_file );

        if ( $return_code !== 0 ) {
            $error = implode( "\n", $output );
            return array(
                'valid' => false,
                'error' => $error,
            );
        }

        return array( 'valid' => true );
    }

    /**
     * 에러 핸들러
     */
    public function error_handler( $errno, $errstr, $errfile, $errline ) {
        // 치명적 오류만 처리
        if ( $errno === E_ERROR || $errno === E_PARSE || $errno === E_CORE_ERROR || $errno === E_COMPILE_ERROR ) {
            throw new Error( $errstr, $errno );
        }
        return false; // 다른 에러는 기본 핸들러로
    }

    /**
     * 바이트를 읽기 쉬운 형식으로 변환
     */
    private function format_bytes( $bytes, $precision = 2 ) {
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB' );

        $bytes = max( $bytes, 0 );
        $pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
        $pow = min( $pow, count( $units ) - 1 );

        $bytes /= pow( 1024, $pow );

        return round( $bytes, $precision ) . ' ' . $units[ $pow ];
    }

    /**
     * 허용된 함수인지 확인
     */
    public function is_allowed_function( $function_name ) {
        return in_array( $function_name, $this->allowed_functions, true );
    }

    /**
     * 금지된 함수인지 확인
     */
    public function is_forbidden_function( $function_name ) {
        return in_array( $function_name, $this->forbidden_functions, true );
    }
}

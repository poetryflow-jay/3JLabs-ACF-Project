<?php
/**
 * JJ AJAX Helper - 공통 AJAX 보안 검증 유틸리티
 *
 * 모든 3J Labs 플러그인에서 사용하는 AJAX 요청 보안 검증 헬퍼 클래스입니다.
 * 중복된 nonce 검증, 권한 확인 로직을 단일 메서드로 통합하여 코드 중복을 제거합니다.
 *
 * @package    3J_Labs_Shared
 * @subpackage Utilities
 * @since      1.0.0
 * @author     3J Labs (제이x제니x제이슨 연구소)
 * @link       https://3j-labs.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_Ajax_Helper' ) ) {

    /**
     * AJAX 보안 검증 헬퍼 클래스
     *
     * @since 1.0.0
     */
    class JJ_Ajax_Helper {

        /**
         * 싱글톤 인스턴스
         *
         * @var JJ_Ajax_Helper|null
         */
        private static $instance = null;

        /**
         * 로그 접두사 (플러그인별로 설정)
         *
         * @var string
         */
        private $log_prefix = '[JJ Ajax]';

        /**
         * 싱글톤 인스턴스 반환
         *
         * @since 1.0.0
         * @return JJ_Ajax_Helper
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * 로그 접두사 설정
         *
         * @since 1.0.0
         * @param string $prefix 로그 메시지 접두사 (예: '[WP Bulk Manager]')
         * @return $this
         */
        public function set_log_prefix( $prefix ) {
            $this->log_prefix = $prefix;
            return $this;
        }

        /**
         * AJAX 요청의 nonce 검증 (안전한 버전)
         *
         * try-catch로 감싸진 check_ajax_referer를 수행하며,
         * 실패 시 JSON 에러 응답을 반환합니다.
         *
         * @since 1.0.0
         *
         * @param string $action      Nonce 액션 이름
         * @param string $query_arg   Nonce 쿼리 인자 이름 (기본: 'nonce')
         * @param bool   $die_on_fail 실패 시 wp_send_json_error 호출 여부 (기본: true)
         * @return bool 검증 성공 여부
         */
        public function verify_nonce( $action, $query_arg = 'nonce', $die_on_fail = true ) {
            try {
                $result = check_ajax_referer( $action, $query_arg, false );

                if ( false === $result ) {
                    $this->log_error( 'Nonce verification failed for action: ' . $action );

                    if ( $die_on_fail ) {
                        wp_send_json_error( array(
                            'message' => __( '보안 검증에 실패했습니다. 페이지를 새로고침 후 다시 시도해주세요.', '3j-labs' ),
                            'code'    => 'nonce_failed',
                        ) );
                    }
                    return false;
                }

                return true;

            } catch ( Exception $e ) {
                $this->log_error( 'Nonce verification exception: ' . $e->getMessage() );

                if ( $die_on_fail ) {
                    wp_send_json_error( array(
                        'message' => __( '보안 검증 중 오류가 발생했습니다.', '3j-labs' ),
                        'code'    => 'nonce_exception',
                    ) );
                }
                return false;

            } catch ( Error $e ) {
                $this->log_error( 'Nonce verification fatal error: ' . $e->getMessage() );

                if ( $die_on_fail ) {
                    wp_send_json_error( array(
                        'message' => __( '보안 검증 중 심각한 오류가 발생했습니다.', '3j-labs' ),
                        'code'    => 'nonce_fatal',
                    ) );
                }
                return false;
            }
        }

        /**
         * 관리자 권한 확인
         *
         * @since 1.0.0
         *
         * @param string $capability  확인할 권한 (기본: 'manage_options')
         * @param bool   $die_on_fail 실패 시 wp_send_json_error 호출 여부 (기본: true)
         * @return bool 권한 있음 여부
         */
        public function verify_capability( $capability = 'manage_options', $die_on_fail = true ) {
            if ( ! current_user_can( $capability ) ) {
                $this->log_error( 'Capability check failed: ' . $capability );

                if ( $die_on_fail ) {
                    wp_send_json_error( array(
                        'message' => __( '이 작업을 수행할 권한이 없습니다.', '3j-labs' ),
                        'code'    => 'no_permission',
                    ) );
                }
                return false;
            }

            return true;
        }

        /**
         * AJAX 요청 전체 검증 (nonce + 권한)
         *
         * 일반적인 AJAX 핸들러 시작 부분에서 호출하여
         * nonce와 권한을 한 번에 검증합니다.
         *
         * @since 1.0.0
         *
         * @param string $action      Nonce 액션 이름
         * @param string $query_arg   Nonce 쿼리 인자 이름 (기본: 'nonce')
         * @param string $capability  확인할 권한 (기본: 'manage_options')
         * @return bool 모든 검증 통과 여부
         */
        public function verify_request( $action, $query_arg = 'nonce', $capability = 'manage_options' ) {
            // 1. 권한 확인
            if ( ! $this->verify_capability( $capability ) ) {
                return false;
            }

            // 2. Nonce 검증
            if ( ! $this->verify_nonce( $action, $query_arg ) ) {
                return false;
            }

            return true;
        }

        /**
         * POST 파라미터 안전하게 가져오기
         *
         * @since 1.0.0
         *
         * @param string $key     파라미터 키
         * @param mixed  $default 기본값 (기본: '')
         * @param string $filter  필터 타입 ('text', 'int', 'email', 'url', 'array')
         * @return mixed 정제된 값
         */
        public function get_post_param( $key, $default = '', $filter = 'text' ) {
            if ( ! isset( $_POST[ $key ] ) ) {
                return $default;
            }

            $value = $_POST[ $key ];

            switch ( $filter ) {
                case 'int':
                    return intval( $value );

                case 'email':
                    return sanitize_email( $value );

                case 'url':
                    return esc_url_raw( $value );

                case 'array':
                    return is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : array();

                case 'html':
                    return wp_kses_post( $value );

                case 'text':
                default:
                    return sanitize_text_field( wp_unslash( $value ) );
            }
        }

        /**
         * 성공 응답 전송
         *
         * @since 1.0.0
         *
         * @param string $message 성공 메시지
         * @param array  $data    추가 데이터 (선택)
         */
        public function send_success( $message, $data = array() ) {
            wp_send_json_success( array_merge(
                array( 'message' => $message ),
                $data
            ) );
        }

        /**
         * 에러 응답 전송
         *
         * @since 1.0.0
         *
         * @param string $message 에러 메시지
         * @param string $code    에러 코드 (선택)
         * @param array  $data    추가 데이터 (선택)
         */
        public function send_error( $message, $code = 'error', $data = array() ) {
            wp_send_json_error( array_merge(
                array(
                    'message' => $message,
                    'code'    => $code,
                ),
                $data
            ) );
        }

        /**
         * 에러 로그 기록
         *
         * @since 1.0.0
         *
         * @param string $message 로그 메시지
         */
        private function log_error( $message ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( $this->log_prefix . ' ' . $message );
            }
        }
    }
}

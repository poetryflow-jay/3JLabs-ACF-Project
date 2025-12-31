<?php
/**
 * JJ Webhook Manager
 *
 * Phase 5.2: API 및 통합 (웹훅)
 * - 설정 변경 이벤트 발생 시, 지정된 URL로 Webhook을 전송합니다.
 *
 * @since 6.0.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_Webhook_Manager' ) ) {
    final class JJ_Webhook_Manager {

        private static $instance = null;

        /** @var string */
        private $option_key = 'jj_style_guide_webhooks';

        public static function instance() {
            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
            if ( function_exists( 'add_action' ) ) {
                add_action( 'jj_style_guide_settings_updated', array( $this, 'on_style_settings_updated' ), 10, 3 );
                add_action( 'jj_style_guide_admin_center_updated', array( $this, 'on_admin_center_updated' ), 10, 3 );

                // Admin Center 테스트 버튼용
                add_action( 'wp_ajax_jj_test_webhook', array( $this, 'ajax_test_webhook' ) );
            }
        }

        /**
         * 기본 설정
         *
         * @return array
         */
        private function get_defaults() {
            return array(
                'enabled'         => false,
                'endpoints'       => array(),
                'secret'          => '',
                'events'          => array( 'style_settings_updated', 'admin_center_updated' ),
                'payload_mode'    => 'minimal', // minimal|full
                'timeout_seconds' => 5,
            );
        }

        /**
         * 현재 설정
         *
         * @return array
         */
        public function get_config() {
            $defaults = $this->get_defaults();
            $cfg = function_exists( 'get_option' ) ? get_option( $this->option_key, array() ) : array();
            if ( ! is_array( $cfg ) ) {
                $cfg = array();
            }

            $merged = array_merge( $defaults, $cfg );

            // normalize
            $merged['enabled'] = ! empty( $merged['enabled'] );
            $merged['payload_mode'] = ( isset( $merged['payload_mode'] ) && 'full' === $merged['payload_mode'] ) ? 'full' : 'minimal';
            $merged['timeout_seconds'] = isset( $merged['timeout_seconds'] ) ? max( 1, min( 30, (int) $merged['timeout_seconds'] ) ) : 5;

            if ( ! isset( $merged['endpoints'] ) || ! is_array( $merged['endpoints'] ) ) {
                $merged['endpoints'] = array();
            }
            $merged['endpoints'] = array_values( array_filter( array_map( 'trim', $merged['endpoints'] ) ) );

            if ( ! isset( $merged['events'] ) || ! is_array( $merged['events'] ) ) {
                $merged['events'] = $defaults['events'];
            }
            $merged['events'] = array_values( array_unique( array_map( 'strval', $merged['events'] ) ) );

            $merged['secret'] = isset( $merged['secret'] ) ? (string) $merged['secret'] : '';

            return $merged;
        }

        /**
         * 스타일 센터 설정 변경 이벤트
         *
         * @param array  $new
         * @param array  $old
         * @param string $source
         */
        public function on_style_settings_updated( $new, $old, $source ) {
            $this->maybe_send( 'style_settings_updated', array(
                'source' => (string) $source,
                'new'    => is_array( $new ) ? $new : array(),
                'old'    => is_array( $old ) ? $old : array(),
            ) );
        }

        /**
         * Admin Center 변경 이벤트
         *
         * @param array  $new
         * @param array  $old
         * @param string $source
         */
        public function on_admin_center_updated( $new, $old, $source ) {
            $this->maybe_send( 'admin_center_updated', array(
                'source' => (string) $source,
                'new'    => is_array( $new ) ? $new : array(),
                'old'    => is_array( $old ) ? $old : array(),
            ) );
        }

        /**
         * 설정에 따라 전송 여부 판단 후 Webhook 전송
         *
         * @param string $event
         * @param array  $data
         * @param bool   $force_blocking 테스트용
         * @return array 결과(테스트 모드에서만 사용)
         */
        public function maybe_send( $event, $data, $force_blocking = false ) {
            $cfg = $this->get_config();
            if ( empty( $cfg['enabled'] ) ) {
                return array();
            }

            if ( empty( $cfg['endpoints'] ) ) {
                return array();
            }

            if ( ! in_array( $event, $cfg['events'], true ) ) {
                return array();
            }

            $payload = $this->build_payload( $event, $data, $cfg );
            return $this->send_to_endpoints( $event, $payload, $cfg, $force_blocking );
        }

        /**
         * 페이로드 생성
         */
        private function build_payload( $event, $data, $cfg ) {
            $version = defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '';
            $edition = defined( 'JJ_STYLE_GUIDE_EDITION' ) ? JJ_STYLE_GUIDE_EDITION : '';
            $license = defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ? JJ_STYLE_GUIDE_LICENSE_TYPE : '';

            $user = array();
            if ( function_exists( 'wp_get_current_user' ) ) {
                $u = wp_get_current_user();
                if ( $u && isset( $u->ID ) ) {
                    $user = array(
                        'id'    => (int) $u->ID,
                        'login' => isset( $u->user_login ) ? (string) $u->user_login : '',
                    );
                }
            }

            $base = array(
                'event'     => (string) $event,
                'createdAt' => time(),
                'site'      => function_exists( 'home_url' ) ? home_url() : '',
                'plugin'    => array(
                    'version' => (string) $version,
                    'edition' => (string) $edition,
                    'license' => (string) $license,
                ),
                'user'      => $user,
            );

            if ( isset( $cfg['payload_mode'] ) && 'full' === $cfg['payload_mode'] ) {
                $base['data'] = $data;
            } else {
                // minimal: 값은 최소화 (키 목록/변경 여부 중심)
                $new = isset( $data['new'] ) && is_array( $data['new'] ) ? $data['new'] : array();
                $old = isset( $data['old'] ) && is_array( $data['old'] ) ? $data['old'] : array();
                $base['data'] = array(
                    'source'        => isset( $data['source'] ) ? (string) $data['source'] : '',
                    'new_keys'       => array_keys( $new ),
                    'old_keys'       => array_keys( $old ),
                    'new_size'       => is_array( $new ) ? count( $new ) : 0,
                    'old_size'       => is_array( $old ) ? count( $old ) : 0,
                );
            }

            return $base;
        }

        /**
         * 실제 전송
         *
         * @return array<string, mixed> 테스트 모드 결과
         */
        private function send_to_endpoints( $event, $payload, $cfg, $force_blocking ) {
            if ( ! function_exists( 'wp_remote_post' ) ) {
                return array();
            }

            $body = function_exists( 'wp_json_encode' ) ? wp_json_encode( $payload ) : json_encode( $payload );
            if ( ! is_string( $body ) ) {
                $body = '';
            }

            $timestamp = (string) time();
            $signature = '';
            if ( ! empty( $cfg['secret'] ) && function_exists( 'hash_hmac' ) ) {
                $signature = hash_hmac( 'sha256', $timestamp . '.' . $body, (string) $cfg['secret'] );
            }

            $headers = array(
                'Content-Type'   => 'application/json; charset=utf-8',
                'X-JJ-Event'     => (string) $event,
                'X-JJ-Timestamp' => $timestamp,
            );
            if ( '' !== $signature ) {
                $headers['X-JJ-Signature'] = $signature;
            }

            $timeout = isset( $cfg['timeout_seconds'] ) ? (int) $cfg['timeout_seconds'] : 5;

            $results = array();
            foreach ( $cfg['endpoints'] as $url ) {
                $args = array(
                    'headers'  => $headers,
                    'body'     => $body,
                    'timeout'  => $timeout,
                    'blocking' => $force_blocking ? true : false,
                );

                $resp = wp_remote_post( $url, $args );

                if ( $force_blocking ) {
                    if ( function_exists( 'is_wp_error' ) && is_wp_error( $resp ) ) {
                        $results[ $url ] = array(
                            'ok'    => false,
                            'error' => $resp->get_error_message(),
                        );
                        continue;
                    }
                    $code = function_exists( 'wp_remote_retrieve_response_code' ) ? (int) wp_remote_retrieve_response_code( $resp ) : 0;
                    $results[ $url ] = array(
                        'ok'   => $code >= 200 && $code < 300,
                        'code' => $code,
                    );
                }
            }

            return $results;
        }

        /**
         * AJAX: Webhook 테스트 전송
         */
        public function ajax_test_webhook() {
            check_ajax_referer( 'jj_admin_center_save_action', 'security' );
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'jj-style-guide' ) ) );
            }

            $payload = array(
                'source' => 'admin_center_test',
                'new'    => array( 'test' => true ),
                'old'    => array(),
            );

            $results = $this->maybe_send( 'test', $payload, true );

            // 테스트는 events 제한을 우회(강제)
            if ( empty( $results ) ) {
                $cfg = $this->get_config();
                if ( empty( $cfg['enabled'] ) ) {
                    wp_send_json_error( array( 'message' => __( 'Webhook이 비활성화 상태입니다.', 'jj-style-guide' ) ) );
                }
                if ( empty( $cfg['endpoints'] ) ) {
                    wp_send_json_error( array( 'message' => __( 'Webhook URL이 설정되어 있지 않습니다.', 'jj-style-guide' ) ) );
                }

                $forced = $this->send_to_endpoints( 'test', $this->build_payload( 'test', $payload, array_merge( $cfg, array( 'payload_mode' => 'minimal' ) ) ), $cfg, true );
                wp_send_json_success( array( 'results' => $forced ) );
            }

            wp_send_json_success( array( 'results' => $results ) );
        }
    }
}



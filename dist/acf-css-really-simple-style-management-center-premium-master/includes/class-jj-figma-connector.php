<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 13] Figma Connector
 * 
 * Figma API를 통한 디자인 토큰 동기화
 * - Figma에서 색상/타이포그래피 가져오기
 * - JJ 스타일 토큰을 Figma Variables JSON으로 내보내기
 * 
 * 참고: 이 기능은 사용자가 직접 Figma API 키를 발급받아 연동해야 합니다.
 * 플러그인에는 API 키가 탑재되지 않습니다.
 * 
 * @since 12.0.0
 */
class JJ_Figma_Connector {

    private static $instance = null;
    private $api_base = 'https://api.figma.com/v1';
    private $options_key = 'jj_figma_connector_options';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function init() {
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_figma_test_connection', array( $this, 'ajax_test_connection' ) );
        add_action( 'wp_ajax_jj_figma_import_tokens', array( $this, 'ajax_import_tokens' ) );
        add_action( 'wp_ajax_jj_figma_export_tokens', array( $this, 'ajax_export_tokens' ) );
        add_action( 'wp_ajax_jj_figma_save_settings', array( $this, 'ajax_save_settings' ) );
        add_action( 'wp_ajax_jj_figma_get_settings', array( $this, 'ajax_get_settings' ) );
    }

    /**
     * 설정 가져오기
     */
    public function get_settings() {
        $defaults = array(
            'api_token' => '',
            'file_key' => '',
            'last_sync' => '',
            'sync_colors' => true,
            'sync_typography' => true,
            'auto_sync' => false,
        );
        $saved = get_option( $this->options_key, array() );
        return wp_parse_args( $saved, $defaults );
    }

    /**
     * 설정 저장
     */
    public function save_settings( $settings ) {
        $current = $this->get_settings();
        $new = wp_parse_args( $settings, $current );
        
        // API 토큰은 암호화해서 저장 (간단한 base64)
        if ( isset( $new['api_token'] ) && ! empty( $new['api_token'] ) ) {
            $new['api_token_encrypted'] = base64_encode( $new['api_token'] );
            unset( $new['api_token'] );
        }
        
        update_option( $this->options_key, $new );
        return true;
    }

    /**
     * API 토큰 가져오기 (복호화)
     */
    private function get_api_token() {
        $settings = get_option( $this->options_key, array() );
        if ( isset( $settings['api_token_encrypted'] ) && ! empty( $settings['api_token_encrypted'] ) ) {
            return base64_decode( $settings['api_token_encrypted'] );
        }
        return '';
    }

    /**
     * Figma API 호출
     */
    private function api_request( $endpoint, $method = 'GET', $body = null ) {
        $token = $this->get_api_token();
        if ( empty( $token ) ) {
            return new WP_Error( 'no_token', __( 'Figma API 토큰이 설정되지 않았습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $url = $this->api_base . $endpoint;
        $args = array(
            'method' => $method,
            'headers' => array(
                'X-Figma-Token' => $token,
                'Content-Type' => 'application/json',
            ),
            'timeout' => 30,
        );

        if ( $body && in_array( $method, array( 'POST', 'PUT', 'PATCH' ), true ) ) {
            $args['body'] = wp_json_encode( $body );
        }

        $response = wp_remote_request( $url, $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( $code >= 400 ) {
            $error_msg = isset( $data['err'] ) ? $data['err'] : __( 'Figma API 오류', 'acf-css-really-simple-style-management-center' );
            return new WP_Error( 'api_error', $error_msg, array( 'status' => $code ) );
        }

        return $data;
    }

    /**
     * 연결 테스트
     */
    public function test_connection() {
        $result = $this->api_request( '/me' );
        if ( is_wp_error( $result ) ) {
            return $result;
        }
        return array(
            'success' => true,
            'user' => array(
                'id' => $result['id'] ?? '',
                'handle' => $result['handle'] ?? '',
                'email' => $result['email'] ?? '',
            ),
        );
    }

    /**
     * Figma 파일에서 스타일 가져오기
     */
    public function import_tokens_from_file( $file_key ) {
        if ( empty( $file_key ) ) {
            return new WP_Error( 'no_file_key', __( 'Figma 파일 키가 필요합니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        // 파일 스타일 가져오기
        $styles_result = $this->api_request( '/files/' . $file_key . '/styles' );
        if ( is_wp_error( $styles_result ) ) {
            return $styles_result;
        }

        // 파일 기본 정보 가져오기
        $file_result = $this->api_request( '/files/' . $file_key . '?depth=1' );
        if ( is_wp_error( $file_result ) ) {
            return $file_result;
        }

        $tokens = array(
            'colors' => array(),
            'typography' => array(),
            'file_name' => $file_result['name'] ?? '',
            'last_modified' => $file_result['lastModified'] ?? '',
        );

        // 스타일 파싱
        $styles = isset( $styles_result['meta']['styles'] ) ? $styles_result['meta']['styles'] : array();
        foreach ( $styles as $style ) {
            $style_type = $style['style_type'] ?? '';
            $name = $style['name'] ?? '';
            $key = $style['key'] ?? '';
            $node_id = $style['node_id'] ?? '';

            if ( $style_type === 'FILL' ) {
                // 색상 스타일 - 상세 정보를 가져와야 함
                $tokens['colors'][] = array(
                    'name' => $name,
                    'key' => $key,
                    'node_id' => $node_id,
                );
            } elseif ( $style_type === 'TEXT' ) {
                // 텍스트 스타일
                $tokens['typography'][] = array(
                    'name' => $name,
                    'key' => $key,
                    'node_id' => $node_id,
                );
            }
        }

        // 스타일 노드의 상세 정보 가져오기
        if ( ! empty( $tokens['colors'] ) || ! empty( $tokens['typography'] ) ) {
            $node_ids = array();
            foreach ( $tokens['colors'] as $c ) {
                if ( ! empty( $c['node_id'] ) ) $node_ids[] = $c['node_id'];
            }
            foreach ( $tokens['typography'] as $t ) {
                if ( ! empty( $t['node_id'] ) ) $node_ids[] = $t['node_id'];
            }

            if ( ! empty( $node_ids ) ) {
                $nodes_result = $this->api_request( '/files/' . $file_key . '/nodes?ids=' . implode( ',', $node_ids ) );
                if ( ! is_wp_error( $nodes_result ) && isset( $nodes_result['nodes'] ) ) {
                    $nodes = $nodes_result['nodes'];

                    // 색상 값 추출
                    foreach ( $tokens['colors'] as &$color ) {
                        $node_id = $color['node_id'];
                        if ( isset( $nodes[ $node_id ]['document']['fills'][0] ) ) {
                            $fill = $nodes[ $node_id ]['document']['fills'][0];
                            if ( isset( $fill['color'] ) ) {
                                $r = round( ( $fill['color']['r'] ?? 0 ) * 255 );
                                $g = round( ( $fill['color']['g'] ?? 0 ) * 255 );
                                $b = round( ( $fill['color']['b'] ?? 0 ) * 255 );
                                $color['hex'] = sprintf( '#%02x%02x%02x', $r, $g, $b );
                            }
                        }
                    }

                    // 타이포그래피 값 추출
                    foreach ( $tokens['typography'] as &$typo ) {
                        $node_id = $typo['node_id'];
                        if ( isset( $nodes[ $node_id ]['document']['style'] ) ) {
                            $style = $nodes[ $node_id ]['document']['style'];
                            $typo['font_family'] = $style['fontFamily'] ?? '';
                            $typo['font_weight'] = $style['fontWeight'] ?? 400;
                            $typo['font_size'] = $style['fontSize'] ?? 16;
                            $typo['line_height'] = isset( $style['lineHeightPercentFontSize'] ) 
                                ? round( $style['lineHeightPercentFontSize'] / 100, 2 ) 
                                : 1.5;
                            $typo['letter_spacing'] = isset( $style['letterSpacing'] ) 
                                ? $style['letterSpacing'] 
                                : 0;
                        }
                    }
                }
            }
        }

        return $tokens;
    }

    /**
     * JJ 스타일 토큰을 Figma Variables JSON 형식으로 내보내기
     */
    public function export_tokens_as_figma_json() {
        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $options = (array) get_option( $key, array() );

        $variables = array(
            'version' => '1.0',
            'generated_by' => 'JJ Style Guide',
            'generated_at' => current_time( 'c' ),
            'collections' => array(
                array(
                    'name' => 'Brand Colors',
                    'modes' => array(
                        array( 'name' => 'Default', 'modeId' => 'default' ),
                    ),
                    'variables' => array(),
                ),
                array(
                    'name' => 'Typography',
                    'modes' => array(
                        array( 'name' => 'Default', 'modeId' => 'default' ),
                    ),
                    'variables' => array(),
                ),
            ),
        );

        // 색상 변수
        if ( isset( $options['palettes'] ) ) {
            $brand = $options['palettes']['brand'] ?? array();
            $system = $options['palettes']['system'] ?? array();

            $color_vars = array();
            foreach ( $brand as $k => $v ) {
                if ( ! empty( $v ) && preg_match( '/^#[0-9A-Fa-f]{3,8}$/', $v ) ) {
                    $color_vars[] = array(
                        'name' => 'brand/' . str_replace( '_', '-', $k ),
                        'resolvedType' => 'COLOR',
                        'valuesByMode' => array(
                            'default' => $this->hex_to_figma_rgba( $v ),
                        ),
                    );
                }
            }
            foreach ( $system as $k => $v ) {
                if ( ! empty( $v ) && preg_match( '/^#[0-9A-Fa-f]{3,8}$/', $v ) ) {
                    $color_vars[] = array(
                        'name' => 'system/' . str_replace( '_', '-', $k ),
                        'resolvedType' => 'COLOR',
                        'valuesByMode' => array(
                            'default' => $this->hex_to_figma_rgba( $v ),
                        ),
                    );
                }
            }
            $variables['collections'][0]['variables'] = $color_vars;
        }

        // 타이포그래피 변수 (숫자 값)
        if ( isset( $options['typography'] ) ) {
            $typo_vars = array();
            foreach ( $options['typography'] as $tag => $props ) {
                if ( ! is_array( $props ) ) continue;

                $font_size = 16;
                if ( isset( $props['font_size'] ) && is_array( $props['font_size'] ) ) {
                    $font_size = floatval( $props['font_size']['desktop'] ?? 16 );
                } elseif ( isset( $props['font_size'] ) ) {
                    $font_size = floatval( $props['font_size'] );
                }

                $typo_vars[] = array(
                    'name' => $tag . '/font-size',
                    'resolvedType' => 'FLOAT',
                    'valuesByMode' => array(
                        'default' => $font_size,
                    ),
                );

                if ( isset( $props['line_height'] ) && is_numeric( $props['line_height'] ) ) {
                    $typo_vars[] = array(
                        'name' => $tag . '/line-height',
                        'resolvedType' => 'FLOAT',
                        'valuesByMode' => array(
                            'default' => floatval( $props['line_height'] ),
                        ),
                    );
                }

                if ( isset( $props['letter_spacing'] ) && is_numeric( $props['letter_spacing'] ) ) {
                    $typo_vars[] = array(
                        'name' => $tag . '/letter-spacing',
                        'resolvedType' => 'FLOAT',
                        'valuesByMode' => array(
                            'default' => floatval( $props['letter_spacing'] ),
                        ),
                    );
                }
            }
            $variables['collections'][1]['variables'] = $typo_vars;
        }

        return $variables;
    }

    /**
     * HEX를 Figma RGBA 형식으로 변환
     */
    private function hex_to_figma_rgba( $hex ) {
        $hex = ltrim( $hex, '#' );
        if ( strlen( $hex ) === 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if ( strlen( $hex ) < 6 ) {
            $hex = str_pad( $hex, 6, '0' );
        }

        $r = hexdec( substr( $hex, 0, 2 ) ) / 255;
        $g = hexdec( substr( $hex, 2, 2 ) ) / 255;
        $b = hexdec( substr( $hex, 4, 2 ) ) / 255;
        $a = 1;

        if ( strlen( $hex ) === 8 ) {
            $a = hexdec( substr( $hex, 6, 2 ) ) / 255;
        }

        return array( 'r' => $r, 'g' => $g, 'b' => $b, 'a' => $a );
    }

    /**
     * AJAX: 연결 테스트
     */
    public function ajax_test_connection() {
        check_ajax_referer( 'jj_figma_connector_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        // 임시 토큰으로 테스트
        $temp_token = isset( $_POST['api_token'] ) ? sanitize_text_field( wp_unslash( $_POST['api_token'] ) ) : '';
        if ( ! empty( $temp_token ) ) {
            // 임시 저장
            $current = get_option( $this->options_key, array() );
            $current['api_token_encrypted'] = base64_encode( $temp_token );
            update_option( $this->options_key, $current );
        }

        $result = $this->test_connection();
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( $result );
    }

    /**
     * AJAX: 토큰 가져오기
     */
    public function ajax_import_tokens() {
        check_ajax_referer( 'jj_figma_connector_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $file_key = isset( $_POST['file_key'] ) ? sanitize_text_field( wp_unslash( $_POST['file_key'] ) ) : '';
        if ( empty( $file_key ) ) {
            // 저장된 file_key 사용
            $settings = $this->get_settings();
            $file_key = $settings['file_key'] ?? '';
        }

        $result = $this->import_tokens_from_file( $file_key );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        // 마지막 동기화 시간 업데이트
        $settings = get_option( $this->options_key, array() );
        $settings['last_sync'] = current_time( 'mysql' );
        $settings['file_key'] = $file_key;
        update_option( $this->options_key, $settings );

        wp_send_json_success( $result );
    }

    /**
     * AJAX: 토큰 내보내기
     */
    public function ajax_export_tokens() {
        check_ajax_referer( 'jj_figma_connector_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $json = $this->export_tokens_as_figma_json();
        $filename = sanitize_file_name( get_bloginfo( 'name' ) ) . '-figma-variables.json';

        wp_send_json_success( array(
            'content' => wp_json_encode( $json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ),
            'filename' => $filename,
        ) );
    }

    /**
     * AJAX: 설정 저장
     */
    public function ajax_save_settings() {
        check_ajax_referer( 'jj_figma_connector_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $settings = array();
        if ( isset( $_POST['api_token'] ) ) {
            $settings['api_token'] = sanitize_text_field( wp_unslash( $_POST['api_token'] ) );
        }
        if ( isset( $_POST['file_key'] ) ) {
            $settings['file_key'] = sanitize_text_field( wp_unslash( $_POST['file_key'] ) );
        }
        if ( isset( $_POST['sync_colors'] ) ) {
            $settings['sync_colors'] = (bool) $_POST['sync_colors'];
        }
        if ( isset( $_POST['sync_typography'] ) ) {
            $settings['sync_typography'] = (bool) $_POST['sync_typography'];
        }

        $this->save_settings( $settings );

        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * AJAX: 설정 가져오기
     */
    public function ajax_get_settings() {
        check_ajax_referer( 'jj_figma_connector_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $settings = $this->get_settings();
        // API 토큰은 마스킹
        $has_token = ! empty( $this->get_api_token() );
        $settings['has_api_token'] = $has_token;
        unset( $settings['api_token_encrypted'] );

        wp_send_json_success( $settings );
    }
}

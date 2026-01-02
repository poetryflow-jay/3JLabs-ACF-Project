<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * [Phase 19.1] Figma Advanced Integration
 * 
 * W Design Kit 스타일의 고급 Figma 통합 기능
 * - Figma Variables 자동 추출 및 매핑
 * - 컴포넌트 → WordPress 블록 변환
 * - 레이아웃 자동 생성
 * - 양방향 실시간 동기화
 * 
 * @since 13.4.7
 * @version 19.1.0
 */
class JJ_Figma_Advanced_Integration {

    private static $instance = null;
    private $figma_connector = null;
    private $options_key = 'jj_figma_advanced_integration';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Figma Connector 인스턴스 가져오기
        if ( class_exists( 'JJ_Figma_Connector' ) ) {
            $this->figma_connector = JJ_Figma_Connector::instance();
        }
    }

    public function init() {
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_figma_get_variables', array( $this, 'ajax_get_figma_variables' ) );
        add_action( 'wp_ajax_jj_figma_map_to_wordpress', array( $this, 'ajax_map_to_wordpress' ) );
        add_action( 'wp_ajax_jj_figma_convert_component', array( $this, 'ajax_convert_component' ) );
        add_action( 'wp_ajax_jj_figma_generate_layout', array( $this, 'ajax_generate_layout' ) );
        add_action( 'wp_ajax_jj_figma_sync_bidirectional', array( $this, 'ajax_sync_bidirectional' ) );
        add_action( 'wp_ajax_jj_figma_get_sync_status', array( $this, 'ajax_get_sync_status' ) );
    }

    /**
     * Figma Variables 가져오기 (Figma Variables API)
     */
    public function get_figma_variables( $file_key ) {
        if ( ! $this->figma_connector ) {
            return new WP_Error( 'no_connector', __( 'Figma Connector가 초기화되지 않았습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        // Figma Variables API 호출 (v1/variables/local)
        $result = $this->figma_connector->api_request( '/files/' . $file_key . '/variables/local' );
        
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Variables 구조 파싱
        $variables = array(
            'collections' => array(),
            'modes' => array(),
            'variables' => array(),
        );

        if ( isset( $result['meta']['variableCollections'] ) ) {
            foreach ( $result['meta']['variableCollections'] as $collection ) {
                $variables['collections'][] = array(
                    'id' => $collection['id'] ?? '',
                    'name' => $collection['name'] ?? '',
                    'modes' => $collection['modes'] ?? array(),
                );
            }
        }

        if ( isset( $result['meta']['variables'] ) ) {
            foreach ( $result['meta']['variables'] as $variable ) {
                $variables['variables'][] = array(
                    'id' => $variable['id'] ?? '',
                    'name' => $variable['name'] ?? '',
                    'type' => $variable['resolvedType'] ?? '',
                    'collection_id' => $variable['variableCollectionId'] ?? '',
                    'values_by_mode' => $variable['valuesByMode'] ?? array(),
                );
            }
        }

        return $variables;
    }

    /**
     * Figma Variables를 WordPress 스타일 토큰으로 자동 매핑
     */
    public function map_figma_variables_to_wordpress( $figma_variables, $mapping_rules = array() ) {
        $key = defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ? JJ_STYLE_GUIDE_OPTIONS_KEY : 'jj_style_guide_options';
        $options = (array) get_option( $key, array() );

        // 기본 매핑 규칙
        $default_rules = array(
            'color' => array(
                'brand/' => 'palettes.brand.',
                'system/' => 'palettes.system.',
                'semantic/' => 'palettes.semantic.',
            ),
            'typography' => array(
                'font-size/' => 'typography.',
                'line-height/' => 'typography.',
                'letter-spacing/' => 'typography.',
            ),
        );

        $rules = wp_parse_args( $mapping_rules, $default_rules );
        $mapped_count = 0;

        // 색상 변수 매핑
        if ( isset( $figma_variables['variables'] ) ) {
            foreach ( $figma_variables['variables'] as $variable ) {
                $name = $variable['name'] ?? '';
                $type = $variable['type'] ?? '';
                $values = $variable['values_by_mode'] ?? array();

                if ( $type === 'COLOR' && ! empty( $values ) ) {
                    // 첫 번째 모드의 값 사용
                    $first_mode_value = reset( $values );
                    if ( is_array( $first_mode_value ) && isset( $first_mode_value['r'] ) ) {
                        $hex = $this->figma_rgba_to_hex( $first_mode_value );
                        
                        // 매핑 규칙에 따라 WordPress 옵션에 저장
                        foreach ( $rules['color'] as $prefix => $wp_path ) {
                            if ( strpos( $name, $prefix ) === 0 ) {
                                $key_name = str_replace( $prefix, '', $name );
                                $key_name = str_replace( '/', '_', $key_name );
                                
                                $path_parts = explode( '.', rtrim( $wp_path, '.' ) );
                                if ( count( $path_parts ) === 2 ) {
                                    if ( ! isset( $options[ $path_parts[0] ] ) ) {
                                        $options[ $path_parts[0] ] = array();
                                    }
                                    if ( ! isset( $options[ $path_parts[0] ][ $path_parts[1] ] ) ) {
                                        $options[ $path_parts[0] ][ $path_parts[1] ] = array();
                                    }
                                    $options[ $path_parts[0] ][ $path_parts[1] ][ $key_name ] = $hex;
                                    $mapped_count++;
                                }
                            }
                        }
                    }
                } elseif ( $type === 'FLOAT' && ! empty( $values ) ) {
                    // 타이포그래피 변수 매핑
                    $first_mode_value = reset( $values );
                    if ( is_numeric( $first_mode_value ) ) {
                        foreach ( $rules['typography'] as $prefix => $wp_path ) {
                            if ( strpos( $name, $prefix ) === 0 ) {
                                // 예: "h1/font-size" -> "h1" 태그의 "font_size" 속성
                                $parts = explode( '/', $name );
                                if ( count( $parts ) === 2 ) {
                                    $tag = $parts[0];
                                    $prop = str_replace( '-', '_', $parts[1] );
                                    
                                    if ( ! isset( $options['typography'] ) ) {
                                        $options['typography'] = array();
                                    }
                                    if ( ! isset( $options['typography'][ $tag ] ) ) {
                                        $options['typography'][ $tag ] = array();
                                    }
                                    
                                    if ( $prop === 'font_size' ) {
                                        if ( ! isset( $options['typography'][ $tag ]['font_size'] ) ) {
                                            $options['typography'][ $tag ]['font_size'] = array();
                                        }
                                        $options['typography'][ $tag ]['font_size']['desktop'] = floatval( $first_mode_value );
                                    } else {
                                        $options['typography'][ $tag ][ $prop ] = floatval( $first_mode_value );
                                    }
                                    $mapped_count++;
                                }
                            }
                        }
                    }
                }
            }
        }

        // 옵션 저장
        if ( $mapped_count > 0 ) {
            update_option( $key, $options );
        }

        return array(
            'success' => true,
            'mapped_count' => $mapped_count,
            'message' => sprintf( __( '%d개의 변수가 WordPress에 매핑되었습니다.', 'acf-css-really-simple-style-management-center' ), $mapped_count ),
        );
    }

    /**
     * Figma RGBA를 HEX로 변환
     */
    private function figma_rgba_to_hex( $rgba ) {
        $r = round( ( $rgba['r'] ?? 0 ) * 255 );
        $g = round( ( $rgba['g'] ?? 0 ) * 255 );
        $b = round( ( $rgba['b'] ?? 0 ) * 255 );
        $a = $rgba['a'] ?? 1;

        if ( $a < 1 ) {
            return sprintf( '#%02x%02x%02x%02x', $r, $g, $b, round( $a * 255 ) );
        }
        return sprintf( '#%02x%02x%02x', $r, $g, $b );
    }

    /**
     * Figma 컴포넌트를 WordPress 블록으로 변환
     */
    public function convert_component_to_block( $file_key, $node_id ) {
        if ( ! $this->figma_connector ) {
            return new WP_Error( 'no_connector', __( 'Figma Connector가 초기화되지 않았습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        // 노드 정보 가져오기
        $result = $this->figma_connector->api_request( '/files/' . $file_key . '/nodes?ids=' . $node_id );
        
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        if ( ! isset( $result['nodes'][ $node_id ] ) ) {
            return new WP_Error( 'node_not_found', __( '노드를 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $node = $result['nodes'][ $node_id ]['document'];
        
        // 컴포넌트 정보 추출
        $component = array(
            'name' => $node['name'] ?? '',
            'type' => $node['type'] ?? '',
            'width' => $node['absoluteBoundingBox']['width'] ?? 0,
            'height' => $node['absoluteBoundingBox']['height'] ?? 0,
            'styles' => array(),
            'children' => array(),
        );

        // 스타일 추출
        if ( isset( $node['fills'] ) && is_array( $node['fills'] ) ) {
            foreach ( $node['fills'] as $fill ) {
                if ( isset( $fill['color'] ) ) {
                    $component['styles']['background'] = $this->figma_rgba_to_hex( $fill['color'] );
                }
            }
        }

        // WordPress 블록 구조 생성
        $block = array(
            'blockName' => 'jj-style-guide/component',
            'attrs' => array(
                'componentName' => $component['name'],
                'width' => $component['width'],
                'height' => $component['height'],
            ),
            'innerBlocks' => array(),
            'innerHTML' => $this->generate_block_html( $component ),
        );

        return $block;
    }

    /**
     * 컴포넌트에서 블록 HTML 생성
     */
    private function generate_block_html( $component ) {
        $styles = array();
        if ( isset( $component['styles']['background'] ) ) {
            $styles[] = 'background-color: ' . esc_attr( $component['styles']['background'] );
        }
        if ( isset( $component['width'] ) ) {
            $styles[] = 'width: ' . esc_attr( $component['width'] ) . 'px';
        }
        if ( isset( $component['height'] ) ) {
            $styles[] = 'height: ' . esc_attr( $component['height'] ) . 'px';
        }

        $style_attr = ! empty( $styles ) ? ' style="' . implode( '; ', $styles ) . '"' : '';
        
        return '<div class="jj-figma-component" data-component="' . esc_attr( $component['name'] ) . '"' . $style_attr . '></div>';
    }

    /**
     * AJAX: Figma Variables 가져오기
     */
    public function ajax_get_figma_variables() {
        check_ajax_referer( 'jj_figma_connector_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $file_key = isset( $_POST['file_key'] ) ? sanitize_text_field( wp_unslash( $_POST['file_key'] ) ) : '';
        if ( empty( $file_key ) ) {
            wp_send_json_error( array( 'message' => __( 'Figma 파일 키가 필요합니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $result = $this->get_figma_variables( $file_key );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( $result );
    }

    /**
     * AJAX: WordPress로 매핑
     */
    public function ajax_map_to_wordpress() {
        check_ajax_referer( 'jj_figma_connector_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $figma_variables = isset( $_POST['variables'] ) ? json_decode( wp_unslash( $_POST['variables'] ), true ) : array();
        $mapping_rules = isset( $_POST['mapping_rules'] ) ? json_decode( wp_unslash( $_POST['mapping_rules'] ), true ) : array();

        if ( empty( $figma_variables ) ) {
            wp_send_json_error( array( 'message' => __( 'Figma Variables 데이터가 필요합니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $result = $this->map_figma_variables_to_wordpress( $figma_variables, $mapping_rules );
        wp_send_json_success( $result );
    }

    /**
     * AJAX: 컴포넌트 변환
     */
    public function ajax_convert_component() {
        check_ajax_referer( 'jj_figma_connector_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $file_key = isset( $_POST['file_key'] ) ? sanitize_text_field( wp_unslash( $_POST['file_key'] ) ) : '';
        $node_id = isset( $_POST['node_id'] ) ? sanitize_text_field( wp_unslash( $_POST['node_id'] ) ) : '';

        if ( empty( $file_key ) || empty( $node_id ) ) {
            wp_send_json_error( array( 'message' => __( '파일 키와 노드 ID가 필요합니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $result = $this->convert_component_to_block( $file_key, $node_id );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( $result );
    }

    /**
     * AJAX: 레이아웃 생성 (플레이스홀더)
     */
    public function ajax_generate_layout() {
        check_ajax_referer( 'jj_figma_connector_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        // Phase 19.1 후반에 구현 예정
        wp_send_json_error( array( 'message' => __( '이 기능은 Phase 19.1 후반에 구현될 예정입니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * AJAX: 양방향 동기화 (플레이스홀더)
     */
    public function ajax_sync_bidirectional() {
        check_ajax_referer( 'jj_figma_connector_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        // Phase 19.1.3에 구현 예정
        wp_send_json_error( array( 'message' => __( '이 기능은 Phase 19.1.3에 구현될 예정입니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * AJAX: 동기화 상태 확인
     */
    public function ajax_get_sync_status() {
        check_ajax_referer( 'jj_figma_connector_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ), 403 );
        }

        $settings = get_option( $this->options_key, array() );
        $status = array(
            'last_sync' => $settings['last_sync'] ?? '',
            'sync_enabled' => $settings['auto_sync'] ?? false,
            'conflicts' => $settings['conflicts'] ?? array(),
        );

        wp_send_json_success( $status );
    }
}

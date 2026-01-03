<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 3J Labs One-Click Demo Importer
 * - 마케팅 핵심: 사용자의 초기 진입 장벽을 완전히 제거
 * - 버튼 하나로 제니가 설계한 스타일을 즉시 적용
 */
class JJ_Demo_Importer {
    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        add_action( 'wp_ajax_jj_import_style_preset', array( $this, 'ajax_import_preset' ) );
    }

    /**
     * AJAX: 선택한 프리셋 데이터를 옵션에 덮어쓰기
     */
    public function ajax_import_preset() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $preset_id = isset( $_POST['preset_id'] ) ? sanitize_key( $_POST['preset_id'] ) : '';
        $presets = JJ_Style_Presets::instance()->get_presets();

        if ( ! isset( $presets[ $preset_id ] ) ) {
            wp_send_json_error( array( 'message' => __( '유효하지 않은 프리셋입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $preset_data = $presets[ $preset_id ];
        
        // 현재 옵션 가져오기
        $current_options = get_option( 'jj_style_guide_options', array() );
        
        // 프리셋 데이터 병합
        $new_options = wp_parse_args( array(
            'colors'     => $preset_data['colors'],
            'typography' => $preset_data['typography'],
            'buttons'    => $preset_data['buttons'],
        ), $current_options );

        // 저장
        $success = update_option( 'jj_style_guide_options', $new_options );

        if ( $success ) {
            wp_send_json_success( array( 
                'message' => sprintf( __( '"%s" 프리셋이 성공적으로 적용되었습니다!', 'acf-css-really-simple-style-management-center' ), $preset_data['name'] ) 
            ) );
        } else {
            wp_send_json_error( array( 'message' => __( '프리셋 적용에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
    }
}

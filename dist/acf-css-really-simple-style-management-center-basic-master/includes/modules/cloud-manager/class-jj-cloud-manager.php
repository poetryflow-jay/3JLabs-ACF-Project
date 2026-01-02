<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Cloud Manager
 * 
 * 스타일 설정을 클라우드(Neural Link Server)에 저장하고 불러오는 클라이언트 모듈입니다.
 * 
 * @since v5.8.0
 */
class JJ_Cloud_Manager {

    private static $instance = null;
    private $api_base_url = 'https://j-j-labs.com/wp-json/jj-neural-link/v1/cloud';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // AJAX 핸들러
        add_action( 'wp_ajax_jj_cloud_export', array( $this, 'ajax_export' ) );
        add_action( 'wp_ajax_jj_cloud_import', array( $this, 'ajax_import' ) );
    }

    /**
     * Cloud API Base URL (필터로 변경 가능)
     *
     * @return string
     */
    private function get_api_base_url() {
        $base = $this->api_base_url;
        if ( function_exists( 'apply_filters' ) ) {
            $base = apply_filters( 'jj_cloud_api_base_url', $base );
        }
        return rtrim( (string) $base, '/' );
    }

    /**
     * SSL Verify (보안 기본값: true)
     *
     * @return bool
     */
    private function get_sslverify() {
        $sslverify = true;
        if ( function_exists( 'apply_filters' ) ) {
            $sslverify = (bool) apply_filters( 'jj_cloud_sslverify', $sslverify );
        }
        return (bool) $sslverify;
    }

    /**
     * 멀티사이트 네트워크 전용 모드에서는 사이트별 Cloud 동기화 차단
     *
     * @return bool true면 차단
     */
    private function is_network_locked() {
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $ms = JJ_Multisite_Controller::instance();
                if ( method_exists( $ms, 'is_enabled' ) && method_exists( $ms, 'allow_site_override' ) ) {
                    return ( $ms->is_enabled() && ! $ms->allow_site_override() );
                }
            } catch ( Exception $e ) {
                return false;
            } catch ( Error $e ) {
                return false;
            }
        }
        return false;
    }

    /**
     * 클라우드로 설정 내보내기 (저장)
     */
    public function ajax_export() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        if ( $this->is_network_locked() ) {
            wp_send_json_error( array( 'message' => '이 사이트는 네트워크에서 스타일을 관리 중입니다. 네트워크 관리자에서 Cloud 동기화를 진행해주세요.' ) );
        }

        // 현재 설정 데이터 수집
        // Main Options
        $main_options = get_option( 'jj_style_guide_options', array() );
        // Visual Options
        $visual_options = get_option( 'jj_style_guide_visual_options', array() );
        
        $export_data = array(
            'version' => JJ_STYLE_GUIDE_VERSION,
            'timestamp' => current_time( 'mysql' ),
            'options' => $main_options,
            'visual_options' => $visual_options,
        );

        // API 호출
        $response = wp_remote_post( $this->get_api_base_url() . '/store', array(
            'timeout' => 15,
            'sslverify' => $this->get_sslverify(),
            'body' => array(
                'license_key' => get_option( 'jj_style_guide_license_key', '' ),
                'data' => wp_json_encode( $export_data )
            )
        ) );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => '서버 통신 오류: ' . $response->get_error_message() ) );
        }

        $body = wp_remote_retrieve_body( $response );
        $result = json_decode( $body, true );

        if ( isset( $result['success'] ) && $result['success'] ) {
            wp_send_json_success( array( 
                'share_code' => $result['share_code'],
                'message' => $result['message']
            ) );
        } else {
            $msg = isset( $result['message'] ) ? $result['message'] : '알 수 없는 오류';
            wp_send_json_error( array( 'message' => $msg ) );
        }
    }

    /**
     * 클라우드에서 설정 불러오기
     */
    public function ajax_import() {
        check_ajax_referer( 'jj_admin_center_save_action', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => '권한이 없습니다.' ) );
        }

        if ( $this->is_network_locked() ) {
            wp_send_json_error( array( 'message' => '이 사이트는 네트워크에서 스타일을 관리 중입니다. 네트워크 관리자에서 Cloud 동기화를 진행해주세요.' ) );
        }

        $share_code = isset( $_POST['share_code'] ) ? sanitize_text_field( $_POST['share_code'] ) : '';

        if ( empty( $share_code ) ) {
            wp_send_json_error( array( 'message' => '공유 코드를 입력하세요.' ) );
        }

        // API 호출
        $response = wp_remote_post( $this->get_api_base_url() . '/fetch', array(
            'timeout' => 15,
            'sslverify' => $this->get_sslverify(),
            'body' => array(
                'share_code' => $share_code
            )
        ) );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => '서버 통신 오류: ' . $response->get_error_message() ) );
        }

        $body = wp_remote_retrieve_body( $response );
        $result = json_decode( $body, true );

        if ( isset( $result['success'] ) && $result['success'] ) {
            $data = $result['data'];
            $old_options = get_option( 'jj_style_guide_options', array() );
            
            // 데이터 적용 (덮어쓰기)
            if ( isset( $data['options'] ) ) {
                update_option( 'jj_style_guide_options', $data['options'] );
            }
            if ( isset( $data['visual_options'] ) ) {
                update_option( 'jj_style_guide_visual_options', $data['visual_options'] );
            }

            // 캐시 플러시
            if ( class_exists( 'JJ_CSS_Cache' ) && method_exists( 'JJ_CSS_Cache', 'instance' ) ) {
                try {
                    $cache = JJ_CSS_Cache::instance();
                    if ( $cache && method_exists( $cache, 'flush' ) ) {
                        $cache->flush();
                    }
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }

            // Webhook/자동화 이벤트
            if ( function_exists( 'do_action' ) ) {
                $new_options = isset( $data['options'] ) && is_array( $data['options'] ) ? $data['options'] : array();
                do_action( 'jj_style_guide_settings_updated', $new_options, (array) $old_options, 'cloud_import' );
            }

            wp_send_json_success( array( 'message' => '설정을 성공적으로 불러왔습니다. 페이지를 새로고침하세요.' ) );
        } else {
            $msg = isset( $result['message'] ) ? $result['message'] : '데이터를 찾을 수 없습니다.';
            wp_send_json_error( array( 'message' => $msg ) );
        }
    }
}


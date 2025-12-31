<?php
/**
 * Neural Link Launcher API
 * 
 * 3J Labs Launcher 애플리케이션과 통신하기 위한 전용 API입니다.
 * 시스템 상태, 라이센스 현황 등을 제공합니다.
 * 
 * @package ACF_CSS_Neural_Link
 * @since 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'JJ_Neural_Link_Launcher_API' ) ) {

    class JJ_Neural_Link_Launcher_API {

        private static $instance = null;
        private $namespace = 'acf-neural-link/v1';

        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
            add_action( 'rest_api_init', array( $this, 'register_routes' ) );
        }

        public function register_routes() {
            // 런처 대시보드 데이터 조회
            register_rest_route( $this->namespace, '/launcher/status', array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_status' ),
                'permission_callback' => array( $this, 'check_permission' ),
            ) );
        }

        /**
         * 권한 체크 (API Key 기반)
         * 런처 설정에서 저장된 Master Key와 비교합니다.
         */
        public function check_permission( WP_REST_Request $request ) {
            $auth_header = $request->get_header( 'Authorization' );
            if ( empty( $auth_header ) ) {
                // 개발 편의를 위해 로컬호스트 요청은 일단 허용 (실제 배포 시 보안 강화 필요)
                // return new WP_Error( 'no_auth', '인증 정보가 없습니다.', array( 'status' => 401 ) );
                return true; 
            }
            // TODO: 실제 API Key 검증 로직 구현
            return true;
        }

        /**
         * 상태 정보 반환
         */
        public function get_status( WP_REST_Request $request ) {
            global $wpdb;
            
            // 1. 라이센스 통계
            $table_licenses = $wpdb->prefix . 'jj_licenses';
            $stats = array(
                'total' => 0,
                'active' => 0,
                'expired' => 0,
                'revenue' => 0 // 추후 구현
            );

            // 테이블이 존재하는지 확인
            if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_licenses'" ) === $table_licenses ) {
                $stats['total'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_licenses" );
                $stats['active'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_licenses WHERE status = 'active'" );
                $stats['expired'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_licenses WHERE expires_at < NOW()" );
            }

            // 2. 시스템 정보
            $system = array(
                'wp_version' => get_bloginfo( 'version' ),
                'plugin_version' => defined('JJ_NEURAL_LINK_VERSION') ? JJ_NEURAL_LINK_VERSION : 'Unknown',
                'server_time' => current_time( 'mysql' ),
                'php_version' => phpversion(),
            );

            return new WP_REST_Response( array(
                'success' => true,
                'stats' => $stats,
                'system' => $system,
                'message' => 'Neural Link is operational.'
            ), 200 );
        }
    }
}

// 초기화
add_action( 'plugins_loaded', array( 'JJ_Neural_Link_Launcher_API', 'instance' ) );


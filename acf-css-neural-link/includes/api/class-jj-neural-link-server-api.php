<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Neural Link Server API
 * 
 * 중앙 서버에서 클라이언트 플러그인의 업데이트 요청을 처리하고,
 * 라이센스 상태를 검증하여 적절한 패키지 URL을 제공합니다.
 * 
 * @since v3.0.0
 */
class JJ_Neural_Link_Server_API {

    private static $instance = null;
    private $namespace = 'jj-neural-link/v1';

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
        // 업데이트 체크 엔드포인트
        register_rest_route( $this->namespace, '/check-update', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'handle_check_update' ),
            'permission_callback' => '__return_true', // 라이센스 키로 자체 인증
        ) );
    }

    /**
     * 업데이트 체크 요청 처리
     */
    public function handle_check_update( $request ) {
        $params = $request->get_params();
        
        $license_key = isset( $params['license_key'] ) ? sanitize_text_field( $params['license_key'] ) : '';
        $current_version = isset( $params['version'] ) ? sanitize_text_field( $params['version'] ) : '';
        $site_url = isset( $params['site_url'] ) ? esc_url_raw( $params['site_url'] ) : '';

        // 1. 라이센스 검증
        $license_data = $this->verify_license( $license_key, $site_url );
        
        if ( is_wp_error( $license_data ) ) {
            return $license_data;
        }

        // 2. 에디션 확인
        $edition = $license_data['edition']; // free, basic, premium, etc.

        // 3. 최신 버전 정보 조회
        // 실제로는 DB나 파일 시스템에서 조회해야 함. 여기서는 하드코딩 예시.
        $latest_version = get_option( 'jj_neural_link_latest_version', '1.0.0' ); 
        
        if ( version_compare( $current_version, $latest_version, '<' ) ) {
            return array(
                'new_version' => $latest_version,
                'package'     => $this->get_package_url( $edition, $latest_version ),
                'slug'        => 'acf-css-really-simple-style-guide',
                'url'         => 'https://j-j-labs.com/plugin/acf-css',
            );
        }

        return array( 'message' => 'Latest version installed.' );
    }

    /**
     * 라이센스 검증 (내부 로직)
     */
    private function verify_license( $key, $site_url ) {
        // TODO: DB 조회 및 검증 로직 구현
        // JJ_License_Validator 클래스 활용
        
        // 임시: 모든 키를 Premium으로 간주
        return array(
            'status' => 'active',
            'edition' => 'premium',
            'expires' => '2099-12-31'
        );
    }

    /**
     * 패키지 다운로드 URL 생성
     */
    private function get_package_url( $edition, $version ) {
        // DB에 저장된 에디션별 파일 URL 반환
        $option_name = "jj_neural_link_file_{$edition}";
        $url = get_option( $option_name );
        
        // 파일이 없으면 빈 문자열 반환 (업데이트 불가)
        return $url ? $url : '';
    }
}


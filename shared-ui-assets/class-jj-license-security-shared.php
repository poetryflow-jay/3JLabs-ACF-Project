<?php
/**
 * [v25.0.0] 공유 라이센스 보안 (간소화 버전)
 * 
 * @package 3J_Labs_Shared
 * @version 25.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Security_Shared {

    private static $instances = array();
    private $plugin_slug = '';

    public static function instance( $plugin_slug ) {
        if ( ! isset( self::$instances[ $plugin_slug ] ) ) {
            self::$instances[ $plugin_slug ] = new self( $plugin_slug );
        }
        return self::$instances[ $plugin_slug ];
    }

    private function __construct( $plugin_slug ) {
        $this->plugin_slug = $plugin_slug;
    }

    /**
     * 라이센스 키 형식 검증
     */
    public function validate_license_format( $license_key ) {
        if ( empty( $license_key ) ) {
            return false;
        }

        $pattern = '/^[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}$/';
        return preg_match( $pattern, $license_key );
    }

    /**
     * 서버에서 라이센스 데이터 가져오기
     */
    public function get_license_data_from_server( $license_key ) {
        $cached = get_transient( 'jj_license_data_' . $this->plugin_slug );
        if ( $cached !== false ) {
            return $cached;
        }

        // 서버 검증 (간소화 버전)
        $license_server_url = get_option( 'jj_license_manager_server_url', 'https://license.3j-labs.com' );
        
        $response = wp_remote_post( $license_server_url . '/api/v1/verify', array(
            'timeout' => 10,
            'body' => json_encode( array(
                'license_key' => $license_key,
                'plugin_slug' => $this->plugin_slug,
                'site_url' => home_url(),
            ) ),
            'headers' => array( 'Content-Type' => 'application/json' ),
        ) );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( $data && isset( $data['success'] ) && $data['success'] ) {
            set_transient( 'jj_license_data_' . $this->plugin_slug, $data['data'] ?? array(), 3600 );
            return $data['data'] ?? array();
        }

        return false;
    }
}

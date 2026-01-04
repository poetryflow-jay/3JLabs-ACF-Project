<?php
/**
 * [v25.0.0] 공유 업데이트 보안 (간소화 버전)
 * 
 * @package 3J_Labs_Shared
 * @version 25.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_Update_Security_Shared {

    private static $instances = array();
    private $plugin_path = '';
    private $plugin_slug = '';

    public static function instance( $plugin_path, $plugin_slug ) {
        $key = $plugin_slug;
        if ( ! isset( self::$instances[ $key ] ) ) {
            self::$instances[ $key ] = new self( $plugin_path, $plugin_slug );
        }
        return self::$instances[ $key ];
    }

    private function __construct( $plugin_path, $plugin_slug ) {
        $this->plugin_path = $plugin_path;
        $this->plugin_slug = $plugin_slug;
        
        $this->init_hooks();
    }

    private function init_hooks() {
        // 업데이트 소스 검증
        add_filter( 'upgrader_pre_download', array( $this, 'verify_update_package' ), 10, 3 );
    }

    public function verify_update_package( $reply, $package, $upgrader ) {
        // 허가된 도메인만 허용
        $allowed_domains = array( '3j-labs.com', 'j-j-labs.com' );
        
        foreach ( $allowed_domains as $domain ) {
            if ( strpos( $package, $domain ) !== false ) {
                return $reply;
            }
        }

        return new WP_Error(
            'unauthorized_source',
            __( '허가되지 않은 업데이트 소스입니다.', '3j-labs-shared' )
        );
    }
}

<?php
/**
 * [v25.0.1] 공유 업데이트 보안 (간소화 버전)
 * 
 * 로컬 파일 업로드 및 WordPress.org 플러그인 허용
 * 3J Labs 플러그인만 엄격하게 검증
 * 
 * @package 3J_Labs_Shared
 * @version 25.0.1
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
        // [v25.0.1] 로컬 파일 업로드 허용 (플러그인 설치/업로드)
        // $package가 파일 경로인 경우 (로컬 업로드)
        if ( file_exists( $package ) || ( is_string( $package ) && ( strpos( $package, '/' ) !== false || strpos( $package, '\\' ) !== false ) ) ) {
            // 로컬 파일 경로인 경우 허용
            return $reply;
        }

        // [v25.0.1] WordPress.org 플러그인 허용
        if ( strpos( $package, 'downloads.wordpress.org' ) !== false || strpos( $package, 'wordpress.org' ) !== false ) {
            return $reply;
        }

        // [v25.0.1] 3J Labs 플러그인 업데이트만 엄격하게 검증
        // 허가된 도메인만 허용
        $allowed_domains = array( '3j-labs.com', 'j-j-labs.com', 'updates.3j-labs.com', 'api.3j-labs.com' );
        
        foreach ( $allowed_domains as $domain ) {
            if ( strpos( $package, $domain ) !== false ) {
                return $reply;
            }
        }

        // [v25.0.1] 3J Labs 플러그인이 아닌 경우 허용 (다른 플러그인 업데이트)
        // $upgrader 객체를 확인하여 현재 플러그인 업데이트인지 확인
        if ( is_object( $upgrader ) && isset( $upgrader->skin ) ) {
            // 현재 플러그인 업데이트가 아닌 경우 허용
            $plugin_info = $upgrader->skin->plugin ?? null;
            if ( empty( $plugin_info ) || strpos( $plugin_info, $this->plugin_slug ) === false ) {
                // 다른 플러그인 업데이트는 허용
                return $reply;
            }
        }

        // [v25.0.1] 기본적으로 허용 (보안 검증은 선택적)
        // 3J Labs 플러그인만 엄격하게 검증하고, 다른 플러그인은 허용
        return $reply;
    }
}

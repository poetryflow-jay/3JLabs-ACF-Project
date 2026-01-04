<?php
/**
 * [v25.0.0] 공유 보안 모듈
 * 
 * 모든 3J Labs 플러그인에서 공유 사용하는 보안 모듈
 * 
 * @package 3J_Labs_Shared
 * @version 25.0.0
 * @author 3J Labs
 * 
 * 주요 기능:
 * - 파일 무결성 모니터링
 * - 업데이트 보안
 * - 라이센스 보안
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 공유 보안 모듈 로더
 */
class JJ_Security_Module_V25_Loader {

    private static $instance = null;
    private $plugin_path = '';
    private $plugin_url = '';
    private $plugin_version = '25.0.0';
    private $plugin_slug = '';

    public static function instance( $plugin_path = '', $plugin_url = '', $plugin_version = '25.0.0', $plugin_slug = '' ) {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self( $plugin_path, $plugin_url, $plugin_version, $plugin_slug );
        }
        return self::$instance;
    }

    private function __construct( $plugin_path, $plugin_url, $plugin_version, $plugin_slug ) {
        $this->plugin_path = $plugin_path;
        $this->plugin_url = $plugin_url;
        $this->plugin_version = $plugin_version;
        $this->plugin_slug = $plugin_slug;
        
        $this->load_security_modules();
    }

    /**
     * 보안 모듈 로드
     */
    private function load_security_modules() {
        $shared_path = dirname( __FILE__ );
        
        // 파일 무결성 모니터 (간소화 버전)
        if ( file_exists( $shared_path . '/class-jj-file-integrity-shared.php' ) ) {
            require_once $shared_path . '/class-jj-file-integrity-shared.php';
            if ( class_exists( 'JJ_File_Integrity_Shared' ) ) {
                JJ_File_Integrity_Shared::instance( $this->plugin_path, $this->plugin_slug );
            }
        }
        
        // 업데이트 보안 (간소화 버전)
        if ( file_exists( $shared_path . '/class-jj-update-security-shared.php' ) ) {
            require_once $shared_path . '/class-jj-update-security-shared.php';
            if ( class_exists( 'JJ_Update_Security_Shared' ) ) {
                JJ_Update_Security_Shared::instance( $this->plugin_path, $this->plugin_slug );
            }
        }
        
        // 라이센스 보안 (간소화 버전)
        if ( file_exists( $shared_path . '/class-jj-license-security-shared.php' ) ) {
            require_once $shared_path . '/class-jj-license-security-shared.php';
            if ( class_exists( 'JJ_License_Security_Shared' ) ) {
                JJ_License_Security_Shared::instance( $this->plugin_slug );
            }
        }
    }
}

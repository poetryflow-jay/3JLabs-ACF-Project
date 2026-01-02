<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Visual Command Center (Module Loader)
 * 
 * 비주얼 커맨드 센터 모듈의 진입점입니다.
 * 하위 컴포넌트(Login Customizer, Admin Theme)를 로드하고 초기화합니다.
 * 
 * @since v5.6.0
 */
class JJ_Visual_Command_Center {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_components();
    }

    private function init_components() {
        // 1. 로그인 커스터마이저
        // 에디션 체크: Basic 이상
        if ( $this->check_capability( 'login_customizer' ) ) {
            require_once dirname( __FILE__ ) . '/class-jj-login-customizer.php';
            JJ_Login_Customizer::instance();
        }

        // 2. 어드민 테마
        // 에디션 체크: Premium 이상
        if ( $this->check_capability( 'admin_theme' ) ) {
            require_once dirname( __FILE__ ) . '/class-jj-admin-theme.php';
            JJ_Admin_Theme::instance();
        }
    }

    /**
     * 기능 사용 권한 확인
     */
    private function check_capability( $cap ) {
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            return JJ_Edition_Controller::instance()->has_capability( $cap );
        }
        return false; // 컨트롤러가 없으면 비활성화
    }
}


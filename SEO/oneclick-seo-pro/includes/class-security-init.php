<?php
/**
 * 1-Click SEO Pro 보안 모듈 초기화
 *
 * 공유 보안 모듈 (shared-ui-assets)을 로드하고 초기화합니다.
 *
 * @package OneClick_SEO_Pro
 * @version 2.0.0
 * @author 3J Labs
 */

if (!defined('ABSPATH')) {
    exit;
}

class OneClick_SEO_Pro_Security_Init {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * License manager instance
     */
    private $license_manager = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_shared_security_modules();
        $this->init_license_manager();
    }

    /**
     * 공유 보안 모듈 로드
     */
    private function load_shared_security_modules() {
        // shared-ui-assets 폴더 경로 (상위 3단계 - SEO/oneclick-seo-pro/includes)
        $shared_path = dirname(dirname(dirname(ONECLICK_SEO_PRO_DIR))) . '/shared-ui-assets';

        // 보안 모듈 로더가 있는지 확인
        if (file_exists($shared_path . '/class-jj-security-module-v25.php')) {
            require_once $shared_path . '/class-jj-security-module-v25.php';

            // 보안 모듈 초기화
            if (class_exists('JJ_Security_Module_V25_Loader')) {
                JJ_Security_Module_V25_Loader::instance(
                    ONECLICK_SEO_PRO_DIR,
                    ONECLICK_SEO_PRO_URL,
                    ONECLICK_SEO_PRO_VERSION,
                    'oneclick-seo-pro'
                );
            }
        }
    }

    /**
     * 라이센스 관리자 초기화
     */
    private function init_license_manager() {
        $shared_path = dirname(dirname(dirname(ONECLICK_SEO_PRO_DIR))) . '/shared-ui-assets';

        if (file_exists($shared_path . '/class-jj-license-manager-shared.php')) {
            require_once $shared_path . '/class-jj-license-manager-shared.php';

            if (class_exists('JJ_License_Manager_Shared')) {
                $this->license_manager = JJ_License_Manager_Shared::instance('oneclick-seo-pro');
            }
        }
    }

    /**
     * 라이센스 관리자 가져오기
     */
    public function get_license_manager() {
        return $this->license_manager;
    }

    /**
     * 라이센스가 유효한지 확인
     */
    public function is_license_valid() {
        if ($this->license_manager) {
            return $this->license_manager->is_valid();
        }
        return false;
    }

    /**
     * 라이센스 타입 가져오기
     */
    public function get_license_type() {
        if ($this->license_manager) {
            return $this->license_manager->get_license_type();
        }
        return 'FREE';
    }

    /**
     * Pro 기능이 활성화되어 있는지 확인
     */
    public function is_pro() {
        $license_type = $this->get_license_type();
        return in_array($license_type, ['PRO', 'PREMIUM', 'UNLIMITED', 'DEVELOPER'], true);
    }

    /**
     * Premium 기능이 활성화되어 있는지 확인
     */
    public function is_premium() {
        $license_type = $this->get_license_type();
        return in_array($license_type, ['PREMIUM', 'UNLIMITED', 'DEVELOPER'], true);
    }
}

/**
 * 보안 모듈 인스턴스 가져오기 헬퍼 함수
 */
function oneclick_seo_security() {
    return OneClick_SEO_Pro_Security_Init::get_instance();
}

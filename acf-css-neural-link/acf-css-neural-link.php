<?php
/**
 * Plugin Name:       ACF CSS Neural Link - License & Update Manager (Advanced Custom Fonts & Colors & Styles)
 * Plugin URI:        https://3j-labs.com
 * Description:       ACF CSS (Advanced Custom Fonts & Colors & Styles) 플러그인의 라이센스 인증, 자동 업데이트, 원격 제어를 담당하는 중앙 관리 시스템입니다. WooCommerce와 연동하여 라이센스를 자동으로 발행하고 관리합니다.
 * Version:           6.0.0
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com
 * Text Domain:       acf-css-neural-link
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * 개발 원칙 (필수 준수)
 * 
 * 이 플러그인의 모든 코드 수정 및 개발은 다음 원칙을 반드시 준수해야 합니다:
 * 
 * ⚠️ 중요: Git 경로와 로컬 경로는 완전히 별개입니다
 * - 로컬 작업 경로: C:\Users\computer\OneDrive\Desktop\클라우드 백업\OneDrive\문서\jj-css-premium
 * - Git worktree 경로: C:\Users\computer\.cursor\worktrees\jj-css-premium\tci
 * - 두 경로는 서로 독립적이며, Git에서 작업한 내용은 반드시 로컬 경로에 동기화해야 함
 * 
 * 1. WordPress 함수 호출 시점 안전성
 *    - 모든 WordPress 함수 호출 전 function_exists() 확인 필수
 *    - plugin_dir_path(), plugin_dir_url(), plugin_basename() 등
 *    - WordPress가 완전히 로드되기 전에도 안전하게 작동해야 함
 * 
 * 2. 클래스 속성에 상수 직접 할당 금지
 *    - PHP Parse Error 방지를 위해 생성자에서 할당
 * 
 * 3. WordPress 상수를 클래스 속성에서 직접 사용 금지
 *    - DAY_IN_SECONDS 등은 생성자에서 확인 후 사용
 * 
 * 4. 활성화 훅 안전성 확보
 *    - 모든 활성화 로직은 try-catch 블록으로 감싸야 함
 *    - 파일/클래스 존재 확인 필수
 * 
 * 자세한 내용은 루트 디렉토리의 DEVELOPMENT_PRINCIPLES.md 파일을 참조하세요.
 */

// 플러그인 상수 정의
// [v2.1.0] 원격 제어 기능 강화, 로그 수집 및 분석 기능 추가, 업데이트 배포 및 공지 기능 추가, 보안 강화
// [v2.1.1] 버전 관리 및 기술 문서 보강: 문서화 개선 및 버전 업데이트
// [v2.1.2] 플러그인 버전별 자동 업데이트 제어 기능 추가, dev 버전과의 호환성 개선
// [v2.1.3] 플러그인 폴더명 및 슬러그 업데이트
// [v2.1.4] Pro 버전 원격 활성화 시스템 지원
define( 'JJ_NEURAL_LINK_VERSION', '6.0.0' ); // [v6.0.0] 순차 배포 및 업데이트 채널 관리 시스템 안정화

// WordPress 함수가 로드되었는지 확인 후 상수 정의
if ( ! defined( 'JJ_NEURAL_LINK_PATH' ) ) {
    if ( function_exists( 'plugin_dir_path' ) ) {
        define( 'JJ_NEURAL_LINK_PATH', plugin_dir_path( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 직접 경로 계산
        define( 'JJ_NEURAL_LINK_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
    }
}

if ( ! defined( 'JJ_NEURAL_LINK_URL' ) ) {
    if ( function_exists( 'plugin_dir_url' ) ) {
        define( 'JJ_NEURAL_LINK_URL', plugin_dir_url( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 기본값 사용 (나중에 업데이트됨)
        define( 'JJ_NEURAL_LINK_URL', '' );
    }
}

if ( ! defined( 'JJ_LICENSE_MANAGER_BASENAME' ) ) {
    if ( function_exists( 'plugin_basename' ) ) {
        define( 'JJ_LICENSE_MANAGER_BASENAME', plugin_basename( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 기본값 사용
        define( 'JJ_LICENSE_MANAGER_BASENAME', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
    }
}

// 자동 로더 (WordPress가 로드된 후에만 실행)
if ( defined( 'JJ_NEURAL_LINK_PATH' ) && function_exists( 'class_exists' ) ) {
    $autoloader_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-autoloader.php';
    if ( file_exists( $autoloader_file ) ) {
        require_once $autoloader_file;
        if ( class_exists( 'JJ_License_Autoloader' ) ) {
            JJ_License_Autoloader::init();
        }
    }
}

/**
 * WooCommerce HPOS(High-Performance Order Storage) 호환성 선언
 */
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

// 플러그인 초기화
function jj_license_manager_init() {
    // WooCommerce 활성화 확인
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'jj_license_manager_woocommerce_notice' );
        return;
    }
    
    // 메인 클래스 존재 확인 후 인스턴스 생성
    if ( ! class_exists( 'JJ_License_Manager_Main' ) ) {
        // 클래스가 로드되지 않은 경우 직접 로드 시도
        $main_class_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-manager-main.php';
        if ( file_exists( $main_class_file ) ) {
            require_once $main_class_file;
        } else {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error"><p>';
                echo '<strong>' . esc_html__( 'JJ License Manager', 'jj-license-manager' ) . '</strong>: ';
                echo esc_html__( '필수 파일을 찾을 수 없습니다. 플러그인을 재설치해주세요.', 'jj-license-manager' );
                echo '</p></div>';
            } );
            return;
        }
    }
    
    // 메인 클래스 인스턴스 생성
    if ( class_exists( 'JJ_License_Manager_Main' ) ) {
        JJ_License_Manager_Main::instance();
    }
}
add_action( 'plugins_loaded', 'jj_license_manager_init', 20 );

/**
 * WooCommerce 미활성화 알림
 */
function jj_license_manager_woocommerce_notice() {
    ?>
    <div class="notice notice-error">
        <p>
            <strong><?php esc_html_e( 'JJ License Manager', 'jj-license-manager' ); ?></strong>: 
            <?php esc_html_e( '이 플러그인은 WooCommerce가 필요합니다. WooCommerce를 설치하고 활성화해주세요.', 'jj-license-manager' ); ?>
        </p>
    </div>
    <?php
}

/**
 * 플러그인 활성화 훅
 * [v2.0.1] 전체 함수를 try-catch로 감싸고 상수 정의 확인 추가
 */
register_activation_hook( __FILE__, 'jj_license_manager_activate' );
function jj_license_manager_activate() {
    try {
        // 상수 정의 확인
        if ( ! defined( 'JJ_NEURAL_LINK_PATH' ) ) {
            // 상수가 정의되지 않은 경우 기본 경로 사용
            if ( function_exists( 'plugin_dir_path' ) ) {
                define( 'JJ_NEURAL_LINK_PATH', plugin_dir_path( __FILE__ ) );
            } else {
                define( 'JJ_NEURAL_LINK_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
            }
        }
        
        // 데이터베이스 테이블 생성
        $db_file = JJ_NEURAL_LINK_PATH . 'includes/class-jj-license-database.php';
        if ( file_exists( $db_file ) ) {
            require_once $db_file;
            if ( class_exists( 'JJ_License_Database' ) ) {
                JJ_License_Database::create_tables();
            }
        }
        
        // 기본 옵션 설정
        if ( function_exists( 'add_option' ) ) {
            add_option( 'jj_license_manager_version', defined( 'JJ_NEURAL_LINK_VERSION' ) ? JJ_NEURAL_LINK_VERSION : '3.9.9' );
            add_option( 'jj_license_manager_activation_time', time() );
        }
        
        // Cron 작업 스케줄링
        if ( function_exists( 'wp_next_scheduled' ) && function_exists( 'wp_schedule_event' ) ) {
            if ( ! wp_next_scheduled( 'jj_license_manager_daily_check' ) ) {
                wp_schedule_event( time(), 'daily', 'jj_license_manager_daily_check' );
            }
        }
        
        // dev 버전과의 호환성: dev 버전이 활성화되어 있어도 문제없이 작동하도록
        // WooCommerce 의존성은 plugins_loaded에서 체크하므로 활성화 시에는 체크하지 않음
    } catch ( Exception $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'JJ License Manager: 활성화 중 오류 - ' . $e->getMessage() );
        }
    } catch ( Error $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'JJ License Manager: 활성화 중 치명적 오류 - ' . $e->getMessage() );
        }
    }
}

/**
 * 플러그인 비활성화 훅
 */
register_deactivation_hook( __FILE__, 'jj_license_manager_deactivate' );
function jj_license_manager_deactivate() {
    // Cron 작업 제거
    wp_clear_scheduled_hook( 'jj_license_manager_daily_check' );
}



// Server API 로드
require_once JJ_NEURAL_LINK_PATH . 'includes/api/class-jj-neural-link-server-api.php';
add_action( 'plugins_loaded', array( 'JJ_Neural_Link_Server_API', 'instance' ) );


// Version Manager 로드 (관리자용)
if ( is_admin() ) {
    require_once JJ_NEURAL_LINK_PATH . 'includes/admin/class-jj-neural-link-version-manager.php';
    add_action( 'plugins_loaded', array( 'JJ_Neural_Link_Version_Manager', 'instance' ) );
}


// Cloud API 로드
require_once JJ_NEURAL_LINK_PATH . 'includes/api/class-jj-neural-link-cloud-api.php';
add_action( 'plugins_loaded', array( 'JJ_Neural_Link_Cloud_API', 'instance' ) );


// [v3.2.0] License REST API 로드 (WooCommerce 연동용)
if ( file_exists( JJ_NEURAL_LINK_PATH . 'includes/class-jj-neural-link-woo-api.php' ) ) {
    require_once JJ_NEURAL_LINK_PATH . 'includes/class-jj-neural-link-woo-api.php';
    // init 훅에서 자동으로 instance 호출됨 (class 파일 내 add_action 확인)
}

// [v3.3.0] Launcher API 로드 (3J Labs Launcher 연동용)
if ( file_exists( JJ_NEURAL_LINK_PATH . 'includes/api/class-jj-neural-link-launcher-api.php' ) ) {
    require_once JJ_NEURAL_LINK_PATH . 'includes/api/class-jj-neural-link-launcher-api.php';
}

// [v4.2.0] JJ Plugin Updater - WordPress 업데이트 시스템 통합
if ( file_exists( JJ_NEURAL_LINK_PATH . 'includes/class-jj-plugin-updater.php' ) ) {
    require_once JJ_NEURAL_LINK_PATH . 'includes/class-jj-plugin-updater.php';
    
    add_action( 'admin_init', function() {
        // Neural Link 자체 업데이트
        if ( class_exists( 'JJ_Plugin_Updater' ) ) {
            $license_key = get_option( 'jj_license_manager_license_key', '' );
            $api_url = get_option( 'jj_license_manager_server_url', 'https://3j-labs.com' );
            
            new JJ_Plugin_Updater(
                $api_url,
                __FILE__,
                array(
                    'version'   => JJ_NEURAL_LINK_VERSION,
                    'license'   => $license_key,
                    'item_name' => 'ACF CSS Neural Link',
                    'author'    => '3J Labs',
                    'beta'      => false,
                )
            );
        }
    } );
}

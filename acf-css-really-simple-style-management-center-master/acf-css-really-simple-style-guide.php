<?php
/**
 * Plugin Name:       ACF CSS - Advanced Custom Fonts & Colors & Styles Setting Manager (Master)
 * Plugin URI:        https://3j-labs.com
 * Description:       ACF CSS (Advanced Custom Fonts & Colors & Styles) - WordPress 웹사이트의 모든 스타일 요소(색상 팔레트, 타이포그래피, 버튼, 폼)를 중앙에서 일관되게 관리하는 통합 스타일 관리 플러그인입니다. Free 버전은 기본적인 스타일 관리 기능을 제공하며, 브랜드 일관성을 유지하고 디자인 시스템을 효율적으로 운영할 수 있습니다. Pro 버전 플러그인을 함께 설치하면 Basic, Premium, Unlimited 기능을 사용할 수 있습니다. WordPress Customizer와 완벽 통합되어 실시간 미리보기와 함께 직관적인 스타일 관리가 가능합니다.
 * Version:           20.1.0
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com
 * Text Domain:       acf-css-really-simple-style-management-center
 * Domain Path:       /languages
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ⚠️ 중요: Git 경로와 로컬 경로는 완전히 별개입니다
 * 
 * - 로컬 작업 경로: C:\Users\computer\Desktop\3J-Labs-Projects\3J-ACF-CSS
 * - Git worktree 경로: C:\Users\computer\.cursor\worktrees\jj-css-premium\tci
 * - 두 경로는 서로 독립적이며, Git에서 작업한 내용은 반드시 로컬 경로에 동기화해야 함
 * - 자세한 내용은 DEVELOPMENT_PRINCIPLES.md의 "Git 및 로컬 경로 관리 원칙" 섹션 참조
 */

/**
 * ==============================================================================
 * 작업 원칙 (Development Principles) - v13.4.7
 * ==============================================================================
 * 
 * 1. 터미널 Python REPL 상태 감지:
 *    - 프롬프트가 ">>>"로 표시되면 exit() 후 재시도
 *    - 모든 명령이 Python 코드로 해석되어 SyntaxError 발생 가능
 * 
 * 2. 타임아웃 및 재시도:
 *    - 40초 이상 응답 없거나 유의미한 진행 없으면 중지 후 다른 방법으로 재시도
 *    - 복잡한 PowerShell 명령은 .ps1 스크립트 파일로 분리
 * 
 * 3. ZIP 빌드 주의사항:
 *    - WordPress 플러그인 ZIP은 플러그인 폴더가 포함되어야 함
 *    - Compress-Archive -Path $folder (not $folder\*)
 *    - 이렇게 해야 WordPress 업로드 설치 시 올바르게 인식됨
 * 
 * 4. 문법/참조 오류 방지:
 *    - 모든 PHP 클래스/함수 호출 전 class_exists(), function_exists(), method_exists() 검증
 *    - private → public 메서드 변경 시 모든 호출 지점 확인
 *    - static 클래스에는 instance() 호출 금지
 * 
 * 5. 파일명/경로 검증:
 *    - 파일 수정 전 file_exists() 확인
 *    - 플러그인별 메인 파일명 정확히 사용
 *    - 상대 경로 대신 JJ_STYLE_GUIDE_PATH, plugin_dir_path() 상수 활용
 * 
 * ==============================================================================
 */

// 플러그인 상수 정의
// [v5.1.6] 전면 재검토 및 오류 방지: 안전한 파일 로더 추가, 모든 버전의 require_once 안전 처리, 결제 유도 문구 추가, 플러그인 목록 페이지 바로가기 링크 추가
// [v5.1.6] Comprehensive review and error prevention: Safe file loader added, all versions' require_once safely handled, purchase prompts added, plugin list page quick links added
// [v1.0.2] 모든 버전 플러그인 활성화 안전성 최종 확보, WordPress 함수 호출 안전 처리
if ( ! defined( 'JJ_STYLE_GUIDE_VERSION' ) ) {
    define( 'JJ_STYLE_GUIDE_VERSION', '20.1.0' ); // [v20.1.0] Phase 22: 업데이트 채널 관리자, 베타 테스트 동의 UI, 순차 배포, 보안 강화
}

// WordPress 함수가 로드되었는지 확인 후 상수 정의
if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    if ( function_exists( 'plugin_dir_path' ) ) {
        define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 직접 경로 계산
        define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
    }
}

if ( ! defined( 'JJ_STYLE_GUIDE_URL' ) ) {
    if ( function_exists( 'plugin_dir_url' ) ) {
        define( 'JJ_STYLE_GUIDE_URL', plugin_dir_url( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 기본값 사용 (나중에 업데이트됨)
        define( 'JJ_STYLE_GUIDE_URL', '' );
    }
}
if ( ! defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) define( 'JJ_STYLE_GUIDE_OPTIONS_KEY', 'jj_style_guide_options' );
if ( ! defined( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY' ) ) define( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY', 'jj_style_guide_temp_options' );
if ( ! defined( 'JJ_STYLE_GUIDE_ADMIN_TEXT_KEY' ) ) define( 'JJ_STYLE_GUIDE_ADMIN_TEXT_KEY', 'jj_style_guide_admin_texts' );
if ( ! defined( 'JJ_STYLE_GUIDE_PAGE_SLUG' ) ) define( 'JJ_STYLE_GUIDE_PAGE_SLUG', 'acf-css-really-simple-style-guide' );
if ( ! defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) ) define( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY', 'jj_style_guide_cockpit_page_id' );
if ( ! defined( 'JJ_STYLE_GUIDE_DEBUG' ) ) define( 'JJ_STYLE_GUIDE_DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
// Pro 버전 활성화 코드 확인 (plugins_loaded 훅에서 처리)
// WordPress가 로드되기 전에는 기본값 사용
// 기본값(빌드 스크립트에서 주입/대체될 수 있음). 이미 정의돼 있으면 건드리지 않습니다.
if ( ! defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', 'MASTER' );
if ( ! defined( 'JJ_STYLE_GUIDE_EDITION' ) ) define( 'JJ_STYLE_GUIDE_EDITION', 'master' );

// [v3.8.0] 환경 구분
if ( ! defined( 'JJ_STYLE_GUIDE_ENV' ) ) {
    define( 'JJ_STYLE_GUIDE_ENV', 'live' );
}

// [v5.3.7] Free 버전은 독립적으로 작동합니다. Master 버전 의존성 제거
// Free 버전은 자체 includes 폴더에 필요한 모든 파일을 포함합니다.

// [v5.4.1] 안전한 파일 로더 적용
$safe_loader = null;
try {
    $safe_loader_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-loader.php';
    if ( file_exists( $safe_loader_path ) ) {
        require_once $safe_loader_path;
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'instance' ) ) {
            $safe_loader = JJ_Safe_Loader::instance();
        }
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Failed: ' . $e->getMessage() );
} catch ( Error $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Fatal Error: ' . $e->getMessage() );
}

/**
 * [B: 안정성 방어막] Safe Loader 폴백
 * - class-jj-safe-loader.php 자체가 누락/손상돼도, 플러그인이 "부팅"은 되도록 합니다.
 * - (원인 파악/자가진단/업데이트 탭 접근) 경로를 확보하는 것이 목표입니다.
 */
$GLOBALS['jj_style_guide_boot_diag'] = isset( $GLOBALS['jj_style_guide_boot_diag'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag'] )
    ? $GLOBALS['jj_style_guide_boot_diag']
    : array(
        'missing_required' => array(),
        'missing_optional' => array(),
        'boot_level'       => 'minimal', // 기본값
    );

// 안전한 require_once 함수 정의 (전역 범위)
if ( ! function_exists( 'jj_safe_require' ) ) {
    /**
     * 파일을 안전하게 로드하고, 실패 시 진단 정보에 기록합니다.
     *
     * @param string $path 파일 경로
     * @param bool $required 필수 여부 (true면 실패 시 치명적 오류로 기록될 수 있음)
     * @return bool 성공 여부
     */
    function jj_safe_require( $path, $required = true ) {
        if ( file_exists( $path ) ) {
            try {
                require_once $path;
                return true;
            } catch ( Exception $e ) {
                error_log( "[JJ_CSS_BOOT_ERROR] Exception loading $path: " . $e->getMessage() );
                return false;
            } catch ( Error $e ) {
                error_log( "[JJ_CSS_BOOT_ERROR] Fatal Error loading $path: " . $e->getMessage() );
                return false;
            }
        } else {
            // Safe Loader가 있으면 거기에 기록, 없으면 전역 배열에 기록
            if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'log_missing_file' ) ) {
                JJ_Safe_Loader::log_missing_file( $path, $required );
            } else {
                $key = $required ? 'missing_required' : 'missing_optional';
                $GLOBALS['jj_style_guide_boot_diag'][ $key ][] = array(
                    'path' => $path,
                    'required' => $required
                );
            }
            return false;
        }
    }
}

// 편의 변수
$jj_safe_require = 'jj_safe_require';

// 1. 헬퍼/유틸리티 로드 (가장 먼저)
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-common-utils.php', true );

// 2. 코어 클래스 로드
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-simple-style-guide.php', true );
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-admin-center.php', true ); // Admin Center (설정 관리자)
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-style-guide-generator.php', true ); // 스타일 생성기 (CSS 변수)
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-style-guide-frontend.php', true ); // 프론트엔드 (CSS 적용)

// 3. 에디션 컨트롤러 (권한 관리)
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-edition-controller.php', true );

// 4. 확장 모듈 로드 (순차적)
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php', true ); // 커스터마이저
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-palette-manager.php', true ); // 팔레트 관리자
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-typography-manager.php', true ); // 타이포그래피 관리자
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-advanced-styling.php', true ); // 고급 스타일링 (버튼, 폼 등)
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-font-manager.php', true ); // 폰트 관리자
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-asset-optimizer.php', true ); // 에셋 최적화
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php', true ); // 업데이트 관리자
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php', true ); // 라이센스 관리자
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-block-editor-integration.php', true ); // 블록 에디터 통합
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-elementor-integration.php', false ); // 엘리멘터 통합 (선택)

// [v13.4.4] Master Integrator 로드 (모든 패밀리 플러그인 기능 통합)
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-master-integrator.php', true );

// [Phase 10.6] Style Guide Live Page (프론트엔드 스타일 가이드 허브)
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-style-guide-live-page.php', true );
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-style-guide-page-duplicator.php', false );

// [Phase 20] 파일 무결성 모니터 (FTP 코드 탈취 방지)
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-file-integrity-monitor.php', true );

// [Phase 20] 보안 강화 모듈 (라이센스 암호화, 업데이트 서버 보안)
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-security-enhancer.php', true );

// [Phase 22] 업데이트 채널 관리자 (베타 테스트 동의, 순차 배포, 채널 선택)
$jj_safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-update-channel-manager.php', true );

// 메인 클래스 초기화
if ( class_exists( 'JJ_Simple_Style_Guide' ) ) {
    try {
        $jj_style_guide = new JJ_Simple_Style_Guide();
    } catch ( Exception $e ) {
        if ( function_exists( 'error_log' ) ) error_log( 'JJ Style Guide Init Failed: ' . $e->getMessage() );
    } catch ( Error $e ) {
        if ( function_exists( 'error_log' ) ) error_log( 'JJ Style Guide Init Fatal Error: ' . $e->getMessage() );
    }
}

// [v5.3.9] 코드 무결성 모니터 초기화 (plugins_loaded 시점)

// [Phase 4.5/4.99] Admin Center 초기화 (메뉴/상단바/관리자 UI)
if ( class_exists( 'JJ_Admin_Center' ) ) {
    try {
        $admin_center = JJ_Admin_Center::instance();
        if ( method_exists( $admin_center, 'init' ) ) {
            $admin_center->init();
        }
    } catch ( Exception $e ) {
        if ( function_exists( 'error_log' ) ) error_log( 'JJ Admin Center Init Failed: ' . $e->getMessage() );
    } catch ( Error $e ) {
        if ( function_exists( 'error_log' ) ) error_log( 'JJ Admin Center Init Fatal Error: ' . $e->getMessage() );
    }
}

// [Phase 10.6] Style Guide Live Page 초기화 (프론트엔드 스타일 가이드 허브)
if ( class_exists( 'JJ_Style_Guide_Live_Page' ) ) {
    try {
        $live_page = JJ_Style_Guide_Live_Page::instance();
        if ( method_exists( $live_page, 'init' ) ) {
            $live_page->init();
        }
    } catch ( Exception $e ) {
        if ( function_exists( 'error_log' ) ) error_log( 'JJ Style Guide Live Page Init Failed: ' . $e->getMessage() );
    } catch ( Error $e ) {
        if ( function_exists( 'error_log' ) ) error_log( 'JJ Style Guide Live Page Init Fatal Error: ' . $e->getMessage() );
    }
}

// 템플릿 태그 (외부 사용용)
/**
 * 저장된 옵션값 가져오기 (헬퍼)
 */
if ( ! function_exists( 'jj_style_guide_get_option' ) ) {
    function jj_style_guide_get_option( $key = '', $default = null ) {
        if ( class_exists( 'JJ_Simple_Style_Guide' ) ) {
            // JJ_Simple_Style_Guide 인스턴스에 접근할 방법이 없으므로,
            // get_option을 직접 사용하거나 전역 인스턴스를 활용해야 함.
            // 여기서는 get_option을 직접 사용하는 방식으로 구현
            $options = get_option( 'jj_style_guide_options', array() );
            if ( $key ) {
                return isset( $options[ $key ] ) ? $options[ $key ] : $default;
            }
            return $options;
        }
        return $default;
    }
}

/**
 * 텍스트 값 가져오기 (Admin Center 설정)
 */
if ( ! function_exists( 'jj_style_guide_text' ) ) {
    function jj_style_guide_text( $key, $fallback = '' ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_text( $key, $fallback );
        }
        return $fallback;
    }
}

if ( ! function_exists( 'jj_style_guide_sections_layout' ) ) {
    function jj_style_guide_sections_layout() {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_sections_layout();
        }
        return array();
    }
}

if ( ! function_exists( 'jj_style_guide_section_index' ) ) {
    function jj_style_guide_section_index( $slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_section_index( $slug );
        }
        return null;
    }
}

if ( ! function_exists( 'jj_style_guide_is_tab_enabled' ) ) {
    function jj_style_guide_is_tab_enabled( $section_slug, $tab_slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->is_tab_enabled( $section_slug, $tab_slug );
        }
        return true;
    }
}

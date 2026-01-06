<?php
/**
 * Plugin Name:       ACF CSS AI Extension - Intelligent Style Generator (Advanced Custom Fonts & Colors & Styles)
 * Plugin URI:        https://3j-labs.com/
 * Description:       ACF CSS (Advanced Custom Fonts & Colors & Styles) Manager의 강력한 확장 플러그인입니다. AI를 활용하여 웹사이트의 스타일을 자동으로 제안하고 생성하며, 로컬 AI 모델(Gemma 3)과의 연동을 지원합니다.
 * Version:           3.4.0
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com/
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Text Domain:       acf-css-ai-extension
 * Domain Path:       /languages
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'JJ_ACF_CSS_AI_EXT_VERSION', '3.4.0' ); // [v3.4.0] Phase 49-1: AI 컬러 팔레트 추천 기능 추가
define( 'JJ_ACF_CSS_AI_EXT_PATH', plugin_dir_path( __FILE__ ) );
define( 'JJ_ACF_CSS_AI_EXT_URL', plugin_dir_url( __FILE__ ) );
define( 'JJ_ACF_CSS_AI_EXT_SLUG', 'acf-css-ai-extension' );

// [v25.0.0] 보안 모듈 및 라이센스 관리 로드
$shared_path = dirname( dirname( __FILE__ ) ) . '/shared-ui-assets';
if ( file_exists( $shared_path . '/class-jj-security-module-v25.php' ) ) {
    require_once $shared_path . '/class-jj-security-module-v25.php';
    if ( class_exists( 'JJ_Security_Module_V25_Loader' ) ) {
        JJ_Security_Module_V25_Loader::instance( JJ_ACF_CSS_AI_EXT_PATH, JJ_ACF_CSS_AI_EXT_URL, JJ_ACF_CSS_AI_EXT_VERSION, JJ_ACF_CSS_AI_EXT_SLUG );
    }
}
if ( file_exists( $shared_path . '/class-jj-license-manager-shared.php' ) ) {
    require_once $shared_path . '/class-jj-license-manager-shared.php';
    if ( class_exists( 'JJ_License_Manager_Shared' ) ) {
        JJ_License_Manager_Shared::instance( JJ_ACF_CSS_AI_EXT_SLUG );
    }
}

require_once JJ_ACF_CSS_AI_EXT_PATH . 'includes/class-jj-acf-css-ai-extension.php';

// [Phase 49-1] AI Color Recommender
require_once JJ_ACF_CSS_AI_EXT_PATH . 'includes/class-jj-ai-color-recommender.php';

// 확장 매니저(Phase 5.3)로 등록
add_filter( 'jj_style_guide_extensions', function ( $items ) {
    if ( ! is_array( $items ) ) {
        $items = array();
    }
    $items[] = function () {
        return JJ_ACF_CSS_AI_Extension::instance();
    };
    return $items;
} );

// 코어가 없어도 “관리자 페이지에서 안내”는 가능하게 하기 위해 직접 로드도 시도
add_action( 'plugins_loaded', function () {
    if ( class_exists( 'JJ_ACF_CSS_AI_Extension' ) ) {
        JJ_ACF_CSS_AI_Extension::instance();
    }
}, 30 );

// 플러그인 목록 페이지 향상 초기화
add_action( 'plugins_loaded', function () {
    $plugin_list_enhancer_file = dirname( dirname( __FILE__ ) ) . '/acf-css-really-simple-style-management-center-master/includes/class-jj-plugin-list-enhancer.php';
    if ( file_exists( $plugin_list_enhancer_file ) ) {
        require_once $plugin_list_enhancer_file;
    }
    
    if ( class_exists( 'JJ_Plugin_List_Enhancer' ) ) {
        $enhancer = new JJ_Plugin_List_Enhancer();
        $enhancer->init( array(
            'plugin_file' => __FILE__,
            'plugin_name' => 'ACF CSS AI Extension',
            'settings_url' => admin_url( 'admin.php?page=jj-ai-extension' ),
            'text_domain' => 'acf-css-ai-extension',
            'version_constant' => 'JJ_ACF_CSS_AI_EXT_VERSION',
            'license_constant' => 'JJ_ACF_CSS_AI_EXT_LICENSE_TYPE',
            'upgrade_url' => 'https://3j-labs.com/',
            'docs_url' => admin_url( 'admin.php?page=jj-ai-extension' ),
            'support_url' => 'https://3j-labs.com/support',
        ) );
    }
}, 25 );



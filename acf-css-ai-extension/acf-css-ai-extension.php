<?php
/**
 * Plugin Name:       ACF CSS AI Extension - Intelligent Style Generator (Advanced Custom Fonts & Colors & Styles)
 * Plugin URI:        https://3j-labs.com
 * Description:       ACF CSS (Advanced Custom Fonts & Colors & Styles) Manager의 강력한 확장 플러그인입니다. AI를 활용하여 웹사이트의 스타일을 자동으로 제안하고 생성하며, 로컬 AI 모델(Gemma 3)과의 연동을 지원합니다.
 * Version:           2.1.0
 * Author:            3J Labs (제이x제니x제이슨 연구소)
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://3j-labs.com
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

define( 'JJ_ACF_CSS_AI_EXT_VERSION', '2.1.0' );
define( 'JJ_ACF_CSS_AI_EXT_PATH', plugin_dir_path( __FILE__ ) );
define( 'JJ_ACF_CSS_AI_EXT_URL', plugin_dir_url( __FILE__ ) );

require_once JJ_ACF_CSS_AI_EXT_PATH . 'includes/class-jj-acf-css-ai-extension.php';

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



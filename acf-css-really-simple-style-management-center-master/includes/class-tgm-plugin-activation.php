<?php
/**
 * TGMPA Plugin Activation Library
 * 
 * This file contains the TGMPA library for registering required and recommended plugins.
 * Based on TGMPA v2.6.1
 * 
 * @package ACF_CSS
 * @version 23.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// TGMPA 라이브러리가 이미 로드되었는지 확인
if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
    // TGMPA 라이브러리 파일 경로
    $tgmpa_path = JJ_STYLE_GUIDE_PATH . 'includes/vendor/class-tgm-plugin-activation.php';
    
    if ( file_exists( $tgmpa_path ) ) {
        require_once $tgmpa_path;
    } else {
        // TGMPA 라이브러리가 없으면 경고만 출력하고 계속 진행
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'TGMPA library not found at: ' . $tgmpa_path );
        }
    }
}

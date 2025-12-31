<?php
/**
 * 자동 로더 클래스
 * 
 * @package JJ_License_includes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JJ_License_Autoloader {
    
    /**
     * 자동 로더 초기화
     */
    public static function init() {
        spl_autoload_register( array( __CLASS__, 'autoload' ) );
    }
    
    /**
     * 클래스 자동 로드
     * 
     * @param string $class_name 클래스 이름
     */
    public static function autoload( $class_name ) {
        // JJ_License_로 시작하는 클래스만 로드
        if ( strpos( $class_name, 'JJ_License_' ) !== 0 ) {
            return;
        }
        
        // 클래스 이름을 파일 경로로 변환
        // JJ_License_Manager_Main -> jj-license-manager-main
        // JJ_License_Admin -> jj-license-admin
        $file_name = str_replace( 'JJ_License_', 'jj-license-', $class_name );
        $file_name = str_replace( '_', '-', $file_name );
        $file_name = 'class-' . strtolower( $file_name ) . '.php';
        
        // 파일 경로 배열
        $paths = array(
            JJ_NEURAL_LINK_PATH . 'includes/' . $file_name,
            JJ_NEURAL_LINK_PATH . 'includes/admin/' . $file_name,
            JJ_NEURAL_LINK_PATH . 'includes/api/' . $file_name,
            JJ_NEURAL_LINK_PATH . 'includes/integrations/' . $file_name,
        );
        
        // 파일 찾기 및 로드
        foreach ( $paths as $path ) {
            if ( file_exists( $path ) ) {
                require_once $path;
                return;
            }
        }
        
        // 디버그 모드에서 로그 기록
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( sprintf( 
                'JJ License Manager Autoloader: 클래스를 찾을 수 없습니다. 클래스: %s, 찾은 경로: %s', 
                $class_name, 
                implode( ', ', $paths ) 
            ) );
        }
    }
}


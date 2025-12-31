<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * JJ Self Tester
 * 
 * 플러그인 자가 진단 및 테스트 실행기
 * 
 * @since v6.2.0
 */
class JJ_Self_Tester {

    public static function run_tests() {
        $results = array();

        // 1. Core Classes Check
        $core_classes = array(
            'JJ_Admin_Center',
            'JJ_Safe_Loader',
            'JJ_Edition_Controller',
            'JJ_CSS_Cache',
            'JJ_Options_Cache',
            'JJ_CSS_Injector',
            'JJ_Smart_Palette', // AI
            'JJ_Cloud_Manager', // Cloud
            'JJ_Visual_Command_Center', // Visual
            'JJ_Partner_Hub', // Partner (Optional)
        );

        foreach ( $core_classes as $class ) {
            if ( class_exists( $class ) ) {
                $results[] = array( 'test' => "Class: $class", 'status' => 'pass', 'message' => 'Loaded successfully' );
            } else {
                // Partner Hub is optional depending on edition
                if ( $class === 'JJ_Partner_Hub' && JJ_Edition_Controller::instance()->get_license_type() !== 'PARTNER' && JJ_Edition_Controller::instance()->get_license_type() !== 'MASTER' ) {
                    $results[] = array( 'test' => "Class: $class", 'status' => 'skip', 'message' => 'Skipped (Edition mismatch)' );
                } else {
                    $results[] = array( 'test' => "Class: $class", 'status' => 'fail', 'message' => 'Class not found' );
                }
            }
        }

        // 2. Directory Permissions
        $dirs = array(
            JJ_STYLE_GUIDE_PATH . 'assets/css/generated' => 'is_writable',
        );

        foreach ( $dirs as $path => $check ) {
            if ( file_exists( $path ) ) {
                if ( is_writable( $path ) ) {
                    $results[] = array( 'test' => "Dir: " . basename( $path ), 'status' => 'pass', 'message' => 'Writable' );
                } else {
                    $results[] = array( 'test' => "Dir: " . basename( $path ), 'status' => 'warn', 'message' => 'Not writable' );
                }
            } else {
                $results[] = array( 'test' => "Dir: " . basename( $path ), 'status' => 'warn', 'message' => 'Not found (Auto-created on save)' );
            }
        }

        // 3. Option Integrity
        $options = get_option( 'jj_style_guide_options' );
        if ( is_array( $options ) ) {
            $results[] = array( 'test' => "Options Integrity", 'status' => 'pass', 'message' => 'Valid Array' );
        } else {
            $results[] = array( 'test' => "Options Integrity", 'status' => 'fail', 'message' => 'Corrupted or Missing' );
        }

        return $results;
    }
}


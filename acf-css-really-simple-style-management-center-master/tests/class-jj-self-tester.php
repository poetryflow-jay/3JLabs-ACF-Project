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

        // 0. Constants / Helpers
        $consts = array(
            'JJ_STYLE_GUIDE_VERSION',
            'JJ_STYLE_GUIDE_PATH',
            'JJ_STYLE_GUIDE_URL',
            'JJ_STYLE_GUIDE_OPTIONS_KEY',
        );
        foreach ( $consts as $c ) {
            $results[] = array(
                'test'    => "Constant: {$c}",
                'status'  => defined( $c ) ? 'pass' : 'fail',
                'message' => defined( $c ) ? 'Defined' : 'Not defined',
            );
        }

        $results[] = array(
            'test'    => 'Helper: jj_style_guide_text()',
            'status'  => function_exists( 'jj_style_guide_text' ) ? 'pass' : 'warn',
            'message' => function_exists( 'jj_style_guide_text' ) ? 'Available' : 'Missing (fallback recommended)',
        );

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
                if ( $class === 'JJ_Partner_Hub' ) {
                    if ( class_exists( 'JJ_Edition_Controller' ) && method_exists( 'JJ_Edition_Controller', 'instance' ) ) {
                        $license_type = JJ_Edition_Controller::instance()->get_license_type();
                        if ( $license_type !== 'PARTNER' && $license_type !== 'MASTER' ) {
                            $results[] = array( 'test' => "Class: $class", 'status' => 'skip', 'message' => 'Skipped (Edition mismatch)' );
                        } else {
                            $results[] = array( 'test' => "Class: $class", 'status' => 'fail', 'message' => 'Class not found' );
                        }
                    } else {
                        $results[] = array( 'test' => "Class: $class", 'status' => 'warn', 'message' => 'Edition controller missing (cannot decide optionality)' );
                    }
                } else {
                    $results[] = array( 'test' => "Class: $class", 'status' => 'fail', 'message' => 'Class not found' );
                }
            }
        }

        // 1.5 Safe Loader Diagnostics (missing/failed requires)
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'get_missing_files' ) ) {
            $missing = (array) JJ_Safe_Loader::get_missing_files();
            $req = array();
            $opt = array();
            foreach ( $missing as $m ) {
                if ( ! is_array( $m ) ) continue;
                if ( ! empty( $m['required'] ) ) {
                    $req[] = $m;
                } else {
                    $opt[] = $m;
                }
            }

            $results[] = array(
                'test'    => 'Safe Loader: Required files',
                'status'  => empty( $req ) ? 'pass' : 'fail',
                'message' => empty( $req )
                    ? 'No missing required files'
                    : ( 'Missing required files: ' . count( $req ) ),
            );

            $results[] = array(
                'test'    => 'Safe Loader: Optional files',
                'status'  => empty( $opt ) ? 'pass' : 'warn',
                'message' => empty( $opt )
                    ? 'No missing optional files'
                    : ( 'Missing optional files: ' . count( $opt ) ),
            );
        } else {
            $results[] = array(
                'test'    => 'Safe Loader: Diagnostics',
                'status'  => 'warn',
                'message' => 'Safe loader diagnostics not available',
            );
        }

        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'get_load_errors' ) ) {
            $errs = (array) JJ_Safe_Loader::get_load_errors();
            $results[] = array(
                'test'    => 'Safe Loader: Load errors',
                'status'  => empty( $errs ) ? 'pass' : 'fail',
                'message' => empty( $errs ) ? 'No load errors' : ( 'Load errors detected: ' . count( $errs ) ),
            );
        }

        // 1.6 File Manifest (critical templates/views)
        $manifest = array(
            // Admin Center tabs
            'includes/admin/views/tabs/tab-updates.php'       => true,
            'includes/admin/views/tabs/tab-system-status.php' => true,
            // Editor views
            'includes/editor-views/view-section-colors.php'   => true,
            // Presets assets
            'assets/js/jj-style-guide-presets.js'             => true,
            'assets/css/jj-style-guide-presets.css'           => true,
        );
        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            foreach ( $manifest as $rel => $required ) {
                $path = JJ_STYLE_GUIDE_PATH . ltrim( $rel, '/\\' );
                $exists = file_exists( $path );
                $results[] = array(
                    'test'    => 'File: ' . $rel,
                    'status'  => $exists ? 'pass' : ( $required ? 'fail' : 'warn' ),
                    'message' => $exists ? 'OK' : 'Missing',
                );
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


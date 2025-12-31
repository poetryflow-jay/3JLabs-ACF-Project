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

            // required 누락은 "원인 파일"을 바로 볼 수 있도록 개별 항목으로 출력(최대 30개)
            if ( ! empty( $req ) ) {
                $max = 30;
                $n = 0;
                foreach ( $req as $r ) {
                    if ( $n >= $max ) {
                        $results[] = array(
                            'test'    => 'Safe Loader: Required files (more)',
                            'status'  => 'fail',
                            'message' => 'Too many missing required files. (truncated)',
                        );
                        break;
                    }
                    $path = isset( $r['path'] ) ? (string) $r['path'] : '';
                    $short = $path;
                    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) && '' !== $path ) {
                        $short = str_replace( JJ_STYLE_GUIDE_PATH, '', $path );
                        $short = ltrim( $short, "/\\" );
                    }
                    $count = isset( $r['count'] ) ? (int) $r['count'] : 1;
                    $results[] = array(
                        'test'    => 'Missing required file: ' . $short,
                        'status'  => 'fail',
                        'message' => 'Not found (count=' . $count . ')',
                    );
                    $n++;
                }
            }
        } else {
            // 폴백 로더 진단( Safe Loader 자체 누락/손상 대비 )
            if ( isset( $GLOBALS['jj_style_guide_boot_diag'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag'] ) ) {
                $mr = isset( $GLOBALS['jj_style_guide_boot_diag']['missing_required'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag']['missing_required'] )
                    ? array_values( $GLOBALS['jj_style_guide_boot_diag']['missing_required'] )
                    : array();
                $mo = isset( $GLOBALS['jj_style_guide_boot_diag']['missing_optional'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag']['missing_optional'] )
                    ? array_values( $GLOBALS['jj_style_guide_boot_diag']['missing_optional'] )
                    : array();
                $errs = isset( $GLOBALS['jj_style_guide_boot_diag']['load_errors'] ) && is_array( $GLOBALS['jj_style_guide_boot_diag']['load_errors'] )
                    ? $GLOBALS['jj_style_guide_boot_diag']['load_errors']
                    : array();

                $results[] = array(
                    'test'    => 'Fallback Loader: Required files',
                    'status'  => empty( $mr ) ? 'pass' : 'fail',
                    'message' => empty( $mr ) ? 'No missing required files' : ( 'Missing required files: ' . count( $mr ) ),
                );
                $results[] = array(
                    'test'    => 'Fallback Loader: Optional files',
                    'status'  => empty( $mo ) ? 'pass' : 'warn',
                    'message' => empty( $mo ) ? 'No missing optional files' : ( 'Missing optional files: ' . count( $mo ) ),
                );
                $results[] = array(
                    'test'    => 'Fallback Loader: Load errors',
                    'status'  => empty( $errs ) ? 'pass' : 'fail',
                    'message' => empty( $errs ) ? 'No load errors' : ( 'Load errors detected: ' . count( $errs ) ),
                );
            } else {
                $results[] = array(
                    'test'    => 'Safe Loader: Diagnostics',
                    'status'  => 'warn',
                    'message' => 'Safe/Fallback loader diagnostics not available',
                );
            }
        }

        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'get_load_errors' ) ) {
            $errs = (array) JJ_Safe_Loader::get_load_errors();
            $results[] = array(
                'test'    => 'Safe Loader: Load errors',
                'status'  => empty( $errs ) ? 'pass' : 'fail',
                'message' => empty( $errs ) ? 'No load errors' : ( 'Load errors detected: ' . count( $errs ) ),
            );

            if ( ! empty( $errs ) ) {
                $max = 30;
                $n = 0;
                foreach ( $errs as $e ) {
                    if ( ! is_array( $e ) ) continue;
                    if ( $n >= $max ) {
                        $results[] = array(
                            'test'    => 'Safe Loader: Load errors (more)',
                            'status'  => 'fail',
                            'message' => 'Too many load errors. (truncated)',
                        );
                        break;
                    }
                    $path = isset( $e['path'] ) ? (string) $e['path'] : '';
                    $short = $path;
                    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) && '' !== $path ) {
                        $short = str_replace( JJ_STYLE_GUIDE_PATH, '', $path );
                        $short = ltrim( $short, "/\\" );
                    }
                    $msg = isset( $e['error'] ) ? (string) $e['error'] : 'Unknown error';
                    if ( function_exists( 'mb_substr' ) ) {
                        $msg = mb_substr( $msg, 0, 220 );
                    } else {
                        $msg = substr( $msg, 0, 220 );
                    }
                    $results[] = array(
                        'test'    => 'Load error: ' . $short,
                        'status'  => 'fail',
                        'message' => $msg,
                    );
                    $n++;
                }
            }
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

        // 4. Update / Cron health
        if ( function_exists( 'wp_next_scheduled' ) ) {
            $next = (int) wp_next_scheduled( 'wp_update_plugins' );
            $results[] = array(
                'test'    => 'Cron: wp_update_plugins',
                'status'  => $next ? 'pass' : 'warn',
                'message' => $next ? ( 'Next: ' . ( function_exists( 'date_i18n' ) ? date_i18n( 'Y-m-d H:i:s', $next ) : (string) $next ) ) : 'Not scheduled (may be disabled)',
            );
        } else {
            $results[] = array( 'test' => 'Cron: wp_update_plugins', 'status' => 'warn', 'message' => 'wp_next_scheduled() not available' );
        }

        // 5. Update transient basic sanity
        if ( function_exists( 'get_site_transient' ) ) {
            $u = get_site_transient( 'update_plugins' );
            $last_checked = ( is_object( $u ) && isset( $u->last_checked ) ) ? (int) $u->last_checked : 0;
            $resp = ( is_object( $u ) && isset( $u->response ) && is_array( $u->response ) ) ? $u->response : array();
            $results[] = array(
                'test'    => 'Update transient: update_plugins',
                'status'  => $u ? 'pass' : 'warn',
                'message' => $u ? ( 'last_checked=' . ( $last_checked ? ( function_exists( 'date_i18n' ) ? date_i18n( 'Y-m-d H:i:s', $last_checked ) : (string) $last_checked ) : '0' ) . ', updates=' . count( $resp ) ) : 'Missing (will be generated on update check)',
            );
        } else {
            $results[] = array( 'test' => 'Update transient: update_plugins', 'status' => 'warn', 'message' => 'get_site_transient() not available' );
        }

        // 6. Auto-update site option sanity
        if ( function_exists( 'get_site_option' ) ) {
            $auto = get_site_option( 'auto_update_plugins', array() );
            $results[] = array(
                'test'    => 'Site option: auto_update_plugins',
                'status'  => is_array( $auto ) ? 'pass' : 'fail',
                'message' => is_array( $auto ) ? ( 'Count: ' . count( $auto ) ) : 'Invalid type (should be array)',
            );
        }

        // 7. Required plugins (WooCommerce) status (if plugin.php available)
        if ( defined( 'ABSPATH' ) ) {
            $plugin_php = ABSPATH . 'wp-admin/includes/plugin.php';
            if ( file_exists( $plugin_php ) ) {
                require_once $plugin_php;
            }
        }
        if ( function_exists( 'is_plugin_active' ) ) {
            $woo_pf = 'woocommerce/woocommerce.php';
            $woo_active = is_plugin_active( $woo_pf );
            $results[] = array(
                'test'    => 'Required: WooCommerce',
                'status'  => $woo_active ? 'pass' : 'warn',
                'message' => $woo_active ? 'Active' : 'Inactive or not installed (Woo License add-on requires it)',
            );
        }

        return $results;
    }
}

